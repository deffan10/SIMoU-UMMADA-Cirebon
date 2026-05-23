@extends('layouts.admin')
@section('title', 'Detail Institusi')

@section('content')
<div class="max-w-3xl">
    <a href="{{ route('admin.institutions.index') }}" class="text-sm text-gray-600 hover:text-blue-600 mb-4 inline-block"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>

    <div class="bg-white rounded-xl shadow-sm border p-6">
        <div class="flex items-center space-x-4 mb-6">
            <img src="{{ $institution->logo_url }}" class="w-16 h-16 rounded-xl object-cover">
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $institution->name }}</h2>
                <p class="text-sm text-gray-500">{{ $institution->getTypeLabel() }} &bull; {{ $institution->city }}, {{ $institution->country }}</p>
                <p class="text-sm text-blue-600 mt-1">{{ $institution->mous_count }} kerjasama</p>
            </div>
        </div>

        @if($institution->mous->count() > 0)
        <h4 class="font-semibold text-gray-900 mb-3">MoU Terkait</h4>
        <div class="space-y-2">
            @foreach($institution->mous as $mou)
            <a href="{{ route('admin.mou.show', $mou) }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                <p class="text-sm font-medium text-gray-900">{{ $mou->title }}</p>
                <p class="text-xs text-gray-500">{{ $mou->mou_number }} &bull; {!! $mou->status_badge !!}</p>
            </a>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
