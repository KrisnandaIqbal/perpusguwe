<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    /* Banner biru + satu kartu putih berisi daftar bernomor. Biru #0f3fb4 sama dengan beranda OPAC.
       Teks #1c2230 di atas putih: kontras di atas 4,5:1. */
    .sirkulasi { background: #f3f6fb; padding: 112px 0 80px; }
    .sirkulasi .wadah { max-width: 1120px; margin: 0 auto; padding: 0 20px; }
    .sirkulasi :focus-visible { outline: 2px solid #0f3fb4; outline-offset: 3px; }

    .sirkulasi .banner {
        position: relative; overflow: hidden; padding: 48px; border-radius: 24px;
        background: #0f3fb4; color: #fff;
    }
    .sirkulasi .banner::before, .sirkulasi .banner::after { content: ""; position: absolute; border-radius: 50%; }
    .sirkulasi .banner::before { right: -60px; top: -90px; width: 320px; height: 320px; background: #2c5ad1; opacity: .55; }
    .sirkulasi .banner::after { right: 150px; bottom: -130px; width: 240px; height: 240px; background: #1d4bc4; opacity: .7; }
    .sirkulasi .banner h1, .sirkulasi .banner p { position: relative; }
    .sirkulasi .banner h1 { margin: 0; font-size: clamp(30px, 4vw, 44px); font-weight: 700; line-height: 1.15; color: #fff; }
    .sirkulasi .banner p { margin: 10px 0 0; font-size: 1.125rem; color: #dfe8ff; }

    .sirkulasi .kartu { margin-top: 32px; padding: 48px 56px; background: #fff; border-radius: 24px; font-size: 1.0625rem; line-height: 1.75; color: #1c2230; }
    .sirkulasi .kartu h2 { margin: 48px 0 16px; font-size: 1.25rem; font-weight: 700; color: #1c2230; }
    .sirkulasi .kartu h2:first-child { margin-top: 0; }
    .sirkulasi .kartu ul, .sirkulasi .kartu ol { margin: 0; padding-left: 24px; }
    .sirkulasi .kartu li + li { margin-top: 6px; }
    .sirkulasi .kartu a { font-weight: 600; color: #0f3fb4; text-decoration: underline; text-underline-offset: 3px; }
    .sirkulasi .kartu a:hover { color: #0a2f8c; }
    .sirkulasi .perhatian { margin: 32px 0 0; padding: 16px 20px; background: #fff6d6; border: 1px solid #f0d77a; border-radius: 12px; }

    @media (max-width: 575px) {
        .sirkulasi .banner { padding: 32px 24px; border-radius: 20px; }
        .sirkulasi .kartu { padding: 32px 24px; border-radius: 20px; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="sirkulasi">
<div class="wadah">

    <header class="banner">
        <h1>Tata Cara Sirkulasi</h1>
        <p>Panduan pinjam dan kembali koleksi.</p>
    </header>

    <article class="kartu">

        <h2>Batas peminjaman</h2>
        <?php if (empty($aturanPinjam)) : ?>
            <p>Aturan peminjaman belum tersedia. Tanyakan ke petugas sirkulasi.</p>
        <?php else : ?>
            <ul>
                <?php foreach ($aturanPinjam as $aturan) : ?>
                    <li>Anggota <strong><?= esc(trim($aturan->jenisanggota)) ?></strong>: <strong><?= (int) $aturan->MaxPinjamKoleksi ?> buku</strong> sekaligus, <strong><?= (int) $aturan->MaxLoanDays ?> hari</strong> per peminjaman.</li>
                <?php endforeach ?>
            </ul>
        <?php endif ?>

        <h2>Syarat meminjam</h2>
        <ol>
            <li>Keanggotaan <strong>aktif</strong> dan kartu belum kedaluwarsa.</li>
            <li><strong>Tidak sedang ditangguhkan</strong> akibat keterlambatan atau pelanggaran.</li>
            <li>Koleksi berstatus <strong>tersedia</strong>. <a href="<?= base_url('opac') ?>">Cek di katalog</a></li>
        </ol>

        <?php /* TODO sebelum merge: dua bagian di bawah diadaptasi dari SOP Perpustakaan BPJS Ketenagakerjaan.
                  Konfirmasi ke mentor: jumlah perpanjangan (jenis_anggota.CountPerpanjang masih 0 untuk ASN & Non ASN),
                  batas waktu penggantian 6 hari kerja, dan ketentuan buku pengganti. */ ?>
        <h2>Dari pinjam sampai kembali</h2>
        <ol>
            <li><strong>Cek ketersediaan</strong> koleksi di katalog.</li>
            <li><strong>Pinjam.</strong> Jatuh tempo mengikuti lama pinjam jenis keanggotaan Anda.</li>
            <li><strong>Perpanjang</strong> sebelum jatuh tempo, bila perlu. Hanya satu kali.</li>
            <li><strong>Kembalikan</strong> paling lambat saat jatuh tempo, dalam kondisi sama.</li>
            <li><strong>Pinjam lagi</strong> setelah koleksi sebelumnya kembali.</li>
        </ol>

        <h2>Koleksi hilang atau rusak</h2>
        <ol>
            <li><strong>Laporkan segera</strong> ke petugas sirkulasi.</li>
            <li>Siapkan pengganti dalam <strong>6 hari kerja</strong> sejak tanggal laporan.</li>
            <li>Serahkan buku dengan <strong>judul, pengarang, dan edisi sama</strong>.</li>
            <li>Jika sudah tidak dijual, berikan edisi terbaru atau judul lain pilihan pustakawan.</li>
        </ol>

        <p class="perhatian" role="note"><strong>Perhatian:</strong> rusak berat (sobek, basah, halaman hilang, dicoret) dianggap hilang dan wajib diganti.</p>

    </article>

</div>
</div>
<?= $this->endSection() ?>
