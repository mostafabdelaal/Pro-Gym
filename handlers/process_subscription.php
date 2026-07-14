<?php
// Controller: record the member's plan choice as a pending subscription.
require_once __DIR__ . '/../includes/auth.php';

require_login();
csrf_verify();

$member = current_member();
if (!$member) {
    header('Location: ../pages/LoginPage.php');
    exit();
}

$ok = app('subscription')->choosePlan((int) $member['id'], $_POST['plan'] ?? '');
if (!$ok) {
    header('Location: ../pages/Packages.php?error=invalid_plan');
    exit();
}

header('Location: ../pages/Payment.php');
exit();
