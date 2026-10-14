<?php

namespace Eksemplar\Controllers;

/**
 * EksemplarFormController
 *
 * Menangani create dan edit eksemplar.
 *
 * CATATAN PERBAIKAN (lihat komentar // FIX: di masing-masing bagian):
 * 1. edit() sekarang bisa mengosongkan/reset field opsional (dulu memakai
 *    !empty() sehingga field yang dikosongkan user tidak pernah tersimpan).
 * 2. Price = 0 sekarang tersimpan dengan benar (dulu dianggap "kosong").
 * 3. generateNomorBarcode() dibungkus transaction + SELECT ... FOR UPDATE
 *    untuk mencegah race condition saat dua request create berjalan bersamaan.
 * 4. insertBatch() sekarang dibungkus transaction eksplisit.
 * 5. Pesan error DB mentah di edit() tidak lagi dibocorkan ke user; disamakan
 *    polanya dengan create() (pesan ramah + log_message untuk detail teknis).
 * 6. Pengecekan Catalog_id ganda (dead code, karena sudah divalidasi 'required')
 *    dihapus.
 * 7. JumlahEksemplar sekarang divalidasi sebagai integer dengan batas wajar.
 * 8. modalCreate() sekarang mengisi $data['locations'] (dulu undefined variable
 *    di view saat mode create).
 * 9. modalCreate()/modalEdit() memakai $this->db (bukan db_connect() terpisah)
 *    supaya konsisten dan tidak membuka koneksi DB kedua tanpa perlu.
 */
class EksemplarFormController extends \Base\Controllers\BaseController
{
    use EksemplarBase;

    // Batas jumlah eksemplar yang boleh dibuat dalam satu request.
    // FIX #7: mencegah nilai kosong/non-numeric/berlebihan di JumlahEksemplar.
    private const MAX_JUMLAH_EKSEMPLAR = 500;

    function __construct()
    {
        $this->initEksemplarBase();
    }

    // ----------------------------------------------------------------
    // CREATE
    // ----------------------------------------------------------------

