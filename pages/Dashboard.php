<?php
// Member dashboard homepage — members only.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();
require_login();

$member    = current_member($conn);
$d         = member_display($member);
$fullName  = $d['fullName'];
$initials  = $d['initials'];
$greetName = $d['greetName'];
$planLabel = $d['planLabel'];
$memberId  = (int) ($member['id'] ?? 0);

// Human-friendly relative day label.
$relDay = static function (?string $ts): string {
    if (!$ts) return '';
    $d0 = new DateTime('today');
    $d1 = new DateTime($ts); $d1->setTime(0, 0);
    $diff = (int) $d0->diff($d1)->format('%r%a');
    if ($diff === 0) return 'Today';
    if ($diff === -1) return 'Yesterday';
    if ($diff === 1) return 'Tomorrow';
    return (new DateTime($ts))->format('D');
};

$WEEK_GOAL = 5;

// This-week aggregates.
$stmt = $conn->prepare(
    "SELECT COUNT(*) sessions, COALESCE(AVG(duration_min),0) avgdur
     FROM workouts WHERE member_id = ? AND YEARWEEK(performed_at,3) = YEARWEEK(CURDATE(),3)"
);
$stmt->bind_param('i', $memberId); $stmt->execute();
$wk = $stmt->get_result()->fetch_assoc(); $stmt->close();
$sessionsWeek = (int) $wk['sessions'];
$avgDur       = (int) round($wk['avgdur']);

$stmt = $conn->prepare(
    "SELECT COALESCE(SUM(ws.weight_kg*ws.reps),0)/1000 tons
     FROM workouts w JOIN workout_sets ws ON ws.workout_id = w.id
     WHERE w.member_id = ? AND YEARWEEK(w.performed_at,3) = YEARWEEK(CURDATE(),3)"
);
$stmt->bind_param('i', $memberId); $stmt->execute();
$volWeek = round((float) $stmt->get_result()->fetch_assoc()['tons'], 1); $stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) c FROM workouts WHERE member_id = ?");
$stmt->bind_param('i', $memberId); $stmt->execute();
$totalSessions = (int) $stmt->get_result()->fetch_assoc()['c']; $stmt->close();

// Recent 3 sessions with per-workout volume.
$recentActivity = [];
$stmt = $conn->prepare(
    "SELECT w.title, w.performed_at, w.duration_min,
            (SELECT COALESCE(SUM(ws.weight_kg*ws.reps),0)/1000 FROM workout_sets ws WHERE ws.workout_id = w.id) tons
     FROM workouts w WHERE w.member_id = ? ORDER BY w.performed_at DESC LIMIT 3"
);
$stmt->bind_param('i', $memberId); $stmt->execute();
$rr = $stmt->get_result();
while ($row = $rr->fetch_assoc()) {
    $meta = $relDay($row['performed_at']);
    if (!empty($row['duration_min'])) $meta .= ' · ' . (int) $row['duration_min'] . ' min';
    $meta .= ' · ' . round((float) $row['tons'], 1) . 't';
    $recentActivity[] = ['name' => $row['title'], 'meta' => $meta];
}
$stmt->close();

// Hero card = latest session.
$heroTitle = 'No session yet'; $heroEx = 0; $heroSets = 0; $heroDur = 0;
$stmt = $conn->prepare(
    "SELECT id, title, duration_min FROM workouts WHERE member_id = ? ORDER BY performed_at DESC, id DESC LIMIT 1"
);
$stmt->bind_param('i', $memberId); $stmt->execute();
$hw = $stmt->get_result()->fetch_assoc(); $stmt->close();
if ($hw) {
    $heroTitle = $hw['title'];
    $heroDur   = (int) $hw['duration_min'];
    $stmt = $conn->prepare(
        "SELECT COUNT(DISTINCT exercise_name) ex, COUNT(*) st FROM workout_sets WHERE workout_id = ?"
    );
    $stmt->bind_param('i', $hw['id']); $stmt->execute();
    $c = $stmt->get_result()->fetch_assoc(); $stmt->close();
    $heroEx = (int) $c['ex']; $heroSets = (int) $c['st'];
}

