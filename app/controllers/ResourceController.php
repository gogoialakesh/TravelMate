<?php
/**
 * TravelMate - ResourceController
 */

class ResourceController
{
    private ResourceService $service;
    private Resource        $resourceModel;
    private TripMember      $tripMemberModel;
    private Trip            $tripModel;

    public function __construct()
    {
        $this->service         = new ResourceService();
        $this->resourceModel   = new Resource();
        $this->tripMemberModel = new TripMember();
        $this->tripModel       = new Trip();
    }

    /**
     * GET /trips/{id}/resources
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

        $resources   = $this->service->getResourcesWithClaims($tripId);
        $isOrganizer = ($trip['creator_id'] === $userId);
        $pageTitle   = 'Resources — ' . $trip['title'];
        $flash       = Security::getFlash();

        require_once VIEWS_PATH . '/resources/index.php';
    }

    /**
     * POST /trips/{id}/resources/create
     */
    public function store(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId    = (int)($params['id'] ?? 0);
        $userId    = Security::userId();
        $validator = new Validator($_POST);
        $validator->required('resource_name', 'Resource Name')
                  ->required('quantity_required', 'Quantity')
                  ->min('quantity_required', 1, 'Quantity');

        if ($validator->fails()) {
            Security::setFlash('error', implode(' ', array_merge(...array_values($validator->errors()))));
            header('Location: ' . BASE_URL . '/trips/' . $tripId . '/resources');
            exit;
        }

        try {
            $this->service->createResource($tripId, $userId, [
                'resource_name'     => Security::sanitize($_POST['resource_name']),
                'quantity_required' => (int)$_POST['quantity_required'],
            ]);
            Security::setFlash('success', 'Resource added!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/resources');
        exit;
    }

    /**
     * POST /resources/{id}/claim
     */
    public function claim(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $resourceId = (int)($params['id'] ?? 0);
        $userId     = Security::userId();
        $quantity   = max(1, (int)($_POST['quantity'] ?? 1));

        $resource = $this->resourceModel->findById($resourceId);
        $tripId   = $resource ? $resource['trip_id'] : 0;

        try {
            $this->service->claimResource($resourceId, $userId, $quantity);
            Security::setFlash('success', 'Resource claimed!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/resources');
        exit;
    }

    /**
     * POST /resources/{id}/unclaim
     */
    public function unclaim(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $resourceId = (int)($params['id'] ?? 0);
        $userId     = Security::userId();

        $resource = $this->resourceModel->findById($resourceId);
        $tripId   = $resource ? $resource['trip_id'] : 0;

        try {
            $this->service->unclaimResource($resourceId, $userId);
            Security::setFlash('success', 'Claim removed.');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/resources');
        exit;
    }

    /**
     * POST /resources/{id}/delete
     */
    public function delete(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $resourceId = (int)($params['id'] ?? 0);
        $userId     = Security::userId();

        $resource = $this->resourceModel->findById($resourceId);
        $tripId   = $resource ? $resource['trip_id'] : 0;

        try {
            $this->service->deleteResource($resourceId, $userId);
            Security::setFlash('success', 'Resource deleted.');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/resources');
        exit;
    }
}
