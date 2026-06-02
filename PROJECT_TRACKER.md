# Syncsity — Project Tracker

> The single source of truth for everything we set out to build, what's done,
> what's pending, and how it all hangs together. Read this before any new work.
>
> **For session handover and quick orientation, read `HANDOVER.md` first.**
>
> **Last updated:** 2026-05-04 · **Status:** Live · **Repo:** `bigchain/syncsity20206` · **Latest commit:** `1e121c0`

---

## 1. Why This Project Exists

Take an existing Lovable-built marketing site and **add a real product layer underneath**:

- A free conversational AI diagnostic that produces a personalised "Revenue Intelligence Report"
- Magic-link login (no passwords)
- A user dashboard
- An automatic 5-step follow-up email sequence
- A genuinely smart contact form (better than WordPress)
- Better SEO/GEO so the site shows up in both Google and AI-generated answers

**Anchor sentence:** *"In 15 minutes we uncover the hidden constraint costing you £10K+ a month and tell you exactly how to fix it — even if you never speak to us again."*

**Target market:** UK & US mid-market service businesses, £1M–£250M revenue, 10–250 staff, ops-heavy.

---

## 2. Architecture (Locked Decisions)

| Layer | Tech | Notes |
|-------|------|-------|
| Marketing front | **Original Lovable React SPA** (`/index.html`) | Kept as the source of truth for design — user explicitly preferred this look |
| New static pages | **Hand-written HTML + `/assets/css/static-page.css`** (no Tailwind) | `/contact.html`, `/demo.html`, `/booking.html`, `/terms.html`, `/privacy.html`, `/sitemap.html`, `/blog/index.php` |
| Product surfaces | **PHP 8.2 + MySQL** | `/assess`, `/auth/login`, `/dashboard`, `/api/*` |
| AI engine | **OpenRouter** — `anthropic/claude-sonnet-4.5` primary, `google/gemini-2.5-pro` fallback | 3-stage pipeline: research → analyse → write |
| Email | **PHPMailer over local Exim** (port 25, no auth — FundCollective pattern) | From `hello@syncsity.com`, replies to `edward@syncsity.com` |
| Persistence | MySQL primary, Google Sheets append best-effort | DB: `marieatlasco_ehhdyy` |
| Auth | **Magic-link** (no passwords). 64-hex token, 15-min expiry, single-use | Adapted from FundCollective's pattern |
| Background jobs | **Fire-and-forget self-curl + cron backstop** | `shell_exec` is disabled on HostFluid |
| Hosting | HostFluid (Hetzner EX63), cPanel, AlmaLinux 9.7, ea-php82 | Account: `marieatlasco` · domain: syncsity.com |

---

## 3. What's Live

### 3.1 Marketing surface

| Page | URL | Status | Tech |
|------|-----|--------|------|
| Home | `/` | ✅ | Original Lovable SPA — design preserved |
| Why Syncsity | `/why-syncsity` | ✅ SPA | React route |
| Pricing | `/pricing` | ✅ SPA | React route |
| About | `/about-us` | ✅ SPA | React route |
| Solutions overview | `/solutions` | ✅ SPA | React route |
| AI Voice | `/solutions/voice-solutions` | ✅ SPA | |
| AI Sales | `/solutions/lead-generation` | ✅ SPA | |
| Process Automation | `/solutions/process-optimization` | ✅ SPA | |
| Workforce Intelligence | `/solutions/workforce-transformation` | ✅ SPA | |
| Transform overview | `/transform` | ✅ SPA | |
| Market Domination | `/transform/market-domination` | ✅ SPA | |
| Revenue Acceleration | `/transform/revenue-acceleration` | ✅ SPA | |
| Operational Supremacy | `/transform/operational-supremacy` | ✅ SPA | |
| Calculators | `/calculators` | ✅ SPA | |

### 3.2 New static pages (now migrated to PHP partials — universal nav + footer)

