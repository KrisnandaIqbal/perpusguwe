<?php

namespace Opac\Controllers;

use Myth\Auth\Password;

/**
 * Login anggota untuk OPAC (tiket L) dan izin personalisasi (T8).
 *
 * Sengaja tidak memakai session Myth Auth (`logged_in`): akun anggota hanya mengisi
 * `session_member`, jadi filter `session`/`permissions` tetap menutup semua halaman admin.
 */
class Anggota extends \Base\Controllers\BaseController
{
    private const GAGAL = 'Nomor anggota atau kata sandi salah.';

    private $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect('data');
    }

    /** Layout OPAC (navbar, footer) butuh nama, logo, dan alamat perpustakaan. */
    private function dataTampilan(string $judul): array
    {
        return ['title' => $judul] + $this->_getLibrarySettings();
    }

    public function masuk()
    {
        if (session('session_member')) {
            return redirect()->to(base_url('opac'));
        }
        return view('Opac\Views\masuk', $this->dataTampilan('Masuk Anggota'));
    }

    public function prosesMasuk()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $user   = $username === '' ? null : model(\Myth\Auth\Models\UserModel::class)->where('username', $username)->first();
        $member = $user && $user->active ? $this->db->table('members')->where('MemberNo', $username)->get()->getRow() : null;

        // Verifikasi tetap dijalankan walau akun tidak ada, supaya waktu responsnya sama.
        $cocok = Password::verify($password, $user->password_hash ?? Password::hash('x'));
        if (!$user || !$member || !$cocok) {
            return redirect()->back()->withInput()->with('error', self::GAGAL);
        }

        session()->regenerate();
        session()->set('session_member', [
            'member_id'     => (int) $member->ID,
            'member_no'     => $member->MemberNo,
            'nama'          => $member->Fullname,
            'unit_kerja_id' => $member->UnitKerja_id !== null ? (int) $member->UnitKerja_id : null,
        ]);

        if (!(int) $member->IzinPersonalisasiDiumumkan) {
            $this->db->table('members')->where('ID', $member->ID)->update(['IzinPersonalisasiDiumumkan' => 1]);
            session()->setFlashdata('notif_izin', true);
        }
        return redirect()->to(base_url('opac'));
    }

    public function keluar()
    {
        session()->remove('session_member');
        return redirect()->to(base_url('opac'));
    }

    public function profil()
    {
        $id = session('session_member')['member_id'] ?? null;
        if (!$id) {
            return redirect()->to(base_url('opac/masuk'));
        }

        $member = $this->db->table('members')
            ->select('members.MemberNo, members.Fullname, members.Email, members.EndDate, members.PhotoUrl, members.IzinPersonalisasi, master_unit.nama_unit')
            ->join('master_unit', 'master_unit.id = members.UnitKerja_id', 'left')
            ->where('members.ID', $id)->get()->getRow();
        if (!$member) {
            return $this->keluar();
        }

        $pinjaman = fn () => $this->db->table('collectionloanitems')->where('member_id', $id);
        $ringkasan = [
            'dipinjam' => $pinjaman()->where('LoanStatus', 'Loan')->where('ActualReturn IS NULL')->countAllResults(),
            'total'    => $pinjaman()->countAllResults(),
        ];

        return view('Dashboard\\Views\\anggota_profil', ['judul' => 'Profil Anggota', 'member' => $member, 'ringkasan' => $ringkasan] + $this->dataDashboard('profil'));
    }

    /** Dashboard BiblioVerse untuk anggota; terpisah dari Dashboard\Controllers\Dashboard (admin). */
    public function dashboard()
    {
        if (!session('session_member')) {
            return redirect()->to(base_url('opac/masuk'));
        }
        $baris = (new Opac())->getBarisAnggota(session('session_member'));
        return view('Dashboard\\Views\\anggota', ['baris_rekomendasi' => array_map(fn ($isi) => array_slice($isi, 0, 5), $baris)] + $this->dataDashboard('beranda'));
    }

    public function keranjang()
    {
        return $this->halamanDaftar('member_keranjang', 'keranjang', 'Keranjang', 'Buku yang kamu siapkan untuk dipinjam di meja layanan.', 'Keranjangmu masih kosong. Tekan ikon keranjang pada buku yang ingin kamu pinjam.');
    }

    public function favorit()
    {
        return $this->halamanDaftar('member_favorit', 'favorit', 'Favorit', 'Buku yang kamu simpan untuk dibaca nanti.', 'Belum ada favorit. Tekan ikon penanda pada buku yang ingin kamu simpan.');
    }

    public function riwayat()
    {
        if (!session('session_member')) {
            return redirect()->to(base_url('opac/masuk'));
        }
        $riwayat = $this->db->table('collectionloanitems cli')
            ->select('c.ID, c.ID AS catalog_id, c.Title, c.Author, c.Subject, c.Publisher, c.CoverURL, cli.LoanDate, cli.DueDate, cli.ActualReturn')
            ->join('collections col', 'col.ID = cli.Collection_id')
            ->join('catalogs c', 'c.ID = col.Catalog_id')
            ->where('cli.member_id', session('session_member')['member_id'])
            ->orderBy('cli.LoanDate', 'DESC')
            ->get()->getResultArray();

        return view('Dashboard\\Views\\anggota_riwayat', ['riwayat' => $riwayat] + $this->dataDashboard('riwayat'));
    }

    /** Simpan atau hapus satu buku dari Favorit; kembali ke halaman asal. */
    public function ubahFavorit()
    {
        return $this->ubahDaftar('member_favorit', 'Favorit');
    }

    public function ubahKeranjang()
    {
        return $this->ubahDaftar('member_keranjang', 'Keranjang');
    }

    /** Id katalog di Favorit atau Keranjang anggota. Kosong bila tabelnya belum dibuat (artifacts/20261122_create_favorit_dan_keranjang_anggota.sql). */
    private function idDaftar(string $tabel): array
    {
        if (!$this->db->tableExists($tabel)) {
            return [];
        }
        return array_map('intval', array_column($this->db->table($tabel)->select('catalog_id')->where('member_id', session('session_member')['member_id'])->get()->getResultArray(), 'catalog_id'));
    }

    /** Data bersama semua halaman dashboard: menu aktif, lencana, dan id favorit/keranjang untuk tombol card. */
    private function dataDashboard(string $aktif): array
    {
        $favorit   = $this->idDaftar('member_favorit');
        $keranjang = $this->idDaftar('member_keranjang');

        return [
            'aktif'         => $aktif,
            'favorit_ids'   => $favorit,
            'keranjang_ids' => $keranjang,
            'hitung'        => ['favorit' => count($favorit), 'keranjang' => count($keranjang)],
        ];
    }

    private function halamanDaftar(string $tabel, string $aktif, string $judul, string $keterangan, string $kosong)
    {
        if (!session('session_member')) {
            return redirect()->to(base_url('opac/masuk'));
        }
        helper('opac');
        $data  = $this->dataDashboard($aktif);
        $ids   = $data[$aktif === 'favorit' ? 'favorit_ids' : 'keranjang_ids'];
        $buku  = $ids ? enrichCatalogsWithCirculation($this->db->table('catalogs')->whereIn('ID', $ids)->get()->getResultArray()) : [];

        return view('Dashboard\\Views\\anggota_daftar', $data + [
            'judulHalaman' => $judul,
            'keterangan'   => $keterangan,
            'kosong'       => $kosong,
            'buku'         => $buku,
            'tersedia'     => $this->db->tableExists($tabel),
        ]);
    }

    private function ubahDaftar(string $tabel, string $nama)
    {
        $id = session('session_member')['member_id'] ?? null;
        if (!$id) {
            return redirect()->to(base_url('opac/masuk'));
        }
        $katalog = (int) $this->request->getPost('catalog_id');
        if (!$this->db->tableExists($tabel) || !$this->db->table('catalogs')->where('ID', $katalog)->countAllResults()) {
            return redirect()->back()->with('pesan', $nama . ' belum bisa dipakai.');
        }

        $baris = ['member_id' => $id, 'catalog_id' => $katalog];
        if ($this->db->table($tabel)->where($baris)->countAllResults()) {
            $this->db->table($tabel)->where($baris)->delete();
            return redirect()->back()->with('pesan', 'Dihapus dari ' . $nama . '.');
        }
        $this->db->table($tabel)->insert($baris);
        return redirect()->back()->with('pesan', 'Ditambahkan ke ' . $nama . '.');
    }

    public function simpanIzin()
    {
        $id = session('session_member')['member_id'] ?? null;
        if (!$id) {
            return redirect()->to(base_url('opac/masuk'));
        }

        $izin = $this->request->getPost('izin') === '1' ? 1 : 0;
        $this->db->table('members')->where('ID', $id)->update(['IzinPersonalisasi' => $izin]);
        cache()->delete('rekomendasi_beranda_' . $id);

        return redirect()->to(base_url('opac/profil'))->with('pesan', $izin
            ? 'Rekomendasi dari riwayat pinjam dinyalakan.'
            : 'Rekomendasi dari riwayat pinjam dimatikan.');
    }
}
