-- =====================================================================
-- Rapikan subjek koleksi (kartu Trello: "Rapikan subjek dan buat
-- judul/subjek koleksi bisa diklik").
--
-- Subjek tersimpan di DUA tempat dan keduanya harus sama:
--   1. catalogs.Subject           -> "Subjek A; Subjek B"
--   2. catalog_ruas (Tag 600)     -> "$a Subjek A; Subjek B"
-- Kalau hanya catalogs.Subject yang diubah, perbaikan akan tertimpa lagi
-- saat katalog disunting dari modul Katalog.
--
-- Penggantian dilakukan per elemen subjek utuh (dibatasi "; "), jadi
-- "Pengadaan baran" tidak ikut mengubah "Pengadaan barang".
-- Hanya memakai fungsi yang ada di MySQL 5.7/8 dan MariaDB.
--
-- Cara pakai:
--   1. Jalankan di database dummy dulu, cek bagian VERIFIKASI di bawah.
--   2. Jalankan di produksi. Tabel cadangan dibuat otomatis (langkah 0).
--   3. Jika OPAC memakai cache (is_opac_cache=1), bersihkan cache.
-- Aman dijalankan ulang: baris yang sudah benar tidak cocok lagi.
-- =====================================================================

-- ---------------------------------------------------------------------
-- Langkah 0. Cadangan (hapus manual setelah hasil dinyatakan benar)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS bak_catalogs_subject_20261105 AS
    SELECT ID, Subject FROM catalogs;
CREATE TABLE IF NOT EXISTS bak_catalog_ruas_600_20261105 AS
    SELECT ID, CatalogId, Value FROM catalog_ruas WHERE Tag = '600';

-- Rollback bila perlu:
-- UPDATE catalogs c JOIN bak_catalogs_subject_20261105 b ON b.ID = c.ID
--    SET c.Subject = b.Subject;
-- UPDATE catalog_ruas r JOIN bak_catalog_ruas_600_20261105 b ON b.ID = r.ID
--    SET r.Value = b.Value;

START TRANSACTION;

-- ---------------------------------------------------------------------
-- Langkah 1. Seragamkan pemisah antarsubjek menjadi "; "
-- ---------------------------------------------------------------------
UPDATE catalogs
   SET Subject = REPLACE(REPLACE(Subject, '; ', ';'), ';', '; ')
 WHERE Subject REGEXP ';[^ ]';

UPDATE catalog_ruas
   SET Value = REPLACE(REPLACE(Value, '; ', ';'), ';', '; ')
 WHERE Tag = '600' AND Value REGEXP ';[^ ]';

-- ---------------------------------------------------------------------
-- Langkah 2. Pemisah sub-subjek: " - " menjadi " -- " (standar tajuk
-- subjek). " - " tidak mungkin muncul di dalam " -- ", jadi aman diulang.
-- ---------------------------------------------------------------------
UPDATE catalogs
   SET Subject = REPLACE(Subject, ' - ', ' -- ')
 WHERE Subject LIKE '% - %';

UPDATE catalog_ruas
   SET Value = REPLACE(Value, ' - ', ' -- ')
 WHERE Tag = '600' AND Value LIKE '% - %';

-- ---------------------------------------------------------------------
-- Langkah 3. Typo
-- ---------------------------------------------------------------------

-- "Pengadaan baran" -> "Pengadaan barang"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Pengadaan baran; ', '; Pengadaan barang; ')))
 WHERE LOCATE('; Pengadaan baran; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Pengadaan baran; ', '; Pengadaan barang; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Pengadaan baran; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Organsisasi, Efektivitas" -> "Organisasi, Efektivitas"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Organsisasi, Efektivitas; ', '; Organisasi, Efektivitas; ')))
 WHERE LOCATE('; Organsisasi, Efektivitas; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Organsisasi, Efektivitas; ', '; Organisasi, Efektivitas; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Organsisasi, Efektivitas; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Ibu dan anak -- Pelayanan Kesehtan -- Undang-undan" -> "Ibu dan anak -- Pelayanan kesehatan -- Undang-undang dan peraturan"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Ibu dan anak -- Pelayanan Kesehtan -- Undang-undan; ', '; Ibu dan anak -- Pelayanan kesehatan -- Undang-undang dan peraturan; ')))
 WHERE LOCATE('; Ibu dan anak -- Pelayanan Kesehtan -- Undang-undan; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Ibu dan anak -- Pelayanan Kesehtan -- Undang-undan; ', '; Ibu dan anak -- Pelayanan kesehatan -- Undang-undang dan peraturan; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Ibu dan anak -- Pelayanan Kesehtan -- Undang-undan; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Hak milih tanah" -> "Hak milik tanah"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Hak milih tanah; ', '; Hak milik tanah; ')))
 WHERE LOCATE('; Hak milih tanah; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Hak milih tanah; ', '; Hak milik tanah; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Hak milih tanah; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Kementrian Ketenagakerjaan" -> "Kementerian Ketenagakerjaan"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Kementrian Ketenagakerjaan; ', '; Kementerian Ketenagakerjaan; ')))
 WHERE LOCATE('; Kementrian Ketenagakerjaan; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Kementrian Ketenagakerjaan; ', '; Kementerian Ketenagakerjaan; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Kementrian Ketenagakerjaan; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Industri pariwisasta" -> "Industri pariwisata"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Industri pariwisasta; ', '; Industri pariwisata; ')))
 WHERE LOCATE('; Industri pariwisasta; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Industri pariwisasta; ', '; Industri pariwisata; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Industri pariwisasta; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Teknologi industr" -> "Teknologi industri"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Teknologi industr; ', '; Teknologi industri; ')))
 WHERE LOCATE('; Teknologi industr; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Teknologi industr; ', '; Teknologi industri; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Teknologi industr; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Pelayanan kesehatan -- Undangundang dan peraturan" -> "Pelayanan kesehatan -- Undang-undang dan peraturan"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Pelayanan kesehatan -- Undangundang dan peraturan; ', '; Pelayanan kesehatan -- Undang-undang dan peraturan; ')))
 WHERE LOCATE('; Pelayanan kesehatan -- Undangundang dan peraturan; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Pelayanan kesehatan -- Undangundang dan peraturan; ', '; Pelayanan kesehatan -- Undang-undang dan peraturan; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Pelayanan kesehatan -- Undangundang dan peraturan; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- ---------------------------------------------------------------------
-- Langkah 4. Tanda baca nyasar di akhir subjek
-- ---------------------------------------------------------------------

-- "Perundang-undangan," -> "Perundang-undangan"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Perundang-undangan,; ', '; Perundang-undangan; ')))
 WHERE LOCATE('; Perundang-undangan,; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Perundang-undangan,; ', '; Perundang-undangan; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Perundang-undangan,; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Undang-undang dan peraturan>" -> "Undang-undang dan peraturan"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Undang-undang dan peraturan>; ', '; Undang-undang dan peraturan; ')))
 WHERE LOCATE('; Undang-undang dan peraturan>; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Undang-undang dan peraturan>; ', '; Undang-undang dan peraturan; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Undang-undang dan peraturan>; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- ---------------------------------------------------------------------
-- Langkah 5. Duplikat beda penulisan (digabung ke bentuk yang paling banyak dipakai)
-- ---------------------------------------------------------------------

-- "Al-Quran" -> "Al Quran"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Al-Quran; ', '; Al Quran; ')))
 WHERE LOCATE('; Al-Quran; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Al-Quran; ', '; Al Quran; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Al-Quran; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Undang Undang" -> "Undang-undang"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Undang Undang; ', '; Undang-undang; ')))
 WHERE LOCATE('; Undang Undang; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Undang Undang; ', '; Undang-undang; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Undang Undang; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Warganegara" -> "Warga Negara"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Warganegara; ', '; Warga Negara; ')))
 WHERE LOCATE('; Warganegara; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Warganegara; ', '; Warga Negara; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Warganegara; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "kerjasama" -> "Kerja Sama"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; kerjasama; ', '; Kerja Sama; ')))
 WHERE LOCATE('; kerjasama; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; kerjasama; ', '; Kerja Sama; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; kerjasama; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Hukum -- Agraria" -> "Hukum Agraria"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Hukum -- Agraria; ', '; Hukum Agraria; ')))
 WHERE LOCATE('; Hukum -- Agraria; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Hukum -- Agraria; ', '; Hukum Agraria; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Hukum -- Agraria; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

-- "Bahan Pustaka -- Pemeliharaan dan perbaikan" -> "Bahan pustaka -- Pemeliharaan dan perbaikan"
UPDATE catalogs
   SET Subject = TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
                 REPLACE(CONCAT('; ', Subject, '; '), '; Bahan Pustaka -- Pemeliharaan dan perbaikan; ', '; Bahan pustaka -- Pemeliharaan dan perbaikan; ')))
 WHERE LOCATE('; Bahan Pustaka -- Pemeliharaan dan perbaikan; ', CONCAT('; ', Subject, '; ')) > 0;
