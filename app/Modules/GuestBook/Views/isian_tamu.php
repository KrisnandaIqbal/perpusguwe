<?php
/**
 * Isian nama, unit asal, dan WhatsApp untuk buku tamu anggota, bukan anggota, dan rombongan.
 *
 * @var array  $isian             nilai awal: Nama, UnitKerja_id, UnitLainnya, HP (rombongan: Jenis, CountPersonel)
 * @var array  $errors            pesan per isian dari IsianTamu::periksa() atau periksaRombongan()
 * @var array  $unit_kerja        satuan kerja dari IsianTamu::pilihanUnit()
 * @var bool   $rombongan         true: pilihan Kemnaker / Lainnya, isian penanggung jawab, dan jumlah anggota
 */

use GuestBook\Libraries\IsianTamu;

$isian     = $isian ?? [];
$errors    = $errors ?? [];
$rombongan = !empty($rombongan);
$nilai     = fn (string $kolom) => (string) ($isian[$kolom] ?? '');
$salah     = fn (string $kolom) => isset($errors[$kolom]) ? 'is-invalid' : '';
// Isian yang salah ditandai aria-invalid dan menunjuk pesannya; $petunjuk: id petunjuk isian (bila ada)
$aria      = fn (string $kolom, string $petunjuk = '') => (isset($errors[$kolom]) ? ' aria-invalid="true"' : '')
    . (isset($errors[$kolom]) || $petunjuk !== '' ? ' aria-describedby="' . trim((isset($errors[$kolom]) ? 'galat_' . $kolom . ' ' : '') . $petunjuk) . '"' : '');
$pesan     = function (string $kolom) use ($errors) {
    return isset($errors[$kolom])
        ? '<div class="bt-invalid-feedback" id="galat_' . $kolom . '">' . esc($errors[$kolom]) . '</div>'
        : '';
};
$unitTerpilih = $nilai('UnitKerja_id') !== '' ? $nilai('UnitKerja_id') : ($nilai('UnitLainnya') !== '' ? IsianTamu::LAINNYA : '');
$jenis        = $nilai('Jenis');
?>

