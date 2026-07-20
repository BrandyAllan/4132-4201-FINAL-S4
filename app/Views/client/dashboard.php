<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Portefeuille — MadaCash</title>

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/home.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/login.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/dashboard.css') ?>" rel="stylesheet">
</head>
<body>

    <!-- Barre de Navigation MadaCash -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-login py-3">
        <div class="container justify-content-between">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('/') ?>">
                <span class="brand-mark"><i class="bi bi-wallet2"></i></span>
                <span>Mada<span>Cash</span></span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white-50 d-none d-sm-inline fs-7"><strong class="text-white"><?= esc(session()->get('telephone')) ?></strong></span>
                <a href="<?= base_url('logout/client') ?>" class="btn btn-sm btn-logout px-3 rounded-pill fw-bold">
                    <i class="bi bi-box-arrow-right me-1"></i> Quitter
                </a>
            </div>
        </div>
    </nav>

    <!-- Contenu Principal -->
    <main class="container dashboard-container flex-grow-1">
        
        <!-- Carte de bienvenue dynamique -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="welcome-card p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h1 class="h3 fw-extrabold mb-1">Manao ahoana, Client !</h1>
                        <p class="text-white-50 mb-0">Ravi de vous revoir sur votre espace sécurisé Mobile Money.</p>
                    </div>
                    <!-- Zone d'affichage du statut du compte -->
                    <div class="status-badge py-2 px-3 rounded-pill d-flex align-items-center gap-2">
                        <span class="position-relative d-flex h-3 w-3">
                            <span class="animate-ping position-absolute inline-flex h-100 w-100 rounded-full bg-success opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-success"></span>
                        </span>
                        <small class="fw-bold text-uppercase tracking-wider">Compte Actif</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestion du message de succès après dépôt -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success rounded-4 border-0 p-4 mb-4 shadow-sm animate-fade-in">
        <div class="d-flex align-items-center gap-3 text-success fw-bold mb-2">
            <div class="bg-success bg-opacity-10 p-2 rounded-circle d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-check-circle-fill fs-4"></i>
            </div>
            <span class="fs-5">Opération validée !</span>
        </div>
        <p class="mb-0 text-success-emphasis fw-medium fs-7 ps-5">
            <?= session()->getFlashdata('success') ?>
        </p>
    </div>
<?php endif; ?>

<!-- Gestion d'une éventuelle erreur flash (ex: compte bloqué entre-temps) -->
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger rounded-4 border-0 p-4 mb-4 shadow-sm">
        <div class="d-flex align-items-center gap-3 text-danger fw-bold mb-2">
            <div class="bg-danger bg-opacity-10 p-2 rounded-circle d-inline-flex align-items-center justify-content-center">
                <i class="bi bi-exclamation-circle-fill fs-4"></i>
            </div>
            <span class="fs-5">Erreur technique</span>
        </div>
        <p class="mb-0 text-danger-emphasis fw-medium fs-7 ps-5">
            <?= session()->getFlashdata('error') ?>
        </p>
    </div>
<?php endif; ?>

        <!-- Section des opérations -->
        <div class="section-heading mb-4">
            <span class="section-kicker text-uppercase">Front-Office</span>
            <h2 class="h4 fw-extrabold text-dark mt-1">Opérations disponibles</h2>
        </div>
        
        <div class="row g-4">
            
            <!-- Action 1 : Solde -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= base_url('client/solde') ?>" class="action-card text-center">
                    <span class="action-icon-wrapper"><i class="bi bi-cash-stack"></i></span>
                    <h3 class="h6 fw-bold mb-1">Consulter mon solde</h3>
                    <small class="text-muted d-none d-sm-block">Vérifier votre crédit disponible</small>
                </a>
            </div>

            <!-- Action 2 : Dépôt -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= base_url('client/depot') ?>" class="action-card text-center">
                    <span class="action-icon-wrapper"><i class="bi bi-box-arrow-in-down-right"></i></span>
                    <h3 class="h6 fw-bold mb-1">Faire un dépôt</h3>
                    <small class="text-muted d-none d-sm-block">Alimenter votre compte</small>
                </a>
            </div>

            <!-- Action 3 : Retrait -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= base_url('client/retrait') ?>" class="action-card text-center">
                    <span class="action-icon-wrapper"><i class="bi bi-box-arrow-up-right"></i></span>
                    <h3 class="h6 fw-bold mb-1">Faire un retrait</h3>
                    <small class="text-muted d-none d-sm-block">Retirer des espèces</small>
                </a>
            </div>

            <!-- Action 4 : Transfert -->
            <div class="col-6 col-md-4 col-lg-3">
                <a href="<?= base_url('client/transfert') ?>" class="action-card text-center">
                    <span class="action-icon-wrapper"><i class="bi bi-arrow-left-right"></i></span>
                    <h3 class="h6 fw-bold mb-1">Transférer de l'argent</h3>
                    <small class="text-muted d-none d-sm-block">Envoyer vers un autre numéro</small>
                </a>
            </div>

            <!-- Action : Historique -->
            <div class="col-12 mt-4">
                <a href="<?= base_url('client/historique') ?>" class="action-card flex-row gap-4 justify-content-start px-4 py-3 align-items-center">
                    <span class="action-icon-wrapper mb-0"><i class="bi bi-clock-history fs-4"></i></span>
                    <div class="flex-grow-1">
                        <h3 class="h6 fw-bold mb-1 text-start">Historique des transactions</h3>
                        <small class="text-muted text-start d-block">Consulter la liste de vos dépôts, retraits et transferts effectués</small>
                    </div>
                    <i class="bi bi-chevron-right ms-auto text-muted fs-5"></i>
                </a>
            </div>

        </div>
    </main>

    <!-- Footer MadaCash uniforme -->
    <footer class="py-4 text-center text-muted border-top bg-white mt-auto border-light">
        <div class="container">
            <small class="copyright">&copy; <?= date('Y') ?> Examen Projet Final - S4 Info & Design. 4132 - 4201</small>
        </div>
    </footer>

    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>