<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Faculty;
use App\Models\ImportLog;
use App\Models\Institution;
use App\Models\Mou;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportController extends Controller
{
    public function index()
    {
        $recentImports = ImportLog::with('admin')->latest()->take(10)->get();
        return view('admin.import.index', compact('recentImports'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');
        $path = $file->store('imports', 'public');

        $importLog = ImportLog::create([
            'admin_id' => auth()->guard('admin')->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => 'pending',
        ]);

        // Parse Excel for preview
        $data = $this->parseExcel(storage_path('app/public/' . $path));

        return view('admin.import.preview', compact('data', 'importLog'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'import_log_id' => 'required|exists:import_logs,id',
        ]);

        $importLog = ImportLog::findOrFail($request->import_log_id);
        $importLog->update(['status' => 'processing']);

        $filePath = storage_path('app/public/' . $importLog->file_path);
        $data = $this->parseExcel($filePath);

        $success = 0;
        $failed = 0;
        $duplicates = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                if (empty($row['nomor_mou']) || empty($row['nama_lembaga'])) {
                    $errors[] = "Baris " . ($index + 2) . ": Data tidak lengkap";
                    $failed++;
                    continue;
                }

                // Check duplicate
                if (Mou::where('mou_number', $row['nomor_mou'])->exists()) {
                    $duplicates++;
                    continue;
                }

                // Get or create institution
                $institution = Institution::firstOrCreate(
                    ['name' => $row['nama_lembaga']],
                    ['slug' => Str::slug($row['nama_lembaga']), 'type' => 'lainnya']
                );

                // Get category
                $category = null;
                if (!empty($row['kategori'])) {
                    $category = Category::firstOrCreate(
                        ['name' => $row['kategori']],
                        ['slug' => Str::slug($row['kategori'])]
                    );
                }

                // Get faculty
                $faculty = null;
                if (!empty($row['fakultas'])) {
                    $faculty = Faculty::firstOrCreate(
                        ['name' => $row['fakultas']],
                        ['slug' => Str::slug($row['fakultas'])]
                    );
                }

                $startDate = $this->parseDate($row['tanggal_mulai'] ?? null);
                $endDate = $this->parseDate($row['tanggal_selesai'] ?? null);

                Mou::create([
                    'mou_number' => $row['nomor_mou'],
                    'title' => $row['judul'] ?? 'Kerjasama dengan ' . $row['nama_lembaga'],
                    'slug' => Str::slug(($row['judul'] ?? $row['nomor_mou'])) . '-' . Str::random(5),
                    'institution_id' => $institution->id,
                    'category_id' => $category?->id,
                    'faculty_id' => $faculty?->id,
                    'level' => $row['tingkat'] ?? 'nasional',
                    'type' => $row['jenis_kerjasama'] ?? 'akademik',
                    'cooperation_type' => 'mou',
                    'start_date' => $startDate ?? now(),
                    'end_date' => $endDate ?? now()->addYears(2),
                    'visibility' => $row['visibility'] ?? 'internal',
                    'description' => $row['deskripsi'] ?? null,
                    'status' => $row['status'] ?? 'aktif',
                    'created_by' => auth()->guard('admin')->id(),
                ]);

                $success++;
            } catch (\Exception $e) {
                $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                $failed++;
            }
        }

        $importLog->update([
            'status' => 'completed',
            'total_rows' => count($data),
            'success_count' => $success,
            'failed_count' => $failed,
            'duplicate_count' => $duplicates,
            'errors' => $errors ?: null,
            'summary' => [
                'total' => count($data),
                'success' => $success,
                'failed' => $failed,
                'duplicates' => $duplicates,
            ],
        ]);

        ActivityLogService::log('import', null, "Import data: {$success} berhasil, {$failed} gagal, {$duplicates} duplikat");

        return redirect()->route('admin.import.index')->with('success', "Import selesai: {$success} berhasil, {$failed} gagal, {$duplicates} duplikat.");
    }

    public function downloadTemplate()
    {
        $templatePath = resource_path('templates/import_template.xlsx');

        if (!file_exists($templatePath)) {
            // Generate simple CSV template as fallback
            $headers = ['nomor_mou', 'judul', 'nama_lembaga', 'kategori', 'tanggal_mulai', 'tanggal_selesai', 'status', 'fakultas', 'jenis_kerjasama', 'visibility', 'deskripsi'];
            $example = ['MOU/001/2024', 'Kerjasama Pendidikan', 'Universitas Contoh', 'Pendidikan', '2024-01-01', '2026-01-01', 'aktif', 'Fakultas Teknik', 'akademik', 'public', 'Deskripsi kerjasama'];

            $csv = implode(',', $headers) . "\n" . implode(',', $example);

            return response($csv)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="template_import_mou.csv"');
        }

        return response()->download($templatePath, 'template_import_mou.xlsx');
    }

    public function logs()
    {
        $logs = ImportLog::with('admin')->latest()->paginate(20);
        return view('admin.import.logs', compact('logs'));
    }

    private function parseExcel(string $filePath): array
    {
        $data = [];

        if (!file_exists($filePath)) {
            return $data;
        }

        // Simple CSV/Excel parsing
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        if (in_array($extension, ['csv', 'txt'])) {
            $handle = fopen($filePath, 'r');
            $headers = fgetcsv($handle);

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === count($headers)) {
                    $data[] = array_combine($headers, $row);
                }
            }
            fclose($handle);
        }

        return $data;
    }

    private function parseDate(?string $date): ?string
    {
        if (empty($date)) return null;

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}