    public function create()
    {
        $this->data['title'] = 'Tambah Eksemplar';
        $isAjax = $this->request->isAJAX();

        $NomorInduk = $this->db->table('settingparameters')->where('Name', 'NomorInduk')->get()->getRow()->Value ?: "Otomatis";
        $this->data['NomorInduk'] = ($NomorInduk == "Manual") ? "True" : "False";

        $this->validation->setRules([
            'Catalog_id'          => ['label' => 'Judul Katalog',       'rules' => 'required'],
            'Location_Library_id' => ['label' => 'Lokasi Perpustakaan', 'rules' => 'permit_empty'],
            'Location_id'         => ['label' => 'Lokasi Ruang',        'rules' => 'permit_empty'],
            'Source_id'           => ['label' => 'Sumber Pengadaan',    'rules' => 'permit_empty'],
            'Partner_id'          => ['label' => 'Nama Sumber',         'rules' => 'permit_empty'],
            'Media_id'            => ['label' => 'Bentuk Fisik',        'rules' => 'required'],
            'Category_id'         => ['label' => 'Kategori Koleksi',    'rules' => 'required'],
            'TanggalPengadaan'    => ['label' => 'Tanggal Pengadaan',   'rules' => 'permit_empty'],
            // FIX #7: validasi JumlahEksemplar sebagai integer 1..MAX_JUMLAH_EKSEMPLAR
            'JumlahEksemplar'     => [
                'label' => 'Jumlah Eksemplar',
                'rules' => 'required|is_natural_no_zero|less_than_equal_to[' . self::MAX_JUMLAH_EKSEMPLAR . ']',
                'errors' => [
                    'required'               => 'Jumlah Eksemplar wajib diisi',
                    'is_natural_no_zero'     => 'Jumlah Eksemplar harus berupa angka lebih dari 0',
                    'less_than_equal_to'     => 'Jumlah Eksemplar maksimal ' . self::MAX_JUMLAH_EKSEMPLAR . ' per input',
                ],
            ],
        ]);

        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                helper('form');
                $post     = $this->request->getPost();
                $redirect = isset($post['redirect']) ? $post['redirect'] : '';

                // FIX #6: Catalog_id sudah dijamin ada oleh rule 'required' di atas,
                // jadi pengecekan empty() duplikat dihapus.
                $Catalog_id = $post['Catalog_id'];
                $total      = (int) $post['JumlahEksemplar'];

                $catalogRow   = $this->db->table('catalogs')
                                         ->where('ID', $Catalog_id)
                                         ->get()->getRow();
                $worksheet_id = (!empty($catalogRow) && isset($catalogRow->Worksheet_id)) ? $catalogRow->Worksheet_id : '';

                $collections = [];

                // FIX #3/#4: seluruh proses generate nomor + insert dibungkus
                // satu transaction, supaya locking di generateNomorBarcode()
                // benar-benar melindungi dari race condition sampai insert selesai.
                $this->db->transStart();

                for ($i = 1; $i <= $total; $i++) {
                    if ($NomorInduk == "Manual") {
                        $idx          = $i - 1;
                        $noInduk      = isset($post['NoInduk' . $idx]) ? $post['NoInduk' . $idx] : '';
                        $nomorBarcode = isset($post['NomorBarcode' . $idx]) ? $post['NomorBarcode' . $idx] : '';
                        $rfid         = isset($post['RFID' . $idx]) ? $post['RFID' . $idx] : '';
                    } else {
                        $collectionData = [
                            'worksheet_id' => $worksheet_id,
                            'category_id'  => isset($post['Category_id']) ? $post['Category_id'] : null,
                            'media_id'     => isset($post['Media_id']) ? $post['Media_id'] : null,
                            'source_id'    => isset($post['Source_id']) ? $post['Source_id'] : null,
                            'partner_id'   => isset($post['Partner_id']) ? $post['Partner_id'] : null,
                        ];
                        $nomorBarcode = $this->generateNomorBarcode($collectionData, $i);
                        $noInduk      = $nomorBarcode;
                        $rfid         = $nomorBarcode;
                    }

                    $save = [
                        'Catalog_id'          => $post['Catalog_id'],
                        'ISDRM'               => isset($post['ISDRM']) ? $post['ISDRM'] : 0,
                        'Location_Library_id' => isset($post['Location_Library_id']) ? $post['Location_Library_id'] : null,
                        'Location_id'         => isset($post['Location_id']) ? $post['Location_id'] : null,
                        'NomorBarcode'        => $nomorBarcode,
                        'NoInduk'             => $noInduk,
                        'RFID'                => $rfid,
                        'CallNumber'          => isset($post['CallNumber']) ? $post['CallNumber'] : '',
                        'IsQUARANTINE'        => '0',
                        'CreateBy'            => user_id(),
                        'CreateDate'          => date("Y-m-d H:i:s"),
                        'UpdateBy'            => user_id(),
                        'UpdateDate'          => date("Y-m-d H:i:s"),
                    ];

                    if (!empty($post['TanggalPengadaan'])) $save['TanggalPengadaan'] = $post['TanggalPengadaan'];
                    if (!empty($post['Rule_id']))          $save['Rule_id']           = $post['Rule_id'];
                    if (!empty($post['Category_id']))      $save['Category_id']       = $post['Category_id'];
                    if (!empty($post['Currency']))         $save['Currency']          = $post['Currency'];
                    if (!empty($post['Media_id']))         $save['Media_id']          = $post['Media_id'];
                    $save['Source_id'] = !empty($post['Source_id']) ? $post['Source_id'] : 2; // default: Hadiah/Hibah
                    if (!empty($post['Status_id']))        $save['Status_id']         = $post['Status_id'];
                    if (!empty($post['Partner_id']))       $save['Partner_id']        = $post['Partner_id'];
                    // FIX #2: pakai array_key_exists supaya Price = 0 tetap tersimpan.
                    if (array_key_exists('Price', $post) && $post['Price'] !== '') $save['Price'] = $post['Price'];
                    if (!empty($post['PriceType']))        $save['PriceType']         = $post['PriceType'];

                    $save['ISOPAC'] = !empty($post['ISOPAC']) ? 1 : 0;

                    array_push($collections, $save);
                }

                if (!empty($collections)) {
                    try {
                        $insert = $this->eksemplarModel->insertBatch($collections);

                        if ($insert === false) {
                            $modelErrors = $this->eksemplarModel->errors();
                            $errorString = !empty($modelErrors) ? implode(', ', $modelErrors) : 'Unknown Model Validation Error';

                            $this->db->transRollback();

                            if ($isAjax) {
                                return $this->response->setJSON(['success' => false, 'message' => 'Eksemplar gagal ditambah. Penyebab: ' . $errorString]);
                            }
                            $this->session->setFlashdata('swal_icon',  'error');
                            $this->session->setFlashdata('swal_title', 'Validasi Model Gagal');
                            $this->session->setFlashdata('swal_text',  'Eksemplar gagal ditambah. Penyebab: ' . $errorString);
                            return redirect()->back()->withInput();
                        }

                        $this->db->transComplete();

                        if ($this->db->transStatus() === false) {
                            // FIX #3/#4: transaction gagal (mis. deadlock/lock timeout
                            // akibat request bersamaan) — beri pesan yang jelas, jangan
                            // lanjut seolah berhasil.
                            if ($isAjax) {
                                return $this->response->setJSON(['success' => false, 'message' => 'Eksemplar gagal ditambah karena konflik transaksi. Silakan coba lagi.']);
                            }
                            $this->session->setFlashdata('swal_icon',  'error');
                            $this->session->setFlashdata('swal_title', 'Gagal Menyimpan');
                            $this->session->setFlashdata('swal_text',  'Eksemplar gagal ditambah karena konflik transaksi. Silakan coba lagi.');
                            return redirect()->back()->withInput();
                        }

                        record_history('CREATE', 'item', $Catalog_id, 'Tambah ' . $total . ' eksemplar baru');

                        if ($isAjax) {
                            return $this->response->setJSON(['success' => true, 'message' => $total . ' eksemplar berhasil ditambahkan']);
                        }

                        $this->session->setFlashdata('swal_icon',  'success');
                        $this->session->setFlashdata('swal_title', 'Berhasil');
                        $this->session->setFlashdata('swal_text',  'Eksemplar berhasil ditambah');

                        $IsRedirect = $this->request->getPost('IsRedirect');
                        if ($IsRedirect == 1) {
                            return !empty($redirect)
                                ? redirect()->to($redirect)
                                : redirect()->to('eksemplar');
                        } else {
                            return redirect()->back();
                        }

                    } catch (\Throwable $e) {
                        $this->db->transRollback();

                        $errorMessage = $e->getMessage();

                        if (str_contains($errorMessage, 'Duplicate entry')) {
                            preg_match("/Duplicate entry '(.+?)' for key/", $errorMessage, $m);
                            $dupValue    = isset($m[1]) ? $m[1] : '';
                            $friendlyMsg = "Nomor Barcode <strong>{$dupValue}</strong> sudah terdaftar di sistem. Gunakan nomor barcode yang berbeda.";
                        } else {
                            $friendlyMsg = 'Eksemplar gagal ditambah. Silakan coba lagi atau hubungi administrator.';
                        }

                        if ($isAjax) {
                            return $this->response->setJSON(['success' => false, 'message' => $friendlyMsg]);
                        }

                        $this->session->setFlashdata('swal_icon',  'error');
                        $this->session->setFlashdata('swal_title', 'Gagal Menyimpan');
                        $this->session->setFlashdata('swal_html',  $friendlyMsg);

                        log_message('error', '[Eksemplar Create DB Error] ' . $errorMessage);
                        return redirect()->back()->withInput();
                    }
                } else {
                    $this->db->transRollback();
                }

            } else {
                $error_msg = '<ul>';
                foreach ($this->validation->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                if ($isAjax) {
                    return $this->response->setJSON(['success' => false, 'message' => $error_msg]);
                }

                $this->session->setFlashdata('swal_icon',  'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html',  $error_msg);

                return redirect()->back()->withInput();
            }
        }

