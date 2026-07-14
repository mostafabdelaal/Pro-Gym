<?php
// About page — public. Restyled into the Pro Gym dark design system.
require_once __DIR__ . '/../includes/auth.php';

$branchCount  = app('branches')->countActive();
$trainerCount = app('trainers')->countActive();

$values = [
    ['title' => 'Programming over guesswork', 'body' => "Every plan is progressive and logged. You train with intent, and the app proves it week over week."],
    ['title' => 'Coaches who watch your form', 'body' => "Certified, obsessive about technique, and matched to your goals — not a rotating cast of strangers."],
    ['title' => 'One pass, nine branches', 'body' => "Sheraton to Madinaty, your membership travels with you across Cairo. Train wherever the day takes you."],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>About — Pro Gym</title>
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
  @media (max-width: 820px) {
    .nav-links { display: none !important; }
    .about-hero h1 { font-size: 48px !important; }
    .about-grid { grid-template-columns: 1fr !important; }
    .about-stats { flex-wrap: wrap !important; gap: 28px !important; }
  }
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
      <a href="AboutUs.php" style="font-size: 14.5px; font-weight: 500; color: #F4F4F6;">About</a>
      <a href="ContactUs.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Contact</a>
    </div>
    <a href="Register.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 11px 22px; font-weight: 700; font-size: 14px;" style-hover="background: #e6ff7a;">Join now</a>
  </nav>

  <!-- Hero: thesis -->
  <section class="about-hero" style="position: relative; padding: 96px 48px 72px; max-width: 1100px; margin: 0 auto; animation: floatUp 0.7s ease both;">
    <div style="font-size: 12.5px; letter-spacing: 0.18em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 20px;">About Pro Gym</div>
    <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 68px; line-height: 1.02; font-weight: 700; letter-spacing: -0.03em; margin: 0 0 24px; max-width: 860px;">Built in Cairo, for people who<span style="color: #D4FF3D;"> keep showing up.</span></h1>
    <p style="font-size: 19px; line-height: 1.6; color: #b6b6c0; max-width: 620px; margin: 0;">Pro Gym is Cairo's premium training club — smart programming, coaches who know your name, and a member app that turns every session into progress you can see.</p>
  </section>

  <!-- Stats band -->
  <section style="padding: 0 48px 84px;">
    <div class="about-stats" style="max-width: 1100px; margin: 0 auto; display: flex; gap: 64px; padding: 40px 44px; background: linear-gradient(120deg,#1a1d10,#151519); border: 1px solid rgba(212,255,61,0.2); border-radius: 22px;">
      <div>
        <div style="font-family: 'Space Grotesk', sans-serif; font-size: 44px; font-weight: 700; letter-spacing: -0.02em;">12k+</div>
        <div style="font-size: 13.5px; color: #8a8a95; margin-top: 4px;">Active members</div>
      </div>
      <div>
        <div style="font-family: 'Space Grotesk', sans-serif; font-size: 44px; font-weight: 700; letter-spacing: -0.02em;"><?php echo $branchCount; ?></div>
        <div style="font-size: 13.5px; color: #8a8a95; margin-top: 4px;">Cairo branches</div>
      </div>
      <div>
        <div style="font-family: 'Space Grotesk', sans-serif; font-size: 44px; font-weight: 700; letter-spacing: -0.02em;"><?php echo $trainerCount; ?>+</div>
        <div style="font-size: 13.5px; color: #8a8a95; margin-top: 4px;">Expert coaches</div>
      </div>
    </div>
  </section>

  <!-- Mission + values -->
  <section style="padding: 0 48px 100px;">
    <div class="about-grid" style="max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 56px; align-items: start;">
      <div style="position: sticky; top: 100px;">
        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 34px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 18px; line-height: 1.1;">What we believe</h2>
        <p style="font-size: 16px; line-height: 1.7; color: #9a9aa5; margin: 0;">Most gyms sell access to equipment. We build the habit that outlasts the January rush — because the people who train with a plan, and a coach who cares, are the ones still here next year.</p>
      </div>
      <div style="display: flex; flex-direction: column; gap: 16px;">
        <?php foreach ($values as $v): ?>
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 28px 30px;" style-hover="border-color: rgba(212,255,61,0.35);">
          <h3 style="font-size: 19px; font-weight: 700; margin: 0 0 10px;"><?php echo htmlspecialchars($v['title']); ?></h3>
          <p style="font-size: 14.5px; line-height: 1.65; color: #9a9aa5; margin: 0;"><?php echo htmlspecialchars($v['body']); ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section style="padding: 0 48px 100px;">
    <div style="max-width: 1100px; margin: 0 auto; background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 24px; padding: 56px; display: flex; align-items: center; justify-content: space-between; gap: 40px; flex-wrap: wrap;">
      <div>
        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 34px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 10px;">Come see for yourself.</h2>
        <p style="font-size: 16px; color: #b6b6c0; margin: 0;">Your first week is on us. No card required.</p>
      </div>
      <a href="Packages.php" style="flex-shrink: 0; display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 16px 34px; font-weight: 700; font-size: 16px;" style-hover="background: #e6ff7a;">View plans →</a>
    </div>
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
