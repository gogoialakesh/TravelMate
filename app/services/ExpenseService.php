<?php
/**
 * TravelMate - ExpenseService
 */

class ExpenseService
{
    private Expense    $expenseModel;
    private TripMember $tripMemberModel;
    private Trip       $tripModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->expenseModel        = new Expense();
        $this->tripMemberModel     = new TripMember();
        $this->tripModel           = new Trip();
        $this->notificationService = new NotificationService();
    }

    /**
     * Add an expense to a trip.
     */
    public function addExpense(int $tripId, int $userId, array $data): int
    {
        if (!$this->tripMemberModel->isApprovedMember($tripId, $userId)) {
            throw new RuntimeException('Only approved trip members can add expenses.');
        }

        if ((float)$data['amount'] <= 0) {
            throw new RuntimeException('Expense amount must be greater than 0.');
        }

        $expenseId = $this->expenseModel->create([
            'trip_id'      => $tripId,
            'added_by'     => $userId,
            'title'        => $data['title'],
            'description'  => $data['description'] ?? '',
            'amount'       => $data['amount'],
            'expense_date' => $data['expense_date'] ?? date('Y-m-d'),
        ]);

        // Notify all trip members
        $members = $this->tripMemberModel->getApprovedMembers($tripId);
        $trip    = $this->tripModel->findById($tripId);
        foreach ($members as $member) {
            if ($member['user_id'] !== $userId) {
                $this->notificationService->send(
                    $member['user_id'],
                    'expense_added',
                    'New Expense Added',
                    "A new expense \"{$data['title']}\" was added to trip \"{$trip['title']}\"",
                    BASE_URL . '/trips/' . $tripId . '/expenses'
                );
            }
        }

        return $expenseId;
    }

    /**
     * Get expense summary for a trip.
     *
     * @param int $tripId
     * @return array  total, participants, individual_share, expenses
     */
    public function getSummary(int $tripId): array
    {
        $expenses        = $this->expenseModel->getByTrip($tripId);
        $total           = $this->expenseModel->getTotalByTrip($tripId);
        $participantCount = $this->tripMemberModel->countApprovedMembers($tripId);

        $individualShare = $participantCount > 0 ? $total / $participantCount : 0;

        return [
            'expenses'         => $expenses,
            'total'            => round($total, 2),
            'participants'     => $participantCount,
            'individual_share' => round($individualShare, 2),
        ];
    }

    /**
     * Delete an expense (only the adder or organizer).
     */
    public function deleteExpense(int $expenseId, int $userId): void
    {
        $expense = $this->expenseModel->findById($expenseId);
        if (!$expense) {
            throw new RuntimeException('Expense not found.');
        }

        if ($expense['added_by'] !== $userId &&
            !$this->tripMemberModel->isOrganizer($expense['trip_id'], $userId)) {
            throw new RuntimeException('You do not have permission to delete this expense.');
        }

        $this->expenseModel->delete($expenseId);
    }
}
