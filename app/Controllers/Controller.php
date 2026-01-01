<?php
/**
 * Contrôleur de base
 */
namespace App\Controllers;

class Controller {
    protected function view($view, $data = []) {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    protected function redirect($url) {
        header('Location: ' . $url);
        exit;
    }
}
