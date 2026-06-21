<?php
/**
 * TravelMate - Notification Model
 */

class Notification
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByUser(int $userId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notifications WHERE user_id = :user_id
             ORDER BY created_at DESC LIMIT :limit'
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',   $limit,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countUnread(int $userId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0'
        );
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notifications (user_id, type, title, message, link)
             VALUES (:user_id, :type, :title, :message, :link)'
        );
        $stmt->execute([
            'user_id' => $data['user_id'],
            'type'    => $data['type']    ?? 'general',
            'title'   => $data['title'],
            'message' => $data['message'],
            'link'    => $data['link']    ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function markRead(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :user_id'
        );
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    public function markAllRead(int $userId): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE notifications SET is_read = 1 WHERE user_id = :user_id'
        );
        return $stmt->execute(['user_id' => $userId]);
    }
}
