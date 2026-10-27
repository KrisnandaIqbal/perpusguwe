<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();

// -----------------------------------------------------------------------------
// Sheet 1: Log Hari Ini (27 Okt 2026)
// -----------------------------------------------------------------------------
$sheetToday = $spreadsheet->getActiveSheet();
$sheetToday->setTitle('Log 27 Okt 2026');

$headers = [
    'Tanggal',
    'Migrasi - Jenis',
    'Aktivitas / Uraian Pekerjaan',
    'Modul / Objek',
    'Penanggung Jawab',
    'Status',
    'Catatan / Hasil',
    'Bukti 1',
    'Bukti 2',
    'Bukti 3'
];

$rowsToday = [
    [
        '10/27/2026',
        'Konfigurasi',
        'Sinkronisasi database lokal InlisLite (import koleksi/eksemplar 5.582 katalog & 8.933 eksemplar), penambahan tabel RDA (mst_content_type, mst_media_type, mst_carrier_type), dan pengaktifan ekstensi PHP zip & gmp di Laragon',
        'Inlislite - Database & PHP Config',
        'Rizki',
        'Selesai',
        'Memperbaiki error tabel RDA tidak ditemukan saat buka form katalog dan error Class ZipArchive not found saat upload Excel; migrasi buku tamu unit & durasi pinjam 10 hari kerja sudah terpasang',
        '20261027_1_sinkronisasi_db_koleksi_rda.txt',
        '',
        ''
    ],
    [
        '10/27/2026',
        'Modul/Fitur',
        'Penyembunyian tombol Login/Dashboard pada navbar publik (akses admin via direct URL /login) dan pertukaran posisi menu (Buku Tamu ke navbar utama, Pendaftaran Anggota ke dalam dropdown Layanan Mandiri)',
        'Inlislite - Navbar OPAC (layout.php)',
        'Rizki',
        'Selesai',
        'Commit 16da10d (push ke main); tombol disembunyikan menggunakan CSS/d-none tanpa menghapus struktur kode',
        '20261027_2_navbar_hide_login_tukar_menu.txt',
        '',
        ''
    ],
    [
        '10/27/2026',
        'Modul/Fitur',
        'Penggantian foto default wanita (nophoto.jpg) dengan avatar vector dinamis berbasis gender (Laki-laki / Perempuan / Netral) pada tabel Daftar Anggota dan profil pemustaka',
        'Inlislite - Modul Keanggotaan (Api/Anggota.php, anggota_helper.php)',
        'Rizki',
        'Selesai',
        'Commit 16da10d; menambahkan file vector baru avatar_male.svg, avatar_female.svg, avatar_neutral.svg dan menimpa file nophoto.jpg lama',
        '20261027_3_avatar_dinamis_gender.txt',
        '',
        ''
    ],
    [
        '10/27/2026',
        'Modul/Fitur',
        'Revisi butir nomor 2 kartu Hak Akses Anggota pada pendaftaran online menjadi "Mendapatkan Kartu Anggota yang telah Terintegrasi dengan Kartu Uang Elektronik (KUE)"',
        'Inlislite - Registrasi Online (index.php)',
        'Rizki',
        'Selesai',
        'Sesuai catatan revisi pada kartu Trello ("Siapin Website"); commit 16da10d',
        '20261027_4_revisi_trello_kue.txt',
        '',
        ''
    ],
    [
        '10/27/2026',
        'Data',
        'Pengosongan seluruh data anggota uji coba (members, relasi sirkulasi/kategori, file foto di uploads/anggota) dan reset counter nomor anggota',
        'Inlislite - Database (Tabel members & relasi)',
        'Rizki',
        'Selesai',
        'Data sebelum dikosongkan telah dicadangkan ke artifacts/backup_members_20261027.sql; counter NomorAnggotaCounter di-reset kembali ke 0',
        '20261027_5_pengosongan_data_anggota.txt',
        '',
        ''
    ],
];

