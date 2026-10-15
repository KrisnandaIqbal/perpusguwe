-- ==============================================================================
-- Migration: Tabel Master Sub-Unit Kerja dan Penambahan Kolom di Tabel Members
-- Tanggal  : 2026-10-15
-- Catatan  : TANPA DATA ANGGOTA ASLI. Aman dijalankan berulang (Idempotent).
-- ==============================================================================

-- 1. Buat Tabel master_subunit
CREATE TABLE IF NOT EXISTS `master_subunit` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `unit_id` INT NOT NULL,
    `nama_subunit` VARCHAR(255) NOT NULL,
    `sort` INT NOT NULL DEFAULT 0,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `CreateDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `UpdateDate` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_unit_id` (`unit_id`),
    KEY `idx_active` (`active`),
    CONSTRAINT `fk_subunit_master_unit` FOREIGN KEY (`unit_id`) REFERENCES `master_unit` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tambah Kolom SubUnitKerja_id di Tabel members (Opsi B)
-- Kolom diletakkan tepat setelah UnitKerja_id
SET @dbname = DATABASE();
SET @tablename = "members";
SET @columnname = "SubUnitKerja_id";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `members` ADD COLUMN `SubUnitKerja_id` INT NULL DEFAULT NULL AFTER `UnitKerja_id`;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Tambah index untuk SubUnitKerja_id di members jika belum ada
SET @indexname = "idx_members_subunit";
SET @prepIndex = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (index_name = @indexname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `members` ADD INDEX `idx_members_subunit` (`SubUnitKerja_id`);"
));
PREPARE addIndexIfNotExists FROM @prepIndex;
EXECUTE addIndexIfNotExists;
DEALLOCATE PREPARE addIndexIfNotExists;

-- 3. Data Awal Sub-Unit Kerja (Sesuai ejaan baku resmi tanpa singkatan/tambahan)

-- [Unit 2: Sekretariat Jenderal]
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Biro Perencanaan dan Manajemen Kinerja', 1, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Biro Perencanaan dan Manajemen Kinerja');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Biro Keuangan dan Barang Milik Negara', 2, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Biro Keuangan dan Barang Milik Negara');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Biro Organisasi dan Sumber Daya Manusia Aparatur', 3, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Biro Organisasi dan Sumber Daya Manusia Aparatur');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Biro Hukum', 4, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Biro Hukum');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Biro Umum', 5, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Biro Umum');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Biro Kerja Sama', 6, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Biro Kerja Sama');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Biro Hubungan Masyarakat', 7, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Biro Hubungan Masyarakat');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Pusat Pengembangan Sumber Daya Manusia Ketenagakerjaan', 8, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Pusat Pengembangan Sumber Daya Manusia Ketenagakerjaan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Pusat Pasar Kerja', 9, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Pusat Pasar Kerja');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 2, 'Politeknik Ketenagakerjaan', 10, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=2 AND `nama_subunit`='Politeknik Ketenagakerjaan');

-- [Unit 3: Inspektorat Jenderal]
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 3, 'Sekretariat Inspektorat Jenderal', 1, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=3 AND `nama_subunit`='Sekretariat Inspektorat Jenderal');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 3, 'Inspektorat I', 2, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=3 AND `nama_subunit`='Inspektorat I');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 3, 'Inspektorat II', 3, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=3 AND `nama_subunit`='Inspektorat II');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 3, 'Inspektorat III', 4, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=3 AND `nama_subunit`='Inspektorat III');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 3, 'Inspektorat IV', 5, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=3 AND `nama_subunit`='Inspektorat IV');

-- [Unit 4: Barenbang]
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 4, 'Sekretariat Badan Perencanaan dan Pengembangan Ketenagakerjaan', 1, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=4 AND `nama_subunit`='Sekretariat Badan Perencanaan dan Pengembangan Ketenagakerjaan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 4, 'Pusat Perencanaan Ketenagakerjaan', 2, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=4 AND `nama_subunit`='Pusat Perencanaan Ketenagakerjaan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 4, 'Pusat Data dan Teknologi Informasi Ketenagakerjaan', 3, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=4 AND `nama_subunit`='Pusat Data dan Teknologi Informasi Ketenagakerjaan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 4, 'Pusat Pengembangan Kebijakan Ketenagakerjaan', 4, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=4 AND `nama_subunit`='Pusat Pengembangan Kebijakan Ketenagakerjaan');

