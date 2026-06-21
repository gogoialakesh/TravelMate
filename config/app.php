<?php
/**
 * TravelMate - Application Configuration
 *
 * Central configuration constants for the application.
 * Load this first before any other includes.
 */

// ============================================================
// Environment Detection
// ============================================================
define('APP_ENV', 'development'); // 'development' | 'production'

// ============================================================
// Base URL — adjust if running in a sub-directory
// ============================================================
define('BASE_URL', 'http://localhost/travelmate');

// ============================================================
// Application Root Paths
// ============================================================
define('ROOT_PATH',    dirname(__DIR__));
define('APP_PATH',     ROOT_PATH . '/app');
define('CONFIG_PATH',  ROOT_PATH . '/config');
define('PUBLIC_PATH',  ROOT_PATH . '/public');
define('ROUTES_PATH',  ROOT_PATH . '/routes');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('VIEWS_PATH',   APP_PATH  . '/views');

// ============================================================
// Upload Directories
// ============================================================
define('UPLOAD_PROFILES',    UPLOADS_PATH . '/profiles');
define('UPLOAD_TRIPS',       UPLOADS_PATH . '/trips');
define('UPLOAD_ALBUMS_IMG',  UPLOADS_PATH . '/albums/images');
define('UPLOAD_ALBUMS_VID',  UPLOADS_PATH . '/albums/videos');
define('UPLOAD_ATTACHMENTS', UPLOADS_PATH . '/attachments');

// ============================================================
// Upload Limits
// ============================================================
define('MAX_IMAGE_SIZE', 10 * 1024 * 1024);    // 10 MB
define('MAX_VIDEO_SIZE', 100 * 1024 * 1024);   // 100 MB

// ============================================================
// Allowed File Types
// ============================================================
define('ALLOWED_IMAGE_EXTS',  ['jpg', 'jpeg', 'png', 'webp']);
define('ALLOWED_IMAGE_MIMES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_VIDEO_EXTS',  ['mp4', 'mov', 'avi']);
define('ALLOWED_VIDEO_MIMES', ['video/mp4', 'video/quicktime', 'video/x-msvideo']);

// ============================================================
// Session Configuration
// ============================================================
define('SESSION_LIFETIME', 7200);      // 2 hours in seconds
define('SESSION_NAME',     'tm_sess'); // Custom session name

// ============================================================
// CSRF
// ============================================================
define('CSRF_TOKEN_LENGTH', 32);

// ============================================================
// Pagination
// ============================================================
define('TRIPS_PER_PAGE',    12);
define('MESSAGES_PER_PAGE', 50);
define('MEDIA_PER_PAGE',    20);

// ============================================================
// Application Name
// ============================================================
define('APP_NAME', 'TravelMate');

// ============================================================
// Log File
// ============================================================
define('LOG_FILE', STORAGE_PATH . '/logs/app.log');

// ============================================================
// Error Reporting (disable in production)
// ============================================================
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
