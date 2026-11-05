---
version: alpha
name: Perpustakaan Kemnaker (Beranda)
description: Portal perpustakaan ketenagakerjaan yang bersih dan tegas, biru institusi di atas permukaan putih sejuk, kontrol berbentuk kapsul, satu font.
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
    fontFamily: "Plus Jakarta Sans"
    fontSize: "clamp(48px, 5vw, 82px)"
    lineHeight: 1.08
    fontWeight: 700
    letterSpacing: "-0.055em"
  section-title:
    fontFamily: "Plus Jakarta Sans"
    fontSize: "clamp(32px, 3.4vw, 50px)"
    lineHeight: 1.1
    fontWeight: 700
    letterSpacing: "-0.05em"
  card-title:
    fontFamily: "Plus Jakarta Sans"
    fontSize: 15px
    lineHeight: 1.35
    fontWeight: 500
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

Portal perpustakaan instansi pemerintah untuk pegawai dan masyarakat. Kesannya tegas dan rapi, bukan ramai. Tingkat energi 3, irama 3, dan gerak 2 (skala 1 sampai 5; nilainya ada pada bagian `dials` di atas): satu blok biru penuh menjadi penanda utama, selebihnya putih sejuk. Susunan halaman bergantian antara bagian terang, bagian biru, dan bagian bawah yang gelap. Gerak hanya dipakai saat kursor diarahkan, saat elemen muncul, dan pada kartu yang melayang pelan.

## Asal dan susunan berkas

- Desain asli dibuat di Figma Make (proyek "Proto 1"). Berkas `public/assets/home/landing.css` adalah hasil olahan dari desain itu. **Jangan diubah langsung.** Tambahan dan penyesuaian ditulis di `public/assets/home/landing-extra.css`.
- Perilaku halaman (menu turun, pilihan tab, pencarian, jendela informasi, bilah menu mengambang, dan perbesaran layar) ada di `public/assets/home/landing.js`.
- Isi halaman depan ada di `app/Modules/Home/Views/index.php`. Halaman baru memakai kelas yang sudah tersedia di `landing.css`. Jangan menambah Tailwind, Bootstrap, atau pustaka CSS lain di halaman ini.
- Setiap kali CSS atau JS diubah, naikkan angka `?v=` pada tautan berkasnya di tampilan agar peramban tidak memakai salinan lama.
- Percobaan tampilan yang mungkin dibatalkan ditulis dalam blok berkomentar `=== nama ... === akhir nama ===` supaya mudah dihapus.

## Warna

Pakai nama warna di tabel, bukan kode hex yang dipilih sendiri. Perkiraan komposisi: sekitar 70% putih sejuk dan abu muda, 20% biru institusi, dan 10% teks gelap serta bagian bawah yang gelap.

| Nama | Hex | Dipakai untuk |
| --- | --- | --- |
| `blue` | `#0F3FB4` | Identitas: bagian "Akses cepat layanan", tombol utama, menu aktif, ikon, penanda posisi keyboard |
| `ink` | `#151922` | Teks utama, tombol kecil gelap, tab aktif |
| `page` | `#F9FBFC` | Latar halaman |
| `panel` / `panel-soft` | `#EEF1F6` / `#F1F4F8` | Latar bagian katalog, area sampul buku, kotak tab |
| `surface` | `#FFFFFF` | Kartu, kolom pencarian, jendela informasi |
| `border` / `divider` | `#E3E7EE` / `#EEF0F4` | Garis tipis pada kartu dan pemisah |
| `footer-bg` | `#11161F` | Latar bagian bawah halaman |
| `footer-text` / `footer-accent` | `#A3ABBA` / `#8FA7E6` | Tautan bagian bawah / judul kolom dan teks kecil |
| `muted-text` | `#5B6272` | **Teks pendamping berukuran kecil.** Warna ini sudah dipasang sebagai nilai `--muted` di `landing-extra.css` |
| `muted-design` | `#747C8C` | Warna lama dari desain Figma. Hanya untuk teks besar (18 px ke atas) atau hiasan |
| `success-text` / `success-bg` | `#0F7A52` / `#E3F6ED` | Keterangan "tersedia" |
| `danger` / `danger-bg` | `#C0392B` / `#FDEBE5` | Pesan kesalahan, keterangan "dipinjam" |

Aturan:

