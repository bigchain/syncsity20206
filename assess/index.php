<?php
/**
 * The Aha! Assessment — conversational diagnostic
 *
 * Server-renders a minimal shell + the question schema as JSON.
 * /assets/js/assess.js handles all rendering, branching, autosave, submission.
 *
 * Submission posts to /api/assess-submit which:
 *   1. Validates + rate-limits
 *   2. Creates / updates the user row
 *   3. Inserts the assessment row (status='queued')
 *   4. Fires off /api/report-generate (non-blocking)
 *   5. Sends the magic-link email so the user can come back to the report
 *   6. Returns { ok: true, redirect: '/assess/processing?id=...' }
 */

declare(strict_types=1);
define('SYNC_ROOT', dirname(__DIR__));
require_once SYNC_ROOT . '/lib/config.php';
require_once SYNC_ROOT . '/lib/csrf.php';

$pageTitle = 'Find the £10K+ leak in your business — Syncsity Aha! Assessment';
$pageDesc  = '15 minutes. 21 questions. One free Revenue Intelligence Report that names the exact constraint costing you money. No call. No card.';
$bodyClass = 'assess-page';

// Pre-fill email from hero ?email=...
$prefillEmail = '';
if (!empty($_GET['email'])) {
    $em = clean_email($_GET['email']);
    if ($em) $prefillEmail = $em;
}
?>
<?php include SYNC_ROOT . '/partials/head.php'; ?>

<main class="assess" data-assess>
  <div class="assess__top">
    <div class="assess__top-inner">
      <a href="/" class="assess__brand" aria-label="Syncsity home">
        <svg width="28" height="28" viewBox="0 0 32 32" aria-hidden="true">
          <circle cx="16" cy="16" r="13" stroke="url(#ag1)" stroke-width="2.4" fill="none"/>
          <path d="M9 19c1.6 1.2 4 2 6.5 2 3 0 5.5-1.6 5.5-4 0-2-1.6-3.2-4.6-4l-2-.5c-3-.7-4.9-2.3-4.9-4.8C9.5 5.2 11.8 4 14.5 4c2.2 0 4.2.8 5.5 2" stroke="url(#ag2)" stroke-width="2.4" stroke-linecap="round" fill="none"/>
          <defs>
            <linearGradient id="ag1" x1="3" y1="3" x2="29" y2="29" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#3385DF"/><stop offset="1" stop-color="#FCA311"/></linearGradient>
            <linearGradient id="ag2" x1="9" y1="4" x2="21" y2="21" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#FCA311"/><stop offset="1" stop-color="#3385DF"/></linearGradient>
          </defs>
        </svg>
        <span>Syncsity · Aha! Assessment</span>
      </a>
      <div class="assess__progress" aria-label="Progress"><div class="assess__progress-bar" data-progress-bar></div></div>
      <span class="assess__count" data-counter>1 / ~21</span>
      <button type="button" class="theme-toggle" aria-label="Toggle theme" data-theme-toggle title="Toggle theme">
        <svg class="ti-light" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
        <svg class="ti-dark"  width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/></svg>
      </button>
    </div>
  </div>

  <div class="assess__main">
    <div class="assess__stage" data-stage>
      <noscript>
        <div class="alert alert--info">
          The Aha! Assessment needs JavaScript. Enable JS or
          <a href="/contact?subject=Engagement">contact us directly</a> and we'll guide you through it.
        </div>
      </noscript>
    </div>
  </div>

  <input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>">
</main>

<?php
/* ── The question schema ──────────────────────────────────────────────────
 * Designed in three movements:
 *   1. Identify   (q1-q5)   — get who we're talking to + light context
 *   2. Diagnose   (q6-q15)  — surface the symptom, then quantify
 *   3. Reveal     (q16-q22) — psychological depth + vision + calibration
 *
 * Branching rules:
 *   - If revenue_band = "under_500k" → still process but the report tags
 *     them as "early-stage" and the upsell becomes a £95 tactical session.
 *   - If team_size = "200_plus" → corporate-ops branch (extra question on
 *     decision-making layers).
 *   - If cant_handle_more = "no_idea" → softer follow-up than "yes".
 * ────────────────────────────────────────────────────────────────────── */

