<?php
// app/Modules/SubModule/Eksemplar/Views/modal_form.php
// Form ringkas tambah/ubah eksemplar, dimuat via AJAX ke dalam modal
// di halaman edit katalog (tab EKSEMPLAR).

$isEdit = ($mode === 'edit');
$e = $eksemplar; // object saat edit, null saat create
?>
<form id="formEksemplarModal" autocomplete="off"
    action="<?= $isEdit ? base_url('eksemplar/edit/' . $e->ID) : base_url('eksemplar/create') ?>"
    method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <input type="hidden" name="Catalog_id" value="<?= esc($catalog_id) ?>">
    <?php if ($isEdit) : ?>
        <input type="hidden" name="_id" value="<?= esc($e->ID) ?>">
    <?php endif; ?>

    <?php if (!$isEdit) : ?>
        <div class="form-group">
            <label>Jumlah Eksemplar</label>
            <input type="number" min="1" max="50" class="form-control" name="JumlahEksemplar" value="1" required>
            <small class="text-muted">No. Induk &amp; No. Barcode dibuat otomatis (format NAKER...).</small>
        </div>
    <?php else : ?>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>No. Induk</label>
                <input type="text" class="form-control" value="<?= esc($e->NoInduk) ?>" readonly>
            </div>
            <div class="form-group col-md-6">
                <label>No. Barcode</label>
                <input type="text" class="form-control" value="<?= esc($e->NomorBarcode) ?>" readonly>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Lokasi Perpustakaan</label>
            <select class="form-control" name="Location_Library_id" id="Location_Library_id">
                <option value="">-- Pilih --</option>
                <?php foreach ($locationLibraries as $row) : ?>
                    <option value="<?= $row->ID ?>" <?= ($isEdit && $e->Location_Library_id == $row->ID) ? 'selected' : '' ?>>
                        <?= esc($row->Name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label>Lokasi Ruang</label>
            <select class="form-control" name="Location_id" id="Location_id">
                <option value="">-- Pilih Lokasi Perpustakaan Dulu --</option>
                <?php if ($isEdit && !empty($locations)) : foreach ($locations as $row) : ?>
                    <option value="<?= $row->ID ?>" <?= ($e->Location_id == $row->ID) ? 'selected' : '' ?>>
                        <?= esc($row->Name) ?>
                    </option>
                <?php endforeach;
                endif; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Bentuk Fisik <span class="text-danger">*</span></label>
            <select class="form-control" name="Media_id" required>
                <option value="">-- Pilih --</option>
                <?php foreach ($medias as $row) : ?>
                    <option value="<?= $row->ID ?>" <?= ($isEdit && $e->Media_id == $row->ID) ? 'selected' : '' ?>>
                        <?= esc($row->Name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label>Kategori Koleksi <span class="text-danger">*</span></label>
            <select class="form-control" name="Category_id" required>
                <option value="">-- Pilih --</option>
                <?php foreach ($categories as $row) : ?>
                    <option value="<?= $row->ID ?>" <?= ($isEdit && $e->Category_id == $row->ID) ? 'selected' : '' ?>>
                        <?= esc($row->Name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label>Sumber Pengadaan</label>
            <select class="form-control" name="Source_id">
                <option value="">-- Pilih --</option>
                <?php foreach ($sources as $row) : ?>
                    <option value="<?= $row->ID ?>" <?= ($isEdit && $e->Source_id == $row->ID) ? 'selected' : '' ?>>
                        <?= esc($row->Name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-6">
            <label>Tanggal Pengadaan</label>
            <input type="date" class="form-control" name="TanggalPengadaan"
                value="<?= ($isEdit && !empty($e->TanggalPengadaan)) ? date('Y-m-d', strtotime($e->TanggalPengadaan)) : date('Y-m-d') ?>">
        </div>
    </div>

    <?php if ($isEdit) : ?>
        <div class="form-group">
        <label>Status</label>
        <select class="form-control" name="Status_id">
            <option value="">-- Pilih --</option>
            <?php foreach ($statuses as $row) : ?>
                <?php
                    $isSelected = $isEdit
                        ? ($e->Status_id == $row->ID)
                        : (strtolower($row->Name) === 'tersedia');
                ?>
                <option value="<?= $row->ID ?>" <?= $isSelected ? 'selected' : '' ?>>
                    <?= esc($row->Name) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <label>No. Panggil</label>
        <input type="text" class="form-control" name="CallNumber" value="<?= $isEdit ? esc($e->CallNumber) : '' ?>">
    </div>

    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="ISOPAC" name="ISOPAC" value="1"
            <?= (!$isEdit || $e->ISOPAC == 1) ? 'checked' : '' ?>>
        <label class="form-check-label" for="ISOPAC">Tampil di OPAC</label>
    </div>
</form>

<script>
    (function () {
        var $libSel = $('#formEksemplarModal #Location_Library_id');
        var $locSel = $('#formEksemplarModal #Location_id');

        $libSel.off('change.eksemplarModal').on('change.eksemplarModal', function () {
            var libId = $(this).val();
            if (!libId) {
                $locSel.html('<option value="">-- Pilih Lokasi Perpustakaan Dulu --</option>');
                return;
            }
            $locSel.html('<option value="">Memuat...</option>');
            $.get('<?= base_url('api/eksemplar/locations') ?>/' + libId, function (res) {
                var opts = '<option value="">-- Pilih --</option>';
                (res.data || []).forEach(function (item) {
                    opts += '<option value="' + item.code + '">' + item.name + '</option>';
                });
                $locSel.html(opts);
            });
        });
    })();
</script>