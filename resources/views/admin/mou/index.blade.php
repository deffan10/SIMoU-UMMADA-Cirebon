@extends('layouts.admin')
@section('title', 'Data MoU')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500">Kelola seluruh data MoU/Kerjasama</p>
    </div>
    <div class="flex space-x-2 mt-2 sm:mt-0">
        <a href="{{ route('admin.mou.trashed') }}" class="px-3 py-2 text-sm border rounded-lg hover:bg-gray-50">
            <i class="fas fa-trash-alt mr-1"></i> Trash
        </a>
        <a href="{{ route('admin.mou.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-1"></i> Tambah MoU
        </a>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-4">
    <form action="{{ route('admin.mou.index') }}" method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="rounded-lg border-gray-300 text-sm flex-1 min-w-[150px]">
        <select name="status" class="rounded-lg border-gray-300 text-sm">
            <option value="">Status</option>
            <option value="aktif" {{ request('status')=='aktif'?'selected':'' }}>Aktif</option>
            <option value="akan_expire" {{ request('status')=='akan_expire'?'selected':'' }}>Akan Expire</option>
            <option value="expire" {{ request('status')=='expire'?'selected':'' }}>Expire</option>
        </select>
        <select name="cooperation_type" class="rounded-lg border-gray-300 text-sm">
            <option value="">Tipe Dokumen</option>
            <option value="mou" {{ request('cooperation_type')=='mou'?'selected':'' }}>MoU</option>
            <option value="moa" {{ request('cooperation_type')=='moa'?'selected':'' }}>MoA</option>
            <option value="lainnya" {{ request('cooperation_type')=='lainnya'?'selected':'' }}>Lainnya</option>
        </select>
        <select name="has_implementation" class="rounded-lg border-gray-300 text-sm">
            <option value="">Status Implementasi</option>
            <option value="yes" {{ request('has_implementation')=='yes'?'selected':'' }}>Memiliki Implementasi</option>
            <option value="no" {{ request('has_implementation')=='no'?'selected':'' }}>Belum Ada Implementasi</option>
        </select>
        <select name="category_id" class="rounded-lg border-gray-300 text-sm">
            <option value="">Kategori</option>
            @foreach($categories as $c)
            <option value="{{ $c->id }}" {{ request('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="visibility" class="rounded-lg border-gray-300 text-sm">
            <option value="">Visibility</option>
            <option value="public" {{ request('visibility')=='public'?'selected':'' }}>Public</option>
            <option value="internal" {{ request('visibility')=='internal'?'selected':'' }}>Internal</option>
        </select>
        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Filter</button>
        @if(request()->hasAny(['search','status','cooperation_type','has_implementation','category_id','visibility']))
        <a href="{{ route('admin.mou.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-red-600">Reset</a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">MoU</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Institusi</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Periode</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Visibility</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($mous as $mou)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900 line-clamp-1">{{ $mou->title }}</p>
                        <p class="text-xs text-gray-500">{{ $mou->mou_number }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-2">
                            <img src="{{ $mou->institution->logo_url }}" class="w-7 h-7 rounded-full object-cover">
                            <span class="text-gray-700 text-xs">{{ Str::limit($mou->institution->name, 25) }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $mou->start_date->format('d/m/Y') }}<br>{{ $mou->end_date->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3">{!! $mou->status_badge !!}</td>
                    <td class="px-4 py-3">
                        @if($mou->visibility == 'public')
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">Public</span>
                        @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">Internal</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-1">
                            <a href="{{ route('admin.mou.show', $mou) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Detail"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('admin.mou.edit', $mou) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.mou.destroy', $mou) }}" method="POST" onsubmit="return confirm('Hapus MoU ini?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Belum ada data MoU.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $mous->links() }}</div>
</div>
@endsection
