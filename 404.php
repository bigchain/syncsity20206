<?php
declare(strict_types=1);
define('SYNC_ROOT', __DIR__);
require_once SYNC_ROOT . '/lib/config.php';

http_response_code(404);

$pageTitle = 'Page not found — Syncsity';
$pageDesc  = 'The page you were looking for has moved or never existed.';

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<section class="section section--hero">
  <div class="bg-glow"></div>
  <div class="container container--md" style="text-align:center; position:relative;">
    <span class="eyebrow">404</span>
    <h1 style="margin: var(--s-4) 0 var(--s-6);">That page isn't here.</h1>
    <p class="lead">It probably never was. Or we moved it. Either way — let's get you somewhere useful.</p>
    <div class="hero__cta-row" style="justify-content:center; margin-top: var(--s-8);">
      <a href="/" class="btn btn--ghost btn--lg">← Home</a>
      <a href="/assess" class="btn btn--primary btn--lg">Take the assessment <span class="arrow">→</span></a>
    </div>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
