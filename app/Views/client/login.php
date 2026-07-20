<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connectez-vous à votre espace client MadaCash.">
    <title>Connexion — MadaCash</title>

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/home.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/login.css') ?>" rel="stylesheet">

</head>
<body>


<nav class="navbar navbar-expand-lg navbar-dark navbar-login">
    <div class="container justify-content-center justify-content-lg-between">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('/') ?>">
            <span class="brand-mark"><i class="bi bi-wallet2"></i></span>
            <span>Mada<span>Cash</span></span>
        </a>
        <a href="<?= site_url('/') ?>" class="btn btn-outline-light rounded-pill px-3 btn-sm d-none d-lg-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-2"></i> Retour à l'accueil
        </a>
    </div>
</nav>


<main class="login-container">
    <div class="container d-flex justify-content-center">
        <div class="login-card">
            
            <div class="login-header text-center mb-4">
                <h2>Espace Client</h2>
                <p class="text-secondary mt-2 mb-0">Pas besoin de compte au préalable. Votre numéro de téléphone suffit pour vous connecter ou créer un profil instantanément</p>
            </div>


            <?php if (session()->getFlashdata('erreur')) : ?>
                <div class="error-box mb-4">
                    <i class="bi bi-exclamation-circle-fill fs-5"></i>
                    <div><?= session()->getFlashdata('erreur') ?></div>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('login/doLogin') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="mb-4">
                    <label for="telephone" class="form-label-custom">NUMÉRO DE TÉLÉPHONE</label>
                    <div class="input-group-custom">
                        <span class="input-icon"><i class="bi bi-phone"></i></span>
                        <input 
                            type="tel" 
                            name="telephone" 
                            id="telephone" 
                            class="form-control-custom" 
                            placeholder="Ex: 033 12 345 67"
                            value="<?= old('telephone') ?>"
                            required
                            autofocus
                        >
                    </div>
                    <div class="mt-3 text-start">
                        <small class="text-muted d-block mb-1">Préfixes autorisés par l'opérateur :</small>
                        
                        <?php if (!empty($prefixes)){ 
                            foreach ($prefixes as $prefixe){ ?>
                            <span class="prefix-badge"><?= esc($prefixe) ?></span>
                    <?php }
                        } else { ?>
                            <span class="text-danger small">Aucun opérateur actif actuellement.</span>
                    <?php } ?>
                    </div>
                </div>

                <button type="submit" class="btn-submit-custom text-uppercase">
                    Accéder à mon portefeuille <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>
            
        </div>
    </div>
</main>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>