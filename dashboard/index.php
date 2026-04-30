<?php
declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/functions.php';

require_auth();

$user = current_user();
if (!$user) redirect('/auth/login?logout=1');

$assessments = DB::all(
    "SELECT id, company, status, leak_amount, root_cause_name, report_share_token, created_at, completed_at
       FROM assessments
      WHERE user_id = ?
      ORDER BY created_at DESC
      LIMIT 50",
    [(int)$user['id']]
);

$pageTitle = 'Your dashboard — Syncsity';
$pageDesc  = 'Your Revenue Intelligence Reports.';

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<main class="dash-shell">
  <div class="container container--md">
    <div class="dash-head">
      <div>
        <span class="eyebrow">Dashboard</span>
        <h1 style="margin: var(--s-2) 0 var(--s-1);">Hi <?= e($user['name'] ?: 'there') ?>.</h1>
        <p class="muted">Logged in as <span class="mono"><?= e($user['email']) ?></span> · <a href="/auth/login?logout=1">Log out</a></p>
      </div>
      <a href="/assess" class="btn btn--primary">Take another assessment <span class="arrow">→</span></a>
    </div>

    <?php if (empty($assessments)): ?>
      <div class="dash-empty">
        <h3 style="margin-bottom: var(--s-3);">No reports yet.</h3>
        <p class="muted" style="margin-bottom: var(--s-6);">Take the 15-minute Aha! Assessment — we'll write you a personalised Revenue Intelligence Report.</p>
        <a href="/assess" class="btn btn--primary">Start the assessment <span class="arrow">→</span></a>
      </div>
    <?php else: ?>
      <h2 style="font-size: 1.2rem; margin-bottom: var(--s-4); color: var(--text-muted); font-family: var(--font-mono); letter-spacing: 0.14em; text-transform: uppercase; font-weight: 600;">Your reports</h2>
      <div class="report-list">
        <?php foreach ($assessments as $a): ?>
          <?php
          $status = (string)$a['status'];
          $isReady = $status === 'ready';
          $href = $isReady
            ? '/assess/report?t=' . urlencode((string)$a['report_share_token'])
            : '/assess/processing?id=' . (int)$a['id'];
          $statusBadge = match ($status) {
              'ready'        => '<span class="pill pill--success">Ready</span>',
              'failed'       => '<span class="pill" style="color:var(--error); border-color: rgba(255,86,48,0.30); background: rgba(255,86,48,0.06);">Failed</span>',
              'queued'       => '<span class="pill">Queued</span>',
              'researching'  => '<span class="pill pill--accent">Researching</span>',
              'analysing'    => '<span class="pill pill--accent">Analysing</span>',
              'writing'      => '<span class="pill pill--accent">Writing</span>',
              default        => '<span class="pill">' . e(ucfirst($status)) . '</span>',
          };
          ?>
          <a href="<?= e($href) ?>" class="report-row" style="text-decoration:none;">
            <div>
              <div class="report-row__title"><?= e($a['company'] ?: 'Untitled assessment') ?></div>
              <div class="report-row__meta">
                Submitted <?= e(time_ago((string)$a['created_at'])) ?>
                <?php if ($isReady && !empty($a['root_cause_name'])): ?>
                  · <strong style="color: var(--text);"><?= e($a['root_cause_name']) ?></strong>
                  · £<?= e(number_format((int)$a['leak_amount'])) ?>/month leak
                <?php endif; ?>
              </div>
            </div>
            <div><?= $statusBadge ?></div>
            <div class="dim mono" style="font-size:0.85rem;">
              <?= $isReady ? 'Open →' : 'View progress →' ?>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <div style="margin-top: var(--s-12); padding: var(--s-8); background: var(--bg-elev-1); border: 1px solid var(--border); border-radius: var(--r-xl); display:flex; gap: var(--s-6); align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width: 280px;">
          <h3 style="font-size: 1.2rem; margin-bottom: var(--s-2);">Pressure-test your diagnosis with a human.</h3>
          <p class="muted" style="font-size: 0.95rem;">A 30-minute Strategy Session with a senior Syncsity operator. Fee credited against any subsequent engagement.</p>
        </div>
        <a href="<?= e(CALENDLY_URL) ?>" class="btn btn--primary">Book a Strategy Session <span class="arrow">→</span></a>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
