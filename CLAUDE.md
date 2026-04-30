# Agent Instructions — Syncsity

> **Read this entire file before writing any code.**
> These rules exist because something broke (or will break) without them.

---

## Project Overview

**Syncsity** is an AI business-transformation studio. The product surface is two things:

1. A **marketing site** (syncsity.com) that converts cold traffic into qualified strategy calls.
2. An **AI Aha! Moment Assessment** — a conversational form that produces a free, deeply personalised "Revenue Intelligence Report" (≈£10K+ hidden constraint, exactly how to fix it). The report is the lead magnet *and* the qualifier.

The flow is:
**Hero email capture → /assess (conversational form) → magic-link login → AI generates the report → user reads report on dashboard → upsell to £950 Strategy Session.**

No Wordpress. No n8n. No Lovable. No SaaS form builders. Just PHP + MySQL on HostFluid (cPanel) + OpenRouter for the AI.

**Stack:**
- **Frontend:** Static HTML, vanilla CSS (CSS vars + light/dark), vanilla JS. No framework.
- **Backend:** PHP 8.2 (cPanel ea-php82), MySQL via PDO, PHPMailer over Exim.
- **AI:** OpenRouter — primary `google/gemini-2.0-flash-001`, fallback `anthropic/claude-3.5-haiku`.
- **Persistence:** MySQL primary; Google Sheets append for human-readable lead trail.
- **Hosting:** HostFluid Hetzner EX63, see `HostFluid_Server_Context (1).md`.

---

## The Anchor Sentence

> **In 15 minutes we uncover the hidden constraint costing you £10K+ a month and tell you exactly how to fix it — even if you never speak to us again.**

Every page, every email, every report paragraph serves this sentence. If copy drifts, cut the drift.

## The Governing Rule

> **The form is not a quiz. It is a diagnostic.**
> Every question must earn the right to be asked. Every answer must visibly tighten the report. If a question doesn't change what we say back, cut it.

---

## Goals, Ranked

1. **Aha-moment delivery first.** The user must feel "they actually understood my business" within 30 seconds of opening the report. Without that, nothing else works.
2. **Qualification second.** The report should naturally separate businesses we can help (£1M–£250M revenue, ops-heavy, growth-blocked) from those we can't.
3. **Strategy-session bookings third.** Money happens after trust. The report's last section is the upsell, not the first.

If a feature helps #1 and doesn't hurt #2 and #3, it's in. Otherwise out.

---

## Architecture (Locked)

- **Server-rendered PHP pages** — no SPA, no client-side router. Each route is a `.php` file or a directory.
- **CSS variables drive the theme** (`--bg`, `--surface`, `--gold`, `--blue`, etc.). Light mode is `[data-theme="light"]` on `<html>`. Toggle stored in `localStorage`.
- **All form posts go through CSRF + rate limit + input clean.** No exceptions.
- **Magic-link auth only.** No passwords, ever. 64-hex token, 15-min expiry, single-use.
- **AI calls happen async** — submit → DB row → background queue → user sees "report being prepared" page → email when done.
- **Never log PII raw.** Email addresses are stored, IPs are SHA256-hashed with `SESSION_SECRET`.
- **Service-account JWT for Google Sheets** (no Composer). See `lib/jwt.php`, `lib/sheets.php`.

---

## Directory Layout

```
syncsity/
├── CLAUDE.md                        # You are here
├── DEPLOY.md                        # Deploy steps for HostFluid cPanel
├── .htaccess                        # URL routing + security headers
├── .env.example                     # Config template (real .env is gitignored)
│
├── index.php                        # Home — hero + email capture
├── why-us.php, services.php, pricing.php, about.php
├── contact.php, privacy.php, terms.php, 404.php
│
├── assess/                          # The conversational diagnostic
│   ├── index.php                    # Form (one question per screen)
│   ├── processing.php               # "Generating your report" page
│   └── report.php                   # Report viewer (auth-gated)
│
├── auth/
│   └── login.php                    # Email entry — POSTs to /api/magic-link
│
├── dashboard/
│   └── index.php                    # User's assessments + reports (auth-gated)
│
├── api/                             # All POST endpoints + auth verify
│   ├── magic-link.php               # Generate + email login token
│   ├── auth.php                     # GET ?token=... — validate & start session
│   ├── assess-submit.php            # Save assessment, queue report
│   ├── report-generate.php          # CLI worker — calls OpenRouter, writes report
│   ├── contact.php                  # Contact form → email + sheet
│   └── logout.php
│
├── lib/                             # Server-side core
│   ├── config.php, db.php, mailer.php
│   ├── functions.php, csrf.php
│   ├── openrouter.php, sheets.php, jwt.php
│   └── email_renderer.php
│
├── partials/                        # Shared HTML chunks
│   ├── head.php, header.php, footer.php, theme-toggle.php
│
├── emails/                          # Transactional templates
│   ├── layout.php, magic_link.php, report_ready.php, contact_relay.php
│
├── prompts/                         # OpenRouter prompts (research → analyze → write)
│   ├── research.txt, analyze.txt, report.txt
│
├── assets/
│   ├── css/{core,components,pages}.css
│   ├── js/{core,assess}.js
│   └── img/{logo.svg, favicon.svg, og-image.png}
│
├── database/
│   └── schema.sql                   # MySQL DDL
│
├── storage/                         # gitignored — runtime
│   ├── .htaccess (Deny all), logs/, sessions/, cache/
│
├── llms.txt, sitemap.xml, robots.txt, humans.txt
└── HostFluid_Server_Context (1).md  # Server context (ambient)
```

