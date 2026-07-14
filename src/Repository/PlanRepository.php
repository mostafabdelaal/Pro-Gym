<?php

namespace ProGym\Repository;

use mysqli;

final class PlanRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    public function findActiveByCode(string $code): ?array
    {
        $stmt = $this->conn->prepare("SELECT id, code, monthly_price FROM plans WHERE code = ? AND is_active = 1");
        $stmt->bind_param('s', $code);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** All active plans, each with a 'features' array. Two queries, grouped in PHP. */
    public function allActiveWithFeatures(): array
    {
        $plansRes = $this->conn->query(
            "SELECT id, code, monthly_price, is_popular FROM plans WHERE is_active = 1 ORDER BY sort_order, id"
        );
        if (!$plansRes || $plansRes->num_rows === 0) {
            return [];
        }

        $featuresByPlan = [];
        $featRes = $this->conn->query(
            "SELECT plan_id, feature FROM plan_features ORDER BY plan_id, sort_order, id"
        );
        if ($featRes) {
            while ($f = $featRes->fetch_assoc()) {
                $featuresByPlan[(int) $f['plan_id']][] = $f['feature'];
            }
        }

        $plans = [];
        while ($p = $plansRes->fetch_assoc()) {
            $pid = (int) $p['id'];
            $plans[] = [
                'name'     => $p['code'],
                'price'    => (float) $p['monthly_price'],
                'popular'  => (bool) $p['is_popular'],
                'features' => $featuresByPlan[$pid] ?? [],
            ];
        }
        return $plans;
    }
}
