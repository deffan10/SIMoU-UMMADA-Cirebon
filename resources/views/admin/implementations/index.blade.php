@extends('layouts.admin')
@section('title', 'Implementasi')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
        <p class="text-sm text-gray-500">Kelola dokumen implementasi kerjasama</p>
    </div>
    <div class="flex flex-wrap items-center gap-2 mt-2 sm:mt-0">
        <!-- Export Segmented Buttons -->
        <div class="inline-flex rounded-lg border border-gray-300 bg-white shadow-sm overflow-hidden">
            <a href="{{ route('admin.implementations.export', array_merge(request()->except('page'), ['format' => 'xlsx'])) }}" 
               class="inline-flex items-center px-3.5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 border-r border-gray-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors"
               title="Export data format Excel (.xlsx)">
                <svg class="w-4 h-4 mr-1.5 text-emerald-600 shrink-0" width="16" height="16" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('admin.implementations.export', array_merge(request()->except('page'), ['format' => 'csv'])) }}" 
               class="inline-flex items-center px-2.5 py-2 text-xs font-semibold text-gray-500 hover:text-gray-800 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors"
               title="Export data format CSV (.csv)">
                CSV
            </a>
        </div>

        <a href="{{ route('admin.implementations.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 transition-colors">
            <svg class="w-4 h-4 mr-1.5 text-white shrink-0" width="16" height="16" style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Tambah Implementasi</span>
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm border p-4 mb-4">
    <form class="flex flex-wrap gap-3" method="GET" action="{{ route('admin.implementations.index') }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul / MoU..." class="flex-1 min-w-[200px] rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 outline-none">
        <select name="visibility" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
            <option value="">Semua Visibility</option>
            <option value="public" {{ request('visibility')=='public'?'selected':'' }}>Public</option>
            <option value="private" {{ request('visibility')=='private'?'selected':'' }}>Private</option>
        </select>
        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded-lg">Filter</button>
        @if(request()->hasAny(['search', 'visibility']))
        <a href="{{ route('admin.implementations.index') }}" class="px-3 py-2 text-sm text-gray-600 hover:text-red-600">Reset</a>
        @endif
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
