<?php
/**
 * /blog — Insights coming-soon landing.
 *
 * The full 5-article launch is in progress. Until then this page exists so the
 * Resources menu and footer "Blog" links don't 404 — and so visitors can opt
 * in to be notified when the first articles drop.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';

$pageTitle = 'Insights — Syncsity';
$pageDesc  = 'Operator-grade essays on constraint, leverage, and the patterns we keep seeing inside mid-market UK & US businesses. Launching shortly.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<link rel="canonical" href="<?= e(APP_URL) ?>/blog">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:image" content="<?= e(APP_URL) ?>/assets/img/og-image.svg">
<meta name="twitter:card" content="summary_large_image">
<meta name="robots" content="index, follow">
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">
<link rel="stylesheet" href="/assets/css/static-page.css">
</head>
<body>

<div class="bg-stage" aria-hidden="true"></div>

<header class="nav">
  <div class="nav__inner">
    <a href="/" class="nav__brand"><img src="/lovable-uploads/03f35f21-123f-4f10-84a1-f2a66d97bc2b.png" alt="Syncsity" style="height:36px;width:auto;"></a>
    <nav class="nav__links">
      <a href="/transform">Transform</a>
      <a href="/solutions">Solutions</a>
      <a href="/assess">Diagnose</a>
      <a href="/why-syncsity">Why Syncsity</a>
      <a href="/pricing">Pricing</a>
      <a href="/blog" class="is-active">Insights</a>
    </nav>
    <div class="nav__cta">
      <a href="/auth/login" class="btn btn--ghost btn--sm" id="auth-btn">Log in</a>
      <a href="/assess" class="btn btn--primary btn--sm">Free assessment</a>
    </div>
  </div>
</header>

<main>
  <section class="section section--hero">
    <div class="container container--md">
      <span class="pill reveal">★ Insights · launching shortly</span>
      <h1 class="reveal reveal--d1" style="margin-top: 24px;">The patterns we keep finding inside mid-market businesses.</h1>
      <p class="lead reveal reveal--d2" style="margin-top: 20px;">
        We're writing five long-form essays — operator-grade, data-led, no LinkedIn pablum.
        The first three drop this month. While you wait, the most useful thing on the site
        isn't a blog post — it's the diagnostic itself.
      </p>
      <div style="display:flex; gap:14px; flex-wrap:wrap; justify-content:center; margin-top: 32px;" class="reveal reveal--d3">
        <a href="/assess" class="btn btn--primary btn--lg">Take the free Aha! Assessment →</a>
        <a href="/contact.html" class="btn btn--ghost btn--lg">Tell us what to write about</a>
      </div>
    </div>
  </section>

  <section class="section section--tight" style="padding-top: 0;">
    <div class="container container--md">
      <div class="card reveal" style="padding: 40px;">
        <h2 style="margin-bottom: 16px;">What's coming</h2>
        <p class="muted" style="margin-bottom: 28px;">Each essay names a specific pattern, quantifies the cost, and prescribes the move — built from real diagnoses, not stock content.</p>

        <div style="display:grid; gap:20px;">
          <?php
          $previews = [
            [
              'tag'   => 'Constraint',
              'title' => 'The £10K Hidden Constraint Pattern',
              'sub'   => 'Why 73% of mid-market UK service businesses lose money to the same bottleneck',
              'date'  => 'Coming this month',
            ],
            [
              'tag'   => 'Founder leverage',
              'title' => 'The Founder Bottleneck',
              'sub'   => 'How to tell if you are the constraint — and three tests anyone can run before lunch',
              'date'  => 'Coming this month',
            ],
            [
              'tag'   => 'AI ops',
              'title' => 'AI Voice Operations vs Human Call Centres',
              'sub'   => 'The real numbers after 12 months in UK service businesses',
              'date'  => 'Coming this month',
            ],
            [
              'tag'   => 'Counter-intuitive',
              'title' => 'The Capacity Wall',
              'sub'   => 'When "get more leads" is the worst advice you can take',
              'date'  => 'Coming next month',
            ],
            [
              'tag'   => 'Margin',
              'title' => 'The Hidden Subsidy',
              'sub'   => '7 ways UK service businesses quietly subsidise their worst clients (and don\'t notice)',
              'date'  => 'Coming next month',
            ],
          ];
          foreach ($previews as $p): ?>
            <div style="display:grid; grid-template-columns: 120px 1fr auto; gap:20px; align-items:start; padding-bottom:20px; border-bottom:1px solid var(--border);">
              <span class="eyebrow" style="margin-top:4px;"><?= e($p['tag']) ?></span>
              <div>
                <h3 style="font-size:1.1rem; margin-bottom:6px;"><?= e($p['title']) ?></h3>
                <p class="muted" style="font-size:0.95rem;"><?= e($p['sub']) ?></p>
              </div>
              <span class="dim mono" style="font-size:0.78rem; white-space:nowrap;"><?= e($p['date']) ?></span>
            </div>
          <?php endforeach; ?>
        </div>

        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--border); text-align:center;">
          <p class="muted" style="margin-bottom: 16px;">Want to be told the moment the first essay drops? Take the free assessment — anyone with a report on file gets the launch list automatically.</p>
          <a href="/assess" class="btn btn--primary">Start the assessment →</a>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div class="container">
      <div class="footer__top">
        <div>
          <img src="/lovable-uploads/03f35f21-123f-4f10-84a1-f2a66d97bc2b.png" alt="Syncsity" style="height:42px;margin-bottom:14px;">
          <p class="muted" style="font-size:14px;max-width:340px;">Empowering ambitious operators through operational excellence and strategic AI implementation.</p>
        </div>
        <div><h4>Quick Links</h4><ul><li><a href="/">Home</a></li><li><a href="/why-syncsity">Why Syncsity</a></li><li><a href="/pricing">Pricing</a></li><li><a href="/about-us">About</a></li><li><a href="/contact.html">Contact</a></li><li><a href="/blog">Blog</a></li></ul></div>
        <div><h4>Our Solutions</h4><ul><li><a href="/solutions/voice-solutions">AI Voice</a></li><li><a href="/solutions/lead-generation">AI Sales</a></li><li><a href="/solutions/process-optimization">Process Automation</a></li><li><a href="/solutions/workforce-transformation">Workforce Intelligence</a></li></ul></div>
        <div><h4>Services</h4><ul><li><a href="/transform/market-domination">Market Domination</a></li><li><a href="/transform/revenue-acceleration">Revenue Acceleration</a></li><li><a href="/transform/operational-supremacy">Operational Supremacy</a></li><li><a href="/assess">Free Assessment</a></li><li><a href="/booking.html">Strategy Session</a></li></ul></div>
      </div>
      <div class="footer__bottom">
        <span>© <span id="year">2026</span> Syncsity Ltd · London, UK · <a href="/privacy.html">Privacy</a> · <a href="/terms.html">Terms</a> · <a href="/sitemap.html">Sitemap</a> · <a href="/llms.txt">LLMs.txt</a></span>
        <span class="mono dim">Built for operators, by operators.</span>
      </div>
    </div>
  </footer>
</main>

<script>
document.getElementById('year').textContent = new Date().getFullYear();
if (/(?:^|;\s*)(?:syncsity_session|PHPSESSID)=/.test(document.cookie || '')) {
  var b = document.getElementById('auth-btn'); if (b) { b.href='/dashboard'; b.textContent='Dashboard'; }
}
</script>
</body>
</html>
