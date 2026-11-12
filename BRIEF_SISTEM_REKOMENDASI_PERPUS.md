# Brief: Sistem Rekomendasi & Pencarian Perpustakaan (Hybrid)

> Catatan untuk Claude Code: brief ini berisi konsep dan rancangan yang **stack-agnostic**.
> Sebelum menulis kode, **pelajari dulu struktur repo, stack, ORM, dan konvensi yang sudah ada**,
> lalu sesuaikan nama tabel, folder, dan endpoint di bawah dengan proyek ini. Jangan membuat ulang
> bagian yang sudah ada (misalnya tabel anggota/koleksi/peminjaman). Perluas saja.
> Kalau ada yang bertentangan dengan kode yang ada, tanyakan dulu sebelum mengubah skema.

---

## 1. Tujuan

Menambahkan dua fitur ke OPAC/sistem perpustakaan:

1. **Pencarian cerdas.** Kalau buku yang dicari **tidak dimiliki** perpustakaan, sistem tetap
   mengenali bukunya (lewat metadata eksternal), lalu merekomendasikan koleksi milik perpus yang
   mirip (pengarang, subjek, klasifikasi DDC). Pencarian yang gagal dicatat sebagai **usulan pengadaan**.
2. **Rekomendasi personal di beranda**, berdasarkan riwayat peminjaman, prodi/angkatan, dan kemiripan konten.

Konsepnya meniru pendekatan Netflix: **hybrid recommender** (content-based + collaborative + popularitas),
lalu diurutkan dengan satu fungsi skor gabungan.

---

## 2. Konsep inti

| Komponen | Peran |
|---|---|
| **Content-based** | Kemiripan item dari metadata: DDC (hierarkis), subjek, pengarang, penerbit, embedding abstrak. Jadi tulang punggung karena data peminjaman perpus relatif sedikit (sparse). |
| **Collaborative (item-item)** | "Yang meminjam X juga meminjam Y", dari riwayat peminjaman, reservasi, dan klik. |
| **Popularitas per prodi/angkatan** | Untuk cold start user baru. |
| **Entity resolution** | Mencocokkan query ke metadata eksternal (Open Library / Google Books via ISBN/judul) untuk buku yang tidak dimiliki. |
| **Hybrid ranker** | Menggabungkan skor dengan bobot dinamis. |
| **Diversifier** | Mengurangi bias popularitas (buku wajib mata kuliah tidak boleh mendominasi). |

**Rumus skor (awal, bobot bisa di-tuning):**

```
skor = w1·content + w2·collab + w3·populer_prodi + w4·tersedia − penalti_sudah_dipinjam
```

**Bobot dinamis (switching/weighted):**
- User dengan 0 pinjaman → populer di prodi & angkatan
- User dengan 1–4 pinjaman → dominan content-based
- User dengan ≥5 pinjaman → dominan collaborative
- Item baru/jarang dipinjam → dominan content; item sering dipinjam → collaborative dapat bobot lebih

---

## 3. Variabel / data yang diperlukan

**Item:** id_koleksi, ISBN/ISSN, judul, pengarang (+peran), penerbit, tahun terbit, kode DDC,
subjek, kata kunci, abstrak, bahasa, jenis koleksi (buku/jurnal/skripsi/ebook).

**Eksemplar (fisik):** id_eksemplar, barcode, lokasi rak, status (tersedia/dipinjam/rusak).

**Pengguna:** id_anggota, peran (mahasiswa/dosen/umum), prodi, angkatan, izin_personalisasi.

**Interaksi:** peminjaman (tgl pinjam/kembali, jumlah perpanjangan), reservasi, klik hasil pencarian
(posisi, waktu).

**Pencarian:** query, waktu, jumlah hasil, hasil yang diklik, **query dengan 0 hasil**.

---

## 4. Flow

### A. Pencarian
```
Query → simpan log_pencarian → cari katalog lokal
  ├─ ADA → hasil + status eksemplar ─────────────────────────┐
  └─ TIDAK ADA → entity resolution (cache → API eksternal)   │
        ├─ ketemu → ambil pengarang/subjek/DDC               │
        └─ tidak  → fallback kemiripan teks (embedding)      │
        → cari koleksi milik perpus yang mirip               │
        → catat/increment usulan_pengadaan                   │
                                                             ▼
                        Hybrid ranker → tampilkan ("Belum tersedia, coba ini…" / "Buku terkait")
                        → klik disimpan ke log_klik (feedback)
```

### B. Rekomendasi beranda
```
Login → hitung riwayat pinjam → pilih bobot (cold start / content / collab)
→ kumpulkan kandidat (mirip yang pernah dipinjam, co-borrow, populer prodi)
→ filter (sudah dipinjam, bahasa, jenis tak relevan)
→ ranking + diversifikasi
→ kelompokkan jadi baris UI berlabel alasan:
   "Karena kamu meminjam …", "Populer di <prodi>", "Subjek: …"
```

### C. Batch offline (terjadwal, misalnya tiap malam)
- Hitung matriks kemiripan item (collab) dari peminjaman + klik
- Bangun/perbarui embedding item dari judul + subjek + DDC + abstrak
- Agregasi pencarian 0 hasil → usulan_pengadaan
- Simpan hasil ke kemiripan_item, item_embedding, rekomendasi_cache

---

## 5. Skema data (ERD ringkas)

Sesuaikan dengan tabel yang sudah ada. Tabel bertanda * kemungkinan besar **baru**.

