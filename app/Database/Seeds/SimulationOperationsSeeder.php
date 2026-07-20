<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;
use Throwable;

class SimulationOperationsSeeder extends Seeder
{
    private int $nombreOperations = 500;

    private int $nombreJours = 30;

    public function run()
    {
        $typeRetrait = $this->db
            ->table('types_operations')
            ->where('code', 'RET')
            ->get()
            ->getRowArray();

        $typeTransfert = $this->db
            ->table('types_operations')
            ->where('code', 'TRANS')
            ->get()
            ->getRowArray();

        if (!$typeRetrait) {
            throw new RuntimeException(
                'Le type RETRAIT est introuvable.'
            );
        }

        if (!$typeTransfert) {
            throw new RuntimeException(
                'Le type TRANSFERT est introuvable.'
            );
        }

        $comptes = $this->db
            ->table('comptes')
            ->where('statut', 'ACTIF')
            ->get()
            ->getResultArray();

        if (count($comptes) < 2) {
            throw new RuntimeException(
                'Il faut au moins deux comptes actifs.'
            );
        }

        $tableOperations = $this->db->table('operations');

        $this->db->transBegin();

        try {
            $nombreInsere = 0;

            for ($i = 1; $i <= $this->nombreOperations; $i++) {
                /*
                 * 45 % de retraits, 55 % de transferts.
                 */
                $estRetrait = random_int(1, 100) <= 45;

                $typeOperation = $estRetrait
                    ? $typeRetrait
                    : $typeTransfert;

                $bareme = $this->choisirBaremeAleatoire(
                    (int) $typeOperation['id']
                );

                if ($bareme === null) {
                    throw new RuntimeException(
                        'Aucun barème actif pour le type '
                        . $typeOperation['code']
                        . '.'
                    );
                }

                $montant = $this->genererMontantDansBareme(
                    $bareme
                );

                $frais = (float) $bareme['frais'];

                $montantTotal = $montant + $frais;

                $compteSource = $comptes[
                    array_rand($comptes)
                ];

                do {
                    $compteDestination = $comptes[
                        array_rand($comptes)
                    ];
                } while (
                    (int) $compteDestination['id']
                    === (int) $compteSource['id']
                );

                $operation = [
                    'reference'             => $this->genererReference($i),
                    'type_operation_id'     => (int) $typeOperation['id'],
                    'compte_source_id'      => (int) $compteSource['id'],
                    'compte_destination_id' => $estRetrait
                        ? null
                        : (int) $compteDestination['id'],
                    'montant'               => $montant,
                    'frais'                 => $frais,
                    'montant_total'         => $montantTotal,
                    'statut'                => 'VALIDEE',
                    'motif'                 => $estRetrait
                        ? 'Simulation d’un retrait'
                        : 'Simulation d’un transfert',
                    'date_operation'        => $this->genererDateAleatoire(),
                ];

                $insertionReussie = $tableOperations->insert(
                    $operation
                );

                if (!$insertionReussie) {
                    throw new RuntimeException(
                        'Échec de l’insertion de l’opération '
                        . $i
                        . PHP_EOL
                        . json_encode(
                            $operation,
                            JSON_UNESCAPED_UNICODE
                            | JSON_PRETTY_PRINT
                        )
                    );
                }

                $nombreInsere++;
            }

            if ($this->db->transStatus() === false) {
                throw new RuntimeException(
                    'La transaction contient une erreur.'
                );
            }

            $this->db->transCommit();

            echo $nombreInsere
                . " opérations créées avec succès."
                . PHP_EOL;
        } catch (Throwable $exception) {
            $this->db->transRollback();

            throw new RuntimeException(
                'Erreur pendant la génération : '
                . $exception->getMessage(),
                0,
                $exception
            );
        }
    }

    private function choisirBaremeAleatoire(
        int $typeOperationId
    ): ?array {
        $baremes = $this->db
            ->table('baremes_frais')
            ->where(
                'type_operation_id',
                $typeOperationId
            )
            ->where('actif', 1)
            ->get()
            ->getResultArray();

        if (empty($baremes)) {
            return null;
        }

        return $baremes[array_rand($baremes)];
    }

    private function genererMontantDansBareme(
        array $bareme
    ): int {
        $minimum = (int) $bareme['montant_min'];

        $maximum = $bareme['montant_max'] !== null
            ? (int) $bareme['montant_max']
            : $minimum + 1000000;

        if ($maximum < $minimum) {
            $maximum = $minimum;
        }

        return random_int($minimum, $maximum);
    }

    private function genererDateAleatoire(): string
    {
        $joursEnArriere = random_int(
            0,
            $this->nombreJours - 1
        );

        $heure = random_int(7, 21);
        $minute = random_int(0, 59);
        $seconde = random_int(0, 59);

        $date = strtotime(
            "-{$joursEnArriere} days"
        );

        return date('Y-m-d', $date)
            . ' '
            . sprintf(
                '%02d:%02d:%02d',
                $heure,
                $minute,
                $seconde
            );
    }

    private function genererReference(int $index): string
    {
        return sprintf(
            'SIM-%s-%04d-%04d',
            date('YmdHis'),
            $index,
            random_int(1000, 9999)
        );
    }
}