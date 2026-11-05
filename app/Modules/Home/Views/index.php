<?php
// Beranda: tampilan dari desain Figma "Proto 1" (landing page perpustakaan).
// CSS: public/assets/home/landing.css, JS: public/assets/home/landing.js
$db = db_connect();
$param = static function (string $name, string $default = '') use ($db) {
    $row = $db->table('settingparameters')->where('Name', $name)->get()->getRow();
    return ($row && $row->Value !== '') ? $row->Value : $default;
};
$namaPerpustakaan = $param('NamaPerpustakaan', 'Perpustakaan Kemnaker RI');
// Alamat dan email tetap (bukan dari pengaturan), supaya footer selalu menampilkan data Kemnaker RI.
$alamat           = 'Jl. Jenderal Gatot Subroto Kav. 51, RT.5/RW.4, Kuningan Timur, Setiabudi, Jakarta Selatan 12950 · Gedung B';

$surel            = 'perpustakaan@kemnaker.go.id';

// Footer: jam layanan, akun sosial, dan direktori. Ganti di sini bila ada perubahan.
$jamLayanan = [['Senin – Kamis', '07.30 – 16.00 WIB'], ['Jumat', '07.30 – 16.30 WIB']];
$sosial = [
    'instagram' => ['Instagram', 'https://www.instagram.com/kemnaker/'],
    'facebook'  => ['Facebook', 'https://www.facebook.com/KemnakerRI'],
    'x'         => ['X', 'https://x.com/KemnakerRI'],
    'youtube'   => ['YouTube', 'https://www.youtube.com/@KemnakerRI'],
];
$direktori = [
    ['Kementerian Ketenagakerjaan', 'https://kemnaker.go.id'],
    ['Perpustakaan Nasional RI', 'https://www.perpusnas.go.id'],
    ['Satu Data Ketenagakerjaan', 'https://satudata.kemnaker.go.id'],
    ['SIAPkerja', 'https://siapkerja.kemnaker.go.id'],
    ['JDIH Kemnaker', 'https://jdih.kemnaker.go.id'],
];

$assets = base_url('assets/home');

