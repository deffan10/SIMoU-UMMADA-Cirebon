@extends('layouts.public')
@section('title', $mou->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <a href="{{ route('kerjasama.index') }}" class="text-blue-600 hover:text-blue-700">Kerjasama</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-500">Detail</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-start space-x-4 mb-6">
                    <img src="{{ $mou->institution->logo_url }}" class="w-16 h-16 rounded-xl object-cover" alt="">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $mou->title }}</h1>
                        <p class="text-gray-600 mt-1">{{ $mou->institution->name }}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            {!! $mou->status_badge !!}
                            {!! $mou->level_badge !!}
                            <span class="text-xs px-2 py-1 rounded-full" style="background-color: {{ $mou->category?->color ?? '#6B7280' }}20; color: {{ $mou->category?->color ?? '#6B7280' }}">
                                {{ $mou->category?->name ?? 'Umum' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($mou->public_summary)
                <div class="prose max-w-none">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Ringkasan</h3>
                    <p class="text-gray-600">{{ $mou->public_summary }}</p>
                </div>
                @endif
            </div>

            <!-- PDF Viewer -->
            @if($mou->show_pdf_public && $mou->main_document)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-file-pdf text-red-500 mr-2"></i> Dokumen {{ $mou->getCooperationTypeLabel() }}
                </h3>
                <div class="border rounded-lg overflow-hidden" style="height: 600px;">
                    <iframe src="{{ $mou->main_document_url }}" class="w-full h-full" frameborder="0"></iframe>
                </div>
                <p class="text-xs text-gray-400 mt-2 text-center">
                    <i class="fas fa-shield-alt mr-1"></i> Dokumen Publik - UMMADA Cirebon
                </p>
            </div>
            @endif

            <!-- Renewal History (Public) -->
            @if($mou->renewal_count > 0)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-history text-blue-500 mr-2"></i> Histori Perpanjangan
                </h3>
                <p class="text-sm text-gray-600 mb-4">Kerjasama ini telah diperpanjang <strong>{{ $mou->renewal_count }}</strong> kali.</p>
                <div class="space-y-3">
                    @foreach($mou->renewals as $renewal)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-blue-600">V{{ $renewal->renewal_number + 1 }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Perpanjangan ke-{{ $renewal->renewal_number }}</p>
                            <p class="text-xs text-gray-500">{{ $renewal->new_start_date->format('d M Y') }} - {{ $renewal->new_end_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Implementasi -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-clipboard-check text-green-500 mr-2"></i> Implementasi
                </h3>
                @php
                    $publicImplementations = $mou->publicImplementations;
                    $totalImplementations = $mou->implementations()->count();
                @endphp

                @if($totalImplementations === 0)
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
                    <i class="fas fa-info-circle text-gray-400 text-xl mb-2"></i>
                    <p class="text-sm text-gray-500">Belum ada implementasi</p>
                </div>
                @elseif($publicImplementations->count() > 0)
                <div class="space-y-3">
                    @foreach($publicImplementations as $impl)
                    <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-900">{{ $impl->title }}</p>
                                @if($impl->description)
                                <p class="text-xs text-green-700 mt-0.5">{{ $impl->description }}</p>
                                @endif
                            </div>
                            @if($impl->file_path)
                            <a href="{{ $impl->file_url }}" target="_blank" class="flex-shrink-0 px-3 py-1 bg-green-600 text-white text-xs rounded-lg hover:bg-green-700">
                                <i class="fas fa-file-pdf mr-1"></i> PDF
                            </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-center">
                    <i class="fas fa-lock text-yellow-500 text-xl mb-2"></i>
                    <p class="text-sm text-yellow-700">File implementasi bersifat private</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Detail Kerjasama</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nomor MoU</dt>
                        <dd class="font-medium text-gray-900">{{ $mou->mou_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tipe Dokumen</dt>
                        <dd class="font-medium text-gray-900">{{ $mou->getCooperationTypeLabel() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Jenis</dt>
                        <dd class="font-medium text-gray-900">{{ $mou->getTypeLabel() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tingkat</dt>
                        <dd class="font-medium text-gray-900 capitalize">{{ $mou->level }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tanggal Mulai</dt>
                        <dd class="font-medium text-gray-900">{{ $mou->start_date->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tanggal Berakhir</dt>
                        <dd class="font-medium text-gray-900">{{ $mou->end_date->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Durasi</dt>
                        <dd class="font-medium text-gray-900">{{ $mou->duration_text }}</dd>
                    </div>
                    @if($mou->faculty)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Fakultas</dt>
                        <dd class="font-medium text-gray-900">{{ $mou->faculty->name }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>{!! $mou->status_badge !!}</dd>
                    </div>
                </dl>
            </div>

            <!-- Institution Info -->
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Institusi Partner</h3>
                <div class="flex items-center space-x-3 mb-3">
                    <img src="{{ $mou->institution->logo_url }}" class="w-12 h-12 rounded-lg object-cover" alt="">
                    <div>
                        <p class="font-medium text-gray-900">{{ $mou->institution->name }}</p>
                        <p class="text-xs text-gray-500">{{ $mou->institution->getTypeLabel() }}</p>
                    </div>
                </div>
                <div class="text-sm text-gray-600 space-y-1 mt-3">
                    @if($mou->institution->city)
                    <p><i class="fas fa-map-marker-alt text-gray-400 w-5"></i> {{ $mou->institution->city }}, {{ $mou->institution->country }}</p>
                    @endif
                    @if($mou->institution->website)
                    <p><i class="fas fa-globe text-gray-400 w-5"></i> <a href="{{ $mou->institution->website }}" target="_blank" class="text-blue-600 hover:underline">Website</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Related -->
    @if($relatedMous->count() > 0)
    <div class="mt-12">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Kerjasama Terkait</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($relatedMous as $related)
            <a href="{{ route('kerjasama.show', $related->slug) }}" class="bg-white rounded-lg border p-4 hover:shadow-md transition">
                <h4 class="font-medium text-sm text-gray-900 line-clamp-2">{{ $related->title }}</h4>
                <p class="text-xs text-gray-500 mt-2">{{ $related->institution->name }}</p>
            </a>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
