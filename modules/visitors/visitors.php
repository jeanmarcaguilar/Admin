<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Visitor Management — Microfinancial Admin</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            "brand-primary": "#059669",
            "brand-primary-hover": "#047857",
            "brand-background-main": "#F0FDF4",
            "brand-border": "#D1FAE5",
            "brand-text-primary": "#1F2937",
            "brand-text-secondary": "#4B5563",
          }
        }
      }
    }
  </script>
  <link rel="stylesheet" href="../../admin.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
  <style>
    .live-badge { display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;letter-spacing:0.5px;color:#059669;background:#ECFDF5;border:1px solid #A7F3D0;padding:3px 10px;border-radius:20px;white-space:nowrap }
    .live-dot { width:7px;height:7px;border-radius:50%;background:#059669;display:inline-block;animation:livePulse 1.5s ease-in-out infinite }
    @keyframes livePulse { 0%,100%{opacity:1;box-shadow:0 0 0 0 rgba(5,150,105,0.5)} 50%{opacity:0.6;box-shadow:0 0 0 4px rgba(5,150,105,0)} }
    .live-badge.refreshing { color:#D97706;background:#FFFBEB;border-color:#FDE68A }
    .live-badge.refreshing .live-dot { background:#D97706;animation:livePulseAmber 0.6s ease-in-out infinite }
    @keyframes livePulseAmber { 0%,100%{opacity:1} 50%{opacity:0.3} }
    .last-updated { font-size:10px;color:#9CA3AF;white-space:nowrap }
  </style>
  <style>
    .qr-scan-zone { border: 3px dashed #D1FAE5; border-radius: 16px; padding: 32px; text-align: center; transition: all 0.3s; }
    .qr-scan-zone.scanning { border-color: #059669; background: #F0FDF4; }
    .qr-scan-zone.found { border-color: #059669; background: #D1FAE5; }
    .qr-scan-zone.error { border-color: #DC2626; background: #FEE2E2; }
    .scan-result-card { background: white; border: 2px solid #D1FAE5; border-radius: 16px; padding: 24px; margin-top: 16px; }
    .visitor-pass { background: white; border: 2px solid #D1FAE5; border-radius: 16px; padding: 24px; display: inline-block; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
    .visitor-pass .pass-header { font-weight: 700; color: #059669; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
    .pulse-dot { animation: pulse-anim 2s infinite; }
    @keyframes pulse-anim { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    #scanner-video { width: 100%; max-width: 400px; border-radius: 12px; border: 3px solid #059669; }
    /* Flip card for QR/Photo toggle */
    .qr-flip-container { perspective: 1000px; cursor: pointer; }
    .qr-flip-inner { position: relative; transition: transform 0.7s cubic-bezier(0.4,0,0.2,1); transform-style: preserve-3d; }
    .qr-flip-inner.flipped { transform: rotateY(180deg); }
    .qr-flip-front, .qr-flip-back { backface-visibility: hidden; -webkit-backface-visibility: hidden; }
    .qr-flip-back { transform: rotateY(180deg); position: absolute; top: 0; left: 0; width: 100%; }
    /* Log toggle buttons */
    .log-toggle-btn { padding:5px 12px; border-radius:8px; font-size:11px; font-weight:600; border:1.5px solid #D1D5DB; background:#fff; color:#4B5563; cursor:pointer; transition:all 0.2s; display:inline-flex; align-items:center; gap:4px; }
    .log-toggle-btn:hover { border-color:#059669; color:#059669; }
    .log-toggle-btn.active { background:#059669; color:#fff; border-color:#059669; }
    .log-toggle-btn.active-blue { background:#3B82F6; color:#fff; border-color:#3B82F6; }

    /* ── VIP Badges & Styling ── */
    .vip-badge { display:inline-flex;align-items:center;gap:4px;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:700;letter-spacing:.5px;text-transform:uppercase }
    .vip-badge.vip-regular { background:#F3F4F6;color:#6B7280 }
    .vip-badge.vip-vip { background:linear-gradient(135deg,#FEF3C7,#FDE68A);color:#92400E;border:1px solid #F59E0B33 }
    .vip-badge.vip-contractor { background:#DBEAFE;color:#1E40AF }
    .vip-badge.vip-government_official { background:#EDE9FE;color:#5B21B6 }
    .vip-fields-box { background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;padding:14px 16px;margin-top:12px }
    .vip-fields-box .vip-fields-title { font-size:12px;font-weight:700;color:#92400E;margin-bottom:10px;display:flex;align-items:center;gap:6px }
    .vip-indicator { width:8px;height:8px;border-radius:50%;display:inline-block }
    .vip-indicator.vi-regular { background:#9CA3AF }
    .vip-indicator.vi-vip { background:#F59E0B;box-shadow:0 0 6px #F59E0B80 }
    .vip-indicator.vi-contractor { background:#3B82F6 }
    .vip-indicator.vi-government_official { background:#8B5CF6 }
    .vip-inside-card { background:linear-gradient(135deg,#FFFBEB,#FEF3C7);border:1px solid #FDE68A;border-radius:12px;padding:14px;display:flex;align-items:center;gap:12px }
    .vip-inside-card .vic-avatar { width:40px;height:40px;border-radius:50%;overflow:hidden;border:2px solid #F59E0B;flex-shrink:0;display:flex;align-items:center;justify-content:center;background:#FEF3C7 }
    .vip-inside-card .vic-avatar img { width:100%;height:100%;object-fit:cover }
    .vip-inside-card .vic-info { flex:1;min-width:0 }
    .vip-inside-card .vic-name { font-weight:700;font-size:13px;color:#92400E }
    .vip-inside-card .vic-detail { font-size:11px;color:#B45309 }
    .vip-upcoming-row { display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#F9FAFB;border-radius:10px }

    /* ── Scan Result Modal ── */
    #modal-scan-result .modal { max-width:420px;border-radius:24px;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,.25) }
    .scan-modal-status { padding:28px 20px;text-align:center }
    .scan-modal-status .sm-icon { width:72px;height:72px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center }
    .scan-modal-status .sm-title { font-size:26px;font-weight:900;letter-spacing:.3px }
    .scan-modal-status .sm-sub { font-size:13px;margin-top:6px;opacity:.8 }
    .scan-info-grid { display:grid;grid-template-columns:1fr 1fr;gap:1px;background:#E5E7EB }
    .scan-info-grid > div { background:#fff;padding:12px 14px;font-size:13px }
    .scan-info-grid .si-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9CA3AF;margin-bottom:2px }
    .scan-info-grid .si-value { font-weight:600;color:#1F2937 }
    .scan-timer-bar { height:4px;background:#E5E7EB;position:relative;overflow:hidden }
    .scan-timer-bar .stb-fill { position:absolute;inset:0;background:#059669;transform-origin:left;animation:scanTimerShrink var(--scan-timer,8s) linear forwards }
    @keyframes scanTimerShrink { from{transform:scaleX(1)} to{transform:scaleX(0)} }
    @keyframes scanModalPop { from{opacity:0;transform:scale(.85) translateY(20px)} to{opacity:1;transform:scale(1) translateY(0)} }
  </style>
  <script src="../../hr2-integration.js"></script>
  <script src="../../hr4-integration.js"></script>

</head>

<body class="bg-brand-background-main min-h-screen font-[Inter,'Segoe_UI',system-ui,-apple-system,sans-serif]">

  <?php $activePage = 'visitors'; $baseUrl = '../../'; include '../../sidebar.php'; ?>

  <!-- MAIN WRAPPER -->
  <div class="md:pl-72">
    <!-- HEADER -->
    <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
      <div class="hidden md:block absolute left-0 top-0 h-16 w-[2px] bg-white"></div>
      <div class="flex items-center gap-3">
        <button id="mobile-menu-btn" class="md:hidden w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center"></button>
      </div>
      <div class="flex items-center gap-3 sm:gap-5">
        <span id="real-time-clock" class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">--:--:--</span>
        <button id="notification-bell" class="w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center relative">🔔<span id="notif-badge" class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-red-500 border-2 border-white"></span></button>
        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>
        <div class="relative">
          <button id="user-menu-button" class="flex items-center gap-3 focus:outline-none group rounded-xl px-2 py-2 hover:bg-gray-100 active:bg-gray-200 transition">
            <div class="w-10 h-10 rounded-full bg-white shadow group-hover:shadow-md transition-shadow overflow-hidden flex items-center justify-center border border-gray-100"><div class="w-full h-full flex items-center justify-center font-bold text-brand-primary bg-emerald-50"><?= $userInitial ?></div></div>
            <div class="hidden md:flex flex-col items-start text-left">
              <span class="text-sm font-bold text-gray-700 group-hover:text-brand-primary transition-colors"><?= htmlspecialchars($userName) ?></span>
              <span class="text-[10px] text-gray-500 font-medium uppercase group-hover:text-brand-primary transition-colors"><?= htmlspecialchars($userRole) ?></span>
            </div>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </button>
          <div id="user-menu-dropdown" class="dropdown-panel hidden opacity-0 translate-y-2 scale-95 pointer-events-none absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100 transition-all duration-200 z-50">
            <div class="px-4 py-3 border-b border-gray-100"><div class="text-sm font-bold text-gray-800"><?= htmlspecialchars($userName) ?></div><div class="text-xs text-gray-500"><?= htmlspecialchars($sessionUser['email'] ?? '') ?></div></div>
            <a href="#" onclick="openProfileModal(); return false;" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">👤 &nbsp;My Profile</a>
            <a href="#" onclick="openSettingsModal(); return false;" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">⚙️ &nbsp;Settings</a>
            <div class="h-px bg-gray-100"></div>
            <a href="#" class="block px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition rounded-b-xl logout">🚪 &nbsp;Logout</a>
          </div>
        </div>
      </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="p-6">

      <div class="animate-in">
        <h1 class="page-title">Visitor Management</h1>
        <p class="page-subtitle">Register & verify visitors, generate QR passes & digital badges, pre-register appointments, track visit history, manage watchlists, and view analytics</p>
      </div>

      <!-- SUBMODULE DIRECTORY -->
      <div class="animate-in delay-1">
        <div class="module-directory-label">Submodule Directory</div>
        <div class="stats-grid" style="margin-bottom:18px">
          <a href="#tab-registration" onclick="showSection('#tab-registration')" class="stat-card stat-card-link">
            <div class="stat-icon purple"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            <div class="stat-info">
              <div class="stat-value" id="stat-total">—</div>
              <div class="stat-label">Registered Visitors</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-logs" onclick="showSection('#tab-logs')" class="stat-card stat-card-link">
            <div class="stat-icon blue"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div class="stat-info">
              <div class="stat-value" id="stat-preregs">—</div>
              <div class="stat-label">Pre-Registered</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-analytics" onclick="showSection('#tab-analytics')" class="stat-card stat-card-link">
            <div class="stat-icon amber"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg></div>
            <div class="stat-info">
              <div class="stat-value" id="stat-today">—</div>
              <div class="stat-label">Today's Visits</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
        </div>
      </div>



      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Registration & ID Verification                 -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-registration" class="tab-content active animate-in delay-3">

        <!-- Sub-tabs: Registered / Pre-Registration -->
        <div class="sub-tabs" style="margin-bottom:16px">
          <button class="sub-tab active" onclick="switchSubTab(this, 'registered')">
            👤 Registered Visitors <span class="sub-tab-badge" id="badge-registered">0</span>
          </button>
          <button class="sub-tab" onclick="switchSubTab(this, 'prereg')">
            Pre-Registration <span class="sub-tab-badge badge-amber" id="badge-prereg">0</span>
          </button>
        </div>

        <!-- SUB-TAB: Registered Visitors -->
        <div id="subtab-registered" class="sub-tab-content active">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Registered Visitors & ID Verification</span>
              <div style="display:flex;gap:8px;align-items:center">
                <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportVisitors('pdf')">📄 PDF</button>
                <button class="btn-export btn-export-csv btn-export-sm" onclick="exportVisitors('csv')">📊 CSV</button>
                <input type="text" id="visitor-search" class="form-input" style="width:200px;padding:6px 12px;font-size:12px" placeholder="🔍 Search visitors..." oninput="renderVisitors()">
              </div>
            </div>
            <div class="card-body">
              <table class="data-table">
                <thead><tr><th>Code</th><th>Visitor</th><th>Company</th><th>Contact</th><th>ID</th><th>QR</th><th>Actions</th></tr></thead>
                <tbody id="visitors-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- SUB-TAB: Pre-Registration (Pending) -->
        <div id="subtab-prereg" class="sub-tab-content">
          <div class="card">
            <div class="card-header">
              <span class="card-title">📋 Pending Pre-Registrations</span>
              <div style="display:flex;gap:8px;align-items:center">
                <button class="btn btn-sm" style="background:#059669;color:#fff;border:none" onclick="openModal('modal-prereg')">+ Pre-Register</button>
                <input type="text" id="prereg-sub-search" class="form-input" style="width:180px;padding:6px 12px;font-size:12px" placeholder="🔍 Search pending..." oninput="renderPreregSub()">
                <span id="prereg-sub-count" style="font-size:12px;color:#6B7280;font-weight:600">0 pending</span>
              </div>
            </div>
            <div class="card-body">
              <table class="data-table">
                <thead><tr><th>Code</th><th>Visitor</th><th>Company</th><th>Purpose</th><th>Expected Date</th><th>Host</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody id="preregs-tbody"></tbody>
              </table>
            </div>
          </div>
        </div>

      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: QR Pass & Digital Badge                         -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-qr" class="tab-content">
        <div class="grid-2">
          <!-- QR Pass Generator -->
          <div class="card">
            <div class="card-header"><span class="card-title">QR Pass & Digital Badge Generator</span></div>
            <div class="card-body" style="padding:20px">
              <div class="form-control">
                <label>Select Visitor</label>
                <select id="qr-visitor-select" class="form-input">
                  <option value="">-- Select Visitor --</option>
                </select>
              </div>
              <button class="btn btn-primary btn-sm" style="margin-top:8px" onclick="generateVisitorQR()">Generate QR Pass</button>

              <div id="qr-preview" style="margin-top:24px;text-align:center;display:none">
                <div class="qr-flip-container" onclick="toggleQRFlip()" title="Click to flip between Photo and QR Code">
                  <div id="qr-flip-inner" class="qr-flip-inner">
                    <!-- FRONT: Visitor Photo -->
                    <div class="qr-flip-front">
                      <div class="visitor-pass" style="width:100%">
                        <div class="pass-header">VISITOR PASS</div>
                        <div style="font-size:11px;color:#6B7280;margin-bottom:4px">Microfinancial Management System</div>
                        <hr style="border-color:#D1FAE5;margin:8px 0">
                        <div id="qr-photo-container" style="width:140px;height:140px;border-radius:50%;margin:16px auto;overflow:hidden;border:4px solid #059669;background:#F3F4F6;display:flex;align-items:center;justify-content:center;transition:box-shadow 0.3s">
                          <svg id="qr-photo-placeholder" xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                          <img id="qr-visitor-photo" src="" alt="Visitor" style="width:100%;height:100%;object-fit:cover;display:none">
                        </div>
                        <div id="qr-visitor-name" style="font-weight:700;font-size:16px;color:#1F2937"></div>
                        <div id="qr-visitor-company" style="font-size:13px;color:#6B7280;margin-bottom:4px"></div>
                        <div id="qr-visitor-visits" style="font-size:12px;color:#059669;font-weight:600;margin-bottom:8px"></div>
                        <div style="font-size:11px;color:#9CA3AF;padding-top:8px;border-top:1px dashed #D1FAE5">
                          <span style="color:#059669">&#8635;</span> Click to view QR Code
                        </div>
                      </div>
                    </div>
                    <!-- BACK: QR Code -->
                    <div class="qr-flip-back">
                      <div class="visitor-pass" id="qr-pass-printable" style="width:100%">
                        <div class="pass-header">VISITOR PASS</div>
                        <div style="font-size:11px;color:#6B7280;margin-bottom:4px">Microfinancial Management System</div>
                        <hr style="border-color:#D1FAE5;margin:8px 0">
                        <div id="qr-visitor-name-back" style="font-weight:700;font-size:16px;color:#1F2937"></div>
                        <div id="qr-visitor-company-back" style="font-size:13px;color:#6B7280;margin-bottom:12px"></div>
                        <img id="qr-code-img" src="" alt="QR" style="width:180px;height:180px;border-radius:8px;margin:0 auto;display:block">
                        <div id="qr-code-id" style="font-size:12px;color:#059669;margin-top:10px;font-weight:700;letter-spacing:1px"></div>
                        <div style="font-size:10px;color:#9CA3AF;margin-top:8px;padding-top:8px;border-top:1px dashed #D1FAE5">
                          <span style="color:#059669">&#8635;</span> Click to view Photo
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div style="margin-top:12px;display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
                  <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); toggleQRFlip()" title="Flip between Photo and QR">&#8635; Flip Card</button>
                  <button class="btn btn-outline btn-sm" onclick="event.stopPropagation(); printQR()">🖨️ Print Pass</button>
                </div>
              </div>
            </div>
          </div>

          <!-- QR Scanner -->
          <div class="card">
            <div class="card-header" style="padding-bottom:0">
              <span class="card-title">QR Scanner</span>
            </div>
            <!-- Inner Tab Bar -->
            <div style="display:flex;border-bottom:2px solid #E5E7EB;padding:0 20px;background:#fff">
              <button id="scantab-btn-scan" onclick="switchScanTab('scan')"
                style="padding:10px 18px;font-size:13px;font-weight:700;border:none;background:none;cursor:pointer;border-bottom:3px solid #059669;color:#059669;margin-bottom:-2px;transition:all .2s">
                📷 Generate QR &amp; Scan
              </button>
              <button id="scantab-btn-status" onclick="switchScanTab('status')"
                style="padding:10px 18px;font-size:13px;font-weight:700;border:none;background:none;cursor:pointer;border-bottom:3px solid transparent;color:#9CA3AF;margin-bottom:-2px;transition:all .2s;display:flex;align-items:center;gap:6px">
                📊 Live Status
                <span id="scantab-status-dot" style="display:none;width:8px;height:8px;border-radius:50%;background:#059669;animation:livePulse 1.5s ease-in-out infinite"></span>
              </button>
            </div>

            <!-- TAB: Generate QR / Scan QR -->
            <div id="scantab-scan" class="card-body" style="padding:20px">
              <!-- Manual Code Entry -->
              <div class="form-control" style="margin-bottom:16px">
                <label>Enter Visitor Code or Scan QR</label>
                <div style="display:flex;gap:8px">
                  <input type="text" id="scan-code-input" class="form-input" placeholder="e.g. VIS-001, PR-2026-001, VL-2026-0001" style="flex:1" onkeydown="if(event.key==='Enter')lookupVisitor()">
                  <button class="btn btn-primary btn-sm" onclick="lookupVisitor()">Lookup</button>
                </div>
              </div>

              <!-- Camera Scanner -->
              <div id="scanner-zone" class="qr-scan-zone">
                <div id="scanner-idle">
                  <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5" style="margin:0 auto 12px"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
                  <div style="font-weight:600;color:#1F2937;margin-bottom:4px">Camera QR Scanner</div>
                  <div style="font-size:13px;color:#9CA3AF;margin-bottom:16px">Point your camera at a visitor's QR code to scan it</div>
                  <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap">
                    <button class="btn btn-primary btn-sm" onclick="startScanner()">📷 Enable Camera</button>
                    <label class="btn btn-outline btn-sm" style="cursor:pointer;display:inline-flex;align-items:center;gap:5px;margin:0">
                      🖼️ Upload QR Image
                      <input type="file" id="qr-upload-input" accept="image/*" style="display:none" onchange="scanQRFromImage(this)">
                    </label>
                  </div>
                </div>
                <div id="scanner-active" style="display:none">
                  <video id="scanner-video" autoplay playsinline></video>
                  <canvas id="scanner-canvas" style="display:none"></canvas>
                  <div style="margin-top:12px;font-size:13px;color:#059669;font-weight:600">
                    <span class="pulse-dot" style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#059669;margin-right:6px"></span>
                    Scanning... Point camera at QR code
                  </div>
                  <button class="btn btn-outline btn-sm" style="margin-top:8px" onclick="stopScanner()">Stop Scanner</button>
                </div>
                <!-- Camera Scan Result -->
                <div id="scanner-camera-result" style="display:none;padding:4px 0">
                  <div id="scanner-camera-status" style="font-size:13px;font-weight:600"></div>
                  <button class="btn btn-primary btn-sm" style="margin-top:10px;width:100%" onclick="clearCameraResult()">📷 Scan Again</button>
                </div>
                <!-- QR Upload Preview -->
                <div id="scanner-upload-preview" style="display:none;margin-top:14px">
                  <img id="scanner-upload-img" style="max-width:100%;max-height:200px;border-radius:10px;border:2px solid #D1FAE5;object-fit:contain">
                  <div id="scanner-upload-status" style="margin-top:8px;font-size:13px;font-weight:600"></div>
                  <button class="btn btn-outline btn-sm" style="margin-top:8px" onclick="clearQRUpload()">✕ Clear</button>
                </div>
              </div>

              <!-- Scan Result (manual lookup) -->
              <div id="scan-result" style="display:none"></div>
            </div>

            <!-- TAB: Live Status -->
            <div id="scantab-status" class="card-body" style="padding:20px;display:none">
              <!-- Empty state -->
              <div id="live-status-empty" style="text-align:center;padding:36px 20px">
                <div style="font-size:48px;margin-bottom:12px">📡</div>
                <div style="font-weight:700;font-size:15px;color:#374151;margin-bottom:6px">Waiting for scan…</div>
                <div style="font-size:13px;color:#9CA3AF">Scan a visitor QR code and the status will appear here automatically.</div>
                <button class="btn btn-outline btn-sm" style="margin-top:16px" onclick="switchScanTab('scan')">← Go to Scanner</button>
              </div>
              <!-- Populated state -->
              <div id="live-status-card" style="display:none">
                <!-- Status header -->
                <div id="ls-header" style="border-radius:14px 14px 0 0;padding:18px 20px;text-align:center">
                  <div id="ls-icon" style="font-size:40px;margin-bottom:6px"></div>
                  <div id="ls-label" style="font-size:20px;font-weight:900;letter-spacing:.5px"></div>
                  <div id="ls-sub" style="font-size:13px;margin-top:4px;opacity:.85"></div>
                </div>
                <!-- Visitor info -->
                <div style="border:2px solid #E5E7EB;border-top:none;border-radius:0 0 14px 14px;padding:16px">
                  <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px">
                    <div id="ls-photo" style="width:58px;height:58px;border-radius:50%;overflow:hidden;border:3px solid #E5E7EB;background:#F3F4F6;display:flex;align-items:center;justify-content:center;flex-shrink:0"></div>
                    <div style="flex:1;min-width:0">
                      <div id="ls-name" style="font-weight:800;font-size:16px;color:#1F2937"></div>
                      <div id="ls-company" style="font-size:12px;color:#6B7280;margin-top:2px"></div>
                      <div id="ls-code" style="font-size:11px;color:#9CA3AF;margin-top:1px"></div>
                    </div>
                  </div>
                  <div id="ls-details" style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px"></div>
                  <!-- Timestamp -->
                  <div id="ls-timestamp" style="font-size:11px;color:#9CA3AF;text-align:center;padding-top:10px;border-top:1px solid #F3F4F6"></div>
                  <div style="display:flex;gap:8px;margin-top:12px">
                    <button class="btn btn-primary btn-sm" style="flex:1" onclick="switchScanTab('scan');startScanner()">📷 Scan Next</button>
                    <button class="btn btn-outline btn-sm" style="flex:1" onclick="switchScanTab('scan')">← Back to Scanner</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Visit History & Logs                            -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-logs" class="tab-content">

        <!-- Visitor Log Folders -->
        <div id="log-folders" style="margin-bottom:20px">
          <div class="card" style="margin-bottom:0">
            <div class="card-header">
              <span class="card-title">�️ Visitor Logs</span>
              <div style="display:flex;gap:8px;align-items:center">
                <input type="text" id="folder-search" class="form-input" style="width:200px;padding:6px 12px;font-size:12px" placeholder="🔍 Search visitor..." oninput="renderLogFolders()">
                <span id="folder-count" style="font-size:12px;color:#6B7280;font-weight:600">0 visitors</span>
              </div>
            </div>
            <div class="card-body" style="padding:12px">
              <div id="log-folders-container" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px"></div>
            </div>
          </div>
        </div>

        <!-- Full Logs Table -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">Visit History & Logs</span>
            <div style="display:flex;gap:8px;align-items:center">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportLogs('pdf')">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportLogs('csv')">📊 CSV</button>
            </div>
          </div>
          <!-- Filter Bar -->
          <div style="padding:12px 20px;background:#F9FAFB;border-bottom:1px solid #E5E7EB;display:flex;flex-wrap:wrap;gap:10px;align-items:center">
            <input type="text" id="log-filter-search" class="form-input" placeholder="Search visitor name..." style="width:180px;padding:6px 12px;font-size:12px" oninput="renderLogs()">
            <select id="log-filter-status" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderLogs()">
              <option value="">All Status</option>
              <option value="checked_in">Checked In</option>
              <option value="checked_out">Checked Out</option>
              <option value="pre_registered">Pre-Registered</option>
            </select>
            <select id="log-filter-purpose" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderLogs()">
              <option value="">All Purpose</option>
              <option value="meeting">Meeting</option>
              <option value="delivery">Delivery</option>
              <option value="interview">Interview</option>
              <option value="inspection">Inspection</option>
              <option value="consultation">Consultation</option>
              <option value="maintenance">Maintenance</option>
              <option value="partnership">Partnership</option>
              <option value="educational">Educational</option>
              <option value="other">Other</option>
            </select>
            <select id="log-filter-dept" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderLogs()">
              <option value="">All Departments</option>
              <option value="Executive Office">Executive Office</option>
              <option value="Human Resources">Human Resources</option>
              <option value="Finance Dept.">Finance Dept.</option>
              <option value="Legal Dept.">Legal Dept.</option>
              <option value="Compliance Dept.">Compliance Dept.</option>
              <option value="Operations">Operations</option>
              <option value="Credit Department">Credit Department</option>
            </select>
            <input type="date" id="log-filter-from" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderLogs()" title="From date">
            <input type="date" id="log-filter-to" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderLogs()" title="To date">
            <button class="btn btn-outline btn-sm" style="font-size:11px" onclick="clearLogFilters()">Clear</button>
            <div style="height:20px;width:1px;background:#D1D5DB"></div>
            <button id="log-filter-repeat" class="log-toggle-btn" onclick="toggleLogRepeat()" title="Show only visitors with 2+ visits">🔄 Repeat Only</button>
            <button id="log-filter-byday" class="log-toggle-btn" onclick="toggleLogByDay()" title="Group logs by day">📅 By Days</button>
            <span id="log-result-count" style="font-size:11px;color:#6B7280;margin-left:auto">0 records</span>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead><tr><th>Visit Code</th><th>Visitor</th><th>Company</th><th>Purpose</th><th>Check-In</th><th>Check-Out</th><th>Duration</th><th>Host</th><th>Status</th></tr></thead>
              <tbody id="logs-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Pre-Registration & Appointments                -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-prereg" class="tab-content">

        <!-- Pre-Reg Stats -->
        <div class="stats-grid animate-in delay-1" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon blue">📋</div><div class="stat-info"><div class="stat-value" id="pr-total">—</div><div class="stat-label">Total Pre-Registrations</div></div></div>
          <div class="stat-card"><div class="stat-icon amber">⏳</div><div class="stat-info"><div class="stat-value" id="pr-pending">—</div><div class="stat-label">Pending Approval</div></div></div>
          <div class="stat-card"><div class="stat-icon green">✅</div><div class="stat-info"><div class="stat-value" id="pr-approved">—</div><div class="stat-label">Approved</div></div></div>
          <div class="stat-card"><div class="stat-icon purple">📅</div><div class="stat-info"><div class="stat-value" id="pr-today">—</div><div class="stat-label">Expected Today</div></div></div>
        </div>

        <!-- Upcoming Schedule Calendar View -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <span class="card-title">📅 Appointment Schedule</span>
            <div style="display:flex;gap:8px;align-items:center">
              <input type="text" id="pr-search" class="form-input" style="width:180px;padding:6px 12px;font-size:12px" placeholder="🔍 Search pending..." oninput="renderPreregTab()">
              <button class="btn btn-sm" style="background:#059669;color:#fff;border:none" onclick="openModal('modal-prereg')">+ Pre-Register</button>
            </div>
          </div>
          <div class="card-body" style="padding:16px">
            <div id="pr-schedule-timeline"></div>
          </div>
        </div>

        <!-- Pre-Registration Table -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">📋 All Pre-Registrations</span>
            <div style="display:flex;gap:8px">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportPreregs('pdf')">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportPreregs('csv')">📊 CSV</button>
              <span id="pr-table-count" style="font-size:12px;color:#6B7280;display:flex;align-items:center">0 records</span>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead><tr><th>Code</th><th>Visitor</th><th>Company</th><th>Purpose</th><th>Expected Date</th><th>Host</th><th>Status</th><th>Actions</th></tr></thead>
              <tbody id="prereg-tab-tbody"></tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Visitor Analytics & Insights                    -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-analytics" class="tab-content">
        <div class="stats-grid">
          <div class="stat-card"><div class="stat-icon green"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div><div class="stat-info"><div class="stat-value" id="stat-month">—</div><div class="stat-label">This Month</div></div></div>
          <div class="stat-card"><div class="stat-icon blue"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div class="stat-info"><div class="stat-value" id="stat-avg-dur">—</div><div class="stat-label">Avg Duration</div></div></div>
          <div class="stat-card"><div class="stat-icon amber"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></div><div class="stat-info"><div class="stat-value" id="stat-top-org">—</div><div class="stat-label">Top Visitor Org</div></div></div>
          <div class="stat-card"><div class="stat-icon purple"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div class="stat-info"><div class="stat-value" id="stat-upcoming">—</div><div class="stat-label">Upcoming Pre-Regs</div></div></div>
        </div>

        <div class="grid-2" style="margin-top:20px">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Visits by Purpose</span>
              <div style="display:flex;align-items:center;gap:8px">
                <span class="live-badge" id="live-vis-analytics"><span class="live-dot"></span> LIVE</span>
                <span class="last-updated" id="updated-vis-analytics"></span>
              </div>
            </div>
            <div class="card-body" style="padding:20px;height:280px">
              <canvas id="chartVisitorPurpose"></canvas>
            </div>
          </div>
          <div class="card">
            <div class="card-header"><span class="card-title">Top Visitors</span></div>
            <div class="card-body" style="padding:16px">
              <div id="top-visitors-list" style="display:flex;flex-direction:column;gap:10px"></div>
            </div>
          </div>
        </div>

        <!-- VIP Dashboard Section -->
        <div class="grid-2" style="margin-top:20px">
          <div class="card">
            <div class="card-header" style="background:linear-gradient(135deg,#FFFBEB,#FEF3C7);border-bottom:1px solid #FDE68A">
              <span class="card-title" style="color:#92400E">⭐ VIP Visitors Inside</span>
              <span id="vip-inside-count" class="badge badge-amber" style="font-size:11px">0</span>
            </div>
            <div class="card-body" style="padding:14px;max-height:320px;overflow-y:auto">
              <div id="vip-inside-list" style="display:flex;flex-direction:column;gap:10px">
                <div class="empty-state" style="padding:20px"><div style="font-size:28px;margin-bottom:6px">⭐</div><div style="font-weight:600;font-size:13px;color:#9CA3AF">No VIP visitors inside right now</div></div>
              </div>
            </div>
          </div>
          <div class="card">
            <div class="card-header" style="background:linear-gradient(135deg,#EDE9FE,#DDD6FE);border-bottom:1px solid #C4B5FD">
              <span class="card-title" style="color:#5B21B6">📅 Upcoming VIP Visits</span>
              <span id="vip-upcoming-count" class="badge badge-purple" style="font-size:11px">0</span>
            </div>
            <div class="card-body" style="padding:14px;max-height:320px;overflow-y:auto">
              <div id="vip-upcoming-list" style="display:flex;flex-direction:column;gap:8px">
                <div class="empty-state" style="padding:20px"><div style="font-size:28px;margin-bottom:6px">📅</div><div style="font-weight:600;font-size:13px;color:#9CA3AF">No upcoming VIP visits</div></div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: QR Scan Result (Auto Check-In/Out)           -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-scan-result" class="modal-overlay" style="display:none;z-index:9999" onclick="if(event.target===this)closeScanModal()">
        <div class="modal" style="max-width:420px;border-radius:24px;overflow:hidden;animation:scanModalPop .35s cubic-bezier(.2,1,.3,1)">
          <div class="scan-timer-bar"><div class="stb-fill" id="scan-timer-fill"></div></div>
          <!-- Visitor Header -->
          <div style="padding:24px 20px 12px;text-align:center">
            <div id="scan-modal-avatar" style="width:72px;height:72px;border-radius:50%;margin:0 auto 12px;overflow:hidden;display:flex;align-items:center;justify-content:center;border:3px solid #D1FAE5;background:#F0FDF4"></div>
            <div id="scan-modal-name" style="font-size:20px;font-weight:800;color:#1F2937"></div>
            <div id="scan-modal-sub" style="font-size:13px;color:#6B7280;margin-top:3px"></div>
          </div>
          <!-- Status Block -->
          <div id="scan-modal-status" class="scan-modal-status"></div>
          <!-- Info Grid -->
          <div id="scan-modal-info" class="scan-info-grid"></div>
          <!-- Action -->
          <div id="scan-modal-action" style="padding:14px 20px 22px;text-align:center"></div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Pre-Register Visitor                         -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-prereg" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-prereg')">
        <div class="modal" style="max-width:640px">
          <div class="modal-header"><span class="modal-title">Pre-Register Visitor</span><button class="modal-close" onclick="closeModal('modal-prereg')">&times;</button></div>
          <div class="modal-body">
            <div class="grid-2">
              <div class="form-control"><label>Full Name <span style="color:red">*</span></label><input type="text" class="form-input" id="prereg-name" placeholder="Expected visitor name"></div>
              <div class="form-control"><label>Company</label><input type="text" class="form-input" id="prereg-company" placeholder="Company name"></div>
            </div>
            <!-- Visitor Type Selection -->
            <div class="form-control" style="margin-bottom:4px">
              <label>Visitor Type <span style="color:red">*</span></label>
              <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px">
                <label style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:2px solid #E5E7EB;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;transition:all .2s" class="vtype-option" data-target="prereg">
                  <input type="radio" name="prereg-visitor-type" value="regular" checked style="accent-color:#059669"> 👤 Regular
                </label>
                <label style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:2px solid #E5E7EB;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;transition:all .2s" class="vtype-option" data-target="prereg">
                  <input type="radio" name="prereg-visitor-type" value="vip" style="accent-color:#F59E0B"> ⭐ VIP
                </label>
                <label style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:2px solid #E5E7EB;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;transition:all .2s" class="vtype-option" data-target="prereg">
                  <input type="radio" name="prereg-visitor-type" value="contractor" style="accent-color:#3B82F6"> 🔧 Contractor
                </label>
                <label style="display:flex;align-items:center;gap:6px;padding:8px 14px;border:2px solid #E5E7EB;border-radius:10px;cursor:pointer;font-size:13px;font-weight:600;transition:all .2s" class="vtype-option" data-target="prereg">
                  <input type="radio" name="prereg-visitor-type" value="government_official" style="accent-color:#8B5CF6"> 🏛️ Gov. Official
                </label>
              </div>
            </div>
            <!-- VIP-specific fields (shown when VIP or Gov Official selected) -->
            <div id="prereg-vip-fields" class="vip-fields-box" style="display:none">
              <div class="vip-fields-title">⭐ VIP / Priority Visitor Settings</div>
              <div class="grid-2">
                <div class="form-control">
                  <label>Security Level</label>
                  <select class="form-input" id="prereg-security-level">
                    <option value="standard">Standard</option>
                    <option value="elevated" selected>Elevated</option>
                    <option value="high">High</option>
                    <option value="executive">Executive</option>
                  </select>
                </div>
                <div class="form-control" style="display:flex;flex-direction:column;gap:8px;padding-top:4px">
                  <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
                    <input type="checkbox" id="prereg-parking" style="accent-color:#059669"> 🅿️ Parking Required
                  </label>
                  <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
                    <input type="checkbox" id="prereg-escort" style="accent-color:#059669"> 🛡️ Escort Required
                  </label>
                </div>
              </div>
            </div>
            <div class="grid-2">
              <div class="form-control"><label>Email</label><input type="email" class="form-input" id="prereg-email" placeholder="visitor@email.com"></div>
              <div class="form-control"><label>Phone</label><input type="tel" class="form-input" id="prereg-phone" placeholder="09XX-XXX-XXXX"></div>
            </div>
            <div class="grid-2">
              <div class="form-control">
                <label>Valid ID Type</label>
                <select class="form-input" id="prereg-id-type">
                  <option value="">-- Select ID Type --</option>
                  <optgroup label="Government IDs">
                    <option value="national_id">Philippine National ID (PhilSys)</option>
                    <option value="passport">Passport</option>
                    <option value="drivers_license">Driver's License</option>
                    <option value="voters_id">Voter's ID</option>
                    <option value="sss_id">SSS ID</option>
                    <option value="philhealth_id">PhilHealth ID</option>
                    <option value="pagibig_id">Pag-IBIG ID</option>
                    <option value="tin_id">TIN ID</option>
                    <option value="postal_id">Postal ID</option>
                    <option value="senior_citizen_id">Senior Citizen ID</option>
                    <option value="pwd_id">PWD ID</option>
                  </optgroup>
                  <optgroup label="Other IDs">
                    <option value="company_id">Company ID</option>
                    <option value="school_id">School ID</option>
                    <option value="barangay_id">Barangay ID</option>
                    <option value="government_id">Other Government ID</option>
                    <option value="other">Other</option>
                  </optgroup>
                </select>
              </div>
              <div class="form-control"><label>ID Number</label><input type="text" class="form-input" id="prereg-id-number" placeholder="ID number"></div>
            </div>
            <div class="grid-2">
              <div class="form-control"><label>Expected Date <span style="color:red">*</span></label><input type="date" class="form-input" id="prereg-date"></div>
              <div class="form-control"><label>Expected Time</label><input type="time" class="form-input" id="prereg-time"></div>
            </div>
            <div class="grid-2">
              <div class="form-control">
                <label>Purpose <span style="color:red">*</span></label>
                <select class="form-input" id="prereg-purpose">
                  <option value="meeting">Meeting</option>
                  <option value="delivery">Delivery</option>
                  <option value="interview">Interview</option>
                  <option value="inspection">Inspection</option>
                  <option value="consultation">Consultation</option>
                  <option value="maintenance">Maintenance</option>
                  <option value="partnership">Partnership</option>
                  <option value="educational">Educational</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="form-control">
                <label>Host / Contact Person <span style="color:red">*</span></label>
                <select class="form-input" id="prereg-host">
                  <option value="">-- Select Host --</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-prereg')">Cancel</button>
            <button class="btn btn-primary" onclick="submitPreRegister()">Pre-Register</button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Check-In Form                                -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-checkin" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-checkin')">
        <div class="modal" style="max-width:560px">
          <div class="modal-header"><span class="modal-title">Check-In Visitor</span><button class="modal-close" onclick="closeModal('modal-checkin')">&times;</button></div>
          <div class="modal-body">
            <div style="background:#D1FAE5;padding:12px 16px;border-radius:10px;margin-bottom:16px;display:flex;align-items:center;gap:12px">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              <div>
                <div id="checkin-visitor-name" style="font-weight:700;color:#065F46"></div>
                <div id="checkin-visitor-company" style="font-size:12px;color:#047857"></div>
              </div>
            </div>
            <input type="hidden" id="checkin-visitor-id">
            <input type="hidden" id="checkin-visitor-type" value="regular">
            <div class="grid-2">
              <div class="form-control">
                <label>Purpose <span style="color:red">*</span></label>
                <select class="form-input" id="checkin-purpose" onchange="togglePurposeDetails('checkin')">
                  <option value="meeting">Meeting</option>
                  <option value="delivery">Delivery</option>
                  <option value="interview">Interview</option>
                  <option value="inspection">Inspection</option>
                  <option value="consultation">Consultation</option>
                  <option value="maintenance">Maintenance</option>
                  <option value="partnership">Partnership</option>
                  <option value="educational">Educational</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="form-control"><label>Badge Number</label><input type="text" class="form-input" id="checkin-badge" placeholder="e.g. B-001"></div>
            </div>
            <!-- VIP check-in extras (shown dynamically) -->
            <div id="checkin-vip-fields" class="vip-fields-box" style="display:none">
              <div class="vip-fields-title">⭐ VIP Check-In Options</div>
              <div class="grid-2">
                <div class="form-control">
                  <label>Security Level</label>
                  <select class="form-input" id="checkin-security-level">
                    <option value="standard">Standard</option>
                    <option value="elevated">Elevated</option>
                    <option value="high">High</option>
                    <option value="executive">Executive</option>
                  </select>
                </div>
                <div class="form-control">
                  <label>Access Level</label>
                  <select class="form-input" id="checkin-access-level">
                    <option value="lobby_only">Lobby Only</option>
                    <option value="general">General</option>
                    <option value="executive_floor">Executive Floor</option>
                    <option value="all_access">All Access</option>
                  </select>
                </div>
              </div>
              <div style="display:flex;gap:16px;margin-top:8px">
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
                  <input type="checkbox" id="checkin-escort" style="accent-color:#059669"> 🛡️ Escort Required
                </label>
                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
                  <input type="checkbox" id="checkin-id-verified" style="accent-color:#059669"> ✅ ID Verified
                </label>
              </div>
            </div>
            <div class="grid-2">
              <div class="form-control"><label>Host Name</label><input type="text" class="form-input" id="checkin-host-name" placeholder="Person to visit"></div>
              <div class="form-control">
                <label>Host Department</label>
                <select class="form-input" id="checkin-host-dept">
                  <option>Executive Office</option>
                  <option>Human Resources</option>
                  <option>Finance Dept.</option>
                  <option>IT Department</option>
                  <option>Legal Dept.</option>
                  <option>Compliance Dept.</option>
                  <option>Operations</option>
                  <option>Credit Department</option>
                </select>
              </div>
            </div>
            <!-- Purpose Breakdown (dynamic) -->
            <div id="checkin-purpose-breakdown" style="display:none">
              <div id="checkin-partnership-fields" style="display:none">
                <div class="grid-2">
                  <div class="form-control"><label>Partner Organization</label><input type="text" class="form-input" id="checkin-partner-org" placeholder="Organization name"></div>
                  <div class="form-control">
                    <label>Partnership Type</label>
                    <select class="form-input" id="checkin-partner-type">
                      <option value="business">Business Partnership</option>
                      <option value="mou">MOU Discussion</option>
                      <option value="joint_venture">Joint Venture</option>
                      <option value="sponsorship">Sponsorship</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>
              </div>
              <div id="checkin-educational-fields" style="display:none">
                <div class="grid-2">
                  <div class="form-control"><label>Institution</label><input type="text" class="form-input" id="checkin-institution" placeholder="School/University"></div>
                  <div class="form-control">
                    <label>Educational Purpose</label>
                    <select class="form-input" id="checkin-edu-type">
                      <option value="thesis">Thesis/Research</option>
                      <option value="ojt">OJT/Internship</option>
                      <option value="seminar">Seminar/Workshop</option>
                      <option value="benchmarking">Benchmarking</option>
                      <option value="field_study">Field Study</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-control"><label>Purpose Details / Notes</label><textarea class="form-input" id="checkin-details" rows="2" placeholder="Additional details about the visit"></textarea></div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-checkin')">Cancel</button>
            <button class="btn btn-primary" onclick="submitCheckIn()">Check In Now</button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Visitor Detail                               -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-visitor-detail" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-visitor-detail')">
        <div class="modal" style="max-width:560px">
          <div class="modal-header"><span class="modal-title" id="detail-title">Visitor Details</span><button class="modal-close" onclick="closeModal('modal-visitor-detail')">&times;</button></div>
          <div class="modal-body" id="detail-body"></div>
          <div class="modal-footer"><button class="btn btn-outline" onclick="closeModal('modal-visitor-detail')">Close</button></div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Visitor Tag (QR + Photo + Name)              -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-visitor-tag" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-visitor-tag')">
        <div class="modal" style="max-width:420px">
          <div class="modal-header">
            <span class="modal-title">🏷️ Visitor Tag</span>
            <button class="modal-close" onclick="closeModal('modal-visitor-tag')">&times;</button>
          </div>
          <div class="modal-body" style="padding:24px;text-align:center">
            <div id="visitor-tag-content" style="display:inline-block;width:100%">
              <div style="background:linear-gradient(135deg,#059669 0%,#047857 100%);border-radius:16px;padding:20px;color:#fff;margin-bottom:16px">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;opacity:0.8;margin-bottom:2px">Microfinancial Management System</div>
                <div style="font-size:22px;font-weight:800;letter-spacing:1px">VISITOR PASS</div>
              </div>
              <div style="background:white;border:2px solid #D1FAE5;border-radius:16px;padding:24px;box-shadow:0 4px 12px rgba(0,0,0,0.06)">
                <!-- Photo -->
                <div id="tag-photo-container" style="width:120px;height:120px;border-radius:50%;margin:0 auto 16px;overflow:hidden;border:4px solid #059669;background:#F3F4F6;display:flex;align-items:center;justify-content:center">
                  <svg id="tag-photo-placeholder" xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                  <img id="tag-photo-img" src="" alt="" style="width:100%;height:100%;object-fit:cover;display:none">
                </div>
                <!-- Name -->
                <div id="tag-visitor-name" style="font-size:20px;font-weight:800;color:#1F2937;margin-bottom:4px"></div>
                <div id="tag-visitor-company" style="font-size:14px;color:#6B7280;margin-bottom:16px"></div>
                <!-- QR Code -->
                <div style="background:#F0FDF4;border-radius:12px;padding:16px;margin-bottom:12px">
                  <img id="tag-qr-img" src="" alt="QR Code" style="width:160px;height:160px;border-radius:8px;margin:0 auto;display:block">
                  <div id="tag-visitor-code" style="font-weight:700;color:#059669;font-size:16px;letter-spacing:2px;margin-top:10px"></div>
                </div>
                <!-- Details -->
                <div style="display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-bottom:12px">
                  <div id="tag-id-badge" style="display:none;background:#EFF6FF;color:#1D4ED8;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600"></div>
                  <div id="tag-date-badge" style="background:#F0FDF4;color:#059669;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600"></div>
                </div>
                <div style="font-size:10px;color:#9CA3AF;border-top:1px solid #E5E7EB;padding-top:12px">
                  Present this pass at the front desk for verification<br>
                  Valid for single-day entry only
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer" style="justify-content:center;gap:8px">
            <button class="btn btn-outline btn-sm" onclick="closeModal('modal-visitor-tag')">Close</button>
            <button class="btn btn-primary btn-sm" onclick="printVisitorTag()">🖨️ Print Tag</button>
            <button class="btn btn-sm" style="background:#3B82F6;color:#fff;border:none" onclick="downloadVisitorTag()">📥 Download</button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Upload Photo                                 -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-upload-photo" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-upload-photo')">
        <div class="modal" style="max-width:480px">
          <div class="modal-header"><span class="modal-title">📷 Upload Visitor Photo</span><button class="modal-close" onclick="closeModal('modal-upload-photo')">&times;</button></div>
          <div class="modal-body" style="padding:24px;text-align:center">
            <input type="hidden" id="upload-photo-visitor-id">
            <div id="upload-photo-preview" style="width:160px;height:160px;border-radius:50%;margin:0 auto 20px;overflow:hidden;border:4px solid #D1FAE5;background:#F3F4F6;display:flex;align-items:center;justify-content:center">
              <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <input type="file" id="upload-photo-file" accept="image/*" style="display:none" onchange="previewUploadPhoto(this)">
            <div style="display:flex;gap:8px;justify-content:center;margin-bottom:16px">
              <button class="btn btn-outline btn-sm" onclick="document.getElementById('upload-photo-file').click()">📁 Choose File</button>
              <button class="btn btn-outline btn-sm" onclick="captureUploadPhoto()">📸 Take Photo</button>
            </div>
            <div style="font-size:12px;color:#9CA3AF">Accepted: JPG, PNG. Max 2MB. Photo will appear on the Visitor Tag.</div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-upload-photo')">Cancel</button>
            <button class="btn btn-primary" onclick="submitUploadPhoto()">Save Photo</button>
          </div>
        </div>
      </div>

    </main>
  </div>

<script src="../../admin.js?v=20260304"></script>
<script src="../../export.js?v=20260304"></script>
<script>
/* ═══════════════════════════════════════════════════════
   VISITOR MANAGEMENT MODULE — Full API Integration
   ═══════════════════════════════════════════════════════ */

const API = '../../api/visitors.php';

// Data stores
let visitors = [], logs = [], preregs = [], stats = {};
let purposeChart = null;
let scannerStream = null;
let scannerInterval = null;
let logViewByDay = false;
let logRepeatOnly = false;
let qrFlipState = 'photo';

// ───── Purpose Breakdown Toggle ─────
function togglePurposeDetails(prefix) {
  const purpose = document.getElementById(prefix + '-purpose').value;
  const breakdown = document.getElementById(prefix + '-purpose-breakdown');
  if (!breakdown) return;
  const partnerFields = document.getElementById(prefix + '-partnership-fields');
  const eduFields = document.getElementById(prefix + '-educational-fields');
  breakdown.style.display = (purpose === 'partnership' || purpose === 'educational') ? 'block' : 'none';
  if (partnerFields) partnerFields.style.display = purpose === 'partnership' ? 'block' : 'none';
  if (eduFields) eduFields.style.display = purpose === 'educational' ? 'block' : 'none';
}

// ───── Helpers ─────
function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
function photoSrc(url) { if (!url) return ''; if (url.startsWith('http') || url.startsWith('data:') || url.startsWith('../../') || url.startsWith('blob:')) return url; return '../../' + url; }
function fmtDate(d) { if (!d) return '—'; return new Date(d).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }); }
function fmtTime(d) { if (!d) return '—'; return new Date(d).toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', hour12:true }); }
function fmtDateTime(d) { if (!d) return '—'; return fmtDate(d) + ' ' + fmtTime(d); }
function labelCase(s) { return (s || '').replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase()); }

const idTypeLabels = {
  national_id: 'Philippine National ID',
  passport: 'Passport',
  drivers_license: "Driver's License",
  voters_id: "Voter's ID",
  sss_id: 'SSS ID',
  philhealth_id: 'PhilHealth ID',
  pagibig_id: 'Pag-IBIG ID',
  tin_id: 'TIN ID',
  postal_id: 'Postal ID',
  senior_citizen_id: 'Senior Citizen ID',
  pwd_id: 'PWD ID',
  company_id: 'Company ID',
  school_id: 'School ID',
  barangay_id: 'Barangay ID',
  government_id: 'Other Government ID',
  other: 'Other'
};
function idTypeLabel(key) { return idTypeLabels[key] || labelCase(key); }

function statusBadge(status) {
  const map = {
    'checked_in': 'badge-green', 'checked_out': 'badge-gray', 'pre_registered': 'badge-blue',
    'cancelled': 'badge-red', 'no_show': 'badge-red', 'pending': 'badge-amber',
    'approved': 'badge-green', 'expired': 'badge-gray', 'rejected': 'badge-red'
  };
  return `<span class="badge ${map[status] || 'badge-gray'}">${labelCase(status)}</span>`;
}

// ───── Visitor Type Helpers ─────
const VTYPE_CONFIG = {
  regular:             { label:'Regular',     icon:'👤', color:'#6B7280', bg:'#F3F4F6' },
  vip:                 { label:'VIP',         icon:'⭐', color:'#92400E', bg:'#FEF3C7' },
  contractor:          { label:'Contractor',  icon:'🔧', color:'#1E40AF', bg:'#DBEAFE' },
  government_official: { label:'Gov. Official', icon:'🏛️', color:'#5B21B6', bg:'#EDE9FE' }
};
function vtypeBadge(type) {
  const c = VTYPE_CONFIG[type] || VTYPE_CONFIG.regular;
  return `<span class="vip-badge vip-${type||'regular'}">${c.icon} ${c.label}</span>`;
}
function vtypeIndicator(type) {
  return `<span class="vip-indicator vi-${type||'regular'}" title="${(VTYPE_CONFIG[type]||VTYPE_CONFIG.regular).label}"></span>`;
}
// Toggle VIP fields when visitor type radio changes
function setupVtypeRadios(prefix) {
  document.querySelectorAll(`input[name="${prefix}-visitor-type"]`).forEach(radio => {
    radio.addEventListener('change', () => {
      const isVip = radio.value === 'vip' || radio.value === 'government_official';
      const box = document.getElementById(`${prefix}-vip-fields`);
      if (box) box.style.display = isVip ? 'block' : 'none';
      // Highlight selected radio label
      radio.closest('.form-control, div').querySelectorAll('.vtype-option').forEach(lbl => {
        const inp = lbl.querySelector('input');
        lbl.style.borderColor = inp.checked ? (VTYPE_CONFIG[inp.value]||VTYPE_CONFIG.regular).color : '#E5E7EB';
        lbl.style.background = inp.checked ? (VTYPE_CONFIG[inp.value]||VTYPE_CONFIG.regular).bg : '#fff';
      });
    });
  });
}

function duration(checkIn, checkOut) {
  if (!checkIn) return '—';
  if (!checkOut) return '<span class="badge badge-green">Active</span>';
  const mins = Math.round((new Date(checkOut) - new Date(checkIn)) / 60000);
  if (mins < 60) return mins + 'm';
  const h = Math.floor(mins / 60);
  const m = mins % 60;
  return h + 'h ' + (m ? m + 'm' : '');
}

// Domain for QR verification — works from any network (WiFi, mobile data, etc.)
const QR_DOMAIN = window.location.origin;

function qrUrl(code, size) {
  // Public QR page — no login required, accessible by any device on any network
  const verifyPage = QR_DOMAIN + '/Admin/public/verify.php?token=' + encodeURIComponent(code);
  return 'https://api.qrserver.com/v1/create-qr-code/?data=' + encodeURIComponent(verifyPage) + '&size=' + (size||40) + 'x' + (size||40) + '&color=059669';
}

// Extract raw visitor code from verify URL (scanned QR encodes full URL)
// Handles both new ?token= (public page) and legacy ?code= param
function extractVisitorCode(raw) {
  if (!raw) return '';
  try {
    const u = new URL(raw);
    if (u.pathname.includes('verify')) {
      const t = u.searchParams.get('token'); if (t) return t.trim();
      const c = u.searchParams.get('code');  if (c) return c.trim();
    }
  } catch(_){}
  return raw.trim();
}

// ───── Data Loading ─────
let hosts = [];

function populateHostDropdown() {
  const sel = document.getElementById('prereg-host');
  if (!sel) return;
  sel.innerHTML = '<option value="">-- Select Host --</option>' + hosts.map(h => {
    const name = ((h.first_name || '') + ' ' + (h.last_name || '')).trim();
    const dept = h.department ? ' — ' + h.department : '';
    return `<option value="${h.user_id}">${name}${dept}</option>`;
  }).join('');
}

async function loadData() {
  try {
    const [sRes, vRes, lRes, pRes, hRes] = await Promise.all([
      fetch(API + '?action=dashboard_stats'),
      fetch(API + '?action=list_visitors'),
      fetch(API + '?action=list_logs'),
      fetch(API + '?action=list_preregistrations'),
      fetch(API + '?action=list_hosts')
    ]);
    stats = await sRes.json();
    visitors = (await vRes.json()).data || [];
    logs = (await lRes.json()).data || [];
    preregs = (await pRes.json()).data || [];
    hosts = (await hRes.json()).data || [];

    renderStats();
    renderVisitors();
    renderPreregSub();
    renderLogFolders();
    renderLogs();
    populateQRSelect();
    populateHostDropdown();
    renderAnalytics();
    renderPreregTab();
    setupVtypeRadios('prereg');
    if (typeof refreshSidebarCounts === 'function') refreshSidebarCounts();
  } catch(e) {
    console.error('Load error:', e);
    Swal.fire({ icon:'error', title:'Load Error', text:'Failed to load visitor data. Check API connection.', confirmButtonColor:'#059669' });
  }
}

// ───── Render Stats ─────
function renderStats() {
  document.getElementById('stat-total').textContent = stats.total_visitors ?? 0;
  document.getElementById('stat-preregs').textContent = stats.pending_preregs ?? 0;
  document.getElementById('stat-today').textContent = stats.today_visits ?? 0;
}

// ───── Render Visitors Table ─────
function renderVisitors() {
  const tbody = document.getElementById('visitors-tbody');
  const searchVal = (document.getElementById('visitor-search')?.value || '').toLowerCase();

  // Only show visitors who are currently checked in OR have never been checked in (fresh)
  // Checked-out visitors go to Visit History & Logs
  let filtered = visitors.filter(v => {
    const hasActiveLog = logs.some(l => l.visitor_id == v.visitor_id && l.status === 'checked_in');
    const hasAnyLog = logs.some(l => l.visitor_id == v.visitor_id);
    return hasActiveLog || !hasAnyLog; // currently checked in OR never checked in
  });

  if (searchVal) {
    filtered = filtered.filter(v => {
      const name = ((v.first_name || '') + ' ' + (v.last_name || '')).toLowerCase();
      const code = (v.visitor_code || '').toLowerCase();
      const comp = (v.company || '').toLowerCase();
      return name.includes(searchVal) || code.includes(searchVal) || comp.includes(searchVal);
    });
  }
  updateBadges();
  if (!filtered.length) {
    tbody.innerHTML = '<tr><td colspan="7"><div class="empty-state" style="padding:30px"><div style="font-size:40px;margin-bottom:8px">👤</div><div style="font-weight:600">No visitors registered yet</div><div style="font-size:13px;color:#9CA3AF">Register a visitor using the button above.</div></div></td></tr>';
    return;
  }
  tbody.innerHTML = filtered.map(v => {
    const name = esc(v.first_name + ' ' + v.last_name);
    const hasActiveLog = logs.some(l => l.visitor_id == v.visitor_id && l.status === 'checked_in');

    // Action buttons: if currently checked in → show Check Out + View; otherwise → Check In + View
    let actionHtml = '';
    if (hasActiveLog) {
      actionHtml = `
        <button class="btn btn-outline btn-sm" style="color:#DC2626;border-color:#DC2626" onclick="checkOutVisitor(${v.visitor_id})" title="Check Out">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </button>
        <button class="btn btn-outline btn-sm" onclick="viewVisitorDetail(${v.visitor_id})" title="View Details">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>`;
    } else {
      actionHtml = `
        <button class="btn btn-primary btn-sm" onclick="openCheckInModal(${v.visitor_id})" title="Check In">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
        </button>
        <button class="btn btn-outline btn-sm" onclick="viewVisitorDetail(${v.visitor_id})" title="View Details">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>`;
    }

    return `<tr${(v.visitor_type === 'vip' || v.visitor_type === 'government_official') ? ' style="background:#FFFBEB08"' : ''}>
      <td style="font-weight:600;font-size:12px;color:#059669">${vtypeIndicator(v.visitor_type)} ${esc(v.visitor_code)}</td>
      <td style="font-weight:600">${name} ${v.visitor_type && v.visitor_type !== 'regular' ? vtypeBadge(v.visitor_type) : ''}</td>
      <td>${esc(v.company) || '<span style="color:#9CA3AF">—</span>'}</td>
      <td style="font-size:12px">${esc(v.phone) || esc(v.email) || '—'}</td>
      <td style="font-size:12px">
        ${v.id_type
          ? `<div><span style="font-weight:600">${idTypeLabel(v.id_type)}</span>${v.id_number ? `<br><span style="color:#6B7280">${esc(v.id_number)}</span>` : ''}</div>`
          : '<span style="color:#9CA3AF">—</span>'}
      </td>
      <td><img src="${qrUrl(v.visitor_code)}" alt="QR" style="width:32px;height:32px;border-radius:4px;cursor:pointer" onclick="showVisitorTag(${v.visitor_id})" title="View Visitor Tag"></td>
      <td>
        <div style="display:flex;gap:4px">
          ${actionHtml}
        </div>
      </td>
    </tr>`;
  }).join('');
}

// ───── Sub-tab Switching ─────
function switchSubTab(btn, tab) {
  document.querySelectorAll('.sub-tab').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.sub-tab-content').forEach(c => c.classList.remove('active'));
  btn.classList.add('active');
  const target = document.getElementById('subtab-' + tab);
  if (target) target.classList.add('active');
  if (tab === 'prereg') renderPreregSub();
}

// ───── Update badge counts ─────
function updateBadges() {
  // Count only active visitors (checked in or never checked in)
  const activeCount = visitors.filter(v => {
    const hasActiveLog = logs.some(l => l.visitor_id == v.visitor_id && l.status === 'checked_in');
    const hasAnyLog = logs.some(l => l.visitor_id == v.visitor_id);
    return hasActiveLog || !hasAnyLog;
  }).length;
  const regEl = document.getElementById('badge-registered');
  if (regEl) regEl.textContent = activeCount;
  const pendingCount = preregs.filter(p => (p.status || '').toLowerCase() === 'pending').length;
  const preEl = document.getElementById('badge-prereg');
  if (preEl) preEl.textContent = pendingCount;
}

// ───── Render Pre-Registrations Sub-Tab ─────
function renderPreregSub() {
  const tbody = document.getElementById('preregs-tbody');
  if (!tbody) return;
  const search = (document.getElementById('prereg-sub-search')?.value || '').toLowerCase();
  const pending = preregs.filter(p => (p.status || '').toLowerCase() === 'pending');
  let filtered = pending;
  if (search) {
    filtered = pending.filter(p => {
      const name = (p.visitor_name || '').toLowerCase();
      const code = (p.prereg_code || '').toLowerCase();
      const comp = (p.visitor_company || p.company || '').toLowerCase();
      return name.includes(search) || code.includes(search) || comp.includes(search);
    });
  }
  const countEl = document.getElementById('prereg-sub-count');
  if (countEl) countEl.textContent = filtered.length + ' pending';

  if (!filtered.length) {
    tbody.innerHTML = '<tr><td colspan="8"><div class="empty-state" style="padding:24px"><div style="font-size:36px;margin-bottom:8px">📋</div><div style="font-weight:600">No pending pre-registrations</div><div style="font-size:13px;color:#9CA3AF;margin-top:4px">All pre-registrations have been processed.</div></div></td></tr>';
    return;
  }
  tbody.innerHTML = filtered.map(p => {
    const code = esc(p.prereg_code);
    return `<tr>
      <td style="font-weight:600;font-size:12px;color:#059669">${code}</td>
      <td style="font-weight:600">${esc(p.visitor_name)} ${p.visitor_type && p.visitor_type !== 'regular' ? vtypeBadge(p.visitor_type) : ''}</td>
      <td>${esc(p.visitor_company || p.company) || '—'}</td>
      <td>${labelCase(p.purpose)}</td>
      <td style="font-size:12px">${fmtDate(p.expected_date)}${p.expected_time ? '<br>' + p.expected_time : ''}</td>
      <td>${esc(p.host_name) || '—'}</td>
      <td>${statusBadge(p.status)}</td>
      <td>
        <div style="display:flex;gap:4px">
          <button class="btn btn-sm" style="background:#059669;color:#fff;border:none" onclick="approvePrereg('${code}')" title="Approve">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
          </button>
          <button class="btn btn-sm" style="background:#DC2626;color:#fff;border:none" onclick="rejectPrereg('${code}')" title="Reject">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          </button>
          <button class="btn btn-outline btn-sm" onclick="viewPreregDetail('${code}')" title="View">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
          </button>
        </div>
      </td>
    </tr>`;
  }).join('');
}

// ───── Render Log Folders (grouped by visitor) ─────
function renderLogFolders() {
  const container = document.getElementById('log-folders-container');
  if (!container) return;
  const search = (document.getElementById('folder-search')?.value || '').toLowerCase();

  // Group logs by visitor_id
  const grouped = {};
  logs.forEach(l => {
    const vid = l.visitor_id;
    if (!grouped[vid]) grouped[vid] = { name: l.visitor_name, company: l.company, visitor_id: vid, logs: [] };
    grouped[vid].logs.push(l);
  });

  let folders = Object.values(grouped);
  // Sort by most recent visit
  folders.sort((a, b) => new Date(b.logs[0].check_in_time || 0) - new Date(a.logs[0].check_in_time || 0));

  if (search) {
    folders = folders.filter(f => (f.name || '').toLowerCase().includes(search) || (f.company || '').toLowerCase().includes(search));
  }

  const countEl = document.getElementById('folder-count');
  if (countEl) countEl.textContent = folders.length + ' visitor' + (folders.length !== 1 ? 's' : '');

  if (!folders.length) {
    container.innerHTML = '<div class="empty-state" style="padding:24px;grid-column:1/-1"><div style="font-size:36px;margin-bottom:8px">📁</div><div style="font-weight:600">No visitor logs yet</div></div>';
    return;
  }

  container.innerHTML = folders.map(f => {
    const totalVisits = f.logs.length;
    const checkedIn = f.logs.filter(l => l.status === 'checked_in').length;
    const lastLog = f.logs[0];
    const lastDate = fmtDate(lastLog.check_in_time);
    const statusDot = checkedIn > 0
      ? '<span style="width:8px;height:8px;border-radius:50%;background:#10B981;display:inline-block" title="Currently checked in"></span>'
      : '<span style="width:8px;height:8px;border-radius:50%;background:#9CA3AF;display:inline-block" title="Checked out"></span>';

    return `<div onclick="viewVisitorHistory(${f.visitor_id})" style="background:#fff;border:1px solid #E5E7EB;border-radius:10px;padding:14px 16px;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;gap:12px;hover:shadow" onmouseover="this.style.borderColor='#059669';this.style.boxShadow='0 2px 8px rgba(5,150,105,0.1)'" onmouseout="this.style.borderColor='#E5E7EB';this.style.boxShadow='none'">
      <div style="width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,#ECFDF5,#D1FAE5);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">📂</div>
      <div style="flex:1;min-width:0">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:2px">
          ${statusDot}
          <span style="font-weight:700;font-size:13px;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(f.name)}</span>
        </div>
        <div style="font-size:11px;color:#6B7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(f.company) || 'No company'}</div>
        <div style="font-size:10px;color:#9CA3AF;margin-top:2px">Last: ${lastDate}</div>
      </div>
      <div style="text-align:center;flex-shrink:0">
        <div style="background:#DBEAFE;color:#1D4ED8;font-weight:700;font-size:14px;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center">${totalVisits}</div>
        <div style="font-size:9px;color:#6B7280;margin-top:2px">visit${totalVisits !== 1 ? 's' : ''}</div>
      </div>
    </div>`;
  }).join('');
}

// ───── Clear Log Filters ─────
function clearLogFilters() {
  ['log-filter-search','log-filter-status','log-filter-purpose','log-filter-dept','log-filter-from','log-filter-to'].forEach(id => document.getElementById(id).value = '');
  logRepeatOnly = false;
  logViewByDay = false;
  const repeatBtn = document.getElementById('log-filter-repeat');
  if (repeatBtn) { repeatBtn.style.background = ''; repeatBtn.style.color = ''; repeatBtn.style.borderColor = ''; repeatBtn.classList.remove('active','active-blue'); }
  const dayBtn = document.getElementById('log-filter-byday');
  if (dayBtn) { dayBtn.style.background = ''; dayBtn.style.color = ''; dayBtn.style.borderColor = ''; dayBtn.classList.remove('active','active-blue'); }
  renderLogs();
}

// ───── Render Logs ─────
function renderLogs() {
  const tbody = document.getElementById('logs-tbody');
  const filterStatus = document.getElementById('log-filter-status')?.value || '';
  const filterPurpose = document.getElementById('log-filter-purpose')?.value || '';
  const filterDept = document.getElementById('log-filter-dept')?.value || '';
  const filterSearch = (document.getElementById('log-filter-search')?.value || '').toLowerCase().trim();
  const filterFrom = document.getElementById('log-filter-from')?.value || '';
  const filterTo = document.getElementById('log-filter-to')?.value || '';

  let filtered = logs;
  if (filterStatus) filtered = filtered.filter(l => l.status === filterStatus);
  if (filterPurpose) filtered = filtered.filter(l => l.purpose === filterPurpose);
  if (filterDept) filtered = filtered.filter(l => (l.host_department || '') === filterDept);
  if (filterSearch) filtered = filtered.filter(l => (l.visitor_name || '').toLowerCase().includes(filterSearch) || (l.company || '').toLowerCase().includes(filterSearch));
  if (filterFrom) filtered = filtered.filter(l => l.check_in_time && l.check_in_time.slice(0,10) >= filterFrom);
  if (filterTo) filtered = filtered.filter(l => l.check_in_time && l.check_in_time.slice(0,10) <= filterTo);

  // Repeat visitors only filter
  if (logRepeatOnly) {
    const repeatIds = new Set(visitors.filter(v => (v.visit_count || 0) > 1).map(v => String(v.visitor_id)));
    filtered = filtered.filter(l => repeatIds.has(String(l.visitor_id)));
  }

  const countEl = document.getElementById('log-result-count');
  if (countEl) countEl.textContent = filtered.length + ' records' + (logRepeatOnly ? ' (repeat visitors)' : '') + (logViewByDay ? ' · by day' : '');

  if (!filtered.length) {
    tbody.innerHTML = '<tr><td colspan="9"><div class="empty-state" style="padding:30px"><div style="font-size:40px;margin-bottom:8px">📋</div><div style="font-weight:600">No visitor logs found</div></div></td></tr>';
    return;
  }

  // Group by day mode
  if (logViewByDay) {
    renderLogsByDay(filtered, tbody);
    return;
  }

  tbody.innerHTML = filtered.map(l => `<tr>
    <td style="font-weight:600;font-size:12px">${esc(l.visit_code)}</td>
    <td><a href="javascript:void(0)" onclick="viewVisitorHistory(${l.visitor_id})" style="font-weight:600;color:#059669;text-decoration:none;cursor:pointer" title="Click to see all visits">${esc(l.visitor_name)}</a></td>
    <td>${esc(l.company) || '—'}</td>
    <td>${labelCase(l.purpose)}</td>
    <td style="font-size:12px">${fmtTime(l.check_in_time)}<br><span style="color:#9CA3AF">${fmtDate(l.check_in_time)}</span></td>
    <td style="font-size:12px">${l.check_out_time ? fmtTime(l.check_out_time) : '—'}</td>
    <td>${duration(l.check_in_time, l.check_out_time)}</td>
    <td style="font-size:12px">${esc(l.host_name) || '—'}${l.host_department ? '<br><span style="color:#9CA3AF">' + esc(l.host_department) + '</span>' : ''}</td>
    <td>${statusBadge(l.status)}</td>
  </tr>`).join('');
}

// ───── Toggle: Repeat Visitors Only ─────
function toggleLogRepeat() {
  logRepeatOnly = !logRepeatOnly;
  const btn = document.getElementById('log-filter-repeat');
  if (btn) btn.classList.toggle('active', logRepeatOnly);
  renderLogs();
}

// ───── Toggle: View Logs by Days ─────
function toggleLogByDay() {
  logViewByDay = !logViewByDay;
  const btn = document.getElementById('log-filter-byday');
  if (btn) btn.classList.toggle('active-blue', logViewByDay);
  renderLogs();
}

// ───── Render Logs Grouped by Day ─────
function renderLogsByDay(filtered, tbody) {
  const today = new Date().toISOString().slice(0, 10);
  const groups = {};
  filtered.forEach(l => {
    const date = l.check_in_time ? l.check_in_time.slice(0, 10) : 'unknown';
    if (!groups[date]) groups[date] = [];
    groups[date].push(l);
  });
  const dates = Object.keys(groups).sort().reverse();
  let html = '';
  dates.forEach(date => {
    const dayLogs = groups[date];
    const dateLabel = date === 'unknown' ? 'Unknown Date' : fmtDate(date + 'T00:00:00');
    const isToday = date === today;
    const uniqueVisitors = new Set(dayLogs.map(l => l.visitor_id)).size;
    const repeatInDay = dayLogs.length > uniqueVisitors;
    html += `<tr><td colspan="9" style="background:linear-gradient(135deg,#F0FDF4,#ECFDF5);padding:12px 16px;border-bottom:2px solid #059669;border-top:2px solid #D1FAE5">
      <div style="display:flex;justify-content:space-between;align-items:center">
        <div style="display:flex;align-items:center;gap:10px">
          <span style="font-size:20px">📅</span>
          <div>
            <div style="font-weight:700;color:#065F46;font-size:14px">${dateLabel}${isToday ? ' <span style="background:#059669;color:#fff;padding:2px 8px;border-radius:10px;font-size:10px;margin-left:6px">Today</span>' : ''}</div>
            <div style="font-size:11px;color:#6B7280">${uniqueVisitors} unique visitor${uniqueVisitors !== 1 ? 's' : ''}${repeatInDay ? ' · <span style="color:#F59E0B;font-weight:600">includes repeat</span>' : ''}</div>
          </div>
        </div>
        <span style="background:#DBEAFE;color:#1D4ED8;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600">${dayLogs.length} visit${dayLogs.length !== 1 ? 's' : ''}</span>
      </div>
    </td></tr>`;
    dayLogs.forEach(l => {
      html += `<tr>
        <td style="font-weight:600;font-size:12px">${esc(l.visit_code)}</td>
        <td><a href="javascript:void(0)" onclick="viewVisitorHistory(${l.visitor_id})" style="font-weight:600;color:#059669;text-decoration:none;cursor:pointer">${esc(l.visitor_name)}</a></td>
        <td>${esc(l.company) || '—'}</td>
        <td>${labelCase(l.purpose)}</td>
        <td style="font-size:12px">${fmtTime(l.check_in_time)}</td>
        <td style="font-size:12px">${l.check_out_time ? fmtTime(l.check_out_time) : '—'}</td>
        <td>${duration(l.check_in_time, l.check_out_time)}</td>
        <td style="font-size:12px">${esc(l.host_name) || '—'}${l.host_department ? '<br><span style="color:#9CA3AF">' + esc(l.host_department) + '</span>' : ''}</td>
        <td>${statusBadge(l.status)}</td>
        </td>
      </tr>`;
    });
  });
  tbody.innerHTML = html;
}

// ───── Populate QR Select ─────
function populateQRSelect() {
  const sel = document.getElementById('qr-visitor-select');
  sel.innerHTML = '<option value="">-- Select Visitor --</option>';
  visitors.forEach(v => {
    sel.innerHTML += `<option value="${v.visitor_code}|${esc(v.first_name + ' ' + v.last_name)}|${esc(v.company||'')}|${v.visit_count||0}">${esc(v.first_name + ' ' + v.last_name)} — ${esc(v.company||'No Company')}</option>`;
  });
}

// ───── QR Pass Generator ─────
function generateVisitorQR() {
  const sel = document.getElementById('qr-visitor-select');
  if (!sel.value) return Swal.fire({ icon:'warning', title:'No Visitor Selected', text:'Please select a visitor to generate a QR pass.', confirmButtonColor:'#059669' });
  const [code, name, company] = sel.value.split('|');

  // Find visitor for photo
  const v = visitors.find(x => x.visitor_code === code);

  // Front side - Photo
  const photoEl = document.getElementById('qr-visitor-photo');
  const placeholderEl = document.getElementById('qr-photo-placeholder');
  if (v && v.photo_url) {
    photoEl.src = photoSrc(v.photo_url);
    photoEl.style.display = 'block';
    placeholderEl.style.display = 'none';
  } else {
    photoEl.style.display = 'none';
    placeholderEl.style.display = 'block';
  }
  document.getElementById('qr-visitor-name').textContent = name;
  document.getElementById('qr-visitor-company').textContent = company || 'No Company';
  document.getElementById('qr-visitor-visits').textContent = v ? (v.visit_count || 0) + ' visit(s)' : '';

  // Back side - QR Code
  document.getElementById('qr-visitor-name-back').textContent = name;
  document.getElementById('qr-visitor-company-back').textContent = company || 'No Company';
  document.getElementById('qr-code-img').src = qrUrl(code, 180);
  document.getElementById('qr-code-id').textContent = code;

  // Store data for print and capture
  generateVisitorQR._lastPhoto = (v && v.photo_url) ? photoSrc(v.photo_url) : null;
  generateVisitorQR._lastName = name;
  generateVisitorQR._lastCompany = company || 'No Company';
  generateVisitorQR._lastCode = code;
  generateVisitorQR._lastVisitorId = v ? v.visitor_id : null;

  // Reset flip to show photo first
  const inner = document.getElementById('qr-flip-inner');
  if (inner) inner.classList.remove('flipped');
  qrFlipState = 'photo';

  document.getElementById('qr-preview').style.display = 'block';
}

function printQR() {
  const photoUrl = generateVisitorQR._lastPhoto || '';
  const name = generateVisitorQR._lastName || '';
  const company = generateVisitorQR._lastCompany || '';
  const code = generateVisitorQR._lastCode || '';
  const qr = qrUrl(code, 200);

  const w = window.open('', '_blank', 'width=450,height=700');
  w.document.write(`<html><head><title>Visitor Pass — ${name}</title>
  <style>
    body { display:flex; justify-content:center; align-items:center; min-height:100vh; font-family:Arial,'Segoe UI',sans-serif; margin:0; padding:20px; box-sizing:border-box; }
    .pass { background:#fff; border:2px solid #D1FAE5; border-radius:16px; padding:28px; text-align:center; width:340px; box-shadow:0 4px 20px rgba(0,0,0,0.08); }
    .pass-header { background:linear-gradient(135deg,#059669,#047857); color:#fff; padding:16px; border-radius:12px; margin-bottom:20px; }
    .pass-header h3 { margin:0; font-size:20px; letter-spacing:2px; }
    .pass-header p { margin:4px 0 0; font-size:10px; opacity:0.8; text-transform:uppercase; letter-spacing:1.5px; }
    .photo { width:120px; height:120px; border-radius:50%; margin:0 auto 16px; overflow:hidden; border:4px solid #059669; background:#F3F4F6; }
    .photo img { width:100%; height:100%; object-fit:cover; }
    .name { font-size:20px; font-weight:800; color:#1F2937; margin-bottom:4px; }
    .company { font-size:13px; color:#6B7280; margin-bottom:16px; }
    .qr-box { background:#F0FDF4; border-radius:12px; padding:16px; margin-bottom:12px; }
    .qr-box img { width:180px; height:180px; border-radius:8px; }
    .code { font-size:16px; font-weight:700; color:#059669; letter-spacing:2px; margin-top:8px; }
    .footer { font-size:10px; color:#9CA3AF; border-top:1px solid #E5E7EB; padding-top:12px; margin-top:12px; }
  </style></head><body>
    <div class="pass">
      <div class="pass-header"><p>Microfinancial Management System</p><h3>VISITOR PASS</h3></div>
      ${photoUrl ? `<div class="photo"><img src="${photoUrl}"></div>` : ''}
      <div class="name">${name}</div>
      <div class="company">${company}</div>
      <div class="qr-box"><img src="${qr}"><div class="code">${code}</div></div>
      <div class="footer">Present this pass at the front desk for verification<br>Valid for single-day entry only · ${new Date().toLocaleDateString('en-US',{year:'numeric',month:'short',day:'numeric'})}</div>
    </div>
  </body></html>`);
  w.document.close();
  // Wait for images to load before printing
  w.onload = () => setTimeout(() => w.print(), 300);
}

// ───── Flip Card Toggle ─────
function toggleQRFlip() {
  const inner = document.getElementById('qr-flip-inner');
  if (!inner) return;
  inner.classList.toggle('flipped');
  qrFlipState = qrFlipState === 'photo' ? 'qr' : 'photo';
}

// ═══════════════════════════════════════════════════════
// QR SCANNER
// ═══════════════════════════════════════════════════════

async function startScanner() {
  try {
    const video = document.getElementById('scanner-video');
    scannerStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
    video.srcObject = scannerStream;
    document.getElementById('scanner-idle').style.display = 'none';
    document.getElementById('scanner-active').style.display = 'block';
    document.getElementById('scanner-zone').classList.add('scanning');

    // Poll for QR codes using canvas
    const canvas = document.getElementById('scanner-canvas');
    const ctx = canvas.getContext('2d');

    scannerInterval = setInterval(() => {
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        // Real QR decoding via jsQR
        if (typeof jsQR === 'function') {
          const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
          const qr = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });
          if (qr && qr.data) {
            const code = extractVisitorCode(qr.data);
            stopScanner();
            processCameraQR(code);
          }
        }
      }
    }, 250);

    Swal.fire({ icon:'info', title:'Camera Active', text:'Camera scanning is active. For best results, also use the manual code entry field above.', timer:3000, showConfirmButton:false, toast:true, position:'top-end' });
  } catch(e) {
    Swal.fire({ icon:'error', title:'Camera Error', text:'Could not access camera. Please use the manual code entry instead.\n\nError: ' + e.message, confirmButtonColor:'#059669' });
  }
}

function stopScanner() {
  if (scannerStream) {
    scannerStream.getTracks().forEach(t => t.stop());
    scannerStream = null;
  }
  if (scannerInterval) {
    clearInterval(scannerInterval);
    scannerInterval = null;
  }
  document.getElementById('scanner-idle').style.display = 'block';
  document.getElementById('scanner-active').style.display = 'none';
  document.getElementById('scanner-zone').classList.remove('scanning');
}

function processCameraQR(code) {
  document.getElementById('scanner-idle').style.display = 'none';
  document.getElementById('scanner-camera-result').style.display = 'block';
  const statusEl = document.getElementById('scanner-camera-status');
  const zone = document.getElementById('scanner-zone');
  document.getElementById('scan-code-input').value = code;
  statusEl.innerHTML = '<span style="color:#6B7280">⏳ Processing <strong>' + esc(code) + '</strong>...</span>';
  processUploadedQR(code, statusEl, zone);
}

function clearCameraResult() {
  document.getElementById('scanner-camera-result').style.display = 'none';
  document.getElementById('scanner-camera-status').innerHTML = '';
  document.getElementById('scanner-idle').style.display = 'block';
  document.getElementById('scanner-zone').classList.remove('scanning', 'found', 'error');
}

// ───── Scanner Inner Tabs ─────
function switchScanTab(tab) {
  const isScan = tab === 'scan';
  document.getElementById('scantab-scan').style.display   = isScan  ? 'block' : 'none';
  document.getElementById('scantab-status').style.display = !isScan ? 'block' : 'none';
  const btnScan   = document.getElementById('scantab-btn-scan');
  const btnStatus = document.getElementById('scantab-btn-status');
  btnScan.style.borderBottomColor   = isScan  ? '#059669' : 'transparent';
  btnScan.style.color               = isScan  ? '#059669' : '#9CA3AF';
  btnStatus.style.borderBottomColor = !isScan ? '#059669' : 'transparent';
  btnStatus.style.color             = !isScan ? '#059669' : '#9CA3AF';
}

function updateLiveStatus({ emoji, label, labelColor, headerBg, headerTextColor, sub, name, photoHtml, company, visitorCode, d1l, d1v, d2l, d2v }) {
  // Show pulsing dot on status tab button
  document.getElementById('scantab-status-dot').style.display = 'inline-block';

  document.getElementById('live-status-empty').style.display = 'none';
  document.getElementById('live-status-card').style.display  = 'block';

  const header = document.getElementById('ls-header');
  header.style.background  = headerBg;
  header.style.color       = headerTextColor;
  document.getElementById('ls-icon').textContent  = emoji;
  document.getElementById('ls-label').textContent = label;
  document.getElementById('ls-label').style.color = labelColor;
  document.getElementById('ls-sub').textContent   = sub;
  document.getElementById('ls-sub').style.color   = labelColor;

  document.getElementById('ls-photo').innerHTML   = photoHtml;
  document.getElementById('ls-name').textContent  = name;
  document.getElementById('ls-company').textContent = company || '';
  document.getElementById('ls-code').textContent  = visitorCode || '';

  document.getElementById('ls-details').innerHTML = `
    <div style="background:#F9FAFB;border-radius:8px;padding:8px 10px">
      <div style="font-size:9px;color:#9CA3AF;font-weight:700;text-transform:uppercase;letter-spacing:.5px">${d1l}</div>
      <div style="font-size:12px;font-weight:700;color:#1F2937;margin-top:2px">${d1v}</div>
    </div>
    <div style="background:#F9FAFB;border-radius:8px;padding:8px 10px">
      <div style="font-size:9px;color:#9CA3AF;font-weight:700;text-transform:uppercase;letter-spacing:.5px">${d2l}</div>
      <div style="font-size:12px;font-weight:700;color:#1F2937;margin-top:2px">${d2v}</div>
    </div>`;

  document.getElementById('ls-timestamp').textContent =
    'Last scanned: ' + new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });

  // Auto-switch to Live Status tab
  switchScanTab('status');
}

// ───── QR Image Upload Scanner ─────
function scanQRFromImage(input) {
  const file = input.files[0];
  if (!file) return;

  const preview = document.getElementById('scanner-upload-preview');
  const img     = document.getElementById('scanner-upload-img');
  const status  = document.getElementById('scanner-upload-status');
  const zone    = document.getElementById('scanner-zone');

  stopScanner();
  document.getElementById('scanner-idle').style.display = 'none';

  const reader = new FileReader();
  reader.onload = function(e) {
    img.src = e.target.result;
    preview.style.display = 'block';
    status.innerHTML = '<span style="color:#6B7280">⏳ Reading QR code...</span>';
    zone.classList.add('scanning');

    const image = new Image();
    image.onload = function() {
      const canvas = document.createElement('canvas');
      canvas.width = image.naturalWidth;
      canvas.height = image.naturalHeight;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(image, 0, 0);
      const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
      const qr = (typeof jsQR === 'function')
        ? jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'attemptBoth' })
        : null;

      zone.classList.remove('scanning');
      input.value = '';

      if (qr && qr.data) {
        const code = extractVisitorCode(qr.data);
        status.innerHTML = '<span style="color:#6B7280">⏳ Processing <strong>' + esc(code) + '</strong>...</span>';
        processUploadedQR(code, status, zone);
      } else {
        zone.classList.add('error');
        setTimeout(() => zone.classList.remove('error'), 3000);
        status.innerHTML = `
          <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 14px;text-align:center;margin-top:4px">
            <div style="font-size:22px;margin-bottom:4px">✗</div>
            <div style="font-weight:700;color:#991B1B">No QR code detected</div>
            <div style="font-size:12px;color:#B91C1C;margin-top:2px">Try a clearer or higher-resolution image</div>
          </div>`;
      }
    };
    image.src = e.target.result;
  };
  reader.readAsDataURL(file);
}

async function processUploadedQR(code, statusEl, zone) {
  try {
    const res = await fetch(API + '?action=lookup_visitor&code=' + encodeURIComponent(code));
    const data = await res.json();

    if (!data.found) {
      zone.classList.add('error');
      setTimeout(() => zone.classList.remove('error'), 3000);
      statusEl.innerHTML = _buildScanCard({
        bg:'#FEF2F2', border:'#FECACA', iconBg:'#FEE2E2',
        emoji:'🔍', label:'NOT FOUND', labelColor:'#991B1B',
        sub:'No visitor matches this QR code.', subColor:'#B91C1C',
        name: esc(code), photoHtml:'❓', company:'',
        d1l:'Scanned Code', d1v: esc(code),
        d2l:'Result', d2v:'Not registered'
      });
      updateLiveStatus({
        emoji:'🔍', label:'NOT FOUND', labelColor:'#991B1B',
        headerBg:'#FEF2F2', headerTextColor:'#991B1B',
        sub:'No visitor matches this QR code.',
        name: esc(code), photoHtml:'❓', company:'', visitorCode: code,
        d1l:'Scanned Code', d1v: esc(code),
        d2l:'Result', d2v:'Not registered'
      });
      return;
    }

    const v = data.visitor;
    const activeLog = data.active_log;
    const name = ((v.first_name || '') + ' ' + (v.last_name || '')).trim();
    const photoHtml = v.photo_url
      ? `<img src="${photoSrc(v.photo_url)}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`
      : `<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`;

    // ── BLACKLISTED ──
    if (parseInt(v.is_blacklisted)) {
      zone.classList.add('error');
      setTimeout(() => zone.classList.remove('error'), 2000);
      const card = _buildScanCard({
        bg:'#FEF2F2', border:'#FECACA', iconBg:'#FEE2E2',
        emoji:'🚫', label:'ACCESS DENIED', labelColor:'#991B1B',
        sub:'This visitor is blacklisted and cannot enter.', subColor:'#B91C1C',
        name, photoHtml, company: v.company,
        d1l:'Visitor Code', d1v: v.visitor_code,
        d2l:'Status', d2v:'<span style="color:#DC2626;font-weight:700">Blacklisted</span>'
      });
      statusEl.innerHTML = card;
      updateLiveStatus({
        emoji:'🚫', label:'ACCESS DENIED', labelColor:'#991B1B',
        headerBg:'#FEF2F2', headerTextColor:'#991B1B',
        sub:'This visitor is blacklisted and cannot enter.',
        name, photoHtml, company: v.company, visitorCode: v.visitor_code,
        d1l:'Visitor Code', d1v: v.visitor_code,
        d2l:'Status', d2v:'Blacklisted'
      });
      return;
    }

    // ── CHECK OUT ──
    if (activeLog) {
      const coRes = await fetch(API + '?action=check_out', {
        method:'POST', headers:{'Content-Type':'application/json'},
        body: JSON.stringify({ log_id: activeLog.log_id })
      });
      const coResult = await coRes.json();
      if (!coResult.success) {
        statusEl.innerHTML = `<div style="color:#DC2626;padding:10px;font-size:13px">✗ Check-out failed: ${esc(coResult.error||'Unknown error')}</div>`;
        return;
      }
      const now = new Date();
      beep(800, 0.08); setTimeout(() => beep(1200, 0.12), 100);
      zone.classList.add('found'); setTimeout(() => zone.classList.remove('found'), 2000);
      const durStr = duration(activeLog.check_in_time, now.toISOString());
      statusEl.innerHTML = _buildScanCard({
        bg:'#FFFBEB', border:'#FDE68A', iconBg:'#FEF3C7',
        emoji:'👋', label:'CHECKED OUT', labelColor:'#92400E',
        sub:'Checked out at ' + now.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true}),
        subColor:'#B45309',
        name, photoHtml, company: v.company,
        d1l:'Duration', d1v: durStr,
        d2l:'Checked In At', d2v: fmtTime(activeLog.check_in_time)
      });
      updateLiveStatus({
        emoji:'👋', label:'CHECKED OUT', labelColor:'#92400E',
        headerBg:'#FFFBEB', headerTextColor:'#92400E',
        sub:'Checked out at ' + now.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true}),
        name, photoHtml, company: v.company, visitorCode: v.visitor_code,
        d1l:'Duration', d1v: durStr,
        d2l:'Checked In At', d2v: fmtTime(activeLog.check_in_time)
      });
      await loadData();
      return;
    }

    // ── CHECK IN ──
    const ciRes = await fetch(API + '?action=check_in', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ visitor_id: parseInt(v.visitor_id), purpose: 'visit' })
    });
    const ciResult = await ciRes.json();
    if (!ciResult.success) {
      statusEl.innerHTML = `<div style="color:#DC2626;padding:10px;font-size:13px">✗ Check-in failed: ${esc(ciResult.error||'Unknown error')}</div>`;
      return;
    }
    const now = new Date();
    beep(1000, 0.08); setTimeout(() => beep(1400, 0.15), 120);
    zone.classList.add('found'); setTimeout(() => zone.classList.remove('found'), 2000);
    const totalVisits = ((parseInt(v.visit_count)||0)+1);
    statusEl.innerHTML = _buildScanCard({
      bg:'#F0FDF4', border:'#A7F3D0', iconBg:'#D1FAE5',
      emoji:'✅', label:'CHECKED IN', labelColor:'#065F46',
      sub:'Welcome! Checked in at ' + now.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true}),
      subColor:'#047857',
      name, photoHtml, company: v.company,
      d1l:'Visit Code', d1v: ciResult.visit_code || '—',
      d2l:'Total Visits', d2v: totalVisits + ' visit' + (totalVisits!==1?'s':'')
    });
    updateLiveStatus({
      emoji:'✅', label:'CHECKED IN', labelColor:'#065F46',
      headerBg:'#F0FDF4', headerTextColor:'#065F46',
      sub:'Welcome! Checked in at ' + now.toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true}),
      name, photoHtml, company: v.company, visitorCode: v.visitor_code,
      d1l:'Visit Code', d1v: ciResult.visit_code || '—',
      d2l:'Total Visits', d2v: totalVisits + ' visit' + (totalVisits!==1?'s':'')
    });
    await loadData();

  } catch(err) {
    statusEl.innerHTML = `<div style="color:#DC2626;padding:10px;font-size:13px">✗ Error: ${esc(err.message)}</div>`;
  }
}

function _buildScanCard({ bg, border, iconBg, emoji, label, labelColor, sub, subColor, name, photoHtml, company, d1l, d1v, d2l, d2v }) {
  return `
    <div style="background:${bg};border:2px solid ${border};border-radius:14px;padding:14px;margin-top:8px">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
        <div style="width:46px;height:46px;border-radius:50%;overflow:hidden;background:${iconBg};border:2px solid ${border};display:flex;align-items:center;justify-content:center;flex-shrink:0">
          ${photoHtml}
        </div>
        <div style="flex:1;min-width:0">
          <div style="font-weight:800;font-size:13px;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(name)}</div>
          ${company ? `<div style="font-size:11px;color:#6B7280;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${esc(company)}</div>` : ''}
        </div>
        <div style="background:${iconBg};border-radius:8px;padding:4px 8px;text-align:center;flex-shrink:0;border:1px solid ${border}">
          <div style="font-size:16px">${emoji}</div>
          <div style="font-size:9px;font-weight:800;color:${labelColor};letter-spacing:.7px;white-space:nowrap">${label}</div>
        </div>
      </div>
      <div style="font-size:12px;font-weight:600;color:${subColor};margin-bottom:8px;padding:6px 10px;background:rgba(255,255,255,.65);border-radius:8px">${sub}</div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
        <div style="background:rgba(255,255,255,.75);border-radius:8px;padding:7px 10px">
          <div style="font-size:9px;color:#9CA3AF;font-weight:700;text-transform:uppercase;letter-spacing:.5px">${d1l}</div>
          <div style="font-size:12px;font-weight:700;color:#1F2937;margin-top:1px">${d1v}</div>
        </div>
        <div style="background:rgba(255,255,255,.75);border-radius:8px;padding:7px 10px">
          <div style="font-size:9px;color:#9CA3AF;font-weight:700;text-transform:uppercase;letter-spacing:.5px">${d2l}</div>
          <div style="font-size:12px;font-weight:700;color:#1F2937;margin-top:1px">${d2v}</div>
        </div>
      </div>
    </div>`;
}

function clearQRUpload() {
  document.getElementById('scanner-upload-preview').style.display = 'none';
  document.getElementById('scanner-upload-img').src = '';
  document.getElementById('scanner-upload-status').innerHTML = '';
  document.getElementById('qr-upload-input').value = '';
  document.getElementById('scanner-idle').style.display = 'block';
  document.getElementById('scanner-zone').classList.remove('scanning', 'found', 'error');
}

// ───── Manual Lookup ─────
async function lookupVisitor() {
  const rawCode = document.getElementById('scan-code-input').value.trim();
  if (!rawCode) return Swal.fire({ icon:'warning', title:'Enter a Code', text:'Please enter a visitor code, pre-registration code, or visit code.', confirmButtonColor:'#059669' });
  const code = extractVisitorCode(rawCode);

  const zone = document.getElementById('scanner-zone');
  const resultDiv = document.getElementById('scan-result');

  try {
    zone.classList.add('scanning');
    const res = await fetch(API + '?action=lookup_visitor&code=' + encodeURIComponent(code));
    const data = await res.json();

    if (!data.found) {
      zone.classList.remove('scanning');
      zone.classList.add('error');
      setTimeout(() => zone.classList.remove('error'), 2000);
      resultDiv.style.display = 'block';
      resultDiv.innerHTML = `<div class="scan-result-card" style="border-color:#FCA5A5">
        <div style="display:flex;align-items:center;gap:12px;color:#DC2626">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          <div>
            <div style="font-weight:700;font-size:15px">Not Found</div>
            <div style="font-size:13px">No visitor found with code: <strong>${esc(code)}</strong></div>
          </div>
        </div>
      </div>`;
      return;
    }

    zone.classList.remove('scanning');
    zone.classList.add('found');
    setTimeout(() => zone.classList.remove('found'), 2000);
    resultDiv.style.display = 'block';

    if (data.type === 'visitor') {
      const v = data.visitor;
      const name = v.first_name + ' ' + v.last_name;
      const activeLog = data.active_log;
      const hasPhoto = !!v.photo_url;
      const photoHtml = hasPhoto
        ? `<img src="${photoSrc(v.photo_url)}" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:14px">`
        : `<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`;

      resultDiv.innerHTML = `<div class="scan-result-card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
          <div style="width:56px;height:56px;border-radius:14px;background:#D1FAE5;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0">
            ${photoHtml}
          </div>
          <div style="flex:1;min-width:0">
            <div style="font-weight:700;font-size:16px;color:#1F2937">${esc(name)}</div>
            <div style="font-size:13px;color:#6B7280">${esc(v.company || 'No Company')} · ${esc(v.visitor_code)}</div>
            <div style="font-size:12px;color:#9CA3AF;margin-top:2px">${v.visit_count || 0} previous visits</div>
          </div>
        </div>
        <!-- Capture Photo -->
        <div style="margin-bottom:12px">
          <button class="btn btn-outline btn-sm" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px" onclick="capturePhotoFromScan(${v.visitor_id})">
            📸 ${hasPhoto ? 'Retake Photo' : 'Capture Photo'}
          </button>
        </div>
        ${activeLog ?
          `<div style="background:#FEF3C7;padding:12px 16px;border-radius:10px;margin-bottom:12px;font-size:13px;color:#92400E">
            <strong>Currently Checked In</strong> since ${fmtTime(activeLog.check_in_time)} · ${esc(activeLog.host_department || '')}
          </div>
          <button class="btn btn-outline btn-sm" style="color:#DC2626;border-color:#DC2626;width:100%" onclick="checkOutByLogId(${activeLog.log_id})">Check Out Now</button>` :
          `<button class="btn btn-primary" style="width:100%" onclick="openCheckInModal(${v.visitor_id})">Check In This Visitor</button>`
        }
      </div>`;

    } else if (data.type === 'prereg') {
      const p = data.prereg;
      resultDiv.innerHTML = `<div class="scan-result-card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
          <div style="width:56px;height:56px;border-radius:14px;background:#DBEAFE;display:flex;align-items:center;justify-content:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div>
            <div style="font-weight:700;font-size:16px;color:#1F2937">${esc(p.visitor_name)}</div>
            <div style="font-size:13px;color:#6B7280">${esc(p.visitor_company || 'No Company')} · ${esc(p.prereg_code)}</div>
            <div style="font-size:12px;color:#9CA3AF;margin-top:2px">Pre-registered · Expected ${fmtDate(p.expected_date)}${p.expected_time ? ' at ' + p.expected_time : ''}</div>
          </div>
        </div>
        <div style="background:#DBEAFE;padding:10px 14px;border-radius:10px;margin-bottom:12px;font-size:13px;color:#1E40AF">
          <strong>Purpose:</strong> ${labelCase(p.purpose)} · <strong>Host:</strong> ${esc(p.host_name || '—')}
        </div>
        <div style="font-size:12px;color:#6B7280;margin-bottom:12px">This visitor is pre-registered. Register them as a visitor first, then check them in.</div>
        <button class="btn btn-primary" style="width:100%" onclick="registerFromPrereg(${JSON.stringify(p).replace(/"/g,'&quot;')})">Register & Check In</button>
      </div>`;

    } else if (data.type === 'visit_log') {
      const l = data.log;
      resultDiv.innerHTML = `<div class="scan-result-card">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px">
          <div style="width:56px;height:56px;border-radius:14px;background:#D1FAE5;display:flex;align-items:center;justify-content:center">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div>
            <div style="font-weight:700;font-size:16px;color:#1F2937">${esc(l.visitor_name)}</div>
            <div style="font-size:13px;color:#6B7280">${esc(l.company || '')} · ${esc(l.visit_code)}</div>
          </div>
        </div>
        <table style="width:100%;font-size:13px;border-collapse:collapse">
          <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Status</td><td style="padding:8px 0">${statusBadge(l.status)}</td></tr>
          <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Check-In</td><td style="padding:8px 0">${fmtDateTime(l.check_in_time)}</td></tr>
          <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Check-Out</td><td style="padding:8px 0">${l.check_out_time ? fmtDateTime(l.check_out_time) : '—'}</td></tr>
          <tr><td style="padding:8px 0;font-weight:600;color:#6B7280">Duration</td><td style="padding:8px 0">${duration(l.check_in_time, l.check_out_time)}</td></tr>
        </table>
        ${l.status === 'checked_in' ? `<button class="btn btn-outline btn-sm" style="color:#DC2626;border-color:#DC2626;width:100%;margin-top:12px" onclick="checkOutByLogId(${l.log_id})">Check Out Now</button>` : ''}
      </div>`;
    }

  } catch(e) {
    console.error(e);
    zone.classList.remove('scanning');
    Swal.fire({ icon:'error', title:'Lookup Error', text:'Failed to lookup visitor: ' + e.message, confirmButtonColor:'#059669' });
  }
}

// ═══════════════════════════════════════════════════════
// CAPTURE PHOTO FROM SCAN/LOOKUP
// ═══════════════════════════════════════════════════════

async function capturePhotoFromScan(visitorId) {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
    const { value: canvas } = await Swal.fire({
      title: '📸 Capture Visitor Photo',
      html: `<div style="text-align:center">
        <video id="scan-cam-video" autoplay playsinline style="width:100%;max-width:400px;border-radius:12px;border:3px solid #059669"></video>
        <div style="font-size:12px;color:#6B7280;margin-top:8px">Position the visitor's face in the frame</div>
      </div>`,
      confirmButtonText: '📸 Capture',
      confirmButtonColor: '#059669',
      showCancelButton: true,
      cancelButtonText: 'Cancel',
      width: 500,
      didOpen: () => {
        document.getElementById('scan-cam-video').srcObject = stream;
      },
      preConfirm: () => {
        const video = document.getElementById('scan-cam-video');
        const c = document.createElement('canvas');
        c.width = video.videoWidth;
        c.height = video.videoHeight;
        c.getContext('2d').drawImage(video, 0, 0);
        return c;
      }
    });
    stream.getTracks().forEach(t => t.stop());
    if (!canvas) return;

    const photoBase64 = canvas.toDataURL('image/jpeg', 0.8);

    // Upload via API
    const res = await fetch(API + '?action=upload_photo', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ visitor_id: visitorId, photo_base64: photoBase64 })
    });
    const result = await res.json();
    if (result.success) {
      Swal.fire({ icon: 'success', title: 'Photo Saved!', text: 'Visitor photo has been captured and saved.', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
      await loadData();
      // Re-lookup to refresh scan result with new photo
      lookupVisitor();
      // Also refresh QR preview if it's visible (so photo shows on flip card)
      const qrPreview = document.getElementById('qr-preview');
      if (qrPreview && qrPreview.style.display !== 'none') {
        const v = visitors.find(x => x.visitor_id == visitorId);
        if (v) {
          const photoEl = document.getElementById('qr-visitor-photo');
          const placeholderEl = document.getElementById('qr-photo-placeholder');
          if (photoEl && v.photo_url) {
            photoEl.src = photoSrc(v.photo_url);
            photoEl.style.display = 'block';
            if (placeholderEl) placeholderEl.style.display = 'none';
            generateVisitorQR._lastPhoto = photoSrc(v.photo_url);
          }
        }
      }
    } else {
      Swal.fire({ icon: 'error', title: 'Upload Failed', text: result.error || 'Unknown error', confirmButtonColor: '#059669' });
    }
  } catch(e) {
    Swal.fire({ icon: 'error', title: 'Camera Error', text: 'Could not access camera: ' + e.message, confirmButtonColor: '#059669' });
  }
}

// ───── Capture Photo from QR Pass section ─────
function capturePhotoForPass() {
  const visitorId = generateVisitorQR._lastVisitorId;
  if (!visitorId) {
    return Swal.fire({ icon: 'warning', title: 'No Visitor Selected', text: 'Generate a QR pass first before capturing a photo.', confirmButtonColor: '#059669' });
  }
  capturePhotoFromScan(visitorId);
}

// ═══════════════════════════════════════════════════════
// FORM SUBMISSIONS
// ═══════════════════════════════════════════════════════

// Pre-Register
async function submitPreRegister() {
  const name = document.getElementById('prereg-name').value.trim();
  const date = document.getElementById('prereg-date').value;
  const purpose = document.getElementById('prereg-purpose').value;
  const host = document.getElementById('prereg-host').value;

  if (!name || !date || !purpose || !host) return Swal.fire({ icon:'warning', title:'Required Fields', text:'Name, date, purpose, and host are required.', confirmButtonColor:'#059669' });

  // Collect visitor type
  const visitorType = (document.querySelector('input[name="prereg-visitor-type"]:checked') || {}).value || 'regular';

  const data = {
    visitor_name: name,
    visitor_email: document.getElementById('prereg-email').value.trim() || null,
    visitor_phone: document.getElementById('prereg-phone').value.trim() || null,
    visitor_company: document.getElementById('prereg-company').value.trim() || null,
    id_type: document.getElementById('prereg-id-type').value || null,
    id_number: document.getElementById('prereg-id-number').value.trim() || null,
    expected_date: date,
    expected_time: document.getElementById('prereg-time').value || null,
    purpose: purpose,
    host_user_id: parseInt(host),
    visitor_type: visitorType,
    security_level: (visitorType === 'vip' || visitorType === 'government_official') ? (document.getElementById('prereg-security-level')?.value || 'standard') : 'standard',
    parking_required: document.getElementById('prereg-parking')?.checked ? 1 : 0,
    escort_required: document.getElementById('prereg-escort')?.checked ? 1 : 0
  };

  try {
    const res = await fetch(API + '?action=preregister', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      closeModal('modal-prereg');
      ['prereg-name','prereg-company','prereg-email','prereg-phone','prereg-date','prereg-time','prereg-id-number'].forEach(id => document.getElementById(id).value = '');
      document.getElementById('prereg-id-type').value = '';
      document.getElementById('prereg-host').value = '';
      // Reset VIP fields
      const regRadio = document.querySelector('input[name="prereg-visitor-type"][value="regular"]');
      if (regRadio) { regRadio.checked = true; setupVtypeRadios('prereg'); }
      const vipBox = document.getElementById('prereg-vip-fields');
      if (vipBox) vipBox.style.display = 'none';
      if (document.getElementById('prereg-parking')) document.getElementById('prereg-parking').checked = false;
      if (document.getElementById('prereg-escort')) document.getElementById('prereg-escort').checked = false;
      if (document.getElementById('prereg-security-level')) document.getElementById('prereg-security-level').value = 'elevated';
      Swal.fire({ icon:'success', title:'Pre-Registered!', html:`Visitor <strong>${esc(name)}</strong> has been pre-registered.<br>Code: <strong>${result.prereg_code}</strong>`, confirmButtonColor:'#059669' });
      await loadData();
    } else {
      Swal.fire({ icon:'error', title:'Failed', text: result.error || 'Unknown error', confirmButtonColor:'#059669' });
    }
  } catch(e) {
    Swal.fire({ icon:'error', title:'Error', text: e.message, confirmButtonColor:'#059669' });
  }
}

// Check-In
function openCheckInModal(visitorId) {
  const v = visitors.find(x => x.visitor_id == visitorId);
  if (!v) return;
  document.getElementById('checkin-visitor-id').value = v.visitor_id;
  document.getElementById('checkin-visitor-name').textContent = v.first_name + ' ' + v.last_name;
  document.getElementById('checkin-visitor-company').textContent = v.company || 'No company';
  document.getElementById('checkin-badge').value = '';
  document.getElementById('checkin-host-name').value = '';
  document.getElementById('checkin-details').value = '';

  // VIP type handling
  const vtype = v.visitor_type || 'regular';
  document.getElementById('checkin-visitor-type').value = vtype;
  const vipBox = document.getElementById('checkin-vip-fields');
  if (vipBox) {
    if (vtype === 'vip' || vtype === 'government_official') {
      vipBox.style.display = 'block';
      // Pre-fill elevated defaults for VIP
      if (document.getElementById('checkin-security-level')) document.getElementById('checkin-security-level').value = vtype === 'government_official' ? 'executive' : 'elevated';
      if (document.getElementById('checkin-access-level')) document.getElementById('checkin-access-level').value = vtype === 'government_official' ? 'executive_floor' : 'general';
      if (document.getElementById('checkin-escort')) document.getElementById('checkin-escort').checked = vtype === 'government_official';
      if (document.getElementById('checkin-id-verified')) document.getElementById('checkin-id-verified').checked = false;
    } else {
      vipBox.style.display = 'none';
    }
  }

  // Show VIP badge next to name if applicable
  const nameEl = document.getElementById('checkin-visitor-name');
  if (nameEl && vtype !== 'regular') {
    nameEl.innerHTML = esc(v.first_name + ' ' + v.last_name) + ' ' + vtypeBadge(vtype);
  }

  openModal('modal-checkin');
}

async function submitCheckIn() {
  const visitorId = document.getElementById('checkin-visitor-id').value;
  const purpose = document.getElementById('checkin-purpose').value;
  if (!visitorId || !purpose) return Swal.fire({ icon:'warning', title:'Required', text:'Purpose is required.', confirmButtonColor:'#059669' });

  // Build purpose details with breakdown
  let purposeDetails = document.getElementById('checkin-details').value.trim() || '';
  if (purpose === 'partnership') {
    const org = document.getElementById('checkin-partner-org')?.value?.trim() || '';
    const ptype = document.getElementById('checkin-partner-type')?.value || '';
    if (org || ptype) purposeDetails = `[Partnership] Org: ${org} | Type: ${ptype}${purposeDetails ? ' | Notes: ' + purposeDetails : ''}`;
  } else if (purpose === 'educational') {
    const inst = document.getElementById('checkin-institution')?.value?.trim() || '';
    const etype = document.getElementById('checkin-edu-type')?.value || '';
    if (inst || etype) purposeDetails = `[Educational] Institution: ${inst} | Type: ${etype}${purposeDetails ? ' | Notes: ' + purposeDetails : ''}`;
  }

  // Collect VIP fields if applicable
  const checkinVtype = document.getElementById('checkin-visitor-type')?.value || 'regular';
  const data = {
    visitor_id: parseInt(visitorId),
    purpose: purpose,
    badge_number: document.getElementById('checkin-badge').value.trim() || null,
    host_name: document.getElementById('checkin-host-name').value.trim() || null,
    host_department: document.getElementById('checkin-host-dept').value,
    purpose_details: purposeDetails || null,
    security_level: (checkinVtype === 'vip' || checkinVtype === 'government_official') ? (document.getElementById('checkin-security-level')?.value || 'standard') : 'standard',
    access_level: (checkinVtype === 'vip' || checkinVtype === 'government_official') ? (document.getElementById('checkin-access-level')?.value || 'lobby_only') : 'lobby_only',
    escort_required: document.getElementById('checkin-escort')?.checked ? 1 : 0,
    id_verified: document.getElementById('checkin-id-verified')?.checked ? 1 : 0
  };

  try {
    const res = await fetch(API + '?action=check_in', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      closeModal('modal-checkin');
      Swal.fire({ icon:'success', title:'Checked In!', html:`Visit code: <strong>${result.visit_code}</strong>`, timer:2000, showConfirmButton:false, toast:true, position:'top-end' });
      await loadData();
    } else {
      Swal.fire({ icon:'error', title:'Check-In Failed', text: result.error || 'Unknown error', confirmButtonColor:'#059669' });
    }
  } catch(e) {
    Swal.fire({ icon:'error', title:'Error', text: e.message, confirmButtonColor:'#059669' });
  }
}

// Check-Out
async function checkOutByLogId(logId) {
  const confirm = await Swal.fire({
    title: 'Check Out Visitor?', text: 'This will mark the visitor as checked out.',
    icon: 'question', showCancelButton: true,
    confirmButtonText: 'Yes, Check Out', confirmButtonColor: '#059669', cancelButtonColor: '#6B7280'
  });
  if (!confirm.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=check_out', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ log_id: logId })
    });
    const result = await res.json();
    if (result.success) {
      Swal.fire({ icon:'success', title:'Checked Out!', timer:2000, showConfirmButton:false, toast:true, position:'top-end' });
      await loadData();
    }
  } catch(e) {
    Swal.fire({ icon:'error', title:'Error', text: e.message, confirmButtonColor:'#059669' });
  }
}

async function checkOutVisitor(visitorId) {
  const activeLog = logs.find(l => l.visitor_id == visitorId && l.status === 'checked_in');
  if (activeLog) {
    await checkOutByLogId(activeLog.log_id);
  }
}

// Register from Pre-Registration
async function registerFromPrereg(prereg) {
  // Parse prereg name into first/last
  const parts = (prereg.visitor_name || '').trim().split(/\s+/);
  const firstName = parts[0] || '';
  const lastName = parts.slice(1).join(' ') || firstName;

  const data = {
    first_name: firstName,
    last_name: lastName,
    email: prereg.visitor_email || null,
    phone: prereg.visitor_phone || null,
    company: prereg.visitor_company || null,
    visitor_type: prereg.visitor_type || 'regular'
  };

  try {
    const res = await fetch(API + '?action=register_visitor', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data)
    });
    const result = await res.json();
    if (result.success) {
      document.getElementById('scan-result').style.display = 'none';
      Swal.fire({ icon:'success', title:'Registered!', html:`<strong>${esc(prereg.visitor_name)}</strong> registered as <strong>${result.visitor_code}</strong>.<br>You can now check them in from the Registration tab.`, confirmButtonColor:'#059669' });
      await loadData();
    }
  } catch(e) {
    Swal.fire({ icon:'error', title:'Error', text: e.message, confirmButtonColor:'#059669' });
  }
}

// ═══════════════════════════════════════════════════════
// VIEW DETAILS
// ═══════════════════════════════════════════════════════

// Consolidated visit history — click visitor name in logs
function viewVisitorHistory(visitorId) {
  const v = visitors.find(x => x.visitor_id == visitorId);
  if (v) return viewVisitorDetail(v.visitor_id);
  // Fallback: show history from logs only
  const visitorLogs = logs.filter(l => l.visitor_id == visitorId);
  if (!visitorLogs.length) return;
  const name = visitorLogs[0].visitor_name || 'Visitor';
  document.getElementById('detail-title').textContent = name + ' — Visit History';
  document.getElementById('detail-body').innerHTML = `
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
      <div style="width:48px;height:48px;border-radius:12px;background:#D1FAE5;display:flex;align-items:center;justify-content:center">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
      </div>
      <div>
        <div style="font-weight:700;font-size:15px;color:#1F2937">${esc(name)}</div>
        <div style="font-size:12px;color:#6B7280">${esc(visitorLogs[0].company || 'No Company')} · ${visitorLogs.length} visit(s)</div>
      </div>
    </div>
    <div style="max-height:400px;overflow-y:auto">
      ${visitorLogs.map(l => `
        <div style="border:1px solid #E5E7EB;border-radius:10px;padding:12px 16px;margin-bottom:8px;font-size:12px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
            <span style="font-weight:700;color:#1F2937">${esc(l.visit_code)}</span>
            ${statusBadge(l.status)}
          </div>
          <div style="color:#6B7280;margin-bottom:4px">
            <span style="font-weight:600">${labelCase(l.purpose)}</span>${l.host_department ? ' · ' + esc(l.host_department) : ''}${l.host_name ? ' · Host: ' + esc(l.host_name) : ''}
          </div>
          <div style="display:flex;gap:16px;margin-top:6px">
            <div style="flex:1;background:#F0FDF4;border-radius:6px;padding:6px 10px">
              <div style="font-size:10px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px">Check In</div>
              <div style="font-weight:600;color:#1F2937;margin-top:2px">${fmtDateTime(l.check_in_time)}</div>
            </div>
            <div style="flex:1;background:${l.check_out_time ? '#FEF2F2' : '#F9FAFB'};border-radius:6px;padding:6px 10px">
              <div style="font-size:10px;font-weight:700;color:${l.check_out_time ? '#DC2626' : '#9CA3AF'};text-transform:uppercase;letter-spacing:0.5px">Check Out</div>
              <div style="font-weight:600;color:#1F2937;margin-top:2px">${l.check_out_time ? fmtDateTime(l.check_out_time) : '<span style="color:#9CA3AF;font-style:italic">Still inside</span>'}</div>
            </div>
          </div>
          <div style="margin-top:6px;text-align:right;font-size:11px;color:#6B7280">
            Duration: <span style="font-weight:600;color:#1F2937">${duration(l.check_in_time, l.check_out_time)}</span>
          </div>
        </div>
      `).join('')}
    </div>`;
  openModal('modal-visitor-detail');
}

function viewIDPhoto(visitorId) {
  const v = visitors.find(x => x.visitor_id == visitorId);
  if (!v || !v.photo_url) return;
  const name = esc(v.first_name + ' ' + v.last_name);
  const idInfo = v.id_type ? idTypeLabel(v.id_type) + (v.id_number ? ' · ' + esc(v.id_number) : '') : (v.id_number ? esc(v.id_number) : 'Photo ID');
  Swal.fire({
    title: name,
    html: `<div style="text-align:center">
      <div style="font-size:12px;color:#6B7280;margin-bottom:12px">${idInfo}</div>
      <img src="${photoSrc(v.photo_url)}" style="max-width:100%;max-height:400px;border-radius:10px;border:1px solid #E5E7EB" alt="ID Photo">
    </div>`,
    showConfirmButton: true,
    confirmButtonColor: '#059669',
    confirmButtonText: 'Close',
    width: 500
  });
}

function viewVisitorDetail(visitorId) {
  const v = visitors.find(x => x.visitor_id == visitorId);
  if (!v) return;
  const name = v.first_name + ' ' + v.last_name;
  const visitorLogs = logs.filter(l => l.visitor_id == visitorId);

  const photoHtml = v.photo_url
    ? `<img src="${photoSrc(v.photo_url)}" alt="Photo" style="width:100px;height:100px;border-radius:50%;object-fit:cover;border:3px solid #059669">`
    : `<div style="width:100px;height:100px;border-radius:50%;background:#F3F4F6;border:3px dashed #D1D5DB;display:flex;align-items:center;justify-content:center">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>`;

  document.getElementById('detail-title').textContent = name;
  document.getElementById('detail-body').innerHTML = `
    <div style="text-align:center;margin-bottom:20px">
      <div style="margin-bottom:12px">${photoHtml}</div>
      <div style="display:flex;gap:6px;justify-content:center;margin-bottom:8px">
        <button class="btn btn-outline btn-sm" onclick="openUploadPhotoModal(${v.visitor_id})" style="font-size:11px">📷 ${v.photo_url ? 'Change' : 'Add'} Picture</button>
        <button class="btn btn-sm" style="background:#059669;color:#fff;border:none;font-size:11px" onclick="showVisitorTag(${v.visitor_id})">🏷️ View Visitor Tag</button>
      </div>
      <img src="${qrUrl(v.visitor_code, 120)}" style="width:120px;height:120px;border-radius:8px;margin-bottom:8px">
      <div style="font-weight:700;color:#059669;font-size:16px;letter-spacing:1px">${esc(v.visitor_code)}</div>
    </div>
    <table style="width:100%;font-size:13px;border-collapse:collapse">
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280;width:130px">Name</td><td style="padding:8px 0">${esc(name)}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Company</td><td style="padding:8px 0">${esc(v.company) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Email</td><td style="padding:8px 0">${esc(v.email) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Phone</td><td style="padding:8px 0">${esc(v.phone) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">ID Type</td><td style="padding:8px 0">${v.id_type ? idTypeLabel(v.id_type) : '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">ID Number</td><td style="padding:8px 0">${esc(v.id_number) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Total Visits</td><td style="padding:8px 0"><span class="badge badge-blue">${v.visit_count || 0}</span></td></tr>
      <tr><td style="padding:8px 0;font-weight:600;color:#6B7280">Registered</td><td style="padding:8px 0">${fmtDate(v.created_at)}</td></tr>
    </table>
    ${visitorLogs.length ? `
      <div style="margin-top:16px;font-weight:700;font-size:13px;color:#1F2937;margin-bottom:8px">Visit History (${visitorLogs.length})</div>
      <div style="max-height:280px;overflow-y:auto">
        ${visitorLogs.map(l => `
          <div style="border:1px solid #E5E7EB;border-radius:10px;padding:12px 16px;margin-bottom:8px;font-size:12px">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
              <span style="font-weight:700;color:#1F2937">${esc(l.visit_code)}</span>
              ${statusBadge(l.status)}
            </div>
            <div style="color:#6B7280;margin-bottom:4px">
              <span style="font-weight:600">${labelCase(l.purpose)}</span>${l.host_department ? ' · ' + esc(l.host_department) : ''}${l.host_name ? ' · Host: ' + esc(l.host_name) : ''}
            </div>
            <div style="display:flex;gap:16px;margin-top:6px">
              <div style="flex:1;background:#F0FDF4;border-radius:6px;padding:6px 10px">
                <div style="font-size:10px;font-weight:700;color:#059669;text-transform:uppercase;letter-spacing:0.5px">Check In</div>
                <div style="font-weight:600;color:#1F2937;margin-top:2px">${fmtDateTime(l.check_in_time)}</div>
              </div>
              <div style="flex:1;background:${l.check_out_time ? '#FEF2F2' : '#F9FAFB'};border-radius:6px;padding:6px 10px">
                <div style="font-size:10px;font-weight:700;color:${l.check_out_time ? '#DC2626' : '#9CA3AF'};text-transform:uppercase;letter-spacing:0.5px">Check Out</div>
                <div style="font-weight:600;color:#1F2937;margin-top:2px">${l.check_out_time ? fmtDateTime(l.check_out_time) : '<span style="color:#9CA3AF;font-style:italic">Still inside</span>'}</div>
              </div>
            </div>
            <div style="margin-top:6px;text-align:right;font-size:11px;color:#6B7280">
              Duration: <span style="font-weight:600;color:#1F2937">${duration(l.check_in_time, l.check_out_time)}</span>
            </div>
          </div>
        `).join('')}
      </div>
    ` : ''}`;
  openModal('modal-visitor-detail');
}

function viewPreregDetail(code) {
  const p = preregs.find(x => x.prereg_code === code);
  if (!p) return;
  document.getElementById('detail-title').textContent = 'Pre-Registration: ' + p.prereg_code;
  document.getElementById('detail-body').innerHTML = `
    <table style="width:100%;font-size:13px;border-collapse:collapse">
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280;width:130px">Code</td><td style="padding:8px 0;font-weight:600;color:#059669">${esc(p.prereg_code)}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Visitor</td><td style="padding:8px 0;font-weight:600">${esc(p.visitor_name)}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Company</td><td style="padding:8px 0">${esc(p.visitor_company) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Email</td><td style="padding:8px 0">${esc(p.visitor_email) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Phone</td><td style="padding:8px 0">${esc(p.visitor_phone) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">ID Type</td><td style="padding:8px 0">${p.id_type ? idTypeLabel(p.id_type) : '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">ID Number</td><td style="padding:8px 0">${esc(p.id_number) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Purpose</td><td style="padding:8px 0">${labelCase(p.purpose)}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Expected Date</td><td style="padding:8px 0">${fmtDate(p.expected_date)}${p.expected_time ? ' at ' + p.expected_time : ''}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Host</td><td style="padding:8px 0">${esc(p.host_name) || '—'}</td></tr>
      <tr><td style="padding:8px 0;font-weight:600;color:#6B7280">Status</td><td style="padding:8px 0">${statusBadge(p.status)}</td></tr>
    </table>`;
  openModal('modal-visitor-detail');
}

function viewLogDetail(logId) {
  const l = logs.find(x => x.log_id == logId);
  if (!l) return;
  document.getElementById('detail-title').textContent = 'Visit: ' + l.visit_code;
  document.getElementById('detail-body').innerHTML = `
    <table style="width:100%;font-size:13px;border-collapse:collapse">
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280;width:130px">Visit Code</td><td style="padding:8px 0;font-weight:600;color:#059669">${esc(l.visit_code)}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Visitor</td><td style="padding:8px 0;font-weight:600">${esc(l.visitor_name)}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Company</td><td style="padding:8px 0">${esc(l.company) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Purpose</td><td style="padding:8px 0">${labelCase(l.purpose)}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Host</td><td style="padding:8px 0">${esc(l.host_name || '—')}${l.host_department ? '<br><span style="color:#9CA3AF">' + esc(l.host_department) + '</span>' : ''}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Badge</td><td style="padding:8px 0">${esc(l.badge_number) || '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Check-In</td><td style="padding:8px 0">${fmtDateTime(l.check_in_time)}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Check-Out</td><td style="padding:8px 0">${l.check_out_time ? fmtDateTime(l.check_out_time) : '—'}</td></tr>
      <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">Duration</td><td style="padding:8px 0">${duration(l.check_in_time, l.check_out_time)}</td></tr>
      <tr><td style="padding:8px 0;font-weight:600;color:#6B7280">Status</td><td style="padding:8px 0">${statusBadge(l.status)}</td></tr>
    </table>`;
  openModal('modal-visitor-detail');
}

// ═══════════════════════════════════════════════════════
// PHOTO HANDLING & VISITOR TAG
// ═══════════════════════════════════════════════════════

let uploadPhotoBase64 = null;

// Preview photo in upload modal
function previewUploadPhoto(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  if (file.size > 2 * 1024 * 1024) {
    Swal.fire({ icon: 'warning', title: 'File Too Large', text: 'Max file size is 2MB.', confirmButtonColor: '#059669' });
    return;
  }
  const reader = new FileReader();
  reader.onload = (e) => {
    uploadPhotoBase64 = e.target.result;
    document.getElementById('upload-photo-preview').innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
  };
  reader.readAsDataURL(file);
}

// Capture photo from camera for upload modal
async function captureUploadPhoto() {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
    const { value: canvas } = await Swal.fire({
      title: 'Take Photo',
      html: `<div style="text-align:center"><video id="cam-video2" autoplay playsinline style="width:100%;max-width:400px;border-radius:12px;border:3px solid #059669"></video></div>`,
      confirmButtonText: '📸 Capture',
      confirmButtonColor: '#059669',
      showCancelButton: true,
      didOpen: () => {
        const video = document.getElementById('cam-video2');
        video.srcObject = stream;
      },
      preConfirm: () => {
        const video = document.getElementById('cam-video2');
        const c = document.createElement('canvas');
        c.width = video.videoWidth;
        c.height = video.videoHeight;
        c.getContext('2d').drawImage(video, 0, 0);
        return c;
      }
    });
    stream.getTracks().forEach(t => t.stop());
    if (canvas) {
      uploadPhotoBase64 = canvas.toDataURL('image/jpeg', 0.8);
      document.getElementById('upload-photo-preview').innerHTML = `<img src="${uploadPhotoBase64}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
    }
  } catch(e) {
    Swal.fire({ icon: 'error', title: 'Camera Error', text: 'Could not access camera: ' + e.message, confirmButtonColor: '#059669' });
  }
}

// Open upload photo modal
function openUploadPhotoModal(visitorId) {
  const v = visitors.find(x => x.visitor_id == visitorId);
  if (!v) return;
  document.getElementById('upload-photo-visitor-id').value = visitorId;
  uploadPhotoBase64 = null;
  if (v.photo_url) {
    document.getElementById('upload-photo-preview').innerHTML = `<img src="${photoSrc(v.photo_url)}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
  } else {
    document.getElementById('upload-photo-preview').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';
  }
  document.getElementById('upload-photo-file').value = '';
  closeModal('modal-visitor-detail');
  openModal('modal-upload-photo');
}

// Submit photo upload
async function submitUploadPhoto() {
  const visitorId = document.getElementById('upload-photo-visitor-id').value;
  if (!uploadPhotoBase64) {
    return Swal.fire({ icon: 'warning', title: 'No Photo', text: 'Please select or capture a photo first.', confirmButtonColor: '#059669' });
  }

  try {
    const res = await fetch(API + '?action=upload_photo', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ visitor_id: parseInt(visitorId), photo_base64: uploadPhotoBase64 })
    });
    const result = await res.json();
    if (result.success) {
      closeModal('modal-upload-photo');
      Swal.fire({ icon: 'success', title: 'Photo Saved!', text: 'Visitor photo has been updated.', timer: 2000, showConfirmButton: false, toast: true, position: 'top-end' });
      await loadData();
      // Re-open visitor detail
      viewVisitorDetail(parseInt(visitorId));
    } else {
      Swal.fire({ icon: 'error', title: 'Upload Failed', text: result.error || 'Unknown error', confirmButtonColor: '#059669' });
    }
  } catch(e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#059669' });
  }
}

// Show Visitor Tag modal
function showVisitorTag(visitorId) {
  const v = visitors.find(x => x.visitor_id == visitorId);
  if (!v) return;
  const name = v.first_name + ' ' + v.last_name;

  // Set photo
  if (v.photo_url) {
    document.getElementById('tag-photo-img').src = photoSrc(v.photo_url);
    document.getElementById('tag-photo-img').style.display = 'block';
    document.getElementById('tag-photo-placeholder').style.display = 'none';
  } else {
    document.getElementById('tag-photo-img').style.display = 'none';
    document.getElementById('tag-photo-placeholder').style.display = 'block';
  }

  // Set name and company
  document.getElementById('tag-visitor-name').textContent = name;
  document.getElementById('tag-visitor-company').textContent = v.company || 'No Company';

  // Set QR
  document.getElementById('tag-qr-img').src = qrUrl(v.visitor_code, 160);
  document.getElementById('tag-visitor-code').textContent = v.visitor_code;

  // ID badge
  const idBadge = document.getElementById('tag-id-badge');
  if (v.id_type) {
    idBadge.textContent = '🪪 ' + idTypeLabel(v.id_type) + (v.id_number ? ': ' + v.id_number : '');
    idBadge.style.display = 'inline-block';
  } else {
    idBadge.style.display = 'none';
  }

  // Date badge
  document.getElementById('tag-date-badge').textContent = '📅 ' + new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

  closeModal('modal-visitor-detail');
  openModal('modal-visitor-tag');
}

// Print Visitor Tag
function printVisitorTag() {
  const content = document.getElementById('visitor-tag-content');
  if (!content) return;
  const w = window.open('', '_blank', 'width=450,height=700');
  w.document.write(`<html><head><title>Visitor Tag</title>
    <style>
      body { display:flex; justify-content:center; align-items:center; min-height:100vh; font-family:Arial,'Segoe UI',sans-serif; margin:0; padding:20px; box-sizing:border-box; }
      * { box-sizing: border-box; }
      img { max-width:100%; }
    </style>
  </head><body>`);
  w.document.write(content.outerHTML);
  w.document.write('</body></html>');
  w.document.close();
  setTimeout(() => { w.print(); }, 500);
}

// Download Visitor Tag as image
async function downloadVisitorTag() {
  try {
    const content = document.getElementById('visitor-tag-content');
    const name = document.getElementById('tag-visitor-name').textContent;
    const code = document.getElementById('tag-visitor-code').textContent;

    // Use html2canvas if available, otherwise use print
    if (typeof html2canvas !== 'undefined') {
      const canvas = await html2canvas(content, { scale: 2, useCORS: true, backgroundColor: '#ffffff' });
      const link = document.createElement('a');
      link.download = `VisitorTag_${code}_${name.replace(/\s+/g, '_')}.png`;
      link.href = canvas.toDataURL('image/png');
      link.click();
    } else {
      // Fallback: generate a simple visitor tag using canvas
      const canvas = document.createElement('canvas');
      canvas.width = 400;
      canvas.height = 600;
      const ctx = canvas.getContext('2d');
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, 400, 600);
      // Header
      ctx.fillStyle = '#059669';
      ctx.fillRect(0, 0, 400, 80);
      ctx.fillStyle = '#ffffff';
      ctx.font = 'bold 10px Arial';
      ctx.textAlign = 'center';
      ctx.fillText('MICROFINANCIAL MANAGEMENT SYSTEM', 200, 30);
      ctx.font = 'bold 24px Arial';
      ctx.fillText('VISITOR PASS', 200, 60);
      // Name
      ctx.fillStyle = '#1F2937';
      ctx.font = 'bold 20px Arial';
      ctx.fillText(name, 200, 230);
      // Company
      const company = document.getElementById('tag-visitor-company').textContent;
      ctx.fillStyle = '#6B7280';
      ctx.font = '14px Arial';
      ctx.fillText(company, 200, 255);
      // Code
      ctx.fillStyle = '#059669';
      ctx.font = 'bold 18px Arial';
      ctx.fillText(code, 200, 480);
      // QR code image
      const qrImg = document.getElementById('tag-qr-img');
      if (qrImg.complete) {
        ctx.drawImage(qrImg, 120, 290, 160, 160);
      }
      // Date
      ctx.fillStyle = '#6B7280';
      ctx.font = '12px Arial';
      ctx.fillText('Date: ' + new Date().toLocaleDateString(), 200, 510);
      ctx.fillText('Present this pass at the front desk', 200, 560);

      const link = document.createElement('a');
      link.download = `VisitorTag_${code}_${name.replace(/\s+/g, '_')}.png`;
      link.href = canvas.toDataURL('image/png');
      link.click();
    }

    Swal.fire({ icon: 'success', title: 'Downloaded!', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
  } catch(e) {
    console.error(e);
    Swal.fire({ icon: 'error', title: 'Download Failed', text: e.message, confirmButtonColor: '#059669' });
  }
}

// ═══════════════════════════════════════════════════════
// ANALYTICS
// ═══════════════════════════════════════════════════════

function renderAnalytics() {
  // Analytics stat cards
  document.getElementById('stat-month').textContent = stats.total_visits_month ?? 0;
  const avgMin = parseInt(stats.avg_duration_min) || 0;
  document.getElementById('stat-avg-dur').textContent = avgMin > 60 ? Math.floor(avgMin/60) + 'h ' + (avgMin%60) + 'm' : avgMin + 'm';
  document.getElementById('stat-top-org').textContent = stats.top_company || 'N/A';
  document.getElementById('stat-upcoming').textContent = stats.pending_preregs ?? 0;

  // Top visitors from visitor data
  const topList = document.getElementById('top-visitors-list');
  const sorted = [...visitors].sort((a,b) => (b.visit_count||0) - (a.visit_count||0)).slice(0, 5);
  if (sorted.length === 0) {
    topList.innerHTML = '<div class="empty-state" style="padding:20px"><div style="font-size:36px;margin-bottom:8px">👤</div><div style="font-weight:600">No visitor data</div></div>';
  } else {
    const badgeColors = ['badge-green','badge-blue','badge-amber','badge-purple','badge-gray'];
    topList.innerHTML = sorted.map((v, i) => `
      <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#F9FAFB;border-radius:10px">
        <div>
          <span style="font-weight:600;font-size:13px;color:#1F2937">${esc(v.first_name + ' ' + v.last_name)}</span>
          <br><span style="font-size:11px;color:#9CA3AF">${esc(v.company || 'No Company')}</span>
        </div>
        <span class="badge ${badgeColors[i] || 'badge-gray'}">${v.visit_count || 0} visits</span>
      </div>
    `).join('');
  }

  // Purpose chart from logs data
  const purposeCounts = {};
  logs.forEach(l => {
    const p = labelCase(l.purpose);
    purposeCounts[p] = (purposeCounts[p] || 0) + 1;
  });
  const labels = Object.keys(purposeCounts);
  const data = Object.values(purposeCounts);
  const colors = ['#8B5CF6','#059669','#3B82F6','#F59E0B','#14B8A6','#DC2626','#6B7280'];

  if (purposeChart) purposeChart.destroy();
  const canvas = document.getElementById('chartVisitorPurpose');
  if (canvas && labels.length > 0) {
    purposeChart = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{ label: 'Visits', data: data, backgroundColor: labels.map((_, i) => colors[i % colors.length]), borderRadius: 6, barPercentage: 0.65 }]
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1F2937', cornerRadius: 8, padding: 10 } },
        scales: { x: { grid: { display: false }, border: { display: false } }, y: { beginAtZero: true, grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { stepSize: 1 } } }
      }
    });
  }

  // Render VIP dashboard widgets
  renderVipDashboard();
}

// ───── VIP Dashboard ─────
function renderVipDashboard() {
  const insideList  = document.getElementById('vip-inside-list');
  const upcomingList = document.getElementById('vip-upcoming-list');
  const insideCount  = document.getElementById('vip-inside-count');
  const upcomingCount = document.getElementById('vip-upcoming-count');
  if (!insideList || !upcomingList) return;

  const now = new Date();
  const todayStr = now.toISOString().slice(0, 10); // YYYY-MM-DD

  // ── VIP visitors currently inside ──
  const vipInside = stats.vip_visitors_inside || [];
  if (insideCount) insideCount.textContent = vipInside.length;
  if (vipInside.length === 0) {
    insideList.innerHTML = '<div class="empty-state" style="padding:20px"><div style="font-size:28px;margin-bottom:6px">⭐</div><div style="font-weight:600;font-size:13px;color:#9CA3AF">No VIP visitors inside right now</div></div>';
  } else {
    insideList.innerHTML = vipInside.map(v => {
      const preregBadge = parseInt(v.was_preregistered)
        ? `<span style="background:#EDE9FE;color:#6D28D9;padding:2px 8px;border-radius:6px;font-weight:600;font-size:10px">📅 Pre-registered</span>`
        : '';
      const sinceTime = v.check_in_time
        ? new Date(v.check_in_time).toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true})
        : '—';
      return `
      <div class="vip-inside-card" style="${parseInt(v.was_preregistered) ? 'border-color:#C4B5FD;background:linear-gradient(135deg,#F5F3FF,#EDE9FE)' : ''}">
        <div style="display:flex;align-items:center;gap:10px;flex:1;min-width:0">
          <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#FEF3C7,#FDE68A);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0">⭐</div>
          <div style="min-width:0">
            <div style="font-weight:700;font-size:13px;color:#1F2937">${esc((v.first_name||'') + ' ' + (v.last_name||''))} ${vtypeBadge(v.visitor_type)}</div>
            <div style="font-size:11px;color:#6B7280">${esc(v.company || 'No company')} · In since ${sinceTime}</div>
          </div>
        </div>
        <div style="text-align:right;display:flex;flex-direction:column;gap:3px;flex-shrink:0">
          ${preregBadge}
          ${v.security_level && v.security_level !== 'standard' ? `<span style="background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:6px;font-weight:600;font-size:10px">🛡️ ${(v.security_level||'').toUpperCase()}</span>` : ''}
          ${v.escort_required ? '<span style="background:#EDE9FE;color:#6D28D9;padding:2px 8px;border-radius:6px;font-weight:600;font-size:10px">👤 Escort</span>' : ''}
        </div>
      </div>`;
    }).join('');
  }

  // ── Upcoming VIP visits ──
  const vipUpcoming = stats.vip_upcoming || [];
  if (upcomingCount) upcomingCount.textContent = vipUpcoming.length;
  if (vipUpcoming.length === 0) {
    upcomingList.innerHTML = '<div class="empty-state" style="padding:20px"><div style="font-size:28px;margin-bottom:6px">📅</div><div style="font-weight:600;font-size:13px;color:#9CA3AF">No upcoming VIP visits</div></div>';
  } else {
    upcomingList.innerHTML = vipUpcoming.map(p => {
      const isToday    = p.expected_date === todayStr;
      const expectedDt = (p.expected_date && p.expected_time)
        ? new Date(p.expected_date + 'T' + p.expected_time)
        : (p.expected_date ? new Date(p.expected_date + 'T00:00:00') : null);
      const msUntil    = expectedDt ? (expectedDt - now) : null;
      // States: overdue (>30min past), arriving now (0–30min window), imminent (<60min), future
      const isOverdue      = isToday && msUntil !== null && msUntil < -30 * 60 * 1000;
      const isArrivingNow  = isToday && msUntil !== null && msUntil >= -30 * 60 * 1000 && msUntil <= 0;
      const isImminent     = isToday && msUntil !== null && msUntil > 0 && msUntil <= 60 * 60 * 1000;

      let rowBg = '', timeBadge = '', borderStyle = '';
      if (isArrivingNow) {
        rowBg = 'background:linear-gradient(135deg,#ECFDF5,#D1FAE5);';
        borderStyle = 'border:1.5px solid #6EE7B7;border-radius:10px;';
        timeBadge = `<span style="display:inline-flex;align-items:center;gap:4px;background:#059669;color:#fff;padding:3px 8px;border-radius:50px;font-size:10px;font-weight:700">` +
          `<span style="width:6px;height:6px;background:#fff;border-radius:50%;animation:pulse-dot 1s infinite"></span>ARRIVING NOW</span>`;
      } else if (isOverdue) {
        rowBg = 'background:#FEF9C3;';
        borderStyle = 'border:1.5px solid #FDE047;border-radius:10px;';
        timeBadge = `<span style="background:#EAB308;color:#fff;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:700">⏰ OVERDUE</span>`;
      } else if (isImminent) {
        rowBg = 'background:#FFF7ED;';
        borderStyle = 'border:1.5px solid #FED7AA;border-radius:10px;';
        const minsLeft = Math.ceil(msUntil / 60000);
        timeBadge = `<span style="background:#F97316;color:#fff;padding:2px 8px;border-radius:50px;font-size:10px;font-weight:700">⏱ ${minsLeft}min</span>`;
      }

      const dateLabel = p.expected_date
        ? new Date(p.expected_date + 'T00:00:00').toLocaleDateString('en-US',{month:'short',day:'numeric'})
        : '—';

      return `
      <div class="vip-upcoming-row" style="${rowBg}${borderStyle}padding:10px;margin-bottom:6px">
        <div style="flex:1;min-width:0">
          <div style="font-weight:700;font-size:13px;color:#1F2937">${esc(p.visitor_name || '')} ${vtypeBadge(p.visitor_type)}</div>
          <div style="font-size:11px;color:#6B7280;margin-top:1px">${esc(p.visitor_company || 'No company')} · ${labelCase(p.purpose || 'visit')}</div>
          ${timeBadge ? `<div style="margin-top:5px">${timeBadge}</div>` : ''}
        </div>
        <div style="text-align:right;flex-shrink:0">
          <div style="font-weight:700;font-size:12px;color:${isArrivingNow ? '#059669' : isOverdue ? '#CA8A04' : '#5B21B6'}">${dateLabel}</div>
          <div style="font-size:11px;color:#9CA3AF">${p.expected_time || ''}</div>
          ${p.host_name ? `<div style="font-size:10px;color:#9CA3AF;margin-top:2px">Host: ${esc(p.host_name)}</div>` : ''}
        </div>
      </div>`;
    }).join('');
  }
}

