# Syncsity — Project Tracker

> The single source of truth for everything we set out to build, what's done,
> what's pending, and how it all hangs together. Read this before any new work.
>
> **Last updated:** 2026-05-01 · **Status:** Live · **Repo:** `bigchain/syncsity20206`

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

### 3.2 New static pages (hand-written, match SPA nav)

| Page | URL | Status | Notes |
|------|-----|--------|-------|
| Contact | `/contact.html` | ✅ | 4-step smart wizard, posts to `/api/contact` |
| Demo | `/demo.html` | ✅ | Form posts to `/api/contact` (subject: Demo Request) |
| Book a Session | `/booking.html` | ✅ | Calendly iframe |
| Terms | `/terms.html` | ✅ | Long-form prose |
| Privacy | `/privacy.html` | ✅ | UK GDPR-aligned |
| Sitemap (HTML) | `/sitemap.html` | ✅ | Human-readable sitemap |
| Insights / Blog | `/blog` | ✅ | Manifesto essay (~1000 words, dated 15 April 2026) |
| Client login (legacy) | `/client-login.html` | ✅ | Redirect to `/auth/login` |

All 7 share the same SPA-matching nav: **Transform · Solutions · Diagnose · Why Syncsity · Pricing · Resources** + **Log in · Free assessment · Book a Session**.

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

— Edward Hadome / Syncsity / 2026-05-01
