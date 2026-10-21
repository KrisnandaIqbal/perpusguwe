-- Migration: Menambahkan kolom target_expire_date pada tabel jenis_anggota
-- Digunakan untuk masa berlaku serentak (berdasarkan tanggal tetap, misal Magang Nasional)
ALTER TABLE jenis_anggota 
ADD COLUMN target_expire_date DATE NULL DEFAULT NULL AFTER MasaBerlakuAnggota;

-- Inisialisasi Magang Nasional (ID 25) sesuai pengaturan saat ini (111 hari dari 2026-10-21 -> 2027-02-09)
UPDATE jenis_anggota 
SET target_expire_date = '2027-02-09' 
WHERE LOWER(jenisanggota) = 'magang nasional' AND (target_expire_date IS NULL OR target_expire_date = '');
