#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────────────────────
# Syncsity — Production deploy
# Run on the HostFluid server (cPanel user marieatlasco) via SSH.
#
# Usage (normal):    bash deploy.sh
# Usage (with FPM):  bash deploy.sh --reload-fpm    (requires root / sudo)
# Usage (force):     bash deploy.sh --force         (stash local changes first)
# ─────────────────────────────────────────────────────────────────────────────

set -euo pipefail

# ── Config ──────────────────────────────────────────────────────────────────
APP_DIR="/home/marieatlasco/public_html"
APP_USER="marieatlasco"
APP_GROUP="marieatlasco"
PHP_FPM_SERVICE="ea-php82-php-fpm"
GIT_REMOTE="origin"
GIT_BRANCH="main"

# ── Args ────────────────────────────────────────────────────────────────────
RELOAD_FPM=false
FORCE_PULL=false
for arg in "$@"; do
    case "$arg" in
        --reload-fpm) RELOAD_FPM=true ;;
        --force)      FORCE_PULL=true ;;
        *) echo "Unknown flag: $arg"; exit 1 ;;
    esac
done

# ── Helpers ─────────────────────────────────────────────────────────────────
log()  { printf "\033[36m[deploy]\033[0m %s\n" "$*"; }
warn() { printf "\033[33m[deploy:warn]\033[0m %s\n" "$*" >&2; }
fail() { printf "\033[31m[deploy:fail]\033[0m %s\n" "$*" >&2; exit 1; }

# ── Pre-flight ──────────────────────────────────────────────────────────────
[ -d "$APP_DIR/.git" ] || fail "$APP_DIR is not a git repo. Did you clone it?"
cd "$APP_DIR"
log "in $APP_DIR"

# ── Pull ────────────────────────────────────────────────────────────────────
if [ -n "$(git status --porcelain)" ]; then
    if [ "$FORCE_PULL" = true ]; then
        log "stashing local changes"
        git stash push -m "auto-stash $(date -u +%FT%TZ)"
    else
        warn "local changes detected — re-run with --force to stash them, or commit them first"
        git status --short
        exit 1
    fi
fi

log "pulling $GIT_REMOTE/$GIT_BRANCH"
git pull "$GIT_REMOTE" "$GIT_BRANCH"

if [ "$FORCE_PULL" = true ]; then
    git stash drop || true
fi

# ── Permissions ─────────────────────────────────────────────────────────────
log "fixing ownership"
chown -R "$APP_USER:$APP_GROUP" .

log "locking storage + secrets"
mkdir -p storage/sessions storage/logs storage/cache
chmod 700 storage storage/sessions storage/logs storage/cache
[ -f .env ] && chmod 600 .env

# ── Optional FPM reload ─────────────────────────────────────────────────────
if [ "$RELOAD_FPM" = true ]; then
    if [ "$(id -u)" -ne 0 ]; then
        warn "--reload-fpm requires root; skipping"
    elif systemctl is-active --quiet "$PHP_FPM_SERVICE"; then
        log "reloading $PHP_FPM_SERVICE"
        systemctl reload "$PHP_FPM_SERVICE"
    else
        warn "$PHP_FPM_SERVICE not active; skipping reload"
    fi
fi

# ── Done ────────────────────────────────────────────────────────────────────
HEAD_SHA=$(git rev-parse --short HEAD)
HEAD_MSG=$(git log -1 --pretty=%s)
log "deployed $HEAD_SHA — $HEAD_MSG"
log "✓ done"
