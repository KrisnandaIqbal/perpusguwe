-- Favorit dan Keranjang di dashboard anggota (opac/profil/favorit dan opac/profil/keranjang).
-- Jalankan di database 'data'. Idempoten: tabel yang sudah ada dilewati. Tidak mengubah tabel INLISLite yang ada.
-- member_id = members.ID, catalog_id = catalogs.ID (level judul, bukan eksemplar).

CREATE TABLE IF NOT EXISTS `member_favorit` (
    `ID`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`  INT NOT NULL,
    `catalog_id` INT NOT NULL,
    `CreateDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `uq_member_favorit` (`member_id`, `catalog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `member_keranjang` (
    `ID`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `member_id`  INT NOT NULL,
    `catalog_id` INT NOT NULL,
    `CreateDate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`ID`),
    UNIQUE KEY `uq_member_keranjang` (`member_id`, `catalog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
