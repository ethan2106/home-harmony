<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tasks = json_decode(file_get_contents('tasks.json'), true) ?? [];
    
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
    file_put_contents('tasks.json', json_encode($tasks, JSON_PRETTY_PRINT));
    
    header('Location: admin.php');
    exit;
}
