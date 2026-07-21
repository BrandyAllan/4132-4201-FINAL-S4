<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PrefixeSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('prefixes_operateur')->insert([
            'prefixe'      => '038',
            'actif'        => 1,
            'date_creation'=> date('Y-m-d H:i:s'),
            'operateur_id' => 1
        ]);
    }
}