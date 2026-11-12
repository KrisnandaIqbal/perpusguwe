<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?php
$db = db_connect();
$rowPerpus = $db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow();
$namaPerpus = ($rowPerpus && !empty($rowPerpus->Value)) ? trim($rowPerpus->Value) : 'Perpustakaan Kemnaker RI';

// Inisial perpustakaan untuk badge kurator
$wordsPerpus = preg_split('/\s+/', preg_replace('/[^a-zA-Z0-9\s]/', '', $namaPerpus));
$inisialPerpus = '';
foreach ($wordsPerpus as $wp) {
    if ($wp !== '' && strlen($inisialPerpus) < 2) {
        $inisialPerpus .= strtoupper(substr($wp, 0, 1));
    }
}
if ($inisialPerpus === '') {
    $inisialPerpus = 'PK';
}

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
    // Jika bagian utama mengandung frasa panjang seperti "A To Z Human Capital Manajemen Sumber Daya Manusia",
    // tetap tampilkan secara utuh dan rapi
} elseif (strpos($rawTitle, ' / ') !== false) {
    $parts = explode(' / ', $rawTitle, 2);
    $mainTitle = trim($parts[0]);
    $subTitle  = trim($parts[1]);
}

// Format khusus bila judul seperti "A To Z Human Capital Manajemen Sumber Daya Manusia"
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

// Format kontributor dengan peran untuk bagian Pengarang & Kontributor
$formattedContributors = [];
foreach ($authorList as $idx => $authName) {
    if ($idx === 0) {
        $formattedContributors[] = $authName . ' (Pengarang Utama)';
    } elseif ($idx === 1) {
        $formattedContributors[] = $authName . ' (Penyunting Ahli)';
    } else {
        $formattedContributors[] = $authName . ' (Kontributor)';
    }
}
$contributorsText = !empty($formattedContributors) ? implode(', ', $formattedContributors) : 'Tidak tercatat';

// Imprint / Penerbit
$cleanPub    = rtrim($rawPublisher, ".,;: ");
$cleanPubLoc = rtrim($rawPubLoc, ".,;: ");
$cleanYear   = rtrim($rawPubYear, ".,;: ");

$imprintParts = [];
if ($cleanPub !== '') {
    $imprintParts[] = $cleanPub;
}
if ($cleanPubLoc !== '') {
    $imprintParts[] = $cleanPubLoc;
}
$imprintHeader = !empty($imprintParts) ? implode(', ', $imprintParts) : 'Penerbit Umum';
if ($cleanYear !== '' && $cleanYear !== '0000') {
    $imprintHeader .= ' (' . $cleanYear . ')';
}

$penerbitLine = $cleanPub !== '' ? $cleanPub : 'Tidak tersedia';
$locYearParts = array_filter([$cleanPubLoc, $cleanYear]);
if (!empty($locYearParts)) {
    $penerbitLine .= ' · ' . implode(', ', $locYearParts);
}

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

$displayCallNumber = $rawCallNumber !== '' ? $rawCallNumber : ($firstCallFromEks !== '' ? $firstCallFromEks : '-');
$displayLocation   = $firstLocationFromEks !== '' ? $firstLocationFromEks : 'Rak Koleksi Umum';

// Tanggal ditambahkan
$formattedDate = '22 Oct 2026';
if (!empty($rawCreateDate)) {
    $ts = strtotime($rawCreateDate);
    if ($ts !== false) {
        $formattedDate = date('d M Y', $ts);
    }
}

// Subjek
$subjects = function_exists('splitSubjects') ? splitSubjects($rawSubject) : array_filter(array_map('trim', explode(';', $rawSubject)));

// Sampul Buku (Cek apakah file gambar sampul benar-benar ada)
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

// Paragraf catatan & ringkasan atas
$noteParagraphs = [];
$leadSummary    = '';

if ($rawNote !== '' && $rawNote !== '-') {
    // Pisahkan kalimat hanya setelah minimal 4 karakter alfanumerik/kurung tutup sebelum titik (menghindari Chr., Dr., L.)
    $splitLines = preg_split('/\R+/', $rawNote, -1, PREG_SPLIT_NO_EMPTY);
    if (count($splitLines) > 1) {
        $noteParagraphs = array_map('trim', $splitLines);
        $sentencesAll   = $noteParagraphs;
    } else {
        $sentencesAll = preg_split('/(?<=[a-zA-Z0-9\)]{4}[.?!])\s+/u', $rawNote, -1, PREG_SPLIT_NO_EMPTY);
        if (count($sentencesAll) >= 3) {
            $p1 = implode(' ', array_slice($sentencesAll, 0, 2));
            $p2 = implode(' ', array_slice($sentencesAll, 2));
            $noteParagraphs = [$p1, $p2];
        } elseif (count($sentencesAll) === 2) {
            $noteParagraphs = [$sentencesAll[0], $sentencesAll[1]];
        } else {
            $noteParagraphs = [$rawNote];
        }
    }

    // Ringkasan pendek di bagian atas (lead summary)
    if (count($sentencesAll) >= 2) {
        $leadSummary = mb_strimwidth($sentencesAll[count($sentencesAll) - 1], 0, 260, '...');
    } else {
        $leadSummary = mb_strimwidth($rawNote, 0, 240, '...');
    }
} else {
    $subjText = !empty($subjects) ? implode(', ', $subjects) : 'literatur umum dan ketenagakerjaan';
    $p1 = 'Koleksi "' . $rawTitle . '" karya ' . $primaryAuthor . ' membahas topik ' . $subjText . ' secara terstruktur sebagai bahan rujukan kepustakaan.';
    $p2 = 'Diterbitkan oleh ' . ($cleanPub ?: 'Penerbit Resmi') . ($cleanPubLoc ? ' di ' . $cleanPubLoc : '') . ($cleanYear ? ' pada tahun ' . $cleanYear : '') . ' dan dikelola dalam katalog induk ' . $namaPerpus . '.';
    $noteParagraphs = [$p1, $p2];
    $leadSummary    = $p1;
}

// Kutipan kurator berbasis data katalog nyata
$curatorNoteText = '';
if ($rawNote !== '' && $rawNote !== '-') {
    $sentencesForCurator = preg_split('/(?<=[a-zA-Z0-9\)]{4}[.?!])\s+/u', $rawNote, -1, PREG_SPLIT_NO_EMPTY);
    if (count($sentencesForCurator) >= 2) {
        $curatorNoteText = $sentencesForCurator[1];
    } else {
        $curatorNoteText = $sentencesForCurator[0];
    }
} else {
    $subjJoined = !empty($subjects) ? implode(' dan ', array_slice($subjects, 0, 3)) : 'ketenagakerjaan';
    $curatorNoteText = 'Koleksi rujukan bagi praktisi dan peneliti yang mendalami bidang ' . $subjJoined . '.';
}

// Format deskripsi fisik
$displayPhysDesc = $rawPhysDesc !== '' ? $rawPhysDesc : 'Koleksi cetak standar perpustakaan';
if ($rawPhysDesc !== '' && stripos($rawPhysDesc, 'kertas') === false) {
    $displayPhysDesc = 'Cetakan lengkap, ' . $rawPhysDesc;
}
?>

