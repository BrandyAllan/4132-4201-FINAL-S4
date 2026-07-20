<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperateurs extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],

            'nom' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],

            'code' => [
                'type'       => 'TEXT',
                'null'       => false,
                'unique'     => true,
            ],

            'actif' => [
                'type'       => 'INTEGER',
                'default'    => 1,
                'null'       => false,
            ],

            'date_creation' => [
                'type'       => 'DATETIME',
                'null'       => false,
                'default'    => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nom');

        $this->forge->createTable('operateurs');
    }

    public function down()
    {
        $this->forge->dropTable('operateurs');
    }
}