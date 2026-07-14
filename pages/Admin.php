<?php
// Admin back-office view — requires the admin role (RBAC).
require_once __DIR__ . '/../includes/auth.php';
$conn = db();
require_admin($conn);

// Member roster for the "Members" tab, from the member repository.
$members = [];
foreach (app('members')->allWithBranchAndPlan() as $row) {
    $fn = $row['first_name'] ?? '';
    $ln = $row['last_name'] ?? '';
    $name = trim($fn . ' ' . $ln);
    if ($name === '') $name = $row['email'] ?? 'Member';
    $ini = strtoupper(substr($fn, 0, 1) . substr($ln, 0, 1));
    if (trim($ini) === '') $ini = strtoupper(substr($name, 0, 2));
    $members[] = [
        'name'     => $name,
        'plan'     => !empty($row['plan']) ? strtoupper(trim($row['plan'])) : 'NONE',
        'branch'   => $row['branch'] ?? '—',
        'status'   => 'Active',
        'joined'   => !empty($row['created_at']) ? date('M Y', strtotime($row['created_at'])) : '—',
        'initials' => $ini,
    ];
}
$totalMembers = count($members);

// --- Real analytics for the Overview tab (were hardcoded) -----------------
$monthlyRevenue = (float) (($conn->query(
    "SELECT COALESCE(SUM(total),0) r FROM payments
     WHERE status = 'paid'
       AND YEAR(paid_at) = YEAR(CURDATE()) AND MONTH(paid_at) = MONTH(CURDATE())"
)->fetch_assoc()['r']) ?? 0);

$activeSubs = (int) (($conn->query(
    "SELECT COUNT(*) c FROM subscriptions WHERE status = 'active'"
)->fetch_assoc()['c']) ?? 0);

$paidPayments = (int) (($conn->query(
    "SELECT COUNT(*) c FROM payments WHERE status = 'paid'"
)->fetch_assoc()['c']) ?? 0);

$revenueByPlan = [];
$rbpRes = $conn->query(
    "SELECT p.name, COALESCE(SUM(pay.total),0) rev
     FROM plans p
     LEFT JOIN subscriptions s ON s.plan_id = p.id
     LEFT JOIN payments pay ON pay.subscription_id = s.id AND pay.status = 'paid'
     GROUP BY p.id
     ORDER BY p.sort_order, p.id"
);
$maxRev = 0;
if ($rbpRes) {
    while ($r = $rbpRes->fetch_assoc()) {
        $rev = (float) $r['rev'];
        $maxRev = max($maxRev, $rev);
        $revenueByPlan[] = ['name' => $r['name'], 'rev' => $rev];
    }
}
foreach ($revenueByPlan as &$r) {
    $r['pct'] = $maxRev > 0 ? (int) round($r['rev'] / $maxRev * 100) : 0;
    $r['rev'] = 'EGP ' . number_format($r['rev']);
}
unset($r);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin — Pro Gym</title>
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
  @keyframes floatUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
