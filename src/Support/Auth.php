<?php

namespace ProGym\Support;

use ProGym\Repository\MemberRepository;

/** Session-backed authentication + the member presentation helper. */
final class Auth
{
    public function __construct(private MemberRepository $members)
    {
    }

    public function requireLogin(): string
    {
        if (empty($_SESSION['email'])) {
            header('Location: LoginPage.php');
            exit();
        }
        return $_SESSION['email'];
    }

    public function currentMember(): ?array
    {
        if (empty($_SESSION['email'])) {
            return null;
        }
        return $this->members->findByEmailWithPlan($_SESSION['email']);
    }

    public function requireAdmin(): array
    {
        $this->requireLogin();
        $member = $this->currentMember();
        if (!$member || ($member['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Forbidden — admin access required.');
        }
        return $member;
    }

    /** Derive name/initials/plan display fields from a member row. */
    public static function display(?array $m): array
    {
        $first = $m['first_name'] ?? '';
        $last  = $m['last_name'] ?? '';
        $email = $m['email'] ?? '';
        $plan  = !empty($m['plan']) ? strtoupper(trim($m['plan'])) : 'NONE';

        $full = trim($first . ' ' . $last);
        if ($full === '') {
            $full = $email;
        }
        $initials = strtoupper(substr($first, 0, 1) . substr($last, 0, 1));
        if (trim($initials) === '') {
            $initials = strtoupper(substr($email, 0, 2));
        }
        $greet = $first !== '' ? $first : (explode('@', $email)[0] ?? $email);

        return [
            'first'     => $first,
            'last'      => $last,
            'email'     => $email,
            'plan'      => $plan,
            'fullName'  => $full,
            'initials'  => $initials,
            'greetName' => $greet,
            'planLabel' => ucfirst(strtolower($plan)) . ' plan',
        ];
    }
}
