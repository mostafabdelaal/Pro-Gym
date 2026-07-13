<?php
// Member profile panel — members only.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();
require_login();

$member    = current_member($conn);
$d         = member_display($member);
$email     = $d['email'];
$fullName  = $d['fullName'];
$initials  = $d['initials'];
$userPlan  = $d['plan'];
$phone     = $member['phone'] ?? '';
$birthdate = $member['birth_date'] ?? '';

// Home branch name via FK (falls back to em dash when unset).
$homeBranch = '—';
if (!empty($member['home_branch_id'])) {
    $bstmt = $conn->prepare("SELECT name FROM branches WHERE id = ?");
    $bstmt->bind_param('i', $member['home_branch_id']);
    $bstmt->execute();
    if ($b = $bstmt->get_result()->fetch_assoc()) {
        $homeBranch = $b['name'];
    }
    $bstmt->close();
}

// Card last-4 from the most recent payment (never the full number).
$cardLast4 = '';
$memberId = (int) $member['id'];
$cstmt = $conn->prepare(
    "SELECT card_last4 FROM payments
     WHERE member_id = ? AND card_last4 IS NOT NULL
     ORDER BY created_at DESC LIMIT 1"
);
$cstmt->bind_param('i', $memberId);
$cstmt->execute();
if ($c = $cstmt->get_result()->fetch_assoc()) {
    $cardLast4 = $c['card_last4'];
}
$cstmt->close();

