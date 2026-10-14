<style>
/* Dropzone preview thumbnail enhancement */
#file_image .dz-preview {
    cursor: pointer !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
#file_image .dz-preview:hover {
    transform: translateY(-2px) scale(1.02);
}
#file_image .dz-preview .dz-image {
    position: relative;
    width: 120px;
    height: 180px;
    aspect-ratio: 2/3;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.18);
}
#file_image .dz-preview .dz-image img {
    width: 100%;
    height: 100%;
    aspect-ratio: 2/3;
    object-fit: cover;
}
#file_image .dz-preview:hover .dz-image::after {
    content: '\f00e  Klik Preview';
    font-family: 'FontAwesome', sans-serif;
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.5px;
    border-radius: 10px;
    pointer-events: none;
    z-index: 10;
    text-shadow: 0 1px 3px rgba(0,0,0,0.8);
}

#imageprev {
    cursor: pointer;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    width: 160px;
    height: 240px;
    aspect-ratio: 2/3;
    object-fit: cover;
}

@keyframes popupZoomIn {
    0% {
        transform: scale(0.92);
        opacity: 0;
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}
</style>

<div class="row">
	<div class="col-md-12">
		<div id="accordion_foto" class="accordion-wrapper mb-3">
			<div class="card">
				<div class="card-header-tab card-header">
					<button type="button" data-toggle="collapse" data-target="#collapse_foto"
						aria-expanded="true" aria-controls="collapse_madatory"
						class="text-left m-0 p-0 btn btn-link">
						<h5 class="m-0 p-0 text-primary">
							<i class="header-icon lnr-picture icon-gradient bg-primary"></i> Pas Foto Pemustaka
						</h5>
					</button>
				</div>
				<div data-parent="#accordion_foto" id="collapse_foto" class="collapse show">
					<div class="card-body">
						<div class="form-row">
							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="file_image" class="font-weight-bold">Upload Foto Anggota</label>
									<div id="file_image" class="dropzone"></div>
									<div id="file_image_listed"></div>
									<div class="mt-1">
										<small class="info help-block text-muted">
											<i class="fa fa-info-circle mr-1"></i> Format: <strong>JPG, JPEG, PNG</strong>. Max 10 MB. <strong>Klik pada foto yang diunggah untuk melihat preview ukuran penuh.</strong>
										</small>
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="position-relative form-group">
									<label for="camera_image" class="font-weight-bold">Ambil Foto Lewat Kamera</label>
									<div id="my_camera" class="mb-2"></div>
									<div id="results" class="mb-2"></div>
									<div class="btn-group">
										<button type="button" class="btn btn-outline-primary" onClick="open_snapshot()">
											<i class="fa fa-camera mr-1"></i> Buka Kamera
										</button>
										<button type="button" class="btn btn-primary" onClick="take_snapshot()">
											<i class="fa fa-dot-circle-o mr-1"></i> Jepret Foto
										</button>
									</div>

									<input type="hidden" name="camera_image" id="camera_image" value="">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Simple Centered Photo Popup (Instan, Ringan) -->
<div id="simplePhotoPopupAdd" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.85); z-index:9999999; justify-content:center; align-items:center; padding:15px; box-sizing:border-box;">
    <div id="simplePhotoBoxAdd" style="position:relative; background:#ffffff; border-radius:10px; max-width:92vw; max-height:92vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 50px rgba(0,0,0,0.6); animation: popupZoomIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:#1e293b; color:#ffffff;">
            <div style="font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px;">
                <i class="fa fa-id-badge text-info"></i>
                <span id="popupTitleTextAdd">Preview Pas Foto</span>
            </div>
            <button type="button" onclick="closeSimplePopupAdd(event)" style="background:none; border:none; color:#ffffff; font-size:26px; line-height:1; cursor:pointer; opacity:0.85; padding:0 6px; margin:0;" title="Tutup (ESC)">&times;</button>
        </div>
        <div style="background:#0f172a; display:flex; align-items:center; justify-content:center; padding:15px; min-width:280px; min-height:220px; max-height:calc(92vh - 110px); overflow:auto;">
            <img id="popupImageTagAdd" src="" alt="Pas Foto" style="max-width:85vw; max-height:65vh; width:auto; height:auto; object-fit:contain; border-radius:6px; box-shadow:0 4px 16px rgba(0,0,0,0.3); display:block;">
        </div>
        <div style="padding:10px 16px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; align-items:center; justify-content:flex-end; gap:10px;">
            <button type="button" onclick="closeSimplePopupAdd(event)" class="btn btn-sm btn-secondary" style="font-size:12px; padding:4px 16px;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<script>
