<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['id'] ?? null;
    $profil = $_POST['profil'] ?? null;
    
    if (!$taskId) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'ID manquant']);
        exit;
    }
    
    $tasks = json_decode(file_get_contents('tasks.json'), true) ?? [];
    
    foreach ($tasks as &$task) {
        if ($task['id'] == $taskId) {
            $today = date('Y-m-d');
            $historyFile = 'history.json';
            $history = file_exists($historyFile) ? json_decode(file_get_contents($historyFile), true) : [];
            if (!is_array($history)) $history = [];

            // Si déjà fait aujourd'hui, on annule
            if (($task['dernier_fait'] ?? '') === $today) {
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
            file_put_contents('history.json', json_encode($history, JSON_PRETTY_PRINT));
            break;
        }
    }
    
    file_put_contents('tasks.json', json_encode($tasks, JSON_PRETTY_PRINT));
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}
