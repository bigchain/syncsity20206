<?php
/**
 * Email — Day 30: final friendly close
 * Vars: name, root_cause_name, calendly_url, report_url, unsubscribe_url
 */
require_once __DIR__ . '/layout.php';

$name            = $name ?? 'there';
$root_cause_name = $root_cause_name ?? 'your constraint';
$calendly_url    = $calendly_url ?? 'https://calendly.com/syncsity/strategy';
$report_url      = $report_url ?? 'https://syncsity.com/dashboard';
$unsubscribe_url = $unsubscribe_url ?? '#';

$subject = 'Last note — and then I\'ll stop';

email_layout_open('30 days on. One last note before I stop emailing.');
?>

<tr>
  <td style="padding:36px 36px 8px;" class="pd">
    <p style="margin:0 0 16px;font-family:Inter,Arial,sans-serif;font-size:17px;color:#0a1022;font-weight:600;"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>,</p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      30 days since your report. This is the last note from me — I don't believe in
      drip-forever sequences when someone hasn't replied. Either the diagnosis was
      useful, or it wasn't, and either way you don't need me badgering you.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      Two things, briefly:
    </p>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 24px;" class="pd">
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      <strong>1. The report is permanent.</strong> Your <em><?= htmlspecialchars($root_cause_name, ENT_QUOTES, 'UTF-8') ?></em>
      diagnosis lives in your dashboard for as long as you want it. Come back any time.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      <strong>2. The Strategy Session offer doesn't expire.</strong> If 6 months from now you've
      tried something and it didn't move the number, my inbox is open. Don't pretend
      it's a fresh diagnosis — just hit reply to this thread and we'll pick up where
      we left off.
    </p>
  </td>
</tr>

<tr>
  <td align="center" style="padding:8px 36px 28px;" class="pd">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
      <tr>
        <td style="padding-right:8px;">
          <a href="<?= htmlspecialchars($report_url, ENT_QUOTES, 'UTF-8') ?>"
             style="display:inline-block;padding:14px 28px;background:rgba(0,0,0,0);color:#3385DF;border:1px solid #3385DF;font-family:Inter,Arial,sans-serif;font-weight:600;font-size:14px;text-decoration:none;border-radius:9999px;">
            Open my report
          </a>
        </td>
        <td style="padding-left:8px;">
          <a href="<?= htmlspecialchars($calendly_url, ENT_QUOTES, 'UTF-8') ?>"
             style="display:inline-block;padding:14px 28px;background:linear-gradient(135deg,#3385DF 0%,#0066D7 100%);color:#ffffff;font-family:Inter,Arial,sans-serif;font-weight:600;font-size:14px;text-decoration:none;border-radius:9999px;">
            Strategy Session →
          </a>
        </td>
      </tr>
    </table>
  </td>
</tr>

<tr>
  <td style="padding:18px 36px 28px;background-color:#f8f9fc;border-top:1px solid #ececf2;" class="pd">
    <p style="margin:0 0 8px;font-family:Inter,Arial,sans-serif;font-size:14px;color:#3a455c;line-height:1.7;">
      Wishing you the breakthrough either way.
    </p>
    <p style="margin:8px 0 0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;">
      — Edward<br>
      <span style="color:#a0acc4;">edward@syncsity.com · founder, Syncsity</span>
    </p>
    <p style="margin:14px 0 0;font-family:Inter,Arial,sans-serif;font-size:11px;color:#a0acc4;">
      <a href="<?= htmlspecialchars($unsubscribe_url, ENT_QUOTES, 'UTF-8') ?>" style="color:#a0acc4;">Unsubscribe</a> · This is the last automated email from Syncsity to you. Replies still go to a real human.
    </p>
  </td>
</tr>

<?php email_layout_close(); ?>
