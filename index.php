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

<!-- ── THREE FUNNEL CARDS (Diagnose / Solutions / Transform) ───────────── -->
<section class="section">
  <div class="container">
    <div class="funnel-grid">
      <a href="/assess" class="funnel-card" data-reveal>
        <div class="funnel-card__top">
          <div class="funnel-card__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
          </div>
          <div>
            <span class="funnel-card__num">01</span>
            <h3>Diagnose</h3>
          </div>
        </div>
        <h4 class="funnel-card__h">Find Your £50M Opportunity</h4>
        <p class="funnel-card__p">Uncover the hidden constraints blocking your exponential growth.</p>
        <ul class="funnel-card__list">
          <li>Executive strategy session</li>
          <li>Bottleneck analysis</li>
          <li>Competitive positioning</li>
          <li>90-day quick wins</li>
        </ul>
        <span class="funnel-card__cta">Book Diagnostic Session</span>
      </a>

      <a href="/services" class="funnel-card" data-reveal>
        <div class="funnel-card__top">
          <div class="funnel-card__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
          </div>
          <div>
            <span class="funnel-card__num">02</span>
            <h3>Solutions</h3>
          </div>
        </div>
        <h4 class="funnel-card__h">Fix Specific Problems</h4>
        <p class="funnel-card__p">Deploy targeted AI solutions for immediate impact.</p>
        <ul class="funnel-card__list">
          <li>AI Voice Operations</li>
          <li>AI Sales System</li>
          <li>Workforce Intelligence</li>
          <li>Custom Development</li>
        </ul>
        <span class="funnel-card__cta">Explore Solutions</span>
      </a>

      <a href="/how-we-work" class="funnel-card" data-reveal>
        <div class="funnel-card__top">
          <div class="funnel-card__icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4l3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>
          </div>
          <div>
            <span class="funnel-card__num">03</span>
            <h3>Transform</h3>
          </div>
        </div>
        <h4 class="funnel-card__h">Become The Market Leader</h4>
        <p class="funnel-card__p">Complete business transformation with C-suite partnership.</p>
        <ul class="funnel-card__list">
          <li>Market Domination</li>
          <li>Revenue Acceleration</li>
          <li>Operational Supremacy</li>
          <li>Custom Programmes</li>
        </ul>
        <span class="funnel-card__cta">Start Transformation</span>
      </a>
    </div>
  </div>
</section>

<!-- ── CLIENT LOGOS — "Clients We've Worked With" ────────────────────── -->
<section class="section section--tight clients">
  <div class="container">
    <div class="section-head" data-reveal>
      <h2>Clients We've Worked With</h2>
      <div class="clients__rule" aria-hidden="true"></div>
      <p>Some of the clients we've had the privilege to collaborate with directly and indirectly.</p>
    </div>

    <div class="clients__marquee" data-reveal>
      <div class="clients__track">
        <?php
        $logos = [
          ['name' => 'PE',     'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/pe.jpg'],
          ['name' => 'SEC',    'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/sec.jpg'],
          ['name' => 'KEW',    'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/kew.jpg'],
          ['name' => 'Elite',  'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/elite.jpg'],
          ['name' => 'Lola',   'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/lola.jpg'],
          ['name' => 'AIS',    'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/ais.jpg'],
          ['name' => 'SR-1',   'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/sr-1.jpg'],
          ['name' => 'Nejob',  'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/nejob.jpg'],
          ['name' => 'Lavy',   'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/lavy.jpg'],
          ['name' => 'Rosco',  'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/rosco.jpg'],
          ['name' => 'WU',     'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/wu.jpg'],
          ['name' => 'SO',     'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/so.jpg'],
          ['name' => 'Medi',   'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/medi.jpg'],
          ['name' => 'Stell',  'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/stell.jpg'],
          ['name' => 'TTI',    'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/tti.jpg'],
          ['name' => 'TDO',    'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/tdo.jpg'],
          ['name' => 'Hadav',  'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/hadav.jpg'],
          ['name' => 'SR',     'src' => 'https://www.syncsity.com/wp-content/uploads/2024/07/sr.jpg'],
        ];
        // Render the list twice so the CSS marquee loops seamlessly
        foreach ([0, 1] as $loop):
          foreach ($logos as $lg): ?>
            <div class="clients__logo" aria-hidden="<?= $loop ? 'true' : 'false' ?>">
              <img src="<?= e($lg['src']) ?>" alt="<?= $loop ? '' : e($lg['name'] . ' logo') ?>" loading="lazy" width="160" height="80">
            </div>
        <?php endforeach; endforeach; ?>
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

<!-- ── WHY US TEASER ────────────────────────────────────────────────── -->
<section class="section section--tight">
  <div class="container container--md">
    <div class="section-head" data-reveal>
      <span class="eyebrow">Why Syncsity</span>
      <h2>We're not a consultancy. We're an operator's leverage.</h2>
      <p>Big consulting sells decks. AI agencies sell tools. We diagnose first, build second, automate last.</p>
    </div>
    <div style="display:flex; gap: var(--s-3); flex-wrap:wrap; justify-content:center;" data-reveal>
      <span class="pill pill--accent">Diagnosis first</span>
      <span class="pill pill--orange">Senior operators only</span>
      <span class="pill">You own everything we build</span>
      <span class="pill pill--success">Money-back milestones</span>
    </div>
    <div style="text-align:center; margin-top: var(--s-8);" data-reveal>
      <a href="/why-us" class="btn btn--ghost">See the full comparison <span class="arrow">→</span></a>
    </div>
  </div>
</section>

<!-- ── FINAL CTA (Aha Moment) ───────────────────────────────────────── -->
<section class="section">
  <div class="container">
    <div class="cta-strip" data-reveal>
      <span class="eyebrow" style="display:block; margin-bottom: var(--s-4);">Take the next 15 minutes</span>
      <h2>Find the £10K+ leak hiding in your business.</h2>
      <p class="lead">No call. No card. Free Revenue Intelligence Report, written for your business, in your inbox.</p>
      <div class="hero__cta-row" style="justify-content:center; margin-top: var(--s-8);">
        <a href="/assess" class="btn btn--primary btn--lg">Start my Aha! Assessment <span class="arrow">→</span></a>
        <a href="/how-we-work" class="btn btn--ghost btn--lg">See how we work</a>
      </div>
    </div>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
