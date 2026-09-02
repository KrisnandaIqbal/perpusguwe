<?php
$paper_size_config = [
    'xp420b-10x5' => [
        'label' => 'Xprinter XP-420B (10x5cm)', 'group' => 'Xprinter',
        'models' => [
            'cetak-label-xp420b-10x5' => 'Model 10x5 (Judul + Barcode + No. Panggil)',
        ],
    ],
    'a4' => [
        'label' => 'Kertas A4', 'group' => 'Kertas A4',
        'models' => [
            'cetak-label-a4-1' => 'Model A4-1 (No. Panggil + Barcode)',
            'cetak-label-a4-2' => 'Model A4-2 (No. Panggil + Barcode)',
            'cetak-label-a4-3' => 'Model A4-3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-a4-4' => 'Model A4-4 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-a4-5' => 'Model A4-5 (No. Panggil + Barcode)',
            'cetak-label-a4-4-qrcode' => 'Model A4-QR (QR Code + No. Panggil)',
        ],
    ],
    'label-roll' => [
        'label' => 'Kertas Label Roll', 'group' => 'Kertas Label Roll',
        'models' => [
            'cetak-label-lr1' => 'Model LR1 (No. Panggil + Barcode)',
            'cetak-label-lr2' => 'Model LR2 (No. Panggil + Barcode)',
            'cetak-label-lr3' => 'Model LR3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-lr4' => 'Model LR4 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-lr5' => 'Model LR5 (No. Panggil Tanpa Barcode)',
            'cetak-label-lr6' => 'Model LR6 (No. Panggil Tanpa Barcode + 1 Warna)',
        ],
    ],
    'barcode-roll' => [
        'label' => 'Kertas Barcode Roll', 'group' => 'Kertas Label Roll',
        'models' => [
            'cetak-label-br1' => 'Model BR1 (Barcode)',
            'cetak-label-br2' => 'Model BR2 (Barcode + Judul)',
        ],
    ],
    'label-tj107' => [
        'label' => 'Kertas Label Tom & Jerry 107', 'group' => 'Kertas Label Stiker',
        'models' => ['cetak-label-tj107-1' => 'Model TJ107-1 (Hanya Barcode)'],
    ],
    'label-tj121' => [
        'label' => 'Kertas Label Tom & Jerry 121', 'group' => 'Kertas Label Stiker',
        'models' => [
            'cetak-label-tj121-1' => 'Model TJ121-1 (No. Panggil + Barcode)',
            'cetak-label-tj121-2' => 'Model TJ121-2 (No. Panggil + Barcode)',
        ],
    ],
    'label-gc121' => [
        'label' => 'Kertas Label Golden Cock 121', 'group' => 'Kertas Label Stiker',
        'models' => [
            'cetak-label-gc121-1' => 'Model GC121-1 (No. Panggil + Barcode)',
            'cetak-label-gc121-2' => 'Model GC121-2 (No. Panggil + Barcode)',
            'cetak-label-gc121-3' => 'Model GC121-3 (No. Panggil + Barcode + 1 Warna)',
            'cetak-label-gc121-4' => 'Model GC121-4 (No. Panggil + Barcode + 1 Warna)',
        ],
    ],
];
$paper_size_json = json_encode($paper_size_config, JSON_UNESCAPED_UNICODE);
?>
<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-print icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Cetak Label
                    <div class="page-title-subheading">Cari koleksi dan cetak label tanpa membuka Daftar Eksemplar</div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-printer icon-gradient bg-plum-plate"></i>
            Pilih Koleksi untuk Dicetak
        </div>

        <div class="card-footer">
            <div class="d-flex align-items-center flex-wrap" style="gap:6px; padding: 8px 0 4px 0;">
                <div class="input-group" style="width:280px; flex-shrink:0;">
                    <div class="input-group-prepend"><span class="btn btn-secondary">Jenis Kertas</span></div>
                    <select class="form-control" id="paper_size" name="paper_size">
                        <option value="">-- Pilih Jenis Kertas --</option>
                        <optgroup label="Xprinter"><option value="xp420b-10x5">Xprinter XP-420B (10x5cm)</option></optgroup>
                        <optgroup label="Kertas A4"><option value="a4">Kertas A4</option></optgroup>
                        <optgroup label="Kertas Label Roll">
                            <option value="label-roll">Kertas Label Roll</option>
                            <option value="barcode-roll">Kertas Barcode Roll</option>
                        </optgroup>
                        <optgroup label="Kertas Label Stiker">
                            <option value="label-tj107">Tom &amp; Jerry 107</option>
                            <option value="label-tj121">Tom &amp; Jerry 121</option>
                            <option value="label-gc121">Golden Cock 121</option>
                        </optgroup>
                    </select>
                </div>
                <div class="input-group" id="label_model_wrapper" style="display:none; width:320px; flex-shrink:0;">
                    <div class="input-group-prepend"><span class="btn btn-secondary">Model Label</span></div>
                    <select class="form-control" id="label_model" name="label_model">
                        <option value="">-- Pilih Model --</option>
                    </select>
                </div>
                <button class="btn btn-primary" id="btnCetak" type="button" style="height:38px; flex-shrink:0;">
                    <i class="fa fa-print"></i> Cetak Label
                </button>
            </div>
        </div>

        <div class="card-body">
            <form name="form_items" id="form_items">
                <table style="width:100%;" id="tbl_data" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center" width="35">No</th>
                            <th class="text-center" width="35"><input type="checkbox" class="check_data" title="Pilih Semua"></th>
                            <th class="text-center" width="100">No. Barcode</th>
                            <th class="text-center" style="min-width: 220px;">Judul</th>
                            <th class="text-center" width="150">Pengarang</th>
                            <th class="text-center" width="80">Th. Terbit</th>
                            <th class="text-center" width="100">No. Panggil</th>
                            <th class="text-center" width="130">Tgl. Edit</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<script>
