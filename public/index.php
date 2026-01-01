<?php

/**
 * Point d'entrée principal de l'application
 * Utilise le routeur pour diriger vers les contrôleurs appropriés
 */

// Autoloading Composer
require_once '../vendor/autoload.php';

// Gestion des erreurs avec Whoops (en développement)
if (class_exists('\Whoops\Run')) {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}

require_once '../includes/bootstrap.php';

// Initialisation du routeur
$router = new \App\Core\Router();

// Définition des routes
$router->addRoute('/', 'DashboardController@index');
$router->addRoute('/admin', 'AdminController@index');

// Actions d'ajout
$router->addRoute('/admin/add-profile', 'AdminController@addProfile');
$router->addRoute('/admin/add-room', 'AdminController@addRoom');
$router->addRoute('/admin/add-task', 'AdminController@addTask');

// Actions utilitaires
$router->addRoute('/admin/clear-list', 'AdminController@clearList');
$router->addRoute('/admin/reset-app', 'AdminController@resetApp');

// APIs AJAX
$router->addRoute('/api/update-task', 'ApiController@updateTask');
$router->addRoute('/api/pick-task', 'ApiController@pickTask');
$router->addRoute('/api/undo-task', 'ApiController@undoTask');

// Dispatch de la requête
$router->dispatch();
