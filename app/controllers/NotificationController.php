<?php
/**
 * TravelMate - NotificationController
 */

class NotificationController
{
    private NotificationService $service;
    private Notification        $notificationModel;

    public function __construct()
    {
        $this->service           = new NotificationService();
        $this->notificationModel = new Notification();
    }

    /**
     * GET /notifications — Full notification page.
     */
    public function index(array $params = []): void
    {
        Security::requireLogin();

        $userId        = Security::userId();
        $notifications = $this->service->getNotifications($userId);
        $pageTitle     = 'Notifications — ' . APP_NAME;
        $flash         = Security::getFlash();

        require_once VIEWS_PATH . '/notifications/index.php';
    }

    /**
     * POST /notifications/{id}/read — Mark one notification as read.
     */
    public function markRead(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $id     = (int)($params['id'] ?? 0);
        $userId = Security::userId();

        $this->notificationModel->markRead($id, $userId);

        // If AJAX, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header('Location: ' . BASE_URL . '/notifications');
        exit;
    }

    /**
     * POST /notifications/read-all — Mark all notifications as read.
     */
    public function markAllRead(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $userId = Security::userId();
        $this->notificationModel->markAllRead($userId);

        header('Location: ' . BASE_URL . '/notifications');
        exit;
    }

    /**
     * GET /notifications/count — AJAX unread count.
     */
    public function count(array $params = []): void
    {
        Security::requireLogin();

        $userId = Security::userId();
        $count  = $this->service->getUnreadCount($userId);

        header('Content-Type: application/json');
        echo json_encode(['count' => $count]);
        exit;
    }
}
