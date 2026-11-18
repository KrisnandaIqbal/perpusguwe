# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

- **Utama: pegawai Kemnaker (PNS/ASN dan Non ASN).** Mencari buku, regulasi, laporan riset, dan publikasi ketenagakerjaan untuk mendukung kerja kebijakan; meminjam, memperpanjang, dan mengembalikan koleksi.
- **Utama: pustakawan.** Mengelola katalog, eksemplar, sirkulasi, keanggotaan, stock opname, laporan, dan meninjau usulan pengadaan di sisi admin.
- **Kedua: masyarakat umum.** Mencari koleksi lewat OPAC dan mendaftar sebagai anggota. Situs ini tetap untuk seluruh Indonesia; sebagian besar pengunjung datang untuk membaca di tempat (offline), sedangkan ASN mendapat jalur khusus untuk menjadi anggota.

## Product Purpose

Portal Perpustakaan Kementerian Ketenagakerjaan Republik Indonesia: OPAC publik, layanan anggota, dan sistem administrasi perpustakaan dalam satu aplikasi. Berhasil bila pegawai menemukan bahan yang mereka butuhkan (atau alternatif terdekat) dari satu kolom pencarian, dan pustakawan bisa menjalankan layanan harian tanpa alat lain.

## Positioning

- **Koleksi ketenagakerjaan.** Regulasi, SKKNI, K3, statistik, dan riset ketenagakerjaan yang tidak ada di perpustakaan umum.
- **Pencarian dan rekomendasi hybrid.** Buku yang tidak dimiliki tetap dikenali lewat metadata eksternal, lalu disarankan koleksi milik perpustakaan yang mirip (pengarang, subjek, DDC); pencarian gagal dicatat sebagai usulan pengadaan. Beranda anggota menampilkan rekomendasi personal (content-based, collaborative, popularitas). Rancangan lengkap: `BRIEF_SISTEM_REKOMENDASI_PERPUS.md`.

- **Pintu ke semua divisi.** Perpustakaan ini bertujuan menjadi gerbang ke layanan dan sumber di seluruh unit kerja Kemnaker (contoh: JDIH untuk regulasi), bukan bersaing dengan mereka.

## Operating Context

- Aplikasi CodeIgniter 4 dengan modul di `app/Modules/` (OPAC, Home, Rekomendasi, Dashboard, layanan mandiri, laporan, dan SubModule administrasi).
- Database mengikuti struktur INLISLite (hasil migrasi dari SLiMS).
- Hanya mentor yang punya akses ke server produksi; perubahan skema dan deploy diserahkan lewat berkas SQL di `artifacts/`.
- Jam layanan: Senin sampai Kamis 07.30 sampai 16.00 WIB, Jumat 07.30 sampai 16.30 WIB.

## Capabilities and Constraints

- Fitur yang ada: OPAC dan detail koleksi, pencarian dengan saran, beranda publik, masuk dan profil anggota, pendaftaran anggota, dashboard anggota dan admin, sirkulasi, pinjam dan kembali mandiri, baca di tempat, buku tamu, survei pemustaka, cetak label, kartu anggota, notifikasi email, berita, banner, FAQ.
- **Skema INLISLite tetap.** Perluas, jangan ubah struktur tabel yang ada; perubahan skema ditulis sebagai SQL untuk mentor.
- **Privasi riwayat pinjam.** Rekomendasi berbasis riwayat hanya tampil ke anggota itu sendiri dan butuh `izin_personalisasi`.
- **Koleksi berbeda dari eksemplar.** Rekomendasi di level judul; ketersediaan dan lokasi rak di level eksemplar.
- Belum diputuskan: jumlah perpanjangan untuk ASN dan Non ASN, batas waktu penggantian buku, dan ketentuan buku pengganti (menunggu konfirmasi mentor).

## Brand Commitments

- Nama resmi: Perpustakaan Kementerian Ketenagakerjaan Republik Indonesia (singkat: Perpustakaan Kemnaker RI). Pakai logo dan identitas instansi.
- Bahasa: Bahasa Indonesia layanan publik pemerintah yang lugas. Istilah teknis UI (navbar, card, hover, dan sejenisnya) tidak diterjemahkan.
- Sistem visual yang sudah ada dicatat di `DESIGN.md`.

## Evidence on Hand

- Teks "Tentang kami", kontak, dan jam layanan di helper beranda (`app/Modules/Home/Helpers/landing_helper.php`), bisa ditimpa dari database.
- Foto interior perpustakaan untuk hero (`landing_hero_photos()`).
- Contoh email layanan di `docs/email/`, laporan uji beranda di `docs/laporan-uji-landing/`.
- Belum ada testimoni, angka pengguna, atau statistik layanan yang terverifikasi; jangan dibuat-buat.

## Product Principles

1. Satu kolom pencarian menjadi pintu masuk utama, dan pencarian tidak boleh berakhir buntu: selalu ada alternatif atau jalur usulan.
2. Data pribadi anggota (riwayat pinjam) tetap milik anggota; personalisasi hanya dengan izin.
3. Kerja pustakawan diutamakan setara dengan pengalaman pencari: alat admin harus cepat dan jelas.
4. Perluas yang sudah ada (skema INLISLite, modul, sistem visual), jangan membangun ulang.

## Accessibility & Inclusion

WCAG 2.1 AA wajib: kontras teks minimal 4,5 banding 1 (3 banding 1 untuk teks besar), teks isi tidak di bawah 14 px, label tidak di bawah 11 px, focus ring terlihat. Pasangan warna yang sudah diukur ada di `DESIGN.md`.
