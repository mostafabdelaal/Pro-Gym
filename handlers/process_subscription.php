<?php
// Record a member's plan choice as a pending subscription, then go to checkout.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();

require_login();
csrf_verify();

$member = current_member($conn);
if (!$member) {
    header('Location: ../pages/LoginPage.php');
    exit();
}

$planCode = strtoupper(trim($_POST['plan'] ?? ''));

$stmt = $conn->prepare("SELECT id FROM plans WHERE code = ? AND is_active = 1");
$stmt->bind_param('s', $planCode);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$plan) {
    header('Location: ../pages/Packages.php?error=invalid_plan');
    exit();
}

$memberId = (int) $member['id'];
$planId   = (int) $plan['id'];

// Drop any stale pending choice, then record the new one.
$del = $conn->prepare("DELETE FROM subscriptions WHERE member_id = ? AND status = 'pending'");
$del->bind_param('i', $memberId);
$del->execute();
$del->close();

$ins = $conn->prepare(
    "INSERT INTO subscriptions (member_id, plan_id, billing_interval, status)
     VALUES (?, ?, 'monthly', 'pending')"
);
$ins->bind_param('ii', $memberId, $planId);
$ins->execute();
$ins->close();

header('Location: ../pages/Payment.php');
exit();
