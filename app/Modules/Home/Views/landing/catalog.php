<?php
// Ajakan membuka katalog OPAC + daftar subjek terbanyak.
$opacUrl       = base_url('opac');
$totalCatalogs = (int) ($total_catalogs ?? 0);
$subjects      = $subjects ?? [];
?>
<section class="catalog-cta" id="katalog">
    <div class="catalog-cta-copy">
        <h2>Jelajahi <?= $totalCatalogs ? number_format($totalCatalogs, 0, ',', '.') . ' judul' : 'seluruh' ?> koleksi berdasarkan subjek.</h2>
        <p>Cari berdasarkan judul, penulis, atau kode buku, lalu cek nomor panggil dan ketersediaan eksemplar.</p>
        <a class="cta-button" href="<?= $opacUrl ?>">Buka katalog lengkap <?= landing_icon('right') ?></a>
    </div>
    <?php if (!empty($subjects)) : ?>
        <ul class="subject-list">
            <?php foreach ($subjects as $subjek) : ?>
                <li>
                    <a href="<?= $opacUrl ?>?Subject=<?= urlencode($subjek->Nama) ?>">
                        <span><?= esc($subjek->Nama) ?></span>
                        <small><?= number_format((int) $subjek->Jumlah, 0, ',', '.') ?> judul</small>
                        <?= landing_icon('up-right') ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>
