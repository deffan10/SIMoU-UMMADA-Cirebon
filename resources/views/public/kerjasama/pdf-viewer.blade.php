@extends('layouts.public')
@section('title', 'PDF - ' . $mou->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('kerjasama.show', $mou->slug) }}" class="text-blue-600 hover:text-blue-700">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Detail
        </a>
        <p class="text-sm text-gray-500">
            <i class="fas fa-shield-alt mr-1"></i> Dokumen Publik - UMMADA Cirebon
        </p>
    </div>
    <div class="bg-white rounded-xl shadow-lg border overflow-hidden" style="height: 80vh;">
        <iframe src="{{ $mou->main_document_url }}#toolbar=0" class="w-full h-full" frameborder="0"></iframe>
    </div>
</div>
@endsection
