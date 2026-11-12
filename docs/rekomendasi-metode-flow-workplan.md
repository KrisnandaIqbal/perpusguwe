# Rekomendasi OPAC: metode, flow, dan work plan

> Lanjutan dari `docs/rencana-sistem-rekomendasi.md` (keputusan K1–K8 ada di sana).
> Dokumen ini menjawab tiga hal: metode apa yang dipakai, bagaimana alurnya, dan urutan pengerjaannya.
> Status: **untuk dibaca**. Belum ada kode yang diubah.

---

## 1. Jawaban singkat

**Bisa pakai pendekatan Netflix dari brief, tetapi versi ringannya.** Konsep yang sama dipakai:
- gabungan beberapa sumber skor (hybrid),
- bobot yang berubah menurut seberapa banyak data anggota,
- beranda berupa baris-baris berlabel alasan.

Yang tidak dipakai adalah bagian yang butuh data jutaan pengguna atau tim data khusus.

| Konsep Netflix | Versi perpusguwe | Dipakai? |
|---|---|---|
| Beranda berupa baris, tiap baris punya judul alasan | Baris "Karena kamu meminjam …", "Populer di <unit kerja>", dll. | **Ya** |
| "Because you watched X" (kemiripan antar-item) | "Karena kamu meminjam X": item-item dari data pinjam + kemiripan isi | **Ya** |
| Tag/genre hasil kurasi manusia | **DDC + subjek** yang sudah diisi pustakawan | **Ya**, jadi tulang punggung |
| Trending / Top 10 | "Paling banyak dipinjam", "Populer di <unit kerja>" | **Ya** |
| New releases | "Koleksi baru" (dari `catalogs.CreateDate`) | **Ya** |
| Satu judul tidak muncul di dua baris sekaligus | Dedup antarbaris | **Ya** |
| Collaborative filtering besar (matrix factorization) | Diganti item-item co-borrow sederhana | Versi kecil |
| Urutan baris dipersonalisasi per orang | Urutan baris tetap | Tidak |
| Deep learning, embedding | Tidak perlu (lihat K7) | Tidak |
| Gambar sampul dipersonalisasi | Tidak relevan | Tidak |
| A/B testing besar-besaran | Diganti log klik + evaluasi offline sederhana | Tidak |

**Kenapa versi ringan cukup, dilihat dari data lokal** (`inlislite_v33`, dicek 2026-11-12):

| Data | Jumlah | Artinya |
|---|---|---|
| Judul (`catalogs`) | 5.582 | Kecil. Semua skor bisa dihitung di PHP tanpa server khusus |
| `DeweyNo` kosong | **1** dari 5.582 | DDC hampir lengkap. Kemiripan isi bisa diandalkan |
| `Subject` kosong | 300 | Sudah ada script pengisinya (`isi_subjek_koleksi_kosong.sql`) |
| Kelas DDC 3 digit yang berbeda | 254 | Cukup bervariasi untuk membedakan topik |
| Kelas terbanyak | 3xx (2.830 judul), terutama 331 ketenagakerjaan | Koleksi terfokus. Pengaman keragaman (diversifier) penting |
| `DeweyNo = 051` (majalah) | 398 | Majalah perlu dibatasi supaya tidak memenuhi rekomendasi |
| Data pinjam lokal | 4 baris | Hanya data uji. **Data pinjam di server produksi belum diketahui** |

Kesimpulannya, **content-based jadi tulang punggung**, dan bagian collaborative akan "menyala sendiri"
saat data pinjam cukup. Cara ini aman, berapa pun jumlah data pinjam di produksi.

---

## 2. Metode yang dipakai: hybrid berbobot

### 2a. Empat sumber skor (semuanya bernilai 0–1)

**1) Content: seberapa mirip isi dua judul**

```
content = 0.50 × ddc + 0.35 × subjek + 0.15 × pengarang
```

