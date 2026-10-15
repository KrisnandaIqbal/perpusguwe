<?php

namespace UnitKerja\Models;

class UnitKerjaModel extends \Base\Models\DataModel
{
    protected $DBGroup              = 'default';
    protected $table                = 'master_unit';
    protected $primaryKey           = 'id';
    protected $returnType           = 'object';
    protected $useSoftDeletes       = false;
    protected $protectFields        = false;
    protected $allowedFields        = ['nama_unit', 'active', 'CreateDate'];
    protected $useTimestamps        = false;
    protected $createdField         = 'CreateDate';
    protected $updatedField         = '';
    protected $deletedField         = '';
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;

    /**
     * Menghitung berapa anggota yang menggunakan unit kerja ini
     */
    public function getMemberCount(int $unitId): int
    {
        $db = db_connect();
        return $db->table('members')->where('UnitKerja_id', $unitId)->countAllResults();
    }
}
