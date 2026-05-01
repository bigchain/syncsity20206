<?php
/**
 * Universal site footer for marketing pages.
 *
 * Optional before include:
 *   $page_path_prefix (string) — '/' for site-root, '../' if nested
 *
 * Renders 4-column footer + bottom row, then the closing </body></html>.
 * Calling page should NOT close </body> or </html> itself.
 */
$page_path_prefix = $page_path_prefix ?? '/';
$h = $h ?? fn($s) => htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
?>
<footer class="footer-home" role="contentinfo" aria-label="Site footer">
  <div class="footer-home__inner">
    <div class="footer-home__cols">
      <div class="footer-home__brand">
        <img src="<?= $h($page_path_prefix) ?>lovable-uploads/03f35f21-123f-4f10-84a1-f2a66d97bc2b.png"
             alt="Syncsity logo" width="200" height="32"
             loading="lazy" decoding="async"
             style="height:32px;width:auto;">
        <p>AI business transformation for ambitious companies. We find the constraints, remove the waste, and help you achieve breakthrough growth.</p>
      </div>
      <div>
        <h4>Quick Links</h4>
        <ul>
          <li><a href="/why-syncsity">Why Syncsity</a></li>
          <li><a href="/pricing">Pricing</a></li>
          <li><a href="/blog">Resources</a></li>
          <li><a href="/contact.html">Contact</a></li>
        </ul>
      </div>
      <div>
        <h4>Our Solutions</h4>
        <ul>
          <li><a href="/solutions/voice-solutions">AI Voice Operations</a></li>
          <li><a href="/solutions/lead-generation">AI Sales System</a></li>
          <li><a href="/solutions/workforce-transformation">Workforce Intelligence</a></li>
          <li><a href="/solutions/process-optimization">Process Automation</a></li>
        </ul>
      </div>
      <div>
        <h4>Services</h4>
        <ul>
          <li><a href="/assess">Free Assessment</a></li>
          <li><a href="/booking.html">Book a Session</a></li>
          <li><a href="/transform">Transformation</a></li>
          <li><a href="/calculators">Calculators</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-home__bottom">
      <span>&copy; <span id="year"><?= date('Y') ?></span> Syncsity. All rights reserved.</span>
      <span><a href="/privacy.html">Privacy</a> &middot; <a href="/terms.html">Terms</a> &middot; <a href="/sitemap.html">Sitemap</a></span>
    </div>
  </div>
</footer>

<script>
// Auth-aware nav: swap "Log in" → "Dashboard" if a session cookie exists.
if (/(?:^|;\s*)(?:syncsity_session|PHPSESSID)=/.test(document.cookie || '')) {
  var b = document.getElementById('auth-btn');
  if (b) { b.href = '/dashboard'; b.textContent = 'Dashboard'; }
}
</script>
<script src="<?= $h($page_path_prefix) ?>assets/js/nav-mobile.js" defer></script>

</body>
</html>
