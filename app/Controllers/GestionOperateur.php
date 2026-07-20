<?php

namespace App\Controllers;
use App\Models\UtilisateurModel;
use App\Models\PrefixeModel;

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

    public function showPrefixe(): string
    {
        $prefixeModel = new PrefixeModel();
        $prefixes = $prefixeModel->findAll();
        return view('operateur/prefixe', ['prefixes' => $prefixes]);
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

    public function ajouterPrefixe() {
        $prefixe = $this->request->getPost('prefixe');

        $prefixeModel = new PrefixeModel();
        $prefixeModel->insert(['prefixe' => $prefixe, 'actif' => 1]);

        return redirect()->back()->with('success', 'Préfixe ajouté avec succès.');
    }

    public function modifierPrefixe() {
        $id = $this->request->getPost('id');
        $prefixe = $this->request->getPost('prefixe');

        $prefixeModel = new PrefixeModel();
        $prefixeModel->update($id, ['prefixe' => $prefixe]);

        return redirect()->back()->with('success', 'Préfixe modifié avec succès.');
    }

    public function desactiverPrefixe($id) {
        $prefixeModel = new PrefixeModel();
        $prefixeModel->update($id, ['actif' => 0]);

        return redirect()->back()->with('success', 'Préfixe supprimé avec succès.');
    }

    public function activerPrefixe($id) {
        $prefixeModel = new PrefixeModel();
        $prefixeModel->update($id, ['actif' => 1]);

        return redirect()->back()->with('success', 'Préfixe activé avec succès.');
    }

}
