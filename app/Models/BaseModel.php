<?php

/**
 * Modèle de base pour les interactions avec la base de données
 */

namespace App\Models;

class BaseModel
{
    protected $pdo;

    public function __construct()
    {
        global $pdo; // Utilise la connexion globale depuis bootstrap.php
        $this->pdo = $pdo;
    }
}
