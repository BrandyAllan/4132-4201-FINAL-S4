<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;
use Throwable;

class SimulationMobileMoneySeeder extends Seeder
{
    public function run()
    {
        /*
         * SQLite exige de désactiver les clés étrangères
         * avant de commencer la transaction.
         */
        $this->db->query('PRAGMA foreign_keys = OFF');

        $this->db->transBegin();

        try {
            $this->verifierStructureOperations();

            $this->nettoyerDonnees();

            $this->creerOperateurs();

            $this->creerPrefixes();

            $typesOperations = $this->creerTypesOperations();

            $this->creerBaremes(
                $typesOperations['RETRAIT'],
                $typesOperations['TRANSFERT']
            );

            $comptes = $this->creerComptesOrange();

            $this->creerOperations(
                $typesOperations['RETRAIT'],
                $typesOperations['TRANSFERT'],
                $comptes
            );

            if ($this->db->transStatus() === false) {
                $erreur = $this->db->error();

                throw new RuntimeException(
                    'Échec de la transaction : '
                    . ($erreur['message'] ?? 'erreur SQL inconnue')
                );
            }

            $this->db->transCommit();
        } catch (Throwable $exception) {
            $this->db->transRollback();

            throw $exception;
        } finally {
            $this->db->query('PRAGMA foreign_keys = ON');
        }
    }

    /**
     * Vérifie que la migration de la table operations
     * a bien été exécutée.
     */
    private function verifierStructureOperations(): void
    {
        if (!$this->db->tableExists('operations')) {
            throw new RuntimeException(
                'La table operations n’existe pas.'
            );
        }

        $champs = $this->db->getFieldNames('operations');

        if (!in_array('telephone_destinataire', $champs, true)) {
            throw new RuntimeException(
                'La colonne telephone_destinataire est absente de '
                . 'la table operations. Exécute d’abord la migration.'
            );
        }

        if (in_array('compte_destination_id', $champs, true)) {
            throw new RuntimeException(
                'La colonne compte_destination_id existe encore. '
                . 'La migration de la table operations n’a pas été '
                . 'correctement appliquée.'
            );
        }
    }

    /**
     * Supprime les anciennes données de simulation.
     */
    private function nettoyerDonnees(): void
    {
        /*
         * L’ordre va des tables dépendantes
         * vers les tables principales.
         */
        $tables = [
            'mouvements_comptes',
            'operations',
            'baremes_frais',
            'comptes',
            'prefixes_operateur',
            'types_operations',
            'operateurs',
        ];

        foreach ($tables as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            $resultat = $this->db
                ->table($table)
                ->emptyTable();

            if ($resultat === false) {
                $this->leverErreur(
                    'Impossible de vider la table ' . $table
                );
            }
        }

        /*
         * Réinitialisation des identifiants AUTOINCREMENT.
         */
        foreach ($tables as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            $this->db->query(
                'DELETE FROM sqlite_sequence WHERE name = ?',
                [$table]
            );
        }
    }

    /**
     * Crée les trois opérateurs avec leurs identifiants fixes.
     */
    private function creerOperateurs(): void
    {
        $date = date('Y-m-d H:i:s');

        $operateurs = [
            [
                'id'            => 1,
                'nom'           => 'Telma',
                'code'          => 'TELMA',
                'actif'         => 1,
                'date_creation' => $date,
            ],
            [
                'id'            => 2,
                'nom'           => 'Orange Madagascar',
                'code'          => 'ORANGE',
                'actif'         => 1,
                'date_creation' => $date,
            ],
            [
                'id'            => 3,
                'nom'           => 'Airtel Madagascar',
                'code'          => 'AIRTEL',
                'actif'         => 1,
                'date_creation' => $date,
            ],
        ];

        $this->insererLot(
            'operateurs',
            $operateurs,
            'création des opérateurs'
        );
    }

