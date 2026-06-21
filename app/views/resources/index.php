<?php
/**
 * Resources View
 * Variables: $trip, $resources, $isOrganizer, $pageTitle, $flash
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
            <i class="bi bi-box-seam me-2"></i>Resources &amp; Equipment
        </h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);"><?= Security::e($trip['title']) ?></p>
    </div>
</div>

<div class="container py-4 pb-5">
    <div class="row g-4">
        <!-- Resource List -->
        <div class="col-lg-8">
            <?php if (empty($resources)): ?>
                <div class="tm-card tm-card-body text-center py-5">
                    <div class="tm-empty-state">
                        <div class="icon"><i class="bi bi-box-seam"></i></div>
                        <h5>No resources yet</h5>
                        <p class="text-muted">Add equipment items that the group needs to bring.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($resources as $res): ?>
                    <?php
                    $assigned = (int)$res['quantity_assigned'];
                    $required = (int)$res['quantity_required'];
                    $fillStatus = $res['fulfillment_status'];
                    $pct = $required > 0 ? min(100, round($assigned / $required * 100)) : 0;
                    $fillClass = match($fillStatus) {
                        'fulfilled' => 'fulfilled',
                        'partial'   => 'partial',
                        default     => 'missing',
                    };
                    $statusIcon = match($fillStatus) {
                        'fulfilled' => '<i class="bi bi-check-circle-fill text-success"></i>',
                        'partial'   => '<i class="bi bi-exclamation-circle-fill text-warning"></i>',
                        default     => '<i class="bi bi-x-circle-fill text-danger"></i>',
                    };

                    // Check if current user has claimed this resource
                    $myClaim = null;
                    foreach ($res['assignments'] as $assignment) {
                        if ($assignment['user_id'] == $userId) {
                            $myClaim = $assignment;
                            break;
                        }
                    }
                    ?>
                    <div class="tm-card">
                        <div class="tm-card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                                <div>
                                    <div class="fw-bold d-flex align-items-center gap-2">
                                        <?= htmlspecialchars_decode($statusIcon) ?>
                                        <?= Security::e($res['resource_name']) ?>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <?= $assigned ?>/<?= $required ?> units claimed
                                    </div>
                                </div>
                                <?php if ($isOrganizer): ?>
                                <form method="POST" action="<?= BASE_URL ?>/resources/<?= $res['id'] ?>/delete">
                                    <?= Security::csrfField() ?>
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Delete this resource?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>

                            <!-- Progress Bar -->
                            <div class="tm-resource-bar mb-3">
                                <div class="tm-resource-fill <?= $fillClass ?>" style="width:<?= $pct ?>%;"></div>
                            </div>

                            <!-- Who's bringing it -->
                            <?php if (!empty($res['assignments'])): ?>
                                <div class="d-flex gap-2 flex-wrap mb-3">
                                    <?php foreach ($res['assignments'] as $assn): ?>
                                    <div class="d-flex align-items-center gap-1 tm-badge tm-badge-primary">
                                        <span><?= Security::e($assn['full_name']) ?></span>
                                        <span class="fw-bold">×<?= $assn['quantity'] ?></span>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Claim / Unclaim -->
                            <?php if ($myClaim): ?>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-muted">You're bringing <strong><?= $myClaim['quantity'] ?></strong> unit(s)</span>
                                    <form method="POST" action="<?= BASE_URL ?>/resources/<?= $res['id'] ?>/unclaim">
                                        <?= Security::csrfField() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="bi bi-x me-1"></i>Remove Claim
                                        </button>
                                    </form>
                                </div>
                            <?php elseif ($fillStatus !== 'fulfilled'): ?>
                                <form method="POST" action="<?= BASE_URL ?>/resources/<?= $res['id'] ?>/claim"
                                      class="d-flex gap-2 align-items-center">
                                    <?= Security::csrfField() ?>
                                    <input type="number" name="quantity" class="form-control form-control-sm"
                                           style="width:80px;" min="1" max="<?= max(1, $required - $assigned) ?>"
                                           value="1" required>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="bi bi-hand-thumbs-up me-1"></i>I'll Bring This
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-success small"><i class="bi bi-check-circle me-1"></i>All units claimed</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Add Resource Form -->
        <div class="col-lg-4">
            <div class="tm-card">
                <div class="tm-card-header">
                    <i class="bi bi-plus-circle me-2 text-primary"></i>Add Resource
                </div>
                <div class="tm-card-body">
                    <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/resources/create">
                        <?= Security::csrfField() ?>
                        <div class="mb-3">
                            <label for="resource_name" class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="resource_name" name="resource_name"
                                   placeholder="e.g., Tent, First aid kit" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="quantity_required" class="form-label">Quantity Needed <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity_required"
                                   name="quantity_required" min="1" value="1" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus me-2"></i>Add Resource
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
