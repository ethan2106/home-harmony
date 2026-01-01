<?php
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO rooms (nom, emoji, couleur, zone) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nom'],
        $_POST['emoji'],
        $_POST['couleur'],
        $_POST['zone'] ?? 'maison'
    ]);
    
    header('Location: admin.php');
    exit;
}
