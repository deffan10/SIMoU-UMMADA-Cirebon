@extends('layouts.admin')
@section('title', 'Edit Fakultas')

@section('content')
<div class="max-w-lg">
    <form action="{{ route('admin.faculties.update', $faculty) }}" method="POST" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        @csrf @method('PUT')
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama Fakultas *</label><input type="text" name="name" value="{{ $faculty->name }}" required class="w-full rounded-lg border-gray-300 text-sm"></div>
        <div><label class="block text-sm font-medium text-gray-700 mb-1">Kode</label><input type="text" name="code" value="{{ $faculty->code }}" class="w-full rounded-lg border-gray-300 text-sm"></div>
        <div><label class="flex items-center space-x-2"><input type="checkbox" name="is_active" value="1" {{ $faculty->is_active?'checked':'' }} class="rounded border-gray-300 text-blue-600"><span class="text-sm">Aktif</span></label></div>
        <div class="flex justify-end space-x-3"><a href="{{ route('admin.faculties.index') }}" class="px-4 py-2 text-sm border rounded-lg">Batal</a><button class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg">Update</button></div>
    </form>
</div>
@endsection
