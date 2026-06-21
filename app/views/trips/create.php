<?php
/**
 * Create Trip Page
 * Variables: $pageTitle, $flash, $formErrors
 */
require_once VIEWS_PATH . '/layouts/header.php';

function tripFieldError(array $errors, string $field): string {
    if (!empty($errors[$field][0])) {
        return '<div class="invalid-feedback d-block">' . Security::e($errors[$field][0]) . '</div>';
    }
    return '';
}

$tripTypes = ['trekking' => 'Trekking', 'camping' => 'Camping', 'backpacking' => 'Backpacking',
              'road_trip' => 'Road Trip', 'photography' => 'Photography', 'adventure' => 'Adventure'];
?>

<div class="tm-page-header">
    <div class="container">
        <h1 class="text-white fw-bold mb-1">Create a New Trip</h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);">
            Set up your adventure and invite companions to join you
        </p>
    </div>
</div>

<div class="container pb-5" style="max-width:780px;">
    <div class="tm-card">
        <div class="tm-card-body">
            <form id="createTripForm" method="POST" action="<?= BASE_URL ?>/trips/create"
                  enctype="multipart/form-data" novalidate>
                <?= Security::csrfField() ?>

                <div class="row g-4">
                    <!-- Trip Title -->
                    <div class="col-12">
                        <label for="title" class="form-label">Trip Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= !empty($formErrors['title']) ? 'is-invalid' : '' ?>"
                               id="title" name="title"
                               value="<?= Security::e($_POST['title'] ?? '') ?>"
                               placeholder="e.g., Dzukou Valley Trek 2026"
                               required maxlength="255">
                        <?= tripFieldError($formErrors, 'title') ?>
                    </div>

                    <!-- Destination -->
                    <div class="col-md-7">
                        <label for="destination" class="form-label">Destination <span class="text-danger">*</span></label>
                        <div class="tm-input-icon-wrapper">
                            <i class="bi bi-geo-alt tm-input-icon"></i>
                            <input type="text" class="form-control <?= !empty($formErrors['destination']) ? 'is-invalid' : '' ?>"
                                   id="destination" name="destination"
                                   value="<?= Security::e($_POST['destination'] ?? '') ?>"
                                   placeholder="e.g., Nagaland, India"
                                   required maxlength="255">
                        </div>
                        <?= tripFieldError($formErrors, 'destination') ?>
                    </div>

                    <!-- Trip Type -->
                    <div class="col-md-5">
                        <label for="trip_type" class="form-label">Trip Type</label>
                        <select class="form-select" id="trip_type" name="trip_type">
                            <option value="">Select type...</option>
                            <?php foreach ($tripTypes as $val => $label): ?>
                                <option value="<?= $val ?>" <?= ($_POST['trip_type'] ?? '') === $val ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  rows="4" placeholder="Describe your trip, itinerary, requirements..."><?= Security::e($_POST['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Start Date -->
                    <div class="col-md-4">
                        <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control <?= !empty($formErrors['start_date']) ? 'is-invalid' : '' ?>"
                               id="start_date" name="start_date"
                               value="<?= Security::e($_POST['start_date'] ?? '') ?>"
                               min="<?= date('Y-m-d') ?>" required>
                        <?= tripFieldError($formErrors, 'start_date') ?>
                    </div>

                    <!-- End Date -->
                    <div class="col-md-4">
                        <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control <?= !empty($formErrors['end_date']) ? 'is-invalid' : '' ?>"
                               id="end_date" name="end_date"
                               value="<?= Security::e($_POST['end_date'] ?? '') ?>"
                               min="<?= date('Y-m-d') ?>" required>
                        <?= tripFieldError($formErrors, 'end_date') ?>
                    </div>

                    <!-- Max Participants -->
                    <div class="col-md-4">
                        <label for="max_participants" class="form-label">Max Participants <span class="text-danger">*</span></label>
                        <input type="number" class="form-control <?= !empty($formErrors['max_participants']) ? 'is-invalid' : '' ?>"
                               id="max_participants" name="max_participants"
                               value="<?= Security::e($_POST['max_participants'] ?? '10') ?>"
                               min="2" max="500" required>
                        <?= tripFieldError($formErrors, 'max_participants') ?>
                    </div>

                    <!-- Visibility -->
                    <div class="col-md-6">
                        <label class="form-label">Visibility</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility"
                                       id="vis_public" value="public"
                                       <?= ($_POST['visibility'] ?? 'public') === 'public' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="vis_public">
                                    <i class="bi bi-globe me-1"></i>Public
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visibility"
                                       id="vis_private" value="private"
                                       <?= ($_POST['visibility'] ?? '') === 'private' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="vis_private">
                                    <i class="bi bi-lock me-1"></i>Private
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Cover Image -->
                    <div class="col-md-6">
                        <label for="cover_image" class="form-label">Cover Photo</label>
                        <input type="file" class="form-control" id="cover_image" name="cover_image"
                               accept=".jpg,.jpeg,.png,.webp">
                        <div class="form-text">JPG, PNG, WebP — max 10 MB</div>
                    </div>

                    <!-- Submit -->
                    <div class="col-12 pt-2">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-plus-circle me-2"></i>Create Trip
                            </button>
                            <a href="<?= BASE_URL ?>/trips" class="btn btn-outline-secondary btn-lg">
                                Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
