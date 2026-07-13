<?php
// Public authentication page.
require_once __DIR__ . '/../includes/auth.php';

// Server-side error messages passed via ?error=...
$errorMsg = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'incorrect_password':
            $errorMsg = 'Incorrect password. Please try again.';
            break;
        case 'email_not_found':
            $errorMsg = 'No account found with that email address.';
            break;
        case 'reset_expired':
            $errorMsg = 'That reset link has expired. Please request a new one.';
            break;
        case 'reset_invalid':
            $errorMsg = 'That reset link is invalid. Please request a new one.';
            break;
        default:
            $errorMsg = 'Unable to log in. Please check your details and try again.';
    }
}

// Success notices
$noticeMsg = '';
if (isset($_GET['reset'])) {
    $noticeMsg = 'Password updated. You can now log in.';
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log in — Pro Gym</title>
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
  input::placeholder { color: #55555f; }
  input:focus { outline: none; border-color: #D4FF3D !important; }
  @keyframes floatUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
</style>
</helmet>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased; display: grid; grid-template-columns: 1fr 1fr;">
  <div style="position: relative; background-image: linear-gradient(180deg, rgba(11,11,14,0.35), rgba(11,11,14,0.85)), url('../images/carousel-1.jpg'); background-size: cover; background-position: center; padding: 48px; display: flex; flex-direction: column; justify-content: space-between;">
    <a href="MainPage.php" style="display: flex; align-items: center; gap: 11px; color: #F4F4F6;">
      <img src="../images/1.png" style="height: 30px; filter: invert(1) brightness(1.25);" alt="">
      <span style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 18px;">PRO GYM</span>
    </a>
    <div>
      <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 40px; font-weight: 700; line-height: 1.05; letter-spacing: -0.02em; margin: 0 0 12px; max-width: 380px;">Every rep counts. We count them for you.</h2>
      <p style="font-size: 16px; color: #c4c4cc; margin: 0; max-width: 360px;">Join 12,000+ members training smarter across Cairo.</p>
    </div>
  </div>
  <div style="display: flex; align-items: center; justify-content: center; padding: 48px;">
    <form action="../handlers/handle_login.php" method="POST" style="width: 100%; max-width: 380px; animation: floatUp 0.5s ease both;">
      <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 34px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 8px;">Welcome back</h1>
      <p style="font-size: 15px; color: #9a9aa5; margin: 0 0 32px;">Log in to pick up where you left off.</p>
      <?php echo csrf_field(); ?>
      <?php if ($noticeMsg !== ''): ?>
      <div style="display: flex; align-items: center; gap: 10px; background: rgba(212,255,61,0.08); border: 1px solid rgba(212,255,61,0.35); color: #cfe98a; border-radius: 12px; padding: 13px 15px; font-size: 14px; margin-bottom: 18px;">
        <span><?php echo htmlspecialchars($noticeMsg); ?></span>
      </div>
      <?php endif; ?>
      <?php if ($errorMsg !== ''): ?>
      <div style="display: flex; align-items: center; gap: 10px; background: rgba(255,90,90,0.08); border: 1px solid rgba(255,90,90,0.35); color: #ff9a8f; border-radius: 12px; padding: 13px 15px; font-size: 14px; margin-bottom: 18px;">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" style="flex-shrink:0;"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v6M12 16.5v.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        <span><?php echo htmlspecialchars($errorMsg); ?></span>
      </div>
      <?php endif; ?>
      <input name="email" type="email" placeholder="Email address" required style="width: 100%; background: #151519; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px 16px; color: #F4F4F6; font-size: 15px; margin-bottom: 16px;">
      <input name="password" type="password" placeholder="Password" required style="width: 100%; background: #151519; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px 16px; color: #F4F4F6; font-size: 15px; margin-bottom: 14px;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 22px;">
        <label style="display: flex; align-items: center; gap: 8px; font-size: 13.5px; color: #b6b6c0; cursor: pointer;"><input type="checkbox" style="accent-color: #D4FF3D; width: 15px; height: 15px;"> Remember me</label>
        <a href="Forget.php" style="font-size: 13.5px; color: #D4FF3D; font-weight: 600;">Forgot password?</a>
      </div>
      <button type="submit" style="display: block; width: 100%; text-align: center; background: #D4FF3D; color: #0B0B0E; border: none; border-radius: 12px; padding: 16px; font-weight: 700; font-size: 15.5px; margin-bottom: 18px; cursor: pointer; font-family: inherit;" style-hover="background: #e6ff7a;">Log in</button>
      <p style="text-align: center; font-size: 14px; color: #9a9aa5; margin: 0;">Don't have an account? <a href="Register.php" style="color: #D4FF3D; font-weight: 700;">Register</a></p>
    </form>
  </div>
</div>
</x-dc>
</body>
</html>
