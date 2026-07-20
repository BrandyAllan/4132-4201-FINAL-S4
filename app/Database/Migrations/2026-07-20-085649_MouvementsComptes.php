<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MouvementsComptes extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = ON');

        $this->db->query("
            CREATE TABLE IF NOT EXISTS mouvements_comptes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                operation_id INTEGER NOT NULL,
                compte_id INTEGER NOT NULL,

                sens TEXT NOT NULL
                    CHECK (sens IN ('CREDIT', 'DEBIT')),

                montant NUMERIC NOT NULL
                    CHECK (montant > 0),

                solde_avant NUMERIC NOT NULL,
                solde_apres NUMERIC NOT NULL,
                libelle TEXT,
                date_mouvement DATETIME NOT NULL
                    DEFAULT CURRENT_TIMESTAMP,

                FOREIGN KEY (operation_id)
                    REFERENCES operations(id)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE,

                FOREIGN KEY (compte_id)
                    REFERENCES comptes(id)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE
            )
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_mouvements_operation
            ON mouvements_comptes(operation_id)
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_mouvements_compte
            ON mouvements_comptes(compte_id)
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_mouvements_date
            ON mouvements_comptes(date_mouvement)
        ");
    }

    public function down()
    {
        $this->forge->dropTable('mouvements_comptes', true);
    }
}
