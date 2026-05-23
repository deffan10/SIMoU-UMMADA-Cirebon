@extends('layouts.admin')
@section('title', 'MoU Terhapus')

@section('content')
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.mou.index') }}" class="text-sm text-gray-600 hover:text-blue-600"><i class="fas fa-arrow-left mr-1"></i> Kembali</a>
</div>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left">MoU</th>
                <th class="px-4 py-3 text-left">Institusi</th>
                <th class="px-4 py-3 text-left">Dihapus</th>
                <th class="px-4 py-3 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($mous as $mou)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3">
                    <p class="font-medium">{{ $mou->title }}</p>
                    <p class="text-xs text-gray-500">{{ $mou->mou_number }}</p>
                </td>
                <td class="px-4 py-3 text-gray-600">{{ $mou->institution?->name }}</td>
                <td class="px-4 py-3 text-xs text-gray-500">{{ $mou->deleted_at->diffForHumans() }}</td>
                <td class="px-4 py-3 text-center">
                    <form action="{{ route('admin.mou.restore', $mou->id) }}" method="POST" class="inline">
                        @csrf
                        <button class="px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">Restore</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center py-8 text-gray-400">Tidak ada data terhapus.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4">{{ $mous->links() }}</div>
</div>
@endsection
