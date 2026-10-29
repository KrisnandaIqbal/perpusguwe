<?php

namespace Katalog\Controllers;

/**
 * KatalogFileController
 * 
 * Menangani tampilan dan dekripsi file PDF yang terlampir
 * pada katalog, termasuk file artikel serial.
 */
class KatalogFileController extends \Base\Controllers\BaseController
{
    use KatalogBase;

    function __construct()
    {
        $this->initKatalogBase();
    }

    // ----------------------------------------------------------------
    // VIEW - DECRYPT (Katalog)
    // ----------------------------------------------------------------

    public function view_decrypted($ID)
    {
        $id = decData($ID);
        if (!$id) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        $file = $this->fileModel->find($id);
        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        $catalog = $this->katalogModel->find($file->Catalog_id);
        $title = is_object($catalog) ? ($catalog->Title ?? 'Buku Digital') : ($catalog['Title'] ?? 'Buku Digital');

        return view('Katalog\Views\slug\pdf_viewer', [
            'fileId'   => $id,
            'fileName' => $file->FileURL,
            'title'    => $title,
        ]);
    }

    public function get_decrypted_content($ID)
    {
        $id = decData($ID);
        if (!$id) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        // Cegah akses unduh langsung di luar flipbook viewer
        if ($this->_isDirectDownloadAttempt()) {
            return $this->response->setStatusCode(403)->setBody('Unduhan tidak diizinkan. Dokumen hanya dapat dibaca melalui Flipbook.');
        }

        $file = $this->fileModel->find($id);
        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        return $this->_streamDecryptedPdf(
            $this->modulePath . $file->FileURL,
            $file->FileURL
        );
    }

    // ----------------------------------------------------------------
    // VIEW - DECRYPT (Artikel)
    // ----------------------------------------------------------------

    public function view_decrypted_article($ID)
    {
        $file = $this->serialArticleFilesModel->find($ID);
        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        $article = $this->artikelModel->find($file->Article_id ?? null);
        $title = is_object($article) ? ($article->Title ?? 'Artikel Digital') : ($article['Title'] ?? 'Artikel Digital');

        return view('Katalog\Views\slug\pdf_viewer_article', [
            'fileId'   => $ID,
            'fileName' => $file->FileURL,
            'title'    => $title,
        ]);
    }

    public function get_decrypted_content_article($ID)
    {
        // Cegah akses unduh langsung di luar flipbook viewer
        if ($this->_isDirectDownloadAttempt()) {
            return $this->response->setStatusCode(403)->setBody('Unduhan tidak diizinkan. Dokumen hanya dapat dibaca melalui Flipbook.');
        }

        $file = $this->serialArticleFilesModel->find($ID);
        if (!$file || !file_exists($this->modulePath . $file->FileURL)) {
            return $this->response->setStatusCode(404)->setBody('File not found');
        }

        return $this->_streamDecryptedPdf(
            $this->modulePath . $file->FileURL,
            $file->FileURL
        );
    }

    // ----------------------------------------------------------------
    // PRIVATE HELPER
    // ----------------------------------------------------------------

    /**
     * Cek apakah permintaan merupakan upaya unduhan langsung (bukan dari pembaca Flipbook).
     */
    private function _isDirectDownloadAttempt(): bool
    {
        // Permintaan navigasi langsung dokumen (misalnya buka link di tab baru atau klik kanan unduh)
        $secFetchDest = $this->request->getHeaderLine('Sec-Fetch-Dest');
        if ($secFetchDest === 'document') {
            return true;
        }

        // Cek header referer jika ada
        $referer = $this->request->getServer('HTTP_REFERER') ?? '';
        $host    = $this->request->getServer('HTTP_HOST') ?? '';

        if (!empty($referer) && !empty($host)) {
            if (strpos($referer, $host) === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Dekripsi file dan stream sebagai binary stream tanpa header unduhan.
     * Menggunakan application/octet-stream dan tanpa header Content-Disposition filename
     * agar IDM / browser download manager tidak mencegat berkas sebagai unduhan.
     *
     * @param string $sourcePath Path file terenkripsi
     * @param string $fileName   Nama file untuk referensi
     */
    private function _streamDecryptedPdf(string $sourcePath, string $fileName)
    {
        // Cek jika file sebenarnya adalah plain PDF
        $handle = fopen($sourcePath, 'rb');
        $magic = $handle ? fread($handle, 4) : '';
        if ($handle) {
            fclose($handle);
        }

        if ($magic === '%PDF') {
            $content = file_get_contents($sourcePath);
        } else {
            $tempDecryptedFile = tempnam(sys_get_temp_dir(), 'decrypted_');

            $encryption = new \App\Libraries\Encryption();
            $encryption->decryptFile($sourcePath, $tempDecryptedFile);

            $content = file_get_contents($tempDecryptedFile);
            if (file_exists($tempDecryptedFile)) {
                @unlink($tempDecryptedFile);
            }
        }

        // Kirim sebagai application/octet-stream tanpa nama file di Content-Disposition
        // untuk mencegah IDM dan peramban menampilkan dialog unduh.
        return $this->response
            ->setContentType('application/octet-stream')
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Length', (string) strlen($content))
            ->setHeader('Accept-Ranges', 'none')
            ->setHeader('X-Frame-Options', 'SAMEORIGIN')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', '0')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setBody($content);
    }
}
