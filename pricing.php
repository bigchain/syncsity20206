<?php
declare(strict_types=1);
define('SYNC_ROOT', __DIR__);
require_once SYNC_ROOT . '/lib/config.php';

$pageTitle = 'Pricing — Aha! diagnostic free, strategy session £950, then scoped engagements';
$pageDesc  = 'No retainers, no SaaS fees. The diagnosis is free. Strategy session pays for itself. Engagements are scoped to outcomes.';
$activeNav = 'pricing';

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<section class="section section--hero">
  <div class="bg-glow"></div>
  <div class="container container--md" style="text-align:center; position:relative;">
    <span class="eyebrow">Pricing</span>
    <h1 style="margin: var(--s-4) 0 var(--s-6);">Pay for outcomes, not for slides.</h1>
    <p class="lead">No retainers, no agency seats, no monthly SaaS that lives forever. Three tiers, one philosophy: each one only costs more once it's already paid for itself.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="container">
    <div class="price-grid">
      <div class="price-card" data-reveal>
        <h3>Aha! Diagnostic</h3>
        <div class="price">Free <small>· 15 minutes</small></div>
        <p class="muted" style="margin-top: var(--s-3);">Honest diagnosis of the constraint blocking your business. Written report. No call. No card.</p>
        <ul>
          <li>15-minute conversational assessment</li>
          <li>AI-written Revenue Intelligence Report</li>
          <li>The exact named constraint (with evidence)</li>
          <li>£10K+ leak quantification</li>
          <li>3-step intervention roadmap</li>
          <li>Yours to keep, regardless of next steps</li>
        </ul>
        <a href="/assess" class="btn btn--ghost btn--block">Start the diagnostic <span class="arrow">→</span></a>
      </div>

      <div class="price-card price-card--featured" data-reveal>
        <h3>Strategy Session</h3>
        <div class="price">£950 <small>· 30 minutes · senior operator</small></div>
        <p class="muted" style="margin-top: var(--s-3);">Pressure-test the diagnosis. Map the 90-day intervention. Decide if you want to build it with us or someone else.</p>
        <ul>
          <li>30 minutes with a Syncsity senior operator</li>
          <li>Diagnosis pressure-test with your real numbers</li>
          <li>90-day intervention plan, written down</li>
          <li>Implementation cost estimate (no surprises)</li>
          <li>Recording + summary you can hand to your team</li>
          <li>Fee credited against any subsequent engagement</li>
        </ul>
        <a href="<?= e(CALENDLY_URL) ?>" class="btn btn--primary btn--block">Book a strategy session <span class="arrow">→</span></a>
      </div>

      <div class="price-card" data-reveal>
        <h3>Engagement</h3>
        <div class="price">From £8K <small>· scoped, fixed</small></div>
        <p class="muted" style="margin-top: var(--s-3);">Scoped intervention with a fixed price and a 30-day measurable outcome. No retainers. No "discovery" months.</p>
        <ul>
          <li>One scoped intervention at a time</li>
          <li>30-day measurable revenue or capacity outcome</li>
          <li>Built with you, fully documented, fully owned by you</li>
          <li>Senior operator + supporting build team</li>
          <li>Phase-by-phase rollout — never bundled</li>
          <li>Money-back if we miss the milestone</li>
        </ul>
        <a href="/contact?subject=Engagement" class="btn btn--ghost btn--block">Talk to us <span class="arrow">→</span></a>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container container--md">
    <div class="section-head" data-reveal>
      <span class="eyebrow">The honest fine print</span>
      <h2>What we won't do</h2>
    </div>
    <div class="grid-2" data-reveal>
      <div class="card">
        <h3>No retainers</h3>
        <p class="muted">"Strategic retainers" are how consultancies bill for showing up. We don't sell our presence — we sell outcomes. Each engagement is scoped, fixed, and earned.</p>
      </div>
      <div class="card">
        <h3>No vendor lock-in</h3>
        <p class="muted">Everything we build runs on tools you already pay for, or on infrastructure you own. We don't put you on a Syncsity platform you can't escape from.</p>
      </div>
      <div class="card">
        <h3>No "discovery" months</h3>
        <p class="muted">The diagnostic is 15 minutes. The strategy session is 30. If we need 8 weeks of "discovery" before we can name what's wrong, we're not the right team.</p>
      </div>
      <div class="card">
        <h3>No bundled pitches</h3>
        <p class="muted">If only one of our four service lines will move your number, we'll only sell you that one. Bundling is how agencies hit their quota — not how operators get to outcomes.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-strip" data-reveal>
      <h2>Start where every Syncsity engagement starts.</h2>
      <p class="lead">The free diagnostic. 15 minutes. No call.</p>
      <a href="/assess" class="btn btn--primary btn--lg" style="margin-top: var(--s-6);">Get my free Revenue Intelligence Report <span class="arrow">→</span></a>
    </div>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
