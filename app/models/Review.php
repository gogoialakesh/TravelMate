<?php
/**
 * TravelMate - Review Model
 */

class Review
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, u.full_name AS reviewer_name, u.profile_photo AS reviewer_photo,
                    t.title AS trip_title
             FROM reviews r
             JOIN users u ON u.id = r.reviewer_id
             JOIN trips t ON t.id = r.trip_id
             WHERE r.reviewed_user_id = :user_id
             ORDER BY r.created_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getAverageRating(int $userId): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(AVG(rating), 0) FROM reviews WHERE reviewed_user_id = ?'
        );
        $stmt->execute([$userId]);
        return (float) $stmt->fetchColumn();
    }

    public function hasReviewed(int $tripId, int $reviewerId, int $reviewedUserId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM reviews WHERE trip_id = :trip_id AND reviewer_id = :reviewer_id
             AND reviewed_user_id = :reviewed_user_id LIMIT 1'
        );
        $stmt->execute([
            'trip_id'          => $tripId,
            'reviewer_id'      => $reviewerId,
            'reviewed_user_id' => $reviewedUserId,
        ]);
        return $stmt->fetch() !== false;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO reviews (trip_id, reviewer_id, reviewed_user_id, rating, review)
             VALUES (:trip_id, :reviewer_id, :reviewed_user_id, :rating, :review)'
        );
        $stmt->execute([
            'trip_id'          => $data['trip_id'],
            'reviewer_id'      => $data['reviewer_id'],
            'reviewed_user_id' => $data['reviewed_user_id'],
            'rating'           => $data['rating'],
            'review'           => $data['review'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
