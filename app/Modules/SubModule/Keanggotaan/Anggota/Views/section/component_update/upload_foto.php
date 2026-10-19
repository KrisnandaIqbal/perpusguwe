<?php
$photoFileName = !empty($anggota->PhotoUrl) ? trim($anggota->PhotoUrl) : '';
$hasExistingPhoto = false;
$existingPhotoUrl = '';
$photoVersion = '';

if ($photoFileName) {
    $fullPath = ROOTPATH . 'public/uploads/anggota/' . $photoFileName;
    if (!file_exists($fullPath)) {
        $fullPath = FCPATH . 'uploads/anggota/' . $photoFileName;
    }
    if (file_exists($fullPath)) {
        $hasExistingPhoto = true;
        $photoVersion = '?v=' . filemtime($fullPath);
        $existingPhotoUrl = base_url('uploads/anggota/' . $photoFileName . $photoVersion);
    }
}
?>

<style>
.existing-photo-card {
    border: 1px solid #b8daff;
    background: linear-gradient(135deg, #f8fbff 0%, #edf5ff 100%);
    border-radius: 8px;
    padding: 14px 16px;
    margin-bottom: 16px;
    transition: all 0.2s ease;
}
.existing-photo-card:hover {
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.12);
}
.photo-thumbnail-box {
    position: relative;
    display: inline-block;
    cursor: pointer;
    border-radius: 6px;
    overflow: hidden;
    border: 2px solid #007bff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.photo-thumbnail-box:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.25);
}
.photo-thumbnail-box .zoom-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    opacity: 0;
    transition: opacity 0.2s ease;
    font-size: 1.25rem;
}
.photo-thumbnail-box:hover .zoom-overlay {
    opacity: 1;
}
.photo-thumbnail-box img {
    display: block;
    width: 80px;
    height: 120px;
    aspect-ratio: 2/3;
    object-fit: cover;
    background: #f0f0f0;
}

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

#photo {
  border: 1px solid black;
  box-shadow: 2px 2px 3px black;
  width: 160px;
  height: 240px;
  aspect-ratio: 2/3;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.2s ease;
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
		<div id="accordion_upload" class="accordion-wrapper mb-3">
			<div class="card">
				<div class="card-header-tab card-header">
					<button type="button" data-bs-toggle="collapse" data-bs-target="#collapse_upload" data-toggle="collapse" data-target="#collapse_upload" aria-expanded="true" aria-controls="collapse_upload" class="text-left m-0 p-0 btn btn-link">
						<h5 class="m-0 p-0 text-primary">
							<i class="header-icon lnr-picture icon-gradient bg-primary"></i> Pas Foto Pemustaka
						</h5>
					</button>
				</div>
				<div data-bs-parent="#accordion_upload" id="collapse_upload" class="collapse show">
					<div class="card-body">

						<!-- Preview Foto Yang Sudah Ada (Jika Ada) -->
						<?php if ($hasExistingPhoto): ?>
						<div class="existing-photo-card">
							<div class="d-flex align-items-center flex-wrap">
								<div class="photo-thumbnail-box mr-3 mb-2 mb-sm-0" onclick="openSimplePopup('<?= esc($existingPhotoUrl, 'attr') ?>', 'Pas Foto Pemustaka Saat Ini')" title="Klik untuk melihat foto di tengah layar">
									<img src="<?= esc($existingPhotoUrl, 'attr') ?>" alt="Pas Foto <?= esc($anggota->Fullname ?? 'Anggota', 'attr') ?>">
									<div class="zoom-overlay">
										<i class="fa fa-search-plus"></i>
									</div>
								</div>
								<div class="flex-grow-1">
									<div class="d-flex align-items-center mb-1 flex-wrap">
										<span class="badge badge-success px-2 py-1 mr-2" style="font-size: 0.78rem;">
											<i class="fa fa-check-circle mr-1"></i> Foto Tersedia
										</span>
										<span class="text-muted small">File: <strong class="text-dark font-monospace"><?= esc($photoFileName) ?></strong></span>
									</div>
									<p class="text-muted small mb-2">
										Klik pada foto atau tombol di bawah untuk melihat foto di tengah layar. Gunakan form di bawah jika ingin mengganti foto anggota ini.
									</p>
									<button type="button" class="btn btn-sm btn-primary" onclick="openSimplePopup('<?= esc($existingPhotoUrl, 'attr') ?>', 'Pas Foto Pemustaka Saat Ini')">
										<i class="fa fa-search-plus mr-1"></i> Lihat Preview Foto
									</button>
								</div>
							</div>
						</div>
						<?php else: ?>
						<div class="alert alert-light border text-muted small d-flex align-items-center mb-3">
							<i class="fa fa-info-circle fa-lg text-info mr-2"></i>
							<span>Anggota ini belum memiliki pas foto tersimpan. Silakan unggah foto baru melalui form di bawah.</span>
						</div>
						<?php endif; ?>

						<!-- Form Upload Foto -->
						<div class="form-row">
							<div class="col-md-12">
								<div class="position-relative form-group mb-0">
									<label for="file_image" class="font-weight-bold">
										<?= $hasExistingPhoto ? 'Ganti File Foto' : 'Unggah File Foto' ?>
									</label>
									<div id="file_image" class="dropzone"></div>
									<div id="file_image_listed"></div>
									<div class="mt-1">
										<small class="info help-block text-muted">
											<i class="fa fa-info-circle mr-1"></i> Format didukung: <strong>JPG, JPEG, PNG</strong>. Maksimal 10 MB. <strong>Klik pada foto yang diunggah untuk melihat preview ukuran penuh.</strong> Foto akan otomatis dinamai sesuai <strong>Nomor Anggota</strong> saat disimpan.
										</small>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Simple Centered Photo Popup (Instan, Ringan, Tanpa Library) -->
