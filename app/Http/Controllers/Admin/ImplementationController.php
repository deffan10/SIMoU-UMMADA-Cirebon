<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Implementation;
use App\Models\Mou;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImplementationController extends Controller
{
    public function index(Request $request)
    {
        $query = Implementation::with(['mou.institution', 'uploader']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhereHas('mou', fn($m) => $m->where('title', 'like', '%' . $request->search . '%'));
            });
        }
        if ($request->filled('visibility')) {
            $query->where('visibility', $request->visibility);
        }

        $implementations = $query->latest()->paginate(15)->withQueryString();

        return view('admin.implementations.index', compact('implementations'));
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
