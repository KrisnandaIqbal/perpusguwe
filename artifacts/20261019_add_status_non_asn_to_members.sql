-- ==============================================================================
-- Migrasi: Penambahan Kolom status_non_asn pada tabel members
-- Tanggal: 19 Oktober 2026
-- Keterangan:
-- Menambahkan kolom `status_non_asn` ENUM('staf', 'magang') NULL DEFAULT NULL
-- setelah kolom `SubUnitKerja_id`. Nilai NULL berlaku untuk ASN dan data lama.
-- ==============================================================================

-- 1. Tambah kolom status_non_asn jika belum ada
SET @dbname = DATABASE();
SET @tablename = "members";
SET @columnname = "status_non_asn";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  "SELECT 1",
  "ALTER TABLE `members` ADD COLUMN `status_non_asn` ENUM('staf', 'magang') NULL DEFAULT NULL AFTER `SubUnitKerja_id`;"
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ==============================================================================
-- ROLLBACK SCRIPT (Jalankan perintah berikut jika ingin membatalkan migrasi):
-- ==============================================================================
-- ALTER TABLE `members` DROP COLUMN `status_non_asn`;
