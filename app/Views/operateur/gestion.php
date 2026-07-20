<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord | MadaCash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/gestion.css') ?>">
</head>

<body>

<div class="dashboard-wrapper">

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-header">

            <a href="<?= site_url('operateur/gestion') ?>" class="sidebar-logo">

                <span class="logo-icon">
                    <i class="bi bi-wallet2"></i>
                </span>

                <span class="logo-text">
                    Mada<span>Cash</span>
                </span>

            </a>

            <button
                type="button"
                class="sidebar-close d-lg-none"
                id="sidebarClose"
            >
                <i class="bi bi-x-lg"></i>
            </button>

        </div>

        <div class="sidebar-user">

            <div class="user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="user-information">

                <strong>
                    <?= esc(session()->get('prenom') ?? 'Opérateur') ?>
                    <?= esc(session()->get('nom') ?? '') ?>
                </strong>

                <span>
                    <?= esc(session()->get('role') ?? 'OPERATEUR') ?>
                </span>

            </div>

        </div>

        <nav class="sidebar-menu">

            <p class="menu-title">MENU PRINCIPAL</p>

            <a
                href="<?= site_url('operateur/gestion') ?>"
                class="menu-link active"
            >
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Tableau de bord</span>
            </a>

            <a
                href="<?= site_url('operateur/prefixes') ?>"
                class="menu-link"
            >
                <i class="bi bi-phone-fill"></i>
                <span>Préfixes opérateurs</span>
            </a>

            <a
                href="<?= site_url('operateur/types-operations') ?>"
                class="menu-link"
            >
                <i class="bi bi-arrow-left-right"></i>
                <span>Types d'opérations</span>
            </a>

            <a
                href="<?= site_url('operateur/frais') ?>"
                class="menu-link"
            >
                <i class="bi bi-cash-stack"></i>
                <span>Gestion des frais</span>
            </a>

            <a
                href="<?= site_url('operateur/comptes') ?>"
                class="menu-link"
            >
                <i class="bi bi-people-fill"></i>
                <span>Comptes clients</span>
            </a>

            <a
                href="<?= site_url('operateur/operations') ?>"
                class="menu-link"
            >
                <i class="bi bi-clock-history"></i>
                <span>Historique</span>
            </a>

        </nav>

        <div class="sidebar-footer">

            <a
                href="<?= site_url('logout/admin') ?>"
                class="logout-link"
            >
                <i class="bi bi-box-arrow-left"></i>
                <span>Se déconnecter</span>
            </a>

        </div>

    </aside>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <main class="main-content">

        <header class="topbar">

            <div class="topbar-left">

                <button
                    type="button"
                    class="menu-toggle d-lg-none"
                    id="menuToggle"
                >
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <h1>Tableau de bord</h1>

                    <p>
                        Bienvenue,
                        <?= esc(session()->get('prenom') ?? 'Opérateur') ?>.
                        Voici la situation de votre plateforme.
                    </p>
                </div>

            </div>

            <div class="topbar-right">

                <button
                    type="button"
                    class="notification-button"
                >
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge">3</span>
                </button>

                <div class="topbar-profile">

                    <div class="profile-avatar">
                        <?= strtoupper(
                            substr(session()->get('prenom') ?? 'O', 0, 1)
                        ) ?>
                    </div>

                    <div class="profile-text d-none d-sm-flex">

                        <strong>
                            <?= esc(
                                session()->get('prenom')
                                ?? 'Opérateur'
                            ) ?>
                        </strong>

                        <span>
                            <?= esc(
                                session()->get('role')
                                ?? 'OPERATEUR'
                            ) ?>
                        </span>

                    </div>

                </div>

            </div>

        </header>

        <div class="dashboard-container">

            <section class="welcome-banner">

                <div class="welcome-content">

                    <span class="welcome-label">
                        <i class="bi bi-stars"></i>
                        ESPACE OPÉRATEUR
                    </span>

                    <h2>
                        Gérez votre plateforme Mobile Money
                    </h2>

                    <p>
                        Configurez les opérateurs, les types d'opérations,
                        les frais et consultez la situation des comptes clients.
                    </p>

                    <a
                        href="<?= site_url('operateur/operations') ?>"
                        class="btn welcome-button"
                    >
                        Voir les opérations
                        <i class="bi bi-arrow-right"></i>
                    </a>

                </div>

                <div class="welcome-illustration d-none d-md-flex">

                    <div class="illustration-circle">

                        <i class="bi bi-phone"></i>

                        <span class="illustration-icon icon-one">
                            <i class="bi bi-cash-coin"></i>
                        </span>

                        <span class="illustration-icon icon-two">
                            <i class="bi bi-arrow-left-right"></i>
                        </span>

                        <span class="illustration-icon icon-three">
                            <i class="bi bi-shield-check"></i>
                        </span>

                    </div>

                </div>

            </section>

            <section class="dashboard-section">

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">GESTION</span>
                        <h2>Configuration de la plateforme</h2>
                    </div>

                    <span class="current-date" id="currentDate"></span>

                </div>

                <div class="row g-4">

                    <div class="col-xl-3 col-md-6">

                        <a
                            href="<?= site_url('operateur/prefixes') ?>"
                            class="dashboard-card"
                        >

                            <div class="card-top">

                                <span class="card-icon orange">
                                    <i class="bi bi-phone-fill"></i>
                                </span>

                                <span class="card-arrow">
                                    <i class="bi bi-arrow-up-right"></i>
                                </span>

                            </div>

                            <div class="card-body-content">

                                <span class="card-counter">
                                    <?= esc($nombrePrefixes ?? 0) ?>
                                </span>

                                <h3>Configuration préfixe</h3>

                                <p>
                                    Gérez les préfixes téléphoniques
                                    associés aux différents opérateurs.
                                </p>

                            </div>

                            <div class="card-footer-content">

                                <span>Configurer</span>

                                <i class="bi bi-chevron-right"></i>

                            </div>

                        </a>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <a
                            href="<?= site_url('operateur/types-operations') ?>"
                            class="dashboard-card"
                        >

                            <div class="card-top">

                                <span class="card-icon blue">
                                    <i class="bi bi-arrow-left-right"></i>
                                </span>

                                <span class="card-arrow">
                                    <i class="bi bi-arrow-up-right"></i>
                                </span>

                            </div>

                            <div class="card-body-content">

                                <span class="card-counter">
                                    <?= esc($nombreTypesOperations ?? 0) ?>
                                </span>

                                <h3>Types d'opérations</h3>

                                <p>
                                    Créez et configurez les opérations
                                    disponibles sur la plateforme.
                                </p>

                            </div>

                            <div class="card-footer-content">

                                <span>Gérer les opérations</span>

                                <i class="bi bi-chevron-right"></i>

                            </div>

                        </a>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <a
                            href="<?= site_url('operateur/frais') ?>"
                            class="dashboard-card"
                        >

                            <div class="card-top">

                                <span class="card-icon green">
                                    <i class="bi bi-cash-stack"></i>
                                </span>

                                <span class="card-arrow">
                                    <i class="bi bi-arrow-up-right"></i>
                                </span>

                            </div>

                            <div class="card-body-content">

                                <span class="card-counter">
                                    <?= esc($nombreBaremes ?? 0) ?>
                                </span>

                                <h3>Gestion des frais</h3>

                                <p>
                                    Définissez les frais selon le type
                                    d'opération et les tranches de montant.
                                </p>

                            </div>

                            <div class="card-footer-content">

                                <span>Configurer les frais</span>

                                <i class="bi bi-chevron-right"></i>

                            </div>

                        </a>

                    </div>

                    <div class="col-xl-3 col-md-6">

                        <a
                            href="<?= site_url('operateur/comptes') ?>"
                            class="dashboard-card"
                        >

                            <div class="card-top">

                                <span class="card-icon purple">
                                    <i class="bi bi-people-fill"></i>
                                </span>

                                <span class="card-arrow">
                                    <i class="bi bi-arrow-up-right"></i>
                                </span>

                            </div>

                            <div class="card-body-content">

                                <span class="card-counter">
                                    <?= esc($nombreComptes ?? 0) ?>
                                </span>

                                <h3>Comptes clients</h3>

                                <p>
                                    Consultez les soldes, les statuts
                                    et la situation des comptes clients.
                                </p>

                            </div>

                            <div class="card-footer-content">

                                <span>Voir les comptes</span>

                                <i class="bi bi-chevron-right"></i>

                            </div>

                        </a>

                    </div>

                </div>

            </section>

            <section class="financial-summary">

                <div class="summary-card">

                    <span class="summary-icon">
                        <i class="bi bi-wallet2"></i>
                    </span>

                    <div>
                        <span>Gains totaux</span>

                        <strong>
                            <?= number_format(
                                $gainTotal ?? 0,
                                0,
                                ',',
                                ' '
                            ) ?>
                            Ar
                        </strong>
                    </div>

                </div>

                <div class="summary-card">

                    <span class="summary-icon withdrawal">
                        <i class="bi bi-box-arrow-down"></i>
                    </span>

                    <div>
                        <span>Gains sur retraits</span>

                        <strong>
                            <?= number_format(
                                $gainRetraits ?? 0,
                                0,
                                ',',
                                ' '
                            ) ?>
                            Ar
                        </strong>
                    </div>

                </div>

                <div class="summary-card">

                    <span class="summary-icon transfer">
                        <i class="bi bi-send-fill"></i>
                    </span>

                    <div>
                        <span>Gains sur transferts</span>

                        <strong>
                            <?= number_format(
                                $gainTransferts ?? 0,
                                0,
                                ',',
                                ' '
                            ) ?>
                            Ar
                        </strong>
                    </div>

                </div>

            </section>

            <section class="dashboard-section charts-section">

                <div class="section-heading">

                    <div>
                        <span class="section-kicker">STATISTIQUES</span>
                        <h2>Situation des gains par frais</h2>
                    </div>

                    <select
                        class="form-select chart-period"
                        id="chartPeriod"
                    >
                        <option value="7">7 derniers jours</option>
                        <option value="30" selected>30 derniers jours</option>
                        <option value="90">3 derniers mois</option>
                    </select>

                </div>

                <div class="row g-4">

                    <div class="col-xl-6">

                        <article class="chart-card">

                            <div class="chart-card-header">

                                <div>

                                    <span class="chart-label">
                                        RETRAITS
                                    </span>

                                    <h3>Gains sur les retraits</h3>

                                    <p>
                                        Évolution des frais encaissés
                                        sur les opérations de retrait.
                                    </p>

                                </div>

                                <span class="chart-icon withdrawal">
                                    <i class="bi bi-box-arrow-down"></i>
                                </span>

                            </div>

                            <div class="chart-total">

                                <strong>
                                    <?= number_format(
                                        $gainRetraits ?? 0,
                                        0,
                                        ',',
                                        ' '
                                    ) ?>
                                    Ar
                                </strong>

                                <span class="positive-evolution">
                                    <i class="bi bi-arrow-up"></i>
                                    Gains enregistrés
                                </span>

                            </div>

                            <div class="chart-container">

                                <canvas id="withdrawalChart"></canvas>

                            </div>

                        </article>

                    </div>

                    <div class="col-xl-6">

                        <article class="chart-card">

                            <div class="chart-card-header">

                                <div>

                                    <span class="chart-label">
                                        TRANSFERTS
                                    </span>

                                    <h3>Gains sur les transferts</h3>

                                    <p>
                                        Évolution des frais encaissés
                                        sur les opérations de transfert.
                                    </p>

                                </div>

                                <span class="chart-icon transfer">
                                    <i class="bi bi-send-fill"></i>
                                </span>

                            </div>

                            <div class="chart-total">

                                <strong>
                                    <?= number_format(
                                        $gainTransferts ?? 0,
                                        0,
                                        ',',
                                        ' '
                                    ) ?>
                                    Ar
                                </strong>

                                <span class="positive-evolution">
                                    <i class="bi bi-arrow-up"></i>
                                    Gains enregistrés
                                </span>

                            </div>

                            <div class="chart-container">

                                <canvas id="transferChart"></canvas>

                            </div>

                        </article>

                    </div>

                </div>

            </section>

        </div>

    </main>

</div>

<script>
    window.dashboardData = {
        labels: <?= json_encode(
            $labelsGraphique
            ?? ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']
        ) ?>,

        retraits: <?= json_encode(
            $gainsRetraitsGraphique
            ?? [12000, 18500, 14000, 23000, 19500, 27000, 24500]
        ) ?>,

        transferts: <?= json_encode(
            $gainsTransfertsGraphique
            ?? [8000, 12500, 10500, 16000, 14500, 21000, 18500]
        ) ?>
    };
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= base_url('assets/js/gestion.js') ?>"></script>

</body>
</html>