/* ──────────────────────────────────────────────────────────────────────────
   Syncsity — Static-page shell injector
   Loaded by every .html static page (contact, demo, booking, terms, privacy,
   sitemap). Replaces the page's <header data-shell="nav"> and
   <footer data-shell="footer"> stubs with the same nav + footer the SPA shows.

   Why a JS injector and not server-side includes? Static .html pages can't
   use PHP. JS injection keeps the markup in ONE place — update once, all
   pages get it.

   SEO note: Google renders JS, so the injected nav/footer counts as content.
   For belt-and-braces, every static page also has its own <noscript> minimal
   footer link list so the page is never orphan even with JS off.
   ────────────────────────────────────────────────────────────────────────── */
(function () {
  'use strict';

  var loggedIn = /(?:^|;\s*)(?:syncsity_session|PHPSESSID)=/.test(document.cookie || '');
  var year = new Date().getFullYear();

  var navHTML =
    '<header class="nav">' +
      '<div class="nav__inner">' +
        '<a href="/" class="nav__brand" aria-label="Syncsity home">' +
          '<img src="/lovable-uploads/03f35f21-123f-4f10-84a1-f2a66d97bc2b.png" alt="Syncsity" style="height:36px;width:auto;">' +
        '</a>' +
        '<nav class="nav__links" aria-label="Primary">' +
          '<a href="/transform">Transform</a>' +
          '<a href="/solutions">Solutions</a>' +
          '<a href="/assess">Diagnose</a>' +
          '<a href="/why-syncsity">Why Syncsity</a>' +
          '<a href="/pricing">Pricing</a>' +
          '<a href="/blog">Resources</a>' +
        '</nav>' +
        '<div class="nav__cta">' +
          (loggedIn
            ? '<a href="/dashboard" class="btn btn--ghost btn--sm">Dashboard</a>'
            : '<a href="/auth/login" class="btn btn--ghost btn--sm">Log in</a>'
          ) +
          '<a href="/assess" class="btn btn--primary btn--sm">Free assessment</a>' +
          '<a href="/booking.html" class="btn btn--orange btn--sm" style="margin-left:4px;">Book a Session</a>' +
        '</div>' +
      '</div>' +
    '</header>';

  var footerHTML =
    '<footer class="footer">' +
      '<div class="container">' +
        '<div class="footer__top">' +
          '<div>' +
            '<img src="/lovable-uploads/03f35f21-123f-4f10-84a1-f2a66d97bc2b.png" alt="Syncsity" style="height:42px;margin-bottom:16px;">' +
            '<p class="muted" style="font-size:14px;max-width:340px;line-height:1.65;">Empowering ambitious operators through operational excellence and strategic AI implementation.</p>' +
            '<div style="display:flex;gap:10px;margin-top:18px;">' +
              '<a href="https://twitter.com/syncsity" target="_blank" rel="noopener" aria-label="Twitter" style="display:grid;place-items:center;width:36px;height:36px;border:1px solid var(--border);border-radius:50%;color:var(--text-muted);transition:all 150ms ease;">' +
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>' +
              '</a>' +
              '<a href="https://www.linkedin.com/company/syncsity" target="_blank" rel="noopener" aria-label="LinkedIn" style="display:grid;place-items:center;width:36px;height:36px;border:1px solid var(--border);border-radius:50%;color:var(--text-muted);transition:all 150ms ease;">' +
                '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>' +
              '</a>' +
            '</div>' +
          '</div>' +
          '<div>' +
            '<h4>Quick Links</h4>' +
            '<ul>' +
              '<li><a href="/">Home</a></li>' +
              '<li><a href="/why-syncsity">Why Syncsity</a></li>' +
              '<li><a href="/pricing">Pricing</a></li>' +
              '<li><a href="/about-us">About Us</a></li>' +
              '<li><a href="/contact.html">Contact</a></li>' +
              '<li><a href="/calculators">Calculators</a></li>' +
              '<li><a href="/blog">Blog</a></li>' +
            '</ul>' +
          '</div>' +
          '<div>' +
            '<h4>Our Solutions</h4>' +
            '<ul>' +
              '<li><a href="/solutions/voice-solutions">AI Voice Operations</a></li>' +
              '<li><a href="/solutions/lead-generation">AI Sales System</a></li>' +
              '<li><a href="/solutions/process-optimization">Process Automation</a></li>' +
              '<li><a href="/solutions/workforce-transformation">Workforce Intelligence</a></li>' +
            '</ul>' +
          '</div>' +
          '<div>' +
            '<h4>Services</h4>' +
            '<ul>' +
              '<li><a href="/transform/market-domination">Market Domination</a></li>' +
              '<li><a href="/transform/revenue-acceleration">Revenue Acceleration</a></li>' +
              '<li><a href="/transform/operational-supremacy">Operational Supremacy</a></li>' +
              '<li><a href="/assess">Free Aha! Assessment</a></li>' +
              '<li><a href="/booking.html">Strategy Session</a></li>' +
            '</ul>' +
          '</div>' +
        '</div>' +
        '<div class="footer__bottom">' +
          '<span>© ' + year + ' Syncsity Ltd · London, UK · ' +
            '<a href="/privacy.html">Privacy</a> · ' +
            '<a href="/terms.html">Terms</a> · ' +
            '<a href="/sitemap.html">Sitemap</a> · ' +
            '<a href="/llms.txt">LLMs.txt</a>' +
          '</span>' +
          '<span class="mono dim">Built for operators, by operators.</span>' +
        '</div>' +
      '</div>' +
    '</footer>';

  function inject() {
    var navHost    = document.querySelector('[data-shell="nav"]');
    var footerHost = document.querySelector('[data-shell="footer"]');
    if (navHost)    navHost.outerHTML    = navHTML;
    if (footerHost) footerHost.outerHTML = footerHTML;
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inject);
  } else {
    inject();
  }
})();
