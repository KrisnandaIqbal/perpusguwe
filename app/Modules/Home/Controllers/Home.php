<?php

namespace Home\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Home extends \Base\Controllers\BaseController
{
    // Kartu melayang di hero hanya tampil bila ada pembaruan dalam rentang ini (hari).
    private const KOLEKSI_BARU_HARI = 30;
    private const ANGGOTA_BARU_HARI = 14;
    // Calon saran pencarian di hero; yang tidak punya hasil di katalog tidak ditampilkan (lihat getSearchChips).
    private const SARAN_PENCARIAN = ['K3', 'Hukum ketenagakerjaan', 'Statistik', 'Pelatihan kerja', 'Upah'];

    public $auth;
    public $authorize;
    public $katalogModel;
    public $visitorModel;
    public $eksemplarModel;
    public $beritaModel;
    public $settingModel;
    public $db;
    public $bannerModel;

    function __construct()
    {
        $this->visitorModel = new \Opac\Models\VisitorModel();
        $this->bannerModel = new \Banner\Models\BannerModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
        $this->settingModel = new \PenomoranKoleksi\Models\PenomoranKoleksiModel();
        $this->beritaModel = new \Berita\Models\BeritaModel();
        $this->db = \Config\Database::connect('data');
        helper(['reference', 'landing', 'opac']);
    }

    public function index()
    {
        $this->data['title'] = 'Beranda';
        $this->data['loader_splash'] = true;
        $this->data['library_name'] = landing_setting('NamaPerpustakaan', 'Perpustakaan Kemnaker RI');

        $useCache = env('is_home_cache') == 1;
        $cacheKey = 'home_index_data';

        if ($useCache && $cached = cache($cacheKey)) {
            $this->data = array_merge($this->data, $cached);
        } else {
            $pageData = [
                'featured_books' => $this->getFeaturedBooks(),
                'popular_books'  => $this->getPopularBooks(),
                'total_catalogs' => $this->getTotalCatalogs(),
                'subjects'       => $this->getTopSubjects(),
                'search_chips'   => $this->getSearchChips(),
                'statistics'     => $this->getLibraryStatistics(),
                'new_members'    => $this->getNewMembers(),
                'new_members_days' => self::ANGGOTA_BARU_HARI,
                'new_catalog_since' => date('Y-m-d H:i:s', strtotime('-' . self::KOLEKSI_BARU_HARI . ' days')),
                'news'           => $this->getNews(),
                'banners'        => $this->getBannersData(),
                'modules'        => $this->getLibraryModules(),
            ];

            if ($useCache) {
                cache()->save($cacheKey, $pageData, 300);
            }

            $this->data = array_merge($this->data, $pageData);
        }

        return view('Home\Views\index', $this->data);
    }

