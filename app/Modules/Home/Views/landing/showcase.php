<?php
// Tab "Terbaru" (koleksi yang paling baru masuk katalog) dan "Populer" (paling sering dipinjam). Tab diganti oleh js/showcase.js.
$terbaru       = array_slice($featured_books ?? [], 0, 4);
$populer      = $popular_books ?? [];
$defaultCover = base_url('assets/img/default-cover.webp');
?>
<section class="showcase" id="buku">
    <div class="section-head">
        <div>
            <h2>Buku terbaru &amp; populer.</h2>
        </div>
        <div class="tab-switch" role="tablist" aria-label="Kategori showcase">
            <button type="button" role="tab" aria-selected="true" class="is-active" data-tab="terbaru">Terbaru</button>
            <button type="button" role="tab" aria-selected="false" data-tab="populer">Populer</button>
        </div>
    </div>

    <?php
    $panels = ['terbaru' => $terbaru, 'populer' => $populer];
    foreach ($panels as $key => $list) : ?>
        <div class="showcase-grid" role="tabpanel" data-panel="<?= $key ?>" <?= $key === 'terbaru' ? '' : 'hidden' ?>>
            <?php if (empty($list)) : ?>
                <p class="showcase-empty">Belum ada data untuk ditampilkan.</p>
            <?php endif; ?>
            <?php foreach ($list as $index => $book) : ?>
                <article class="showcase-card">
                    <div class="showcase-cover">
                        <?php if ($key === 'populer') : ?><span class="showcase-rank">#<?= $index + 1 ?></span><?php endif; ?>
                        <?php if (landing_has_cover($book)) : ?>
                            <img src="<?= get_catalog_thumb_url($book->CoverURL, 300, 450) ?>" alt="" loading="lazy" onerror="this.onerror=null;this.src='<?= $defaultCover ?>'">
                        <?php else :
                            $gaya     = landing_cover_style($book);
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
                                <?= landing_icon('katalog') ?> <span><?= (int) $book->Loans ?> kali dipinjam</span>
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
