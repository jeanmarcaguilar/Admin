<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Legal Management — Microfinancial Admin</title>

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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <script src="../../hr2-integration.js"></script>
  <script src="../../hr4-integration.js"></script>
  <style>
    .secure-text { letter-spacing: 2px; font-family: monospace; }
    .doc-secured .doc-title-text { filter: blur(5px); user-select: none; transition: filter 0.3s; }
    .doc-secured.revealed .doc-title-text { filter: none; user-select: auto; }
    .eye-toggle { cursor: pointer; opacity: 0.5; transition: opacity 0.2s; }
    .eye-toggle:hover { opacity: 1; }
    .contract-paper { background: #FFFEF7; border: 1px solid #E5E7EB; border-radius: 12px; padding: 32px; font-family: 'Georgia', serif; line-height: 1.8; white-space: pre-wrap; max-height: 500px; overflow-y: auto; }
    .contract-paper .signature-block { margin-top: 24px; padding-top: 16px; border-top: 1px dashed #D1D5DB; }
    .attorney-seal { display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #F0FDF4, #D1FAE5); border: 2px solid #059669; border-radius: 10px; padding: 8px 16px; font-size: 12px; font-weight: 600; color: #065F46; }
  </style>
</head>

<body class="bg-brand-background-main min-h-screen font-[Inter,'Segoe_UI',system-ui,-apple-system,sans-serif]">

  <?php $activePage = 'legal'; $baseUrl = '../../'; include '../../sidebar.php'; ?>

  <!-- MAIN WRAPPER -->
  <div class="md:pl-72">
    <!-- HEADER -->
    <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
      <div class="hidden md:block absolute left-0 top-0 h-16 w-[2px] bg-white"></div>
      <div class="flex items-center gap-3">
        <button id="mobile-menu-btn" class="md:hidden w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center">☰</button>
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
        <h1 class="page-title">Legal Management</h1>
        <p class="page-subtitle">Loan documentation & disclosure, case tracking & recovery, contracts, permits & legal calendar</p>
      </div>

      <!-- SUBMODULE DIRECTORY -->
      <div class="animate-in delay-1">
        <div class="module-directory-label">Submodule Directory</div>
        <div class="stats-grid" style="margin-bottom:18px">
          <a href="#tab-loans" onclick="showSection('#tab-loans')" class="stat-card stat-card-link">
            <div class="stat-icon green">📄</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-loans">0</div>
              <div class="stat-label">Loan Documentation</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-cases" onclick="showSection('#tab-cases')" class="stat-card stat-card-link">
            <div class="stat-icon amber">⚖️</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-cases">0</div>
              <div class="stat-label">Case Tracking</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-contracts" onclick="showSection('#tab-contracts')" class="stat-card stat-card-link">
            <div class="stat-icon green">📝</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-contracts">0</div>
              <div class="stat-label">Contracts</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-permits" onclick="showSection('#tab-permits')" class="stat-card stat-card-link">
            <div class="stat-icon amber">🪪</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-permits">0</div>
              <div class="stat-label">Permits & Licenses</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-legal-calendar" onclick="showSection('#tab-legal-calendar')" class="stat-card stat-card-link">
            <div class="stat-icon blue">📅</div>
            <div class="stat-info">
              <div class="stat-value">—</div>
              <div class="stat-label">Legal Calendar</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
        </div>
      </div>


      <!-- Legal Security PIN Gate (shared for all submodules) -->
      <div id="legal-pin-gate" style="display:none">
        <div class="card" style="max-width:520px;margin:40px auto;border:2px solid #FDE68A">
          <div class="card-body" style="padding:40px 32px;text-align:center">
            <div style="width:72px;height:72px;margin:0 auto 20px;background:#FEF3C7;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px">🔐</div>
            <h2 style="font-size:22px;font-weight:800;color:#1F2937;margin:0 0 8px">Legal Security Verification</h2>
            <p style="font-size:14px;color:#6B7280;margin:0 0 24px;line-height:1.6">A 4-digit security PIN will be sent to your email. Enter it below to access <strong id="lpin-tab-label" style="color:#D97706">this legal module</strong>.</p>

            <!-- Step 1: Request PIN -->
            <div id="lpin-step-request">
              <button class="btn btn-primary" onclick="requestLegalPin()" style="font-size:14px;padding:12px 28px;background:#D97706;border-color:#D97706">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Send Security PIN
              </button>
            </div>

            <!-- Step 2: Enter PIN -->
            <div id="lpin-step-verify" style="display:none">
              <div style="margin-bottom:16px;font-size:13px;color:#D97706;font-weight:600">✅ PIN sent to your email!</div>
              <div id="lpin-fallback-display" style="display:none;margin-bottom:16px;background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;padding:12px 16px">
                <div style="font-size:12px;color:#92400E;font-weight:600;margin-bottom:6px">⚠️ Email could not be sent. Use this PIN:</div>
                <div id="lpin-fallback-code" style="font-size:28px;font-weight:800;color:#D97706;letter-spacing:8px"></div>
              </div>
              <div style="display:flex;gap:8px;justify-content:center;margin-bottom:16px">
                <input type="text" id="lpin-digit-1" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#D97706;border:2px solid #FDE68A;border-radius:12px" oninput="lpinAutoFocus(this,2)" onkeydown="lpinKeyNav(event,1)">
                <input type="text" id="lpin-digit-2" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#D97706;border:2px solid #FDE68A;border-radius:12px" oninput="lpinAutoFocus(this,3)" onkeydown="lpinKeyNav(event,2)">
                <input type="text" id="lpin-digit-3" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#D97706;border:2px solid #FDE68A;border-radius:12px" oninput="lpinAutoFocus(this,4)" onkeydown="lpinKeyNav(event,3)">
                <input type="text" id="lpin-digit-4" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#D97706;border:2px solid #FDE68A;border-radius:12px" oninput="lpinAutoFocus(this,null)" onkeydown="lpinKeyNav(event,4)">
              </div>
              <div id="lpin-timer" style="font-size:12px;color:#9CA3AF;margin-bottom:16px">Expires in <span id="lpin-countdown">120</span>s</div>
              <div style="display:flex;gap:10px;justify-content:center">
                <button class="btn btn-primary" onclick="verifyLegalPin()" style="font-size:13px;padding:10px 24px;background:#D97706;border-color:#D97706">Verify PIN</button>
                <button class="btn btn-outline" onclick="requestLegalPin()" style="font-size:13px;padding:10px 24px">Resend</button>
              </div>
            </div>
          </div>
        </div>
      </div>


      <!-- ==================== TAB 1: LOAN DOCUMENTATION & DISCLOSURE ==================== -->
      <div id="tab-loans" class="tab-content active animate-in delay-3">
        <div class="card">
          <div class="card-header">
            <span class="card-title">Loan Documentation & Disclosure</span>
            <div style="display:flex;gap:8px">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportLoans('pdf')">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportLoans('csv')">📊 CSV</button>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Borrower</th>
                  <th>Amount</th>
                  <th>Interest</th>
                  <th>Term</th>
                  <th>Security Type</th>
                  <th>Status</th>
                  <th>Attorney</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="loans-tbody">
                <tr><td colspan="9" class="text-center text-gray-400 py-8">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ==================== TAB 3: CASE TRACKING & RECOVERY ==================== -->
      <div id="tab-cases" class="tab-content">

        <!-- Case Workflow Dashboard Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-bottom:20px">
          <div style="background:#FEF3C7;border:1px solid #F59E0B;border-radius:12px;padding:14px;text-align:center;cursor:pointer" onclick="filterCasesByStep('complaint_filed')">
            <div style="font-size:24px">📋</div>
            <div style="font-size:20px;font-weight:800;color:#92400E" id="wf-complaint-filed">0</div>
            <div style="font-size:11px;color:#92400E;font-weight:600">Complaint Filed</div>
          </div>
          <div style="background:#DBEAFE;border:1px solid #3B82F6;border-radius:12px;padding:14px;text-align:center;cursor:pointer" onclick="filterCasesByStep('under_review')">
            <div style="font-size:24px">🔍</div>
            <div style="font-size:20px;font-weight:800;color:#1E40AF" id="wf-under-review">0</div>
            <div style="font-size:11px;color:#1E40AF;font-weight:600">Under Review</div>
          </div>
          <div style="background:#FEE2E2;border:1px solid #EF4444;border-radius:12px;padding:14px;text-align:center;cursor:pointer" onclick="filterCasesByStep('for_hearing')">
            <div style="font-size:24px">🏛️</div>
            <div style="font-size:20px;font-weight:800;color:#991B1B" id="wf-for-hearing">0</div>
            <div style="font-size:11px;color:#991B1B;font-weight:600">For Hearing</div>
          </div>
          <div style="background:#E0E7FF;border:1px solid #6366F1;border-radius:12px;padding:14px;text-align:center;cursor:pointer" onclick="filterCasesByStep('ongoing_investigation')">
            <div style="font-size:24px">🔎</div>
            <div style="font-size:20px;font-weight:800;color:#3730A3" id="wf-investigating">0</div>
            <div style="font-size:11px;color:#3730A3;font-weight:600">Investigating</div>
          </div>
          <div style="background:#FDE68A;border:1px solid #D97706;border-radius:12px;padding:14px;text-align:center;cursor:pointer" onclick="filterCasesByStep('verdict')">
            <div style="font-size:24px">⚖️</div>
            <div style="font-size:20px;font-weight:800;color:#78350F" id="wf-verdict">0</div>
            <div style="font-size:11px;color:#78350F;font-weight:600">Verdict</div>
          </div>
          <div style="background:#D1FAE5;border:1px solid #059669;border-radius:12px;padding:14px;text-align:center;cursor:pointer" onclick="filterCasesByStep('closed')">
            <div style="font-size:24px">✅</div>
            <div style="font-size:20px;font-weight:800;color:#065F46" id="wf-closed">0</div>
            <div style="font-size:11px;color:#065F46;font-weight:600">Closed</div>
          </div>
          <div style="background:#F3F4F6;border:1px solid #9CA3AF;border-radius:12px;padding:14px;text-align:center;cursor:pointer" onclick="filterCasesByStep('dismissed')">
            <div style="font-size:24px">🚫</div>
            <div style="font-size:20px;font-weight:800;color:#374151" id="wf-dismissed">0</div>
            <div style="font-size:11px;color:#374151;font-weight:600">Dismissed</div>
          </div>
        </div>

        <!-- Overdue Escalation Warning -->
        <div id="escalation-warning" style="display:none;background:#FEF2F2;border:2px solid #EF4444;border-radius:12px;padding:14px;margin-bottom:16px">
          <div style="display:flex;align-items:center;gap:10px">
            <span style="font-size:22px">⏳</span>
            <div>
              <div style="font-weight:700;color:#991B1B">Overdue Escalations</div>
              <div style="font-size:13px;color:#B91C1C"><span id="escalation-count">0</span> case(s) have exceeded their escalation deadline (7 days no action)</div>
            </div>
            <button class="btn btn-sm" style="margin-left:auto;background:#EF4444;color:#fff;border:none;padding:6px 14px;border-radius:8px;font-weight:600;cursor:pointer" onclick="runEscalationCheck()">Auto-Escalate Now</button>
          </div>
        </div>

        <!-- Legal Cases -->
        <div class="card" style="margin-bottom:20px">
          <div class="card-header">
            <span class="card-title">⚖️ Legal Cases</span>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <button class="btn btn-sm" style="background:#059669;color:#fff;border:none;padding:6px 14px;border-radius:8px;font-weight:600;cursor:pointer" onclick="openFileComplaint()">📋 File Complaint</button>
              <button class="btn btn-sm" style="background:#6366F1;color:#fff;border:none;padding:6px 14px;border-radius:8px;font-weight:600;cursor:pointer" onclick="openDecisionMatrix()">📊 Decision Matrix</button>
              <button class="btn btn-sm" style="background:#0EA5E9;color:#fff;border:none;padding:6px 14px;border-radius:8px;font-weight:600;cursor:pointer" onclick="openCaseAnalytics()">📈 Analytics</button>
              <select id="case-filter-step" onchange="filterCasesByDropdown()" style="padding:5px 10px;border-radius:8px;border:1px solid #D1D5DB;font-size:12px">
                <option value="">All Steps</option>
                <option value="complaint_filed">Complaint Filed</option>
                <option value="under_review">Under Review</option>
                <option value="for_hearing">For Hearing</option>
                <option value="ongoing_investigation">Investigating</option>
                <option value="verdict">Verdict</option>
                <option value="closed">Closed</option>
                <option value="dismissed">Dismissed</option>
              </select>
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportCases('pdf')">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportCases('csv')">📊 CSV</button>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Case #</th>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Severity</th>
                  <th>Workflow</th>
                  <th>Priority</th>
                  <th>Complainant</th>
                  <th>Accused</th>
                  <th>Financial</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="cases-tbody">
                <tr><td colspan="10" class="text-center text-gray-400 py-8">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Demand Letters -->
        <div class="card" style="margin-bottom:20px">
          <div class="card-header">
            <span class="card-title">📨 Demand Letters</span>
            <div style="display:flex;gap:8px">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportDemands('pdf')">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportDemands('csv')">📊 CSV</button>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Borrower</th>
                  <th>Amount</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Sent Date</th>
                  <th>Response Deadline</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="demands-tbody">
                <tr><td colspan="8" class="text-center text-gray-400 py-8">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ==================== TAB 6: CONTRACT LIFECYCLE ==================== -->
      <div id="tab-contracts" class="tab-content">
        <!-- Contract Folder Cards -->
        <div id="contract-folders-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:20px">
          <div class="card" style="cursor:pointer;border:2px solid transparent;transition:all 0.2s" onmouseover="this.style.borderColor='#059669';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='transparent';this.style.transform=''" onclick="showContractFolder('employee')">
            <div class="card-body" style="padding:20px">
              <div style="display:flex;align-items:center;gap:14px">
                <div style="width:52px;height:52px;border-radius:14px;background:#D1FAE5;display:flex;align-items:center;justify-content:center;font-size:26px">👤</div>
                <div>
                  <div style="font-size:15px;font-weight:800;color:#1F2937">Employee Contracts</div>
                  <div style="font-size:12px;color:#6B7280;margin-top:2px">Employment agreements &amp; NDAs</div>
                  <div style="font-size:12px;font-weight:600;color:#059669;margin-top:4px" id="contract-count-employee">0 contracts</div>
                </div>
              </div>
            </div>
          </div>
          <div class="card" style="cursor:pointer;border:2px solid transparent;transition:all 0.2s" onmouseover="this.style.borderColor='#3B82F6';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='transparent';this.style.transform=''" onclick="showContractFolder('vendor')">
            <div class="card-body" style="padding:20px">
              <div style="display:flex;align-items:center;gap:14px">
                <div style="width:52px;height:52px;border-radius:14px;background:#DBEAFE;display:flex;align-items:center;justify-content:center;font-size:26px">🏢</div>
                <div>
                  <div style="font-size:15px;font-weight:800;color:#1F2937">Vendor Contracts</div>
                  <div style="font-size:12px;color:#6B7280;margin-top:2px">Vendor, service &amp; lease agreements</div>
                  <div style="font-size:12px;font-weight:600;color:#3B82F6;margin-top:4px" id="contract-count-vendor">0 contracts</div>
                </div>
              </div>
            </div>
          </div>
          <div class="card" style="cursor:pointer;border:2px solid transparent;transition:all 0.2s" onmouseover="this.style.borderColor='#8B5CF6';this.style.transform='translateY(-2px)'" onmouseout="this.style.borderColor='transparent';this.style.transform=''" onclick="showContractFolder('partnership')">
            <div class="card-body" style="padding:20px">
              <div style="display:flex;align-items:center;gap:14px">
                <div style="width:52px;height:52px;border-radius:14px;background:#EDE9FE;display:flex;align-items:center;justify-content:center;font-size:26px">🤝</div>
                <div>
                  <div style="font-size:15px;font-weight:800;color:#1F2937">Partnership Contracts</div>
                  <div style="font-size:12px;color:#6B7280;margin-top:2px">Partnership &amp; joint venture agreements</div>
                  <div style="font-size:12px;font-weight:600;color:#8B5CF6;margin-top:4px" id="contract-count-partnership">0 contracts</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Contract Folder Detail Panel -->
        <div id="contract-folder-panel" class="card" style="display:none">
          <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px">
              <button class="btn btn-outline btn-sm" onclick="closeContractFolder()">← Back</button>
              <span class="card-title" id="contract-folder-title">Contracts</span>
            </div>
            <div style="display:flex;gap:8px">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportContracts('pdf')">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportContracts('csv')">📊 CSV</button>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Contract #</th>
                  <th>Title</th>
                  <th>Type</th>
                  <th>Party</th>
                  <th>Value</th>
                  <th>Start</th>
                  <th>End</th>
                  <th>Status</th>
                  <th>Assigned</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="contracts-tbody">
                <tr><td colspan="10" class="text-center text-gray-400 py-8">Select a folder above</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ==================== TAB 7: PERMITS, LICENSES & RENEWALS ==================== -->
      <div id="tab-permits" class="tab-content">
        <div class="card">
          <div class="card-header">
            <span class="card-title">Permits, Licenses & Renewals</span>
            <div style="display:flex;gap:8px">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportPermits('pdf')">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportPermits('csv')">📊 CSV</button>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Code</th>
                  <th>Permit Name</th>
                  <th>Issuing Body</th>
                  <th>Type</th>
                  <th>Permit #</th>
                  <th>Issue Date</th>
                  <th>Expiry</th>
                  <th>Fee</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="permits-tbody">
                <tr><td colspan="10" class="text-center text-gray-400 py-8">Loading...</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ==================== TAB 8: LEGAL CALENDAR & DEADLINES ==================== -->
      <div id="tab-legal-calendar" class="tab-content">

        <!-- ── Navigation bar ── -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header" style="flex-wrap:wrap;gap:10px">
            <div style="display:flex;align-items:center;gap:10px">
              <button class="btn btn-outline btn-sm" onclick="legalCalNav(-1)">&#8249;</button>
              <span class="card-title" id="legal-cal-title" style="min-width:170px;text-align:center">—</span>
              <button class="btn btn-outline btn-sm" onclick="legalCalNav(1)">&#8250;</button>
              <button class="btn btn-outline btn-sm" onclick="legalCalToday()" style="font-size:11px;padding:4px 10px">Today</button>
            </div>
            <div style="display:flex;gap:14px;align-items:center;flex-wrap:wrap">
              <span style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:50%;background:#EF4444;display:inline-block"></span>Cases</span>
              <span style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:50%;background:#3B82F6;display:inline-block"></span>Contracts</span>
              <span style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:50%;background:#059669;display:inline-block"></span>Compliance</span>
              <span style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:50%;background:#F59E0B;display:inline-block"></span>Permits</span>
              <span style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:50%;background:#EC4899;display:inline-block"></span>Demands</span>
              <span style="font-size:11px;display:flex;align-items:center;gap:5px"><span style="width:10px;height:10px;border-radius:50%;background:#6366F1;display:inline-block"></span>Board</span>
            </div>
          </div>
        </div>

        <!-- ── Two-column layout ── -->
        <div style="display:grid;grid-template-columns:1fr 300px;gap:16px;align-items:start">

          <!-- Left: Calendar grid -->
          <div class="card" style="margin-bottom:0;overflow:hidden">
            <div id="legal-calendar-grid" style="display:grid;grid-template-columns:repeat(7,minmax(0,1fr));background:#E5E7EB;gap:1px">
            </div>
          </div>

          <!-- Right: Upcoming deadlines -->
          <div class="card" style="margin-bottom:0">
            <div class="card-header">
              <span class="card-title">⏰ Upcoming Deadlines</span>
              <span id="deadline-count-badge" style="background:#FEF3C7;color:#92400E;padding:2px 10px;border-radius:50px;font-size:11px;font-weight:700">0</span>
            </div>
            <div id="upcoming-deadlines-body" style="max-height:640px;overflow-y:auto;padding:12px;display:flex;flex-direction:column;gap:6px">
              <div style="text-align:center;padding:24px;color:#9CA3AF;font-size:13px">Loading...</div>
            </div>
          </div>

        </div>

        <!-- Day Detail Panel -->
        <div id="legal-day-panel" class="card" style="display:none;margin-top:16px">
          <div class="card-header">
            <span class="card-title" id="legal-day-title">—</span>
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('legal-day-panel').style.display='none'">Close</button>
          </div>
          <div class="card-body" id="legal-day-body" style="padding:16px"></div>
        </div>
      </div>



      <!-- ==================== MODALS ==================== -->

      <!-- Modal: View Loan Contract -->
      <div id="modal-loan-contract" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-loan-contract')">
        <div class="modal" style="max-width:800px">
          <div class="modal-header">
            <span class="modal-title">📄 Loan Contract</span>
            <button class="modal-close" onclick="closeModal('modal-loan-contract')">&times;</button>
          </div>
          <div class="modal-body" id="loan-contract-body"></div>
          <div class="modal-footer">
            <button class="btn btn-primary" id="btn-download-loan-pdf" style="display:none" onclick="exportLoanContractPDF()">📥 Download PDF</button>
            <button class="btn btn-outline" onclick="closeModal('modal-loan-contract')">Close</button>
          </div>
        </div>
      </div>

      <!-- Modal: View Collateral Details -->

      <!-- Modal: View Demand Letter -->
      <div id="modal-demand-detail" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-demand-detail')">
        <div class="modal" style="max-width:750px">
          <div class="modal-header">
            <span class="modal-title">📨 Demand Letter</span>
            <button class="modal-close" onclick="closeModal('modal-demand-detail')">&times;</button>
          </div>
          <div class="modal-body" id="demand-detail-body"></div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-demand-detail')">Close</button>
          </div>
        </div>
      </div>

      <!-- Modal: View Legal Case (Enhanced Workflow) -->
      <div id="modal-case-detail" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-case-detail')">
        <div class="modal" style="max-width:900px;max-height:92vh;overflow-y:auto">
          <div class="modal-header">
            <span class="modal-title">⚖️ Legal Case Details</span>
            <button class="modal-close" onclick="closeModal('modal-case-detail')">&times;</button>
          </div>
          <div class="modal-body" id="case-detail-body"></div>
          <div class="modal-footer" id="case-detail-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-case-detail')">Close</button>
          </div>
        </div>
      </div>

      <!-- Modal: File Complaint -->
      <div id="modal-file-complaint" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-file-complaint')">
        <div class="modal" style="max-width:750px;max-height:92vh;overflow-y:auto">
          <div class="modal-header">
            <span class="modal-title">📋 File New Complaint</span>
            <button class="modal-close" onclick="closeModal('modal-file-complaint')">&times;</button>
          </div>
          <div class="modal-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
              <div style="grid-column:1/-1">
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Case Title *</label>
                <input type="text" id="fc-title" placeholder="e.g. Loan Default - Juan Dela Cruz" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Case Type *</label>
                <select id="fc-case-type" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
                  <option value="">Select type...</option>
                  <option value="loan_default">Loan Default</option>
                  <option value="fraud">Fraud</option>
                  <option value="theft">Theft</option>
                  <option value="harassment">Harassment</option>
                  <option value="data_breach">Data Breach</option>
                  <option value="forgery">Forgery</option>
                  <option value="contract_violation">Contract Violation</option>
                  <option value="policy_violation">Policy Violation</option>
                  <option value="litigation">Litigation</option>
                  <option value="compliance">Compliance</option>
                  <option value="internal_investigation">Internal Investigation</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Priority</label>
                <select id="fc-priority" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
                  <option value="low">Low</option>
                  <option value="medium" selected>Medium</option>
                  <option value="high">High</option>
                  <option value="critical">Critical</option>
                </select>
              </div>
              <div style="grid-column:1/-1">
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Severity *</label>
                <div style="display:flex;gap:16px">
                  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
                    <input type="radio" name="fc-severity" value="minor"> 🟢 Minor
                  </label>
                  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
                    <input type="radio" name="fc-severity" value="moderate" checked> 🟡 Moderate
                  </label>
                  <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px">
                    <input type="radio" name="fc-severity" value="major"> 🔴 Major
                  </label>
                </div>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Complainant Name</label>
                <input type="text" id="fc-complainant-name" placeholder="Full name" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Complainant Department</label>
                <input type="text" id="fc-complainant-dept" placeholder="Department" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Accused Name</label>
                <input type="text" id="fc-accused-name" placeholder="Full name" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Accused Department</label>
                <input type="text" id="fc-accused-dept" placeholder="Department" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Accused Employee ID</label>
                <input type="text" id="fc-accused-empid" placeholder="EMP-XXXX" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Financial Impact (₱)</label>
                <input type="number" id="fc-financial" placeholder="0.00" step="0.01" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Assigned Lawyer</label>
                <input type="text" id="fc-lawyer" placeholder="Attorney name" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Legal Officer</label>
                <input type="text" id="fc-officer" placeholder="Handling officer" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Opposing Party</label>
                <input type="text" id="fc-opposing" placeholder="External party (if any)" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Court / Venue</label>
                <input type="text" id="fc-venue" placeholder="Court venue (if applicable)" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div style="grid-column:1/-1">
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Description *</label>
                <textarea id="fc-description" rows="4" placeholder="Detailed description of the complaint..." style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px;resize:vertical"></textarea>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Linked Loan (optional)</label>
                <select id="fc-linked-loan" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
                  <option value="">None</option>
                </select>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Department</label>
                <input type="text" id="fc-department" placeholder="Filing department" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-file-complaint')">Cancel</button>
            <button class="btn" style="background:#059669;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:600;cursor:pointer" onclick="submitComplaint()">📋 File Complaint</button>
          </div>
        </div>
      </div>

      <!-- Modal: Add Hearing -->
      <div id="modal-add-hearing" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-add-hearing')">
        <div class="modal" style="max-width:650px">
          <div class="modal-header">
            <span class="modal-title">🏛️ Schedule Hearing</span>
            <button class="modal-close" onclick="closeModal('modal-add-hearing')">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="ah-case-id">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Hearing Date & Time *</label>
                <input type="datetime-local" id="ah-date" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Hearing Type</label>
                <select id="ah-type" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
                  <option value="initial_review">Initial Review</option>
                  <option value="admin_hearing">Admin Hearing</option>
                  <option value="investigation">Investigation</option>
                  <option value="formal_hearing">Formal Hearing</option>
                  <option value="verdict_hearing">Verdict Hearing</option>
                  <option value="follow_up">Follow Up</option>
                </select>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Location</label>
                <input type="text" id="ah-location" placeholder="Room / venue" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Presiding Officer</label>
                <input type="text" id="ah-officer" placeholder="Officer name" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Attendees (comma sep.)</label>
                <input type="text" id="ah-attendees" placeholder="Name1, Name2" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Witnesses (comma sep.)</label>
                <input type="text" id="ah-witnesses" placeholder="Witness1, Witness2" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div style="grid-column:1/-1">
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Minutes / Notes</label>
                <textarea id="ah-minutes" rows="3" placeholder="Hearing minutes and notes..." style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px;resize:vertical"></textarea>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Outcome</label>
                <input type="text" id="ah-outcome" placeholder="Hearing outcome" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Next Action</label>
                <input type="text" id="ah-next-action" placeholder="Follow-up action" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-add-hearing')">Cancel</button>
            <button class="btn" style="background:#059669;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:600;cursor:pointer" onclick="submitHearing()">🏛️ Save Hearing</button>
          </div>
        </div>
      </div>

      <!-- Modal: Add Evidence (with file upload) -->
      <div id="modal-add-evidence" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-add-evidence')">
        <div class="modal" style="max-width:620px">
          <div class="modal-header">
            <span class="modal-title">📎 Add Evidence</span>
            <button class="modal-close" onclick="closeModal('modal-add-evidence')">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="ae-case-id">
            <div style="display:grid;gap:14px">
              <!-- File drop zone -->
              <div id="ae-dropzone"
                ondragover="event.preventDefault();this.style.borderColor='#059669';this.style.background='#F0FDF4'"
                ondragleave="this.style.borderColor='#D1D5DB';this.style.background='#F9FAFB'"
                ondrop="handleEvidenceDrop(event)"
                onclick="document.getElementById('ae-file-input').click()"
                style="border:2px dashed #D1D5DB;border-radius:12px;padding:28px;text-align:center;cursor:pointer;background:#F9FAFB;transition:all 0.2s">
                <div style="font-size:36px;margin-bottom:8px">☁️</div>
                <div style="font-weight:600;color:#374151;font-size:14px">Click or drag & drop your file here</div>
                <div style="font-size:12px;color:#9CA3AF;margin-top:4px">Supports: Images, Videos, Audio, PDF, Word, Excel, ZIP — Max 50 MB</div>
                <input type="file" id="ae-file-input" style="display:none"
                  accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar,.eml"
                  onchange="handleEvidenceFileSelect(this.files[0])">
              </div>

              <!-- File preview -->
              <div id="ae-preview" style="display:none">
                <div style="background:#F0FDF4;border:1px solid #A7F3D0;border-radius:10px;padding:12px;display:flex;align-items:center;gap:12px">
                  <div id="ae-preview-icon" style="font-size:32px">📄</div>
                  <div style="flex:1;min-width:0">
                    <div id="ae-preview-name" style="font-weight:600;font-size:13px;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></div>
                    <div id="ae-preview-size" style="font-size:11px;color:#6B7280"></div>
                  </div>
                  <button onclick="clearEvidenceFile()" style="background:none;border:none;cursor:pointer;color:#EF4444;font-size:20px;line-height:1" title="Remove file">✕</button>
                </div>
                <!-- Image thumbnail (shown only for images) -->
                <div id="ae-img-thumb-wrap" style="display:none;margin-top:8px;text-align:center">
                  <img id="ae-img-thumb" style="max-height:200px;max-width:100%;border-radius:8px;border:1px solid #E5E7EB;object-fit:contain" src="" alt="preview">
                </div>
              </div>

              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                  <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Evidence Type</label>
                  <select id="ae-type" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
                    <option value="document">Document</option>
                    <option value="photo">Photo</option>
                    <option value="video">Video</option>
                    <option value="audio">Audio</option>
                    <option value="email">Email</option>
                    <option value="report">Report</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div>
                  <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Description</label>
                  <input type="text" id="ae-description" placeholder="Optional description" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
                </div>
              </div>

              <!-- Upload progress -->
              <div id="ae-progress-wrap" style="display:none">
                <div style="font-size:12px;color:#6B7280;margin-bottom:4px">Uploading...</div>
                <div style="height:8px;background:#E5E7EB;border-radius:4px;overflow:hidden">
                  <div id="ae-progress-bar" style="height:100%;width:0%;background:#059669;border-radius:4px;transition:width 0.3s"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-add-evidence')">Cancel</button>
            <button id="ae-submit-btn" class="btn" style="background:#059669;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:600;cursor:pointer" onclick="submitEvidence()">📎 Upload Evidence</button>
          </div>
        </div>
      </div>

      <!-- Modal: Render Verdict -->
      <div id="modal-render-verdict" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-render-verdict')">
        <div class="modal" style="max-width:600px">
          <div class="modal-header">
            <span class="modal-title">⚖️ Render Verdict</span>
            <button class="modal-close" onclick="closeModal('modal-render-verdict')">&times;</button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="rv-case-id">
            <div style="display:grid;gap:14px">
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Verdict *</label>
                <select id="rv-verdict" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
                  <option value="">Select verdict...</option>
                  <option value="not_guilty">Not Guilty</option>
                  <option value="guilty_warning">Guilty — Warning</option>
                  <option value="guilty_suspension">Guilty — Suspension</option>
                  <option value="guilty_termination">Guilty — Termination</option>
                  <option value="filed_in_court">Filed in Court</option>
                  <option value="deduct_salary">Deduct from Salary</option>
                  <option value="dismissed">Case Dismissed</option>
                </select>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Penalty Amount (₱)</label>
                <input type="number" id="rv-amount" placeholder="0.00" step="0.01" style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px">
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Penalty Details</label>
                <textarea id="rv-details" rows="3" placeholder="Describe the penalty and conditions..." style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px;resize:vertical"></textarea>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;display:block;margin-bottom:4px">Resolution Summary</label>
                <textarea id="rv-summary" rows="3" placeholder="Summary of the resolution..." style="width:100%;padding:8px 12px;border:1px solid #D1D5DB;border-radius:8px;font-size:13px;resize:vertical"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-render-verdict')">Cancel</button>
            <button class="btn" style="background:#EF4444;color:#fff;border:none;padding:8px 20px;border-radius:8px;font-weight:600;cursor:pointer" onclick="submitVerdict()">⚖️ Render Verdict</button>
          </div>
        </div>
      </div>

      <!-- Modal: Decision Matrix -->
      <div id="modal-decision-matrix" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-decision-matrix')">
        <div class="modal" style="max-width:850px;max-height:90vh;overflow-y:auto">
          <div class="modal-header">
            <span class="modal-title">📊 Legal Decision Matrix</span>
            <button class="modal-close" onclick="closeModal('modal-decision-matrix')">&times;</button>
          </div>
          <div class="modal-body" id="decision-matrix-body">Loading...</div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-decision-matrix')">Close</button>
          </div>
        </div>
      </div>

      <!-- Modal: Case Analytics -->
      <div id="modal-case-analytics" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-case-analytics')">
        <div class="modal" style="max-width:900px;max-height:92vh;overflow-y:auto">
          <div class="modal-header">
            <span class="modal-title">📈 Case Analytics</span>
            <button class="modal-close" onclick="closeModal('modal-case-analytics')">&times;</button>
          </div>
          <div class="modal-body" id="case-analytics-body">Loading...</div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-case-analytics')">Close</button>
          </div>
        </div>
      </div>

      <!-- Modal: View Contract -->
      <div id="modal-contract-detail" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-contract-detail')">
        <div class="modal" style="max-width:700px">
          <div class="modal-header">
            <span class="modal-title">📝 Contract Details</span>
            <button class="modal-close" onclick="closeModal('modal-contract-detail')">&times;</button>
          </div>
          <div class="modal-body" id="contract-detail-body"></div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-contract-detail')">Close</button>
          </div>
        </div>
      </div>

      <!-- Modal: View Permit -->
      <div id="modal-permit-detail" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-permit-detail')">
        <div class="modal" style="max-width:700px">
          <div class="modal-header">
            <span class="modal-title">🪪 Permit Details</span>
            <button class="modal-close" onclick="closeModal('modal-permit-detail')">&times;</button>
          </div>
          <div class="modal-body" id="permit-detail-body"></div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-permit-detail')">Close</button>
          </div>
        </div>
      </div>

    </main>
  </div>

<script src="../../admin.js?v=20260304"></script>
<script src="../../export.js?v=20260304"></script>
<script>
const API = '../../api/legal.php';

// Data stores
let loans = [], cases = [], demands = [], contracts = [], permits = [], stats = {};
let compliance = [], resolutions = [];

// ===== Helpers =====
function money(v) {
  const n = parseFloat(v);
  if (isNaN(n)) return '₱0.00';
  return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function fmtDate(d) {
  if (!d) return '—';
  const dt = new Date(d);
  if (isNaN(dt)) return d;
  return dt.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function esc(s) {
  if (s === null || s === undefined) return '';
  const d = document.createElement('div');
  d.textContent = String(s);
  return d.innerHTML;
}

function labelCase(s) {
  if (!s) return '';
  return s.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
}

// ===== Badge Helpers =====
function loanStatusBadge(s) {
  const map = {
    draft: 'badge badge-gray', pending_signature: 'badge badge-amber', signed: 'badge badge-blue',
    active: 'badge badge-green', defaulted: 'badge badge-red', paid: 'badge badge-purple', cancelled: 'badge badge-gray'
  };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function lienStatusBadge(s) {
  const map = { active: 'badge badge-green', released: 'badge badge-blue', foreclosed: 'badge badge-red', pending_release: 'badge badge-amber' };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function priorityBadge(p) {
  const map = { low: 'badge badge-gray', medium: 'badge badge-blue', high: 'badge badge-amber', critical: 'badge badge-red' };
  return '<span class="' + (map[p] || 'badge badge-gray') + '">' + labelCase(p) + '</span>';
}

function caseStatusBadge(s) {
  const map = {
    open: 'badge badge-amber', in_progress: 'badge badge-blue', pending_review: 'badge badge-amber',
    resolved: 'badge badge-green', closed: 'badge badge-gray', appealed: 'badge badge-purple'
  };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function workflowBadge(s) {
  const map = {
    complaint_filed: ['#F59E0B','#78350F'], under_review: ['#3B82F6','#1E3A8A'],
    for_hearing: ['#EF4444','#7F1D1D'], ongoing_investigation: ['#6366F1','#312E81'],
    verdict: ['#D97706','#78350F'], closed: ['#059669','#064E3B'], dismissed: ['#9CA3AF','#374151']
  };
  const c = map[s] || ['#9CA3AF','#374151'];
  return '<span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700;background:' + c[0] + '22;color:' + c[1] + ';border:1px solid ' + c[0] + '44">' + labelCase(s) + '</span>';
}

function severityBadge(s) {
  const map = { minor: ['🟢','#059669'], moderate: ['🟡','#D97706'], major: ['🔴','#EF4444'] };
  const c = map[s] || ['⚪','#6B7280'];
  return '<span style="color:' + c[1] + ';font-weight:700;font-size:12px">' + c[0] + ' ' + labelCase(s) + '</span>';
}

function verdictBadge(v) {
  if (!v) return '<span style="color:#9CA3AF;font-size:12px">—</span>';
  const map = {
    not_guilty: ['#059669','Not Guilty'], guilty_warning: ['#F59E0B','Warning'], guilty_suspension: ['#EF4444','Suspension'],
    guilty_termination: ['#7F1D1D','Termination'], filed_in_court: ['#7C3AED','Filed in Court'],
    deduct_salary: ['#D97706','Salary Deduction'], dismissed: ['#6B7280','Dismissed']
  };
  const c = map[v] || ['#6B7280', labelCase(v)];
  return '<span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700;background:' + c[0] + '22;color:' + c[0] + '">' + c[1] + '</span>';
}

function demandStatusBadge(s) {
  const map = {
    draft: 'badge badge-gray', sent: 'badge badge-blue', received: 'badge badge-green',
    responded: 'badge badge-purple', expired: 'badge badge-red', escalated: 'badge badge-red'
  };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function riskBadge(r) {
  const map = { low: 'badge badge-green', medium: 'badge badge-amber', high: 'badge badge-red', pep: 'badge badge-purple' };
  return '<span class="' + (map[r] || 'badge badge-gray') + '">' + labelCase(r) + '</span>';
}

function kycStatusBadge(s) {
  const map = {
    pending: 'badge badge-amber', verified: 'badge badge-green', rejected: 'badge badge-red',
    expired: 'badge badge-gray', under_review: 'badge badge-blue'
  };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function complianceStatusBadge(s) {
  const map = {
    compliant: 'badge badge-green', non_compliant: 'badge badge-red', in_progress: 'badge badge-blue',
    pending_review: 'badge badge-amber', exempted: 'badge badge-gray'
  };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function resolutionStatusBadge(s) {
  const map = { draft: 'badge badge-gray', approved: 'badge badge-green', filed: 'badge badge-blue', superseded: 'badge badge-amber' };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function poaStatusBadge(s) {
  const map = { active: 'badge badge-green', expired: 'badge badge-gray', revoked: 'badge badge-red', superseded: 'badge badge-amber' };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function contractStatusBadge(s) {
  const map = {
    draft: 'badge badge-gray', active: 'badge badge-green', expired: 'badge badge-red',
    terminated: 'badge badge-red', renewed: 'badge badge-blue', under_review: 'badge badge-amber'
  };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function permitStatusBadge(s) {
  const map = {
    active: 'badge badge-green', expired: 'badge badge-red', pending_renewal: 'badge badge-amber',
    suspended: 'badge badge-amber', revoked: 'badge badge-red'
  };
  return '<span class="' + (map[s] || 'badge badge-gray') + '">' + labelCase(s) + '</span>';
}

function isExpiringSoon(dateStr) {
  if (!dateStr) return false;
  const exp = new Date(dateStr);
  const now = new Date();
  const diffDays = (exp - now) / (1000 * 60 * 60 * 24);
  return diffDays > 0 && diffDays <= 90;
}

function emptyRow(cols, msg) {
  return '<tr><td colspan="' + cols + '" class="text-center text-gray-400 py-8"><div class="empty-state"><div style="font-size:36px;margin-bottom:8px">📭</div><div>' + (msg || 'No records found') + '</div></div></td></tr>';
}

// ===== Data Loading =====
async function loadData() {
  try {
    const [sRes, lRes, csRes, dRes, ctRes, pmRes, coRes, rrRes] = await Promise.all([
      fetch(API + '?action=dashboard_stats'),
      fetch(API + '?action=list_loans'),
      fetch(API + '?action=list_cases'),
      fetch(API + '?action=list_demands'),
      fetch(API + '?action=list_contracts'),
      fetch(API + '?action=list_permits'),
      fetch(API + '?action=list_compliance'),
      fetch(API + '?action=list_resolutions')
    ]);

    stats = await sRes.json();
    loans = (await lRes.json()).data || [];
    cases = (await csRes.json()).data || [];
    demands = (await dRes.json()).data || [];
    contracts = (await ctRes.json()).data || [];
    permits = (await pmRes.json()).data || [];
    compliance = (await coRes.json()).data || [];
    resolutions = (await rrRes.json()).data || [];

    renderStats();
    renderLoans();
    renderDemands();
    renderCases();
    renderContracts();
    renderPermits();
    if (typeof refreshSidebarCounts === 'function') refreshSidebarCounts();
    // Re-render calendar now that all data is ready
    if (document.getElementById('tab-legal-calendar') && document.getElementById('tab-legal-calendar').classList.contains('active')) {
      renderLegalCalendar();
    }
  } catch (e) {
    console.error('Load error:', e);
    Swal.fire({ icon: 'error', title: 'Load Error', text: 'Failed to load legal data. Please refresh the page.', confirmButtonColor: '#059669' });
  }
}

// ===== Render: Stats =====
function renderStats() {
  document.getElementById('stat-loans').textContent = stats.total_loans || 0;
  document.getElementById('stat-cases').textContent = stats.active_cases || 0;
  document.getElementById('stat-contracts').textContent = contracts.length;
  document.getElementById('stat-permits').textContent = permits.length;
}

// ===== Render: Loans =====
function renderLoans() {
  const tb = document.getElementById('loans-tbody');
  if (!loans.length) { tb.innerHTML = emptyRow(9, 'No loan documents found'); return; }
  tb.innerHTML = loans.map(function(l, i) {
    return '<tr>' +
      '<td><span class="secure-text" style="font-size:11px">' + esc(l.loan_doc_code) + '</span></td>' +
      '<td style="font-weight:600">' + esc(l.borrower_name) + '</td>' +
      '<td>' + money(l.loan_amount) + '</td>' +
      '<td>' + parseFloat(l.interest_rate || 0).toFixed(2) + '%</td>' +
      '<td>' + (l.loan_term_months || 0) + ' mos</td>' +
      '<td>' + labelCase(l.security_type) + '</td>' +
      '<td>' + loanStatusBadge(l.status) + '</td>' +
      '<td>' + (l.attorney_name ? '<span class="attorney-seal">⚖️ ' + esc(l.attorney_name) + '</span>' : '<span class="text-gray-400">—</span>') + '</td>' +
      '<td><button class="btn btn-outline btn-sm" onclick="viewLoanContract(' + i + ')">View Contract</button></td>' +
    '</tr>';
  }).join('');
}

// ===== View: Loan Contract =====
let viewLoanContractIdx = null;

function viewLoanContract(idx) {
  const l = loans[idx];
  if (!l) return;
  viewLoanContractIdx = idx;
  document.getElementById('btn-download-loan-pdf').style.display = 'inline-flex';

  var html = '<div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:20px;flex-wrap:wrap;gap:12px">' +
    '<div>' +
      '<div style="font-weight:700;font-size:16px;color:#1F2937">' + esc(l.loan_doc_code) + '</div>' +
      '<div style="font-size:13px;color:#6B7280">Borrower: <strong>' + esc(l.borrower_name) + '</strong></div>' +
      (l.borrower_address ? '<div style="font-size:12px;color:#9CA3AF">' + esc(l.borrower_address) + '</div>' : '') +
    '</div>' +
    '<div style="text-align:right">' +
      loanStatusBadge(l.status) +
      '<div style="font-size:12px;color:#6B7280;margin-top:4px">Amount: <strong>' + money(l.loan_amount) + '</strong></div>' +
    '</div>' +
  '</div>';

  html += '<div class="grid-2" style="margin-bottom:16px">' +
    '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
      '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Loan Terms</div>' +
      '<div style="font-size:13px;margin-top:4px">Interest: <strong>' + parseFloat(l.interest_rate || 0).toFixed(2) + '%</strong> | Term: <strong>' + l.loan_term_months + ' months</strong></div>' +
      '<div style="font-size:13px">Repayment: <strong>' + labelCase(l.repayment_schedule) + '</strong> | Security: <strong>' + labelCase(l.security_type) + '</strong></div>' +
      '<div style="font-size:13px">Penalty Rate: <strong>' + parseFloat(l.penalty_rate || 0).toFixed(2) + '%/mo</strong></div>' +
      (l.purpose ? '<div style="font-size:13px">Purpose: <strong>' + esc(l.purpose) + '</strong></div>' : '') +
    '</div>' +
    '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
      '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Key Dates</div>' +
      '<div style="font-size:13px;margin-top:4px">Signed: <strong>' + fmtDate(l.signed_date) + '</strong></div>' +
      '<div style="font-size:13px">Effective: <strong>' + fmtDate(l.effective_date) + '</strong></div>' +
      '<div style="font-size:13px">Maturity: <strong>' + fmtDate(l.maturity_date) + '</strong></div>' +
    '</div>' +
  '</div>';

  if (l.contract_body) {
    html += '<div style="font-weight:600;font-size:13px;color:#1F2937;margin-bottom:8px">📄 Contract Body</div>' +
      '<div class="contract-paper">' + esc(l.contract_body) + '</div>';
  }

  if (l.disclosure_statement) {
    html += '<div style="font-weight:600;font-size:13px;color:#1F2937;margin:16px 0 8px">📋 Truth in Lending Disclosure</div>' +
      '<div class="contract-paper" style="max-height:200px">' + esc(l.disclosure_statement) + '</div>';
  }

  if (l.promissory_note) {
    html += '<div style="font-weight:600;font-size:13px;color:#1F2937;margin:16px 0 8px">📝 Promissory Note</div>' +
      '<div class="contract-paper" style="max-height:200px">' + esc(l.promissory_note) + '</div>';
  }

  if (l.attorney_name) {
    html += '<div style="margin-top:20px;padding:16px;background:linear-gradient(135deg,#F0FDF4,#D1FAE5);border-radius:12px;border:1px solid #A7F3D0">' +
      '<div style="font-weight:700;font-size:13px;color:#065F46;margin-bottom:8px">⚖️ Attorney Credentials</div>' +
      '<div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;font-size:12px">' +
        '<div><strong>Name:</strong> ' + esc(l.attorney_name) + '</div>' +
        '<div><strong>PRC No:</strong> ' + (esc(l.attorney_prc) || '—') + '</div>' +
        '<div><strong>PTR No:</strong> ' + (esc(l.attorney_ptr) || '—') + '</div>' +
        '<div><strong>IBP No:</strong> ' + (esc(l.attorney_ibp) || '—') + '</div>' +
        '<div><strong>Roll No:</strong> ' + (esc(l.attorney_roll) || '—') + '</div>' +
        '<div><strong>MCLE No:</strong> ' + (esc(l.attorney_mcle) || '—') + '</div>' +
      '</div>' +
    '</div>';
  }

  if (l.notary_name) {
    html += '<div style="margin-top:12px;padding:12px;background:#FFFBEB;border-radius:8px;border:1px solid #FDE68A;font-size:12px">' +
      '<strong>Notary:</strong> ' + esc(l.notary_name) + ' | <strong>Commission:</strong> ' + (esc(l.notary_commission) || '—') +
      ' | <strong>Doc No:</strong> ' + (esc(l.doc_series_no) || '—') + ' | <strong>Page:</strong> ' + (esc(l.doc_page_no) || '—') +
      ' | <strong>Book:</strong> ' + (esc(l.doc_book_no) || '—') +
    '</div>';
  }

  document.getElementById('loan-contract-body').innerHTML = html;
  openModal('modal-loan-contract');
}

// ===== Render: Demands =====
function renderDemands() {
  const tb = document.getElementById('demands-tbody');
  if (!demands.length) { tb.innerHTML = emptyRow(8, 'No demand letters found'); return; }
  tb.innerHTML = demands.map(function(d, i) {
    return '<tr>' +
      '<td><span class="secure-text" style="font-size:11px">' + esc(d.demand_code) + '</span></td>' +
      '<td style="font-weight:600">' + esc(d.borrower_name) + '</td>' +
      '<td>' + money(d.amount_demanded) + '</td>' +
      '<td>' + labelCase(d.demand_type) + '</td>' +
      '<td>' + demandStatusBadge(d.status) + '</td>' +
      '<td>' + fmtDate(d.sent_date) + '</td>' +
      '<td>' + fmtDate(d.response_deadline) + '</td>' +
      '<td><button class="btn btn-outline btn-sm" onclick="viewDemand(' + i + ')">View</button></td>' +
    '</tr>';
  }).join('');
}

// ===== View: Demand =====
function viewDemand(idx) {
  const d = demands[idx];
  if (!d) return;
  document.getElementById('demand-detail-body').innerHTML =
    '<div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;flex-wrap:wrap;gap:12px">' +
      '<div>' +
        '<div style="font-weight:700;font-size:16px;color:#1F2937">' + esc(d.demand_code) + '</div>' +
        '<div style="font-size:13px;color:#6B7280">Borrower: <strong>' + esc(d.borrower_name) + '</strong></div>' +
        (d.borrower_address ? '<div style="font-size:12px;color:#9CA3AF">' + esc(d.borrower_address) + '</div>' : '') +
      '</div>' +
      '<div style="text-align:right">' +
        demandStatusBadge(d.status) +
        '<div style="font-size:13px;color:#6B7280;margin-top:4px">Amount: <strong>' + money(d.amount_demanded) + '</strong></div>' +
      '</div>' +
    '</div>' +
    '<div class="grid-2" style="gap:12px;margin-bottom:16px">' +
      '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
        '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Letter Details</div>' +
        '<div style="font-size:13px;margin-top:4px"><strong>Type:</strong> ' + labelCase(d.demand_type) + '</div>' +
        (d.attorney_name ? '<div style="font-size:13px"><strong>Attorney:</strong> ' + esc(d.attorney_name) + '</div>' : '') +
        (d.sent_via ? '<div style="font-size:13px"><strong>Sent Via:</strong> ' + labelCase(d.sent_via) + '</div>' : '') +
      '</div>' +
      '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
        '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Timeline</div>' +
        '<div style="font-size:13px;margin-top:4px"><strong>Sent:</strong> ' + fmtDate(d.sent_date) + '</div>' +
        '<div style="font-size:13px"><strong>Received:</strong> ' + fmtDate(d.received_date) + '</div>' +
        '<div style="font-size:13px"><strong>Response Deadline:</strong> ' + fmtDate(d.response_deadline) + '</div>' +
      '</div>' +
    '</div>' +
    (d.borrower_responded ? '<div style="background:#F0FDF4;padding:12px;border-radius:8px;border:1px solid #A7F3D0;margin-bottom:12px">' +
      '<div style="font-size:12px;font-weight:600;color:#065F46">✅ Borrower Responded</div>' +
      (d.response_summary ? '<div style="font-size:13px;margin-top:4px">' + esc(d.response_summary) + '</div>' : '') +
    '</div>' : '') +
    (d.escalated_to_litigation ? '<div class="badge badge-red" style="margin-bottom:12px">⚠ Escalated to Litigation</div>' : '') +
    (d.letter_body ? '<div style="font-weight:600;font-size:13px;margin-bottom:8px">📄 Letter Body</div>' +
      '<div class="contract-paper">' + esc(d.letter_body) + '</div>' : '');
  openModal('modal-demand-detail');
}

// ===== Render: Cases (Enhanced Workflow) =====
let caseFilterStep = '';

function renderCaseWorkflowCards() {
  const counts = { complaint_filed:0, under_review:0, for_hearing:0, ongoing_investigation:0, verdict:0, closed:0, dismissed:0 };
  cases.forEach(c => { if (counts.hasOwnProperty(c.workflow_step)) counts[c.workflow_step]++; });
  document.getElementById('wf-complaint-filed').textContent = counts.complaint_filed;
  document.getElementById('wf-under-review').textContent = counts.under_review;
  document.getElementById('wf-for-hearing').textContent = counts.for_hearing;
  document.getElementById('wf-investigating').textContent = counts.ongoing_investigation;
  document.getElementById('wf-verdict').textContent = counts.verdict;
  document.getElementById('wf-closed').textContent = counts.closed;
  document.getElementById('wf-dismissed').textContent = counts.dismissed;
  // Escalation warning
  const overdue = stats.overdue_escalations || 0;
  const warn = document.getElementById('escalation-warning');
  if (overdue > 0) { warn.style.display = 'block'; document.getElementById('escalation-count').textContent = overdue; }
  else { warn.style.display = 'none'; }
}

function filterCasesByStep(step) {
  caseFilterStep = step;
  document.getElementById('case-filter-step').value = step;
  renderCases();
}

function filterCasesByDropdown() {
  caseFilterStep = document.getElementById('case-filter-step').value;
  renderCases();
}

function renderCases() {
  renderCaseWorkflowCards();
  const tb = document.getElementById('cases-tbody');
  let filtered = cases;
  if (caseFilterStep) filtered = cases.filter(c => c.workflow_step === caseFilterStep);
  if (!filtered.length) { tb.innerHTML = emptyRow(10, 'No legal cases found'); return; }
  tb.innerHTML = filtered.map(function(c) {
    const idx = cases.indexOf(c);
    const isOverdue = c.escalation_deadline && new Date(c.escalation_deadline) < new Date() && !['closed','dismissed'].includes(c.workflow_step);
    return '<tr' + (isOverdue ? ' style="background:#FEF2F2"' : '') + '>' +
      '<td><span class="secure-text" style="font-size:11px">' + esc(c.case_number) + '</span>' +
        (isOverdue ? ' <span title="Overdue" style="color:#EF4444;font-size:14px">⏳</span>' : '') +
        (c.auto_escalated == 1 ? ' <span title="Auto-Escalated" style="color:#F59E0B;font-size:12px">⚡</span>' : '') +
      '</td>' +
      '<td style="font-weight:600;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + esc(c.title) + '">' + esc(c.title) + '</td>' +
      '<td style="font-size:12px">' + labelCase(c.case_type) + '</td>' +
      '<td>' + severityBadge(c.severity) + '</td>' +
      '<td>' + workflowBadge(c.workflow_step) + '</td>' +
      '<td>' + priorityBadge(c.priority) + '</td>' +
      '<td style="font-size:12px">' + (esc(c.complainant_name) || '—') + '</td>' +
      '<td style="font-size:12px">' + (esc(c.accused_name) || '—') + '</td>' +
      '<td>' + money(c.financial_impact) + '</td>' +
      '<td><button class="btn btn-outline btn-sm" onclick="viewCase(' + idx + ')">View</button></td>' +
    '</tr>';
  }).join('');
}

// ===== View: Case (Enhanced with Workflow) =====
let viewCaseIdx = null;

function viewCase(idx) {
  const c = cases[idx];
  if (!c) return;
  viewCaseIdx = idx;

  // Workflow stepper
  const steps = ['complaint_filed','under_review','for_hearing','ongoing_investigation','verdict','closed'];
  const stepLabels = ['📋 Filed','🔍 Review','🏛️ Hearing','🔎 Investigation','⚖️ Verdict','✅ Closed'];
  const currentIdx = steps.indexOf(c.workflow_step);
  const isDismissed = c.workflow_step === 'dismissed';

  let stepperHtml = '<div style="display:flex;align-items:center;gap:0;margin-bottom:20px;overflow-x:auto;padding:4px 0">';
  steps.forEach((s, i) => {
    const isActive = i === currentIdx;
    const isDone = i < currentIdx;
    let bg = '#F3F4F6', color = '#9CA3AF', border = '#E5E7EB';
    if (isDismissed) { bg = '#F3F4F6'; color = '#9CA3AF'; }
    else if (isActive) { bg = '#059669'; color = '#fff'; border = '#059669'; }
    else if (isDone) { bg = '#D1FAE5'; color = '#065F46'; border = '#A7F3D0'; }
    stepperHtml += '<div style="flex:1;text-align:center;padding:8px 4px;border-radius:8px;font-size:11px;font-weight:600;background:' + bg + ';color:' + color + ';border:1px solid ' + border + ';min-width:80px">' + stepLabels[i] + '</div>';
    if (i < steps.length - 1) stepperHtml += '<div style="width:20px;text-align:center;color:#D1D5DB;font-size:16px">→</div>';
  });
  if (isDismissed) stepperHtml += '<div style="width:20px;text-align:center;color:#D1D5DB;font-size:16px">→</div><div style="flex:1;text-align:center;padding:8px 4px;border-radius:8px;font-size:11px;font-weight:600;background:#EF4444;color:#fff;border:1px solid #EF4444;min-width:80px">🚫 Dismissed</div>';
  stepperHtml += '</div>';

  let html = stepperHtml;

  // Header
  html += '<div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;flex-wrap:wrap;gap:12px">' +
    '<div>' +
      '<div style="font-weight:700;font-size:16px;color:#1F2937">' + esc(c.case_number) + '</div>' +
      '<div style="font-weight:600;font-size:14px;color:#374151;margin-top:2px">' + esc(c.title) + '</div>' +
    '</div>' +
    '<div style="display:flex;gap:6px;flex-wrap:wrap">' + severityBadge(c.severity) + ' ' + priorityBadge(c.priority) + ' ' + workflowBadge(c.workflow_step) + '</div>' +
  '</div>';

  // Info panels
  html += '<div class="grid-2" style="gap:12px;margin-bottom:16px">' +
    '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
      '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Case Info</div>' +
      '<div style="font-size:13px;margin-top:4px"><strong>Type:</strong> ' + labelCase(c.case_type) + '</div>' +
      (c.opposing_party ? '<div style="font-size:13px"><strong>Opposing:</strong> ' + esc(c.opposing_party) + '</div>' : '') +
      (c.court_venue ? '<div style="font-size:13px"><strong>Venue:</strong> ' + esc(c.court_venue) + '</div>' : '') +
      (c.assigned_lawyer ? '<div style="font-size:13px"><strong>Lawyer:</strong> ' + esc(c.assigned_lawyer) + '</div>' : '') +
      (c.legal_officer ? '<div style="font-size:13px"><strong>Legal Officer:</strong> ' + esc(c.legal_officer) + '</div>' : '') +
      (c.department ? '<div style="font-size:13px"><strong>Dept:</strong> ' + esc(c.department) + '</div>' : '') +
    '</div>' +
    '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
      '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Dates & Impact</div>' +
      '<div style="font-size:13px;margin-top:4px"><strong>Filed:</strong> ' + fmtDate(c.filing_date) + '</div>' +
      '<div style="font-size:13px"><strong>Due:</strong> ' + fmtDate(c.due_date) + '</div>' +
      (c.next_hearing ? '<div style="font-size:13px"><strong>Next Hearing:</strong> ' + fmtDate(c.next_hearing) + '</div>' : '') +
      (c.resolution_date ? '<div style="font-size:13px"><strong>Resolved:</strong> ' + fmtDate(c.resolution_date) + '</div>' : '') +
      '<div style="font-size:13px"><strong>Financial:</strong> ' + money(c.financial_impact) + '</div>' +
      (c.assigned_name ? '<div style="font-size:13px"><strong>Assigned:</strong> ' + esc(c.assigned_name) + '</div>' : '') +
    '</div>' +
  '</div>';

  // Complainant & Accused
  if (c.complainant_name || c.accused_name) {
    html += '<div class="grid-2" style="gap:12px;margin-bottom:16px">';
    if (c.complainant_name) {
      html += '<div style="background:#F0FDF4;padding:12px;border-radius:8px;border:1px solid #A7F3D0">' +
        '<div style="font-size:11px;color:#065F46;text-transform:uppercase;font-weight:600">👤 Complainant</div>' +
        '<div style="font-size:13px;margin-top:4px;font-weight:600">' + esc(c.complainant_name) + '</div>' +
        (c.complainant_department ? '<div style="font-size:12px;color:#6B7280">' + esc(c.complainant_department) + '</div>' : '') +
      '</div>';
    }
    if (c.accused_name) {
      html += '<div style="background:#FEF2F2;padding:12px;border-radius:8px;border:1px solid #FECACA">' +
        '<div style="font-size:11px;color:#991B1B;text-transform:uppercase;font-weight:600">⚠️ Accused</div>' +
        '<div style="font-size:13px;margin-top:4px;font-weight:600">' + esc(c.accused_name) + '</div>' +
        (c.accused_department ? '<div style="font-size:12px;color:#6B7280">' + esc(c.accused_department) + '</div>' : '') +
        (c.accused_employee_id ? '<div style="font-size:12px;color:#6B7280">ID: ' + esc(c.accused_employee_id) + '</div>' : '') +
      '</div>';
    }
    html += '</div>';
  }

  // Verdict panel
  if (c.verdict) {
    html += '<div style="background:#FFFBEB;padding:14px;border-radius:10px;border:2px solid #F59E0B;margin-bottom:16px">' +
      '<div style="font-size:12px;font-weight:700;color:#78350F;margin-bottom:6px">⚖️ VERDICT</div>' +
      '<div style="font-size:16px;font-weight:800;margin-bottom:4px">' + verdictBadge(c.verdict) + '</div>' +
      (c.penalty_amount ? '<div style="font-size:13px"><strong>Penalty Amount:</strong> ' + money(c.penalty_amount) + '</div>' : '') +
      (c.penalty_details ? '<div style="font-size:13px;margin-top:4px">' + esc(c.penalty_details) + '</div>' : '') +
    '</div>';
  }

  // Admin Decision
  if (c.admin_decision) {
    html += '<div style="background:#EDE9FE;padding:12px;border-radius:8px;border:1px solid #C4B5FD;margin-bottom:16px">' +
      '<div style="font-size:11px;color:#5B21B6;text-transform:uppercase;font-weight:600">Admin Decision</div>' +
      '<div style="font-size:13px;margin-top:4px;font-weight:600">' + labelCase(c.admin_decision) + '</div>' +
    '</div>';
  }

  // Description
  html += '<div style="background:#F9FAFB;padding:12px;border-radius:8px;margin-bottom:12px">' +
    '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Description</div>' +
    '<div style="font-size:13px;margin-top:4px;white-space:pre-wrap">' + esc(c.description) + '</div>' +
  '</div>';

  if (c.resolution_summary) {
    html += '<div style="background:#F0FDF4;padding:12px;border-radius:8px;border:1px solid #A7F3D0;margin-bottom:12px">' +
      '<div style="font-size:12px;font-weight:600;color:#065F46">📋 Resolution Summary</div>' +
      '<div style="font-size:13px;margin-top:4px;white-space:pre-wrap">' + esc(c.resolution_summary) + '</div>' +
    '</div>';
  }

  // Hearing Log placeholder
  html += '<div style="margin-bottom:16px">' +
    '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">' +
      '<div style="font-weight:700;font-size:14px;color:#1F2937">🏛️ Hearing Log (' + (c.hearing_count || 0) + ')</div>' +
      '<button class="btn btn-outline btn-sm" onclick="openAddHearing(' + c.case_id + ')">+ Add Hearing</button>' +
    '</div>' +
    '<div id="case-hearings-list" style="font-size:13px;color:#6B7280">Loading hearings...</div>' +
  '</div>';

  // Evidence placeholder
  html += '<div style="margin-bottom:16px">' +
    '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">' +
      '<div style="font-weight:700;font-size:14px;color:#1F2937">📎 Evidence (' + (c.evidence_count || 0) + ')</div>' +
      '<button class="btn btn-outline btn-sm" onclick="openAddEvidence(' + c.case_id + ')">+ Add Evidence</button>' +
    '</div>' +
    '<div id="case-evidence-list" style="font-size:13px;color:#6B7280">Loading evidence...</div>' +
  '</div>';

  // Notices placeholder
  html += '<div style="margin-bottom:16px">' +
    '<div style="font-weight:700;font-size:14px;color:#1F2937;margin-bottom:8px">📨 Escalation Notices</div>' +
    '<div id="case-notices-list" style="font-size:13px;color:#6B7280">Loading notices...</div>' +
  '</div>';

  document.getElementById('case-detail-body').innerHTML = html;

  // Build footer actions based on workflow step
  let footerHtml = '<button class="btn btn-outline" onclick="closeModal(\'modal-case-detail\')">Close</button>';
  const ws = c.workflow_step;
  if (ws === 'complaint_filed') {
    footerHtml += ' <button class="btn" style="background:#3B82F6;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="advanceWorkflow(' + c.case_id + ',\'under_review\')">🔍 Start Review</button>';
    footerHtml += ' <button class="btn" style="background:#9CA3AF;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="advanceWorkflow(' + c.case_id + ',\'dismissed\')">🚫 Dismiss</button>';
  } else if (ws === 'under_review') {
    footerHtml += ' <button class="btn" style="background:#EF4444;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="advanceWorkflow(' + c.case_id + ',\'for_hearing\')">🏛️ Set for Hearing</button>';
    footerHtml += ' <button class="btn" style="background:#6366F1;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="advanceWorkflow(' + c.case_id + ',\'ongoing_investigation\')">🔎 Investigate</button>';
    footerHtml += ' <button class="btn" style="background:#9CA3AF;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="advanceWorkflow(' + c.case_id + ',\'dismissed\')">🚫 Dismiss</button>';
  } else if (ws === 'for_hearing') {
    footerHtml += ' <button class="btn" style="background:#6366F1;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="advanceWorkflow(' + c.case_id + ',\'ongoing_investigation\')">🔎 Investigate</button>';
    footerHtml += ' <button class="btn" style="background:#D97706;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="openRenderVerdict(' + c.case_id + ')">⚖️ Render Verdict</button>';
  } else if (ws === 'ongoing_investigation') {
    footerHtml += ' <button class="btn" style="background:#EF4444;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="advanceWorkflow(' + c.case_id + ',\'for_hearing\')">🏛️ Back to Hearing</button>';
    footerHtml += ' <button class="btn" style="background:#D97706;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="openRenderVerdict(' + c.case_id + ')">⚖️ Render Verdict</button>';
  } else if (ws === 'verdict') {
    footerHtml += ' <button class="btn" style="background:#059669;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;cursor:pointer" onclick="closeCase(' + c.case_id + ')">✅ Close Case</button>';
  }
  document.getElementById('case-detail-footer').innerHTML = footerHtml;

  openModal('modal-case-detail');

  // Load sub-data
  loadCaseHearings(c.case_id);
  loadCaseEvidence(c.case_id);
  loadCaseNotices(c.case_id);
}

// ===== Render: Contracts =====
// Contract folder type mapping
const contractFolderMap = {
  'employee': { types: ['employment', 'nda'], title: '👤 Employee Contracts', color: '#059669' },
  'vendor':   { types: ['vendor', 'service', 'lease', 'loan'], title: '🏢 Vendor Contracts', color: '#3B82F6' },
  'partnership': { types: ['partnership', 'other'], title: '🤝 Partnership Contracts', color: '#8B5CF6' }
};
let activeContractFolder = null;

function updateContractFolderCounts() {
  const empCount = contracts.filter(c => contractFolderMap.employee.types.includes(c.contract_type)).length;
  const vendCount = contracts.filter(c => contractFolderMap.vendor.types.includes(c.contract_type)).length;
  const partCount = contracts.filter(c => contractFolderMap.partnership.types.includes(c.contract_type)).length;
  document.getElementById('contract-count-employee').textContent = empCount + ' contract' + (empCount !== 1 ? 's' : '');
  document.getElementById('contract-count-vendor').textContent = vendCount + ' contract' + (vendCount !== 1 ? 's' : '');
  document.getElementById('contract-count-partnership').textContent = partCount + ' contract' + (partCount !== 1 ? 's' : '');
}

function showContractFolder(folderKey) {
  activeContractFolder = folderKey;
  const folder = contractFolderMap[folderKey];
  if (!folder) return;
  document.getElementById('contract-folders-grid').style.display = 'none';
  document.getElementById('contract-folder-panel').style.display = 'block';
  document.getElementById('contract-folder-title').textContent = folder.title;
  renderContracts();
}

function closeContractFolder() {
  activeContractFolder = null;
  document.getElementById('contract-folders-grid').style.display = 'grid';
  document.getElementById('contract-folder-panel').style.display = 'none';
}

function renderContracts() {
  const tb = document.getElementById('contracts-tbody');
  updateContractFolderCounts();
  let filtered = contracts;
  if (activeContractFolder && contractFolderMap[activeContractFolder]) {
    filtered = contracts.filter(c => contractFolderMap[activeContractFolder].types.includes(c.contract_type));
  }
  if (!filtered.length) { tb.innerHTML = emptyRow(10, 'No contracts found in this folder'); return; }
  tb.innerHTML = filtered.map(function(c) {
    var idx = contracts.indexOf(c);
    return '<tr>' +
      '<td><span class="secure-text" style="font-size:11px">' + esc(c.contract_number) + '</span></td>' +
      '<td style="font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + esc(c.title) + '</td>' +
      '<td>' + labelCase(c.contract_type) + '</td>' +
      '<td>' + esc(c.party_name) + '</td>' +
      '<td>' + money(c.value) + '</td>' +
      '<td>' + fmtDate(c.start_date) + '</td>' +
      '<td>' + fmtDate(c.end_date) + '</td>' +
      '<td>' + contractStatusBadge(c.status) + '</td>' +
      '<td>' + (esc(c.assigned_name) || '—') + '</td>' +
      '<td><button class="btn btn-outline btn-sm" onclick="viewContract(' + idx + ')">View</button></td>' +
    '</tr>';
  }).join('');
}

// ===== View: Contract =====
function viewContract(idx) {
  const c = contracts[idx];
  if (!c) return;
  document.getElementById('contract-detail-body').innerHTML =
    '<div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;flex-wrap:wrap;gap:12px">' +
      '<div>' +
        '<div style="font-weight:700;font-size:16px;color:#1F2937">' + esc(c.contract_number) + '</div>' +
        '<div style="font-weight:600;font-size:14px;color:#374151;margin-top:2px">' + esc(c.title) + '</div>' +
      '</div>' +
      '<div>' + contractStatusBadge(c.status) + '</div>' +
    '</div>' +
    '<div class="grid-2" style="gap:12px;margin-bottom:16px">' +
      '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
        '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Contract Info</div>' +
        '<div style="font-size:13px;margin-top:4px"><strong>Type:</strong> ' + labelCase(c.contract_type) + '</div>' +
        '<div style="font-size:13px"><strong>Party:</strong> ' + esc(c.party_name) + '</div>' +
        (c.party_contact ? '<div style="font-size:13px"><strong>Contact:</strong> ' + esc(c.party_contact) + '</div>' : '') +
        '<div style="font-size:13px"><strong>Value:</strong> ' + money(c.value) + ' ' + esc(c.currency || 'PHP') + '</div>' +
        '<div style="font-size:13px"><strong>Auto-Renew:</strong> ' + (c.auto_renew ? '✅ Yes' : 'No') + '</div>' +
      '</div>' +
      '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
        '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Duration</div>' +
        '<div style="font-size:13px;margin-top:4px"><strong>Start:</strong> ' + fmtDate(c.start_date) + '</div>' +
        '<div style="font-size:13px"><strong>End:</strong> ' + fmtDate(c.end_date) + '</div>' +
        (c.renewal_notice_days ? '<div style="font-size:13px"><strong>Renewal Notice:</strong> ' + c.renewal_notice_days + ' days</div>' : '') +
        (c.assigned_name ? '<div style="font-size:13px"><strong>Assigned To:</strong> ' + esc(c.assigned_name) + '</div>' : '') +
      '</div>' +
    '</div>' +
    (c.description ? '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
      '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Description</div>' +
      '<div style="font-size:13px;margin-top:4px;white-space:pre-wrap">' + esc(c.description) + '</div>' +
    '</div>' : '');
  openModal('modal-contract-detail');
}

// ===== Render: Permits =====
function renderPermits() {
  const tb = document.getElementById('permits-tbody');
  if (!permits.length) { tb.innerHTML = emptyRow(10, 'No permits found'); return; }
  tb.innerHTML = permits.map(function(p, i) {
    var expiring = isExpiringSoon(p.expiry_date);
    return '<tr' + (expiring ? ' style="background:#FFFBEB"' : '') + '>' +
      '<td><span class="secure-text" style="font-size:11px">' + esc(p.permit_code) + '</span></td>' +
      '<td style="font-weight:600">' + esc(p.permit_name) + '</td>' +
      '<td>' + esc(p.issuing_body) + '</td>' +
      '<td>' + labelCase(p.permit_type) + '</td>' +
      '<td>' + (esc(p.permit_number) || '—') + '</td>' +
      '<td>' + fmtDate(p.issue_date) + '</td>' +
      '<td>' + (expiring ? '<span class="badge badge-amber">⚠ ' + fmtDate(p.expiry_date) + '</span>' : fmtDate(p.expiry_date)) + '</td>' +
      '<td>' + money(p.renewal_fee) + '</td>' +
      '<td>' + permitStatusBadge(p.status) + '</td>' +
      '<td><button class="btn btn-outline btn-sm" onclick="viewPermit(' + i + ')">View</button></td>' +
    '</tr>';
  }).join('');
}

// ===== View: Permit =====
function viewPermit(idx) {
  const p = permits[idx];
  if (!p) return;
  document.getElementById('permit-detail-body').innerHTML =
    '<div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:16px;flex-wrap:wrap;gap:12px">' +
      '<div>' +
        '<div style="font-weight:700;font-size:16px;color:#1F2937">' + esc(p.permit_code) + '</div>' +
        '<div style="font-weight:600;font-size:14px;color:#374151;margin-top:2px">' + esc(p.permit_name) + '</div>' +
      '</div>' +
      '<div style="display:flex;gap:8px;flex-wrap:wrap">' +
        (isExpiringSoon(p.expiry_date) ? '<span class="badge badge-amber">⚠ Expiring Soon</span> ' : '') +
        permitStatusBadge(p.status) +
      '</div>' +
    '</div>' +
    '<div class="grid-2" style="gap:12px;margin-bottom:16px">' +
      '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
        '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Permit Info</div>' +
        '<div style="font-size:13px;margin-top:4px"><strong>Issuing Body:</strong> ' + esc(p.issuing_body) + '</div>' +
        '<div style="font-size:13px"><strong>Type:</strong> ' + labelCase(p.permit_type) + '</div>' +
        '<div style="font-size:13px"><strong>Permit #:</strong> ' + (esc(p.permit_number) || '—') + '</div>' +
        '<div style="font-size:13px"><strong>Renewal Fee:</strong> ' + money(p.renewal_fee) + '</div>' +
      '</div>' +
      '<div style="background:#F9FAFB;padding:12px;border-radius:8px">' +
        '<div style="font-size:11px;color:#9CA3AF;text-transform:uppercase;font-weight:600">Validity</div>' +
        '<div style="font-size:13px;margin-top:4px"><strong>Issued:</strong> ' + fmtDate(p.issue_date) + '</div>' +
        '<div style="font-size:13px"><strong>Expiry:</strong> ' + fmtDate(p.expiry_date) + '</div>' +
        (isExpiringSoon(p.expiry_date) ? '<div style="margin-top:8px;padding:6px 10px;background:#FEF3C7;border-radius:6px;font-size:12px;color:#92400E">⚠ This permit expires within 90 days. Please initiate renewal.</div>' : '') +
      '</div>' +
    '</div>' +
    (p.notes ? '<div style="font-size:13px;color:#6B7280"><strong>Notes:</strong> ' + esc(p.notes) + '</div>' : '');
  openModal('modal-permit-detail');
}

// ═══════════════════════════════════════════════════════
// LEGAL CALENDAR
// ═══════════════════════════════════════════════════════

let legalCalYear = new Date().getFullYear();
let legalCalMonth = new Date().getMonth();

function legalCalNav(dir) {
  legalCalMonth += dir;
  if (legalCalMonth > 11) { legalCalMonth = 0; legalCalYear++; }
  if (legalCalMonth < 0) { legalCalMonth = 11; legalCalYear--; }
  renderLegalCalendar();
}

function legalCalToday() {
  legalCalYear = new Date().getFullYear();
  legalCalMonth = new Date().getMonth();
  renderLegalCalendar();
}

function getLegalEvents() {
  const events = [];
  const colorMap = { case:'#EF4444', contract:'#3B82F6', compliance:'#059669', permit:'#F59E0B', demand:'#EC4899', board:'#6366F1' };
  // Cases: filing dates, next hearing
  (cases || []).forEach(c => {
    if (c.filing_date) events.push({ date: c.filing_date.slice(0,10), type:'case', color:colorMap.case, label: c.title || c.case_number, detail:c });
    if (c.next_hearing) events.push({ date: c.next_hearing.slice(0,10), type:'case', color:colorMap.case, label:'🔔 Hearing: ' + (c.title || c.case_number), detail:c });
  });
  // Contracts: start/end dates
  (contracts || []).forEach(c => {
    if (c.start_date) events.push({ date: c.start_date.slice(0,10), type:'contract', color:colorMap.contract, label:'📝 Start: ' + (c.title || c.contract_number), detail:c });
    if (c.end_date) events.push({ date: c.end_date.slice(0,10), type:'contract', color:colorMap.contract, label:'📝 End: ' + (c.title || c.contract_number), detail:c });
  });
  // Compliance: deadlines
  (compliance || []).forEach(c => {
    if (c.deadline) events.push({ date: c.deadline.slice(0,10), type:'compliance', color:colorMap.compliance, label:'✅ ' + (c.requirement || c.compliance_code), detail:c });
  });
  // Permits: expiry dates
  (permits || []).forEach(p => {
    if (p.expiry_date) events.push({ date: p.expiry_date.slice(0,10), type:'permit', color:colorMap.permit, label:'📜 Expiry: ' + (p.permit_name || p.permit_code), detail:p });
  });
  // Demands: response deadlines
  (demands || []).forEach(d => {
    if (d.response_deadline) events.push({ date: d.response_deadline.slice(0,10), type:'demand', color:colorMap.demand, label:'⚠ Demand: ' + (d.borrower_name || d.demand_code), detail:d });
  });
  // Board resolutions: meeting dates
  (resolutions || []).forEach(r => {
    if (r.meeting_date) events.push({ date: r.meeting_date.slice(0,10), type:'board', color:colorMap.board, label:'📋 Board: ' + (r.title || r.resolution_code), detail:r });
  });
  return events;
}

function renderLegalCalendar() {
  const grid = document.getElementById('legal-calendar-grid');
  if (!grid) return;
  const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
  document.getElementById('legal-cal-title').textContent = monthNames[legalCalMonth] + ' ' + legalCalYear;

  const events = getLegalEvents();
  const firstDay = new Date(legalCalYear, legalCalMonth, 1).getDay();
  const daysInMonth = new Date(legalCalYear, legalCalMonth + 1, 0).getDate();
  const today = new Date();
  const todayStr = today.getFullYear() + '-' + String(today.getMonth()+1).padStart(2,'0') + '-' + String(today.getDate()).padStart(2,'0');

  let html = '';
  // Day-of-week headers
  ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(d => {
    html += '<div style="background:#F3F4F6;padding:8px 4px;text-align:center;font-size:11px;font-weight:700;color:#6B7280;border-bottom:1px solid #E5E7EB">' + d + '</div>';
  });

  // Empty cells before first day
  for (let i = 0; i < firstDay; i++) {
    html += '<div style="background:#FAFAFA;padding:6px;min-height:90px"></div>';
  }

  // Day cells
  for (let day = 1; day <= daysInMonth; day++) {
    const dateStr = legalCalYear + '-' + String(legalCalMonth+1).padStart(2,'0') + '-' + String(day).padStart(2,'0');
    const dayEvents = events.filter(e => e.date === dateStr);
    const isToday = dateStr === todayStr;
    const isPast  = dateStr < todayStr;
    const hasEvents = dayEvents.length > 0;

    let cellBg = isToday ? '#F0FDF4' : (hasEvents ? '#FFFBEB' : (isPast ? '#FAFAFA' : 'white'));
    let cellExtra = isToday ? 'outline:2px solid #059669;outline-offset:-2px;' : '';

    html += '<div style="background:' + cellBg + ';' + cellExtra + 'padding:6px;min-height:90px;vertical-align:top;cursor:' + (hasEvents ? 'pointer' : 'default') + '" onclick="showLegalDayEvents(\'' + dateStr + '\')">';
    html += '<div style="font-size:12px;font-weight:' + (isToday ? '800' : '600') + ';color:' + (isToday ? '#059669' : isPast ? '#9CA3AF' : '#1F2937') + ';margin-bottom:3px;line-height:1">' + day + (isToday ? '<span style="font-size:9px;font-weight:700;color:#059669;margin-left:3px">●</span>' : '') + '</div>';
    if (hasEvents) {
      html += '<div style="display:flex;flex-direction:column;gap:2px">';
      dayEvents.slice(0, 3).forEach(e => {
        html += '<div style="font-size:9px;padding:2px 4px;border-radius:3px;background:' + e.color + '18;color:' + e.color + ';overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:600;border-left:2px solid ' + e.color + '">' + esc(e.label.substring(0,22)) + '</div>';
      });
      if (dayEvents.length > 3) {
        html += '<div style="font-size:9px;color:#6B7280;font-weight:600;padding-left:4px">+' + (dayEvents.length - 3) + ' more</div>';
      }
      html += '</div>';
    }
    html += '</div>';
  }

  grid.innerHTML = html;
  renderUpcomingDeadlines();
}

function renderUpcomingDeadlines() {
  const body  = document.getElementById('upcoming-deadlines-body');
  const badge = document.getElementById('deadline-count-badge');
  if (!body) return;

  const todayStr  = new Date().toISOString().slice(0,10);
  const cutoff    = new Date(); cutoff.setDate(cutoff.getDate() + 90);
  const cutoffStr = cutoff.toISOString().slice(0,10);

  const typeLabel = { case:'Case', contract:'Contract', compliance:'Compliance', permit:'Permit', demand:'Demand', board:'Board Resolution' };
  const colorMap  = { case:'#EF4444', contract:'#3B82F6', compliance:'#059669', permit:'#F59E0B', demand:'#EC4899', board:'#6366F1' };

  const upcoming = getLegalEvents()
    .filter(e => e.date >= todayStr && e.date <= cutoffStr)
    .sort((a, b) => a.date.localeCompare(b.date));

  if (badge) badge.textContent = upcoming.length;

  if (!upcoming.length) {
    body.innerHTML = '<div style="text-align:center;padding:32px 16px;color:#9CA3AF"><div style="font-size:28px;margin-bottom:8px">🎉</div><div style="font-size:13px;font-weight:600">No deadlines in the next 90 days</div></div>';
    return;
  }

  // Group by date
  const byDate = {};
  upcoming.forEach(e => { if (!byDate[e.date]) byDate[e.date] = []; byDate[e.date].push(e); });

  const todayDt = new Date(); todayDt.setHours(0,0,0,0);
  let html = '';
  Object.keys(byDate).sort().forEach(dateStr => {
    const dt      = new Date(dateStr + 'T00:00:00');
    const diffDay = Math.round((dt - todayDt) / 86400000);
    const isToday    = diffDay === 0;
    const isTomorrow = diffDay === 1;
    const isUrgent   = diffDay <= 3;
    const isSoon     = diffDay <= 7;

    let dayLabel, pillBg = '#F3F4F6', pillColor = '#6B7280';
    if (isToday)       { dayLabel = 'TODAY'; pillBg = '#FEE2E2'; pillColor = '#DC2626'; }
    else if (isTomorrow){ dayLabel = 'TOMORROW'; pillBg = '#FEF3C7'; pillColor = '#D97706'; }
    else if (isUrgent) { dayLabel = 'In ' + diffDay + ' days'; pillBg = '#FEF3C7'; pillColor = '#D97706'; }
    else if (isSoon)   { dayLabel = 'In ' + diffDay + ' days'; pillBg = '#DBEAFE'; pillColor = '#1D4ED8'; }
    else               { dayLabel = 'In ' + diffDay + ' days'; }

    html += '<div style="display:flex;align-items:center;gap:8px;margin-top:6px;margin-bottom:2px">';
    html += '<span style="font-size:11px;font-weight:700;color:#4B5563">' + esc(fmtDate(dateStr)) + '</span>';
    html += '<span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:50px;background:' + pillBg + ';color:' + pillColor + '">' + esc(dayLabel) + '</span>';
    html += '</div>';

    byDate[dateStr].forEach(e => {
      const c = colorMap[e.type] || '#6B7280';
      // Compute view button onclick
      let viewOnclick = '';
      if (e.type === 'case')       { const i = cases.indexOf(e.detail);       if (i !== -1) viewOnclick = 'viewCase(' + i + ')'; }
      else if (e.type === 'demand')   { const i = demands.indexOf(e.detail);    if (i !== -1) viewOnclick = 'viewDemand(' + i + ')'; }
      else if (e.type === 'contract') { const i = contracts.indexOf(e.detail);  if (i !== -1) viewOnclick = 'viewContract(' + i + ')'; }
      else if (e.type === 'permit')   { const i = permits.indexOf(e.detail);    if (i !== -1) viewOnclick = 'viewPermit(' + i + ')'; }
      else if (e.type === 'compliance'){ const i = compliance.indexOf(e.detail);if (i !== -1) viewOnclick = 'viewComplianceRecord(' + i + ')'; }
      else if (e.type === 'board')    { const i = resolutions.indexOf(e.detail);if (i !== -1) viewOnclick = 'viewResolutionRecord(' + i + ')'; }

      html += '<div style="display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:8px;background:' + c + '08;border:1px solid ' + c + '22;border-left:3px solid ' + c + '">';
      html += '<div style="flex:1;min-width:0">';
      html += '<div style="font-size:12px;font-weight:600;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + esc(e.label) + '</div>';
      html += '<div style="font-size:10px;color:' + c + ';font-weight:600;margin-top:1px">' + esc(typeLabel[e.type] || e.type) + '</div>';
      html += '</div>';
      if (viewOnclick) {
        html += '<button onclick="' + viewOnclick + '" style="flex-shrink:0;font-size:10px;padding:3px 8px;border-radius:6px;border:1px solid ' + c + ';color:' + c + ';background:white;cursor:pointer;white-space:nowrap;font-weight:600">👁 View</button>';
      }
      html += '</div>';
    });
  });

  body.innerHTML = html;
}

function showLegalDayEvents(dateStr) {
  const events = getLegalEvents().filter(e => e.date === dateStr);
  if (!events.length) return;

  const panel = document.getElementById('legal-day-panel');
  panel.style.display = 'block';
  document.getElementById('legal-day-title').textContent = '📅 ' + fmtDate(dateStr) + ' — ' + events.length + ' record(s)';

  let html = '';
  events.forEach(e => {
    const colorMap  = { case:'#EF4444', contract:'#3B82F6', compliance:'#059669', permit:'#F59E0B', demand:'#EC4899', board:'#6366F1' };
    const typeLabel = { case:'Case/Litigation', contract:'Contract', compliance:'Compliance/Governance', permit:'Permit/License', demand:'Demand Letter', board:'Board Resolution' };
    const c = colorMap[e.type] || '#6B7280';
    html += '<div style="border:1px solid #E5E7EB;border-radius:10px;padding:12px 16px;margin-bottom:8px;border-left:4px solid ' + c + '">';
    html += '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">';
    html += '<span style="font-weight:700;font-size:13px;color:#1F2937">' + esc(e.label) + '</span>';
    html += '<span class="badge" style="background:' + c + '20;color:' + c + ';font-size:10px">' + (typeLabel[e.type] || e.type) + '</span>';
    html += '</div>';
    // Show key details based on type
    const d = e.detail;
    if (e.type === 'case' && d) {
      html += '<div style="font-size:12px;color:#6B7280">';
      if (d.case_number) html += '<strong>#</strong> ' + esc(d.case_number) + ' · ';
      if (d.status) html += 'Status: ' + esc(d.status) + ' · ';
      if (d.opposing_party) html += 'vs. ' + esc(d.opposing_party);
      html += '</div>';
    } else if (e.type === 'contract' && d) {
      html += '<div style="font-size:12px;color:#6B7280">';
      if (d.contract_number) html += '<strong>#</strong> ' + esc(d.contract_number) + ' · ';
      if (d.party_name) html += 'Party: ' + esc(d.party_name) + ' · ';
      if (d.status) html += 'Status: ' + esc(d.status);
      html += '</div>';
    } else if (e.type === 'compliance' && d) {
      html += '<div style="font-size:12px;color:#6B7280">';
      if (d.regulatory_body) html += 'Body: ' + esc(d.regulatory_body) + ' · ';
      if (d.status) html += 'Status: ' + esc(d.status);
      if (d.resolution_code) html += '<strong>#</strong> ' + esc(d.resolution_code) + ' · Type: ' + esc(d.resolution_type || '');
      html += '</div>';
    } else if (e.type === 'permit' && d) {
      html += '<div style="font-size:12px;color:#6B7280">';
      if (d.issuing_body) html += 'Issuer: ' + esc(d.issuing_body) + ' · ';
      if (d.permit_number) html += 'Permit #' + esc(d.permit_number) + ' · ';
      if (d.status) html += 'Status: ' + esc(d.status);
      html += '</div>';
    } else if (e.type === 'demand' && d) {
      html += '<div style="font-size:12px;color:#6B7280">';
      if (d.demand_code) html += '<strong>#</strong> ' + esc(d.demand_code) + ' · ';
      if (d.borrower_name) html += 'Borrower: ' + esc(d.borrower_name) + ' · ';
      if (d.demand_amount) html += 'Amount: ' + esc(d.demand_amount) + ' · ';
      if (d.status) html += 'Status: ' + esc(d.status);
      html += '</div>';
    } else if (e.type === 'board' && d) {
      html += '<div style="font-size:12px;color:#6B7280">';
      if (d.resolution_code) html += '<strong>#</strong> ' + esc(d.resolution_code) + ' · ';
      if (d.resolution_type) html += 'Type: ' + esc(d.resolution_type) + ' · ';
      if (d.status) html += 'Status: ' + esc(d.status);
      html += '</div>';
    }
    // View button
    let viewBtn = '';
    if (e.type === 'case') {
      const idx = cases.indexOf(e.detail);
      if (idx !== -1) viewBtn = '<button class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 12px;margin-top:8px;border-color:' + c + ';color:' + c + '" onclick="viewCase(' + idx + ')">👁 View Case</button>';
    } else if (e.type === 'demand') {
      const idx = demands.indexOf(e.detail);
      if (idx !== -1) viewBtn = '<button class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 12px;margin-top:8px;border-color:' + c + ';color:' + c + '" onclick="viewDemand(' + idx + ')">👁 View Demand</button>';
    } else if (e.type === 'contract') {
      const idx = contracts.indexOf(e.detail);
      if (idx !== -1) viewBtn = '<button class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 12px;margin-top:8px;border-color:' + c + ';color:' + c + '" onclick="viewContract(' + idx + ')">👁 View Contract</button>';
    } else if (e.type === 'permit') {
      const idx = permits.indexOf(e.detail);
      if (idx !== -1) viewBtn = '<button class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 12px;margin-top:8px;border-color:' + c + ';color:' + c + '" onclick="viewPermit(' + idx + ')">👁 View Permit</button>';
    } else if (e.type === 'compliance') {
      const idx = compliance.indexOf(e.detail);
      if (idx !== -1) viewBtn = '<button class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 12px;margin-top:8px;border-color:' + c + ';color:' + c + '" onclick="viewComplianceRecord(' + idx + ')">👁 View Compliance</button>';
    } else if (e.type === 'board') {
      const idx = resolutions.indexOf(e.detail);
      if (idx !== -1) viewBtn = '<button class="btn btn-outline btn-sm" style="font-size:11px;padding:4px 12px;margin-top:8px;border-color:' + c + ';color:' + c + '" onclick="viewResolutionRecord(' + idx + ')">👁 View Resolution</button>';
    }
    if (viewBtn) html += viewBtn;
    html += '</div>';
  });

  document.getElementById('legal-day-body').innerHTML = html;
  panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function viewComplianceRecord(idx) {
  const r = compliance[idx];
  if (!r) return;
  Swal.fire({
    title: '<strong>Compliance / Governance</strong>',
    html:
      '<div style="text-align:left;font-size:13px;line-height:1.8">' +
      (r.compliance_code ? '<div><strong>Code:</strong> ' + esc(r.compliance_code) + '</div>' : '') +
      (r.requirement    ? '<div><strong>Requirement:</strong> ' + esc(r.requirement) + '</div>' : '') +
      (r.regulatory_body? '<div><strong>Regulatory Body:</strong> ' + esc(r.regulatory_body) + '</div>' : '') +
      (r.deadline        ? '<div><strong>Deadline:</strong> ' + fmtDate(r.deadline) + '</div>' : '') +
      (r.status          ? '<div><strong>Status:</strong> ' + esc(r.status) + '</div>' : '') +
      (r.description     ? '<div style="margin-top:8px;padding:10px;background:#F9FAFB;border-radius:8px;font-size:12px">' + esc(r.description) + '</div>' : '') +
      '</div>',
    icon: 'info',
    confirmButtonColor: '#059669',
    confirmButtonText: 'Close',
    width: 480
  });
}

function viewResolutionRecord(idx) {
  const r = resolutions[idx];
  if (!r) return;
  Swal.fire({
    title: '<strong>Board Resolution</strong>',
    html:
      '<div style="text-align:left;font-size:13px;line-height:1.8">' +
      (r.resolution_code ? '<div><strong>Code:</strong> ' + esc(r.resolution_code) + '</div>' : '') +
      (r.title           ? '<div><strong>Title:</strong> ' + esc(r.title) + '</div>' : '') +
      (r.resolution_type ? '<div><strong>Type:</strong> ' + esc(r.resolution_type) + '</div>' : '') +
      (r.meeting_date    ? '<div><strong>Meeting Date:</strong> ' + fmtDate(r.meeting_date) + '</div>' : '') +
      (r.status          ? '<div><strong>Status:</strong> ' + esc(r.status) + '</div>' : '') +
      (r.summary         ? '<div style="margin-top:8px;padding:10px;background:#F9FAFB;border-radius:8px;font-size:12px">' + esc(r.summary) + '</div>' : '') +
      '</div>',
    icon: 'info',
    confirmButtonColor: '#6366F1',
    confirmButtonText: 'Close',
    width: 480
  });
}

// ===== Legal Tab Names =====
const legalTabNames = {
  'tab-loans': 'Loan Documentation',
  'tab-cases': 'Case Tracking',
  'tab-contracts': 'Contracts',
  'tab-permits': 'Permits & Licenses',
  'tab-legal-calendar': 'Legal Calendar'
};
const legalTabIds = Object.keys(legalTabNames);

// ===== Section Switching (hash-driven) =====
function showSection(hash) {
  var sections = document.querySelectorAll('.tab-content');
  var id = hash ? hash.replace('#', '') : 'tab-loans';

  // Highlight active directory card
  document.querySelectorAll('.module-directory-label + .stats-grid .stat-card-link').forEach(function(c) {
    var href = c.getAttribute('href') || '';
    if (href === '#' + id) { c.classList.add('active-module'); var a = c.querySelector('.stat-arrow'); if(a) a.textContent = '●'; }
    else { c.classList.remove('active-module'); var a = c.querySelector('.stat-arrow'); if(a) a.textContent = '→'; }
  });

  sections.forEach(function(s) { s.classList.remove('active'); });
  var target = document.getElementById(id);
  if (target) target.classList.add('active');
  else if (sections[0]) sections[0].classList.add('active');
  if (id === 'tab-legal-calendar') renderLegalCalendar();

  // Security check for all legal tabs
  if (legalTabIds.includes(id)) {
    // Hide content immediately to prevent flash before access check
    if (target) target.style.display = 'none';
    checkLegalAccess(id);
  } else {
    document.getElementById('legal-pin-gate').style.display = 'none';
  }
}
window.addEventListener('hashchange', function() { showSection(location.hash); });

// ===== Init =====
document.addEventListener('DOMContentLoaded', function() {
  loadData().then(function() {
    showSection(location.hash);
  });
});


// ═══════════════════════════════════════════════════════
// LEGAL SECURITY PIN (shared gate for all submodules)
// ═══════════════════════════════════════════════════════

let legalPinVerified = false;
let lpinCountdownInterval = null;
let currentLegalTab = 'tab-loans';

async function checkLegalAccess(tabId) {
  currentLegalTab = tabId || 'tab-loans';
  const label = legalTabNames[currentLegalTab] || 'Legal Module';
  const el = document.getElementById('lpin-tab-label');
  if (el) el.textContent = label;

  try {
    const res = await fetch(API + '?action=check_legal_access&tab=legal');
    const json = await res.json();
    if (json.verified) {
      legalPinVerified = true;
      showLegalContent(tabId);
    } else {
      legalPinVerified = false;
      document.getElementById('legal-pin-gate').style.display = '';
      // Hide the active tab content
      var target = document.getElementById(tabId);
      if (target) target.style.display = 'none';
    }
  } catch (err) {
    console.error('Check legal access error:', err);
  }
}

async function requestLegalPin() {
  try {
    const reqBtn = document.querySelector('#lpin-step-request button');
    if (reqBtn) { reqBtn.disabled = true; reqBtn.textContent = 'Sending...'; }
    const label = legalTabNames[currentLegalTab] || 'Legal Module';

    const res = await fetch(API + '?action=send_legal_pin&tab=' + encodeURIComponent(label), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({})
    });
    const json = await res.json();

    if (json.success) {
      // SweetAlert: Legal OTP sent successfully
      Swal.fire({
        icon: 'success',
        title: 'OTP Sent!',
        text: json.mail_failed ? 'Security PIN generated. Check fallback code below.' : 'A security PIN has been sent to your registered email for legal access.',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false,
        confirmButtonColor: '#D97706',
        customClass: { popup: 'rounded-2xl' }
      });

      document.getElementById('lpin-step-request').style.display = 'none';
      document.getElementById('lpin-step-verify').style.display = '';

      if (json.mail_failed && json.fallback_pin) {
        document.getElementById('lpin-fallback-display').style.display = '';
        document.getElementById('lpin-fallback-code').textContent = json.fallback_pin;
      } else {
        document.getElementById('lpin-fallback-display').style.display = 'none';
      }

      for (let i = 1; i <= 4; i++) document.getElementById('lpin-digit-' + i).value = '';
      document.getElementById('lpin-digit-1').focus();
      startLegalPinCountdown(json.expires_in || 120);
    }
  } catch (err) {
    console.error('Request legal PIN error:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not send security PIN.', confirmButtonColor: '#D97706' });
  } finally {
    const reqBtn = document.querySelector('#lpin-step-request button');
    if (reqBtn) { reqBtn.disabled = false; reqBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Send Security PIN'; }
  }
}

async function verifyLegalPin() {
  const pin = [1,2,3,4].map(i => document.getElementById('lpin-digit-' + i).value).join('');
  if (pin.length !== 4) {
    Swal.fire({ icon: 'warning', title: 'Incomplete PIN', text: 'Please enter all 4 digits.', confirmButtonColor: '#D97706' });
    return;
  }
  try {
    const res = await fetch(API + '?action=verify_legal_pin', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ pin: pin, tab: 'legal' })
    });
    const json = await res.json();
    if (json.success) {
      legalPinVerified = true;
      if (lpinCountdownInterval) clearInterval(lpinCountdownInterval);
      Swal.fire({ icon: 'success', title: 'Verified!', text: 'Legal access granted.', confirmButtonColor: '#D97706', timer: 1500 });
      showLegalContent(currentLegalTab);
    } else if (json.expired) {
      Swal.fire({ icon: 'warning', title: 'PIN Expired', text: 'Please request a new PIN.', confirmButtonColor: '#D97706' });
      resetLegalPinUI();
    } else {
      Swal.fire({ icon: 'error', title: 'Invalid PIN', text: json.message || 'Please try again.', confirmButtonColor: '#D97706' });
      for (let i = 1; i <= 4; i++) document.getElementById('lpin-digit-' + i).value = '';
      document.getElementById('lpin-digit-1').focus();
    }
  } catch (err) {
    console.error('Verify legal PIN error:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Verification failed.', confirmButtonColor: '#D97706' });
  }
}

function showLegalContent(tabId) {
  document.getElementById('legal-pin-gate').style.display = 'none';
  // Show the tab content
  var target = document.getElementById(tabId);
  if (target) target.style.display = '';
  if (tabId === 'tab-legal-calendar') renderLegalCalendar();
}

function resetLegalPinUI() {
  document.getElementById('lpin-step-request').style.display = '';
  document.getElementById('lpin-step-verify').style.display = 'none';
  if (lpinCountdownInterval) clearInterval(lpinCountdownInterval);
}

function startLegalPinCountdown(seconds) {
  let remaining = seconds;
  const el = document.getElementById('lpin-countdown');
  if (lpinCountdownInterval) clearInterval(lpinCountdownInterval);
  el.textContent = remaining;
  lpinCountdownInterval = setInterval(() => {
    remaining--;
    el.textContent = remaining;
    if (remaining <= 0) {
      clearInterval(lpinCountdownInterval);
      Swal.fire({ icon: 'warning', title: 'PIN Expired', text: 'Please request a new PIN.', confirmButtonColor: '#D97706' });
      resetLegalPinUI();
    }
  }, 1000);
}

function lpinAutoFocus(el, nextNum) {
  el.value = el.value.replace(/[^0-9]/g, '');
  if (el.value && nextNum) document.getElementById('lpin-digit-' + nextNum).focus();
  if (!nextNum && el.value) {
    const pin = [1,2,3,4].map(i => document.getElementById('lpin-digit-' + i).value).join('');
    if (pin.length === 4) verifyLegalPin();
  }
}

function lpinKeyNav(event, currentNum) {
  if (event.key === 'Backspace' && !event.target.value && currentNum > 1) {
    document.getElementById('lpin-digit-' + (currentNum - 1)).focus();
  }
  if (event.key === 'Enter') verifyLegalPin();
}


// ===== Export Functions =====
function exportLoans(format) {
  const headers = ['Code', 'Borrower', 'Amount', 'Interest', 'Term', 'Security Type', 'Status', 'Attorney'];
  const rows = loans.map(l => [
    l.loan_code || '', l.borrower_name || '', money(l.loan_amount), (l.interest_rate || '') + '%',
    l.loan_term || '', l.security_type || '', l.status || '', l.assigned_attorney || ''
  ]);
  format === 'csv' ? ExportHelper.exportCSV('Legal_Loans', headers, rows)
    : ExportHelper.exportPDF('Legal_Loans', 'Legal — Loan Documentation', headers, rows, { landscape: true, subtitle: loans.length + ' records' });
}

function exportDemands(format) {
  const headers = ['Code', 'Borrower', 'Amount', 'Type', 'Status', 'Sent Date', 'Response Deadline'];
  const rows = demands.map(d => [
    d.demand_code || '', d.borrower_name || '', money(d.demand_amount), d.demand_type || '',
    d.status || '', fmtDate(d.sent_date), fmtDate(d.response_deadline)
  ]);
  format === 'csv' ? ExportHelper.exportCSV('Legal_Demands', headers, rows)
    : ExportHelper.exportPDF('Legal_Demands', 'Legal — Demand Letters', headers, rows, { subtitle: demands.length + ' records' });
}

// ═══════════════════════════════════════════════════════
// CASE WORKFLOW FUNCTIONS
// ═══════════════════════════════════════════════════════

function openFileComplaint() {
  // Reset form
  ['fc-title','fc-complainant-name','fc-complainant-dept','fc-accused-name','fc-accused-dept','fc-accused-empid','fc-financial','fc-lawyer','fc-officer','fc-opposing','fc-venue','fc-description','fc-department'].forEach(id => {
    const el = document.getElementById(id); if (el) el.value = '';
  });
  document.getElementById('fc-case-type').value = '';
  document.getElementById('fc-priority').value = 'medium';
  document.querySelector('input[name="fc-severity"][value="moderate"]').checked = true;
  // Populate linked loan dropdown
  const loanSel = document.getElementById('fc-linked-loan');
  loanSel.innerHTML = '<option value="">None</option>' + loans.map(l =>
    '<option value="' + l.loan_doc_id + '">' + esc(l.loan_doc_code) + ' — ' + esc(l.borrower_name) + ' (' + money(l.loan_amount) + ')</option>'
  ).join('');
  openModal('modal-file-complaint');
}

async function submitComplaint() {
  const title = document.getElementById('fc-title').value.trim();
  const caseType = document.getElementById('fc-case-type').value;
  const desc = document.getElementById('fc-description').value.trim();
  if (!title || !caseType) {
    Swal.fire({ icon: 'warning', title: 'Required', text: 'Title and case type are required.', confirmButtonColor: '#059669' });
    return;
  }
  const severity = document.querySelector('input[name="fc-severity"]:checked')?.value || 'moderate';
  const body = {
    title, case_type: caseType, description: desc, severity,
    priority: document.getElementById('fc-priority').value,
    complainant_name: document.getElementById('fc-complainant-name').value.trim() || null,
    complainant_department: document.getElementById('fc-complainant-dept').value.trim() || null,
    accused_name: document.getElementById('fc-accused-name').value.trim() || null,
    accused_department: document.getElementById('fc-accused-dept').value.trim() || null,
    accused_employee_id: document.getElementById('fc-accused-empid').value.trim() || null,
    financial_impact: parseFloat(document.getElementById('fc-financial').value) || 0,
    assigned_lawyer: document.getElementById('fc-lawyer').value.trim() || null,
    legal_officer: document.getElementById('fc-officer').value.trim() || null,
    opposing_party: document.getElementById('fc-opposing').value.trim() || null,
    court_venue: document.getElementById('fc-venue').value.trim() || null,
    department: document.getElementById('fc-department').value.trim() || null,
    linked_loan_id: document.getElementById('fc-linked-loan').value || null
  };
  try {
    const res = await fetch(API + '?action=file_complaint', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body)
    });
    const data = await res.json();
    if (data.success) {
      closeModal('modal-file-complaint');
      Swal.fire({ icon: 'success', title: 'Complaint Filed', text: 'Case ' + data.case_number + ' has been created.', confirmButtonColor: '#059669', timer: 3000, showConfirmButton: false });
      loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'Failed to file complaint.', confirmButtonColor: '#059669' });
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Network Error', text: e.message, confirmButtonColor: '#059669' });
  }
}

async function advanceWorkflow(caseId, newStep) {
  const labels = {
    under_review: 'Start Review', for_hearing: 'Set for Hearing', ongoing_investigation: 'Begin Investigation',
    verdict: 'Proceed to Verdict', closed: 'Close Case', dismissed: 'Dismiss Case'
  };
  const confirm = await Swal.fire({
    icon: 'question', title: labels[newStep] || 'Advance Workflow',
    text: 'Move this case to "' + labelCase(newStep) + '"?',
    showCancelButton: true, confirmButtonColor: '#059669', confirmButtonText: 'Yes, proceed'
  });
  if (!confirm.isConfirmed) return;
  try {
    const res = await fetch(API + '?action=update_workflow', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ case_id: caseId, workflow_step: newStep })
    });
    const data = await res.json();
    if (data.success) {
      closeModal('modal-case-detail');
      Swal.fire({ icon: 'success', title: 'Updated', text: 'Case moved to ' + labelCase(newStep), confirmButtonColor: '#059669', timer: 2500, showConfirmButton: false });
      loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'Failed', confirmButtonColor: '#059669' });
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Network Error', text: e.message, confirmButtonColor: '#059669' });
  }
}

async function closeCase(caseId) {
  const { value: summary } = await Swal.fire({
    title: 'Close Case', input: 'textarea', inputLabel: 'Resolution Summary (optional)',
    inputPlaceholder: 'Summarize the resolution...', showCancelButton: true,
    confirmButtonColor: '#059669', confirmButtonText: 'Close Case'
  });
  if (summary === undefined) return; // cancelled
  try {
    const res = await fetch(API + '?action=close_case', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ case_id: caseId, resolution_summary: summary || null })
    });
    const data = await res.json();
    if (data.success) {
      closeModal('modal-case-detail');
      Swal.fire({ icon: 'success', title: 'Case Closed', confirmButtonColor: '#059669', timer: 2500, showConfirmButton: false });
      loadData();
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#059669' });
  }
}

function openAddHearing(caseId) {
  document.getElementById('ah-case-id').value = caseId;
  ['ah-date','ah-location','ah-officer','ah-attendees','ah-witnesses','ah-minutes','ah-outcome','ah-next-action'].forEach(id => {
    const el = document.getElementById(id); if (el) el.value = '';
  });
  document.getElementById('ah-type').value = 'initial_review';
  openModal('modal-add-hearing');
}

async function submitHearing() {
  const caseId = document.getElementById('ah-case-id').value;
  const hearingDate = document.getElementById('ah-date').value;
  if (!caseId || !hearingDate) {
    Swal.fire({ icon: 'warning', title: 'Required', text: 'Hearing date is required.', confirmButtonColor: '#059669' });
    return;
  }
  const body = {
    case_id: parseInt(caseId), hearing_date: hearingDate,
    hearing_type: document.getElementById('ah-type').value,
    location: document.getElementById('ah-location').value.trim() || null,
    officer_name: document.getElementById('ah-officer').value.trim() || null,
    attendees: document.getElementById('ah-attendees').value.trim() || null,
    witnesses: document.getElementById('ah-witnesses').value.trim() || null,
    minutes: document.getElementById('ah-minutes').value.trim() || null,
    outcome: document.getElementById('ah-outcome').value.trim() || null,
    next_action: document.getElementById('ah-next-action').value.trim() || null
  };
  try {
    const res = await fetch(API + '?action=add_hearing', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body)
    });
    const data = await res.json();
    if (data.success) {
      closeModal('modal-add-hearing');
      Swal.fire({ icon: 'success', title: 'Hearing Saved', confirmButtonColor: '#059669', timer: 2000, showConfirmButton: false });
      loadCaseHearings(caseId);
      loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'Failed', confirmButtonColor: '#059669' });
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#059669' });
  }
}

