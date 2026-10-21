<?php

namespace GuestBook\Controllers;

use GuestBook\Libraries\IsianTamu;

class GuestBook extends \App\Controllers\BaseController
{
	public $memberguestModel;
	public $groupguestModel;
	public $uploadPath;
	public $modulePath;
	public $data = [];
	public $language;
	public $session;
	public $validation;
	public $db;
	public $settingModel;

	function __construct()
	{
		$this->language = \Config\Services::language();
		$this->language->setLocale('id');
         $this->session = session();
		 $this->db=\Config\Database::connect('data');
		 $this->validation = \Config\Services::validation();
		$this->memberguestModel = new \BukuTamu\Models\MemberGuestModel();
		$this->groupguestModel  =  new \BukuTamu\Models\GroupGuestModel();
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();

		helper('reference');
		helper('peminjaman');
		helper('pengembalian');
		// helper('member');
		helper('lokasiruang');
		helper('home');
	}

	
	public function index()
	{

		$this->data['SettingBukuTamu'] = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';
		
		// Get member number from request — trim to handle barcode scanner spaces
		$member_no = trim($this->request->getGet('member_no') ?? '');

		// Get member data from database
		$member = $member_no ? $this->db->table('members')->where('MemberNo', $member_no)->get()->getRow() : null;

		$locationId = $this->request->getCookie('Location_id') ?: null;

		$data = $this->lokasiTamu($locationId);


		if ($member) {
			$pilihan = IsianTamu::pilihanUnit($this->db);
			$this->data += $pilihan;

			$isian = $this->session->getFlashdata('isian_tamu') ?? [
				'Nama'            => $member->Fullname,
				'UnitKerja_id'    => $member->UnitKerja_id,
				'SubUnitKerja_id' => $member->SubUnitKerja_id,
				'HP'              => $member->NoHp ?: $member->Phone,
			];
			$this->data['isian']  = $isian;
			$this->data['errors'] = $this->session->getFlashdata('errors_tamu') ?? [];

			// Simpan otomatis hanya kalau data anggota sudah lengkap dan benar; selain itu anggota melengkapi dulu
			$this->data['isian_lengkap'] = $this->data['errors'] === [] && IsianTamu::periksa(
				$isian,
				array_column($pilihan['unit_kerja'], 'id'),
				$pilihan['sub_units_grouped']
			)['errors'] === [];
		}

		// Set data for view
		$this->data['title'] = 'Buku Tamu - Anggota';
		$this->data['data'] = $data;
		$this->data['member'] = $member;
		$this->data['tujuan_kunjungan'] = get_ref_table('tujuan_kunjungan', 'ID, TujuanKunjungan', 'Member=1', 'data');
		$this->data['message'] = $this->session->getFlashdata('message')
			?: ($member_no !== '' && !$member ? 'Nomor Anggota tidak ditemukan.' : null);

		// Render view
		return view('GuestBook\Views\add', $this->data);
	}


	/**
	 * Ruang tempat buku tamu dibuka (cookie Location_id). Buku tamu tidak lagi mewajibkannya;
	 * tanpa ruang, judul halaman memakai nama perpustakaan dan kunjungan tersimpan tanpa lokasi.
	 */
	private function lokasiTamu($locationId): object
	{
		$data = $locationId ? $this->db->table('locations as a')
			->select('a.ID, a.Code, a.Name')
			->select('b.Name as LocationLibrary_name, b.Code as LocationLibrary_code')
			->select('a.Branch_id, c.Name as Branch_name')
			->join('location_library as b', 'b.ID = a.LocationLibrary_id', 'left')
			->join('branchs as c', 'c.ID = a.Branch_id', 'left')
			->where('a.ID', $locationId)
			->get()->getRow() : null;

		return $data ?? (object) [
			'Name'                 => $this->db->table('settingparameters')->where('Name', 'NamaPerpustakaan')->get()->getRow()->Value ?? 'Perpustakaan Kemnaker RI',
			'LocationLibrary_name' => 'Buku Tamu',
			'Branch_id'            => null,
		];
	}

