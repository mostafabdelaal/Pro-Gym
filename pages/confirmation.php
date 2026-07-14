<?php
// Subscription success — members only. Restyled into the Pro Gym dark system.
require_once __DIR__ . '/../includes/auth.php';
require_login();

$member = current_member();
$d      = member_display($member);
$plan   = $d['plan'] !== 'NONE' ? ucfirst(strtolower($d['plan'])) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>You're in — Pro Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0B0B0E; }
  a { text-decoration: none; }
  ::selection { background: #D4FF3D; color: #0B0B0E; }
  @keyframes floatUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes popIn { 0% { transform: scale(0.7); opacity: 0; } 60% { transform: scale(1.08); } 100% { transform: scale(1); opacity: 1; } }
  @keyframes drawCheck { from { stroke-dashoffset: 48; } to { stroke-dashoffset: 0; } }
</style>
</head>
<body>
<div style="min-height: 100vh; background: radial-gradient(1200px 600px at 50% -10%, rgba(212,255,61,0.08), transparent 60%), #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased; display: flex; align-items: center; justify-content: center; padding: 48px;">
  <div style="width: 100%; max-width: 440px; text-align: center; animation: floatUp 0.6s ease both;">
    <a href="MainPage.php" style="display: inline-flex; align-items: center; gap: 10px; color: #F4F4F6; margin-bottom: 40px;">
      <img src="../images/1.png" style="height: 26px; filter: invert(1) brightness(1.25);" alt="">
      <span style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 16px;">PRO GYM</span>
    </a>

    <div style="width: 84px; height: 84px; margin: 0 auto 28px; border-radius: 50%; background: rgba(212,255,61,0.12); border: 1px solid rgba(212,255,61,0.4); display: flex; align-items: center; justify-content: center; animation: popIn 0.5s ease both 0.15s;">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
        <path d="M20 6L9 17l-5-5" stroke="#D4FF3D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="48" style="animation: drawCheck 0.5s ease both 0.5s;"/>
      </svg>
    </div>

    <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 32px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 12px;">You're all set<?php echo $plan !== '' ? ', ' . htmlspecialchars($d['greetName']) : ''; ?>.</h1>
    <p style="font-size: 16px; line-height: 1.6; color: #9a9aa5; margin: 0 0 8px;">
      <?php if ($plan !== ''): ?>
        Your <strong style="color:#F4F4F6;"><?php echo htmlspecialchars($plan); ?></strong> membership is active. All 9 branches and the Pro Gym app are yours.
      <?php else: ?>
        Your membership is active. All 9 branches and the Pro Gym app are yours.
      <?php endif; ?>
    </p>

    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 32px;">
      <a href="Dashboard.php" style="display: block; background: #D4FF3D; color: #0B0B0E; border-radius: 12px; padding: 15px; font-weight: 700; font-size: 15.5px;" style-hover="background: #e6ff7a;">Go to dashboard</a>
      <a href="Profile.php" style="display: block; background: transparent; color: #b6b6c0; border: 1px solid rgba(255,255,255,0.14); border-radius: 12px; padding: 15px; font-weight: 600; font-size: 15px;" style-hover="border-color: rgba(255,255,255,0.4); color: #F4F4F6;">View my profile</a>
    </div>
  </div>
</div>
</body>
</html>
