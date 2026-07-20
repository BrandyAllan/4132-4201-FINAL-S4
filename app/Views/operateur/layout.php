<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord | MadaCash</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/css/bootstrap-icons.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/gestion.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/prefixe.css') ?>">
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
                class="menu-link <?= uri_string() === 'operateur/gestion' ? 'active' : '' ?>"
            >
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Tableau de bord</span>
            </a>

            <a
                href="<?= site_url('operateur/prefixes') ?>"
                class="menu-link <?= uri_string() === 'operateur/prefixes' ? 'active' : '' ?>"
            >
                <i class="bi bi-phone-fill"></i>
                <span>Préfixes opérateurs</span>
            </a>

            <a
                href="<?= site_url('operateur/types-operations') ?>"
                class="menu-link <?= uri_string() === 'operateur/types-operations' ? 'active' : '' ?>"
            >
                <i class="bi bi-arrow-left-right"></i>
                <span>Types d'opérations</span>
            </a>

            <a
                href="<?= site_url('operateur/frais') ?>"
                class="menu-link <?= uri_string() === 'operateur/frais' ? 'active' : '' ?>"
            >
                <i class="bi bi-cash-stack"></i>
                <span>Gestion des frais</span>
            </a>

            <a
                href="<?= site_url('operateur/comptes') ?>"
                class="menu-link <?= uri_string() === 'operateur/comptes' ? 'active' : '' ?>"
            >
                <i class="bi bi-people-fill"></i>
                <span>Comptes clients</span>
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
        <?= $this->renderSection('content') ?>
    </main>

</div>

</body>
</html>