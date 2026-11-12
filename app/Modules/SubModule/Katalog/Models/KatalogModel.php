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

	/**
	 * Kandidat baris "Paling banyak dipinjam": peminjam berbeda 365 hari terakhir,
	 * 'populer' dinormalisasi ke nilai tertinggi (rencana 2a poin 3).
	 */
	function kandidatPopuler(int $limit = 60): array
	{
		$rows = $this->db->table('catalogs')
			->select('catalogs.*, catalogs.ID AS id, COUNT(DISTINCT cli.member_id) AS peminjam', false)
			->join('collections col', 'col.Catalog_id = catalogs.ID')
			->join('collectionloanitems cli', 'cli.Collection_id = col.ID')
			->where('catalogs.IsOPAC', 1)
			->where('cli.LoanDate >=', date('Y-m-d H:i:s', strtotime('-365 days')))
			->groupBy('catalogs.ID')
			->orderBy('peminjam', 'DESC')
			->limit($limit)
			->get()->getResultArray();

		$maks = max(array_column($rows, 'peminjam') ?: [0]);
		foreach ($rows as &$r) {
			$r['populer'] = $maks > 0 ? $r['peminjam'] / $maks : 0;
		}
		return $rows;
	}

	/** Kandidat baris "Koleksi baru": CreateDate terbaru. Tanpa skor, urutan dijaga Ranker (sort stabil). */
	function kandidatBaru(int $limit = 60): array
	{
		return $this->db->table('catalogs')
			->select('catalogs.*, catalogs.ID AS id')
			->where('IsOPAC', 1)
			->orderBy('CreateDate', 'DESC')
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
