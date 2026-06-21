<?php
/**
 * TravelMate - ResourceService
 */

class ResourceService
{
    private Resource           $resourceModel;
    private ResourceAssignment $assignmentModel;
    private TripMember         $tripMemberModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->resourceModel       = new Resource();
        $this->assignmentModel     = new ResourceAssignment();
        $this->tripMemberModel     = new TripMember();
        $this->notificationService = new NotificationService();
    }

    /**
     * Create a resource requirement for a trip.
     * Only approved members can create resources.
     */
    public function createResource(int $tripId, int $userId, array $data): int
    {
        if (!$this->tripMemberModel->isApprovedMember($tripId, $userId)) {
            throw new RuntimeException('Only approved trip members can add resources.');
        }

        return $this->resourceModel->create([
            'trip_id'           => $tripId,
            'resource_name'     => $data['resource_name'],
            'quantity_required' => max(1, (int)$data['quantity_required']),
        ]);
    }

    /**
     * Claim a resource (mark user as bringing it).
     */
    public function claimResource(int $resourceId, int $userId, int $quantity): void
    {
        $resource = $this->resourceModel->findById($resourceId);
        if (!$resource) {
            throw new RuntimeException('Resource not found.');
        }

        if (!$this->tripMemberModel->isApprovedMember($resource['trip_id'], $userId)) {
            throw new RuntimeException('Only approved trip members can claim resources.');
        }

        if ($quantity < 1) {
            throw new RuntimeException('Quantity must be at least 1.');
        }

        $this->assignmentModel->claim($resourceId, $userId, $quantity);

        // Recalculate quantity_assigned
        $this->resourceModel->updateQuantityAssigned($resourceId);
    }

    /**
     * Remove a user's resource claim.
     */
    public function unclaimResource(int $resourceId, int $userId): void
    {
        $resource = $this->resourceModel->findById($resourceId);
        if (!$resource) {
            throw new RuntimeException('Resource not found.');
        }

        $this->assignmentModel->unclaim($resourceId, $userId);
        $this->resourceModel->updateQuantityAssigned($resourceId);
    }

    /**
     * Get all resources with claims for a trip.
     */
    public function getResourcesWithClaims(int $tripId): array
    {
        $resources = $this->resourceModel->getByTrip($tripId);

        foreach ($resources as &$resource) {
            $resource['assignments'] = $this->assignmentModel->getByResource($resource['id']);

            // Calculate status
            $assigned = (int)$resource['quantity_assigned'];
            $required = (int)$resource['quantity_required'];

            if ($assigned >= $required) {
                $resource['fulfillment_status'] = 'fulfilled';
            } elseif ($assigned > 0) {
                $resource['fulfillment_status'] = 'partial';
            } else {
                $resource['fulfillment_status'] = 'missing';
            }
        }

        return $resources;
    }

    /**
     * Delete a resource (organizer only).
     */
    public function deleteResource(int $resourceId, int $userId): void
    {
        $resource = $this->resourceModel->findById($resourceId);
        if (!$resource) {
            throw new RuntimeException('Resource not found.');
        }

        if (!$this->tripMemberModel->isOrganizer($resource['trip_id'], $userId)) {
            throw new RuntimeException('Only the trip organizer can delete resources.');
        }

        $this->resourceModel->delete($resourceId);
    }
}
