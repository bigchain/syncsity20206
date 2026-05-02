<?php
/**
 * Contact — 4-step conversational wizard. Migrated from contact.html.
 */

$page_path_prefix = '/';
$page_title       = 'Contact Syncsity | Smart routing, real human reply';
$page_description = 'Tell us what you need in 30 seconds. We\'ll route you to the right path — free assessment, strategy session, or a real reply from Edward at edward@syncsity.com.';
$page_canonical   = 'https://syncsity.com/contact.php';
$page_breadcrumb  = [
    ['Home',    'https://syncsity.com/'],
    ['Contact', 'https://syncsity.com/contact.php'],
];

$page_extra_jsonld = <<<'JSONLD'

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ContactPage",
  "@id": "https://syncsity.com/contact.php#contactpage",
  "name": "Contact Syncsity",
  "url": "https://syncsity.com/contact.php",
  "description": "Smart routing contact wizard. Real reply from edward@syncsity.com within one UK business day.",
  "publisher": { "@id": "https://syncsity.com/#organization" },
  "mainEntity": {
    "@type": "ContactPoint",
    "contactType": "customer support",
    "email": "edward@syncsity.com",
    "areaServed": ["GB","US","AE","CA","AU"],
    "availableLanguage": ["English"]
  }
}
</script>
JSONLD;

include __DIR__ . '/partials/site-head.php';
?>

<style>
/* Conversational contact wizard */
.wizard {
  max-width: 720px; margin: 0 auto;
  background: rgba(255,255,255,0.04);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  border: 1px solid var(--border);
  border-radius: 24px;
  padding: 40px;
  position: relative;
}
@media (max-width:540px) { .wizard { padding: 24px; } }

.wizard__progress { display: flex; gap: 6px; margin-bottom: 24px; }
.wizard__pip {
  height: 4px; flex: 1;
  background: rgba(255,255,255,0.10);
  border-radius: 2px;
  transition: background-color 300ms ease;
}
.wizard__pip.is-active { background: linear-gradient(90deg, var(--blue-400), var(--orange)); }
.wizard__pip.is-done   { background: var(--blue-400); }

.wizard__step-num {
  font-family: 'JetBrains Mono', monospace;
  font-size: 11px; letter-spacing: 0.16em;
  color: var(--blue-300); text-transform: uppercase; font-weight: 600;
  margin-bottom: 10px;
  display: inline-flex; align-items: center; gap: 8px;
}
.wizard__step-num::before { content: ''; width: 16px; height: 1px; background: var(--blue-300); }
.wizard__step-title {
  font-size: clamp(1.4rem, 1vw + 1.3rem, 1.9rem);
  font-weight: 700; letter-spacing: -0.02em;
  color: #fff; margin-bottom: 10px; line-height: 1.2;
}
.wizard__step-sub { color: var(--text-muted); font-size: 1rem; margin-bottom: 24px; line-height: 1.6; }

.step { display: none; }
.step.is-active { display: block; animation: stepIn 400ms cubic-bezier(0.22,1,0.36,1); }
@keyframes stepIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

