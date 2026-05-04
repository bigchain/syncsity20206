# Syncsity — Session Handover

> **For the next agent picking this up.** Read this first. Then `CLAUDE.md`, then `PROJECT_TRACKER.md` if you need the full reference. This doc captures the live state, the lessons learned the hard way, and the recipe for continuing the SPA-rebuild work without re-introducing the same bugs.
>
> **Last handover:** 2026-05-04 · **Latest commit:** `1e121c0` (voice page light theme)

---

## 1. The 60-second context

The user is migrating an existing **Lovable React SPA** marketing site to **hand-written PHP + universal partials**, page by page, so they can:
- Stop fighting Tailwind specificity wars
- Own every line of HTML/CSS
- Get proper SEO + GEO (JSON-LD schema, structured FAQ, etc.) per page
- Have one universal nav + footer that gets edited in one place

The home page lives at `/syncdemo/index.html` (visible demo of the new design).
The first SPA-route migration is `/solutions/voice-solutions`. 8+ more routes to go.

---

## 2. Live state of the codebase

### 2.1 Partials (universal chrome)

| File | Role |
|---|---|
| `partials/site-head.php` | Opens `<html><head>` with all meta + 4 universal JSON-LD schemas (Organization, WebSite, WebPage, BreadcrumbList). Caller sets `$page_*` vars before include. |
| `partials/site-nav.php` | Skip-link + sticky `<header class="nav">` with the 6-item nav and 3 hover dropdowns (Transform 3-card, Solutions 4-card, Resources 2-card) + 3 CTAs. |
| `partials/site-footer.php` | `<footer class="footer-home">` 4-column + bottom row + auth-aware Log-in→Dashboard swap script + nav-mobile.js include + closes `</body></html>`. |

### 2.2 Universal CSS

| File | Role |
|---|---|
| `assets/css/static-page.css` | Original design system (variables, components). Mostly unchanged. Defensive `display: none !important` rules added for `.nav__drawer` + `.nav__burger`. |
| `assets/css/site-nav.css` | Universal nav + dropdowns + skip-link + drawer + footer. Includes `body.page--light` modifier for inner pages. Loaded by `site-head.php`. |

### 2.3 Mobile drawer JS

