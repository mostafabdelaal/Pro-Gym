<?php
// Controller: step 1 of password reset — issue a token for an email.
// No email is sent (dev mode); the link is surfaced on the Forget page and
// logged locally when the account exists.
require_once __DIR__ . '/../includes/auth.php';

csrf_verify();

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

$_SESSION['reset_requested'] = true;
unset($_SESSION['dev_reset_link']);

if ($email !== false) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000';
    $link   = app('reset')->requestFor($email, $scheme . '://' . $host);

    if ($link !== null) {
        $_SESSION['dev_reset_link'] = $link;
        $dir = __DIR__ . '/../storage';
        if (!is_dir($dir)) {
            @mkdir($dir, 0700, true);
        }
        @file_put_contents(
            $dir . '/reset_links.log',
            sprintf("[%s] %s -> %s\n", date('c'), $email, $link),
            FILE_APPEND | LOCK_EX
        );
    }
}

header('Location: ../pages/Forget.php?sent=1');
exit();
