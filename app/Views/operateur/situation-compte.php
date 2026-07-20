<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <div class="page-header mb-4 ms-4">
        <div>
            <span class="page-kicker">CONFIGURATION</span>
            <h1 class="page-title">Situation des comptes</h1>
            <p class="page-description">
                Consultation des soldes et des opérateurs
            </p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <form
                method="get"
                action="<?= site_url(
                    'operateur/comptes'
                ) ?>"
            >
                <div class="row g-3 align-items-end">

                    <div class="col-12 col-md-4">
                        <label
                            for="telephone"
                            class="form-label"
                        >
                            Téléphone
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="telephone"
                            name="telephone"
                            placeholder="Exemple : 034"
                            value="<?= esc($telephone) ?>"
                        >
                    </div>

                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-search"></i>
                                Rechercher
                            </button>

                            <a
                                href="<?= site_url(
                                    'operateur/comptes'
                                ) ?>"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-arrow-clockwise"></i>
                                Réinitialiser
                            </a>

                            <button
                                type="submit"
                                name="ordre"
                                value="telephone"
                                class="btn btn-outline-primary"
                            >
                                <i class="bi bi-sort-alpha-down"></i>
                                Ordre téléphone
                            </button>

                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div
            class="card-header bg-white
                   d-flex justify-content-between
                   align-items-center py-3"
        >
            <h2 class="h5 mb-0">
                Liste des comptes
            </h2>

            <span class="badge text-bg-primary">
                <?= count($comptes) ?>
                résultat(s) sur cette page
            </span>
        </div>

        <div class="table-responsive">
            <table
                class="table table-hover
                       align-middle mb-0"
            >
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Téléphone</th>
                        <th>Préfixe</th>
                        <th>Opérateur</th>
                        <th class="text-end">Solde</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (!empty($comptes)): ?>

                    <?php foreach ($comptes as $compte): ?>
                        <tr>
                            <td>
                                <?= esc($compte['id']) ?>
                            </td>

                            <td class="fw-semibold">
                                <?= esc(
                                    $compte['telephone']
                                ) ?>
                            </td>

                            <td>
                                <?php if (
                                    !empty($compte['prefixe'])
                                ): ?>
                                    <span
                                        class="badge text-bg-light
                                               border"
                                    >
                                        <?= esc(
                                            $compte['prefixe']
                                        ) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-secondary">
                                        Non identifié
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (
                                    !empty(
                                        $compte['operateur_nom']
                                    )
                                ): ?>
                                    <?= esc(
                                        $compte['operateur_nom']
                                    ) ?>
                                <?php else: ?>
                                    <span class="text-danger">
                                        Opérateur inconnu
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end fw-semibold">
                                <?= number_format(
                                    (float) $compte['solde'],
                                    0,
                                    ',',
                                    ' '
                                ) ?>
                                Ar
                            </td>

                            <td>
                                <?php
                                $classeStatut = match (
                                    $compte['statut']
                                ) {
                                    'ACTIF' =>
                                        'text-bg-success',

                                    'BLOQUE' =>
                                        'text-bg-warning',

                                    'FERME' =>
                                        'text-bg-secondary',

                                    default =>
                                        'text-bg-light',
                                };
                                ?>

                                <span
                                    class="badge <?= $classeStatut ?>"
                                >
                                    <?= esc(
                                        $compte['statut']
                                    ) ?>
                                </span>
                            </td>

                            <td>
                                <?= esc(
                                    date(
                                        'd/m/Y H:i',
                                        strtotime(
                                            $compte[
                                                'date_creation'
                                            ]
                                        )
                                    )
                                ) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td
                            colspan="7"
                            class="text-center py-5"
                        >
                            <i
                                class="bi bi-search
                                       fs-1 text-secondary"
                            ></i>

                            <p class="text-secondary mt-2 mb-0">
                                Aucun compte trouvé.
                            </p>
                        </td>
                    </tr>

                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (isset($pager)): ?>
            <div class="card-footer bg-white py-3">
                <?= $pager->links(
                    'situation_comptes',
                    'bootstrap_full'
                ) ?>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>
