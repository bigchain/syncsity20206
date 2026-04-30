<?php
/**
 * Top navigation. Set $activeNav to a slug (home|why-us|services|pricing|about|contact)
 * to highlight the current section.
 */
if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/functions.php';

$activeNav ??= '';
$loggedIn = is_logged_in();
?>
<header class="site-header">
  <div class="container">
    <nav class="nav" aria-label="Primary">
      <a href="/" class="nav__brand" aria-label="Syncsity home">
        <img src="/assets/img/logo.png" alt="Syncsity" class="nav__brand-img" width="160" height="40">
      </a>

      <ul class="nav__list">
        <li><a href="/services"  class="<?= $activeNav === 'services' ? 'is-active' : '' ?>">Transform</a></li>
        <li><a href="/services"  class="<?= $activeNav === 'services' ? 'is-active' : '' ?>">Solutions</a></li>
        <li><a href="/assess"    class="<?= $activeNav === 'diagnose' ? 'is-active' : '' ?>">Diagnose</a></li>
        <li><a href="/why-us"    class="<?= $activeNav === 'why-us'   ? 'is-active' : '' ?>">Why Syncsity</a></li>
        <li><a href="/pricing"   class="<?= $activeNav === 'pricing'  ? 'is-active' : '' ?>">Pricing</a></li>
        <li><a href="/contact"   class="<?= $activeNav === 'contact'  ? 'is-active' : '' ?>">Contact</a></li>
      </ul>

      <div class="nav__cta">
        <button type="button" class="theme-toggle" aria-label="Toggle theme" data-theme-toggle>
          <svg class="ti-light" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
          <svg class="ti-dark"  width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
        </button>

        <?php if ($loggedIn): ?>
          <a href="/dashboard" class="btn btn--ghost btn--sm">Dashboard</a>
        <?php else: ?>
          <a href="/auth/login" class="btn btn--ghost btn--sm">Login</a>
        <?php endif; ?>
        <a href="/assess" class="btn btn--primary btn--sm">Free assessment <span class="arrow">→</span></a>

        <button type="button" class="nav__toggle" aria-label="Open menu" aria-expanded="false" data-nav-toggle>
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>
    </nav>
  </div>

  <div class="mobile-nav" data-mobile-nav>
    <a href="/why-us">Why us</a>
    <a href="/services">Services</a>
    <a href="/pricing">Pricing</a>
    <a href="/about">About</a>
    <a href="/contact">Contact</a>
    <?php if ($loggedIn): ?>
      <a href="/dashboard">Dashboard</a>
      <a href="/api/logout">Log out</a>
    <?php else: ?>
      <a href="/auth/login">Login</a>
    <?php endif; ?>
    <a href="/assess" class="btn btn--primary">Free assessment <span class="arrow">→</span></a>
  </div>
</header>
