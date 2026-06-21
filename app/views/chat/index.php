<?php
/**
 * Chat View
 * Variables: $trip, $messages, $lastId, $pageTitle, $flash
 */
$extraScripts = '<script src="' . BASE_URL . '/assets/js/chat.js"></script>';
require_once VIEWS_PATH . '/layouts/header.php';

$userId = Security::userId();
?>

<div class="container py-4">
    <div class="d-flex align-items-center gap-3 mb-3">
        <a href="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Back to Trip
        </a>
        <h1 class="h5 fw-bold mb-0">
            <i class="bi bi-chat-dots text-primary me-2"></i><?= Security::e($trip['title']) ?> — Chat
        </h1>
    </div>

    <div class="tm-chat-container">
        <!-- Messages -->
        <div class="tm-chat-messages" id="chatMessages" data-last-id="<?= $lastId ?>"
             data-trip-id="<?= $trip['id'] ?>" data-user-id="<?= $userId ?>">

            <?php if (empty($messages)): ?>
                <div class="text-center text-muted py-4" id="noMsgPlaceholder">
                    <i class="bi bi-chat-heart d-block fs-2 mb-2"></i>
                    Be the first to say something!
                </div>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <?php
                    $isOwn = ($msg['user_id'] == $userId);
                    ?>
                    <div class="tm-chat-bubble <?= $isOwn ? 'own' : 'other' ?>" id="msg-<?= $msg['id'] ?>">
                        <?php if (!$isOwn): ?>
                            <div class="tm-avatar-placeholder-sm flex-shrink-0" style="align-self:flex-end;">
                                <?= strtoupper(substr($msg['full_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <?php if (!$isOwn): ?>
                                <div class="fw-semibold mb-1" style="font-size:0.75rem;color:var(--tm-gray-600);">
                                    <?= Security::e($msg['full_name']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="tm-chat-text"><?= nl2br(Security::e($msg['message'])) ?></div>
                            <div class="text-muted mt-1" style="font-size:0.7rem;text-align:<?= $isOwn ? 'right' : 'left' ?>;">
                                <?= date('g:ia', strtotime($msg['created_at'])) ?>
                            </div>
                        </div>
                        <?php if ($isOwn): ?>
                            <div class="tm-avatar-placeholder-sm flex-shrink-0" style="align-self:flex-end;">
                                <?= strtoupper(substr($msg['full_name'], 0, 1)) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Input Area -->
        <div class="tm-chat-input-area">
            <textarea class="tm-chat-input" id="messageInput"
                      placeholder="Type your message... (Enter to send, Shift+Enter for new line)"
                      rows="1" maxlength="2000" aria-label="Message"></textarea>
            <button class="btn btn-primary btn-icon" id="sendBtn" title="Send message">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>

    <!-- Hidden CSRF for AJAX -->
    <input type="hidden" id="csrfToken" value="<?= Security::generateCsrfToken() ?>">
    <input type="hidden" id="sendUrl" value="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/chat/send">
    <input type="hidden" id="pollUrl" value="<?= BASE_URL ?>/trips/<?= $trip['id'] ?>/chat/poll">
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