// Re-evaluate time-based states every 60s without a full API refetch
setInterval(() => { if (typeof stats !== 'undefined' && stats.vip_upcoming) renderVipDashboard(); }, 60000);

// ───── Approve Pre-Registration ─────
async function approvePrereg(code) {
  const result = await Swal.fire({
    title: 'Approve Pre-Registration?',
    text: `Approve ${code}? The visitor will be added to Registered Visitors.`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#059669',
    confirmButtonText: 'Yes, Approve',
    cancelButtonText: 'Cancel'
  });
  if (!result.isConfirmed) return;
  try {
    const res = await fetch(API + '?action=approve_prereg', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ prereg_code: code })
    });
    const data = await res.json();
    if (data.success) {
      Swal.fire({ icon:'success', title:'Approved!', text: data.message || 'Pre-registration approved and visitor registered.', timer:2000, showConfirmButton:false });
      await loadData();
    } else {
      Swal.fire({ icon:'error', title:'Error', text: data.error || 'Failed to approve.' });
    }
  } catch (e) {
    Swal.fire({ icon:'error', title:'Error', text: 'Network error.' });
  }
}

// ───── Reject Pre-Registration ─────
async function rejectPrereg(code) {
  const result = await Swal.fire({
    title: 'Reject Pre-Registration?',
    text: `Reject ${code}? This action cannot be undone.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#DC2626',
    confirmButtonText: 'Yes, Reject',
    cancelButtonText: 'Cancel'
  });
  if (!result.isConfirmed) return;
  try {
    const res = await fetch(API + '?action=reject_prereg', {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify({ prereg_code: code })
    });
    const data = await res.json();
    if (data.success) {
      Swal.fire({ icon:'success', title:'Rejected', text:'Pre-registration rejected.', timer:2000, showConfirmButton:false });
      await loadData();
    } else {
      Swal.fire({ icon:'error', title:'Error', text: data.error || 'Failed to reject.' });
    }
  } catch (e) {
    Swal.fire({ icon:'error', title:'Error', text: 'Network error.' });
  }
}

// ═══════════════════════════════════════════════════════
// QR AUTO SCAN — Automatic Check-In / Check-Out with Modal
// ═══════════════════════════════════════════════════════
let scanModalTimer = null;
let scanCooldown = false;

function beep(freq = 1200, dur = 0.1) {
  try { const ac = new (window.AudioContext||window.webkitAudioContext)(); const o = ac.createOscillator(); const g = ac.createGain(); o.connect(g); g.connect(ac.destination); o.frequency.value = freq; g.gain.value = 0.13; o.start(); o.stop(ac.currentTime + dur); } catch(e){}
}

async function autoScanProcess(code) {
  if (scanCooldown) return;
  scanCooldown = true;
  beep(1200);

  // Also show code in the manual input for reference
  document.getElementById('scan-code-input').value = code;

  try {
    const res = await fetch(API + '?action=lookup_visitor&code=' + encodeURIComponent(code));
    const data = await res.json();

    if (!data.found) {
      showScanModal({
        avatar: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        name: 'Not Found', sub: 'Code: ' + code,
        statusBg: '#FEF2F2', statusColor: '#DC2626',
        statusIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
        statusTitle: 'NOT FOUND', statusSub: 'No visitor matches this QR code',
        info: '', action: ''
      });
      setTimeout(() => { scanCooldown = false; }, 3000);
      return;
    }

    if (data.type === 'visitor') {
      const v = data.visitor;
      const activeLog = data.active_log;
      const name = (v.first_name||'') + ' ' + (v.last_name||'');
      const photo = v.photo_url
        ? `<img src="${photoSrc(v.photo_url)}" style="width:100%;height:100%;object-fit:cover">`
        : `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`;

      if (activeLog) {
        // ── Already checked in → Auto-CHECK OUT ──
        try {
          const coRes = await fetch(API + '?action=check_out', {
            method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ log_id: activeLog.log_id })
          });
          const coResult = await coRes.json();
          if (coResult.success) {
            beep(800, 0.08); setTimeout(() => beep(1200, 0.12), 100);
            showScanModal({
              avatar: photo, name: esc(name), sub: esc(v.company||'No Company') + ' · ' + esc(v.visitor_code),
              statusBg: '#FEF3C7', statusColor: '#92400E',
              statusIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
              statusTitle: 'CHECKED OUT',
              statusSub: 'Goodbye! Checked out at ' + new Date().toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true}),
              info: `<div><div class="si-label">Checked In</div><div class="si-value">${fmtTime(activeLog.check_in_time)}</div></div>
                     <div><div class="si-label">Duration</div><div class="si-value">${duration(activeLog.check_in_time, new Date().toISOString())}</div></div>`,
              action: ''
            });
            await loadData();
          } else {
            Swal.fire({icon:'error',title:'Check-Out Failed',text:coResult.error||'Unknown error',confirmButtonColor:'#059669'});
          }
        } catch(e) { Swal.fire({icon:'error',title:'Error',text:e.message,confirmButtonColor:'#059669'}); }
      } else {
        // ── Not checked in → Auto-CHECK IN (quick with default purpose "visit") ──
        try {
          const ciRes = await fetch(API + '?action=check_in', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body: JSON.stringify({ visitor_id: parseInt(v.visitor_id), purpose: 'visit' })
          });
          const ciResult = await ciRes.json();
          if (ciResult.success) {
            beep(1000, 0.08); setTimeout(() => beep(1400, 0.15), 120);
            showScanModal({
              avatar: photo, name: esc(name), sub: esc(v.company||'No Company') + ' · ' + esc(v.visitor_code),
              statusBg: '#D1FAE5', statusColor: '#065F46',
              statusIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>',
              statusTitle: 'CHECKED IN',
              statusSub: 'Welcome! Visit code: ' + (ciResult.visit_code || ''),
              info: `<div><div class="si-label">Time</div><div class="si-value">${new Date().toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true})}</div></div>
                     <div><div class="si-label">Purpose</div><div class="si-value">Visit</div></div>`,
              action: ''
            });
            await loadData();
          } else {
            Swal.fire({icon:'error',title:'Check-In Failed',text:ciResult.error||'Unknown error',confirmButtonColor:'#059669'});
          }
        } catch(e) { Swal.fire({icon:'error',title:'Error',text:e.message,confirmButtonColor:'#059669'}); }
      }

    } else if (data.type === 'prereg') {
      const p = data.prereg;
      showScanModal({
        avatar: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        name: esc(p.visitor_name), sub: esc(p.visitor_company||'No Company') + ' · ' + esc(p.prereg_code),
        statusBg: '#EFF6FF', statusColor: '#1E40AF',
        statusIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#3B82F6" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
        statusTitle: 'PRE-REGISTERED',
        statusSub: 'Expected ' + fmtDate(p.expected_date) + (p.expected_time ? ' at '+p.expected_time : ''),
        info: `<div><div class="si-label">Purpose</div><div class="si-value">${labelCase(p.purpose||'—')}</div></div>
               <div><div class="si-label">Host</div><div class="si-value">${esc(p.host_name||'—')}</div></div>`,
        action: `<button class="btn btn-primary" style="width:100%;padding:10px;font-size:14px" onclick="closeScanModal();registerFromPrereg(${JSON.stringify(p).replace(/"/g,'&quot;').replace(/'/g,"\\'")})">
                  Register &amp; Check In</button>`
      });

    } else if (data.type === 'visit_log') {
      const l = data.log;
      const isIn = l.status === 'checked_in';
      showScanModal({
        avatar: '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
        name: esc(l.visitor_name), sub: esc(l.company||'') + ' · ' + esc(l.visit_code),
        statusBg: isIn ? '#D1FAE5' : '#F3F4F6', statusColor: isIn ? '#065F46' : '#374151',
        statusIcon: isIn
          ? '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>'
          : '<svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
        statusTitle: isIn ? 'CHECKED IN' : 'CHECKED OUT',
        statusSub: isIn ? 'Since ' + fmtTime(l.check_in_time) : 'At ' + fmtTime(l.check_out_time),
        info: `<div><div class="si-label">Check-In</div><div class="si-value">${fmtDateTime(l.check_in_time)}</div></div>
               <div><div class="si-label">Check-Out</div><div class="si-value">${l.check_out_time ? fmtDateTime(l.check_out_time) : '—'}</div></div>
               <div><div class="si-label">Duration</div><div class="si-value">${duration(l.check_in_time, l.check_out_time)}</div></div>
               <div><div class="si-label">Purpose</div><div class="si-value">${labelCase(l.purpose||'—')}</div></div>`,
        action: isIn ? `<button class="btn btn-outline btn-sm" style="color:#DC2626;border-color:#DC2626;width:100%;padding:10px;font-size:14px" onclick="scanModalCheckOut(${l.log_id})">Check Out Now</button>` : ''
      });
    }

  } catch(e) {
    console.error('Auto-scan error:', e);
    Swal.fire({icon:'error',title:'Scan Error',text:e.message,confirmButtonColor:'#059669'});
  }

  setTimeout(() => { scanCooldown = false; }, 3000);
}

function showScanModal(opts) {
  const modal = document.getElementById('modal-scan-result');
  document.getElementById('scan-modal-avatar').innerHTML = opts.avatar || '';
  document.getElementById('scan-modal-name').innerHTML = opts.name || '';
  document.getElementById('scan-modal-sub').innerHTML = opts.sub || '';

  const st = document.getElementById('scan-modal-status');
  st.style.background = opts.statusBg;
  st.innerHTML = `<div class="sm-icon" style="background:${opts.statusBg};border:3px solid ${opts.statusColor}20">${opts.statusIcon}</div>
    <div class="sm-title" style="color:${opts.statusColor}">${opts.statusTitle}</div>
    <div class="sm-sub" style="color:${opts.statusColor}">${opts.statusSub||''}</div>`;

  document.getElementById('scan-modal-info').innerHTML = opts.info || '';
  document.getElementById('scan-modal-action').innerHTML = opts.action || '';

  // Timer bar reset
  const fill = document.getElementById('scan-timer-fill');
  fill.style.animation = 'none'; fill.offsetHeight;
  fill.style.setProperty('--scan-timer', '8s');
  fill.style.animation = '';

  modal.style.display = 'flex'; modal.style.alignItems = 'center'; modal.style.justifyContent = 'center';
  clearTimeout(scanModalTimer);
  scanModalTimer = setTimeout(() => closeScanModal(), 8000);
}

function closeScanModal() {
  const m = document.getElementById('modal-scan-result');
  if (m) m.style.display = 'none';
  clearTimeout(scanModalTimer);
}

async function scanModalCheckOut(logId) {
  closeScanModal();
  try {
    const res = await fetch(API + '?action=check_out', {
      method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({ log_id: logId })
    });
    const result = await res.json();
    if (result.success) {
      beep(800,0.08); setTimeout(()=>beep(1200,0.12),100);
      showScanModal({
        avatar: '<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>',
        name: 'Checked Out', sub: '',
        statusBg: '#D1FAE5', statusColor: '#065F46',
        statusIcon: '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>',
        statusTitle: 'CHECKED OUT', statusSub: 'Success · ' + new Date().toLocaleTimeString('en-US',{hour:'numeric',minute:'2-digit',hour12:true}),
        info: '', action: ''
      });
      await loadData();
    } else {
      Swal.fire({icon:'error',title:'Failed',text:result.error||'Unknown error',confirmButtonColor:'#059669'});
    }
  } catch(e) {
    Swal.fire({icon:'error',title:'Error',text:e.message,confirmButtonColor:'#059669'});
  }
}

// ───── Section Switching (hash-driven) ─────
function showSection(hash) {
  const sections = document.querySelectorAll('.tab-content');
  let id = hash ? hash.replace('#', '') : 'tab-registration';

  // Highlight active directory card
  document.querySelectorAll('.module-directory-label + .stats-grid .stat-card-link').forEach(c => {
    const href = c.getAttribute('href') || '';
    c.classList.toggle('active-module', href === '#' + id);
    const arrow = c.querySelector('.stat-arrow');
    if (arrow) arrow.textContent = href === '#' + id ? '●' : '→';
  });

  // Redirect tab-prereg to tab-registration with prereg sub-tab
  let subtabToActivate = null;
  if (id === 'tab-prereg') {
    id = 'tab-registration';
    subtabToActivate = 'prereg';
  }

  sections.forEach(s => s.classList.remove('active'));
  const target = document.getElementById(id);
  if (target) target.classList.add('active');
  else if (sections[0]) sections[0].classList.add('active');

  if (id === 'tab-analytics') renderAnalytics();
  if (id === 'tab-logs') renderLogFolders();

  if (id === 'tab-registration' && subtabToActivate) {
    const btn = document.querySelector(`.sub-tab[onclick*="'${subtabToActivate}'"]`);
    if (btn) switchSubTab(btn, subtabToActivate);
  }
}
window.addEventListener('hashchange', () => showSection(location.hash));

// ───── Init ─────
(async () => {
  await loadData();
  showSection(location.hash);

  // ─── Real-time auto-refresh every 30 seconds ───
  setInterval(async () => {
    const badge = document.getElementById('live-vis-analytics');
    if (badge) badge.classList.add('refreshing');
    try {
      await loadData();
    } catch(e) { console.error('Auto-refresh error:', e); }
    if (badge) badge.classList.remove('refreshing');
    const ts = document.getElementById('updated-vis-analytics');
    if (ts) ts.textContent = new Date().toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', second:'2-digit', hour12:true });
  }, 30000);
})();

// ───── Export Functions ─────
function exportVisitors(format) {
  const headers = ['Code', 'Name', 'Company', 'Contact', 'ID Type', 'ID Number', 'Total Visits'];
  const rows = visitors.map(v => [
    v.visitor_code || '', v.name || (v.first_name || '') + ' ' + (v.last_name || ''),
    v.company || '', v.contact_number || v.phone || '', v.id_type || '',
    v.id_number || '', v.visit_count || v.total_visits || 0
  ]);
  format === 'csv' ? ExportHelper.exportCSV('Visitors_Registered', headers, rows)
    : ExportHelper.exportPDF('Visitors_Registered', 'Visitor Management — Registered Visitors', headers, rows, { subtitle: visitors.length + ' visitors' });
}

function exportPreregs(format) {
  const headers = ['Code', 'Visitor Name', 'Company', 'Purpose', 'Expected Date', 'Host', 'Status'];
  const rows = preregs.map(p => [
    p.prereg_code || p.code || '', p.visitor_name || p.name || '',
    p.company || '', p.purpose || '', p.expected_date || p.visit_date || '',
    p.host_name || p.host || '', p.status || ''
  ]);
  format === 'csv' ? ExportHelper.exportCSV('Visitors_PreRegistrations', headers, rows)
    : ExportHelper.exportPDF('Visitors_PreRegistrations', 'Visitor Management — Pre-Registrations', headers, rows, { subtitle: preregs.length + ' records' });
}

function exportLogs(format) {
  const headers = ['Visit Code', 'Visitor', 'Company', 'Purpose', 'Check-In', 'Check-Out', 'Duration', 'Host', 'Status'];
  const rows = logs.map(l => {
    let duration = '';
    if (l.check_in_time && l.check_out_time) {
      const diff = Math.abs(new Date(l.check_out_time) - new Date(l.check_in_time));
      const hrs = Math.floor(diff / 3600000);
      const mins = Math.floor((diff % 3600000) / 60000);
      duration = hrs > 0 ? hrs + 'h ' + mins + 'm' : mins + 'm';
    }
    return [
      l.log_code || l.visit_code || '', l.visitor_name || '',
      l.company || '', l.purpose || '', l.check_in_time || '',
      l.check_out_time || '', duration, l.host_name || l.host || '', l.status || ''
    ];
  });
  format === 'csv' ? ExportHelper.exportCSV('Visitors_Logs', headers, rows)
    : ExportHelper.exportPDF('Visitors_Logs', 'Visitor Management — Visit History & Logs', headers, rows, { landscape: true, subtitle: logs.length + ' log entries' });
}

// ═══════════════════════════════════════════════════════
// SUBMODULE: PRE-REGISTRATION & APPOINTMENTS
// ═══════════════════════════════════════════════════════

function renderPreregTab() {
  // Only show pending pre-registrations (approved ones move to registered visitors)
  const list = (preregs || []).filter(p => (p.status || '').toLowerCase() === 'pending');
  const allList = preregs || [];
  const search = (document.getElementById('pr-search')?.value || '').toLowerCase();
  const today = new Date().toISOString().slice(0, 10);

  // Stats
  let pending = list.length, approved = allList.filter(p => (p.status || '').toLowerCase() === 'approved').length, expectedToday = 0;
  list.forEach(p => {
    const ed = (p.expected_date || p.visit_date || '').slice(0, 10);
    if (ed === today) expectedToday++;
  });
  document.getElementById('pr-total').textContent = list.length;
  document.getElementById('pr-pending').textContent = pending;
  document.getElementById('pr-approved').textContent = approved;
  document.getElementById('pr-today').textContent = expectedToday;

  // Filter by search only (status filter removed — we always show pending)
  let filtered = list.filter(p => {
    if (search) {
      const name = (p.visitor_name || p.name || '').toLowerCase();
      const company = (p.company || '').toLowerCase();
      const code = (p.prereg_code || p.code || '').toLowerCase();
      if (!name.includes(search) && !company.includes(search) && !code.includes(search)) return false;
    }
    return true;
  });

  // Schedule Timeline (group by date)
  const byDate = {};
  filtered.forEach(p => {
    const d = (p.expected_date || p.visit_date || '').slice(0, 10) || 'Unknown';
    if (!byDate[d]) byDate[d] = [];
    byDate[d].push(p);
  });
  const sortedDates = Object.keys(byDate).sort();
  let timelineHtml = '';
  if (sortedDates.length === 0) {
    timelineHtml = '<div style="text-align:center;padding:30px;color:#9CA3AF">No pre-registrations found</div>';
  } else {
    sortedDates.forEach(date => {
      const isToday = date === today;
      const dateLabel = isToday ? '📅 Today (' + date + ')' : date;
      const borderColor = isToday ? '#059669' : '#E5E7EB';
      timelineHtml += `<div style="margin-bottom:16px;border-left:3px solid ${borderColor};padding-left:16px">
        <div style="font-weight:700;font-size:14px;color:${isToday ? '#059669' : '#374151'};margin-bottom:8px">${dateLabel} · ${byDate[date].length} appointment${byDate[date].length > 1 ? 's' : ''}</div>`;
      byDate[date].forEach(p => {
        const statusColors = { pending: '#F59E0B', approved: '#059669', rejected: '#EF4444', completed: '#3B82F6' };
        const sc = statusColors[(p.status || '').toLowerCase()] || '#6B7280';
        timelineHtml += `<div style="display:flex;align-items:center;gap:12px;padding:8px 12px;background:#F9FAFB;border-radius:8px;margin-bottom:6px">
          <div style="width:8px;height:8px;border-radius:50%;background:${sc};flex-shrink:0"></div>
          <div style="flex:1;min-width:0">
            <span style="font-weight:600;font-size:13px;color:#1F2937">${p.visitor_name || p.name || 'Unknown'}</span>
            <span style="font-size:12px;color:#6B7280;margin-left:8px">${p.company || ''}</span>
          </div>
          <span style="font-size:12px;color:#6B7280">${p.purpose || ''}</span>
          <span style="font-size:11px;font-weight:600;color:${sc};text-transform:capitalize">${p.status || ''}</span>
        </div>`;
      });
      timelineHtml += '</div>';
    });
  }
  document.getElementById('pr-schedule-timeline').innerHTML = timelineHtml;

  // Table
  document.getElementById('pr-table-count').textContent = filtered.length + ' records';
  let tbody = '';
  filtered.forEach(p => {
    const statusColors = { pending: 'background:#FEF3C7;color:#92400E', approved: 'background:#D1FAE5;color:#065F46', rejected: 'background:#FEE2E2;color:#991B1B', completed: 'background:#DBEAFE;color:#1E40AF' };
    const sStyle = statusColors[(p.status || '').toLowerCase()] || 'background:#F3F4F6;color:#374151';
    const code = p.prereg_code || p.code || '';
    tbody += `<tr>
      <td><span style="font-weight:700;color:#059669;font-size:12px">${code}</span></td>
      <td style="font-weight:600">${p.visitor_name || p.name || ''}</td>
      <td>${p.company || ''}</td>
      <td>${p.purpose || ''}</td>
      <td>${p.expected_date || p.visit_date || ''}</td>
      <td>${p.host_name || p.host || ''}</td>
      <td><span class="status-badge" style="font-size:11px;${sStyle};text-transform:capitalize">${p.status || ''}</span></td>
      <td style="white-space:nowrap">
        ${(p.status || '').toLowerCase() === 'pending' ? `<button class="btn btn-outline btn-sm" style="font-size:11px;color:#059669;border-color:#059669" onclick="approvePrereg('${code}')">✅ Approve</button>
        <button class="btn btn-outline btn-sm" style="font-size:11px;color:#DC2626;border-color:#DC2626;margin-left:4px" onclick="rejectPrereg('${code}')">❌ Reject</button>` : '—'}
      </td>
    </tr>`;
  });
  document.getElementById('prereg-tab-tbody').innerHTML = tbody || '<tr><td colspan="8" class="text-center text-gray-400 py-8">No pre-registrations found</td></tr>';
}

// ═══════════════════════════════════════════════════════
// HR2 INTEGRATION — Data Display Logic
// ═══════════════════════════════════════════════════════

let hr2Loaded = false;
let hr2EmployeesData = [];
const HR2_BRIDGE = '../../api/hr2.php';

function switchHR2Tab(panelId, btn) {
  document.querySelectorAll('#hr2-sub-tabs .sub-tab').forEach(t => t.classList.remove('active'));
  if (btn) btn.classList.add('active');
  document.querySelectorAll('.hr2-panel').forEach(p => p.style.display = 'none');
  const panel = document.getElementById(panelId);
  if (panel) panel.style.display = '';
}

async function hr2InitTab() {
  if (hr2Loaded) return;
  hr2Loaded = true;
  hr2CheckConnection();
  hr2LoadEmployees();
  hr2LoadLeaves();
  hr2LoadTraining();
  hr2LoadSuccessors();
  hr2LoadJobs();
}

function hr2RefreshAll() { hr2Loaded = false; hr2InitTab(); }

async function hr2CheckConnection() {
  const dot = document.getElementById('hr2-status-dot');
  const txt = document.getElementById('hr2-status-text');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=health').then(r => r.json());
    if (res.hr2_alive) {
      dot.style.background = '#059669'; dot.style.animation = 'none';
      txt.textContent = '\u2713 Connected to HR2 (' + (res.domain || 'hr2.microfinancial-1.com') + ')';
      txt.style.color = '#059669';
      document.getElementById('stat-hr2').innerHTML = '<span style="color:#059669;font-weight:700">Online</span>';
    } else {
      dot.style.background = '#EF4444'; dot.style.animation = 'none';
      txt.textContent = '\u2717 HR2 unreachable'; txt.style.color = '#EF4444';
      document.getElementById('stat-hr2').innerHTML = '<span style="color:#EF4444">Offline</span>';
    }
  } catch {
    dot.style.background = '#EF4444'; dot.style.animation = 'none';
    txt.textContent = '\u2717 HR2 bridge error'; txt.style.color = '#EF4444';
    document.getElementById('stat-hr2').innerHTML = '<span style="color:#EF4444">Error</span>';
  }
}

async function hr2LoadEmployees() {
  const tbody = document.getElementById('hr2-emp-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=employees').then(r => r.json());
    hr2EmployeesData = res.data || [];
    document.getElementById('hr2-stat-employees').textContent = hr2EmployeesData.length;
    hr2RenderEmployees(hr2EmployeesData);
  } catch (err) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; }
}

function hr2RenderEmployees(list) {
  const tbody = document.getElementById('hr2-emp-tbody');
  if (!list.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No employees found</td></tr>'; return; }
  tbody.innerHTML = list.slice(0, 100).map(e => {
    const st = e.status || e.employment_status || '\u2014';
    const stColor = ['active','regular'].includes((st+'').toLowerCase()) ? '#059669' : '#EF4444';
    return `<tr><td style="font-weight:600">${e.employee_id || '\u2014'}</td><td style="font-weight:600">${e.full_name || '\u2014'}</td><td style="font-size:12px;color:#6B7280">${e.email || '\u2014'}</td><td>${e.department || '\u2014'}</td><td>${e.position || e.job_title || '\u2014'}</td><td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td></tr>`;
  }).join('');
}

function hr2SearchEmployees() {
  const q = (document.getElementById('hr2-emp-search').value || '').toLowerCase();
  if (!q) { hr2RenderEmployees(hr2EmployeesData); return; }
  hr2RenderEmployees(hr2EmployeesData.filter(e => (e.full_name||'').toLowerCase().includes(q)||(e.email||'').toLowerCase().includes(q)||(e.department||'').toLowerCase().includes(q)));
}

async function hr2LoadLeaves() {
  const tbody = document.getElementById('hr2-leaves-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=leaves').then(r => r.json());
    const leaves = res.data || [];
    document.getElementById('hr2-stat-leaves').textContent = leaves.length;
    if (!leaves.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No leave records</td></tr>'; return; }
    tbody.innerHTML = leaves.slice(0,100).map(l => {
      const st = l.status || '\u2014';
      const stColor = st==='approved'?'#059669':st==='pending'?'#D97706':'#EF4444';
      return `<tr><td style="font-weight:600">${l.employee_name||'\u2014'}</td><td>${l.leave_type||'\u2014'}</td><td style="font-size:12px">${l.start_date||'\u2014'}</td><td style="font-size:12px">${l.end_date||'\u2014'}</td><td style="text-align:center">${l.days||'\u2014'}</td><td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td></tr>`;
    }).join('');
  } catch (err) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; }
}

async function hr2LoadTraining() {
  const tbody = document.getElementById('hr2-training-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=training').then(r => r.json());
    const items = res.data || [];
    document.getElementById('hr2-stat-training').textContent = items.length;
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF">No training records</td></tr>'; return; }
    tbody.innerHTML = items.slice(0,100).map(t => `<tr><td style="font-weight:600">${t.employee_name||'\u2014'}</td><td>${t.training_name||t.title||'\u2014'}</td><td>${t.provider||'\u2014'}</td><td style="font-size:12px">${t.date||t.start_date||'\u2014'}</td><td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:#05966920;color:#059669">${t.status||'enrolled'}</span></td></tr>`).join('');
  } catch (err) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; }
}

