<?php
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
    
    foreach ($tasks as &$task) {
        if ($task['id'] == $taskId) {
            $today = date('Y-m-d');
            $history = loadData('history.json');

            // Si on ne reçoit pas de profil, on force l'annulation (undo)
            // OU si la tâche est déjà marquée comme faite aujourd'hui
            if (!$profil || ($task['dernier_fait'] ?? '') === $today) {
                $task['dernier_fait'] = null;
                $task['fait_par'] = null;
                
                // Retirer de l'historique (la dernière entrée pour cette tâche aujourd'hui)
                foreach (array_reverse($history, true) as $key => $entry) {
                    if ($entry['task_id'] == $taskId && $entry['date'] === $today) {
                        unset($history[$key]);
                        break;
                    }
                }
                $history = array_values($history);
            } else {
                // Sinon on marque comme fait
                $task['dernier_fait'] = $today;
                $task['fait_par'] = $profil;
                
                // Ajouter à l'historique
                $history[] = [
                    'task_id' => $taskId,
                    'profil' => $profil,
                    'date' => $today
                ];
            }
            saveData('history.json', $history);
            break;
        }
    }

    
    saveData('tasks.json', $tasks);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}
