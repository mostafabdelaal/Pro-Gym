<?php
// Step 1 of password reset: issue a one-time token for a given email.
// No email is sent (dev mode) — the link is shown back on the Forget page when
// the account exists. The token is stored only as a SHA-256 hash.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();

csrf_verify();

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);

// Always respond the same way; only generate a link when the member exists.
$_SESSION['reset_requested'] = true;
unset($_SESSION['dev_reset_link']);

if ($email !== false) {
    $stmt = $conn->prepare("SELECT id FROM members WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $member = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($member) {
        $memberId  = (int) $member['id'];
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        // Invalidate previous unused tokens for this member.
        $del = $conn->prepare("DELETE FROM password_resets WHERE member_id = ? AND used_at IS NULL");
        $del->bind_param('i', $memberId);
        $del->execute();
        $del->close();

        $ins = $conn->prepare(
            "INSERT INTO password_resets (member_id, token_hash, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))"
        );
        $ins->bind_param('is', $memberId, $tokenHash);
        $ins->execute();
        $ins->close();

        // Build an absolute reset link for the current host.
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? '127.0.0.1:8000';
        $link   = $scheme . '://' . $host . '/pages/ResetPassword.php?token=' . urlencode($token);

        // Dev delivery: surface the link to the requester + log it.
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
