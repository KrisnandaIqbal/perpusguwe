<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('page'); ?>
<?php
$link = function ($p) {
    return site_url('laporan-cetak-label') . '?' . http_build_query(array_merge($_GET, ['page' => $p]));
};
$from = ($total == 0) ? 0 : (($page - 1) * $per + 1);
$to   = min($page * $per, $total);
?>
<div class="card">
  <div class="card-header"><h5 class="mb-0">Laporan Cetak Label</h5></div>
  <div class="card-body">
    <form method="get" class="form-row align-items-end mb-3">
      <div class="col-auto"><label>Dari</label>
        <input type="date" name="dari" value="<?= esc($dari) ?>" class="form-control"></div>
      <div class="col-auto"><label>Sampai</label>
        <input type="date" name="sampai" value="<?= esc($sampai) ?>" class="form-control"></div>
      <div class="col-auto"><label>Petugas</label>
        <select name="user_id" class="form-control">
          <option value="">Semua</option>
          <?php foreach ($users as $u): ?>
            <option value="<?= $u['user_id'] ?>" <?= $user_id == $u['user_id'] ? 'selected' : '' ?>><?= esc($u['nama']) ?></option>
          <?php endforeach ?>
        </select></div>
      <div class="col-auto"><label>Tampilan</label>
        <select name="mode" class="form-control">
          <option value="asli" <?= $mode == 'asli' ? 'selected' : '' ?>>Apa adanya (seperti SLiMS)</option>
          <option value="unik" <?= $mode == 'unik' ? 'selected' : '' ?>>Unik per kode buku</option>
        </select></div>
      <div class="col-auto"><label>Per halaman</label>
        <select name="per" class="form-control">
          <?php foreach ($perOpts as $o): ?>
            <option value="<?= $o ?>" <?= $per == $o ? 'selected' : '' ?>><?= $o ?></option>
          <?php endforeach ?>
        </select></div>
      <div class="col-auto">
        <button class="btn btn-primary">Tampilkan</button>
        <a class="btn btn-success" href="<?= site_url('laporan-cetak-label/export') . '?' . http_build_query($_GET) ?>">Ekspor XLSX (semua baris)</a>
      </div>
    </form>

    <p>Menampilkan <?= $from ?>-<?= $to ?> dari <?= $total ?> baris</p>
    <div class="table-responsive">
      <table class="table table-bordered table-sm">
        <thead><tr><th>Waktu Cetak</th><th>Nama User</th><th>Kode Buku</th><th>Judul</th><th>Pengarang</th><th>Call Number</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr><td><?= esc($r['print_date']) ?></td><td><?= esc($r['nama_user']) ?></td>
              <td><?= esc($r['item_code']) ?></td><td><?= esc($r['title']) ?></td>
              <td><?= esc($r['author']) ?></td><td><?= esc($r['call_number']) ?></td></tr>
        <?php endforeach ?>
        </tbody>
      </table>
    </div>

    <?php if ($pages > 1): ?>
    <nav><ul class="pagination">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="<?= $link(max(1, $page - 1)) ?>">&laquo;</a></li>
      <?php for ($i = max(1, $page - 3); $i <= min($pages, $page + 3); $i++): ?>
        <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="<?= $link($i) ?>"><?= $i ?></a></li>
      <?php endfor ?>
      <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>"><a class="page-link" href="<?= $link(min($pages, $page + 1)) ?>">&raquo;</a></li>
    </ul></nav>
    <?php endif ?>
  </div>
</div>
<?= $this->endSection('page'); ?>
