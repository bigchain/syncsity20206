<?php
/**
 * Syncdemo — home page candidate using universal site partials.
 *
 * This is the .php twin of syncdemo/index.html. They render identically
 * but this version uses the shared site-head / site-nav / site-footer
 * partials so the universal nav + footer can be edited in ONE place.
 *
 * Path prefix is "../" because we're nested one level under site root.
 */
$page_path_prefix = '../';
$page_title       = 'Syncsity — Find Your £50M Opportunity | AI Business Transformation';
$page_description = 'In 15 minutes we uncover the hidden constraint costing you £10K+ a month and tell you exactly how to fix it. AI-powered business transformation for ambitious companies.';
$page_canonical   = 'https://syncsity.com/syncdemo/';
$page_breadcrumb  = [
    ['Home', 'https://syncsity.com/'],
    ['Demo', 'https://syncsity.com/syncdemo/'],
];

// Page-specific JSON-LD: Service catalogue + FAQ. Append to whatever
// universal schemas site-head.php already emits (Organization, WebSite,
// WebPage, BreadcrumbList).
$page_extra_jsonld = <<<'JSONLD'

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Syncsity Services",
  "itemListElement": [
    { "@type": "Service", "position": 1, "name": "AI Voice Operations", "description": "Voice agents that handle unlimited conversations 24/7 in 40+ languages with 90% cost savings.", "provider": { "@id": "https://syncsity.com/#organization" }, "url": "https://syncsity.com/solutions/voice-solutions", "serviceType": "AI Voice Automation" },
    { "@type": "Service", "position": 2, "name": "AI Sales System", "description": "Lead generation engine that finds prospects, manages outreach, books meetings, and fills pipeline automatically.", "provider": { "@id": "https://syncsity.com/#organization" }, "url": "https://syncsity.com/solutions/lead-generation", "serviceType": "AI Sales Automation" },
    { "@type": "Service", "position": 3, "name": "Process Automation", "description": "Intelligent automation that identifies and removes operational constraints without human intervention.", "provider": { "@id": "https://syncsity.com/#organization" }, "url": "https://syncsity.com/solutions/process-optimization", "serviceType": "Business Process Automation" },
    { "@type": "Service", "position": 4, "name": "Workforce Intelligence", "description": "Capture knowledge, generate training, and optimise workforce performance without adding overhead.", "provider": { "@id": "https://syncsity.com/#organization" }, "url": "https://syncsity.com/solutions/workforce-transformation", "serviceType": "Workforce Optimisation" }
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
<!--
  Page-specific styles for syncdemo home — hero, stats, pillars, why-choose,
  enterprise-services, transform-cta, cases, about, final-aha. The universal
  nav/footer/skip-link/dropdown styles live in /assets/css/site-nav.css.

  TODO: For brevity, this .php version reuses the existing index.html for
  the visible <body> markup. To migrate fully:
    1. Delete the readfile() call below
    2. Paste the index.html <body> contents (between <body> and </body>)
       directly here
    3. Replace the inline <header class="nav"> with: <?php include __DIR__ . '/../partials/site-nav.php'; ?>
    4. Replace the inline <footer class="footer-home"> with the include of site-footer.php
  Until then, this .php file is parked. The .html version is what's served.
-->
<body>
<?php include __DIR__ . '/../partials/site-nav.php'; ?>
<main id="main" aria-label="Home page content">
  <p style="padding:120px 24px;text-align:center;color:#fff;font-size:18px;">
    Page body migration pending — see comment in <code>syncdemo/index.php</code>.
    Until then, view <a href="/syncdemo/index.html" style="color:#3385DF;">/syncdemo/index.html</a>.
  </p>
</main>
<?php include __DIR__ . '/../partials/site-footer.php'; ?>