- Hanya **satu** warna identitas, yaitu biru. Dilarang menambah warna baru (oranye, ungu, atau warna bergradasi) untuk tombol atau judul.
- Teks di atas `blue` selalu putih (kontras 8,79 banding 1). Teks pendamping di atas biru memakai putih dengan ketebalan warna minimal 72% (kontras 5,31 banding 1); jangan lebih rendah.
- Warna bergradasi dan bayangan berwarna tidak dipakai sebagai hiasan. Satu-satunya efek kaca ada pada bilah menu atas.

### Kontras yang sudah diukur

Kontras adalah ukuran seberapa jelas teks terbaca di atas latarnya. Pedoman WCAG AA meminta paling sedikit 4,5 banding 1 untuk teks kecil dan 3 banding 1 untuk teks besar.

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

**Satu keluarga huruf: Plus Jakarta Sans** (ketebalan 400, 500, 600, 700, dan 800), dimuat dari Google Fonts. Huruf kedua pernah dicoba (Lora untuk judul) lalu dibatalkan. Jangan menambahkannya lagi tanpa keputusan baru.

| Peran | Ukuran | Ketebalan | Catatan |
| --- | --- | --- | --- |
| Judul utama | `clamp(48px, 5vw, 82px)` | 700 | Jarak antar huruf `-0.055em`, tinggi baris `1.08`, garis bawah biru pada frasa kunci |
| Judul bagian | `clamp(32px, 3.4vw, 50px)` | 700 | Jarak antar huruf `-0.05em` |
| Judul kartu | 14 sampai 15 px | 500 sampai 700 | |
| Paragraf | 15 px, tinggi baris `1.8` | 400 | Lebar paling banyak 540 px |
| Label huruf kapital | 11 px, jarak antar huruf `0.13em` | 800 | Disertai garis biru pendek di kiri (`.eyebrow`, `.section-kicker`) |
| Tombol kecil | 12 sampai 15 px | 700 | |

Aturan: jangan menurunkan ukuran teks di bawah 11 px, dan teks isi tidak di bawah 14 px (desain asli sempat memakai 7 sampai 10 px pada beberapa label; sudah dinaikkan). Huruf kapital dengan jarak lebar hanya untuk label pendek, bukan untuk kalimat.

## Tata letak

- **Layar penuh.** Tidak ada bingkai kartu di sekeliling halaman. Latar dan setiap bagian mengikuti lebar layar.
- **Margin samping**: `clamp(24px, 5vw, 76px)` di kiri dan kanan semua bagian (20 px di ponsel).
- **Jarak vertikal**: bagian utama tanpa jarak atas (tingginya mengikuti kolom teks), "Akses cepat layanan" 36 dan 40 px, bagian buku 96 dan 90 px, bagian katalog 84 px, dan bagian bawah halaman 72 px di atas.
- **Susunan kolom**: layanan 6 kolom (jarak 12 px), buku 4 kolom (jarak 22 px), subjek 2 kolom (jarak 10 px). Titik perubahan tampilan: lebar 1080, 800, dan 560 px.
- **Perbesaran layar.** Desain dibuat untuk lebar 1420 px. Pada layar 1024 px ke atas, `landing.js` memperbesar seluruh halaman sebesar lebar layar dibagi 1420 (paling kecil 1 kali). Konstanta `SKALA` mengalikan semuanya. **Jangan membesarkan teks dengan menimpa ukuran satu per satu**, karena itu merusak proporsi dan membuat jarak berantakan. Kalau terasa kecil, ubah `SKALA`.
- **Bagian utama**: foto berbentuk kubah di kanan. Tepi bawah kartu jumlah anggota yang melayang sejajar dengan baris terakhir di kolom kiri.

## Bentuk sudut

| Bagian | Kebulatan sudut |
| --- | --- |
| Tombol, tab, kolom pencarian, keterangan status | `999px` (bentuk kapsul) |
| Kotak ikon, kolom isian di jendela informasi | `10px` sampai `12px` |
| Kartu layanan, kartu subjek, hasil pencarian | `16px` sampai `20px` |
| Jendela informasi | `24px` |
| Avatar dan tombol ikon bulat | `50%` |
| Sampul buku | `3px 8px 8px 3px` (punggung buku di kiri) |

Bentuk kapsul untuk semua kontrol yang bisa ditekan, sudut sedang untuk wadah. Jangan mencampur tombol bersudut tajam dengan tombol kapsul dalam satu layar.

## Kedalaman dan gerak

