<?php
/**
 * Syncsity — Shared helpers
 *
 *   - rate_limit()        — DB-backed token bucket
 *   - hash_ip()           — GDPR-safe IP hashing
 *   - audit()             — append to audit_log
 *   - find_or_create_user / current_user / require_auth
 *   - send_magic_link()   — single source of truth for the magic-link flow
 *   - flash() / get_flash()
 */

declare(strict_types=1);

if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/csrf.php';

// ─── Rate limiting (DB-backed) ──────────────────────────────────────────────
function rate_limit(string $key, int $maxHits, int $windowSeconds = 3600): bool
{
    $windowEnd = (new DateTime('+' . $windowSeconds . ' seconds', new DateTimeZone('UTC')))
        ->format('Y-m-d H:i:s');

    try {
        DB::run(
            "INSERT INTO rate_limits (`key`, hits, window_end)
             VALUES (?, 1, ?)
             ON DUPLICATE KEY UPDATE
                hits       = IF(window_end < NOW(), 1,                hits + 1),
                window_end = IF(window_end < NOW(), VALUES(window_end), window_end)",
            [$key, $windowEnd]
        );
        $row = DB::one("SELECT hits FROM rate_limits WHERE `key` = ? LIMIT 1", [$key]);
        return $row !== false && (int)$row['hits'] <= $maxHits;
    } catch (PDOException $e) {
        error_log('[rate_limit] ' . $e->getMessage());
        return true; // Fail open — never lock out everyone if the table breaks
    }
}

// ─── IP hashing ─────────────────────────────────────────────────────────────
function hash_ip(string $ip = ''): string
{
    if ($ip === '') {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
           ?? $_SERVER['HTTP_X_FORWARDED_FOR']
           ?? $_SERVER['REMOTE_ADDR']
           ?? '0.0.0.0';
        $ip = trim(explode(',', $ip)[0]);
    }
    $salt = (string)env('SESSION_SECRET', 'syncsity-default-salt');
    return hash('sha256', $ip . $salt);
}

// ─── Audit log ──────────────────────────────────────────────────────────────
function audit(string $action, array $detail = [], ?int $userId = null): void
{
    try {
        DB::run(
            "INSERT INTO audit_log (user_id, action, detail, ip_hash, user_agent, created_at)
             VALUES (?, ?, ?, ?, ?, NOW())",
            [
                $userId,
                $action,
                json_encode($detail, JSON_UNESCAPED_UNICODE),
                hash_ip(),
                mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 500),
            ]
        );
    } catch (PDOException $e) {
        error_log('[audit] ' . $e->getMessage());
    }
}

// ─── User helpers ───────────────────────────────────────────────────────────
function find_or_create_user(string $email, string $name = '', string $company = ''): array
{
    $row = DB::one("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);
    if ($row) {
        // Backfill name / company if newly provided and empty
        if (($name && empty($row['name'])) || ($company && empty($row['company']))) {
            DB::run(
                "UPDATE users SET name = COALESCE(NULLIF(?,''), name), company = COALESCE(NULLIF(?,''), company), updated_at = NOW() WHERE id = ?",
                [$name, $company, $row['id']]
            );
            $row = DB::one("SELECT * FROM users WHERE id = ? LIMIT 1", [$row['id']]);
        }
        return $row;
    }

    $id = DB::insert(
        "INSERT INTO users (email, name, company, gdpr_consent, gdpr_consent_at, created_at)
         VALUES (?, ?, ?, 1, NOW(), NOW())",
        [$email, $name ?: null, $company ?: null]
    );
    return DB::one("SELECT * FROM users WHERE id = ? LIMIT 1", [$id]) ?: [
        'id' => $id, 'email' => $email, 'name' => $name, 'company' => $company,
    ];
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) return null;
    $row = DB::one("SELECT * FROM users WHERE id = ? LIMIT 1", [$_SESSION['user_id']]);
    return $row ?: null;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_email']);
}

function require_auth(string $redirectTo = ''): void
{
    if (!is_logged_in()) {
        $back = $redirectTo !== '' ? $redirectTo : ($_SERVER['REQUEST_URI'] ?? '/dashboard');
        redirect('/auth/login?redirect=' . urlencode($back));
    }
}

// ─── Magic link ─────────────────────────────────────────────────────────────
/**
 * Generate + email a magic-link login. Returns true on send.
 * Caller responsibilities: validate email, rate limit, find_or_create_user.
 */
function send_magic_link(int $userId, string $email, string $name = '', string $redirect = ''): bool
{
    if ($userId <= 0 || $email === '') return false;

    require_once SYNC_ROOT . '/lib/mailer.php';
    require_once SYNC_ROOT . '/lib/email_renderer.php';

    $token = bin2hex(random_bytes(32));
    try {
        DB::run(
            "UPDATE users
                SET magic_link_token  = ?,
                    magic_link_expiry = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
              WHERE id = ?",
            [$token, $userId]
        );
    } catch (Throwable $e) {
        error_log('[send_magic_link] token write failed: ' . $e->getMessage());
        return false;
    }

    $magicUrl = APP_URL . '/api/auth?token=' . urlencode($token);
    if ($redirect !== '' && $redirect[0] === '/' && !str_starts_with($redirect, '//')
        && !str_contains($redirect, '%') && !str_contains($redirect, '#')
        && preg_match('#^/[a-zA-Z0-9/_\-?=&.]*$#', $redirect)) {
        $magicUrl .= '&redirect=' . urlencode($redirect);
    }

    try {
        $rendered = render_email('magic_link', [
            'name'            => $name !== '' ? $name : 'there',
            'magic_url'       => $magicUrl,
            'expires_minutes' => 15,
        ]);
        $sent = Mailer::send(
            $email,
            $name !== '' ? $name : '',
            $rendered['subject'],
            $rendered['html'],
            $rendered['text']
        );
    } catch (Throwable $e) {
        error_log('[send_magic_link] render/send failed: ' . $e->getMessage());
        return false;
    }

    if ($sent) {
        audit('magic_link_sent', ['expires_minutes' => 15, 'redirect' => $redirect ?: null], $userId);
    }
    return $sent;
}

// ─── Flash messages ─────────────────────────────────────────────────────────
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash(): array
{
    $msgs = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $msgs;
}

// ─── Misc ───────────────────────────────────────────────────────────────────
/** Human-readable relative time. */
function time_ago(string $datetime): string
{
    $ts = strtotime($datetime);
    if ($ts === false) return $datetime;
    $diff = time() - $ts;
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60)    . 'm ago';
    if ($diff < 86400)  return floor($diff / 3600)  . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return date('j M Y', $ts);
}
