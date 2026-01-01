<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['id'];
    $today = date('Y-m-d');
    
    $tasks = loadData('tasks.json');
    
    foreach ($tasks as &$task) {
        if ($task['id'] == $taskId) {
            $task['date_prevue'] = $today;
            break;
        }
    }
    
    saveData('tasks.json', $tasks);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}
