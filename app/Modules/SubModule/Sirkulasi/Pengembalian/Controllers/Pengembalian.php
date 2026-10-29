<?php

namespace Pengembalian\Controllers;

use \CodeIgniter\Files\File;

class Pengembalian extends \Base\Controllers\BaseController
{
	public $auth;
	public $authorize;
	public $pengembalianModel;
	public $collectionModel;
	public $uploadPath;
	public $modulePath;
	public $collectionLoanModel;
	public $collectionLoanItemModel;
	public $cart;
	public $db;

	function __construct()
	{
		$this->pengembalianModel = new \Pengembalian\Models\PengembalianModel();
		$this->collectionModel = new \Peminjaman\Models\CollectionModel();
		$this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
		$this->collectionLoanItemModel = new \Peminjaman\Models\CollectionLoanItemModel();
		$this->cart = new \App\Libraries\Cart();
		$this->db = db_connect();

		$this->uploadPath = ROOTPATH . 'public/uploads/';
		$this->modulePath = ROOTPATH . 'public/uploads/pengembalian/';

		if (!file_exists($this->uploadPath)) {
			mkdir($this->uploadPath);
		}

		if (!file_exists($this->modulePath)) {
			mkdir($this->modulePath);
		}
		$this->cart = new \App\Libraries\Cart();



		helper('reference');
		helper('sirkulasi');
		helper('peminjaman');
		helper('pengembalian');
	}
	public function index()
	{
		$carts = get_cart_return();
		$this->data['carts'] = $carts;

		$this->data['title'] = 'Pengembalian';
		echo view('Pengembalian\Views\list', $this->data);
	}

	public function create()
	{
		// Get barcode parameter from URL
		$this->data['title'] = 'Pengembalian Mandiri';
		$nomorBarcode = $this->request->getGet('NomorBarcode');

		// Initialize data
		$this->data['nomorBarcode'] = $nomorBarcode;


		return view('Pengembalian\Views\add', $this->data);
	}

	private function parseInputDate($dateStr): \DateTime
	{
		if (empty($dateStr)) {
			return new \DateTime();
		}
		$dateStr = trim($dateStr);
		// Format DD/MM/YYYY atau DD-MM-YYYY
		if (preg_match('/^(\d{1,2})[\/\.-](\d{1,2})[\/\.-](\d{4})/', $dateStr, $m)) {
			return new \DateTime(sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]));
		}
		// Format YYYY-MM-DD
		if (preg_match('/^(\d{4})[\/\.-](\d{1,2})[\/\.-](\d{1,2})/', $dateStr, $m)) {
			return new \DateTime(sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3]));
		}
		try {
			return new \DateTime($dateStr);
		} catch (\Throwable $e) {
			return new \DateTime();
		}
	}

	public function checkBook()
    {
        $nomorBarcode = $this->request->getPost('nomorBarcode');
        
        if (empty($nomorBarcode)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Nomor barcode tidak boleh kosong']);
        }
        
        // 1. Cari buku yang discan (JOIN dengan tabel members untuk ambil Fullname)
        $scannedItem = $this->db->table('collections c')
            ->select('cli.CollectionLoan_id, cli.member_id, m.Fullname')
            ->join('collectionloanitems cli', 'cli.Collection_id = c.ID AND cli.LoanStatus = "Loan" AND cli.ActualReturn IS NULL', 'inner')
            ->join('members m', 'm.ID = cli.member_id', 'left') // Join tabel member
            ->where('c.NomorBarcode', $nomorBarcode)
            ->get()->getRow();
            
        if (!$scannedItem) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Buku dengan barcode tersebut tidak ditemukan atau sudah dikembalikan']);
        }

        // 2. Ambil SEMUA buku yang ada dalam transaksi yang sama
        $allItems = $this->db->table('collectionloanitems cli')
            ->select('cli.*, c.NomorBarcode, cat.Title, cat.Author')
            ->join('collections c', 'c.ID = cli.Collection_id')
            ->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
            ->where('cli.CollectionLoan_id', $scannedItem->CollectionLoan_id)
            ->where('cli.LoanStatus', 'Loan')
            ->where('cli.ActualReturn IS NULL')
            ->get()->getResult();

        $actualReturnDate = $this->parseInputDate($this->request->getPost('returnDate'));
        $today = $actualReturnDate;
        $formattedItems = [];

        foreach ($allItems as $item) {
            $daysOverdue = hitungHariKerjaTerlambat($item->DueDate, $actualReturnDate);
            $isOverdue   = ($daysOverdue > 0);
            
            $formattedItems[] = [
                'id' => $item->ID, // Primary key collectionloanitems
                'title' => $item->Title,
                'author' => $item->Author,
                'barcode' => $item->NomorBarcode,
                'loan_date' => $item->LoanDate,
                'due_date' => $item->DueDate,
                'is_overdue' => $isOverdue,
                'days_overdue' => $daysOverdue
            ];
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'member_id' => $scannedItem->member_id,
                'member_name' => $scannedItem->Fullname, // Kirim nama member
                'collection_loan_id' => $scannedItem->CollectionLoan_id,
                'items' => $formattedItems
            ]
        ]);
    }