- **ddc**: `DeweyNo` dinormalisasi dulu, yaitu titik dan spasi dibuang (`331.110 26` → `33111026`).
  Lalu dihitung panjang awalan yang sama:

  | Awalan sama | Contoh | Skor |
  |---|---|---|
  | ≥ 5 digit | `658.40` vs `658.401 2` | 1.0 |
  | 4 digit | `658.4` vs `658.41` | 0.8 |
  | 3 digit | `658.3` vs `658.4` | 0.6 |
  | 2 digit | `658` vs `651` | 0.3 |
  | 1 digit | `658` vs `600` | 0.1 |
  | 0 | `658` vs `331` | 0 |

- **subjek**: Jaccard dari daftar subjek (dipisah `; `), yaitu jumlah subjek yang sama dibagi jumlah semua subjek.
- **pengarang**: 1 kalau ada nama pengarang yang sama (dipisah `;`), selain itu 0.

**2) Kolab: "yang meminjam X juga meminjam Y"**

```
kolab(X, Y) = c / √(nX × nY)
keyakinan   = min(1, c / 3)
```

- `c` = jumlah anggota berbeda yang pernah meminjam X dan Y. Pasangan dengan `c < 2` diabaikan.
- `nX`, `nY` = jumlah anggota berbeda yang pernah meminjam X dan Y masing-masing.
- `keyakinan` membuat judul yang jarang dipinjam otomatis lebih bergantung pada content (sesuai brief).

**3) Populer**: jumlah peminjam berbeda dalam 365 hari terakhir, dibagi nilai tertinggi di daftar kandidat.
Dihitung per unit kerja. Kalau unit kerjanya punya kurang dari 10 pinjaman, dipakai angka populer umum.

**4) Tersedia**: 1 kalau ada eksemplar berstatus tersedia, selain itu 0.

### 2b. Rumus akhir dan bobot dinamis

```
skor = wc × content + wk × kolab × keyakinan + wp × populer + 0.1 × tersedia
```

| Kondisi | wc | wk | wp | Catatan |
|---|---|---|---|---|
| Halaman detail ("Buku terkait"), semua pengunjung | 0.7 | 0.3 | 0 | Tidak memakai data pribadi |
| Anggota login, 0 pinjaman | 0 | 0 | 1 | Cold start: populer di unit kerja |
| Anggota login, 1–4 pinjaman | 0.6 | 0.2 | 0.2 | Dominan content |
| Anggota login, ≥ 5 pinjaman | 0.35 | 0.45 | 0.2 | Dominan kolab |
| Anggota dengan `IzinPersonalisasi = 0` | 0 | 0 | 1 | Diperlakukan seperti 0 pinjaman |

Angka bobot di atas adalah **titik awal**. Disimpan sebagai konstanta di satu tempat, lalu disetel ulang
setelah evaluasi (T10).

### 2c. Aturan sesudah skor dihitung (filter + diversifier)

1. Buang judul yang **sudah pernah dipinjam** anggota itu.
2. Buang judul dengan `IsOPAC = 0`.
3. Per baris: maksimal **2 judul dari pengarang yang sama**, dan maksimal **1 majalah** (DDC `05x`),
   kecuali judul sumbernya juga majalah.
4. Satu judul **tidak muncul di dua baris** pada halaman yang sama.
5. Baris yang isinya kurang dari 4 judul **tidak ditampilkan**.

### 2d. Baris di beranda OPAC

| Urutan | Tamu (belum login) | Anggota login |
|---|---|---|
| 1 | Paling banyak dipinjam | "Karena kamu meminjam <judul terakhir>" (kalau ada riwayat dan izin aktif) |
| 2 | Koleksi baru | "Populer di <unit kerja>" |
| 3 | | "Subjek favoritmu: <subjek>" (subjek yang paling sering dipinjam) |
| 4 | | "Koleksi baru di bidang <kelas DDC favorit>" |

---

## 3. Flow (ASCII)

### 3a. Gambaran besar

