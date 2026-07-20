<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="page-header mb-4 ms-4">
    <div>
        <span class="page-kicker">CONFIGURATION</span>

        <h1 class="page-title">
            Gestion des types d’opérations
        </h1>

        <p class="page-description">
            Ajoutez et gérez les différents types d’opérations.
        </p>
    </div>
</div>

<?php if ($success = session()->getFlashdata('success')): ?>

    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="bi bi-check-circle-fill me-2"></i>

        <?= esc($success) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Fermer"
        ></button>

    </div>

<?php endif; ?>

<?php if ($error = session()->getFlashdata('error')): ?>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="bi bi-exclamation-circle-fill me-2"></i>

        <?= esc($error) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Fermer"
        ></button>

    </div>

<?php endif; ?>


<!-- Formulaire d’ajout -->
<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-header bg-white border-0 px-4 pt-4">

        <div class="d-flex align-items-center gap-3">

            <div class="form-card-icon">
                <i class="bi bi-arrow-left-right"></i>
            </div>

            <div>

                <h2 class="h5 fw-bold mb-1">
                    Ajouter un type d’opération
                </h2>

                <p class="text-secondary small mb-0">
                    Enregistrez un nouveau type d’opération.
                </p>

            </div>

        </div>

    </div>

    <div class="card-body p-4">

        <form
            action="<?= site_url('operateur/types-operations/ajouter') ?>"
            method="post"
        >

            <?= csrf_field() ?>

            <div class="row g-3">

                <div class="col-lg-3">

                    <label for="code" class="form-label fw-semibold">
                        Code
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-hash"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control"
                            id="code"
                            name="code"
                            value="<?= old('code') ?>"
                            placeholder="Exemple : RETRAIT"
                            maxlength="30"
                            required
                        >

                    </div>

                    <?php if (session('errors.code')): ?>

                        <small class="text-danger">
                            <?= esc(session('errors.code')) ?>
                        </small>

                    <?php endif; ?>

                </div>

                <div class="col-lg-5">

                    <label for="libelle" class="form-label fw-semibold">
                        Libellé
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-card-text"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control"
                            id="libelle"
                            name="libelle"
                            value="<?= old('libelle') ?>"
                            placeholder="Exemple : Retrait d’argent"
                            maxlength="100"
                            required
                        >

                    </div>

                    <?php if (session('errors.libelle')): ?>

                        <small class="text-danger">
                            <?= esc(session('errors.libelle')) ?>
                        </small>

                    <?php endif; ?>

                </div>

                <div class="col-lg-3">

                    <label for="actif" class="form-label fw-semibold">
                        Statut
                    </label>

                    <select
                        class="form-select"
                        id="actif"
                        name="actif"
                        required
                    >

                        <option value="1" <?= old('actif') !== '0' ? 'selected' : '' ?>>
                            Actif
                        </option>

                        <option value="0" <?= old('actif') === '0' ? 'selected' : '' ?>>
                            Inactif
                        </option>

                    </select>

                </div>

            </div>

            <div class="d-flex justify-content-end mt-4">

                <button
                    type="reset"
                    class="btn btn-light rounded-pill px-4 me-2"
                >

                    <i class="bi bi-arrow-counterclockwise me-2"></i>

                    Réinitialiser

                </button>

                <button
                    type="submit"
                    class="btn btn-primary-custom rounded-pill px-4"
                >

                    <i class="bi bi-plus-circle-fill me-2"></i>

                    Ajouter

                </button>

            </div>

        </form>

    </div>

</div>


