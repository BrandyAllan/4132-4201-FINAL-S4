<?php

namespace App\Models;

use CodeIgniter\Model;

class MouvementCompteModel extends Model
{
    protected $table      = 'mouvements_comptes';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'operation_id', 
        'compte_id', 
        'sens', 
        'montant', 
        'solde_avant', 
        'solde_apres', 
        'libelle', 
        'date_mouvement'
    ];

    protected $useTimestamps = false; 

    protected $returnType = 'array';
}