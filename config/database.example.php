<?php
// Template for config/database.php (which is gitignored).
// Copy this file to config/database.php and fill in your local credentials.
//
// On the reference XAMPP box MariaDB listens on port 3307. Default XAMPP is 3306
// — set $port to match your `my.ini` [mysqld] port.
$servername = "127.0.0.1";
$username   = "root";
$password   = "";        // your local MySQL password
$database   = "gymster";
$port       = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    error_log("DB connection failed: " . $conn->connect_error);
    http_response_code(500);
    exit("Service temporarily unavailable.");
}
$conn->set_charset("utf8mb4");
