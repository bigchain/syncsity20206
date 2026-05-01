# Agent Instructions — Syncsity

> **Read this entire file before writing any code.**
> Last major update: 2026-05-01 — strategy pivoted to hand-written HTML/CSS, no Tailwind, no SPA.

---

## Project Overview

**Syncsity** is an AI business-transformation studio. Two product surfaces:

1. A **marketing site** at `syncsity.com` that converts cold traffic into qualified strategy-call bookings.
2. An **AI Aha! Assessment** — a conversational form that produces a free, deeply personalised "Revenue Intelligence Report".

The flow:
**Hero email capture → /assess (conversational form) → magic-link login → AI generates the report → user reads report on dashboard → optional £950 Strategy Session.**

Backend: PHP 8.2 + MySQL on HostFluid (cPanel). OpenRouter for LLM. PHPMailer over Exim. Service-account JWT for Google Sheets. **No Tailwind. No React SPA. No Composer for the core (PHPMailer optional).**

---

## ⚠️ Strategy Pivot (2026-05-01)

After multiple rounds of Tailwind + shadcn + compiled-React fighting, the user and I agreed:

> **The Lovable React SPA is being replaced with hand-written HTML/CSS/PHP, page by page, one commit per page, verified before the next page starts.**

### Why
The compiled SPA's Tailwind utilities collide with every CSS override I write. Fixes that work in isolation regress other pages. Fragile.

### What this means in practice
- The marketing pages are **plain HTML files** (or PHP for dynamic ones), styled with **`/assets/css/static-page.css`** — the one design system we control.
- **No Tailwind. No shadcn. No JSX.** Everything renders server-side or in plain JS.
- The shared `nav` and `footer` HTML lives in **two canonical blocks** (see `partials/_nav.html` and `partials/_footer.html` once created). Every page paste-includes them or PHP-includes them.
- The original SPA stays parked in `old-lovable-build/` for reference — never deleted.
- **Page-by-page rebuild order:** home → why-us → pricing → solutions (overview + 4 sub) → transform (overview + 3 sub) → about-us → calculators → blog. Each one its own commit, user verifies before next.

### Pages already done in pure HTML/CSS (no Tailwind)
- `/contact.html` — 4-step conversational wizard
- `/demo.html`
- `/booking.html`
- `/terms.html`
- `/privacy.html`
- `/sitemap.html`
- `/client-login.html` (redirect)
- `/blog/index.php` — manifesto essay
- `/auth/login.php`, `/dashboard/index.php`, `/assess/*` (own design system, slightly different)

### Pages still SPA-rendered (to be rebuilt next)
- `/` (home)
- `/why-us`, `/why-syncsity`
- `/pricing`
- `/solutions/*`
- `/transform/*`
- `/about-us`
- `/calculators`

---

## The Anchor Sentence

> **In 15 minutes we uncover the hidden constraint costing you £10K+ a month and tell you exactly how to fix it — even if you never speak to us again.**

Every page, every email, every report paragraph serves this sentence.

---

## The Governing Rules (do not break)

### Rule 1 — One page per commit, verified before the next
The user has been frustrated by wholesale rebuilds at 2am that broke working pages. Going forward:
- Each page rebuild = its own commit
- Commit message names exactly which page
- Push, wait for user verification, then move to the next
- No "let me also fix X while I'm here"

### Rule 2 — Surgical CSS only
Targeting must be by exact class names or specific selectors. **NEVER use `[class*="bg-white"]` or other broad partial-match wildcards.** They cause collateral damage on the SPA's Tailwind classes.

### Rule 3 — Don't add placeholder content that looks fake
The "PE / SEC / KEW" navy boxes looked like broken images. Either real content or visibly intentional minimalism, never "almost looks real".

### Rule 4 — Design system = `/assets/css/static-page.css`
This is the canonical design system for new pure-HTML pages. CSS variables for tokens. Components defined: `.nav`, `.btn`, `.card`, `.field`, `.flash`, `.footer`, `.bg-stage`, `.eyebrow`, `.lead`. Add to it; don't fork it.