// Tambahkan di dalam class Pengembalian

    public function getJenisPelanggaran()
    {
        $data = $this->db->table('jenis_pelanggaran')
            ->select('ID, JenisPelanggaran')
            ->where('active', 1)
            ->get()->getResult();

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    public function getJenisDenda()
    {
        $data = $this->db->table('jenis_denda')
            ->select('ID, Name')
            ->where('active', 1)
            ->get()->getResult();

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }
    public function processReturn()
{
    try {
        $itemIdsJson   = $this->request->getPost('item_ids');
        $processVio    = $this->request->getPost('process_violation'); // '1' atau '0'
        $vioDataJson   = $this->request->getPost('violation_data');    // JSON object
        $inputReturnDate = $this->request->getPost('returnDate');

        if (empty($itemIdsJson)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada buku yang dipilih untuk dikembalikan']);
        }

        $itemIds = json_decode($itemIdsJson, true);
        if (!is_array($itemIds) || count($itemIds) === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Format data tidak valid']);
        }

        $violationData = [];
        if ($processVio === '1' && !empty($vioDataJson)) {
            $violationData = json_decode($vioDataJson, true);
        }

        $this->db->transStart();

        $loanItems = $this->db->table('collectionloanitems')
            ->whereIn('ID', $itemIds)
            ->where('LoanStatus', 'Loan')
            ->where('ActualReturn IS NULL')
            ->get()->getResult();

        if (count($loanItems) === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Buku sudah dikembalikan atau tidak ditemukan']);
        }

        $actualReturnDate = $this->parseInputDate($inputReturnDate);
        $returnDate       = $actualReturnDate->format('Y-m-d') . ' ' . date('H:i:s');
        $returnedCount    = 0;
        $violationCount   = 0;
        $totalDenda       = 0;
        $collectionLoanId = null;
        $memberId         = null;

        foreach ($loanItems as $item) {
            if (!$collectionLoanId) $collectionLoanId = $item->CollectionLoan_id;
            if (!$memberId)         $memberId         = $item->member_id;

            $lateDays          = hitungHariKerjaTerlambat($item->DueDate, $actualReturnDate);
            $isOverdue         = ($lateDays > 0);

            // Update loan item
            $this->db->table('collectionloanitems')
                ->where('ID', $item->ID)
                ->update([
                    'ActualReturn'   => $returnDate,
                    'LateDays'       => $lateDays,
                    'LoanStatus'     => 'Return',
                    'UpdateBy'       => user_id() ?: 1,
                    'UpdateDate'     => $returnDate,
                    'UpdateTerminal' => $this->request->getIPAddress()
                ]);

            // Update status koleksi ke Available
            $this->db->table('collections')
                ->where('ID', $item->Collection_id)
                ->update([
                    'Status_id'      => 1,
                    'UpdateBy'       => user_id() ?: 1,
                    'UpdateDate'     => $returnDate,
                    'UpdateTerminal' => $this->request->getIPAddress()
                ]);

            // Catat pelanggaran jika buku terlambat (Aturan baru: terlambat N hari, suspend N hari, dihitung dari tanggal pengembalian. Tanpa denda uang.)
            if ($isOverdue) {
                $this->db->table('pelanggaran')->insert([
                    'CollectionLoan_id'     => $item->CollectionLoan_id,
                    'CollectionLoanItem_id' => $item->ID,
                    'JenisPelanggaran_id'   => !empty($violationData['jenis_pelanggaran_id']) ? $violationData['jenis_pelanggaran_id'] : 1,
                    'JenisDenda_id'         => 5, // 5 = Suspend (Skorsing)
                    'JumlahDenda'           => 0, // Tanpa denda uang
                    'JumlahSuspend'         => $lateDays, // Otomatis dari hari keterlambatan
                    'Paid'                  => 1,
                    'Member_id'             => $item->member_id,
                    'Collection_id'         => $item->Collection_id,
                    'Branch_id'             => null,
                    'active'                => 1,
                    'CreateBy'              => user_id() ?: 1,
                    'CreateDate'            => $returnDate,
                    'CreateTerminal'        => $this->request->getIPAddress()
                ]);

                $violationCount++;
            }

            $returnedCount++;
        }

        if ($returnedCount > 0 && $collectionLoanId) {
            $this->db->table('collectionloans')
                ->where('ID', $collectionLoanId)
                ->set('ReturnCount', 'ReturnCount + ' . $returnedCount, false)
                ->set('UpdateBy',       user_id() ?: 1)
                ->set('UpdateDate',     $returnDate)
                ->set('UpdateTerminal', $this->request->getIPAddress())
                ->update();
        }

        if ($violationCount > 0 && $memberId) {
            // Update status anggota menjadi Suspend (ID: 5)
            $this->db->table('members')
                ->where('ID', $memberId)
                ->update([
                    'StatusAnggota_id' => 5, // 5 = Suspend
                    'UpdateBy'         => user_id() ?: 1,
                    'UpdateDate'       => $returnDate,
                    'UpdateTerminal'   => $this->request->getIPAddress()
                ]);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan saat memproses database']);
        }

        $memberEmail = '';
        if ($memberId) {
            $memberObj = $this->db->table('members')->where('ID', $memberId)->get()->getRow();
            if ($memberObj) {
                $memberEmail = $memberObj->Email;
            }
        }

        $msg = "<b>$returnedCount buku</b> berhasil dikembalikan!";
        if ($violationCount > 0) {
            $msg .= "<br><small>&#9888; $violationCount pelanggaran keterlambatan telah dicatat.</small>";
        }

        // Struk dikirim otomatis setelah commit; gagal kirim tidak membatalkan pengembalian
        $emailStatus = $memberId ? $this->kirimStruk((int) $memberId, $itemIds) : ['success' => false, 'message' => 'Data anggota tidak ditemukan.'];

        // Store minimal struk data in session for the success page
        session()->set('struk_pengembalian', [
            'item_ids'        => $itemIds,
            'violation_count' => $violationCount,
            'total_denda'     => $totalDenda,
            'return_date'     => $returnDate,
            'email_status'    => $emailStatus,
        ]);

        return $this->response->setJSON([
            'status'    => 'success',
            'message'   => $msg,
            'struk_url' => base_url('sirkulasi-pengembalian/success'),
            'data'      => [
                'member_id' => $memberId, 
                'member_email' => $memberEmail,
                'returned_count' => $returnedCount, 
                'violation_count' => $violationCount,
                'item_ids' => $itemIds
            ]
        ]);

    } catch (\Exception $e) {
        return $this->response->setJSON(['status' => 'error', 'message' => 'System Error: ' . $e->getMessage()]);
    }
}


    public function sendStruk(): \CodeIgniter\HTTP\ResponseInterface
    {
        $itemIds  = json_decode($this->request->getPost('item_ids') ?? '[]', true);
        $memberId = (int) $this->request->getPost('member_id');

        if (empty($itemIds) || !$memberId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak lengkap.']);
        }

        return $this->response->setJSON($this->kirimStruk($memberId, $itemIds));
    }

    /**
     * Kirim struk pengembalian ke email anggota. Dipakai otomatis setelah pengembalian tersimpan
     * dan oleh tombol "Kirim Ulang Email". Tidak pernah melempar error.
     */
    private function kirimStruk(int $memberId, array $itemIds): array
    {
        try {
            $member = $this->db->table('members')->where('ID', $memberId)->get()->getRow();
            if (!$member) {
                return ['success' => false, 'message' => 'Data anggota tidak ditemukan.'];
            }

            if (empty($member->Email) || !filter_var($member->Email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Anggota tidak punya email, struk tidak dikirim.'];
            }

            $items = $this->db->table('collectionloanitems as cli')
                ->select('cli.ID, cli.DueDate, cli.ActualReturn, cli.LateDays, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
                ->join('collections as col', 'col.ID = cli.Collection_id')
                ->join('catalogs as cat', 'cat.ID = col.Catalog_id')
                ->whereIn('cli.ID', $itemIds)
                ->get()->getResult();

            return (new \App\Libraries\EmailNotificationLibrary())->sendStrukPengembalianEmail($member, $items);
        } catch (\Throwable $e) {
            log_message('error', '[EmailStruk] pengembalian anggota ' . $memberId . ': ' . $e->getMessage());
            return ['success' => false, 'message' => 'Email gagal dikirim.'];
        }
    }

    public function success()
    {
        $struk = session()->get('struk_pengembalian');
        if (empty($struk)) {
            return redirect()->to('sirkulasi-pengembalian/create');
        }
        // session()->remove('struk_pengembalian'); // Commented out so it can be printed multiple times

        $items = $this->db->table('collectionloanitems as cli')
            ->select('cli.ID, cli.DueDate, cli.ActualReturn, cli.LateDays, cli.CollectionLoan_id, cli.member_id, col.NomorBarcode, col.CallNumber, cat.Title, cat.Author')
            ->join('collections as col', 'col.ID = cli.Collection_id')
            ->join('catalogs as cat', 'cat.ID = col.Catalog_id')
            ->whereIn('cli.ID', $struk['item_ids'])
            ->get()->getResult();

        $member = null;
        if (!empty($items)) {
            $member = $this->db->table('members')
                ->where('ID', $items[0]->member_id)
                ->get()->getRow();
        }

        $this->data['title']           = 'Struk Pengembalian';
        $this->data['member']          = $member;
        $this->data['items']           = $items;
        $this->data['return_date']     = $struk['return_date'];
        $this->data['violation_count'] = $struk['violation_count'];
        $this->data['total_denda']     = $struk['total_denda'];
        $this->data['emailStatus']     = $struk['email_status'] ?? null;

        return view('Pengembalian\Views\success', $this->data);
    }

	public function getReturnHistory()
	{
		$limit = $this->request->getGet('limit') ?? 5;
		$offset = $this->request->getGet('offset') ?? 0;
		$member_id = $this->request->getGet('member_id'); // Menangkap member_id

		$query = $this->db->table('collectionloanitems cli')
			->select('cli.*, c.NomorBarcode, cat.Title, cat.Author, m.Fullname, m.MemberNo')
			->join('collections c', 'c.ID = cli.Collection_id')
			->join('catalogs cat', 'cat.ID = c.Catalog_id', 'left')
			->join('members m', 'm.ID = cli.member_id', 'left')
			->where('cli.LoanStatus', 'Return');

		// Filter history jika member_id tersedia
		if (!empty($member_id)) {
			$query->where('cli.member_id', $member_id);
		}

		$history = $query->orderBy('cli.ActualReturn', 'DESC')
			->limit($limit, $offset)
			->get()->getResult();

		foreach ($history as $h) {
			$loan = !empty($h->LoanDate) ? new \DateTime($h->LoanDate) : null;
			$ret  = !empty($h->ActualReturn) ? new \DateTime($h->ActualReturn) : new \DateTime();
			$h->duration_days = ($loan && $ret) ? $loan->diff($ret)->days : 0;
			$h->is_on_time = ((int)$h->LateDays <= 0);
		}

		return $this->response->setJSON([
			'status' => 'success',
			'data' => $history
		]);
	}
}
