<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi MoU - {{ config('app.name') }}">
    <title>@yield('title', 'SIMoU') - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">M</span>
                        </div>
                        <div class="hidden sm:block">
                            <span class="text-lg font-bold text-gray-900">SIMoU</span>
                            <span class="text-xs text-gray-500 block -mt-1">UMMADA Cirebon</span>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('home') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('home') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        Beranda
                    </a>
                    <a href="{{ route('kerjasama.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('kerjasama.*') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        Kerjasama
                    </a>
                    <a href="{{ route('statistik') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('statistik') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        Statistik
                    </a>
                    <a href="{{ route('tentang') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('tentang') ? 'text-blue-600 bg-blue-50' : 'text-gray-700 hover:text-blue-600 hover:bg-blue-50' }}">
                        Tentang
                    </a>
                    <a href="{{ route('admin.login') }}" class="ml-4 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-lock mr-1"></i> Login Admin
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="p-2 rounded-md text-gray-700 hover:bg-gray-100">
                        <i class="fas" :class="open ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-cloak class="md:hidden border-t bg-white">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-blue-50">Beranda</a>
                <a href="{{ route('kerjasama.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-blue-50">Kerjasama</a>
                <a href="{{ route('statistik') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-blue-50">Statistik</a>
                <a href="{{ route('tentang') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-blue-50">Tentang</a>
                <a href="{{ route('admin.login') }}" class="block px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg text-center">Login Admin</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">M</span>
                        </div>
                        <div>
                            <span class="font-bold">SIMoU</span>
                            <span class="text-gray-400 block text-xs">UMMADA Cirebon</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm">Sistem Informasi Repository MoU/Kerjasama Universitas Muhammadiyah Ahmad Dahlan Cirebon.</p>
                </div>
                <div>
                    <h3 class="font-semibold mb-3">Link Cepat</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white">Beranda</a></li>
                        <li><a href="{{ route('kerjasama.index') }}" class="hover:text-white">Daftar Kerjasama</a></li>
                        <li><a href="{{ route('statistik') }}" class="hover:text-white">Statistik</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-3">Kontak</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><i class="fas fa-globe mr-2"></i> ummada.ac.id</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@ummada.ac.id</li>
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Cirebon, Jawa Barat</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} SIMoU UMMADA Cirebon. All rights reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