```
                         ┌──────────────────────────────┐
  Pengunjung OPAC ──────►│  Opac (controller yang ada)  │
  (tamu / anggota)       │  index · search · detail     │
                         └──────┬───────────────┬───────┘
                                │               │
                 cari / klik    │               │  minta rekomendasi
                                ▼               ▼
                 ┌────────────────────┐   ┌───────────────────────────────┐
                 │ Log                │   │ Modul Rekomendasi (baru)      │
                 │ opac_search_log    │   │                               │
                 │ opac_click_log     │   │  ContentScore  (DDC, subjek,  │
                 └─────────┬──────────┘   │                 pengarang)    │
                           │              │  CoBorrow      (item-item)    │
         hasil 0 / klik    │              │  Popular       (unit kerja)   │
         OneSearch         ▼              │  Ranker        (bobot, filter,│
                 ┌────────────────────┐   │                 diversifier)  │
                 │ t_usulankoleksi    │   └───────┬───────────────────────┘
                 │ (diperluas)        │           │ baca
                 └─────────┬──────────┘           ▼
                           │              ┌───────────────────────────────┐
                           ▼              │ Tabel yang sudah ada          │
                 Pustakawan melihat       │ catalogs · collections        │
                 daftar usulan (admin)    │ collectionloanitems · members │
                                          └───────────────────────────────┘
                                                  ▲
                                          Cache CI4 24 jam (populer, co-borrow)
```

### 3b. Pencarian

```
 Pengunjung mengetik kata kunci
            │
            ▼
 Opac::search()  ── FULLTEXT / LIKE seperti sekarang
            │
            ├──► simpan opac_search_log (query, search_by, result_count, member_id atau NULL)
            │
            ▼
     ┌─ result_count? ─────────────────────────────────────────────┐
     │                         │                                   │
     ▼ ≥ 5                     ▼ 1–4                               ▼ 0
 tampilkan hasil          tampilkan hasil                    "Buku ini belum ada di
 seperti biasa            + link kecil                        perpustakaan kami."
                          "Cari juga di OneSearch"                 │
                                                                   ├──► tombol "Cari di Indonesia OneSearch"
                                                                   │      klik → api/Opac/click (target=onesearch)
                                                                   │           → tab baru onesearch.id
                                                                   │
                                                                   ├──► "Mungkin kamu tertarik":
                                                                   │      FULLTEXT NATURAL LANGUAGE MODE
                                                                   │      → ambil DDC + subjek hasil teratas
                                                                   │      → ContentScore → Ranker
                                                                   │      → kosong? populer (unit kerja / umum)
                                                                   │
                                                                   └──► catat usulan (lihat 3e)

 Setiap klik judul di hasil → api/Opac/click (target=katalog, position)
```

### 3c. Halaman detail: "Buku terkait"

```
 opac/detail/{id}
       │
       ▼
 Ambil judul X (DeweyNo, Subject, Author)
       │
       ▼
 Kandidat (maks 200):
   DDC 3 digit sama  ∪  subjek sama  ∪  co-borrow dengan X (dari cache)
       │
       ▼
 Untuk tiap kandidat Y:
   content(X,Y), kolab(X,Y) × keyakinan
       │
       ▼
 Ranker  (wc=0.7, wk=0.3) → filter → diversifier → ambil 8 teratas
       │
       ▼
 Kurang dari 4?  ── ya ──► bagian "Buku terkait" tidak ditampilkan
       │ tidak
       ▼
 Tampilkan baris "Buku terkait" (kartu katalog yang sudah ada)
```

### 3d. Beranda anggota yang login

