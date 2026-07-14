<?php
// Trainer showcase — members only.
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Coaching team from the trainer repository (was hardcoded).
$trainers = app('trainers')->allActiveForShowcase();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Trainers — Pro Gym</title>
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
</style>
</helmet>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased;">
  <nav style="position: sticky; top: 0; z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 18px 48px; background: rgba(11,11,14,0.72); backdrop-filter: blur(18px); border-bottom: 1px solid rgba(255,255,255,0.07);">
    <a href="MainPage.php" style="display: flex; align-items: center; gap: 11px; color: #F4F4F6;">
      <img src="../images/1.png" style="height: 30px; width: auto; filter: invert(1) brightness(1.25) contrast(1.1);" alt="Pro Gym">
      <span style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 18px; letter-spacing: 0.04em;">PRO GYM</span>
    </a>
    <div style="display: flex; align-items: center; gap: 34px;">
      <a href="MainPage.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Home</a>
      <a href="Packages.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Plans</a>
      <a href="Trainer.php" style="font-size: 14.5px; font-weight: 500; color: #F4F4F6;">Trainers</a>
      <a href="Dashboard.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Dashboard</a>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="Profile.php" style="display: inline-flex; align-items: center; background: transparent; color: #F4F4F6; border: 1px solid rgba(255,255,255,0.16); border-radius: 999px; padding: 10px 20px; font-weight: 600; font-size: 14px; white-space: nowrap;" style-hover="border-color: rgba(255,255,255,0.4);">My profile</a>
      <a href="Dashboard.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 11px 22px; font-weight: 700; font-size: 14px;" style-hover="background: #e6ff7a;">Dashboard</a>
    </div>
  </nav>

  <section style="padding: 72px 48px 100px; max-width: 1200px; margin: 0 auto;">
    <div style="font-size: 12.5px; letter-spacing: 0.18em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 14px;">The team</div>
    <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 52px; font-weight: 700; letter-spacing: -0.03em; margin: 0 0 8px;">Coaches who care.</h1>
    <p style="font-size: 17px; color: #9a9aa5; margin: 0 0 48px; max-width: 560px;">Certified, obsessive about form, and matched to your goals. Book a session in-app anytime.</p>
    <?php if (empty($trainers)): ?>
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 80px 20px; border: 1px dashed rgba(255,255,255,0.14); border-radius: 20px;">
      <div style="width: 60px; height: 60px; border-radius: 16px; background: rgba(212,255,61,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; color: #D4FF3D; font-size: 24px;">✦</div>
      <h2 style="font-size: 22px; font-weight: 700; margin: 0 0 8px;">No trainers listed yet</h2>
      <p style="font-size: 15px; color: #9a9aa5; margin: 0; max-width: 380px;">Coaching staff will appear here once they're added.</p>
    </div>
    <?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
      <sc-for list="{{ trainers }}" as="t" hint-placeholder-count="5">
        <div style="background: #151519; border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; overflow: hidden;" style-hover="border-color: rgba(212,255,61,0.35);">
          <div style="height: 300px; background-image: {{ t.bg }}; background-size: cover; background-position: center top; position: relative;">
            <div style="position: absolute; inset: 0; background: linear-gradient(to top, #151519 4%, transparent 55%);"></div>
            <span style="position: absolute; top: 14px; left: 14px; background: rgba(11,11,14,0.7); backdrop-filter: blur(6px); border: 1px solid rgba(255,255,255,0.12); color: #D4FF3D; font-size: 11.5px; font-weight: 700; padding: 5px 11px; border-radius: 999px; letter-spacing: 0.05em;">{{ t.tag }}</span>
          </div>
          <div style="padding: 20px 22px 24px;">
            <h3 style="font-size: 20px; font-weight: 700; margin: 0 0 4px;">{{ t.name }}</h3>
            <p style="font-size: 14px; color: #9a9aa5; margin: 0 0 16px;">{{ t.role }}</p>
            <a href="Classes.php" style="display: block; text-align: center; background: rgba(255,255,255,0.05); color: #F4F4F6; border: 1px solid rgba(255,255,255,0.14); border-radius: 10px; padding: 11px; font-weight: 600; font-size: 14px;" style-hover="background: rgba(212,255,61,0.1); border-color: rgba(212,255,61,0.35);">Book session</a>
          </div>
        </div>
      </sc-for>
    </div>
    <?php endif; ?>
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
    // Trainers come from the database (see the PHP block above).
    const trainers = <?php echo json_encode($trainers); ?>;
    return { trainers };
  }
}
</script>
</body>
</html>
