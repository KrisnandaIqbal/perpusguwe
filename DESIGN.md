---
version: alpha
name: Perpustakaan Kemnaker (Beranda)
description: Portal perpustakaan ketenagakerjaan yang bersih dan tegas, biru institusi di atas permukaan putih sejuk, kontrol berbentuk pill, dua font family (sans untuk antarmuka, serif untuk judul konten).
dials:
  energy: 3
  rhythm: 3
  motion: 2
colors:
  blue: "#0F3FB4"
  ink: "#151922"
  muted-design: "#747C8C"
  muted-text: "#5B6272"
  page: "#F9FBFC"
  panel: "#EEF1F6"
  panel-soft: "#F1F4F8"
  surface: "#FFFFFF"
  border: "#E3E7EE"
  divider: "#EEF0F4"
  footer-bg: "#11161F"
  footer-text: "#A3ABBA"
  footer-accent: "#8FA7E6"
  success-bg: "#E3F6ED"
  success-design: "#1A9A6B"
  success-text: "#0F7A52"
  danger: "#C0392B"
  danger-bg: "#FDEBE5"
  on-blue: "#FFFFFF"
  focus-ring: "#0F3FB4"
typography:
  display:
    fontFamily: "Source Serif 4"
    fontSize: "clamp(48px, 5vw, 82px)"
    lineHeight: 1.08
    fontWeight: 600
    letterSpacing: "-0.01em"
  section-title:
    fontFamily: "Source Serif 4"
    fontSize: "clamp(32px, 3.4vw, 50px)"
    lineHeight: 1.1
    fontWeight: 600
    letterSpacing: "-0.01em"
  card-title:
    fontFamily: "Source Serif 4"
    fontSize: 16px
    lineHeight: 1.35
    fontWeight: 600
  body:
    fontFamily: "Plus Jakarta Sans"
    fontSize: 15px
    lineHeight: 1.8
    fontWeight: 400
  label-caps:
    fontFamily: "Plus Jakarta Sans"
    fontSize: 11px
    lineHeight: 1.4
    fontWeight: 800
    letterSpacing: "0.13em"
rounded:
  control: 12px
  card: 18px
  panel: 24px
  pill: 999px
spacing:
  gutter: "clamp(24px, 5vw, 76px)"
  section-y: 96px
  grid-gap: 22px
components:
  button-primary:
    backgroundColor: "{colors.ink}"
    textColor: "{colors.on-blue}"
    rounded: "{rounded.pill}"
    padding: "8px 16px"
    hoverBackgroundColor: "{colors.blue}"
  button-cta:
    backgroundColor: "{colors.blue}"
    textColor: "{colors.on-blue}"
    rounded: "{rounded.pill}"
    padding: "15px 24px"
  search-field:
    backgroundColor: "{colors.surface}"
    borderColor: "{colors.border}"
    rounded: "{rounded.pill}"
    height: 62px
  shortcut-card:
    backgroundColor: "rgba(255,255,255,0.08)"
    textColor: "{colors.on-blue}"
    hoverBackgroundColor: "{colors.surface}"
    hoverTextColor: "{colors.ink}"
    rounded: "{rounded.card}"
    padding: 18px
  showcase-card:
    backgroundColor: "{colors.surface}"
    borderColor: "{colors.border}"
    rounded: 20px
---

# Perpustakaan Kemnaker, Halaman Depan

Dokumen ini adalah acuan untuk **semua halaman baru** agar tampilannya seragam dengan halaman depan (`/`) dan tidak berubah menjadi tampilan generik yang terkesan asal jadi. Semua nilai di sini diambil dari CSS yang benar-benar dipakai (`public/assets/home/landing.css`), bukan perkiraan. Versi lama dokumen ini tersimpan di `docs/arsip/DESIGN-lama-2026-11-05.md`.

## Gambaran desain

