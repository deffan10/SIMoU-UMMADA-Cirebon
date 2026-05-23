@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div class="max-w-2xl space-y-6">
    <!-- Profile -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-user text-blue-500 mr-2"></i>Profil</h3>
        <form action="{{ route('admin.settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="flex items-center space-x-4 mb-4">
                <img src="{{ $admin->photo_url }}" class="w-16 h-16 rounded-full object-cover">
                <input type="file" name="photo" accept="image/*" class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Nama</label><input type="text" name="name" value="{{ $admin->name }}" required class="w-full rounded-lg border-gray-300 text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" value="{{ $admin->email }}" required class="w-full rounded-lg border-gray-300 text-sm"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label><input type="text" name="phone" value="{{ $admin->phone }}" class="w-full rounded-lg border-gray-300 text-sm"></div>
            </div>
            <button class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Update Profil</button>
        </form>
    </div>

    <!-- Password -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-lock text-red-500 mr-2"></i>Ganti Password</h3>
        <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Password Lama *</label><input type="password" name="current_password" required class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Password Baru *</label><input type="password" name="password" required class="w-full rounded-lg border-gray-300 text-sm"><p class="text-xs text-gray-400 mt-1">Min 8 karakter, huruf besar, huruf kecil, angka</p></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password *</label><input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 text-sm"></div>
            <button class="px-6 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Ganti Password</button>
        </form>
    </div>
</div>
@endsection
