<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    .browse-letter {
        display: inline-block;
        width: 38px;
        height: 38px;
        line-height: 38px;
        text-align: center;
        margin: 3px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        color: #1b3878;
        background: #f1f5f9;
        border: 1px solid #dbeafe;
        transition: all 0.2s ease;
    }
    .browse-letter:hover {
        background: #1b3878;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(27,56,120,0.2);
    }
    .browse-letter.active {
        background: #1b3878;
        color: white;
        box-shadow: 0 4px 10px rgba(27,56,120,0.3);
    }
    .catalog-card {
        border: none;
        border-radius: 16px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        height: 100%;
        overflow: hidden;
    }
    .catalog-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    .book-cover-wrapper {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    .book-cover {
        width: 100%;
        height: 180px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    .catalog-card:hover .book-cover {
        transform: scale(1.05);
    }
    .book-overlay {
        background: rgba(42, 82, 152, 0.8);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .catalog-card:hover .book-overlay {
        opacity: 1;
    }
    .book-meta {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 6px;
    }
    .book-meta i {
        width: 16px;
        color: #a0aec0;
    }
    .badge-floating {
        position: absolute;
        top: 10px;
        left: -5px;
        z-index: 10;
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
    }
    .subject-badge-item {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
        font-weight: 500;
        font-size: 0.78rem;
        padding: 4px 8px;
        border-radius: 6px;
        transition: all 0.2s ease-in-out;
        display: inline-flex;
        align-items: center;
        text-decoration: none !important;
    }
    .subject-badge-item:hover {
        background-color: #334155 !important;
        color: #ffffff !important;
        border-color: #334155 !important;
        transform: translateY(-1px);
        box-shadow: 0 3px 6px rgba(30, 41, 59, 0.25);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container py-5" style="padding-top: 100px !important;">

    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-5 fw-bold text-primary mb-3">
                <i class="fas fa-list me-3"></i>Browse Katalog
            </h1>
            <p class="lead text-muted">Jelajahi koleksi perpustakaan berdasarkan huruf awal</p>
        </div>
    </div>

    <!-- Statistik & Pilih Tipe -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Pilih Kategori Browse
                    </h5>
                </div>
                <div class="card-body">

                    <!-- Statistik -->
                    <div class="row text-center mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="bg-primary text-white p-3 rounded">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h5 class="mb-0"><?= number_format($total_authors) ?></h5>
                                <small>Total Pengarang</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="bg-success text-white p-3 rounded">
                                <i class="fas fa-book fa-2x mb-2"></i>
                                <h5 class="mb-0"><?= number_format($total_titles) ?></h5>
                                <small>Total Judul</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="bg-info text-white p-3 rounded">
                                <i class="fas fa-tags fa-2x mb-2"></i>
                                <h5 class="mb-0"><?= number_format($total_subjects) ?></h5>
                                <small>Total Subjek</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="bg-warning text-white p-3 rounded">
                                <i class="fas fa-globe fa-2x mb-2"></i>
                                <h5 class="mb-0"><?= number_format($total_languages) ?></h5>
                                <small>Bahasa</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Tipe Browse -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('opac/browse?type=author&letter=' . ($letter ?? 'A')) ?>"
                               class="btn <?= ($browse_type ?? '') == 'author' ? 'btn-primary' : 'btn-outline-primary' ?> btn-lg w-100">
                                <i class="fas fa-user fa-2x mb-2 d-block"></i>
                                <strong>Pengarang</strong><br>
                                <small>Browse berdasarkan nama pengarang</small>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('opac/browse?type=title&letter=' . ($letter ?? 'A')) ?>"
                               class="btn <?= ($browse_type ?? '') == 'title' ? 'btn-success' : 'btn-outline-success' ?> btn-lg w-100">
                                <i class="fas fa-book fa-2x mb-2 d-block"></i>
                                <strong>Judul</strong><br>
                                <small>Browse berdasarkan judul buku</small>
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="<?= base_url('opac/browse?type=subject&letter=' . ($letter ?? 'A')) ?>"
                               class="btn <?= ($browse_type ?? '') == 'subject' ? 'btn-info' : 'btn-outline-info' ?> btn-lg w-100">
                                <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                                <strong>Subjek</strong><br>
                                <small>Browse berdasarkan subjek</small>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <?php
    $typeNames = [
        'author'  => 'Pengarang',
        'title'   => 'Judul',
        'subject' => 'Subjek',
    ];
    if (isset($browse_type)):
    ?>

        <!-- Navigasi Alfabet -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-sort-alpha-down me-2"></i>
                            Pilih Huruf Awal
                            <span class="text-primary">(<?= $typeNames[$browse_type] ?? $browse_type ?>)</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alphabet-nav text-center">
                            <?php foreach ($alphabet ?? range('A', 'Z') as $char): ?>
                                <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=' . $char) ?>"
                                   class="browse-letter <?= ($letter ?? 'A') == $char ? 'active' : '' ?>">
                                    <?= $char ?>
                                </a>
                            <?php endforeach; ?>

                            <div class="mt-2">
                                <?php for ($i = 0; $i <= 9; $i++): ?>
                                    <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=' . $i) ?>"
                                       class="browse-letter <?= ($letter ?? '') == $i ? 'active' : '' ?>">
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hasil Browse -->
        <div class="row">
            <div class="col-12">
                <?php if (!empty($catalogs)): ?>

                    <!-- Header Hasil -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="text-primary mb-0">
                            <i class="fas fa-list-ul me-2"></i>
                            <?= ucfirst($typeNames[$browse_type] ?? $browse_type) ?> dimulai dengan
                            "<strong><?= esc($letter) ?></strong>"
                            <span class="badge bg-primary ms-2">
                                <?= number_format($pager->getTotal('browse')) ?> ditemukan
                            </span>
                        </h3>

                        <div class="btn-group">
                            <button class="btn btn-outline-secondary dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-eye me-1"></i>Tampilan
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('grid'); return false;">
                                        <i class="fas fa-th me-2"></i>Grid
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('list'); return false;">
                                        <i class="fas fa-list me-2"></i>List
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="switchView('table'); return false;">
                                        <i class="fas fa-table me-2"></i>Tabel
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Grid View (Default) -->
                    <div id="gridView" class="view-container">
                        <div class="row g-4">
                            <?php foreach ($catalogs as $catalog): ?>
                                <?= renderModernBookCard($catalog, 'col-lg-3 col-md-6') ?>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- List View -->
                    <div id="listView" class="view-container" style="display: none;">
                        <?php foreach ($catalogs as $catalog): ?>
                            <div class="card mb-3 border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="mb-1 text-primary">
                                                <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" class="text-decoration-none text-primary">
                                                    <?= esc($catalog->Title ?? 'Tanpa Judul') ?>
                                                </a>
                                            </h5>
                                            <p class="mb-1 text-muted small">
                                                <strong>Pengarang:</strong> <?= esc($catalog->Author ?? 'N/A') ?> &nbsp;|&nbsp;
                                                <strong>Penerbit:</strong> <?= esc($catalog->Publisher ?? 'N/A') ?> &nbsp;|&nbsp;
                                                <strong>Tahun:</strong> <?= esc($catalog->PublishYear ?? 'N/A') ?>
                                            </p>
                                            <?php if (!empty($catalog->Subject)): ?>
                                                <div class="mt-1 d-flex flex-wrap gap-1">
                                                    <?php foreach (splitSubjects($catalog->Subject) as $subItem): ?>
                                                        <a href="<?= base_url('opac?search=' . urlencode($subItem) . '&search_by=Subject') ?>"
                                                           class="badge subject-badge-item"
                                                           title="Cari subjek: <?= esc($subItem) ?>">
                                                            <i class="fas fa-tag me-1"></i><?= esc($subItem) ?>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <span class="badge bg-secondary mb-2 d-block">
                                                <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                                            </span>
                                            <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>"
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Detail
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Table View -->
                    <div id="tableView" class="view-container" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Control Number</th>
                                        <th>Judul</th>
                                        <th>Pengarang</th>
                                        <th>Penerbit</th>
                                        <th>Tahun</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $currentPage = $pager->getCurrentPage('browse');
                                    $startNo     = ($currentPage - 1) * $perPage + 1;
                                    foreach ($catalogs as $index => $catalog):
                                    ?>
                                        <tr>
                                            <td><?= $startNo + $index ?></td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?= esc($catalog->ControlNumber ?? 'N/A') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong>
                                                    <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>" class="text-decoration-none text-dark">
                                                        <?= esc(substr($catalog->Title ?? 'Tanpa Judul', 0, 50)) ?>
                                                        <?= strlen($catalog->Title ?? '') > 50 ? '...' : '' ?>
                                                    </a>
                                                </strong>
                                            </td>
                                            <td><?= esc($catalog->Author ?? 'N/A') ?></td>
                                            <td><?= esc($catalog->Publisher ?? 'N/A') ?></td>
                                            <td><?= esc($catalog->PublishYear ?? 'N/A') ?></td>
                                            <td>
                                                <a href="<?= base_url('opac/detail/' . $catalog->ID) ?>"
                                                   class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Export -->
                    <div class="text-center mt-4">
                        <div class="btn-group">
                            <button class="btn btn-success" onclick="exportBrowseResults('excel')">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </button>
                            <button class="btn btn-info text-white" onclick="exportBrowseResults('csv')">
                                <i class="fas fa-file-csv me-2"></i>Export CSV
                            </button>
                        </div>
                    </div>

                    <!-- ✅ Pagination — gunakan $pager->links('browse') -->
                    <div class="row mt-5">
                        <div class="col-12 d-flex flex-column align-items-center">
                            <?php if (isset($pager)): ?>
                                <div class="custom-pagination">
                                    <?= $pager->links('browse','opac_pagination') ?>
                                </div>
                                <div class="text-muted mt-2">
                                    <small>
                                        <i class="fas fa-list text-primary me-1"></i>
                                        Halaman <strong><?= $pager->getCurrentPage('browse') ?></strong>
                                        dari <strong><?= $pager->getPageCount('browse') ?></strong>
                                        &nbsp;|&nbsp;
                                        Total <strong><?= number_format($pager->getTotal('browse')) ?></strong> hasil
                                    </small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>

                    <!-- Tidak Ada Hasil -->
                    <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">
                            Tidak ada <?= $typeNames[$browse_type] ?? $browse_type ?>
                            yang dimulai dengan "<?= esc($letter) ?>"
                        </h4>
                        <p class="text-muted mb-4">Coba pilih huruf lain atau ubah kategori browse</p>
                        <div class="btn-group">
                            <a href="<?= base_url('opac/browse?type=' . $browse_type . '&letter=A') ?>"
                               class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i>Reset ke A
                            </a>
                            <a href="<?= base_url('opac/browse') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Pilih Kategori Lain
                            </a>
                        </div>
                    </div>

                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>
</div>

<?= $this->section('script') ?>
<script>
function switchView(viewType) {
    document.querySelectorAll('.view-container').forEach(el => el.style.display = 'none');
    const target = document.getElementById(viewType + 'View');
    if (target) target.style.display = 'block';
    showToast('Tampilan diubah ke ' + viewType, 'success');
}

function exportBrowseResults(format) {
    const params = new URLSearchParams(window.location.search);
    params.set('format', format);
    params.set('export', 'browse_results');
    window.open('<?= base_url('opac/export') ?>?' + params.toString(), '_blank');
    showToast('Export ' + format.toUpperCase() + ' dimulai...', 'info');
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    toast.style.cssText = 'top: 80px; right: 20px; z-index: 9999; max-width: 350px;';
    toast.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(toast);
    setTimeout(() => { if (toast.parentNode) toast.parentNode.removeChild(toast); }, 3000);
}

function quickJumpToLetter() {
    const letter = prompt('Masukkan huruf yang ingin dicari (A-Z, 0-9):');
    if (letter && letter.match(/^[a-zA-Z0-9]$/)) {
        const params   = new URLSearchParams(window.location.search);
        const browseType = params.get('type') || 'author';
        window.location.href = '<?= base_url('opac/browse') ?>?type=' + browseType + '&letter=' + letter.toUpperCase();
    }
}

document.addEventListener('keydown', function(e) {
    if (e.altKey && e.key.match(/^[a-zA-Z0-9]$/)) {
        const letter     = e.key.toUpperCase();
        const params     = new URLSearchParams(window.location.search);
        const browseType = params.get('type') || 'author';
        window.location.href = '<?= base_url('opac/browse') ?>?type=' + browseType + '&letter=' + letter;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const currentLetter = '<?= $letter ?? '' ?>';
    if (currentLetter) {
        document.querySelectorAll('.opac-book-heading a').forEach(function(el) {
            const text = el.textContent.trim();
            if (text.toLowerCase().startsWith(currentLetter.toLowerCase())) {
                const first = text.charAt(0);
                el.innerHTML = '<span class="bg-warning px-1 rounded">' + first + '</span>' + text.slice(1);
            }
        });
    }

    // Tombol Quick Jump
    const alphabetNav = document.querySelector('.alphabet-nav');
    if (alphabetNav) {
        const btn    = document.createElement('button');
        btn.className = 'btn btn-outline-primary btn-sm ms-3 mt-2';
        btn.innerHTML = '<i class="fas fa-search me-1"></i>Jump';
        btn.onclick   = quickJumpToLetter;
        alphabetNav.appendChild(btn);
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>