<div id="simplePhotoPopup" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.85); z-index:9999999; justify-content:center; align-items:center; padding:15px; box-sizing:border-box;">
    <div id="simplePhotoBox" style="position:relative; background:#ffffff; border-radius:10px; max-width:92vw; max-height:92vh; display:flex; flex-direction:column; overflow:hidden; box-shadow:0 20px 50px rgba(0,0,0,0.6); animation: popupZoomIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);">
        
        <!-- Header Popup -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:#1e293b; color:#ffffff;">
            <div style="font-weight:600; font-size:14px; display:flex; align-items:center; gap:8px;">
                <i class="fa fa-id-badge text-info"></i>
                <span id="popupTitleText">Preview Pas Foto Pemustaka</span>
            </div>
            <button type="button" onclick="closeSimplePopup(event)" style="background:none; border:none; color:#ffffff; font-size:26px; line-height:1; cursor:pointer; opacity:0.85; padding:0 6px; margin:0;" title="Tutup (ESC)">&times;</button>
        </div>

        <!-- Body Foto Ukuran Penuh -->
        <div style="background:#0f172a; display:flex; align-items:center; justify-content:center; padding:15px; min-width:280px; min-height:220px; max-height:calc(92vh - 110px); overflow:auto;">
            <img id="popupImageTag" src="<?= esc($existingPhotoUrl, 'attr') ?>" alt="Pas Foto" style="max-width:85vw; max-height:65vh; width:auto; height:auto; object-fit:contain; border-radius:6px; box-shadow:0 4px 16px rgba(0,0,0,0.3); display:block;">
        </div>

        <!-- Footer Info & Tombol -->
        <div style="padding:10px 16px; background:#f8fafc; border-top:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
            <div style="font-size:13px; color:#334155;">
                <strong class="text-dark"><?= esc($anggota->Fullname ?? '-') ?></strong> &bull; <span class="badge badge-primary px-2 py-1"><?= esc($anggota->MemberNo ?? '-') ?></span>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
                <a id="popupNewTabLink" href="<?= esc($existingPhotoUrl, 'attr') ?>" target="_blank" class="btn btn-sm btn-outline-secondary" style="font-size:12px; padding:4px 12px;">
                    <i class="fa fa-external-link"></i> Tab Baru
                </a>
                <button type="button" onclick="closeSimplePopup(event)" class="btn btn-sm btn-secondary" style="font-size:12px; padding:4px 16px;">
                    Tutup
                </button>
            </div>
        </div>

    </div>
