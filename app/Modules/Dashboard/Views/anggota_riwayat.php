<?php
/** Riwayat peminjaman anggota. Variabel: $riwayat (baris collectionloanitems + katalog, terbaru di atas). */
helper(['landing', 'dashboard_anggota']);

$tgl     = static fn (?string $t): string => $t ? date('d/m/Y', strtotime($t)) : '-';
$hariIni = strtotime('today');

// Sisa hari ke jatuh tempo; tanda bahaya mulai 3 hari sebelumnya.
foreach ($riwayat as &$r) {
    $r['kembali'] = !empty($r['ActualReturn']);
    $r['sisa']    = (!$r['kembali'] && !empty($r['DueDate'])) ? (int) round((strtotime($r['DueDate']) - $hariIni) / 86400) : null;
    $r['bahaya']  = $r['sisa'] !== null && $r['sisa'] <= 3;
}
unset($r);
$dipinjam = count(array_filter($riwayat, static fn ($r) => !$r['kembali']));
$bahaya   = count(array_filter($riwayat, static fn ($r) => $r['bahaya']));
$kembali  = count($riwayat) - $dipinjam;
$ringkas  = [
    ['Sedang dipinjam', $dipinjam, 'katalog', ''],
    ['Harus dikembalikan', $bahaya, 'unit', ' bahaya'],
    ['Sudah dikembalikan', $kembali, 'centang', ' ok'],
];
?>
<?= $this->extend('Dashboard\Views\anggota_kerangka') ?>

<?= $this->section('isi') ?>
<section class="dm-sec" aria-labelledby="judul-halaman">
    <div class="dm-sec-head">
        <h1 id="judul-halaman" class="dm-judul">Riwayat Peminjaman</h1>
    </div>
    <p class="dm-sub">Semua buku yang pernah kamu pinjam, dari yang terbaru.</p>
    <?php if (!$riwayat) : ?>
        <p class="dm-empty">Kamu belum pernah meminjam buku. Cari buku di katalog, lalu pinjam di meja layanan.</p>
    <?php else : ?>
        <div class="dm-pf-stats dm-rw-ringkas">
            <?php foreach ($ringkas as [$label, $jumlah, $ikon, $kelas]) : ?>
                <div class="dm-pf-stat<?= $kelas ?>">
                    <span class="dm-pf-ico"><?= $ikon === 'centang' ? '<svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>' : dm_ikon($ikon === 'unit' ? 'riwayat' : $ikon) ?></span>
                    <span><b><?= $jumlah ?></b><?= $label ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="dm-table-wrap">
            <table class="dm-table">
                <thead>
                    <tr><th scope="col">Buku</th><th scope="col">Dipinjam</th><th scope="col">Jatuh tempo</th><th scope="col">Dikembalikan</th><th scope="col">Status</th></tr>
                </thead>
                <tbody>
                <?php foreach ($riwayat as $r) :
                    $status = $r['kembali'] ? ['Dikembalikan', ''] : ($r['bahaya'] ? ['Harus dikembalikan', ' bahaya'] : ['Dipinjam', ' pinjam']);
                    $sisa   = $r['sisa'];
                    $ket    = $sisa === null ? '' : ($sisa < 0 ? 'Terlambat ' . abs($sisa) . ' hari' : ($sisa === 0 ? 'Hari ini' : $sisa . ' hari lagi'));
                    ?>
                    <tr<?= $r['bahaya'] ? ' class="bahaya"' : '' ?>>
                        <td class="dm-rw-kolom-buku">
                            <div class="dm-rw-buku">
                                <div class="dm-rw-cover"><?= dm_cover($r) ?></div>
                                <div>
                                    <a class="dm-rw-judul" href="<?= base_url('opac/detail/' . (int) $r['catalog_id']) ?>"><?= esc($r['Title']) ?></a>
                                    <small><?= esc(trim((string) $r['Author'], " ;,\t\n")) ?></small>
                                </div>
                            </div>
                        </td>
                        <td><?= $tgl($r['LoanDate']) ?></td>
                        <td<?= $r['bahaya'] ? ' class="bahaya"' : '' ?>><?= $tgl($r['DueDate']) ?><?= $ket ? '<small>' . $ket . '</small>' : '' ?></td>
                        <td><?= $tgl($r['ActualReturn']) ?></td>
                        <td><span class="dm-chip-inline<?= $status[1] ?>"><?= $status[0] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="dm-rw-catatan">Buku yang jatuh tempo dalam 3 hari atau sudah lewat ditandai merah. Kembalikan ke meja layanan sebelum tanggalnya lewat.</p>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>
