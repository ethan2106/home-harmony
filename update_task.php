<?php
/**
 * Engine de mise à jour des tâches
 * Gère la validation (avec profil) et l'annulation (sans profil).
 */
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['id'] ?? null;
    $profil = $_POST['profil'] ?? null;
    
    if (!$taskId) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'ID manquant']);
        exit;
    }
    
    $tasks = loadData('tasks.json');
    $today = date('Y-m-d');
    $updated = false;
    
    foreach ($tasks as &$task) {
        if ($task['id'] == $taskId) {
            $history = loadData('history.json') ?? [];

            // CAS 1 : ANNULATION (Pas de profil reçu ou déjà fait aujourd'hui)
            if (!$profil || ($task['dernier_fait'] ?? '') === $today) {
                $task['dernier_fait'] = null;
                $task['fait_par'] = null;
                
                // Nettoyage de l'historique pour éviter les doublons ou scores erronés
                $history = array_filter($history, function($entry) use ($taskId, $today) {
                    return !($entry['task_id'] == $taskId && $entry['date'] === $today);
                });
                $history = array_values($history); // Réindexer
            } 
            // CAS 2 : VALIDATION
            else {
                $task['dernier_fait'] = $today;
                $task['fait_par'] = $profil;
                
                $history[] = [
                    'task_id' => $taskId,
                    'profil' => $profil,
                    'date' => $today
                ];
            }
            
            saveData('history.json', $history);
            $updated = true;
            break;
        }
    }

    if ($updated) {
        saveData('tasks.json', $tasks);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        header('Content-Type: application/json', true, 404);
        echo json_encode(['success' => false, 'error' => 'Tâche non trouvée']);
    }
    exit;
}