$icons = [
    'search'   => '<circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4 4"/>',
    'up-right' => '<path d="M7 17 17 7M9 7h8v8"/>',
    'right'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
    'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    'close'    => '<path d="m6 6 12 12M18 6 6 18"/>',
    'chevron'  => '<path d="m6 9 6 6 6-6"/>',
    'home'     => '<path d="M4 10.5 12 4l8 6.5V19a1.5 1.5 0 0 1-1.5 1.5H15v-6H9v6H5.5A1.5 1.5 0 0 1 4 19Z"/>',
    'grid'     => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.6"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.6"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.6"/>',
    'tamu'     => '<path d="M6 3.5h11a1.5 1.5 0 0 1 1.5 1.5v14a1.5 1.5 0 0 1-1.5 1.5H6Z"/><path d="M6 3.5v17M9.5 8h5.5M9.5 11.5h5.5M9.5 15h3"/>',
    'registrasi' => '<circle cx="9.5" cy="8" r="3.5"/><path d="M3.5 19.5c.6-3.3 3-5.3 6-5.3s5.4 2 6 5.3M18.5 8v6M15.5 11h6"/>',
    'pinjam'   => '<path d="M4 8V5.5A1.5 1.5 0 0 1 5.5 4H8M16 4h2.5A1.5 1.5 0 0 1 20 5.5V8M20 16v2.5a1.5 1.5 0 0 1-1.5 1.5H16M8 20H5.5A1.5 1.5 0 0 1 4 18.5V16"/><path d="M8 8.5v7M11 8.5v7M13.5 8.5v7M16 8.5v7M3 12h18"/>',
    'kembali'  => '<path d="M3.5 13.5 6 6h12l2.5 7.5V18a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2Z"/><path d="M3.5 13.5H8l1.5 2.5h5l1.5-2.5h4.5M12 3v7M9.5 7.5 12 10l2.5-2.5"/>',
    'baca'     => '<path d="M3 10.5h18M5 10.5V20M19 10.5V20M8 10.5V7.5a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v3"/><path d="M9.5 4h5"/>',
    'isbn'     => '<rect x="3.5" y="4.5" width="17" height="15" rx="2"/><path d="M7 8v6M9.5 8v6M12 8v6M15.5 8v6M17.5 8v6M7 16.5h10.5"/>',
    'pin'      => '<path d="M12 21s-6.5-5.6-6.5-11A6.5 6.5 0 0 1 18.5 10c0 5.4-6.5 11-6.5 11Z"/><circle cx="12" cy="10" r="2.3"/>',
    'mail'     => '<rect x="3.5" y="5.5" width="17" height="13" rx="2"/><path d="m4.5 7 7.5 6 7.5-6"/>',
    'clock'    => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.5V12l3 2"/>',
    'external' => '<path d="M7 17 17 7M9 7h8v8"/>',
    'instagram' => '<rect x="4" y="4" width="16" height="16" rx="4.5"/><circle cx="12" cy="12" r="3.6"/><circle cx="16.8" cy="7.2" r=".6"/>',
    'facebook' => '<path d="M14 8.5h2.5V5H14c-2.2 0-3.5 1.5-3.5 3.6V11H8v3.5h2.5V20H14v-5.5h2.4L17 11h-3V9c0-.3.3-.5.6-.5Z"/>',
    'x'        => '<path d="m5 5 14 14M19 5 5 19"/>',
    'youtube'  => '<rect x="3.5" y="6.5" width="17" height="11" rx="3"/><path d="m10.5 9.5 4 2.5-4 2.5Z"/>',
    'sirkulasi' => '<path d="M4.5 9.5A8 8 0 0 1 19 7.5M19.5 14.5A8 8 0 0 1 5 16.5"/><path d="M19.5 3.5v4.5H15M4.5 20.5V16H9"/>',
];
$icon = static function (string $name, string $class = '') use ($icons): string {
    return '<svg class="' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icons[$name] . '</svg>';
};
$iconIsiPath = [
    'instagram' => 'M12 7.3a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4Zm0 7.7a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm6-7.9a1.1 1.1 0 1 1-2.2 0 1.1 1.1 0 0 1 2.2 0ZM21.1 8c-.1-1.5-.4-2.8-1.5-3.9S17.5 2.7 16 2.6c-1.5-.1-6.1-.1-7.7 0-1.5.1-2.8.4-3.9 1.5S2.9 6.5 2.8 8c-.1 1.6-.1 6.2 0 7.8.1 1.5.4 2.8 1.5 3.9s2.4 1.4 3.9 1.5c1.6.1 6.1.1 7.7 0 1.5-.1 2.8-.4 3.9-1.5s1.4-2.4 1.5-3.9c.1-1.6.1-6.2 0-7.8Zm-2 9.5a3.1 3.1 0 0 1-1.8 1.8c-1.2.5-4.2.4-5.5.4s-4.3.1-5.5-.4a3.1 3.1 0 0 1-1.8-1.8c-.5-1.2-.4-4.2-.4-5.5s-.1-4.3.4-5.5a3.1 3.1 0 0 1 1.8-1.8c1.2-.5 4.2-.4 5.5-.4s4.3-.1 5.5.4a3.1 3.1 0 0 1 1.8 1.8c.5 1.2.4 4.2.4 5.5s.1 4.3-.4 5.5Z',
    'facebook'  => 'M13.4 21v-7.6h2.6l.4-3h-3V8.5c0-.9.2-1.5 1.5-1.5h1.6V4.3c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v2.2H7.7v3h2.6V21h3.1Z',
    'x'         => 'M17.2 3.5h2.9l-6.3 7.2 7.4 9.8h-5.8l-4.5-5.9-5.2 5.9H2.8l6.7-7.7-7.1-9.3h5.9l4.1 5.4 4.8-5.4Zm-1 15.3h1.6L7.5 5.1H5.8l10.4 13.7Z',
    'youtube'   => 'M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4a2.5 2.5 0 0 0-1.8 1.8C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8ZM10 15V9l5.2 3-5.2 3Z',
];
$iconIsi = static function (string $name) use ($iconIsiPath): string {
    return '<svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="' . $iconIsiPath[$name] . '"/></svg>';
};
$star = '<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3Z"/></svg>';

