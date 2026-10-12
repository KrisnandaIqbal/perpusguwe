-- Menambah anggota contoh "bob" (pendaftar online yang sudah disetujui).
-- Aman dijalankan berulang: baris hanya ditambah kalau belum ada.
-- Jalankan di database inlislite_v33 (hasil impor cekk.sql).

INSERT INTO `members`
    (`MemberNo`, `Fullname`, `AddressNow`, `Phone`, `NoHp`, `Email`,
     `IdentityType_id`, `IdentityNo`, `JenisAnggota_id`, `UnitKerja_id`, `StatusAnggota_id`,
     `RegisterDate`, `TglPersetujuan`, `EndDate`, `CreateDate`, `UpdateDate`, `active`)
SELECT
    '26101200004', 'bob', 'Jl. SCBD', '0888888887777', '0888888887777', 'abcdefg@gmail.com',
    1, '3333333333333333', 23, 1, 3,
    '2026-10-12 10:06:30', '2026-10-12 10:07:14', '2027-10-12 00:00:00', '2026-10-12 10:06:30', '2026-10-12 10:07:14', 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `members` WHERE `IdentityNo` = '3333333333333333');

-- Nomor urut anggota terakhir yang sudah terpakai, supaya nomor berikutnya lanjut dari 00005.
INSERT INTO `settingparameters` (`Name`, `Value`, `CreateDate`)
SELECT 'NomorAnggotaCounter', '4', NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `settingparameters` WHERE `Name` = 'NomorAnggotaCounter');

UPDATE `settingparameters`
SET `Value` = GREATEST(CAST(`Value` AS UNSIGNED), 4)
WHERE `Name` = 'NomorAnggotaCounter';
