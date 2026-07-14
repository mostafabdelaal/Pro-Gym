<?php

namespace ProGym\Service;

use ProGym\Repository\MemberRepository;
use mysqli_sql_exception;

final class RegistrationService
{
    public const OK = 'ok';
    public const INVALID = 'invalid';
    public const WEAK_PASSWORD = 'weak_password';
    public const EMAIL_TAKEN = 'email_taken';
    public const ERROR = 'server';

    public function __construct(private MemberRepository $members)
    {
    }

    /** @param array $in raw $_POST data. @return string one of the class constants. */
    public function register(array $in): string
    {
        $first = trim($in['first_Name'] ?? '');
        $last  = trim($in['last_Name'] ?? '');
        $email = filter_var(trim($in['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $phone = trim($in['phone'] ?? '');
        $birth = trim($in['birth_date'] ?? '');
        $password = $in['password'] ?? '';

        if ($first === '' || $last === '' || $email === false || $password === '') {
            return self::INVALID;
        }
        if (strlen($password) < 8) {
            return self::WEAK_PASSWORD;
        }

        $birthValue = $birth !== '' ? $birth : null;
        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $this->members->create($first, $last, $email, $phone, $birthValue, $hash);
            return self::OK;
        } catch (mysqli_sql_exception $e) {
            if ((int) $e->getCode() === 1062) {
                return self::EMAIL_TAKEN;
            }
            error_log('Registration failed: ' . $e->getMessage());
            return self::ERROR;
        }
    }
}
