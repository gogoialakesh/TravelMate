<?php
/**
 * Reviews / Leave Review View
 * Variables: $trip, $members, $pageTitle, $flash
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
            <i class="bi bi-star me-2"></i>Leave Reviews
        </h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);">Rate your fellow travelers from <?= Security::e($trip['title']) ?></p>
    </div>
</div>

<div class="container py-4 pb-5" style="max-width:760px;">
    <?php
    $reviewableMembers = array_filter($members, fn($m) => $m['user_id'] != $userId);
    if (empty($reviewableMembers)):
    ?>
        <div class="tm-card tm-card-body text-center py-5">
            <div class="tm-empty-state">
                <div class="icon"><i class="bi bi-people"></i></div>
                <h5>No one to review</h5>
                <p class="text-muted">You can review your fellow trip members once others have joined.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-4">
            <?php foreach ($reviewableMembers as $member): ?>
            <div class="tm-card">
                <div class="tm-card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="tm-avatar-placeholder-md">
                            <?= strtoupper(substr($member['full_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?= Security::e($member['full_name']) ?></div>
                            <div class="small text-muted">
                                <i class="bi bi-shield-fill me-1 text-primary"></i>
                                Reliability: <?= $member['reliability_score'] ?>%
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="<?= BASE_URL ?>/reviews/submit">
                        <?= Security::csrfField() ?>
                        <input type="hidden" name="trip_id" value="<?= $trip['id'] ?>">
                        <input type="hidden" name="reviewed_user_id" value="<?= $member['user_id'] ?>">

                        <!-- Star Rating -->
                        <div class="mb-3">
                            <label class="form-label">Rating <span class="text-danger">*</span></label>
                            <div class="d-flex gap-1">
                                <?php for ($s = 1; $s <= 5; $s++): ?>
                                <div class="form-check form-check-inline p-0 m-0">
                                    <input class="form-check-input visually-hidden" type="radio"
                                           name="rating_<?= $member['user_id'] ?>" value="<?= $s ?>"
                                           id="star_<?= $member['user_id'] ?>_<?= $s ?>">
                                    <label class="form-check-label fs-4 cursor-pointer tm-star"
                                           for="star_<?= $member['user_id'] ?>_<?= $s ?>"
                                           data-user-id="<?= $member['user_id'] ?>"
                                           data-rating="<?= $s ?>"
                                           style="cursor:pointer;color:var(--tm-gray-300);transition:color 0.1s;">
                                        ★
                                    </label>
                                </div>
                                <?php endfor; ?>
                            </div>
                            <!-- Hidden actual rating input -->
                            <input type="hidden" name="rating" id="ratingValue_<?= $member['user_id'] ?>" value="" required>
                        </div>

                        <!-- Review Text -->
                        <div class="mb-3">
                            <label for="review_<?= $member['user_id'] ?>" class="form-label">Your Review</label>
                            <textarea class="form-control" id="review_<?= $member['user_id'] ?>" name="review"
                                      rows="3" placeholder="Share your experience travelling with this person..."
                                      maxlength="2000"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Submit Review
                        </button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Star rating interactivity
document.querySelectorAll('.tm-star').forEach(star => {
    star.addEventListener('click', function() {
        const userId = this.getAttribute('data-user-id');
        const rating = parseInt(this.getAttribute('data-rating'), 10);
        // Update hidden input
        const hiddenInput = document.getElementById('ratingValue_' + userId);
        if (hiddenInput) {
            hiddenInput.value = rating;
        }
        // Update check status of radio input
        const radioInput = document.getElementById(`star_${userId}_${rating}`);
        if (radioInput) {
            radioInput.checked = true;
        }
        // Update star colors
        updateStars(userId, rating);
    });

    star.addEventListener('mouseover', function() {
        const userId = this.getAttribute('data-user-id');
        const rating = parseInt(this.getAttribute('data-rating'), 10);
        updateStars(userId, rating);
    });
});

document.querySelectorAll('.d-flex.gap-1').forEach(container => {
    container.addEventListener('mouseleave', function() {
        const hiddenInput = this.parentElement.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            const rating = parseInt(hiddenInput.value, 10) || 0;
            const userId = hiddenInput.id.replace('ratingValue_', '');
            updateStars(userId, rating);
        }
    });
});

function updateStars(userId, rating) {
    const stars = document.querySelectorAll(`.tm-star[data-user-id="${userId}"]`);
    stars.forEach(star => {
        const r = parseInt(star.getAttribute('data-rating'), 10);
        star.style.color = (r <= rating) ? '#F59E0B' : 'var(--tm-gray-300)';
    });
}
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
