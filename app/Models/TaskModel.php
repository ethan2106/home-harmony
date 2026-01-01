<?php
namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class TaskModel extends BaseModel {

    /**
     * Récupère toutes les tâches avec leurs pièces associées
     * @return array
     */
    public function getAllTasksWithRooms(): array {
        $stmt = $this->pdo->query("
            SELECT t.*, r.nom as room_nom, r.emoji as room_emoji, r.couleur as room_couleur, r.zone
            FROM tasks t
            LEFT JOIN rooms r ON t.room_id = r.id
            ORDER BY t.id
        ");
        return $stmt->fetchAll();
    }

    /**
     * Récupère les tâches à faire pour une date donnée
     * @param string $today
     * @return array
     */
    public function getTodoTasks(string $today): array {
        $tasks = $this->getAllTasksWithRooms();
        return array_filter($tasks, function($t) use ($today) {
            // Si faite aujourd'hui, elle n'est plus à faire
            if (($t['dernier_fait'] ?? '') === $today) {
                return false;
            }
            return $this->isTaskDue($t, $today);
        });
    }

    /**
     * Récupère les tâches terminées pour une date donnée
     * @param string $today
     * @return array
     */
    public function getDoneTasks(string $today): array {
        $tasks = $this->getAllTasksWithRooms();
        return array_filter($tasks, function($t) use ($today) {
            return ($t['dernier_fait'] ?? '') === $today;
        });
    }

    private function isTaskDue($task, $today) {
        // Si jamais faite, elle est due
        if (empty($task['dernier_fait'])) return true;

        $lastDone = new \DateTime($task['dernier_fait']);
        $now = new \DateTime($today);
        
        // Si la date de dernière réalisation est dans le futur, on ne l'affiche pas
        if ($now < $lastDone) return false;

        $diff = $now->diff($lastDone)->days;

        switch ($task['frequence']) {
            case 'Quotidien': return $diff >= 1;
            case 'Hebdomadaire': return $diff >= 7;
            case 'Mensuel': return $diff >= 30;
            case 'Saisonnier': return $diff >= 90;
            default: return true;
        }
    }

    /**
     * Supprime une tâche
     * @param int $id
     * @return bool
     */
    public function deleteTask(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Crée une nouvelle tâche
     * @param array $data
     * @return bool
     */
    public function createTask(array $data): bool {
        $stmt = $this->pdo->prepare("INSERT INTO tasks (room_id, titre, frequence, date_creation) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['room_id'],
            $data['titre'],
            $data['frequence'],
            date('Y-m-d')
        ]);
    }

    /**
     * Valide une tâche et attribue des points
     * @param int $taskId
     * @param int|null $userId
     * @return array ['success' => bool, 'points' => int]
     */
    public function validateTask(int $taskId, ?int $userId): array {
        $today = date('Y-m-d');

        // Vérifier si la tâche est déjà faite aujourd'hui
        $stmtCheck = $this->pdo->prepare("SELECT dernier_fait FROM tasks WHERE id = ?");
        $stmtCheck->execute([$taskId]);
        $task = $stmtCheck->fetch();

        if ($task && $task['dernier_fait'] === $today) {
            return ['success' => false, 'error' => 'Tâche déjà faite aujourd\'hui'];
        }

        // Récupérer les infos de l'utilisateur
        $userName = 'Inconnu';
        if ($userId) {
            $stmtUser = $this->pdo->prepare("SELECT nom FROM users WHERE id = ?");
            $stmtUser->execute([$userId]);
            $user = $stmtUser->fetch();
            if ($user) {
                $userName = $user['nom'];
            }
        }

        // Calculer les points
        $points = $this->calculatePoints($taskId);

        // Insérer dans l'historique
        $stmtHistory = $this->pdo->prepare("INSERT INTO history (task_id, user_id, profil, date_action, points) VALUES (?, ?, ?, ?, ?)");
        $stmtHistory->execute([$taskId, $userId, $userName, $today, $points]);

        // Mettre à jour la tâche
        $stmtUpdate = $this->pdo->prepare("UPDATE tasks SET dernier_fait = ?, fait_par = ?, date_prevue = NULL WHERE id = ?");
        $stmtUpdate->execute([$today, $userName, $taskId]);

        // Mettre à jour les points de l'utilisateur
        if ($userId) {
            $stmtPoints = $this->pdo->prepare("UPDATE users SET points = IFNULL(points, 0) + ? WHERE id = ?");
            $stmtPoints->execute([$points, $userId]);
        }

        return ['success' => true, 'points' => $points];
    }

    /**
     * Annule la validation d'une tâche
     * @param int $taskId
     * @return array ['success' => bool]
     */
    public function undoTask(int $taskId): array {
        $today = date('Y-m-d');

        // Récupérer les infos de l'historique avant suppression pour les points
        $stmtHist = $this->pdo->prepare("SELECT user_id, points FROM history WHERE task_id = ? AND date_action = ?");
        $stmtHist->execute([$taskId, $today]);
        $history = $stmtHist->fetch();

        if ($history) {
            // Soustraire les points
            if ($history['user_id']) {
                $stmtPoints = $this->pdo->prepare("UPDATE users SET points = points - ? WHERE id = ?");
                $stmtPoints->execute([$history['points'], $history['user_id']]);
            }
        }

        // Supprimer de l'historique
        $stmtDelete = $this->pdo->prepare("DELETE FROM history WHERE task_id = ? AND date_action = ?");
        $stmtDelete->execute([$taskId, $today]);

        // Remettre à jour la tâche
        $stmtUpdate = $this->pdo->prepare("UPDATE tasks SET dernier_fait = NULL, fait_par = NULL WHERE id = ?");
        $stmtUpdate->execute([$taskId]);

        return ['success' => true];
    }

    /**
     * Sélectionne une tâche pour aujourd'hui
     * Note: La colonne date_prevue n'existe pas encore dans le schéma
     * @param int $taskId
     * @return bool
     */
    public function pickTask(int $taskId): bool {
        // TODO: Implémenter la logique de sélection de tâche
        // Pour l'instant, on retourne true sans modification
        return true;
    }

    private function calculatePoints($taskId) {
        // Logique de calcul des points (simplifiée)
        return 10; // Points fixes pour l'instant
    }

    /**
     * Remet à zéro toutes les tâches (enlève les dates prévues)
     * @return bool
     */
    public function resetAllTasks(): bool {
        $stmt = $this->pdo->prepare("UPDATE tasks SET date_prevue = NULL");
        return $stmt->execute();
    }

    /**
     * Reset complet de toutes les données
     * @return bool
     */
    public function resetAllData(): bool {
        // Reset des tables
        $this->pdo->exec("DELETE FROM tasks");
        $this->pdo->exec("DELETE FROM rooms");
        $this->pdo->exec("DELETE FROM users");
        $this->pdo->exec("DELETE FROM history");
        $this->pdo->exec("DELETE FROM trophies");

        // Reset auto-increment
        $this->pdo->exec("DELETE FROM sqlite_sequence WHERE name IN ('tasks', 'rooms', 'users', 'history', 'trophies')");

        // Supprimer le fichier de reset
        $configPath = __DIR__ . '/../../data/last_reset.txt';
        if (file_exists($configPath)) {
            unlink($configPath);
        }

        return true;
    }
}
