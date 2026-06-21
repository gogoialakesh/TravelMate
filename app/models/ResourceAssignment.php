<?php
/**
 * TravelMate - ResourceAssignment Model
 */

class ResourceAssignment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByResource(int $resourceId): array
    {
        $stmt = $this->db->prepare(
            'SELECT ra.*, u.full_name, u.profile_photo
             FROM resource_assignments ra
             JOIN users u ON u.id = ra.user_id
             WHERE ra.resource_id = :resource_id
             ORDER BY ra.assigned_at ASC'
        );
        $stmt->execute(['resource_id' => $resourceId]);
        return $stmt->fetchAll();
    }

    public function findByUserAndResource(int $resourceId, int $userId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM resource_assignments WHERE resource_id = :resource_id AND user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['resource_id' => $resourceId, 'user_id' => $userId]);
        return $stmt->fetch();
    }

    public function claim(int $resourceId, int $userId, int $quantity): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO resource_assignments (resource_id, user_id, quantity)
             VALUES (:resource_id, :user_id, :quantity)
             ON DUPLICATE KEY UPDATE quantity = :update_quantity'
        );
        return $stmt->execute([
            'resource_id'     => $resourceId,
            'user_id'         => $userId,
            'quantity'        => $quantity,
            'update_quantity' => $quantity,
        ]);
    }

    public function unclaim(int $resourceId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM resource_assignments WHERE resource_id = :resource_id AND user_id = :user_id'
        );
        return $stmt->execute(['resource_id' => $resourceId, 'user_id' => $userId]);
    }
}
