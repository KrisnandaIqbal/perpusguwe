-- ==============================================================
-- Migrasi Opsional: Pembaruan Istilah Magang Biasa ke Magang Reguler
-- Target: Database inlislite_v33 (Mentor / Server Production)
-- Dibuat: 22 Oktober 2026
-- ==============================================================

-- 1. Perbarui nama Jenis Anggota di tabel `jenis_anggota`
UPDATE `jenis_anggota`
SET `jenisanggota` = 'Magang Reguler'
WHERE `jenisanggota` = 'Magang Biasa';

-- 2. (Opsional) Jika mentor ingin nilai enum di database juga berubah menjadi 'magang_reguler':
-- Langkah A: Tambahkan 'magang_reguler' ke definisi ENUM
ALTER TABLE `members` 
MODIFY COLUMN `status_non_asn` ENUM('staf', 'magang', 'magang_nasional', 'magang_biasa', 'magang_reguler') NULL DEFAULT NULL;

-- Langkah B: Migrasikan data yang sudah tersimpan dengan 'magang_biasa' ke 'magang_reguler'
UPDATE `members` 
SET `status_non_asn` = 'magang_reguler' 
WHERE `status_non_asn` = 'magang_biasa';

-- Catatan:
-- Aplikasi telah dibuat kompatibel untuk membaca baik 'magang_biasa' maupun 'magang_reguler'.
-- Keduanya akan ditampilkan ke pengguna sebagai "Magang Reguler".
