<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - SIMoU Admin</title>
    <link rel="icon" type="image/png" href="{{ $siteFavicon ?? asset('favicon.ico') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    @stack('styles')
</head>
<body class="bg-gray-100 min-h-screen" x-data="{ sidebarOpen: true, mobileSidebar: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden lg:flex lg:flex-shrink-0" :class="sidebarOpen ? 'lg:w-64' : 'lg:w-20'">
            <div class="flex flex-col w-full bg-gray-900 text-white transition-all duration-300">
                <!-- Logo -->
                <div class="flex items-center h-16 px-4 bg-gray-800">
                    @if($siteHasLogo ?? false)
                    <img src="{{ $siteLogo }}" class="h-7 w-auto object-contain flex-shrink-0" alt="Logo">
                    @else
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">M</span>
                    </div>
                    @endif
                    <span x-show="sidebarOpen" class="ml-3 font-bold text-sm">SIMoU Admin</span>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-tachometer-alt w-5 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.mou.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.mou.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-file-contract w-5 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3">Data MoU</span>
                    </a>
                    <a href="{{ route('admin.institutions.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.institutions.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-building w-5 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3">Institusi</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.categories.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-tags w-5 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3">Kategori</span>
                    </a>
                    <a href="{{ route('admin.faculties.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.faculties.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-university w-5 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3">Fakultas</span>
                    </a>
                    <a href="{{ route('admin.import.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.import.*') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                        <i class="fas fa-file-import w-5 text-center"></i>
                        <span x-show="sidebarOpen" class="ml-3">Import Data</span>
                    </a>

                    <div class="pt-4 mt-4 border-t border-gray-700">
                        <a href="{{ route('admin.notifications') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.notifications') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fas fa-bell w-5 text-center"></i>
                            <span x-show="sidebarOpen" class="ml-3">Notifikasi</span>
                        </a>
                        <a href="{{ route('admin.activity-logs') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.activity-logs') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fas fa-history w-5 text-center"></i>
                            <span x-show="sidebarOpen" class="ml-3">Activity Log</span>
                        </a>
                        <a href="{{ route('admin.settings') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.settings') ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                            <i class="fas fa-cog w-5 text-center"></i>
                            <span x-show="sidebarOpen" class="ml-3">Pengaturan</span>
                        </a>
                    </div>
                </nav>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div x-show="mobileSidebar" x-cloak class="lg:hidden fixed inset-0 z-50">
            <div class="absolute inset-0 bg-black/50" @click="mobileSidebar = false"></div>
            <div class="relative w-64 bg-gray-900 h-full text-white overflow-y-auto">
                <div class="flex items-center h-16 px-4 bg-gray-800">
                    @if($siteHasLogo ?? false)
                    <img src="{{ $siteLogo }}" class="h-7 w-auto object-contain" alt="Logo">
                    @else
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">M</span>
                    </div>
                    @endif
                    <span class="ml-3 font-bold text-sm">SIMoU Admin</span>
                </div>
                <nav class="py-4 px-2 space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800"><i class="fas fa-tachometer-alt w-5 text-center"></i><span class="ml-3">Dashboard</span></a>
                    <a href="{{ route('admin.mou.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800"><i class="fas fa-file-contract w-5 text-center"></i><span class="ml-3">Data MoU</span></a>
                    <a href="{{ route('admin.institutions.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800"><i class="fas fa-building w-5 text-center"></i><span class="ml-3">Institusi</span></a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800"><i class="fas fa-tags w-5 text-center"></i><span class="ml-3">Kategori</span></a>
                    <a href="{{ route('admin.faculties.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800"><i class="fas fa-university w-5 text-center"></i><span class="ml-3">Fakultas</span></a>
                    <a href="{{ route('admin.import.index') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800"><i class="fas fa-file-import w-5 text-center"></i><span class="ml-3">Import</span></a>
                    <a href="{{ route('admin.settings') }}" class="flex items-center px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-gray-800"><i class="fas fa-cog w-5 text-center"></i><span class="ml-3">Pengaturan</span></a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm border-b h-16 flex items-center px-4 lg:px-6 justify-between">
                <div class="flex items-center space-x-3">
                    <button @click="mobileSidebar = true" class="lg:hidden p-2 rounded-md hover:bg-gray-100">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button @click="sidebarOpen = !sidebarOpen" class="hidden lg:block p-2 rounded-md hover:bg-gray-100">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('home') }}" target="_blank" class="text-sm text-gray-500 hover:text-blue-600">
                        <i class="fas fa-external-link-alt mr-1"></i> Lihat Website
                    </a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                            <img src="{{ auth()->guard('admin')->user()->photo_url }}" class="w-8 h-8 rounded-full object-cover">
                            <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->guard('admin')->user()->name }}</span>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg py-1 z-50">
                            <a href="{{ route('admin.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Pengaturan
                            </a>
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                <!-- Flash Messages -->
                @if(session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-700 text-sm">{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center" x-data="{ show: true }" x-show="show">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                    <span class="text-red-700 text-sm">{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button>
                </div>
                @endif

                @if($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="font-medium text-red-700 text-sm mb-2"><i class="fas fa-exclamation-triangle mr-1"></i> Terdapat kesalahan:</p>
                    <ul class="list-disc pl-5 text-sm text-red-600 space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
