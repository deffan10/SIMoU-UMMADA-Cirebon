<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InstitutionController extends Controller
{
    public function index(Request $request)
    {
        $query = Institution::withCount('mous');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $institutions = $query->latest()->paginate(15)->withQueryString();

        return view('admin.institutions.index', compact('institutions'));
    }

    public function create()
    {
        return view('admin.institutions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:universitas,pemerintah,industri,sekolah,ngo,organisasi,lainnya',
            'country' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('institutions', 'public');
        }

        $institution = Institution::create($validated);

        ActivityLogService::log('create', $institution, "Institusi '{$institution->name}' ditambahkan");

        return redirect()->route('admin.institutions.index')->with('success', 'Institusi berhasil ditambahkan.');
    }

    public function show(Institution $institution)
    {
        $institution->load(['mous' => function ($q) {
            $q->latest()->take(10);
        }]);
        $institution->loadCount('mous');

        return view('admin.institutions.show', compact('institution'));
    }

    public function edit(Institution $institution)
    {
        return view('admin.institutions.edit', compact('institution'));
    }

    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:universitas,pemerintah,industri,sekolah,ngo,organisasi,lainnya',
            'country' => 'required|string|max:100',
            'city' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($institution->logo) {
                Storage::disk('public')->delete($institution->logo);
            }
            $validated['logo'] = $request->file('logo')->store('institutions', 'public');
        }

        $institution->update($validated);

        ActivityLogService::log('update', $institution, "Institusi '{$institution->name}' diupdate");

        return redirect()->route('admin.institutions.index')->with('success', 'Institusi berhasil diupdate.');
    }

    public function destroy(Institution $institution)
    {
        if ($institution->mous()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus institusi yang memiliki MoU.');
        }

        ActivityLogService::log('delete', $institution, "Institusi '{$institution->name}' dihapus");
        $institution->delete();

        return redirect()->route('admin.institutions.index')->with('success', 'Institusi berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $institutions = Institution::where('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->take(10)
            ->get(['id', 'name', 'type', 'city', 'logo']);

        return response()->json($institutions);
    }
}
