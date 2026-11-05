// Membuat laporan PDF dari hasil uji halaman depan (beranda).
//   1. PLAYWRIGHT_JSON_OUTPUT_NAME=docs/laporan-uji-landing/hasil.json \
//      npx playwright test tests/e2e/landing.spec.js --workers=1 --reporter=list,json
//   2. node tests/e2e/buat-laporan.js
// Keluaran: docs/laporan-uji-landing/laporan-uji-landing.html dan .pdf
const fs = require('fs');
const path = require('path');
const { chromium } = require('@playwright/test');

const DIR = path.join(__dirname, '..', '..', 'docs', 'laporan-uji-landing');
const IMG = path.join(DIR, 'img');
const hasil = JSON.parse(fs.readFileSync(path.join(DIR, 'hasil.json'), 'utf8'));

const esc = (t) => String(t).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
const koma = (n) => String(n).replace('.', ',');
const detik = (ms) => (ms >= 60000 ? `${koma((ms / 60000).toFixed(1))} menit` : `${koma((ms / 1000).toFixed(1))} detik`);
const b64 = (file) => 'data:image/png;base64,' + fs.readFileSync(path.join(IMG, file)).toString('base64');
const kode = (t) => esc(t).replace(/`([^`]+)`/g, '<code>$1</code>');

// ---- Kumpulkan tes dan anotasinya
const tes = [];
(function telusuri(s) {
  for (const sp of s.specs || []) {
    for (const t of sp.tests) {
      const r = t.results[t.results.length - 1];
      const anot = r.annotations || [];
      const data = Object.assign({}, ...anot.filter((a) => a.type === 'data').map((a) => JSON.parse(a.description)));
      tes.push({
        id: sp.title.split(' ')[0],
        nama: sp.title.replace(/^T\d+\s+/, ''),
        status: r.status === 'passed' ? 'lulus' : 'gagal',
        durasi: r.duration,
        keterangan: anot.filter((a) => a.type === 'keterangan').map((a) => a.description),
        gambar: anot.filter((a) => a.type === 'gambar').map((a) => { const [file, ...c] = a.description.split('|'); return { file, caption: c.join('|') }; }),
        data,
        galat: (r.errors || []).map((e) => e.message.replace(/\u001b\[[0-9;]*m/g, '')),
      });
    }
  }
  for (const c of s.suites || []) telusuri(c);
})(hasil.suites[0]);

const ambil = (id) => tes.find((t) => t.id === id);
const lulus = tes.filter((t) => t.status === 'lulus').length;
const gagal = tes.length - lulus;
const tanggal = new Date(hasil.stats.startTime).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

const tautan = (ambil('T12').data.tautan || []);
const lambat = [...tautan].sort((a, b) => b.ms - a.ms).slice(0, 3);
const kontras = (ambil('T17').data.kontras || []);
const urutanTab = (ambil('T16').data.urutanTab || []);
const detikPemuatan = ambil('T01').data.layarPemuatanDetik;

// Nilai kontras sebelum perbaikan, dari uji pertama pada hari yang sama (teks yang saat itu di bawah batas).
const sebelum = {
  'Paragraf bagian utama': 4.04,
  'Tulisan "Populer:"': 4.04,
  'Keterangan kecil di kartu buku': 4.2,
  'Nama penulis buku': 4.2,
  'Paragraf bagian katalog': 3.8,
  'Jumlah judul pada daftar subjek': 4.2,
};
const baris = Object.entries(sebelum).map(([label, lama]) => {
  const k = kontras.find((x) => x.label === label);
  return { label, lama, baru: k ? k.rasio : null, batas: k ? k.batas : 4.5 };
});
const cincin = urutanTab.find((u) => /kolom pencarian/.test(u.jenis));

// ---- Diagram alur (ASCII)
const alur = {
  peta: String.raw`
 PENGUNJUNG
     |
     v
 [ Membuka alamat utama  /  ]
     |
     v
 [ Layar pemuatan (logo): paling singkat 3 detik, sekali per tab ] ---> [ Beranda siap dipakai ]
                                                                              |
        +---------------+---------------+---------------+---------------------+
        |               |               |               |                     |
        v               v               v               v                     v
   BILAH MENU      BAGIAN UTAMA     AKSES CEPAT        BUKU                KATALOG
   Beranda         judul            6 kartu            pilihan Unggulan    8 subjek
   Cari Koleksi    kolom pencarian  layanan            pilihan Populer     + tombol
   Buku Tamu       saran Populer    saat kursor        saat kursor         "Buka katalog
   Layanan (turun) foto + kartu     diarahkan:         diarahkan: sampul   lengkap"
   mengambang      jumlah anggota   kartu jadi putih   miring ke atas
   saat digulir
        |               |               |               |                     |
        v               v               v               v                     v
   /opac           /opac?search=   /peminjaman-       /opac/detail/ID    /opac?Subject=...
   /buku-tamu      kata yang       mandiri dan
   halaman layanan dicari          layanan lain
                                                                              |
                                                                              v
                                                                     BAGIAN BAWAH HALAMAN
                                       kontak | 4 media sosial | tautan lain | [Tata Tertib] ---> jendela
