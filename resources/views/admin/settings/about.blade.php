@extends('layouts.admin')
@section('title', 'Edit Halaman Tentang')

@section('content')
<div class="max-w-4xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500">Edit konten halaman "Tentang" yang tampil di website publik.</p>
        </div>
        <a href="{{ route('tentang') }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-700">
            <i class="fas fa-external-link-alt mr-1"></i> Lihat Halaman
        </a>
    </div>

    <form action="{{ route('admin.settings.about.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm border p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-gray-900"><i class="fas fa-file-alt text-blue-500 mr-2"></i>Konten Halaman Tentang</h3>
                <div class="flex items-center space-x-2 text-xs text-gray-400">
                    <span class="px-2 py-1 bg-gray-100 rounded">HTML didukung</span>
                </div>
            </div>

            <!-- Toolbar hint -->
            <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-700"><i class="fas fa-info-circle mr-1"></i> Anda bisa menggunakan tag HTML berikut:</p>
                <p class="text-xs text-blue-600 mt-1 font-mono">&lt;h2&gt;, &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;a&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;br&gt;, &lt;hr&gt;, &lt;blockquote&gt;, &lt;img&gt;, &lt;table&gt;</p>
            </div>

            <!-- Editor -->
            <div>
                <textarea
                    name="about_content"
                    id="about_editor"
                    rows="20"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm font-mono focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none"
                    placeholder="Masukkan konten halaman tentang..."
                >{{ old('about_content', $aboutContent) }}</textarea>
                @error('about_content')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-400 mt-1">Konten akan ditampilkan dengan styling prose (typography). Max 50.000 karakter.</p>
            </div>

            <!-- Preview Toggle -->
            <div x-data="{ showPreview: false }">
                <button type="button" @click="showPreview = !showPreview" class="text-sm text-blue-600 hover:text-blue-700">
                    <i class="fas" :class="showPreview ? 'fa-eye-slash' : 'fa-eye'"></i>
                    <span x-text="showPreview ? 'Sembunyikan Preview' : 'Tampilkan Preview'"></span>
                </button>

                <div x-show="showPreview" x-cloak class="mt-3 p-4 bg-gray-50 border rounded-lg">
                    <p class="text-xs text-gray-500 mb-2 font-medium">Preview:</p>
                    <div class="prose prose-sm max-w-none" id="preview-area"></div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('admin.settings') }}" class="text-sm text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Pengaturan
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 shadow-sm">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('about_editor');
    const preview = document.getElementById('preview-area');

    function updatePreview() {
        if (preview) {
            preview.innerHTML = textarea.value;
        }
    }

    textarea.addEventListener('input', updatePreview);
    updatePreview();
});
</script>
@endpush
