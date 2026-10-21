-- Bersihkan catatan sumbangan dan pelanggaran milik anggota yang datanya sudah dihapus.
--
-- Kenapa: catatan ini mencantumkan ID anggota yang sudah tidak ada. Kalau ID itu dipakai lagi oleh
-- pendaftar baru, catatan lama ikut tampil di profil pendaftar baru tersebut.
--
-- Bisa langsung di-import di phpMyAdmin (pilih database dulu, lalu tab Import), atau ditempel di tab SQL.
-- Aman dijalankan lebih dari sekali: kalau sudah bersih, tidak ada yang berubah.
--
-- Catatan yang dihapus disalin dulu ke tabel cadangan:
--   sumbangan_cadangan_tanpa_anggota
--   pelanggaran_cadangan_tanpa_anggota
-- Tabel cadangan boleh dihapus kalau sudah yakin tidak dibutuhkan.
--
-- Catatan peminjaman (collectionloans, collectionloanitems) sengaja TIDAK disentuh karena dipakai
-- Laporan Sirkulasi; menghapusnya akan mengubah angka laporan lama.

-- 1. Cadangan
CREATE TABLE IF NOT EXISTS sumbangan_cadangan_tanpa_anggota LIKE sumbangan;
INSERT IGNORE INTO sumbangan_cadangan_tanpa_anggota
SELECT s.* FROM sumbangan s LEFT JOIN members m ON m.ID = s.Member_id
WHERE s.Member_id IS NOT NULL AND m.ID IS NULL;

CREATE TABLE IF NOT EXISTS pelanggaran_cadangan_tanpa_anggota LIKE pelanggaran;
INSERT IGNORE INTO pelanggaran_cadangan_tanpa_anggota
SELECT p.* FROM pelanggaran p LEFT JOIN members m ON m.ID = p.Member_id
WHERE p.Member_id IS NOT NULL AND m.ID IS NULL;

-- 2. Hapus
DELETE s FROM sumbangan s LEFT JOIN members m ON m.ID = s.Member_id
WHERE s.Member_id IS NOT NULL AND m.ID IS NULL;

DELETE p FROM pelanggaran p LEFT JOIN members m ON m.ID = p.Member_id
WHERE p.Member_id IS NOT NULL AND m.ID IS NULL;

-- 3. Hasil: jumlah catatan yang tersimpan di cadangan, dan sisa yang belum bersih (harus 0)
SELECT 'sumbangan' AS tabel,
       (SELECT COUNT(*) FROM sumbangan_cadangan_tanpa_anggota) AS di_cadangan,
       (SELECT COUNT(*) FROM sumbangan s LEFT JOIN members m ON m.ID = s.Member_id
         WHERE s.Member_id IS NOT NULL AND m.ID IS NULL) AS sisa
UNION ALL
SELECT 'pelanggaran',
       (SELECT COUNT(*) FROM pelanggaran_cadangan_tanpa_anggota),
       (SELECT COUNT(*) FROM pelanggaran p LEFT JOIN members m ON m.ID = p.Member_id
         WHERE p.Member_id IS NOT NULL AND m.ID IS NULL);
