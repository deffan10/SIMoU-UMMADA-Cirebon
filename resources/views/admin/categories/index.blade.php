@extends('layouts.admin')
@section('title', 'Kategori')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-500">Kelola kategori kerjasama</p>
    <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus mr-1"></i> Tambah</a>
</div>
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr><th class="px-4 py-3 text-left">Kategori</th><th class="px-4 py-3 text-center">Warna</th><th class="px-4 py-3 text-center">MoU</th><th class="px-4 py-3 text-center">Aksi</th></tr></thead>
        <tbody class="divide-y">
            @foreach($categories as $cat)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $cat->name }}</td>
                <td class="px-4 py-3 text-center"><span class="w-6 h-6 inline-block rounded-full" style="background:{{ $cat->color }}"></span></td>
                <td class="px-4 py-3 text-center font-semibold">{{ $cat->mous_count }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('admin.categories.edit', $cat) }}" class="text-yellow-600 mr-2"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