async function hr2LoadSuccessors() {
  const tbody = document.getElementById('hr2-successors-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=successors').then(r => r.json());
    const items = res.data || [];
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF">No succession data</td></tr>'; return; }
    tbody.innerHTML = items.slice(0,100).map(s => `<tr><td style="font-weight:600">${s.position||'\u2014'}</td><td>${s.incumbent||'\u2014'}</td><td>${s.successor||'\u2014'}</td><td>${s.readiness||'\u2014'}</td><td>${s.priority||'\u2014'}</td></tr>`).join('');
  } catch (err) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; }
}

async function hr2LoadJobs() {
  const tbody = document.getElementById('hr2-jobs-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=jobs').then(r => r.json());
    const items = res.data || [];
    document.getElementById('hr2-stat-jobs').textContent = items.length;
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF">No job titles</td></tr>'; return; }
    tbody.innerHTML = items.slice(0,100).map(j => `<tr><td style="font-weight:600">${j.title||j.job_title||'\u2014'}</td><td>${j.department||'\u2014'}</td><td>${j.level||'\u2014'}</td><td style="text-align:center">${j.headcount||'\u2014'}</td><td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:#05966920;color:#059669">${j.status||'active'}</span></td></tr>`).join('');
  } catch (err) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; }
}

</script>
</body>
</html>
