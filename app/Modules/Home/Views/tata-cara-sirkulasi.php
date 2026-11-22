<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    /* Gaya sama dengan beranda OPAC: latar putih, serif Source Serif 4, biru #0f3fb4, sudut kecil, tanpa bayangan.
       Teks #151922 dan #5b6272 di atas putih: kontras di atas 4,5:1. */
    .sirkulasi {
        --sk-biru: #0f3fb4;
        --sk-judul: #0e1a3a;
        --sk-teks: #151922;
        --sk-redup: #5b6272;
        --sk-garis: #d9dfea;
        background: #fff;
        padding: 112px 0 72px;
    }
    .sirkulasi .wadah { max-width: 960px; margin: 0 auto; padding: 0 20px; }
    .sirkulasi :focus-visible { outline: 2px solid var(--sk-biru); outline-offset: 3px; }

    .sirkulasi h1, .sirkulasi h2, .sirkulasi .angka, .sirkulasi .no-langkah {
        font-family: 'Source Serif 4', Georgia, serif;
    }
    .sirkulasi h1 {
        margin: 0; font-size: clamp(28px, 3.4vw, 40px); font-weight: 600;
        line-height: 1.2; letter-spacing: -.015em; color: var(--sk-judul);
    }
    .sirkulasi .intro { max-width: 60ch; margin: 12px 0 0; font-size: 1.0625rem; color: var(--sk-redup); }

    .sirkulasi section { margin-top: 56px; }
    .sirkulasi h2 { margin: 0 0 20px; font-size: 1.5rem; font-weight: 600; line-height: 1.3; color: var(--sk-judul); }
    .sirkulasi p { color: var(--sk-teks); }
    .sirkulasi .judul-butir { margin: 0 0 4px; font-weight: 700; color: var(--sk-judul); }
    .sirkulasi .isi-butir { margin: 0; color: var(--sk-redup); line-height: 1.6; }

    /* Batas pinjam: angkanya yang jadi pusat perhatian */
    .sirkulasi .kartu-aturan { height: 100%; padding: 24px; background: #fff; border: 1px solid var(--sk-garis); border-radius: 8px; }
    .sirkulasi .kartu-aturan h3 { margin: 0 0 20px; font-size: 1rem; font-weight: 700; color: var(--sk-judul); }
    .sirkulasi .kartu-aturan dl { display: flex; gap: 24px; margin: 0; }
    .sirkulasi .kartu-aturan dl > div { display: flex; flex-direction: column-reverse; flex: 1; }
    .sirkulasi .kartu-aturan dt { font-size: .875rem; font-weight: 500; color: var(--sk-redup); }
    .sirkulasi .angka { margin: 0 0 4px; font-size: 2.75rem; font-weight: 600; line-height: 1; color: var(--sk-biru); font-variant-numeric: tabular-nums; }

    /* Syarat: tiga butir polos, dibatasi garis tipis di atas dan bawah */
    .sirkulasi .syarat { padding: 32px 0; border-top: 1px solid var(--sk-garis); border-bottom: 1px solid var(--sk-garis); }
    .sirkulasi .syarat h2 { margin-bottom: 24px; }
    .sirkulasi .tautan-katalog {
        display: inline-flex; align-items: center; min-height: 44px; margin-top: 4px;
        font-weight: 600; color: var(--sk-biru); text-decoration: underline; text-underline-offset: 3px;
    }
    .sirkulasi .tautan-katalog:hover { color: #0a2f8c; }

    /* Nomor langkah: urutannya informasi, jadi nomornya dibuat jelas */
    .sirkulasi .no-langkah {
        display: inline-flex; align-items: center; justify-content: center;
        width: 40px; height: 40px; flex-shrink: 0; border-radius: 6px;
        background: var(--sk-biru); color: #fff; font-size: 1.125rem; font-weight: 600;
    }
    .sirkulasi .alur { list-style: none; padding: 0; margin: 0; display: grid; gap: 28px; }
    .sirkulasi .alur li { padding-top: 20px; border-top: 2px solid var(--sk-biru); }
    .sirkulasi .alur .no-langkah { margin-bottom: 12px; }
    @media (min-width: 768px) {
        .sirkulasi .alur { grid-template-columns: repeat(3, 1fr); gap: 32px; }
    }

    .sirkulasi .catatan { margin: 0; padding: 16px 20px; background: #f4f6fb; border: 1px solid var(--sk-garis); border-radius: 8px; color: var(--sk-teks); }
    .sirkulasi .langkah { list-style: none; padding: 0; margin: 0; }
    .sirkulasi .langkah li { display: flex; gap: 16px; }
    .sirkulasi .langkah li + li { margin-top: 24px; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="sirkulasi">
<div class="wadah">

    <h1>Tata cara sirkulasi</h1>
    <p class="intro">Berapa buku yang bisa Anda pinjam dan untuk berapa lama, apa syaratnya, dan apa yang perlu dilakukan kalau koleksi hilang atau rusak.</p>

    <section aria-labelledby="batas-pinjam">
        <h2 id="batas-pinjam">Batas peminjaman</h2>
        <?php if (empty($aturanPinjam)) : ?>
            <p>Aturan peminjaman belum tersedia. Tanyakan ke petugas sirkulasi.</p>
        <?php else : ?>
            <div class="row g-3">
                <?php foreach ($aturanPinjam as $aturan) : ?>
                    <div class="col-md-6">
                        <div class="kartu-aturan">
                            <h3>Anggota <?= esc(trim($aturan->jenisanggota)) ?></h3>
                            <dl>
                                <div>
                                    <dt>eksemplar sekaligus</dt>
                                    <dd class="angka"><?= (int) $aturan->MaxPinjamKoleksi ?></dd>
                                </div>
                                <div>
                                    <dt>hari per peminjaman</dt>
                                    <dd class="angka"><?= (int) $aturan->MaxLoanDays ?></dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>

    <section aria-labelledby="syarat-pinjam" class="syarat">
        <h2 id="syarat-pinjam">Syarat meminjam</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <p class="judul-butir">Keanggotaan aktif</p>
                <p class="isi-butir">Status Anda aktif dan masa berlaku kartu belum habis.</p>
            </div>
            <div class="col-md-4">
                <p class="judul-butir">Tidak sedang ditangguhkan</p>
                <p class="isi-butir">Anda tidak punya penangguhan karena terlambat atau melanggar aturan sebelumnya.</p>
            </div>
            <div class="col-md-4">
                <p class="judul-butir">Koleksi tersedia</p>
                <p class="isi-butir">Koleksi berstatus tersedia dan boleh dipinjam.</p>
                <a class="tautan-katalog" href="<?= base_url('opac') ?>">Cek ketersediaan di katalog</a>
            </div>
        </div>
    </section>

    <?php /* TODO sebelum merge: dua section di bawah diadaptasi dari SOP Perpustakaan BPJS Ketenagakerjaan.
              Konfirmasi ke mentor: jumlah perpanjangan (jenis_anggota.CountPerpanjang masih 0 untuk ASN & Non ASN),
              batas waktu penggantian 6 hari kerja, dan ketentuan buku pengganti. */ ?>
    <section aria-labelledby="alur-pinjam">
        <h2 id="alur-pinjam">Dari pinjam sampai kembali</h2>
        <ol class="alur">
            <li>
                <span class="no-langkah">1</span>
                <p class="judul-butir">Pinjam</p>
                <p class="isi-butir">Koleksi dicatat atas nama Anda. Tanggal jatuh tempo dihitung dari lama pinjam untuk jenis keanggotaan Anda.</p>
            </li>
            <li>
                <span class="no-langkah">2</span>
                <p class="judul-butir">Perpanjang, kalau perlu</p>
                <p class="isi-butir">Ajukan sebelum jatuh tempo. Perpanjangan hanya bisa satu kali.</p>
            </li>
            <li>
                <span class="no-langkah">3</span>
                <p class="judul-butir">Kembalikan</p>
                <p class="isi-butir">Kembalikan paling lambat pada tanggal jatuh tempo, dalam kondisi yang sama seperti saat dipinjam. Anda baru bisa meminjam lagi setelah koleksi sebelumnya kembali.</p>
            </li>
        </ol>
    </section>

    <section aria-labelledby="hilang-rusak">
        <div class="row g-4 g-md-5">
            <div class="col-md-5">
                <h2 id="hilang-rusak">Kalau koleksi hilang atau rusak</h2>
                <p>Anda bertanggung jawab atas koleksi selama dipinjam.</p>
                <p class="catatan" role="note">Kerusakan berat (sobek, basah, halaman hilang, atau dicoret) diperlakukan sama dengan kehilangan dan wajib diganti.</p>
            </div>
            <div class="col-md-7">
                <ol class="langkah">
                    <li>
                        <span class="no-langkah">1</span>
                        <div>
                            <p class="judul-butir">Laporkan segera</p>
                            <p class="isi-butir">Hubungi petugas sirkulasi begitu Anda tahu koleksi hilang atau rusak.</p>
                        </div>
                    </li>
                    <li>
                        <span class="no-langkah">2</span>
                        <div>
                            <p class="judul-butir">Siapkan pengganti dalam 6 hari kerja</p>
                            <p class="isi-butir">Dihitung sejak tanggal laporan.</p>
                        </div>
                    </li>
                    <li>
                        <span class="no-langkah">3</span>
                        <div>
                            <p class="judul-butir">Serahkan buku pengganti</p>
                            <p class="isi-butir">Berikan buku baru dengan judul, pengarang, dan edisi yang sama. Kalau buku itu tidak dijual lagi, berikan edisi terbaru atau judul lain yang ditentukan pustakawan.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </section>

</div>
</div>
<?= $this->endSection() ?>
