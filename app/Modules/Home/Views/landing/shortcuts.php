<?php
// Kartu "Akses cepat layanan". Daftar layanannya ada di landing_services() (Helpers/landing_helper.php).
$shortcuts = landing_shortcuts();
?>
<section class="shortcuts" id="layanan" aria-labelledby="shortcut-title">
    <div class="shortcuts-head">
        <h2 id="shortcut-title">Akses cepat layanan</h2>
    </div>
    <div class="shortcut-grid">
        <?php foreach ($shortcuts as $s) : ?>
            <a href="<?= $s['url'] ?>" class="shortcut-card">
                <span class="shortcut-top">
                    <span class="shortcut-icon"><?= $s['icon'] ?></span>
                </span>
                <strong><?= esc($s['title']) ?></strong>
                <small><?= esc($s['short']) ?></small>
                <span class="shortcut-go" aria-hidden="true"><?= landing_icon('right') ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>
