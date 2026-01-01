<?php
/**
 * Engine de mise à jour des tâches
 * Gère la validation (avec profil) et l'annulation (sans profil).
 */
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['id'] ?? null;
    $profil = $_POST['profil'] ?? null;
    
    if (!$taskId) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'ID manquant']);
        exit;
    }
    
    $today = date('Y-m-d');
    
    // On récupère la tâche actuelle
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    $task = $stmt->fetch();

    if (!$task) {
        header('Content-Type: application/json', true, 404);
        echo json_encode(['success' => false, 'error' => 'Tâche non trouvée']);
        exit;
    }

    // CAS 1 : ANNULATION (Pas de profil reçu ou déjà fait aujourd'hui)
    if (!$profil || ($task['dernier_fait'] ?? '') === $today) {
        $stmt = $pdo->prepare("UPDATE tasks SET dernier_fait = NULL, fait_par = NULL WHERE id = ?");
        $stmt->execute([$taskId]);
        
        $stmt = $pdo->prepare("DELETE FROM history WHERE task_id = ? AND date_action = ?");
        $stmt->execute([$taskId, $today]);
    } 
    // CAS 2 : VALIDATION
    else {
        $stmt = $pdo->prepare("UPDATE tasks SET dernier_fait = ?, fait_par = ? WHERE id = ?");
        $stmt->execute([$today, $profil, $taskId]);
        
        // On récupère l'user_id
        $stmtUser = $pdo->prepare("SELECT id FROM users WHERE nom = ?");
        $stmtUser->execute([$profil]);
        $user = $stmtUser->fetch();

        $stmt = $pdo->prepare("INSERT INTO history (task_id, user_id, date_action, profil) VALUES (?, ?, ?, ?)");
        $stmt->execute([$taskId, $user['id'] ?? null, $today, $profil]);

        // Optionnel : Ajouter des points à l'utilisateur
        if ($user) {
            $pdo->prepare("UPDATE users SET points = points + 10 WHERE id = ?")->execute([$user['id']]);
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}
