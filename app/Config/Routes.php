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
        $routes->get('modifier/(:num)', 'GestionOperateur::showModifierPrefixe/$1');
        $routes->post('modifier', 'GestionOperateur::modifierPrefixe');
        $routes->get('desactiver/(:num)', 'GestionOperateur::desactiverPrefixe/$1');
        $routes->get('activer/(:num)', 'GestionOperateur::activerPrefixe/$1');
    });
    $routes->group('types-operations', function ($routes) {
        $routes->get('/', 'GestionOperateur::showTypeOperation');
        $routes->post('ajouter', 'GestionOperateur::ajouterTypeOperation');
        $routes->get('modifier/(:num)', 'GestionOperateur::showModifierTypeOperation/$1');
        $routes->post('modifier', 'GestionOperateur::modifierTypeOperation');
    });
    $routes->group('frais', function ($routes) {
        $routes->get('/', 'GestionOperateur::showFrais');
        $routes->group('retrait', static function ($routes) {
            $routes->get(
                '/',
                'GestionOperateur::showFraisRetrait'
            );

            $routes->post(
                'ajouter',
                'GestionOperateur::ajouterFraisRetrait'
            );

            $routes->post(
                'activer/(:num)',
                'GestionOperateur::activerFraisRetrait/$1'
            );

            $routes->post(
                'desactiver/(:num)',
                'GestionOperateur::desactiverFraisRetrait/$1'
            );

            $routes->post(
                'supprimer/(:num)',
                'GestionOperateur::supprimerFraisRetrait/$1'
            );
        });
        $routes->group('transfert', static function ($routes) {

            $routes->get(
                '/',
                'GestionOperateur::showFraisTransfert'
            );

            $routes->post(
                'ajouter',
                'GestionOperateur::ajouterFraisTransfert'
            );

            $routes->get(
                'modifier/(:num)',
                'GestionOperateur::modifierFraisTransfert/$1'
            );

            $routes->post(
                'update',
                'GestionOperateur::updateFraisTransfert'
            );

            $routes->post(
                'activer/(:num)',
                'GestionOperateur::activerFraisTransfert/$1'
            );

            $routes->post(
                'desactiver/(:num)',
                'GestionOperateur::desactiverFraisTransfert/$1'
            );

            $routes->post(
                'supprimer/(:num)',
                'GestionOperateur::supprimerFraisTransfert/$1'
            );

        });
        
    });
    $routes->get('comptes', 'GestionOperateur::situationCompte');
    $routes->get('comptes', 'GestionOperateur::situationCompte', ['filter' => 'auth']);
});

$routes->get('connexion/client', 'LoginClient::showLogin');
$routes->get('logout/client', 'LoginClient::logout');


$routes->group('client', function ($routes) {
    $routes->post('login', 'LoginClient::doLogin');
    $routes->get('dashboard', 'LoginClient::showDashboard');
    $routes->get('dashboard', 'LoginClient::showDashboard');
    $routes->get('solde', 'GestionClient::showSolde');
    $routes->get('depot', 'GestionClient::showDepot');
    $routes->post('depot', 'GestionClient::doDepot');
    $routes->get('retrait', 'GestionClient::showRetrait');
    $routes->post('retrait', 'GestionClient::doRetrait');
    $routes->get('transfert', 'GestionClient::showTransfert');
    $routes->post('transfert', 'GestionClient::doTransfert');
    $routes->get('historique', 'GestionClient::showhistorique');
});

