<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — Microfinancial Management System I</title>

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

  <link rel="stylesheet" href="admin.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .live-badge {
      display: inline-flex; align-items: center; gap: 5px;
      font-size: 10px; font-weight: 700; letter-spacing: 0.5px;
      color: #059669; background: #ECFDF5; border: 1px solid #A7F3D0;
      padding: 3px 10px; border-radius: 20px; white-space: nowrap;
    }
    .live-dot {
      width: 7px; height: 7px; border-radius: 50%;
      background: #059669; display: inline-block;
      animation: livePulse 1.5s ease-in-out infinite;
    }
    @keyframes livePulse {
      0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(5,150,105,0.5); }
      50% { opacity: 0.6; box-shadow: 0 0 0 4px rgba(5,150,105,0); }
    }
    .live-badge.refreshing { color: #D97706; background: #FFFBEB; border-color: #FDE68A; }
    .live-badge.refreshing .live-dot { background: #D97706; animation: livePulseAmber 0.6s ease-in-out infinite; }
    @keyframes livePulseAmber {
      0%, 100% { opacity: 1; } 50% { opacity: 0.3; }
    }
    .last-updated {
      font-size: 10px; color: #9CA3AF; white-space: nowrap;
    }
  </style>
</head>

<body class="bg-brand-background-main min-h-screen font-[Inter,'Segoe_UI',system-ui,-apple-system,sans-serif]">

  <?php $activePage = 'dashboard'; $baseUrl = ''; include 'sidebar.php'; ?>

  <!-- MAIN WRAPPER -->
  <div class="md:pl-72">

    <!-- TOP HEADER -->
    <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative
                   shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
      <div class="hidden md:block absolute left-0 top-0 h-16 w-[2px] bg-white"></div>

      <div class="flex items-center gap-3">
        <button id="mobile-menu-btn"
          class="md:hidden w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center">
          ☰
        </button>
      </div>

      <div class="flex items-center gap-3 sm:gap-5">
        <span id="real-time-clock"
          class="text-xs font-bold text-gray-700 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200">
          --:--:--
        </span>

        <button id="notification-bell"
          class="w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center relative">
          🔔
          <span id="notif-badge" class="absolute top-2 right-2 w-2.5 h-2.5 rounded-full bg-red-500 border-2 border-white"></span>
        </button>

        <div class="h-8 w-px bg-gray-200 hidden sm:block"></div>

        <div class="relative">
          <button id="user-menu-button"
            class="flex items-center gap-3 focus:outline-none group rounded-xl px-2 py-2
                   hover:bg-gray-100 active:bg-gray-200 transition">
            <div class="w-10 h-10 rounded-full bg-white shadow group-hover:shadow-md transition-shadow overflow-hidden flex items-center justify-center border border-gray-100">
              <div class="w-full h-full flex items-center justify-center font-bold text-brand-primary bg-emerald-50"><?= $userInitial ?></div>
            </div>
            <div class="hidden md:flex flex-col items-start text-left">
              <span class="text-sm font-bold text-gray-700 group-hover:text-brand-primary transition-colors">
                <?= htmlspecialchars($userName) ?>
              </span>
              <span class="text-[10px] text-gray-500 font-medium uppercase group-hover:text-brand-primary transition-colors">
                <?= htmlspecialchars($userRole) ?>
              </span>
            </div>
            <svg class="w-4 h-4 text-gray-400 group-hover:text-brand-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>

          <div id="user-menu-dropdown"
            class="dropdown-panel hidden opacity-0 translate-y-2 scale-95 pointer-events-none
                   absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100
                   transition-all duration-200 z-50">
            <div class="px-4 py-3 border-b border-gray-100">
              <div class="text-sm font-bold text-gray-800"><?= htmlspecialchars($userName) ?></div>
              <div class="text-xs text-gray-500"><?= htmlspecialchars($sessionUser['email'] ?? '') ?></div>
            </div>
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

      <div class="animate-in" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px">
        <div>
          <h1 class="page-title">Administrative Dashboard</h1>
          <p class="page-subtitle">Microfinancial Management System I — Real-Time QR Integration &amp; OCR Smart Operations</p>
        </div>
        <div class="export-btn-group">
          <button class="btn-export btn-export-pdf" onclick="exportDashboard('pdf')">📄 Export PDF</button>
          <button class="btn-export btn-export-csv" onclick="exportDashboard('csv')">📊 Export CSV</button>
        </div>
      </div>

      <!-- OVERVIEW STAT CARDS (clickable, dynamic) -->
      <div class="stats-grid animate-in delay-1">
        <a href="modules/facilities/facilities.php" class="stat-card stat-card-link">
          <div class="stat-icon green">🏢</div>
          <div class="stat-info">
            <div class="stat-value" id="dash-total-facilities">—</div>
            <div class="stat-label">Total Facilities</div>
          </div>
          <div class="stat-arrow">→</div>
        </a>
        <a href="modules/documents/documents.php" class="stat-card stat-card-link">
          <div class="stat-icon blue">📄</div>
          <div class="stat-info">
            <div class="stat-value" id="dash-active-docs">—</div>
            <div class="stat-label">Active Documents</div>
          </div>
          <div class="stat-arrow">→</div>
        </a>
        <a href="modules/legal/legal.php" class="stat-card stat-card-link">
          <div class="stat-icon amber">⚖️</div>
          <div class="stat-info">
            <div class="stat-value" id="dash-legal-cases">—</div>
            <div class="stat-label">Legal Cases</div>
          </div>
          <div class="stat-arrow">→</div>
        </a>
        <a href="modules/visitors/visitors.php" class="stat-card stat-card-link">
          <div class="stat-icon purple">🧑‍💼</div>
          <div class="stat-info">
            <div class="stat-value" id="dash-visitors">—</div>
            <div class="stat-label">Registered Visitors</div>
          </div>
          <div class="stat-arrow">→</div>
        </a>
      </div>

      <!-- SECONDARY STAT ROW -->
      <div class="stats-grid stats-grid-secondary animate-in delay-1" style="margin-top:-8px">
        <div class="stat-card-mini">
          <span class="stat-mini-dot green"></span>
          <span class="stat-mini-val" id="dash-avail-fac">—</span>
          <span class="stat-mini-lbl">Available</span>
          <span class="stat-mini-sep">|</span>
          <span class="stat-mini-val" id="dash-pending-res">—</span>
          <span class="stat-mini-lbl">Pending</span>
        </div>
        <div class="stat-card-mini">
          <span class="stat-mini-dot blue"></span>
          <span class="stat-mini-val" id="dash-archived-docs">—</span>
          <span class="stat-mini-lbl">Archived</span>
          <span class="stat-mini-sep">|</span>
          <span class="stat-mini-val" id="dash-ocr-queue">—</span>
          <span class="stat-mini-lbl">OCR Queue</span>
        </div>
        <div class="stat-card-mini">
          <span class="stat-mini-dot amber"></span>
          <span class="stat-mini-val" id="dash-active-contracts">—</span>
          <span class="stat-mini-lbl">Contracts</span>
          <span class="stat-mini-sep">|</span>
          <span class="stat-mini-val" id="dash-compliance">—</span>
          <span class="stat-mini-lbl">Compliance</span>
        </div>
        <div class="stat-card-mini">
          <span class="stat-mini-dot purple"></span>
          <span class="stat-mini-val" id="dash-checked-in">—</span>
          <span class="stat-mini-lbl">Checked In</span>
          <span class="stat-mini-sep">|</span>
          <span class="stat-mini-val" id="dash-preregs">—</span>
          <span class="stat-mini-lbl">Pre-Regs</span>
        </div>
      </div>

      <!-- ANALYTICS CHART WIDGETS -->
      <div class="analytics-grid animate-in delay-2">

        <div class="chart-widget">
          <div class="chart-widget-header">
            <div class="widget-title-area">
              <div class="widget-icon" style="background:#D1FAE5">🏢</div>
              <div>
                <div class="widget-title">Facilities Reservation</div>
                <div class="widget-subtitle">Monthly reservation trends &amp; status breakdown</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <span class="last-updated" id="updated-fac"></span>
            </div>
          </div>
          <div class="chart-widget-body"><canvas id="chartFacilities"></canvas></div>
          <div class="chart-widget-footer">
            <div class="widget-stats">
              <div class="ws-item"><div class="ws-val" id="ws-fac-avail">—</div><div class="ws-lbl">Available</div></div>
              <div class="ws-item"><div class="ws-val" id="ws-fac-pending">—</div><div class="ws-lbl">Pending</div></div>
              <div class="ws-item"><div class="ws-val" id="ws-fac-today">—</div><div class="ws-lbl">Today</div></div>
            </div>
            <a href="modules/facilities/facilities.php" class="btn-go btn-go-green">
              Go to Directory
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
          </div>
        </div>

        <div class="chart-widget">
          <div class="chart-widget-header">
            <div class="widget-title-area">
              <div class="widget-icon" style="background:#DBEAFE">📄</div>
              <div>
                <div class="widget-title">Document Management</div>
                <div class="widget-subtitle">Archive distribution by category &amp; OCR status</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <span class="last-updated" id="updated-doc"></span>
            </div>
          </div>
          <div class="chart-widget-body"><canvas id="chartDocuments"></canvas></div>
          <div class="chart-widget-footer">
            <div class="widget-stats">
              <div class="ws-item"><div class="ws-val" id="ws-doc-total">—</div><div class="ws-lbl">Documents</div></div>
              <div class="ws-item"><div class="ws-val" id="ws-doc-depts">—</div><div class="ws-lbl">Departments</div></div>
              <div class="ws-item"><div class="ws-val" id="ws-doc-ocr">—</div><div class="ws-lbl">OCR Queue</div></div>
            </div>
            <a href="modules/documents/documents.php" class="btn-go btn-go-blue">
              Go to Directory
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
          </div>
        </div>

        <div class="chart-widget">
          <div class="chart-widget-header">
            <div class="widget-title-area">
              <div class="widget-icon" style="background:#FEF3C7">⚖️</div>
              <div>
                <div class="widget-title">Legal Management</div>
                <div class="widget-subtitle">Case status, compliance risk &amp; contract values</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <span class="last-updated" id="updated-leg"></span>
            </div>
          </div>
          <div class="chart-widget-body"><canvas id="chartLegal"></canvas></div>
          <div class="chart-widget-footer">
            <div class="widget-stats">
              <div class="ws-item"><div class="ws-val" id="ws-leg-cases">—</div><div class="ws-lbl">Cases</div></div>
              <div class="ws-item"><div class="ws-val" id="ws-leg-contracts">—</div><div class="ws-lbl">Contracts</div></div>
              <div class="ws-item"><div class="ws-val" id="ws-leg-compliance">—</div><div class="ws-lbl">Compliance</div></div>
            </div>
            <a href="modules/legal/legal.php" class="btn-go btn-go-amber">
              Go to Directory
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
          </div>
        </div>

        <div class="chart-widget">
          <div class="chart-widget-header">
            <div class="widget-title-area">
              <div class="widget-icon" style="background:#EDE9FE">🧑‍💼</div>
              <div>
                <div class="widget-title">Visitor Management</div>
                <div class="widget-subtitle">Daily visitor traffic &amp; check-in/out analytics</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <span class="last-updated" id="updated-vis"></span>
            </div>
          </div>
          <div class="chart-widget-body"><canvas id="chartVisitors"></canvas></div>
          <div class="chart-widget-footer">
            <div class="widget-stats">
              <div class="ws-item"><div class="ws-val" id="ws-vis-total">—</div><div class="ws-lbl">Visitors</div></div>
              <div class="ws-item"><div class="ws-val" id="ws-vis-in">—</div><div class="ws-lbl">Checked In</div></div>
              <div class="ws-item"><div class="ws-val" id="ws-vis-preregs">—</div><div class="ws-lbl">Pre-Regs</div></div>
            </div>
            <a href="modules/visitors/visitors.php" class="btn-go btn-go-purple">
              Go to Directory
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </a>
          </div>
        </div>

      </div>

      <!-- MODULE QUICK-ACCESS CARDS -->
      <div class="modules-grid animate-in delay-3">
        <a href="modules/facilities/facilities.php" class="module-card">
          <div class="module-icon" style="background:#D1FAE5">🏢</div>
          <div class="module-title">Facilities Reservation</div>
          <div class="module-desc">Manage room bookings, venue reservations, equipment inventory, and maintenance requests. QR codes auto-generated.</div>
          <div class="module-stats">
            <div class="module-stat-item"><div class="val" id="mc-fac-avail">—</div><div class="lbl">Available</div></div>
            <div class="module-stat-item"><div class="val" id="mc-fac-pending">—</div><div class="lbl">Pending</div></div>
            <div class="module-stat-item"><div class="val" id="mc-fac-equip">—</div><div class="lbl">Equipment</div></div>
          </div>
        </a>
        <a href="modules/documents/documents.php" class="module-card">
          <div class="module-icon" style="background:#DBEAFE">📄</div>
          <div class="module-title">Document Management</div>
          <div class="module-desc">Archive &amp; organize microfinancial documents with automated OCR text extraction, QR-coded tracking, and version control.</div>
          <div class="module-stats">
            <div class="module-stat-item"><div class="val" id="mc-doc-total">—</div><div class="lbl">Documents</div></div>
            <div class="module-stat-item"><div class="val" id="mc-doc-depts">—</div><div class="lbl">Departments</div></div>
            <div class="module-stat-item"><div class="val" id="mc-doc-ocr">—</div><div class="lbl">OCR Queue</div></div>
          </div>
        </a>
        <a href="modules/legal/legal.php" class="module-card">
          <div class="module-icon" style="background:#FEF3C7">⚖️</div>
          <div class="module-title">Legal Management</div>
          <div class="module-desc">Track legal cases, manage contracts &amp; agreements, monitor compliance with BSP, AMLC, NPC, and other regulatory bodies.</div>
          <div class="module-stats">
            <div class="module-stat-item"><div class="val" id="mc-leg-cases">—</div><div class="lbl">Cases</div></div>
            <div class="module-stat-item"><div class="val" id="mc-leg-contracts">—</div><div class="lbl">Contracts</div></div>
            <div class="module-stat-item"><div class="val" id="mc-leg-compliance">—</div><div class="lbl">Compliance</div></div>
          </div>
        </a>
        <a href="modules/visitors/visitors.php" class="module-card">
          <div class="module-icon" style="background:#EDE9FE">🧑‍💼</div>
          <div class="module-title">Visitor Management</div>
          <div class="module-desc">Register visitors, generate QR passes for quick check-in/out, pre-register expected guests, and view real-time analytics.</div>
          <div class="module-stats">
            <div class="module-stat-item"><div class="val" id="mc-vis-total">—</div><div class="lbl">Visitors</div></div>
            <div class="module-stat-item"><div class="val" id="mc-vis-in">—</div><div class="lbl">Checked In</div></div>
            <div class="module-stat-item"><div class="val" id="mc-vis-preregs">—</div><div class="lbl">Pre-Regs</div></div>
          </div>
        </a>
      </div>

      <!-- DEPARTMENT FOLDERS OVERVIEW -->
      <div class="animate-in delay-3" style="margin-top:4px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
          <div>
            <h2 style="font-size:17px;font-weight:800;color:#1F2937;margin:0">📁 Department Folders</h2>
            <p style="font-size:12px;color:#6B7280;margin:4px 0 0" id="dash-folder-summary">Loading department folders...</p>
          </div>
          <a href="modules/documents/documents.php#tab-folders" class="btn btn-primary btn-sm" style="font-size:12px;padding:6px 14px;text-decoration:none">
            Open All Folders →
          </a>
        </div>
        <div id="dash-dept-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px">
          <div style="grid-column:1/-1;text-align:center;padding:40px;color:#9CA3AF">
            <div style="font-size:32px;margin-bottom:8px">📁</div>
            <div style="font-weight:600">Loading folders...</div>
          </div>
        </div>
      </div>

      <!-- AUDIT LOG TRAIL -->
      <div class="animate-in delay-3" style="margin-top:4px">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
          <div>
            <h2 style="font-size:17px;font-weight:800;color:#1F2937;margin:0">🛡️ Audit Log Trail</h2>
            <p style="font-size:12px;color:#6B7280;margin:4px 0 0" id="dash-audit-summary">Loading audit logs...</p>
          </div>
          <div style="display:flex;gap:8px;align-items:center">
            <select id="audit-module-filter" class="form-input" style="font-size:12px;padding:5px 10px;width:auto;border-radius:8px;border:1px solid #D1D5DB" onchange="refreshAuditLogs()">
              <option value="">All Modules</option>
              <option value="system">🖥️ System</option>
              <option value="facilities">🏢 Facilities</option>
              <option value="documents">📄 Documents</option>
              <option value="legal">⚖️ Legal</option>
              <option value="visitors">🧑‍💼 Visitors</option>
              <option value="departments">🏬 Departments</option>
            </select>
          </div>
        </div>
        <div class="card">
          <div class="card-body" style="padding:0;overflow-x:auto">
            <table class="data-table" style="margin:0">
              <thead>
                <tr>
                  <th style="padding:12px 16px">Timestamp</th>
                  <th style="padding:12px 16px">User</th>
                  <th style="padding:12px 16px">Module</th>
                  <th style="padding:12px 16px">Action</th>
                  <th style="padding:12px 16px">Details</th>
                  <th style="padding:12px 16px">IP Address</th>
                </tr>
              </thead>
              <tbody id="dash-audit-tbody">
                <tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF"><div style="font-size:32px;margin-bottom:8px">🛡️</div><div style="font-weight:600">Loading audit logs...</div></td></tr>
              </tbody>
            </table>
            <div id="dash-audit-pagination"></div>
          </div>
        </div>
      </div>

      <!-- RECENT ACTIVITY & NOTIFICATIONS -->
      <div class="grid-2 animate-in delay-4">
        <div class="card">
          <div class="card-header">
            <span class="card-title">📋 Recent Activity</span>
          </div>
          <div class="card-body">
            <div id="recent-activity-list" style="max-height:320px;overflow-y:auto">
              <div class="empty-state" style="padding:30px"><div style="font-size:36px;margin-bottom:8px">📋</div><div style="font-weight:600">Loading activity...</div></div>
            </div>
          </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:20px">
          <div class="card" style="flex:1">
            <div class="card-header">
              <span class="card-title">🔔 System Overview</span>
            </div>
            <div class="card-body" style="padding:12px 16px">
              <div id="system-overview-list">
                <div class="empty-state" style="padding:20px"><div style="font-weight:600">Loading...</div></div>
              </div>
            </div>
          </div>

          <div class="qr-card">
            <div class="qr-placeholder">📱</div>
            <div class="qr-label">Scan QR to access Microfinancial Admin</div>
            <button class="btn btn-primary btn-sm" style="margin-top:12px" onclick="this.previousElementSibling.textContent='QR Generated!'; this.parentElement.querySelector('.qr-placeholder').innerHTML='<img src=\'https://api.qrserver.com/v1/create-qr-code/?data=MICROFINANCIAL-ADMIN-2026&size=120x120&color=059669\' alt=\'QR\' style=\'width:120px;border-radius:8px\'>'">
              ⚡ Generate System QR
            </button>
          </div>
        </div>
      </div>

    </main>
  </div>

<script src="admin.js?v=20260304"></script>
<script src="export.js?v=20260304"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  if (typeof Chart !== 'undefined') {
    Chart.defaults.font.family = "'Inter','Segoe UI',system-ui,sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.padding = 16;
    Chart.defaults.plugins.legend.labels.boxWidth = 8;
  }

  // ─── Shared state ───
  let facStats = {}, docStats = {}, legStats = {}, visStats = {};
  let facReservations = [], docList = [], legCases = [], visLogs = [];

  // ─── Chart instances (for real-time updates) ───
  let chartFac = null, chartDoc = null, chartLeg = null, chartVis = null;

  const REFRESH_INTERVAL = 30000; // 30 seconds

  // ─── Department Folders Config (mirrors documents.php deptConfig) ───
  const dashDeptConfig = {
    'HR 1':           { icon: '👥', folder: 'Talent Acquisition & Workforce Entry',        color: '#059669', bg: '#D1FAE5' },
    'HR 2':           { icon: '📝', folder: 'Talent Development & Career Pathing',          color: '#2563EB', bg: '#DBEAFE' },
    'HR 3':           { icon: '🎓', folder: 'Workforce Operations & Time Management',       color: '#7C3AED', bg: '#EDE9FE' },
    'HR 4':           { icon: '📋', folder: 'Compensation & HR Intelligence',               color: '#DC2626', bg: '#FEE2E2' },
    'Core 1':         { icon: '🏦', folder: 'Client Services & Financial Transactions',     color: '#D97706', bg: '#FEF3C7' },
    'Core 2':         { icon: '📊', folder: 'Institutional Oversight & Financial Control',   color: '#059669', bg: '#D1FAE5' },
    'Log 1':          { icon: '🚚', folder: 'Smart Supply Chain & Procurement Management',   color: '#0891B2', bg: '#CFFAFE' },
    'Log 2':          { icon: '📦', folder: 'Fleet and Transportation Operations',           color: '#9333EA', bg: '#F3E8FF' },
    'Financial':      { icon: '💵', folder: 'Financial Management',                         color: '#16A34A', bg: '#DCFCE7' },
    'Administrative': { icon: '⚖️', folder: 'Administrative Services',                      color: '#B91C1C', bg: '#FEE2E2' },
  };

  async function refreshDeptFolders() {
    try {
      const res = await safeFetch('api/documents.php?action=list_folders');
      const folders = (res && res.data) ? res.data : [];

      // Build counts per department from API data
      const deptCounts = {};
      folders.forEach(f => {
        const dept = f.department || 'Other';
        if (!deptCounts[dept]) deptCounts[dept] = { total: 0, active: 0, archived: 0, retained: 0 };
        deptCounts[dept].total    += parseInt(f.doc_count) || 0;
        deptCounts[dept].active   += parseInt(f.active_count) || 0;
        deptCounts[dept].archived += parseInt(f.archived_count) || 0;
        deptCounts[dept].retained += parseInt(f.retained_count) || 0;
      });

      // Merge: show ALL configured depts + any extra from data
      const allDepts = new Set([...Object.keys(dashDeptConfig), ...Object.keys(deptCounts)]);
      const grid = document.getElementById('dash-dept-grid');
      const totalDocs = Object.values(deptCounts).reduce((s, c) => s + c.total, 0);
      setText('dash-folder-summary', allDepts.size + ' department folders · ' + totalDocs + ' documents');

      grid.innerHTML = '';
      allDepts.forEach(deptId => {
        const cfg = dashDeptConfig[deptId] || { icon: '📁', folder: deptId, color: '#6B7280', bg: '#F3F4F6' };
        const counts = deptCounts[deptId] || { total: 0, active: 0, archived: 0, retained: 0 };

        const card = document.createElement('a');
        card.href = 'modules/documents/documents.php#tab-folders';
        card.style.cssText = 'display:block;background:#fff;border-radius:14px;border:2px solid transparent;padding:16px;text-decoration:none;transition:all 0.2s;box-shadow:0 1px 3px rgba(0,0,0,0.04);';
        card.onmouseover = function() { this.style.borderColor = cfg.color; this.style.boxShadow = '0 6px 20px rgba(0,0,0,0.08)'; this.style.transform = 'translateY(-2px)'; };
        card.onmouseout  = function() { this.style.borderColor = 'transparent'; this.style.boxShadow = '0 1px 3px rgba(0,0,0,0.04)'; this.style.transform = ''; };

        const badges = [];
        if (counts.active)   badges.push('<span style="background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px">🟢 ' + counts.active + ' Active</span>');
        if (counts.archived) badges.push('<span style="background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px">📦 ' + counts.archived + ' Archived</span>');
        if (counts.retained) badges.push('<span style="background:#EDE9FE;color:#5B21B6;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px">🔒 ' + counts.retained + ' Retained</span>');
        if (counts.total === 0) badges.push('<span style="background:#F3F4F6;color:#9CA3AF;padding:2px 8px;border-radius:6px;font-weight:600;font-size:11px">Empty</span>');

        card.innerHTML = `
          <div style="display:flex;align-items:flex-start;gap:14px">
            <div style="width:48px;height:48px;border-radius:12px;background:${cfg.bg};display:flex;align-items:center;justify-content:center;font-size:24px;flex-shrink:0">
              ${cfg.icon}
            </div>
            <div style="flex:1;min-width:0">
              <div style="display:flex;align-items:center;gap:6px">
                <span style="font-size:14px;font-weight:800;color:#1F2937">${esc(deptId)}</span>
                <span style="font-size:13px;color:#D97706">🔒</span>
              </div>
              <div style="font-size:11px;color:#6B7280;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">📂 ${esc(cfg.folder)}</div>
              <div style="font-size:12px;font-weight:700;color:${cfg.color};margin-top:4px">${counts.total} document${counts.total !== 1 ? 's' : ''}</div>
            </div>
          </div>
          <div style="display:flex;gap:6px;margin-top:10px;flex-wrap:wrap">${badges.join('')}</div>
        `;
        grid.appendChild(card);
      });
    } catch (e) { console.error('Dept folders refresh:', e); }
  }

  // ─── Audit Log Trail ───
  const AUDIT_MODULE_STYLES = {
    system:      { icon: '🖥️', color: '#6B7280', bg: '#F3F4F6', label: 'System' },
    facilities:  { icon: '🏢', color: '#059669', bg: '#D1FAE5', label: 'Facilities' },
    documents:   { icon: '📄', color: '#2563EB', bg: '#DBEAFE', label: 'Documents' },
    legal:       { icon: '⚖️', color: '#D97706', bg: '#FEF3C7', label: 'Legal' },
    visitors:    { icon: '🧑‍💼', color: '#7C3AED', bg: '#EDE9FE', label: 'Visitors' },
    departments: { icon: '🏬', color: '#0891B2', bg: '#CFFAFE', label: 'Departments' }
  };

  Paginator.create('dash-audit', { perPage: 10, onPageChange: function() { renderAuditRows(); } });
  let _auditLogs = [];

  async function refreshAuditLogs() {
    try {
      const modFilter = document.getElementById('audit-module-filter')?.value || '';
      const url = 'api/audit.php?action=recent&limit=100' + (modFilter ? '&module=' + encodeURIComponent(modFilter) : '');
      const res = await safeFetch(url);
      _auditLogs = (res && res.data) ? res.data : [];
      Paginator.reset('dash-audit');
      renderAuditRows();
    } catch (e) { console.error('Audit logs refresh:', e); }
  }

  function renderAuditRows() {
    const tbody = document.getElementById('dash-audit-tbody');
    if (!_auditLogs.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF"><div style="font-size:32px;margin-bottom:8px">🛡️</div><div style="font-weight:600">No audit logs found</div></td></tr>';
      document.getElementById('dash-audit-pagination').innerHTML = '';
      setText('dash-audit-summary', '0 entries');
      return;
    }
    setText('dash-audit-summary', _auditLogs.length + ' entries loaded');

    var allRows = _auditLogs.map(function(log) {
      var ms = AUDIT_MODULE_STYLES[log.module] || AUDIT_MODULE_STYLES.system;
      var userName = (log.first_name && log.last_name) ? esc(log.first_name + ' ' + log.last_name) : '<span style="color:#9CA3AF">System</span>';
      var roleBadge = log.role ? '<span style="font-size:10px;color:#6B7280;display:block;margin-top:1px">' + esc(log.role) + '</span>' : '';
      var actionLabel = (log.action || '').replace(/_/g, ' ');
      var details = '';
      if (log.new_values) {
        try {
          var nv = JSON.parse(log.new_values);
          var keys = Object.keys(nv).slice(0, 3);
          details = keys.map(function(k) { return '<span style="font-size:11px;color:#4B5563">' + esc(k) + ': <b>' + esc(String(nv[k]).substring(0, 30)) + '</b></span>'; }).join('<br>');
        } catch(e) { details = '<span style="font-size:11px;color:#9CA3AF">—</span>'; }
      } else {
        details = '<span style="font-size:11px;color:#9CA3AF">—</span>';
      }
      var ts = log.created_at ? new Date(log.created_at) : null;
      var timeStr = ts ? ts.toLocaleDateString('en-US', {month:'short',day:'numeric',year:'numeric'}) + '<br><span style="font-size:11px;color:#6B7280">' + ts.toLocaleTimeString('en-US', {hour:'numeric',minute:'2-digit',second:'2-digit',hour12:true}) + '</span>' : '—';

      return '<tr>' +
        '<td style="padding:10px 16px;font-size:12px;white-space:nowrap">' + timeStr + '</td>' +
        '<td style="padding:10px 16px;font-size:13px">' + userName + roleBadge + '</td>' +
        '<td style="padding:10px 16px"><span style="display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:' + ms.bg + ';color:' + ms.color + '">' + ms.icon + ' ' + ms.label + '</span></td>' +
        '<td style="padding:10px 16px"><span style="font-size:12px;font-weight:600;color:#1F2937">' + esc(actionLabel) + '</span>' + (log.table_name ? '<br><span style="font-size:11px;color:#9CA3AF">' + esc(log.table_name) + (log.record_id ? ' #' + log.record_id : '') + '</span>' : '') + '</td>' +
        '<td style="padding:10px 16px;max-width:220px">' + details + '</td>' +
        '<td style="padding:10px 16px;font-size:11px;color:#6B7280;font-family:monospace">' + esc(log.ip_address || '—') + '</td>' +
        '</tr>';
    });

    var paged = Paginator.paginate('dash-audit', allRows);
    tbody.innerHTML = paged.join('');
    Paginator.renderControls('dash-audit', 'dash-audit-pagination');
  }

  function setText(id, val) {
    const el = document.getElementById(id);
    if (el) el.textContent = val;
  }
  function esc(s) { const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
  function fmtDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }); }
  function fmtTime(d) { return new Date(d).toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', second:'2-digit', hour12:true }); }

  const statusClass = s => {
    s = (s || '').toLowerCase();
    if (['approved','active','checked in','compliant'].includes(s)) return 'badge-green';
    if (['pending','open','in_progress'].includes(s)) return 'badge-amber';
    if (['cancelled','rejected','non_compliant'].includes(s)) return 'badge-red';
    return 'badge-gray';
  };

  function setLiveBadge(id, refreshing) {
    const el = document.getElementById(id);
    if (el) { if (refreshing) el.classList.add('refreshing'); else el.classList.remove('refreshing'); }
  }
  function setUpdatedTime(id) {
    setText(id, fmtTime(new Date()));
  }

  // ─── Progressive activity & overview updates ───
  function rebuildRecentActivity() {
    const activities = [];
    facReservations.slice(0, 5).forEach(r => {
      activities.push({ text: (r.title || r.facility_name || 'Reservation') + ' reservation', module: 'Facilities', badge: 'badge-green', status: r.status || 'pending', time: r.created_at || r.start_datetime });
    });
    docList.slice(0, 3).forEach(d => {
      activities.push({ text: (d.title || d.document_name || 'Document') + ' uploaded', module: 'Documents', badge: 'badge-blue', status: 'active', time: d.created_at || d.upload_date });
    });
    legCases.slice(0, 3).forEach(c => {
      activities.push({ text: (c.case_number || c.title || 'Case') + ' ' + (c.status || 'open'), module: 'Legal', badge: 'badge-amber', status: c.status || 'open', time: c.created_at || c.filed_date });
    });
    visLogs.slice(0, 3).forEach(l => {
      activities.push({ text: (l.visitor_name || 'Visitor') + ' ' + (l.status === 'checked_in' ? 'checked in' : 'checked out'), module: 'Visitors', badge: 'badge-purple', status: l.status === 'checked_in' ? 'checked in' : 'checked out', time: l.check_in_time });
    });
    activities.sort((a, b) => new Date(b.time || 0) - new Date(a.time || 0));
    const actDiv = document.getElementById('recent-activity-list');
    if (activities.length === 0) {
      actDiv.innerHTML = '<div class="empty-state" style="padding:30px"><div style="font-size:36px;margin-bottom:8px">📋</div><div style="font-weight:600">No recent activity</div></div>';
    } else {
      actDiv.innerHTML = activities.slice(0, 10).map(a => `
        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #F3F4F6">
          <div style="flex:1;min-width:0">
            <div style="font-weight:600;color:#1F2937;font-size:13px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${esc(a.text)}</div>
            <div style="display:flex;gap:6px;margin-top:4px">
              <span class="badge ${a.badge}" style="font-size:10px">${a.module}</span>
              <span class="badge ${statusClass(a.status)}" style="font-size:10px">${a.status}</span>
            </div>
          </div>
          <div style="font-size:11px;color:#9CA3AF;white-space:nowrap;margin-left:12px">${fmtDate(a.time)}</div>
        </div>
      `).join('');
    }
  }

  function rebuildSystemOverview() {
    const overviewDiv = document.getElementById('system-overview-list');
    const overviewItems = [
      { icon: '🏢', label: 'Facilities available', value: facStats.available_facilities ?? 0, color: '#059669' },
      { icon: '🔧', label: 'Open maintenance', value: facStats.open_maintenance ?? 0, color: facStats.open_maintenance > 0 ? '#D97706' : '#059669' },
      { icon: '📄', label: 'Pending OCR', value: docStats.pending_ocr ?? 0, color: docStats.pending_ocr > 0 ? '#D97706' : '#059669' },
      { icon: '⚖️', label: 'Active legal cases', value: legStats.active_cases ?? 0, color: legStats.active_cases > 0 ? '#D97706' : '#059669' },
      { icon: '🧑‍💼', label: 'Visitors checked in now', value: visStats.checked_in_now ?? 0, color: '#3B82F6' },
      { icon: '📅', label: "Today's visits", value: visStats.today_visits ?? 0, color: '#8B5CF6' }
    ];
    overviewDiv.innerHTML = overviewItems.map(oi => `
      <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #F3F4F6;font-size:13px">
        <span>${oi.icon} <span style="color:#4B5563">${oi.label}</span></span>
        <span style="font-weight:700;color:${oi.color}">${oi.value}</span>
      </div>
    `).join('');
  }

  // ─── Chart config factories (reusable) ───
  const facChartOpts = {
    responsive: true, maintainAspectRatio: false,
    animation: { duration: 800, easing: 'easeInOutQuart' },
    plugins: { legend: { position: 'top', align: 'end' }, tooltip: { backgroundColor: '#1F2937', cornerRadius: 8, padding: 10 }},
    scales: { x: { grid: { display: false }, border: { display: false }}, y: { beginAtZero: true, grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { stepSize: 5 }}}
  };
  const docChartOpts = {
    responsive: true, maintainAspectRatio: false, cutout: '62%',
    animation: { duration: 800, easing: 'easeInOutQuart' },
    plugins: { legend: { position: 'right', labels: { padding: 12, font: { size: 11 }}}, tooltip: { backgroundColor: '#1F2937', cornerRadius: 8, padding: 10 }}
  };
  const legChartOpts = {
    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
    animation: { duration: 800, easing: 'easeInOutQuart' },
    plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1F2937', cornerRadius: 8, padding: 10 }},
    scales: { x: { beginAtZero: true, grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { stepSize: 1 }}, y: { grid: { display: false }, border: { display: false }}}
  };
  const visChartOpts = {
    responsive: true, maintainAspectRatio: false,
    animation: { duration: 800, easing: 'easeInOutQuart' },
    plugins: { legend: { position: 'top', align: 'end' }, tooltip: { backgroundColor: '#1F2937', cornerRadius: 8, padding: 10, mode: 'index', intersect: false }},
    interaction: { mode: 'index', intersect: false },
    scales: { x: { grid: { display: false }, border: { display: false }}, y: { beginAtZero: true, grid: { color: '#F3F4F6' }, border: { display: false }, ticks: { stepSize: 2 }}}
  };

  // ─── Data computation helpers ───
  function computeFacChartData() {
    const now = new Date();
    const monthLabels = [], approvedData = [], pendingData = [], cancelledData = [];
    for (let i = 5; i >= 0; i--) {
      const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
      const m = d.getMonth(), y = d.getFullYear();
      monthLabels.push(d.toLocaleDateString('en-US', { month: 'short' }));
      approvedData.push(facReservations.filter(r => { const rd = new Date(r.start_datetime || r.created_at); return rd.getMonth() === m && rd.getFullYear() === y && r.status === 'approved'; }).length);
      pendingData.push(facReservations.filter(r => { const rd = new Date(r.start_datetime || r.created_at); return rd.getMonth() === m && rd.getFullYear() === y && r.status === 'pending'; }).length);
      cancelledData.push(facReservations.filter(r => { const rd = new Date(r.start_datetime || r.created_at); return rd.getMonth() === m && rd.getFullYear() === y && r.status === 'cancelled'; }).length);
    }
    return { labels: monthLabels, datasets: [
      { label: 'Approved', data: approvedData, backgroundColor: '#059669', borderRadius: 6, barPercentage: 0.6 },
      { label: 'Pending', data: pendingData, backgroundColor: '#FCD34D', borderRadius: 6, barPercentage: 0.6 },
      { label: 'Cancelled', data: cancelledData, backgroundColor: '#FCA5A5', borderRadius: 6, barPercentage: 0.6 }
    ]};
  }

  function computeDocChartData() {
    const catCounts = {};
    docList.forEach(d => { const cat = d.department || d.category || 'Other'; catCounts[cat] = (catCounts[cat] || 0) + 1; });
    const docLabels = Object.keys(catCounts), docData = Object.values(catCounts);
    const docColors = ['#059669','#3B82F6','#F59E0B','#14B8A6','#8B5CF6','#6B7280','#DC2626','#EC4899'];
    return { labels: docLabels.length ? docLabels : ['No Data'],
      datasets: [{ data: docData.length ? docData : [1], backgroundColor: docData.length ? docLabels.map((_,i) => docColors[i % docColors.length]) : ['#E5E7EB'], borderWidth: 3, borderColor: '#fff', hoverOffset: 8 }]
    };
  }

  function computeLegChartData() {
    const legLabels = ['Open Cases','Active Cases','Active Contracts','Compliant','Non-Compliant'];
    const legData = [
      (legStats.total_cases ?? 0) - (legStats.active_cases ?? 0), legStats.active_cases ?? 0,
      legStats.active_contracts ?? 0, (legStats.compliance_items ?? 0) - (legStats.non_compliant ?? 0), legStats.non_compliant ?? 0
    ];
    return { labels: legLabels, datasets: [{ label: 'Count', data: legData, backgroundColor: ['#F59E0B','#3B82F6','#059669','#14B8A6','#EF4444'], borderRadius: 6, barPercentage: 0.7 }] };
  }

  function computeVisChartData() {
    const dayLabels = [], checkIns = [], checkOuts = [];
    for (let i = 6; i >= 0; i--) {
      const d = new Date(); d.setDate(d.getDate() - i);
      const ds = d.toISOString().slice(0, 10);
      dayLabels.push(d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }));
      checkIns.push(visLogs.filter(l => (l.check_in_time || '').slice(0, 10) === ds).length);
      checkOuts.push(visLogs.filter(l => l.check_out_time && l.check_out_time.slice(0, 10) === ds).length);
    }
    return { labels: dayLabels, datasets: [
      { label: 'Check-Ins', data: checkIns, borderColor: '#8B5CF6', backgroundColor: 'rgba(139,92,246,0.08)', fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#8B5CF6', pointBorderColor: '#fff', pointBorderWidth: 2, borderWidth: 2.5 },
      { label: 'Check-Outs', data: checkOuts, borderColor: '#059669', backgroundColor: 'rgba(5,150,105,0.06)', fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#059669', pointBorderColor: '#fff', pointBorderWidth: 2, borderWidth: 2.5 }
    ]};
  }

  // ─── Update chart data smoothly (animate transitions) ───
  function updateChartData(chart, newData) {
    if (!chart) return;
    chart.data.labels = newData.labels;
    newData.datasets.forEach((ds, i) => {
      if (chart.data.datasets[i]) {
        Object.assign(chart.data.datasets[i], ds);
      } else {
        chart.data.datasets.push(ds);
      }
    });
    // Remove extra datasets if new data has fewer
    while (chart.data.datasets.length > newData.datasets.length) chart.data.datasets.pop();
    chart.update('active');
  }

  // ─── Populate stat card values for a module ───
  function populateFacStats() {
    setText('dash-total-facilities', facStats.total_facilities ?? 0);
    setText('dash-avail-fac', facStats.available_facilities ?? 0);
    setText('dash-pending-res', facStats.pending_reservations ?? 0);
    setText('ws-fac-avail', facStats.available_facilities ?? 0);
    setText('ws-fac-pending', facStats.pending_reservations ?? 0);
    setText('ws-fac-today', facStats.today_reservations ?? 0);
    setText('mc-fac-avail', facStats.available_facilities ?? 0);
    setText('mc-fac-pending', facStats.pending_reservations ?? 0);
    setText('mc-fac-equip', facStats.total_equipment ?? 0);
  }
  function populateDocStats() {
    setText('dash-active-docs', docStats.active_documents ?? 0);
    setText('dash-archived-docs', docStats.archived_documents ?? 0);
    setText('dash-ocr-queue', docStats.pending_ocr ?? 0);
    setText('ws-doc-total', docStats.total_documents ?? 0);
    setText('ws-doc-depts', docStats.departments ?? 0);
    setText('ws-doc-ocr', docStats.pending_ocr ?? 0);
    setText('mc-doc-total', docStats.total_documents ?? 0);
    setText('mc-doc-depts', docStats.departments ?? 0);
    setText('mc-doc-ocr', docStats.pending_ocr ?? 0);
  }
  function populateLegStats() {
    setText('dash-legal-cases', legStats.total_cases ?? 0);
    setText('dash-active-contracts', legStats.active_contracts ?? 0);
    setText('dash-compliance', legStats.compliance_items ?? 0);
    setText('ws-leg-cases', legStats.total_cases ?? 0);
    setText('ws-leg-contracts', legStats.total_contracts ?? 0);
    setText('ws-leg-compliance', legStats.compliance_items ?? 0);
    setText('mc-leg-cases', legStats.total_cases ?? 0);
    setText('mc-leg-contracts', legStats.total_contracts ?? 0);
    setText('mc-leg-compliance', legStats.compliance_items ?? 0);
  }
  function populateVisStats() {
    setText('dash-visitors', visStats.total_visitors ?? 0);
    setText('dash-checked-in', visStats.checked_in_now ?? 0);
    setText('dash-preregs', visStats.pending_preregs ?? 0);
    setText('ws-vis-total', visStats.total_visitors ?? 0);
    setText('ws-vis-in', visStats.checked_in_now ?? 0);
    setText('ws-vis-preregs', visStats.pending_preregs ?? 0);
    setText('mc-vis-total', visStats.total_visitors ?? 0);
    setText('mc-vis-in', visStats.checked_in_now ?? 0);
    setText('mc-vis-preregs', visStats.pending_preregs ?? 0);
  }

  // ═══════════════════════════════════════════════════════
  // INITIAL LOAD + REAL-TIME REFRESH PER MODULE
  // ═══════════════════════════════════════════════════════

  // Helper: safe fetch that returns parsed JSON or null on any error
  async function safeFetch(url) {
    try {
      const r = await fetch(url, { credentials: 'same-origin' });
      if (!r.ok) { console.warn('Dashboard fetch ' + url + ' returned HTTP ' + r.status); return null; }
      return await r.json();
    } catch(e) { console.warn('Dashboard fetch failed: ' + url, e); return null; }
  }

  async function refreshFacilities() {
    try {
      const [stats, res] = await Promise.all([
        safeFetch('api/facilities.php?action=dashboard_stats'),
        safeFetch('api/facilities.php?action=list_reservations')
      ]);
      if (stats) { facStats = stats; }
      if (res) { facReservations = res.data || []; }
      populateFacStats();
      const data = computeFacChartData();
      if (typeof Chart !== 'undefined') {
        if (!chartFac) {
          chartFac = new Chart(document.getElementById('chartFacilities'), { type: 'bar', data, options: facChartOpts });
        } else {
          updateChartData(chartFac, data);
        }
      }
      rebuildRecentActivity();
      rebuildSystemOverview();
      setUpdatedTime('updated-fac');
    } catch(e) { console.error('Facilities refresh error:', e); }
  }

  async function refreshDocuments() {
    try {
      const [stats, list] = await Promise.all([
        safeFetch('api/documents.php?action=dashboard_stats'),
        safeFetch('api/documents.php?action=list_documents')
      ]);
      if (stats) { docStats = stats; }
      if (list) { docList = list.data || []; }
      populateDocStats();
      const data = computeDocChartData();
      if (typeof Chart !== 'undefined') {
        if (!chartDoc) {
          chartDoc = new Chart(document.getElementById('chartDocuments'), { type: 'doughnut', data, options: docChartOpts });
        } else {
          updateChartData(chartDoc, data);
        }
      }
      rebuildRecentActivity();
      rebuildSystemOverview();
      setUpdatedTime('updated-doc');
    } catch(e) { console.error('Documents refresh error:', e); }
  }

  async function refreshLegal() {
    try {
      const [stats, cases] = await Promise.all([
        safeFetch('api/legal.php?action=dashboard_stats'),
        safeFetch('api/legal.php?action=list_cases')
      ]);
      if (stats) { legStats = stats; }
      if (cases) { legCases = cases.data || []; }
      populateLegStats();
      const data = computeLegChartData();
      if (typeof Chart !== 'undefined') {
        if (!chartLeg) {
          chartLeg = new Chart(document.getElementById('chartLegal'), { type: 'bar', data, options: legChartOpts });
        } else {
          updateChartData(chartLeg, data);
        }
      }
      rebuildRecentActivity();
      rebuildSystemOverview();
      setUpdatedTime('updated-leg');
    } catch(e) { console.error('Legal refresh error:', e); }
  }

  async function refreshVisitors() {
    try {
      const [stats, logRes] = await Promise.all([
        safeFetch('api/visitors.php?action=dashboard_stats'),
        safeFetch('api/visitors.php?action=list_logs')
      ]);
      if (stats) { visStats = stats; }
      if (logRes) { visLogs = logRes.data || []; }
      populateVisStats();
      const data = computeVisChartData();
      if (typeof Chart !== 'undefined') {
        if (!chartVis) {
          chartVis = new Chart(document.getElementById('chartVisitors'), { type: 'line', data, options: visChartOpts });
        } else {
          updateChartData(chartVis, data);
        }
      }
      rebuildRecentActivity();
      rebuildSystemOverview();
      setUpdatedTime('updated-vis');
    } catch(e) { console.error('Visitors refresh error:', e); }
  }

  // ─── Initial load (all fire independently) ───
  refreshFacilities();
  refreshDocuments();
  refreshLegal();
  refreshVisitors();
  refreshDeptFolders();
  refreshAuditLogs();

  // ─── Auto-refresh every 30 seconds ───
  setInterval(() => {
    refreshFacilities();
    refreshDocuments();
    refreshLegal();
    refreshVisitors();
    refreshDeptFolders();
    refreshAuditLogs();
  }, REFRESH_INTERVAL);

  // ─── Export Dashboard function (used by header buttons) ───
  window.exportDashboard = function(format) {
    if (format === 'csv') {
      const headers = ['Module', 'Metric', 'Value'];
      const rows = [
        ['Facilities', 'Total Facilities', facStats.total_facilities ?? 0],
        ['Facilities', 'Available', facStats.available_facilities ?? 0],
        ['Facilities', 'Pending Reservations', facStats.pending_reservations ?? 0],
        ['Facilities', 'Today Reservations', facStats.today_reservations ?? 0],
        ['Facilities', 'Total Equipment', facStats.total_equipment ?? 0],
        ['Documents', 'Active Documents', docStats.active_documents ?? 0],
        ['Documents', 'Total Documents', docStats.total_documents ?? 0],
        ['Documents', 'Archived', docStats.archived_documents ?? 0],
        ['Documents', 'Pending OCR', docStats.pending_ocr ?? 0],
        ['Documents', 'Departments', docStats.departments ?? 0],
        ['Legal', 'Total Cases', legStats.total_cases ?? 0],
        ['Legal', 'Active Cases', legStats.active_cases ?? 0],
        ['Legal', 'Active Contracts', legStats.active_contracts ?? 0],
        ['Legal', 'Compliance Items', legStats.compliance_items ?? 0],
        ['Legal', 'Non-Compliant', legStats.non_compliant ?? 0],
        ['Visitors', 'Total Visitors', visStats.total_visitors ?? 0],
        ['Visitors', 'Checked In Now', visStats.checked_in_now ?? 0],
        ['Visitors', 'Pending Pre-Regs', visStats.pending_preregs ?? 0],
        ['Visitors', 'Today Visits', visStats.today_visits ?? 0],
      ];
      ExportHelper.exportCSV('Dashboard_Report', headers, rows);
    } else {
      ExportHelper.exportDashboardPDF('Dashboard_Report', 'Administrative Dashboard Report', [
        {
          type: 'stats', title: 'System Overview',
          items: [
            { label: 'Total Facilities', value: facStats.total_facilities ?? 0 },
            { label: 'Active Documents', value: docStats.active_documents ?? 0 },
            { label: 'Legal Cases', value: legStats.total_cases ?? 0 },
            { label: 'Registered Visitors', value: visStats.total_visitors ?? 0 },
            { label: 'Available Facilities', value: facStats.available_facilities ?? 0 },
            { label: 'Pending Reservations', value: facStats.pending_reservations ?? 0 },
            { label: 'Archived Docs', value: docStats.archived_documents ?? 0 },
            { label: 'Checked In Now', value: visStats.checked_in_now ?? 0 },
          ]
        },
        { type: 'chart', title: 'Facilities Reservation Trends', canvasId: 'chartFacilities' },
        { type: 'chart', title: 'Document Distribution', canvasId: 'chartDocuments' },
        { type: 'chart', title: 'Legal Management Overview', canvasId: 'chartLegal' },
        { type: 'chart', title: 'Visitor Traffic (Last 7 Days)', canvasId: 'chartVisitors' },
        {
          type: 'table', title: 'Recent Reservations',
          headers: ['Facility', 'Event', 'Status', 'Date'],
          rows: facReservations.slice(0, 10).map(r => [r.facility_name || '', r.title || r.purpose || '', r.status || '', r.start_datetime || ''])
        },
        {
          type: 'table', title: 'Recent Documents',
          headers: ['Title', 'Department', 'Type', 'Lifecycle', 'Date Filed'],
          rows: docList.slice(0, 10).map(d => [d.title || d.document_name || '', d.department || '', d.document_type || '', d.lifecycle_status || '', d.created_at || d.upload_date || ''])
        },
        {
          type: 'table', title: 'Legal Cases',
          headers: ['Case #', 'Title', 'Type', 'Status'],
          rows: legCases.slice(0, 10).map(c => [c.case_number || '', c.title || '', c.case_type || '', c.status || ''])
        },
        {
          type: 'table', title: 'Recent Visitor Logs',
          headers: ['Visitor', 'Purpose', 'Check-In', 'Status'],
          rows: visLogs.slice(0, 10).map(l => [l.visitor_name || '', l.purpose || '', l.check_in_time || '', l.status || ''])
        }
      ]);
    }
  };
});
</script>
</body>
</html>
