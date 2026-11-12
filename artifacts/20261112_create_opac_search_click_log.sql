-- Log pencarian dan klik OPAC untuk sistem rekomendasi (docs/rekomendasi-metode-flow-workplan.md, tiket T3).
-- Jalankan di database 'data' (sama dengan tabel catalogs dan members). Idempoten.
-- Data yang disimpan: kata kunci, kolom pencarian, jumlah hasil, member_id (hanya kalau anggota login).
-- Tidak menyimpan IP, user agent, atau nama.

CREATE TABLE IF NOT EXISTS `opac_search_log` (
    `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`    INT NULL,
    `query`        VARCHAR(255) NOT NULL,
    `search_by`    VARCHAR(20) NULL,
    `result_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_search_created` (`created_at`),
    KEY `idx_search_member` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `opac_click_log` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `search_log_id` BIGINT UNSIGNED NULL,
    `catalog_id`    INT NULL,
    `target`        ENUM('katalog','onesearch') NOT NULL DEFAULT 'katalog',
    `position`      SMALLINT UNSIGNED NULL,
    `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_click_search` (`search_log_id`),
    KEY `idx_click_catalog` (`catalog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
