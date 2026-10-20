<?php helper('anggota'); ?>
<style>
    /* Hilangkan bayangan aneh 100vh dari modal-dialog tema & posisikan persis di tengah */
    #modal_approval .modal-dialog {
        max-width: 620px;
        box-shadow: none !important;
        background: transparent !important;
    }
    #modal_approval .modal-dialog-centered {
        display: flex !important;
        align-items: center !important;
        min-height: calc(100% - 2rem) !important;
        min-height: calc(100vh - 2rem) !important;
        margin: 1rem auto !important;
    }
    #modal_approval .modal-dialog::before,
    #modal_approval .modal-dialog::after {
        display: none !important;
        content: none !important;
        height: 0 !important;
        width: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
    }
    #modal_approval .modal-content {
        max-height: calc(100vh - 2rem);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
        border: 1px solid rgba(0, 0, 0, 0.12) !important;
        border-radius: 8px !important;
        overflow: hidden;
        background-color: #fff !important;
    }
    #modal_approval .modal-body {
        overflow-y: auto;
    }

    /* Layout Data Pendaftar (Langkah 1 & Langkah 3A) */
    #modal_approval .approval-data { display: grid; grid-template-columns: 120px 1fr; gap: 8px 14px; margin: 0; }
    #modal_approval .approval-data dt { font-weight: normal; color: #495057; font-size: 13.5px; }
    #modal_approval .approval-data dd { margin: 0; font-weight: 600; word-break: break-word; font-size: 13.5px; }
    #modal_approval .approval-photo { width: 90px; height: 135px; aspect-ratio: 2/3; object-fit: cover; border-radius: 6px; flex-shrink: 0; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.15); transition: transform 0.2s ease; }
    #modal_approval .approval-photo:hover { transform: scale(1.03); }

    /* Pas Foto Portrait 2:3 pada Langkah 2 (Penolakan) */
    #modal_approval .approval-reject-photobox {
        width: 56px;
        height: 84px;
        aspect-ratio: 2/3;
        flex-shrink: 0;
        border-radius: 4px;
        overflow: hidden;
        border: 1px solid #ced4da;
        box-shadow: 0 1px 4px rgba(0,0,0,0.12);
        background: #f1f3f5;
        cursor: pointer;
        position: relative;
    }
    #modal_approval .approval-reject-photobox:hover {
        border-color: #adb5bd;
    }
    #modal_approval #approval_reject_photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
    #modal_approval #approval_reject_photo_placeholder {
        width: 100%;
        height: 100%;
        display: none;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 10px;
        text-align: center;
        line-height: 1.2;
    }

    #modal_approval .modal-footer .btn { min-height: 38px; min-width: 96px; font-size: 13.5px; }
    #modal_approval .form-control { min-height: 36px; }

    /* Grid 2 Kolom untuk 9 Alasan Resmi */
    #modal_approval .approval-alasan-grid,
    #modal_approval .approval-status-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 14px;
        padding: 10px 12px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
    }
    #modal_approval .approval-alasan-grid .form-check,
    #modal_approval .approval-status-grid .form-check {
        margin-bottom: 0;
        padding-left: 1.6em;
    }
    #modal_approval .approval-alasan-grid .form-check-input,
    #modal_approval .approval-status-grid .form-check-input {
        cursor: pointer;
        margin-top: 0.25em;
    }
    #modal_approval .approval-alasan-grid .form-check-label,
    #modal_approval .approval-status-grid .form-check-label {
        cursor: pointer;
        font-size: 13px;
        line-height: 1.35;
        color: #212529;
    }
    #modal_approval .approval-status-grid .form-check-input:disabled + .form-check-label {
        cursor: not-allowed;
        color: #adb5bd;
    }
    @media (max-width: 575px) {
        #modal_approval .modal-dialog { max-width: calc(100% - 1rem); margin: 0.5rem auto; }
        #modal_approval .approval-body { flex-direction: column; }
        #modal_approval .approval-data { grid-template-columns: 1fr; gap: 2px; }
        #modal_approval .approval-data dd { margin-bottom: 8px; }
        #modal_approval .approval-alasan-grid,
        #modal_approval .approval-status-grid { grid-template-columns: 1fr; gap: 8px; }
        #modal_approval .modal-footer .btn { flex: 1 1 100%; }
    }
</style>

