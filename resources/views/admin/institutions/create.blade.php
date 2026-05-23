@extends('layouts.admin')
@section('title', 'Tambah Institusi')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.institutions.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Institusi *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                <select name="type" required class="w-full rounded-lg border-gray-300 text-sm">
                    @foreach(['universitas','pemerintah','industri','sekolah','ngo','organisasi','lainnya'] as $t)
                    <option value="{{ $t }}">{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Negara *</label>
                <input type="text" name="country" value="{{ old('country', 'Indonesia') }}" required class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                <input type="text" name="city" value="{{ old('city') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="url" name="website" value="{{ old('website') }}" class="w-full rounded-lg border-gray-300 text-sm" placeholder="https://">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                <input type="file" name="logo" accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ old('address') }}</textarea>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-4">
            <a href="{{ route('admin.institutions.index') }}" class="px-4 py-2 text-sm border rounded-lg">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Simpan</button>
        </div>
    </form>
</div>
@endsection
