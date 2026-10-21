<div class="modal fade" id="modal_create" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="header-icon lnr-plus-circle icon-gradient bg-plum-plate"> </i> Tambah Jenis Anggota
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frm_add" method="post" action="">
                <div class="modal-body">
                    <div id="frm_add_message"></div>
					<div class="form-row">
					<div class="col-lg-6">
							<div class="form-group">
								<label for="jenisanggota">Jenis Anggota</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_jenisanggota" name="jenisanggota" placeholder="Jenis Anggota" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<div class="d-flex justify-content-between align-items-center mb-1">
									<label for="frm_add_MasaBerlakuAnggota" class="mb-0 font-weight-bold">Masa Berlaku</label>
									<div class="btn-group btn-group-sm" role="group">
										<button type="button" class="btn btn-outline-primary btn-xs active" id="btn_mode_hari_add">Hari</button>
										<button type="button" class="btn btn-outline-primary btn-xs" id="btn_mode_tanggal_add">Pilih Tanggal</button>
									</div>
								</div>
								<!-- Mode 1: Berdasarkan Jumlah Hari -->
								<div id="wrap_mode_hari_add">
									<div class="input-group">
										<input required type="number" min="1" class="form-control" id="frm_add_MasaBerlakuAnggota" name="MasaBerlakuAnggota" placeholder="Contoh: 180" value="365" />
										<div class="input-group-append">
											<span class="input-group-text">hari</span>
										</div>
									</div>
								</div>
								<!-- Mode 2: Berdasarkan Tanggal Berakhir -->
								<div id="wrap_mode_tanggal_add" style="display:none;">
									<div class="input-group">
										<input type="date" class="form-control" id="frm_add_MasaBerlakuTanggal" name="target_expire_date" disabled />
										<div class="input-group-append">
											<span class="input-group-text"><i class="fa fa-calendar"></i></span>
										</div>
									</div>
								</div>
								<small class="form-text text-muted" id="preview_masa_berlaku_add">Masa berlaku aktif untuk jenis anggota ini.</small>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="BiayaPendaftaran">Biaya Pendaftaran</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_BiayaPendaftaran" name="BiayaPendaftaran" placeholder="Biaya Pendaftaran" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="BiayaPerpanjangan">Biaya Perpanjangan</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_BiayaPerpanjangan" name="BiayaPerpanjangan" placeholder="Biaya Perpanjangan" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="MaxPinjamKoleksi">Maksimal Pinjam Koleksi</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_MaxPinjamKoleksi" name="MaxPinjamKoleksi" placeholder="Maksimal Pinjam Koleksi" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="MaxLoanDays">Maksimal Lama Pinjam Koleksi</label>
								<div>
									<input required type="text" class="form-control" id="frm_add_MaxLoanDays" name="MaxLoanDays" placeholder="Maksimal Lama Pinjam Koleksi" value="" />
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label for="UploadDokumenKeanggotaanOnline">Upload Dokumen Peminjaman</label>
								<div>
									<select class="form-control" id="frm_add_UploadDokumenKeanggotaanOnline" name="UploadDokumenKeanggotaanOnline">
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
                    <button type="submit" class="btn btn-primary" name="submit" id="btnAdd">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
	function updatePreviewMasaBerlakuAdd(days) {
		days = parseInt(days, 10);
		if (!days || isNaN(days) || days <= 0) {
			$('#preview_masa_berlaku_add').text('Masukkan jumlah hari atau pilih tanggal.');
			return;
		}
		$('#preview_masa_berlaku_add').html('<i class="fa fa-info-circle text-info"></i> Berlaku <strong>' + days + ' hari</strong> untuk setiap anggota, tidak serentak.');
	}

	$('#btn_mode_hari_add').click(function() {
		$(this).addClass('active');
		$('#btn_mode_tanggal_add').removeClass('active');
		$('#wrap_mode_hari_add').show();
		$('#wrap_mode_tanggal_add').hide();
		$('#frm_add_MasaBerlakuTanggal').val('').prop('disabled', true);
		updatePreviewMasaBerlakuAdd($('#frm_add_MasaBerlakuAnggota').val());
	});

	$('#btn_mode_tanggal_add').click(function() {
		$(this).addClass('active');
		$('#btn_mode_hari_add').removeClass('active');
		$('#wrap_mode_tanggal_add').show();
		$('#wrap_mode_hari_add').hide();
		$('#frm_add_MasaBerlakuTanggal').prop('disabled', false);
	});

	$('#frm_add_MasaBerlakuAnggota').on('input change', function() {
		updatePreviewMasaBerlakuAdd($(this).val());
	});

	$('#frm_add_MasaBerlakuTanggal').on('input change', function() {
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
		$('#frm_add_MasaBerlakuAnggota').val(diffDays);
		var p = val.split('-');
		$('#preview_masa_berlaku_add').html('<i class="fa fa-info-circle text-info"></i> Berlaku serentak s/d <strong>' + p[2] + '/' + p[1] + '/' + p[0] + '</strong> (' + diffDays + ' hari lagi)');
	});

	$('#modal_create').on('show.bs.modal', function() {
		var defaultDays = $('#frm_add_MasaBerlakuAnggota').val() || 365;
		updatePreviewMasaBerlakuAdd(defaultDays);
		$('#btn_mode_hari_add').trigger('click');
	});

    $('#frm_add').submit(function(event) {
		event.preventDefault();

		var url = "<?=base_url('api/master-jenis-anggota/create')?>";
		var data_post = $(this).serializeArray();

		$("#btnAdd").html('<i class="fa fa-spinner fa-spin loading"></i> Mohon menunggu...');
		$("#btnAdd").attr('disabled', true);

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
					html: 'Jenis Anggota berhasil ditambah.',
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
					$("#btnAdd").attr('disabled', false);
					$("#btnAdd").html('Simpan');
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
				$("#btnAdd").attr('disabled', false);
				$("#btnAdd").html('Simpan');
			});
		});
		
		return false;
    });

    $('#modal_create').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset');
        $('#frm_add_message').html('');
    });
</script>