-- Pemetaan lokasi koleksi SLiMS ke INLISLite
-- Hanya koleksi hasil migrasi; ketersediaan tidak diubah.

START TRANSACTION;

-- Lokasi Ruang: SLiMS item.site -> INLISLite Location_id
UPDATE perpustakaan_inlislite.collections AS c
JOIN perpustakaan_kemnaker.item AS i
  ON c.NomorBarcode = i.item_code
JOIN perpustakaan_inlislite.locations AS r
  ON r.CreateTerminal = 'SLIMS_MIGRATION'
 AND r.Name = CASE
      WHEN i.site = 'Rak Lingkar 5' THEN 'Rak Lingkar Nomor 5'
      ELSE i.site
    END
SET c.Location_id = r.ID
WHERE c.CreateTerminal = 'SLIMS_MIGRATION'
  AND i.site IS NOT NULL
  AND TRIM(i.site) <> '';

-- Lokasi Perpustakaan: SLiMS item.location_id -> INLISLite Location_Library_id
UPDATE perpustakaan_inlislite.collections AS c
JOIN perpustakaan_kemnaker.item AS i
  ON c.NomorBarcode = i.item_code
JOIN perpustakaan_inlislite.location_library AS ll
  ON ll.Code = CASE
       WHEN i.location_id = 'SL' THEN 'PUSAT'
       WHEN i.location_id IN ('DP', 'L6', 'KD') THEN i.location_id
     END
SET c.Location_Library_id = ll.ID
WHERE c.CreateTerminal = 'SLIMS_MIGRATION'
  AND i.location_id IN ('SL', 'DP', 'L6', 'KD');

COMMIT;
