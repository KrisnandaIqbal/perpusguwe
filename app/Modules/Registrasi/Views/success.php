<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<style>
    .rf-success-wrap {
        background: var(--slate-50);
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 16px;
    }

    .rf-success-card {
        max-width: 520px;
        width: 100%;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 6px 20px rgba(0,0,0,0.03);
        padding: 48px 36px;
        text-align: center;
    }

    .rf-success-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #f0fdf4;
        border: 2px solid #bbf7d0;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        animation: rfPop 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes rfPop {
        0% { transform: scale(0.5); opacity: 0; }
        70% { transform: scale(1.06); }
        100% { transform: scale(1); opacity: 1; }
    }

    .rf-success-icon i {
        font-size: 24px;
        color: #16a34a;
    }

    .rf-success-card h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--slate-900);
        margin: 0 0 12px;
    }

    .rf-success-card p {
        font-size: 0.92rem;
        color: var(--slate-500);
        line-height: 1.6;
        margin: 0 0 32px;
    }

    .rf-success-card p strong {
        color: var(--brand-600);
    }

    .rf-success-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 44px;
        padding: 0 24px;
        background: var(--slate-900);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        font-family: inherit;
        text-decoration: none;
        transition: all 0.2s;
    }

    .rf-success-back:hover {
        background: #0c1322;
        color: #fff;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);
    }

    @media (max-width: 480px) {
        .rf-success-card {
            padding: 36px 24px;
        }
        .rf-success-card h1 {
            font-size: 1.3rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="rf-success-wrap">
    <div class="rf-success-card">
        <div class="rf-success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1>Pendaftaran Diterima</h1>
        <p>Data pendaftaran Anda telah kami terima dan saat ini <strong>menunggu verifikasi pustakawan</strong>. Notifikasi lebih lanjut akan dikirim melalui email yang Anda daftarkan.</p>
        <a href="<?= base_url('opac') ?>" class="rf-success-back">
            <i class="fas fa-arrow-left" style="font-size:0.78rem;"></i>
            Kembali ke Beranda
        </a>
    </div>
</div>

<?= $this->endSection() ?>
