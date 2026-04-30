/* ──────────────────────────────────────────────────────────────────────────
   Syncsity — Core JS
   Theme toggle, mobile nav, header scroll behaviour, AHA hero email handoff
   ────────────────────────────────────────────────────────────────────────── */

(function () {
  'use strict';

  // ── Theme toggle (light / dark) ────────────────────────────────────────
  const root = document.documentElement;
  const stored = localStorage.getItem('syncsity-theme');
  if (stored === 'light') root.setAttribute('data-theme', 'light');

  document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-theme-toggle]');
    if (!btn) return;
    if (root.getAttribute('data-theme') === 'light') {
      root.removeAttribute('data-theme');
      localStorage.setItem('syncsity-theme', 'dark');
    } else {
      root.setAttribute('data-theme', 'light');
      localStorage.setItem('syncsity-theme', 'light');
    }
  });

  // ── Mobile nav toggle ──────────────────────────────────────────────────
  const navToggle = document.querySelector('[data-nav-toggle]');
  const mobileNav = document.querySelector('[data-mobile-nav]');
  if (navToggle && mobileNav) {
    navToggle.addEventListener('click', function () {
      const open = mobileNav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', String(open));
      document.body.style.overflow = open ? 'hidden' : '';
    });
    mobileNav.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () {
        mobileNav.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      });
    });
  }

  // ── AHA hero email capture → /assess?email=... ─────────────────────────
  document.querySelectorAll('[data-aha-form]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const input = form.querySelector('input[type=email]');
      const email = (input && input.value || '').trim();
      if (!email) return;
      // Save for the form to pre-populate, then redirect.
      try { sessionStorage.setItem('syncsity-prefill-email', email); } catch (_) {}
      const url = '/assess?email=' + encodeURIComponent(email);
      window.location.href = url;
    });
  });

  // ── Header scroll shadow ───────────────────────────────────────────────
  const header = document.querySelector('.site-header');
  if (header) {
    const onScroll = function () {
      header.style.boxShadow = window.scrollY > 4 ? '0 6px 20px rgba(0,0,0,0.18)' : 'none';
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ── Reveal-on-scroll (subtle) ──────────────────────────────────────────
  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (e.isIntersecting) {
          e.target.classList.add('is-revealed');
          io.unobserve(e.target);
        }
      });
    }, { rootMargin: '0px 0px -10% 0px', threshold: 0.05 });
    document.querySelectorAll('[data-reveal]').forEach(function (el) { io.observe(el); });
  }

  // ── Year stamp ─────────────────────────────────────────────────────────
  document.querySelectorAll('[data-year]').forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });
})();
