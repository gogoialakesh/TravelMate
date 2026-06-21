<?php
/**
 * TravelMate - ExpenseController
 */

class ExpenseController
{
    private ExpenseService $service;
    private Expense        $expenseModel;
    private TripMember     $tripMemberModel;
    private Trip           $tripModel;

    public function __construct()
    {
        $this->service         = new ExpenseService();
        $this->expenseModel    = new Expense();
        $this->tripMemberModel = new TripMember();
        $this->tripModel       = new Trip();
    }

    /**
     * GET /trips/{id}/expenses
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

        $summary     = $this->service->getSummary($tripId);
        $isOrganizer = ($trip['creator_id'] === $userId);
        $pageTitle   = 'Expenses — ' . $trip['title'];
        $flash       = Security::getFlash();

        require_once VIEWS_PATH . '/expenses/index.php';
    }

    /**
     * POST /trips/{id}/expenses/create
     */
    public function store(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId    = (int)($params['id'] ?? 0);
        $userId    = Security::userId();
        $validator = new Validator($_POST);
        $validator->required('title', 'Title')
                  ->required('amount', 'Amount')
                  ->min('amount', 0.01, 'Amount');

        if ($validator->fails()) {
            Security::setFlash('error', implode(' ', array_merge(...array_values($validator->errors()))));
            header('Location: ' . BASE_URL . '/trips/' . $tripId . '/expenses');
            exit;
        }

        try {
            $this->service->addExpense($tripId, $userId, [
                'title'        => Security::sanitize($_POST['title']),
                'description'  => Security::sanitize($_POST['description'] ?? ''),
                'amount'       => (float)$_POST['amount'],
                'expense_date' => $_POST['expense_date'] ?: date('Y-m-d'),
            ]);
            Security::setFlash('success', 'Expense added!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/expenses');
        exit;
    }

    /**
     * POST /expenses/{id}/delete
     */
    public function delete(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $expenseId = (int)($params['id'] ?? 0);
        $userId    = Security::userId();
        $expense   = $this->expenseModel->findById($expenseId);
        $tripId    = $expense ? $expense['trip_id'] : 0;

        try {
            $this->service->deleteExpense($expenseId, $userId);
            Security::setFlash('success', 'Expense deleted.');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/expenses');
        exit;
    }
}
