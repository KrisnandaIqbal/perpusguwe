# Serah terima: perbaikan beranda dari hasil critique

Tanggal: 17 November 2026. Cabang: `feat/footer-publik-dan-navbar-polos`. Belum ada commit. Sumber: critique beranda (skor 23/40), arsipnya di `.impeccable/critique/2026-11-17T04-29-21Z__app-modules-home-views-index-php.md`. Konteks produk ada di `PRODUCT.md` (baru).

## Sudah selesai

### P0: pencarian tidak boleh buntu

| Perubahan | Berkas |
|---|---|
| Saran di bawah kolom pencarian hanya berisi topik yang punya hasil di katalog (K3, Hukum ketenagakerjaan, Statistik, Pelatihan kerja, Upah). "SKKNI" dan "ISBN" dibuang. Label "Populer:" jadi "Topik:" karena daftarnya bukan hasil hitungan popularitas | `Home.php` (`SARAN_PENCARIAN`, `getSearchChips`), `hero.php` |
| Singkatan regulasi dibuka sebelum mencari: `uu 13 2003` jadi "undang-undang nomor 13 tahun 2003", juga `permenaker`, `permenakertrans`, `perpres`, `kepmenaker`, `kepmenakertrans`. Hasil di DB dev: "uu 13 2003" 0 jadi 20, "permenaker" 2 jadi 64 | `landing_query_expand()` di `landing_helper.php`, `Home::filterBooks()` |
| Bila 0 buku: dropdown menulis "Belum ada “X” di koleksi kami." lalu dua jalan keluar: cek di OPAC dan Indonesia OneSearch, dan JDIH Kemnaker. OPAC sudah mencatat pencarian 0 hasil sebagai usulan (`catatPencarian`) dan menampilkan OneSearch, jadi tidak ada rute usulan baru | `omnisearch.js`, `landing_jdih_url()`, `scripts.php`, `landing-extra.css` |

### P1: hasil yang terbaca dan hero yang jujur

- Judul hasil pencarian: 2 baris, 14 px, awalan diringkas (Permenakertrans, UU, Perpres, dan seterusnya), judul lengkap ada di tooltip. Judul berisi beberapa peraturan yang dipisah baris baru digabung dengan " / ". Tag hijau "Katalog" diganti "Regulasi" atau "Buku".
- H1 jadi "Cari regulasi, buku, dan riset ketenagakerjaan." Paragraf di bawahnya memakai jumlah judul dari DB: "5.550 judul koleksi. Cari di sini, lalu baca di perpustakaan." Slogan lama dibuang (eyebrow dilarang oleh aturan craft).
- Placeholder pencarian jadi "Cari buku, regulasi, layanan" supaya muat di 375 px pada 16 px.

### P2: mobile, tanda visual, ukuran teks, aksesibilitas

- Input pencarian 16 px (iOS tidak lagi memperbesar halaman). Foto hero di ponsel 340 px, card layanan mulai di y≈975 (sebelumnya 1232). Kartu buku satu kolom di ponsel. Footer ponsel 2 kolom (1.638 px jadi 1.384 px).
- Peringkat "#1–#4" hanya di tab Populer. Ikon bintang diganti ikon buku. Avatar "A D R" dan nomor 01–06 dibuang. Teks "BUKA" di card layanan dibuang (panah tetap).
- Teks 7–10 px dinaikkan ke 11–14 px (floating card, card layanan, tag, label navbar, teks sampul buatan). Sampul buatan: kategori dan penulis dipotong satu baris.
- Aksesibilitas: `aria-activedescendant` dan `id` pada option combobox, `aria-live` untuk jumlah hasil, tombol jeda carousel (44 px, disembunyikan bila "kurangi gerakan"), skip link "Lewati ke konten utama", `role="group"` pada foto hero.
- Copy layanan yang hanya bisa dilakukan di lokasi: "Pinjam sendiri di perpustakaan, pindai barcode" dan "Kembalikan lewat drop-box di perpustakaan".
- Judul tab: "Beranda | Perpustakaan Kementerian Ketenagakerjaan" (kata "Digital" dihapus). Telepon "-" tidak lagi jadi tautan `tel:`. Ajakan katalog tidak lagi mengulang kolom pencarian. Berita tanpa cover (atau berkas cover hilang) tidak menampilkan kotak abu-abu.

## Sudah dicoba

- `php -l` dan `node --check` untuk semua berkas yang berubah.
- Unit test: `LandingQueryTest` (baru, 3 tes) dan `BerandaTest` (7 tes) lulus, dengan PHP Laragon.
- curl ke `/home/search-books` dan beranda: hasil di tabel P0, lima chip tampil.
- Browser pane (desktop dan 375 px): H1, chip, dropdown dengan judul regulasi, blok 0 hasil untuk "SKKNI", tidak ada scroll horizontal, kartu layanan, kartu buku, berita, tinggi footer.
- Spec e2e `tests/e2e/landing.spec.js` hanya diubah teks H1-nya (baris 142–143). **Tidak dijalankan** (aturan proyek).

## Belum dicoba atau belum terbukti

