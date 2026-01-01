<?php

/**
 * Modèle User - Gestion des utilisateurs/profils
 */

namespace App\Models;

class UserModel extends BaseModel
{
    /** @var string */
    protected $table = 'users';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY nom ASC");

        return $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getUserById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * @param string|null $name
     * @return array<string, mixed>|null
     */
    public function getUserByName(?string $name): ?array
    {
        if ($name === null) {
            return null;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE nom = ?");
        $stmt->execute([$name]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * @param array<string, mixed> $data
     * @return bool
     */
    public function createUser($data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (nom, emoji, couleur) VALUES (?, ?, ?)");

        return $stmt->execute([$data['nom'], $data['emoji'], $data['couleur']]);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function updateUser($id, $data): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET nom = ?, emoji = ?, couleur = ? WHERE id = ?");

        return $stmt->execute([$data['nom'], $data['emoji'], $data['couleur'], $id]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteUser($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");

        return $stmt->execute([$id]);
    }

    /**
     * @param int $userId
     * @return array<string, mixed>|false
     */
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
