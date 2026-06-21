<?php
/**
 * TravelMate - AlbumController
 */

class AlbumController
{
    private AlbumService $service;
    private Album        $albumModel;
    private Media        $mediaModel;
    private TripMember   $tripMemberModel;
    private Trip         $tripModel;

    public function __construct()
    {
        $this->service         = new AlbumService();
        $this->albumModel      = new Album();
        $this->mediaModel      = new Media();
        $this->tripMemberModel = new TripMember();
        $this->tripModel       = new Trip();
    }

    /**
     * GET /trips/{id}/albums
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
            Security::setFlash('error', 'Access restricted to approved trip members.');
            header('Location: ' . BASE_URL . '/trips/' . $tripId);
            exit;
        }

        $albums      = $this->albumModel->getByTrip($tripId);
        $isOrganizer = ($trip['creator_id'] === $userId);
        $pageTitle   = 'Albums — ' . $trip['title'];
        $flash       = Security::getFlash();

        require_once VIEWS_PATH . '/albums/index.php';
    }

    /**
     * POST /trips/{id}/albums/create
     */
    public function createAlbum(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $tripId = (int)($params['id'] ?? 0);
        $userId = Security::userId();
        $title  = Security::sanitize($_POST['title'] ?? '');

        if (empty($title)) {
            Security::setFlash('error', 'Album title is required.');
            header('Location: ' . BASE_URL . '/trips/' . $tripId . '/albums');
            exit;
        }

        try {
            $this->service->createAlbum($tripId, $userId, $title);
            Security::setFlash('success', 'Album created!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/trips/' . $tripId . '/albums');
        exit;
    }

    /**
     * GET /albums/{id} — Show album media gallery.
     */
    public function show(array $params = []): void
    {
        Security::requireLogin();

        $albumId = (int)($params['id'] ?? 0);
        $userId  = Security::userId();

        $album = $this->albumModel->findById($albumId);
        if (!$album) {
            Security::setFlash('error', 'Album not found.');
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        if (!$this->tripMemberModel->isApprovedMember($album['trip_id'], $userId)) {
            Security::setFlash('error', 'Access restricted to approved trip members.');
            header('Location: ' . BASE_URL . '/trips/' . $album['trip_id']);
            exit;
        }

        $page   = max(1, (int)($_GET['page'] ?? 1));
        $offset = ($page - 1) * MEDIA_PER_PAGE;
        $media  = $this->mediaModel->getByAlbum($albumId, MEDIA_PER_PAGE, $offset);

        $isOrganizer = $this->tripMemberModel->isOrganizer($album['trip_id'], $userId);
        $pageTitle   = Security::e($album['title']) . ' — Album';
        $flash       = Security::getFlash();

        require_once VIEWS_PATH . '/albums/show.php';
    }

    /**
     * POST /albums/{id}/upload
     */
    public function upload(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $albumId = (int)($params['id'] ?? 0);
        $userId  = Security::userId();
        $caption = Security::sanitize($_POST['caption'] ?? '');

        if (!FileUpload::hasFile($_FILES['media'] ?? null)) {
            Security::setFlash('error', 'Please select a file to upload.');
            header('Location: ' . BASE_URL . '/albums/' . $albumId);
            exit;
        }

        try {
            $this->service->uploadMedia($albumId, $userId, $_FILES['media'], $caption);
            Security::setFlash('success', 'Media uploaded successfully!');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/albums/' . $albumId);
        exit;
    }

    /**
     * POST /media/{id}/delete
     */
    public function deleteMedia(array $params = []): void
    {
        Security::requireLogin();
        Security::validateCsrf();

        $mediaId = (int)($params['id'] ?? 0);
        $userId  = Security::userId();

        $media   = $this->mediaModel->findById($mediaId);
        $albumId = $media ? $media['album_id'] : 0;

        try {
            $this->service->deleteMedia($mediaId, $userId);
            Security::setFlash('success', 'Media deleted.');
        } catch (RuntimeException $e) {
            Security::setFlash('error', $e->getMessage());
        }

        header('Location: ' . BASE_URL . '/albums/' . $albumId);
        exit;
    }
}