<div class="modal fade" id="modal_approval" tabindex="-1" role="dialog" aria-labelledby="modal_approval_title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header py-2 px-3 bg-light border-bottom">
                <h5 class="modal-title font-weight-bold text-dark" id="modal_approval_title" style="font-size: 16px;">Periksa pendaftaran ini</h5>
                <button type="button" class="close approval-close" data-bs-dismiss="modal" aria-label="Tutup" style="padding: 0.5rem 0.75rem; margin: -0.5rem -0.5rem -0.5rem auto; border: none; background: transparent; font-size: 1.4rem; line-height: 1; opacity: 0.6; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body py-3 px-3">
                <p id="approval_loading" class="mb-0 text-muted">Memuat data anggota...</p>
                <div id="approval_error" class="alert alert-danger mb-0" role="alert" style="display:none">
                    Data anggota gagal dimuat, jadi belum bisa diputuskan dari sini. Tutup lalu coba lagi, atau buka form edit anggotanya.
                </div>
                <div id="approval_content" style="display:none">
                    <!-- Tampilan 1: Periksa Data Pendaftar Lengkap (Khusus Menunggu) -->
                    <div id="approval_inspect_view" style="display:none">
                        <div class="approval-body d-flex" style="gap:16px">
                            <img class="approval-photo" id="approval_photo" src="" alt="Foto pendaftar" title="Klik untuk melihat foto ukuran penuh" onclick="if(this.src && typeof openSimplePopup === 'function') openSimplePopup(this.src, 'Pas Foto Pendaftar');">
                            <dl class="approval-data">
                                <dt>Jenis anggota</dt><dd data-field="JenisAnggota"></dd>
                                <dt id="dt_approval_status_non_asn" style="display:none;">Status Non-ASN</dt><dd id="dd_approval_status_non_asn" data-field="StatusNonAsn" style="display:none;"></dd>
                                <dt data-field="JenisIdentitas"></dt><dd data-field="IdentityNo"></dd>
                                <dt>Nama lengkap</dt><dd data-field="Fullname"></dd>
                                <dt>Jenis kelamin</dt><dd data-field="JenisKelamin"></dd>
                                <dt>Unit kerja</dt><dd data-field="UnitKerja"></dd>
                                <dt>Alamat</dt><dd data-field="AddressNow"></dd>
                                <dt>Email</dt><dd data-field="Email"></dd>
                                <dt>No. HP</dt><dd data-field="Phone"></dd>
                                <dt>Tgl. daftar</dt><dd data-field="RegisterDate"></dd>
                                <dt id="dt_approval_masa_berlaku">Masa berlaku</dt><dd id="dd_approval_masa_berlaku" class="text-primary font-weight-bold">-</dd>
                            </dl>
                        </div>
                        <p id="approval_sebelumnya" class="alert alert-warning mt-3 mb-0" style="display:none"></p>

                        <!-- Banner Preview Masa Berlaku Sebelum Konfirmasi -->
                        <div id="approval_masa_berlaku_box" class="alert alert-info border mt-3 mb-0 py-2 px-3 text-dark d-flex align-items-center" style="display:none; font-size: 13px; line-height: 1.4; gap: 8px;">
                            <i class="fa fa-calendar-check-o text-info fa-lg"></i>
                            <div>
                                <span id="approval_masa_berlaku_text"></span>
                            </div>
                        </div>

                        <p id="approval_note" class="alert alert-light border mt-2 mb-0 py-2 px-3 text-muted" style="font-size: 13px; line-height: 1.4;">
                            <i class="fa fa-info-circle text-info mr-1"></i> Setelah disetujui, No. Anggota dibuat otomatis dan kartu anggota dikirim ke email pendaftar.
                        </p>
                    </div>

                    <!-- Tampilan 2: Formulir Belum Bisa Diterima (Khusus Penolakan & Hapus Data Pendaftar Menunggu) -->
                    <form id="approval_reject_form" style="display:none" novalidate>
                        <!-- Header Ringkas Identitas Pendaftar dengan Pas Foto 2:3 yang Proporsional -->
                        <div class="d-flex align-items-center p-2 mb-2 bg-light rounded border" style="gap: 14px;">
                            <div class="approval-reject-photobox" title="Klik untuk melihat pas foto ukuran penuh" onclick="var src=$('#approval_reject_photo').attr('src'); if(src && typeof openSimplePopup === 'function') openSimplePopup(src, 'Pas Foto Pendaftar');">
                                <img id="approval_reject_photo" src="" alt="Pas Foto">
                                <div id="approval_reject_photo_placeholder">
                                    <i class="fa fa-user fa-lg mb-1"></i>
                                    <span>Tanpa Foto</span>
                                </div>
                            </div>
                            <div style="min-width: 0; flex-grow: 1; font-size: 13px; line-height: 1.4;">
                                <div class="font-weight-bold text-dark text-truncate" id="approval_reject_nama" style="font-size: 15px; margin-bottom: 2px;"></div>
                                <div class="text-muted text-truncate mb-1">
                                    <span class="badge badge-secondary" id="approval_reject_jenis"></span>
                                    <span class="badge badge-info ml-1" id="approval_reject_status_non_asn" style="display:none;"></span>
                                    <span class="ml-1 font-weight-bold text-dark" id="approval_reject_identitas"></span>
                                </div>
                                <div class="text-muted text-truncate" id="approval_reject_unit_wrapper">
                                    <i class="fa fa-building text-secondary mr-1" style="width: 14px;"></i> <span id="approval_reject_unit"></span>
                                </div>
                                <div class="text-muted text-truncate">
                                    <i class="fa fa-envelope text-secondary mr-1" style="width: 14px;"></i> <span id="approval_reject_email"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Peringatan Merah -->
                        <div class="alert alert-danger py-2 px-3 mb-2" role="alert" style="font-size: 13px; line-height: 1.4;">
                            <i class="fa fa-exclamation-triangle mr-1"></i> <strong>Perhatian:</strong> Email akan dikirim dan data pendaftar dihapus permanen.
                        </div>

                        <!-- Checklist 9 Alasan (2 Kolom) -->
                        <div class="form-group mb-2">
                            <label class="d-block font-weight-bold mb-1" style="font-size: 13px;">Alasan belum bisa diterima <span class="text-danger">*</span> (pilih minimal satu)</label>
                            <div class="approval-alasan-grid">
                                <?php foreach (alasanPerbaikan() as $kunci => $alasan) : ?>
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input approval-alasan-cb" id="alasan_<?= esc($kunci, 'attr') ?>" name="alasan[]" value="<?= esc($kunci, 'attr') ?>">
                                        <label class="form-check-label" for="alasan_<?= esc($kunci, 'attr') ?>">
                                            <?= esc($alasan['label']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Catatan untuk Pendaftar (2 baris) -->
                        <div class="form-group mb-1">
                            <label for="approval_catatan" class="font-weight-bold mb-1" style="font-size: 13px;">Catatan untuk pendaftar <span id="approval_catatan_opsional" class="font-weight-normal text-muted">(opsional, maks 300 karakter)</span></label>
                            <textarea class="form-control py-1 px-2" id="approval_catatan" name="catatan" rows="2" maxlength="300" placeholder="Tuliskan catatan tambahan jika ada..." style="font-size: 13px;"></textarea>
                            <small class="form-text text-muted text-right mb-0"><span id="approval_catatan_count">0</span>/300 karakter</small>
                        </div>

                        <div id="approval_reject_error" class="alert alert-danger py-2 px-3 mb-0 mt-2" role="alert" style="display:none; font-size: 13px;"></div>
                    </form>

                    <!-- Tampilan 3A: Detail Data Anggota & Riwayat Email (Untuk Status Non-Menunggu) -->
                    <div id="approval_member_detail_view" style="display:none">
                        <div class="approval-body d-flex" style="gap:16px">
                            <img class="approval-photo" id="approval_member_photo" src="" alt="Foto anggota" title="Klik untuk melihat foto ukuran penuh" onclick="if(this.src && typeof openSimplePopup === 'function') openSimplePopup(this.src, 'Pas Foto Anggota');">
                            <dl class="approval-data">
                                <dt>No. Anggota</dt><dd id="approval_member_no" class="text-primary font-weight-bold"></dd>
                                <dt>Nama lengkap</dt><dd id="approval_member_fullname"></dd>
                                <dt>Jenis kelamin</dt><dd id="approval_member_jenis_kelamin"></dd>
                                <dt>Status saat ini</dt><dd id="approval_member_status_badge"></dd>
                                <dt>Jenis anggota</dt><dd id="approval_member_jenis"></dd>
                                <dt id="dt_member_status_non_asn" style="display:none;">Status Non-ASN</dt><dd id="approval_member_status_non_asn" style="display:none;"></dd>
                                <dt id="approval_member_identitas_label">Identitas</dt><dd id="approval_member_identitas_val"></dd>
                                <dt>Unit kerja</dt><dd id="approval_member_unit"></dd>
                                <dt>Alamat</dt><dd id="approval_member_alamat"></dd>
                                <dt>Email</dt><dd id="approval_member_email"></dd>
                                <dt>No. HP</dt><dd id="approval_member_phone"></dd>
                                <dt>Tgl. daftar</dt><dd id="approval_member_regdate"></dd>
                                <dt>Masa berlaku</dt><dd id="approval_member_enddate"></dd>
                            </dl>
                        </div>

                        <div id="approval_member_email_box" class="alert alert-light border mt-3 mb-0 py-2 px-3 d-flex align-items-center justify-content-between" style="font-size: 13px; line-height: 1.4; display:none;">
                            <div>
                                <i class="fa fa-envelope text-info mr-1"></i> <span id="approval_member_email_info"></span>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="approval_member_btn_resend_email" style="min-height: 30px; font-size: 12px;">
                                <i class="fa fa-paper-plane mr-1"></i> Kirim ulang email
                            </button>
                        </div>
                    </div>

                    <!-- Tampilan 3B: Formulir Ubah Status Anggota (Untuk Status Non-Menunggu) -->
                    <div id="approval_change_status_view" style="display:none">
                        <div class="p-2 mb-3 bg-light rounded border d-flex align-items-center justify-content-between" style="font-size: 13.5px;">
                            <div>
                                <span class="font-weight-bold text-dark" id="approval_status_target_name"></span>
                                <span class="text-muted ml-1">&bull; <span id="approval_status_target_no"></span></span>
                            </div>
                            <div>
                                <span class="text-muted mr-1" style="font-size: 12px;">Status Saat Ini:</span>
                                <span id="approval_status_current_badge"></span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="d-block font-weight-bold mb-2" style="font-size: 13.5px;">Pilih Status Baru <span class="text-danger">*</span></label>
                            <div class="approval-status-grid" id="approval_status_radio_list">
                                <!-- Pilihan status akan di-render secara dinamis dari database -->
                            </div>
                        </div>

                        <div class="form-group mb-1">
                            <label for="approval_status_catatan" class="font-weight-bold mb-1" style="font-size: 13px;">Catatan <span class="font-weight-normal text-muted">(opsional, maks 300 karakter)</span></label>
                            <textarea class="form-control py-1 px-2" id="approval_status_catatan" rows="2" maxlength="300" placeholder="Tuliskan catatan perubahan status jika ada..." style="font-size: 13px;"></textarea>
                        </div>

                        <div id="approval_status_error" class="alert alert-danger py-2 px-3 mb-0 mt-2" role="alert" style="display:none; font-size: 13px;"></div>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2 px-3 bg-light border-top">
                <!-- Tombol Alur Pendaftar Menunggu (Langkah 1 & 2) -->
                <button type="button" class="btn btn-secondary approval-close" id="approval_cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-secondary" id="approval_back" style="display:none">Kembali</button>
                <button type="button" class="btn btn-outline-danger" id="approval_reject" style="display:none" disabled>Belum bisa diterima</button>
                <button type="button" class="btn btn-success" id="approval_confirm" style="display:none" disabled>Setujui</button>
                <button type="submit" form="approval_reject_form" class="btn btn-danger" id="approval_reject_confirm" style="display:none">Kirim email dan hapus data</button>

                <!-- Tombol Alur Anggota Lainnya (Langkah 3A & 3B) -->
                <button type="button" class="btn btn-secondary approval-close" id="approval_member_close" data-bs-dismiss="modal" style="display:none">Tutup</button>
                <button type="button" class="btn btn-primary" id="approval_goto_change_status" style="display:none">Ubah Status</button>
                <button type="button" class="btn btn-secondary" id="approval_status_back" style="display:none">Kembali</button>
                <button type="button" class="btn btn-success" id="approval_status_save" style="display:none" disabled>Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // State anggota yang sedang dibuka di modal
    var currentMemberData = null;
    var currentSwitchUrl = '';
    var currentRejectUrl = '';

    // Helper merender HTML badge status sesuai ketentuan warna
    function getStatusBadgeHtml(statusName) {
        var s = (statusName || '').toLowerCase();
        var cls = 'badge-secondary';
        var style = '';
        if (s === 'aktif') {
            cls = 'badge-success';
        } else if (s === 'menunggu') {
            cls = 'badge-warning text-dark';
        } else if (s === 'perlu perbaikan') {
            cls = '';
            style = 'background-color: #fd7e14; color: #ffffff;';
        } else if (s === 'baru') {
            cls = 'badge-primary';
        } else if (s === 'nonaktif') {
            cls = 'badge-secondary';
        } else if (s === 'suspend') {
            cls = 'badge-danger';
        } else if (s === 'bebas pustaka') {
            cls = 'badge-alternate';
            style = 'background-color: #6f42c1; color: #ffffff;';
        }
        var styleAttr = style ? ' style="' + style + ' font-size: 12px; cursor: default;"' : ' style="font-size: 12px; cursor: default;"';
        return '<span class="badge ' + cls + ' py-1 px-2 font-weight-normal"' + styleAttr + '>' + (statusName || '-') + '</span>';
    }

    // Fungsi Utama: Membuka modal anggota (baik status Menunggu maupun status lainnya)
    function open_member_modal(summaryUrl, switchUrl, rejectUrl, startReject) {
        var $modal = $('#modal_approval');
        currentSwitchUrl = switchUrl || '';
        currentRejectUrl = rejectUrl || '';
        currentMemberData = null;

        // Reset semua tampilan dan indikator
        $('#approval_content, #approval_error').hide();
        $('#approval_inspect_view, #approval_reject_form, #approval_member_detail_view, #approval_change_status_view').hide();
        $('#approval_cancel, #approval_back, #approval_reject, #approval_confirm, #approval_reject_confirm').hide();
        $('#approval_member_close, #approval_goto_change_status, #approval_status_back, #approval_status_save').hide();
        $('#approval_loading').show();

        $modal.modal('show');

        $.getJSON(summaryUrl)
            .done(function(data) {
                currentMemberData = data;
                $('#approval_loading').hide();
                $('#approval_content').show();

                var isPending = (data.StatusAnggota === 'Menunggu' || (data.MemberNo && data.MemberNo.indexOf('PENDING-') === 0));

                if (isPending) {
                    // ALUR 1: Pendaftar Online Status Menunggu (Pertahankan Langkah 1 & 2 utuh)
                    initPendingApprovalFlow(data, startReject);
                } else {
                    // ALUR 2: Anggota dengan Status Lain (Tampilan Detail + Ubah Status)
                    initMemberDetailFlow(data);
                }
            })
            .fail(function() {
                $('#approval_loading').hide();
                $('#approval_error').show();
            });
    }

    // Inisialisasi Alur Pendaftar Menunggu (Langkah 1 & Langkah 2)
    function initPendingApprovalFlow(data, startReject) {
        var $modal = $('#modal_approval');
        $('#modal_approval_title').text('Periksa pendaftaran ini');

        $modal.find('#approval_inspect_view [data-field]').each(function() {
            var field = $(this).attr('data-field');
            var value = data[field];
            if (field === 'RegisterDate' && value) value = value.substring(0, 10);
            $(this).text(value || '-');
        });
        if (!data.JenisIdentitas) $modal.find('#approval_inspect_view dt[data-field="JenisIdentitas"]').text('No. identitas');

        $('#approval_photo')
            .off('error').on('error', function() { $(this).hide(); })
            .toggle(!!data.PhotoUrl).attr('src', data.PhotoUrl || '');

        // Isi form penolakan (Langkah 2)
        if (data.PhotoUrl) {
            $('#approval_reject_photo').show().attr('src', data.PhotoUrl);
            $('#approval_reject_photo_placeholder').hide();
        } else {
            $('#approval_reject_photo').hide();
            $('#approval_reject_photo_placeholder').css('display', 'flex');
        }
        $('#approval_reject_nama').text(data.Fullname || '-');
        $('#approval_reject_jenis').text(data.JenisAnggota || '-');
        if (data.StatusNonAsn) {
            $('#dt_approval_status_non_asn, #dd_approval_status_non_asn').show();
            $('#approval_reject_status_non_asn').text(data.StatusNonAsn).show();
        } else {
            $('#dt_approval_status_non_asn, #dd_approval_status_non_asn').hide();
            $('#approval_reject_status_non_asn').hide();
        }
        $('#approval_reject_identitas').text((data.JenisIdentitas ? data.JenisIdentitas + ': ' : '') + (data.IdentityNo || '-'));
        $('#approval_reject_unit').text(data.UnitKerja || '-');
        $('#approval_reject_unit_wrapper').toggle(!!data.UnitKerja);
        $('#approval_reject_email').text(data.Email || '-');

        $('#approval_sebelumnya')
            .text(data.PerbaikanSebelumnya ? 'Pendaftaran ini pernah diminta diperbaiki: ' + data.PerbaikanSebelumnya : '')
            .toggle(!!data.PerbaikanSebelumnya);

        if (data.MasaBerlakuPreview) {
            $('#dd_approval_masa_berlaku').text(data.MasaBerlakuDurasi + ' (s/d ' + data.MasaBerlakuEndDate + ')');
            $('#approval_masa_berlaku_text').html(data.MasaBerlakuPreview);
            $('#approval_masa_berlaku_box').show();
        } else {
            $('#dd_approval_masa_berlaku').text('-');
            $('#approval_masa_berlaku_box').hide();
        }

        $('#approval_reject_form').off('submit')[0].reset();
        $('.approval-alasan-cb').prop('checked', false);
        $('#approval_catatan_count').text('0');
        $('#approval_catatan_opsional').text('(opsional, maks 300 karakter)');

        $('#approval_confirm').prop('disabled', false).off('click').on('click', function() {
            $(this).prop('disabled', true);
            $('#modal_approval').modal('hide');
            if (typeof ajax_post === 'function' && currentSwitchUrl) {
                // Aktifkan anggota
                ajax_post(currentSwitchUrl, 'field=StatusAnggota_id&value=' + (data.StatusList ? getStatusIdByName(data.StatusList, 'Aktif') : '3'));
            }
        });

        $('#approval_reject').prop('disabled', false).off('click').on('click', function() {
            approval_step(true);
        });

        $('#approval_reject_form').on('submit', function(e) {
            e.preventDefault();
            approval_reject_submit(currentRejectUrl);
        });

        approval_step(!!startReject);
    }

    function approval_step(rejecting) {
        $('#modal_approval_title').text(rejecting ? 'Pendaftaran belum bisa diterima' : 'Periksa pendaftaran ini');
        $('#approval_inspect_view').toggle(!rejecting);
        $('#approval_reject_form').toggle(rejecting);

        // Tombol footer alur Menunggu
        $('#approval_cancel, #approval_reject, #approval_confirm').toggle(!rejecting);
        $('#approval_back, #approval_reject_confirm').toggle(rejecting);

        $('#approval_reject_error').hide();
        $('#approval_reject_confirm').prop('disabled', false).text('Kirim email dan hapus data');
        $('#approval_back').prop('disabled', false);
        if (rejecting) {
            $('.approval-alasan-cb').first().trigger('focus');
        }
    }

    function approval_reject_submit(url) {
        var alasan = [];
        $('.approval-alasan-cb:checked').each(function() {
            alasan.push($(this).val());
        });
        var catatan = $.trim($('#approval_catatan').val());
        var $error = $('#approval_reject_error').hide();
        var $button = $('#approval_reject_confirm');

        if (alasan.length === 0) {
            $error.text('Pilih minimal satu alasan pendaftaran belum bisa diterima.').show();
            return;
        }
        if (alasan.length === 1 && alasan[0] === 'lainnya' && !catatan) {
            $error.text('Untuk alasan Lainnya, silakan tulis penjelasannya pada catatan.').show();
            $('#approval_catatan').trigger('focus');
            return;
        }
        if (catatan.length > 300) {
            $error.text('Catatan maksimal 300 karakter.').show();
            $('#approval_catatan').trigger('focus');
            return;
        }

        $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Mengirim email & menghapus...');
        $('#approval_back').prop('disabled', true);

        var postData = {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            alasan: alasan,
            catatan: catatan
        };

        $.post(url, postData)
            .done(function(res) {
                if (res.error) {
                    $error.html(res.message).show();
                    $button.prop('disabled', false).text('Kirim email dan hapus data');
                    $('#approval_back').prop('disabled', false);
                    return;
                }
                $('#modal_approval').modal('hide');
                if (typeof t !== 'undefined' && t && typeof t.ajax === 'object') {
                    t.ajax.reload(null, false);
                }
                show_result(res);
            })
            .fail(function(xhr) {
                var msg = 'Keputusan belum tersimpan karena server tidak menjawab. Coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $error.html(msg).show();
                $button.prop('disabled', false).text('Kirim email dan hapus data');
                $('#approval_back').prop('disabled', false);
            });
    }

    // Inisialisasi Alur Anggota Non-Menunggu (Langkah 3A & Langkah 3B)
    function initMemberDetailFlow(data) {
        $('#modal_approval_title').text('Detail Anggota');
        $('#approval_inspect_view, #approval_reject_form, #approval_change_status_view').hide();
        $('#approval_cancel, #approval_back, #approval_reject, #approval_confirm, #approval_reject_confirm').hide();
        $('#approval_status_back, #approval_status_save').hide();

        // Tampilkan Langkah 3A (Detail Anggota)
        $('#approval_member_detail_view').show();
        $('#approval_member_close, #approval_goto_change_status').show();

        // Isi data detail
        $('#approval_member_no').text(data.MemberNo || '-');
        $('#approval_member_fullname').text(data.Fullname || '-');
        $('#approval_member_jenis_kelamin').text(data.JenisKelamin || '-');
        $('#approval_member_status_badge').html(getStatusBadgeHtml(data.StatusAnggota));
        $('#approval_member_jenis').text(data.JenisAnggota || '-');
        if (data.StatusNonAsn) {
            $('#dt_member_status_non_asn').show();
            $('#approval_member_status_non_asn').text(data.StatusNonAsn).show();
        } else {
            $('#dt_member_status_non_asn, #approval_member_status_non_asn').hide();
        }
        $('#approval_member_identitas_label').text(data.JenisIdentitas || 'No. Identitas');
        $('#approval_member_identitas_val').text(data.IdentityNo || '-');
        $('#approval_member_unit').text(data.UnitKerja || '-');
        $('#approval_member_alamat').text(data.AddressNow || '-');
        $('#approval_member_email').text(data.Email || '-');
        $('#approval_member_phone').text(data.Phone || '-');
        $('#approval_member_regdate').text(data.RegisterDate ? data.RegisterDate.substring(0, 10) : '-');
        $('#approval_member_enddate').text(data.EndDate || '-');

        $('#approval_member_photo')
            .off('error').on('error', function() { $(this).hide(); })
            .toggle(!!data.PhotoUrl).attr('src', data.PhotoUrl || '');

        // Info & tombol kirim ulang email
        if (data.BisaKirimUlang) {
            $('#approval_member_email_info').text(data.EmailInfo || 'Email pemberitahuan pendaftaran.');
            $('#approval_member_email_box').show();
            $('#approval_member_btn_resend_email').off('click').on('click', function() {
                var $btn = $(this).prop('disabled', true);
                var orig = $btn.html();
                $btn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Mengirim...');
                $.post(data.KirimUlangUrl)
                    .done(function(res) {
                        if (res.error) {
                            Swal.fire({ title: 'Email belum terkirim', text: res.message, icon: 'error', confirmButtonText: 'Mengerti' });
                        } else {
                            show_result(res);
                            if (typeof t !== 'undefined' && t && typeof t.ajax === 'object') {
                                t.ajax.reload(null, false);
                            }
                        }
                    })
                    .fail(function() {
                        Swal.fire({ title: 'Email belum terkirim', text: 'Server tidak menjawab. Coba lagi.', icon: 'error', confirmButtonText: 'Mengerti' });
                    })
                    .always(function() {
                        $btn.prop('disabled', false).html(orig);
                    });
            });
        } else {
            $('#approval_member_email_box').hide();
        }
    }

    // Beralih ke Langkah 3B: Formulir Ubah Status
    function showChangeStatusStep() {
        if (!currentMemberData) return;
        var data = currentMemberData;

        $('#modal_approval_title').text('Ubah Status Anggota');
        $('#approval_member_detail_view').hide();
        $('#approval_member_close, #approval_goto_change_status').hide();

        $('#approval_change_status_view').show();
        $('#approval_status_back, #approval_status_save').show();
        $('#approval_status_error').hide();

        // Ringkasan target
        $('#approval_status_target_name').text(data.Fullname || '-');
        $('#approval_status_target_no').text(data.MemberNo || '-');
        $('#approval_status_current_badge').html(getStatusBadgeHtml(data.StatusAnggota));

        // Render radio buttons
        var $radioList = $('#approval_status_radio_list').empty();
        var currentStatusId = parseInt(data.StatusAnggota_id, 10);

        if (data.StatusList && data.StatusList.length > 0) {
            data.StatusList.forEach(function(st) {
                var stId = parseInt(st.id, 10);
                var isMenunggu = (st.Nama === 'Menunggu');
                var isChecked = (stId === currentStatusId);
                var isDisabled = isMenunggu; // Menunggu tidak bisa dipilih manual

                var $item = $('<div class="form-check">' +
                    '<input class="form-check-input" type="radio" name="approval_new_status" id="st_radio_' + stId + '" value="' + stId + '" data-name="' + escAttr(st.Nama) + '"' +
                    (isChecked ? ' checked' : '') +
                    (isDisabled ? ' disabled' : '') +
                    '>' +
                    '<label class="form-check-label d-flex align-items-center" for="st_radio_' + stId + '">' +
                        getStatusBadgeHtml(st.Nama) +
                        (isMenunggu ? ' <small class="text-muted ml-1">(khusus online)</small>' : '') +
                    '</label>' +
                '</div>');
                $radioList.append($item);
            });
        }

        $('#approval_status_catatan').val('');
        // Simpan nonaktif bila status tidak berubah
        $('#approval_status_save').prop('disabled', true);
    }

    // Helper sanitasi teks atribut
    function escAttr(str) {
        return (str || '').replace(/"/g, '&quot;');
    }

    function getStatusIdByName(statusList, name) {
        for (var i = 0; i < statusList.length; i++) {
            if (statusList[i].Nama === name) return statusList[i].id;
        }
        return '3';
    }

    // Handler perubahan radio status
    $(document).on('change', 'input[name="approval_new_status"]', function() {
        if (!currentMemberData) return;
        var selectedVal = parseInt($(this).val(), 10);
        var currentVal = parseInt(currentMemberData.StatusAnggota_id, 10);
        // Tombol simpan hanya aktif jika status berubah
        $('#approval_status_save').prop('disabled', selectedVal === currentVal);
    });

    // Handler tombol "Ubah Status" di Langkah 3A
    $(document).on('click', '#approval_goto_change_status', function() {
        showChangeStatusStep();
    });

    // Handler tombol "Kembali" di Langkah 3B
    $(document).on('click', '#approval_status_back', function() {
        if (currentMemberData) {
            initMemberDetailFlow(currentMemberData);
        }
    });

    // Handler tombol "Simpan" perubahan status
    $(document).on('click', '#approval_status_save', function() {
        if (!currentMemberData) return;
        var selectedRadio = $('input[name="approval_new_status"]:checked');
        var newStatusId = parseInt(selectedRadio.val(), 10);
        var newStatusName = selectedRadio.attr('data-name');
        var catatan = $.trim($('#approval_status_catatan').val());

        if (isNaN(newStatusId) || newStatusId === parseInt(currentMemberData.StatusAnggota_id, 10)) {
            return;
        }

        // Suspend dan Nonaktif meminta konfirmasi SweetAlert2
        if (newStatusName === 'Suspend' || newStatusName === 'Nonaktif') {
            Swal.fire({
                title: 'Konfirmasi Perubahan Status',
                html: 'Apakah Anda yakin ingin mengubah status <strong>' + escAttr(currentMemberData.Fullname) + '</strong> menjadi <strong>' + escAttr(newStatusName) + '</strong>?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#dd6b55',
                confirmButtonText: 'Ya, Ubah Status',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    executeStatusSwitch(newStatusId, catatan);
                }
            });
        } else {
            executeStatusSwitch(newStatusId, catatan);
        }
    });

    // Eksekusi AJAX Simpan Perubahan Status
    function executeStatusSwitch(newStatusId, catatan) {
        var $btn = $('#approval_status_save').prop('disabled', true);
        var origHtml = $btn.html();
        $btn.html('<i class="fa fa-spinner fa-spin mr-1"></i> Menyimpan...');
        $('#approval_status_back').prop('disabled', true);
        $('#approval_status_error').hide();

        var postData = {
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>',
            field: 'StatusAnggota_id',
            value: newStatusId,
            catatan: catatan
        };

        $.post(currentSwitchUrl, postData)
            .done(function(res) {
                if (res.error) {
                    $('#approval_status_error').html(res.message).show();
                    $btn.prop('disabled', false).html(origHtml);
                    $('#approval_status_back').prop('disabled', false);
                    return;
                }
                $('#modal_approval').modal('hide');
                if (typeof t !== 'undefined' && t && typeof t.ajax === 'object') {
                    t.ajax.reload(null, false);
                }
                show_result(res);
            })
            .fail(function(xhr) {
                var msg = 'Status gagal disimpan karena server tidak merespon. Coba beberapa saat lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                $('#approval_status_error').html(msg).show();
                $btn.prop('disabled', false).html(origHtml);
                $('#approval_status_back').prop('disabled', false);
            });
    }

    // Fungsi wrapper open_approval untuk menjaga kompatibilitas pemanggil lama
    function open_approval(select, startReject) {
        var $select = $(select);
        var summaryUrl = $select.attr('data-summary');
        var switchUrl = $select.attr('data-href');
        var rejectUrl = $select.attr('data-reject-href');
        open_member_modal(summaryUrl, switchUrl, rejectUrl, startReject);
    }

    // Event listener karakter count catatan penolakan
    $(document).on('input propertychange', '#approval_catatan', function() {
        var len = $(this).val().length;
        $('#approval_catatan_count').text(len);
        if (len > 300) {
            $('#approval_catatan_count').addClass('text-danger font-weight-bold');
        } else {
            $('#approval_catatan_count').removeClass('text-danger font-weight-bold');
        }
    });

    $(document).on('change', '.approval-alasan-cb', function() {
        var checkedOther = $('#alasan_lainnya').is(':checked');
        var totalChecked = $('.approval-alasan-cb:checked').length;
        if (checkedOther && totalChecked === 1) {
            $('#approval_catatan_opsional').text('(wajib diisi jika hanya memilih alasan ini)');
        } else {
            $('#approval_catatan_opsional').text('(opsional, maks 300 karakter)');
        }
    });

    $(document).on('click', '#approval_back', function() {
        approval_step(false);
    });

    $(document).on('click', '#modal_approval .approval-close', function() {
        $('#modal_approval').modal('hide');
    });
</script>
