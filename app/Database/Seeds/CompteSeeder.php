<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;

class CompteSeeder extends Seeder
{
    private int $nombreComptes = 100;

    public function run()
    {
        if (!$this->db->tableExists('comptes')) {
            throw new RuntimeException(
                'La table comptes est introuvable.'
            );
        }

        $prefixes = [
            '032',
            '033',
            '034',
            '038',
        ];

        $statuts = [
            'ACTIF',
            'ACTIF',
            'ACTIF',
            'ACTIF',
            'ACTIF',
            'ACTIF',
            'ACTIF',
            'ACTIF',
            'BLOQUE',
            'FERME',
        ];

        /*
         * Récupérer les numéros déjà présents
         * afin d'éviter les doublons.
         */
        $comptesExistants = $this->db
            ->table('comptes')
            ->select('telephone')
            ->get()
            ->getResultArray();

        $telephonesUtilises = [];

        foreach ($comptesExistants as $compte) {
            $telephonesUtilises[$compte['telephone']] = true;
        }

        $comptes = [];

        for ($i = 1; $i <= $this->nombreComptes; $i++) {
            do {
                $prefixe = $prefixes[array_rand($prefixes)];

                $telephone = $prefixe
                    . str_pad(
                        (string) random_int(0, 9999999),
                        7,
                        '0',
                        STR_PAD_LEFT
                    );
            } while (isset($telephonesUtilises[$telephone]));

            $telephonesUtilises[$telephone] = true;

            $statut = $statuts[array_rand($statuts)];

            /*
             * Un compte fermé possède ici un solde nul.
             */
            $solde = $statut === 'FERME'
                ? 0
                : random_int(5000, 5000000);

            /*
             * Date de création aléatoire
             * au cours des 12 derniers mois.
             */
            $joursEcoules = random_int(0, 365);

            $dateCreation = date(
                'Y-m-d H:i:s',
                strtotime("-{$joursEcoules} days")
            );

            $dateModification = random_int(1, 100) <= 35
                ? date(
                    'Y-m-d H:i:s',
                    strtotime(
                        $dateCreation
                        . ' +' . random_int(1, 30) . ' days'
                    )
                )
                : null;

            $comptes[] = [
                'telephone'         => $telephone,
                'solde'             => $solde,
                'statut'            => $statut,
                'date_creation'     => $dateCreation,
                'date_modification' => $dateModification,
            ];
        }

        $this->db->transStart();

        foreach (array_chunk($comptes, 50) as $lot) {
            $this->db
                ->table('comptes')
                ->insertBatch($lot);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            throw new RuntimeException(
                'La création des comptes a échoué.'
            );
        }

        echo count($comptes)
            . " comptes créés avec succès."
            . PHP_EOL;
    }
}