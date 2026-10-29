<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmailLogsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'recipient_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'member_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'member_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'email_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['success', 'failed'],
                'default'    => 'success',
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
            ],
            'created_by' => [
                'type' => 'INT',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('sent_at');
        $this->forge->addKey('email_type');
        $this->forge->addKey('status');
        $this->forge->addKey('recipient_email');
        $this->forge->createTable('email_logs', true);

        $this->db->query("
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
            $this->db->query(
                "INSERT INTO auth_permissions (name, description)
                 SELECT ?, ? FROM DUAL
                 WHERE NOT EXISTS (SELECT 1 FROM auth_permissions WHERE name = ?)",
                [$name, $desc, $name]
            );
        }

        $this->db->query("
            INSERT IGNORE INTO auth_groups_permissions (group_id, permission_id)
            SELECT g.id, p.id
            FROM auth_groups g
            CROSS JOIN auth_permissions p
            WHERE g.id IN (1, 10, 11)
            AND p.name LIKE 'laporan-email/%'
        ");

        if (function_exists('delete_cache')) {
            delete_cache('routes_list');
            delete_cache('display_menu', true);
            delete_cache('list_menu', true);
        }
    }

    public function down()
    {
        $this->db->query("
            DELETE agp FROM auth_groups_permissions agp
            JOIN auth_permissions ap ON ap.id = agp.permission_id
            WHERE ap.name LIKE 'laporan-email/%'
        ");
        $this->db->table('auth_permissions')->like('name', 'laporan-email/', 'after')->delete();
        $this->db->table('c_menus')->where('controller', 'laporan-email')->delete();
        $this->forge->dropTable('email_logs', true);
    }
}
