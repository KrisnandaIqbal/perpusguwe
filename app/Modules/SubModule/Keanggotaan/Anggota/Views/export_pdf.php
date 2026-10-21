<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Anggota Perpustakaan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 15mm 15mm 15mm;
        }
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #222;
            line-height: 1.45;
            margin: 0;
            padding: 0;
        }
        .page {
            page-break-after: always;
            position: relative;
            min-height: 255mm;
        }
        .page:last-child {
            page-break-after: avoid;
        }

        /* Tampilan Khusus Layar Pratinjau (A4 Paper Effect) */
        @media screen {
            body {
                background-color: #525659;
                padding: 24px 10px;
                display: flex;
                flex-direction: column;
                align-items: center;
                box-sizing: border-box;
            }
            .page {
                background: #ffffff;
                width: 210mm;
                max-width: 96%;
                min-height: 297mm;
                box-sizing: border-box;
                padding: 15mm;
                margin: 0 auto 24px auto;
                box-shadow: 0 6px 20px rgba(0,0,0,0.35);
                border-radius: 3px;
            }
            .footer-info {
                left: 15mm !important;
                right: 15mm !important;
                bottom: 10mm !important;
                width: calc(100% - 30mm) !important;
            }
        }
        
        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-bottom: 2.5px solid #1a365d;
            padding-bottom: 8px;
            margin-bottom: 14px;
        }
        .kop-instansi {
            font-size: 11pt;
            font-weight: bold;
            color: #2b6cb0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-title {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2px 0;
            color: #1a202c;
        }
        .kop-address {
            font-size: 8.5pt;
            color: #4a5568;
            margin-top: 2px;
        }

        /* Dokumen Title Bar */
        .doc-title-box {
            text-align: center;
            margin-bottom: 14px;
            padding: 6px 0;
            background-color: #f7fafc;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        .doc-title {
            font-size: 11.5pt;
            font-weight: bold;
            color: #1a202c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        /* Header Box (Kartu & Pasfoto) */
        .header-box {
            width: 100%;
            margin-bottom: 12px;
            border: 1px solid #e2e8f0;
            background-color: #ffffff;
            border-collapse: collapse;
        }
        .header-info-cell {
            padding: 10px 14px;
            vertical-align: top;
        }
        .photo-cell {
            width: 110px;
            text-align: center;
            vertical-align: middle;
            padding: 10px;
            border-left: 1px solid #e2e8f0;
            background-color: #fbfbfa;
        }
        .photo-box {
            width: 95px;
            height: 125px;
            border: 1px solid #cbd5e0;
            background-color: #edf2f7;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
            color: #718096;
            margin: 0 auto;
        }
        .photo-img {
            width: 95px;
            height: 125px;
            object-fit: cover;
            border: 1px solid #cbd5e0;
        }

        /* Section Header */
        .section-header {
            background-color: #edf2f7;
            border-left: 4px solid #2b6cb0;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 9.5pt;
            margin-top: 12px;
            margin-bottom: 8px;
            color: #2d3748;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        /* Data Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .data-table td {
            padding: 4.5px 6px;
            vertical-align: top;
            font-size: 9.5pt;
            border-bottom: 1px solid #f0f2f5;
        }
        .data-label {
            width: 32%;
            font-weight: bold;
            color: #4a5568;
        }
        .data-separator {
            width: 3%;
            text-align: center;
            color: #718096;
        }
        .data-value {
            width: 65%;
            color: #1a202c;
        }

        /* Tanda Tangan */
        .sign-table {
            width: 100%;
            margin-top: 36px;
            border-collapse: collapse;
        }
        .sign-table td {
            width: 50%;
            vertical-align: top;
            font-size: 9.5pt;
            text-align: center;
        }
        .sign-space {
            height: 65px;
        }

        /* Footer Info */
        .footer-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
            font-size: 8pt;
            color: #718096;
            width: 100%;
        }
    </style>
</head>
<body>
<?php 
foreach ($membersData as $item): 
    $m = $item['member'];
    $photoPath = $item['photoPath'];

    // Cek kategori ASN / Non-ASN
    $isAsn = false;
    $jaText = strtolower($m->JenisAnggota ?? '');
    if (strpos($jaText, 'asn') !== false && strpos($jaText, 'non') === false) {
        $isAsn = true;
    }

    $identityLabel = $isAsn ? 'Nomor Induk Pegawai' : 'Nomor Induk Kependudukan';
