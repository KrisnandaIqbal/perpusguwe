<!-- Modal Edit Sub-Unit Kerja -->
<div class="modal fade" id="modal_edit_subunit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-edit text-primary mr-1"></i> Ubah Sub-Unit Kerja
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_edit_subunit" method="post">
                <input type="hidden" id="edit_subunit_id" name="id">
                <div class="modal-body">
                    <div id="frm_edit_subunit_alert"></div>

                    <div id="edit_subunit_member_notice" class="alert alert-info py-2 px-3 small mb-3" style="display:none;">
                        <i class="fa fa-info-circle mr-1"></i> Sub-unit ini sudah terhubung dengan data anggota. Perubahan nama akan otomatis merefleksikan seluruh tampilan data anggota terkait.
                    </div>

                    <div class="form-group">
                        <label for="edit_unit_id" class="font-weight-bold">Unit Kerja Induk (Eselon I) <span class="text-danger">*</span></label>
                        <select class="form-control" id="edit_unit_id" name="unit_id" required>
                            <option value="">-- Pilih Unit Kerja Induk --</option>
                            <?php foreach ($units as $u): ?>
                                <option value="<?= $u->id ?>"><?= esc($u->nama_unit) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit_nama_subunit" class="font-weight-bold">Nama Sub-Unit (Biro / Direktorat / Unit) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_subunit" name="nama_subunit" required>
                    </div>

                    <div class="form-row">
                        <div class="col-md-6 form-group">
                            <label for="edit_sort" class="font-weight-bold">Nomor Urut</label>
                            <input type="number" class="form-control" id="edit_sort" name="sort" min="1" max="999">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="edit_active" class="font-weight-bold">Status Aktif</label>
                            <select class="form-control" id="edit_active" name="active">
                                <option value="1">Aktif (Tampil di form)</option>
                                <option value="0">Nonaktif (Disembunyikan)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveEditSubunit"><i class="fa fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Unit Kerja Induk -->
<div class="modal fade" id="modal_edit_unit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold">
                    <i class="fa fa-building text-primary mr-1"></i> Ubah Unit Kerja Induk
                </h5>
                <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_edit_unit" method="post">
                <input type="hidden" id="edit_unit_id_val" name="id">
                <div class="modal-body">
                    <div id="frm_edit_unit_alert"></div>

                    <div class="form-group">
                        <label for="edit_nama_unit" class="font-weight-bold">Nama Unit Kerja Eselon I <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_unit" name="nama_unit" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_unit_active" class="font-weight-bold">Status Aktif</label>
                        <select class="form-control" id="edit_unit_active" name="active">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSaveEditUnit"><i class="fa fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>