	public function lokasi()
	{
		$this->data['title'] = 'Setting- Lokasi';
		$this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors() : $this->session->getFlashdata('message');
		echo view('GuestBook\Views\lokasi', $this->data);
	}

	public function store_anggota()
	{
		$locationId = $this->request->getCookie('Location_id') ?: null;

		$member_no = trim((string) $this->request->getPost('member_no'));
		$member    = $member_no !== '' ? $this->db->table('members')->where('MemberNo', $member_no)->get()->getRow() : null;

		if (empty($member)) {
			$this->session->setFlashdata('message', 'Nomor Anggota tidak ditemukan.');
			return redirect()->to(base_url('buku-tamu'));
		}

		$pilihan = IsianTamu::pilihanUnit($this->db);
		$hasil   = IsianTamu::periksa(
			$this->request->getPost(),
			array_column($pilihan['unit_kerja'], 'id'),
			$pilihan['sub_units_grouped']
		);

		if ($hasil['errors'] !== []) {
			$this->session->setFlashdata('isian_tamu', $this->request->getPost());
			$this->session->setFlashdata('errors_tamu', $hasil['errors']);
			return redirect()->to(base_url('buku-tamu') . '?member_no=' . rawurlencode($member->MemberNo));
		}

		// Pendidikan, pekerjaan, dan alamat tetap diambil dari data anggota untuk Laporan Buku Tamu
		$save_data = $hasil['data'] + [
			'NoAnggota'             => $member->MemberNo,
			'PendidikanTerakhir_id' => $member->EducationLevel_id,
			'Profesi_id'            => $member->Job_id,
			'Alamat'                => $member->Address,
			'Location_id'           => $locationId,
			'Branch_id'             => $member->Branch_id,
		];

		$SettingBukuTamu = $this->settingModel->where('Name', 'SettingBukuTamu')->first()->Value ?? '0';

		// Tujuan kunjungan hanya disimpan kalau pilihannya ditampilkan (setting buku tamu = 1)
		if ($SettingBukuTamu == '1') {
			$save_data['TujuanKunjungan_id'] = $this->request->getPost('TujuanKunjungan_id');
		}

		if ($this->memberguestModel->insert($save_data)) {
			$this->session->setFlashdata('success', 'Kunjungan berhasil dicatat.');
		} else {
			$this->session->setFlashdata('message', 'Buku Tamu gagal disimpan');
		}

		return redirect()->to(base_url('buku-tamu'));
	}
	public function non_anggota($prefix = '')
	{
		$locationId = $this->request->getCookie('Location_id') ?: null;
		$data = $this->lokasiTamu($locationId);
		$this->data['title'] = 'Buku Tamu - Non Anggota';
		$this->data['data'] = $data;
		$branch_id = $data->Branch_id;

		$pilihan = IsianTamu::pilihanUnit($this->db);
		$this->data += $pilihan;
		$this->data['isian']  = [];
		$this->data['errors'] = [];

		if ($this->request->is('post')) {
			$hasil = IsianTamu::periksa(
				$this->request->getPost(),
				array_column($pilihan['unit_kerja'], 'id'),
				$pilihan['sub_units_grouped']
			);

			if ($hasil['errors'] === []) {
				$save_data = $hasil['data'] + [
					'Location_id' => $locationId,
					'Branch_id' => $branch_id,
					'CreateBy' => user_id(),
				];

				if ($this->memberguestModel->insert($save_data)) {
					$this->session->setFlashdata('success', 'Kunjungan berhasil dicatat.');
				} else {
					$this->session->setFlashdata('message', 'Buku Tamu gagal disimpan');
				}

				return redirect()->to('/buku-tamu/non_anggota');
			}

			$this->data['isian']  = $this->request->getPost();
			$this->data['errors'] = $hasil['errors'];
		}

		$this->data['title'] = 'Buku Tamu - Bukan Anggota';
		$this->data['message'] = $this->data['errors'] ? 'Periksa lagi isian yang ditandai merah.' : $this->session->getFlashdata('message');
		return view('GuestBook\Views\add_non_anggota', $this->data);
	}

