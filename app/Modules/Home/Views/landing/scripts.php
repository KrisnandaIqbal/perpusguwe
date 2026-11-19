<?php
// Data untuk JavaScript beranda (window.HOME_DATA) dan daftar berkas JS.
// Urutan berkas penting: util.js dulu karena dipakai berkas lain.
$layanan = array_map(
    static fn($s) => array_intersect_key($s, array_flip(['id', 'title', 'short', 'url', 'keywords', 'icon'])),
    landing_shortcuts()
);
$berkasJs = ['util', 'layout', 'hero', 'hero-carousel', 'showcase', 'omnisearch', 'rules-modal'];
?>
<script>
    window.HOME_DATA = {
        opacUrl: <?= json_encode(base_url('opac')) ?>,
        searchUrl: <?= json_encode(base_url('home/search-books')) ?>,
        rulesUrl: <?= json_encode(landing_asset('tata-tertib.json')) ?>,
        detailUrl: <?= json_encode(base_url('opac/detail')) ?>,
        jdihUrl: <?= json_encode(landing_jdih_url()) ?>,
        services: <?= json_encode($layanan, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        arrow: <?= json_encode(landing_icon('up-right', 'omni-all-icon')) ?>
    };
</script>
<?php foreach ($berkasJs as $berkas) : ?>
    <script src="<?= landing_asset('js/' . $berkas . '.js') ?>"></script>
<?php endforeach; ?>