```
 opac/index
     │
     ▼
 session('session_member') ada?
     │
     ├── tidak ──► baris tamu: "Paling banyak dipinjam", "Koleksi baru"
     │
     ▼ ya
 Login pertama dan belum diberi tahu?  ── ya ──► tampilkan pemberitahuan izin (K4)
     │                                            → set IzinPersonalisasiDiumumkan = 1
     ▼
 IzinPersonalisasi = 0 ?  ── ya ──► hanya "Populer di <unit kerja>" + "Koleksi baru"
     │ tidak
     ▼
 Hitung jumlah pinjaman anggota → pilih bobot (tabel 2b)
     │
     ▼
 Bangun baris:
   1. "Karena kamu meminjam <judul terakhir>"  ← co-borrow + content dari judul itu
   2. "Populer di <unit kerja>"                ← Popular
   3. "Subjek favoritmu: <subjek>"             ← subjek yang paling sering dipinjam
   4. "Koleksi baru di bidang <kelas DDC>"     ← CreateDate terbaru di kelas favorit
     │
     ▼
 Ranker per baris → buang yang sudah dipinjam → diversifier → dedup antarbaris
     │
     ▼
 Buang baris yang isinya < 4 → tampilkan
```

### 3e. Usulan pengadaan dari pencarian

```
 Pencarian 0 hasil  /  klik OneSearch
            │
            ▼
 query_normal = lowercase, rapikan spasi, buang tanda baca di ujung
            │
            ▼
 Session ini sudah dihitung untuk query ini hari ini?  ── ya ──► berhenti
            │ tidak
            ▼
 Ada baris t_usulankoleksi (source='pencarian', query_normal sama)?
            │
            ├── tidak ──► INSERT (request_count = 1 atau onesearch_click_count = 1)
            └── ya    ──► request_count + 1  atau  onesearch_click_count + 1
                          last_searched_at = sekarang
            │
            ▼
 Daftar usulan admin menampilkan baris ini hanya kalau
   request_count ≥ 3  ATAU  onesearch_click_count ≥ 2
 (usulan manual selalu tampil)
```

### 3f. Cache (pengganti batch terjadwal)

```
 Ranker butuh data populer / co-borrow
            │
            ▼
 Ada di cache CI4 dan umurnya < 24 jam? ── ya ──► pakai
            │ tidak
            ▼
 Jalankan query SQL → simpan ke cache → pakai
```

Kalau query saat cache kosong ternyata lebih dari sekitar 1 detik di data produksi, baru dibuat
`php spark rekomendasi:build` untuk mengisi cache tiap malam (perlu bantuan mentor untuk jadwalnya).

---

## 4. Work plan

### 4a. Posisi project sekarang

- Branch aktif `feat/footer-publik-dan-navbar-polos` masih punya **banyak perubahan yang belum di-commit**,
  termasuk `UsulanKoleksi` (ganti base class ke `\Base\...`) dan `Opac/Views/member_list.php`.
  **Pekerjaan rekomendasi dimulai setelah branch itu masuk `main`**, supaya tidak bentrok.
- Rekomendasi lama (`Opac::calculateRecommendations`) masih aktif lewat `?member_no=`.
  Route `opac/recommendations` dan `opac/getRecommendations` mengarah ke method yang **tidak ada**.
- **Sudah ada dasar login anggota**: `api/member/login` (`Member\Controllers\Api\Member::login`) memakai Myth Auth
  dengan username = `members.IdentityNo`. Ada juga `register`, `activate`, dan `resend_email`.
  Login anggota di website bisa dibangun di atas ini, tidak dari nol.

### 4b. Aturan kerja

- Satu tiket = satu branch dari `main` (`feat/rekomendasi-tN-<nama>`) = satu PR.
- SQL ditulis di `artifacts/YYYYMMDD_<nama>.sql` (idempoten, pola yang sudah ada). Di lokal dijalankan sendiri,
  di server oleh mentor.
- Verifikasi: `php -l`, PHPUnit untuk logika baru, `curl` / browser untuk halaman. **Playwright tidak dijalankan.**
- Saat mulai dikerjakan, tiket di bawah disalin ke file TODO dengan format yang sama seperti `TODO-2026-10-22.md`.

