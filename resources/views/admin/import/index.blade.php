@extends('layouts.admin')
@section('title', 'Import Data')

@section('content')
<div class="max-w-4xl">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-file-import text-blue-500 mr-2"></i>Import dari Excel</h3>
            <p class="text-sm text-gray-600 mb-4">Upload file Excel (.xlsx) berisi data kerjasama lama untuk diimport ke sistem.</p>
            <form action="{{ route('admin.import.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <input type="file" name="file" accept=".xlsx,.xls" required class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
                <button type="submit" class="w-full py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    <i class="fas fa-upload mr-1"></i> Upload & Preview
                </button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="font-semibold text-gray-900 mb-4"><i class="fas fa-download text-green-500 mr-2"></i>Template Import</h3>
            <p class="text-sm text-gray-600 mb-4">Download template Excel untuk format import yang benar.</p>
            <a href="{{ route('admin.import.template') }}" class="inline-block w-full text-center py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                <i class="fas fa-file-excel mr-1"></i> Download Template
            </a>
            <div class="mt-4 p-3 bg-gray-50 rounded-lg text-xs text-gray-600">
                <p class="font-medium mb-1">Kolom yang diperlukan:</p>
                <p>nomor_mou, judul, nama_lembaga, kategori, tanggal_mulai, tanggal_selesai, status, fakultas, jenis_kerjasama, visibility, deskripsi</p>
            </div>
        </div>
    </div>

    @if($recentImports->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Riwayat Import</h3>
        <div class="space-y-3">
            @foreach($recentImports as $log)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium">{{ $log->file_name }}</p>
                    <p class="text-xs text-gray-500">{{ $log->created_at->format('d M Y H:i') }} &bull; {{ $log->admin?->name }}</p>
                </div>
                <div class="text-right text-xs">
                    <span class="px-2 py-1 rounded-full {{ $log->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($log->status) }}</span>
                    @if($log->summary)
                    <p class="text-gray-500 mt-1">{{ $log->success_count }} berhasil, {{ $log->failed_count }} gagal</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
