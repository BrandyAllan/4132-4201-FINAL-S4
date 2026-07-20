<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Plateforme mobile money : dépôt, retrait et transfert d'argent.">
    <title>MadaCash — Votre argent, simplement</title>

    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/home.css') ?>" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('/') ?>">
            <span class="brand-mark"><i class="bi bi-wallet2"></i></span>
            <span>Mada<span>Cash</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarContent" aria-controls="navbarContent"
                aria-expanded="false" aria-label="Ouvrir le menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="#accueil">Accueil</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#avantages">Avantages</a></li>
                <li class="nav-item"><a class="nav-link" href="#fonctionnement">Comment ça marche ?</a></li>
            </ul>

            <a href="<?= site_url('connexion/client') ?>" class="btn btn-light rounded-pill px-4 fw-semibold">
                <i class="bi bi-person-circle me-2"></i>Mon compte
            </a>

            <a href="<?= site_url('connexion/admin') ?>" class="btn btn-outline-light rounded-pill px-4 fw-semibold ms-2">
                <i class="bi bi-lock-fill"></i>
            </a>
        </div>
    </div>
</nav>

<main>
    <section class="hero-section" id="accueil">
        <div class="hero-orb hero-orb-one"></div>
        <div class="hero-orb hero-orb-two"></div>

        <div class="container position-relative">
            <div class="row align-items-center min-vh-100 py-5">
                <div class="col-lg-6 pt-5 pt-lg-0">
                    <span class="hero-badge">
                        <i class="bi bi-shield-check"></i>
                        Simple, rapide et sécurisé
                    </span>

                    <h1 class="hero-title mt-4">
                        Votre argent vous suit
                        <span>partout.</span>
                    </h1>

                    <p class="hero-description">
                        Envoyez, recevez, déposez et retirez de l'argent en quelques secondes,
                        directement depuis votre téléphone.
                    </p>

                    <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                        <a href="<?= site_url('connexion') ?>" class="btn btn-light btn-lg rounded-pill px-4 fw-bold">
                            Commencer maintenant
                            <i class="bi bi-arrow-right ms-2"></i>
                        </a>

                        <a href="#services" class="btn btn-outline-light btn-lg rounded-pill px-4">
                            Découvrir les services
                        </a>
                    </div>

                    <div class="hero-trust mt-5">
                        <div>
                            <strong>24h/24</strong>
                            <span>Service disponible</span>
                        </div>
                        <div>
                            <strong>100%</strong>
                            <span>Transactions sécurisées</span>
                        </div>
                        <div>
                            <strong>Instantané</strong>
                            <span>Envoi et réception</span>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 d-none d-lg-flex justify-content-center">
                    <div class="phone-scene">
                        <div class="floating-card card-transfer">
                            <span class="floating-icon"><i class="bi bi-arrow-up-right"></i></span>
                            <div>
                                <small>Transfert réussi</small>
                                <strong>- 25 000 Ar</strong>
                            </div>
                        </div>

                        <div class="phone-mockup">
                            <div class="phone-speaker"></div>
                            <div class="phone-screen">
                                <div class="app-header">
                                    <div>
                                        <small>Bonjour,</small>
                                        <strong>Brandy</strong>
                                    </div>
                                    <span class="avatar"><i class="bi bi-person"></i></span>
                                </div>

                                <div class="balance-card">
                                    <small>Solde disponible</small>
                                    <h3>250 000 <span>Ar</span></h3>
                                    <button type="button">
                                        <i class="bi bi-eye"></i> Afficher le solde
                                    </button>
                                </div>

                                <div class="quick-actions">
                                    <a href="<?= site_url('depot') ?>">
                                        <span><i class="bi bi-plus-lg"></i></span>
                                        Dépôt
                                    </a>
                                    <a href="<?= site_url('retrait') ?>">
                                        <span><i class="bi bi-dash-lg"></i></span>
                                        Retrait
                                    </a>
                                    <a href="<?= site_url('transfert') ?>">
                                        <span><i class="bi bi-send"></i></span>
                                        Transfert
                                    </a>
                                </div>

                                <div class="recent-operation">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <strong>Activités récentes</strong>
                                        <small>Voir tout</small>
                                    </div>

                                    <div class="operation-row">
                                        <span class="operation-icon"><i class="bi bi-arrow-down-left"></i></span>
                                        <div class="flex-grow-1">
                                            <strong>Dépôt</strong>
                                            <small>Aujourd'hui, 09:20</small>
                                        </div>
                                        <b class="positive">+ 50 000 Ar</b>
                                    </div>

                                    <div class="operation-row">
                                        <span class="operation-icon"><i class="bi bi-arrow-up-right"></i></span>
                                        <div class="flex-grow-1">
                                            <strong>Transfert</strong>
                                            <small>Hier, 16:45</small>
                                        </div>
                                        <b>- 15 000 Ar</b>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="floating-card card-secure">
                            <span class="floating-icon"><i class="bi bi-lock"></i></span>
                            <div>
                                <small>Paiement</small>
                                <strong>Sécurisé</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <a href="#services" class="scroll-indicator" aria-label="Voir les services">
            <i class="bi bi-chevron-down"></i>
        </a>
    </section>

    <section class="services-section section-padding" id="services">
        <div class="container">
            <div class="section-heading text-center mx-auto">
                <span class="section-kicker">NOS SERVICES</span>
                <h2>Tout ce dont vous avez besoin, dans votre téléphone</h2>
                <p>Des opérations simples et accessibles pour gérer votre argent au quotidien.</p>
            </div>

            <div class="row g-4 mt-4">
                <div class="col-md-6 col-lg-3">
                    <article class="service-card h-100">
                        <span class="service-icon"><i class="bi bi-cash-stack"></i></span>
                        <h3>Dépôt</h3>
                        <p>Alimentez votre compte rapidement et consultez immédiatement votre nouveau solde.</p>
                        <a href="<?= site_url('depot') ?>">Faire un dépôt <i class="bi bi-arrow-right"></i></a>
                    </article>
                </div>

                <div class="col-md-6 col-lg-3">
                    <article class="service-card h-100">
                        <span class="service-icon"><i class="bi bi-wallet"></i></span>
                        <h3>Retrait</h3>
                        <p>Retirez votre argent en toute sécurité avec des frais clairement affichés.</p>
                        <a href="<?= site_url('retrait') ?>">Faire un retrait <i class="bi bi-arrow-right"></i></a>
                    </article>
                </div>

                <div class="col-md-6 col-lg-3">
                    <article class="service-card h-100 featured">
                        <span class="service-icon"><i class="bi bi-send"></i></span>
                        <h3>Transfert</h3>
                        <p>Envoyez de l'argent instantanément vers un autre numéro de téléphone.</p>
                        <a href="<?= site_url('transfert') ?>">Envoyer de l'argent <i class="bi bi-arrow-right"></i></a>
                    </article>
                </div>

                <div class="col-md-6 col-lg-3">
                    <article class="service-card h-100">
                        <span class="service-icon"><i class="bi bi-clock-history"></i></span>
                        <h3>Historique</h3>
                        <p>Retrouvez vos dépôts, retraits et transferts dans un historique détaillé.</p>
                        <a href="<?= site_url('historique') ?>">Voir l'historique <i class="bi bi-arrow-right"></i></a>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <section class="advantages-section section-padding" id="avantages">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="visual-card">
                        <div class="visual-pattern"></div>
                        <div class="security-panel">
                            <span><i class="bi bi-shield-lock-fill"></i></span>
                            <h3>Votre sécurité, notre priorité</h3>
                            <p>Chaque opération est vérifiée et enregistrée.</p>
                            <div class="security-check">
                                <i class="bi bi-check-circle-fill"></i>
                                Numéro de téléphone contrôlé
                            </div>
                            <div class="security-check">
                                <i class="bi bi-check-circle-fill"></i>
                                Historique complet
                            </div>
                            <div class="security-check">
                                <i class="bi bi-check-circle-fill"></i>
                                Solde mis à jour instantanément
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <span class="section-kicker">POURQUOI NOUS CHOISIR ?</span>
                    <h2 class="display-5 fw-bold mt-2">Une expérience pensée pour être simple</h2>
                    <p class="lead text-secondary mt-3">
                        Pas de formulaire compliqué : votre numéro de téléphone suffit pour accéder à votre compte.
                    </p>

                    <div class="advantage-item">
                        <span><i class="bi bi-lightning-charge-fill"></i></span>
                        <div>
                            <h4>Rapide</h4>
                            <p>Vos transactions sont traitées en quelques secondes.</p>
                        </div>
                    </div>

                    <div class="advantage-item">
                        <span><i class="bi bi-phone-fill"></i></span>
                        <div>
                            <h4>Accessible</h4>
                            <p>Une interface responsive utilisable sur mobile, tablette et ordinateur.</p>
                        </div>
                    </div>

                    <div class="advantage-item">
                        <span><i class="bi bi-receipt-cutoff"></i></span>
                        <div>
                            <h4>Transparent</h4>
                            <p>Les frais applicables sont affichés avant la validation de l'opération.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="steps-section section-padding" id="fonctionnement">
        <div class="container">
            <div class="section-heading text-center mx-auto">
                <span class="section-kicker">COMMENT ÇA MARCHE ?</span>
                <h2>Commencez en trois étapes</h2>
            </div>

            <div class="row g-4 mt-4 position-relative">
                <div class="col-md-4">
                    <div class="step-card">
                        <span class="step-number">01</span>
                        <i class="bi bi-phone"></i>
                        <h3>Saisissez votre numéro</h3>
                        <p>Utilisez un numéro correspondant à l'un des préfixes autorisés.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="step-card">
                        <span class="step-number">02</span>
                        <i class="bi bi-person-check"></i>
                        <h3>Accédez à votre compte</h3>
                        <p>Votre compte est retrouvé ou créé automatiquement lors de la première connexion.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="step-card">
                        <span class="step-number">03</span>
                        <i class="bi bi-arrow-left-right"></i>
                        <h3>Effectuez vos opérations</h3>
                        <p>Déposez, retirez, transférez et suivez toutes vos transactions.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <div>
                    <span class="section-kicker text-white-50">PRÊT À COMMENCER ?</span>
                    <h2>Votre portefeuille mobile est à portée de main.</h2>
                    <p>Connectez-vous simplement avec votre numéro de téléphone.</p>
                </div>

                <a href="<?= site_url('connexion/client') ?>" class="btn btn-light btn-lg rounded-pill px-4 fw-bold">
                    Accéder à mon compte
                    <i class="bi bi-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <a class="navbar-brand d-inline-flex align-items-center gap-2" href="<?= site_url('/') ?>">
                    <span class="brand-mark"><i class="bi bi-wallet2"></i></span>
                    <span>Mada<span>Cash</span></span>
                </a>
                <p class="mt-3 mb-0">Votre solution mobile money simple, rapide et sécurisée.</p>
            </div>

            <div class="col-md-6 text-md-end">
                <a href="#services">Services</a>
                <a href="#avantages">Sécurité</a>
                <a href="<?= site_url('connexion') ?>">Connexion</a>
                <p class="copyright mt-3 mb-0">
                    &copy; <?= date('Y') ?> MadaCash. Projet pédagogique.
                </p>
            </div>
        </div>
    </div>
</footer>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/home.js') ?>"></script>
</body>
</html>
