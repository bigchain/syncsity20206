<?php
/**
 * Email: magic link
 * Vars: name, magic_url, expires_minutes
 */
require_once __DIR__ . '/layout.php';

$name            = $name ?? 'there';
$magic_url       = $magic_url ?? 'https://syncsity.com';
$expires_minutes = (int)($expires_minutes ?? 15);

$subject = 'Your Syncsity login link';

email_layout_open('Your secure login link — valid for ' . $expires_minutes . ' minutes.');
?>

<tr>
  <td style="padding:36px 36px 8px;" class="pd">
    <p style="margin:0 0 16px;font-family:Inter,Arial,sans-serif;font-size:17px;color:#0a1022;font-weight:600;">Hi <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>,</p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      Here's your one-tap login to Syncsity — no password needed. Click the button below
      to access your Aha! Assessment and (when ready) your Revenue Intelligence Report.
    </p>
  </td>
</tr>

<tr>
  <td align="center" style="padding:8px 36px 32px;" class="pd">
    <a href="<?= htmlspecialchars($magic_url, ENT_QUOTES, 'UTF-8') ?>"
       style="display:inline-block;padding:16px 36px;background:linear-gradient(135deg,#3385DF 0%,#0066D7 100%);color:#ffffff;font-family:Inter,Arial,sans-serif;font-weight:600;font-size:15px;text-decoration:none;border-radius:10px;box-shadow:0 6px 18px rgba(51,133,223,0.30);">
      Log in to Syncsity →
    </a>
    <p style="margin:14px 0 0;font-family:Inter,Arial,sans-serif;font-size:12px;color:#9aa5bd;">
      This link expires in <?= $expires_minutes ?> minutes. Single-use.<br>
      If you didn't request it, you can safely ignore this email.
    </p>
  </td>
</tr>

<tr>
  <td style="padding:18px 36px 32px;background-color:#f8f9fc;border-top:1px solid #ececf2;" class="pd">
    <p style="margin:0 0 6px;font-family:Inter,Arial,sans-serif;font-size:12px;color:#7e8aa3;font-weight:600;">Can't click the button?</p>
    <p style="margin:0;font-family:'JetBrains Mono',monospace;font-size:11px;color:#a0acc4;word-break:break-all;line-height:1.6;">
      <?= htmlspecialchars($magic_url, ENT_QUOTES, 'UTF-8') ?>
    </p>
  </td>
</tr>

<?php email_layout_close(); ?>
