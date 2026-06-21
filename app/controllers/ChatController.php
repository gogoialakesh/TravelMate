<?php
/**
 * TravelMate - ChatController
 */

class ChatController
{
    private Message    $messageModel;
    private TripMember $tripMemberModel;
    private Trip       $tripModel;

    public function __construct()
    {
        $this->messageModel    = new Message();
        $this->tripMemberModel = new TripMember();
        $this->tripModel       = new Trip();
    }

    /**
     * GET /trips/{id}/chat — Chat UI.
     */
    public function index(array $params = []): void
    {
        Security::requireLogin();

        $tripId = (int)($params['id'] ?? 0);
        $userId = Security::userId();

        $trip = $this->tripModel->findById($tripId);
        if (!$trip) {
            Security::setFlash('error', 'Trip not found.');
            header('Location: ' . BASE_URL . '/trips');
            exit;
        }

        if (!$this->tripMemberModel->isApprovedMember($tripId, $userId)) {
            Security::setFlash('error', 'Only approved trip members can access the chat.');
            header('Location: ' . BASE_URL . '/trips/' . $tripId);
            exit;
        }

        $messages  = $this->messageModel->getLatestMessages($tripId, MESSAGES_PER_PAGE);
        $lastId    = $this->messageModel->getLastId($tripId);
        $pageTitle = 'Chat — ' . $trip['title'];
        $flash     = Security::getFlash();

        require_once VIEWS_PATH . '/chat/index.php';
    }

    /**
     * POST /trips/{id}/chat/send — Send a message.
     */
    public function send(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId  = (int)($params['id'] ?? 0);
        $userId  = Security::userId();
        $message = Security::sanitize($_POST['message'] ?? '');

        if (!$this->tripMemberModel->isApprovedMember($tripId, $userId)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            exit;
        }

        if (empty($message)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message cannot be empty.']);
            exit;
        }

        if (mb_strlen($message) > 2000) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Message too long.']);
            exit;
        }

        try {
            $msgId = $this->messageModel->create([
                'trip_id' => $tripId,
                'user_id' => $userId,
                'message' => $message,
            ]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message_id' => $msgId]);
        } catch (Exception $e) {
            Logger::error('Message send failed', ['error' => $e->getMessage()]);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to send message.']);
        }
        exit;
    }

    /**
     * GET /trips/{id}/chat/poll — AJAX polling for new messages.
     *
     * @param array $params
     */
    public function poll(array $params = []): void
    {
        Security::requireLogin();

        $tripId  = (int)($params['id'] ?? 0);
        $userId  = Security::userId();
        $afterId = (int)($_GET['after'] ?? 0);

        if (!$this->tripMemberModel->isApprovedMember($tripId, $userId)) {
            http_response_code(403);
            echo json_encode([]);
            exit;
        }

        $messages = $this->messageModel->getByTrip($tripId, 50, $afterId);

        // Escape for XSS
        $safeMessages = array_map(function (array $msg): array {
            return [
                'id'            => $msg['id'],
                'user_id'       => $msg['user_id'],
                'full_name'     => Security::e($msg['full_name']),
                'profile_photo' => $msg['profile_photo'],
                'message'       => Security::e($msg['message']),
                'created_at'    => $msg['created_at'],
            ];
        }, $messages);

        header('Content-Type: application/json');
        echo json_encode($safeMessages);
        exit;
    }
}
