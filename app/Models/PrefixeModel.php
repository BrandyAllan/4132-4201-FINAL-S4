<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeModel extends Model
{
    protected $table            = 'prefixes_operateur';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields = [
        'prefixe',
        'operateur_id',
        'actif',
        'date_creation',
    ];


    public function recupererPrefixesActifs(): array
    {
        $resultats = $this->where('actif', 1)->findAll();
        return array_column($resultats, 'prefixe');
    }


    public function estPrefixeValide(string $prefixe): bool
    {
        $check = $this->where('prefixe', $prefixe)
                      ->where('actif', 1)
                      ->first();
                      
        return $check !== null;
    }
}