-- Pemetaan lokasi koleksi SLiMS ke INLISLite
-- IMPORT KE DATABASE INLISLITE TUJUAN.
-- Hanya koleksi hasil migrasi SLiMS yang diproses.
-- Lokasi ruang kosong dan lokasi perpustakaan di luar mapping
-- yang tersedia dibiarkan tidak berubah.

START TRANSACTION;

-- 1. Lokasi Ruang
-- SLiMS item.site -> INLISLite collections.Location_id
-- Alias Rak Lingkar 5 dipetakan ke Rak Lingkar Nomor 5.

UPDATE collections AS c
JOIN perpustakaan_kemnaker.item AS i
  ON c.NomorBarcode = i.item_code
JOIN locations AS r
  ON r.CreateTerminal = 'SLIMS_MIGRATION'
 AND r.Name = CASE
      WHEN i.site = 'Rak Lingkar 5' THEN 'Rak Lingkar Nomor 5'
      ELSE i.site
    END
SET c.Location_id = r.ID
WHERE c.CreateTerminal = 'SLIMS_MIGRATION'
  AND i.site IS NOT NULL
  AND TRIM(i.site) <> '';

-- 2. Lokasi Perpustakaan
-- SLiMS item.location_id -> INLISLite collections.Location_Library_id
-- SL -> PUSAT; DP, L6, KD dipetakan berdasarkan Code.

UPDATE collections AS c
JOIN perpustakaan_kemnaker.item AS i
  ON c.NomorBarcode = i.item_code
JOIN location_library AS ll
  ON ll.Code = CASE
       WHEN i.location_id = 'SL' THEN 'PUSAT'
       WHEN i.location_id IN ('DP', 'L6', 'KD') THEN i.location_id
     END
SET c.Location_Library_id = ll.ID
WHERE c.CreateTerminal = 'SLIMS_MIGRATION'
  AND i.location_id IN ('SL', 'DP', 'L6', 'KD');

COMMIT;
