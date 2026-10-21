-- ==============================================================================
-- Migrasi: Penyesuaian Kolom status_non_asn pada tabel members
-- Tanggal: 21 Oktober 2026
-- Keterangan:
-- Menambahkan opsi 'magang_nasional' (6 bulan) dan 'magang_biasa' (1 bulan).
-- Opsi legacy 'magang' dipertahankan di ENUM agar data lama tetap valid.
-- PERINGATAN: Sesuai instruksi, skrip ini TIDAK dijalankan secara otomatis.
-- Menunggu persetujuan user sebelum dieksekusi.
-- ==============================================================================

-- 1. Skrip Migrasi (Jalankan setelah disetujui):
ALTER TABLE `members` 
MODIFY COLUMN `status_non_asn` ENUM('staf', 'magang', 'magang_nasional', 'magang_biasa') NULL DEFAULT NULL;

-- ==============================================================================
-- ROLLBACK SCRIPT (Jalankan perintah berikut jika ingin membatalkan migrasi):
-- ==============================================================================
-- PERHATIAN: Pastikan tidak ada data yang memakai 'magang_nasional' atau 'magang_biasa'
-- sebelum melakukan rollback:
-- UPDATE `members` SET `status_non_asn` = 'magang' WHERE `status_non_asn` IN ('magang_nasional', 'magang_biasa');
--
-- ALTER TABLE `members` 
-- MODIFY COLUMN `status_non_asn` ENUM('staf', 'magang') NULL DEFAULT NULL;
