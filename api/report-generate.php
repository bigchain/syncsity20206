<?php
/**
 * Report Generator — runs the OpenRouter 3-stage pipeline.
 *
 * Two entry modes:
 *   1. HTTP (fire-and-forget kick from /api/assess-submit):
 *        GET /api/report-generate?id=<assessment_id>&kick=<hmac>
 *      The hmac proves it came from our own assess-submit (cheap auth — the
 *      worst case is that someone re-triggers a generator, which is idempotent).
 *
 *   2. CLI (cron backstop, every 5 minutes):
 *        php api/report-generate.php
 *      Picks up any 'queued' assessment older than 30s.
 *
 * Pipeline (per assessment):
 *   queued → researching → analysing → writing → ready (or failed)
 *
 * Idempotency: every transition uses an UPDATE...WHERE status='<expected>',
 * so two concurrent workers can't double-process.
 */

declare(strict_types=1);

define('SYNC_ROOT', dirname(__DIR__));

require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/functions.php';
require_once SYNC_ROOT . '/lib/openrouter.php';
require_once SYNC_ROOT . '/lib/mailer.php';
require_once SYNC_ROOT . '/lib/email_renderer.php';

// Always log generator activity to its own file.
ini_set('error_log', SYNC_ROOT . '/storage/logs/report-engine.log');
set_time_limit(180); // 3 min cap per assessment

set_exception_handler(function (Throwable $e) {
    error_log('[report-generate FATAL] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (PHP_SAPI !== 'cli') {
        http_response_code(500);
        echo 'engine error';
    }
    exit(1);
});

// ─── Pick the assessment(s) to process ───────────────────────────────────────
$assessments = [];

if (PHP_SAPI === 'cli') {
    // Cron mode: pick all queued > 30s OR stalled in mid-pipeline > 5 min
    $rows = DB::all(
        "SELECT id FROM assessments
         WHERE (status = 'queued'      AND queued_at < DATE_SUB(NOW(), INTERVAL 30 SECOND))
            OR (status IN ('researching','analysing','writing') AND started_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE))
         ORDER BY queued_at ASC
         LIMIT 5"
    );
    $assessments = array_column($rows, 'id');
    if (!$assessments) {
        engine_log('cron: no work to do');
        exit(0);
    }
} else {
    $id = (int)($_GET['id'] ?? 0);
    $kick = (string)($_GET['kick'] ?? '');
    $expected = hash_hmac('sha256', (string)$id, (string)env('SESSION_SECRET', 'syncsity'));
    if ($id <= 0 || !hash_equals($expected, $kick)) {
        http_response_code(400);
        echo 'invalid';
        exit;
    }
    $assessments = [$id];
    // Respond instantly so the kicker can disconnect.
    if (function_exists('fastcgi_finish_request')) {
        echo 'kicked';
        @fastcgi_finish_request();
    } else {
        ignore_user_abort(true);
        ob_start();
        echo 'kicked';
        header('Content-Length: ' . ob_get_length());
        header('Connection: close');
        ob_end_flush();
        @ob_flush(); @flush();
    }
}

foreach ($assessments as $aid) {
    process_assessment((int)$aid);
}

if (PHP_SAPI !== 'cli') exit(0);


