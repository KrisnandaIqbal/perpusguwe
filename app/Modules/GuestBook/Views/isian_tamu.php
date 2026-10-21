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
$pesan     = function (string $kolom) use ($errors) {
    return isset($errors[$kolom])
        ? '<div class="invalid-feedback" id="galat_' . $kolom . '">' . esc($errors[$kolom]) . '</div>'
        : '';
};
$unitTerpilih = $nilai('UnitKerja_id') !== '' ? $nilai('UnitKerja_id') : ($nilai('UnitLainnya') !== '' ? IsianTamu::LAINNYA : '');
$jenis        = $nilai('Jenis');
?>
<style>
	.isian-tamu .form-group { margin-bottom: 25px; text-align: left; }
	.isian-tamu label { color: var(--text); font-weight: 600; margin-bottom: 8px; display: block; font-size: 0.95rem; }
	.isian-tamu label.required::after, .isian-tamu .judul-isian.required::after { content: " *"; color: #dc3545; font-weight: bold; }
	.isian-tamu .form-control { width: 100%; padding: 15px 20px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 16px; background-color: #fff; }
	.isian-tamu .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(3, 149, 80, 0.25); }
	.isian-tamu .form-control::placeholder { color: #6c757d; }
	.isian-tamu .form-control.is-invalid { border-color: #dc3545; box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1); }
	.isian-tamu .invalid-feedback { color: #c82333; font-size: 0.875rem; margin-top: 5px; display: block; }
	.isian-tamu .petunjuk { color: #5a6268; font-size: 0.875rem; margin-top: 5px; }
	.isian-tamu .isian-baris { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 0 20px; }
	.isian-tamu [hidden] { display: none !important; }
	.isian-tamu .judul-isian { color: var(--text); font-weight: 600; margin-bottom: 8px; font-size: 0.95rem; padding: 0; }
	.isian-tamu .pilihan-jenis { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
	.isian-tamu .pilihan-jenis input { position: absolute; opacity: 0; pointer-events: none; }
	.isian-tamu .pilihan-jenis label { margin: 0; padding: 16px 18px; border: 2px solid #e9ecef; border-radius: 10px; cursor: pointer; font-weight: 500; }
	.isian-tamu .pilihan-jenis label strong { display: block; font-size: 1rem; color: var(--text); }
	.isian-tamu .pilihan-jenis label span { color: #5a6268; font-size: 0.875rem; }
	.isian-tamu .pilihan-jenis input:checked + label { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(3, 149, 80, 0.15); }
	.isian-tamu .pilihan-jenis input:focus-visible + label { outline: 3px solid var(--primary); outline-offset: 2px; }
	.isian-tamu .pilihan-jenis.is-invalid label { border-color: #dc3545; }
</style>

<div class="isian-tamu">
	<?php if ($rombongan) : ?>
		<fieldset class="form-group" aria-describedby="<?= isset($errors['Jenis']) ? 'galat_Jenis' : '' ?>">
			<legend class="judul-isian required">Rombongan dari</legend>
			<div class="pilihan-jenis <?= $salah('Jenis') ?>">
				<div>
					<input type="radio" name="Jenis" id="jenis_kemnaker" value="kemnaker" <?= $jenis === 'kemnaker' ? 'checked' : '' ?> required>
					<label for="jenis_kemnaker"><strong>Kemnaker</strong><span>Satuan kerja di Kementerian Ketenagakerjaan</span></label>
				</div>
				<div>
					<input type="radio" name="Jenis" id="jenis_lainnya" value="<?= IsianTamu::LAINNYA ?>" <?= $jenis === IsianTamu::LAINNYA ? 'checked' : '' ?>>
					<label for="jenis_lainnya"><strong>Lainnya</strong><span>Sekolah, kampus, perusahaan, atau instansi lain</span></label>
				</div>
			</div>
			<?= $pesan('Jenis') ?>
		</fieldset>
	<?php endif; ?>

	<div class="form-group">
		<label for="Nama" class="required"><?= $rombongan ? 'Nama Penanggung Jawab' : 'Nama' ?></label>
		<input type="text" class="form-control <?= $salah('Nama') ?>" name="Nama" id="Nama" maxlength="100"
			autocomplete="name" placeholder="Nama lengkap" value="<?= esc($nilai('Nama'), 'attr') ?>" required>
		<?= $pesan('Nama') ?>
	</div>

	<div class="isian-baris">
		<div class="form-group" id="kotakUnit" <?= $rombongan && $jenis !== 'kemnaker' ? 'hidden' : '' ?>>
			<label for="UnitKerja_id" class="required">Satuan Kerja</label>
			<select class="form-control <?= $salah('UnitKerja_id') ?>" name="UnitKerja_id" id="UnitKerja_id" <?= $rombongan ? '' : 'required' ?>>
				<option value="">Pilih satuan kerja</option>
				<?php foreach ($unit_kerja as $unit) : ?>
					<option value="<?= (int) $unit->id ?>" <?= $unitTerpilih === (string) $unit->id ? 'selected' : '' ?>><?= esc($unit->nama_unit) ?></option>
				<?php endforeach; ?>
				<?php if (!$rombongan) : ?>
					<option value="<?= IsianTamu::LAINNYA ?>" <?= $unitTerpilih === IsianTamu::LAINNYA ? 'selected' : '' ?>>Lainnya (dari luar Kemnaker)</option>
				<?php endif; ?>
			</select>
			<?= $pesan('UnitKerja_id') ?>
		</div>

		<div class="form-group" id="kotakUnitLainnya" hidden>
			<label for="UnitLainnya" class="required"><?= $rombongan ? 'Nama Instansi' : 'Nama Instansi / Asal' ?></label>
			<input type="text" class="form-control <?= $salah('UnitLainnya') ?>" name="UnitLainnya" id="UnitLainnya" maxlength="150"
				autocomplete="organization" placeholder="Contoh: nama sekolah, kampus, perusahaan, atau instansi" value="<?= esc($nilai('UnitLainnya'), 'attr') ?>">
			<?= $pesan('UnitLainnya') ?>
		</div>
	</div>

	<div class="form-group">
		<label for="HP" class="required"><?= $rombongan ? 'Nomor WhatsApp Penanggung Jawab' : 'Nomor WhatsApp' ?></label>
		<input type="tel" class="form-control <?= $salah('HP') ?>" name="HP" id="HP" maxlength="20" inputmode="tel"
			autocomplete="tel" placeholder="Diawali 08 atau 62" value="<?= esc($nilai('HP'), 'attr') ?>"
			aria-describedby="petunjukHP" required>
		<?= $pesan('HP') ?>
		<div class="petunjuk" id="petunjukHP">Nomor aktif yang bisa dihubungi lewat WhatsApp.</div>
	</div>

	<?php if ($rombongan) : ?>
		<div class="form-group">
			<label for="CountPersonel" class="required">Jumlah Anggota Rombongan</label>
			<input type="number" class="form-control <?= $salah('CountPersonel') ?>" name="CountPersonel" id="CountPersonel"
				min="<?= IsianTamu::MIN_ROMBONGAN ?>" max="<?= IsianTamu::MAX_ROMBONGAN ?>" step="1" inputmode="numeric"
				placeholder="Paling sedikit <?= IsianTamu::MIN_ROMBONGAN ?> orang" value="<?= esc($nilai('CountPersonel'), 'attr') ?>"
				aria-describedby="petunjukJumlah" required>
			<?= $pesan('CountPersonel') ?>
			<div class="petunjuk" id="petunjukJumlah">Termasuk penanggung jawab. Kalau kurang dari <?= IsianTamu::MIN_ROMBONGAN ?> orang, setiap orang mengisi buku tamu Anggota atau Bukan Anggota.</div>
		</div>
	<?php endif; ?>
</div>

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
		const pakaiLainnya = rombongan ? jenis === LAINNYA : unit.value === LAINNYA;

		kotakUnit.hidden = !pakaiUnit;
		unit.required = pakaiUnit;
		kotakLainnya.hidden = !pakaiLainnya;
		lainnya.required = pakaiLainnya;
		if (!awal && pakaiLainnya) lainnya.focus();
	}

	unit.addEventListener('change', function () { tampilkan(false); });
	document.querySelectorAll('input[name="Jenis"]').forEach(function (r) {
		r.addEventListener('change', function () { tampilkan(false); });
	});
	tampilkan(true);
})();
</script>
