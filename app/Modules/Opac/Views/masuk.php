<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('content') ?>
<main class="container" style="padding-top: 130px; padding-bottom: 64px; max-width: 460px;">
    <h1 class="h3 fw-bold mb-2">Masuk sebagai anggota</h1>
    <p class="text-secondary mb-4">Dengan masuk, kamu bisa mendapat saran buku dari riwayat pinjammu.</p>

    <?php if (session('error')): ?>
        <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('opac/masuk') ?>">
        <div class="mb-3">
            <label for="username" class="form-label">Nomor anggota</label>
            <input type="text" id="username" name="username" class="form-control" style="min-height: 44px;"
                   value="<?= esc(old('username')) ?>" autocomplete="username" required autofocus>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Kata sandi</label>
            <input type="password" id="password" name="password" class="form-control" style="min-height: 44px;"
                   autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-brand w-100" style="min-height: 44px;">Masuk</button>
    </form>

    <p class="mt-4 mb-0 text-secondary small">
        Petugas perpustakaan? <a href="<?= base_url('login') ?>">Masuk ke dasbor</a>.
    </p>
</main>
<?= $this->endSection() ?>
