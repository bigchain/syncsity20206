<?php
/**
 * Email: relay a contact-form message to the internal inbox
 * Vars: name, email, company, subject, message
 */
require_once __DIR__ . '/layout.php';

$name    = $name    ?? '';
$email   = $email   ?? '';
$company = $company ?? '';
$subjectField = $subject ?? 'General';
$message = $message ?? '';

$subject = 'New contact: ' . $subjectField . ' — ' . $name . ($company !== '' ? ' (' . $company . ')' : '');

email_layout_open('New message via syncsity.com/contact');
?>

<tr>
  <td style="padding:32px 36px 16px;" class="pd">
    <p style="margin:0 0 6px;font-family:'JetBrains Mono',monospace;font-size:10px;color:#3385DF;letter-spacing:0.18em;text-transform:uppercase;">New contact</p>
    <h1 class="h1" style="margin:0 0 4px;font-family:Inter,Arial,sans-serif;font-size:22px;color:#0a1022;font-weight:700;">
      <?= htmlspecialchars($subjectField, ENT_QUOTES, 'UTF-8') ?>
    </h1>
    <p style="margin:0;font-family:Inter,Arial,sans-serif;font-size:14px;color:#7e8aa3;">
      from <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>
      <?= $company ? '· ' . htmlspecialchars($company, ENT_QUOTES, 'UTF-8') : '' ?>
    </p>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 24px;" class="pd">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-collapse:collapse;">
      <tr>
        <td style="padding:8px 0;border-bottom:1px solid #ececf2;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;width:120px;">Name</td>
        <td style="padding:8px 0;border-bottom:1px solid #ececf2;font-family:Inter,Arial,sans-serif;font-size:13px;color:#0a1022;font-weight:500;"><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
      <tr>
        <td style="padding:8px 0;border-bottom:1px solid #ececf2;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;">Email</td>
        <td style="padding:8px 0;border-bottom:1px solid #ececf2;font-family:Inter,Arial,sans-serif;font-size:13px;color:#0a1022;font-weight:500;">
          <a href="mailto:<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" style="color:#3385DF;text-decoration:none;"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></a>
        </td>
      </tr>
      <?php if ($company !== ''): ?>
      <tr>
        <td style="padding:8px 0;border-bottom:1px solid #ececf2;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;">Company</td>
        <td style="padding:8px 0;border-bottom:1px solid #ececf2;font-family:Inter,Arial,sans-serif;font-size:13px;color:#0a1022;font-weight:500;"><?= htmlspecialchars($company, ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
      <?php endif; ?>
      <tr>
        <td style="padding:8px 0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#7e8aa3;">Subject</td>
        <td style="padding:8px 0;font-family:Inter,Arial,sans-serif;font-size:13px;color:#0a1022;font-weight:500;"><?= htmlspecialchars($subjectField, ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
    </table>
  </td>
</tr>

<tr>
  <td style="padding:0 36px 32px;" class="pd">
    <p style="margin:0 0 8px;font-family:'JetBrains Mono',monospace;font-size:10px;color:#7e8aa3;letter-spacing:0.16em;text-transform:uppercase;">Message</p>
    <div style="padding:18px;background-color:#f8f9fc;border-left:3px solid #3385DF;border-radius:6px;font-family:Inter,Arial,sans-serif;font-size:14px;color:#0a1022;line-height:1.7;white-space:pre-wrap;"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
  </td>
</tr>

<?php email_layout_close(); ?>
