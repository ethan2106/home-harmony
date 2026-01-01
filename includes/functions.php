<?php
/**
 * Fonctions utilitaires pour l'application Harmony
 */

/**
 * Charge les données d'un fichier JSON
 */
function loadData($filename, $default = []) {
    $path = __DIR__ . '/../' . $filename;
    if (!file_exists($path)) {
        return $default;
    }
    return json_decode(file_get_contents($path), true) ?? $default;
}

/**
 * Sauvegarde les données dans un fichier JSON
 */
function saveData($filename, $data) {
    $path = __DIR__ . '/../' . $filename;
    return file_put_contents($path, json_encode(array_values($data), JSON_PRETTY_PRINT));
}

/**
 * Vérifie si une tâche doit être faite
 */
function isTaskDue($task) {
    if (empty($task['dernier_fait'])) return true;
    $lastDone = new DateTime($task['dernier_fait']);
    $now = new DateTime(date('Y-m-d'));
    $diff = $now->diff($lastDone)->days;
    switch ($task['frequence']) {
        case 'Quotidien': return $diff >= 1;
        case 'Hebdomadaire': return $diff >= 7;
        case 'Mensuel': return $diff >= 30;
        case 'Saisonnier': return $diff >= 90;
        default: return true;
    }
}

/**
 * Retourne la date du jour en français
 */
function getFrenchDate() {
    $jours = [
        'Monday' => 'Lundi', 'Tuesday' => 'Mardi', 'Wednesday' => 'Mercredi', 
        'Thursday' => 'Jeudi', 'Friday' => 'Vendredi', 'Saturday' => 'Samedi', 
        'Sunday' => 'Dimanche'
    ];
    $mois = [
        'January' => 'Janvier', 'February' => 'Février', 'March' => 'Mars', 
        'April' => 'Avril', 'May' => 'Mai', 'June' => 'Juin', 
        'July' => 'Juillet', 'August' => 'Août', 'September' => 'Septembre', 
        'October' => 'Octobre', 'November' => 'Novembre', 'December' => 'Décembre'
    ];
    
    return $jours[date('l')] . ' ' . date('d') . ' ' . $mois[date('F')];
}
