<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PrefixesOperateur extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = ON');

        $this->db->query("
            CREATE TABLE IF NOT EXISTS prefixes_operateur (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                prefixe TEXT NOT NULL UNIQUE,
                actif INTEGER NOT NULL DEFAULT 1
                    CHECK (actif IN (0, 1)),
                date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down()
    {
        $this->forge->dropTable('prefixes_operateur', true);
    }
}
