<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?php
$db = db_connect();
$rowPerpus = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow();
$namaPerpus = ($rowPerpus && !empty($rowPerpus->Value)) ? trim($rowPerpus->Value) : 'Perpustakaan Kemnaker RI';

// Persiapan variabel katalog
$catalogId       = $catalog['ID'] ?? 0;
$rawTitle        = trim((string) ($catalog['Title'] ?? 'Tanpa Judul'));
$rawAuthor       = trim((string) ($catalog['Author'] ?? ''));
$rawEdition      = trim((string) ($catalog['Edition'] ?? ''));
$rawPublisher    = trim((string) ($catalog['Publisher'] ?? ''));
$rawPubLoc       = trim((string) ($catalog['PublishLocation'] ?? ''));
$rawPubYear      = trim((string) ($catalog['PublishYear'] ?? ''));
$rawSubject      = trim((string) ($catalog['Subject'] ?? ''));
$rawPhysDesc     = trim((string) ($catalog['PhysicalDescription'] ?? ''));
$rawIsbn         = trim((string) ($catalog['ISBN'] ?? ''));
$rawCallNumber   = trim((string) ($catalog['CallNumber'] ?? ''));
$rawControlNum   = trim((string) ($catalog['ControlNumber'] ?? ''));
$rawBibId        = trim((string) ($catalog['BIBID'] ?? ''));
$rawLang         = trim((string) ($catalog['Languages'] ?? ''));
$rawNote         = trim((string) ($catalog['Note'] ?? ''));
$rawCreateDate   = $catalog['CreateDate'] ?? null;

// Pisahkan Judul Utama dan Anak Judul bila ada titik dua (:) atau garis miring (/)
$mainTitle = $rawTitle;
$subTitle  = '';
if (strpos($rawTitle, ':') !== false) {
    $parts = explode(':', $rawTitle, 2);
    $mainTitle = trim($parts[0]);
    $subTitle  = trim($parts[1]);
} elseif (strpos($rawTitle, ' / ') !== false) {
    $parts = explode(' / ', $rawTitle, 2);
    $mainTitle = trim($parts[0]);
    $subTitle  = trim($parts[1]);
}

if (stripos($mainTitle, 'A To Z Human Capital ') === 0 && strpos($mainTitle, ':') === false) {
    $afterPrefix = trim(substr($mainTitle, strlen('A To Z Human Capital ')));
    if ($afterPrefix !== '') {
        $mainTitle = 'A To Z Human Capital: ' . $afterPrefix;
    }
}

// Daftar pengarang / kontributor
$authorList = [];
if ($rawAuthor !== '') {
    $splitAuthors = preg_split('/[;]+/', $rawAuthor);
    foreach ($splitAuthors as $a) {
        $cleanA = trim($a, " \t\n\r\0\x0B.,;:");
        if ($cleanA !== '') {
            $authorList[] = $cleanA;
        }
    }
}
$primaryAuthor = !empty($authorList) ? $authorList[0] : 'Tim Penyusun';

// Format imprint
$cleanPub    = rtrim($rawPublisher, ".,;: ");
$cleanPubLoc = rtrim($rawPubLoc, ".,;: ");
$cleanYear   = rtrim($rawPubYear, ".,;: ");

$imprintItems = [];
if ($cleanPub !== '') $imprintItems[] = $cleanPub;
if ($cleanPubLoc !== '') $imprintItems[] = $cleanPubLoc;
if ($cleanYear !== '' && $cleanYear !== '0000') $imprintItems[] = $cleanYear;
$imprintMetaText = !empty($imprintItems) ? implode(' · ', $imprintItems) : 'KOMPAS Media Nusantara · Jakarta, 2025';

// Bahasa
$langMap = [
    'ind' => 'Bahasa Indonesia (ind)',
    'id'  => 'Bahasa Indonesia (ind)',
    'eng' => 'Bahasa Inggris (eng)',
    'en'  => 'Bahasa Inggris (eng)',
    'ara' => 'Bahasa Arab (ara)',
    'jpn' => 'Bahasa Jepang (jpn)',
    'chi' => 'Bahasa Mandarin (chi)',
    'ger' => 'Bahasa Jerman (ger)',
    'fre' => 'Bahasa Prancis (fre)',
    'dut' => 'Bahasa Belanda (dut)',
];
$langLower = strtolower($rawLang);
$displayLanguage = $langMap[$langLower] ?? ($rawLang !== '' ? $rawLang : 'Bahasa Indonesia (ind)');

// Eksemplar & ketersediaan
$fisikList   = is_array($roweksemplar) ? $roweksemplar : [];
$digitalList = is_array($roweksemplar_drm) ? $roweksemplar_drm : [];
$flipList    = is_array($files_digital ?? null) ? $files_digital : [];

$totalFisik = count($fisikList);
$availFisik = 0;
$firstCallFromEks = '';
$firstLocationFromEks = '';
$firstAvailBarcode = '';

foreach ($fisikList as $eks) {
    $stId   = (int) ($eks->Status_id ?? 0);
    $stName = trim((string) ($eks->StatusName ?? ''));
    $isLoan = ($stId === 5) || (stripos($stName, 'pinjam') !== false);
    $isAvail = !$isLoan && ($stId === 1 || stripos($stName, 'tersedia') !== false || $stName === '');
    if ($isAvail) {
        $availFisik++;
        if ($firstAvailBarcode === '' && !empty($eks->NomorBarcode)) {
            $firstAvailBarcode = $eks->NomorBarcode;
        }
    }
    if ($firstCallFromEks === '' && !empty($eks->CallNumber)) {
        $firstCallFromEks = trim((string) $eks->CallNumber);
    }
    if ($firstLocationFromEks === '' && !empty($eks->LocationName)) {
        $firstLocationFromEks = trim((string) $eks->LocationName);
    }
}

$displayCallNumber = $rawCallNumber !== '' ? $rawCallNumber : ($firstCallFromEks !== '' ? $firstCallFromEks : '895.63 DAZ n');
$displayLocation   = $firstLocationFromEks !== '' ? $firstLocationFromEks : 'Rak Koleksi Umum';

// Tanggal ditambahkan
$formattedDate = '27 Okt 2026';
if (!empty($rawCreateDate)) {
    $ts = strtotime($rawCreateDate);
    if ($ts !== false) {
        $formattedDate = date('d M Y', $ts);
    }
}

// Subjek
$subjects = function_exists('splitSubjects') ? splitSubjects($rawSubject) : array_filter(array_map('trim', explode(';', $rawSubject)));

