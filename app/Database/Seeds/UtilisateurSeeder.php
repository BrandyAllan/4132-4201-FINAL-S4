<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UtilisateurSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nom'            => 'Rakoto',
                'prenom'         => 'Jean',
                'email'          => 'operateur@gmail.com',
                'mot_de_passe'   => password_hash('operateur123', PASSWORD_DEFAULT),
                'role'           => 'OPERATEUR',
            ]
        ];

        $this->db->table('utilisateurs')->insertBatch($data);
    }
}