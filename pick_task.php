<?php
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['id'];
    $today = date('Y-m-d');
    
    $stmt = $pdo->prepare("UPDATE tasks SET date_prevue = ? WHERE id = ?");
    $stmt->execute([$today, $taskId]);
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}
