<?= $this->extend('client/client_layout') ?>

<?= $this->section('title') ?>Effectuer un Transfert<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container d-flex justify-content-center">
    <div class="login-card">
        
        <div class="login-header text-center mb-4">
            <h2>Envoyer de l'argent</h2>
            <p class="text-secondary mt-2 mb-0">Transférez instantanément des fonds vers un autre compte.</p>
        </div>

        <!-- Affichage du solde disponible -->
        <div class="alert alert-light border text-center mb-4 p-3">
            <span class="text-muted d-block small">Solde disponible :</span>
            <span class="fs-4 fw-bold text-primary"><?= number_format($solde, 2, ',', ' ') ?> Ar</span>
        </div>

        <!-- Messages d'erreurs -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger rounded-4 border-0 p-3 mb-4">
                <ul class="mb-0 ps-3 text-danger fs-7 fw-semibold">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Formulaire de Transfert -->
        <form action="<?= base_url('client/transfert') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="destinataire" class="form-label-custom">NUMÉRO DU DESTINATAIRE</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-telephone"></i></span>
                    <input type="text" name="destinataire" id="destinataire" class="form-control-custom" placeholder="Ex: 034XXXXXXX" value="<?= old('destinataire') ?>" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label for="montant" class="form-label-custom">MONTANT À ENVOYER (AR)</label>
                <div class="input-group-custom">
                    <span class="input-icon"><i class="bi bi-arrow-left-right text-primary"></i></span>
                    <input type="number" name="montant" id="montant" class="form-control-custom" placeholder="Ex: 15000" min="1" step="any" value="<?= old('montant') ?>" required>
                </div>
            </div>

            <!-- Zone frais dynamiques -->
            <div id="frais-container" class="mb-3 text-center p-2" style="display: none;">
                <span class="text-secondary small">Frais de transfert : </span>
                <span id="montant-frais" class="fw-bold text-danger">0 Ar</span>
            </div>

            <!-- Option inclure frais de retrait -->
            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="inclureFraisRetrait" name="inclureFraisRetrait">
                <label class="form-check-label" for="inclureFraisRetrait">Inclure les frais de retrait lors de l'envoi</label>
            </div>

            <!-- Nouvelle ligne pour afficher les frais de retrait -->
            <div id="frais-retrait-container" class="mb-3 text-center p-2" style="display: none; background: #f8f9fa; border-radius: 8px;">
                <span class="text-secondary small">Frais de retrait inclus : </span>
                <span id="montant-frais-retrait" class="fw-bold text-success">0 Ar</span>
            </div>

            <button type="submit" class="btn-submit-custom text-uppercase btn-primary">
                Valider le transfert <i class="bi bi-send ms-2"></i>
            </button>
        </form>
    </div>
</div>

<script>
    const baremes = <?= json_encode($bareme) ?>;
    const inputMontant = document.getElementById('montant');
    const checkboxFrais = document.getElementById('inclureFraisRetrait');
    const containerFrais = document.getElementById('frais-container');
    const containerFraisRetrait = document.getElementById('frais-retrait-container');
    const spanFrais = document.getElementById('montant-frais');
    const spanFraisRetrait = document.getElementById('montant-frais-retrait');

    function calculerEtAfficher() {
        const montant = parseFloat(inputMontant.value);
        const item = baremes.find(b => montant >= Number(b.montant_min) && montant <= Number(b.montant_max));
        
        if (item && montant > 0) {
            // Affichage frais de transfert
            spanFrais.textContent = Number(item.frais).toLocaleString('fr-FR') + ' Ar';
            containerFrais.style.display = 'block';

            // Logique frais de retrait
            if (checkboxFrais.checked) {
                spanFraisRetrait.textContent = Number(item.frais).toLocaleString('fr-FR') + ' Ar';
                containerFraisRetrait.style.display = 'block';
            } else {
                containerFraisRetrait.style.display = 'none';
            }
        } else {
            containerFrais.style.display = 'none';
            containerFraisRetrait.style.display = 'none';
        }
    }

    inputMontant.addEventListener('input', calculerEtAfficher);
    checkboxFrais.addEventListener('change', calculerEtAfficher);
</script>

<?= $this->endSection() ?>