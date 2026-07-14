<?php

namespace ProGym\Repository;

use mysqli;

final class SubscriptionRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    public function deletePendingForMember(int $memberId): void
    {
        $stmt = $this->conn->prepare("DELETE FROM subscriptions WHERE member_id = ? AND status = 'pending'");
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $stmt->close();
    }

    public function createPending(int $memberId, int $planId): int
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO subscriptions (member_id, plan_id, billing_interval, status)
             VALUES (?, ?, 'monthly', 'pending')"
        );
        $stmt->bind_param('ii', $memberId, $planId);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    /** Latest pending subscription for a member with its plan price, or null. */
    public function pendingWithPrice(int $memberId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT s.id AS subscription_id, p.monthly_price
             FROM subscriptions s JOIN plans p ON p.id = s.plan_id
             WHERE s.member_id = ? AND s.status = 'pending'
             ORDER BY s.created_at DESC LIMIT 1"
        );
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Selected plan for the checkout summary: pending preferred, else latest. */
    public function selectedPlanForCheckout(int $memberId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT p.code, p.monthly_price
             FROM subscriptions s JOIN plans p ON p.id = s.plan_id
             WHERE s.member_id = ?
             ORDER BY (s.status = 'pending') DESC, s.created_at DESC
             LIMIT 1"
        );
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function activate(int $subscriptionId): void
    {
        $stmt = $this->conn->prepare(
            "UPDATE subscriptions
             SET status = 'active', started_at = CURDATE(),
                 expires_at = DATE_ADD(CURDATE(), INTERVAL 1 MONTH)
             WHERE id = ?"
        );
        $stmt->bind_param('i', $subscriptionId);
        $stmt->execute();
        $stmt->close();
    }
}
