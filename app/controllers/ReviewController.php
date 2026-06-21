<?php
/**
 * TravelMate - ReviewController
 */

class ReviewController
{
    private ReviewService $service;
    private TripMember    $tripMemberModel;
    private Trip          $tripModel;

    public function __construct()
    {
        $this->service         = new ReviewService();
        $this->tripMemberModel = new TripMember();
        $this->tripModel       = new Trip();
    }

    /**
     * GET /trips/{id}/reviews — Review submission page.
     */
    public function index(array $params = []): void
    {
        Security::requireLogin();

        $tripId = (int)($params['id'] ?? 0);
        $userId = Security::userId();

        $trip = $this->tripModel->findById($tripId);
        if (!$trip) {
            Security::setFlash('error', 'Trip not found.');
            header('Location: ' . BASE_URL . '/trips');
            exit;
        }

        if (!$this->tripMemberModel->isApprovedMember($tripId, $userId)) {
            Security::setFlash('error', 'Only trip members can submit reviews.');
            header('Location: ' . BASE_URL . '/trips/' . $tripId);
            exit;
        }

        $members   = $this->tripMemberModel->getApprovedMembers($tripId);
        $pageTitle = 'Leave Reviews — ' . $trip['title'];
        $flash     = Security::getFlash();

        require_once VIEWS_PATH . '/reviews/create.php';
    }

    /**
     * POST /reviews/submit — Submit a review.
     */
    public function submit(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $reviewerId = Security::userId();
        $tripId     = (int)($_POST['trip_id']          ?? 0);
        $validator  = new Validator($_POST);
        $validator->required('trip_id',           'Trip')
                  ->required('reviewed_user_id',  'Reviewed User')
                  ->required('rating',             'Rating')
                  ->min('rating', 1,               'Rating');

        if ($validator->fails()) {
            Security::setFlash('error', implode(' ', array_merge(...array_values($validator->errors()))));
            header('Location: ' . BASE_URL . '/trips/' . $tripId . '/reviews');
            exit;
        }

        try {
            $this->service->submitReview($reviewerId, [
                'trip_id'          => $tripId,
                'reviewed_user_id' => (int)$_POST['reviewed_user_id'],
                'rating'           => (int)$_POST['rating'],
                'review'           => Security::sanitize($_POST['review'] ?? ''),
            ]);
            Security::setFlash('success', 'Review submitted successfully!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/reviews');
        exit;
    }
}