| Page | URL | Status | File | Notes |
|------|-----|--------|------|-------|
| Contact | `/contact.html` → `/contact.php` | ✅ migrated | `contact.php` | 4-step smart wizard, posts to `/api/contact` |
| Demo | `/demo.html` → `/demo.php` | ✅ migrated | `demo.php` | Form posts to `/api/contact` |
| Book a Session | `/booking.html` → `/booking.php` | ✅ migrated | `booking.php` | Calendly iframe + Service/Offer JSON-LD |
| Terms | `/terms.html` → `/terms.php` | ✅ migrated | `terms.php` | Long-form prose + WebPage/legal JSON-LD |
| Privacy | `/privacy.html` → `/privacy.php` | ✅ migrated | `privacy.php` | UK GDPR-aligned |
| Sitemap (HTML) | `/sitemap.html` → `/sitemap.php` | ✅ migrated | `sitemap.php` | 7-column link grid |
| Insights / Blog | `/blog` | ✅ | `blog/index.php` | Manifesto essay |
| Client login (legacy) | `/client-login.html` | ✅ | unchanged | 14-line meta-refresh redirect to `/auth/login` |
| Demo home | `/syncdemo/index.html` | ✅ | `syncdemo/index.html` | Hand-written home page rebuild — preview of replacement for the SPA at `/` |

**`.htaccess`** internally rewrites `/foo.html` → `/foo.php` for migrated pages (no 301, so old inbound links keep working). Extension-less URLs (`/terms`, `/privacy`, etc.) also serve the `.php` content.

All migrated pages share the same universal nav (with hover dropdowns: Transform · Solutions · Resources) + 3 CTAs (Log in · Free assessment · Book a Session) + 4-column footer.

### 3.2.1 Universal partials architecture (NEW)

| File | Role |
|------|------|
| `partials/site-head.php` | Opens `<html><head>` with meta + 4 universal JSON-LD schemas (Organization, WebSite, WebPage, BreadcrumbList). Caller sets `$page_title`, `$page_description`, `$page_canonical`, `$page_breadcrumb`, `$page_extra_jsonld`. |
| `partials/site-nav.php` | Skip-link + `<header class="nav">` with 6-item nav + 3 hover dropdowns (Transform 3-card / Solutions 4-card / Resources 2-card) + 3 CTAs. |
| `partials/site-footer.php` | `<footer class="footer-home">` 4-column + bottom row + auth-aware Log-in→Dashboard JS + nav-mobile.js include + closes `</body></html>`. |
| `assets/css/site-nav.css` | Universal nav + dropdowns + skip-link + drawer + footer styles. Includes `body.page--light` modifier for inner pages. Loaded by `site-head.php`. |
| `assets/js/nav-mobile.js` | Accordion drawer for mobile (<880px). Walks only direct children of `.nav__links`, extracts sub-items from dropdown panels, builds expandable sections. Inline-style fallback so drawer never renders in flow even with stale CSS cache. |

Page-specific PHP variables consumed by `site-head.php`:
```php
$page_path_prefix = '/';        // or '../' for nested paths
$page_title       = 'Page Title | Syncsity';
$page_description = '...';      // 140-160 chars
$page_canonical   = 'https://syncsity.com/...';
$page_breadcrumb  = [['Home','https://syncsity.com/'], [...]];
$page_extra_jsonld = '...';     // raw JSON-LD <script> blocks (FAQPage, Service, etc.)
```

### 3.2.2 SPA-route migrations (NEW — replacing React routes with hand-written PHP)

| Page | URL | Status | File | Theme |
|------|-----|--------|------|-------|
| AI Voice Operations | `/solutions/voice-solutions` | ✅ migrated | `solutions/voice-solutions.php` | LIGHT |

`.htaccess` extended so `/solutions/foo` and `/transform/foo` route to the corresponding `.php` file BEFORE the SPA fallback fires. Unmigrated routes still serve the React bundle.

### 3.3 Product surfaces (PHP)

