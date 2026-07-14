<?php
// Controller: process a (simulated) payment for the member's pending subscription.
// The service stores only card_last4 + a token — never the PAN, expiry, or CVV.
require_once __DIR__ . '/../includes/auth.php';

use ProGym\Service\PaymentService;

require_login();
csrf_verify();

$member = current_member();
if (!$member) {
    header('Location: ../pages/LoginPage.php');
    exit();
}

$cardNumber = implode('', [
    $_POST['card_id1'] ?? '', $_POST['card_id2'] ?? '',
    $_POST['card_id3'] ?? '', $_POST['card_id4'] ?? '',
]);
// Expiry and CVV are intentionally never forwarded to storage.
unset($_POST['cvv'], $_POST['expiry']);

$result = app('payment')->pay((int) $member['id'], $cardNumber);

switch ($result) {
    case PaymentService::OK:
        header('Location: ../pages/confirmation.php');
        exit();
    case PaymentService::BAD_CARD:
        header('Location: ../pages/Payment.php?error=card');
        exit();
    case PaymentService::NO_PLAN:
        header('Location: ../pages/Packages.php?error=no_plan');
        exit();
    default:
        header('Location: ../pages/Payment.php?error=server');
        exit();
}
