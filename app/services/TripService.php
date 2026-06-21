<?php
/**
 * TravelMate - TripService
 *
 * Business logic for trip creation, joining, approval, and management.
 */

class TripService
{
    private Trip               $tripModel;
    private TripMember         $tripMemberModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->tripModel           = new Trip();
        $this->tripMemberModel     = new TripMember();
        $this->notificationService = new NotificationService();
    }

    /**
     * Create a new trip and automatically add the creator as organizer.
     *
     * @param array      $data
     * @param array|null $coverFile  $_FILES element for cover image
     * @return int       New trip ID
     */
    public function createTrip(array $data, ?array $coverFile = null): int
    {
        // Handle cover image
        if ($coverFile && FileUpload::hasFile($coverFile)) {
            try {
                $data['cover_image'] = FileUpload::uploadTripCover($coverFile);
            } catch (RuntimeException $e) {
                Logger::error('Trip cover upload failed', ['error' => $e->getMessage()]);
                // Non-fatal: proceed without cover image
            }
        }

        $tripId = $this->tripModel->create($data);

        // Automatically add creator as organizer with approved status
        $this->tripMemberModel->addMember($tripId, $data['creator_id'], 'organizer', 'approved');

        Logger::info('Trip created', ['trip_id' => $tripId, 'creator_id' => $data['creator_id']]);

        return $tripId;
    }

    /**
     * Submit a join request for a trip.
     *
     * @param int $tripId
     * @param int $userId
     * @throws RuntimeException on business rule violation
     */
    public function joinTrip(int $tripId, int $userId): void
    {
        $trip = $this->tripModel->findById($tripId);
        if (!$trip) {
            throw new RuntimeException('Trip not found.');
        }

        if ($trip['status'] === 'completed' || $trip['status'] === 'cancelled') {
            throw new RuntimeException('This trip is no longer accepting new members.');
        }

        $existingMembership = $this->tripMemberModel->getMemberStatus($tripId, $userId);
        if ($existingMembership) {
            $status = $existingMembership['join_status'];
            if ($status === 'approved') {
                throw new RuntimeException('You are already a member of this trip.');
            }
            if ($status === 'pending') {
                throw new RuntimeException('Your join request is already pending.');
            }
        }

        // Check capacity
        $approvedCount = $this->tripMemberModel->countApprovedMembers($tripId);
        if ($approvedCount >= $trip['max_participants']) {
            throw new RuntimeException('This trip has reached its maximum participant limit.');
        }

        $this->tripMemberModel->addMember($tripId, $userId, 'participant', 'pending');

        // Notify the trip creator
        $this->notificationService->send(
            $trip['creator_id'],
            'join_request',
            'New Join Request',
            "Someone requested to join your trip: {$trip['title']}",
            BASE_URL . '/trips/' . $tripId
        );

        Logger::info('Join request submitted', ['trip_id' => $tripId, 'user_id' => $userId]);
    }

    /**
     * Approve a pending member (organizer action).
     *
     * @param int $tripId
     * @param int $organizerId  Current user must be organizer
     * @param int $userId       User to approve
     * @throws RuntimeException on authorization failure
     */
    public function approveMember(int $tripId, int $organizerId, int $userId): void
    {
        $this->assertOrganizer($tripId, $organizerId);

        $trip = $this->tripModel->findById($tripId);

        $this->tripMemberModel->updateStatus($tripId, $userId, 'approved');

        // Notify the approved user
        $this->notificationService->send(
            $userId,
            'join_approved',
            'Join Request Approved!',
            "Your request to join \"{$trip['title']}\" has been approved.",
            BASE_URL . '/trips/' . $tripId
        );

        Logger::info('Member approved', ['trip_id' => $tripId, 'user_id' => $userId]);
    }

    /**
     * Reject a pending member (organizer action).
     *
     * @param int $tripId
     * @param int $organizerId
     * @param int $userId
     */
    public function rejectMember(int $tripId, int $organizerId, int $userId): void
    {
        $this->assertOrganizer($tripId, $organizerId);

        $this->tripMemberModel->updateStatus($tripId, $userId, 'rejected');

        Logger::info('Member rejected', ['trip_id' => $tripId, 'user_id' => $userId]);
    }

    /**
     * Mark a trip as completed (organizer action).
     *
     * @param int $tripId
     * @param int $organizerId
     */
    public function markComplete(int $tripId, int $organizerId): void
    {
        $this->assertOrganizer($tripId, $organizerId);
        $this->tripModel->updateStatus($tripId, 'completed');

        Logger::info('Trip completed', ['trip_id' => $tripId]);
    }

    /**
     * Remove a user from a trip (leave or kick).
     *
     * @param int $tripId
     * @param int $userId
     */
    public function leaveTrip(int $tripId, int $userId): void
    {
        $member = $this->tripMemberModel->getMemberStatus($tripId, $userId);
        if ($member && $member['role'] === 'organizer') {
            throw new RuntimeException('Organizers cannot leave their own trip. Transfer ownership first or delete the trip.');
        }
        $this->tripMemberModel->removeMember($tripId, $userId);
    }

    /**
     * Assert the current user is the organizer of the trip.
     *
     * @param int $tripId
     * @param int $userId
     * @throws RuntimeException
     */
    private function assertOrganizer(int $tripId, int $userId): void
    {
        $trip = $this->tripModel->findById($tripId);
        if (!$trip || $trip['creator_id'] !== $userId) {
            throw new RuntimeException('Only the trip organizer can perform this action.');
        }
    }
}
