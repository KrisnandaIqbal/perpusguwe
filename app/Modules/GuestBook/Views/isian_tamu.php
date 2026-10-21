<?php
/**
 * Isian nama, unit asal, dan WhatsApp untuk buku tamu anggota dan bukan anggota.
 *
 * @var array  $isian             nilai awal: Nama, UnitKerja_id, SubUnitKerja_id, UnitLainnya, HP
 * @var array  $errors            pesan per isian dari IsianTamu::periksa()
 * @var array  $unit_kerja        satuan kerja dari IsianTamu::pilihanUnit()
 * @var array  $sub_units_grouped unit per satuan kerja
 */

use GuestBook\Libraries\IsianTamu;

$isian  = $isian ?? [];
$errors = $errors ?? [];
$nilai  = fn (string $kolom) => (string) ($isian[$kolom] ?? '');
$salah  = fn (string $kolom) => isset($errors[$kolom]) ? 'is-invalid' : '';
$pesan  = function (string $kolom) use ($errors) {
    return isset($errors[$kolom])
        ? '<div class="invalid-feedback" id="galat_' . $kolom . '">' . esc($errors[$kolom]) . '</div>'
        : '';
};
$unitTerpilih = $nilai('UnitKerja_id') !== '' ? $nilai('UnitKerja_id') : ($nilai('UnitLainnya') !== '' ? IsianTamu::LAINNYA : '');
?>
<style>
	.isian-tamu .form-group { margin-bottom: 25px; text-align: left; }
	.isian-tamu label { color: var(--text); font-weight: 600; margin-bottom: 8px; display: block; font-size: 0.95rem; }
	.isian-tamu label.required::after { content: " *"; color: #dc3545; font-weight: bold; }
	.isian-tamu .form-control { width: 100%; padding: 15px 20px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 16px; background-color: #fff; }
	.isian-tamu .form-control:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(3, 149, 80, 0.25); }
	.isian-tamu .form-control::placeholder { color: #6c757d; }
	.isian-tamu .form-control.is-invalid { border-color: #dc3545; box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1); }
	.isian-tamu .invalid-feedback { color: #c82333; font-size: 0.875rem; margin-top: 5px; display: block; }
	.isian-tamu .petunjuk { color: #5a6268; font-size: 0.875rem; margin-top: 5px; }
	.isian-tamu .isian-baris { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 0 20px; }
	.isian-tamu [hidden] { display: none !important; }
</style>

<div class="isian-tamu">
	<div class="form-group">
		<label for="Nama" class="required">Nama</label>
		<input type="text" class="form-control <?= $salah('Nama') ?>" name="Nama" id="Nama" maxlength="100"
			autocomplete="name" placeholder="Nama lengkap" value="<?= esc($nilai('Nama'), 'attr') ?>" required>
		<?= $pesan('Nama') ?>
	</div>

	<div class="isian-baris">
		<div class="form-group">
			<label for="UnitKerja_id" class="required">Satuan Kerja</label>
			<select class="form-control <?= $salah('UnitKerja_id') ?>" name="UnitKerja_id" id="UnitKerja_id" required>
				<option value="">Pilih satuan kerja</option>
				<?php foreach ($unit_kerja as $unit) : ?>
					<option value="<?= (int) $unit->id ?>" <?= $unitTerpilih === (string) $unit->id ? 'selected' : '' ?>><?= esc($unit->nama_unit) ?></option>
				<?php endforeach; ?>
				<option value="<?= IsianTamu::LAINNYA ?>" <?= $unitTerpilih === IsianTamu::LAINNYA ? 'selected' : '' ?>>Lainnya (dari luar Kemnaker)</option>
			</select>
			<?= $pesan('UnitKerja_id') ?>
		</div>

		<div class="form-group" id="kotakSubUnit" hidden>
			<label for="SubUnitKerja_id" class="required">Biro / Direktorat / Unit</label>
			<select class="form-control <?= $salah('SubUnitKerja_id') ?>" name="SubUnitKerja_id" id="SubUnitKerja_id"
				data-terpilih="<?= esc($nilai('SubUnitKerja_id'), 'attr') ?>">
				<option value="">Pilih biro, direktorat, atau unit</option>
			</select>
			<?= $pesan('SubUnitKerja_id') ?>
		</div>

		<div class="form-group" id="kotakUnitLainnya" hidden>
			<label for="UnitLainnya" class="required">Nama Instansi / Asal</label>
			<input type="text" class="form-control <?= $salah('UnitLainnya') ?>" name="UnitLainnya" id="UnitLainnya" maxlength="150"
				autocomplete="organization" placeholder="Contoh: nama kampus, perusahaan, atau instansi" value="<?= esc($nilai('UnitLainnya'), 'attr') ?>">
			<?= $pesan('UnitLainnya') ?>
		</div>
	</div>

	<div class="form-group">
		<label for="HP" class="required">Nomor WhatsApp</label>
		<input type="tel" class="form-control <?= $salah('HP') ?>" name="HP" id="HP" maxlength="20" inputmode="tel"
			autocomplete="tel" placeholder="Diawali 08 atau 62" value="<?= esc($nilai('HP'), 'attr') ?>"
			aria-describedby="petunjukHP" required>
		<?= $pesan('HP') ?>
		<div class="petunjuk" id="petunjukHP">Nomor aktif yang bisa dihubungi lewat WhatsApp.</div>
	</div>
</div>

<script>
(function () {
	const subUnits = <?= json_encode($sub_units_grouped ?? [], JSON_HEX_TAG | JSON_HEX_AMP) ?>;
	const LAINNYA = '<?= IsianTamu::LAINNYA ?>';
	const unit = document.getElementById('UnitKerja_id');
	const sub = document.getElementById('SubUnitKerja_id');
	const kotakSub = document.getElementById('kotakSubUnit');
	const lainnya = document.getElementById('UnitLainnya');
	const kotakLainnya = document.getElementById('kotakUnitLainnya');

	function tampilkan(awal) {
		const pilihan = subUnits[unit.value] || [];
		const terpilih = awal ? sub.dataset.terpilih : '';

		sub.length = 1;
		pilihan.forEach(function (s) {
			const opsi = new Option(s.nama_subunit, s.id);
			opsi.selected = String(s.id) === terpilih;
			sub.add(opsi);
		});

		kotakSub.hidden = pilihan.length === 0;
		sub.required = pilihan.length > 0;
		kotakLainnya.hidden = unit.value !== LAINNYA;
		lainnya.required = unit.value === LAINNYA;
		if (!awal && unit.value === LAINNYA) lainnya.focus();
	}

	unit.addEventListener('change', function () { tampilkan(false); });
	tampilkan(true);
})();
</script>
