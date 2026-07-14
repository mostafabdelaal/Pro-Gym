<?php

namespace ProGym\Config;

use mysqli;

/**
 * Lazy singleton mysqli connection built from config/database.php credentials.
 */
final class Database
{
    private static ?mysqli $conn = null;

    public static function connection(): mysqli
    {
        if (self::$conn instanceof mysqli) {
            return self::$conn;
        }

        $creds = require dirname(__DIR__, 2) . '/config/database.php';
        $conn = @new mysqli(
            $creds['host'],
            $creds['user'],
            $creds['pass'],
            $creds['name'],
            (int) ($creds['port'] ?? 3306)
        );

        if ($conn->connect_error) {
            error_log('DB connection failed: ' . $conn->connect_error);
            http_response_code(500);
            exit('Service temporarily unavailable.');
        }
        $conn->set_charset('utf8mb4');

        return self::$conn = $conn;
    }
}
