<?php

namespace ProGym\Service;

use ProGym\Repository\PaymentRepository;
use ProGym\Repository\SubscriptionRepository;
use mysqli;
use Throwable;

final class PaymentService
{
    public const OK = 'ok';
    public const BAD_CARD = 'card';
    public const NO_PLAN = 'no_plan';
    public const ERROR = 'server';

    public function __construct(
        private SubscriptionRepository $subscriptions,
        private PaymentRepository $payments,
        private mysqli $conn
    ) {
    }

    /**
     * Charge (simulated) the member's pending subscription.
     * Only the card's last 4 digits are derived; PAN/expiry/CVV are discarded.
     */
    public function pay(int $memberId, string $rawCardNumber): string
    {
        $digits = preg_replace('/\D/', '', $rawCardNumber);
        if (strlen($digits) < 12 || strlen($digits) > 19) {
            return self::BAD_CARD;
        }
        $last4 = substr($digits, -4);

        $sub = $this->subscriptions->pendingWithPrice($memberId);
        if (!$sub) {
            return self::NO_PLAN;
        }

        $subscriptionId = (int) $sub['subscription_id'];
        $amount = (float) $sub['monthly_price'];
        $vat    = round($amount * 0.14, 2);
        $total  = $amount + $vat;
        $token  = 'SIMULATED-' . bin2hex(random_bytes(8));

        $this->conn->begin_transaction();
        try {
            $this->payments->createPaid($memberId, $subscriptionId, $amount, $vat, $total, $last4, $token);
            $this->subscriptions->activate($subscriptionId);
            $this->conn->commit();
            return self::OK;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('Payment failed: ' . $e->getMessage());
            return self::ERROR;
        }
    }
}
