<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faire un Retrait — MadaCash</title>

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/home.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/login.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

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

    <main class="login-container flex-grow-1">
        <div class="container d-flex justify-content-center">
            <div class="login-card">
                
                <div class="login-header text-center mb-4">
                    <h2>Retirer des espèces</h2>
                    <p class="text-secondary mt-2 mb-0">Indiquez le montant à retirer de votre solde disponible.</p>
                </div>

                <!-- Messages d'erreurs Flashdata -->
                <?php if (session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger rounded-4 border-0 p-3 mb-4">
                        <ul class="mb-0 ps-3 text-danger fs-7 fw-semibold">
                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger rounded-4 border-0 p-3 mb-4 d-flex align-items-center gap-2 text-danger fw-bold fs-7">
                        <i class="bi bi-exclamation-circle-fill fs-5"></i>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de Retrait -->
                <form action="<?= base_url('client/retrait') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="montant" class="form-label-custom">MONTANT À RETIRER (AR)</label>
                        <div class="input-group-custom">
                            <span class="input-icon"><i class="bi bi-dash-circle-fill text-danger"></i></span>
                            <input 
                                type="number" 
                                name="montant" 
                                id="montant" 
                                class="form-control-custom" 
                                placeholder="Ex: 20000"
                                min="1"
                                step="any"
                                value="<?= old('montant') ?>"
                                required
                                autofocus
                            >
                        </div>
                    </div>

                    <button type="submit" class="btn-submit-custom text-uppercase btn-danger bg-dark border-dark">
                        Confirmer le retrait <i class="bi bi-arrow-up-right ms-2"></i>
                    </button>
                </form>
                
            </div>
        </div>
    </main>

    <footer class="py-4 text-center text-muted border-top bg-white mt-auto border-light">
        <div class="container">
            <small class="copyright">&copy; <?= date('Y') ?> Examen Projet Final - S4 Info & Design. Tous droits réservés.</small>
        </div>
    </footer>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>