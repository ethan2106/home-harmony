<?php
/**
 * Modèle Room - Gestion des pièces/zones
 */
namespace App\Models;

require_once __DIR__ . '/BaseModel.php';

class RoomModel extends BaseModel
{
    protected $table = 'rooms';

    public function getAllRooms()
    {
        $stmt = $this->pdo->query("SELECT * FROM rooms ORDER BY nom ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getRoomById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rooms WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getRoomsByZone($zone)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rooms WHERE zone = ? ORDER BY nom ASC");
        $stmt->execute([$zone]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createRoom($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO rooms (nom, emoji, couleur, zone) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['nom'], $data['emoji'], $data['couleur'], $data['zone']]);
    }

    public function updateRoom($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE rooms SET nom = ?, emoji = ?, couleur = ?, zone = ? WHERE id = ?");
        return $stmt->execute([$data['nom'], $data['emoji'], $data['couleur'], $data['zone'], $id]);
    }

    public function deleteRoom($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM rooms WHERE id = ?");
        return $stmt->execute([$id]);
    }

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
