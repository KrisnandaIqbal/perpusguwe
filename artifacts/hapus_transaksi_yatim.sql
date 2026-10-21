-- Hapus baris sumbangan dan pelanggaran yang Member_id-nya menunjuk ke anggota yang sudah tidak ada.
-- Baris ini hanya tampil di profil/dashboard per anggota, jadi tanpa anggotanya tidak dipakai,
-- dan bisa nyasar ke anggota baru yang kebetulan mendapat ID yang sama.
--
-- collectionloans dan collectionloanitems sengaja TIDAK disentuh: Laporan Sirkulasi menghitungnya
-- lewat LEFT JOIN members, jadi peminjaman lama tetap masuk statistik walau anggotanya sudah dihapus.
--
-- Cadangan disimpan dulu di tabel *_yatim_backup_20261021. Hapus tabel cadangan itu kalau sudah yakin.

-- 1. Lihat dulu jumlahnya
SELECT 'sumbangan' AS tabel, COUNT(*) AS yatim
FROM sumbangan s LEFT JOIN members m ON m.ID = s.Member_id
WHERE s.Member_id IS NOT NULL AND m.ID IS NULL
UNION ALL
SELECT 'pelanggaran', COUNT(*)
FROM pelanggaran p LEFT JOIN members m ON m.ID = p.Member_id
WHERE p.Member_id IS NOT NULL AND m.ID IS NULL;

-- 2. Cadangan
CREATE TABLE IF NOT EXISTS sumbangan_yatim_backup_20261021 AS
SELECT s.* FROM sumbangan s LEFT JOIN members m ON m.ID = s.Member_id
WHERE s.Member_id IS NOT NULL AND m.ID IS NULL;

CREATE TABLE IF NOT EXISTS pelanggaran_yatim_backup_20261021 AS
SELECT p.* FROM pelanggaran p LEFT JOIN members m ON m.ID = p.Member_id
WHERE p.Member_id IS NOT NULL AND m.ID IS NULL;

-- 3. Hapus
DELETE s FROM sumbangan s LEFT JOIN members m ON m.ID = s.Member_id
WHERE s.Member_id IS NOT NULL AND m.ID IS NULL;

DELETE p FROM pelanggaran p LEFT JOIN members m ON m.ID = p.Member_id
WHERE p.Member_id IS NOT NULL AND m.ID IS NULL;