<!-- Liste -->
<div class="card border-0 shadow-sm rounded-4">

    <div class="card-header bg-white border-0 px-4 pt-4">

        <div
            class="d-flex flex-column flex-md-row
                   align-items-md-center justify-content-between gap-3"
        >

            <div>

                <h2 class="h5 fw-bold mb-1">
                    Liste des types d’opérations
                </h2>

                <p class="text-secondary small mb-0">
                    <?= count($typesOperations ?? []) ?>
                    type(s) d’opération enregistré(s)
                </p>

            </div>

            <div class="prefix-search">

                <div class="input-group">

                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>

                    <input
                        type="search"
                        class="form-control"
                        id="searchTypeOperation"
                        placeholder="Rechercher..."
                    >

                </div>

            </div>

        </div>

    </div>

    <div class="card-body p-4">

        <div class="table-responsive">

            <table
                class="table table-hover align-middle"
                id="typeOperationTable"
            >

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Libellé</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th class="text-end">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (! empty($typesOperations)): ?>

                        <?php foreach ($typesOperations as $index => $item): ?>

                            <tr>

                                <td>
                                    <?= $index + 1 ?>
                                </td>

                                <td>

                                    <span class="prefix-badge">
                                        <?= esc($item['code']) ?>
                                    </span>

                                </td>

                                <td>
                                    <strong>
                                        <?= esc($item['libelle']) ?>
                                    </strong>
                                </td>

                                <td>

                                    <?php if ((int) $item['actif'] === 1): ?>

                                        <span class="badge rounded-pill text-bg-success">

                                            <i class="bi bi-check-circle me-1"></i>

                                            Actif

                                        </span>

                                    <?php else: ?>

                                        <span class="badge rounded-pill text-bg-secondary">

                                            <i class="bi bi-x-circle me-1"></i>

                                            Inactif

                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <?php if (! empty($item['date_creation'])): ?>

                                        <?= date(
                                            'd/m/Y H:i',
                                            strtotime($item['date_creation'])
                                        ) ?>

                                    <?php else: ?>

                                        —

                                    <?php endif; ?>

                                </td>

                                <td class="text-end">

                                    <a
                                        href="<?= site_url(
                                            'operateur/types-operations/modifier/'
                                            . $item['id']
                                        ) ?>"
                                        class="btn btn-sm btn-outline-primary rounded-pill"
                                    >

                                        <i class="bi bi-pencil-square"></i>

                                        <span class="d-none d-xl-inline ms-1">
                                            Modifier
                                        </span>

                                    </a>

                                    <?php if ((int) $item['actif'] === 1): ?>

                                        <form
                                            action="<?= site_url(
                                                'operateur/types-operations/desactiver/'
                                                . $item['id']
                                            ) ?>"
                                            method="post"
                                            class="d-inline"
                                            onsubmit="return confirm(
                                                'Voulez-vous vraiment désactiver ce type d’opération ?'
                                            );"
                                        >

                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill"
                                            >

                                                <i class="bi bi-slash-circle"></i>

                                                <span class="d-none d-xl-inline ms-1">
                                                    Désactiver
                                                </span>

                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <form
                                            action="<?= site_url(
                                                'operateur/types-operations/activer/'
                                                . $item['id']
                                            ) ?>"
                                            method="post"
                                            class="d-inline"
                                        >

                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-success rounded-pill"
                                            >

                                                <i class="bi bi-check-circle"></i>

                                                <span class="d-none d-xl-inline ms-1">
                                                    Activer
                                                </span>

                                            </button>

                                        </form>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6" class="text-center py-5">

                                <div class="empty-state">

                                    <span class="empty-state-icon">
                                        <i class="bi bi-arrow-left-right"></i>
                                    </span>

                                    <h3>
                                        Aucun type d’opération enregistré
                                    </h3>

                                    <p>
                                        Utilisez le formulaire ci-dessus pour
                                        ajouter votre premier type d’opération.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput =
            document.getElementById('searchTypeOperation');

        const table =
            document.getElementById('typeOperationTable');

        if (!searchInput || !table) {
            return;
        }

        searchInput.addEventListener('input', function () {
            const searchValue =
                this.value.toLowerCase().trim();

            const rows =
                table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                const rowText =
                    row.textContent.toLowerCase();

                row.style.display =
                    rowText.includes(searchValue)
                        ? ''
                        : 'none';
            });
        });
    });
</script>

<?= $this->endSection() ?>