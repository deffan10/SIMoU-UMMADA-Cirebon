<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Implementation;
use App\Models\Mou;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ImplementationController extends Controller
{
    private function buildFilteredQuery(Request $request)
    {
        $query = Implementation::with(['mou.institution', 'mou.category', 'mou.faculty', 'uploader']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%')
                    ->orWhereHas('mou', function ($m) use ($request) {
                        $m->where('title', 'like', '%' . $request->search . '%')
                            ->orWhere('mou_number', 'like', '%' . $request->search . '%');
                    });
            });
        }
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $implementations = $this->buildFilteredQuery($request)->latest()->paginate(15)->withQueryString();

        return view('admin.implementations.index', compact('implementations'));
    }

    public function export(Request $request)
    {
        $format = strtolower($request->query('format', 'xlsx'));
        if (!in_array($format, ['xlsx', 'csv'])) {
            $format = 'xlsx';
        }

        $items = $this->buildFilteredQuery($request)->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Implementasi');

        $headers = [
            'No',
            'Judul Implementasi',
            'Nomor Dokumen MoU',
            'Judul MoU Terkait',
            'Institusi Mitra',
            'Kategori MoU',
            'Fakultas',
            'Visibility',
            'Nama File',
            'Ukuran File',
            'Diupload Oleh',
            'Tanggal Upload',
            'Deskripsi',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $lastColumn = 'M';
        $headerRange = 'A1:' . $lastColumn . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');

        $rowNumber = 2;
        foreach ($items as $idx => $item) {
            $sheet->setCellValue('A' . $rowNumber, $idx + 1);
            $sheet->setCellValueExplicit('B' . $rowNumber, (string) ($item->title ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $rowNumber, (string) ($item->mou?->mou_number ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('D' . $rowNumber, (string) ($item->mou?->title ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('E' . $rowNumber, $item->mou?->institution?->name ?? '-');
            $sheet->setCellValue('F' . $rowNumber, $item->mou?->category?->name ?? '-');
            $sheet->setCellValue('G' . $rowNumber, $item->mou?->faculty?->name ?? '-');
            $sheet->setCellValue('H' . $rowNumber, ucfirst($item->visibility ?? ''));
            $sheet->setCellValueExplicit('I' . $rowNumber, (string) ($item->original_filename ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('J' . $rowNumber, $item->file_size_formatted);
            $sheet->setCellValue('K' . $rowNumber, $item->uploader?->name ?? '-');
            $sheet->setCellValue('L' . $rowNumber, $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-');
            $sheet->setCellValueExplicit('M' . $rowNumber, (string) ($item->description ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            $rowNumber++;
        }

        $totalRows = max($rowNumber - 1, 1);

        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFE5E7EB'],
                ],
            ],
        ];
        $sheet->getStyle('A1:' . $lastColumn . $totalRows)->applyFromArray($borderStyle);

        foreach (range('A', $lastColumn) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        ActivityLogService::log(
            'export',
            null,
            'Export data implementasi format ' . strtoupper($format) . ' (' . $items->count() . ' baris)'
        );

        $fileName = 'data_implementasi_' . date('Ymd_His') . '.' . $format;
        $tempDir = storage_path('app');
        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }
        $tempPath = $tempDir . '/' . $fileName;

        if ($format === 'csv') {
            $writer = new Csv($spreadsheet);
            $writer->setDelimiter(',');
            $writer->setEnclosure('"');
            $writer->setLineEnding("\r\n");
            $writer->setSheetIndex(0);
        } else {
            $writer = new Xlsx($spreadsheet);
        }

        $writer->save($tempPath);

        return response()->download($tempPath, $fileName, [
            'Content-Type' => $format === 'csv' ? 'text/csv' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function create()
    {
        return view('admin.implementations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mou_id' => 'required|exists:mous,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf|max:20480',
            'visibility' => 'required|in:public,private',
        ]);

        $file = $request->file('file');
        $fileName = 'impl_' . Str::slug($validated['title']) . '_' . time() . '.pdf';
        $filePath = $file->storeAs('implementations', $fileName, 'public');

        Implementation::create([
            'mou_id' => $validated['mou_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'original_filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'visibility' => $validated['visibility'],
            'uploaded_by' => auth()->guard('admin')->id(),
        ]);

        $mou = Mou::find($validated['mou_id']);
        ActivityLogService::log('create', $mou, "Implementasi '{$validated['title']}' ditambahkan untuk MoU '{$mou->title}'");

        return redirect()->route('admin.implementations.index')->with('success', 'Implementasi berhasil ditambahkan.');
    }

    public function edit(Implementation $implementation)
    {
        $implementation->load('mou');
        return view('admin.implementations.edit', compact('implementation'));
    }

    public function update(Request $request, Implementation $implementation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf|max:20480',
            'visibility' => 'required|in:public,private',
        ]);

        if ($request->hasFile('file')) {
            if ($implementation->file_path) {
                Storage::disk('public')->delete($implementation->file_path);
            }
            $file = $request->file('file');
            $fileName = 'impl_' . Str::slug($validated['title']) . '_' . time() . '.pdf';
            $validated['file_path'] = $file->storeAs('implementations', $fileName, 'public');
            $validated['original_filename'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        $implementation->update($validated);

        ActivityLogService::log('update', $implementation->mou, "Implementasi '{$implementation->title}' diupdate");

        return redirect()->route('admin.implementations.index')->with('success', 'Implementasi berhasil diupdate.');
    }

    public function destroy(Implementation $implementation)
    {
        if ($implementation->file_path) {
            Storage::disk('public')->delete($implementation->file_path);
        }

        ActivityLogService::log('delete', $implementation->mou, "Implementasi '{$implementation->title}' dihapus");
        $implementation->forceDelete();

        return redirect()->route('admin.implementations.index')->with('success', 'Implementasi berhasil dihapus.');
    }

    public function download(Implementation $implementation)
    {
        if (!$implementation->file_path || !Storage::disk('public')->exists($implementation->file_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($implementation->file_path, $implementation->original_filename);
    }

    /**
     * AJAX search MoU for autocomplete
     */
    public function searchMou(Request $request)
    {
        $query = $request->get('q', '');

        $mous = Mou::where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
                ->orWhere('mou_number', 'like', "%{$query}%");
        })
            ->with('institution:id,name')
            ->take(10)
            ->get(['id', 'title', 'mou_number', 'institution_id']);

        return response()->json($mous);
    }
}
