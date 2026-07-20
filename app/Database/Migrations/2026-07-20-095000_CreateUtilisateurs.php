<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUtilisateurs extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE utilisateurs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT NOT NULL,
                prenom TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                mot_de_passe TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'OPERATEUR',
                date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function down()
    {
        $this->forge->dropTable('utilisateurs', true);
    }
}