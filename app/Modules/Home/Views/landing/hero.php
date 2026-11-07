<?php
// Bagian utama: judul, kolom pencarian, carousel foto, dan dua kartu pembaruan.
// Data dari Home::index(): featured_books, new_catalog_since, new_members, new_members_days.
$opacUrl  = base_url('opac');
$fotoHero = landing_hero_photos();
$newest   = ($featured_books ?? [])[0] ?? null;
?>
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
                <?= landing_icon('search', 'search-icon') ?>
                <label class="sr-only" for="omnisearch-input">Cari buku, penulis, topik, atau layanan</label>
                <input id="omnisearch-input" name="search" autocomplete="off" role="combobox" aria-expanded="false" aria-controls="omni-results" placeholder="Cari judul, penulis, topik, atau layanan">
                <button type="submit" aria-label="Cari di katalog"><?= landing_icon('up-right') ?></button>
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
            <div class="hero-slides" data-hero-slides>
                <?php foreach ($fotoHero as $i => $foto) : ?>
                    <img class="hero-slide<?= $i === 0 ? ' is-active' : '' ?>" src="<?= esc($foto['src'], 'attr') ?>" alt="<?= esc($foto['alt'], 'attr') ?>" data-label="<?= esc($foto['label'], 'attr') ?>" <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                <?php endforeach; ?>
            </div>
            <div class="image-shade"></div>
            <div class="image-caption">
                <span data-hero-label>01 / <?= esc($fotoHero[0]['label']) ?></span>
            </div>
            <?php if (count($fotoHero) > 1) : ?>
                <div class="hero-nav" role="group" aria-label="Pilih foto (juga bisa digeser atau memakai tombol panah atas dan bawah)">
                    <span class="hero-tetes" aria-hidden="true"></span>
                    <?php foreach ($fotoHero as $i => $foto) : ?>
                        <button type="button" class="hero-dot<?= $i === 0 ? ' is-active' : '' ?>" aria-label="Foto <?= $i + 1 ?>: <?= esc($foto['label'], 'attr') ?>" aria-pressed="<?= $i === 0 ? 'true' : 'false' ?>"></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php /* Kartu melayang hanya tampil bila ada pembaruan (lihat KOLEKSI_BARU_HARI dan ANGGOTA_BARU_HARI di Home.php). */ ?>
        <?php if ($newest && !empty($newest->CreateDate) && $newest->CreateDate >= ($new_catalog_since ?? '9999')) : ?>
            <a class="floating-card card-top" href="<?= base_url('opac/detail/' . $newest->ID) ?>" style="text-decoration:none;color:inherit">
                <span class="mini-icon">Aa</span>
                <div>
                    <small>Koleksi baru</small>
                    <strong><?= esc(mb_strimwidth($newest->Title, 0, 28, '…')) ?></strong>
                </div>
            </a>
        <?php endif; ?>
        <?php if (($new_members ?? 0) > 0) : ?>
            <div class="floating-card card-bottom">
                <div class="reader-avatars"><span>A</span><span>D</span><span>R</span></div>
                <div>
                    <strong>+<?= number_format((int) $new_members, 0, ',', '.') ?></strong>
                    <small>Anggota baru, <?= (int) ($new_members_days ?? 14) ?> hari terakhir</small>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
