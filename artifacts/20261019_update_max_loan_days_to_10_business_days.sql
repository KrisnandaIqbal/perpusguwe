-- ============================================================================
-- Migrasi: Update MaxLoanDays menjadi 10 hari kerja untuk semua jenis anggota
-- Tanggal: 2026-10-19
-- Perhatian: File ini hanya draft migrasi. JANGAN dijalankan sebelum disetujui.
-- ============================================================================

-- MIGRATION:
-- Update MaxLoanDays menjadi 10 untuk seluruh jenis anggota (Pegawai, Magang, ASN, Non ASN)
UPDATE jenis_anggota 
SET MaxLoanDays = 10 
WHERE id IN (19, 20, 21, 22, 23, 24);

-- ============================================================================
-- ROLLBACK:
-- Mengembalikan durasi MaxLoanDays ke nilai sebelumnya:
-- - ASN dan Pegawai (id: 19, 22) kembali ke 14 hari
-- - Non ASN dan Magang (id: 20, 21, 23, 24) kembali ke 7 hari
-- ============================================================================

-- ROLLBACK:
-- UPDATE jenis_anggota SET MaxLoanDays = 14 WHERE id IN (19, 22);
-- UPDATE jenis_anggota SET MaxLoanDays = 7  WHERE id IN (20, 21, 23, 24);