function applySheetStyling($sheet, $headers, $rows) {
    // Write headers
    $col = 'A';
    foreach ($headers as $header) {
        $sheet->setCellValue($col . '1', $header);
        $col++;
    }
    $lastCol = chr(ord('A') + count($headers) - 1);

    // Style Header
    $headerRange = "A1:{$lastCol}1";
    $sheet->getStyle($headerRange)->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size' => 11,
            'name' => 'Segoe UI',
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '1B4D3E'], // Dark green Kemnaker
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
    ]);
    $sheet->getRowDimension(1)->setRowHeight(28);

    // Write Data Rows
    $rowIndex = 2;
    foreach ($rows as $row) {
        $col = 'A';
        foreach ($row as $val) {
            $sheet->setCellValue($col . $rowIndex, $val);
            $col++;
        }
        $sheet->getRowDimension($rowIndex)->setRowHeight(42);
        $rowIndex++;
    }
    $lastRow = $rowIndex - 1;

    // Data Styling
    $dataRange = "A2:{$lastCol}{$lastRow}";
    $sheet->getStyle($dataRange)->applyFromArray([
        'font' => [
            'name' => 'Segoe UI',
            'size' => 10,
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER,
            'wrapText' => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'D0D7DE'],
            ],
        ],
    ]);

    // Header Borders
    $sheet->getStyle($headerRange)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => '0E2A22'],
            ],
        ],
    ]);

    // Center specific columns: Tanggal, Migrasi-Jenis, PIC, Status, Bukti 1-3
    $sheet->getStyle("A2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("E2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("H2:{$lastCol}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

    // Status column highlight (Selesai = soft green)
    for ($r = 2; $r <= $lastRow; $r++) {
        $statusCell = "F{$r}";
        if ($sheet->getCell($statusCell)->getValue() === 'Selesai') {
            $sheet->getStyle($statusCell)->applyFromArray([
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E6F4EA'],
                ],
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => '137333'],
                ],
            ]);
        }
    }

    // Auto-size or set width
    $sheet->getColumnDimension('A')->setWidth(14); // Tanggal
    $sheet->getColumnDimension('B')->setWidth(18); // Jenis
    $sheet->getColumnDimension('C')->setWidth(46); // Uraian
    $sheet->getColumnDimension('D')->setWidth(30); // Modul
    $sheet->getColumnDimension('E')->setWidth(18); // PIC
    $sheet->getColumnDimension('F')->setWidth(14); // Status
    $sheet->getColumnDimension('G')->setWidth(40); // Catatan
    $sheet->getColumnDimension('H')->setWidth(36); // Bukti 1
    $sheet->getColumnDimension('I')->setWidth(20); // Bukti 2
    $sheet->getColumnDimension('J')->setWidth(20); // Bukti 3

    $sheet->freezePane('A2');
}

applySheetStyling($sheetToday, $headers, $rowsToday);

// -----------------------------------------------------------------------------
// Sheet 2: Riwayat Lengkap (19 - 27 Okt 2026)
// -----------------------------------------------------------------------------
$sheetAll = $spreadsheet->createSheet();
$sheetAll->setTitle('Semua Riwayat (Okt 2026)');

