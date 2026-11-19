<?php
/**
 * Partial View: Hasil Browse Katalog (Judul & Pengarang)
 * Tampilan katalog diselaraskan persis dengan layout & kartu OPAC (renderModernBookCard, grid/list view, pagination).
 * Digunakan untuk render awal di browse.php dan update live via AJAX tanpa reload.
 */
?>

<?php if (!empty($catalogs)): ?>
    <?php
    $totalCount = isset($pager) ? (int) $pager->getTotal('browse') : count($catalogs);
    ?>

    <!-- ─── Header Katalog (Persis Layout OPAC) ─── -->
    <div class="opac-catalog-header">
        <div>
            <?php if (!empty($search_query)): ?>
                <h2 class="opac-section-heading">
                    Hasil pencarian <?= $browse_type === 'author' ? 'pengarang' : 'judul' ?>: <span style="color: var(--opac-primary, #0f3fb4);">"<?= esc($search_query) ?>"</span>
                </h2>
                <p class="opac-section-subheading">
                    Ditemukan <?= number_format($totalCount) ?> koleksi &bull;
                    <a href="javascript:void(0);" onclick="resetBrowseSearch()" class="text-decoration-none fw-semibold" style="color: var(--opac-primary, #0f3fb4);">
                        <i class="fa-solid fa-rotate-left me-1"></i>Kembali ke Huruf <?= esc($letter ?? 'A') ?>
                    </a>
                </p>
            <?php else: ?>
                <h2 class="opac-section-heading">
                    Koleksi <?= $browse_type === 'author' ? 'pengarang' : 'judul' ?> berawalan "<?= esc($letter ?? 'A') ?>"
                </h2>
                <p class="opac-section-subheading">
                    <?= number_format($totalCount) ?> buku untuk menemani rasa ingin tahu Anda
                </p>
            <?php endif; ?>
        </div>

        <div class="opac-header-actions">
            <!-- Dropdown Ekspor -->
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 0.82rem; font-weight: 500;">
                    <i class="fa-solid fa-file-export me-1"></i>Ekspor
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                    <li><a class="dropdown-item py-2" href="#" onclick="exportBrowseResults('excel'); return false;"><i class="fa-solid fa-file-excel text-success me-2"></i>Ekspor ke Excel</a></li>
                    <li><a class="dropdown-item py-2" href="#" onclick="exportBrowseResults('csv'); return false;"><i class="fa-solid fa-file-csv text-info me-2"></i>Ekspor ke CSV</a></li>
                </ul>
            </div>

            <!-- View Switcher (Persis OPAC: Grid, List, dan Tabel) -->
            <div class="d-flex align-items-center gap-1 ms-1">
                <button type="button" class="opac-view-switch-btn active" id="btnViewGrid" onclick="switchView('grid')" title="Tampilan Kisi" aria-label="Tampilan Kisi">
                    <i class="fas fa-th-large"></i>
                </button>
                <button type="button" class="opac-view-switch-btn" id="btnViewList" onclick="switchView('list')" title="Tampilan Daftar" aria-label="Tampilan Daftar">
                    <i class="fas fa-bars"></i>
                </button>
                <button type="button" class="opac-view-switch-btn" id="btnViewTable" onclick="switchView('table')" title="Tampilan Tabel" aria-label="Tampilan Tabel">
                    <i class="fas fa-table"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- ─── 1. Grid & List Katalog (Persis OPAC: renderModernBookCard & mode list fleksibel) ─── -->
    <div class="row g-2 g-md-3 opac-catalog-grid" id="opacCatalogGrid">
        <?php foreach ($catalogs as $catalog): ?>
            <?= renderModernBookCard($catalog, 'opac-col-book col-xl-3 col-lg-4 col-md-6 col-6') ?>
        <?php endforeach; ?>
    </div>

    <!-- ─── 2. Tampilan Tabel Ringkas (Opsi Tambahan untuk Browse) ─── -->
    <div id="tableView" style="display: none;">
        <div class="table-responsive bg-white rounded-3 border p-3 shadow-sm" style="border-color: #e2e8f0 !important;">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Judul Koleksi</th>
                        <th>Pengarang</th>
                        <th>Penerbit &amp; Tahun</th>
                        <th>No. Panggil</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentPage = isset($pager) ? (int) $pager->getCurrentPage('browse') : 1;
                    $perPageVal  = (int) ($perPage ?? 12);
                    $startNo     = ($currentPage - 1) * $perPageVal + 1;
                    foreach ($catalogs as $index => $catalog):
                        $isArr     = is_array($catalog);
                        $cId       = $isArr ? ($catalog['ID'] ?? 0) : ($catalog->ID ?? 0);
                        $cTitle    = $isArr ? ($catalog['Title'] ?? 'Tanpa Judul') : ($catalog->Title ?? 'Tanpa Judul');
                        $cCtrl     = $isArr ? ($catalog['ControlNumber'] ?? '') : ($catalog->ControlNumber ?? '');
                        $cAuthor   = $isArr ? ($catalog['Author'] ?? 'N/A') : ($catalog->Author ?? 'N/A');
                        $cPub      = $isArr ? ($catalog['Publisher'] ?? '-') : ($catalog->Publisher ?? '-');
                        $cYear     = $isArr ? ($catalog['PublishYear'] ?? '') : ($catalog->PublishYear ?? '');
                        $cCall     = $isArr ? ($catalog['CallNumber'] ?? '-') : ($catalog->CallNumber ?? '-');
                    ?>
                        <tr>
                            <td class="text-muted small"><?= $startNo + $index ?></td>
                            <td>
                                <a href="<?= base_url('opac/detail/' . $cId) ?>" class="text-decoration-none fw-bold text-dark hover-primary catalog-table-title">
                                    <?= esc($cTitle) ?>
                                </a>
                                <?php if (!empty($cCtrl)): ?>
                                    <div class="small text-muted"><?= esc($cCtrl) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="small catalog-table-author"><?= esc($cAuthor) ?></td>
                            <td class="small">
                                <?= esc($cPub) ?>
                                <?php if (!empty($cYear) && $cYear !== '-' && $cYear !== '0000'): ?>
                                    (<?= esc($cYear) ?>)
                                <?php endif; ?>
                            </td>
                            <td class="small"><span class="badge bg-light text-dark border"><?= esc($cCall) ?></span></td>
                            <td class="text-center">
                                <a href="<?= base_url('opac/detail/' . $cId) ?>" class="btn btn-sm btn-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; line-height: 32px;" title="Lihat Detail">
                                    <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ─── Pagination Bawah (Persis OPAC) ─── -->
    <?php
    $currP     = isset($pager) ? (int) $pager->getCurrentPage('browse') : 1;
    $pSize     = (int) ($perPage ?? 12);
    $totalRec  = isset($pager) ? (int) $pager->getTotal('browse') : count($catalogs);
    $startItem = max(1, ($currP - 1) * $pSize + 1);
    $endItem   = min($currP * $pSize, $totalRec);
    ?>
    <div class="opac-pagination-container">
        <p class="opac-page-info">
            Menampilkan <?= $startItem ?> sampai <?= $endItem ?> dari <?= number_format($totalRec) ?> buku
        </p>

        <div>
            <?php if (!empty($pager)): ?>
                <?= $pager->links('browse', 'opac_pagination') ?>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>

    <!-- ─── Empty State (Persis OPAC) ─── -->
    <div class="col-12 my-3">
        <div class="text-center py-5 bg-white rounded-3 shadow-sm border" style="border-color: #e2e8f0 !important;">
            <div class="mb-3">
                <i class="fas fa-book-open fa-3x text-muted opacity-50"></i>
            </div>
            <h4 class="text-dark fw-bold mb-2">
                Koleksi tidak ditemukan
            </h4>
            <p class="text-muted mb-4 px-3" style="font-size: 0.9rem;">
                <?php if (!empty($search_query)): ?>
                    Tidak ada <?= $browse_type === 'author' ? 'pengarang' : 'judul' ?> yang cocok dengan kata kunci <span class="browse-highlight-blur"><?= esc($search_query) ?></span>.
                <?php else: ?>
                    Tidak ada <?= $browse_type === 'author' ? 'pengarang' : 'judul' ?> yang diawali dengan huruf "<strong><?= esc($letter ?? 'A') ?></strong>".
                <?php endif; ?>
            </p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <?php if (!empty($search_query)): ?>
                    <button type="button" onclick="resetBrowseSearch()" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset Pencarian
                    </button>
                <?php else: ?>
                    <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=A') ?>" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset ke Huruf A
                    </a>
                <?php endif; ?>
                <a href="<?= base_url('opac/browse?type=subject') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fa-solid fa-tags me-1"></i> Coba Browse Subjek
                </a>
            </div>
        </div>
    </div>

<?php endif; ?>
