<?php
// Footer publik: tentang perpustakaan, kontak, jam layanan, media sosial, dan tautan. Dipakai beranda dan layout OPAC.
// Isi tetapnya ada di landing_footer(). CSS: assets/home/footer.css.
helper('landing');
['alamat' => $alamat, 'surel' => $surel, 'jam' => $jamLayanan, 'sosial' => $sosial, 'direktori' => $direktori] = landing_footer();
$services = landing_services();
$opacUrl  = base_url('opac');
$_depan   = in_array(trim(current_url(true)->getPath(), '/'), ['', 'home', 'index.php'], true);
?>
<link rel="stylesheet" href="<?= landing_asset('footer.css') ?>">
<footer class="site-footer" id="kontak">
    <div class="footer-main">
        <div class="footer-about">
            <a href="<?= $_depan ? '#beranda' : base_url('home') ?>" class="footer-brand">
                <img src="<?= landing_asset('logo-kemnaker-white.png') ?>" alt="" width="40" height="40">
                <span>
                    <strong>Perpustakaan Kemnaker</strong>
                    <small>Kementerian Ketenagakerjaan Republik Indonesia</small>
                </span>
            </a>
            <p class="footer-description">Perpustakaan Kementerian Ketenagakerjaan Republik Indonesia berkomitmen menyediakan layanan perpustakaan yang berkualitas bagi seluruh pegawai dan masyarakat. Kami menghimpun koleksi buku, regulasi, laporan riset, dan publikasi ketenagakerjaan untuk mendukung perumusan kebijakan serta peningkatan literasi insan ketenagakerjaan Indonesia.</p>

            <ul class="footer-contact">
                <li><?= landing_icon('pin') ?><span><?= esc($alamat) ?></span></li>
                <li><?= landing_icon('mail') ?><a href="mailto:<?= esc($surel) ?>"><?= esc($surel) ?></a></li>
                <li>
                    <?= landing_icon('clock') ?>
                    <span>
                        <?php foreach ($jamLayanan as [$hari, $jam]) : ?>
                            <span class="footer-hours"><b><?= esc($hari) ?></b> <?= esc($jam) ?></span>
                        <?php endforeach; ?>
                    </span>
                </li>
            </ul>

            <div class="footer-socials">
                <?php foreach ($sosial as $id => [$nama, $url]) : ?>
                    <a href="<?= esc($url) ?>" target="_blank" rel="noreferrer" aria-label="<?= esc($nama) ?> Kemnaker"><?= landing_icon_filled($id) ?></a>
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
                <li><?php if ($_depan) : ?><button type="button" data-rules>Tata Tertib Keanggotaan</button><?php else : ?><a href="<?= base_url('home') ?>#tata-tertib">Tata Tertib Keanggotaan</a><?php endif; ?></li>
                <li><a href="<?= $services['tamu']['url'] ?>">Buku Tamu</a></li>
                <li><a href="mailto:<?= esc($surel) ?>?subject=Usulan%20Buku">Usulan Buku</a></li>
            </ul>
        </nav>

        <nav class="footer-col" aria-label="Direktori">
            <h2>Direktori</h2>
            <ul>
                <?php foreach ($direktori as [$nama, $url]) : ?>
                    <li><a href="<?= esc($url) ?>" target="_blank" rel="noreferrer"><?= esc($nama) ?><?= landing_icon('up-right', 'footer-ext') ?></a></li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>

    <div class="footer-bottom">
        <span>© <?= date('Y') ?> Perpustakaan Kementerian Ketenagakerjaan. Seluruh hak cipta dilindungi.</span>
        <span class="footer-mark">Kementerian Ketenagakerjaan · Republik Indonesia</span>
    </div>
</footer>
