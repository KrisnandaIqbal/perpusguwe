<?php
/**
 * Email ke pendaftar anggota. Satu kerangka untuk tiga pemberitahuan.
 * Warna mengikuti kartu anggota (navy, krem, emas) supaya email dan kartunya terbaca satu asal.
 *
 * @var string $jenis       diterima | disetujui | perbaikan
 * @var object $member
 * @var string $nama_perpus
 * @var string $url_daftar
 * @var string $berlaku     hanya untuk disetujui
 * @var array  $alasan      hanya untuk perbaikan: label, cara, daftar_ulang
 * @var bool   $lainnya     hanya untuk perbaikan: alasannya ditulis petugas di catatan
 * @var string $catatan     hanya untuk perbaikan
 */

$judul = [
    'diterima'  => 'Pendaftaran Anda sudah kami terima',
    'disetujui' => 'Pendaftaran Anda disetujui',
    'perbaikan' => 'Pendaftaran Anda belum bisa diterima',
][$jenis];

$baris = static function (string $label, string $nilai): string {
    return '<tr>'
        . '<td style="padding:6px 16px 6px 0;font-size:14px;line-height:20px;color:#4A5563;vertical-align:top;white-space:nowrap;">' . esc($label) . '</td>'
        . '<td style="padding:6px 0;font-size:14px;line-height:20px;color:#0D1C2E;font-weight:bold;">' . esc($nilai) . '</td>'
        . '</tr>';
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($judul) ?></title>
</head>
<body style="margin:0;padding:0;background:#F0EBE1;font-family:'Segoe UI',Arial,Helvetica,sans-serif;color:#0D1C2E;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F0EBE1;">
        <tr>
            <td align="center" style="padding:24px 12px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#FFFFFF;">
                    <tr>
                        <td style="padding:20px 28px;background:#0D1C2E;border-bottom:4px solid #B8943F;font-size:14px;line-height:20px;font-weight:bold;color:#F0EBE1;">
                            <?= esc($nama_perpus) ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px;">
                            <h1 style="margin:0 0 16px;font-size:22px;line-height:28px;font-weight:bold;color:#0D1C2E;"><?= esc($judul) ?></h1>
                            <p style="margin:0 0 16px;font-size:15px;line-height:24px;">Yth. <?= esc($member->Fullname) ?>,</p>

                            <?php if ($jenis === 'diterima'): ?>
                                <p style="margin:0 0 16px;font-size:15px;line-height:24px;">
                                    Formulir pendaftaran anggota Anda sudah masuk dan sedang diperiksa petugas perpustakaan.
                                    Hasilnya kami kirim ke alamat email ini.
                                </p>
                                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px;">
                                    <?= $baris('Jenis anggota', (string) ($member->JenisAnggota ?? '-')) ?>
                                    <?php if (!empty($member->status_non_asn)): ?>
                                        <?= $baris('Status Non-ASN', ucfirst($member->status_non_asn)) ?>
                                    <?php endif; ?>
                                    <?= $baris('Unit kerja', (string) (get_member_unit_display($member) ?: '-')) ?>
                                    <?= $baris('Tanggal daftar', tgl_indonesia(substr((string) $member->RegisterDate, 0, 10))) ?>
                                </table>
                                <p style="margin:0;font-size:15px;line-height:24px;">Anda tidak perlu mendaftar lagi selama menunggu.</p>

                            <?php elseif ($jenis === 'disetujui'): ?>
                                <p style="margin:0 0 16px;font-size:15px;line-height:24px;">
                                    Anda sekarang terdaftar sebagai anggota <?= esc($nama_perpus) ?>.
                                </p>
                                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px;">
                                    <?= $baris('No. Anggota', (string) $member->MemberNo) ?>
                                    <?= $baris('Berlaku sampai', $berlaku) ?>
                                </table>
                                <p style="margin:0;font-size:15px;line-height:24px;">
                                    Kartu anggota Anda terlampir sebagai satu file PDF berisi sisi depan dan sisi belakang.
                                    Kartu memuat nama, No. Anggota, dan kode QR. Saat berkunjung, tunjukkan sisi belakang untuk dipindai.
                                </p>

                            <?php else: ?>
                                <p style="margin:0 0 16px;font-size:15px;line-height:24px;">
                                    Petugas perpustakaan telah memeriksa pendaftaran Anda. Pendaftaran tersebut <strong>belum bisa diterima</strong> karena beberapa hal berikut:
                                </p>

                                <?php if (!empty($daftar_alasan) && is_array($daftar_alasan)): ?>
                                    <ul style="margin:0 0 16px 20px;padding:0;font-size:14px;line-height:22px;color:#0D1C2E;">
                                        <?php foreach ($daftar_alasan as $item): ?>
                                            <li style="margin-bottom:8px;">
                                                <strong><?= esc($item['label']) ?></strong>
                                                <?php if (!empty($item['cara'])): ?>
                                                    <div style="font-size:13px;color:#4A5563;margin-top:2px;"><?= esc($item['cara']) ?></div>
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 16px;">
                                        <?= $baris('Alasan', $lainnya ? $catatan : ($alasan['label'] ?? 'Perlu perbaikan data')) ?>
                                    </table>
                                    <?php if (!empty($alasan['cara'])): ?>
                                        <p style="margin:0 0 16px;font-size:14px;line-height:22px;color:#4A5563;"><?= esc($alasan['cara']) ?></p>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (!empty($catatan)): ?>
                                    <div style="margin:0 0 16px;padding:12px 16px;background:#F9FAFB;border-left:4px solid #0D1C2E;font-size:14px;line-height:20px;">
                                        <strong style="display:block;margin-bottom:4px;color:#0D1C2E;">Catatan Petugas:</strong>
                                        <span style="color:#4A5563;"><?= nl2br(esc($catatan)) ?></span>
                                    </div>
                                <?php endif; ?>

                                <p style="margin:0 0 16px;font-size:14px;line-height:22px;color:#6B7280;">
                                    Data pendaftaran sebelumnya telah dihapus dari sistem kami.
                                </p>

                                <?php if ($bisa_daftar_ulang ?? ($alasan['daftar_ulang'] ?? true)): ?>
                                    <p style="margin:0 0 20px;font-size:15px;line-height:24px;">
                                        Anda dapat mengisi kembali formulir pendaftaran online dengan data yang telah disesuaikan:
                                    </p>
                                    <table role="presentation" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="background:#0D1C2E;border-radius:6px;">
                                                <a href="<?= esc($url_daftar, 'attr') ?>" style="display:inline-block;padding:12px 22px;font-size:14px;line-height:20px;font-weight:bold;color:#F0EBE1;text-decoration:none;">Isi Ulang Formulir Pendaftaran</a>
                                            </td>
                                        </tr>
                                    </table>
                                <?php else: ?>
                                    <p style="margin:0;font-size:14px;line-height:22px;color:#4A5563;">
                                        Jika Anda merasa ini keliru atau membutuhkan informasi lebih lanjut, silakan hubungi petugas perpustakaan.
                                    </p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 28px;border-top:1px solid #E5DFD5;font-size:12px;line-height:18px;color:#4A5563;">
                            Email ini dikirim otomatis karena alamat ini dipakai mendaftar anggota <?= esc($nama_perpus) ?>.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
