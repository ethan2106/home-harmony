<?php

/**
 * Routeur simple pour Home Harmony
 * Dirige les requêtes vers les contrôleurs appropriés.
 */

namespace App\Core;

class Router
{
    /** @var array<string, string> */
    private $routes = [];

    /**
     * @param string $path
     * @param string $controllerAction
     * @return void
     */
    public function addRoute($path, $controllerAction)
    {
        $this->routes[$path] = $controllerAction;
    }

    /**
     * @return void
     */
    public function dispatch()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Supprimer les paramètres de requête
        $path = parse_url($requestUri, PHP_URL_PATH);

        if ($path === false || $path === null) {
            $path = '/';
        }

        // Enlever le préfixe du projet si nécessaire (ex: /project-x/)
        $path = str_replace('/project-x/public', '', $path);
        $path = str_replace('/project-x', '', $path);

        if (isset($this->routes[$path])) {
            $controllerAction = $this->routes[$path];
            list($controllerName, $action) = explode('@', $controllerAction);

            // Utiliser le namespace complet pour le contrôleur
            $fullControllerName = "App\\Controllers\\{$controllerName}";

            if (class_exists($fullControllerName)) {
                $controller = new $fullControllerName();
                if (method_exists($controller, $action)) {
                    $controller->$action();
                } else {
                    http_response_code(404);
                    echo "Action non trouvée";
                }
            } else {
                http_response_code(404);
                echo "Contrôleur non trouvé";
            }
        } else {
            // Par défaut, rediriger vers le dashboard
            header('Location: /');
            exit;
        }
    }
}
