<?php

namespace App\Controllers;
use App\Models\UtilisateurModel;

class GestionOperateur extends BaseController
{
    public function index(): string
    {
        return view('operateur/gestion');
    }

    public function showFormLogin(): string
    {
        return view('operateur/login');
    }

    public function showFormPrefixe(): string
    {
        return view('operateur/ajouter-prefixe');
    }

    public function showFormCompte(): string
    {
        return view('operateur/ajouter-compte');
    }

    public function showFormTypeOperation(): string
    {
        return view('operateur/ajouter-type-operation');
    }

    public function showFormBaremeFrais(): string
    {
        return view('operateur/ajouter-bareme-frais');
    }

    public function doLogin() {
        $identifiant = $this->request->getPost('identifiant');
        $mot_de_passe = $this->request->getPost('mot_de_passe');

        $UtilisateurModel = new UtilisateurModel();

        $utilisateur = $UtilisateurModel->where('email', $identifiant)->first();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            session()->set('utilisateur', $utilisateur);
            return redirect()->to(site_url('operateur/gestion'));
        } else {
            return redirect()->back()->withInput()->with('error', 'Identifiant ou mot de passe incorrect.');
        }
    }

    public function logout() {
        session()->remove('utilisateur');
        return redirect()->to(site_url('/'));
    }

    public function addPrefixe() {
        $prefixe = $this->request->getPost('prefixe');

    }

}
