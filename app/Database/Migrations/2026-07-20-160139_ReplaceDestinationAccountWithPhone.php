<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Throwable;

class ReplaceDestinationAccountWithPhone extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = OFF');

        try {
            $this->db->transStart();

            $this->db->query(
                'DROP TABLE IF EXISTS operations_nouveau'
            );

            $this->db->query("
                CREATE TABLE operations_nouveau (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,

                    reference TEXT NOT NULL UNIQUE,

                    type_operation_id INTEGER NOT NULL,

                    compte_source_id INTEGER,

                    telephone_destinataire TEXT,

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

                    date_operation DATETIME NOT NULL
                        DEFAULT CURRENT_TIMESTAMP,

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
                        ON UPDATE CASCADE
                )
            ");

            if ($this->db->tableExists('operations')) {
                /*
                 * Les anciennes destinations sont transformées
                 * en numéros de téléphone à partir de la table comptes.
                 */
                $this->db->query("
                    INSERT INTO operations_nouveau (
                        id,
                        reference,
                        type_operation_id,
                        compte_source_id,
                        telephone_destinataire,
                        montant,
                        frais,
                        montant_total,
                        statut,
                        motif,
                        date_operation
                    )
                    SELECT
                        o.id,
                        o.reference,
                        o.type_operation_id,
                        o.compte_source_id,
                        cd.telephone,
                        o.montant,
                        o.frais,
                        o.montant_total,
                        o.statut,
                        o.motif,
                        o.date_operation
                    FROM operations o
                    LEFT JOIN comptes cd
                        ON cd.id = o.compte_destination_id
                ");

                $this->db->query(
                    'DROP TABLE operations'
                );
            }

            $this->db->query("
                ALTER TABLE operations_nouveau
                RENAME TO operations
            ");

            $this->db->query("
                CREATE INDEX IF NOT EXISTS
                idx_operations_type
                ON operations(type_operation_id)
            ");

            $this->db->query("
                CREATE INDEX IF NOT EXISTS
                idx_operations_source
                ON operations(compte_source_id)
            ");

            $this->db->query("
                CREATE INDEX IF NOT EXISTS
                idx_operations_destination
                ON operations(telephone_destinataire)
            ");

            $this->db->query("
                CREATE INDEX IF NOT EXISTS
                idx_operations_date
                ON operations(date_operation)
            ");

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException(
                    'Échec de la modification de la table operations.'
                );
            }
        } catch (Throwable $exception) {
            $this->db->transRollback();

            throw $exception;
        } finally {
            $this->db->query('PRAGMA foreign_keys = ON');
        }
    }

    public function down()
    {
        $this->db->query('PRAGMA foreign_keys = OFF');

        try {
            $this->db->transStart();

            $this->db->query(
                'DROP TABLE IF EXISTS operations_ancien'
            );

            $this->db->query("
                CREATE TABLE operations_ancien (
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

                    date_operation DATETIME NOT NULL
                        DEFAULT CURRENT_TIMESTAMP,

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

            if ($this->db->tableExists('operations')) {
                /*
                 * Les numéros Orange présents dans comptes seront
                 * reconvertis en compte_destination_id.
                 *
                 * Les destinations externes Telma/Airtel deviendront NULL.
                 */
                $this->db->query("
                    INSERT INTO operations_ancien (
                        id,
                        reference,
                        type_operation_id,
                        compte_source_id,
                        compte_destination_id,
                        montant,
                        frais,
                        montant_total,
                        statut,
                        motif,
                        date_operation
                    )
                    SELECT
                        o.id,
                        o.reference,
                        o.type_operation_id,
                        o.compte_source_id,
                        cd.id,
                        o.montant,
                        o.frais,
                        o.montant_total,
                        o.statut,
                        o.motif,
                        o.date_operation
                    FROM operations o
                    LEFT JOIN comptes cd
                        ON cd.telephone =
                           o.telephone_destinataire
                ");

                $this->db->query(
                    'DROP TABLE operations'
                );
            }

            $this->db->query("
                ALTER TABLE operations_ancien
                RENAME TO operations
            ");

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException(
                    'Échec de la restauration de la table operations.'
                );
            }
        } catch (Throwable $exception) {
            $this->db->transRollback();

            throw $exception;
        } finally {
            $this->db->query('PRAGMA foreign_keys = ON');
        }
    }
}