function openRenderVerdict(caseId) {
  document.getElementById('rv-case-id').value = caseId;
  document.getElementById('rv-verdict').value = '';
  document.getElementById('rv-amount').value = '';
  document.getElementById('rv-details').value = '';
  document.getElementById('rv-summary').value = '';
  openModal('modal-render-verdict');
}

async function submitVerdict() {
  const caseId = document.getElementById('rv-case-id').value;
  const verdict = document.getElementById('rv-verdict').value;
  if (!verdict) {
    Swal.fire({ icon: 'warning', title: 'Required', text: 'Select a verdict.', confirmButtonColor: '#059669' });
    return;
  }
  const body = {
    case_id: parseInt(caseId), verdict,
    penalty_amount: parseFloat(document.getElementById('rv-amount').value) || null,
    penalty_details: document.getElementById('rv-details').value.trim() || null,
    resolution_summary: document.getElementById('rv-summary').value.trim() || null
  };
  try {
    const res = await fetch(API + '?action=render_verdict', {
      method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(body)
    });
    const data = await res.json();
    if (data.success) {
      closeModal('modal-render-verdict');
      closeModal('modal-case-detail');
      Swal.fire({ icon: 'success', title: 'Verdict Rendered', confirmButtonColor: '#059669', timer: 2500, showConfirmButton: false });
      loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: data.error || 'Failed', confirmButtonColor: '#059669' });
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#059669' });
  }
}