### Rule 5 — Shared nav and footer
Every static HTML page MUST have:
- The same nav: Transform / Solutions / Diagnose / Why Syncsity / Pricing / Resources + Log in (or Dashboard if logged in) + Free assessment
- The same footer: 4 columns (Logo+tagline+social, Quick Links, Our Solutions, Services)
- Same favicons: `/assets/img/favicon.svg` + `/favicon.ico` fallback

### Rule 6 — Trust the user's screenshots
When the user sends a screenshot of a bug, that screenshot IS the source of truth. Don't argue. Fix it.

### Rule 7 — Auto mode means execute, not interrupt
Make reasonable assumptions, push, wait for course correction. Don't ask for permission on routine decisions.

### Rule 8 — Update REQUIREMENTS.md whenever something is asked or completed
The persistent record. Stops me from forgetting things.

---

## Architecture (Locked)

| Layer | Tech |
|-------|------|
| Marketing pages | Plain HTML + CSS (no Tailwind), or PHP where dynamic |
| Product surfaces (`/assess`, `/auth`, `/dashboard`) | PHP 8.2 + MySQL |
| `/api/*` endpoints | PHP, JSON-out, CSRF + rate-limit + honeypot |
| AI engine | OpenRouter — `anthropic/claude-sonnet-4.5` primary, `google/gemini-2.5-pro` fallback |
| Email | PHPMailer over Exim (port 25, no auth — FundCollective pattern) |
| Persistence | MySQL primary, Google Sheets append best-effort |
| Auth | Magic-link (no passwords). 64-hex token, 15-min expiry, single-use |
| Background jobs | Fire-and-forget self-curl + cron backstop (`shell_exec` is disabled on HostFluid) |

---

## Directory Layout

```
syncsity/
├── CLAUDE.md                    # this file — operational protocol
├── REQUIREMENTS.md              # everything the user has asked for
├── BUILD_LOG.md                 # change log
├── DEPLOY.md                    # deploy guide
├── deploy.sh                    # one-line server deploy
├── .htaccess                    # routing + security headers
├── .env / .env.example          # config
├── .gitignore
│
├── index.html                   # SPA loader (TO BE REPLACED with index.php)
├── contact.html, demo.html, booking.html, terms.html, privacy.html, sitemap.html, client-login.html
│                                # all hand-written HTML using /assets/css/static-page.css
│
├── assess/                      # the conversational diagnostic
│   ├── index.php (form)
│   ├── processing.php
│   └── report.php
├── auth/
│   ├── login.php (magic-link)
│   └── index.php (redirect to login)
├── dashboard/
│   └── index.php
├── blog/
│   └── index.php (manifesto + future essays)
│
├── api/                         # PHP backend
│   ├── magic-link.php, auth.php, logout.php
│   ├── assess-submit.php, report-generate.php, report-status.php
│   ├── contact.php
│   ├── email-worker.php (cron-driven follow-up)
│   └── unsubscribe.php
│
├── lib/                         # PHP core
│   ├── config.php, db.php, csrf.php, mailer.php
│   ├── functions.php, email_renderer.php
│   ├── openrouter.php, sheets.php, jwt.php, md.php
│
├── partials/                    # PHP includes for /assess, /auth, /dashboard
│   ├── head.php, header.php, footer.php
│
├── emails/                      # transactional templates
│   ├── layout.php, magic_link.php, report_ready.php, contact_relay.php
│   ├── followup_day{1,3,7,14,30}.php
│
├── prompts/                     # OpenRouter prompts
│   ├── research.txt, analyze.txt, report.txt
│
├── assets/
│   ├── css/
│   │   ├── static-page.css      # ← canonical design system for hand-written pages
│   │   ├── core.css, components.css, pages.css   (used by /assess, /auth, /dashboard)
│   │   └── spa-fix.css          # surgical SPA overrides (will be deprecated as SPA goes away)
│   ├── js/
│   │   ├── core.js, assess.js
│   │   ├── spa-shim.js          # injects nav buttons, fixes SPA bugs
│   │   └── static-shell.js      # nav/footer injector for static .html (optional)
│   └── img/
│       ├── logo.svg, favicon.svg, og-image.svg
│       └── clients/*.svg        (placeholders)
│
├── lovable-uploads/             # original Syncsity logo PNGs
├── database/schema.sql
├── storage/                     # gitignored runtime
│   ├── sessions/, logs/, cache/
│
├── llms.txt, sitemap.xml, robots.txt, humans.txt
└── old-lovable-build/           # parked SPA + earlier iterations (never deployed)
```