<div class="isian-tamu">
	<?php if ($rombongan) : ?>
		<fieldset class="bt-field-group" aria-describedby="<?= isset($errors['Jenis']) ? 'galat_Jenis' : '' ?>">
			<legend class="bt-field-label">Rombongan dari <span class="required">*</span></legend>
			<div class="pilihan-jenis <?= $salah('Jenis') ?>" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
				<div>
					<input type="radio" name="Jenis" id="jenis_kemnaker" value="kemnaker" <?= $jenis === 'kemnaker' ? 'checked' : '' ?> required style="position: absolute; opacity: 0; pointer-events: none;">
					<label for="jenis_kemnaker" class="bt-radio-card">
						<span class="bt-radio-teks">
							<strong>Kemnaker</strong>
							<span>Satuan kerja di Kementerian Ketenagakerjaan</span>
						</span>
					</label>
				</div>
				<div>
					<input type="radio" name="Jenis" id="jenis_lainnya" value="<?= IsianTamu::LAINNYA ?>" <?= $jenis === IsianTamu::LAINNYA ? 'checked' : '' ?> style="position: absolute; opacity: 0; pointer-events: none;">
					<label for="jenis_lainnya" class="bt-radio-card">
						<span class="bt-radio-teks">
							<strong>Lainnya</strong>
							<span>Sekolah, kampus, perusahaan, atau instansi lain</span>
						</span>
					</label>
				</div>
			</div>
			<?= $pesan('Jenis') ?>
		</fieldset>
	<?php endif; ?>

	<div class="bt-field-group">
		<label for="Nama" class="bt-field-label"><?= $rombongan ? 'Nama Penanggung Jawab' : 'Nama' ?> <span class="required">*</span></label>
		<div class="bt-input-wrap">
			<i class="far fa-user bt-input-icon"></i>
			<input type="text" class="bt-input form-control <?= $salah('Nama') ?>" name="Nama" id="Nama" maxlength="100"<?= $aria('Nama') ?>
				autocomplete="name" placeholder="Nama lengkap" value="<?= esc($nilai('Nama'), 'attr') ?>" required>
		</div>
		<?= $pesan('Nama') ?>
	</div>

	<div class="isian-baris" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 0 16px;">
		<div class="bt-field-group" id="kotakUnit" <?= $rombongan && $jenis !== 'kemnaker' ? 'hidden' : '' ?>>
			<label for="UnitKerja_id" class="bt-field-label">Satuan Kerja <span class="required">*</span></label>
			<div class="bt-input-wrap">
				<i class="fas fa-briefcase bt-input-icon"></i>
				<select class="bt-input form-control <?= $salah('UnitKerja_id') ?>" name="UnitKerja_id" id="UnitKerja_id" <?= $rombongan ? '' : 'required' ?><?= $aria('UnitKerja_id') ?>>
					<option value="">Pilih satuan kerja</option>
					<?php foreach ($unit_kerja as $unit) : ?>
						<option value="<?= (int) $unit->id ?>" <?= $unitTerpilih === (string) $unit->id ? 'selected' : '' ?>><?= esc($unit->nama_unit) ?></option>
					<?php endforeach; ?>
					<?php if (!$rombongan) : ?>
						<option value="<?= IsianTamu::LAINNYA ?>" <?= $unitTerpilih === IsianTamu::LAINNYA ? 'selected' : '' ?>>Lainnya (dari luar Kemnaker)</option>
					<?php endif; ?>
				</select>
			</div>
			<?= $pesan('UnitKerja_id') ?>
		</div>

		<div class="bt-field-group" id="kotakUnitLainnya" hidden>
			<label for="UnitLainnya" class="bt-field-label"><?= $rombongan ? 'Nama Instansi' : 'Nama Instansi / Asal' ?> <span class="required">*</span></label>
			<div class="bt-input-wrap">
				<i class="far fa-building bt-input-icon"></i>
				<input type="text" class="bt-input form-control <?= $salah('UnitLainnya') ?>" name="UnitLainnya" id="UnitLainnya" maxlength="150"<?= $aria('UnitLainnya') ?>
					autocomplete="organization" placeholder="Contoh: nama sekolah, kampus, perusahaan, atau instansi" value="<?= esc($nilai('UnitLainnya'), 'attr') ?>">
			</div>
			<?= $pesan('UnitLainnya') ?>
		</div>
	</div>

	<div class="bt-field-group">
		<label for="HP" class="bt-field-label"><?= $rombongan ? 'Nomor WhatsApp Penanggung Jawab' : 'Nomor WhatsApp' ?> <span class="required">*</span></label>
		<div class="bt-input-wrap">
			<i class="fas fa-phone-alt bt-input-icon"></i>
			<input type="tel" class="bt-input form-control <?= $salah('HP') ?>" name="HP" id="HP" maxlength="20" inputmode="tel"
				autocomplete="tel" placeholder="Diawali 08 atau 62" value="<?= esc($nilai('HP'), 'attr') ?>"
				<?= trim($aria('HP', 'petunjukHP')) ?> required>
		</div>
		<?= $pesan('HP') ?>
		<div class="bt-field-hint" id="petunjukHP">Nomor aktif yang bisa dihubungi lewat WhatsApp.</div>
	</div>

	<?php if ($rombongan) : ?>
		<div class="bt-field-group">
			<label for="CountPersonel" class="bt-field-label">Jumlah Anggota Rombongan <span class="required">*</span></label>
			<div class="bt-input-wrap bt-input-sempit">
				<i class="fas fa-user-friends bt-input-icon"></i>
				<input type="number" class="bt-input form-control <?= $salah('CountPersonel') ?>" name="CountPersonel" id="CountPersonel"
					min="<?= IsianTamu::MIN_ROMBONGAN ?>" max="<?= IsianTamu::MAX_ROMBONGAN ?>" step="1" inputmode="numeric"
					placeholder="Paling sedikit <?= IsianTamu::MIN_ROMBONGAN ?> orang" value="<?= esc($nilai('CountPersonel'), 'attr') ?>"
					<?= trim($aria('CountPersonel', 'petunjukJumlah')) ?> required>
			</div>
			<?= $pesan('CountPersonel') ?>
			<div class="bt-field-hint" id="petunjukJumlah">Termasuk penanggung jawab. Kalau kurang dari <?= IsianTamu::MIN_ROMBONGAN ?> orang, setiap orang mengisi buku tamu Anggota atau Bukan Anggota.</div>
		</div>
	<?php endif; ?>
