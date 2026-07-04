@extends('layouts.public')
@section('title', 'Daftar Kerjasama')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Daftar Kerjasama</h1>
        <p class="text-gray-600 mt-2">Publikasi MoU & kerjasama UMMADA Cirebon</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border p-4 mb-6" x-data="{ showFilter: false }">
        <form action="{{ route('kerjasama.index') }}" method="GET">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kerjasama..." class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="button" @click="showFilter = !showFilter" class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
            </div>

            <div x-show="showFilter" x-cloak class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mt-4 pt-4 border-t">
                <select name="cooperation_type" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Semua Tipe Dokumen</option>
                    <option value="mou" {{ request('cooperation_type') == 'mou' ? 'selected' : '' }}>MoU</option>
                    <option value="moa" {{ request('cooperation_type') == 'moa' ? 'selected' : '' }}>MoA</option>
                    <option value="ia" {{ request('cooperation_type') == 'ia' ? 'selected' : '' }}>IA</option>
                    <option value="pks" {{ request('cooperation_type') == 'pks' ? 'selected' : '' }}>PKS</option>
                    <option value="lainnya" {{ request('cooperation_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                <select name="category" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" {{ request('category') == $cat->slug ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
                <select name="institution" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Semua Institusi</option>
                    @foreach($institutions as $inst)
                    <option value="{{ $inst->id }}" {{ request('institution') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                    @endforeach
                </select>
                <select name="year" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
                <select name="level" class="rounded-lg border-gray-300 text-sm">
                    <option value="">Semua Level</option>
                    <option value="lokal" {{ request('level') == 'lokal' ? 'selected' : '' }}>Lokal</option>
                    <option value="nasional" {{ request('level') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                    <option value="internasional" {{ request('level') == 'internasional' ? 'selected' : '' }}>Internasional</option>
                </select>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($mous as $mou)
        <a href="{{ route('kerjasama.show', $mou->slug) }}" class="block bg-white rounded-xl border hover:shadow-lg transition group">
            <div class="p-5">
                <div class="flex items-start space-x-3 mb-3">
                    <img src="{{ $mou->institution->logo_url }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0" alt="">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition line-clamp-2">{{ $mou->title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ $mou->institution->name }}</p>
                    </div>
                </div>
                @if($mou->public_summary)
                <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $mou->public_summary }}</p>
                @endif
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-100">
                    <div class="flex items-center space-x-2">
                        <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: {{ $mou->category?->color ?? '#6B7280' }}20; color: {{ $mou->category?->color ?? '#6B7280' }}">
                            {{ $mou->category?->name ?? 'Umum' }}
                        </span>
                        {!! $mou->level_badge !!}
                    </div>
                    {!! $mou->status_badge !!}
                </div>
                <div class="flex items-center text-xs text-gray-400 mt-3 space-x-3">
                    <span><i class="far fa-calendar mr-1"></i> {{ $mou->start_date->format('M Y') }} - {{ $mou->end_date->format('M Y') }}</span>
                </div>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">Tidak ada data kerjasama ditemukan.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $mous->links() }}
    </div>
</div>
@endsection
