<?php
/**
 * Expenses View
 * Variables: $trip, $summary (expenses, total, participants, individual_share), $isOrganizer, $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';
$userId = Security::userId();
?>

<div class="tm-page-header">
    <div class="container">
        <div class="d-flex align-items-center gap-3 mb-2">
            <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
        <h1 class="text-white fw-bold mb-1">
            <i class="bi bi-wallet2 me-2"></i>Trip Expenses
        </h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);"><?= Security::e($trip['title']) ?></p>
    </div>
</div>

<div class="container py-4 pb-5">
    <!-- Expense Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="tm-expense-summary">
                <div class="small fw-semibold mb-1 opacity-75">Total Expenses</div>
                <div class="tm-expense-total">$<?= number_format($summary['total'], 2) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tm-stat-card h-100">
                <div class="tm-stat-icon tm-stat-icon-purple"><i class="bi bi-people"></i></div>
                <div>
                    <div class="tm-stat-value"><?= $summary['participants'] ?></div>
                    <div class="tm-stat-label">Participants</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="tm-stat-card h-100">
                <div class="tm-stat-icon tm-stat-icon-green"><i class="bi bi-person-wallet"></i></div>
                <div>
                    <div class="tm-stat-value">$<?= number_format($summary['individual_share'], 2) ?></div>
                    <div class="tm-stat-label">Per Person</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Expense List -->
        <div class="col-lg-8">
            <?php if (empty($summary['expenses'])): ?>
                <div class="tm-card tm-card-body text-center py-5">
                    <div class="tm-empty-state">
                        <div class="icon"><i class="bi bi-receipt"></i></div>
                        <h5>No expenses yet</h5>
                        <p class="text-muted">Add expenses to track shared costs among participants.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="tm-card">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Expense</th>
                                <th>Added By</th>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                                <th class="text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($summary['expenses'] as $exp): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold"><?= Security::e($exp['title']) ?></div>
                                    <?php if ($exp['description']): ?>
                                        <div class="small text-muted"><?= Security::e($exp['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="tm-avatar-placeholder-sm" style="width:24px;height:24px;font-size:0.65rem;">
                                            <?= strtoupper(substr($exp['added_by_name'], 0, 1)) ?>
                                        </div>
                                        <span class="small"><?= Security::e($exp['added_by_name']) ?></span>
                                    </div>
                                </td>
                                <td class="small text-muted"><?= date('M d', strtotime($exp['expense_date'])) ?></td>
                                <td class="text-end fw-bold text-success">$<?= number_format($exp['amount'], 2) ?></td>
                                <td class="text-end">
                                    <?php if ($exp['added_by'] == $userId || $isOrganizer): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/expenses/<?= $exp['id'] ?>/delete">
                                        <?= Security::csrfField() ?>
                                        <button type="submit" class="btn btn-link btn-sm text-danger p-0"
                                                onclick="return confirm('Delete this expense?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="fw-bold">Total</td>
                                <td class="text-end fw-bold text-success">$<?= number_format($summary['total'], 2) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Add Expense Form -->
        <div class="col-lg-4">
            <div class="tm-card">
                <div class="tm-card-header">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Add Expense
                </div>
                <div class="tm-card-body">
                    <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/expenses/create">
                        <?= Security::csrfField() ?>
                        <div class="mb-3">
                            <label for="exp_title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="exp_title" name="title"
                                   placeholder="e.g., Campsite booking fee" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="exp_amount" class="form-label">Amount ($) <span class="text-danger">*</span></label>
                            <div class="tm-input-icon-wrapper">
                                <i class="bi bi-currency-dollar tm-input-icon"></i>
                                <input type="number" class="form-control" id="exp_amount" name="amount"
                                       min="0.01" step="0.01" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="exp_desc" class="form-label">Notes</label>
                            <textarea class="form-control" id="exp_desc" name="description"
                                      rows="2" placeholder="Optional notes..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="exp_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="exp_date" name="expense_date"
                                   value="<?= date('Y-m-d') ?>">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus me-2"></i>Add Expense
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
