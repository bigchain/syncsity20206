<?php
declare(strict_types=1);
define('SYNC_ROOT', __DIR__);
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/csrf.php';

$pageTitle = 'Syncsity — Find the £50M opportunity hiding inside your business';
$pageDesc  = 'In 15 minutes we uncover the hidden constraint costing you £10K+ a month and tell you exactly how to fix it. Free Revenue Intelligence Report. No call required.';
$activeNav = 'home';

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<!-- ── HERO ───────────────────────────────────────────────────────────── -->
<!-- Background video (fixed, behind everything) -->
<div class="hero-video" aria-hidden="true">
  <div class="hero-video__fallback"></div>
  <iframe
    id="background-video"
    class="hero-video__iframe"
    src="https://www.youtube-nocookie.com/embed/b-lHRWWUpsY?controls=0&autoplay=1&mute=1&loop=1&playlist=b-lHRWWUpsY&showinfo=0&rel=0&modestbranding=1&playsinline=1&enablejsapi=1&vq=hd1080"
    title="Background"
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
    loading="lazy"></iframe>
  <div class="hero-video__overlay"></div>
</div>

<section class="hero hero--video">
  <div class="container">
    <div class="hero__inner">
      <span class="pill pill--accent">★ Free Revenue Intelligence Report — no call required</span>

      <h1 class="hero__title" data-reveal>
        <span class="line">We Find The Constraints.</span>
        <span class="line accent">We Remove The Waste.</span>
        <span class="line">You Achieve Breakthrough Growth.</span>
      </h1>

      <p class="lead hero__lede">
        Trusted by ambitious companies. First we diagnose what's blocking your growth.
        Then we apply AI and automation only where it multiplies results.
      </p>

      <!-- AHA email capture -->
      <div class="aha-card" data-reveal>
        <p style="font-size:1.05rem; line-height:1.7;">
          You already <em>sense</em> something's blocking your growth — you just can't pinpoint what.
          In 15 minutes, we'll uncover the hidden constraint costing you <strong>£10K+ monthly</strong>
          and show you exactly how to fix it. Even if you never speak to us again.
        </p>
        <form class="aha-form" data-aha-form>
          <input type="email" class="input" placeholder="Your work email" required autocomplete="email" name="email">
          <button type="submit" class="btn btn--accent">
            Get my Aha! Moment <span class="arrow">→</span>
          </button>
        </form>
        <p class="dim" style="font-size:0.82rem; margin-top:var(--s-3); text-align:center;">
          ★ No obligation. Guaranteed actionable insight. Built by operators for operators.
        </p>
      </div>

      <div class="hero__pillars" data-reveal>
        <div class="hero__pillar">
          <span class="hero__pillar-tag is-blue">1</span>
          <h3>Immediate</h3>
          <p>30-day quick wins that prove ROI before you commit to anything bigger.</p>
        </div>
        <div class="hero__pillar">
          <span class="hero__pillar-tag">↗</span>
          <h3>Exponential</h3>
          <p>An 18-month plan to dominate your market — phase by phase, leverage by leverage.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── STATS ─────────────────────────────────────────────────────────── -->
<section class="section section--tight">
  <div class="container">
    <div class="stats" data-reveal>
      <div>
        <div class="stat__num">£10K<span class="unit">+</span></div>
        <div class="stat__label">Average monthly leak we find in 15 minutes</div>
      </div>
      <div>
        <div class="stat__num">7<span class="unit">×</span></div>
        <div class="stat__label">Capacity unlock when the right constraint is removed</div>
      </div>
      <div>
        <div class="stat__num">90<span class="unit">d</span></div>
        <div class="stat__label">From diagnosis to measurable revenue impact</div>
      </div>
      <div>
        <div class="stat__num">100<span class="unit">%</span></div>
        <div class="stat__label">Implementation done <em>with</em> you, not handed off</div>
      </div>
    </div>
  </div>
</section>

