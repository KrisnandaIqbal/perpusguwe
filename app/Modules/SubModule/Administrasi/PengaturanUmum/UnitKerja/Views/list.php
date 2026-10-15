<?= $this->extend('App\Views\layout\main'); ?>

<?= $this->section('style'); ?>
<style>
    .nav-tabs .nav-link {
        font-weight: 600;
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        color: #2b6cb0;
        border-bottom: 3px solid #2b6cb0;
    }
    .filter-wrapper {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 12px 16px;
        margin-bottom: 16px;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>
<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-network icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Pengaturan Unit Kerja & Sub-Unit
                    <div class="page-title-subheading">Kelola struktur organisasi Eselon I dan Biro/Direktorat/Unit penempatan anggota.</div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i class="fa fa-home"></i> Home</a></li>
                        <li class="breadcrumb-item">Administrasi</li>
                        <li class="breadcrumb-item">Pengaturan Umum</li>
                        <li class="breadcrumb-item active" aria-current="page">Unit Kerja</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <!-- Alert Flash Data -->
    <?php if (session()->getFlashdata('swal_title')): ?>
        <div class="alert alert-<?= session()->getFlashdata('swal_icon') === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert">
            <strong><?= esc(session()->getFlashdata('swal_title')) ?>:</strong> <?= esc(session()->getFlashdata('swal_text')) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="main-card mb-4 card">
        <div class="card-header border-bottom-0 pb-0">
            <ul class="nav nav-tabs card-header-tabs" id="unitTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-subunit-link" data-toggle="tab" href="#tab-subunit" role="tab" aria-controls="tab-subunit" aria-selected="true">
                        <i class="fa fa-sitemap mr-1"></i> Sub-Unit Kerja (Biro / Direktorat / Unit)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-unit-link" data-toggle="tab" href="#tab-unit" role="tab" aria-controls="tab-unit" aria-selected="false">
                        <i class="fa fa-building mr-1"></i> Unit Kerja Induk (Eselon I)
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content" id="unitTabsContent">
                <!-- TAB 1: SUB-UNIT KERJA -->
                <div class="tab-pane fade show active" id="tab-subunit" role="tabpanel" aria-labelledby="tab-subunit-link">
                    <!-- Filter Toolbar -->
                    <div class="filter-wrapper d-flex flex-wrap justify-content-between align-items-center">
                        <div class="d-flex align-items-center mb-2 mb-md-0">
                            <label for="filter_unit_id" class="mr-2 mb-0 font-weight-bold text-nowrap"><i class="fa fa-filter mr-1"></i> Unit Induk:</label>
                            <select id="filter_unit_id" class="form-control form-control-sm" style="min-width: 280px;">
                                <option value="">-- Semua Unit Kerja Induk --</option>
                                <?php foreach ($units as $u): ?>
                                    <option value="<?= $u->id ?>"><?= esc($u->nama_unit) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal_create_subunit">
                                <i class="fa fa-plus-circle mr-1"></i> Tambah Sub-Unit
                            </button>
                        </div>
                    </div>

                    <!-- DataTable Sub-Unit -->
                    <table style="width: 100%;" id="tbl_subunit" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="40">No</th>
                                <th>Unit Kerja Induk (Eselon I)</th>
                                <th>Biro / Direktorat / Unit</th>
                                <th class="text-center" width="70">Urutan</th>
                                <th class="text-center" width="110">Anggota</th>
                                <th class="text-center" width="90">Status</th>
                                <th class="text-center" width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <!-- TAB 2: UNIT KERJA INDUK -->
                <div class="tab-pane fade" id="tab-unit" role="tabpanel" aria-labelledby="tab-unit-link">
                    <div class="alert alert-light border py-2 px-3 mb-3 small text-muted">
                        <i class="fa fa-info-circle mr-1 text-info"></i> Unit kerja induk merupakan instansi tingkat Eselon I. Sesuai aturan sistem, unit kerja yang sudah memiliki riwayat anggota tidak boleh dihapus.
                    </div>

                    <!-- DataTable Unit Induk -->
                    <table style="width: 100%;" id="tbl_unit" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="40">No</th>
                                <th>Nama Unit Kerja Eselon I</th>
                                <th class="text-center" width="130">Jumlah Sub-Unit</th>
                                <th class="text-center" width="130">Anggota Terdaftar</th>
                                <th class="text-center" width="90">Status</th>
                                <th class="text-center" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>
<?= $this->include('UnitKerja\Views\add_modal'); ?>
<?= $this->include('UnitKerja\Views\update_modal'); ?>

<script>
var tblSubunit;
var tblUnit;

function alertSudahDipakai(count) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'warning',
            title: 'Dilarang Menghapus',
            text: 'Sub-Unit Kerja ini sudah dipakai oleh ' + count + ' data anggota perpustakaan. Untuk menjaga keutuhan riwayat keanggotaan, sub-unit tidak boleh dihapus. Silakan gunakan tombol nonaktifkan saja.',
            confirmButtonText: 'Mengerti'
        });
    } else {
        alert('Sub-Unit Kerja ini sudah dipakai oleh ' + count + ' data anggota dan tidak boleh dihapus. Silakan nonaktifkan saja.');
    }
}

