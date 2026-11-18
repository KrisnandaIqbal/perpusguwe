-- Izin personalisasi rekomendasi anggota (docs/rencana-sistem-rekomendasi.md K4, tiket T8).
-- Jalankan di database 'data'. Idempoten: kolom yang sudah ada dilewati.
-- IzinPersonalisasi: 1 = rekomendasi memakai riwayat pinjam (bawaan), 0 = dimatikan anggota.
-- IzinPersonalisasiDiumumkan: 1 = pemberitahuan login pertama sudah ditampilkan.

SET @db = DATABASE();

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'members' AND COLUMN_NAME = 'IzinPersonalisasi') = 0,
    'ALTER TABLE `members` ADD COLUMN `IzinPersonalisasi` TINYINT(1) NOT NULL DEFAULT 1',
    'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 'members' AND COLUMN_NAME = 'IzinPersonalisasiDiumumkan') = 0,
    'ALTER TABLE `members` ADD COLUMN `IzinPersonalisasiDiumumkan` TINYINT(1) NOT NULL DEFAULT 0',
    'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
