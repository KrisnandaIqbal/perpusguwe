<?php
// Berita terbaru: satu berita utama + tiga berita berikutnya. Data dari Home::getNews() (t_berita aktif, terbaru dulu).
$berita = $news ?? [];
$bulan  = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tanggal = static fn ($t) => date('j', strtotime($t)) . ' ' . $bulan[(int) date('n', strtotime($t))] . ' ' . date('Y', strtotime($t));
$ringkas = static fn ($html, $n) => mb_strimwidth(trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $html)))), 0, $n, '…');
$url     = static fn ($b) => base_url('news/detail/' . $b['id'] . '/' . $b['slug']);
$cover   = static fn ($b) => !empty($b['file_cover']) ? base_url('uploads/berita/' . $b['file_cover']) : '';
?>
<section class="news" id="berita">
    <div class="section-head">
        <div>
            <h2>Berita &amp; pengumuman.</h2>
        </div>
        <a class="news-all" href="<?= base_url('news') ?>">Semua berita</a>
    </div>

    <?php if (empty($berita)) : ?>
        <p class="showcase-empty">Belum ada berita yang dipublikasikan.</p>
    <?php else : $utama = $berita[0]; ?>
        <div class="news-layout">
            <a class="news-main" href="<?= $url($utama) ?>">
                <span class="news-main-img">
                    <?php if ($cover($utama)) : ?>
                        <img src="<?= $cover($utama) ?>" alt="" loading="lazy" onerror="this.remove()">
                    <?php endif; ?>
                </span>
                <time datetime="<?= esc(date('Y-m-d', strtotime($utama['created_at']))) ?>"><?= $tanggal($utama['created_at']) ?></time>
                <h3><?= esc($utama['title']) ?></h3>
                <p><?= esc($ringkas($utama['content'], 170)) ?></p>
            </a>

            <div class="news-list">
                <?php foreach (array_slice($berita, 1, 3) as $b) : ?>
                    <a class="news-item" href="<?= $url($b) ?>">
                        <span class="news-item-img">
                            <?php if ($cover($b)) : ?>
                                <img src="<?= $cover($b) ?>" alt="" loading="lazy" onerror="this.remove()">
                            <?php endif; ?>
                        </span>
                        <span class="news-item-text">
                            <time datetime="<?= esc(date('Y-m-d', strtotime($b['created_at']))) ?>"><?= $tanggal($b['created_at']) ?></time>
                            <h3><?= esc($b['title']) ?></h3>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</section>
