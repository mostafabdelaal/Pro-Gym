<?php
// Controller: authenticate a member.
require_once __DIR__ . '/../includes/auth.php';

csrf_verify();

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ../pages/LoginPage.php?error=missing');
    exit();
}

$user = app('authService')->attempt($email, $password);

if (!$user) {
    $exists = app('members')->findByEmail($email) !== null;
    header('Location: ../pages/LoginPage.php?error=' . ($exists ? 'incorrect_password' : 'email_not_found'));
    exit();
}

// Anti-fixation: fresh session id on privilege change.
session_regenerate_id(true);
$_SESSION['email']     = $user['email'];
$_SESSION['member_id'] = (int) $user['id'];
$_SESSION['role']      = $user['role'];

header('Location: ../pages/Dashboard.php?welcome=1');
exit();
