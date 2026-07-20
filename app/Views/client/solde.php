<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Solde — MadaCash</title>

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/home.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/login.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/solde.css') ?>" rel="stylesheet">
    
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
    <main class="container dashboard-container flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                
                <!-- En-tête de section -->
                <div class="section-heading mb-4">
                    <span class="section-kicker text-uppercase">Consultation</span>
                    <h2 class="h4 fw-extrabold text-dark mt-1">Mon Solde Actuel</h2>
                </div>

                <!-- Carte du Solde -->
                <div class="balance-card mb-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <small class="text-white-50 text-uppercase tracking-wider fs-7">Solde disponible</small>
                            <!-- Formatage du solde avec 2 décimales -->
                            <div class="balance-amount fw-extrabold mt-1">
                                <?= number_format($solde, 2, ',', ' ') ?> <span class="currency">Ar</span>
                            </div>
                        </div>
                        <span class="fs-3 text-white-50"><i class="bi bi-shield-check"></i></span>
                    </div>
                    
                    <div class="border-top border-secondary pt-3 mt-2 d-flex justify-content-between align-items-center text-white-50 fs-7">
                        <span>Numéro de compte :</span>
                        <strong class="text-white"><?= esc(session()->get('telephone')) ?></strong>
                    </div>
                </div>

                <!-- Bouton d'action rapide alternative -->
                <div class="d-grid">
                    <a href="<?= base_url('client/dashboard') ?>" class="btn btn-light border py-3 rounded-pill fw-bold text-dark transition">
                        <i class="bi bi-grid-1x2-fill me-2 text-muted"></i> Revenir au menu principal
                    </a>
                </div>

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