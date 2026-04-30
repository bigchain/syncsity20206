<?php
/**
 * /auth/login        — magic-link request form
 * /auth/login?sent=1 — confirmation page
 * /auth/login?logout — end session, then show form
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/csrf.php';
require_once SYNC_ROOT . '/lib/functions.php';

if (isset($_GET['logout'])) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: /auth/login');
    exit;
}

// Already logged in → dashboard (or validated redirect)
if (is_logged_in()) {
    $dest = safe_redirect_path((string)($_GET['redirect'] ?? ''), '/dashboard');
    header('Location: ' . $dest);
    exit;
}

$sent     = isset($_GET['sent']);
$emailIn  = clean($_GET['email']    ?? '', 254);
$redirect = clean($_GET['redirect'] ?? '', 200);

$pageTitle = $sent ? 'Check your inbox — Syncsity' : 'Log in — Syncsity';
$pageDesc  = 'One-tap login. No passwords. We email you a secure link.';

include SYNC_ROOT . '/partials/head.php';
?>

<main class="auth-shell">
  <div class="auth-card">
    <div class="auth-brand">
      <svg width="28" height="28" viewBox="0 0 32 32" aria-hidden="true">
        <circle cx="16" cy="16" r="13" stroke="url(#lh1)" stroke-width="2.4" fill="none"/>
        <path d="M9 19c1.6 1.2 4 2 6.5 2 3 0 5.5-1.6 5.5-4 0-2-1.6-3.2-4.6-4l-2-.5c-3-.7-4.9-2.3-4.9-4.8C9.5 5.2 11.8 4 14.5 4c2.2 0 4.2.8 5.5 2" stroke="url(#lh2)" stroke-width="2.4" stroke-linecap="round" fill="none"/>
        <defs>
          <linearGradient id="lh1" x1="3" y1="3" x2="29" y2="29" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#3385DF"/><stop offset="1" stop-color="#FCA311"/></linearGradient>
          <linearGradient id="lh2" x1="9" y1="4" x2="21" y2="21" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#FCA311"/><stop offset="1" stop-color="#3385DF"/></linearGradient>
        </defs>
      </svg>
      <span>Syncsity</span>
    </div>

    <?php if ($sent): ?>
      <div style="text-align:center;">
        <div style="font-size: 32px; margin-bottom: var(--s-3);">📬</div>
        <h1>Check your inbox.</h1>
        <p class="lead">We just emailed a secure login link to <strong><?= e($emailIn ?: 'you') ?></strong>. It expires in 15 minutes. Single-use.</p>
        <p class="muted" style="margin-top: var(--s-4); font-size: 0.92rem;">Didn't arrive? Check spam, then <a href="/auth/login?email=<?= urlencode($emailIn) ?>">try again</a>.</p>
      </div>
    <?php else: ?>
      <h1>Log in to Syncsity</h1>
      <p class="lead">Enter your email — we'll send you a one-tap login link. No passwords, ever.</p>

      <form id="login-form" novalidate style="display:flex; flex-direction:column; gap: var(--s-4); margin-top: var(--s-6);">
        <?= csrf_field() ?>
        <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
        <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">

        <div class="field">
          <label class="field__label" for="email">Email</label>
          <input id="email" name="email" type="email" class="input" placeholder="you@yourcompany.com" required autocomplete="email" autofocus value="<?= e($emailIn) ?>">
        </div>

        <div class="field__error" id="login-error" style="display:none;"></div>

        <button type="submit" class="btn btn--primary btn--block" id="login-submit">Send my login link <span class="arrow">→</span></button>
      </form>

      <p class="dim" style="margin-top: var(--s-6); text-align: center; font-size: 0.85rem;">
        New here? Take the <a href="/assess">free Aha! Assessment</a> instead — login link comes with the report.
      </p>

      <script>
      (function(){
        const form = document.getElementById('login-form');
        const submit = document.getElementById('login-submit');
        const err = document.getElementById('login-error');
        form.addEventListener('submit', function(e){
          e.preventDefault();
          err.style.display = 'none';
          submit.setAttribute('aria-busy', 'true');
          submit.disabled = true;
          submit.innerHTML = 'Sending…';
          const data = new FormData(form);
          fetch('/api/magic-link', { method:'POST', body:data, credentials:'same-origin' })
            .then(function(r){ return r.json(); })
            .then(function(res){
              if (res && res.ok) {
                const email = data.get('email');
                window.location.href = '/auth/login?sent=1&email=' + encodeURIComponent(email);
              } else {
                err.textContent = (res && res.error) || 'Something went wrong. Please try again.';
                err.style.display = 'block';
                submit.removeAttribute('aria-busy');
                submit.disabled = false;
                submit.innerHTML = 'Send my login link <span class="arrow">→</span>';
              }
            })
            .catch(function(){
              err.textContent = 'Network error. Please try again.';
              err.style.display = 'block';
              submit.removeAttribute('aria-busy');
              submit.disabled = false;
              submit.innerHTML = 'Send my login link <span class="arrow">→</span>';
            });
        });
      })();
      </script>
    <?php endif; ?>
  </div>
</main>

<script src="/assets/js/core.js" defer></script>
</body>
</html>
