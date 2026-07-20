<?php

namespace App\Controllers;

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

    public function addPrefixe() {
        $prefixe = $this->request->getPost('prefixe');

    }
}
