<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Controllers\BaseController;

class LoginClient extends BaseController
{
    public function showLogin(): string {
        $prefixeModel = new PrefixeModel();
        $prefixesActifs = $prefixeModel->recupererPrefixesActifs();

        $data = [
            'prefixes' => $prefixesActifs
        ];

        return view('client/login', $data);
    }


    public function doLogin(){
        $session = session();
        $clientModel = new ClientModel();
        $prefixeModel = new PrefixeModel();

        $telephone = trim($this->request->getPost('telephone') ?? '');

        //////////////////////////////////////////////////////////////////////
        if (empty($telephone)) {
            $session->setFlashdata('erreur', 'Veuillez entrer un numéro de téléphone.');
            return redirect()->back()->withInput();
        }

        //////////////////////////////////////////////////////////////////////
        $prefixeSaisi = substr($telephone, 0, 3);
        
        if (!$prefixeModel->estPrefixeValide($prefixeSaisi)) {
            $session->setFlashdata('erreur', 'Ce numéro appartient à un opérateur inactif');
            return redirect()->back()->withInput();
        }

        //////////////////////////////////////////////////////////////////////
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

        //////////////////////////////////////////////////////////////////////
        $session->set([
            'client_id'    => $compte['id'],
            'client_tel'   => $compte['telephone'],
            'est_connecte' => true
        ]);

        return redirect()->to('/client/dashboard');

    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}