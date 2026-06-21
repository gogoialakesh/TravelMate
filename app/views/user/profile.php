<?php
/**
 * User Profile View
 * Variables: $profile (user data + reviews + average_rating), $isOwnProfile, $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';
$userId = Security::userId();
?>

<div class="tm-page-header">
    <div class="container">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <?php if ($profile['profile_photo'] && file_exists(ROOT_PATH . '/uploads/profiles/' . $profile['profile_photo'])): ?>
                <img src="<?= BASE_URL ?>/uploads/profiles/<?= Security::e($profile['profile_photo']) ?>"
                     alt="<?= Security::e($profile['full_name']) ?>"
                     class="tm-avatar-lg" style="border-color:rgba(255,255,255,0.4);">
            <?php else: ?>
                <div class="tm-avatar-placeholder-lg">
                    <?= strtoupper(substr($profile['full_name'], 0, 1)) ?>
                </div>
            <?php endif; ?>

            <div>
                <h1 class="text-white fw-bold mb-1"><?= Security::e($profile['full_name']) ?></h1>
                <div class="text-white-50 small mb-2">@<?= Security::e($profile['username']) ?></div>
                <div class="d-flex gap-3 flex-wrap">
                    <?php if ($profile['location']): ?>
                        <span class="text-white-50 small"><i class="bi bi-geo-alt me-1"></i><?= Security::e($profile['location']) ?></span>
                    <?php endif; ?>
                    <span class="text-white-50 small"><i class="bi bi-calendar3 me-1"></i>Joined <?= date('M Y', strtotime($profile['created_at'])) ?></span>
                </div>
            </div>

            <?php if ($isOwnProfile): ?>
                <div class="ms-auto">
                    <a href="<?= BASE_URL ?>/profile/edit" class="btn btn-outline-light">
                        <i class="bi bi-pencil me-2"></i>Edit Profile
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container py-4 pb-5">
    <div class="row g-4">
        <!-- Left: Profile Info -->
        <div class="col-lg-4">
            <!-- Reliability Score -->
            <div class="tm-card mb-3">
                <div class="tm-card-body text-center">
                    <h2 class="tm-section-title justify-content-center mb-3">Reliability Score</h2>
                    <?php $score = (float)$profile['reliability_score']; ?>
                    <div class="d-flex justify-content-center mb-3">
                        <div class="tm-reliability-score" style="--score:<?= $score ?>">
                            <span class="tm-reliability-score-text"><?= round($score) ?>%</span>
                        </div>
                    </div>
                    <div class="small text-muted">
                        <?php if ($score >= 80): ?>
                            <span class="text-success fw-semibold">Highly Reliable Traveler ✓</span>
                        <?php elseif ($score >= 60): ?>
                            <span class="text-warning fw-semibold">Good Traveler</span>
                        <?php else: ?>
                            <span class="text-muted">Building reputation...</span>
                        <?php endif; ?>
                    </div>
                    <div class="mt-2 small text-muted">
                        Avg Rating: <?= round($profile['average_rating'], 1) ?>/5.0
                        &nbsp;(<?= count($profile['reviews']) ?> review<?= count($profile['reviews']) !== 1 ? 's' : '' ?>)
                    </div>
                </div>
            </div>

            <!-- Bio -->
            <?php if ($profile['bio']): ?>
            <div class="tm-card mb-3">
                <div class="tm-card-body">
                    <h3 class="tm-section-title mb-2">About</h3>
                    <p class="text-muted small mb-0"><?= nl2br(Security::e($profile['bio'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Right: Reviews -->
        <div class="col-lg-8">
            <h2 class="tm-section-title mb-3">
                Reviews
                <?php if (!empty($profile['reviews'])): ?>
                    <span class="ms-2 tm-badge tm-badge-primary"><?= count($profile['reviews']) ?></span>
                <?php endif; ?>
            </h2>

            <?php if (empty($profile['reviews'])): ?>
                <div class="tm-card tm-card-body text-center py-5">
                    <div class="tm-empty-state">
                        <div class="icon"><i class="bi bi-star"></i></div>
                        <h5>No reviews yet</h5>
                        <p class="text-muted">Reviews appear after completing trips with other travelers.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($profile['reviews'] as $review): ?>
                    <div class="tm-card">
                        <div class="tm-card-body">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="tm-avatar-placeholder-sm">
                                        <?= strtoupper(substr($review['reviewer_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small"><?= Security::e($review['reviewer_name']) ?></div>
                                        <div class="text-muted" style="font-size:0.72rem;">
                                            Trip: <?= Security::e($review['trip_title']) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <span style="color:<?= $s <= $review['rating'] ? '#F59E0B' : 'var(--tm-gray-300)' ?>;font-size:1rem;">★</span>
                                    <?php endfor; ?>
                                    <span class="text-muted small ms-1"><?= $review['rating'] ?>/5</span>
                                </div>
                            </div>
                            <?php if ($review['review']): ?>
                                <p class="small text-muted mb-0"><?= Security::e($review['review']) ?></p>
                            <?php endif; ?>
                            <div class="text-muted mt-2" style="font-size:0.7rem;">
                                <?= date('M d, Y', strtotime($review['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
