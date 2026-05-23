@extends('layouts.admin')
@section('title', 'Detail MoU')

@section('content')
<div class="max-w-5xl">
    <!-- Header Actions -->
    <div class="flex flex-wrap items-center justify-between mb-6 gap-3">
        <a href="{{ route('admin.mou.index') }}" class="text-sm text-gray-600 hover:text-blue-600"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.renewal.create', $mou) }}" class="px-3 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700"><i class="fas fa-sync mr-1"></i> Perpanjang</a>
            <a href="{{ route('admin.mou.edit', $mou) }}" class="px-3 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600"><i class="fas fa-edit mr-1"></i> Edit</a>
            <form action="{{ route('admin.mou.destroy', $mou) }}" method="POST" onsubmit="return confirm('Hapus?')">
                @csrf @method('DELETE')
                <button class="px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700"><i class="fas fa-trash mr-1"></i> Hapus</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- Main Info -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <div class="flex items-start space-x-4 mb-4">
                    <img src="{{ $mou->institution->logo_url }}" class="w-14 h-14 rounded-xl object-cover">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $mou->title }}</h2>
                        <p class="text-gray-600">{{ $mou->institution->name }}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            {!! $mou->status_badge !!} {!! $mou->level_badge !!}
                            @if($mou->visibility == 'public')
                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Public</span>
                            @else
                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">Internal</span>
                            @endif
                        </div>
                    </div>
                </div>
                @if($mou->description)
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-1">Deskripsi Internal</h4>
                    <p class="text-sm text-gray-600">{{ $mou->description }}</p>
                </div>
                @endif
                @if($mou->public_summary)
                <div class="mt-3 p-4 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-700 mb-1">Ringkasan Publik</h4>
                    <p class="text-sm text-blue-600">{{ $mou->public_summary }}</p>
                </div>
                @endif
            </div>

            <!-- Attachments -->
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-paperclip mr-2"></i>Lampiran</h3>
                @if($mou->main_document)
                <div class="p-3 bg-gray-50 rounded-lg flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-file-pdf text-red-500"></i>
                        <span class="text-sm">{{ basename($mou->main_document) }}</span>
                    </div>
                    <a href="{{ $mou->main_document_url }}" target="_blank" class="text-blue-600 text-sm hover:underline">Preview</a>
                </div>
                @endif
                @foreach($mou->attachments as $att)
                <div class="p-3 bg-gray-50 rounded-lg flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-file text-gray-400"></i>
                        <span class="text-sm">{{ $att->original_name }}</span>
                        <span class="text-xs text-gray-400">{{ $att->file_size_formatted }}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('admin.attachments.download', $att) }}" class="text-blue-600 text-xs"><i class="fas fa-download"></i></a>
                        <form action="{{ route('admin.attachments.destroy', $att) }}" method="POST" onsubmit="return confirm('Hapus file?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                @endforeach

                <!-- Upload Form -->
                <form action="{{ route('admin.attachments.store', $mou) }}" method="POST" enctype="multipart/form-data" class="mt-4 pt-4 border-t">
                    @csrf
                    <div class="flex items-center space-x-3">
                        <input type="file" name="files[]" multiple accept=".pdf,.doc,.docx" class="flex-1 text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:bg-gray-100 file:text-gray-700">
                        <button type="submit" class="px-3 py-2 bg-gray-800 text-white text-xs rounded-lg">Upload</button>
                    </div>
                </form>
            </div>

            <!-- Renewal History -->
            @if($mou->renewals->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-history text-purple-500 mr-2"></i>Histori Perpanjangan</h3>
                <div class="space-y-4">
                    @foreach($mou->renewals as $renewal)
                    <div class="relative pl-8 pb-4 {{ !$loop->last ? 'border-l-2 border-purple-200 ml-3' : 'ml-3' }}">
                        <div class="absolute left-0 top-0 -translate-x-1/2 w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center">
                            <span class="text-xs font-bold text-purple-600">{{ $renewal->renewal_number }}</span>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-purple-900">Version {{ $renewal->renewal_number + 1 }}</span>
                                <span class="text-xs text-gray-500">{{ $renewal->created_at->format('d M Y') }}</span>
                            </div>
                            <p class="text-xs text-gray-600 mt-1">{{ $renewal->new_start_date->format('d/m/Y') }} - {{ $renewal->new_end_date->format('d/m/Y') }}</p>
                            @if($renewal->renewal_note)
                            <p class="text-xs text-gray-500 mt-1">{{ $renewal->renewal_note }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">Oleh: {{ $renewal->renewedByAdmin?->name ?? '-' }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h4 class="font-semibold text-gray-900 mb-3">Informasi</h4>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between"><dt class="text-gray-500">No. MoU</dt><dd class="font-medium">{{ $mou->mou_number }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Tipe</dt><dd class="font-medium uppercase">{{ $mou->cooperation_type }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Jenis</dt><dd class="font-medium">{{ $mou->getTypeLabel() }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Kategori</dt><dd class="font-medium">{{ $mou->category?->name ?? '-' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Fakultas</dt><dd class="font-medium">{{ $mou->faculty?->name ?? 'Umum' }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Mulai</dt><dd class="font-medium">{{ $mou->start_date->format('d M Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Berakhir</dt><dd class="font-medium">{{ $mou->end_date->format('d M Y') }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Durasi</dt><dd class="font-medium">{{ $mou->duration_text }}</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Sisa Hari</dt><dd class="font-bold {{ $mou->remaining_days <= 30 ? 'text-red-600' : 'text-green-600' }}">{{ $mou->remaining_days }} hari</dd></div>
                    <div class="flex justify-between"><dt class="text-gray-500">Perpanjangan</dt><dd class="font-medium">{{ $mou->renewal_count }}x</dd></div>
                </dl>
            </div>

            @if($mou->pic_name)
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h4 class="font-semibold text-gray-900 mb-3">PIC</h4>
                <p class="text-sm font-medium">{{ $mou->pic_name }}</p>
                @if($mou->pic_email)<p class="text-xs text-gray-500">{{ $mou->pic_email }}</p>@endif
                @if($mou->pic_phone)<p class="text-xs text-gray-500">{{ $mou->pic_phone }}</p>@endif
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border p-5">
                <h4 class="font-semibold text-gray-900 mb-3">Metadata</h4>
                <dl class="space-y-2 text-xs text-gray-500">
                    <div><dt>Dibuat oleh</dt><dd class="font-medium text-gray-700">{{ $mou->creator?->name ?? '-' }}</dd></div>
                    <div><dt>Dibuat</dt><dd>{{ $mou->created_at->format('d M Y H:i') }}</dd></div>
                    <div><dt>Diupdate</dt><dd>{{ $mou->updated_at->format('d M Y H:i') }}</dd></div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
