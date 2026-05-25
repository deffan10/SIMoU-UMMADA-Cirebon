@extends('layouts.admin')
@section('title', 'Implementasi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <p class="text-sm text-gray-500">Kelola dokumen implementasi kerjasama</p>
    <a href="{{ route('admin.implementations.create') }}" class="mt-2 sm:mt-0 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
        <i class="fas fa-plus mr-1"></i> Tambah Implementasi
    </a>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-4">
    <form class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / MoU..." class="flex-1 min-w-[200px] rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 outline-none">
        <select name="visibility" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Visibility</option>
            <option value="public" {{ request('visibility')=='public'?'selected':'' }}>Public</option>
            <option value="private" {{ request('visibility')=='private'?'selected':'' }}>Private</option>
        </select>
        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Filter</button>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">Implementasi</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">MoU</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-600">File</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-600">Visibility</th>
                    <th class="px-4 py-3 text-center font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($implementations as $impl)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <p class="font-medium text-gray-900">{{ $impl->title }}</p>
                        @if($impl->description)
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $impl->description }}</p>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-gray-700 text-xs">{{ Str::limit($impl->mou->title, 40) }}</p>
                        <p class="text-xs text-gray-400">{{ $impl->mou->mou_number }}</p>
                    </td>
                    <td class="px-4 py-3">
                        @if($impl->file_path)
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-file-pdf text-red-500"></i>
                            <span class="text-xs text-gray-500">{{ $impl->file_size_formatted }}</span>
                        </div>
                        @else
                        <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($impl->visibility == 'public')
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Public</span>
                        @else
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-700">Private</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-1">
                            @if($impl->file_path)
                            <a href="{{ route('admin.implementations.download', $impl) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="Download"><i class="fas fa-download"></i></a>
                            @endif
                            <a href="{{ route('admin.implementations.edit', $impl) }}" class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.implementations.destroy', $impl) }}" method="POST" onsubmit="return confirm('Hapus implementasi ini?')">
                                @csrf @method('DELETE')
                                <button class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-gray-400">Belum ada data implementasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t">{{ $implementations->links() }}</div>
</div>
@endsection
