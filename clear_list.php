<?php
$tasks = json_decode(file_get_contents('tasks.json'), true);
$today = date('Y-m-d');

foreach ($tasks as &$task) {
    // On enlève la date prévue pour aujourd'hui
    if (isset($task['date_prevue']) && $task['date_prevue'] === $today) {
        $task['date_prevue'] = null;
    }
}

file_put_contents('tasks.json', json_encode($tasks, JSON_PRETTY_PRINT));
header('Location: index.php');
