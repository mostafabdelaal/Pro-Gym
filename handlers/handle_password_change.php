<?php
// Step 2 of password reset: consume a valid token and set a new hashed password.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();

csrf_verify();

$token       = trim($_POST['token'] ?? '');
$newPassword = $_POST['password'] ?? '';

if ($token === '' || $newPassword === '') {
    header('Location: ../pages/LoginPage.php?error=reset_invalid');
    exit();
}
if (strlen($newPassword) < 8) {
    header('Location: ../pages/ResetPassword.php?token=' . urlencode($token) . '&error=weak_password');
    exit();
}

$tokenHash = hash('sha256', $token);

$stmt = $conn->prepare(
    "SELECT id, member_id FROM password_resets
     WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()
     LIMIT 1"
);
$stmt->bind_param('s', $tokenHash);
$stmt->execute();
$reset = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$reset) {
    header('Location: ../pages/LoginPage.php?error=reset_expired');
    exit();
}

$resetId  = (int) $reset['id'];
$memberId = (int) $reset['member_id'];
$hash     = password_hash($newPassword, PASSWORD_DEFAULT);

$conn->begin_transaction();
try {
    $upd = $conn->prepare(
        "UPDATE members SET password_hash = ?, must_change_password = 0 WHERE id = ?"
    );
    $upd->bind_param('si', $hash, $memberId);
    $upd->execute();
    $upd->close();

    $use = $conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
    $use->bind_param('i', $resetId);
    $use->execute();
    $use->close();

    // Kill any other outstanding tokens for this member.
    $clr = $conn->prepare("DELETE FROM password_resets WHERE member_id = ? AND used_at IS NULL");
    $clr->bind_param('i', $memberId);
    $clr->execute();
    $clr->close();

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    error_log('Password reset failed: ' . $e->getMessage());
    header('Location: ../pages/ResetPassword.php?token=' . urlencode($token) . '&error=server');
    exit();
}

header('Location: ../pages/LoginPage.php?reset=1');
exit();
