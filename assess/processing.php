<?php
/**
 * /assess/processing?id=<assessment_id>
 *
 * Polls /api/report-status until the report is 'ready', then redirects.
 * Auth: must be logged in as the owner OR carry the share token.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/functions.php';

$id    = (int)($_GET['id'] ?? 0);
$token = clean($_GET['t']  ?? '', 64);

if ($id <= 0) redirect('/assess');

$assessment = DB::one("SELECT id, user_id, status, report_share_token FROM assessments WHERE id = ? LIMIT 1", [$id]);
if (!$assessment) redirect('/assess');

$ownedByUser = !empty($_SESSION['user_id']) && (int)$_SESSION['user_id'] === (int)$assessment['user_id'];
$tokenMatch  = $token !== '' && $token === (string)$assessment['report_share_token'];
if (!$ownedByUser && !$tokenMatch) {
    redirect('/auth/login?redirect=' . urlencode('/assess/processing?id=' . $id));
}

// Already ready? Jump straight in.
if ($assessment['status'] === 'ready') {
    redirect('/assess/report?t=' . urlencode((string)$assessment['report_share_token']));
}

$pageTitle = 'Generating your report — Syncsity';
$pageDesc  = 'Your Revenue Intelligence Report is being researched and written.';

include SYNC_ROOT . '/partials/head.php';
?>

<main class="auth-shell">
  <div class="auth-card" style="max-width: 540px; text-align:center;">
    <div class="auth-brand" style="justify-content:center;">
      <svg width="28" height="28" viewBox="0 0 32 32" aria-hidden="true">
        <circle cx="16" cy="16" r="13" stroke="url(#ph1)" stroke-width="2.4" fill="none"/>
        <path d="M9 19c1.6 1.2 4 2 6.5 2 3 0 5.5-1.6 5.5-4 0-2-1.6-3.2-4.6-4l-2-.5c-3-.7-4.9-2.3-4.9-4.8C9.5 5.2 11.8 4 14.5 4c2.2 0 4.2.8 5.5 2" stroke="url(#ph2)" stroke-width="2.4" stroke-linecap="round" fill="none"/>
        <defs>
          <linearGradient id="ph1" x1="3" y1="3" x2="29" y2="29" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#3385DF"/><stop offset="1" stop-color="#FCA311"/></linearGradient>
          <linearGradient id="ph2" x1="9" y1="4" x2="21" y2="21" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#FCA311"/><stop offset="1" stop-color="#3385DF"/></linearGradient>
        </defs>
      </svg>
      <span>Syncsity</span>
    </div>

    <span class="eyebrow" style="display:block; margin-bottom: var(--s-3);">Reading you. Writing your report.</span>
    <h1 style="font-size: 1.6rem; margin-bottom: var(--s-3);">Hold on a moment.</h1>
    <p class="lead" id="status-message">Lining up the analysis…</p>

    <!-- Progress bar -->
    <div style="margin: var(--s-8) 0; height: 6px; background: var(--surface); border-radius: 999px; overflow: hidden;">
      <div id="progress-bar" style="height: 100%; width: 8%; background: linear-gradient(90deg, var(--blue-400), var(--orange)); transition: width 600ms cubic-bezier(0.22, 1, 0.36, 1);"></div>
    </div>

    <ol style="text-align:left; display:grid; gap: var(--s-3); padding-left: 0; list-style:none;">
      <li id="step-research"  class="step-line">Reading your business and your industry</li>
      <li id="step-analyse"   class="step-line">Naming the constraint, quantifying the leak</li>
      <li id="step-write"     class="step-line">Writing your Revenue Intelligence Report</li>
    </ol>

    <p class="dim" style="font-size: 0.85rem; margin-top: var(--s-8);">
      Average run: 60–90 seconds. Your report will also be emailed to you when it's ready —
      so you can close this tab if you'd rather come back later.
    </p>
  </div>
</main>

<style>
  .step-line {
    display:flex; align-items:center; gap:12px;
    padding: 10px 14px; border-radius: 8px;
    color: var(--text-dim); font-size: 0.95rem;
    border: 1px solid var(--border);
    transition: color 240ms, border-color 240ms, background-color 240ms;
  }
  .step-line::before {
    content: ''; width: 8px; height: 8px; border-radius: 50%;
    background: var(--text-dim);
    flex-shrink: 0;
    transition: background-color 240ms, box-shadow 240ms;
  }
  .step-line.is-active { color: var(--text); border-color: var(--accent); background: rgba(51,133,223,0.08); }
  .step-line.is-active::before { background: var(--accent); box-shadow: 0 0 0 4px rgba(51,133,223,0.18); animation: pulse 1.4s ease-in-out infinite; }
  .step-line.is-done   { color: var(--text-muted); border-color: var(--green); }
  .step-line.is-done::before { background: var(--green); }
  @keyframes pulse { 0%,100%{transform:scale(1);opacity:1;} 50%{transform:scale(1.4);opacity:0.5;} }
</style>

<script>
(function(){
  var id    = <?= json_encode($id) ?>;
  var token = <?= json_encode($token) ?>;
  var msg   = document.getElementById('status-message');
  var bar   = document.getElementById('progress-bar');
  var steps = {
    research: document.getElementById('step-research'),
    analyse:  document.getElementById('step-analyse'),
    write:    document.getElementById('step-write'),
  };
  var statusToStep = {
    queued:      'research',
    researching: 'research',
    analysing:   'analyse',
    writing:     'write',
    ready:       'done',
    failed:      'failed',
  };
  function renderStep(s) {
    Object.keys(steps).forEach(function(k){ steps[k].className = 'step-line'; });
    if (s === 'research') steps.research.classList.add('is-active');
    if (s === 'analyse')  { steps.research.classList.add('is-done'); steps.analyse.classList.add('is-active'); }
    if (s === 'write')    { steps.research.classList.add('is-done'); steps.analyse.classList.add('is-done'); steps.write.classList.add('is-active'); }
    if (s === 'done')     { Object.keys(steps).forEach(function(k){ steps[k].classList.add('is-done'); }); }
  }
  var attempts = 0, maxAttempts = 90; // ~3 min total
  function poll() {
    attempts++;
    fetch('/api/report-status?id=' + id + '&t=' + encodeURIComponent(token), { credentials: 'same-origin' })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (!res) return;
        if (res.message) msg.textContent = res.message;
        if (typeof res.progress === 'number') bar.style.width = res.progress + '%';
        var step = statusToStep[res.status] || 'research';
        renderStep(step);
        if (res.status === 'ready' && res.redirect) {
          setTimeout(function(){ window.location.href = res.redirect; }, 700);
          return;
        }
        if (res.status === 'failed') {
          msg.innerHTML = "Something went wrong. We've been alerted. Please <a href=\"/contact?subject=General\">contact us</a> and we'll generate it manually.";
          return;
        }
        if (attempts < maxAttempts) {
          setTimeout(poll, 2200);
        } else {
          msg.innerHTML = "This is taking longer than usual. We've emailed you — open the email when it arrives, the report will be linked there.";
        }
      })
      .catch(function(){
        if (attempts < maxAttempts) setTimeout(poll, 3500);
      });
  }
  poll();
})();
</script>

<script src="/assets/js/core.js" defer></script>
</body>
</html>
