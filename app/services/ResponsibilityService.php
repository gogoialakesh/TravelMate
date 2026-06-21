<?php
/**
 * TravelMate - ResponsibilityService
 */

class ResponsibilityService
{
    private Responsibility      $responsibilityModel;
    private TripMember          $tripMemberModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->responsibilityModel = new Responsibility();
        $this->tripMemberModel     = new TripMember();
        $this->notificationService = new NotificationService();
    }

    /**
     * Create a responsibility for a trip.
     * Only approved members can create responsibilities.
     */
    public function createResponsibility(int $tripId, int $userId, array $data): int
    {
        if (!$this->tripMemberModel->isApprovedMember($tripId, $userId)) {
            throw new RuntimeException('Only approved trip members can create responsibilities.');
        }

        return $this->responsibilityModel->create([
            'trip_id'     => $tripId,
            'created_by'  => $userId,
            'title'       => $data['title'],
            'description' => $data['description'] ?? '',
            'due_date'    => $data['due_date']    ?? null,
        ]);
    }

    /**
     * Assign a responsibility to a member.
     */
    public function assignResponsibility(int $responsibilityId, int $assigneeId, int $actorId, Trip $tripModel): void
    {
        $responsibility = $this->responsibilityModel->findById($responsibilityId);
        if (!$responsibility) {
            throw new RuntimeException('Responsibility not found.');
        }

        if (!$this->tripMemberModel->isApprovedMember($responsibility['trip_id'], $actorId)) {
            throw new RuntimeException('You do not have permission to assign this responsibility.');
        }

        $this->responsibilityModel->assign($responsibilityId, $assigneeId);

        // Recalculate reliability score for the new assignee
        $userService = new UserService();
        $userService->recalculateReliabilityScore($assigneeId);

        // Recalculate for the old assignee if it was previously assigned to someone else
        if ($responsibility['assigned_to'] && $responsibility['assigned_to'] !== $assigneeId) {
            $userService->recalculateReliabilityScore($responsibility['assigned_to']);
        }

        // Notify assigned user
        $trip = $tripModel->findById($responsibility['trip_id']);
        $this->notificationService->send(
            $assigneeId,
            'responsibility_assigned',
            'New Responsibility Assigned',
            "You have been assigned: \"{$responsibility['title']}\" in trip \"{$trip['title']}\"",
            BASE_URL . '/trips/' . $responsibility['trip_id'] . '/responsibilities'
        );
    }

    /**
     * Mark a responsibility as complete.
     */
    public function completeResponsibility(int $responsibilityId, int $userId): void
    {
        $responsibility = $this->responsibilityModel->findById($responsibilityId);
        if (!$responsibility) {
            throw new RuntimeException('Responsibility not found.');
        }

        // Only the assigned user or organizer can complete
        if ($responsibility['assigned_to'] !== $userId &&
            !$this->tripMemberModel->isOrganizer($responsibility['trip_id'], $userId)) {
            throw new RuntimeException('Only the assigned user or organizer can mark this complete.');
        }

        $this->responsibilityModel->markComplete($responsibilityId);

        // Recalculate reliability score for the assigned user
        if ($responsibility['assigned_to']) {
            $userService = new UserService();
            $userService->recalculateReliabilityScore($responsibility['assigned_to']);
        }
    }

    /**
     * Delete a responsibility.
     */
    public function deleteResponsibility(int $responsibilityId, int $userId): void
    {
        $responsibility = $this->responsibilityModel->findById($responsibilityId);
        if (!$responsibility) {
            throw new RuntimeException('Responsibility not found.');
        }

        if (!$this->tripMemberModel->isOrganizer($responsibility['trip_id'], $userId)) {
            throw new RuntimeException('Only the trip organizer can delete responsibilities.');
        }

        $this->responsibilityModel->delete($responsibilityId);

        // Recalculate reliability score for the assigned user
        if ($responsibility['assigned_to']) {
            $userService = new UserService();
            $userService->recalculateReliabilityScore($responsibility['assigned_to']);
        }
    }
}
