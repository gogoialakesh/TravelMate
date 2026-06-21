<?php
/**
 * TravelMate - Trip Model
 *
 * Database access layer for the `trips` table.
 */

class Trip
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // --------------------------------------------------------
    // Read Operations
    // --------------------------------------------------------

    /**
     * Find a trip by ID.
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.full_name AS creator_name, u.username AS creator_username,
                    u.profile_photo AS creator_photo,
                    (SELECT COUNT(*) FROM trip_members tm
                     WHERE tm.trip_id = t.id AND tm.join_status = "approved") AS member_count
             FROM trips t
             JOIN users u ON u.id = t.creator_id
             WHERE t.id = ?
             LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Get all public trips with optional filters and pagination.
     *
     * @param array $filters  Keys: destination, trip_type, status
     * @param int   $limit
     * @param int   $offset
     * @return array
     */
    public function getAll(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $conditions = ["t.visibility = 'public'"];
        $values     = [];

        if (!empty($filters['destination'])) {
            $conditions[] = 't.destination LIKE :destination';
            $values['destination'] = '%' . $filters['destination'] . '%';
        }

        if (!empty($filters['trip_type'])) {
            $conditions[] = 't.trip_type = :trip_type';
            $values['trip_type'] = $filters['trip_type'];
        }

        if (!empty($filters['status'])) {
            $conditions[] = 't.status = :status';
            $values['status'] = $filters['status'];
        }

        $where = implode(' AND ', $conditions);

        $stmt = $this->db->prepare(
            "SELECT t.*, u.full_name AS creator_name, u.profile_photo AS creator_photo,
                    (SELECT COUNT(*) FROM trip_members tm
                     WHERE tm.trip_id = t.id AND tm.join_status = 'approved') AS member_count
             FROM trips t
             JOIN users u ON u.id = t.creator_id
             WHERE {$where}
             ORDER BY t.created_at DESC
             LIMIT :limit OFFSET :offset"
        );

        foreach ($values as $key => $val) {
            $stmt->bindValue(':' . $key, $val);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count all public trips matching filters (for pagination).
     *
     * @param array $filters
     * @return int
     */
    public function countAll(array $filters = []): int
    {
        $conditions = ["t.visibility = 'public'"];
        $values     = [];

        if (!empty($filters['destination'])) {
            $conditions[] = 't.destination LIKE :destination';
            $values['destination'] = '%' . $filters['destination'] . '%';
        }
        if (!empty($filters['trip_type'])) {
            $conditions[] = 't.trip_type = :trip_type';
            $values['trip_type'] = $filters['trip_type'];
        }

        $where = implode(' AND ', $conditions);

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM trips t WHERE {$where}"
        );
        $stmt->execute($values);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get all trips created by or participated in by a user.
     *
     * @param int $userId
     * @return array
     */
    public function getByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT t.*, u.full_name AS creator_name,
                    tm.role, tm.join_status,
                    (SELECT COUNT(*) FROM trip_members tm2
                     WHERE tm2.trip_id = t.id AND tm2.join_status = 'approved') AS member_count
             FROM trips t
             JOIN users u ON u.id = t.creator_id
             JOIN trip_members tm ON tm.trip_id = t.id AND tm.user_id = :user_id
             WHERE tm.join_status = 'approved'
             ORDER BY t.start_date ASC"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get pending join requests for a trip (organizer view).
     *
     * @param int $tripId
     * @return array
     */
    public function getPendingMembers(int $tripId): array
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

    // --------------------------------------------------------
    // Write Operations
    // --------------------------------------------------------

    /**
     * Create a new trip record.
     *
     * @param array $data
     * @return int  New trip ID
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO trips
             (creator_id, title, destination, description, trip_type, visibility,
              start_date, end_date, max_participants, cover_image)
             VALUES
             (:creator_id, :title, :destination, :description, :trip_type, :visibility,
              :start_date, :end_date, :max_participants, :cover_image)'
        );
        $stmt->execute([
            'creator_id'       => $data['creator_id'],
            'title'            => $data['title'],
            'destination'      => $data['destination'],
            'description'      => $data['description']      ?? null,
            'trip_type'        => $data['trip_type']        ?? null,
            'visibility'       => $data['visibility']       ?? 'public',
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'max_participants' => $data['max_participants'],
            'cover_image'      => $data['cover_image']      ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a trip's editable fields.
     *
     * @param int   $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE trips
             SET title = :title, destination = :destination, description = :description,
                 trip_type = :trip_type, visibility = :visibility,
                 start_date = :start_date, end_date = :end_date,
                 max_participants = :max_participants
             WHERE id = :id'
        );
        return $stmt->execute([
            'title'            => $data['title'],
            'destination'      => $data['destination'],
            'description'      => $data['description']  ?? null,
            'trip_type'        => $data['trip_type']    ?? null,
            'visibility'       => $data['visibility'],
            'start_date'       => $data['start_date'],
            'end_date'         => $data['end_date'],
            'max_participants' => $data['max_participants'],
            'id'               => $id,
        ]);
    }

    /**
     * Update trip cover image.
     *
     * @param int    $id
     * @param string $fileName
     * @return bool
     */
    public function updateCoverImage(int $id, string $fileName): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE trips SET cover_image = :cover WHERE id = :id'
        );
        return $stmt->execute(['cover' => $fileName, 'id' => $id]);
    }

    /**
     * Update trip status.
     *
     * @param int    $id
     * @param string $status  upcoming|ongoing|completed|cancelled
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE trips SET status = :status WHERE id = :id'
        );
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    /**
     * Delete a trip by ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM trips WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
