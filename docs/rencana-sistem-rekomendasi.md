# Rencana: Sistem Rekomendasi & Pencarian OPAC (disesuaikan ke perpusguwe)

> Turunan dari `BRIEF_SISTEM_REKOMENDASI_PERPUS.md`. Brief itu masih umum (stack-agnostic).
> Dokumen ini memetakan isinya ke kode dan tabel yang **sudah ada** di project ini.
> Status: **rencana final untuk dibaca**. Belum ada kode atau skema yang diubah.

---

## 0. Keputusan (2026-11-12)

| # | Topik | Keputusan | Alasan singkat |
|---|---|---|---|
| K1 | Koleksi yang tidak dimiliki | **Dirujuk ke Indonesia OneSearch** (onesearch.id) lewat link. Tidak memakai Open Library / Google Books | Katalog induk Perpusnas. Tidak perlu API, tidak ada data yang disimpan dari luar |
| K2 | Login anggota | **Akan dibuat.** Data anggota yang login disimpan di session dengan key **`session_member`** | Rekomendasi personal butuh identitas yang tidak bisa dipalsukan lewat URL |
| K3 | `?member_no=` di OPAC | **Dimatikan sekarang**, tidak menunggu login jadi | Siapa pun bisa menebak riwayat pinjam orang lain dari URL. Fiturnya juga jarang dipakai karena orang harus tahu nomor anggotanya |
| K4 | Izin personalisasi | **Aktif secara default (opt-out)**, dengan pemberitahuan saat login pertama dan tombol mematikan di halaman profil | Lihat bagian 0a |
| K5 | Usulan dari pencarian | **Semua pencarian 0 hasil dicatat**, tetapi baru **tampil di daftar usulan** pustakawan kalau sudah cukup sering dicari | Pencarian iseng atau salah ketik tidak memenuhi daftar |
| K6 | Klik ke OneSearch | **Dicatat**, dan dihitung sebagai sinyal permintaan yang lebih kuat | Orang yang mengeklik memang mencari buku itu |
| K7 | Embedding | **Tidak dipakai** | Lihat bagian 0b |
| K8 | Batch terjadwal | **Tidak dipakai.** Hasil hitungan disimpan di cache selama 24 jam dan dihitung ulang saat cache habis | Tidak perlu meminta mentor memasang cron/Task Scheduler. Bisa ditambah nanti kalau ternyata lambat |

### 0a. Kenapa opt-out (aktif secara default)

- Rekomendasi berbasis riwayat **hanya ditampilkan ke anggota itu sendiri**. Riwayatnya juga sudah tercatat
  untuk keperluan sirkulasi, jadi tidak ada data baru yang dikumpulkan.
- Kalau opt-in, hampir tidak ada anggota yang menyalakannya, dan fiturnya jadi tidak terpakai.
- UU PDP (UU 27/2022) memberi anggota hak menolak pemrofilan. Karena itu:
  - saat **login pertama**, tampilkan satu pemberitahuan singkat:
    "Kami memakai riwayat pinjammu untuk menyarankan buku. Hanya kamu yang bisa melihatnya. Kamu bisa mematikannya di Profil."
  - di **halaman profil** ada tombol untuk mematikannya, dan perubahannya langsung berlaku.
- Kalau dimatikan, anggota tetap mendapat rekomendasi yang tidak memakai riwayat pribadi,
  yaitu populer di unit kerjanya dan buku terkait di halaman detail.

### 0b. Apa itu embedding, dan kenapa tidak dipakai

Embedding mengubah judul atau abstrak menjadi deretan angka, supaya komputer bisa menilai bahwa "pemrograman web"
dan "membuat situs dengan PHP" itu mirip walaupun kata-katanya berbeda. Untuk menghasilkannya perlu model AI
yang berjalan di luar PHP (Python atau layanan berbayar).

Di project ini, kemiripan sudah bisa didapat dari tiga sumber yang ada:
- **nomor DDC** (`005.13` dan `005.1` jelas berdekatan),
- **subjek** yang sudah dirapikan,
- **FULLTEXT MySQL** untuk judul.

Untuk ukuran koleksi perpustakaan instansi, ketiganya cukup. Embedding baru layak dipertimbangkan
kalau nanti `DeweyNo` dan `Subject` ternyata banyak yang kosong (lihat bagian 7).

---

## 1. Yang sudah ada di project