$(document).ready(function () {
    var paperSizeConfig = <?= $paper_size_json ?>;

    // Simpan koleksi terpilih di sini (persist lintas halaman datatable)
    // key = ID eksemplar, value = { title, order }
    var selectedItems = {};
    var selectOrder = 0;

    var t = $('#tbl_data').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        pageLength: 100,
        lengthMenu: [10, 25, 50, 100],
        order: [[7, 'desc']], // urut default berdasarkan Tgl. Edit terbaru
        ajax: { url: '<?= site_url('api/eksemplar/datatable_cetaklabel') ?>' },
        columns: [
            { data: 'no', className: 'text-center', searchable: false, orderable: false, render: function (data, type, row, meta) { return meta.settings._iDisplayStart + meta.row + 1; } },
            { data: 'ID', className: 'text-center', orderable: false },
            { data: 'NomorBarcode', className: 'text-left' },
            { data: 'Title' },
            { data: 'Author', className: 'text-left' },
            { data: 'PublishYear', className: 'text-center' },
            { data: 'CallNumber', className: 'text-center' },
            { data: 'UpdateDate', className: 'text-center' }
        ],
        drawCallback: function () {
            // setiap halaman baru dirender, centang ulang baris yang sudah dipilih sebelumnya
            $('#tbl_data tbody input.check').each(function () {
                var id = $(this).val();
                if (selectedItems[id]) {
                    $(this).prop('checked', true);
                }
            });
        }
    });

    // saat checkbox individual diklik/dilepas
    $(document).on('change', '#tbl_data tbody input.check', function () {
        var id = $(this).val();
        var title = $(this).data('title') || '';
        if ($(this).prop('checked')) {
            if (!selectedItems[id]) {
                selectOrder++;
                selectedItems[id] = { title: title, order: selectOrder };
            }
        } else {
            delete selectedItems[id];
        }
    });

    $('#paper_size').on('change', function () {
        var paperKey = $(this).val();
        var $modelSel = $('#label_model');
        $modelSel.html('<option value="">-- Pilih Model --</option>');
        if (!paperKey || !paperSizeConfig[paperKey]) {
            $('#label_model_wrapper').slideUp(200);
            return;
        }
        $.each(paperSizeConfig[paperKey].models, function (key, label) {
            $modelSel.append($('<option>', { value: key, text: label }));
        });
        $('#label_model_wrapper').slideDown(200);
    });

    $('#btnCetak').on('click', function () {
        var paperSize = $('#paper_size').val();
        var labelModel = $('#label_model').val();
        if (!paperSize || !labelModel) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih jenis kertas dan model label dulu.' });
            return false;
        }
        // ambil semua koleksi terpilih dari selectedItems (lintas halaman),
        // urutkan berdasarkan kapan dicentang (order ascending)
        var checkedItems = Object.keys(selectedItems)
            .map(function (id) { return { id: id, order: selectedItems[id].order }; })
            .sort(function (a, b) { return a.order - b.order; })
            .map(function (item) { return item.id; });

        if (checkedItems.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Pilih minimal satu koleksi.' });
            return false;
        }
        var form = $('<form>', { method: 'post', action: '<?= base_url('cetak-label/print_label') ?>', target: '_blank' });
        form.append($('<input>', { type: 'hidden', name: 'eksemplar_ids', value: checkedItems.join(',') }));
        form.append($('<input>', { type: 'hidden', name: 'eksemplar_tpl', value: labelModel }));
        form.append($('<input>', { type: 'hidden', name: 'paper_size', value: paperSize }));
        form.appendTo('body').submit();
    });

    $(document).on('change', '.check_data', function () {
        // "Pilih Semua" hanya berlaku untuk baris yang sedang tampil di halaman ini
        $('#tbl_data tbody .check').prop('checked', $(this).prop('checked')).trigger('change');
    });
});
</script>
<?= $this->endSection('script'); ?>