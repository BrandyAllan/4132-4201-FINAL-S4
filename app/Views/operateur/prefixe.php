<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="page-header mb-4">
    <div>
        <span class="page-kicker">CONFIGURATION</span>
        <h1 class="page-title">Gestion des préfixes</h1>
        <p class="page-description">
            Ajoutez et gérez les préfixes téléphoniques des opérateurs.
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

<div class="card border-0 shadow-sm rounded-4 mb-4">

    <div class="card-header bg-white border-0 px-4 pt-4">

        <div class="d-flex align-items-center gap-3">

            <div class="form-card-icon">
                <i class="bi bi-phone-fill"></i>
            </div>

            <div>
                <h2 class="h5 fw-bold mb-1">
                    Ajouter un préfixe
                </h2>

                <p class="text-secondary small mb-0">
                    Enregistrez un nouveau préfixe opérateur.
                </p>
            </div>

        </div>

    </div>

    <div class="card-body p-4">

        <form
            action="<?= site_url('operateur/prefixes/ajouter') ?>"
            method="post"
        >

            <?= csrf_field() ?>

            <div class="row g-3">

                <div class="col-lg-4">

                    <label for="prefixe" class="form-label fw-semibold">
                        Préfixe
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-telephone"></i>
                        </span>

                        <input
                            type="text"
                            class="form-control"
                            id="prefixe"
                            name="prefixe"
                            value="<?= old('prefixe') ?>"
                            placeholder="Exemple : 032"
                            maxlength="10"
                            required
                        >

                    </div>

                    <?php if (session('errors.prefixe')): ?>
                        <small class="text-danger">
                            <?= esc(session('errors.prefixe')) ?>
                        </small>
                    <?php endif; ?>

                </div>

                <div class="col-lg-3">

                    <label for="statut" class="form-label fw-semibold">
                        Statut
                    </label>

                    <select
                        class="form-select"
                        id="statut"
                        name="statut"
                    >
                        <option value="ACTIF" selected>
                            Actif
                        </option>

                        <option value="INACTIF">
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
                    Ajouter le préfixe
                </button>

            </div>

        </form>

    </div>

</div>

<div class="card border-0 shadow-sm rounded-4">

    <div class="card-header bg-white border-0 px-4 pt-4">

        <div
            class="d-flex flex-column flex-md-row
                   align-items-md-center justify-content-between gap-3"
        >

            <div>
                <h2 class="h5 fw-bold mb-1">
                    Liste des préfixes
                </h2>

                <p class="text-secondary small mb-0">
                    <?= count($prefixes ?? []) ?> préfixe(s) enregistré(s)
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
                        id="searchPrefix"
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
                id="prefixTable"
            >

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Préfixe</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th class="text-end">Actions</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (! empty($prefixes)): ?>

                        <?php foreach ($prefixes as $index => $item): ?>

                            <tr>

                                <td>
                                    <?= $index + 1 ?>
                                </td>

                                <td>

                                    <span class="prefix-badge">
                                        <?= esc($item['prefixe']) ?>
                                    </span>

                                </td>

                                <td>

                                    <?php if ($item['actif'] === 1): ?>

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

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary rounded-pill btn-modifier-prefixe"
                                        data-id="<?= esc($item['id']) ?>"
                                        data-prefixe="<?= esc($item['prefixe']) ?>"
                                        data-actif="<?= esc($item['actif']) ?>"
                                    >
                                        <i class="bi bi-pencil-square"></i>

                                        <span class="d-none d-xl-inline ms-1">
                                            Modifier
                                        </span>
                                    </button>

                                    <?php if ($item['actif'] === 1): ?>

                                        <form
                                            action="<?= site_url(
                                                'operateur/prefixes/desactiver/'
                                                . $item['id']
                                            ) ?>"
                                            method="get"
                                            class="d-inline"
                                            onsubmit="return confirm(
                                                'Voulez-vous vraiment désactiver ce préfixe ?'
                                            );"
                                        >

                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-pill"
                                                title="Désactiver"
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
                                                'operateur/prefixes/activer/'
                                                . $item['id']
                                            ) ?>"
                                            method="get"
                                            class="d-inline"
                                        >

                                            <?= csrf_field() ?>

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-success rounded-pill"
                                                title="Activer"
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
                                        <i class="bi bi-phone"></i>
                                    </span>

                                    <h3>Aucun préfixe enregistré</h3>

                                    <p>
                                        Utilisez le formulaire ci-dessus pour
                                        ajouter votre premier préfixe.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="prefixe-popup-overlay" id="prefixePopupOverlay">

            <div class="prefixe-popup">

                <div class="prefixe-popup-header">

                    <div class="d-flex align-items-center gap-3">

                        <span class="popup-icon">
                            <i class="bi bi-pencil-square"></i>
                        </span>

                        <div>
                            <h2>Modifier le préfixe</h2>

                            <p>
                                Modifiez les informations du préfixe sélectionné.
                            </p>
                        </div>

                    </div>

                    <button
                        type="button"
                        class="popup-close"
                        id="closePrefixePopup"
                    >
                        <i class="bi bi-x-lg"></i>
                    </button>

                </div>

                <form
                    action="<?= site_url('operateur/prefixes/modifier') ?>"
                    method="post"
                    id="formModifierPrefixe"
                >

                    <?= csrf_field() ?>

                    <input
                        type="hidden"
                        name="id"
                        id="modifierId"
                    >

                    <div class="mb-3">

                        <label
                            for="modifierPrefixe"
                            class="form-label fw-semibold"
                        >
                            Préfixe
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="bi bi-telephone"></i>
                            </span>

                            <input
                                type="text"
                                class="form-control"
                                id="modifierPrefixe"
                                name="prefixe"
                                placeholder="Exemple : 032"
                                maxlength="10"
                                required
                            >

                        </div>

                    </div>

                    <div class="mb-4">

                        <label
                            for="modifierActif"
                            class="form-label fw-semibold"
                        >
                            Statut
                        </label>

                        <select
                            class="form-select"
                            id="modifierActif"
                            name="actif"
                            required
                        >
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>

                    </div>

                    <div class="popup-actions">

                        <button
                            type="button"
                            class="btn btn-light rounded-pill px-4"
                            id="cancelPrefixePopup"
                        >
                            Annuler
                        </button>

                        <button
                            type="submit"
                            class="btn btn-save-prefixe rounded-pill px-4"
                        >
                            <i class="bi bi-check-circle-fill me-2"></i>
                            Enregistrer
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchPrefix');
        const table = document.getElementById('prefixTable');

        if (!searchInput || !table) {
            return;
        }

        searchInput.addEventListener('input', function () {
            const searchValue = this.value.toLowerCase().trim();
            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {
                const rowText = row.textContent.toLowerCase();

                row.style.display = rowText.includes(searchValue)
                    ? ''
                    : 'none';
            });
        });
    });

</script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script src="<?= base_url('assets/js/prefixe.js') ?>"></script>

<?= $this->endSection() ?>