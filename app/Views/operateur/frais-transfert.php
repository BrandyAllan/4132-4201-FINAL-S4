<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <!-- En-tête -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 ms-4">

        <div>
            <h3 class="fw-bold mb-1">
                Frais de transfert
            </h3>

            <p class="text-muted mb-0">
                Gestion des barèmes appliqués aux opérations de transfert.
            </p>
        </div>

        <a
            href="<?= site_url('operateur/frais') ?>"
            class="btn btn-outline-secondary rounded-pill px-4"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Retour
        </a>

    </div>

    <!-- Messages -->
    <?php if (session()->getFlashdata('success')): ?>

        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>

            <?= esc(session()->getFlashdata('success')) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>

        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <?= esc(session()->getFlashdata('error')) ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

    <?php endif; ?>

    <?php if (!$typeTransfert): ?>

        <div class="alert alert-warning">
            <strong>Attention :</strong>

            le type d’opération ayant le code
            <code>TRANSFERT</code>
            n’existe pas dans la table
            <code>types_operations</code>.
        </div>

    <?php else: ?>

        <!-- Formulaire -->
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <h5 class="fw-bold mb-1">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>
                    Ajouter un nouveau barème
                </h5>

                <p class="text-muted small mb-0">
                    Définissez l’intervalle de transfert et les frais correspondants.
                </p>

            </div>

            <div class="card-body p-4">

                <form
                    action="<?= site_url('operateur/frais/transfert/ajouter') ?>"
                    method="post"
                >

                    <?= csrf_field() ?>

                    <div class="row g-3">

                        <!-- Montant minimum -->
                        <div class="col-lg-3 col-md-6">

                            <label
                                for="montant_min"
                                class="form-label fw-semibold"
                            >
                                Montant minimum
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    name="montant_min"
                                    id="montant_min"
                                    class="form-control"
                                    value="<?= old('montant_min') ?>"
                                    min="0"
                                    step="1"
                                    placeholder="Ex. 100"
                                    required
                                >

                                <span class="input-group-text">
                                    Ar
                                </span>

                            </div>

                        </div>

                        <!-- Montant maximum -->
                        <div class="col-lg-3 col-md-6">

                            <label
                                for="montant_max"
                                class="form-label fw-semibold"
                            >
                                Montant maximum
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    name="montant_max"
                                    id="montant_max"
                                    class="form-control"
                                    value="<?= old('montant_max') ?>"
                                    min="0"
                                    step="1"
                                    placeholder="Ex. 1 000"
                                >

                                <span class="input-group-text">
                                    Ar
                                </span>

                            </div>

                            <small class="text-muted">
                                Laissez vide si le barème n’a pas de limite.
                            </small>

                        </div>

                        <!-- Frais -->
                        <div class="col-lg-3 col-md-6">

                            <label
                                for="frais"
                                class="form-label fw-semibold"
                            >
                                Frais appliqués
                                <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <input
                                    type="number"
                                    name="frais"
                                    id="frais"
                                    class="form-control"
                                    value="<?= old('frais') ?>"
                                    min="0"
                                    step="1"
                                    placeholder="Ex. 100"
                                    required
                                >

                                <span class="input-group-text">
                                    Ar
                                </span>

                            </div>

                        </div>

                        <!-- Statut -->
                        <div class="col-lg-3 col-md-6">

                            <label
                                for="actif"
                                class="form-label fw-semibold"
                            >
                                Statut
                            </label>

                            <select
                                name="actif"
                                id="actif"
                                class="form-select"
                            >

                                <option
                                    value="1"
                                    <?= old('actif', '1') === '1' ? 'selected' : '' ?>
                                >
                                    Actif
                                </option>

                                <option
                                    value="0"
                                    <?= old('actif') === '0' ? 'selected' : '' ?>
                                >
                                    Inactif
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-4">

                        <button
                            type="submit"
                            class="btn btn-primary rounded-pill px-4"
                        >
                            <i class="bi bi-plus-lg me-1"></i>
                            Ajouter le barème
                        </button>

                    </div>

                </form>

            </div>

        </div>

        <!-- Tableau récapitulatif -->
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-0 pt-4 px-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <div>
                        <h5 class="fw-bold mb-1">
                            Barèmes de transfert actuels
                        </h5>

                        <p class="text-muted small mb-0">
                            <?= count($baremes) ?>
                            barème(s) enregistré(s)
                        </p>
                    </div>

                    <div style="max-width: 300px; width: 100%;">

                        <div class="input-group">

                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="search"
                                id="rechercheBareme"
                                class="form-control"
                                placeholder="Rechercher..."
                            >

                        </div>

                    </div>

                </div>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table
                        class="table table-hover align-middle mb-0"
                        id="tableBaremes"
                    >

                        <thead class="table-light">

                            <tr>
                                <th class="ps-4">#</th>
                                <th>Montant minimum</th>
                                <th>Montant maximum</th>
                                <th>Frais</th>
                                <th>Statut</th>
                                <th>Date de création</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (!empty($baremes)): ?>

                                <?php foreach ($baremes as $index => $bareme): ?>

                                    <tr>

                                        <td class="ps-4">
                                            <?= $index + 1 ?>
                                        </td>

                                        <td class="fw-semibold">
                                            <?= number_format(
                                                (float) $bareme['montant_min'],
                                                0,
                                                ',',
                                                ' '
                                            ) ?>
                                            Ar
                                        </td>

                                        <td>

                                            <?php if ($bareme['montant_max'] !== null): ?>

                                                <?= number_format(
                                                    (float) $bareme['montant_max'],
                                                    0,
                                                    ',',
                                                    ' '
                                                ) ?>
                                                Ar

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    Sans limite
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>
                                            <span class="fw-bold text-primary">
                                                <?= number_format(
                                                    (float) $bareme['frais'],
                                                    0,
                                                    ',',
                                                    ' '
                                                ) ?>
                                                Ar
                                            </span>
                                        </td>

                                        <td>

                                            <?php if ((int) $bareme['actif'] === 1): ?>

                                                <span class="badge rounded-pill text-bg-success">
                                                    Actif
                                                </span>

                                            <?php else: ?>

                                                <span class="badge rounded-pill text-bg-secondary">
                                                    Inactif
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>
                                            <?= esc($bareme['date_creation']) ?>
                                        </td>

                                        <td class="text-end pe-4">

                                            <div class="d-inline-flex gap-2">

                                                <?php if ((int) $bareme['actif'] === 1): ?>

                                                    <form
                                                        action="<?= site_url(
                                                            'operateur/frais/transfert/desactiver/'
                                                            . $bareme['id']
                                                        ) ?>"
                                                        method="post"
                                                    >
                                                        <?= csrf_field() ?>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-warning rounded-pill"
                                                            title="Désactiver"
                                                        >
                                                            <i class="bi bi-pause-circle"></i>
                                                        </button>
                                                    </form>

                                                <?php else: ?>

                                                    <form
                                                        action="<?= site_url(
                                                            'operateur/frais/transfert/activer/'
                                                            . $bareme['id']
                                                        ) ?>"
                                                        method="post"
                                                    >
                                                        <?= csrf_field() ?>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-sm btn-outline-success rounded-pill"
                                                            title="Activer"
                                                        >
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    </form>

                                                <?php endif; ?>

                                                <form
                                                    action="<?= site_url(
                                                        'operateur/frais/transfert/supprimer/'
                                                        . $bareme['id']
                                                    ) ?>"
                                                    method="post"
                                                    onsubmit="return confirm(
                                                        'Voulez-vous vraiment supprimer ce barème ?'
                                                    );"
                                                >
                                                    <?= csrf_field() ?>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger rounded-pill"
                                                        title="Supprimer"
                                                    >
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                    >

                                        <i class="bi bi-inbox display-5 text-muted"></i>

                                        <p class="text-muted mt-3 mb-0">
                                            Aucun barème de transfert enregistré.
                                        </p>

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const recherche = document.getElementById('rechercheBareme');
    const lignes = document.querySelectorAll('#tableBaremes tbody tr');

    if (!recherche) {
        return;
    }

    recherche.addEventListener('input', function () {
        const valeur = this.value.toLowerCase().trim();

        lignes.forEach(function (ligne) {
            const contenu = ligne.textContent.toLowerCase();

            ligne.style.display = contenu.includes(valeur)
                ? ''
                : 'none';
        });
    });
});
</script>

<?= $this->endSection() ?>