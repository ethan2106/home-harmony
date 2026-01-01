<?php

/**
 * Modèle Room - Gestion des pièces/zones
 */

namespace App\Models;

class RoomModel extends BaseModel
{
    /** @var string */
    protected $table = 'rooms';

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAllRooms(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM rooms ORDER BY nom ASC");

        return $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * @param int $id
     * @return array<string, mixed>|null
     */
    public function getRoomById(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * @param string $zone
     * @return array<int, array<string, mixed>>
     */
    public function getRoomsByZone(string $zone): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rooms WHERE zone = ? ORDER BY nom ASC");
        $stmt->execute([$zone]);

        return $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * @param array<string, mixed> $data
     * @return bool
     */
    public function createRoom($data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO rooms (nom, emoji, couleur, zone) VALUES (?, ?, ?, ?)");

        return $stmt->execute([$data['nom'], $data['emoji'], $data['couleur'], $data['zone']]);
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return bool
     */
    public function updateRoom($id, $data): bool
    {
        $stmt = $this->pdo->prepare("UPDATE rooms SET nom = ?, emoji = ?, couleur = ?, zone = ? WHERE id = ?");

        return $stmt->execute([$data['nom'], $data['emoji'], $data['couleur'], $data['zone'], $id]);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteRoom($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM rooms WHERE id = ?");

        return $stmt->execute([$id]);
    }

    /**
     * @param int $roomId
     * @return array<string, mixed>|false
     */
    public function getRoomStats($roomId)
    {
        $stmt = $this->pdo->prepare("
            SELECT
                COUNT(*) as total_tasks,
                COUNT(CASE WHEN status = 'done' THEN 1 END) as completed_tasks
            FROM tasks
            WHERE room_id = ?
        ");
        $stmt->execute([$roomId]);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
