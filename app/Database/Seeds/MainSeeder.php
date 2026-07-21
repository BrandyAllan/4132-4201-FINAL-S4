<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{

        public function run()
{
    $this->call('OperateurSeeder');    // 1. D'abord les opérateurs
    $this->call('PrefixeSeeder');       // 2. Ensuite les préfixes
    $this->call('TypesOperationsSeeder');
    $this->call('BaremeSeeder');        // 3. Enfin les barèmes
}

}
