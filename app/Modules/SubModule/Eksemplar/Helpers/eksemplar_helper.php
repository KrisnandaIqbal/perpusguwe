<?php

use Eksemplar\Models\EksemplarModel;

if (!function_exists('count_collections')) {
	function count_collections($catalog_id = null)
	{
		$model = new EksemplarModel();

		$data = $model->where('Catalog_id', $catalog_id)->countAllResults();
		return $data;
	}
}

if (!function_exists('data_catalog')) {
	function data_catalog($param)
	{
		$db      = \Config\Database::connect();

		$query = $db->table('catalog_ruas')->where('CatalogId', $param)->where('Tag', 245)
			->select('CatalogId AS ID,Value AS Value')->get();
		$queryx = $db->table('catalog_ruas')->where('CatalogId', $param)
			->select('CatalogId AS ID,Value AS Value')->get();
		$data = $query->getRow();
		$datax = $queryx->getResult();

		$catalog_json = array();
		foreach (array_filter(explode('$', $data->Value)) as $value) {
			$catalog_json[substr($value, 0, 1)] = substr($value, 2);
		}

		return json_encode($catalog_json);
	}
}

function get_branch_id($location_library_id)
{
	$db = db_connect();
	$query = $db->table('location_library')->select('Branch_id as no')->where('ID', $location_library_id)->get();
	$no = $query->getRow()->no;
	return $no;
}
function get_branch($branch_id)
{
	$db = db_connect();
	$query = $db->table('branchs')->where('ID', $branch_id)->get();
	$row = $query->getRow();
	return $row;
}

function gen_no_barcode($i = 1)
{
        $db = db_connect();
        $query = $db->table('collections')->select('MAX(RIGHT(NomorBarcode,5)) as no')->get();
        $no = $query->getRow()->no;

        if (empty($no)) {
                $no = $i;
        } else {
                $no = intval($no) + $i;
                $no = str_pad($no, 5, "0", STR_PAD_LEFT);
        }

        $formatResult = $db->table('settingparameters')
                     ->select('Value')
                     ->where('Name', 'FormatNomorInduk')
                     ->get()
                     ->getRow();

        $formatString = ($formatResult && !empty($formatResult->Value))
                ? $formatResult->Value
                : get_parameter('nomor-barcode', '{yyyy}/PN/{99999}');

        if (strpos($formatString, '|') === false) {
                $format2 = str_replace('{yyyy}', date('Y'), $formatString);
                return str_replace('{99999}', $no, $format2);
        }

        $formatArray = explode('|', $formatString);
        $result = '';
        $separatorOptions = ['2' => '', '3' => '/', '4' => '-', '5' => '.'];

        for ($j = 0; $j < count($formatArray); $j++) {
                $value = trim($formatArray[$j]);
                if ($j % 2 === 0) {
                        if ($value !== '0') {
                                if (preg_match('/\{([^}]+)\}/', $value, $matches)) {
                                        $result .= ($matches[1] === '99999') ? $no : $matches[1];
                                } elseif ($value === '6') {
                                        $result .= $no;
                                } elseif ($value === '7') {
                                        $result .= date('Y');
                                }
                                // posisi 2,3,4,5 (kode jenis/kategori/bentuk/sumber) sengaja dilewati:
                                // fungsi ini tidak menerima collectionData, jadi kode tersebut tidak tersedia di sini
                        }
                } else {
                        if ($value !== '2' && isset($separatorOptions[$value])) {
                                $result .= $separatorOptions[$value];
                        }
                }
        }

        return $result;
}

function gen_no_rfid($i = 1)
{
	$db = db_connect();
	$query = $db->table('collections')->select('MAX(RIGHT(RFID,5)) as no')->get();
	$no = $query->getRow()->no;
	$format = get_parameter('nomor-rfid', '{yyyy}/PN/{99999}');
	$format2 = str_replace('{yyyy}', date('Y'), $format);
	$format3 = str_replace('{99999}', '', $format2);

	if (empty($no)) {
		$no = $i;
	} else {
		$no = intval($no) + $i;
		$no = str_pad($no, 5, "0", STR_PAD_LEFT);
	}

	$number = $format3 . $no;
	return $number;
}

function gen_no_induk($i = 1)
{
	$db = db_connect();
	$query = $db->table('collections')->select('MAX(RIGHT(NoInduk,5)) as no')->get();
	$no = $query->getRow()->no;
	$format = get_parameter('nomor-induk', '{yyyy}/PN/{99999}');
	$format2 = str_replace('{yyyy}', date('Y'), $format);
	$format3 = str_replace('{99999}', '', $format2);

	if (empty($no)) {
		$no = $i;
	} else {
		$no = intval($no) + $i;
		$no = str_pad($no, 5, "0", STR_PAD_LEFT);
	}

	$number = $format3 . $no;
	return $number;
}
