<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-note2 icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Riwayat Perubahan Data
                    <div class="page-title-subheading">Log seluruh aktivitas tambah/ubah data di sistem.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item" aria-current="page">Riwayat Perubahan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-body">

            <form method="get" class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Tabel</label>
                    <select name="table" class="form-control">
                        <option value="">-- Semua Tabel --</option>
                        <?php foreach ($tableList as $t): ?>
                            <option value="<?= esc($t['TableName']) ?>" <?= ($filterTable == $t['TableName']) ? 'selected' : '' ?>>
                                <?= esc($t['TableName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Aksi</label>
                    <select name="action" class="form-control">
                        <option value="">-- Semua --</option>
                        <option value="CREATE" <?= ($filterAction == 'CREATE') ? 'selected' : '' ?>>CREATE</option>
                        <option value="UPDATE" <?= ($filterAction == 'UPDATE') ? 'selected' : '' ?>>UPDATE</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-control" value="<?= esc($filterDateFrom) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-control" value="<?= esc($filterDateTo) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2"><i class="fa fa-filter"></i> Filter</button>
                    <a href="<?= base_url('riwayat-perubahan') ?>" class="btn btn-secondary">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Aksi</th>
                            <th>Tabel</th>
                            <th>ID Ref</th>
                            <th>Catatan</th>
                            <th>Dibuat Oleh</th>
                            <th>Diubah Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($history)): ?>
                            <?php foreach ($history as $row): ?>
                                <tr>
                                    <td><?= esc($row->CreateDate) ?></td>
                                    <td>
                                        <span class="badge <?= ($row->Action == 'CREATE') ? 'badge-success' : 'badge-warning' ?>">
                                            <?= esc($row->Action) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($row->TableName) ?></td>
                                    <td><?= esc($row->IDRef) ?></td>
                                    <td><?= esc($row->Note) ?></td>
                                    <td><?= esc($row->CreateByName) ?></td>
                                    <td><?= esc($row->UpdateByName) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-center">Belum ada riwayat perubahan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?= $pager->links() ?>

        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>