    /**
     * Crée les préfixes des opérateurs.
     */
    private function creerPrefixes(): void
    {
        $date = date('Y-m-d H:i:s');

        $prefixes = [
            /*
             * Telma : opérateur ID 1.
             */
            [
                'prefixe'       => '034',
                'operateur_id'  => 1,
                'actif'         => 1,
                'date_creation' => $date,
            ],
            [
                'prefixe'       => '038',
                'operateur_id'  => 1,
                'actif'         => 1,
                'date_creation' => $date,
            ],

            /*
             * Orange : opérateur ID 2.
             */
            [
                'prefixe'       => '032',
                'operateur_id'  => 2,
                'actif'         => 1,
                'date_creation' => $date,
            ],
            [
                'prefixe'       => '037',
                'operateur_id'  => 2,
                'actif'         => 1,
                'date_creation' => $date,
            ],

            /*
             * Airtel : opérateur ID 3.
             */
            [
                'prefixe'       => '033',
                'operateur_id'  => 3,
                'actif'         => 1,
                'date_creation' => $date,
            ],
        ];

        $this->insererLot(
            'prefixes_operateur',
            $prefixes,
            'création des préfixes'
        );
    }

    /**
     * Crée les types RETRAIT et TRANSFERT.
     *
     * @return array<string, int>
     */
    private function creerTypesOperations(): array
    {
        $date = date('Y-m-d H:i:s');

        $types = [
            [
                'code'          => 'RETRAIT',
                'libelle'       => 'Retrait',
                'actif'         => 1,
                'date_creation' => $date,
            ],
            [
                'code'          => 'TRANSFERT',
                'libelle'       => 'Transfert',
                'actif'         => 1,
                'date_creation' => $date,
            ],
        ];

        $table = $this->db->table('types_operations');

        $identifiants = [];

        foreach ($types as $type) {
            $resultat = $table->insert($type);

            if ($resultat === false) {
                $this->leverErreur(
                    'Impossible de créer le type ' . $type['code']
                );
            }

            $identifiants[$type['code']] =
                (int) $this->db->insertID();
        }

        return $identifiants;
    }

    /**
     * Crée les barèmes de frais.
     */
    private function creerBaremes(
        int $retraitId,
        int $transfertId
    ): void {
        $date = date('Y-m-d H:i:s');

        $baremes = [
            /*
             * Barèmes de retrait.
             */
            [
                'type_operation_id' => $retraitId,
                'montant_min'       => 1,
                'montant_max'       => 5000,
                'frais'             => 200,
                'commission'        => 0,
                'actif'             => 1,
                'date_creation'     => $date,
            ],
            [
                'type_operation_id' => $retraitId,
                'montant_min'       => 5001,
                'montant_max'       => 20000,
                'frais'             => 500,
                'commission'        => 0,
                'actif'             => 1,
                'date_creation'     => $date,
            ],
            [
                'type_operation_id' => $retraitId,
                'montant_min'       => 20001,
                'montant_max'       => 50000,
                'frais'             => 1000,
                'commission'        => 0,
                'actif'             => 1,
                'date_creation'     => $date,
            ],
            [
                'type_operation_id' => $retraitId,
                'montant_min'       => 50001,
                'montant_max'       => 100000,
                'frais'             => 1800,
                'commission'        => 0,
                'actif'             => 1,
                'date_creation'     => $date,
            ],
            [
                'type_operation_id' => $retraitId,
                'montant_min'       => 100001,
                'montant_max'       => null,
                'frais'             => 3000,
                'commission'        => 0,
                'actif'             => 1,
                'date_creation'     => $date,
            ],

            /*
             * Barèmes de transfert.
             *
             * La commission représente un pourcentage :
             * 1.00 = 1 %
             * 1.25 = 1,25 %
             */
            [
                'type_operation_id' => $transfertId,
                'montant_min'       => 1,
                'montant_max'       => 10000,
                'frais'             => 100,
                'commission'        => 1.00,
                'actif'             => 1,
                'date_creation'     => $date,
            ],
            [
                'type_operation_id' => $transfertId,
                'montant_min'       => 10001,
                'montant_max'       => 50000,
                'frais'             => 300,
                'commission'        => 1.25,
                'actif'             => 1,
                'date_creation'     => $date,
            ],
            [
                'type_operation_id' => $transfertId,
                'montant_min'       => 50001,
                'montant_max'       => 100000,
                'frais'             => 600,
                'commission'        => 1.50,
                'actif'             => 1,
                'date_creation'     => $date,
            ],
            [
                'type_operation_id' => $transfertId,
                'montant_min'       => 100001,
                'montant_max'       => null,
                'frais'             => 1000,
                'commission'        => 2.00,
                'actif'             => 1,
                'date_creation'     => $date,
            ],
        ];

        $this->insererLot(
            'baremes_frais',
            $baremes,
            'création des barèmes'
        );
    }

