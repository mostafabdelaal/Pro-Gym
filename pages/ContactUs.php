<?php
// Contact page — public. Restyled into the Pro Gym dark design system.
require_once __DIR__ . '/../includes/auth.php';

$channels = [
    ['label' => 'Email',    'value' => 'hello@progym.eg',        'href' => 'mailto:hello@progym.eg'],
    ['label' => 'Phone',    'value' => '+20 11 1234 5678',       'href' => 'tel:+201112345678'],
    ['label' => 'WhatsApp', 'value' => 'Chat with the front desk','href' => 'https://wa.me/201112345678'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Contact — Pro Gym</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0B0B0E; }
  a { text-decoration: none; }
  ::selection { background: #D4FF3D; color: #0B0B0E; }
  input::placeholder, textarea::placeholder { color: #55555f; }
  input:focus, textarea:focus { outline: none; border-color: #D4FF3D !important; }
  @keyframes floatUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
  @media (max-width: 860px) {
    .nav-links { display: none !important; }
    .contact-grid { grid-template-columns: 1fr !important; gap: 40px !important; }
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
      <a href="AboutUs.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">About</a>
      <a href="ContactUs.php" style="font-size: 14.5px; font-weight: 500; color: #F4F4F6;">Contact</a>
    </div>
    <a href="Register.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 11px 22px; font-weight: 700; font-size: 14px;" style-hover="background: #e6ff7a;">Join now</a>
  </nav>

  <section style="max-width: 1100px; margin: 0 auto; padding: 80px 48px 100px;">
    <div class="contact-grid" style="display: grid; grid-template-columns: 0.85fr 1.15fr; gap: 64px; align-items: start; animation: floatUp 0.6s ease both;">
      <!-- Left: how to reach us -->
      <div>
        <div style="font-size: 12.5px; letter-spacing: 0.18em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 16px;">Contact</div>
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 44px; font-weight: 700; letter-spacing: -0.03em; margin: 0 0 16px; line-height: 1.05;">Talk to a human.</h1>
        <p style="font-size: 16px; line-height: 1.65; color: #9a9aa5; margin: 0 0 36px;">Questions about membership, branches, or bringing a friend? The front desk answers fast.</p>
        <div style="display: flex; flex-direction: column; gap: 14px;">
          <?php foreach ($channels as $ch): ?>
          <a href="<?php echo htmlspecialchars($ch['href']); ?>" style="display: flex; align-items: center; justify-content: space-between; background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 18px 20px;" style-hover="border-color: rgba(212,255,61,0.35);">
            <div>
              <div style="font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #8a8a95; margin-bottom: 4px;"><?php echo htmlspecialchars($ch['label']); ?></div>
              <div style="font-size: 15.5px; font-weight: 600; color: #F4F4F6;"><?php echo htmlspecialchars($ch['value']); ?></div>
            </div>
            <span style="color: #D4FF3D; font-size: 18px;">→</span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Right: message form -->
      <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 34px;">
        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; margin: 0 0 22px;">Send a message</h2>
        <form action="ContactUs.php" method="POST" style="display: flex; flex-direction: column; gap: 16px;">
          <?php echo csrf_field(); ?>
          <div style="display: flex; gap: 14px;">
            <input name="first_name" placeholder="First name" required style="width: 100%; background: #0F0F13; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: #F4F4F6; font-size: 15px; font-family: inherit;">
            <input name="last_name" placeholder="Last name" required style="width: 100%; background: #0F0F13; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: #F4F4F6; font-size: 15px; font-family: inherit;">
          </div>
          <input name="email" type="email" placeholder="Email address" required style="width: 100%; background: #0F0F13; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: #F4F4F6; font-size: 15px; font-family: inherit;">
          <input name="phone" placeholder="Mobile number" style="width: 100%; background: #0F0F13; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: #F4F4F6; font-size: 15px; font-family: inherit;">
          <textarea name="message" placeholder="How can we help?" rows="4" required style="width: 100%; background: #0F0F13; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: #F4F4F6; font-size: 15px; font-family: inherit; resize: vertical;"></textarea>
          <button type="submit" style="align-self: flex-start; background: #D4FF3D; color: #0B0B0E; border: none; border-radius: 12px; padding: 14px 28px; font-weight: 700; font-size: 15px; cursor: pointer; font-family: inherit;" style-hover="background: #e6ff7a;">Send message</button>
        </form>
      </div>
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
