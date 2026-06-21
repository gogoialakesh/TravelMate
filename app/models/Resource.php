<?php
/**
 * TravelMate - Resource Model
 */

class Resource
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByTrip(int $tripId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*,
                    (SELECT SUM(ra.quantity) FROM resource_assignments ra WHERE ra.resource_id = r.id) AS total_claimed
             FROM resources r
             WHERE r.trip_id = :trip_id
             ORDER BY r.created_at ASC'
        );
        $stmt->execute(['trip_id' => $tripId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM resources WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO resources (trip_id, resource_name, quantity_required)
             VALUES (:trip_id, :resource_name, :quantity_required)'
        );
        $stmt->execute([
            'trip_id'           => $data['trip_id'],
            'resource_name'     => $data['resource_name'],
            'quantity_required' => $data['quantity_required'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateQuantityAssigned(int $id): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE resources r
             SET r.quantity_assigned = (
                 SELECT COALESCE(SUM(ra.quantity), 0)
                 FROM resource_assignments ra
                 WHERE ra.resource_id = r.id
             )
             WHERE r.id = ?'
        );
        return $stmt->execute([$id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM resources WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