async function loadCaseHearings(caseId) {
  try {
    const res = await fetch(API + '?action=list_hearings&case_id=' + caseId);
    const data = await res.json();
    const list = data.data || [];
    const el = document.getElementById('case-hearings-list');
    if (!list.length) { el.innerHTML = '<div style="text-align:center;color:#9CA3AF;padding:12px">No hearings recorded yet</div>'; return; }
    el.innerHTML = list.map(h =>
      '<div style="background:#F9FAFB;padding:10px;border-radius:8px;margin-bottom:8px;border-left:3px solid #3B82F6">' +
        '<div style="display:flex;justify-content:space-between;align-items:center">' +
          '<div style="font-weight:600">' + fmtDate(h.hearing_date) + ' — ' + labelCase(h.hearing_type) + '</div>' +
          '<span style="font-size:11px;color:#6B7280">by ' + esc(h.created_by_name) + '</span>' +
        '</div>' +
        (h.location ? '<div style="font-size:12px;color:#6B7280">📍 ' + esc(h.location) + '</div>' : '') +
        (h.officer_name ? '<div style="font-size:12px;color:#6B7280">👤 Officer: ' + esc(h.officer_name) + '</div>' : '') +
        (h.attendees ? '<div style="font-size:12px;color:#6B7280">👥 Attendees: ' + esc(h.attendees) + '</div>' : '') +
        (h.witnesses ? '<div style="font-size:12px;color:#6B7280">🧑‍⚖️ Witnesses: ' + esc(h.witnesses) + '</div>' : '') +
        (h.minutes ? '<div style="font-size:12px;margin-top:4px;white-space:pre-wrap">' + esc(h.minutes) + '</div>' : '') +
        (h.outcome ? '<div style="font-size:12px;margin-top:4px;color:#059669;font-weight:600">Outcome: ' + esc(h.outcome) + '</div>' : '') +
        (h.next_action ? '<div style="font-size:12px;color:#D97706">Next: ' + esc(h.next_action) + '</div>' : '') +
      '</div>'
    ).join('');
  } catch (e) {
    document.getElementById('case-hearings-list').innerHTML = '<div style="color:#EF4444">Failed to load hearings</div>';
  }
}

