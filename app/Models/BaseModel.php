<?php

/**
 * Modèle de base pour les interactions avec la base de données
 */

namespace App\Models;

class BaseModel
{
    /** @var \PDO */
    protected $pdo;

    public function __construct(?\PDO $pdo = null)
    {
        if ($pdo !== null) {
            $this->pdo = $pdo;
            return;
        }

        global $pdo; // Utilise la connexion globale depuis bootstrap.php
        $this->pdo = $pdo;
    }
}
