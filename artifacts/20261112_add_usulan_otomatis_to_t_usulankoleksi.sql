-- Usulan pengadaan otomatis dari pencarian 0 hasil dan klik OneSearch (docs/rekomendasi-metode-flow-workplan.md, tiket T5).
-- Jalankan di database 'data'. Idempoten: kolom dan indeks yang sudah ada dilewati.
-- Baris lama (usulan manual) mendapat source = 'manual' lewat DEFAULT.

SET @db = DATABASE();

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 't_usulankoleksi' AND COLUMN_NAME = 'source') = 0,
    "ALTER TABLE `t_usulankoleksi` ADD COLUMN `source` VARCHAR(20) NOT NULL DEFAULT 'manual'",
    'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 't_usulankoleksi' AND COLUMN_NAME = 'query_normal') = 0,
    'ALTER TABLE `t_usulankoleksi` ADD COLUMN `query_normal` VARCHAR(255) NULL DEFAULT NULL',
    'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 't_usulankoleksi' AND COLUMN_NAME = 'request_count') = 0,
    'ALTER TABLE `t_usulankoleksi` ADD COLUMN `request_count` INT UNSIGNED NOT NULL DEFAULT 0',
    'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 't_usulankoleksi' AND COLUMN_NAME = 'onesearch_click_count') = 0,
    'ALTER TABLE `t_usulankoleksi` ADD COLUMN `onesearch_click_count` INT UNSIGNED NOT NULL DEFAULT 0',
    'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 't_usulankoleksi' AND COLUMN_NAME = 'last_searched_at') = 0,
    'ALTER TABLE `t_usulankoleksi` ADD COLUMN `last_searched_at` DATETIME NULL DEFAULT NULL',
    'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;

SET @sql = IF((SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = @db AND TABLE_NAME = 't_usulankoleksi' AND INDEX_NAME = 'idx_usulan_source_query') = 0,
    'ALTER TABLE `t_usulankoleksi` ADD KEY `idx_usulan_source_query` (`source`, `query_normal`)',
    'SELECT 1');
PREPARE s FROM @sql; EXECUTE s; DEALLOCATE PREPARE s;
