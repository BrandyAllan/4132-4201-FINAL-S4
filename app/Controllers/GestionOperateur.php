<?php

namespace App\Controllers;
use App\Models\UtilisateurModel;
use App\Models\PrefixeModel;
use App\Models\TypeOperationModel;
use App\Models\BaremeFraisModel;
use App\Models\ClientModel;
use App\Models\OperateurModel;

class GestionOperateur extends BaseController
{
    public function index(): string
    {
        $db = db_connect();

        $totalOperations = $db
            ->table('operations')
            ->countAllResults();

        $montantTotalResult = $db
            ->table('operations')
            ->selectSum('montant', 'montant_total')
            ->get()
            ->getRowArray();

        $montantTotal = (float) (
            $montantTotalResult['montant_total'] ?? 0
        );

        $totalFraisResult = $db
            ->table('operations')
            ->selectSum('frais', 'total_frais')
            ->get()
            ->getRowArray();

        $totalFrais = (float) (
            $totalFraisResult['total_frais'] ?? 0
        );

        $comptesActifs = $db
            ->table('comptes')
            ->where('statut', 'ACTIF')
            ->countAllResults();

        $gainTotal = $totalFrais;

        $resultatGainRetraits = $db
            ->table('operations')
            ->selectSum('frais', 'gain_retraits')
            ->where('type_operation_id', 1)
            ->get()
            ->getRowArray();

        $gainRetraits = (float) (
            $resultatGainRetraits['gain_retraits'] ?? 0
        );

        $resultatGainTransferts = $db
            ->table('operations')
            ->selectSum('frais', 'gain_transferts')
            ->where('type_operation_id', 3)
            ->get()
            ->getRowArray();

        $gainTransferts = (float) (
            $resultatGainTransferts['gain_transferts'] ?? 0
        );

        $retraitsParJour = $db
            ->table('operations')
            ->select(
                'DATE(date_operation) AS jour, '
                . 'SUM(frais) AS total',
                false
            )
            ->where('type_operation_id', 1)
            ->where(
                'date_operation >=',
                date(
                    'Y-m-d 00:00:00',
                    strtotime('-29 days')
                )
            )
            ->groupBy('DATE(date_operation)')
            ->orderBy('jour', 'ASC')
            ->get()
            ->getResultArray();

        $transfertsParJour = $db
            ->table('operations')
            ->select(
                'DATE(date_operation) AS jour, '
                . 'SUM(frais) AS total',
                false
            )
            ->where('type_operation_id', 3)
            ->where(
                'date_operation >=',
                date(
                    'Y-m-d 00:00:00',
                    strtotime('-29 days')
                )
            )
            ->groupBy('DATE(date_operation)')
            ->orderBy('jour', 'ASC')
            ->get()
            ->getResultArray();

        $retraitsParDate = [];

        foreach ($retraitsParJour as $ligne) {
            $retraitsParDate[$ligne['jour']] =
                (float) $ligne['total'];
        }

        $transfertsParDate = [];

        foreach ($transfertsParJour as $ligne) {
            $transfertsParDate[$ligne['jour']] =
                (float) $ligne['total'];
        }

        $labelsGraphique = [];
        $gainsRetraitsGraphique = [];
        $gainsTransfertsGraphique = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = date(
                'Y-m-d',
                strtotime("-{$i} days")
            );

            $labelsGraphique[] = date(
                'd/m',
                strtotime($date)
            );

            $gainsRetraitsGraphique[] =
                $retraitsParDate[$date] ?? 0;

            $gainsTransfertsGraphique[] =
                $transfertsParDate[$date] ?? 0;
        }

