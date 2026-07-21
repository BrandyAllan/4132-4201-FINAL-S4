<?= $this->extend('client/client_layout') ?>

<?= $this->section('title') ?>Faire un Dépôt<?= $this->endSection() ?>

<?= $this->section('content') ?>

        <div class="container d-flex justify-content-center">
            <div class="login-card">
                
                <div class="login-header text-center mb-4">
                    <h2>Pourcentage d'épargne</h2>
                    <p class="text-secondary mt-2 mb-0">Entrez le pourcentage d'épargne que vous souhaitez pour votre portefeuille électronique.</p>
                </div>

                <!-- Gestion des messages d'erreurs (Flashdata de validation) -->
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger rounded-4 border-0 p-3 mb-4">
                        <div class="d-flex align-items-center gap-2 text-danger fw-bold mb-1 fs-7">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Erreur de saisie :</span>
                        </div>
                        <ul class="mb-0 ps-3 text-danger fs-7 fw-semibold">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Message d'erreur simple -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="error-box mb-4">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de Dépôt -->
                <form action="<?= base_url('client/epargne') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="pourcentage" class="form-label-custom">POURCENTAGE D'EPARGNE (%)</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-cash-stack"></i></span>
                            <input 
                                type="number" 
                                name="pourcentage" 
                                id="pourcentage" 
                                class="form-control-custom" 
                                placeholder="Ex: 50000"
                                min="1"
                                step="any"
                                value="<?= old('pourcentage') ?>"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-custom text-uppercase">
                        Confirmer <i class="bi bi-check-circle ms-2"></i>
                    </button>
                </form>
                
            </div>
        </div>
        
<?= $this->endSection() ?>