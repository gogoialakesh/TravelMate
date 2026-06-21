<?php
/**
 * TravelMate - TripMember Model
 *
 * Database access layer for the `trip_members` table.
 */

class TripMember
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Add a user to a trip (creates join request or direct entry).
     *
     * @param int    $tripId
     * @param int    $userId
     * @param string $role        organizer|co_organizer|participant
     * @param string $joinStatus  pending|approved|rejected
     * @return bool
     */
    public function addMember(int $tripId, int $userId, string $role = 'participant', string $joinStatus = 'pending'): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO trip_members (trip_id, user_id, role, join_status)
             VALUES (:trip_id, :user_id, :role, :join_status)
             ON DUPLICATE KEY UPDATE role = :update_role, join_status = :update_join_status'
        );
        return $stmt->execute([
            'trip_id'            => $tripId,
            'user_id'            => $userId,
            'role'               => $role,
            'join_status'        => $joinStatus,
            'update_role'        => $role,
            'update_join_status' => $joinStatus,
        ]);
    }

    /**
     * Update a member's join status.
     *
     * @param int    $tripId
     * @param int    $userId
     * @param string $status  pending|approved|rejected
     * @return bool
     */
    public function updateStatus(int $tripId, int $userId, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE trip_members SET join_status = :status
             WHERE trip_id = :trip_id AND user_id = :user_id'
        );
        return $stmt->execute([
            'status'  => $status,
            'trip_id' => $tripId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Get a member's current status for a trip.
     *
     * @param int $tripId
     * @param int $userId
     * @return array|false
     */
    public function getMemberStatus(int $tripId, int $userId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM trip_members WHERE trip_id = :trip_id AND user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['trip_id' => $tripId, 'user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * Get all approved members of a trip.
     *
     * @param int $tripId
     * @return array
     */
    public function getApprovedMembers(int $tripId): array
    {
        $stmt = $this->db->prepare(
            "SELECT tm.*, u.full_name, u.username, u.profile_photo, u.reliability_score
             FROM trip_members tm
             JOIN users u ON u.id = tm.user_id
             WHERE tm.trip_id = :trip_id AND tm.join_status = 'approved'
             ORDER BY FIELD(tm.role, 'organizer', 'co_organizer', 'participant'), tm.joined_at ASC"
        );
        $stmt->execute(['trip_id' => $tripId]);
        return $stmt->fetchAll();
    }

    /**
     * Get all pending join requests for a trip.
     *
     * @param int $tripId
     * @return array
     */
    public function getPendingRequests(int $tripId): array
    {
        $stmt = $this->db->prepare(
            "SELECT tm.*, u.full_name, u.username, u.profile_photo, u.reliability_score
             FROM trip_members tm
             JOIN users u ON u.id = tm.user_id
             WHERE tm.trip_id = :trip_id AND tm.join_status = 'pending'
             ORDER BY tm.joined_at ASC"
        );
        $stmt->execute(['trip_id' => $tripId]);
        return $stmt->fetchAll();
    }

    /**
     * Count the number of approved members for a trip.
     *
     * @param int $tripId
     * @return int
     */
    public function countApprovedMembers(int $tripId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM trip_members WHERE trip_id = ? AND join_status = 'approved'"
        );
        $stmt->execute([$tripId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Remove a member from a trip.
     *
     * @param int $tripId
     * @param int $userId
     * @return bool
     */
    public function removeMember(int $tripId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            'DELETE FROM trip_members WHERE trip_id = :trip_id AND user_id = :user_id'
        );
        return $stmt->execute(['trip_id' => $tripId, 'user_id' => $userId]);
    }

    /**
     * Check if a user is an approved member of a trip.
     *
     * @param int $tripId
     * @param int $userId
     * @return bool
     */
    public function isApprovedMember(int $tripId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM trip_members
             WHERE trip_id = :trip_id AND user_id = :user_id AND join_status = 'approved'
             LIMIT 1"
        );
        $stmt->execute(['trip_id' => $tripId, 'user_id' => $userId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check if a user is the organizer of a trip.
     *
     * @param int $tripId
     * @param int $userId
     * @return bool
     */
    public function isOrganizer(int $tripId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM trip_members
             WHERE trip_id = :trip_id AND user_id = :user_id AND role = 'organizer'
             LIMIT 1"
        );
        $stmt->execute(['trip_id' => $tripId, 'user_id' => $userId]);
        return $stmt->fetch() !== false;
    }
}
