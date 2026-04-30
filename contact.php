<?php
declare(strict_types=1);
define('SYNC_ROOT', __DIR__);
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/csrf.php';

$pageTitle = 'Contact Syncsity — talk to a senior operator';
$pageDesc  = 'Press, partnerships, considered engagements, or general questions. We read every message.';
$activeNav = 'contact';

$prefillSubject = clean($_GET['subject'] ?? '');
$flash = '';
if (isset($_GET['sent']))   $flash = 'Message received. We\'ll be in touch within one business day.';
if (isset($_GET['error']))  $flash = clean($_GET['error']);

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<section class="section section--hero">
  <div class="bg-glow"></div>
  <div class="container container--md" style="position:relative;">
    <span class="eyebrow">Contact</span>
    <h1 style="margin: var(--s-4) 0 var(--s-6);">Ask anything. We read every message.</h1>
    <p class="lead">
      For most enquiries, the <a href="/assess">free Revenue Intelligence Assessment</a> is the
      faster path — you'll have a useful answer before we'd reply. For everything else, this form goes
      straight to <strong>edward@syncsity.com</strong>.
    </p>
  </div>
</section>

<section class="section section--tight">
  <div class="container container--sm">
    <?php if ($flash): ?>
      <div class="alert <?= isset($_GET['sent']) ? 'alert--success' : 'alert--error' ?>" style="margin-bottom: var(--s-6);">
        <?= e($flash) ?>
      </div>
    <?php endif; ?>

    <form action="/api/contact" method="POST" class="card" style="display:flex; flex-direction:column; gap: var(--s-4);">
      <?= csrf_field() ?>
      <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px" aria-hidden="true">

      <div class="grid-2">
        <div class="field">
          <label class="field__label" for="name">Your name</label>
          <input type="text" id="name" name="name" class="input" required autocomplete="name" maxlength="120">
        </div>
        <div class="field">
          <label class="field__label" for="email">Work email</label>
          <input type="email" id="email" name="email" class="input" required autocomplete="email" maxlength="254">
        </div>
      </div>

      <div class="field">
        <label class="field__label" for="company">Company</label>
        <input type="text" id="company" name="company" class="input" autocomplete="organization" maxlength="200">
      </div>

      <div class="field">
        <label class="field__label" for="subject">Subject</label>
        <select id="subject" name="subject" class="select">
          <?php foreach (['Engagement','Strategy session','Partnership','Press','General'] as $s): ?>
            <option value="<?= e($s) ?>"<?= $prefillSubject === $s ? ' selected' : '' ?>><?= e($s) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="field">
        <label class="field__label" for="message">Your message</label>
        <textarea id="message" name="message" class="textarea" rows="6" required maxlength="5000" placeholder="Tell us what you need. The more specific, the faster we can be useful."></textarea>
      </div>

      <label style="display:flex; gap: var(--s-3); align-items: flex-start; font-size: 0.85rem; color: var(--text-muted);">
        <input type="checkbox" name="gdpr_consent" required style="margin-top: 4px;">
        <span>I'm OK with Syncsity processing my message to reply to me. I can <a href="/privacy">read the privacy policy</a> any time.</span>
      </label>

      <div style="display:flex; gap: var(--s-3); align-items:center;">
        <button type="submit" class="btn btn--primary">Send message <span class="arrow">→</span></button>
        <span class="dim mono" style="font-size:0.82rem;">→ edward@syncsity.com</span>
      </div>
    </form>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: var(--s-6); margin-top: var(--s-12);">
      <div>
        <h4 style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--text-dim); margin-bottom: var(--s-3);">Email</h4>
        <a href="mailto:edward@syncsity.com" style="color: var(--text);">edward@syncsity.com</a>
      </div>
      <div>
        <h4 style="font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.14em; color: var(--text-dim); margin-bottom: var(--s-3);">Office</h4>
        <p class="muted" style="font-size:0.92rem;">London, United Kingdom<br>By appointment only.</p>
      </div>
    </div>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
