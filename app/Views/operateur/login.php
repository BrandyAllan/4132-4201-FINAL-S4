<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Connexion à l'espace administrateur MadaCash.">
    <title>Connexion administrateur — MadaCash</title>

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/admin-login.css') ?>" rel="stylesheet">
</head>
<body>

<main class="login-page">
    <div class="login-orb login-orb-one"></div>
    <div class="login-orb login-orb-two"></div>

    <div class="container position-relative">
        <div class="row min-vh-100 align-items-center justify-content-center py-5">
            <div class="col-xl-10">
                <div class="login-wrapper">

                    <section class="login-presentation d-none d-lg-flex">
                        <a href="<?= site_url('/') ?>" class="brand">
                            <span class="brand-icon">
                                <i class="bi bi-wallet2"></i>
                            </span>
                            <span>Mada<span>Cash</span></span>
                        </a>

                        <div class="presentation-content">
                            <span class="security-badge">
                                <i class="bi bi-shield-lock-fill"></i>
                                Espace sécurisé
                            </span>

                            <h1>Gérez votre opérateur mobile money.</h1>

                            <div class="presentation-features">
                                <div class="feature-item">
                                    <span><i class="bi bi-bar-chart-fill"></i></span>
                                    <div>
                                        <strong>Tableau de bord</strong>
                                        <small>Suivi des activités en temps réel</small>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <span><i class="bi bi-cash-coin"></i></span>
                                    <div>
                                        <strong>Gestion des frais</strong>
                                        <small>Barèmes configurables par tranche</small>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <span><i class="bi bi-people-fill"></i></span>
                                    <div>
                                        <strong>Comptes clients</strong>
                                        <small>Consultation des soldes et statuts</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p class="presentation-footer">
                            <i class="bi bi-lock-fill"></i>
                            Connexion réservée aux administrateurs autorisés
                        </p>
                    </section>

                    <section class="login-form-section">
                        <div class="mobile-brand d-lg-none">
                            <a href="<?= site_url('/') ?>" class="brand">
                                <span class="brand-icon">
                                    <i class="bi bi-wallet2"></i>
                                </span>
                                <span>Mada<span>Cash</span></span>
                            </a>
                        </div>

                        <div class="login-form-container">
                            <div class="login-icon">
                                <i class="bi bi-person-lock"></i>
                            </div>

                            <span class="form-kicker">ADMINISTRATION</span>
                            <h2>Bienvenue</h2>
                            <p class="form-description">
                                Connectez-vous pour accéder au tableau de bord.
                            </p>

                            <?php if (session()->getFlashdata('error')): ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                                    <?= esc(session()->getFlashdata('error')) ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?= site_url('operateur/login') ?>" method="post" class="mt-4">
                                <?= csrf_field() ?>

                                <div class="mb-3">

                                    <div class="input-group custom-input">
                                        <span class="input-group-text">
                                            <i class="bi bi-person"></i>
                                        </span>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="identifiant"
                                            name="identifiant"
                                            value="<?= old('identifiant') ?>"
                                            placeholder="Entrez votre identifiant"
                                            autocomplete="username"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="mb-3">        

                                    <div class="input-group custom-input">
                                        <span class="input-group-text">
                                            <i class="bi bi-lock"></i>
                                        </span>

                                        <input
                                            type="password"
                                            class="form-control"
                                            id="mot_de_passe"
                                            name="mot_de_passe"
                                            placeholder="Entrez votre mot de passe"
                                            autocomplete="current-password"
                                            required
                                        >

                                        <button
                                            class="btn password-toggle"
                                            type="button"
                                            id="togglePassword"
                                            aria-label="Afficher ou masquer le mot de passe"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>

                                    <div class="d-flex justify-content-end mt-2">
                                        <a href="#" class="forgot-link">
                                            Mot de passe oublié ?
                                        </a>
                                    </div>
                                </div>

                                <button type="submit" class="btn login-button w-100">
                                    <span>Se connecter</span>
                                    <i class="bi bi-arrow-right"></i>
                                </button>
                            </form>

                            <div class="back-home">
                                <a href="<?= site_url('/') ?>">
                                    <i class="bi bi-arrow-left"></i>
                                    Retour à l'accueil
                                </a>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</main>

<script src="<?= base_url('assets/js/admin-login.js') ?>"></script>
</body>
</html>
