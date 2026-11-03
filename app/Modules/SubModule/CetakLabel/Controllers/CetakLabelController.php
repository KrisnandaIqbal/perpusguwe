<?php
namespace CetakLabel\Controllers;

class CetakLabelController extends \Base\Controllers\BaseController
{
    public $eksemplarModel;
    public $katalogModel;
    public $db;

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->eksemplarModel = new \Eksemplar\Models\EksemplarModel();
        $this->katalogModel   = new \Katalog\Models\KatalogModel();
        $this->db             = \Config\Database::connect('data');

        helper(['reference', 'katalog', 'eksemplar']);
    }

    public function index()
    {
        $this->data['title'] = 'Cetak Label';
        return view('CetakLabel\Views\index', $this->data);
    }

    /**
     * Logic disalin persis dari Eksemplar\Controllers\EksemplarLabelController::print_label
     * Sengaja tidak duplikasi file template — tetap pakai Eksemplar\Views\template\*
     */
    public function print_label()
    {
        helper(['thumbnail', 'form']);
        $this->data['title'] = 'Cetak Label';

        $this->validation->setRules([
            'eksemplar_ids' => ['label' => 'Eksemplar',      'rules' => 'required'],
            'eksemplar_tpl' => ['label' => 'Template Label', 'rules' => 'required'],
        ]);

        if (!$this->request->getPost() || !$this->validation->withRequest($this->request)->run()) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',
                $this->validation->getErrors()
                    ? $this->validation->listErrors()
                    : 'Tidak ada eksemplar yang dipilih.'
            );
            return redirect()->back();
        }

        $post      = $this->request->getPost();
        $template  = $post['eksemplar_tpl'];
        $paperSize = $post['paper_size'] ?? 'a4';

        $idsArr = array_filter(
            array_map('intval', explode(',', preg_replace('/[^0-9,]/', '', $post['eksemplar_ids']))),
            fn($id) => $id > 0
        );

        if (empty($idsArr)) {
            $this->session->setFlashdata('swal_icon',  'warning');
            $this->session->setFlashdata('swal_title', 'Peringatan');
            $this->session->setFlashdata('swal_html',  'Tidak ada ID eksemplar yang valid.');
            return redirect()->back();
        }

        $allowedTemplates = [
            'cetak-label-a4-1', 'cetak-label-a4-2', 'cetak-label-a4-3',
            'cetak-label-a4-4', 'cetak-label-a4-5', 'cetak-label-a4-4-qrcode', 'cetak-label-xp420b-10x5',
            'cetak-label-lr1',  'cetak-label-lr2',  'cetak-label-lr3',
            'cetak-label-lr4',  'cetak-label-lr5',  'cetak-label-lr6',
            'cetak-label-br1',  'cetak-label-br2',
            'cetak-label-tj107-1',
            'cetak-label-tj121-1', 'cetak-label-tj121-2',
            'cetak-label-gc121-1', 'cetak-label-gc121-2',
            'cetak-label-gc121-3', 'cetak-label-gc121-4',
        ];

        if (!in_array($template, $allowedTemplates, true)) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',  'Template tidak dikenali: ' . esc($template));
            return redirect()->back();
        }

        $db = db_connect();
        $eksemplarData = $db->table('collections as a')
            ->select('a.ID, a.NomorBarcode, b.ID as biblio_id, b.Title, b.CallNumber, b.DeweyNo, b.Author, b.PublishYear')
            ->join('catalogs b', 'b.ID = a.Catalog_id')
            ->whereIn('a.ID', $idsArr)
            ->get()
            ->getResultObject();

        if (empty($eksemplarData)) {
            $this->session->setFlashdata('swal_icon',  'error');
            $this->session->setFlashdata('swal_title', 'Gagal');
            $this->session->setFlashdata('swal_html',  'Data eksemplar tidak ditemukan.');
            return redirect()->back();
        }

        $firstChars = array_values(array_unique(array_filter(
            array_map(fn($row) => strtoupper(substr((string) ($row->DeweyNo ?? ''), 0, 1)), $eksemplarData),
            fn($c) => $c !== ''
        )));

        $warnaMap = [];
        if (!empty($firstChars)) {
            $placeholders = implode(',', array_fill(0, count($firstChars), '?'));
            $kelasRows = $db->query(
                "SELECT KdKelas, Warna FROM master_kelas_besar WHERE LEFT(KdKelas, 1) IN ($placeholders)",
                $firstChars
            )->getResultArray();
            foreach ($kelasRows as $kelas) {
                $key = strtoupper(substr((string) $kelas['KdKelas'], 0, 1));
                if (!isset($warnaMap[$key])) {
                    $warnaMap[$key] = $kelas['Warna'];
                }
            }
        }

        $namaPerpustakaan = $db->table('settingparameters')
            ->where('Name', 'NamaPerpustakaan')
            ->get()
            ->getRow()
            ->Value ?? 'Perpustakaan Mitra';

        $useQrCode = str_contains($template, 'qrcode') || str_contains($paperSize, 'qrcode');

        $LabelData = [];
        $currentUser = user();
        foreach ($eksemplarData as $row) {
            $firstChar = strtoupper(substr((string)($row->DeweyNo ?? ''), 0, 1));
            $LabelData[] = [
                'Title'            => character_limiter($row->Title, 50),
                'Barcode'          => $row->NomorBarcode,
                'CallNumber'       => $row->CallNumber,
                'NamaPerpustakaan' => $namaPerpustakaan,
                'Warna1'           => $warnaMap[$firstChar] ?? '#FFFF66',
                'BarcodePNG'       => $useQrCode
                                        ? get_qrcode_png($row->NomorBarcode)
                                        : get_barcode_png($row->NomorBarcode),
            ];
        
            $db->table('item_label_print_log')->insert([
                'item_code'    => $row->NomorBarcode,
                'biblio_id'    => $row->biblio_id,
                'title'        => $row->Title,
                'author'       => $row->Author,
                'publish_year' => $row->PublishYear,
                'call_number'  => $row->CallNumber,
                'print_date'   => date('Y-m-d H:i:s'),
                'user_id'      => $currentUser->id ?? null,
                'username'     => $currentUser->username ?? null,
            ]);
        }

        return view('Eksemplar\Views\template\\' . $template, ['LabelData' => $LabelData]);
    }

    /**
     * Status cetak per barcode (untuk penanda di daftar Cetak Label).
     * GET ?codes=A,B,C -> {"A":{"n":2,"last":"2026-10-22 09:04:05"}}
     */
    public function status()
    {
        $codes = array_values(array_unique(array_filter(
            array_map('trim', explode(',', (string) $this->request->getGet('codes')))
        )));
        $codes = array_slice($codes, 0, 300);

        $out = [];
        if (!empty($codes)) {
            $rows = db_connect()->table('item_label_print_log')
                ->select('item_code, COUNT(*) AS n, MAX(print_date) AS last_print')
                ->whereIn('item_code', $codes)
                ->groupBy('item_code')
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $out[$r['item_code']] = ['n' => (int) $r['n'], 'last' => $r['last_print']];
            }
        }
        // gabungkan dengan riwayat cetak dari SLiMS (system_log)
        if (!empty($codes)) {
            $dbx = db_connect();
            $in  = implode(',', array_map(fn($c) => $dbx->escape($c), $codes));
            $sql = "SELECT SUBSTRING_INDEX(log_msg, '||', 1) AS item_code, COUNT(*) AS n, MAX(log_date) AS last_print
                    FROM `perpustakaan_kemnaker`.system_log
                    WHERE log_location = 'label_barcode' AND action = 'Label Print'
                      AND SUBSTRING_INDEX(log_msg, '||', 1) IN ($in)
                    GROUP BY item_code";
            try {
                foreach ($dbx->query($sql)->getResultArray() as $r) {
                    $c    = $r['item_code'];
                    $n    = (int) $r['n'];
                    $last = $r['last_print'];
                    if (isset($out[$c])) {
                        $n += $out[$c]['n'];
                        if ($out[$c]['last'] > $last) { $last = $out[$c]['last']; }
                    }
                    $out[$c] = ['n' => $n, 'last' => $last];
                }
            } catch (\Throwable $e) {
                // SLiMS tidak terbaca: abaikan, tetap tampilkan log INLISLite
            }
        }

        return $this->response->setJSON((object) $out);
    }
}
