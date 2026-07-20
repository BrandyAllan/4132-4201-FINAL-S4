<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaremeSeeder extends Seeder
{
    public function run()
    {
        $data = [

            ['montant_min' => 1001,     'montant_max' => 5000,     'frais' => 70],
            ['montant_min' => 5001,     'montant_max' => 10000,    'frais' => 150],
            ['montant_min' => 10001,    'montant_max' => 25000,    'frais' => 250],
            ['montant_min' => 25001,    'montant_max' => 50000,    'frais' => 500],
            ['montant_min' => 50001,    'montant_max' => 100000,   'frais' => 1000],
            ['montant_min' => 100001,   'montant_max' => 250000,   'frais' => 1900],
            ['montant_min' => 250001,   'montant_max' => 500000,   'frais' => 1900],
            ['montant_min' => 500001,   'montant_max' => 1000000,  'frais' => 3200],
            ['montant_min' => 1000001,  'montant_max' => 2000000,  'frais' => 3800],
            ['montant_min' => 2000001,  'montant_max' => 3000000,  'frais' => 5000],
            ['montant_min' => 3000001,  'montant_max' => 4000000,  'frais' => 6300],
            ['montant_min' => 4000001,  'montant_max' => 5000000,  'frais' => 7500],
            ['montant_min' => 5000001,  'montant_max' => 6000000,  'frais' => 9400],
            ['montant_min' => 6000001,  'montant_max' => 7000000,  'frais' => 10700],
            ['montant_min' => 7000001,  'montant_max' => 8000000,  'frais' => 12500],
            ['montant_min' => 8000001,  'montant_max' => 9000000,  'frais' => 14400],
            ['montant_min' => 9000001,  'montant_max' => 10000000, 'frais' => 15700],
            ['montant_min' => 10000001, 'montant_max' => 11000000, 'frais' => 17500],
            ['montant_min' => 11000001, 'montant_max' => 12000000, 'frais' => 18800],
            ['montant_min' => 12000001, 'montant_max' => 13000000, 'frais' => 20000],
            ['montant_min' => 13000001, 'montant_max' => 14000000, 'frais' => 21300],
            ['montant_min' => 14000001, 'montant_max' => 15000000, 'frais' => 23200],
            ['montant_min' => 15000001, 'montant_max' => 16000000, 'frais' => 25000],
            ['montant_min' => 16000001, 'montant_max' => 17000000, 'frais' => 26300],
            ['montant_min' => 17000001, 'montant_max' => 18000000, 'frais' => 28200],
            ['montant_min' => 18000001, 'montant_max' => 19000000, 'frais' => 30000],

        ];

        foreach ($data as $item) {

            $this->db->table('baremes_frais')->insert([
                'type_operation_id' => 3,
                'montant_min'       => $item['montant_min'],
                'montant_max'       => $item['montant_max'],
                'frais'             => $item['frais'],
                'actif'             => 1,
            ]);

        }
    }
}