### 4c. Daftar tiket

Ukuran: **S** = setengah hari, **M** = 1–2 hari, **L** = 3+ hari (perkiraan kasar).

| Tiket | Isi | Bergantung pada | Ukuran |
|---|---|---|---|
| **T0** | Bersih-bersih: matikan `?member_no=` di `Opac::index()`, hapus kode user-based lama, route `recommendations`/`getRecommendations`, dan view `recommendations.php` kalau tidak dipakai | branch footer masuk `main` | S |
| **T1** | Modul `app/Modules/Rekomendasi/` + `ContentScore` (`ddcSimilarity`, Jaccard subjek, pengarang) + `Ranker` (rumus 2b, filter, diversifier 2c). **Hanya logika + unit test**, belum ada tampilan | – | M |
| **T2** | "Buku terkait" di `opac/detail` memakai content saja (wk = 0 dulu). Tracer bullet pertama dari ujung ke ujung | T1 | M |
| **T3** | Log: SQL `opac_search_log` + `opac_click_log`, catat di `Opac::search()`, endpoint `api/Opac/click`, JS kecil di kartu hasil | – | M |
| **T4** | Pencarian 0 hasil: blok "belum ada" + tombol OneSearch (pemetaan `search_by` → `type`) + "Mungkin kamu tertarik". Unit test penyusun URL | T1, T3 | M |
| **T5** | Usulan otomatis: SQL kolom baru `t_usulankoleksi`, aturan 3e, kolom dan filter di daftar admin UsulanKoleksi. Unit test normalisasi + ambang | T3, T4 | M |
| **T6** | Co-borrow (`kolab` + `keyakinan`) + cache 24 jam, dipasang di "Buku terkait" (wk = 0.3). Ukur waktu query di data produksi bersama mentor | T2 | M |
| **T7** | Populer (umum, per unit kerja) + Koleksi baru → baris beranda **tamu** | T1 | S |
| **L** | **Login anggota** (fitur terpisah, prasyarat). Halaman login + logout + profil, menyimpan `session('session_member')` berisi minimal `member_id`. Dibangun di atas `api/member/login` | – | L |
| **T8** | Izin personalisasi: SQL 2 kolom `members`, pemberitahuan login pertama, tombol di profil | L | S |
| **T9** | Beranda anggota: 4 baris berlabel (3d), bobot dinamis, dedup antarbaris | L, T6, T7, T8 | M |
| **T10** | (Opsional) Evaluasi offline: precision@k / recall@k dari data pinjam produksi dengan split waktu, lalu setel bobot | T9, data produksi | M |

### 4d. Urutan dan jalur paralel

```
                    branch footer masuk main
                              │
                              ▼
                             T0
                              │
          ┌───────────────────┼─────────────────────┐
          ▼                   ▼                     ▼
   Jalur A: rekomendasi   Jalur B: pencarian    Jalur C: login
          │                   │                     │
         T1                  T3                     L
        ┌─┴──────┐            │                     │
        ▼        ▼            │                    T8
       T2       T7            │                     │
        │        │      T1 ──►T4                    │
       T6        │            │                     │
        │        │           T5                     │
        └────────┴──────────────────┬───────────────┘
                                    ▼
                                   T9
                                    │
                                    ▼
                                  T10 (opsional)
```

- Jalur A, B, dan C bisa dikerjakan orang yang berbeda, karena file yang disentuh hampir terpisah.
  Titik temunya: T4 butuh T1 (ContentScore), dan T9 butuh ketiga jalur.
- **Yang sudah bisa dirasakan pengguna paling cepat**: T0 → T1 → T2 ("Buku terkait" di halaman detail),
  lalu T3 → T4 (pencarian kosong diarahkan ke OneSearch).
- File yang paling sering disentuh bersama: `Opac/Controllers/Opac.php` dan `Opac/Views/index.php`, `detail.php`.
  Kalau dua orang bekerja paralel, kabari dulu sebelum mengubah bagian yang sama.

