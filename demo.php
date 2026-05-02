<?php
/**
 * Request a Demo — migrated from demo.html.
 */

$page_path_prefix = '/';
$page_title       = 'Request a Demo | Syncsity AI Voice Agents & Operations';
$page_description = 'See AI Voice Agents, sales systems, and process automation in action. The fastest demo is the free 15-minute Aha! Assessment — your first concrete insight before any call.';
$page_canonical   = 'https://syncsity.com/demo.php';
$page_breadcrumb  = [
    ['Home', 'https://syncsity.com/'],
    ['Demo', 'https://syncsity.com/demo.php'],
];

$page_extra_jsonld = <<<'JSONLD'

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "@id": "https://syncsity.com/demo.php#contactpage",
  "name": "Request a Syncsity Demo",
  "url": "https://syncsity.com/demo.php",
  "description": "Demo request page for Syncsity AI Voice Agents and operations products.",
  "publisher": { "@id": "https://syncsity.com/#organization" }
}
</script>
JSONLD;

include __DIR__ . '/partials/site-head.php';
?>

<style>
.demo-hero { padding: 56px 0 24px; text-align: center; }
.demo-hero .pill {
  display: inline-block; padding: 6px 14px;
  background: rgba(252,163,17,0.14); color: var(--orange);
  border-radius: 9999px; font-size: 13px; font-weight: 600;
}
.demo-hero h1 {
  margin: 24px 0 20px; font-size: clamp(32px, 4.4vw, 48px);
  font-weight: 700; color: #fff; letter-spacing: -0.02em; line-height: 1.1;
}
.demo-hero h1 em { color: var(--blue-300); font-style: normal; }
.demo-hero .lead {
  color: var(--text-muted); font-size: 16px; line-height: 1.6;
  max-width: 720px; margin: 0 auto;
}
.demo-hero__ctas {
  display: flex; gap: 14px; flex-wrap: wrap; justify-content: center;
  margin-top: 32px;
}

.demo-grid {
  display: grid; grid-template-columns: 1.4fr 1fr; gap: 40px;
  align-items: start; padding: 32px 0 80px;
}
@media (max-width: 880px) { .demo-grid { grid-template-columns: 1fr; } }

