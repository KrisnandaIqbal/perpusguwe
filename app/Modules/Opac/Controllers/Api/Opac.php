<?php

namespace Opac\Controllers\Api;

use CodeIgniter\API\ResponseTrait;

class Opac extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;

	/**
	 * Mencatat satu klik (judul di hasil pencarian, atau tombol OneSearch) ke opac_click_log.
	 * Dipanggil lewat sendBeacon, jadi jawabannya tidak dibaca browser.
	 */
	public function click()
	{
		$target = (string) $this->request->getPost('target');
		if (!in_array($target, ['katalog', 'onesearch'], true)) {
			return $this->failValidationErrors('target tidak valid');
		}

		$searchLogId = (int) $this->request->getPost('search_log_id');
		$catalogId   = (int) $this->request->getPost('catalog_id');
		$position    = (int) $this->request->getPost('position');

		try {
			\Config\Database::connect('data')->table('opac_click_log')->insert([
				'search_log_id' => $searchLogId > 0 ? $searchLogId : null,
				'catalog_id'    => $catalogId > 0 ? $catalogId : null,
				'target'        => $target,
				'position'      => $position > 0 ? $position : null,
			]);
		} catch (\Throwable $e) {
			log_message('error', 'opac_click_log gagal: ' . $e->getMessage());
			return $this->fail('gagal mencatat', 500);
		}

		return $this->respondNoContent();
	}
}