### 4e. Definisi selesai per tiket

| Tiket | Selesai kalau |
|---|---|
| T0 | `opac?member_no=...` menampilkan katalog biasa. `grep calculateRecommendations` kosong |
| T1 | Unit test lulus untuk: tabel skor DDC (semua baris di 2a), Jaccard subjek, filter sudah dipinjam, batas 2 per pengarang, batas 1 majalah |
| T2 | Detail judul ber-DDC 658.x menampilkan judul 658.x lain. Judul tanpa kandidat tidak menampilkan baris kosong |
| T3 | Satu pencarian = satu baris `opac_search_log`. Klik judul = satu baris `opac_click_log` |
| T4 | Kata kunci acak menampilkan tombol OneSearch dengan URL yang benar dan baris koleksi mirip kalau ada |
| T5 | Query yang sama dicari 3 kali dari session berbeda muncul di daftar usulan, 2 kali tidak muncul |
| T6 | Waktu respons halaman detail dengan cache kosong tercatat. Cache terisi setelah pemanggilan pertama |
| T7 | Beranda tamu punya 2 baris. Majalah tidak memenuhi baris |
| L | Anggota bisa login/logout. `session_member` berisi `member_id`. **Akun anggota tidak bisa membuka halaman admin** |
| T8 | Pemberitahuan muncul sekali. Tombol di profil mengubah rekomendasi saat itu juga |
| T9 | Semua kriteria di `rencana-sistem-rekomendasi.md` bagian 6 terpenuhi |
| T10 | Ada angka precision@k sebelum dan sesudah penyetelan bobot |

---

## 5. Risiko dan yang belum dicek

- **Data pinjam produksi belum diketahui.** Kalau sedikit, kolab hampir tidak berpengaruh (sudah diantisipasi lewat `keyakinan`).
  Minta mentor menjalankan:
  `SELECT COUNT(*), COUNT(DISTINCT member_id), MIN(LoanDate) FROM collectionloanitems;`
- **Login anggota memakai Myth Auth yang sama dengan admin.** `$auth->attempt()` mengisi session `logged_in`,
  dan key yang sama dipakai filter admin (`app/Filters/Permissions.php`). Saat mengerjakan L, perlu dipastikan
  akun anggota tidak bisa membuka halaman admin. Ini belum aku uji.
- Nilai `type` OneSearch selain `AllFields` masih tebakan dari nilai bawaan VuFind.
- Format `Author` di data lokal kadang berantakan (ada yang isinya hanya `;`). Skor pengarang memakai bobot kecil (0.15),
  jadi dampaknya terbatas.
- Angka bobot dan ambang (3 pencarian, 2 klik, 4 judul per baris) masih tebakan awal dan perlu disetel setelah ada data.

---

## Catatan T6: waktu respons Kolab

- Kolab dihitung di `Opac::kolab()` per judul, di-cache 24 jam lewat `cache()` CI4 (kunci `rekomendasi_kolab_<id>`, handler `file`). Waktu hitung saat cache kosong dicatat di `writable/logs` dengan awalan `[rekomendasi] kolab katalog`.
- Diukur di data lokal (4 pinjaman, belum ada pasangan c ≥ 2), judul 39110: hitung Kolab 2.9 ms saat cache kosong. Respons halaman `opac/detail` 1.18 dtk (cache kosong) dan 1.05 dtk (cache terisi), hampir semuanya waktu halaman biasa. Cache terisi sesudah panggilan pertama; panggilan kedua tidak menulis log lagi.
- **Belum diukur: data produksi.** Untuk mentor: buka `opac/detail/<id>` judul yang banyak dipinjam, saat cache kosong, lalu baca baris log itu. Kalau > ± 1 detik, buat `php spark rekomendasi:build` (lihat 3f). Query memakai `collectionloanitems` yang sudah punya indeks di `member_id` dan `Collection_id`.
