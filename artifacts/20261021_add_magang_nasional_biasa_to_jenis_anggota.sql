-- ==============================================================================
-- Migrasi: Penambahan Jenis Anggota Magang Nasional dan Magang Biasa
-- Tanggal: 21 Oktober 2026
-- Keterangan:
-- Menambahkan jenis anggota:
-- 1. 'Magang Nasional' (MasaBerlaku: 180 hari / 6 bulan, MaxPinjam: 2, MaxLoanDays: 7)
-- 2. 'Magang Biasa' (MasaBerlaku: 30 hari / 1 bulan, MaxPinjam: 2, MaxLoanDays: 7)
-- ==============================================================================

-- 1. Tambahkan 'Magang Nasional' jika belum ada
INSERT INTO `jenis_anggota` (
    `jenisanggota`,
    `MasaBerlakuAnggota`,
    `BiayaPendaftaran`,
    `BiayaPerpanjangan`,
    `MaxPinjamKoleksi`,
    `MaxLoanDays`,
    `UploadDokumenKeanggotaanOnline`,
    `active`,
    `CreateDate`
)
SELECT 'Magang Nasional', 180, 0, 0, 2, 7, 0, 0, NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `jenis_anggota` WHERE LOWER(TRIM(`jenisanggota`)) = 'magang nasional'
);

-- 2. Tambahkan 'Magang Biasa' jika belum ada
INSERT INTO `jenis_anggota` (
    `jenisanggota`,
    `MasaBerlakuAnggota`,
    `BiayaPendaftaran`,
    `BiayaPerpanjangan`,
    `MaxPinjamKoleksi`,
    `MaxLoanDays`,
    `UploadDokumenKeanggotaanOnline`,
    `active`,
    `CreateDate`
)
SELECT 'Magang Biasa', 30, 0, 0, 2, 7, 0, 0, NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `jenis_anggota` WHERE LOWER(TRIM(`jenisanggota`)) = 'magang biasa'
);

-- ==============================================================================
-- ROLLBACK SCRIPT:
-- ==============================================================================
-- DELETE FROM `jenis_anggota` WHERE `jenisanggota` IN ('Magang Nasional', 'Magang Biasa');
