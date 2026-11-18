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
            'unit_kerja_id' => (int) $member->UnitKerja_id,
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
            ->select('members.MemberNo, members.Fullname, members.Email, members.IzinPersonalisasi, master_unit.nama_unit')
            ->join('master_unit', 'master_unit.id = members.UnitKerja_id', 'left')
            ->where('members.ID', $id)->get()->getRow();
        if (!$member) {
            return $this->keluar();
        }
        return view('Opac\Views\profil', $this->dataTampilan('Profil Anggota') + ['member' => $member]);
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
