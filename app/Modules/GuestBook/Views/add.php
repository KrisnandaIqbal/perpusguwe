<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/buku-tamu-modern.css') ?>?v=<?= time() ?>">
<style>
	/* Member specific styles inside card */
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
	<!-- Ornamen dekoratif latar belakang -->
	<div class="bt-bg-circle bt-bg-circle-left"></div>
	<div class="bt-bg-circle bt-bg-circle-left-inner"></div>
	<div class="bt-bg-circle bt-bg-circle-right"></div>
	<div class="bt-bg-circle bt-bg-circle-right-inner"></div>

	<div class="bt-container">
		<!-- Hero Banner Card -->
		<div class="bt-hero-card">
			<div class="bt-hero-content">
				<h1 class="bt-hero-title"><?= esc($data->Name ?? 'Perpustakaan') ?></h1>
				<div class="bt-hero-subtitle"><?= esc($data->LocationLibrary_name ?? 'Buku Tamu') ?></div>
				
				<nav class="bt-breadcrumb" aria-label="Breadcrumb">
					<a href="<?= base_url() ?>"><i class="fas fa-home"></i> Beranda</a>
					<span class="separator">/</span>
					<span>Buku Tamu</span>
					<span class="separator">/</span>
					<span class="current" id="btBreadcrumbCurrent">Anggota</span>
				</nav>
			</div>

			<div class="bt-hero-illustration" style="background-image: url('<?= base_url('assets/img/buku-tamu-hero-people-clean.png') ?>');"></div>
		</div>

		<!-- Segmented Switch Tabs (Seamless Instant Switch) -->
		<div class="bt-tabs-pill" role="tablist">
			<button type="button" class="bt-tab-btn active" data-target="anggota" id="tabBtnAnggota">
				<i class="far fa-user"></i>
				<span>Anggota</span>
			</button>
			<button type="button" class="bt-tab-btn" data-target="non_anggota" id="tabBtnNonAnggota">
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

		<!-- ════════════════════ TAB 1: ANGGOTA ════════════════════ -->
		<div class="bt-tab-pane active" id="paneAnggota">
			<div class="bt-card">
				<?php if (empty($member)) : ?>
					<div class="bt-card-header">
						<h2 class="bt-card-title">Buku Tamu Anggota</h2>
						<p class="bt-card-subtitle">Masukkan nomor anggota atau pindai barcode kartu perpustakaan Anda</p>
					</div>

					<?php if (!empty($message)) : ?>
						<div class="bt-alert bt-alert-error" role="alert">
							<i class="fas fa-exclamation-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
							<div><?= esc(strip_tags($message)) ?></div>
							<button type="button" class="bt-alert-close" onclick="this.parentElement.style.display='none'">
								<span>&times;</span>
							</button>
						</div>
					<?php endif; ?>

					<?php if (!empty($kunjungan_baru)) : ?>
						<div class="bt-alert bt-alert-success" role="alert">
							<i class="fas fa-check-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
							<div>
								Kunjungan Anda sudah dicatat. Ada data yang salah? 
								<a href="<?= base_url('buku-tamu/anggota') . '?' . http_build_query(['member_no' => $kunjungan_baru['member_no'], 'ubah' => $kunjungan_baru['id']]) ?>" style="color: #1d4ed8; font-weight: 700; text-decoration: underline;">Ubah data kunjungan</a>
							</div>
						</div>
					<?php endif; ?>

					<!-- Search Box Form -->
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
								Tempelkan kartu pada alat pemindai atau tekan Cari setelah mengisi nomor anggota.
							</p>
						</div>
					</form>

				<?php else : ?>

					<div class="bt-card-header">
						<h2 class="bt-card-title">Konfirmasi Data Kunjungan</h2>
						<?php if (!empty($kirim_otomatis)) : ?>
							<p class="bt-card-subtitle" aria-live="polite">Mencatat kunjungan Anda...</p>
						<?php else : ?>
							<p class="bt-card-subtitle">Periksa nama, unit, dan nomor WhatsApp Anda. Ubah bila ada yang tidak sesuai.</p>
						<?php endif; ?>
					</div>

					<?php if (!empty($errors)) : ?>
						<div class="bt-alert bt-alert-error" role="alert">
							<i class="fas fa-exclamation-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
							<div>Periksa kembali isian yang ditandai merah di bawah ini.</div>
						</div>
					<?php endif; ?>

					<form id="frm_store_anggota" method="post" action="<?= base_url('buku-tamu/store_anggota'); ?>"<?= !empty($kirim_otomatis) ? ' data-kirim-otomatis' : '' ?>>
						<?= csrf_field() ?>
						<input type="hidden" name="member_no" value="<?= esc($member->MemberNo, 'attr') ?>">
						<?php if (!empty($kunjungan_id)) : ?>
							<input type="hidden" name="kunjungan_id" value="<?= (int) $kunjungan_id ?>">
						<?php endif; ?>

						<?= view('GuestBook\Views\isian_tamu', [
							'isian'             => $isian,
							'errors'            => $errors,
							'unit_kerja'        => $unit_kerja,
						]) ?>

						<div class="bt-action-area">
							<button type="submit" class="bt-btn-primary" id="simpanKunjungan">
								<i class="fas fa-save" style="font-size: 0.9rem;"></i>
								<span>Simpan Kunjungan</span>
							</button>
							<p class="bt-legal-note">
								<?php if (!empty($kunjungan_id)) : ?>
									Ubah isian yang salah, lalu tekan Simpan Kunjungan.
								<?php elseif (empty($kirim_otomatis)) : ?>
									Lengkapi isian yang masih kosong, lalu tekan Simpan Kunjungan.
								<?php else : ?>
									Sedang menyimpan data kunjungan Anda secara otomatis...
								<?php endif; ?>
							</p>
						</div>
					</form>

					<!-- Profil Anggota Ringkas -->
					<div class="bt-member-profile-wrap">
						<div class="d-flex align-items-center gap-3 mb-3">
							<div style="width: 44px; height: 44px; border-radius: 12px; background: #eff6ff; color: #1d4ed8; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
								<i class="far fa-id-card"></i>
							</div>
							<div>
								<h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: #1e293b;">Profil Anggota</h3>
								<small style="color: #64748b;">No. Anggota: <?= esc($member->MemberNo) ?></small>
							</div>
						</div>
						<?= view('Member\Views\member_profile', array('member' => $member ?? '', 'jenis_anggota' => $jenis_anggota ?? [])) ?>
					</div>

				<?php endif; ?>
			</div>
		</div>

		<!-- ════════════════════ TAB 2: BUKAN ANGGOTA ════════════════════ -->
		<div class="bt-tab-pane" id="paneNonAnggota">
			<div class="bt-card">
				<div class="bt-card-header">
					<h2 class="bt-card-title">Buku Tamu Non Anggota</h2>
					<p class="bt-card-subtitle">Silakan isi data diri Anda untuk tercatat sebagai pengunjung perpustakaan</p>
				</div>

				<form id="frm_create" method="post" action="<?= base_url('buku-tamu/non_anggota') ?>">
					<?= csrf_field() ?>
					
					<?= view('GuestBook\Views\isian_tamu', [
						'isian'             => [],
						'errors'            => [],
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

	document.querySelectorAll('.bt-tab-btn').forEach(btn => {
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			const target = this.getAttribute('data-target');
			switchTab(target, true);
		});
	});

	window.addEventListener('popstate', function(e) {
		if (e.state && e.state.tab) {
			switchTab(e.state.tab, false);
		} else {
			const path = window.location.pathname;
			if (path.includes('non_anggota')) {
				switchTab('non_anggota', false);
			} else if (path.includes('rombongan')) {
				switchTab('rombongan', false);
			} else {
				switchTab('anggota', false);
			}
		}
	});

	const searchInput = document.getElementById('member_no');
	if (searchInput) {
		searchInput.focus();
	}
});

document.getElementById('member_no')?.addEventListener('keypress', function(e) {
	if (e.key === 'Enter') {
		e.preventDefault();
		this.closest('form').submit();
	}
});
</script>

<?php if (session()->getFlashdata('success')) : ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		if (typeof Swal !== 'undefined') {
			Swal.fire({
				icon: 'success',
				title: 'Berhasil!',
				text: <?= json_encode((string) session()->getFlashdata('success')) ?>,
				showConfirmButton: false,
				timer: 2000
			});
		} else {
			alert(<?= json_encode((string) session()->getFlashdata('success')) ?>);
		}
	});
</script>
<?php endif; ?>

<?php if (!empty($kirim_otomatis)) : ?>
<script>
	document.querySelector('[data-kirim-otomatis]').submit();
</script>
<?php endif; ?>

<?= $this->endsection() ?>
