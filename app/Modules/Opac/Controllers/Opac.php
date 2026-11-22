<?php

namespace Opac\Controllers;

use \CodeIgniter\Files\File;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Opac extends \Base\Controllers\BaseController
{
    public $auth;
    public $authorize;
    public $visitorModel;
    public $katalogModel;
    public $fileModel;
    public $data = [];
    public $db;
    public $memberModel;
    public $collectionLoanModel;
    public $eksemplarModel;
    public $katalogRuasModel;
    public $bannerModel;

    /** Topik di rak "Jelajah per topik": kunci => [label chip, nilai ?category= yang dipakai filter katalog]. Urutan array = urutan chip. */
    private const TOPIK = [
        'kerja'     => ['Ketenagakerjaan', 'Ketenagakerjaan'],
        'manajemen' => ['Manajemen & Bisnis', 'Manajemen'],
        'diri'      => ['Pengembangan Diri', 'pengembangan_diri'],
        'komputer'  => ['Komputer & TI', 'Komputer'],
        'fiksi'     => ['Fiksi & Sastra', 'fiksi'],
        'sejarah'   => ['Sejarah & Biografi', 'sejarah'],
    ];


    function __construct()
    {
        $this->visitorModel = new \Opac\Models\VisitorModel();
        $this->katalogModel = new \Katalog\Models\KatalogModel();
        $this->bannerModel = new \Banner\Models\BannerModel();
        $this->db = \Config\Database::connect('data');
        $this->fileModel = new \Katalog\Models\FileModel();
        $this->memberModel = new \Anggota\Models\AnggotaModel();
        $this->collectionLoanModel = new \Peminjaman\Models\CollectionLoanModel();
        $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
        $this->katalogRuasModel = new \Katalog\Models\KatalogRuasModel();

        helper('opac');
        helper('sanitize');
    }


   public function index()
{
    $startTime = microtime(true);
    $this->data['title'] = 'OPAC - Online Public Access Catalog';

    if (env('is_opac_cache', 0) == 1) {
        $this->loadRegularCatalogscache();
    } else {
        $this->loadRegularCatalogs();
    }

    $this->data['opac_banners'] = $this->getOpacBanners();
    $this->data['search_log_id'] = $this->logSearch();
    $this->loadRujukanTanpaHasil();
    $this->data['mode_beranda'] = $this->berandaPolos();
    $this->data['baris_rekomendasi'] = $this->getBarisBeranda();
    $this->data['rak_beranda'] = $this->getBarisTamu();
    $this->data['topik_daftar'] = array_map(static fn ($t) => $t[0], self::TOPIK);
    $this->data['topik_aktif'] = $this->topikAktif();
    $this->data['topik_kategori'] = self::TOPIK[$this->topikAktif()][1];
    $this->data['jelajah'] = $this->getJelajah();
    $endTime = microtime(true);
    $this->data['execution_time'] = $endTime - $startTime;
    return view('Opac\Views\index', $this->data);
}

/**
 * Kejutan Buku Acak: Redirect ke halaman detail buku acak yang aktif.
 */
public function acak()
{
    $randomBook = $this->db->table('catalogs')
        ->select('ID')
        ->where('IsOPAC', 1)
        ->orderBy('RAND()')
        ->limit(1)
        ->get()
        ->getRow();

    if ($randomBook && !empty($randomBook->ID)) {
        return redirect()->to(base_url('opac/detail/' . $randomBook->ID));
    }

    return redirect()->to(base_url('opac'));
}

/**
 * Satu pencarian = satu baris opac_search_log. Halaman 2 dan seterusnya tidak dicatat ulang.
 * member_id terisi hanya kalau anggota login (session_member dari tiket L), selain itu NULL.
 * Gagal mencatat tidak boleh merusak halaman.
 */
private function logSearch(): ?int
{
    $query = trim((string) ($this->request->getVar('search') ?? ''));
    if ($query === '' || (int) ($this->request->getVar('page') ?? 1) > 1) {
        return null;
    }

    $searchBy = (string) ($this->request->getVar('search_by') ?? '');
    $allowed  = ['Title', 'Author', 'Subject', 'Publisher', 'ISBN', 'DeweyNo'];

    try {
        $this->db->table('opac_search_log')->insert([
            'member_id'    => session('session_member')['member_id'] ?? null,
            'query'        => mb_substr($query, 0, 255),
            'search_by'    => in_array($searchBy, $allowed, true) ? $searchBy : null,
            'result_count' => (int) ($this->data['total_records'] ?? 0),
        ]);
        return (int) $this->db->insertID();
    } catch (\Throwable $e) {
        log_message('error', 'opac_search_log gagal: ' . $e->getMessage());
        return null;
    }
}

/**
 * Pencarian kata kunci: URL OneSearch (hasil 0-4) dan koleksi lokal yang mirip (hasil 0).
 * Filter sisi kiri (Subject=, DeweyNo=, dst.) tidak dihitung; hanya kolom search.
 */
private function loadRujukanTanpaHasil(): void
{
    $query = trim((string) ($this->request->getVar('search') ?? ''));
    $total = (int) ($this->data['total_records'] ?? 0);
    if ($query === '' || $total >= 5) {
        return;
    }

    if ($total === 0) {
        (new \UsulanKoleksi\Models\UsulanKoleksiModel())->catatPencarian($query, 'pencarian');
    }

    $this->data['onesearch_url'] = \Rekomendasi\Libraries\OneSearch::url($query, (string) ($this->request->getVar('search_by') ?? ''));
    if ($total === 0) {
        try {
            $this->data['catalogs_mirip'] = $this->koleksiMirip($query);
        } catch (\Throwable $e) {
            log_message('error', 'koleksiMirip gagal: ' . $e->getMessage());
        }
    }
}

/**
 * Judul lokal yang mirip kata kunci: FULLTEXT NATURAL LANGUAGE (lebih longgar dari pencarian utama),
 * lalu diperluas ke judul sedang DDC 3 digit yang sama, diurutkan ContentScore terhadap hasil teratas.
 */
private function koleksiMirip(string $query, int $batas = 8): array
{
    $term = trim(preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $query));
    if ($term === '') {
        return [];
    }

    $kolom = 'ID AS id, IsOPAC, DeweyNo, Subject, Author';
    $match = 'MATCH(Title, Author, Subject, Publisher) AGAINST (' . $this->db->escape($term) . ' IN NATURAL LANGUAGE MODE)';
    $bibit = $this->db->table('catalogs')->select($kolom)->where('IsOPAC', 1)
        ->where($match, null, false)->orderBy($match, 'DESC', false)->limit(5)->get()->getResultArray();
    if (!$bibit) {
        return [];
    }

    $kandidat = array_column($bibit, null, 'id');
    $ddc = array_unique(array_filter(array_map(fn ($b) => substr(preg_replace('/[\s.]/', '', (string) $b['DeweyNo']), 0, 3), $bibit), fn ($d) => strlen($d) === 3));
    if ($ddc) {
        $q = $this->db->table('catalogs')->select($kolom)->where('IsOPAC', 1)->groupStart();
        foreach ($ddc as $d) {
            $q->orLike('DeweyNo', $d, 'after');
        }
        $q->groupEnd();
        // shortcut: 200 judul pertama per DDC tanpa urutan relevansi, upgrade kalau koleksi besar membuat hasil kurang tajam.
        foreach ($q->limit(200)->get()->getResultArray() as $k) {
            $kandidat[$k['id']] ??= $k;
        }
    }

    foreach ($kandidat as &$k) {
        $k['content'] = max(array_map(fn ($b) => \Rekomendasi\Libraries\ContentScore::score($b, $k), $bibit));
    }
    unset($k);

    $urut = array_column(\Rekomendasi\Libraries\Ranker::baris($kandidat, \Rekomendasi\Libraries\Ranker::BOBOT_DETAIL, [], false, $batas), 'id');
    if (!$urut) {
        return [];
    }

    $baris = array_column($this->katalogModel->whereIn('ID', $urut)->findAll(), null, 'ID');
    return enrichCatalogsWithCirculation(array_values(array_filter(array_map(fn ($id) => $baris[$id] ?? null, $urut))));
}

// Beranda polos (tanpa pencarian, filter, huruf abjad, atau halaman > 1).
private function berandaPolos(): bool
{
    foreach (['search', 'search_by', 'Author', 'Publisher', 'ISBN', 'Subject', 'PublishYear', 'PublishLocation', 'DeweyNo', 'format', 'language', 'year_start', 'year_end'] as $p) {
        if (trim((string) ($this->request->getVar($p) ?? '')) !== '') {
            return false;
        }
    }

    // ?semua=1 = tombol "Lihat semua": daftar katalog penuh tanpa rak.
    if ($this->request->getVar('semua') !== null) {
        return false;
    }

    $letter = trim((string) ($this->request->getVar('letter') ?? ''));
    if ($letter !== '' && preg_match('/^[A-Za-z0-9]$/', $letter)) {
        return false;
    }

    if ((int) ($this->request->getVar('page') ?? 1) > 1) {
        return false;
    }

    $category = trim((string) ($this->request->getVar('category') ?? ''));
    if ($category !== '' && $category !== 'semua') {
        return false;
    }

    $availability = trim((string) ($this->request->getVar('availability') ?? ''));
    if ($availability !== '' && $availability !== 'semua') {
        return false;
    }

    $sort = trim((string) ($this->request->getVar('sort') ?? ''));
    if ($sort !== '' && $sort !== 'terbaru') {
        return false;
    }

    if ((int) ($this->request->getVar('year_from') ?? 0) > 0
        || (int) ($this->request->getVar('year_to') ?? 0) > 0
        || (int) ($this->request->getVar('only_available') ?? 0) === 1) {
        return false;
    }

    $advQueries = $this->request->getVar('adv_query');
    if (is_array($advQueries)) {
        foreach ($advQueries as $q) {
            if (trim((string) $q) !== '') {
                return false;
            }
        }
    }

    return true;
}

private function topikAktif(): string
{
    $kunci = (string) ($this->request->getVar('topik') ?? '');
    return isset(self::TOPIK[$kunci]) ? $kunci : array_key_first(self::TOPIK);
}

/** Buku satu topik, terbaru dulu; 'populer' diisi dari $petaPopuler supaya yang sering dipinjam naik. */
private function kandidatTopik(string $topik, array $petaPopuler): array
{
    $builder = $this->db->table('catalogs')->select('catalogs.*, catalogs.ID AS id')->where('catalogs.IsOPAC', 1);
    $this->terapkanKategori($builder, self::TOPIK[$topik][1]);
    $rows = $builder->orderBy('catalogs.ID', 'DESC')->limit(200)->get()->getResultArray();

    foreach ($rows as &$r) {
        $r['populer'] = $petaPopuler[$r['id']] ?? 0;
    }
    unset($r);
    return $rows;
}

/**
 * Rak beranda untuk semua pengunjung, urut dari atas: topik, populer, baru (judul tidak muncul di dua rak; yang atas menang).
 * Kunci: topik, populer, baru. Rak dengan kurang dari 4 judul tidak ikut. Hanya di beranda polos (tanpa pencarian, filter, atau halaman lain).
 */
private function getBarisTamu(): array
{
    if (!$this->berandaPolos()) {
        return [];
    }

    $topik = $this->topikAktif();
    return cache()->remember('opac_rak_beranda_' . $topik, 3600, function () use ($topik) {
        $R       = \Rekomendasi\Libraries\Ranker::class;
        $populer = $this->katalogModel->kandidatPopuler();
        $peta    = array_column($populer, 'populer', 'id');

        $baris = $R::halaman([
            'topik'   => $R::baris($this->kandidatTopik($topik, $peta), $R::BOBOT_COLD_START),
            'populer' => $R::baris($populer, $R::BOBOT_COLD_START),
            'baru'    => $R::baris($this->katalogModel->kandidatBaru(), $R::BOBOT_COLD_START),
        ]);
        foreach ($baris as &$b) {
            $b = enrichCatalogsWithCirculation($b);
        }
        unset($b);
        return $baris;
    });
}

private function getBarisBeranda(): array
{
    if (!$this->berandaPolos()) {
        return [];
    }

    $anggota = session('session_member');
    if (!$anggota) {
        return [];
    }

    $semua = $this->getBarisAnggota($anggota);
    return array_diff_key($semua, ['Paling banyak dipinjam' => 1, 'Koleksi baru' => 1]);
}

// Data panel Filter (kelas DDC dan pengarang terbanyak). Keduanya di-cache, jadi aman dipanggil di setiap halaman OPAC.
private function getJelajah(): array
{
    $pengarang = cache('opac_top_authors_v2');
    if (!$pengarang) {
        $pengarang = $this->db->table('catalogs')
            ->select('Author as name, COUNT(*) as count')
            ->where('IsOPAC', 1)
            ->where("Author IS NOT NULL AND Author != ''", null, false)
            ->groupBy('Author')
            ->orderBy('count', 'DESC')
            ->limit(12)
            ->get()
            ->getResultArray();
        cache()->save('opac_top_authors_v2', $pengarang, 600);
    }

    return ['ddc' => $this->getDdcDirectoryData()['ddc_hierarchy'] ?? [], 'pengarang' => $pengarang];
}

