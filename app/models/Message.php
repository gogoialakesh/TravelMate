<?php
/**
 * TravelMate - Message Model
 */

class Message
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByTrip(int $tripId, int $limit = 50, int $afterId = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, u.full_name, u.username, u.profile_photo
             FROM messages m
             JOIN users u ON u.id = m.user_id
             WHERE m.trip_id = :trip_id AND m.id > :after_id
             ORDER BY m.created_at ASC
             LIMIT :limit'
        );
        $stmt->bindValue(':trip_id',  $tripId, PDO::PARAM_INT);
        $stmt->bindValue(':after_id', $afterId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',    $limit,   PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getLatestMessages(int $tripId, int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, u.full_name, u.username, u.profile_photo
             FROM messages m
             JOIN users u ON u.id = m.user_id
             WHERE m.trip_id = :trip_id
             ORDER BY m.created_at ASC
             LIMIT :limit'
        );
        $stmt->bindValue(':trip_id', $tripId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',   $limit,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO messages (trip_id, user_id, message, attachment)
             VALUES (:trip_id, :user_id, :message, :attachment)'
        );
        $stmt->execute([
            'trip_id'    => $data['trip_id'],
            'user_id'    => $data['user_id'],
            'message'    => $data['message'],
            'attachment' => $data['attachment'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function getLastId(int $tripId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(MAX(id), 0) FROM messages WHERE trip_id = ?'
        );
        $stmt->execute([$tripId]);
        return (int) $stmt->fetchColumn();
    }
}
