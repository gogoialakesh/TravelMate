<?php
/**
 * TravelMate - File Upload Handler
 *
 * Centralized file upload management.
 * Validates MIME type, extension, and file size.
 * Renames files to prevent conflicts.
 * Never trusts original filenames.
 */

class FileUpload
{
    /**
     * Upload a profile photo.
     *
     * @param array  $file     $_FILES['field_name']
     * @return string          Stored filename on success
     * @throws RuntimeException on validation or move failure
     */
    public static function uploadProfilePhoto(array $file): string
    {
        return self::uploadImage($file, UPLOAD_PROFILES);
    }

    /**
     * Upload a trip cover image.
     *
     * @param array  $file     $_FILES['field_name']
     * @return string          Stored filename on success
     */
    public static function uploadTripCover(array $file): string
    {
        return self::uploadImage($file, UPLOAD_TRIPS);
    }

    /**
     * Upload an album image.
     *
     * @param array  $file     $_FILES['field_name']
     * @return string          Stored filename
     */
    public static function uploadAlbumImage(array $file): string
    {
        return self::uploadImage($file, UPLOAD_ALBUMS_IMG);
    }

    /**
     * Upload an album video.
     *
     * @param array  $file     $_FILES['field_name']
     * @return string          Stored filename
     */
    public static function uploadAlbumVideo(array $file): string
    {
        return self::uploadVideo($file, UPLOAD_ALBUMS_VID);
    }

    // --------------------------------------------------------
    // Internal Upload Processors
    // --------------------------------------------------------

    /**
     * Handle image upload with full validation.
     *
     * @param array  $file   $_FILES element
     * @param string $dir    Target upload directory
     * @return string        Generated filename
     */
    private static function uploadImage(array $file, string $dir): string
    {
        self::checkUploadError($file);

        // Size check
        if ($file['size'] > MAX_IMAGE_SIZE) {
            throw new RuntimeException('Image file size exceeds the 10 MB limit.');
        }

        // Extension check
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_IMAGE_EXTS, true)) {
            throw new RuntimeException('Invalid image type. Allowed: jpg, jpeg, png, webp.');
        }

        // MIME type check using finfo (not trusting $_FILES['type'])
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, ALLOWED_IMAGE_MIMES, true)) {
            throw new RuntimeException('Invalid image MIME type detected.');
        }

        return self::moveFile($file['tmp_name'], $dir, $ext);
    }

    /**
     * Handle video upload with full validation.
     *
     * @param array  $file   $_FILES element
     * @param string $dir    Target upload directory
     * @return string        Generated filename
     */
    private static function uploadVideo(array $file, string $dir): string
    {
        self::checkUploadError($file);

        // Size check
        if ($file['size'] > MAX_VIDEO_SIZE) {
            throw new RuntimeException('Video file size exceeds the 100 MB limit.');
        }

        // Extension check
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_VIDEO_EXTS, true)) {
            throw new RuntimeException('Invalid video type. Allowed: mp4, mov, avi.');
        }

        // MIME type check
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, ALLOWED_VIDEO_MIMES, true)) {
            throw new RuntimeException('Invalid video MIME type detected.');
        }

        return self::moveFile($file['tmp_name'], $dir, $ext);
    }

    /**
     * Generate a unique name and move the temp file to destination.
     *
     * @param string $tmpPath  Temp file path
     * @param string $dir      Target directory
     * @param string $ext      File extension
     * @return string          Generated filename (e.g., "abc123def.jpg")
     */
    private static function moveFile(string $tmpPath, string $dir, string $ext): string
    {
        // Ensure target directory exists
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Generate a cryptographically safe unique filename
        $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
        $destPath = $dir . '/' . $fileName;

        if (!move_uploaded_file($tmpPath, $destPath)) {
            throw new RuntimeException('Failed to save uploaded file. Please try again.');
        }

        // Prevent script execution inside upload dirs
        self::writeHtaccessToDir($dir);

        return $fileName;
    }

    /**
     * Check for PHP upload errors.
     *
     * @param array $file
     */
    private static function checkUploadError(array $file): void
    {
        if (!isset($file['error'])) {
            throw new RuntimeException('Invalid file upload data.');
        }

        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'File exceeds server maximum upload size.',
            UPLOAD_ERR_FORM_SIZE  => 'File exceeds form maximum upload size.',
            UPLOAD_ERR_PARTIAL    => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload directory.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the upload.',
        ];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $msg = $errors[$file['error']] ?? 'Unknown upload error.';
            throw new RuntimeException($msg);
        }
    }

    /**
     * Write an .htaccess file to restrict script execution in upload dirs.
     * Only written once per directory.
     *
     * @param string $dir
     */
    private static function writeHtaccessToDir(string $dir): void
    {
        $htaccess = $dir . '/.htaccess';
        if (!file_exists($htaccess)) {
            $content = "Options -ExecCGI\n" .
                       "AddHandler cgi-script .php .pl .py .sh\n" .
                       "<FilesMatch \"\\.(php|php5|php7|php8|phtml|pl|py|jsp|asp|sh|cgi)$\">\n" .
                       "    Order Deny,Allow\n" .
                       "    Deny from all\n" .
                       "</FilesMatch>\n";
            file_put_contents($htaccess, $content);
        }
    }

    /**
     * Check if a file was actually submitted (non-empty upload).
     *
     * @param array|null $file $_FILES element
     * @return bool
     */
    public static function hasFile(?array $file): bool
    {
        return !empty($file) && $file['error'] === UPLOAD_ERR_OK && $file['size'] > 0;
    }

    /**
     * Delete a file from disk safely.
     *
     * @param string $filePath Full path to file
     */
    public static function delete(string $filePath): void
    {
        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }
}
