-- =====================================================================
-- Script Pengisian Subjek Koleksi yang Masih Kosong
-- Tanggal: 2026-11-05
-- Total data kosong: 300 judul
-- Koleksi riil yang diperbarui: 282 judul
-- Data uji coba / dummy yang dilewati: 18 judul
--
-- Subjek diperbarui di dua tempat (konsisten):
--   1. catalogs.Subject       -> 'Subjek 1; Subjek 2'
--   2. catalog_ruas (Tag 600) -> ' Subjek 1; Subjek 2'
-- =====================================================================

-- Langkah 0: Cadangan (tabel backup)
CREATE TABLE IF NOT EXISTS bak_catalogs_subject_isi_20261105 AS
    SELECT ID, Subject FROM catalogs WHERE Subject IS NULL OR TRIM(Subject) = '';

CREATE TABLE IF NOT EXISTS bak_catalog_ruas_600_isi_20261105 AS
    SELECT ID, CatalogId, Tag, Value FROM catalog_ruas WHERE CatalogId IN (
        SELECT ID FROM catalogs WHERE Subject IS NULL OR TRIM(Subject) = ''
    ) AND Tag = '600';

START TRANSACTION;

-- ID: 41181 | Mengakhiri Era Tenaga Kerja Murah
UPDATE catalogs SET Subject = 'Ketenagakerjaan; Upah -- Tenaga kerja' WHERE ID = 41181 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41181, '600', '#', '#', '$a Ketenagakerjaan; Upah -- Tenaga kerja', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41181 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ketenagakerjaan; Upah -- Tenaga kerja' WHERE CatalogId = 41181 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35906 | Metodologi Penelitian Pedesaan : Koreksi dan Pembenaran
UPDATE catalogs SET Subject = 'Metodologi penelitian; Sosiologi pedesaan' WHERE ID = 35906 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35906, '600', '#', '#', '$a Metodologi penelitian; Sosiologi pedesaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35906 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Metodologi penelitian; Sosiologi pedesaan' WHERE CatalogId = 35906 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41207 | Sekarang Belajar Sekarang Lancar Microsoft Access 2002
UPDATE catalogs SET Subject = 'Komputer; Sistem informasi' WHERE ID = 41207 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41207, '600', '#', '#', '$a Komputer; Sistem informasi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41207 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Komputer; Sistem informasi' WHERE CatalogId = 41207 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35985 | Daftar Tajuk Subyek untuk Perpustakaan: Edisi Ringkas
UPDATE catalogs SET Subject = 'Ilmu perpustakaan; Perpustakaan -- Manajemen' WHERE ID = 35985 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35985, '600', '#', '#', '$a Ilmu perpustakaan; Perpustakaan -- Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35985 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ilmu perpustakaan; Perpustakaan -- Manajemen' WHERE CatalogId = 35985 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36007 | Warta Demografi Ke-29, no 1, 1999
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 36007 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36007, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36007 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 36007 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36008 | Warta Demografi Ke-28, no 2, 1999
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 36008 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36008, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36008 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 36008 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36009 | Warta Demografi Ke-29, no 3, 1999
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 36009 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36009, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36009 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 36009 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36010 | Warta Demografi Ke-29, no 4, 1999
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 36010 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36010, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36010 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 36010 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36622 | Bulletin Berita BAPPENAS (Badan Perentjanaan Pembangunan Nasional) No. 2
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 36622 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36622, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36622 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 36622 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41117 | Dana Bergulir Edisi 168 Tahun XVII April-Juni 2025
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41117 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41117, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41117 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41117 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41122 | Latihan Dasar Kemiliteran Komponen Cadangan (Latsarmil Komcad) : ASN Kementerian Gelombang I TA 2026 SATDIK WINGDIK 500 LANUD ATANG SANDJAJA BOGOR
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41122 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41122, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41122 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41122 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41171 | WIRATA : Warta Informasi Peraturan Perundang-Undangan Bidang Ketenagakerjaan : Sistem Jaringan Dokumentasi Dan Informasi (SJDI) Hukum 1996/1997 No.17/V/I
UPDATE catalogs SET Subject = 'Ketenagakerjaan; Majalah Indonesia' WHERE ID = 41171 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41171, '600', '#', '#', '$a Ketenagakerjaan; Majalah Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41171 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ketenagakerjaan; Majalah Indonesia' WHERE CatalogId = 41171 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41228 | Indonesia Arbitration : Towards A Better Enforcement Of Arbital Awards
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41228 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41228, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41228 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41228 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41232 | MPOWER: Jendela Informasi Ketenagakerjaan Edisi Semester 1-2026
UPDATE catalogs SET Subject = 'Ketenagakerjaan; Majalah Indonesia' WHERE ID = 41232 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41232, '600', '#', '#', '$a Ketenagakerjaan; Majalah Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41232 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ketenagakerjaan; Majalah Indonesia' WHERE CatalogId = 41232 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41236 | Majalah Bank Indonesia : BICARA Edisi 108 Tahun 2025
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41236 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41236, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41236 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41236 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41237 | Majalah Bank Indonesia : BICARA Edisi 107 Tahun 2025
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41237 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41237, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41237 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41237 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41238 | Intisari Agustus 2016 : Hatta The Untold Story
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41238 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41238, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41238 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41238 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41239 | GPriority Jelajah Nusantara : Rencana Pembangunan di Mahulu Disesuaikan dengan Pusat
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41239 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41239, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41239 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41239 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41240 | Newsletter: JGF 2019 From Disarray Towards a New Normal
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41240 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41240, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41240 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41240 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41241 | Newsletter: Program Pendidikan Singkat Angkatan 24 Lemhannas RI Resmi Ditutup
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41241 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41241, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41241 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41241 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41242 | Tempo Edisi 13-19 April 2026 : PT Nasdem Indonesia Raya TBK
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41242 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41242, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41242 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41242 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41243 | Newsletter: Governor of Lemhannas RI, Dr. H. TB. Ace Hasan Syadzily, M.Si. Officially Inaugurated
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41243 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41243, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41243 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41243 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41244 | Majalah Parahyangan Vol. XIII No. 2 2026
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41244 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41244, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41244 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41244 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41245 | Newsletter: Lemhannas RI Support Indonesia to Keep Moving Forward Indonesia
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41245 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41245, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41245 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41245 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41246 | Newsletter: 59th Anniversary Celebration as National Resilience Reactualization
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41246 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41246, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41246 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41246 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41247 | Newsletter  Edisi 185 Desember 2024 : Gubernur Lemhannas RI Sampaikan Rencana Kerja 100 Hari Lemhannas di Rapat Dengar Pendapat DPR RI
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41247 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41247, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41247 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41247 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41248 | Newsletter: Lemhannas RI Visits PT Nexter France to Build international Cooperation
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41248 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41248, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41248 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41248 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41249 | Parlementaria: Meracik Resep Mujarab Penawar Defisit
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41249 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41249, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41249 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41249 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41250 | Tempo : Sepanjang Tahun 2024 16 DPO Imigrasi Bekerjasama dengan Interpol Mengamankan Daftar Pencarian Orang (DPO) Internasional di Indonesia
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41250 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41250, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41250 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41250 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41251 | Majalah Prahyangan: Kampus Berdampak, Apa Harapan Kita?
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41251 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41251, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41251 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41251 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41252 | Kadin Indonesia Edisi Maret 2020: Bersatu Melawan Covid-19
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41252 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41252, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41252 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41252 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41253 | Newsletter: Governor Presents Lemhannas RI's 100-Day-Work-Plan at the Hearing Meeting
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41253 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41253, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41253 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41253 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41254 | Kiprah Volume 118 Tahun XXI Maret 2023 : Torehan Pencapaian 2022 dan Rencana Kerja 2023
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41254 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41254, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41254 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41254 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41256 | Tempo : 9T Imigrasi Setor Ke Kas Negara
UPDATE catalogs SET Subject = 'Majalah Indonesia; Terbitan berkala' WHERE ID = 41256 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41256, '600', '#', '#', '$a Majalah Indonesia; Terbitan berkala', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41256 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Majalah Indonesia; Terbitan berkala' WHERE CatalogId = 41256 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41258 | Indonesian Companies Operating in Vietnam
UPDATE catalogs SET Subject = 'Perusahaan -- Indonesia; Investasi luar negeri -- Vietnam' WHERE ID = 41258 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41258, '600', '#', '#', '$a Perusahaan -- Indonesia; Investasi luar negeri -- Vietnam', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41258 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Perusahaan -- Indonesia; Investasi luar negeri -- Vietnam' WHERE CatalogId = 41258 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35844 | Standar Kompetensi Wartawan
UPDATE catalogs SET Subject = 'Wartawan -- Standar kompetensi; Jurnalisme' WHERE ID = 35844 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35844, '600', '#', '#', '$a Wartawan -- Standar kompetensi; Jurnalisme', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35844 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Wartawan -- Standar kompetensi; Jurnalisme' WHERE CatalogId = 35844 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41205 | The Mystery of 2012 : Prediksi, Ramalan, dan Kemungkinan
UPDATE catalogs SET Subject = 'Ramalan; Eskatologi' WHERE ID = 41205 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41205, '600', '#', '#', '$a Ramalan; Eskatologi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41205 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ramalan; Eskatologi' WHERE CatalogId = 41205 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41192 | Psychological : Testing and Assessment Twelfth Edition
UPDATE catalogs SET Subject = 'Psikologi' WHERE ID = 41192 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41192, '600', '#', '#', '$a Psikologi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41192 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Psikologi' WHERE CatalogId = 41192 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41120 | Segala Sesuatu yang Perlu Anda Ketahui tentang Psikologi
UPDATE catalogs SET Subject = 'Psikologi' WHERE ID = 41120 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41120, '600', '#', '#', '$a Psikologi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41120 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Psikologi' WHERE CatalogId = 41120 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41227 | I'd Like You To Know
UPDATE catalogs SET Subject = 'Psikologi' WHERE ID = 41227 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41227, '600', '#', '#', '$a Psikologi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41227 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Psikologi' WHERE CatalogId = 41227 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41211 | Terapi Rumi : Dari Era Pengetahuan ke Era Kebijaksanaan
UPDATE catalogs SET Subject = 'Psikologi' WHERE ID = 41211 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41211, '600', '#', '#', '$a Psikologi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41211 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Psikologi' WHERE CatalogId = 41211 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 38706 | The secret of self-improvement: detoks hati dan pikiran
UPDATE catalogs SET Subject = 'Pengembangan diri; Motivasi' WHERE ID = 38706 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 38706, '600', '#', '#', '$a Pengembangan diri; Motivasi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 38706 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pengembangan diri; Motivasi' WHERE CatalogId = 38706 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41137 | 99 Tips Cespleng Hidup Sukses ala Karyawan
UPDATE catalogs SET Subject = 'Pengembangan diri; Psikologi terapan' WHERE ID = 41137 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41137, '600', '#', '#', '$a Pengembangan diri; Psikologi terapan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41137 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pengembangan diri; Psikologi terapan' WHERE CatalogId = 41137 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35861 | Mengefektifkan Kinerja : Saran untuk menghadapi 44 jenis orang yang menimbulkan masalah di lingkungan kerja
UPDATE catalogs SET Subject = 'Hubungan antar pribadi di tempat kerja; Pengembangan diri' WHERE ID = 35861 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35861, '600', '#', '#', '$a Hubungan antar pribadi di tempat kerja; Pengembangan diri', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35861 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hubungan antar pribadi di tempat kerja; Pengembangan diri' WHERE CatalogId = 35861 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36106 | Agaman-agama Manusia
UPDATE catalogs SET Subject = 'Agama -- Perbandingan' WHERE ID = 36106 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36106, '600', '#', '#', '$a Agama -- Perbandingan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36106 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Agama -- Perbandingan' WHERE CatalogId = 36106 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36105 | Psikologi Agama
UPDATE catalogs SET Subject = 'Psikologi agama' WHERE ID = 36105 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36105, '600', '#', '#', '$a Psikologi agama', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36105 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Psikologi agama' WHERE CatalogId = 36105 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35975 | Atlas Agama-agama: Mengantarkan Setiap Orang Beragama Lebih Memahami Agama Masing-masing
UPDATE catalogs SET Subject = 'Agama -- Atlas; Agama -- Sejarah' WHERE ID = 35975 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35975, '600', '#', '#', '$a Agama -- Atlas; Agama -- Sejarah', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35975 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Agama -- Atlas; Agama -- Sejarah' WHERE CatalogId = 35975 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41196 | Menjadi Istri Penuh Pesona : 101 Cara Cerdas Menjadi Istri Teladan yang Mungkin Belum Pernah Anda Pikirkan Sebelumnya
UPDATE catalogs SET Subject = 'Keluarga; Hubungan suami istri; Wanita' WHERE ID = 41196 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41196, '600', '#', '#', '$a Keluarga; Hubungan suami istri; Wanita', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41196 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Keluarga; Hubungan suami istri; Wanita' WHERE CatalogId = 41196 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35833 | Zikir 1001 Asma'ul Husna dan Fadhilah Surah Yasin
UPDATE catalogs SET Subject = 'Doa dan Zikir; Asmaul Husna' WHERE ID = 35833 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35833, '600', '#', '#', '$a Doa dan Zikir; Asmaul Husna', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35833 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Doa dan Zikir; Asmaul Husna' WHERE CatalogId = 35833 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35954 | Hukum Pidana Islam
UPDATE catalogs SET Subject = 'Hukum Islam; Fikih' WHERE ID = 35954 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35954, '600', '#', '#', '$a Hukum Islam; Fikih', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35954 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum Islam; Fikih' WHERE CatalogId = 35954 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36185 | Fiqh dan Ushul Fiqh
UPDATE catalogs SET Subject = 'Hukum Islam; Fikih' WHERE ID = 36185 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36185, '600', '#', '#', '$a Hukum Islam; Fikih', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36185 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum Islam; Fikih' WHERE CatalogId = 36185 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35895 | Suamiku Dengarkan Curahan Hatiku
UPDATE catalogs SET Subject = 'Hukum Islam; Fikih' WHERE ID = 35895 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35895, '600', '#', '#', '$a Hukum Islam; Fikih', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35895 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum Islam; Fikih' WHERE CatalogId = 35895 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35896 | Istriku Dengarkan Kata Hatiku
UPDATE catalogs SET Subject = 'Hukum Islam; Fikih' WHERE ID = 35896 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35896, '600', '#', '#', '$a Hukum Islam; Fikih', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35896 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum Islam; Fikih' WHERE CatalogId = 35896 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36092 | Memperbincangkan Dinamika Intelektual dan Pemikiran HAMKA tentang Pendidikan Islam
UPDATE catalogs SET Subject = 'Pendidikan Islam' WHERE ID = 36092 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36092, '600', '#', '#', '$a Pendidikan Islam', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36092 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pendidikan Islam' WHERE CatalogId = 36092 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36102 | Al-Islam & IPTEK (buku kedua)
UPDATE catalogs SET Subject = 'Islam -- Dakwah dan pembinaan' WHERE ID = 36102 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36102, '600', '#', '#', '$a Islam -- Dakwah dan pembinaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36102 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Islam -- Dakwah dan pembinaan' WHERE CatalogId = 36102 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36111 | Al-islam & Iptek
UPDATE catalogs SET Subject = 'Islam -- Dakwah dan pembinaan' WHERE ID = 36111 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36111, '600', '#', '#', '$a Islam -- Dakwah dan pembinaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36111 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Islam -- Dakwah dan pembinaan' WHERE CatalogId = 36111 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36087 | Jejak-jejak Ekonomi Syariah
UPDATE catalogs SET Subject = 'Islam -- Dakwah dan pembinaan' WHERE ID = 36087 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36087, '600', '#', '#', '$a Islam -- Dakwah dan pembinaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36087 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Islam -- Dakwah dan pembinaan' WHERE CatalogId = 36087 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41199 | Rasulullah's Business School - Edisi Revisi
UPDATE catalogs SET Subject = 'Islam -- Dakwah dan pembinaan' WHERE ID = 41199 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41199, '600', '#', '#', '$a Islam -- Dakwah dan pembinaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41199 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Islam -- Dakwah dan pembinaan' WHERE CatalogId = 41199 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35914 | Lintasan Sejarah : Pemikiran Perkembangan Modern dalam Islam
UPDATE catalogs SET Subject = 'Islam -- Dakwah dan pembinaan' WHERE ID = 35914 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35914, '600', '#', '#', '$a Islam -- Dakwah dan pembinaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35914 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Islam -- Dakwah dan pembinaan' WHERE CatalogId = 35914 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35923 | Sejarah Sosial Ummat Islam Bagian Kesatu dan dua
UPDATE catalogs SET Subject = 'Islam -- Sejarah; Sosiologi agama' WHERE ID = 35923 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35923, '600', '#', '#', '$a Islam -- Sejarah; Sosiologi agama', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35923 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Islam -- Sejarah; Sosiologi agama' WHERE CatalogId = 35923 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36096 | Aliran-aliran Moderen dalam Islam
UPDATE catalogs SET Subject = 'Islam -- Pemikiran modern; Mazhab dan aliran Islam' WHERE ID = 36096 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36096, '600', '#', '#', '$a Islam -- Pemikiran modern; Mazhab dan aliran Islam', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36096 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Islam -- Pemikiran modern; Mazhab dan aliran Islam' WHERE CatalogId = 36096 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36084 | Tuhan, Maaf, Kami Sedang Sibuk
UPDATE catalogs SET Subject = 'Etika Islam; Pengembangan diri (Islam)' WHERE ID = 36084 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36084, '600', '#', '#', '$a Etika Islam; Pengembangan diri (Islam)', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36084 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Etika Islam; Pengembangan diri (Islam)' WHERE CatalogId = 36084 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35950 | Teori Sosiologi Modern
UPDATE catalogs SET Subject = 'Sosiologi' WHERE ID = 35950 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35950, '600', '#', '#', '$a Sosiologi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35950 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Sosiologi' WHERE CatalogId = 35950 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41047 | Manusia Satu Dimensi
UPDATE catalogs SET Subject = 'Filsafat manusia; Sosiologi kritis' WHERE ID = 41047 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41047, '600', '#', '#', '$a Filsafat manusia; Sosiologi kritis', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41047 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Filsafat manusia; Sosiologi kritis' WHERE CatalogId = 41047 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35823 | Hari yang Keduapuluh Sembilan
UPDATE catalogs SET Subject = 'Ekologi manusia; Lingkungan hidup' WHERE ID = 35823 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35823, '600', '#', '#', '$a Ekologi manusia; Lingkungan hidup', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35823 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ekologi manusia; Lingkungan hidup' WHERE CatalogId = 35823 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41235 | Terampil Wawancara
UPDATE catalogs SET Subject = 'Ilmu Komunikasi' WHERE ID = 41235 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41235, '600', '#', '#', '$a Ilmu Komunikasi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41235 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ilmu Komunikasi' WHERE CatalogId = 41235 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35940 | Gaya Kepemimpinan Sukarno-Suharto
UPDATE catalogs SET Subject = 'Kepemimpinan' WHERE ID = 35940 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35940, '600', '#', '#', '$a Kepemimpinan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35940 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kepemimpinan' WHERE CatalogId = 35940 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39150 | Pemimpin dan Kepemimpinan: Apakah Kepemimpinan Abnormal Itu?
UPDATE catalogs SET Subject = 'Kepemimpinan' WHERE ID = 39150 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39150, '600', '#', '#', '$a Kepemimpinan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39150 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kepemimpinan' WHERE CatalogId = 39150 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41212 | Excellent Leadership : Rahasia Menjadi Pemimpin Sukses
UPDATE catalogs SET Subject = 'Kepemimpinan' WHERE ID = 41212 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41212, '600', '#', '#', '$a Kepemimpinan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41212 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kepemimpinan' WHERE CatalogId = 41212 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35949 | Revolusi Mental: Pembentukan Karakter Bangsa Indonesia
UPDATE catalogs SET Subject = 'Pendidikan karakter; Kepribadian nasional -- Indonesia' WHERE ID = 35949 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35949, '600', '#', '#', '$a Pendidikan karakter; Kepribadian nasional -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35949 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pendidikan karakter; Kepribadian nasional -- Indonesia' WHERE CatalogId = 35949 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35728 | Peranan Demografi dalam Pembangunan
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35728 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35728, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35728 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35728 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35963 | Hukum dan Perkembangan Sosial: Buku Teks Sosiologi Hukum Buku III
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35963 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35963, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35963 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35963 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35944 | Jangan Biarkan Bumi Merana: Laporan Worldwatch Insitute
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35944 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35944, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35944 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35944 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35953 | Bumi Dalam Keseimbangan: Ekologi dan Semangat Manusia
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35953 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35953, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35953 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35953 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35725 | Dinamika Kependudukan Indonesia Sampai 30 Mendatang dan Implikasinya Pada Tabungan Masyarakat
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35725 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35725, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35725 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35725 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35822 | Kembali di Simpang Jalan : Masalah Kependudukan dengan Sumber Daya Alam
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35822 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35822, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35822 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35822 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35850 | Ruang Lingkup Teori Ekonomi Kependudukan
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35850 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35850, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35850 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35850 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35851 | Beberapa Makalah tentang Masalah Kependudukan dan Program Pendidikan Kependudukan di Lingkungan Pendidikan Islam
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35851 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35851, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35851 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35851 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35852 | Era Pasca Transisi Demografi di Indoneisa
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35852 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35852, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35852 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35852 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35854 | Analisis Migrasi Penduduk Berdasarkan Data Supas 1985 Propinsi Kalimantan Timur
UPDATE catalogs SET Subject = 'Migrasi penduduk -- Indonesia; Demografi' WHERE ID = 35854 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35854, '600', '#', '#', '$a Migrasi penduduk -- Indonesia; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35854 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Migrasi penduduk -- Indonesia; Demografi' WHERE CatalogId = 35854 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35856 | Analisis Migrasi Penduduk Berdasarkan Data Supas 1985 Propinsi Jawa Timur
UPDATE catalogs SET Subject = 'Migrasi penduduk -- Indonesia; Demografi' WHERE ID = 35856 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35856, '600', '#', '#', '$a Migrasi penduduk -- Indonesia; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35856 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Migrasi penduduk -- Indonesia; Demografi' WHERE CatalogId = 35856 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35857 | Analisis Migrasi Penduduk Berdasarkan Data Supas 1985 Propinsi DKI Jakarta
UPDATE catalogs SET Subject = 'Migrasi penduduk -- Indonesia; Demografi' WHERE ID = 35857 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35857, '600', '#', '#', '$a Migrasi penduduk -- Indonesia; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35857 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Migrasi penduduk -- Indonesia; Demografi' WHERE CatalogId = 35857 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35859 | Analisis Migrasi Penduduk Berdasarkan Data Supas 1985 Propinsi Kalimantan
UPDATE catalogs SET Subject = 'Migrasi penduduk -- Indonesia; Demografi' WHERE ID = 35859 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35859, '600', '#', '#', '$a Migrasi penduduk -- Indonesia; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35859 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Migrasi penduduk -- Indonesia; Demografi' WHERE CatalogId = 35859 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35873 | Kecendrungan dan Faktor Penentu Fertilitas & Mortalitas di Indonesia
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35873 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35873, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35873 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35873 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35934 | Variable Demografi Dalam Perencanaan Pembangunan Ekonomi
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35934 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35934, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35934 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35934 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35729 | Faktor-faktor yang Mempengaruhi Struktur Umur Penduduk
UPDATE catalogs SET Subject = 'Kependudukan -- Proyeksi; Demografi -- Indonesia' WHERE ID = 35729 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35729, '600', '#', '#', '$a Kependudukan -- Proyeksi; Demografi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35729 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan -- Proyeksi; Demografi -- Indonesia' WHERE CatalogId = 35729 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35745 | Proyeksi Penduduk Indonesia Tahun 1990 - 2050
UPDATE catalogs SET Subject = 'Kependudukan -- Proyeksi; Demografi -- Indonesia' WHERE ID = 35745 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35745, '600', '#', '#', '$a Kependudukan -- Proyeksi; Demografi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35745 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan -- Proyeksi; Demografi -- Indonesia' WHERE CatalogId = 35745 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35936 | Analisa Perkembangan Kependudukan menurut Sensus penduduk 1990: Dinamika Mortalitas
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 35936 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35936, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35936 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 35936 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39499 | Di atas 40 tahun: kondisi problematik pria wanita
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 39499 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39499, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39499 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 39499 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36003 | Kumpulan Data Kependudukan, Keluarga Berencana, dan Keluarga Sejahtera = Data Collection of Population, Family Planning, and Prosperous Family
UPDATE catalogs SET Subject = 'Kependudukan; Demografi' WHERE ID = 36003 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36003, '600', '#', '#', '$a Kependudukan; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36003 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan; Demografi' WHERE CatalogId = 36003 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39985 | Negara dan Kekerasan Terhadap Perempuan
UPDATE catalogs SET Subject = 'Gender; Wanita -- Masalah sosial' WHERE ID = 39985 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39985, '600', '#', '#', '$a Gender; Wanita -- Masalah sosial', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39985 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Gender; Wanita -- Masalah sosial' WHERE CatalogId = 39985 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35959 | Pengantar Ilmu Antropologi Edisi Revisi 2009
UPDATE catalogs SET Subject = 'Antropologi' WHERE ID = 35959 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35959, '600', '#', '#', '$a Antropologi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35959 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Antropologi' WHERE CatalogId = 35959 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36403 | Dewan Perwakilan Rakyat Republik Indonesia Periode 1977-1982
UPDATE catalogs SET Subject = 'Dewan Perwakilan Rakyat (DPR); Lembaga legislatif -- Indonesia' WHERE ID = 36403 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36403, '600', '#', '#', '$a Dewan Perwakilan Rakyat (DPR); Lembaga legislatif -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36403 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Dewan Perwakilan Rakyat (DPR); Lembaga legislatif -- Indonesia' WHERE CatalogId = 36403 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39496 | Aneka hukum perceraian di Indonesia
UPDATE catalogs SET Subject = 'Perceraian -- Aspek hukum; Perkawinan -- Hukum -- Indonesia' WHERE ID = 39496 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39496, '600', '#', '#', '$a Perceraian -- Aspek hukum; Perkawinan -- Hukum -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39496 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Perceraian -- Aspek hukum; Perkawinan -- Hukum -- Indonesia' WHERE CatalogId = 39496 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41204 | Dimensi Kritis Proses Pembangunan di Indonesia
UPDATE catalogs SET Subject = 'Pembangunan ekonomi -- Indonesia; Sosiologi pembangunan' WHERE ID = 41204 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41204, '600', '#', '#', '$a Pembangunan ekonomi -- Indonesia; Sosiologi pembangunan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41204 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pembangunan ekonomi -- Indonesia; Sosiologi pembangunan' WHERE CatalogId = 41204 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35951 | Masyarakat dan Manusia dalam Pembangunan: Pokok-pokok pikiran Selo Soemardjan
UPDATE catalogs SET Subject = 'Sosiologi pembangunan; Pembangunan sosial -- Indonesia' WHERE ID = 35951 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35951, '600', '#', '#', '$a Sosiologi pembangunan; Pembangunan sosial -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35951 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Sosiologi pembangunan; Pembangunan sosial -- Indonesia' WHERE CatalogId = 35951 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35902 | Petunjuk Statistik kalimantan Timur 1980 = Statistical Guide East Kalimantan 1980
UPDATE catalogs SET Subject = 'Transmigrasi; Kependudukan' WHERE ID = 35902 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35902, '600', '#', '#', '$a Transmigrasi; Kependudukan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35902 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi; Kependudukan' WHERE CatalogId = 35902 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36071 | Studi Kontribusi Transmigrasi Terhadap Kebutuhan Pangan
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 36071 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36071, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36071 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 36071 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36255 | Statistik Realisasi Pemberangkatan Transmigran Jawa Barat Sejak Pra Pelita S/D Pelita V Tahun ke 1 (1989/1990)
UPDATE catalogs SET Subject = 'Transmigrasi; Kependudukan' WHERE ID = 36255 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36255, '600', '#', '#', '$a Transmigrasi; Kependudukan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36255 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi; Kependudukan' WHERE CatalogId = 36255 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35866 | Masyarakat Desa di Indonesia
UPDATE catalogs SET Subject = 'Sosiologi pedesaan; Masyarakat desa -- Indonesia' WHERE ID = 35866 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35866, '600', '#', '#', '$a Sosiologi pedesaan; Masyarakat desa -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35866 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Sosiologi pedesaan; Masyarakat desa -- Indonesia' WHERE CatalogId = 35866 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 30658 | Indikator Pasar Tenaga Kerja Indonesia Agustus 2014
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia; Ketenagakerjaan -- Statistik' WHERE ID = 30658 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 30658, '600', '#', '#', '$a Statistik Umum Indonesia; Ketenagakerjaan -- Statistik', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 30658 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia; Ketenagakerjaan -- Statistik' WHERE CatalogId = 30658 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35979 | Statistik Indonesia 2014 = Statistical yearbook of Indonesia 2014
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 35979 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35979, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35979 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 35979 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35984 | Statistik Penduduk Usia Lanjut 2017
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 35984 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35984, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35984 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 35984 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35986 | Statistik Penduduk 1996 = Statistical Yearbook of Indonesia 1996
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 35986 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35986, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35986 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 35986 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36218 | Statistik Indonesia 2004 = Statistical Yearbook of Indonesia 2004
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36218 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36218, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36218 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36218 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36230 | Indikator Konstruksi 1999 = Construction Indicator 1999
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36230 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36230, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36230 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36230 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36231 | Statistik Kesehatan 1998 = Health Statistics 1998
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36231 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36231, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36231 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36231 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36232 | Statistik Pertambangan Minyak dan Gas Bumi 1998 = Mining Statistics of Petroleum and Natural Gas 1998
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36232 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36232, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36232 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36232 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36233 | Daftar Nama, Kode & Peta Wilayah Administrasi Indonesia 1989
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36233 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36233, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36233 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36233 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36234 | Statistik Kehutanan Indonesia 2003 = Forestry Statistics of Indonesia 2003
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36234 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36234, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36234 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36234 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36235 | Statistik Indonesia 2008 = Statistical Yearbook of Indonesia 2008
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36235 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36235, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36235 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36235 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36238 | Keadaan Angkatan Pekerja di Indonesia Februari 2014 = Labor Force Situation in Indonesia February 2014
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36238 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36238, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36238 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36238 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36239 | Keadaan Angkatan Kerja di Indonesia Agustus 1999 = Labor Force Situation in Indonesia August 1999
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36239 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36239, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36239 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36239 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36240 | Statistik Lingkungan hidup Indonesia 1985
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36240 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36240, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36240 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36240 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36241 | Penyandang Cacat di Indonesia 1980 = Disabled Persons in Indonesia 1980
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36241 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36241, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36241 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36241 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36242 | Peta Penduduk dan Kesejahteraan Rakyat 1980
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36242 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36242, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36242 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36242 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36245 | Daftar Nama, Kode & Peta Wilayah Administrasi Indonesia 1996
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36245 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36245, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36245 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36245 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36246 | Profil Wanita Kepala Rumah Tangga 1998 = Women of Head of Household Profile
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36246 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36246, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36246 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36246 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36248 | Profil Statistik Wanita, Ibu dan Anak di Indonesia 1994 = Statistical Profile of Women, Mothers and Children in Indonesia 1994
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36248 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36248, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36248 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36248 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36249 | Keadaan angkatan pekerja  di Indonesia Agustus 2014 = Labor Force Situation in Indonesia August 2014
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36249 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36249, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36249 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36249 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36250 | Profil Usaha Kecil dan Menengah Tidak Berbadan Hukum Indonesia Tahun 1999
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36250 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36250, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36250 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36250 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36251 | Penduduk Indonesia dalam Gambar 1980 = Population of Indonesia in Graphs 1980
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36251 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36251, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36251 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36251 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36252 | Statistik Lingkungan Hidup Indonesia 1988 = Environmental Statistics of Indonesia 1988
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36252 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36252, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36252 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36252 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36253 | Statistik Lingkungan Hidup Indonesia 1986
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36253 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36253, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36253 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36253 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36254 | Penduduk Indonesia: Hasil Sub-sampel Sensus Penduduk 1980
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36254 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36254, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36254 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36254 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36256 | Profil Usaha Kecil dan Menengah Tidak Berbadan Hukum 1998
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 36256 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36256, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36256 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 36256 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40127 | Statistik Air Minum 1991 = Water Supply Statistics
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 40127 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40127, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40127 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 40127 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40128 | Statistik Air Minum 1991 = Water Supply Statistics
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 40128 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40128, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40128 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 40128 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40130 | Statistik Bangunan/Konstruksi Non Anggota AKI 1998 = Building Construction Statistics for Non Member of The Indonesian Contractor Association
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 40130 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40130, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40130 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 40130 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40131 | Statistik Bangunan/Konstruksi Non Anggota AKI 1998 = Building Construction Statistics for Non Member of The Indonesian Contractor Association
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 40131 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40131, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40131 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 40131 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41221 | Statistik Pemuda Indonesia 2014
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia' WHERE ID = 41221 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41221, '600', '#', '#', '$a Statistik Umum Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41221 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia' WHERE CatalogId = 41221 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36197 | Menuju Indonesia Baru Bersama SBY
UPDATE catalogs SET Subject = 'Ilmu Politik; Pemilihan umum' WHERE ID = 36197 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36197, '600', '#', '#', '$a Ilmu Politik; Pemilihan umum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36197 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ilmu Politik; Pemilihan umum' WHERE CatalogId = 36197 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39835 | Pembangunan Politik Dalam Negeri Indonesia
UPDATE catalogs SET Subject = 'Ilmu Politik; Pemilihan umum' WHERE ID = 39835 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39835, '600', '#', '#', '$a Ilmu Politik; Pemilihan umum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39835 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ilmu Politik; Pemilihan umum' WHERE CatalogId = 39835 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41100 | Membangun Memori Kolektif Bangsa Menuju Indonesia Emas 2045
UPDATE catalogs SET Subject = 'Ilmu Politik; Pemilihan umum' WHERE ID = 41100 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41100, '600', '#', '#', '$a Ilmu Politik; Pemilihan umum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41100 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ilmu Politik; Pemilihan umum' WHERE CatalogId = 41100 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37617 | Militer dan Politik di Indonesia
UPDATE catalogs SET Subject = 'Militer dan politik -- Indonesia; Angkatan Bersenjata' WHERE ID = 37617 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37617, '600', '#', '#', '$a Militer dan politik -- Indonesia; Angkatan Bersenjata', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37617 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Militer dan politik -- Indonesia; Angkatan Bersenjata' WHERE CatalogId = 37617 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36261 | Harapan, Keprihatinan, dan Tekad: Angkatan '45 Merampungkan Tugas Sejarahnya
UPDATE catalogs SET Subject = 'Indonesia -- Sejarah -- Perjuangan kemerdekaan; Pejuang 45' WHERE ID = 36261 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36261, '600', '#', '#', '$a Indonesia -- Sejarah -- Perjuangan kemerdekaan; Pejuang 45', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36261 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Indonesia -- Sejarah -- Perjuangan kemerdekaan; Pejuang 45' WHERE CatalogId = 36261 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36204 | Pemuda dan Masa Depan
UPDATE catalogs SET Subject = 'Pemuda -- Indonesia; Peranan sosial' WHERE ID = 36204 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36204, '600', '#', '#', '$a Pemuda -- Indonesia; Peranan sosial', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36204 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pemuda -- Indonesia; Peranan sosial' WHERE CatalogId = 36204 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40259 | Menggugat Pemilihan Umum Orde Baru
UPDATE catalogs SET Subject = 'Ilmu Politik; Pemilihan umum' WHERE ID = 40259 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40259, '600', '#', '#', '$a Ilmu Politik; Pemilihan umum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40259 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ilmu Politik; Pemilihan umum' WHERE CatalogId = 40259 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36267 | Politik Luar Negeri Indonesia
UPDATE catalogs SET Subject = 'Politik luar negeri -- Indonesia; Hubungan internasional' WHERE ID = 36267 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36267, '600', '#', '#', '$a Politik luar negeri -- Indonesia; Hubungan internasional', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36267 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Politik luar negeri -- Indonesia; Hubungan internasional' WHERE CatalogId = 36267 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35942 | Prinsip Ekologi Untuk Pembangunan Ekonomi
UPDATE catalogs SET Subject = 'Ilmu Ekonomi' WHERE ID = 35942 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35942, '600', '#', '#', '$a Ilmu Ekonomi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35942 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ilmu Ekonomi' WHERE CatalogId = 35942 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40134 | Pesan Seorang Bapak untuk Masa Depan Anak-anaknya : Kaya atau Makmur, Pilih Mana?
UPDATE catalogs SET Subject = 'Ilmu Ekonomi' WHERE ID = 40134 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40134, '600', '#', '#', '$a Ilmu Ekonomi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40134 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ilmu Ekonomi' WHERE CatalogId = 40134 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41116 | Hukum Ketenagakerjaan Pasca Reformasi
UPDATE catalogs SET Subject = 'Ketenagakerjaan' WHERE ID = 41116 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41116, '600', '#', '#', '$a Ketenagakerjaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41116 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ketenagakerjaan' WHERE CatalogId = 41116 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41219 | Department Of Manpower
UPDATE catalogs SET Subject = 'Ketenagakerjaan' WHERE ID = 41219 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41219, '600', '#', '#', '$a Ketenagakerjaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41219 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ketenagakerjaan' WHERE CatalogId = 41219 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41208 | Peraturan Menteri Tenaga Kerja Dan Transmigrasi RI Nomor Per.01/???/1/2009 Tentang Pedoman Penggunaan Metoda Statistika Ketenagakerjaan
UPDATE catalogs SET Subject = 'Transmigrasi -- Undang-undang dan peraturan' WHERE ID = 41208 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41208, '600', '#', '#', '$a Transmigrasi -- Undang-undang dan peraturan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41208 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Undang-undang dan peraturan' WHERE CatalogId = 41208 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41134 | Ekonomi Sumber Daya Manusia dalam Perspektif Pembangunan 2006
UPDATE catalogs SET Subject = 'Produktivitas tenaga kerja; Tenaga kerja' WHERE ID = 41134 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41134, '600', '#', '#', '$a Produktivitas tenaga kerja; Tenaga kerja', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41134 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Produktivitas tenaga kerja; Tenaga kerja' WHERE CatalogId = 41134 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41200 | Measures for Rural Employment Generation in Asia and The Pacific
UPDATE catalogs SET Subject = 'Produktivitas tenaga kerja; Tenaga kerja' WHERE ID = 41200 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41200, '600', '#', '#', '$a Produktivitas tenaga kerja; Tenaga kerja', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41200 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Produktivitas tenaga kerja; Tenaga kerja' WHERE CatalogId = 41200 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36143 | Peraturan Menteri Tenaga Kerja dan Transmigrasi RI Nomor PER.21/MEN/IX/2009 tentang Pedoman Pelayanan Produktivitas- Peraturan Menteri Tenaga Kerja dan Transmigrasi RI Nomor PER. 22/MEN/IX/2009 tentang Penyelenggaraan Pemagangan di Dalam Negeri
UPDATE catalogs SET Subject = 'Transmigrasi -- Undang-undang dan peraturan' WHERE ID = 36143 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36143, '600', '#', '#', '$a Transmigrasi -- Undang-undang dan peraturan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36143 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Undang-undang dan peraturan' WHERE CatalogId = 36143 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41109 | Surat Keputusan Direktur Jenderal Pembinaan Pelatihan dan Produktivitas Nomor : 384/LATTAS/XII/2006 tentang Pedoman Penyusunan Kebutuhan Instruktur
UPDATE catalogs SET Subject = 'Produktivitas tenaga kerja; Manajemen produktivitas' WHERE ID = 41109 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41109, '600', '#', '#', '$a Produktivitas tenaga kerja; Manajemen produktivitas', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41109 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Produktivitas tenaga kerja; Manajemen produktivitas' WHERE CatalogId = 41109 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35897 | Studi Pengembangan Kebijakan Perluasan Kesempatan Kerja di Kawasan Transmigrasi
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 35897 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35897, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35897 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 35897 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41119 | Pedoman Penyelesaian PHK
UPDATE catalogs SET Subject = 'Serikat pekerja; Hubungan industrial' WHERE ID = 41119 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41119, '600', '#', '#', '$a Serikat pekerja; Hubungan industrial', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41119 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Serikat pekerja; Hubungan industrial' WHERE CatalogId = 41119 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37009 | An Introduction to Collective Bargaining and Industrial Relation
UPDATE catalogs SET Subject = 'Serikat pekerja; Hubungan industrial' WHERE ID = 37009 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37009, '600', '#', '#', '$a Serikat pekerja; Hubungan industrial', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37009 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Serikat pekerja; Hubungan industrial' WHERE CatalogId = 37009 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37682 | Peraturan Menteri Tenaga kerja dan Transmigrasi Republik Indonesia Nomor PER. 31/MEN/XII/2008 tentang Pedoman Penyelesaian Perselisihan Hubungan Industrial Melalui Perundingan Bipartit dan Peraturan Menteri Tenaga Kerja dan Transmigrasi Republik Indonesia Nomor PER. 32/MEN/XII/2008 tentang Tatacara Pembentukan dan Susunan Keanggotaan Lembaga Kerja Sama Bipartit
UPDATE catalogs SET Subject = 'Transmigrasi -- Undang-undang dan peraturan' WHERE ID = 37682 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37682, '600', '#', '#', '$a Transmigrasi -- Undang-undang dan peraturan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37682 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Undang-undang dan peraturan' WHERE CatalogId = 37682 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41233 | Hukum Kerja: Hukum Ketenagakerjaan Bidang Hubungan Kerja
UPDATE catalogs SET Subject = 'Serikat pekerja; Hubungan industrial' WHERE ID = 41233 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41233, '600', '#', '#', '$a Serikat pekerja; Hubungan industrial', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41233 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Serikat pekerja; Hubungan industrial' WHERE CatalogId = 41233 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35935 | Pola Konsumsi Energi Penduduk Indonesia di Masa Depan
UPDATE catalogs SET Subject = 'Energi -- Konsumsi; Sumber daya energi -- Indonesia' WHERE ID = 35935 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35935, '600', '#', '#', '$a Energi -- Konsumsi; Sumber daya energi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35935 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Energi -- Konsumsi; Sumber daya energi -- Indonesia' WHERE CatalogId = 35935 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37517 | Mikro Ekonomi : Teori Pengantar
UPDATE catalogs SET Subject = 'Ekonomi industri; Pembangunan industri' WHERE ID = 37517 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37517, '600', '#', '#', '$a Ekonomi industri; Pembangunan industri', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37517 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ekonomi industri; Pembangunan industri' WHERE CatalogId = 37517 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36226 | Produk Domestik Regional Bruo Kabupaten/Kota di Indonesia 2000-2004 = Gross Regional Domestic Product Regencies/Municipalities in Indonesia 2000-2004
UPDATE catalogs SET Subject = 'Makroekonomi; Kebijakan fiskal' WHERE ID = 36226 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36226, '600', '#', '#', '$a Makroekonomi; Kebijakan fiskal', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36226 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Makroekonomi; Kebijakan fiskal' WHERE CatalogId = 36226 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35760 | Mengurai Benang Kusut Metropolitan
UPDATE catalogs SET Subject = 'Makroekonomi; Kebijakan fiskal' WHERE ID = 35760 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35760, '600', '#', '#', '$a Makroekonomi; Kebijakan fiskal', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35760 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Makroekonomi; Kebijakan fiskal' WHERE CatalogId = 35760 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37757 | Petunjuk Pelaksanaan Undang-undang Nomor 2 Tahun 2004 tentang Penyelesaian Perselisihan Hubungan Industri pada Pengadilan Hubungan Industrial
UPDATE catalogs SET Subject = 'Hubungan industrial; Perjanjian kerja bersama' WHERE ID = 37757 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37757, '600', '#', '#', '$a Hubungan industrial; Perjanjian kerja bersama', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37757 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hubungan industrial; Perjanjian kerja bersama' WHERE CatalogId = 37757 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40022 | Himpunan Peraturan Perundang-Undangan : Undang-undang Rencana Pembangunan Jangka Panjang Nasional tahun 2005-2025
UPDATE catalogs SET Subject = 'Pembangunan nasional -- Rencana jangka panjang; Hukum -- Indonesia' WHERE ID = 40022 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40022, '600', '#', '#', '$a Pembangunan nasional -- Rencana jangka panjang; Hukum -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40022 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pembangunan nasional -- Rencana jangka panjang; Hukum -- Indonesia' WHERE CatalogId = 40022 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40135 | Peraturan Presiden Republik Indonesia Nomor 95 Tahun 2007 tentang Perubahan Ketujuh Atas Keputusan Presiden Nomor 80 Tahun 2003 Tentang Pedoman Pelaksanaan Pengadaan Barang/Jasa Pemerintah
UPDATE catalogs SET Subject = 'Pengadaan barang dan jasa -- Peraturan perundang-undangan' WHERE ID = 40135 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40135, '600', '#', '#', '$a Pengadaan barang dan jasa -- Peraturan perundang-undangan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40135 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pengadaan barang dan jasa -- Peraturan perundang-undangan' WHERE CatalogId = 40135 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41159 | Infromasi Peraturan Perundang-Undang 2007
UPDATE catalogs SET Subject = 'Hukum -- Peraturan perundang-undangan -- Indonesia' WHERE ID = 41159 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41159, '600', '#', '#', '$a Hukum -- Peraturan perundang-undangan -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41159 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum -- Peraturan perundang-undangan -- Indonesia' WHERE CatalogId = 41159 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35962 | Hukum dan Perkembangan Sosial: Buku Teks Sosiologi Hukum Buku I
UPDATE catalogs SET Subject = 'Sosiologi hukum' WHERE ID = 35962 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35962, '600', '#', '#', '$a Sosiologi hukum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35962 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Sosiologi hukum' WHERE CatalogId = 35962 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40136 | Konsep Pembentukan Peraturan Perundang-undangan : Teori Sejarah, dan Perbandingan dengan Beberapa Negara Bikameral
UPDATE catalogs SET Subject = 'Peraturan perundang-undangan -- Pembuatan; Hukum perundang-undangan' WHERE ID = 40136 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40136, '600', '#', '#', '$a Peraturan perundang-undangan -- Pembuatan; Hukum perundang-undangan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40136 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Peraturan perundang-undangan -- Pembuatan; Hukum perundang-undangan' WHERE CatalogId = 40136 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35973 | Lima Tahun Menegakan Konstitusi: Gambaran Singkat Pelaksanaan Tugas Mahkamah Konstitusi 2003-2008
UPDATE catalogs SET Subject = 'Hukum tata negara' WHERE ID = 35973 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35973, '600', '#', '#', '$a Hukum tata negara', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35973 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum tata negara' WHERE CatalogId = 35973 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36045 | Deidologi Pancasila: analisis kritis perspektif sejarah hukum ketatanegaraan Indonesia
UPDATE catalogs SET Subject = 'Hukum tata negara' WHERE ID = 36045 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36045, '600', '#', '#', '$a Hukum tata negara', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36045 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum tata negara' WHERE CatalogId = 36045 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37794 | Undang-undang Rencana Pembangunan Jangka Panjang Nasional tahun 2005-2025 (UU RI No. 17 tahun 2007)
UPDATE catalogs SET Subject = 'Hukum tata negara' WHERE ID = 37794 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37794, '600', '#', '#', '$a Hukum tata negara', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37794 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum tata negara' WHERE CatalogId = 37794 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41008 | Peraturan Menteri Dalam Negeri Nomor 54 tahun 2010 tentang Pelaksanaan Peraturan Pemerintah Nomor 8 Tahun 2008 tentang Tahapan, Tatacara Penyusunan, Pengendalian, dan Evaluasi Pelaksanaan Rencana Pembangunan Daerah
UPDATE catalogs SET Subject = 'Hukum tata negara' WHERE ID = 41008 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41008, '600', '#', '#', '$a Hukum tata negara', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41008 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum tata negara' WHERE CatalogId = 41008 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35969 | Standar Biaya Masukan Tahun Anggaran 2014 (Per.Men.Keu Nomor 72/PMK.02/2013) & Pedoman Standar Biaya Standar Struktur Biaya, dan Indeksasi dalam Penyusunan Rencana Kerja dan Anggaran Kementrian negara/Lembaga (Per.Men.Keu Nomor 71/PMK.02/2013)
UPDATE catalogs SET Subject = 'Hukum tata negara' WHERE ID = 35969 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35969, '600', '#', '#', '$a Hukum tata negara', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35969 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum tata negara' WHERE CatalogId = 35969 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39016 | HUKUM PENYIARAN
UPDATE catalogs SET Subject = 'Hukum penyiaran; Telekomunikasi -- Aspek hukum' WHERE ID = 39016 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39016, '600', '#', '#', '$a Hukum penyiaran; Telekomunikasi -- Aspek hukum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39016 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum penyiaran; Telekomunikasi -- Aspek hukum' WHERE CatalogId = 39016 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36247 | Indikator Pasar Tenaga kerja Indonesia Agustus 2014
UPDATE catalogs SET Subject = 'Statistik Umum Indonesia; Ketenagakerjaan -- Statistik' WHERE ID = 36247 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36247, '600', '#', '#', '$a Statistik Umum Indonesia; Ketenagakerjaan -- Statistik', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36247 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Statistik Umum Indonesia; Ketenagakerjaan -- Statistik' WHERE CatalogId = 36247 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41110 | Pedoman Ketenagakerjaan tentang Perjanjian Kerja, Penyerahan Sebagian Pekerjaan kepada Perusahaan Lain melalui Pemborongan Pekerjaan, Tata Cara Perizinan Perusahaan Penyedia Jasa Pekerja/Buruh, Beserta Peraturan Pelengkap
UPDATE catalogs SET Subject = 'Ketenagakerjaan -- Undang-undang dan peraturan' WHERE ID = 41110 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41110, '600', '#', '#', '$a Ketenagakerjaan -- Undang-undang dan peraturan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41110 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ketenagakerjaan -- Undang-undang dan peraturan' WHERE CatalogId = 41110 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41113 | Keputusan Menteri Tenaga Kerja dan Transmigrasi Republik Indonesia Nomor : KEP.224/MEN/2003 tentang Kewajiban Pengusaha yang Mempekerjakan Pekerja/Buruh Perempuan Antara Pukul 23.00 sampai dengan 07.00
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 41113 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41113, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41113 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 41113 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41259 | Kedudukan Hukum Badan Penyelenggara Jaminan Sosial Ketenagakerjaan dalam Penyelenggaraan Jaminan Sosial Tenaga Kerja
UPDATE catalogs SET Subject = 'Jaminan sosial tenaga kerja; Asuransi sosial' WHERE ID = 41259 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41259, '600', '#', '#', '$a Jaminan sosial tenaga kerja; Asuransi sosial', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41259 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Jaminan sosial tenaga kerja; Asuransi sosial' WHERE CatalogId = 41259 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35867 | Kumpulan Peraturan Pelaksanaan Transmigrasi Swakarsa
UPDATE catalogs SET Subject = 'Transmigrasi -- Undang-undang dan peraturan' WHERE ID = 35867 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35867, '600', '#', '#', '$a Transmigrasi -- Undang-undang dan peraturan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35867 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Undang-undang dan peraturan' WHERE CatalogId = 35867 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35977 | Peraturan Menteri Keuangan Nomor: 120/PMK.06/2007 Tentang Penatausahaan barang milik Negara
UPDATE catalogs SET Subject = 'Hukum -- Peraturan perundang-undangan -- Indonesia' WHERE ID = 35977 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35977, '600', '#', '#', '$a Hukum -- Peraturan perundang-undangan -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35977 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum -- Peraturan perundang-undangan -- Indonesia' WHERE CatalogId = 35977 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36206 | Pedoman Pelaksanaan Transmigrasi Swakarsa
UPDATE catalogs SET Subject = 'Transmigrasi swakarsa; Transmigrasi -- Indonesia' WHERE ID = 36206 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36206, '600', '#', '#', '$a Transmigrasi swakarsa; Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36206 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi swakarsa; Transmigrasi -- Indonesia' WHERE CatalogId = 36206 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36257 | Statistik Tenaga kerja Indonesia 1973
UPDATE catalogs SET Subject = 'Tenaga kerja Indonesia -- Luar negeri; Pekerja migran' WHERE ID = 36257 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36257, '600', '#', '#', '$a Tenaga kerja Indonesia -- Luar negeri; Pekerja migran', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36257 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Tenaga kerja Indonesia -- Luar negeri; Pekerja migran' WHERE CatalogId = 36257 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36386 | Strategi Efektif Keberhasilan PJPT Kedua: Menjemput mereka yang tertinggal dan yang tercecer oleh derasnya pembangunan pada PJPT pertama (Upaya menanggulangi Kemiskinan)
UPDATE catalogs SET Subject = 'Hukum -- Peraturan perundang-undangan -- Indonesia' WHERE ID = 36386 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36386, '600', '#', '#', '$a Hukum -- Peraturan perundang-undangan -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36386 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Hukum -- Peraturan perundang-undangan -- Indonesia' WHERE CatalogId = 36386 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37908 | Administrasi Pembangunan : Studi Kajian PKL
UPDATE catalogs SET Subject = 'Administrasi publik; Manajemen pemerintahan' WHERE ID = 37908 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37908, '600', '#', '#', '$a Administrasi publik; Manajemen pemerintahan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37908 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Administrasi publik; Manajemen pemerintahan' WHERE CatalogId = 37908 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35971 | Pembinaan Karir Pegawai Negeri Sipil & Pemantapan Aparatur Negara Dalam Pelaynanan masyarakat
UPDATE catalogs SET Subject = 'Administrasi publik; Manajemen pemerintahan' WHERE ID = 35971 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35971, '600', '#', '#', '$a Administrasi publik; Manajemen pemerintahan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35971 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Administrasi publik; Manajemen pemerintahan' WHERE CatalogId = 35971 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39572 | Peraturan Menteri Keuangan Nomor 62/PMK.05/2007 tentang Perubahan atas Peraturan Menteri Keuangan Nomor 45/PMK.05/2007 tentang Perjalanan Dinas Jabatan dalam Negeri Bagi Pejabat Negara, Pegawai Negeri, dan Pegawai Tidak Tetap
UPDATE catalogs SET Subject = 'Administrasi publik; Manajemen pemerintahan' WHERE ID = 39572 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39572, '600', '#', '#', '$a Administrasi publik; Manajemen pemerintahan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39572 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Administrasi publik; Manajemen pemerintahan' WHERE CatalogId = 39572 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35926 | Risiko Apapun Tetap Netral
UPDATE catalogs SET Subject = 'Administrasi publik; Manajemen pemerintahan' WHERE ID = 35926 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35926, '600', '#', '#', '$a Administrasi publik; Manajemen pemerintahan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35926 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Administrasi publik; Manajemen pemerintahan' WHERE CatalogId = 35926 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41260 | 5 Tahun Kiprah UKP-PPI Memorandum Jabatan Utusan Khusus Presiden untuk Pengendalian Perubahan Iklim 2015-2019 / UKP-PPI
UPDATE catalogs SET Subject = 'Administrasi publik; Manajemen pemerintahan' WHERE ID = 41260 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41260, '600', '#', '#', '$a Administrasi publik; Manajemen pemerintahan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41260 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Administrasi publik; Manajemen pemerintahan' WHERE CatalogId = 41260 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39836 | Profil Lengkap Kabinet Indonesia Bersatu II
UPDATE catalogs SET Subject = 'Kabinet -- Indonesia; Eksekutif -- Indonesia' WHERE ID = 39836 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39836, '600', '#', '#', '$a Kabinet -- Indonesia; Eksekutif -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39836 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kabinet -- Indonesia; Eksekutif -- Indonesia' WHERE CatalogId = 39836 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35724 | Labour Market Developments and International Migration in Indonesia
UPDATE catalogs SET Subject = 'Pasar tenaga kerja; Ketenagakerjaan -- Indonesia' WHERE ID = 35724 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35724, '600', '#', '#', '$a Pasar tenaga kerja; Ketenagakerjaan -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35724 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pasar tenaga kerja; Ketenagakerjaan -- Indonesia' WHERE CatalogId = 35724 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35781 | Pedoman Umum Pola-pola transmigrasi
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 35781 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35781, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35781 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 35781 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35782 | Petunjuk Direktur Jendral Transmigrasi Tentang Pengelolaan Jaminan Hidup
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 35782 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35782, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35782 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 35782 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35835 | Kamus Istilah dan Singkatan Pengerahan dan Pembinaan Transmigrasi
UPDATE catalogs SET Subject = 'Transmigrasi -- Kamus' WHERE ID = 35835 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35835, '600', '#', '#', '$a Transmigrasi -- Kamus', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35835 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Kamus' WHERE CatalogId = 35835 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35848 | Population Growth, Distribution, Migration, and Urbanization in Indonesia
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 35848 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35848, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35848 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 35848 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35869 | Kajian Sistem Insentif Bagi pengelola Kawasan Transmigrasi secara Terpadu
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 35869 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35869, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35869 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 35869 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35890 | Proyek Pemukiman Transmigrasi NPS. Sebamban V dan Perkembangannya
UPDATE catalogs SET Subject = 'Transmigrasi -- Permukiman; Transmigrasi -- Indonesia' WHERE ID = 35890 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35890, '600', '#', '#', '$a Transmigrasi -- Permukiman; Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35890 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Permukiman; Transmigrasi -- Indonesia' WHERE CatalogId = 35890 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36015 | Keputusan Menteri Tenaga Kerja dan Transmigrasi Republik Indonesia Nomor: KEP. 55/MEN/1986 tentang Panca Matra Transmigrasi Terpadu Sebagai Layanan Kebijaksanaan dan Strategi Penyelenggaraan Transmigrasi
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 36015 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36015, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36015 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 36015 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36237 | Statistik Tenaga Kerja Indonesia 1978
UPDATE catalogs SET Subject = 'Tenaga kerja Indonesia -- Luar negeri; Pekerja migran' WHERE ID = 36237 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36237, '600', '#', '#', '$a Tenaga kerja Indonesia -- Luar negeri; Pekerja migran', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36237 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Tenaga kerja Indonesia -- Luar negeri; Pekerja migran' WHERE CatalogId = 36237 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36437 | Pengelolaan Lingkungan Sosial
UPDATE catalogs SET Subject = 'Ekologi sosial; Lingkungan hidup' WHERE ID = 36437 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36437, '600', '#', '#', '$a Ekologi sosial; Lingkungan hidup', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36437 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ekologi sosial; Lingkungan hidup' WHERE CatalogId = 36437 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35720 | Jurnal Perempuan
UPDATE catalogs SET Subject = 'Wanita -- Masalah sosial; Majalah Indonesia' WHERE ID = 35720 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35720, '600', '#', '#', '$a Wanita -- Masalah sosial; Majalah Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35720 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Wanita -- Masalah sosial; Majalah Indonesia' WHERE CatalogId = 35720 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41214 | Encyclopedia of Law Enforcement = Ensiklopedia Penegakan Hukum Volume 2
UPDATE catalogs SET Subject = 'Penegakan hukum -- Ensiklopedia; Polisi' WHERE ID = 41214 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41214, '600', '#', '#', '$a Penegakan hukum -- Ensiklopedia; Polisi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41214 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Penegakan hukum -- Ensiklopedia; Polisi' WHERE CatalogId = 41214 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41215 | Encyclopedia of Law Enforcement = Ensiklopedia Penegakan Hukum Volume 3
UPDATE catalogs SET Subject = 'Penegakan hukum -- Ensiklopedia; Polisi' WHERE ID = 41215 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41215, '600', '#', '#', '$a Penegakan hukum -- Ensiklopedia; Polisi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41215 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Penegakan hukum -- Ensiklopedia; Polisi' WHERE CatalogId = 41215 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41198 | Peraturan Presiden Republik Indonesia Nomor 7 Tahun 2021 tentang Rencana Aksi Nasional Pencegahan dan Penanggulangan Ekstrimisme Berbasis Kekerasan yang Mengarah pada Terorisme Tahun 2020-2024
UPDATE catalogs SET Subject = 'Terorisme -- Pencegahan -- Peraturan perundang-undangan' WHERE ID = 41198 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41198, '600', '#', '#', '$a Terorisme -- Pencegahan -- Peraturan perundang-undangan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41198 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Terorisme -- Pencegahan -- Peraturan perundang-undangan' WHERE CatalogId = 41198 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36146 | Berwisata Alam di Taman Nasional
UPDATE catalogs SET Subject = 'Taman nasional; Ekowisata -- Indonesia' WHERE ID = 36146 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36146, '600', '#', '#', '$a Taman nasional; Ekowisata -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36146 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Taman nasional; Ekowisata -- Indonesia' WHERE CatalogId = 36146 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35825 | Dampak Variable Demografi pada Masalah Lingkungan
UPDATE catalogs SET Subject = 'Ekologi manusia; Lingkungan hidup' WHERE ID = 35825 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35825, '600', '#', '#', '$a Ekologi manusia; Lingkungan hidup', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35825 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ekologi manusia; Lingkungan hidup' WHERE CatalogId = 35825 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37201 | Etika Lingkungan
UPDATE catalogs SET Subject = 'Ekologi manusia; Lingkungan hidup' WHERE ID = 37201 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37201, '600', '#', '#', '$a Ekologi manusia; Lingkungan hidup', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37201 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ekologi manusia; Lingkungan hidup' WHERE CatalogId = 37201 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35945 | Masalah Penduduk dalam Fakta dan Angka
UPDATE catalogs SET Subject = 'Kependudukan -- Statistik; Demografi' WHERE ID = 35945 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35945, '600', '#', '#', '$a Kependudukan -- Statistik; Demografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35945 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kependudukan -- Statistik; Demografi' WHERE CatalogId = 35945 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35929 | Tindakan Pidana Pegawai negeri Sipil Di Indonesia
UPDATE catalogs SET Subject = 'Pegawai negeri sipil -- Tindak pidana; Korupsi -- Aspek hukum' WHERE ID = 35929 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35929, '600', '#', '#', '$a Pegawai negeri sipil -- Tindak pidana; Korupsi -- Aspek hukum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35929 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pegawai negeri sipil -- Tindak pidana; Korupsi -- Aspek hukum' WHERE CatalogId = 35929 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39957 | Perilaku Remaja dan Permasalahannya
UPDATE catalogs SET Subject = 'Psikologi remaja; Kenakalan remaja' WHERE ID = 39957 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39957, '600', '#', '#', '$a Psikologi remaja; Kenakalan remaja', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39957 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Psikologi remaja; Kenakalan remaja' WHERE CatalogId = 39957 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35967 | Keputusan Menteri Tenaga Kerja dan Transmigrasi Republik Indonesia Nomor: Kep.24/MEN/1994 tentang Pembukuan dan Pelaporan Inventarisasi Barang Milik/kekayaan Negara Lingkungan Departemen Transmigrasi dan Pemukiman Perambah Hutan
UPDATE catalogs SET Subject = 'Transmigrasi -- Permukiman; Transmigrasi -- Indonesia' WHERE ID = 35967 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35967, '600', '#', '#', '$a Transmigrasi -- Permukiman; Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35967 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Permukiman; Transmigrasi -- Indonesia' WHERE CatalogId = 35967 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36947 | Standar Latihan Kerja Tukang Gambar Teknik Mesin
UPDATE catalogs SET Subject = 'Pendidikan tinggi; Perguruan tinggi' WHERE ID = 36947 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36947, '600', '#', '#', '$a Pendidikan tinggi; Perguruan tinggi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36947 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pendidikan tinggi; Perguruan tinggi' WHERE CatalogId = 36947 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35960 | 97 dB
UPDATE catalogs SET Subject = 'Alumni perguruan tinggi; Komunikasi -- Alumni' WHERE ID = 35960 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35960, '600', '#', '#', '$a Alumni perguruan tinggi; Komunikasi -- Alumni', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35960 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Alumni perguruan tinggi; Komunikasi -- Alumni' WHERE CatalogId = 35960 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35932 | Keputusan Menteri Tenaga Kerja dan Transmigrasi Republik Indonesia Nomor: Kep.219/Men/2002 tentang Organisasi dan Tata kerja Departemen Tenaga Kerja dan Transmigrasi
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 35932 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35932, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35932 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 35932 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39518 | Keputusan Menteri Tenaga Kerja dan Transmigrasi Republik Indonesia Nomor: Kep.219/Men/2002 tentang Organisasi dan Tata kerja Departemen Tenaga Kerja dan Transmigrasi
UPDATE catalogs SET Subject = 'Transmigrasi -- Indonesia' WHERE ID = 39518 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39518, '600', '#', '#', '$a Transmigrasi -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39518 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Transmigrasi -- Indonesia' WHERE CatalogId = 39518 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41201 | Structural Adjustment In Agriculture In Asia
UPDATE catalogs SET Subject = 'Pertanian -- Ekonomi -- Asia' WHERE ID = 41201 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41201, '600', '#', '#', '$a Pertanian -- Ekonomi -- Asia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41201 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pertanian -- Ekonomi -- Asia' WHERE CatalogId = 41201 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41224 | Bahasa Indonesia : Sebuah Pengantar Penulisan Ilmiah
UPDATE catalogs SET Subject = 'Bahasa Indonesia -- Penulisan ilmiah' WHERE ID = 41224 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41224, '600', '#', '#', '$a Bahasa Indonesia -- Penulisan ilmiah', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41224 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Bahasa Indonesia -- Penulisan ilmiah' WHERE CatalogId = 41224 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39133 | Australian English (Authentic English Conversation) : dan Beberapa Kilas States of The Arts Dalam Dunia Linguistik Pragmatic
UPDATE catalogs SET Subject = 'Bahasa Inggris -- Tata bahasa dan percakapan' WHERE ID = 39133 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39133, '600', '#', '#', '$a Bahasa Inggris -- Tata bahasa dan percakapan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39133 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Bahasa Inggris -- Tata bahasa dan percakapan' WHERE CatalogId = 39133 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41223 | English for Academic Purposes
UPDATE catalogs SET Subject = 'Bahasa Inggris -- Tata bahasa dan percakapan' WHERE ID = 41223 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41223, '600', '#', '#', '$a Bahasa Inggris -- Tata bahasa dan percakapan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41223 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Bahasa Inggris -- Tata bahasa dan percakapan' WHERE CatalogId = 41223 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36116 | Ensiklopedia Nusantara: Mengenal Tanahair
UPDATE catalogs SET Subject = 'Indonesia -- Ensiklopedia; Geografi Indonesia' WHERE ID = 36116 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36116, '600', '#', '#', '$a Indonesia -- Ensiklopedia; Geografi Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36116 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Indonesia -- Ensiklopedia; Geografi Indonesia' WHERE CatalogId = 36116 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36088 | Menaklukan kanker serviks dan kanker payudara dengan 3 terapi alami
UPDATE catalogs SET Subject = 'Kanker serviks; Kanker payudara; Terapi alami' WHERE ID = 36088 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36088, '600', '#', '#', '$a Kanker serviks; Kanker payudara; Terapi alami', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36088 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kanker serviks; Kanker payudara; Terapi alami' WHERE CatalogId = 36088 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40129 | English For Medical
UPDATE catalogs SET Subject = 'Bahasa Inggris -- Istilah medis' WHERE ID = 40129 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40129, '600', '#', '#', '$a Bahasa Inggris -- Istilah medis', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40129 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Bahasa Inggris -- Istilah medis' WHERE CatalogId = 40129 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37430 | Rahasia Terkini Awet Sehat
UPDATE catalogs SET Subject = 'Kesehatan; Gaya hidup sehat' WHERE ID = 37430 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37430, '600', '#', '#', '$a Kesehatan; Gaya hidup sehat', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37430 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kesehatan; Gaya hidup sehat' WHERE CatalogId = 37430 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36075 | Wanita, Kesehatan dan Keluarga Berencana
UPDATE catalogs SET Subject = 'Keluarga berencana; Kesehatan wanita' WHERE ID = 36075 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36075, '600', '#', '#', '$a Keluarga berencana; Kesehatan wanita', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36075 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Keluarga berencana; Kesehatan wanita' WHERE CatalogId = 36075 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41206 | Mendapatkan Jenis Kelamin Anak sesuai Idaman
UPDATE catalogs SET Subject = 'Karya Umum' WHERE ID = 41206 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41206, '600', '#', '#', '$a Karya Umum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41206 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Karya Umum' WHERE CatalogId = 41206 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36069 | Kubis Si Pencegah Kanker
UPDATE catalogs SET Subject = 'Sayuran -- Khasiat obat; Kanker -- Pencegahan' WHERE ID = 36069 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36069, '600', '#', '#', '$a Sayuran -- Khasiat obat; Kanker -- Pencegahan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36069 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Sayuran -- Khasiat obat; Kanker -- Pencegahan' WHERE CatalogId = 36069 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41108 | Daun Ajaib Tumpas Penyakit
UPDATE catalogs SET Subject = 'Karya Umum' WHERE ID = 41108 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41108, '600', '#', '#', '$a Karya Umum', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41108 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Karya Umum' WHERE CatalogId = 41108 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37259 | Deteksi Gangguan Kesehatan dengan Lidah, Bau Napas, dan Urine
UPDATE catalogs SET Subject = 'Diagnosis medis; Kesehatan tubuh' WHERE ID = 37259 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37259, '600', '#', '#', '$a Diagnosis medis; Kesehatan tubuh', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37259 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Diagnosis medis; Kesehatan tubuh' WHERE CatalogId = 37259 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41262 | Laporan Tahunan 2018 / Lembaga Penerbangan dan Antariksa Nasional
UPDATE catalogs SET Subject = 'LAPAN -- Laporan tahunan; Penerbangan dan antariksa' WHERE ID = 41262 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41262, '600', '#', '#', '$a LAPAN -- Laporan tahunan; Penerbangan dan antariksa', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41262 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a LAPAN -- Laporan tahunan; Penerbangan dan antariksa' WHERE CatalogId = 41262 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36067 | Teknik Servis Mobil
UPDATE catalogs SET Subject = 'Mobil -- Pemeliharaan dan perbaikan' WHERE ID = 36067 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36067, '600', '#', '#', '$a Mobil -- Pemeliharaan dan perbaikan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36067 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Mobil -- Pemeliharaan dan perbaikan' WHERE CatalogId = 36067 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36228 | Survei Pertanian: Luas Tanah Menurut Penggunaannya di Luar Jawa 1985 = Agricultural Survey: Land Area by Utilization in Outer Jawa 1985
UPDATE catalogs SET Subject = 'Pertanian -- Statistik; Penggunaan tanah -- Luar Jawa' WHERE ID = 36228 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36228, '600', '#', '#', '$a Pertanian -- Statistik; Penggunaan tanah -- Luar Jawa', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36228 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pertanian -- Statistik; Penggunaan tanah -- Luar Jawa' WHERE CatalogId = 36228 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36229 | Luas Tanah menurut Penggunaannya di Jawa & Madura 1980
UPDATE catalogs SET Subject = 'Pertanian -- Statistik; Penggunaan tanah -- Jawa dan Madura' WHERE ID = 36229 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36229, '600', '#', '#', '$a Pertanian -- Statistik; Penggunaan tanah -- Jawa dan Madura', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36229 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Pertanian -- Statistik; Penggunaan tanah -- Jawa dan Madura' WHERE CatalogId = 36229 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 38357 | Bertanam Nilam : Bahan Industri Wewangian
UPDATE catalogs SET Subject = 'Nilam -- Budidaya; Tanaman industri wewangian' WHERE ID = 38357 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 38357, '600', '#', '#', '$a Nilam -- Budidaya; Tanaman industri wewangian', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 38357 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Nilam -- Budidaya; Tanaman industri wewangian' WHERE CatalogId = 38357 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41106 | Usaha Tani Jahe : Dilengkapi dengan Pengolahan Jahe Segar
UPDATE catalogs SET Subject = 'Jahe -- Budidaya dan pengolahan; Usahatani' WHERE ID = 41106 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41106, '600', '#', '#', '$a Jahe -- Budidaya dan pengolahan; Usahatani', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41106 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Jahe -- Budidaya dan pengolahan; Usahatani' WHERE CatalogId = 41106 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41203 | Bertanam Cabai dalam Pot Edisi revisi
UPDATE catalogs SET Subject = 'Cabai -- Budidaya; Hortikultura' WHERE ID = 41203 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41203, '600', '#', '#', '$a Cabai -- Budidaya; Hortikultura', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41203 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Cabai -- Budidaya; Hortikultura' WHERE CatalogId = 41203 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37325 | My Healthy Life Trio Herbal
UPDATE catalogs SET Subject = 'Tanaman obat; Herbal' WHERE ID = 37325 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37325, '600', '#', '#', '$a Tanaman obat; Herbal', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37325 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Tanaman obat; Herbal' WHERE CatalogId = 37325 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41105 | Beternak Ayam Kampung Petelur
UPDATE catalogs SET Subject = 'Ayam petelur -- Peternakan; Unggas' WHERE ID = 41105 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41105, '600', '#', '#', '$a Ayam petelur -- Peternakan; Unggas', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41105 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Ayam petelur -- Peternakan; Unggas' WHERE CatalogId = 41105 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41202 | Buku Saki : Pelaksanaan KIE Pencegahan dan Pengendalian Penyakit Mulut dan Kuku (PMK)
UPDATE catalogs SET Subject = 'Penyakit mulut dan kuku; Ternak -- Penyakit' WHERE ID = 41202 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41202, '600', '#', '#', '$a Penyakit mulut dan kuku; Ternak -- Penyakit', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41202 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Penyakit mulut dan kuku; Ternak -- Penyakit' WHERE CatalogId = 41202 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36060 | Khasiat Selangit Air Putih, Air Kelapa Manggis dan Sirsak
UPDATE catalogs SET Subject = 'Khasiat bahan alami; Makanan sehat' WHERE ID = 36060 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36060, '600', '#', '#', '$a Khasiat bahan alami; Makanan sehat', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36060 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Khasiat bahan alami; Makanan sehat' WHERE CatalogId = 36060 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37323 | Khasiat Selangit 101 Buah dan Sayur
UPDATE catalogs SET Subject = 'Buah-buahan -- Khasiat; Sayur-sayuran -- Khasiat' WHERE ID = 37323 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37323, '600', '#', '#', '$a Buah-buahan -- Khasiat; Sayur-sayuran -- Khasiat', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37323 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Buah-buahan -- Khasiat; Sayur-sayuran -- Khasiat' WHERE CatalogId = 37323 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36222 | Indikator Kesejahteraan Anak 1994
UPDATE catalogs SET Subject = 'Anak -- Kesejahteraan -- Statistik' WHERE ID = 36222 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36222, '600', '#', '#', '$a Anak -- Kesejahteraan -- Statistik', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36222 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Anak -- Kesejahteraan -- Statistik' WHERE CatalogId = 36222 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37459 | Mengenal Praktik Bisnis
UPDATE catalogs SET Subject = 'Bisnis; Manajemen bisnis' WHERE ID = 37459 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37459, '600', '#', '#', '$a Bisnis; Manajemen bisnis', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37459 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Bisnis; Manajemen bisnis' WHERE CatalogId = 37459 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 38923 | Manajemen Internasional
UPDATE catalogs SET Subject = 'Bisnis internasional; Manajemen perusahaan' WHERE ID = 38923 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 38923, '600', '#', '#', '$a Bisnis internasional; Manajemen perusahaan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 38923 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Bisnis internasional; Manajemen perusahaan' WHERE CatalogId = 38923 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37722 | PT. Jamsostek : Laporan Keuangan Konsolidasi Per 30 September 2000
UPDATE catalogs SET Subject = 'Jaminan sosial tenaga kerja; Asuransi sosial' WHERE ID = 37722 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37722, '600', '#', '#', '$a Jaminan sosial tenaga kerja; Asuransi sosial', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37722 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Jaminan sosial tenaga kerja; Asuransi sosial' WHERE CatalogId = 37722 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41195 | Transparansi Dalam Pemberesan Boedel Pailit
UPDATE catalogs SET Subject = 'Akuntansi' WHERE ID = 41195 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41195, '600', '#', '#', '$a Akuntansi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41195 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Akuntansi' WHERE CatalogId = 41195 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41197 | Sistem Informasi Akuntansi
UPDATE catalogs SET Subject = 'Akuntansi' WHERE ID = 41197 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41197, '600', '#', '#', '$a Akuntansi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41197 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Akuntansi' WHERE CatalogId = 41197 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 38120 | Sistem Pengendalian Manajemen
UPDATE catalogs SET Subject = 'Manajemen sumber daya manusia; Pegawai Manajemen' WHERE ID = 38120 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 38120, '600', '#', '#', '$a Manajemen sumber daya manusia; Pegawai Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 38120 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Manajemen sumber daya manusia; Pegawai Manajemen' WHERE CatalogId = 38120 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 38257 | Teknik Mendengarkan yang Efektif dalam Berkomunikasi
UPDATE catalogs SET Subject = 'Manajemen sumber daya manusia; Pegawai Manajemen' WHERE ID = 38257 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 38257, '600', '#', '#', '$a Manajemen sumber daya manusia; Pegawai Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 38257 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Manajemen sumber daya manusia; Pegawai Manajemen' WHERE CatalogId = 38257 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41135 | Manajemen Sumber Daya Menusia : Reformasi Birokrasi dan Manajemen Pegawai Negeri Sipil
UPDATE catalogs SET Subject = 'Manajemen sumber daya manusia; Pegawai Manajemen' WHERE ID = 41135 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41135, '600', '#', '#', '$a Manajemen sumber daya manusia; Pegawai Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41135 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Manajemen sumber daya manusia; Pegawai Manajemen' WHERE CatalogId = 41135 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41182 | Pedoman Lengkap Profesional SDM Indonesia : Edisi Revisi
UPDATE catalogs SET Subject = 'Manajemen sumber daya manusia; Pegawai Manajemen' WHERE ID = 41182 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41182, '600', '#', '#', '$a Manajemen sumber daya manusia; Pegawai Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41182 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Manajemen sumber daya manusia; Pegawai Manajemen' WHERE CatalogId = 41182 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35938 | Stress & Kepuasan Kerja: Acuan Mencari Alternatif untuk Meningkatkan Kepuasan kerja Karyawan dalam Lingkungan Kerja yang Sesuai
UPDATE catalogs SET Subject = 'Kepuasan kerja; Psikologi industri' WHERE ID = 35938 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35938, '600', '#', '#', '$a Kepuasan kerja; Psikologi industri', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35938 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kepuasan kerja; Psikologi industri' WHERE CatalogId = 35938 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41118 | Analisis Jabatan 2008
UPDATE catalogs SET Subject = 'Manajemen sumber daya manusia; Pegawai Manajemen' WHERE ID = 41118 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41118, '600', '#', '#', '$a Manajemen sumber daya manusia; Pegawai Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41118 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Manajemen sumber daya manusia; Pegawai Manajemen' WHERE CatalogId = 41118 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36122 | Studi Pengembangan Sumber Daya Manusia Pengelolaan Dana Pensiun
UPDATE catalogs SET Subject = 'Manajemen sumber daya manusia; Pegawai Manajemen' WHERE ID = 36122 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36122, '600', '#', '#', '$a Manajemen sumber daya manusia; Pegawai Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36122 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Manajemen sumber daya manusia; Pegawai Manajemen' WHERE CatalogId = 36122 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 39711 | Multimedia and e-Learning: A New Direction for Productivity Promotion and Enhancement
UPDATE catalogs SET Subject = 'Manajemen sumber daya manusia; Pegawai Manajemen' WHERE ID = 39711 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 39711, '600', '#', '#', '$a Manajemen sumber daya manusia; Pegawai Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 39711 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Manajemen sumber daya manusia; Pegawai Manajemen' WHERE CatalogId = 39711 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35840 | Power & Influence = Kekuasaan dan Pengaruh : bukan sekedar otoritas formal
UPDATE catalogs SET Subject = 'Kepemimpinan; Perusahaan -- Manajemen' WHERE ID = 35840 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35840, '600', '#', '#', '$a Kepemimpinan; Perusahaan -- Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35840 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kepemimpinan; Perusahaan -- Manajemen' WHERE CatalogId = 35840 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41216 | Corporate Governance : Menuju Penguatan Konseptual dan Implementasi di Indonesia
UPDATE catalogs SET Subject = 'Kepemimpinan; Perusahaan -- Manajemen' WHERE ID = 41216 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41216, '600', '#', '#', '$a Kepemimpinan; Perusahaan -- Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41216 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kepemimpinan; Perusahaan -- Manajemen' WHERE CatalogId = 41216 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 37549 | Feasibility Study = Teknik Evaluasi Gagasan Usaha
UPDATE catalogs SET Subject = 'Kepemimpinan; Perusahaan -- Manajemen' WHERE ID = 37549 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 37549, '600', '#', '#', '$a Kepemimpinan; Perusahaan -- Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 37549 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kepemimpinan; Perusahaan -- Manajemen' WHERE CatalogId = 37549 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36190 | Perentjanaan dan Pengawasan dengan PERT dan CPM (Network Planning)
UPDATE catalogs SET Subject = 'Kepemimpinan; Perusahaan -- Manajemen' WHERE ID = 36190 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36190, '600', '#', '#', '$a Kepemimpinan; Perusahaan -- Manajemen', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36190 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kepemimpinan; Perusahaan -- Manajemen' WHERE CatalogId = 36190 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 38997 | PROYEK INFRASTRUKTUR DAN SENGKETA KONSTRUKSI
UPDATE catalogs SET Subject = 'Konstruksi -- Aspek hukum; Sengketa proyek infrastruktur' WHERE ID = 38997 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 38997, '600', '#', '#', '$a Konstruksi -- Aspek hukum; Sengketa proyek infrastruktur', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 38997 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Konstruksi -- Aspek hukum; Sengketa proyek infrastruktur' WHERE CatalogId = 38997 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 40258 | The magic of Blender 3d modelling
UPDATE catalogs SET Subject = 'Kesenian dan rekreasi' WHERE ID = 40258 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 40258, '600', '#', '#', '$a Kesenian dan rekreasi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 40258 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kesenian dan rekreasi' WHERE CatalogId = 40258 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41193 | 200+ Solusi Editing Naskah dan Penerbitan
UPDATE catalogs SET Subject = 'Penerbitan; Penyuntingan naskah' WHERE ID = 41193 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41193, '600', '#', '#', '$a Penerbitan; Penyuntingan naskah', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41193 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Penerbitan; Penyuntingan naskah' WHERE CatalogId = 41193 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41032 | The Twilight Saga : the Official Ilustrated Guide
UPDATE catalogs SET Subject = 'Kesusastraan Amerika; Panduan novel fiksi' WHERE ID = 41032 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41032, '600', '#', '#', '$a Kesusastraan Amerika; Panduan novel fiksi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41032 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Kesusastraan Amerika; Panduan novel fiksi' WHERE CatalogId = 41032 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41121 | Laskar Pelangi
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41121 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41121, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41121 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41121 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41125 | This is what home could feel like
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41125 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41125, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41125 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41125 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41126 | After Rain : Suatu saat Aku Berhenti Menangisimu
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41126 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41126, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41126 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41126 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41127 | The Golden Compass = Kompas Emas
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41127 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41127, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41127 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41127 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41128 | The Heart of Glass : Tales from the Five Kingdoms = Jantung Kaca
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41128 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41128, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41128 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41128 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41129 | Ketika Cinta Bertasbih : Buku 2 Dwilogi Pembangun Jiwa
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41129 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41129, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41129 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41129 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41130 | Emak Ingin Naik Haji
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41130 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41130, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41130 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41130 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41131 | Kain Cinta Tanpa Batas
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41131 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41131, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41131 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41131 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41132 | Supernova : Akar
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41132 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41132, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41132 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41132 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41133 | Dewa Ruci : Sebuah Kisah Nyata Pelayaran Pertama Menaklukan Tujuh Samudra
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41133 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41133, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41133 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41133 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41217 | GOOSEBUMPS : Makhluk Mungil Pembawa Bencana
UPDATE catalogs SET Subject = 'Fiksi Amerika; Novel horor terjemahan' WHERE ID = 41217 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41217, '600', '#', '#', '$a Fiksi Amerika; Novel horor terjemahan', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41217 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Amerika; Novel horor terjemahan' WHERE CatalogId = 41217 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41045 | Bilangan Fu
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41045 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41045, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41045 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41045 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36107 | Contoh-contoh MC: Pidato Da'wah & Do'a
UPDATE catalogs SET Subject = 'Protokoler (MC); Pidato dakwah; Doa' WHERE ID = 36107 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36107, '600', '#', '#', '$a Protokoler (MC); Pidato dakwah; Doa', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36107 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Protokoler (MC); Pidato dakwah; Doa' WHERE CatalogId = 36107 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41033 | Ronggeng Dukuh Paruh
UPDATE catalogs SET Subject = 'Fiksi Indonesia; Novel' WHERE ID = 41033 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41033, '600', '#', '#', '$a Fiksi Indonesia; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41033 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Indonesia; Novel' WHERE CatalogId = 41033 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41255 | The Setting Sun
UPDATE catalogs SET Subject = 'Fiksi Jepang; Novel' WHERE ID = 41255 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41255, '600', '#', '#', '$a Fiksi Jepang; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41255 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Jepang; Novel' WHERE CatalogId = 41255 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41257 | No Longer Human
UPDATE catalogs SET Subject = 'Fiksi Jepang; Novel' WHERE ID = 41257 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41257, '600', '#', '#', '$a Fiksi Jepang; Novel', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41257 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Fiksi Jepang; Novel' WHERE CatalogId = 41257 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35876 | Supar Muchtar : Penuturan tentang perjalanan hidup seorang anak desa yang bercita-cita menjadi kepala desa, tapi tuhan menentukan lain
UPDATE catalogs SET Subject = 'Biografi' WHERE ID = 35876 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35876, '600', '#', '#', '$a Biografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35876 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Biografi' WHERE CatalogId = 35876 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35877 | Jejak Langkah Pak Harto : 28 Maret 1968 - 23 Maret 1973
UPDATE catalogs SET Subject = 'Biografi' WHERE ID = 35877 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35877, '600', '#', '#', '$a Biografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35877 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Biografi' WHERE CatalogId = 35877 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35957 | Suharto: Sebuah Biografi Politik
UPDATE catalogs SET Subject = 'Biografi' WHERE ID = 35957 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35957, '600', '#', '#', '$a Biografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35957 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Biografi' WHERE CatalogId = 35957 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41210 | Gus Dur, The Reconciler
UPDATE catalogs SET Subject = 'Biografi' WHERE ID = 41210 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41210, '600', '#', '#', '$a Biografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41210 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Biografi' WHERE CatalogId = 41210 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35862 | Perempuan, Politik dan Jurnalisme : Tujuhpuluh Tahun Toety Azis
UPDATE catalogs SET Subject = 'Biografi' WHERE ID = 35862 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35862, '600', '#', '#', '$a Biografi', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35862 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Biografi' WHERE CatalogId = 35862 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35879 | Umar Wirahadikusumah : Dari Peristiwa ke Peristiwa
UPDATE catalogs SET Subject = 'Biografi; Tokoh militer -- Indonesia' WHERE ID = 35879 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35879, '600', '#', '#', '$a Biografi; Tokoh militer -- Indonesia', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35879 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Biografi; Tokoh militer -- Indonesia' WHERE CatalogId = 35879 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41213 | Habibie dan Ainun (Arabic Version)
UPDATE catalogs SET Subject = 'Biografi; Tokoh nasional -- Indonesia; Novel biografis' WHERE ID = 41213 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41213, '600', '#', '#', '$a Biografi; Tokoh nasional -- Indonesia; Novel biografis', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41213 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Biografi; Tokoh nasional -- Indonesia; Novel biografis' WHERE CatalogId = 41213 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 35918 | Meniti Sejarah Menuju Kejayaan Islam
UPDATE catalogs SET Subject = 'Indonesia -- Sejarah' WHERE ID = 35918 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 35918, '600', '#', '#', '$a Indonesia -- Sejarah', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 35918 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Indonesia -- Sejarah' WHERE CatalogId = 35918 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 36089 | Jejak-jejak Pahlawan: Dari Sultan Agung hingga Syekh Yusuf
UPDATE catalogs SET Subject = 'Indonesia -- Sejarah' WHERE ID = 36089 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 36089, '600', '#', '#', '$a Indonesia -- Sejarah', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 36089 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Indonesia -- Sejarah' WHERE CatalogId = 36089 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

-- ID: 41209 | Indonesia Menggugat : Pidato Pembelaan Bung Karno Di Muka Hukum Kolonial
UPDATE catalogs SET Subject = 'Indonesia -- Sejarah' WHERE ID = 41209 AND (Subject IS NULL OR TRIM(Subject) = '');
INSERT INTO catalog_ruas (CatalogId, Tag, Indicator1, Indicator2, Value, Sequence, active)
SELECT 41209, '600', '#', '#', '$a Indonesia -- Sejarah', 1, 1
WHERE NOT EXISTS (SELECT 1 FROM catalog_ruas WHERE CatalogId = 41209 AND Tag = '600');
UPDATE catalog_ruas SET Value = '$a Indonesia -- Sejarah' WHERE CatalogId = 41209 AND Tag = '600' AND (Value IS NULL OR Value = '$a' OR Value = '$a ' OR TRIM(Value) = '' OR TRIM(Value) = '$a');

COMMIT;

-- Verifikasi hasil:
-- SELECT COUNT(*) FROM catalogs WHERE Subject IS NULL OR TRIM(Subject) = '';
-- (Harus tersisa 18 data dummy)