// Upcoming classes (next 2).
$upcoming = [];
$stmt = $conn->prepare(
    "SELECT c.name, c.starts_at, c.capacity, b.name branch,
            (SELECT COUNT(*) FROM class_bookings cb WHERE cb.class_id = c.id AND cb.status='booked') booked,
            (SELECT COUNT(*) FROM class_bookings cb WHERE cb.class_id = c.id AND cb.member_id = ? AND cb.status='booked') mine
     FROM classes c LEFT JOIN branches b ON b.id = c.branch_id
     WHERE c.starts_at > NOW() ORDER BY c.starts_at LIMIT 2"
);
$stmt->bind_param('i', $memberId); $stmt->execute();
$ur = $stmt->get_result();
while ($row = $ur->fetch_assoc()) {
    $spots = ((int) $row['mine'] > 0)
        ? 'Booked'
        : max(0, (int) $row['capacity'] - (int) $row['booked']) . ' spots left';
    $time = $relDay($row['starts_at']) . ' · ' . (new DateTime($row['starts_at']))->format('g:i A');
    $upcoming[] = ['name' => $row['name'], 'time' => $time, 'branch' => $row['branch'] ?? '—', 'spots' => $spots];
}
$stmt->close();

$dashStats = [
    ['label' => 'Sessions this week', 'value' => (string) $sessionsWeek, 'sub' => 'of ' . $WEEK_GOAL . ' goal', 'trend' => $sessionsWeek >= $WEEK_GOAL ? '✓' : ''],
    ['label' => 'Volume this week',   'value' => $volWeek . 't', 'sub' => 'this week', 'trend' => ''],
    ['label' => 'Avg duration',       'value' => $avgDur . 'm', 'sub' => 'per session', 'trend' => ''],
    ['label' => 'Total sessions',     'value' => (string) $totalSessions, 'sub' => 'all time', 'trend' => '🔥'],
];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard — Pro Gym</title>
<script src="./support.js"></script>
</head>
<body>
<x-dc>
<helmet>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0B0B0E; }
  ::selection { background: #D4FF3D; color: #0B0B0E; }
  a { text-decoration: none; }
  ::-webkit-scrollbar { width: 10px; }
  ::-webkit-scrollbar-thumb { background: #26262e; border-radius: 8px; border: 2px solid #0B0B0E; }
  @keyframes floatUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes ringGrow { from { stroke-dashoffset: 339; } }
</style>
</helmet>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased; display: flex;">
  <aside style="width: 250px; flex-shrink: 0; border-right: 1px solid rgba(255,255,255,0.07); padding: 24px 18px; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh;">
    <a href="MainPage.php" style="display: flex; align-items: center; gap: 10px; padding: 6px 8px 22px; color: #F4F4F6;">
      <img src="../images/1.png" style="height: 26px; filter: invert(1) brightness(1.25);" alt="">
      <span style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 16px;">PRO GYM</span>
    </a>
    <nav style="display: flex; flex-direction: column; gap: 4px;">
      <a href="Dashboard.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 700; color: #0B0B0E; background: #D4FF3D;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/></svg>Dashboard</a>
      <a href="Workout.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 500; color: #a6a6b0; background: transparent;" style-hover="background: rgba(255,255,255,0.04); color: #F4F4F6;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M6.5 6.5v11M4 9v5M17.5 6.5v11M20 9v5M6.5 12h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Workouts</a>
      <a href="Progress.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 500; color: #a6a6b0; background: transparent;" style-hover="background: rgba(255,255,255,0.04); color: #F4F4F6;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M4 20V4M4 20h16M8 16l4-5 3 3 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>Progress</a>
      <a href="Classes.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 500; color: #a6a6b0; background: transparent;" style-hover="background: rgba(255,255,255,0.04); color: #F4F4F6;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 9.5h17M8 3.5v3M16 3.5v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Classes</a>
      <a href="Profile.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 500; color: #a6a6b0; background: transparent;" style-hover="background: rgba(255,255,255,0.04); color: #F4F4F6;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/><path d="M4.5 20c0-3.5 3.4-6 7.5-6s7.5 2.5 7.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Profile</a>
    </nav>
    <div style="margin-top: auto; display: flex; flex-direction: column; gap: 10px;">
      <a href="Admin.php" style="display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 11px; border: 1px dashed rgba(255,255,255,0.16); font-size: 13.5px; color: #a6a6b0;" style-hover="border-color: rgba(212,255,61,0.4); color: #F4F4F6;">⚙ Admin view</a>
      <a href="MainPage.php" style="display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 11px; font-size: 13.5px; color: #a6a6b0;" style-hover="color: #F4F4F6;">↦ Log out</a>
      <div style="display: flex; align-items: center; gap: 11px; padding: 16px 8px 10px; border-top: 1px solid rgba(255,255,255,0.07);">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg,#D4FF3D,#9fd400); color: #0B0B0E; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;"><?php echo htmlspecialchars($initials); ?></div>
        <div style="flex: 1; min-width: 0;">
          <div style="font-size: 14px; font-weight: 700;"><?php echo htmlspecialchars($fullName); ?></div>
          <div style="font-size: 12px; color: #8a8a95;"><?php echo htmlspecialchars($planLabel); ?></div>
        </div>
      </div>
    </div>
  </aside>

  <main style="flex: 1; min-width: 0;">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 40px; border-bottom: 1px solid rgba(255,255,255,0.07); position: sticky; top: 0; background: rgba(11,11,14,0.8); backdrop-filter: blur(16px); z-index: 20;">
      <div style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: -0.01em;">Dashboard</div>
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 8px; background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 9px 14px; width: 240px; color: #6a6a74; font-size: 14px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          Search exercises…
        </div>
        <div style="width: 40px; height: 40px; border-radius: 10px; background: #141418; border: 1px solid rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M18 8a6 6 0 10-12 0c0 7-3 8-3 8h18s-3-1-3-8M13.7 21a2 2 0 01-3.4 0" stroke="#c4c4cc" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
          <span style="position: absolute; top: 8px; right: 9px; width: 7px; height: 7px; background: #D4FF3D; border-radius: 50%; border: 2px solid #141418;"></span>
        </div>
      </div>
    </div>

    <div style="padding: 32px 40px 60px; animation: floatUp 0.4s ease both;">
      <div style="margin-bottom: 24px;">
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 30px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 4px;">Good morning, <?php echo htmlspecialchars($greetName); ?> 👋</h1>
        <p style="font-size: 15px; color: #9a9aa5; margin: 0;"><?php
          $remaining = max(0, $WEEK_GOAL - $sessionsWeek);
          echo $remaining > 0
              ? "You're " . $remaining . ' session' . ($remaining === 1 ? '' : 's') . " away from hitting this week's goal."
              : "You've hit this week's goal. Great work.";
        ?></p>
      </div>

      <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px; margin-bottom: 16px;">
        <div style="background: linear-gradient(120deg,#1a1d10,#141418); border: 1px solid rgba(212,255,61,0.22); border-radius: 18px; padding: 28px; display: flex; align-items: center; justify-content: space-between;">
          <div>
            <div style="font-size: 12.5px; letter-spacing: 0.14em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 10px;">Latest session</div>
            <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700; margin: 0 0 6px;"><?php echo htmlspecialchars($heroTitle); ?></h2>
            <p style="font-size: 14.5px; color: #b6b6c0; margin: 0 0 22px;"><?php echo $heroEx; ?> exercise<?php echo $heroEx === 1 ? '' : 's'; ?> · <?php echo $heroSets; ?> sets<?php echo $heroDur > 0 ? ' · ~' . $heroDur . ' min' : ''; ?></p>
            <a href="Workout.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 14px 28px; font-weight: 700; font-size: 15px;" style-hover="background:#e6ff7a;">Start workout →</a>
          </div>
          <div style="width: 130px; height: 130px; border-radius: 20px; background-image: linear-gradient(rgba(11,11,14,0.2),rgba(11,11,14,0.4)), url('../images/2.jpg'); background-size: cover; background-position: center;"></div>
        </div>
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 24px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
          <div style="position: relative; width: 128px; height: 128px;">
            <svg width="128" height="128" viewBox="0 0 128 128" style="transform: rotate(-90deg);">
              <circle cx="64" cy="64" r="54" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="12"/>
              <circle cx="64" cy="64" r="54" fill="none" stroke="#D4FF3D" stroke-width="12" stroke-linecap="round" stroke-dasharray="339" stroke-dashoffset="<?php echo (int) round(339 * (1 - min($sessionsWeek, $WEEK_GOAL) / $WEEK_GOAL)); ?>" style="animation: ringGrow 1s ease both;"/>
            </svg>
            <div style="position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
              <span style="font-family: 'Space Grotesk', sans-serif; font-size: 30px; font-weight: 700;"><?php echo $sessionsWeek; ?>/<?php echo $WEEK_GOAL; ?></span>
              <span style="font-size: 12px; color: #8a8a95;">sessions</span>
            </div>
          </div>
          <div style="font-size: 14px; color: #b6b6c0; margin-top: 14px; text-align: center;">Weekly goal</div>
        </div>
      </div>

      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px;">
        <sc-for list="{{ dashStats }}" as="s" hint-placeholder-count="4">
          <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: start;">
              <div style="font-size: 13px; color: #8a8a95;">{{ s.label }}</div>
              <div style="font-size: 12px; color: #D4FF3D; font-weight: 700;">{{ s.trend }}</div>
            </div>
            <div style="font-family: 'Space Grotesk', sans-serif; font-size: 30px; font-weight: 700; margin: 10px 0 2px;">{{ s.value }}</div>
            <div style="font-size: 12.5px; color: #6a6a74;">{{ s.sub }}</div>
          </div>
        </sc-for>
      </div>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 24px;">
          <div style="font-size: 16px; font-weight: 700; margin-bottom: 18px;">Recent activity</div>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <sc-for list="{{ recentActivity }}" as="a" hint-placeholder-count="3">
              <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(212,255,61,0.1); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#D4FF3D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <div style="flex: 1;">
                  <div style="font-size: 14.5px; font-weight: 600;">{{ a.name }}</div>
                  <div style="font-size: 12.5px; color: #8a8a95;">{{ a.meta }}</div>
                </div>
              </div>
            </sc-for>
          </div>
        </div>
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 24px;">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px;">
            <div style="font-size: 16px; font-weight: 700;">Upcoming classes</div>
            <a href="Classes.php" style="font-size: 13px; color: #D4FF3D; font-weight: 600;">See all</a>
          </div>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <sc-for list="{{ upcoming }}" as="c" hint-placeholder-count="2">
              <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px; border: 1px solid rgba(255,255,255,0.07); border-radius: 12px;">
                <div>
                  <div style="font-size: 14.5px; font-weight: 600;">{{ c.name }}</div>
                  <div style="font-size: 12.5px; color: #8a8a95;">{{ c.time }} · {{ c.branch }}</div>
                </div>
                <span style="font-size: 12px; font-weight: 600; color: #D4FF3D;">{{ c.spots }}</span>
              </div>
            </sc-for>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
</x-dc>
<script type="text/x-dc" data-dc-script>
class Component extends DCLogic {
  renderVals() {
    return {
      dashStats: <?php echo json_encode($dashStats); ?>,
      recentActivity: <?php echo json_encode($recentActivity); ?>,
      upcoming: <?php echo json_encode($upcoming); ?>,
    };
  }
}
</script>
</body>
</html>
