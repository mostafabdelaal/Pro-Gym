<?php
// Controller: consume a reset token and set a new password.
require_once __DIR__ . '/../includes/auth.php';

use ProGym\Service\PasswordResetService;

csrf_verify();

$token       = trim($_POST['token'] ?? '');
$newPassword = $_POST['password'] ?? '';

$result = app('reset')->reset($token, $newPassword);

switch ($result) {
    case PasswordResetService::OK:
        header('Location: ../pages/LoginPage.php?reset=1');
        exit();
    case PasswordResetService::WEAK_PASSWORD:
        header('Location: ../pages/ResetPassword.php?token=' . urlencode($token) . '&error=weak_password');
        exit();
    case PasswordResetService::INVALID:
        // Empty token -> invalid link; otherwise a used/expired/unknown token.
        $code = $token === '' ? 'reset_invalid' : 'reset_expired';
        header('Location: ../pages/LoginPage.php?error=' . $code);
        exit();
    default:
        header('Location: ../pages/ResetPassword.php?token=' . urlencode($token) . '&error=server');
        exit();
}