$pastRows = [
    ['10/19/2026', 'Modul/Fitur', 'Penambahan dropdown Status Non-ASN (Staf/Magang) pada pendaftaran online beserta validasi frontend dan backend; perbaikan bug JavaScript yang membuat form tidak responsif', 'Inlislite - Pendaftaran online', 'Rizki', 'Selesai', 'Kolom members.status_non_asn aktif; merge dengan origin/main tanpa konflik', '20261019_add_status_non_asn_to_members.sql', '', ''],
    ['10/19/2026', 'Data', 'Migrasi kolom status_non_asn pada tabel members beserta backup dan rollback', 'Inlislite - tabel members', 'Rizki', 'Selesai', 'artifacts/20261019_add_status_non_asn_to_members.sql', 'backup_members_before_status_non_asn.sql', '', ''],
    ['10/19/2026', 'Modul/Fitur', 'Aturan masa berlaku keanggotaan Non-ASN (Magang 6 bulan, Staf 1 tahun dari tanggal konfirmasi), helper hitungMasaBerlaku, preview di modal persetujuan', 'Inlislite - Keanggotaan', 'Rizki', 'Selesai', 'Diuji unit (31 Agustus + 6 bulan = 28/29 Februari) dan manual di browser', 'members.php', '', ''],
    ['10/19/2026', 'Modul/Fitur', 'Koreksi label Eselon I dan Eselon II pada dropdown Unit Kerja dan Biro/Direktorat/Unit (form publik dan form admin)', 'Inlislite - Pendaftaran online & Keanggotaan', 'Rizki', 'Selesai', 'Commit 1f04d3d dan 0c57eac; import/export Excel tidak diubah', 'index.php', '', ''],
    ['10/19/2026', 'Modul/Fitur', 'Cek duplikat nomor WhatsApp dan penambahan field Jenis Kelamin (Sex_id) pada pendaftaran online', 'Inlislite - Pendaftaran online', 'Rizki', 'Selesai', 'Pakai kolom Sex_id dan master jenis_kelamin yang sudah ada; tampil di modal persetujuan', 'index.php', '', ''],
    ['10/19/2026', 'Modul/Fitur', 'Perbaikan error simpan pada form Ubah Anggota (Undefined property AnggotaController::$db) dan penghapusan fitur ambil foto lewat kamera', 'Inlislite - Keanggotaan', 'Rizki', 'Selesai', 'Ubah email dan unggah foto diuji ulang di browser lokal', 'Anggota.php', '', ''],
    ['10/19/2026', 'Modul/Fitur', 'Perubahan masa pinjam menjadi 10 hari kerja (Sabtu, Minggu, dan hari libur tidak dihitung) memakai tabel holidays; perbaikan kolom Hari Terlambat', 'Inlislite - Sirkulasi (Peminjaman)', 'Rizki', 'Selesai', 'Helper hitungJatuhTempoHariKerja aktif; migrasi MaxLoanDays 10 hari diterapkan di database', '20261019_update_max_loan_days_to_10_business_days.sql', '', ''],
    ['10/20/2026', 'Modul/Fitur', 'Perbaikan gagal mengembalikan koleksi (Undefined variable $actualReturnDate) pada halaman Entri Pengembalian', 'Inlislite - Sirkulasi (Pengembalian)', 'Rizki', 'Selesai', 'Commit 003ebf4; diuji cek buku, kembali tepat waktu, dan kembali terlambat', 'Pengembalian.php', '', ''],
    ['10/21/2026', 'Modul/Fitur', 'Batas peminjaman ASN dan Non-ASN disamakan menjadi 2 eksemplar (MaxPinjamKoleksi)', 'Inlislite - Sirkulasi / jenis_anggota', 'Rizki', 'Selesai', 'Dikonfigurasi pada master jenis anggota; fallback controller diselaraskan', 'aturan_anggota.sql', '', ''],
    ['10/21/2026', 'Modul/Fitur', 'Penyaringan unit kerja "Kementerian Ketenagakerjaan" dari dropdown pendaftaran online (R2)', 'Inlislite - Pendaftaran online', 'Rizki', 'Selesai', 'Commit 6ead215; filter lewat controller (id != 1), data master dan anggota lama tidak berubah', 'Registrasi.php', '', ''],
    ['10/21/2026', 'Modul/Fitur', 'Penambahan status Magang Nasional dan Magang Biasa beserta aturan masa berlaku, sinkronisasi tanggal target, dan import Excel (R3)', 'Inlislite - Keanggotaan / Pendaftaran online', 'Rizki', 'Selesai', 'Commit ae6a4f1, 8815e23, 132e48b; Magang Biasa +1 bulan, Magang Nasional serentak via tanggal target', '20261021_update_status_non_asn_enum.sql', '', ''],
    ['10/21/2026', 'Data', 'Migrasi enum status_non_asn (magang_nasional, magang_biasa) dan kolom target_expire_date pada tabel jenis_anggota', 'Inlislite - tabel members & jenis_anggota', 'Rizki', 'Selesai', 'artifacts/20261021_update_status_non_asn_enum.sql dan 20261021_add_target_expire_date_to_jenis_anggota.sql dieksekusi', '20261021_add_target_expire_date_to_jenis_anggota.sql', '', ''],
    ['10/21/2026', 'Modul/Fitur', 'Restriksi kartu kategori keanggotaan pendaftaran online publik khusus ASN dan Non-ASN', 'Inlislite - Pendaftaran online', 'Rizki', 'Selesai', 'Commit 2c12c93; tampilan form registrasi rapi dan terarah', 'index.php', '', ''],
    ['10/21/2026', 'Modul/Fitur', 'Pembaruan petunjuk NIP/NIK pendaftaran online: letak di atas input, warna biru, teks sukses hijau bawah dihilangkan, badge hitung digit real-time (R1)', 'Inlislite - Pendaftaran online', 'Rizki', 'Selesai', 'Commit 11a0e1a; badge live ✔ 18/18 digit (NIP) dan ✔ 16/16 digit (NIK) hijau saat valid, ✖ merah saat kurang', 'index.php', '', ''],
    ['10/21/2026', 'Modul/Fitur', 'Pembaruan navigasi navbar dan footer OPAC: tambah menu "Pendaftaran Anggota" dan sembunyikan menu peminjaman/pengembalian mandiri (R5.1 & R5.5)', 'Inlislite - OPAC (Layout)', 'Rizki', 'Selesai', 'Commit 4ff07b3; menu pendaftaran langsung di navbar, menu mandiri tersembunyi (tetap aktif via URL)', 'layout.php', '', ''],
    ['10/21/2026', 'Modul/Fitur', 'Visualisasi buku berstatus sedang dipinjam berwarna merah dan tombol label "Sedang Dipinjam" di OPAC (R5.14)', 'Inlislite - OPAC (Detail Buku)', 'Rizki', 'Selesai', 'Commit de82f38; eksemplar fisik & DRM berstatus pinjam memakai row table-danger, badge merah, dan tombol merah', 'detail.php', '', ''],
    ['10/21/2026', 'Tampilan', 'Standardisasi font antarmuka global menggunakan Plus Jakarta Sans (OPAC, buku tamu, login, pendaftaran, admin) (R5.11)', 'Inlislite - OPAC, Buku Tamu, Admin', 'Rizki', 'Selesai', 'Commit cda5455; Google Fonts Plus Jakarta Sans diterapkan menyeluruh', 'layout.php', '', ''],
    ['10/21/2026', 'Modul/Fitur', 'Sinkronisasi waktu peminjaman dengan master Jenis Anggota (MaxLoanDays) dan perbaikan teks struk dinamis', 'Inlislite - Sirkulasi (Peminjaman & Mandiri)', 'Rizki', 'Selesai', 'Commit 1990e6c; teks struk tidak lagi hardcoded 7 hari melainkan membaca MaxLoanDays jenis anggota', 'Peminjaman.php', '', ''],
];

