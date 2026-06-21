<?php
/**
 * TravelMate - Media Model
 */

class Media
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByAlbum(int $albumId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, u.full_name, u.username, u.profile_photo
             FROM media m
             JOIN users u ON u.id = m.user_id
             WHERE m.album_id = :album_id
             ORDER BY m.uploaded_at DESC
             LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':album_id', $albumId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',    $limit,   PDO::PARAM_INT);
        $stmt->bindValue(':offset',   $offset,  PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare('SELECT * FROM media WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO media (album_id, user_id, file_name, file_path, file_type, caption)
             VALUES (:album_id, :user_id, :file_name, :file_path, :file_type, :caption)'
        );
        $stmt->execute([
            'album_id'  => $data['album_id'],
            'user_id'   => $data['user_id'],
            'file_name' => $data['file_name'],
            'file_path' => $data['file_path'],
            'file_type' => $data['file_type'],
            'caption'   => $data['caption'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM media WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
