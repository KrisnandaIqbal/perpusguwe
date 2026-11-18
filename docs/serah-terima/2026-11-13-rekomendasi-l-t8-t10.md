# Serah terima: sistem rekomendasi OPAC, L dan T8–T10

Tanggal: 13 November 2026. Cabang: `feat/footer-publik-dan-navbar-polos`. Lanjutan dari `2026-11-12-rekomendasi-t1-t7.md`. Belum ada commit.

## Sudah selesai

| Tiket | Isi | File utama |
|---|---|---|
| L | Login, logout, profil anggota. `session('session_member')` = `member_id`, `member_no`, `nama`, `unit_kerja_id`. Tombol Masuk/Profil di navbar publik | `Opac/Controllers/Anggota.php`, `Opac/Views/masuk.php`, `profil.php`, `Opac/Config/Routes.php`, `Config/Filters.php`, `navbar_publik.php` |
| T8 | SQL 2 kolom `members`, pemberitahuan login pertama (sekali, lewat flashdata), tombol nyala/mati di profil. Mengubah izin menghapus cache beranda anggota itu | `artifacts/20261113_add_izin_personalisasi_to_members.sql`, `Anggota::simpanIzin()`, `Opac/Views/index.php` |
| T9 | Beranda anggota: "Karena kamu meminjam …", "Populer di <unit kerja>", "Subjek favoritmu: …", "Koleksi baru di bidang …". Bobot dinamis, dedup antarbaris lewat `Ranker::halaman()`, cache 1 jam per anggota | `Opac::getBarisBeranda()`, `getBarisAnggota()`, `barisSerupa()`, `Libraries/Beranda.php`, `KatalogModel` (`kandidatPopuler` per unit, `kandidatBaru` per kelas, `riwayatAnggota`, `kandidatSubjek`) |
| T10 | Evaluasi offline: split waktu, precision@k dan recall@k, bandingkan set bobot, `--grid` untuk mencari bobot | `Libraries/Evaluasi.php`, `app/Commands/EvaluasiRekomendasi.php` |

### Keputusan di L

Login anggota **tidak** memakai session Myth Auth (`logged_in`). Akun anggota hanya mengisi `session_member`, jadi filter `session` dan `permissions` tetap menutup halaman admin. Kata sandi dicek dengan `Myth\Auth\Password::verify` terhadap tabel `users` (username = nomor anggota), lalu `members.MemberNo` harus ada. Tautan "Masuk ke dasbor" untuk petugas ada di halaman masuk.

Cara pakai evaluasi:

```bash
php spark rekomendasi:evaluasi --hari 90 --k 10 --grid
```

## Sudah dicoba di DB dev (curl, server PHP bawaan)

- Login salah ditolak, login benar mengisi `session_member`, profil tanpa login diarahkan ke halaman masuk.
- Dengan `session_member` aktif: `/dashboard`, `/anggota`, `/api/anggota`, `/api/katalog` tetap dialihkan ke `/login`.
- Pemberitahuan izin muncul sekali, tidak muncul di muat ulang.
- Izin dimatikan di profil: beranda langsung berubah (hanya "Koleksi baru"). Dinyalakan lagi: tiga baris personal kembali.
- Unit test: `BerandaTest` (7), `RekomendasiTest` (17), `OneSearchTest` (12), `UsulanOtomatisTest` (16) lulus. Dijalankan dengan PHP Laragon (`C:\laragon\bin\php\php-8.3.36-Win32-vs16-x64\php.exe vendor/bin/phpunit tests/unit/<berkas>`), tanpa bootstrap sementara, jadi catatan "bootstrap gagal" di serah terima T1–T7 hanya berlaku untuk PHP terminal bawaan.
- `rekomendasi:evaluasi` berjalan sampai selesai (data dev cuma 4 pinjaman, hasil 0 semua).

Akun uji di DB dev: user id 298, username `26102800006` (anggota id 58), kata sandi `ujianggota1`. Dibuat langsung lewat SQL, bukan bagian dari kode. Hapus kalau tidak dipakai.

## Belum dicoba atau belum terbukti

- **Produksi:** SQL T8 belum dijalankan, hanya mentor yang bisa. **Jalankan SQL sebelum kode ini dipasang**, kalau tidak login anggota dan beranda anggota error karena kolom belum ada.
- **Akun anggota produksi:** grup `anggota` tidak ada di DB dev, jadi tidak dipakai sebagai syarat login. Belum dicek apakah semua anggota produksi punya baris di `users`.
- **T10 belum menghasilkan angka berarti.** Syarat selesai ("ada angka precision@k sebelum dan sesudah penyetelan bobot") baru terpenuhi setelah perintah di atas dijalankan di data pinjam produksi bersama mentor. Bobot di `Ranker` belum diubah.
- **Baris "Populer di <unit kerja>"** kosong di DB dev, karena judul yang pernah dipinjam anggota uji dibuang dan pinjaman hanya 4 baris. Pergantian ke populer umum saat unit punya < 10 pinjaman setahun (`KatalogModel::MIN_PINJAMAN_UNIT`) sudah ditulis tapi belum diuji dengan data.
- **Tampilan:** halaman masuk, profil, dan pemberitahuan belum dilihat di browser (tidak ada tangkapan layar, belum dicek di 320 px dan tema gelap). Audit antislop belum ditulis.
- **Tidak ada pembatasan percobaan login** (rate limit) di halaman masuk anggota, dan form tidak memakai token CSRF karena filter `csrf` mati untuk seluruh aplikasi.
- Anggota yang `EndDate`-nya lewat atau berstatus tidak aktif tetap bisa masuk. Belum ada aturan di rencana.
- Akun anggota masih bisa masuk lewat `/login` (login petugas) dan sampai ke `/dashboard`, karena `dashboard*` dikecualikan dari filter `permissions`. Perilaku lama, tidak diubah.
- Evaluasi memakai popularitas umum (bukan per unit kerja) dan kandidat seluruh katalog OPAC per anggota. Di data produksi besar, pakai `--maks` untuk membatasi waktu.