public function getBarisAnggota(array $anggota): array
{
    $id = (int) $anggota['member_id'];
    return cache()->remember('rekomendasi_beranda_' . $id, 3600, function () use ($id, $anggota) {
        $R    = \Rekomendasi\Libraries\Ranker::class;
        $unit = (int) ($anggota['unit_kerja_id'] ?? 0) ?: null;
        $izin = (int) ($this->db->table('members')->select('IzinPersonalisasi')->where('ID', $id)->get()->getRow()->IzinPersonalisasi ?? 1);

        $riwayat  = $izin ? $this->katalogModel->riwayatAnggota($id) : [];
        $dipinjam = array_column($riwayat, 'id');
        $bobot    = \Rekomendasi\Libraries\Beranda::bobot(count($riwayat), (bool) $izin);

        $populerUnit = $this->katalogModel->kandidatPopuler(60, $unit);
        $namaUnit    = $unit ? $this->db->table('master_unit')->select('nama_unit')->where('id', $unit)->get()->getRow()->nama_unit ?? null : null;
        $petaPopuler = array_column($populerUnit, 'populer', 'id');

        $baris = [];
        if ($riwayat) {
            $terakhir = $riwayat[0] + ['ID' => $riwayat[0]['id']];
            $baris['Karena kamu meminjam "' . mb_strimwidth($terakhir['Title'], 0, 60, '...') . '"'] = $this->barisSerupa($terakhir, $bobot, $dipinjam, 20, $petaPopuler);
        }

        $baris[$namaUnit && $this->katalogModel->unitCukupPinjaman($unit) ? 'Populer di ' . $namaUnit : 'Paling banyak dipinjam'] = $R::baris($populerUnit, $R::BOBOT_COLD_START, $dipinjam);

        $subjek = \Rekomendasi\Libraries\Beranda::subjekFavorit($riwayat);
        if ($subjek) {
            $sumber   = ['DeweyNo' => '', 'Subject' => $subjek, 'Author' => ''];
            $kandidat = $this->katalogModel->kandidatSubjek($subjek);
            foreach ($kandidat as &$k) {
                $k['content'] = \Rekomendasi\Libraries\ContentScore::score($sumber, $k);
                $k['populer'] = $petaPopuler[$k['id']] ?? 0;
            }
            unset($k);
            $baris['Subjek favoritmu: ' . mb_convert_case($subjek, MB_CASE_TITLE)] = $R::baris($kandidat, $bobot, $dipinjam);
        }

        $kelas     = \Rekomendasi\Libraries\Beranda::kelasFavorit($riwayat);
        $judulBaru = 'Koleksi baru';
        $baru      = [];
        foreach ($kelas ? [$kelas, $kelas[0]] : [null] as $awalan) {
            $baru = $R::baris($this->katalogModel->kandidatBaru(60, $awalan), $R::BOBOT_COLD_START, $dipinjam);
            if ($awalan && count($baru) >= $R::MIN_PER_BARIS) {
                $tigaDigit = strlen($awalan) === 3;
                $nama      = $this->db->table('master_kelas_besar')->select('namakelas')->where('kdKelas', $tigaDigit ? $awalan : $awalan . '00')->get()->getRow()->namakelas ?? null;
                $nama      = $nama ? trim(preg_replace('/^\d+\s*-\s*/', '', $nama)) : null;
                $judulBaru = $tigaDigit
                    ? 'Koleksi baru di ' . ($nama ? 'bidang ' . $nama . ' (DDC ' . $awalan . ')' : 'DDC ' . $awalan)
                    : ($nama ? 'Koleksi baru di bidang ' . $nama : 'Koleksi baru di DDC ' . $awalan . '00');
                break;
            }
        }
        if (count($baru) < $R::MIN_PER_BARIS) {
            $judulBaru = 'Koleksi baru';
            $baru      = $R::baris($this->katalogModel->kandidatBaru(), $R::BOBOT_COLD_START, $dipinjam);
        }
        $baris[$judulBaru] = $baru;

        $baris = $R::halaman($baris);
        foreach ($baris as &$b) {
            $b = enrichCatalogsWithCirculation($b);
        }
        unset($b);
        return $baris;
    });
}

private function getOpacBanners()
{
    try {
        return $this->bannerModel->where('active', 1)->where('category', 'Opac')->orderBy('sort', 'ASC')->findAll();
    } catch (\Exception $e) {
        return [];
    }
}

private function applyAdvancedSearch($builder): bool
{
    $hasAdvCriteria = false;
    $advFields = $this->request->getVar('adv_field');
    $advQueries = $this->request->getVar('adv_query');
    $advBools = $this->request->getVar('adv_bool');
    $advMatches = $this->request->getVar('adv_match');

    $allowedFields = [
        'Title'           => 'Title',
        'Author'          => 'Author',
        'Subject'         => 'Subject',
        'Publisher'       => 'Publisher',
        'PublishLocation' => 'PublishLocation',
        'ISBN'            => 'ISBN',
        'DeweyNo'         => 'DeweyNo',
        'CallNumber'      => 'CallNumber'
    ];

    if (is_array($advQueries)) {
        $validCriteria = [];
        foreach ($advQueries as $i => $rawQ) {
            $cleanQ = sanitizeSearch((string)$rawQ);
            $field = $advFields[$i] ?? 'Title';
            if ($cleanQ !== '' && isset($allowedFields[$field])) {
                $boolOp = strtoupper(trim((string)($advBools[$i] ?? 'AND')));
                if (!in_array($boolOp, ['AND', 'OR', 'NOT'], true)) {
                    $boolOp = 'AND';
                }
                $matchMode = strtolower(trim((string)($advMatches[$i] ?? 'contains')));
                if (!in_array($matchMode, ['contains', 'exact', 'starts_with'], true)) {
                    $matchMode = 'contains';
                }

                $validCriteria[] = [
                    'field' => $allowedFields[$field],
                    'query' => $cleanQ,
                    'raw'   => trim((string)$rawQ),
                    'bool'  => $boolOp,
                    'match' => $matchMode
                ];
            }
        }

        if (!empty($validCriteria)) {
            $hasAdvCriteria = true;
            $builder->groupStart();
            foreach ($validCriteria as $idx => $c) {
                $col = $c['field'];
                $val = $c['query'];
                $raw = $c['raw'];
                $match = $c['match'];
                $bool = $c['bool'];

                $isFirst = ($idx === 0);

                if ($match === 'exact') {
                    if ($isFirst) {
                        $builder->where($col, $val);
                    } else {
                        if ($bool === 'OR') {
                            $builder->orWhere($col, $val);
                        } elseif ($bool === 'NOT') {
                            $builder->where("{$col} !=", $val);
                        } else {
                            $builder->where($col, $val);
                        }
                    }
                } elseif ($match === 'starts_with') {
                    if ($isFirst) {
                        $builder->like($col, $val, 'after');
                    } else {
                        if ($bool === 'OR') {
                            $builder->orLike($col, $val, 'after');
                        } elseif ($bool === 'NOT') {
                            $builder->notLike($col, $val, 'after');
                        } else {
                            $builder->like($col, $val, 'after');
                        }
                    }
                } else { // contains
                    if ($col === 'Title' || $col === 'Subject') {
                        if ($isFirst) {
                            $this->applyFulltextSearch($builder, $val, $col, $raw);
                        } else {
                            if ($bool === 'OR') {
                                $builder->orLike($col, $val);
                            } elseif ($bool === 'NOT') {
                                $builder->notLike($col, $val);
                            } else {
                                $builder->like($col, $val);
                            }
                        }
                    } else {
                        if ($isFirst) {
                            $builder->like($col, $val);
                        } else {
                            if ($bool === 'OR') {
                                $builder->orLike($col, $val);
                            } elseif ($bool === 'NOT') {
                                $builder->notLike($col, $val);
                            } else {
                                $builder->like($col, $val);
                            }
                        }
                    }
                }
            }
            $builder->groupEnd();
        }
    }

    // Filter rentang tahun
    $yearFrom = (int)($this->request->getVar('year_from') ?? 0);
    $yearTo   = (int)($this->request->getVar('year_to') ?? 0);
    if ($yearFrom > 0 && $yearTo > 0) {
        $minY = min($yearFrom, $yearTo);
        $maxY = max($yearFrom, $yearTo);
        $builder->where("catalogs.PublishYear REGEXP '^[12][0-9]{3}$'");
        $builder->where("CAST(catalogs.PublishYear AS SIGNED) >=", $minY);
        $builder->where("CAST(catalogs.PublishYear AS SIGNED) <=", $maxY);
        $hasAdvCriteria = true;
    } elseif ($yearFrom > 0) {
        $builder->where("catalogs.PublishYear REGEXP '^[12][0-9]{3}$'");
        $builder->where("CAST(catalogs.PublishYear AS SIGNED) >=", $yearFrom);
        $hasAdvCriteria = true;
    } elseif ($yearTo > 0) {
        $builder->where("catalogs.PublishYear REGEXP '^[12][0-9]{3}$'");
        $builder->where("CAST(catalogs.PublishYear AS SIGNED) <=", $yearTo);
        $hasAdvCriteria = true;
    }

    // Filter bahasa
    $lang = trim((string)($this->request->getVar('language') ?? ''));
    if ($lang !== '') {
        $builder->like('catalogs.Languages', sanitizeSearch($lang));
        $hasAdvCriteria = true;
    }

    // Filter hanya koleksi tersedia (eksemplar aktif & tidak sedang dipinjam: Status_id = 1)
    $onlyAvail = (int)($this->request->getVar('only_available') ?? 0);
    if ($onlyAvail === 1) {
        $builder->where("EXISTS (SELECT 1 FROM collections col_chk WHERE col_chk.Catalog_id = catalogs.ID AND col_chk.Status_id = 1)");
        $hasAdvCriteria = true;
    }

    return $hasAdvCriteria;
}

    /** Batasi $builder ke satu kategori subjek. Dipakai filter katalog (?category=) dan rak topik beranda. */
    private function terapkanKategori($builder, string $category): void
    {
        switch ($category) {
            case 'fiksi':
                $builder->where("(catalogs.Subject LIKE '%fiksi%' OR catalogs.Subject LIKE '%sastra%' OR catalogs.Subject LIKE '%novel%' OR catalogs.Subject LIKE '%cerita%')", null, false);
                break;
            case 'pendidikan':
                $builder->where("catalogs.Subject LIKE '%pendidikan%'", null, false);
                break;
            case 'sejarah':
                $builder->where("(catalogs.Subject LIKE '%sejarah%' OR catalogs.Subject LIKE '%biografi%')", null, false);
                break;
            case 'anak':
                $builder->where("(catalogs.Subject LIKE '%anak%' OR catalogs.Subject LIKE '%cerita%' OR catalogs.Subject LIKE '%dongeng%')", null, false);
                break;
            case 'pengembangan_diri':
                $builder->where("(catalogs.Subject LIKE '%pengembangan diri%' OR catalogs.Subject LIKE '%psikologi%' OR catalogs.Subject LIKE '%motivasi%' OR catalogs.Subject LIKE '%kepemimpinan%' OR catalogs.Subject LIKE '%kewiraswastaan%')", null, false);
                break;
            case 'menenangkan':
                $builder->where("(catalogs.Subject LIKE '%puisi%' OR catalogs.Subject LIKE '%sastra%' OR catalogs.Subject LIKE '%seni%' OR catalogs.Subject LIKE '%filsafat%' OR catalogs.Subject LIKE '%refleksi%' OR catalogs.Subject LIKE '%spiritual%')", null, false);
                break;
            case 'rasa_ingin_tahu':
                $builder->where("(catalogs.Subject LIKE '%sains%' OR catalogs.Subject LIKE '%ilmiah%' OR catalogs.Subject LIKE '%teknologi%' OR catalogs.Subject LIKE '%riset%' OR catalogs.Subject LIKE '%penelitian%' OR catalogs.Subject LIKE '%sejarah%')", null, false);
                break;
            case 'inspirasi_visual':
                $builder->where("(catalogs.Subject LIKE '%desain%' OR catalogs.Subject LIKE '%arsitektur%' OR catalogs.Subject LIKE '%fotografi%' OR catalogs.Subject LIKE '%seni rupa%' OR catalogs.Subject LIKE '%grafis%' OR catalogs.Subject LIKE '%ilustrasi%')", null, false);
                break;
            case 'petualangan':
                $builder->where("(catalogs.Subject LIKE '%petualangan%' OR catalogs.Subject LIKE '%eksplorasi%' OR catalogs.Subject LIKE '%fiksi%' OR catalogs.Subject LIKE '%travel%' OR catalogs.Subject LIKE '%perjalanan%')", null, false);
                break;
            default:
                $builder->like('catalogs.Subject', sanitizeSearch($category));
                break;
        }
    }

    private function applyOpacCatalogFilters($builder): array
    {
        $allowedSearchBy = ['Title', 'Author', 'Subject', 'Publisher', 'ISBN', 'DeweyNo'];
        $rawSearchBy     = (string) ($this->request->getVar('search_by') ?? '');
        $searchBy        = in_array($rawSearchBy, $allowedSearchBy, true) ? $rawSearchBy : '';

        $rawSearch = (string) ($this->request->getVar('search') ?? '');
        $search    = sanitizeSearch($rawSearch);

        $this->applyAdvancedSearch($builder);

        if ($search !== '') {
            $this->applyFulltextSearch($builder, $search, $searchBy, $rawSearch);
        }

        // Filter Kategori (?category=, dari rak topik dan tautan "Semua buku ...")
        $category = trim((string) ($this->request->getVar('category') ?? ''));
        if ($category !== '' && $category !== 'semua') {
            $this->terapkanKategori($builder, $category);
        }

        // Filter Ketersediaan (Semua, Tersedia, Sedang dipinjam)
        $availability = trim((string) ($this->request->getVar('availability') ?? 'semua'));
        if ($availability === 'tersedia') {
            $builder->where("EXISTS (SELECT 1 FROM collections col_av WHERE col_av.Catalog_id = catalogs.ID AND col_av.Status_id = 1)", null, false);
        } elseif ($availability === 'dipinjam') {
            $builder->where("EXISTS (SELECT 1 FROM collections col_ln WHERE col_ln.Catalog_id = catalogs.ID AND col_ln.Status_id = 5)", null, false);
        }

        // Filter Format Koleksi (Buku cetak=1, E-book=2, Buku audio=10)
        $format = trim((string) ($this->request->getVar('format') ?? ''));
        if ($format === 'cetak') {
            $builder->where('catalogs.Worksheet_id', 1);
        } elseif ($format === 'ebook') {
            $builder->where('catalogs.Worksheet_id', 2);
        } elseif ($format === 'audio') {
            $builder->where('catalogs.Worksheet_id', 10);
        }

        // Filter Bahasa (Indonesia: ind, Inggris: eng)
        $language = trim((string) ($this->request->getVar('language') ?? ''));
        if ($language === 'ind' || $language === 'id') {
            $builder->where('catalogs.Languages', 'ind');
        } elseif ($language === 'eng' || $language === 'en') {
            $builder->where('catalogs.Languages', 'eng');
        }

        // Filter Rentang Tahun Terbit (Dari & Sampai)
        $yearStart = trim((string) ($this->request->getVar('year_start') ?? ''));
        $yearEnd   = trim((string) ($this->request->getVar('year_end') ?? ''));
        if ($yearStart !== '' && is_numeric($yearStart)) {
            $builder->where('catalogs.PublishYear >=', (int) $yearStart);
        }
        if ($yearEnd !== '' && is_numeric($yearEnd)) {
            $builder->where('catalogs.PublishYear <=', (int) $yearEnd);
        }

        // Filter Tambahan Tradisional
        $additionalFilters = ['Publisher', 'Author', 'ISBN', 'PublishLocation', 'Subject', 'PublishYear', 'DeweyNo'];
        foreach ($additionalFilters as $filter) {
            $value = $this->request->getVar($filter);
            if (!empty($value)) {
                if ($filter === 'Author') {
                    $authors = array_values(array_filter(array_map('trim', preg_split('/[;,]+/', (string) $value))));
                    if (!empty($authors)) {
                        $builder->groupStart();
                        foreach ($authors as $idx => $author) {
                            $cleanAuthor = sanitizeSearch($author);
                            if ($idx === 0) {
                                $builder->like('Author', $cleanAuthor);
                            } else {
                                $builder->orLike('Author', $cleanAuthor);
                            }
                        }
                        $builder->groupEnd();
                    }
                } elseif ($filter === 'Subject') {
                    if ($searchBy === 'Subject' && mb_strtolower(trim((string) $value)) === mb_strtolower(trim($rawSearch))) {
                        continue;
                    }
                    $this->applyFulltextSearch($builder, sanitizeSearch((string) $value), 'Subject', (string) $value);
                } elseif ($filter === 'DeweyNo') {
                    if ($searchBy === 'DeweyNo' && mb_strtolower(trim((string) $value)) === mb_strtolower(trim($rawSearch))) {
                        continue;
                    }
                    $cleanDewey = sanitizeSearch((string) $value);
                    if (strlen($cleanDewey) === 3 && substr($cleanDewey, -1) === '0') {
                        $prefix = substr($cleanDewey, 0, 2);
                        $builder->where("(DeweyNo LIKE '{$cleanDewey}%' OR DeweyNo LIKE '{$prefix}%')", null, false);
                    } else {
                        $builder->like('DeweyNo', $cleanDewey, 'after');
                    }
                } else {
                    $builder->like($filter, sanitizeSearch((string) $value));
                }
            }
        }

        // Filter huruf abjad (?letter=A)
        $rawLetter = trim((string) ($this->request->getVar('letter') ?? ''));
        $letter    = preg_match('/^[A-Za-z0-9]$/', $rawLetter) ? strtoupper($rawLetter) : '';
        $letterCol = ($searchBy === 'Author') ? 'catalogs.Author' : 'catalogs.Title';
        if ($letter !== '') {
            $builder->like($letterCol, $letter, 'after');
        }

        // Pengurutan (Sorting)
        $rawSort = trim((string) ($this->request->getVar('sort') ?? ''));
        $sort    = $rawSort !== '' ? $rawSort : 'terbaru';

        if ($rawSort === '' && $letter !== '') {
            $builder->orderBy($letterCol, 'ASC')->orderBy('catalogs.ID', 'DESC');
        } else {
            switch ($sort) {
                case 'title_asc':
                    $builder->orderBy('catalogs.Title', 'ASC');
                    break;
                case 'title_desc':
                    $builder->orderBy('catalogs.Title', 'DESC');
                    break;
                case 'year_desc':
                    $builder->orderBy('catalogs.PublishYear', 'DESC')->orderBy('catalogs.ID', 'DESC');
                    break;
                case 'year_asc':
                    $builder->where("catalogs.PublishYear IS NOT NULL AND catalogs.PublishYear != '' AND catalogs.PublishYear != '0000'")
                            ->orderBy('catalogs.PublishYear', 'ASC')->orderBy('catalogs.ID', 'DESC');
                    break;
                case 'populer':
                    $builder->join(
                        '(SELECT Catalog_id, COUNT(*) as loan_count FROM collections c JOIN collectionloanitems cli ON cli.Collection_id = c.ID GROUP BY Catalog_id) loan_stats',
                        'loan_stats.Catalog_id = catalogs.ID',
                        'left'
                    )->orderBy('COALESCE(loan_stats.loan_count, 0)', 'DESC', false)->orderBy('catalogs.ID', 'DESC');
                    break;
                case 'terbaru':
                default:
                    $builder->orderBy('catalogs.ID', 'DESC');
                    break;
            }
        }

        return [
            'search'              => $rawSearch,
            'search_by'           => $searchBy,
            'letter'              => $letter,
            'category'            => $category,
            'availability'        => $availability,
            'format'              => $format,
            'language'            => $language,
            'year_start'          => $yearStart,
            'year_end'            => $yearEnd,
            'sort'                => $sort,
            'Author'              => (string) ($this->request->getVar('Author') ?? ''),
            'Subject'             => (string) ($this->request->getVar('Subject') ?? ''),
            'DeweyNo'             => (string) ($this->request->getVar('DeweyNo') ?? ''),
            'Publisher'           => (string) ($this->request->getVar('Publisher') ?? ''),
            'ISBN'                => (string) ($this->request->getVar('ISBN') ?? ''),
            'PublishYear'         => (string) ($this->request->getVar('PublishYear') ?? ''),
            'PublishLocation'     => (string) ($this->request->getVar('PublishLocation') ?? ''),
        ];
    }

