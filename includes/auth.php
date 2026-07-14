<?php
// Composition root + backward-compatible helper shims.
//
// Boots the autoloader/session, wires the repository + service layer with a
// tiny lazy container (app()), and exposes the thin procedural helpers the
// pages and handlers already call. New code should prefer app('...') services.

require_once __DIR__ . '/bootstrap.php';

use ProGym\Config\Database;
use ProGym\Repository\MemberRepository;
use ProGym\Repository\PlanRepository;
use ProGym\Repository\SubscriptionRepository;
use ProGym\Repository\PaymentRepository;
use ProGym\Repository\PasswordResetRepository;
use ProGym\Repository\BranchRepository;
use ProGym\Repository\TrainerRepository;
use ProGym\Service\AuthService;
use ProGym\Service\RegistrationService;
use ProGym\Service\SubscriptionService;
use ProGym\Service\PaymentService;
use ProGym\Service\PasswordResetService;
use ProGym\Support\Auth;
use ProGym\Support\Csrf;

/**
 * Lazy service container. Each key is built once and cached.
 * Keys: conn, members, plans, subscriptions, payments, resets,
 *       auth, authService, registration, subscription, payment, reset.
 */
function app(string $key)
{
    static $c = [];
    if (array_key_exists($key, $c)) {
        return $c[$key];
    }

    $conn      = Database::connection();
    $members   = static fn() => new MemberRepository($conn);
    $plans     = static fn() => new PlanRepository($conn);
    $subs      = static fn() => new SubscriptionRepository($conn);
    $payments  = static fn() => new PaymentRepository($conn);
    $resets    = static fn() => new PasswordResetRepository($conn);

    $build = [
        'conn'         => static fn() => $conn,
        'members'      => $members,
        'plans'        => $plans,
        'subscriptions'=> $subs,
        'payments'     => $payments,
        'resets'       => $resets,
        'branches'     => static fn() => new BranchRepository($conn),
        'trainers'     => static fn() => new TrainerRepository($conn),
        'auth'         => static fn() => new Auth($members()),
        'authService'  => static fn() => new AuthService($members()),
        'registration' => static fn() => new RegistrationService($members()),
        'subscription' => static fn() => new SubscriptionService($plans(), $subs()),
        'payment'      => static fn() => new PaymentService($subs(), $payments(), $conn),
        'reset'        => static fn() => new PasswordResetService($members(), $resets(), $conn),
    ];

    if (!isset($build[$key])) {
        throw new InvalidArgumentException("Unknown service: $key");
    }
    return $c[$key] = $build[$key]();
}

/* ---- Backward-compatible procedural helpers (used across the pages) ------- */

function db(): mysqli
{
    return app('conn');
}

function csrf_token(): string
{
    return Csrf::token();
}

function csrf_field(): string
{
    return Csrf::field();
}

function csrf_verify(): void
{
    Csrf::verify();
}

function require_login(): string
{
    return app('auth')->requireLogin();
}

// The optional $conn arg is ignored — kept so existing call sites still work.
function current_member(?mysqli $conn = null): ?array
{
    return app('auth')->currentMember();
}

function require_admin(?mysqli $conn = null): array
{
    return app('auth')->requireAdmin();
}

function member_display(?array $m): array
{
    return Auth::display($m);
}
