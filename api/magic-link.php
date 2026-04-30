<?php
/**
 * POST /api/magic-link
 * Body: email, _csrf, redirect?
 * Response JSON: { ok: true } | { ok: false, error: string }
 *
 * Rate limit: 3 magic-link requests per email per hour.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/csrf.php';
require_once SYNC_ROOT . '/lib/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed.'], 405);
}

if (!csrf_verify($_POST['_csrf'] ?? '')) {
    json_response(['ok' => false, 'error' => 'Session expired — please refresh and try again.'], 403);
}

// Honeypot
if (!empty($_POST['website'])) {
    error_log('[magic-link] honeypot triggered');
    json_response(['ok' => true]); // appear successful to bots
}

$email = clean_email($_POST['email'] ?? '');
if (!$email) {
    json_response(['ok' => false, 'error' => 'Please enter a valid email address.'], 422);
}

// Rate limit
$maxPerHour = (int)env('MAGIC_LINK_PER_EMAIL_PER_HOUR', 3);
$rlKey = 'magic_link:' . hash('sha256', $email);
if (!rate_limit($rlKey, $maxPerHour, 3600)) {
    json_response([
        'ok'    => false,
        'error' => 'Too many login requests. Please wait an hour and try again.',
    ], 429);
}

$user = find_or_create_user($email);
$userId = (int)$user['id'];
$name   = (string)($user['name'] ?? '');

$redirect = clean($_POST['redirect'] ?? '', 200);
$ok = send_magic_link($userId, $email, $name, $redirect);

if (!$ok) {
    json_response(['ok' => false, 'error' => 'We couldn\'t send the email. Please try again in a moment.'], 500);
}

json_response(['ok' => true]);
