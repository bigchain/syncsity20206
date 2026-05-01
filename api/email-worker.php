<?php
/**
 * Follow-up Email Worker
 *
 * Cron-driven (hourly):
 *   0 * * * * cd /home/marieatlasco/public_html && /usr/local/cpanel/3rdparty/bin/php api/email-worker.php >> storage/logs/email-worker.log 2>&1
 *
 * Logic per sequence step:
 *   - Find users whose ready report aged into the step's window
 *   - Skip if already sent (email_log unique key on (user_id, sequence_key, assessment_id))
 *   - Skip if email is in email_optout
 *   - Skip if user has booked a Strategy Session (we'd track that via a future
 *     'bookings' table — for now we just send all 5; the day14/30 templates
 *     are kind even if they did book)
 *   - Render template, send via Mailer, log result.
 *
 * Idempotent. Safe to run any number of times. The unique key prevents dupes.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';

// CLI-only by default. HTTP access requires an HMAC kick token (so a future
// "trigger from health-check" pattern works without exposing the worker).
if (PHP_SAPI !== 'cli') {
    $kick     = (string)($_GET['kick'] ?? '');
    $expected = hash_hmac('sha256', 'email-worker', (string)env('SESSION_SECRET', 'syncsity'));
    if (!hash_equals($expected, $kick)) {
        http_response_code(403);
        exit('forbidden');
    }
    if (function_exists('fastcgi_finish_request')) {
        echo 'kicked'; @fastcgi_finish_request();
    } else {
        ignore_user_abort(true);
        ob_start(); echo 'kicked'; header('Content-Length: ' . ob_get_length()); header('Connection: close');
        ob_end_flush(); @ob_flush(); @flush();
    }
}

require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/functions.php';
require_once SYNC_ROOT . '/lib/mailer.php';
require_once SYNC_ROOT . '/lib/email_renderer.php';

ini_set('error_log', SYNC_ROOT . '/storage/logs/email-worker.log');
set_time_limit(180);

set_exception_handler(function (Throwable $e) {
    error_log('[email-worker FATAL] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    exit(1);
});

// Each step: { key, days_after_report, template, subject_hint }
$STEPS = [
    ['key' => 'followup_day1',  'days' => 1,  'template' => 'followup_day1'],
    ['key' => 'followup_day3',  'days' => 3,  'template' => 'followup_day3'],
    ['key' => 'followup_day7',  'days' => 7,  'template' => 'followup_day7'],
    ['key' => 'followup_day14', 'days' => 14, 'template' => 'followup_day14'],
    ['key' => 'followup_day30', 'days' => 30, 'template' => 'followup_day30'],
];

$totalSent = 0; $totalSkipped = 0; $totalFailed = 0;

foreach ($STEPS as $step) {
    // Find ready assessments that aged into this window AND haven't been sent this step.
    // Window is "completed_at <= NOW() - INTERVAL X DAY" — simple ageing, no upper bound
    // (so we don't miss anything if cron is offline for hours).
    $rows = DB::all(
        "SELECT a.id AS assessment_id, a.user_id, a.report_share_token,
                a.leak_amount, a.root_cause_name,
                a.frustration, a.real_block, a.priority, a.biz_type,
                a.answers_json, a.completed_at,
                u.email, u.name
           FROM assessments a
           JOIN users u ON u.id = a.user_id
      LEFT JOIN email_log el ON el.user_id = a.user_id
                            AND el.assessment_id = a.id
                            AND el.sequence_key = ?
      LEFT JOIN email_optout eo ON eo.email = u.email
          WHERE a.status = 'ready'
            AND a.completed_at IS NOT NULL
            AND a.completed_at <= DATE_SUB(NOW(), INTERVAL ? DAY)
            AND el.id IS NULL
            AND eo.email IS NULL
       ORDER BY a.completed_at ASC
          LIMIT 50",
        [$step['key'], $step['days']]
    );

    if (!$rows) {
        worker_log("step={$step['key']}: no candidates");
        continue;
    }

    worker_log("step={$step['key']}: {$step['days']} candidates: " . count($rows));

    foreach ($rows as $r) {
        try {
            $userId       = (int)$r['user_id'];
            $assessmentId = (int)$r['assessment_id'];
            $email        = (string)$r['email'];
            $name         = (string)($r['name'] ?? '');
            $token        = (string)$r['report_share_token'];

            $answers = json_decode((string)$r['answers_json'], true) ?: [];

            $vars = [
                'name'              => $name ?: 'there',
                'root_cause_name'   => $r['root_cause_name'] ?: 'The Hidden Constraint',
                'leak_amount'       => '£' . number_format((int)$r['leak_amount']),
                'biz_type'          => $r['biz_type'] ?: 'service business',
                'priority'          => $answers['priority'] ?? ($r['priority'] ?: 'lift conversion'),
                'frustration_quote' => trim((string)($answers['frustration_more'] ?? '')),
                'real_block_quote'  => trim((string)($answers['real_block_more']  ?? '')),
                'report_url'        => APP_URL . '/assess/report?t=' . urlencode($token),
                'calendly_url'      => CALENDLY_URL,
                'unsubscribe_url'   => APP_URL . '/api/unsubscribe?email=' . urlencode($email)
                                            . '&t=' . hash_hmac('sha256', $email, (string)env('SESSION_SECRET', 'syncsity')),
            ];

            $rendered = render_email($step['template'], $vars);

            $sent = Mailer::send($email, $name, $rendered['subject'], $rendered['html'], $rendered['text']);

            if ($sent) {
                DB::run(
                    "INSERT INTO email_log (user_id, assessment_id, sequence_key, status, sent_at)
                     VALUES (?, ?, ?, 'sent', NOW())
                     ON DUPLICATE KEY UPDATE status = 'sent', sent_at = NOW(), error_message = NULL",
                    [$userId, $assessmentId, $step['key']]
                );
                $totalSent++;
                worker_log("  ✓ sent {$step['key']} → {$email}");
            } else {
                DB::run(
                    "INSERT INTO email_log (user_id, assessment_id, sequence_key, status, error_message, sent_at)
                     VALUES (?, ?, ?, 'failed', 'Mailer::send returned false', NOW())
                     ON DUPLICATE KEY UPDATE status = 'failed', error_message = 'Mailer::send returned false', sent_at = NOW()",
                    [$userId, $assessmentId, $step['key']]
                );
                $totalFailed++;
                worker_log("  ✗ failed {$step['key']} → {$email}");
            }
        } catch (Throwable $e) {
            $totalFailed++;
            error_log("[email-worker] error on user_id={$r['user_id']} step={$step['key']}: " . $e->getMessage());
            try {
                DB::run(
                    "INSERT INTO email_log (user_id, assessment_id, sequence_key, status, error_message, sent_at)
                     VALUES (?, ?, ?, 'failed', ?, NOW())
                     ON DUPLICATE KEY UPDATE status = 'failed', error_message = VALUES(error_message), sent_at = NOW()",
                    [(int)$r['user_id'], (int)$r['assessment_id'], $step['key'], mb_substr($e->getMessage(), 0, 480)]
                );
            } catch (Throwable $e2) { error_log('[email-worker] log write failed: ' . $e2->getMessage()); }
        }
    }
}

worker_log("done. sent={$totalSent} failed={$totalFailed}");
exit(0);

function worker_log(string $msg): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    error_log($line);
    if (PHP_SAPI === 'cli') echo $line;
}
