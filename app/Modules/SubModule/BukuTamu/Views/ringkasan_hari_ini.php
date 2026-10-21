<?php
/**
 * Ringkasan kunjungan hari ini di halaman admin buku tamu.
 *
 * @var array $hari_ini dari BukuTamu::kunjunganHariIni()
 */
$hari_ini = $hari_ini ?? ['total' => 0, 'anggota' => 0, 'bukan_anggota' => 0, 'rombongan' => 0, 'rombongan_orang' => 0];
?>
<div class="main-card mb-3 card">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap align-items-baseline" style="gap: 8px 24px;">
            <div>
                <span class="text-muted">Total kunjungan hari ini</span>
                <strong style="font-size: 1.5rem; margin-left: 6px;"><?= (int) $hari_ini['total'] ?></strong>
                <span class="text-muted">orang</span>
            </div>
            <div class="text-muted">
                Anggota <?= (int) $hari_ini['anggota'] ?>
                · Bukan anggota <?= (int) $hari_ini['bukan_anggota'] ?>
                · Rombongan <?= (int) $hari_ini['rombongan'] ?> kelompok (<?= (int) $hari_ini['rombongan_orang'] ?> orang)
            </div>
        </div>
    </div>
</div>