-- [Unit 5: Binalavotas]
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 5, 'Sekretariat Direktorat Jenderal Pembinaan Pelatihan Vokasi dan Produktivitas', 1, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=5 AND `nama_subunit`='Sekretariat Direktorat Jenderal Pembinaan Pelatihan Vokasi dan Produktivitas');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 5, 'Direktorat Bina Standardisasi Kompetensi dan Program Pelatihan', 2, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=5 AND `nama_subunit`='Direktorat Bina Standardisasi Kompetensi dan Program Pelatihan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 5, 'Direktorat Bina Kelembagaan Pelatihan Vokasi', 3, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=5 AND `nama_subunit`='Direktorat Bina Kelembagaan Pelatihan Vokasi');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 5, 'Direktorat Bina Instruktur dan Tenaga Pelatihan', 4, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=5 AND `nama_subunit`='Direktorat Bina Instruktur dan Tenaga Pelatihan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 5, 'Direktorat Bina Penyelenggaraan Pelatihan Vokasi dan Pemagangan', 5, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=5 AND `nama_subunit`='Direktorat Bina Penyelenggaraan Pelatihan Vokasi dan Pemagangan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 5, 'Direktorat Bina Peningkatan Produktivitas', 6, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=5 AND `nama_subunit`='Direktorat Bina Peningkatan Produktivitas');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 5, 'Sekretariat Badan Nasional Sertifikasi Profesi', 7, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=5 AND `nama_subunit`='Sekretariat Badan Nasional Sertifikasi Profesi');

-- [Unit 6: Binapenta & PKK]
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 6, 'Sekretariat Direktorat Jenderal', 1, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=6 AND `nama_subunit`='Sekretariat Direktorat Jenderal');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 6, 'Direktorat Penempatan Tenaga Kerja', 2, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=6 AND `nama_subunit`='Direktorat Penempatan Tenaga Kerja');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 6, 'Direktorat Penempatan Tenaga Kerja Khusus', 3, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=6 AND `nama_subunit`='Direktorat Penempatan Tenaga Kerja Khusus');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 6, 'Direktorat Perluasan Kesempatan Kerja', 4, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=6 AND `nama_subunit`='Direktorat Perluasan Kesempatan Kerja');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 6, 'Direktorat Pengendalian Penggunaan Tenaga Kerja Asing', 5, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=6 AND `nama_subunit`='Direktorat Pengendalian Penggunaan Tenaga Kerja Asing');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 6, 'Direktorat Pengantar Kerja', 6, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=6 AND `nama_subunit`='Direktorat Pengantar Kerja');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 6, 'Balai Besar Pengembangan Pasar Kerja dan Perluasan', 7, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=6 AND `nama_subunit`='Balai Besar Pengembangan Pasar Kerja dan Perluasan');

-- [Unit 7: PHI - JSK]
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 7, 'Sekretariat Direktorat Jenderal', 1, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=7 AND `nama_subunit`='Sekretariat Direktorat Jenderal');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 7, 'Direktorat Hubungan Kerja dan Pengupahan', 2, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=7 AND `nama_subunit`='Direktorat Hubungan Kerja dan Pengupahan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 7, 'Direktorat Bina Mediator Hubungan Industrial', 3, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=7 AND `nama_subunit`='Direktorat Bina Mediator Hubungan Industrial');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 7, 'Direktorat Jaminan Sosial Tenaga Kerja dan Fasilitasi Kesejahteraan Pekerja', 4, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=7 AND `nama_subunit`='Direktorat Jaminan Sosial Tenaga Kerja dan Fasilitasi Kesejahteraan Pekerja');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 7, 'Direktorat Kelembagaan dan Pencegahan Perselisihan Hubungan Industrial', 5, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=7 AND `nama_subunit`='Direktorat Kelembagaan dan Pencegahan Perselisihan Hubungan Industrial');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 7, 'Direktorat Penyelesaian Perselisihan Hubungan Industrial', 6, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=7 AND `nama_subunit`='Direktorat Penyelesaian Perselisihan Hubungan Industrial');

-- [Unit 8: Binwasnaker & K3]
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 8, 'Sekretariat Direktorat Jenderal', 1, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=8 AND `nama_subunit`='Sekretariat Direktorat Jenderal');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 8, 'Direktorat Bina Sistem Pengawasan Ketenagakerjaan', 2, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=8 AND `nama_subunit`='Direktorat Bina Sistem Pengawasan Ketenagakerjaan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 8, 'Direktorat Bina Kelembagaan Keselamatan dan Kesehatan Kerja', 3, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=8 AND `nama_subunit`='Direktorat Bina Kelembagaan Keselamatan dan Kesehatan Kerja');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 8, 'Direktorat Bina Pemeriksaan Norma Ketenagakerjaan', 4, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=8 AND `nama_subunit`='Direktorat Bina Pemeriksaan Norma Ketenagakerjaan');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 8, 'Direktorat Bina Pengujian Keselamatan dan Kesehatan Kerja', 5, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=8 AND `nama_subunit`='Direktorat Bina Pengujian Keselamatan dan Kesehatan Kerja');
INSERT INTO `master_subunit` (`unit_id`, `nama_subunit`, `sort`, `active`)
SELECT 8, 'Direktorat Bina Pengawas Ketenagakerjaan dan Penguji Keselamatan dan Kesehatan Kerja', 6, 1 WHERE NOT EXISTS (SELECT 1 FROM `master_subunit` WHERE `unit_id`=8 AND `nama_subunit`='Direktorat Bina Pengawas Ketenagakerjaan dan Penguji Keselamatan dan Kesehatan Kerja');
