<?php
/**
 * Email — Day 1: warm check-in
 * Vars: name, root_cause_name, leak_amount, report_url, unsubscribe_url
 */
require_once __DIR__ . '/layout.php';

$name            = $name ?? 'there';
$root_cause_name = $root_cause_name ?? 'your constraint';
$leak_amount     = $leak_amount ?? '£10,000';
$report_url      = $report_url ?? 'https://syncsity.com/dashboard';
$unsubscribe_url = $unsubscribe_url ?? '#';

$subject = 'Did the diagnosis land?';

email_layout_open('A quick check-in 24 hours after your Revenue Intelligence Report.');
?>

<tr>
  <td style="padding:36px 36px 8px;" class="pd">
    <p style="margin:0 0 16px;font-family:Inter,Arial,sans-serif;font-size:17px;color:#0a1022;font-weight:600;">Hi <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>,</p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      Edward here. Wanted to check in 24 hours after your Revenue Intelligence Report
      landed in your inbox. Two questions:
    </p>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 24px;" class="pd">
    <ol style="margin:0 0 16px 22px;padding:0;font-family:Inter,Arial,sans-serif;font-size:15px;color:#0a1022;line-height:1.8;">
      <li><strong>Did the diagnosis ring true?</strong> The report named <em><?= htmlspecialchars($root_cause_name, ENT_QUOTES, 'UTF-8') ?></em> as the root cause. Operators usually have one of three reactions: nodding immediately, arguing with it, or 50/50. All three are useful — please tell me which.</li>
      <li><strong>Did the maths feel right?</strong> We pegged the leak at roughly <strong><?= htmlspecialchars($leak_amount, ENT_QUOTES, 'UTF-8') ?>/month</strong>. Is that conservative, central, or too aggressive for your setup?</li>
    </ol>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 32px;" class="pd">
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:15px;color:#3a455c;line-height:1.7;">
      Just hit reply. One sentence is fine. I read every reply, personally.
    </p>
    <p style="margin:0 0 14px;font-family:Inter,Arial,sans-serif;font-size:14px;color:#7e8aa3;line-height:1.6;">
      If you haven't opened the report yet, here it is:
    </p>
  </td>
</tr>

<tr>
  <td align="center" style="padding:0 36px 32px;" class="pd">
    <a href="<?= htmlspecialchars($report_url, ENT_QUOTES, 'UTF-8') ?>"
       style="display:inline-block;padding:14px 32px;background:linear-gradient(135deg,#3385DF 0%,#0066D7 100%);color:#ffffff;font-family:Inter,Arial,sans-serif;font-weight:600;font-size:14px;text-decoration:none;border-radius:8px;box-shadow:0 6px 18px rgba(51,133,223,0.30);">
      Open my report →
    </a>
  </td>
</tr>

<tr>
  <td style="padding:18px 36px 28px;background-color:#f8f9fc;border-top:1px solid #ececf2;" class="pd">
    <p style="margin:0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#3a455c;line-height:1.7;">
      — Edward<br>
      <span style="color:#7e8aa3;">edward@syncsity.com · founder, Syncsity</span>
    </p>
    <p style="margin:14px 0 0;font-family:Inter,Arial,sans-serif;font-size:11px;color:#a0acc4;">
      Want to stop these check-ins? <a href="<?= htmlspecialchars($unsubscribe_url, ENT_QUOTES, 'UTF-8') ?>" style="color:#a0acc4;">Unsubscribe in one click</a>.
    </p>
  </td>
</tr>

<?php email_layout_close(); ?>
