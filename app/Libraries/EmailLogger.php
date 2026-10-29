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

    private static bool $checked = false;

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

    public static function ensureInstalled(): void
    {
        if (self::$checked) {
            return;
        }
        self::$checked = true;

        try {
            if (function_exists('read_cache') && read_cache('email_logs_installed')) {
                return;
            }

            $db = db_connect();
            $db->query("
                CREATE TABLE IF NOT EXISTS email_logs (
                    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    recipient_email VARCHAR(150) NOT NULL,
                    recipient_name VARCHAR(150) NULL DEFAULT NULL,
                    member_no VARCHAR(100) NULL DEFAULT NULL,
                    member_id INT NULL DEFAULT NULL,
                    email_type VARCHAR(50) NOT NULL,
                    subject VARCHAR(255) NOT NULL,
                    status ENUM('success', 'failed') NOT NULL DEFAULT 'success',
                    error_message TEXT NULL DEFAULT NULL,
                    sent_at DATETIME NOT NULL,
                    created_by INT NULL DEFAULT NULL,
                    PRIMARY KEY (id),
                    INDEX idx_email_logs_sent_at (sent_at),
                    INDEX idx_email_logs_email_type (email_type),
                    INDEX idx_email_logs_status (status),
                    INDEX idx_email_logs_recipient (recipient_email)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            $db->query("
                INSERT INTO c_menus (name, parent, controller, slug, icon, permission, type, category_id, category, sort, description, active, created_at)
                SELECT 'Laporan Pengiriman Email', 3, 'laporan-email', 'laporan-email', 'fas fa-envelope-open-text', 'access|index|create|edit|delete', 'menu', 1, 'backend-menu', 39, 'Laporan Pengiriman Email', 1, NOW()
                FROM DUAL
                WHERE NOT EXISTS (SELECT 1 FROM c_menus WHERE controller = 'laporan-email' AND parent = 3)
            ");

            $permissions = [
                'laporan-email/access'     => 'Access Laporan Pengiriman Email',
                'laporan-email/index'      => 'Index Laporan Pengiriman Email',
                'laporan-email/preview'    => 'Preview Laporan Pengiriman Email',
                'laporan-email/export'     => 'Export Excel Laporan Pengiriman Email',
                'laporan-email/export_pdf' => 'Export PDF Laporan Pengiriman Email',
                'laporan-email/create'     => 'Create Permission for menu Laporan Pengiriman Email',
                'laporan-email/edit'       => 'Edit Permission for menu Laporan Pengiriman Email',
                'laporan-email/delete'     => 'Delete Permission for menu Laporan Pengiriman Email',
            ];

            foreach ($permissions as $name => $desc) {
                $db->query(
                    "INSERT INTO auth_permissions (name, description)
                     SELECT ?, ? FROM DUAL
                     WHERE NOT EXISTS (SELECT 1 FROM auth_permissions WHERE name = ?)",
                    [$name, $desc, $name]
                );
            }

            $db->query("
                INSERT IGNORE INTO auth_groups_permissions (group_id, permission_id)
                SELECT g.id, p.id
                FROM auth_groups g
                CROSS JOIN auth_permissions p
                WHERE g.id IN (1, 10, 11)
                AND p.name LIKE 'laporan-email/%'
            ");

            if (function_exists('write_cache')) {
                write_cache('email_logs_installed', true);
            }
        } catch (\Throwable $e) {
            log_message('error', '[EmailLogger] ensureInstalled: ' . $e->getMessage());
        }
    }

    public static function log(array $payload): void
    {
        try {
            self::ensureInstalled();
            $db = db_connect();

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
