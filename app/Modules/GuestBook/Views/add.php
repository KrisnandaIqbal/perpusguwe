<?= $this->extend('App\Views\layout\opac\layout'); ?>

<?= $this->section('style') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/buku-tamu-modern.css') ?>?v=<?= time() ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php $tab = $tab ?? 'anggota'; ?>
<div class="bt-page-wrapper">
	<div class="bt-container bt-container-lebar">
		<h1 class="bt-hero-title">Buku Tamu Perpustakaan</h1>

		<h2 class="bt-tabs-label" id="labelJenis">Anda datang sebagai</h2>
		<div class="bt-tabs-pill" role="group" aria-labelledby="labelJenis">
			<button type="button" class="bt-tab-btn<?= $tab === 'anggota' ? ' active' : '' ?>"<?= $tab === 'anggota' ? ' aria-current="true"' : '' ?> data-target="anggota" id="tabBtnAnggota">
				<i class="far fa-user"></i>
				<span class="bt-tab-teks">
					<span class="bt-tab-judul">Anggota</span>
					<span class="bt-tab-ket">Sudah punya kartu perpustakaan</span>
				</span>
			</button>
			<button type="button" class="bt-tab-btn<?= $tab === 'non_anggota' ? ' active' : '' ?>"<?= $tab === 'non_anggota' ? ' aria-current="true"' : '' ?> data-target="non_anggota" id="tabBtnNonAnggota">
				<i class="fas fa-user-plus"></i>
				<span class="bt-tab-teks">
					<span class="bt-tab-judul">Bukan Anggota</span>
					<span class="bt-tab-ket">Belum punya kartu, datang sendiri</span>
				</span>
			</button>
			<button type="button" class="bt-tab-btn<?= $tab === 'rombongan' ? ' active' : '' ?>"<?= $tab === 'rombongan' ? ' aria-current="true"' : '' ?> data-target="rombongan" id="tabBtnRombongan">
				<i class="fas fa-users"></i>
				<span class="bt-tab-teks">
					<span class="bt-tab-judul">Rombongan</span>
					<span class="bt-tab-ket">Datang bersama, minimal <?= \GuestBook\Libraries\IsianTamu::MIN_ROMBONGAN ?> orang</span>
				</span>
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

		<?php if (!empty($message) && $tab !== 'anggota') : ?>
			<div class="bt-alert bt-alert-error" id="validationAlert" role="alert">
				<i class="fas fa-exclamation-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
				<div><?= esc(strip_tags($message)) ?></div>
				<button type="button" class="bt-alert-close" aria-label="Tutup pesan" onclick="this.parentElement.style.display='none'">
					<span>&times;</span>
				</button>
			</div>
		<?php endif; ?>

		<!-- ════════════════════ TAB 1: ANGGOTA ════════════════════ -->
		<div class="bt-tab-pane<?= $tab === 'anggota' ? ' active' : '' ?>" id="paneAnggota">
			<div class="bt-layout<?= !empty($member) ? ' bt-layout-solo' : '' ?>">
			<div class="bt-card">
				<?php if (empty($member)) : ?>
					<div class="bt-card-header">
						<h2 class="bt-card-title">Catat kunjungan Anda</h2>
						<p class="bt-card-subtitle">Pindai kode QR di kartu anggota, atau ketik nomor anggota Anda.</p>
					</div>

					<?php if (!empty($message)) : ?>
						<div class="bt-alert bt-alert-error" role="alert">
							<i class="fas fa-exclamation-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
							<div><?= esc(strip_tags($message)) ?></div>
							<button type="button" class="bt-alert-close" aria-label="Tutup pesan" onclick="this.parentElement.style.display='none'">
								<span>&times;</span>
							</button>
						</div>
					<?php endif; ?>

					<?php if (!empty($kunjungan_baru)) : ?>
						<div class="bt-alert bt-alert-success" role="alert">
							<i class="fas fa-check-circle" style="font-size: 1.25rem; margin-top: 2px;"></i>
							<div>
								Kunjungan Anda sudah dicatat. Ada data yang salah? 
								<a href="<?= base_url('buku-tamu/anggota') . '?' . http_build_query(['member_no' => $kunjungan_baru['member_no'], 'ubah' => $kunjungan_baru['id']]) ?>">Ubah data kunjungan</a>
							</div>
						</div>
					<?php endif; ?>

					<!-- Search Box Form -->
					<form method="get" action="<?= base_url('buku-tamu/anggota') ?>">
						<label for="member_no" class="bt-field-label">Nomor anggota <span class="required">*</span></label>
						<div class="bt-cari-baris">
							<div class="bt-input-wrap">
								<i class="fas fa-qrcode bt-input-icon"></i>
								<input type="text"
									class="bt-input bt-input-nomor form-control"
									name="member_no"
									id="member_no"
									placeholder="Pindai atau ketik nomor"
									aria-describedby="petunjukNomor"
									autocomplete="off"
									required>
							</div>
							<button class="bt-btn-primary" type="submit">
								<i class="fas fa-search"></i>
								<span>Cari Anggota</span>
							</button>
						</div>
						<p class="bt-field-hint" id="petunjukNomor">Pemindai akan mengisi kolom ini dan langsung mencari. Mengetik? Tekan Enter atau Cari Anggota.</p>
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
							<div style="width: 44px; height: 44px; border-radius: 8px; background: #f5f7fa; color: #0f3fb4; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
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

			<?php if (empty($member)) : ?>
			<aside class="bt-samping" aria-labelledby="judulKartu">
				<h2 class="bt-samping-judul" id="judulKartu">Cari di sisi belakang kartu</h2>

				<div class="bt-kartu" role="img" aria-label="Sisi belakang kartu anggota: kode QR di kiri, nomor anggota lengkap di kanan bawah">
					<div class="bt-kartu-qr">
						<svg viewBox="0 0 21 21" aria-hidden="true"><g fill="#0d1c2e"><path d="M0 0h7v7H0zM14 0h7v7h-7zM0 14h7v7H0z"/></g><g fill="#f0ebe1"><path d="M1 1h5v5H1zM15 1h5v5h-5zM1 15h5v5H1z"/></g><g fill="#0d1c2e"><path d="M2 2h3v3H2zM16 2h3v3h-3zM2 16h3v3H2zM9 0h2v2H9zM8 3h1v3H8zM10 4h2v2h-2zM9 8h3v1H9zM0 9h2v2H0zM3 8h2v1H3zM4 10h3v1H4zM13 9h2v3h-2zM17 8h2v2h-2zM19 11h2v1h-2zM8 11h2v3H8zM11 13h2v2h-2zM14 14h3v1h-3zM18 14h2v3h-2zM9 16h3v1H9zM13 17h2v4h-2zM16 18h3v1h-3zM9 19h2v2H9zM19 20h2v1h-2z"/></g></svg>
					</div>
					<span class="bt-kartu-teks bt-kartu-qr-label">QR SCAN</span>
					<span class="bt-kartu-teks bt-kartu-no-label">NO. ANGGOTA</span>
					<span class="bt-kartu-no"></span>
					<span class="bt-kartu-titik bt-kartu-titik-1">1</span>
					<span class="bt-kartu-titik bt-kartu-titik-2">2</span>
				</div>

				<ol class="bt-langkah">
					<li><strong>Kode QR.</strong> Dekatkan ke alat pemindai di meja tamu.</li>
					<li><strong>Nomor anggota.</strong> Ketik angka ini bila pemindai tidak membaca kode.</li>
				</ol>

				<div class="bt-samping-blok">
					<h3>Kartu tertinggal?</h3>
					<p>Tanyakan nomor anggota ke petugas di meja layanan, atau isi buku tamu sebagai <a href="<?= base_url('buku-tamu/non_anggota') ?>" data-target="non_anggota">Bukan Anggota</a>.</p>
					<p>Telepon perpustakaan: <a href="tel:0215207797">(021) 520-7797</a></p>
				</div>
			</aside>
			<?php endif; ?>
			</div>
		</div>

		<!-- ════════════════════ TAB 2: BUKAN ANGGOTA ════════════════════ -->
		<div class="bt-tab-pane<?= $tab === 'non_anggota' ? ' active' : '' ?>" id="paneNonAnggota">
			<div class="bt-layout">
				<div class="bt-card">
					<div class="bt-card-header">
						<h2 class="bt-card-title">Isi data kunjungan</h2>
						<p class="bt-card-subtitle">Isi data diri Anda untuk tercatat sebagai pengunjung perpustakaan.</p>
					</div>

					<form id="frm_create" method="post" action="<?= base_url('buku-tamu/non_anggota') ?>">
						<?= csrf_field() ?>

						<?= view('GuestBook\Views\isian_tamu', [
							'isian'             => $tab === 'non_anggota' ? ($isian ?? []) : [],
							'errors'            => $tab === 'non_anggota' ? ($errors ?? []) : [],
							'unit_kerja'        => $unit_kerja ?? [],
						]) ?>

						<div class="bt-action-area">
							<button type="submit" class="bt-btn-primary" id="saveBtn" name="submit">
								<span class="btn-text">Simpan Buku Tamu</span>
								<span class="btn-loading" style="display: none;">
									<i class="fas fa-spinner fa-spin"></i>
									Menyimpan...
								</span>
							</button>
						</div>
					</form>
				</div>

				<aside class="bt-samping" aria-labelledby="judulSalahPilih">
					<h2 class="bt-samping-judul" id="judulSalahPilih">Bukan pilihan yang tepat?</h2>
					<div class="bt-samping-blok">
						<h3>Punya kartu anggota</h3>
						<p>Pilih <a href="<?= base_url('buku-tamu/anggota') ?>" data-target="anggota">Anggota</a>, lalu pindai kartu. Nama Anda terisi dari data anggota.</p>
					</div>
					<div class="bt-samping-blok">
						<h3>Datang <?= \GuestBook\Libraries\IsianTamu::MIN_ROMBONGAN ?> orang atau lebih</h3>
						<p>Pilih <a href="<?= base_url('buku-tamu/rombongan') ?>" data-target="rombongan">Rombongan</a>. Cukup satu penanggung jawab yang mengisi.</p>
					</div>
					<div class="bt-samping-blok">
						<h3>Butuh bantuan</h3>
						<p>Tanyakan petugas di meja layanan, atau telepon <a href="tel:0215207797">(021) 520-7797</a>.</p>
					</div>
				</aside>
			</div>
		</div>

		<!-- ════════════════════ TAB 3: ROMBONGAN ════════════════════ -->
		<div class="bt-tab-pane<?= $tab === 'rombongan' ? ' active' : '' ?>" id="paneRombongan">
			<div class="bt-layout">
				<div class="bt-card">
					<div class="bt-card-header">
						<h2 class="bt-card-title">Isi data rombongan</h2>
						<p class="bt-card-subtitle">Untuk kunjungan bersama paling sedikit <?= \GuestBook\Libraries\IsianTamu::MIN_ROMBONGAN ?> orang. Diisi oleh penanggung jawab rombongan.</p>
					</div>

					<form id="frm_create_rombongan" method="post" action="<?= base_url('buku-tamu/rombongan') ?>">
						<?= csrf_field() ?>

						<?= view('GuestBook\Views\isian_tamu', [
							'isian'             => $tab === 'rombongan' ? ($isian ?? []) : [],
							'errors'            => $tab === 'rombongan' ? ($errors ?? []) : [],
							'unit_kerja'        => $unit_kerja ?? [],
							'rombongan'         => true,
						]) ?>

						<div class="bt-action-area">
							<button type="submit" class="bt-btn-primary" id="saveBtnRombongan" name="submit">
								<span class="btn-text">Simpan Buku Tamu Rombongan</span>
								<span class="btn-loading" style="display: none;">
									<i class="fas fa-spinner fa-spin"></i>
									Menyimpan...
								</span>
							</button>
						</div>
					</form>
				</div>

				<aside class="bt-samping" aria-labelledby="judulInfo">
					<h2 class="bt-samping-judul" id="judulInfo">Sebelum mengisi</h2>
					<div class="bt-samping-blok">
						<h3>Asal rombongan</h3>
						<p>Pilih <strong>Kemnaker</strong>, lalu pilih satuan kerja. Pilih <strong>Lainnya</strong>, lalu tulis nama sekolah, kampus, atau instansi.</p>
					</div>
					<div class="bt-samping-blok">
						<h3>Butuh bantuan</h3>
						<p>Tanyakan petugas di meja layanan, atau telepon <a href="tel:0215207797">(021) 520-7797</a>.</p>
					</div>
				</aside>
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

	function switchTab(targetKey, updateHistory = true) {
		if (!tabs[targetKey]) return;

		Object.keys(tabs).forEach(key => {
			const item = tabs[key];
			if (key === targetKey) {
				item.btn.classList.add('active');
				item.btn.setAttribute('aria-current', 'true');
				item.pane.classList.add('active');
			} else {
				item.btn.classList.remove('active');
				item.btn.removeAttribute('aria-current');
				item.pane.classList.remove('active');
			}
		});

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

	document.querySelectorAll('[data-target]').forEach(btn => {
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
	if (searchInput && <?= $tab === 'anggota' ? 'true' : 'false' ?>) {
		searchInput.focus();
	}

	const validationAlert = document.getElementById('validationAlert');
	if (validationAlert) {
		setTimeout(() => {
			validationAlert.style.transition = 'opacity 0.5s ease';
			validationAlert.style.opacity = '0';
			setTimeout(() => { validationAlert.style.display = 'none'; }, 500);
		}, 7000);
	}
});

// Tombol simpan dikunci selagi data dikirim, dibuka lagi setelah 12 detik bila halaman tidak berpindah
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
				text: <?= json_encode((string) session()->getFlashdata('success'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
				showConfirmButton: false,
				timer: 3000
			});
		} else {
			alert(<?= json_encode((string) session()->getFlashdata('success'), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>);
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