	public function rombongan()
	{
		$locationId = $this->request->getCookie('Location_id') ?: null;
		$data = $this->lokasiTamu($locationId);
		$this->data['title'] = 'Buku Tamu - Rombongan';
		$this->data['data'] = $data;

		$pilihan = IsianTamu::pilihanUnit($this->db);
		$this->data += $pilihan;
		$this->data['isian']  = [];
		$this->data['errors'] = [];

		if ($this->request->is('post')) {
			$hasil = IsianTamu::periksaRombongan(
				$this->request->getPost(),
				array_column($pilihan['unit_kerja'], 'id'),
				$pilihan['sub_units_grouped']
			);

			if ($hasil['errors'] === []) {
				$isian = $hasil['data'];

				// AsalInstansi tetap berisi teks supaya tabel dan laporan rombongan yang lama terbaca
				$namaUnit = array_column($pilihan['unit_kerja'], 'nama_unit', 'id')[$isian['UnitKerja_id']] ?? null;
				$namaSub  = null;
				foreach ($pilihan['sub_units_grouped'][$isian['UnitKerja_id']] ?? [] as $sub) {
					if ($sub['id'] === $isian['SubUnitKerja_id']) {
						$namaSub = $sub['nama_subunit'];
					}
				}

				$save_data = [
					'NamaKetua'        => $isian['Nama'],
					'NomerTelponKetua' => $isian['HP'],
					'AsalInstansi'     => IsianTamu::teksUnit($namaUnit, $namaSub, $isian['UnitLainnya']),
					'UnitKerja_id'     => $isian['UnitKerja_id'],
					'SubUnitKerja_id'  => $isian['SubUnitKerja_id'],
					'AlamatInstansi'   => '',
					'CountPersonel'    => $isian['CountPersonel'],
					'CountTk'          => 0,
					'NoPengunjung'     => date('Ymd') . sprintf('%04d', random_int(1, 9999)),
					'Location_ID'      => $locationId,
					'Branch_id'        => $data->Branch_id,
					'CreateBy'         => user_id(),
					'CreateDate'       => date('Y-m-d H:i:s'),
					'CreateTerminal'   => $this->request->getIPAddress(),
				];

				if ($this->groupguestModel->insert($save_data)) {
					$this->session->setFlashdata('success', 'Kunjungan rombongan ' . $save_data['AsalInstansi'] . ' (' . $save_data['CountPersonel'] . ' orang) berhasil dicatat.');
				} else {
					$this->session->setFlashdata('message', 'Buku Tamu Rombongan gagal disimpan. Silakan coba lagi.');
				}

				return redirect()->to('/buku-tamu/rombongan');
			}

			$this->data['isian']  = $this->request->getPost();
			$this->data['errors'] = $hasil['errors'];
		}

		$this->data['message'] = $this->data['errors'] ? 'Periksa lagi isian yang ditandai merah.' : $this->session->getFlashdata('message');

		return view('GuestBook\Views\add_rombongan', $this->data);
	}

	public function getKnownFaces($prefix = '')
{
	$db = \Config\Database::connect();
	
    $query = $db->query("SELECT id, name, PhotoUrl FROM members");
    $results = $query->getResult();

    $knownFaces = [];
    foreach ($results as $row) {
        $photoPath = FCPATH . 'uploads/anggota/' . $row->PhotoUrl;
        if (file_exists($photoPath)) {
            $knownFaces[] = [
                'id' => $row->id,
                'name' => $row->name,
                'photoUrl' => base_url('uploads/anggota/' . $row->PhotoUrl)
            ];
        }
    }

    return $this->response->setJSON($knownFaces);
}
}