private function loadRegularCatalogs()
{
    $perPage     = 16;
    $currentPage = (int) ($this->request->getVar('page') ?? 1);
    if ($currentPage < 1) $currentPage = 1;

    $builder = $this->katalogModel->select('catalogs.*')->where('IsOPAC', 1);
    $filterParams = $this->applyOpacCatalogFilters($builder);

    $catalogs = $builder->paginate($perPage, 'default', $currentPage);
    $catalogs = enrichCatalogsWithCirculation($catalogs);
    $pager    = $this->katalogModel->pager;

    $this->data['pager']         = $pager;
    $this->data['catalogs']      = $catalogs;
    $this->data['total_records'] = $pager ? $pager->getTotal() : count($catalogs);
    $this->data['currentPage']   = $currentPage;
    $this->data['perPage']       = $perPage;

    $this->loadTopSidebarFacets();

    $this->data = array_merge($this->data, $filterParams);

    $subjectParam = trim((string) ($this->request->getVar('Subject') ?? ''));
    $deweyParam   = trim((string) ($this->request->getVar('DeweyNo') ?? ''));
    if ($filterParams['search'] === '' && $subjectParam !== '') {
        $this->data['search']    = $subjectParam;
        $this->data['search_by'] = 'Subject';
    } elseif ($filterParams['search'] === '' && $deweyParam !== '') {
        $this->data['search']    = $deweyParam;
        $this->data['search_by'] = 'DeweyNo';
    } else {
        $this->data['search']    = $filterParams['search'];
        $this->data['search_by'] = $filterParams['search_by'];
    }
}

private function loadRegularCatalogscache()
{
    $cacheTTL    = 3600;
    $perPage     = 16;
    $currentPage = (int) ($this->request->getVar('page') ?? 1);
    if ($currentPage < 1) $currentPage = 1;

    $requestParams = $this->request->getGet();
    ksort($requestParams);
    $cacheKey = 'catalog_data_v2_' . md5(http_build_query($requestParams));

    if ($cachedData = cache($cacheKey)) {
        // ========== CACHE HIT ==========
        $this->data   = array_merge($this->data, $cachedData);
        $totalRecords = $cachedData['total_records'];

        // ✅ Buat pager HTML string dengan custom template
        $pager = service('pager');
        $pager->setPath('/opac', 'default');
        $this->data['pager'] = $pager->makeLinks($currentPage, $perPage, $totalRecords, 'opac_pagination');

    } else {
        // ========== CACHE MISS ==========
        $builder = $this->katalogModel->select('catalogs.*')->where('IsOPAC', 1);
        $filterParams = $this->applyOpacCatalogFilters($builder);

        $catalogs     = $builder->paginate($perPage, 'default', $currentPage);
        $catalogs     = enrichCatalogsWithCirculation($catalogs);
        $pager        = $this->katalogModel->pager;
        $totalRecords = $pager ? $pager->getTotal() : count($catalogs);

        // ✅ Simpan pager sebagai OBJEK untuk cache miss
        $this->data['pager']         = $pager;
        $this->data['catalogs']      = $catalogs;
        $this->data['total_records'] = $totalRecords;

        $dataToCache = [
            'total_records'          => $totalRecords,
            'catalogs'               => $catalogs,
        ];

        cache()->save($cacheKey, $dataToCache, $cacheTTL);
        $this->data = array_merge($this->data, $dataToCache);
    }

    $this->loadTopSidebarFacets();

    $rawLetter       = trim((string) ($this->request->getVar('letter') ?? ''));
    $rawSearch       = (string) ($this->request->getVar('search') ?? '');
    $rawSearchBy     = trim((string) ($this->request->getVar('search_by') ?? ''));
    $allowedSearchBy = ['Title', 'Author', 'Subject', 'Publisher', 'ISBN', 'DeweyNo'];
    $searchBy        = in_array($rawSearchBy, $allowedSearchBy, true) ? $rawSearchBy : '';

    $this->data['currentPage']       = $currentPage;
    $this->data['perPage']           = $perPage;
    $this->data['letter']            = preg_match('/^[A-Za-z0-9]$/', $rawLetter) ? strtoupper($rawLetter) : '';
    $this->data['Author']            = (string) ($this->request->getVar('Author') ?? '');
    $this->data['Subject']           = (string) ($this->request->getVar('Subject') ?? '');
    $this->data['DeweyNo']           = (string) ($this->request->getVar('DeweyNo') ?? '');
    $this->data['Publisher']         = (string) ($this->request->getVar('Publisher') ?? '');
    $this->data['PublishYear']       = (string) ($this->request->getVar('PublishYear') ?? '');
    $this->data['PublishLocation']   = (string) ($this->request->getVar('PublishLocation') ?? '');
    $this->data['category']          = (string) ($this->request->getVar('category') ?? '');
    $this->data['availability']      = (string) ($this->request->getVar('availability') ?? 'semua');
    $this->data['format']            = (string) ($this->request->getVar('format') ?? '');
    $this->data['language']          = (string) ($this->request->getVar('language') ?? '');
    $this->data['year_start']        = (string) ($this->request->getVar('year_start') ?? '');
    $this->data['year_end']          = (string) ($this->request->getVar('year_end') ?? '');
    $this->data['sort']              = (string) ($this->request->getVar('sort') ?? 'terbaru');

    $subjectParam = trim((string) ($this->request->getVar('Subject') ?? ''));
    $deweyParam   = trim((string) ($this->request->getVar('DeweyNo') ?? ''));
    if ($rawSearch === '' && $subjectParam !== '') {
        $this->data['search']    = $subjectParam;
        $this->data['search_by'] = 'Subject';
    } elseif ($rawSearch === '' && $deweyParam !== '') {
        $this->data['search']    = $deweyParam;
        $this->data['search_by'] = 'DeweyNo';
    } else {
        $this->data['search']    = $rawSearch;
        $this->data['search_by'] = $searchBy;
    }
}

