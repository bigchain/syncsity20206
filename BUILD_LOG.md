# Syncsity Build Log

> Persistent tracker for what's been built, what's pending, and any security/operational decisions that should outlive a chat session.

**Started:** 2026-04-30
**Build target:** cPanel / PHP 8.2 / MySQL on HostFluid Hetzner EX63
**Domain:** syncsity.com

---

## 🔐 Security register

| Item | Status | Action |
|------|--------|--------|
| `.env` created with DB credentials | ✅ | **ROTATE the DB password after deploy** — it travelled through chat. cPanel → MySQL Databases → Change Password. |
| OpenRouter API key in `.env` | ✅ | **ROTATE after deploy** — also travelled through chat. https://openrouter.ai/keys |
| `.env` added to `.gitignore` | ✅ | Confirm before any `git init` / push. |
| `.env` blocked from web by `.htaccess` | ✅ | `<FilesMatch "\.(env\|sql\|md\|...)>` deny all. |
| `/storage/`, `/lib/`, `/prompts/`, `/database/`, `/emails/` blocked from web | ✅ | RewriteRule `^storage/?.*` → `[F,L]` etc. |
| Sessions (HttpOnly + SameSite=Lax + Secure-in-prod) | ✅ | Configured in `lib/config.php`. |
| CSRF (session token + double-submit cookie) | ✅ | `lib/csrf.php`. |
| Magic-link tokens: 256-bit, 15-min, single-use, constant-time compare | ✅ | `send_magic_link()` + `/api/auth.php`. |
| Rate limiting (DB-backed) per email + per IP | ✅ | `rate_limits` table; helpers in `lib/functions.php`. |
| IP hashing (SHA-256 + SESSION_SECRET salt) | ✅ | `hash_ip()`. |
| CSP, X-Frame-Options, HSTS, X-Content-Type-Options | ✅ | `send_security_headers()`. |
| Input sanitisation: `clean()`, `clean_email()`, prepared statements only | ✅ | `lib/db.php` + `lib/config.php`. |
| `chmod 600 .env` on server | ⏳ | **Do this manually after upload.** See `DEPLOY.md`. |
| Service-account JSON for Sheets stored OUTSIDE web root | ⏳ | Place at `/home/<cpanel-user>/credentials/syncsity-sheets.json`, set `chmod 600`, point `GOOGLE_SERVICE_ACCOUNT_JSON` at it. |

---

## ✅ What's built (full inventory)

### Core protocol
- `CLAUDE.md` — Syncsity-specific operational protocol
- `BUILD_LOG.md` — this file

### Server config
- `.htaccess` — gzip, cache headers, security headers, pretty URLs, deny-all on sensitive dirs, 404 fallback
- `.env` — production secrets (gitignored)
- `.env.example` — template
- `.gitignore`

### PHP library (`/lib/`)
- `config.php` — env loader, security headers, CSP, sessions, helpers (`e()`, `clean()`, `clean_email()`, `secure_token()`, `json_response()`, `redirect()`, `safe_redirect_path()`)
- `db.php` — PDO singleton with prepared-statement helpers (`DB::run`, `DB::all`, `DB::one`, `DB::val`, `DB::insert`, `DB::begin/commit/rollback`)
- `csrf.php` — dual session-token + double-submit-cookie CSRF
- `functions.php` — `rate_limit()`, `hash_ip()`, `audit()`, `find_or_create_user()`, `current_user()`, `is_logged_in()`, `require_auth()`, `send_magic_link()`, `flash()/get_flash()`, `time_ago()`
- `mailer.php` — PHPMailer-preferred, `mail()` fallback
- `email_renderer.php` — template loader
- `openrouter.php` — `llm_call()` with retry, fallback model, JSON parsing
- `jwt.php` — pure-PHP RS256 JWT for Google service accounts
- `sheets.php` — Google Sheets append (no Composer)

