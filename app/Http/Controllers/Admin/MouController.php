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

class MouController extends Controller
{
    public function index(Request $request)
    {
        $query = Mou::with(['institution', 'category', 'faculty']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
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

        $mous = $query->latest()->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();
        $institutions = Institution::where('is_active', true)->get();
        $faculties = Faculty::where('is_active', true)->get();

        return view('admin.mou.index', compact('mous', 'categories', 'institutions', 'faculties'));
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
            'cooperation_type' => 'required|in:mou,moa,ia,pks,lainnya',
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
            'cooperation_type' => 'required|in:mou,moa,ia,pks,lainnya',
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