$profileFields = [
    ['label' => 'Email',       'value' => $email],
    ['label' => 'Phone',       'value' => $phone !== '' ? $phone : '—'],
    ['label' => 'Birthdate',   'value' => $birthdate !== '' ? $birthdate : '—'],
    ['label' => 'Home branch', 'value' => $homeBranch],
    ['label' => 'Card ID',     'value' => $cardLast4 !== '' ? '•••• ' . $cardLast4 : 'Not set'],
    ['label' => 'Plan',        'value' => $userPlan],
];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profile — Pro Gym</title>
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
      <a href="Progress.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 500; color: #a6a6b0; background: transparent;" style-hover="background: rgba(255,255,255,0.04); color: #F4F4F6;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M4 20V4M4 20h16M8 16l4-5 3 3 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>Progress</a>
      <a href="Classes.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 500; color: #a6a6b0; background: transparent;" style-hover="background: rgba(255,255,255,0.04); color: #F4F4F6;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 9.5h17M8 3.5v3M16 3.5v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Classes</a>
      <a href="Profile.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 700; color: #0B0B0E; background: #D4FF3D;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/><path d="M4.5 20c0-3.5 3.4-6 7.5-6s7.5 2.5 7.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Profile</a>
    </nav>
    <div style="margin-top: auto; display: flex; flex-direction: column; gap: 10px;">
      <a href="Admin.php" style="display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 11px; border: 1px dashed rgba(255,255,255,0.16); font-size: 13.5px; color: #a6a6b0;" style-hover="border-color: rgba(212,255,61,0.4); color: #F4F4F6;">⚙ Admin view</a>
      <a href="MainPage.php" style="display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 11px; font-size: 13.5px; color: #a6a6b0;" style-hover="color: #F4F4F6;">↦ Log out</a>
      <div style="display: flex; align-items: center; gap: 11px; padding: 16px 8px 10px; border-top: 1px solid rgba(255,255,255,0.07);">
        <div style="width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg,#D4FF3D,#9fd400); color: #0B0B0E; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px;"><?php echo htmlspecialchars($initials); ?></div>
        <div style="flex: 1; min-width: 0;">
          <div style="font-size: 14px; font-weight: 700;"><?php echo htmlspecialchars($fullName); ?></div>
          <div style="font-size: 12px; color: #8a8a95;"><?php echo htmlspecialchars(ucfirst(strtolower($userPlan))); ?> plan</div>
        </div>
      </div>
    </div>
  </aside>

  <main style="flex: 1; min-width: 0;">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 40px; border-bottom: 1px solid rgba(255,255,255,0.07); position: sticky; top: 0; background: rgba(11,11,14,0.8); backdrop-filter: blur(16px); z-index: 20;">
      <div style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: -0.01em;">Profile</div>
    </div>
    <div style="padding: 32px 40px 60px; max-width: 880px; animation: floatUp 0.4s ease both;">
      <div style="display: grid; grid-template-columns: 5fr 4fr; gap: 16px; margin-bottom: 16px;">
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 26px; display: flex; align-items: center; gap: 20px;">
          <div style="width: 74px; height: 74px; border-radius: 20px; background: linear-gradient(135deg,#D4FF3D,#9fd400); color: #0B0B0E; display: flex; align-items: center; justify-content: center; font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 26px;"><?php echo htmlspecialchars($initials); ?></div>
          <div>
            <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 26px; font-weight: 700; margin: 0 0 6px;"><?php echo htmlspecialchars($fullName); ?></h1>
            <div style="display: flex; gap: 8px;">
              <span style="font-size: 12.5px; font-weight: 700; color: #0B0B0E; background: #D4FF3D; padding: 4px 11px; border-radius: 999px;"><?php echo htmlspecialchars($userPlan); ?></span>
              <span style="font-size: 12.5px; color: #9a9aa5; padding: 4px 0;">Pro Gym member</span>
            </div>
          </div>
        </div>
        <div style="background: linear-gradient(135deg,#22242a,#141418); border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 22px; position: relative; overflow: hidden;">
          <div style="display: flex; justify-content: space-between; align-items: start;">
            <span style="font-size: 12.5px; letter-spacing: 0.12em; text-transform: uppercase; color: #8a8a95; font-weight: 700;">Membership card</span>
            <img src="../images/1.png" style="height: 20px; filter: invert(1) brightness(1.2);" alt="">
          </div>
          <div style="font-family: 'Space Grotesk', sans-serif; font-size: 21px; letter-spacing: 0.14em; margin: 26px 0 8px;">•••• •••• •••• <?php echo htmlspecialchars($cardLast4 !== '' ? $cardLast4 : '••••'); ?></div>
          <div style="display: flex; justify-content: space-between; font-size: 12.5px; color: #9a9aa5;"><span><?php echo htmlspecialchars(strtoupper($fullName)); ?></span><span>ZAMALEK</span></div>
        </div>
      </div>
      <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 26px; margin-bottom: 16px;">
        <div style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">Account details</div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 22px 40px;">
          <sc-for list="{{ profileFields }}" as="f" hint-placeholder-count="6">
            <div>
              <div style="font-size: 12px; color: #8a8a95; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 6px;">{{ f.label }}</div>
              <div style="font-size: 15.5px; font-weight: 600;">{{ f.value }}</div>
            </div>
          </sc-for>
        </div>
      </div>
      <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 26px;">
        <div style="font-size: 16px; font-weight: 700; margin-bottom: 8px;">Preferences</div>
        <sc-for list="{{ prefs }}" as="p" hint-placeholder-count="3">
          <div style="display: flex; align-items: center; justify-content: space-between; padding: 16px 0; border-top: 1px solid rgba(255,255,255,0.07);">
            <div>
              <div style="font-size: 14.5px; font-weight: 600;">{{ p.label }}</div>
              <div style="font-size: 12.5px; color: #8a8a95;">{{ p.sub }}</div>
            </div>
            <div onClick="{{ p.toggle }}" style="{{ p.trackStyle }}"><div style="{{ p.knobStyle }}"></div></div>
          </div>
        </sc-for>
      </div>
    </div>
  </main>
</div>
</x-dc>
<script type="text/x-dc" data-dc-script>
class Component extends DCLogic {
  state = { prefs: { notifications: true, darkmode: true, arabic: false } };
  renderVals() {
    const accent = '#D4FF3D';
    return {
      profileFields: <?php echo json_encode($profileFields); ?>,
      prefs: [
        { label: 'Workout reminders', sub: 'Push notifications before sessions', on: this.state.prefs.notifications, key: 'notifications' },
        { label: 'Dark appearance', sub: 'Use the dark theme across the app', on: this.state.prefs.darkmode, key: 'darkmode' },
        { label: 'العربية · Arabic (RTL)', sub: 'Switch interface language and direction', on: this.state.prefs.arabic, key: 'arabic' },
      ].map((p) => ({
        ...p,
        toggle: () => this.setState((st) => ({ prefs: { ...st.prefs, [p.key]: !st.prefs[p.key] } })),
        trackStyle: `width:46px;height:26px;border-radius:999px;background:${p.on ? accent : 'rgba(255,255,255,0.14)'};padding:3px;display:flex;cursor:pointer;transition:background .2s;justify-content:${p.on ? 'flex-end' : 'flex-start'};`,
        knobStyle: 'width:20px;height:20px;border-radius:50%;background:#0B0B0E;',
      })),
    };
  }
}
</script>
</body>
</html>