    /**
     * Crée uniquement des comptes Orange.
     *
     * Aucun compte 033, 034 ou 038 n’est créé.
     *
     * @return array<string, int>
     */
    private function creerComptesOrange(): array
    {
        $comptes = [
            [
                'telephone'         => '0321112233',
                'solde'             => 2500000,
                'statut'            => 'ACTIF',
                'date_creation'     => '2026-01-05 08:00:00',
                'date_modification' => null,
            ],
            [
                'telephone'         => '0322223344',
                'solde'             => 1800000,
                'statut'            => 'ACTIF',
                'date_creation'     => '2026-02-10 09:30:00',
                'date_modification' => null,
            ],
            [
                'telephone'         => '0323334455',
                'solde'             => 3200000,
                'statut'            => 'ACTIF',
                'date_creation'     => '2026-03-12 10:15:00',
                'date_modification' => null,
            ],
            [
                'telephone'         => '0324445566',
                'solde'             => 1650000,
                'statut'            => 'ACTIF',
                'date_creation'     => '2026-04-04 08:45:00',
                'date_modification' => null,
            ],
            [
                'telephone'         => '0371112233',
                'solde'             => 2100000,
                'statut'            => 'ACTIF',
                'date_creation'     => '2026-01-18 11:00:00',
                'date_modification' => null,
            ],
            [
                'telephone'         => '0372223344',
                'solde'             => 2750000,
                'statut'            => 'ACTIF',
                'date_creation'     => '2026-02-20 12:20:00',
                'date_modification' => null,
            ],
            [
                'telephone'         => '0373334455',
                'solde'             => 1500000,
                'statut'            => 'ACTIF',
                'date_creation'     => '2026-03-08 14:40:00',
                'date_modification' => null,
            ],
            [
                'telephone'         => '0374445566',
                'solde'             => 2900000,
                'statut'            => 'ACTIF',
                'date_creation'     => '2026-04-15 10:30:00',
                'date_modification' => null,
            ],
        ];

        $table = $this->db->table('comptes');

        $identifiants = [];

        foreach ($comptes as $compte) {
            $resultat = $table->insert($compte);

            if ($resultat === false) {
                $this->leverErreur(
                    'Impossible de créer le compte '
                    . $compte['telephone']
                );
            }

            $identifiants[$compte['telephone']] =
                (int) $this->db->insertID();
        }

        return $identifiants;
    }

