<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Pendaftaran dan pengelolaan anggota kini hanya untuk ASN.
 * Jenis anggota lain tidak dihapus, hanya dinonaktifkan agar anggota lama tetap punya rujukan jenis.
 */
class NonaktifkanJenisAnggotaSelainAsn extends Migration
{
    private const JENIS_NONAKTIF = ['Non ASN', 'Magang Nasional', 'Magang Reguler'];

    public function up()
    {
        $this->db->table('jenis_anggota')
            ->whereIn('jenisanggota', self::JENIS_NONAKTIF)
            ->update(['active' => 0]);
    }

    public function down()
    {
        $this->db->table('jenis_anggota')
            ->whereIn('jenisanggota', self::JENIS_NONAKTIF)
            ->update(['active' => 1]);
    }
}
