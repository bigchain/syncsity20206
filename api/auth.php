<?php
/**
 * GET /api/auth?token=…&redirect=…
 *
 * Validates the magic-link token, consumes it, sets the session, redirects.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    exit('Method not allowed.');
}

$token    = trim($_GET['token']    ?? '');
$redirect = trim($_GET['redirect'] ?? '');

if (!preg_match('/^[0-9a-f]{64}$/', $token)) {
    render_auth_error('This login link is not valid. Please request a new one.');
    exit;
}

$rlKey = 'auth_token:' . hash_ip();
if (!rate_limit($rlKey, 10, 3600)) {
    render_auth_error('Too many login attempts. Please try again later.');
    exit;
}

$user = DB::one(
    "SELECT id, name, email, magic_link_token
       FROM users
      WHERE magic_link_token = ?
        AND magic_link_expiry >= NOW()
      LIMIT 1",
    [$token]
);

if (!$user) {
    render_auth_error('This login link has expired or has already been used. Please request a new one.', true);
    exit;
}

if (!hash_equals((string)$user['magic_link_token'], $token)) {
    render_auth_error('This login link is not valid.');
    exit;
}

$userId = (int)$user['id'];

// Consume (single-use)
DB::run(
    "UPDATE users SET magic_link_token = NULL, magic_link_expiry = NULL WHERE id = ?",
    [$userId]
);

// Start session
$_SESSION['user_id']    = $userId;
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name']  = $user['name'] ?? '';
$_SESSION['auth_at']    = time();
$_SESSION['_created']   = time();

session_write_close();

audit('magic_link_login', ['ip_hash' => hash_ip()], $userId);

$dest = safe_redirect_path($redirect, '/dashboard');
header('Location: ' . $dest, true, 302);
exit;

// ─── Error renderer (lightweight standalone page) ───────────────────────────
function render_auth_error(string $msg, bool $showResend = false): void
{
    http_response_code(400);
    $resend = $showResend
        ? '<a href="/auth/login" class="btn btn--primary">Request a new login link</a>'
        : '<a href="/" class="btn btn--ghost">Back home</a>';
    $msgEsc = e($msg);

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login link invalid — Syncsity</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/core.css">
<link rel="stylesheet" href="/assets/css/components.css">
<link rel="stylesheet" href="/assets/css/pages.css">
</head>
<body>
<main class="auth-shell">
  <div class="auth-card">
    <div style="text-align:center; margin-bottom: var(--s-6);">
      <div style="font-size:32px; margin-bottom: var(--s-3);">🔒</div>
      <h1>Login link invalid</h1>
      <p class="lead">{$msgEsc}</p>
    </div>
    <div style="display:flex; gap: var(--s-3); justify-content:center; flex-wrap:wrap;">
      {$resend}
    </div>
  </div>
</main>
<script src="/assets/js/core.js" defer></script>
</body>
</html>
HTML;
}