$schema = [
    'estimatedTotal' => 21,
    'start'          => 'email',
    'questions'      => [

        // ── 1. EMAIL ──────────────────────────────────────────────────────
        [
            'id'    => 'email',
            'label' => 'Question 1',
            'title' => 'First — where should we send your report?',
            'subtitle' => 'No spam, no sequences, no nagging. The report is yours either way. We\'ll also email you a one-tap login so you can come back to it any time.',
            'type'  => 'email',
            'placeholder' => 'you@yourcompany.com',
            'required' => true,
            'autocomplete' => 'email',
            'validate' => true,
            'errorRequired' => 'A valid email is the one thing we genuinely need.',
            'cta'   => 'OK',
            'next'  => 'name',
            'skipIfFilled' => true,
        ],

        // ── 2. NAME ────────────────────────────────────────────────────────
        [
            'id'    => 'name',
            'label' => 'Question 2',
            'title' => 'And what should we call you?',
            'subtitle' => 'First name is fine. The report is written for you, not your job title.',
            'type'  => 'text',
            'placeholder' => 'Your first name',
            'required' => true,
            'minLength' => 2,
            'autocomplete' => 'given-name',
            'next' => 'company',
        ],

        // ── 3. COMPANY ─────────────────────────────────────────────────────
        [
            'id'    => 'company',
            'label' => 'Question 3',
            'title' => 'What\'s the business called?',
            'type'  => 'text',
            'placeholder' => 'Company name',
            'required' => true,
            'autocomplete' => 'organization',
            'next' => 'website',
        ],

        // ── 4. WEBSITE (optional but powerful for AI research) ────────────
        [
            'id'    => 'website',
            'label' => 'Question 4',
            'title' => 'Got a website?',
            'subtitle' => 'Optional — but if you share it, our AI reads it before writing your report. Markedly sharper insights when it does.',
            'type'  => 'url',
            'placeholder' => 'https://yourcompany.com',
            'required' => false,
            'optional' => true,
            'validate' => true,
            'next' => 'country',
        ],

        // ── 5. COUNTRY ─────────────────────────────────────────────────────
        [
            'id'    => 'country',
            'label' => 'Question 5',
            'title' => 'Where do you operate from?',
            'subtitle' => 'We\'ll calibrate to your market\'s benchmarks (UK ≠ US ≠ EU ≠ rest-of-world).',
            'type'  => 'single',
            'options' => [
                ['value' => 'uk',    'label' => '🇬🇧  United Kingdom'],
                ['value' => 'us',    'label' => '🇺🇸  United States'],
                ['value' => 'eu',    'label' => '🇪🇺  Europe'],
                ['value' => 'au',    'label' => '🇦🇺  Australia / NZ'],
                ['value' => 'mena',  'label' => '🌍  Middle East / Africa'],
                ['value' => 'asia',  'label' => '🌏  Asia / Pacific'],
                ['value' => 'other', 'label' => '🌐  Somewhere else'],
            ],
            'required' => true,
            'next' => 'biz_type',
        ],

        // ── 6. BUSINESS TYPE ──────────────────────────────────────────────
        [
            'id'    => 'biz_type',
            'label' => 'Question 6',
            'title' => 'What kind of business is this?',
            'subtitle' => 'Pick the closest. The report is sharper when we know the model.',
            'type'  => 'single',
            'options' => [
                ['value' => 'agency',     'label' => 'Agency / Studio (marketing, design, dev, content)'],
                ['value' => 'pro_serv',   'label' => 'Professional services (consulting, accounting, legal, etc.)'],
                ['value' => 'ecommerce',  'label' => 'E-commerce / DTC brand'],
                ['value' => 'saas',       'label' => 'SaaS / Software product'],
                ['value' => 'logistics',  'label' => 'Logistics / 3PL / Operations-heavy'],
                ['value' => 'finserv',    'label' => 'Financial services / Fintech'],
                ['value' => 'health',     'label' => 'Health / Wellness / Clinic'],
                ['value' => 'manuf',      'label' => 'Manufacturing / Industrial'],
                ['value' => 'corporate',  'label' => 'Larger corporate / multi-unit'],
                ['value' => 'other',      'label' => 'Something else'],
            ],
            'required' => true,
            'next' => 'team_size',
        ],

        // ── 7. TEAM SIZE ──────────────────────────────────────────────────
        [
            'id'    => 'team_size',
            'label' => 'Question 7',
            'title' => 'How big is the team right now?',
            'type'  => 'single',
            'options' => [
                ['value' => 'solo',     'label' => 'Just me'],
                ['value' => '2_10',    'label' => '2 – 10 people'],
                ['value' => '11_50',   'label' => '11 – 50 people'],
                ['value' => '51_200',  'label' => '51 – 200 people'],
                ['value' => '200_plus','label' => '200+ people'],
            ],
            'required' => true,
            'next' => [
                '200_plus' => 'org_layers',
                '_default' => 'revenue_band',
            ],
        ],

        // ── 7b. CORPORATE BRANCH (only fires for 200+) ────────────────────
        [
            'id'    => 'org_layers',
            'label' => 'Question 7a · For larger orgs',
            'title' => 'How many decision layers between you and the front line?',
            'subtitle' => 'Asking because 200+ orgs almost always have hidden waste between layers. The number tells us where to look.',
            'type'  => 'single',
            'options' => [
                ['value' => '1_2', 'label' => '1 – 2 layers'],
                ['value' => '3_4', 'label' => '3 – 4 layers'],
                ['value' => '5_plus','label' => '5+ layers'],
                ['value' => 'unsure','label' => "Honestly, I'm not sure"],
            ],
            'required' => true,
            'next' => 'revenue_band',
        ],

        // ── 8. REVENUE BAND ───────────────────────────────────────────────
        [
            'id'    => 'revenue_band',
            'label' => 'Question 8',
            'title' => 'Roughly, what\'s the annual revenue?',
            'subtitle' => 'Bands only — we don\'t need the exact number. Calibrates the leak estimate.',
            'type'  => 'single',
            'options' => [
                ['value' => 'under_500k',  'label' => 'Under £500K'],
                ['value' => '500k_1m',     'label' => '£500K – £1M'],
                ['value' => '1m_5m',       'label' => '£1M – £5M'],
                ['value' => '5m_20m',      'label' => '£5M – £20M'],
                ['value' => '20m_100m',    'label' => '£20M – £100M'],
                ['value' => '100m_plus',   'label' => '£100M+'],
                ['value' => 'prefer_not',  'label' => 'Prefer not to say'],
            ],
            'required' => true,
            'next' => 'frustration',
        ],

        // ── 9. THE BIGGEST FRUSTRATION ────────────────────────────────────
        [
            'id'    => 'frustration',
            'label' => 'Question 9 · The frustration',
            'title' => 'What\'s the ONE thing frustrating you most right now?',
            'subtitle' => 'Pick the closest. We\'re going to challenge it, by the way — that\'s the point.',
            'type'  => 'single',
            'options' => [
                ['value' => 'not_enough_inquiries', 'label' => "My phone doesn't ring enough — I need more inquiries"],
                ['value' => 'low_conversion',       'label' => "Lots of inquiries but few become customers"],
                ['value' => 'can_not_deliver',      'label' => "Can't deliver the work we already have"],
                ['value' => 'thin_margin',          'label' => "Margin is too thin even when busy"],
                ['value' => 'founder_dependent',    'label' => "Whole business runs through me — I can't take a holiday"],
                ['value' => 'running_to_stand',     'label' => "We grew, but it feels like we're just running to stand still"],
                ['value' => 'team_quality',         'label' => "The team is busy but the output isn't getting better"],
                ['value' => 'other',                'label' => "Something else"],
            ],
            'required' => true,
            'next' => 'frustration_more',
        ],

        // ── 10. TELL US MORE ──────────────────────────────────────────────
        [
            'id'    => 'frustration_more',
            'label' => 'Question 10',
            'title' => 'Tell us more about that.',
            'subtitle' => 'Two or three sentences. The more honest, the better the report. (You can be brutally honest — only Edward reads this.)',
            'type'  => 'textarea',
            'placeholder' => "What's actually happening week to week...",
            'required' => true,
            'minLength' => 30,
            'next' => 'monthly_inquiries',
        ],

        // ── 11. MONTHLY INQUIRIES ─────────────────────────────────────────
        [
            'id'    => 'monthly_inquiries',
            'label' => 'Question 11 · The numbers',
            'title' => 'How many qualified inquiries do you get in a typical month?',
            'subtitle' => 'Best estimate. "Qualified" = actually a real prospect, not random spam.',
            'type'  => 'number',
            'placeholder' => 'e.g. 25',
            'min' => '0',
            'required' => true,
            'next' => 'conversion_rate',
        ],

        // ── 12. CONVERSION RATE ───────────────────────────────────────────
        [
            'id'    => 'conversion_rate',
            'label' => 'Question 12',
            'title' => 'Of those, what % become paying customers?',
            'subtitle' => 'Best guess. We\'re looking at the conversion gap, not auditing your CRM.',
            'type'  => 'number',
            'placeholder' => 'e.g. 20',
            'min' => '0',
            'required' => true,
            'next' => 'avg_deal',
        ],

        // ── 13. AVERAGE DEAL ──────────────────────────────────────────────
        [
            'id'    => 'avg_deal',
            'label' => 'Question 13',
            'title' => 'What\'s a typical deal / project / customer worth?',
            'subtitle' => 'In £. First-year value or single-engagement value — whichever is more representative.',
            'type'  => 'currency',
            'currency' => '£',
            'placeholder' => '5000',
            'required' => true,
            'next' => 'capacity',
        ],

        // ── 14. CAPACITY ──────────────────────────────────────────────────
        [
            'id'    => 'capacity',
            'label' => 'Question 14',
            'title' => 'If 10 perfect-fit clients called you tomorrow, could you handle them all?',
            'subtitle' => 'This question tells us more than you think.',
            'type'  => 'single',
            'options' => [
                ['value' => 'yes_easy',   'label' => 'Yes, easily'],
                ['value' => 'yes_strain', 'label' => "Yes — but it'd hurt"],
                ['value' => 'no',         'label' => "No, definitely not"],
                ['value' => 'unsure',     'label' => "Honestly, I'm not sure"],
            ],
            'required' => true,
            'next' => 'already_tried',
        ],

        // ── 15. WHAT HAVE YOU TRIED ───────────────────────────────────────
        [
            'id'    => 'already_tried',
            'label' => 'Question 15 · The truth',
            'title' => 'What have you already tried to fix this?',
            'subtitle' => "Big agencies, hires, courses, tools, all-nighters — list them. We won't repeat what didn't work.",
            'type'  => 'textarea',
            'placeholder' => 'Hired someone, tried this tool, attended that course...',
            'required' => true,
            'minLength' => 20,
            'next' => 'real_block',
        ],

        // ── 16. THE REAL BLOCK ────────────────────────────────────────────
        [
            'id'    => 'real_block',
            'label' => 'Question 16 · The deeper layer',
            'title' => "If you really think about it, what's ACTUALLY held you back from fixing this already?",
            'subtitle' => 'The answer most people give first is rarely the real one. Pick the one that stings a bit.',
            'type'  => 'single',
            'options' => [
                ['value' => 'no_time',          'label' => "I don't have time — I'm already maxed out"],
                ['value' => 'wrong_move',       'label' => "I can't afford to make the wrong move"],
                ['value' => 'been_burned',      'label' => "I've been burned by 'experts' before"],
                ['value' => 'pulled_into_ops',  'label' => "I keep getting pulled back into day-to-day ops"],
                ['value' => 'dont_know_what',   'label' => "I genuinely don't know what would actually work"],
                ['value' => 'team_resistance',  'label' => "The team would resist any real change"],
                ['value' => 'cash_tight',       'label' => "Cash is tight — I can't afford to invest right now"],
                ['value' => 'unsure',           'label' => "Honestly, I'm not sure — that's part of the problem"],
            ],
            'required' => true,
            'next' => 'real_block_more',
        ],

        // ── 17. EXPAND ────────────────────────────────────────────────────
        [
            'id'    => 'real_block_more',
            'label' => 'Question 17',
            'title' => 'Tell us more about that.',
            'subtitle' => "What's the story behind that answer? This is the most important question on the page — the report leans hard on it.",
            'type'  => 'textarea',
            'placeholder' => 'Be honest — this is what makes the report actually land...',
            'required' => true,
            'minLength' => 40,
            'next' => 'vision',
        ],

        // ── 18. THE VISION ────────────────────────────────────────────────
        [
            'id'    => 'vision',
            'label' => 'Question 18 · The vision',
            'title' => 'Picture the business 12 months from now at its absolute best. What\'s different?',
            'subtitle' => 'Don\'t hold back. We use this to calibrate the gap — and the gap is usually smaller than you think.',
            'type'  => 'textarea',
            'placeholder' => '12 months from now, the business looks like...',
            'required' => true,
            'minLength' => 40,
            'next' => 'confidence',
        ],

        // ── 19. CONFIDENCE SCALE ──────────────────────────────────────────
        [
            'id'    => 'confidence',
            'label' => 'Question 19',
            'title' => "On a scale of 0–10, how confident are you that you'll get there on the path you're currently on?",
            'subtitle' => '0 = no chance. 10 = inevitable.',
            'type'  => 'scale',
            'min'   => 0,
            'max'   => 10,
            'scaleLabels' => ['No chance', 'Inevitable'],
            'required' => true,
            'next' => 'priority',
        ],

        // ── 20. 90-DAY PRIORITY ───────────────────────────────────────────
        [
            'id'    => 'priority',
            'label' => 'Question 20',
            'title' => 'What\'s the priority for the next 90 days?',
            'subtitle' => 'Pick one. Even if two feel important. We need to know where to point the leverage.',
            'type'  => 'single',
            'options' => [
                ['value' => 'more_inquiries',   'label' => 'More qualified inquiries'],
                ['value' => 'better_convert',   'label' => 'Convert inquiries we\'re already getting'],
                ['value' => 'add_capacity',     'label' => 'Add capacity without adding chaos'],
                ['value' => 'reduce_founder',   'label' => 'Reduce how much runs through me'],
                ['value' => 'lift_margin',      'label' => 'Lift margin without lifting price'],
                ['value' => 'stabilise',        'label' => 'Stabilise — we\'re a bit chaotic right now'],
            ],
            'required' => true,
            'next' => 'anything_else',
        ],

        // ── 21. ANYTHING ELSE ─────────────────────────────────────────────
        [
            'id'    => 'anything_else',
            'label' => 'Final question',
            'title' => 'Anything else we should know?',
            'subtitle' => 'Optional. Only Edward reads this. The most useful reports often come from this box.',
            'type'  => 'textarea',
            'placeholder' => 'Anything that didn\'t fit elsewhere...',
            'required' => false,
            'optional' => true,
            'cta' => 'Generate my report',
            'next' => null,
        ],
    ],
];

// Pass prefill into the schema.
?>

<script id="assess-schema" type="application/json"><?= json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php if ($prefillEmail): ?>
<script>try{sessionStorage.setItem('syncsity-prefill-email', <?= json_encode($prefillEmail) ?>);}catch(e){}</script>
<?php endif; ?>

<script src="/assets/js/core.js" defer></script>
<script src="/assets/js/assess.js" defer></script>
</body>
</html>
