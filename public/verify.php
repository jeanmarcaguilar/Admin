<?php
/**
 * Public Visitor QR Identity Page
 * ────────────────────────────────
 * NO LOGIN REQUIRED — accessible by anyone who scans a visitor QR code.
 * READ-ONLY: displays the visitor's current status. Never modifies any data.
 * Check-in / check-out can only be performed by admin staff via the admin panel.
 *
 * URL:  /Admin/public/verify.php?token=VIS-XXXXXX
 */

// No session, no auth — fully public, fully read-only
require_once __DIR__ . '/../config/db.php';

$token     = trim($_GET['token'] ?? '');
$visitor   = null;
$logData   = null;
$result    = 'error';   // 'currently_in' | 'currently_out' | 'blacklisted' | 'not_found' | 'error'
$error     = '';
$actionTime = date('F d, Y — h:i A');

// Base URL for assets (photos stored relative to Admin root)
$baseUrl = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($token === '') {
    $error  = 'No visitor token provided.';
    $result = 'error';
} else {
    try {
        $db = getDB();

        // 1. Fetch visitor — READ ONLY, no writes
        $stmt = $db->prepare("
            SELECT visitor_id, visitor_code, first_name, last_name,
                   company, photo_url, visit_count, is_blacklisted, visitor_type
            FROM visitors WHERE visitor_code = ? LIMIT 1
        ");
        $stmt->execute([$token]);
        $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$visitor) {
            $result = 'not_found';
            $error  = 'No visitor matches this QR code.';
        } elseif ((int)$visitor['is_blacklisted']) {
            $result = 'blacklisted';
        } else {
            // 2. Read current status — never write
            $stmt2 = $db->prepare("
                SELECT log_id, visit_code, purpose, host_name, check_in_time, check_out_time, status
                FROM visitor_logs
                WHERE visitor_id = ?
                ORDER BY log_id DESC LIMIT 1
            ");
            $stmt2->execute([$visitor['visitor_id']]);
            $lastLog = $stmt2->fetch(PDO::FETCH_ASSOC);

            if ($lastLog && $lastLog['status'] === 'checked_in') {
                // Currently inside the premises
                $logData = $lastLog;
                $result  = 'currently_in';
            } elseif ($lastLog && $lastLog['status'] === 'checked_out') {
                // Has visited before, currently outside
                $logData = $lastLog;
                $result  = 'currently_out';
            } else {
                // Registered but never visited
                $result  = 'currently_out';
                $logData = null;
            }
        }
    } catch (Exception $e) {
        $result = 'error';
        $error  = 'Unable to process at this time.';
    }
}

// ── Resolve display values ──
$displayName  = $visitor ? htmlspecialchars(trim($visitor['first_name'] . ' ' . $visitor['last_name'])) : '—';
$displayComp  = $visitor ? htmlspecialchars($visitor['company'] ?: '') : '';
$displayCode  = $visitor ? htmlspecialchars($visitor['visitor_code']) : htmlspecialchars($token);
// Photo URL: stored as 'uploads/visitors/...' relative to Admin root → prefix with base URL
$displayPhoto = ($visitor && $visitor['photo_url'])
    ? htmlspecialchars($baseUrl . '/' . ltrim($visitor['photo_url'], '/'))
    : '';
$displayVtype = $visitor ? ($visitor['visitor_type'] ?? 'regular') : 'regular';
$isVip = in_array($displayVtype, ['vip', 'government_official']);

$vtypeLabels = [
    'regular' => '',
    'vip' => '<span style="display:inline-block;background:linear-gradient(135deg,#F59E0B,#D97706);color:#fff;padding:3px 12px;border-radius:50px;font-size:11px;font-weight:800;margin-left:6px;vertical-align:middle">⭐ VIP</span>',
    'contractor' => '<span style="display:inline-block;background:linear-gradient(135deg,#3B82F6,#2563EB);color:#fff;padding:3px 12px;border-radius:50px;font-size:11px;font-weight:800;margin-left:6px;vertical-align:middle">🔧 Contractor</span>',
    'government_official' => '<span style="display:inline-block;background:linear-gradient(135deg,#8B5CF6,#7C3AED);color:#fff;padding:3px 12px;border-radius:50px;font-size:11px;font-weight:800;margin-left:6px;vertical-align:middle">🏛️ Gov. Official</span>',
];
$vtypeBadgeHtml = $vtypeLabels[$displayVtype] ?? '';

$statusMap = [
    'currently_in'  => ['label' => 'INSIDE',       'color' => '#065F46', 'bg' => '#D1FAE5', 'header' => '#059669'],
    'currently_out' => ['label' => 'NOT INSIDE',   'color' => '#065F46', 'bg' => '#D1FAE5', 'header' => '#059669'],
    'blacklisted'   => ['label' => 'BLACKLISTED',  'color' => '#991B1B', 'bg' => '#FEE2E2', 'header' => '#DC2626'],
    'not_found'     => ['label' => 'NOT FOUND',    'color' => '#374151', 'bg' => '#F3F4F6', 'header' => '#6B7280'],
    'error'         => ['label' => 'ERROR',        'color' => '#374151', 'bg' => '#F3F4F6', 'header' => '#6B7280'],
];
$s = $statusMap[$result] ?? $statusMap['error'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor <?php echo htmlspecialchars($s['label']); ?><?php echo $visitor ? ' — ' . $displayCode : ''; ?></title>
    <link rel="icon" href="<?php echo $baseUrl; ?>/assets/favicon/favicon.ico">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, <?php echo $isVip ? '#92400E' : $s['header']; ?> 0%, <?php echo $isVip ? '#78350F' : '#065f46'; ?> 100%);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0,0,0,.3);
            max-width: 420px; width: 100%;
            overflow: hidden;
            animation: slideUp .45s ease-out;
        }
        @keyframes slideUp { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }

        /* ── Header ── */
        .card-header {
            background: <?php echo $isVip ? 'linear-gradient(135deg,#D97706,#92400E)' : $s['header']; ?>;
            padding: 20px 24px 18px;
            text-align: center;
        }
        .card-header .result-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,.18);
            border-radius: 50%;
            margin: 0 auto 12px;
            display: flex; align-items: center; justify-content: center;
        }
        .card-header h1 {
            color: #fff;
            font-size: 24px; font-weight: 900;
            letter-spacing: .5px;
        }
        .card-header .sub {
            color: rgba(255,255,255,.80);
            font-size: 13px; margin-top: 5px;
        }
        /* Public badge */
        .card-header .public-tag {
            display: inline-block;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.35);
            color: rgba(255,255,255,.9);
            font-size: 10px; font-weight: 700;
            letter-spacing: 1px;
            padding: 3px 10px; border-radius: 50px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        /* ── Photo ── */
        .photo-section { display: flex; justify-content: center; padding: 24px 24px 12px; }
        .photo-frame {
            width: 150px; height: 150px;
            border-radius: 16px; overflow: hidden;
            box-shadow: 0 8px 28px rgba(0,0,0,.14);
            border: 4px solid <?php echo $isVip ? '#D97706' : $s['header']; ?>;
            background: #f3f4f6;
            display: flex; align-items: center; justify-content: center;
        }
        .photo-frame img { width:100%; height:100%; object-fit:cover; }
        .photo-frame .no-photo { text-align:center; color:#9ca3af; font-size:12px; padding:16px; }
        .photo-frame .no-photo svg { width:44px; height:44px; fill:#d1d5db; margin-bottom:6px; display:block; margin-left:auto; margin-right:auto; }

        /* ── Info ── */
        .info-section { padding: 6px 24px 22px; text-align: center; }
        .visitor-name { font-size: 21px; font-weight: 700; color: #111827; margin-bottom: 2px; }
        .visitor-company { font-size: 13px; color: #6b7280; margin-bottom: 14px; }
        .status-pill {
            display: inline-block;
            padding: 6px 20px;
            border-radius: 50px;
            background: <?php echo $s['bg']; ?>;
            color: <?php echo $s['color']; ?>;
            font-size: 12px; font-weight: 800;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        /* ── Details grid ── */
        .details { background: #f9fafb; border-radius: 12px; overflow: hidden; }
        .detail-row {
            display: flex; justify-content: space-between;
            padding: 10px 14px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        .detail-row:last-child { border-bottom: none; }
        .dl { color: #6b7280; font-weight: 500; }
        .dv { color: #111827; font-weight: 600; text-align: right; }

        /* ── Footer ── */
        .card-footer {
            padding: 14px 24px;
            border-top: 1px solid #f3f4f6;
            text-align: center;
        }
        .card-footer p { font-size: 11px; color: #9ca3af; }

        /* ── Error / Not Found ── */
        .error-body { padding: 40px 28px; text-align: center; }
        .error-body .err-icon { width:56px; height:56px; fill:#f87171; margin: 0 auto 14px; display:block; }
        .error-body h2 { color:#374151; font-size:18px; margin-bottom:8px; }
        .error-body p { color:#9ca3af; font-size:13px; line-height:1.6; }
    </style>
</head>
<body>
<div class="card">

<?php if ($result === 'currently_in'): ?>
  <!-- ══════════ CURRENTLY INSIDE ══════════ -->
  <div class="card-header">
    <div class="public-tag">🌍 Read-Only · Public QR</div>
    <div class="result-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
    </div>
    <h1>Currently Inside</h1>
    <div class="sub"><?php echo $isVip ? '⭐ Priority Visitor · ' : ''; ?>This visitor is currently on the premises</div>
  </div>

  <div class="photo-section">
    <div class="photo-frame">
      <?php if ($displayPhoto): ?>
        <img src="<?php echo $displayPhoto; ?>" alt="Visitor Photo"
             onerror="this.parentElement.innerHTML='<div class=\'no-photo\'><svg viewBox=\'0 0 24 24\'><path d=\'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\'/></svg><span>No photo</span></div>'">
      <?php else: ?>
        <div class="no-photo">
          <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
          No photo
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="info-section">
    <div class="visitor-name"><?php echo $displayName; ?><?php echo $vtypeBadgeHtml; ?></div>
    <?php if ($displayComp): ?><div class="visitor-company"><?php echo $displayComp; ?></div><?php endif; ?>
    <div class="status-pill">✓ INSIDE</div>
    <div class="details">
      <div class="detail-row"><span class="dl">Visitor Code</span><span class="dv"><?php echo $displayCode; ?></span></div>
      <div class="detail-row"><span class="dl">Visit Code</span><span class="dv"><?php echo htmlspecialchars($logData['visit_code']); ?></span></div>
      <?php if ($isVip): ?>
      <div class="detail-row"><span class="dl">Visitor Type</span><span class="dv" style="color:<?php echo $displayVtype === 'government_official' ? '#7C3AED' : '#D97706'; ?>">
        <?php echo $displayVtype === 'government_official' ? '🏛️ Government Official' : '⭐ VIP'; ?>
      </span></div>
      <?php endif; ?>
      <div class="detail-row"><span class="dl">Checked In At</span><span class="dv"><?php echo date('M d, Y h:i A', strtotime($logData['check_in_time'])); ?></span></div>
      <div class="detail-row"><span class="dl">Total Visits</span><span class="dv"><?php echo (int)$visitor['visit_count']; ?></span></div>
    </div>
  </div>

<?php elseif ($result === 'currently_out'): ?>
  <!-- ══════════ NOT CURRENTLY INSIDE ══════════ -->
  <div class="card-header">
    <div class="public-tag">🌍 Read-Only · Public QR</div>
    <div class="result-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
    </div>
    <h1>Not Inside</h1>
    <div class="sub"><?php echo $isVip ? '⭐ Priority Visitor · ' : ''; ?>This visitor is not currently on the premises</div>
  </div>

  <div class="photo-section">
    <div class="photo-frame">
      <?php if ($displayPhoto): ?>
        <img src="<?php echo $displayPhoto; ?>" alt="Visitor Photo"
             onerror="this.parentElement.innerHTML='<div class=\'no-photo\'><svg viewBox=\'0 0 24 24\'><path d=\'M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z\'/></svg><span>No photo</span></div>'">
      <?php else: ?>
        <div class="no-photo">
          <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
          No photo
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="info-section">
    <div class="visitor-name"><?php echo $displayName; ?><?php echo $vtypeBadgeHtml; ?></div>
    <?php if ($displayComp): ?><div class="visitor-company"><?php echo $displayComp; ?></div><?php endif; ?>
    <div class="status-pill">← NOT INSIDE</div>
    <div class="details">
      <div class="detail-row"><span class="dl">Visitor Code</span><span class="dv"><?php echo $displayCode; ?></span></div>
      <div class="detail-row"><span class="dl">Total Visits</span><span class="dv"><?php echo (int)$visitor['visit_count']; ?></span></div>
      <?php if ($logData): ?>
      <div class="detail-row"><span class="dl">Last Checked In</span><span class="dv"><?php echo date('M d, h:i A', strtotime($logData['check_in_time'])); ?></span></div>
      <?php if ($logData['check_out_time']): ?>
      <div class="detail-row"><span class="dl">Last Checked Out</span><span class="dv"><?php echo date('M d, h:i A', strtotime($logData['check_out_time'])); ?></span></div>
      <?php endif; ?>
      <?php else: ?>
      <div class="detail-row"><span class="dl">Last Visit</span><span class="dv" style="color:#9CA3AF">No visit history</span></div>
      <?php endif; ?>
    </div>
  </div>

<?php elseif ($result === 'blacklisted'): ?>
  <!-- ══════════ BLACKLISTED ══════════ -->
  <div class="card-header">
    <div class="public-tag">🌍 Read-Only · Public QR</div>
    <div class="result-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
    </div>
    <h1>Access Denied</h1>
    <div class="sub">This visitor is not permitted entry</div>
  </div>
  <div class="error-body">
    <svg class="err-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/></svg>
    <h2><?php echo $displayName; ?></h2>
    <p>This visitor has been <strong style="color:#dc2626">blacklisted</strong> and cannot enter the premises.</p>
  </div>

<?php elseif ($result === 'not_found'): ?>
  <!-- ══════════ NOT FOUND ══════════ -->
  <div class="card-header">
    <div class="public-tag">🌍 Read-Only · Public QR</div>
    <div class="result-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
    </div>
    <h1>Not Found</h1>
    <div class="sub">No visitor matches this QR code</div>
  </div>
  <div class="error-body">
    <svg class="err-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <h2>Visitor Not Found</h2>
    <p>Token: <strong><?php echo htmlspecialchars($token); ?></strong><br>This QR code does not match any registered visitor.</p>
  </div>

<?php else: ?>
  <!-- ══════════ ERROR ══════════ -->
  <div class="card-header">
    <div class="public-tag">🌍 Read-Only · Public QR</div>
    <div class="result-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <h1>Error</h1>
    <div class="sub">Could not process this request</div>
  </div>
  <div class="error-body">
    <svg class="err-icon" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
    <h2>Something Went Wrong</h2>
    <p><?php echo htmlspecialchars($error ?: 'An unexpected error occurred. Please try again.'); ?></p>
  </div>
<?php endif; ?>

  <div class="card-footer">
    <p><?php echo $actionTime; ?> · Microfinancial Management</p>
  </div>

</div>
</body>
</html>
