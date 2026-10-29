<?php

namespace App\Libraries;

class EmailLogger
{
    public const TYPE_REGISTRASI_DITERIMA  = 'registrasi_diterima';
    public const TYPE_REGISTRASI_DISETUJUI = 'registrasi_disetujui';
    public const TYPE_REGISTRASI_PERBAIKAN = 'registrasi_perbaikan';
    public const TYPE_REGISTRASI_DITOLAK   = 'registrasi_ditolak';
    public const TYPE_KARTU_DIGITAL        = 'kartu_digital';
    public const TYPE_STRUK_PINJAM         = 'struk_pinjam';
    public const TYPE_STRUK_KEMBALI        = 'struk_kembali';
    public const TYPE_STRUK_PERPANJANG     = 'struk_perpanjang';
    public const TYPE_KETERLAMBATAN        = 'keterlambatan';
    public const TYPE_AKUN_MEMBER          = 'akun_member';

    public static function typeLabels(): array
    {
        return [
            self::TYPE_REGISTRASI_DITERIMA  => 'Pendaftaran Diterima',
            self::TYPE_REGISTRASI_DISETUJUI => 'Pendaftaran Disetujui',
            self::TYPE_REGISTRASI_PERBAIKAN => 'Perlu Perbaikan Pendaftaran',
            self::TYPE_REGISTRASI_DITOLAK   => 'Penolakan Pendaftaran',
            self::TYPE_KARTU_DIGITAL        => 'Kartu Anggota Digital',
            self::TYPE_STRUK_PINJAM         => 'Struk Peminjaman',
            self::TYPE_STRUK_KEMBALI        => 'Struk Pengembalian',
            self::TYPE_STRUK_PERPANJANG     => 'Struk Perpanjangan',
            self::TYPE_KETERLAMBATAN        => 'Peringatan Keterlambatan',
            self::TYPE_AKUN_MEMBER          => 'Aktivasi / Reset Akun',
        ];
    }

    public static function labelFor(string $type): string
    {
        $labels = self::typeLabels();
        return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    public static function log(array $payload): void
    {
        try {
            $db = db_connect();
            if (!$db->tableExists('email_logs')) {
                return;
            }

            $createdBy = $payload['created_by'] ?? null;
            if ($createdBy === null && function_exists('user_id')) {
                $uid = user_id();
                if ($uid) {
                    $createdBy = (int) $uid;
                }
            }

            $db->table('email_logs')->insert([
                'recipient_email' => substr((string) ($payload['recipient_email'] ?? ''), 0, 150),
                'recipient_name'  => isset($payload['recipient_name']) ? substr((string) $payload['recipient_name'], 0, 150) : null,
                'member_no'       => isset($payload['member_no']) ? substr((string) $payload['member_no'], 0, 100) : null,
                'member_id'       => isset($payload['member_id']) ? (int) $payload['member_id'] : null,
                'email_type'      => substr((string) ($payload['email_type'] ?? 'lainnya'), 0, 50),
                'subject'         => substr((string) ($payload['subject'] ?? ''), 0, 255),
                'status'          => ($payload['status'] ?? 'success') === 'failed' ? 'failed' : 'success',
                'error_message'   => $payload['error_message'] ?? null,
                'sent_at'         => $payload['sent_at'] ?? date('Y-m-d H:i:s'),
                'created_by'      => $createdBy,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[EmailLogger] ' . $e->getMessage());
        }
    }
}