    /**
     * Crée les opérations de simulation.
     *
     * @param array<string, int> $comptes
     */
    private function creerOperations(
        int $retraitId,
        int $transfertId,
        array $comptes
    ): void {
        $operations = [
            /*
             * ================================
             * RETRAITS VALIDÉS
             * ================================
             */
            $this->retrait(
                'RET-2026-0001',
                $retraitId,
                $comptes['0321112233'],
                5000,
                200,
                '2026-01-12 09:15:00'
            ),

            $this->retrait(
                'RET-2026-0002',
                $retraitId,
                $comptes['0371112233'],
                15000,
                500,
                '2026-02-08 11:20:00'
            ),

            $this->retrait(
                'RET-2026-0003',
                $retraitId,
                $comptes['0322223344'],
                30000,
                1000,
                '2026-03-14 10:30:00'
            ),

            $this->retrait(
                'RET-2026-0004',
                $retraitId,
                $comptes['0372223344'],
                75000,
                1800,
                '2026-04-03 13:45:00'
            ),

            $this->retrait(
                'RET-2026-0005',
                $retraitId,
                $comptes['0323334455'],
                150000,
                3000,
                '2026-05-18 08:40:00'
            ),

            $this->retrait(
                'RET-2026-0006',
                $retraitId,
                $comptes['0373334455'],
                45000,
                1000,
                '2026-06-12 16:10:00'
            ),

            $this->retrait(
                'RET-2026-0007',
                $retraitId,
                $comptes['0324445566'],
                95000,
                1800,
                '2026-07-09 14:30:00'
            ),

            /*
             * ================================
             * TRANSFERTS ORANGE VERS ORANGE
             * Aucune commission inter-opérateur
             * ================================
             */
            $this->transfert(
                'TRF-2026-0001',
                $transfertId,
                $comptes['0321112233'],
                '0371112233',
                8000,
                100,
                0,
                '2026-01-20 10:00:00',
                'Transfert Orange vers Orange'
            ),

            $this->transfert(
                'TRF-2026-0002',
                $transfertId,
                $comptes['0372223344'],
                '0322223344',
                25000,
                300,
                0,
                '2026-03-08 14:00:00',
                'Transfert Orange vers Orange'
            ),

            $this->transfert(
                'TRF-2026-0003',
                $transfertId,
                $comptes['0323334455'],
                '0373334455',
                70000,
                600,
                0,
                '2026-05-04 09:30:00',
                'Transfert Orange vers Orange'
            ),

            $this->transfert(
                'TRF-2026-0004',
                $transfertId,
                $comptes['0374445566'],
                '0324445566',
                120000,
                1000,
                0,
                '2026-07-04 12:30:00',
                'Transfert Orange vers Orange'
            ),

            /*
             * ================================
             * ORANGE VERS TELMA
             * Préfixes 034 et 038
             * Commission appliquée
             * ================================
             */
            $this->transfert(
                'TRF-2026-0005',
                $transfertId,
                $comptes['0321112233'],
                '0341234567',
                10000,
                100,
                1.00,
                '2026-01-25 15:00:00',
                'Transfert Orange vers Telma 034'
            ),

            $this->transfert(
                'TRF-2026-0006',
                $transfertId,
                $comptes['0371112233'],
                '0382345678',
                20000,
                300,
                1.25,
                '2026-02-16 16:20:00',
                'Transfert Orange vers Telma 038'
            ),

            $this->transfert(
                'TRF-2026-0007',
                $transfertId,
                $comptes['0322223344'],
                '0343456789',
                40000,
                300,
                1.25,
                '2026-03-22 11:10:00',
                'Transfert Orange vers Telma 034'
            ),

            $this->transfert(
                'TRF-2026-0008',
                $transfertId,
                $comptes['0372223344'],
                '0384567890',
                80000,
                600,
                1.50,
                '2026-04-14 12:45:00',
                'Transfert Orange vers Telma 038'
            ),

            $this->transfert(
                'TRF-2026-0009',
                $transfertId,
                $comptes['0323334455'],
                '0345678901',
                150000,
                1000,
                2.00,
                '2026-05-21 09:50:00',
                'Transfert Orange vers Telma 034'
            ),

            $this->transfert(
                'TRF-2026-0010',
                $transfertId,
                $comptes['0373334455'],
                '0386789012',
                200000,
                1000,
                2.00,
                '2026-06-18 14:30:00',
                'Transfert Orange vers Telma 038'
            ),

            /*
             * ================================
             * ORANGE VERS AIRTEL
             * Préfixe 033
             * Commission appliquée
             * ================================
             */
            $this->transfert(
                'TRF-2026-0011',
                $transfertId,
                $comptes['0321112233'],
                '0331112233',
                7000,
                100,
                1.00,
                '2026-01-28 17:25:00',
                'Transfert Orange vers Airtel'
            ),

            $this->transfert(
                'TRF-2026-0012',
                $transfertId,
                $comptes['0371112233'],
                '0332223344',
                15000,
                300,
                1.25,
                '2026-02-25 10:15:00',
                'Transfert Orange vers Airtel'
            ),

            $this->transfert(
                'TRF-2026-0013',
                $transfertId,
                $comptes['0322223344'],
                '0333334455',
                50000,
                300,
                1.25,
                '2026-03-27 13:40:00',
                'Transfert Orange vers Airtel'
            ),

            $this->transfert(
                'TRF-2026-0014',
                $transfertId,
                $comptes['0372223344'],
                '0334445566',
                95000,
                600,
                1.50,
                '2026-04-26 11:35:00',
                'Transfert Orange vers Airtel'
            ),

            $this->transfert(
                'TRF-2026-0015',
                $transfertId,
                $comptes['0324445566'],
                '0335556677',
                125000,
                1000,
                2.00,
                '2026-06-27 15:45:00',
                'Transfert Orange vers Airtel'
            ),

            $this->transfert(
                'TRF-2026-0016',
                $transfertId,
                $comptes['0374445566'],
                '0336667788',
                175000,
                1000,
                2.00,
                '2026-07-15 16:50:00',
                'Transfert Orange vers Airtel'
            ),
        ];

        /*
         * Opération annulée :
         * elle ne doit pas entrer dans les gains.
         */
        $operationAnnulee = $this->transfert(
            'TRF-2026-0017',
            $transfertId,
            $comptes['0321112233'],
            '0347778899',
            30000,
            300,
            1.25,
            '2026-07-17 09:00:00',
            'Simulation de transfert annulé'
        );

        $operationAnnulee['statut'] = 'ANNULEE';

        $operations[] = $operationAnnulee;

        /*
         * Opération échouée :
         * elle ne doit pas entrer dans les gains.
         */
        $operationEchouee = $this->transfert(
            'TRF-2026-0018',
            $transfertId,
            $comptes['0371112233'],
            '0338889900',
            60000,
            600,
            1.50,
            '2026-07-18 10:10:00',
            'Simulation de transfert échoué'
        );

        $operationEchouee['statut'] = 'ECHOUEE';

        $operations[] = $operationEchouee;

        /*
         * Opération en attente :
         * elle ne doit pas entrer dans les gains.
         */
        $operationEnAttente = $this->transfert(
            'TRF-2026-0019',
            $transfertId,
            $comptes['0322223344'],
            '0389990011',
            45000,
            300,
            1.25,
            '2026-07-19 11:25:00',
            'Simulation de transfert en attente'
        );

        $operationEnAttente['statut'] = 'EN_ATTENTE';

        $operations[] = $operationEnAttente;

        $this->insererLot(
            'operations',
            $operations,
            'création des opérations'
        );
    }