Stack: CodeIgniter 4, modular (`app/Modules/...`), MySQL/MariaDB dengan dua DB group
(`default` dan `data`). Skema katalog mengikuti INLISLite.

| Kebutuhan di brief | Yang sudah ada | Catatan |
|---|---|---|
| koleksi (judul) | `catalogs` | Kolom: `Title`, `Author`, `Subject`, `Publisher`, `PublishYear`, `ISBN`, `DeweyNo`, `CallNumber`, `Languages`, `CoverURL`, `IsOPAC` |
| eksemplar (fisik) | `collections` | `Catalog_id`, `Status_id` → `collectionstatus`, `Location_id` → `locations`, `Rule_id` → `collectionrules` |
| pengarang, penerbit | teks di `catalogs.Author` / `catalogs.Publisher` | **Tidak ada tabel terpisah.** Pengarang bisa lebih dari satu dalam satu string |
| subjek | `catalogs.Subject` (dipisah `; `) + `catalog_ruas` tag 600 | Sudah dirapikan lewat `app/Database/rapikan_subjek_koleksi.sql` dan `isi_subjek_koleksi_kosong.sql` |
| klasifikasi DDC | `catalogs.DeweyNo` (teks) + `master_kelas_besar` | Hanya kelas besar. **Tidak ada tabel hierarki** (`kode_induk`) |
| peminjaman | `collectionloans`, `collectionloanitems` (`member_id`, `Collection_id`), perpanjangan di `CollectionLoanExtendModel` | Modul `SubModule/Sirkulasi/Peminjaman` |
| reservasi | `t_pemesanankoleksi` | Modul `Keanggotaan/PemesananKoleksi` |
| usulan pengadaan | `t_usulankoleksi` (`Title`, `Author`, `Publisher`, `PublishYear`, `isbn`, `description`, `Status`) | Modul `Keanggotaan/UsulanKoleksi`. **Sudah ada**, tinggal diperluas |
| anggota | `members` | Ada `JenisAnggota_id`, `UnitKerja_id`, `SubUnitKerja_id`. Kolom `Fakultas_id`/`Jurusan_id`/`ProgramStudi_id` warisan INLISLite |
| pencarian katalog | `Opac::search()`, `Opac::applyFulltextSearch()` | FULLTEXT `ft_title`, `ft_author`, `ft_subject`, `ft_publisher`, `ft_all` (lihat `app/Database/opac_fulltext_indexes.sql`), fallback `LIKE` |
| rekomendasi | `Opac::calculateRecommendations()` + view `Opac/Views/recommendations.php` | Lihat bagian 2 |
| cache | `app/Libraries/Cache` dan cache bawaan CI4 | Dipakai untuk K8 |
| unit test | `tests/unit/*` (PHPUnit) | Pola sudah ada, misalnya `NamaRulesTest.php` |

### Konteks pengguna
Anggota di sini pegawai dan peserta magang instansi, bukan mahasiswa. Jadi
**"prodi/angkatan" di brief diganti dengan `UnitKerja_id`/`SubUnitKerja_id` dan `JenisAnggota_id`**
(ASN, non-ASN, magang, dan seterusnya).

---

## 2. Kondisi rekomendasi yang sekarang

Letak: `app/Modules/Opac/Controllers/Opac.php` sekitar baris 987–1300.

Cara kerjanya:
1. Halaman OPAC dibuka dengan `?member_no=XXX`.
2. Semua baris `collectionloanitems` digabung dengan `catalogs`, lalu dimuat ke memori PHP.
3. Dibuat pivot anggota × judul, lalu dihitung cosine similarity **antaranggota** (user-based CF).
4. Judul dari 10 anggota yang paling mirip direkomendasikan.
5. Kalau anggota belum pernah pinjam (cold start), yang tampil buku terpopuler secara umum.
6. Precision/NDCG dihitung, tetapi hasilnya tidak dipakai.

