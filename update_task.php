<?php
/**
 * Engine de mise à jour des tâches - Version SQLite
 * Gère la validation (avec profil) et l'annulation via PDO.
 */
require_once 'includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = $_POST['id'] ?? null;
    $profilNom = $_POST['profil'] ?? null;
    $today = date('Y-m-d');

    if (!$taskId) {
        header('Content-Type: application/json', true, 400);
        echo json_encode(['success' => false, 'error' => 'ID manquant']);
        exit;
    }

    try {
        // 1. Récupérer l'ID de l'utilisateur à partir de son nom
        $stmtUser = $pdo->prepare("SELECT id FROM users WHERE nom = ?");
        $stmtUser->execute([$profilNom]);
        $user = $stmtUser->fetch();
        $userId = $user ? $user['id'] : null;

        // 2. Vérifier si la tâche est déjà faite aujourd'hui
        $stmtCheck = $pdo->prepare("SELECT dernier_fait FROM tasks WHERE id = ?");
        $stmtCheck->execute([$taskId]);
        $task = $stmtCheck->fetch();

        // CAS : ANNULATION (Si pas de profil ou déjà fait)
        if (!$profilNom || ($task && $task['dernier_fait'] === $today)) {
            // Update tâche
            $pdo->prepare("UPDATE tasks SET dernier_fait = NULL, fait_par = NULL WHERE id = ?")
                ->execute([$taskId]);
            
            // Nettoyage historique du jour pour cette tâche
            $pdo->prepare("DELETE FROM history WHERE task_id = ? AND date_action = ?")
                ->execute([$taskId, $today]);
            
            $message = "Tâche annulée";
        } 
        // CAS : VALIDATION
        else {
            // Update tâche
            $pdo->prepare("UPDATE tasks SET dernier_fait = ?, fait_par = ? WHERE id = ?")
                ->execute([$today, $profilNom, $taskId]);
            
            // Ajout à l'historique
            $pdo->prepare("INSERT INTO history (task_id, user_id, date_action, profil) VALUES (?, ?, ?, ?)")
                ->execute([$taskId, $userId, $today, $profilNom]);
            
            // Optionnel : Ajouter des points à l'utilisateur ici pour la gamification
            if ($userId) {
                $pdo->prepare("UPDATE users SET points = points + 10 WHERE id = ?")
                    ->execute([$userId]);
            }

            $message = "Tâche validée";
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => $message]);

    } catch (PDOException $e) {
        header('Content-Type: application/json', true, 500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
