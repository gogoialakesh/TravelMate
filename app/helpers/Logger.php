<?php
/**
 * TravelMate - Application Logger
 *
 * File-based logging to storage/logs/app.log.
 * Logs errors, auth events, upload failures, etc.
 */

class Logger
{
    private static string $logFile;

    /**
     * Initialize the logger.
     * Called once during bootstrap.
     */
    public static function init(): void
    {
        self::$logFile = LOG_FILE;

        // Ensure the log directory exists
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    /**
     * Write a log entry.
     *
     * @param string $level   DEBUG | INFO | WARNING | ERROR | CRITICAL
     * @param string $message Log message
     * @param array  $context Additional context key-value pairs
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $userId    = $_SESSION['user_id'] ?? 'guest';
        $ip        = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

        $contextStr = !empty($context) ? ' | ' . json_encode($context) : '';

        $entry = sprintf(
            "[%s] [%s] [user:%s] [ip:%s] %s%s\n",
            $timestamp,
            strtoupper($level),
            $userId,
            $ip,
            $message,
            $contextStr
        );

        error_log($entry, 3, self::$logFile);
    }

    /** Convenience methods */
    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::log('CRITICAL', $message, $context);
    }

    public static function authEvent(string $event, string $email = ''): void
    {
        self::log('INFO', "AUTH: {$event}", ['email' => $email]);
    }
}