$keanggotaanOnline = env('Is_keanggotaan_online') == 1;

// Layanan: url ke halaman yang sudah ada di aplikasi ini.
$services = [
    'registrasi' => ['title' => 'Pendaftaran Anggota', 'short' => 'Daftar anggota secara mandiri', 'url' => base_url('home/pendaftaran_online'), 'keywords' => 'daftar registrasi anggota member kartu', 'enabled' => $keanggotaanOnline],
    'pinjam'     => ['title' => 'Peminjaman', 'short' => 'Pindai barcode, langsung pinjam', 'url' => base_url('peminjaman-mandiri'), 'keywords' => 'pinjam peminjaman sirkulasi scan barcode', 'enabled' => true],
    'kembali'    => ['title' => 'Pengembalian', 'short' => 'Kembalikan buku lewat drop-box', 'url' => base_url('pengembalian-mandiri'), 'keywords' => 'kembali pengembalian sirkulasi drop box', 'enabled' => true],
    'baca'       => ['title' => 'Baca di Tempat', 'short' => 'Reservasi meja di ruang baca', 'url' => base_url('baca-ditempat'), 'keywords' => 'baca tempat ruang meja reservasi', 'enabled' => true],
    'isbn'       => ['title' => 'Layanan ISBN', 'short' => 'Ajukan ISBN publikasi Anda', 'url' => base_url('home/layanan-isbn'), 'keywords' => 'isbn perpusnas terbit publikasi penerbit', 'enabled' => true],
    'tamu'       => ['title' => 'Buku Tamu', 'short' => 'Catat kunjungan Anda', 'url' => base_url('buku-tamu'), 'keywords' => 'tamu kunjungan pengunjung', 'enabled' => true],
    'sirkulasi'  => ['title' => 'Tata Cara Sirkulasi', 'short' => 'Panduan pinjam & kembali buku', 'url' => base_url('home/tata-cara-sirkulasi'), 'keywords' => 'sirkulasi tata cara aturan panduan pinjam kembali denda perpanjang', 'enabled' => true],
];
$layananMenu   = ['registrasi', 'baca', 'isbn', 'sirkulasi'];
$shortcutOrder = ['registrasi', 'pinjam', 'kembali', 'baca', 'isbn', 'tamu'];
$services = array_filter($services, static fn($s) => $s['enabled']);
$layananMenu   = array_values(array_filter($layananMenu, static fn($id) => isset($services[$id])));
$shortcutOrder = array_values(array_filter($shortcutOrder, static fn($id) => isset($services[$id])));

$jsServices = [];
foreach ($shortcutOrder as $id) {
    $jsServices[] = ['id' => $id, 'title' => $services[$id]['title'], 'short' => $services[$id]['short'], 'url' => $services[$id]['url'], 'keywords' => $services[$id]['keywords'], 'icon' => $icon($id)];
}