Portal perpustakaan instansi pemerintah untuk pegawai dan masyarakat. Kesannya tegas dan rapi, bukan ramai. Tingkat energi 3, irama 3, dan motion 2 (skala 1 sampai 5; nilainya ada pada bagian `dials` di atas): satu blok biru penuh menjadi penanda utama, selebihnya putih sejuk. Susunan halaman bergantian antara bagian terang, bagian biru, dan bagian bawah yang gelap. Motion hanya dipakai saat kursor diarahkan, saat elemen muncul, dan pada card yang melayang pelan.

## Asal dan susunan berkas

- Desain asli dibuat di Figma Make (proyek "Proto 1"). Berkas `public/assets/home/landing.css` adalah dasar dari desain itu (sudah dirapikan dan layar Figma yang tidak dipakai dibuang). Ubah tampilan di `public/assets/home/landing-extra.css`, yang dimuat setelahnya dan menang atas `landing.css`.
- Perilaku halaman ada di `public/assets/home/js/` (satu berkas per fitur: pilihan tab, pencarian, modal, carousel foto, perbesaran layar) dan `navbar.js` (dropdown dan navbar mengambang). Peta lengkapnya di `app/Modules/Home/README.md`.
- Isi halaman depan: kerangka di `app/Modules/Home/Views/index.php`, tiap bagian di `Views/landing/`, data tetap dan fungsi bantu di `Helpers/landing_helper.php`. Halaman baru memakai kelas yang sudah tersedia di `landing.css`. Jangan menambah Tailwind, Bootstrap, atau pustaka CSS lain di halaman ini.
- Nomor versi aset (`?v=`) diisi otomatis oleh `landing_asset()` dari waktu ubah berkas; tidak perlu dinaikkan manual.
- Percobaan tampilan yang mungkin dibatalkan ditulis dalam blok berkomentar `=== nama ... === akhir nama ===` supaya mudah dihapus.

## Warna

Pakai nama warna di tabel, bukan kode hex yang dipilih sendiri. Perkiraan komposisi: sekitar 70% putih sejuk dan abu muda, 20% biru institusi, dan 10% teks gelap serta bagian bawah yang gelap.

| Nama | Hex | Dipakai untuk |
| --- | --- | --- |
| `blue` | `#0F3FB4` | Identitas: bagian "Akses cepat layanan", button utama, menu aktif, ikon, focus ring |
| `ink` | `#151922` | Teks utama, button kecil gelap, tab aktif |
| `page` | `#F9FBFC` | Background halaman |
| `panel` / `panel-soft` | `#EEF1F6` / `#F1F4F8` | Background bagian katalog, area cover buku, kotak tab |
| `surface` | `#FFFFFF` | Card, search field, modal |
| `border` / `divider` | `#E3E7EE` / `#EEF0F4` | Garis tipis pada card dan pemisah |
| `footer-bg` | `#11161F` | Background footer |
| `footer-text` / `footer-accent` | `#A3ABBA` / `#8FA7E6` | Tautan bagian bawah / judul kolom dan teks kecil |
| `muted-text` | `#5B6272` | **Secondary text berukuran kecil.** Warna ini sudah dipasang sebagai nilai `--muted` di `landing-extra.css` |
| `muted-design` | `#747C8C` | Warna lama dari desain Figma. Hanya untuk teks besar (18 px ke atas) atau hiasan |
| `success-text` / `success-bg` | `#0F7A52` / `#E3F6ED` | Keterangan "tersedia" |
| `danger` / `danger-bg` | `#C0392B` / `#FDEBE5` | Pesan kesalahan, keterangan "dipinjam" |

Aturan:

- Hanya **satu** warna identitas, yaitu biru. Dilarang menambah warna baru (oranye, ungu, atau warna bergradasi) untuk button atau judul.
- Teks di atas `blue` selalu putih (kontras 8,79 banding 1). Secondary text di atas biru memakai putih dengan opacity minimal 72% (kontras 5,31 banding 1); jangan lebih rendah.
- Warna bergradasi dan shadow berwarna tidak dipakai sebagai hiasan. Efek glass (blur) hanya di tiga tempat: navbar, dua floating card di foto hero, dan background modal.
- Tulisan putih di atas foto harus lolos 4,5 banding 1 di titik terterang, bukan hanya rata-ratanya. Lapisan gelap di bawah foto (`.image-shade`) diatur untuk itu (4,86 banding 1 di titik terburuk); ukur ulang bila foto diganti.

