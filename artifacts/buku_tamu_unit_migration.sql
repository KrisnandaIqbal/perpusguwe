-- Buku tamu: kolom unit asal pengunjung dan rombongan (satuan kerja, unit, atau instansi di luar Kemnaker).
-- Nomor WhatsApp memakai kolom HP yang sudah ada.
--
-- Bisa langsung di-import di phpMyAdmin (pilih database dulu, lalu tab Import).
-- Aman dijalankan lebih dari sekali: kolom yang sudah ada dilewati.

SET @db = DATABASE();

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'memberguesses' AND COLUMN_NAME = 'UnitKerja_id') = 0,
              'ALTER TABLE memberguesses ADD COLUMN UnitKerja_id INT NULL AFTER Alamat',
              'SELECT 1');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'memberguesses' AND COLUMN_NAME = 'SubUnitKerja_id') = 0,
              'ALTER TABLE memberguesses ADD COLUMN SubUnitKerja_id INT NULL AFTER UnitKerja_id',
              'SELECT 1');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'memberguesses' AND COLUMN_NAME = 'UnitLainnya') = 0,
              'ALTER TABLE memberguesses ADD COLUMN UnitLainnya VARCHAR(150) NULL AFTER SubUnitKerja_id',
              'SELECT 1');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

-- Rombongan Kemnaker: satuan kerja dan unit penanggung jawab. Nama unit atau instansi tetap ditulis di AsalInstansi.
SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'groupguesses' AND COLUMN_NAME = 'UnitKerja_id') = 0,
              'ALTER TABLE groupguesses ADD COLUMN UnitKerja_id INT NULL AFTER AsalInstansi',
              'SELECT 1');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS
               WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'groupguesses' AND COLUMN_NAME = 'SubUnitKerja_id') = 0,
              'ALTER TABLE groupguesses ADD COLUMN SubUnitKerja_id INT NULL AFTER UnitKerja_id',
              'SELECT 1');
PREPARE st FROM @sql; EXECUTE st; DEALLOCATE PREPARE st;

SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @db
  AND ((TABLE_NAME = 'memberguesses' AND COLUMN_NAME IN ('UnitKerja_id', 'SubUnitKerja_id', 'UnitLainnya', 'HP'))
    OR (TABLE_NAME = 'groupguesses' AND COLUMN_NAME IN ('UnitKerja_id', 'SubUnitKerja_id')));
