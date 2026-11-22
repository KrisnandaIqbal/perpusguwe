<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?php
helper(['opac', 'opac_beranda']);

$catalogId    = $catalog['ID'] ?? 0;
$rawTitle     = trim((string) ($catalog['Title'] ?? '')) ?: 'Tanpa judul';
$rawAuthor    = trim((string) ($catalog['Author'] ?? ''));
$rawEdition   = trim((string) ($catalog['Edition'] ?? ''));
$rawPublisher = trim((string) ($catalog['Publisher'] ?? ''), " .,;:");
$rawPubLoc    = trim((string) ($catalog['PublishLocation'] ?? ''), " .,;:");
$rawPubYear   = trim((string) ($catalog['PublishYear'] ?? ''), " .,;:");
$rawPubYear   = in_array($rawPubYear, ['', '0000', '-'], true) ? '' : $rawPubYear;
$rawSubject   = trim((string) ($catalog['Subject'] ?? ''));
$rawPhysDesc  = trim((string) ($catalog['PhysicalDescription'] ?? ''));
$rawIsbn      = trim((string) ($catalog['ISBN'] ?? ''));
$rawCall      = trim((string) ($catalog['CallNumber'] ?? ''));
$rawControl   = trim((string) ($catalog['ControlNumber'] ?? ''));
$rawBibId     = trim((string) ($catalog['BIBID'] ?? ''));
$rawLang      = trim((string) ($catalog['Languages'] ?? ''));
$rawNote      = trim((string) ($catalog['Note'] ?? ''));
$rawNote      = $rawNote === '-' ? '' : $rawNote;

// Judul utama dan anak judul dipisah di titik dua atau " / "
$mainTitle = $rawTitle;
$subTitle  = '';
if (strpos($rawTitle, ':') !== false) {
    [$mainTitle, $subTitle] = array_map('trim', explode(':', $rawTitle, 2));
} elseif (strpos($rawTitle, ' / ') !== false) {
    [$mainTitle, $subTitle] = array_map('trim', explode(' / ', $rawTitle, 2));
}

$authorList = array_values(array_filter(array_map(
    static fn ($a) => trim($a, " \t\n\r\0\x0B.,;:"),
    preg_split('/;+/', $rawAuthor)
)));
$primaryAuthor = $authorList[0] ?? 'Tim penyusun';

$imprint = implode(' · ', array_filter([$rawPublisher, $rawPubLoc, $rawPubYear]));

$langMap = [
    'ind' => 'Bahasa Indonesia', 'id' => 'Bahasa Indonesia', 'eng' => 'Bahasa Inggris', 'en' => 'Bahasa Inggris',
    'ara' => 'Bahasa Arab', 'jpn' => 'Bahasa Jepang', 'chi' => 'Bahasa Mandarin', 'ger' => 'Bahasa Jerman',
    'fre' => 'Bahasa Prancis', 'dut' => 'Bahasa Belanda',
];
$displayLanguage = $langMap[strtolower($rawLang)] ?? $rawLang;

// Eksemplar fisik dan koleksi digital
$fisikList   = is_array($roweksemplar) ? $roweksemplar : [];
$digitalList = is_array($roweksemplar_drm) ? $roweksemplar_drm : [];
$flipList    = is_array($files_digital ?? null) ? $files_digital : [];

$totalFisik = count($fisikList);
$availFisik = 0;
$firstCallFromEks = '';
$firstLocationFromEks = '';
foreach ($fisikList as $eks) {
    $stId   = (int) ($eks->Status_id ?? 0);
    $stName = trim((string) ($eks->StatusName ?? ''));
    $isLoan = ($stId === 5) || (stripos($stName, 'pinjam') !== false);
    if (!$isLoan && ($stId === 1 || stripos($stName, 'tersedia') !== false || $stName === '')) {
        $availFisik++;
    }
    $firstCallFromEks     = $firstCallFromEks !== '' ? $firstCallFromEks : trim((string) ($eks->CallNumber ?? ''));
    $firstLocationFromEks = $firstLocationFromEks !== '' ? $firstLocationFromEks : trim((string) ($eks->LocationName ?? ''));
}

$displayCallNumber = $rawCall !== '' ? $rawCall : ($firstCallFromEks !== '' ? $firstCallFromEks : '-');
$displayLocation   = $firstLocationFromEks;
$totalDigital      = count($digitalList) + count($flipList);

