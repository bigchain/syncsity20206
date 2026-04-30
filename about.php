<?php
declare(strict_types=1);
define('SYNC_ROOT', __DIR__);
require_once SYNC_ROOT . '/lib/config.php';

$pageTitle = 'About Syncsity — Operator-led AI transformation, London';
$pageDesc  = 'Syncsity is the diagnostic-first AI studio for ambitious operators. Built by people who have run the businesses they now help.';
$activeNav = 'about';

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<section class="section section--hero">
  <div class="bg-glow"></div>
  <div class="container container--md" style="position:relative;">
    <span class="eyebrow">About</span>
    <h1 style="margin: var(--s-4) 0 var(--s-6);">We were operators before we were "an AI company".</h1>
    <p class="lead">
      Syncsity exists because most AI consulting is sold by people who've never had to make
      payroll. We've run the businesses. We've felt the constraint. We've been the bottleneck.
      And we've used AI — properly — to break out.
    </p>
    <p class="lead">
      Now we do that for ambitious operators across the UK and US. The diagnostic is free
      because we want you to feel the difference before you pay for anything.
    </p>
  </div>
</section>

<section class="section">
  <div class="container container--md">
    <div class="grid-2">
      <div class="card card--feature" data-reveal>
        <span class="eyebrow">Our belief</span>
        <h3 style="margin: var(--s-3) 0;">AI is leverage, not lipstick.</h3>
        <p class="muted">Adding AI to a broken process makes it broken faster. We diagnose the constraint first because the wrong leverage at the wrong point makes things worse, not better.</p>
      </div>
      <div class="card card--feature" data-reveal>
        <span class="eyebrow">Our standard</span>
        <h3 style="margin: var(--s-3) 0;">If we can't help, we say so.</h3>
        <p class="muted">Most agencies say yes to every brief. We say no often. Pre-revenue founders, agencies wanting white-label, anyone seeking a tool to install — we politely route elsewhere.</p>
      </div>
      <div class="card card--feature" data-reveal>
        <span class="eyebrow">Our promise</span>
        <h3 style="margin: var(--s-3) 0;">You own everything we build.</h3>
        <p class="muted">No "Syncsity platform" you can't leave. Workflows, prompts, integrations, data — all documented, all handed over, all yours. We're an accelerator, not a host.</p>
      </div>
      <div class="card card--feature" data-reveal>
        <span class="eyebrow">Our base</span>
        <h3 style="margin: var(--s-3) 0;">London. UK-time. UK-data.</h3>
        <p class="muted">London-headquartered, GDPR-aligned, ISO-27001-aware. Most clients are UK or US mid-market. We work with select global operators when there's a real fit.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-strip" data-reveal>
      <h2>The fastest way to see if we're the right fit.</h2>
      <p class="lead">Take the 15-minute diagnostic. The report alone tells you who you're dealing with.</p>
      <a href="/assess" class="btn btn--primary btn--lg" style="margin-top: var(--s-6);">Take the assessment <span class="arrow">→</span></a>
    </div>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
