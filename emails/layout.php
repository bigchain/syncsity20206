<?php
/**
 * Syncsity — Email layout helper
 * Table-based, Outlook-compatible. Inline CSS only.
 */

function email_layout_open(string $preheader = ''): void
{
    $year = date('Y');
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <title>Syncsity</title>
  <style type="text/css">
    body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
    table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
    img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; display: block; }
    body { margin: 0 !important; padding: 0 !important; background-color: #f1f3f8; }
    a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }
    @media screen and (max-width: 600px) {
      .ec { width: 100% !important; }
      .pd { padding: 24px 22px !important; }
      .h1 { font-size: 26px !important; line-height: 1.2 !important; }
    }
  </style>
</head>
<body style="margin:0;padding:0;background-color:#f1f3f8;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;">
<?php if ($preheader !== ''): ?>
<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f1f3f8;"><?= htmlspecialchars($preheader, ENT_QUOTES, 'UTF-8') ?>&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;</div>
<?php endif; ?>

<table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color:#f1f3f8;">
  <tr>
    <td align="center" style="padding:24px 12px;">
      <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="ec" style="max-width:600px;background-color:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 4px 18px rgba(10,16,34,0.08);">

        <!-- Brand bar -->
        <tr>
          <td style="background-color:#0a1022;padding:24px 36px;" align="left">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
              <tr>
                <td>
                  <a href="https://syncsity.com" style="text-decoration:none;color:#ffffff;">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                      <tr>
                        <td style="padding-right:10px;vertical-align:middle;">
                          <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#3385DF 0%,#FCA311 100%);display:inline-block;text-align:center;line-height:32px;font-weight:700;color:#0a1022;font-family:Inter,Arial,sans-serif;font-size:15px;">S</div>
                        </td>
                        <td style="vertical-align:middle;">
                          <span style="font-family:Inter,Arial,sans-serif;font-size:17px;font-weight:600;color:#ffffff;letter-spacing:-0.01em;">Syncsity</span>
                        </td>
                      </tr>
                    </table>
                  </a>
                </td>
                <td align="right" style="vertical-align:middle;">
                  <span style="font-family:'JetBrains Mono',monospace;font-size:10px;color:#6b7a99;text-transform:uppercase;letter-spacing:0.16em;">AI Business Transformation</span>
                </td>
              </tr>
            </table>
          </td>
        </tr>
<?php
}

function email_layout_close(): void
{
    $year = date('Y');
?>
        <tr>
          <td style="background-color:#fafbfd;padding:24px 36px;border-top:1px solid #ececf2;" class="pd">
            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
              <tr>
                <td align="center">
                  <p style="margin:0 0 6px;font-family:Inter,Arial,sans-serif;font-size:12px;color:#7e8aa3;line-height:1.6;">
                    <a href="https://syncsity.com" style="color:#3385DF;text-decoration:none;font-weight:500;">syncsity.com</a>
                    &nbsp;·&nbsp;
                    <a href="https://syncsity.com/privacy" style="color:#7e8aa3;text-decoration:none;">Privacy</a>
                    &nbsp;·&nbsp;
                    <a href="https://syncsity.com/contact" style="color:#7e8aa3;text-decoration:none;">Contact</a>
                  </p>
                  <p style="margin:0;font-family:Inter,Arial,sans-serif;font-size:11px;color:#a0acc4;line-height:1.6;">
                    © <?= $year ?> Syncsity Ltd · London, United Kingdom<br>
                    You're receiving this because you took the free Aha! Assessment.
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

      </table>
    </td>
  </tr>
</table>
</body>
</html>
<?php
}
