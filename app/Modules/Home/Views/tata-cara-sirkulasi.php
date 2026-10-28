<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    .sirkulasi { margin-top: 100px; margin-bottom: 4rem; }
    .sirkulasi .intro { max-width: 60ch; }
    .sirkulasi section { margin-top: 3.5rem; }
    .sirkulasi h2 { color: var(--slate-900); margin-bottom: 1.25rem; }

    /* Kartu aturan per jenis anggota: angkanya yang jadi pusat perhatian */
    .sirkulasi .kartu-aturan { background: #fff; }
    .sirkulasi .angka {
        font-size: 2.5rem; font-weight: 700; line-height: 1;
        color: var(--brand-600); font-variant-numeric: tabular-nums;
    }
    .sirkulasi .kartu-aturan dt { font-weight: 500; color: var(--slate-800); font-size: .875rem; }

    .sirkulasi .panel-syarat { background: var(--brand-50); }

    /* Nomor langkah: urutannya informasi, jadi nomornya dibuat jelas */
    .sirkulasi .no-langkah {
        display: inline-flex; align-items: center; justify-content: center;
        width: 2rem; height: 2rem; flex-shrink: 0; border-radius: 50%;
        background: var(--brand-600); color: #fff; font-weight: 600;
    }
    .sirkulasi .alur { list-style: none; padding: 0; margin: 0; display: grid; gap: 1.5rem; }
    .sirkulasi .alur li { border-top: 2px solid var(--brand-100); padding-top: 1rem; }
    @media (min-width: 768px) {
        .sirkulasi .alur { grid-template-columns: repeat(3, 1fr); }
    }
    .sirkulasi .langkah { list-style: none; padding: 0; margin: 0; }
    .sirkulasi .langkah li { display: flex; gap: 1rem; }
    .sirkulasi .langkah li + li { margin-top: 1.25rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container sirkulasi">

    <h1 class="h2 fw-bold mb-3">Tata Cara Sirkulasi</h1>
    <p class="lead intro">Berapa banyak dan berapa lama Anda bisa meminjam, apa saja syaratnya, dan apa yang harus dilakukan kalau koleksi hilang atau rusak.</p>

    <section aria-labelledby="batas-pinjam">
        <h2 id="batas-pinjam" class="h4 fw-bold">Batas peminjaman</h2>
        <?php if (empty($aturanPinjam)) : ?>
            <p>Aturan peminjaman belum tersedia. Silakan tanyakan ke petugas sirkulasi.</p>
        <?php else : ?>
            <div class="row g-3">
                <?php foreach ($aturanPinjam as $aturan) : ?>
                    <div class="col-md-6">
                        <div class="card kartu-aturan h-100">
                            <div class="card-body p-4">
                                <h3 class="h6 fw-bold mb-4">Anggota <?= esc(trim($aturan->jenisanggota)) ?></h3>
                                <dl class="row mb-0">
                                    <div class="col-6 d-flex flex-column-reverse">
                                        <dt>eksemplar sekaligus</dt>
                                        <dd class="angka mb-2"><?= (int) $aturan->MaxPinjamKoleksi ?></dd>
                                    </div>
                                    <div class="col-6 d-flex flex-column-reverse">
                                        <dt>hari masa pinjam</dt>
                                        <dd class="angka mb-2"><?= (int) $aturan->MaxLoanDays ?></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </section>

    <section aria-labelledby="syarat-pinjam">
        <div class="panel-syarat rounded-3 p-4 p-md-5">
            <h2 id="syarat-pinjam" class="h4 fw-bold">Syarat meminjam</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <p class="fw-bold mb-1">Keanggotaan aktif</p>
                    <p class="mb-0">Status anggota aktif dan masa berlaku kartu belum habis.</p>
                </div>
                <div class="col-md-4">
                    <p class="fw-bold mb-1">Tidak sedang di-suspend</p>
                    <p class="mb-0">Tidak ada penangguhan karena keterlambatan atau pelanggaran sebelumnya.</p>
                </div>
                <div class="col-md-4">
                    <p class="fw-bold mb-1">Koleksi tersedia</p>
                    <p class="mb-0">Koleksi berstatus tersedia dan termasuk koleksi yang boleh dipinjam.</p>
                </div>
            </div>
        </div>
    </section>

    <?php /* TODO sebelum merge: dua section di bawah diadaptasi dari SOP Perpustakaan BPJS Ketenagakerjaan.
              Konfirmasi ke mentor: jumlah perpanjangan (jenis_anggota.CountPerpanjang masih 0 untuk ASN & Non ASN),
              batas waktu penggantian 6 hari kerja, dan ketentuan buku pengganti. */ ?>
    <section aria-labelledby="alur-pinjam">
        <h2 id="alur-pinjam" class="h4 fw-bold">Dari pinjam sampai kembali</h2>
        <ol class="alur">
            <li>
                <span class="no-langkah mb-3">1</span>
                <p class="fw-bold mb-1">Pinjam</p>
                <p class="mb-0">Koleksi dicatat atas nama Anda. Tanggal jatuh tempo dihitung dari masa pinjam jenis keanggotaan Anda.</p>
            </li>
            <li>
                <span class="no-langkah mb-3">2</span>
                <p class="fw-bold mb-1">Perpanjang, kalau perlu</p>
                <p class="mb-0">Ajukan sebelum jatuh tempo. Perpanjangan hanya berlaku satu kali.</p>
            </li>
            <li>
                <span class="no-langkah mb-3">3</span>
                <p class="fw-bold mb-1">Kembalikan</p>
                <p class="mb-0">Paling lambat pada tanggal jatuh tempo, dalam kondisi sama seperti saat dipinjam. Peminjaman baru bisa dilakukan setelah koleksi sebelumnya kembali.</p>
            </li>
        </ol>
    </section>

    <section aria-labelledby="hilang-rusak">
        <div class="card">
            <div class="card-body p-4 p-md-5">
                <div class="row g-4 g-md-5">
                    <div class="col-md-5">
                        <h2 id="hilang-rusak" class="h4 fw-bold">Kalau koleksi hilang atau rusak</h2>
                        <p>Anda bertanggung jawab atas koleksi selama dipinjam.</p>
                        <div class="alert alert-warning mb-0" role="note">
                            Kerusakan berat (sobek, basah, halaman hilang, atau dicoret) diperlakukan sama dengan kehilangan dan wajib diganti.
                        </div>
                    </div>
                    <div class="col-md-7">
                        <ol class="langkah">
                            <li>
                                <span class="no-langkah">1</span>
                                <div>
                                    <p class="fw-bold mb-1">Laporkan segera</p>
                                    <p class="mb-0">Hubungi petugas sirkulasi begitu Anda tahu koleksi hilang atau rusak.</p>
                                </div>
                            </li>
                            <li>
                                <span class="no-langkah">2</span>
                                <div>
                                    <p class="fw-bold mb-1">Siapkan pengganti dalam 6 hari kerja</p>
                                    <p class="mb-0">Dihitung sejak tanggal laporan.</p>
                                </div>
                            </li>
                            <li>
                                <span class="no-langkah">3</span>
                                <div>
                                    <p class="fw-bold mb-1">Serahkan buku pengganti</p>
                                    <p class="mb-0">Buku baru yang sama (judul, pengarang, edisi). Kalau sudah tidak dijual, edisi terbaru atau judul lain yang ditentukan pustakawan.</p>
                                </div>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
<?= $this->endSection() ?>
