<?php
// Application bootstrap: PSR-4 autoloader (no Composer — respects the project's
// no-package-manager convention) + session start. Include this once per request.

spl_autoload_register(static function (string $class): void {
    $prefix  = 'ProGym\\';
    $baseDir = __DIR__ . '/../src/';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
