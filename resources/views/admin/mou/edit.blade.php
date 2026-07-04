@extends('layouts.admin')
@section('title', 'Edit MoU')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.mou.update', $mou) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf @method('PUT')

        <!-- Institusi -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-building text-blue-500 mr-2"></i>Institusi Partner</h3>
            <select name="institution_id" required class="w-full rounded-lg border-gray-300 text-sm">
                @foreach($institutions as $inst)
                <option value="{{ $inst->id }}" {{ $mou->institution_id == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Data Kerjasama -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-file-contract text-green-500 mr-2"></i>Data Kerjasama</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor MoU *</label>
                    <input type="text" name="mou_number" value="{{ old('mou_number', $mou->mou_number) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $mou->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul *</label>
                    <input type="text" name="title" value="{{ old('title', $mou->title) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat</label>
                    <select name="level" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="lokal" {{ $mou->level=='lokal'?'selected':'' }}>Lokal</option>
                        <option value="nasional" {{ $mou->level=='nasional'?'selected':'' }}>Nasional</option>
                        <option value="internasional" {{ $mou->level=='internasional'?'selected':'' }}>Internasional</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="type" required class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach(['akademik','penelitian','mbkm','industri','pengabdian','pemerintah','internasional'] as $t)
                        <option value="{{ $t }}" {{ $mou->type==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Dokumen</label>
                    <select name="cooperation_type" required class="w-full rounded-lg border-gray-300 text-sm">
                        @foreach(['mou'=>'MoU','moa'=>'MoA','lainnya'=>'Lainnya'] as $k=>$v)
                        <option value="{{ $k }}" {{ $mou->cooperation_type==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                    <select name="faculty_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Universitas</option>
                        @foreach($faculties as $f)
                        <option value="{{ $f->id }}" {{ $mou->faculty_id==$f->id?'selected':'' }}>{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                    <input type="text" name="study_program" value="{{ old('study_program', $mou->study_program) }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
        </div>

        <!-- PIC -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-user text-purple-500 mr-2"></i>PIC</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="pic_name" value="{{ old('pic_name', $mou->pic_name) }}" placeholder="Nama PIC" class="rounded-lg border-gray-300 text-sm">
                <input type="text" name="pic_phone" value="{{ old('pic_phone', $mou->pic_phone) }}" placeholder="Telepon" class="rounded-lg border-gray-300 text-sm">
                <input type="email" name="pic_email" value="{{ old('pic_email', $mou->pic_email) }}" placeholder="Email" class="rounded-lg border-gray-300 text-sm">
            </div>
        </div>

        <!-- Periode -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-calendar text-orange-500 mr-2"></i>Periode</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $mou->start_date->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $mou->end_date->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
        </div>

        <!-- Visibility -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-eye text-indigo-500 mr-2"></i>Visibility & Deskripsi</h3>
            <div class="space-y-4">
                <textarea name="description" rows="3" placeholder="Deskripsi internal" class="w-full rounded-lg border-gray-300 text-sm">{{ old('description', $mou->description) }}</textarea>
                <textarea name="public_summary" rows="3" placeholder="Ringkasan publik" class="w-full rounded-lg border-gray-300 text-sm">{{ old('public_summary', $mou->public_summary) }}</textarea>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <select name="visibility" class="rounded-lg border-gray-300 text-sm">
                        <option value="internal" {{ $mou->visibility=='internal'?'selected':'' }}>Internal</option>
                        <option value="public" {{ $mou->visibility=='public'?'selected':'' }}>Public</option>
                    </select>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_pdf_public" value="1" {{ $mou->show_pdf_public?'checked':'' }} class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm">PDF publik</span>
                    </label>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="allow_download" value="1" {{ $mou->allow_download?'checked':'' }} class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm">Allow download</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- File -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-upload text-cyan-500 mr-2"></i>Dokumen</h3>
            @if($mou->main_document)
            <p class="text-sm text-gray-500 mb-2">File saat ini: <span class="font-medium">{{ basename($mou->main_document) }}</span></p>
            @endif
            <input type="file" name="main_document" accept=".pdf,.doc,.docx" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700">
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin mengganti file.</p>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.mou.show', $mou) }}" class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Update MoU
            </button>
        </div>
    </form>
</div>
@endsection
