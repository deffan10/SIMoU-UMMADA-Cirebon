<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\Mou;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudyProgramController extends Controller
{
    public function index(Faculty $faculty)
    {
        $studyPrograms = $faculty->studyPrograms()->orderBy('level')->orderBy('name')->get();
        return view('admin.faculties.study-programs.index', compact('faculty', 'studyPrograms'));
    }

    public function store(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'level' => 'required|in:D3,D4,S1,S2,S3',
        ]);

        $validated['faculty_id'] = $faculty->id;
        $validated['is_active'] = true;

        // Generate unique slug in case of name similarity
        $slug = Str::slug($validated['name']);
        if (StudyProgram::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::lower($validated['level']) . '-' . Str::random(3);
        }
        $validated['slug'] = $slug;

        StudyProgram::create($validated);

        return redirect()->route('admin.faculties.study-programs.index', $faculty)
            ->with('success', 'Program Studi berhasil ditambahkan.');
    }

    public function edit(StudyProgram $studyProgram)
    {
        $faculty = $studyProgram->faculty;
        return view('admin.faculties.study-programs.edit', compact('studyProgram', 'faculty'));
    }

    public function update(Request $request, StudyProgram $studyProgram)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
            'level' => 'required|in:D3,D4,S1,S2,S3',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        
        // Handle slug change if name is updated
        if ($studyProgram->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            if (StudyProgram::where('slug', $slug)->where('id', '!=', $studyProgram->id)->exists()) {
                $slug .= '-' . Str::lower($validated['level']) . '-' . Str::random(3);
            }
            $validated['slug'] = $slug;
        }

        $studyProgram->update($validated);

        return redirect()->route('admin.faculties.study-programs.index', $studyProgram->faculty_id)
            ->with('success', 'Program Studi berhasil diupdate.');
    }

    public function destroy(StudyProgram $studyProgram)
    {
        // Check if study program is used in Mou
        $isUsed = Mou::where('faculty_id', $studyProgram->faculty_id)
            ->where('study_program', $studyProgram->name)
            ->exists();

        if ($isUsed) {
            return back()->with('error', 'Tidak dapat menghapus program studi yang telah digunakan dalam kerjasama.');
        }

        $faculty = $studyProgram->faculty;
        $studyProgram->delete();

        return redirect()->route('admin.faculties.study-programs.index', $faculty)
            ->with('success', 'Program Studi berhasil dihapus.');
    }
}
