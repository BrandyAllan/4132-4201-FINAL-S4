<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="row justify-content-center">

        <div class="col-lg-7">

            <div class="mb-4">

                <a
                    href="<?= site_url('operateur/types-operations') ?>"
                    class="btn btn-light rounded-pill px-4"
                >

                    <i class="bi bi-arrow-left me-2"></i>

                    Retour à la liste

                </a>

            </div>

            <div class="card border-0 shadow-sm rounded-4">

                <div class="card-header bg-white border-0 px-4 pt-4">

                    <div class="d-flex align-items-center gap-3">

                        <div class="form-card-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>

                        <div>

                            <h1 class="h4 fw-bold mb-1">
                                Modifier le type d’opération
                            </h1>

                            <p class="text-secondary small mb-0">
                                Modifiez les informations du type d’opération.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <?php if ($error = session()->getFlashdata('error')): ?>

                        <div class="alert alert-danger">
                            <?= esc($error) ?>
                        </div>

                    <?php endif; ?>

                    <form
                        action="<?= site_url(
                            'operateur/types-operations/modifier'
                        ) ?>"
                        method="post"
                    >

                        <?= csrf_field() ?>

                        <input
                            type="hidden"
                            name="id"
                            value="<?= esc($typeOperation['id']) ?>"
                        >

                        <div class="mb-4">

                            <label
                                for="code"
                                class="form-label fw-semibold"
                            >
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
                                    value="<?= esc(
                                        old(
                                            'code',
                                            $typeOperation['code']
                                        )
                                    ) ?>"
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

                        <div class="mb-4">

                            <label
                                for="libelle"
                                class="form-label fw-semibold"
                            >
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
                                    value="<?= esc(
                                        old(
                                            'libelle',
                                            $typeOperation['libelle']
                                        )
                                    ) ?>"
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

                        <div class="mb-4">

                            <label
                                for="actif"
                                class="form-label fw-semibold"
                            >
                                Statut
                            </label>

                            <?php
                                $actif = old(
                                    'actif',
                                    (string) $typeOperation['actif']
                                );
                            ?>

                            <select
                                class="form-select"
                                id="actif"
                                name="actif"
                                required
                            >

                                <option
                                    value="1"
                                    <?= $actif === '1' ? 'selected' : '' ?>
                                >
                                    Actif
                                </option>

                                <option
                                    value="0"
                                    <?= $actif === '0' ? 'selected' : '' ?>
                                >
                                    Inactif
                                </option>

                            </select>

                        </div>

                        <div class="d-flex justify-content-end gap-2">

                            <a
                                href="<?= site_url(
                                    'operateur/types-operations'
                                ) ?>"
                                class="btn btn-light rounded-pill px-4"
                            >
                                Annuler
                            </a>

                            <button
                                type="submit"
                                class="btn btn-primary-custom rounded-pill px-4"
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

</div>

<?= $this->endSection() ?>