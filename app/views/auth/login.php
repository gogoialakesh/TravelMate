<?php
/**
 * Login Page View
 * Variables: $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';
?>
<div class="tm-auth-bg">
    <div class="tm-auth-card tm-animate-slide-up">
        <!-- Brand -->
        <div class="text-center mb-4">
            <a href="<?= BASE_URL ?>/" class="tm-brand justify-content-center fs-3 mb-3 d-inline-flex">
                <i class="bi bi-compass-fill me-2"></i>TravelMate
            </a>
            <h1 class="h4 fw-bold text-dark mb-1">Welcome back</h1>
            <p class="text-muted small">Sign in to your account to continue your adventures</p>
        </div>

        <!-- Flash Messages from header (already rendered above) -->


        <!-- Login Form -->
        <form id="loginForm" method="POST" action="<?= BASE_URL ?>/auth/login" novalidate>
            <?= Security::csrfField() ?>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <div class="tm-input-icon-wrapper">
                    <i class="bi bi-envelope tm-input-icon"></i>
                    <input type="email" class="form-control" id="email" name="email"
                           placeholder="you@example.com"
                           value="<?= Security::e($_POST['email'] ?? '') ?>"
                           required autocomplete="email">
                </div>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <div class="d-flex justify-content-between mb-1">
                    <label for="password" class="form-label mb-0">Password</label>
                </div>
                <div class="tm-input-icon-wrapper">
                    <i class="bi bi-lock tm-input-icon"></i>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Your password"
                           required autocomplete="current-password">
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 btn-lg" id="loginBtn">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted small mb-0">
                Don't have an account?
                <a href="<?= BASE_URL ?>/auth/register" class="fw-semibold">Create one free</a>
            </p>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
