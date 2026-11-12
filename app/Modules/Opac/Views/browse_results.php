<?php
/**
 * Partial View: Hasil Browse Katalog (Judul & Pengarang)
 * Digunakan untuk render awal di browse.php dan update live via AJAX tanpa refresh.
 */
?>

<?php if (!empty($catalogs)): ?>

    <!-- Results Header & Controls -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 bg-white rounded-4 border border-light shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <?php if (!empty($search_query)): ?>
                <div class="letter-badge-circle" style="width: 40px; height: 40px; font-size: 1.1rem; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">
                        Hasil Pencarian <?= $browse_type === 'author' ? 'Pengarang' : 'Judul' ?>: <span class="browse-highlight-blur"><?= esc($search_query) ?></span>
                    </h4>
                    <small class="text-muted">
                        Ditemukan <strong><?= number_format(isset($pager) ? $pager->getTotal('browse') : count($catalogs)) ?></strong> koleksi
                        &nbsp;&bull;&nbsp;
                        <a href="javascript:void(0);" onclick="resetBrowseSearch()" class="text-primary text-decoration-none fw-semibold">
                            <i class="fa-solid fa-rotate-left me-1"></i>Kembali ke Huruf <?= esc($letter ?? 'A') ?>
                        </a>
                    </small>
                </div>
            <?php else: ?>
                <div class="letter-badge-circle" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <?= esc($letter ?? 'A') ?>
                </div>
                <div>
                    <h4 class="fw-bold mb-0 text-dark">
                        <?= $browse_type === 'author' ? 'Pengarang' : 'Judul' ?> berawalan "<strong><?= esc($letter ?? 'A') ?></strong>"
                    </h4>
                    <small class="text-muted">
                        Ditemukan <strong><?= number_format(isset($pager) ? $pager->getTotal('browse') : count($catalogs)) ?></strong> koleksi
                    </small>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- View Mode Toggle -->
            <div class="btn-group rounded-pill overflow-hidden border" role="group">
                <button type="button" class="view-mode-btn active" id="btnViewGrid" onclick="switchView('grid')" title="Tampilan Kotak Grid">
                    <i class="fa-solid fa-table-cells me-1"></i>Grid
                </button>
                <button type="button" class="view-mode-btn" id="btnViewList" onclick="switchView('list')" title="Tampilan Daftar List">
                    <i class="fa-solid fa-list me-1"></i>List
                </button>
                <button type="button" class="view-mode-btn" id="btnViewTable" onclick="switchView('table')" title="Tampilan Tabel Ringkas">
                    <i class="fa-solid fa-table me-1"></i>Tabel
                </button>
            </div>

            <!-- Export Dropdown -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fa-solid fa-file-export me-1"></i>Ekspor
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                    <li><a class="dropdown-item py-2" href="#" onclick="exportBrowseResults('excel'); return false;"><i class="fa-solid fa-file-excel text-success me-2"></i>Ekspor ke Excel</a></li>
                    <li><a class="dropdown-item py-2" href="#" onclick="exportBrowseResults('csv'); return false;"><i class="fa-solid fa-file-csv text-info me-2"></i>Ekspor ke CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- 1. Grid View (Modern Cards) -->
    <div id="gridView" class="view-container">
        <div class="row g-4">
            <?php foreach ($catalogs as $catalog): ?>
                <?= renderModernBookCard($catalog, 'col-xl-3 col-lg-4 col-md-6') ?>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 2. List View (Horizontal Modern Card) -->
    <div id="listView" class="view-container" style="display: none;">
        <div class="d-flex flex-column gap-3">
            <?php foreach ($catalogs as $catalog): ?>
                <?php
                $isArr      = is_array($catalog);
                $id         = $isArr ? ($catalog['ID'] ?? 0) : ($catalog->ID ?? 0);
                $title      = trim((string)($isArr ? ($catalog['Title'] ?? 'Tanpa Judul') : ($catalog->Title ?? 'Tanpa Judul')));
                $author     = trim((string)($isArr ? ($catalog['Author'] ?? '') : ($catalog->Author ?? '')));
                $publisher  = trim((string)($isArr ? ($catalog['Publisher'] ?? '') : ($catalog->Publisher ?? '')));
                $year       = trim((string)($isArr ? ($catalog['PublishYear'] ?? '') : ($catalog->PublishYear ?? '')));
                $edition    = trim((string)($isArr ? ($catalog['Edition'] ?? '') : ($catalog->Edition ?? '')));
                $subjectRaw = trim((string)($isArr ? ($catalog['Subject'] ?? '') : ($catalog->Subject ?? '')));
                $coverURL   = trim((string)($isArr ? ($catalog['CoverURL'] ?? '') : ($catalog->CoverURL ?? '')));
                $isbn       = trim((string)($isArr ? ($catalog['ISBN'] ?? '') : ($catalog->ISBN ?? '')));
                $note       = trim(strip_tags((string)($isArr ? ($catalog['Note'] ?? '') : ($catalog->Note ?? ''))));
                $controlNo  = trim((string)($isArr ? ($catalog['ControlNumber'] ?? 'N/A') : ($catalog->ControlNumber ?? 'N/A')));

                $catCall    = trim((string)($isArr ? ($catalog['CallNumber'] ?? '') : ($catalog->CallNumber ?? '')));
                $colCall    = trim((string)($isArr ? ($catalog['ColCallNumber'] ?? '') : ($catalog->ColCallNumber ?? '')));
                $callNumber = $catCall !== '' ? $catCall : ($colCall !== '' ? $colCall : 'Koleksi Umum');

                $totalEks   = (int)($isArr ? ($catalog['TotalEks'] ?? 0) : ($catalog->TotalEks ?? 0));
                $availEks   = (int)($isArr ? ($catalog['AvailEks'] ?? 0) : ($catalog->AvailEks ?? 0));
                $shelfLoc   = trim((string)($isArr ? ($catalog['ShelfLocation'] ?? 'Rak Koleksi Umum') : ($catalog->ShelfLocation ?? 'Rak Koleksi Umum')));

                $subjects   = splitSubjects($subjectRaw);
                $firstSubj  = !empty($subjects) ? $subjects[0] : ($publisher !== '' ? $publisher : 'Koleksi Perpustakaan');
                $topLabel   = mb_strtoupper(mb_substr($firstSubj, 0, 18));
                $displayAuthor = ($author !== '' && $author !== '-') ? $author : 'Tim Penyusun / Instansi';
                $detailUrl  = base_url('opac/detail/' . $id);

                // Periksa apakah file cover benar-benar ada di disk
                $hasValidCoverFile = false;
                $coverImgSrc       = '';
                if ($coverURL !== '' && $coverURL !== 'default-cover.jpg' && $coverURL !== 'default-cover.png') {
                    if (filter_var($coverURL, FILTER_VALIDATE_URL)) {
                        $hasValidCoverFile = true;
                        $coverImgSrc       = $coverURL;
                    } else {
                        $localPath = FCPATH . 'uploads/katalog/' . $coverURL;
                        if (is_file($localPath)) {
                            $hasValidCoverFile = true;
                            $coverImgSrc       = base_url('uploads/katalog/' . $coverURL);
                        }
                    }
                }

                // Palet warna untuk mini cover
                $palettes = [
                    ['bg' => 'linear-gradient(145deg, #10254c 0%, #0a1833 100%)', 'accent' => '#f59e0b'],
                    ['bg' => 'linear-gradient(145deg, #093529 0%, #052119 100%)', 'accent' => '#fbbf24'],
                    ['bg' => 'linear-gradient(145deg, #153462 0%, #0c203f 100%)', 'accent' => '#fcd34d'],
                    ['bg' => 'linear-gradient(145deg, #4a1e0c 0%, #2d1106 100%)', 'accent' => '#fbbf24'],
                    ['bg' => 'linear-gradient(145deg, #2b194d 0%, #180d2e 100%)', 'accent' => '#f59e0b'],
                ];
                $palette = $palettes[$id % count($palettes)];

                // Status ketersediaan eksemplar
                if ($totalEks > 0 && $availEks > 0) {
                    $statusBadgeHtml = '<span class="opac-status-pill status-available"><i class="fa-regular fa-circle-check me-1"></i>Tersedia di Rak</span>';
                    $eksCountHtml    = esc($availEks . '/' . $totalEks . ' Eks.');
                } elseif ($totalEks > 0 && $availEks === 0) {
                    $statusBadgeHtml = '<span class="opac-status-pill status-borrowed"><i class="fa-regular fa-clock me-1"></i>Dipinjam</span>';
                    $eksCountHtml    = esc('0/' . $totalEks . ' Eks.');
                } else {
                    $statusBadgeHtml = '<span class="opac-status-pill status-available"><i class="fa-regular fa-circle-check me-1"></i>Tersedia di Rak</span>';
                    $eksCountHtml    = '1/1 Eks.';
                }

                // Modal data untuk pratinjau cepat
                $modalData = [
                    'id'              => $id,
                    'title'           => $title,
                    'author'          => $displayAuthor,
                    'pubAndYear'      => ($publisher !== '' ? $publisher : 'Kemnaker') . ($year !== '' && $year !== '-' ? ' (' . $year . ')' : ''),
                    'shelfLoc'        => $shelfLoc,
                    'isbnAndEdition'  => $isbn !== '' ? $isbn : 'Koleksi Cetak',
                    'callNumber'      => $callNumber,
                    'abstract'        => $note !== '' ? $note : 'Koleksi "' . $title . '" karya ' . $displayAuthor,
                    'subjects'        => !empty($subjects) ? $subjects : ['Koleksi Umum'],
                    'isAvailable'     => ($totalEks === 0 || $availEks > 0),
                    'statusModalText' => ($totalEks === 0 || $availEks > 0) ? 'Tersedia di Rak (' . ($totalEks > 0 ? $availEks . '/' . $totalEks : '1/1') . ' Eks.)' : 'Dipinjam (0/' . $totalEks . ' Eks.)',
                    'detailUrl'       => $detailUrl,
                    'hasCover'        => $hasValidCoverFile,
                    'coverImgSrc'     => $coverImgSrc,
                    'paletteBg'       => $palette['bg'],
                    'paletteAccent'   => $palette['accent'],
                    'palettePillBg'   => 'rgba(255,255,255,0.15)',
                    'topLabel'        => $topLabel,
                    'year'            => ($year !== '' && $year !== '-') ? $year : '',
                    'footerRight'     => $edition !== '' ? $edition : 'Koleksi Resmi',
                ];
                $modalJsonAttr = htmlspecialchars(json_encode($modalData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="catalog-list-card opac-modern-card" data-book-meta="<?= $modalJsonAttr ?>">
                    <div class="d-flex flex-column flex-sm-row gap-3 align-items-start">
                        
                        <!-- Mini Cover (Gambar Asli atau Mini Sampul 3D Elegan) -->
                        <a href="<?= $detailUrl ?>" class="catalog-list-cover-link js-open-book-modal" title="<?= esc($title) ?>">
                            <?php if ($hasValidCoverFile): ?>
                                <img src="<?= esc($coverImgSrc) ?>"
                                     alt="<?= esc($title) ?>"
                                     class="catalog-list-thumb"
                                     loading="lazy"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <?php endif; ?>
                            <div class="catalog-list-thumb-cover" style="background: <?= $palette['bg'] ?>; <?= $hasValidCoverFile ? 'display: none;' : '' ?>">
                                <div class="catalog-thumb-spine"></div>
                                <div class="catalog-thumb-tag" style="color: <?= $palette['accent'] ?>;"><?= esc($topLabel) ?></div>
                                <div class="catalog-thumb-title"><?= esc(mb_strimwidth($title, 0, 48, '...')) ?></div>
                                <div class="catalog-thumb-footer">
                                    <span class="catalog-thumb-call"><?= esc(mb_strimwidth($callNumber, 0, 10, '')) ?></span>
                                    <?php if ($year !== '' && $year !== '-' && $year !== '0000'): ?>
                                        <span><?= esc($year) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>

                        <!-- Informasi Buku -->
                        <div class="flex-grow-1 min-w-0">
                            <!-- Baris Tag Atas: No Induk/Control, Status Sirkulasi, Tahun -->
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge-control-no">
                                        <i class="fa-solid fa-hashtag me-1 opacity-50"></i><?= esc($controlNo) ?>
                                    </span>
                                    <?= $statusBadgeHtml ?>
                                    <span class="opac-eks-count ms-1"><?= $eksCountHtml ?></span>
                                </div>
                                <?php if (!empty($year) && $year !== '-' && $year !== '0000'): ?>
                                    <span class="catalog-year-pill">
                                        <i class="fa-regular fa-calendar-days me-1"></i>Tahun <?= esc($year) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Judul Buku -->
                            <h5 class="catalog-list-heading">
                                <a href="<?= $detailUrl ?>" class="text-decoration-none catalog-list-title js-open-book-modal">
                                    <?= esc($title) ?>
                                </a>
                            </h5>

                            <!-- Metadata Buku -->
                            <div class="catalog-list-meta-row d-flex flex-wrap align-items-center text-muted small mb-2">
                                <span class="meta-item">
                                    <i class="fa-solid fa-user-pen text-primary me-1"></i>
                                    <strong>Pengarang:</strong>
                                    <span class="catalog-list-author text-dark fw-medium ms-1"><?= esc($displayAuthor) ?></span>
                                </span>
                                <?php if (!empty($publisher)): ?>
                                    <span class="meta-item">
                                        <i class="fa-solid fa-building text-secondary me-1"></i>
                                        <strong>Penerbit:</strong>
                                        <span class="text-dark ms-1"><?= esc($publisher) ?></span>
                                    </span>
                                <?php endif; ?>
                                <span class="meta-item">
                                    <i class="fa-solid fa-barcode text-secondary me-1"></i>
                                    <strong>No. Panggil:</strong>
                                    <span class="badge bg-light text-dark border ms-1 fw-semibold"><?= esc($callNumber) ?></span>
                                </span>
                                <?php if (!empty($shelfLoc)): ?>
                                    <span class="meta-item">
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i>
                                        <span class="text-secondary"><?= esc($shelfLoc) ?></span>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Tajuk Subjek -->
                            <?php if (!empty($subjects)): ?>
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <?php foreach (array_slice($subjects, 0, 4) as $subItem): ?>
                                        <a href="<?= base_url('opac?search=' . urlencode($subItem) . '&search_by=Subject') ?>"
                                           class="badge subject-badge-item"
                                           title="Cari subjek: <?= esc($subItem) ?>">
                                            <i class="fa-solid fa-tag me-1 small opacity-75"></i><?= esc($subItem) ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tombol Aksi di Kanan -->
                        <div class="text-end d-flex flex-row flex-sm-column gap-2 flex-shrink-0 align-self-start align-self-sm-center ps-sm-2 mt-2 mt-sm-0">
                            <a href="<?= $detailUrl ?>" class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-semibold d-inline-flex align-items-center justify-content-center shadow-sm">
                                <i class="fa-solid fa-circle-info me-1"></i>Detail Buku
                            </a>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-1 js-open-book-modal" title="Pratinjau Singkat">
                                <i class="fa-solid fa-eye me-1"></i>Pratinjau
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- 3. Table View -->
    <div id="tableView" class="view-container" style="display: none;">
        <div class="table-responsive bg-white rounded-4 border border-light shadow-sm p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Judul Koleksi</th>
                        <th>Pengarang</th>
                        <th>Penerbit & Tahun</th>
                        <th>No. Panggil</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentPage = isset($pager) ? $pager->getCurrentPage('browse') : 1;
                    $perPageVal  = $perPage ?? 12;
                    $startNo     = ($currentPage - 1) * $perPageVal + 1;
                    foreach ($catalogs as $index => $catalog):
                    ?>
                        <tr>
                            <td class="text-muted small"><?= $startNo + $index ?></td>
                            <td>
                                <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" class="text-decoration-none fw-bold text-dark hover-primary catalog-table-title">
                                    <?= esc($catalog->Title ?? 'Tanpa Judul') ?>
                                </a>
                                <div class="small text-muted"><?= esc($catalog->ControlNumber ?? '') ?></div>
                            </td>
                            <td class="small catalog-table-author"><?= esc($catalog->Author ?? 'N/A') ?></td>
                            <td class="small">
                                <?= esc($catalog->Publisher ?? '-') ?>
                                <?php if (!empty($catalog->PublishYear)): ?>
                                    (<?= esc($catalog->PublishYear) ?>)
                                <?php endif; ?>
                            </td>
                            <td class="small"><span class="badge bg-light text-dark border"><?= esc($catalog->CallNumber ?? '-') ?></span></td>
                            <td class="text-center">
                                <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" class="btn btn-sm btn-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; line-height: 32px;" title="Lihat Detail">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="row mt-5">
        <div class="col-12 d-flex flex-column align-items-center">
            <?php if (isset($pager)): ?>
                <div class="custom-pagination">
                    <?= $pager->links('browse', 'opac_pagination') ?>
                </div>
                <div class="text-muted mt-2">
                    <small>
                        <i class="fa-solid fa-layer-group text-primary me-1"></i>
                        Halaman <strong><?= $pager->getCurrentPage('browse') ?></strong> dari <strong><?= $pager->getPageCount('browse') ?></strong>
                        &nbsp;&bull;&nbsp;
                        Total <strong><?= number_format($pager->getTotal('browse')) ?></strong> koleksi
                    </small>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>

    <!-- Empty State (Judul / Pengarang) -->
    <div class="text-center py-5 my-4 bg-white rounded-4 border border-light shadow-sm">
        <div class="mb-3 text-secondary opacity-50">
            <i class="fa-solid fa-book-open fa-4x"></i>
        </div>
        <h4 class="fw-bold text-dark mb-2">Koleksi Tidak Ditemukan</h4>
        <p class="text-muted mb-4">
            <?php if (!empty($search_query)): ?>
                Tidak ada <?= $browse_type === 'author' ? 'pengarang' : 'judul' ?> yang cocok dengan kata kunci <span class="browse-highlight-blur"><?= esc($search_query) ?></span>.
            <?php else: ?>
                Tidak ada <?= $browse_type === 'author' ? 'pengarang' : 'judul' ?> yang diawali dengan huruf "<strong><?= esc($letter ?? 'A') ?></strong>".
            <?php endif; ?>
        </p>
        <div class="d-flex justify-content-center gap-2">
            <?php if (!empty($search_query)): ?>
                <button type="button" onclick="resetBrowseSearch()" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset Pencarian
                </button>
            <?php else: ?>
                <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=A') ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="fa-solid fa-rotate-left me-1"></i> Reset ke Huruf A
                </a>
            <?php endif; ?>
            <a href="<?= base_url('opac/browse?type=subject') ?>" class="btn btn-outline-primary rounded-pill px-4">
                <i class="fa-solid fa-tags me-1"></i> Coba Browse Subjek
            </a>
        </div>
    </div>

<?php endif; ?>
