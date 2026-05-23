@extends('layouts.admin')
@section('title', 'Preview Import')

@section('content')
<div class="max-w-5xl">
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <h3 class="font-semibold text-gray-900 mb-2">Preview Data Import</h3>
        <p class="text-sm text-gray-600">{{ count($data) }} baris data ditemukan. Periksa data sebelum melanjutkan import.</p>
    </div>

    @if(count($data) > 0)
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-3 py-2 text-left">#</th>
                        <th class="px-3 py-2 text-left">No. MoU</th>
                        <th class="px-3 py-2 text-left">Judul</th>
                        <th class="px-3 py-2 text-left">Lembaga</th>
                        <th class="px-3 py-2 text-left">Kategori</th>
                        <th class="px-3 py-2 text-left">Mulai</th>
                        <th class="px-3 py-2 text-left">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach(array_slice($data, 0, 20) as $i => $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                        <td class="px-3 py-2">{{ $row['nomor_mou'] ?? '-' }}</td>
                        <td class="px-3 py-2">{{ Str::limit($row['judul'] ?? '-', 30) }}</td>
                        <td class="px-3 py-2">{{ $row['nama_lembaga'] ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $row['kategori'] ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $row['tanggal_mulai'] ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $row['tanggal_selesai'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <form action="{{ route('admin.import.process') }}" method="POST" class="flex items-center space-x-3">
        @csrf
        <input type="hidden" name="import_log_id" value="{{ $importLog->id }}">
        <a href="{{ route('admin.import.index') }}" class="px-4 py-2 text-sm border rounded-lg">Batal</a>
        <button type="submit" class="px-6 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700" onclick="return confirm('Proses import {{ count($data) }} data?')">
            <i class="fas fa-check mr-1"></i> Proses Import ({{ count($data) }} baris)
        </button>
    </form>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
        <p class="text-yellow-700">Tidak ada data yang dapat dibaca dari file. Pastikan format sesuai template.</p>
        <a href="{{ route('admin.import.index') }}" class="mt-3 inline-block text-sm text-blue-600">Kembali</a>
    </div>
    @endif
</div>
@endsection
