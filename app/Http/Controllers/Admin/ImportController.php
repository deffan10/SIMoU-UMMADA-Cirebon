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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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
                    $errors[] = "Baris " . ($index + 2) . ": nomor_mou atau nama_lembaga kosong";
                    $failed++;
                    continue;
                }

                if (Mou::where('mou_number', $row['nomor_mou'])->exists()) {
                    $duplicates++;
                    continue;
                }

                $institution = Institution::firstOrCreate(
                    ['name' => $row['nama_lembaga']],
                    ['slug' => Str::slug($row['nama_lembaga']), 'type' => 'lainnya']
                );

                $category = null;
                if (!empty($row['kategori'])) {
                    $category = Category::firstOrCreate(
                        ['name' => $row['kategori']],
                        ['slug' => Str::slug($row['kategori'])]
                    );
                }

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
                    'level' => $this->mapLevel($row['tingkat'] ?? 'nasional'),
                    'type' => $this->mapType($row['jenis_kerjasama'] ?? 'akademik'),
                    'cooperation_type' => 'mou',
                    'start_date' => $startDate ?? now(),
                    'end_date' => $endDate ?? now()->addYears(2),
                    'visibility' => in_array(strtolower($row['visibility'] ?? ''), ['public', 'internal']) ? strtolower($row['visibility']) : 'internal',
                    'description' => $row['deskripsi'] ?? null,
                    'status' => $this->mapStatus($row['status'] ?? 'aktif'),
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
            'summary' => ['total' => count($data), 'success' => $success, 'failed' => $failed, 'duplicates' => $duplicates],
        ]);

        ActivityLogService::log('import', null, "Import data: {$success} berhasil, {$failed} gagal, {$duplicates} duplikat");

        return redirect()->route('admin.import.index')->with('success', "Import selesai: {$success} berhasil, {$failed} gagal, {$duplicates} duplikat.");
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import MoU');

        $headers = ['nomor_mou', 'judul', 'nama_lembaga', 'kategori', 'tanggal_mulai', 'tanggal_selesai', 'status', 'fakultas', 'jenis_kerjasama', 'tingkat', 'visibility', 'deskripsi'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);

        // Example rows
        $examples = [
            ['MOU/UMMADA/001/2024', 'Kerjasama Tri Dharma', 'Universitas Indonesia', 'Pendidikan & Pengajaran', '2024-01-15', '2027-01-15', 'aktif', 'Fakultas Teknik', 'akademik', 'nasional', 'public', 'Kerjasama bidang pendidikan.'],
            ['MOU/UMMADA/002/2024', 'Program Magang MBKM', 'PT Telkom Indonesia', 'Magang & MBKM', '2024-03-01', '2026-03-01', 'aktif', 'Fakultas Teknik', 'mbkm', 'nasional', 'public', 'Program magang bersertifikat.'],
            ['MOU/UMMADA/003/2023', 'Pertukaran Mahasiswa', 'Universiti Malaya', 'Beasiswa & Pertukaran', '2023-08-01', '2026-08-01', 'aktif', '', 'internasional', 'internasional', 'public', 'Program pertukaran mahasiswa.'],
        ];

        foreach ($examples as $rowIdx => $row) {
            foreach ($row as $colIdx => $val) {
                $sheet->setCellValueByColumnAndRow($colIdx + 1, $rowIdx + 2, $val);
            }
        }

        $sheet->getStyle('A2:L4')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Instruction sheet
        $info = $spreadsheet->createSheet();
        $info->setTitle('Petunjuk');
        $info->setCellValue('A1', 'PETUNJUK IMPORT DATA MoU');
        $info->setCellValue('A3', 'Kolom Wajib: nomor_mou, nama_lembaga');
        $info->setCellValue('A4', 'Format Tanggal: YYYY-MM-DD (contoh: 2024-01-15)');
        $info->setCellValue('A6', 'Nilai status: aktif, akan_expire, expire');
        $info->setCellValue('A7', 'Nilai jenis_kerjasama: akademik, penelitian, mbkm, industri, pengabdian, pemerintah, internasional');
        $info->setCellValue('A8', 'Nilai tingkat: lokal, nasional, internasional');
        $info->setCellValue('A9', 'Nilai visibility: public, internal');
        $info->setCellValue('A11', 'Catatan:');
        $info->setCellValue('A12', '- Institusi/kategori/fakultas baru otomatis dibuat jika belum ada');
        $info->setCellValue('A13', '- Nomor MoU duplikat akan di-skip');
        $info->setCellValue('A14', '- Hapus baris contoh (2-4) sebelum mengisi data Anda');
        $info->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $info->getColumnDimension('A')->setWidth(80);

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'template_import_mou.xlsx';
        $tempPath = storage_path('app/' . $fileName);
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        return response()->download($tempPath, $fileName)->deleteFileAfterSend(true);
    }

    public function logs()
    {
        $logs = ImportLog::with('admin')->latest()->paginate(20);
        return view('admin.import.logs', compact('logs'));
    }

    private function parseExcel(string $filePath): array
    {
        $data = [];
        if (!file_exists($filePath)) return $data;

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (count($rows) < 2) return $data;

            $headers = array_map(fn($h) => strtolower(trim(str_replace(' ', '_', $h ?? ''))), $rows[0]);

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                if (empty(array_filter($row))) continue;

                $rowData = [];
                foreach ($headers as $index => $header) {
                    if (!empty($header)) {
                        $rowData[$header] = trim($row[$index] ?? '');
                    }
                }
                $data[] = $rowData;
            }
        } catch (\Exception $e) {
            \Log::warning('Excel parse error: ' . $e->getMessage());
        }

        return $data;
    }

    private function parseDate(?string $date): ?string
    {
        if (empty($date)) return null;
        try {
            if (is_numeric($date)) {
                return Carbon::createFromFormat('Y-m-d', gmdate('Y-m-d', ($date - 25569) * 86400))->format('Y-m-d');
            }
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function mapLevel(string $level): string
    {
        $level = strtolower(trim($level));
        return in_array($level, ['lokal', 'nasional', 'internasional']) ? $level : 'nasional';
    }

    private function mapType(string $type): string
    {
        $type = strtolower(trim($type));
        $valid = ['akademik', 'penelitian', 'mbkm', 'industri', 'pengabdian', 'pemerintah', 'internasional'];
        return in_array($type, $valid) ? $type : 'akademik';
    }

    private function mapStatus(string $status): string
    {
        $status = strtolower(trim($status));
        return in_array($status, ['aktif', 'akan_expire', 'expire']) ? $status : 'aktif';
    }
}
