@extends('layouts.public')
@section('title', 'Tentang')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <h1 class="text-3xl font-bold text-gray-900">Tentang SIMoU</h1>
        <p class="text-gray-600 mt-2">Sistem Informasi Repository MoU/Kerjasama</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border p-8 prose max-w-none">
        <h2>Universitas Muhammadiyah Ahmad Dahlan Cirebon</h2>
        <p>SIMoU (Sistem Informasi Memorandum of Understanding) adalah platform digital yang digunakan untuk mengelola, mendokumentasikan, dan mempublikasikan seluruh kerjasama yang dilakukan oleh UMMADA Cirebon dengan berbagai institusi partner.</p>

        <h3>Tujuan Sistem</h3>
        <ul>
            <li>Menyediakan repository digital untuk seluruh dokumen MoU/kerjasama</li>
            <li>Monitoring masa berlaku dan status kerjasama secara real-time</li>
            <li>Mempublikasikan informasi kerjasama kepada publik</li>
            <li>Menyediakan reminder otomatis untuk perpanjangan kerjasama</li>
            <li>Memudahkan tracking histori perpanjangan kerjasama</li>
            <li>Menampilkan statistik dan visualisasi data kerjasama</li>
        </ul>

        <h3>Fitur Utama</h3>
        <ul>
            <li><strong>Public Portal</strong> - Halaman publik untuk menampilkan informasi kerjasama</li>
            <li><strong>Admin Panel</strong> - Panel administrasi untuk pengelolaan data kerjasama</li>
            <li><strong>Sistem Renewal</strong> - Perpanjangan MoU dengan histori versi lengkap</li>
            <li><strong>Import Data</strong> - Migrasi arsip kerjasama lama dari file Excel</li>
            <li><strong>Statistik</strong> - Visualisasi data kerjasama dalam bentuk grafik</li>
            <li><strong>PDF Viewer</strong> - Preview dokumen langsung di browser</li>
        </ul>

        <h3>Kontak</h3>
        <p>Untuk informasi lebih lanjut mengenai kerjasama dengan UMMADA Cirebon, silakan hubungi:</p>
        <ul>
            <li>Email: kerjasama@ummada.ac.id</li>
            <li>Website: ummada.ac.id</li>
        </ul>
    </div>
</div>
@endsection
