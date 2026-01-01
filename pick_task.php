<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['id'];
    $today = date('Y-m-d');
    
    $tasks = json_decode(file_get_contents('tasks.json'), true) ?? [];
    
    foreach ($tasks as &$task) {
        if ($task['id'] == $taskId) {
            $task['date_prevue'] = $today;
            break;
        }
    }
    
    file_put_contents('tasks.json', json_encode($tasks, JSON_PRETTY_PRINT));
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}
