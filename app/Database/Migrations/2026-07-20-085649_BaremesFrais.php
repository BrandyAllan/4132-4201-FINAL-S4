<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BaremesFrais extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = ON');

        $this->db->query("
            CREATE TABLE IF NOT EXISTS baremes_frais (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                type_operation_id INTEGER NOT NULL,
                montant_min NUMERIC NOT NULL,
                montant_max NUMERIC,
                frais NUMERIC NOT NULL DEFAULT 0,
                actif INTEGER NOT NULL DEFAULT 1
                    CHECK (actif IN (0, 1)),
                date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

                CHECK (montant_min >= 0),
                CHECK (
                    montant_max IS NULL
                    OR montant_max >= montant_min
                ),
                CHECK (frais >= 0),

                FOREIGN KEY (type_operation_id)
                    REFERENCES types_operations(id)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE
            )
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_baremes_type_operation
            ON baremes_frais(type_operation_id)
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_baremes_montants
            ON baremes_frais(montant_min, montant_max)
        ");
    }

    public function down()
    {
        $this->forge->dropTable('baremes_frais', true);
    }
}
