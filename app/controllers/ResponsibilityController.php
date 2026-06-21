<?php
/**
 * TravelMate - ResponsibilityController
 */

class ResponsibilityController
{
    private ResponsibilityService $service;
    private Responsibility        $responsibilityModel;
    private TripMember            $tripMemberModel;
    private Trip                  $tripModel;

    public function __construct()
    {
        $this->service             = new ResponsibilityService();
        $this->responsibilityModel = new Responsibility();
        $this->tripMemberModel     = new TripMember();
        $this->tripModel           = new Trip();
    }

    /**
     * GET /trips/{id}/responsibilities
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
            Security::setFlash('error', 'Access restricted to approved trip members.');
            header('Location: ' . BASE_URL . '/trips/' . $tripId);
            exit;
        }

        $responsibilities = $this->responsibilityModel->getByTrip($tripId);
        $members          = $this->tripMemberModel->getApprovedMembers($tripId);
        $isOrganizer      = ($trip['creator_id'] === $userId);
        $pageTitle        = 'Responsibilities — ' . $trip['title'];
        $flash            = Security::getFlash();

        require_once VIEWS_PATH . '/responsibilities/index.php';
    }

    /**
     * POST /trips/{id}/responsibilities/create
     */
    public function store(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId    = (int)($params['id'] ?? 0);
        $userId    = Security::userId();
        $validator = new Validator($_POST);
        $validator->required('title', 'Title')
                  ->maxLength('title', 255, 'Title');

        if ($validator->fails()) {
            Security::setFlash('error', implode(' ', array_merge(...array_values($validator->errors()))));
            header('Location: ' . BASE_URL . '/trips/' . $tripId . '/responsibilities');
            exit;
        }

        try {
            $this->service->createResponsibility($tripId, $userId, [
                'title'       => Security::sanitize($_POST['title']),
                'description' => Security::sanitize($_POST['description'] ?? ''),
                'due_date'    => $_POST['due_date'] ?: null,
            ]);
            Security::setFlash('success', 'Responsibility added!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/responsibilities');
        exit;
    }

    /**
     * POST /responsibilities/{id}/assign
     */
    public function assign(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $responsibilityId = (int)($params['id'] ?? 0);
        $userId           = Security::userId();
        $assigneeId       = (int)($_POST['assignee_id'] ?? 0);

        $responsibility = $this->responsibilityModel->findById($responsibilityId);
        $tripId = $responsibility ? $responsibility['trip_id'] : 0;

        try {
            $this->service->assignResponsibility($responsibilityId, $assigneeId, $userId, $this->tripModel);
            Security::setFlash('success', 'Responsibility assigned!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/responsibilities');
        exit;
    }

    /**
     * POST /responsibilities/{id}/complete
     */
    public function complete(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $responsibilityId = (int)($params['id'] ?? 0);
        $userId           = Security::userId();

        $responsibility = $this->responsibilityModel->findById($responsibilityId);
        $tripId = $responsibility ? $responsibility['trip_id'] : 0;

        try {
            $this->service->completeResponsibility($responsibilityId, $userId);
            Security::setFlash('success', 'Responsibility marked complete!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/responsibilities');
        exit;
    }

    /**
     * POST /responsibilities/{id}/delete
     */
    public function delete(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $responsibilityId = (int)($params['id'] ?? 0);
        $userId           = Security::userId();

        $responsibility = $this->responsibilityModel->findById($responsibilityId);
        $tripId = $responsibility ? $responsibility['trip_id'] : 0;

        try {
            $this->service->deleteResponsibility($responsibilityId, $userId);
            Security::setFlash('success', 'Responsibility deleted.');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/responsibilities');
        exit;
    }
}
