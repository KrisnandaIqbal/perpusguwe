<!-- Modal Tambah Sub-Unit Kerja -->
<div class="modal fade" id="modal_create_subunit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-plus-circle text-success mr-1"></i> Tambah Sub-Unit Kerja
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_add_subunit" method="post">
                <div class="modal-body">
                    <div id="frm_add_subunit_alert"></div>

                    <div class="form-group">
                        <label for="add_unit_id" class="font-weight-bold">Unit Kerja Induk (Eselon I) <span class="text-danger">*</span></label>
                        <select class="form-control" id="add_unit_id" name="unit_id" required>
                            <option value="">-- Pilih Unit Kerja Induk --</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= $u->id ?>"><?= esc($u->nama_unit) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="add_nama_subunit" class="font-weight-bold">Nama Sub-Unit (Biro / Direktorat / Unit) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_nama_subunit" name="nama_subunit" placeholder="Contoh: Biro Keuangan dan BMN" required>
                        <small class="form-text text-muted">Gunakan ejaan baku resmi tanpa singkatan.</small>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label for="add_sort" class="font-weight-bold">Nomor Urut Tampilan</label>
                            <input type="number" class="form-control" id="add_sort" name="sort" value="1" min="1" max="999">
                            <small class="form-text text-muted">Urutan posisi dalam dropdown.</small>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="add_active" class="font-weight-bold">Status Aktif</label>
                            <select class="form-control" id="add_active" name="active">
                                <option value="1">Aktif (Tampil di form)</option>
                                <option value="0">Nonaktif (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveAddSubunit"><i class="fa fa-save"></i> Simpan Sub-Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>