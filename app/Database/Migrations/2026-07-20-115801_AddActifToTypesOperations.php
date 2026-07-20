<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddActifToTypesOperations extends Migration
{
    public function up()
    {
        $this->forge->addColumn('types_operations', [
            'actif' => [
                'type'       => 'INTEGER',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'after'      => 'libelle',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('types_operations', 'actif');
    }
}