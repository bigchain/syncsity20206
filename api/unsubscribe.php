<?php
/**
 * GET /api/unsubscribe?email=...&t=<hmac>
 *
 * One-click unsubscribe. The HMAC is computed from email + SESSION_SECRET so
 * we can validate the link came from us without storing per-email tokens.
 * Adds the email to email_optout. From that moment on, the email-worker
 * skips the address — but transactional mail (magic link, report-ready)
 * still goes through.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/functions.php';

$email = strtolower(trim((string)($_GET['email'] ?? '')));
$token = (string)($_GET['t'] ?? '');

$ok = false;
if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $expected = hash_hmac('sha256', $email, (string)env('SESSION_SECRET', 'syncsity'));
    if (hash_equals($expected, $token)) {
        try {
            DB::run(
                "INSERT INTO email_optout (email, opted_out_at, reason)
                 VALUES (?, NOW(), 'one-click')
                 ON DUPLICATE KEY UPDATE opted_out_at = NOW()",
                [$email]
            );
            $ok = true;
            audit('email_unsubscribe', ['email_hash' => hash('sha256', $email)]);
        } catch (Throwable $e) {
            error_log('[unsubscribe] ' . $e->getMessage());
        }
    }
}

http_response_code($ok ? 200 : 400);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $ok ? 'Unsubscribed' : 'Unsubscribe link invalid' ?> — Syncsity</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<link rel="stylesheet" href="/assets/css/static-page.css">
</head>
<body>
<div class="bg-stage" aria-hidden="true"></div>

<main style="min-height: 100vh; display:flex; align-items:center; justify-content:center; padding: 48px 24px;">
  <div class="card" style="max-width: 480px; padding: 40px; text-align: center;">
    <?php if ($ok): ?>
      <div style="font-size: 36px; margin-bottom: 16px;">✓</div>
      <h1 style="font-size: 1.6rem; margin-bottom: 12px;">You're unsubscribed.</h1>
      <p class="muted" style="margin-bottom: 24px;">
        We've removed <strong style="color:#fff;"><?= e($email) ?></strong> from our follow-up
        sequence. Your existing report stays available in your dashboard. Transactional emails
        (magic-link logins, report-ready notifications) will still be sent if you log in or
        take another assessment.
      </p>
      <a href="/" class="btn btn--ghost">Back home</a>
    <?php else: ?>
      <div style="font-size: 36px; margin-bottom: 16px;">⚠</div>
      <h1 style="font-size: 1.6rem; margin-bottom: 12px;">This link is invalid.</h1>
      <p class="muted" style="margin-bottom: 24px;">
        The unsubscribe link is malformed or expired. If you want to stop hearing from us,
        reply to any email with the word <strong style="color:#fff;">"unsubscribe"</strong> —
        a human will action it.
      </p>
      <a href="mailto:edward@syncsity.com" class="btn btn--ghost">Email Edward</a>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
