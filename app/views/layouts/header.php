<?php
/**
 * TravelMate - Global Header Layout
 *
 * Included on every page. Provides navigation, flash messages,
 * notification bell, and Bootstrap 5 setup.
 *
 * Expected variables:
 *   $pageTitle   (string) — <title> content
 */

$currentUserId    = Security::userId();
$isLoggedIn       = Security::isLoggedIn();
$notifService     = $isLoggedIn ? new NotificationService() : null;
$unreadNotifCount = $isLoggedIn ? $notifService->getUnreadCount($currentUserId) : 0;
$flash            = $flash ?? Security::getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TravelMate — Collaborative travel planning. Create trips, find companions, share resources, track expenses, and make memories together.">
    <title><?= Security::e($pageTitle ?? APP_NAME) ?></title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

<!-- ======================================================
     NAVBAR
     ====================================================== -->
<nav class="navbar navbar-expand-lg tm-navbar sticky-top">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand tm-brand" href="<?= BASE_URL ?>/">
            <i class="bi bi-compass-fill me-2"></i>TravelMate
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarMain" aria-controls="navbarMain"
                aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/trips">
                        <i class="bi bi-map me-1"></i>Explore Trips
                    </a>
                </li>
                <?php if ($isLoggedIn): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/dashboard">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-primary btn-sm px-3 ms-1" href="<?= BASE_URL ?>/trips/create">
                        <i class="bi bi-plus-circle me-1"></i>Create Trip
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <?php if ($isLoggedIn): ?>
                    <!-- Notification Bell -->
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative" href="#" id="notifDropdown"
                           role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                            <?php if ($unreadNotifCount > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger tm-notif-badge" id="notifBadge">
                                    <?= $unreadNotifCount > 99 ? '99+' : $unreadNotifCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end tm-notif-dropdown" aria-labelledby="notifDropdown">
                            <li class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2">
                                <strong>Notifications</strong>
                                <?php if ($unreadNotifCount > 0): ?>
                                    <form method="POST" action="<?= BASE_URL ?>/notifications/read-all" class="d-inline">
                                        <?= Security::csrfField() ?>
                                        <button type="submit" class="btn btn-link btn-sm text-decoration-none p-0">
                                            Mark all read
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </li>
                            <li><hr class="dropdown-divider my-0"></li>
                            <?php
                            $headerNotifs = $notifService->getNotifications($currentUserId);
                            $headerNotifs = array_slice($headerNotifs, 0, 5);
                            if (empty($headerNotifs)):
                            ?>
                                <li class="dropdown-item text-muted text-center py-3">
                                    <i class="bi bi-bell-slash d-block fs-3 mb-1"></i>
                                    No notifications yet
                                </li>
                            <?php else: ?>
                                <?php foreach ($headerNotifs as $notif): ?>
                                    <li>
                                        <a href="<?= Security::e($notif['link'] ?? BASE_URL . '/notifications') ?>"
                                           class="dropdown-item tm-notif-item <?= $notif['is_read'] ? '' : 'tm-notif-unread' ?>">
                                            <div class="fw-semibold small"><?= Security::e($notif['title']) ?></div>
                                            <div class="text-muted small text-truncate"><?= Security::e($notif['message']) ?></div>
                                            <div class="text-muted" style="font-size:0.7rem"><?= date('M d, g:ia', strtotime($notif['created_at'])) ?></div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider my-0"></li>
                            <li>
                                <a class="dropdown-item text-center text-primary small py-2"
                                   href="<?= BASE_URL ?>/notifications">View All Notifications</a>
                            </li>
                        </ul>
                    </li>

                    <!-- User Avatar Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2"
                           href="#" id="userDropdown" role="button"
                           data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                            $photo = $_SESSION['user_photo'] ?? null;
                            if ($photo && file_exists(ROOT_PATH . '/uploads/profiles/' . $photo)):
                            ?>
                                <img src="<?= BASE_URL ?>/uploads/profiles/<?= Security::e($photo) ?>"
                                     alt="Profile" class="tm-avatar-sm rounded-circle">
                            <?php else: ?>
                                <div class="tm-avatar-placeholder-sm">
                                    <?= strtoupper(substr(Security::userName(), 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <span class="d-none d-lg-inline"><?= Security::e(Security::userName()) ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/profile">
                                    <i class="bi bi-person me-2"></i>My Profile
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= BASE_URL ?>/profile/edit">
                                    <i class="bi bi-gear me-2"></i>Settings
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?= BASE_URL ?>/auth/logout">
                                    <?= Security::csrfField() ?>
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>

                <?php else: ?>
                    <li class="nav-item me-2">
                        <a class="nav-link" href="<?= BASE_URL ?>/auth/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary px-4" href="<?= BASE_URL ?>/auth/register">Get Started</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- ======================================================
     FLASH MESSAGES
     ====================================================== -->
<?php if (!empty($flash)): ?>
<div class="container mt-3" id="flash-container">
    <?php foreach ($flash as $type => $message): ?>
        <?php
        $bsType = match($type) {
            'success' => 'success',
            'error'   => 'danger',
            'warning' => 'warning',
            default   => 'info',
        };
        $icon = match($type) {
            'success' => 'check-circle-fill',
            'error'   => 'exclamation-triangle-fill',
            'warning' => 'exclamation-circle-fill',
            default   => 'info-circle-fill',
        };
        ?>
        <div class="alert alert-<?= $bsType ?> alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-<?= $icon ?>"></i>
            <div><?= Security::e($message) ?></div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ======================================================
     MAIN CONTENT START
     ====================================================== -->
<main>