### Kontras yang sudah diukur

Kontras adalah ukuran seberapa jelas teks terbaca di atas background-nya. Pedoman WCAG AA meminta paling sedikit 4,5 banding 1 untuk teks kecil dan 3 banding 1 untuk teks besar.

| Pasangan warna | Kontras | Keterangan |
| --- | --- | --- |
| `ink` di atas `page` | 16,94 : 1 | Lulus |
| Putih di atas `blue` | 8,79 : 1 | Lulus (AAA) |
| `blue` di atas putih | 8,79 : 1 | Lulus (AAA) |
| `footer-text` di atas `footer-bg` | 7,85 : 1 | Lulus |
| `muted-text` di atas putih / `page` / `panel` | 6,11 / 5,89 / 5,40 | Lulus |
| `muted-design` di atas putih / `page` / `panel` | **4,20 / 4,04 / 3,71** | **Kurang** untuk teks kecil |
| Hijau lama `#1A9A6B` di atas `success-bg` | **3,18 : 1** | **Kurang**; pakai `success-text` (4,76 : 1) |

Desain asli memakai `muted-design` dan hijau lama untuk teks kecil. Pada halaman baru jangan mengikutinya; pakai `muted-text` dan `success-text`.

## Huruf

**Dua font family**, dimuat dari Google Fonts:

- **Source Serif 4** (weight 600) untuk judul konten yang dibaca: judul utama, judul bagian, judul halaman dalam, dan judul buku di card. Variabelnya `--font-serif` di `landing-extra.css`. Judul utama dikunci ke potongan teks (`font-variation-settings: "opsz" 24`) karena potongan *display* otomatis di ukuran besar terlalu tipis.
- **Plus Jakarta Sans** (weight 400, 500, 600, 700, dan 800) untuk semua yang lain: menu, button, input field, label, teks meta, dan paragraf.

Aturan praktis: serif untuk judul konten yang dibaca, sans untuk yang diklik atau dioperasikan. Jangan memakai serif untuk teks di bawah 14 px. Pernah dicoba dan tidak dipakai: Lora, Newsreader, Fraunces, dan Instrument Serif. Jangan menambah font family ketiga tanpa keputusan baru.

| Peran | Ukuran | Weight | Catatan |
| --- | --- | --- | --- |
| Judul utama | `clamp(48px, 5vw, 82px)` | 600 | Source Serif 4. Letter spacing `-0.01em`, line height `1.08`, underline biru pada frasa kunci |
| Judul bagian | `clamp(32px, 3.4vw, 50px)` | 600 | Source Serif 4. Letter spacing `-0.01em` |
| Judul card buku | 16 px | 600 | Source Serif 4 |
| Judul card lain | 14 sampai 15 px | 500 sampai 700 | Plus Jakarta Sans |
| Paragraf | 15 px, line height `1.8` | 400 | Lebar paling banyak 540 px |
| Label uppercase | 11 px, letter spacing `0.13em` | 800 | Disertai garis biru pendek di kiri (`.eyebrow`, hanya di hero) |
| Button kecil | 12 sampai 15 px | 700 | |

Aturan: jangan menurunkan ukuran teks di bawah 11 px, dan teks isi tidak di bawah 14 px (desain asli sempat memakai 7 sampai 10 px pada beberapa label; sudah dinaikkan). Uppercase dengan jarak lebar hanya untuk label pendek, bukan untuk kalimat.

## Tata letak

