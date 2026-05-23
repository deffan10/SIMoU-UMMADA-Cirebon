@extends('layouts.public')
@section('title', 'Statistik Kerjasama')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Statistik Kerjasama</h1>
        <p class="text-gray-600 mt-2">Data dan informasi kerjasama UMMADA Cirebon</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-4xl font-bold text-blue-600">{{ $totalMous }}</div>
            <div class="text-gray-600 mt-1">Total Kerjasama</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-4xl font-bold text-green-600">{{ $totalActive }}</div>
            <div class="text-gray-600 mt-1">Kerjasama Aktif</div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6 text-center">
            <div class="text-4xl font-bold text-purple-600">{{ $totalInstitutions }}</div>
            <div class="text-gray-600 mt-1">Institusi Partner</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- By Year Chart -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Kerjasama Per Tahun</h3>
            <canvas id="yearChart" height="200"></canvas>
        </div>

        <!-- By Level -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Berdasarkan Tingkat</h3>
            <canvas id="levelChart" height="200"></canvas>
        </div>

        <!-- By Category -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Berdasarkan Kategori</h3>
            <div class="space-y-3">
                @foreach($byCategory as $cat)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $cat->color }}"></div>
                        <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                    </div>
                    <span class="font-semibold text-gray-900">{{ $cat->mous_count }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Institutions -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Institusi Paling Aktif</h3>
            <div class="space-y-3">
                @foreach($topInstitutions as $i => $inst)
                <div class="flex items-center space-x-3">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">{{ $i + 1 }}</span>
                    <span class="text-sm text-gray-700 flex-1">{{ $inst->name }}</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $inst->mous_count }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('yearChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($byYear->pluck('year')) !!},
            datasets: [{
                label: 'Kerjasama',
                data: {!! json_encode($byYear->pluck('total')) !!},
                borderColor: '#3B82F6',
                backgroundColor: '#3B82F620',
                fill: true, tension: 0.4,
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('levelChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($byLevel->pluck('level')->map(fn($l) => ucfirst($l))) !!},
            datasets: [{
                data: {!! json_encode($byLevel->pluck('total')) !!},
                backgroundColor: ['#6B7280', '#3B82F6', '#8B5CF6'],
            }]
        },
        options: { responsive: true }
    });
});
</script>
@endpush
