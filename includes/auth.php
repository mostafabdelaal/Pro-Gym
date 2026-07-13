<?php
// Shared session, CSRF, authentication, and member helpers.
// Include this at the top of every page/handler that needs a session or the DB.

require_once __DIR__ . '/../config/database.php'; // provides $conn (mysqli)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/** Return the shared mysqli connection. */
function db(): mysqli
{
    global $conn;
    return $conn;
}

/* --------------------------------------------------------------------------
 * CSRF protection
 * ------------------------------------------------------------------------ */

/** Get (or lazily create) the per-session CSRF token. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Hidden <input> to drop inside any POST form. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

/** Abort the request unless a valid CSRF token was posted. Call in handlers. */
function csrf_verify(): void
{
    $sent = $_POST['csrf_token'] ?? '';
    if (!is_string($sent) || !hash_equals($_SESSION['csrf_token'] ?? '', $sent)) {
        http_response_code(400);
        exit('Invalid or missing CSRF token. Please reload the page and try again.');
    }
}

/* --------------------------------------------------------------------------
 * Authentication / authorization
 * ------------------------------------------------------------------------ */

/** Redirect to login if no session; otherwise return the logged-in email. */
function require_login(): string
{
    if (empty($_SESSION['email'])) {
        header('Location: LoginPage.php');
        exit();
    }
    return $_SESSION['email'];
}

/**
 * Fetch the logged-in member joined to their current plan code.
 * Returns null when not logged in or the member no longer exists.
 */
function current_member(mysqli $conn): ?array
{
    if (empty($_SESSION['email'])) {
        return null;
    }
    $sql = "SELECT m.*, p.code AS plan
            FROM members m
            LEFT JOIN subscriptions s
                   ON s.member_id = m.id AND s.status IN ('active', 'pending')
            LEFT JOIN plans p ON p.id = s.plan_id
            WHERE m.email = ?
            ORDER BY (s.status = 'active') DESC, s.created_at DESC
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $_SESSION['email']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

/** Require an authenticated member with the admin role, or 403. */
function require_admin(mysqli $conn): array
{
    require_login();
    $member = current_member($conn);
    if (!$member || ($member['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Forbidden — admin access required.');
    }
    return $member;
}

/* --------------------------------------------------------------------------
 * Presentation helper — DRYs the name/initials/plan block the sidebar pages
 * used to duplicate (and repairs the old first_Name/first_name casing hack).
 * ------------------------------------------------------------------------ */
function member_display(?array $m): array
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
