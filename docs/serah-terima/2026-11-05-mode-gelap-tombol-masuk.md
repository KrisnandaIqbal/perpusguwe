# Serah terima: tombol Masuk, mode gelap, dan kurasi antislop (2026-11-05)

Dokumen ini untuk sesi baru yang melanjutkan pekerjaan. Bacalah dulu bagian 1 sampai 3, lalu kerjakan bagian 6.

## 1. Ringkasan satu paragraf

Pada halaman depan (`/`) sudah ditambahkan tombol "Masuk" (menuju `/login`) dan tombol mode gelap di sebelah kirinya, lengkap dengan tampilan gelap seluruh halaman. Setelah itu seluruh hasilnya dikurasi dengan skill antislop (Mode 2, semua nomor disetujui pemilik karena sedang tidak di tempat), dan semua temuan yang berada dalam lingkup sudah diperbaiki. Hasil akhir: 25 dari 25 tes Playwright lulus. **Semua perubahan ini BELUM di-commit dan BELUM di-push.**

## 2. Keadaan repositori

- Cabang `main` lokal di commit `05e1a7a` ("halaman depan baru sesuai desain Figma"). `origin/main` dan `fork/main` juga di `05e1a7a`.
- **`kemnaker/main` sudah maju ke `c5be0d9`** (PR #29 rekan: "rapikan pemetaan subjek koleksi dan buat subjek serta judul koleksi bisa diklik"; menyentuh `Home.php`, controller dan tampilan OPAC, `layout/opac/layout.php`, dan `docs/draft_pemetaan_subjek_koleksi.md`). Tidak ada berkas yang saya ubah ikut tersentuh. Sebelum commit: `git fetch kemnaker`, lalu `git merge --ff-only kemnaker/main` pada `main` lokal (perubahan belum di-commit milik pekerjaan ini tidak tumpang tindih), baru buat cabang.
- Dampak PR #29 yang perlu dicek: OPAC tetap menerima `?Subject=` (tautan daftar subjek di halaman depan tetap bekerja), dan `Home::getFeaturedBooks()` sekarang mengembalikan `enrichCatalogsWithCirculation($books)` (data sirkulasi tambahan). Itu bisa dimanfaatkan untuk menampilkan ketersediaan di kartu buku bila diinginkan. Jalankan ulang seluruh tes setelah fast-forward.
- Berkas terlacak yang berubah dan belum di-commit (milik pekerjaan ini):
  - `app/Modules/Home/Views/index.php` (skrip tema di `<head>`, `<noscript>`, tombol di header, nomor `?v=`)
  - `public/assets/home/landing-extra.css` (tombol, mode gelap, perbaikan audit, layar sempit)
  - `public/assets/home/landing.js` (tombol tema; huruf thumbnail pencarian)
  - `tests/e2e/landing.spec.js` (tes T19 sampai T25, T21 dan T23 diperketat)
  - `DESIGN.md` (bagian Mode gelap, komponen Masuk dan mode gelap, aturan kaca, 44 px)
- Berkas baru (belum terlacak) milik pekerjaan ini: `anti-slop/audit-004-2026-11-05.md` dan dokumen ini.
- **Jangan ikut di-commit** (bukan bagian pekerjaan ini): `cadangan-db/` (cadangan database), `bukti-log-2026-10-22/`, `.agents/`, `skills-lock.json`, `Grid Swiss Brutalism Style (Copy).fig`, `TODO-2026-10-13.md`, `anti-slop/audit-001` dan `audit-002`, `public/assets/home/library-hero-lama.jpg`.
- Ada dua `git stash` lama milik pemilik (`fitur: tombol Unduh PDF di flipbook...` dan `latihan todo 27 okt`). Jangan dihapus atau di-pop.
- Nomor versi aset di `index.php`: `landing.css?v=20`, `landing-extra.css?v=51`, `landing.js?v=47`. **Naikkan angkanya setiap kali CSS atau JS diubah**, kalau tidak peramban memakai salinan lama (ini pernah membuat perubahan font terlihat "tidak berpengaruh").

## 3. Yang dikerjakan dan di mana

| Hal | Di mana |
| --- | --- |
| Tombol "Masuk" (kapsul biru 44 px, `/login`) dan tombol mode gelap (lingkaran 44 px, ikon bulan atau matahari) | `index.php` bagian `.header-actions`; gaya di blok `=== Tombol Masuk dan tombol mode gelap ===` pada `landing-extra.css` |
| Cara kerja tema | Skrip kecil di `<head>` `index.php` memasang `data-theme` pada `<html>` sebelum CSS (tidak berkedip). Pilihan disimpan di `localStorage` dengan kunci `perpus-tema`. Bila belum ada pilihan, mengikuti pengaturan perangkat. `landing.js` mengurus klik tombol dan `aria-pressed` |
| Tampilan gelap | Blok `=== Mode gelap ===` sampai `=== akhir mode gelap ===` di `landing-extra.css`, semua diawali `:root[data-theme="dark"]`. Palet dan aturan ada di `DESIGN.md` bagian "Mode gelap" |
| Perbaikan hasil audit (kaca, gerak, skrim foto) | Blok "Hasil audit antislop" di `landing-extra.css` |
| Layar sempit (320 sampai 420 px) | Aturan `@media (max-width: 420px)`, `380px`, dan `359px` di blok tombol |
| Laporan audit | `anti-slop/audit-004-2026-11-05.md` (15 temuan dengan status, alasan satu baris, dan daftar yang lolos) |
| Kartu "Anggota terdaftar" tidak lagi menutupi kalimat di foto pada ponsel | Aturan `.floating-card.card-bottom { bottom: -30px }` di blok Hero `landing-extra.css`; dijaga T25 |

Cara membatalkan mode gelap: hapus blok `=== Mode gelap ===` di CSS, skrip tema dan `<noscript>` di `<head>`, tombol `#theme-toggle` di header, dan bagian tombol tema di `landing.js`.

## 4. Pengujian

Jalankan (Laragon harus menyala):

```bash
PLAYWRIGHT_JSON_OUTPUT_NAME=docs/laporan-uji-landing/hasil.json npx playwright test tests/e2e/landing.spec.js --workers=1 --reporter=list,json
```

Hasil terakhir: **25 dari 25 lulus** (sekitar 5 menit; T12 memakan 1 menit karena memeriksa 18 tautan).

- T01 sampai T18: pengujian halaman depan sebelumnya (lihat `tests/e2e/README.md`).
- T19: tombol Masuk dan mode gelap (letak, label, tersimpan, mengikuti perangkat, keyboard, menuju `/login`).
- T20: kontras teks di mode gelap.
- T21: tombol di ponsel (44 px, tidak bertumpuk, ujung tulisan nama diukur).
- T22: klik satu per satu di kedua mode, tanpa pesan galat di konsol (bukti R-35).
- T23: header tidak bertabrakan di 18 lebar (320 sampai 1920 px), dua mode.
- T24: efek kaca dibatasi.
- T25: tulisan putih di atas foto terbaca di titik terterang (4,5 banding 1) dan kartu "Anggota terdaftar" tidak menutupi kalimat di foto pada ponsel (320, 375, 414, 480 px).

Catatan: laporan PDF di `docs/laporan-uji-landing/laporan-uji-landing.pdf` masih versi 18 tes. Untuk memperbaruinya: jalankan tes di atas lalu `node tests/e2e/buat-laporan.js`. Itu mengubah berkas biner 8 MB yang terlacak, jadi lakukan hanya bila memang perlu dikirim.

## 5. Keputusan dan jebakan yang perlu diingat

- **`landing.css` jangan diedit langsung.** Itu hasil olahan Figma Make dan hanya satu baris panjang. Semua penyesuaian ke `landing-extra.css`.
- **Efek kaca navbar mati bila** `.site-frame` diberi `overflow` (hidden atau clip), atau navbar diberi lapisan `::after` atau `mix-blend-mode`. Penahan scroll samping dipasang per bagian (`.hero`, `.shortcuts`, dan seterusnya).
- **Batas efek kaca (R-10):** hanya bilah menu dan latar jendela. Jangan menambah blur ke kartu atau panel lain.
- **Tombol minimal 44 x 44 px** (R-03). Ukuran nama perpustakaan 18 px di ponsel adalah perubahan pemilik; di layar 380 px ke bawah mengecil bertahap (16 px, lalu 14 px di bawah 360 px) agar tidak tertimpa tombol.
- **Warna biru identitas `--blue` jangan ditimpa di mode gelap**, karena juga dipakai sebagai latar. Teks dan ikon biru di mode gelap memakai `#8FA7E6`.
- **Jangan mengubah UI lewat skrip tempel** (R-33). Pakai alat Edit. Di sesi ini sebagian perubahan sebelum audit sempat dilakukan lewat skrip Python di folder sementara (tidak ada yang tertinggal di repo); itu dicatat di audit nomor 9.
- **Skill antislop** ada di `C:\Users\iqbal\.agents\skills\antislop*` (versi 3.2.20). Alat Skill tidak mengenalinya, jadi baca berkasnya langsung dengan Read. Tidak ada pengaturan mode global, jadi tanyakan mode (during atau after) di sesi baru bila belum disebut, sesuai `AGENTS.md`.
- **Alur git yang biasa dipakai pemilik:** satu pekerjaan satu cabang, push cabang ke remote `kemnaker`, lalu gabung ke `main` dengan fast-forward (`git fetch kemnaker`, lalu `git push kemnaker <cabang>:main`), satu per satu. `origin` dan `fork` didorong setelahnya bila diminta. Hanya mentor yang bisa men-deploy ke server kantor.
- **`.env` lokal** sudah punya `app.maintenance = false` dan `app.maintenanceAllowIPs`. Mode pemeliharaan (halaman 503) sudah masuk `main`.
- **Skrip Node di luar folder proyek** tidak menemukan Playwright; jalankan dengan `NODE_PATH=C:/laragon/www/perpusguwe-main/node_modules`.
- **Heredoc panjang di shell sering gagal** pada sesi ini. Tulis skrip ke berkas dengan alat Write, lalu jalankan.

## 6. Pekerjaan lanjutan (urut prioritas)

1. **Commit dan push** pekerjaan ini dengan alur satu per satu. Saran cabang: `feat/mode-gelap-tombol-masuk`. Stage hanya berkas pada bagian 2 (jangan `git add -A`). Mungkin baik dipecah menjadi dua commit: (a) tombol Masuk dan mode gelap, (b) perbaikan audit. Setelah itu minta mentor menarik `main`.
2. **Target sentuh bawaan desain di bawah 44 px** (audit no. 10): saran "Populer" 31 px, tab Unggulan/Populer 38 px, tombol "Lihat" 34 px, tautan bagian bawah halaman 20 px tinggi, tautan nama/logo 40 px. Perlu menambah bantalan tanpa merusak tata letak; uji ulang T14 dan T23.
3. **Tombol menu ponsel hanya ikon** (audit no. 11): pertimbangkan tulisan "Menu".
4. **Mode gelap untuk halaman lain** (OPAC, login, layanan) bila diinginkan; saat ini hanya halaman depan dan pilihan tema tidak terbawa (audit no. 14).
5. **Layar pemuatan menahan sekitar 6 detik** sebelum pencarian bisa dipakai (minimal 3 detik di desain). Pertimbangkan memendekkannya; ini keputusan produk.
6. **Halaman layanan terasa lambat di lokal** (`/baca-ditempat` sekitar 6,8 detik, mode debug). Ukur ulang di server produksi.
7. **Warna sampul per divisi** belum terbukti dengan data database (data lokal tidak memuat buku divisi). Tambahkan data uji lalu tes.
8. **Uji di Firefox dan Safari** (perbesaran layar lebar memakai CSS `zoom`, baru didukung Firefox sejak versi 126).
9. **Perbarui laporan PDF** (bagian 4) bila akan dikirim ke pihak lain.

## 7. Kalimat awal yang disarankan untuk sesi baru

> Lanjutkan dari `docs/serah-terima/2026-11-05-mode-gelap-tombol-masuk.md`. Baca juga `DESIGN.md` dan `anti-slop/audit-004-2026-11-05.md`. Mulai dengan nomor 1 di bagian 6 (commit dan push satu per satu). Pakai antislop mode setelah selesai. Jawab dalam Bahasa Indonesia.
