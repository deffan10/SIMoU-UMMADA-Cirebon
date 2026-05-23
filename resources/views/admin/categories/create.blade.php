@extends('layouts.admin')
@section('title', 'Tambah Kategori')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
            <input type="color" name="color" value="{{ old('color', '#3B82F6') }}" class="rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ old('description') }}</textarea>
        </div>
        <div class="flex justify-end space-x-3"><a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm border rounded-lg">Batal</a><button class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg">Simpan</button></div>
    </form>
</div>
@endsection