async function loadCaseEvidence(caseId) {
  try {
    const res = await fetch(API + '?action=list_evidence&case_id=' + caseId);
    const data = await res.json();
    const list = data.data || [];
    const el = document.getElementById('case-evidence-list');
    if (!list.length) { el.innerHTML = '<div style="text-align:center;color:#9CA3AF;padding:12px">No evidence attached yet</div>'; return; }
    const typeIcons = { document:'📄', photo:'🖼️', video:'🎥', audio:'🔊', email:'📧', report:'📊', other:'📎' };
    el.innerHTML = list.map(e => {
      const base = '../../api/legal.php?action=get_evidence_file&evidence_id=' + e.evidence_id;
      const mt = e.mime_type || '';
      const isImage = mt.startsWith('image/');
      const isVideo = mt.startsWith('video/');
      const isAudio = mt.startsWith('audio/');
      const isPdf   = mt === 'application/pdf';

      let preview = '';
      if (isImage) {
        preview = '<div style="margin-top:8px"><img src="' + base + '" style="max-height:160px;max-width:100%;border-radius:8px;border:1px solid #E5E7EB;object-fit:cover;cursor:pointer" onclick="window.open(\'' + base + '\',\'_blank\')" title="Click to open"></div>';
      } else if (isVideo) {
        preview = '<div style="margin-top:8px"><video controls style="max-height:180px;max-width:100%;border-radius:8px;border:1px solid #E5E7EB" src="' + base + '"></video></div>';
      } else if (isAudio) {
        preview = '<div style="margin-top:6px"><audio controls style="width:100%" src="' + base + '"></audio></div>';
      }

      const fmtSize = e.file_size ? (e.file_size > 1048576 ? (e.file_size/1048576).toFixed(1)+' MB' : (e.file_size/1024).toFixed(0)+' KB') : '';

      return '<div style="background:#F9FAFB;padding:10px 12px;border-radius:10px;margin-bottom:8px;border:1px solid #E5E7EB">' +
        '<div style="display:flex;align-items:center;gap:10px">' +
          '<span style="font-size:22px">' + (typeIcons[e.evidence_type] || '📎') + '</span>' +
          '<div style="flex:1;min-width:0">' +
            '<div style="font-weight:600;font-size:13px;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + esc(e.file_name) + '</div>' +
            '<div style="font-size:11px;color:#6B7280">' + labelCase(e.evidence_type) + (fmtSize ? ' · ' + fmtSize : '') + (e.description ? ' · ' + esc(e.description) : '') + '</div>' +
            '<div style="font-size:11px;color:#9CA3AF">' + fmtDate(e.uploaded_at) + (e.uploaded_by_name ? ' · ' + esc(e.uploaded_by_name) : '') + '</div>' +
          '</div>' +
          '<div style="display:flex;gap:6px;flex-shrink:0">' +
            (isImage || isVideo || isPdf
              ? '<a href="' + base + '" target="_blank" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#DBEAFE;color:#1E40AF;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none">👁 View</a>'
              : '') +
            '<a href="' + base + '&download=1" style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:#D1FAE5;color:#065F46;border-radius:6px;font-size:11px;font-weight:600;text-decoration:none">⬇ Download</a>' +
          '</div>' +
        '</div>' +
        preview +
      '</div>';
    }).join('');
  } catch(e) {
    document.getElementById('case-evidence-list').innerHTML = '<div style="color:#EF4444">Failed to load evidence</div>';
  }
}

