<?php

namespace App\Models;

use CodeIgniter\Model;

class SubUnitModel extends Model
{
    protected $table            = 'master_subunit';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = false;
    protected $allowedFields    = ['unit_id', 'nama_subunit', 'sort', 'active'];
    protected $useTimestamps    = true;
    protected $createdField     = 'CreateDate';
    protected $updatedField     = 'UpdateDate';
    protected $validationRules  = [];
    protected $skipValidation   = true;

    /**
     * Mengambil semua sub-unit aktif berdasarkan ID unit kerja induk.
     * Diurutkan berdasarkan kolom sort ASC, id ASC.
     *
     * @param int $unitId
     * @return array
     */
    public function getActiveByUnitId(int $unitId): array
    {
        return $this->where('unit_id', $unitId)
                    ->where('active', 1)
                    ->orderBy('sort', 'ASC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    /**
     * Mengambil semua sub-unit aktif yang dikelompokkan berdasarkan unit_id.
     * Format: [unit_id => [ ['id' => 1, 'nama' => '...'], ... ]]
     * Sangat berguna untuk preloading data di form frontend.
     *
     * @return array
     */
    public function getAllActiveGrouped(): array
    {
        $rows = $this->where('active', 1)
                     ->orderBy('unit_id', 'ASC')
                     ->orderBy('sort', 'ASC')
                     ->orderBy('id', 'ASC')
                     ->findAll();

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row->unit_id][] = [
                'id'           => (int) $row->id,
                'unit_id'      => (int) $row->unit_id,
                'nama_subunit' => $row->nama_subunit,
                'sort'         => (int) $row->sort,
            ];
        }

        return $grouped;
    }

    /**
     * Memeriksa apakah suatu unit kerja induk memiliki sub-unit aktif.
     *
     * @param int $unitId
     * @return bool
     */
    public function unitHasSubUnits(int $unitId): bool
    {
        return $this->where('unit_id', $unitId)
                    ->where('active', 1)
                    ->countAllResults() > 0;
    }

    /**
     * Validasi di SERVER: memastikan sub-unit benar-benar milik unit yang dipilih dan aktif.
     *
     * @param int $unitId
     * @param int $subUnitId
     * @return bool
     */
    public function isValidSubUnit(int $unitId, int $subUnitId): bool
    {
        return $this->where('id', $subUnitId)
                    ->where('unit_id', $unitId)
                    ->where('active', 1)
                    ->countAllResults() > 0;
    }

    /**
     * Memeriksa apakah sub-unit sudah digunakan oleh data anggota di tabel members.
     * ATURAN DATA: Yang sudah dipakai anggota TIDAK boleh dihapus, hanya dinonaktifkan.
     *
     * @param int $subUnitId
     * @return bool
     */
    public function isUsedByMembers(int $subUnitId): bool
    {
        $db = db_connect();
        return $db->table('members')
                  ->where('SubUnitKerja_id', $subUnitId)
                  ->countAllResults() > 0;
    }

    /**
     * Mengambil data sub-unit beserta nama unit induknya (JOIN).
     *
     * @param int $id
     * @return object|null
     */
    public function getWithUnit(int $id)
    {
        return $this->select('master_subunit.*, master_unit.nama_unit')
                    ->join('master_unit', 'master_unit.id = master_subunit.unit_id', 'left')
                    ->where('master_subunit.id', $id)
                    ->first();
    }
}
