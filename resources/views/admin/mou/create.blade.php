@extends('layouts.admin')
@section('title', 'Tambah MoU')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('admin.mou.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Institusi -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-building text-blue-500 mr-2"></i>Institusi Partner</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Institusi *</label>
                    <select name="institution_id" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Pilih Institusi</option>
                        @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }} ({{ $inst->getTypeLabel() }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Jika belum ada, <a href="{{ route('admin.institutions.create') }}" class="text-blue-600">tambah institusi baru</a>.</p>
                </div>
            </div>
        </div>

        <!-- Data Kerjasama -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-file-contract text-green-500 mr-2"></i>Data Kerjasama</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor MoU *</label>
                    <input type="text" name="mou_number" value="{{ old('mou_number') }}" required class="w-full rounded-lg border-gray-300 text-sm" placeholder="MOU/UMMADA/001/2024">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kerjasama *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat *</label>
                    <select name="level" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="lokal" {{ old('level')=='lokal'?'selected':'' }}>Lokal</option>
                        <option value="nasional" {{ old('level','nasional')=='nasional'?'selected':'' }}>Nasional</option>
                        <option value="internasional" {{ old('level')=='internasional'?'selected':'' }}>Internasional</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kerjasama *</label>
                    <select name="type" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="akademik" {{ old('type')=='akademik'?'selected':'' }}>Akademik</option>
                        <option value="penelitian" {{ old('type')=='penelitian'?'selected':'' }}>Penelitian</option>
                        <option value="mbkm" {{ old('type')=='mbkm'?'selected':'' }}>MBKM</option>
                        <option value="industri" {{ old('type')=='industri'?'selected':'' }}>Industri</option>
                        <option value="pengabdian" {{ old('type')=='pengabdian'?'selected':'' }}>Pengabdian</option>
                        <option value="pemerintah" {{ old('type')=='pemerintah'?'selected':'' }}>Pemerintah</option>
                        <option value="internasional" {{ old('type')=='internasional'?'selected':'' }}>Internasional</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Dokumen *</label>
                    <select name="cooperation_type" required class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="mou" {{ old('cooperation_type')=='mou'?'selected':'' }}>MoU</option>
                        <option value="moa" {{ old('cooperation_type')=='moa'?'selected':'' }}>MoA</option>
                        <option value="lainnya" {{ old('cooperation_type')=='lainnya'?'selected':'' }}>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fakultas</label>
                    <select name="faculty_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Universitas</option>
                        @foreach($faculties as $f)
                        <option value="{{ $f->id }}" {{ old('faculty_id') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Program Studi</label>
                    <input type="text" name="study_program" value="{{ old('study_program') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
        </div>

        <!-- PIC -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-user text-purple-500 mr-2"></i>PIC (Person In Charge)</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama PIC</label>
                    <input type="text" name="pic_name" value="{{ old('pic_name') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon PIC</label>
                    <input type="text" name="pic_phone" value="{{ old('pic_phone') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email PIC</label>
                    <input type="email" name="pic_email" value="{{ old('pic_email') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
        </div>

        <!-- Periode -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-calendar text-orange-500 mr-2"></i>Masa Kerjasama</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>
        </div>

        <!-- Deskripsi & Visibility -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-align-left text-indigo-500 mr-2"></i>Deskripsi & Visibility</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Internal</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 text-sm">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ringkasan Publik</label>
                    <textarea name="public_summary" rows="3" class="w-full rounded-lg border-gray-300 text-sm">{{ old('public_summary') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Visibility *</label>
                        <select name="visibility" required class="w-full rounded-lg border-gray-300 text-sm">
                            <option value="internal" {{ old('visibility')=='internal'?'selected':'' }}>Internal</option>
                            <option value="public" {{ old('visibility')=='public'?'selected':'' }}>Public</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="show_pdf_public" value="1" class="rounded border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Tampilkan PDF di publik</span>
                        </label>
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="allow_download" value="1" class="rounded border-gray-300 text-blue-600">
                            <span class="text-sm text-gray-700">Izinkan download</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- File Upload -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-upload text-cyan-500 mr-2"></i>Dokumen MoU</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File MoU (PDF/DOC/DOCX, max 20MB)</label>
                <input type="file" name="main_document" accept=".pdf,.doc,.docx" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.mou.index') }}" class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Simpan MoU
            </button>
        </div>
    </form>
</div>
@endsection