</div>

<script>
// ========================================================
// PURE VANILLA JAVASCRIPT POPUP (ZERO JQUERY DEPENDENCY)
// ========================================================
function openSimplePopup(imgUrl, titleText) {
    if (!imgUrl) return;
    var popup = document.getElementById('simplePhotoPopup');
    var imgTag = document.getElementById('popupImageTag');
    var linkTag = document.getElementById('popupNewTabLink');
    var titleTag = document.getElementById('popupTitleText');
    if (!popup) return;

    // Pindahkan popup ke document.body agar tidak terpotong kontainer parent
    if (popup.parentNode !== document.body) {
        document.body.appendChild(popup);
    }

    if (imgTag) imgTag.src = imgUrl;
    if (linkTag) linkTag.href = imgUrl;
    if (titleTag) titleTag.textContent = titleText || 'Preview Pas Foto Pemustaka';

    popup.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeSimplePopup(e) {
    if (e && e.stopPropagation) {
        e.stopPropagation();
    }
    var popup = document.getElementById('simplePhotoPopup');
    if (popup) {
        popup.style.display = 'none';
        document.body.style.overflow = '';
    }
}

function showPhotoPreviewModal(imgUrl, fileName) {
    openSimplePopup(imgUrl);
}

// Mencegah klik di dalam kotak popup menutup popup
document.addEventListener('DOMContentLoaded', function() {
    var box = document.getElementById('simplePhotoBox');
    if (box) {
        box.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});

// Tutup saat klik latar belakang gelap luar
document.addEventListener('click', function(e) {
    var popup = document.getElementById('simplePhotoPopup');
    if (popup && popup.style.display === 'flex' && e.target === popup) {
        closeSimplePopup();
    }
});

// Tutup saat tombol ESC ditekan
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
        closeSimplePopup();
    }
});

// ========================================================
// GLOBAL CAPTURING CLICK HANDLER UNTUK PREVIEW DROPZONE & KAMERA
// ========================================================
document.addEventListener('click', function(e) {
    // 1. Cek apakah yang diklik adalah bagian dari preview Dropzone (#file_image .dz-preview)
    var preview = e.target.closest('#file_image .dz-preview');
    if (preview) {
        // Jika yang diklik adalah tombol "Remove file", biarkan fungsi hapus bekerja
        if (e.target.closest('.dz-remove')) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        var src = preview.getAttribute('data-blob-url') || preview.getAttribute('data-full-url') || '';

        // Jika belum ada di attribute, coba cari dari objek file Dropzone
        if (!src && typeof Dropzone !== 'undefined') {
            try {
                var dzEl = document.getElementById('file_image');
                var dz = dzEl ? dzEl.dropzone : null;
                if (dz && dz.files && dz.files.length) {
                    for (var i = 0; i < dz.files.length; i++) {
                        var f = dz.files[i];
                        if (f.previewElement === preview) {
                            if (window.URL && typeof window.URL.createObjectURL === 'function') {
                                src = URL.createObjectURL(f);
                                preview.setAttribute('data-blob-url', src);
                            } else if (f.dataURL) {
                                src = f.dataURL;
                            }
                            break;
                        }
                    }
                }
            } catch (err) {
                console.warn(err);
            }
        }

        // Fallback terakhir: ambil dari tag <img> di dalam preview
        if (!src) {
            var img = preview.querySelector('img[data-dz-thumbnail], img');
            if (img && img.src && !img.src.endsWith('data:image/png;base64,') && img.src.length > 50) {
                src = img.src;
            }
        }

        if (src) {
            openSimplePopup(src, 'Preview Foto Baru (Akan Disimpan)');
        }
        return;
    }

}, true); // useCapture = true agar menangkap klik sebelum Dropzone mengintersepsi!
</script>