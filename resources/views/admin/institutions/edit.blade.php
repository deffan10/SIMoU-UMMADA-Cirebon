@extends('layouts.admin')
@section('title', 'Edit Institusi')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.institutions.update', $institution) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Institusi *</label>
                <input type="text" name="name" value="{{ old('name', $institution->name) }}" required class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                <select name="type" required class="w-full rounded-lg border-gray-300 text-sm">
                    @foreach(['universitas','pemerintah','industri','sekolah','ngo','organisasi','lainnya'] as $t)
                    <option value="{{ $t }}" {{ $institution->type==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Negara</label>
                <input type="text" name="country" value="{{ old('country', $institution->country) }}" required class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                <input type="text" name="city" value="{{ old('city', $institution->city) }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                <input type="url" name="website" value="{{ old('website', $institution->website) }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $institution->email) }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $institution->phone) }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                @if($institution->logo)<p class="text-xs text-gray-400 mb-1">Logo saat ini tersedia.</p>@endif
                <input type="file" name="logo" accept="image/*" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="address" rows="2" class="w-full rounded-lg border-gray-300 text-sm">{{ old('address', $institution->address) }}</textarea>
            </div>
        </div>
        <div class="flex justify-end space-x-3 pt-4">
            <a href="{{ route('admin.institutions.index') }}" class="px-4 py-2 text-sm border rounded-lg">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Update</button>
        </div>
    </form>
</div>
@endsection
