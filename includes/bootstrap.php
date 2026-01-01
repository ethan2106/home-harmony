<?php
/**
 * Initialisation de l'application
 */

require_once __DIR__ . '/functions.php';

// --- 1. LOGIQUE DE RESET JOURNALIER (LAZY CRON) ---
$lastResetFile = __DIR__ . '/../last_reset.txt';
$today = date('Y-m-d');
$lastReset = file_exists($lastResetFile) ? file_get_contents($lastResetFile) : '';

if ($lastReset !== $today) {
    $tasksData = loadData('tasks.json');
    if (is_array($tasksData)) {
        foreach ($tasksData as &$task) {
            if (isset($task['date_prevue'])) { $task['date_prevue'] = null; }
        }
        saveData('tasks.json', $tasksData);
    }
    file_put_contents($lastResetFile, $today);
}

// --- 2. CHARGEMENT DES DONNÉES ---
$profiles = loadData('profiles.json');
$tasks = loadData('tasks.json');
$rooms = loadData('rooms.json');
$history = loadData('history.json');

// --- 3. STATISTIQUES ---
$stats = [];
$currentMonth = date('Y-m');
foreach ($profiles as $p) { $stats[$p['nom']] = 0; }
if (is_array($history)) {
    foreach ($history as $entry) {
        if (isset($entry['date']) && strpos($entry['date'], $currentMonth) === 0) {
            if (isset($stats[$entry['profil']])) { $stats[$entry['profil']]++; }
        }
    }
}

// --- 4. DATE ---
$date_fr = getFrenchDate();

// --- 5. FILTRES POUR LE DASHBOARD ---
$todoTasks = array_filter($tasks, function($t) use ($today) {
    return ($t['dernier_fait'] ?? '') !== $today && isTaskDue($t);
});

$doneTasks = array_filter($tasks, function($t) use ($today) {
    return ($t['dernier_fait'] ?? '') === $today;
});

