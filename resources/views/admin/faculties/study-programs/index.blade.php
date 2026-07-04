@extends('layouts.admin')
@section('title', 'Program Studi - ' . $faculty->name)

@section('content')
<div class="mb-6">
    <nav class="text-sm mb-2">
        <a href="{{ route('admin.faculties.index') }}" class="text-blue-600 hover:underline">Fakultas</a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-500">Program Studi ({{ $faculty->name }})</span>
    </nav>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Program Studi (Prodi)</h1>
    <p class="text-sm text-gray-500">Fakultas: <span class="font-semibold text-gray-800">{{ $faculty->name }}</span> ({{ $faculty->code ?? '-' }})</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- List of Study Programs -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="p-5 border-b bg-gray-50">
                <h3 class="font-semibold text-gray-900">Daftar Program Studi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">Nama Prodi</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600">Kode</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600">Jenjang</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600">Status</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($studyPrograms as $program)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">
                                {{ $program->name }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">
                                {{ $program->code ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-gray-700">
                                {{ $program->level }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($program->is_active)
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.study-programs.edit', $program) }}" class="p-1 text-yellow-600 hover:bg-yellow-50 rounded" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.study-programs.destroy', $program) }}" method="POST" onsubmit="return confirm('Hapus program studi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-red-600 hover:bg-red-50 rounded" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-400">
                                Belum ada data program studi untuk fakultas ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Add Form -->
    <div>
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h3 class="font-semibold text-gray-900 mb-4 pb-2 border-b">Tambah Prodi Baru</h3>
            <form action="{{ route('admin.faculties.study-programs.store', $faculty) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Program Studi *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Teknik Informatika" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Prodi</label>
                    <input type="text" name="code" value="{{ old('code') }}" placeholder="Contoh: TIF" class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenjang Pendidikan *</label>
                    <select name="level" required class="w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="S1" {{ old('level')=='S1'?'selected':'' }}>S1 - Sarjana</option>
                        <option value="D3" {{ old('level')=='D3'?'selected':'' }}>D3 - Diploma 3</option>
                        <option value="D4" {{ old('level')=='D4'?'selected':'' }}>D4 - Diploma 4</option>
                        <option value="S2" {{ old('level')=='S2'?'selected':'' }}>S2 - Magister</option>
                        <option value="S3" {{ old('level')=='S3'?'selected':'' }}>S3 - Doktor</option>
                    </select>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-sm transition">
                        <i class="fas fa-plus mr-1"></i> Tambah Prodi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
