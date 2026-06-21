<?php
/**
 * TravelMate - Database Configuration
 *
 * PDO connection singleton.
 * All database interactions must go through this connection.
 * Never use mysqli.
 */

class Database
{
    // --------------------------------------------------------
    // Database credentials — update for your environment
    // --------------------------------------------------------
    private static string $host     = '127.0.0.1';
    private static string $dbName   = 'travelmate_db';
    private static string $user     = 'root';
    private static string $password = '';
    private static string $charset  = 'utf8mb4';

    /** @var PDO|null Singleton instance */
    private static ?PDO $instance = null;

    /**
     * Private constructor — prevents direct instantiation.
     */
    private function __construct() {}

    /**
     * Returns the singleton PDO connection.
     *
     * @return PDO
     * @throws RuntimeException on connection failure
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::$host,
                self::$dbName,
                self::$charset
            );

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            ];

            try {
                self::$instance = new PDO($dsn, self::$user, self::$password, $options);
            } catch (PDOException $e) {
                // Log the real error; show generic message to user
                error_log('[TravelMate DB Error] ' . $e->getMessage());
                throw new RuntimeException('Database connection failed. Please try again later.');
            }
        }

        return self::$instance;
    }

    /**
     * Prevent cloning of the singleton.
     */
    private function __clone() {}
}
