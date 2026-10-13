<style>
/* ─── Admin Choice Cards (Kategori Keanggotaan Simple & Responsive) ─── */
.admin-choice-cards {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
}

.admin-choice-card-item {
  position: relative;
}

.admin-choice-input {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
  pointer-events: none;
}

.admin-choice-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 18px;
  min-height: 56px;
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  margin-bottom: 0;
  user-select: none;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.admin-choice-card:hover {
  border-color: #94a3b8;
  background: #f8fafc;
  transform: translateY(-1px);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.admin-choice-content {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}

.admin-choice-icon {
  width: 38px;
  height: 38px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.admin-choice-icon.icon-asn {
  background: #eff6ff;
  color: #1d4ed8;
  border: 1px solid #bfdbfe;
}

.admin-choice-icon.icon-nonasn {
  background: #f0fdf4;
  color: #047857;
  border: 1px solid #bbf7d0;
}

.admin-choice-text {
  display: flex;
  flex-direction: column;
}

.admin-choice-title {
  font-size: 1rem;
  font-weight: 700;
  color: #0f172a;
  line-height: 1.25;
}

.admin-choice-sub {
  font-size: 0.78rem;
  color: #64748b;
  margin-top: 2px;
}

.admin-choice-radio-indicator {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  border: 2px solid #cbd5e1;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  color: transparent;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

/* Checked State for Kategori Keanggotaan */
.admin-choice-input:checked + .admin-choice-card {
  border-color: #2563eb;
  background-color: #f8faff;
  box-shadow: 0 0 0 1px #2563eb, 0 4px 14px -2px rgba(37, 99, 235, 0.15);
}

.admin-choice-input:checked + .admin-choice-card .admin-choice-radio-indicator {
  border-color: #2563eb;
  background: #2563eb;
  color: #ffffff;
}

.admin-choice-input:checked + .admin-choice-card .admin-choice-title {
  color: #1d4ed8;
}

/* ─── Select2 Harmonization & Bug Fix (Satuan Kerja & Single Selects) ─── */
.select2-container {
  width: 100% !important;
  display: block !important;
}

.select2-container .select2-selection--single,
.select2-container--default .select2-selection--single,
.select2-container--bootstrap4 .select2-selection--single {
  height: 42px !important;
  min-height: 42px !important;
  padding: 6px 14px !important;
  background-color: #ffffff !important;
  border: 1.5px solid #d1d5db !important;
  border-radius: 8px !important;
  display: flex !important;
  align-items: center !important;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out !important;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
  box-sizing: border-box !important;
  position: relative !important;
}

.select2-container .select2-selection--single:focus,
.select2-container.select2-container--focus .select2-selection--single,
.select2-container.select2-container--open .select2-selection--single,
.select2-container--default .select2-selection--single:focus,
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single,
.select2-container--bootstrap4 .select2-selection--single:focus,
.select2-container--bootstrap4.select2-container--focus .select2-selection--single,
.select2-container--bootstrap4.select2-container--open .select2-selection--single {
  border-color: #2563eb !important;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
  outline: 0 !important;
}

.select2-container .select2-selection--single .select2-selection__rendered,
.select2-container--default .select2-selection--single .select2-selection__rendered,
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
  color: #1e293b !important;
  font-size: 0.95rem !important;
  font-weight: 500 !important;
  line-height: normal !important;
  padding-left: 0 !important;
  padding-right: 32px !important;
  width: 100% !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  white-space: nowrap !important;
  display: block !important;
}

.select2-container .select2-selection--single .select2-selection__placeholder,
.select2-container--default .select2-selection--single .select2-selection__placeholder,
.select2-container--bootstrap4 .select2-selection--single .select2-selection__placeholder {
  color: #94a3b8 !important;
  font-weight: 400 !important;
}

.select2-container .select2-selection--single .select2-selection__arrow,
.select2-container--default .select2-selection--single .select2-selection__arrow,
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow {
  height: 100% !important;
  top: 0 !important;
  right: 12px !important;
  width: 20px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  position: absolute !important;
}

.select2-container .select2-selection--single .select2-selection__arrow b,
.select2-container--default .select2-selection--single .select2-selection__arrow b,
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow b {
  border-color: #64748b transparent transparent transparent !important;
  border-width: 6px 4.5px 0 4.5px !important;
  border-style: solid !important;
  margin: 0 !important;
  position: static !important;
}

.select2-container.select2-container--open .select2-selection--single .select2-selection__arrow b,
.select2-container--default.select2-container--open .select2-selection--single .select2-selection__arrow b,
.select2-container--bootstrap4.select2-container--open .select2-selection--single .select2-selection__arrow b {
  border-color: transparent transparent #2563eb transparent !important;
  border-width: 0 4.5px 6px 4.5px !important;
}

.select2-container .select2-selection--single .select2-selection__clear,
.select2-container--default .select2-selection--single .select2-selection__clear,
.select2-container--bootstrap4 .select2-selection--single .select2-selection__clear {
  color: #94a3b8 !important;
  font-size: 1.15rem !important;
  font-weight: 600 !important;
  margin-right: 20px !important;
  line-height: 1 !important;
  float: right !important;
}

.select2-container .select2-selection--single .select2-selection__clear:hover,
.select2-container--default .select2-selection--single .select2-selection__clear:hover,
.select2-container--bootstrap4 .select2-selection--single .select2-selection__clear:hover {
  color: #ef4444 !important;
}

.select2-container .select2-selection--multiple,
.select2-container--default .select2-selection--multiple,
.select2-container--bootstrap4 .select2-selection--multiple {
  min-height: 42px !important;
  padding: 4px 8px !important;
  border: 1.5px solid #d1d5db !important;
  border-radius: 8px !important;
}

.select2-dropdown {
  border: 1.5px solid #d1d5db !important;
  border-radius: 8px !important;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05) !important;
  overflow: hidden !important;
  z-index: 1060 !important;
}

.select2-container--default .select2-search--dropdown {
  padding: 8px 10px !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field {
  border: 1.5px solid #d1d5db !important;
  border-radius: 6px !important;
  padding: 6px 12px !important;
  font-size: 0.88rem !important;
  outline: none !important;
}

.select2-container--default .select2-search--dropdown .select2-search__field:focus {
  border-color: #2563eb !important;
  box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2) !important;
}

.select2-container--default .select2-results__option {
  padding: 8px 14px !important;
  font-size: 0.88rem !important;
  color: #334155 !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
  background-color: #2563eb !important;
  color: #ffffff !important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
  background-color: #eff6ff !important;
  color: #1d4ed8 !important;
  font-weight: 600 !important;
}

/* ─── Admin Status Cards / Chips (Tanpa Status 'Baru') ─── */
.admin-status-cards {
  display: grid;
  grid-template-columns: repeat(5, minmax(0, 1fr));
  gap: 10px;
}

.admin-status-item {
  position: relative;
}

.admin-status-input {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
  pointer-events: none;
}

.admin-status-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 14px;
  background: #ffffff;
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 0;
  user-select: none;
  min-height: 48px;
}

