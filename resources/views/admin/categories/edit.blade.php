@extends('layouts.admin')
@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
            <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
            <input type="color" name="color" value="{{ old('color', $category->color) }}" class="rounded border-gray-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
            <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        <div>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" value="1" {{ $category->is_active?'checked':'' }} class="rounded border-gray-300 text-blue-600">
                <span class="text-sm">Aktif</span>
            </label>
        </div>
        <div class="flex justify-end space-x-3"><a href="{{ route('admin.categories.index') }}" class="px-4 py-2 text-sm border rounded-lg">Batal</a><button class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg">Update</button></div>
    </form>
</div>
@endsection
