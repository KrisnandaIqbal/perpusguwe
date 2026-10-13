-- Pendaftaran yang belum bisa diterima (status "Perlu Perbaikan") dan catatan pemberitahuan email.
-- Dijalankan satu kali setelah registrasi_migration.sql.

-- 1. Status baru
INSERT INTO `status_anggota` (`Nama`, `Branch_id`, `active`)
SELECT 'Perlu Perbaikan', (SELECT `Branch_id` FROM `status_anggota` s WHERE s.`Nama` = 'Menunggu' LIMIT 1), 1
WHERE NOT EXISTS (
    SELECT 1 FROM `status_anggota` WHERE `Nama` = 'Perlu Perbaikan'
);

-- 2. Keputusan admin atas pendaftaran, dan hasil pengiriman email terakhir ke pendaftar.
-- Catatan: kalau muncul error 'Duplicate column name', kolomnya sudah ada dan baris ini dilewati.
ALTER TABLE `members`
    ADD COLUMN `AlasanPerbaikan` VARCHAR(30) NULL DEFAULT NULL AFTER `TglPersetujuan`,
    ADD COLUMN `CatatanPerbaikan` VARCHAR(500) NULL DEFAULT NULL AFTER `AlasanPerbaikan`,
    ADD COLUMN `TglKeputusan` DATETIME NULL DEFAULT NULL AFTER `CatatanPerbaikan`,
    ADD COLUMN `KeputusanBy` INT NULL DEFAULT NULL AFTER `TglKeputusan`,
    ADD COLUMN `NotifJenis` VARCHAR(20) NULL DEFAULT NULL AFTER `KeputusanBy`,
    ADD COLUMN `NotifTerkirim` TINYINT(1) NULL DEFAULT NULL AFTER `NotifJenis`,
    ADD COLUMN `NotifDate` DATETIME NULL DEFAULT NULL AFTER `NotifTerkirim`;