$formattedDate = '';
if (!empty($catalog['CreateDate']) && ($ts = strtotime($catalog['CreateDate'])) !== false) {
    $formattedDate = date('d M Y', $ts);
}

$subjects = function_exists('splitSubjects') ? splitSubjects($rawSubject) : array_filter(array_map('trim', explode(';', $rawSubject)));
$subjects = array_values($subjects);

// Sampul: gambar asli bila ada, kalau tidak sampul buatan yang sama dengan kartu rak
$meta  = opacBookMeta($catalog);
$gaya  = $meta['coverStyle'];

$cariUrl = static fn (string $q, string $by) => base_url('opac?search=' . urlencode($q) . '&search_by=' . $by);

// Baris informasi bibliografi: hanya yang terisi. Nilai berupa HTML yang sudah di-escape.
$baris = [];
if ($authorList) {
    $baris[] = ['Pengarang', implode(', ', array_map(static fn ($a) => '<a href="' . esc($cariUrl($a, 'Author'), 'attr') . '">' . esc($a) . '</a>', $authorList))];
}
if ($rawPublisher !== '' || $rawPubLoc !== '') {
    $baris[] = ['Penerbit', esc(implode(', ', array_filter([$rawPublisher, $rawPubLoc])))];
}
if ($rawPubYear !== '') {
    $baris[] = ['Tahun terbit', esc($rawPubYear)];
}
if ($rawEdition !== '' && $rawEdition !== '-') {
    $baris[] = ['Edisi', esc($rawEdition)];
}
if ($displayLanguage !== '') {
    $baris[] = ['Bahasa', esc($displayLanguage)];
}
$baris[] = ['Nomor panggil', '<span class="od-mono">' . esc($displayCallNumber) . '</span>'];
if ($rawPhysDesc !== '') {
    $baris[] = ['Deskripsi fisik', esc($rawPhysDesc)];
}
if ($rawIsbn !== '' && $rawIsbn !== '-') {
    $baris[] = ['ISBN', '<span class="od-mono">' . esc($rawIsbn) . '</span>'];
}
if ($subjects) {
    $baris[] = ['Subjek', implode(', ', array_map(static fn ($s) => '<a href="' . esc($cariUrl($s, 'Subject'), 'attr') . '">' . esc($s) . '</a>', $subjects))];
}
if ($displayLocation !== '') {
    $baris[] = ['Lokasi koleksi', esc($displayLocation)];
}
if ($formattedDate !== '') {
    $baris[] = ['Ditambahkan', esc($formattedDate)];
}