        $this->data['redirect'] = base_url('eksemplar/create');
        echo view('Eksemplar\Views\add', $this->data);
    }

    // ----------------------------------------------------------------
    // EDIT
    // ----------------------------------------------------------------

    public function edit($id)
    {
        $this->data['title'] = 'Ubah Eksemplar';
        $isAjax = $this->request->isAJAX();

        $eksemplar = $this->eksemplarModel->find($id);
        $this->data['eksemplar'] = $eksemplar;

        if (!empty($eksemplar)) {
            $katalog = $this->katalogModel->find($eksemplar->Catalog_id);
            $this->data['katalog'] = $katalog;
        } else {
            if ($isAjax) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data eksemplar tidak ditemukan']);
            }
            $this->session->setFlashdata('swal_icon', 'error');
            $this->session->setFlashdata('swal_title', 'Tidak Ditemukan');
            $this->session->setFlashdata('swal_text', 'Data eksemplar tidak ditemukan');
            return redirect()->to('/eksemplar');
        }

        $this->data['CreateBy'] = get_username(isset($eksemplar->CreateBy) ? $eksemplar->CreateBy : 0);
        $this->data['UpdateBy'] = get_username(isset($eksemplar->UpdateBy) ? $eksemplar->UpdateBy : 0);

        $this->validation->setRules([
            'Catalog_id'          => ['label' => 'Judul Katalog',       'rules' => 'required'],
            'Location_Library_id' => ['label' => 'Lokasi Perpustakaan', 'rules' => 'permit_empty'],
            'Location_id'         => ['label' => 'Lokasi Ruang',        'rules' => 'permit_empty'],
            'Source_id'           => ['label' => 'Sumber Pengadaan',    'rules' => 'permit_empty'],
            'Partner_id'          => ['label' => 'Nama Sumber',         'rules' => 'permit_empty'],
            'Media_id'            => ['label' => 'Bentuk Fisik',        'rules' => 'required'],
            'Category_id'         => ['label' => 'Kategori Koleksi',    'rules' => 'required'],
            'TanggalPengadaan'    => ['label' => 'Tanggal Pengadaan',   'rules' => 'permit_empty'],
        ]);

        if ($this->request->getPost()) {
            if ($this->validation->withRequest($this->request)->run()) {
                helper('form');
                $post     = $this->request->getPost();
                $redirect = isset($post['redirect']) ? $post['redirect'] : '';

                $update = [
                    'Location_Library_id' => isset($post['Location_Library_id']) ? $post['Location_Library_id'] : null,
                    'Location_id'         => isset($post['Location_id']) ? $post['Location_id'] : null,
                    'ISDRM'               => isset($post['ISDRM']) ? $post['ISDRM'] : 0,
                    'NomorBarcode'        => isset($post['NomorBarcode0']) ? $post['NomorBarcode0'] : (isset($post['NomorBarcode']) ? $post['NomorBarcode'] : $eksemplar->NomorBarcode),
                    'NoInduk'             => isset($post['NoInduk0']) ? $post['NoInduk0'] : (isset($post['NoInduk']) ? $post['NoInduk'] : $eksemplar->NoInduk),
                    'RFID'                => isset($post['RFID0']) ? $post['RFID0'] : (isset($post['RFID']) ? $post['RFID'] : $eksemplar->RFID),
                    'UpdateBy'            => user_id(),
                    'UpdateDate'          => date("Y-m-d H:i:s"),
                ];

                // FIX #1: pakai array_key_exists (bukan !empty()) untuk field opsional,
                // supaya user bisa MENGOSONGKAN field ini saat edit, bukan cuma mengisi/
                // menimpanya. Kalau field memang tidak dikirim sama sekali oleh form,
                // nilai lama di DB tidak disentuh.
                if (array_key_exists('TanggalPengadaan', $post)) $update['TanggalPengadaan'] = $post['TanggalPengadaan'] ?: null;
                if (array_key_exists('Rule_id', $post))          $update['Rule_id']          = $post['Rule_id'] ?: null;
                if (array_key_exists('Category_id', $post))      $update['Category_id']      = $post['Category_id'] ?: null;
                if (array_key_exists('Currency', $post))         $update['Currency']         = $post['Currency'] ?: null;
                if (array_key_exists('Media_id', $post))         $update['Media_id']         = $post['Media_id'] ?: null;
                if (array_key_exists('Source_id', $post))        $update['Source_id']        = $post['Source_id'] ?: null;
                if (array_key_exists('Status_id', $post))        $update['Status_id']        = $post['Status_id'] ?: null;
                if (array_key_exists('Partner_id', $post))       $update['Partner_id']       = $post['Partner_id'] ?: null;
                // FIX #2: Price = "0" harus tersimpan sebagai 0, bukan diperlakukan
                // sebagai "kosong".
                if (array_key_exists('Price', $post))            $update['Price']            = ($post['Price'] === '' ? null : $post['Price']);
                if (array_key_exists('PriceType', $post))        $update['PriceType']        = $post['PriceType'] ?: null;
                $update['ISOPAC'] = !empty($post['ISOPAC']) ? 1 : 0;

                try {
                    $this->eksemplarModel->update($id, $update);

                    record_history('UPDATE', 'item', $id, 'Update eksemplar');

                    if ($isAjax) {
                        return $this->response->setJSON(['success' => true, 'message' => 'Eksemplar berhasil diubah']);
                    }

                    $this->session->setFlashdata('swal_icon', 'success');
                    $this->session->setFlashdata('swal_title', 'Berhasil');
                    $this->session->setFlashdata('swal_text', 'Eksemplar berhasil diubah');

                    $IsRedirect = $this->request->getPost('IsRedirect');
                    if ($IsRedirect == 1) {
                        return !empty($redirect)
                            ? redirect()->to($redirect)
                            : redirect()->to('eksemplar');
                    } else {
                        return redirect()->back();
                    }

                } catch (\Throwable $e) {
                    // FIX #5: jangan tampilkan pesan error DB mentah ke user (bisa
                    // membocorkan struktur tabel/kolom). Samakan pola dengan create():
                    // pesan ramah ke user, detail teknis ke log saja.
                    $errorMessage = $e->getMessage();

                    if (str_contains($errorMessage, 'Duplicate entry')) {
                        preg_match("/Duplicate entry '(.+?)' for key/", $errorMessage, $m);
                        $dupValue    = isset($m[1]) ? $m[1] : '';
                        $friendlyMsg = "Nomor Barcode/No. Induk/RFID <strong>{$dupValue}</strong> sudah terdaftar di sistem.";
                    } else {
                        $friendlyMsg = 'Eksemplar gagal diubah. Silakan coba lagi atau hubungi administrator.';
                    }

                    log_message('error', '[Eksemplar Edit DB Error] ' . $errorMessage);

                    if ($isAjax) {
                        return $this->response->setJSON(['success' => false, 'message' => $friendlyMsg]);
                    }
                    $this->session->setFlashdata('swal_icon', 'error');
                    $this->session->setFlashdata('swal_title', 'Terjadi Kesalahan');
                    $this->session->setFlashdata('swal_text', $friendlyMsg);
                    return redirect()->back()->withInput();
                }

            } else {
                $error_msg = '<ul>';
                foreach ($this->validation->getErrors() as $error) {
                    $error_msg .= '<li>' . esc($error) . '</li>';
                }
                $error_msg .= '</ul>';

                if ($isAjax) {
                    return $this->response->setJSON(['success' => false, 'message' => $error_msg]);
                }

                $this->session->setFlashdata('swal_icon', 'error');
                $this->session->setFlashdata('swal_title', 'Validasi Gagal');
                $this->session->setFlashdata('swal_html', $error_msg);

                return redirect()->back()->withInput();
            }
        }

        echo view('Eksemplar\Views\update', $this->data);
    }

    // ----------------------------------------------------------------
    // MODAL (AJAX) — dipanggil dari tab EKSEMPLAR di halaman edit katalog
    // ----------------------------------------------------------------

    public function modalCreate($catalog_id)
    {
        // FIX #9: pakai $this->db (bukan db_connect() terpisah) supaya
        // konsisten dengan method lain dan tidak membuka koneksi DB kedua.
        $db = $this->db;

        $data['catalog_id']        = $catalog_id;
        $data['mode']              = 'create';
        $data['eksemplar']         = null;
        $data['locationLibraries'] = $db->table('location_library')->select('ID, Name')->orderBy('Name')->get()->getResult();
        // FIX #8: sediakan $data['locations'] juga untuk mode create (dulu
        // undefined variable karena belum ada Location_Library_id terpilih).
        // Kosong dulu — diisi lewat AJAX cascading setelah user memilih
        // Lokasi Perpustakaan di form.
        $data['locations']         = [];
        $data['medias']            = $db->table('collectionmedias')->select('ID, Name')->orderBy('Name')->get()->getResult();
        $data['categories']        = $db->table('collectioncategorys')->select('ID, Name')->orderBy('Name')->get()->getResult();
        $data['sources']           = $db->table('collectionsources')->select('ID, Name')->orderBy('Name')->get()->getResult();
        $data['statuses']          = $db->table('collectionstatus')->select('ID, Name')->orderBy('Name')->get()->getResult();

        return view('Eksemplar\Views\modal_form', $data);
    }

    public function modalEdit($id)
    {
        // FIX #9: pakai $this->db (bukan db_connect() terpisah).
        $db = $this->db;

        $eksemplar = $this->eksemplarModel->find($id);
        if (!$eksemplar) {
            return $this->response->setStatusCode(404)->setBody('<div class="alert alert-danger">Eksemplar tidak ditemukan</div>');
        }

        $data['catalog_id']        = $eksemplar->Catalog_id;
        $data['mode']              = 'edit';
        $data['eksemplar']         = $eksemplar;
        $data['locationLibraries'] = $db->table('location_library')->select('ID, Name')->orderBy('Name')->get()->getResult();
        $data['locations']         = $db->table('locations')->select('ID, Name')->where('LocationLibrary_id', $eksemplar->Location_Library_id)->orderBy('Name')->get()->getResult();
        $data['medias']            = $db->table('collectionmedias')->select('ID, Name')->orderBy('Name')->get()->getResult();
        $data['categories']        = $db->table('collectioncategorys')->select('ID, Name')->orderBy('Name')->get()->getResult();
        $data['sources']           = $db->table('collectionsources')->select('ID, Name')->orderBy('Name')->get()->getResult();
        $data['statuses']          = $db->table('collectionstatus')->select('ID, Name')->orderBy('Name')->get()->getResult();

        return view('Eksemplar\Views\modal_form', $data);
    }


    // ----------------------------------------------------------------
    // PRIVATE HELPERS
    // ----------------------------------------------------------------

    /**
     * Generate nomor barcode otomatis.
     * Array index genap = formatOptions, ganjil = separatorOptions.
     *
     * FIX #3: query MAX(...) sekarang pakai "FOR UPDATE" supaya baris yang
     * relevan terkunci sampai transaction pemanggil (di create()) selesai.
     * Ini mencegah dua request create() berjalan bersamaan mendapatkan nomor
     * dasar yang sama. Method ini HARUS dipanggil di dalam transaction aktif
     * (lihat create() yang membungkusnya dengan $this->db->transStart()).
     */
    private function generateNomorBarcode($collectionData = [], $increment = 1)
    {
        $db = $this->db;

        // FIX #3: SELECT ... FOR UPDATE untuk locking di dalam transaction aktif.
        $query = $db->query('SELECT MAX(RIGHT(NomorBarcode,5)) as no FROM collections FOR UPDATE');
        $no    = $query->getRow()->no;

        // TAMBAHKAN INI:
        if (empty($no)) {
            $no = $increment;
        } else {
            $no = intval($no) + $increment;
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
            $format3 = str_replace('{99999}', '', $format2);

            if (empty($no)) {
                $no = $increment;
            } else {
                $no = intval($no) + $increment;
                $no = str_pad($no, 5, "0", STR_PAD_LEFT);
            }

            return $format3 . $no;
        }

        $formatArray = explode('|', $formatString);
        $result      = '';

        $separatorOptions = [
            '2' => '',
            '3' => '/',
            '4' => '-',
            '5' => '.',
        ];

        for ($i = 0; $i < count($formatArray); $i++) {
            $value = trim($formatArray[$i]);

            if ($i % 2 === 0) {
                if ($value !== '0') {
                    if (preg_match('/\{([^}]+)\}/', $value, $matches)) {
                        $result .= ($matches[1] === '99999') ? '{99999}' : $matches[1];
                    } elseif (is_numeric($value)) {
                        switch ($value) {
                            case '2':
                                if (!empty($collectionData['worksheet_id'])) {
                                    $r = $db->table('worksheets')->select('Code')->where('ID', $collectionData['worksheet_id'])->get()->getRow();
                                    $result .= $r ? $r->Code : '';
                                }
                                break;
                            case '3':
                                if (!empty($collectionData['category_id'])) {
                                    $r = $db->table('collectioncategorys')->select('Code')->where('ID', $collectionData['category_id'])->get()->getRow();
                                    $result .= $r ? $r->Code : '';
                                }
                                break;
                            case '4':
                                if (!empty($collectionData['media_id'])) {
                                    $r = $db->table('collectionmedias')->select('Code')->where('ID', $collectionData['media_id'])->get()->getRow();
                                    $result .= $r ? $r->Code : '';
                                }
                                break;
                            case '5':
                                if (!empty($collectionData['partner_id'])) {
                                    $r = $db->table('partners')->select('Name')->where('ID', $collectionData['partner_id'])->get()->getRow();
                                    // FIX (minor): sanitasi nama partner supaya barcode tidak
                                    // mengandung spasi/simbol yang bisa bikin masalah saat
                                    // di-scan fisik. Ambil huruf/angka saja, uppercase.
                                    $partnerName = $r ? $r->Name : '';
                                    $result .= strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $partnerName));
                                }
                                break;
                            case '6':
                                $result .= $no;
                                break;
                            case '7':
                                $result .= date('Y');
                                break;
                        }
                    }
                }
            } else {
                if (isset($separatorOptions[$value])) {
                    $result .= $separatorOptions[$value];
                }
            }
        }

        return $result;
    }
}