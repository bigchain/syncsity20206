# Syncsity — Master Requirements & Memory

> Persistent record of every requirement the user has stated. Updated whenever
> something new is asked or completed. Read this before doing any new work.

**Last updated:** 2026-05-01
**Status legend:** ✅ done · 🟡 partial · ❌ not started · 🔴 broken

---

## 1. Site architecture

| # | Requirement | Status |
|---|-------------|--------|
| 1.1 | Replace WordPress / Lovable with PHP + MySQL on HostFluid (no n8n) | ✅ |
| 1.2 | Keep the original Lovable design as the marketing front | ✅ (SPA serves it) |
| 1.3 | New product surface (`/assess`, `/auth`, `/dashboard`, `/api`) on PHP | ✅ |
| 1.4 | Hero email capture redirects to `/assess` | ✅ (JS bundle patched) |
| 1.5 | Resources menu links to `/blog` (not `blog.syncsity.com`) | ✅ (bundle patched) |
| 1.6 | All static `.html` pages must have **the same nav and footer** as the SPA | 🟡 (contact rebuilt; demo/booking/terms/privacy/sitemap need same nav structure verified) |
| 1.7 | All buttons readable across all pages (no white-on-white) | 🟡 (`spa-fix.css` v2 surgical) |
| 1.8 | Replace Lovable favicon with our brand | ✅ (`/assets/img/favicon.svg`) |

---

## 2. The Aha! Assessment & Report engine

| # | Requirement | Status |
|---|-------------|--------|
| 2.1 | 21-question conversational form, branching, autosave | ✅ |
| 2.2 | Magic-link login (no passwords) — FundCollective pattern | ✅ |
| 2.3 | AI-written Revenue Intelligence Report — fully written, not guessing | ✅ confirmed working |
| 2.4 | OpenRouter, top-tier model | ✅ Claude Sonnet 4.5 + Gemini 2.5 Pro fallback |
| 2.5 | Persona stack: Goldratt + Munger + Sutherland + Hormozi + Naval + Robbins-vision + Musk | ✅ in prompts |
| 2.6 | Save to MySQL + Google Sheets append | ✅ |
| 2.7 | Send to `edward@syncsity.com` internally | ✅ |
| 2.8 | Quiz/form design must be top-quality | 🟡 functional, could be more premium |

---

## 3. Forms & contact

| # | Requirement | Status |
|---|-------------|--------|
| 3.1 | Forms must work better than WordPress forms | ✅ |
| 3.2 | Contact form must feel "like a whole living space" | 🟡 4-step wizard built, may need more |
| 3.3 | Smart routing: support / sales / partnership / engagement | ✅ (4 routes) |
| 3.4 | Spam-resistant: honeypot, rate limit, CSRF where applicable | ✅ |
| 3.5 | Get in touch should also offer "let me get in touch" alongside "book" | ✅ |
| 3.6 | Posts to `edward@syncsity.com` | ✅ |
| 3.7 | Form should "talk to you" step by step | ✅ (4-step wizard) |
| 3.8 | Confirmation email back to sender | ❌ not yet |

---

## 4. Email follow-up engine

| # | Requirement | Status |
|---|-------------|--------|
| 4.1 | Automatic follow-up sequence (make site "alive") | ✅ |
| 4.2 | 5-step drip: Day 1, 3, 7, 14, 30 | ✅ |
| 4.3 | Idempotent worker, one-click HMAC unsubscribe | ✅ |
| 4.4 | Cron installed on server | ❌ user must add cron |
| 4.5 | Schema applied (`email_log`, `email_optout`) | ❌ user must run schema.sql |

---

## 5. Blog / Insights / SEO / GEO

| # | Requirement | Status |
|---|-------------|--------|
| 5.1 | **Five articles, fully thought through** | 🟡 ONE manifesto written; 4 still pending |
| 5.2 | **Back-dated** so site looks established | 🟡 the manifesto dated 15 April 2026 |
| 5.3 | **Tables, charts, photos** in articles | ❌ not yet — manifesto is text-only |
| 5.4 | Authority-establishing (patterns from data + logic + gaps) | 🟡 manifesto delivers; 4 more needed |
| 5.5 | Full SEO + GEO to max | 🟡 OG tags, JSON-LD Article schema, llms.txt all in place |
| 5.6 | Properly formatted (proper headings, sections) | ✅ for the one essay |
| 5.7 | `/blog` page renders properly | ✅ |
| 5.8 | `llms.txt`, `sitemap.xml`, `robots.txt`, `humans.txt` | ✅ |

