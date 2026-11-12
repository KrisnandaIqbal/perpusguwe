<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/opac_style.css') ?>">
<style>
    /* Global Styles */
    body {
        background-color: #f8f9fa;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        padding-top: 140px;
        padding-bottom: 80px;
        border-bottom-left-radius: 40px;
        border-bottom-right-radius: 40px;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .hero-section p.lead {
        color: #d1e0ff;
    }

    /* Carousel sebagai background hero */
    .hero-bg-carousel {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
    }

    .hero-bg-carousel .carousel-inner,
    .hero-bg-carousel .carousel-item {
        height: 100%;
    }

    .hero-bg-carousel .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
    }

    .hero-bg-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(20, 45, 100, 0.65);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    /* Glass Search & Recommendation Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(10px);
    }

    .custom-input {
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
    }

    .custom-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(42, 82, 152, 0.25);
        border-color: #2a5298;
    }

    .btn-primary-custom {
        background: #2a5298;
        border: none;
        border-radius: 10px;
        padding: 12px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        background: #1e3c72;
        transform: translateY(-2px);
    }

    /* Sidebar Styles */
    .sidebar-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 24px;
        background: #ffffff;
    }

    .sidebar-card .card-header {
        /* Gradasi biru senada dengan hero section agar tulisan putih terbaca */
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        border-bottom: none;
        padding: 15px 20px;
        border-radius: 16px 16px 0 0;
    }

    .sidebar-card .card-title {
        color: #ffffff;
        /* Mengubah tulisan menjadi putih */
        font-size: 1.1rem;
        font-weight: 600;
    }

    .sidebar-card .card-title i {
        color: #ffffff;
        /* Mengubah ikon menjadi putih */
    }

    .stat-list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px dashed #f0f0f0;
    }

    .stat-list-item:last-child {
        border-bottom: none;
    }

    .stat-list-item a {
        color: #555;
        transition: color 0.3s;
        font-size: 0.9rem;
    }

    .stat-list-item a:hover {
        color: #2a5298;
        font-weight: 500;
    }

    .stat-badge {
        background: #edf2f7;
        color: #4a5568;
        border-radius: 20px;
        padding: 4px 10px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Catalog Cards */
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
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="hero-section">
    <?php if (!empty($opac_banners)): ?>
        <div id="opacBannerCarousel" class="carousel slide hero-bg-carousel" data-bs-ride="carousel" data-bs-interval="4000">
            <div class="carousel-inner">
                <?php foreach ($opac_banners as $i => $b): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= base_url('uploads/banner/' . esc($b->file_cover)) ?>" alt="Banner <?= $i + 1 ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($opac_banners) > 1): ?>
                <button class="carousel-control-prev" type="button" data-bs-target="#opacBannerCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#opacBannerCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            <?php endif; ?>
        </div>
        <div class="hero-bg-overlay"></div>
    <?php endif; ?>

    <div class="container hero-content">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold mb-3">
                        <i class="fas fa-book-reader me-3"></i>
                        Katalog Perpustakaan
                    </h1>
                    <p class="lead">Temukan referensi, buku, dan koleksi literatur dengan mudah dan cepat.</p>
                </div>

                <div class="glass-card">
                    
                    <form method="GET" action="<?= base_url('opac') ?>">
                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <select class="form-select custom-input" name="search_by">
                                    <option value="" <?= ($search_by ?? '') == '' ? 'selected' : '' ?>>Semua Kriteria</option>
                                    <option value="Title" <?= ($search_by ?? '') == 'Title' ? 'selected' : '' ?>>Judul</option>
                                    <option value="Author" <?= ($search_by ?? '') == 'Author' ? 'selected' : '' ?>>Pengarang</option>
                                    <option value="Subject" <?= ($search_by ?? '') == 'Subject' ? 'selected' : '' ?>>Subjek</option>
                                    <option value="DeweyNo" <?= ($search_by ?? '') == 'DeweyNo' ? 'selected' : '' ?>>Klasifikasi DDC</option>
                                    <option value="Publisher" <?= ($search_by ?? '') == 'Publisher' ? 'selected' : '' ?>>Penerbit</option>
                                    <option value="ISBN" <?= ($search_by ?? '') == 'ISBN' ? 'selected' : '' ?>>ISBN</option>
                                </select>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control custom-input" name="search"
                                    placeholder="Masukkan kata kunci pencarian..." value="<?= esc($search ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary-custom w-100 text-white">
                                    <i class="fas fa-search me-1"></i>Cari
                                </button>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" class="form-control custom-input form-control-sm" name="Author" placeholder="Filter Pengarang..." value="<?= esc(request()->getVar('Author')) ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control custom-input form-control-sm" name="Publisher" placeholder="Filter Penerbit..." value="<?= esc(request()->getVar('Publisher')) ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control custom-input form-control-sm" name="Subject" placeholder="Filter Subjek..." value="<?= esc(request()->getVar('Subject')) ?>">
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control custom-input form-control-sm" name="PublishYear" placeholder="Filter Tahun..." value="<?= esc(request()->getVar('PublishYear')) ?>">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content-section mb-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 sidebar-section">
                <div class="sidebar-container position-sticky" style="top: 20px;">

                    <div class="card sidebar-card">
                        <div class="card-header d-flex align-items-center">
                            <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2"></i>Statistik Koleksi</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="me-3">
                                    <i class="fas fa-books fa-2x text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0 fw-bold text-dark"><?= isset($total_records) ? number_format($total_records) : '0' ?></h4>
                                    <small class="text-muted">Total Katalog</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (isset($publisher_counts) && !empty($publisher_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-building me-2"></i>Penerbit Teratas</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($publisher_counts);
                                    $top_publishers = array_slice($publisher_counts, 0, 5, true);
                                    foreach ($top_publishers as $publisher => $count):
                                        if (!empty(trim($publisher))):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('Publisher', $publisher)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($publisher) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($author_counts) && !empty($author_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-user-edit me-2"></i>Pengarang Teratas</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($author_counts);
                                    $top_authors = array_slice($author_counts, 0, 5, true);
                                    foreach ($top_authors as $author => $count):
                                        if (!empty(trim($author))):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('Author', $author)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($author) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($subject_counts) && !empty($subject_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-tags me-2"></i>Subjek Populer</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($subject_counts);
                                    $top_subjects = array_slice($subject_counts, 0, 5, true);
                                    foreach ($top_subjects as $subject => $count):
                                        if (!empty(trim($subject))):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('Subject', $subject)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($subject) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($year_counts) && !empty($year_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-calendar-alt me-2"></i>Tahun Terbit</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($year_counts);
                                    $top_years = array_slice($year_counts, 0, 5, true);
                                    foreach ($top_years as $year => $count):
                                        if (!empty(trim($year)) && $year != '1970'):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('PublishYear', $year)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($year) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($publish_location_counts) && !empty($publish_location_counts)): ?>
                        <div class="card sidebar-card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i>Kota Terbit</h5>
                            </div>
                            <div class="card-body pt-2">
                                <div class="stat-list">
                                    <?php
                                    arsort($publish_location_counts);
                                    $top_locations = array_slice($publish_location_counts, 0, 8, true);
                                    foreach ($top_locations as $location => $count):
                                        if (!empty(trim($location))):
                                    ?>
                                            <div class="stat-list-item">
                                                <a href="<?= esc(buildFilterUrl('PublishLocation', $location)) ?>" class="text-decoration-none text-truncate pe-2">
                                                    <?= esc($location) ?>
                                                </a>
                                                <span class="stat-badge"><?= $count ?></span>
                                            </div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-9 catalog-section">
                <?php
                $activeSubject  = (($search_by ?? '') === 'Subject' && !empty($search))
                    ? $search
                    : (string) (request()->getVar('Subject') ?? '');
                $activeDewey    = (($search_by ?? '') === 'DeweyNo' && !empty($search))
                    ? $search
                    : (string) (request()->getVar('DeweyNo') ?? '');
                $activeAuthor   = (string) (request()->getVar('Author') ?? '');
                $activePub      = (string) (request()->getVar('Publisher') ?? '');
                $activeYear     = (string) (request()->getVar('PublishYear') ?? '');
                $activeLocation = (string) (request()->getVar('PublishLocation') ?? '');
                $hasActiveQuery = !empty($search) || $activeSubject !== '' || $activeDewey !== '' || $activeAuthor !== '' || $activePub !== '' || $activeYear !== '' || $activeLocation !== '';
                ?>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 pb-2 border-bottom">
                    <h4 class="mb-0 fw-bold text-dark">
                        <?php if ($activeSubject !== ''): ?>
                            <i class="fas fa-tags text-primary me-2"></i>Subjek: <span class="text-primary"><?= esc($activeSubject) ?></span>
                        <?php elseif ($activeDewey !== ''): ?>
                            <i class="fas fa-shapes text-primary me-2"></i>Klasifikasi DDC: <span class="text-primary"><?= esc($activeDewey) ?></span>
                        <?php elseif (!empty($search)): ?>
                            <i class="fas fa-search text-primary me-2"></i>Hasil Pencarian: <span class="text-primary"><?= esc($search) ?></span>
                        <?php elseif ($activeAuthor !== ''): ?>
                            <i class="fas fa-user-edit text-primary me-2"></i>Pengarang: <span class="text-primary"><?= esc($activeAuthor) ?></span>
                        <?php elseif ($activePub !== ''): ?>
                            <i class="fas fa-building text-primary me-2"></i>Penerbit: <span class="text-primary"><?= esc($activePub) ?></span>
                        <?php elseif ($activeYear !== ''): ?>
                            <i class="fas fa-calendar-alt text-primary me-2"></i>Tahun Terbit: <span class="text-primary"><?= esc($activeYear) ?></span>
                        <?php elseif ($activeLocation !== ''): ?>
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>Kota Terbit: <span class="text-primary"><?= esc($activeLocation) ?></span>
                        <?php else: ?>
                            <i class="fas fa-stream text-primary me-2"></i>Koleksi Terbaru
                        <?php endif; ?>
                    </h4>
                    <?php if ($hasActiveQuery): ?>
                        <a href="<?= base_url('opac') ?>" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="fas fa-times me-1"></i>Tampilkan Semua Koleksi
                        </a>
                    <?php endif; ?>
                </div>

                <div class="row g-4">
                    <?php
                    $displayCatalogs = $catalogs ?? [];
                    ?>

                    <?php if (!empty($displayCatalogs)): ?>
                        <?php foreach ($displayCatalogs as $catalog): ?>
                            <?= renderModernBookCard($catalog, 'col-xl-4 col-md-6') ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                                <div class="mb-4">
                                    <i class="fas fa-search-minus fa-4x text-muted opacity-50"></i>
                                </div>
                                <h4 class="text-dark fw-bold">
                                    <?php if ($hasActiveQuery): ?>
                                        Koleksi tidak ditemukan
                                    <?php else: ?>
                                        Mulai temukan koleksi
                                    <?php endif; ?>
                                </h4>
                                <p class="text-muted mb-4 px-3">
                                    <?php if ($hasActiveQuery): ?>
                                        Coba gunakan kata kunci yang lebih umum atau periksa ejaan Anda.
                                    <?php endif; ?>
                                </p>
                                <a href="<?= base_url('opac') ?>" class="btn btn-primary-custom text-white px-4">
                                    <i class="fas fa-sync-alt me-2"></i>Muat Ulang Katalog
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 d-flex flex-column align-items-center">
                <?php if (!empty($pager)): ?>
                    <div class="custom-pagination">
                        <?php
                        // ✅ Tangani dua kemungkinan: objek (non-cache) atau string HTML (cache hit)
                        if (is_string($pager)) {
                            echo $pager;
                        } else {
                            echo $pager->links('default', 'opac_pagination');
                        }
                        ?>
                    </div>
                    <div class="text-muted mt-2">
                        <small>
                            <i class="fas fa-list text-primary me-1"></i>
                            Total <strong><?= number_format($total_records ?? 0) ?></strong> koleksi ditemukan
                        </small>
                    </div>
                <?php endif; ?>

                <?php if (isset($execution_time)): ?>
                    <div class="text-muted mt-3">
                        <small>
                            <i class="fas fa-bolt text-warning me-1"></i>
                            Waktu muat: <strong><?= number_format($execution_time, 4) ?></strong> detik
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput && !searchInput.value) {
            searchInput.focus();
        }
    });
</script>
<?= $this->endSection() ?>