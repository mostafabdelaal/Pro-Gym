<?php

namespace ProGym\Service;

use ProGym\Repository\MemberRepository;
use ProGym\Repository\PasswordResetRepository;
use mysqli;
use Throwable;

final class PasswordResetService
{
    public const OK = 'ok';
    public const INVALID = 'invalid';
    public const WEAK_PASSWORD = 'weak_password';
    public const ERROR = 'server';

    public function __construct(
        private MemberRepository $members,
        private PasswordResetRepository $resets,
        private mysqli $conn
    ) {
    }

    /**
     * Issue a reset token for an email. Returns the absolute reset link when the
     * member exists, or null otherwise (caller must not reveal which).
     */
    public function requestFor(string $email, string $baseUrl): ?string
    {
        $member = $this->members->findByEmail($email);
        if (!$member) {
            return null;
        }
        $memberId = (int) $member['id'];
        $token    = bin2hex(random_bytes(32));

        $this->resets->deleteUnusedForMember($memberId);
        $this->resets->create($memberId, hash('sha256', $token));

        return rtrim($baseUrl, '/') . '/pages/ResetPassword.php?token=' . urlencode($token);
    }

    /** Consume a token and set a new password. Returns a class constant. */
    public function reset(string $token, string $newPassword): string
    {
        if ($token === '' || $newPassword === '') {
            return self::INVALID;
        }
        if (strlen($newPassword) < 8) {
            return self::WEAK_PASSWORD;
        }
        $row = $this->resets->findValid(hash('sha256', $token));
        if (!$row) {
            return self::INVALID;
        }

        $this->conn->begin_transaction();
        try {
            $this->members->updatePasswordHash((int) $row['member_id'], password_hash($newPassword, PASSWORD_DEFAULT));
            $this->resets->markUsed((int) $row['id']);
            $this->resets->deleteUnusedForMember((int) $row['member_id']);
            $this->conn->commit();
            return self::OK;
        } catch (Throwable $e) {
            $this->conn->rollback();
            error_log('Password reset failed: ' . $e->getMessage());
            return self::ERROR;
        }
    }
}
