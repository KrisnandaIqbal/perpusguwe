<?php
if (!function_exists('get_member')) {
    function get_member($member_id = null)
    {        
		$model = new \Anggota\Models\AnggotaModel();

		$data = $model->where('MemberNo',$member_id)->get()->getRow();
        return $data;
    }
}
if(!function_exists('get_member_type')){
    function get_member_type($jenisanggota_id = null){
        $jenisanggotaModel = new \JenisAnggota\Models\JenisAnggotaModel();
        $data = $jenisanggotaModel->where('id', $jenisanggota_id)->first();
        return $data;
    }
}

if (!function_exists('get_MemberNo')) {
    function get_MemberNo()
    {
        $baseModel = new \hamkamannan\adminigniter\Models\BaseModel();
        $baseModel->setTable('t_anggota');
        $kode = $baseModel
			->select ('RIGHT(MemberNo,4) as MemberNo', false)
			->orderBy('MemberNo','DESC')
			->limit(1)->get()->getRowArray();

        if (empty($kode['MemberNo'])){
            $no=1;
        }else{
            $no=intval($kode['MemberNo']) + 1; }
        $tgl= date('Ymd');
        $batas = str_pad($no, 4, "0", STR_PAD_LEFT);
        $MemberNo = $tgl.$batas;
        return $MemberNo;
    }
    
}

if (!function_exists('get_member_no')) {
    function get_member_no()
    {        
		$no = 1;
		$anggotaModel = new \Anggota\Models\AnggotaModel();
		$query = $anggotaModel
			->where('LENGTH(MemberNo) >=', 12, FALSE)
			->orderBy('id','desc')
			->limit(1);

		$data = $query->get()->getRow();

		if(!empty($data)){
			$no = intval(substr($data->MemberNo, -4, 4)) + 1;
		} 

		$today = date('Ymd');
        $increment = str_pad($no, 4, "0", STR_PAD_LEFT);
        $member_no = $today.$increment; 

        return $member_no;
    }
}

if (!function_exists('get_sumbangan')) {
    function get_sumbangan($member_id = null)
    {        
		$model = new \Sumbangan\Models\SumbanganModel();
        $query = $model
            ->select('sumbangan.*');

		if(!empty($member_id)){
			$query->where('Member_id',$member_id);
		}

		$data = $query->get()->getResult();
        return $data;
    }
}

if (!function_exists('get_perpanjangan')) {
    function get_perpanjangan($member_id = null)
    {        
		$model = new \PerpanjanganAnggota\Models\PerpanjanganAnggotaModel();
        $query = $model
            ->select('member_perpanjangan.*');

		if(!empty($member_id)){
			$query->where('Member_id',$member_id);
		}

		$data = $query->get()->getResult();
    //   dd($data);
        return $data;
    }
}

