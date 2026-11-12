<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    :root {
        --subjek-primary: #1b3878;
        --subjek-primary-dark: #122550;
        --subjek-accent: #3b82f6;
        --subjek-bg-soft: #f8fafc;
        --subjek-border: #e2e8f0;
    }

    .subjek-hero {
        background: linear-gradient(135deg, #1b3878 0%, #2563eb 100%);
        color: #ffffff;
        border-radius: 24px;
        padding: 40px 32px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(27, 56, 120, 0.15);
    }
    .subjek-hero::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Live Search Input */
    .subjek-search-box {
        position: relative;
        max-width: 680px;
        margin: 0 auto;
    }
    .subjek-search-input {
        height: 56px;
        border-radius: 50px;
        padding-left: 54px;
        padding-right: 48px;
        font-size: 1rem;
        border: 2px solid #ffffff;
        background: #ffffff;
        color: #1e293b;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        transition: all 0.25s ease;
    }
    .subjek-search-input:focus {
        outline: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        border-color: #93c5fd;
    }
    .subjek-search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 1.15rem;
    }
    .subjek-search-clear {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        font-size: 1.1rem;
        padding: 4px 8px;
        display: none;
    }
    .subjek-search-clear:hover {
        color: #ef4444;
    }

    /* Subjek Populer Pills */
    .populer-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        padding: 8px 16px;
        border-radius: 9999px;
        font-size: 0.88rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }
    .populer-chip:hover {
        background: #1b3878;
        color: #ffffff;
        border-color: #1b3878;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(27, 56, 120, 0.2);
    }
    .populer-chip .badge-count {
        background: #e2e8f0;
        color: #475569;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 9999px;
        transition: all 0.2s ease;
    }
    .populer-chip:hover .badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Alphabet Navigation Bar */
    .alphabet-nav-container {
        position: sticky;
        top: 80px;
        z-index: 95;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 10px 14px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
        margin-bottom: 24px;
    }
    .alphabet-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
        justify-content: center;
    }
    .alphabet-btn {
        min-width: 38px;
        height: 38px;
        padding: 0 8px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.88rem;
        text-decoration: none;
        color: #334155;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        transition: all 0.15s ease;
        position: relative;
    }
    .alphabet-btn:hover:not(.disabled) {
        background: #1b3878;
        color: #ffffff;
        border-color: #1b3878;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(27, 56, 120, 0.2);
    }
    .alphabet-btn.active {
        background: #1b3878;
        color: #ffffff;
        border-color: #1b3878;
        box-shadow: 0 4px 10px rgba(27, 56, 120, 0.3);
    }
    .alphabet-btn.all-btn {
        min-width: 68px;
        font-weight: 600;
    }
    .alphabet-btn.disabled {
        opacity: 0.35;
        cursor: not-allowed;
        background: #f8fafc;
        border-color: #f1f5f9;
        color: #94a3b8;
    }

    /* Subject Section & Card */
    .letter-group-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 36px;
        margin-bottom: 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e2e8f0;
    }
    .letter-badge-circle {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #1b3878 0%, #2563eb 100%);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 800;
        box-shadow: 0 4px 10px rgba(27, 56, 120, 0.25);
    }
    .letter-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    /* Subject Card Item */
    .subject-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 14px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
        position: relative;
    }
    .subject-card:hover {
        background: #ffffff;
        border-color: #3b82f6;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.1);
        color: inherit;
    }
    .subject-card .subject-name {
        font-size: 0.92rem;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.4;
        display: block;
        transition: color 0.15s ease;
    }
    .subject-card:hover .subject-name {
        color: #1b3878;
    }
    .subject-sub-heading {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        font-weight: 400;
        margin-top: 3px;
    }
    .subject-card .card-footer-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
        padding-top: 6px;
        border-top: 1px dashed #f1f5f9;
    }
    .subject-count-badge {
        font-size: 0.74rem;
        font-weight: 600;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #dbeafe;
        padding: 2px 7px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
    }
    .subject-action-arrow {
        color: #94a3b8;
        font-size: 0.8rem;
        transition: transform 0.2s ease, color 0.2s ease;
    }
    .subject-card:hover .subject-action-arrow {
        transform: translateX(3px);
        color: #1b3878;
    }

    /* Back to Top Button */
    .btn-back-to-top {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #1b3878;
        color: #ffffff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        box-shadow: 0 6px 18px rgba(27, 56, 120, 0.35);
        cursor: pointer;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        z-index: 99;
    }
    .btn-back-to-top.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .btn-back-to-top:hover {
        background: #2563eb;
        color: #ffffff;
        transform: translateY(-3px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.4);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container py-4" style="padding-top: 100px !important;">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size: 0.9rem;">
            <li class="breadcrumb-item">
                <a href="<?= base_url('home') ?>" class="text-decoration-none text-muted">
                    <i class="fa-solid fa-home me-1"></i>Beranda
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('opac') ?>" class="text-decoration-none text-muted">
                    <i class="fa-solid fa-magnifying-glass me-1"></i>OPAC
                </a>
            </li>
            <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">
                <i class="fa-solid fa-tags me-1"></i>Browse Subjek
            </li>
        </ol>
    </nav>

    <!-- Hero Banner -->
    <div class="subjek-hero mb-4">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center gap-2 bg-white bg-opacity-20 text-white px-3 py-1 rounded-pill small fw-semibold mb-3">
                    <i class="fa-solid fa-layer-group"></i> Direktori Tajuk Subjek
                </div>
                <h1 class="display-6 fw-bold mb-2">Jelajah Koleksi Lewat Subjek</h1>
                <p class="lead mb-4 text-white-50" style="font-size: 1.05rem;">
                    Telusuri ribuan koleksi buku dan dokumen perpustakaan Kementerian Ketenagakerjaan melalui klasifikasi tajuk subjek bidang keilmuan secara teratur dan mudah dipindai.
                </p>

                <!-- Live Search Box -->
                <div class="subjek-search-box">
                    <i class="fa-solid fa-magnifying-glass subjek-search-icon"></i>
                    <input type="text"
                           id="subjectSearchInput"
                           class="form-control subjek-search-input"
                           placeholder="Ketik untuk menyaring subjek (misal: Ketenagakerjaan, Hukum, Manajemen)..."
                           value="<?= esc($search_query ?? '') ?>"
                           autocomplete="off">
                    <button type="button" id="subjectSearchClear" class="subjek-search-clear" title="Bersihkan pencarian">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <!-- Summary Info -->
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 mt-3 text-white-50 small">
                    <span>
                        <i class="fa-solid fa-tags me-1 text-warning"></i>
                        <strong class="text-white"><?= number_format($total_subjects ?? 0) ?></strong> Tajuk Subjek Terdaftar
                    </span>
                    <span>&bull;</span>
                    <span>
                        <i class="fa-solid fa-book me-1 text-info"></i>
                        <strong class="text-white"><?= number_format($total_catalogs ?? 0) ?></strong> Koleksi Berkatalog
                    </span>
                    <span>&bull;</span>
                    <span>
                        <i class="fa-solid fa-arrow-pointer me-1 text-success"></i>
                        Klik nama subjek untuk melihat seluruh koleksinya
                    </span>
                </div>

            </div>
        </div>
    </div>

    <!-- Subjek Terpopuler / Pintasan Utama -->
    <?php if (!empty($top_subjects)): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 1.05rem;">
                    <i class="fa-solid fa-fire text-danger me-2"></i>Subjek Terpopuler
                </h5>
                <small class="text-muted">Topik dengan jumlah koleksi terbanyak</small>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($top_subjects as $top): ?>
                    <a href="<?= base_url('opac?search=' . urlencode($top['name']) . '&search_by=Subject') ?>"
                       class="populer-chip"
                       title="Lihat <?= $top['count'] ?> koleksi tentang <?= esc($top['name']) ?>">
                        <i class="fa-solid fa-tag text-primary opacity-75"></i>
                        <span><?= esc($top['name']) ?></span>
                        <span class="badge-count"><?= number_format($top['count']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sticky Alphabet Navigation Bar -->
    <div class="alphabet-nav-container" id="alphabetNav">
        <div class="alphabet-nav">
            <a href="javascript:void(0);"
               class="alphabet-btn all-btn <?= empty($active_letter) ? 'active' : '' ?>"
               data-letter="all"
               title="Tampilkan semua huruf">
                <i class="fa-solid fa-grip me-1 small"></i> Semua
            </a>

            <?php foreach ($alphabet as $char): ?>
                <?php
                $count = $letter_counts[$char] ?? 0;
                $isDisabled = ($count === 0);
                $isActive = ($active_letter === $char);
                ?>
                <a href="javascript:void(0);"
                   class="alphabet-btn <?= $isActive ? 'active' : '' ?> <?= $isDisabled ? 'disabled' : '' ?>"
                   data-letter="<?= $char ?>"
                   title="<?= $isDisabled ? 'Belum ada subjek berawalan ' . $char : 'Huruf ' . $char . ' (' . $count . ' subjek)' ?>">
                    <?= $char ?>
                </a>
            <?php endforeach; ?>

            <?php
            $countPound = $letter_counts['#'] ?? 0;
            $isDisabledPound = ($countPound === 0);
            $isActivePound = ($active_letter === '#');
            ?>
            <a href="javascript:void(0);"
               class="alphabet-btn <?= $isActivePound ? 'active' : '' ?> <?= $isDisabledPound ? 'disabled' : '' ?>"
               data-letter="#"
               title="<?= $isDisabledPound ? 'Belum ada subjek angka/simbol' : 'Angka & Simbol (' . $countPound . ' subjek)' ?>">
                #
            </a>
        </div>
    </div>

    <!-- Live Filter Counter Info (Updated via JS) -->
    <div id="filterResultInfo" class="d-flex align-items-center justify-content-between mb-3 text-muted small px-1" style="display: none !important;">
        <div>
            <i class="fa-solid fa-filter text-primary me-1"></i>
            Menampilkan <strong id="filterMatchCount" class="text-dark">0</strong> subjek yang cocok
        </div>
        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" id="resetFilterBtn">
            <i class="fa-solid fa-rotate-left me-1"></i>Reset Tampilan
        </button>
    </div>

    <!-- Subject Directory Container -->
    <div id="subjectDirectory">
        <?php
        $totalRendered = 0;
        foreach ($grouped_by_letter as $char => $items):
            if (empty($items)) continue;
            $totalRendered += count($items);
        ?>
            <div class="letter-section mb-4" data-letter-group="<?= $char ?>" id="group-<?= $char ?>">
                <div class="letter-group-header">
                    <div class="letter-badge-circle"><?= $char ?></div>
                    <div>
                        <h2 class="letter-title">Huruf <?= $char ?></h2>
                        <span class="text-muted small group-count-text"><?= count($items) ?> tajuk subjek</span>
                    </div>
                </div>

                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                    <?php foreach ($items as $item): ?>
                        <div class="col subject-item" data-name="<?= esc(mb_strtolower($item['name'])) ?>">
                            <a href="<?= base_url('opac?search=' . urlencode($item['name']) . '&search_by=Subject') ?>"
                               class="subject-card shadow-sm"
                               title="Lihat koleksi: <?= esc($item['name']) ?>">
                                <div class="subject-card-body">
                                    <span class="subject-name">
                                        <i class="fa-solid fa-tag text-primary me-1 small opacity-75"></i><?= esc($item['main_term']) ?>
                                        <?php if (!empty($item['sub_term'])): ?>
                                            <span class="subject-sub-heading">
                                                <i class="fa-solid fa-angle-right text-muted small me-1"></i><?= esc($item['sub_term']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="card-footer-meta">
                                    <span class="subject-count-badge">
                                        <i class="fa-solid fa-book-bookmark me-1"></i><?= number_format($item['count']) ?> koleksi
                                    </span>
                                    <span class="subject-action-arrow">
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </span>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Empty State (When no subjects found) -->
        <div id="emptySearchState" class="text-center py-5 my-4 bg-white rounded-4 border border-light shadow-sm" style="<?= ($totalRendered === 0) ? '' : 'display: none;' ?>">
            <div class="mb-3 text-secondary opacity-50">
                <i class="fa-solid fa-tags fa-4x"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Subjek Tidak Ditemukan</h4>
            <p class="text-muted mb-4" id="emptyStateMessage">
                Tidak ada tajuk subjek yang cocok dengan kata kunci atau filter yang Anda pilih.
            </p>
            <button type="button" class="btn btn-primary rounded-pill px-4" id="emptyResetBtn">
                <i class="fa-solid fa-rotate-left me-1"></i> Tampilkan Semua Subjek
            </button>
        </div>

    </div>

</div>

<!-- Floating Back to Top Button -->
<button type="button" class="btn-back-to-top" id="backToTopBtn" title="Kembali ke atas" aria-label="Kembali ke atas">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput      = document.getElementById('subjectSearchInput');
    const searchClear      = document.getElementById('subjectSearchClear');
    const alphabetButtons  = document.querySelectorAll('.alphabet-btn');
    const subjectSections  = document.querySelectorAll('.letter-section');
    const subjectCards     = document.querySelectorAll('.subject-item');
    const emptyState       = document.getElementById('emptySearchState');
    const filterResultInfo = document.getElementById('filterResultInfo');
    const filterMatchCount = document.getElementById('filterMatchCount');
    const resetFilterBtn   = document.getElementById('resetFilterBtn');
    const emptyResetBtn    = document.getElementById('emptyResetBtn');
    const backToTopBtn     = document.getElementById('backToTopBtn');

    let currentLetterFilter = '<?= esc($active_letter ?? 'all') ?>' || 'all';

    // Toggle tombol clear pada input pencarian
    function toggleClearButton() {
        if (searchInput.value.trim().length > 0) {
            searchClear.style.display = 'block';
        } else {
            searchClear.style.display = 'none';
        }
    }

    // Fungsi utama pemfilteran instan di sisi klien
    function applyFilter() {
        const query = searchInput.value.trim().toLowerCase();
        let totalVisible = 0;

        toggleClearButton();

        subjectSections.forEach(section => {
            const sectionLetter = section.getAttribute('data-letter-group');
            const items = section.querySelectorAll('.subject-item');
            let sectionVisibleCount = 0;

            // Jika ada letter filter aktif (selain 'all'), lewati section huruf lain
            if (currentLetterFilter !== 'all' && currentLetterFilter !== '' && sectionLetter !== currentLetterFilter) {
                section.style.display = 'none';
                return;
            }

            items.forEach(item => {
                const subjectName = item.getAttribute('data-name') || '';
                let matches = true;

                if (query.length > 0 && !subjectName.includes(query)) {
                    matches = false;
                }

                if (matches) {
                    item.style.display = '';
                    sectionVisibleCount++;
                    totalVisible++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Sembunyikan header huruf jika tidak ada item yang cocok di huruf tersebut
            if (sectionVisibleCount > 0) {
                section.style.display = '';
                const countText = section.querySelector('.group-count-text');
                if (countText) {
                    if (query.length > 0) {
                        countText.textContent = `${sectionVisibleCount} cocok`;
                    } else {
                        countText.textContent = `${items.length} tajuk subjek`;
                    }
                }
            } else {
                section.style.display = 'none';
            }
        });

        // Tampilkan info hasil jika ada filter aktif
        if (query.length > 0 || (currentLetterFilter !== 'all' && currentLetterFilter !== '')) {
            filterResultInfo.style.setProperty('display', 'flex', 'important');
            filterMatchCount.textContent = totalVisible.toLocaleString('id-ID');
        } else {
            filterResultInfo.style.setProperty('display', 'none', 'important');
        }

        // Tampilkan empty state jika 0 hasil
        if (totalVisible === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    // Event input pencarian realtime
    let searchDebounce;
    searchInput.addEventListener('input', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(applyFilter, 80);
    });

    searchClear.addEventListener('click', function () {
        searchInput.value = '';
        searchInput.focus();
        applyFilter();
    });

    // Event klik navigasi huruf (A-Z)
    alphabetButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            if (this.classList.contains('disabled')) return;

            alphabetButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const letter = this.getAttribute('data-letter');
            currentLetterFilter = letter;

            applyFilter();

            // Jika memilih huruf tertentu, scroll lembut ke section tersebut
            if (letter !== 'all' && letter !== '') {
                const targetEl = document.getElementById(`group-${letter}`);
                if (targetEl && targetEl.style.display !== 'none') {
                    const navHeight = 160;
                    const elPos = targetEl.getBoundingClientRect().top + window.pageYOffset - navHeight;
                    window.scrollTo({ top: elPos, behavior: 'smooth' });
                }
            }
        });
    });

    // Reset filter
    function resetAll() {
        searchInput.value = '';
        currentLetterFilter = 'all';
        alphabetButtons.forEach(b => {
            if (b.getAttribute('data-letter') === 'all') {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });
        applyFilter();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    if (resetFilterBtn) resetFilterBtn.addEventListener('click', resetAll);
    if (emptyResetBtn) emptyResetBtn.addEventListener('click', resetAll);

    // Tombol Back to Top
    window.addEventListener('scroll', function () {
        if (window.pageYOffset > 400) {
            backToTopBtn.classList.add('show');
        } else {
            backToTopBtn.classList.remove('show');
        }
    });

    backToTopBtn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Inisialisasi awal
    if (searchInput.value.trim().length > 0 || currentLetterFilter !== 'all') {
        applyFilter();
    }
});
</script>
<?= $this->endSection() ?>
