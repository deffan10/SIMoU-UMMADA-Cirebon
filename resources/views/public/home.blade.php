@extends('layouts.public')
@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Repository Kerjasama</h1>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">Sistem Informasi MoU & Kerjasama<br>Universitas Muhammadiyah Ahmad Dahlan Cirebon</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('kerjasama.index') }}" class="px-6 py-3 bg-white text-blue-700 font-semibold rounded-lg hover:bg-blue-50 transition shadow-lg">
                    <i class="fas fa-handshake mr-2"></i> Lihat Kerjasama
                </a>
                <a href="{{ route('statistik') }}" class="px-6 py-3 bg-blue-500/30 text-white font-semibold rounded-lg hover:bg-blue-500/50 transition border border-white/30">
                    <i class="fas fa-chart-bar mr-2"></i> Statistik
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-16">
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5 text-center border border-white/20">
                <div class="text-3xl font-bold">{{ $stats['total_aktif'] }}</div>
                <div class="text-blue-200 text-sm mt-1">Kerjasama Aktif</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5 text-center border border-white/20">
                <div class="text-3xl font-bold">{{ $stats['total_institutions'] }}</div>
                <div class="text-blue-200 text-sm mt-1">Institusi Partner</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5 text-center border border-white/20">
                <div class="text-3xl font-bold">{{ $stats['total_nasional'] }}</div>
                <div class="text-blue-200 text-sm mt-1">Nasional</div>
            </div>
            <div class="bg-white/10 backdrop-blur-sm rounded-xl p-5 text-center border border-white/20">
                <div class="text-3xl font-bold">{{ $stats['total_internasional'] }}</div>
                <div class="text-blue-200 text-sm mt-1">Internasional</div>
            </div>
        </div>
    </div>
</section>

<!-- Category Stats -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900">Kategori Kerjasama</h2>
            <p class="text-gray-600 mt-2">Bidang-bidang kerjasama UMMADA Cirebon</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categoryStats as $cat)
            <div class="p-5 rounded-xl border border-gray-200 hover:shadow-md transition text-center">
                <div class="w-12 h-12 rounded-full mx-auto mb-3 flex items-center justify-center" style="background-color: {{ $cat->color }}20">
                    <i class="fas fa-folder text-lg" style="color: {{ $cat->color }}"></i>
                </div>
                <div class="font-semibold text-gray-900">{{ $cat->name }}</div>
                <div class="text-2xl font-bold mt-1" style="color: {{ $cat->color }}">{{ $cat->mous_count }}</div>
                <div class="text-xs text-gray-500">kerjasama</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Chart Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900">Tren Kerjasama Per Tahun</h2>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-6 max-w-3xl mx-auto">
            <canvas id="yearlyChart" height="200"></canvas>
        </div>
    </div>
</section>

<!-- Recent Partnerships -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Kerjasama Terbaru</h2>
            <a href="{{ route('kerjasama.index') }}" class="text-blue-600 hover:text-blue-700 font-medium text-sm">Lihat Semua <i class="fas fa-arrow-right ml-1"></i></a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($recentMous as $mou)
            <a href="{{ route('kerjasama.show', $mou->slug) }}" class="block bg-white border border-gray-200 rounded-xl p-5 hover:shadow-lg transition group">
                <div class="flex items-start space-x-3 mb-3">
                    <img src="{{ $mou->institution->logo_url }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0" alt="{{ $mou->institution->name }}">
                    <div class="min-w-0 flex-1">
                        <h3 class="font-semibold text-gray-900 text-sm group-hover:text-blue-600 transition line-clamp-2">{{ $mou->title }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $mou->institution->name }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                    <span class="text-xs px-2 py-1 rounded-full" style="background-color: {{ $mou->category?->color ?? '#6B7280' }}20; color: {{ $mou->category?->color ?? '#6B7280' }}">
                        {{ $mou->category?->name ?? 'Umum' }}
                    </span>
                    {!! $mou->status_badge !!}
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Partners -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <h2 class="text-2xl font-bold text-gray-900">Institusi Partner</h2>
            <p class="text-gray-600 mt-2">Jaringan kerjasama UMMADA Cirebon</p>
        </div>
        <div class="flex flex-wrap justify-center gap-6">
            @foreach($partners as $partner)
            <div class="bg-white rounded-xl p-4 shadow-sm hover:shadow-md transition flex items-center justify-center w-32 h-32">
                <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="max-w-full max-h-full object-contain rounded-lg" title="{{ $partner->name }}">
            </div>
            @endforeach
        </div>
    </div>
</section>

@if($renewedMous->count() > 0)
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-8">Baru Diperpanjang</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($renewedMous as $mou)
            <div class="border rounded-xl p-4 flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-sync text-green-600"></i>
                </div>
                <div>
                    <a href="{{ route('kerjasama.show', $mou->slug) }}" class="font-semibold text-gray-900 hover:text-blue-600">{{ $mou->title }}</a>
                    <p class="text-sm text-gray-500">{{ $mou->institution->name }} &bull; Diperpanjang {{ $mou->renewal_count }}x</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('yearlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($yearlyData->pluck('year')) !!},
            datasets: [{
                label: 'Jumlah Kerjasama',
                data: {!! json_encode($yearlyData->pluck('total')) !!},
                backgroundColor: '#3B82F6',
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
});
</script>
@endpush
