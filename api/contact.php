<?php
/**
 * POST /api/contact
 *
 * Receives the contact-form payload, validates, persists to MySQL,
 * relays to edward@syncsity.com via the email layout, appends to Google Sheets.
 *
 * Returns: redirect to /contact?sent=1 (or ?error=...) — server-rendered redirect
 *          since the form is a native <form action="/api/contact" method="POST">.
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/db.php';
require_once SYNC_ROOT . '/lib/csrf.php';
require_once SYNC_ROOT . '/lib/functions.php';
require_once SYNC_ROOT . '/lib/mailer.php';
require_once SYNC_ROOT . '/lib/email_renderer.php';
require_once SYNC_ROOT . '/lib/sheets.php';

set_exception_handler(function (Throwable $e) {
    error_log('[contact FATAL] ' . $e->getMessage());
    redirect('/contact?error=' . urlencode('Server error. Please try again.'));
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

if (!csrf_verify($_POST['_csrf'] ?? '')) {
    redirect('/contact?error=' . urlencode('Session expired — please refresh and try again.'));
}

// Honeypot
if (!empty($_POST['website'])) {
    error_log('[contact] honeypot triggered');
    redirect('/contact?sent=1'); // appear normal to bots
}

$name    = clean($_POST['name']    ?? '', 120);
$email   = clean_email($_POST['email'] ?? '');
$company = clean($_POST['company'] ?? '', 200);
$subject = clean($_POST['subject'] ?? 'General', 60);
$message = trim((string)($_POST['message'] ?? ''));
$message = mb_substr($message, 0, 5000);
$gdpr    = !empty($_POST['gdpr_consent']) ? 1 : 0;

if (mb_strlen($name) < 2)            redirect('/contact?error=' . urlencode('Please tell us your name.'));
if (!$email)                          redirect('/contact?error=' . urlencode('Please enter a valid email address.'));
if (mb_strlen($message) < 10)        redirect('/contact?error=' . urlencode('Please write a slightly longer message.'));
if (!$gdpr)                          redirect('/contact?error=' . urlencode('Please tick the consent box.'));

$validSubjects = ['Engagement','Strategy session','Partnership','Press','General'];
if (!in_array($subject, $validSubjects, true)) $subject = 'General';

// Rate limit: 5 per IP per hour
$rlKey = 'contact:' . hash_ip();
if (!rate_limit($rlKey, (int)env('CONTACT_PER_IP_PER_HOUR', 5), 3600)) {
    redirect('/contact?error=' . urlencode('Too many messages from this network. Please try again in an hour.'));
}

// Persist
$id = DB::insert(
    "INSERT INTO contact_messages (name, email, company, subject, message, ip_hash, created_at)
     VALUES (?, ?, ?, ?, ?, ?, NOW())",
    [$name, $email, $company ?: null, $subject, $message, hash_ip()]
);

audit('contact_submitted', ['id' => $id, 'subject' => $subject]);

// Relay to internal inbox
$relayed = false;
try {
    $internal = (string)env('MAIL_NOTIFY_INTERNAL', NOTIFY_INTERNAL);
    $rendered = render_email('contact_relay', [
        'name'    => $name,
        'email'   => $email,
        'company' => $company,
        'subject' => $subject,
        'message' => $message,
    ]);
    $relayed = Mailer::send($internal, 'Edward', $rendered['subject'], $rendered['html'], $rendered['text']);
} catch (Throwable $e) {
    error_log('[contact] relay send failed: ' . $e->getMessage());
}

// Best-effort Google Sheets append (re-uses the leads row shape; subject=source)
try {
    $sheetId = (string)env('GOOGLE_SHEETS_LEADS_ID', '');
    if ($sheetId !== '') {
        GoogleSheets::appendRow($sheetId, [
            date('Y-m-d H:i:s'),
            $email, $name, $company, '',
            '', '', '', '',
            $subject,
            mb_substr($message, 0, 1000),
            '',
            '', '', '',
            '', '', '', '',
            '', '',
            'contact_form',
            '', '',
        ], (string)env('GOOGLE_SHEETS_LEADS_RANGE', 'Leads!A1'));
        DB::run("UPDATE contact_messages SET sheet_appended = 1 WHERE id = ?", [$id]);
    }
} catch (Throwable $e) {
    error_log('[contact] sheet append failed: ' . $e->getMessage());
}

if ($relayed) {
    DB::run("UPDATE contact_messages SET relayed_to_email = 1 WHERE id = ?", [$id]);
}

redirect('/contact?sent=1');
