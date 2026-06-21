<?php
/**
 * Albums Index View
 * Variables: $trip, $albums, $isOrganizer, $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="tm-page-header">
    <div class="container">
        <div class="d-flex align-items-center gap-3 mb-2">
            <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Back
            </a>
        </div>
        <h1 class="text-white fw-bold mb-1">
            <i class="bi bi-images me-2"></i>Trip Albums
        </h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);"><?= Security::e($trip['title']) ?></p>
    </div>
</div>

<div class="container py-4 pb-5">
    <!-- Create Album Form -->
    <div class="tm-card mb-4">
        <div class="tm-card-body">
            <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/albums/create"
                  class="d-flex gap-3 align-items-end flex-wrap">
                <?= Security::csrfField() ?>
                <div class="flex-grow-1">
                    <label for="albumTitle" class="form-label">New Album Name</label>
                    <input type="text" class="form-control" id="albumTitle" name="title"
                           placeholder="e.g., Day 1 — Summit Photos" required maxlength="255">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-folder-plus me-2"></i>Create Album
                </button>
            </form>
        </div>
    </div>

    <!-- Albums Grid -->
    <?php if (empty($albums)): ?>
        <div class="tm-card tm-card-body text-center py-5">
            <div class="tm-empty-state">
                <div class="icon"><i class="bi bi-images"></i></div>
                <h5>No albums yet</h5>
                <p class="text-muted">Create albums to organize your trip photos and videos.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($albums as $album): ?>
            <div class="col-sm-6 col-lg-4">
                <a href="<?= BASE_URL ?>/albums/<?= $album['id'] ?>" class="text-decoration-none">
                    <div class="tm-trip-card">
                        <!-- Cover Thumbnail -->
                        <?php if ($album['cover_path']): ?>
                            <img src="<?= BASE_URL ?>/../<?= Security::e($album['cover_path']) ?>"
                                 alt="<?= Security::e($album['title']) ?>" class="tm-trip-card-img">
                        <?php else: ?>
                            <div class="tm-trip-card-img-placeholder">
                                <i class="bi bi-image"></i>
                            </div>
                        <?php endif; ?>
                        <div class="tm-trip-card-body">
                            <h3 class="tm-trip-card-title text-dark"><?= Security::e($album['title']) ?></h3>
                            <div class="small text-muted">
                                <i class="bi bi-images me-1"></i><?= (int)$album['media_count'] ?> item<?= $album['media_count'] !== '1' ? 's' : '' ?>
                                &nbsp;·&nbsp;<?= date('M d, Y', strtotime($album['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