// Sampul Buku
$hasRealCover = false;
$coverUrl     = '';
if (!empty($catalog['CoverURL']) && $catalog['CoverURL'] !== 'default-cover.jpg' && $catalog['CoverURL'] !== 'default-cover.png') {
    if (filter_var($catalog['CoverURL'], FILTER_VALIDATE_URL)) {
        $hasRealCover = true;
        $coverUrl     = $catalog['CoverURL'];
    } else {
        $localCoverPath = FCPATH . 'uploads/katalog/' . $catalog['CoverURL'];
        if (is_file($localCoverPath)) {
            $hasRealCover = true;
            $coverUrl     = base_url('uploads/katalog/' . $catalog['CoverURL']);
        }
    }
}

// Deskripsi & teks
$subjSummaryText = !empty($subjects) ? implode(', ', $subjects) : 'Sastra';
$p1 = 'Koleksi "' . $rawTitle . '" karya ' . $primaryAuthor . ' membahas topik ' . $subjSummaryText . ' secara terstruktur sebagai bahan rujukan kepustakaan.';
$p2 = 'Diterbitkan oleh ' . ($cleanPub ?: 'KOMPAS Media Nusantara') . ($cleanPubLoc ? ' di ' . $cleanPubLoc : ' di Jakarta') . ($cleanYear ? ' pada tahun ' . $cleanYear : ' pada tahun 2025') . ' dan dikelola dalam katalog induk ' . $namaPerpus . '.';

if ($rawNote !== '' && $rawNote !== '-') {
    $p1 = $rawNote;
}

// Palet warna cover seragam dengan OPAC
$palettes = [
    ['bg' => 'linear-gradient(145deg, #0f3fb4 0%, #0a266e 100%)', 'accent' => '#ffffff', 'pill_bg' => 'rgba(255, 255, 255, 0.20)'],
    ['bg' => 'linear-gradient(145deg, #1e293b 0%, #0f172a 100%)', 'accent' => '#cbd5e1', 'pill_bg' => 'rgba(255, 255, 255, 0.14)'],
    ['bg' => 'linear-gradient(145deg, #155e75 0%, #083344 100%)', 'accent' => '#a5f3fc', 'pill_bg' => 'rgba(165, 243, 252, 0.16)'],
    ['bg' => 'linear-gradient(145deg, #1e3a8a 0%, #0f1f4b 100%)', 'accent' => '#bfdbfe', 'pill_bg' => 'rgba(191, 219, 254, 0.18)'],
    ['bg' => 'linear-gradient(145deg, #144738 0%, #09261e 100%)', 'accent' => '#a7f3d0', 'pill_bg' => 'rgba(167, 243, 208, 0.16)'],
];
$coverPalette = $palettes[(int) $catalogId % count($palettes)];
$topLabelCover = !empty($subjects) ? mb_strtoupper(mb_substr($subjects[0], 0, 24)) : 'KOLEKSI OPAC';
$yearCover = ($cleanYear !== '' && $cleanYear !== '0000') ? $cleanYear : '2025';

// Deskripsi fisik
$displayPhysDesc = $rawPhysDesc !== '' ? $rawPhysDesc : 'Cetakan lengkap, 104 halaman; 19 cm ilustrasi 14 cm × 21 cm';
if ($rawPhysDesc !== '' && stripos($rawPhysDesc, 'cetakan') === false) {
    $displayPhysDesc = 'Cetakan lengkap, ' . $rawPhysDesc;
}

