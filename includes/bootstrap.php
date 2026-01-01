<?php
/**
 * Bootstrap Harmony - Version SQLite & MVC Light
 * Gère la connexion, les tables et le Reset Quotidien.
 */

require_once __DIR__ . '/functions.php';

// 1. Connexion SQLite (Le fichier sera créé dans /data/)
try {
    $dbPath = __DIR__ . '/../data/harmony.sq3';
    if (!is_dir(__DIR__ . '/../data/')) {
        mkdir(__DIR__ . '/../data/', 0777, true);
    }
    
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

// Définition de la base URL pour le routage
define('BASE_URL', ''); 

// 2. Création des tables (Schéma initial)
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT UNIQUE,
        couleur TEXT,
        emoji TEXT,
        points INTEGER DEFAULT 0
    );

    CREATE TABLE IF NOT EXISTS rooms (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nom TEXT,
        emoji TEXT,
        couleur TEXT,
        zone TEXT -- 'maison' ou 'jardin'
    );

    CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        room_id INTEGER,
        titre TEXT,
        frequence TEXT, -- 'Quotidien', 'Hebdomadaire', 'Mensuel', 'Saisonnier'
        dernier_fait DATE,
        fait_par TEXT,
        date_prevue DATE,
        date_creation DATE,
        FOREIGN KEY(room_id) REFERENCES rooms(id)
    );

    CREATE TABLE IF NOT EXISTS history (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        task_id INTEGER,
        user_id INTEGER,
        date_action DATE,
        profil TEXT, -- Pour compatibilité temporaire ou historique
        points INTEGER,
        FOREIGN KEY(task_id) REFERENCES tasks(id),
        FOREIGN KEY(user_id) REFERENCES users(id)
    );

    CREATE TABLE IF NOT EXISTS trophies (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER,
        type TEXT, -- 'bronze', 'argent', 'or'
        date_obtention DATE,
        FOREIGN KEY(user_id) REFERENCES users(id)
    );
");

// 3. Logique de Reset Quotidien (Lazy Cron)
$configPath = __DIR__ . '/../data/last_reset.txt';
$today = date('Y-m-d');
$lastReset = file_exists($configPath) ? file_get_contents($configPath) : '';

if ($lastReset !== $today) {
    // On réinitialise les tâches quotidiennes qui ont été faites avant aujourd'hui
    $stmt = $pdo->prepare("UPDATE tasks SET dernier_fait = NULL, fait_par = NULL WHERE frequence = 'Quotidien'");
    $stmt->execute();
    file_put_contents($configPath, $today);
}

// 4. Chargement des données globales
$profiles = $pdo->query("SELECT * FROM users")->fetchAll();
$rooms = $pdo->query("SELECT * FROM rooms")->fetchAll();
$history = $pdo->query("SELECT * FROM history ORDER BY date_action DESC")->fetchAll();

// --- 5. STATISTIQUES ---
$stats = [];
$currentMonth = date('Y-m');
foreach ($profiles as $p) { $stats[$p['nom']] = 0; }
foreach ($history as $entry) {
    if (isset($entry['date_action']) && strpos($entry['date_action'], $currentMonth) === 0) {
        if (isset($entry['profil']) && isset($stats[$entry['profil']])) {
            $stats[$entry['profil']]++;
        }
    }
}

// --- 6. DATE ---
$date_fr = getFrenchDate();