| # | Masalah | Penyelesaian |
|---|---|---|
| P1 | Rekomendasi muncul untuk siapa pun yang mengetik `member_no` di URL | K3: dimatikan sekarang. K2: setelah login jadi, anggota diambil dari `session_member` |
| P2 | Seluruh riwayat peminjaman dimuat dan dihitung **setiap kali halaman dibuka** (O(anggota²)) | Diganti query item-item di SQL + cache 24 jam (K8) |
| P3 | Cold start memakai populer **umum**, bukan per unit kerja | Populer per `UnitKerja_id`, dengan populer umum sebagai cadangan |
| P4 | Buku yang sudah pernah dipinjam bisa direkomendasikan lagi (perlu dicek di `getBookRecommendations`) | Disaring di `Ranker` |
| P5 | Tidak ada label alasan ("Karena kamu meminjam …") | Setiap baris membawa label alasan |
| P6 | Pencarian 0 hasil tidak dicatat, dan pengguna tidak diarahkan ke mana pun | Link OneSearch + koleksi mirip + pencatatan usulan (bagian 4a, 4b) |

---

## 3. Perubahan tabel

Prinsip: pakai tabel yang ada, tambah sesedikit mungkin. SQL ditulis sebagai file
idempoten di `artifacts/` (pola yang sudah dipakai), lalu dijalankan mentor di server.

| Tabel di brief | Keputusan | Alasan |
|---|---|---|
| prodi | **Tidak dibuat.** Pakai `master_unit` / `master_subunit` | Sudah ada |
| anggota.izin_personalisasi | **Tambah kolom** `members.IzinPersonalisasi TINYINT(1) NOT NULL DEFAULT 1` dan `members.IzinPersonalisasiDiumumkan TINYINT(1) NOT NULL DEFAULT 0` | K4. Kolom kedua menandai pemberitahuan login pertama sudah ditampilkan |
| koleksi, eksemplar, peminjaman, reservasi | **Tidak dibuat** | Sudah ada |
| pengarang, subjek, penerbit (N–N) | **Tidak dibuat.** Pecah string `Author`/`Subject` saat dihitung | Normalisasi penuh akan menyentuh modul Katalog dan MARC |
| klasifikasi_ddc (hierarki) | **Tidak dibuat.** Jarak DDC dihitung dari prefix string `DeweyNo` (`005.13` → `005.1` → `005` → `00`) | Hierarki DDC memang tersirat di kodenya |
| log_pencarian | **Baru**: `opac_search_log` (`id`, `member_id` nullable, `query`, `search_by`, `result_count`, `created_at`) | Brief |
| log_klik | **Baru**: `opac_click_log` (`id`, `search_log_id`, `catalog_id` nullable, `target` (`katalog`/`onesearch`), `position`, `created_at`) | Brief + K6 |
| metadata_eksternal | **Tidak dibuat** | K1 |
| usulan_pengadaan | **Perluas `t_usulankoleksi`**: `source` (`manual`/`pencarian`), `query_normal` (indeks unik bersama `source`), `request_count`, `onesearch_click_count`, `last_searched_at` | K5, K6. Pustakawan cukup memantau satu daftar |
| kemiripan_item | **Tidak dibuat.** Diganti cache | K8 |
| item_embedding | **Tidak dibuat** | K7 |
| rekomendasi_cache | **Tidak dibuat.** Pakai cache CI4 | K8 |

Semua tabel baru masuk DB group yang sama dengan `catalogs` (perlu dicek: `default` atau `data`).

---

## 4. Letak kode

Modul baru `app/Modules/Rekomendasi/` dengan pola folder yang sama seperti modul lain
(`Config/Routes.php`, `Controllers/`, `Models/`, `Libraries/`).

| Komponen brief | Di project ini |
|---|---|
| `catalog_search` | **Tidak dibuat.** Tetap `Opac::search()`. Hanya ditambah pencatatan log dan cabang "hasil 0" |
| `content_based` | `Rekomendasi/Libraries/ContentScore.php`: DDC + subjek + pengarang |
| `ddc` util | Fungsi di `ContentScore`: `ddcSimilarity('005.13', '005.1')` |
| `popularity` | Query `COUNT` per `UnitKerja_id` dari `collectionloanitems` + `members`, di-cache 24 jam |
| `collaborative` | Item-item "yang meminjam X juga meminjam Y": satu query self-join `collectionloanitems`, di-cache 24 jam per judul |
| `hybrid_ranker` | `Rekomendasi/Libraries/Ranker.php`: fungsi murni (input skor, output urutan) supaya mudah diuji. Termasuk saring buku yang sudah dipinjam |
| `diversifier` | Bagian dari `Ranker`: maksimal 2 judul dengan pengarang yang sama per baris |
| `entity_resolver` + `external_metadata` | **Diganti link ke OneSearch** (bagian 4a). Satu fungsi kecil yang menyusun URL |
| `privacy` util | Tidak perlu modul khusus. Semua query rekomendasi hanya memakai `member_id` numerik |
| Job batch | **Tidak ada** (K8) |

