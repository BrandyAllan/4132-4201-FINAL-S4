<?php

namespace App\Models;

use CodeIgniter\Model;

class UtilisateurModel extends Model
{
    protected $table            = 'utilisateurs';
    protected $primaryKey       = 'id';

    protected $returnType       = 'array';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'mot_de_passe',
        'role'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'nom' => 'required|min_length[2]|max_length[100]',
        'prenom' => 'required|min_length[2]|max_length[100]',
        'email' => 'required|valid_email|is_unique[utilisateurs.email,id,{id}]',
        'mot_de_passe' => 'required|min_length[6]',
        'role' => 'required|in_list[ADMIN,OPERATEUR]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => "Cette adresse email existe déjà."
        ]
    ];

    protected $skipValidation = false;
}