<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Peminjaman extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_peminjaman' => [
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
            'tanggal_peminjaman' => [
                'type'           => 'DATE',
                'null'           => true,
            ],
            'tanggal_pengembalian' => [
                'type'           => 'DATE',
                'null'           => true,
            ],
            'status_peminjaman' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => true,
            ],
            'denda' => [
                'type'           => 'DECIMAL',
                'constraint'     => '10,2',
                'null'           => true,
            ]
        ]);

        $this->forge->addPrimaryKey('id_peminjaman');
        $this->forge->createTable('peminjaman');
    }

    public function down()
    {
        $this->forge->dropTable('peminjaman');
    }
}