### Article topics agreed (need to write 4 more):
1. ✅ "Diagnose first. Or don't bother." (manifesto — published)
2. ❌ "The Founder Bottleneck — three tests anyone can run before lunch"
3. ❌ "AI Voice Operations vs human call centres: 12-month real numbers"
4. ❌ "The Capacity Wall — when 'get more leads' is the worst advice"
5. ❌ "The Hidden Subsidy — 7 ways UK service firms subsidise their worst clients"

Each needs: chart(s), table(s), photo(s), 1500-2500 words, full SEO.

---

## 6. Pages still incomplete or broken

| # | Issue | Status |
|---|-------|--------|
| 6.1 | `/solutions` empty (top-level — no PHP file, hits SPA fallback) | ❌ |
| 6.2 | `/transform` (top-level) — same | ❌ |
| 6.3 | `/calculators` — exists in SPA but content quality unverified | ❓ |
| 6.4 | `/auth/login` and `/dashboard` use my hand-rolled CSS (different feel from SPA) | 🟡 |
| 6.5 | Footer: duplicate columns ("Services" mostly duplicates "Our Solutions") | 🔴 |
| 6.6 | Some footer links point to pages that don't exist (Strategic Consulting, etc.) | 🔴 |
| 6.7 | Missing favicons referenced in `index.html`: `apple-touch-icon.png`, `favicon-16x16.png`, `favicon-32x32.png`, `site.webmanifest` | 🟡 low priority |

---

## 7. The Tailwind problem (acknowledged)

The SPA is compiled Tailwind + shadcn-ui. Every CSS override I write collides with their utility classes. Fixes that work in isolation regress other pages.

**Three honest options to fully resolve this:**
- **Option A:** keep the SPA, surgically patch via JS DOM manipulation (current approach, fragile)
- **Option B:** replace the SPA with hand-written PHP/HTML using one design system I control (4-6 hours of focused work, durable result)
- **Option C:** rebuild the SPA in Next.js so we own the source (longer, cleaner)

User has not picked yet. Default = Option A.

---

## 8. Auth, security, deploy

| # | Requirement | Status |
|---|-------------|--------|
| 8.1 | Magic-link login | ✅ |
| 8.2 | CSRF + rate-limit + honeypot on every form | ✅ |
| 8.3 | `.env` web-blocked + `chmod 600` | ✅ (user must chmod on server) |
| 8.4 | DB password rotated post-chat-leak | ❌ user pending |
| 8.5 | OpenRouter key rotated post-chat-leak | ❌ user pending |
| 8.6 | Cron for `report-generate` | ✅ |
| 8.7 | Cron for `email-worker` | ❌ user pending |

---

## 9. User preferences captured

- Wants pages to be IDENTICAL design (header/footer same on every page)
- Doesn't want WordPress
- Doesn't want n8n
- Wants the "old design" but on "new tech"
- Wants AUTOMATIC follow-up
- Wants smart, intelligent forms (not dumb)
- Frustrated by:
  - Visual divergence
  - Broken buttons
  - Duplicates
  - Empty pages
  - Pages that "make no sense"
- Wants thinking like a chess player (multi-step, hold layers)
- Wants articles with charts/tables/photos
- Wants Edward's voice in everything
- Doesn't want LinkedIn pablum

---

## 10. Things I keep getting wrong (to avoid)

- **Going too broad in CSS overrides** (e.g., `[class*="bg-white"]`) — collateral damage on nav
- **Adding placeholder content that looks fake** (e.g., "PE / SEC / KEW" boxes that look like broken images)
- **Not finishing what I started** (5 articles asked for, 1 written)
- **Reverting and re-reverting** instead of just iterating
- **Wholesale rebuilds at 2am** instead of surgical fixes
- **Not testing in a real browser** (I can't, but I can be more careful in code)

---

## 11. Next-up priority list (locked)

In order:

1. **Fix footer duplicates** (this commit)
2. **Add confirmation email when contact form submitted** (4-hour task)
3. **Write articles 2 + 3** with charts/tables (Founder Bottleneck + AI Voice numbers)
4. **Create `/solutions` and `/transform` overview pages** so they're not empty
5. **Restyle `/auth/login` + `/dashboard`** to match SPA design
6. **Write articles 4 + 5**

Items 7+: TBD based on user direction.
