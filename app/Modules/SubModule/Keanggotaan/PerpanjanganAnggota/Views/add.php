<?php

$request = service('request');

$db = db_connect($DBGroup = 'data');
$builder = $db->table('members')
    ->select('members.ID, members.Fullname, members.MemberNo, members.NoHp, members.Email, members.JenisAnggota_id, members.EndDate, members.Address, members.UnitKerja_id, members.SubUnitKerja_id, members.status_non_asn, members.PhotoUrl, members.StatusAnggota_id')
    ->select('jenis_anggota.jenisanggota as JenisAnggota_label')
    ->select('master_unit.nama_unit as Unit_label')
    ->select('master_subunit.nama_subunit as SubUnit_label')
    ->join('jenis_anggota', 'jenis_anggota.id = members.JenisAnggota_id', 'left')
    ->join('master_unit', 'master_unit.id = members.UnitKerja_id', 'left')
    ->join('master_subunit', 'master_subunit.id = members.SubUnitKerja_id', 'left')
    ->orderBy('members.Fullname', 'ASC');
$anggotas = $builder->get()->getResult();

$unitModel = new \UnitKerja\Models\UnitKerjaModel();
$subUnitModel = new \App\Models\SubUnitModel();
$unit_kerja = $unit_kerja ?? $unitModel->where('active', 1)->orderBy('id', 'ASC')->findAll();
$sub_units_grouped = $sub_units_grouped ?? $subUnitModel->getAllActiveGrouped();
$jenis_anggota_list = $jenis_anggota_list ?? $db->table('jenis_anggota')->where('active', 1)->orderBy('id', 'ASC')->get()->getResult();

if (!function_exists('safe_old_value')) {
    function safe_old_value($field, $default = '') {
        $req = service('request');
        if (method_exists($req, 'getOldInput')) {
            $val = old($field);
            return ($val !== null && $val !== '') ? $val : $default;
        }
        return $default;
    }
}

?>

<?= $this->extend('App\Views\layout\main'); ?>
<?= $this->section('style'); ?>
<style>
    .select2-container .select2-selection--multiple {
        min-height: 42px;
        border: 1px solid #ced4da;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #3f6ad8;
        border-color: #2955c8;
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff;
        margin-right: 5px;
    }
    .btn-xs {
        padding: 0.15rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1.5;
        border-radius: 0.2rem;
    }
    .bg-light-accent {
        background-color: #f8f9fa;
        border-left: 4px solid #3f6ad8;
    }
</style>
<?= $this->endSection('style'); ?>

<?= $this->section('page'); ?>

