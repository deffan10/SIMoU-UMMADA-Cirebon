@extends('layouts.admin')
@section('title', 'Pengaturan')

@section('content')
<div class="max-w-2xl space-y-6">
    <!-- Halaman Tentang -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-900"><i class="fas fa-file-alt text-indigo-500 mr-2"></i>Halaman Tentang</h3>
                <p class="text-sm text-gray-500 mt-1">Edit konten halaman "Tentang" yang tampil di website publik.</p>
            </div>
            <a href="{{ route('admin.settings.about') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                <i class="fas fa-edit mr-1"></i> Edit Konten
            </a>
        </div>
    </div>

    <!-- Hero Image -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-panorama text-orange-500 mr-2"></i>Hero Image (Landing Page)</h3>
        <p class="text-sm text-gray-500 mb-4">Gambar latar belakang di hero section halaman utama. Ukuran rekomendasi: <strong>1920x600 px</strong> (landscape, minimal lebar 1200px).</p>

        @if($currentHero)
        <div class="mb-4">
            <p class="text-xs text-gray-500 mb-2">Preview Hero Saat Ini:</p>
            <div class="border rounded-lg overflow-hidden">
                <img src="{{ asset('storage/' . $currentHero) }}" class="w-full h-40 object-cover" alt="Hero Image">
            </div>
        </div>
        @endif

        <form action="{{ route('admin.settings.hero') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div>
                <input type="file" name="hero_image" accept="image/png,image/jpeg,image/webp" required
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                <p class="text-xs text-gray-400 mt-1">Format: JPG, PNG, WebP. Max 10MB. Rekomendasi: 1920x600 px.</p>
            </div>
            @error('hero_image')
            <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
            <div class="flex items-center space-x-3">
                <button type="submit" class="px-5 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700">
                    <i class="fas fa-upload mr-1"></i> Upload Hero Image
                </button>
                @if($currentHero)
                <button type="button" onclick="document.getElementById('remove-hero-form').submit()" class="px-4 py-2 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50">
                    <i class="fas fa-trash mr-1"></i> Hapus
                </button>
                @endif
            </div>
        </form>
        @if($currentHero)
        <form id="remove-hero-form" action="{{ route('admin.settings.hero.remove') }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
        @endif
    </div>

    <!-- Site Logo -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-image text-green-500 mr-2"></i>Logo Website</h3>
        <p class="text-sm text-gray-500 mb-4">Logo akan ditampilkan di navbar public, halaman login, sidebar admin, dan otomatis generate favicon.</p>

        <!-- Current Logo Preview -->
        <div class="mb-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-xs text-gray-500 mb-2">Preview Logo Saat Ini:</p>
            <div class="flex items-center space-x-6">
                <!-- Navbar Preview -->
                <div class="text-center">
                    <div class="bg-white border rounded-lg p-3 inline-flex items-center space-x-2">
                        @if($siteHasLogo)
                        <img src="{{ $siteLogo }}" class="h-8 w-auto object-contain" alt="Logo">
                        @else
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">M</span>
                        </div>
                        @endif
                        <span class="text-sm font-bold text-gray-900">SIMoU</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Navbar</p>
                </div>

                <!-- Favicon Preview -->
                <div class="text-center">
                    <div class="bg-white border rounded-lg p-3 inline-flex items-center justify-center">
                        @if($currentFavicon)
                        <img src="{{ asset('storage/' . $currentFavicon) }}" class="w-8 h-8 object-contain" alt="Favicon">
                        @else
                        <div class="w-8 h-8 bg-gray-200 rounded flex items-center justify-center">
                            <span class="text-gray-400 text-xs">?</span>
                        </div>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Favicon</p>
                </div>

                <!-- Large Preview -->
                @if($siteHasLogo)
                <div class="text-center">
                    <div class="bg-white border rounded-lg p-3 inline-flex items-center justify-center">
                        <img src="{{ $siteLogo }}" class="h-16 w-auto object-contain" alt="Logo Large">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Original</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Upload Form -->
        <form action="{{ route('admin.settings.logo') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Logo Baru</label>
                <input type="file" name="site_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" required
                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-400 mt-1">Format: PNG, JPG, SVG, WebP. Max 5MB. Rekomendasi: PNG transparan, rasio 1:1 atau landscape.</p>
            </div>
            @error('site_logo')
            <p class="text-red-500 text-xs">{{ $message }}</p>
            @enderror
            <div class="flex items-center space-x-3">
                <button type="submit" class="px-5 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                    <i class="fas fa-upload mr-1"></i> Upload & Generate Favicon
                </button>
                @if($siteHasLogo)
                <button type="button" onclick="document.getElementById('remove-logo-form').submit()" class="px-4 py-2 text-sm text-red-600 border border-red-200 rounded-lg hover:bg-red-50">
                    <i class="fas fa-trash mr-1"></i> Hapus Logo
                </button>
                @endif
            </div>
        </form>

        <!-- Remove Logo Form (hidden) -->
        @if($siteHasLogo)
        <form id="remove-logo-form" action="{{ route('admin.settings.logo.remove') }}" method="POST" class="hidden">
            @csrf @method('DELETE')
        </form>
        @endif

        <!-- Info -->
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-xs text-blue-700"><i class="fas fa-info-circle mr-1"></i> Saat upload logo, sistem otomatis akan:</p>
            <ul class="text-xs text-blue-600 mt-1 ml-4 list-disc space-y-0.5">
                <li>Menyimpan logo original untuk navbar & halaman</li>
                <li>Generate favicon 32x32 pixel secara otomatis</li>
                <li>Mengganti favicon di seluruh halaman website</li>
            </ul>
        </div>
    </div>

    <!-- Profile -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-user text-blue-500 mr-2"></i>Profil</h3>
        <form action="{{ route('admin.settings.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf @method('PUT')
            <div class="flex items-center space-x-4 mb-4">
                <img src="{{ $admin->photo_url }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-200">
                <div>
                    <input type="file" name="photo" accept="image/*" class="text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
                    <p class="text-xs text-gray-400 mt-1">Foto profil admin. Max 2MB.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ $admin->name }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ $admin->email }}" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                    <input type="text" name="phone" value="{{ $admin->phone }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                </div>
            </div>
            <button class="px-6 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">Update Profil</button>
        </form>
    </div>

    <!-- Password -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-lock text-red-500 mr-2"></i>Ganti Password</h3>
        <form action="{{ route('admin.settings.password') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Lama *</label>
                <input type="password" name="current_password" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru *</label>
                <input type="password" name="password" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">Min 8 karakter, huruf besar, huruf kecil, angka</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password *</label>
                <input type="password" name="password_confirmation" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
            </div>
            <button class="px-6 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">Ganti Password</button>
        </form>
    </div>
</div>
@endsection