$allRows = array_merge($pastRows, $rowsToday);
applySheetStyling($sheetAll, $headers, $allRows);

// -----------------------------------------------------------------------------
// Sheet 3: Rincian Bukti 27 Okt 2026
// -----------------------------------------------------------------------------
$sheetBukti = $spreadsheet->createSheet();
$sheetBukti->setTitle('Rincian Bukti (27 Okt 2026)');

$buktiHeaders = ['No', 'Nama File Bukti', 'Kategori', 'Commit / Status', 'Path Lokal', 'Link GitHub'];
$buktiRows = [
    [
        '1',
        '20261027_1_sinkronisasi_db_koleksi_rda.txt',
        'Database & PHP',
        'Local DB / 16da10d',
        'artifacts/bukti/20261027_1_sinkronisasi_db_koleksi_rda.txt',
        'https://github.com/adminperpuskemnaker/inlislitekemnaker/blob/main/artifacts/bukti/20261027_1_sinkronisasi_db_koleksi_rda.txt'
    ],
    [
        '2',
        '20261027_2_navbar_hide_login_tukar_menu.txt',
        'Navbar OPAC',
        'Commit 16da10d',
        'artifacts/bukti/20261027_2_navbar_hide_login_tukar_menu.txt',
        'https://github.com/adminperpuskemnaker/inlislitekemnaker/blob/main/artifacts/bukti/20261027_2_navbar_hide_login_tukar_menu.txt'
    ],
    [
        '3',
        '20261027_3_avatar_dinamis_gender.txt',
        'Avatar Dinamis Gender',
        'Commit 16da10d',
        'artifacts/bukti/20261027_3_avatar_dinamis_gender.txt',
        'https://github.com/adminperpuskemnaker/inlislitekemnaker/blob/main/artifacts/bukti/20261027_3_avatar_dinamis_gender.txt'
    ],
    [
        '4',
        '20261027_4_revisi_trello_kue.txt',
        'Hak Akses Anggota KUE',
        'Commit 16da10d',
        'artifacts/bukti/20261027_4_revisi_trello_kue.txt',
        'https://github.com/adminperpuskemnaker/inlislitekemnaker/blob/main/artifacts/bukti/20261027_4_revisi_trello_kue.txt'
    ],
    [
        '5',
        '20261027_5_pengosongan_data_anggota.txt',
        'Reset Anggota Uji Coba',
        'DB Truncate / Backup OK',
        'artifacts/bukti/20261027_5_pengosongan_data_anggota.txt',
        'https://github.com/adminperpuskemnaker/inlislitekemnaker/blob/main/artifacts/bukti/20261027_5_pengosongan_data_anggota.txt'
    ],
];