<div class="app-main__inner">
    <div class="app-page-title">
        <div class="page-title-wrapper">
            <div class="page-title-heading">
                <div class="page-title-icon">
                    <i class="pe-7s-id icon-gradient bg-strong-bliss"></i>
                </div>
                <div>Perpanjangan Anggota
                    <div class="page-title-subheading">Perpanjangan masa berlaku keanggotaan ASN, Non-ASN, Magang Nasional & Magang Reguler
                    </div>
                </div>
            </div>
            <div class="page-title-actions">
                <nav class="" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>"><i
                                    class="fa fa-home"></i></a></li>
                        <li class="breadcrumb-item">Keanggotaan</li>
                        <li class="breadcrumb-item"><a href="<?= base_url('perpanjangan-anggota') ?>">Perpanjangan Anggota</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-header">
            <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Form Perpanjangan Anggota
        </div>
        <div class="card-body">
            <div id="infoMessage"><?= $message ?? ''; ?></div>
            <?= get_message('message'); ?>

            <form id="frm_create" class="col-md-12 mx-auto" method="post"
                action="<?= base_url('perpanjangan-anggota/create'); ?>">
                
                <div class="form-row">
                    <!-- SISI KIRI: PROFILE / RINGKASAN PILIHAN -->
                    <div class="col-md-4">
                        <div id="section_single_profile">
                            <?= $this->include('PerpanjanganAnggota\Views\section\member_profile') ?>
                        </div>
                        <div id="section_multiple_summary" style="display:none;">
                            <div class="card-shadow-dark card-border mb-3 card">
                                <div class="card-header bg-corporate-primary2 text-white d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="header-icon lnr-users icon-gradient bg-plum-plate"> </i>
                                        Ringkasan Pilihan (<span id="summary_count">0</span>)
                                    </span>
                                    <span class="badge badge-light" id="summary_total_badge">0 Anggota</span>
                                </div>
                                <div class="card-body p-0" style="max-height: 480px; overflow-y: auto;">
                                    <ul class="list-group list-group-flush" id="summary_list">
                                        <!-- Anggota terpilih di-generate dinamis via JavaScript -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SISI KANAN: FORM FILTER & INPUT PERPANJANGAN -->
                    <div class="col-md-8">
                        
                        <!-- SECTION FILTER ORGANISASI KEMNAKER (ESELON 1, ESELON 2, ASN / NON-ASN, MAGANG) -->
                        <div class="mb-3 p-3 border rounded bg-light-accent shadow-sm">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="font-weight-bold text-dark">
                                    <i class="fa fa-filter text-primary mr-1"></i> Filter Anggota Berdasarkan Struktur:
                                </span>
                                <small class="text-muted">Pilih kriteria untuk memilih banyak anggota sekaligus</small>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-2">
                                    <label class="small text-muted mb-1 font-weight-bold">Eselon I (Unit Kerja)</label>
                                    <select class="form-control form-control-sm" id="filter_unit">
                                        <option value="">-- Semua Eselon I --</option>
                                        <?php foreach ($unit_kerja as $u): ?>
                                            <option value="<?= $u->id ?>"><?= esc($u->nama_unit) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="small text-muted mb-1 font-weight-bold">Eselon II (Biro / Direktorat)</label>
                                    <select class="form-control form-control-sm" id="filter_subunit" disabled>
                                        <option value="">-- Semua Eselon II --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="small text-muted mb-1 font-weight-bold">Kategori Keanggotaan</label>
                                    <select class="form-control form-control-sm" id="filter_jenis">
                                        <option value="">-- Semua Kategori --</option>
                                        <?php foreach ($jenis_anggota_list as $ja): ?>
                                            <?php 
                                                $jaId = is_array($ja) ? $ja['id'] : $ja->id; 
                                                $jaNama = is_array($ja) ? $ja['jenisanggota'] : $ja->jenisanggota;
                                            ?>
                                            <option value="<?= $jaId ?>"><?= esc($jaNama) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label class="small text-muted mb-1 font-weight-bold">Status Penugasan Non-ASN</label>
                                    <select class="form-control form-control-sm" id="filter_status_non_asn">
                                        <option value="">-- Semua Status Non-ASN --</option>
                                        <option value="staf">Staf</option>
                                        <option value="magang_nasional">Magang Nasional</option>
                                        <option value="magang_biasa">Magang Reguler</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                <span class="small text-muted" id="filter-hint">Pilih filter lalu klik tombol terapkan</span>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mr-1" id="btn-reset-filter">
                                        <i class="fa fa-undo mr-1"></i> Reset Filter
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary font-weight-bold" id="btn-apply-filter" title="Pilih Semua yang Cocok">
                                        <i class="fa fa-check-double mr-1"></i> Pilih Anggota Sesuai Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL CEPAT & PILIH ANGGOTA -->
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-danger mr-1" id="btn-select-expired">
                                    <i class="fa fa-clock mr-1"></i> Pilih Semua Anggota Expired
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-clear-selection">
                                    <i class="fa fa-times mr-1"></i> Bersihkan Semua Pilihan
                                </button>
                            </div>
                            <small class="text-muted"><span id="badge-total-selected" class="badge badge-info font-weight-bold">0</span> anggota dipilih</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="package" class="font-weight-bold">Daftar Anggota yang Diperpanjang <span class="text-danger">*</span></label>
                            <select class="custom-select js-example-basic-multiple" id="package"
                                name="Member_id[]" multiple="multiple" style="width: 100%;">
                                <?php foreach ($anggotas as $row): ?>
                                    <?php 
                                        $is_expired = false;
                                        if (!empty($row->EndDate)) {
                                            $is_expired = (date('Y-m-d', strtotime($row->EndDate)) < date('Y-m-d'));
                                        }
                                        $is_asn = function_exists('isJenisAnggotaAsn') ? isJenisAnggotaAsn($row->JenisAnggota_id) : ($row->JenisAnggota_id == 22);
                                        
                                        $status_non_asn_label = '';
                                        if (!$is_asn && !empty($row->status_non_asn)) {
                                            $status_non_asn_label = function_exists('format_status_non_asn') ? format_status_non_asn($row->status_non_asn) : ucfirst($row->status_non_asn);
                                        }

                                        $photo_url = '';
                                        if (!empty($row->PhotoUrl)) {
                                            $pName = trim(explode(',', $row->PhotoUrl)[0]);
                                            if ($pName !== '') {
                                                $photo_url = base_url('uploads/anggota/' . $pName);
                                            }
                                        }
                                        if (empty($photo_url)) {
                                            $photo_url = base_url('uploads/default/avatar_neutral.svg');
                                        }

                                        $unit_str = $row->Unit_label ?: '-';
                                        if (!empty($row->SubUnit_label)) {
                                            $unit_str .= ' / ' . $row->SubUnit_label;
                                        }

                                        $kategori_display = $row->JenisAnggota_label ?: '-';
                                        if (!$is_asn && !empty($status_non_asn_label)) {
                                            $kategori_display .= ' (' . $status_non_asn_label . ')';
                                        }
                                    ?>
                                    <option value="<?= $row->ID ?>"
                                        data-id="<?= $row->ID ?>"
                                        data-no_anggota="<?= esc($row->MemberNo) ?>"
                                        data-name="<?= esc($row->Fullname) ?>"
                                        data-email="<?= esc($row->Email) ?>"
                                        data-address="<?= esc($row->Address) ?>"
                                        data-nomor="<?= esc($row->NoHp) ?>"
                                        data-date="<?= !empty($row->EndDate) ? date('Y-m-d', strtotime($row->EndDate)) : '' ?>"
                                        data-date_formatted="<?= !empty($row->EndDate) ? date('d-m-Y', strtotime($row->EndDate)) : '-' ?>"
                                        data-unit="<?= $row->UnitKerja_id ?? '' ?>"
                                        data-unit_label="<?= esc($row->Unit_label ?? '-') ?>"
                                        data-subunit="<?= $row->SubUnitKerja_id ?? '' ?>"
                                        data-subunit_label="<?= esc($row->SubUnit_label ?? '-') ?>"
                                        data-jenis="<?= $row->JenisAnggota_id ?? '' ?>"
                                        data-jenis_label="<?= esc($row->JenisAnggota_label ?? '-') ?>"
                                        data-is_asn="<?= $is_asn ? '1' : '0' ?>"
                                        data-status_non_asn="<?= esc($row->status_non_asn ?? '') ?>"
                                        data-status_non_asn_label="<?= esc($status_non_asn_label) ?>"
                                        data-photo="<?= esc($photo_url) ?>"
                                        data-expired="<?= $is_expired ? '1' : '0' ?>">
                                        <?= esc($row->MemberNo) ?> - <?= esc($row->Fullname) ?> [<?= esc($unit_str) ?>] [<?= esc($kategori_display) ?>] <?= $is_expired ? '(Expired)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Ketik nomor anggota atau nama untuk mencari dan memilih anggota.</small>
                        </div>

                        <hr>

                        <!-- FORM UPDATE DATA PERPANJANGAN -->
                        <h6 class="font-weight-bold text-primary mb-3">
                            <i class="fa fa-edit mr-1"></i> Data Pembaruan Masa Berlaku & Status
                        </h6>

                        <div class="form-group mb-3">
                            <label for="date2" class="font-weight-bold">Update Tanggal Berakhir <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                </div>
                                <input type="text" class="form-control datepicker" id="date2" name="EndDate"
                                    placeholder="YYYY-MM-DD" value="<?= safe_old_value('EndDate') ?>" required autocomplete="off" />
                            </div>
                            <div class="mt-1 d-flex flex-wrap align-items-center">
                                <small class="text-muted mr-2">Set Cepat dari hari ini:</small>
                                <button type="button" class="btn btn-xs btn-outline-info mr-1 mb-1 btn-quick-date" data-months="12">
                                    <i class="fa fa-plus-circle"></i> +1 Tahun (ASN / Staf)
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-info mr-1 mb-1 btn-quick-date" data-months="6">
                                    <i class="fa fa-plus-circle"></i> +6 Bulan (Magang Nas.)
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-info mr-1 mb-1 btn-quick-date" data-months="1">
                                    <i class="fa fa-plus-circle"></i> +1 Bulan (Magang Reg.)
                                </button>
                            </div>
                            <small class="form-text text-muted" id="end_date_hint"></small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="jenisanggota_id" class="font-weight-bold">Update Kategori Keanggotaan <span class="text-danger">*</span></label>
                            <select class="form-control" name="Jenisanggota_id" id="jenisanggota_id" required>
                                <option value="">-- Pilih Kategori Anggota --</option>
                                <?php foreach ($jenis_anggota_list as $row): ?>
                                    <?php 
                                        $jId = is_array($row) ? $row['id'] : $row->id;
                                        $jName = is_array($row) ? $row['jenisanggota'] : $row->jenisanggota;
                                        $jIsAsn = function_exists('isJenisAnggotaAsn') ? isJenisAnggotaAsn($jId) : ($jId == 22);
                                    ?>
                                    <option value="<?= $jId ?>" data-is_asn="<?= $jIsAsn ? '1' : '0' ?>" <?= ($jId == safe_old_value('JenisAnggota_id')) ? 'selected' : '' ?>>
                                        <?= esc($jName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- SECTION STATUS PENUGASAN NON-ASN -->
                        <div id="section_status_non_asn" style="display:none;" class="p-3 border rounded mb-3 bg-light">
                            <label for="update_status_non_asn" class="font-weight-bold text-dark mb-1">
                                <i class="fa fa-user-tag text-warning mr-1"></i> Status Penugasan Non-ASN
                            </label>
                            <select class="form-control" name="status_non_asn" id="update_status_non_asn">
                                <option value="">-- Pilih Status Non-ASN --</option>
                                <option value="staf" <?= safe_old_value('status_non_asn') == 'staf' ? 'selected' : '' ?>>Staf</option>
                                <option value="magang_nasional" <?= safe_old_value('status_non_asn') == 'magang_nasional' ? 'selected' : '' ?>>Magang Nasional (Masa Berlaku 6 Bulan)</option>
                                <option value="magang_biasa" <?= safe_old_value('status_non_asn') == 'magang_biasa' ? 'selected' : '' ?>>Magang Reguler (Masa Berlaku 1 Bulan)</option>
                            </select>
                            <small class="form-text text-muted">Pilih status penugasan untuk anggota Non-ASN atau Magang.</small>
                        </div>

                        <!-- SECTION UNIT KERJA ESELON I & ESELON II -->
                        <div class="p-3 border rounded mb-3 bg-light">
                            <div class="form-group mb-2">
                                <label for="update_unit" class="font-weight-bold mb-1">
                                    <i class="fa fa-building text-primary mr-1"></i> Update Unit Kerja (Eselon I)
                                </label>
                                <select class="form-control" name="UnitKerja_id" id="update_unit">
                                    <option value="">-- Tidak Diubah / Tetap --</option>
                                    <?php foreach ($unit_kerja as $row): ?>
                                        <option value="<?= $row->id ?>"><?= esc($row->nama_unit) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-0">
                                <label for="update_subunit" class="font-weight-bold mb-1">
                                    <i class="fa fa-sitemap text-info mr-1"></i> Update Biro / Direktorat (Eselon II)
                                </label>
                                <select class="form-control" name="SubUnitKerja_id" id="update_subunit" disabled>
                                    <option value="">-- Pilih Eselon I Terlebih Dahulu --</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="col-md-7">
                                <div class="form-group mb-3">
                                    <label for="frm_create_biaya" class="font-weight-bold">Biaya Perpanjangan (Rp)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Rp</span>
                                        </div>
                                        <input type="number" class="form-control" id="frm_create_biaya" name="biaya"
                                            placeholder="0" value="<?= safe_old_value('biaya', '0') ?>" min="0" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 d-flex align-items-center">
                                <div class="custom-control custom-checkbox mt-2">
                                    <input type="checkbox" class="custom-control-input" id="is_lunas" name="is_lunas" value="1" <?= safe_old_value('is_lunas', '1') ? 'checked' : '' ?>>
                                    <label class="custom-control-label font-weight-bold" for="is_lunas">Sudah Lunas</label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="frm_create_description" class="font-weight-bold">Keterangan / Catatan</label>
                            <textarea id="frm_create_description" name="Keterangan" rows="2"
                                class="form-control" placeholder="Catatan perpanjangan anggota (opsional)"><?= safe_old_value('Keterangan') ?></textarea>
                        </div>

                    </div>
                </div>

                <div class="form-group mt-3 text-right border-top pt-3">
                    <a href="<?= base_url('perpanjangan-anggota') ?>" class="btn btn-secondary mr-2">
                        <i class="fa fa-arrow-left mr-1"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary font-weight-bold px-4">
                        <i class="fa fa-save mr-1"></i> Simpan Perpanjangan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection('page'); ?>

<?= $this->section('script'); ?>

<script type="text/javascript">
    var _confirmed = false;
    var subUnitsGrouped = <?= json_encode($sub_units_grouped) ?>;
    var defaultAvatar = '<?= base_url('uploads/default/avatar_neutral.svg') ?>';

    function formatDateIndo(dateStr) {
        if (!dateStr || dateStr === '-') return '-';
        var months = ['Januari','Februari','Maret','April','Mei','Juni',
            'Juli','Agustus','September','Oktober','November','Desember'];
        var parts = dateStr.split('-');
        if (parts.length === 3) {
            var y = parseInt(parts[0], 10);
            var m = parseInt(parts[1], 10) - 1;
            var d = parseInt(parts[2], 10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d) && m >= 0 && m < 12) {
                return d + ' ' + months[m] + ' ' + y;
            }
        }
        var date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
    }

    function addMonthsToToday(months) {
        var d = new Date();
        d.setMonth(d.getMonth() + parseInt(months));
        var yyyy = d.getFullYear();
        var mm = String(d.getMonth() + 1).padStart(2, '0');
        var dd = String(d.getDate()).padStart(2, '0');
        return yyyy + '-' + mm + '-' + dd;
    }

    function populateSubUnitDropdown(unitId, $targetSelect, placeholderText, selectedSubUnitId) {
        $targetSelect.empty();

        if (!unitId) {
            $targetSelect.html('<option value="">' + placeholderText + '</option>');
            $targetSelect.prop('disabled', true);
            return;
        }

        var list = subUnitsGrouped[unitId] || [];

        if (list.length > 0) {
            $targetSelect.append('<option value="">' + placeholderText + '</option>');
            $.each(list, function(i, item) {
                var isSelected = (selectedSubUnitId && selectedSubUnitId == item.id) ? 'selected' : '';
                $targetSelect.append('<option value="' + item.id + '" ' + isSelected + '>' + item.nama_subunit + '</option>');
            });
            $targetSelect.prop('disabled', false);
        } else {
            if (unitId == '1') {
                $targetSelect.append('<option value="">(Kemnaker RI - Tidak ada Eselon II)</option>');
            } else {
                $targetSelect.append('<option value="">(Tidak ada Eselon II untuk unit ini)</option>');
            }
            $targetSelect.prop('disabled', false);
        }
    }

    $(function () {
        // Inisialisasi Select2
        $('.js-example-basic-multiple').select2({
            placeholder: "Ketik Nama atau Nomor Anggota...",
            allowClear: true
        });

        // Inisialisasi Datepicker
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            startDate: new Date()
        });

        // Cascading Filter Eselon I -> Eselon II
        $('#filter_unit').on('change', function() {
            var unitId = $(this).val();
            populateSubUnitDropdown(unitId, $('#filter_subunit'), '-- Semua Eselon II --');
        });

        // Cascading Update Eselon I -> Eselon II
        $('#update_unit').on('change', function() {
            var unitId = $(this).val();
            populateSubUnitDropdown(unitId, $('#update_subunit'), '-- Pilih Eselon II --');
        });

        // Tombol Set Cepat Tanggal Berakhir
        $('.btn-quick-date').on('click', function() {
            var months = $(this).data('months');
            var newDate = addMonthsToToday(months);
            $('#date2').val(newDate);
            $('#end_date_hint').html('<span class="text-success"><i class="fa fa-check"></i> Diset ke ' + formatDateIndo(newDate) + ' (+' + months + ' bulan)</span>');
        });

        // Dynamic Toggle Status Non-ASN berdasarkan Kategori
        $('#jenisanggota_id').on('change', function() {
            var selectedOpt = $(this).find('option:selected');
            var isAsn = selectedOpt.data('is_asn');

            if (isAsn == '1') {
                $('#section_status_non_asn').slideUp();
                $('#update_status_non_asn').val('');
            } else if ($(this).val()) {
                $('#section_status_non_asn').slideDown();
            } else {
                $('#section_status_non_asn').slideUp();
            }
        });

        // Event saat Pilihan Anggota Berubah
        $('#package').on('change', function () {
            const selectedOptions = $('#package option:selected');
            const summaryList = $('#summary_list');
            const summaryCount = $('#summary_count');
            const count = selectedOptions.length;
            
            $('#badge-total-selected').text(count);
            $('#summary_total_badge').text(count + ' Anggota');

            if (count === 1) {
                $('#section_single_profile').show();
                $('#section_multiple_summary').hide();

                const opt = $(selectedOptions[0]);
                const no_anggota = opt.data('no_anggota') || '-';
                const name = opt.data('name') || '-';
                const email = opt.data('email') || '-';
                const address = opt.data('address') || '-';
                const nomor = opt.data('nomor') || '-';
                const date = opt.data('date');
                const date_formatted = opt.data('date_formatted') || '-';
                const unit = opt.data('unit');
                const unit_label = opt.data('unit_label') || '-';
                const subunit = opt.data('subunit');
                const subunit_label = opt.data('subunit_label') || '-';
                const jenis = opt.data('jenis');
                const jenis_label = opt.data('jenis_label') || '-';
                const is_asn = opt.data('is_asn');
                const status_non_asn = opt.data('status_non_asn') || '';
                const status_non_asn_label = opt.data('status_non_asn_label') || '';
                const photo = opt.data('photo') || defaultAvatar;
                const is_expired = opt.data('expired') == '1';

                // Isi Sisi Profil Kiri
                $('#profile_avatar').attr('src', photo);
                $('#name').text(name);
                $('[name=no_anggota]').text(no_anggota);
                
                // Badges Kategori & Status
                let badgeHtml = '';
                if (is_asn == '1') {
                    badgeHtml = '<span class="badge badge-success px-2 py-1"><i class="fa fa-user-tie mr-1"></i> ASN Kemnaker</span>';
                } else {
                    badgeHtml = '<span class="badge badge-info px-2 py-1"><i class="fa fa-id-card mr-1"></i> ' + jenis_label + '</span>';
                    if (status_non_asn_label) {
                        badgeHtml += ' <span class="badge badge-warning text-dark px-2 py-1 ml-1">' + status_non_asn_label + '</span>';
                    }
                }
                $('#profile_kategori_badge').html(badgeHtml);

                // Unit Kerja
                var fullUnit = unit_label;
                if (subunit_label && subunit_label !== '-') {
                    fullUnit += ' / ' + subunit_label;
                }
                $('#profile_unit_kerja').text(fullUnit);

                // Masa Berlaku dengan Status
                var statusMasaHtml = date_formatted;
                if (is_expired) {
                    statusMasaHtml += ' <span class="badge badge-danger ml-1">Expired</span>';
                } else {
                    statusMasaHtml += ' <span class="badge badge-success ml-1">Aktif</span>';
                }
                $('#date').html(statusMasaHtml);

                $('#nomor').text(nomor);
                $('[name=email]').text(email);
                $('[name=address]').text(address);

                // Sinkronkan ke form update kanan
                if (jenis) {
                    $('#jenisanggota_id').val(jenis).trigger('change');
                }
                if (status_non_asn) {
                    $('#update_status_non_asn').val(status_non_asn);
                }
                if (unit) {
                    $('#update_unit').val(unit);
                    populateSubUnitDropdown(unit, $('#update_subunit'), '-- Pilih Eselon II --', subunit);
                }

                // Kalkulasi otomatis rekomendasi tanggal berakhir jika belum diisi
                if (!$('#date2').val() || $('#date2').data('auto-set') == '1') {
                    var suggestedMonths = 12; // Default 1 tahun untuk ASN & Staf
                    if (is_asn != '1') {
                        if (status_non_asn === 'magang_nasional') suggestedMonths = 6;
                        else if (status_non_asn === 'magang_biasa') suggestedMonths = 1;
                    }
                    var suggestedDate = addMonthsToToday(suggestedMonths);
                    $('#date2').val(suggestedDate).data('auto-set', '1');
                    $('#end_date_hint').html('<span class="text-info"><i class="fa fa-magic"></i> Disarankan: ' + formatDateIndo(suggestedDate) + ' (+' + suggestedMonths + ' bulan)</span>');
                }

            } else if (count > 1) {
                $('#section_single_profile').hide();
                $('#section_multiple_summary').show();
                
                summaryCount.text(count);
                let html = '';
                selectedOptions.each(function() {
                    const opt = $(this);
                    const name = opt.data('name');
                    const no = opt.data('no_anggota');
                    const date_formatted = opt.data('date_formatted') || '-';
                    const jenis = opt.data('jenis_label') || '-';
                    const is_asn = opt.data('is_asn');
                    const status_non_asn_label = opt.data('status_non_asn_label') || '';
                    const unit_label = opt.data('unit_label') || '-';
                    const subunit_label = opt.data('subunit_label') || '';
                    const is_expired = opt.data('expired') == '1';

                    var badgeKat = is_asn == '1'
                        ? '<span class="badge badge-success mr-1">ASN</span>'
                        : '<span class="badge badge-info mr-1">Non-ASN</span>';
                    
                    if (status_non_asn_label) {
                        badgeKat += '<span class="badge badge-warning text-dark mr-1">' + status_non_asn_label + '</span>';
                    }

                    var expBadge = is_expired
                        ? '<span class="badge badge-danger">Expired</span>'
                        : '<span class="badge badge-secondary">' + date_formatted + '</span>';

                    var fullUnit = unit_label;
                    if (subunit_label && subunit_label !== '-') {
                        fullUnit += ' / ' + subunit_label;
                    }

                    html += `<li class="list-group-item p-2">
                        <div class="widget-content p-0">
                            <div class="widget-content-wrapper">
                                <div class="widget-content-left flex2">
                                    <div class="widget-heading font-weight-bold" style="font-size: 0.85rem;">${name}</div>
                                    <div class="widget-subheading opacity-8" style="font-size: 0.75rem;">
                                        ${no} &bull; ${badgeKat} ${expBadge}
                                        <small class="d-block text-muted mt-1"><i class="fa fa-building"></i> ${fullUnit}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>`;
                });
                summaryList.html(html);

            } else {
                // Tidak ada anggota dipilih
                $('#section_single_profile').show();
                $('#section_multiple_summary').hide();
                
                $('#profile_avatar').attr('src', defaultAvatar);
                $('#name').text('Pilih Anggota');
                $('[name=no_anggota]').text('-');
                $('#profile_kategori_badge').html('<span class="badge badge-secondary">-</span>');
                $('#profile_unit_kerja').text('-');
                $('#date').text('-');
                $('#nomor').text('-');
                $('[name=email]').text('-');
                $('[name=address]').text('-');
            }
        });

        // Tombol Pilih Semua Anggota Expired
        $('#btn-select-expired').on('click', function() {
            const expiredIds = [];
            $('#package option').each(function() {
                if ($(this).data('expired') == '1') {
                    expiredIds.push($(this).val());
                }
            });

            if (expiredIds.length > 0) {
                $('#package').val(expiredIds).trigger('change');
                Swal.fire({
                    title: 'Anggota Expired Dipilih',
                    text: 'Berhasil memilih ' + expiredIds.length + ' anggota yang masa berlakunya telah kadaluarsa.',
                    type: 'info'
                });
            } else {
                Swal.fire('Info', 'Tidak ada anggota dengan masa berlaku yang telah kadaluarsa.', 'info');
            }
        });

        // Tombol Bersihkan Pilihan
        $('#btn-clear-selection').on('click', function() {
            $('#package').val([]).trigger('change');
            $('#update_unit').val('');
            $('#update_subunit').html('<option value="">-- Pilih Eselon I Terlebih Dahulu --</option>').prop('disabled', true);
            $('#jenisanggota_id').val('').trigger('change');
            $('#update_status_non_asn').val('');
            $('#date2').val('');
            $('#end_date_hint').empty();
        });

        // Tombol Reset Filter
        $('#btn-reset-filter').on('click', function() {
            $('#filter_unit').val('');
            $('#filter_subunit').html('<option value="">-- Semua Eselon II --</option>').prop('disabled', true);
            $('#filter_jenis').val('');
            $('#filter_status_non_asn').val('');
            $('#filter-hint').text('Filter telah direset.');
        });

        // Terapkan Filter Struktur Kemnaker (Eselon 1, Eselon 2, Jenis Anggota, Status Non-ASN)
        $('#btn-apply-filter').on('click', function() {
            const unit_id = $('#filter_unit').val();
            const subunit_id = $('#filter_subunit').val();
            const jenis_id = $('#filter_jenis').val();
            const status_non_asn = $('#filter_status_non_asn').val();

            if (!unit_id && !subunit_id && !jenis_id && !status_non_asn) {
                Swal.fire('Peringatan', 'Silakan pilih minimal satu kriteria filter terlebih dahulu.', 'warning');
                return;
            }

            const selectedIds = [];

            $('#package option').each(function() {
                const opt_u = $(this).data('unit');
                const opt_su = $(this).data('subunit');
                const opt_j = $(this).data('jenis');
                const opt_sna = $(this).data('status_non_asn');
                
                let match = true;
                if (unit_id && opt_u != unit_id) match = false;
                if (subunit_id && opt_su != subunit_id) match = false;
                if (jenis_id && opt_j != jenis_id) match = false;
                if (status_non_asn && opt_sna != status_non_asn) match = false;

                if (match) {
                    selectedIds.push($(this).val());
                }
            });

            if (selectedIds.length > 0) {
                $('#package').val(selectedIds).trigger('change');
                
                // Pra-isi form update jika filter spesifik dipilih
                if (unit_id) {
                    $('#update_unit').val(unit_id);
                    populateSubUnitDropdown(unit_id, $('#update_subunit'), '-- Pilih Eselon II --', subunit_id);
                }
                if (jenis_id) {
                    $('#jenisanggota_id').val(jenis_id).trigger('change');
                }
                if (status_non_asn) {
                    $('#update_status_non_asn').val(status_non_asn);
                }

                Swal.fire({
                    title: 'Filter Diterapkan',
                    text: 'Ditemukan ' + selectedIds.length + ' anggota yang cocok dengan filter dan otomatis dipilih.',
                    type: 'success'
                });
            } else {
                Swal.fire('Info', 'Tidak ditemukan anggota yang sesuai dengan kriteria filter tersebut.', 'info');
            }
        });

        // Konfirmasi Submit Perpanjangan
        $('#frm_create').on('submit', function(e) {
            if (_confirmed) {
                _confirmed = false;
                return true;
            }

            e.preventDefault();

            var members = $('#package').val();
            var endDate = $('[name=EndDate]').val();
            var jenisId = $('[name=Jenisanggota_id]').val();

            if (!members || members.length === 0) {
                Swal.fire('Peringatan', 'Silakan pilih minimal satu anggota yang ingin diperpanjang.', 'warning');
                return false;
            }

            if (!endDate) {
                Swal.fire('Peringatan', 'Silakan isi Tanggal Berakhir perpanjangan.', 'warning');
                return false;
            }

            if (!jenisId) {
                Swal.fire('Peringatan', 'Silakan pilih Kategori Anggota.', 'warning');
                return false;
            }

            var jumlah = members.length;
            var confirmText = jumlah > 1
                ? 'Anda akan memperpanjang masa berlaku ' + jumlah + ' anggota sekaligus sampai dengan tanggal ' + formatDateIndo(endDate) + '. Lanjutkan?'
                : 'Anda akan memperpanjang 1 anggota sampai dengan tanggal ' + formatDateIndo(endDate) + '. Lanjutkan?';

            Swal.fire({
                title: 'Konfirmasi Perpanjangan',
                text: confirmText,
                type: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Simpan Perpanjangan',
                cancelButtonText: 'Batal',
                cancelButtonColor: '#d33',
            }).then(function(result) {
                if (result.value) {
                    _confirmed = true;
                    $('#frm_create').submit();
                }
            });
        });
    });

    $(".btn-pilih").click(function () {
        var id = $(this).data('id');
        var currentValues = $('#package').val() || [];
        if (currentValues.indexOf(id.toString()) === -1) {
            currentValues.push(id.toString());
            $('#package').val(currentValues).trigger('change');
        }
        $('#modal_create').modal('hide');
    });
</script>

<?= $this->endSection('script'); ?>
