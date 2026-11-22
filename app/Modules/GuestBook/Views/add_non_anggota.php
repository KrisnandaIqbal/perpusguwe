<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/buku-tamu-modern.css') ?>?v=<?= time() ?>">
<style>
	.bt-member-search-box {
		max-width: 560px;
		margin: 0 auto;
	}
	.bt-member-profile-wrap {
		margin-top: 32px;
		border-top: 1px solid var(--bt-border-color);
		padding-top: 28px;
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bt-page-wrapper">
	<div class="bt-container">
		<div class="bt-hero-card">
			<h1 class="bt-hero-title"><?= esc($data->Name ?? 'Perpustakaan') ?></h1>
			<div class="bt-hero-subtitle"><?= esc($data->LocationLibrary_name ?? 'Buku Tamu') ?></div>
		</div>

		<!-- Segmented Switch Tabs (Seamless Instant Switch) -->
		<div class="bt-tabs-pill" role="tablist">
			<button type="button" class="bt-tab-btn" data-target="anggota" id="tabBtnAnggota">
				<i class="far fa-user"></i>
				<span>Anggota</span>
			</button>
			<button type="button" class="bt-tab-btn active" data-target="non_anggota" id="tabBtnNonAnggota">
				<i class="fas fa-user-plus"></i>
				<span>Bukan Anggota</span>
			</button>
			<button type="button" class="bt-tab-btn" data-target="rombongan" id="tabBtnRombongan">
				<i class="fas fa-users"></i>
				<span>Rombongan</span>
			</button>
		</div>

		<!-- Fallback link jika Javascript dinonaktifkan -->
		<noscript>
			<div style="text-align: center; margin-bottom: 20px;">
				<a href="<?= base_url('buku-tamu/anggota') ?>">Anggota</a> | 
				<a href="<?= base_url('buku-tamu/non_anggota') ?>">Bukan Anggota</a> | 
				<a href="<?= base_url('buku-tamu/rombongan') ?>">Rombongan</a>
			</div>
		</noscript>

		<!-- Error Alert (jika ada pesan galat dari session) -->
		<?php if (!empty($message)) : ?>
			<div class="bt-alert bt-alert-error" id="validationAlert" role="alert">
				<i class="fas fa-exclamation-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
				<div>
					<strong>Perhatian!</strong><br>
					<?= $message ?>
				</div>
				<button type="button" class="bt-alert-close" aria-label="Tutup pesan" onclick="this.parentElement.style.display='none'">
					<span>&times;</span>
				</button>
			</div>
		<?php endif; ?>

		<!-- ════════════════════ TAB 1: ANGGOTA ════════════════════ -->
		<div class="bt-tab-pane" id="paneAnggota">
			<div class="bt-card">
				<div class="bt-card-header">
					<h2 class="bt-card-title">Buku Tamu Anggota</h2>
					<p class="bt-card-subtitle">Masukkan nomor anggota atau pindai barcode kartu perpustakaan Anda</p>
				</div>

				<form method="get" action="<?= base_url('buku-tamu/anggota') ?>" class="bt-member-search-box">
					<div class="bt-field-group">
						<label for="member_no" class="bt-field-label text-center d-block mb-2">Nomor Anggota / Barcode <span class="required">*</span></label>
						<div class="bt-input-wrap">
							<i class="fas fa-barcode bt-input-icon"></i>
							<input type="text"
								class="bt-input form-control"
								name="member_no"
								id="member_no"
								placeholder="Ketik atau pindai nomor anggota..."
								autocomplete="off"
								required>
						</div>
					</div>

					<div class="bt-action-area" style="margin-top: 20px;">
						<button class="bt-btn-primary" type="submit">
							<i class="fas fa-search" style="font-size: 0.9rem;"></i>
							<span>Cari Anggota</span>
						</button>
						<p class="bt-legal-note">
							Tempelkan kartu pada alat pemindai barcode atau tekan Cari setelah mengisi nomor anggota.
						</p>
					</div>
				</form>
			</div>
		</div>

		<!-- ════════════════════ TAB 2: BUKAN ANGGOTA ════════════════════ -->
		<div class="bt-tab-pane active" id="paneNonAnggota">
			<div class="bt-card">
				<div class="bt-card-header">
					<h2 class="bt-card-title">Buku Tamu Non Anggota</h2>
					<p class="bt-card-subtitle">Silakan isi data diri Anda untuk tercatat sebagai pengunjung perpustakaan</p>
				</div>

				<form id="frm_create" method="post" action="<?= base_url('buku-tamu/non_anggota') ?>">
					<?= csrf_field() ?>
					
					<?= view('GuestBook\Views\isian_tamu', [
						'isian'             => $isian ?? [],
						'errors'            => $errors ?? [],
						'unit_kerja'        => $unit_kerja ?? [],
					]) ?>

					<div class="bt-action-area">
						<button type="submit" class="bt-btn-primary" id="saveBtn" name="submit">
							<i class="fas fa-lock" style="font-size: 0.9rem;"></i>
							<span class="btn-text">Simpan Buku Tamu</span>
							<span class="btn-loading" style="display: none;">
								<i class="fas fa-spinner fa-spin"></i>
								Menyimpan...
							</span>
						</button>
						<p class="bt-legal-note">
							Dengan mengisi formulir ini, data kunjungan Anda akan tercatat dalam buku tamu perpustakaan.
						</p>
					</div>
				</form>
			</div>
		</div>

		<!-- ════════════════════ TAB 3: ROMBONGAN ════════════════════ -->
		<div class="bt-tab-pane" id="paneRombongan">
			<div class="bt-card">
				<div class="bt-card-header">
					<h2 class="bt-card-title">Buku Tamu Rombongan</h2>
					<p class="bt-card-subtitle">Untuk kunjungan bersama paling sedikit 6 orang. Diisi oleh penanggung jawab rombongan.</p>
				</div>

				<form id="frm_create_rombongan" method="post" action="<?= base_url('buku-tamu/rombongan') ?>">
					<?= csrf_field() ?>
					
					<?= view('GuestBook\Views\isian_tamu', [
						'isian'             => [],
						'errors'            => [],
						'unit_kerja'        => $unit_kerja ?? [],
						'rombongan'         => true,
					]) ?>

					<div class="bt-action-area">
						<button type="submit" class="bt-btn-primary" id="saveBtnRombongan" name="submit">
							<i class="fas fa-lock" style="font-size: 0.9rem;"></i>
							<span class="btn-text">Simpan Buku Tamu Rombongan</span>
							<span class="btn-loading" style="display: none;">
								<i class="fas fa-spinner fa-spin"></i>
								Menyimpan...
							</span>
						</button>
						<p class="bt-legal-note">
							Dengan mengisi formulir ini, data kunjungan rombongan akan tercatat dalam buku tamu perpustakaan.
						</p>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<script>
// Seamless Instant Switching between tabs
document.addEventListener('DOMContentLoaded', function() {
	const tabs = {
		'anggota': {
			btn: document.getElementById('tabBtnAnggota'),
			pane: document.getElementById('paneAnggota'),
			title: 'Anggota',
			url: '<?= base_url('buku-tamu/anggota') ?>'
		},
		'non_anggota': {
			btn: document.getElementById('tabBtnNonAnggota'),
			pane: document.getElementById('paneNonAnggota'),
			title: 'Bukan Anggota',
			url: '<?= base_url('buku-tamu/non_anggota') ?>'
		},
		'rombongan': {
			btn: document.getElementById('tabBtnRombongan'),
			pane: document.getElementById('paneRombongan'),
			title: 'Rombongan',
			url: '<?= base_url('buku-tamu/rombongan') ?>'
		}
	};

	const breadcrumbCurrent = document.getElementById('btBreadcrumbCurrent');

	function switchTab(targetKey, updateHistory = true) {
		if (!tabs[targetKey]) return;

		Object.keys(tabs).forEach(key => {
			const item = tabs[key];
			if (key === targetKey) {
				item.btn.classList.add('active');
				item.pane.classList.add('active');
			} else {
				item.btn.classList.remove('active');
				item.pane.classList.remove('active');
			}
		});

		if (breadcrumbCurrent) {
			breadcrumbCurrent.textContent = tabs[targetKey].title;
		}

		if (updateHistory && window.history.pushState) {
			window.history.pushState({ tab: targetKey }, tabs[targetKey].title, tabs[targetKey].url);
		}

		// Focus pada input yang sesuai
		if (targetKey === 'anggota') {
			const memberInput = document.getElementById('member_no');
			if (memberInput) memberInput.focus();
		} else if (targetKey === 'non_anggota') {
			const namaInput = document.querySelector('#paneNonAnggota input[name="Nama"]');
			if (namaInput) namaInput.focus();
		} else if (targetKey === 'rombongan') {
			const namaRombongan = document.querySelector('#paneRombongan input[name="Nama"]');
			if (namaRombongan) namaRombongan.focus();
		}
	}

	// Event listener tombol tab
	document.querySelectorAll('.bt-tab-btn').forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			const target = this.getAttribute('data-target');
			switchTab(target, true);
		});
	});

	// Handle browser back/forward buttons
	window.addEventListener('popstate', function(e) {
		if (e.state && e.state.tab) {
			switchTab(e.state.tab, false);
		} else {
			// Deteksi dari pathname jika ada
			const path = window.location.pathname;
			if (path.includes('anggota') && !path.includes('non_anggota')) {
				switchTab('anggota', false);
			} else if (path.includes('rombongan')) {
				switchTab('rombongan', false);
			} else {
				switchTab('non_anggota', false);
			}
		}
	});

	// Auto hide alert error
	const validationAlert = document.getElementById('validationAlert');
	if (validationAlert) {
		setTimeout(() => {
			validationAlert.style.transition = 'opacity 0.5s ease';
			validationAlert.style.opacity = '0';
			setTimeout(() => { validationAlert.style.display = 'none'; }, 500);
		}, 7000);
	}
});

