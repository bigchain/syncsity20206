<?php
/**
 * Privacy Policy — migrated from privacy.html to use universal partials.
 */

$page_path_prefix = '/';
$page_title       = 'Privacy Policy | Syncsity';
$page_description = 'How Syncsity collects, uses, and protects your data. UK GDPR + Data Protection Act 2018 aligned. No third-party trackers, no data selling.';
$page_canonical   = 'https://syncsity.com/privacy.php';
$page_modified    = '2026-05-01T00:00:00Z';
$page_breadcrumb  = [
    ['Home', 'https://syncsity.com/'],
    ['Privacy Policy', 'https://syncsity.com/privacy.php'],
];

$page_extra_jsonld = <<<'JSONLD'

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "@id": "https://syncsity.com/privacy.php#legal",
  "name": "Syncsity Privacy Policy",
  "description": "UK GDPR-aligned plain-English privacy policy.",
  "lastReviewed": "2026-05-01",
  "reviewedBy": { "@id": "https://syncsity.com/#organization" },
  "specialty": "Privacy Policy",
  "inLanguage": "en-GB"
}
</script>
JSONLD;

include __DIR__ . '/partials/site-head.php';
?>

<style>
.legal-hero { padding: 56px 0 32px; border-bottom: 1px solid var(--border); }
.legal-hero .eyebrow {
  display: inline-block; padding: 4px 12px;
  background: rgba(252,163,17,0.14); color: var(--orange);
  border-radius: 9999px; font-size: 12px; font-weight: 600;
  letter-spacing: 0.06em; text-transform: uppercase;
}
.legal-hero h1 {
  margin: 16px 0 12px; font-size: clamp(34px, 4.4vw, 48px);
  font-weight: 700; color: #fff; letter-spacing: -0.02em;
}
.legal-hero .lead { color: var(--text-muted); font-size: 16px; max-width: 640px; }
.legal-body { padding: 48px 0 80px; }
.prose { color: var(--text-muted); font-size: 15.5px; line-height: 1.7; }
.prose h2 { color: #fff; font-size: 22px; font-weight: 700; margin: 36px 0 12px; letter-spacing: -0.01em; }
.prose h2:first-child { margin-top: 0; }
.prose p { margin: 0 0 14px; }
.prose ul { margin: 0 0 16px; padding-left: 22px; }
.prose li { margin-bottom: 8px; }
.prose a { color: var(--blue-400); text-decoration: underline; text-underline-offset: 3px; }
.prose a:hover { color: #fff; }
.prose strong { color: #fff; }
.prose em { color: rgba(255,255,255,0.85); }
</style>
</head>
<body>

<?php include __DIR__ . '/partials/site-nav.php'; ?>

<main id="main" aria-label="Privacy Policy">

  <section class="legal-hero">
    <div class="container container--md">
      <span class="eyebrow">Legal</span>
      <h1>Privacy Policy</h1>
      <p class="lead">Plain-English. Last updated 1 May 2026. UK GDPR + Data Protection Act 2018 aligned.</p>
    </div>
  </section>

  <section class="legal-body">
    <div class="container container--md">
      <article class="prose">
        <h2>Who we are</h2>
        <p>Syncsity Ltd ("Syncsity", "we", "us") is a UK-incorporated company providing AI-driven business assessment and transformation services. The data controller for this site is Syncsity Ltd. Our contact email is <a href="mailto:edward@syncsity.com">edward@syncsity.com</a>.</p>

        <h2>What we collect</h2>
        <ul>
          <li><strong>Email address</strong> &mdash; when you submit the assessment, request a magic link, or contact us.</li>
          <li><strong>Assessment answers</strong> &mdash; the responses you give in the conversational form.</li>
          <li><strong>Hashed IP and user agent</strong> &mdash; for rate limiting and abuse prevention. Raw IPs are never stored; we hash them with a server-side salt.</li>
          <li><strong>Anonymous analytics</strong> &mdash; page views, referrers, and aggregate device info. No third-party advertising trackers.</li>
        </ul>

        <h2>Why we collect it</h2>
        <ul>
          <li>To generate your Revenue Intelligence Report.</li>
          <li>To email you the report and any follow-up you request.</li>
          <li>To prevent abuse (rate limiting, magic-link expiry).</li>
          <li>To improve the service in aggregate.</li>
        </ul>
        <p>The lawful basis is legitimate interest (responding to your enquiry / providing the service you requested) and consent for any subsequent marketing communications, which you can withdraw at any time by replying "unsubscribe" to any email.</p>

        <h2>Who we share it with</h2>
        <p>We share the minimum necessary data with the following processors, each under data-processing agreements:</p>
        <ul>
          <li><strong>OpenRouter / underlying LLM providers</strong> &mdash; to generate your report. Your business answers and a non-identifying business profile are sent. We instruct providers not to train on this data where the option exists.</li>
          <li><strong>Google (Sheets / Workspace)</strong> &mdash; for an internal lead trail. Limited fields; access tightly scoped.</li>
          <li><strong>Hosting (Hetzner GmbH, EU)</strong> &mdash; server infrastructure, EU-based.</li>
        </ul>
        <p>We do not sell your data, ever. We do not use it for advertising. We do not run third-party retargeting pixels.</p>

        <h2>How long we keep it</h2>
        <ul>
          <li>Assessments and reports &mdash; <strong>3 years</strong> from your last interaction.</li>
          <li>Magic-link tokens &mdash; <strong>15 minutes</strong>, then deleted.</li>
          <li>Audit log entries &mdash; <strong>12 months</strong>.</li>
          <li>Hashed IPs &mdash; <strong>90 days</strong>.</li>
        </ul>

        <h2>Your rights</h2>
        <p>Under UK GDPR you have the right to access, correct, delete, or export your data, and to object to processing. Email <a href="mailto:edward@syncsity.com">edward@syncsity.com</a> with the subject line <em>"GDPR request"</em> and we'll respond within 30 days. You can also complain to the ICO (<a href="https://ico.org.uk" target="_blank" rel="noopener">ico.org.uk</a>).</p>

        <h2>Cookies</h2>
        <p>We use only first-party, strictly-necessary cookies: a session cookie to keep you logged in (HttpOnly, SameSite=Lax), a CSRF token cookie, and a theme preference. No third-party trackers.</p>

        <h2>Changes</h2>
        <p>We update this page when our practices change and reset the "last updated" date. Significant changes will be flagged on the home page for at least 14 days.</p>
      </article>
    </div>
  </section>

</main>

<?php include __DIR__ . '/partials/site-footer.php'; ?>
