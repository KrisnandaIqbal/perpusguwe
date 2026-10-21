<?php

namespace GuestBook\Libraries;

use Registrasi\Libraries\IdentityValidator;

/**
 * Isian buku tamu yang sama untuk anggota dan bukan anggota: nama, unit asal, dan nomor WhatsApp.
 */
class IsianTamu
{
    /** Nilai pilihan satuan kerja untuk pengunjung dari luar Kemnaker. */
    public const LAINNYA = 'lainnya';

    /**
     * Satuan kerja aktif beserta unitnya, untuk dropdown.
     * "Kementerian Ketenagakerjaan Republik Indonesia" tidak ditawarkan karena bukan satuan kerja.
     */
    public static function pilihanUnit($db): array
    {
        $unitKerja = $db->table('master_unit')
            ->where('active', 1)
            ->notLike('nama_unit', 'Kementerian Ketenagakerjaan', 'after')
            ->orderBy('id', 'ASC')
            ->get()->getResult();

        return [
            'unit_kerja'        => $unitKerja,
            'sub_units_grouped' => (new \App\Models\SubUnitModel())->getAllActiveGrouped(),
        ];
    }

    /**
     * @param array $post            isian dari formulir
     * @param array $unitIds         ID satuan kerja yang boleh dipilih
     * @param array $subUnitsGrouped unit per satuan kerja, seperti SubUnitModel::getAllActiveGrouped()
     *
     * @return array{data: array, errors: array<string, string>}
     */
    public static function periksa(array $post, array $unitIds, array $subUnitsGrouped): array
    {
        $errors = [];

        $nama = trim(preg_replace('/\s+/u', ' ', (string) ($post['Nama'] ?? '')));
        if ($nama === '') {
            $errors['Nama'] = 'Nama wajib diisi.';
        } elseif (mb_strlen($nama) > 100) {
            $errors['Nama'] = 'Nama paling banyak 100 huruf.';
        }

        $unit        = trim((string) ($post['UnitKerja_id'] ?? ''));
        $unitKerjaId = null;
        $subUnitId   = null;
        $unitLainnya = null;

        if ($unit === self::LAINNYA) {
            $unitLainnya = trim(preg_replace('/\s+/u', ' ', (string) ($post['UnitLainnya'] ?? '')));
            if ($unitLainnya === '') {
                $errors['UnitLainnya'] = 'Tulis nama instansi atau asal Anda.';
            } elseif (mb_strlen($unitLainnya) > 150) {
                $errors['UnitLainnya'] = 'Nama instansi paling banyak 150 huruf.';
            }
        } elseif ($unit === '' || !in_array((int) $unit, array_map('intval', $unitIds), true)) {
            $errors['UnitKerja_id'] = 'Pilih satuan kerja, atau Lainnya kalau Anda dari luar Kemnaker.';
        } else {
            $unitKerjaId = (int) $unit;
            $pilihanSub  = array_map(fn ($s) => (int) $s['id'], $subUnitsGrouped[$unitKerjaId] ?? []);
            if ($pilihanSub !== []) {
                $sub = (int) ($post['SubUnitKerja_id'] ?? 0);
                if (!in_array($sub, $pilihanSub, true)) {
                    $errors['SubUnitKerja_id'] = 'Pilih biro, direktorat, atau unit Anda.';
                } else {
                    $subUnitId = $sub;
                }
            }
        }

        $hp = IdentityValidator::normalizePhone($post['HP'] ?? '');
        if (!IdentityValidator::isValidPhone($hp)) {
            $errors['HP'] = 'Nomor WhatsApp diawali 08 atau 62, 10 sampai 15 angka.';
        }

        return [
            'data' => [
                'Nama'            => $nama,
                'UnitKerja_id'    => $unitKerjaId,
                'SubUnitKerja_id' => $subUnitId,
                'UnitLainnya'     => $unitLainnya,
                'HP'              => $hp,
            ],
            'errors' => $errors,
        ];
    }

    /** Unit asal untuk ditampilkan di tabel admin. Butuh helper anggota. */
    public static function teksUnit(?string $unitNama, ?string $subUnitNama, ?string $unitLainnya): string
    {
        $lainnya = trim((string) $unitLainnya);

        return $lainnya !== '' ? $lainnya : format_unit_kerja($unitNama, $subUnitNama);
    }
}
