<?php
/**
 * HTML sitemap — migrated from sitemap.html.
 * (Distinct from /sitemap.xml which is the search-engine XML sitemap.)
 */

$page_path_prefix = '/';
$page_title       = 'Sitemap | Syncsity';
$page_description = 'Every page on Syncsity, organised by section. Search engines: /sitemap.xml. AI / LLM crawlers: /llms.txt.';
$page_canonical   = 'https://syncsity.com/sitemap.php';
$page_breadcrumb  = [
    ['Home', 'https://syncsity.com/'],
    ['Sitemap', 'https://syncsity.com/sitemap.php'],
];

include __DIR__ . '/partials/site-head.php';
?>

<link rel="alternate" type="application/xml" href="/sitemap.xml" title="XML sitemap">
<style>
.sitemap-hero { padding: 56px 0 32px; border-bottom: 1px solid var(--border); }
.sitemap-hero .eyebrow {
  display: inline-block; padding: 4px 12px;
  background: rgba(51,133,223,0.14); color: var(--blue-400);
  border-radius: 9999px; font-size: 12px; font-weight: 600;
  letter-spacing: 0.06em; text-transform: uppercase;
}
.sitemap-hero h1 {
  margin: 16px 0 12px; font-size: clamp(34px, 4.4vw, 48px);
  font-weight: 700; color: #fff; letter-spacing: -0.02em;
}
.sitemap-hero .lead { color: var(--text-muted); font-size: 16px; max-width: 640px; }
.sitemap-hero .lead a { color: var(--blue-400); text-decoration: underline; }
.sitemap-body { padding: 48px 0 80px; }
.sitemap-grid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 32px;
}
.sitemap-col h2 {
  font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase;
  color: var(--text-dim); font-family: 'JetBrains Mono', monospace;
  margin: 0 0 16px;
}
.sitemap-col ul { display: flex; flex-direction: column; gap: 8px; list-style: none; padding: 0; margin: 0; }
.sitemap-col a {
  color: #fff; font-size: 15px;
  text-decoration: none; padding: 4px 0;
  border-bottom: 1px solid transparent;
  transition: border-color 150ms ease, color 150ms ease;
}
.sitemap-col a:hover { color: var(--blue-400); border-bottom-color: var(--blue-400); }
</style>
</head>
<body>

<?php include __DIR__ . '/partials/site-nav.php'; ?>

<main id="main" aria-label="Sitemap">

  <section class="sitemap-hero">
    <div class="container container--md">
      <span class="eyebrow">Sitemap</span>
      <h1>Everything on Syncsity, in one list.</h1>
      <p class="lead">Search engines: <a href="/sitemap.xml">/sitemap.xml</a>. AI / LLM crawlers: <a href="/llms.txt">/llms.txt</a>.</p>
    </div>
  </section>

  <section class="sitemap-body">
    <div class="container container--md">
      <div class="sitemap-grid">

        <div class="sitemap-col">
          <h2>Marketing</h2>
          <ul>
            <li><a href="/">Home</a></li>
            <li><a href="/why-syncsity">Why Syncsity</a></li>
            <li><a href="/pricing">Pricing</a></li>
            <li><a href="/about-us">About</a></li>
            <li><a href="/blog">Insights</a></li>
          </ul>
        </div>

        <div class="sitemap-col">
          <h2>Solutions</h2>
          <ul>
            <li><a href="/solutions/voice-solutions">AI Voice Operations</a></li>
            <li><a href="/solutions/lead-generation">AI Sales System</a></li>
            <li><a href="/solutions/process-optimization">Process Automation</a></li>
            <li><a href="/solutions/workforce-transformation">Workforce Intelligence</a></li>
          </ul>
        </div>

        <div class="sitemap-col">
          <h2>Transform</h2>
          <ul>
            <li><a href="/transform/market-domination">Market Domination</a></li>
            <li><a href="/transform/revenue-acceleration">Revenue Acceleration</a></li>
            <li><a href="/transform/operational-supremacy">Operational Supremacy</a></li>
          </ul>
        </div>

        <div class="sitemap-col">
          <h2>Product</h2>
          <ul>
            <li><a href="/assess">Free Aha! Assessment</a></li>
            <li><a href="/booking.php">Strategy Session (£950)</a></li>
            <li><a href="/dashboard">Dashboard (login required)</a></li>
            <li><a href="/calculators">ROI Calculator</a></li>
          </ul>
        </div>

        <div class="sitemap-col">
          <h2>Account</h2>
          <ul>
            <li><a href="/auth/login">Log in (magic link)</a></li>
            <li><a href="/dashboard">Dashboard</a></li>
          </ul>
        </div>

        <div class="sitemap-col">
          <h2>Contact</h2>
          <ul>
            <li><a href="/contact.php">Contact form</a></li>
            <li><a href="/demo.php">Request a demo</a></li>
            <li><a href="/booking.php">Book a session</a></li>
            <li><a href="mailto:edward@syncsity.com">edward@syncsity.com</a></li>
          </ul>
        </div>

        <div class="sitemap-col">
          <h2>Legal</h2>
          <ul>
            <li><a href="/privacy.php">Privacy Policy</a></li>
            <li><a href="/terms.php">Terms of Service</a></li>
            <li><a href="/sitemap.xml">XML Sitemap</a></li>
            <li><a href="/llms.txt">LLMs.txt</a></li>
          </ul>
        </div>

      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . '/partials/site-footer.php'; ?>
