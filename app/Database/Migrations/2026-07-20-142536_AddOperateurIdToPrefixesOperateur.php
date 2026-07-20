<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOperateurIdToPrefixesOperateur extends Migration
{
    public function up()
    {
        $this->db->query('PRAGMA foreign_keys = OFF');

        $this->db->transBegin();

        try {
            $this->db->query(
                'CREATE TABLE prefixes_operateur_nouveau (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    prefixe TEXT NOT NULL UNIQUE,
                    operateur_id INTEGER,
                    actif INTEGER NOT NULL DEFAULT 1
                        CHECK (actif IN (0, 1)),
                    date_creation DATETIME NOT NULL
                        DEFAULT CURRENT_TIMESTAMP,

                    FOREIGN KEY (operateur_id)
                        REFERENCES operateurs(id)
                        ON DELETE RESTRICT
                        ON UPDATE CASCADE
                )'
            );

            $this->db->query(
                'INSERT INTO prefixes_operateur_nouveau (
                    id,
                    prefixe,
                    actif,
                    date_creation
                )
                SELECT
                    id,
                    prefixe,
                    actif,
                    date_creation
                FROM prefixes_operateur'
            );

            $this->db->query(
                'DROP TABLE prefixes_operateur'
            );

            $this->db->query(
                'ALTER TABLE prefixes_operateur_nouveau
                 RENAME TO prefixes_operateur'
            );

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException(
                    'La reconstruction de prefixes_operateur a échoué.'
                );
            }

            $this->db->transCommit();
        } catch (\Throwable $exception) {
            $this->db->transRollback();

            throw $exception;
        } finally {
            $this->db->query('PRAGMA foreign_keys = ON');
        }
    }

    public function down()
    {
        $this->db->query('PRAGMA foreign_keys = OFF');

        $this->db->transBegin();

        try {
            $this->db->query(
                'CREATE TABLE prefixes_operateur_ancien (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    prefixe TEXT NOT NULL UNIQUE,
                    actif INTEGER NOT NULL DEFAULT 1
                        CHECK (actif IN (0, 1)),
                    date_creation DATETIME NOT NULL
                        DEFAULT CURRENT_TIMESTAMP
                )'
            );

            $this->db->query(
                'INSERT INTO prefixes_operateur_ancien (
                    id,
                    prefixe,
                    actif,
                    date_creation
                )
                SELECT
                    id,
                    prefixe,
                    actif,
                    date_creation
                FROM prefixes_operateur'
            );

            $this->db->query(
                'DROP TABLE prefixes_operateur'
            );

            $this->db->query(
                'ALTER TABLE prefixes_operateur_ancien
                 RENAME TO prefixes_operateur'
            );

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException(
                    'La restauration de prefixes_operateur a échoué.'
                );
            }

            $this->db->transCommit();
        } catch (\Throwable $exception) {
            $this->db->transRollback();

            throw $exception;
        } finally {
            $this->db->query('PRAGMA foreign_keys = ON');
        }
    }
}