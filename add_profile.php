<?php
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $emoji = $_POST['emoji'] ?? '';
    $couleur = $_POST['couleur'] ?? 'indigo-500';

    if (!empty($nom)) {
        $stmt = $pdo->prepare("INSERT INTO users (nom, emoji, couleur) VALUES (?, ?, ?)");
        $stmt->execute([$nom, $emoji, $couleur]);
    }
}

header('Location: admin.php');
exit;
