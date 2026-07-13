# Pro Gym — Database Documentation

Fresh, normalized (3NF) schema for the `gymster` MySQL/MariaDB database. Replaces
the legacy single `members_data` god-table (which mixed identity, auth, subscription
and raw card data in one place).

## Design principles

- **One entity per table**, related by foreign keys.
- **No plaintext secrets**: passwords are stored only as a hash (`members.password_hash`).
- **No card data at rest**: `payments` keeps `card_last4` + a processor `provider_token`
  only — never the full PAN, never the CVV.
- **Canonical column names**: fixes the old `first_Name` / `first_name` casing bug —
  there is now exactly one `first_name` column.
- **utf8mb4 / InnoDB** throughout for full Unicode + referential integrity.

## How to load

```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS gymster"
mysql -u root gymster < database/migrations/001_create_schema.sql
mysql -u root gymster < database/seeders/002_seed_reference_data.sql
```

## ER overview

```
branches ──1:N── members ──1:N── subscriptions ──N:1── plans ──1:N── plan_features
   │                │ │ │ │                │
   │                │ │ │ │                └─1:N─ payments
   │                │ │ │ └─1:N── workouts ──1:N── workout_sets
   │                │ │ └─1:N── body_metrics
   │                │ └─1:N── personal_records
   │                ├─1:N── class_bookings ──N:1── classes
   │                └─1:N── password_resets
   └──1:N── trainers ──1:N── classes
```

## Tables

### `branches`
Physical gym locations (was hardcoded in `Branches.php` / `MainPage.php`).
| Column | Type | Notes |
|---|---|---|
| id | INT UNSIGNED PK | |
| name | VARCHAR(100) | e.g. "Zamalek" |
| slug | VARCHAR(120) UNIQUE | URL/lookup key |
| city | VARCHAR(80) | default 'Cairo' |
| image_path | VARCHAR(255) | branch photo |
| is_active | TINYINT(1) | soft hide |

### `plans`
Subscription tiers (was hardcoded twice: `Packages.php` JS + `Payment.php` PHP map).
| Column | Type | Notes |
|---|---|---|
| id | INT UNSIGNED PK | |
| code | VARCHAR(20) UNIQUE | BEGINNER…ELITE |
| name | VARCHAR(60) | display name |
| monthly_price | DECIMAL(10,2) | EGP |
| is_popular | TINYINT(1) | "Most popular" badge |
| sort_order | SMALLINT | display order |

### `plan_features`
One row per feature bullet (3NF — no repeating groups on `plans`).
FK `plan_id` → `plans.id` (CASCADE).

### `members`
Identity + authentication (replaces identity/auth part of `members_data`).
| Column | Type | Notes |
|---|---|---|
| id | INT UNSIGNED PK | internal id |
| first_name / last_name | VARCHAR(80) | canonical casing |
| email | VARCHAR(190) UNIQUE | login key; enforces one account/email |
| phone | VARCHAR(30) | |
| birth_date | DATE | |
| password_hash | VARCHAR(255) | `password_hash()` output only |
| role | ENUM('member','admin') | gates the admin area |
| home_branch_id | FK→branches | SET NULL on branch delete |
| must_change_password | TINYINT(1) | force reset flow |

### `password_resets`
Token-based reset (replaces the insecure unauthenticated `Forget.php`).
Stores a **hash** of the emailed token, an expiry, and a `used_at` stamp.
FK `member_id` → `members.id` (CASCADE).

### `subscriptions`
A member's plan over time (replaces `members_data.plan`).
| Column | Notes |
|---|---|
| member_id / plan_id | FKs |
| billing_interval | monthly / annual |
| status | pending / active / expired / cancelled |
| started_at / expires_at | DATE |

Indexed on `(member_id, status)` for "current plan" lookups.

### `payments`
Money movement. **No PAN, no CVV.**
| Column | Notes |
|---|---|
| amount / vat / total | DECIMAL(10,2) |
| currency | default EGP |
| card_last4 | display only |
| card_brand | display only |
| provider_token | opaque token from processor |
| status | pending / paid / failed / refunded |
FKs → `members`, `subscriptions`.

### `trainers`
Coaching staff (was hardcoded in `Trainer.php`). FK `branch_id` → `branches`.

### `classes`
Scheduled group sessions (Dashboard "upcoming", Classes page).
FKs → `trainers`, `branches`. Indexed on `starts_at`.

### `class_bookings`
M:N members ↔ classes. `UNIQUE(class_id, member_id)` prevents double-booking.

### `workouts` / `workout_sets`
Logged training (Dashboard, Workout, Progress). `workout_sets` holds per-set
weight/reps/done flag. FKs cascade from `members` → `workouts` → `workout_sets`.

### `body_metrics`
Weight trend for the Progress chart. `UNIQUE(member_id, recorded_at)`.

### `personal_records`
PRs shown on Progress. FK `member_id` → `members`.

## Seed data

`002_seed_reference_data.sql` loads the catalog data previously hardcoded in the UI:
9 branches, 5 plans + their features, 5 trainers. Idempotent (safe to re-run).
Member/workout/class rows are created by the app, not seeded.

## Migration status

Greenfield: the original `gymster` DB / dump could not be located, so this schema
is authored fresh rather than migrated. No production member data to preserve.