<!-- ── THE THREE THINGS ─────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow">The Syncsity method</span>
      <h2>Three moves. In order. No fluff.</h2>
      <p>Most growth advice skips the first two and starts with tools. That's why most "transformation" projects burn cash and produce nothing.</p>
    </div>

    <div class="grid-3">
      <div class="card card--feature card--hover" data-reveal>
        <div class="card__icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        </div>
        <h3>1. Find the constraint</h3>
        <p class="muted">
          The thing throttling growth is rarely the thing you think it is. We diagnose your operation —
          revenue, ops, retention, founder time — and name the exact bottleneck. With evidence.
        </p>
      </div>
      <div class="card card--feature card--hover" data-reveal>
        <div class="card__icon card__icon--orange">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6h18l-2 13H5L3 6Z"/><path d="M8 6V4a4 4 0 0 1 8 0v2"/></svg>
        </div>
        <h3>2. Remove the waste</h3>
        <p class="muted">
          Then we cut. Manual ops that should be automated. Decisions made on vibes that should be data.
          Hand-offs that should be deletions. Every removed inefficiency widens the constraint.
        </p>
      </div>
      <div class="card card--feature card--hover" data-reveal>
        <div class="card__icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3v18h18"/><path d="m7 12 4-4 4 4 6-6"/></svg>
        </div>
        <h3>3. Compound the breakthrough</h3>
        <p class="muted">
          With the bottleneck gone and the waste cut, AI and automation finally do what they're supposed
          to: multiply. We layer them in only where they compound the gain — never as cosmetics.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ── PROCESS ──────────────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow">How a Syncsity engagement runs</span>
      <h2>Four phases. Each one earns the next.</h2>
    </div>

    <div class="steps">
      <div class="step" data-reveal>
        <div class="step__num">1</div>
        <h4>Aha! Diagnostic</h4>
        <p>15-minute assessment. Free. AI-written Revenue Intelligence Report names the exact constraint costing you money — with evidence.</p>
      </div>
      <div class="step" data-reveal>
        <div class="step__num">2</div>
        <h4>Strategy Session</h4>
        <p>£950 paid 30-minute deep dive. We pressure-test the diagnosis with a senior operator and map the 90-day intervention.</p>
      </div>
      <div class="step" data-reveal>
        <div class="step__num">3</div>
        <h4>30-Day Quick Win</h4>
        <p>One scoped intervention. Done with you, not at you. Measurable revenue or capacity impact in the first month or your money back.</p>
      </div>
      <div class="step" data-reveal>
        <div class="step__num">4</div>
        <h4>18-Month Compounding</h4>
        <p>Phase-by-phase rollout — automation, AI, processes, hires. You own everything we build. We're an accelerator, not a dependency.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── SERVICES ──────────────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow">What we actually build</span>
      <h2>AI where it multiplies. Process where it scales. People where it matters.</h2>
    </div>

    <div class="grid-2">
      <div class="card card--hover" data-reveal>
        <div class="card__icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v4"/><path d="M12 18v4"/><path d="m4.93 4.93 2.83 2.83"/><path d="m16.24 16.24 2.83 2.83"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="m4.93 19.07 2.83-2.83"/><path d="m16.24 7.76 2.83-2.83"/></svg>
        </div>
        <h3>AI Voice & Conversation Operations</h3>
        <p class="muted">24/7 voice agents that handle inbound, qualify, book and route. 40+ languages. Branded, on-script, with human handoff. Cuts call-handling cost ~90%.</p>
      </div>
      <div class="card card--hover" data-reveal>
        <div class="card__icon card__icon--orange">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="7" r="4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><circle cx="17" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>
        </div>
        <h3>AI Sales System</h3>
        <p class="muted">A done-for-you outbound engine: prospect, personalise, sequence, book. Owned by you, controlled by you, measurable to the meeting.</p>
      </div>
      <div class="card card--hover" data-reveal>
        <div class="card__icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h4l3-9 4 18 3-9h4"/></svg>
        </div>
        <h3>Process Automation</h3>
        <p class="muted">We map your back office, kill the manual seams, and rebuild the tendons with workflow + AI. You don't add headcount — you add capacity.</p>
      </div>
      <div class="card card--hover" data-reveal>
        <div class="card__icon card__icon--orange">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>
        </div>
        <h3>Workforce Intelligence</h3>
        <p class="muted">Capture tribal knowledge from your senior people, generate training, monitor performance — without adding management overhead.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── INDUSTRIES ────────────────────────────────────────────────────── -->
<section class="section">
  <div class="container container--md">
    <div class="section-head" data-reveal>
      <span class="eyebrow">Built for operators in</span>
      <h2>Who we work with</h2>
      <p>Mid-market service businesses. UK & US. £1M–£250M. Ops-heavy. Growth-blocked.</p>
    </div>
    <div class="logos" data-reveal>
      <div>Agencies</div>
      <div>Professional Services</div>
      <div>E-commerce Ops</div>
      <div>SaaS Scale-ups</div>
      <div>Financial Services</div>
      <div>Logistics &amp; 3PL</div>
    </div>
  </div>
</section>

<!-- ── FAQ ───────────────────────────────────────────────────────────── -->
<section class="section">
  <div class="container container--md">
    <div class="section-head" data-reveal>
      <span class="eyebrow">Sceptical? Good.</span>
      <h2>Hard questions, plainly answered.</h2>
    </div>
    <div class="faq" data-reveal>
      <details>
        <summary>Is the assessment really free?</summary>
        <p>Yes. The 15-minute assessment and the AI-written Revenue Intelligence Report are both free. There is no card, no sales call required, no follow-up nag if the report tells you we can't help. We do this because the report is the most honest demo we can give you.</p>
      </details>
      <details>
        <summary>Why would you give it away?</summary>
        <p>Because if the diagnosis lands, you'll want the team that wrote it. And because we'd rather lose 100 free reports than waste an hour on the phone with someone we can't actually help.</p>
      </details>
      <details>
        <summary>Who is Syncsity NOT for?</summary>
        <p>Pre-revenue businesses, agencies that resell us as their "AI partner" without disclosure, and anyone looking for a tool to install rather than a system to build. We say no often.</p>
      </details>
      <details>
        <summary>Do you really use AI, or is this just packaging?</summary>
        <p>The Revenue Intelligence Report is generated by a multi-step AI pipeline that researches your business, your industry, and your specific answers. The strategic engagements that follow are led by a human operator — AI is a tool we deploy, not a product we sell.</p>
      </details>
      <details>
        <summary>What happens to my data?</summary>
        <p>UK-based, GDPR-compliant, ISO-27001-aligned. Your answers are yours. We don't train on them, sell them, or share them. See our <a href="/privacy">privacy policy</a>.</p>
      </details>
    </div>
  </div>
</section>

<!-- ── FINAL CTA ─────────────────────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="cta-strip" data-reveal>
      <span class="eyebrow" style="display:block; margin-bottom: var(--s-4);">Take the next 15 minutes</span>
      <h2>Find the £10K+ leak hiding in your business.</h2>
      <p class="lead">No call. No card. Free Revenue Intelligence Report, written for your business, in your inbox.</p>
      <div class="hero__cta-row" style="justify-content:center; margin-top: var(--s-8);">
        <a href="/assess" class="btn btn--primary btn--lg">Start my Aha! Assessment <span class="arrow">→</span></a>
        <a href="/services" class="btn btn--ghost btn--lg">See how we work</a>
      </div>
    </div>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
