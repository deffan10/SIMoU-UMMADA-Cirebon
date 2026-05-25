@extends('layouts.admin')
@section('title', 'Tambah Implementasi')

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.implementations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Pilih MoU (Autocomplete) -->
        <div class="bg-white rounded-xl shadow-sm border p-6" x-data="mouSearch()">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-file-contract text-blue-500 mr-2"></i>Pilih MoU</h3>
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.300ms="fetchMous()" @focus="showDropdown = true" placeholder="Ketik judul atau nomor MoU..."
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                <input type="hidden" name="mou_id" :value="selectedId">

                <!-- Selected indicator -->
                <div x-show="selectedTitle" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded-lg flex items-center justify-between">
                    <span class="text-sm text-blue-700" x-text="selectedTitle"></span>
                    <button type="button" @click="clear()" class="text-blue-500 hover:text-blue-700"><i class="fas fa-times"></i></button>
                </div>

                <!-- Dropdown -->
                <div x-show="showDropdown && results.length > 0" x-cloak @click.away="showDropdown = false"
                    class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <template x-for="mou in results" :key="mou.id">
                        <button type="button" @click="select(mou)" class="w-full text-left px-4 py-2.5 hover:bg-blue-50 border-b border-gray-100 last:border-0">
                            <p class="text-sm font-medium text-gray-900" x-text="mou.title"></p>
                            <p class="text-xs text-gray-500"><span x-text="mou.mou_number"></span> &bull; <span x-text="mou.institution?.name || ''"></span></p>
                        </button>
                    </template>
                </div>
            </div>
            @error('mou_id')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Detail Implementasi -->
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4"><i class="fas fa-clipboard-check text-green-500 mr-2"></i>Detail Implementasi</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Implementasi *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        placeholder="Contoh: Laporan Pelaksanaan Magang Batch 1">
                    @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                        placeholder="Deskripsi singkat implementasi...">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File PDF *</label>
                    <input type="file" name="file" accept=".pdf" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-400 mt-1">Format PDF, max 20MB</p>
                    @error('file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visibility *</label>
                    <select name="visibility" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 outline-none">
                        <option value="private" {{ old('visibility')=='private'?'selected':'' }}>Private (hanya admin)</option>
                        <option value="public" {{ old('visibility')=='public'?'selected':'' }}>Public (tampil di website)</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Jika public, file PDF akan bisa diakses dari halaman detail kerjasama.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('admin.implementations.index') }}" class="px-4 py-2 text-sm border rounded-lg hover:bg-gray-50">Batal</a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-1"></i> Simpan Implementasi
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function mouSearch() {
    return {
        search: '',
        results: [],
        selectedId: '',
        selectedTitle: '',
        showDropdown: false,
        async fetchMous() {
            if (this.search.length < 2) { this.results = []; return; }
            const res = await fetch('{{ route("admin.implementations.search-mou") }}?q=' + encodeURIComponent(this.search));
            this.results = await res.json();
            this.showDropdown = true;
        },
        select(mou) {
            this.selectedId = mou.id;
            this.selectedTitle = mou.title + ' (' + mou.mou_number + ')';
            this.search = '';
            this.results = [];
            this.showDropdown = false;
        },
        clear() {
            this.selectedId = '';
            this.selectedTitle = '';
            this.search = '';
        }
    }
}
</script>
@endpush
