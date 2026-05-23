@extends('layouts.admin')
@section('title', 'Perpanjang MoU')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="flex items-center space-x-3 mb-4">
            <img src="{{ $mou->institution->logo_url }}" class="w-10 h-10 rounded-lg object-cover">
            <div>
                <h3 class="font-semibold text-gray-900">{{ $mou->title }}</h3>
                <p class="text-sm text-gray-500">{{ $mou->institution->name }} &bull; {{ $mou->mou_number }}</p>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4 p-3 bg-gray-50 rounded-lg text-sm">
            <div><span class="text-gray-500">Periode Saat Ini:</span><br><strong>{{ $mou->start_date->format('d/m/Y') }} - {{ $mou->end_date->format('d/m/Y') }}</strong></div>
            <div><span class="text-gray-500">Durasi:</span><br><strong>{{ $mou->duration_text }}</strong></div>
            <div><span class="text-gray-500">Perpanjangan ke:</span><br><strong>{{ $mou->renewal_count + 1 }}</strong></div>
        </div>
    </div>

    <form action="{{ route('admin.renewal.store', $mou) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900"><i class="fas fa-sync text-purple-500 mr-2"></i>Data Perpanjangan Baru</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Baru *</label>
                    <input type="date" name="new_start_date" value="{{ old('new_start_date', $mou->end_date->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Baru *</label>
                    <input type="date" name="new_end_date" value="{{ old('new_end_date', $mou->end_date->addYears(2)->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Perpanjangan</label>
                <textarea name="renewal_note" rows="3" class="w-full rounded-lg border-gray-300 text-sm" placeholder="Catatan mengenai perpanjangan ini...">{{ old('renewal_note') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload File MoU Baru</label>
                <input type="file" name="new_file" accept=".pdf,.doc,.docx" class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700">
            </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <a href="{{ route('admin.mou.show', $mou) }}" class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700">
                <i class="fas fa-sync mr-1"></i> Perpanjang MoU
            </button>
        </div>
    </form>
</div>
@endsection
