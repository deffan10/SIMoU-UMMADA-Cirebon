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
                    'cooperation_type' => $this->mapCooperationType($row['tipe_dokumen'] ?? 'mou'),
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

        $headers = ['nomor_mou', 'judul', 'nama_lembaga', 'kategori', 'tanggal_mulai', 'tanggal_selesai', 'status', 'fakultas', 'jenis_kerjasama', 'tipe_dokumen', 'tingkat', 'visibility', 'deskripsi'];
        foreach ($headers as $i => $h) {
            $sheet->setCellValueByColumnAndRow($i + 1, 1, $h);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:M1')->applyFromArray($headerStyle);

        // Set kolom tanggal (E & F) sebagai TEXT agar Excel tidak auto-format
        $sheet->getStyle('E:E')->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('F:F')->getNumberFormat()->setFormatCode('@');

        // Example rows
        $examples = [
            ['MOU/UMMADA/001/2024', 'Kerjasama Tri Dharma', 'Universitas Indonesia', 'Pendidikan & Pengajaran', '2024-01-15', '2027-01-15', 'aktif', 'Fakultas Teknik', 'akademik', 'mou', 'nasional', 'public', 'Kerjasama bidang pendidikan.'],
            ['MOU/UMMADA/002/2024', 'Program Magang MBKM', 'PT Telkom Indonesia', 'Magang & MBKM', '2024-03-01', '2026-03-01', 'aktif', 'Fakultas Teknik', 'mbkm', 'moa', 'nasional', 'public', 'Program magang bersertifikat.'],
            ['MOU/UMMADA/003/2023', 'Pertukaran Mahasiswa', 'Universiti Malaya', 'Beasiswa & Pertukaran', '2023-08-01', '2026-08-01', 'aktif', '', 'internasional', 'mou', 'internasional', 'public', 'Program pertukaran mahasiswa.'],
        ];

        foreach ($examples as $rowIdx => $row) {
            foreach ($row as $colIdx => $val) {
                // Kolom tanggal (index 4 & 5) set explicit sebagai string
                if ($colIdx === 4 || $colIdx === 5) {
                    $sheet->setCellValueExplicitByColumnAndRow($colIdx + 1, $rowIdx + 2, $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                } else {
                    $sheet->setCellValueByColumnAndRow($colIdx + 1, $rowIdx + 2, $val);
                }
            }
        }

        $sheet->getStyle('A2:M4')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '6B7280']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Highlight kolom tanggal dengan warna kuning muda
        $sheet->getStyle('E1:F1')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F59E0B']],
        ]);
        $sheet->getStyle('E2:F4')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '92400E']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
        ]);

        foreach (range('A', 'M') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Tambah note di baris 6 sebagai reminder format tanggal
        $sheet->setCellValue('E6', 'FORMAT: YYYY-MM-DD');
        $sheet->setCellValue('F6', 'FORMAT: YYYY-MM-DD');
        $sheet->getStyle('E6:F6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'DC2626']],
        ]);

        // Instruction sheet
        $info = $spreadsheet->createSheet();
        $info->setTitle('Petunjuk');
        $info->setCellValue('A1', 'PETUNJUK IMPORT DATA MoU');
        $info->setCellValue('A3', 'KOLOM WAJIB:');
        $info->setCellValue('A4', '  - nomor_mou (unik, tidak boleh duplikat)');
        $info->setCellValue('A5', '  - nama_lembaga');
        $info->setCellValue('A7', 'FORMAT TANGGAL:');
        $info->setCellValue('A8', '  - Format: YYYY-MM-DD (contoh: 2024-01-15)');
        $info->setCellValue('A9', '  - Kolom tanggal sudah di-set sebagai TEXT. Ketik langsung.');
        $info->setCellValue('A10', '  - JANGAN format sebagai Date di Excel, biarkan sebagai Text.');
        $info->setCellValue('A12', 'NILAI YANG VALID:');
        $info->setCellValue('A14', '  status:');
        $info->setCellValue('A15', '    - aktif');
        $info->setCellValue('A16', '    - akan_expire');
        $info->setCellValue('A17', '    - expire');
        $info->setCellValue('A19', '  jenis_kerjasama:');
        $info->setCellValue('A20', '    - akademik');
        $info->setCellValue('A21', '    - penelitian');
        $info->setCellValue('A22', '    - mbkm');
        $info->setCellValue('A23', '    - industri');
        $info->setCellValue('A24', '    - pengabdian');
        $info->setCellValue('A25', '    - pemerintah');
        $info->setCellValue('A26', '    - internasional');
        $info->setCellValue('A28', '  tipe_dokumen:');
        $info->setCellValue('A29', '    - mou (Memorandum of Understanding)');
        $info->setCellValue('A30', '    - moa (Memorandum of Agreement)');
        $info->setCellValue('A31', '    - ia (Implementation Arrangement)');
        $info->setCellValue('A32', '    - pks (Perjanjian Kerja Sama)');
        $info->setCellValue('A33', '    - lainnya');
        $info->setCellValue('A35', '  tingkat:');
        $info->setCellValue('A36', '    - lokal');
        $info->setCellValue('A37', '    - nasional');
        $info->setCellValue('A38', '    - internasional');
        $info->setCellValue('A40', '  visibility:');
        $info->setCellValue('A41', '    - public (tampil di website)');
        $info->setCellValue('A42', '    - internal (hanya admin)');
        $info->setCellValue('A44', 'CATATAN:');
        $info->setCellValue('A45', '  - Gunakan nama yang PERSIS SAMA dengan daftar di sheet Kategori/Fakultas/Institusi');
        $info->setCellValue('A46', '  - Jika nama tidak ada di daftar, akan otomatis dibuat baru');
        $info->setCellValue('A47', '  - Nomor MoU yang sudah ada di database akan di-skip (tidak diimport ulang)');
        $info->setCellValue('A48', '  - Hapus baris contoh (2-4) di sheet Template sebelum mengisi data Anda');
        $info->setCellValue('A49', '  - Jika kolom dikosongkan, akan menggunakan nilai default');
        $info->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $info->getStyle('A3')->getFont()->setBold(true);
        $info->getStyle('A7')->getFont()->setBold(true);
        $info->getStyle('A9:A10')->getFont()->setItalic(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('DC2626'));
        $info->getStyle('A12')->getFont()->setBold(true);
        $info->getStyle('A14')->getFont()->setBold(true);
        $info->getStyle('A19')->getFont()->setBold(true);
        $info->getStyle('A28')->getFont()->setBold(true);
        $info->getStyle('A35')->getFont()->setBold(true);
        $info->getStyle('A40')->getFont()->setBold(true);
        $info->getStyle('A44')->getFont()->setBold(true);
        $info->getColumnDimension('A')->setWidth(85);

        // === REFERENSI DATA DARI DATABASE ===

        // Sheet: Daftar Kategori
        $catSheet = $spreadsheet->createSheet();
        $catSheet->setTitle('Daftar Kategori');
        $catSheet->setCellValue('A1', 'Nama Kategori (gunakan persis untuk kolom "kategori")');
        $catSheet->getStyle('A1')->getFont()->setBold(true);
        $catSheet->getColumnDimension('A')->setWidth(60);
        $categories = Category::where('is_active', true)->orderBy('sort_order')->pluck('name');
        foreach ($categories as $i => $name) {
            $catSheet->setCellValue('A' . ($i + 2), $name);
        }
        if ($categories->isEmpty()) {
            $catSheet->setCellValue('A2', '(belum ada kategori)');
            $catSheet->getStyle('A2')->getFont()->setItalic(true);
        }

        // Sheet: Daftar Fakultas
        $facSheet = $spreadsheet->createSheet();
        $facSheet->setTitle('Daftar Fakultas');
        $facSheet->setCellValue('A1', 'Kode');
        $facSheet->setCellValue('B1', 'Nama Fakultas (gunakan persis untuk kolom "fakultas")');
        $facSheet->getStyle('A1:B1')->getFont()->setBold(true);
        $facSheet->getColumnDimension('A')->setWidth(10);
        $facSheet->getColumnDimension('B')->setWidth(50);
        $faculties = Faculty::where('is_active', true)->orderBy('name')->get(['code', 'name']);
        foreach ($faculties as $i => $fac) {
            $facSheet->setCellValue('A' . ($i + 2), $fac->code ?? '-');
            $facSheet->setCellValue('B' . ($i + 2), $fac->name);
        }
        if ($faculties->isEmpty()) {
            $facSheet->setCellValue('A2', '-');
            $facSheet->setCellValue('B2', '(belum ada fakultas)');
            $facSheet->getStyle('B2')->getFont()->setItalic(true);
        }

        // Sheet: Daftar Institusi
        $instSheet = $spreadsheet->createSheet();
        $instSheet->setTitle('Daftar Institusi');
        $instSheet->setCellValue('A1', 'Nama Institusi (gunakan persis untuk kolom "nama_lembaga")');
        $instSheet->setCellValue('B1', 'Tipe');
        $instSheet->setCellValue('C1', 'Kota');
        $instSheet->getStyle('A1:C1')->getFont()->setBold(true);
        $instSheet->getColumnDimension('A')->setWidth(50);
        $instSheet->getColumnDimension('B')->setWidth(15);
        $instSheet->getColumnDimension('C')->setWidth(20);
        $institutions = Institution::where('is_active', true)->orderBy('name')->get(['name', 'type', 'city']);
        foreach ($institutions as $i => $inst) {
            $instSheet->setCellValue('A' . ($i + 2), $inst->name);
            $instSheet->setCellValue('B' . ($i + 2), $inst->type);
            $instSheet->setCellValue('C' . ($i + 2), $inst->city ?? '-');
        }
        if ($institutions->isEmpty()) {
            $instSheet->setCellValue('A2', '(belum ada institusi)');
            $instSheet->getStyle('A2')->getFont()->setItalic(true);
        }

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

    private function mapCooperationType(string $type): string
    {
        $type = strtolower(trim($type));
        return in_array($type, ['mou', 'moa', 'ia', 'pks', 'lainnya']) ? $type : 'mou';
    }
}
