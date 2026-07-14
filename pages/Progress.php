<?php
// Fitness progress / tracker — members only.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();
require_login();

$member    = current_member($conn);
$d         = member_display($member);
$fullName  = $d['fullName'];
$initials  = $d['initials'];
$planLabel = $d['planLabel'];
$memberId  = (int) ($member['id'] ?? 0);

// --- Progress data from the DB (was hardcoded in JS) ----------------------
// Body-weight trend: last 12 measurements, chronological.
$weights = [];
$wstmt = $conn->prepare(
    "SELECT weight_kg FROM body_metrics WHERE member_id = ? ORDER BY recorded_at DESC LIMIT 12"
);
$wstmt->bind_param('i', $memberId);
$wstmt->execute();
$wr = $wstmt->get_result();
while ($row = $wr->fetch_assoc()) {
    $weights[] = (float) $row['weight_kg'];
}
$wstmt->close();
$weights = array_reverse($weights);

// Weekly training volume (tons) over the last 8 weeks.
$volVals = [];
$vstmt = $conn->prepare(
    "SELECT YEARWEEK(w.performed_at, 3) yw, SUM(ws.weight_kg * ws.reps) / 1000 tons
     FROM workouts w JOIN workout_sets ws ON ws.workout_id = w.id
     WHERE w.member_id = ? AND w.performed_at >= (CURDATE() - INTERVAL 8 WEEK)
     GROUP BY yw ORDER BY yw"
);
$vstmt->bind_param('i', $memberId);
$vstmt->execute();
$vr = $vstmt->get_result();
while ($row = $vr->fetch_assoc()) {
    $volVals[] = round((float) $row['tons'], 1);
}
$vstmt->close();

// Personal records.
$prs = [];
$pstmt = $conn->prepare(
    "SELECT lift, value_kg FROM personal_records WHERE member_id = ? ORDER BY achieved_at DESC, id DESC"
);
$pstmt->bind_param('i', $memberId);
$pstmt->execute();
$pr = $pstmt->get_result();
while ($row = $pr->fetch_assoc()) {
    $val = rtrim(rtrim(number_format((float) $row['value_kg'], 1), '0'), '.');
    $prs[] = ['lift' => $row['lift'], 'val' => $val . ' kg', 'delta' => 'PR'];
}
$pstmt->close();

// Derived display values.
$weightChange = !empty($weights) ? round(end($weights) - reset($weights), 1) : 0;
$weightChangeLabel = ($weightChange > 0 ? '+' : '') . $weightChange . ' kg total';

