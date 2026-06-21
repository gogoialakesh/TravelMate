<?php
/**
 * Responsibilities View
 * Variables: $trip, $responsibilities, $members, $isOrganizer, $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';
$userId = Security::userId();
$statusLabels = ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'];
$statusColors = ['pending' => 'tm-badge-warning', 'in_progress' => 'tm-badge-primary', 'completed' => 'tm-badge-success'];
?>

<div class="tm-page-header">
    <div class="container">
        <div class="d-flex align-items-center gap-3 mb-2">
            <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
        <h1 class="text-white fw-bold mb-1">
            <i class="bi bi-clipboard-check me-2"></i>Responsibilities
        </h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);"><?= Security::e($trip['title']) ?></p>
    </div>
</div>

<div class="container py-4 pb-5">
    <div class="row g-4">
        <!-- Left: Task List -->
        <div class="col-lg-8">
            <?php if (empty($responsibilities)): ?>
                <div class="tm-card tm-card-body text-center py-5">
                    <div class="tm-empty-state">
                        <div class="icon"><i class="bi bi-clipboard-check"></i></div>
                        <h5>No responsibilities yet</h5>
                        <p class="text-muted">Add tasks and assign them to trip members.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($responsibilities as $resp): ?>
                    <div class="tm-task-item <?= $resp['status'] ?>">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="tm-badge <?= $statusColors[$resp['status']] ?? '' ?>">
                                        <?= $statusLabels[$resp['status']] ?? $resp['status'] ?>
                                    </span>
                                    <?php if ($resp['due_date']): ?>
                                        <span class="small text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>Due <?= date('M d, Y', strtotime($resp['due_date'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="fw-semibold"><?= Security::e($resp['title']) ?></div>
                                <?php if ($resp['description']): ?>
                                    <div class="small text-muted mt-1"><?= Security::e($resp['description']) ?></div>
                                <?php endif; ?>
                                <?php if ($resp['assigned_name']): ?>
                                    <div class="small mt-1">
                                        <i class="bi bi-person-check text-primary me-1"></i>
                                        Assigned to <strong><?= Security::e($resp['assigned_name']) ?></strong>
                                    </div>
                                <?php else: ?>
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-person-dash me-1"></i>Unassigned
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Actions -->
                            <div class="d-flex gap-1 flex-wrap">
                                <?php if ($resp['status'] !== 'completed'): ?>
                                    <!-- Assign -->
                                    <?php if ($isOrganizer || !$resp['assigned_to']): ?>
                                    <button class="btn btn-outline-primary btn-sm" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#assign-<?= $resp['id'] ?>">
                                        <i class="bi bi-person-plus"></i>
                                    </button>
                                    <?php endif; ?>
                                    <!-- Complete -->
                                    <?php if ($resp['assigned_to'] == $userId || $isOrganizer): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/responsibilities/<?= $resp['id'] ?>/complete">
                                        <?= Security::csrfField() ?>
                                        <button type="submit" class="btn btn-success btn-sm" title="Mark complete">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <!-- Delete (organizer) -->
                                <?php if ($isOrganizer): ?>
                                <form method="POST" action="<?= BASE_URL ?>/responsibilities/<?= $resp['id'] ?>/delete">
                                    <?= Security::csrfField() ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete"
                                            onclick="return confirm('Delete this responsibility?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Assign Collapse -->
                        <?php if ($resp['status'] !== 'completed' && ($isOrganizer || !$resp['assigned_to'])): ?>
                        <div class="collapse mt-2" id="assign-<?= $resp['id'] ?>">
                            <form method="POST" action="<?= BASE_URL ?>/responsibilities/<?= $resp['id'] ?>/assign"
                                  class="d-flex gap-2">
                                <?= Security::csrfField() ?>
                                <select class="form-select form-select-sm" name="assignee_id" required>
                                    <option value="">— Select member —</option>
                                    <?php foreach ($members as $m): ?>
                                        <option value="<?= $m['user_id'] ?>"
                                            <?= ($resp['assigned_to'] ?? '') == $m['user_id'] ? 'selected' : '' ?>>
                                            <?= Security::e($m['full_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Assign</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Add Responsibility Form -->
        <div class="col-lg-4">
            <div class="tm-card">
                <div class="tm-card-header">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Add Responsibility
                </div>
                <div class="tm-card-body">
                    <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/responsibilities/create">
                        <?= Security::csrfField() ?>
                        <div class="mb-3">
                            <label for="resp_title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="resp_title" name="title"
                                   placeholder="e.g., Book campsite" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="resp_desc" class="form-label">Description</label>
                            <textarea class="form-control" id="resp_desc" name="description"
                                      rows="2" placeholder="Optional details..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="resp_due" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="resp_due" name="due_date">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus me-2"></i>Add Task
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
