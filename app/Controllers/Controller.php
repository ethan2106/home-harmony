<?php

/**
 * Contrôleur de base
 */

namespace App\Controllers;

class Controller
{
    /**
     * @param string $view
     * @param array<string, mixed> $data
     * @return void
     */
    protected function view($view, $data = [])
    {
        extract($data);
        require_once __DIR__ . '/../Views/' . $view . '.php';
    }

    /**
     * @param string $url
     * @return void
     */
    protected function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}
