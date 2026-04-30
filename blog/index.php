<?php
/**
 * /blog — listing page (and single-article router via ?slug=...).
 *
 * Articles are real PHP files at /blog/<slug>.php so they can be edited
 * directly without a CMS. This index page reads their metadata from a
 * registry below and renders the listing.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';

require_once __DIR__ . '/_articles.php';   // populates $ARTICLES

usort($ARTICLES, function ($a, $b) { return strcmp($b['date'], $a['date']); });

$pageTitle = 'Insights — Syncsity blog';
$pageDesc  = 'Operator-grade essays on constraint, leverage, and the patterns we keep seeing in mid-market UK & US businesses. Hard-earned, not LinkedIn-pablum.';
$activeNav = 'blog';
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
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&family=Source+Serif+4:wght@400;600&display=swap">
<link rel="stylesheet" href="/assets/css/core.css">
<link rel="stylesheet" href="/assets/css/components.css">
<link rel="stylesheet" href="/assets/css/pages.css">
<link rel="stylesheet" href="/assets/css/blog.css">
<script>(function(){try{var t=localStorage.getItem('syncsity-theme');if(t==='light')document.documentElement.setAttribute('data-theme','light');}catch(e){}})();</script>

<!-- Schema.org Blog -->
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'Blog',
    'name'     => 'Syncsity Insights',
    'url'      => APP_URL . '/blog',
    'publisher'=> [
        '@type' => 'Organization',
        'name'  => 'Syncsity',
        'logo'  => ['@type' => 'ImageObject', 'url' => APP_URL . '/assets/img/logo.png'],
    ],
    'blogPost' => array_map(function ($a) {
        return [
            '@type'         => 'BlogPosting',
            'headline'      => $a['title'],
            'datePublished' => $a['date'],
            'url'           => APP_URL . '/blog/' . $a['slug'],
            'author'        => ['@type' => 'Person', 'name' => $a['author']],
        ];
    }, $ARTICLES),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
</head>
<body>

<header class="blog-nav">
  <div class="container">
    <a href="/" class="blog-nav__brand">
      <img src="/assets/img/logo.png" alt="Syncsity" height="32">
    </a>
    <nav class="blog-nav__links">
      <a href="/why-us">Why Syncsity</a>
      <a href="/pricing">Pricing</a>
      <a href="/blog" class="is-active">Insights</a>
      <a href="/contact.html">Contact</a>
    </nav>
    <a href="/assess" class="blog-nav__cta">Free assessment →</a>
  </div>
</header>

<main class="blog-shell">

  <section class="blog-hero">
    <div class="container container--md">
      <span class="blog-hero__eyebrow">Syncsity Insights</span>
      <h1 class="blog-hero__title">Hard-earned patterns from inside the businesses no one writes about.</h1>
      <p class="blog-hero__lede">
        Most "AI for business" content is regurgitated press releases. These essays are
        what we actually see when we open the books — the patterns that repeat across
        agencies, professional services, e-commerce ops, SaaS scale-ups. Bring tea.
      </p>
    </div>
  </section>

  <section class="blog-list">
    <div class="container container--md">
      <?php
      $featured = $ARTICLES[0] ?? null;
      $rest     = array_slice($ARTICLES, 1);
      ?>

      <?php if ($featured): ?>
        <a href="/blog/<?= e($featured['slug']) ?>" class="blog-card blog-card--featured">
          <div class="blog-card__cover" style="background-image: linear-gradient(135deg, <?= e($featured['accent_a']) ?> 0%, <?= e($featured['accent_b']) ?> 100%);">
            <span class="blog-card__cover-eyebrow"><?= e($featured['category']) ?></span>
          </div>
          <div class="blog-card__body">
            <span class="blog-card__meta">
              <?= e(date('j F Y', strtotime($featured['date']))) ?>
              · <?= e($featured['reading_time']) ?> min read
              · <?= e($featured['author']) ?>
            </span>
            <h2><?= e($featured['title']) ?></h2>
            <p><?= e($featured['excerpt']) ?></p>
            <span class="blog-card__cta">Read the essay →</span>
          </div>
        </a>
      <?php endif; ?>

      <div class="blog-grid">
        <?php foreach ($rest as $a): ?>
          <a href="/blog/<?= e($a['slug']) ?>" class="blog-card">
            <div class="blog-card__cover" style="background-image: linear-gradient(135deg, <?= e($a['accent_a']) ?> 0%, <?= e($a['accent_b']) ?> 100%);">
              <span class="blog-card__cover-eyebrow"><?= e($a['category']) ?></span>
            </div>
            <div class="blog-card__body">
              <span class="blog-card__meta">
                <?= e(date('j F Y', strtotime($a['date']))) ?>
                · <?= e($a['reading_time']) ?> min
              </span>
              <h3><?= e($a['title']) ?></h3>
              <p><?= e($a['excerpt']) ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="blog-cta">
    <div class="container container--md">
      <div class="cta-strip">
        <h2>Want your business analysed like this?</h2>
        <p class="lead">Take the free 15-minute Aha! Assessment. The same diagnostic that informs every essay above — applied to your business.</p>
        <a href="/assess" class="btn btn--primary btn--lg" style="margin-top: var(--s-6);">Start my Aha! Assessment <span class="arrow">→</span></a>
      </div>
    </div>
  </section>

</main>

<footer class="blog-footer">
  <div class="container">
    <p>© <?= date('Y') ?> Syncsity Ltd · London, UK · <a href="/privacy">Privacy</a> · <a href="/terms">Terms</a> · <a href="/llms.txt">LLMs.txt</a></p>
  </div>
</footer>

<script src="/assets/js/core.js" defer></script>
</body>
</html>
