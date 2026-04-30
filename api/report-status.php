<?php
/**
 * GET /api/report-status?id=<assessment_id>
 *
 * Used by /assess/processing to poll. Returns:
 *   { status: 'queued' | 'researching' | 'analysing' | 'writing' | 'ready' | 'failed',
 *     progress: 0-100,
 *     redirect: '/assess/report?t=...'  (only when status='ready'),
 *     message:  string }
 *
 * Auth: must be the same user who owns the assessment, OR the share token must match.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/functions.php';

$id    = (int)($_GET['id'] ?? 0);
$token = clean($_GET['t'] ?? '', 64);

if ($id <= 0) {
    json_response(['status' => 'failed', 'progress' => 0, 'message' => 'invalid id'], 400);
}

$row = DB::one("SELECT id, user_id, status, status_message, report_share_token FROM assessments WHERE id = ? LIMIT 1", [$id]);
if (!$row) json_response(['status' => 'failed', 'progress' => 0, 'message' => 'not found'], 404);

// Auth: owner OR matching share token
$ownedByUser = !empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$row['user_id'];
$tokenMatch  = $token !== '' && $token === (string)$row['report_share_token'];
if (!$ownedByUser && !$tokenMatch) {
    json_response(['status' => 'failed', 'progress' => 0, 'message' => 'forbidden'], 403);
}

$progressMap = [
    'queued'      => 8,
    'researching' => 30,
    'analysing'   => 60,
    'writing'     => 85,
    'ready'       => 100,
    'failed'      => 0,
];
$messageMap = [
    'queued'      => 'Lining up the analysis…',
    'researching' => 'Reading your business and your industry…',
    'analysing'   => 'Naming the constraint and quantifying the leak…',
    'writing'     => 'Writing your Revenue Intelligence Report…',
    'ready'       => 'Done — opening your report.',
    'failed'      => 'Something went wrong — please contact us.',
];

$status   = (string)$row['status'];
$payload  = [
    'status'   => $status,
    'progress' => $progressMap[$status]  ?? 0,
    'message'  => $messageMap[$status]   ?? '…',
];

if ($status === 'ready') {
    $payload['redirect'] = '/assess/report?t=' . urlencode((string)$row['report_share_token']);
}
if ($status === 'failed' && !empty($row['status_message']) && APP_DEBUG) {
    $payload['debug'] = $row['status_message'];
}

json_response($payload);
