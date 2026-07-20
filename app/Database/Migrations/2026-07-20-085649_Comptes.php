<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Comptes extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = ON');

        $this->db->query("
            CREATE TABLE IF NOT EXISTS comptes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                telephone TEXT NOT NULL UNIQUE,
                solde NUMERIC NOT NULL DEFAULT 0
                    CHECK (solde >= 0),
                statut TEXT NOT NULL DEFAULT 'ACTIF'
                    CHECK (statut IN ('ACTIF', 'BLOQUE', 'FERME')),
                date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                date_modification DATETIME
            )
        ");
    }

    public function down()
    {
        $this->forge->dropTable('comptes', true);
    }
}
