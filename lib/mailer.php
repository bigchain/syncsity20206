<?php
/**
 * Syncsity — Mailer
 *
 * Tries PHPMailer (if installed via cPanel's "PHP Composer / PHPMailer" or
 * /usr/share/php). Falls back to native mail() with a multipart body.
 *
 * On HostFluid Exim is the local MTA — port 587 with STARTTLS to the cPanel
 * mailbox is the recommended path. SPF + DKIM + DMARC are configured at the
 * server level for any *@syncsity.com address.
 */

declare(strict_types=1);

if (!defined('SYNC_ROOT')) define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';

class Mailer
{
    public static function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody = ''
    ): bool {
        $email = filter_var($toEmail, FILTER_VALIDATE_EMAIL);
        if (!$email) return false;

        $toName  = strip_tags($toName);
        $subject = strip_tags($subject);
        if ($textBody === '') {
            $textBody = trim(strip_tags(preg_replace(['/<br\s*\/?>/i', '/<\/p>/i', '/<\/li>/i'], "\n", $htmlBody)));
        }

        // Try PHPMailer if available
        if (self::tryPhpMailer($email, $toName, $subject, $htmlBody, $textBody)) {
            return true;
        }
        return self::nativeMail($email, $toName, $subject, $htmlBody, $textBody);
    }

    private static function tryPhpMailer(string $email, string $name, string $subject, string $html, string $text): bool
    {
        // Only attempt if PHPMailer is autoloaded
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            $vendor = SYNC_ROOT . '/vendor/autoload.php';
            if (file_exists($vendor)) {
                require_once $vendor;
            } else {
                return false;
            }
        }
        if (!class_exists('PHPMailer\\PHPMailer\\PHPMailer')) return false;

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = (string)env('SMTP_HOST', 'localhost');
            $mail->Port       = (int)env('SMTP_PORT', 587);
            $mail->SMTPAuth   = !empty(env('SMTP_USER'));
            $mail->Username   = (string)env('SMTP_USER', '');
            $mail->Password   = (string)env('SMTP_PASS', '');

            $enc = strtolower((string)env('SMTP_ENCRYPTION', 'tls'));
            $mail->SMTPSecure = match ($enc) {
                'ssl'  => \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS,
                'tls'  => \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS,
                default => '',
            };

            if (APP_ENV === 'production') {
                $mail->SMTPOptions = ['ssl' => [
                    'verify_peer'      => true,
                    'verify_peer_name' => true,
                    'allow_self_signed' => false,
                ]];
            }

            $fromEmail = (string)env('MAIL_FROM_EMAIL', 'hello@syncsity.com');
            $fromName  = (string)env('MAIL_FROM_NAME',  APP_NAME);
            $replyTo   = (string)env('MAIL_REPLY_TO',   $fromEmail);

            $mail->setFrom($fromEmail, $fromName);
            $mail->addReplyTo($replyTo, $fromName);
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;

            $mail->send();
            return true;
        } catch (Throwable $e) {
            error_log('[Mailer:phpmailer] ' . $e->getMessage());
            return false;
        }
    }

    private static function nativeMail(string $email, string $name, string $subject, string $html, string $text): bool
    {
        $fromEmail = (string)env('MAIL_FROM_EMAIL', 'hello@syncsity.com');
        $fromName  = (string)env('MAIL_FROM_NAME',  APP_NAME);
        $replyTo   = (string)env('MAIL_REPLY_TO',   $fromEmail);

        $boundary = '=_' . md5(uniqid((string)time(), true));
        $headers  = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            'From: ' . self::encodeHeader($fromName) . ' <' . $fromEmail . '>',
            'Reply-To: ' . $replyTo,
            'X-Mailer: Syncsity/1.0',
        ]);

        $body  = '--' . $boundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($text)) . "\r\n";
        $body .= '--' . $boundary . "\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($html)) . "\r\n";
        $body .= '--' . $boundary . '--';

        $ok = @mail($email, self::encodeHeader($subject), $body, $headers, '-f' . $fromEmail);
        if (!$ok) {
            error_log('[Mailer:native] mail() returned false for ' . $email);
        }
        return $ok;
    }

    private static function encodeHeader(string $text): string
    {
        if (mb_detect_encoding($text, 'ASCII', true)) return $text;
        return '=?UTF-8?B?' . base64_encode($text) . '?=';
    }
}
