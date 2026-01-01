<?php
require_once 'includes/functions.php';

$tasks = loadData('tasks.json');
$today = date('Y-m-d');

foreach ($tasks as &$task) {
    // On enlève la date prévue pour aujourd'hui
    if (isset($task['date_prevue']) && $task['date_prevue'] === $today) {
        $task['date_prevue'] = null;
    }
}

saveData('tasks.json', $tasks);
header('Location: index.php');
