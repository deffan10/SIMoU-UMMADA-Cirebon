@extends('layouts.admin')
@section('title', 'Institusi')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-500">Kelola data institusi partner</p>
    <a href="{{ route('admin.institutions.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700"><i class="fas fa-plus mr-1"></i> Tambah</a>
</div>

<div class="bg-white rounded-xl shadow-sm border p-4 mb-4">
    <form class="flex gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari institusi..." class="flex-1 rounded-lg border-gray-300 text-sm">
        <select name="type" class="rounded-lg border-gray-300 text-sm">
            <option value="">Semua Tipe</option>
            @foreach(['universitas','pemerintah','industri','sekolah','ngo','organisasi','lainnya'] as $t)
            <option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Cari</button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left">Institusi</th>
                <th class="px-4 py-3 text-left">Tipe</th>
                <th class="px-4 py-3 text-left">Lokasi</th>
                <th class="px-4 py-3 text-center">MoU</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($institutions as $inst)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $inst->logo_url }}" class="w-9 h-9 rounded-lg object-cover">
                        <span class="font-medium text-gray-900">{{ $inst->name }}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-gray-600 capitalize">{{ $inst->type }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $inst->city }}, {{ $inst->country }}</td>
                <td class="px-4 py-3 text-center font-semibold">{{ $inst->mous_count }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('admin.institutions.edit', $inst) }}" class="text-yellow-600 hover:text-yellow-700 mr-2"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.institutions.destroy', $inst) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-8 text-gray-400">Belum ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $institutions->links() }}</div>
</div>
@endsection
