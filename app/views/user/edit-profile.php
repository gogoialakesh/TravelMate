<?php
/**
 * Edit Profile View
 * Variables: $user, $pageTitle, $flash, $formErrors
 */
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="tm-page-header">
    <div class="container">
        <h1 class="text-white fw-bold mb-1">Account Settings</h1>
        <p class="mb-0" style="color:rgba(255,255,255,0.75);">Update your profile information and preferences</p>
    </div>
</div>

<div class="container py-4 pb-5" style="max-width:760px;">
    <div class="row g-4">
        <!-- Profile Update -->
        <div class="col-12">
            <div class="tm-card">
                <div class="tm-card-header">
                    <i class="bi bi-person-circle me-2 text-primary"></i>Profile Information
                </div>
                <div class="tm-card-body">
                    <form method="POST" action="<?= BASE_URL ?>/profile/edit"
                          enctype="multipart/form-data">
                        <?= Security::csrfField() ?>
                        <div class="row g-3">
                            <!-- Avatar Preview -->
                            <div class="col-12 text-center mb-2">
                                <?php if ($user['profile_photo'] && file_exists(ROOT_PATH . '/uploads/profiles/' . $user['profile_photo'])): ?>
                                    <img src="<?= BASE_URL ?>/uploads/profiles/<?= Security::e($user['profile_photo']) ?>"
                                         alt="Avatar" class="tm-avatar-lg mb-2">
                                <?php else: ?>
                                    <div class="tm-avatar-placeholder-lg mx-auto mb-2">
                                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <label for="profile_photo" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-camera me-1"></i>Change Photo
                                    </label>
                                    <input type="file" id="profile_photo" name="profile_photo"
                                           class="visually-hidden" accept=".jpg,.jpeg,.png,.webp"
                                           onchange="previewPhoto(this)">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name"
                                       value="<?= Security::e($user['full_name']) ?>" required maxlength="100">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="@<?= Security::e($user['username']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="<?= Security::e($user['email']) ?>" disabled>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <div class="tm-input-icon-wrapper">
                                    <i class="bi bi-geo-alt tm-input-icon"></i>
                                    <input type="text" class="form-control" id="location" name="location"
                                           value="<?= Security::e($user['location'] ?? '') ?>"
                                           placeholder="City, Country" maxlength="255">
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3"
                                          placeholder="Tell other travelers a bit about yourself..."
                                          maxlength="1000"><?= Security::e($user['bio'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-2"></i>Save Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-12">
            <div class="tm-card">
                <div class="tm-card-header">
                    <i class="bi bi-lock me-2 text-danger"></i>Change Password
                </div>
                <div class="tm-card-body">
                    <form method="POST" action="<?= BASE_URL ?>/profile/change-password">
                        <?= Security::csrfField() ?>
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="current_password" class="form-label">Current Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="current_password"
                                       name="current_password" required autocomplete="current-password">
                            </div>
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="new_password"
                                       name="new_password" required minlength="8" autocomplete="new-password">
                            </div>
                            <div class="col-md-6">
                                <label for="confirm_new_password" class="form-label">Confirm New <span class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="confirm_new_password"
                                       name="confirm_new_password" required autocomplete="new-password">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="bi bi-key me-2"></i>Change Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const avatarEl = document.querySelector('.tm-avatar-lg, .tm-avatar-placeholder-lg');
            if (avatarEl) {
                avatarEl.outerHTML = `<img src="${e.target.result}" class="tm-avatar-lg mb-2" alt="Preview">`;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
