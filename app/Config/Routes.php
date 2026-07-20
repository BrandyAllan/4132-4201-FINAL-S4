<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\GestionOperateur;
/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('connexion/admin', 'GestionOperateur::showFormLogin');
$routes->get('logout/admin', 'GestionOperateur::logout');
$routes->post('operateur/login', 'GestionOperateur::doLogin');
$routes->get('operateur/gestion', 'GestionOperateur::index');

$routes->get('connexion/client', 'LoginClient::showLogin');
$routes->get('logout/client', 'LoginClient::logout');


$routes->group('client', function ($routes) {
    $routes->post('login', 'LoginClient::doLogin');
    $routes->get('dashboard', 'LoginClient::showDashboard');
});

