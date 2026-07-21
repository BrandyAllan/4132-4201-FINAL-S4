<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Controllers\BaseController;

class LoginClient extends BaseController
{
    public function showLogin(): string 
    {
        $session = session();

        $prefixeModel = new PrefixeModel();
        $operateur_id = 2;
        $session->set('operateur_id', $operateur_id);

        $prefixesActifs = $prefixeModel->recupererPrefixesActifs($operateur_id);

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

        $operateur_id = $session->get('operateur_id');

        /////////////////////////////////////////////////
        $rules = [
            'telephone' => 'required|numeric|exact_length[10]'
        ];
        if (!$this->validate($rules)) {
            $session->setFlashdata('erreur', 'Le numéro de téléphone doit contenir exactement 10 chiffres.');
            return redirect()->back()->withInput();
        }

        /////////////////////////////////////////////////
        $telephone = trim($this->request->getPost('telephone') ?? '');
        $prefixeSaisi = substr($telephone, 0, 3);
        
        if (!$prefixeModel->estPrefixeValide($prefixeSaisi,$operateur_id)) {
            $session->setFlashdata('erreur', 'Ce numéro appartient à un opérateur inactif.');
            return redirect()->back()->withInput();
        }

        /////////////////////////////////////////////////
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

        /////////////////////////////////////////////////
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