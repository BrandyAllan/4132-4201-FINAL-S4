<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Operations extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = ON');

        $this->db->query("
            CREATE TABLE IF NOT EXISTS operations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                reference TEXT NOT NULL UNIQUE,
                type_operation_id INTEGER NOT NULL,
                compte_source_id INTEGER,
                compte_destination_id INTEGER,

                montant NUMERIC NOT NULL,
                frais NUMERIC NOT NULL DEFAULT 0,
                montant_total NUMERIC NOT NULL,

                statut TEXT NOT NULL DEFAULT 'VALIDEE'
                    CHECK (
                        statut IN (
                            'EN_ATTENTE',
                            'VALIDEE',
                            'ANNULEE',
                            'ECHOUEE'
                        )
                    ),

                motif TEXT,
                date_operation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

                CHECK (montant > 0),
                CHECK (frais >= 0),
                CHECK (montant_total >= montant),

                FOREIGN KEY (type_operation_id)
                    REFERENCES types_operations(id)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE,

                FOREIGN KEY (compte_source_id)
                    REFERENCES comptes(id)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE,

                FOREIGN KEY (compte_destination_id)
                    REFERENCES comptes(id)
                    ON DELETE RESTRICT
                    ON UPDATE CASCADE
            )
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_operations_type
            ON operations(type_operation_id)
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_operations_source
            ON operations(compte_source_id)
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_operations_destination
            ON operations(compte_destination_id)
        ");

        $this->db->query("
            CREATE INDEX IF NOT EXISTS idx_operations_date
            ON operations(date_operation)
        ");
    }

    public function down()
    {
        $this->forge->dropTable('operations', true);
    }
}
