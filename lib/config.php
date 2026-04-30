<?php
/**
 * Syncsity — Core Configuration
 *
 * Loads .env, defines constants, sets security headers, starts the session.
 * Must be the FIRST require in every PHP entrypoint.
 */

declare(strict_types=1);

date_default_timezone_set('UTC');

if (!defined('SYNC_ROOT')) {
    define('SYNC_ROOT', dirname(__DIR__));
}

// ─── Load .env ──────────────────────────────────────────────────────────────
$envFile = SYNC_ROOT . '/.env';
if (!file_exists($envFile)) {
    // Allow CLI scripts to bootstrap without crashing the web; for web entrypoints
    // we'll still 500 with a vague message.
    if (PHP_SAPI !== 'cli') {
        http_response_code(500);
        die('Service temporarily unavailable.');
    }
} else {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);
        // Strip surrounding quotes
        if (strlen($value) >= 2) {
            $first = $value[0]; $last = substr($value, -1);
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        if ($key !== '') {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

/** Read an env var with defaults & boolean coercion. */
function env(string $key, mixed $default = null): mixed
{
    $val = $_ENV[$key] ?? getenv($key);
    if ($val === false || $val === null || $val === '') return $default;
    return match (strtolower((string)$val)) {
        'true', '1', 'yes' => true,
        'false', '0', 'no' => false,
        'null'             => null,
        default            => $val,
    };
}

// ─── Constants ──────────────────────────────────────────────────────────────
define('APP_ENV',   (string)env('APP_ENV', 'production'));
define('APP_URL',   rtrim((string)env('APP_URL', 'https://syncsity.com'), '/'));
define('APP_NAME',  (string)env('APP_NAME', 'Syncsity'));
define('APP_DEBUG', APP_ENV !== 'production');

define('NOTIFY_INTERNAL', (string)env('MAIL_NOTIFY_INTERNAL', 'edward@syncsity.com'));
define('CALENDLY_URL',    (string)env('CALENDLY_STRATEGY_URL', '#'));

// ─── Error reporting ────────────────────────────────────────────────────────
$logDir = SYNC_ROOT . '/storage/logs';
if (!is_dir($logDir)) { @mkdir($logDir, 0750, true); }

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', $logDir . '/php-errors.log');
}

// ─── Session security ───────────────────────────────────────────────────────
if (PHP_SAPI !== 'cli' && !defined('SYNC_NO_SESSION')) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', APP_ENV === 'production' ? '1' : '0');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_path', '/');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.gc_maxlifetime', '14400');

    // App-specific session save path — survives cPanel /tmp GC
    $sessPath = SYNC_ROOT . '/storage/sessions';
    if (!is_dir($sessPath)) { @mkdir($sessPath, 0700, true); @chmod($sessPath, 0700); }
    if (is_dir($sessPath) && is_writable($sessPath)) {
        ini_set('session.save_path', $sessPath);
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Periodic ID regeneration to prevent fixation (preserve key state)
    if (!isset($_SESSION['_created'])) {
        $_SESSION['_created'] = time();
    } elseif (time() - $_SESSION['_created'] > 3600) {
        $preserve = [
            'csrf_token'        => $_SESSION['csrf_token']        ?? null,
            'csrf_token_time'   => $_SESSION['csrf_token_time']   ?? null,
            'user_id'           => $_SESSION['user_id']           ?? null,
            'user_email'        => $_SESSION['user_email']        ?? null,
            'user_name'         => $_SESSION['user_name']         ?? null,
            'auth_at'           => $_SESSION['auth_at']           ?? null,
            'pending_assessment'=> $_SESSION['pending_assessment'] ?? null,
        ];
        session_regenerate_id(true);
        $_SESSION['_created'] = time();
        foreach ($preserve as $k => $v) {
            if ($v !== null) $_SESSION[$k] = $v;
        }
    }
}

// ─── Security headers ───────────────────────────────────────────────────────
function send_security_headers(): void
{
    if (headers_sent()) return;

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");

    $csp = [
        "default-src 'self'",
        "script-src 'self' https://fonts.googleapis.com 'unsafe-inline'",
        "style-src 'self' https://fonts.googleapis.com 'unsafe-inline'",
        "font-src 'self' https://fonts.gstatic.com",
        "img-src 'self' data: https:",
        "connect-src 'self'",
        "frame-src https://calendly.com https://www.youtube.com https://www.youtube-nocookie.com",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp));

    if (APP_ENV === 'production') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }

    header_remove('X-Powered-By');
    header_remove('Server');
}

// ─── Helpers (ALWAYS available) ─────────────────────────────────────────────

/** Safe HTML output. */
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Trim, length-limit, strip tags. */
function clean(mixed $input, int $maxLen = 1000): string
{
    if (!is_string($input)) return '';
    $s = trim($input);
    $s = strip_tags($s);
    return mb_substr($s, 0, $maxLen);
}

/** Validate & lowercase email. */
function clean_email(mixed $input): string|false
{
    if (!is_string($input)) return false;
    $email = strtolower(trim($input));
    $email = mb_substr($email, 0, 254);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

/** Cryptographically-secure hex token. */
function secure_token(int $bytes = 32): string
{
    return bin2hex(random_bytes($bytes));
}

/** JSON response + exit. */
function json_response(array $data, int $code = 200): never
{
    if (!headers_sent()) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }
    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

/** Safe relative redirect (rejects external schemes & open-redirect tricks). */
function redirect(string $url, int $code = 302): never
{
    if (!str_starts_with($url, '/') || str_starts_with($url, '//')) {
        $url = '/';
    }
    header("Location: $url", true, $code);
    exit;
}

/** Validate that a redirect target is a relative same-origin path. */
function safe_redirect_path(string $path, string $fallback = '/dashboard'): string
{
    if ($path === '' || $path[0] !== '/' || str_starts_with($path, '//')) return $fallback;
    if (str_contains($path, '%') || str_contains($path, '#')) return $fallback;
    return preg_match('#^/[a-zA-Z0-9/_\-]*(?:\?[a-zA-Z0-9_=&.\-]+)?$#', $path) ? $path : $fallback;
}

// ─── Send headers now (web only) ────────────────────────────────────────────
if (PHP_SAPI !== 'cli') {
    send_security_headers();
}