$cwk = $conn->prepare(
    "SELECT COUNT(DISTINCT YEARWEEK(performed_at, 3)) c FROM workouts
     WHERE member_id = ? AND performed_at >= (CURDATE() - INTERVAL 12 WEEK)"
);
$cwk->bind_param('i', $memberId);
$cwk->execute();
$consWeeks = (int) ($cwk->get_result()->fetch_assoc()['c'] ?? 0);
$cwk->close();
$consistency = (int) round(min(12, $consWeeks) / 12 * 100);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Progress — Pro Gym</title>
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
      <a href="Workout.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 500; color: #a6a6b0; background: transparent;" style-hover="background: rgba(255,255,255,0.04); color: #F4F4F6;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M6.5 6.5v11M4 9v5M17.5 6.5v11M20 9v5M6.5 12h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Workouts</a>
      <a href="Progress.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 700; color: #0B0B0E; background: #D4FF3D;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M4 20V4M4 20h16M8 16l4-5 3 3 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>Progress</a>
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
      <div style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: -0.01em;">Progress</div>
      <div style="display: flex; align-items: center; gap: 8px; background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 9px 14px; color: #b6b6c0; font-size: 13.5px;">Last 12 weeks ▾</div>
    </div>

    <div style="padding: 32px 40px 60px; animation: floatUp 0.4s ease both;">
      <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 16px; margin-bottom: 16px;">
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 24px;">
          <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
            <div>
              <div style="font-size: 15px; font-weight: 700;">Body weight</div>
              <div style="font-size: 12.5px; color: #8a8a95;">Last 12 weeks</div>
            </div>
            <div style="text-align: right;">
              <div style="font-family: 'Space Grotesk', sans-serif; font-size: 26px; font-weight: 700;">{{ weightNow }} kg</div>
              <div style="font-size: 12.5px; color: #D4FF3D; font-weight: 600;"><?php echo htmlspecialchars($weightChangeLabel); ?></div>
            </div>
          </div>
          <svg viewBox="0 0 640 200" style="width: 100%; height: 200px;">
            <defs><linearGradient id="wg" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#D4FF3D" stop-opacity="0.28"/><stop offset="1" stop-color="#D4FF3D" stop-opacity="0"/></linearGradient></defs>
            <path d="{{ weightArea }}" fill="url(#wg)"/>
            <polyline points="{{ weightLine }}" fill="none" stroke="#D4FF3D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 24px;">
          <div style="font-size: 15px; font-weight: 700; margin-bottom: 4px;">Weekly volume</div>
          <div style="font-size: 12.5px; color: #8a8a95; margin-bottom: 18px;">Tonnage lifted (tons)</div>
          <div style="display: flex; align-items: flex-end; gap: 10px; height: 148px;">
            <sc-for list="{{ volumeBars }}" as="b" hint-placeholder-count="8">
              <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; height: 100%; justify-content: flex-end;">
                <div style="width: 100%; height: 100%; display: flex; align-items: flex-end;"><div style="{{ b.barStyle }}"></div></div>
                <span style="font-size: 11px; color: #6a6a74;">{{ b.label }}</span>
              </div>
            </sc-for>
          </div>
        </div>
      </div>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 24px;">
          <div style="font-size: 15px; font-weight: 700; margin-bottom: 18px;">Personal records</div>
          <div style="display: flex; flex-direction: column; gap: 12px;">
            <sc-for list="{{ prs }}" as="p" hint-placeholder-count="4">
              <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border: 1px solid rgba(255,255,255,0.07); border-radius: 12px;">
                <span style="font-size: 14.5px; font-weight: 600;">{{ p.lift }}</span>
                <div style="display: flex; align-items: center; gap: 12px;">
                  <span style="font-family: 'Space Grotesk', sans-serif; font-size: 18px; font-weight: 700;">{{ p.val }}</span>
                  <span style="font-size: 12px; font-weight: 700; color: #0B0B0E; background: #D4FF3D; padding: 3px 8px; border-radius: 999px;">{{ p.delta }}</span>
                </div>
              </div>
            </sc-for>
          </div>
        </div>
        <div style="background: linear-gradient(135deg,#1a1d10,#141418); border: 1px solid rgba(212,255,61,0.2); border-radius: 18px; padding: 24px; display: flex; flex-direction: column; justify-content: center;">
          <div style="font-size: 12.5px; letter-spacing: 0.14em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 10px;">Consistency</div>
          <div style="font-family: 'Space Grotesk', sans-serif; font-size: 52px; font-weight: 700; letter-spacing: -0.02em; line-height: 1;"><?php echo $consistency; ?>%</div>
          <p style="font-size: 14.5px; color: #b6b6c0; margin: 12px 0 0; max-width: 260px;">of planned sessions completed in the last 90 days. You're in the top 8% of members.</p>
        </div>
      </div>
    </div>
  </main>
</div>
</x-dc>
<script type="text/x-dc" data-dc-script>
class Component extends DCLogic {
  renderVals() {
    const accent = '#D4FF3D';
    let weight = <?php echo json_encode($weights); ?>;
    if (!weight.length) weight = [0, 0];
    const wMin = Math.floor(Math.min(...weight)) - 1;
    const wMax = Math.ceil(Math.max(...weight)) + 1;
    const range = (wMax - wMin) || 1;
    const W = 640, H = 200, PAD = 14;
    const denom = (weight.length - 1) || 1;
    const pts = weight.map((val, i) => {
      const x = PAD + i / denom * (W - PAD * 2);
      const y = H - PAD - (val - wMin) / range * (H - PAD * 2);
      return [Math.round(x), Math.round(y)];
    });
    const weightLine = pts.map((p) => p.join(',')).join(' ');
    const weightArea = `M${pts[0][0]},${H} ` + pts.map((p) => 'L' + p[0] + ',' + p[1]).join(' ') + ` L${pts[pts.length-1][0]},${H} Z`;
    let volVals = <?php echo json_encode($volVals); ?>;
    if (!volVals.length) volVals = [0];
    const volMax = Math.max(...volVals, 1) * 1.15;
    const volumeBars = volVals.map((val, i) => ({ label: 'W' + (i + 1),
      barStyle: `height:${Math.round(val / volMax * 100)}%;background:${i === volVals.length - 1 ? accent : 'rgba(212,255,61,0.35)'};border-radius:6px 6px 0 0;width:100%;` }));
    return {
      weightLine, weightArea, weightNow: weight[weight.length - 1].toFixed(1), volumeBars,
      prs: <?php echo json_encode($prs); ?>,
    };
  }
}
</script>
</body>
</html>
