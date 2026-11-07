<?php
// Kartu "Akses cepat layanan". Daftar layanannya ada di landing_services() (Helpers/landing_helper.php).
$shortcuts = landing_shortcuts();
?>
<section class="shortcuts" id="layanan" aria-labelledby="shortcut-title">
    <div class="shortcuts-head">
        <h2 id="shortcut-title">Akses cepat layanan</h2>
        <span><?= count($shortcuts) ?> layanan · langsung dari halaman ini</span>
    </div>
    <div class="shortcut-grid">
        <?php foreach ($shortcuts as $index => $s) : ?>
            <a href="<?= $s['url'] ?>" class="shortcut-card">
                <span class="shortcut-top">
                    <span class="shortcut-icon"><?= $s['icon'] ?></span>
                    <span class="shortcut-number"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                </span>
                <strong><?= esc($s['title']) ?></strong>
                <small><?= esc($s['short']) ?></small>
                <span class="shortcut-go">Buka <?= landing_icon('right') ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