// Sampul buku buatan sendiri untuk katalog tanpa gambar sampul.
// Warna mengikuti unit penerbit: [latar, aksen]. Buku umum dapat warna acak (tetap per buku) dari $warnaAcak.
$warnaDivisi = [
    ['pola' => '/barenbang/i',                                         'warna' => ['#ea580c', '#ffe1c9'], 'terang' => false],
    ['pola' => '/binalavotas/i',                                       'warna' => ['#1d4ed8', '#cfe0ff'], 'terang' => false],
    ['pola' => '/\birjen\b|inspektorat\s+jenderal/i',                  'warna' => ['#eaff00', '#2f3300'], 'terang' => true],
    ['pola' => '/\bsekjen\b|\bsetjen\b|sekretariat\s+jenderal/i',      'warna' => ['#a7f0b4', '#14532d'], 'terang' => true],
    ['pola' => '/\bphi\b|\bjsk\b|hubungan\s+industrial\s+dan\s+jaminan/i', 'warna' => ['#ffffff', '#1f2a44'], 'terang' => true],
    ['pola' => '/binapenta/i',                                         'warna' => ['#ff8fc1', '#5c0a33'], 'terang' => true],
    ['pola' => '/binwasnaker/i',                                       'warna' => ['#d91f26', '#ffd9d9'], 'terang' => false],
];
$warnaAcak = [
    ['#6c3fd1', '#e2d6ff'], ['#0f766e', '#ccfbf1'], ['#101521', '#f2b544'], ['#7c4a2d', '#f6dcc4'],
    ['#7e1f6b', '#f7d3ee'], ['#1f6f5c', '#c9f0de'], ['#3a3f4b', '#e6e8ee'], ['#4b2e83', '#d9ccff'],
];
$punyaSampul = static fn($book) => !empty($book->CoverURL) && is_file(FCPATH . 'uploads/katalog/' . $book->CoverURL);
$gayaSampul = static function ($book) use ($warnaDivisi, $warnaAcak): array {
    $teks = ($book->Author ?? '') . ' ' . ($book->Publisher ?? '');
    foreach ($warnaDivisi as $divisi) {
        if (preg_match($divisi['pola'], $teks)) {
            return ['warna' => $divisi['warna'], 'terang' => $divisi['terang']];
        }
    }
    return ['warna' => $warnaAcak[crc32((string) $book->ID) % count($warnaAcak)], 'terang' => false];
};

$opacUrl      = base_url('opac');
$featured     = array_slice($featured_books ?? [], 0, 4);
$popular      = $popular_books ?? [];
$newest       = $featured[0] ?? null;
$coverFor     = static fn($book) => get_catalog_thumb_url($book->CoverURL ?: '', 300, 450);
$defaultCover = base_url('assets/img/default-cover.webp');
$totalMembers = (int) ($statistics['total_members'] ?? 0);
$totalCatalogs = (int) ($total_catalogs ?? 0);
$subjects      = $subjects ?? [];
?>
<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Beranda') ?> | <?= esc($namaPerpustakaan) ?></title>
    <link rel="icon" href="<?= base_url('assets/img/logo-inlislite-icon.webp') ?>">
    <link rel="stylesheet" href="<?= $assets ?>/landing.css?v=20">
    <link rel="stylesheet" href="<?= $assets ?>/landing-extra.css?v=39">
</head>

