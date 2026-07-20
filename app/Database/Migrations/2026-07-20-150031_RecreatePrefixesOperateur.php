<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RecreatePrefixesOperateur extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = OFF');

        $this->forge->dropTable(
            'prefixes_operateur_nouveau',
            true
        );

        $this->forge->dropTable(
            'prefixes_operateur',
            true
        );

        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],

            'prefixe' => [
                'type'       => 'TEXT',
                'null'       => false,
                'unique'     => true,
            ],

            'operateur_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],

            'actif' => [
                'type'       => 'INTEGER',
                'default'    => 1,
                'null'       => false,
                'constraint' => 'CHECK (actif IN (0,1))',
            ],

            'date_creation' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('prefixe');

        $this->forge->addForeignKey(
            'operateur_id',
            'operateurs',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('prefixes_operateur');

        $this->db->query('PRAGMA foreign_keys = ON');
    }

    public function down()
    {
        $this->db->query('PRAGMA foreign_keys = OFF');

        $this->forge->dropTable(
            'prefixes_operateur',
            true
        );

        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],

            'prefixe' => [
                'type'       => 'TEXT',
                'null'       => false,
                'unique'     => true,
            ],

            'actif' => [
                'type'       => 'INTEGER',
                'default'    => 1,
                'null'       => false,
                'constraint' => 'CHECK (actif IN (0,1))',
            ],

            'date_creation' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('prefixe');

        $this->forge->createTable('prefixes_operateur');

        $this->db->query('PRAGMA foreign_keys = ON');
    }
}