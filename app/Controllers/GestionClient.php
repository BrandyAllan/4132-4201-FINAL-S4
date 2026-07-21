<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Models\MouvementCompteModel;
use App\Models\BaremeFraisModel;
use App\Models\RemiseModel;
use App\Controllers\BaseController;

class GestionClient extends BaseController
{
    public function showSolde()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('connexion/client');
        }

        $clientModel = new ClientModel();
        $telephone = session()->get('telephone');
        $compte = $clientModel->where('telephone', $telephone)->first();

        if (!$compte) {
            session()->destroy();
            return redirect()->to('connexion/client');
        }

        $data = [
            'solde' => $compte['solde']
        ];

        return view('client/solde', $data);
    }

    public function showDepot()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('connexion/client');
        }

        return view('client/depot');
    }

    public function showRetrait()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('connexion/client');
        }

        $telephone = session()->get('telephone');
        $clientModel = new ClientModel();
        $baremeModel = new BaremeFraisModel();

        $compte = $clientModel->where('telephone', $telephone)->first();
        
        if (!$compte) {
            return redirect()->to('connexion/client')->with('error', 'Compte introuvable.');
        }

        $liste_bareme = $baremeModel->findAll();

        return view('client/retrait', ['solde' => $compte['solde'], 'bareme' => json_encode($liste_bareme)
        ]);
    }

    public function showTransfert()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('connexion/client');
        }

        $telephone = session()->get('telephone');
        $clientModel = new ClientModel();
        $baremeModel = new BaremeFraisModel();
        $prefixeModel = new PrefixeModel();
        
        $compte = $clientModel->where('telephone', $telephone)->first();

        if (!$compte) {
            return redirect()->to('connexion/client')->with('error', 'Compte introuvable.');
        }

        $liste_bareme = $baremeModel->findAll();
        $prefixesValides = $prefixeModel->findAll(); 

        //////////////////////////////////////////////
        $db = \Config\Database::connect();
        $operateur_id = session()->get('operateur_id');
        
        $prefixesActuel = [];
        if ($operateur_id) {
            $result = $db->table('prefixes_operateur')
                ->where('operateur_id', $operateur_id)
                ->where('actif', 1)
                ->get()
                ->getResultArray();
            
            $prefixesActuel = array_column($result, 'prefixe');
        }

        return view('client/transfert', [
            'solde' => $compte['solde'], 
            'bareme' => $liste_bareme,
            'prefixes' => $prefixesValides,
            'prefixesActuel' => $prefixesActuel 
        ]);
    }


    public function showHistorique()
    {
        $session = session();
        if (!$session->has('est_connecte')) {
            return redirect()->to('connexion/client');
        }

        $clientId = $session->get('client_id');
        $mouvementModel = new MouvementCompteModel(); 
        
        $historique = $mouvementModel->select('mouvements_comptes.*, types_operations.code as type_operation')
                            ->join('operations', 'operations.id = mouvements_comptes.operation_id')
                            ->join('types_operations', 'types_operations.id = operations.type_operation_id')
                            ->where('mouvements_comptes.compte_id', $clientId)
                            ->orderBy('mouvements_comptes.date_mouvement', 'DESC')
                            ->findAll();

        return view('client/historique', [
            'historique' => $historique,
            'telephone'  => $session->get('telephone')
        ]);
    }

    public function showFormEpargne() {
        return view('client/epargne');
    }

    public function configEpargne() {
        $session = session();
        if (!$session->has('est_connecte')) {
            return redirect()->to('connexion/client');
        }
        $db = \Config\Database::connect();
        $pourcentage = $this->request->getPost('pourcentage');
        $clientId = $session->get('client_id');

        $db->table('comptes')->where('id', $clientId)->update([
            'pourcentage_epargne'             => $pourcentage
        ]);

        return redirect()->to('client/dashboard')->with('success', 'Configuration de ' . $pourcentage . ' d\'épargne effectué');

    }

    ///////////////////////////////////////////////////////////////////
    private function genererReference(string $codeTypeOperation): string
    {
        $date = date('Ymd-His');
        $random = strtoupper(bin2hex(random_bytes(2))); 
        return "{$codeTypeOperation}-{$date}-{$random}";
    }
    
    private function insertOperation($db, int $typeOpId, string $ref, ?int $sourceId, ?string $telDest, float $montant, string $motif, ?int $frais): int
    {
        $db->table('operations')->insert([
            'reference'             => $ref,
            'type_operation_id'     => $typeOpId,
            'compte_source_id'      => $sourceId,      // Peut être null (Dépôt)
            'telephone_destinataire' => $telDest,        // Peut être null (Retrait)
            'montant'               => $montant,
            'frais'                 => $frais,
            'montant_total'         => $montant,
            'statut'                => 'VALIDEE',
            'motif'                 => $motif,
            'date_operation'        => date('Y-m-d H:i:s')
        ]);

        return $db->insertID();
    }


    private function insertMouvement($db, int $opId, int $compteId, string $sens, float $avant, float $apres, float $montant, string $libelle)
    {
        $db->table('mouvements_comptes')->insert([
            'operation_id'    => $opId,
            'compte_id'       => $compteId,
            'sens'            => $sens,               // 'CREDIT' ou 'DEBIT'
            'montant'         => $montant,
            'solde_avant'     => $avant,
            'solde_apres'     => $apres,
            'libelle'         => $libelle,
            'date_mouvement'  => date('Y-m-d H:i:s')
        ]);
    }
    
    /////////////////////////////////////////////////////////////////////
    private function calculFrais($db, float $montant): float
    {
        $bareme = $db->table('baremes_frais')
                    ->where('montant_min <=', $montant)
                    ->where('montant_max >=', $montant)
                    ->get()
                    ->getRow();

        return $bareme ? (float)$bareme->frais : 0.0;
    }

    /////////////////////////////////////////////////////////////////////
    public function doDepot()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('login/client');
        }

        $rules = ['montant' => 'required|numeric|greater_than[0]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $telephone = session()->get('telephone');
        $montant = (float) $this->request->getPost('montant');
        $db = \Config\Database::connect();
        
        // ----------------------------------------------------------------
        $db->transStart();

        $typeOp = $db->table('types_operations')->where('code', 'DEP')->get()->getRow();
        $compte = $db->table('comptes')->where('telephone', $telephone)->get()->getRow();

        if (!$typeOp || !$compte || $compte->statut !== 'ACTIF') {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur de configuration ou compte inactif.');
        }

        $soldeAvant = (float) $compte->solde;   
        $soldeApres = $soldeAvant + $montant;
        $reference  = $this->genererReference($typeOp->code);

        $operationId = $this->insertOperation(
            $db, 
            $typeOp->id, 
            $reference, 
            $compte->id, 
            null,                
            $montant, 
            'Dépôt en espèces',
            0                   
        );

        $this->insertMouvement(
            $db, 
            $operationId, 
            $compte->id, 
            'CREDIT', 
            $soldeAvant, 
            $soldeApres, 
            $montant, 
            'Crédit suite à dépôt de fonds'
        );

        $db->table('comptes')->where('id', $compte->id)->update([
            'solde'             => $soldeApres,
            'date_modification' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();
        // ----------------------------------------------------------------

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Le dépôt a échoué suite à un problème technique.');
        }

        return redirect()->to('client/dashboard')->with('success', 'Dépôt de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué (Réf: ' . $reference . ')');
    }
    /////////////////////////////////////////////////////////////////////
    public function doRetrait()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('login/client');
        }

        $rules = ['montant' => 'required|numeric|greater_than[0]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();

        $telephone = session()->get('telephone');
        $montant = (float) $this->request->getPost('montant');
        $frais = $this->calculFrais($db,$montant);
        $montant = $montant + $frais;
        
        // ----------------------------------------------------------------
        $db->transStart();

        $typeOp = $db->table('types_operations')->where('code', 'RET')->get()->getRow();
        $compte = $db->table('comptes')->where('telephone', $telephone)->get()->getRow();

        if (!$typeOp || !$compte || $compte->statut !== 'ACTIF') {
            $db->transRollback();
            return redirect()->back()->with('error', 'Configuration manquante ou compte inactif.');
        }

        $soldeAvant = (float) $compte->solde;
        if ($soldeAvant < $montant) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour effectuer ce retrait.');
        }

        $soldeApres = $soldeAvant - $montant;
        $reference  = $this->genererReference($typeOp->code);

        $operationId = $this->insertOperation($db, $typeOp->id, $reference, $compte->id, null, $montant, 'Retrait en espèces', $frais);
        $this->insertMouvement($db, $operationId, $compte->id, 'DEBIT', $soldeAvant, $soldeApres, $montant, 'Débit suite à retrait de fonds');
        $db->table('comptes')->where('id', $compte->id)->update([
            'solde'             => $soldeApres,
            'date_modification' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();
        // ----------------------------------------------------------------

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Le retrait a échoué suite à un problème technique.');
        }

        return redirect()->to('client/dashboard')->with('success', 'Retrait de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué avec succès (Réf: ' . $reference . ').' . 'Frais: '. number_format($frais, 2, ',', ' ') . ' Ar');
    }


    /////////////////////////////////////////////////////////////////////
    private function ValiderTelephone($db, string $telephone): ?int
    {
        $session = session();
        $operateur_id = $session->get('operateur_id');
        
        if (!$operateur_id) {
            return null;
        }

        if (!preg_match('/^(032|033|034|038|037|031)\d{7}$/', $telephone)) {
            return null;
        }

        $prefixe = substr($telephone, 0, 3);

        $prefixeAppartient = $db->table('prefixes_operateur')
            ->where('prefixe', $prefixe)
            ->where('operateur_id', $operateur_id)
            ->where('actif', 1)
            ->get()
            ->getRow();

        return ($prefixeAppartient !== null) ? 1 : 0;
    }

    /////////////////////////////////////////////////////////////////////
        private function calculFraisOperateur($db, float $montant, string $telephone): float
    {
        $bareme = $db->table('baremes_frais')
                    ->where('montant_min <=', $montant)
                    ->where('montant_max >=', $montant)
                    ->get()
                    ->getRow();
        
        $frais_base = (float)$bareme->frais;


        if($this->ValiderTelephone($db, $telephone) === 1){
            $remise = new RemiseModel();

            $remise = $db->table('remise')
                         ->where('actif',1)
                         ->get()
                         ->getRow();

            $pourcentage = (float)$remise->pourcentage;

            $frais = $frais_base - (( $frais_base * $pourcentage )/ 100);
            return $frais;
        }

        return $bareme;
    }

    /////////////////////////////////////////////////////////////////////
    public function doTransfert()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('login/client');
        }

        //////////////////////////////
        $rules = [
            'destinataire' => 'required|numeric',
            'montant'      => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }


        //////////////////////////////
        $db = \Config\Database::connect();
        $telExpediteur = session()->get('telephone');
        $telDestinataire = $this->request->getPost('destinataire');
        $montantInitial = (float) $this->request->getPost('montant');
        $inclureFraisRetrait = $this->request->getPost('inclureFraisRetrait') !== null;

        if ($telExpediteur === $telDestinataire) {
            return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas transférer à vous-même.');
        }

        $validationOp = $this->ValiderTelephone($db, $telDestinataire);

        if ($validationOp === null) {
            return redirect()->back()->withInput()->with('error', 'Le numéro du destinataire est invalide ou le format est incorrect.');
        }

        // $validationOp == 1 <=> même opérateur
        // $validationOp == 0 <=> autre opérateur
        $estMemeOperateur = ($validationOp === 1);
        
        //////////////////////////////
        $fraisTransfert = $this->calculFraisOperateur($db, $montantInitial, $telDestinataire);
        
        $fraisRetrait = 0.0;
        if ($inclureFraisRetrait && $estMemeOperateur) {
            $fraisRetrait = $this->calculFrais($db, $montantInitial);
        }

        $compteDest = $db->table('comptes')->where('telephone', $telDestinataire)->get()->getRow();
        if($compteDest) {
            $pourcentage_epargne = $compteDest->pourcentage_epargne;
        }
        $montantEpargneDest = $montantInitial * $pourcentage_epargne / 100;
        $montantPourDestinataire = ($montantInitial - $montantEpargneDest) + $fraisRetrait; 
        $totalADebiter = $montantInitial + $fraisTransfert + $fraisRetrait;


        //////////////////////////////
        $db->transStart();
        $typeOp    = $db->table('types_operations')->where('code', 'TRANS')->get()->getRow();
        $compteExp = $db->table('comptes')->where('telephone', $telExpediteur)->get()->getRow();
        

        if (!$typeOp || !$compteExp || $compteExp->statut !== 'ACTIF') {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Configuration manquante ou compte inactif.');
        }

        if ((float)$compteExp->solde < $totalADebiter) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour effectuer ce transfert.');
        }

        $reference = $this->genererReference($typeOp->code);

                //////////////////////////////
        $operationId = $this->insertOperation(
            $db, $typeOp->id, $reference, $compteExp->id,
            $telDestinataire,
            $montantPourDestinataire,
            "Transfert vers {$telDestinataire}",
            $fraisTransfert
        );

                //////////////////////////////
        $soldeApresExp = (float)$compteExp->solde - $totalADebiter;
        $this->insertMouvement($db, $operationId, $compteExp->id, 'DEBIT', $compteExp->solde, $soldeApresExp, $totalADebiter, "Transfert envoyé");
        $db->table('comptes')->update(['solde' => $soldeApresExp], ['id' => $compteExp->id]);

        if ($compteDest) {
            $soldeApresDest = (float)$compteDest->solde + $montantPourDestinataire;
            $this->insertMouvement($db, $operationId, $compteDest->id, 'CREDIT', $compteDest->solde, $soldeApresDest, $montantPourDestinataire, "Reçu de {$telExpediteur}");

            $epargneDest = $db->table('epargne')->where('compte_id', $compteDest->id)->get()->getRow();

            if($epargneDest) {
                $nouveauSolde = $epargneDest->solde + $montantEpargneDest;
                $db->table('epargne')->where('id', $compteDest->id)->update(['solde'], $nouveauSolde);
            } else {
                $db->table('epargne')->insert([
                    'compte_id' => $compteDest->id,
                    'solde' => $montantEpargneDest
                ]);
            }
            $db->table('comptes')->update(['solde' => $soldeApresDest], ['id' => $compteDest->id]);
        }

        $db->transComplete();

        return redirect()->to('client/dashboard')->with('success', 'Transfert réussi (Réf: ' . $reference . ').');
    }

    /////////////////////////////////////////////////////////////////////
    public function doTransfertMultiple()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('connexion/client');
        }

        // Récupération des données du formulaire
        $montantGlobal = (float) $this->request->getPost('montant_global');
        $destinataires = $this->request->getPost('destinataires'); // C'est un tableau de numéros

        if ($montantGlobal <= 0 || empty($destinataires) || !is_array($destinataires)) {
            return redirect()->back()->withInput()->with('error', 'Données invalides pour le transfert multiple.');
        }

        $db = \Config\Database::connect();
        $telExpediteur = session()->get('telephone');

        // 1. Filtrer les doublons ou le propre numéro de l'expéditeur si besoin
        $destinataires = array_unique(array_filter($destinataires));
        $nombreDestinataires = count($destinataires);

        if ($nombreDestinataires === 0) {
            return redirect()->back()->withInput()->with('error', 'Veuillez renseigner au moins un destinataire valide.');
        }

        // 2. Calcul du montant par destinataire (division du montant global)
        $montantParPersonne = $montantGlobal / $nombreDestinataires;

        // Récupération du compte expéditeur
        $compteExp = $db->table('comptes')->where('telephone', $telExpediteur)->get()->getRow();
        $typeOp    = $db->table('types_operations')->where('code', 'TRA')->get()->getRow();

        if (!$compteExp || $compteExp->statut !== 'ACTIF' || !$typeOp) {
            return redirect()->back()->withInput()->with('error', 'Compte expéditeur inactif ou configuration manquante.');
        }

        // Calcul des frais globaux (par exemple, basés sur le montant global ou sur chaque part)
        // Ici, on calcule les frais sur le montant global du transfert
        $fraisTransfertGlobal = $this->calculFrais($db, $montantGlobal);
        $totalADebiter = $montantGlobal + $fraisTransfertGlobal;

        if ((float)$compteExp->solde < $totalADebiter) {
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour couvrir le montant global et les frais.');
        }

        $db->transStart();

        $reference = $this->genererReference($typeOp->code);

        // Enregistrement d'une opération globale ou par destinataire selon votre logique
        // Ici on enregistre l'opération globale avec le montant total
        $operationId = $this->insertOperation(
            $db, 
            $typeOp->id, 
            $reference, 
            $compteExp->id, 
            null, // ou un libellé groupé
            $montantGlobal, 
            $fraisTransfertGlobal,
            "Transfert multiple vers {$nombreDestinataires} numéros"
        );

        // Débit unique de l'expéditeur pour le total (montant global + frais globaux)
        $soldeAvantExp = (float)$compteExp->solde;
        $soldeApresExp = $soldeAvantExp - $totalADebiter;
        
        $this->insertMouvement(
            $db, $operationId, $compteExp->id, 'DEBIT', 
            $soldeAvantExp, $soldeApresExp, $totalADebiter, 
            "Envoi multiple de {$montantGlobal} Ar réparti sur {$nombreDestinataires} personnes"
        );

        $db->table('comptes')->update(['solde' => $soldeApresExp], ['id' => $compteExp->id]);

        // 3. Boucle sur chaque destinataire pour créditer leur part
        foreach ($destinataires as $telDest) {
            if ($telDest === $telExpediteur) {
                continue; // Optionnel : ignorer si l'expéditeur s'est mis lui-même dans la liste
            }

            $compteDest = $db->table('comptes')->where('telephone', $telDest)->get()->getRow();

            // Si le destinataire possède un compte dans notre système, on le crédite de sa part
            if ($compteDest) {
                $soldeAvantDest = (float)$compteDest->solde;
                $soldeApresDest = $soldeAvantDest + $montantParPersonne;

                $this->insertMouvement(
                    $db, $operationId, $compteDest->id, 'CREDIT', 
                    $soldeAvantDest, $soldeApresDest, $montantParPersonne, 
                    "Reçu (part de transfert multiple de {$telExpediteur})"
                );

                $db->table('comptes')->update(['solde' => $soldeApresDest], ['id' => $compteDest->id]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Erreur lors du traitement du transfert multiple.');
        }

        return redirect()->to('client/dashboard')->with('success', 'Transfert multiple de ' . number_format($montantGlobal, 2) . ' Ar effectué avec succès (Réf: ' . $reference . ').');
    }

    
}