<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    .opac-anggota-shell {
        max-width: 460px;
        margin: 0 auto;
        padding-top: calc(var(--nav-h, 68px) + 24px);
        padding-bottom: 64px;
    }
    .opac-anggota-shell .form-control {
        font-size: 1rem;
        min-height: 44px;
    }
    .opac-anggota-shell .btn {
        min-height: 44px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .opac-anggota-shell .opac-staff-link {
        display: inline-flex;
        align-items: center;
        min-height: 44px;
    }
    @media (max-width: 767.98px) {
        .opac-anggota-shell {
            padding-bottom: calc(96px + env(safe-area-inset-bottom, 0px));
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container px-3 opac-anggota-shell">
    <h1 class="h3 fw-bold mb-2">Masuk sebagai anggota</h1>
    <p class="text-secondary mb-4">Dengan masuk, kamu bisa mendapat saran buku dari riwayat pinjammu.</p>

    <?php if (session('error')): ?>
        <div class="alert alert-danger" role="alert"><?= esc(session('error')) ?></div>
    <?php endif; ?>

    <form method="post" action="<?= base_url('opac/masuk') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label for="username" class="form-label">Nomor anggota</label>
            <input type="text" id="username" name="username" class="form-control"
                   value="<?= esc(old('username')) ?>" autocomplete="username" required autofocus>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Kata sandi</label>
            <input type="password" id="password" name="password" class="form-control"
                   autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn btn-brand w-100">Masuk</button>
    </form>

</div>
<?= $this->endSection() ?>