### Design system (`/assets/`)
- `css/core.css` — tokens, reset, typography, light/dark theme variables
- `css/components.css` — buttons, cards, forms, header/nav, footer, alerts, conversational form, report viewer
- `css/pages.css` — hero, stats, grids, pricing, FAQ, CTA, auth, dashboard
- `js/core.js` — theme toggle, mobile nav, AHA hero email handoff, scroll, reveal-on-scroll
- `js/assess.js` — conversational form engine (text / textarea / single / multi / scale / number / currency / url / email; branching; autosave; keyboard nav; submit)
- `img/logo.svg`, `img/favicon.svg`, `img/og-image.svg` — brand assets

### Partials (`/partials/`)
- `head.php` — full SEO/OG/JSON-LD `<head>` block
- `header.php` — sticky nav, light/dark toggle, mobile menu
- `footer.php` — three-column footer + JS bootstrap

### Marketing pages (root)
- `index.php` — hero + AHA capture + stats + 3-step method + 4-phase process + 4-services + industries + FAQ + CTA
- `why-us.php` — 3 differentiators + comparison table + CTA
- `services.php` — full breakdown of voice / sales / process automation / workforce intelligence
- `pricing.php` — Free Aha! · £950 Strategy Session · From £8K Engagement (no retainers)
- `about.php` — beliefs · standards · base
- `contact.php` — full form with CSRF, honeypot, GDPR consent
- `privacy.php` — UK GDPR + DPA 2018 plain-English
- `terms.php` — plain-English ToS
- `404.php`

### Assessment flow (`/assess/`)
- `index.php` — full 21-question conversational schema with branching (corporate-org branch for 200+; revenue-band aware) and skipIfFilled email prefill
- `processing.php` — TODO
- `report.php` — TODO

### Database (`/database/`)
- `schema.sql` — `users`, `assessments`, `rate_limits`, `audit_log`, `contact_messages` (all utf8mb4, FK constraints, indexed)

### Emails (`/emails/`)
- `layout.php` — Outlook-safe table layout
- `magic_link.php`
- `report_ready.php`
- `contact_relay.php`

---

## 🏛️ Final architecture decision (2026-04-30, late session)

After cycling through approaches, the cleanest and lowest-risk pattern is:

| Layer | Tech | Owns |
|-------|------|------|
| **Marketing front** | Lovable React SPA (the original `dist/`) | `/` and all legacy URLs (`/why-us`, `/services`, `/transform/*`, `/solutions/*`, `/pricing`, `/contact`, etc.). Pixel-perfect design preserved verbatim. |
| **New product surface** | PHP 8.2 + MySQL (our build) | `/assess`, `/auth/*`, `/dashboard`, `/api/*` |

The SPA's hero email capture has been patched at the bundle level so it redirects to **`/assess?email=…`** on the same domain, bridging the original front-end to the new backend.

`.htaccess` routing:
1. `/api/foo` → `/api/foo.php`
2. `/auth/foo`, `/assess/foo`, `/dashboard/foo` → respective `.php` files
3. Everything else not a real file → `/index.html` (SPA bootstraps, React Router handles the path client-side)

`old-lovable-build/php-marketing-v2/` keeps the hand-rolled PHP marketing pages I built — preserved, never deleted, can be revived if Lovable is ever retired.

## ⏳ Outstanding (V1 = complete; the items below are deploy-time tasks)

- [ ] **Deploy.** Run through `DEPLOY.md` end-to-end on the HostFluid server.
- [ ] **Rotate** DB password + OpenRouter key (both leaked through chat).
- [ ] **Smoke test** the magic-link round-trip with a real email.
- [ ] **Smoke test** end-to-end assessment → report on production.
- [ ] **Mobile pass** on a real phone (Chrome iOS + Safari iOS + Chrome Android).
- [ ] **Accessibility pass** — `axe` browser extension pass on home + assess + report.
- [ ] **Calendly setup** at https://calendly.com/syncsity/strategy (or change `CALENDLY_STRATEGY_URL` in `.env` to whatever booking link is correct).

## ✅ V1 done

