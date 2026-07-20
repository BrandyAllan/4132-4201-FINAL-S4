<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\GestionOperateur;
/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('connexion/admin', 'GestionOperateur::showFormLogin');
$routes->get('logout/admin', 'GestionOperateur::logout');
$routes->group('operateur', function ($routes) {
    $routes->post('login', 'GestionOperateur::doLogin');
    $routes->get('gestion', 'GestionOperateur::index');
    $routes->group('prefixes', function ($routes) {
        $routes->get('/', 'GestionOperateur::showPrefixe');
        $routes->post('ajouter', 'GestionOperateur::ajouterPrefixe');
        $routes->post('modifier', 'GestionOperateur::modifierPrefixe');
        $routes->get('desactiver/(:num)', 'GestionOperateur::desactiverPrefixe/$1');
        $routes->get('activer/(:num)', 'GestionOperateur::activerPrefixe/$1');
    });
    $routes->get('comptes', 'GestionOperateur::showFormCompte');
    $routes->get('types-operations', 'GestionOperateur::showFormTypeOperation');
    $routes->get('frais', 'GestionOperateur::showFormBaremeFrais');
    $routes->get('operations', 'GestionOperateur::showHistorique');
});

$routes->get('connexion/client', 'LoginClient::showLogin');
$routes->get('logout/client', 'LoginClient::logout');


$routes->group('client', function ($routes) {
    $routes->post('login', 'LoginClient::doLogin');
    $routes->get('dashboard', 'LoginClient::showDashboard');
});

