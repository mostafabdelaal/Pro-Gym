-- ============================================================================
-- Pro Gym — 001_create_schema
-- Fresh normalized schema (3NF) for the `gymster` database.
-- Engine: InnoDB, charset utf8mb4. Target: MySQL 8 / MariaDB 10.4+ (XAMPP).
--
-- Security notes baked into the design:
--   * Passwords are stored ONLY as a bcrypt/argon2 hash (never plaintext).
--   * NO card PAN or CVV is ever stored. Payments keep last-4 + a provider token.
--   * Roles are explicit (member/admin) so the admin area can be gated.
--
-- Run against an empty `gymster` database:
--   mysql -u root gymster < database/migrations/001_create_schema.sql
-- ============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------
-- branches — physical gym locations (was hardcoded in Branches.php / MainPage)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS branches (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(120) NOT NULL,
    city        VARCHAR(80)  NOT NULL DEFAULT 'Cairo',
    image_path  VARCHAR(255) NULL,
    is_active   TINYINT(1)   NOT NULL DEFAULT 1,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_branches_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- plans — subscription tiers (was hardcoded in Packages.php AND Payment.php)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS plans (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    code          VARCHAR(20)  NOT NULL,             -- BEGINNER, INTERMEDIATE, ...
    name          VARCHAR(60)  NOT NULL,
    monthly_price DECIMAL(10,2) NOT NULL,            -- EGP, no rounding loss
    is_popular    TINYINT(1)   NOT NULL DEFAULT 0,
    sort_order    SMALLINT     NOT NULL DEFAULT 0,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_plans_code (code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- plan_features — one row per bullet point (3NF: no repeating groups in plans)
CREATE TABLE IF NOT EXISTS plan_features (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    plan_id    INT UNSIGNED NOT NULL,
    feature    VARCHAR(160) NOT NULL,
    sort_order SMALLINT     NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_plan_features_plan (plan_id),
    CONSTRAINT fk_plan_features_plan FOREIGN KEY (plan_id)
        REFERENCES plans (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- members — identity + auth (replaces the identity/auth part of members_data)
--   Fixes the old first_Name/first_name casing mess with one canonical column.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS members (
    id                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name            VARCHAR(80)  NOT NULL,
    last_name             VARCHAR(80)  NOT NULL,
    email                 VARCHAR(190) NOT NULL,     -- 190 keeps utf8mb4 index < 767B
    phone                 VARCHAR(30)  NULL,
    birth_date            DATE         NULL,
    password_hash         VARCHAR(255) NOT NULL,     -- password_hash(), never plaintext
    role                  ENUM('member','admin') NOT NULL DEFAULT 'member',
    home_branch_id        INT UNSIGNED NULL,
    must_change_password  TINYINT(1)   NOT NULL DEFAULT 0,
    created_at            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_members_email (email),             -- enforces one account per email
    KEY idx_members_home_branch (home_branch_id),
    CONSTRAINT fk_members_home_branch FOREIGN KEY (home_branch_id)
        REFERENCES branches (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- password_resets — token-based reset (replaces the unauthenticated Forget.php)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS password_resets (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id   INT UNSIGNED NOT NULL,
    token_hash  CHAR(64)     NOT NULL,               -- sha256 of the emailed token
    expires_at  DATETIME     NOT NULL,
    used_at     DATETIME     NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_password_resets_token (token_hash),
    KEY idx_password_resets_member (member_id),
    CONSTRAINT fk_password_resets_member FOREIGN KEY (member_id)
        REFERENCES members (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- subscriptions — a member's plan over time (replaces members_data.plan)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS subscriptions (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id        INT UNSIGNED NOT NULL,
    plan_id          INT UNSIGNED NOT NULL,
    billing_interval ENUM('monthly','annual') NOT NULL DEFAULT 'monthly',
    status           ENUM('pending','active','expired','cancelled') NOT NULL DEFAULT 'pending',
    started_at       DATE NULL,
    expires_at       DATE NULL,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_subscriptions_member_status (member_id, status),
    KEY idx_subscriptions_plan (plan_id),
    CONSTRAINT fk_subscriptions_member FOREIGN KEY (member_id)
        REFERENCES members (id) ON DELETE CASCADE,
    CONSTRAINT fk_subscriptions_plan FOREIGN KEY (plan_id)
        REFERENCES plans (id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- payments — money movement. NO PAN, NO CVV. Only last-4 + processor token.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payments (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id       INT UNSIGNED NOT NULL,
    subscription_id INT UNSIGNED NULL,
    amount          DECIMAL(10,2) NOT NULL,          -- subtotal
    vat             DECIMAL(10,2) NOT NULL DEFAULT 0,
    total           DECIMAL(10,2) NOT NULL,
    currency        CHAR(3)      NOT NULL DEFAULT 'EGP',
    card_last4      CHAR(4)      NULL,               -- display only
    card_brand      VARCHAR(20)  NULL,               -- 'visa' etc, display only
    provider_token  VARCHAR(120) NULL,               -- opaque token from processor
    status          ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
    paid_at         DATETIME     NULL,
    created_at      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_payments_member (member_id),
    KEY idx_payments_subscription (subscription_id),
    CONSTRAINT fk_payments_member FOREIGN KEY (member_id)
        REFERENCES members (id) ON DELETE CASCADE,
    CONSTRAINT fk_payments_subscription FOREIGN KEY (subscription_id)
        REFERENCES subscriptions (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- trainers — coaching staff (was hardcoded in Trainer.php)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS trainers (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name          VARCHAR(120) NOT NULL,
    role          VARCHAR(80)  NOT NULL,             -- 'Yoga Instructor' etc
    specialty_tag VARCHAR(40)  NULL,                 -- 'Strength', 'Boxing' ...
    branch_id     INT UNSIGNED NULL,
    photo_path    VARCHAR(255) NULL,
    is_active     TINYINT(1)   NOT NULL DEFAULT 1,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_trainers_branch (branch_id),
    CONSTRAINT fk_trainers_branch FOREIGN KEY (branch_id)
        REFERENCES branches (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- classes — scheduled group sessions (Dashboard "upcoming", Classes page)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS classes (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(120) NOT NULL,
    trainer_id INT UNSIGNED NULL,
    branch_id  INT UNSIGNED NULL,
    starts_at  DATETIME     NOT NULL,
    duration_min SMALLINT   NOT NULL DEFAULT 60,
    capacity   SMALLINT     NOT NULL DEFAULT 20,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_classes_starts_at (starts_at),
    KEY idx_classes_trainer (trainer_id),
    KEY idx_classes_branch (branch_id),
    CONSTRAINT fk_classes_trainer FOREIGN KEY (trainer_id)
        REFERENCES trainers (id) ON DELETE SET NULL,
    CONSTRAINT fk_classes_branch FOREIGN KEY (branch_id)
        REFERENCES branches (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- class_bookings — M:N members <-> classes (Dashboard "Booked"/"spots left")
CREATE TABLE IF NOT EXISTS class_bookings (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    class_id   INT UNSIGNED NOT NULL,
    member_id  INT UNSIGNED NOT NULL,
    status     ENUM('booked','cancelled','attended') NOT NULL DEFAULT 'booked',
    booked_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_class_bookings (class_id, member_id),
    KEY idx_class_bookings_member (member_id),
    CONSTRAINT fk_class_bookings_class FOREIGN KEY (class_id)
        REFERENCES classes (id) ON DELETE CASCADE,
    CONSTRAINT fk_class_bookings_member FOREIGN KEY (member_id)
        REFERENCES members (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- workouts / workout_sets — logged training (Dashboard, Workout, Progress)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS workouts (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id    INT UNSIGNED NOT NULL,
    title        VARCHAR(120) NOT NULL,              -- 'Push Day · Chest & Triceps'
    performed_at DATETIME     NOT NULL,
    duration_min SMALLINT     NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_workouts_member_date (member_id, performed_at),
    CONSTRAINT fk_workouts_member FOREIGN KEY (member_id)
        REFERENCES members (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS workout_sets (
    id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    workout_id    INT UNSIGNED NOT NULL,
    exercise_name VARCHAR(120) NOT NULL,
    target_muscle VARCHAR(60)  NULL,
    set_number    SMALLINT     NOT NULL,
    weight_kg     DECIMAL(6,2) NULL,
    reps          SMALLINT     NULL,
    is_done       TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_workout_sets_workout (workout_id),
    CONSTRAINT fk_workout_sets_workout FOREIGN KEY (workout_id)
        REFERENCES workouts (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- body_metrics — weight trend (Progress "Body weight" chart)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS body_metrics (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id   INT UNSIGNED NOT NULL,
    recorded_at DATE          NOT NULL,
    weight_kg   DECIMAL(6,2)  NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_body_metrics (member_id, recorded_at),
    CONSTRAINT fk_body_metrics_member FOREIGN KEY (member_id)
        REFERENCES members (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- personal_records — PRs (Progress "Personal records")
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS personal_records (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    member_id   INT UNSIGNED NOT NULL,
    lift        VARCHAR(80)   NOT NULL,              -- 'Back Squat'
    value_kg    DECIMAL(6,2)  NOT NULL,
    achieved_at DATE          NULL,
    PRIMARY KEY (id),
    KEY idx_personal_records_member (member_id),
    CONSTRAINT fk_personal_records_member FOREIGN KEY (member_id)
        REFERENCES members (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