// ─── Evidence Upload Modal ───
let _evidenceFile = null;

function handleEvidenceDrop(ev) {
  ev.preventDefault();
  const dz = document.getElementById('ae-dropzone');
  dz.style.borderColor = '#D1D5DB'; dz.style.background = '#F9FAFB';
  const file = ev.dataTransfer.files[0];
  if (file) handleEvidenceFileSelect(file);
}

function handleEvidenceFileSelect(file) {
  if (!file) return;
  _evidenceFile = file;
  const icons = { 'image': '🖼️', 'video': '🎥', 'audio': '🔊', 'application/pdf': '📄' };
  const topType = file.type.split('/')[0];
  const icon = icons[topType] || (file.type === 'application/pdf' ? '📄' : '📎');

  document.getElementById('ae-preview-icon').textContent = icon;
  document.getElementById('ae-preview-name').textContent = file.name;
  const sz = file.size > 1048576 ? (file.size/1048576).toFixed(1)+' MB' : (file.size/1024).toFixed(0)+' KB';
  document.getElementById('ae-preview-size').textContent = sz + ' · ' + file.type;
  document.getElementById('ae-preview').style.display = 'block';
  document.getElementById('ae-dropzone').style.display = 'none';

  // Auto-set evidence type
  const typeMap = { image: 'photo', video: 'video', audio: 'audio', text: 'document', application: 'document' };
  document.getElementById('ae-type').value = typeMap[topType] || 'document';

  // Show image thumbnail
  if (topType === 'image') {
    const reader = new FileReader();
    reader.onload = e => {
      document.getElementById('ae-img-thumb').src = e.target.result;
      document.getElementById('ae-img-thumb-wrap').style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    document.getElementById('ae-img-thumb-wrap').style.display = 'none';
  }
}

function clearEvidenceFile() {
  _evidenceFile = null;
  document.getElementById('ae-file-input').value = '';
  document.getElementById('ae-preview').style.display = 'none';
  document.getElementById('ae-img-thumb-wrap').style.display = 'none';
  document.getElementById('ae-dropzone').style.display = 'block';
}

function openAddEvidence(caseId) {
  _evidenceFile = null;
  document.getElementById('ae-case-id').value = caseId;
  document.getElementById('ae-file-input').value = '';
  document.getElementById('ae-description').value = '';
  document.getElementById('ae-type').value = 'document';
  document.getElementById('ae-preview').style.display = 'none';
  document.getElementById('ae-img-thumb-wrap').style.display = 'none';
  document.getElementById('ae-dropzone').style.display = 'block';
  document.getElementById('ae-progress-wrap').style.display = 'none';
  document.getElementById('ae-progress-bar').style.width = '0%';
  openModal('modal-add-evidence');
}

async function submitEvidence() {
  if (!_evidenceFile) {
    Swal.fire({ icon: 'warning', title: 'No File', text: 'Please select a file to upload.', confirmButtonColor: '#059669' });
    return;
  }
  const caseId = document.getElementById('ae-case-id').value;
  const formData = new FormData();
  formData.append('case_id', caseId);
  formData.append('evidence_type', document.getElementById('ae-type').value);
  formData.append('description', document.getElementById('ae-description').value.trim());
  formData.append('evidence_file', _evidenceFile, _evidenceFile.name);

  const btn = document.getElementById('ae-submit-btn');
  btn.disabled = true; btn.textContent = 'Uploading...';
  document.getElementById('ae-progress-wrap').style.display = 'block';

  // Use XHR for progress tracking
  const xhr = new XMLHttpRequest();
  xhr.open('POST', API + '?action=add_evidence');
  xhr.upload.addEventListener('progress', e => {
    if (e.lengthComputable) {
      document.getElementById('ae-progress-bar').style.width = Math.round(e.loaded / e.total * 100) + '%';
    }
  });
  xhr.onload = () => {
    btn.disabled = false; btn.textContent = '📎 Upload Evidence';
    try {
      const data = JSON.parse(xhr.responseText);
      if (data.success) {
        closeModal('modal-add-evidence');
        Swal.fire({ icon: 'success', title: 'Evidence Uploaded', text: _evidenceFile.name + ' saved.', confirmButtonColor: '#059669', timer: 2500, showConfirmButton: false });
        loadCaseEvidence(caseId);
        loadData();
      } else {
        Swal.fire({ icon: 'error', title: 'Upload Failed', text: data.error || 'Unknown error', confirmButtonColor: '#059669' });
        document.getElementById('ae-progress-wrap').style.display = 'none';
      }
    } catch(e) {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Server returned invalid response.', confirmButtonColor: '#059669' });
      document.getElementById('ae-progress-wrap').style.display = 'none';
    }
  };
  xhr.onerror = () => {
    btn.disabled = false; btn.textContent = '📎 Upload Evidence';
    Swal.fire({ icon: 'error', title: 'Network Error', text: 'Upload failed.', confirmButtonColor: '#059669' });
    document.getElementById('ae-progress-wrap').style.display = 'none';
  };
  xhr.send(formData);
}

async function deleteEvidence(evidenceId, caseId) {
  const conf = await Swal.fire({
    icon:'warning', title:'Delete Evidence?', text:'This will permanently remove the file.',
    showCancelButton:true, confirmButtonColor:'#EF4444', confirmButtonText:'Delete'
  });
  if (!conf.isConfirmed) return;
  try {
    const res = await fetch(API + '?action=delete_evidence', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body: JSON.stringify({ evidence_id: evidenceId })
    });
    const data = await res.json();
    if (data.success) {
      Swal.fire({ icon:'success', title:'Deleted', timer:1800, showConfirmButton:false });
      loadCaseEvidence(caseId);
      loadData();
    } else {
      Swal.fire({ icon:'error', title:'Error', text: data.error || 'Failed', confirmButtonColor:'#059669' });
    }
  } catch(e) {
    Swal.fire({ icon:'error', title:'Error', text: e.message, confirmButtonColor:'#059669' });
  }
}

