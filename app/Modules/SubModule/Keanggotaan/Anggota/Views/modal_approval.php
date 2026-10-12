<style>
    #modal_approval .approval-data { display: grid; grid-template-columns: 130px 1fr; gap: 8px 14px; margin: 0; }
    #modal_approval .approval-data dt { font-weight: normal; color: #495057; }
    #modal_approval .approval-data dd { margin: 0; font-weight: 600; word-break: break-word; }
    #modal_approval .approval-photo { width: 96px; height: 120px; object-fit: cover; border-radius: 4px; flex-shrink: 0; }
    #modal_approval .modal-footer .btn { min-height: 44px; min-width: 96px; }
    @media (max-width: 575px) {
        #modal_approval .approval-body { flex-direction: column; }
        #modal_approval .approval-data { grid-template-columns: 1fr; gap: 2px; }
        #modal_approval .approval-data dd { margin-bottom: 8px; }
    }
</style>

<div class="modal fade" id="modal_approval" tabindex="-1" role="dialog" aria-labelledby="modal_approval_title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal_approval_title">Setujui pendaftaran ini?</h5>
                <button type="button" class="close approval-close" data-bs-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="approval_loading" class="mb-0">Memuat data pendaftar...</p>
                <div id="approval_error" class="alert alert-danger mb-0" role="alert" style="display:none">
                    Data pendaftar gagal dimuat, jadi belum bisa disetujui dari sini. Tutup lalu coba lagi, atau buka form edit anggotanya.
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
                    <p class="mt-3 mb-0">Setelah disetujui, No. Anggota dibuat otomatis dan tanggal persetujuan dicatat. Nomor yang sudah dibuat tidak bisa dibatalkan.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary approval-close" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="approval_confirm" disabled>Setujui</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Dipanggil dari dropdown status di list.php untuk pendaftar online yang belum disetujui
    function open_approval(select) {
        var $select = $(select);
        var approveValue = $select.val();
        var $modal = $('#modal_approval');

        // Dropdown kembali ke status semula sampai admin menekan Setujui
        $select.val($select.find('option[selected]').val());

        $('#approval_content, #approval_error').hide();
        $('#approval_loading').show();
        $('#approval_confirm').prop('disabled', true).off('click');
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

                $('#approval_loading').hide();
                $('#approval_content').show();
                $('#approval_confirm').prop('disabled', false).on('click', function() {
                    $(this).prop('disabled', true);
                    $modal.modal('hide');
                    ajax_post($select.attr('data-href'), 'field=' + $select.attr('data-field') + '&value=' + approveValue);
                });
            })
            .fail(function() {
                $('#approval_loading').hide();
                $('#approval_error').show();
            });
    }

    $(document).on('click', '#modal_approval .approval-close', function() {
        $('#modal_approval').modal('hide');
    });
</script>
