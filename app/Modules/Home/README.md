# Modul Home (beranda / landing page)

Halaman `/` yang dibuka pengunjung. Navbar-nya dipakai bersama halaman OPAC, berita, dan buku tamu.

## Peta berkas

| Yang ingin diubah | Buka berkas ini |
| --- | --- |
| Data yang dikirim ke halaman (buku, subjek, statistik) | `Controllers/Home.php`, metode `index()` |
| Kerangka halaman (urutan bagian, tag `<head>`) | `Views/index.php` |
| Teks dan susunan satu bagian | `Views/landing/hero.php`, `shortcuts.php`, `showcase.php`, `catalog.php`, `footer.php` |
| Daftar layanan, isi footer, foto hero, warna sampul buku | `Helpers/landing_helper.php` |
| Navbar | `app/Views/layout/partial/navbar_publik.php` + `public/assets/home/navbar.css` + `navbar.js` |
| Tampilan beranda | `public/assets/home/landing-extra.css` (ubah di sini), `landing.css` (dasar dari Figma) |
| Perilaku beranda | `public/assets/home/js/` (satu berkas per fitur, lihat tabel di bawah) |
| Isi jendela Tata Tertib | `public/assets/home/tata-tertib.json` |

## JavaScript (`public/assets/home/js/`)

Dimuat berurutan oleh `Views/landing/scripts.php`. `util.js` harus pertama.

| Berkas | Tugasnya |
| --- | --- |
| `util.js` | `Landing.esc()` dan `Landing.normalize()`, dipakai berkas lain |
| `layout.js` | Memperbesar halaman di layar lebar, menghitung tinggi minimum hero |
| `hero.js` | Kaca cair di dua kartu pembaruan, lengkung foto, kemunculan kartu |
| `hero-carousel.js` | Carousel foto hero (titik, geser, tombol panah, otomatis) |
| `showcase.js` | Tab "Terbaru" dan "Populer" |
| `omnisearch.js` | Kolom pencarian hero (layanan, buku, tautan OPAC) |
| `rules-modal.js` | Jendela Tata Tertib |

## Hal yang sering ditanyakan

- **Tambah atau ganti foto hero:** taruh gambar di `public/assets/home/hero/`. Urutannya menurut nama berkas, dan nama berkas jadi label foto (`02-ruang-baca-anak.jpg` tampil sebagai "02 / Ruang Baca Anak"). Paling banyak 8 foto.
- **Tambah layanan di "Akses cepat":** tambah butir di `landing_services()` lalu masukkan kuncinya ke daftar urutan di `landing_shortcuts()`. Ikonnya ada di `landing_icon_paths()`.
- **Nomor versi CSS/JS:** tidak perlu dinaikkan manual. `landing_asset()` menambahkan `?v=` dari waktu ubah berkas, jadi peramban memuat ulang sendiri.
- **Tes:** `npx playwright test tests/e2e/landing.spec.js` (Laragon harus menyala).
- **Aturan tampilan:** `DESIGN.md` (warna, huruf, bentuk) dan audit antislop di `anti-slop/`.
