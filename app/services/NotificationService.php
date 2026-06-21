<?php
/**
 * TravelMate - NotificationService
 *
 * Creates and manages user notifications.
 * Must be loaded before services that depend on it.
 */

class NotificationService
{
    private Notification $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new Notification();
    }

    /**
     * Send a notification to a user.
     *
     * @param int    $userId
     * @param string $type     join_request|join_approved|responsibility_assigned|expense_added|media_uploaded|general
     * @param string $title
     * @param string $message
     * @param string $link     Optional URL
     */
    public function send(int $userId, string $type, string $title, string $message, string $link = ''): void
    {
        try {
            $this->notificationModel->create([
                'user_id' => $userId,
                'type'    => $type,
                'title'   => $title,
                'message' => $message,
                'link'    => $link ?: null,
            ]);
        } catch (Exception $e) {
            Logger::error('Failed to send notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get unread notification count for a user.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->notificationModel->countUnread($userId);
    }

    /**
     * Get recent notifications for a user.
     *
     * @param int $userId
     * @return array
     */
    public function getNotifications(int $userId): array
    {
        return $this->notificationModel->getByUser($userId, 20);
    }
}