- Bayangan lembut dan dingin, hanya untuk bagian yang terangkat: kolom pencarian `0 16px 40px rgba(47,58,77,.08)`, hasil pencarian dan kartu mengambang `0 26px 50px rgba(22,33,51,.16)`, foto utama `0 30px 55px rgba(25,37,58,.18)`.
- **Bilah menu mengambang** (muncul saat halaman digulir): kotak kaca buram (`backdrop-filter: blur(20px) saturate(190%)`) dengan sudut `22px`. Catatan teknis yang sudah terbukti: jangan beri `overflow` pada `.site-frame`, jangan tambah lapisan `::after` atau `mix-blend-mode` pada bilah menu, karena semuanya mematikan efek buram.
- Gerak: muncul perlahan (`rise`, 0,42 detik), jendela informasi (`pop`, 0,26 detik), kartu mengambang naik-turun 9 px selama 5 detik (`float`), kartu naik 4 sampai 6 px saat kursor diarahkan, dan sampul buku miring ke atas saat kursor diarahkan (`perspective(900px) rotateX(9deg) rotate(-3deg) translateY(-12px)`).
- Semua gerak yang tidak penting harus berhenti bila pengguna memilih pengaturan `prefers-reduced-motion: reduce` (kurangi gerakan) di perangkatnya.

## Komponen

- **Bilah menu atas**: logo dan dua baris teks ("Perpustakaan" tebal 19 px, lalu "KEMENTERIAN KETENAGAKERJAAN RI" huruf kapital kecil), diikuti menu (Beranda, Cari Koleksi, Buku Tamu, dan Layanan dengan menu turun). Menu aktif berwarna biru dengan garis bawah. Di ponsel berubah menjadi tombol menu dan teks kecil di bawah nama disembunyikan.
- **Kolom pencarian**: berbentuk kapsul setinggi 62 px dengan tombol bulat biru di kanan. Hasilnya muncul di bawahnya: layanan yang cocok, buku dari katalog, lalu tautan "Lihat semua hasil di katalog OPAC". Dapat dipakai dengan panah atas dan bawah, Enter, dan Esc.
- **Kartu layanan** (`.shortcut-card`): di atas latar biru, putih transparan. Saat kursor diarahkan atau kartu dipilih dengan keyboard: latar menjadi putih, teks `ink`, ikon biru berisi ikon putih, dan tulisan "BUKA" biru. Kartu yang berupa tautan **tidak boleh** diberi `style="color: ..."` langsung di tagnya, karena menimpa warna saat kursor diarahkan (pernah menyebabkan teks putih di atas latar putih).
- **Kartu buku** (`.showcase-card`): sampul di atas, lalu tahun atau jumlah peminjaman, judul, penulis, dan tombol "Lihat". Nomor urut `#1` di pojok.
- **Sampul buku otomatis** (untuk katalog yang belum punya gambar): warna latar mengikuti unit penerbit, sedangkan teks dan lingkaran memakai warna pendamping sampul.

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

  Warna buku umum tidak boleh sama dengan warna divisi. Sampul yang terang memakai teks gelap dan garis tipis.
- **Tombol kecil** (`.btn-small`): kapsul gelap yang berubah menjadi biru saat kursor diarahkan. **Tombol utama** (`.cta-button`): kapsul biru dengan ikon panah yang berubah menjadi `ink` saat kursor diarahkan.
- **Daftar subjek** (`.subject-list`): kartu putih bergaris tipis, nama subjek tebal, jumlah judul abu-abu, dan panah biru. Tautannya menuju OPAC dengan penyaring `?Subject=`.
- **Bagian bawah halaman**: latar `footer-bg`; identitas dan kontak di kiri (alamat, email, jam layanan, empat ikon media sosial); tiga kolom tautan (Layanan, Petunjuk, Direktori); garis pemisah; hak cipta di kiri dan "Kementerian Ketenagakerjaan · Republik Indonesia" di kanan.
- **Jendela informasi** (`.modal-panel`): kartu putih bersudut `24px`. Ditutup dengan tombol, klik di luar jendela, atau Esc. Halaman di belakangnya dikunci saat terbuka, posisi keyboard pindah ke jendela, lalu kembali ke tombol pemicu saat ditutup.

## Isi dan data