</div>

<style>
	.isian-tamu [hidden] { display: none !important; }
	.isian-tamu .bt-input-sempit { max-width: 320px; }
	.isian-tamu label.bt-radio-card {
		display: flex;
		align-items: flex-start;
		gap: 14px;
		min-height: 64px;
		margin: 0;
		padding: 16px 18px;
		border: 1.5px solid #7c8699;
		border-radius: 8px;
		cursor: pointer;
		transition: background-color 0.18s, border-color 0.18s;
	}
	.isian-tamu label.bt-radio-card::before {
		content: '';
		flex: none;
		box-sizing: border-box;
		width: 20px;
		height: 20px;
		margin-top: 2px;
		border: 2px solid #7c8699;
		border-radius: 50%;
		background: #ffffff;
	}
	.isian-tamu label.bt-radio-card:hover {
		background-color: #f5f7fa;
	}
	.isian-tamu .bt-radio-teks {
		display: flex;
		flex-direction: column;
	}
	.isian-tamu .bt-radio-teks strong {
		font-size: 1rem;
		color: #151922;
	}
	.isian-tamu .bt-radio-teks span {
		font-size: 0.9rem;
		color: #5b6272;
	}
	.isian-tamu input[type="radio"]:checked + label.bt-radio-card {
		border-color: #0f3fb4;
		background-color: #eef2fc;
		box-shadow: 0 0 0 1px #0f3fb4;
	}
	.isian-tamu input[type="radio"]:checked + label.bt-radio-card::before {
		border-color: #0f3fb4;
		background: radial-gradient(circle, #0f3fb4 0 4px, #ffffff 5px);
	}
	.isian-tamu input[type="radio"]:focus-visible + label.bt-radio-card {
		outline: 2px solid #0f3fb4;
		outline-offset: 3px;
	}
	.isian-tamu .pilihan-jenis.is-invalid label.bt-radio-card {
		border-color: #b91c1c;
	}
</style>

<script>
(function () {
	const LAINNYA = '<?= IsianTamu::LAINNYA ?>';
	const rombongan = <?= $rombongan ? 'true' : 'false' ?>;
	const unit = document.getElementById('UnitKerja_id');
	const kotakUnit = document.getElementById('kotakUnit');
	const lainnya = document.getElementById('UnitLainnya');
	const kotakLainnya = document.getElementById('kotakUnitLainnya');

	function jenisTerpilih() {
		const r = document.querySelector('input[name="Jenis"]:checked');
		return r ? r.value : '';
	}

	function tampilkan(awal) {
		const jenis = rombongan ? jenisTerpilih() : '';
		const pakaiUnit = rombongan ? jenis === 'kemnaker' : true;
		const pakaiLainnya = rombongan ? jenis === LAINNYA : (unit && unit.value === LAINNYA);

		if (kotakUnit) {
			kotakUnit.hidden = !pakaiUnit;
			if (unit) unit.required = pakaiUnit;
		}
		if (kotakLainnya) {
			kotakLainnya.hidden = !pakaiLainnya;
			if (lainnya) lainnya.required = pakaiLainnya;
		}
		if (!awal && pakaiLainnya && lainnya) lainnya.focus();
	}

	if (unit) {
		unit.addEventListener('change', function () { tampilkan(false); });
	}
	document.querySelectorAll('input[name="Jenis"]').forEach(function (r) {
		r.addEventListener('change', function () { tampilkan(false); });
	});
	tampilkan(true);
})();
</script>