// Write Bukti Header
$col = 'A';
foreach ($buktiHeaders as $bh) {
    $sheetBukti->setCellValue($col . '1', $bh);
    $col++;
}
$sheetBukti->getStyle('A1:F1')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11, 'name' => 'Segoe UI'],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F5132']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
]);
$sheetBukti->getRowDimension(1)->setRowHeight(28);

$bRowIdx = 2;
foreach ($buktiRows as $br) {
    $col = 'A';
    foreach ($br as $cIdx => $bVal) {
        $sheetBukti->setCellValue($col . $bRowIdx, $bVal);
        // Add actual hyperlink for URL
        if ($cIdx === 5) {
            $sheetBukti->getCell($col . $bRowIdx)->getHyperlink()->setUrl($bVal);
            $sheetBukti->getStyle($col . $bRowIdx)->getFont()->setUnderline(true)->getColor()->setRGB('0D6EFD');
        }
        $col++;
    }
    $sheetBukti->getRowDimension($bRowIdx)->setRowHeight(26);
    $bRowIdx++;
}
$lastBRow = $bRowIdx - 1;

$sheetBukti->getStyle("A2:F{$lastBRow}")->applyFromArray([
    'font' => ['name' => 'Segoe UI', 'size' => 10],
    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']]],
]);
$sheetBukti->getStyle("A2:A{$lastBRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheetBukti->getStyle("C2:D{$lastBRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheetBukti->getColumnDimension('A')->setWidth(6);
$sheetBukti->getColumnDimension('B')->setWidth(46);
$sheetBukti->getColumnDimension('C')->setWidth(26);
$sheetBukti->getColumnDimension('D')->setWidth(24);
$sheetBukti->getColumnDimension('E')->setWidth(50);
$sheetBukti->getColumnDimension('F')->setWidth(65);

$sheetBukti->freezePane('A2');

// Set active sheet back to Sheet 1
$spreadsheet->setActiveSheetIndex(0);

// Save to artifacts/Log_Pekerjaan_Rizki_InlisLite.xlsx
$outputPath = __DIR__ . '/artifacts/Log_Pekerjaan_Rizki_InlisLite.xlsx';
$writer = new Xlsx($spreadsheet);
$writer->save($outputPath);

echo "SUCCESS: Spreadsheet saved to {$outputPath}\n";
