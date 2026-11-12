<?php

namespace UsulanKoleksi\Models;

class UsulanKoleksiModel extends \Base\Models\BaseModel
{
    protected $table      = 't_usulankoleksi';
    protected $primaryKey = 'id';
    protected $returnType     = 'object';
    protected $useSoftDeletes = false;
    protected $protectFields = false;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = true;

    /**
     * Mencatat satu pencarian 0 hasil ('pencarian') atau satu klik OneSearch ('onesearch') sebagai usulan otomatis.
     * Satu session dihitung sekali per query, jenis, dan hari. Gagal mencatat tidak boleh merusak halaman.
     */
    public function catatPencarian(string $query, string $jenis): void
    {
        $kolom  = ['pencarian' => 'request_count', 'onesearch' => 'onesearch_click_count'][$jenis] ?? null;
        $normal = \Rekomendasi\Libraries\UsulanOtomatis::normal($query);
        if ($kolom === null || $normal === '') {
            return;
        }

        $kunci    = date('Y-m-d') . "|$jenis|$normal";
        $dihitung = session()->get('usulan_dihitung') ?? [];
        if (isset($dihitung[$kunci])) {
            return;
        }

        try {
            $baris    = $this->where(['source' => 'pencarian', 'query_normal' => $normal])->first();
            $sekarang = date('Y-m-d H:i:s');
            if ($baris) {
                $this->update($baris->id, [$kolom => $baris->$kolom + 1, 'last_searched_at' => $sekarang]);
            } else {
                $judul = mb_substr(trim($query), 0, 255);
                $this->insert([
                    'title' => $judul, 'slug' => url_title($judul, '-', true), 'source' => 'pencarian',
                    'query_normal' => $normal, $kolom => 1, 'last_searched_at' => $sekarang,
                ]);
            }
            // shortcut: 50 kunci terakhir per session, cukup untuk satu hari penelusuran.
            $dihitung[$kunci] = true;
            session()->set('usulan_dihitung', array_slice($dihitung, -50, null, true));
        } catch (\Throwable $e) {
            log_message('error', 'usulan otomatis gagal: ' . $e->getMessage());
        }
    }
}
