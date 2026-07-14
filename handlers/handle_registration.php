<?php
// Controller: register a new member.
require_once __DIR__ . '/../includes/auth.php';

use ProGym\Service\RegistrationService;

csrf_verify();

$result = app('registration')->register($_POST);

switch ($result) {
    case RegistrationService::OK:
        header('refresh:2;url=../pages/LoginPage.php');
        echo 'Registration complete. Redirecting to the login page…';
        exit();
    case RegistrationService::EMAIL_TAKEN:
        header('Location: ../pages/Register.php?error=email_taken');
        exit();
    case RegistrationService::WEAK_PASSWORD:
        header('Location: ../pages/Register.php?error=weak_password');
        exit();
    case RegistrationService::INVALID:
        header('Location: ../pages/Register.php?error=invalid');
        exit();
    default:
        header('Location: ../pages/Register.php?error=server');
        exit();
}
