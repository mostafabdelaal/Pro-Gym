<?php

namespace ProGym\Repository;

use mysqli;

final class PasswordResetRepository
{
    public function __construct(private mysqli $conn)
    {
    }

    public function deleteUnusedForMember(int $memberId): void
    {
        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE member_id = ? AND used_at IS NULL");
        $stmt->bind_param('i', $memberId);
        $stmt->execute();
        $stmt->close();
    }

    public function create(int $memberId, string $tokenHash): void
    {
        $stmt = $this->conn->prepare(
            "INSERT INTO password_resets (member_id, token_hash, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))"
        );
        $stmt->bind_param('is', $memberId, $tokenHash);
        $stmt->execute();
        $stmt->close();
    }

    /** Valid (unused, unexpired) reset row for a token hash, or null. */
    public function findValid(string $tokenHash): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT id, member_id FROM password_resets
             WHERE token_hash = ? AND used_at IS NULL AND expires_at > NOW()
             LIMIT 1"
        );
        $stmt->bind_param('s', $tokenHash);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    public function markUsed(int $resetId): void
    {
        $stmt = $this->conn->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?");
        $stmt->bind_param('i', $resetId);
        $stmt->execute();
        $stmt->close();
    }
}
