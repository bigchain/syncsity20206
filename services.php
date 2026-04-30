<?php
declare(strict_types=1);
define('SYNC_ROOT', __DIR__);
require_once SYNC_ROOT . '/lib/config.php';

$pageTitle = 'Services — AI voice, sales systems, process automation, workforce intelligence';
$pageDesc  = 'Four service lines, one philosophy: deploy AI only where it multiplies the result. Built with you, owned by you.';
$activeNav = 'services';

include SYNC_ROOT . '/partials/head.php';
include SYNC_ROOT . '/partials/header.php';
?>

<section class="section section--hero">
  <div class="bg-glow"></div>
  <div class="container container--md" style="text-align:center; position:relative;">
    <span class="eyebrow">Services</span>
    <h1 style="margin: var(--s-4) 0 var(--s-6);">Four levers. Each one only pulled when it'll move the number.</h1>
    <p class="lead">We don't sell every service to every client. The Aha! Assessment tells us which lever to pull first. The strategy session tells us how hard.</p>
  </div>
</section>

<?php
$svc = [
  [
    'tag'   => 'Voice operations',
    'title' => 'AI Voice &amp; Conversation Operations',
    'lede'  => 'Inbound calls, qualification, booking, and routing — handled 24/7 by branded AI voice agents in 40+ languages, with seamless human handoff.',
    'bullets' => [
      'Drop-in replacement for under-staffed call centres',
      '~90% reduction in cost per handled call',
      'Always-on, never-tired, never off-script',
      'Audit trail + sentiment analysis on every call',
      'Integrates with your CRM, calendar, and ticketing',
    ],
    'best' => 'Best for: Service businesses where the phone is still the front door — clinics, agencies, financial services, logistics.',
    'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.79 19.79 0 0 1 2.06 4.18 2 2 0 0 1 4.05 2h3a2 2 0 0 1 2 1.72c.13.96.37 1.9.7 2.81a2 2 0 0 1-.45 2.11L8 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.33 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>',
  ],
  [
    'tag'   => 'Sales',
    'title' => 'AI Sales System',
    'lede'  => 'A done-for-you outbound engine: prospect research, hyper-personalised sequences, multi-channel outreach, meeting booking. Owned and controlled by you.',
    'bullets' => [
      'Daily flow of qualified meetings, not noise',
      'Personalisation grounded in real research, not [first_name] tokens',
      'Inbox warming, deliverability monitoring, full transparency',
      'Direct hand-off to your sales team or your AE',
      'You own every domain, list, and template forever',
    ],
    'best' => 'Best for: B2B service / SaaS doing £1M-£20M who can\'t afford to lose another quarter to under-pipeline.',
    'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-8-8 18-2-8-8-2Z"/></svg>',
  ],
  [
    'tag'   => 'Operations',
    'title' => 'Process Automation',
    'lede'  => 'We map your back office, kill the manual seams, and rebuild the connective tissue with workflows + AI. You don\'t add headcount — you add capacity.',
    'bullets' => [
      'Map 30-60 of your highest-volume workflows',
      'Identify the 5 that account for ~80% of waste',
      'Rebuild them with native automation + LLM judgement',
      'Documented, version-controlled, handed over',
      'No vendor lock-in — runs on tools you already pay for',
    ],
    'best' => 'Best for: Operators who feel the team is "running just to stand still" — agencies, e-commerce ops, professional services.',
    'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>',
  ],
  [
    'tag'   => 'People',
    'title' => 'Workforce Intelligence',
    'lede'  => 'Capture the tribal knowledge inside your senior people, generate training assets, and monitor performance — all without adding management overhead.',
    'bullets' => [
      'AI-led knowledge capture from your top 10% performers',
      'Auto-generated playbooks, training, and onboarding',
      'Performance signal feeds without spy-ware vibes',
      'Helps new hires hit competence in weeks, not months',
      'Reduces founder dependency without losing standards',
    ],
    'best' => 'Best for: Founder-led businesses where "the way we do it here" lives in one person\'s head — and that\'s a problem.',
    'icon' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 17 10 5 10-5"/><path d="m2 12 10 5 10-5"/></svg>',
  ],
];
?>

<section class="section">
  <div class="container container--md">
    <?php foreach ($svc as $i => $s): ?>
      <div class="card card--feature" style="margin-bottom: var(--s-8); padding: var(--s-10);" data-reveal>
        <div style="display:flex; gap: var(--s-6); flex-wrap:wrap; align-items:flex-start;">
          <div class="card__icon <?= $i % 2 === 1 ? 'card__icon--orange' : '' ?>" style="width:64px; height:64px; flex-shrink:0;"><?= $s['icon'] ?></div>
          <div style="flex:1; min-width:240px;">
            <span class="eyebrow"><?= e($s['tag']) ?></span>
            <h2 style="margin: var(--s-2) 0 var(--s-4);"><?= $s['title'] ?></h2>
            <p class="lead" style="margin-bottom: var(--s-5);"><?= $s['lede'] ?></p>
            <ul style="display:grid; gap: var(--s-2); margin-bottom: var(--s-5);">
              <?php foreach ($s['bullets'] as $b): ?>
                <li style="display:flex; gap:10px; align-items:flex-start;">
                  <span style="color: var(--accent); font-weight:700; flex-shrink:0;">→</span>
                  <span class="muted"><?= e($b) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
            <p class="dim" style="font-style:italic;"><?= e($s['best']) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta-strip" data-reveal>
      <h2>Don't pick a service. Get a diagnosis first.</h2>
      <p class="lead">The Aha! Assessment tells us which one of these will move your number — and which won't.</p>
      <a href="/assess" class="btn btn--primary btn--lg" style="margin-top: var(--s-6);">Start the assessment <span class="arrow">→</span></a>
    </div>
  </div>
</section>

<?php include SYNC_ROOT . '/partials/footer.php'; ?>
