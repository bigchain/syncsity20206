<?php
/**
 * /assess/report?t=<share_token>   OR   /assess/report?id=<assessment_id>
 *
 * Renders the AI-generated Revenue Intelligence Report.
 * Auth: owner OR matching share token. Either suffices.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/functions.php';
require_once SYNC_ROOT . '/lib/md.php';

$token = clean($_GET['t']  ?? '', 64);
$id    = (int)($_GET['id'] ?? 0);

$assessment = null;
if ($token !== '') {
    $assessment = DB::one("SELECT * FROM assessments WHERE report_share_token = ? LIMIT 1", [$token]);
} elseif ($id > 0) {
    $assessment = DB::one("SELECT * FROM assessments WHERE id = ? LIMIT 1", [$id]);
}

if (!$assessment) {
    http_response_code(404);
    redirect('/');
}

$ownedByUser = !empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$assessment['user_id'];
$tokenMatch  = $token !== '' && $token === (string)$assessment['report_share_token'];
if (!$ownedByUser && !$tokenMatch) {
    redirect('/auth/login?redirect=' . urlencode('/assess/report?t=' . urlencode((string)$assessment['report_share_token'])));
}

if ($assessment['status'] !== 'ready' || empty($assessment['report_json'])) {
    redirect('/assess/processing?id=' . $assessment['id'] . '&t=' . urlencode((string)$assessment['report_share_token']));
}

$payload = json_decode((string)$assessment['report_json'], true);
$report  = $payload['report']   ?? [];
$research = $payload['research'] ?? [];
$analysis = $payload['analysis'] ?? [];

$company       = (string)($assessment['company'] ?? '');
$name          = (string)($assessment['name'] ?? '');
$leakDisplay   = (string)($report['headline_leak_display'] ?? ('£' . number_format((int)$assessment['leak_amount'])));
$rootCauseName = (string)($report['root_cause_name'] ?? $assessment['root_cause_name'] ?? 'The Hidden Constraint');
$titleText     = (string)($report['title'] ?? ('Revenue Intelligence Report — ' . $company));
$generatedAt   = !empty($assessment['completed_at']) ? date('j F Y', strtotime((string)$assessment['completed_at'])) : date('j F Y');

$pageTitle = 'Your Revenue Intelligence Report — Syncsity';
$pageDesc  = 'Personalised diagnostic written for ' . $company . '. £' . number_format((int)$assessment['leak_amount']) . '/month leak identified.';
$bodyClass = 'report-page';

// noindex — this is a private artefact
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex,nofollow">
<title><?= e($pageTitle) ?></title>
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&family=Source+Serif+4:wght@400;600&display=swap">
<link rel="stylesheet" href="/assets/css/core.css">
<link rel="stylesheet" href="/assets/css/components.css">
<link rel="stylesheet" href="/assets/css/pages.css">
<style>
  .report .report-content { font-family: var(--font-sans); font-size: 1.05rem; line-height: 1.75; color: var(--text); }
  .report .report-content > * + * { margin-top: var(--s-4); }
  .report .report-content h2 { margin-top: var(--s-12); margin-bottom: var(--s-3); font-size: clamp(1.5rem, 1vw + 1.4rem, 2rem); letter-spacing: -0.025em; }
  .report .report-content h3 { margin-top: var(--s-8); margin-bottom: var(--s-2); font-size: 1.2rem; color: var(--accent); }
  .report .report-content p  { color: var(--text-muted); }
  .report .report-content blockquote {
    margin: var(--s-6) 0; padding: var(--s-5) var(--s-6);
    border-left: 3px solid var(--orange); background: rgba(252,163,17,0.06);
    border-radius: 0 var(--r-md) var(--r-md) 0;
    font-family: 'Source Serif 4', Georgia, serif; font-style: italic; color: var(--text);
  }
  .report .report-content blockquote p { color: var(--text); }
  .report .report-content ul, .report .report-content ol { padding-left: var(--s-6); display: grid; gap: var(--s-2); color: var(--text-muted); }
  .report .report-content li { padding-left: 4px; }
  .report .report-content strong { color: var(--text); font-weight: 700; }
  .report .report-content a { color: var(--accent); text-decoration: underline; text-underline-offset: 3px; }
  .report .report-content code { font-family: var(--font-mono); padding: 2px 6px; background: var(--surface); border: 1px solid var(--border); border-radius: 4px; font-size: 0.92em; }
  .report-table {
    width: 100%; border-collapse: collapse;
    margin: var(--s-4) 0;
    font-size: 0.95rem;
    border: 1px solid var(--border); border-radius: var(--r-md); overflow: hidden;
  }
  .report-table th { text-align: left; padding: 10px 14px; background: var(--bg-elev-1); color: var(--text-muted); font-family: var(--font-mono); font-size: 0.78rem; letter-spacing: 0.14em; text-transform: uppercase; font-weight: 600; border-bottom: 1px solid var(--border); }
  .report-table td { padding: 10px 14px; border-bottom: 1px solid var(--border); color: var(--text-muted); }
  .report-table tr:last-child td { border-bottom: none; }
  .report-table tr:hover td { background: var(--surface); }
  .toolbar {
    display: flex; gap: var(--s-3); align-items: center; justify-content: space-between;
    padding: var(--s-4) 0;
    margin-bottom: var(--s-8);
    border-bottom: 1px solid var(--border);
  }
  @media print {
    .toolbar, .site-header, .site-footer, .nav__toggle, .theme-toggle { display: none !important; }
    body { background: white !important; color: #0a1022 !important; }
    .report__hero { background: #f7f9fc !important; color: #0a1022 !important; }
    .report__hero::before { display: none !important; }
    .report__hero-meta, .report__hero-leak, .report__divider-label, .report .report-content h3 { color: #0a1022 !important; }
    .report__hero-leak { color: #b9750a !important; }
  }
</style>
<script>(function(){try{var t=localStorage.getItem('syncsity-theme');if(t==='light')document.documentElement.setAttribute('data-theme','light');}catch(e){}})();</script>
</head>
<body>

<?php
$activeNav = '';
include SYNC_ROOT . '/partials/header.php';
?>

<main class="report">
  <div class="toolbar">
    <a href="/dashboard" class="btn btn--ghost btn--sm">← My reports</a>
    <div style="display:flex; gap: var(--s-3); align-items:center;">
      <button class="btn btn--ghost btn--sm" onclick="window.print()" type="button">Print / PDF</button>
      <a href="<?= e(CALENDLY_URL) ?>" class="btn btn--primary btn--sm">Book strategy session <span class="arrow">→</span></a>
    </div>
  </div>

  <!-- Hero -->
  <header class="report__hero">
    <div class="report__hero-meta">Revenue Intelligence Report · <?= e($generatedAt) ?></div>
    <h1 style="font-size: clamp(2rem, 2vw + 1.5rem, 3rem); margin-bottom: var(--s-4);"><?= e($titleText) ?></h1>
    <p class="lead">Prepared for <strong><?= e($name ?: 'you') ?></strong> at <strong><?= e($company) ?></strong>.</p>
    <div style="display:flex; gap: var(--s-8); flex-wrap:wrap; margin-top: var(--s-6); align-items:flex-end;">
      <div>
        <div class="report__hero-meta">Estimated monthly leak</div>
        <div class="report__hero-leak"><?= e($leakDisplay) ?><span class="report__hero-leak-label" style="font-size:1.05rem; margin-left:8px;">/ month</span></div>
      </div>
      <div style="flex: 1; min-width: 240px;">
        <div class="report__hero-meta">Root cause</div>
        <div style="font-size: clamp(1.3rem, 0.6vw + 1.2rem, 1.7rem); font-weight: 700; color: var(--text); margin-top: 6px; letter-spacing:-0.01em;">
          <?= e($rootCauseName) ?>
        </div>
      </div>
    </div>
  </header>

  <article class="report-content">
    <?php
    $sections = [
      'section_opener',
      'section_diagnosis',
      'section_leak',
      'section_market',
      'section_levers',
      'section_thirty_day',
      'section_trap',
      'section_ninety_day',
      'section_twelve_month',
      'section_invitation',
      'section_personal_note',
    ];
    foreach ($sections as $key) {
        if (empty($report[$key])) continue;
        echo md_to_html((string)$report[$key]);
    }
    ?>
  </article>

  <!-- Footer micro-copy -->
  <p class="dim" style="font-size:0.85rem; margin-top: var(--s-12); padding-top: var(--s-6); border-top: 1px solid var(--border); text-align:center;">
    <?= e($report['footer_microcopy'] ?? ('This report was researched and written specifically for ' . $name . ' at ' . $company . ' on ' . $generatedAt . '. The analysis is yours to use, share, and challenge.')) ?>
  </p>

  <!-- Final CTA depending on verdict -->
  <?php
  $verdict = $analysis['verdict']['fit_for_syncsity'] ?? 'maybe';
  if ($verdict === 'yes' || $verdict === 'maybe'): ?>
    <div class="report__cta">
      <h2>Want to pressure-test this with a senior operator?</h2>
      <p class="lead">A 30-minute Strategy Session: we challenge the diagnosis with your real numbers and map your 90-day intervention. Fee credited against any subsequent engagement.</p>
      <a href="<?= e(CALENDLY_URL) ?>" class="btn btn--primary btn--lg" style="margin-top: var(--s-6);">Book the £950 Strategy Session <span class="arrow">→</span></a>
    </div>
  <?php endif; ?>

</main>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
