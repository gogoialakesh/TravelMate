<?php
/**
 * TravelMate - UserService
 *
 * Business logic for user profiles and reliability scoring.
 */

class UserService
{
    private User           $userModel;
    private Review         $reviewModel;
    private Responsibility $responsibilityModel;
    private TripMember     $tripMemberModel;

    public function __construct()
    {
        $this->userModel           = new User();
        $this->reviewModel         = new Review();
        $this->responsibilityModel = new Responsibility();
        $this->tripMemberModel     = new TripMember();
    }

    /**
     * Get a user's public profile with statistics.
     *
     * @param int $userId
     * @return array
     * @throws RuntimeException if user not found
     */
    public function getProfile(int $userId): array
    {
        $user = $this->userModel->findById($userId);
        if (!$user) {
            throw new RuntimeException('User not found.');
        }

        $user['reviews']       = $this->reviewModel->getByUser($userId);
        $user['average_rating'] = $this->reviewModel->getAverageRating($userId);

        return $user;
    }

    /**
     * Update a user's profile information.
     *
     * @param int   $userId
     * @param array $data   Keys: full_name, bio, location
     * @param array|null $photoFile  $_FILES element for profile photo
     * @return void
     */
    public function updateProfile(int $userId, array $data, ?array $photoFile = null): void
    {
        $this->userModel->update($userId, [
            'full_name' => $data['full_name'],
            'bio'       => $data['bio']      ?? '',
            'location'  => $data['location'] ?? '',
        ]);

        // Handle profile photo upload
        if ($photoFile && FileUpload::hasFile($photoFile)) {
            try {
                $fileName = FileUpload::uploadProfilePhoto($photoFile);
                $this->userModel->updatePhoto($userId, $fileName);
                // Update session photo
                $_SESSION['user_photo'] = $fileName;
            } catch (RuntimeException $e) {
                Logger::error('Profile photo upload failed', ['error' => $e->getMessage()]);
                throw $e;
            }
        }

        // Update session name
        $_SESSION['user_name'] = $data['full_name'];
    }

    /**
     * Change a user's password after verifying the current one.
     *
     * @param int    $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @throws RuntimeException on verification failure
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): void
    {
        $storedHash = $this->userModel->getPasswordHash($userId);

        if (!$storedHash || !password_verify($currentPassword, $storedHash)) {
            throw new RuntimeException('Current password is incorrect.');
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->userModel->updatePassword($userId, $newHash);

        Logger::info('Password changed', ['user_id' => $userId]);
    }

    /**
     * Recalculate and persist a user's reliability score.
     *
     * Formula (from DATABASE.md):
     *   40% trip completion score
     *   30% task completion score
     *   20% average rating (normalized to 100)
     *   10% report history (placeholder: 100 if no reports)
     *
     * @param int $userId
     */
    public function recalculateReliabilityScore(int $userId): void
    {
        // Task completion score
        $totalTasks     = $this->responsibilityModel->countTotalByUser($userId);
        $completedTasks = $this->responsibilityModel->countCompletedByUser($userId);
        $taskScore = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 100;

        // Average rating score (ratings are 1–5, normalize to 0–100)
        $avgRating   = $this->reviewModel->getAverageRating($userId);
        $ratingScore = $avgRating > 0 ? (($avgRating - 1) / 4) * 100 : 100;

        // Trip completion — approximate via approved membership count (simplified)
        $tripScore = 100; // Full score for now; refine when trip completion data is tracked

        // Report history — no reports system yet; default 100
        $reportScore = 100;

        $score = ($tripScore * 0.4) + ($taskScore * 0.3) + ($ratingScore * 0.2) + ($reportScore * 0.1);
        $score = max(0, min(100, round($score, 2)));

        $this->userModel->updateReliabilityScore($userId, $score);
    }
}
