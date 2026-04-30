<?php
/**
 * POST /api/assess-submit
 *
 * Receives the conversational-form FormData payload, validates, persists,
 * fires the report generator (non-blocking), sends the magic-link email,
 * appends to Google Sheets best-effort, returns JSON for the JS engine.
 *
 * Body fields (FormData from assess.js):
 *   _csrf                — CSRF token
 *   answers              — JSON string of {qid: value}
 *   a[<qid>]             — flattened individual answers (also sent for safety)
 *
 * Returns JSON:
 *   { ok: true,  redirect: "/assess/processing?id=123" }
 *   { ok: false, error: "..." }
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/csrf.php';
require_once SYNC_ROOT . '/lib/functions.php';
require_once SYNC_ROOT . '/lib/sheets.php';

// Catch fatals & always return JSON
set_exception_handler(function (Throwable $e) {
    error_log('[assess-submit FATAL] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    json_response(['ok' => false, 'error' => 'Server error. Please try again.'], 500);
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['ok' => false, 'error' => 'Method not allowed.'], 405);
}

// CSRF
if (!csrf_verify($_POST['_csrf'] ?? '')) {
    json_response(['ok' => false, 'error' => 'Session expired — refresh the page and try again.'], 403);
}

// ─── Parse answers ───────────────────────────────────────────────────────────
$answers = [];

// Primary path: 'answers' JSON blob
if (!empty($_POST['answers']) && is_string($_POST['answers'])) {
    $decoded = json_decode($_POST['answers'], true);
    if (is_array($decoded)) $answers = $decoded;
}

// Belt-and-braces: also merge a[<qid>] flattened values (assess.js sends both)
if (!empty($_POST['a']) && is_array($_POST['a'])) {
    foreach ($_POST['a'] as $k => $v) {
        // multi-choice arrives as "a|b|c"
        if (is_string($v) && str_contains($v, '|') && !isset($answers[$k])) {
            $answers[$k] = explode('|', $v);
        } elseif (!isset($answers[$k])) {
            $answers[$k] = $v;
        }
    }
}

// ─── Required fields ─────────────────────────────────────────────────────────
$email   = clean_email($answers['email'] ?? '');
$name    = clean($answers['name']    ?? '', 120);
$company = clean($answers['company'] ?? '', 200);

if (!$email)            json_response(['ok' => false, 'error' => 'A valid email is required.'], 422);
if (mb_strlen($name) < 2)    json_response(['ok' => false, 'error' => 'Please tell us your first name.'], 422);
if (mb_strlen($company) < 1) json_response(['ok' => false, 'error' => 'Please tell us your company name.'], 422);

// ─── Rate limit (per email per day) ──────────────────────────────────────────
$maxPerDay = (int)env('ASSESS_PER_EMAIL_PER_DAY', 3);
$rlKey = 'assess:' . hash('sha256', $email . (string)env('SESSION_SECRET', ''));
if (!rate_limit($rlKey, $maxPerDay, 86400)) {
    json_response(['ok' => false, 'error' => 'You\'ve already submitted a few assessments today — give us a chance to send your report. Check your inbox.'], 429);
}

// ─── Persist ─────────────────────────────────────────────────────────────────
$user = find_or_create_user($email, $name, $company);
$userId = (int)$user['id'];

// Extract structured columns for filtering / Sheets
$website  = clean($answers['website']  ?? '', 500);
if ($website !== '' && !preg_match('#^https?://#i', $website)) $website = 'https://' . $website;
$country  = clean($answers['country']      ?? '', 40);
$bizType  = clean($answers['biz_type']     ?? '', 40);
$teamSize = clean($answers['team_size']    ?? '', 40);
$revenue  = clean($answers['revenue_band'] ?? '', 40);
$frustr   = clean($answers['frustration']  ?? '', 60);
$realBlk  = clean($answers['real_block']   ?? '', 60);
$capacity = clean($answers['capacity']     ?? '', 20);
$priority = clean($answers['priority']     ?? '', 40);

$mInq    = ctype_digit((string)($answers['monthly_inquiries'] ?? '')) ? (int)$answers['monthly_inquiries']    : null;
$conv    = is_numeric($answers['conversion_rate'] ?? '')               ? (float)$answers['conversion_rate']   : null;
$avgDeal = is_numeric($answers['avg_deal']        ?? '')               ? (float)$answers['avg_deal']          : null;
$conf    = ctype_digit((string)($answers['confidence']        ?? '')) ? (int)$answers['confidence']         : null;
if ($conf !== null) $conf = max(0, min(10, $conf));

$reportShareToken = bin2hex(random_bytes(20)); // 40 chars

DB::begin();
try {
    $assessmentId = DB::insert(
        "INSERT INTO assessments
            (user_id, answers_json, name, company, website, country, biz_type, team_size, revenue_band,
             frustration, real_block, monthly_inquiries, conversion_rate, avg_deal, capacity,
             confidence, priority, report_share_token, status, queued_at, created_at)
         VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?,
             ?, ?, ?, ?, ?, ?,
             ?, ?, ?, 'queued', NOW(), NOW())",
        [
            $userId,
            json_encode($answers, JSON_UNESCAPED_UNICODE),
            $name, $company, $website ?: null, $country ?: null, $bizType ?: null, $teamSize ?: null, $revenue ?: null,
            $frustr ?: null, $realBlk ?: null,
            $mInq, $conv, $avgDeal,
            $capacity ?: null,
            $conf, $priority ?: null,
            $reportShareToken,
        ]
    );
    DB::commit();
} catch (Throwable $e) {
    DB::rollback();
    error_log('[assess-submit] DB insert failed: ' . $e->getMessage());
    json_response(['ok' => false, 'error' => 'We couldn\'t save your assessment. Try again in a moment.'], 500);
}
$assessmentId = (int)$assessmentId;

audit('assessment_submitted', ['assessment_id' => $assessmentId, 'biz_type' => $bizType], $userId);

// ─── Best-effort: append to Google Sheets ────────────────────────────────────
GoogleSheets::appendLeadRow([
    'email'             => $email,
    'name'              => $name,
    'company'           => $company,
    'website'           => $website,
    'country'           => $country,
    'industry'          => $bizType,
    'team_size'         => $teamSize,
    'revenue_band'      => $revenue,
    'biggest_frustration' => $frustr,
    'real_problem'      => $realBlk,
    'monthly_inquiries' => $mInq,
    'conversion_rate'   => $conv,
    'avg_deal_size'     => $avgDeal,
    'cant_handle_more'  => $capacity,
    'already_tried'     => mb_substr((string)($answers['already_tried']     ?? ''), 0, 800),
    'hidden_block'      => mb_substr((string)($answers['real_block_more']  ?? ''), 0, 800),
    'vision_12_months'  => mb_substr((string)($answers['vision']            ?? ''), 0, 800),
    'stated_problem'    => mb_substr((string)($answers['frustration_more']  ?? ''), 0, 800),
    'source'            => 'aha_assessment',
    'assessment_id'     => $assessmentId,
    'report_url'        => APP_URL . '/assess/report?t=' . $reportShareToken,
]);

// ─── Auto-login if not already logged in (so processing/report are accessible) ──
if (empty($_SESSION['user_id']) || (int)$_SESSION['user_id'] !== $userId) {
    $_SESSION['user_id']    = $userId;
    $_SESSION['user_email'] = $email;
    $_SESSION['user_name']  = $name;
    $_SESSION['auth_at']    = time();
    $_SESSION['_created']   = time();
    session_write_close();
    // re-open for any later writes after this point
    if (session_status() === PHP_SESSION_NONE) session_start();
}

// ─── Send magic-link email (so they can come back later) ─────────────────────
// Best-effort — don't block submission on a mail failure.
try {
    send_magic_link($userId, $email, $name, '/assess/processing?id=' . $assessmentId);
} catch (Throwable $e) {
    error_log('[assess-submit] magic-link send failed: ' . $e->getMessage());
}

// ─── Fire-and-forget the report generator ────────────────────────────────────
// HostFluid disables shell_exec/proc_open. We use a non-blocking self-curl.
trigger_report_generator($assessmentId);

// ─── Done ────────────────────────────────────────────────────────────────────
json_response([
    'ok'       => true,
    'redirect' => '/assess/processing?id=' . $assessmentId,
]);

// ─── Helpers ─────────────────────────────────────────────────────────────────
function trigger_report_generator(int $assessmentId): void
{
    $url = APP_URL . '/api/report-generate?id=' . $assessmentId
         . '&kick=' . hash_hmac('sha256', (string)$assessmentId, (string)env('SESSION_SECRET', 'syncsity'));

    // Fire-and-forget: 1ms timeout, fail silently. The cron job will pick up
    // any assessment still in 'queued' state after 30s as a backstop.
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_NOSIGNAL       => 1,
        CURLOPT_TIMEOUT_MS     => 200,
        CURLOPT_CONNECTTIMEOUT_MS => 200,
        CURLOPT_SSL_VERIFYPEER => false,  // localhost loopback
        CURLOPT_HTTPHEADER     => ['User-Agent: Syncsity-internal-kick'],
    ]);
    @curl_exec($ch);
    curl_close($ch);
}
