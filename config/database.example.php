<?php
// Template for config/database.php (which is gitignored).
// Copy this file to config/database.php and fill in your local credentials.
//
// On the reference XAMPP box MariaDB listens on port 3307. Default XAMPP is 3306
// — set 'port' to match your `my.ini` [mysqld] port.
return [
    'host' => '127.0.0.1',
    'user' => 'root',
    'pass' => '',          // your local MySQL password
    'name' => 'gymster',
    'port' => 3307,
];