- **Layar penuh.** Tidak ada bingkai card di sekeliling halaman. Background dan setiap bagian mengikuti lebar layar.
- **Gutter**: `clamp(24px, 5vw, 76px)` di kiri dan kanan semua bagian (20 px di ponsel).
- **Spacing vertikal**: hero tanpa jarak atas (tingginya mengikuti kolom teks), "Akses cepat layanan" 36 dan 40 px, bagian buku 96 dan 90 px, bagian katalog 84 px, dan footer 72 px di atas.
- **Susunan kolom**: layanan 6 kolom (jarak 12 px), buku 4 kolom (jarak 22 px), subjek 2 kolom (jarak 10 px). Breakpoint: lebar 1080, 800, dan 560 px.
- **Scaling halaman.** Desain dibuat untuk lebar 1420 px. Pada layar 1024 px ke atas, `js/layout.js` memperbesar seluruh halaman sebesar lebar layar dibagi 1420, dikali konstanta `SKALA` (sekarang 0,75), paling kecil 1 kali dan paling besar 2 kali. Artinya layar sampai sekitar 1890 px tampil seperti desain. **Jangan membesarkan teks dengan menimpa ukuran satu per satu**, karena itu merusak proporsi dan membuat jarak berantakan. Kalau terasa kecil, ubah `SKALA`.
- **Hero**: foto berbentuk arch di kanan. Tepi bawah card jumlah anggota yang melayang sejajar dengan baris terakhir di kolom kiri (desktop). Di ponsel card menggantung 30 px di bawah foto agar tidak menutupi tulisan di foto (diuji di 320 sampai 480 px, tes T25).

## Corner radius

| Bagian | Corner radius |
| --- | --- |
| Button, tab, search field, keterangan status | `999px` (bentuk pill) |
| Icon box, input field di modal | `10px` sampai `12px` |
| Card layanan, card subjek, search results | `16px` sampai `20px` |
| Modal | `24px` |
| Avatar dan button ikon bulat | `50%` |
| Cover buku | `3px 8px 8px 3px` (punggung buku di kiri) |

Bentuk pill untuk semua kontrol yang bisa ditekan, sudut sedang untuk wadah. Jangan mencampur button bersudut tajam dengan button pill dalam satu layar.

## Elevation dan motion

- Shadow lembut dan dingin, hanya untuk bagian yang terangkat: search field `0 16px 40px rgba(47,58,77,.08)`, search results dan floating card `0 26px 50px rgba(22,33,51,.16)`, foto utama `0 30px 55px rgba(25,37,58,.18)`.
- **Navbar publik** (satu partial, `app/Views/layout/partial/navbar_publik.php`, dipakai beranda dan halaman OPAC/berita/buku tamu; gaya di `public/assets/home/navbar.css`, perilaku di `navbar.js`, semua kelas berawalan `gn-`): liquid glass bersudut `22px`, tinggi 98 px, `position: fixed`. Glass muncul saat halaman digulir di beranda dan selalu ada di halaman dalam. Di Chromium tepi glass membiaskan background lewat filter SVG; peramban lain memakai `blur(14px)`. Isi: logo Kemnaker, Beranda, OPAC, Browse, Buku Tamu, Pendaftaran Anggota (bila keanggotaan online aktif), dropdown Layanan dan Statistik, dan button **Masuk** (berubah jadi Dashboard bila sudah login). Di bawah 1100 px menunya menjadi card putih yang dibuka button. Catatan teknis yang sudah terbukti: jangan beri `overflow` pada `.site-frame`, jangan tambah lapisan `::after` atau `mix-blend-mode` pada navbar, karena semuanya mematikan efek buram.
- Motion: muncul perlahan (`rise`, 0,42 detik), modal (`pop`, 0,26 detik), floating card naik-turun 9 px selama 5 detik sebanyak tiga kali lalu diam (`float`; motion tanpa henti tidak dipakai), card naik 4 sampai 6 px saat kursor diarahkan, dan cover buku miring ke atas saat kursor diarahkan (`perspective(900px) rotateX(9deg) rotate(-3deg) translateY(-12px)`).
- Semua motion yang tidak penting harus berhenti bila pengguna memilih pengaturan `prefers-reduced-motion: reduce` (kurangi gerakan) di perangkatnya.

## Komponen

