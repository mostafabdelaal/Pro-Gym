<?php
// Branch locations — public. DB-driven, restyled into the Pro Gym dark system.
require_once __DIR__ . '/../includes/auth.php';

$branches = app('branches')->allActive();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Branches — Pro Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0B0B0E; }
  a { text-decoration: none; }
  ::selection { background: #D4FF3D; color: #0B0B0E; }
  ::-webkit-scrollbar { width: 10px; }
  ::-webkit-scrollbar-thumb { background: #26262e; border-radius: 8px; border: 2px solid #0B0B0E; }
  @keyframes floatUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
  .branch-card:hover { border-color: rgba(212,255,61,0.4); transform: translateY(-3px); }
  .branch-card { transition: border-color .2s, transform .2s; }
  @media (max-width: 820px) { .nav-links { display: none !important; } }
</style>
</head>
<body>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased;">
  <nav style="position: sticky; top: 0; z-index: 50; display: flex; align-items: center; justify-content: space-between; padding: 18px 48px; background: rgba(11,11,14,0.72); backdrop-filter: blur(18px); border-bottom: 1px solid rgba(255,255,255,0.07);">
    <a href="MainPage.php" style="display: flex; align-items: center; gap: 11px; color: #F4F4F6;">
      <img src="../images/1.png" style="height: 30px; width: auto; filter: invert(1) brightness(1.25) contrast(1.1);" alt="Pro Gym">
      <span style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 18px; letter-spacing: 0.04em;">PRO GYM</span>
    </a>
    <div class="nav-links" style="display: flex; align-items: center; gap: 34px;">
      <a href="MainPage.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Home</a>
      <a href="Packages.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Plans</a>
      <a href="Trainer.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Trainers</a>
      <a href="AboutUs.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">About</a>
      <a href="ContactUs.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Contact</a>
    </div>
    <a href="Register.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 11px 22px; font-weight: 700; font-size: 14px;" style-hover="background: #e6ff7a;">Join now</a>
  </nav>

  <section style="max-width: 1200px; margin: 0 auto; padding: 80px 48px 100px; animation: floatUp 0.6s ease both;">
    <div style="font-size: 12.5px; letter-spacing: 0.18em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 14px;">Locations</div>
    <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 52px; font-weight: 700; letter-spacing: -0.03em; margin: 0 0 8px;">One pass. Every branch.</h1>
    <p style="font-size: 17px; color: #9a9aa5; margin: 0 0 48px; max-width: 560px;">Your membership works at all <?php echo count($branches); ?> Pro Gym locations across Cairo. Train wherever the day takes you.</p>

    <?php if (empty($branches)): ?>
      <div style="border: 1px dashed rgba(255,255,255,0.14); border-radius: 20px; padding: 80px 20px; text-align: center; color: #9a9aa5;">No branches available yet.</div>
    <?php else: ?>
      <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 18px;">
        <?php foreach ($branches as $b): ?>
          <?php
          $img = $b['image_path'] ?? '';
          $tag = ($img !== '') ? '<a href="../' . htmlspecialchars($img) . '" target="_blank"' : '<div';
          $end = ($img !== '') ? '</a>' : '</div>';
          ?>
          <?php echo $tag; ?> class="branch-card" style="display: block; background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; overflow: hidden;">
            <div style="height: 150px; background: <?php echo $img !== '' ? "linear-gradient(rgba(11,11,14,0.15),rgba(11,11,14,0.55)), url('../" . htmlspecialchars($img) . "')" : 'linear-gradient(135deg,#1a1d10,#151519)'; ?>; background-size: cover; background-position: center; display: flex; align-items: flex-start; justify-content: flex-end; padding: 12px;">
              <span style="display: inline-flex; align-items: center; gap: 5px; background: rgba(11,11,14,0.7); backdrop-filter: blur(6px); border: 1px solid rgba(212,255,61,0.3); color: #D4FF3D; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none"><path d="M12 21s7-5.5 7-11a7 7 0 10-14 0c0 5.5 7 11 7 11z" stroke="currentColor" stroke-width="2"/><circle cx="12" cy="10" r="2.4" stroke="currentColor" stroke-width="2"/></svg>
                Cairo
              </span>
            </div>
            <div style="padding: 18px 20px 20px;">
              <h3 style="font-family: 'Space Grotesk', sans-serif; font-size: 19px; font-weight: 700; margin: 0 0 4px;"><?php echo htmlspecialchars($b['name']); ?> Branch</h3>
              <span style="font-size: 13px; color: #8a8a95;"><?php echo $img !== '' ? 'View location →' : 'Opening soon'; ?></span>
            </div>
          <?php echo $end; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <footer style="border-top: 1px solid rgba(255,255,255,0.07); padding: 44px 48px; display: flex; align-items: center; justify-content: space-between; color: #6a6a74; font-size: 13.5px; flex-wrap: wrap; gap: 16px;">
    <div style="display: flex; align-items: center; gap: 10px;">
      <img src="../images/1.png" style="height: 22px; filter: invert(1) brightness(1.2);" alt="">
      <span>© 2025 Pro Gym · Cairo, Egypt</span>
    </div>
    <div style="display: flex; gap: 24px; color: #6a6a74;"><span>Privacy</span><span>Terms</span><span>العربية</span></div>
  </footer>
</div>
</body>
</html>