</style>
</helmet>
<div style="min-height: 100vh; background: #0B0B0E; color: #F4F4F6; font-family: 'Manrope', sans-serif; -webkit-font-smoothing: antialiased; display: flex;">
  <aside style="width: 250px; flex-shrink: 0; border-right: 1px solid rgba(255,255,255,0.07); padding: 24px 18px; display: flex; flex-direction: column; position: sticky; top: 0; height: 100vh;">
    <div style="display: flex; align-items: center; gap: 10px; padding: 6px 8px 22px;">
      <img src="../images/1.png" style="height: 26px; filter: invert(1) brightness(1.25);" alt="">
      <span style="font-family: 'Space Grotesk', sans-serif; font-weight: 700; font-size: 16px;">PRO GYM</span>
      <span style="font-size: 10.5px; font-weight: 700; letter-spacing: 0.08em; color: #0B0B0E; background: #D4FF3D; padding: 2px 7px; border-radius: 6px;">ADMIN</span>
    </div>
    <nav style="display: flex; flex-direction: column; gap: 4px;">
      <div onClick="{{ setAdminOverview }}" style="{{ nav.overview }}"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="3" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="3" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/><rect x="14" y="14" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.8"/></svg>Overview</div>
      <div onClick="{{ setAdminMembers }}" style="{{ nav.members }}"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.8"/><path d="M4.5 20c0-3.5 3.4-6 7.5-6s7.5 2.5 7.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Members</div>
      <div onClick="{{ setAdminPlans }}" style="{{ nav.plans }}"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M6.5 6.5v11M4 9v5M17.5 6.5v11M20 9v5M6.5 12h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Plans</div>
      <div onClick="{{ setAdminRevenue }}" style="{{ nav.revenue }}"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><path d="M4 20V4M4 20h16M8 16l4-5 3 3 5-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>Revenue</div>
      <div onClick="{{ setAdminClasses }}" style="{{ nav.classes }}"><svg width="19" height="19" viewBox="0 0 24 24" fill="none"><rect x="3.5" y="5" width="17" height="15" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 9.5h17M8 3.5v3M16 3.5v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>Classes</div>
    </nav>
    <div style="margin-top: auto;">
      <a href="Dashboard.php" style="display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 11px; border: 1px dashed rgba(255,255,255,0.16); font-size: 13.5px; color: #a6a6b0;" style-hover="border-color: rgba(212,255,61,0.4); color: #F4F4F6;">← Member app</a>
    </div>
  </aside>

  <main style="flex: 1; min-width: 0;">
    <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 40px; border-bottom: 1px solid rgba(255,255,255,0.07); position: sticky; top: 0; background: rgba(11,11,14,0.8); backdrop-filter: blur(16px); z-index: 20;">
      <div style="font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700;">{{ adminPageTitle }}</div>
      <div style="display: flex; align-items: center; gap: 12px;">
        <span style="font-size: 13.5px; color: #8a8a95;"><?php echo date('D, d M Y'); ?></span>
        <div style="width: 38px; height: 38px; border-radius: 50%; background: #22242a; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;">MA</div>
      </div>
    </div>

    <sc-if value="{{ adminOverview }}" hint-placeholder-val="{{ true }}">
    <div style="padding: 32px 40px 60px; animation: floatUp 0.4s ease both;">
      <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 16px;">
        <sc-for list="{{ adminStats }}" as="s" hint-placeholder-count="4">
          <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; padding: 22px;">
            <div style="font-size: 13px; color: #8a8a95; margin-bottom: 12px;">{{ s.label }}</div>
            <div style="font-family: 'Space Grotesk', sans-serif; font-size: 25px; font-weight: 700; margin-bottom: 8px; white-space: nowrap; letter-spacing: -0.01em;">{{ s.value }}</div>
            <div style="font-size: 12.5px; color: #D4FF3D; font-weight: 600;">{{ s.trend }}</div>
          </div>
        </sc-for>
      </div>
      <div style="display: grid; grid-template-columns: 3fr 2fr; gap: 16px;">
        <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; padding: 24px;">
          <div style="font-size: 16px; font-weight: 700; margin-bottom: 20px;">Revenue by plan</div>
          <div style="display: flex; flex-direction: column; gap: 16px;">
            <sc-for list="{{ revenueByPlan }}" as="r" hint-placeholder-count="5">
              <div>
                <div style="display: flex; justify-content: space-between; font-size: 13.5px; margin-bottom: 7px;"><span style="font-weight: 600;">{{ r.name }}</span><span style="color: #b6b6c0;">{{ r.rev }}</span></div>
                <div style="height: 8px; background: rgba(255,255,255,0.07); border-radius: 999px; overflow: hidden;"><div style="{{ r.barStyle }}"></div></div>
              </div>
            </sc-for>
          </div>
        </div>
        <div style="background: linear-gradient(135deg,#1a1d10,#141418); border: 1px solid rgba(212,255,61,0.2); border-radius: 18px; padding: 24px; display: flex; flex-direction: column; justify-content: center;">
          <div style="font-size: 12.5px; letter-spacing: 0.14em; text-transform: uppercase; color: #D4FF3D; font-weight: 700; margin-bottom: 10px;">Retention</div>
          <div style="font-family: 'Space Grotesk', sans-serif; font-size: 52px; font-weight: 700; line-height: 1;">88.4%</div>
          <p style="font-size: 14px; color: #b6b6c0; margin: 12px 0 0;">12-month member retention, up 4.2 points year over year.</p>
        </div>
      </div>
    </div>
    </sc-if>

    <sc-if value="{{ adminMembers }}" hint-placeholder-val="{{ true }}">
    <div style="padding: 32px 40px 60px; animation: floatUp 0.4s ease both;">
      <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px;">
        <div style="display: flex; align-items: center; gap: 8px; background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 10px 14px; width: 300px; color: #6a6a74; font-size: 14px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.8"/><path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
          Search members…
        </div>
        <button style="background: #D4FF3D; color: #0B0B0E; border: none; border-radius: 10px; padding: 11px 20px; font-weight: 700; font-size: 14px; cursor: pointer;">+ Add member</button>
      </div>
      <div style="background: #141418; border: 1px solid rgba(255,255,255,0.08); border-radius: 16px; overflow: hidden;">
        <div style="display: grid; grid-template-columns: 2.4fr 1.2fr 1.4fr 1.2fr 1fr; gap: 16px; padding: 14px 22px; border-bottom: 1px solid rgba(255,255,255,0.07); font-size: 11.5px; text-transform: uppercase; letter-spacing: 0.08em; color: #6a6a74;">
          <span>Member</span><span>Plan</span><span>Branch</span><span>Status</span><span>Joined</span>
        </div>
        <sc-for list="{{ members }}" as="m" hint-placeholder-count="6">
          <div style="display: grid; grid-template-columns: 2.4fr 1.2fr 1.4fr 1.2fr 1fr; gap: 16px; padding: 15px 22px; border-bottom: 1px solid rgba(255,255,255,0.05); align-items: center;" style-hover="background: rgba(255,255,255,0.02);">
            <div style="display: flex; align-items: center; gap: 12px;">
              <div style="width: 36px; height: 36px; border-radius: 50%; background: #22242a; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12.5px; color: #c4c4cc;">{{ m.initials }}</div>
              <span style="font-size: 14.5px; font-weight: 600;">{{ m.name }}</span>
            </div>
            <span style="font-size: 12.5px; font-weight: 700; color: #b6b6c0;">{{ m.plan }}</span>
            <span style="font-size: 14px; color: #b6b6c0;">{{ m.branch }}</span>
            <span style="{{ m.statusStyle }}"><span style="{{ m.dotStyle }}"></span>{{ m.status }}</span>
            <span style="font-size: 13.5px; color: #8a8a95;">{{ m.joined }}</span>
          </div>
        </sc-for>
      </div>
    </div>
    </sc-if>

    <sc-if value="{{ adminOther }}" hint-placeholder-val="{{ true }}">
    <div style="padding: 32px 40px; animation: floatUp 0.4s ease both;">
      <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 90px 20px; border: 1px dashed rgba(255,255,255,0.14); border-radius: 20px;">
        <div style="width: 60px; height: 60px; border-radius: 16px; background: rgba(212,255,61,0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; color: #D4FF3D; font-size: 24px;">✦</div>
        <h2 style="font-family: 'Space Grotesk', sans-serif; font-size: 23px; font-weight: 700; margin: 0 0 8px;">{{ adminEmpty }}</h2>
        <p style="font-size: 15px; color: #9a9aa5; margin: 0; max-width: 400px;">This module is part of the proposed admin build-out. Wire it to the members database to go live.</p>
      </div>
    </div>
    </sc-if>
  </main>