---

## V1 Scope (Locked)

- Marketing pages (rebuilt one at a time): home, why-us, pricing, services, about, contact, privacy, terms, blog
- Conversational assess at `/assess` — 21 questions, branching, autosave, server-resume
- Magic-link auth
- AI Revenue Intelligence Report (research → analyse → write, OpenRouter)
- User dashboard
- Contact form → relay to `edward@syncsity.com` + Google Sheets append
- 5-step follow-up email sequence (Day 1 / 3 / 7 / 14 / 30)
- AI/SEO: schema.org, OpenGraph, sitemap.xml, robots.txt, llms.txt

## V1 Non-Goals

- No Stripe / payments. Strategy Session is a Calendly link.
- No multi-tenant accounts. One user = one email = one set of reports.
- No file uploads.
- No live chat widget.
- No native app.

---

## Coding Conventions

- PHP: `declare(strict_types=1);`, 4-space indent, snake_case functions, PascalCase classes.
- HTML/CSS/JS: 2-space indent, kebab-case file names, double quotes in HTML, single in JS.
- Always escape output (`htmlspecialchars` / `e()` for HTML; `JSON_HEX_*` flags for JS).
- Always use prepared statements; never string-interpolate user input into SQL.
- No silent catches; log to `storage/logs/`.
- CSS: prefer custom properties over framework classes. Add to `static-page.css`; don't fork.

## Mandatory Rules (Never Break)

- Never hardcode secrets. All credentials in `.env`.
- Never expose internal error messages.
- Every form has CSRF + rate limit + honeypot (CSRF optional for static-html forms; tighten rate limit when missing).
- Magic-link tokens: 256-bit, single-use, 15-min expiry, constant-time compare.
- Sessions: HttpOnly, Secure (prod), SameSite=Lax.
- Rate-limit every write endpoint.
- Never log raw IP, raw token, raw email body.

## Patterns We Avoid (lessons learned)

- Modifying code unrelated to the current task.
- Refactoring working code unless explicitly asked.
- Wholesale rebuilds at 2am.
- Reverting commits then immediately re-reverting.
- Adding placeholder content that looks fake.
- Broad CSS wildcards (`[class*="..."]`).
- Inline `style=` for things the design system already covers.

---

## Session Protocol (Do This Before ANY Work)

1. **Read** this file end to end.
2. **Read** `REQUIREMENTS.md`.
3. **Confirm** in chat: *"Instructions read. Working on: [page X]. Approach: [brief]."*
4. **Ask** if unclear. Otherwise proceed.
5. **One page per commit.** Push. Wait.

---

## How to Operate

**1. Search before creating.** Grep first.
**2. Read before writing.** Read 3 similar existing files; match their patterns.
**3. Trace before editing.** READ → TRACE → LIST → FIX → VERIFY.
**4. Max 2 files per change** unless explicitly a multi-file feature.
**5. Test in real browser** when possible (user does this; I rely on screenshots).

---

## Self-Improvement Loop

Every failure makes the protocol stronger:

1. Identify what broke
2. Fix it
3. Update `CLAUDE.md` or `REQUIREMENTS.md` with the lesson
4. Move on with a stronger system

---

## Bottom Line

Hand-written HTML/CSS, no Tailwind, no SPA. One page per commit. Verify before next. Surgical, not wholesale. The `/assess` engine and AI report are the moat — protect them. The marketing pages exist to deliver visitors to `/assess` cleanly.
