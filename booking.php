<?php
/**
 * Strategy Session booking page — migrated from booking.html.
 */

$page_path_prefix = '/';
$page_title       = 'Book a Strategy Session | Syncsity';
$page_description = 'Book a 30-minute paid Strategy Session with a senior Syncsity operator. £950, fee credited against any subsequent engagement.';
$page_canonical   = 'https://syncsity.com/booking.php';
$page_breadcrumb  = [
    ['Home', 'https://syncsity.com/'],
    ['Book a Session', 'https://syncsity.com/booking.php'],
];

// Service + Offer JSON-LD for the £950 Strategy Session
$page_extra_jsonld = <<<'JSONLD'

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Service",
  "@id": "https://syncsity.com/booking.php#strategy-session",
  "name": "Syncsity Strategy Session",
  "description": "30-minute paid call with a senior Syncsity operator. Diagnosis pressure-tested, written 90-day plan, implementation cost estimate, recording + summary within 24 hours.",
  "provider": { "@id": "https://syncsity.com/#organization" },
  "areaServed": ["GB","US"],
  "offers": {
    "@type": "Offer",
    "price": "950",
    "priceCurrency": "GBP",
    "availability": "https://schema.org/InStock",
    "description": "Fee credited against any subsequent engagement"
  }
}
</script>

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ReserveAction",
  "target": "https://calendly.com/syncsity/strategy-session",
  "result": { "@type": "Reservation", "reservationFor": { "@id": "https://syncsity.com/booking.php#strategy-session" } }
}
</script>
JSONLD;

include __DIR__ . '/partials/site-head.php';
?>

<style>
.booking-hero { padding: 64px 0 32px; text-align: center; }
.booking-hero .pill {
  display: inline-block; padding: 6px 14px;
  background: rgba(252,163,17,0.14); color: var(--orange);
  border-radius: 9999px; font-size: 13px; font-weight: 600;
}
.booking-hero h1 {
  margin: 24px 0 20px; font-size: clamp(34px, 4.6vw, 52px);
  font-weight: 700; color: #fff; letter-spacing: -0.02em;
}
.booking-hero .lead {
  color: var(--text-muted); font-size: 17px; line-height: 1.55;
  max-width: 720px; margin: 0 auto;
}

.booking-card { padding: 32px 0; }
.booking-card__inner {
  background: #fff; border-radius: 16px; overflow: hidden;
  box-shadow: 0 24px 48px rgba(0,0,0,0.20);
}
.booking-card__head {
  background: linear-gradient(135deg, #14213D 0%, #1a2c52 100%);
  padding: 24px 32px; border-bottom: 1px solid var(--border);
}
.booking-card__head h2 { font-size: 22px; color: #fff; margin: 0; }
.booking-card__head p { color: rgba(255,255,255,0.72); font-size: 14px; margin: 4px 0 0; }

.booking-grid {
  display: grid; grid-template-columns: 1fr 1fr; gap: 24px;
  padding: 32px 0;
}
@media (max-width: 720px) { .booking-grid { grid-template-columns: 1fr; } }
.booking-grid .card {
  background: rgba(255,255,255,0.03); border: 1px solid var(--border);
  border-radius: 14px; padding: 28px;
}
.booking-grid h3 { color: #fff; font-size: 18px; font-weight: 700; margin: 0 0 16px; }
.booking-grid h3:nth-of-type(2) { font-size: 15px; margin-top: 18px; }
.booking-grid ul { list-style: disc; padding-left: 20px; display: flex; flex-direction: column; gap: 10px; color: var(--text-muted); font-size: 15px; line-height: 1.5; margin: 0; }
.booking-grid p { color: var(--text-muted); font-size: 15px; line-height: 1.55; margin: 0 0 16px; }

.booking-cta {
  text-align: center; padding: 48px;
  background: linear-gradient(135deg, rgba(51,133,223,0.08), rgba(252,163,17,0.04));
  border-radius: 16px; margin: 32px 0 80px;
}
.booking-cta .eyebrow {
  display: inline-block; padding: 4px 12px;
  background: rgba(51,133,223,0.14); color: var(--blue-400);
  border-radius: 9999px; font-size: 12px; font-weight: 600;
  letter-spacing: 0.06em; text-transform: uppercase;
}
.booking-cta h2 {
  margin: 16px 0; font-size: clamp(22px, 2.8vw, 30px);
  font-weight: 700; color: #fff; letter-spacing: -0.01em;
}
.booking-cta .lead {
  color: var(--text-muted); font-size: 16px; max-width: 580px;
  margin: 0 auto 28px; line-height: 1.55;
}
</style>
</head>
<body>

<?php include __DIR__ . '/partials/site-nav.php'; ?>

<main id="main" aria-label="Book a Strategy Session">

  <section class="booking-hero">
    <div class="container container--md">
      <span class="pill">&#9733; Strategy Session &middot; &pound;950 &middot; 30 minutes</span>
      <h1>Pressure-test the diagnosis with a senior operator.</h1>
      <p class="lead">For people who've taken the free assessment and want to go deeper. The fee is credited against any subsequent engagement &mdash; so if you decide to work with us, the call effectively pays for itself.</p>
    </div>
  </section>

  <section class="booking-card">
    <div class="container container--md">
      <div class="booking-card__inner">
        <div class="booking-card__head">
          <h2>Pick a time that works</h2>
          <p>Calendly handles the scheduling. You'll be invoiced after the call.</p>
        </div>
        <iframe src="https://calendly.com/syncsity/strategy-session"
                style="width:100%;height:760px;border:0;display:block;"
                loading="lazy"
                title="Calendly &mdash; book your Syncsity Strategy Session"></iframe>
      </div>
    </div>
  </section>

  <section>
    <div class="container container--md">
      <div class="booking-grid">
        <div class="card">
          <h3>What you get</h3>
          <ul>
            <li>30 minutes with a senior Syncsity operator (no juniors, no slide template)</li>
            <li>Your diagnosis pressure-tested with your real numbers</li>
            <li>Written 90-day intervention plan you can hand to your team</li>
            <li>Implementation cost estimate &mdash; no surprises</li>
            <li>Recording + summary delivered within 24 hours</li>
            <li>Fee credited against any subsequent engagement</li>
          </ul>
        </div>
        <div class="card">
          <h3>Who it's for</h3>
          <p>Founders, CEOs, or heads of operations at UK &amp; US service businesses doing &pound;1M&ndash;&pound;250M in revenue. You should already know your business &mdash; the call is for sharpening, not first principles.</p>
          <h3>Not for</h3>
          <p>Pre-revenue businesses, agencies wanting white-label resale, or operators looking for a tool to install rather than a system to build. We say no often.</p>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container container--md">
      <div class="booking-cta">
        <span class="eyebrow">Recommended path</span>
        <h2>Take the free 15-minute Aha! Assessment first.</h2>
        <p class="lead">The Strategy Session is most valuable when we're already looking at your Revenue Intelligence Report together. Most operators who do both say the assessment made the call worth 10x what they paid.</p>
        <a href="/assess" class="btn btn--primary btn--lg">Start with the free assessment &rarr;</a>
      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . '/partials/site-footer.php'; ?>
