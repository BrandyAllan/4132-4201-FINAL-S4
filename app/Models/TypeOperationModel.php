<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table = 'types_operations';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'code',
        'libelle',
        'actif',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'code' => 'required|max_length[30]',
        'libelle' => 'required|max_length[100]',
        'actif' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'code' => [
            'required' => 'Le code est obligatoire.',
            'max_length' => 'Le code ne doit pas dépasser 30 caractères.',
        ],
        'libelle' => [
            'required' => 'Le libellé est obligatoire.',
            'max_length' => 'Le libellé ne doit pas dépasser 100 caractères.',
        ],
    ];
}