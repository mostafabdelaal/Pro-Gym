<?php
// Register a new member. Prepared statement + hashed password + CSRF + validation.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();

csrf_verify();

$first    = trim($_POST['first_Name'] ?? '');
$last     = trim($_POST['last_Name'] ?? '');
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone    = trim($_POST['phone'] ?? '');
$birth    = trim($_POST['birth_date'] ?? '');
$password = $_POST['password'] ?? '';

if ($first === '' || $last === '' || $email === false || $password === '') {
    header('Location: ../pages/Register.php?error=invalid');
    exit();
}
if (strlen($password) < 8) {
    header('Location: ../pages/Register.php?error=weak_password');
    exit();
}

$birthValue = $birth !== '' ? $birth : null;
$hash       = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO members (first_name, last_name, email, phone, birth_date, password_hash)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param('ssssss', $first, $last, $email, $phone, $birthValue, $hash);

if ($stmt->execute()) {
    $stmt->close();
    header('refresh:2;url=../pages/LoginPage.php');
    echo 'Registration complete. Redirecting to the login page…';
    exit();
}

$duplicate = ($conn->errno === 1062);
$stmt->close();
if ($duplicate) {
    header('Location: ../pages/Register.php?error=email_taken');
    exit();
}
error_log('Registration failed: ' . $conn->error);
header('Location: ../pages/Register.php?error=server');
exit();
