<?php
/**
 * Email — Day 3: extracted insight specific to their answers
 * Vars: name, root_cause_name, leak_amount, frustration_quote, real_block_quote,
 *       priority, biz_type, report_url, unsubscribe_url
 */
require_once __DIR__ . '/layout.php';

$name            = $name ?? 'there';
$root_cause_name = $root_cause_name ?? 'your constraint';
$leak_amount     = $leak_amount ?? '£10,000';
$frustration_quote = $frustration_quote ?? '';
$real_block_quote  = $real_block_quote ?? '';
$priority        = $priority ?? 'lift conversion';
$biz_type        = $biz_type ?? 'service business';
$report_url      = $report_url ?? 'https://syncsity.com/dashboard';
$unsubscribe_url = $unsubscribe_url ?? '#';

$subject = 'Re-reading your answers — one thing stood out';

email_layout_open('A specific observation from your assessment that\'s worth two minutes.');
?>

<tr>
  <td style="padding:36px 36px 8px;" class="pd">
    <p style="margin:0 0 16px;font-family:Inter,Arial,sans-serif;font-size:17px;color:#0a1022;font-weight:600;"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>,</p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      I've been re-reading your assessment. One thing worth pulling out:
    </p>
  </td>
</tr>

<?php if ($real_block_quote !== ''): ?>
<tr>
  <td style="padding:0 36px 24px;" class="pd">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background:#f8f9fc;border-left:3px solid #3385DF;border-radius:0 8px 8px 0;">
      <tr>
        <td style="padding:18px 22px;font-family:Georgia,serif;font-size:15px;color:#0a1022;font-style:italic;line-height:1.6;">
          "<?= htmlspecialchars(mb_substr($real_block_quote, 0, 280), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($real_block_quote) > 280 ? '…' : '' ?>"
        </td>
      </tr>
    </table>
    <p style="margin:10px 0 0;font-family:'JetBrains Mono',monospace;font-size:11px;color:#7e8aa3;letter-spacing:0.10em;">— your own words</p>
  </td>
</tr>
<?php endif; ?>

<tr>
  <td style="padding:0 36px 18px;" class="pd">
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      That sentence is doing a lot of work. Most operators in your archetype
      (<em><?= htmlspecialchars($root_cause_name, ENT_QUOTES, 'UTF-8') ?></em> at a <?= htmlspecialchars($biz_type, ENT_QUOTES, 'UTF-8') ?>) don't realise the
      thing blocking them isn't the obvious thing — it's the <strong>second-order</strong> thing
      they wrote between the lines.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      Specifically: when you say what's holding you back, you're describing a
      <strong>capacity</strong> story. But the leak (<?= htmlspecialchars($leak_amount, ENT_QUOTES, 'UTF-8') ?>/month) almost
      always comes from a <strong>routing</strong> story — the wrong things hit your inbox before
      they hit anyone else's. Which means hiring more capacity makes the leak <em>worse</em>, not better.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      The simplest test: spend 15 minutes this week mapping every inbound (call,
      email, DM, form) and labelling each one <strong>"founder-required"</strong>,
      <strong>"founder-optional"</strong>, <strong>"founder-never"</strong>. The ratio will
      surprise you. Most operators discover they're personally handling 60-80% of
      what should be in the second two buckets.
    </p>
  </td>
</tr>

<tr>
  <td align="center" style="padding:8px 36px 32px;" class="pd">
    <a href="<?= htmlspecialchars($report_url, ENT_QUOTES, 'UTF-8') ?>"
       style="display:inline-block;padding:14px 32px;background:linear-gradient(135deg,#3385DF 0%,#0066D7 100%);color:#ffffff;font-family:Inter,Arial,sans-serif;font-weight:600;font-size:14px;text-decoration:none;border-radius:8px;">
      Re-read my report →
    </a>
  </td>
</tr>

<tr>
  <td style="padding:18px 36px 28px;background-color:#f8f9fc;border-top:1px solid #ececf2;" class="pd">
    <p style="margin:0 0 8px;font-family:Inter,Arial,sans-serif;font-size:13px;color:#3a455c;line-height:1.7;">
      Try the 15-minute test. If the ratio surprises you, reply with the result and
      I'll send you the second move.
    </p>
    <p style="margin:8px 0 0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;">
      — Edward<br>
      <span style="color:#a0acc4;">If this isn't useful, <a href="<?= htmlspecialchars($unsubscribe_url, ENT_QUOTES, 'UTF-8') ?>" style="color:#a0acc4;">unsubscribe in one click</a>.</span>
    </p>
  </td>
</tr>

<?php email_layout_close(); ?>