$(document).ready(function() {
    // 1. Inisialisasi DataTable Sub-Unit Kerja
    tblSubunit = $('#tbl_subunit').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: {
            url: '<?= base_url("api/unit-kerja/subunit/datatable") ?>',
            data: function(d) {
                d.unit_id = $('#filter_unit_id').val();
            }
        },
        columns: [
            { data: 'no', className: 'text-center' },
            { data: 'UnitKerja' },
            { data: 'nama_subunit' },
            { data: 'sort', className: 'text-center' },
            { data: 'MemberCount', className: 'text-center' },
            { data: 'active', className: 'text-center' },
            { data: 'action', className: 'text-center', orderable: false, searchable: false }
        ],
        oLanguage: {
            sSearch: "<i class='fa fa-search'></i> _INPUT_",
            sLengthMenu: "_MENU_",
            sZeroRecords: "Tidak ada data sub-unit kerja",
            sInfo: "Menampilkan _START_ s/d _END_ dari _TOTAL_ sub-unit",
            sInfoEmpty: "Menampilkan 0 sub-unit",
            sProcessing: "Memuat data...",
            oPaginate: {
                sNext: "<i class='fa fa-chevron-right'></i>",
                sPrevious: "<i class='fa fa-chevron-left'></i>"
            }
        }
    });

    // Filter onChange
    $('#filter_unit_id').on('change', function() {
        tblSubunit.ajax.reload();
    });

    // 2. Inisialisasi DataTable Unit Kerja Induk
    tblUnit = $('#tbl_unit').DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: {
            url: '<?= base_url("api/unit-kerja/datatable") ?>'
        },
        columns: [
            { data: 'no', className: 'text-center' },
            { data: 'nama_unit' },
            { data: 'SubUnitCount', className: 'text-center' },
            { data: 'MemberCount', className: 'text-center' },
            { data: 'active', className: 'text-center' },
            { data: 'action', className: 'text-center', orderable: false, searchable: false }
        ],
        oLanguage: {
            sSearch: "<i class='fa fa-search'></i> _INPUT_",
            sLengthMenu: "_MENU_",
            sProcessing: "Memuat data...",
            oPaginate: {
                sNext: "<i class='fa fa-chevron-right'></i>",
                sPrevious: "<i class='fa fa-chevron-left'></i>"
            }
        }
    });

    // Tab switch reload table width
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
    });

    // 3. Submit Tambah Sub-Unit (AJAX)
    $('#frm_add_subunit').on('submit', function(e) {
        e.preventDefault();
        var $btn = $('#btnSaveAddSubunit');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url("api/unit-kerja/subunit/create") ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Sub-Unit');
                if (res.success || res.id) {
                    $('#modal_create_subunit').modal('hide');
                    $('#frm_add_subunit')[0].reset();
                    tblSubunit.ajax.reload(null, false);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil', 'Sub-Unit Kerja berhasil ditambahkan', 'success');
                    }
                } else {
                    $('#frm_add_subunit_alert').html('<div class="alert alert-danger">' + (res.message || 'Gagal menyimpan') + '</div>');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Sub-Unit');
                var msg = xhr.responseJSON && xhr.responseJSON.messages ? (xhr.responseJSON.messages.error || JSON.stringify(xhr.responseJSON.messages)) : 'Terjadi kesalahan sistem';
                $('#frm_add_subunit_alert').html('<div class="alert alert-danger">' + msg + '</div>');
            }
        });
    });

    // 4. Buka Modal Edit Sub-Unit
    $(document).on('click', '.btn-edit-subunit', function() {
        var id = $(this).data('id');
        $('#frm_edit_subunit_alert').empty();

        $.getJSON('<?= base_url("api/unit-kerja/subunit/detail") ?>/' + id, function(res) {
            $('#edit_subunit_id').val(res.id);
            $('#edit_unit_id').val(res.unit_id);
            $('#edit_nama_subunit').val(res.nama_subunit);
            $('#edit_sort').val(res.sort);
            $('#edit_active').val(res.active);

            if (res.member_count > 0) {
                $('#edit_subunit_member_notice').show();
            } else {
                $('#edit_subunit_member_notice').hide();
            }

            $('#modal_edit_subunit').modal('show');
        });
    });

    // 5. Submit Ubah Sub-Unit (AJAX)
    $('#frm_edit_subunit').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_subunit_id').val();
        var $btn = $('#btnSaveEditSubunit');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url("api/unit-kerja/subunit/edit") ?>/' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Perubahan');
                if (res.success) {
                    $('#modal_edit_subunit').modal('hide');
                    tblSubunit.ajax.reload(null, false);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil', 'Sub-Unit Kerja berhasil diperbarui', 'success');
                    }
                } else {
                    $('#frm_edit_subunit_alert').html('<div class="alert alert-danger">' + (res.message || 'Gagal menyimpan') + '</div>');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Perubahan');
                var msg = xhr.responseJSON && xhr.responseJSON.messages ? (xhr.responseJSON.messages.error || JSON.stringify(xhr.responseJSON.messages)) : 'Terjadi kesalahan sistem';
                $('#frm_edit_subunit_alert').html('<div class="alert alert-danger">' + msg + '</div>');
            }
        });
    });

    // 6. Buka Modal Edit Unit Induk
    $(document).on('click', '.btn-edit-unit', function() {
        var id = $(this).data('id');
        $('#frm_edit_unit_alert').empty();

        $.getJSON('<?= base_url("api/unit-kerja/detail") ?>/' + id, function(res) {
            $('#edit_unit_id_val').val(res.id);
            $('#edit_nama_unit').val(res.nama_unit);
            $('#edit_unit_active').val(res.active);
            $('#modal_edit_unit').modal('show');
        });
    });

    // 7. Submit Ubah Unit Induk (AJAX)
    $('#frm_edit_unit').on('submit', function(e) {
        e.preventDefault();
        var id = $('#edit_unit_id_val').val();
        var $btn = $('#btnSaveEditUnit');
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

        $.ajax({
            url: '<?= base_url("api/unit-kerja/edit") ?>/' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Perubahan');
                if (res.success) {
                    $('#modal_edit_unit').modal('hide');
                    tblUnit.ajax.reload(null, false);
                    tblSubunit.ajax.reload(null, false);
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Berhasil', 'Unit Kerja Induk berhasil diperbarui', 'success');
                    }
                } else {
                    $('#frm_edit_unit_alert').html('<div class="alert alert-danger">' + (res.message || 'Gagal menyimpan') + '</div>');
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan Perubahan');
                var msg = xhr.responseJSON && xhr.responseJSON.messages ? (xhr.responseJSON.messages.error || JSON.stringify(xhr.responseJSON.messages)) : 'Terjadi kesalahan sistem';
                $('#frm_edit_unit_alert').html('<div class="alert alert-danger">' + msg + '</div>');
            }
        });
    });
});
</script>
<?= $this->endSection('script'); ?>