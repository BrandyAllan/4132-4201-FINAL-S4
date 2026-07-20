<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> — MadaCash</title>

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

    <!-- Contenu Principal Injecté -->
    <main class="login-container flex-grow-1">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer MadaCash -->
    <footer class="py-4 text-center text-muted border-top bg-white mt-auto border-light">
        <div class="container">
            <small class="copyright">&copy; <?= date('Y') ?> Examen Projet Final - S4 Info & Design. 4132 - 4201</small>
        </div>
    </footer>

    <!-- Scripts Communs -->
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/validation_client.js') ?>"></script>
</body>
</html>