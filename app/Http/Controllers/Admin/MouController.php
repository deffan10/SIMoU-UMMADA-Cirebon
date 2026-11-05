<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Faculty;
use App\Models\Institution;
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

class MouController extends Controller
{
    private function buildFilteredQuery(Request $request)
    {
        $query = Mou::with(['institution', 'category', 'faculty'])->withCount('implementations');

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('cooperation_type')) {
            $query->where('cooperation_type', $request->cooperation_type);
        }
        if ($request->filled('has_implementation')) {
            if ($request->has_implementation == 'yes') {
                $query->has('implementations');
            } elseif ($request->has_implementation == 'no') {
                $query->doesntHave('implementations');
            }
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('institution_id')) {
            $query->where('institution_id', $request->institution_id);
        }
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }
        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }
        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $mous = $this->buildFilteredQuery($request)->latest()->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $institutions = Institution::where('is_active', true)->get();
        $faculties = Faculty::where('is_active', true)->get();

        return view('admin.mou.index', compact('mous', 'categories', 'institutions', 'faculties'));
    }

    public function export(Request $request)
    {
        $format = strtolower($request->query('format', 'xlsx'));
        if (!in_array($format, ['xlsx', 'csv'])) {
            $format = 'xlsx';
        }

        $mous = $this->buildFilteredQuery($request)->latest()->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data MoU');

        $headers = [
            'No',
            'Nomor Dokumen',
            'Judul Kerjasama',
            'Institusi Mitra',
            'Kategori',
            'Tingkat',
            'Bidang Kerjasama',
            'Tipe Dokumen',
            'Fakultas',
            'Program Studi',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Durasi (Bulan)',
            'Status',
            'Visibility',
            'PIC Nama',
            'PIC Telepon',
            'PIC Email',
            'Jumlah Implementasi',
            'Deskripsi / Ringkasan',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $lastColumn = 'T';
        $headerRange = 'A1:' . $lastColumn . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFF3F4F6');

        $rowNumber = 2;
        foreach ($mous as $idx => $mou) {
            $sheet->setCellValue('A' . $rowNumber, $idx + 1);
            $sheet->setCellValueExplicit('B' . $rowNumber, (string) ($mou->mou_number ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('C' . $rowNumber, (string) ($mou->title ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $rowNumber, $mou->institution?->name ?? '-');
            $sheet->setCellValue('E' . $rowNumber, $mou->category?->name ?? '-');
            $sheet->setCellValue('F' . $rowNumber, ucfirst($mou->level ?? ''));
            $sheet->setCellValue('G' . $rowNumber, ucfirst($mou->type ?? ''));
            $sheet->setCellValue('H' . $rowNumber, strtoupper($mou->cooperation_type ?? ''));
            $sheet->setCellValue('I' . $rowNumber, $mou->faculty?->name ?? '-');
            $sheet->setCellValue('J' . $rowNumber, $mou->study_program ?? '-');
            $sheet->setCellValue('K' . $rowNumber, $mou->start_date ? $mou->start_date->format('d/m/Y') : '-');
            $sheet->setCellValue('L' . $rowNumber, $mou->end_date ? $mou->end_date->format('d/m/Y') : '-');
            $sheet->setCellValue('M' . $rowNumber, $mou->duration_months ?? 0);
            $sheet->setCellValue('N' . $rowNumber, ucfirst(str_replace('_', ' ', $mou->status ?? '')));
            $sheet->setCellValue('O' . $rowNumber, ucfirst($mou->visibility ?? ''));
            $sheet->setCellValueExplicit('P' . $rowNumber, (string) ($mou->pic_name ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('Q' . $rowNumber, (string) ($mou->pic_phone ?? ''), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('R' . $rowNumber, $mou->pic_email ?? '-');
            $sheet->setCellValue('S' . $rowNumber, $mou->implementations_count ?? 0);
            $sheet->setCellValueExplicit('T' . $rowNumber, (string) ($mou->public_summary ?: ($mou->description ?: '-')), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

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
            'Export data MoU format ' . strtoupper($format) . ' (' . $mous->count() . ' baris)'
        );

        $fileName = 'data_mou_' . date('Ymd_His') . '.' . $format;
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
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $institutions = Institution::where('is_active', true)->orderBy('name')->get();
        $faculties = Faculty::where('is_active', true)->with('studyPrograms')->get();

        return view('admin.mou.create', compact('categories', 'institutions', 'faculties'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mou_number' => 'required|string|unique:mous,mou_number',
            'title' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'required|in:lokal,nasional,internasional',
            'type' => 'required|in:akademik,penelitian,mbkm,industri,pengabdian,pemerintah,internasional',
            'cooperation_type' => 'required|in:mou,moa,lainnya',
            'faculty_id' => 'nullable|exists:faculties,id',
            'study_program' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:50',
            'pic_email' => 'nullable|email|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'visibility' => 'required|in:public,internal',
            'description' => 'nullable|string',
            'public_summary' => 'nullable|string',
            'show_pdf_public' => 'boolean',
            'allow_download' => 'boolean',
            'main_document' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(5);
        $validated['duration_months'] = \Carbon\Carbon::parse($validated['start_date'])
            ->diffInMonths(\Carbon\Carbon::parse($validated['end_date']));
        $validated['created_by'] = auth()->guard('admin')->id();
        $validated['show_pdf_public'] = $request->boolean('show_pdf_public');
        $validated['allow_download'] = $request->boolean('allow_download');

        // Calculate initial status
        $daysRemaining = now()->diffInDays(\Carbon\Carbon::parse($validated['end_date']), false);
        if ($daysRemaining <= 0) {
            $validated['status'] = 'expire';
        } elseif ($daysRemaining <= 90) {
            $validated['status'] = 'akan_expire';
        } else {
            $validated['status'] = 'aktif';
        }

        if ($request->hasFile('main_document')) {
            $file = $request->file('main_document');
            $fileName = 'mou_' . Str::slug($validated['mou_number']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $validated['main_document'] = $file->storeAs('mou-documents', $fileName, 'public');
        }

        $mou = Mou::create($validated);

        ActivityLogService::log('create', $mou, "MoU '{$mou->title}' berhasil ditambahkan");

        return redirect()->route('admin.mou.show', $mou)->with('success', 'MoU berhasil ditambahkan.');
    }

    public function show(Mou $mou)
    {
        $mou->load(['institution', 'category', 'faculty', 'renewals.renewedByAdmin', 'attachments', 'creator']);

        return view('admin.mou.show', compact('mou'));
    }

    public function edit(Mou $mou)
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $institutions = Institution::where('is_active', true)->orderBy('name')->get();
        $faculties = Faculty::where('is_active', true)->with('studyPrograms')->get();

        return view('admin.mou.edit', compact('mou', 'categories', 'institutions', 'faculties'));
    }

    public function update(Request $request, Mou $mou)
    {
        $validated = $request->validate([
            'mou_number' => 'required|string|unique:mous,mou_number,' . $mou->id,
            'title' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'required|in:lokal,nasional,internasional',
            'type' => 'required|in:akademik,penelitian,mbkm,industri,pengabdian,pemerintah,internasional',
            'cooperation_type' => 'required|in:mou,moa,lainnya',
            'faculty_id' => 'nullable|exists:faculties,id',
            'study_program' => 'nullable|string|max:255',
            'pic_name' => 'nullable|string|max:255',
            'pic_phone' => 'nullable|string|max:50',
            'pic_email' => 'nullable|email|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'visibility' => 'required|in:public,internal',
            'description' => 'nullable|string',
            'public_summary' => 'nullable|string',
            'show_pdf_public' => 'boolean',
            'allow_download' => 'boolean',
            'main_document' => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $oldValues = $mou->toArray();

        $validated['duration_months'] = \Carbon\Carbon::parse($validated['start_date'])
            ->diffInMonths(\Carbon\Carbon::parse($validated['end_date']));
        $validated['updated_by'] = auth()->guard('admin')->id();
        $validated['show_pdf_public'] = $request->boolean('show_pdf_public');
        $validated['allow_download'] = $request->boolean('allow_download');

        // Recalculate status
        $daysRemaining = now()->diffInDays(\Carbon\Carbon::parse($validated['end_date']), false);
        if ($daysRemaining <= 0) {
            $validated['status'] = 'expire';
        } elseif ($daysRemaining <= 90) {
            $validated['status'] = 'akan_expire';
        } else {
            $validated['status'] = 'aktif';
        }

        if ($request->hasFile('main_document')) {
            if ($mou->main_document) {
                Storage::disk('public')->delete($mou->main_document);
            }
            $file = $request->file('main_document');
            $fileName = 'mou_' . Str::slug($validated['mou_number']) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $validated['main_document'] = $file->storeAs('mou-documents', $fileName, 'public');
        }

        $mou->update($validated);

        ActivityLogService::log('update', $mou, "MoU '{$mou->title}' berhasil diupdate", $oldValues, $validated);

        return redirect()->route('admin.mou.show', $mou)->with('success', 'MoU berhasil diupdate.');
    }

    public function destroy(Mou $mou)
    {
        ActivityLogService::log('delete', $mou, "MoU '{$mou->title}' dihapus (soft delete)");
        $mou->delete();

        return redirect()->route('admin.mou.index')->with('success', 'MoU berhasil dihapus.');
    }

    public function trashed()
    {
        $mous = Mou::onlyTrashed()->with(['institution', 'category'])->latest('deleted_at')->paginate(15);

        return view('admin.mou.trashed', compact('mous'));
    }

    public function restore($id)
    {
        $mou = Mou::onlyTrashed()->findOrFail($id);
        $mou->restore();

        ActivityLogService::log('restore', $mou, "MoU '{$mou->title}' berhasil direstore");

        return redirect()->route('admin.mou.index')->with('success', 'MoU berhasil direstore.');
    }
}
