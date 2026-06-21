<?php
/**
 * TravelMate - Responsibility Model
 */

class Responsibility
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByTrip(int $tripId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.full_name AS assigned_name, u.profile_photo AS assigned_photo,
                    c.full_name AS created_by_name
             FROM responsibilities r
             LEFT JOIN users u ON u.id = r.assigned_to
             LEFT JOIN users c ON c.id = r.created_by
             WHERE r.trip_id = :trip_id
             ORDER BY r.status ASC, r.due_date ASC, r.created_at ASC'
        );
        $stmt->execute(['trip_id' => $tripId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM responsibilities WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO responsibilities (trip_id, created_by, title, description, due_date)
             VALUES (:trip_id, :created_by, :title, :description, :due_date)'
        );
        $stmt->execute([
            'trip_id'     => $data['trip_id'],
            'created_by'  => $data['created_by'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'due_date'    => $data['due_date']    ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function assign(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE responsibilities SET assigned_to = :user_id, status = 'in_progress'
             WHERE id = :id"
        );
        return $stmt->execute(['user_id' => $userId, 'id' => $id]);
    }

    public function markComplete(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE responsibilities SET status = 'completed' WHERE id = ?"
        );
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM responsibilities WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function countCompletedByUser(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM responsibilities WHERE assigned_to = ? AND status = 'completed'"
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function countTotalByUser(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM responsibilities WHERE assigned_to = ?'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }
}