async function loadCaseNotices(caseId) {
  try {
    const res = await fetch(API + '?action=list_notices&case_id=' + caseId);
    const data = await res.json();
    const list = data.data || [];
    const el = document.getElementById('case-notices-list');
    if (!list.length) { el.innerHTML = '<div style="text-align:center;color:#9CA3AF;padding:12px">No notices issued</div>'; return; }
    el.innerHTML = list.map(n =>
      '<div style="background:#FFFBEB;padding:8px 12px;border-radius:8px;margin-bottom:6px;border-left:3px solid #F59E0B">' +
        '<div style="display:flex;justify-content:space-between;align-items:center">' +
          '<div style="font-weight:600;font-size:13px">' + labelCase(n.notice_type) + (n.auto_generated == 1 ? ' ⚡ Auto' : '') + '</div>' +
          '<span style="font-size:11px;padding:2px 8px;border-radius:99px;background:' + (n.status==='sent'?'#D1FAE5':'#F3F4F6') + ';color:' + (n.status==='sent'?'#065F46':'#6B7280') + '">' + labelCase(n.status) + '</span>' +
        '</div>' +
        '<div style="font-size:12px;color:#6B7280">To: ' + esc(n.recipient_name) + (n.recipient_dept ? ' (' + esc(n.recipient_dept) + ')' : '') + '</div>' +
        '<div style="font-size:12px;font-weight:500">' + esc(n.subject) + '</div>' +
        (n.days_overdue ? '<div style="font-size:11px;color:#EF4444">Days Overdue: ' + n.days_overdue + '</div>' : '') +
        '<div style="font-size:11px;color:#9CA3AF">' + fmtDate(n.created_at) + '</div>' +
      '</div>'
    ).join('');
  } catch (e) {
    document.getElementById('case-notices-list').innerHTML = '<div style="color:#EF4444">Failed to load notices</div>';
  }
}