if (!function_exists('get_peminjaman')) {
    function get_peminjaman($member_id = null, $DBGroup='data')
    {
        $model = new \Peminjaman\Models\CollectionLoanItemModel();

        $query = $model
            ->select("
                collectionloanitems.*,
                collections.NomorBarcode AS barcode_no,
                catalogs.Title AS title,
                catalogs.Publisher AS publisher
            ")
            ->join('collections', 'collections.ID = collectionloanitems.Collection_id', 'left')
            ->join('catalogs', 'catalogs.ID = collections.Catalog_id', 'left');

        if (!empty($member_id)) {
            $query->where('collectionloanitems.member_id', $member_id);
        }

        $data = $query->findAll();
        return $data;
    }
}


if (!function_exists('get_pelanggaran')) {
    function get_pelanggaran($member_id = null, $DBGroup='data')
    {
        $model = new \Anggota\Models\PelanggaranModel();

        $query = $model
            ->select("
                pelanggaran.*,
                collections.NomorBarcode AS barcode_no,
                catalogs.Title AS title,
                catalogs.Publisher AS publisher,
                collectionloanitems.LoanDate AS loan_date,
                collectionloanitems.DueDate AS due_date
            ")
            ->join('collectionloanitems', 'collectionloanitems.ID = pelanggaran.CollectionLoanItem_id', 'left')
            ->join('collections', 'collections.ID = pelanggaran.Collection_id', 'left')
            ->join('catalogs', 'catalogs.ID = collections.Catalog_id', 'left');

        if (!empty($member_id)) {
            $query->where('pelanggaran.Member_id', $member_id);
        }

        return $query->findAll();
    }
}


if (!function_exists('tgl_indonesia')) {
function tgl_indonesia($tgl){ 
    $tanggal = substr($tgl,8,2);
    $nama_bulan = array("Januari", "Februari", "Maret", "April", "Mei", 
            "Juni", "Juli", "Agustus", "September", 
            "Oktober", "November", "Desember");
    $bulan = $nama_bulan[substr($tgl,5,2) - 1];
    $tahun = substr($tgl,0,4);
    return $tanggal.' '.$bulan.' '.$tahun;       
}
}

if ( ! function_exists('date_indo'))
{
    function date_indo($tgl)
    {
        $ubah = gmdate($tgl, time()+60*60*8);
        $pecah = explode("-",$ubah);
        $tanggal = $pecah[2];
        $bulan = bulan($pecah[1]);
        $tahun = $pecah[0];
        return $tanggal.' '.$bulan.' '.$tahun;
    }
}
  
if ( ! function_exists('bulan'))
{
    function bulan($bln)
    {
        switch ($bln)
        {
            case 1:
                return "Januari";
                break;
            case 2:
                return "Februari";
                break;
            case 3:
                return "Maret";
                break;
            case 4:
                return "April";
                break;
            case 5:
                return "Mei";
                break;
            case 6:
                return "Juni";
                break;
            case 7:
                return "Juli";
                break;
            case 8:
                return "Agustus";
                break;
            case 9:
                return "September";
                break;
            case 10:
                return "Oktober";
                break;
            case 11:
                return "November";
                break;
            case 12:
                return "Desember";
                break;
        }
    }
}


// Tambahkan function helper untuk generate member number
if (!function_exists('generateMemberNumber')) {
    function generateMemberNumber($identityNo = null) {
        $db = db_connect();
        
        // Ambil setting dari tabel settingparameters
        $setting = $db->table('settingparameters')
                     ->where('Name', 'TipePenomoranAnggota')
                     ->get()
                     ->getRow();
        
        if (!$setting) {
            // Default jika setting tidak ditemukan
            return $identityNo ?? date('YmdHis');
        }
        
        $tipeNomor = $setting->Value ?? '4'; // Default NIK jika Value kosong
        $memberNo = '';
        
        switch($tipeNomor) {
            case '1': // YYMMDD99999
                $memberNo = generateFormat1();
                break;
            case '2': // YYYYMM99
                $memberNo = generateFormat2();
                break;
            case '3': // 99999L2015
                $memberNo = generateFormat3();
                break;
            case '4': // NIK
            default:
                $memberNo = $identityNo ?? generateAutoNumber();
                break;
        }
        
        return $memberNo;
    }
}

if (!function_exists('generateFormat1')) {
    function generateFormat1() {
        // Format: YYMMDD99999
        $db = db_connect();
        $today = date('ymd'); // YY-MM-DD format
        
        // Cari nomor terakhir dengan prefix hari ini
        $lastMember = $db->table('members')
                        ->select('MemberNo')
                        ->like('MemberNo', $today, 'after')
                        ->where('LENGTH(MemberNo)', 11) // YYMMDD99999 = 11 karakter
                        ->orderBy('MemberNo', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRow();
        
        if ($lastMember) {
            // Ambil 5 digit terakhir dan tambah 1
            $lastNumber = (int)substr($lastMember->MemberNo, -5);
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }
        
        return $today . $newNumber;
    }
}

if (!function_exists('generateFormat2')) {
    function generateFormat2() {
        // Format: YYYYMM99
        $db = db_connect();
        $yearMonth = date('Ym'); // YYYY-MM format
        
        // Cari nomor terakhir dengan prefix bulan ini
        $lastMember = $db->table('members')
                        ->select('MemberNo')
                        ->like('MemberNo', $yearMonth, 'after')
                        ->where('LENGTH(MemberNo)', 8) // YYYYMM99 = 8 karakter
                        ->orderBy('MemberNo', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRow();
        
        if ($lastMember) {
            // Ambil 2 digit terakhir dan tambah 1
            $lastNumber = (int)substr($lastMember->MemberNo, -2);
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }
        
        return $yearMonth . $newNumber;
    }
}

if (!function_exists('generateFormat3')) {
    function generateFormat3() {
        // Format: 99999L2015
        $db = db_connect();
        $year = date('Y'); // Current year
        $suffix = 'L' . $year;
        
        // Cari nomor terakhir dengan suffix tahun ini
        $lastMember = $db->table('members')
                        ->select('MemberNo')
                        ->like('MemberNo', $suffix, 'before')
                        ->where('LENGTH(MemberNo)', 10) // 99999L2015 = 10 karakter
                        ->orderBy('MemberNo', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRow();
        
        if ($lastMember) {
            // Ambil 5 digit pertama dan tambah 1
            $lastNumber = (int)substr($lastMember->MemberNo, 0, 5);
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }
        
        return $newNumber . $suffix;
    }
}

if (!function_exists('generateAutoNumber')) {
    function generateAutoNumber() {
        // Auto increment number
        $db = db_connect();
        $lastMember = $db->table('members')
                        ->select('MemberNo')
                        // PERBAIKAN: Gabungkan kondisi REGEXP menjadi satu string
                        ->where("MemberNo REGEXP '^[0-9]+$'") 
                        ->orderBy('CAST(MemberNo AS UNSIGNED)', 'DESC')
                        ->limit(1)
                        ->get()
                        ->getRow();
        if ($lastMember) {
            $newNumber = (int)$lastMember->MemberNo + 1;
        } else {
            $newNumber = 1;
        }
        return str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generateUniversalMemberNumber')) {
    function generateUniversalMemberNumber() {
        // Format: YYMMDD99999, 5 digit terakhir berurutan terus (tidak diulang per hari)
        $db = db_connect();
        $db->transStart();

        // Counter disimpan supaya nomor tidak terpakai ulang walau anggota terakhir dihapus
        $counter = $db->query("SELECT ID, Value FROM settingparameters WHERE Name = 'NomorAnggotaCounter' FOR UPDATE")->getRow();
        $lastMember = $db->query("SELECT MAX(CAST(RIGHT(MemberNo, 5) AS UNSIGNED)) AS last FROM members WHERE MemberNo REGEXP '^[0-9]{11}$'")->getRow();

        $newNumber = max((int) ($counter->Value ?? 0), (int) ($lastMember->last ?? 0)) + 1;

        if ($counter) {
            $db->table('settingparameters')->where('ID', $counter->ID)->update([
                'Value'      => $newNumber,
                'UpdateDate' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('settingparameters')->insert([
                'Name'       => 'NomorAnggotaCounter',
                'Value'      => $newNumber,
                'CreateDate' => date('Y-m-d H:i:s'),
            ]);
        }

        $db->transComplete();

        return date('ymd') . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('memberApprovalData')) {
    /**
     * Data tambahan saat anggota pendaftaran online (MemberNo PENDING-...) disetujui,
     * yaitu ketika statusnya diubah menjadi Aktif. Selain itu mengembalikan array kosong.
     * Masa berlaku (EndDate) dihitung satu tahun dari tanggal pendaftaran.
     */
    function memberApprovalData($memberId, $newStatusId) {
        $db = db_connect();

        $member = $db->table('members')->select('MemberNo, RegisterDate')->where('ID', $memberId)->get()->getRow();
        if (!$member || strpos((string) $member->MemberNo, 'PENDING-') !== 0) {
            return [];
        }

        $aktif = $db->table('status_anggota')->where('Nama', 'Aktif')->get()->getRow();
        if (!$aktif || (int) $newStatusId !== (int) $aktif->id) {
            return [];
        }

        $daftar = !empty($member->RegisterDate) ? strtotime($member->RegisterDate) : time();

        return [
            'MemberNo'       => generateUniversalMemberNumber(),
            'TglPersetujuan' => date('Y-m-d H:i:s'),
            'TglKeputusan'   => date('Y-m-d H:i:s'),
            'KeputusanBy'    => login_id(),
            'EndDate'        => date('Y-m-d 00:00:00', strtotime('+1 year', $daftar)),
        ];
    }
}

if (!function_exists('statusAnggotaId')) {
    function statusAnggotaId(string $nama): ?int {
        $row = db_connect()->table('status_anggota')->select('id')->where('Nama', $nama)->get()->getRow();
        return $row ? (int) $row->id : null;
    }
}

if (!function_exists('alasanPerbaikan')) {
    /**
     * Alasan pendaftaran belum bisa diterima (9 alasan resmi).
     * 'cara' dikirim ke email pendaftar.
     * 'daftar_ulang' false berarti pendaftar tidak diminta mengisi formulir lagi.
     */
    function alasanPerbaikan(bool $includeLegacy = false): array {
        $reasons = [
            'nik_salah' => [
                'label'        => 'Format NIK tidak valid',
                'cara'         => 'Nomor Induk Kependudukan (NIK) harus berupa 16 digit angka sesuai KTP resmi.',
                'daftar_ulang' => true,
            ],
            'nip_salah' => [
                'label'        => 'Format NIP tidak valid',
                'cara'         => 'Nomor Induk Pegawai (NIP) harus berupa 18 digit angka resmi BKN tanpa spasi atau tanda baca.',
                'daftar_ulang' => true,
            ],
            'nama_tidak_sesuai' => [
                'label'        => 'Nama lengkap tidak sesuai identitas',
                'cara'         => 'Masukkan nama lengkap sesuai dengan kartu identitas resmi yang diunggah.',
                'daftar_ulang' => true,
            ],
            'unit_kerja_tidak_sesuai' => [
                'label'        => 'Unit kerja tidak sesuai penempatan',
                'cara'         => 'Pilih unit kerja atau satuan kerja tempat Anda bertugas saat ini.',
                'daftar_ulang' => true,
            ],
            'alamat_belum_jelas' => [
                'label'        => 'Alamat domisili belum lengkap atau jelas',
                'cara'         => 'Lengkapi alamat domisili/tempat tinggal saat ini secara jelas.',
                'daftar_ulang' => true,
            ],
            'foto_tidak_sesuai' => [
                'label'        => 'Pas foto tidak sesuai ketentuan',
                'cara'         => 'Unggah pas foto formal berpakaian rapi dengan orientasi potret (rasio 2:3) dan wajah terlihat jelas.',
                'daftar_ulang' => true,
            ],
            'whatsapp_tidak_valid' => [
                'label'        => 'Nomor WhatsApp atau kontak tidak aktif',
                'cara'         => 'Pastikan nomor telepon/WhatsApp yang didaftarkan aktif dan dapat dihubungi.',
                'daftar_ulang' => true,
            ],
            'data_ganda' => [
                'label'        => 'Pendaftaran ganda / sudah terdaftar',
                'cara'         => 'Data Anda sudah tercatat sebelumnya pada sistem perpustakaan, sehingga tidak perlu mendaftar ulang.',
                'daftar_ulang' => false,
            ],
            'lainnya' => [
                'label'        => 'Lainnya',
                'cara'         => '',
                'daftar_ulang' => true,
            ],
        ];

        if ($includeLegacy) {
            $reasons['identitas']     = ['label' => 'Nomor identitas tidak valid', 'cara' => 'Periksa lagi nomor identitas Anda.', 'daftar_ulang' => true];
            $reasons['unit_kerja']    = ['label' => 'Bukan pegawai atau unit kerja tidak sesuai', 'cara' => 'Pilih unit kerja tempat Anda bertugas.', 'daftar_ulang' => true];
            $reasons['jenis_anggota'] = ['label' => 'Jenis anggota tidak sesuai', 'cara' => 'Pilih jenis anggota yang sesuai.', 'daftar_ulang' => true];
            $reasons['tidak_lengkap'] = ['label' => 'Data tidak lengkap', 'cara' => 'Isi kembali formulir pendaftaran dengan data lengkap.', 'daftar_ulang' => true];
            $reasons['ganda']         = ['label' => 'Pendaftaran ganda', 'cara' => 'Anda sudah tercatat dengan pendaftaran lain.', 'daftar_ulang' => false];
        }

        return $reasons;
    }
}

if (!function_exists('tanggalKartu')) {
    function tanggalKartu($date): string {
        $time = $date ? strtotime($date) : false;
        if (!$time || $time <= 0) {
            return '-';
        }

        $bulan = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];

        return date('j', $time) . ' ' . $bulan[(int) date('n', $time) - 1] . ' ' . date('Y', $time);
    }
}

if (!function_exists('kartuAnggotaData')) {
    /** Isi kartu anggota, dipakai halaman cetak dan lampiran PDF di email persetujuan. */
    function kartuAnggotaData(object $anggota): array {
        $memberNo = (string) $anggota->MemberNo;

        // No. Anggota YYMMDD99999: sisi depan menampilkan nomor urutnya saja (001, 002, ...)
        $isUniversal = (bool) preg_match('/^[0-9]{11}$/', $memberNo);

        // PhotoUrl bisa berisi beberapa file dipisah koma; kartu memakai yang pertama
        $fotoName = trim(explode(',', (string) $anggota->PhotoUrl)[0]);
        $fotoPath = $fotoName !== '' && is_file(FCPATH . 'uploads/anggota/' . $fotoName) ? FCPATH . 'uploads/anggota/' . $fotoName : null;
        $fotoUrl  = $fotoPath ? base_url('uploads/anggota/' . $fotoName) : null;

        return [
            'nama'        => $anggota->Fullname,
            'foto'        => $fotoUrl,
            'foto_path'   => $fotoPath,
            'member_no'   => $memberNo,
            'nomor_depan' => $isUniversal ? str_pad((string) (int) substr($memberNo, -5), 3, '0', STR_PAD_LEFT) : $memberNo,
            'nomor_urut'  => $isUniversal,
            'berlaku'     => tanggalKartu($anggota->EndDate),
            'qr'          => (new \chillerlan\QRCode\QRCode(new \chillerlan\QRCode\QROptions(['addQuietzone' => false])))->getMatrix($memberNo)->matrix(true),
        ];
    }
}



if (!function_exists('is_form_field_active')) {
    /**
     * Memeriksa apakah sebuah field pada form pendaftaran anggota aktif
     * berdasarkan jenis perpustakaan.
     *
     * @param string $field_name Nama field yang ingin diperiksa (e.g., 'Jenis Identitas').
     * @param int $jenis_perpustakaan_id ID dari jenis perpustakaan.
     * @return bool Mengembalikan true jika aktif, false jika tidak.
     */
    function is_form_field_active(string $field_id, int $jenis_perpustakaan_id): bool
    {
        $db = db_connect();
        $field_setting = $db->table('members_form as a')
            ->select('a.active')
            ->join('member_fields as b', 'b.id = a.Member_Field_id')
            ->where('a.Jenis_Perpustakaan_id', $jenis_perpustakaan_id)
            ->where('a.Member_Field_id', $field_id)
            ->get()->getRow();

        // Kembalikan true jika data ditemukan DAN statusnya aktif (1)
        if ($field_setting && $field_setting->active == 1) {
            return true;
        }

        return false;
    }
}

if (!function_exists('format_unit_kerja')) {
    /**
     * Memformat tampilan nama Unit Kerja dan Sub-Unit Kerja secara terpusat.
     * Sesuai aturan:
     * - Anggota lama / sub-unit kosong: tampil nama unit saja, atau '-' jika unit kosong.
     * - Jika unit dan sub-unit ada: "Nama Unit - Nama Sub-Unit".
     *
     * @param string|null $unitNama
     * @param string|null $subUnitNama
     * @return string
     */
    function format_unit_kerja(?string $unitNama, ?string $subUnitNama = null): string
    {
        $unit = trim((string) $unitNama);
        $sub  = trim((string) $subUnitNama);

        if ($unit === '' || $unit === '-') {
            return '-';
        }

        if ($sub === '' || $sub === '-') {
            return $unit;
        }

        return $unit . ' - ' . $sub;
    }
}

if (!function_exists('get_member_unit_display')) {
    /**
     * Mengambil dan memformat data unit kerja anggota dari satu fungsi/model bersama.
     * Menerima object/array anggota (bisa dari tabel members atau query JOIN).
     * Jika field string nama belum ada di query, fungsi ini mengambil dari database
     * dengan caching memori agar cepat dan tidak membebani database.
     *
     * Format pengembalian ($format):
     * - 'combined' (default): "Nama Unit - Nama Sub-Unit" (atau "-" jika kosong)
     * - 'unit'              : "Nama Unit" (atau "-")
     * - 'subunit'           : "Nama Sub-Unit" (atau "-")
     * - 'array'             : ['unit' => ..., 'subunit' => ..., 'full' => ...]
     *
     * @param mixed $member Object atau array data anggota
     * @param string $format 'combined' | 'unit' | 'subunit' | 'array'
     * @return string|array
     */
    function get_member_unit_display($member, string $format = 'combined')
    {
        static $unitCache = [];
        static $subUnitCache = [];

        if (empty($member)) {
            $empty = ['unit' => '-', 'subunit' => '-', 'full' => '-'];
            return $format === 'array' ? $empty : '-';
        }

        // Ekstraksi nilai string nama jika sudah di-JOIN
        $unitNama    = is_object($member) ? ($member->UnitKerja ?? null) : ($member['UnitKerja'] ?? null);
        $subUnitNama = is_object($member) ? ($member->SubUnitKerja ?? null) : ($member['SubUnitKerja'] ?? null);

        // Ekstraksi ID
        $unitId    = is_object($member) ? ($member->UnitKerja_id ?? null) : ($member['UnitKerja_id'] ?? null);
        $subUnitId = is_object($member) ? ($member->SubUnitKerja_id ?? null) : ($member['SubUnitKerja_id'] ?? null);

        $db = null;

        // Ambil nama unit jika belum ada di record
        if (empty($unitNama) && !empty($unitId)) {
            $unitId = (int) $unitId;
            if (!isset($unitCache[$unitId])) {
                $db = $db ?? db_connect();
                $u = $db->table('master_unit')->select('nama_unit')->where('id', $unitId)->get()->getRow();
                $unitCache[$unitId] = $u ? $u->nama_unit : null;
            }
            $unitNama = $unitCache[$unitId];
        }

        // Ambil nama sub-unit jika belum ada di record
        if (empty($subUnitNama) && !empty($subUnitId)) {
            $subUnitId = (int) $subUnitId;
            if (!isset($subUnitCache[$subUnitId])) {
                $db = $db ?? db_connect();
                $su = $db->table('master_subunit')->select('nama_subunit')->where('id', $subUnitId)->get()->getRow();
                $subUnitCache[$subUnitId] = $su ? $su->nama_subunit : null;
            }
            $subUnitNama = $subUnitCache[$subUnitId];
        }

        $unitClean = trim((string) $unitNama);
        if ($unitClean === '') $unitClean = '-';

        $subClean = trim((string) $subUnitNama);
        if ($subClean === '') $subClean = '-';

        $full = format_unit_kerja($unitClean !== '-' ? $unitClean : null, $subClean !== '-' ? $subClean : null);

        if ($format === 'unit') {
            return $unitClean;
        }
        if ($format === 'subunit') {
            return $subClean;
        }
        if ($format === 'array') {
            return [
                'unit'    => $unitClean,
                'subunit' => $subClean,
                'full'    => $full
            ];
        }

        return $full;
    }
}

if (!function_exists('is_valid_unit_subunit')) {
    /**
     * Memvalidasi di server apakah pilihan unit dan sub-unit sah.
     * Aturan:
     * - Unit belum dipilih / 0: tidak valid
     * - Unit yang punya sub-unit (unit 2-8): sub-unit wajib diisi, aktif, dan milik unit itu
     * - Unit tanpa sub-unit (unit 1): sub-unit boleh kosong / null
     *
     * @param int|null $unitId
     * @param int|null $subUnitId
     * @return array{valid: bool, message: string}
     */
    function is_valid_unit_subunit($unitId, $subUnitId = null): array
    {
        $unitId = (int) $unitId;
        if ($unitId <= 0) {
            return ['valid' => false, 'message' => 'Unit kerja wajib dipilih.'];
        }

        $db = db_connect();
        $unit = $db->table('master_unit')->where('id', $unitId)->where('active', 1)->get()->getRow();
        if (!$unit) {
            return ['valid' => false, 'message' => 'Unit kerja yang dipilih tidak terdaftar atau tidak aktif.'];
        }

        $model = new \App\Models\SubUnitModel();
        $hasSub = $model->unitHasSubUnits($unitId);

        if (!$hasSub) {
            // Unit 1 tidak punya sub-unit -> valid
            return ['valid' => true, 'message' => 'Valid'];
        }

        $subUnitId = (int) $subUnitId;
        if ($subUnitId <= 0) {
            return ['valid' => false, 'message' => 'Biro / Direktorat / Unit wajib dipilih.'];
        }

        if (!$model->isValidSubUnit($unitId, $subUnitId)) {
            return ['valid' => false, 'message' => 'Biro / Direktorat / Unit yang dipilih tidak sesuai dengan unit kerja penempatan.'];
        }

        return ['valid' => true, 'message' => 'Valid'];
    }
}


?>

