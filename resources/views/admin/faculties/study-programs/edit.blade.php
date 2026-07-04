@extends('layouts.admin')
@section('title', 'Edit Program Studi - ' . $studyProgram->name)

@section('content')
<div class="mb-6">
    <nav class="text-sm mb-2">
        <a href="{{ route('admin.faculties.index') }}" class="text-blue-600 hover:underline">Fakultas</a>
        <span class="mx-2 text-gray-400">/</span>
        <a href="{{ route('admin.faculties.study-programs.index', $faculty) }}" class="text-blue-600 hover:underline">Program Studi ({{ $faculty->name }})</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-500">Edit</span>
    </nav>
    <h1 class="text-2xl font-bold text-gray-900">Edit Program Studi (Prodi)</h1>
    <p class="text-sm text-gray-500">Fakultas: <span class="font-semibold text-gray-800">{{ $faculty->name }}</span></p>
</div>

<div class="max-w-xl">
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <form action="{{ route('admin.study-programs.update', $studyProgram) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program Studi *</label>
                <input type="text" name="name" value="{{ old('name', $studyProgram->name) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kode Prodi</label>
                <input type="text" name="code" value="{{ old('code', $studyProgram->code) }}" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Pendidikan *</label>
                <select name="level" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="S1" {{ old('level', $studyProgram->level)=='S1'?'selected':'' }}>S1 - Sarjana</option>
                    <option value="D3" {{ old('level', $studyProgram->level)=='D3'?'selected':'' }}>D3 - Diploma 3</option>
                    <option value="D4" {{ old('level', $studyProgram->level)=='D4'?'selected':'' }}>D4 - Diploma 4</option>
                    <option value="S2" {{ old('level', $studyProgram->level)=='S2'?'selected':'' }}>S2 - Magister</option>
                    <option value="S3" {{ old('level', $studyProgram->level)=='S3'?'selected':'' }}>S3 - Doktor</option>
                </select>
            </div>

            <div class="flex items-center space-x-3 py-2 border-t border-b">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $studyProgram->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 select-none">Status Program Studi Aktif</label>
            </div>

            <div class="flex justify-end space-x-3 pt-2">
                <a href="{{ route('admin.faculties.study-programs.index', $faculty) }}" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
