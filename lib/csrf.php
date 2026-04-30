<?php
/**
 * Syncsity — CSRF protection
 *
 * Dual mechanism: session token + double-submit cookie.
 * Works even when sessions are dropped (cPanel /tmp GC, etc).
 */

declare(strict_types=1);

if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';

function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) return '';

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token']      = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    $token = $_SESSION['csrf_token'];

    if (!isset($_COOKIE['_sync_csrf']) || $_COOKIE['_sync_csrf'] !== $token) {
        if (!headers_sent()) {
            setcookie('_sync_csrf', $token, [
                'expires'  => 0,
                'path'     => '/',
                'secure'   => APP_ENV === 'production',
                'httponly' => false,
                'samesite' => 'Lax',
            ]);
        }
    }
    return $token;
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(string $token): bool
{
    if ($token === '') return false;

    $sessionToken = $_SESSION['csrf_token']      ?? '';
    $created      = (int)($_SESSION['csrf_token_time'] ?? 0);
    $lifetime     = 14400;
    if ($sessionToken !== '' && (time() - $created) <= $lifetime) {
        if (hash_equals($sessionToken, $token)) return true;
    }

    $cookieToken = $_COOKIE['_sync_csrf'] ?? '';
    if ($cookieToken !== '' && hash_equals($cookieToken, $token)) {
        return true;
    }

    return false;
}