// Form loading handler
['frm_create', 'frm_create_rombongan'].forEach(formId => {
	const frm = document.getElementById(formId);
	if (!frm) return;
	frm.addEventListener('submit', function() {
		const saveBtn = this.querySelector('button[type="submit"]');
		if (!saveBtn) return;
		const btnText = saveBtn.querySelector('.btn-text');
		const btnLoading = saveBtn.querySelector('.btn-loading');

		saveBtn.disabled = true;
		if (btnText) btnText.style.display = 'none';
		if (btnLoading) btnLoading.style.display = 'inline-flex';
		
		setTimeout(() => {
			if (saveBtn.disabled) {
				saveBtn.disabled = false;
				if (btnText) btnText.style.display = 'inline-flex';
				if (btnLoading) btnLoading.style.display = 'none';
			}
		}, 12000);
	});
});
</script>

<?php if (session()->getFlashdata('success')) : ?>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		if (typeof Swal !== 'undefined') {
			Swal.fire({
				icon: 'success',
				title: 'Berhasil!',
				text: <?= json_encode((string) session()->getFlashdata('success')) ?>,
				showConfirmButton: false,
				timer: 2200
			});
		} else {
			alert(<?= json_encode((string) session()->getFlashdata('success')) ?>);
		}
	});
</script>
<?php endif; ?>

<?= $this->endsection() ?>
