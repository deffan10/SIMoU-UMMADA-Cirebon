@extends('layouts.admin')
@section('title', 'Edit Implementasi')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.implementations.update', $implementation) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <!-- MoU Info (read-only) -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-file-contract text-blue-500 mr-2"></i>MoU Terkait</h3>
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-sm font-medium text-blue-900">{{ $implementation->mou->title }}</p>
                <p class="text-xs text-blue-600">{{ $implementation->mou->mou_number }} &bull; {{ $implementation->mou->institution->name ?? '' }}</p>
            </div>
        </div>

        <!-- Detail -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-clipboard-check text-green-500 mr-2"></i>Detail Implementasi</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Implementasi *</label>
                    <input type="text" name="title" value="{{ old('title', $implementation->title) }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">{{ old('description', $implementation->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File PDF</label>
                    @if($implementation->file_path)
                    <p class="text-sm text-gray-600 mb-2"><i class="fas fa-file-pdf text-red-500 mr-1"></i> {{ $implementation->original_filename }} ({{ $implementation->file_size_formatted }})</p>
                    @endif
                    <input type="file" name="file" accept=".pdf"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti file.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visibility *</label>
                    <select name="visibility" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 outline-none">
                        <option value="private" {{ $implementation->visibility=='private'?'selected':'' }}>Private (hanya admin)</option>
                        <option value="public" {{ $implementation->visibility=='public'?'selected':'' }}>Public (tampil di website)</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.implementations.index') }}" class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Update Implementasi
            </button>
        </div>
    </form>
</div>
@endsection
