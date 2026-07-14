<?php

namespace ProGym\Repository;

use mysqli;

final class MemberRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, email, password_hash, role FROM members WHERE email = ?"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Member row joined to their current plan code (active preferred, else pending). */
    public function findByEmailWithPlan(string $email): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT m.*, p.code AS plan
             FROM members m
             LEFT JOIN subscriptions s
                    ON s.member_id = m.id AND s.status IN ('active','pending')
             LEFT JOIN plans p ON p.id = s.plan_id
             WHERE m.email = ?
             ORDER BY (s.status = 'active') DESC, s.created_at DESC
             LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** @return int new member id */
    public function create(
        string $first,
        string $last,
        string $email,
        string $phone,
        ?string $birthDate,
        string $passwordHash
    ): int {
        $stmt = $this->conn->prepare(
            "INSERT INTO members (first_name, last_name, email, phone, birth_date, password_hash)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('ssssss', $first, $last, $email, $phone, $birthDate, $passwordHash);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function updatePasswordHash(int $memberId, string $hash): void
    {
        $stmt = $this->conn->prepare(
            "UPDATE members SET password_hash = ?, must_change_password = 0 WHERE id = ?"
        );
        $stmt->bind_param('si', $hash, $memberId);
        $stmt->execute();
        $stmt->close();
    }

    /** Admin roster: every member with branch name + current plan code. */
    public function allWithBranchAndPlan(): array
    {
        $sql = "SELECT m.id, m.first_name, m.last_name, m.email, m.created_at,
                       b.name AS branch,
                       (SELECT p.code FROM subscriptions s
                          JOIN plans p ON p.id = s.plan_id
                         WHERE s.member_id = m.id AND s.status IN ('active','pending')
                         ORDER BY (s.status = 'active') DESC, s.created_at DESC
                         LIMIT 1) AS plan
                FROM members m
                LEFT JOIN branches b ON b.id = m.home_branch_id
                ORDER BY m.created_at DESC";
        $res = $this->conn->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
}
