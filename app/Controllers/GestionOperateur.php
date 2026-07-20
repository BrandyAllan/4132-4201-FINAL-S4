<?php

namespace App\Controllers;
use App\Models\UtilisateurModel;
use App\Models\PrefixeModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;

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

    public function showTypeOperation(): string
    {
        $typeModel = new TypeOperationModel();

        $typesOperations = $typeModel
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('operateur/type-operation', [
            'typesOperations' => $typesOperations,
        ]);
    }

    public function showFrais(): string
    {
        return view('operateur/frais');
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

    public function ajouterPrefixe()
    {
        $prefixe = trim(
            (string) $this->request->getPost('prefixe')
        );

        if ($prefixe === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le préfixe est obligatoire.'
                );
        }

        $prefixeModel = new PrefixeModel();

        $existant = $prefixeModel
            ->where('prefixe', $prefixe)
            ->first();

        if ($existant) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Ce préfixe existe déjà.'
                );
        }

        $prefixeModel->insert([
            'prefixe' => $prefixe,
            'actif'   => 1,
        ]);

        return redirect()
            ->back()
            ->with(
                'success',
                'Préfixe ajouté avec succès.'
            );
    }

    public function modifierPrefixe() {
        $id = $this->request->getPost('id');
        $prefixe = $this->request->getPost('prefixe');
        $actif = $this->request->getPost('actif');

        $prefixeModel = new PrefixeModel();
        $prefixeModel->update($id, ['prefixe' => $prefixe, 'actif' => $actif]);

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

    public function showModifierPrefixe($id) {
        $prefixeModel = new PrefixeModel();
        $prefixe = $prefixeModel->find($id);

        if (!$prefixe) {
            return redirect()->back()->with('error', 'Préfixe introuvable.');
        }

        return view('operateur/modifier-prefixe', ['prefixe' => $prefixe]);
    }

    public function ajouterTypeOperation()
    {
        $code = strtoupper(
            trim((string) $this->request->getPost('code'))
        );

        $libelle = trim(
            (string) $this->request->getPost('libelle')
        );

        if ($code === '' || $libelle === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le code et le libellé sont obligatoires.'
                );
        }

        $typeModel = new TypeOperationModel();

        $typeExistant = $typeModel
            ->where('code', $code)
            ->first();

        if ($typeExistant) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Ce code de type d’opération existe déjà.'
                );
        }

        $typeModel->insert([
            'code'     => $code,
            'libelle'  => $libelle,
            'actif'    => 1,
        ]);

        return redirect()
            ->to(site_url('operateur/types-operations'))
            ->with(
                'success',
                'Type d’opération ajouté avec succès.'
            );
    }

    public function showModifierTypeOperation($id) {
        $typeModel = new TypeOperationModel();
        $typeOperation = $typeModel->find($id);

        if(!$typeOperation) {
            return redirect()->back()->with('error', 'Type d\'opération introuvable.');
        }

        return view('operateur/modifier-type-operation', ['typeOperation' => $typeOperation]);
    }

    public function modifierTypeOperation() {
        $id = $this->request->getPost('id');

        $code = trim($this->request->getPost('code'));
        $libelle = trim($this->request->getPost('libelle'));
        $actif = $this->request->getPost('actif');

        $typeModel = new TypeOperationModel();

        $typeModel->update($id, [
            'code' => $code,
            'libelle' => $libelle,
            'actif' => $actif,
        ]);

        return redirect()
            ->to(site_url('operateur/types-operations'))
            ->with('success', 'Type d\'opération modifié avec succès.');
    }

    public function showFraisRetrait(): string
    {
        $typeModel = new TypeOperationModel();
        $baremeModel = new BaremeFraisModel();

        $typeRetrait = $typeModel
            ->where('libelle', 'Retrait')
            ->first();

        $baremes = [];

        if ($typeRetrait) {
            $baremes = $baremeModel
                ->where('type_operation_id', $typeRetrait['id'])
                ->orderBy('montant_min', 'ASC')
                ->findAll();
        }

        return view('operateur/frais-retrait', [
            'typeRetrait' => $typeRetrait,
            'baremes'     => $baremes,
        ]);
    }

    public function ajouterFraisRetrait()
    {
        $typeModel   = new TypeOperationModel();
        $baremeModel = new BaremeFraisModel();

        $typeRetrait = $typeModel
            ->where('libelle', 'Retrait')
            ->first();

        if (!$typeRetrait) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le type d\'opération RETRAIT est introuvable.'
                );
        }

        $montantMinSaisi = trim(
            (string) $this->request->getPost('montant_min')
        );

        $montantMaxSaisi = trim(
            (string) $this->request->getPost('montant_max')
        );

        $fraisSaisi = trim(
            (string) $this->request->getPost('frais')
        );

        $actif = (int) $this->request->getPost('actif');

        if ($montantMinSaisi === '' || $fraisSaisi === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le montant minimum et les frais sont obligatoires.'
                );
        }

        if (
            !is_numeric($montantMinSaisi)
            || !is_numeric($fraisSaisi)
            || (
                $montantMaxSaisi !== ''
                && !is_numeric($montantMaxSaisi)
            )
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Les montants et les frais doivent être numériques.'
                );
        }

        $montantMin = (float) $montantMinSaisi;

        $montantMax = $montantMaxSaisi !== ''
            ? (float) $montantMaxSaisi
            : null;

        $frais = (float) $fraisSaisi;

        if ($montantMin < 0 || $frais < 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Les montants et les frais ne peuvent pas être négatifs.'
                );
        }

        if (
            $montantMax !== null
            && $montantMax < $montantMin
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le montant maximum doit être supérieur ou égal au montant minimum.'
                );
        }

        $baremesActifs = $baremeModel
            ->where(
                'type_operation_id',
                $typeRetrait['id']
            )
            ->where('actif', 1)
            ->findAll();

        $baremeIdentique = null;

        foreach ($baremesActifs as $bareme) {
            $minimumExistant = (float) $bareme['montant_min'];

            $maximumExistant = $bareme['montant_max'] !== null
                ? (float) $bareme['montant_max']
                : null;

            $minimumIdentique =
                $montantMin === $minimumExistant;

            $maximumIdentique =
                (
                    $montantMax === null
                    && $maximumExistant === null
                )
                ||
                (
                    $montantMax !== null
                    && $maximumExistant !== null
                    && $montantMax === $maximumExistant
                );

            if ($minimumIdentique && $maximumIdentique) {
                $baremeIdentique = $bareme;

                continue;
            }

            $nouveauMaximum = $montantMax ?? INF;
            $ancienMaximum  = $maximumExistant ?? INF;

            $chevauchement =
                $montantMin <= $ancienMaximum
                && $nouveauMaximum >= $minimumExistant;

            if ($chevauchement) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Le nouveau barème chevauche un barème actif existant compris entre '
                        . number_format($minimumExistant, 0, ',', ' ')
                        . ' Ar et '
                        . (
                            $maximumExistant !== null
                                ? number_format(
                                    $maximumExistant,
                                    0,
                                    ',',
                                    ' '
                                ) . ' Ar'
                                : 'un montant sans limite'
                        )
                        . '.'
                    );
            }
        }

        $db = db_connect();

        $db->transStart();

        if ($baremeIdentique !== null) {
            $baremeModel->update(
                $baremeIdentique['id'],
                [
                    'actif' => 0,
                ]
            );
        }

        $baremeModel->insert([
            'type_operation_id' => $typeRetrait['id'],
            'montant_min'       => $montantMin,
            'montant_max'       => $montantMax,
            'frais'             => $frais,
            'actif'             => $actif === 0 ? 0 : 1,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Une erreur est survenue pendant l\'enregistrement du barème.'
                );
        }

        if ($baremeIdentique !== null) {
            return redirect()
                ->to(site_url('operateur/frais/retrait'))
                ->with(
                    'success',
                    'Un barème identique existait. Il a été désactivé et le nouveau barème a été enregistré.'
                );
        }

        return redirect()
            ->to(site_url('operateur/frais/retrait'))
            ->with(
                'success',
                'Le nouveau barème de retrait a été ajouté avec succès.'
            );
    }

    public function activerFraisRetrait($id)
    {
        $baremeModel = new BaremeFraisModel();
        $typeModel = new TypeOperationModel();

        $typeRetrait = $typeModel
            ->where('libelle', 'Retrait')
            ->first();

        $bareme = $baremeModel->find($id);

        if (
            !$typeRetrait
            || !$bareme
            || (int) $bareme['type_operation_id'] !== (int) $typeRetrait['id']
        ) {
            return redirect()
                ->to(site_url('operateur/frais/retrait'))
                ->with('error', 'Barème de retrait introuvable.');
        }

        $baremeModel->update($id, [
            'actif' => 1,
        ]);

        return redirect()
            ->to(site_url('operateur/frais/retrait'))
            ->with('success', 'Le barème a été activé.');
    }

    public function desactiverFraisRetrait($id)
    {
        $baremeModel = new BaremeFraisModel();
        $typeModel = new TypeOperationModel();

        $typeRetrait = $typeModel
            ->where('libelle', 'Retrait')
            ->first();

        $bareme = $baremeModel->find($id);

        if (
            !$typeRetrait
            || !$bareme
            || (int) $bareme['type_operation_id'] !== (int) $typeRetrait['id']
        ) {
            return redirect()
                ->to(site_url('operateur/frais/retrait'))
                ->with('error', 'Barème de retrait introuvable.');
        }

        $baremeModel->update($id, [
            'actif' => 0,
        ]);

        return redirect()
            ->to(site_url('operateur/frais/retrait'))
            ->with('success', 'Le barème a été désactivé.');
    }

    public function supprimerFraisRetrait($id)
    {
        $baremeModel = new BaremeFraisModel();
        $typeModel = new TypeOperationModel();

        $typeRetrait = $typeModel
            ->where('libelle', 'Retrait')
            ->first();

        $bareme = $baremeModel->find($id);

        if (
            !$typeRetrait
            || !$bareme
            || (int) $bareme['type_operation_id'] !== (int) $typeRetrait['id']
        ) {
            return redirect()
                ->to(site_url('operateur/frais/retrait'))
                ->with('error', 'Barème de retrait introuvable.');
        }

        $baremeModel->delete($id);

        return redirect()
            ->to(site_url('operateur/frais/retrait'))
            ->with(
                'success',
                'Le barème de retrait a été supprimé.'
            );
    }

    public function showFraisTransfert() {
        $typeModel = new TypeOperationModel();
        $baremeModel = new BaremeFraisModel();

        $typeTransfert = $typeModel
            ->where('libelle', 'Transfert')
            ->first();

        $baremes = [];

        if ($typeTransfert) {
            $baremes = $baremeModel
                ->where('type_operation_id', $typeTransfert['id'])
                ->orderBy('montant_min', 'ASC')
                ->findAll();
        }

        return view('operateur/frais-transfert', [
            'typeTransfert' => $typeTransfert,
            'baremes'     => $baremes,
        ]);
    }

    public function ajouterFraisTransfert()
    {
        $typeModel   = new TypeOperationModel();
        $baremeModel = new BaremeFraisModel();

        $typeRetrait = $typeModel
            ->where('libelle', 'Transfert')
            ->first();

        if (!$typeRetrait) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le type d\'opération Transfert est introuvable.'
                );
        }

        $montantMinSaisi = trim(
            (string) $this->request->getPost('montant_min')
        );

        $montantMaxSaisi = trim(
            (string) $this->request->getPost('montant_max')
        );

        $fraisSaisi = trim(
            (string) $this->request->getPost('frais')
        );

        $actif = (int) $this->request->getPost('actif');

        if ($montantMinSaisi === '' || $fraisSaisi === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le montant minimum et les frais sont obligatoires.'
                );
        }

        if (
            !is_numeric($montantMinSaisi)
            || !is_numeric($fraisSaisi)
            || (
                $montantMaxSaisi !== ''
                && !is_numeric($montantMaxSaisi)
            )
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Les montants et les frais doivent être numériques.'
                );
        }

        $montantMin = (float) $montantMinSaisi;

        $montantMax = $montantMaxSaisi !== ''
            ? (float) $montantMaxSaisi
            : null;

        $frais = (float) $fraisSaisi;

        if ($montantMin < 0 || $frais < 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Les montants et les frais ne peuvent pas être négatifs.'
                );
        }

        if (
            $montantMax !== null
            && $montantMax < $montantMin
        ) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le montant maximum doit être supérieur ou égal au montant minimum.'
                );
        }

        $baremesActifs = $baremeModel
            ->where(
                'type_operation_id',
                $typeRetrait['id']
            )
            ->where('actif', 1)
            ->findAll();

        $baremeIdentique = null;

        foreach ($baremesActifs as $bareme) {
            $minimumExistant = (float) $bareme['montant_min'];

            $maximumExistant = $bareme['montant_max'] !== null
                ? (float) $bareme['montant_max']
                : null;

            $minimumIdentique =
                $montantMin === $minimumExistant;

            $maximumIdentique =
                (
                    $montantMax === null
                    && $maximumExistant === null
                )
                ||
                (
                    $montantMax !== null
                    && $maximumExistant !== null
                    && $montantMax === $maximumExistant
                );

            if ($minimumIdentique && $maximumIdentique) {
                $baremeIdentique = $bareme;

                continue;
            }

            $nouveauMaximum = $montantMax ?? INF;
            $ancienMaximum  = $maximumExistant ?? INF;

            $chevauchement =
                $montantMin <= $ancienMaximum
                && $nouveauMaximum >= $minimumExistant;

            if ($chevauchement) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Le nouveau barème chevauche un barème actif existant compris entre '
                        . number_format($minimumExistant, 0, ',', ' ')
                        . ' Ar et '
                        . (
                            $maximumExistant !== null
                                ? number_format(
                                    $maximumExistant,
                                    0,
                                    ',',
                                    ' '
                                ) . ' Ar'
                                : 'un montant sans limite'
                        )
                        . '.'
                    );
            }
        }

        $db = db_connect();

        $db->transStart();

        if ($baremeIdentique !== null) {
            $baremeModel->update(
                $baremeIdentique['id'],
                [
                    'actif' => 0,
                ]
            );
        }

        $baremeModel->insert([
            'type_operation_id' => $typeRetrait['id'],
            'montant_min'       => $montantMin,
            'montant_max'       => $montantMax,
            'frais'             => $frais,
            'actif'             => $actif === 0 ? 0 : 1,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Une erreur est survenue pendant l\'enregistrement du barème.'
                );
        }

        if ($baremeIdentique !== null) {
            return redirect()
                ->to(site_url('operateur/frais/transfert'))
                ->with(
                    'success',
                    'Un barème identique existait. Il a été désactivé et le nouveau barème a été enregistré.'
                );
        }

        return redirect()
            ->to(site_url('operateur/frais/transfert'))
            ->with(
                'success',
                'Le nouveau barème de transfert a été ajouté avec succès.'
            );
    }

    public function activerFraisTransfert($id)
    {
        $baremeModel = new BaremeFraisModel();
        $typeModel = new TypeOperationModel();

        $typeTransfert = $typeModel
            ->where('libelle', 'Transfert')
            ->first();

        $bareme = $baremeModel->find($id);

        if (
            !$typeTransfert
            || !$bareme
            || (int) $bareme['type_operation_id'] !== (int) $typeTransfert['id']
        ) {
            return redirect()
                ->to(site_url('operateur/frais/transfert'))
                ->with('error', 'Barème de transfert introuvable.');
        }

        $baremeModel->update($id, [
            'actif' => 1,
        ]);

        return redirect()
            ->to(site_url('operateur/frais/transfert'))
            ->with('success', 'Le barème a été activé.');
    }

    public function desactiverFraisTransfert($id)
    {
        $baremeModel = new BaremeFraisModel();
        $typeModel = new TypeOperationModel();

        $typeTransfert = $typeModel
            ->where('libelle', 'Transfert')
            ->first();

        $bareme = $baremeModel->find($id);

        if (
            !$typeTransfert
            || !$bareme
            || (int) $bareme['type_operation_id'] !== (int) $typeTransfert['id']
        ) {
            return redirect()
                ->to(site_url('operateur/frais/transfert'))
                ->with('error', 'Barème de transfert introuvable.');
        }

        $baremeModel->update($id, [
            'actif' => 0,
        ]);

        return redirect()
            ->to(site_url('operateur/frais/transfert'))
            ->with('success', 'Le barème a été désactivé.');
    }

    public function supprimerFraisTransfert($id)
    {
        $baremeModel = new BaremeFraisModel();
        $typeModel = new TypeOperationModel();

        $typeTransfert = $typeModel
            ->where('libelle', 'Transfert')
            ->first();

        $bareme = $baremeModel->find($id);

        if (
            !$typeTransfert
            || !$bareme
            || (int) $bareme['type_operation_id'] !== (int) $typeTransfert['id']
        ) {
            return redirect()
                ->to(site_url('operateur/frais/transfert'))
                ->with('error', 'Barème de transfert introuvable.');
        }

        $baremeModel->delete($id);

        return redirect()
            ->to(site_url('operateur/frais/transfert'))
            ->with(
                'success',
                'Le barème de transfert a été supprimé.'
            );
    }
}
