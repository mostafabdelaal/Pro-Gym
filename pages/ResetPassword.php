<?php
// Password reset — step 2: set a new password using a token from the link.
require_once __DIR__ . '/../includes/auth.php';

$token = trim($_GET['token'] ?? '');
$error = $_GET['error'] ?? '';
$errorMsg = '';
if ($error === 'weak_password') {
    $errorMsg = 'Password must be at least 8 characters.';
} elseif ($error === 'server') {
    $errorMsg = 'Something went wrong. Please try again.';
}

if ($token === '') {
    header('Location: LoginPage.php?error=reset_invalid');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Choose a new password — Pro Gym</title>
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
    <h1 style="font-size: 30px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 8px;">New password</h1>
    <p style="font-size: 15px; color: #9a9aa5; margin: 0 0 30px;">Choose a strong password of at least 8 characters.</p>

    <?php if ($errorMsg !== ''): ?>
      <div style="background: rgba(255,90,90,0.08); border: 1px solid rgba(255,90,90,0.35); color: #ff9a8f; border-radius: 12px; padding: 13px 15px; font-size: 14px; margin-bottom: 18px;">
        <?php echo htmlspecialchars($errorMsg); ?>
      </div>
    <?php endif; ?>

    <form action="../handlers/handle_password_change.php" method="POST">
      <?php echo csrf_field(); ?>
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($token, ENT_QUOTES); ?>">
      <input name="password" type="password" placeholder="New password" required minlength="8" style="width: 100%; background: #151519; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 15px 16px; color: #F4F4F6; font-size: 15px; margin-bottom: 18px;">
      <button type="submit" style="display: block; width: 100%; background: #D4FF3D; color: #0B0B0E; border: none; border-radius: 12px; padding: 16px; font-weight: 700; font-size: 15.5px; cursor: pointer;">Update password</button>
    </form>
  </div>
</div>
</body>
</html>
