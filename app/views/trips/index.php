<?php
/**
 * Trip Listing Page
 * Variables: $pageTitle, $trips, $filters, $page, $totalPages, $total, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';

$tripTypes = ['trekking' => 'Trekking', 'camping' => 'Camping', 'backpacking' => 'Backpacking',
              'road_trip' => 'Road Trip', 'photography' => 'Photography', 'adventure' => 'Adventure'];
?>

<div class="tm-page-header">
    <div class="container">
        <h1 class="text-white fw-bold mb-2">Explore Trips</h1>
        <p class="mb-4" style="color:rgba(255,255,255,0.75);">
            Find your next adventure — <?= number_format($total) ?> trip<?= $total !== 1 ? 's' : '' ?> available
        </p>
        <!-- Search / Filter Form -->
        <form method="GET" action="<?= BASE_URL ?>/trips" class="row g-2">
            <div class="col-md-5">
                <div class="tm-input-icon-wrapper">
                    <i class="bi bi-search tm-input-icon"></i>
                    <input type="text" class="form-control" name="destination"
                           value="<?= Security::e($filters['destination'] ?? '') ?>"
                           placeholder="Search by destination...">
                </div>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="trip_type">
                    <option value="">All Trip Types</option>
                    <?php foreach ($tripTypes as $val => $label): ?>
                        <option value="<?= $val ?>" <?= ($filters['trip_type'] ?? '') === $val ? 'selected' : '' ?>>
                            <?= $label ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="bi bi-search me-1"></i>Search
                </button>
                <?php if (!empty($filters['destination']) || !empty($filters['trip_type'])): ?>
                    <a href="<?= BASE_URL ?>/trips" class="btn btn-outline-light">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="container pb-5">
    <?php if (empty($trips)): ?>
        <div class="text-center py-5">
            <div class="tm-empty-state">
                <div class="icon"><i class="bi bi-compass"></i></div>
                <h5>No trips found</h5>
                <p class="text-muted">Try different search terms or be the first to create a trip!</p>
                <?php if (Security::isLoggedIn()): ?>
                    <a href="<?= BASE_URL ?>/trips/create" class="btn btn-primary">Create a Trip</a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($trips as $i => $trip): ?>
            <div class="col-md-6 col-lg-4 tm-animate-fade-in" style="animation-delay:<?= $i * 0.05 ?>s">
                <div class="tm-trip-card">
                    <?php if ($trip['cover_image']): ?>
                        <img src="<?= BASE_URL ?>/uploads/trips/<?= Security::e($trip['cover_image']) ?>"
                             alt="<?= Security::e($trip['title']) ?>" class="tm-trip-card-img">
                    <?php else: ?>
                        <div class="tm-trip-card-img-placeholder">
                            <i class="bi bi-mountain-snow"></i>
                        </div>
                    <?php endif; ?>

                    <div class="tm-trip-card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="tm-badge tm-status-<?= $trip['status'] ?>">
                                <?= ucfirst($trip['status']) ?>
                            </span>
                            <?php if ($trip['trip_type']): ?>
                                <span class="tm-badge tm-badge-primary">
                                    <?= Security::e(ucwords(str_replace('_', ' ', $trip['trip_type']))) ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <h3 class="tm-trip-card-title"><?= Security::e($trip['title']) ?></h3>
                        <div class="tm-trip-card-destination">
                            <i class="bi bi-geo-alt me-1"></i><?= Security::e($trip['destination']) ?>
                        </div>
                        <div class="tm-trip-card-meta mt-1">
                            <span><i class="bi bi-calendar3 me-1"></i><?= date('M d', strtotime($trip['start_date'])) ?> – <?= date('M d, Y', strtotime($trip['end_date'])) ?></span>
                        </div>

                        <!-- Capacity bar -->
                        <?php
                        $filled  = (int)$trip['member_count'];
                        $max     = (int)$trip['max_participants'];
                        $pct     = $max > 0 ? min(100, round($filled / $max * 100)) : 0;
                        $barCls  = $pct >= 100 ? 'bg-danger' : ($pct >= 80 ? 'bg-warning' : 'bg-success');
                        ?>
                        <div class="mt-2">
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span><i class="bi bi-people me-1"></i><?= $filled ?>/<?= $max ?> members</span>
                                <span><?= $max - $filled ?> seats left</span>
                            </div>
                            <div class="progress" style="height:5px;border-radius:3px;">
                                <div class="progress-bar <?= $barCls ?>" style="width:<?= $pct ?>%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="tm-trip-card-footer">
                        <div class="d-flex align-items-center gap-2">
                            <div class="tm-avatar-placeholder-sm" style="width:28px;height:28px;font-size:0.7rem;">
                                <?= strtoupper(substr($trip['creator_name'], 0, 1)) ?>
                            </div>
                            <span class="small text-muted"><?= Security::e($trip['creator_name']) ?></span>
                        </div>
                        <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="btn btn-primary btn-sm">
                            View Trip <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <nav class="mt-5 d-flex justify-content-center" aria-label="Trip pagination">
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page - 1])) ?>">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $p])) ?>">
                            <?= $p ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $page + 1])) ?>">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