- Dropdown di browser pane memakai data asli dari curl yang disuntikkan ke `fetch`, karena `public/.htaccess:26-30` memblokir UA yang memuat `Claude|fetch|index` (balasan 429). Aturan itu bisa ikut memblokir webview sungguhan; perlu ditinjau.
- Tombol jeda carousel dan skip link hanya dicek ada di DOM dan tampil, belum diuji dengan pembaca layar atau keyboard sungguhan. Combobox belum diuji dengan NVDA/VoiceOver.
- Tampilan footer 2 kolom di ponsel hanya dicek lewat tinggi halaman, bukan dilihat. Mode gelap, Safari, dan Firefox belum dicek. Halaman OPAC dan detail tidak disentuh.
- `is_home_cache`: bila aktif, data beranda dicache 300 detik dan chip baru muncul setelah cache lama habis (view memakai `?? []`, jadi tidak error).

## Tidak dikerjakan, perlu keputusan

1. **SKKNI belum ada di katalog** (0 hasil). Kementerian ini penerbitnya, jadi ini celah koleksi, bukan masalah tampilan. Untuk sementara H1 tidak menyebut SKKNI.
2. **Loader layar penuh** (`partial/loader.php`) ditahan minimal 3 detik, sekali per sesi tab. Itu momen merek yang dibuat sengaja, jadi tidak saya ubah. Bila mau, kurangi ke 1 detik atau matikan `loader_splash` di `Home::index()`.
3. **Kartu "Peminjaman" dan "Pengembalian"** di beranda adalah layanan kios di lokasi. Anda setuju boleh diganti dengan layanan yang bisa dilakukan dari meja kerja, dengan Pendaftaran Anggota dan Buku Tamu tetap ada. Belum diganti karena saya tidak tahu layanan online apa yang sudah ada (perpanjangan, usulan). Saat ini hanya teksnya yang dijelaskan "di perpustakaan".
4. **Daftar subjek** di bagian katalog memunculkan "Majalah Indonesia" di atas topik inti. Perlu dikurasi, karena saya tidak bisa menilai mana yang noise tanpa pustakawan.
5. **Foto hero** `02-lorong-rak-putih` mirip perpustakaan lain. Bila itu bukan ruang Kemnaker, alt-nya di `landing_helper.php` (`landing_hero_photos()`) menyesatkan. Perlu dicek.
6. **Berita utama** berisi lorem ipsum dan gambar non-institusi (data uji di DB dev, bukan kode).
7. **"pp" tidak ditambahkan** ke singkatan regulasi, karena juga cocok dengan "PPh". Kalau mau, tambahkan hanya saat diikuti angka.
8. **Tautan JDIH** membuka beranda JDIH, tanpa kata kunci, karena format URL pencariannya tidak saya ketahui.
9. Jawaban Anda soal audiens (pembaca offline, ASN dapat jalur anggota khusus, situs untuk seluruh Indonesia, pintu ke semua divisi) sudah dicatat di `PRODUCT.md`. Daftar "Pengguna" di sana masih menyebut ASN dan pustakawan sebagai pengguna utama; bila urutan prioritasnya berubah, ubah di sana.

## Lanjutan 18 November 2026 (critique putaran 2, P1 dan P2 kode)

- Enter di kolom pencarian sekarang mencari ke OPAC. Baris saran hanya terbuka bila dipilih dengan panah atau kursor (`omnisearch.js`, `active = -1`).
- Pencarian buku gagal: ada baris "Coba muat buku lagi" yang bisa dipilih dengan panah dan Enter, dan pengumuman untuk pembaca layar.
- Blok 0 hasil menjelaskan bahwa pencarian dicatat sebagai usulan bila dibuka di OPAC, dan apa itu Indonesia OneSearch. Teks status dan catatan 14 px.
- Kartu layanan: judul 15 px dua baris, deskripsi 14 px, isi dijangkar ke atas (panah di bawah), sehingga judul dan deskripsi sejajar di semua kartu.
- Hero: tautan "JDIH Kemnaker" di paragraf bawah judul.
- Tidak dikerjakan: P3 (glass dan doodle di pita biru kartu layanan, `landing-extra.css:17-21`), karena tidak dipilih. P0 konten uji (berita lorem ipsum, foto hero) adalah data.
- Catatan uji: `localhost/perpusguwe-main/public/` kena blokir UA (429); pakai `http://perpusguwe-main.test/` di browser pane.

## Lanjutan 18 November 2026 (banner jadi foto hero)

- Foto hero dari CMS > Banner, kategori Beranda, aktif, urut `sort`, maks 8. Kosong atau berkas hilang: kembali ke `assets/home/hero/` (`landing_hero_photos($banners)`).
- Upload banner Beranda dipotong otomatis ke 1220 × 1080 (rasio bingkai hero 610 × 540): di server `Banner::potongBingkaiHero()` (potong dari tengah), di browser lewat Cropper.js dari cdnjs (`Cms/Banner/Views/_potong.php`) dengan pratinjau bingkai, geser, zoom, dan peringatan bila foto terlalu kecil.
- Tombol jeda carousel dihapus atas permintaan Iqbal. Konsekuensi: geseran otomatis 6 detik tidak bisa dijeda (WCAG 2.2.2); masih berhenti saat kursor atau fokus di foto dan saat "kurangi gerakan".
- Belum diuji: Cropper di admin (butuh login dan akses cdnjs), dan unggah banner sungguhan. Potong di server diuji dengan gambar 3000×1000, 800×1600, dan 1220×1080: semuanya jadi 1220×1080.
