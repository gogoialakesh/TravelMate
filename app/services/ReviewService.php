<?php
/**
 * TravelMate - ReviewService
 */

class ReviewService
{
    private Review     $reviewModel;
    private TripMember $tripMemberModel;
    private Trip       $tripModel;
    private UserService $userService;

    public function __construct()
    {
        $this->reviewModel     = new Review();
        $this->tripMemberModel = new TripMember();
        $this->tripModel       = new Trip();
        $this->userService     = new UserService();
    }

    /**
     * Submit a review for a trip participant.
     *
     * @param int   $reviewerId
     * @param array $data  trip_id, reviewed_user_id, rating, review
     */
    public function submitReview(int $reviewerId, array $data): void
    {
        $tripId          = (int)$data['trip_id'];
        $reviewedUserId  = (int)$data['reviewed_user_id'];
        $rating          = (int)$data['rating'];

        // Reviewer must be an approved member
        if (!$this->tripMemberModel->isApprovedMember($tripId, $reviewerId)) {
            throw new RuntimeException('Only approved trip members can submit reviews.');
        }

        // Cannot review yourself
        if ($reviewerId === $reviewedUserId) {
            throw new RuntimeException('You cannot review yourself.');
        }

        // Reviewed user must also be a member
        if (!$this->tripMemberModel->isApprovedMember($tripId, $reviewedUserId)) {
            throw new RuntimeException('The reviewed user is not a member of this trip.');
        }

        // Validate rating
        if ($rating < 1 || $rating > 5) {
            throw new RuntimeException('Rating must be between 1 and 5.');
        }

        // Only one review per reviewer per reviewed user per trip
        if ($this->reviewModel->hasReviewed($tripId, $reviewerId, $reviewedUserId)) {
            throw new RuntimeException('You have already reviewed this member for this trip.');
        }

        $this->reviewModel->create([
            'trip_id'          => $tripId,
            'reviewer_id'      => $reviewerId,
            'reviewed_user_id' => $reviewedUserId,
            'rating'           => $rating,
            'review'           => $data['review'] ?? '',
        ]);

        // Recalculate reliability score for the reviewed user
        $this->userService->recalculateReliabilityScore($reviewedUserId);
    }
}
