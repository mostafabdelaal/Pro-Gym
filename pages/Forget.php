<?php
// Password reset — step 1: request a reset link by email.
require_once __DIR__ . '/../includes/auth.php';

$sent    = isset($_GET['sent']);
$devLink = $_SESSION['dev_reset_link'] ?? '';
unset($_SESSION['dev_reset_link']); // one-shot display
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reset password — Pro Gym</title>
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0B0B0E; }
  a { text-decoration: none; }
  input::placeholder { color: #55555f; }
  input:focus { outline: none; border-color: #D4FF3D !important; }
  @keyframes floatUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
</style>
</head>
<body>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', system-ui, sans-serif; -webkit-font-smoothing: antialiased; display: flex; align-items: center; justify-content: center; padding: 48px;">
  <div style="width: 100%; max-width: 400px; animation: floatUp 0.5s ease both;">
    <a href="LoginPage.php" style="display: flex; align-items: center; gap: 11px; color: #F4F4F6; margin-bottom: 40px;">
      <img src="../images/1.png" style="height: 28px; filter: invert(1) brightness(1.25);" alt="">
      <span style="font-weight: 700; font-size: 18px;">PRO GYM</span>
    </a>
    <h1 style="font-size: 30px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 8px;">Reset password</h1>
    <p style="font-size: 15px; color: #9a9aa5; margin: 0 0 30px;">Enter your account email and we'll generate a reset link.</p>

    <?php if ($sent): ?>
      <div style="background: rgba(212,255,61,0.08); border: 1px solid rgba(212,255,61,0.35); color: #cfe98a; border-radius: 12px; padding: 14px 16px; font-size: 14px; margin-bottom: 18px;">
        If an account exists for that email, a reset link has been generated.
      </div>
      <?php if ($devLink !== ''): ?>
      <div style="background: #151519; border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; padding: 14px 16px; font-size: 13px; margin-bottom: 18px; word-break: break-all;">
        <div style="color: #8a8a95; text-transform: uppercase; letter-spacing: 0.08em; font-size: 11px; margin-bottom: 8px;">Dev mode — reset link (not emailed)</div>
        <a href="<?php echo htmlspecialchars($devLink, ENT_QUOTES); ?>" style="color: #D4FF3D; font-weight: 600;"><?php echo htmlspecialchars($devLink, ENT_QUOTES); ?></a>
      </div>
      <?php endif; ?>
    <?php endif; ?>

    <form action="../handlers/request_password_reset.php" method="POST">
      <?php echo csrf_field(); ?>
      <input name="email" type="email" placeholder="Email address" required style="width: 100%; background: #151519; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px 16px; color: #F4F4F6; font-size: 15px; margin-bottom: 18px;">
      <button type="submit" style="display: block; width: 100%; background: #D4FF3D; color: #0B0B0E; border: none; border-radius: 12px; padding: 16px; font-weight: 700; font-size: 15.5px; cursor: pointer; margin-bottom: 18px;">Generate reset link</button>
    </form>
    <p style="text-align: center; font-size: 14px; color: #9a9aa5; margin: 0;">Remembered it? <a href="LoginPage.php" style="color: #D4FF3D; font-weight: 700;">Log in</a></p>
  </div>
</div>
</body>
</html>