UPDATE catalog_ruas
   SET Value = CONCAT('$a ', TRIM(LEADING '; ' FROM TRIM(TRAILING '; ' FROM
               REPLACE(CONCAT('; ', SUBSTRING(Value, 4), '; '), '; Bahan Pustaka -- Pemeliharaan dan perbaikan; ', '; Bahan pustaka -- Pemeliharaan dan perbaikan; '))))
 WHERE Tag = '600' AND Value LIKE '$a %'
   AND LOCATE('; Bahan Pustaka -- Pemeliharaan dan perbaikan; ', CONCAT('; ', SUBSTRING(Value, 4), '; ')) > 0;

COMMIT;

-- =====================================================================
-- VERIFIKASI (semua harus 0)
-- =====================================================================
-- Subjek MARC tidak sama dengan catalogs.Subject:
-- SELECT COUNT(*) FROM catalogs c
--   JOIN catalog_ruas r ON r.CatalogId = c.ID AND r.Tag = '600'
--  WHERE r.Value <> CONCAT('$a ', c.Subject);
--
-- Masih ada " - " tunggal atau pemisah tanpa spasi:
-- SELECT COUNT(*) FROM catalogs WHERE Subject LIKE '% - %' OR Subject REGEXP ';[^ ]';
--
-- Masih ada subjek lama:
-- SELECT COUNT(*) FROM catalogs
--  WHERE CONCAT('; ', Subject, '; ') REGEXP '; (Pengadaan baran|Organsisasi|Hak milih|Kementrian|Industri pariwisasta|Teknologi industr|Perundang-undangan,|Al-Quran|Undang Undang|Warganegara|kerjasama); ';
