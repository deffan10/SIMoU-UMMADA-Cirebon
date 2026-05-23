@extends('layouts.admin')
@section('title', 'Histori Perpanjangan')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.mou.show', $mou) }}" class="text-sm text-gray-600 hover:text-blue-600 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail</a>

    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <h2 class="font-bold text-gray-900">{{ $mou->title }}</h2>
        <p class="text-sm text-gray-500">{{ $mou->institution->name }} &bull; {{ $mou->mou_number }}</p>
    </div>

    <div class="space-y-4">
        @forelse($mou->renewals as $renewal)
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm font-medium">Version {{ $renewal->renewal_number + 1 }}</span>
                <span class="text-sm text-gray-500">{{ $renewal->created_at->format('d M Y H:i') }}</span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Periode Lama:</p>
                    <p class="font-medium">{{ $renewal->old_start_date->format('d/m/Y') }} - {{ $renewal->old_end_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Periode Baru:</p>
                    <p class="font-medium text-green-600">{{ $renewal->new_start_date->format('d/m/Y') }} - {{ $renewal->new_end_date->format('d/m/Y') }}</p>
                </div>
            </div>
            @if($renewal->renewal_note)
            <p class="text-sm text-gray-600 mt-3 p-2 bg-gray-50 rounded">{{ $renewal->renewal_note }}</p>
            @endif
            <p class="text-xs text-gray-400 mt-3">Diperpanjang oleh: {{ $renewal->renewedByAdmin?->name ?? '-' }}</p>
        </div>
        @empty
        <p class="text-center text-gray-400 py-8">Belum ada histori perpanjangan.</p>
        @endforelse
    </div>
</div>
@endsection