`assets/js/nav-mobile.js` — accordion drawer. Walks **only direct children** of `.nav__links` (so dropdown sub-cards don't get cloned wholesale). For each `.nav__has-menu` wrapper it extracts sub-items via `extractSubItems(menuEl)` and builds `<button>` + `<div>` accordion sections. Plain links become `.nav__drawer__flat` rows.

Sets inline styles on the drawer element on creation as a defensive fallback against stale CSS cache:
```js
drawer.style.position = 'fixed';
drawer.style.display = 'none';
drawer.style.visibility = 'hidden';
// ...
```

### 2.4 Migrated pages (using partials)

| Page | File | Theme | URL routing |
|---|---|---|---|
| Home demo | `syncdemo/index.html` | Dark hero / mixed | Direct |
| Home demo (php twin) | `syncdemo/index.php` | — | Test harness only |
| Terms | `terms.php` | Dark | `.htaccess` rewrites `/terms.html` and `/terms` |
| Privacy | `privacy.php` | Dark | Same pattern |
| Sitemap | `sitemap.php` | Dark | Same |
| Booking | `booking.php` | Dark | Same |
| Demo | `demo.php` | Dark | Same |
| Contact | `contact.php` | Dark | Same |
| **AI Voice Operations** | `solutions/voice-solutions.php` | **LIGHT** | `.htaccess` routes `/solutions/voice-solutions` to it |

### 2.5 .htaccess routing rules added

```apache
# Migrated legacy URLs serve new .php content
RewriteRule ^terms\.html$    /terms.php    [L]
# ... etc for privacy/sitemap/booking/demo/contact

# Extension-less canonicals
RewriteRule ^(terms|privacy|sitemap|booking|demo|contact)$ /$1.php [L]

# SPA-route migration: /solutions/foo and /transform/foo serve .php if file exists
RewriteRule ^(auth|assess|dashboard|solutions|transform)/([a-zA-Z0-9_-]+)$ /$1/$2.php [L,QSA]
```

When a `/solutions/foo.php` doesn't exist on disk, the SPA fallback (`/index.html`) catches it. So unmigrated routes still render via React.

---

## 3. Lessons from prior sessions (do NOT repeat)

These bugs/mistakes cost the user multiple round-trips. Internalise before starting work.

### 3.1 Theme awareness — check FIRST
- **Home page is DARK theme** (navy + white text + dark glass nav)
- **Inner pages are LIGHT theme** (white bg, navy text, white nav)
- I defaulted to dark for `/solutions/voice-solutions` and the user had to tell me. **Wrong.** Before drafting any inner page, look at every screenshot the user has sent for that page and decide light-vs-dark theme as a foundational call.
- Light pages add `<body class="page--light">` to swap the nav into the white/navy variant.

### 3.2 The "hand-holding" pattern (user's word)
The user has flagged this twice:
> "you need to figure it out before presenting — if it requires me saying every tiny detail then it wont work"
>
> "so much hand holding :)"

What triggered it:
- Cloned home but missed the YouTube video hero (b-lHRWWUpsY) — found via grep on https URLs in bundle, NOT image extensions
- Built case-study cards as solid gradients when live uses Unsplash photos (`photo-1581092795360-fd1ca04f0952`, `photo-1460574283810-2aab119d8511`, `photo-1493397212122-2b85dda8106b`) with colored gradient overlays
- Built voice-solutions in dark theme when live is light

**The fix:** before writing any clone, do exhaustive discovery:
1. `grep -oE '"https://[^"]+"' assets/index-Cvwr8-XU.js` for ALL external URLs (catches videos, Unsplash, CDN images)
2. `grep -oE 'url\([^)]+\)' assets/index-Di5VW5bt.css` for all CSS background-image URLs
3. Check every screenshot the user has sent for that area — re-read messages
4. If still missing critical assets, say so explicitly with the list of places searched, BEFORE shipping a substitute

### 3.3 Mobile drawer leak
nav-mobile.js used to clone the entire `.nav__has-menu` wrapper *including* the hidden hover panel. On touch, a tap fires `:hover`/`:focus-within` and the cloned panel rendered inline in the drawer with a 3-column desktop grid forced into ~70px-wide column → one-letter-per-line gibberish.

**Fixed by**: rewriting `nav-mobile.js` to walk only direct children of `.nav__links` and extract sub-items from `.nav__menu` via a parser, NOT cloning the panel. Plus defensive `!important` CSS in both `static-page.css` and `site-nav.css`. Plus inline-style fallback on the drawer element in JS.

### 3.4 CSS selector bug — `.nav__links a` was too greedy
The original `.nav__links a` selector matched every anchor INSIDE the dropdown panels too. So dropdown card anchors got the 40px-tall pill styling and the whole menu looked jumbled.

**Fixed by**: scoping to direct children only — `.nav__links > a, .nav__has-menu > a { ... }`.

### 3.5 Dropdown overflow off-screen
`Transform` is the leftmost trigger; centering a 1080px panel under it pushed half off-screen left. `Resources` had the inverse problem.

**Fixed by**: per-menu anchor — `Transform` left:0 (extends right), `Solutions` centered, `Resources` right:0 (extends left). Plus hard `max-width: calc(100vw - 32px)` on all panels.

### 3.6 Two-h1-per-page SEO bug
Both the hero and the About section used `<h1>`, splitting keyword authority. Fixed by demoting `About Syncsity` to `<h2>` and `Human-Centric AI for Deeper Business Connections` to `<p class="about__hero-tagline">`.

### 3.7 Duplicate section headings
Two sections both titled "Clients We've Worked With" (case studies + logo marquee) — bad SEO. Renamed marquee to "Trusted by Ambitious Companies".

### 3.8 The early `rm -rf` lesson
Very early on, the user ran `rm -rf public_html` which wiped a WordPress install and its media. Never propose destructive shell operations without an `mv` alternative. Don't suggest force-pushes, hard resets, etc. without explicit confirmation.

---

## 4. Migration recipe (proven)

To migrate any SPA route or static page:

### Step 1 — Pull all bundle content for the page

```bash
# Find page-specific URLs (videos, photos, external CDN)
grep -oE '"https://[^"]+"' assets/index-Cvwr8-XU.js | sort -u

# Find page-specific CSS background-image URLs
grep -oE 'url\([^)]+\)' assets/index-Di5VW5bt.css | sort -u

# Pull all body copy related to your topic
grep -oE '"[A-Z][^"]{15,300}"' assets/index-Cvwr8-XU.js \
  | grep -iE 'YOUR|TOPIC|KEYWORDS' | sort -u

# Pull FAQ Q&A — gold for GEO
grep -oE '"[^"]{20,500}\?"' assets/index-Cvwr8-XU.js | sort -u
```

### Step 2 — Determine theme (DARK or LIGHT)

Look at any screenshot the user sent for this page. Home & syncdemo are DARK. All inner solution / transform / pricing / about / privacy / etc. pages are LIGHT.

### Step 3 — Copy the closest existing template

- Light-theme inner page: copy `solutions/voice-solutions.php`
- Dark-theme prose page: copy `terms.php`
- Light-theme prose page: TBD (none yet — adapt terms.php with `class="page--light"`)

### Step 4 — Update the page-specific block at top

```php
$page_path_prefix = '/';        // or '../' for nested
$page_title       = '...';
$page_description = '...';      // 140-160 chars
$page_canonical   = 'https://syncsity.com/...';
$page_breadcrumb  = [['Home','https://syncsity.com/'], [...]];
$page_extra_jsonld = '...';     // FAQPage / Service / etc.
```

For light-theme pages add `<body class="page--light">` after `include site-head.php`.

### Step 5 — Replace body content

Keep partial includes intact:
```php
<?php include __DIR__ . '/../partials/site-nav.php'; ?>
<main id="main" aria-label="...">
  ...your sections...
</main>
<?php include __DIR__ . '/../partials/site-footer.php'; ?>
```

Page-specific JS goes BEFORE the footer include (footer closes `</body></html>`).

### Step 6 — Add to .htaccess if it's a new path

For SPA routes the existing rule already handles `/solutions/foo` and `/transform/foo` automatically. For brand-new paths, add a rewrite.

### Step 7 — Audit before commit

Run a line-by-line CSS check for:
- Orphan classes referenced in animation/transition rules but not in HTML
- `display: none` on `.nav__drawer` (defensive)
- Single `<h1>` per page
- All images have width/height (CLS prevention)
- All section bgs are consistent with chosen theme
- `prefers-reduced-motion` rules cover all hovers/transitions

### Step 8 — Commit with detailed message

User likes thorough commit messages. Pattern: heading + 3-section body (what / why / how). Examples in `git log`.

---

## 5. Pages still pending

### SPA solution routes (8 remaining)
- [ ] `/solutions/lead-generation` — "AI Sales System" / "Build a Machine That Prints Money"
- [ ] `/solutions/process-optimization` — "Process Automation" / "Eliminate Bottlenecks Forever"
- [ ] `/solutions/workforce-transformation` — "Workforce Intelligence" / "Build Self-Improving Teams"
- [ ] `/solutions/operational-diagnostics`
- [ ] `/solutions/advanced-ai-automation`
- [ ] `/solutions/enterprise-ai-strategy`
- [ ] `/solutions/human-ai-collaboration`
- [ ] `/solutions/audience-intelligence`

### SPA transform routes (3 remaining)
- [ ] `/transform/market-domination` — "Own Your Industry in 18 Months"
- [ ] `/transform/revenue-acceleration` — "10X Growth Without 10X Headcount"
- [ ] `/transform/operational-supremacy` — "Cut Costs 40% While Scaling Infinitely"

### Top-level SPA routes
- [ ] `/why-syncsity`
- [ ] `/pricing` (general — different from per-solution pricing)
- [ ] `/about-us`
- [ ] `/calculators`

### Optional — home page
- [ ] Promote `/syncdemo/` to replace the SPA at `/`

### Skipped intentionally
- `client-login.html` — 14-line meta-refresh redirect; no partials needed
- `/auth/*`, `/assess/*`, `/dashboard/*` — different design system, separate concern

---

## 6. Quick gotchas

| Symptom | Likely cause | Fix |
|---|---|---|
| Dropdown panel cuts off left edge | Centered under leftmost trigger | Use `left: 0` for Transform, `right: 0` for Resources |
| Mobile drawer renders below footer in flow | Stale CSS or cloned hover panel | Check `nav-mobile.js` is latest; defensive `!important` CSS in place |
| Footer shows old PRODUCT/COMPANY/LEGAL columns instead of universal Quick Links | User is hitting legacy `.html` URL but server hasn't pulled yet | `git pull` on server |
| Case study cards show as solid gradient with no photo | User has cached CSS from before commit `07ed337` | Hard refresh (Ctrl+F5) |
| Dropdowns show on mobile and overflow | `.nav__menu` not hidden via `@media (max-width: 879px)` | Belt-and-braces rule already in `site-nav.css` |
| Page builds but PHP errors in browser | Variable not set before include | All `$page_*` vars have `??=` defaults in `site-head.php` so this should not happen |
| Local file:// rendering broken paths | Absolute paths like `/assets/...` don't resolve | The .php pages on server resolve fine; for .html testing use relative `../assets/...` like syncdemo does |

---

## 7. Useful commands

### Pull on server (run after every push)
```bash
sudo -u marieatlasco git -C /home/marieatlasco/public_html pull origin main && systemctl reload ea-php82-php-fpm
```

### Bundle content extraction
```bash
# All quoted strings (for body copy discovery)
grep -oE '"[A-Z][^"]{15,300}"' assets/index-Cvwr8-XU.js | sort -u

# All external URLs (for assets)
grep -oE '"https?://[^"]+"' assets/index-Cvwr8-XU.js | sort -u

# All CSS background-image() URLs
grep -oE 'url\([^)]+\)' assets/index-Di5VW5bt.css | sort -u

# All Syncsity color tokens (in case you need them)
grep -oE -- '--syncsity-[a-z0-9-]+:[^;]+' assets/index-Di5VW5bt.css | sort -u
```

### Color tokens (already cached — use freely)
```
--syncsity-orange:    #FCA311
--syncsity-blue-50:   #E6F0FB
--syncsity-blue-100:  #CCE0F7
--syncsity-blue-200:  #99C2EF
--syncsity-blue-300:  #66A3E7
--syncsity-blue-400:  #3385DF
--syncsity-blue-500:  #0066D7
--syncsity-blue-600:  #0052AC
--syncsity-blue-700:  #003D81
--syncsity-blue-800:  #002956
--syncsity-blue-900:  #00142B
--syncsity-navy:      #14213D
--syncsity-dark-navy: #0A1022
--syncsity-black:     #24314a
--syncsity-accent:    #00B8D9 (teal)
--syncsity-success:   #36B37E (green)
--syncsity-warning:   #FFAB00
--syncsity-error:     #FF5630
```

### Hero YouTube video (home page)
```
https://www.youtube.com/embed/b-lHRWWUpsY?controls=0&autoplay=1&mute=1&loop=1&playlist=b-lHRWWUpsY&showinfo=0&rel=0&modestbranding=1&playsinline=1&enablejsapi=1&vq=hd1080
```

### Case-study Unsplash photos
```
Sec-Curity:    https://images.unsplash.com/photo-1581092795360-fd1ca04f0952
Bankiom:       https://images.unsplash.com/photo-1460574283810-2aab119d8511
Dftw Internals:https://images.unsplash.com/photo-1493397212122-2b85dda8106b
```

---

## 8. Auto mode + the right cadence

The user has auto mode on. They expect:
- Execute immediately, minimal questions
- Make reasonable assumptions on routine decisions
- One thorough commit per logical unit, with detailed message
- Push after each commit
- Brief status update — what changed and what's next

But auto mode is **NOT a license to**:
- Delete files without explicit permission
- Force push or destructive git operations
- Take wild architecture leaps without checking in

If you hit a destructive decision point or genuine ambiguity, ask. Otherwise carry on.

---

## 9. Where the project lives

| Item | Path |
|---|---|
| Repo | `c:\Users\User\Documents\project33\syncsity-love\site\syncsity` |
| GitHub | `bigchain/syncsity20206` |
| Live | `https://syncsity.com/` |
| Server account | `marieatlasco` on HostFluid (Hetzner EX63) |
| PHP version | ea-php82 (FPM) |
| Memory dir | `C:\Users\User\.claude\projects\c--Users-User-Documents-project33-syncsity-love-site-syncsity\memory\` |

Read `CLAUDE.md` next for operational protocol, then `PROJECT_TRACKER.md` for the full reference.
