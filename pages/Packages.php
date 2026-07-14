<?php
// Plan subscription grid — members only.
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Plans + features from the plan repository (was hardcoded in JS).
$planData = app('plans')->allActiveWithFeatures();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Plans — Pro Gym</title>
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
      <a href="Packages.php" style="font-size: 14.5px; font-weight: 500; color: #F4F4F6;">Plans</a>
      <a href="Trainer.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Trainers</a>
      <a href="Dashboard.php" style="font-size: 14.5px; font-weight: 500; color: #b6b6c0;" style-hover="color: #F4F4F6;">Dashboard</a>
    </div>
    <div style="display: flex; align-items: center; gap: 12px;">
      <a href="Profile.php" style="display: inline-flex; align-items: center; background: transparent; color: #F4F4F6; border: 1px solid rgba(255,255,255,0.16); border-radius: 999px; padding: 10px 20px; font-weight: 600; font-size: 14px; white-space: nowrap;" style-hover="border-color: rgba(255,255,255,0.4);">My profile</a>
      <a href="Dashboard.php" style="display: inline-flex; align-items: center; background: #D4FF3D; color: #0B0B0E; border-radius: 999px; padding: 11px 22px; font-weight: 700; font-size: 14px;" style-hover="background: #e6ff7a;">Dashboard</a>
    </div>
  </nav>

  <section style="padding: 72px 48px 100px; max-width: 1240px; margin: 0 auto;">
    <div style="text-align: center; margin-bottom: 44px;">
      <div style="font-size: 12.5px; letter-spacing: 0.18em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 14px;">Membership</div>
      <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 52px; font-weight: 700; letter-spacing: -0.03em; margin: 0 0 14px;">Pick your pace.</h1>
      <p style="font-size: 17px; color: #9a9aa5; margin: 0 0 30px;">Every plan includes access to all 9 Cairo branches and the Pro Gym app.</p>
      <div style="display: inline-flex; padding: 5px; background: #151519; border: 1px solid rgba(255,255,255,0.08); border-radius: 999px;">
        <button onClick="{{ setMonthly }}" style="{{ monthlyBtnStyle }}">Monthly</button>
        <button onClick="{{ setAnnual }}" style="{{ annualBtnStyle }}">Annual · save 17%</button>
      </div>
    </div>
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; align-items: stretch;">
      <sc-for list="{{ plans }}" as="p" hint-placeholder-count="5">
        <div style="{{ p.cardStyle }}">
          <sc-if value="{{ p.popular }}" hint-placeholder-val="{{ true }}"><div style="position: absolute; top: -11px; left: 50%; transform: translateX(-50%); background: #D4FF3D; color: #0B0B0E; font-size: 11px; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; padding: 5px 12px; border-radius: 999px;">Most popular</div></sc-if>
          <div style="font-size: 12px; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 700; color: {{ p.accent }}; margin-bottom: 14px;">{{ p.name }}</div>
          <div style="display: flex; align-items: baseline; gap: 4px;">
            <span style="font-family: 'Space Grotesk', sans-serif; font-size: 38px; font-weight: 700; letter-spacing: -0.02em;">{{ p.priceDisplay }}</span>
            <span style="font-size: 13px; color: #8a8a95;">EGP/mo</span>
          </div>
          <div style="font-size: 12.5px; color: #6a6a74; margin: 4px 0 20px; min-height: 16px;">{{ p.billNote }}</div>
          <form action="../handlers/process_subscription.php" method="POST">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="plan" value="{{ p.name }}">
            <button type="submit" style="{{ p.btnStyle }}">Choose {{ p.name }}</button>
          </form>
          <div style="height: 1px; background: rgba(255,255,255,0.07); margin: 22px 0;"></div>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
            <sc-for list="{{ p.features }}" as="feat" hint-placeholder-count="4">
              <li style="display: flex; gap: 9px; font-size: 13.5px; color: #c4c4cc; line-height: 1.4;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" style="flex-shrink: 0; margin-top: 1px;"><path d="M20 6L9 17l-5-5" stroke="#D4FF3D" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                {{ feat }}
              </li>
            </sc-for>
          </ul>
        </div>
      </sc-for>
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
  state = { billing: 'monthly' };
  renderVals() {
    const accent = '#D4FF3D';
    const annual = this.state.billing === 'annual';
    const planData = <?php echo json_encode($planData); ?>;
    const plans = planData.map((p) => {
      const disp = annual ? Math.round(p.price * 10 / 12) : p.price;
      return {
        ...p,
        accent: p.popular ? accent : '#c4c4cc',
        priceDisplay: disp.toLocaleString(),
        billNote: annual ? 'billed annually · 2 months free' : 'billed monthly',
        cardStyle: `position: relative; background: ${p.popular ? 'linear-gradient(180deg,#191c10,#151519)' : '#141418'}; border: 1px solid ${p.popular ? 'rgba(212,255,61,0.45)' : 'rgba(255,255,255,0.08)'}; border-radius: 18px; padding: 26px 22px; display: flex; flex-direction: column;`,
        btnStyle: `display: block; text-align: center; background: ${p.popular ? accent : 'rgba(255,255,255,0.06)'}; color: ${p.popular ? '#0B0B0E' : '#F4F4F6'}; border: 1px solid ${p.popular ? accent : 'rgba(255,255,255,0.14)'}; border-radius: 10px; padding: 12px; font-weight: 700; font-size: 14px; width: 100%; cursor: pointer; font-family: inherit;`,
      };
    });
    const segOn = 'background: #D4FF3D; color: #0B0B0E; border: none; border-radius: 999px; padding: 10px 20px; font-weight: 700; font-size: 14px; cursor: pointer;';
    const segOff = 'background: transparent; color: #9a9aa5; border: none; border-radius: 999px; padding: 10px 20px; font-weight: 600; font-size: 14px; cursor: pointer;';
    return {
      plans,
      monthlyBtnStyle: annual ? segOff : segOn,
      annualBtnStyle: annual ? segOn : segOff,
      setMonthly: () => this.setState({ billing: 'monthly' }),
      setAnnual: () => this.setState({ billing: 'annual' }),
    };
  }
}
</script>
</body>
</html>
