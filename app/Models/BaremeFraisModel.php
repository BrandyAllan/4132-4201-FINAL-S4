<?php

namespace App\Models;

use CodeIgniter\Model;

class BaremeFraisModel extends Model
{
    protected $table = 'baremes_frais';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'type_operation_id',
        'montant_min',
        'montant_max',
        'frais',
        'actif',
    ];

    protected $validationRules = [
        'type_operation_id' => 'required|integer',
        'montant_min'       => 'required|decimal|greater_than_equal_to[0]',
        'montant_max'       => 'permit_empty|decimal|greater_than_equal_to[0]',
        'frais'             => 'required|decimal|greater_than_equal_to[0]',
        'actif'             => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'montant_min' => [
            'required' => 'Le montant minimum est obligatoire.',
            'decimal'  => 'Le montant minimum doit être un nombre.',
        ],

        'montant_max' => [
            'decimal' => 'Le montant maximum doit être un nombre.',
        ],

        'frais' => [
            'required' => 'Le montant des frais est obligatoire.',
            'decimal'  => 'Les frais doivent être un nombre.',
        ],
    ];
}