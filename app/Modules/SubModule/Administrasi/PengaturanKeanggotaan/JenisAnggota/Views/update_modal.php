<div class="modal fade" id="modal_update" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-pencil icon-gradient bg-plum-plate"> </i> Edit Jenis Anggota
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_update" method="post" data-action="<?= base_url('api/master-jenis-anggota/edit') ?>" data-id="">
				<div class="modal-body">
                    <div id="frm_update_message"></div>
					<div class="form-row">
						<div class="col-lg-6">
							<div class="form-group">
								<label for="jenisanggota">Jenis Anggota</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_jenisanggota" name="jenisanggota" placeholder="Jenis Anggota" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<div class="d-flex justify-content-between align-items-center mb-1">
									<label for="frm_update_MasaBerlakuAnggota" class="mb-0 font-weight-bold">Masa Berlaku</label>
									<div class="btn-group btn-group-sm" role="group">
										<button type="button" class="btn btn-outline-primary btn-xs active" id="btn_mode_hari_update">Hari</button>
										<button type="button" class="btn btn-outline-primary btn-xs" id="btn_mode_tanggal_update">Pilih Tanggal</button>
									</div>
								</div>
								<!-- Mode 1: Berdasarkan Jumlah Hari -->
								<div id="wrap_mode_hari_update">
									<div class="input-group">
										<input required type="number" min="1" class="form-control" id="frm_update_MasaBerlakuAnggota" name="MasaBerlakuAnggota" placeholder="Contoh: 180" value="" />
										<div class="input-group-append">
											<span class="input-group-text">hari</span>
										</div>
									</div>
								</div>
								<!-- Mode 2: Berdasarkan Tanggal Berakhir -->
								<div id="wrap_mode_tanggal_update" style="display:none;">
									<div class="input-group">
										<input type="date" class="form-control" id="frm_update_MasaBerlakuTanggal" name="target_expire_date" disabled />
										<div class="input-group-append">
											<span class="input-group-text"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>
								<small class="form-text text-muted" id="preview_masa_berlaku_update">Masa berlaku aktif untuk jenis anggota ini.</small>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="BiayaPendaftaran">Biaya Pendaftaran</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_BiayaPendaftaran" name="BiayaPendaftaran" placeholder="Biaya Pendaftaran" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="BiayaPerpanjangan">Biaya Perpanjangan</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_BiayaPerpanjangan" name="BiayaPerpanjangan" placeholder="Biaya Perpanjangan" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="MaxLoanDays">Maksimal Pinjam Koleksi</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_MaxPinjamKoleksi" name="MaxPinjamKoleksi" placeholder="Maksimal Pinjam Koleksi" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="MaxLoanDays">Maksimal Lama Pinjam Koleksi</label>
								<div>
									<input required type="text" class="form-control" id="frm_update_MaxLoanDays" name="MaxLoanDays" placeholder="Maksimal Pinjam Koleksi" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="frm_update_DayPerpanjang">Lama Perpanjangan (hari kerja)</label>
								<div>
									<input required type="number" min="0" class="form-control" id="frm_update_DayPerpanjang" name="DayPerpanjang" placeholder="0 = sama dengan lama pinjam" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="frm_update_CountPerpanjang">Maksimal Perpanjangan (kali)</label>
								<div>
									<input required type="number" min="0" class="form-control" id="frm_update_CountPerpanjang" name="CountPerpanjang" placeholder="0 = tidak bisa diperpanjang" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="UploadDokumenKeanggotaanOnline">Upload Dokumen Peminjaman</label>
								<div>
									<select class="form-control" id="frm_update_UploadDokumenKeanggotaanOnline" name="UploadDokumenKeanggotaanOnline">
										<option value="1">Ya</option>
										<option value="0">Tdk</option>
									</select>
								</div>
							</div>
						</div>
					</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" name="submit" id="btnUpdate">Simpan</button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
	function updatePreviewMasaBerlakuUpdate(days) {
		days = parseInt(days, 10);
		if (!days || isNaN(days) || days <= 0) {
			$('#preview_masa_berlaku_update').text('Masukkan jumlah hari atau pilih tanggal.');
			return;
		}
		$('#preview_masa_berlaku_update').html('<i class="fa fa-info-circle text-info"></i> Berlaku <strong>' + days + ' hari</strong> untuk setiap anggota, tidak serentak.');
	}

	$('#btn_mode_hari_update').click(function() {
		$(this).addClass('active');
		$('#btn_mode_tanggal_update').removeClass('active');
		$('#wrap_mode_hari_update').show();
		$('#wrap_mode_tanggal_update').hide();
		$('#frm_update_MasaBerlakuTanggal').val('').prop('disabled', true);
		updatePreviewMasaBerlakuUpdate($('#frm_update_MasaBerlakuAnggota').val());
	});

	$('#btn_mode_tanggal_update').click(function() {
		$(this).addClass('active');
		$('#btn_mode_hari_update').removeClass('active');
		$('#wrap_mode_tanggal_update').show();
		$('#wrap_mode_hari_update').hide();
		$('#frm_update_MasaBerlakuTanggal').prop('disabled', false);
	});

	$('#frm_update_MasaBerlakuAnggota').on('input change', function() {
		updatePreviewMasaBerlakuUpdate($(this).val());
	});

	$('#frm_update_MasaBerlakuTanggal').on('input change', function() {
		var val = $(this).val();
		if (!val) return;
		var target = new Date(val + 'T00:00:00');
		var now = new Date();
		now.setHours(0, 0, 0, 0);
		var diffTime = target.getTime() - now.getTime();
		var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
		if (diffDays <= 0) {
			if (typeof Swal !== 'undefined') {
				Swal.fire('Perhatian', 'Tanggal berakhir harus lebih besar dari hari ini.', 'warning');
			}
			return;
		}
		$('#frm_update_MasaBerlakuAnggota').val(diffDays);
		var p = val.split('-');
		$('#preview_masa_berlaku_update').html('<i class="fa fa-info-circle text-info"></i> Berlaku serentak s/d <strong>' + p[2] + '/' + p[1] + '/' + p[0] + '</strong> (' + diffDays + ' hari lagi)');
	});

	$("body").on("click", ".show-data", function() {
		var url = $(this).attr('data-href');
        $.ajax({
            url: url,
            type: 'get',
            dataType: 'json',
            success: function(response) {
                $('#frm_update').attr("data-id", response.id);
                $('#frm_update_jenisanggota').val(response.jenisanggota);
                if (response.target_expire_date) {
                    $('#btn_mode_tanggal_update').trigger('click');
                    $('#frm_update_MasaBerlakuTanggal').val(response.target_expire_date);
                    var target = new Date(response.target_expire_date + 'T00:00:00');
                    var now = new Date();
                    now.setHours(0, 0, 0, 0);
                    var diffTime = target.getTime() - now.getTime();
                    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    $('#frm_update_MasaBerlakuAnggota').val(diffDays > 0 ? diffDays : response.MasaBerlakuAnggota);
                    var p = response.target_expire_date.split('-');
                    var fmt = p[2] + '/' + p[1] + '/' + p[0];
                    $('#preview_masa_berlaku_update').html('<i class="fa fa-info-circle text-info"></i> Berlaku serentak s/d <strong>' + fmt + '</strong> (' + diffDays + ' hari lagi)');
                } else {
                    $('#btn_mode_hari_update').trigger('click');
                    $('#frm_update_MasaBerlakuTanggal').val('');
                    $('#frm_update_MasaBerlakuAnggota').val(response.MasaBerlakuAnggota);
                    updatePreviewMasaBerlakuUpdate(response.MasaBerlakuAnggota);
                }
                $('#frm_update_BiayaPendaftaran').val(response.BiayaPendaftaran);
                $('#frm_update_BiayaPerpanjangan').val(response.BiayaPerpanjangan);
                $('#frm_update_MaxPinjamKoleksi').val(response.MaxPinjamKoleksi);
                $('#frm_update_MaxLoanDays').val(response.MaxLoanDays);
                $('#frm_update_DayPerpanjang').val(response.DayPerpanjang);
                $('#frm_update_CountPerpanjang').val(response.CountPerpanjang);
                $('#frm_update_UploadDokumenKeanggotaanOnline').val(response.UploadDokumenKeanggotaanOnline);
                $('#modal_update').modal('show');
            }
        });
	});

    $('#modal_update').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_update_message').html('');
    });

    $('#frm_update').submit(function(event) {
        event.preventDefault();
		var url = $(this).data('action') + '/' + $(this).data('id');
		var data_post = $(this).serializeArray();

		$("#btnUpdate").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
		$("#btnUpdate").attr('disabled', true);

		$.ajax({
			url: url,
			type: 'POST',
			data: data_post,
		})
		.done(function(res) {
			console.log(res)

			if (res.error == false) {
				Swal.fire({
					title: 'Berhasil',
					icon: 'success',
					html: 'Jenis Anggota berhasil diubah.',
					type: 'success',
					showConfirmButton: false,
					timer: 5000,
				}).then(() => {
					window.location.href = `<?=base_url('master-jenis-anggota')?>`;
				});
			} else {				
				Swal.fire({
					title: 'Oups',
					icon: 'error',
					text: res.message,
					type: 'error',
					showConfirmButton: false,
					timer: 5000
				}).then(() => {
					$("#btnUpdate").attr('disabled', false);
					$("#btnUpdate").html('Simpan');
				});
			}
		})
		.fail(function(res) {
			console.log(res);

			Swal.fire({
				title: 'Oups',
				text: 'Maaf, terjadi kesalahan. Coba beberapa saat lagi atau hubungi Admin',
				type: 'error',
				showConfirmButton: false,
				timer: 5000
			}).then(() => {
				$("#btnUpdate").attr('disabled', false);
				$("#btnUpdate").html('Simpan');
			});
		});
		
		return false;
    });
</script>