**Session anggota (K2)**: fitur login anggota menyimpan data di `session('session_member')`.
Rekomendasi hanya butuh `session('session_member')['member_id']` (= `members.ID`).
Kalau key ini kosong, pengunjung dianggap tamu.

**Endpoint**, disesuaikan dengan grup route `opac` yang sudah ada:

| Brief | Di project ini |
|---|---|
| `GET /search?q=` | Tetap `opac/index` dan `opac/search`, ditambah blok OneSearch + koleksi mirip |
| `GET /recommend/home` | `opac/index` (baris rekomendasi) atau beranda `Home` |
| `GET /recommend/item/{id}` | Bagian baru di `opac/detail/(:any)`: "Buku terkait" |
| `POST /feedback/click` | `api/Opac/click`: klik ke katalog dan ke OneSearch |
| `GET /usulan-pengadaan` | **Sudah ada** di modul UsulanKoleksi (admin). Tambah kolom jumlah dicari, jumlah klik OneSearch, dan filter sumber |

**UI**: pakai kartu katalog yang sudah ada di `Opac/Views/index.php` dan `browse.php`,
bukan komponen baru. Yang ditambah hanya judul baris beserta label alasannya.

### 4a. Rujukan ke OneSearch

Sudah dicek ke situsnya pada 2026-11-12. onesearch.id adalah "Indonesia OneSearch" milik Perpusnas,
dengan sekitar 16,5 juta entri dari sekitar 3.700 institusi. Mesinnya kemungkinan besar VuFind.

Format URL pencarian:

```
https://onesearch.id/Search/Results?lookfor=<kata kunci>&type=<jenis>
```

| Pencarian di OPAC kita (`search_by`) | `type` di OneSearch | Status |
|---|---|---|
| Semua kolom | `AllFields` | Sudah dicek, jalan |
| Judul | `Title` | Nilai bawaan VuFind, **belum dicek** |
| Pengarang | `Author` | Belum dicek |
| Subjek | `Subject` | Belum dicek |
| ISBN | `ISN` | Belum dicek |

Contoh: `lookfor=laskar+pelangi&type=AllFields` mengembalikan 1.166 hasil.

Cara pakainya:
- Saat hasil pencarian OPAC **0**, tampilkan:
  "Buku ini belum ada di perpustakaan kami." + tombol **"Cari di Indonesia OneSearch"** (buka tab baru)
  + baris "Mungkin kamu tertarik" berisi koleksi lokal yang mirip.
- Saat hasilnya **sedikit** (kurang dari 5), link OneSearch tetap tampil di bawah hasil.
- Kata kunci di-`urlencode`. Ini hanya link, jadi tidak ada data anggota yang dikirim ke luar.
- Klik tombol dicatat lewat `api/Opac/click` dengan `target = 'onesearch'` sebelum tab baru terbuka (K6).

**Koleksi lokal yang mirip**:
1. Cari query dengan FULLTEXT `ft_all` dalam `NATURAL LANGUAGE MODE`
   (lebih longgar daripada `BOOLEAN MODE` yang sekarang dipakai `search()`).
2. Dari hasil teratas, ambil `DeweyNo` dan `Subject`, lalu perluas lewat content-based.
3. Kalau tetap kosong, tampilkan populer di unit kerja anggota (atau populer umum untuk tamu).

**Tidak dipakai**: API JSON (`/api/v1/search` mengembalikan 404) dan feed RSS (`&view=rss`).
RSS tidak memuat ISBN dan penerbit, urutannya tidak berdasarkan relevansi, dan butuh akses internet dari server.

### 4b. Aturan usulan dari pencarian (K5, K6)

1. Query dinormalisasi: huruf kecil, spasi ganda dirapikan, tanda baca di awal/akhir dibuang.
2. Pencarian 0 hasil menaikkan `request_count` pada baris `t_usulankoleksi` dengan `query_normal` yang sama
   (dibuat baru kalau belum ada, `source = 'pencarian'`). Satu session hanya dihitung **sekali per query per hari**,
   supaya satu orang yang menekan Enter berkali-kali tidak menggelembungkan angka.