    /**
     * Construit une opération de retrait.
     */
    private function retrait(
        string $reference,
        int $typeOperationId,
        int $compteSourceId,
        float $montant,
        float $frais,
        string $dateOperation
    ): array {
        return [
            'reference'               => $reference,
            'type_operation_id'       => $typeOperationId,
            'compte_source_id'        => $compteSourceId,
            'telephone_destinataire'  => null,
            'montant'                 => $montant,
            'frais'                   => $frais,
            'montant_total'           => $montant + $frais,
            'statut'                  => 'VALIDEE',
            'motif'                   => 'Simulation de retrait',
            'date_operation'          => $dateOperation,
        ];
    }

    /**
     * Construit une opération de transfert.
     */
    private function transfert(
        string $reference,
        int $typeOperationId,
        int $compteSourceId,
        string $telephoneDestinataire,
        float $montant,
        float $frais,
        float $tauxCommission,
        string $dateOperation,
        string $motif
    ): array {
        $montantCommission = round(
            $montant * $tauxCommission / 100,
            2
        );

        return [
            'reference'               => $reference,
            'type_operation_id'       => $typeOperationId,
            'compte_source_id'        => $compteSourceId,
            'telephone_destinataire'  => $telephoneDestinataire,
            'montant'                 => $montant,
            'frais'                   => $frais,
            'montant_total'           => (
                $montant
                + $frais
                + $montantCommission
            ),
            'statut'                  => 'VALIDEE',
            'motif'                   => $motif,
            'date_operation'          => $dateOperation,
        ];
    }

    /**
     * Insère plusieurs lignes et affiche l’erreur SQL réelle.
     */
    private function insererLot(
        string $table,
        array $donnees,
        string $contexte
    ): void {
        if (empty($donnees)) {
            return;
        }

        $resultat = $this->db
            ->table($table)
            ->insertBatch($donnees);

        if ($resultat === false) {
            $this->leverErreur(
                'Erreur pendant la ' . $contexte
            );
        }
    }

    /**
     * Lève une exception avec le message SQLite réel.
     */
    private function leverErreur(string $message): void
    {
        $erreur = $this->db->error();

        throw new RuntimeException(
            $message
            . ' : '
            . ($erreur['message'] ?? 'erreur SQL inconnue')
        );
    }
}