@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">MoU Aktif</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['total_aktif'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Akan Expire</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_akan_expire'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Expired</p>
                <p class="text-2xl font-bold text-red-600">{{ $stats['total_expire'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Institusi Partner</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_institutions'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-building text-blue-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Stats Tipe Dokumen -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total MoU</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['total_mou'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-contract text-blue-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total MoA</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_moa'] }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-signature text-indigo-600"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total IA</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['total_ia'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-file-invoice text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Reminders -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
            <span class="text-orange-600 font-bold text-sm">90</span>
        </div>
        <div>
            <p class="text-sm font-medium text-orange-700">H-90</p>
            <p class="text-lg font-bold text-orange-800">{{ $reminders['h90'] }} MoU</p>
        </div>
    </div>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
            <span class="text-yellow-600 font-bold text-sm">30</span>
        </div>
        <div>
            <p class="text-sm font-medium text-yellow-700">H-30</p>
            <p class="text-lg font-bold text-yellow-800">{{ $reminders['h30'] }} MoU</p>
        </div>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center space-x-3">
        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
            <span class="text-red-600 font-bold text-sm">7</span>
        </div>
        <div>
            <p class="text-sm font-medium text-red-700">H-7</p>
            <p class="text-lg font-bold text-red-800">{{ $reminders['h7'] }} MoU</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Chart -->
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Kerjasama Per Tahun</h3>
        <canvas id="dashYearChart" height="180"></canvas>
    </div>

    <!-- By Category -->
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Per Kategori</h3>
        <canvas id="dashCatChart" height="180"></canvas>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h3>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($recentActivities as $activity)
            <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded-lg">
                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fas fa-clock text-gray-400 text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-700">{{ $activity->description }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $activity->admin?->name ?? 'System' }} &bull; {{ $activity->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada aktivitas.</p>
            @endforelse
        </div>
    </div>

    <!-- Recent Renewals -->
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <h3 class="font-semibold text-gray-900 mb-4">Perpanjangan Terbaru</h3>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @forelse($recentRenewals as $renewal)
            <div class="flex items-start space-x-3 p-2 hover:bg-gray-50 rounded-lg">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-sync text-purple-600 text-xs"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-700">{{ $renewal->mou?->title }}</p>
                    <p class="text-xs text-gray-500">{{ $renewal->mou?->institution?->name }}</p>
                    <p class="text-xs text-gray-400">V{{ $renewal->renewal_number + 1 }} &bull; {{ $renewal->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">Belum ada perpanjangan.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('dashYearChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($mousByYear->pluck('year')) !!},
            datasets: [{ data: {!! json_encode($mousByYear->pluck('total')) !!}, backgroundColor: '#3B82F6', borderRadius: 6 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    const catData = @json($mousByCategory);
    new Chart(document.getElementById('dashCatChart'), {
        type: 'doughnut',
        data: {
            labels: catData.map(c => c.category?.name || 'N/A'),
            datasets: [{ data: catData.map(c => c.total), backgroundColor: ['#3B82F6','#10B981','#F59E0B','#8B5CF6','#EC4899','#06B6D4','#EF4444','#6366F1'] }]
        },
        options: { responsive: true }
    });
});
</script>
@endpush
