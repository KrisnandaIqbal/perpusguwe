<?php

namespace Anggota\Controllers\Api;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use App\Libraries\DataTable;
//use Hermawan\DataTables\DataTable;

class Anggota extends \Base\Controllers\BaseResourceController
{
	use ResponseTrait;
	public $auth;
	public $authorize;
	public $anggotaModel;
	public $validation;
	public $session;
	public $modulePath;
	public $uploadPath;
	public $regionModel;

	function __construct()
	{
		$this->anggotaModel = new \Anggota\Models\AnggotaModel();
		$this->regionModel = new \Region\Models\RegionModel();
		$this->validation = \Config\Services::validation();
		$this->session = is_cli() ? null : session();
		$this->modulePath = ROOTPATH . 'public/uploads/anggota/';
		$this->uploadPath = WRITEPATH . 'uploads/';

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}

		helper('reference');
		helper('thumbnail');
	}

	public function index()
	{
		$data = $this->anggotaModel->findAll();
		return $this->respond($data, 200);
	}

	/**
	 * Show an array of resource (paginated).
	 *
	 * @return mixed
	 */
	public function datatable($isKeranjang = 0)
	{
		/**
		
		 */
		$db = db_connect();
		$builder = $db->table('members as a');
		$builder->select([
			// Kolom dari tabel members (a)
			'a.ID',
			'a.ID as action',
			'a.ID as cid',
			'a.IsKeranjang',
			'a.FullName',
			'a.Phone',
			'a.Email',
			'a.PhotoUrl',
			'a.MemberNo',
			'a.RegisterDate',
			'a.EndDate',
			'a.JenisAnggota_id',
			'a.StatusAnggota_id',
			'a.AlasanPerbaikan',
			'a.NotifJenis',
			'a.NotifTerkirim',
			'a.NotifDate',
			'a.CreateDate',
			'a.Sex_id',
			
			// Kolom dari tabel join
			'jenis_anggota.jenisanggota as JenisAnggota',
			'status_anggota.Nama as StatusAnggota'
		]);
		$builder->join('jenis_anggota', 'jenis_anggota.id = a.JenisAnggota_id', 'left'); // Menggunakan LEFT JOIN
		$builder->join('status_anggota', 'status_anggota.id = a.StatusAnggota_id', 'left'); // Menggunakan LEFT JOIN
		
		if($isKeranjang == 1){
			$builder->where('a.IsKeranjang', $isKeranjang);
		}
		elseif($isKeranjang==0){
			$builder->where('a.IsKeranjang',$isKeranjang);
		}
       
		/**
		 * DataTables
		 * @var \Hermawan\DataTables\DataTable $dataTable
		 */
		$aktif = $db->table('status_anggota')->where('Nama', 'Aktif')->get()->getRow();
		$aktifId = $aktif->id ?? '';

		helper(['anggota', 'sirkulasi']);
		$perbaikanId = statusAnggotaId('Perlu Perbaikan') ?? '';
		$alasanList  = alasanPerbaikan(true);

		$dataTable = DataTable::of($builder)
			->addNumbering('no')
			->edit('cid', function ($row) {
				$html = '<input type="checkbox" class="check" name="ID[]" value="' . $row->cid . '">';
				return $html;
			})
			->edit('FullName', function ($row) {
				$default = get_default_member_avatar($row->Sex_id ?? null);
				$photoVersion = !empty($row->UpdateDate) ? '?v=' . strtotime($row->UpdateDate) : '';
				$photoName = trim(explode(',', (string) ($row->PhotoUrl ?? ''))[0]);
				$photoPath = ($photoName !== '') ? FCPATH . 'uploads/anggota/' . $photoName : null;
				$image = ($photoPath && is_file($photoPath)) ? base_url('uploads/anggota/' . $photoName . $photoVersion) : $default;

				$html =
					'<div class="widget-content p-0">
							<div class="widget-content-wrapper">
								<div class="widget-content-left mr-3">
									<a href="' . $image . '" class="image-link" onclick="if(typeof openSimplePopup===\'function\'){openSimplePopup(\'' . $image . '\', \'Pas Foto ' . esc($row->FullName, 'attr') . '\'); return false;}">
										<div style="width: 64px; height: 96px; aspect-ratio: 2/3; overflow: hidden; border-radius: 6px; border: 1px solid #d1d5db; box-shadow: 0 2px 4px rgba(0,0,0,0.1); background: #f8fafc; display: flex; align-items: center; justify-content: center;">
											<img class="lazy" data-src="' . $image . '" src="' . $image . '" id="lazy' . $row->ID . '" onerror="this.onerror=null;this.src=\'' . $default . '\';" alt="' . esc($row->FullName, 'attr') . '" style="width: 100%; height: 100%; aspect-ratio: 2/3; object-fit: cover; display: block;">
										</div>
									</a>
								</div>
						
								<div class="widget-content-left">
									<div class="widget-heading">' . $row->FullName . '</div>
									<div class="widget-subheading"><i class="fa fa-envelope"></i> ' . $row->Email . '</div>
									<div class="widget-subheading"><i class="fa fa-user"></i> ' . $row->JenisAnggota . '</div>
									<button type="button" 
										class="btn btn-secondary btn-sm mt-2 btn-upload-foto" 
										data-id="' . $row->ID . '" 
										data-format=".pdf,.jpg,.png" 
										data-format-title="Format (JPG|PNG). Max 1 Files @ 2MB" 
										data-field="PhotoUrl" 
										data-title="Upload Foto"
									><i class="fa fa-upload"></i> Upload Foto</button>

									<button type="button" 
										class="btn btn-secondary btn-sm mt-2 btn-ambil-foto" 
										data-id="' . $row->ID . '" 
										data-format=".pdf,.jpg,.png" 
										data-format-title="Format (JPG|PNG). Max 1 Files @ 2MB" 
										data-field="PhotoUrl" 
										data-title="Ambil Foto"
									><i class="fa fa-camera"></i> Ambil Foto</button>
								</div>
							</div>
						</div>';
				return $html;
			})
			->edit('MemberNo', function ($row) {
				$html =
					'<div class="widget-content p-0">
							<div class="widget-content-wrapper">
								<div class="widget-content-left mr-3">
									<i class="far fa-id-card fa-2x text-info"></i>
								</div>
								<div class="widget-content-left">
									<div class="widget-heading">' . $row->MemberNo . '</div>
								</div>
							</div>
						</div>';
				return $html;
			})
			->edit('RegisterDate', function ($row) {
				$html  =  '<badge class="badge badge-primary badge-pill">' . substr($row->RegisterDate, 0, 10) . '</badge><br>';
				return $html;
			})
			->edit('EndDate', function ($row) {
				$html  =  '<badge class="badge badge-warning badge-pill">' . substr($row->EndDate, 0, 10) . '</badge>';
				return $html;
			})
			->edit('StatusAnggota', function ($row) use ($perbaikanId, $alasanList, $db) {
				$statusName = $row->StatusAnggota ?? '-';
				$suspendInfo = function_exists('cek_suspend_anggota') ? cek_suspend_anggota($row->cid) : ['is_suspended' => false];

				if (!empty($suspendInfo['is_suspended'])) {
					$statusName = 'Suspend';
					if ((int)$row->StatusAnggota_id !== 5) {
						$db->table('members')->where('ID', $row->cid)->update(['StatusAnggota_id' => 5]);
					}
				} elseif ((int)$row->StatusAnggota_id === 5) {
					// Masa berlaku suspend telah selesai atau sanksi dicabut, kembalikan ke Aktif
					$db->table('members')->where('ID', $row->cid)->update(['StatusAnggota_id' => 3]);
					$statusName = 'Aktif';
				}

				$badgeClass = 'badge-secondary';
				$badgeStyle = '';
				switch (strtolower($statusName)) {
					case 'aktif':
						$badgeClass = 'badge-success';
						break;
					case 'menunggu':
						$badgeClass = 'badge-warning text-dark';
						break;
					case 'perlu perbaikan':
						$badgeClass = '';
						$badgeStyle = 'background-color: #fd7e14; color: #ffffff;';
						break;
					case 'baru':
						$badgeClass = 'badge-primary';
						break;
					case 'nonaktif':
						$badgeClass = 'badge-secondary';
						break;
					case 'suspend':
						$badgeClass = 'badge-danger';
						break;
					case 'bebas pustaka':
						$badgeClass = 'badge-alternate';
						$badgeStyle = 'background-color: #6f42c1; color: #ffffff;';
						break;
					default:
						$badgeClass = 'badge-secondary';
						break;
				}

				$styleAttr = $badgeStyle ? ' style="' . $badgeStyle . ' font-size: 12px; cursor: default;"' : ' style="font-size: 12px; cursor: default;"';
				$html = '<span class="badge ' . $badgeClass . ' py-1 px-2 font-weight-normal"' . $styleAttr . '>' . esc($statusName) . '</span>';

				if (!empty($suspendInfo['is_suspended']) && !empty($suspendInfo['suspend_until'])) {
					$untilFormatted = date('d/m/Y', strtotime($suspendInfo['suspend_until']));
					$html .= '<div class="small text-danger font-weight-bold text-center mt-1" style="font-size: 11px;"><i class="fa fa-ban"></i> s.d. ' . esc($untilFormatted) . '</div>';
				}

				// Info status email sebagai ikon amplop kecil dengan tooltip
				if (!empty($row->Email) && \App\Libraries\EmailPendaftaran::jenisUntuk((string) $row->MemberNo, $row->StatusAnggota_id)) {
					if (!empty($row->NotifJenis) && (string) $row->NotifTerkirim === '1') {
						$tglNotif = !empty($row->NotifDate) ? date('d/m/Y H:i', strtotime($row->NotifDate)) : '';
						$tooltipText = 'Email terkirim ' . $tglNotif;
						$html .= ' <span class="d-inline-block ml-1" data-toggle="tooltip" data-placement="top" title="' . esc($tooltipText, 'attr') . '"><i class="fa fa-envelope text-success" style="font-size: 13px;"></i></span>';
					} else {
						$tooltipText = 'Email pemberitahuan belum terkirim';
						$html .= ' <span class="d-inline-block ml-1" data-toggle="tooltip" data-placement="top" title="' . esc($tooltipText, 'attr') . '"><i class="far fa-envelope text-warning" style="font-size: 13px;"></i></span>';
					}
				}

				if ($perbaikanId !== '' && $row->StatusAnggota_id == $perbaikanId && isset($alasanList[$row->AlasanPerbaikan])) {
					$html .= '<div class="small text-muted text-center mt-1" style="font-size: 11px;">' . esc($alasanList[$row->AlasanPerbaikan]['label']) . '</div>';
				}

				return $html;
			})
		
			->edit('action', function ($row) {
				$isPending = ($row->StatusAnggota === 'Menunggu' || strpos((string) $row->MemberNo, 'PENDING-') === 0);
				if ($isPending) {
					$previewBtnClass = 'btn-warning btn-preview-pending text-dark';
					$previewTitle = 'Periksa pendaftaran ini (Menunggu persetujuan)';
				} else {
					$previewBtnClass = 'btn-info';
					$previewTitle = 'Pratinjau dan ubah status';
				}
				$preview = '<button type="button" class="btn ' . $previewBtnClass . ' btn-preview-status mr-1" data-id="' . $row->ID . '" data-summary="' . base_url('api/anggota/approval_summary/' . $row->ID) . '" data-href="' . base_url('api/anggota/switch/' . $row->ID) . '" data-reject-href="' . base_url('api/anggota/minta_perbaikan/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="' . $previewTitle . '"><i class="fa fa-eye font-weight-bold"></i></button>';
				$edit = '<a href="' . base_url('anggota/edit/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Ubah" class="btn btn-primary"><i class="pe-7s-note font-weight-bold"> </i></a>';
				$delete = '<a href="javascript:void(0);" data-href="' . base_url('anggota/delete/' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Hapus" class="btn btn-danger remove-data ml-1"><i class="pe-7s-trash font-weight-bold"> </i></a>';
				if ($row->IsKeranjang == 1) {
					$edit = '<a href="' . base_url('anggota/pulihkan_keranjang?ID[]=' . $row->ID) . '" data-toggle="tooltip" data-placement="top" title="Pulihkan" class="btn btn-warning ml-1"><i class="fa fa-undo font-weight-bold"> </i></a>';
				}
				return $preview . $edit . ' ' . $delete;
			})
			->toJson();

		return $dataTable;
}
	// Data yang diisi pendaftar di form pendaftaran online, untuk konfirmasi persetujuan
	public function approval_summary($id = null)
	{
		$db = db_connect();
		$row = $db->table('members as a')
			->select('a.ID, a.MemberNo, a.JenisAnggota_id, a.StatusAnggota_id, a.Fullname, a.IdentityNo, a.Sex_id, a.status_non_asn, a.AddressNow, a.Email, a.NoHp, a.Phone, a.PhotoUrl, a.RegisterDate, a.EndDate, a.AlasanPerbaikan, a.CatatanPerbaikan, a.NotifJenis, a.NotifTerkirim, a.NotifDate, jenis_anggota.jenisanggota as JenisAnggota, master_jenis_identitas.Nama as JenisIdentitas, jenis_kelamin.Name as JenisKelamin, master_unit.nama_unit as UnitKerja, master_subunit.nama_subunit as SubUnitKerja, status_anggota.Nama as StatusAnggota')
			->join('jenis_anggota', 'jenis_anggota.id = a.JenisAnggota_id', 'left')
			->join('master_jenis_identitas', 'master_jenis_identitas.id = a.IdentityType_id', 'left')
			->join('jenis_kelamin', 'jenis_kelamin.ID = a.Sex_id', 'left')
			->join('master_unit', 'master_unit.id = a.UnitKerja_id', 'left')
			->join('master_subunit', 'master_subunit.id = a.SubUnitKerja_id', 'left')
			->join('status_anggota', 'status_anggota.id = a.StatusAnggota_id', 'left')
			->where('a.ID', $id)
			->get()->getRow();

		if (!$row) {
			return $this->failNotFound('Data anggota tidak ditemukan');
		}

		helper(['anggota', 'sirkulasi']);
		$alasan     = alasanPerbaikan(true)[$row->AlasanPerbaikan] ?? null;
		$sebelumnya = $alasan ? trim($alasan['label'] . '. ' . $row->CatatanPerbaikan) : null;

		$suspendInfo = function_exists('cek_suspend_anggota') ? cek_suspend_anggota($row->ID) : ['is_suspended' => false];
		$isSuspended = !empty($suspendInfo['is_suspended']);
		$suspendUntil = !empty($suspendInfo['suspend_until']) ? date('d/m/Y', strtotime($suspendInfo['suspend_until'])) : null;
		$statusName = $row->StatusAnggota ?? '-';
		if ($isSuspended) {
			$statusName = 'Suspend';
		}

		// Hitung preview masa berlaku (terhitung dari tanggal hari ini saat petugas memeriksa)
		$mbPreview  = hitungMasaBerlaku($row->JenisAnggota_id ?? $row->JenisAnggota, $row->status_non_asn, date('Y-m-d H:i:s'), $row->RegisterDate);

		// Daftar status anggota untuk pilihan radio ubah status
		$statusList = $db->table('status_anggota')->select('id, Nama')->orderBy('id', 'ASC')->get()->getResultArray();

		// Info email pendaftaran
		$emailInfo = '';
		$bisaKirimUlang = false;
		if (!empty($row->Email) && \App\Libraries\EmailPendaftaran::jenisUntuk((string) $row->MemberNo, $row->StatusAnggota_id)) {
			$bisaKirimUlang = true;
			if (!empty($row->NotifJenis) && (string) $row->NotifTerkirim === '1') {
				$emailInfo = 'Email terkirim pada ' . date('d/m/Y H:i', strtotime($row->NotifDate)) . '.';
			} else {
				$emailInfo = 'Email pemberitahuan belum terkirim.';
			}
		}

		$formattedUnit = format_unit_kerja($row->UnitKerja, $row->SubUnitKerja);

		return $this->respond([
			'ID'                  => $row->ID,
			'MemberNo'            => $row->MemberNo ?? '-',
			'StatusAnggota_id'    => $row->StatusAnggota_id,
			'StatusAnggota'       => $statusName,
			'is_suspended'        => $isSuspended,
			'suspend_until'       => $suspendUntil,
			'suspend_days'        => $suspendInfo['suspend_days'] ?? 0,
			'suspend_message'     => $suspendInfo['message'] ?? '',
			'JenisAnggota'        => $row->JenisAnggota,
			'StatusNonAsn'        => !empty($row->status_non_asn) ? format_status_non_asn($row->status_non_asn) : null,
			'status_non_asn'      => !empty($row->status_non_asn) ? format_status_non_asn($row->status_non_asn) : null,
			'JenisIdentitas'      => $row->JenisIdentitas,
			'IdentityNo'          => $row->IdentityNo,
			'Fullname'            => $row->Fullname,
			'Sex_id'              => $row->Sex_id,
			'JenisKelamin'        => $row->JenisKelamin ?? '-',
			'UnitKerja'           => $formattedUnit ?: ($row->UnitKerja ?: '-'),
			'UnitKerjaInduk'      => $row->UnitKerja,
			'SubUnitKerja'        => $row->SubUnitKerja,
			'UnitKerjaFull'       => $formattedUnit,
			'AddressNow'          => $row->AddressNow,
			'Email'               => $row->Email,
			'Phone'               => $row->NoHp ?: $row->Phone,
			'PhotoUrl'            => !empty($row->PhotoUrl) ? base_url('uploads/anggota/' . $row->PhotoUrl) : null,
			'RegisterDate'        => $row->RegisterDate,
			'EndDate'             => !empty($row->EndDate) ? date('d/m/Y', strtotime($row->EndDate)) : '-',
			'MasaBerlakuPreview'  => $mbPreview['preview_text'],
			'MasaBerlakuDurasi'   => $mbPreview['durasi'],
			'MasaBerlakuEndDate'  => $mbPreview['end_date_formatted'],
			'MasaBerlakuLabel'    => $mbPreview['label_status'],
			'PerbaikanSebelumnya' => $sebelumnya,
			'EmailInfo'           => $emailInfo,
			'BisaKirimUlang'      => $bisaKirimUlang,
			'KirimUlangUrl'       => base_url('api/anggota/kirim_ulang_email/' . $row->ID),
			'StatusList'          => $statusList,
		]);
	}

	// Pendaftaran belum bisa diterima: kirim email penolakan dengan alasan & hapus data pendaftar agar bisa daftar ulang
	public function minta_perbaikan($id = null)
	{
		helper('anggota');

		// 1. Cek sesi petugas
		if (!logged_in()) {
			return $this->simpleResponse(['error' => true, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
		}

		// 2. Ambil input
		$request     = $this->request ?? service('request');
		$alasanInput = $request->getPost('alasan');
		$catatanRaw  = (string) $request->getPost('catatan');

		// Normalisasi alasan menjadi array
		$alasanCodes = is_array($alasanInput) ? $alasanInput : (strlen(trim((string) $alasanInput)) ? [trim((string) $alasanInput)] : []);
		$masterAlasan = alasanPerbaikan(true);

		// Whitelist validasi alasan
		$validAlasanCodes = [];
		foreach ($alasanCodes as $kode) {
			$kode = trim((string) $kode);
			if (isset($masterAlasan[$kode])) {
				$validAlasanCodes[] = $kode;
			}
		}

		// Validasi catatan (maks 300 karakter, sanitasi tag HTML dan control characters)
		$catatan = strip_tags($catatanRaw);
		$catatan = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $catatan);
		$catatan = trim($catatan);
		if (mb_strlen($catatan) > 300) {
			$catatan = mb_substr($catatan, 0, 300);
		}

		// Validasi minimal 1 alasan
		if (empty($validAlasanCodes)) {
			return $this->simpleResponse(['error' => true, 'message' => 'Pilih minimal satu alasan pendaftaran belum bisa diterima.']);
		}

		// Jika hanya memilih alasan 'lainnya' dan catatan kosong, minta petugas mengisi catatan
		if (count($validAlasanCodes) === 1 && $validAlasanCodes[0] === 'lainnya' && $catatan === '') {
			return $this->simpleResponse(['error' => true, 'message' => 'Untuk alasan Lainnya, silakan tulis penjelasannya pada kolom catatan.']);
		}

		// 3. Ambil data pendaftar
		$member = $this->anggotaModel->find($id);
		if (!$member) {
			return $this->simpleResponse(['error' => true, 'message' => 'Data pendaftar tidak ditemukan.']);
		}

		// Pastikan hanya pendaftar online baru yang belum memiliki No. Anggota definitif (diawali PENDING-)
		if (strpos((string) $member->MemberNo, 'PENDING-') !== 0) {
			return $this->simpleResponse(['error' => true, 'message' => 'Hanya pendaftar online baru (PENDING-) yang dapat ditolak dan dihapus datanya.']);
		}

		// 4. Periksa relasi database (foreign keys / data transaksi)
		// Hanya transaksi sejak pendaftar dibuat yang dihitung: baris lebih lama milik anggota yang sudah
		// dihapus (atau sisa migrasi) dan kebetulan menunjuk ke ID yang sama, bukan riwayat pendaftar ini.
		$db         = db_connect();
		$sejak      = $member->CreateDate ?? $member->RegisterDate ?? null;
		$hitungSejak = function (string $tabel, string $kolom) use ($db, $id, $sejak): int {
			$builder = $db->table($tabel)->where($kolom, $id);
			if (!empty($sejak)) {
				$builder->where('CreateDate >=', $sejak);
			}
			return $builder->countAllResults();
		};
		$loanCount        = $hitungSejak('collectionloans', 'Member_id');
		$loanItemCount    = $hitungSejak('collectionloanitems', 'member_id');
		$pelanggaranCount = $hitungSejak('pelanggaran', 'Member_id');
		$sumbanganCount   = $hitungSejak('sumbangan', 'Member_id');

		if ($loanCount > 0 || $loanItemCount > 0 || $pelanggaranCount > 0 || $sumbanganCount > 0) {
			return $this->simpleResponse([
				'error'   => true,
				'message' => 'Data pendaftar tidak dapat dihapus karena memiliki riwayat transaksi (peminjaman/pelanggaran/sumbangan) di perpustakaan.'
			]);
		}

		// 5. Simpan detail pendaftar di memori sebelum dihapus
		$namaPendaftar  = $member->Fullname;
		$emailPendaftar = $member->Email;
		$photoFile      = !empty($member->PhotoUrl) ? FCPATH . 'uploads/anggota/' . $member->PhotoUrl : null;

		// 6. Kirim email penolakan terlebih dahulu
		$emailLib = new \App\Libraries\EmailPendaftaran();
		$emailRes = $emailLib->kirimPenolakan($member, $validAlasanCodes, $catatan);

		// JIKA email gagal dikirim: batalkan penghapusan data pendaftar
		if (empty($emailRes['terkirim'])) {
			return $this->simpleResponse([
				'error'   => true,
				'message' => 'Email gagal dikirim ke pendaftar: ' . esc($emailRes['pesan']) . '<br>Data pendaftar <b>tidak dihapus</b> agar dapat diperiksa kembali.'
			]);
		}

		// 7. JIKA email berhasil dikirim: jalankan query hapus data pendaftar dari tabel members dalam database transaction
		$db->transBegin();

		$deleted = $db->table('members')->where('ID', $id)->delete();

		if ($db->transStatus() === false || !$deleted) {
			$db->transRollback();
			log_message('error', '[minta_perbaikan] Gagal menghapus pendaftar ID ' . $id . ' dari database setelah email terkirim.');
			return $this->simpleResponse([
				'error'   => true,
				'message' => 'Email pemberitahuan berhasil dikirim, namun data pendaftar gagal dihapus dari database. Silakan hapus secara manual.'
			]);
		}

		$db->transCommit();

		// 8. Hapus file pas foto dari direktori uploads jika ada
		if ($photoFile && file_exists($photoFile) && is_file($photoFile)) {
			@unlink($photoFile);
		}

		return $this->simpleResponse([
			'error'      => false,
			'deleted_id' => $id,
			'message'    => 'Pendaftaran <b>' . esc($namaPendaftar) . '</b> ditolak.<br>Email pemberitahuan telah dikirim ke ' . esc($emailPendaftar) . ' dan data pendaftar telah <b>dihapus permanen</b> agar pendaftar dapat mengisi ulang datanya.',
		]);
	}

	public function kirim_ulang_email($id = null)
	{
		$email = (new \App\Libraries\EmailPendaftaran())->kirimUlang($id);

		return $this->simpleResponse([
			'error'   => !$email['terkirim'],
			'message' => esc($email['pesan']),
		]);
	}

	// Keputusan admin tetap tersimpan walau email gagal; admin diarahkan ke tombol kirim ulang
	private function pesanEmail(array $email, string $terkirim): string
	{
		return $email['terkirim']
			? $terkirim
			: '<b>Email pemberitahuan belum terkirim.</b> ' . esc($email['pesan']) . '<br>Kirim ulang dari kolom Status Anggota.';
	}

	public function detail($id = null)
	{
		$data = $this->anggotaModel->find($id);
		if ($data) {
			return $this->respond($data);
		} else {
			return $this->failNotFound('No Data Found with id ' . $id);
		}
	}

	public function create()
	{
		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$save_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$newAnggotaId = $this->anggotaModel->insert($save_data);
			if ($newAnggotaId) {
				$this->session->setFlashdata('toastr_msg', lang('Anggota.info.successfully_saved'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Anggota.info.successfully_saved')
					]
				];
				return $this->respondCreated($response);
			} else {
				$response = [
					'status'   => 400,
					'error'    => null,
					'messages' => [
						'error' =>  lang('Anggota.info.failed_saved')
					]
				];
				return $this->fail($response);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function edit($id = null)
	{
		$this->validation->setRule('name', 'Nama', 'required');
		if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
			$slug = url_title($this->request->getPost('name'), '-', TRUE);
			$update_data = array(
				'name' => $this->request->getPost('name'),
				'slug' => $slug,
				'sort' => $this->request->getPost('sort'),
				'description' => $this->request->getPost('description'),
			);

			$anggotaUpdate = $this->anggotaModel->update($id, $update_data);
			if ($anggotaUpdate) {
				$this->session->setFlashdata('toastr_msg', lang('Anggota.info.successfully_updated'));
				$this->session->setFlashdata('toastr_type', 'success');
				$response = [
					'status'   => 201,
					'error'    => null,
					'messages' => [
						'success' => lang('Anggota.info.successfully_updated')
					]
				];
				return $this->respond($response);
			} else {
				return $this->fail('<div class="alert alert-danger fade show" role="alert">' . lang('Anggota.info.failed_updated') . '</div>', 400);
			}
		} else {
			$message = $this->validation->listErrors();
			return $this->fail($message, 400);
		}
	}

	public function delete($id = null)
	{
		$data = $this->anggotaModel->find($id);
		if ($data) {
			$this->anggotaModel->delete($id);
			$response = [
				'status'   => 200,
				'error'    => null,
				'messages' => [
					'success' => lang('Anggota.info.successfully_deleted')
				]
			];
			return $this->respondDeleted($response);
		} else {
			return $this->failNotFound(lang('Anggota.info.not_found') . ' ID:' . $id);
		}
	}

	public function hapusall()
	{

		$anggota_id = $this->request->getGet('anggota_id');

		$jmldata = count($anggota_id);

		// dd('stop');
		for ($i = 0; $i < $jmldata; $i++) {
			//check
			$cekdata = $this->anggotaModel->find($anggota_id[$i]);
			// dd($cekdata);

			$fotolama = $cekdata->Fullname;
			// dd($fotolama);
			// if ($fotolama != 'default.png') {
			//     unlink_file($this->modulePath, $anggota->PhotoUrl);
			// }
			$this->anggotaModel->delete($anggota_id[$i]);
		}

		$msg = [
			'sukses' => "$jmldata Data berhasil dihapus"
		];
		return redirect()->to('/anggota');
	}
	public function cities($code = '11')
	{
		$propinsi_id = $this->request->getGet('propinsi_id');
		if (!empty($propinsi_id)) {
			$data = get_dropdown('m_kota', 'propinsi_id = ' . $propinsi_id);
		} else {
			$data = get_dropdown('m_kota');
		}
		// $data= $this->regionModel
		// ->select('code,name')
		// ->where('level', 2)
		// ->like('code', $code)
		// ->findAll();

		return $this->respond($data, 200);
	}

	public function get_date()
	{
		$date = date('Y-m-d');
		return $this->respond($date, 200);
	}

	public function upload_file()
	{
		$upload_id = $this->request->getPost('upload_id');
		$upload_field = $this->request->getPost('upload_field');
		$upload_title = $this->request->getPost('upload_title');

		$update_data = [];
		$files = (array) $this->request->getPost('file_pendukung');
		if (count($files)) {
			$listed_file = array();
			foreach ($files as $uuid => $name) {
				if (file_exists($this->uploadPath . $name)) {
					$file = new File($this->uploadPath . $name);
					$newFileName = $file->getRandomName();
					$file->move($this->modulePath, $newFileName);
					$listed_file[] = $newFileName;
				}
			}
			$update_data['PhotoUrl'] = implode(',', $listed_file);
		}

		$anggota = $this->anggotaModel->find($upload_id);
		$anggotaUpdate = $this->anggotaModel->update($upload_id, $update_data);
		if ($anggotaUpdate) {
			$this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Upload file berhasil'
				]
			];
			return $this->respondCreated($response);
		} else {
			$response = [
				'status'   => 400,
				'error'    => null,
				'messages' => [
					'error' => 'Upload file gagal'
				]
			];
			return $this->fail($response);
		}
	}

	public function capture_file()
	{
		$capture_id = $this->request->getPost('capture_id');

		$base64_string = $this->request->getPost('camera_image');
		if (!empty($base64_string)) {
			$file = new File($this->uploadPath);
			$newFileName = $file->getRandomName() . '.jpg';
			base64_to_jpeg($base64_string, $this->modulePath . $newFileName);
			$update_data['PhotoUrl'] =  $newFileName;
		}

		$anggota = $this->anggotaModel->find($capture_id);
		$anggotaUpdate = $this->anggotaModel->update($capture_id, $update_data);
		if ($anggotaUpdate) {
			$this->session->setFlashdata('toastr_msg', 'Upload file berhasil');
			$this->session->setFlashdata('toastr_type', 'success');
			$response = [
				'status'   => 201,
				'error'    => null,
				'messages' => [
					'success' => 'Upload file berhasil'
				]
			];
			return $this->respondCreated($response);
		} else {
			$response = [
				'status'   => 400,
				'error'    => null,
				'messages' => [
					'error' => 'Upload file gagal'
				]
			];
			return $this->fail($response);
		}
	}

	public function switch($id = null)
	{
		// Cek sesi login petugas
		if (!logged_in()) {
			return $this->simpleResponse(['error' => true, 'message' => 'Sesi Anda telah berakhir. Silakan login kembali.']);
		}
		// Cek hak akses edit anggota
		if (!is_allowed('anggota/edit') && !is_member('admin')) {
			return $this->simpleResponse(['error' => true, 'message' => 'Maaf, Anda tidak memiliki hak akses untuk mengubah status anggota.']);
		}

		$field = $this->request->getPost('field');
		$value = $this->request->getPost('value');
		$catatan = trim((string) $this->request->getPost('catatan'));

		$update_data = array($field => $value);
		$pelanggaranDicabut = 0;
		if ($field === 'StatusAnggota_id') {
			helper(['anggota', 'sirkulasi']);
			if ((int) $value === statusAnggotaId('Perlu Perbaikan')) {
				return $this->simpleResponse([
					'error'   => true,
					'message' => 'Status Perlu Perbaikan harus disertai alasan. Silakan gunakan menu Belum Bisa Diterima.',
				]);
			}
			if (!empty($catatan)) {
				$update_data['CatatanPerbaikan'] = strip_tags($catatan);
			}
			$update_data = array_merge($update_data, memberApprovalData($id, $value));

			// Sinkronkan sanksi suspend pada tabel pelanggaran bila diubah ke selain Suspend (misal: Aktif)
			$pelanggaranDicabut = sinkronkan_status_suspend_anggota($id, $value);
		}

		$update_data_id = $this->anggotaModel->update($id, $update_data);

		$success = 'Field ' . $field . ' berhasil disimpan';
		$failed  = 'Field ' . $field . ' gagal disimpan. Silakan coba lagi';

		if ($field === 'StatusAnggota_id') {
			$member = $this->anggotaModel->find($id);
			$status = db_connect()->table('status_anggota')->where('id', $value)->get()->getRow();
			$nama   = esc($member->Fullname ?? 'Anggota');

			if (isset($update_data['MemberNo'])) {
				$success = $nama . ' sudah disetujui dan aktif sebagai anggota.<br>No. Anggota: <b>' . esc($update_data['MemberNo']) . '</b>';
			} else {
				$statusLabel = esc($status->Nama ?? '-');
				if ($pelanggaranDicabut > 0 && (int)$value === 3) {
					$success = 'Status <b>' . $nama . '</b> sekarang <b>' . $statusLabel . '</b>. Sisa sanksi suspend telah dicabut dan disinkronkan.';
				} else {
					$success = 'Status <b>' . $nama . '</b> sekarang <b>' . $statusLabel . '</b>.';
				}
			}
			$failed = 'Status ' . $nama . ' gagal diubah. Silakan coba lagi.';
		}

		if ($update_data_id) {
			$response = [
				'error' => false,
				'message' => $success,
			];

			if (isset($update_data['MemberNo'])) {
				$email = (new \App\Libraries\EmailPendaftaran())->kirim(\App\Libraries\EmailPendaftaran::DISETUJUI, $id);
				$response['warning']  = !$email['terkirim'];
				$response['message'] .= '<br>' . $this->pesanEmail($email, 'Kartu anggota dikirim ke ' . esc($member->Email) . '.');
			}
		} else {
			$response = [
				'error' => true,
				'message' => $failed,
			];
		}
		return $this->simpleResponse($response);
	}
}