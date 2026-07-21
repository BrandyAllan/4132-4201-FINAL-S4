<?php

namespace App\Models;

use CodeIgniter\Model;

class RemiseModel extends Model
{
    protected $table = 'remise';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'pourcentage',
        'date_creation',
        'actif',
    ];

    protected $useTimestamps = false;
}