---

## V1 Scope (Locked — do not deviate without explicit approval)

- Marketing pages: home, why-us, services, pricing, about, contact, privacy, terms.
- Conversational assessment with branching, progress bar, autosave to localStorage, server-side resume.
- Magic-link authentication.
- AI-generated Revenue Intelligence Report (research → analyse → write, 3 LLM calls).
- User dashboard listing their reports.
- Contact form → relay to `edward@syncsity.com` + Google Sheet append.
- Light/dark theme on every page.
- AI/SEO: schema.org, OpenGraph, sitemap.xml, robots.txt, llms.txt, JSON-LD service catalog.

## V1 Non-Goals (Locked — do not build)

- ❌ No payments / Stripe in V1. Strategy-session booking is a Calendly link off the report.
- ❌ No multi-tenant accounts. One user = one email = one set of reports.
- ❌ No file uploads.
- ❌ No live chat widget.
- ❌ No native app.

If tempted by any of these, flag for V2.

---

## Coding Conventions

- **PHP:** strict types (`declare(strict_types=1);`), 4-space indent, `snake_case` functions, `PascalCase` classes.
- **HTML/CSS/JS:** 2-space indent, kebab-case file names, double quotes in HTML, single in JS.
- **Always escape output** with `htmlspecialchars()` / `e()` for HTML, `json_encode` with `JSON_HEX_*` flags for JS.
- **Always use prepared statements** — never string interpolate user input into SQL.
- **No silent catches** — log to `storage/logs/` with context.
- **Errors visible to users** must be human, never expose stack traces.
- **CSS:** prefer custom properties over Tailwind / framework classes. One stylesheet per concern (`core.css`, `components.css`, `pages.css`).

## Mandatory Rules (Never Break)

- Never hardcode secrets. All credentials in `.env`. `.env` never committed.
- Never expose internal error messages.
- Every form has CSRF + rate limit + honeypot.
- Every redirect is validated against an allow-list of relative paths.
- Magic-link tokens are 256-bit, single-use, expire in 15 minutes, stored hashed-equivalent (constant-time compare).
- Sessions: HttpOnly, Secure (prod), SameSite=Lax, regenerated on auth.
- All email FROM addresses are `@syncsity.com` and SPF/DKIM-aligned.
- Rate limit every write endpoint.
- Never log raw IP, raw token, raw email body.

## Patterns We Avoid

- Modifying code unrelated to the current task.
- Refactoring working code unless explicitly asked.
- Adding dependencies (Composer) without asking — current build runs zero-deps.
- Changing test expectations to make tests pass — fix the implementation.
- Inline styles or hardcoded hex colours in templates — use CSS vars.
- `console.log` / `var_dump` / `print_r` in production output — use the logger.

---

## Session Protocol (Do This Before ANY Work)

1. **Read** this file end to end.
2. **Read** `DEPLOY.md` if the task touches deploy / hosting.
3. **Confirm** in chat: *"Instructions read. Working on: [task]. Approach: [brief plan]."*
4. **Ask 3–5 hard questions** before starting:
    - What breaks if I change this?
    - Is there existing code that already does this?
    - Does this clear the aha-moment / qualification / booking filter?
    - Does this touch a locked V1 decision?
    - What's the rollback plan if this goes wrong?
5. **Plan** before coding — show approach before writing code.

## How to Operate

**1. Search before creating.** Before building anything new, grep for it. Reuse beats rewrite.
**2. Read before writing.** Before writing new code, read the 3 most similar existing files. Match their patterns exactly.
**3. Trace before edit.** READ → TRACE → LIST → FIX → VERIFY → TEST.
**4. Max 2 files per change.** No cascading fallbacks beyond 4 paths. If you can't trace the chain, read more first.
**5. Test in real browser.** Real PHP, real DB, real magic link.

---

## The Self-Improvement Loop

Every failure makes the system stronger:

1. Identify what broke.
2. Fix the code.
3. Verify the fix.
4. **Update CLAUDE.md or the relevant doc.** Don't silently absorb lessons.
5. Move on with a stronger system.

---

## Reference Files

- [DEPLOY.md](DEPLOY.md) — cPanel + DB + env setup
- [HostFluid_Server_Context (1).md](HostFluid_Server_Context%20%281%29.md) — server environment
- [database/schema.sql](database/schema.sql) — MySQL DDL
- [prompts/](prompts/) — OpenRouter prompts
- [.env.example](.env.example) — config template

---

## Bottom Line

Search before creating. Read before writing. Trace before editing. Test before committing. Ask hard questions before starting.

The marketing site converts. The form diagnoses. The report wow's. The booking earns. Everything else is decoration.
