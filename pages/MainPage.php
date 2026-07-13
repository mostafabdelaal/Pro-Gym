<?php
// Public guest landing page — no authentication required.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();

// Real counts for the hero band (branches/coaches were hardcoded).
$branchCount  = (int) (($conn->query("SELECT COUNT(*) c FROM branches WHERE is_active = 1")->fetch_assoc()['c']) ?? 0);
$trainerCount = (int) (($conn->query("SELECT COUNT(*) c FROM trainers WHERE is_active = 1")->fetch_assoc()['c']) ?? 0);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pro Gym — Train with intent</title>
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
  ::-webkit-scrollbar { width: 10px; height: 10px; }
  ::-webkit-scrollbar-thumb { background: #26262e; border-radius: 8px; border: 2px solid #0B0B0E; }
  @keyframes floatUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
</style>
</helmet>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased;">
  <nav style="position: sticky; top: 0; z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 18px 48px; background: rgba(11,11,14,0.72); backdrop-filter: blur(18px); border-bottom: 1px solid rgba(255,255,255,0.07);">
    <a href="MainPage.php" style="display: flex; align-items: center; gap: 11px; color: #F4F4F6;">
      <img src="../images/1.png" style="height: 30px; width: auto; filter: invert(1) brightness(1.25) contrast(1.1);" alt="Pro Gym">
      <span style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 18px; letter-spacing: 0.04em;">PRO GYM</span>
    </a>
    <div style="display: flex; align-items: center; gap: 34px;">
      <a href="MainPage.php" style="font-size: 14.5px; font-weight: 500; color: #F4F4F6;">Home</a>
      <a href="Packages.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Plans</a>
      <a href="Trainer.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Trainers</a>
      <a href="MainPage.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Branches</a>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="LoginPage.php" style="display: inline-flex; align-items: center; background: transparent; color: #F4F4F6; border: 1px solid rgba(255,255,255,0.16); border-radius: 999px; padding: 10px 20px; font-weight: 600; font-size: 14px; white-space: nowrap;" style-hover="border-color: rgba(255,255,255,0.4);">Log in</a>
      <a href="Register.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 11px 22px; font-weight: 700; font-size: 14px;" style-hover="background: #e6ff7a;">Join now</a>
    </div>
  </nav>

  <section style="position: relative; min-height: 90vh; display: flex; align-items: center; padding: 0 48px; overflow: hidden;">
    <div style="position: absolute; inset: 0; background-image: linear-gradient(90deg, rgba(11,11,14,0.96) 0%, rgba(11,11,14,0.7) 45%, rgba(11,11,14,0.35) 100%), url('../images/gymback.jpg'); background-size: cover; background-position: center; z-index: 0;"></div>
    <div style="position: relative; z-index: 2; max-width: 720px; animation: floatUp 0.9s ease both;">
      <div style="display: inline-flex; align-items: center; gap: 8px; padding: 7px 14px; border: 1px solid rgba(212,255,61,0.3); background: rgba(212,255,61,0.06); border-radius: 999px; margin-bottom: 26px;">
        <span style="width: 6px; height: 6px; border-radius: 50%; background: #D4FF3D; box-shadow: 0 0 8px #D4FF3D;"></span>
        <span style="font-size: 12.5px; letter-spacing: 0.14em; text-transform: uppercase; color: #D4FF3D; font-weight: 700;">9 branches across Cairo</span>
      </div>
      <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 82px; line-height: 0.98; font-weight: 700; letter-spacing: -0.03em; margin: 0 0 24px;">Train with<br>intent.<span style="color: #D4FF3D;"> Build strong.</span></h1>
      <p style="font-size: 19px; line-height: 1.6; color: #b6b6c0; max-width: 500px; margin: 0 0 38px;">Pro Gym is Cairo's premium training club — smart programming, world-class coaches, and a member app that actually tracks your progress.</p>
      <div style="display: flex; gap: 14px; align-items: center;">
        <a href="Register.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 16px 32px; font-weight: 700; font-size: 16px;" style-hover="background: #e6ff7a;">Start your journey →</a>
        <a href="Packages.php" style="display: inline-flex; align-items: center; background: rgba(255,255,255,0.06); color: #F4F4F6; border: 1px solid rgba(255,255,255,0.16); border-radius: 999px; padding: 15px 30px; font-weight: 600; font-size: 16px;" style-hover="background: rgba(255,255,255,0.12);">View plans</a>
      </div>
      <div style="display: flex; gap: 40px; margin-top: 56px;">
        <sc-for list="{{ heroStats }}" as="s" hint-placeholder-count="3">
          <div>
            <div style="font-family: 'Space Grotesk', sans-serif; font-size: 34px; font-weight: 700; letter-spacing: -0.02em;">{{ s.value }}</div>
            <div style="font-size: 13px; color: #8a8a95; margin-top: 2px;">{{ s.label }}</div>
          </div>
        </sc-for>
      </div>
    </div>
  </section>

  <section style="padding: 100px 48px; border-top: 1px solid rgba(255,255,255,0.06);">
    <div style="max-width: 1200px; margin: 0 auto;">
      <div style="font-size: 12.5px; letter-spacing: 0.18em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 14px;">Why Pro Gym</div>
      <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 44px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 56px; max-width: 640px; line-height: 1.05;">Everything a serious training habit needs — in one membership.</h2>
      <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <sc-for list="{{ features }}" as="f" hint-placeholder-count="3">
          <div style="background: #151519; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 30px;" style-hover="border-color: rgba(212,255,61,0.35);">
            <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(212,255,61,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; color: #D4FF3D; font-size: 20px;">{{ f.icon }}</div>
            <h3 style="font-size: 19px; font-weight: 700; margin: 0 0 9px;">{{ f.title }}</h3>
            <p style="font-size: 14.5px; line-height: 1.6; color: #9a9aa5; margin: 0;">{{ f.body }}</p>
          </div>
        </sc-for>
      </div>
    </div>
  </section>

  <section style="padding: 0 48px 100px;">
    <div style="max-width: 1200px; margin: 0 auto; background: linear-gradient(120deg, #1a1d10, #151519); border: 1px solid rgba(212,255,61,0.2); border-radius: 26px; padding: 64px; display: flex; align-items: center; justify-content: space-between; gap: 40px;">
      <div>
        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 40px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 12px;">Your first week is on us.</h2>
        <p style="font-size: 17px; color: #b6b6c0; margin: 0;">No card required. Cancel anytime. Cairo's best equipment awaits.</p>
      </div>
      <a href="Packages.php" style="flex-shrink: 0; display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 17px 36px; font-weight: 700; font-size: 16px;" style-hover="background: #e6ff7a;">Choose a plan</a>
    </div>
  </section>

  <footer style="border-top: 1px solid rgba(255,255,255,0.07); padding: 44px 48px; display: flex; align-items: center; justify-content: space-between; color: #6a6a74; font-size: 13.5px;">
    <div style="display: flex; align-items: center; gap: 10px;">
      <img src="../images/1.png" style="height: 22px; filter: invert(1) brightness(1.2);" alt="">
      <span>© 2025 Pro Gym · Cairo, Egypt</span>
    </div>
    <div style="display: flex; gap: 24px; color: #6a6a74;"><span>Privacy</span><span>Terms</span><span>العربية</span></div>
  </footer>
</div>
</x-dc>
<script type="text/x-dc" data-dc-script>
class Component extends DCLogic {
  renderVals() {
    return {
      heroStats: [
        { value: '12k+', label: 'Active members' },
        { value: <?php echo json_encode((string) $branchCount); ?>, label: 'Cairo branches' },
        { value: <?php echo json_encode($trainerCount . '+'); ?>, label: 'Expert coaches' },
      ],
      features: [
        { title: 'Smart programming', body: 'Progressive plans that adapt to your logged performance, week over week.', icon: '◱' },
        { title: 'Track everything', body: 'Log sets, reps and weight in seconds. See volume and PRs trend over time.', icon: '↗' },
        { title: '9 branches, one pass', body: 'Sheraton, Nasr City, Zamalek, Madinaty and more — your membership travels.', icon: '⚲' },
      ],
    };
  }
}
</script>
</body>
</html>
