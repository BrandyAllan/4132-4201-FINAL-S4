<?= $this->extend('operateur/layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">
                Gestion des frais
            </h3>
            <p class="text-muted mb-0">
                Choisissez le type de frais à gérer.
            </p>
        </div>
    </div>

    <div class="row g-4">

        <!-- Frais de retrait -->
        <div class="col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body text-center py-5">

                    <div class="mb-4">
                        <i class="bi bi-cash-stack display-3 text-success"></i>
                    </div>

                    <h4 class="fw-bold">
                        Frais de retrait
                    </h4>

                    <p class="text-muted mt-3">
                        Gérer les barèmes des frais appliqués aux opérations de retrait.
                    </p>

                    <a href="<?= site_url('operateur/frais/retrait') ?>"
                       class="btn btn-success rounded-pill px-4">
                        Gérer
                    </a>

                </div>

            </div>

        </div>

        <!-- Frais de transfert -->
        <div class="col-md-6">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body text-center py-5">

                    <div class="mb-4">
                        <i class="bi bi-arrow-left-right display-3 text-primary"></i>
                    </div>

                    <h4 class="fw-bold">
                        Frais de transfert
                    </h4>

                    <p class="text-muted mt-3">
                        Gérer les barèmes des frais appliqués aux opérations de transfert.
                    </p>

                    <a href="<?= site_url('operateur/frais/transfert') ?>"
                       class="btn btn-primary rounded-pill px-4">
                        Gérer
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?= $this->endSection() ?>