| Surface | URL | Status | Notes |
|---------|-----|--------|-------|
| Conversational diagnostic | `/assess` | ✅ | 21 questions, branching, autosave, keyboard nav |
| Generation status page | `/assess/processing?id=…` | ✅ | Polls every 2.2s |
| Report viewer | `/assess/report?t=…` | ✅ | Server-rendered, share-token-gated |
| Magic-link login | `/auth/login` | ✅ | |
| Login email | (sent automatically) | ✅ | 64-hex token, 15-min expiry, single-use |
| Dashboard | `/dashboard` | ✅ | Auth-gated, lists user's reports |
| Logout | `/api/logout` | ✅ | |

### 3.4 API endpoints

| Endpoint | Method | What it does |
|----------|--------|--------------|
| `/api/magic-link` | POST | Validates, rate-limits, finds-or-creates user, sends magic link |
| `/api/auth?token=…` | GET | Validates token, consumes (single-use), creates session |
| `/api/logout` | GET | Destroys session |
| `/api/assess-submit` | POST | Saves assessment, fires report-generate, sends magic link |
| `/api/report-generate` | CLI + HTTP | 3-stage OpenRouter pipeline; idempotent |
| `/api/report-status?id=…` | GET | Polled by processing page |
| `/api/contact` | POST | Saves, relays to `edward@syncsity.com`, appends to Sheet |
| `/api/email-worker` | CLI | Cron-driven follow-up sender |
| `/api/unsubscribe?email=…&t=hmac` | GET | One-click HMAC opt-out |

### 3.5 Email engine

5-step automatic follow-up after a user completes an assessment:

| Day | Template | Purpose |
|-----|----------|---------|
| 1 | `emails/followup_day1.php` | Warm check-in: "Did the diagnosis ring true?" |
| 3 | `emails/followup_day3.php` | Pulls a quote from THEIR answers + concrete 15-min self-test |
| 7 | `emails/followup_day7.php` | "What 70% do wrong vs 30% who win" pattern observation |
| 14 | `emails/followup_day14.php` | Soft Strategy Session offer (£950) |
| 30 | `emails/followup_day30.php` | "Last note" — clean exit, no nag |

Plus transactional templates:
- `emails/magic_link.php`
- `emails/report_ready.php`
- `emails/contact_relay.php`

### 3.6 Database schema

| Table | Purpose |
|-------|---------|
| `users` | email, name, company, magic-link token, GDPR consent |
| `assessments` | answers (JSON), extracted columns, status, leak_amount, root_cause_name, report (JSON), share_token |
| `email_log` | one row per (user × sequence_step) — idempotency |
| `email_optout` | one-click opt-out list |
| `rate_limits` | DB-backed token bucket |
| `audit_log` | every important action (logins, submissions, etc.) |
| `contact_messages` | every contact-form submission |

### 3.7 SEO / GEO

| Asset | Status |
|-------|--------|
| `sitemap.xml` (every URL listed) | ✅ |
| `robots.txt` (AI bots whitelisted) | ✅ |
| `llms.txt` (AI-curated summary) | ✅ |
| `humans.txt` | ✅ |
| `og-image.svg` | ✅ (PNG version pending) |
| Canonical URLs on every page | ✅ |
| Open Graph + Twitter card meta | ✅ |
| JSON-LD: Organization (home), BlogPosting (manifesto), ContactPage (contact) | ✅ |

---

## 4. What's Pending (Priority Order)