// ─── The pipeline ────────────────────────────────────────────────────────────
function process_assessment(int $assessmentId): void
{
    engine_log("→ start assessment={$assessmentId}");

    // Atomically claim the row so another worker can't race us
    $claimed = DB::run(
        "UPDATE assessments
            SET status='researching', started_at=NOW(), updated_at=NOW()
          WHERE id = ?
            AND (status='queued' OR (status IN ('researching','analysing','writing') AND started_at < DATE_SUB(NOW(), INTERVAL 5 MINUTE)))",
        [$assessmentId]
    );
    if ($claimed->rowCount() === 0) {
        engine_log("  assessment={$assessmentId} not in claimable state — skip");
        return;
    }

    try {
        $assessment = DB::one("SELECT * FROM assessments WHERE id = ? LIMIT 1", [$assessmentId]);
        $user       = DB::one("SELECT * FROM users WHERE id = ? LIMIT 1", [$assessment['user_id']]);
        if (!$assessment || !$user) {
            throw new RuntimeException('assessment or user missing');
        }

        $answers = json_decode((string)$assessment['answers_json'], true) ?: [];

        // ── Stage 1: research ────────────────────────────────────────────
        engine_log("  stage 1: research");
        $researchPrompt = load_prompt('research', vars_for_research($assessment, $answers));
        $research = llm_call($researchPrompt, system_msg(), 4500, true);
        DB::run("UPDATE assessments SET research_json = ?, status='analysing', updated_at=NOW() WHERE id = ?",
            [json_encode($research, JSON_UNESCAPED_UNICODE), $assessmentId]);

        // ── Stage 2: analyse ─────────────────────────────────────────────
        engine_log("  stage 2: analyse");
        $analyzePrompt = load_prompt('analyze', array_merge(
            vars_for_research($assessment, $answers),
            ['research_json'    => json_encode($research, JSON_UNESCAPED_UNICODE),
             'archetype_name'   => $research['the_archetype']['name'] ?? 'The Hidden Constraint']
        ));
        $analysis = llm_call($analyzePrompt, system_msg(), 5500, true);
        DB::run("UPDATE assessments SET status='writing', updated_at=NOW() WHERE id = ?", [$assessmentId]);

        // ── Stage 3: write ───────────────────────────────────────────────
        engine_log("  stage 3: write");
        $reportPrompt = load_prompt('report', array_merge(
            vars_for_research($assessment, $answers),
            ['research_json' => json_encode($research, JSON_UNESCAPED_UNICODE),
             'analysis_json' => json_encode($analysis, JSON_UNESCAPED_UNICODE)]
        ));
        $report = llm_call($reportPrompt, system_msg(), 8000, true);

        // ── Persist final ────────────────────────────────────────────────
        $leakAmount = (int)($analysis['leak']['headline_amount_gbp'] ?? ($research['leak_estimate']['monthly_amount_gbp'] ?? 10000));
        $rootName   = (string)($analysis['root_cause']['name'] ?? ($research['the_archetype']['name'] ?? 'The Hidden Constraint'));

        DB::run(
            "UPDATE assessments
               SET report_json = ?, leak_amount = ?, root_cause_name = ?,
                   status = 'ready', completed_at = NOW(), updated_at = NOW(),
                   status_message = NULL
             WHERE id = ?",
            [
                json_encode([
                    'research' => $research,
                    'analysis' => $analysis,
                    'report'   => $report,
                    'rendered_at' => date('c'),
                ], JSON_UNESCAPED_UNICODE),
                $leakAmount, $rootName, $assessmentId,
            ]
        );

        engine_log("  ✓ ready — leak £{$leakAmount}/month — {$rootName}");
        audit('report_generated', ['assessment_id' => $assessmentId, 'leak' => $leakAmount, 'root' => $rootName], (int)$user['id']);

        // ── Email the user that it's ready ───────────────────────────────
        send_report_ready_email($user, $assessment, $leakAmount, $rootName);

    } catch (Throwable $e) {
        engine_log("  ✗ ERROR: " . $e->getMessage());
        $attempts = (int)($assessment['failed_attempts'] ?? 0) + 1;
        $newStatus = $attempts >= 3 ? 'failed' : 'queued';
        DB::run(
            "UPDATE assessments
               SET status = ?, status_message = ?, failed_attempts = ?, updated_at = NOW()
             WHERE id = ?",
            [$newStatus, mb_substr($e->getMessage(), 0, 480), $attempts, $assessmentId]
        );
    }
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

function engine_log(string $msg): void
{
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
    error_log($line);
    if (PHP_SAPI === 'cli') echo $line;
}

function system_msg(): string
{
    return "You are a senior business analyst writing for Syncsity, a UK AI transformation studio. You always return ONLY valid JSON, no markdown wrapping, no prose outside the JSON. You are precise, specific, never sycophantic, never patronising. Use UK English spellings.";
}

function vars_for_research(array $a, array $answers): array
{
    return [
        'name'              => str_or_empty($a['name'] ?? ($answers['name'] ?? '')),
        'company'           => str_or_empty($a['company'] ?? ($answers['company'] ?? '')),
        'website'           => str_or_empty($a['website'] ?? ($answers['website'] ?? '')) ?: 'not provided',
        'country'           => str_or_empty($a['country'] ?? ($answers['country'] ?? '')) ?: 'not provided',
        'biz_type'          => str_or_empty($a['biz_type'] ?? ($answers['biz_type'] ?? '')) ?: 'not provided',
        'team_size'         => str_or_empty($a['team_size'] ?? ($answers['team_size'] ?? '')) ?: 'not provided',
        'revenue_band'      => str_or_empty($a['revenue_band'] ?? ($answers['revenue_band'] ?? '')) ?: 'not provided',
        'org_layers'        => str_or_empty($answers['org_layers'] ?? '') ?: 'n/a',
        'frustration'       => str_or_empty($a['frustration'] ?? ($answers['frustration'] ?? '')),
        'frustration_more'  => str_or_empty($answers['frustration_more'] ?? ''),
        'monthly_inquiries' => num_or_empty($a['monthly_inquiries'] ?? ($answers['monthly_inquiries'] ?? '')),
        'conversion_rate'   => num_or_empty($a['conversion_rate']   ?? ($answers['conversion_rate']   ?? '')),
        'avg_deal'          => num_or_empty($a['avg_deal']          ?? ($answers['avg_deal']          ?? '')),
        'capacity'          => str_or_empty($a['capacity'] ?? ($answers['capacity'] ?? '')),
        'already_tried'     => str_or_empty($answers['already_tried']    ?? ''),
        'real_block'        => str_or_empty($a['real_block'] ?? ($answers['real_block'] ?? '')),
        'real_block_more'   => str_or_empty($answers['real_block_more']  ?? ''),
        'vision'            => str_or_empty($answers['vision']           ?? ''),
        'confidence'        => num_or_empty($a['confidence'] ?? ($answers['confidence'] ?? '')),
        'priority'          => str_or_empty($a['priority']   ?? ($answers['priority']   ?? '')),
    ];
}

function str_or_empty(mixed $v): string
{
    if (is_array($v)) return implode(', ', array_map('strval', $v));
    return is_string($v) ? trim($v) : (is_scalar($v) ? (string)$v : '');
}

function num_or_empty(mixed $v): string
{
    return is_numeric($v) ? (string)$v : '';
}

function send_report_ready_email(array $user, array $assessment, int $leak, string $root): void
{
    try {
        $token  = (string)$assessment['report_share_token'];
        $reportUrl = APP_URL . '/assess/report?t=' . urlencode($token);

        // Generate a fresh magic link that lands directly on the report
        $name  = (string)($user['name'] ?? '');
        $email = (string)$user['email'];
        send_magic_link((int)$user['id'], $email, $name, '/assess/report?t=' . $token);

        $rendered = render_email('report_ready', [
            'name'            => $name ?: 'there',
            'leak_amount'     => '£' . number_format($leak),
            'root_cause_name' => $root,
            'report_url'      => $reportUrl,
        ]);

        Mailer::send($email, $name, $rendered['subject'], $rendered['html'], $rendered['text']);

        // Internal copy
        $internal = (string)env('MAIL_NOTIFY_INTERNAL', NOTIFY_INTERNAL);
        if ($internal !== '') {
            Mailer::send($internal, 'Syncsity', '[New Report] ' . ($user['email'] ?? '') . ' — £' . number_format($leak) . '/month leak — ' . $root,
                "<p>New Revenue Intelligence Report ready.</p><p><strong>For:</strong> " . htmlspecialchars($email) . "<br><strong>Company:</strong> " . htmlspecialchars((string)($assessment['company'] ?? '')) . "<br><strong>Leak:</strong> £" . number_format($leak) . "/month<br><strong>Root cause:</strong> " . htmlspecialchars($root) . "</p><p><a href=\"" . htmlspecialchars($reportUrl) . "\">Open report</a></p>",
                "New report ready for {$email}. Leak £" . number_format($leak) . "/month. Root cause: {$root}. URL: {$reportUrl}"
            );
        }
    } catch (Throwable $e) {
        error_log('[report-generate] email send failed: ' . $e->getMessage());
    }
}
