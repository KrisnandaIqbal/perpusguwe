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
    }

    public function down()
    {
        $this->forge->dropTable('email_logs', true);
    }
}
