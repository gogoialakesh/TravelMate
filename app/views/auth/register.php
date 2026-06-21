<?php
/**
 * Register Page View
 * Variables: $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';

$formErrors  = $_SESSION['form_errors']   ?? [];
$oldData     = $_SESSION['form_old_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_old_data']);

function regFieldError(array $errors, string $field): string {
    if (!empty($errors[$field][0])) {
        return '<div class="invalid-feedback d-block">' . Security::e($errors[$field][0]) . '</div>';
    }
    return '';
}
?>
<div class="tm-auth-bg" style="align-items:flex-start;padding-top:2rem;">
    <div class="tm-auth-card tm-animate-slide-up" style="max-width:520px;">
        <!-- Brand -->
        <div class="text-center mb-4">
            <a href="<?= BASE_URL ?>/" class="tm-brand justify-content-center fs-3 mb-3 d-inline-flex">
                <i class="bi bi-compass-fill me-2"></i>TravelMate
            </a>
            <h1 class="h4 fw-bold text-dark mb-1">Create Your Account</h1>
            <p class="text-muted small">Join thousands of travelers planning adventures together</p>
        </div>

        <!-- Register Form -->
        <form id="registerForm" method="POST" action="<?= BASE_URL ?>/auth/register" novalidate>
            <?= Security::csrfField() ?>

            <div class="row g-3">
                <!-- Full Name -->
                <div class="col-12">
                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                    <div class="tm-input-icon-wrapper">
                        <i class="bi bi-person tm-input-icon"></i>
                        <input type="text" class="form-control <?= !empty($formErrors['full_name']) ? 'is-invalid' : '' ?>"
                               id="full_name" name="full_name"
                               value="<?= Security::e($oldData['full_name'] ?? '') ?>"
                               placeholder="Your full name" required maxlength="100">
                    </div>
                    <?= regFieldError($formErrors, 'full_name') ?>
                </div>

                <!-- Username -->
                <div class="col-12">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <div class="tm-input-icon-wrapper">
                        <i class="bi bi-at tm-input-icon"></i>
                        <input type="text" class="form-control <?= !empty($formErrors['username']) ? 'is-invalid' : '' ?>"
                               id="username" name="username"
                               value="<?= Security::e($oldData['username'] ?? '') ?>"
                               placeholder="johndoe123" required minlength="3" maxlength="50"
                               pattern="[a-zA-Z0-9_]+">
                    </div>
                    <div class="form-text">Letters, numbers, and underscores only. 3–50 characters.</div>
                    <?= regFieldError($formErrors, 'username') ?>
                </div>

                <!-- Email -->
                <div class="col-12">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="tm-input-icon-wrapper">
                        <i class="bi bi-envelope tm-input-icon"></i>
                        <input type="email" class="form-control <?= !empty($formErrors['email']) ? 'is-invalid' : '' ?>"
                               id="email" name="email"
                               value="<?= Security::e($oldData['email'] ?? '') ?>"
                               placeholder="you@example.com" required autocomplete="email">
                    </div>
                    <?= regFieldError($formErrors, 'email') ?>
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="tm-input-icon-wrapper">
                        <i class="bi bi-lock tm-input-icon"></i>
                        <input type="password" class="form-control <?= !empty($formErrors['password']) ? 'is-invalid' : '' ?>"
                               id="password" name="password"
                               placeholder="Min. 8 characters" required minlength="8"
                               autocomplete="new-password">
                    </div>
                    <?= regFieldError($formErrors, 'password') ?>
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">Confirm <span class="text-danger">*</span></label>
                    <div class="tm-input-icon-wrapper">
                        <i class="bi bi-lock-fill tm-input-icon"></i>
                        <input type="password" class="form-control <?= !empty($formErrors['confirm_password']) ? 'is-invalid' : '' ?>"
                               id="confirm_password" name="confirm_password"
                               placeholder="Repeat password" required
                               autocomplete="new-password">
                    </div>
                    <?= regFieldError($formErrors, 'confirm_password') ?>
                </div>

                <!-- Submit -->
                <div class="col-12 mt-1">
                    <button type="submit" class="btn btn-primary w-100 btn-lg" id="registerBtn">
                        <i class="bi bi-rocket-takeoff me-2"></i>Create Account
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted small mb-0">
                Already have an account?
                <a href="<?= BASE_URL ?>/auth/login" class="fw-semibold">Sign in</a>
            </p>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
