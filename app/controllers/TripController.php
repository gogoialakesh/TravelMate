<?php
/**
 * TravelMate - TripController
 */

class TripController
{
    private TripService $tripService;
    private Trip        $tripModel;
    private TripMember  $tripMemberModel;

    public function __construct()
    {
        $this->tripService     = new TripService();
        $this->tripModel       = new Trip();
        $this->tripMemberModel = new TripMember();
    }

    /**
     * GET /trips — Browse all public trips.
     */
    public function index(array $params = []): void
    {
        $pageTitle = 'Explore Trips — ' . APP_NAME;
        $flash     = Security::getFlash();

        $filters = [
            'destination' => Security::sanitize($_GET['destination'] ?? ''),
            'trip_type'   => Security::sanitize($_GET['trip_type']   ?? ''),
        ];

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * TRIPS_PER_PAGE;

        $trips = $this->tripModel->getAll($filters, TRIPS_PER_PAGE, $offset);
        $total = $this->tripModel->countAll($filters);
        $totalPages = (int) ceil($total / TRIPS_PER_PAGE);

        require_once VIEWS_PATH . '/trips/index.php';
    }

    /**
     * GET /trips/create — Show create trip form.
     */
    public function create(array $params = []): void
    {
        Security::requireLogin();
        $pageTitle  = 'Create Trip — ' . APP_NAME;
        $flash      = Security::getFlash();
        $formErrors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);
        require_once VIEWS_PATH . '/trips/create.php';
    }

    /**
     * POST /trips/create — Process create trip form.
     */
    public function store(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $userId    = Security::userId();
        $validator = new Validator($_POST);
        $validator->required('title',       'Trip Title')
                  ->maxLength('title', 255, 'Trip Title')
                  ->required('destination', 'Destination')
                  ->required('start_date',  'Start Date')
                  ->date('start_date',      'Start Date')
                  ->required('end_date',    'End Date')
                  ->date('end_date',        'End Date')
                  ->dateAfter('end_date', 'start_date', 'End Date', 'Start Date')
                  ->required('max_participants', 'Max Participants')
                  ->min('max_participants', 2, 'Max Participants')
                  ->in('visibility', ['public', 'private'], 'Visibility');

        if ($validator->fails()) {
            $_SESSION['form_errors'] = $validator->errors();
            Security::setFlash('error', 'Please fix the errors below.');
            header('Location: ' . BASE_URL . '/trips/create');
            exit;
        }

        try {
            $tripId = $this->tripService->createTrip(
                [
                    'creator_id'       => $userId,
                    'title'            => Security::sanitize($_POST['title']),
                    'destination'      => Security::sanitize($_POST['destination']),
                    'description'      => Security::sanitize($_POST['description']   ?? ''),
                    'trip_type'        => Security::sanitize($_POST['trip_type']     ?? ''),
                    'visibility'       => $_POST['visibility'] ?? 'public',
                    'start_date'       => $_POST['start_date'],
                    'end_date'         => $_POST['end_date'],
                    'max_participants' => (int)$_POST['max_participants'],
                ],
                $_FILES['cover_image'] ?? null
            );

            Security::setFlash('success', 'Trip created successfully!');
            header('Location: ' . BASE_URL . '/trips/' . $tripId);
            exit;
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '/trips/create');
            exit;
        }
    }

    /**
     * GET /trips/{id} — Trip detail page.
     */
    public function show(array $params = []): void
    {
        $tripId    = (int)($params['id'] ?? 0);
        $pageTitle = 'Trip Details — ' . APP_NAME;
        $flash     = Security::getFlash();

        $trip = $this->tripModel->findById($tripId);
        if (!$trip) {
            http_response_code(404);
            Security::setFlash('error', 'Trip not found.');
            header('Location: ' . BASE_URL . '/trips');
            exit;
        }

        $members        = $this->tripMemberModel->getApprovedMembers($tripId);
        $pendingMembers = [];
        $userMembership = null;
        $isOrganizer    = false;

        if (Security::isLoggedIn()) {
            $userId         = Security::userId();
            $userMembership = $this->tripMemberModel->getMemberStatus($tripId, $userId);
            $isOrganizer    = ($trip['creator_id'] === $userId);
            if ($isOrganizer) {
                $pendingMembers = $this->tripMemberModel->getPendingRequests($tripId);
            }
        }

        require_once VIEWS_PATH . '/trips/show.php';
    }

    /**
     * GET /trips/{id}/edit — Edit trip form.
     */
    public function edit(array $params = []): void
    {
        Security::requireLogin();

        $tripId  = (int)($params['id'] ?? 0);
        $userId  = Security::userId();
        $trip    = $this->tripModel->findById($tripId);

        if (!$trip || $trip['creator_id'] !== $userId) {
            Security::setFlash('error', 'Not authorized to edit this trip.');
            header('Location: ' . BASE_URL . '/trips');
            exit;
        }

        $pageTitle  = 'Edit Trip — ' . APP_NAME;
        $flash      = Security::getFlash();
        $formErrors = $_SESSION['form_errors'] ?? [];
        unset($_SESSION['form_errors']);

        require_once VIEWS_PATH . '/trips/edit.php';
    }

    /**
     * POST /trips/{id}/edit — Process edit trip form.
     */
    public function update(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId = (int)($params['id'] ?? 0);
        $userId = Security::userId();
        $trip   = $this->tripModel->findById($tripId);

        if (!$trip || $trip['creator_id'] !== $userId) {
            Security::setFlash('error', 'Not authorized.');
            header('Location: ' . BASE_URL . '/trips');
            exit;
        }

        $validator = new Validator($_POST);
        $validator->required('title', 'Trip Title')
                  ->required('destination', 'Destination')
                  ->required('start_date', 'Start Date')
                  ->date('start_date', 'Start Date')
                  ->required('end_date', 'End Date')
                  ->date('end_date', 'End Date')
                  ->dateAfter('end_date', 'start_date', 'End Date', 'Start Date')
                  ->required('max_participants', 'Max Participants')
                  ->min('max_participants', 2, 'Max Participants');

        if ($validator->fails()) {
            $_SESSION['form_errors'] = $validator->errors();
            header('Location: ' . BASE_URL . '/trips/' . $tripId . '/edit');
            exit;
        }

        $this->tripModel->update($tripId, [
            'title'            => Security::sanitize($_POST['title']),
            'destination'      => Security::sanitize($_POST['destination']),
            'description'      => Security::sanitize($_POST['description'] ?? ''),
            'trip_type'        => Security::sanitize($_POST['trip_type']   ?? ''),
            'visibility'       => $_POST['visibility'] ?? 'public',
            'start_date'       => $_POST['start_date'],
            'end_date'         => $_POST['end_date'],
            'max_participants' => (int)$_POST['max_participants'],
        ]);

        Security::setFlash('success', 'Trip updated successfully!');
        header('Location: ' . BASE_URL . '/trips/' . $tripId);
        exit;
    }

    /**
     * POST /trips/{id}/join — Submit join request.
     */
    public function join(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId = (int)($params['id'] ?? 0);
        $userId = Security::userId();

        try {
            $this->tripService->joinTrip($tripId, $userId);
            Security::setFlash('success', 'Join request submitted! Awaiting approval.');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId);
        exit;
    }

    /**
     * POST /trips/{id}/leave — Leave a trip.
     */
    public function leave(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId = (int)($params['id'] ?? 0);
        $userId = Security::userId();

        try {
            $this->tripService->leaveTrip($tripId, $userId);
            Security::setFlash('success', 'You have left the trip.');
            header('Location: ' . BASE_URL . '/trips/' . $tripId);
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
            header('Location: ' . BASE_URL . '/trips/' . $tripId);
        }
        exit;
    }

    /**
     * POST /trips/{id}/approve — Approve a pending member.
     */
    public function approveMember(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId     = (int)($params['id'] ?? 0);
        $userId     = Security::userId();
        $memberId   = (int)($_POST['member_id'] ?? 0);

        try {
            $this->tripService->approveMember($tripId, $userId, $memberId);
            Security::setFlash('success', 'Member approved!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId);
        exit;
    }

    /**
     * POST /trips/{id}/reject — Reject a pending member.
     */
    public function rejectMember(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId   = (int)($params['id'] ?? 0);
        $userId   = Security::userId();
        $memberId = (int)($_POST['member_id'] ?? 0);

        try {
            $this->tripService->rejectMember($tripId, $userId, $memberId);
            Security::setFlash('success', 'Member request rejected.');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId);
        exit;
    }

    /**
     * POST /trips/{id}/complete — Mark trip as completed.
     */
    public function markComplete(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId = (int)($params['id'] ?? 0);
        $userId = Security::userId();

        try {
            $this->tripService->markComplete($tripId, $userId);
            Security::setFlash('success', 'Trip marked as completed!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId);
        exit;
    }

    /**
     * POST /trips/{id}/delete — Delete a trip.
     */
    public function delete(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId = (int)($params['id'] ?? 0);
        $userId = Security::userId();
        $trip   = $this->tripModel->findById($tripId);

        if (!$trip || $trip['creator_id'] !== $userId) {
            Security::setFlash('error', 'Not authorized.');
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $this->tripModel->delete($tripId);
        Security::setFlash('success', 'Trip deleted.');
        header('Location: ' . BASE_URL . '/dashboard');
        exit;
    }
}
