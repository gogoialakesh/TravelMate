<?php
/**
 * Trip Detail Page
 * Variables: $trip, $members, $pendingMembers, $userMembership, $isOrganizer, $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';

$userId          = Security::userId();
$isApproved      = $userMembership && $userMembership['join_status'] === 'approved';
$isPending       = $userMembership && $userMembership['join_status'] === 'pending';
$isMember        = $isApproved || $isOrganizer;
$approvedCount   = count($members);
$seatsLeft       = max(0, $trip['max_participants'] - $approvedCount);
?>

<!-- Trip Cover Header -->
<div style="position:relative;overflow:hidden;height:320px;background:var(--tm-gradient-hero);">
    <?php if ($trip['cover_image']): ?>
        <img src="<?= BASE_URL ?>/uploads/trips/<?= Security::e($trip['cover_image']) ?>"
             alt="<?= Security::e($trip['title']) ?>"
             style="width:100%;height:100%;object-fit:cover;opacity:0.7;">
    <?php endif; ?>
    <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.8) 0%,rgba(0,0,0,0.2) 100%);"></div>
    <div class="container h-100 d-flex align-items-end pb-4" style="position:relative;z-index:1;">
        <div>
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                <span class="tm-badge tm-status-<?= $trip['status'] ?>">
                    <?= ucfirst($trip['status']) ?>
                </span>
                <?php if ($trip['trip_type']): ?>
                    <span class="tm-badge tm-badge-info">
                        <?= Security::e(ucwords(str_replace('_', ' ', $trip['trip_type']))) ?>
                    </span>
                <?php endif; ?>
                <span class="tm-badge tm-badge-dark">
                    <i class="bi bi-<?= $trip['visibility'] === 'public' ? 'globe' : 'lock' ?> me-1"></i>
                    <?= ucfirst($trip['visibility']) ?>
                </span>
            </div>
            <h1 class="text-white fw-bold mb-1" style="font-size:clamp(1.5rem,4vw,2.5rem);">
                <?= Security::e($trip['title']) ?>
            </h1>
            <div class="text-white-50 d-flex gap-3 flex-wrap small">
                <span><i class="bi bi-geo-alt me-1"></i><?= Security::e($trip['destination']) ?></span>
                <span><i class="bi bi-calendar3 me-1"></i><?= date('M d', strtotime($trip['start_date'])) ?> – <?= date('M d, Y', strtotime($trip['end_date'])) ?></span>
                <span><i class="bi bi-person-circle me-1"></i>Organized by <?= Security::e($trip['creator_name']) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">

            <!-- Description -->
            <?php if ($trip['description']): ?>
            <div class="tm-card tm-card-body mb-4">
                <h2 class="tm-section-title mb-3">About This Trip</h2>
                <p class="text-muted mb-0" style="line-height:1.8;"><?= nl2br(Security::e($trip['description'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Trip Navigation (for approved members) -->
            <?php if ($isMember): ?>
            <div class="tm-card mb-4">
                <div class="tm-card-body">
                    <h2 class="tm-section-title mb-3">Trip Modules</h2>
                    <div class="row g-2">
                        <?php
                        $modules = [
                            ['url' => "/trips/{$trip['id']}/responsibilities", 'icon' => 'bi-clipboard-check', 'label' => 'Responsibilities', 'color' => '#2563EB'],
                            ['url' => "/trips/{$trip['id']}/resources",        'icon' => 'bi-box-seam',         'label' => 'Resources',        'color' => '#10B981'],
                            ['url' => "/trips/{$trip['id']}/chat",             'icon' => 'bi-chat-dots',        'label' => 'Chat',             'color' => '#8B5CF6'],
                            ['url' => "/trips/{$trip['id']}/expenses",         'icon' => 'bi-wallet2',          'label' => 'Expenses',         'color' => '#F97316'],
                            ['url' => "/trips/{$trip['id']}/albums",           'icon' => 'bi-images',           'label' => 'Albums',           'color' => '#0EA5E9'],
                            ['url' => "/trips/{$trip['id']}/reviews",          'icon' => 'bi-star',             'label' => 'Reviews',          'color' => '#F59E0B'],
                        ];
                        foreach ($modules as $mod):
                        ?>
                        <div class="col-4 col-md-2">
                            <a href="<?= BASE_URL ?><?= $mod['url'] ?>"
                               class="d-flex flex-column align-items-center text-center p-2 rounded tm-card text-decoration-none"
                               style="gap:0.4rem;border-radius:10px!important;">
                                <div style="width:40px;height:40px;border-radius:10px;background:<?= $mod['color'] ?>18;color:<?= $mod['color'] ?>;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">
                                    <i class="<?= $mod['icon'] ?>"></i>
                                </div>
                                <span style="font-size:0.7rem;font-weight:600;color:var(--tm-dark)"><?= $mod['label'] ?></span>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Pending Requests (organizer only) -->
            <?php if ($isOrganizer && !empty($pendingMembers)): ?>
            <div class="tm-card mb-4">
                <div class="tm-card-header">
                    <i class="bi bi-person-check me-2 text-warning"></i>
                    Pending Join Requests (<?= count($pendingMembers) ?>)
                </div>
                <div class="tm-card-body">
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($pendingMembers as $pending): ?>
                        <div class="d-flex justify-content-between align-items-center gap-3 p-3 rounded" style="background:var(--tm-gray-50);border:1px solid var(--tm-gray-200);">
                            <div class="d-flex align-items-center gap-3">
                                <div class="tm-avatar-placeholder-sm">
                                    <?= strtoupper(substr($pending['full_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-semibold small"><?= Security::e($pending['full_name']) ?></div>
                                    <div class="text-muted" style="font-size:0.75rem;">@<?= Security::e($pending['username']) ?> · ⭐ <?= $pending['reliability_score'] ?>%</div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/approve">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="member_id" value="<?= $pending['user_id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-lg me-1"></i>Approve
                                    </button>
                                </form>
                                <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/reject">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="member_id" value="<?= $pending['user_id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-lg me-1"></i>Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Approved Members -->
            <div class="tm-card">
                <div class="tm-card-header">
                    <i class="bi bi-people me-2 text-primary"></i>
                    Trip Members (<?= $approvedCount ?>/<?= $trip['max_participants'] ?>)
                </div>
                <div class="tm-card-body">
                    <?php if (empty($members)): ?>
                        <p class="text-muted text-center py-3 mb-0">No members yet. Be the first to join!</p>
                    <?php else: ?>
                        <div class="row g-3">
                            <?php foreach ($members as $member): ?>
                            <div class="col-sm-6 col-lg-4">
                                <div class="tm-member-card">
                                    <div class="tm-avatar-placeholder-sm">
                                        <?= strtoupper(substr($member['full_name'], 0, 1)) ?>
                                    </div>
                                    <div class="flex-grow-1 min-width-0">
                                        <div class="fw-semibold small text-truncate">
                                            <a href="<?= BASE_URL ?>/profile/<?= $member['user_id'] ?>" class="text-dark">
                                                <?= Security::e($member['full_name']) ?>
                                            </a>
                                        </div>
                                        <div class="d-flex gap-1 mt-1">
                                            <?php if ($member['role'] === 'organizer'): ?>
                                                <span class="tm-badge tm-badge-primary" style="font-size:0.65rem;">Organizer</span>
                                            <?php else: ?>
                                                <span class="tm-badge" style="background:var(--tm-gray-100);color:var(--tm-gray-600);font-size:0.65rem;">Member</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Action Card -->
            <div class="tm-card mb-3">
                <div class="tm-card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between text-muted small mb-1">
                            <span>Availability</span>
                            <span><?= $approvedCount ?>/<?= $trip['max_participants'] ?></span>
                        </div>
                        <?php
                        $pct    = $trip['max_participants'] > 0 ? min(100, round($approvedCount / $trip['max_participants'] * 100)) : 0;
                        $barCls = $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success');
                        ?>
                        <div class="progress" style="height:8px;border-radius:4px;">
                            <div class="progress-bar <?= $barCls ?>" style="width:<?= $pct ?>%"></div>
                        </div>
                        <?php if ($seatsLeft > 0): ?>
                            <div class="text-success small mt-1"><i class="bi bi-check-circle me-1"></i><?= $seatsLeft ?> seat<?= $seatsLeft !== 1 ? 's' : '' ?> available</div>
                        <?php else: ?>
                            <div class="text-danger small mt-1"><i class="bi bi-x-circle me-1"></i>Trip is full</div>
                        <?php endif; ?>
                    </div>

                    <?php if (!Security::isLoggedIn()): ?>
                        <a href="<?= BASE_URL ?>/auth/login" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login to Join
                        </a>
                    <?php elseif ($isOrganizer): ?>
                        <div class="d-grid gap-2">
                            <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/edit" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-2"></i>Edit Trip
                            </a>
                            <?php if ($trip['status'] === 'ongoing' || $trip['status'] === 'upcoming'): ?>
                            <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/complete">
                                <?= Security::csrfField() ?>
                                <button type="submit" class="btn btn-success w-100" id="btn-complete-trip"
                                        onclick="return confirm('Mark this trip as completed?')">
                                    <i class="bi bi-check2-all me-2"></i>Mark Complete
                                </button>
                            </form>
                            <?php endif; ?>
                            <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/delete">
                                <?= Security::csrfField() ?>
                                <button type="submit" class="btn btn-outline-danger w-100" id="btn-delete-trip"
                                        onclick="return confirm('Delete this trip? This cannot be undone.')">
                                    <i class="bi bi-trash me-2"></i>Delete Trip
                                </button>
                            </form>
                        </div>
                    <?php elseif ($isApproved): ?>
                        <div class="d-grid gap-2">
                            <div class="alert alert-success py-2 text-center small mb-0">
                                <i class="bi bi-check-circle-fill me-1"></i>You're part of this trip!
                            </div>
                            <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/leave">
                                <?= Security::csrfField() ?>
                                <button type="submit" class="btn btn-outline-danger w-100 btn-sm"
                                        onclick="return confirm('Leave this trip?')">
                                    <i class="bi bi-box-arrow-left me-2"></i>Leave Trip
                                </button>
                            </form>
                        </div>
                    <?php elseif ($isPending): ?>
                        <div class="alert alert-warning py-2 text-center small mb-0">
                            <i class="bi bi-hourglass-split me-1"></i>Request pending approval…
                        </div>
                    <?php elseif ($trip['status'] !== 'completed' && $trip['status'] !== 'cancelled' && $seatsLeft > 0): ?>
                        <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/join">
                            <?= Security::csrfField() ?>
                            <button type="submit" class="btn btn-primary w-100 btn-lg">
                                <i class="bi bi-person-plus me-2"></i>Request to Join
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary w-100" disabled>Not Available</button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Trip Info Card -->
            <div class="tm-card">
                <div class="tm-card-body">
                    <h3 class="tm-section-title mb-3">Trip Info</h3>
                    <dl class="row mb-0 small">
                        <dt class="col-5 text-muted">Duration</dt>
                        <dd class="col-7 fw-semibold">
                            <?php
                            $d1 = new DateTime($trip['start_date']);
                            $d2 = new DateTime($trip['end_date']);
                            $duration = $d1->diff($d2)->days + 1;
                            echo $duration . ' day' . ($duration !== 1 ? 's' : '');
                            ?>
                        </dd>
                        <dt class="col-5 text-muted">Start</dt>
                        <dd class="col-7 fw-semibold"><?= date('D, M d Y', strtotime($trip['start_date'])) ?></dd>
                        <dt class="col-5 text-muted">End</dt>
                        <dd class="col-7 fw-semibold"><?= date('D, M d Y', strtotime($trip['end_date'])) ?></dd>
                        <?php if ($trip['trip_type']): ?>
                        <dt class="col-5 text-muted">Type</dt>
                        <dd class="col-7 fw-semibold"><?= Security::e(ucwords(str_replace('_', ' ', $trip['trip_type']))) ?></dd>
                        <?php endif; ?>
                        <dt class="col-5 text-muted">Created</dt>
                        <dd class="col-7 fw-semibold"><?= date('M d, Y', strtotime($trip['created_at'])) ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
