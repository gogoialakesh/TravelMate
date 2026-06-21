<?php
/**
 * TravelMate - Security Helper
 *
 * Provides:
 * - CSRF token generation and validation
 * - XSS output escaping
 * - Session management helpers
 * - Authentication guards
 */

class Security
{
    // --------------------------------------------------------
    // CSRF Protection
    // --------------------------------------------------------

    /**
     * Generate a CSRF token and store it in the session.
     * Returns the token string.
     */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Return a hidden HTML input field with the current CSRF token.
     */
    public static function csrfField(): string
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Validate the CSRF token submitted with a form.
     * Terminates with 403 if token is invalid.
     */
    public static function validateCsrf(): void
    {
        $submitted = $_POST['csrf_token'] ?? '';
        $stored    = $_SESSION['csrf_token'] ?? '';

        if (empty($submitted) || empty($stored) || !hash_equals($stored, $submitted)) {
            http_response_code(403);
            die('Invalid CSRF token. Please refresh the page and try again.');
        }
    }

    // --------------------------------------------------------
    // XSS Protection
    // --------------------------------------------------------

    /**
     * Escape a string for safe HTML output.
     * Always use this when echoing user-supplied data.
     *
     * @param mixed $value
     * @return string
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    // --------------------------------------------------------
    // Session Helpers
    // --------------------------------------------------------

    /**
     * Start the PHP session with secure configuration.
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Secure cookie parameters
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,    // Set true in production with HTTPS
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
            session_name(SESSION_NAME);
            session_start();
        }
    }

    /**
     * Regenerate session ID — call on privilege escalation (login).
     */
    public static function regenerateSession(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Destroy the session completely (logout).
     */
    public static function destroySession(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    // --------------------------------------------------------
    // Authentication Checks
    // --------------------------------------------------------

    /**
     * Check whether a user is currently logged in.
     */
    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Redirect to login if the user is not authenticated.
     * Call at the top of any protected page/controller.
     */
    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Redirect to dashboard if already logged in.
     * Call on login/register pages.
     */
    public static function requireGuest(): void
    {
        if (self::isLoggedIn()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    }

    /**
     * Get the current logged-in user's ID.
     */
    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * Get the current logged-in user's name.
     */
    public static function userName(): string
    {
        return $_SESSION['user_name'] ?? '';
    }

    // --------------------------------------------------------
    // Flash Messages
    // --------------------------------------------------------

    /**
     * Set a one-time flash message.
     *
     * @param string $type  'success' | 'error' | 'warning' | 'info'
     * @param string $message
     */
    public static function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    /**
     * Retrieve and clear all flash messages.
     *
     * @return array
     */
    public static function getFlash(): array
    {
        $flash = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flash;
    }

    // --------------------------------------------------------
    // Input Sanitization
    // --------------------------------------------------------

    /**
     * Sanitize a single string input.
     * Strips tags and trims whitespace.
     *
     * @param string $input
     * @return string
     */
    public static function sanitize(string $input): string
    {
        return trim(strip_tags($input));
    }

    /**
     * Sanitize an integer input.
     *
     * @param mixed $input
     * @return int
     */
    public static function sanitizeInt(mixed $input): int
    {
        return (int) filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
}
