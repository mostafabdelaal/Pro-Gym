<?php
// Process a (simulated) payment for the member's pending subscription.
// SECURITY: never store the full card number, expiry, or CVV. Only the last 4
// digits and an opaque processor token are persisted.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();

require_login();
csrf_verify();

$member = current_member($conn);
if (!$member) {
    header('Location: ../pages/LoginPage.php');
    exit();
}
$memberId = (int) $member['id'];

// Reconstruct the card number only to derive last4 + basic validation.
$digits = preg_replace('/\D/', '', implode('', [
    $_POST['card_id1'] ?? '', $_POST['card_id2'] ?? '',
    $_POST['card_id3'] ?? '', $_POST['card_id4'] ?? '',
]));
if (strlen($digits) < 12 || strlen($digits) > 19) {
    header('Location: ../pages/Payment.php?error=card');
    exit();
}
$last4 = substr($digits, -4);
// The full PAN, expiry and CVV are intentionally discarded here.
unset($digits, $_POST['cvv'], $_POST['expiry']);

// Find the member's pending subscription and its price — server-side, never trust the client.
$stmt = $conn->prepare(
    "SELECT s.id AS subscription_id, p.monthly_price
     FROM subscriptions s
     JOIN plans p ON p.id = s.plan_id
     WHERE s.member_id = ? AND s.status = 'pending'
     ORDER BY s.created_at DESC
     LIMIT 1"
);
$stmt->bind_param('i', $memberId);
$stmt->execute();
$sub = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$sub) {
    header('Location: ../pages/Packages.php?error=no_plan');
    exit();
}

$subscriptionId = (int) $sub['subscription_id'];
$amount = (float) $sub['monthly_price'];
$vat    = round($amount * 0.14, 2);
$total  = $amount + $vat;
$token  = 'SIMULATED-' . bin2hex(random_bytes(8));

$conn->begin_transaction();
try {
    $pay = $conn->prepare(
        "INSERT INTO payments
            (member_id, subscription_id, amount, vat, total, currency,
             card_last4, card_brand, provider_token, status, paid_at)
         VALUES (?, ?, ?, ?, ?, 'EGP', ?, 'card', ?, 'paid', NOW())"
    );
    $pay->bind_param('iiddsss', $memberId, $subscriptionId, $amount, $vat, $total, $last4, $token);
    $pay->execute();
    $pay->close();

    $act = $conn->prepare(
        "UPDATE subscriptions
         SET status = 'active', started_at = CURDATE(),
             expires_at = DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
         WHERE id = ?"
    );
    $act->bind_param('i', $subscriptionId);
    $act->execute();
    $act->close();

    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    error_log('Payment failed: ' . $e->getMessage());
    header('Location: ../pages/Payment.php?error=server');
    exit();
}

header('Location: ../pages/confirmation.php');
exit();