| Tabel | Kolom utama | Relasi |
|---|---|---|
| prodi | id_prodi, nama_prodi, fakultas | 1–N anggota |
| anggota | id_anggota, id_prodi, nama, peran, angkatan, izin_personalisasi* | N–1 prodi |
| koleksi | id_koleksi, isbn_issn, judul, tahun_terbit, id_penerbit, kode_ddc, bahasa, jenis, abstrak | inti |
| eksemplar | id_eksemplar, id_koleksi, kode_barcode, lokasi_rak, status | N–1 koleksi |
| pengarang | id_pengarang, nama | N–N koleksi via koleksi_pengarang (peran) |
| subjek | id_subjek, nama_subjek | N–N koleksi via koleksi_subjek |
| penerbit | id_penerbit, nama | 1–N koleksi |
| klasifikasi_ddc | kode_ddc, nama_kelas, kode_induk (self-ref, hierarki) | 1–N koleksi |
| peminjaman | id_pinjam, id_anggota, id_eksemplar, tgl_pinjam, tgl_kembali, jumlah_perpanjang | anggota, eksemplar |
| reservasi | id_reservasi, id_anggota, id_koleksi, tgl_reservasi, status | anggota, koleksi |
| log_pencarian* | id_log, id_anggota (nullable), query, waktu, jumlah_hasil, id_meta_ext (nullable) | anggota |
| log_klik* | id_klik, id_log, id_koleksi, posisi_hasil, waktu | log_pencarian, koleksi |
| metadata_eksternal* | id_meta_ext, isbn, judul, pengarang_teks, subjek_teks, kode_ddc_perkiraan, sumber, diambil_pada | cache API |
| usulan_pengadaan* | id_usulan, id_meta_ext (nullable), query_normal, jumlah_permintaan, pertama_dicari, terakhir_dicari, status (baru/ditinjau/dibeli/ditolak) | metadata_eksternal |
| item_embedding* | id_koleksi (PK), vektor, versi_model, diperbarui | 1–1 koleksi |
| kemiripan_item* | id_koleksi_a, id_koleksi_b (PK gabungan), skor_content, skor_collab, diperbarui | koleksi |
| rekomendasi_cache* | id_anggota, jenis_baris, id_koleksi (PK gabungan), skor, alasan, dibuat_pada | anggota, koleksi |

Prinsip desain:
- **Koleksi ≠ eksemplar.** Rekomendasi di level judul; ketersediaan dan rak di level fisik.
- **DDC hierarkis** (kode_induk) supaya bisa menghitung jarak 005.13 → 005.1 → 005.
- **metadata_eksternal = cache**, supaya API tidak dipanggil berulang.

---

## 6. Modul / layanan yang dibutuhkan

Tempatkan sesuai struktur proyek yang ada.

- `catalog_search` untuk pencarian katalog lokal
- `entity_resolver` + `external_metadata` untuk mencocokkan query ke Open Library / Google Books (dengan cache)
- `content_based` untuk kemiripan DDC, subjek, pengarang, dan embedding
- `collaborative` untuk item-item dari peminjaman/klik
- `popularity` untuk populer per prodi/angkatan
- `hybrid_ranker` untuk menggabungkan skor dengan bobot dinamis
- `diversifier` untuk membatasi item terlalu populer per baris
- `ddc` util untuk jarak hierarki DDC
- `privacy` util untuk anonimisasi ID saat pelatihan/batch
- Job batch: build_item_similarity, build_embeddings, aggregate_failed_search, scheduler

**Endpoint (usulan):**
- `GET /search?q=` → hasil + saran jika tidak ada
- `GET /recommend/home` → baris-baris rekomendasi beranda
- `GET /recommend/item/{id}` → buku terkait di halaman detail
- `POST /feedback/click` → catat klik
- `GET /usulan-pengadaan` → daftar untuk pustakawan (role khusus)

**Komponen UI (OPAC):** BookCard (judul, sampul, status, lokasi rak), RecRow (baris + label alasan),
NotFoundSuggest ("Buku ini belum tersedia, coba ini…").

---

## 7. Privasi & etika

- Riwayat peminjaman itu sensitif. Rekomendasi berbasis riwayat **hanya ditampilkan ke anggota itu sendiri**.
- Hormati `izin_personalisasi = false`: hanya pakai rekomendasi berbasis prodi + konten.
- Anonimkan ID saat data dipakai untuk batch/pelatihan.
- log_pencarian boleh anonim (id_anggota nullable).

---

## 8. Tahapan pengerjaan

1. **Tahap 1 (MVP):** content-based (DDC + subjek + pengarang) + populer per prodi; logging pencarian & klik.
2. **Tahap 2:** entity resolution + metadata eksternal + usulan pengadaan.
3. **Tahap 3:** collaborative item-item dari peminjaman; hybrid ranker dengan bobot dinamis.
4. **Tahap 4:** embedding abstrak, diversifikasi, cache rekomendasi via batch.
5. **Evaluasi:** precision@k / recall@k offline dari data peminjaman historis (split berdasarkan waktu).

## 9. Kriteria selesai (per tahap)

- Pencarian judul yang tidak dimiliki menampilkan ≥1 saran relevan **atau** fallback yang jelas.
- Setiap pencarian 0 hasil menambah/increment baris di usulan_pengadaan.
- Beranda user baru tetap menampilkan rekomendasi (populer prodi).
- Setiap baris rekomendasi punya label alasan.
- Buku yang sudah dipinjam user tidak direkomendasikan ulang.
- Ada unit test untuk ranker, util DDC, dan entity resolver (API eksternal di-mock).