- Bahasa Indonesia yang tegas dan sopan. Label tombol berupa tindakan yang jelas ("Buka katalog lengkap", "Lihat", "Kirim"), bukan "Klik di sini" atau "Selengkapnya".
- Gunakan kata yang mudah dipahami. Bila istilah asing tidak bisa dihindari, jelaskan singkat pada pemakaian pertama.
- **Dilarang** memakai tanda pisah panjang (em dash) pada teks tampilan dan dokumentasi. Gunakan koma, titik, atau tanda hubung biasa.
- **Data harus asli.** Jumlah koleksi, subjek, buku populer, jumlah anggota, alamat, dan email diambil dari database atau tetapan yang sudah dipastikan benar. Jangan membuat angka atau testimoni karangan (misalnya "8.200+ pembaca aktif") dan jangan memakai teks pengisi.
- Tautan layanan menuju halaman yang sudah ada (`/opac`, `/buku-tamu`, `/peminjaman-mandiri`, `/pengembalian-mandiri`, `/baca-ditempat`, `/home/pendaftaran_online`, `/home/layanan-isbn`, `/home/tata-cara-sirkulasi`). Jangan membuat tautan ke halaman yang belum ada.
- Data dari katalog perlu dibersihkan sebelum ditampilkan (contoh: nama penulis yang berakhiran `;`, atau subjek yang berisi beberapa nilai dipisah `;`).
- Setiap daftar wajib punya tampilan untuk keadaan kosong (jelaskan penyebab dan langkah selanjutnya) dan keadaan gagal yang mudah dipahami.

## Kemudahan akses

- Tombol dan tautan punya penanda posisi yang jelas saat dipilih dengan keyboard (garis luar biru 2 px, berjarak 2 px). Jangan menghapus garis luar tanpa pengganti. Kolom pencarian memakai penanda pada pembungkusnya: `.catalog-search:focus-within { outline: 2px solid #0F3FB4; outline-offset: 3px; }`.
- Area yang ditekan minimal 40 px; tinggi kolom pencarian 62 px.
- Menu turun, hasil pencarian, dan jendela informasi dapat dipakai dengan keyboard (Tab, panah, Enter, Esc).
- Gambar hiasan memakai `alt=""`; gambar yang bermakna punya `alt` yang menjelaskan isinya.
- Perhatikan tabel kontras di atas. Teks pendamping berukuran kecil memakai `muted-text`; teks contoh di kolom pencarian memakai `#687081` (4,97 : 1 di atas putih).

## Yang dilarang (ciri tampilan generik)

- Latar utama ungu-biru bergradasi, bola cahaya melayang, bintik cahaya, atau latar berpola jaring.
- Tiga atau empat kartu fitur yang sama persis (ikon, judul, dua baris teks) hanya untuk mengisi halaman. Pakai kartu hanya untuk data atau tautan yang nyata.
- Emoji sebagai ikon, ikon dari paket acak, atau ilustrasi stok yang umum.
- Efek kaca di luar bilah menu atas.
- Huruf kedua, huruf serif untuk judul, atau lebih dari satu keluarga huruf dalam satu layar.
- Tombol berwarna oranye, ungu, atau bergradasi. Satu-satunya tombol berwarna adalah biru institusi.
- Statistik, nilai bintang, atau jumlah pembaca karangan, serta keterangan status yang tidak menandai keadaan nyata.
- Kalimat pembuka basa-basi ("Selamat datang di portal kami yang inovatif") dan kata berlebihan seperti "revolusioner" atau "solusi terpadu".
- Membesarkan teks dengan menimpa ukurannya satu per satu (gunakan `SKALA`).
- Memberi `style=""` langsung pada bagian yang berubah saat kursor diarahkan.

## Daftar periksa untuk halaman baru

1. Pakai kelas dari `landing.css`; tambahan ditulis di `landing-extra.css`. Naikkan angka `?v=`.
2. Bilah menu atas dan bagian bawah halaman sama dengan halaman depan (salin dari tampilan halaman depan).
3. Hanya memakai warna dari tabel; teks kecil memakai `muted-text`.
4. Semua tautan menuju halaman yang ada, dan semua data berasal dari sumber yang nyata.
5. Ada tampilan untuk keadaan kosong, sedang memuat, dan gagal pada data yang berubah-ubah.
6. Uji pada lebar 375, 768, 1366, dan 1920 px: tidak bergeser ke samping, bilah menu mengambang, serta tampilan saat kursor diarahkan dan penanda keyboard berfungsi.
7. Jalankan `npx playwright test` dan pastikan tidak ada pesan kesalahan di konsol peramban.
8. Tidak ada em dash, tidak ada angka karangan, tidak ada huruf kedua.