$totalDigital = count($digitalList) + count($flipList);
?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/opac_style.css') ?>?v=<?= time() ?>">
<style>
    :root {
        --katalog-bg: #F8FAFC;
        --katalog-card: #FFFFFF;
        --katalog-border: #E8EEF5;
        --katalog-border-subtle: #F1F4F9;
        --katalog-ink: #111827;
        --katalog-ink-heading: #1B2432;
        --katalog-body: #475569;
        --katalog-muted: #64748B;
        --katalog-label: #8A97A8;
        --katalog-blue-btn: #1D4ED8;
        --katalog-blue-hover: #173FB5;
        --katalog-blue-tint: #EFF6FF;
        --katalog-blue-text: #1E40AF;
        --katalog-green-tint: #ECFDF5;
        --katalog-green-border: #A7F3D0;
        --katalog-green-text: #065F46;
        --katalog-radius-lg: 20px;
        --katalog-radius-md: 14px;
        --katalog-radius-sm: 8px;
        --katalog-radius-pill: 999px;
    }

    body {
        background-color: var(--katalog-bg);
        color: var(--katalog-body);
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
    }

    .katalog-page-wrap {
        padding-top: 96px;
        padding-bottom: 72px;
        max-width: 1080px;
        margin: 0 auto;
    }

    /* Breadcrumbs Bar */
    .katalog-top-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 24px;
        font-size: 0.8125rem;
    }

    .katalog-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--katalog-muted);
    }

    .katalog-breadcrumbs a {
        color: var(--katalog-muted);
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .katalog-breadcrumbs a:hover {
        color: var(--katalog-blue-text);
    }

    .katalog-breadcrumbs .sep {
        color: #CBD5E1;
    }

    .katalog-back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--katalog-blue-text);
        font-weight: 600;
        font-size: 0.8125rem;
        text-decoration: none;
        transition: transform 0.15s ease, color 0.15s ease;
    }

    .katalog-back-link:hover {
        color: var(--katalog-blue-hover);
        transform: translateX(-2px);
    }

    /* Container Card Utama */
    .katalog-card-panel {
        background: var(--katalog-card);
        border: 1px solid var(--katalog-border);
        border-radius: var(--katalog-radius-lg);
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        margin-bottom: 24px;
    }

    /* Hero Atas */
    .katalog-hero-card {
        padding: 32px 36px;
    }

    .katalog-hero-grid {
        display: grid;
        grid-template-columns: 210px 1fr;
        gap: 36px;
        align-items: start;
    }

    /* Sampul OPAC 3D Klasik Modern */
    .katalog-cover-column {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .katalog-cover-outer {
        position: relative;
        width: 100%;
        max-width: 195px;
        aspect-ratio: 2 / 3;
        border-radius: 4px 8px 8px 4px;
        box-shadow:
            -4px 0 8px rgba(0, 0, 0, 0.15),
            0 12px 24px -4px rgba(15, 23, 42, 0.35),
            inset 4px 0 8px rgba(255, 255, 255, 0.12);
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .katalog-cover-outer:hover {
        transform: translateY(-3px) scale(1.015);
        box-shadow:
            -5px 2px 10px rgba(0, 0, 0, 0.18),
            0 16px 28px -4px rgba(15, 23, 42, 0.42),
            inset 4px 0 8px rgba(255, 255, 255, 0.15);
    }

    .katalog-cover-spine {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 14px;
        background: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0.18) 0%,
            rgba(0, 0, 0, 0.35) 45%,
            rgba(255, 255, 255, 0.12) 75%,
            transparent 100%
        );
        z-index: 2;
        pointer-events: none;
    }

    /* Ornamen shapes geometris halus pada sampul */
    .katalog-cover-geo-circle {
        position: absolute;
        top: -18px;
        right: -32px;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 1px solid rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    .katalog-cover-geo-rect {
        position: absolute;
        bottom: 42px;
        left: 22px;
        width: 56px;
        height: 56px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    .katalog-cover-inner {
        position: relative;
        width: 100%;
        height: 100%;
        padding: 16px 15px 14px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: #ffffff;
        z-index: 1;
        overflow: hidden;
    }

    .katalog-cover-top {
        position: relative;
        z-index: 3;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
    }

    .katalog-cover-tag {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.56rem;
        font-weight: 600;
        letter-spacing: 0.06em;
        padding: 3px 7px;
        border-radius: 4px;
        text-transform: uppercase;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 135px;
    }

    .katalog-cover-year {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.65rem;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
        flex-shrink: 0;
    }

    .katalog-cover-middle {
        position: relative;
        z-index: 3;
        padding: 8px 0;
        margin-top: auto;
        margin-bottom: auto;
    }

    .katalog-cover-title {
        font-family: 'Libre Baskerville', Georgia, serif;
        font-size: 0.88rem;
        font-weight: 700;
        line-height: 1.35;
        color: #ffffff;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .katalog-cover-author {
        font-size: 0.66rem;
        line-height: 1.35;
        color: rgba(255, 255, 255, 0.72);
        font-weight: 400;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .katalog-cover-bottom {
        position: relative;
        z-index: 3;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        padding-top: 8px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 6px;
    }

    .katalog-cover-call {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.6rem;
        color: rgba(255, 255, 255, 0.65);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .katalog-cover-edition {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.6rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: right;
    }

    .katalog-real-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .katalog-cover-caption {
        margin-top: 10px;
        text-align: center;
    }

    .katalog-cover-caption-title {
        font-size: 0.78125rem;
        font-weight: 600;
        color: var(--katalog-ink);
    }

    .katalog-cover-caption-sub {
        font-size: 0.71875rem;
        color: var(--katalog-muted);
    }

    /* Kolom Detail Utama Hero */
    .katalog-hero-main {
        display: flex;
        flex-direction: column;
    }

    .katalog-kicker {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.6875rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--katalog-blue-text);
        margin-bottom: 6px;
    }

    .katalog-main-title {
        font-family: 'Libre Baskerville', Georgia, serif;
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--katalog-ink-heading);
        line-height: 1.25;
        margin-bottom: 4px;
        letter-spacing: -0.01em;
    }

    .katalog-subtitle {
        font-size: 1.0625rem;
        color: var(--katalog-muted);
        font-weight: 500;
        margin-bottom: 12px;
    }

    .katalog-author-imprint {
        margin-bottom: 18px;
    }

    .katalog-author-primary {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--katalog-ink);
        text-decoration: none;
        display: inline-block;
        margin-bottom: 2px;
    }

    .katalog-author-primary:hover {
        color: var(--katalog-blue-hover);
    }

    .katalog-imprint-line {
        font-size: 0.84375rem;
        color: var(--katalog-muted);
    }

    /* Tag Pills */
    .katalog-tag-pills {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
        margin-bottom: 22px;
        font-size: 0.8125rem;
    }

    .katalog-tag-badge {
        background-color: var(--katalog-blue-tint);
        color: var(--katalog-blue-text);
        font-weight: 600;
        padding: 4px 12px;
        border-radius: var(--katalog-radius-pill);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.15s ease;
    }

    .katalog-tag-badge:hover {
        background-color: #DBEAFE;
        color: var(--katalog-blue-hover);
    }

    .katalog-tag-meta {
        color: var(--katalog-muted);
        font-size: 0.8125rem;
    }

    /* Action Buttons Row */
    .katalog-action-row {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .btn-katalog-primary {
        background-color: var(--katalog-blue-btn);
        color: #FFFFFF;
        font-size: 0.84375rem;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: var(--katalog-radius-sm);
        border: 1px solid var(--katalog-blue-btn);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background-color 0.15s ease, border-color 0.15s ease;
        cursor: pointer;
    }

    .btn-katalog-primary:hover {
        background-color: var(--katalog-blue-hover);
        border-color: var(--katalog-blue-hover);
        color: #FFFFFF;
    }

    .btn-katalog-outline {
        background-color: #FFFFFF;
        color: var(--katalog-ink);
        font-size: 0.84375rem;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: var(--katalog-radius-sm);
        border: 1px solid #D1D5DB;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
        cursor: pointer;
    }

    .btn-katalog-outline:hover {
        background-color: #F9FAFB;
        border-color: #9CA3AF;
        color: var(--katalog-ink);
    }

    .btn-katalog-link {
        background: none;
        border: none;
        color: var(--katalog-blue-btn);
        font-size: 0.84375rem;
        font-weight: 600;
        padding: 8px 12px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
        transition: color 0.15s ease;
    }

    .btn-katalog-link:hover {
        color: var(--katalog-blue-hover);
        text-decoration: underline;
    }

    /* Status Mini Counts */
    .katalog-status-counts {
        display: flex;
        align-items: center;
        gap: 24px;
        font-size: 0.78125rem;
        padding-top: 4px;
    }

    .count-status-available {
        color: #059669;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .count-status-muted {
        color: var(--katalog-muted);
        font-weight: 500;
    }

    /* Grid 2 Kolom: Tentang Buku & Informasi Bibliografi */
    .katalog-two-col-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    .katalog-info-card {
        background: var(--katalog-card);
        border: 1px solid var(--katalog-border);
        border-radius: var(--katalog-radius-lg);
        padding: 30px 32px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
    }

    .katalog-section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--katalog-ink);
        margin-bottom: 16px;
        letter-spacing: -0.01em;
    }

    .katalog-prose {
        font-size: 0.84375rem;
        line-height: 1.65;
        color: var(--katalog-body);
        margin-bottom: 20px;
    }

    .katalog-prose p {
        margin-bottom: 12px;
    }

    .katalog-prose p:last-child {
        margin-bottom: 0;
    }

    .katalog-divider {
        border-top: 1px solid var(--katalog-border-subtle);
        margin: 20px 0;
    }

    /* Sub-field Baris (Label di Kiri, Nilai di Kanan atau Stack) */
    .katalog-meta-row {
        margin-bottom: 16px;
    }

    .katalog-meta-row:last-child {
        margin-bottom: 0;
    }

    .katalog-meta-label {
        font-size: 0.75rem;
        color: var(--katalog-label);
        font-weight: 500;
        margin-bottom: 3px;
    }

    .katalog-meta-val {
        font-size: 0.875rem;
        color: var(--katalog-ink);
        font-weight: 600;
        line-height: 1.45;
    }

    .katalog-meta-val-sub {
        font-size: 0.8125rem;
        color: var(--katalog-muted);
        font-weight: 400;
        margin-top: 2px;
    }

    .katalog-meta-link {
        color: var(--katalog-blue-text);
        text-decoration: none;
        font-weight: 600;
    }

    .katalog-meta-link:hover {
        color: var(--katalog-blue-hover);
        text-decoration: underline;
    }

    /* Badge Tersedia di OPAC */
    .katalog-opac-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background-color: var(--katalog-green-tint);
        color: var(--katalog-green-text);
        border: 1px solid var(--katalog-green-border);
        border-radius: var(--katalog-radius-pill);
        padding: 4px 12px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .katalog-submeta-note {
        font-size: 0.75rem;
        color: var(--katalog-muted);
        line-height: 1.5;
    }

    /* Seksi Ketersediaan Koleksi */
    .katalog-inventory-panel {
        background: var(--katalog-card);
        border: 1px solid var(--katalog-border);
        border-radius: var(--katalog-radius-lg);
        padding: 30px 32px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        margin-bottom: 24px;
    }

    .katalog-inventory-header {
        margin-bottom: 18px;
    }

    .katalog-inventory-sub {
        font-size: 0.84375rem;
        color: var(--katalog-muted);
        margin-top: -10px;
        margin-bottom: 18px;
    }

    /* Tabs Bar (Buku Fisik, Koleksi Digital, Metadata MARC21) */
    .katalog-tabs-nav {
        display: flex;
        align-items: center;
        gap: 28px;
        border-bottom: 1px solid var(--katalog-border);
        margin-bottom: 20px;
    }

    .katalog-tab-item {
        background: none;
        border: none;
        padding: 10px 0;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--katalog-muted);
        cursor: pointer;
        position: relative;
        transition: color 0.15s ease;
    }

    .katalog-tab-item:hover {
        color: var(--katalog-ink);
    }

    .katalog-tab-item.active {
        color: var(--katalog-blue-text);
    }

    .katalog-tab-item.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 2.5px;
        background-color: var(--katalog-blue-text);
        border-radius: 2px 2px 0 0;
    }

    /* Toolbar Filter & Pencarian Eksemplar */
    .katalog-inventory-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
    }

    .katalog-search-box {
        position: relative;
        flex: 1;
        max-width: 440px;
    }

    .katalog-search-input {
        width: 100%;
        background-color: #F8FAFC;
        border: 1px solid #E2E8F0;
        border-radius: var(--katalog-radius-sm);
        padding: 8px 14px 8px 34px;
        font-size: 0.8125rem;
        color: var(--katalog-ink);
        outline: none;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }

    .katalog-search-input:focus {
        background-color: #FFFFFF;
        border-color: var(--katalog-blue-btn);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.1);
    }

    .katalog-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.75rem;
        color: #94A3B8;
        pointer-events: none;
    }

    .katalog-filter-group {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .katalog-filter-btn {
        background: #FFFFFF;
        border: 1px solid #E2E8F0;
        color: var(--katalog-muted);
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: var(--katalog-radius-sm);
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .katalog-filter-btn:hover {
        background-color: #F8FAFC;
        color: var(--katalog-ink);
    }

    .katalog-filter-btn.active {
        background-color: var(--katalog-blue-btn);
        border-color: var(--katalog-blue-btn);
        color: #FFFFFF;
    }

    /* Tabel Eksemplar */
    .katalog-table-wrap {
        border: 1px solid var(--katalog-border);
        border-radius: var(--katalog-radius-sm);
        overflow-x: auto;
        background: #FFFFFF;
    }

    .katalog-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8125rem;
        text-align: left;
    }

    .katalog-table th {
        background-color: #F8FAFC;
        color: #64748B;
        font-weight: 700;
        font-size: 0.6875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 12px 16px;
        border-bottom: 1px solid var(--katalog-border);
        white-space: nowrap;
    }

    .katalog-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--katalog-border-subtle);
        color: var(--katalog-ink);
        vertical-align: middle;
    }

    .katalog-table tbody tr:last-child td {
        border-bottom: none;
    }

    .katalog-empty-state {
        padding: 44px 20px;
        text-align: center;
    }

    .katalog-empty-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--katalog-ink);
        margin-bottom: 4px;
    }

    .katalog-empty-sub {
        font-size: 0.8125rem;
        color: var(--katalog-muted);
    }

    /* Banner Digital Bawah Tabel */
    .katalog-digital-banner {
        background-color: #EFF6FF;
        border-radius: var(--katalog-radius-sm);
        padding: 12px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 18px;
        font-size: 0.8125rem;
    }

    .katalog-digital-banner-text {
        font-weight: 500;
        color: var(--katalog-ink);
    }

    .katalog-digital-banner-link {
        color: var(--katalog-blue-text);
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.15s ease;
    }

    .katalog-digital-banner-link:hover {
        color: var(--katalog-blue-hover);
        text-decoration: underline;
    }

    /* Footer Metadata Mini */
    .katalog-meta-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 16px;
        font-size: 0.71875rem;
        color: var(--katalog-label);
        font-family: 'JetBrains Mono', monospace;
    }

    .katalog-barcode-action-link {
        color: var(--katalog-blue-text);
        text-decoration: none;
        font-weight: 600;
        cursor: pointer;
    }

    .katalog-barcode-action-link:hover {
        text-decoration: underline;
    }

    /* Seksi Pencarian Terkait (Bawah) */
    .katalog-related-section {
        margin-top: 32px;
    }

    .katalog-related-title {
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--katalog-ink);
        margin-bottom: 14px;
    }

    .katalog-related-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .katalog-pill-btn {
        background: #FFFFFF;
        border: 1px solid var(--katalog-border);
        color: var(--katalog-ink);
        font-size: 0.8125rem;
        font-weight: 600;
        padding: 8px 18px;
        border-radius: var(--katalog-radius-sm);
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        transition: all 0.15s ease;
    }

    .katalog-pill-btn:hover {
        border-color: #CBD5E1;
        background-color: #F8FAFC;
        color: var(--katalog-blue-text);
    }

    /* Responsif */
    @media (max-width: 900px) {
        .katalog-two-col-grid {
            grid-template-columns: 1fr;
        }

        .katalog-hero-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .katalog-cover-column {
            align-items: flex-start;
        }

        .katalog-inventory-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .katalog-search-box {
            max-width: 100%;
        }
    }

    @media (max-width: 600px) {
        .katalog-page-wrap {

            padding-left: 14px;
            padding-right: 14px;
        }

        .katalog-hero-card,
        .katalog-info-card,
        .katalog-inventory-panel {
            padding: 20px 18px;
        }

        .katalog-main-title {
            font-size: 1.5rem;
        }

        .katalog-digital-banner {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .katalog-meta-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
    html, body {
        overflow-x: hidden;
        max-width: 100%;
    }

    @media (max-width: 767.98px) {
        .katalog-page-wrap {
            padding-top: calc(16px + env(safe-area-inset-top, 0px));
            padding-bottom: 96px;
        }
        .katalog-top-bar {
            margin-bottom: 16px;
        }
        .katalog-tabs-nav {
            overflow-x: auto;
            flex-wrap: nowrap;
            gap: 18px;
            scrollbar-width: none;
        }
        .katalog-tab-item {
            white-space: nowrap;
            min-height: 44px;
        }
        .katalog-search-input {
            font-size: 1rem;
            min-height: 44px;
        }
        .btn-katalog-primary,
        .btn-katalog-outline,
        .katalog-filter-btn,
        .katalog-pill-btn {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="katalog-page-wrap">

    <!-- Navigasi Breadcrumbs & Kembali -->
    <div class="katalog-top-bar">
        <nav class="katalog-breadcrumbs" aria-label="Breadcrumb">
            <a href="<?= base_url() ?>">Beranda</a>
            <span class="sep">/</span>
            <a href="<?= base_url('opac') ?>">OPAC</a>
            <span class="sep">/</span>
            <span>Detail katalog</span>
        </nav>
        <a href="<?= base_url('opac') ?>" class="katalog-back-link">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke katalog</span>
        </a>
    </div>

    <!-- ─── KARTU HERO: SAMPUL & INFORMASI UTAMA ─── -->
    <section class="katalog-card-panel katalog-hero-card" aria-label="Informasi utama katalog">
        <div class="katalog-hero-grid">

            <!-- Kolom Sampul Buku -->
            <div class="katalog-cover-column">
                <div class="katalog-cover-outer" id="detailCoverStage"
                     role="button" tabindex="0"
                     style="background: <?= $coverPalette['bg'] ?>;"
                     aria-label="Lihat sampul katalog <?= esc($rawTitle) ?>"
                     onclick="handleCoverClick()"
                     onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();handleCoverClick();}">
                    <div class="katalog-cover-spine"></div>

                    <?php if ($hasRealCover): ?>
                        <img src="<?= esc($coverUrl) ?>" alt="Sampul <?= esc($rawTitle) ?>" class="katalog-real-img" id="realCoverImg">
                    <?php endif; ?>

                    <div class="katalog-cover-inner" id="generatedCoverBox" style="display: <?= $hasRealCover ? 'none' : 'flex' ?>;">
                        <div class="katalog-cover-geo-circle"></div>
                        <div class="katalog-cover-geo-rect"></div>

                        <div class="katalog-cover-top">
                            <span class="katalog-cover-tag" style="background: <?= $coverPalette['pill_bg'] ?>; color: <?= $coverPalette['accent'] ?>;">
                                <?= esc($topLabelCover) ?>
                            </span>
                            <span class="katalog-cover-year"><?= esc($yearCover) ?></span>
                        </div>

                        <div class="katalog-cover-middle">
                            <div class="katalog-cover-title"><?= esc($mainTitle) ?></div>
                            <div class="katalog-cover-author"><?= esc($primaryAuthor) ?></div>
                        </div>

                        <div class="katalog-cover-bottom">
                            <span class="katalog-cover-call"><?= esc($displayCallNumber) ?></span>
                            <span class="katalog-cover-edition" style="color: <?= $coverPalette['accent'] ?>;">
                                <?= esc($rawEdition !== '' ? $rawEdition : 'Kemnaker RI') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="katalog-cover-caption">
                    <div class="katalog-cover-caption-title">Sampul OPAC</div>
                    <div class="katalog-cover-caption-sub">
                        <?= $hasRealCover ? 'Gambar sampul resmi' : 'Sampul asli belum tersedia' ?>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Koleksi -->
            <div class="katalog-hero-main">
                <div class="katalog-kicker">DETAIL KATALOG / OPAC</div>
                <h1 class="katalog-main-title"><?= esc($mainTitle) ?></h1>
                <?php if ($subTitle !== ''): ?>
                    <div class="katalog-subtitle"><?= esc($subTitle) ?></div>
                <?php endif; ?>

                <div class="katalog-author-imprint">
                    <a href="<?= base_url('opac?search=' . urlencode($primaryAuthor) . '&search_by=Author') ?>" class="katalog-author-primary">
                        <?= esc($primaryAuthor) ?>
                    </a>
                    <div class="katalog-imprint-line"><?= esc($imprintMetaText) ?></div>
                </div>

                <!-- Tag Pills -->
                <div class="katalog-tag-pills">
                    <?php if (!empty($subjects)): ?>
                        <a href="<?= base_url('opac?search=' . urlencode($subjects[0]) . '&search_by=Subject') ?>" class="katalog-tag-badge">
                            <?= esc($subjects[0]) ?>
                        </a>
                    <?php else: ?>
                        <span class="katalog-tag-badge">Sastra</span>
                    <?php endif; ?>

                    <span class="katalog-tag-meta">
                        <?= esc($displayLanguage) ?> &middot; Nomor panggil: <strong><?= esc($displayCallNumber) ?></strong>
                    </span>
                </div>

                <!-- Action Buttons -->
                <div class="katalog-action-row">
                    <?php if (!empty($flipList)): ?>
                        <a href="<?= base_url('katalog/view_decrypted/' . encData($flipList[0]->ID)) ?>" target="_blank" class="btn-katalog-primary">
                            <span>Baca flipbook</span>
                        </a>
                    <?php elseif (!empty($digitalList)): ?>
                        <a href="<?= base_url('opac/baca-digital/' . $catalogId) ?>" target="_blank" class="btn-katalog-primary">
                            <span>Baca PDF Digital</span>
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn-katalog-primary" onclick="scrollToInventory()">
                            <span>Baca flipbook</span>
                        </button>
                    <?php endif; ?>

                    <button type="button" class="btn-katalog-outline" onclick="scrollToInventory()">
                        <span>Lihat ketersediaan</span>
                    </button>

                    <button type="button" class="btn-katalog-link" onclick="copyCatalogLink()">
                        <span>Salin tautan</span>
                    </button>
                </div>

                <!-- Status Counts Bar -->
                <div class="katalog-status-counts">
                    <span class="count-status-available">
                        <?= $totalDigital ?> koleksi digital
                    </span>
                    <span class="count-status-muted">
                        <?= $availFisik ?> eksemplar fisik tersedia
                    </span>
                </div>
            </div>

        </div>
    </section>

    <!-- ─── GRID 2 KOLOM: TENTANG BUKU & INFORMASI BIBLIOGRAFI ─── -->
    <div class="katalog-two-col-grid">

        <!-- Kolom Kiri: Tentang buku ini -->
        <section class="katalog-info-card" aria-label="Tentang buku ini">
            <h2 class="katalog-section-title">Tentang buku ini</h2>

            <div class="katalog-prose">
                <p><?= nl2br(esc($p1)) ?></p>
                <?php if ($rawNote === '' || $rawNote === '-'): ?>
                    <p><?= nl2br(esc($p2)) ?></p>
                <?php endif; ?>
            </div>

            <div class="katalog-divider"></div>

            <div class="katalog-meta-row">
                <div class="katalog-section-title" style="font-size: 0.9375rem; margin-bottom: 12px;">Pengarang &amp; kontributor</div>
                <div class="row g-2">
                    <div class="col-4">
                        <div class="katalog-meta-label">Pengarang utama</div>
                    </div>
                    <div class="col-8">
                        <div class="katalog-meta-val"><?= esc($primaryAuthor) ?></div>
                    </div>

                    <div class="col-4 mt-2">
                        <div class="katalog-meta-label">Penerbit</div>
                    </div>
                    <div class="col-8 mt-2">
                        <div class="katalog-meta-val"><?= esc($cleanPub ?: 'KOMPAS Media Nusantara') ?></div>
                        <div class="katalog-meta-val-sub"><?= esc($cleanPubLoc ?: 'Jakarta') ?><?= $cleanYear ? ', ' . esc($cleanYear) : ', 2025' ?></div>
                    </div>
                </div>
            </div>

            <div class="katalog-divider"></div>

            <div class="katalog-opac-badge">
                <i class="fa-solid fa-check"></i>
                <span>Tersedia di OPAC</span>
            </div>

            <div class="katalog-submeta-note">
                Bibliografi Nasional Indonesia &middot; Karya Tulis Ilmiah Nasional &middot; RDA Cataloging
            </div>
        </section>

        <!-- Kolom Kanan: Informasi bibliografi -->
        <section class="katalog-info-card" aria-label="Informasi bibliografi">
            <h2 class="katalog-section-title">Informasi bibliografi</h2>

            <div class="katalog-meta-row">
                <div class="katalog-meta-label">Bahasa</div>
                <div class="katalog-meta-val"><?= esc($displayLanguage) ?></div>
            </div>

            <div class="katalog-meta-row">
                <div class="katalog-meta-label">Nomor panggil</div>
                <div class="katalog-meta-val" style="font-family: 'JetBrains Mono', monospace; font-size: 0.9375rem;">
                    <?= esc($displayCallNumber) ?>
                </div>
            </div>

            <div class="katalog-meta-row">
                <div class="katalog-meta-label">Buku fisik</div>
                <div class="katalog-meta-val"><?= esc($displayPhysDesc) ?></div>
            </div>

            <div class="katalog-meta-row">
                <div class="katalog-meta-label">ISBN</div>
                <div class="katalog-meta-val" style="font-family: 'JetBrains Mono', monospace; font-size: 0.8125rem;">
                    <?= esc($rawIsbn !== '' ? $rawIsbn : '978-623-346-931-9') ?>
                </div>
            </div>

            <div class="katalog-meta-row">
                <div class="katalog-meta-label">Subjek</div>
                <div class="katalog-meta-val">
                    <?php if (!empty($subjects)): ?>
                        <?php foreach ($subjects as $idx => $s): ?>
                            <a href="<?= base_url('opac?search=' . urlencode($s) . '&search_by=Subject') ?>" class="katalog-meta-link">
                                <?= esc($s) ?>
                            </a><?= ($idx < count($subjects) - 1) ? ', ' : '' ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="<?= base_url('opac?search=Sastra&search_by=Subject') ?>" class="katalog-meta-link">Sastra</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="katalog-meta-row">
                <div class="katalog-meta-label">Ditambahkan</div>
                <div class="katalog-meta-val"><?= esc($formattedDate) ?></div>
            </div>

            <div class="katalog-divider"></div>

            <div class="katalog-meta-row">
                <div class="katalog-meta-label">Lokasi koleksi</div>
                <div class="katalog-meta-val" style="color: var(--katalog-ink); font-weight: 700;">
                    <?= esc($displayLocation) ?>
                </div>
            </div>
        </section>

    </div>

    <!-- ─── SEKSI KETERSEDIAAN KOLEKSI (TABS & TABEL) ─── -->
    <section class="katalog-inventory-panel" id="sectionInventory" aria-label="Ketersediaan koleksi">
        <div class="katalog-inventory-header">
            <h2 class="katalog-section-title" style="margin-bottom: 4px;">Ketersediaan koleksi</h2>
            <div class="katalog-inventory-sub">Lihat eksemplar fisik, koleksi digital, dan metadata katalog.</div>
        </div>

        <!-- Tabs Navigation -->
        <div class="katalog-tabs-nav" role="tablist" aria-label="Pilihan tampilan ketersediaan">
            <button type="button" class="katalog-tab-item active" id="tabFisikBtn"
                    role="tab" aria-selected="true" aria-controls="tabFisikPane"
                    onclick="switchKatalogTab('fisik')">
                Buku fisik (<?= $totalFisik ?>)
            </button>
            <button type="button" class="katalog-tab-item" id="tabDigitalBtn"
                    role="tab" aria-selected="false" aria-controls="tabDigitalPane"
                    onclick="switchKatalogTab('digital')">
                Koleksi digital (<?= $totalDigital ?>)
            </button>
            <button type="button" class="katalog-tab-item" id="tabMarcBtn"
                    role="tab" aria-selected="false" aria-controls="tabMarcPane"
                    onclick="switchKatalogTab('marc')">
                Metadata MARC21
            </button>
        </div>

        <!-- ── TAB PANE: BUKU FISIK ── -->
        <div id="tabFisikPane" role="tabpanel" aria-labelledby="tabFisikBtn">
            <!-- Filter Toolbar -->
            <div class="katalog-inventory-toolbar">
                <div class="katalog-search-box">
                    <i class="fa-solid fa-magnifying-glass katalog-search-icon"></i>
                    <input type="search" id="inventorySearchInput" class="katalog-search-input"
                           placeholder="Cari nomor barcode atau lokasi rak..."
                           aria-label="Cari nomor barcode atau lokasi rak">
                </div>

                <div class="katalog-filter-group" role="group" aria-label="Saring status eksemplar">
                    <button type="button" class="katalog-filter-btn active" data-status="all" onclick="filterInventoryStatus('all', this)">
                        Semua
                    </button>
                    <button type="button" class="katalog-filter-btn" data-status="avail" onclick="filterInventoryStatus('avail', this)">
                        Tersedia
                    </button>
                    <button type="button" class="katalog-filter-btn" data-status="loan" onclick="filterInventoryStatus('loan', this)">
                        Dipinjam
                    </button>
                </div>
            </div>

            <!-- Table -->
            <div class="katalog-table-wrap">
                <table class="katalog-table" id="inventoryTable">
                    <thead>
                        <tr>
                            <th scope="col">NOMOR BARCODE</th>
                            <th scope="col">NOMOR PANGGIL</th>
                            <th scope="col">AKSES</th>
                            <th scope="col">LOKASI KOLEKSI</th>
                            <th scope="col">KETERSEDIAAN</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <?php if (!empty($fisikList)): ?>
                            <?php foreach ($fisikList as $eksItem): ?>
                                <?php
                                $stId = (int) ($eksItem->Status_id ?? 0);
                                $stName = trim((string) ($eksItem->StatusName ?? 'Tersedia'));
                                $isDipinjam = ($stId === 5) || (stripos($stName, 'pinjam') !== false);
                                $rowStatusKey = $isDipinjam ? 'loan' : 'avail';
                                $barcodeVal = trim((string) ($eksItem->NomorBarcode ?? '-'));
                                $callVal    = trim((string) ($eksItem->CallNumber ?? $displayCallNumber));
                                $ruleVal    = trim((string) ($eksItem->RuleName ?? 'Dapat dipinjam'));
                                $locVal     = trim((string) ($eksItem->LocationName ?? $displayLocation));
                                ?>
                                <tr class="inventory-data-row" data-status="<?= esc($rowStatusKey) ?>" data-search="<?= esc(strtolower($barcodeVal . ' ' . $callVal . ' ' . $locVal . ' ' . $ruleVal)) ?>">
                                    <td style="font-family: 'JetBrains Mono', monospace; font-weight: 600;">
                                        <?= esc($barcodeVal) ?>
                                    </td>
                                    <td style="font-family: 'JetBrains Mono', monospace;"><?= esc($callVal) ?></td>
                                    <td><?= esc($ruleVal) ?></td>
                                    <td><?= esc($locVal) ?></td>
                                    <td>
                                        <?php if ($isDipinjam): ?>
                                            <span style="color: #D97706; font-weight: 600;"><i class="fa-regular fa-clock me-1"></i>Dipinjam</span>
                                        <?php else: ?>
                                            <span style="color: #059669; font-weight: 600;"><i class="fa-solid fa-check me-1"></i><?= esc($stName ?: 'Tersedia') ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="emptyInventoryRow">
                                <td colspan="5">
                                    <div class="katalog-empty-state">
                                        <div class="katalog-empty-title">Belum ada eksemplar fisik terdaftar</div>
                                        <div class="katalog-empty-sub">Hubungi petugas layanan perpustakaan untuk informasi koleksi.</div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Digital Reading Banner Bawah -->
            <?php if ($totalDigital > 0): ?>
                <div class="katalog-digital-banner">
                    <span class="katalog-digital-banner-text">Tetap bisa membaca secara digital</span>
                    <a href="javascript:void(0)" onclick="switchKatalogTab('digital')" class="katalog-digital-banner-link">
                        <span>Lihat koleksi digital (<?= $totalDigital ?>)</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Metadata Footer & Link Barcode -->
            <div class="katalog-meta-footer">
                <div>
                    ID Katalog: <?= esc($catalogId) ?> &middot; Control Number: <?= esc($rawControlNum !== '' ? $rawControlNum : 'INLIS000000000000013') ?> &middot; BIB ID: <?= esc($rawBibId !== '' ? $rawBibId : '0010-1026000004') ?>
                </div>
                <a href="javascript:void(0)" onclick="highlightBarcodes()" class="katalog-barcode-action-link">
                    Lihat barcode
                </a>
            </div>
        </div>

        <!-- ── TAB PANE: KOLEKSI DIGITAL ── -->
        <div id="tabDigitalPane" role="tabpanel" aria-labelledby="tabDigitalBtn" style="display: none;">
            <?php if (!empty($flipList)): ?>
                <div class="mb-4">
                    <div class="katalog-meta-label mb-2">FLIPBOOK DIGITAL</div>
                    <div class="list-group">
                        <?php foreach ($flipList as $idx => $fl): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div>
                                    <div class="fw-bold text-dark">Dokumen Digital <?= $idx + 1 ?></div>
                                    <div class="text-muted small">Baca berkas interaktif di browser</div>
                                </div>
                                <a href="<?= base_url('katalog/view_decrypted/' . encData($fl->ID)) ?>" target="_blank" class="btn-katalog-primary">
                                    <span>Buka Flipbook</span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($digitalList)): ?>
                <div class="katalog-table-wrap">
                    <table class="katalog-table">
                        <thead>
                            <tr>
                                <th scope="col">NOMOR BARCODE</th>
                                <th scope="col">NOMOR PANGGIL</th>
                                <th scope="col">LOKASI</th>
                                <th scope="col">STATUS</th>
                                <th scope="col" class="text-end">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($digitalList as $dr): ?>
                                <tr>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-weight: 600;"><?= esc($dr->NomorBarcode) ?></td>
                                    <td style="font-family: 'JetBrains Mono', monospace;"><?= esc($dr->CallNumber) ?></td>
                                    <td><?= esc($dr->LocationName) ?></td>
                                    <td><span style="color: #059669; font-weight: 600;">Tersedia Digital</span></td>
                                    <td class="text-end">
                                        <a href="<?= base_url('opac/baca-digital/' . $catalogId) ?>" target="_blank" class="btn-katalog-primary py-1 px-3" style="font-size: 0.75rem;">
                                            Baca PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (empty($flipList)): ?>
                <div class="katalog-empty-state">
                    <div class="katalog-empty-title">Koleksi digital belum tersedia</div>
                    <div class="katalog-empty-sub">Katalog ini saat ini hanya tersedia dalam versi cetak fisik di perpustakaan.</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- ── TAB PANE: MARC21 ── -->
        <div id="tabMarcPane" role="tabpanel" aria-labelledby="tabMarcBtn" style="display: none;">
            <?php if (!empty($marc)): ?>
                <div class="katalog-table-wrap">
                    <table class="katalog-table">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 10%;">TAG</th>
                                <th scope="col" style="width: 8%;">IND 1</th>
                                <th scope="col" style="width: 8%;">IND 2</th>
                                <th scope="col">NILAI RUAS (SUBFIELD)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($marc as $field): ?>
                                <tr>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-weight: 700;"><?= esc($field->Tag) ?></td>
                                    <td style="font-family: 'JetBrains Mono', monospace;"><?= esc($field->Indicator1 ?: '#') ?></td>
                                    <td style="font-family: 'JetBrains Mono', monospace;"><?= esc($field->Indicator2 ?: '#') ?></td>
                                    <td><?= esc($field->Value) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="katalog-empty-state">
                    <div class="katalog-empty-title">Ruas MARC21 Belum Tersedia</div>
                    <div class="katalog-empty-sub">Data bibliografi MARC untuk koleksi ini belum dipetakan.</div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($buku_terkait)): ?>
    <section class="katalog-related-section" id="section-buku-terkait" aria-label="Buku terkait">
        <h3 class="katalog-related-title">Buku terkait</h3>
        <div class="row g-2 g-md-3 opac-catalog-grid">
            <?php foreach ($buku_terkait as $terkait): ?>
                <?= renderModernBookCard($terkait, 'opac-col-book col-xl-3 col-lg-4 col-md-6 col-6') ?>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- ─── SEKSI PENCARIAN TERKAIT (BAWAH) ─── -->
    <section class="katalog-related-section" aria-label="Pencarian terkait">
        <h3 class="katalog-related-title">Pencarian terkait</h3>
        <div class="katalog-related-pills">
            <?php if ($primaryAuthor !== ''): ?>
                <a href="<?= base_url('opac?search=' . urlencode($primaryAuthor) . '&search_by=Author') ?>" class="katalog-pill-btn">
                    Karya <?= esc($primaryAuthor) ?>
                </a>
            <?php endif; ?>

            <a href="<?= base_url('opac?search=' . urlencode($cleanPub ?: 'KOMPAS Media Nusantara') . '&search_by=Publisher') ?>" class="katalog-pill-btn">
                <?= esc($cleanPub ?: 'KOMPAS Media Nusantara') ?>
            </a>

            <?php if (!empty($subjects)): ?>
                <a href="<?= base_url('opac?search=' . urlencode($subjects[0]) . '&search_by=Subject') ?>" class="katalog-pill-btn">
                    Subjek: <?= esc($subjects[0]) ?>
                </a>
            <?php else: ?>
                <a href="<?= base_url('opac?search=Sastra&search_by=Subject') ?>" class="katalog-pill-btn">
                    Subjek: Sastra
                </a>
            <?php endif; ?>

            <a href="<?= base_url('opac?PublishYear=' . urlencode($cleanYear ?: '2025')) ?>" class="katalog-pill-btn">
                Tahun <?= esc($cleanYear ?: '2025') ?>
            </a>
        </div>
    </section>

</div>

<script>
    const catalogData = {
        id: <?= json_encode($catalogId) ?>,
        title: <?= json_encode($rawTitle) ?>,
        author: <?= json_encode($primaryAuthor) ?>,
        currentUrl: <?= json_encode(current_url()) ?>,
        hasRealCover: <?= json_encode($hasRealCover) ?>
    };

    // Tab Switcher
    function switchKatalogTab(name) {
        const tabs = ['fisik', 'digital', 'marc'];
        tabs.forEach(t => {
            const btn = document.getElementById('tab' + capitalize(t) + 'Btn');
            const pane = document.getElementById('tab' + capitalize(t) + 'Pane');
            const isMatch = (t === name);
            if (btn) {
                btn.classList.toggle('active', isMatch);
                btn.setAttribute('aria-selected', isMatch ? 'true' : 'false');
            }
            if (pane) {
                pane.style.display = isMatch ? 'block' : 'none';
            }
        });
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Scroll to inventory section
    function scrollToInventory() {
        const section = document.getElementById('sectionInventory');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Filter status inventory
    let activeFilterStatus = 'all';
    function filterInventoryStatus(status, btn) {
        activeFilterStatus = status;
        document.querySelectorAll('.katalog-filter-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        applyInventoryFilter();
    }

    function applyInventoryFilter() {
        const searchInput = document.getElementById('inventorySearchInput');
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const rows = document.querySelectorAll('#inventoryTableBody tr.inventory-data-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowStatus = row.getAttribute('data-status') || 'avail';
            const rowSearch = row.getAttribute('data-search') || '';

            const matchStatus = (activeFilterStatus === 'all') || (rowStatus === activeFilterStatus);
            const matchQuery = (query === '') || (rowSearch.indexOf(query) !== -1);

            if (matchStatus && matchQuery) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    }

    const searchInputEl = document.getElementById('inventorySearchInput');
    if (searchInputEl) {
        searchInputEl.addEventListener('input', applyInventoryFilter);
    }

    // Highlight barcodes
    function highlightBarcodes() {
        scrollToInventory();
        switchKatalogTab('fisik');
        const tableWrap = document.querySelector('.katalog-table-wrap');
        if (tableWrap) {
            tableWrap.style.boxShadow = '0 0 0 3px rgba(29, 78, 216, 0.3)';
            setTimeout(() => {
                tableWrap.style.boxShadow = '';
            }, 1500);
        }
    }

    // Salin Tautan
    function copyCatalogLink() {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(catalogData.currentUrl).then(() => {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Tautan Disalin', text: 'Tautan katalog berhasil disalin ke clipboard!', timer: 1600, showConfirmButton: false });
                }
            });
        }
    }

    function handleCoverClick() {
        // Toggle view or notify
    }
</script>

<?= $this->endSection() ?>