</div>
</x-dc>
<script type="text/x-dc" data-dc-script>
class Component extends DCLogic {
  state = { tab: 'overview' };
  renderVals() {
    const accent = '#D4FF3D';
    const at = this.state.tab;
    const navRow = (active) => `display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:11px;cursor:pointer;font-size:14.5px;font-weight:${active ? 700 : 500};color:${active ? '#0B0B0E' : '#a6a6b0'};background:${active ? accent : 'transparent'};`;
    const setTab = (k) => () => this.setState({ tab: k });
    const dbMembers = <?php echo json_encode($members); ?>;
    const members = dbMembers.map((m) => {
      const c = m.status === 'Active' ? '#D4FF3D' : m.status === 'Frozen' ? '#6ea8ff' : '#ff8f6e';
      return { ...m, statusStyle: `display:inline-flex;align-items:center;gap:6px;font-size:12.5px;font-weight:600;color:${c};`, dotStyle: `width:7px;height:7px;border-radius:50%;background:${c};` };
    });
    return {
      nav: {
        overview: navRow(at === 'overview'), members: navRow(at === 'members'),
        plans: navRow(at === 'plans'), revenue: navRow(at === 'revenue'), classes: navRow(at === 'classes'),
      },
      setAdminOverview: setTab('overview'), setAdminMembers: setTab('members'),
      setAdminPlans: setTab('plans'), setAdminRevenue: setTab('revenue'), setAdminClasses: setTab('classes'),
      adminOverview: at === 'overview', adminMembers: at === 'members',
      adminOther: !['overview', 'members'].includes(at),
      adminEmpty: { plans: 'Plan management', revenue: 'Revenue analytics', classes: 'Class scheduling' }[at] || '',
      adminPageTitle: { overview: 'Overview', members: 'Members', plans: 'Plans', revenue: 'Revenue', classes: 'Classes' }[at] || 'Overview',
      adminStats: [
        { label: 'Total members', value: <?php echo json_encode(number_format($totalMembers)); ?>, trend: 'live from DB' },
        { label: 'Monthly revenue', value: <?php echo json_encode('EGP ' . number_format($monthlyRevenue)); ?>, trend: 'this month' },
        { label: 'Active plans', value: <?php echo json_encode(number_format($activeSubs)); ?>, trend: 'live from DB' },
        { label: 'Payments', value: <?php echo json_encode(number_format($paidPayments)); ?>, trend: 'paid total' },
      ],
      revenueByPlan: (<?php echo json_encode($revenueByPlan); ?>)
        .map((r) => ({ ...r, barStyle: `width:${r.pct}%;height:100%;background:${accent};border-radius:999px;` })),
      members,
    };
  }
}
</script>
</body>
</html>