`,
  cari: String.raw`
 Pengunjung mengetik di kolom pencarian
        |
        v
 menunggu sebentar (0,2 detik) setelah berhenti mengetik
        |
        +--> mencocokkan nama layanan di halaman (paling banyak 2) -----------+
        |                                                                     |
        +--> meminta data ke /home/search-books (paling banyak 4 buku) ---+   |
                                                                          v   v
                    daftar hasil:  [layanan]   [buku]   [Lihat semua hasil di katalog OPAC]
                                                  |
        Panah atas/bawah = memilih  |  Esc = menutup  |  Enter atau klik = membuka
                                                  |
             +------------------+-----------------+----------------------+
             v                  v                                        v
      halaman layanan    /opac/detail/ID                       /opac?search=kata
`,
  menu: String.raw`
 Pengunjung menggulir halaman
        |
        v
 sudah turun lebih dari 24 px dari atas ?
        |
        +-- belum --> bilah menu menyatu dengan bagian utama (tanpa efek kaca)
        |
        +-- sudah --> bilah menu ditandai "menempel"
                           |
                           v
              berubah menjadi kotak kaca buram yang tetap di tepi atas layar
                           |
                 menggulir kembali ke atas ---> tanda dilepas, efek kaca hilang
`,
  jendela: String.raw`
 Pengunjung menekan "Tata Tertib Keanggotaan" (bagian bawah halaman)
        |
        v
 jendela dibuka -> halaman di belakangnya dikunci -> posisi keyboard masuk ke jendela
        |
        v
 isi diambil dari berkas tata-tertib.json (sekali saja) -> 10 bab ditampilkan
        |
        v
 menutup dengan:  tombol Tutup  |  tombol Esc  |  klik di luar jendela
        |
        v
 jendela dihapus -> kunci halaman dilepas -> posisi keyboard kembali ke tombol tadi
`,
  uji: String.raw`
 Persiapan: Laragon menyala -> membuka halaman utama -> menunggu layar pemuatan hilang
        |
        +-- A. Dasar ............... T01 halaman terbuka   T02 bagian utama   T03 huruf   T13 gambar
        |
        +-- B. Interaksi ........... T04 bilah menu   T05 menu mengambang   T06 pencarian
        |                            T07 layanan   T08 buku   T09 katalog   T10 bagian bawah   T11 jendela
        |
        +-- C. Tautan .............. T12 semua tautan di dalam situs (kode dan waktu jawaban)
        |
        +-- D. Ukuran layar ........ T14 empat ukuran   T15 ponsel   T18 seluruh halaman
        |
        +-- E. Kemudahan akses ..... T16 penanda posisi keyboard   T17 keterbacaan warna
        |
        v
 data hasil + gambar layar ---> pembuat laporan ---> laporan PDF ini
