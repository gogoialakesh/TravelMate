<?php
/**
 * Notifications Page
 * Variables: $notifications, $pageTitle, $flash
 */
require_once VIEWS_PATH . '/layouts/header.php';
$userId = Security::userId();
?>

<div class="tm-page-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1"><i class="bi bi-bell me-2"></i>Notifications</h1>
                <p class="mb-0" style="color:rgba(255,255,255,0.75);">Stay updated on your trips and activities</p>
            </div>
            <?php
            $unread = array_filter($notifications, fn($n) => !$n['is_read']);
            if (!empty($unread)):
            ?>
                <form method="POST" action="<?= BASE_URL ?>/notifications/read-all">
                    <?= Security::csrfField() ?>
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-check2-all me-1"></i>Mark All Read
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container py-4 pb-5" style="max-width:760px;">
    <?php if (empty($notifications)): ?>
        <div class="tm-card tm-card-body text-center py-5">
            <div class="tm-empty-state">
                <div class="icon"><i class="bi bi-bell-slash"></i></div>
                <h5>No notifications yet</h5>
                <p class="text-muted">You'll see activity from your trips here.</p>
            </div>
        </div>
    <?php else: ?>
        <div class="d-flex flex-column gap-2">
            <?php foreach ($notifications as $notif): ?>
            <?php
            $iconMap = [
                'join_request'          => ['icon' => 'bi-person-plus', 'color' => '#2563EB'],
                'join_approved'         => ['icon' => 'bi-check-circle', 'color' => '#10B981'],
                'responsibility_assigned' => ['icon' => 'bi-clipboard-check', 'color' => '#8B5CF6'],
                'expense_added'         => ['icon' => 'bi-wallet2', 'color' => '#F97316'],
                'media_uploaded'        => ['icon' => 'bi-images', 'color' => '#0EA5E9'],
                'general'               => ['icon' => 'bi-bell', 'color' => '#64748B'],
            ];
            $ni = $iconMap[$notif['type']] ?? $iconMap['general'];
            ?>
            <div class="tm-card <?= !$notif['is_read'] ? 'border-start border-primary border-3' : '' ?>"
                 style="border-radius:10px;">
                <div class="p-3 d-flex align-items-start gap-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:<?= $ni['color'] ?>18;color:<?= $ni['color'] ?>;display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                        <i class="<?= $ni['icon'] ?>"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-1">
                            <div class="fw-semibold <?= !$notif['is_read'] ? '' : 'text-muted' ?>">
                                <?= Security::e($notif['title']) ?>
                                <?php if (!$notif['is_read']): ?>
                                    <span class="tm-badge tm-badge-primary ms-1" style="font-size:0.6rem;">New</span>
                                <?php endif; ?>
                            </div>
                            <span class="text-muted" style="font-size:0.72rem;">
                                <?= date('M d, g:ia', strtotime($notif['created_at'])) ?>
                            </span>
                        </div>
                        <div class="text-muted small mt-1"><?= Security::e($notif['message']) ?></div>
                        <div class="d-flex gap-2 mt-2">
                            <?php if ($notif['link']): ?>
                                <a href="<?= Security::e($notif['link']) ?>" class="btn btn-primary btn-sm">
                                    View <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            <?php endif; ?>
                            <?php if (!$notif['is_read']): ?>
                                <form method="POST" action="<?= BASE_URL ?>/notifications/<?= $notif['id'] ?>/read">
                                    <?= Security::csrfField() ?>
                                    <button type="submit" class="btn btn-outline-secondary btn-sm">
                                        Mark Read
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
