<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TypesOperationsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'code'    => 'DEP',
                'libelle' => 'Dépôt d\'argent',
            ],
            [
                'code'    => 'RET',
                'libelle' => 'Retrait d\'argent',
            ],
            [
                'code'    => 'TRA',
                'libelle' => 'Transfert d\'argent',
            ],
        ];

        $builder = $this->db->table('types_operations');
        
        if ($builder->countAllResults(false) === 0) {
            $builder->insertBatch($data);
        }
    }
}