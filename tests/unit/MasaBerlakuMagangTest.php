<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Masa berlaku Magang Biasa dihitung per anggota sejak mendaftar; yang serentak hanya Magang Nasional.
 * Memakai database lokal: baris jenis_anggota Magang Biasa / Magang Nasional diubah sementara lalu dikembalikan.
 *
 * @internal
 */
final class MasaBerlakuMagangTest extends CIUnitTestCase
{
    /** @var array<string, array|null> nilai asli per nama jenis; null berarti baris dibuat oleh tes */
    private array $asli = [];

    protected function setUp(): void
    {
        parent::setUp();
        helper('anggota');

        // Saat testing, CodeIgniter memakai grup "tests"; arahkan ke database lokal yang sama.
        $dbConfig        = config('Database');
        $dbConfig->tests = $dbConfig->default;

        if (! db_connect()->fieldExists('target_expire_date', 'jenis_anggota')) {
            $this->markTestSkipped('Kolom jenis_anggota.target_expire_date belum ada di database lokal.');
        }
    }

    protected function tearDown(): void
    {
        $db = db_connect();
        foreach ($this->asli as $nama => $baris) {
            if ($baris === null) {
                $db->table('jenis_anggota')->where('LOWER(jenisanggota)', $nama)->delete();
            } else {
                $db->table('jenis_anggota')->where('id', $baris['id'])->update([
                    'MasaBerlakuAnggota' => $baris['MasaBerlakuAnggota'],
                    'target_expire_date' => $baris['target_expire_date'],
                ]);
            }
        }
        parent::tearDown();
    }

    /** Mengatur baris jenis anggota untuk tes dan mengembalikan ID-nya. */
    private function atur(string $nama, int $hari, ?string $serentak): int
    {
        $db    = db_connect();
        $baris = $db->table('jenis_anggota')->where('LOWER(jenisanggota)', strtolower($nama))->get()->getRowArray();

        if ($baris === null) {
            $db->table('jenis_anggota')->insert(['jenisanggota' => $nama, 'MasaBerlakuAnggota' => $hari, 'target_expire_date' => $serentak]);
            $this->asli[strtolower($nama)] = null;

            return (int) $db->insertID();
        }

        $this->asli[strtolower($nama)] ??= $baris;
        $db->table('jenis_anggota')->where('id', $baris['id'])->update(['MasaBerlakuAnggota' => $hari, 'target_expire_date' => $serentak]);

        return (int) $baris['id'];
    }

    public function testMagangBiasaTigaPuluhHariSejakMendaftar(): void
    {
        $id = $this->atur('Magang Biasa', 30, null);

        $hasil = hitungMasaBerlaku($id, 'magang_biasa', '2026-10-25 10:00:00', '2026-10-21 09:15:00');

        $this->assertSame('2026-11-20 00:00:00', $hasil['end_date']);
        $this->assertSame('30 hari', $hasil['durasi']);
    }

    public function testMagangBiasaTidakSerentakWalauTanggalSerentakTersimpan(): void
    {
        // Data lama: mode Hari dulu ikut menyimpan tanggal serentak
        $id = $this->atur('Magang Biasa', 30, '2026-11-21');

        $budi = hitungMasaBerlaku($id, 'magang_biasa', '2026-10-25 10:00:00', '2026-10-01 08:00:00');
        $sari = hitungMasaBerlaku($id, 'magang_biasa', '2026-10-25 10:00:00', '2026-10-15 08:00:00');

        $this->assertSame('2026-10-31 00:00:00', $budi['end_date']);
        $this->assertSame('2026-11-14 00:00:00', $sari['end_date']);
    }

    public function testMagangBiasaTanpaTanggalMendaftarMemakaiTanggalKonfirmasi(): void
    {
        $id = $this->atur('Magang Biasa', 30, null);

        $this->assertSame('2026-11-24 00:00:00', hitungMasaBerlaku($id, 'magang_biasa', '2026-10-25 10:00:00', null)['end_date']);
    }

    public function testMagangNasionalTetapSerentak(): void
    {
        $id = $this->atur('Magang Nasional', 180, '2027-03-31');

        $budi = hitungMasaBerlaku($id, 'magang_nasional', '2026-10-25 10:00:00', '2026-10-01 08:00:00');
        $sari = hitungMasaBerlaku($id, 'magang_nasional', '2026-11-10 10:00:00', '2026-11-05 08:00:00');

        $this->assertSame('2027-03-31 00:00:00', $budi['end_date']);
        $this->assertSame('2027-03-31 00:00:00', $sari['end_date']);
    }
}
