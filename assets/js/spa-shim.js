/* ──────────────────────────────────────────────────────────────────────────
   Syncsity — SPA shim
   Injects auth-aware "Dashboard / Log in" + "Free assessment" buttons into
   the Lovable React navbar after it mounts. Bridges the original SPA design
   to the new PHP product surface (/auth/login, /dashboard, /assess).
   ────────────────────────────────────────────────────────────────────────── */
(function () {
  'use strict';

  var injected = false;

  function isLoggedIn() {
    return /(?:^|;\s*)(?:syncsity_session|PHPSESSID)=/.test(document.cookie || '');
  }

  function makeBtn(label, href, primary) {
    var a = document.createElement('a');
    a.href = href;
    a.className = 'sync-btn ' + (primary ? 'sync-btn--primary' : 'sync-btn--ghost');
    a.textContent = label;
    return a;
  }

  function injectStyles() {
    if (document.getElementById('sync-shim-css')) return;
    var s = document.createElement('style');
    s.id = 'sync-shim-css';
    s.textContent =
      '.sync-actions{display:inline-flex;align-items:center;gap:10px;margin-right:12px}' +
      '.sync-btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 18px;border-radius:9999px;font-weight:600;font-size:14px;line-height:1;text-decoration:none;transition:transform 150ms ease, background-color 150ms ease, color 150ms ease, border-color 150ms ease}' +
      '.sync-btn:hover{transform:translateY(-1px)}' +
      '.sync-btn--ghost{background:transparent;color:#fff;border:1px solid rgba(255,255,255,0.30)}' +
      '.sync-btn--ghost:hover{border-color:#fff;background:rgba(255,255,255,0.06)}' +
      '.sync-btn--primary{background:linear-gradient(135deg,#3385DF 0%,#0066D7 100%);color:#fff;box-shadow:0 6px 18px rgba(51,133,223,0.30)}' +
      '.sync-btn--primary:hover{box-shadow:0 10px 26px rgba(51,133,223,0.45)}' +
      '/* When the navbar has scrolled (white bg) the ghost button needs darker text */' +
      '.bg-white .sync-btn--ghost,nav.shadow-md .sync-btn--ghost{color:#14213D;border-color:rgba(20,33,61,0.20)}' +
      '.bg-white .sync-btn--ghost:hover,nav.shadow-md .sync-btn--ghost:hover{border-color:#14213D;background:rgba(20,33,61,0.04)}' +
      '@media (max-width:880px){.sync-actions{display:none}}';
    document.head.appendChild(s);
  }

  function inject() {
    if (injected) return true;
    var nav = document.querySelector('nav');
    if (!nav) return false;

    // The original navbar puts "Book a Session" inside a flex row on the right.
    // Find the deepest flex container that holds the CTA.
    var bookBtn = Array.prototype.find.call(
      nav.querySelectorAll('a, button'),
      function (el) { return /book a session/i.test((el.textContent || '').trim()); }
    );
    if (!bookBtn) return false;

    var host = bookBtn.parentElement;
    if (!host) return false;

    var actions = document.createElement('div');
    actions.className = 'sync-actions';

    if (isLoggedIn()) {
      actions.appendChild(makeBtn('Dashboard', '/dashboard', false));
    } else {
      actions.appendChild(makeBtn('Log in', '/auth/login', false));
    }
    actions.appendChild(makeBtn('Free assessment →', '/assess', true));

    host.insertBefore(actions, bookBtn);
    injected = true;
    return true;
  }

  injectStyles();

  // Try immediately, then watch for SPA hydration.
  if (inject()) return;

  var tries = 0;
  var poll = setInterval(function () {
    tries++;
    if (inject() || tries > 50) clearInterval(poll);  // up to ~10s
  }, 200);

  // Also re-attempt on route change (the React SPA may rerender the nav)
  var lastPath = location.pathname;
  setInterval(function () {
    if (location.pathname !== lastPath) {
      lastPath = location.pathname;
      injected = false;
      inject();
    }
  }, 600);
})();
