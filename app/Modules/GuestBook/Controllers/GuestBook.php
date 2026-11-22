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
		// Lewat model() supaya tes bisa menggantinya dengan model tiruan
		$this->memberguestModel = model(\BukuTamu\Models\MemberGuestModel::class);
		$this->groupguestModel  = model(\BukuTamu\Models\GroupGuestModel::class);
		$this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();

		helper('reference');
		helper('peminjaman');
		helper('pengembalian');
		// helper('member');
		helper('lokasiruang');
		helper('home');
	}

	
	/** Pintu masuk buku tamu: langsung ke form bukan anggota; tab tipe lain tetap tersedia. */
	public function index()
	{
		// Tautan lama dari scanner (buku-tamu?member_no=...) tetap jalan
		if ($this->request->getGet('member_no') !== null) {
			return redirect()->to(base_url('buku-tamu/anggota') . '?' . http_build_query($this->request->getGet()));
		}

		return redirect()->to(base_url('buku-tamu/non_anggota'));
	}

	public function anggota()
	{

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
				'Nama'         => $member->Fullname,
				'UnitKerja_id' => $member->UnitKerja_id,
				'HP'           => $member->NoHp ?: $member->Phone,
			];
			$this->data['isian']  = $isian;
			$this->data['errors'] = $this->session->getFlashdata('errors_tamu') ?? [];

			// ?ubah= membuka form untuk mengoreksi kunjungan yang baru dicatat, tanpa kirim otomatis
			$ubah = (int) $this->request->getGet('ubah');
			$this->data['kunjungan_id'] = $ubah && $this->kunjunganBolehDiubah($ubah, $member) ? $ubah : null;

			// Data yang sudah lengkap dan benar langsung dikirim oleh halaman (POST); selain itu anggota melengkapi form dulu
			$this->data['kirim_otomatis'] = !$this->data['kunjungan_id'] && $this->data['errors'] === []
				&& IsianTamu::periksa($isian, array_column($pilihan['unit_kerja'], 'id'))['errors'] === [];
		}

		// Set data for view
		$this->data['title'] = 'Buku Tamu - Anggota';
		$this->data['data'] = $data;
		$this->data['member'] = $member;
		$this->data['message'] = $this->session->getFlashdata('message')
			?: ($member_no !== '' && !$member ? 'Nomor Anggota tidak ditemukan.' : null);
		$this->data['kunjungan_baru'] = $this->session->getFlashdata('kunjungan_baru');

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
			return redirect()->to(base_url('buku-tamu/anggota'));
		}

		$pilihan = IsianTamu::pilihanUnit($this->db);
		$hasil   = IsianTamu::periksa(
			$this->request->getPost(),
			array_column($pilihan['unit_kerja'], 'id')
		);

		if ($hasil['errors'] !== []) {
			$this->session->setFlashdata('isian_tamu', $this->request->getPost());
			$this->session->setFlashdata('errors_tamu', $hasil['errors']);
			return redirect()->to(base_url('buku-tamu/anggota') . '?member_no=' . rawurlencode($member->MemberNo));
		}

		$kunjunganId = (int) $this->request->getPost('kunjungan_id');
		if ($kunjunganId && $this->kunjunganBolehDiubah($kunjunganId, $member)) {
			return $this->ubahKunjunganAnggota($kunjunganId, $hasil['data']);
		}

		return $this->simpanKunjunganAnggota($member, $hasil['data'], $locationId);
	}

	/** Kunjungan yang boleh dikoreksi: milik anggota ini dan dicatat hari ini. */
	private function kunjunganBolehDiubah(int $id, object $member): bool
	{
		return $this->db->table('memberguesses')
			->where('ID', $id)
			->where('NoAnggota', $member->MemberNo)
			->where('DATE(CreateDate)', date('Y-m-d'))
			->countAllResults() > 0;
	}

	private function ubahKunjunganAnggota(int $id, array $isian)
	{
		try {
			$this->memberguestModel->update($id, $isian);
			$this->session->setFlashdata('success', 'Data kunjungan berhasil diubah.');
		} catch (\Throwable $e) {
			log_message('error', '[BukuTamu] Gagal mengubah kunjungan ' . $id . ': ' . $e->getMessage());
			$this->session->setFlashdata('message', 'Buku tamu gagal disimpan. Silakan hubungi petugas perpustakaan.');
		}

		return redirect()->to(base_url('buku-tamu/anggota'));
	}

	/** Mencatat kunjungan anggota dari isian yang sudah diperiksa, lalu kembali ke layar pindai. */
	private function simpanKunjunganAnggota(object $member, array $isian, $locationId)
	{
		// Pendidikan, pekerjaan, dan alamat tetap diambil dari data anggota untuk Laporan Buku Tamu
		$save_data = $isian + [
			'NoAnggota'             => $member->MemberNo,
			'PendidikanTerakhir_id' => $member->EducationLevel_id,
			'Profesi_id'            => $member->Job_id,
			'Alamat'                => $member->Address,
			'Location_id'           => $locationId,
			'Branch_id'             => $member->Branch_id,
		];

		$id = $this->simpanTamu($this->memberguestModel, $save_data, 'Kunjungan berhasil dicatat.');
		if ($id) {
			// Untuk tautan "Data kunjungan salah? Ubah" di layar pindai
			$this->session->setFlashdata('kunjungan_baru', ['id' => $id, 'member_no' => $member->MemberNo]);
		}

		return redirect()->to(base_url('buku-tamu/anggota'));
	}

	/**
	 * Menyimpan satu baris buku tamu dan menyiapkan pesan untuk pengunjung. Mengembalikan ID baris baru, atau null kalau gagal.
	 * Kesalahan database (misalnya migrasi belum di-import di server) dicatat di log; pengunjung cukup melihat pesan gagal.
	 */
	private function simpanTamu($model, array $data, string $pesanBerhasil): ?int
	{
		try {
			$id = (int) $model->insert($data) ?: null;
		} catch (\Throwable $e) {
			log_message('error', '[BukuTamu] Gagal menyimpan ke ' . get_class($model) . ': ' . $e->getMessage());
			$id = null;
		}

		if ($id) {
			$this->session->setFlashdata('success', $pesanBerhasil);
		} else {
			$this->session->setFlashdata('message', 'Buku tamu gagal disimpan. Silakan hubungi petugas perpustakaan.');
		}

		return $id;
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
				array_column($pilihan['unit_kerja'], 'id')
			);

			if ($hasil['errors'] === []) {
				$save_data = $hasil['data'] + [
					'Location_id' => $locationId,
					'Branch_id' => $branch_id,
					'CreateBy' => user_id(),
				];

				$this->simpanTamu($this->memberguestModel, $save_data, 'Kunjungan berhasil dicatat.');

				return redirect()->to('/buku-tamu/non_anggota');
			}

			$this->data['isian']  = $this->request->getPost();
			$this->data['errors'] = $hasil['errors'];
		}

		$this->data['title'] = 'Buku Tamu - Bukan Anggota';
		$this->data['message'] = $this->data['errors'] ? 'Periksa lagi isian yang ditandai merah.' : $this->session->getFlashdata('message');
		return view('GuestBook\Views\add', $this->data + ['tab' => 'non_anggota']);
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
				array_column($pilihan['unit_kerja'], 'id')
			);

			if ($hasil['errors'] === []) {
				$isian = $hasil['data'];

				// AsalInstansi tetap berisi teks supaya tabel dan laporan rombongan yang lama terbaca
				$namaUnit = array_column($pilihan['unit_kerja'], 'nama_unit', 'id')[$isian['UnitKerja_id']] ?? null;

				$save_data = [
					'NamaKetua'        => $isian['Nama'],
					'NomerTelponKetua' => $isian['HP'],
					'AsalInstansi'     => IsianTamu::teksUnit($namaUnit, null, $isian['UnitLainnya']),
					'UnitKerja_id'     => $isian['UnitKerja_id'],
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

				$this->simpanTamu($this->groupguestModel, $save_data, 'Kunjungan rombongan ' . $save_data['AsalInstansi'] . ' (' . $save_data['CountPersonel'] . ' orang) berhasil dicatat.');

				return redirect()->to('/buku-tamu/rombongan');
			}

			$this->data['isian']  = $this->request->getPost();
			$this->data['errors'] = $hasil['errors'];
		}

		$this->data['message'] = $this->data['errors'] ? 'Periksa lagi isian yang ditandai merah.' : $this->session->getFlashdata('message');

		return view('GuestBook\Views\add', $this->data + ['tab' => 'rombongan']);
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
