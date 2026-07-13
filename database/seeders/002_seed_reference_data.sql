-- ============================================================================
-- Pro Gym — 002_seed_reference_data
-- Seeds reference/catalog data that used to be hardcoded in the PHP/JS pages:
--   branches (Branches.php), plans + features (Packages.php / Payment.php),
--   trainers (Trainer.php). Safe to re-run: uses INSERT ... ON DUPLICATE KEY.
--
--   mysql -u root gymster < database/seeders/002_seed_reference_data.sql
-- ============================================================================

SET NAMES utf8mb4;

-- --- branches (9 Cairo branches from Branches.php) --------------------------
INSERT INTO branches (name, slug, city, image_path) VALUES
    ('Sheraton',      'sheraton',      'Cairo', 'images/BranchesImg/sheraton.png'),
    ('Nasr City',     'nasr-city',     'Cairo', 'images/BranchesImg/nasr.png'),
    ('Zamalek',       'zamalek',       'Cairo', 'images/BranchesImg/zamalek.png'),
    ('5th Settlement','5th-settlement','Cairo', 'images/BranchesImg/5th.png'),
    ('Madinaty',      'madinaty',      'Cairo', 'images/BranchesImg/mad.png'),
    ('Al Korba',      'al-korba',      'Cairo', 'images/BranchesImg/zamalek.png'),
    ('Al Mohandesin', 'al-mohandesin', 'Cairo', 'images/BranchesImg/5th.png'),
    ('Al Shorouk',    'al-shorouk',    'Cairo', 'images/BranchesImg/mad.png'),
    ('Al Dokki',      'al-dokki',      'Cairo', NULL)
ON DUPLICATE KEY UPDATE name = VALUES(name), city = VALUES(city), image_path = VALUES(image_path);

-- --- plans (prices from Packages.php / Payment.php priceMap) -----------------
INSERT INTO plans (code, name, monthly_price, is_popular, sort_order) VALUES
    ('BEGINNER',     'Beginner',     1000.00, 0, 1),
    ('INTERMEDIATE', 'Intermediate', 2000.00, 0, 2),
    ('ADVANCED',     'Advanced',     3000.00, 1, 3),
    ('EXPERT',       'Expert',       4000.00, 0, 4),
    ('ELITE',        'Elite',        5000.00, 0, 5)
ON DUPLICATE KEY UPDATE name = VALUES(name), monthly_price = VALUES(monthly_price),
    is_popular = VALUES(is_popular), sort_order = VALUES(sort_order);

-- --- plan_features (bullets from Packages.php) -------------------------------
-- Clear existing feature rows for these plans, then reinsert (idempotent reseed).
DELETE pf FROM plan_features pf
    JOIN plans p ON p.id = pf.plan_id
    WHERE p.code IN ('BEGINNER','INTERMEDIATE','ADVANCED','EXPERT','ELITE');

INSERT INTO plan_features (plan_id, feature, sort_order)
SELECT p.id, f.feature, f.sort_order FROM plans p
JOIN (
    SELECT 'BEGINNER' code, 'Access to basic gym equipment' feature, 1 sort_order UNION ALL
    SELECT 'BEGINNER', 'Basic fitness assessment', 2 UNION ALL
    SELECT 'BEGINNER', '1 group class per week', 3 UNION ALL
    SELECT 'BEGINNER', '1 private session / month', 4 UNION ALL
    SELECT 'INTERMEDIATE', 'Full equipment access', 1 UNION ALL
    SELECT 'INTERMEDIATE', 'Personalized workout plan', 2 UNION ALL
    SELECT 'INTERMEDIATE', 'Access to swimming pool', 3 UNION ALL
    SELECT 'INTERMEDIATE', '2 private sessions / month', 4 UNION ALL
    SELECT 'ADVANCED', 'Full equipment access', 1 UNION ALL
    SELECT 'ADVANCED', 'Unlimited group classes', 2 UNION ALL
    SELECT 'ADVANCED', 'Pool / sauna / steam', 3 UNION ALL
    SELECT 'ADVANCED', '3 private sessions / month', 4 UNION ALL
    SELECT 'EXPERT', 'Customized workout plan', 1 UNION ALL
    SELECT 'EXPERT', 'Exclusive VIP events', 2 UNION ALL
    SELECT 'EXPERT', '5 private sessions / month', 3 UNION ALL
    SELECT 'EXPERT', 'Unlimited group classes', 4 UNION ALL
    SELECT 'ELITE', 'Advanced fitness testing', 1 UNION ALL
    SELECT 'ELITE', 'Customized nutrition plans', 2 UNION ALL
    SELECT 'ELITE', 'Priority scheduling', 3 UNION ALL
    SELECT 'ELITE', 'Performance tracking & analysis', 4
) f ON f.code = p.code;

-- --- trainers (from Trainer.php) --------------------------------------------
INSERT INTO trainers (name, role, specialty_tag, branch_id, photo_path)
SELECT t.name, t.role, t.tag, b.id, t.photo FROM (
    SELECT 'Mohamed Ahmed'    name, 'Certified Fitness Trainer' role, 'Strength' tag, 'zamalek'      branch_slug, 'images/t5.jpg' photo UNION ALL
    SELECT 'Mostafa Abdelal',      'Yoga Instructor',               'Mobility',      'nasr-city',   'images/t4.jpg' UNION ALL
    SELECT 'Ahmed Yasser',         'Personal Trainer',              '1-on-1',        'sheraton',    'images/t2.jpg' UNION ALL
    SELECT 'Abdullah Medhat',      'Boxing Instructor',             'Boxing',        'madinaty',    'images/t3.jpg' UNION ALL
    SELECT 'Asser Essam',          'CrossFit Coach',                'CrossFit',      '5th-settlement','images/t1.jpg'
) t LEFT JOIN branches b ON b.slug = t.branch_slug;