/**
 * Memuat data statistik agregat teratas (penerbit, pengarang, subjek populer, kota terbit, tahun terbit)
 * didasarkan pada data terbanyak dari seluruh koleksi katalog di perpustakaan.
 */
private function loadTopSidebarFacets(): void
{
    $cacheKey = 'opac_top_sidebar_facets_v2';
    $cached = cache($cacheKey);

    if ($cached !== null && is_array($cached)) {
        $this->data = array_merge($this->data, $cached);
        return;
    }

    $db = \Config\Database::connect();

    // 1. Penerbit Teratas (Top 10)
    $publisherRows = $db->query("
        SELECT TRIM(TRAILING ',' FROM TRIM(TRAILING ';' FROM TRIM(Publisher))) as name, COUNT(*) as cnt
        FROM catalogs
        WHERE IsOPAC = 1 AND Publisher IS NOT NULL AND TRIM(Publisher) != ''
        GROUP BY name
        ORDER BY cnt DESC
        LIMIT 10
    ")->getResultArray();

    $publisherCounts = [];
    foreach ($publisherRows as $row) {
        if (!empty($row['name'])) {
            $publisherCounts[$row['name']] = (int)$row['cnt'];
        }
    }

    // 2. Pengarang Teratas (Top 10)
    $authorRows = $db->query("
        SELECT TRIM(TRAILING ';' FROM TRIM(TRAILING ',' FROM TRIM(Author))) as name, COUNT(*) as cnt
        FROM catalogs
        WHERE IsOPAC = 1 AND Author IS NOT NULL AND TRIM(Author) != ''
        GROUP BY name
        ORDER BY cnt DESC
        LIMIT 10
    ")->getResultArray();

    $authorCounts = [];
    foreach ($authorRows as $row) {
        if (!empty($row['name'])) {
            $authorCounts[$row['name']] = (int)$row['cnt'];
        }
    }

    // 3. Kota Terbit Teratas (Top 10)
    $locationRows = $db->query("
        SELECT TRIM(TRAILING ':' FROM TRIM(TRAILING ',' FROM TRIM(PublishLocation))) as name, COUNT(*) as cnt
        FROM catalogs
        WHERE IsOPAC = 1 AND PublishLocation IS NOT NULL AND TRIM(PublishLocation) != ''
        GROUP BY name
        ORDER BY cnt DESC
        LIMIT 10
    ")->getResultArray();

    $locationCounts = [];
    foreach ($locationRows as $row) {
        if (!empty($row['name'])) {
            $locationCounts[$row['name']] = (int)$row['cnt'];
        }
    }

    // 4. Tahun Terbit Teratas (Top 10)
    $yearRows = $db->query("
        SELECT PublishYear as name, COUNT(*) as cnt
        FROM catalogs
        WHERE IsOPAC = 1 AND PublishYear IS NOT NULL AND PublishYear REGEXP '^[12][0-9]{3}$'
        GROUP BY PublishYear
        ORDER BY cnt DESC
        LIMIT 10
    ")->getResultArray();

    $yearCounts = [];
    foreach ($yearRows as $row) {
        if (!empty($row['name'])) {
            $yearCounts[(string)$row['name']] = (int)$row['cnt'];
        }
    }

    // 5. Subjek Populer Teratas (Top 10, dipecah per subjek individual)
    $subjectRows = $db->query("
        SELECT Subject
        FROM catalogs
        WHERE IsOPAC = 1 AND Subject IS NOT NULL AND TRIM(Subject) != ''
    ")->getResultArray();

    helper('opac');
    $allSubjectCounts = [];
    foreach ($subjectRows as $row) {
        $subs = splitSubjects($row['Subject']);
        foreach ($subs as $s) {
            $allSubjectCounts[$s] = ($allSubjectCounts[$s] ?? 0) + 1;
        }
    }
    arsort($allSubjectCounts);
    $subjectCounts = array_slice($allSubjectCounts, 0, 10, true);

    $facets = [
        'publisher_counts'        => $publisherCounts,
        'author_counts'           => $authorCounts,
        'publish_location_counts' => $locationCounts,
        'year_counts'             => $yearCounts,
        'subject_counts'          => $subjectCounts,
    ];

    cache()->save($cacheKey, $facets, 3600);
    $this->data = array_merge($this->data, $facets);
}
 

    public function detail($id)
    {
        $file = $this->fileModel->where('Catalog_id', $id)->first();
        if ($file !== null) {
            $ID = $file->ID;
            $this->data['ID'] = $ID;
        }



        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        // Get eksemplar data
        $EksemplarModel = new \Eksemplar\Models\EksemplarModel();

        // Get physical books (non-DRM)
        $roweksemplar = $EksemplarModel
            ->select('collections.NomorBarcode, collections.CallNumber, collections.Status_id, collectionrules.Name as RuleName, locations.Name as LocationName, collectionstatus.Name as StatusName')
            ->join('collectionrules', 'collectionrules.id = collections.Rule_id', 'left')
            ->join('locations', 'locations.id = collections.Location_id', 'left')
            ->join('collectionstatus', 'collectionstatus.id = collections.Status_id', 'left')
            ->where('collections.catalog_id', $id)
            ->where('collections.ISDRM', 0)
            ->findAll();


        // Get digital books (DRM)
        $roweksemplar_drm = $EksemplarModel
            ->select('collections.ID as CollectionID, collections.NomorBarcode, collections.CallNumber, collections.Status_id, collectionrules.Name as RuleName, locations.Name as LocationName, collectionstatus.Name as StatusName')
            ->join('collectionrules', 'collectionrules.id = collections.Rule_id', 'left')
            ->join('locations', 'locations.id = collections.Location_id', 'left')
            ->join('collectionstatus', 'collectionstatus.id = collections.Status_id', 'left')
            ->where('collections.catalog_id', $id)
            ->where('collections.ISDRM', 1)
            ->findAll();

        // Kumpulkan ID koleksi DRM yang sedang dipinjam oleh anggota yang login
        $member_active_loan_collections = [];
        try {
            $nomor_anggota = user()->username;
            $member        = $this->memberModel->where('MemberNo', $nomor_anggota)->first();
            if ($member && !empty($roweksemplar_drm)) {
                $collection_ids = array_column((array) $roweksemplar_drm, 'CollectionID');
                $active_loans   = $this->db->table('collectionloanitems')
                    ->whereIn('Collection_id', $collection_ids)
                    ->where('member_id', $member->ID)
                    ->where('LoanStatus', 'Loan')
                    ->where('DueDate >=', date('Y-m-d H:i:s'))
                    ->get()
                    ->getResult();
                $member_active_loan_collections = array_column($active_loans, 'Collection_id');
            }
        } catch (\Throwable $e) {
            // User tidak login atau bukan anggota – abaikan
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->findAll();

        $this->data['marc'] = $marc;
        $this->data['files_digital'] = $this->fileModel->where('Catalog_id', $id)->findAll();

        $this->data['title']       = 'Detail Katalog - ' . $catalog['Title'];
        $this->data['catalog']     = $catalog;
        $this->data['roweksemplar']     = $roweksemplar;
        $this->data['roweksemplar_drm'] = $roweksemplar_drm;
        $this->data['member_active_loan_collections'] = $member_active_loan_collections;
        $this->data['buku_terkait'] = $this->bukuTerkait($catalog);

        return view('Opac\Views\detail', $this->data);
    }

    /** Kolab judul $id ke judul lain, di-cache 24 jam. Cache kosong: dua query ke collectionloanitems, waktunya dicatat di log. */
    private function kolab(int $id): array
    {
        $key  = 'rekomendasi_kolab_' . $id;
        $ada  = cache($key);
        if (is_array($ada)) {
            return $ada;
        }

        $t0 = microtime(true);
        $pinjaman = $this->db->query(
            'SELECT DISTINCT li.member_id, c.catalog_id
             FROM collectionloanitems li JOIN collections c ON c.ID = li.Collection_id
             WHERE li.member_id IN (
                 SELECT li2.member_id FROM collectionloanitems li2
                 JOIN collections c2 ON c2.ID = li2.Collection_id WHERE c2.catalog_id = ?)',
            [$id]
        )->getResultArray();

        $tetangga = \Rekomendasi\Libraries\Kolab::tetangga($pinjaman, $id);
        $nY = [];
        if ($tetangga['c']) {
            $rows = $this->db->table('collectionloanitems li')
                ->select('c.catalog_id, COUNT(DISTINCT li.member_id) AS n', false)
                ->join('collections c', 'c.ID = li.Collection_id')
                ->whereIn('c.catalog_id', array_keys($tetangga['c']))
                ->groupBy('c.catalog_id')
                ->get()->getResultArray();
            $nY = array_column($rows, 'n', 'catalog_id');
        }

        $hasil = \Rekomendasi\Libraries\Kolab::hitung($tetangga, $nY);
        cache()->save($key, $hasil, 86400);
        log_message('info', sprintf('[rekomendasi] kolab katalog %d: cache kosong, %.1f ms, %d pasangan', $id, (microtime(true) - $t0) * 1000, count($hasil)));
        return $hasil;
    }

    /** Baris "Buku terkait" untuk satu judul; kosong kalau kurang dari Ranker::MIN_PER_BARIS. */
    private function bukuTerkait(array $catalog): array
    {
        $baris = $this->barisSerupa($catalog, \Rekomendasi\Libraries\Ranker::BOBOT_DETAIL);
        return \Rekomendasi\Libraries\Ranker::halaman(['terkait' => $baris])['terkait'] ?? [];
    }

    private function barisSerupa(array $catalog, array $bobot, array $dipinjam = [], int $batas = 8, array $populer = []): array
    {
        $ddc = preg_replace('/[\s.]/', '', (string) ($catalog['DeweyNo'] ?? ''));
        if (strlen($ddc) < 3) {
            return [];
        }

        $kandidat = $this->katalogModel->asArray()
            ->where('IsOPAC', 1)
            ->where('ID !=', $catalog['ID'])
            ->like('DeweyNo', substr($ddc, 0, 3), 'after')
            ->limit(200)
            ->findAll();

        // tambah judul yang ikut dipinjam peminjam judul ini, walau DDC-nya beda
        $kolab = $this->kolab((int) $catalog['ID']);
        $ada   = array_column($kandidat, 'ID');
        $baru  = array_diff(array_keys($kolab), $ada);
        if ($baru) {
            $kandidat = array_merge($kandidat, $this->katalogModel->asArray()->where('IsOPAC', 1)->whereIn('ID', $baru)->findAll());
        }

        foreach ($kandidat as &$k) {
            $k['id']        = $k['ID'];
            $k['content']   = \Rekomendasi\Libraries\ContentScore::score($catalog, $k);
            $k['kolab']     = $kolab[$k['ID']]['kolab'] ?? 0;
            $k['keyakinan'] = $kolab[$k['ID']]['keyakinan'] ?? 0;
            $k['populer']   = $populer[$k['ID']] ?? 0;
        }
        unset($k);

        return \Rekomendasi\Libraries\Ranker::baris($kandidat, $bobot, $dipinjam, \Rekomendasi\Libraries\Ranker::majalah($catalog), $batas);
    }

    public function search()
    {
        $this->data['title'] = 'Pencarian Katalog';

        // Advanced search form
        $searchData = [
            'title' => $this->request->getVar('title'),
            'author' => $this->request->getVar('author'),
            'subject' => $this->request->getVar('subject'),
            'publisher' => $this->request->getVar('publisher'),
            'isbn' => $this->request->getVar('isbn'),
            'year_from' => $this->request->getVar('year_from'),
            'year_to' => $this->request->getVar('year_to'),
            'language' => $this->request->getVar('language')
        ];

        $results     = [];
        $total       = 0;
        $perPage     = 12;
        $currentPage = $this->request->getVar('page') ?? 1;

        if ($this->request->getMethod() === 'post' || $this->request->getVar('submit')) {
            $builder = $this->katalogModel->select('catalogs.*');

            if ($searchData['title']) {
                $term = $this->toFtTerm($searchData['title']);
                $term ? $builder->where("MATCH(Title) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Title', $searchData['title']);
            }
            if ($searchData['author']) {
                $term = $this->toFtTerm($searchData['author']);
                $term ? $builder->where("MATCH(Author) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Author', $searchData['author']);
            }
            if ($searchData['subject']) {
                $term = $this->toFtTerm($searchData['subject']);
                $term ? $builder->where("MATCH(Subject) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Subject', $searchData['subject']);
            }
            if ($searchData['publisher']) {
                $term = $this->toFtTerm($searchData['publisher']);
                $term ? $builder->where("MATCH(Publisher) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Publisher', $searchData['publisher']);
            }
            if ($searchData['isbn']) {
                $builder->like('ISBN', $searchData['isbn']);
            }
            if ($searchData['year_from']) {
                $builder->where('PublishYear >=', $searchData['year_from']);
            }
            if ($searchData['year_to']) {
                $builder->where('PublishYear <=', $searchData['year_to']);
            }
            if ($searchData['language']) {
                $builder->like('Languages', $searchData['language']);
            }

            $results = $builder->paginate($perPage, 'default', $currentPage);
            $total   = $this->katalogModel->pager->getTotal();
        }

        $this->data['pager']       = $this->katalogModel->pager ?? null;
        $this->data['search_data'] = $searchData;
        $this->data['results']     = $results;
        $this->data['total_found'] = $total;

        return view('Opac\Views\search', $this->data);
    }

/**
 * Halaman Browse digabung ke OPAC (#jelajah). Tautan lama tetap hidup lewat redirect.
 */
public function browse()
{
    $type   = (string) ($this->request->getVar('type') ?? '');
    $q      = trim((string) ($this->request->getVar('q') ?? ''));
    $letter = strtoupper(trim((string) ($this->request->getVar('letter') ?? '')));

    if ($type === 'title' || $type === 'author') {
        $by = $type === 'author' ? 'Author' : 'Title';
        $to = $q !== ''
            ? ['search' => $q, 'search_by' => $by]
            : ['search_by' => $by, 'letter' => preg_match('/^[A-Z0-9]$/', $letter) ? $letter : 'A'];
        return redirect()->to(base_url('opac?' . http_build_query($to)));
    }

    if ($q !== '') {
        return redirect()->to(base_url('opac?' . http_build_query(['search' => $q, 'search_by' => ctype_digit($q) ? 'DeweyNo' : 'Subject'])));
    }

    return redirect()->to(base_url('opac#jelajah'));
}

private function getSubjectDirectoryData($letter = '', $query = '')
{
    $cacheKey  = 'opac_subjects_directory_v2';
    $cacheData = cache($cacheKey);

    if (!$cacheData) {
        $rows = $this->db->table('catalogs')
            ->select('Subject')
            ->where('IsOPAC', 1)
            ->where("Subject IS NOT NULL AND Subject != ''", null, false)
            ->get()
            ->getResultArray();

        $subjectMap = [];
        $totalCatalogsWithSubject = count($rows);

        foreach ($rows as $r) {
            $parts = explode(';', $r['Subject']);
            $seenInThisCatalog = [];

            foreach ($parts as $p) {
                $p = trim($p);
                if ($p === '') continue;
                $clean = trim($p, " \t\n\r\0\x0B.,;:-");
                if ($clean === '') continue;

                $lower = mb_strtolower($clean);
                if (isset($seenInThisCatalog[$lower])) continue;
                $seenInThisCatalog[$lower] = true;

                if (!isset($subjectMap[$lower])) {
                    $firstChar = mb_strtoupper(mb_substr($clean, 0, 1));
                    if (!preg_match('/^[A-Z]$/', $firstChar)) {
                        $firstChar = '#';
                    }

                    // Deteksi subdivisi sub-tajuk dengan pemisah '--'
                    $subParts = explode('--', $clean);
                    $mainTerm = trim($subParts[0]);
                    $subTerm  = isset($subParts[1]) ? trim(implode(' -- ', array_slice($subParts, 1))) : null;

                    $subjectMap[$lower] = [
                        'name'      => $clean,
                        'main_term' => $mainTerm,
                        'sub_term'  => $subTerm,
                        'count'     => 0,
                        'letter'    => $firstChar,
                    ];
                }
                $subjectMap[$lower]['count']++;
            }
        }

        // Urutkan secara alfabetis berdasarkan nama subjek
        uasort($subjectMap, static function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        // 12 Subjek terpopuler dengan jumlah koleksi terbanyak
        $topList = $subjectMap;
        uasort($topList, static function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        $topSubjects = array_slice(array_values($topList), 0, 12);

        // Pengelompokan berdasarkan huruf A-Z dan #
        $grouped = [];
        $letterCounts = [];
        foreach (range('A', 'Z') as $char) {
            $grouped[$char] = [];
            $letterCounts[$char] = 0;
        }
        $grouped['#'] = [];
        $letterCounts['#'] = 0;

        foreach ($subjectMap as $item) {
            $l = $item['letter'];
            $grouped[$l][] = $item;
            $letterCounts[$l]++;
        }

        $cacheData = [
            'total_subjects'    => count($subjectMap),
            'total_catalogs'    => $totalCatalogsWithSubject,
            'top_subjects'      => $topSubjects,
            'grouped_by_letter' => $grouped,
            'letter_counts'     => $letterCounts,
            'all_subjects'      => array_values($subjectMap),
        ];

        cache()->save($cacheKey, $cacheData, 600);
    }

    $allSubjects     = $cacheData['all_subjects'];
    $groupedByLetter = $cacheData['grouped_by_letter'];
    $letterCounts    = $cacheData['letter_counts'];
    $topSubjects     = $cacheData['top_subjects'];
    $totalSubjects   = $cacheData['total_subjects'];
    $totalCatalogs   = $cacheData['total_catalogs'];

    // Saring data jika ada query parameter pencarian (q) atau huruf (letter)
    $filteredGrouped = $groupedByLetter;
    $activeLetter    = '';
    $searchQuery     = '';
    $matchedCount    = $totalSubjects;

    if ($query !== '') {
        $searchQuery     = $query;
        $filteredGrouped = [];
        $queryLower      = mb_strtolower($query);
        $matchedCount    = 0;

        foreach ($groupedByLetter as $l => $items) {
            $matchingItems = [];
            foreach ($items as $it) {
                if (mb_stripos($it['name'], $queryLower) !== false) {
                    $matchingItems[] = $it;
                    $matchedCount++;
                }
            }
            if (!empty($matchingItems)) {
                $filteredGrouped[$l] = $matchingItems;
            }
        }
    } elseif ($letter !== '' && strtoupper($letter) !== 'ALL' && strtoupper($letter) !== 'SEMUA') {
        $activeLetter = strtoupper($letter);
        if (isset($groupedByLetter[$activeLetter])) {
            $filteredGrouped = [
                $activeLetter => $groupedByLetter[$activeLetter]
            ];
            $matchedCount = count($groupedByLetter[$activeLetter]);
        } else {
            $filteredGrouped = [];
            $matchedCount    = 0;
        }
    }

    return [
        'total_subjects'    => $totalSubjects,
        'total_catalogs'    => $totalCatalogs,
        'top_subjects'      => $topSubjects,
        'grouped_by_letter' => $filteredGrouped,
        'letter_counts'     => $letterCounts,
        'active_letter'     => $activeLetter,
        'search_query'      => $searchQuery,
        'matched_count'     => $matchedCount,
    ];
}

/**
 * Helper untuk mengambil dan mencache direktori klasifikasi DDC (Dewey Decimal Classification)
 * Disusun secara hierarkis dan urut: 10 Kelas Utama (000-900) dan 100 Divisi (000, 010, 020, ... 990)
 */
private function getDdcDirectoryData($selectedClass = '', $query = '')
{
    $cacheKey  = 'opac_ddc_directory_v3';
    $cacheData = cache($cacheKey);

    if (!$cacheData) {
        $ddcStructure = [
            '000' => [
                'name' => 'Karya Umum, Komputer & Informasi',
                'icon' => 'fa-laptop-code',
                'color' => '#023e9c',
                'divisions' => [
                    '000' => 'Ilmu Komputer, Informasi & Karya Umum',
                    '010' => 'Bibliografi & Katalog Buku',
                    '020' => 'Ilmu Perpustakaan & Informasi',
                    '030' => 'Ensiklopedia & Buku Referensi Umum',
                    '040' => 'Topik Khusus & Koleksi Terpilih',
                    '050' => 'Majalah, Jurnal & Publikasi Serial',
                    '060' => 'Organisasi, Asosiasi & Museum',
                    '070' => 'Media Berita, Jurnalisme & Penerbitan',
                    '080' => 'Kutipan & Koleksi Karya Umum',
                    '090' => 'Manuskrip, Naskah & Buku Langka'
                ]
            ],
            '100' => [
                'name' => 'Filsafat & Psikologi',
                'icon' => 'fa-brain',
                'color' => '#d97706',
                'divisions' => [
                    '100' => 'Filsafat Umum & Teori Pemikiran',
                    '110' => 'Metafisika & Hakikat Keberadaan',
                    '120' => 'Epistemologi, Sebab-Akibat & Manusia',
                    '130' => 'Parapsikologi, Okultisme & Fenomena Pikiran',
                    '140' => 'Aliran & Pandangan Filsafat Khusus',
                    '150' => 'Psikologi & Pengembangan Kepribadian',
                    '160' => 'Logika & Seni Bernalar',
                    '170' => 'Etika (Filsafat Moral)',
                    '180' => 'Filsafat Kuno, Abad Pertengahan & Timur',
                    '190' => 'Filsafat Modern Barat'
                ]
            ],
            '200' => [
                'name' => 'Agama & Spiritualitas',
                'icon' => 'fa-hands-praying',
                'color' => '#16a34a',
                'divisions' => [
                    '200' => 'Agama & Spiritualitas Umum',
                    '210' => 'Filsafat Agama & Teologi Alami',
                    '220' => 'Alkitab & Kajian Kitab Suci',
                    '230' => 'Kekristenan & Teologi Kristen',
                    '240' => 'Moral, Etika & Praktik Kehidupan Kristen',
                    '250' => 'Tata Kelola Gereja & Pelayanan Rohani',
                    '260' => 'Teologi Sosial Kristen & Eklesiologi',
                    '270' => 'Sejarah Gereja & Tokoh Agama',
                    '280' => 'Denominasi & Aliran Kristen',
                    '290' => 'Agama Lain & Agama Perbandingan (Islam, Hindu, Buddha, dll)'
                ]
            ],
            '300' => [
                'name' => 'Ilmu-ilmu Sosial',
                'icon' => 'fa-users',
                'color' => '#7c3aed',
                'divisions' => [
                    '300' => 'Ilmu Sosial Umum, Sosiologi & Antropologi',
                    '310' => 'Statistik Umum & Demografi Penduduk',
                    '320' => 'Ilmu Politik, Pemerintahan & Hubungan Luar Negeri',
                    '330' => 'Ekonomi, Ketenagakerjaan & Keuangan',
                    '340' => 'Hukum & Peraturan Perundang-undangan',
                    '350' => 'Administrasi Publik & Tata Kelola Negara',
                    '360' => 'Masalah Sosial, Layanan Sosial & Jaminan Sosial',
                    '370' => 'Pendidikan, Kurikulum & Pelatihan Kerja',
                    '380' => 'Perdagangan, Komunikasi & Transportasi',
                    '390' => 'Adat Istiadat, Tradisi Rakyat & Tata Krama'
                ]
            ],
            '400' => [
                'name' => 'Bahasa & Linguistik',
                'icon' => 'fa-language',
                'color' => '#db2777',
                'divisions' => [
                    '400' => 'Bahasa & Linguistik Umum',
                    '410' => 'Linguistik Terapan & Tata Bahasa',
                    '420' => 'Bahasa Inggris & Bahasa Britania',
                    '430' => 'Bahasa Jerman & Kerabatnya',
                    '440' => 'Bahasa Prancis & Roman',
                    '450' => 'Bahasa Italia, Rumania & Dialek Terkait',
                    '460' => 'Bahasa Spanyol & Portugis',
                    '470' => 'Bahasa Latin & Bahasa Italik Klasik',
                    '480' => 'Bahasa Yunani Klasik',
                    '490' => 'Bahasa Lain (Bahasa Indonesia, Arab, Melayu, Daerah)'
                ]
            ],
            '500' => [
                'name' => 'Sains & Matematika',
                'icon' => 'fa-atom',
                'color' => '#0284c7',
                'divisions' => [
                    '500' => 'Sains & Ilmu Pengetahuan Alam',
                    '510' => 'Matematika, Aljabar & Geometri',
                    '520' => 'Astronomi & Eksplorasi Luar Angkasa',
                    '530' => 'Fisika & Teori Material',
                    '540' => 'Kimia, Mineralogi & Kristalografi',
                    '550' => 'Ilmu Kebumian, Geologi & Cuaca',
                    '560' => 'Paleontologi & Fosil Prasejarah',
                    '570' => 'Biologi & Ilmu Kehidupan',
                    '580' => 'Botani (Ilmu Tumbuh-tumbuhan)',
                    '590' => 'Zoologi (Ilmu Hewan & Ekologi Hewan)'
                ]
            ],
            '600' => [
                'name' => 'Teknologi & Ilmu Terapan',
                'icon' => 'fa-gears',
                'color' => '#ea580c',
                'divisions' => [
                    '600' => 'Teknologi & Ilmu Terapan Umum',
                    '610' => 'Kesehatan, Kedokteran & K3 (Keselamatan Kerja)',
                    '620' => 'Rekayasa, Teknik Elektro & Mesin',
                    '630' => 'Pertanian, Kehutanan & Perikanan',
                    '640' => 'Tata Boga, Manajemen Rumah Tangga & Keluarga',
                    '650' => 'Manajemen Bisnis, Organisasi & SDM',
                    '660' => 'Teknik Kimia & Teknologi Industri Terkait',
                    '670' => 'Manufaktur & Pabrikasi Industri',
                    '680' => 'Produk Khusus & Kerajinan Industri',
                    '690' => 'Teknik Konstruksi & Bangunan Gedung'
                ]
            ],
            '700' => [
                'name' => 'Kesenian & Rekreasi',
                'icon' => 'fa-palette',
                'color' => '#9333ea',
                'divisions' => [
                    '700' => 'Kesenian Umum & Desain Kreatif',
                    '710' => 'Perencanaan Wilayah, Lanskap & Tata Kota',
                    '720' => 'Arsitektur & Desain Bangunan',
                    '730' => 'Seni Pahat, Seni Patung & Keramik',
                    '740' => 'Menggambar, Desain Grafis & Seni Terapan',
                    '750' => 'Seni Lukis & Lukisan Koleksi',
                    '760' => 'Seni Cetak Grafis & Litografi',
                    '770' => 'Fotografi, Sinematografi & Seni Visual Digital',
                    '780' => 'Musik & Komposisi Musikal',
                    '790' => 'Olahraga, Permainan & Seni Pertunjukan'
                ]
            ],
            '800' => [
                'name' => 'Kesusasteraan & Sastra',
                'icon' => 'fa-feather-pointed',
                'color' => '#65a30d',
                'divisions' => [
                    '800' => 'Sastra Umum, Teori Sastra & Retorika',
                    '810' => 'Sastra Amerika',
                    '820' => 'Sastra Inggris & Klasik Anglo-Saxon',
                    '830' => 'Sastra Jerman & Negara Berbahasa Jerman',
                    '840' => 'Sastra Prancis & Francophone',
                    '850' => 'Sastra Italia',
                    '860' => 'Sastra Spanyol & Portugis',
                    '870' => 'Sastra Latin Klasik',
                    '880' => 'Sastra Yunani Klasik',
                    '890' => 'Sastra Indonesia, Sastra Nusantara & Sastra Dunia Lain'
                ]
            ],
            '900' => [
                'name' => 'Sejarah & Geografi',
                'icon' => 'fa-earth-asia',
                'color' => '#dc2626',
                'divisions' => [
                    '900' => 'Sejarah, Geografi & Perjalanan Dunia',
                    '910' => 'Geografi, Ekspedisi & Wisata Penjelajahan',
                    '920' => 'Biografi, Otobiografi & Genealogi Tokoh',
                    '930' => 'Sejarah Dunia Kuno (Hingga ~499 M)',
                    '940' => 'Sejarah Umum Eropa & Dunia Barat',
                    '950' => 'Sejarah Umum Asia & Timur Tengah',
                    '960' => 'Sejarah Umum Afrika',
                    '970' => 'Sejarah Umum Amerika Utara',
                    '980' => 'Sejarah Umum Amerika Selatan & Latin',
                    '990' => 'Sejarah Kawasan Lain, Pasifik & Oseania'
                ]
            ]
        ];

        // Hitung statistik aktual dari tabel catalogs
        $rows = $this->db->table('catalogs')
            ->select("DeweyNo, COUNT(*) as cnt")
            ->where('IsOPAC', 1)
            ->where("DeweyNo IS NOT NULL AND DeweyNo != '' AND DeweyNo != 'NONE'", null, false)
            ->groupBy('DeweyNo')
            ->orderBy('cnt', 'DESC')
            ->get()
            ->getResultArray();

        $mainClassCounts  = array_fill_keys(array_keys($ddcStructure), 0);
        $divisionCounts   = [];
        $divisionSections = [];

        foreach ($ddcStructure as $mKey => $mVal) {
            foreach ($mVal['divisions'] as $dKey => $dName) {
                $divisionCounts[$dKey]   = 0;
                $divisionSections[$dKey] = [];
            }
        }

        foreach ($rows as $r) {
            $dewey = trim($r['DeweyNo']);
            $count = (int)$r['cnt'];
            if (!preg_match('/^[0-9]/', $dewey)) continue;

            $firstDigit = substr($dewey, 0, 1);
            $mainKey    = $firstDigit . '00';

            if (isset($mainClassCounts[$mainKey])) {
                $mainClassCounts[$mainKey] += $count;
            }

            if (preg_match('/^[0-9]{2}/', $dewey)) {
                $twoDigits = substr($dewey, 0, 2);
                $divKey    = $twoDigits . '0';

                if (isset($divisionCounts[$divKey])) {
                    $divisionCounts[$divKey] += $count;
                    if (count($divisionSections[$divKey]) < 6) {
                        $cleanSec = substr(explode(' ', explode('.', $dewey)[0])[0], 0, 3);
                        if (!empty($cleanSec)) {
                            if (!isset($divisionSections[$divKey][$cleanSec])) {
                                $divisionSections[$divKey][$cleanSec] = $count;
                            } else {
                                $divisionSections[$divKey][$cleanSec] += $count;
                            }
                        }
                    }
                }
            }
        }

        // Susun 10 divisi terpopuler
        $sortedDivisions = [];
        foreach ($ddcStructure as $mKey => $mVal) {
            foreach ($mVal['divisions'] as $dKey => $dName) {
                $cnt = $divisionCounts[$dKey] ?? 0;
                if ($cnt > 0) {
                    $sortedDivisions[] = [
                        'code'  => $dKey,
                        'name'  => $dName,
                        'count' => $cnt,
                        'class' => $mKey
                    ];
                }
            }
        }
        usort($sortedDivisions, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        $topDivisions = array_slice($sortedDivisions, 0, 12);

        // Susun struktur hierarki 000-900 urut
        $hierarchy = [];
        foreach ($ddcStructure as $mKey => $mVal) {
            $divList = [];
            foreach ($mVal['divisions'] as $dKey => $dName) {
                $secPills = [];
                if (!empty($divisionSections[$dKey])) {
                    arsort($divisionSections[$dKey]);
                    foreach (array_slice($divisionSections[$dKey], 0, 3, true) as $sCode => $sCnt) {
                        $secPills[] = ['code' => $sCode, 'count' => $sCnt];
                    }
                }

                $divList[] = [
                    'code'     => $dKey,
                    'name'     => $dName,
                    'count'    => $divisionCounts[$dKey] ?? 0,
                    'sections' => $secPills
                ];
            }

            $hierarchy[$mKey] = [
                'code'      => $mKey,
                'name'      => $mVal['name'],
                'icon'      => $mVal['icon'],
                'color'     => $mVal['color'],
                'count'     => $mainClassCounts[$mKey] ?? 0,
                'divisions' => $divList
            ];
        }

        $cacheData = [
            'ddc_hierarchy'     => $hierarchy,
            'ddc_top_divisions' => $topDivisions,
            'ddc_class_counts'  => $mainClassCounts,
            'total_ddc'         => array_sum($mainClassCounts)
        ];

        cache()->save($cacheKey, $cacheData, 600);
    }

    return $cacheData;
}

/**
 * Halaman browse/jelajah DDC dialihkan ke direktori subjek berstandar DDC (type=subject)
 */
public function ddc()
{
    $queryString = $this->request->getUri()->getQuery();
    $targetUrl   = base_url('opac/browse?type=subject' . ($queryString ? '&' . $queryString : ''));
    return redirect()->to($targetUrl);
}

/**
 * Halaman browse/jelajah subjek terintegrasi ke dalam browse katalog (type=subject)
 * Mengalihkan secara otomatis ke direktori subjek dalam halaman Browse
 */
public function subjek()
{
    $queryString = $this->request->getUri()->getQuery();
    $targetUrl   = base_url('opac/browse?type=subject' . ($queryString ? '&' . $queryString : ''));
    return redirect()->to($targetUrl);
}

    public function export()
    {
        $format = $this->request->getVar('format') ?? 'excel';
        $search = $this->request->getVar('search');

        $builder = $this->katalogModel->select('catalogs.*');

        if ($search) {
            $builder->groupStart()
                ->like('Title', $search)
                ->orLike('Author', $search)
                ->orLike('Subject', $search)
                ->groupEnd();
        }

        $catalogs = $builder->findAll();

        if ($format === 'excel') {
            return $this->exportToExcel($catalogs);
        } else {
            return $this->exportToCSV($catalogs);
        }
    }

    private function exportToExcel($catalogs)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header
        $headers = [
            'A1' => 'ID',
            'B1' => 'Control Number',
            'C1' => 'BIBID',
            'D1' => 'Title',
            'E1' => 'Author',
            'F1' => 'Edition',
            'G1' => 'Publisher',
            'H1' => 'Publish Location',
            'I1' => 'Publish Year',
            'J1' => 'Subject',
            'K1' => 'Physical Description',
            'L1' => 'ISBN',
            'M1' => 'Call Number',
            'N1' => 'Languages'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Data
        $row = 2;
        foreach ($catalogs as $catalog) {
            $sheet->setCellValue('A' . $row, $catalog['ID']);
            $sheet->setCellValue('B' . $row, $catalog['ControlNumber']);
            $sheet->setCellValue('C' . $row, $catalog['BIBID']);
            $sheet->setCellValue('D' . $row, $catalog['Title']);
            $sheet->setCellValue('E' . $row, $catalog['Author']);
            $sheet->setCellValue('F' . $row, $catalog['Edition']);
            $sheet->setCellValue('G' . $row, $catalog['Publisher']);
            $sheet->setCellValue('H' . $row, $catalog['PublishLocation']);
            $sheet->setCellValue('I' . $row, $catalog['PublishYear']);
            $sheet->setCellValue('J' . $row, $catalog['Subject']);
            $sheet->setCellValue('K' . $row, $catalog['PhysicalDescription']);
            $sheet->setCellValue('L' . $row, $catalog['ISBN']);
            $sheet->setCellValue('M' . $row, $catalog['CallNumber']);
            $sheet->setCellValue('N' . $row, $catalog['Languages']);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);

        $filename = 'catalog_export_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function exportToCSV($catalogs)
    {
        $filename = 'catalog_export_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header
        fputcsv($output, [
            'ID',
            'Control Number',
            'BIBID',
            'Title',
            'Author',
            'Edition',
            'Publisher',
            'Publish Location',
            'Publish Year',
            'Subject',
            'Physical Description',
            'ISBN',
            'Call Number',
            'Languages'
        ]);

        // Data
        foreach ($catalogs as $catalog) {
            fputcsv($output, [
                $catalog['ID'],
                $catalog['ControlNumber'],
                $catalog['BIBID'],
                $catalog['Title'],
                $catalog['Author'],
                $catalog['Edition'],
                $catalog['Publisher'],
                $catalog['PublishLocation'],
                $catalog['PublishYear'],
                $catalog['Subject'],
                $catalog['PhysicalDescription'],
                $catalog['ISBN'],
                $catalog['CallNumber'],
                $catalog['Languages']
            ]);
        }

        fclose($output);
        exit;
    }

    public function statistics()
    {
        $this->data['title'] = 'Statistik Katalog';

        // Total katalog
        $this->data['total_catalogs'] = $this->katalogModel->countAll();

        // Katalog per tahun
        $this->data['by_year'] = $this->katalogModel
        ->select('PublishYear, COUNT(*) as total')
        ->where("PublishYear REGEXP '^[0-9]{4}$'")
        ->groupBy('PublishYear')
        ->orderBy('PublishYear', 'DESC')
        ->findAll();


        // Katalog per bahasa
        $this->data['by_language'] = $this->katalogModel
            ->select('Languages, COUNT(*) as total')
            ->where('Languages IS NOT NULL')
            ->where('Languages !=', '')
            ->groupBy('Languages')
            ->orderBy('total', 'DESC')
            ->findAll();

        // Katalog per penerbit (top 10)
        $builder = $this->db->table('catalogs');
        $this->data['by_publisher'] = $builder
            ->select('Publisher, COUNT(*) as total')
            ->where('Publisher IS NOT NULL')
            ->where('Publisher !=', '')
            ->groupBy('Publisher')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->getResult(); // atau getResultArray();


        return view('Opac\Views\statistics', $this->data);
    }

    public function statistics_anggota()
    {
        $this->data['title'] = 'Statistik Anggota';

        // 1. Total anggota
        $this->data['total_members'] = $this->memberModel
            ->where('active', 1)
            ->countAllResults();

        // 2. Anggota aktif (IsOnlineActive = 1)
        $this->data['active_members'] = $this->memberModel
            ->where('active', 1)
            ->where('IsOnlineActive', 1)
            ->countAllResults();

        // 3. Anggota baru bulan ini
        $this->data['new_members_this_month'] = $this->memberModel
            ->where('active', 1)
            ->where('MONTH(RegisterDate)', date('m'))
            ->where('YEAR(RegisterDate)', date('Y'))
            ->countAllResults();

        // 4. Distribusi berdasarkan jenis kelamin
        $this->data['by_gender'] = $this->db->query("
            SELECT 
                CASE 
                    WHEN Sex_id = 1 THEN 'Laki-laki'
                    WHEN Sex_id = 2 THEN 'Perempuan'
                    ELSE 'Tidak Diketahui'
                END as gender,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            GROUP BY Sex_id
            ORDER BY total DESC
        ")->getResult();

        // 5. Distribusi berdasarkan jenjang pendidikan
        $this->data['by_education'] = $this->db->query("
            SELECT 
                mp.Nama as education_level,
                COUNT(m.ID) as total
            FROM members m
            LEFT JOIN master_pendidikan mp ON m.EducationLevel_id = mp.id
            WHERE m.active = 1
            GROUP BY m.EducationLevel_id, mp.Nama
            ORDER BY total DESC
            LIMIT 10
        ")->getResult();

        // 6. Distribusi berdasarkan pekerjaan
        // Di Controller (Bagian 6)
        $this->data['by_job'] = $this->db->query("
            SELECT 
                COALESCE(mpk.Pekerjaan, 'Lainnya/Tidak Diisi') as job_name,
                COUNT(m.ID) as total
            FROM members m
            LEFT JOIN master_pekerjaan mpk ON m.Job_id = mpk.id
            WHERE m.active = 1
            GROUP BY m.Job_id, mpk.Pekerjaan
            ORDER BY total DESC
            LIMIT 10
        ")->getResult();
    
       

        // 7. Registrasi anggota per bulan (12 bulan terakhir)
        $this->data['by_month'] = $this->db->query("
            SELECT 
                DATE_FORMAT(RegisterDate, '%Y-%m') as month,
                DATE_FORMAT(RegisterDate, '%b %Y') as month_name,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            AND RegisterDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(RegisterDate, '%Y-%m'), DATE_FORMAT(RegisterDate, '%b %Y')
            ORDER BY month DESC
        ")->getResult();

        // 8. Distribusi berdasarkan tahun registrasi
        $this->data['by_year'] = $this->db->query("
            SELECT 
                YEAR(RegisterDate) as year,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            AND RegisterDate IS NOT NULL
            GROUP BY YEAR(RegisterDate)
            ORDER BY year DESC
            LIMIT 10
        ")->getResult();

        // 9. Distribusi berdasarkan rentang usia
        $this->data['by_age_range'] = $this->db->query("
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) < 13 THEN '< 13 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 18 AND 25 THEN '18-25 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 26 AND 35 THEN '26-35 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 36 AND 45 THEN '36-45 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) BETWEEN 46 AND 55 THEN '46-55 tahun'
                    WHEN TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE()) > 55 THEN '> 55 tahun'
                    ELSE 'Tidak Diketahui'
                END as age_range,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            AND DateOfBirth IS NOT NULL
            GROUP BY age_range
            ORDER BY 
                CASE age_range
                    WHEN '< 13 tahun' THEN 1
                    WHEN '13-17 tahun' THEN 2
                    WHEN '18-25 tahun' THEN 3
                    WHEN '26-35 tahun' THEN 4
                    WHEN '36-45 tahun' THEN 5
                    WHEN '46-55 tahun' THEN 6
                    WHEN '> 55 tahun' THEN 7
                    ELSE 8
                END
        ")->getResult();

        // 10. Distribusi berdasarkan kota/provinsi (top 10)
        $this->data['by_province'] = $this->db->query("
            SELECT 
                COALESCE(Province, 'Tidak Diketahui') as province,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            GROUP BY Province
            ORDER BY total DESC
            LIMIT 10
        ")->getResult();

        // 11. Distribusi berdasarkan status perkawinan
        $this->data['by_marital_status'] = $this->db->query("
            SELECT 
                CASE 
                    WHEN MaritalStatus_id = 1 THEN 'Belum Menikah'
                    WHEN MaritalStatus_id = 2 THEN 'Menikah'
                    WHEN MaritalStatus_id = 3 THEN 'Cerai'
                    ELSE 'Tidak Diketahui'
                END as marital_status,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            GROUP BY MaritalStatus_id
            ORDER BY total DESC
        ")->getResult();

        // 12. Anggota dengan keterlambatan pengembalian
        $this->data['late_return_stats'] = $this->db->query("
            SELECT 
                CASE 
                    WHEN LoanReturnLateCount = 0 THEN 'Tidak Pernah'
                    WHEN LoanReturnLateCount BETWEEN 1 AND 3 THEN '1-3 kali'
                    WHEN LoanReturnLateCount BETWEEN 4 AND 6 THEN '4-6 kali'
                    WHEN LoanReturnLateCount > 6 THEN '> 6 kali'
                END as late_category,
                COUNT(*) as total
            FROM members
            WHERE active = 1
            GROUP BY late_category
            ORDER BY 
                CASE late_category
                    WHEN 'Tidak Pernah' THEN 1
                    WHEN '1-3 kali' THEN 2
                    WHEN '4-6 kali' THEN 3
                    WHEN '> 6 kali' THEN 4
                END
        ")->getResult();

        // 13. Distribusi berdasarkan jenis identitas
        $this->data['by_identity_type'] = $this->db->query("
            SELECT 
                mji.Nama as identity_type,
                COUNT(m.ID) as total
            FROM members m
            LEFT JOIN master_jenis_identitas mji ON m.IdentityType_id = mji.id
            WHERE m.active = 1
            GROUP BY m.IdentityType_id, mji.Nama
            ORDER BY total DESC
        ")->getResult();

        // 14. Statistik tambahan
        $this->data['avg_age'] = $this->db->query("
            SELECT 
                ROUND(AVG(TIMESTAMPDIFF(YEAR, DateOfBirth, CURDATE())), 1) as avg_age
            FROM members
            WHERE active = 1 
            AND DateOfBirth IS NOT NULL
        ")->getRow()->avg_age ?? 0;

        // 15. Anggota terdaftar hari ini
        $this->data['today_registrations'] = $this->memberModel
            ->where('active', 1)
            ->where('DATE(RegisterDate)', date('Y-m-d'))
            ->countAllResults();

        // 16. Growth rate (perbandingan dengan tahun lalu)
        $currentYearMembers = $this->memberModel
            ->where('active', 1)
            ->where('YEAR(RegisterDate)', date('Y'))
            ->countAllResults();

        $lastYearMembers = $this->memberModel
            ->where('active', 1)
            ->where('YEAR(RegisterDate)', date('Y') - 1)
            ->countAllResults();

        $this->data['growth_rate'] = $lastYearMembers > 0 
            ? (($currentYearMembers - $lastYearMembers) / $lastYearMembers) * 100 
            : 0;

        return view('Opac\Views\statistics_anggota', $this->data);
    }  


    public function downloadMarcUtf8($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $content = "=LDR  00000nam  2200000   4500\n";

        foreach ($marc as $field) {
            $tag = str_pad($field->Tag, 3, '0', STR_PAD_LEFT);
            $ind1 = $field->Indicator1 ?: ' ';
            $ind2 = $field->Indicator2 ?: ' ';
            $value = $field->Value;

            if (intval($tag) < 10) {
                // Control fields
                $content .= "={$tag}  {$value}\n";
            } else {
                // Data fields
                $content .= "={$tag}  {$ind1}{$ind2}\${$value}\n";
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_UTF8.txt';

        return $this->response
            ->setContentType('text/plain; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($content);
    }

    /**
     * Download MARC in XML format
     */
    public function downloadMarcXml($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        // Root element
        $collection = $xml->createElement('collection');
        $collection->setAttribute('xmlns', 'http://www.loc.gov/MARC21/slim');
        $xml->appendChild($collection);

        // Record element
        $record = $xml->createElement('record');
        $collection->appendChild($record);

        // Leader
        $leader = $xml->createElement('leader', '00000nam  2200000   4500');
        $record->appendChild($leader);

        foreach ($marc as $field) {
            $tag = str_pad($field->Tag, 3, '0', STR_PAD_LEFT);

            if (intval($tag) < 10) {
                // Control field
                $controlfield = $xml->createElement('controlfield', htmlspecialchars($field->Value));
                $controlfield->setAttribute('tag', $tag);
                $record->appendChild($controlfield);
            } else {
                // Data field
                $datafield = $xml->createElement('datafield');
                $datafield->setAttribute('tag', $tag);
                $datafield->setAttribute('ind1', $field->Indicator1 ?: ' ');
                $datafield->setAttribute('ind2', $field->Indicator2 ?: ' ');

                // Parse subfields
                $subfields = $this->parseSubfields($field->Value);
                foreach ($subfields as $code => $value) {
                    $subfield = $xml->createElement('subfield', htmlspecialchars($value));
                    $subfield->setAttribute('code', $code);
                    $datafield->appendChild($subfield);
                }

                $record->appendChild($datafield);
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_XML.xml';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Download in MODS format
     */
    public function downloadMarcMods($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $mods = $xml->createElement('mods');
        $mods->setAttribute('xmlns', 'http://www.loc.gov/mods/v3');
        $mods->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $mods->setAttribute('xsi:schemaLocation', 'http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-7.xsd');
        $xml->appendChild($mods);

        foreach ($marc as $field) {
            $tag = $field->Tag;
            $value = $field->Value;

            switch ($tag) {
                case '245': // Title
                    $titleInfo = $xml->createElement('titleInfo');
                    $title = $xml->createElement('title', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $titleInfo->appendChild($title);
                    $subtitle = $this->extractSubfield($value, 'b');
                    if ($subtitle) {
                        $subTitle = $xml->createElement('subTitle', htmlspecialchars($subtitle));
                        $titleInfo->appendChild($subTitle);
                    }
                    $mods->appendChild($titleInfo);
                    break;

                case '100': // Author
                    $name = $xml->createElement('name');
                    $name->setAttribute('type', 'personal');
                    $namePart = $xml->createElement('namePart', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $name->appendChild($namePart);
                    $role = $xml->createElement('role');
                    $roleTerm = $xml->createElement('roleTerm', 'author');
                    $roleTerm->setAttribute('type', 'text');
                    $role->appendChild($roleTerm);
                    $name->appendChild($role);
                    $mods->appendChild($name);
                    break;

                case '260': // Publication info
                    $originInfo = $xml->createElement('originInfo');
                    $publisher = $this->extractSubfield($value, 'b');
                    if ($publisher) {
                        $pub = $xml->createElement('publisher', htmlspecialchars($publisher));
                        $originInfo->appendChild($pub);
                    }
                    $dateIssued = $this->extractSubfield($value, 'c');
                    if ($dateIssued) {
                        $date = $xml->createElement('dateIssued', htmlspecialchars($dateIssued));
                        $originInfo->appendChild($date);
                    }
                    $place = $this->extractSubfield($value, 'a');
                    if ($place) {
                        $placeTerm = $xml->createElement('placeTerm', htmlspecialchars($place));
                        $placeTerm->setAttribute('type', 'text');
                        $placeElement = $xml->createElement('place');
                        $placeElement->appendChild($placeTerm);
                        $originInfo->appendChild($placeElement);
                    }
                    $mods->appendChild($originInfo);
                    break;

                case '650': // Subject
                    $subject = $xml->createElement('subject');
                    $topic = $xml->createElement('topic', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $subject->appendChild($topic);
                    $mods->appendChild($subject);
                    break;
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_MODS.xml';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Download Dublin Core RDF format
     */
    public function downloadMarcRdf($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $rdf = $xml->createElement('rdf:RDF');
        $rdf->setAttribute('xmlns:rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
        $rdf->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $xml->appendChild($rdf);

        $description = $xml->createElement('rdf:Description');
        $description->setAttribute('rdf:about', 'http://example.com/catalog/' . $catalog['ID']);
        $rdf->appendChild($description);

        foreach ($marc as $field) {
            $tag = $field->Tag;
            $value = $field->Value;

            switch ($tag) {
                case '245': // Title
                    $title = $xml->createElement('dc:title', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $description->appendChild($title);
                    break;

                case '100': // Creator
                    $creator = $xml->createElement('dc:creator', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $description->appendChild($creator);
                    break;

                case '260': // Publisher
                    $publisher = $this->extractSubfield($value, 'b');
                    if ($publisher) {
                        $pub = $xml->createElement('dc:publisher', htmlspecialchars($publisher));
                        $description->appendChild($pub);
                    }
                    $date = $this->extractSubfield($value, 'c');
                    if ($date) {
                        $dateEl = $xml->createElement('dc:date', htmlspecialchars($date));
                        $description->appendChild($dateEl);
                    }
                    break;

                case '650': // Subject
                    $subject = $xml->createElement('dc:subject', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $description->appendChild($subject);
                    break;
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_DC_RDF.xml';

        return $this->response
            ->setContentType('application/rdf+xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Download Dublin Core OAI format
     */
    public function downloadMarcOai($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $oai_dc = $xml->createElement('oai_dc:dc');
        $oai_dc->setAttribute('xmlns:oai_dc', 'http://www.openarchives.org/OAI/2.0/oai_dc/');
        $oai_dc->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $oai_dc->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $oai_dc->setAttribute('xsi:schemaLocation', 'http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd');
        $xml->appendChild($oai_dc);

        foreach ($marc as $field) {
            $tag = $field->Tag;
            $value = $field->Value;

            switch ($tag) {
                case '245': // Title
                    $title = $xml->createElement('dc:title', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $oai_dc->appendChild($title);
                    break;

                case '100': // Creator
                    $creator = $xml->createElement('dc:creator', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $oai_dc->appendChild($creator);
                    break;

                case '260': // Publisher and Date
                    $publisher = $this->extractSubfield($value, 'b');
                    if ($publisher) {
                        $pub = $xml->createElement('dc:publisher', htmlspecialchars($publisher));
                        $oai_dc->appendChild($pub);
                    }
                    $date = $this->extractSubfield($value, 'c');
                    if ($date) {
                        $dateEl = $xml->createElement('dc:date', htmlspecialchars($date));
                        $oai_dc->appendChild($dateEl);
                    }
                    break;

                case '650': // Subject
                    $subject = $xml->createElement('dc:subject', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $oai_dc->appendChild($subject);
                    break;
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_DC_OAI.xml';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Download Dublin Core SRW format
     */
    public function downloadMarcSrw($id)
    {
        $catalog = $this->katalogModel->asArray()->find($id);

        if (!$catalog) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Katalog tidak ditemukan');
        }

        $marc = $this->katalogRuasModel
            ->select('*')
            ->where('CatalogId', $id)
            ->orderBy('Sequence', 'ASC')
            ->findAll();

        if (empty($marc)) {
            return redirect()->back()->with('error', 'Data MARC tidak tersedia');
        }

        $xml = new \DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;

        $srw_dc = $xml->createElement('srw_dc:dc');
        $srw_dc->setAttribute('xmlns:srw_dc', 'info:srw/schema/1/dc-schema');
        $srw_dc->setAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $xml->appendChild($srw_dc);

        foreach ($marc as $field) {
            $tag = $field->Tag;
            $value = $field->Value;

            switch ($tag) {
                case '245': // Title
                    $title = $xml->createElement('dc:title', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $srw_dc->appendChild($title);
                    break;

                case '100': // Creator
                    $creator = $xml->createElement('dc:creator', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $srw_dc->appendChild($creator);
                    break;

                case '260': // Publisher and Date
                    $publisher = $this->extractSubfield($value, 'b');
                    if ($publisher) {
                        $pub = $xml->createElement('dc:publisher', htmlspecialchars($publisher));
                        $srw_dc->appendChild($pub);
                    }
                    $date = $this->extractSubfield($value, 'c');
                    if ($date) {
                        $dateEl = $xml->createElement('dc:date', htmlspecialchars($date));
                        $srw_dc->appendChild($dateEl);
                    }
                    break;

                case '650': // Subject
                    $subject = $xml->createElement('dc:subject', htmlspecialchars($this->extractSubfield($value, 'a')));
                    $srw_dc->appendChild($subject);
                    break;
            }
        }

        $filename = 'MARC_' . $catalog['ID'] . '_DC_SRW.xml';

        return $this->response
            ->setContentType('application/xml; charset=utf-8')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setBody($xml->saveXML());
    }

    /**
     * Helper method to parse subfields
     */
    private function parseSubfields($value)
    {
        $subfields = [];
        $parts = explode('$', $value);

        foreach ($parts as $part) {
            if (strlen($part) >= 2) {
                $code = substr($part, 0, 1);
                $text = substr($part, 1);
                $subfields[$code] = trim($text);
            }
        }

        return $subfields;
    }

    /**
     * Helper method to extract specific subfield
     */
    private function extractSubfield($value, $subfieldCode)
    {
        $subfields = $this->parseSubfields($value);
        return isset($subfields[$subfieldCode]) ? $subfields[$subfieldCode] : '';
    }

    /**
     * Apply FULLTEXT search to query builder, fallback to LIKE when term is too short.
     * Requires FULLTEXT indexes on catalogs table — see app/Database/opac_fulltext_indexes.sql
     */
    private function applyFulltextSearch($builder, string $search, string $searchBy, ?string $rawSearch = null): void
    {
        $rawSearch = $rawSearch ?? $search;

        $builder->groupStart();
        switch ($searchBy) {
            case 'Title':
                $term = $this->toFtTerm($search);
                $term ? $builder->where("MATCH(Title) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Title', $search);
                break;

            case 'Author':
                $authors = array_filter(array_map('trim', preg_split('/[;,]+/', $rawSearch)));
                $first   = true;
                foreach ($authors as $author) {
                    $term = $this->toFtTerm(sanitizeSearch($author));
                    if ($first) {
                        $term ? $builder->where("MATCH(Author) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                              : $builder->like('Author', sanitizeSearch($author));
                    } else {
                        $term ? $builder->orWhere("MATCH(Author) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                              : $builder->orLike('Author', sanitizeSearch($author));
                    }
                    $first = false;
                }
                break;

            case 'Subject':
                $term = $this->toFtTerm($search);
                $cleanLike = trim(preg_replace('/[+><\~*"@\\\\\'`]/', '', $rawSearch));
                if ($term) {
                    $builder->groupStart()
                            ->where("MATCH(Subject) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                            ->orLike('Subject', $cleanLike ?: $search)
                            ->groupEnd();
                } else {
                    $builder->like('Subject', $cleanLike ?: $search);
                }
                break;

            case 'ISBN':
                $builder->like('ISBN', $search);
                break;

            case 'DeweyNo':
                $cleanDewey = sanitizeSearch($rawSearch);
                if (strlen($cleanDewey) === 3 && substr($cleanDewey, -1) === '0') {
                    $prefix = substr($cleanDewey, 0, 2);
                    $builder->where("(DeweyNo LIKE '{$cleanDewey}%' OR DeweyNo LIKE '{$prefix}%')", null, false);
                } else {
                    $builder->like('DeweyNo', $cleanDewey, 'after');
                }
                break;

            case 'Publisher':
                $term = $this->toFtTerm($search);
                $term ? $builder->where("MATCH(Publisher) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                      : $builder->like('Publisher', $search);
                break;

            default: // semua kolom
                $term = $this->toFtTerm($search);
                if ($term) {
                    $builder->where("MATCH(Title, Author, Subject, Publisher) AGAINST ('{$term}' IN BOOLEAN MODE)", null, false)
                            ->orLike('ISBN', $search);
                } else {
                    $builder->orLike('Title', $search)
                            ->orLike('Author', $rawSearch)
                            ->orLike('Subject', $search)
                            ->orLike('ISBN', $search)
                            ->orLike('Publisher', $search);
                }
        }
        $builder->groupEnd();
    }

    /**
     * Convert a sanitized search string to MySQL FULLTEXT Boolean mode term.
     * Returns empty string when all words are too short (falls back to LIKE).
     * Input sudah melalui sanitizeSearch() sehingga karakter berbahaya sudah dihapus.
     */
    private function toFtTerm(string $search): string
    {
        // Ganti tanda hubung, underscore, garis miring, kurung, dan tanda baca pemisah kata dengan spasi
        // agar kata majemuk seperti 'Perundang-undangan' menjadi 'perundang undangan' (sesuai tokenisasi MySQL FULLTEXT)
        $search = preg_replace('/[\-_\/.,;:&()]/', ' ', $search);

        // Hapus operator FULLTEXT Boolean dan single quote sebagai lapisan keamanan tambahan
        $search = preg_replace('/[+><\~*"@\'\\\\]/', '', $search);
        $words  = preg_split('/\s+/', trim($search), -1, PREG_SPLIT_NO_EMPTY);
        $terms  = [];
        foreach ($words as $word) {
            // Token < 3 karakter diabaikan dari prefiks '+' agar tidak menyebabkan 0 hasil pada MySQL InnoDB boolean mode
            if (mb_strlen($word) < 3) continue;
            $terms[] = '+' . $word . '*';
        }
        return implode(' ', $terms);
    }

    public function bacaDigital($catalog_id){
    
        helper('reference');

        $file = $this->fileModel->where('Catalog_id', $catalog_id)->first();
      
      
        if (!$file) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File digital tidak ditemukan');
        }

        $nomor_anggota = user()->username;
        $member        = $this->memberModel->where('MemberNo', $nomor_anggota)->first();
        $member_id     = $member ? (int) $member->ID : 0;

        if ($member_id > 0) {
            $collection = $this->db->table('collections')
                ->where('Catalog_id', $catalog_id)
                ->where('ISDRM', 1)
                ->get()
                ->getRow();

            if ($collection) {
                $loanItem = $this->db->table('collectionloanitems')
                    ->where('Collection_id', $collection->ID)
                    ->where('member_id', $member_id)
                    ->where('LoanStatus', 'Loan')
                    ->get()
                    ->getRow();

                if ($loanItem) {
                    if (strtotime($loanItem->DueDate) < time()) {
                        $this->_autoReturnDigitalLoan($loanItem, $collection->ID);
                        return redirect()
                            ->to(base_url('opac/detail/' . $catalog_id))
                            ->with('digital_error', 'Masa peminjaman digital telah berakhir. Koleksi dikembalikan otomatis. Silakan pinjam kembali.');
                    }
                    // Loan masih aktif – langsung izinkan baca
                } else {
                    // Tidak punya loan aktif – cek apakah koleksi masih tersedia
                    if ((int) $collection->Status_id !== 1) {
                        return redirect()
                            ->to(base_url('opac/detail/' . $catalog_id))
                            ->with('digital_info', 'Koleksi digital ini sedang dipinjam oleh anggota lain. Silakan coba lagi nanti.');
                    }
                    // Koleksi tersedia – catat peminjaman baru
                    try {
                        $this->_recordDigitalLoan((int) $catalog_id, $member_id);
                    } catch (\Exception $e) {
                        log_message('error', 'Digital loan recording error: ' . $e->getMessage());
                    }
                }
            }
        }
         
        return redirect()->to(base_url('katalog/view_decrypted/' . encData($file->ID)));
    }

    private function _autoReturnDigitalLoan($loanItem, int $collection_id)
{
    // Hanya proses auto-return untuk koleksi digital (ISDRM = 1)
    $collection = $this->db->table('collections')
        ->select('ID, ISDRM, Status_id')
        ->where('ID', $collection_id)
        ->get()
        ->getRow();

    if (!$collection || (int) $collection->ISDRM !== 1) {
        return; // bukan koleksi digital, tidak perlu auto-return
    }

    $now = date('Y-m-d H:i:s');
    $ip  = $this->request->getIPAddress();

    $this->db->transBegin();

    $this->db->table('collectionloanitems')
        ->where('ID', $loanItem->ID)
        ->update([
            'LoanStatus'     => 'Return',
            'ActualReturn'   => $now,
            'UpdateDate'     => $now,
            'UpdateTerminal' => $ip,
        ]);

    $loan = $this->db->table('collectionloans')
        ->where('ID', $loanItem->CollectionLoan_id)
        ->get()
        ->getRow();

    if ($loan) {
        $this->db->table('collectionloans')
            ->where('ID', $loanItem->CollectionLoan_id)
            ->update([
                'ReturnCount'    => (int) ($loan->ReturnCount ?? 0) + 1,
                'UpdateDate'     => $now,
                'UpdateTerminal' => $ip,
            ]);
    }

    $this->db->table('collections')
        ->where('ID', $collection_id)
        ->update([
            'Status_id'      => 1,
            'UpdateDate'     => $now,
            'UpdateTerminal' => $ip,
        ]);

    if ($this->db->transStatus() === false) {
        $this->db->transRollback();
    } else {
        $this->db->transCommit();
    }
}

    private function _recordDigitalLoan(int $catalog_id, int $member_id)
    {
        $collection = $this->db->table('collections')
            ->where('Catalog_id', $catalog_id)
            ->where('ISDRM', 1)
            ->get()
            ->getRow();

        if (!$collection) {
            return;
        }

        $existing = $this->db->table('collectionloanitems')
            ->where('Collection_id', $collection->ID)
            ->where('member_id', $member_id)
            ->where('LoanStatus', 'Loan')
            ->get()
            ->getRow();

        if ($existing) {
            return;
        }

        $member = $this->db->table('members as m')
            ->select('m.ID, m.Branch_id, ja.MaxLoanDays')
            ->join('jenis_anggota as ja', 'ja.ID = m.JenisAnggota_id', 'left')
            ->where('m.ID', $member_id)
            ->get()
            ->getRow();

        if (!$member) {
            return;
        }

        $loanDays = (int) ($member->MaxLoanDays ?? 7);
        $last_loan = get_ref_single('collectionloans', 'ID IS NOT NULL', 'data');
        $lastNumber = $last_loan ? (int) substr($last_loan->ID, -5) : 0;
        $loan_id = get_pad_number($lastNumber + 1, date('ymd'), 5);

        $now       = date('Y-m-d H:i:s');
        $dueDate   = date('Y-m-d H:i:s', strtotime("+{$loanDays} days"));
        $createdBy = session()->get('logged_in') ?? 0;
        $ip        = $this->request->getIPAddress();

        $this->db->transBegin();

        $this->db->table('collectionloans')->insert([
            'ID'                => $loan_id,
            'CollectionCount'   => 1,
            'LateCount'         => 0,
            'ExtendCount'       => 0,
            'LoanCount'         => 1,
            'ReturnCount'       => 0,
            'Member_id'         => $member_id,
            'LocationLibrary_id'=> $collection->Location_Library_id ?? null,
            'Branch_id'         => $member->Branch_id,
            'CreateBy'          => $createdBy,
            'CreateDate'        => $now,
            'CreateTerminal'    => $ip,
        ]);

        $this->db->table('collectionloanitems')->insert([
            'CollectionLoan_id' => $loan_id,
            'LoanDate'          => $now,
            'DueDate'           => $dueDate,
            'LoanStatus'        => 'Loan',
            'Collection_id'     => $collection->ID,
            'member_id'         => $member_id,
            'CreateBy'          => $createdBy,
            'CreateDate'        => $now,
            'CreateTerminal'    => $ip,
            'Branch_id'         => $member->Branch_id,
        ]);

        $this->db->table('collections')
            ->where('ID', $collection->ID)
            ->update([
                'Status_id'      => 5,
                'UpdateBy'       => $createdBy,
                'UpdateDate'     => $now,
                'UpdateTerminal' => $ip,
            ]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            throw new \Exception('Transaksi gagal saat mencatat peminjaman digital');
        }

        $this->db->transCommit();
    }
}