Every file in the build inventory is in place. The local site is functionally complete pending deploy + secret rotation. See `DEPLOY.md`.

---

## 🧠 Architecture decisions (locked)

| Decision | Choice | Why |
|----------|--------|-----|
| Frontend | Static HTML + vanilla CSS + vanilla JS | No framework lock-in, fastest, easiest to audit. |
| Backend | PHP 8.2 + MySQL via PDO, no Composer | Matches HostFluid cPanel default. Composer-free for zero-dep deploy; Composer optional for PHPMailer. |
| Auth | Magic-link only (no passwords) | FundCollective-proven pattern. 256-bit token, 15-min, single-use. |
| Background jobs | Fire-and-forget self-curl + cron backup (5-min) | `shell_exec` is disabled on HostFluid. |
| AI | OpenRouter, primary `anthropic/claude-sonnet-4.5`, fallback `google/gemini-2.5-pro` | "Advanced brains" requirement. ~$0.15-0.30 per report. |
| Persistence | MySQL primary, Google Sheets append (best-effort, async) | MySQL is canonical; Sheet is for human-readable trail. |
| Theme | CSS variables + `[data-theme=light]` toggle, `localStorage` persisted | No FOUC (bootstrap script in `<head>`). |
| Brand palette | Existing Syncsity navy + blue + orange — no new gold | User explicitly wants design continuity. |

---

## ⚙️ Operational notes

- `assess.js` autosaves to `sessionStorage` (`syncsity-assess`) — survives accidental tab close but not full browser close.
- Hero email captured via `[data-aha-form]` is stashed in `sessionStorage` (`syncsity-prefill-email`) AND passed via `?email=` URL param to `/assess`.
- Rate-limit table is fail-open: if the table is missing, requests are allowed (don't lock everyone out on a deploy hiccup).
- `send_security_headers()` is called automatically by `lib/config.php` on every web request.
- Sessions live in `/storage/sessions/` (mode 0700) — survives cPanel `/tmp` GC.

---

## 🌐 Deploy target (locked)

| Item | Value |
|------|-------|
| Server | HostFluid (Hetzner EX63), 77.42.1.80 |
| cPanel user | `marieatlasco` |
| Web root | `/home/marieatlasco/public_html/` |
| MySQL DB name | `marieatlasco_ehhdyy` |
| MySQL user | `marieatlasco_hey87` |
| Credentials dir (outside web root) | `/home/marieatlasco/credentials/` |
| Git remote | `https://github.com/bigchain/syncsity20206` |

## 🪛 Lessons captured (don't repeat)

- **Git on Windows:** if pushes/fetches fail with SSL, the system-level `C:/Program Files/Git/etc/gitconfig` may force `http.sslBackend = openssl`. Override per-user with:
  ```
  git config --global http.sslBackend schannel
  git config --show-origin --get-all http.sslBackend   # confirm precedence
  ```
- **`shell_exec` is disabled** on HostFluid — use fire-and-forget self-curl + cron backup, never `exec`/`shell_exec`/`proc_open`.
- **Apache `SecRuleUpdateTargetById` in `.htaccess` crashes Apache** (ModSecurity rule load order). Fix false positives at code level instead.
- **`.env` web access**: blocked by `<FilesMatch "\.(env|sql|md|...)>` in `.htaccess`, AND by extra deny rules on `/storage`, `/lib`, `/prompts`, `/database`, `/emails` paths. Defence in depth.
- **Sessions on cPanel**: `/tmp` GC is aggressive. Use app-specific session save path (`/storage/sessions`, mode 0700).

## 📍 Source-of-truth files

- [CLAUDE.md](CLAUDE.md) — operational protocol
- [DEPLOY.md](DEPLOY.md) — deployment steps (TODO)
- [database/schema.sql](database/schema.sql) — DDL
- [.env.example](.env.example) — config template
- [HostFluid_Server_Context (1).md](HostFluid_Server_Context%20%281%29.md) — server environment

---

*Update this file at the end of every working session.*
