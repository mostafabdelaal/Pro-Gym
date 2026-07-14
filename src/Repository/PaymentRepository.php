<?php

namespace ProGym\Repository;

use mysqli;

final class PaymentRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    /** Record a paid payment. Stores last-4 + token only — never PAN/CVV. */
    public function createPaid(
        int $memberId,
        int $subscriptionId,
        float $amount,
        float $vat,
        float $total,
        string $last4,
        string $token
    ): void {
        $stmt = $this->conn->prepare(
            "INSERT INTO payments
                (member_id, subscription_id, amount, vat, total, currency,
                 card_last4, card_brand, provider_token, status, paid_at)
             VALUES (?, ?, ?, ?, ?, 'EGP', ?, 'card', ?, 'paid', NOW())"
        );
        $stmt->bind_param('iiddsss', $memberId, $subscriptionId, $amount, $vat, $total, $last4, $token);
        $stmt->execute();
        $stmt->close();
    }
}
