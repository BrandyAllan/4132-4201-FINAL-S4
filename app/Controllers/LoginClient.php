<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Controllers\BaseController;

class LoginClient extends BaseController
{
    public function showLogin(): string 
    {
        $prefixeModel = new PrefixeModel();
        $prefixesActifs = $prefixeModel->recupererPrefixesActifs();

        $data = [
            'prefixes' => $prefixesActifs
        ];

        return view('client/login', $data);
    }


    public function doLogin()
    {
        $session = session();
        $clientModel = new ClientModel();
        $prefixeModel = new PrefixeModel();

        // 1. Définir les règles strictes : requis, numérique, et exactement 10 caractères
        $rules = [
            'telephone' => 'required|numeric|exact_length[10]'
        ];

        if (!$this->validate($rules)) {
            // Renvoie l'erreur si la longueur ou le format n'est pas bon (ex: 9 chiffres)
            $session->setFlashdata('erreur', 'Le numéro de téléphone doit contenir exactement 10 chiffres.');
            return redirect()->back()->withInput();
        }

        $telephone = trim($this->request->getPost('telephone') ?? '');

        // 2. Vérification du préfixe opérateur (032, 033, 034, 038)
        $prefixeSaisi = substr($telephone, 0, 3);
        
        if (!$prefixeModel->estPrefixeValide($prefixeSaisi)) {
            $session->setFlashdata('erreur', 'Ce numéro appartient à un opérateur inactif.');
            return redirect()->back()->withInput();
        }

        // 3. Récupération ou création du compte client
        $compte = $clientModel->where('telephone', $telephone)->first();

        if ($compte) {
            if ($compte['statut'] !== 'ACTIF') {
                $session->setFlashdata('erreur', 'Votre compte est bloqué ou fermé.');
                return redirect()->back();
            }
        } else {
            $donneesNouvelles = [
                'telephone' => $telephone,
                'solde'     => 0,
                'statut'    => 'ACTIF'
            ];
            $idInsere = $clientModel->insert($donneesNouvelles);
            $compte = $clientModel->find($idInsere);
        }

        // 4. Connexion de la session
        $session->set([
            'client_id'    => $compte['id'],
            'telephone'   => $compte['telephone'],
            'est_connecte' => true
        ]);

        return redirect()->to('client/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('connexion/client');
    }

    public function showdashboard()
    {
        if (!session()->has('telephone')) {
        return redirect()->to('connexion/client');
        }

        return view('client/dashboard');
    }
}