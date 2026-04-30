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
    redirect('/contact.html?error=' . urlencode('Server error. Please try again.'));
});

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

// Where to bounce the user after success/error. Defaults to /contact.html.
// Static forms (contact.html, demo.html, booking.html) pass _return so each
// lands back on its own page.
$returnPage = (string)($_POST['_return'] ?? '/contact.html');
$allowedReturns = ['/contact.html', '/demo.html', '/booking.html', '/contact'];
if (!in_array($returnPage, $allowedReturns, true)) $returnPage = '/contact.html';

// CSRF is required when present (PHP-rendered forms); optional for the static
// contact.html form (which can't generate a session token). When CSRF isn't
// supplied we fall back to honeypot + stricter rate limiting.
$hasCsrf = !empty($_POST['_csrf']);
if ($hasCsrf && !csrf_verify((string)$_POST['_csrf'])) {
    redirect($returnPage . '?error=' . urlencode('Session expired — please refresh and try again.'));
}

// Honeypot
if (!empty($_POST['website'])) {
    error_log('[contact] honeypot triggered');
    redirect($returnPage . '?sent=1'); // appear normal to bots
}

// Combine firstName + lastName when 'name' isn't set (static form pattern)
$nameRaw = (string)($_POST['name'] ?? '');
if ($nameRaw === '') {
    $first = clean($_POST['firstName'] ?? '', 80);
    $last  = clean($_POST['lastName']  ?? '', 80);
    $nameRaw = trim($first . ' ' . $last);
}
$_POST['name'] = $nameRaw; // so the rest of the file works unchanged

$name    = clean($_POST['name']    ?? '', 120);
$email   = clean_email($_POST['email'] ?? '');
$company = clean($_POST['company'] ?? '', 200);
$subject = clean($_POST['subject'] ?? 'General', 60);
$message = trim((string)($_POST['message'] ?? ''));
$message = mb_substr($message, 0, 5000);
$gdpr    = !empty($_POST['gdpr_consent']) ? 1 : 0;

if (mb_strlen($name) < 2)            redirect($returnPage . '?error=' . urlencode('Please tell us your name.'));
if (!$email)                          redirect($returnPage . '?error=' . urlencode('Please enter a valid email address.'));
if (mb_strlen($message) < 10)        redirect($returnPage . '?error=' . urlencode('Please write a slightly longer message.'));
if (!$gdpr)                          redirect($returnPage . '?error=' . urlencode('Please tick the consent box.'));

// Accept any non-empty subject (the static form has different options like
// 'AI Voice Solutions', 'Lead Generation', etc. — keep them as-is).
if ($subject === '') $subject = 'General';

// When CSRF isn't supplied, tighten rate limit (3/hr instead of 5/hr).
$rlMax = $hasCsrf ? (int)env('CONTACT_PER_IP_PER_HOUR', 5) : 3;

// Rate limit by IP per hour (tightened above when CSRF not present)
$rlKey = 'contact:' . hash_ip();
if (!rate_limit($rlKey, $rlMax, 3600)) {
    redirect($returnPage . '?error=' . urlencode('Too many messages from this network. Please try again in an hour.'));
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

redirect($returnPage . '?sent=1');
