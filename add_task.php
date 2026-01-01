<?php
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO tasks (room_id, titre, frequence, date_creation) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['room_id'],
        $_POST['titre'],
        $_POST['frequence'],
        date('Y-m-d')
    ]);
    
    header('Location: admin.php');
    exit;
}
