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

        $operationId = $this->insertOperation($db, $typeOp->id, $reference, null, $compte->id, $montant, 'Dépôt en espèces');
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

        $telephone = session()->get('telephone');
        $montant = (float) $this->request->getPost('montant');
        $db = \Config\Database::connect();
        
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

        $operationId = $this->insertOperation($db, $typeOp->id, $reference, $compte->id, null, $montant, 'Retrait en espèces');
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

        return redirect()->to('client/dashboard')->with('success', 'Retrait de ' . number_format($montant, 2, ',', ' ') . ' Ar effectué avec succès (Réf: ' . $reference . ').');
    }

    /////////////////////////////////////////////////////////////////////
    public function doTransfert()
    {
        if (!session()->has('telephone')) {
            return redirect()->to('connexion/client');
        }

        $rules = [
            'destinataire' => 'required|numeric',
            'montant'      => 'required|numeric|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $telExpediteur = session()->get('telephone');
        $telDestinataire = $this->request->getPost('destinataire');
        $montant = (float) $this->request->getPost('montant');

        if ($telExpediteur === $telDestinataire) {
            return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas transférer de l\'argent à votre propre numéro.');
        }

        $db = \Config\Database::connect();
        // ----------------------------------------------------------------
        $db->transStart();

        try {
            $typeOp    = $db->table('types_operations')->where('code', 'TRA')->get()->getRow();
            $compteExp = $db->table('comptes')->where('telephone', $telExpediteur)->get()->getRow();
            $compteDest = $db->table('comptes')->where('telephone', $telDestinataire)->get()->getRow();

            if (!$compteExp || $compteExp->statut !== 'ACTIF') {
                $db->transRollback();
                return redirect()->back()->with('error', 'Votre compte est inactif ou introuvable.');
            }

            if (!$compteDest || $compteDest->statut !== 'ACTIF') {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Le numéro du destinataire est introuvable ou inactif.');
            }

            if (!$typeOp) {
                $db->transRollback();
                return redirect()->back()->with('error', 'Configuration du type d\'opération "TRA" manquante.');
            }

            $soldeAvantExp = (float) $compteExp->solde;
            if ($soldeAvantExp < $montant) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Solde insuffisant pour effectuer ce transfert.');
            }

            $soldeApresExp  = $soldeAvantExp - $montant;
            $soldeAvantDest = (float) $compteDest->solde;
            $soldeApresDest = $soldeAvantDest + $montant;
            
            $reference = $this->genererReference($typeOp->code);

            $operationId = $this->enregistrerOperation($db, $typeOp->id, $reference, $compteExp->id, $compteDest->id, $montant, 'Transfert de compte à compte');

            $this->enregistrerMouvement($db, $operationId, $compteExp->id, 'DEBIT', $soldeAvantExp, $soldeApresExp, $montant, "Transfert envoyé au {$telDestinataire}");
            
            $this->enregistrerMouvement($db, $operationId, $compteDest->id, 'CREDIT', $soldeAvantDest, $soldeApresDest, $montant, "Transfert reçu du {$telExpediteur}");

            $db->table('comptes')->where('id', $compteExp->id)->update([
                'solde'             => $soldeApresExp,
                'date_modification' => date('Y-m-d H:i:s')
            ]);

            $db->table('comptes')->where('id', $compteDest->id)->update([
                'solde'             => $soldeApresDest,
                'date_modification' => date('Y-m-d H:i:s')
            ]);

            $db->transComplete();
        // ----------------------------------------------------------------
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Une erreur critique est survenue : ' . $e->getMessage());
        }

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Le transfert a échoué suite à un problème technique.');
        }

        return redirect()->to('client/dashboard')->with('success', 'Transfert de ' . number_format($montant, 2, ',', ' ') . ' Ar envoyé avec succès à ' . $telDestinataire . ' (Réf: ' . $reference . ').');
    }
}