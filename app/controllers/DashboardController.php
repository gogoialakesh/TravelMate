<?php
/**
 * TravelMate - DashboardController
 */

class DashboardController
{
    private Trip               $tripModel;
    private TripMember         $tripMemberModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->tripModel           = new Trip();
        $this->tripMemberModel     = new TripMember();
        $this->notificationService = new NotificationService();
    }

    /**
     * GET /dashboard — User dashboard.
     */
    public function index(array $params = []): void
    {
        Security::requireLogin();

        $userId        = Security::userId();
        $flash         = Security::getFlash();
        $pageTitle     = 'Dashboard — ' . APP_NAME;
        $myTrips       = $this->tripModel->getByUser($userId);
        $notifications = $this->notificationService->getNotifications($userId);
        $unreadCount   = $this->notificationService->getUnreadCount($userId);

        require_once VIEWS_PATH . '/dashboard/index.php';
    }
}
