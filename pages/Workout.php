<?php
// Workout planner / live session — members only.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();
require_login();

$member    = current_member($conn);
$d         = member_display($member);
$fullName  = $d['fullName'];
$initials  = $d['initials'];
$planLabel = $d['planLabel'];
$memberId  = (int) ($member['id'] ?? 0);

// --- Latest workout session from the DB (was hardcoded in JS) --------------
$workoutTitle = 'No active session';
$exData   = [];
$doneSets = [];
$wq = $conn->prepare(
    "SELECT id, title FROM workouts WHERE member_id = ? ORDER BY performed_at DESC, id DESC LIMIT 1"
);
$wq->bind_param('i', $memberId);
$wq->execute();
$w = $wq->get_result()->fetch_assoc();
$wq->close();

if ($w) {
    $workoutTitle = $w['title'];
    $sq = $conn->prepare(
        "SELECT exercise_name, target_muscle, weight_kg, reps, is_done
         FROM workout_sets WHERE workout_id = ? ORDER BY id"
    );
    $sq->bind_param('i', $w['id']);
    $sq->execute();
    $sr = $sq->get_result();

    $index = [];              // exercise_name => position in $exData
    while ($row = $sr->fetch_assoc()) {
        $name = $row['exercise_name'];
        if (!isset($index[$name])) {
            $index[$name] = count($exData);
            $exData[] = ['name' => $name, 'target' => $row['target_muscle'] ?? '', 'sets' => []];
        }
        $ei = $index[$name];
        $si = count($exData[$ei]['sets']);
        $exData[$ei]['sets'][] = [
            'w' => 0 + $row['weight_kg'],
            'r' => (int) $row['reps'],
        ];
        if ((int) $row['is_done'] === 1) {
            $doneSets[$ei . '-' . $si] = true;
        }
    }
    $sq->close();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Workout — Pro Gym</title>
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
</style>
</helmet>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased; display: flex;">
  <aside style="width: 250px; flex-shrink: 0; border-right: 1px solid rgba(255,255,255,0.07); padding: 24px 18px; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh;">
    <a href="MainPage.php" style="display: flex; align-items: center; gap: 10px; padding: 6px 8px 22px; color: #F4F4F6;">
      <img src="../images/1.png" style="height: 26px; filter: invert(1) brightness(1.25);" alt="">
      <span style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 16px;">PRO GYM</span>
    </a>
    <nav style="display: flex; flex-direction: column; gap: 4px;">
      <a href="Dashboard.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 500; color: #a6a6b0; background: transparent;" style-hover="background: rgba(255,255,255,0.04); color: #F4F4F6;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/></svg>Dashboard</a>
      <a href="Workout.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 700; color: #0B0B0E; background: #D4FF3D;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M6.5 6.5v11M4 9v5M17.5 6.5v11M20 9v5M6.5 12h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Workouts</a>
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
      <div style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: -0.01em;">Workout</div>
      <a href="Dashboard.php" style="font-size: 14px; color: #b6b6c0;" style-hover="color: #F4F4F6;">← Back to dashboard</a>
    </div>

    <div style="padding: 32px 40px 60px; max-width: 860px; animation: floatUp 0.4s ease both;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px;">
        <div>
          <div style="font-size: 12.5px; letter-spacing: 0.14em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 6px;">Live session</div>
          <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 30px; font-weight: 700; letter-spacing: -0.02em; margin: 0;"><?php echo htmlspecialchars($workoutTitle); ?></h1>
        </div>
        <div style="text-align: right;">
          <div style="font-family: 'Space Grotesk', sans-serif; font-size: 26px; font-weight: 700;">42:18</div>
          <div style="font-size: 12.5px; color: #8a8a95;">elapsed</div>
        </div>
      </div>
      <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 16px 20px; margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; font-size: 13.5px; margin-bottom: 10px;"><span style="color: #b6b6c0;">Session progress</span><span style="font-weight: 700; color: #D4FF3D;">{{ workoutDone }}/{{ workoutTotal }} sets</span></div>
        <div style="height: 8px; background: rgba(255,255,255,0.08); border-radius: 999px; overflow: hidden;"><div style="{{ workoutPctStyle }}"></div></div>
      </div>

      <div style="display: flex; flex-direction: column; gap: 14px;">
        <sc-for list="{{ exercises }}" as="ex" hint-placeholder-count="4">
          <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 20px;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
              <div style="width: 32px; height: 32px; border-radius: 9px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 15px; color: #D4FF3D;">{{ ex.num }}</div>
              <div style="flex: 1;">
                <div style="font-size: 16.5px; font-weight: 700;">{{ ex.name }}</div>
                <div style="font-size: 12.5px; color: #8a8a95;">{{ ex.target }}</div>
              </div>
            </div>
            <div style="display: grid; grid-template-columns: 34px 1fr 1fr 44px; gap: 10px; padding: 0 14px 8px; font-size: 11.5px; color: #6a6a74; text-transform: uppercase; letter-spacing: 0.08em;"><span>Set</span><span>Weight</span><span>Reps</span><span></span></div>
            <div style="display: flex; flex-direction: column; gap: 8px;">
              <sc-for list="{{ ex.sets }}" as="st" hint-placeholder-count="4">
                <div style="{{ st.rowStyle }}">
                  <span style="font-weight: 700; color: #8a8a95; font-size: 14px;">{{ st.num }}</span>
                  <span style="font-size: 15px; font-weight: 600;">{{ st.w }} kg</span>
                  <span style="font-size: 15px; font-weight: 600;">{{ st.r }} reps</span>
                  <div onClick="{{ st.toggle }}" style="{{ st.checkStyle }}"><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#0B0B0E" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                </div>
              </sc-for>
            </div>
          </div>
        </sc-for>
      </div>
      <div style="display: flex; gap: 12px; margin-top: 24px;">
        <a href="Dashboard.php" style="flex: 1; text-align: center; background: #D4FF3D; color: #0B0B0E; border-radius: 12px; padding: 16px; font-weight: 700; font-size: 15.5px;" style-hover="background:#e6ff7a;">Finish &amp; save session</a>
        <a href="Dashboard.php" style="text-align: center; background: transparent; color: #b6b6c0; border: 1px solid rgba(255,255,255,0.16); border-radius: 12px; padding: 16px 24px; font-weight: 600; font-size: 15px;">Discard</a>
      </div>
    </div>
  </main>
</div>
</x-dc>
<script type="text/x-dc" data-dc-script>
class Component extends DCLogic {
  state = { doneSets: <?php echo json_encode((object) $doneSets); ?> };
  renderVals() {
    const accent = '#D4FF3D';
    const exData = <?php echo json_encode($exData); ?>;
    let totalSets = 0, doneCount = 0;
    const exercises = exData.map((ex, ei) => {
      const sets = ex.sets.map((s, si) => {
        const key = ei + '-' + si;
        const done = !!this.state.doneSets[key];
        totalSets++; if (done) doneCount++;
        return { ...s, num: si + 1,
          toggle: () => this.setState((st) => ({ doneSets: { ...st.doneSets, [key]: !st.doneSets[key] } })),
          rowStyle: `display:grid;grid-template-columns:34px 1fr 1fr 44px;align-items:center;gap:10px;padding:11px 14px;border-radius:10px;background:${done ? 'rgba(212,255,61,0.08)' : 'rgba(255,255,255,0.03)'};border:1px solid ${done ? 'rgba(212,255,61,0.25)' : 'rgba(255,255,255,0.06)'};`,
          checkStyle: `width:26px;height:26px;border-radius:8px;border:1.5px solid ${done ? accent : 'rgba(255,255,255,0.2)'};background:${done ? accent : 'transparent'};display:flex;align-items:center;justify-content:center;cursor:pointer;` };
      });
      return { ...ex, num: ei + 1, sets };
    });
    const workoutPct = totalSets ? Math.round(doneCount / totalSets * 100) : 0;
    return {
      exercises, workoutDone: doneCount, workoutTotal: totalSets,
      workoutPctStyle: `width:${workoutPct}%;height:100%;background:${accent};border-radius:999px;transition:width .4s;`,
    };
  }
}
</script>
</body>
</html>
