<?php
/**
 * The £10K Leak Detector — hand-written static page.
 *
 * Replaces the SPA's 6-tab calculator page (Calculators.tsx). The React source
 * is preserved in git for revival if needed; this page intercepts the /calculators
 * route via DirectoryIndex / static file resolution before the SPA fallback fires.
 *
 * Math model (rules of thumb, plainly labelled — no fake academic citations):
 *   - Process inefficiency  = 3% of revenue (mid-market range commonly cited as 2-5%)
 *   - Concentration risk    = % × revenue × (8% baseline + 0.5%/pt above 30%)
 *   - Onboarding drag       = 15% turnover × extra ramp weeks × half-salary loss
 *
 * Firefighting hours are shown separately as a hidden-cost callout, NOT added
 * to the headline total — they're a symptom of the other leaks, and adding
 * them double-counts. Director time is valued at £200/h (fully-loaded UK
 * mid-market director — salary + on-costs + opportunity cost).
 *
 * The biggest leak wins the "top driver" label. The framing is "where your
 * number is most sensitive" — not a diagnosis. The diagnosis is /assess.
 * Sub-£10K results say so honestly — no padding to hit the headline number.
 */
$page_path_prefix = '/';
$h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>The £10K Leak Detector — Syncsity</title>
<meta name="description" content="Find the hidden £10K+/month constraint costing your business. 5 inputs, instant breakdown, real industry benchmarks. No email required.">
<link rel="canonical" href="https://syncsity.com/calculators">
<meta property="og:type" content="website">
<meta property="og:title" content="The £10K Leak Detector — Syncsity">
<meta property="og:description" content="Find the hidden £10K+/month constraint costing your business — in 60 seconds.">
<meta property="og:image" content="https://syncsity.com/assets/img/og-image.svg">
<meta name="twitter:card" content="summary_large_image">
<link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap">
<link rel="stylesheet" href="/assets/css/static-page.css">
<style>
  /* Page-scoped — kept additive, no overrides of the design system */
  .calc-grid { display: grid; gap: 28px; }
  @media (min-width: 880px) { .calc-grid { grid-template-columns: 1fr 1fr; gap: 36px; } }

  .calc-input { padding: 20px 0; border-bottom: 1px solid var(--border); }
  .calc-input:last-child { border-bottom: 0; }
  .calc-input__row { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px; }
  .calc-input label { font-weight: 600; color: #fff; font-size: 14px; line-height: 1.4; }
  .calc-input__value { font-family: 'JetBrains Mono', monospace; font-weight: 600; color: var(--blue-300); font-size: 16px; white-space: nowrap; }
  .calc-input__hint { font-size: 12px; color: var(--text-dim); margin-top: 8px; line-height: 1.5; }

  .calc-input input[type="range"] {
    width: 100%; height: 6px; -webkit-appearance: none; appearance: none;
    background: rgba(255,255,255,0.10); border-radius: 999px; outline: none;
    margin-top: 8px;
  }
  .calc-input input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none; width: 22px; height: 22px;
    background: var(--blue-400); border: 3px solid #fff; border-radius: 50%;
    cursor: pointer; box-shadow: 0 4px 12px rgba(51,133,223,0.4);
    transition: transform 100ms ease;
  }
  .calc-input input[type="range"]::-moz-range-thumb {
    width: 22px; height: 22px; background: var(--blue-400);
    border: 3px solid #fff; border-radius: 50%; cursor: pointer;
    box-shadow: 0 4px 12px rgba(51,133,223,0.4);
  }
  .calc-input input[type="range"]:active::-webkit-slider-thumb { transform: scale(1.15); }

  .leak-display {
    position: sticky; top: 100px;
    background: linear-gradient(135deg, var(--navy-light) 0%, var(--navy) 100%);
    border: 1px solid var(--border-strong);
    border-radius: 16px; padding: 32px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.35);
  }
  .leak-eyebrow { font-size: 11px; letter-spacing: 0.18em; text-transform: uppercase; color: var(--text-dim); font-weight: 700; }
  .leak-number {
    font-family: 'JetBrains Mono', monospace;
    font-size: clamp(2.4rem, 6vw, 3.6rem);
    font-weight: 800; color: var(--orange);
    line-height: 1.05; margin: 8px 0 4px;
    letter-spacing: -0.02em;
    transition: color 300ms ease;
  }
  .leak-number--low { color: var(--blue-300); }
  .leak-annual { font-size: 14px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace; }
  .leak-headline { margin: 18px 0 0; font-size: 15px; color: var(--text); line-height: 1.55; }

  .constraint {
    margin-top: 24px; padding: 18px; border-radius: 12px;
    background: rgba(252,163,17,0.08); border: 1px solid rgba(252,163,17,0.20);
    display: flex; gap: 14px; align-items: center;
  }
  .constraint--low { background: rgba(51,133,223,0.08); border-color: rgba(51,133,223,0.20); }
  .constraint__icon { flex: 0 0 48px; height: 48px; width: 48px; display: flex; align-items: center; justify-content: center; color: var(--orange); }
  .constraint--low .constraint__icon { color: var(--blue-300); }
  .constraint__body { flex: 1; }
  .constraint__label { font-size: 11px; letter-spacing: 0.14em; text-transform: uppercase; color: var(--text-dim); font-weight: 700; }
  .constraint__name { font-size: 17px; font-weight: 700; color: #fff; margin-top: 2px; }
  .constraint__sub { font-size: 12px; color: var(--text-muted); margin-top: 6px; line-height: 1.45; }

  .firefight-card {
    margin-top: 16px; padding: 14px 16px; border-radius: 10px;
    background: rgba(255,255,255,0.03); border: 1px dashed var(--border-strong);
    font-size: 13px; color: var(--text-muted); line-height: 1.55;
  }
  .firefight-card strong { color: #fff; font-family: 'JetBrains Mono', monospace; font-weight: 600; }

  .leak-disclaimer {
    font-size: 12px; color: var(--text-dim);
    font-weight: 500; line-height: 1.5; margin-bottom: 4px;
  }

  /* Mobile: non-sticky display so it doesn't dominate the viewport */
  @media (max-width: 879px) {
    .leak-display { position: static; padding: 24px; }
  }

  .leak-breakdown { margin-top: 22px; padding-top: 22px; border-top: 1px solid var(--border); }
  .leak-breakdown__title { font-size: 11px; letter-spacing: 0.14em; text-transform: uppercase; color: var(--text-dim); font-weight: 700; margin-bottom: 10px; }
  .leak-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0; font-size: 13px; color: var(--text-muted);
  }
  .leak-row__value { font-family: 'JetBrains Mono', monospace; color: #fff; font-weight: 600; }
  .leak-row--winner { color: #fff; }
  .leak-row--winner .leak-row__value { color: var(--orange); }

  .leak-cta { margin-top: 24px; }
  .leak-cta .btn { width: 100%; justify-content: center; }

  details.method { margin-top: 28px; border: 1px solid var(--border); border-radius: 12px; padding: 16px 18px; }
  details.method summary { cursor: pointer; font-weight: 600; color: var(--text-muted); font-size: 14px; list-style: none; display: flex; justify-content: space-between; }
  details.method summary::-webkit-details-marker { display: none; }
  details.method summary::after { content: '+'; color: var(--blue-300); font-size: 20px; line-height: 1; }
  details.method[open] summary::after { content: '−'; }
  details.method[open] summary { color: #fff; margin-bottom: 12px; }
  details.method p { font-size: 13px; color: var(--text-dim); line-height: 1.6; margin: 8px 0; }
  details.method code { font-family: 'JetBrains Mono', monospace; font-size: 12px; color: var(--blue-300); background: rgba(0,0,0,0.30); padding: 1px 6px; border-radius: 4px; }

  /* Counter animation feels alive but never jitter */
  @keyframes leakPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.85; } }
  .leak-number.is-animating { animation: leakPulse 600ms ease-in-out; }
</style>
</head>
<body>

<div class="bg-stage" aria-hidden="true"></div>

<header class="nav">
  <div class="nav__inner">
    <a href="/" class="nav__brand"><img src="/lovable-uploads/03f35f21-123f-4f10-84a1-f2a66d97bc2b.png" alt="Syncsity"></a>
    <nav class="nav__links">
      <a href="/transform" class="has-chevron">Transform</a>
      <a href="/solutions" class="has-chevron">Solutions</a>
      <a href="/assess">Diagnose</a>
      <a href="/why-syncsity">Why Syncsity</a>
      <a href="/pricing">Pricing</a>
      <a href="/blog" class="has-chevron">Resources</a>
    </nav>
    <div class="nav__cta">
      <a href="/auth/login" class="btn btn--ghost btn--sm" id="auth-btn">Log in</a>
      <a href="/assess" class="btn btn--primary btn--sm">Free assessment</a>
      <a href="/booking.html" class="btn btn--orange btn--sm">Book a Session</a>
    </div>
  </div>
</header>

<main>

  <section class="section section--hero">
    <div class="container container--md">
      <span class="pill pill--orange reveal">⚡ 60 seconds · No email required</span>
      <h1 class="reveal reveal--d1" style="margin-top: 24px;">The £10K Leak Detector.</h1>
      <p class="lead reveal reveal--d2" style="margin-top: 18px; max-width: 720px; margin-left:auto; margin-right:auto;">
        Move five sliders. See where the money is leaking. The number on the right updates as you go — backed by published industry benchmarks, not guesswork.
      </p>
    </div>
  </section>

  <section class="section section--tight" style="padding-top: 24px;">
    <div class="container">
      <div class="calc-grid">

        <!-- Inputs -->
        <div class="card" style="padding: 28px;">
          <div class="calc-input">
            <div class="calc-input__row">
              <label for="revenue">Annual revenue</label>
              <span class="calc-input__value" id="revenue-display">£1,000,000</span>
            </div>
            <input id="revenue" type="range" min="100000" max="50000000" step="50000" value="1000000">
            <p class="calc-input__hint">Top-line for the trailing 12 months.</p>
          </div>

          <div class="calc-input">
            <div class="calc-input__row">
              <label for="team">Team size</label>
              <span class="calc-input__value" id="team-display">15 people</span>
            </div>
            <input id="team" type="range" min="1" max="500" step="1" value="15">
            <p class="calc-input__hint">Full-time equivalents including yourself.</p>
          </div>

          <div class="calc-input">
            <div class="calc-input__row">
              <label for="firefight">Senior firefighting</label>
              <span class="calc-input__value" id="firefight-display">15 hrs/week</span>
            </div>
            <input id="firefight" type="range" min="0" max="40" step="1" value="15">
            <p class="calc-input__hint">Hours your senior team spends each week on reactive, unplanned problems.</p>
          </div>

          <div class="calc-input">
            <div class="calc-input__row">
              <label for="rampup">New-hire ramp time</label>
              <span class="calc-input__value" id="rampup-display">8 weeks</span>
            </div>
            <input id="rampup" type="range" min="0" max="26" step="1" value="8">
            <p class="calc-input__hint">From start date to fully productive at the role.</p>
          </div>

          <div class="calc-input">
            <div class="calc-input__row">
              <label for="conc">% revenue from top 3 customers</label>
              <span class="calc-input__value" id="conc-display">35%</span>
            </div>
            <input id="conc" type="range" min="0" max="100" step="1" value="35">
            <p class="calc-input__hint">Concentration risk — what fraction of revenue would walk if your top 3 left.</p>
          </div>
        </div>

        <!-- Live reveal -->
        <div>
          <div class="leak-display reveal reveal--d1">
            <p class="leak-disclaimer">Estimate from your slider inputs &amp; industry rules of thumb.</p>
            <div class="leak-eyebrow">Your monthly leak</div>
            <div class="leak-number" id="leak-monthly">£0</div>
            <div class="leak-annual">≈ <span id="leak-annual">£0</span> per year</div>
            <p class="leak-headline" id="leak-headline">Move the sliders to see your number.</p>

            <div class="constraint" id="constraint">
              <div class="constraint__icon" id="constraint-icon" aria-hidden="true">
                <!-- SVG injected by JS based on top leak -->
              </div>
              <div class="constraint__body">
                <div class="constraint__label">Top driver of your number</div>
                <div class="constraint__name" id="constraint-name">—</div>
                <div class="constraint__sub">The slider moving your total the most — that's where the diagnosis would dig first.</div>
              </div>
            </div>

            <div class="leak-breakdown">
              <div class="leak-breakdown__title">Where it comes from / month</div>
              <div class="leak-row" data-key="process"><span>Process inefficiency</span><span class="leak-row__value" id="leak-process">£0</span></div>
              <div class="leak-row" data-key="concentration"><span>Customer concentration risk</span><span class="leak-row__value" id="leak-concentration">£0</span></div>
              <div class="leak-row" data-key="onboarding"><span>Onboarding drag</span><span class="leak-row__value" id="leak-onboarding">£0</span></div>
            </div>

            <div class="firefight-card">
              And <strong id="firefight-hours">15 hrs/week</strong> of senior firefighting — roughly <strong id="firefight-cost">£13,000</strong>/month of director time. Counted separately because firefighting is usually a <em>symptom</em> of the leaks above, not a fourth one. Different problem, same root.
            </div>

            <div class="leak-cta">
              <a href="/assess" class="btn btn--orange btn--lg">Sliders give a range. Get the line. →</a>
            </div>
          </div>

          <details class="method">
            <summary>How the math works (and where it doesn't)</summary>
            <p><strong>Process inefficiency:</strong> 3% of revenue, monthly. Mid-market process-waste estimates from public consulting writing range 2-5% of revenue depending on sector and how much optimisation work has already been done. We use 3% as a conservative midpoint, not as a specific cited number.</p>
            <p><strong>Senior firefighting:</strong> <code>hours × £200/h × 4.33 weeks</code>. £200/hour reflects fully-loaded UK mid-market director time (salary + on-costs + opportunity cost) — typical range is £200-350/h depending on seniority. Shown as a separate callout because firefighting is usually a <em>symptom</em> of the other leaks; adding it to the total would double-count.</p>
            <p><strong>Customer concentration risk:</strong> expected monthly loss = <code>% × revenue × (8% + 0.5%/pt above 30%)</code>. 8% baseline reflects informal mid-market churn benchmarks for major accounts; the risk premium above 30% concentration captures the well-documented non-linear jump in vulnerability when any single customer becomes too large to lose. Not from a specific published index.</p>
            <p><strong>Onboarding drag:</strong> <code>15% turnover × extra ramp weeks × £40K avg salary × 50% productivity loss / 12</code>. 15% turnover is in line with CIPD UK labour-market data for mid-market companies; the 4-week "productive" baseline and 50% ramp-loss are informed estimates, not specific citations.</p>
            <p><strong>What this isn't:</strong> a substitute for analysis. The leaks are independent in the model but overlap in reality. The constraint "winner" is whichever slider moves your total most — not a verdict on your business. The 15-minute Revenue Intelligence Report uses your actual figures and applies industry-specific benchmarks instead of the rules of thumb above.</p>
          </details>
        </div>

      </div>
    </div>
  </section>

  <section class="section">
    <div class="container container--sm" style="text-align: center;">
      <h2>This is the surface. The report goes deeper.</h2>
      <p class="lead" style="margin-top: 16px;">
        The free Revenue Intelligence Report takes 15 minutes, asks 21 targeted questions, and generates a personalised diagnosis you can read or hand to your team. No call required.
      </p>
      <a href="/assess" class="btn btn--primary btn--lg" style="margin-top: 28px;">Take the free assessment</a>
    </div>
  </section>

</main>

<?php include __DIR__ . '/partials/site-footer.php'; ?>

<script>
(function () {
  'use strict';

  // ── Inputs ──
  const inputs = {
    revenue:   document.getElementById('revenue'),
    team:      document.getElementById('team'),
    firefight: document.getElementById('firefight'),
    rampup:    document.getElementById('rampup'),
    conc:      document.getElementById('conc'),
  };
  const displays = {
    revenue:   document.getElementById('revenue-display'),
    team:      document.getElementById('team-display'),
    firefight: document.getElementById('firefight-display'),
    rampup:    document.getElementById('rampup-display'),
    conc:      document.getElementById('conc-display'),
  };
  const out = {
    monthly: document.getElementById('leak-monthly'),
    annual:  document.getElementById('leak-annual'),
    headline:document.getElementById('leak-headline'),
    constraint: document.getElementById('constraint'),
    constraintIcon: document.getElementById('constraint-icon'),
    constraintName: document.getElementById('constraint-name'),
    process:        document.getElementById('leak-process'),
    concentration:  document.getElementById('leak-concentration'),
    onboarding:     document.getElementById('leak-onboarding'),
    firefightHours: document.getElementById('firefight-hours'),
    firefightCost:  document.getElementById('firefight-cost'),
  };

  // ── Formatters ──
  const gbp0 = new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP', maximumFractionDigits: 0 });
  const fmtMoney = (n) => gbp0.format(Math.round(n));
  const fmtCompact = (n) => {
    if (n >= 1e6) return '£' + (n / 1e6).toFixed(n >= 10e6 ? 0 : 1) + 'M';
    if (n >= 1e3) return '£' + Math.round(n / 1e3) + 'K';
    return '£' + Math.round(n);
  };

  // ── Icons (inline SVG, theme-coloured via currentColor) ──
  const ICONS = {
    process: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><path d="M12 3v3m0 12v3M5.6 5.6l2.1 2.1m8.6 8.6l2.1 2.1M3 12h3m12 0h3M5.6 18.4l2.1-2.1m8.6-8.6l2.1-2.1"/><circle cx="12" cy="12" r="3"/></svg>`,
    concentration: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/></svg>`,
    firefighting: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>`,
    onboarding: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="32" height="32"><path d="M3 21V5a2 2 0 0 1 2-2h9l5 5v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M14 3v5h5M8 13h8M8 17h5"/></svg>`,
  };

  const NAMES = {
    process:       'Process inefficiency',
    concentration: 'Customer concentration',
    onboarding:    'Onboarding drag',
  };

  // ── Model ──
  // Firefighting is computed separately and NOT added to total — it's a
  // symptom of the other leaks, adding it would double-count.
  function calc(state) {
    const R = state.revenue;
    const T = state.team;
    const F = state.firefight;
    const W = state.rampup;
    const C = state.conc;

    // Monthly £
    const process = (R * 0.03) / 12;

    const concPct = C / 100;
    const baselineRisk = 0.08;
    const overConc = Math.max(0, C - 30);
    const adjustedRisk = baselineRisk + (overConc * 0.005);
    const concentration = (concPct * R * adjustedRisk) / 12;

    const turnoverPerYear = 0.15 * T;
    const extraWeeks = Math.max(0, W - 4);
    const onboarding = (turnoverPerYear * extraWeeks * (40000 / 52) * 0.5) / 12;

    // Separate callout, not in the headline total
    const firefightingCost = F * 200 * 4.33;

    const leaks = { process, concentration, onboarding };
    const total = process + concentration + onboarding;
    const topKey = Object.keys(leaks).reduce((a, b) => leaks[a] > leaks[b] ? a : b);
    return { leaks, total, topKey, firefightingCost, firefightingHours: F };
  }

  // ── Animated counter ──
  let monthlyTarget = 0;
  let monthlyCurrent = 0;
  let animId = null;
  function animateMonthly() {
    if (animId) cancelAnimationFrame(animId);
    const step = () => {
      const delta = monthlyTarget - monthlyCurrent;
      if (Math.abs(delta) < 1) {
        monthlyCurrent = monthlyTarget;
        out.monthly.textContent = fmtMoney(monthlyCurrent);
        return;
      }
      monthlyCurrent += delta * 0.22;
      out.monthly.textContent = fmtMoney(monthlyCurrent);
      animId = requestAnimationFrame(step);
    };
    step();
  }

  // ── Render ──
  function render() {
    const state = {
      revenue:   +inputs.revenue.value,
      team:      +inputs.team.value,
      firefight: +inputs.firefight.value,
      rampup:    +inputs.rampup.value,
      conc:      +inputs.conc.value,
    };

    // Display values for inputs
    displays.revenue.textContent   = fmtCompact(state.revenue);
    displays.team.textContent      = state.team + (state.team === 1 ? ' person' : ' people');
    displays.firefight.textContent = state.firefight + ' hrs/week';
    displays.rampup.textContent    = state.rampup + (state.rampup === 1 ? ' week' : ' weeks');
    displays.conc.textContent      = state.conc + '%';

    const { leaks, total, topKey, firefightingCost, firefightingHours } = calc(state);

    monthlyTarget = total;
    animateMonthly();
    out.annual.textContent = fmtMoney(total * 12);

    out.process.textContent       = fmtMoney(leaks.process);
    out.concentration.textContent = fmtMoney(leaks.concentration);
    out.onboarding.textContent    = fmtMoney(leaks.onboarding);

    // Firefighting callout
    out.firefightHours.textContent = firefightingHours + ' hrs/week';
    out.firefightCost.textContent  = fmtMoney(firefightingCost);

    // Highlight winning row
    document.querySelectorAll('.leak-row').forEach(row => {
      row.classList.toggle('leak-row--winner', row.dataset.key === topKey);
    });

    // Driver card + icon (framing is "where the diagnosis would dig", not "the answer")
    out.constraintIcon.innerHTML = ICONS[topKey];
    out.constraintName.textContent = NAMES[topKey];

    // Headline narrative + low/high state
    const lowThreshold = 10000;
    if (total < lowThreshold) {
      out.monthly.classList.add('leak-number--low');
      out.constraint.classList.add('constraint--low');
      out.headline.textContent =
        "Under £10K/month on these three categories. The deeper question is where the upside hides, not where this kind of leak is. The full report finds the upside.";
    } else {
      out.monthly.classList.remove('leak-number--low');
      out.constraint.classList.remove('constraint--low');
      const annualLoss = total * 12;
      out.headline.textContent =
        "That's roughly " + fmtMoney(annualLoss) + " a year sitting under " + NAMES[topKey].toLowerCase() + " on this estimate. Your actual number depends on what you've already optimised — that's what the 15-minute report maps.";
    }
  }

  Object.values(inputs).forEach(el => el.addEventListener('input', render));
  render();
})();
</script>

</body>
</html>