- **Navbar**: logo dan dua baris teks ("Perpustakaan" tebal 19 px, lalu "KEMENTERIAN KETENAGAKERJAAN RI" uppercase kecil), diikuti menu (Beranda, Cari Koleksi, Buku Tamu, dan Layanan dengan dropdown). Menu aktif berwarna biru dengan underline. Di ponsel berubah menjadi button menu dan teks kecil di bawah nama disembunyikan.
- **Button "Masuk"** (`.login-button`): di kanan navbar. Berbentuk pill biru setinggi 44 px menuju `/login`; saat kursor diarahkan berubah menjadi `ink`.
- **Search field**: berbentuk pill setinggi 62 px dengan button bulat biru di kanan. Hasilnya muncul di bawahnya: layanan yang cocok, buku dari katalog, lalu tautan "Lihat semua hasil di katalog OPAC". Dapat dipakai dengan panah atas dan bawah, Enter, dan Esc.
- **Card layanan** (`.shortcut-card`): di atas background biru, putih transparan. Saat kursor diarahkan atau card dipilih dengan keyboard: background menjadi putih, teks `ink`, ikon biru berisi ikon putih, dan tulisan "BUKA" biru. Card yang berupa tautan **tidak boleh** diberi `style="color: ..."` langsung di tagnya, karena menimpa warna saat kursor diarahkan (pernah menyebabkan teks putih di atas background putih).
- **Card buku** (`.showcase-card`): cover di atas, lalu tahun atau jumlah peminjaman, judul, penulis, dan button "Lihat". Nomor urut `#1` di pojok.
- **Cover buku otomatis** (untuk katalog yang belum punya gambar): warna background mengikuti unit penerbit, sedangkan teks dan lingkaran memakai warna pendamping cover.

  | Unit | Warna |
  | --- | --- |
  | Barenbang | Oren |
  | Binalavotas | Biru |
  | Irjen / Inspektorat Jenderal | Kuning stabilo |
  | Sekjen / Sekretariat Jenderal | Hijau muda |
  | PHI-JSK | Putih |
  | Binapenta | Pink |
  | Binwasnaker | Merah |
  | Buku umum | Acak dari 8 warna lain (ungu, hijau tua, arang, cokelat, plum, abu), tetap sama untuk satu buku |

  Warna buku umum tidak boleh sama dengan warna divisi. Cover yang terang memakai teks gelap dan garis tipis.
- **Button kecil** (`.btn-small`): pill gelap yang berubah menjadi biru saat kursor diarahkan. **Button utama** (`.cta-button`): pill biru dengan ikon panah yang berubah menjadi `ink` saat kursor diarahkan.
- **Subject list** (`.subject-list`): card putih bergaris tipis, nama subjek tebal, jumlah judul abu-abu, dan panah biru. Tautannya menuju OPAC dengan penyaring `?Subject=`.
- **Footer**: background `footer-bg`; identitas dan kontak di kiri (alamat, email, jam layanan, empat ikon media sosial); tiga kolom tautan (Layanan, Petunjuk, Direktori); garis pemisah; hak cipta di kiri dan "Kementerian Ketenagakerjaan · Republik Indonesia" di kanan.
- **Modal** (`.modal-panel`): card putih bersudut `24px`. Ditutup dengan button, klik di luar modal, atau Esc. Halaman di belakangnya dikunci saat terbuka, posisi keyboard pindah ke modal, lalu kembali ke button pemicu saat ditutup.

## Isi dan data

- Bahasa Indonesia yang tegas dan sopan. Label button berupa tindakan yang jelas ("Buka katalog lengkap", "Lihat", "Kirim"), bukan "Klik di sini" atau "Selengkapnya".
- Gunakan kata yang mudah dipahami. Bila istilah asing tidak bisa dihindari, jelaskan singkat pada pemakaian pertama.
- **Dilarang** memakai tanda pisah panjang (em dash) pada teks tampilan dan dokumentasi. Gunakan koma, titik, atau tanda hubung biasa.
- **Data harus asli.** Jumlah koleksi, subjek, buku populer, jumlah anggota, alamat, dan email diambil dari database atau tetapan yang sudah dipastikan benar. Jangan membuat angka atau testimoni karangan (misalnya "8.200+ pembaca aktif") dan jangan memakai teks pengisi.
- Tautan layanan menuju halaman yang sudah ada (`/opac`, `/buku-tamu`, `/peminjaman-mandiri`, `/pengembalian-mandiri`, `/baca-ditempat`, `/home/pendaftaran_online`, `/home/layanan-isbn`, `/home/tata-cara-sirkulasi`). Jangan membuat tautan ke halaman yang belum ada.
- Data dari katalog perlu dibersihkan sebelum ditampilkan (contoh: nama penulis yang berakhiran `;`, atau subjek yang berisi beberapa nilai dipisah `;`).
- Setiap daftar wajib punya tampilan untuk empty state (jelaskan penyebab dan langkah selanjutnya) dan error state yang mudah dipahami.

