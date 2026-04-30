<?php
declare(strict_types=1);
define('SYNC_ROOT', __DIR__);
require_once SYNC_ROOT . '/lib/config.php';

$pageTitle = 'Privacy Policy — Syncsity';
$pageDesc  = 'How Syncsity collects, uses, and protects your data. UK GDPR-aligned.';

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<section class="section section--hero">
  <div class="container container--md">
    <span class="eyebrow">Privacy Policy</span>
    <h1 style="margin: var(--s-4) 0 var(--s-6);">Plain-English privacy.</h1>
    <p class="lead">Last updated: <?= date('j F Y') ?>. UK GDPR + Data Protection Act 2018 aligned.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container container--md" style="font-size:1.02rem; line-height:1.75; color: var(--text-muted);">
    <h2 style="color: var(--text);">Who we are</h2>
    <p>Syncsity Ltd ("Syncsity", "we", "us") is a UK-incorporated company providing AI-driven business assessment and transformation services. The data controller for this site is Syncsity Ltd. Our contact email is <a href="mailto:edward@syncsity.com">edward@syncsity.com</a>.</p>

    <h2 style="color: var(--text); margin-top: var(--s-10);">What we collect</h2>
    <ul style="display:grid; gap: var(--s-2); padding-left: var(--s-6); list-style: disc;">
      <li><strong style="color:var(--text);">Email address</strong> — when you submit the assessment, request a magic link, or contact us.</li>
      <li><strong style="color:var(--text);">Assessment answers</strong> — the responses you give in the conversational form.</li>
      <li><strong style="color:var(--text);">Hashed IP and user agent</strong> — for rate limiting and abuse prevention. Raw IPs are never stored; we hash them with a server-side salt.</li>
      <li><strong style="color:var(--text);">Anonymous analytics</strong> — page views, referrers, and aggregate device info via privacy-preserving analytics. No third-party advertising trackers.</li>
    </ul>

    <h2 style="color: var(--text); margin-top: var(--s-10);">Why we collect it</h2>
    <ul style="display:grid; gap: var(--s-2); padding-left: var(--s-6); list-style: disc;">
      <li>To generate your Revenue Intelligence Report.</li>
      <li>To email you the report and any follow-up you request.</li>
      <li>To prevent abuse (rate limiting, magic-link expiry).</li>
      <li>To improve the service in aggregate.</li>
    </ul>
    <p>The lawful basis is legitimate interest (responding to your enquiry / providing the service you requested) and consent for any subsequent marketing communications, which you can withdraw at any time by replying "unsubscribe" to any email.</p>

    <h2 style="color: var(--text); margin-top: var(--s-10);">Who we share it with</h2>
    <p>We share the minimum necessary data with the following processors, each under data-processing agreements:</p>
    <ul style="display:grid; gap: var(--s-2); padding-left: var(--s-6); list-style: disc;">
      <li><strong style="color:var(--text);">OpenRouter / underlying LLM providers</strong> — to generate your report. Your business answers and a non-identifying business profile are sent. We instruct providers not to train on this data where the option exists.</li>
      <li><strong style="color:var(--text);">Google (Sheets / Workspace)</strong> — for an internal lead trail. Limited fields; access tightly scoped.</li>
      <li><strong style="color:var(--text);">Hosting (Hetzner GmbH, EU)</strong> — server infrastructure, EU-based.</li>
    </ul>
    <p>We do not sell your data, ever. We do not use it for advertising. We do not run third-party retargeting pixels.</p>

    <h2 style="color: var(--text); margin-top: var(--s-10);">How long we keep it</h2>
    <ul style="display:grid; gap: var(--s-2); padding-left: var(--s-6); list-style: disc;">
      <li>Assessments and reports — <strong style="color:var(--text);">3 years</strong> from your last interaction.</li>
      <li>Magic-link tokens — <strong style="color:var(--text);">15 minutes</strong>, then deleted.</li>
      <li>Audit log entries — <strong style="color:var(--text);">12 months</strong>.</li>
      <li>Hashed IPs — <strong style="color:var(--text);">90 days</strong>.</li>
    </ul>

    <h2 style="color: var(--text); margin-top: var(--s-10);">Your rights</h2>
    <p>Under UK GDPR you have the right to access, correct, delete, or export your data, and to object to processing. Email <a href="mailto:edward@syncsity.com">edward@syncsity.com</a> with the subject line <em>"GDPR request"</em> and we'll respond within 30 days. You can also complain to the ICO (<a href="https://ico.org.uk">ico.org.uk</a>).</p>

    <h2 style="color: var(--text); margin-top: var(--s-10);">Cookies</h2>
    <p>We use only first-party, strictly-necessary cookies: a session cookie to keep you logged in (HttpOnly, SameSite=Lax), a CSRF token cookie, and a theme preference. No third-party trackers.</p>

    <h2 style="color: var(--text); margin-top: var(--s-10);">Changes</h2>
    <p>We update this page when our practices change and reset the "last updated" date. Significant changes will be flagged on the home page for at least 14 days.</p>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
