<?php
/**
 * TravelMate - User Model
 *
 * Database access layer for the `users` table.
 * All queries use PDO prepared statements.
 * No business logic — only data operations.
 */

class User
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
     * Find a user by their email address.
     *
     * @param string $email
     * @return array|false
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, full_name, username, email, password, profile_photo,
                    bio, location, reliability_score, status, created_at
             FROM users
             WHERE email = ?
             LIMIT 1'
        );
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    /**
     * Find a user by their ID.
     *
     * @param int $id
     * @return array|false
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, full_name, username, email, profile_photo,
                    bio, location, reliability_score, status, created_at
             FROM users
             WHERE id = ?
             LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Find a user by username.
     *
     * @param string $username
     * @return array|false
     */
    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, full_name, username, email, profile_photo, bio, location, reliability_score
             FROM users WHERE username = ? LIMIT 1'
        );
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /**
     * Check whether an email already exists.
     *
     * @param string $email
     * @return bool
     */
    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch() !== false;
    }

    /**
     * Check whether a username already exists.
     *
     * @param string $username
     * @return bool
     */
    public function usernameExists(string $username): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        return $stmt->fetch() !== false;
    }

    // --------------------------------------------------------
    // Write Operations
    // --------------------------------------------------------

    /**
     * Create a new user record.
     *
     * @param array $data  Keys: full_name, username, email, password (hashed)
     * @return int         Newly inserted user ID
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (full_name, username, email, password)
             VALUES (:full_name, :username, :email, :password)'
        );
        $stmt->execute([
            'full_name' => $data['full_name'],
            'username'  => $data['username'],
            'email'     => $data['email'],
            'password'  => $data['password'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a user's profile information.
     *
     * @param int   $id
     * @param array $data  Keys: full_name, bio, location
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET full_name = :full_name, bio = :bio, location = :location
             WHERE id = :id'
        );
        return $stmt->execute([
            'full_name' => $data['full_name'],
            'bio'       => $data['bio']      ?? null,
            'location'  => $data['location'] ?? null,
            'id'        => $id,
        ]);
    }

    /**
     * Update a user's profile photo filename.
     *
     * @param int    $id
     * @param string $fileName
     * @return bool
     */
    public function updatePhoto(int $id, string $fileName): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET profile_photo = :photo WHERE id = :id'
        );
        return $stmt->execute(['photo' => $fileName, 'id' => $id]);
    }

    /**
     * Update a user's hashed password.
     *
     * @param int    $id
     * @param string $hashedPassword
     * @return bool
     */
    public function updatePassword(int $id, string $hashedPassword): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET password = :password WHERE id = :id'
        );
        return $stmt->execute(['password' => $hashedPassword, 'id' => $id]);
    }

    /**
     * Update reliability score.
     *
     * @param int   $id
     * @param float $score  0–100
     * @return bool
     */
    public function updateReliabilityScore(int $id, float $score): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET reliability_score = :score WHERE id = :id'
        );
        return $stmt->execute(['score' => round($score, 2), 'id' => $id]);
    }

    /**
     * Get the password hash for a user (for verification only).
     *
     * @param int $id
     * @return string|false
     */
    public function getPasswordHash(int $id): string|false
    {
        $stmt = $this->db->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $row['password'] : false;
    }
}
