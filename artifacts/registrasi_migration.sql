-- SQL Migration Script untuk Pendaftaran Anggota

-- 1. Create tabel master_unit jika belum ada
CREATE TABLE IF NOT EXISTS `master_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_unit` varchar(255) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `CreateDate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insert Data Unit Kerja (Sesuai Kemnaker) jika belum ada
INSERT INTO `master_unit` (`nama_unit`, `CreateDate`)
SELECT * FROM (
    SELECT 'Kementerian Ketenagakerjaan Republik Indonesia' AS nama_unit, NOW() AS CreateDate UNION ALL
    SELECT 'Sekretariat Jenderal', NOW() UNION ALL
    SELECT 'Inspektorat Jenderal', NOW() UNION ALL
    SELECT 'Barenbang', NOW() UNION ALL
    SELECT 'Binalavotas', NOW() UNION ALL
    SELECT 'Binapenta & PKK', NOW() UNION ALL
    SELECT 'PHI - JSK', NOW() UNION ALL
    SELECT 'Binwasnaker & K3', NOW()
) AS tmp
WHERE NOT EXISTS (
    SELECT 1 FROM `master_unit` LIMIT 1
);

-- 3. Tambahkan kolom TglPersetujuan di tabel members (jalankan jika belum ada kolomnya)
-- Catatan: Jika menggunakan MySQL/MariaDB dan error 'Duplicate column name', lewati baris ini.
ALTER TABLE `members` ADD COLUMN `TglPersetujuan` DATETIME NULL DEFAULT NULL AFTER `RegisterDate`;

-- 4. Tambahkan master data Jenis Identitas 'NIP' jika belum ada
INSERT INTO `master_jenis_identitas` (`Nama`, `IsNIK`, `active`, `CreateDate`)
SELECT 'NIP', 0, 1, NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `master_jenis_identitas` WHERE `Nama` = 'NIP'
) LIMIT 1;

-- 5. Tambahkan Jenis Anggota 'ASN' dan 'Non ASN' (Hanya 2 ini yang aktif)
UPDATE `jenis_anggota` SET `active` = 1 WHERE `jenisanggota` IN ('ASN', 'Non ASN');
UPDATE `jenis_anggota` SET `active` = 0 WHERE `jenisanggota` NOT IN ('ASN', 'Non ASN');

INSERT INTO `jenis_anggota` (`jenisanggota`, `MasaBerlakuAnggota`, `UploadDokumenKeanggotaanOnline`, `MaxPinjamKoleksi`, `MaxLoanDays`, `active`, `CreateDate`)
SELECT * FROM (SELECT 'ASN', 1000, 1, 5, 14, 1, NOW()) AS tmp
WHERE NOT EXISTS (
    SELECT jenisanggota FROM `jenis_anggota` WHERE jenisanggota = 'ASN'
) LIMIT 1;

INSERT INTO `jenis_anggota` (`jenisanggota`, `MasaBerlakuAnggota`, `UploadDokumenKeanggotaanOnline`, `MaxPinjamKoleksi`, `MaxLoanDays`, `active`, `CreateDate`)
SELECT * FROM (SELECT 'Non ASN', 365, 1, 3, 7, 1, NOW()) AS tmp
WHERE NOT EXISTS (
    SELECT jenisanggota FROM `jenis_anggota` WHERE jenisanggota = 'Non ASN'
) LIMIT 1;

INSERT INTO `jenis_anggota` (`jenisanggota`, `MasaBerlakuAnggota`, `UploadDokumenKeanggotaanOnline`, `MaxPinjamKoleksi`, `MaxLoanDays`, `active`, `CreateDate`)
SELECT * FROM (SELECT 'Magang', 180, 1, 2, 7, 1, NOW()) AS tmp
WHERE NOT EXISTS (
    SELECT jenisanggota FROM `jenis_anggota` WHERE jenisanggota = 'Magang'
) LIMIT 1;