async function runEscalationCheck() {
  try {
    const res = await fetch(API + '?action=check_escalations', { method: 'POST' });
    const data = await res.json();
    if (data.success) {
      if (data.escalated_count > 0) {
        Swal.fire({ icon: 'warning', title: 'Auto-Escalated', text: data.escalated_count + ' case(s) escalated: ' + data.escalated_cases.join(', '), confirmButtonColor: '#059669' });
      } else {
        Swal.fire({ icon: 'info', title: 'No Escalations', text: 'All cases are within their escalation windows.', confirmButtonColor: '#059669' });
      }
      loadData();
    }
  } catch (e) {
    Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#059669' });
  }
}

async function openDecisionMatrix() {
  document.getElementById('decision-matrix-body').innerHTML = 'Loading...';
  openModal('modal-decision-matrix');
  try {
    const res = await fetch(API + '?action=list_decision_matrix');
    const data = await res.json();
    const list = data.data || [];
    if (!list.length) { document.getElementById('decision-matrix-body').innerHTML = '<div style="text-align:center;color:#9CA3AF;padding:20px">No decision matrix rules found</div>'; return; }
    let html = '<table class="data-table"><thead><tr><th>Case Type</th><th>Severity</th><th>Recommended Action</th><th>Days Threshold</th><th>Amount Threshold</th><th>Description</th></tr></thead><tbody>';
    list.forEach(r => {
      html += '<tr>' +
        '<td style="font-weight:600">' + esc(r.case_type) + '</td>' +
        '<td>' + severityBadge(r.severity) + '</td>' +
        '<td style="font-weight:600;color:#1F2937">' + esc(r.recommended_action) + '</td>' +
        '<td style="text-align:center">' + (r.days_threshold ? r.days_threshold + ' days' : '—') + '</td>' +
        '<td style="text-align:center">' + (r.amount_threshold ? money(r.amount_threshold) : '—') + '</td>' +
        '<td style="font-size:12px;color:#6B7280">' + esc(r.description) + '</td>' +
      '</tr>';
    });
    html += '</tbody></table>';
    document.getElementById('decision-matrix-body').innerHTML = html;
  } catch (e) {
    document.getElementById('decision-matrix-body').innerHTML = '<div style="color:#EF4444">Failed to load decision matrix</div>';
  }
}

async function openCaseAnalytics() {
  document.getElementById('case-analytics-body').innerHTML = 'Loading analytics...';
  openModal('modal-case-analytics');
  try {
    const res = await fetch(API + '?action=case_analytics');
    const data = await res.json();
    let html = '';

    // Summary cards
    html += '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-bottom:20px">';
    html += '<div style="background:#F0FDF4;padding:14px;border-radius:10px;text-align:center"><div style="font-size:24px;font-weight:800;color:#065F46">' + (data.avg_resolution_days || 0) + '</div><div style="font-size:12px;color:#6B7280">Avg Days to Resolve</div></div>';
    html += '<div style="background:#FEF2F2;padding:14px;border-radius:10px;text-align:center"><div style="font-size:24px;font-weight:800;color:#991B1B">' + (data.overdue_escalations || 0) + '</div><div style="font-size:12px;color:#6B7280">Overdue Escalations</div></div>';
    const totalCases = (data.by_status || []).reduce((s, r) => s + parseInt(r.count), 0);
    html += '<div style="background:#DBEAFE;padding:14px;border-radius:10px;text-align:center"><div style="font-size:24px;font-weight:800;color:#1E40AF">' + totalCases + '</div><div style="font-size:12px;color:#6B7280">Total Cases</div></div>';
    html += '</div>';

    // By Workflow Status
    html += '<div style="margin-bottom:20px"><div style="font-weight:700;font-size:14px;margin-bottom:8px">Cases by Workflow Status</div>';
    if (data.by_status && data.by_status.length) {
      html += '<div style="display:flex;gap:8px;flex-wrap:wrap">';
      data.by_status.forEach(r => {
        html += '<div style="background:#F3F4F6;padding:8px 14px;border-radius:8px;text-align:center">' +
          '<div style="font-size:18px;font-weight:800">' + r.count + '</div>' +
          '<div style="font-size:11px;color:#6B7280">' + labelCase(r.workflow_step || 'unknown') + '</div></div>';
      });
      html += '</div>';
    } else html += '<div style="color:#9CA3AF">No data</div>';
    html += '</div>';

    // By Severity
    html += '<div style="margin-bottom:20px"><div style="font-weight:700;font-size:14px;margin-bottom:8px">Cases by Severity</div>';
    if (data.by_severity && data.by_severity.length) {
      html += '<div style="display:flex;gap:8px;flex-wrap:wrap">';
      data.by_severity.forEach(r => {
        html += '<div style="padding:8px 14px;border-radius:8px;text-align:center">' + severityBadge(r.severity) + ' <strong>' + r.count + '</strong></div>';
      });
      html += '</div>';
    } else html += '<div style="color:#9CA3AF">No data</div>';
    html += '</div>';

    // By Type
    html += '<div style="margin-bottom:20px"><div style="font-weight:700;font-size:14px;margin-bottom:8px">Cases by Type</div>';
    if (data.by_type && data.by_type.length) {
      const maxCount = Math.max(...data.by_type.map(r => parseInt(r.count)));
      data.by_type.forEach(r => {
        const pct = maxCount > 0 ? (parseInt(r.count) / maxCount * 100) : 0;
        html += '<div style="margin-bottom:6px"><div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:2px"><span>' + labelCase(r.case_type) + '</span><strong>' + r.count + '</strong></div>' +
          '<div style="height:8px;background:#E5E7EB;border-radius:4px;overflow:hidden"><div style="height:100%;width:' + pct + '%;background:#059669;border-radius:4px"></div></div></div>';
      });
    } else html += '<div style="color:#9CA3AF">No data</div>';
    html += '</div>';

    // By Department
    html += '<div style="margin-bottom:20px"><div style="font-weight:700;font-size:14px;margin-bottom:8px">Top Departments</div>';
    if (data.by_department && data.by_department.length) {
      data.by_department.forEach(r => {
        html += '<div style="display:flex;justify-content:space-between;padding:4px 0;font-size:13px;border-bottom:1px solid #F3F4F6"><span>' + esc(r.dept) + '</span><strong>' + r.count + '</strong></div>';
      });
    } else html += '<div style="color:#9CA3AF">No data</div>';
    html += '</div>';

    // Verdict Distribution
    html += '<div style="margin-bottom:20px"><div style="font-weight:700;font-size:14px;margin-bottom:8px">Verdict Distribution</div>';
    if (data.by_verdict && data.by_verdict.length) {
      html += '<div style="display:flex;gap:8px;flex-wrap:wrap">';
      data.by_verdict.forEach(r => {
        html += '<div style="padding:6px 12px;border-radius:8px;background:#F3F4F6">' + verdictBadge(r.verdict) + ' <strong>' + r.count + '</strong></div>';
      });
      html += '</div>';
    } else html += '<div style="color:#9CA3AF">No verdicts yet</div>';
    html += '</div>';

    // Monthly Trend
    html += '<div style="margin-bottom:20px"><div style="font-weight:700;font-size:14px;margin-bottom:8px">Monthly Trend (Last 12 Months)</div>';
    if (data.monthly_trend && data.monthly_trend.length) {
      const maxM = Math.max(...data.monthly_trend.map(r => parseInt(r.count)));
      html += '<div style="display:flex;align-items:flex-end;gap:4px;height:120px;padding-top:10px">';
      data.monthly_trend.forEach(r => {
        const pct = maxM > 0 ? (parseInt(r.count) / maxM * 100) : 0;
        html += '<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:2px">' +
          '<div style="font-size:10px;font-weight:600">' + r.count + '</div>' +
          '<div style="width:100%;background:#059669;border-radius:4px 4px 0 0;min-height:4px;height:' + pct + '%"></div>' +
          '<div style="font-size:9px;color:#6B7280;transform:rotate(-45deg);white-space:nowrap;margin-top:4px">' + r.month + '</div>' +
        '</div>';
      });
      html += '</div>';
    } else html += '<div style="color:#9CA3AF">No data</div>';
    html += '</div>';

    document.getElementById('case-analytics-body').innerHTML = html;
  } catch (e) {
    document.getElementById('case-analytics-body').innerHTML = '<div style="color:#EF4444">Failed to load analytics: ' + e.message + '</div>';
  }
}

// ═══════════════════════════════════════════════════════
// EXPORT FUNCTIONS
// ═══════════════════════════════════════════════════════

function exportCases(format) {
  const headers = ['Case #', 'Title', 'Type', 'Severity', 'Workflow', 'Priority', 'Complainant', 'Accused', 'Financial Impact', 'Verdict'];
  const rows = cases.map(c => [
    c.case_number || '', c.title || '', c.case_type || '', c.severity || '',
    c.workflow_step || '', c.priority || '', c.complainant_name || '', c.accused_name || '',
    money(c.financial_impact), c.verdict || ''
  ]);
  format === 'csv' ? ExportHelper.exportCSV('Legal_Cases', headers, rows)
    : ExportHelper.exportPDF('Legal_Cases', 'Legal — Legal Cases', headers, rows, { landscape: true, subtitle: cases.length + ' records' });
}

function exportContracts(format) {
  const headers = ['Contract #', 'Title', 'Type', 'Party', 'Value', 'Start', 'End', 'Status', 'Assigned'];
  const rows = contracts.map(c => [
    c.contract_number || '', c.title || '', c.contract_type || '', c.party_name || '',
    money(c.contract_value), fmtDate(c.start_date), fmtDate(c.end_date), c.status || '', c.assigned_to || ''
  ]);
  format === 'csv' ? ExportHelper.exportCSV('Legal_Contracts', headers, rows)
    : ExportHelper.exportPDF('Legal_Contracts', 'Legal — Contracts & Agreements', headers, rows, { landscape: true, subtitle: contracts.length + ' records' });
}

function exportPermits(format) {
  const headers = ['Code', 'Permit Name', 'Issuing Body', 'Type', 'Permit #', 'Issue Date', 'Expiry', 'Fee', 'Status'];
  const rows = permits.map(p => [
    p.permit_code || '', p.permit_name || '', p.issuing_body || '', p.permit_type || '',
    p.permit_number || '', fmtDate(p.issue_date), fmtDate(p.expiry_date), money(p.renewal_fee), p.status || ''
  ]);
  format === 'csv' ? ExportHelper.exportCSV('Legal_Permits', headers, rows)
    : ExportHelper.exportPDF('Legal_Permits', 'Legal — Permits & Licenses', headers, rows, { landscape: true, subtitle: permits.length + ' records' });
}

