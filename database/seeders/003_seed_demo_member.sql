-- ============================================================================
-- Pro Gym — 003_seed_demo_member
-- Creates a fully-populated demo member so the member app (Dashboard, Progress,
-- Workout, Classes) renders real data instead of hardcoded placeholders.
--
-- Login:  demo@progym.local  /  demo12345
--
-- Idempotent: deletes the demo member first (FK cascades wipe its child rows),
-- then reseeds. Requires 001_create_schema + 002_seed_reference_data first.
--   mysql -u root gymster < database/seeders/003_seed_demo_member.sql
-- ============================================================================

SET NAMES utf8mb4;

DELETE FROM members WHERE email = 'demo@progym.local';

INSERT INTO members (first_name, last_name, email, phone, birth_date, password_hash, role, home_branch_id)
VALUES ('Nour', 'Hassan', 'demo@progym.local', '01000000000', '1996-04-12',
        '$2y$10$wdkacPhBAo5cYNcKZwPtjeYyehCWMXNg/Mm5NfJHWVoApOL.VPnxi', 'member',
        (SELECT id FROM branches WHERE slug = 'zamalek'));
SET @mid  := LAST_INSERT_ID();
SET @plan := (SELECT id FROM plans WHERE code = 'ADVANCED');

-- Active subscription + its payment
INSERT INTO subscriptions (member_id, plan_id, billing_interval, status, started_at, expires_at)
VALUES (@mid, @plan, 'monthly', 'active', CURDATE() - INTERVAL 20 DAY, CURDATE() + INTERVAL 10 DAY);
SET @sub := LAST_INSERT_ID();

INSERT INTO payments (member_id, subscription_id, amount, vat, total, currency, card_last4, card_brand, provider_token, status, paid_at)
VALUES (@mid, @sub, 3000.00, 420.00, 3420.00, 'EGP', '4242', 'visa', 'SEED-DEMO', 'paid', NOW() - INTERVAL 20 DAY);

-- Body weight trend: 12 weekly measurements, trending down
INSERT INTO body_metrics (member_id, recorded_at, weight_kg) VALUES
(@mid, CURDATE() - INTERVAL 77 DAY, 82.4),
(@mid, CURDATE() - INTERVAL 70 DAY, 81.6),
(@mid, CURDATE() - INTERVAL 63 DAY, 81.1),
(@mid, CURDATE() - INTERVAL 56 DAY, 80.2),
(@mid, CURDATE() - INTERVAL 49 DAY, 79.6),
(@mid, CURDATE() - INTERVAL 42 DAY, 79.0),
(@mid, CURDATE() - INTERVAL 35 DAY, 78.3),
(@mid, CURDATE() - INTERVAL 28 DAY, 77.9),
(@mid, CURDATE() - INTERVAL 21 DAY, 77.2),
(@mid, CURDATE() - INTERVAL 14 DAY, 76.8),
(@mid, CURDATE() - INTERVAL  7 DAY, 76.3),
(@mid, CURDATE(),                    75.9);

-- Personal records
INSERT INTO personal_records (member_id, lift, value_kg, achieved_at) VALUES
(@mid, 'Back Squat',     140.0, CURDATE() - INTERVAL 10 DAY),
(@mid, 'Deadlift',       175.0, CURDATE() - INTERVAL 18 DAY),
(@mid, 'Bench Press',     95.0, CURDATE() - INTERVAL  4 DAY),
(@mid, 'Overhead Press',  60.0, CURDATE() - INTERVAL  2 DAY);

-- Eight past weekly sessions (for the volume trend + streak), increasing tonnage.
-- Each uses one compound lift with a few sets.
INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Leg Day · Squat focus', CURDATE() - INTERVAL 8 WEEK, 58);
SET @w := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@w,'Back Squat','Legs',1,90,8,1),(@w,'Back Squat','Legs',2,100,6,1),(@w,'Back Squat','Legs',3,110,5,1);

INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Pull Day · Back & Biceps', CURDATE() - INTERVAL 7 WEEK, 61);
SET @w := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@w,'Deadlift','Back',1,120,6,1),(@w,'Deadlift','Back',2,140,4,1),(@w,'Barbell Row','Back',3,70,8,1);

INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Push Day · Chest & Triceps', CURDATE() - INTERVAL 6 WEEK, 60);
SET @w := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@w,'Bench Press','Chest',1,70,8,1),(@w,'Bench Press','Chest',2,80,6,1),(@w,'Incline Press','Chest',3,60,8,1);

INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Leg Day · Squat focus', CURDATE() - INTERVAL 5 WEEK, 63);
SET @w := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@w,'Back Squat','Legs',1,100,8,1),(@w,'Back Squat','Legs',2,115,5,1),(@w,'Leg Press','Legs',3,180,10,1);

INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Pull Day · Back & Biceps', CURDATE() - INTERVAL 4 WEEK, 64);
SET @w := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@w,'Deadlift','Back',1,140,5,1),(@w,'Deadlift','Back',2,160,3,1),(@w,'Pull-up','Back',3,10,10,1);

INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Push Day · Chest & Triceps', CURDATE() - INTERVAL 3 WEEK, 62);
SET @w := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@w,'Bench Press','Chest',1,80,6,1),(@w,'Bench Press','Chest',2,90,4,1),(@w,'Dips','Triceps',3,20,10,1);

INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Leg Day · Squat focus', CURDATE() - INTERVAL 2 WEEK, 66);
SET @w := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@w,'Back Squat','Legs',1,115,6,1),(@w,'Back Squat','Legs',2,130,4,1),(@w,'Leg Press','Legs',3,200,10,1);

INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Pull Day · Back & Biceps', CURDATE() - INTERVAL 1 WEEK, 64);
SET @w := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@w,'Deadlift','Back',1,150,5,1),(@w,'Deadlift','Back',2,175,2,1),(@w,'Barbell Row','Back',3,80,8,1);

-- Today's live session: Push Day, 4 exercises (matches the Workout page layout).
INSERT INTO workouts (member_id, title, performed_at, duration_min)
VALUES (@mid, 'Push Day · Chest & Triceps', NOW(), NULL);
SET @today := LAST_INSERT_ID();
INSERT INTO workout_sets (workout_id, exercise_name, target_muscle, set_number, weight_kg, reps, is_done) VALUES
(@today,'Barbell Bench Press','Chest',1,60,10,1),
(@today,'Barbell Bench Press','Chest',2,70,8,1),
(@today,'Barbell Bench Press','Chest',3,80,6,0),
(@today,'Barbell Bench Press','Chest',4,80,6,0),
(@today,'Incline Dumbbell Press','Upper chest',1,24,12,1),
(@today,'Incline Dumbbell Press','Upper chest',2,26,10,0),
(@today,'Incline Dumbbell Press','Upper chest',3,28,8,0),
(@today,'Cable Fly','Chest',1,15,15,0),
(@today,'Cable Fly','Chest',2,17,12,0),
(@today,'Cable Fly','Chest',3,20,12,0),
(@today,'Triceps Rope Pushdown','Triceps',1,25,15,0),
(@today,'Triceps Rope Pushdown','Triceps',2,30,12,0),
(@today,'Triceps Rope Pushdown','Triceps',3,35,10,0);

-- A couple of upcoming classes + one booking for the demo member.
INSERT INTO classes (name, trainer_id, branch_id, starts_at, duration_min, capacity)
VALUES
('HIIT with Asser',
 (SELECT id FROM trainers WHERE name = 'Asser Essam'),
 (SELECT id FROM branches WHERE slug = 'zamalek'),
 CURDATE() + INTERVAL 1 DAY + INTERVAL 18 HOUR, 45, 20),
('Boxing with Abdullah',
 (SELECT id FROM trainers WHERE name = 'Abdullah Medhat'),
 (SELECT id FROM branches WHERE slug = 'nasr-city'),
 CURDATE() + INTERVAL 2 DAY + INTERVAL 7 HOUR + INTERVAL 30 MINUTE, 60, 16);

INSERT INTO class_bookings (class_id, member_id, status)
VALUES ((SELECT id FROM classes WHERE name = 'Boxing with Abdullah' ORDER BY id DESC LIMIT 1), @mid, 'booked');
