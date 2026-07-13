<?php
// Checkout / payment details — members only.
require_once __DIR__ . '/../includes/auth.php';
$conn = db();
require_login();

$member = current_member($conn);
if (!$member) {
    header("Location: LoginPage.php");
    exit();
}
$memberId = (int) $member['id'];

// Pull the plan the member selected so the summary reflects reality.
$stmt = $conn->prepare(
    "SELECT p.code, p.monthly_price
     FROM subscriptions s
     JOIN plans p ON p.id = s.plan_id
     WHERE s.member_id = ?
     ORDER BY (s.status = 'pending') DESC, s.created_at DESC
     LIMIT 1"
);
$stmt->bind_param("i", $memberId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    // No plan chosen yet — send them to pick one first.
    header("Location: Packages.php");
    exit();
}

$selectedPlan = strtoupper($row['code']);
$planPrice = (float) $row['monthly_price'];
$vat = round($planPrice * 0.14, 2);
$total = $planPrice + $vat;
$planLabel = ucfirst(strtolower($selectedPlan)) . ' plan';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Checkout — Pro Gym</title>
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
</style>
</helmet>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased; padding: 28px 48px 60px;">
  <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 36px;">
    <a href="Packages.php" style="display: flex; align-items: center; gap: 7px; font-size: 14px; color: #b6b6c0;" style-hover="color: #F4F4F6;">← Back to plans</a>
  </div>
  <div style="max-width: 980px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
    <div>
      <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 34px; font-weight: 700; letter-spacing: -0.02em; margin: 0 0 26px;">Checkout</h1>
      <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 26px; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 18px; border-bottom: 1px solid rgba(255,255,255,0.07);">
          <div>
            <div style="font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; color: #D4FF3D; font-weight: 700;"><?php echo htmlspecialchars($planLabel); ?></div>
            <div style="font-size: 14px; color: #9a9aa5; margin-top: 4px;">Monthly · all branches</div>
          </div>
          <div style="font-family: 'Space Grotesk', sans-serif; font-size: 24px; font-weight: 700;">EGP <?php echo number_format($planPrice); ?></div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 12px; padding: 18px 0; font-size: 14px; color: #b6b6c0;">
          <div style="display: flex; justify-content: space-between;"><span>Subtotal</span><span>EGP <?php echo number_format($planPrice); ?></span></div>
          <div style="display: flex; justify-content: space-between;"><span>VAT (14%)</span><span>EGP <?php echo number_format($vat); ?></span></div>
        </div>
        <div style="display: flex; justify-content: space-between; align-items: baseline; padding-top: 18px; border-top: 1px solid rgba(255,255,255,0.07);">
          <span style="font-size: 15px; font-weight: 700;">Total due today</span>
          <span style="font-family: 'Space Grotesk', sans-serif; font-size: 28px; font-weight: 700;">EGP <?php echo number_format($total); ?></span>
        </div>
      </div>
      <div style="display: flex; align-items: center; gap: 10px; font-size: 13px; color: #8a8a95;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><rect x="4" y="10" width="16" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10V7a4 4 0 018 0v3" stroke="currentColor" stroke-width="1.8"/></svg>
        Secured with 256-bit encryption
      </div>
    </div>
    <div>
      <div style="background: linear-gradient(135deg,#22242a,#141418); border: 1px solid rgba(255,255,255,0.1); border-radius: 18px; padding: 24px; margin-bottom: 20px; box-shadow: 0 20px 50px -20px rgba(0,0,0,0.6);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
          <div style="width: 42px; height: 30px; border-radius: 6px; background: linear-gradient(135deg,#f0c34e,#c99a2e);"></div>
          <img src="../images/1.png" style="height: 18px; filter: invert(1) brightness(1.2);" alt="">
        </div>
        <div style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; letter-spacing: 0.16em; margin-bottom: 20px;">•••• •••• •••• ••••</div>
        <div style="display: flex; justify-content: space-between; font-size: 12px; color: #9a9aa5; text-transform: uppercase; letter-spacing: 0.06em;"><span>Card holder</span><span>Expires</span></div>
      </div>
      <form action="../handlers/handle_payment.php" method="POST" style="display: flex; flex-direction: column; gap: 14px;">
        <?php echo csrf_field(); ?>
        <div>
          <label style="font-size: 12.5px; color: #8a8a95; display: block; margin-bottom: 7px;">Card number</label>
          <div style="display: flex; gap: 10px;">
            <input name="card_id1" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="4921" required style="width: 100%; text-align: center; letter-spacing: 0.08em; background: #141418; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 8px; color: #F4F4F6; font-size: 15px;">
            <input name="card_id2" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="0000" required style="width: 100%; text-align: center; letter-spacing: 0.08em; background: #141418; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 8px; color: #F4F4F6; font-size: 15px;">
            <input name="card_id3" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="0000" required style="width: 100%; text-align: center; letter-spacing: 0.08em; background: #141418; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 8px; color: #F4F4F6; font-size: 15px;">
            <input name="card_id4" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" placeholder="0000" required style="width: 100%; text-align: center; letter-spacing: 0.08em; background: #141418; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 8px; color: #F4F4F6; font-size: 15px;">
          </div>
        </div>
        <div>
          <label style="font-size: 12.5px; color: #8a8a95; display: block; margin-bottom: 7px;">Card holder name</label>
          <input name="card_holderName" placeholder="NOUR HASSAN" required style="width: 100%; background: #141418; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: #F4F4F6; font-size: 15px;">
        </div>
        <div style="display: flex; gap: 14px;">
          <div style="flex: 1;"><label style="font-size: 12.5px; color: #8a8a95; display: block; margin-bottom: 7px;">Expiry</label><input name="expiry" placeholder="MM / YY" maxlength="7" required style="width: 100%; background: #141418; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: #F4F4F6; font-size: 15px;"></div>
          <div style="flex: 1;"><label style="font-size: 12.5px; color: #8a8a95; display: block; margin-bottom: 7px;">CVV</label><input name="cvv" type="password" inputmode="numeric" maxlength="4" placeholder="•••" required style="width: 100%; background: #141418; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: #F4F4F6; font-size: 15px;"></div>
        </div>
        <button type="submit" style="display: block; width: 100%; text-align: center; background: #D4FF3D; color: #0B0B0E; border: none; border-radius: 12px; padding: 16px; font-weight: 700; font-size: 15.5px; margin-top: 6px; cursor: pointer; font-family: inherit;" style-hover="background: #e6ff7a;">Pay EGP <?php echo number_format($total); ?> →</button>
      </form>
    </div>
  </div>
</div>
</x-dc>
</body>
</html>
