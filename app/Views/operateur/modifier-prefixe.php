<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="row justify-content-center">

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

        <div class="col-lg-7">

            <div class="card border-0 shadow rounded-4">

                <div class="card-header bg-white py-4">

                    <div class="d-flex align-items-center">

                        <div class="form-card-icon me-3">

                            <i class="bi bi-pencil-square"></i>

                        </div>

                        <div>

                            <h3 class="fw-bold mb-1">

                                Modifier un préfixe

                            </h3>

                            <small class="text-secondary">

                                Modification des informations du préfixe.

                            </small>

                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <form
                        action="<?= site_url('operateur/prefixes/modifier') ?>"
                        method="post"
                    >

                        <?= csrf_field() ?>

                        <input
                            type="hidden"
                            name="id"
                            value="<?= $prefixe['id'] ?>"
                        >

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                Préfixe

                            </label>

                            <div class="input-group">

                                <span class="input-group-text">

                                    <i class="bi bi-phone"></i>

                                </span>

                                <input
                                    type="text"
                                    name="prefixe"
                                    class="form-control"
                                    value="<?= esc($prefixe['prefixe']) ?>"
                                    required
                                >

                            </div>

                        </div>

                        <div class="mb-4">

                            <label class="form-label fw-semibold">

                                Statut

                            </label>

                            <select
                                class="form-select"
                                name="actif"
                            >

                                <option
                                    value="1"
                                    <?= $prefixe['actif'] == 1 ? 'selected' : '' ?>
                                >
                                    Actif
                                </option>

                                <option
                                    value="0"
                                    <?= $prefixe['actif'] == 0 ? 'selected' : '' ?>
                                >
                                    Inactif
                                </option>

                            </select>

                        </div>

                        <div class="d-flex justify-content-end">

                            <a
                                href="<?= site_url('operateur/prefixes') ?>"
                                class="btn btn-light rounded-pill px-4 me-2"
                            >

                                Retour

                            </a>

                            <button
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