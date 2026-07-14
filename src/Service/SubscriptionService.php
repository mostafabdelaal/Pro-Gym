<?php

namespace ProGym\Service;

use ProGym\Repository\PlanRepository;
use ProGym\Repository\SubscriptionRepository;

final class SubscriptionService
{
    public function __construct(
        private PlanRepository $plans,
        private SubscriptionRepository $subscriptions
    ) {
    }

    /** Record a pending subscription for the chosen plan code. False if unknown plan. */
    public function choosePlan(int $memberId, string $planCode): bool
    {
        $plan = $this->plans->findActiveByCode(strtoupper(trim($planCode)));
        if (!$plan) {
            return false;
        }
        $this->subscriptions->deletePendingForMember($memberId);
        $this->subscriptions->createPending($memberId, (int) $plan['id']);
        return true;
    }
}