.choice-grid { display: grid; gap: 12px; }
.choice {
  display: grid; grid-template-columns: 40px 1fr auto; gap: 16px; align-items: center;
  padding: 18px 20px;
  background: rgba(255,255,255,0.03);
  border: 1px solid var(--border-strong);
  border-radius: 14px;
  cursor: pointer;
  text-align: left; width: 100%;
  transition: transform 150ms ease, border-color 150ms ease, background-color 150ms ease;
  color: inherit;
}
.choice:hover { transform: translateX(4px); border-color: var(--blue-400); background: rgba(51,133,223,0.06); }
.choice.is-selected { border-color: var(--blue-400); background: rgba(51,133,223,0.10); }
.choice__icon {
  width: 40px; height: 40px;
  display: grid; place-items: center;
  background: rgba(51,133,223,0.10); color: var(--blue-300);
  border-radius: 10px;
}
.choice__icon--orange { background: rgba(252,163,17,0.10); color: var(--orange); }
.choice__icon--green  { background: rgba(54,179,126,0.10); color: #6ee0a8; }
.choice__icon--purple { background: rgba(170,120,255,0.10); color: #c5a8ff; }
.choice__body h4 { color: #fff; font-size: 1.05rem; margin-bottom: 4px; font-weight: 600; }
.choice__body p  { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; }
.choice__cta { color: var(--text-dim); font-family: 'JetBrains Mono', monospace; font-size: 11px; }
@media (max-width:540px) { .choice { grid-template-columns: 36px 1fr; } .choice__cta { display:none; } }

.step__actions {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: 24px; gap: 12px; flex-wrap: wrap;
}
.step__actions-meta { color: var(--text-dim); font-size: 12px; font-family: 'JetBrains Mono', monospace; }

.preferred-time { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 8px; }
@media (max-width:540px) { .preferred-time { grid-template-columns: repeat(2, 1fr); } }
.time-pill {
  padding: 10px 12px;
  background: rgba(255,255,255,0.04);
  border: 1px solid var(--border);
  border-radius: 10px;
  cursor: pointer;
  font-size: 14px;
  color: var(--text-muted);
  text-align: center;
  transition: all 150ms ease;
}
.time-pill:hover { border-color: var(--border-strong); color: #fff; }
.time-pill.is-selected { border-color: var(--blue-400); color: #fff; background: rgba(51,133,223,0.10); }

.summary-row {
  display: grid; grid-template-columns: 120px 1fr; gap: 16px; padding: 12px 0;
  border-bottom: 1px solid var(--border);
  font-size: 14px;
}
.summary-row:last-child { border-bottom: 0; }
.summary-row__label { color: var(--text-dim); font-family: 'JetBrains Mono', monospace; font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; padding-top: 2px; }
.summary-row__value { color: #fff; }

.live-strip {
  display: inline-flex; align-items: center; gap: 12px;
  padding: 8px 14px;
  background: rgba(54,179,126,0.08);
  border: 1px solid rgba(54,179,126,0.30);
  border-radius: 9999px;
  font-family: 'JetBrains Mono', monospace;
  font-size: 12px; color: #6ee0a8;
  margin-bottom: 24px;
}
.live-strip__dot { width: 7px; height: 7px; border-radius: 50%; background: #36b37e; animation: pulse 2s ease-in-out infinite; }
@keyframes pulse { 0%,100%{opacity:1;}50%{opacity:0.4;} }

.contact-hero { padding: 56px 0 32px; text-align: center; }
.contact-hero h1 {
  font-size: clamp(34px, 4.4vw, 52px);
  font-weight: 700; color: #fff; letter-spacing: -0.02em; line-height: 1.1;
  margin: 0 0 18px;
}
.contact-hero .lead { color: var(--text-muted); font-size: 17px; line-height: 1.55; max-width: 660px; margin: 0 auto; }
</style>
</head>
<body>

<?php include __DIR__ . '/partials/site-nav.php'; ?>

<main id="main" aria-label="Contact Syncsity">

  <section class="contact-hero">
    <div class="container container--md">
      <div class="live-strip">
        <span class="live-strip__dot" aria-hidden="true"></span>
        <span>Reading every message &middot; Edward &middot; UK working hours</span>
      </div>
      <h1>Get in touch.</h1>
      <p class="lead">30 seconds. Three quick questions. We'll route you to the fastest answer &mdash; free assessment, strategy session, or a real reply from <strong style="color:#fff;">edward@syncsity.com</strong> within one UK business day.</p>
    </div>
  </section>

  <section style="padding-bottom: 80px;">
    <div class="container">
      <div class="wizard" data-wizard>

        <div class="wizard__progress" data-progress>
          <span class="wizard__pip is-active"></span>
          <span class="wizard__pip"></span>
          <span class="wizard__pip"></span>
          <span class="wizard__pip"></span>
        </div>

        <div id="wizard-flash" class="flash" role="status"></div>

        <form id="contact-form" action="/api/contact" method="POST" novalidate>
          <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">
          <input type="hidden" name="_return" value="/contact.php">
          <input type="hidden" name="gdpr_consent" value="1">
          <input type="hidden" name="name" id="hidden-name">
          <input type="hidden" name="subject" id="hidden-subject" value="General">
          <input type="hidden" name="message" id="hidden-message">
          <input type="hidden" name="preferred_time" id="hidden-time">

          <div class="step is-active" data-step="1">
            <div class="wizard__step-num">Question 1 of 4</div>
            <h2 class="wizard__step-title">What brings you in?</h2>
            <p class="wizard__step-sub">Pick the closest. We'll route you to the fastest path.</p>

            <div class="choice-grid">
              <button type="button" class="choice" data-route="diagnose">
                <span class="choice__icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg></span>
                <span class="choice__body"><h4>Diagnose my business</h4><p>Free 15-minute Aha! Assessment + AI-written Revenue Intelligence Report.</p></span>
                <span class="choice__cta">&rarr; /assess</span>
              </button>

              <button type="button" class="choice" data-route="strategy">
                <span class="choice__icon choice__icon--orange"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
                <span class="choice__body"><h4>Book a Strategy Session</h4><p>&pound;950 &middot; 30 minutes with a senior operator. Pressure-test your diagnosis.</p></span>
                <span class="choice__cta">&rarr; Calendly</span>
              </button>

              <button type="button" class="choice" data-route="engagement">
                <span class="choice__icon choice__icon--green"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
                <span class="choice__body"><h4>Considered engagement</h4><p>You've read about us, you have a real brief. Tell us properly.</p></span>
                <span class="choice__cta">Continue &rarr;</span>
              </button>

              <button type="button" class="choice" data-route="other">
                <span class="choice__icon choice__icon--purple"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><circle cx="12" cy="8" r="0.5" fill="currentColor"/></svg></span>
                <span class="choice__body"><h4>Press, partnership, hiring, or other</h4><p>Anything that doesn't fit above. Edward reads it personally.</p></span>
                <span class="choice__cta">Continue &rarr;</span>
              </button>
            </div>
          </div>

          <div class="step" data-step="2">
            <div class="wizard__step-num">Question 2 of 4</div>
            <h2 class="wizard__step-title" id="step2-title">Tell us about you.</h2>
            <p class="wizard__step-sub">Just enough so the reply lands in the right inbox.</p>

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
                <input type="text" name="company" id="company" class="input" placeholder="Company" maxlength="200" autocomplete="organization">
                <label class="field__label" for="company">Company</label>
              </div>
            </div>

            <div class="step__actions">
              <button type="button" class="btn btn--ghost btn--sm" data-prev>&larr; Back</button>
              <div style="display:flex;gap:12px;align-items:center;">
                <span class="step__actions-meta">press <kbd style="border:1px solid var(--border-strong);padding:2px 8px;border-radius:4px;font-family:'JetBrains Mono',monospace;font-size:10px;">Enter</kbd></span>
                <button type="button" class="btn btn--primary" data-next-from="2">Continue &rarr;</button>
              </div>
            </div>
          </div>

          <div class="step" data-step="3">
            <div class="wizard__step-num">Question 3 of 4</div>
            <h2 class="wizard__step-title" id="step3-title">What can we help with?</h2>
            <p class="wizard__step-sub" id="step3-sub">The more concrete, the faster Edward can be useful. Brutal honesty welcome &mdash; only Edward reads this.</p>

            <div class="field">
              <textarea id="messageInput" class="textarea" placeholder="Your message" required minlength="20" maxlength="5000" style="min-height:160px;"></textarea>
              <label class="field__label" for="messageInput">Your message</label>
            </div>

            <div style="margin-top:18px;">
              <p class="wizard__step-sub" style="margin-bottom: 10px; font-size: 13px; color: var(--text-dim); font-family: 'JetBrains Mono', monospace; letter-spacing: 0.1em; text-transform: uppercase;">Optional &middot; best time to reach you</p>
              <div class="preferred-time" data-time>
                <button type="button" class="time-pill" data-tval="anytime">Anytime</button>
                <button type="button" class="time-pill" data-tval="morning">Morning</button>
                <button type="button" class="time-pill" data-tval="afternoon">Afternoon</button>
                <button type="button" class="time-pill" data-tval="thisweek">This week</button>
              </div>
            </div>

            <div class="step__actions">
              <button type="button" class="btn btn--ghost btn--sm" data-prev>&larr; Back</button>
              <button type="button" class="btn btn--primary" data-next-from="3">Continue &rarr;</button>
            </div>
          </div>

          <div class="step" data-step="4">
            <div class="wizard__step-num">Question 4 of 4 &middot; Final</div>
            <h2 class="wizard__step-title">Quick review, then we send.</h2>
            <p class="wizard__step-sub">Confirm everything looks right. Reply lands within one UK business day.</p>

            <div style="background:rgba(0,0,0,0.15);border:1px solid var(--border);border-radius:12px;padding:20px 24px;">
              <div class="summary-row"><div class="summary-row__label">Path</div><div class="summary-row__value" data-summary="route">&mdash;</div></div>
              <div class="summary-row"><div class="summary-row__label">Name</div><div class="summary-row__value" data-summary="name">&mdash;</div></div>
              <div class="summary-row"><div class="summary-row__label">Email</div><div class="summary-row__value" data-summary="email">&mdash;</div></div>
              <div class="summary-row"><div class="summary-row__label">Company</div><div class="summary-row__value" data-summary="company">&mdash;</div></div>
              <div class="summary-row"><div class="summary-row__label">When best</div><div class="summary-row__value" data-summary="time">Anytime</div></div>
              <div class="summary-row"><div class="summary-row__label">Message</div><div class="summary-row__value" data-summary="message" style="white-space:pre-wrap;">&mdash;</div></div>
            </div>

            <label class="checkbox-row">
              <input type="checkbox" name="gdpr_consent_visible" value="1" required checked>
              <span>I'm OK with Syncsity processing this to reply to me. <a href="/privacy.php">Privacy policy</a>.</span>
            </label>

            <div class="step__actions">
              <button type="button" class="btn btn--ghost btn--sm" data-prev>&larr; Back</button>
              <button type="submit" class="btn btn--primary btn--lg">Send to Edward &rarr;</button>
            </div>
            <p style="margin-top:12px;text-align:center;color:var(--text-dim);font-size:12px;font-family:'JetBrains Mono',monospace;">&rarr; edward@syncsity.com</p>
          </div>
        </form>
      </div>
    </div>
  </section>

</main>

<script>
(function(){
  var form     = document.getElementById('contact-form');
  var steps    = form.querySelectorAll('.step');
  var pips     = document.querySelectorAll('[data-progress] .wizard__pip');
  var flash    = document.getElementById('wizard-flash');
  var current  = 1;
  var maxStep  = 4;
  var route    = '';

  var routeMeta = {
    diagnose:   { title: 'Free Aha! Assessment',     subject: 'Diagnose request',      step2: 'Tell us where to send the report.', step3: 'Anything we should know first?', step3sub: 'Optional context. The form will redirect you to the assessment when you submit.' },
    strategy:   { title: 'Strategy Session (£950)',  subject: 'Strategy Session',      step2: 'Tell us about you.',                step3: 'What do you want to pressure-test?', step3sub: 'Specifics make the call worth 10x more. We\'ll redirect you to Calendly to pick a slot after this.' },
    engagement: { title: 'Considered engagement',    subject: 'Engagement enquiry',    step2: 'Who are you?',                      step3: 'What\'s the brief?', step3sub: 'Where you are now, what success looks like, what you\'ve already tried.' },
    other:      { title: 'Press / partnership / other', subject: 'General enquiry',     step2: 'Who are you?',                      step3: 'What can we help with?', step3sub: 'Anything that doesn\'t fit elsewhere. Edward reads it personally.' }
  };

  document.querySelectorAll('.choice').forEach(function (btn) {
    btn.addEventListener('click', function () {
      route = btn.dataset.route;
      document.getElementById('hidden-subject').value = (routeMeta[route] || {}).subject || 'General';
      var meta = routeMeta[route] || {};
      var s2t = document.getElementById('step2-title'); if (s2t) s2t.textContent = meta.step2 || s2t.textContent;
      var s3t = document.getElementById('step3-title'); if (s3t) s3t.textContent = meta.step3 || s3t.textContent;
      var s3s = document.getElementById('step3-sub');  if (s3s) s3s.textContent = meta.step3sub || s3s.textContent;
      goTo(2);
    });
  });

  document.querySelectorAll('[data-time] .time-pill').forEach(function (b) {
    b.addEventListener('click', function () {
      document.querySelectorAll('[data-time] .time-pill').forEach(function (x) { x.classList.remove('is-selected'); });
      b.classList.add('is-selected');
      document.getElementById('hidden-time').value = b.dataset.tval;
    });
  });
  var defaultTime = document.querySelector('.time-pill[data-tval="anytime"]');
  if (defaultTime) { defaultTime.classList.add('is-selected'); document.getElementById('hidden-time').value = 'anytime'; }

  document.querySelectorAll('[data-prev]').forEach(function (b) {
    b.addEventListener('click', function () { goTo(current - 1); });
  });
  document.querySelectorAll('[data-next-from]').forEach(function (b) {
    b.addEventListener('click', function () {
      var from = parseInt(b.dataset.nextFrom, 10);
      if (!validateStep(from)) return;
      goTo(from + 1);
    });
  });

  ['firstName','lastName','email','company'].forEach(function (id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('keydown', function (e) { if (e.key === 'Enter') { e.preventDefault(); if (current === 2 && validateStep(2)) goTo(3); } });
  });

  function validateStep(step) {
    flash.classList.remove('is-error'); flash.style.display = 'none';
    if (step === 2) {
      var fn = document.getElementById('firstName').value.trim();
      var ln = document.getElementById('lastName').value.trim();
      var em = document.getElementById('email').value.trim();
      if (fn.length < 2 || ln.length < 2) { return showError('Both names please — even just initials.'); }
      if (!/^[^@\s]+@[^@\s]+\.[^@\s]{2,}$/.test(em)) { return showError('That email looks off.'); }
    }
    if (step === 3) {
      var msg = document.getElementById('messageInput').value.trim();
      if (msg.length < 20) { return showError('A bit more detail (min 20 chars) so we can be useful.'); }
    }
    return true;
  }
  function showError(t) { flash.classList.add('is-error'); flash.textContent = t; flash.style.display = 'block'; flash.scrollIntoView({behavior:'smooth',block:'nearest'}); return false; }

  function goTo(n) {
    if (n < 1 || n > maxStep) return;
    current = n;
    steps.forEach(function (s) { s.classList.remove('is-active'); });
    form.querySelector('[data-step="' + n + '"]').classList.add('is-active');
    pips.forEach(function (p, i) {
      p.classList.remove('is-active', 'is-done');
      if (i < n - 1) p.classList.add('is-done');
      if (i === n - 1) p.classList.add('is-active');
    });
    if (n === 4) populateSummary();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function populateSummary() {
    var meta = routeMeta[route] || {};
    var fn = document.getElementById('firstName').value.trim();
    var ln = document.getElementById('lastName').value.trim();
    var em = document.getElementById('email').value.trim();
    var co = document.getElementById('company').value.trim();
    var msg = document.getElementById('messageInput').value.trim();
    var t = document.getElementById('hidden-time').value || 'anytime';
    var setS = function (k, v) { var el = document.querySelector('[data-summary="' + k + '"]'); if (el) el.textContent = v; };
    setS('route',   meta.title || '—');
    setS('name',    (fn + ' ' + ln).trim() || '—');
    setS('email',   em || '—');
    setS('company', co || '—');
    setS('time',    { anytime: 'Anytime', morning: 'Mornings (UK)', afternoon: 'Afternoons (UK)', thisweek: 'This week' }[t] || t);
    setS('message', msg || '—');
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var fn = document.getElementById('firstName').value.trim();
    var ln = document.getElementById('lastName').value.trim();
    document.getElementById('hidden-name').value = (fn + ' ' + ln).trim();
    var msg = document.getElementById('messageInput').value.trim();
    var t = document.getElementById('hidden-time').value || 'anytime';
    var meta = routeMeta[route] || {};
    var fullMessage = msg + '\n\n— Best time to reach: ' + t + '\n— Path chosen: ' + (meta.title || 'General');
    document.getElementById('hidden-message').value = fullMessage;

    var afterRedirect = '';
    if (route === 'diagnose') afterRedirect = '/assess?email=' + encodeURIComponent(document.getElementById('email').value);
    if (route === 'strategy') afterRedirect = '/booking.php';

    var fd = new FormData(form);
    var btn = e.submitter;
    if (btn) { btn.disabled = true; btn.textContent = 'Sending…'; }

    fetch('/api/contact', { method: 'POST', body: fd, credentials: 'same-origin', redirect: 'manual' })
      .then(function () {
        if (afterRedirect) window.location.href = afterRedirect;
        else window.location.href = '/contact.php?sent=1';
      })
      .catch(function () {
        window.location.href = afterRedirect || '/contact.php?sent=1';
      });
  });

  var qs = new URLSearchParams(location.search);
  if (qs.get('sent') === '1') {
    flash.classList.add('is-success');
    flash.innerHTML = '<strong>Message in.</strong> Edward will reply within one UK business day from edward@syncsity.com. A confirmation just landed in your inbox.';
    flash.style.display = 'block';
    flash.scrollIntoView({ behavior: 'smooth', block: 'center' });
  } else if (qs.get('error')) {
    flash.classList.add('is-error');
    flash.textContent = qs.get('error');
    flash.style.display = 'block';
  }
})();
</script>

<?php include __DIR__ . '/partials/site-footer.php'; ?>
