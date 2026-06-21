<?php
/**
 * TravelMate - AlbumService
 */

class AlbumService
{
    private Album      $albumModel;
    private Media      $mediaModel;
    private TripMember $tripMemberModel;
    private NotificationService $notificationService;

    public function __construct()
    {
        $this->albumModel          = new Album();
        $this->mediaModel          = new Media();
        $this->tripMemberModel     = new TripMember();
        $this->notificationService = new NotificationService();
    }

    /**
     * Create a new album for a trip.
     * Only approved members can create albums.
     */
    public function createAlbum(int $tripId, int $userId, string $title): int
    {
        if (!$this->tripMemberModel->isApprovedMember($tripId, $userId)) {
            throw new RuntimeException('Only approved trip members can create albums.');
        }

        return $this->albumModel->create(['trip_id' => $tripId, 'title' => $title]);
    }

    /**
     * Upload media to an album.
     *
     * @param int   $albumId
     * @param int   $userId
     * @param array $file     $_FILES element
     * @param string $caption
     * @return int  New media ID
     */
    public function uploadMedia(int $albumId, int $userId, array $file, string $caption = ''): int
    {
        $album = $this->albumModel->findById($albumId);
        if (!$album) {
            throw new RuntimeException('Album not found.');
        }

        if (!$this->tripMemberModel->isApprovedMember($album['trip_id'], $userId)) {
            throw new RuntimeException('Only approved trip members can upload media.');
        }

        // Determine file type and upload
        $ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $isVideo   = in_array($ext, ALLOWED_VIDEO_EXTS, true);
        $isImage   = in_array($ext, ALLOWED_IMAGE_EXTS, true);

        if (!$isImage && !$isVideo) {
            throw new RuntimeException('Unsupported file type.');
        }

        if ($isVideo) {
            $fileName = FileUpload::uploadAlbumVideo($file);
            $filePath = 'uploads/albums/videos/' . $fileName;
            $fileType = 'video';
        } else {
            $fileName = FileUpload::uploadAlbumImage($file);
            $filePath = 'uploads/albums/images/' . $fileName;
            $fileType = 'image';
        }

        $mediaId = $this->mediaModel->create([
            'album_id'  => $albumId,
            'user_id'   => $userId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'caption'   => $caption,
        ]);

        return $mediaId;
    }

    /**
     * Delete a media item (uploader or organizer only).
     */
    public function deleteMedia(int $mediaId, int $userId): void
    {
        $media = $this->mediaModel->findById($mediaId);
        if (!$media) {
            throw new RuntimeException('Media not found.');
        }

        $album = $this->albumModel->findById($media['album_id']);

        if ($media['user_id'] !== $userId &&
            !$this->tripMemberModel->isOrganizer($album['trip_id'], $userId)) {
            throw new RuntimeException('You do not have permission to delete this media.');
        }

        // Delete file from disk
        $fullPath = ROOT_PATH . '/' . $media['file_path'];
        FileUpload::delete($fullPath);

        $this->mediaModel->delete($mediaId);
    }
}
