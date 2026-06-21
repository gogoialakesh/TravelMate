<?php
/**
 * Album Detail (Media Gallery) View
 * Variables: $album, $media, $isOrganizer, $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';
$userId = Security::userId();
?>

<div class="tm-page-header">
    <div class="container">
        <div class="d-flex align-items-center gap-3 mb-2">
            <a href="<?= BASE_URL ?>/trips/<?= $album['trip_id'] ?>/albums" class="btn btn-outline-light btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Albums
            </a>
        </div>
        <h1 class="text-white fw-bold mb-1">
            <i class="bi bi-images me-2"></i><?= Security::e($album['title']) ?>
        </h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);"><?= Security::e($album['trip_title']) ?></p>
    </div>
</div>

<div class="container py-4 pb-5">
    <!-- Upload Form -->
    <div class="tm-card mb-4">
        <div class="tm-card-body">
            <form method="POST" action="<?= BASE_URL ?>/albums/<?= $album['id'] ?>/upload"
                  enctype="multipart/form-data" class="d-flex gap-3 align-items-end flex-wrap">
                <?= Security::csrfField() ?>
                <div>
                    <label class="form-label">Photo / Video <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" name="media"
                           accept=".jpg,.jpeg,.png,.webp,.gif,.mp4,.mov,.avi" required>
                    <div class="form-text">Images: JPG/PNG/WebP (max 10MB) · Videos: MP4/MOV (max 50MB)</div>
                </div>
                <div class="flex-grow-1">
                    <label class="form-label">Caption</label>
                    <input type="text" class="form-control" name="caption"
                           placeholder="Optional caption..." maxlength="500">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-cloud-upload me-2"></i>Upload
                </button>
            </form>
        </div>
    </div>

    <!-- Media Grid -->
    <?php if (empty($media)): ?>
        <div class="tm-card tm-card-body text-center py-5">
            <div class="tm-empty-state">
                <div class="icon"><i class="bi bi-camera"></i></div>
                <h5>No photos yet</h5>
                <p class="text-muted">Upload photos and videos from your trip!</p>
            </div>
        </div>
    <?php else: ?>
        <div class="tm-media-grid">
            <?php foreach ($media as $item): ?>
            <div class="tm-media-item">
                <?php if ($item['file_type'] === 'video'): ?>
                    <video src="<?= BASE_URL ?>/../<?= Security::e($item['file_path']) ?>"
                           muted preload="metadata"></video>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:2rem;pointer-events:none;">
                        <i class="bi bi-play-circle-fill"></i>
                    </div>
                <?php else: ?>
                    <img src="<?= BASE_URL ?>/../<?= Security::e($item['file_path']) ?>"
                         alt="<?= Security::e($item['caption'] ?? '') ?>" loading="lazy">
                <?php endif; ?>
                <div class="tm-media-overlay">
                    <div class="text-white" style="font-size:0.75rem;">
                        <?php if ($item['caption']): ?>
                            <div class="fw-semibold mb-1"><?= Security::e($item['caption']) ?></div>
                        <?php endif; ?>
                        <div class="opacity-75"><?= Security::e($item['full_name']) ?></div>
                        <?php if ($item['user_id'] == $userId || $isOrganizer): ?>
                        <form method="POST" action="<?= BASE_URL ?>/media/<?= $item['id'] ?>/delete"
                              class="d-inline mt-1">
                            <?= Security::csrfField() ?>
                            <button type="submit" class="btn btn-sm btn-danger p-0 px-2"
                                    style="font-size:0.7rem;"
                                    onclick="return confirm('Delete this media?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
