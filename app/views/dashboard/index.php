<?php
/**
 * Dashboard View
 * Variables: $pageTitle, $myTrips, $notifications, $unreadCount, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';

$userId    = Security::userId();
$upcoming  = array_filter($myTrips, fn($t) => $t['status'] === 'upcoming');
$ongoing   = array_filter($myTrips, fn($t) => $t['status'] === 'ongoing');
$completed = array_filter($myTrips, fn($t) => $t['status'] === 'completed');
?>

<div class="tm-page-header">
    <div class="container">
        <h1 class="text-white fw-bold mb-1">
            Welcome back, <?= Security::e(Security::userName()) ?>! 👋
        </h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);">
            Your travel dashboard — manage trips, stay organized, and plan your next adventure.
        </p>
    </div>
</div>

<div class="container pb-5">

    <!-- Stat Cards -->
    <div class="row g-3 mb-5">
        <div class="col-sm-6 col-lg-3">
            <div class="tm-stat-card">
                <div class="tm-stat-icon tm-stat-icon-blue"><i class="bi bi-map"></i></div>
                <div>
                    <div class="tm-stat-value"><?= count($myTrips) ?></div>
                    <div class="tm-stat-label">Total Trips</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tm-stat-card">
                <div class="tm-stat-icon tm-stat-icon-green"><i class="bi bi-calendar-check"></i></div>
                <div>
                    <div class="tm-stat-value"><?= count($upcoming) + count($ongoing) ?></div>
                    <div class="tm-stat-label">Active Trips</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tm-stat-card">
                <div class="tm-stat-icon tm-stat-icon-orange"><i class="bi bi-trophy"></i></div>
                <div>
                    <div class="tm-stat-value"><?= count($completed) ?></div>
                    <div class="tm-stat-label">Completed</div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tm-stat-card">
                <div class="tm-stat-icon tm-stat-icon-purple"><i class="bi bi-bell"></i></div>
                <div>
                    <div class="tm-stat-value"><?= $unreadCount ?></div>
                    <div class="tm-stat-label">Unread Alerts</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Trips -->
        <div class="col-lg-8">
            <!-- My Trips -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="tm-section-title">My Trips</h2>
                <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>/trips" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-compass me-1"></i>Explore
                    </a>
                    <a href="<?= BASE_URL ?>/trips/create" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus me-1"></i>New Trip
                    </a>
                </div>
            </div>

            <?php if (empty($myTrips)): ?>
                <div class="tm-card tm-card-body text-center py-5">
                    <div class="tm-empty-state">
                        <div class="icon"><i class="bi bi-compass"></i></div>
                        <h5>No trips yet</h5>
                        <p class="text-muted">Create your first trip or join an existing one to get started.</p>
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="<?= BASE_URL ?>/trips/create" class="btn btn-primary">Create Trip</a>
                            <a href="<?= BASE_URL ?>/trips" class="btn btn-outline-primary">Explore Trips</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($myTrips as $trip): ?>
                    <div class="tm-card tm-animate-fade-in">
                        <div class="row g-0">
                            <!-- Cover Thumbnail -->
                            <div class="col-3" style="max-width:120px;">
                                <?php if ($trip['cover_image']): ?>
                                    <img src="<?= BASE_URL ?>/uploads/trips/<?= Security::e($trip['cover_image']) ?>"
                                         alt="" style="width:100%;height:100%;object-fit:cover;border-radius:var(--tm-border-radius-lg) 0 0 var(--tm-border-radius-lg);">
                                <?php else: ?>
                                    <div style="background:var(--tm-gradient-primary);height:100%;min-height:80px;border-radius:var(--tm-border-radius-lg) 0 0 var(--tm-border-radius-lg);display:flex;align-items:center;justify-content:center;font-size:2rem;color:rgba(255,255,255,0.5);">
                                        <i class="bi bi-mountain-snow"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="col">
                                <div class="tm-card-body d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            <span class="tm-badge tm-status-<?= $trip['status'] ?>">
                                                <?= ucfirst($trip['status']) ?>
                                            </span>
                                            <?php if ($trip['role'] === 'organizer'): ?>
                                                <span class="tm-badge tm-badge-dark">
                                                    <i class="bi bi-star-fill me-1"></i>Organizer
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <h3 class="h6 fw-bold mb-1">
                                            <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="text-dark">
                                                <?= Security::e($trip['title']) ?>
                                            </a>
                                        </h3>
                                        <div class="text-muted small d-flex gap-3 flex-wrap">
                                            <span><i class="bi bi-geo-alt me-1"></i><?= Security::e($trip['destination']) ?></span>
                                            <span><i class="bi bi-calendar3 me-1"></i><?= date('M d', strtotime($trip['start_date'])) ?> – <?= date('M d, Y', strtotime($trip['end_date'])) ?></span>
                                            <span><i class="bi bi-people me-1"></i><?= (int)$trip['member_count'] ?> members</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/chat" class="btn btn-outline-primary btn-sm btn-icon" title="Chat">
                                            <i class="bi bi-chat-dots"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="btn btn-primary btn-sm px-3">
                                            View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Notifications -->
        <div class="col-lg-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="tm-section-title">Recent Alerts</h2>
                <a href="<?= BASE_URL ?>/notifications" class="btn btn-link btn-sm text-decoration-none">View All</a>
            </div>

            <?php if (empty($notifications)): ?>
                <div class="tm-card tm-card-body text-center py-4">
                    <i class="bi bi-bell-slash fs-2 text-muted mb-2 d-block"></i>
                    <p class="text-muted small mb-0">No notifications yet</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-2">
                    <?php foreach (array_slice($notifications, 0, 8) as $notif): ?>
                    <div class="tm-card <?= !$notif['is_read'] ? 'border-start border-primary border-3' : '' ?>" style="border-radius:10px;">
                        <div class="p-3">
                            <div class="fw-semibold small mb-0"><?= Security::e($notif['title']) ?></div>
                            <div class="text-muted" style="font-size:0.8rem;line-height:1.4;margin-top:2px;"><?= Security::e($notif['message']) ?></div>
                            <div class="text-muted mt-1" style="font-size:0.7rem;"><?= date('M d, g:ia', strtotime($notif['created_at'])) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
