<?php
/**
 * Universal site nav for marketing pages.
 *
 * Optional before include:
 *   $page_path_prefix (string) — '/' for site-root pages, '../' for nested
 *   $nav_active       (string) — slug to highlight: transform|solutions|diagnose|why|pricing|resources
 *
 * Renders:
 *   - Skip-to-content link (.skip-link → #main)
 *   - Sticky <header class="nav"> with logo, 6 nav items (3 with hover dropdowns),
 *     and 3 CTAs (Log in / Free assessment / Book a Session)
 *   - Hover dropdowns:
 *     * Transform   → 3 programme cards
 *     * Solutions   → 4 solution cards
 *     * Resources   → 2 resource cards (ROI Calculator / Blog)
 *
 * Mobile (<880px) hides desktop dropdowns and lets nav-mobile.js build an
 * accordion drawer triggered by the hamburger button.
 */
$page_path_prefix = $page_path_prefix ?? '/';
$nav_active       = $nav_active       ?? '';
$h = $h ?? fn($s) => htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// Mark active link helper
$is = fn($k) => $nav_active === $k ? ' is-active' : '';
?>
<a href="#main" class="skip-link">Skip to main content</a>

<header class="nav" role="banner">
  <div class="nav__inner">

    <a href="/" class="nav__brand" aria-label="Syncsity home">
      <img src="<?= $h($page_path_prefix) ?>lovable-uploads/03f35f21-123f-4f10-84a1-f2a66d97bc2b.png"
           alt="Syncsity logo" width="200" height="36"
           decoding="async" fetchpriority="high">
    </a>

    <nav class="nav__links" aria-label="Primary">

      <div class="nav__has-menu">
        <a href="/transform" class="has-chevron<?= $is('transform') ?>">Transform</a>
        <div class="nav__menu nav__menu--transform" role="menu" aria-label="Transform">
          <div class="nav__menu__grid">

            <a class="nav__menu__card" href="/transform/market-domination" role="menuitem">
              <div class="nav__menu__card__head">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#FCA311" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>
                <h3>Market Domination<br>Programme</h3>
              </div>
              <p class="nav__menu__card__tag">Own Your Industry in 18 Months</p>
              <p class="nav__menu__card__body">Transform from competitor to category king with comprehensive AI integration, competitive intelligence, and strategic positioning.</p>
              <span class="nav__menu__card__more">Learn More</span>
            </a>

            <a class="nav__menu__card" href="/transform/revenue-acceleration" role="menuitem">
              <div class="nav__menu__card__head">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#FCA311" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>
                <h3>Revenue Acceleration<br>Programme</h3>
              </div>
              <p class="nav__menu__card__tag">10X Growth Without 10X Headcount</p>
              <p class="nav__menu__card__body">Build self-multiplying revenue engines that generate leads, close deals, and expand accounts 24/7 through intelligent automation.</p>
              <span class="nav__menu__card__more">Learn More</span>
            </a>

            <a class="nav__menu__card" href="/transform/operational-supremacy" role="menuitem">
              <div class="nav__menu__card__head">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#FCA311" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                <h3>Operational Supremacy<br>Programme</h3>
              </div>
              <p class="nav__menu__card__tag">Cut Costs 40% While Scaling Infinitely</p>
              <p class="nav__menu__card__body">Achieve 10X operational efficiency through intelligent process automation, workforce optimization, and AI systems.</p>
              <span class="nav__menu__card__more">Learn More</span>
            </a>

          </div>
          <div class="nav__menu__footer">
            <a href="/transform">Compare All Transformations</a>
          </div>
        </div>
      </div>

      <div class="nav__has-menu">
        <a href="/solutions" class="has-chevron<?= $is('solutions') ?>">Solutions</a>
        <div class="nav__menu nav__menu--solutions" role="menu" aria-label="Solutions">
          <div class="nav__menu__grid">

            <a class="nav__menu__card" href="/solutions/voice-solutions" role="menuitem">
              <div class="nav__menu__card__head">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/></svg>
                <h3>AI Voice Operations</h3>
              </div>
              <p class="nav__menu__card__tag">Replace Your Call Center with AI</p>
              <p class="nav__menu__card__body">Deploy voice agents that handle unlimited conversations 24/7 in 40+ languages with 90% cost savings.</p>
              <span class="nav__menu__card__more">Learn More</span>
            </a>

            <a class="nav__menu__card" href="/solutions/lead-generation" role="menuitem">
              <div class="nav__menu__card__head">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                <h3>AI Sales System</h3>
              </div>
              <p class="nav__menu__card__tag">Build a Machine That Prints Money</p>
              <p class="nav__menu__card__body">Complete lead generation engine that finds prospects, manages outreach, books meetings, and fills your pipeline automatically.</p>
              <span class="nav__menu__card__more">Learn More</span>
            </a>

            <a class="nav__menu__card" href="/solutions/process-optimization" role="menuitem">
              <div class="nav__menu__card__head">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                <h3>Process Automation</h3>
              </div>
              <p class="nav__menu__card__tag">Eliminate Bottlenecks Forever</p>
              <p class="nav__menu__card__body">Intelligent automation that identifies and removes operational constraints without human intervention.</p>
              <span class="nav__menu__card__more">Learn More</span>
            </a>

            <a class="nav__menu__card" href="/solutions/workforce-transformation" role="menuitem">
              <div class="nav__menu__card__head">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <h3>Workforce Intelligence</h3>
              </div>
              <p class="nav__menu__card__tag">Build Self-Improving Teams</p>
              <p class="nav__menu__card__body">Automatically capture knowledge, generate training, and optimize workforce performance without adding overhead.</p>
              <span class="nav__menu__card__more">Learn More</span>
            </a>

          </div>
          <div class="nav__menu__footer">
            <a href="/solutions">View All Solutions</a>
          </div>
        </div>
      </div>

      <a href="/solutions/operational-diagnostics" class="<?= $is('diagnose') ?>">Diagnose</a>
      <a href="/why-syncsity" class="<?= $is('why') ?>">Why Syncsity</a>
      <a href="/pricing" class="<?= $is('pricing') ?>">Pricing</a>

      <div class="nav__has-menu">
        <a href="/blog" class="has-chevron<?= $is('resources') ?>">Resources</a>
        <div class="nav__menu nav__menu--resources" role="menu" aria-label="Resources">
          <div class="nav__menu__grid">

            <a class="nav__menu__card" href="/calculators" role="menuitem">
              <div class="nav__menu__card__head">
                <h3>ROI Calculator</h3>
              </div>
              <p class="nav__menu__card__tag">Calculate your potential savings</p>
              <p class="nav__menu__card__body">Interactive tool showing exactly how much you could save with our AI solutions. Takes 2 minutes.</p>
              <span class="nav__menu__card__more">Calculate Your ROI</span>
            </a>

            <a class="nav__menu__card" href="/blog" role="menuitem">
              <div class="nav__menu__card__head">
                <h3>Blog &amp; Insights</h3>
              </div>
              <p class="nav__menu__card__tag">Latest thinking on AI &amp; growth</p>
              <p class="nav__menu__card__body">Weekly insights on constraints, automation, market domination, and breakthrough growth.</p>
              <span class="nav__menu__card__more">Read Latest Insights</span>
            </a>

          </div>
        </div>
      </div>

    </nav>

    <div class="nav__cta">
      <a href="/auth/login" class="btn btn--ghost btn--sm" id="auth-btn">Log in</a>
      <a href="/assess" class="btn btn--primary btn--sm">Free assessment &rarr;</a>
      <a href="/booking.html" class="btn btn--orange btn--sm">Book a Session</a>
    </div>

  </div>
</header>
