<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rooms = loadData('rooms.json');
    
    $newRoom = [
        'id' => time(),
        'nom' => $_POST['nom'],
        'emoji' => $_POST['emoji'],
        'couleur' => $_POST['couleur']
    ];
    
    $rooms[] = $newRoom;
    saveData('rooms.json', $rooms);
    
    header('Location: admin.php');
    exit;
}
