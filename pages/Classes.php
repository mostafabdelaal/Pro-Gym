<?php
// Class schedule listing — members only.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();
require_login();

$member    = current_member($conn);
$d         = member_display($member);
$fullName  = $d['fullName'];
$initials  = $d['initials'];
$planLabel = $d['planLabel'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Classes — Pro Gym</title>
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
      <a href="Classes.php" style="display: flex; align-items: center; gap: 12px; padding: 11px 14px; border-radius: 11px; font-size: 14.5px; font-weight: 700; color: #0B0B0E; background: #D4FF3D;"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 9.5h17M8 3.5v3M16 3.5v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Classes</a>
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
      <div style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: -0.01em;">Classes</div>
    </div>
    <div style="padding: 32px 40px 60px; max-width: 720px; animation: floatUp 0.4s ease both;">
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 80px 20px; border: 1px dashed rgba(255,255,255,0.14); border-radius: 20px;">
        <div style="width: 64px; height: 64px; border-radius: 18px; background: rgba(212,255,61,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 22px; color: #D4FF3D;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 9.5h17M8 3.5v3M16 3.5v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </div>
        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 24px; font-weight: 700; margin: 0 0 8px;">No classes booked yet</h2>
        <p style="font-size: 15px; color: #9a9aa5; margin: 0 0 24px; max-width: 380px;">Browse the weekly schedule across all 9 branches and reserve your spot in seconds.</p>
        <a href="Trainer.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 13px 26px; font-weight: 700; font-size: 15px;" style-hover="background:#e6ff7a;">Browse schedule</a>
      </div>
    </div>
  </main>
</div>
</x-dc>
</body>
</html>