function openSimplePopupAdd(imgUrl, titleText) {
    var popup = document.getElementById('simplePhotoPopupAdd');
    var imgTag = document.getElementById('popupImageTagAdd');
    var titleTag = document.getElementById('popupTitleTextAdd');
    if (!popup) return;

    if (popup.parentNode !== document.body) {
        document.body.appendChild(popup);
    }

    if (imgUrl && imgTag) {
        imgTag.src = imgUrl;
    }
    if (titleTag) {
        titleTag.textContent = titleText || 'Preview Pas Foto';
    }

    popup.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeSimplePopupAdd(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    var popup = document.getElementById('simplePhotoPopupAdd');
    if (popup) {
        popup.style.display = 'none';
        document.body.style.overflow = '';
    }
}

// Click preview untuk Dropzone pada form create
$(document).on('click', '#file_image .dz-preview', function(e) {
    if ($(e.target).closest('.dz-remove').length) return;
    var previewEl = this;
    var src = '';

    try {
        if (typeof Dropzone !== 'undefined') {
            var dz = Dropzone.forElement('#file_image');
            if (dz && dz.files && dz.files.length) {
                var matchedFile = dz.files.find(function(f) {
                    return f.previewElement === previewEl;
                });
                if (matchedFile) {
                    if (window.URL && typeof window.URL.createObjectURL === 'function') {
                        src = URL.createObjectURL(matchedFile);
                    } else if (matchedFile.dataURL) {
                        src = matchedFile.dataURL;
                    }
                }
            }
        }
    } catch (err) {
        console.log(err);
    }

    if (!src) {
        var img = $(this).find('img[data-dz-thumbnail], img').first();
        src = img.attr('src') || '';
    }

    if (src) {
        openSimplePopupAdd(src, 'Preview Foto Baru (Akan Disimpan)');
    }
});

// Click preview untuk snapshot kamera
$(document).on('click', '#imageprev', function() {
    var src = $(this).attr('src');
    if (src) {
        openSimplePopupAdd(src, 'Preview Hasil Jepretan Kamera');
    }
});

// Tutup klik luar / ESC
document.addEventListener('click', function(e) {
    var popup = document.getElementById('simplePhotoPopupAdd');
    if (popup && popup.style.display === 'flex' && e.target === popup) {
        closeSimplePopupAdd();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
        closeSimplePopupAdd();
    }
});

// Konfigurasi kamera
function open_snapshot() {
    Webcam.set({
        width: 320,
        height: 240,
        image_format: 'jpeg',
        jpeg_quality: 90
    });
    Webcam.attach('#my_camera');
}

function take_snapshot() {
    Webcam.snap(function(data_uri) {
        if (typeof openImageCropper === 'function') {
            openImageCropper(data_uri, function(blob, croppedDataUrl) {
                document.getElementById('results').innerHTML = '<img id="imageprev" src="' + croppedDataUrl + '" title="Klik untuk memperbesar" style="width:160px; height:240px; aspect-ratio:2/3; object-fit:cover; cursor:pointer;"/><br><small class="text-muted"><i class="fa fa-search-plus"></i> Klik untuk preview besar</small>';
                $('#camera_image').val(croppedDataUrl);
            });
        } else {
            document.getElementById('results').innerHTML = '<img id="imageprev" src="' + data_uri + '" title="Klik untuk memperbesar" style="width:160px; height:240px; aspect-ratio:2/3; object-fit:cover; cursor:pointer;"/><br><small class="text-muted"><i class="fa fa-search-plus"></i> Klik untuk preview besar</small>';
            $('#camera_image').val(data_uri);
        }
    });
    Webcam.reset();
}
</script>