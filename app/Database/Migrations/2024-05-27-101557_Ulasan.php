<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Ulasan extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_ulasan' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
            ],
            'buku_id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
            ],
            'rating' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],

            'komentar' => [
                'type'           => 'VARCHAR',
                'constraint'     => 225,
                'null'           => true,
            ],
            'tanggal_ulasan' => [
                'type'           => 'DATE',
                'null'           => true,
            ],

        ]);
        
        $this->forge->addKey('id_ulasan', true);
        $this->forge->createTable('ulasan');
    }

    public function down()
    {
        $this->forge->dropTable('ulasan');
    }
}