## Aksesibilitas

- Button dan tautan punya focus ring yang jelas saat dipilih dengan keyboard (outline biru 2 px, berjarak 2 px). Jangan menghapus outline tanpa pengganti. Search field memakai focus ring pada wrapper-nya: `.catalog-search:focus-within { outline: 2px solid #0F3FB4; outline-offset: 3px; }`.
- Touch target minimal 44 x 44 px dengan jarak antar button (aturan R-03). Beberapa bagian bawaan desain Figma masih lebih kecil (saran "Populer" 31 px, tab 38 px, button "Lihat" 34 px, tautan di footer 20 px); itu dicatat sebagai pekerjaan lanjutan, dan komponen baru tidak boleh meniru ukuran itu. Tinggi search field 62 px.
- Dropdown, search results, dan modal dapat dipakai dengan keyboard (Tab, panah, Enter, Esc).
- Gambar hiasan memakai `alt=""`; gambar yang bermakna punya `alt` yang menjelaskan isinya.
- Perhatikan tabel kontras di atas. Secondary text berukuran kecil memakai `muted-text`; teks contoh di search field memakai `#687081` (4,97 : 1 di atas putih).

## Yang dilarang (ciri tampilan generik)

- Background utama ungu-biru bergradasi, bola cahaya melayang, bintik cahaya, atau background berpola jaring.
- Tiga atau empat card fitur yang sama persis (ikon, judul, dua baris teks) hanya untuk mengisi halaman. Pakai card hanya untuk data atau tautan yang nyata.
- Emoji sebagai ikon, ikon dari paket acak, atau ilustrasi stok yang umum.
- Efek glass (blur) di luar navbar, dua floating card di foto hero, dan background modal.
- Font family ketiga, atau serif untuk teks kecil, menu, button, dan label (serif hanya untuk judul konten).
- Button berwarna oranye, ungu, atau bergradasi. Satu-satunya button berwarna adalah biru institusi.
- Statistik, nilai bintang, atau jumlah pembaca karangan, serta keterangan status yang tidak menandai keadaan nyata.
- Kalimat pembuka basa-basi ("Selamat datang di portal kami yang inovatif") dan kata berlebihan seperti "revolusioner" atau "solusi terpadu".
- Membesarkan teks dengan menimpa ukurannya satu per satu (gunakan `SKALA`).
- Memberi `style=""` langsung pada bagian yang berubah saat kursor diarahkan.

## Daftar periksa untuk halaman baru

1. Pakai kelas dari `landing.css`; tambahan ditulis di `landing-extra.css`. Naikkan angka `?v=`.
2. Navbar dan footer sama dengan halaman depan (salin dari tampilan halaman depan).
3. Hanya memakai warna dari tabel; teks kecil memakai `muted-text`.
4. Semua tautan menuju halaman yang ada, dan semua data berasal dari sumber yang nyata.
5. Ada tampilan untuk empty state, sedang memuat, dan gagal pada data yang berubah-ubah.
6. Uji pada lebar 375, 768, 1366, dan 1920 px: tidak bergeser ke samping, navbar mengambang, serta tampilan saat kursor diarahkan dan focus ring berfungsi.
7. Jalankan `npx playwright test` dan pastikan tidak ada pesan kesalahan di konsol peramban.
8. Tidak ada em dash, tidak ada angka karangan, tidak ada huruf kedua.
