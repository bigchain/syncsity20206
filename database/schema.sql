-- ───────────────────────────────────────────────────────────────────────────
-- Syncsity — Database schema
-- MySQL 8 / MariaDB 10.5+
--
-- Apply with:
--   mysql -u <user> -p <database> < database/schema.sql
--
-- All tables use utf8mb4_unicode_ci. Soft-deletes are NOT used — we hard-
-- delete on GDPR request.
-- ───────────────────────────────────────────────────────────────────────────

SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;
SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION';

-- ── Users ────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    email               VARCHAR(254) NOT NULL,
    name                VARCHAR(120) NULL,
    company             VARCHAR(200) NULL,

    -- Magic-link auth
    magic_link_token    CHAR(64) NULL,
    magic_link_expiry   DATETIME NULL,

    -- GDPR
    gdpr_consent        TINYINT(1) NOT NULL DEFAULT 1,
    gdpr_consent_at     DATETIME NULL,

    -- Bookkeeping
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uk_users_email (email),
    KEY idx_users_token (magic_link_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Assessments ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS assessments (
    id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED NOT NULL,

    -- Snapshot of answer fields (JSON for the full set, plus extracted columns
    -- for filtering / sorting / reporting).
    answers_json        LONGTEXT NOT NULL,             -- full {qid: value} blob
    name                VARCHAR(120) NULL,
    company             VARCHAR(200) NULL,
    website             VARCHAR(500) NULL,
    country             VARCHAR(40) NULL,
    biz_type            VARCHAR(40) NULL,
    team_size           VARCHAR(40) NULL,
    revenue_band        VARCHAR(40) NULL,
    frustration         VARCHAR(60) NULL,
    real_block          VARCHAR(60) NULL,
    monthly_inquiries   INT NULL,
    conversion_rate     DECIMAL(5,2) NULL,
    avg_deal            DECIMAL(12,2) NULL,
    capacity            VARCHAR(20) NULL,
    confidence          TINYINT NULL,
    priority            VARCHAR(40) NULL,

    -- Report status pipeline
    status              ENUM('queued','researching','analysing','writing','ready','failed') NOT NULL DEFAULT 'queued',
    status_message      VARCHAR(500) NULL,
    queued_at           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    started_at          DATETIME NULL,
    completed_at        DATETIME NULL,
    failed_attempts     TINYINT UNSIGNED NOT NULL DEFAULT 0,

    -- Generated report (JSON of all sections — rendered server-side into HTML)
    report_json         LONGTEXT NULL,
    report_share_token  CHAR(40) NULL,
    leak_amount         DECIMAL(12,2) NULL,    -- the headline £/month leak
    root_cause_name     VARCHAR(80) NULL,      -- e.g. "The Founder Bottleneck"

    -- Optional research artefacts (kept for debugging / re-rendering)
    research_json       LONGTEXT NULL,

    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    KEY idx_assess_user (user_id),
    KEY idx_assess_status (status, queued_at),
    UNIQUE KEY uk_assess_share (report_share_token),
    CONSTRAINT fk_assess_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Rate limits ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS rate_limits (
    `key`               VARCHAR(160) NOT NULL,
    hits                INT UNSIGNED NOT NULL DEFAULT 0,
    window_end          DATETIME NOT NULL,
    PRIMARY KEY (`key`),
    KEY idx_rl_window (window_end)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Audit log ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS audit_log (
    id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED NULL,
    action              VARCHAR(60) NOT NULL,
    detail              TEXT NULL,
    ip_hash             CHAR(64) NULL,
    user_agent          VARCHAR(500) NULL,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_audit_user (user_id, created_at),
    KEY idx_audit_action (action, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Contact messages ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS contact_messages (
    id                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name                VARCHAR(120) NOT NULL,
    email               VARCHAR(254) NOT NULL,
    company             VARCHAR(200) NULL,
    subject             VARCHAR(60) NULL,
    message             TEXT NOT NULL,
    ip_hash             CHAR(64) NULL,
    relayed_to_email    TINYINT(1) NOT NULL DEFAULT 0,
    sheet_appended      TINYINT(1) NOT NULL DEFAULT 0,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_contact_email (email, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