3. Klik ke OneSearch menaikkan `onesearch_click_count` dengan aturan sekali hitung yang sama.
4. Baris tampil di daftar usulan pustakawan kalau **`request_count ≥ 3` atau `onesearch_click_count ≥ 2`**.
   Angkanya disimpan sebagai konstanta dan mudah diubah.
5. Usulan manual (`source = 'manual'`) tetap tampil seperti sekarang, tanpa ambang.

---

## 5. Tahapan

**Tahap 0: bereskan yang ada** (kecil, bisa langsung)
- Matikan `?member_no=` di `Opac::index()` (K3). OPAC kembali ke tampilan katalog biasa.
- Pindahkan kode rekomendasi dari `Opac.php` (file ini sudah lebih dari 2000 baris) ke modul `Rekomendasi`.

**Tahap 1: buku terkait + log (tanpa login)**
- "Buku terkait" di halaman detail: content-based (DDC prefix + subjek + pengarang) + item-item.
- Populer umum dan populer per jenis anggota.
- `opac_search_log` dan `opac_click_log`.
- Unit test: `ddcSimilarity`, `Ranker` (urutan, saring yang sudah dipinjam, batas per pengarang).

**Tahap 2: pencarian 0 hasil (tanpa login)**
- Blok OneSearch + koleksi mirip (bagian 4a).
- Pencatatan usulan dan ambang tampil (bagian 4b), kolom baru di daftar UsulanKoleksi.
- Unit test: penyusun URL OneSearch (pemetaan `search_by` → `type`, encoding), normalisasi query, aturan ambang.

**Prasyarat tahap 3: login anggota** (fitur terpisah)
- Menyimpan `member_id` di `session('session_member')`.
- Halaman profil anggota, tempat tombol izin personalisasi.

**Tahap 3: rekomendasi personal (setelah login jadi)**
- Pemberitahuan login pertama + tombol di profil (K4).
- Baris beranda dengan label alasan: "Karena kamu meminjam …", "Populer di <unit kerja>", "Subjek: …".
- Bobot berdasarkan jumlah pinjaman anggota, sesuai brief:
  0 pinjaman → populer di unit kerja; 1–4 → dominan content-based; ≥5 → dominan item-item.
- Kalau `IzinPersonalisasi = 0`: hanya populer di unit kerja.
- Hapus kode user-based yang lama.

**Tahap 4 (opsional): evaluasi**
- precision@k / recall@k dari data peminjaman lama, dengan split berdasarkan waktu.
  Fungsi `calculateMetrics`/`calculateNDCG` yang ada bisa dipindah ke sini.

---

## 6. Kriteria selesai

- Pencarian judul yang tidak dimiliki menampilkan link OneSearch, dan koleksi mirip kalau ada.
- Setiap pencarian 0 hasil tercatat di `t_usulankoleksi`. Yang tampil ke pustakawan hanya yang melewati ambang.
- Halaman detail selalu punya "Buku terkait" (atau tidak menampilkan bagian itu sama sekali, bukan baris kosong).
- Anggota baru yang login tetap mendapat rekomendasi (populer di unit kerja).
- Setiap baris rekomendasi punya label alasan.
- Buku yang sudah dipinjam anggota tidak direkomendasikan ulang.
- Riwayat pinjam seseorang tidak bisa dilihat orang lain lewat URL mana pun.
- Ada unit test untuk `Ranker`, `ddcSimilarity`, penyusun URL OneSearch, dan aturan ambang usulan.

---

## 7. Belum dicek

- Skema DB asli tidak dibuka langsung. Nama kolom diambil dari kode (model, query, migration).
- ~~Seberapa lengkap isian `DeweyNo` dan `Subject`.~~ Sudah dicek di DB lokal: `DeweyNo` kosong 1 dari 5.582,
  `Subject` kosong 300. Content-based layak. Metode, flow, dan work plan lengkap ada di
  `docs/rekomendasi-metode-flow-workplan.md`.
- Apakah `getBookRecommendations` sudah menyaring buku yang pernah dipinjam (P4).
- DB group (`default` / `data`) untuk tabel `catalogs` dan `collectionloanitems`.
- Nilai `type` OneSearch selain `AllFields` (`Title`, `Author`, `Subject`, `ISN`). Cukup dicoba sekali di browser.
- Kecepatan query item-item di data riil. Kalau lebih dari sekitar 1 detik saat cache kosong,
  baru pertimbangkan batch terjadwal (dan saat itu perlu bantuan mentor untuk memasang jadwalnya).
