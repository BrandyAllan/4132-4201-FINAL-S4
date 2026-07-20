<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommissionToBaremesFrais extends Migration
{
    public function up()
    {
        $fields = [
            'commission' => [
                'type'       => 'NUMERIC',
                'null'       => false,
                'default'    => 0,
                'constraint' => 'CHECK (commission >= 0)',
                'after'      => 'frais',
            ],
        ];

        $this->forge->addColumn('baremes_frais', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn(
            'baremes_frais',
            'commission'
        );
    }
}