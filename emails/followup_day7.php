<?php
/**
 * Email — Day 7: pattern across operators in their archetype
 * Vars: name, root_cause_name, biz_type, leak_amount, report_url, unsubscribe_url
 */
require_once __DIR__ . '/layout.php';

$name            = $name ?? 'there';
$root_cause_name = $root_cause_name ?? 'your constraint';
$biz_type        = $biz_type ?? 'service business';
$leak_amount     = $leak_amount ?? '£10,000';
$report_url      = $report_url ?? 'https://syncsity.com/dashboard';
$unsubscribe_url = $unsubscribe_url ?? '#';

$subject = 'What other operators in your spot did next';

email_layout_open('Pattern from operators with the same archetype as yours.');
?>

<tr>
  <td style="padding:36px 36px 16px;" class="pd">
    <p style="margin:0 0 16px;font-family:Inter,Arial,sans-serif;font-size:17px;color:#0a1022;font-weight:600;"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>,</p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      Quick pattern observation. We've now run the diagnostic across enough operators
      that archetypes form clusters. Yours — <strong><?= htmlspecialchars($root_cause_name, ENT_QUOTES, 'UTF-8') ?></strong>
      at a <?= htmlspecialchars($biz_type, ENT_QUOTES, 'UTF-8') ?> — has a clear bifurcation in what people do next.
    </p>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 24px;" class="pd">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
      <tr>
        <td style="width:50%;padding-right:8px;vertical-align:top;">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#f8f9fc;border-radius:8px;border:1px solid #ececf2;">
            <tr>
              <td style="padding:18px 20px;">
                <p style="margin:0 0 6px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:0.16em;text-transform:uppercase;color:#FCA311;">Path A · ~70%</p>
                <h3 style="margin:0 0 8px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#0a1022;font-weight:700;">Hire</h3>
                <p style="margin:0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#3a455c;line-height:1.6;">Add headcount. 11 months later: same constraint, 30% more salary cost.</p>
              </td>
            </tr>
          </table>
        </td>
        <td style="width:50%;padding-left:8px;vertical-align:top;">
          <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:linear-gradient(135deg,rgba(51,133,223,0.08),rgba(51,133,223,0.02));border-radius:8px;border:1px solid #3385DF;">
            <tr>
              <td style="padding:18px 20px;">
                <p style="margin:0 0 6px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:0.16em;text-transform:uppercase;color:#3385DF;">Path B · ~30%</p>
                <h3 style="margin:0 0 8px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#0a1022;font-weight:700;">Map &amp; route</h3>
                <p style="margin:0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#3a455c;line-height:1.6;">Map every inbound, install routing, then automate. 3 months later: <?= htmlspecialchars($leak_amount, ENT_QUOTES, 'UTF-8') ?>+/mo back.</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 18px;" class="pd">
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      Path A feels like progress (new desk, new salary, busy hiring meeting). It mostly
      isn't — because the leak isn't capacity, it's <em>routing</em>. Path B feels like
      slow procedural work for the first 3 weeks, then the curve compounds hard.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      The reason ~70% pick Path A: it's <strong>visible</strong>. A new hire is something
      to point at in a board meeting. Process maps and routing rules aren't.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      The reason ~30% pick Path B and win: they ran the diagnostic, accepted the diagnosis,
      and resisted the urge to do the obvious thing. Your report is the first half of that.
    </p>
  </td>
</tr>

<tr>
  <td align="center" style="padding:8px 36px 32px;" class="pd">
    <a href="<?= htmlspecialchars($report_url, ENT_QUOTES, 'UTF-8') ?>"
       style="display:inline-block;padding:14px 32px;background:linear-gradient(135deg,#3385DF 0%,#0066D7 100%);color:#ffffff;font-family:Inter,Arial,sans-serif;font-weight:600;font-size:14px;text-decoration:none;border-radius:8px;">
      Open my report →
    </a>
  </td>
</tr>

<tr>
  <td style="padding:18px 36px 28px;background-color:#f8f9fc;border-top:1px solid #ececf2;" class="pd">
    <p style="margin:0 0 8px;font-family:Inter,Arial,sans-serif;font-size:13px;color:#3a455c;line-height:1.7;">
      If you want to talk through which path makes sense for your specific setup,
      reply to this email — no booking required.
    </p>
    <p style="margin:8px 0 0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;">
      — Edward<br>
      <span style="color:#a0acc4;"><a href="<?= htmlspecialchars($unsubscribe_url, ENT_QUOTES, 'UTF-8') ?>" style="color:#a0acc4;">Unsubscribe</a></span>
    </p>
  </td>
</tr>

<?php email_layout_close(); ?>