// ═══════════════════════════════════════════════════════
// LOAN CONTRACT PDF EXPORT
// ═══════════════════════════════════════════════════════
function exportLoanContractPDF() {
  const l = loans[viewLoanContractIdx];
  if (!l) { Swal.fire({ icon: 'warning', title: 'No Data', text: 'No loan contract selected.', confirmButtonColor: '#059669' }); return; }

  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('p', 'mm', 'a4');
  const pageW = doc.internal.pageSize.getWidth();
  const pageH = doc.internal.pageSize.getHeight();
  const marginL = 20, marginR = 20, contentW = pageW - marginL - marginR;
  let y = 20;

  const brandColor = [5, 150, 105];       // #059669
  const darkColor  = [31, 41, 55];        // #1F2937
  const grayColor  = [107, 114, 128];     // #6B7280
  const lightGray  = [229, 231, 235];     // #E5E7EB

  function checkPage(needed) {
    if (y + needed > pageH - 25) {
      // Footer on current page
      drawFooter(doc);
      doc.addPage();
      y = 20;
    }
  }

  function drawFooter(d) {
    d.setDrawColor(...lightGray);
    d.line(marginL, pageH - 18, pageW - marginR, pageH - 18);
    d.setFont('helvetica', 'normal');
    d.setFontSize(8);
    d.setTextColor(...grayColor);
    d.text('Microfinancial Management System — Confidential', marginL, pageH - 12);
    d.text('Page ' + d.internal.getCurrentPageInfo().pageNumber, pageW - marginR, pageH - 12, { align: 'right' });
  }

  function fmtDatePdf(d) {
    if (!d) return '—';
    const dt = new Date(d);
    if (isNaN(dt)) return d;
    return dt.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
  }

  function moneyPdf(v) {
    const n = parseFloat(v);
    if (isNaN(n)) return '₱0.00';
    return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }

  // ─── HEADER BANNER ───
  doc.setFillColor(...brandColor);
  doc.rect(0, 0, pageW, 40, 'F');

  doc.setFont('helvetica', 'bold');
  doc.setFontSize(22);
  doc.setTextColor(255, 255, 255);
  doc.text('LOAN AGREEMENT / CONTRACT', pageW / 2, 18, { align: 'center' });

  doc.setFontSize(10);
  doc.setFont('helvetica', 'normal');
  doc.text('Microfinancial Management System', pageW / 2, 27, { align: 'center' });
  doc.text('Reference: ' + (l.loan_doc_code || '—'), pageW / 2, 34, { align: 'center' });

  y = 50;

  // ─── DOCUMENT INFO ROW ───
  doc.setFillColor(240, 253, 244); // #F0FDF4
  doc.roundedRect(marginL, y, contentW, 16, 3, 3, 'F');
  doc.setFont('helvetica', 'normal');
  doc.setFontSize(9);
  doc.setTextColor(...grayColor);
  doc.text('Document Code: ' + (l.loan_doc_code || '—'), marginL + 6, y + 6);
  doc.text('Status: ' + (l.status ? l.status.replace(/_/g, ' ').toUpperCase() : '—'), marginL + 6, y + 12);
  doc.text('Generated: ' + new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }), pageW - marginR - 6, y + 6, { align: 'right' });
  y += 22;

  // ─── BORROWER SECTION ───
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(...darkColor);
  doc.text('BORROWER INFORMATION', marginL, y);
  y += 2;
  doc.setDrawColor(...brandColor);
  doc.setLineWidth(0.8);
  doc.line(marginL, y, marginL + 60, y);
  y += 8;

  doc.setFont('helvetica', 'normal');
  doc.setFontSize(10);
  doc.setTextColor(...darkColor);
  doc.text('Name:', marginL, y);
  doc.setFont('helvetica', 'bold');
  doc.text(l.borrower_name || '—', marginL + 30, y);
  y += 6;

  if (l.borrower_address) {
    doc.setFont('helvetica', 'normal');
    doc.text('Address:', marginL, y);
    doc.setFont('helvetica', 'bold');
    const addrLines = doc.splitTextToSize(l.borrower_address, contentW - 35);
    doc.text(addrLines, marginL + 30, y);
    y += addrLines.length * 5 + 2;
  }
  y += 6;

  // ─── LOAN TERMS TABLE ───
  checkPage(50);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(...darkColor);
  doc.text('LOAN TERMS', marginL, y);
  y += 2;
  doc.setDrawColor(...brandColor);
  doc.setLineWidth(0.8);
  doc.line(marginL, y, marginL + 40, y);
  y += 6;

  const termsData = [
    ['Loan Amount', moneyPdf(l.loan_amount)],
    ['Interest Rate', parseFloat(l.interest_rate || 0).toFixed(2) + '% per annum'],
    ['Loan Term', (l.loan_term_months || 0) + ' months'],
    ['Repayment Schedule', (l.repayment_schedule || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())],
    ['Security Type', (l.security_type || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())],
    ['Penalty Rate', parseFloat(l.penalty_rate || 0).toFixed(2) + '% per month (late payment)']
  ];
  if (l.purpose) termsData.push(['Purpose', l.purpose]);

  doc.autoTable({
    startY: y,
    head: [],
    body: termsData,
    theme: 'plain',
    margin: { left: marginL, right: marginR },
    styles: { fontSize: 10, cellPadding: { top: 3, bottom: 3, left: 6, right: 6 }, textColor: darkColor },
    columnStyles: {
      0: { fontStyle: 'bold', cellWidth: 50, textColor: grayColor },
      1: { fontStyle: 'normal' }
    },
    alternateRowStyles: { fillColor: [249, 250, 251] },
    didDrawPage: function() {}
  });
  y = doc.lastAutoTable.finalY + 8;

  // ─── KEY DATES TABLE ───
  checkPage(40);
  doc.setFont('helvetica', 'bold');
  doc.setFontSize(12);
  doc.setTextColor(...darkColor);
  doc.text('KEY DATES', marginL, y);
  y += 2;
  doc.setDrawColor(...brandColor);
  doc.setLineWidth(0.8);
  doc.line(marginL, y, marginL + 35, y);
  y += 6;

  const datesData = [
    ['Date Signed', fmtDatePdf(l.signed_date)],
    ['Effective Date', fmtDatePdf(l.effective_date)],
    ['Maturity Date', fmtDatePdf(l.maturity_date)]
  ];

  doc.autoTable({
    startY: y,
    head: [],
    body: datesData,
    theme: 'plain',
    margin: { left: marginL, right: marginR },
    styles: { fontSize: 10, cellPadding: { top: 3, bottom: 3, left: 6, right: 6 }, textColor: darkColor },
    columnStyles: {
      0: { fontStyle: 'bold', cellWidth: 50, textColor: grayColor },
      1: { fontStyle: 'normal' }
    },
    alternateRowStyles: { fillColor: [249, 250, 251] }
  });
  y = doc.lastAutoTable.finalY + 10;

  // ─── CONTRACT BODY ───
  if (l.contract_body) {
    checkPage(30);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.setTextColor(...darkColor);
    doc.text('CONTRACT BODY', marginL, y);
    y += 2;
    doc.setDrawColor(...brandColor);
    doc.setLineWidth(0.8);
    doc.line(marginL, y, marginL + 45, y);
    y += 8;

    doc.setFont('times', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(...darkColor);
    const bodyLines = doc.splitTextToSize(l.contract_body, contentW - 10);
    const lineHeight = 5;

    for (let i = 0; i < bodyLines.length; i++) {
      checkPage(lineHeight + 2);
      doc.text(bodyLines[i], marginL + 5, y);
      y += lineHeight;
    }
    y += 8;
  }

  // ─── DISCLOSURE STATEMENT ───
  if (l.disclosure_statement) {
    checkPage(30);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11);
    doc.setTextColor(...darkColor);
    doc.text('TRUTH IN LENDING DISCLOSURE', marginL, y);
    y += 2;
    doc.setDrawColor(...brandColor);
    doc.setLineWidth(0.5);
    doc.line(marginL, y, marginL + 65, y);
    y += 8;

    doc.setFont('times', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(...darkColor);
    const discLines = doc.splitTextToSize(l.disclosure_statement, contentW - 10);
    const lineHeight = 5;

    for (let i = 0; i < discLines.length; i++) {
      checkPage(lineHeight + 2);
      doc.text(discLines[i], marginL + 5, y);
      y += lineHeight;
    }
    y += 8;
  }

  // ─── PROMISSORY NOTE ───
  if (l.promissory_note) {
    checkPage(30);
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(11);
    doc.setTextColor(...darkColor);
    doc.text('PROMISSORY NOTE', marginL, y);
    y += 2;
    doc.setDrawColor(...brandColor);
    doc.setLineWidth(0.5);
    doc.line(marginL, y, marginL + 45, y);
    y += 8;

    doc.setFont('times', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(...darkColor);
    const noteLines = doc.splitTextToSize(l.promissory_note, contentW - 10);
    const lineHeight = 5;

    for (let i = 0; i < noteLines.length; i++) {
      checkPage(lineHeight + 2);
      doc.text(noteLines[i], marginL + 5, y);
      y += lineHeight;
    }
    y += 8;
  }

  // ─── ATTORNEY CREDENTIALS & SIGNATURE ───
  if (l.attorney_name) {
    checkPage(80);

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.setTextColor(...darkColor);
    doc.text('ATTORNEY CREDENTIALS', marginL, y);
    y += 2;
    doc.setDrawColor(...brandColor);
    doc.setLineWidth(0.8);
    doc.line(marginL, y, marginL + 55, y);
    y += 4;

    // Green background box for attorney details
    const attyBoxH = 42;
    checkPage(attyBoxH + 10);
    doc.setFillColor(240, 253, 244); // #F0FDF4
    doc.setDrawColor(167, 243, 208); // #A7F3D0
    doc.setLineWidth(0.5);
    doc.roundedRect(marginL, y, contentW, attyBoxH, 3, 3, 'FD');

    y += 8;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(6, 95, 70);       // #065F46

    const col1X = marginL + 8;
    const col2X = marginL + contentW / 2 + 5;
    const rowH = 6;

    doc.setFont('helvetica', 'bold');
    doc.text('Name:', col1X, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.attorney_name || '—', col1X + 22, y);

    doc.setFont('helvetica', 'bold');
    doc.text('PRC No:', col2X, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.attorney_prc || '—', col2X + 22, y);
    y += rowH;

    doc.setFont('helvetica', 'bold');
    doc.text('PTR No:', col1X, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.attorney_ptr || '—', col1X + 22, y);

    doc.setFont('helvetica', 'bold');
    doc.text('IBP No:', col2X, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.attorney_ibp || '—', col2X + 22, y);
    y += rowH;

    doc.setFont('helvetica', 'bold');
    doc.text('Roll No:', col1X, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.attorney_roll || '—', col1X + 22, y);

    doc.setFont('helvetica', 'bold');
    doc.text('MCLE No:', col2X, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.attorney_mcle || '—', col2X + 25, y);
    y += rowH;

    y += (attyBoxH - 3 * rowH - 8) + 4;

    // ─── ATTORNEY SIGNATURE ───
    checkPage(50);
    y += 8;
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.setTextColor(...darkColor);
    doc.text('Attorney Signature:', marginL, y);
    y += 4;

    if (l.attorney_signature) {
      try {
        let sigData = l.attorney_signature;
        if (!sigData.startsWith('data:')) {
          sigData = 'data:image/png;base64,' + sigData;
        }
        doc.addImage(sigData, 'PNG', marginL, y, 60, 25);
        y += 28;
      } catch (e) {
        console.warn('Could not add attorney signature image:', e);
        doc.setFont('helvetica', 'italic');
        doc.setFontSize(9);
        doc.setTextColor(...grayColor);
        doc.text('[Signature on file]', marginL, y + 8);
        y += 14;
      }
    } else {
      // Draw a signature line
      doc.setDrawColor(...lightGray);
      doc.setLineWidth(0.5);
      doc.line(marginL, y + 15, marginL + 70, y + 15);
      doc.setFont('helvetica', 'italic');
      doc.setFontSize(8);
      doc.setTextColor(...grayColor);
      doc.text('Signature Over Printed Name', marginL, y + 20);
      y += 24;
    }

    // Attorney printed name
    doc.setFont('helvetica', 'bold');
    doc.setFontSize(10);
    doc.setTextColor(...darkColor);
    doc.text(l.attorney_name, marginL, y);
    y += 5;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(8);
    doc.setTextColor(...grayColor);
    doc.text('Counsel', marginL, y);
    y += 10;
  }

  // ─── NOTARY SECTION ───
  if (l.notary_name) {
    checkPage(50);

    doc.setFont('helvetica', 'bold');
    doc.setFontSize(12);
    doc.setTextColor(...darkColor);
    doc.text('ACKNOWLEDGMENT / NOTARIAL CERTIFICATE', marginL, y);
    y += 2;
    doc.setDrawColor(...brandColor);
    doc.setLineWidth(0.8);
    doc.line(marginL, y, marginL + 80, y);
    y += 4;

    // Yellow background box for notarial section
    const notaryBoxH = 38;
    checkPage(notaryBoxH + 10);
    doc.setFillColor(255, 251, 235); // #FFFBEB
    doc.setDrawColor(253, 230, 138); // #FDE68A
    doc.setLineWidth(0.5);
    doc.roundedRect(marginL, y, contentW, notaryBoxH, 3, 3, 'FD');

    y += 8;
    doc.setFont('helvetica', 'normal');
    doc.setFontSize(10);
    doc.setTextColor(146, 64, 14);     // #92400E

    const notCol1 = marginL + 8;
    const notCol2 = marginL + contentW / 2 + 5;
    const nRowH = 6;

    doc.setFont('helvetica', 'bold');
    doc.text('Notary Public:', notCol1, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.notary_name || '—', notCol1 + 35, y);

    doc.setFont('helvetica', 'bold');
    doc.text('Commission:', notCol2, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.notary_commission || '—', notCol2 + 30, y);
    y += nRowH;

    doc.setFont('helvetica', 'bold');
    doc.text('Doc No:', notCol1, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.doc_series_no || '—', notCol1 + 35, y);

    doc.setFont('helvetica', 'bold');
    doc.text('Page No:', notCol2, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.doc_page_no || '—', notCol2 + 30, y);
    y += nRowH;

    doc.setFont('helvetica', 'bold');
    doc.text('Book No:', notCol1, y);
    doc.setFont('helvetica', 'normal');
    doc.text(l.doc_book_no || '—', notCol1 + 35, y);
    y += nRowH;

    y += (notaryBoxH - 3 * nRowH - 8) + 6;
  }

  // ─── FOOTER ON LAST PAGE ───
  drawFooter(doc);

  // Save the file
  const filename = 'Loan_Contract_' + (l.loan_doc_code || 'DRAFT').replace(/[^A-Za-z0-9_-]/g, '_') + '.pdf';
  doc.save(filename);

  Swal.fire({
    icon: 'success',
    title: 'PDF Downloaded',
    text: 'Loan contract PDF has been saved as ' + filename,
    confirmButtonColor: '#059669',
    timer: 3000,
    showConfirmButton: false
  });
}
// ═══════════════════════════════════════════════════════
// HR2 INTEGRATION — Data Display Logic
// ═══════════════════════════════════════════════════════

var hr2Loaded = false;
var hr2EmployeesData = [];
var HR2_BRIDGE = '../../api/hr2.php';

function switchHR2Tab(panelId, btn) {
  document.querySelectorAll('#hr2-sub-tabs .sub-tab').forEach(function(t) { t.classList.remove('active'); });
  if (btn) btn.classList.add('active');
  document.querySelectorAll('.hr2-panel').forEach(function(p) { p.style.display = 'none'; });
  var panel = document.getElementById(panelId);
  if (panel) panel.style.display = '';
}

function hr2InitTab() {
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

function hr2CheckConnection() {
  var dot = document.getElementById('hr2-status-dot');
  var txt = document.getElementById('hr2-status-text');
  fetch(HR2_BRIDGE + '?action=health').then(function(r){return r.json();}).then(function(res) {
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
  }).catch(function() {
    dot.style.background = '#EF4444'; dot.style.animation = 'none';
    txt.textContent = '\u2717 HR2 bridge error'; txt.style.color = '#EF4444';
    document.getElementById('stat-hr2').innerHTML = '<span style="color:#EF4444">Error</span>';
  });
}

function hr2LoadEmployees() {
  var tbody = document.getElementById('hr2-emp-tbody');
  fetch(HR2_BRIDGE + '?action=employees').then(function(r){return r.json();}).then(function(res) {
    hr2EmployeesData = res.data || [];
    document.getElementById('hr2-stat-employees').textContent = hr2EmployeesData.length;
    hr2RenderEmployees(hr2EmployeesData);
  }).catch(function(err) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; });
}

function hr2RenderEmployees(list) {
  var tbody = document.getElementById('hr2-emp-tbody');
  if (!list.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No employees found</td></tr>'; return; }
  tbody.innerHTML = list.slice(0, 100).map(function(e) {
    var st = e.status || e.employment_status || '\u2014';
    var stColor = ['active','regular'].indexOf((st+'').toLowerCase()) >= 0 ? '#059669' : '#EF4444';
    return '<tr><td style="font-weight:600">' + (e.employee_id||'\u2014') + '</td><td style="font-weight:600">' + (e.full_name||'\u2014') + '</td><td style="font-size:12px;color:#6B7280">' + (e.email||'\u2014') + '</td><td>' + (e.department||'\u2014') + '</td><td>' + (e.position||e.job_title||'\u2014') + '</td><td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:' + stColor + '20;color:' + stColor + '">' + st + '</span></td></tr>';
  }).join('');
}

function hr2SearchEmployees() {
  var q = (document.getElementById('hr2-emp-search').value || '').toLowerCase();
  if (!q) { hr2RenderEmployees(hr2EmployeesData); return; }
  hr2RenderEmployees(hr2EmployeesData.filter(function(e) { return (e.full_name||'').toLowerCase().indexOf(q)>=0||(e.email||'').toLowerCase().indexOf(q)>=0||(e.department||'').toLowerCase().indexOf(q)>=0; }));
}

function hr2LoadLeaves() {
  var tbody = document.getElementById('hr2-leaves-tbody');
  fetch(HR2_BRIDGE + '?action=leaves').then(function(r){return r.json();}).then(function(res) {
    var leaves = res.data || [];
    document.getElementById('hr2-stat-leaves').textContent = leaves.length;
    if (!leaves.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No leave records</td></tr>'; return; }
    tbody.innerHTML = leaves.slice(0,100).map(function(l) {
      var st = l.status || '\u2014';
      var stColor = st==='approved'?'#059669':st==='pending'?'#D97706':'#EF4444';
      return '<tr><td style="font-weight:600">' + (l.employee_name||'\u2014') + '</td><td>' + (l.leave_type||'\u2014') + '</td><td style="font-size:12px">' + (l.start_date||'\u2014') + '</td><td style="font-size:12px">' + (l.end_date||'\u2014') + '</td><td style="text-align:center">' + (l.days||'\u2014') + '</td><td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:' + stColor + '20;color:' + stColor + '">' + st + '</span></td></tr>';
    }).join('');
  }).catch(function(err) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; });
}

function hr2LoadTraining() {
  var tbody = document.getElementById('hr2-training-tbody');
  fetch(HR2_BRIDGE + '?action=training').then(function(r){return r.json();}).then(function(res) {
    var items = res.data || [];
    document.getElementById('hr2-stat-training').textContent = items.length;
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF">No training records</td></tr>'; return; }
    tbody.innerHTML = items.slice(0,100).map(function(t) { return '<tr><td style="font-weight:600">' + (t.employee_name||'\u2014') + '</td><td>' + (t.training_name||t.title||'\u2014') + '</td><td>' + (t.provider||'\u2014') + '</td><td style="font-size:12px">' + (t.date||t.start_date||'\u2014') + '</td><td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:#05966920;color:#059669">' + (t.status||'enrolled') + '</span></td></tr>'; }).join('');
  }).catch(function(err) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; });
}

function hr2LoadSuccessors() {
  var tbody = document.getElementById('hr2-successors-tbody');
  fetch(HR2_BRIDGE + '?action=successors').then(function(r){return r.json();}).then(function(res) {
    var items = res.data || [];
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF">No succession data</td></tr>'; return; }
    tbody.innerHTML = items.slice(0,100).map(function(s) { return '<tr><td style="font-weight:600">' + (s.position||'\u2014') + '</td><td>' + (s.incumbent||'\u2014') + '</td><td>' + (s.successor||'\u2014') + '</td><td>' + (s.readiness||'\u2014') + '</td><td>' + (s.priority||'\u2014') + '</td></tr>'; }).join('');
  }).catch(function(err) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; });
}

function hr2LoadJobs() {
  var tbody = document.getElementById('hr2-jobs-tbody');
  fetch(HR2_BRIDGE + '?action=jobs').then(function(r){return r.json();}).then(function(res) {
    var items = res.data || [];
    document.getElementById('hr2-stat-jobs').textContent = items.length;
    if (!items.length) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#9CA3AF">No job titles</td></tr>'; return; }
    tbody.innerHTML = items.slice(0,100).map(function(j) { return '<tr><td style="font-weight:600">' + (j.title||j.job_title||'\u2014') + '</td><td>' + (j.department||'\u2014') + '</td><td>' + (j.level||'\u2014') + '</td><td style="text-align:center">' + (j.headcount||'\u2014') + '</td><td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:#05966920;color:#059669">' + (j.status||'active') + '</span></td></tr>'; }).join('');
  }).catch(function(err) { tbody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>'; });
}

</script>
</body>
</html>
