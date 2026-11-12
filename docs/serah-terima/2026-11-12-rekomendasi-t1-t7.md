# Serah terima: sistem rekomendasi OPAC, T1–T7

Tanggal: 12 November 2026. Cabang: `feat/footer-publik-dan-navbar-polos`. Rancangan: `docs/rekomendasi-metode-flow-workplan.md`.

## Sudah selesai

| Tiket | Isi | File utama |
|---|---|---|
| T1 | Hitung kemiripan judul dan susun urutan. Murni logika. | `app/Modules/Rekomendasi/Libraries/ContentScore.php`, `Ranker.php` |
| T2 | "Buku terkait" di halaman detail | `Opac::bukuTerkait()`, `Opac/Views/detail.php` |
| T3 | Log pencarian dan klik | `artifacts/20261112_create_opac_search_click_log.sql`, `Opac::index()`, `Api/Opac::click()` |
| T4 | Pencarian 0 hasil: blok "belum ada", tombol OneSearch, "Mungkin kamu tertarik" | `Libraries/OneSearch.php`, `Opac::loadRujukanTanpaHasil()` |
| T5 | Usulan pengadaan otomatis | `Libraries/UsulanOtomatis.php`, `artifacts/20261112_add_usulan_otomatis_to_t_usulankoleksi.sql`, daftar admin UsulanKoleksi |
| T6 | Kolab (yang meminjam X juga meminjam Y) dan cache 24 jam di "Buku terkait" | `Libraries/Kolab.php` |
| T7 | Baris beranda tamu: "Paling banyak dipinjam" dan "Koleksi baru" | `Opac::getBarisTamu()`, `KatalogModel` |

Batas judul per baris di `Ranker::baris()` diatur 20.

## Sudah dicoba di `perpusguwe-main.test` (DB dev)

- Detail judul menampilkan 8 judul terkait.
- Beranda tamu menampilkan "Koleksi baru".
- Pencarian 0 hasil menampilkan tombol OneSearch dengan URL benar dan 8 judul "Mungkin kamu tertarik" (pakai campuran kata nyata dan kata ngawur).
- Satu pencarian menulis satu baris `opac_search_log`. Klik menulis satu baris `opac_click_log`.
- Kata dicari 3 kali masuk daftar usulan, 2 kali tersembunyi.

## Belum dicoba atau belum terbukti

- **Produksi:** SQL T3 dan T5 belum dijalankan. Hanya mentor yang bisa. Waktu respons "Buku terkait" dengan cache kosong di data produksi juga belum diukur.
- **Baris "Paling banyak dipinjam"** belum tampil di DB dev karena tabel pinjaman hanya berisi 4 baris.
- **Kolab (T6)** belum terbukti memengaruhi urutan. Pasangan dengan c < 2 diabaikan, jadi di data sepi urutan masih ditentukan content saja.
- **Daftar admin UsulanKoleksi** belum dibuka lewat browser (butuh login admin). Aturan ambangnya sudah dicek lewat SQL.
- **Unit test:** bootstrap PHPUnit CI4 gagal di mesin ini (PHP terminal tidak punya MySQL plugin dan `intl`). Test dijalankan dengan bootstrap sementara. Perlu dijalankan sekali di mesin yang sehat: `vendor/bin/phpunit tests/unit/RekomendasiTest.php tests/unit/OneSearchTest.php tests/unit/UsulanOtomatisTest.php`.
- **Kandidat "Mungkin kamu tertarik"** diambil dari 200 judul pertama per kelas DDC tanpa urutan relevansi. Di koleksi produksi hasilnya bisa kurang tajam.

## Catatan teknis yang bikin salah paham

- `app/Config/Autoload.php` menyimpan daftar modul di `writable/cache/routes_list`. Setelah menambah modul baru, hapus file itu, kalau tidak muncul error `Class "Rekomendasi\Libraries\Ranker" not found`.
- Saat menguji dari worktree, jangan jadikan `vendor` satu shortcut utuh ke repo utama. `Config\Filters` akan terbaca dari repo utama, dan pengecualian filter `session` untuk `api/Opac/click` tidak berlaku.

## Belum dikerjakan

- **L: login anggota.** Prasyarat T8 dan T9. Halaman login, logout, profil, `session('session_member')` berisi `member_id`. Akun anggota tidak boleh membuka halaman admin.
- **T8: izin personalisasi.** SQL 2 kolom `members`, pemberitahuan login pertama, tombol di profil.
- **T9: beranda anggota.** 4 baris berlabel, bobot dinamis (`Ranker::BOBOT_COLD_START`, `BOBOT_DOMINAN_CONTENT`, `BOBOT_DOMINAN_KOLAB` sudah ada), dedup antarbaris lewat `Ranker::halaman()`.
- **T10 (opsional): evaluasi offline.** precision@k dan recall@k dari data pinjam produksi, lalu setel bobot.

Urutan yang disarankan: L, T8, T9, T10. T9 butuh L, T6, T7, dan T8.
