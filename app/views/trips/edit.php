<?php
/**
 * Edit Trip View
 * Variables: $trip, $pageTitle, $formErrors, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';

$tripTypes = ['trekking' => 'Trekking', 'camping' => 'Camping', 'backpacking' => 'Backpacking',
              'road_trip' => 'Road Trip', 'photography' => 'Photography', 'adventure' => 'Adventure'];

function etFieldError(array $errors, string $field): string {
    return !empty($errors[$field][0])
        ? '<div class="invalid-feedback d-block">' . Security::e($errors[$field][0]) . '</div>'
        : '';
}
?>
<div class="tm-page-header">
    <div class="container">
        <h1 class="text-white fw-bold mb-1">Edit Trip</h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);"><?= Security::e($trip['title']) ?></p>
    </div>
</div>

<div class="container pb-5" style="max-width:780px;">
    <div class="tm-card">
        <div class="tm-card-body">
            <form method="POST" action="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/edit"
                  enctype="multipart/form-data" novalidate>
                <?= Security::csrfField() ?>

                <div class="row g-4">
                    <div class="col-12">
                        <label for="title" class="form-label">Trip Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="<?= Security::e($_POST['title'] ?? $trip['title']) ?>"
                               required maxlength="255">
                        <?= etFieldError($formErrors, 'title') ?>
                    </div>
                    <div class="col-md-7">
                        <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="destination" name="destination"
                               value="<?= Security::e($_POST['destination'] ?? $trip['destination']) ?>"
                               required maxlength="255">
                        <?= etFieldError($formErrors, 'destination') ?>
                    </div>
                    <div class="col-md-5">
                        <label for="trip_type" class="form-label">Trip Type</label>
                        <select class="form-select" id="trip_type" name="trip_type">
                            <option value="">Select type...</option>
                            <?php foreach ($tripTypes as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($trip['trip_type'] ?? '') === $val ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4"><?= Security::e($trip['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                               value="<?= Security::e($trip['start_date']) ?>" required>
                        <?= etFieldError($formErrors, 'start_date') ?>
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                               value="<?= Security::e($trip['end_date']) ?>" required>
                        <?= etFieldError($formErrors, 'end_date') ?>
                    </div>
                    <div class="col-md-4">
                        <label for="max_participants" class="form-label">Max Participants <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="max_participants" name="max_participants"
                               value="<?= Security::e($trip['max_participants']) ?>" min="2" required>
                        <?= etFieldError($formErrors, 'max_participants') ?>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Visibility</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility"
                                       id="vis_public" value="public"
                                       <?= $trip['visibility'] === 'public' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="vis_public"><i class="bi bi-globe me-1"></i>Public</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility"
                                       id="vis_private" value="private"
                                       <?= $trip['visibility'] === 'private' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="vis_private"><i class="bi bi-lock me-1"></i>Private</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 pt-2">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-save me-2"></i>Save Changes
                            </button>
                            <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
