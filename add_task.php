<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tasks = loadData('tasks.json');
    
    $newTask = [
        'id' => time(),
        'titre' => $_POST['titre'],
        'room_id' => $_POST['room_id'],
        'frequence' => $_POST['frequence'],
        'dernier_fait' => null,
        'fait_par' => null,
        'date_creation' => date('Y-m-d')
    ];
    
    $tasks[] = $newTask;
    saveData('tasks.json', $tasks);
    
    header('Location: admin.php');
    exit;
}
