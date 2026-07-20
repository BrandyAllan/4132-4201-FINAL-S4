<?php

namespace App\Controllers;

use App\Models\ClientModel;
use App\Models\PrefixeModel;
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

        return view('client/retrait');
    }

    public function showTransfert()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('connexion/client');
        }

        return view('client/transfert');
    }


    public function showhistorique()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('connexion/client');
        }


    }


    ///////////////////////////////////////////////////////////////////
    private function genererReference(string $codeTypeOperation): string
    {
        $date = date('Ymd-His');
        $random = strtoupper(bin2hex(random_bytes(2))); 
        return "{$codeTypeOperation}-{$date}-{$random}";
    }
    
    
    ///////////////////////////////////////////////////////////////////
    private function insertOperation($db, int $typeOpId, string $ref, ?int $sourceId, ?int $destId, float $montant, string $motif): int
    {
        $db->table('operations')->insert([
            'reference'             => $ref,
            'type_operation_id'     => $typeOpId,
            'compte_source_id'      => $sourceId,      // Peut être null (Dépôt)
            'compte_destination_id' => $destId,        // Peut être null (Retrait)
            'montant'               => $montant,
            'frais'                 => 0,
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
        // DEBUT TRANSACTION
        // ----------------------------------------------------------------
        $db->transStart();

        // 2. Récupération des entités de base
        $typeOp = $db->table('types_operations')->where('code', 'DEP')->get()->getRow();
        $compte = $db->table('comptes')->where('telephone', $telephone)->get()->getRow();

        if (!$typeOp || !$compte || $compte->statut !== 'ACTIF') {
            $db->transRollback();
            return redirect()->back()->with('error', 'Erreur de configuration ou compte inactif.');
        }

        // 3. Calculs des soldes
        $soldeAvant = (float) $compte->solde;
        $soldeApres = $soldeAvant + $montant;
        $reference  = $this->genererReference($typeOp->code);

        // 4. Utilisation des mini-fonctions pour insérer et tracer
        $operationId = $this->insertOperation($db, $typeOp->id, $reference, null, $compte->id, $montant, 'Dépôt en espèces');
        $this->insertMouvement($db, $operationId, $compte->id, 'CREDIT', $soldeAvant, $soldeApres, $montant, 'Crédit suite à dépôt de fonds');
        // 5. Mise à jour du solde final du compte
        $db->table('comptes')->where('id', $compte->id)->update([
            'solde'             => $soldeApres,
            'date_modification' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();
        // ----------------------------------------------------------------
        // FIN TRANSACTION
        // ----------------------------------------------------------------

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Le dépôt a échoué suite à un problème technique.');
        }

        return redirect()->to('client/dashboard')->with('success', 'Dépôt de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué (Réf: ' . $reference . ')');
    }

    /////////////////////////////////////////////////////////////////////


    public function doRetrait()
    {
        // 1. Sécurité & Validation du montant
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
        // DEBUT TRANSACTION
        // ----------------------------------------------------------------
        $db->transStart();

        // 2. Récupération du type 'RET' et du compte
        $typeOp = $db->table('types_operations')->where('code', 'RET')->get()->getRow();
        $compte = $db->table('comptes')->where('telephone', $telephone)->get()->getRow();

        if (!$typeOp || !$compte || $compte->statut !== 'ACTIF') {
            $db->transRollback();
            return redirect()->back()->with('error', 'Configuration manquante ou compte inactif.');
        }

        // 3. VÉRIFICATION DU SOLDE : Le client a-t-il assez d'argent ?
        $soldeAvant = (float) $compte->solde;
        if ($soldeAvant < $montant) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour effectuer ce retrait.');
        }

        // 4. Calcul du nouveau solde décroissant et de la référence
        $soldeApres = $soldeAvant - $montant;
        $reference  = $this->genererReference($typeOp->code);

        // 5. Enregistrements en cascades
        $operationId = $this->insertOperation($db, $typeOp->id, $reference, $compte->id, null, $montant, 'Retrait en espèces');
        $this->insertMouvement($db, $operationId, $compte->id, 'DEBIT', $soldeAvant, $soldeApres, $montant, 'Débit suite à retrait de fonds');
        // 6. Mise à jour réelle du solde du compte
        $db->table('comptes')->where('id', $compte->id)->update([
            'solde'             => $soldeApres,
            'date_modification' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();
        // ----------------------------------------------------------------
        // FIN TRANSACTION
        // ----------------------------------------------------------------

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Le retrait a échoué suite à un problème technique.');
        }

        // Redirection vers le dashboard avec un message flash de succès
        return redirect()->to('client/dashboard')->with('success', 'Retrait de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué avec succès (Réf: ' . $reference . ').');
    }
}