<body>
    <?= $this->include('App\Views\layout\partial\loader'); ?>
    <main class="page-shell">
        <div class="site-frame">

            <header class="site-header">
                <a href="#beranda" class="brand" aria-label="<?= esc($namaPerpustakaan) ?> - Beranda">
                    <img class="brand-mark" src="<?= $assets ?>/logo-kemnaker.png" alt="" width="40" height="40">
                    <span class="brand-copy">
                        <strong>Perpustakaan</strong>
                        <span>Kementerian Ketenagakerjaan RI</span>
                    </span>
                </a>

                <nav class="main-nav" id="main-nav" aria-label="Navigasi utama">
                    <a href="#beranda" class="active"><?= $icon('home', 'nav-icon') ?>Beranda</a>
                    <a href="<?= $opacUrl ?>"><?= $icon('search', 'nav-icon') ?>Cari Koleksi</a>
                    <a href="<?= $services['tamu']['url'] ?>" class="nav-link"><?= $icon('tamu', 'nav-icon') ?>Buku Tamu</a>

                    <div class="nav-dropdown" id="nav-dropdown">
                        <button type="button" class="nav-link dropdown-trigger" aria-expanded="false" aria-controls="layanan-menu">
                            <?= $icon('grid', 'nav-icon') ?>Layanan<?= $icon('chevron', 'chevron') ?>
                        </button>
                        <div class="dropdown-panel" id="layanan-menu">
                            <span class="dropdown-title">Layanan perpustakaan</span>
                            <ul>
                                <?php foreach ($layananMenu as $id) : $s = $services[$id]; ?>
                                    <li>
                                        <a class="dropdown-item" href="<?= $s['url'] ?>">
                                            <span class="dropdown-icon"><?= $icon($id) ?></span>
                                            <span class="dropdown-copy">
                                                <strong><?= esc($s['title']) ?></strong>
                                                <small><?= esc($s['short']) ?></small>
                                            </span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </nav>

                <div class="header-actions">
                    <button class="menu-button" id="menu-button" type="button" aria-label="Buka menu" aria-expanded="false">
                        <span data-open><?= $icon('menu') ?></span>
                        <span data-close hidden><?= $icon('close') ?></span>
                    </button>
                </div>
            </header>

            <section class="hero" id="beranda">
                <div class="hero-copy">
                    <div class="eyebrow"><span></span> Pusat literasi ketenagakerjaan</div>
                    <h1>
                        Pengetahuan Untuk<br>
                        <span>Kemajuan Pekerja</span><br>
                        Indonesia.
                    </h1>
                    <p>Cari buku, regulasi, riset, hingga layanan perpustakaan dalam satu kolom pencarian.</p>

                    <div class="omnisearch" id="omnisearch">
                        <form class="catalog-search" action="<?= $opacUrl ?>" method="get" role="search">
                            <?= $icon('search', 'search-icon') ?>
                            <label class="sr-only" for="omnisearch-input">Cari buku, penulis, topik, atau layanan</label>
                            <input id="omnisearch-input" name="search" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="omni-results" placeholder="Cari judul, penulis, topik, atau layanan">
                            <button type="submit" aria-label="Cari di katalog"><?= $icon('up-right') ?></button>
                        </form>
                        <ul class="omni-results" id="omni-results" role="listbox" hidden></ul>

                        <div class="omni-suggestions">
                            <span>Populer:</span>
                            <?php foreach (['K3', 'Hukum ketenagakerjaan', 'SKKNI', 'Statistik', 'ISBN'] as $item) : ?>
                                <button type="button" data-suggest="<?= esc($item) ?>"><?= esc($item) ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="hero-visual" aria-label="Interior perpustakaan modern">
                    <div class="visual-orbit orbit-one"></div>
                    <div class="visual-orbit orbit-two"></div>
                    <div class="hero-image-wrap">
                        <img src="<?= $assets ?>/library-hero.jpg?v=2" alt="Ruang baca perpustakaan dengan rak buku kayu, bangku, dan meja baca">
                        <div class="image-shade"></div>
                        <div class="image-caption">
                            <span>01 / Ruang Baca</span>
                            <strong>Tempat ide bertemu dan bertumbuh.</strong>
                        </div>
                    </div>

                    <?php if ($newest) : ?>
                        <a class="floating-card card-top" href="<?= base_url('opac/detail/' . $newest->ID) ?>" style="text-decoration:none;color:inherit">
                            <span class="mini-icon">Aa</span>
                            <div>
                                <small>Koleksi baru</small>
                                <strong><?= esc(mb_strimwidth($newest->Title, 0, 28, '…')) ?></strong>
                            </div>
                        </a>
                    <?php endif; ?>
                    <?php if ($totalMembers > 0) : ?>
                        <div class="floating-card card-bottom">
                            <div class="reader-avatars"><span>A</span><span>D</span><span>R</span></div>
                            <div>
                                <strong><?= number_format($totalMembers, 0, ',', '.') ?></strong>
                                <small>Anggota terdaftar</small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="shortcuts" id="layanan" aria-labelledby="shortcut-title">
                <div class="shortcuts-head">
                    <h2 id="shortcut-title">Akses cepat layanan</h2>
                    <span><?= count($shortcutOrder) ?> layanan · langsung dari halaman ini</span>
                </div>
                <div class="shortcut-grid">
                    <?php foreach ($shortcutOrder as $index => $id) : $s = $services[$id]; ?>
                        <a href="<?= $s['url'] ?>" class="shortcut-card">
                            <span class="shortcut-top">
                                <span class="shortcut-icon"><?= $icon($id) ?></span>
                                <span class="shortcut-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                            </span>
                            <strong><?= esc($s['title']) ?></strong>
                            <small><?= esc($s['short']) ?></small>
                            <span class="shortcut-go">Buka <?= $icon('right') ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="showcase" id="unggulan">
                <div class="section-head">
                    <div>
                        <div class="section-kicker"><span></span> Pilihan pustakawan</div>
                        <h2>Buku unggulan &amp; populer.</h2>
                    </div>
                    <div class="tab-switch" role="tablist" aria-label="Kategori showcase">
                        <button type="button" role="tab" aria-selected="true" class="is-active" data-tab="terbaru">Unggulan</button>
                        <button type="button" role="tab" aria-selected="false" data-tab="populer">Populer</button>
                    </div>
                </div>

                <?php
                $panels = ['terbaru' => $featured, 'populer' => $popular];
                foreach ($panels as $key => $list) : ?>
                    <div class="showcase-grid" role="tabpanel" data-panel="<?= $key ?>" <?= $key === 'terbaru' ? '' : 'hidden' ?>>
                        <?php if (empty($list)) : ?>
                            <p class="showcase-empty">Belum ada data untuk ditampilkan.</p>
                        <?php endif; ?>
                        <?php foreach ($list as $index => $book) : ?>
                            <article class="showcase-card">
                                <div class="showcase-cover">
                                    <span class="showcase-rank">#<?= $index + 1 ?></span>
                                    <?php if ($punyaSampul($book)) : ?>
                                        <img src="<?= $coverFor($book) ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='<?= $defaultCover ?>'">
                                    <?php else :
                                        $gaya     = $gayaSampul($book);
                                        $penulis  = trim((string) $book->Author, " ;,\t\n");
                                        $kategori = trim(explode(';', (string) ($book->Subject ?? ''))[0], " ,\t\n");
                                    ?>
                                        <div class="book-cover book-cover-md<?= $gaya['terang'] ? ' book-cover-light' : '' ?>" style="background:<?= $gaya['warna'][0] ?>;color:<?= $gaya['warna'][1] ?>" aria-hidden="true">
                                            <span class="book-cover-spine"></span>
                                            <span class="book-cover-category"><?= esc(mb_strimwidth($kategori ?: 'Koleksi', 0, 26, '…')) ?></span>
                                            <strong class="book-cover-title"><?= esc($book->Title) ?></strong>
                                            <span class="book-cover-author"><?= esc(mb_strimwidth($penulis ?: 'Anonim', 0, 34, '…')) ?></span>
                                            <span class="book-cover-shape" style="background:<?= $gaya['warna'][1] ?>"></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="showcase-info">
                                    <span class="showcase-meta">
                                        <?php if ($key === 'populer') : ?>
                                            <?= $star ?> <span><?= (int) $book->Loans ?> kali dipinjam</span>
                                        <?php else : ?>
                                            <span><?= esc($book->PublishYear ?: 'Koleksi baru') ?></span>
                                        <?php endif; ?>
                                    </span>
                                    <h3><?= esc($book->Title) ?></h3>
                                    <p><?= esc(trim((string) $book->Author, " ;,	
") ?: 'Anonim') ?></p>
                                    <div class="showcase-actions">
                                        <a class="btn-small" href="<?= base_url('opac/detail/' . $book->ID) ?>">Lihat</a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="catalog-cta" id="katalog">
                <div class="catalog-cta-copy">
                    <div class="section-kicker"><span></span> Eksplorasi katalog OPAC</div>
                    <h2>Jelajahi <?= $totalCatalogs ? number_format($totalCatalogs, 0, ',', '.') . ' judul' : 'seluruh' ?> koleksi berdasarkan subjek.</h2>
                    <p>Cari berdasarkan judul, penulis, atau kode buku, lalu cek nomor panggil dan ketersediaan eksemplar.</p>
                    <a class="cta-button" href="<?= $opacUrl ?>">Buka katalog lengkap <?= $icon('right') ?></a>
                </div>
                <?php if (!empty($subjects)) : ?>
                    <ul class="subject-list">
                        <?php foreach ($subjects as $subjek) : ?>
                            <li>
                                <a href="<?= $opacUrl ?>?Subject=<?= urlencode($subjek->Nama) ?>">
                                    <span><?= esc($subjek->Nama) ?></span>
                                    <small><?= number_format((int) $subjek->Jumlah, 0, ',', '.') ?> judul</small>
                                    <?= $icon('up-right') ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <footer class="site-footer" id="kontak">
                <div class="footer-main">
                    <div class="footer-about">
                        <a href="#beranda" class="footer-brand">
                            <img src="<?= $assets ?>/logo-kemnaker-white.png" alt="" width="40" height="40">
                            <span>
                                <strong>Perpustakaan Kemnaker</strong>
                                <small>Kementerian Ketenagakerjaan Republik Indonesia</small>
                            </span>
                        </a>
                        <p class="footer-description">Perpustakaan Kementerian Ketenagakerjaan Republik Indonesia berkomitmen menyediakan layanan perpustakaan yang berkualitas bagi seluruh pegawai dan masyarakat. Kami menghimpun koleksi buku, regulasi, laporan riset, dan publikasi ketenagakerjaan untuk mendukung perumusan kebijakan serta peningkatan literasi insan ketenagakerjaan Indonesia.</p>

                        <ul class="footer-contact">
                            <li><?= $icon('pin') ?><span><?= esc($alamat) ?></span></li>
                            <li><?= $icon('mail') ?><a href="mailto:<?= esc($surel) ?>"><?= esc($surel) ?></a></li>
                            <li>
                                <?= $icon('clock') ?>
                                <span>
                                    <?php foreach ($jamLayanan as [$hari, $jam]) : ?>
                                        <span class="footer-hours"><b><?= esc($hari) ?></b> <?= esc($jam) ?></span>
                                    <?php endforeach; ?>
                                </span>
                            </li>
                        </ul>

                        <div class="footer-socials">
                            <?php foreach ($sosial as $id => [$nama, $url]) : ?>
                                <a href="<?= esc($url) ?>" target="_blank" rel="noreferrer" aria-label="<?= esc($nama) ?> Kemnaker"><?= $iconIsi($id) ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <nav class="footer-col" aria-label="Layanan">
                        <h2>Layanan</h2>
                        <ul>
                            <li><a href="<?= $opacUrl ?>">Katalog (OPAC)</a></li>
                            <?php foreach (['registrasi' => 'Pendaftaran Anggota', 'pinjam' => 'Peminjaman Mandiri', 'kembali' => 'Pengembalian Mandiri', 'baca' => 'Baca di Tempat', 'isbn' => 'Layanan ISBN'] as $id => $label) : if (!isset($services[$id])) continue; ?>
                                <li><a href="<?= $services[$id]['url'] ?>"><?= esc($label) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>

                    <nav class="footer-col" aria-label="Petunjuk">
                        <h2>Petunjuk</h2>
                        <ul>
                            <li><a href="<?= $services['sirkulasi']['url'] ?>">Tata Cara Sirkulasi</a></li>
                            <li><button type="button" data-rules>Tata Tertib Keanggotaan</button></li>
                            <li><a href="<?= $services['tamu']['url'] ?>">Buku Tamu</a></li>
                            <li><a href="mailto:<?= esc($surel) ?>?subject=Usulan%20Buku">Usulan Buku</a></li>
                        </ul>
                    </nav>

                    <nav class="footer-col" aria-label="Direktori">
                        <h2>Direktori</h2>
                        <ul>
                            <?php foreach ($direktori as [$nama, $url]) : ?>
                                <li><a href="<?= esc($url) ?>" target="_blank" rel="noreferrer"><?= esc($nama) ?><?= $icon('up-right', 'footer-ext') ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </nav>
                </div>

                <div class="footer-bottom">
                    <span>© <?= date('Y') ?> Perpustakaan Kementerian Ketenagakerjaan. Seluruh hak cipta dilindungi.</span>
                    <span class="footer-mark">Kementerian Ketenagakerjaan · Republik Indonesia</span>
                </div>
            </footer>
        </div>
    </main>

    <script>
        window.HOME_DATA = {
            opacUrl: <?= json_encode($opacUrl) ?>,
            searchUrl: <?= json_encode(base_url('home/search-books')) ?>,
            rulesUrl: <?= json_encode($assets . '/tata-tertib.json') ?>,
            detailUrl: <?= json_encode(base_url('opac/detail')) ?>,
            services: <?= json_encode($jsServices, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
            arrow: <?= json_encode($icon('up-right', 'omni-all-icon')) ?>
        };
    </script>
    <script src="<?= $assets ?>/landing.js?v=21"></script>
</body>

</html>