`,
};

// ---- Perbaikan dan temuan lain
const temuanLain = [
  {
    prioritas: 'SEDANG',
    judul: 'Layar pemuatan menunda pemakaian kolom pencarian',
    bukti: `Pada uji T01, sejak halaman dibuka sampai layar pemuatan (logo) hilang butuh ${detikPemuatan ? koma(Number(detikPemuatan).toFixed(1)) : '?'} detik di komputer lokal. Layar ini memang dirancang menahan logo paling singkat 3 detik, sekali untuk setiap tab.`,
    saran: 'Karena tujuan beranda adalah agar pengunjung langsung dapat mencari, dapat dipertimbangkan untuk memendekkan waktu tahan logo atau menampilkannya hanya bila halaman memang lambat dimuat. Ini keputusan tampilan, bukan kesalahan program.',
  },
  {
    prioritas: 'SEDANG',
    judul: 'Beberapa halaman layanan terasa lambat di komputer lokal',
    bukti: `Pada uji T12, waktu jawaban terlama: ${lambat.map((l) => `${l.url} ${koma((l.ms / 1000).toFixed(1))} detik`).join(', ')}. Halaman lain sekitar 2,3 sampai 3,0 detik. Semuanya berhasil dibuka.`,
    saran: 'Angka ini diukur di Laragon dengan perangkat bantu pengembang menyala dan tanpa penyimpanan sementara, sehingga belum mewakili server sebenarnya. Mohon diukur ulang di server yang dipakai pengunjung. Bila tetap lebih dari 1 detik, periksa pengambilan data pada halaman peminjaman, pengembalian, dan baca di tempat.',
  },
  {
    prioritas: 'RENDAH',
    judul: 'Warna sampul buku per divisi belum terbukti dengan data nyata',
    bukti: 'Pada uji T08, keempat sampul otomatis berwarna acak, karena data di komputer lokal tidak memuat buku dari Barenbang, Binalavotas, dan divisi lain.',
    saran: 'Aturan warna sudah diuji dengan 12 contoh nama divisi, tetapi belum dengan isi database. Setelah data buku divisi tersedia, sebaiknya ditambahkan uji yang memastikan warnanya sesuai tabel di berkas DESIGN.md.',
  },
  {
    prioritas: 'INFO',
    judul: 'Baru diuji di satu jenis peramban',
    bukti: 'Seluruh uji memakai Chromium (dasar dari Chrome dan Edge). Perbesaran otomatis di layar lebar memakai fitur CSS bernama zoom.',
    saran: 'Fitur zoom baru didukung Firefox mulai versi 126. Sebelum diluncurkan, mohon diuji juga di Firefox dan Safari. Pada peramban yang belum mendukung, halaman tetap terbaca tetapi tidak diperbesar di layar lebar.',
  },
];

const css = `
@page { size: A4; margin: 16mm 14mm; }
* { box-sizing: border-box; }
body { font-family: "Segoe UI", Arial, sans-serif; font-size: 10pt; line-height: 1.55; color: #151922; margin: 0; }
h1 { font-size: 23pt; line-height: 1.18; margin: 0 0 6pt; letter-spacing: -0.02em; }
h2 { font-size: 15pt; margin: 22pt 0 8pt; padding-bottom: 4pt; border-bottom: 2px solid #0f3fb4; color: #0f3fb4; break-after: avoid; }
h3 { font-size: 11.5pt; margin: 0 0 4pt; break-after: avoid; }
p { margin: 0 0 6pt; }
.sampul { padding: 30mm 0 0; }
.sampul .kicker { color: #0f3fb4; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; font-size: 9pt; margin-bottom: 8pt; }
.sampul .sub { font-size: 12pt; color: #4a5160; margin-bottom: 16pt; }
.meta { border-collapse: collapse; margin: 12pt 0; width: 100%; }
.meta td { padding: 5pt 8pt; border-bottom: 1px solid #e3e7ee; vertical-align: top; }
.meta td:first-child { width: 38mm; color: #5b6272; }
.angka { display: flex; gap: 10pt; margin: 14pt 0; }
.angka div { flex: 1; border: 1px solid #e3e7ee; border-radius: 8pt; padding: 10pt 12pt; }
.angka b { display: block; font-size: 22pt; line-height: 1.1; }
.angka span { color: #5b6272; font-size: 9pt; }
.lulus { color: #0b6b47; } .gagal { color: #b3261e; }
table.tabel { border-collapse: collapse; width: 100%; font-size: 9pt; margin: 6pt 0 10pt; }
table.tabel th { text-align: left; background: #eef1f6; padding: 5pt 6pt; border-bottom: 1px solid #cfd6e2; }
table.tabel td { padding: 4pt 6pt; border-bottom: 1px solid #e3e7ee; vertical-align: top; }
table.tabel tr { break-inside: avoid; }
.lencana { display: inline-block; padding: 1pt 7pt; border-radius: 99pt; font-weight: 700; font-size: 8pt; }
.lencana.lulus { background: #e3f6ed; } .lencana.gagal { background: #fdebe5; }
.lencana.SELESAI { background: #e3f6ed; color: #0b6b47; }
.lencana.SEDANG { background: #fff3d6; color: #8a5a00; } .lencana.RENDAH { background: #e6eefc; color: #0f3fb4; } .lencana.INFO { background: #eef1f6; color: #4a5160; }
pre.alur { font-family: Consolas, "Courier New", monospace; font-size: 6.6pt; line-height: 1.28; background: #f7f8fb; border: 1px solid #e3e7ee; border-radius: 6pt; padding: 8pt 10pt; margin: 6pt 0 10pt; white-space: pre; overflow: hidden; break-inside: avoid; }
.tes { margin: 0 0 14pt; padding: 10pt 12pt; border: 1px solid #e3e7ee; border-radius: 8pt; }
.tes.gagal-kotak { border-color: #e0806d; background: #fffaf8; }
.tes header { display: flex; justify-content: space-between; align-items: baseline; gap: 8pt; margin-bottom: 4pt; }
.tes .durasi { color: #5b6272; font-size: 8.5pt; white-space: nowrap; }
.tes ul { margin: 4pt 0 6pt 14pt; padding: 0; } .tes li { margin-bottom: 2pt; }
figure { margin: 6pt 0 8pt; break-inside: avoid; }
figure img { width: 100%; border: 1px solid #d6dbe4; border-radius: 4pt; display: block; }
figure.tinggi img { width: auto; max-height: 215mm; margin: 0 auto; }
figure.kecil img { width: 62%; margin: 0 auto; }
figcaption { font-size: 8.5pt; color: #4a5160; margin-top: 3pt; }
.galat { background: #fdebe5; border-left: 3px solid #c0392b; padding: 5pt 8pt; font-size: 8.5pt; white-space: pre-wrap; margin: 4pt 0; }
.kotak { margin: 0 0 10pt; padding: 8pt 12pt; border: 1px solid #e3e7ee; border-radius: 8pt; break-inside: avoid; }
.kotak dl { margin: 4pt 0 0; } .kotak dt { font-weight: 700; font-size: 9pt; margin-top: 3pt; } .kotak dd { margin: 0; font-size: 9.5pt; }
code { font-family: Consolas, monospace; font-size: 8.8pt; background: #eef1f6; padding: 0 3pt; border-radius: 3pt; }
dl.istilah dt { font-weight: 700; margin-top: 4pt; } dl.istilah dd { margin: 0 0 2pt; }
.hal { break-before: page; }
`;

let nomorGambar = 0;
function bagianTes(t) {
  const gambar = t.gambar.map((g) => {
    nomorGambar += 1;
    const tinggi = /halaman-penuh/.test(g.file);
    return `<figure class="${tinggi ? 'tinggi' : ''}${/ponsel-menu|layar-375/.test(g.file) ? ' kecil' : ''}"><img src="${b64(g.file)}" alt="${esc(g.caption)}"><figcaption><b>Gambar ${nomorGambar}.</b> ${esc(g.caption)}</figcaption></figure>`;
  }).join('');
  let isi = `<ul>${t.keterangan.map((k) => `<li>${esc(k)}</li>`).join('')}</ul>`;
  if (t.id === 'T12') {
    isi += `<table class="tabel"><tr><th>Alamat</th><th>Kode jawaban</th><th>Waktu jawaban</th></tr>${tautan.map((l) => `<tr><td>${esc(decodeURIComponent(l.url))}</td><td>${l.status}</td><td>${l.ms} milidetik</td></tr>`).join('')}</table>`;
  }
  if (t.id === 'T16') {
    isi += `<table class="tabel"><tr><th>Urutan</th><th>Bagian</th><th>Bentuk penanda</th><th>Kontras penanda</th></tr>${urutanTab.map((u, i) => `<tr><td>${i + 1}</td><td>${esc(u.teks || u.el)}</td><td>${esc(u.jenis)}</td><td>${u.kontras !== null ? koma(u.kontras) + ' : 1' : 'bawaan peramban'}</td></tr>`).join('')}</table>`;
  }
  if (t.id === 'T17') {
    isi += `<table class="tabel"><tr><th>Teks</th><th>Ukuran</th><th>Kontras terukur</th><th>Batas</th><th>Hasil</th></tr>${kontras.map((k) => { const ok = k.rasio >= k.batas; return `<tr><td>${esc(k.label)}</td><td>${koma(k.ukuran)} px</td><td>${koma(k.rasio)} : 1</td><td>${koma(k.batas)} : 1</td><td class="${ok ? 'lulus' : 'gagal'}"><b>${ok ? 'Cukup' : 'Kurang'}</b></td></tr>`; }).join('')}</table>`;
  }
  const galat = t.status === 'gagal' ? t.galat.map((g) => `<div class="galat">${esc(g.slice(0, 900))}</div>`).join('') : '';
  return `<section class="tes ${t.status === 'gagal' ? 'gagal-kotak' : ''}"><header><h3>${t.id}. ${esc(t.nama)} <span class="lencana ${t.status}">${t.status === 'lulus' ? 'LULUS' : 'GAGAL'}</span></h3><span class="durasi">${detik(t.durasi)}</span></header>${isi}${galat}${gambar}</section>`;
}

const barisTes = tes.map((t) => `<tr><td><b>${t.id}</b></td><td>${esc(t.nama)}</td><td><span class="lencana ${t.status}">${t.status === 'lulus' ? 'LULUS' : 'GAGAL'}</span></td><td>${detik(t.durasi)}</td></tr>`).join('');
const semuaLulus = gagal === 0;

function html(versiPeramban) {
  return `<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Laporan Uji Halaman Depan Perpustakaan Kemnaker</title><style>${css}</style></head><body>
<section class="sampul">
  <div class="kicker">Laporan pengujian otomatis</div>
  <h1>Halaman depan Perpustakaan Kemnaker</h1>
  <div class="sub">Pengujian tampilan dan fungsi halaman beranda dengan Playwright</div>
  <table class="meta">
    <tr><td>Tanggal</td><td>${esc(tanggal)}</td></tr>
    <tr><td>Alamat yang diuji</td><td>http://perpusguwe-main.test/ (komputer lokal dengan Laragon)</td></tr>
    <tr><td>Alat uji</td><td>Playwright ${esc(hasil.config.version || '')}, peramban Chromium ${esc(versiPeramban)}</td></tr>
    <tr><td>Ukuran layar</td><td>Utama 1366 x 768 piksel; uji ukuran lain: 375, 768, dan 1920 piksel</td></tr>
    <tr><td>Berkas uji</td><td><code>tests/e2e/landing.spec.js</code></td></tr>
    <tr><td>Lama pengujian</td><td>${detik(hasil.stats.duration)}</td></tr>
  </table>
  <div class="angka">
    <div><b>${tes.length}</b><span>pengujian dijalankan</span></div>
    <div><b class="lulus">${lulus}</b><span>lulus</span></div>
    <div><b class="${gagal ? 'gagal' : 'lulus'}">${gagal}</b><span>belum lulus</span></div>
    <div><b>3</b><span>perbaikan dikerjakan</span></div>
  </div>
  <h3>Ringkasan</h3>
  <p>${semuaLulus ? `Seluruh ${tes.length} pengujian lulus.` : `${lulus} dari ${tes.length} pengujian lulus; ${gagal} masih perlu perhatian.`} Halaman terbuka tanpa kesalahan, kolom pencarian langsung terlihat dan mengambil data dari katalog, bilah menu ikut mengambang saat halaman digulir, semua kartu dan tautan menuju halaman yang tersedia, jendela Tata Tertib dapat dioperasikan dengan keyboard, dan tampilan tidak bergeser ke samping pada empat ukuran layar.</p>
  <p>Pada pengujian pertama ditemukan dua hal yang perlu diperbaiki, yaitu keterbacaan warna enam teks pendamping dan penanda posisi keyboard pada kolom pencarian yang terlalu samar. Keduanya sudah diperbaiki dan diuji ulang; rinciannya ada pada bagian 4.</p>
  <h3>Cara membaca laporan</h3>
  <p>Bagian 1 berisi diagram alur. Bagian 2 memuat tabel hasil. Bagian 3 menjelaskan setiap pengujian disertai gambar. Bagian 4 memuat perbaikan yang sudah dikerjakan serta temuan lain beserta sarannya. Bagian 5 menjelaskan batas pengujian, dan bagian 6 berisi daftar istilah singkat.</p>
</section>

<section class="hal">
<h2>1. Diagram alur</h2>
<h3>1.1 Peta halaman dan perjalanan pengunjung</h3>
<pre class="alur">${esc(alur.peta)}</pre>
<h3>1.2 Alur pencarian</h3>
<pre class="alur">${esc(alur.cari)}</pre>
<h3>1.3 Alur bilah menu mengambang</h3>
<pre class="alur">${esc(alur.menu)}</pre>
<h3>1.4 Alur jendela Tata Tertib</h3>
<pre class="alur">${esc(alur.jendela)}</pre>
<h3>1.5 Alur pengujian</h3>
<pre class="alur">${esc(alur.uji)}</pre>
</section>

<section class="hal">
<h2>2. Ringkasan hasil</h2>
<table class="tabel"><tr><th>Kode</th><th>Pengujian</th><th>Hasil</th><th>Lama</th></tr>${barisTes}</table>
</section>

<section class="hal">
<h2>3. Hasil setiap pengujian, keterangan, dan gambar</h2>
${tes.map(bagianTes).join('')}
</section>

<section class="hal">
<h2>4. Perbaikan yang sudah dikerjakan dan temuan lain</h2>
<h3>4.1 Perbaikan setelah pengujian pertama</h3>
<div class="kotak"><h3>1. Warna teks pendamping dipekatkan <span class="lencana SELESAI">SELESAI</span></h3>
<dl><dt>Masalah</dt><dd>Enam teks pendamping berwarna abu-abu terlalu pucat, sehingga kurang jelas dibaca (kontras di bawah batas 4,5 banding 1).</dd>
<dt>Perbaikan</dt><dd>Warna abu-abu dipekatkan dari <code>#747C8C</code> menjadi <code>#5B6272</code> pada berkas <code>landing-extra.css</code>.</dd></dl>
<table class="tabel"><tr><th>Teks</th><th>Sebelum</th><th>Sesudah</th><th>Batas</th></tr>${baris.map((b) => `<tr><td>${esc(b.label)}</td><td class="gagal">${koma(b.lama)} : 1</td><td class="${b.baru !== null && b.baru >= b.batas ? 'lulus' : 'gagal'}"><b>${b.baru !== null ? koma(b.baru) + ' : 1' : '-'}</b></td><td>${koma(b.batas)} : 1</td></tr>`).join('')}</table></div>
<div class="kotak"><h3>2. Penanda posisi keyboard pada kolom pencarian dipertegas <span class="lencana SELESAI">SELESAI</span></h3>
<dl><dt>Masalah</dt><dd>Saat kolom pencarian dipilih dengan keyboard, satu-satunya perubahan adalah garis tepi biru yang sangat transparan (kontras 1,90 banding 1, batas 3 banding 1).</dd>
<dt>Perbaikan</dt><dd>Ditambahkan cincin biru setebal 2 piksel: <code>.catalog-search:focus-within { outline: 2px solid #0F3FB4; outline-offset: 3px; }</code>.</dd>
<dt>Hasil uji ulang</dt><dd>${cincin ? `Kontras penanda kini ${koma(cincin.kontras)} banding 1, berupa cincin biru pada kolom pencarian.` : 'Lihat pengujian T16.'}</dd></dl></div>
<div class="kotak"><h3>3. Teks contoh pada kolom pencarian dipekatkan <span class="lencana SELESAI">SELESAI</span></h3>
<dl><dt>Masalah</dt><dd>Teks contoh "Cari judul, penulis, topik, atau layanan" berwarna <code>#9AA1AE</code> (kontras 2,6 banding 1).</dd>
<dt>Perbaikan</dt><dd>Diganti <code>#687081</code> (kontras 4,97 banding 1 di atas putih). Perbaikan tambahan ini ditemukan saat memeriksa sumber warna, di luar daftar pengujian.</dd></dl></div>

<h3>4.2 Temuan lain dan saran</h3>
${temuanLain.map((f, i) => `<div class="kotak"><h3>${i + 1}. ${esc(f.judul)} <span class="lencana ${f.prioritas}">${f.prioritas}</span></h3><dl><dt>Bukti</dt><dd>${kode(f.bukti)}</dd><dt>Saran</dt><dd>${kode(f.saran)}</dd></dl></div>`).join('')}
</section>

<section>
<h2>5. Batas pengujian</h2>
<ul>
<li><b>Sudah diuji:</b> pemuatan halaman, huruf, bagian utama, bilah menu dan menu turun, bilah menu mengambang, pencarian (termasuk permintaan data ke <code>/home/search-books</code>), kartu layanan, pilihan dan gerak kartu buku, daftar subjek, bagian bawah halaman, jendela Tata Tertib, ${tautan.length} tautan di dalam situs, gambar, empat ukuran layar, menu ponsel, penanda posisi keyboard, dan keterbacaan warna.</li>
<li><b>Belum diuji:</b> Firefox dan Safari, tablet posisi mendatar, data sebenarnya di server, uji beban, pembaca layar, tampilan gelap, dan halaman selain beranda.</li>
<li><b>Data di komputer lokal:</b> tidak ada sampul buku asli dan tidak ada buku dari divisi Kemnaker, sehingga tampilan sampul asli dan warna per divisi tidak terlihat pada gambar.</li>
<li><b>Waktu jawaban halaman</b> diukur di Laragon dengan perangkat bantu pengembang menyala, sehingga tidak boleh dibaca sebagai kecepatan di server sebenarnya.</li>
<li><b>Ikon api di pojok kanan bawah gambar</b> adalah perangkat bantu pengembang CodeIgniter di komputer lokal. Ikon itu bukan bagian desain dan tidak muncul di server sebenarnya.</li>
<li><b>Tautan ke situs lain</b> (media sosial dan tautan di kolom Direktori) hanya diperiksa pengaturannya (dibuka di tab baru dan aman), tidak dibuka, karena menuju situs pihak lain.</li>
</ul>
<h3>Menjalankan ulang</h3>
<pre class="alur">PLAYWRIGHT_JSON_OUTPUT_NAME=docs/laporan-uji-landing/hasil.json npx playwright test tests/e2e/landing.spec.js --workers=1 --reporter=list,json
node tests/e2e/buat-laporan.js</pre>
<p>Laragon harus menyala. Hasilnya tersimpan di folder <code>docs/laporan-uji-landing</code>.</p>

<h2>6. Daftar istilah singkat</h2>
<dl class="istilah">
<dt>Kontras</dt><dd>Perbedaan terang-gelap antara teks dan latarnya. Semakin besar angkanya, semakin mudah dibaca. Pedoman WCAG AA meminta paling sedikit 4,5 banding 1 untuk teks kecil dan 3 banding 1 untuk teks besar.</dd>
<dt>Kursor diarahkan</dt><dd>Keadaan ketika penunjuk mouse berada di atas suatu bagian halaman tanpa menekannya.</dd>
<dt>Tombol Tab</dt><dd>Tombol keyboard untuk berpindah antar tombol dan tautan tanpa mouse. Penanda posisi harus terlihat jelas agar pengguna tahu sedang berada di mana.</dd>
<dt>Peramban</dt><dd>Aplikasi untuk membuka situs, misalnya Chrome, Edge, Firefox, dan Safari.</dd>
<dt>OPAC</dt><dd>Katalog daring perpustakaan, yaitu tempat mencari koleksi.</dd>
<dt>Sampul otomatis</dt><dd>Sampul berwarna yang dibuat otomatis oleh halaman bagi buku yang belum memiliki gambar sampul.</dd>
<dt>Bilah menu mengambang</dt><dd>Bilah menu di bagian atas yang tetap terlihat saat halaman digulir dan tampil sebagai kotak kaca buram.</dd>
</dl>
</section>
</body></html>`;
}

(async () => {
  const browser = await chromium.launch();
  const htmlFile = path.join(DIR, 'laporan-uji-landing.html');
  fs.writeFileSync(htmlFile, html(browser.version()));
  const page = await browser.newPage();
  await page.goto('file:///' + htmlFile.replace(/\\/g, '/'));
  await page.emulateMedia({ media: 'print' });
  const pdf = path.join(DIR, 'laporan-uji-landing.pdf');
  await page.pdf({
    path: pdf, format: 'A4', printBackground: true, displayHeaderFooter: true,
    headerTemplate: '<span></span>',
    footerTemplate: '<div style="font-size:7pt;width:100%;text-align:center;color:#747c8c;font-family:Arial">Laporan uji halaman depan Perpustakaan Kemnaker | halaman <span class="pageNumber"></span> dari <span class="totalPages"></span></div>',
    margin: { top: '16mm', bottom: '16mm', left: '14mm', right: '14mm' },
  });
  await browser.close();
  console.log('PDF:', pdf, (fs.statSync(pdf).size / 1024 / 1024).toFixed(2) + ' MB');
})();
