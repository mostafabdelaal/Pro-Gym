<?php

namespace ProGym\Service;

use ProGym\Repository\MemberRepository;

final class AuthService
{
    public function __construct(private MemberRepository $members)
    {
    }

    /** @return array|null the member row on success, null on bad credentials */
    public function attempt(string $email, string $password): ?array
    {
        $user = $this->members->findByEmail($email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }
        return $user;
    }
}
