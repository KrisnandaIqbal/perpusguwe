<script>
	var ROOTPATH = '/';
	var WRITEPATH = '/writable/';
	var BASEURL = '<?= base_url() ?>';

	$('.select2').select2({
		theme: "bootstrap4",
		width: "100%",
	});
	$('.tags').select2({
		theme: "bootstrap4",
		width: "100%",
		allowClear: true,
		tags: true,
		tokenSeparators: [',']
	});

	/* Sidebar */
	$('.close-sidebar-btn').click(function() {
		var classToSwitch = $(this).attr('data-class');
		var containerElement = '.app-container';
		$(containerElement).toggleClass(classToSwitch);

		var closeBtn = $(this);

		if (closeBtn.hasClass('is-active')) {
			closeBtn.removeClass('is-active');
			$('.user-profile').show();
		} else {
			closeBtn.addClass('is-active');
			$('.user-profile').hide();
		}
	});

	function toggleUserProfile() {
		var closeBtn = $('.app-container');

		if (closeBtn.hasClass('closed-sidebar')) {
			closeBtn.removeClass('closed-sidebar');
			$('.user-profile').hide();
		} else {
			closeBtn.addClass('closed-sidebar');
			$('.user-profile').show();
		}
	}

	// $(document).ready(function() {
	// 	toggleUserProfile();
	// });

	/* SweetAlert - Flash global (swal_icon / swal_title / swal_text) */
	<?php $swal_icon = session()->getFlashdata('swal_icon'); ?>
	<?php if ($swal_icon): ?>
	Swal.fire({
		icon:  '<?= esc($swal_icon) ?>',
		title: '<?= esc(session()->getFlashdata('swal_title') ?? '') ?>',
		text:  '<?= esc(session()->getFlashdata('swal_text')  ?? '') ?>',
		showConfirmButton: false,
		timer: 3000,
		timerProgressBar: true,
	});
	<?php endif; ?>

	/* SweetAlert - Permission Denied */
	<?php $swal_permission = session()->getFlashdata('swal_permission'); ?>
	<?php if (is_array($swal_permission)): ?>
	Swal.fire({
		icon: '<?= esc((string)($swal_permission['icon']  ?? '')) ?>',
		title: '<?= esc((string)($swal_permission['title'] ?? '')) ?>',
		text: '<?= esc((string)($swal_permission['text']  ?? '')) ?>',
		confirmButtonColor: '#d33',
		timer: 4000,
		timerProgressBar: true,
	});
	<?php endif; ?>

	/* Toastr */
	var toastr_msg = '<?= get_message('toastr_msg'); ?>';
	var toastr_type = '<?= get_message('toastr_type'); ?>';

	if (toastr_msg.length > 0) {
		toastr.options = {
			"closeButton": true,
			"debug": false,
			"newestOnTop": true,
			"progressBar": true,
			"positionClass": "toast-top-full-width",
			"preventDuplicates": false,
			"onclick": null,
			"showDuration": "300",
			"hideDuration": "1000",
			"timeOut": "3500",
			"extendedTimeOut": "1000",
			"showEasing": "swing",
			"hideEasing": "linear",
			"showMethod": "fadeIn",
			"hideMethod": "fadeOut"
		};
		toastr[toastr_type](toastr_msg, "Information");
	}

	/* Magnific Popup */
	$('.ajax-popup-link').magnificPopup({
		type: 'iframe',
		iframe: {
			markup: '<style>.mfp-iframe-holder .mfp-content {max-width: 95%;height:95%}</style>' +
				'<div class="mfp-iframe-scaler" >' +
				'<div class="mfp-close"></div>' +
				'<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
				'</div></div>'
		},
	});

	$('.image-link').magnificPopup({
		type: 'image'
	});

	$('.youtube-link').magnificPopup({
		type: 'iframe',
		iframe: {
			markup: `
				<style>.mfp-iframe-holder .mfp-content {max-width: 95%;height:95%}</style>
				<div class="mfp-iframe-scaler" >
					<div class="mfp-close"></div>
						<iframe class="mfp-iframe" frameborder="5" allow="autoplay;" allowfullscreen></iframe>' +
					</div>
				</div>`,
		},
	});

	function youtube_parser(url) {
		var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
		var match = url.match(regExp);
		return (match && match[7].length == 11) ? match[7] : false;
	}

	function image_url(path) {
		return 'https://www.perpusnas.go.id/' + path;
	}

	/* Data Table*/
	function setDataTable(dom, disableOrderCols = [4, 6, 7], defaultOrderCols = [0, 'asc'], autoNumber = false) {
		var t = $(dom).DataTable({
			"dom": "<'row'<'col-md-6 col-sm-8 col-xs-12 text-left'f><'col-md-6 col-sm-4 col-xs-12 d-none d-sm-block text-right'p>>" +
				"<'row'<'col-md-12'tr>>" +
				"<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12 text-right'i>>",
			"pagingType": "full",
			"oLanguage": {
				"sSearch": "<i class='fa fa-search'></i> _INPUT_",
				"sLengthMenu": "_MENU_",
				// "sInfo": "_START_ - _END_ of _TOTAL_",
				"oPaginate": {
					"sNext": "<i class='fa fa-chevron-right'></i>",
					"sPrevious": "<i class='fa fa-chevron-left'></i>",
					"sLast": "<i class='fa fa-chevron-double-right'></i>",
					"sFirst": "<i class='fa fa-chevron-double-left'></i>",
				}
			},
			"drawCallback": function(settings) {
				$('.apply-status').bootstrapToggle();

				$(".apply-status").on('change', function() {
					var href = $(this).attr('data-href');
					var field = $(this).attr('data-field');
					var id = $(this).attr('data-id');
					var switchStatus = $(this).is(':checked');

					if (switchStatus) {
						var url = href + '/' + id + '?field=' + field + '&value=1';
						window.location.href = url;
					} else {
						var url = href + '/' + id + '?field=' + field + '&value=0';
						window.location.href = url;
					}
				});
			},
			"columnDefs": [{
				"searchable": false,
				"orderable": false,
				"targets": disableOrderCols
			}],
			"order": [
				defaultOrderCols
			]
		});

		if (autoNumber) {
			t.on('order.dt search.dt', function() {
				t.column(0, {
					search: 'applied',
					order: 'applied'
				}).nodes().each(function(cell, i) {
					cell.innerHTML = i + 1;
				});
			}).draw();
		}

		return t;
	}

	/* Parameter*/
	function setParameter(name, value) {
		var data_post = {
			name: name,
			value: value,
			'<?= csrf_token() ?>': '<?= csrf_hash() ?>',
		};

		$('.loading').show();

		$.ajax({
				url: '<?= base_url('api/parameter/create') ?>',
				type: 'POST',
				dataType: 'json',
				data: data_post,
			})
			.done(function(res) {
				window.location.reload();
			})
			.fail(function(res) {
				Swal.fire({
					title: 'Oups',
					text: 'Parameter gagal disimpan',
					type: 'warning',
					showConfirmButton: false,
					timer: 3000
				});
			})
			.always(function() {
				$('.loading').hide();
			});

		return false;
	}

	/* ========================================================
	   GLOBAL IMAGE CROPPER (Rasio 2:3 - Auto Mulai dari Tengah)
	   ======================================================== */
	var _activeCropper = null;
	var _cropperOnApply = null;
	var _cropperOnCancel = null;
	var _cropperObjectUrl = null;

	function ensureCropperModalDOM() {
		if (document.getElementById('globalImageCropperModal')) return;

		var modalHtml = `
		<div id="globalImageCropperModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(15,23,42,0.88); z-index:99999999; justify-content:center; align-items:center; padding:15px; box-sizing:border-box;">
			<div style="position:relative; background:#ffffff; border-radius:14px; max-width:92vw; width:680px; max-height:92vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 25px 60px rgba(0,0,0,0.6);">
				
				<!-- Header -->
				<div style="display:flex; align-items:center; justify-content:space-between; padding:12px 18px; background:#1e293b; color:#ffffff;">
					<div style="font-weight:600; font-size:15px; display:flex; align-items:center; gap:8px;">
						<i class="fa fa-crop text-info"></i>
						<span>Sesuaikan / Potong Pas Foto (Rasio 2:3)</span>
					</div>
					<button type="button" id="cropperCloseTopBtn" style="background:none; border:none; color:#ffffff; font-size:24px; line-height:1; cursor:pointer; opacity:0.85; padding:0 4px;" title="Batal (ESC)">&times;</button>
				</div>

				<!-- Cropper Stage -->
				<div style="background:#0b1120; width:100%; height:380px; max-height:55vh; display:flex; align-items:center; justify-content:center; overflow:hidden; position:relative;">
					<img id="cropperImageSource" src="" alt="Crop Area" style="max-width:100%; max-height:100%; display:block;">
				</div>

				<!-- Toolbar -->
				<div style="background:#1e293b; padding:8px 16px; border-top:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;">
					<div style="display:flex; align-items:center; gap:6px;">
						<button type="button" class="btn btn-sm btn-dark text-white border-secondary" id="cropperBtnZoomIn" title="Perbesar"><i class="fa fa-search-plus"></i></button>
						<button type="button" class="btn btn-sm btn-dark text-white border-secondary" id="cropperBtnZoomOut" title="Perkecil"><i class="fa fa-search-minus"></i></button>
						<button type="button" class="btn btn-sm btn-dark text-white border-secondary" id="cropperBtnRotateLeft" title="Putar Kiri 90°"><i class="fa fa-rotate-left"></i></button>
						<button type="button" class="btn btn-sm btn-dark text-white border-secondary" id="cropperBtnRotateRight" title="Putar Kanan 90°"><i class="fa fa-rotate-right"></i></button>
						<button type="button" class="btn btn-sm btn-dark text-white border-secondary" id="cropperBtnReset" title="Reset Posisi Tengah"><i class="fa fa-refresh mr-1"></i> Reset</button>
					</div>
					<div style="display:flex; align-items:center; gap:6px;">
						<button type="button" class="btn btn-sm btn-info text-white" id="cropperBtnRatio23" title="Kunci Pas Foto 2:3"><i class="fa fa-lock mr-1"></i> 2:3 (Pas Foto)</button>
						<button type="button" class="btn btn-sm btn-outline-light" id="cropperBtnRatioFree" title="Rasio Bebas"><i class="fa fa-unlock mr-1"></i> Bebas</button>
					</div>
				</div>

				<!-- Footer -->
				<div style="padding:12px 18px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
					<div style="font-size:12px; color:#64748b;">
						<i class="fa fa-info-circle text-primary mr-1"></i> Otomatis mulai dari tengah. Geser & sesuaikan sesuai kebutuhan.
					</div>
					<div style="display:flex; align-items:center; gap:8px;">
						<button type="button" class="btn btn-sm btn-secondary" id="cropperBtnCancel" style="padding:5px 16px;">Batal</button>
						<button type="button" class="btn btn-sm btn-primary" id="cropperBtnApply" style="padding:5px 20px;">
							<i class="fa fa-check mr-1"></i> Gunakan Foto Ini
						</button>
					</div>
				</div>

			</div>
		</div>`;

		$('body').append(modalHtml);

		$('#cropperBtnZoomIn').on('click', function() { if (_activeCropper) _activeCropper.zoom(0.1); });
		$('#cropperBtnZoomOut').on('click', function() { if (_activeCropper) _activeCropper.zoom(-0.1); });
		$('#cropperBtnRotateLeft').on('click', function() { if (_activeCropper) _activeCropper.rotate(-90); });
		$('#cropperBtnRotateRight').on('click', function() { if (_activeCropper) _activeCropper.rotate(90); });
		$('#cropperBtnReset').on('click', function() { if (_activeCropper) _activeCropper.reset(); });
		
		$('#cropperBtnRatio23').on('click', function() {
			if (_activeCropper) {
				_activeCropper.setAspectRatio(2 / 3);
				$('#cropperBtnRatio23').addClass('btn-info text-white').removeClass('btn-outline-light');
				$('#cropperBtnRatioFree').addClass('btn-outline-light').removeClass('btn-info text-white');
			}
		});

		$('#cropperBtnRatioFree').on('click', function() {
			if (_activeCropper) {
				_activeCropper.setAspectRatio(NaN);
				$('#cropperBtnRatioFree').addClass('btn-info text-white').removeClass('btn-outline-light');
				$('#cropperBtnRatio23').addClass('btn-outline-light').removeClass('btn-info text-white');
			}
		});

		$('#cropperCloseTopBtn, #cropperBtnCancel').on('click', function() {
			closeCropperModal();
			if (typeof _cropperOnCancel === 'function') {
				_cropperOnCancel();
			}
		});

		$('#cropperBtnApply').on('click', function() {
			if (!_activeCropper) return;
			var canvas = _activeCropper.getCroppedCanvas({
				width: 600,
				height: 900,
				imageSmoothingEnabled: true,
				imageSmoothingQuality: 'high'
			});
			if (!canvas) return;

			var croppedDataUrl = canvas.toDataURL('image/jpeg', 0.92);
			canvas.toBlob(function(blob) {
				closeCropperModal();
				if (typeof _cropperOnApply === 'function') {
					_cropperOnApply(blob, croppedDataUrl);
				}
			}, 'image/jpeg', 0.92);
		});
	}

	function openImageCropper(imageSource, onApplyCallback, onCancelCallback) {
		if (typeof Cropper === 'undefined') {
			console.warn('Cropper.js belum tersedia, melanjutkan file tanpa crop.');
			if (typeof onApplyCallback === 'function') {
				onApplyCallback(imageSource, typeof imageSource === 'string' ? imageSource : '');
			}
			return;
		}

		ensureCropperModalDOM();

		_cropperOnApply = onApplyCallback;
		_cropperOnCancel = onCancelCallback;

		var modal = document.getElementById('globalImageCropperModal');
		var targetImg = document.getElementById('cropperImageSource');
		if (!modal || !targetImg) return;

		if (_activeCropper) {
			_activeCropper.destroy();
			_activeCropper = null;
		}

		if (_cropperObjectUrl) {
			URL.revokeObjectURL(_cropperObjectUrl);
			_cropperObjectUrl = null;
		}

		var imgSrc = '';
		if (typeof imageSource === 'string') {
			imgSrc = imageSource;
		} else if (imageSource instanceof Blob) {
			_cropperObjectUrl = URL.createObjectURL(imageSource);
			imgSrc = _cropperObjectUrl;
		}

		targetImg.src = imgSrc;
		modal.style.display = 'flex';
		document.body.style.overflow = 'hidden';

		var initCropper = function() {
			if (_activeCropper) {
				_activeCropper.destroy();
			}
			_activeCropper = new Cropper(targetImg, {
				aspectRatio: 2 / 3, // Otomatis rasio 2:3
				viewMode: 1, // Batasi crop box tidak keluar dari gambar
				dragMode: 'move',
				autoCrop: true,
				autoCropArea: 0.85, // Area 85%, otomatis terpusat di tengah
				center: true,
				guides: true,
				highlight: true,
				cropBoxMovable: true, // Pengguna bebas menggeser
				cropBoxResizable: true, // Pengguna bebas mengubah ukuran
				toggleDragModeOnDblclick: false,
				responsive: true,
				checkOrientation: true
			});
			targetImg.removeEventListener('load', initCropper);
		};

		if (targetImg.complete && targetImg.naturalWidth > 0) {
			initCropper();
		} else {
			targetImg.addEventListener('load', initCropper);
		}
	}

	function closeCropperModal() {
		var modal = document.getElementById('globalImageCropperModal');
		if (modal) {
			modal.style.display = 'none';
			document.body.style.overflow = '';
		}
		if (_activeCropper) {
			_activeCropper.destroy();
			_activeCropper = null;
		}
		if (_cropperObjectUrl) {
			URL.revokeObjectURL(_cropperObjectUrl);
			_cropperObjectUrl = null;
		}
	}

	$(document).on('keydown', function(e) {
		var modal = document.getElementById('globalImageCropperModal');
		if (modal && modal.style.display === 'flex' && (e.key === 'Escape' || e.keyCode === 27)) {
			closeCropperModal();
			if (typeof _cropperOnCancel === 'function') {
				_cropperOnCancel();
			}
		}
	});

	/* Dropzone*/
	Dropzone.autoDiscover = false;

	function setDropzone(domID, routePath, acceptedFiles = 'application/pdf', maxFiles = 1, maxFilesize = 10, writePath = false) {
		var baseUrl = BASEURL + "/" + routePath;
		if (maxFiles > 1) {
			url = baseUrl + "/do_upload";
			paramName = "file";
		} else {
			url = baseUrl + "/do_upload";
			paramName = "file";
		}

		var myDropzone = new Dropzone('#' + domID, {
			dictDefaultMessage: "<span class='text-muted'><i class='material-icons mt-2'>cloud_upload</i><br>DRAG &amp; DROP FILES HERE OR CLICK TO UPLOAD</span>",
			url: url,
			paramName: paramName,
			maxFiles: maxFiles,
			maxFilesize: maxFilesize,
			maxThumbnailFilesize: maxFilesize || 10,
			thumbnailWidth: 120,
			thumbnailHeight: 180,
			addRemoveLinks: true,
			acceptedFiles: acceptedFiles,
			transformFile: function(file, done) {
				var isImage = (file.type && file.type.match(/^image\//)) || (file.name && file.name.match(/\.(jpg|jpeg|png)$/i));
				var shouldCrop = isImage && (routePath === 'anggota' || acceptedFiles.indexOf('.jpg') !== -1 || acceptedFiles.indexOf('image') !== -1);

				if (!shouldCrop || typeof openImageCropper !== 'function') {
					return done(file);
				}

				openImageCropper(file, function(croppedBlob, croppedDataUrl) {
					var fileName = file.name || ('cropped_' + new Date().getTime() + '.jpg');
					var transformedFile;
					try {
						transformedFile = new File([croppedBlob], fileName, {
							type: 'image/jpeg',
							lastModified: Date.now()
						});
					} catch(e) {
						croppedBlob.name = fileName;
						transformedFile = croppedBlob;
					}

					if (file.previewElement) {
						var blobUrl = URL.createObjectURL(croppedBlob);
						file.previewElement.setAttribute('data-blob-url', blobUrl);
						var imgEl = file.previewElement.querySelector('img[data-dz-thumbnail], img');
						if (imgEl) {
							imgEl.src = blobUrl;
						}
					}

					done(transformedFile);
				}, function() {
					myDropzone.removeFile(file);
				});
			},
			renameFile: function(file) {
				var filename = new Date().getTime() + '_' + file.name.toLowerCase().replace(' ', '_');
				console.log("renameFile");
				console.log(filename);

				return filename;
			},
			accept: function(file, done) {
				console.log("accept");
				console.log(file);
				done();
			},
			init: function() {
				this.on("addedfile", function(file) {
					if (maxFiles === 1 && this.files.length > 1) {
						this.removeFile(this.files[0]);
					}
					try {
						if (file && file.previewElement && window.URL && typeof window.URL.createObjectURL === 'function') {
							var blobUrl = URL.createObjectURL(file);
							file.previewElement.setAttribute('data-blob-url', blobUrl);
						}
					} catch (e) {}
				});
				this.on("maxfilesexceeded", function(file) {
					console.log("max files exceeded");
					console.log(file);
				});
			},
			success: function(file, response) {
				console.log('success');
				console.log(response);

				var uuid = file.upload ? file.upload.uuid : new Date().getTime();
				var value = (response && response.data && response.data.name) ? response.data.name : '';
				var name = domID + '[' + uuid + ']';

				if (maxFiles === 1) {
					$('#' + domID + '_listed').empty();
				}
				if (value) {
					$('#' + domID + '_listed').append('<input type="hidden" name="' + name + '" value="' + value + '" />');
					if (file.previewElement) {
						file.previewElement.setAttribute('data-full-url', BASEURL + '/uploads/' + value);
					}
				}
			},
			removedfile: function(file) {
				console.log('removedfile');
				var name = "";
				var path = WRITEPATH + '/' + 'uploads';

				if (writePath) {
					path = writePath;
				}

				if (file.upload !== undefined) {
					name = file.upload.filename;
				} else {
					name = file.name;
					path = ROOTPATH + '/public/uploads/' + routePath;
				}

				$.ajax({
					type: 'POST',
					url: baseUrl + "/do_delete",
					data: "name=" + name + "&path=" + path,
					dataType: 'html'
				});

				if (file.upload && file.upload.uuid) {
					$('input[name="' + domID + '[' + file.upload.uuid + ']"]').remove();
				} else if (maxFiles === 1) {
					$('#' + domID + '_listed').empty();
				}

				var _ref;
				return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
			}
		});

		return myDropzone;
	}

	function initDropzones() {
		Dropzone.autoDiscover = false;
		$('.dropzone').each(function() {
			let dropzoneControl = $(this)[0].dropzone;
			if (dropzoneControl) {
				dropzoneControl.destroy();
			}
		})
	}

	/* Unique ID */
	function get_unique_id(length) {
		var result = '';
		var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for (var i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

	/* Axios */
	const getData = async (url, dom, selected = false, label = '-All-') => {
		await axios.get(url)
			.then(res => {
				$(dom).html('<option value="">Loading...</option>');
				var output = '<option value="">' + label + '</option>';
				$.each(res.data, function(key, val) {
					output += '<option value="' + val.code + '" data-text="' + val.name + '">' + val.name.toUpperCase() + '</option>';
				});
				$(dom).html(output);
				if (selected) {
					$(dom).val(selected);
				}
			})
			.catch(err => {
				console.log(err)
			});
	}

	function deleteConfirm(callback) {
		Swal.fire({
			title: 'Anda yakin ?',
			text: "Data yang sudah dihapus tidak dapat dikembalikan lagi",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(callback);
	}

	function deleteInfo(success = true) {
		if (success) {
			Swal.fire({
				title: 'Berhasil',
				text: 'Data berhasil dihapus',
				type: 'success',
				showConfirmButton: false,
				timer: 3000
			});
		} else {
			Swal.fire({
				title: 'Gagal',
				text: 'Data gagal dihapus',
				type: 'warning',
				showConfirmButton: false,
				timer: 2000
			});
		}
	}

	function successInfo(success = true) {
		if (success) {
			Swal.fire({
				title: 'Berhasil',
				text: `Data berhasil disimpan`,
				type: 'success',
				showConfirmButton: false,
				timer: 3000
			});
		} else {
			Swal.fire({
				title: 'Gagal',
				text: `Data gagal disimpan`,
				type: 'warning',
				showConfirmButton: false,
				timer: 2000
			});
		}
	}

	function makeAjaxCall(url, methodType, callback) {
		return $.ajax({
			url: url,
			method: methodType,
			dataType: "json"
		})
	}

	function makeAjaxCall2(url, methodType, callback) {
		$.ajax({
			url: url,
			method: methodType,
			dataType: "json",
			success: callback,
			error: function(reason, xhr) {
				console.log("Error in processing your request", reason);
			}
		});
	}

	function unquote(string) {
		return string && string.replace(/^['"]|['"]$/g, '');
	}

	function formatAmount(num, thousand_separator = '.', decimal_separator = ',') {
		var number = num.toString();
		var number_string = number.replace(/[^,\d]/g, "").toString(),
			split = number_string.split(decimal_separator),
			mod = split[0].length % 3,
			amount = split[0].substr(0, mod),
			thousand = split[0].substr(mod).match(/\d{3}/gi);

		if (thousand) {
			separator = mod ? thousand_separator : "";
			amount += separator + thousand.join(thousand_separator);
		}

		amount = split[1] != undefined ? amount + decimal_separator + split[1] : amount;
		return amount;
	}

	function formatRp(num, thousand_separator = '.', decimal_separator = ',') {
		var amount = formatAmount(num, thousand_separator, decimal_separator);

		return "Rp. " + amount;
	}

	function getRadioStatus(domID) {
		var status = true;
		$('#' + domID + ' input:radio').each(function() {
			var name = $(this).attr("name");
			if (!$('#' + domID + ' input:radio[name="' + name + '"]').is(':checked')) {
				status = false;
			}
		});

		return status;
	}

	function getInputStatus(domID) {
		var status = true;

		if ($('#' + domID).val().length == 0) {
			status = false;
		}

		return status;
	}

	function addToLocalStorage(itemName, itemValue) {
		var value = +localStorage.getItem(itemName);

		if (value) {
			value += itemValue;
		} else {
			value = itemValue;
		}

		localStorage.setItem(itemName, value);

		if (itemName == 'count_all') {
			var count_all = +localStorage.getItem('count_all');
			$('.bmn_count_all').html(formatAmount(count_all) + ' NUP');
		}

		if (itemName == 'amount_all') {
			var amount_all = +localStorage.getItem('amount_all');
			$('.bmn_amount_all').html(formatRp(amount_all));
		}

		if (itemName == 'count_psp0') {
			var count_psp0 = +localStorage.getItem('count_psp0');
			$('.bmn_count_psp0').html(formatAmount(count_psp0) + ' NUP');
		}

		if (itemName == 'amount_psp0') {
			var amount_psp0 = +localStorage.getItem('amount_psp0');
			$('.bmn_amount_psp0').html(formatRp(amount_psp0));
		}

		if (itemName == 'count_kondisi3') {
			var count_kondisi3 = +localStorage.getItem('count_kondisi3');
			$('.bmn_count_kondisi3').html(formatAmount(count_kondisi3) + ' NUP');
		}

		if (itemName == 'amount_kondisi3') {
			var amount_kondisi3 = +localStorage.getItem('amount_kondisi3');
			$('.bmn_amount_kondisi3').html(formatRp(amount_kondisi3));
		}
	}

	function displayBMN(bmn_00, isAddToLocalStorage = false) {
		// Total Aset
		var _dom_count_all = '.' + bmn_00 + '_count_all';
		var _url_count_all = $(_dom_count_all).data('url');
		$(_dom_count_all).html('loading...');
		makeAjaxCall(_url_count_all, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('count_all', +respJson.data);
			}
			$(_dom_count_all).html(formatAmount(respJson.data) + ' NUP');
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		var _dom_amount_all = '.' + bmn_00 + '_amount_all';
		var _url_amount_all = $(_dom_amount_all).data('url');
		$(_dom_amount_all).html('loading...');
		makeAjaxCall(_url_amount_all, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('amount_all', +respJson.data);
			}
			$(_dom_amount_all).html(formatRp(respJson.data));
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		// Belum PSP
		var _dom_count_psp0 = '.' + bmn_00 + '_count_psp0';
		var _url_count_psp0 = $(_dom_count_psp0).data('url');
		$(_dom_count_psp0).html('loading...');
		makeAjaxCall(_url_count_psp0, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('count_psp0', +respJson.data);
			}
			$(_dom_count_psp0).html(formatAmount(respJson.data) + ' NUP');
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		var _dom_amount_psp0 = '.' + bmn_00 + '_amount_psp0';
		var _url_amount_psp0 = $(_dom_amount_psp0).data('url');
		$(_dom_amount_psp0).html('loading...');
		makeAjaxCall(_url_amount_psp0, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('amount_psp0', +respJson.data);
			}
			$(_dom_amount_psp0).html(formatRp(respJson.data));
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		//Rusak Berat
		var _dom_count_kondisi3 = '.' + bmn_00 + '_count_kondisi3';
		var _url_count_kondisi3 = $(_dom_count_kondisi3).data('url');
		$(_dom_count_kondisi3).html('loading...');
		makeAjaxCall(_url_count_kondisi3, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('count_kondisi3', +respJson.data);
			}
			$(_dom_count_kondisi3).html(formatAmount(respJson.data) + ' NUP');
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		var _dom_amount_kondisi3 = '.' + bmn_00 + '_amount_kondisi3';
		var _url_amount_kondisi3 = $(_dom_amount_kondisi3).data('url');
		$(_dom_amount_kondisi3).html('loading...');
		makeAjaxCall(_url_amount_kondisi3, "GET").then(function(respJson) {
			if (isAddToLocalStorage) {
				addToLocalStorage('amount_kondisi3', +respJson.data);
			}
			$(_dom_amount_kondisi3).html(formatRp(respJson.data));
		}, function(reason) {
			console.log("Error in processing your request", reason);
		});

		if (bmn_00 == 'bmn_03') {
			// Belum SIP
			var _dom_count_sip0 = '.' + bmn_00 + '_count_sip0';
			var _url_count_sip0 = $(_dom_count_sip0).data('url');
			$(_dom_count_sip0).html('loading...');
			makeAjaxCall(_url_count_sip0, "GET").then(function(respJson) {
				$(_dom_count_sip0).html(formatAmount(respJson.data) + ' NUP');
			}, function(reason) {
				console.log("Error in processing your request", reason);
			});

			var _dom_amount_sip0 = '.' + bmn_00 + '_amount_sip0';
			var _url_amount_sip0 = $(_dom_amount_sip0).data('url');
			$(_dom_amount_sip0).html('loading...');
			makeAjaxCall(_url_amount_sip0, "GET").then(function(respJson) {
				$(_dom_amount_sip0).html(formatRp(respJson.data));
			}, function(reason) {
				console.log("Error in processing your request", reason);
			});
		}

	}

	function getDropdown(domID, uriParam = '', title = 'Pilih', selectedCode = false, includeCode = true) {
		var _dom = '#' + domID;
		var _url = $(_dom).data('url');
		if (uriParam.length > 0) {
			_url = _url + uriParam;
		}

		$("#" + domID).empty();
		$('#' + domID).append('<option value="">Loading...</option>');
		makeAjaxCall(_url, "GET").then(function(respJson) {
			$("#" + domID).empty();
			$('#' + domID).append('<option value="">' + title + '</option>');
			$.each(respJson, function(index, itemData) {
				var code = itemData.code;
				var text = itemData.text;
				var selected = '';

				if (code == selectedCode) {
					selected = 'selected';
				}

				if (includeCode) {
					text = +itemData.code + ' - ' + itemData.text;
				}
				$('#' + domID).append('<option value="' + code + '" ' + selected + '>' + text + ' </option>');
			});
		}, function(reason) {
			console.log(reason);
			$("#" + domID).empty();
			$('#' + domID).append('<option value="">Loading Error...</option>');
		});
	}

	function makeAjaxSelect(dom, url, value = false) {
		$(dom).empty();
		$(dom).append('<option value="">Loading...</option>');
		makeAjaxCall(url, "GET").then(function(respJson) {
			$(dom).empty();
			$(dom).append('<option value=""></option>');
			$.each(respJson, function(index, itemData) {
				var id = itemData.id;
				var name = itemData.name;
				var selected = '';
				if (id == value) selected = 'selected';
				$(dom).append('<option value="' + id + '" ' + selected + '>' + name + ' </option>');
			});
		}, function(reason) {
			console.log(reason);
			$(dom).empty();
			$(dom).append('<option value="">Loading Error...</option>');
		});
	}

	$('.btn-checkin').click(function() {
		var url = $(this).data('url');
		Swal.fire({
			title: 'Anda yakin?',
			text: "Memulai sesi penambahan Aset ke Keranjang untuk disimpan ke Daftar Usulan BMN.",
			type: 'info',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value) {
				window.location.href = url;
			}
		});
	});

	$('.btn-checkout').click(function() {
		var url = $(this).data('url');
		Swal.fire({
			title: 'Anda yakin?',
			text: "Mengakhiri sesi penambahan Aset ke Keranjang untuk disimpan ke Daftar Usulan BMN.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value) {
				window.location.href = url;
			}
		});
	});

	$('.btn-destroy').click(function() {
		var url = $(this).data('url');
		Swal.fire({
			title: 'Anda yakin?',
			text: "Keranjang Usulan akan dikosongkan.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value) {
				window.location.href = url;
			}
		});
	});

	$('.btn-delete').click(function() {
		var url = $(this).data('url');
		Swal.fire({
			title: 'Anda yakin?',
			text: "Aset yang terpilih akan dihapus dari Keranjang Usulan.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#dd6b55',
			confirmButtonText: 'Ya',
			cancelButtonText: 'Tidak'
		}).then(function(result) {
			if (result.value) {
				window.location.href = url;
			}
		});
	});

	function checkAll(domCheckAll = '.check_all', domCheckBox = 'input.check') {
		var checkAll = $(domCheckAll);
		var checkboxes = $(domCheckBox);

		checkAll.on('ifChecked ifUnchecked', function(event) {
			if (event.type == 'ifChecked') {
				checkboxes.iCheck('check');
			} else {
				checkboxes.iCheck('uncheck');
			}
		});

		checkboxes.on('ifChanged', function(event) {
			if (checkboxes.filter(':checked').length == checkboxes.length) {
				checkAll.prop('checked', 'checked');
			} else {
				checkAll.removeProp('checked');
			}
			checkAll.iCheck('update');
		});
	}

	// Drawer
	checkAll('.cart_check_all', 'input.cart_check');
	setDataTable('#cart_tbl_bmns', disableOrderCols = [0, 8], defaultOrderCols = [1, 'asc'], autoNumber = false);

	$('#remove_from_cart').click(function() {
		var form = $('#cart_form_bmns');
		var serialize_bulk = form.serialize();
		var url = "<?= base_url('request/cart_remove') ?>" + '/?' + serialize_bulk;
		console.log(serialize_bulk);
		console.log(url);

		if (serialize_bulk.includes("&id%5B%5D=")) {
			Swal.fire({
				title: 'Anda yakin?',
				text: "Semua aset yang terpilih akan dihapus dari keranjang!",
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#dd6b55',
				confirmButtonText: '<?= lang('App.btn.yes') ?>',
				cancelButtonText: '<?= lang('App.btn.no') ?>'
			}).then((result) => {
				if (result.value) {
					window.location.href = url;
				}
			});
		} else {
			Swal.fire({
				title: 'Oups',
				text: 'Aset yang terpilih kosong.',
				type: 'warning',
				showConfirmButton: false,
				timer: 2000
			});
		}

		return false;
	});

	function pad(num, size) {
		num = num.toString();
		while (num.length < size) num = "0" + num;
		return num;
	}
</script>