<?php helper('anggota'); ?>
<style>
    #modal_approval .approval-data { display: grid; grid-template-columns: 130px 1fr; gap: 8px 14px; margin: 0; }
    #modal_approval .approval-data dt { font-weight: normal; color: #495057; }
    #modal_approval .approval-data dd { margin: 0; font-weight: 600; word-break: break-word; }
    #modal_approval .approval-photo { width: 96px; height: 120px; object-fit: cover; border-radius: 4px; flex-shrink: 0; }
    #modal_approval .modal-footer .btn { min-height: 44px; min-width: 96px; }
    #modal_approval .form-control { min-height: 44px; }
    @media (max-width: 575px) {
        #modal_approval .approval-body { flex-direction: column; }
        #modal_approval .approval-data { grid-template-columns: 1fr; gap: 2px; }
        #modal_approval .approval-data dd { margin-bottom: 8px; }
        #modal_approval .modal-footer .btn { flex: 1 1 100%; }
    }
</style>

<div class="modal fade" id="modal_approval" tabindex="-1" role="dialog" aria-labelledby="modal_approval_title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_approval_title">Periksa pendaftaran ini</h5>
                <button type="button" class="close approval-close" data-bs-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="approval_loading" class="mb-0">Memuat data pendaftar...</p>
                <div id="approval_error" class="alert alert-danger mb-0" role="alert" style="display:none">
                    Data pendaftar gagal dimuat, jadi belum bisa diputuskan dari sini. Tutup lalu coba lagi, atau buka form edit anggotanya.
                </div>
                <div id="approval_content" style="display:none">
                    <div class="approval-body d-flex" style="gap:18px">
                        <img class="approval-photo" id="approval_photo" src="" alt="Foto pendaftar">
                        <dl class="approval-data">
                            <dt>Jenis anggota</dt><dd data-field="JenisAnggota"></dd>
                            <dt data-field="JenisIdentitas"></dt><dd data-field="IdentityNo"></dd>
                            <dt>Nama lengkap</dt><dd data-field="Fullname"></dd>
                            <dt>Unit kerja</dt><dd data-field="UnitKerja"></dd>
                            <dt>Alamat</dt><dd data-field="AddressNow"></dd>
                            <dt>Email</dt><dd data-field="Email"></dd>
                            <dt>No. HP</dt><dd data-field="Phone"></dd>
                            <dt>Tgl. daftar</dt><dd data-field="RegisterDate"></dd>
                        </dl>
                    </div>
                    <p id="approval_sebelumnya" class="alert alert-warning mt-3 mb-0" style="display:none"></p>

                    <p id="approval_note" class="mt-3 mb-0">Setelah disetujui, No. Anggota dibuat otomatis dan kartu anggota dikirim ke email pendaftar. Nomor yang sudah dibuat tidak bisa dibatalkan.</p>

                    <form id="approval_reject_form" class="mt-3" style="display:none" novalidate>
                        <div class="form-group">
                            <label for="approval_alasan">Yang perlu diperbaiki</label>
                            <select class="form-control" id="approval_alasan" name="alasan" required>
                                <option value="">Pilih alasan</option>
                                <?php foreach (alasanPerbaikan() as $kunci => $alasan) : ?>
                                    <option value="<?= esc($kunci, 'attr') ?>"><?= esc($alasan['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <label for="approval_catatan">Catatan untuk pendaftar <span id="approval_catatan_opsional">(boleh kosong)</span></label>
                            <textarea class="form-control" id="approval_catatan" name="catatan" rows="3" maxlength="500"></textarea>
                        </div>
                        <div id="approval_reject_error" class="alert alert-danger mb-2" role="alert" style="display:none"></div>
                        <p class="mb-0">Alasan dan catatan ini dikirim ke email pendaftar. Pendaftar bisa mendaftar lagi dengan nomor identitas dan email yang sama.</p>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary approval-close" id="approval_cancel" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-secondary" id="approval_back" style="display:none">Kembali</button>
                <button type="button" class="btn btn-outline-danger" id="approval_reject" disabled>Belum bisa diterima</button>
                <button type="button" class="btn btn-success" id="approval_confirm" disabled>Setujui</button>
                <button type="submit" form="approval_reject_form" class="btn btn-danger" id="approval_reject_confirm" style="display:none">Minta perbaikan</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Dipanggil dari dropdown status di list.php untuk pendaftar online yang belum disetujui.
    // startReject: admin memilih Perlu Perbaikan, jadi dialog langsung membuka isian alasan.
    function open_approval(select, startReject) {
        var $select = $(select);
        var approveValue = $select.attr('data-approve-value');
        var $modal = $('#modal_approval');

        // Dropdown kembali ke status semula sampai admin memutuskan di dialog
        $select.val($select.find('option[selected]').val());

        $('#approval_content, #approval_error').hide();
        $('#approval_loading').show();
        $('#approval_confirm, #approval_reject').prop('disabled', true).off('click');
        $('#approval_reject_form').off('submit')[0].reset();
        $('#approval_alasan').trigger('change');
        approval_step(false);
        $modal.modal('show');

        $.getJSON($select.attr('data-summary'))
            .done(function(data) {
                $modal.find('[data-field]').each(function() {
                    var value = data[$(this).attr('data-field')];
                    if ($(this).attr('data-field') === 'RegisterDate' && value) value = value.substring(0, 10);
                    $(this).text(value || '-');
                });
                if (!data.JenisIdentitas) $modal.find('dt[data-field="JenisIdentitas"]').text('No. identitas');

                $('#approval_photo')
                    .off('error').on('error', function() { $(this).hide(); })
                    .toggle(!!data.PhotoUrl).attr('src', data.PhotoUrl || '');

                $('#approval_sebelumnya')
                    .text(data.PerbaikanSebelumnya ? 'Pendaftaran ini pernah diminta diperbaiki: ' + data.PerbaikanSebelumnya : '')
                    .toggle(!!data.PerbaikanSebelumnya);

                $('#approval_loading').hide();
                $('#approval_content').show();

                $('#approval_confirm').prop('disabled', false).on('click', function() {
                    $(this).prop('disabled', true);
                    $modal.modal('hide');
                    ajax_post($select.attr('data-href'), 'field=' + $select.attr('data-field') + '&value=' + approveValue);
                });

                $('#approval_reject').prop('disabled', false).on('click', function() {
                    approval_step(true);
                });

                $('#approval_reject_form').on('submit', function(e) {
                    e.preventDefault();
                    approval_reject_submit($select.attr('data-reject-href'));
                });

                if (startReject) approval_step(true);
            })
            .fail(function() {
                $('#approval_loading').hide();
                $('#approval_error').show();
            });
    }

    function approval_step(rejecting) {
        $('#modal_approval_title').text(rejecting ? 'Pendaftaran belum bisa diterima' : 'Periksa pendaftaran ini');
        $('#approval_note, #approval_cancel, #approval_reject, #approval_confirm').toggle(!rejecting);
        $('#approval_reject_form, #approval_back, #approval_reject_confirm').toggle(rejecting);
        $('#approval_reject_error').hide();
        $('#approval_reject_confirm').prop('disabled', false).text('Minta perbaikan');
        if (rejecting) $('#approval_alasan').trigger('focus');
    }

    function approval_reject_submit(url) {
        var alasan = $('#approval_alasan').val();
        var catatan = $.trim($('#approval_catatan').val());
        var $error = $('#approval_reject_error').hide();
        var $button = $('#approval_reject_confirm');

        if (!alasan) {
            $error.text('Pilih dulu yang perlu diperbaiki.').show();
            $('#approval_alasan').trigger('focus');
            return;
        }
        if (alasan === 'lainnya' && !catatan) {
            $error.text('Untuk alasan Lainnya, tulis alasannya di catatan.').show();
            $('#approval_catatan').trigger('focus');
            return;
        }

        $button.prop('disabled', true).text('Menyimpan...');

        $.post(url, { alasan: alasan, catatan: catatan })
            .done(function(res) {
                if (res.error) {
                    $error.text(res.message).show();
                    $button.prop('disabled', false).text('Minta perbaikan');
                    return;
                }
                $('#modal_approval').modal('hide');
                t.ajax.reload(null, false);
                show_result(res);
            })
            .fail(function() {
                $error.text('Keputusan belum tersimpan karena server tidak menjawab. Coba lagi.').show();
                $button.prop('disabled', false).text('Minta perbaikan');
            });
    }

    $(document).on('change', '#approval_alasan', function() {
        var wajib = $(this).val() === 'lainnya';
        $('#approval_catatan_opsional').text(wajib ? '(wajib diisi)' : '(boleh kosong)');
        $('#approval_catatan').prop('required', wajib);
    });

    $(document).on('click', '#approval_back', function() {
        approval_step(false);
    });

    $(document).on('click', '#modal_approval .approval-close', function() {
        $('#modal_approval').modal('hide');
    });
</script>
