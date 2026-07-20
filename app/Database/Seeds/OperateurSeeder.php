<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class OperateurSeeder extends Seeder
{
    public function run()
    {
        $operateurs = [
            [
                'nom'             => 'Telma',
                'code'            => 'TELMA',
                'actif'           => 1,
                'date_creation'   => date('Y-m-d H:i:s'),
            ],
            [
                'nom'             => 'Orange Madagascar',
                'code'            => 'ORANGE',
                'actif'           => 1,
                'date_creation'   => date('Y-m-d H:i:s'),
            ],
            [
                'nom'             => 'Airtel Madagascar',
                'code'            => 'AIRTEL',
                'actif'           => 1,
                'date_creation'   => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($operateurs as $operateur) {
            $existe = $this->db
                ->table('operateurs')
                ->where('code', $operateur['code'])
                ->countAllResults();

            if ($existe === 0) {
                $this->db
                    ->table('operateurs')
                    ->insert($operateur);
            }
        }

        echo "Opérateurs ajoutés avec succès." . PHP_EOL;
    }
}