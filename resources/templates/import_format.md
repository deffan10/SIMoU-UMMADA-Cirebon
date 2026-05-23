# Format Template Import MoU

## Kolom Excel (.xlsx)

| No | Kolom | Contoh | Wajib |
|----|-------|--------|-------|
| 1 | nomor_mou | MOU/UMMADA/001/2024 | Ya |
| 2 | judul | Kerjasama Tri Dharma | Ya |
| 3 | nama_lembaga | Universitas Indonesia | Ya |
| 4 | kategori | Pendidikan & Pengajaran | Tidak |
| 5 | tanggal_mulai | 2024-01-01 | Ya |
| 6 | tanggal_selesai | 2026-01-01 | Ya |
| 7 | status | aktif | Tidak (default: aktif) |
| 8 | fakultas | Fakultas Teknik | Tidak |
| 9 | jenis_kerjasama | akademik | Tidak (default: akademik) |
| 10 | tingkat | nasional | Tidak (default: nasional) |
| 11 | visibility | public | Tidak (default: internal) |
| 12 | deskripsi | Kerjasama bidang... | Tidak |

## Catatan Penting

1. Format tanggal: YYYY-MM-DD (contoh: 2024-01-15)
2. Nilai status: aktif, akan_expire, expire
3. Nilai jenis: akademik, penelitian, mbkm, industri, pengabdian, pemerintah, internasional
4. Nilai tingkat: lokal, nasional, internasional
5. Nilai visibility: public, internal
6. Jika institusi belum ada di database, akan otomatis dibuat baru
7. Jika kategori/fakultas belum ada, akan otomatis dibuat baru
8. Nomor MoU yang duplikat akan di-skip (tidak diimport ulang)
