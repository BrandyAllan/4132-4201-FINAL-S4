<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TypesOperations extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = ON');

        $this->db->query("
            CREATE TABLE IF NOT EXISTS types_operations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL UNIQUE,
                libelle TEXT NOT NULL,
                date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down()
    {
        $this->forge->dropTable('types_operations', true);
    }
}