<?= $this->section('style') ?>
<style>
    :root {
        --det-bg-page: #f1f5f9;
        --det-surface: #ffffff;
        --det-surface-soft: #f8fafc;
        --det-surface-tint: #eff6ff;
        --det-border: #e2e8f0;
        --det-border-strong: #cbd5e1;
        --det-ink: #0f172a;
        --det-ink-secondary: #1e293b;
        --det-body: #334155;
        --det-muted: #475569;
        --det-subtle: #64748b;
        --det-brand: #1b3878;
        --det-brand-hover: #122858;
        --det-brand-accent: #1d4ed8;
        --det-brand-light: #dbeafe;
        --det-avail-text: #047857;
        --det-avail-bg: #ecfdf5;
        --det-avail-border: #a7f3d0;
        --det-loan-text: #b45309;
        --det-loan-bg: #fffbeb;
        --det-loan-border: #fde68a;
        --det-gold: #fcd34d;
        --det-gold-muted: rgba(252, 211, 77, 0.72);
    }

    body {
        background-color: var(--det-bg-page);
    }

    .opac-detail-wrapper {
        padding-top: 102px;
        padding-bottom: 64px;
    }

    /* Lembar Utama Katalog */
    .opac-detail-sheet {
        background: var(--det-surface);
        border: 1px solid var(--det-border);
        border-radius: 20px;
        box-shadow: 0 12px 32px -8px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .opac-sheet-section {
        padding: 38px 42px;
    }

    .opac-sheet-section + .opac-sheet-section {
        border-top: 1px solid var(--det-border);
    }

    /* ─── Bagian Atas: Sampul & Judul Utama ─── */
    .opac-cover-column {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .opac-cover-stage-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 16px;
        width: 100%;
    }

    /* Navigasi Vertikal Samping Sampul */
    .opac-cover-Pager {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        user-select: none;
    }

    .opac-pager-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 1px solid var(--det-border);
        background: var(--det-surface);
        color: var(--det-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .opac-pager-btn:hover {
        background: var(--det-surface-tint);
        border-color: var(--det-brand-accent);
        color: var(--det-brand);
    }

    .opac-pager-btn:focus-visible {
        outline: 2px solid var(--det-brand);
        outline-offset: 2px;
    }

    .opac-pager-indicator {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.68rem;
        color: var(--det-subtle);
        font-weight: 600;
    }

    /* Sampul Buku Hardcover 3D */
    .opac-hardcover-frame {
        position: relative;
        width: 220px;
        aspect-ratio: 3 / 4.25;
        border-radius: 4px 10px 10px 4px;
        background: linear-gradient(155deg, #163268 0%, #0d2149 55%, #081530 100%);
        box-shadow:
            -6px 0 12px rgba(15, 23, 42, 0.18),
            0 18px 34px -6px rgba(15, 23, 42, 0.38),
            inset 4px 0 8px rgba(255, 255, 255, 0.14);
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.28s ease, box-shadow 0.28s ease;
        flex-shrink: 0;
    }

    .opac-hardcover-frame:hover {
        transform: translateY(-3px);
        box-shadow:
            -8px 2px 14px rgba(15, 23, 42, 0.22),
            0 22px 40px -6px rgba(15, 23, 42, 0.45),
            inset 4px 0 8px rgba(255, 255, 255, 0.18);
    }

    /* Lipatan Punggung Buku */
    .opac-hardcover-spine {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 16px;
        background: linear-gradient(
            90deg,
            rgba(255, 255, 255, 0.2) 0%,
            rgba(0, 0, 0, 0.42) 48%,
            rgba(255, 255, 255, 0.12) 78%,
            transparent 100%
        );
        z-index: 4;
        pointer-events: none;
    }

    /* Bingkai Emas Klasik di dalam Sampul OPAC */
    .opac-hardcover-inner {
        position: relative;
        width: 100%;
        height: 100%;
        padding: 14px 14px 14px 22px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        z-index: 2;
    }

    .opac-hardcover-border {
        width: 100%;
        height: 100%;
        border: 1px solid rgba(252, 211, 77, 0.34);
        border-radius: 3px;
        padding: 16px 12px 14px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-align: center;
        position: relative;
    }

    .opac-hardcover-border::before {
        content: '';
        position: absolute;
        inset: 3px;
        border: 1px solid rgba(252, 211, 77, 0.14);
        pointer-events: none;
    }

    .opac-hc-edition-top {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.52rem;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--det-gold);
        font-weight: 600;
        line-height: 1.4;
    }

    .opac-hc-title-block {
        my: auto;
        padding: 6px 2px;
    }

    .opac-hc-kicker {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.16em;
        text-transform: uppercase;
        color: #e2e8f0;
        margin-bottom: 4px;
    }

    .opac-hc-main-title {
        font-family: 'Libre Baskerville', Georgia, serif;
        font-size: 0.92rem;
        font-weight: 700;
        color: #ffffff;
        line-height: 1.28;
        margin-bottom: 8px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
    }

    .opac-hc-divider {
        width: 54px;
        height: 1px;
        background: rgba(252, 211, 77, 0.45);
        margin: 6px auto 8px;
    }

    .opac-hc-subtitle {
        font-size: 0.56rem;
        line-height: 1.38;
        color: rgba(241, 245, 249, 0.82);
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .opac-hc-medallion {
        width: 56px;
        height: 36px;
        margin: 6px auto;
        border: 1px solid rgba(252, 211, 77, 0.42);
        border-radius: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.28);
    }

    .opac-hc-medallion span:first-child {
        font-family: 'Libre Baskerville', Georgia, serif;
        font-style: italic;
        font-size: 0.52rem;
        color: var(--det-gold);
        line-height: 1.1;
    }

    .opac-hc-medallion span:last-child {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.52rem;
        color: #ffffff;
        font-weight: 600;
        line-height: 1.2;
    }

    .opac-hc-footer {
        padding-top: 4px;
    }

    .opac-hc-author {
        font-size: 0.6rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .opac-hc-publisher {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.5rem;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: rgba(226, 232, 240, 0.72);
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .opac-real-cover-full {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .opac-cover-mode-btn {
        margin-top: 12px;
        background: transparent;
        border: 1px solid transparent;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 0.74rem;
        color: var(--det-subtle);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .opac-cover-mode-btn:hover {
        color: var(--det-brand);
        background: var(--det-surface-tint);
        border-color: var(--det-brand-light);
    }

    /* Kolom Kanan Header Bibliografis */
    .opac-kicker-meta {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.76rem;
        color: var(--det-subtle);
        margin-bottom: 10px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .opac-kicker-meta a {
        color: var(--det-brand);
        text-decoration: none;
        font-weight: 600;
    }

    .opac-kicker-meta a:hover {
        text-decoration: underline;
    }

    .opac-hero-title {
        font-family: 'Libre Baskerville', Georgia, serif;
        font-size: clamp(1.6rem, 2.5vw, 2.25rem);
        font-weight: 700;
        color: var(--det-ink);
        line-height: 1.24;
        letter-spacing: -0.015em;
        margin-bottom: 8px;
    }

    .opac-hero-subtitle {
        font-family: 'Libre Baskerville', Georgia, serif;
        font-style: italic;
        font-weight: 400;
        font-size: clamp(0.98rem, 1.35vw, 1.14rem);
        color: var(--det-muted);
        line-height: 1.48;
        margin-bottom: 14px;
    }

    .opac-author-imprint {
        font-size: 0.92rem;
        color: var(--det-muted);
        margin-bottom: 14px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .opac-author-imprint .author-link {
        color: var(--det-ink);
        font-weight: 700;
        text-decoration: none;
    }

    .opac-author-imprint .author-link:hover {
        color: var(--det-brand);
        text-decoration: underline;
    }

    .opac-lead-summary {
        font-size: 0.88rem;
        color: var(--det-muted);
        line-height: 1.65;
        max-width: 68ch;
        margin-bottom: 22px;
        padding-bottom: 22px;
        border-bottom: 1px solid var(--det-border);
    }

    /* Baris Tombol Tindakan Atas */
    .opac-action-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .opac-primary-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .btn-opac-pill-primary {
        background-color: var(--det-brand);
        color: #ffffff;
        border: 1.5px solid var(--det-brand);
        border-radius: 999px;
        padding: 9px 22px;
        font-size: 0.84rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-opac-pill-primary:hover {
        background-color: var(--det-brand-hover);
        border-color: var(--det-brand-hover);
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(27, 56, 120, 0.22);
    }

    .btn-opac-pill-outline {
        background-color: var(--det-surface);
        color: var(--det-ink-secondary);
        border: 1.5px solid var(--det-border-strong);
        border-radius: 999px;
        padding: 9px 20px;
        font-size: 0.84rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-opac-pill-outline:hover {
        border-color: var(--det-brand);
        color: var(--det-brand);
        background-color: var(--det-surface-tint);
    }

    .opac-utility-icons {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-opac-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1px solid transparent;
        background: transparent;
        color: var(--det-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.92rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-opac-icon:hover,
    .btn-opac-icon.is-active {
        background: var(--det-surface-tint);
        border-color: var(--det-brand-light);
        color: var(--det-brand);
    }

    .btn-opac-icon:focus-visible,
    .btn-opac-pill-primary:focus-visible,
    .btn-opac-pill-outline:focus-visible {
        outline: 2px solid var(--det-brand);
        outline-offset: 2px;
    }

    /* ─── Bagian Tengah: Catatan & Rincian Spesifikasi 2 Kolom ─── */
    .opac-mid-grid {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 42px;
    }

    .opac-mid-left {
        padding-right: 12px;
        min-width: 0;
        overflow-wrap: break-word;
    }

    .opac-mid-right {
        border-left: 1px solid var(--det-border);
        padding-left: 36px;
        min-width: 0;
        overflow-wrap: break-word;
    }

    .opac-spec-heading {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--det-ink);
        margin-bottom: 10px;
        letter-spacing: -0.01em;
    }

    .opac-note-prose p {
        font-size: 0.9rem;
        color: var(--det-body);
        line-height: 1.72;
        margin-bottom: 14px;
        max-width: 68ch;
    }

    .opac-note-prose p:last-child {
        margin-bottom: 0;
    }

    .opac-subject-status-box {
        margin-top: 22px;
        padding-top: 18px;
        border-top: 1px solid var(--det-border);
        font-size: 0.84rem;
    }

    .opac-meta-inline-row {
        display: flex;
        flex-wrap: wrap;
        align-items: baseline;
        gap: 6px;
        margin-bottom: 8px;
        line-height: 1.6;
    }

    .opac-meta-inline-row:last-child {
        margin-bottom: 0;
    }

    .opac-meta-inline-label {
        font-weight: 700;
        color: var(--det-ink);
        margin-right: 4px;
    }

    .opac-subject-link {
        color: var(--det-brand-accent);
        font-weight: 600;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .opac-subject-link:hover {
        color: var(--det-brand);
        text-decoration: underline;
    }

    .opac-status-check {
        color: var(--det-avail-text);
        font-weight: 600;
    }

    .opac-status-tag {
        color: var(--det-muted);
    }

    /* Kartu Kurator / Katalogisasi */
    .opac-curator-box {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--det-border);
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .opac-curator-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background-color: var(--det-surface-tint);
        border: 1px solid var(--det-brand-light);
        color: var(--det-brand);
        font-size: 0.76rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .opac-curator-header {
        font-size: 0.82rem;
        color: var(--det-subtle);
        margin-bottom: 4px;
    }

    .opac-curator-header strong {
        color: var(--det-ink);
        font-weight: 700;
    }

    .opac-curator-quote {
        font-family: 'Libre Baskerville', Georgia, serif;
        font-style: italic;
        font-size: 0.84rem;
        color: var(--det-muted);
        line-height: 1.6;
        margin: 0;
    }

    /* Blok Spesifikasi Kanan */
    .opac-spec-group {
        padding-bottom: 18px;
        margin-bottom: 18px;
        border-bottom: 1px solid var(--det-border);
    }

    .opac-spec-group.no-border {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 20px;
    }

    .opac-spec-primary {
        font-size: 0.88rem;
        color: var(--det-body);
        line-height: 1.58;
    }

    .opac-spec-secondary {
        font-size: 0.8rem;
        color: var(--det-subtle);
        margin-top: 4px;
        line-height: 1.5;
    }

    .opac-spec-mono {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.76rem;
        color: var(--det-subtle);
        margin-top: 6px;
        line-height: 1.55;
    }

    /* Kotak Lokasi Koleksi */
    .opac-location-callout {
        background-color: var(--det-surface-soft);
        border: 1px solid var(--det-border);
        border-radius: 12px;
        padding: 15px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .opac-location-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--det-ink);
        margin-bottom: 2px;
    }

    .opac-location-sub {
        font-size: 0.79rem;
        color: var(--det-muted);
    }

    .opac-location-link {
        background: none;
        border: none;
        padding: 4px 0;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--det-brand);
        text-decoration: underline;
        text-underline-offset: 4px;
        white-space: nowrap;
        cursor: pointer;
        transition: color 0.15s ease;
    }

    .opac-location-link:hover {
        color: var(--det-brand-accent);
    }

    /* ─── Bagian Bawah: Tabel Eksemplar & Metadata Bibliografi ─── */
    .opac-eks-header {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
    }

    .opac-eks-title {
        font-family: 'Libre Baskerville', Georgia, serif;
        font-size: 1.32rem;
        font-weight: 700;
        color: var(--det-ink);
        margin-bottom: 4px;
    }

    .opac-eks-submeta {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.76rem;
        color: var(--det-subtle);
    }

    /* Tab Switcher Kanan Atas */
    .opac-tab-group {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .opac-tab-btn {
        background: var(--det-surface);
        color: var(--det-muted);
        border: 1.5px solid var(--det-border);
        border-radius: 10px;
        padding: 7px 15px;
        font-size: 0.82rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .opac-tab-btn:hover {
        border-color: var(--det-brand);
        color: var(--det-brand);
    }

    .opac-tab-btn.active {
        border-color: var(--det-brand);
        color: var(--det-brand);
        background-color: var(--det-surface-tint);
        box-shadow: 0 2px 6px rgba(27, 56, 120, 0.08);
    }

    /* Baris Filter & Pencarian Eksemplar */
    .opac-eks-toolbar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .opac-eks-search-wrap {
        position: relative;
        flex: 1 1 280px;
        max-width: 360px;
    }

    .opac-eks-search-wrap i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--det-subtle);
        font-size: 0.82rem;
        pointer-events: none;
    }

    .opac-eks-search-input {
        width: 100%;
        border: 1px solid var(--det-border);
        background-color: var(--det-surface-soft);
        border-radius: 8px;
        padding: 8px 14px 8px 36px;
        font-size: 0.82rem;
        color: var(--det-ink);
        transition: all 0.2s ease;
    }

    .opac-eks-search-input:focus {
        outline: none;
        background-color: var(--det-surface);
        border-color: var(--det-brand);
        box-shadow: 0 0 0 3px rgba(27, 56, 120, 0.1);
    }

    .opac-eks-controls-right {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }

    .opac-filter-pills {
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .opac-filter-btn {
        background: var(--det-surface);
        color: var(--det-muted);
        border: 1px solid var(--det-border);
        border-radius: 7px;
        padding: 6px 13px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .opac-filter-btn:hover {
        border-color: var(--det-brand);
        color: var(--det-brand);
    }

    .opac-filter-btn.active {
        background: var(--det-brand);
        border-color: var(--det-brand);
        color: #ffffff;
    }

    .btn-marc-download {
        background: var(--det-surface);
        color: var(--det-ink-secondary);
        border: 1px solid var(--det-border-strong);
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-marc-download:hover {
        border-color: var(--det-brand);
        color: var(--det-brand);
        background: var(--det-surface-tint);
    }

    /* Tabel Eksemplar Responsif */
    .opac-table-region {
        overflow: auto;
        max-inline-size: 100%;
        border: 1px solid var(--det-border);
        border-radius: 12px;
        container-type: inline-size;
        background: var(--det-surface);
    }

    .opac-table-region:focus-visible {
        outline: 2px solid var(--det-brand);
        outline-offset: 2px;
    }

    .opac-eks-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin: 0;
        font-size: 0.85rem;
    }

    .opac-eks-table thead {
        position: sticky;
        inset-block-start: 0;
        z-index: 2;
        background-color: var(--det-surface-soft);
    }

    .opac-eks-table thead th {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--det-subtle);
        padding: 13px 18px;
        border-bottom: 1px solid var(--det-border);
        white-space: nowrap;
        background-color: var(--det-surface-soft);
    }

    .opac-eks-table tbody tr {
        transition: background-color 0.15s ease;
    }

    .opac-eks-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .opac-eks-table tbody td,
    .opac-eks-table tbody th {
        padding: 15px 18px;
        border-bottom: 1px solid var(--det-border);
        vertical-align: middle;
        color: var(--det-body);
        font-weight: 400;
        background: var(--det-surface);
    }

    .opac-eks-table tbody tr:last-child td,
    .opac-eks-table tbody tr:last-child th {
        border-bottom: none;
    }

    .opac-eks-table tbody th {
        position: sticky;
        inset-inline-start: 0;
        z-index: 1;
    }

    .barcode-cell-wrap {
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .barcode-code {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--det-brand-accent);
        letter-spacing: 0.02em;
    }

    .btn-copy-barcode {
        border: none;
        background: transparent;
        color: var(--det-subtle);
        padding: 2px 5px;
        border-radius: 4px;
        font-size: 0.76rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-copy-barcode:hover {
        color: var(--det-brand);
        background: var(--det-surface-tint);
    }

    .callnumber-code {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.82rem;
        color: var(--det-ink-secondary);
        font-weight: 500;
    }

    .eks-status-avail {
        color: var(--det-avail-text);
        font-weight: 600;
        font-size: 0.83rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .eks-status-loan {
        color: var(--det-loan-text);
        font-weight: 600;
        font-size: 0.83rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .eks-action-link {
        background: none;
        border: none;
        padding: 0;
        color: var(--det-ink);
        font-size: 0.83rem;
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 4px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        transition: color 0.15s ease;
    }

    .eks-action-link:hover {
        color: var(--det-brand-accent);
    }

    /* Highlight animasi saat tombol Lihat Barcode diklik */
    @keyframes barcodePulse {
        0% { background-color: #dbeafe; }
        100% { background-color: transparent; }
    }

    .barcode-highlight-flash {
        animation: barcodePulse 1.6s ease-out;
    }

    /* Stacked Mobile Table via Container Query */
    @container (width < 640px) {
        .opac-eks-table,
        .opac-eks-table thead,
        .opac-eks-table tbody,
        .opac-eks-table tr,
        .opac-eks-table th,
        .opac-eks-table td {
            display: block;
        }

        .opac-eks-table thead {
            display: none;
        }

        .opac-eks-table tbody tr {
            border-bottom: 2px solid var(--det-border);
            padding: 8px 0;
        }

        .opac-eks-table tbody th,
        .opac-eks-table tbody td {
            position: static;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 16px;
            border-bottom: 1px dashed var(--det-border);
            text-align: right;
        }

        .opac-eks-table tbody td:last-child {
            border-bottom: none;
        }

        .opac-eks-table {
            --label-1: "Nomor Barcode";
            --label-2: "Nomor Panggil";
            --label-3: "Akses";
            --label-4: "Lokasi Koleksi";
            --label-5: "Ketersediaan";
            --label-6: "Tindakan";
        }

        .opac-eks-table tbody th::before {
            content: var(--label-1) ": ";
            font-weight: 700;
            color: var(--det-subtle);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .opac-eks-table tbody td:nth-child(2)::before {
            content: var(--label-2) ": ";
            font-weight: 700;
            color: var(--det-subtle);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .opac-eks-table tbody td:nth-child(3)::before {
            content: var(--label-3) ": ";
            font-weight: 700;
            color: var(--det-subtle);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .opac-eks-table tbody td:nth-child(4)::before {
            content: var(--label-4) ": ";
            font-weight: 700;
            color: var(--det-subtle);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .opac-eks-table tbody td:nth-child(5)::before {
            content: var(--label-5) ": ";
            font-weight: 700;
            color: var(--det-subtle);
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .opac-eks-table tbody td:nth-child(6)::before {
            content: var(--label-6) ": ";
            font-weight: 700;
            color: var(--det-subtle);
            font-size: 0.75rem;
            text-transform: uppercase;
        }
    }

    /* Baris Pencarian Terkait di Kaki Kartu */
    .opac-related-footer {
        padding: 20px 42px;
        background-color: var(--det-surface-soft);
        border-top: 1px solid var(--det-border);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 18px;
        font-size: 0.82rem;
    }

    .opac-related-label {
        color: var(--det-subtle);
        font-weight: 600;
    }

    .opac-related-link {
        color: var(--det-ink-secondary);
        font-weight: 600;
        text-decoration: underline;
        text-underline-offset: 4px;
        transition: color 0.15s ease;
    }

    .opac-related-link:hover {
        color: var(--det-brand-accent);
    }

    /* Sitasi & Ekspor Box */
    .citation-card {
        background: var(--det-surface-soft);
        border: 1px solid var(--det-border);
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 12px;
    }

    .citation-style-label {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--det-brand);
        margin-bottom: 4px;
    }

    .citation-text {
        font-size: 0.85rem;
        color: var(--det-body);
        line-height: 1.55;
        margin-bottom: 0;
    }

    /* Responsif Layar Tablet & Ponsel */
    @media (max-width: 991.98px) {
        .opac-sheet-section {
            padding: 28px 24px;
        }

        .opac-mid-grid {
            grid-template-columns: 1fr;
            gap: 28px;
        }

        .opac-mid-left {
            padding-right: 0;
        }

        .opac-mid-right {
            border-left: none;
            border-top: 1px solid var(--det-border);
            padding-left: 0;
            padding-top: 28px;
        }

        .opac-related-footer {
            padding: 18px 24px;
        }
    }

    @media (max-width: 575.98px) {
        .opac-detail-wrapper {
            padding-top: 88px;
            padding-bottom: 40px;
        }

        .opac-sheet-section {
            padding: 22px 16px;
        }

        .opac-hardcover-frame {
            width: 185px;
        }

        .opac-eks-search-wrap {
            max-width: 100%;
        }

        .opac-related-footer {
            padding: 16px 16px;
            gap: 12px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container opac-detail-wrapper">
    <article class="opac-detail-sheet">

        <!-- ─── BAGIAN 1: HEADER & SAMPUL KOLEKSI ─── -->
        <section class="opac-sheet-section">
            <div class="row g-4 g-lg-5 align-items-start">

                <!-- Kolom Kiri: Sampul 3D & Navigasi Tampilan -->
                <div class="col-lg-4 col-md-5 opac-cover-column">
                    <div class="opac-cover-stage-wrap">
                        <!-- Bingkai Sampul Buku 3D -->
                        <div class="opac-hardcover-frame" id="bookCoverStage"
                             role="button" tabindex="0"
                             aria-label="Perbesar sampul buku <?= esc($rawTitle) ?>"
                             onclick="handleCoverStageClick()"
                             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();handleCoverStageClick();}">
                            <div class="opac-hardcover-spine"></div>

                            <?php if ($hasRealCover): ?>
                                <img src="<?= esc($coverUrl) ?>"
                                     alt="Sampul buku <?= esc($rawTitle) ?>"
                                     id="realBookCoverView"
                                     class="opac-real-cover-full"
                                     onerror="this.style.display='none'; document.getElementById('opacGeneratedCoverView').style.display='flex';">
                            <?php endif; ?>

                            <!-- Sampul Standar OPAC (Hardcover Klasik Biru Institusi) -->
                            <?php
                            $coverKicker = '';
                            $coverDisplayTitle = $mainTitle;
                            if (stripos($mainTitle, 'A To Z ') === 0) {
                                $coverKicker = 'A TO Z';
                                $coverDisplayTitle = trim(substr($mainTitle, 7));
                                if (strpos($coverDisplayTitle, ':') !== false) {
                                    $coverDisplayTitle = trim(explode(':', $coverDisplayTitle, 2)[0]);
                                }
                            }
                            ?>
                            <div class="opac-hardcover-inner" id="opacGeneratedCoverView" style="display: <?= $hasRealCover ? 'none' : 'flex' ?>;">
                                <div class="opac-hardcover-border">
                                    <div class="opac-hc-edition-top">
                                        <?= esc($rawEdition !== '' ? strtoupper($rawEdition) : 'KOLEKSI OPAC') ?> &middot; KOMPREHENSIF
                                    </div>

                                    <div class="opac-hc-title-block">
                                        <?php if ($coverKicker !== ''): ?>
                                            <div class="opac-hc-kicker"><?= esc($coverKicker) ?></div>
                                        <?php endif; ?>
                                        <div class="opac-hc-main-title">
                                            <?= esc($coverDisplayTitle) ?>
                                        </div>
                                        <div class="opac-hc-divider"></div>
                                        <?php if ($subTitle !== ''): ?>
                                            <div class="opac-hc-subtitle"><?= esc($subTitle) ?></div>
                                        <?php elseif (!empty($subjects)): ?>
                                            <div class="opac-hc-subtitle"><?= esc(implode(', ', array_slice($subjects, 0, 3))) ?></div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="opac-hc-medallion">
                                        <span><?= esc($rawEdition !== '' ? $rawEdition : 'Edisi') ?></span>
                                        <span><?= esc($cleanYear !== '' ? $cleanYear : 'OPAC') ?></span>
                                    </div>

                                    <div class="opac-hc-footer">
                                        <div class="opac-hc-author"><?= esc($primaryAuthor) ?></div>
                                        <div class="opac-hc-publisher">
                                            <?= esc($cleanPub ?: 'PERPUSTAKAAN') ?><?= $cleanPubLoc !== '' ? ' &middot; ' . esc($cleanPubLoc) : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Mode Sampul OPAC -->
                    <button type="button" class="opac-cover-mode-btn" id="btnToggleCoverMode" onclick="toggleOpacCoverMode()">
                        <i class="fa-regular fa-image"></i>
                        <span id="coverModeLabel">
                            <?= $hasRealCover ? 'Lihat Mode Sampul OPAC' : 'Mode Sampul OPAC (Tanpa Cover)' ?>
                        </span>
                    </button>
                </div>

                <!-- Kolom Kanan: Judul, Pengarang, Ringkasan, & Bilah Aksi -->
                <div class="col-lg-8 col-md-7">
                    <!-- Kicker Metadata -->
                    <div class="opac-kicker-meta">
                        <a href="<?= base_url('opac') ?>">Detail Katalog OPAC</a>
                        <?php if ($rawEdition !== ''): ?>
                            <span>&bull;</span>
                            <span><?= esc($rawEdition) ?></span>
                        <?php endif; ?>
                        <span>&bull;</span>
                        <span>Nomor Panggil: <strong><?= esc($displayCallNumber) ?></strong></span>
                    </div>

                    <!-- Judul Utama & Anak Judul -->
                    <h1 class="opac-hero-title"><?= esc($mainTitle) ?></h1>
                    <?php if ($subTitle !== ''): ?>
                        <div class="opac-hero-subtitle"><?= esc($subTitle) ?></div>
                    <?php endif; ?>

                    <!-- Pengarang Utama & Imprint -->
                    <div class="opac-author-imprint">
                        <a href="<?= base_url('opac?search=' . urlencode($primaryAuthor) . '&search_by=Author') ?>" class="author-link">
                            <?= esc($primaryAuthor) ?>
                        </a>
                        <span>&middot;</span>
                        <span><?= esc($imprintHeader) ?></span>
                    </div>

                    <!-- Ringkasan Singkat -->
                    <p class="opac-lead-summary">
                        <?= esc($leadSummary) ?>
                    </p>

                    <!-- Bilah Tombol Tindakan -->
                    <div class="opac-action-bar">
                        <div class="opac-primary-actions">
                            <!-- Tombol Pinjam Eksemplar disembunyikan sementara -->
                            <button type="button" class="btn-opac-pill-primary" style="display: none;" onclick="scrollToEksemplarAndPinjam()">
                                <span>Pinjam Eksemplar</span>
                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.75rem;"></i>
                            </button>

                            <button type="button" class="btn-opac-pill-outline" onclick="scrollToEksemplarAndFilterAvail()">
                                <span>Cek <?= $availFisik ?>/<?= $totalFisik ?> Tersedia</span>
                            </button>

                            <?php if (!empty($flipList)): ?>
                                <a href="<?= base_url('katalog/view_decrypted/' . encData($flipList[0]->ID)) ?>" target="_blank" class="btn-opac-pill-outline">
                                    <i class="fa-solid fa-book-open text-brand"></i>
                                    <span>Baca Flipbook</span>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Ikon Utilitas Kanan -->
                        <div class="opac-utility-icons">
                            <button type="button" class="btn-opac-icon" id="btnBookmarkCatalog"
                                    onclick="toggleBookmarkCatalog()"
                                    title="Simpan ke daftar koleksi favorit"
                                    aria-label="Simpan ke daftar koleksi favorit">
                                <i class="fa-regular fa-bookmark" id="iconBookmarkCatalog"></i>
                            </button>

                            <button type="button" class="btn-opac-icon"
                                    onclick="copyToClipboard()"
                                    title="Salin detail katalog"
                                    aria-label="Salin detail katalog">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ─── BAGIAN 2: CATATAN KATALOG & RINCIAN BIBLIOGRAFI (2 KOLOM) ─── -->
        <section class="opac-sheet-section">
            <div class="opac-mid-grid">

                <!-- Kolom Kiri: Catatan, Subjek, Status, & Kurator -->
                <div class="opac-mid-left">
                    <h2 class="opac-spec-heading">Catatan &amp; Deskripsi Katalog</h2>

                    <div class="opac-note-prose">
                        <?php foreach ($noteParagraphs as $para): ?>
                            <p><?= nl2br(esc($para)) ?></p>
                        <?php endforeach; ?>
                    </div>

                    <!-- Subjek & Status Katalog -->
                    <div class="opac-subject-status-box">
                        <div class="opac-meta-inline-row">
                            <span class="opac-meta-inline-label">Subjek:</span>
                            <?php if (!empty($subjects)): ?>
                                <?php foreach ($subjects as $idx => $subj): ?>
                                    <a href="<?= base_url('opac?search=' . urlencode($subj) . '&search_by=Subject') ?>" class="opac-subject-link">
                                        <?= esc($subj) ?>
                                    </a>
                                    <?php if ($idx < count($subjects) - 1): ?>
                                        <span class="text-muted">&middot;</span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Koleksi Umum</span>
                            <?php endif; ?>
                        </div>

                        <div class="opac-meta-inline-row">
                            <span class="opac-meta-inline-label">Status Katalog:</span>
                            <?php if (!empty($catalog['IsOPAC']) || ($catalog['IsOPAC'] ?? 1) == 1): ?>
                                <span class="opac-status-check"><i class="fa-solid fa-check me-1"></i>Tersedia di OPAC</span>
                            <?php else: ?>
                                <span class="opac-status-tag">Internal Katalog</span>
                            <?php endif; ?>

                            <span class="text-muted">&middot;</span>
                            <span class="opac-status-tag">Bibliografi Nasional Indonesia</span>
                            <span class="text-muted">&middot;</span>
                            <span class="opac-status-tag">Karya Tulis Ilmiah Nasional</span>
                            <span class="text-muted">&middot;</span>
                            <span class="opac-status-tag">RDA Cataloging</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Pengarang, Bahasa, Buku Fisik, & Kotak Lokasi -->
                <div class="opac-mid-right">
                    <!-- 1. Pengarang & Kontributor -->
                    <div class="opac-spec-group">
                        <h3 class="opac-spec-heading">Pengarang &amp; Kontributor</h3>
                        <div class="opac-spec-primary"><?= esc($contributorsText) ?></div>
                        <div class="opac-spec-secondary">Penerbit: <?= esc($penerbitLine) ?></div>
                    </div>

                    <!-- 2. Bahasa & Klasifikasi -->
                    <div class="opac-spec-group">
                        <h3 class="opac-spec-heading">Bahasa &amp; Klasifikasi</h3>
                        <div class="opac-spec-primary">
                            <?= esc($displayLanguage) ?>
                            <?php if ($rawEdition !== ''): ?>
                                &middot; <?= esc($rawEdition) ?>
                            <?php endif; ?>
                        </div>
                        <div class="opac-spec-mono">
                            Nomor Panggil: <?= esc($displayCallNumber) ?> &bull; Ditambahkan: <?= esc($formattedDate) ?>
                        </div>
                    </div>

                    <!-- 3. Buku Fisik -->
                    <div class="opac-spec-group no-border">
                        <h3 class="opac-spec-heading">
                            Buku Fisik <?= $rawEdition !== '' ? '(' . esc($rawEdition) . ')' : '' ?>
                        </h3>
                        <div class="opac-spec-primary"><?= esc($displayPhysDesc) ?></div>
                        <div class="opac-spec-mono">
                            ISBN: <?= esc($rawIsbn !== '' ? $rawIsbn : '-') ?><br>
                            Control Number: <?= esc($rawControlNum !== '' ? $rawControlNum : '-') ?> &bull; BIB ID: <?= esc($rawBibId !== '' ? $rawBibId : '-') ?>
                        </div>
                    </div>

                    <!-- 4. Kotak Lokasi Koleksi -->
                    <div class="opac-location-callout">
                        <div>
                            <div class="opac-location-title">Lokasi Koleksi: <?= esc($displayLocation) ?></div>
                            <div class="opac-location-sub">
                                <?= $availFisik ?> dari <?= $totalFisik ?> eksemplar tersedia untuk dipinjam
                            </div>
                        </div>
                        <button type="button" class="opac-location-link" onclick="scrollToAndHighlightBarcodes()">
                            Lihat Barcode
                        </button>
                    </div>
                </div>

            </div>
        </section>

        <!-- ─── BAGIAN 3: INFORMASI EKSEMPLAR & METADATA BIBLIOGRAFI ─── -->
        <section class="opac-sheet-section" id="section-eksemplar">
            <!-- Header Seksi & Navigasi Tab -->
            <div class="opac-eks-header">
                <div>
                    <h2 class="opac-eks-title">Informasi Eksemplar &amp; Metadata Bibliografi</h2>
                    <div class="opac-eks-submeta">
                        ID Katalog: <?= esc($catalogId) ?> &bull; Control Number: <?= esc($rawControlNum !== '' ? $rawControlNum : '-') ?> &bull; Ditambahkan: <?= esc($formattedDate) ?>
                    </div>
                </div>

                <div class="opac-tab-group" role="tablist" aria-label="Pilih tampilan eksemplar atau metadata">
                    <button type="button" class="opac-tab-btn active" id="tabBtnFisik"
                            role="tab" aria-selected="true" aria-controls="paneFisik"
                            onclick="switchDetailTab('fisik')">
                        <i class="fa-solid fa-book"></i>
                        <span>Buku Fisik (<?= $totalFisik ?>)</span>
                    </button>

                    <?php if (!empty($digitalList) || !empty($flipList)): ?>
                        <button type="button" class="opac-tab-btn" id="tabBtnDigital"
                                role="tab" aria-selected="false" aria-controls="paneDigital"
                                onclick="switchDetailTab('digital')">
                            <i class="fa-solid fa-tablet-screen-button"></i>
                            <span>Koleksi Digital (<?= count($digitalList) + count($flipList) ?>)</span>
                        </button>
                    <?php endif; ?>

                    <button type="button" class="opac-tab-btn" id="tabBtnMarc"
                            role="tab" aria-selected="false" aria-controls="paneMarc"
                            onclick="switchDetailTab('marc')">
                        <i class="fa-solid fa-code"></i>
                        <span>MARC21</span>
                    </button>
                </div>
            </div>

            <!-- ── PANEL 1: BUKU FISIK ── -->
            <div id="paneFisik" role="tabpanel" aria-labelledby="tabBtnFisik">
                <!-- Bilah Pencarian & Penyaring Status -->
                <div class="opac-eks-toolbar">
                    <div class="opac-eks-search-wrap">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="search" id="eksSearchInput" class="opac-eks-search-input"
                               placeholder="Cari nomor barcode atau lokasi rak..."
                               aria-label="Cari nomor barcode atau lokasi rak">
                    </div>

                    <div class="opac-eks-controls-right">
                        <div class="opac-filter-pills" role="group" aria-label="Saring berdasarkan ketersediaan">
                            <button type="button" class="opac-filter-btn active" data-eks-filter="all" onclick="setEksStatusFilter('all', this)">
                                Semua
                            </button>
                            <button type="button" class="opac-filter-btn" data-eks-filter="avail" onclick="setEksStatusFilter('avail', this)">
                                Tersedia
                            </button>
                            <button type="button" class="opac-filter-btn" data-eks-filter="loan" onclick="setEksStatusFilter('loan', this)">
                                Dipinjam
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tabel Data Eksemplar -->
                <div tabindex="0" role="region" aria-label="Daftar eksemplar buku fisik" class="opac-table-region">
                    <table class="opac-eks-table" id="tbl_eksemplars_fisik">
                        <thead>
                            <tr>
                                <th scope="col">Nomor Barcode</th>
                                <th scope="col">Nomor Panggil</th>
                                <th scope="col">Akses</th>
                                <th scope="col">Lokasi Koleksi</th>
                                <th scope="col">Ketersediaan</th>
                                <th scope="col" class="text-end" style="display: none;">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody id="eksTableBody">
                            <?php if (!empty($fisikList)): ?>
                                <?php foreach ($fisikList as $eksemplar): ?>
                                    <?php
                                    $stId = (int) ($eksemplar->Status_id ?? 0);
                                    $stName = trim((string) ($eksemplar->StatusName ?? 'Tersedia'));
                                    $isDipinjam = ($stId === 5) || (stripos($stName, 'pinjam') !== false);
                                    $rowStatusKey = $isDipinjam ? 'loan' : 'avail';
                                    $barcodeVal = trim((string) ($eksemplar->NomorBarcode ?? '-'));
                                    $callVal    = trim((string) ($eksemplar->CallNumber ?? $displayCallNumber));
                                    $ruleVal    = trim((string) ($eksemplar->RuleName ?? 'Dapat dipinjam'));
                                    $locVal     = trim((string) ($eksemplar->LocationName ?? $displayLocation));
                                    ?>
                                    <tr class="eks-data-row"
                                        data-status="<?= esc($rowStatusKey) ?>"
                                        data-search="<?= esc(strtolower($barcodeVal . ' ' . $callVal . ' ' . $locVal . ' ' . $ruleVal)) ?>">
                                        <th scope="row" class="barcode-cell">
                                            <div class="barcode-cell-wrap">
                                                <span class="barcode-code"><?= esc($barcodeVal) ?></span>
                                                <button type="button" class="btn-copy-barcode"
                                                        onclick="copySingleBarcode('<?= esc($barcodeVal) ?>')"
                                                        title="Salin nomor barcode <?= esc($barcodeVal) ?>"
                                                        aria-label="Salin nomor barcode <?= esc($barcodeVal) ?>">
                                                    <i class="fa-regular fa-copy"></i>
                                                </button>
                                            </div>
                                        </th>
                                        <td><span class="callnumber-code"><?= esc($callVal) ?></span></td>
                                        <td><?= esc($ruleVal) ?></td>
                                        <td><?= esc($locVal) ?></td>
                                        <td>
                                            <?php if ($isDipinjam): ?>
                                                <span class="eks-status-loan">
                                                    <i class="fa-regular fa-clock"></i>
                                                    <span>Sedang Dipinjam</span>
                                                </span>
                                            <?php else: ?>
                                                <span class="eks-status-avail">
                                                    <i class="fa-solid fa-check"></i>
                                                    <span><?= esc($stName !== '' ? $stName : 'Tersedia') ?></span>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <!-- Kolom Pinjam Eksemplar disembunyikan sementara -->
                                        <td class="text-end" style="display: none;">
                                            <?php if (!$isDipinjam): ?>
                                                <button type="button" class="eks-action-link"
                                                        onclick="openPinjamDialog('<?= esc($barcodeVal) ?>', '<?= esc($callVal) ?>', '<?= esc($locVal) ?>')">
                                                    <span>Pinjam Eksemplar</span>
                                                    <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.7rem;"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small">Sedang Dipinjam</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="eksEmptyFilterRow" style="display: none;">
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Tidak ada eksemplar yang cocok dengan kata kunci atau filter status yang dipilih.
                                        <button type="button" class="btn btn-link btn-sm text-brand fw-semibold p-0 ms-1" onclick="resetEksFilter()">Reset filter</button>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Data eksemplar fisik untuk katalog ini belum terdaftar di rak sirkulasi. Silakan hubungi petugas layanan perpustakaan untuk informasi koleksi.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── PANEL 2: KOLEKSI DIGITAL / FLIPBOOK (JIKA ADA) ── -->
            <?php if (!empty($digitalList) || !empty($flipList)): ?>
                <div id="paneDigital" role="tabpanel" aria-labelledby="tabBtnDigital" style="display: none;">
                    <?php if (!empty($flipList)): ?>
                        <div class="mb-3">
                            <h3 class="opac-spec-heading mb-2">Dokumen Digital Interaktif (Flipbook)</h3>
                            <div class="list-group">
                                <?php foreach ($flipList as $i => $f): ?>
                                    <div class="list-group-item d-flex flex-wrap justify-content-between align-items-center gap-3 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fa-solid fa-file-pdf text-danger fs-4"></i>
                                            <div>
                                                <div class="fw-bold text-dark">Dokumen Digital <?= $i + 1 ?></div>
                                                <div class="text-muted small">Baca dokumen secara interaktif di peramban</div>
                                            </div>
                                        </div>
                                        <a href="<?= base_url('katalog/view_decrypted/' . encData($f->ID)) ?>" target="_blank" class="btn-opac-pill-primary py-2 px-3">
                                            <i class="fa-solid fa-book-open"></i>
                                            <span>Baca Flipbook</span>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($digitalList)): ?>
                        <div tabindex="0" role="region" aria-label="Daftar eksemplar buku digital" class="opac-table-region">
                            <table class="opac-eks-table" id="tbl_eksemplars_digital">
                                <thead>
                                    <tr>
                                        <th scope="col">Nomor Barcode</th>
                                        <th scope="col">Nomor Panggil</th>
                                        <th scope="col">Akses</th>
                                        <th scope="col">Lokasi</th>
                                        <th scope="col">Ketersediaan</th>
                                        <th scope="col" class="text-end">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($digitalList as $eksemplar_drm): ?>
                                        <?php
                                        $canRead = ($eksemplar_drm->Status_id ?? 0) == 1
                                            || in_array($eksemplar_drm->CollectionID, $member_active_loan_collections ?? []);
                                        $isDrmDipinjam = !$canRead || (($eksemplar_drm->Status_id ?? 0) == 5) || (stripos($eksemplar_drm->StatusName ?? '', 'pinjam') !== false);
                                        ?>
                                        <tr>
                                            <th scope="row"><span class="barcode-code"><?= esc($eksemplar_drm->NomorBarcode) ?></span></th>
                                            <td><span class="callnumber-code"><?= esc($eksemplar_drm->CallNumber) ?></span></td>
                                            <td><?= esc($eksemplar_drm->RuleName) ?></td>
                                            <td><?= esc($eksemplar_drm->LocationName) ?></td>
                                            <td>
                                                <?php if ($isDrmDipinjam): ?>
                                                    <span class="eks-status-loan"><i class="fa-regular fa-clock"></i> Sedang Dipinjam</span>
                                                <?php else: ?>
                                                    <span class="eks-status-avail"><i class="fa-solid fa-check"></i> <?= esc($eksemplar_drm->StatusName ?: 'Tersedia') ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end">
                                                <?php if ($canRead): ?>
                                                    <a href="<?= base_url('opac/baca-digital/' . $catalogId) ?>" target="_blank" class="eks-action-link">
                                                        <span>Baca PDF</span>
                                                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.7rem;"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <button type="button" class="eks-action-link text-danger"
                                                            onclick="Swal.fire({icon:'error',title:'Sedang Dipinjam',text:'Koleksi digital ini sedang dipinjam oleh anggota lain. Silakan coba lagi nanti.',confirmButtonText:'Oke'})">
                                                        Sedang Dipinjam
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- ── PANEL 3: MARC21 METADATA ── -->
            <div id="paneMarc" role="tabpanel" aria-labelledby="tabBtnMarc" style="display: none;">
                <?php if (!empty($marc)): ?>
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <div class="small text-muted">
                            <i class="fa-solid fa-database me-1 text-brand"></i>
                            Standar Metadata MARC21 &bull; Total <strong><?= count($marc) ?></strong> ruas terdaftar
                        </div>
                    </div>

                    <div tabindex="0" role="region" aria-label="Tabel ruas metadata MARC21" class="opac-table-region">
                        <table class="opac-eks-table" id="marcTable">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 12%;">Tag</th>
                                    <th scope="col" style="width: 10%;">Ind 1</th>
                                    <th scope="col" style="width: 10%;">Ind 2</th>
                                    <th scope="col" style="width: 58%;">Nilai Ruas (Subfield)</th>
                                    <th scope="col" style="width: 10%;" class="text-end">Urutan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($marc as $field): ?>
                                    <tr>
                                        <th scope="row"><span class="barcode-code"><?= esc($field->Tag) ?></span></th>
                                        <td><span class="callnumber-code"><?= esc($field->Indicator1 ?: '#') ?></span></td>
                                        <td><span class="callnumber-code"><?= esc($field->Indicator2 ?: '#') ?></span></td>
                                        <td><?= esc($field->Value) ?></td>
                                        <td class="text-end"><span class="callnumber-code"><?= esc($field->Sequence) ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted border rounded-3 bg-light">
                        Ruas metadata MARC21 belum tersedia untuk katalog ini.
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <?php if (!empty($buku_terkait)): ?>
        <section class="opac-sheet-section" id="section-buku-terkait" aria-label="Buku terkait">
            <h2 class="h5 fw-bold mb-3">Buku terkait</h2>
            <div class="row g-4">
                <?php foreach ($buku_terkait as $terkait): ?>
                    <?= renderModernBookCard($terkait, 'col-xl-3 col-lg-4 col-md-6') ?>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- ─── BAGIAN 4: PENCARIAN TERKAIT (FOOTER BAR) ─── -->
        <footer class="opac-related-footer" aria-label="Tautan pencarian terkait">
            <span class="opac-related-label">Pencarian Terkait:</span>

            <?php if ($rawAuthor !== ''): ?>
                <a href="<?= base_url('opac?search=' . urlencode($rawAuthor) . '&search_by=Author') ?>" class="opac-related-link">
                    Karya <?= esc($rawAuthor) ?>
                </a>
            <?php endif; ?>

            <?php if ($cleanPub !== ''): ?>
                <a href="<?= base_url('opac?search=' . urlencode($cleanPub) . '&search_by=Publisher') ?>" class="opac-related-link">
                    Dari <?= esc($cleanPub) ?>
                </a>
            <?php endif; ?>

            <?php foreach ($subjects as $subItem): ?>
                <a href="<?= base_url('opac?search=' . urlencode($subItem) . '&search_by=Subject') ?>" class="opac-related-link">
                    Subjek: <?= esc($subItem) ?>
                </a>
            <?php endforeach; ?>

            <?php if ($cleanYear !== '' && $cleanYear !== '0000'): ?>
                <a href="<?= base_url('opac?PublishYear=' . urlencode($cleanYear)) ?>" class="opac-related-link">
                    Tahun <?= esc($cleanYear) ?>
                </a>
            <?php endif; ?>
        </footer>

    </article>
</div>

<!-- Modal Preview Sampul Buku -->
<div class="modal fade" id="coverModal" tabindex="-1" aria-labelledby="coverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="coverModalLabel" style="font-size: 1rem;">Sampul: <?= esc($rawTitle) ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center py-4">
                <img src="<?= esc($coverUrl) ?>" id="modalCoverImage" alt="Sampul <?= esc($rawTitle) ?>" class="img-fluid rounded shadow-sm" style="max-height: 460px;">
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <?php if ($hasRealCover): ?>
                    <a href="<?= esc($coverUrl) ?>" id="downloadCoverBtn" download class="btn-opac-pill-primary py-2 px-3">
                        <i class="fa-solid fa-download"></i>
                        <span>Unduh Sampul</span>
                    </a>
                <?php endif; ?>
                <button type="button" class="btn-opac-pill-outline py-2 px-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Panduan Peminjaman Eksemplar -->
<div class="modal fade" id="pinjamEksModal" tabindex="-1" aria-labelledby="pinjamEksModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; padding: 8px;">
            <div class="modal-header border-bottom-0">
                <div>
                    <div class="text-uppercase fw-bold" style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #1b3878; letter-spacing: 0.08em;">
                        SIRKULASI &amp; PEMINJAMAN KOLEKSI
                    </div>
                    <h5 class="modal-title fw-bold mt-1" id="pinjamEksModalTitle" style="font-family: 'Libre Baskerville', Georgia, serif; font-size: 1.15rem;">
                        Pinjam Eksemplar Koleksi
                    </h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-0">
                <p class="small text-muted mb-3">
                    Gunakan informasi nomor panggil dan nomor barcode di bawah ini untuk mengambil buku di rak dan melakukan peminjaman di meja sirkulasi atau layanan peminjaman mandiri.
                </p>
                <div class="p-3 rounded-3 mb-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="row g-2 small">
                        <div class="col-5 text-muted">Judul Koleksi:</div>
                        <div class="col-7 fw-bold text-dark"><?= esc($mainTitle) ?></div>

                        <div class="col-5 text-muted">Nomor Barcode:</div>
                        <div class="col-7">
                            <span class="barcode-code" id="modalPinjamBarcode"><?= esc($firstAvailBarcode ?: '-') ?></span>
                            <button type="button" class="btn-copy-barcode ms-1" onclick="copySingleBarcode(document.getElementById('modalPinjamBarcode').textContent)">
                                <i class="fa-regular fa-copy"></i>
                            </button>
                        </div>

                        <div class="col-5 text-muted">Nomor Panggil:</div>
                        <div class="col-7"><span class="callnumber-code" id="modalPinjamCall"><?= esc($displayCallNumber) ?></span></div>

                        <div class="col-5 text-muted">Lokasi Rak:</div>
                        <div class="col-7 fw-semibold text-brand" id="modalPinjamLoc"><?= esc($displayLocation) ?></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 d-flex justify-content-between">
                <button type="button" class="btn-opac-pill-outline py-2 px-3" data-bs-dismiss="modal">Tutup</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn-opac-pill-outline py-2 px-3"
                            onclick="copySingleBarcode(document.getElementById('modalPinjamBarcode').textContent)">
                        <i class="fa-regular fa-copy"></i>
                        <span>Salin Barcode</span>
                    </button>
                    <a href="<?= base_url('peminjaman-mandiri') ?>" class="btn-opac-pill-primary py-2 px-3">
                        <span>Layanan Peminjaman</span>
                        <i class="fa-solid fa-arrow-right" style="font-size: 0.75rem;"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    <?php if (session()->getFlashdata('digital_error')): ?>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'warning',
            title: 'Masa Peminjaman Berakhir',
            text: <?= json_encode(session()->getFlashdata('digital_error')) ?>,
            confirmButtonText: 'Oke',
        });
    });
    <?php endif; ?>
    <?php if (session()->getFlashdata('digital_info')): ?>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'info',
            title: 'Sedang Dipinjam',
            text: <?= json_encode(session()->getFlashdata('digital_info')) ?>,
            confirmButtonText: 'Oke',
        });
    });
    <?php endif; ?>

    const catalogData = {
        id: <?= json_encode($catalogId) ?>,
        controlNumber: <?= json_encode($rawControlNum) ?>,
        title: <?= json_encode($rawTitle) ?>,
        author: <?= json_encode($rawAuthor) ?>,
        publisher: <?= json_encode($cleanPub) ?>,
        publishLocation: <?= json_encode($cleanPubLoc) ?>,
        publishYear: <?= json_encode($cleanYear) ?>,
        isbn: <?= json_encode($rawIsbn) ?>,
        callNumber: <?= json_encode($displayCallNumber) ?>,
        subject: <?= json_encode($rawSubject) ?>,
        physicalDescription: <?= json_encode($rawPhysDesc) ?>,
        languages: <?= json_encode($rawLang) ?>,
        note: <?= json_encode($rawNote) ?>,
        currentUrl: <?= json_encode(current_url()) ?>,
        hasRealCover: <?= json_encode($hasRealCover) ?>,
        coverUrl: <?= json_encode($coverUrl) ?>,
        firstAvailBarcode: <?= json_encode($firstAvailBarcode) ?>,
        defaultLocation: <?= json_encode($displayLocation) ?>
    };

    // ─── Pengaturan Mode Sampul (Gambar Asli vs Mode Sampul OPAC) ───
    let showingGeneratedCover = !catalogData.hasRealCover;

    function toggleOpacCoverMode() {
        const realImg = document.getElementById('realBookCoverView');
        const genView = document.getElementById('opacGeneratedCoverView');
        const labelEl = document.getElementById('coverModeLabel');
        const pagerEl = document.getElementById('coverPagerText');

        if (!catalogData.hasRealCover || !realImg) {
            showToast('Menampilkan Mode Sampul Standar OPAC (Katalog belum memiliki gambar sampul unggahan).', 'info');
            return;
        }

        showingGeneratedCover = !showingGeneratedCover;
        if (showingGeneratedCover) {
            realImg.style.display = 'none';
            genView.style.display = 'flex';
            if (labelEl) labelEl.textContent = 'Tampilkan Sampul Asli';
            if (pagerEl) pagerEl.textContent = '2/2';
        } else {
            realImg.style.display = 'block';
            genView.style.display = 'none';
            if (labelEl) labelEl.textContent = 'Lihat Mode Sampul OPAC';
            if (pagerEl) pagerEl.textContent = '1/2';
        }
    }

    function handleCoverStageClick() {
        if (catalogData.hasRealCover && !showingGeneratedCover && catalogData.coverUrl) {
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('coverModal'));
            modal.show();
        } else if (catalogData.hasRealCover) {
            toggleOpacCoverMode();
        } else {
            showToast('Sampul ditampilkan dalam Mode Sampul OPAC.', 'info');
        }
    }

    // ─── Navigasi Tab (Buku Fisik, Digital, MARC21, Ekspor & Sitasi) ───
    function switchDetailTab(tabName) {
        const tabs = {
            fisik:   { btn: 'tabBtnFisik',   pane: 'paneFisik' },
            digital: { btn: 'tabBtnDigital', pane: 'paneDigital' },
            marc:    { btn: 'tabBtnMarc',    pane: 'paneMarc' },
            ekspor:  { btn: 'tabBtnEkspor',  pane: 'paneEkspor' }
        };

        Object.keys(tabs).forEach(function (key) {
            const btnEl  = document.getElementById(tabs[key].btn);
            const paneEl = document.getElementById(tabs[key].pane);
            const isActive = (key === tabName);

            if (btnEl) {
                btnEl.classList.toggle('active', isActive);
                btnEl.setAttribute('aria-selected', isActive ? 'true' : 'false');
            }
            if (paneEl) {
                paneEl.style.display = isActive ? 'block' : 'none';
            }
        });
    }

    function openExportCitationTab() {
        switchDetailTab('ekspor');
        const section = document.getElementById('section-eksemplar');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // ─── Pencarian & Filter Status Eksemplar ───
    let activeEksStatusFilter = 'all';

    function setEksStatusFilter(status, btnEl) {
        activeEksStatusFilter = status;
        document.querySelectorAll('.opac-filter-btn').forEach(function (b) {
            b.classList.remove('active');
        });
        if (btnEl) {
            btnEl.classList.add('active');
        }
        applyEksTableFilter();
    }

    function resetEksFilter() {
        const searchInput = document.getElementById('eksSearchInput');
        if (searchInput) searchInput.value = '';
        const allBtn = document.querySelector('.opac-filter-btn[data-eks-filter="all"]');
        setEksStatusFilter('all', allBtn);
    }

    function applyEksTableFilter() {
        const searchInput = document.getElementById('eksSearchInput');
        const query = searchInput ? searchInput.value.trim().toLowerCase() : '';
        const rows = document.querySelectorAll('#eksTableBody tr.eks-data-row');
        const emptyRow = document.getElementById('eksEmptyFilterRow');

        let visibleCount = 0;
        rows.forEach(function (row) {
            const rowStatus = row.getAttribute('data-status') || 'avail';
            const rowSearch = row.getAttribute('data-search') || '';

            const matchStatus = (activeEksStatusFilter === 'all') || (rowStatus === activeEksStatusFilter);
            const matchQuery  = (query === '') || (rowSearch.indexOf(query) !== -1);

            if (matchStatus && matchQuery) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (emptyRow) {
            emptyRow.style.display = (rows.length > 0 && visibleCount === 0) ? '' : 'none';
        }
    }

    // ─── Scroll & Tindakan Cepat Eksemplar ───
    function scrollToEksemplarAndPinjam() {
        switchDetailTab('fisik');
        const section = document.getElementById('section-eksemplar');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        if (catalogData.firstAvailBarcode) {
            openPinjamDialog(catalogData.firstAvailBarcode, catalogData.callNumber, catalogData.defaultLocation);
        } else {
            showToast('Silakan periksa daftar ketersediaan eksemplar pada tabel di bawah.', 'info');
        }
    }

    function scrollToEksemplarAndFilterAvail() {
        switchDetailTab('fisik');
        const availBtn = document.querySelector('.opac-filter-btn[data-eks-filter="avail"]');
        setEksStatusFilter('avail', availBtn);
        const section = document.getElementById('section-eksemplar');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function scrollToAndHighlightBarcodes() {
        switchDetailTab('fisik');
        resetEksFilter();
        const section = document.getElementById('section-eksemplar');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        document.querySelectorAll('#tbl_eksemplars_fisik .barcode-cell').forEach(function (cell) {
            cell.classList.remove('barcode-highlight-flash');
            void cell.offsetWidth;
            cell.classList.add('barcode-highlight-flash');
        });
    }

    function openPinjamDialog(barcode, callNum, locationName) {
        const bcEl   = document.getElementById('modalPinjamBarcode');
        const callEl = document.getElementById('modalPinjamCall');
        const locEl  = document.getElementById('modalPinjamLoc');

        if (bcEl) bcEl.textContent = barcode || '-';
        if (callEl) callEl.textContent = callNum || catalogData.callNumber || '-';
        if (locEl) locEl.textContent = locationName || catalogData.defaultLocation || '-';

        const modalEl = document.getElementById('pinjamEksModal');
        if (modalEl) {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    }

    // ─── Simpan Koleksi Favorit (Bookmark via localStorage) ───
    const BOOKMARK_STORAGE_KEY = 'opac_saved_catalogs_v1';

    function getSavedCatalogs() {
        try {
            const raw = localStorage.getItem(BOOKMARK_STORAGE_KEY);
            return raw ? JSON.parse(raw) : [];
        } catch (e) {
            return [];
        }
    }

    function updateBookmarkButtonState() {
        const list = getSavedCatalogs();
        const isSaved = list.indexOf(Number(catalogData.id)) !== -1;
        const btn = document.getElementById('btnBookmarkCatalog');
        const icon = document.getElementById('iconBookmarkCatalog');

        if (btn && icon) {
            btn.classList.toggle('is-active', isSaved);
            icon.className = isSaved ? 'fa-solid fa-bookmark' : 'fa-regular fa-bookmark';
        }
    }

    function toggleBookmarkCatalog() {
        let list = getSavedCatalogs();
        const idNum = Number(catalogData.id);
        const idx = list.indexOf(idNum);

        if (idx === -1) {
            list.push(idNum);
            showToast('Koleksi disimpan ke daftar bacaan Anda.', 'success');
        } else {
            list.splice(idx, 1);
            showToast('Koleksi dihapus dari daftar bacaan.', 'info');
        }

        try {
            localStorage.setItem(BOOKMARK_STORAGE_KEY, JSON.stringify(list));
        } catch (e) {}
        updateBookmarkButtonState();
    }

    // ─── Salin, Bagikan, & Ekspor ───
    function copySingleBarcode(barcode) {
        if (!barcode || barcode === '-') return;
        copyTextWithToast(barcode, 'Nomor barcode ' + barcode + ' disalin ke clipboard!');
    }

    function copyToClipboard() {
        const textData = `Judul: ${catalogData.title}
Pengarang: ${catalogData.author || 'N/A'}
Penerbit: ${catalogData.publisher || 'N/A'}
Tahun: ${catalogData.publishYear || 'N/A'}
ISBN: ${catalogData.isbn || 'N/A'}
Nomor Panggil: ${catalogData.callNumber || 'N/A'}
Tautan: ${catalogData.currentUrl}`;

        copyTextWithToast(textData, 'Detail katalog telah disalin ke clipboard!');
    }

    function copyLink() {
        copyTextWithToast(catalogData.currentUrl, 'Tautan katalog telah disalin ke clipboard!');
    }

    function copyTextWithToast(text, successMsg) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(function () {
                showToast(successMsg, 'success');
            }).catch(function () {
                fallbackCopyTextToClipboard(text, successMsg);
            });
        } else {
            fallbackCopyTextToClipboard(text, successMsg);
        }
    }

    function fallbackCopyTextToClipboard(text, successMsg) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.top = '0';
        textArea.style.left = '0';
        textArea.style.opacity = '0';

        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();

        try {
            const successful = document.execCommand('copy');
            if (successful) {
                showToast(successMsg || 'Berhasil disalin ke clipboard!', 'success');
            } else {
                showToast('Gagal menyalin ke clipboard', 'danger');
            }
        } catch (err) {
            showToast('Peramban tidak mendukung salin otomatis', 'warning');
        }

        document.body.removeChild(textArea);
    }

    function shareViaEmail() {
        const subject = encodeURIComponent('Katalog Perpustakaan: ' + catalogData.title);
        const body = encodeURIComponent(`Berikut informasi koleksi katalog perpustakaan:

Judul: ${catalogData.title}
Pengarang: ${catalogData.author || '-'}
Penerbit: ${catalogData.publisher || '-'} (${catalogData.publishYear || '-'})
Nomor Panggil: ${catalogData.callNumber || '-'}

Lihat detail lengkap di: ${catalogData.currentUrl}`);

        window.open(`mailto:?subject=${subject}&body=${body}`);
    }

    function shareViaWhatsapp() {
        const text = encodeURIComponent(`*Katalog Perpustakaan*

*Judul:* ${catalogData.title}
*Pengarang:* ${catalogData.author || '-'}
*Penerbit:* ${catalogData.publisher || '-'} (${catalogData.publishYear || '-'})
*Nomor Panggil:* ${catalogData.callNumber || '-'}

Tautan: ${catalogData.currentUrl}`);

        window.open(`https://wa.me/?text=${text}`, '_blank');
    }

    function closeShareMenu() {
        const menuBagikan = document.getElementById('bagikanDropdownMenu');
        if (menuBagikan) menuBagikan.style.display = 'none';
    }

    function exportBibTeX() {
        const controlNumber = (catalogData.controlNumber || '').replace(/[^a-zA-Z0-9]/g, '') || 'catalog' + catalogData.id;
        const bibtex = `@book{${controlNumber},
    title = {${catalogData.title}},
    author = {${catalogData.author}},
    publisher = {${catalogData.publisher}},
    year = {${catalogData.publishYear}},
    isbn = {${catalogData.isbn}},
    address = {${catalogData.publishLocation}},
    note = {Available at: ${catalogData.currentUrl}}
}`;

        downloadFile(`catalog_${catalogData.id}.bib`, bibtex, 'text/plain');
    }

    function exportRIS() {
        const ris = `TY  - BOOK
TI  - ${catalogData.title}
AU  - ${catalogData.author}
PB  - ${catalogData.publisher}
PY  - ${catalogData.publishYear}
SN  - ${catalogData.isbn}
CY  - ${catalogData.publishLocation}
CN  - ${catalogData.callNumber}
UR  - ${catalogData.currentUrl}
N2  - ${(catalogData.note || '').substring(0, 250)}
KW  - ${catalogData.subject}
ER  - 
`;

        downloadFile(`catalog_${catalogData.id}.ris`, ris, 'application/x-research-info-systems');
    }

    function exportCSV() {
        const csvContent = [
            ['Field', 'Value'],
            ['ID', catalogData.id],
            ['Control Number', catalogData.controlNumber],
            ['Title', catalogData.title],
            ['Author', catalogData.author],
            ['Publisher', catalogData.publisher],
            ['Publish Location', catalogData.publishLocation],
            ['Publish Year', catalogData.publishYear],
            ['ISBN', catalogData.isbn],
            ['Call Number', catalogData.callNumber],
            ['Subject', catalogData.subject],
            ['Physical Description', catalogData.physicalDescription],
            ['Languages', catalogData.languages],
            ['Note', catalogData.note],
            ['URL', catalogData.currentUrl]
        ];

        const csv = csvContent.map(row =>
            row.map(cell => `"${String(cell || '').replace(/"/g, '""')}"`).join(',')
        ).join('\n');

        downloadFile(`catalog_${catalogData.id}.csv`, csv, 'text/csv');
    }

    function downloadFile(filename, content, mimeType = 'text/plain') {
        try {
            const blob = new Blob([content], { type: mimeType + ';charset=utf-8' });
            const url = window.URL.createObjectURL(blob);
            const element = document.createElement('a');
            element.setAttribute('href', url);
            element.setAttribute('download', filename);
            element.style.display = 'none';

            document.body.appendChild(element);
            element.click();
            document.body.removeChild(element);

            setTimeout(() => {
                window.URL.revokeObjectURL(url);
            }, 100);

            showToast(`Berkas ${filename} berhasil diunduh!`, 'success');
        } catch (error) {
            console.error('Download error:', error);
            showToast('Gagal mengunduh berkas. Silakan coba lagi.', 'danger');
        }
    }

    function showToast(message, type = 'info') {
        const existingToasts = document.querySelectorAll('.custom-toast');
        existingToasts.forEach(toast => toast.remove());

        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed custom-toast`;
        toast.style.cssText = 'top: 88px; right: 24px; z-index: 9999; max-width: 380px; border-radius: 12px; box-shadow: 0 10px 25px rgba(15,23,42,0.15);';

        const iconClass = type === 'success' ? 'check-circle' :
            type === 'danger' ? 'exclamation-circle' :
            type === 'warning' ? 'exclamation-triangle' : 'info-circle';

        toast.innerHTML = `
            <i class="fas fa-${iconClass} me-2"></i>
            <span>${message}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 150);
            }
        }, 3800);
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateBookmarkButtonState();

        // Live search input eksemplar
        const eksSearchInput = document.getElementById('eksSearchInput');
        if (eksSearchInput) {
            eksSearchInput.addEventListener('input', applyEksTableFilter);
        }

        // Dropdown Unduh MARC
        const btnMarc  = document.getElementById('downloadMarcDropdown');
        const menuMarc = document.getElementById('marcDropdownMenu');
        const caretMarc = document.getElementById('marcCaretIcon');

        if (btnMarc && menuMarc) {
            btnMarc.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = menuMarc.style.display === 'block';
                menuMarc.style.display = isOpen ? 'none' : 'block';
                btnMarc.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
                if (caretMarc) {
                    caretMarc.className = isOpen ? 'fa-solid fa-caret-down' : 'fa-solid fa-caret-up';
                }
            });

            document.addEventListener('click', function (e) {
                const wrapper = document.getElementById('marcDropdownWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    menuMarc.style.display = 'none';
                    btnMarc.setAttribute('aria-expanded', 'false');
                    if (caretMarc) caretMarc.className = 'fa-solid fa-caret-down';
                }
            });
        }

        // Dropdown Bagikan
        const btnBagikan  = document.getElementById('bagikanDropdownBtn');
        const menuBagikan = document.getElementById('bagikanDropdownMenu');

        if (btnBagikan && menuBagikan) {
            btnBagikan.addEventListener('click', function (e) {
                e.stopPropagation();
                const isOpen = menuBagikan.style.display === 'block';
                menuBagikan.style.display = isOpen ? 'none' : 'block';
                btnBagikan.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
            });

            document.addEventListener('click', function (e) {
                const wrapper = document.getElementById('bagikanDropdownWrapper');
                if (wrapper && !wrapper.contains(e.target)) {
                    menuBagikan.style.display = 'none';
                    btnBagikan.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>

<?= $this->endSection() ?>
