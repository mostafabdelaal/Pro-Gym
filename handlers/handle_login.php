<?php
// Authenticate a member. Prepared statement + hashed password + CSRF.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();

csrf_verify();

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    header('Location: ../pages/LoginPage.php?error=missing');
    exit();
}

$stmt = $conn->prepare(
    "SELECT id, email, password_hash, role FROM members WHERE email = ?"
);
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    header('Location: ../pages/LoginPage.php?error=email_not_found');
    exit();
}

if (!password_verify($password, $user['password_hash'])) {
    header('Location: ../pages/LoginPage.php?error=incorrect_password');
    exit();
}

// Prevent session fixation: issue a fresh session id on privilege change.
session_regenerate_id(true);
$_SESSION['email']     = $user['email'];
$_SESSION['member_id'] = (int) $user['id'];
$_SESSION['role']      = $user['role'];

header('Location: ../pages/Dashboard.php?welcome=1');
exit();
