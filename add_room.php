<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rooms = json_decode(file_get_contents('rooms.json'), true) ?? [];
    
    $newRoom = [
        'id' => time(),
        'nom' => $_POST['nom'],
        'emoji' => $_POST['emoji'],
        'couleur' => $_POST['couleur']
    ];
    
    $rooms[] = $newRoom;
    file_put_contents('rooms.json', json_encode($rooms, JSON_PRETTY_PRINT));
    
    header('Location: admin.php');
    exit;
}
