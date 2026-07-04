@extends('layouts.admin')
@section('title', 'Fakultas')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-500">Kelola data fakultas & program studi</p>
    <a href="{{ route('admin.faculties.create') }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg"><i class="fas fa-plus mr-1"></i> Tambah</a>
</div>
<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b"><tr><th class="px-4 py-3 text-left">Fakultas</th><th class="px-4 py-3 text-center">Kode</th><th class="px-4 py-3 text-center">Prodi</th><th class="px-4 py-3 text-center">MoU</th><th class="px-4 py-3 text-center">Aksi</th></tr></thead>
        <tbody class="divide-y">
            @foreach($faculties as $f)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium">{{ $f->name }}</td>
                <td class="px-4 py-3 text-center text-gray-500">{{ $f->code ?? '-' }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('admin.faculties.study-programs.index', $f) }}" class="text-blue-600 hover:underline font-semibold">
                        {{ $f->study_programs_count }}
                    </a>
                </td>
                <td class="px-4 py-3 text-center font-semibold">{{ $f->mous_count }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('admin.faculties.study-programs.index', $f) }}" class="text-blue-600 mr-2" title="Kelola Prodi"><i class="fas fa-list"></i></a>
                    <a href="{{ route('admin.faculties.edit', $f) }}" class="text-yellow-600 mr-2" title="Edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.faculties.destroy', $f) }}" method="POST" class="inline" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="text-red-600" title="Hapus"><i class="fas fa-trash"></i></button></form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
