<?php
/**
 * Email: Revenue Intelligence Report ready
 * Vars: name, leak_amount, root_cause_name, report_url
 */
require_once __DIR__ . '/layout.php';

$name            = $name ?? 'there';
$leak_amount     = $leak_amount ?? '£10,000';
$root_cause_name = $root_cause_name ?? 'The Hidden Constraint';
$report_url      = $report_url ?? 'https://syncsity.com/dashboard';

$subject = 'Your Revenue Intelligence Report — ' . $leak_amount . '/month leak found';

email_layout_open('We found ' . $leak_amount . ' a month leaking from your business. Full report inside.');
?>

<tr>
  <td style="padding:36px 36px 0;" class="pd">
    <p style="margin:0 0 8px;font-family:'JetBrains Mono',monospace;font-size:11px;color:#FCA311;letter-spacing:0.18em;text-transform:uppercase;">Your report is ready</p>
    <h1 class="h1" style="margin:0 0 16px;font-family:Inter,Arial,sans-serif;font-size:30px;color:#0a1022;font-weight:800;letter-spacing:-0.02em;line-height:1.15;">
      Hi <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?> — we found the leak.
    </h1>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 18px;" class="pd">
    <p style="margin:0 0 12px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      The Aha! diagnostic surfaced one specific constraint costing your business roughly
      <strong style="color:#0a1022;"><?= htmlspecialchars($leak_amount, ENT_QUOTES, 'UTF-8') ?> a month</strong>.
      We named it. We quantified it. And we wrote a 3-step intervention plan you can use whether or not you ever speak to us.
    </p>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 24px;" class="pd">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:linear-gradient(135deg,rgba(252,163,17,0.10) 0%,rgba(51,133,223,0.06) 100%);border:1px solid rgba(252,163,17,0.30);border-radius:12px;">
      <tr>
        <td style="padding:22px 22px;">
          <p style="margin:0 0 4px;font-family:'JetBrains Mono',monospace;font-size:10px;color:#FCA311;letter-spacing:0.18em;text-transform:uppercase;">Your root cause</p>
          <p style="margin:0;font-family:Inter,Arial,sans-serif;font-size:22px;color:#0a1022;font-weight:700;letter-spacing:-0.01em;">
            <?= htmlspecialchars($root_cause_name, ENT_QUOTES, 'UTF-8') ?>
          </p>
        </td>
      </tr>
    </table>
  </td>
</tr>

<tr>
  <td align="center" style="padding:0 36px 32px;" class="pd">
    <a href="<?= htmlspecialchars($report_url, ENT_QUOTES, 'UTF-8') ?>"
       style="display:inline-block;padding:16px 36px;background:linear-gradient(135deg,#3385DF 0%,#0066D7 100%);color:#ffffff;font-family:Inter,Arial,sans-serif;font-weight:600;font-size:15px;text-decoration:none;border-radius:10px;box-shadow:0 6px 18px rgba(51,133,223,0.30);">
      Read my full report →
    </a>
    <p style="margin:14px 0 0;font-family:Inter,Arial,sans-serif;font-size:12px;color:#9aa5bd;">
      You're already logged in via this link.
    </p>
  </td>
</tr>

<tr>
  <td style="padding:18px 36px 32px;background-color:#f8f9fc;border-top:1px solid #ececf2;" class="pd">
    <p style="margin:0 0 8px;font-family:Inter,Arial,sans-serif;font-size:13px;color:#3a455c;font-weight:600;">What happens next is up to you.</p>
    <p style="margin:0 0 0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;line-height:1.7;">
      Read the report. Argue with it. Show it to your team. If you want to pressure-test the diagnosis with a senior operator before doing anything, you'll see the £950 Strategy Session option at the end of the report.<br><br>
      If we never hear from you again — that's fine too. The clarity is the gift.
    </p>
  </td>
</tr>

<?php email_layout_close(); ?>
