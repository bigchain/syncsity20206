<?php
declare(strict_types=1);
define('SYNC_ROOT', __DIR__);
require_once SYNC_ROOT . '/lib/config.php';

$pageTitle = 'Why Syncsity — Operator-led AI transformation, no slideware';
$pageDesc  = 'Most "AI consultancies" sell tools. We diagnose first, build second, automate last. Here\'s why ambitious operators choose Syncsity.';
$activeNav = 'why-us';

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<section class="section section--hero">
  <div class="bg-glow"></div>
  <div class="container container--md" style="text-align:center; position:relative;">
    <span class="eyebrow">Why Syncsity</span>
    <h1 style="margin: var(--s-4) 0 var(--s-6);">We're not a consultancy. We're an operator's leverage.</h1>
    <p class="lead">
      Big consulting will sell you a deck. AI agencies will sell you a tool. Both will leave you with
      something pretty that doesn't move the number. We're the third option.
    </p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="grid-3">
      <div class="card card--feature" data-reveal>
        <span class="pill pill--accent">Diagnosis first</span>
        <h3 style="margin: var(--s-4) 0 var(--s-3);">Constraint, not cosmetics</h3>
        <p class="muted">Every engagement starts with naming the actual bottleneck. We don't deploy AI until we know it'll multiply the right thing — and we tell you when it won't.</p>
      </div>
      <div class="card card--feature" data-reveal>
        <span class="pill pill--orange">Built with you</span>
        <h3 style="margin: var(--s-4) 0 var(--s-3);">No black boxes</h3>
        <p class="muted">You own the systems we build. Every workflow, every prompt, every integration is documented and handed over. We're an accelerant, not a dependency.</p>
      </div>
      <div class="card card--feature" data-reveal>
        <span class="pill">Operator-led</span>
        <h3 style="margin: var(--s-4) 0 var(--s-3);">Founders &amp; CEOs only</h3>
        <p class="muted">You'll only ever speak to a senior operator who has done this in real businesses. No analysts, no juniors with a slide template, no offshored grunt work.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container container--md">
    <div class="section-head" data-reveal>
      <span class="eyebrow">Compared to</span>
      <h2>The honest comparison</h2>
    </div>
    <div class="card" data-reveal>
      <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:0.95rem;">
          <thead>
            <tr style="text-align:left; color: var(--text-muted); font-family: var(--font-mono); font-size: 0.78rem; letter-spacing: 0.14em; text-transform: uppercase;">
              <th style="padding: var(--s-3) var(--s-4); border-bottom: 1px solid var(--border);"></th>
              <th style="padding: var(--s-3) var(--s-4); border-bottom: 1px solid var(--border);">Big consulting</th>
              <th style="padding: var(--s-3) var(--s-4); border-bottom: 1px solid var(--border);">AI agency</th>
              <th style="padding: var(--s-3) var(--s-4); border-bottom: 1px solid var(--border); color: var(--accent);">Syncsity</th>
            </tr>
          </thead>
          <tbody class="muted">
            <?php
            $rows = [
              ['Starts with diagnosis',         'Sometimes',       'Rarely',          'Always'],
              ['Senior operator on every call', 'After year 1',    'No',              'Always'],
              ['You own what we build',         'Licensed back',   'Vendor lock-in',  'Yes — fully'],
              ['Charges for the diagnosis',     '£25K+',           '£3-10K',          'Free'],
              ['Time to first revenue impact',  '6-12 months',     '3-6 months',      '30 days'],
              ['Walks away when wrong fit',     'No',              'No',              'Yes'],
            ];
            foreach ($rows as $r): ?>
              <tr>
                <td style="padding: var(--s-3) var(--s-4); border-bottom: 1px solid var(--border); color: var(--text); font-weight: 500;"><?= e($r[0]) ?></td>
                <td style="padding: var(--s-3) var(--s-4); border-bottom: 1px solid var(--border);"><?= e($r[1]) ?></td>
                <td style="padding: var(--s-3) var(--s-4); border-bottom: 1px solid var(--border);"><?= e($r[2]) ?></td>
                <td style="padding: var(--s-3) var(--s-4); border-bottom: 1px solid var(--border); color: var(--accent); font-weight: 600;"><?= e($r[3]) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-strip" data-reveal>
      <h2>The diagnosis is free. The clarity is the gift.</h2>
      <p class="lead">If we can help, the report will make it obvious. If we can't, you still walk away knowing exactly what's blocking you.</p>
      <a href="/assess" class="btn btn--primary btn--lg" style="margin-top: var(--s-6);">Start my Aha! Assessment <span class="arrow">→</span></a>
    </div>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
