<?php

namespace ProGym\Support;

/** Per-session CSRF token helpers. */
final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="'
            . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    /** Abort the request unless a valid token was posted. */
    public static function verify(): void
    {
        $sent = $_POST['csrf_token'] ?? '';
        if (!is_string($sent) || !hash_equals($_SESSION['csrf_token'] ?? '', $sent)) {
            http_response_code(400);
            exit('Invalid or missing CSRF token. Please reload the page and try again.');
        }
    }
}
