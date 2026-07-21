<?php

namespace App\Models;

use CodeIgniter\Model;

class EpargneModel extends Model
{
    protected $table = 'epargne';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'compte_id',
        'solde',
    ];

    protected $useTimestamps = false;
}