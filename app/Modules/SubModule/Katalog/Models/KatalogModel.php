<?php

namespace Katalog\Models;

class KatalogModel extends \Base\Models\DataModel
{
	protected $DBGroup              = 'data';
    protected $table      			= 'catalogs';
    protected $primaryKey 			= 'ID';
    protected $returnType     		= 'object';
    protected $useSoftDeletes 		= false;
    protected $protectFields 		= false;
    protected $useTimestamps 		= true;
    protected $createdField  		= 'CreateDate';
    protected $updatedField  		= 'UpdateDate';
    protected $deletedField  		= 'DeleteDate';
    protected $validationRules    	= [];
    protected $validationMessages 	= [];
    protected $skipValidation     	= true;

	function __construct()
    {
        parent::__construct();
    }

	/** Minimal pinjaman setahun di satu unit kerja supaya angka populernya dipakai; kurang dari ini dipakai populer umum. */
	const MIN_PINJAMAN_UNIT = 10;

	/**
	 * Kandidat baris "Paling banyak dipinjam": peminjam berbeda 365 hari terakhir,
	 * 'populer' dinormalisasi ke nilai tertinggi (rencana 2a poin 3).
	 * Dengan $unitKerjaId: hanya pinjaman anggota unit itu; kalau pinjamannya kurang dari MIN_PINJAMAN_UNIT, populer umum.
	 */
	function kandidatPopuler(int $limit = 60, ?int $unitKerjaId = null): array
	{
		$sejak = date('Y-m-d H:i:s', strtotime('-365 days'));
		if ($unitKerjaId && !$this->unitCukupPinjaman($unitKerjaId)) {
			$unitKerjaId = null;
		}

		$q = $this->db->table('catalogs')
			->select('catalogs.*, catalogs.ID AS id, COUNT(DISTINCT cli.member_id) AS peminjam', false)
			->join('collections col', 'col.Catalog_id = catalogs.ID')
			->join('collectionloanitems cli', 'cli.Collection_id = col.ID')
			->where('catalogs.IsOPAC', 1)
			->where('cli.LoanDate >=', $sejak);
		if ($unitKerjaId) {
			$q->join('members m', 'm.ID = cli.member_id')->where('m.UnitKerja_id', $unitKerjaId);
		}
		$rows = $q->groupBy('catalogs.ID')->orderBy('peminjam', 'DESC')->limit($limit)->get()->getResultArray();

		$maks = max(array_column($rows, 'peminjam') ?: [0]);
		foreach ($rows as &$r) {
			$r['populer'] = $maks > 0 ? $r['peminjam'] / $maks : 0;
		}
		return $rows;
	}

	/** true kalau anggota unit ini punya cukup pinjaman setahun terakhir (MIN_PINJAMAN_UNIT) untuk angka populer per unit. */
	function unitCukupPinjaman(int $unitKerjaId): bool
	{
		return $this->db->table('collectionloanitems cli')
			->join('members m', 'm.ID = cli.member_id')
			->where('m.UnitKerja_id', $unitKerjaId)
			->where('cli.LoanDate >=', date('Y-m-d H:i:s', strtotime('-365 days')))
			->countAllResults() >= self::MIN_PINJAMAN_UNIT;
	}

	/** Kandidat baris "Koleksi baru": CreateDate terbaru, opsional hanya awalan DDC (mis. '331' atau '3'). Tanpa skor, urutan dijaga Ranker (sort stabil). */
	function kandidatBaru(int $limit = 60, ?string $kelasDdc = null): array
	{
		$q = $this->db->table('catalogs')
			->select('catalogs.*, catalogs.ID AS id')
			->where('IsOPAC', 1);
		if ($kelasDdc) {
			$q->like('DeweyNo', $kelasDdc, 'after');
		}
		return $q->orderBy('CreateDate', 'DESC')->limit($limit)->get()->getResultArray();
	}

	/** Judul berbeda yang pernah dipinjam anggota, terbaru di atas. */
	function riwayatAnggota(int $memberId): array
	{
		return $this->db->table('collectionloanitems cli')
			->select('c.ID AS id, c.Title, c.Author, c.Subject, c.DeweyNo, MAX(cli.LoanDate) AS terakhir', false)
			->join('collections col', 'col.ID = cli.Collection_id')
			->join('catalogs c', 'c.ID = col.Catalog_id')
			->where('cli.member_id', $memberId)
			->groupBy('c.ID')
			->orderBy('terakhir', 'DESC')
			->get()->getResultArray();
	}

	/** Kandidat satu subjek, dibatasi $limit. */
	function kandidatSubjek(string $subjek, int $limit = 200): array
	{
		return $this->db->table('catalogs')
			->select('catalogs.*, catalogs.ID AS id')
			->where('IsOPAC', 1)
			->like('Subject', $subjek)
			->limit($limit)
			->get()->getResultArray();
	}

	// get all
    function get_null_quarantine()
    {
        // return $this->findAll();
        return $this->where('IsQUARANTINE', null)
            ->findAll();
    }

    // get all 
    function get_all_quarantine()
    {
        return $this->where('IsQUARANTINE', 1)
            ->findAll();
    }

    // get data by id
    function get_all_by_id($id)
    {
        $conn      = \Config\Database::connect('data');
        // $db = $conn->table($this->table);
        // return $db->where('ID', $id)
        //     ->get()
        //     ->getRowArray();

        return $conn->table('catalogs')->join('worksheets', 'worksheets.ID = catalogs.Worksheet_id')->where('catalogs.ID', $id)->get()->getRowArray();
    }

    // get data by id
    function get_by_id($id)
    {
        $conn      = \Config\Database::connect('data');
        $db = $conn->query("select catalog_ruas.*,catalogs.IsOPAC FROM catalogs INNER JOIN catalog_ruas ON catalogs.ID = catalog_ruas.CatalogId WHERE catalogs.id = $id");
        return $db->getResult();
    }

}