.demo-form-card {
  background: rgba(255,255,255,0.03); border: 1px solid var(--border);
  border-radius: 16px; padding: 40px;
}
.demo-form-card .eyebrow {
  display: inline-block; padding: 4px 12px;
  background: rgba(51,133,223,0.14); color: var(--blue-400);
  border-radius: 9999px; font-size: 12px; font-weight: 600;
  letter-spacing: 0.06em; text-transform: uppercase;
  margin-bottom: 16px;
}
.demo-form-card h2 { color: #fff; font-size: 22px; font-weight: 700; margin: 0 0 8px; }
.demo-form-card .intro { color: var(--text-muted); font-size: 14.5px; margin: 0 0 24px; line-height: 1.5; }

.demo-aside { display: flex; flex-direction: column; gap: 20px; }
.demo-aside .card {
  background: rgba(255,255,255,0.03); border: 1px solid var(--border);
  border-radius: 14px; padding: 24px 28px;
}
.demo-aside .card--highlight {
  background: linear-gradient(135deg, rgba(252,163,17,0.10), rgba(51,133,223,0.06));
  border-color: rgba(252,163,17,0.30);
}
.demo-aside h3 {
  font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase;
  color: var(--text-dim); font-family: 'JetBrains Mono', monospace;
  margin: 0 0 10px; font-weight: 600;
}
.demo-aside .card--highlight h3 { color: var(--orange); }
.demo-aside .card-title {
  font-size: 18px; color: #fff; font-weight: 600; margin: 0 0 8px;
}
.demo-aside .card-body {
  color: var(--text-muted); font-size: 14px; margin: 0 0 14px; line-height: 1.5;
}
.demo-aside .card-link {
  color: var(--blue-300); font-weight: 600; font-size: 14px; text-decoration: none;
}
.demo-aside .card--highlight .card-link { color: var(--orange); }
</style>
</head>
<body>

<?php include __DIR__ . '/partials/site-nav.php'; ?>

<main id="main" aria-label="Request a demo">

  <section class="demo-hero">
    <div class="container container--sm">
      <span class="pill">&#9733; Real demos beat scripted ones</span>
      <h1>See it on <em>your</em> business &mdash; not a fake one.</h1>
      <p class="lead">
        Most "request a demo" forms send you a calendar invite for a generic walkthrough.
        Ours does something useful before we even talk: take the free 15-minute Aha! Assessment
        and you'll have a personalised Revenue Intelligence Report &mdash; for your actual business &mdash;
        in your inbox within 90 seconds.
      </p>
      <div class="demo-hero__ctas">
        <a href="/assess" class="btn btn--primary btn--lg">Take the live demo (free, 15 min) &rarr;</a>
        <a href="/booking.php" class="btn btn--ghost btn--lg">Talk to a human first</a>
      </div>
    </div>
  </section>

  <section>
    <div class="container container--md">
      <div class="demo-grid">

        <div class="demo-form-card">
          <span class="eyebrow">Or &mdash; request a custom demo call</span>
          <h2>Specifics? Tell us about your operation.</h2>
          <p class="intro">Used by ops leaders evaluating AI Voice for clinics, agencies, financial services, and logistics. The more concrete your situation, the more useful our reply.</p>

          <div id="demo-flash" class="flash" role="status"></div>

          <form id="demo-form" action="/api/contact" method="POST" novalidate>
            <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
            <input type="hidden" name="_return" value="/demo.php">
            <input type="hidden" name="gdpr_consent" value="1">
            <input type="hidden" name="name" id="hidden-name">
            <input type="hidden" name="subject" id="hidden-subject" value="Demo Request">
            <input type="hidden" name="message" id="hidden-message">

            <div class="field__row">
              <div class="field">
                <input type="text" id="firstName" class="input" placeholder="First name" required maxlength="80" autocomplete="given-name">
                <label class="field__label" for="firstName">First name</label>
              </div>
              <div class="field">
                <input type="text" id="lastName" class="input" placeholder="Last name" required maxlength="80" autocomplete="family-name">
                <label class="field__label" for="lastName">Last name</label>
              </div>
            </div>

            <div class="field__row">
              <div class="field">
                <input type="email" name="email" id="email" class="input" placeholder="Work email" required maxlength="254" autocomplete="email">
                <label class="field__label" for="email">Work email</label>
              </div>
              <div class="field">
                <input type="tel" id="phone" class="input" placeholder="Phone" maxlength="40" autocomplete="tel">
                <label class="field__label" for="phone">Phone</label>
              </div>
            </div>

            <div class="field__row">
              <div class="field">
                <input type="text" name="company" id="company" class="input" placeholder="Company" required maxlength="200" autocomplete="organization">
                <label class="field__label" for="company">Company</label>
              </div>
              <div class="field">
                <input type="text" id="role" class="input" placeholder="Your role" required maxlength="120">
                <label class="field__label" for="role">Your role</label>
              </div>
            </div>

            <div class="field">
              <select id="useCase" class="select" data-empty="true" required>
                <option value="" disabled selected hidden></option>
                <option value="Sales outreach">Sales outreach</option>
                <option value="Customer support">Customer support</option>
                <option value="Appointment scheduling">Appointment scheduling</option>
                <option value="Lead qualification">Lead qualification</option>
                <option value="Workflow automation">Workflow automation</option>
                <option value="Other">Other</option>
              </select>
              <label class="field__label" for="useCase">Primary use case</label>
            </div>

            <div class="field">
              <textarea id="details" class="textarea" placeholder="What do you want the demo to actually answer?" minlength="20" maxlength="3000"></textarea>
              <label class="field__label" for="details">What do you want the demo to actually answer?</label>
              <div class="field__hint">e.g. "We get 200 inbound calls/day for a 4-clinic dental group; can AI handle 80% without a botched booking?" &mdash; that's gold.</div>
            </div>

            <label class="checkbox-row">
              <input type="checkbox" required checked>
              <span>I'm OK with Syncsity processing this for our reply. <a href="/privacy.php">Privacy policy</a>.</span>
            </label>

            <div class="submit-row">
              <button type="submit" class="btn btn--primary btn--lg">Request the demo &rarr;</button>
              <span class="submit-meta">&rarr; edward@syncsity.com</span>
            </div>
          </form>
        </div>

        <aside class="demo-aside">
          <div class="card">
            <h3>For operators</h3>
            <p class="card-title">Aha! Assessment</p>
            <p class="card-body">15 min. Free. AI-written report names the &pound;10K+/month leak in your business with a 3-step intervention plan.</p>
            <a href="/assess" class="card-link">Start the assessment &rarr;</a>
          </div>
          <div class="card card--highlight">
            <h3>For senior ops / IT leaders</h3>
            <p class="card-title">&pound;950 Strategy Session</p>
            <p class="card-body">30 min with a senior operator. Diagnosis pressure-test + 90-day plan. Fee credited against any subsequent engagement.</p>
            <a href="/booking.php" class="card-link">Book on Calendly &rarr;</a>
          </div>
          <div class="card">
            <h3>Why we don't do generic demos</h3>
            <p class="card-body" style="margin: 0;">A 30-minute generic walkthrough wastes everyone's time. Our diagnostic does in 15 minutes &mdash; for free &mdash; what most consultancies charge &pound;25K to deliver. We'd rather show you that than show you slides.</p>
          </div>
        </aside>

      </div>
    </div>
  </section>

</main>

<script>
(function(){
  var sel = document.getElementById('useCase');
  if (sel) {
    var sync = function(){ sel.setAttribute('data-empty', sel.value === '' ? 'true' : 'false'); };
    sel.addEventListener('change', sync); sync();
  }
  var form = document.getElementById('demo-form');
  if (form) {
    form.addEventListener('submit', function(){
      var fn=(document.getElementById('firstName')||{}).value||'';
      var ln=(document.getElementById('lastName')||{}).value||'';
      var role=(document.getElementById('role')||{}).value||'';
      var phone=(document.getElementById('phone')||{}).value||'';
      var useCase=(document.getElementById('useCase')||{}).value||'';
      var details=(document.getElementById('details')||{}).value||'';
      document.getElementById('hidden-name').value = (fn+' '+ln).trim();
      if (useCase) document.getElementById('hidden-subject').value = 'Demo Request: ' + useCase;
      var parts = [];
      if (role)    parts.push('Role: '+role);
      if (phone)   parts.push('Phone: '+phone);
      if (useCase) parts.push('Primary use case: '+useCase);
      if (details) parts.push('\nDetails:\n'+details);
      document.getElementById('hidden-message').value = parts.join('\n') || 'Demo request from ' + (fn+' '+ln).trim();
    });
  }
  var qs = new URLSearchParams(location.search);
  var flash = document.getElementById('demo-flash');
  if (flash) {
    if (qs.get('sent')==='1') {
      flash.classList.add('is-success');
      flash.innerHTML = '<strong>Request received.</strong> We\'ll be in touch within one business day.';
      flash.scrollIntoView({behavior:'smooth',block:'center'});
    } else if (qs.get('error')) {
      flash.classList.add('is-error');
      flash.textContent = qs.get('error');
    }
  }
})();
</script>

<?php include __DIR__ . '/partials/site-footer.php'; ?>