        $gainCommissions = $db->table('operations o')
            ->select(
                'COALESCE(SUM(
                    o.montant * b.commission / 100
                ), 0) AS total_commission',
                false
            )
            ->join(
                'types_operations t',
                't.id = o.type_operation_id'
            )
            ->join(
                'baremes_frais b',
                'b.type_operation_id = o.type_operation_id
                AND o.montant >= b.montant_min
                AND (
                    b.montant_max IS NULL
                    OR o.montant <= b.montant_max
                )',
                'left'
            )
            ->where('t.code', 'TRANS')
            ->where('o.statut', 'VALIDEE')
            ->where('b.actif', 1)
            ->get()
            ->getRow()
            ->total_commission ?? 0;

        $gainsCommissionsGraphique = $db->query("
            SELECT
                strftime('%m', o.date_operation) AS mois,

                COALESCE(
                    SUM(
                        o.montant * b.commission / 100
                    ),
                    0
                ) AS total

            FROM operations o

            INNER JOIN types_operations t
                ON t.id = o.type_operation_id

            INNER JOIN baremes_frais b
                ON b.type_operation_id = o.type_operation_id
                AND o.montant >= b.montant_min
                AND (
                    b.montant_max IS NULL
                    OR o.montant <= b.montant_max
                )

            WHERE o.statut = 'VALIDEE'
            AND t.code = 'TRANS'
            AND b.actif = 1

            GROUP BY strftime('%m', o.date_operation)

            ORDER BY strftime('%m', o.date_operation)
        ")->getResultArray();

        $montantsParOperateur = $db->query("
            SELECT
                op.id AS operateur_id,
                op.nom AS operateur,
                op.code,

                COUNT(o.id) AS nombre_transferts,

                COALESCE(
                    SUM(o.montant),
                    0
                ) AS montant_envoye

            FROM operateurs op

            INNER JOIN prefixes_operateur p
                ON p.operateur_id = op.id
                AND p.actif = 1

            LEFT JOIN operations o
                ON SUBSTR(
                    o.telephone_destinataire,
                    1,
                    LENGTH(p.prefixe)
                ) = p.prefixe

                AND o.statut = 'VALIDEE'

                AND o.type_operation_id = (
                    SELECT id
                    FROM types_operations
                    WHERE code = 'TRANSFERT'
                    LIMIT 1
                )

            WHERE op.actif = 1

            GROUP BY
                op.id,
                op.nom,
                op.code

            ORDER BY montant_envoye DESC
        ")->getResultArray();

        return view('operateur/gestion', [
            'gainTotal'                => $gainTotal,
            'gainRetraits'             => $gainRetraits,
            'gainTransferts'           => $gainTransferts,
            'totalOperations'          => $totalOperations,
            'montantTotal'             => $montantTotal,
            'totalFrais'               => $totalFrais,
            'comptesActifs'            => $comptesActifs,
            'labelsGraphique'          => $labelsGraphique,
            'gainsRetraitsGraphique'   => $gainsRetraitsGraphique,
            'gainsTransfertsGraphique' => $gainsTransfertsGraphique,
            'gainsCommissions' => $gainCommissions,
            'gainsCommissionsGraphique' => $gainsCommissionsGraphique,
            'montantsParOperateur' => $montantsParOperateur,
        ]);
    }

    public function showFormLogin(): string
    {
        return view('operateur/login');
    }

    public function showPrefixe(): string
    {
        $prefixeModel = new PrefixeModel();
        $prefixes = $prefixeModel->findAll();

        $operateurModel = new OperateurModel();
        $operateurs = $operateurModel->findAll();

        return view('operateur/prefixe', ['prefixes' => $prefixes, 'operateurs' => $operateurs]);
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
        $operateurId = (int) $this->request
            ->getPost('operateur_id');

        if ($prefixe === '') {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Le préfixe est obligatoire.'
                );
        }

        if ($operateurId <= 0) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Veuillez sélectionner un opérateur.'
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

        $operateurModel = new OperateurModel();

        $operateur = $operateurModel->find($operateurId);

        if ($operateur === null) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Opérateur introuvable.'
                );
        }

        if ((int) $operateur['actif'] !== 1) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Cet opérateur est inactif.'
                );
        }

        $insertion = $prefixeModel->insert([
            'prefixe'       => $prefixe,
            'actif'         => 1,
            'operateur_id'  => $operateurId,
            'date_creation' => date('Y-m-d H:i:s'),
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
        
        $commission = (double) $this->request->getPost('commision');

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

    public function situationCompte(): string
    {
        $compteModel = new ClientModel();
        $operateurModel = new OperateurModel();

        $telephone = trim(
            (string) $this->request->getGet('telephone')
        );

        $operateurId = (int) (
            $this->request->getGet('operateur_id') ?? 0
        );

        $ordre = (string) (
            $this->request->getGet('ordre') ?? 'telephone'
        );

        if (!in_array(
            $ordre,
            ['telephone', 'operateur'],
            true
        )) {
            $ordre = 'telephone';
        }

        $requete = $compteModel
            ->select([
                'comptes.id',
                'comptes.telephone',
                'comptes.solde',
                'comptes.statut',
                'comptes.date_creation',
                'prefixes_operateur.prefixe',
                'operateurs.id AS operateur_id',
                'operateurs.nom AS operateur_nom',
                'operateurs.code AS operateur_code',
            ])
            ->join(
                'prefixes_operateur',
                "SUBSTR(
                    comptes.telephone,
                    1,
                    LENGTH(prefixes_operateur.prefixe)
                ) = prefixes_operateur.prefixe",
                'left',
                false
            )
            ->join(
                'operateurs',
                'operateurs.id = prefixes_operateur.operateur_id',
                'left'
            );

        if ($telephone !== '') {
            $requete->like(
                'comptes.telephone',
                $telephone
            );
        }

        if ($operateurId > 0) {
            $requete->where(
                'prefixes_operateur.operateur_id',
                $operateurId
            );
        }

        if ($ordre === 'operateur') {
            $requete
                ->orderBy('operateurs.nom', 'ASC')
                ->orderBy('comptes.telephone', 'ASC');
        } else {
            $requete
                ->orderBy('comptes.telephone', 'ASC');
        }

        $comptes = $requete->paginate(
            20,
            'situation_comptes'
        );

        $operateurs = $operateurModel
            ->where('actif', 1)
            ->orderBy('nom', 'ASC')
            ->findAll();

        return view('operateur/situation-compte', [
            'comptes'            => $comptes,
            'operateurs'         => $operateurs,
            'pager'              => $compteModel->pager,
            'telephone'          => $telephone,
            'operateurSelection' => $operateurId,
            'ordre'              => $ordre,
        ]);
    }
}