    /**
     * Get featured books from catalog
     */
    private function getFeaturedBooks()
    {
        try {
            $books = $this->db->table('catalogs')
                ->where('IsOPAC', 1)
                ->orderBy('CreateDate', 'DESC')
                ->limit(8)
                ->get()
                ->getResult();
            return enrichCatalogsWithCirculation($books);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Buku yang paling sering dipinjam, untuk tab "Populer".
     */
    private function getPopularBooks()
    {
        try {
            return $this->db->table('collectionloanitems')
                ->select('catalogs.ID, catalogs.Title, catalogs.Author, catalogs.Publisher, catalogs.Subject, catalogs.CoverURL, catalogs.PublishYear, COUNT(collectionloanitems.ID) AS Loans')
                ->join('collections', 'collections.ID = collectionloanitems.Collection_id')
                ->join('catalogs', 'catalogs.ID = collections.Catalog_id')
                ->where('catalogs.IsOPAC', 1)
                ->groupBy('catalogs.ID')
                ->orderBy('Loans', 'DESC')
                ->limit(4)
                ->get()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Subjek terbanyak (subjek pertama tiap katalog), untuk kisi subjek di beranda.
     */
    private function getTopSubjects()
    {
        try {
            return $this->db->table('catalogs')
                ->select("TRIM(SUBSTRING_INDEX(Subject, ';', 1)) AS Nama, COUNT(*) AS Jumlah", false)
                ->where('IsOPAC', 1)
                ->where("Subject <> ''", null, false)
                ->groupBy('Nama')
                ->having("Nama <> ''", null, false)
                ->orderBy('Jumlah', 'DESC')
                ->limit(8)
                ->get()
                ->getResult();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getTotalCatalogs()
    {
        try {
            return $this->db->table('catalogs')->where('IsOPAC', 1)->countAllResults();
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get library statistics
     */
    private function getLibraryStatistics()
    {
        try {
            $totalBooks = $this->eksemplarModel->countAllResults();

            // You can add more statistics from other tables
            return [
                'total_books' => $totalBooks,
                'total_members' => $this->getTotalMembers(),
                'books_borrowed' => $this->getBorrowedBooks(),
                'visitors_today' => $this->getTodayVisitors()
            ];
        } catch (\Exception $e) {
            return [
                'total_books' => 0,
                'total_members' => 0,
                'books_borrowed' => 0,
                'visitors_today' => 0
            ];
        }
    }

    /**
     * Get total members (if members table exists)
     */
    private function getTotalMembers()
    {
        try {
            if ($this->db->tableExists('members')) {
                return $this->db->table('members')->countAllResults();
            }
            return 1250; // Dummy data
        } catch (\Exception $e) {
            return 1250; // Dummy data
        }
    }

    /**
     * Jumlah anggota yang mendaftar dalam ANGGOTA_BARU_HARI hari terakhir. Dipakai kartu "anggota baru" di hero:
     * kartu hanya tampil bila ada pembaruan. Gagal membaca berarti 0 (kartu tidak tampil), bukan angka karangan.
     */
    private function getNewMembers(): int
    {
        try {
            return (int) $this->db->table('members')
                ->where('CreateDate >=', date('Y-m-d H:i:s', strtotime('-' . self::ANGGOTA_BARU_HARI . ' days')))
                ->countAllResults();
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Get borrowed books count
     */
    private function getBorrowedBooks()
    {
        try {
            if ($this->db->tableExists('collectionloanitems')) {
                return $this->db->table('collectionloanitems')
                    ->where('LoanStatus', 'Loan')
                    ->countAllResults();
            }
            return 0; // Dummy data
        } catch (\Exception $e) {
            return 0; // Dummy data
        }
    }

    /**
     * Get today's visitors count
     */
    private function getTodayVisitors()
    {
        try {
        $today = date('Y-m-d');
        $builderAnggota = $this->db->table('memberguesses');
        $builderAnggota->where('DATE(CreateDate)', $today);
		 $totalAnggota = $builderAnggota->countAllResults();
		$builderRombongan = $this->db->table('groupguesses');
        $builderRombongan->selectSum('CountPersonel', 'total_personel');
        $builderRombongan->where('DATE(CreateDate)', $today);
		$resultRombongan = $builderRombongan->get()->getRow();
        $totalRombongan = (int)($resultRombongan->total_personel ?? 0);
		$totalKunjungan = $totalAnggota + $totalRombongan;
            return $totalKunjungan;
        } catch (\Exception $e) {
            return 23; // Dummy data
        }
    }

    /**
     * Get news/announcements (dummy data)
     */
  private function getNews()
{
    try {
        return $this->beritaModel
            ->asArray()
            ->select('t_berita.*, users.username')         
            ->join('users', 'users.id = t_berita.created_by', 'left')          
            ->where('t_berita.active', 1)
            ->orderBy('t_berita.created_at', 'DESC')
            ->findAll(4);
            
    } catch (\Exception $e) {
        return []; 
    }
}

    /**
     * Get banner data (dummy data)
     */


    private function getBannersData()
    {
        try {
            return $this->bannerModel->where('active', 1)->where('category', 'Beranda')->orderBy('sort', 'ASC')->findAll();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get library modules
     */
    private function getLibraryModules()
    {
        return [
            [
                'name' => 'OPAC',
                'description' => 'Cari dan jelajahi koleksi perpustakaan',
                'icon' => 'fas fa-search',
                'img' => base_url('assets/img/opac1.png'),
                'link' => base_url('opac'),
                'color' => 'primary'
            ],
            [
                'name' => 'Buku Tamu',
                'description' => 'Registrasi kunjungan perpustakaan',
                'img' => base_url('assets/img/bukutamu.png'),
                'icon' => 'fas fa-book-open',
                'link' => base_url('buku-tamu'),
                'color' => 'success'
            ],
            // Sembunyikan kartu Baca di Tempat dari halaman depan (fitur/route tetap aktif jika diakses langsung)
            // [
            //     'name' => 'Baca di Tempat',
            //     'description' => 'Layanan membaca di perpustakaan',
            //     'img' => base_url('assets/img/bacaditempat.png'),
            //     'icon' => 'fas fa-chair',
            //     'link' => base_url('baca-ditempat'),
            //     'color' => 'info'
            // ],
            ...(env('Is_keanggotaan_online') == 1 ? [[
                'name' => 'Keanggotaan Online',
                'description' => 'Daftar menjadi anggota perpustakaan',
                'img' => base_url('assets/img/anggota.png'),
                'icon' => 'fas fa-user-plus',
                'link' => base_url('home/pendaftaran_online'),
                'color' => 'warning'
            ]] : []),
            [
                'name' => 'Peminjaman Mandiri',
                'description' => 'Pinjam buku secara mandiri',
                'img' => base_url('assets/img/peminjaman.png'),
                'icon' => 'fas fa-hand-holding-heart',
                'link' => base_url('peminjaman-mandiri'),
                'color' => 'danger'
            ],
            [
                'name' => 'Pengembalian Mandiri',
                'description' => 'Kembalikan buku secara mandiri',
                'img' => base_url('assets/img/pengembalian.png'),
                'icon' => 'fas fa-undo',
                'link' => base_url('pengembalian-mandiri'),
                'color' => 'secondary'
            ]
        ];
    }

    /**
     * Syarat pencarian beranda: kata kunci di judul, pengarang, atau penerbit; singkatan regulasi
     * (uu, permenaker, "13 2003") juga dicocokkan lewat kata-kata judul.
     */
    private function filterBooks(string $keyword): void
    {
        $this->katalogModel->where('IsOPAC', 1)
            ->groupStart()
            ->like('Title', $keyword)
            ->orLike('Author', $keyword)
            ->orLike('Publisher', $keyword);

        if (($diperluas = landing_query_expand($keyword)) !== null) {
            $this->katalogModel->orGroupStart();
            foreach (explode(' ', $diperluas) as $kata) {
                $this->katalogModel->like('Title', $kata);
            }
            $this->katalogModel->groupEnd();
        }

        $this->katalogModel->groupEnd();
    }

    /** Topik saran di bawah kolom pencarian; hanya yang punya hasil, supaya saran tidak berujung "0 hasil". */
    private function getSearchChips(): array
    {
        $chips = [];
        foreach (self::SARAN_PENCARIAN as $topik) {
            try {
                $this->filterBooks($topik);
                if ($this->katalogModel->countAllResults() > 0) {
                    $chips[] = $topik;
                }
            } catch (\Throwable $e) {
                log_message('error', 'getSearchChips gagal: ' . $e->getMessage());
                return [];
            }
        }

        return $chips;
    }

    /**
     * Search books (for AJAX)
     */
    public function searchBooks()
    {
        $keyword = $this->request->getGet('q');

        if (!$keyword) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Keyword tidak boleh kosong'
            ]);
        }

        try {
            $this->filterBooks($keyword);
            $books = $this->katalogModel
                ->select('ID, Title, Author, Publisher, ControlNumber, PublishYear')
                ->limit(4)
                ->findAll();

            $this->filterBooks($keyword);

            return $this->response->setJSON([
                'status' => 'success',
                'total' => $this->katalogModel->countAllResults(),
                'data' => $books
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mencari buku'
            ]);
        }
    }

    public function pendaftaran_online()
    {
        if (env('Is_keanggotaan_online') != 1) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $this->data['title'] = 'Pendaftaran Online';
        return view('Home\Views\pendaftaran-online', $this->data);
    }

 public function tata_cara_sirkulasi()
    {
        $this->data['title'] = 'Tata Cara Sirkulasi';
        $this->data['aturanPinjam'] = $this->db->table('jenis_anggota')
            ->select('jenisanggota, MaxPinjamKoleksi, MaxLoanDays')
            ->where('active', 1)
            ->where('MaxLoanDays >', 0)
            ->orderBy('jenisanggota', 'ASC')
            ->get()
            ->getResult();

        return view('Home\Views\tata-cara-sirkulasi', $this->data);
    }


    public function layanan_isbn()
    {
        $this->data['title'] = 'Layanan ISBN';
        return view('Home\Views\layanan-isbn', $this->data);
    }
}
