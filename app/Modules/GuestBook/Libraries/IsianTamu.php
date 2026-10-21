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

    /** Rombongan paling sedikit 6 orang; kurang dari itu mengisi buku tamu perorangan. */
    public const MIN_ROMBONGAN = 6;
    public const MAX_ROMBONGAN = 1000;

    /**
     * Isian rombongan: Jenis (kemnaker atau lainnya), penanggung jawab (Nama, HP), unit atau instansi,
     * dan CountPersonel. Pesan galat memakai nama isian yang sama dengan periksa().
     *
     * @return array{data: array, errors: array<string, string>}
     */
    public static function periksaRombongan(array $post, array $unitIds, array $subUnitsGrouped): array
    {
        $jenis = (string) ($post['Jenis'] ?? '');

        // Jenis menentukan isian mana yang berlaku: Kemnaker memilih unit, Lainnya menulis instansi
        if ($jenis === self::LAINNYA) {
            $post['UnitKerja_id'] = self::LAINNYA;
        } elseif ($jenis === 'kemnaker') {
            $post['UnitLainnya'] = '';
            if (($post['UnitKerja_id'] ?? '') === self::LAINNYA) {
                $post['UnitKerja_id'] = '';
            }
        }

        $hasil = self::periksa($post, $unitIds, $subUnitsGrouped);

        if (!in_array($jenis, ['kemnaker', self::LAINNYA], true)) {
            $hasil['errors'] = ['Jenis' => 'Pilih Kemnaker atau Lainnya.'] + $hasil['errors'];
            unset($hasil['errors']['UnitKerja_id'], $hasil['errors']['SubUnitKerja_id'], $hasil['errors']['UnitLainnya']);
        } elseif ($jenis === 'kemnaker' && isset($hasil['errors']['UnitKerja_id'])) {
            $hasil['errors']['UnitKerja_id'] = 'Pilih satuan kerja penanggung jawab.';
        }

        $jumlah = trim((string) ($post['CountPersonel'] ?? ''));
        if (!ctype_digit($jumlah) || (int) $jumlah < self::MIN_ROMBONGAN) {
            $hasil['errors']['CountPersonel'] = 'Rombongan paling sedikit ' . self::MIN_ROMBONGAN . ' orang, termasuk penanggung jawab.';
        } elseif ((int) $jumlah > self::MAX_ROMBONGAN) {
            $hasil['errors']['CountPersonel'] = 'Jumlah anggota paling banyak ' . self::MAX_ROMBONGAN . ' orang.';
        }

        $hasil['data']['Jenis']         = $jenis;
        $hasil['data']['CountPersonel'] = (int) $jumlah;

        return $hasil;
    }

    /** Unit asal sebagai teks: "Satuan Kerja - Unit", nama instansi, atau "-". */
    public static function teksUnit(?string $unitNama, ?string $subUnitNama, ?string $unitLainnya): string
    {
        helper('anggota');
        $lainnya = trim((string) $unitLainnya);

        return $lainnya !== '' ? $lainnya : format_unit_kerja($unitNama, $subUnitNama);
    }
}
