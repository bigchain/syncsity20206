<?php
/**
 * Syncdemo — partials test harness.
 *
 * This is the .php sibling of syncdemo/index.html. It exists to prove the
 * universal site partials (site-head, site-nav, site-footer) wire together
 * cleanly and to act as the copy-and-modify template for new marketing
 * pages.
 *
 * To use this pattern on a new page:
 *   1. Copy this file
 *   2. Update the $page_* vars below
 *   3. Replace the <main> body with your page's content
 *   4. (Optional) Add page-specific JSON-LD via $page_extra_jsonld
 *   5. (Optional) Add page-specific <style> just before </head>
 *
 * The visible /syncdemo/ home demo is still served from index.html. This
 * file is parked at /syncdemo/index.php so the .htaccess DirectoryIndex
 * order (index.html before index.php) keeps the .html serving by default.
 */

$page_path_prefix = '../';
$page_title       = 'Syncsity — Find Your £50M Opportunity | AI Business Transformation';
$page_description = 'In 15 minutes we uncover the hidden constraint costing you £10K+ a month and tell you exactly how to fix it. AI-powered business transformation for ambitious companies.';
$page_canonical   = 'https://syncsity.com/syncdemo/';
$page_breadcrumb  = [
    ['Home', 'https://syncsity.com/'],
    ['Demo', 'https://syncsity.com/syncdemo/'],
];

// Page-specific JSON-LD: Service catalogue + FAQ
$page_extra_jsonld = <<<'JSONLD'

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Syncsity Services",
  "itemListElement": [
    { "@type": "Service", "position": 1, "name": "AI Voice Operations", "description": "Voice agents that handle unlimited conversations 24/7 in 40+ languages with 90% cost savings.", "provider": { "@id": "https://syncsity.com/#organization" }, "url": "https://syncsity.com/solutions/voice-solutions" },
    { "@type": "Service", "position": 2, "name": "AI Sales System", "description": "Lead generation engine that finds prospects, manages outreach, books meetings, and fills pipeline automatically.", "provider": { "@id": "https://syncsity.com/#organization" }, "url": "https://syncsity.com/solutions/lead-generation" },
    { "@type": "Service", "position": 3, "name": "Process Automation", "description": "Intelligent automation that identifies and removes operational constraints without human intervention.", "provider": { "@id": "https://syncsity.com/#organization" }, "url": "https://syncsity.com/solutions/process-optimization" },
    { "@type": "Service", "position": 4, "name": "Workforce Intelligence", "description": "Capture knowledge, generate training, and optimise workforce performance without adding overhead.", "provider": { "@id": "https://syncsity.com/#organization" }, "url": "https://syncsity.com/solutions/workforce-transformation" }
  ]
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    { "@type": "Question", "name": "What is Syncsity?", "acceptedAnswer": { "@type": "Answer", "text": "Syncsity is an AI business transformation studio. In 15 minutes we uncover the hidden operational constraint costing you £10K+ a month and show you exactly how to fix it — even if you never speak to us again." } },
    { "@type": "Question", "name": "How does the AI Aha! Assessment work?", "acceptedAnswer": { "@type": "Answer", "text": "A 21-question conversational form takes about 10 minutes. Our AI engine then produces a free, deeply personalised Revenue Intelligence Report identifying the single hidden constraint blocking your growth and the specific steps to fix it." } },
    { "@type": "Question", "name": "What services does Syncsity offer?", "acceptedAnswer": { "@type": "Answer", "text": "Four core solutions: AI Voice Operations, AI Sales System, Process Automation, and Workforce Intelligence." } },
    { "@type": "Question", "name": "How long until I see results?", "acceptedAnswer": { "@type": "Answer", "text": "30 days to first results. Clients typically see 40% efficiency gains, 3x ROI in year one, and 90% cost reduction in automated functions." } },
    { "@type": "Question", "name": "Where does Syncsity operate?", "acceptedAnswer": { "@type": "Answer", "text": "Headquartered in the UK, serving clients across the United Kingdom, United States, United Arab Emirates, Canada, and Australia." } }
  ]
}
</script>
JSONLD;

include __DIR__ . '/../partials/site-head.php';
?>

<style>
/* Page-specific styles only — universal nav/footer/skip-link/overflow guard
   are loaded by site-head.php via site-nav.css */
.partials-test {
  min-height: calc(100vh - 200px);
  display: flex; align-items: center; justify-content: center;
  padding: 80px 24px;
  background: linear-gradient(180deg, #0a1022 0%, #050814 100%);
  color: #fff; text-align: center;
}
.partials-test__inner { max-width: 640px; }
.partials-test h1 {
  font-size: clamp(28px, 4vw, 44px); font-weight: 700;
  margin: 0 0 16px; letter-spacing: -0.02em;
}
.partials-test p {
  color: rgba(229,231,235,0.85); font-size: 16px;
  line-height: 1.55; margin: 0 0 24px;
}
.partials-test code {
  background: rgba(255,255,255,0.06); padding: 2px 6px;
  border-radius: 4px; font-size: 13px;
}
.partials-test__cta {
  display: inline-flex; align-items: center; gap: 8px;
  background: linear-gradient(135deg, #3385DF 0%, #0066D7 100%);
  color: #fff; padding: 12px 24px; border-radius: 9999px;
  font-weight: 600; text-decoration: none;
  box-shadow: 0 8px 22px rgba(51,133,223,0.32);
}
.partials-test ul {
  text-align: left; max-width: 480px; margin: 24px auto;
  padding: 0; list-style: none;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px; padding: 20px 24px;
}
.partials-test li { margin-bottom: 10px; font-size: 14px; color: rgba(229,231,235,0.85); }
.partials-test li:last-child { margin-bottom: 0; }
.partials-test li::before { content: '✓ '; color: #36B37E; font-weight: 700; }
</style>
</head>
<body>

<?php include __DIR__ . '/../partials/site-nav.php'; ?>

<main id="main" aria-label="Partials test page">
  <section class="partials-test">
    <div class="partials-test__inner">
      <h1>Universal site partials — test harness</h1>
      <p>This page is rendered using <code>partials/site-head.php</code>, <code>partials/site-nav.php</code>, and <code>partials/site-footer.php</code>. It validates that any new marketing page can adopt the universal nav and footer with a few PHP includes.</p>
      <ul>
        <li>Universal <code>&lt;head&gt;</code> meta + Organization, WebSite, WebPage, BreadcrumbList JSON-LD</li>
        <li>Page-specific JSON-LD added here: Service catalogue + FAQPage</li>
        <li>Universal sticky nav with Transform / Solutions / Resources hover dropdowns</li>
        <li>Mobile-aware (hamburger drawer at &lt;880px)</li>
        <li>Universal 4-column footer with auth-aware <em>Log in / Dashboard</em> swap</li>
        <li>Skip-to-content link, focus rings, semantic landmarks</li>
      </ul>
      <p>The visible Syncdemo home demo is at <code>/syncdemo/index.html</code>:</p>
      <a href="/syncdemo/index.html" class="partials-test__cta">View the full demo &rarr;</a>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../partials/site-footer.php'; ?>
