<?php
/**
 * Email — Day 14: specific next move + soft Strategy Session intro
 * Vars: name, root_cause_name, leak_amount, biz_type, priority, calendly_url, report_url, unsubscribe_url
 */
require_once __DIR__ . '/layout.php';

$name            = $name ?? 'there';
$root_cause_name = $root_cause_name ?? 'your constraint';
$leak_amount     = $leak_amount ?? '£10,000';
$biz_type        = $biz_type ?? 'service business';
$priority        = $priority ?? 'lift conversion';
$calendly_url    = $calendly_url ?? 'https://calendly.com/syncsity/strategy';
$report_url      = $report_url ?? 'https://syncsity.com/dashboard';
$unsubscribe_url = $unsubscribe_url ?? '#';

$subject = 'The single highest-leverage move for your situation';

email_layout_open('Two weeks in. Here\'s the move I\'d make if I were running your shop.');
?>

<tr>
  <td style="padding:36px 36px 8px;" class="pd">
    <p style="margin:0 0 16px;font-family:Inter,Arial,sans-serif;font-size:17px;color:#0a1022;font-weight:600;"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>,</p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      Two weeks since your report landed. If the diagnosis hasn't been argued
      down by now, it's probably right.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      So — practically — what's the single highest-leverage move for your shop right now?
      Based on your answers (priority: <em><?= htmlspecialchars($priority, ENT_QUOTES, 'UTF-8') ?></em>; archetype: <em><?= htmlspecialchars($root_cause_name, ENT_QUOTES, 'UTF-8') ?></em>):
    </p>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 24px;" class="pd">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:linear-gradient(135deg,rgba(252,163,17,0.10),rgba(51,133,223,0.06));border:1px solid rgba(252,163,17,0.30);border-radius:12px;">
      <tr>
        <td style="padding:24px 28px;">
          <p style="margin:0 0 8px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:#FCA311;">The move</p>
          <p style="margin:0 0 12px;font-family:Inter,Arial,sans-serif;font-size:18px;color:#0a1022;font-weight:700;letter-spacing:-0.01em;">
            Get a senior operator to look at your real numbers for 30 minutes.
          </p>
          <p style="margin:0;font-family:Inter,Arial,sans-serif;font-size:14px;color:#3a455c;line-height:1.65;">
            Not a "let's chat" call. Not a sales pitch. We open the report next to your
            actual P&amp;L / pipeline / inbound logs and pressure-test where the
            <?= htmlspecialchars($leak_amount, ENT_QUOTES, 'UTF-8') ?> is leaking. By the end you have a
            written 90-day plan and a number we'd both bet on.
          </p>
        </td>
      </tr>
    </table>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 18px;" class="pd">
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      The Strategy Session is <strong>£950, 30 minutes</strong>. The fee is credited against
      any subsequent engagement, so if you decide to work with us, it's effectively free.
      If you don't, you've got a written plan you didn't have before.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      I'm direct about it: ~30% of the people I run this with end up engaging us.
      The other ~70% take the plan and execute themselves, and that's the right outcome too.
    </p>
  </td>
</tr>

<tr>
  <td align="center" style="padding:8px 36px 28px;" class="pd">
    <a href="<?= htmlspecialchars($calendly_url, ENT_QUOTES, 'UTF-8') ?>"
       style="display:inline-block;padding:16px 36px;background-color:#FCA311;color:#0a1022;font-family:Inter,Arial,sans-serif;font-weight:700;font-size:15px;text-decoration:none;border-radius:9999px;box-shadow:0 8px 24px rgba(252,163,17,0.30);">
      Book the Strategy Session →
    </a>
    <p style="margin:14px 0 0;font-family:Inter,Arial,sans-serif;font-size:12px;color:#9aa5bd;">
      Or just reply to this email with a question. No call required.
    </p>
  </td>
</tr>

<tr>
  <td style="padding:18px 36px 28px;background-color:#f8f9fc;border-top:1px solid #ececf2;" class="pd">
    <p style="margin:0 0 8px;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;line-height:1.7;">
      Need to re-read the report? <a href="<?= htmlspecialchars($report_url, ENT_QUOTES, 'UTF-8') ?>" style="color:#3385DF;">It's here</a>.
    </p>
    <p style="margin:8px 0 0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;">
      — Edward<br>
      <span style="color:#a0acc4;"><a href="<?= htmlspecialchars($unsubscribe_url, ENT_QUOTES, 'UTF-8') ?>" style="color:#a0acc4;">Unsubscribe</a></span>
    </p>
  </td>
</tr>

<?php email_layout_close(); ?>
