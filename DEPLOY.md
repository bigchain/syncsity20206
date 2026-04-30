# Syncsity — Deployment Guide

> Target: HostFluid (Hetzner EX63), cPanel/WHM 132+, AlmaLinux 9.7, ea-php82, MySQL 8.
> cPanel user: `marieatlasco` · web root: `/home/marieatlasco/public_html/`

This guide is a copy-paste runbook. Run the blocks **in order**.

---

## 0. Prerequisites

- cPanel access for `marieatlasco` user (login through https://server.hostfluid.com:2083 or hostfluid's branded URL).
- Terminal SSH access (port 2222, ed25519 key — the user already has this).
- A working email mailbox `hello@syncsity.com` in cPanel → Email Accounts (create if missing; remember the password).
- DNS for `syncsity.com` pointing to `77.42.1.80` (or NS at `ns1/ns2.hostfluid.com`).

---

## 1. Upload code

### Option A — git clone (preferred)

```bash
ssh -p 2222 marieatlasco@server.hostfluid.com
cd /home/marieatlasco
# back up anything currently in public_html
mv public_html public_html.bak.$(date +%Y%m%d)
git clone https://github.com/bigchain/syncsity20206.git public_html
cd public_html
```

### Option B — cPanel File Manager (Windows-friendly)

1. Zip the project locally (excluding `old-lovable-build/`, `node_modules/` if present, and `.git/`).
2. cPanel → File Manager → `/home/marieatlasco/public_html/` → Upload zip → Extract.
3. Set permissions: directories `0755`, files `0644` (cPanel's "Reset Permissions" works).

---

## 2. Create the database

cPanel → MySQL® Databases:

1. Create database: **already exists as `marieatlasco_ehhdyy`** — confirm.
2. Confirm user **`marieatlasco_hey87`** has `ALL PRIVILEGES` on `marieatlasco_ehhdyy`.
3. Open phpMyAdmin → select `marieatlasco_ehhdyy` → SQL tab → paste contents of `database/schema.sql` → Go.

```bash
# Or via SSH:
mysql -u marieatlasco_hey87 -p marieatlasco_ehhdyy < /home/marieatlasco/public_html/database/schema.sql
```

---

## 3. Configure `.env`

The `.env` file is committed to the local repo for staging convenience but **must be locked down** on the server.

```bash
cd /home/marieatlasco/public_html
chmod 600 .env
chown marieatlasco:marieatlasco .env

# Generate the SESSION_SECRET (replace placeholder in .env)
php -r "echo bin2hex(random_bytes(32));"
# Edit .env and paste the output as SESSION_SECRET=<that hex>

# Verify .env is unreachable from the web — should return 403:
curl -I https://syncsity.com/.env
```

### Rotate exposed secrets

Both the DB password and the OpenRouter key travelled through chat once. **Rotate both:**

- **DB password**: cPanel → MySQL Databases → Change Password → for `marieatlasco_hey87`. Update `DB_PASS=` in `.env`.
- **OpenRouter key**: https://openrouter.ai/keys → revoke the existing key → create a fresh one → paste into `OPENROUTER_API_KEY=` in `.env`.

### Set the SMTP mailbox password

`SMTP_PASS=` should match the password of `hello@syncsity.com` in cPanel → Email Accounts.

---

## 4. Storage permissions

```bash
cd /home/marieatlasco/public_html
mkdir -p storage/sessions storage/logs storage/cache
chmod 700 storage storage/sessions storage/logs storage/cache
chown -R marieatlasco:marieatlasco storage
```

---

## 5. Composer (optional — for PHPMailer)

The mailer falls back to PHP `mail()` if PHPMailer isn't installed. cPanel's `mail()` works (Exim is configured), but PHPMailer is more reliable for SMTP authentication and HTML email. Install when convenient:

```bash
cd /home/marieatlasco/public_html
# cPanel ships composer at /usr/local/cpanel/3rdparty/bin/composer or similar
which composer || alias composer='/usr/local/cpanel/3rdparty/bin/php -d allow_url_fopen=on /usr/local/cpanel/3rdparty/bin/composer.phar'
composer require phpmailer/phpmailer
```

---

## 6. Cron — report generator backstop

The report generator usually runs immediately via fire-and-forget self-curl from `/api/assess-submit`. Cron is the backstop for any submission that didn't fire (e.g. server restart, network glitch).

cPanel → Cron Jobs → Add new cron. Use the cPanel-bundled PHP (most reliable on HostFluid):

```
*/5 * * * * cd /home/marieatlasco/public_html && /usr/local/cpanel/3rdparty/bin/php api/report-generate.php >> storage/logs/report-cron.log 2>&1
```

If `/usr/local/cpanel/3rdparty/bin/php` doesn't exist on your box, try `/usr/local/bin/php` instead. Confirm with:
```bash
ls -la /usr/local/cpanel/3rdparty/bin/php /usr/local/bin/php
```

Note the `cd` is required so relative paths inside the script resolve correctly.

---

## 7. Google Sheets (optional but recommended)

1. Google Cloud Console → create project (or reuse the `dailydebate-171023` one) → enable **Google Sheets API**.
2. Create a **Service Account** → generate a JSON key → download.
3. Upload the JSON to `/home/marieatlasco/credentials/syncsity-sheets.json`. **Outside web root.**
   ```bash
   mkdir -p /home/marieatlasco/credentials
   chmod 700 /home/marieatlasco/credentials
   # upload file via SFTP / cPanel File Manager
   chmod 600 /home/marieatlasco/credentials/syncsity-sheets.json
   ```
4. Open the JSON, copy the `client_email` (looks like `…@…iam.gserviceaccount.com`).
5. Create a Google Sheet (or reuse one). Add tabs `Leads` and `Reports`. Share the sheet with the `client_email` as **Editor**.
6. Copy the sheet's ID from its URL (`https://docs.google.com/spreadsheets/d/<THIS>/edit`).
7. In `.env`, set:
   ```
   GOOGLE_SERVICE_ACCOUNT_JSON=/home/marieatlasco/credentials/syncsity-sheets.json
   GOOGLE_SHEETS_LEADS_ID=<the sheet id>
   ```

---

## 8. SSL (AutoSSL)

```bash
# After DNS is resolving:
/usr/local/cpanel/bin/autossl_check --user=marieatlasco
```

cPanel → SSL/TLS Status should show valid certificates for `syncsity.com` and `www.syncsity.com`.

---

## 9. Smoke test

```bash
# Home page
curl -I https://syncsity.com/                    # Expect 200

# Assess page
curl -I https://syncsity.com/assess              # Expect 200

# robots, sitemap, llms.txt
curl https://syncsity.com/robots.txt
curl https://syncsity.com/sitemap.xml
curl https://syncsity.com/llms.txt | head

# .env should NOT be reachable
curl -I https://syncsity.com/.env                # Expect 403

# /storage/ should NOT be reachable
curl -I https://syncsity.com/storage/sessions/   # Expect 403

# Test the magic-link API (don't put a real email — the rate-limit will flip)
curl -X POST -d "email=test@example.com&_csrf=invalid" https://syncsity.com/api/magic-link
# Expect 403 — CSRF rejected. That's correct.
```

### Email smoke test

1. Visit `/auth/login`, enter your own email, submit.
2. Check your inbox (and spam) within 60 seconds.
3. Click the magic link → you should land on `/dashboard` logged in.

If email doesn't arrive:

```bash
# Tail Exim log for the send attempt
tail -50 /var/log/exim_mainlog | grep <your-email>

# Check your domain's SPF / DKIM at https://www.mail-tester.com
```

### Report generator smoke test

1. Take the assessment at `/assess` with a real email.
2. After submitting, the processing page should show progress within ~5 seconds.
3. Watch the engine log:
   ```bash
   tail -f /home/marieatlasco/public_html/storage/logs/report-engine.log
   ```
4. Within 60–120 seconds the assessment should reach `status='ready'` and the report-ready email should arrive.

---

## 10. Going live checklist

- [ ] DNS `syncsity.com` and `www.syncsity.com` resolve to the server.
- [ ] AutoSSL issued.
- [ ] `chmod 600 .env` confirmed.
- [ ] `SESSION_SECRET` is a fresh 64-char hex string.
- [ ] DB password rotated (post-chat).
- [ ] OpenRouter key rotated (post-chat).
- [ ] SMTP_PASS matches the cPanel mailbox password for `hello@syncsity.com`.
- [ ] Cron `*/5 * * * *` is installed.
- [ ] Service-account JSON in place + Google Sheet shared with it.
- [ ] Smoke tests above all green.
- [ ] Home page renders, hero looks right in dark + light themes.
- [ ] Assessment completes end-to-end with real email + real report generation.
- [ ] Test report received at `edward@syncsity.com` (internal copy).

---

## Common operations

### Rolling back

```bash
cd /home/marieatlasco
mv public_html public_html.broken
mv public_html.bak.<date> public_html
```

### Tailing all logs

```bash
tail -f /home/marieatlasco/public_html/storage/logs/*.log
```

### Re-running the engine for a stuck assessment

```bash
# Check status
mysql -u marieatlasco_hey87 -p marieatlasco_ehhdyy -e "SELECT id, user_id, status, status_message FROM assessments ORDER BY id DESC LIMIT 10;"

# Reset to queued
mysql -u marieatlasco_hey87 -p marieatlasco_ehhdyy -e "UPDATE assessments SET status='queued', failed_attempts=0 WHERE id=<id>;"

# Cron will pick it up within 5 minutes, or run manually:
php /home/marieatlasco/public_html/api/report-generate.php
```

### Updating from git

```bash
cd /home/marieatlasco/public_html
git pull origin main
chown -R marieatlasco:marieatlasco .
# Don't blow away storage
chmod 700 storage storage/sessions storage/logs storage/cache
```

---

## What this guide INTENTIONALLY does not cover

- WAF / ModSecurity tuning — defaults are correct for our patterns.
- Email warm-up for cold outbound (we don't run cold outbound from this domain).
- Multi-region redundancy — this is a single-region UK service by design.
- Containerisation — overkill for the current scope.

If any of those become relevant, they earn their way into a follow-up doc.