?>
    <div class="page">
        <!-- KOP SURAT -->
        <table class="kop-table" cellpadding="0" cellspacing="0">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="kop-instansi">KEMENTERIAN KETENAGAKERJAAN REPUBLIK INDONESIA</div>
                    <div class="kop-title"><?= htmlspecialchars($namaPerpustakaan ?? 'PERPUSTAKAAN KEMENTERIAN KETENAGAKERJAAN') ?></div>
                    <div class="kop-address"><?= htmlspecialchars($alamatPerpustakaan ?: 'Jl. Jenderal Gatot Subroto Kav. 51, RT.5/RW.4, Kuningan Timur, Setiabudi, Jakarta Selatan 12950') ?></div>
                </td>
            </tr>
        </table>

        <!-- JUDUL DOKUMEN -->
        <div class="doc-title-box">
            <div class="doc-title">LEMBAR DATA ANGGOTA PERPUSTAKAAN</div>
        </div>

        <!-- RINGKASAN STATUS & PASFOTO -->
        <table class="header-box" cellpadding="0" cellspacing="0">
            <tr>
                <td class="header-info-cell">
                    <table cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style="width: 160px; padding: 3px 0; font-weight: bold; color: #4a5568;">No. Anggota / Barcode</td>
                            <td style="width: 15px; text-align: center;">:</td>
                            <td style="font-weight: bold; font-size: 11pt; color: #1a365d;"><?= htmlspecialchars($m->MemberNo ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; font-weight: bold; color: #4a5568;">Status Keanggotaan</td>
                            <td style="text-align: center;">:</td>
                            <td style="font-weight: bold; color: <?= ($m->StatusAnggota == 'Aktif') ? '#22543d' : '#744210' ?>;">
                                <?= htmlspecialchars($m->StatusAnggota ?? '-') ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; font-weight: bold; color: #4a5568;">Kategori Keanggotaan</td>
                            <td style="text-align: center;">:</td>
                            <td><strong><?= htmlspecialchars($m->JenisAnggota ?? '-') ?></strong></td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; font-weight: bold; color: #4a5568;">Tanggal Registrasi</td>
                            <td style="text-align: center;">:</td>
                            <td><?= !empty($m->RegisterDate) ? date('d/m/Y', strtotime($m->RegisterDate)) : '-' ?></td>
                        </tr>
                        <tr>
                            <td style="padding: 3px 0; font-weight: bold; color: #4a5568;">Masa Berlaku Kartu</td>
                            <td style="text-align: center;">:</td>
                            <td><?= !empty($m->EndDate) ? date('d/m/Y', strtotime($m->EndDate)) : '-' ?></td>
                        </tr>
                    </table>
                </td>
                <td class="photo-cell">
                    <?php if (!empty($photoPath) && file_exists($photoPath)): ?>
                        <?php 
                            $type = pathinfo($photoPath, PATHINFO_EXTENSION);
                            $data = file_get_contents($photoPath);
                            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        ?>
                        <img src="<?= $base64 ?>" class="photo-img" alt="Foto Pemustaka">
                    <?php else: ?>
                        <div class="photo-box">
                            <table style="width: 100%; height: 100%;">
                                <tr>
                                    <td style="text-align: center; vertical-align: middle;">
                                        Pasfoto<br>3 &times; 4
                                    </td>
                                </tr>
                            </table>
                        </div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <!-- 01. DATA IDENTITAS & KEPEGAWAIAN -->
        <div class="section-header">01. Data Identitas & Kepegawaian</div>
        <table class="data-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="data-label">Nama Lengkap</td>
                <td class="data-separator">:</td>
                <td class="data-value"><strong><?= htmlspecialchars($m->Fullname ?? '-') ?></strong></td>
            </tr>
            <tr>
                <td class="data-label">Kategori Keanggotaan</td>
                <td class="data-separator">:</td>
                <td class="data-value"><?= htmlspecialchars($m->JenisAnggota ?? '-') ?></td>
            </tr>
            <?php if (!empty($m->status_non_asn)): ?>
            <tr>
                <td class="data-label">Status Non-ASN</td>
                <td class="data-separator">:</td>
                <td class="data-value"><strong><?= htmlspecialchars(ucfirst($m->status_non_asn)) ?></strong></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="data-label"><?= $identityLabel ?></td>
                <td class="data-separator">:</td>
                <td class="data-value">
                    <strong><?= htmlspecialchars($m->IdentityNo ?? '-') ?></strong>
                </td>
            </tr>
            <tr>
                <td class="data-label">Satuan Kerja / Unit Penempatan</td>
                <td class="data-separator">:</td>
                <td class="data-value"><strong><?= htmlspecialchars(format_unit_kerja($m->UnitKerja ?? null, $m->SubUnitKerja ?? null) ?: '-') ?></strong></td>
            </tr>
        </table>

        <!-- 02. KONTAK & ALAMAT DOMISILI -->
        <div class="section-header">02. Kontak & Alamat Domisili</div>
        <table class="data-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="data-label">Alamat Email</td>
                <td class="data-separator">:</td>
                <td class="data-value"><?= htmlspecialchars($m->Email ?? '-') ?></td>
            </tr>
            <tr>
                <td class="data-label">Nomor WhatsApp / HP</td>
                <td class="data-separator">:</td>
                <td class="data-value"><strong><?= htmlspecialchars($m->Phone ?? '-') ?></strong></td>
            </tr>
            <tr>
                <td class="data-label">Alamat Domisili Saat Ini</td>
                <td class="data-separator">:</td>
                <td class="data-value"><?= nl2br(htmlspecialchars($m->AddressNow ?? '-')) ?></td>
            </tr>
        </table>

        <!-- PENGESAHAN / TANDA TANGAN -->
        <table class="sign-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    Pemustaka Terdaftar,
                    <div class="sign-space"></div>
                    <strong><?= htmlspecialchars($m->Fullname ?? 'Pemustaka') ?></strong>
                </td>
                <td>
                    Jakarta, <?= $tglCetak ?><br>
                    Petugas Perpustakaan,
                    <div class="sign-space"></div>
                    <strong><?= htmlspecialchars($petugasNama ?? 'Pustakawan') ?></strong>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <table class="footer-info" cellpadding="0" cellspacing="0">
            <tr>
                <td style="text-align: left;">
                    Dicetak dari Sistem Perpustakaan Kemnaker RI pada: <?= date('d/m/Y H:i') ?> WIB
                </td>
            </tr>
        </table>
    </div>
<?php 
endforeach; 
?>
</body>
</html>
