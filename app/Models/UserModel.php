<?php

/**
 * Modèle User - Gestion des utilisateurs/profils
 */

namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    protected $table = 'users';

    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY nom ASC");

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getUserById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getUserByName($name)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE nom = ?");
        $stmt->execute([$name]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function createUser($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (nom, emoji, couleur) VALUES (?, ?, ?)");

        return $stmt->execute([$data['nom'], $data['emoji'], $data['couleur']]);
    }

    public function updateUser($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET nom = ?, emoji = ?, couleur = ? WHERE id = ?");

        return $stmt->execute([$data['nom'], $data['emoji'], $data['couleur'], $id]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");

        return $stmt->execute([$id]);
    }

    public function getUserStats($userId)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                COUNT(*) as total_tasks_completed,
                SUM(points) as total_points,
                MAX(date_action) as last_activity
            FROM history
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
