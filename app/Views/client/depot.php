<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faire un Dépôt — MadaCash</title>

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/home.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/login.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

    <!-- Barre de Navigation MadaCash -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-login py-3">
        <div class="container justify-content-between">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('client/dashboard') ?>">
                <span class="brand-mark"><i class="bi bi-wallet2"></i></span>
                <span>Mada<span>Cash</span></span>
            </a>
            <a href="<?= base_url('client/dashboard') ?>" class="btn btn-sm btn-logout px-3 rounded-pill fw-bold">
                <i class="bi bi-arrow-left me-1"></i> Retour
            </a>
        </div>
    </nav>

    <!-- Contenu Principal -->
    <main class="login-container flex-grow-1">
        <div class="container d-flex justify-content-center">
            <div class="login-card">
                
                <div class="login-header text-center mb-4">
                    <h2>Alimenter mon compte</h2>
                    <p class="text-secondary mt-2 mb-0">Entrez le montant en Ariary que vous souhaitez déposer sur votre portefeuille électronique.</p>
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
                <form action="<?= base_url('client/depot') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="montant" class="form-label-custom">MONTANT DU DÉPÔT (AR)</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-cash-stack"></i></span>
                            <input 
                                type="number" 
                                name="montant" 
                                id="montant" 
                                class="form-control-custom" 
                                placeholder="Ex: 50000"
                                min="1"
                                step="any"
                                value="<?= old('montant') ?>"
                                required
                                autofocus
                            >
                        </div>
                        <div class="mt-2 text-start">
                            <small class="text-muted"><i class="bi bi-info-circle me-1"></i> Aucun frais n'est appliqué sur les dépôts en espèces.</small>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-custom text-uppercase">
                        Confirmer le dépôt <i class="bi bi-check-circle ms-2"></i>
                    </button>
                </form>
                
            </div>
        </div>
    </main>

    <!-- Footer MadaCash -->
    <footer class="py-4 text-center text-muted border-top bg-white mt-auto border-light">
        <div class="container">
            <small class="copyright">&copy; <?= date('Y') ?> Examen Projet Final - S4 Info & Design. Tous droits réservés.</small>
        </div>
    </footer>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>