$idKatalog = implode(' · ', array_filter([
    'ID katalog: ' . $catalogId,
    $rawControl !== '' ? 'Control Number: ' . $rawControl : '',
    $rawBibId !== '' ? 'BIB ID: ' . $rawBibId : '',
]));
?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/opac_detail.css') ?>?v=<?= filemtime(FCPATH . 'assets/css/opac_detail.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ─── Hero: sampul dan informasi utama ─── -->
<section class="od-hero" aria-labelledby="odJudul">
    <div class="ob-shell">
        <nav class="od-krumb" aria-label="Breadcrumb">
            <a href="<?= base_url() ?>">Beranda</a><span aria-hidden="true">/</span>
            <a href="<?= base_url('opac') ?>">OPAC</a><span aria-hidden="true">/</span>
            <span aria-current="page">Detail katalog</span>
        </nav>

        <div class="od-hero-grid">
            <div class="od-sampul">
                <?php if ($meta['hasCover']) : ?>
                    <img class="ob-cover-img" src="<?= esc($meta['coverImgSrc'], 'attr') ?>" alt="Sampul <?= esc($rawTitle, 'attr') ?>"
                         onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                <?php endif; ?>
                <div class="ob-cover<?= $gaya['terang'] ? ' ob-cover-terang' : '' ?>" style="background:<?= $gaya['warna'][0] ?>;color:<?= $gaya['warna'][1] ?>" aria-hidden="true" <?= $meta['hasCover'] ? 'hidden' : '' ?>>
                    <span class="ob-cover-punggung"></span>
                    <span class="ob-cover-kategori"><?= esc($meta['topLabel']) ?></span>
                    <strong class="ob-cover-judul"><?= esc($mainTitle) ?></strong>
                    <span class="ob-cover-penulis"><?= esc(mb_strimwidth($primaryAuthor, 0, 34, '…')) ?></span>
                    <span class="ob-cover-bulat" style="background:<?= $gaya['warna'][1] ?>"></span>
                </div>
            </div>

            <div class="od-hero-teks">
                <h1 class="od-judul" id="odJudul"><?= esc($mainTitle) ?></h1>
                <?php if ($subTitle !== '') : ?><p class="od-anak"><?= esc($subTitle) ?></p><?php endif; ?>

                <div class="od-penulis">
                    <?php if ($authorList) : ?>
                        <a href="<?= esc($cariUrl($primaryAuthor, 'Author'), 'attr') ?>"><?= esc($primaryAuthor) ?></a><?= count($authorList) > 1 ? ' dan ' . (count($authorList) - 1) . ' penulis lain' : '' ?>
                    <?php else : ?>
                        <?= esc($primaryAuthor) ?>
                    <?php endif; ?>
                </div>
                <?php if ($imprint !== '') : ?><p class="od-terbit"><?= esc($imprint) ?></p><?php endif; ?>

                <ul class="od-stok" aria-label="Ketersediaan">
                    <?php if ($totalFisik > 0) : ?>
                        <li class="<?= $availFisik > 0 ? 'od-stok-ada' : 'od-stok-habis' ?>">
                            <?= $availFisik > 0 ? $availFisik . ' dari ' . $totalFisik . ' eksemplar tersedia' : 'Semua eksemplar sedang dipinjam' ?>
                        </li>
                    <?php else : ?>
                        <li class="od-stok-info">Eksemplar fisik belum terdata</li>
                    <?php endif; ?>
                    <?php if ($totalDigital > 0) : ?><li class="od-stok-ada"><?= $totalDigital ?> koleksi digital</li><?php endif; ?>
                </ul>

                <div class="od-aksi">
                    <?php if (!empty($flipList)) : ?>
                        <a class="od-btn od-btn-utama" href="<?= base_url('katalog/view_decrypted/' . encData($flipList[0]->ID)) ?>" target="_blank" rel="noopener">Baca flipbook</a>
                    <?php elseif (!empty($digitalList)) : ?>
                        <a class="od-btn od-btn-utama" href="<?= base_url('opac/baca-digital/' . $catalogId) ?>" target="_blank" rel="noopener">Baca PDF digital</a>
                    <?php endif; ?>
                    <button type="button" class="od-btn <?= $totalDigital > 0 ? 'od-btn-lain' : 'od-btn-utama' ?>" data-ke="sectionInventory">Lihat ketersediaan</button>
                    <button type="button" class="od-btn od-btn-teks" id="odSalin">Salin tautan</button>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="ob-shell od-main">

    <!-- ─── Ringkasan dan informasi bibliografi ─── -->
    <div class="od-dua<?= $rawNote !== '' ? ' od-dua-ringkasan' : '' ?>">
        <?php if ($rawNote !== '') : ?>
            <section class="od-kartu" aria-labelledby="odRingkasan">
                <h2 id="odRingkasan">Ringkasan</h2>
                <p class="od-ringkas"><?= esc($rawNote) ?></p>
            </section>
        <?php endif; ?>

        <section class="od-kartu" aria-labelledby="odBiblio">
            <h2 id="odBiblio">Informasi bibliografi</h2>
            <dl class="od-info">
                <?php foreach ($baris as [$label, $nilai]) : ?>
                    <div><dt><?= $label ?></dt><dd><?= $nilai ?></dd></div>
                <?php endforeach; ?>
            </dl>
        </section>
    </div>

    <!-- ─── Ketersediaan koleksi ─── -->
    <section class="od-kartu" id="sectionInventory" aria-labelledby="odKoleksi">
        <h2 id="odKoleksi">Ketersediaan koleksi</h2>
        <p class="od-sub">Eksemplar fisik, koleksi digital, dan metadata katalog.</p>

        <div class="od-tab" role="tablist" aria-label="Pilihan tampilan ketersediaan">
            <button type="button" role="tab" id="tabFisikBtn" aria-selected="true" aria-controls="tabFisikPane" data-tab="fisik">Buku fisik (<?= $totalFisik ?>)</button>
            <button type="button" role="tab" id="tabDigitalBtn" aria-selected="false" aria-controls="tabDigitalPane" data-tab="digital" tabindex="-1">Koleksi digital (<?= $totalDigital ?>)</button>
            <button type="button" role="tab" id="tabMarcBtn" aria-selected="false" aria-controls="tabMarcPane" data-tab="marc" tabindex="-1">Metadata MARC21</button>
        </div>

        <!-- Buku fisik -->
        <div id="tabFisikPane" role="tabpanel" aria-labelledby="tabFisikBtn">
            <div class="od-alat">
                <input type="search" class="od-cari" id="inventorySearchInput" placeholder="Cari barcode atau lokasi rak" aria-label="Cari nomor barcode atau lokasi rak">
                <div class="od-saring" role="group" aria-label="Saring status eksemplar">
                    <button type="button" data-status="all" aria-pressed="true">Semua</button>
                    <button type="button" data-status="avail" aria-pressed="false">Tersedia</button>
                    <button type="button" data-status="loan" aria-pressed="false">Dipinjam</button>
                </div>
            </div>

            <div class="od-tabel">
                <table id="inventoryTable">
                    <thead>
                        <tr>
                            <th scope="col">Nomor barcode</th>
                            <th scope="col">Nomor panggil</th>
                            <th scope="col">Akses</th>
                            <th scope="col">Lokasi koleksi</th>
                            <th scope="col">Ketersediaan</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <?php foreach ($fisikList as $eksItem) :
                            $stId       = (int) ($eksItem->Status_id ?? 0);
                            $stName     = trim((string) ($eksItem->StatusName ?? '')) ?: 'Tersedia';
                            $isDipinjam = ($stId === 5) || (stripos($stName, 'pinjam') !== false);
                            $barcodeVal = trim((string) ($eksItem->NomorBarcode ?? '')) ?: '-';
                            $callVal    = trim((string) ($eksItem->CallNumber ?? '')) ?: $displayCallNumber;
                            $ruleVal    = trim((string) ($eksItem->RuleName ?? '')) ?: 'Dapat dipinjam';
                            $locVal     = trim((string) ($eksItem->LocationName ?? '')) ?: '-';
                        ?>
                            <tr class="inventory-data-row" data-status="<?= $isDipinjam ? 'loan' : 'avail' ?>" data-search="<?= esc(strtolower($barcodeVal . ' ' . $callVal . ' ' . $locVal . ' ' . $ruleVal), 'attr') ?>">
                                <td class="od-kode"><?= esc($barcodeVal) ?></td>
                                <td class="od-kode"><?= esc($callVal) ?></td>
                                <td><?= esc($ruleVal) ?></td>
                                <td><?= esc($locVal) ?></td>
                                <td class="<?= $isDipinjam ? 'od-status-habis' : 'od-status-ada' ?>"><?= $isDipinjam ? 'Dipinjam' : esc($stName) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="inventoryEmptyRow" <?= $totalFisik > 0 ? 'hidden' : '' ?>>
                            <td colspan="5">
                                <div class="od-kosong">
                                    <strong id="inventoryEmptyTitle"><?= $totalFisik > 0 ? 'Tidak ada eksemplar yang cocok' : 'Belum ada eksemplar fisik terdaftar' ?></strong>
                                    <span><?= $totalFisik > 0 ? 'Ubah kata kunci atau pilih status lain.' : 'Tanyakan ke petugas layanan perpustakaan.' ?></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <?php if ($totalDigital > 0) : ?>
                <div class="od-digital">
                    <span>Buku ini juga bisa dibaca secara digital.</span>
                    <button type="button" class="od-btn od-btn-teks" data-tab="digital">Lihat koleksi digital (<?= $totalDigital ?>)</button>
                </div>
            <?php endif; ?>

            <p class="od-ids"><?= esc($idKatalog) ?></p>
        </div>

        <!-- Koleksi digital -->
        <div id="tabDigitalPane" role="tabpanel" aria-labelledby="tabDigitalBtn" hidden>
            <?php if (!empty($flipList)) : ?>
                <ul class="od-daftar-digital">
                    <?php foreach ($flipList as $idx => $fl) : ?>
                        <li>
                            <div>
                                <strong>Dokumen digital <?= $idx + 1 ?></strong>
                                <span>Berkas interaktif, dibaca di browser</span>
                            </div>
                            <a class="od-btn od-btn-utama" href="<?= base_url('katalog/view_decrypted/' . encData($fl->ID)) ?>" target="_blank" rel="noopener">Buka flipbook</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($digitalList)) : ?>
                <div class="od-tabel">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Nomor barcode</th>
                                <th scope="col">Nomor panggil</th>
                                <th scope="col">Lokasi</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($digitalList as $dr) : ?>
                                <tr>
                                    <td class="od-kode"><?= esc($dr->NomorBarcode) ?></td>
                                    <td class="od-kode"><?= esc($dr->CallNumber) ?></td>
                                    <td><?= esc($dr->LocationName) ?></td>
                                    <td class="od-status-ada">Tersedia digital</td>
                                    <td><a class="od-btn od-btn-lain" href="<?= base_url('opac/baca-digital/' . $catalogId) ?>" target="_blank" rel="noopener">Baca PDF</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php elseif (empty($flipList)) : ?>
                <div class="od-kosong">
                    <strong>Koleksi digital belum tersedia</strong>
                    <span>Buku ini baru ada dalam versi cetak di perpustakaan.</span>
                </div>
            <?php endif; ?>
        </div>

        <!-- MARC21 -->
        <div id="tabMarcPane" role="tabpanel" aria-labelledby="tabMarcBtn" hidden>
            <?php if (!empty($marc)) : ?>
                <div class="od-tabel">
                    <table>
                        <thead>
                            <tr>
                                <th scope="col">Tag</th>
                                <th scope="col">Ind 1</th>
                                <th scope="col">Ind 2</th>
                                <th scope="col">Nilai ruas (subfield)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($marc as $field) : ?>
                                <tr>
                                    <td class="od-kode"><strong><?= esc($field->Tag) ?></strong></td>
                                    <td class="od-kode"><?= esc($field->Indicator1 ?: '#') ?></td>
                                    <td class="od-kode"><?= esc($field->Indicator2 ?: '#') ?></td>
                                    <td><?= esc($field->Value) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <div class="od-kosong">
                    <strong>Ruas MARC21 belum tersedia</strong>
                    <span>Data MARC untuk buku ini belum dipetakan.</span>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ─── Buku terkait ─── -->
    <?php if (!empty($buku_terkait)) : ?>
        <?= beranda_rak('buku-terkait', 'Buku terkait', '', $buku_terkait) ?>
    <?php endif; ?>

    <!-- ─── Pencarian terkait ─── -->
    <section aria-labelledby="odPencarian">
        <h2 class="od-rak-judul" id="odPencarian">Pencarian terkait</h2>
        <ul class="od-pencarian">
            <?php if ($authorList) : ?>
                <li><a href="<?= esc($cariUrl($primaryAuthor, 'Author'), 'attr') ?>">Karya <?= esc($primaryAuthor) ?></a></li>
            <?php endif; ?>
            <?php if ($rawPublisher !== '') : ?>
                <li><a href="<?= esc($cariUrl($rawPublisher, 'Publisher'), 'attr') ?>"><?= esc($rawPublisher) ?></a></li>
            <?php endif; ?>
            <?php if ($subjects) : ?>
                <li><a href="<?= esc($cariUrl($subjects[0], 'Subject'), 'attr') ?>">Subjek: <?= esc($subjects[0]) ?></a></li>
            <?php endif; ?>
            <?php if ($rawPubYear !== '') : ?>
                <li><a href="<?= base_url('opac?PublishYear=' . urlencode($rawPubYear)) ?>">Tahun <?= esc($rawPubYear) ?></a></li>
            <?php endif; ?>
        </ul>
    </section>
</div>

<script src="<?= base_url('assets/js/opac_beranda.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/opac_beranda.js') ?>" defer></script>
<script src="<?= base_url('assets/js/opac_detail.js') ?>?v=<?= filemtime(FCPATH . 'assets/js/opac_detail.js') ?>" defer></script>
<script>window.OPAC_DETAIL = { url: <?= json_encode(current_url()) ?> };</script>

<?= $this->endSection() ?>