.admin-status-card:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
  transform: translateY(-1px);
}

.admin-status-content {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
}

.admin-status-icon {
  font-size: 0.95rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.admin-status-name {
  font-size: 0.84rem;
  font-weight: 600;
  color: #334155;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.admin-status-check {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  border: 1.5px solid #cbd5e1;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  color: transparent;
  flex-shrink: 0;
  margin-left: 6px;
  transition: all 0.2s ease;
}

/* Status Theme Variants */
.admin-status-card.status-menunggu .admin-status-icon { color: #d97706; }
.admin-status-card.status-aktif .admin-status-icon { color: #16a34a; }
.admin-status-card.status-nonaktif .admin-status-icon { color: #64748b; }
.admin-status-card.status-suspend .admin-status-icon { color: #dc2626; }
.admin-status-card.status-bebas .admin-status-icon { color: #7c3aed; }

/* Status Active/Checked States */
.admin-status-input:checked + .admin-status-card {
  box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.08);
}

.admin-status-input:checked + .admin-status-card.status-menunggu {
  border-color: #d97706;
  background: #fffbeb;
}
.admin-status-input:checked + .admin-status-card.status-menunggu .admin-status-check {
  border-color: #d97706;
  background: #d97706;
  color: #ffffff;
}
.admin-status-input:checked + .admin-status-card.status-menunggu .admin-status-name {
  color: #b45309;
}

.admin-status-input:checked + .admin-status-card.status-aktif {
  border-color: #16a34a;
  background: #f0fdf4;
}
.admin-status-input:checked + .admin-status-card.status-aktif .admin-status-check {
  border-color: #16a34a;
  background: #16a34a;
  color: #ffffff;
}
.admin-status-input:checked + .admin-status-card.status-aktif .admin-status-name {
  color: #15803d;
}

.admin-status-input:checked + .admin-status-card.status-nonaktif {
  border-color: #64748b;
  background: #f8fafc;
}
.admin-status-input:checked + .admin-status-card.status-nonaktif .admin-status-check {
  border-color: #64748b;
  background: #64748b;
  color: #ffffff;
}
.admin-status-input:checked + .admin-status-card.status-nonaktif .admin-status-name {
  color: #334155;
}

.admin-status-input:checked + .admin-status-card.status-suspend {
  border-color: #dc2626;
  background: #fef2f2;
}
.admin-status-input:checked + .admin-status-card.status-suspend .admin-status-check {
  border-color: #dc2626;
  background: #dc2626;
  color: #ffffff;
}
.admin-status-input:checked + .admin-status-card.status-suspend .admin-status-name {
  color: #b91c1c;
}

.admin-status-input:checked + .admin-status-card.status-bebas {
  border-color: #7c3aed;
  background: #f5f3ff;
}
.admin-status-input:checked + .admin-status-card.status-bebas .admin-status-check {
  border-color: #7c3aed;
  background: #7c3aed;
  color: #ffffff;
}
.admin-status-input:checked + .admin-status-card.status-bebas .admin-status-name {
  color: #6d28d9;
}

/* ─── Responsive Queries ─── */
@media (max-width: 992px) {
  .admin-status-cards {
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
  }
}

@media (max-width: 768px) {
  .admin-choice-cards {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  
  .admin-choice-card {
    padding: 12px 14px;
  }
}

@media (max-width: 576px) {
  .admin-status-cards {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* ─── Legacy Accordion Styling & Animation ─── */
.legacy-accordion-card {
  border: 1px solid #e2e8f0 !important;
  border-radius: 10px !important;
  overflow: hidden;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
}

.legacy-accordion-header {
  cursor: pointer;
  background-color: #ffffff;
  padding: 13px 18px;
  user-select: none;
  transition: background-color 0.15s ease;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.legacy-accordion-header:hover {
  background-color: #f8fafc;
}

.legacy-chevron-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  background: #f1f5f9;
  color: #64748b;
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.2s ease, color 0.2s ease;
}

.legacy-accordion-header:hover .legacy-chevron-icon {
  background: #e2e8f0;
  color: #1e293b;
}

.legacy-accordion-header.is-open .legacy-chevron-icon {
  transform: rotate(180deg);
  background: #dbeafe;
  color: #2563eb;
}
</style>

<form id="frm" method="post" action="<?= base_url('anggota/edit/' . $anggota->ID) ?>">
  <input type="hidden" id="IdentityType_id" name="IdentityType_id" value="<?= esc($anggota->IdentityType_id ?? ($nip_id ?? '')) ?>">

  <div class="form-group mt-1 mb-3">
    <?php if (!$is_anggota) : ?>
      <a target="_blank" href="<?= base_url('anggota/printanggota/' . $anggota->ID . '?slug=template-1'); ?>" data-toggle="tooltip" data-placement="top" title="Cetak Kartu" class="btn btn-primary"><i class="fa fa-print"></i> Cetak Kartu Anggota
      </a>
      <a href="javascript:void(0);" data-href="<?= base_url('anggota/printkartubelakang/' . $anggota->ID . '?slug=template-2'); ?>" data-toggle="tooltip" data-placement="top" title="Cetak Kartu Belakang" class="btn btn-outline-primary remove-data"><i class="fa fa-print"></i> Cetak Kartu Belakang
      </a>
      <a href="javascript:void(0);" data-href="<?= base_url('anggota/bebaspustaka/' . $anggota->ID); ?>" data-toggle="tooltip" data-placement="top" title="Cetak Bebas Pustaka" class="btn btn-outline-secondary cetak-kartu"><i class="fa fa-file-text"></i> Cetak Bebas Pustaka
      </a>
    <?php endif; ?>
  </div>

  <!-- ============================================================== -->
  <!-- 01. DATA IDENTITAS & KEPEGAWAIAN (SESUAI PENDAFTARAN ONLINE) -->
  <!-- ============================================================== -->
  <div class="card mb-3 shadow-sm border-0">
    <div class="card-header bg-light d-flex align-items-center">
      <i class="fa fa-id-card text-primary mr-2"></i>
      <h6 class="m-0 font-weight-bold text-dark">01. Data Identitas & Kepegawaian (Pendaftaran Online)</h6>
    </div>
    <div class="card-body">
      <!-- Kategori Keanggotaan Interactive Cards (Simple & Clean) -->
      <div class="form-group mb-3">
        <label class="font-weight-bold d-block mb-2">Kategori Keanggotaan <span class="text-danger">*</span></label>
        <div class="admin-choice-cards" id="adminJenisAnggotaCards">
          <?php foreach (($jenis_anggota_list ?? []) as $row) : 
              $jaLower = strtolower(trim($row->jenisanggota));
              $isAsn = stripos($jaLower, 'asn') !== false && stripos($jaLower, 'non') === false;
              $isChecked = ($row->id == $anggota->JenisAnggota_id);
          ?>
            <div class="admin-choice-card-item">
              <input type="radio" name="JenisAnggota_id" id="ja_adm_<?= $row->id ?>" 
                     value="<?= $row->id ?>" 
                     data-name="<?= esc($jaLower) ?>" 
                     class="admin-choice-input" 
                     <?= $isChecked ? 'checked' : '' ?> required>
              <label for="ja_adm_<?= $row->id ?>" class="admin-choice-card <?= $isAsn ? 'is-asn' : 'is-nonasn' ?>">
                <div class="admin-choice-content">
                  <div class="admin-choice-icon <?= $isAsn ? 'icon-asn' : 'icon-nonasn' ?>">
                    <?php if ($isAsn) : ?>
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M3 10h18M5 10v11M9 10v11M15 10v11M19 10v11M12 2L2 7h20L12 2z"/>
                      </svg>
                    <?php else : ?>
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                      </svg>
                    <?php endif; ?>
                  </div>
                  <div class="admin-choice-text">
                    <span class="admin-choice-title"><?= esc($row->jenisanggota) ?></span>
                    <span class="admin-choice-sub"><?= $isAsn ? 'Pegawai ASN (PNS & PPPK Kemnaker)' : 'Tenaga Non-ASN, Honorer & Magang' ?></span>
                  </div>
                </div>
                <div class="admin-choice-radio-indicator">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                  </svg>
                </div>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <small class="form-text text-muted mt-1">Pilih kategori kepegawaian pemustaka untuk penyesuaian otomatis validasi NIP atau NIK.</small>
      </div>

      <div class="form-row">
        <!-- Nomor Identitas (NIP / NIK) -->
        <div class="col-md-6 mb-3 form-group">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="IdentityNo" id="identityLabel" class="font-weight-bold m-0">Nomor Induk Pegawai (NIP) <span class="text-danger">*</span></label>
            <span class="badge badge-secondary" id="digitBadge">0 / 18 digit</span>
          </div>
          <input type="text" class="form-control" id="IdentityNo" name="IdentityNo" 
                 placeholder="Masukkan 18 digit NIP resmi BKN" 
                 value="<?= set_value('IdentityNo', $anggota->IdentityNo ?? ''); ?>" 
                 maxlength="18" inputmode="numeric" required />
          <small class="form-text text-muted" id="identityHintText">NIP 18 digit resmi BKN tanpa spasi.</small>
        </div>

        <!-- Nama Lengkap -->
        <div class="col-md-6 mb-3 form-group">
          <label for="Fullname" class="font-weight-bold">Nama Lengkap (Tanpa Gelar) <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="Fullname" name="Fullname" 
                 placeholder="Contoh: Raden Satria Pamungkas" 
                 value="<?= set_value('Fullname', $anggota->Fullname ?? ''); ?>" required />
          <small class="form-text text-muted">Nama pemustaka sesuai kartu tanda pengenal kedinasan / KTP.</small>
        </div>
      </div>

      <div class="form-row">
        <!-- Satuan Kerja / Unit Kerja -->
        <div class="col-md-12 mb-2 form-group">
          <label for="UnitKerja_id" class="font-weight-bold">Satuan Kerja / Unit Eselon I Penempatan <span class="text-danger">*</span></label>
          <select class="form-control select2" name="UnitKerja_id" id="UnitKerja_id" style="width: 100%;" required>
            <option value="">Pilih Satuan Kerja / Unit Eselon I</option>
            <?php foreach (($unit_kerja ?? []) as $uk) : ?>
              <option value="<?= $uk->id ?>" <?= ($uk->id == ($anggota->UnitKerja_id ?? '')) ? 'selected' : '' ?>>
                <?= esc($uk->nama_unit) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small class="form-text text-muted">Direktorat / Satuan Kerja penempatan di Kementerian Ketenagakerjaan.</small>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 02. KONTAK & DOMISILI (SESUAI PENDAFTARAN ONLINE) -->
  <!-- ============================================================== -->
  <div class="card mb-3 shadow-sm border-0">
    <div class="card-header bg-light d-flex align-items-center">
      <i class="fa fa-envelope text-primary mr-2"></i>
      <h6 class="m-0 font-weight-bold text-dark">02. Kontak & Alamat Domisili (Pendaftaran Online)</h6>
    </div>
    <div class="card-body">
      <div class="form-row">
        <!-- Email Aktif -->
        <div class="col-md-6 mb-3 form-group">
          <label for="Email" class="font-weight-bold">Alamat Email Aktif <span class="text-danger">*</span></label>
          <input type="email" class="form-control" id="Email" name="Email" 
                 placeholder="nama@kemnaker.go.id" 
                 value="<?= set_value('Email', $anggota->Email ?? ''); ?>" required />
          <small class="form-text text-muted">Surel aktif untuk pengiriman notifikasi dan bukti registrasi.</small>
        </div>

        <!-- Nomor WhatsApp / HP -->
        <div class="col-md-6 mb-3 form-group">
          <label for="Phone" class="font-weight-bold">Nomor WhatsApp / HP Aktif <span class="text-danger">*</span></label>
          <input type="text" class="form-control" id="Phone" name="Phone" 
                 placeholder="081234567890" 
                 value="<?= set_value('Phone', $anggota->Phone ?? ''); ?>" required />
          <small class="form-text text-muted">Nomor telepon aktif dengan awalan 08.</small>
        </div>
      </div>

      <div class="form-row">
        <!-- Alamat Domisili -->
        <div class="col-md-12 mb-2 form-group">
          <label for="AddressNow" class="font-weight-bold">Alamat Domisili Saat Ini <span class="text-danger">*</span></label>
          <textarea class="form-control" id="AddressNow" name="AddressNow" rows="2" 
                    placeholder="Alamat lengkap tempat tinggal pemustaka saat ini" required><?= set_value('AddressNow', $anggota->AddressNow ?? ''); ?></textarea>
          <small class="form-text text-muted">Alamat domisili lengkap untuk keperluan kearsipan dan kartu perpustakaan.</small>
        </div>
      </div>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 03. PENGATURAN KEANGGOTAAN & PAS FOTO (KHUSUS ADMIN) -->
  <!-- ============================================================== -->
  <div class="card mb-3 shadow-sm border-0">
    <div class="card-header bg-light d-flex align-items-center">
      <i class="fa fa-cogs text-primary mr-2"></i>
      <h6 class="m-0 font-weight-bold text-dark">03. Pengaturan Perpustakaan & Pas Foto (Khusus Admin)</h6>
    </div>
    <div class="card-body">
      <!-- Status Anggota Interactive Choice Cards (Tanpa Status 'Baru') -->
      <div class="form-group mb-4">
        <label class="font-weight-bold d-block mb-2">Status Keanggotaan <span class="text-danger">*</span></label>
        <div class="admin-status-cards" id="adminStatusCards">
          <?php foreach (($status_anggota_list ?? []) as $st) : 
              $stName = trim($st->Nama);
              $stLower = strtolower($stName);
              
              // Hapus status 'Baru' dari pilihan sesuai permintaan revisi
              if ($stLower === 'baru' || $st->id == 1) {
                  continue;
              }

              // Jika data anggota saat ini berstatus 'Baru' (ID 1), arahkan ke 'Menunggu' (ID 2)
              $currentStatusId = ($anggota->StatusAnggota_id == 1) ? 2 : $anggota->StatusAnggota_id;
              $isChecked = ($st->id == $currentStatusId);
              
              // Color & SVG mapping per status
              $themeClass = 'status-default';
              if (stripos($stLower, 'menunggu') !== false) {
                  $themeClass = 'status-menunggu';
              } elseif (stripos($stLower, 'aktif') !== false && stripos($stLower, 'non') === false) {
                  $themeClass = 'status-aktif';
              } elseif (stripos($stLower, 'non') !== false) {
                  $themeClass = 'status-nonaktif';
              } elseif (stripos($stLower, 'suspend') !== false) {
                  $themeClass = 'status-suspend';
              } elseif (stripos($stLower, 'bebas') !== false) {
                  $themeClass = 'status-bebas';
              }
          ?>
            <div class="admin-status-item">
              <input type="radio" name="StatusAnggota_id" id="st_adm_<?= $st->id ?>" 
                     value="<?= $st->id ?>" 
                     class="admin-status-input" 
                     <?= $isChecked ? 'checked' : '' ?> required>
              <label for="st_adm_<?= $st->id ?>" class="admin-status-card <?= $themeClass ?>">
                <div class="admin-status-content">
                  <span class="admin-status-icon">
                    <?php if ($themeClass === 'status-menunggu') : ?>
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                      </svg>
                    <?php elseif ($themeClass === 'status-aktif') : ?>
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                      </svg>
                    <?php elseif ($themeClass === 'status-nonaktif') : ?>
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                      </svg>
                    <?php elseif ($themeClass === 'status-suspend') : ?>
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                      </svg>
                    <?php elseif ($themeClass === 'status-bebas') : ?>
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                      </svg>
                    <?php else : ?>
                      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                      </svg>
                    <?php endif; ?>
                  </span>
                  <span class="admin-status-name"><?= esc($st->Nama) ?></span>
                </div>
                <div class="admin-status-check">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                  </svg>
                </div>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
        <small class="form-text text-muted mt-1">Status validasi akun pemustaka untuk akses sirkulasi dan peminjaman buku perpustakaan.</small>
      </div>

      <div class="form-row">
        <!-- Nomor Anggota / Barcode -->
        <div class="col-md-4 mb-3 form-group">
          <label for="MemberNo" class="font-weight-bold">Nomor Anggota / Barcode <span class="text-danger">*</span></label>
          <input type="text" class="form-control font-weight-bold" id="MemberNo" name="MemberNo" 
                 value="<?= set_value('MemberNo', $anggota->MemberNo ?? ''); ?>" required />
          <small class="form-text text-muted">Nomor fisik kartu anggota atau barcode sirkulasi.</small>
        </div>

        <!-- Tanggal Register -->
        <div class="col-md-4 mb-3 form-group">
          <label for="RegisterDate" class="font-weight-bold">Tanggal Registrasi</label>
          <input type="text" class="form-control" id="RegisterDate" name="RegisterDate" 
                 value="<?= substr(($anggota->RegisterDate ?? date('Y-m-d')), 0, 10) ?>" readonly />
          <small class="form-text text-muted">Tanggal akun pertama kali dibuat.</small>
        </div>

        <!-- Tanggal Berakhir -->
        <div class="col-md-4 mb-3 form-group">
          <label for="EndDate" class="font-weight-bold">Tanggal Masa Berlaku Kartu</label>
          <input type="text" class="form-control" id="EndDate" name="EndDate" 
                 value="<?= substr(($anggota->EndDate ?? date('Y-m-d', strtotime('+1 year'))), 0, 10) ?>" readonly />
          <small class="form-text text-muted">Masa aktif kartu (1 tahun sejak disetujui).</small>
        </div>
      </div>

      <!-- Hak Akses Koleksi & Lokasi -->
      <div class="form-row">
        <div class="col-md-6 mb-3 form-group">
          <label for="CategoryLoan_id" class="font-weight-bold">Hak Akses Koleksi Pinjaman</label>
          <select class="form-control select2" id="CategoryLoan_id" name="CategoryLoan_id[]" multiple="multiple" style="width:100%">
            <?php foreach (get_ref_table('collectioncategorys', 'id, Name', null, 'data') as $row) : ?>
              <option value="<?= $row->id ?>" <?= in_array($row->id, $arr_hak_akses_koleksi ?? []) ? 'selected' : '' ?>>
                <?= esc($row->Name) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 mb-3 form-group">
          <label for="LocationLoan_id" class="font-weight-bold">Lokasi Perpustakaan</label>
          <select class="form-control select2" name="LocationLoan_id[]" multiple="multiple" style="width:100%">
            <?php foreach (get_ref_table('location_library', 'ID, Name', null, 'data') as $row) : ?>
              <option value="<?= $row->ID ?>" <?= in_array($row->ID, $arr_hak_akses_lokasi ?? []) ? 'selected' : '' ?>>
                <?= esc($row->Name) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Pas Foto Component -->
      <div class="border-top pt-3 mt-2">
        <label class="font-weight-bold mb-2"><i class="fa fa-camera mr-1"></i> Pas Foto Pemustaka</label>
        <?= $this->include("Anggota\Views\section\component_update\upload_foto"); ?>
      </div>
    </div>
  </div>

  <!-- ============================================================== -->
  <!-- 04. DATA TAMBAHAN LEGACY INLISLITE (OPSIONAL / TERLINDUNGI) -->
  <!-- ============================================================== -->
  <div class="card mb-3 legacy-accordion-card">
    <div class="legacy-accordion-header" id="headingLegacy" onclick="toggleLegacyAccordion()" role="button" tabindex="0">
      <div class="d-flex align-items-center font-weight-bold text-dark" style="font-size: 0.95rem;">
        <svg class="mr-2 text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="21 8 21 21 3 21 3 8"/>
          <rect x="1" y="3" width="22" height="5"/>
          <line x1="10" y1="12" x2="14" y2="12"/>
        </svg>
        <span>Data Tambahan Lainnya (Opsional / Legacy INLISLite)</span>
      </div>
      <div class="legacy-chevron-icon">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="6 9 12 15 18 9"/>
        </svg>
      </div>
    </div>
    <div id="collapseLegacy" style="display: none;">
      <div class="card-body bg-light border-top">
        <p class="text-muted small mb-3">Data di bawah ini tersimpan di database bawaan INLISLite dan dapat disesuaikan jika diperlukan secara khusus:</p>
        <div class="form-row">
          <div class="col-md-4 mb-2">
            <label class="small">Tempat Lahir</label>
            <input type="text" class="form-control form-control-sm" name="PlaceOfBirth" value="<?= set_value('PlaceOfBirth', $anggota->PlaceOfBirth ?? ''); ?>" placeholder="Kota Kelahiran">
          </div>
          <div class="col-md-4 mb-2">
            <label class="small">Tanggal Lahir</label>
            <input type="date" class="form-control form-control-sm" name="DateOfBirth" value="<?= substr($anggota->DateOfBirth ?? '', 0, 10); ?>">
          </div>
          <div class="col-md-4 mb-2">
            <label class="small">Jenis Kelamin</label>
            <select class="form-control form-control-sm" name="Sex_id">
              <option value="">Pilih</option>
              <?php foreach (get_table('jenis_kelamin', 'ID, Name', null, 'data') as $jk) : ?>
                <option value="<?= $jk->ID ?>" <?= ($jk->ID == ($anggota->Sex_id ?? '')) ? 'selected' : '' ?>><?= esc($jk->Name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 mb-2">
            <label class="small">Alamat KTP (Identitas)</label>
            <input type="text" class="form-control form-control-sm" name="Address" value="<?= set_value('Address', $anggota->Address ?? ''); ?>" placeholder="Alamat sesuai KTP">
          </div>
          <div class="col-md-6 mb-2">
            <label class="small">Nama Ibu Kandung</label>
            <input type="text" class="form-control form-control-sm" name="MotherMaidenName" value="<?= set_value('MotherMaidenName', $anggota->MotherMaidenName ?? ''); ?>" placeholder="Nama Ibu Kandung">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Audit Trail Box -->
  <div class="table-responsive my-3">
    <table class="table table-sm table-bordered table-striped mb-0 text-muted small">
      <tbody>
        <tr>
          <th width="160" scope="row">Dibuat Oleh</th>
          <td><?= esc($CreateBy ?? '-') ?></td>
          <th width="160" scope="row">Diperbarui Oleh</th>
          <td><?= esc($UpdateBy ?? '-') ?></td>
        </tr>
        <tr>
          <th scope="row">Dibuat Pada</th>
          <td><?= esc($anggota->CreateDate ?? '-') ?></td>
          <th scope="row">Diperbarui Pada</th>
          <td><?= esc($anggota->UpdateDate ?? '-') ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="form-group mt-3 text-right">
    <a href="<?= base_url('anggota') ?>" class="btn btn-secondary mr-2">
      <i class="fa fa-arrow-left"></i> Kembali ke Daftar
    </a>
    <button type="submit" class="btn btn-primary px-4" id="btn-submit" name="submit">
      <i class="fa fa-save"></i> Simpan Perubahan
    </button>
  </div>
</form>

<script>
// Global Vanilla Toggle Handler (Zero Dependency, Bekerja Langsung)
function toggleLegacyAccordion() {
    var content = document.getElementById('collapseLegacy');
    var header = document.getElementById('headingLegacy');
    if (!content) return;

    if (window.jQuery) {
        var $content = window.jQuery(content);
        var $header = window.jQuery(header);
        $content.stop(true, true).slideToggle(220, function() {
            if ($content.is(':visible')) {
                $header.addClass('is-open');
            } else {
                $header.removeClass('is-open');
            }
        });
    } else {
        var isHidden = content.style.display === 'none' || getComputedStyle(content).display === 'none';
        content.style.display = isHidden ? 'block' : 'none';
        if (header) {
            if (isHidden) {
                header.classList.add('is-open');
            } else {
                header.classList.remove('is-open');
            }
        }
    }
}

// Setup interaksi form ketika seluruh DOM & script library selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    var nipId = '<?= $nip_id ?? "" ?>';
    var nikId = '<?= $nik_id ?? "" ?>';

    function initFormInteractions() {
        if (typeof jQuery === 'undefined') return;
        var $ = jQuery;

        function syncEditJenisAnggota() {
            var selectedOpt = $('input[name="JenisAnggota_id"]:checked');
            var text = (selectedOpt.data('name') || selectedOpt.siblings('label').find('.admin-choice-title').text() || '').toLowerCase();
            var isAsn = text.includes('asn') && !text.includes('non');

            if (isAsn) {
                $('#identityLabel').html('Nomor Induk Pegawai (NIP) <span class="text-danger">*</span>');
                $('#IdentityNo').attr('placeholder', 'Masukkan 18 digit NIP resmi BKN').attr('maxlength', '18');
                $('#IdentityType_id').val(nipId);
                $('#identityHintText').text('Nomor Induk Pegawai 18 digit resmi BKN tanpa spasi.');
            } else {
                $('#identityLabel').html('Nomor Induk Kependudukan (NIK) <span class="text-danger">*</span>');
                $('#IdentityNo').attr('placeholder', 'Masukkan 16 digit NIK KTP').attr('maxlength', '16');
                $('#IdentityType_id').val(nikId);
                $('#identityHintText').text('Nomor Induk Kependudukan 16 digit resmi tanpa spasi.');
            }
            updateEditDigitCount();
        }

        function updateEditDigitCount() {
            var val = ($('#IdentityNo').val() || '').replace(/\D/g, '');
            $('#IdentityNo').val(val);
            var selectedOpt = $('input[name="JenisAnggota_id"]:checked');
            var text = (selectedOpt.data('name') || selectedOpt.siblings('label').find('.admin-choice-title').text() || '').toLowerCase();
            var maxDigits = (text.includes('asn') && !text.includes('non')) ? 18 : 16;
            
            $('#digitBadge').text(val.length + ' / ' + maxDigits + ' digit');
            if (val.length === maxDigits) {
                $('#digitBadge').removeClass('badge-secondary badge-warning').addClass('badge-success');
            } else if (val.length > 0) {
                $('#digitBadge').removeClass('badge-secondary badge-success').addClass('badge-warning');
            } else {
                $('#digitBadge').removeClass('badge-success badge-warning').addClass('badge-secondary');
            }
        }

        $('input[name="JenisAnggota_id"]').on('change', syncEditJenisAnggota);
        $('#IdentityNo').on('input', updateEditDigitCount);
        
        // Inisialisasi awal
        syncEditJenisAnggota();

        // Select2 Unit Kerja
        if ($.fn.select2) {
            $('#UnitKerja_id').select2({
                placeholder: 'Pilih Satuan Kerja / Unit Eselon I',
                allowClear: true,
                width: '100%'
            });
        }
    }

    if (window.jQuery) {
        initFormInteractions();
    } else {
        window.addEventListener('load', initFormInteractions);
    }
});
</script>