<?php
/**
 * TravelMate - Album Model
 */

class Album
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByTrip(int $tripId): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*,
                    (SELECT COUNT(*) FROM media m WHERE m.album_id = a.id) AS media_count,
                    (SELECT m2.file_path FROM media m2 WHERE m2.album_id = a.id AND m2.file_type = "image"
                     ORDER BY m2.uploaded_at DESC LIMIT 1) AS cover_path
             FROM albums a
             WHERE a.trip_id = :trip_id
             ORDER BY a.created_at DESC'
        );
        $stmt->execute(['trip_id' => $tripId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, t.title AS trip_title, t.id AS trip_id
             FROM albums a
             JOIN trips t ON t.id = a.trip_id
             WHERE a.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO albums (trip_id, title) VALUES (:trip_id, :title)'
        );
        $stmt->execute(['trip_id' => $data['trip_id'], 'title' => $data['title']]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM albums WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
