<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<main class="container" style="padding-top: 130px; padding-bottom: 64px; max-width: 640px;">
    <h1 class="h3 fw-bold mb-4">Profil</h1>

    <?php if (session('pesan')): ?>
        <div class="alert alert-success" role="status"><?= esc(session('pesan')) ?></div>
    <?php endif; ?>

    <dl class="row mb-5">
        <dt class="col-sm-4">Nama</dt>
        <dd class="col-sm-8"><?= esc($member->Fullname) ?></dd>
        <dt class="col-sm-4">Nomor anggota</dt>
        <dd class="col-sm-8"><?= esc($member->MemberNo) ?></dd>
        <dt class="col-sm-4">Unit kerja</dt>
        <dd class="col-sm-8"><?= esc($member->nama_unit ?: '-') ?></dd>
        <dt class="col-sm-4">Email</dt>
        <dd class="col-sm-8"><?= esc($member->Email ?: '-') ?></dd>
    </dl>

    <section aria-labelledby="judul-izin">
        <h2 id="judul-izin" class="h5 fw-bold">Saran buku dari riwayat pinjam</h2>
        <p class="text-secondary">
            Kami memakai riwayat pinjammu untuk menyarankan buku. Hanya kamu yang bisa melihatnya.
            Kalau dimatikan, kamu tetap mendapat buku populer di unit kerjamu dan buku baru.
        </p>
        <p class="fw-semibold">Sekarang: <?= $member->IzinPersonalisasi ? 'menyala' : 'mati' ?></p>
        <form method="post" action="<?= base_url('opac/profil/izin') ?>">
            <input type="hidden" name="izin" value="<?= $member->IzinPersonalisasi ? '0' : '1' ?>">
            <button type="submit" class="btn <?= $member->IzinPersonalisasi ? 'btn-outline-secondary' : 'btn-brand' ?>" style="min-height: 44px;">
                <?= $member->IzinPersonalisasi ? 'Matikan saran dari riwayat pinjam' : 'Nyalakan saran dari riwayat pinjam' ?>
            </button>
        </form>
    </section>

    <a href="<?= base_url('opac/keluar') ?>" class="btn btn-outline-secondary mt-5" style="min-height: 44px;">Keluar</a>
</main>
<?= $this->endSection() ?>