### Immediate / Production-blocking
- [ ] **DB password rotation** post-chat-leak (cPanel → MySQL Databases)
- [ ] **OpenRouter key rotation** post-chat-leak (https://openrouter.ai/keys)
- [ ] **`SMTP_PASS`** in `.env` — set to the cPanel mailbox password for `hello@syncsity.com` (forms work, but report-ready emails won't go without it)
- [ ] **Add email-worker cron** in cPanel → Cron Jobs:
      `0 * * * * cd /home/marieatlasco/public_html && /usr/local/cpanel/3rdparty/bin/php api/email-worker.php >> storage/logs/email-worker.log 2>&1`

### Polish / SEO+GEO upgrades (impact-ranked)
- [ ] Generate proper `og-image.png` (1200×630 PNG, not SVG — universal compat)
- [ ] Add `FAQPage` JSON-LD to `/contact.html`
- [ ] Add `WebSite` + `SearchAction` schema to home (sitelinks search box)
- [ ] Add `BreadcrumbList` JSON-LD to non-home pages
- [ ] Add TL;DR summaries at top of long-form pages (manifesto, etc.)
- [ ] Add `Person` schema for Edward Hadome
- [ ] Cite the "73%" statistic in the manifesto with a methodology footnote
- [ ] Wire analytics — recommend Plausible (privacy-first, no banner needed)

### Content / future articles (5 essays were promised; 1 done)
- [x] **Diagnose first. Or don't bother.** (manifesto — published)
- [ ] The Founder Bottleneck — three tests anyone can run before lunch
- [ ] AI Voice Operations vs human call centres: 12-month real numbers (with chart + table)
- [ ] The Capacity Wall — when "get more leads" is the worst advice (with diagram)
- [ ] The Hidden Subsidy — 7 ways UK service firms subsidise their worst clients (with case-study table)

Each future article needs: chart(s), table(s), photo(s), 1500-2500 words, full SEO + GEO markup.

### Visual consistency
- [ ] `/auth/login` and `/dashboard` use a different CSS system (`/assets/css/components.css`); could unify with `static-page.css` for a single visual language across the whole site
- [ ] `/assess`, `/assess/processing`, `/assess/report` — same — they have their own styling

### Long-term page rebuild (optional — "old design new tech" path)
The Lovable SPA serves the marketing pages today and looks how the user wants. Replacing each one with hand-written PHP/HTML using `static-page.css` as the design system would:
- Eliminate Tailwind/shadcn fragility
- Give 100% source control
- Allow deeper SEO customisation per page
- Make footer duplicates fixable cleanly

This is **explicitly optional** — the SPA works. The list is here in case the user wants to continue:
- [ ] `/why-syncsity` (HTML rebuild)
- [ ] `/pricing`
- [ ] `/solutions` (overview + 4 sub-pages)
- [ ] `/transform` (overview + 3 sub-pages)
- [ ] `/about-us`
- [ ] `/calculators`

---

## 5. Operational State

### 5.1 Server

| Item | Value |
|------|-------|
| Provider | HostFluid (Hetzner EX63), 77.42.1.80 |
| OS | AlmaLinux 9.7, cPanel 132+ |
| cPanel user | `marieatlasco` |
| Web root | `/home/marieatlasco/public_html/` |
| PHP | ea-php82 (FPM) |
| Email | Exim (local relay, port 25 no-auth) |
| Backups | JetBackup 5 → Google Drive |

### 5.2 Database

| Item | Value |
|------|-------|
| Engine | MySQL 8 |
| DB name | `marieatlasco_ehhdyy` |
| DB user | `marieatlasco_hey87` |
| Schema | `database/schema.sql` (idempotent — `CREATE TABLE IF NOT EXISTS`) |

### 5.3 Cron jobs (in cPanel → Cron Jobs)

```
*/5 * * * * cd /home/marieatlasco/public_html && /usr/local/cpanel/3rdparty/bin/php api/report-generate.php >> storage/logs/report-cron.log 2>&1

0 * * * * cd /home/marieatlasco/public_html && /usr/local/cpanel/3rdparty/bin/php api/email-worker.php >> storage/logs/email-worker.log 2>&1
```

The first is installed. The second still needs adding.

### 5.4 Secrets currently in `.env`

| Key | Status |
|-----|--------|
| `DB_PASS` | Set, **needs rotation** (leaked through chat) |
| `OPENROUTER_API_KEY` | Set, **needs rotation** (leaked through chat) |
| `SESSION_SECRET` | Set fresh on last deploy |
| `SMTP_PASS` | Empty — emails fall back to local Exim relay (works for transactional) |
| `GOOGLE_SHEETS_LEADS_ID` | Empty — Sheets append disabled until set |

### 5.5 GitHub

| Item | Value |
|------|-------|
| Repo | https://github.com/bigchain/syncsity20206 |
| Branch | `main` |
| Visibility | Public |

---

## 6. Deploy Runbook

### Routine deploy (every change after the initial one)

```bash
sudo -u marieatlasco git -C /home/marieatlasco/public_html pull origin main && systemctl reload ea-php82-php-fpm
```

This is **safe**. `git pull` only updates files in the repo; it doesn't touch `.env`, `storage/`, or anything outside the tracked tree.

### Recovery deploy (if `public_html` is in a bad state)

```bash
mv /home/marieatlasco/public_html /home/marieatlasco/public_html.bak.$(date +%Y%m%d-%H%M%S)
sudo -u marieatlasco git clone https://github.com/bigchain/syncsity20206.git /home/marieatlasco/public_html
# then re-create .env, storage/, schema, cron — see DEPLOY.md or our chat history
```

**Never run `rm -rf public_html`.** Always `mv` to preserve the prior state.

### Smoke test after any deploy

```bash
curl -sI https://syncsity.com/                                  | head -1   # 200
curl -sI https://syncsity.com/.env                              | head -1   # 403
curl -sI https://syncsity.com/assets/img/clients/pe.jpg         | head -1   # 200
curl -sI https://syncsity.com/contact.html                      | head -1   # 200
curl -sI https://syncsity.com/blog                              | head -1   # 200
curl -sI https://syncsity.com/assess                            | head -1   # 200
```

All six should return their expected status codes.

---

## 7. Lessons Learned (Don't Repeat)

| Mistake | Lesson |
|---------|--------|
| `rm -rf public_html` on first deploy | **Never delete; always `mv`.** Wiped the WordPress media library including all client logos. JetBackup recovered them; otherwise it would have been catastrophic. |
| Trying to patch the compiled SPA bundle with `sed` | **If a bundle isn't ours to source, treat it as immutable.** Patching minified JS with string replacements introduced cache mismatches and subtle bugs across browsers. |
| Broad CSS selectors like `[class*="bg-white"]` | **Use exact class selectors only.** Wildcards on a Tailwind+shadcn build cause collateral damage on every page. |
| Wholesale rebuilds in single commits | **One page per commit.** When 8 things change in one commit, neither party can isolate the regression. |
| Adding placeholder content that looks fake | **Either real or visibly intentional.** "PE / SEC / KEW" navy boxes looked like broken images, not stylistic choice. |
| Browser cache + 30-day Cache-Control on `/assets/*` | **Cache-bust JS/CSS via versioned URLs.** The 30-day cache meant fixes took days to be visible without manual hard-refresh. |
| Continuing past a "this looks wrong" comment | **Stop and revert at the first sign of regression.** Three more "fixes" don't recover from one wrong direction. |
| Mixing deploy commands with code commits | **Separate operational instructions from git commits.** Deploys belong in a runbook, not in chat. |

---

## 8. URL Reference Map

### Public marketing
- https://syncsity.com/ — home (SPA)
- https://syncsity.com/why-syncsity — why us (SPA)
- https://syncsity.com/pricing — pricing (SPA)
- https://syncsity.com/about-us — about (SPA)
- https://syncsity.com/solutions — solutions overview (SPA)
- https://syncsity.com/solutions/voice-solutions — AI Voice (SPA)
- https://syncsity.com/solutions/lead-generation — AI Sales (SPA)
- https://syncsity.com/solutions/process-optimization — Process Automation (SPA)
- https://syncsity.com/solutions/workforce-transformation — Workforce Intel (SPA)
- https://syncsity.com/transform — transform programmes (SPA)
- https://syncsity.com/transform/market-domination — Market Domination (SPA)
- https://syncsity.com/transform/revenue-acceleration — Revenue Acceleration (SPA)
- https://syncsity.com/transform/operational-supremacy — Operational Supremacy (SPA)
- https://syncsity.com/calculators — ROI calculator (SPA)
- https://syncsity.com/blog — Insights / manifesto (PHP, hand-written)

### Static pages (hand-written)
- https://syncsity.com/contact.html — 4-step smart contact form
- https://syncsity.com/demo.html — demo request
- https://syncsity.com/booking.html — Calendly Strategy Session booking
- https://syncsity.com/terms.html
- https://syncsity.com/privacy.html
- https://syncsity.com/sitemap.html

### Product (PHP, auth-aware)
- https://syncsity.com/assess — conversational diagnostic
- https://syncsity.com/auth/login — magic-link request
- https://syncsity.com/dashboard — user reports

### Machine-readable
- https://syncsity.com/sitemap.xml — search engines
- https://syncsity.com/robots.txt — bot rules + AI whitelist
- https://syncsity.com/llms.txt — AI engine summary
- https://syncsity.com/humans.txt — credits

---

## 9. Glossary

| Term | What it means here |
|------|---------------------|
| **SPA** | The original Lovable React build, compiled to `assets/index-Cvwr8-XU.js`. Renders most marketing pages. |
| **Static page** | A hand-written `.html` file that uses `static-page.css`. |
| **Aha! Assessment** | The 21-question conversational diagnostic at `/assess`. |
| **Revenue Intelligence Report** | The AI-written output of the assessment. |
| **Strategy Session** | The £950 paid 30-minute follow-up call. Booked via Calendly. |
| **Magic link** | One-click login URL emailed to the user. |
| **Engine** | The 3-stage OpenRouter pipeline: research → analyse → write. |
| **Persona stack** | The mental-model authors the prompts channel: Goldratt, Musk, Munger, Sutherland, Hormozi, Naval, Robbins (vision section only). |
| **GEO** | Generative Engine Optimisation — being cited by ChatGPT, Claude, Perplexity, etc. |

---

## 10. How to Use This Document

- **Before starting any work session:** read sections 1, 2, 4 (priority list).
- **When something breaks:** check section 7 (lessons learned).
- **When deploying:** section 6 (runbook).
- **When the user asks "what's done?":** sections 3 + 4.
- **When updating this doc:** keep it tight, dated, scannable. If a section grows past two screens, split it.

This file replaces the older `REQUIREMENTS.md` as the master tracker. `REQUIREMENTS.md` remains for historical reference but new work should update **this** file.

---

## 11. Lessons Learned (do NOT repeat)

These are bugs and mistakes that cost the user multiple round-trips. Internalise before starting work. The full version with reproduction details is in `HANDOVER.md` section 3.

### 11.1 Theme awareness — check FIRST
- **Home page is DARK theme** (navy bg + white text + dark glass nav)
- **Inner pages are LIGHT theme** (white bg + navy text + white nav)
- Default ≠ dark. Before drafting any new page, look at every screenshot the user has sent for that page family and decide light-vs-dark theme as a **foundational** call. Light pages need `<body class="page--light">`.

### 11.2 The "hand-holding" pattern
The user has flagged this twice ("you need to figure it out before presenting" / "so much hand holding :)"). The pattern: I default to a guess, the user has to spell out the missing detail (hero video URL, real Unsplash photos, light theme, etc.). The fix is exhaustive discovery BEFORE writing code:

1. `grep -oE '"https://[^"]+"' assets/index-Cvwr8-XU.js` for all external URLs
2. `grep -oE 'url\([^)]+\)' assets/index-Di5VW5bt.css` for all CSS background-image URLs
3. Re-read every screenshot the user has sent for the page area
4. State explicitly what's missing before substituting

### 11.3 CSS selector traps
- `.nav__links a` was matching dropdown card anchors too — fixed with `.nav__links > a, .nav__has-menu > a` direct-child selectors.
- `.voice-step` referenced in animation rule but was renamed to `.how-step` — orphan classes break silently. Always grep before commit.

### 11.4 Dropdown overflow
Centring a wide dropdown panel under a leftmost or rightmost trigger pushes it off-screen. Per-menu anchor: `Transform → left:0`, `Solutions → centred`, `Resources → right:0`. Plus hard `max-width: calc(100vw - 32px)` cap.

### 11.5 Mobile drawer leak
`nav-mobile.js` previously cloned the entire `.nav__has-menu` wrapper including the hidden hover panel. On touch, `:hover`/`:focus-within` rendered the panel inline. Rewrite walks only direct children and extracts sub-items via `extractSubItems(menuEl)`. Plus defensive `!important` CSS in both stylesheets and inline-style fallback in JS.

### 11.6 Heading hierarchy
- One `<h1>` per page (was: hero h1 + About h1 — splits keyword authority)
- No duplicate section headings (was: two "Clients We've Worked With" — case studies + logo marquee — renamed marquee to "Trusted by Ambitious Companies")

### 11.7 Caches
Multiple cache layers — browser, Apache 30-day for `/assets/*`, CDN. After a CSS/JS change, the user needs hard refresh (Ctrl+F5). Server needs `git pull` — without that, screenshots show old commit's output.

### 11.8 Destructive shell commands
The early `rm -rf public_html` wiped a WordPress install. Never propose destructive shell ops without an `mv` alternative. Never force-push, hard-reset, or `--no-verify` without explicit user confirmation. Auto mode is **not** a license for this.

---

## 12. Migration Recipe (proven)

To migrate any SPA route or static page:

### Step 1 — Pull all bundle content
```bash
grep -oE '"[A-Z][^"]{15,300}"' assets/index-Cvwr8-XU.js | sort -u > /tmp/strings.txt
grep -oE '"https://[^"]+"' assets/index-Cvwr8-XU.js | sort -u > /tmp/urls.txt
grep -oE 'url\([^)]+\)' assets/index-Di5VW5bt.css | sort -u > /tmp/cssurls.txt
```
Then narrow to your page topic with `grep -i 'voice|call cent|...'`.

### Step 2 — Theme decision (DARK or LIGHT)
Re-read all user screenshots for this area. Get this right BEFORE writing code.

### Step 3 — Copy the closest template
- Light inner page: `solutions/voice-solutions.php`
- Dark prose page: `terms.php`

### Step 4 — Set page variables at top
```php
$page_path_prefix = '/';        // or '../' for nested
$page_title       = 'Title | Syncsity';
$page_description = '...';
$page_canonical   = 'https://syncsity.com/...';
$page_breadcrumb  = [['Home','...'], [...]];
$page_extra_jsonld = '...';
```

### Step 5 — Body content
Keep `<?php include site-nav.php ?>`, the `<main>`, then `<?php include site-footer.php ?>`. Page-specific JS BEFORE the footer include (footer closes `</body></html>`).

### Step 6 — `.htaccess`
SPA routes already covered by the `^(auth|assess|dashboard|solutions|transform)/...` rule. Brand-new paths need a new rewrite.

### Step 7 — Pre-commit audit
- Single `<h1>` per page
- All images have width/height
- No orphan classes in animation/transition rules
- `display: none !important` on `.nav__drawer` defensive rule still present
- All section bgs match chosen theme
- `prefers-reduced-motion` covers all hovers
- `body.page--light` if light theme

### Step 8 — Commit + push
Detailed multi-paragraph commit message (see `git log --pretty=full` for examples). Push immediately. Tell user to pull on server.

---

## 13. Pages Pending (master backlog)

### SPA solution routes (8 remaining)
- [ ] `/solutions/lead-generation` — "AI Sales System"
- [ ] `/solutions/process-optimization` — "Process Automation"
- [ ] `/solutions/workforce-transformation` — "Workforce Intelligence"
- [ ] `/solutions/operational-diagnostics`
- [ ] `/solutions/advanced-ai-automation`
- [ ] `/solutions/enterprise-ai-strategy`
- [ ] `/solutions/human-ai-collaboration`
- [ ] `/solutions/audience-intelligence`

### SPA transform routes (3 remaining)
- [ ] `/transform/market-domination`
- [ ] `/transform/revenue-acceleration`
- [ ] `/transform/operational-supremacy`

### Top-level SPA routes
- [ ] `/why-syncsity`
- [ ] `/pricing` (general — different from per-solution pricing)
- [ ] `/about-us`
- [ ] `/calculators`
- [ ] `/` (promote `/syncdemo/` to root)

### Done
- [x] `/solutions/voice-solutions` — first SPA migration, LIGHT theme, all 9 sections (hero / capabilities / features / pricing / implementation / how-it-works / comparison / FAQ / CTA)

— Edward Hadome / Syncsity / handover updated 2026-05-04
