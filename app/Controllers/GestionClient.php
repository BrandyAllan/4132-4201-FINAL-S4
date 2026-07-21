<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;
use App\Models\MouvementCompteModel;
use App\Models\BaremeFraisModel;
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
        
        $compte = $clientModel->where('telephone', $telephone)->first();

        if (!$compte) {
            return redirect()->to('connexion/client')->with('error', 'Compte introuvable.');
        }

        $liste_bareme = $baremeModel->findAll();

        $prefixeModel = new PrefixeModel();
        $prefixesValides = $prefixeModel->findAll(); 

        return view('client/transfert', [
            'solde' => $compte['solde'], 
            'bareme' => $liste_bareme,
            'prefixes' => $prefixesValides 
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



    ///////////////////////////////////////////////////////////////////
    private function genererReference(string $codeTypeOperation): string
    {
        $date = date('Ymd-His');
        $random = strtoupper(bin2hex(random_bytes(2))); 
        return "{$codeTypeOperation}-{$date}-{$random}";
    }
    
    private function insertOperation($db, int $typeOpId, string $ref, ?int $sourceId, ?int $destId, float $montant, string $motif, ?int $frais): int
    {
        $db->table('operations')->insert([
            'reference'             => $ref,
            'type_operation_id'     => $typeOpId,
            'compte_source_id'      => $sourceId,      // Peut être null (Dépôt)
            'compte_destination_id' => $destId,        // Peut être null (Retrait)
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

        $operationId = $this->insertOperation($db, $typeOp->id, $reference, null, $compte->id, $montant, 'Dépôt en espèces', 0);
        $this->insertMouvement($db, $operationId, $compte->id, 'CREDIT', $soldeAvant, $soldeApres, $montant, 'Crédit suite à dépôt de fonds');
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
public function doTransfert()
{
    if (!session()->has('telephone')) {
        return redirect()->to('login/client');
    }

    $rules = [
        'destinataire' => 'required|numeric',
        'montant'      => 'required|numeric|greater_than[0]'
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }

    $db = \Config\Database::connect();
    $telExpediteur = session()->get('telephone');
    $telDestinataire = $this->request->getPost('destinataire');
    $montantInitial = (float) $this->request->getPost('montant');
    $inclureFraisRetrait = $this->request->getPost('inclureFraisRetrait') !== null;

    // 1. Détection de l'opérateur du destinataire
    $prefixeDest = substr($telDestinataire, 0, 3);
    $infoOp = $db->table('prefixes_operateur')
                 ->join('operateurs', 'operateurs.id = prefixes_operateur.operateur_id')
                 ->where('prefixe', $prefixeDest)
                 ->get()->getRow();

    // On considère comme "Autre Opérateur" si le nom n'est pas "MadaCash" (ajustez selon votre nom réel)
    $estAutreOperateur = ($infoOp && $infoOp->nom !== 'MadaCash');

    // 2. Calculs des coûts
    $fraisTransfert = $this->calculFrais($db, $montantInitial);
    
    // Règle V2 : Pas de frais de retrait pour les autres opérateurs
    $fraisRetrait = 0.0;
    if ($inclureFraisRetrait && !$estAutreOperateur) {
        $fraisRetrait = $this->calculFrais($db, $montantInitial);
    }

    // 3. Calculs pour la table opérations
    $montantPourDestinataire = $montantInitial + $fraisRetrait; 
    $totalADebiter = $montantInitial + $fraisTransfert + $fraisRetrait;

    if ($telExpediteur === $telDestinataire) {
        return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas transférer à vous-même.');
    }

    $db->transStart();

    $typeOp    = $db->table('types_operations')->where('code', 'TRA')->get()->getRow();
    $compteExp = $db->table('comptes')->where('telephone', $telExpediteur)->get()->getRow();
    // Le compte destinataire peut être absent si c'est un autre opérateur
    $compteDest = $db->table('comptes')->where('telephone', $telDestinataire)->get()->getRow();

    if (!$typeOp || !$compteExp || $compteExp->statut !== 'ACTIF') {
        $db->transRollback();
        return redirect()->back()->withInput()->with('error', 'Configuration manquante ou compte inactif.');
    }

    if ((float)$compteExp->solde < $totalADebiter) {
        $db->transRollback();
        return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour effectuer ce transfert.');
    }

    $reference = $this->genererReference($typeOp->code);

    // Insertion
    $operationId = $this->insertOperation(
        $db, $typeOp->id, $reference, $compteExp->id,
        ($compteDest ? $compteDest->id : null),
        $montantPourDestinataire,
        "Transfert vers {$telDestinataire}",
        $fraisTransfert
    );

    // Mises à jour
    $soldeApresExp = (float)$compteExp->solde - $totalADebiter;
    $this->insertMouvement($db, $operationId, $compteExp->id, 'DEBIT', $compteExp->solde, $soldeApresExp, $totalADebiter, "Transfert envoyé");
    $db->table('comptes')->update(['solde' => $soldeApresExp], ['id' => $compteExp->id]);

    // Si compte interne, on crédite le destinataire
    if ($compteDest) {
        $soldeApresDest = (float)$compteDest->solde + $montantPourDestinataire;
        $this->insertMouvement($db, $operationId, $compteDest->id, 'CREDIT', $compteDest->solde, $soldeApresDest, $montantPourDestinataire, "Reçu de {$telExpediteur}");
        $db->table('comptes')->update(['solde' => $soldeApresDest], ['id' => $compteDest->id]);
    }

    $db->transComplete();

    return redirect()->to('client/dashboard')->with('success', 'Transfert réussi (Réf: ' . $reference . ').');
}


}