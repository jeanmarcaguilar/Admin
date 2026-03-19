<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document Management — Microfinancial Admin</title>

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
  <style>
    /* Pagination styles */
    .pagination-container {
      display: flex;
      justify-content: center;
      gap: 8px;
      margin-top: 24px;
      flex-wrap: wrap;
      min-height: 44px;
    }

    .pagination-container button {
      min-width: 36px;
      height: 36px;
      padding: 0 8px;
      border: 1px solid #E5E7EB;
      background: white;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 500;
      color: #4B5563;
      cursor: pointer;
      transition: all 0.2s;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .pagination-container button:hover:not(:disabled) {
      background: #F3F4F6;
      border-color: #D1D5DB;
    }

    .pagination-container button.active {
      background: #059669;
      border-color: #059669;
      color: white;
    }

    .pagination-container button:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      background: #F9FAFB;
    }

    .pagination-container .pagination-info {
      display: flex;
      align-items: center;
      padding: 0 12px;
      font-size: 13px;
      color: #6B7280;
      background: #F9FAFB;
      border-radius: 8px;
      border: 1px solid #E5E7EB;
    }

    /* Upload button pulse animation */
    .upload-pulse {
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(5, 150, 105, 0.4);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(5, 150, 105, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(5, 150, 105, 0);
      }
    }

    /* Minimal utility styles */
  </style>

</head>

<body class="bg-brand-background-main min-h-screen font-[Inter,'Segoe_UI',system-ui,-apple-system,sans-serif]">

  <?php $activePage = 'documents'; $baseUrl = '../../'; include '../../sidebar.php'; ?>

  <!-- MAIN WRAPPER -->
  <div class="md:pl-72">
    <!-- HEADER -->
    <header class="h-16 bg-white flex items-center justify-between px-4 sm:px-6 relative shadow-[0_2px_8px_rgba(0,0,0,0.06)]">
      <div class="hidden md:block absolute left-0 top-0 h-16 w-[2px] bg-white"></div>
      <div class="flex items-center gap-3">
        <button id="mobile-menu-btn" class="md:hidden w-10 h-10 rounded-xl hover:bg-gray-100 active:bg-gray-200 transition flex items-center justify-center">☰</button>
      </div>
      <div class="flex items-center gap-3 sm:gap-5">
        <!-- UPLOAD DOCUMENT BUTTON - Aesthetic placement -->
        <button onclick="openUploadModal()" 
                class="upload-pulse relative flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-brand-primary to-brand-primary-hover text-white rounded-xl hover:shadow-lg transition-all duration-300 transform hover:scale-105 active:scale-95 group"
                title="Upload new document">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:animate-bounce">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
            <polyline points="17 8 12 3 7 8"/>
            <line x1="12" y1="3" x2="12" y2="15"/>
          </svg>
          <span class="hidden sm:inline font-semibold text-sm">Upload</span>
          <span class="absolute -top-2 -right-2 w-4 h-4 bg-amber-500 rounded-full border-2 border-white"></span>
        </button>
        
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
        <h1 class="page-title">Document Management</h1>
        <p class="page-subtitle">Folder-based document registry with secure archiving, access control & audit trail, and lifecycle analytics. No deletion — view only.</p>
      </div>

      <!-- LIFECYCLE INFO BANNER -->
      <div class="animate-in delay-1" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap">
        <div style="flex:1;min-width:220px;background:#D1FAE5;border:1px solid #A7F3D0;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px">
          <span style="font-size:22px">🟢</span>
          <div><div style="font-weight:700;font-size:13px;color:#065F46">Active</div><div style="font-size:11px;color:#047857">Documents &lt; 6 months old</div></div>
        </div>
        <div style="flex:1;min-width:220px;background:#FEF3C7;border:1px solid #FDE68A;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px">
          <span style="font-size:22px">📦</span>
          <div><div style="font-weight:700;font-size:13px;color:#92400E">Archived</div><div style="font-size:11px;color:#B45309">6 months – 3 years old</div></div>
        </div>
        <div style="flex:1;min-width:220px;background:#EDE9FE;border:1px solid #DDD6FE;border-radius:12px;padding:12px 16px;display:flex;align-items:center;gap:10px">
          <span style="font-size:22px">🔒</span>
          <div><div style="font-weight:700;font-size:13px;color:#5B21B6">Retained</div><div style="font-size:11px;color:#6D28D9">3+ years old · Permanent record</div></div>
        </div>
      </div>

      <!-- SUBMODULE DIRECTORY -->
      <div class="animate-in delay-1">
        <div class="module-directory-label">Submodule Directory</div>
        <div class="stats-grid" style="margin-bottom:18px">
          <a href="#tab-folders" onclick="showSection('#tab-folders')" class="stat-card stat-card-link">
            <div class="stat-icon green">📁</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-depts">—</div>
              <div class="stat-label">Department Folders</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>

          <a href="#tab-archiving" onclick="showSection('#tab-archiving')" class="stat-card stat-card-link">
            <div class="stat-icon amber">📦</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-archived">—</div>
              <div class="stat-label">Secure Archive Vault</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-access-control" onclick="showSection('#tab-access-control')" class="stat-card stat-card-link">
            <div class="stat-icon purple">🔐</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-active">—</div>
              <div class="stat-label">Access Control & Audit</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-doc-analytics" onclick="showSection('#tab-doc-analytics')" class="stat-card stat-card-link">
            <div class="stat-icon blue">📊</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-retained">—</div>
              <div class="stat-label">Document Analytics</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-view-audit" onclick="showSection('#tab-view-audit')" class="stat-card stat-card-link" style="border-left:3px solid #6366F1">
            <div class="stat-icon blue">👁️</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-view-logs">—</div>
              <div class="stat-label">View / Download Logs</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-access-requests" onclick="showSection('#tab-access-requests')" class="stat-card stat-card-link" style="border-left:3px solid #F59E0B">
            <div class="stat-icon amber">📩</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-access-requests">—</div>
              <div class="stat-label">Access Requests</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
        </div>
      </div>



      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Department Folders                              -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-folders" class="tab-content active animate-in delay-3">

        <!-- Folder Content (always visible — per-folder PIN gate on click) -->
        <div id="folder-content">

        <!-- Filters -->
        <div style="margin-bottom:20px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <div style="flex:1;min-width:240px;position:relative">
            <input type="search" id="folder-search" class="form-input" placeholder="🔍 Search documents by title, folder, or department..." oninput="filterFolderSearch(this.value)" autocomplete="new-password" name="doc-search-nofill-x" readonly onfocus="this.removeAttribute('readonly')" style="padding-left:14px">
          </div>
          <select id="folder-filter-source" class="form-input" style="width:auto;padding:6px 12px;font-size:12px;display:none" onchange="filterFolderGrid()">
            <option value="">All Sources</option>
          </select>
          <select id="folder-filter-dept" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="filterFolderGrid()">
            <option value="">All Departments</option>
          </select>
          <select id="folder-filter-lifecycle" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="filterFolderGrid()">
            <option value="">All Lifecycle</option>
            <option value="active">🟢 Active</option>
            <option value="archived">📦 Archived</option>
            <option value="retained">🔒 Retained</option>
          </select>
          <span id="folder-summary" style="font-size:12px;color:#9CA3AF">Loading...</span>
        </div>

        <!-- Department Folder Grid -->
        <div id="dept-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-bottom:20px">
        </div>

        <!-- Expanded Folder Panel -->
        <div id="folder-panel" class="card" style="display:none;margin-top:4px">
          <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px">
              <button class="btn btn-outline btn-sm" onclick="closeFolderPanel()">← Back</button>
              <span class="card-title" id="folder-panel-title">📂 Department — Folder</span>
            </div>
            <span id="folder-panel-count" style="font-size:12px;color:#6B7280">0 documents</span>
          </div>
          <div class="card-body" id="folder-panel-body">
          </div>
          <div id="folder-panel-pagination" class="pagination-container"></div>
        </div>

        </div><!-- /folder-content -->
      </div>



      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Secure Archive Vault                            -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-archiving" class="tab-content">

        <!-- Security PIN Gate -->
        <div id="archive-pin-gate" class="animate-in delay-1">
          <div class="card" style="max-width:520px;margin:40px auto;border:2px solid #FDE68A">
            <div class="card-body" style="padding:40px 32px;text-align:center">
              <div style="width:72px;height:72px;margin:0 auto 20px;background:#FEF3C7;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px">🔐</div>
              <h2 style="font-size:22px;font-weight:800;color:#1F2937;margin:0 0 8px">Archive Security Verification</h2>
              <p style="font-size:14px;color:#6B7280;margin:0 0 24px;line-height:1.6">A 4-digit security PIN will be sent to your email. Enter it below to access the archive management functions.</p>
              
              <!-- Step 1: Request PIN -->
              <div id="pin-step-request">
                <button class="btn btn-primary" onclick="requestArchivePin()" style="font-size:14px;padding:12px 28px">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                  Send Security PIN
                </button>
              </div>

              <!-- Step 2: Enter PIN -->
              <div id="pin-step-verify" style="display:none">
                <div style="margin-bottom:16px;font-size:13px;color:#059669;font-weight:600">
                  ✅ PIN sent to your email!
                </div>
                <div id="pin-fallback-display" style="display:none;margin-bottom:16px;background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;padding:12px 16px">
                  <div style="font-size:12px;color:#92400E;font-weight:600;margin-bottom:6px">⚠️ Email could not be sent. Use this PIN:</div>
                  <div id="pin-fallback-code" style="font-size:28px;font-weight:800;color:#D97706;letter-spacing:8px"></div>
                </div>
                <div style="display:flex;gap:8px;justify-content:center;margin-bottom:16px">
                  <input type="text" id="pin-digit-1" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#D97706;border:2px solid #FDE68A;border-radius:12px" oninput="pinAutoFocus(this,2)" onkeydown="pinKeyNav(event,1)">
                  <input type="text" id="pin-digit-2" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#D97706;border:2px solid #FDE68A;border-radius:12px" oninput="pinAutoFocus(this,3)" onkeydown="pinKeyNav(event,2)">
                  <input type="text" id="pin-digit-3" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#D97706;border:2px solid #FDE68A;border-radius:12px" oninput="pinAutoFocus(this,4)" onkeydown="pinKeyNav(event,3)">
                  <input type="text" id="pin-digit-4" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#D97706;border:2px solid #FDE68A;border-radius:12px" oninput="pinAutoFocus(this,null)" onkeydown="pinKeyNav(event,4)">
                </div>
                <div id="pin-timer" style="font-size:12px;color:#9CA3AF;margin-bottom:16px">Expires in <span id="pin-countdown">120</span>s</div>
                <div style="display:flex;gap:10px;justify-content:center">
                  <button class="btn btn-primary" onclick="verifyArchivePin()" style="font-size:13px;padding:10px 24px">Verify PIN</button>
                  <button class="btn btn-outline" onclick="requestArchivePin()" style="font-size:13px;padding:10px 24px">Resend</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Archive Content (hidden until PIN verified) -->
        <div id="archive-content" style="display:none">

        <!-- Archiving Stats -->
        <div class="stats-grid animate-in delay-1" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon green">🟢</div><div class="stat-info"><div class="stat-value" id="ar-active">—</div><div class="stat-label">Active Documents</div></div></div>
          <div class="stat-card"><div class="stat-icon amber">📦</div><div class="stat-info"><div class="stat-value" id="ar-archived">—</div><div class="stat-label">Archived (6mo–3yr)</div></div></div>
          <div class="stat-card"><div class="stat-icon purple">🔒</div><div class="stat-info"><div class="stat-value" id="ar-retained">—</div><div class="stat-label">Retained (3yr+)</div></div></div>
          <div class="stat-card"><div class="stat-icon red">⚠️</div><div class="stat-info"><div class="stat-value" id="ar-pending">—</div><div class="stat-label">Pending Archive</div></div></div>
        </div>

        <!-- Archive Policy Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px;margin-bottom:20px">
          <div class="card" style="border-left:4px solid #059669">
            <div class="card-body" style="padding:16px 20px">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                <span style="font-size:24px">🟢</span>
                <div style="font-weight:700;color:#065F46;font-size:15px">Active Phase</div>
              </div>
              <div style="font-size:13px;color:#047857;line-height:1.6">Documents less than <strong>6 months</strong> old remain fully active with all access permissions. Regular editing and sharing is enabled.</div>
            </div>
          </div>
          <div class="card" style="border-left:4px solid #D97706">
            <div class="card-body" style="padding:16px 20px">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                <span style="font-size:24px">📦</span>
                <div style="font-weight:700;color:#92400E;font-size:15px">Archive Phase</div>
              </div>
              <div style="font-size:13px;color:#B45309;line-height:1.6">Documents between <strong>6 months</strong> and <strong>3 years</strong> are automatically archived. View-only access with audit logging.</div>
            </div>
          </div>
          <div class="card" style="border-left:4px solid #7C3AED">
            <div class="card-body" style="padding:16px 20px">
              <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                <span style="font-size:24px">🔒</span>
                <div style="font-weight:700;color:#5B21B6;font-size:15px">Retention Phase</div>
              </div>
              <div style="font-size:13px;color:#6D28D9;line-height:1.6">Documents older than <strong>3 years</strong> are permanently retained. No modification or deletion is allowed. Institutional records.</div>
            </div>
          </div>
        </div>

        <!-- Run Archive Cycle -->
        <div class="card" style="margin-bottom:16px">
          <div class="card-header">
            <span class="card-title">⚙️ Archive Lifecycle Engine</span>
            <button class="btn btn-primary" onclick="runArchiveCycle()" style="font-size:13px">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/></svg>
              Run Archive Cycle
            </button>
          </div>
          <div class="card-body" style="padding:16px 20px">
            <div style="font-size:13px;color:#4B5563;line-height:1.6">
              Manually trigger the document lifecycle engine to archive active documents older than 6 months and retain archived documents older than 3 years. 
              This process runs automatically but can be triggered on demand.
            </div>
            <div style="margin-top:10px;font-size:12px;color:#9CA3AF" id="ar-last-run">Last run: —</div>
          </div>
        </div>

        <!-- Archive Timeline -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">📊 Archive Timeline</span>
            <span style="font-size:12px;color:#6B7280">Monthly document lifecycle distribution</span>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead><tr>
                <th>Month</th><th>Total</th><th>🟢 Active</th><th>📦 Archived</th><th>🔒 Retained</th><th>Distribution</th>
              </tr></thead>
              <tbody id="ar-timeline-tbody"></tbody>
            </table>
            <div id="ar-timeline-pagination" class="pagination-container"></div>
          </div>
        </div>
      </div><!-- /archive-content -->
      </div><!-- /tab-archiving -->

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Access Control & Audit Trail                    -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-access-control" class="tab-content">

        <!-- Access Control Stats -->
        <div class="stats-grid animate-in delay-1" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon blue">🔑</div><div class="stat-info"><div class="stat-value" id="ac-total">—</div><div class="stat-label">Total Grants</div></div></div>
          <div class="stat-card"><div class="stat-icon green">✅</div><div class="stat-info"><div class="stat-value" id="ac-active">—</div><div class="stat-label">Active Grants</div></div></div>
          <div class="stat-card"><div class="stat-icon red">⏰</div><div class="stat-info"><div class="stat-value" id="ac-expired">—</div><div class="stat-label">Expired</div></div></div>
          <div class="stat-card"><div class="stat-icon amber">👥</div><div class="stat-info"><div class="stat-value" id="ac-users">—</div><div class="stat-label">Users with Access</div></div></div>
        </div>

        <!-- Access Control Banner -->
        <div class="card" style="margin-bottom:16px;border-left:4px solid #DC2626">
          <div class="card-body" style="padding:14px 20px;display:flex;align-items:center;gap:12px">
            <span style="font-size:24px">🔑</span>
            <div>
              <div style="font-weight:700;color:#991B1B">Access Control & Audit Trail</div>
              <div style="font-size:13px;color:#DC2626">Manage who can view, download, edit, or administer documents. All access changes are logged in the audit trail. Expired grants are automatically revoked.</div>
            </div>
          </div>
        </div>

        <!-- Permission Level Summary -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;margin-bottom:20px">
          <div style="background:#D1FAE5;border:1px solid #A7F3D0;border-radius:10px;padding:12px;text-align:center">
            <div style="font-size:20px">👁️</div>
            <div style="font-weight:800;font-size:18px;color:#065F46" id="ac-view-count">—</div>
            <div style="font-size:11px;color:#047857;font-weight:600">View Only</div>
          </div>
          <div style="background:#DBEAFE;border:1px solid #BFDBFE;border-radius:10px;padding:12px;text-align:center">
            <div style="font-size:20px">⬇️</div>
            <div style="font-weight:800;font-size:18px;color:#1E40AF" id="ac-download-count">—</div>
            <div style="font-size:11px;color:#1D4ED8;font-weight:600">Download</div>
          </div>
          <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;padding:12px;text-align:center">
            <div style="font-size:20px">✏️</div>
            <div style="font-weight:800;font-size:18px;color:#92400E" id="ac-edit-count">—</div>
            <div style="font-size:11px;color:#B45309;font-weight:600">Edit</div>
          </div>
          <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:10px;padding:12px;text-align:center">
            <div style="font-size:20px">⚙️</div>
            <div style="font-weight:800;font-size:18px;color:#991B1B" id="ac-admin-count">—</div>
            <div style="font-size:11px;color:#B91C1C;font-weight:600">Admin</div>
          </div>
        </div>

        <!-- Grant Access Button -->
        <div style="margin-bottom:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <button class="btn btn-primary" onclick="openGrantAccessModal()" style="font-size:13px">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            Grant Access
          </button>
        </div>

        <!-- Access Grants Table -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">🔑 Access Grants & Audit Trail</span>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <select id="ac-filter-perm" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="Paginator.reset('doc-ac');renderAccessTable()">
                <option value="">All Permissions</option>
                <option value="view">👁️ View</option>
                <option value="download">⬇️ Download</option>
                <option value="edit">✏️ Edit</option>
                <option value="admin">⚙️ Admin</option>
              </select>
              <input type="text" id="ac-search" class="form-input" style="width:200px;padding:6px 12px;font-size:12px" placeholder="🔍 Search..." oninput="Paginator.reset('doc-ac');renderAccessTable()">
              <span style="font-size:12px;color:#6B7280" id="ac-table-count">Loading...</span>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead><tr>
                <th>Document</th><th>User</th><th>Department</th><th>Permission</th>
                <th>Granted By</th><th>Expires</th><th>Status</th><th>Actions</th>
              </tr></thead>
              <tbody id="ac-tbody"></tbody>
            </table>
            <div id="ac-pagination" class="pagination-container"></div>
          </div>
        </div>


      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Document Analytics                              -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-doc-analytics" class="tab-content">

        <!-- Analytics Stats -->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
          <div></div>
          <div style="display:flex;align-items:center;gap:8px">
            <span id="updated-doc-analytics" style="font-size:10px;color:#9CA3AF;white-space:nowrap"></span>
          </div>
        </div>
        <div class="stats-grid animate-in delay-1" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon blue">📊</div><div class="stat-info"><div class="stat-value" id="da-total">—</div><div class="stat-label">Total Documents</div></div></div>
          <div class="stat-card"><div class="stat-icon green">📁</div><div class="stat-info"><div class="stat-value" id="da-folders">—</div><div class="stat-label">Total Folders</div></div></div>
          <div class="stat-card"><div class="stat-icon amber">📅</div><div class="stat-info"><div class="stat-value" id="da-this-month">—</div><div class="stat-label">Filed This Month</div></div></div>
          <div class="stat-card"><div class="stat-icon purple">🏢</div><div class="stat-info"><div class="stat-value" id="da-depts">—</div><div class="stat-label">Departments</div></div></div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(440px,1fr));gap:16px;margin-bottom:20px">
          <!-- Documents by Department -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">📊 Documents by Department</span>
              <div style="display:flex;gap:8px">
                <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportDocAnalytics('dept','pdf')">📄 PDF</button>
                <button class="btn-export btn-export-csv btn-export-sm" onclick="exportDocAnalytics('dept','csv')">📊 CSV</button>
              </div>
            </div>
            <div class="card-body" style="padding:16px">
              <div id="da-dept-bars"></div>
            </div>
          </div>

          <!-- Lifecycle Distribution -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">🔄 Lifecycle Distribution</span>
            </div>
            <div class="card-body" style="padding:16px">
              <div id="da-lifecycle-chart" style="display:flex;flex-direction:column;gap:12px"></div>
            </div>
          </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(440px,1fr));gap:16px;margin-bottom:20px">
          <!-- Document Types -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">📋 Document Types</span>
            </div>
            <div class="card-body" style="padding:16px">
              <div id="da-type-chart"></div>
            </div>
          </div>

          <!-- Filing Trend (Last 6 Months) -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">📈 Filing Trend (Last 6 Months)</span>
            </div>
            <div class="card-body" style="padding:16px">
              <div id="da-trend-chart"></div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">🕒 Recently Filed Documents</span>
            <span id="da-recent-count" style="font-size:12px;color:#6B7280">Last 10 documents</span>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead><tr>
                <th>Code</th><th>Title</th><th>Folder</th><th>Department</th><th>Type</th>
                <th>Date Filed</th><th>Lifecycle</th><th>Age</th>
              </tr></thead>
              <tbody id="da-recent-tbody"></tbody>
            </table>
            <div id="da-recent-pagination" class="pagination-container"></div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: View / Download Audit Logs                      -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-view-audit" class="tab-content">
        <div class="stats-grid" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon blue">👁️</div><div class="stat-info"><div class="stat-value" id="vl-total-views">0</div><div class="stat-label">Total Views</div></div></div>
          <div class="stat-card"><div class="stat-icon green">⬇️</div><div class="stat-info"><div class="stat-value" id="vl-total-downloads">0</div><div class="stat-label">Total Downloads</div></div></div>
          <div class="stat-card"><div class="stat-icon purple">👤</div><div class="stat-info"><div class="stat-value" id="vl-unique-users">0</div><div class="stat-label">Unique Users</div></div></div>
          <div class="stat-card"><div class="stat-icon amber">📄</div><div class="stat-info"><div class="stat-value" id="vl-unique-docs">0</div><div class="stat-label">Unique Documents</div></div></div>
          <div class="stat-card"><div class="stat-icon green">📅</div><div class="stat-info"><div class="stat-value" id="vl-today">0</div><div class="stat-label">Today's Activity</div></div></div>
          <div class="stat-card" style="border-left:3px solid #6366F1"><div class="stat-icon blue">⭐</div><div class="stat-info"><div class="stat-value" id="vl-most-viewed" style="font-size:13px">—</div><div class="stat-label">Most Viewed Doc</div></div></div>
        </div>
        <div class="card">
          <div class="card-header">
            <span class="card-title">👁️ Document View & Download Audit Trail</span>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportViewLogs('csv')" title="Export CSV">📊 CSV</button>
              <select id="vl-filter-action" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadViewLogs()">
                <option value="">All Actions</option>
                <option value="view">👁️ View</option>
                <option value="download">⬇️ Download</option>
                <option value="preview">🔍 Preview</option>
              </select>
              <input type="date" id="vl-filter-from" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadViewLogs()" title="From">
              <input type="date" id="vl-filter-to" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadViewLogs()" title="To">
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead><tr>
                <th>#</th><th>Document</th><th>Action</th><th>User</th><th>Role</th><th>Department</th><th>Access Method</th><th>Date & Time</th>
              </tr></thead>
              <tbody id="view-logs-tbody">
                <tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr>
              </tbody>
            </table>
            <div id="view-logs-pagination" class="pagination-container"></div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Access Requests (Approval Workflow)             -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-access-requests" class="tab-content">
        <div class="stats-grid" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon amber">📩</div><div class="stat-info"><div class="stat-value" id="ar-total">0</div><div class="stat-label">Total Requests</div></div></div>
          <div class="stat-card" style="border-left:3px solid #F59E0B"><div class="stat-icon amber">⏳</div><div class="stat-info"><div class="stat-value" id="ar-pending">0</div><div class="stat-label">Pending</div></div></div>
          <div class="stat-card" style="border-left:3px solid #059669"><div class="stat-icon green">✅</div><div class="stat-info"><div class="stat-value" id="ar-approved">0</div><div class="stat-label">Approved</div></div></div>
          <div class="stat-card" style="border-left:3px solid #DC2626"><div class="stat-icon red">❌</div><div class="stat-info"><div class="stat-value" id="ar-denied">0</div><div class="stat-label">Denied</div></div></div>
        </div>
        <div class="card">
          <div class="card-header">
            <span class="card-title">📩 Document Access Requests</span>
            <div style="display:flex;gap:8px;align-items:center">
              <select id="ar-filter-status" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadAccessRequests()">
                <option value="">All Status</option>
                <option value="pending">⏳ Pending</option>
                <option value="approved">✅ Approved</option>
                <option value="denied">❌ Denied</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table">
              <thead><tr>
                <th>#</th><th>Document</th><th>Requested By</th><th>Role / Dept</th><th>Permission</th><th>Reason</th><th>Status</th><th>Reviewed By</th><th>Date</th><th>Actions</th>
              </tr></thead>
              <tbody id="access-requests-tbody">
                <tr><td colspan="10" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr>
              </tbody>
            </table>
            <div id="access-requests-pagination" class="pagination-container"></div>
          </div>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════════════ -->
      <!-- FULL-SCREEN DOCUMENT VIEWER MODAL                    -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-fullscreen-viewer" class="modal-overlay" style="z-index:9999" onclick="if(event.target===this)closeFullViewer()">
        <div style="width:96vw;max-width:1400px;height:94vh;background:#fff;border-radius:16px;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 25px 60px rgba(0,0,0,0.3)">
          <!-- Viewer Header -->
          <div id="fv-header" style="padding:14px 20px;border-bottom:1px solid #E5E7EB;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;background:linear-gradient(135deg,#F9FAFB,#F0FDF4)">
            <div style="display:flex;align-items:center;gap:12px;min-width:0;flex:1">
              <span id="fv-icon" style="font-size:32px">📄</span>
              <div style="min-width:0">
                <div id="fv-title" style="font-weight:800;font-size:16px;color:#1F2937;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">Document</div>
                <div id="fv-meta" style="font-size:12px;color:#6B7280;margin-top:2px">—</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
              <span id="fv-access-badge" style="padding:4px 12px;border-radius:8px;font-size:11px;font-weight:700"></span>
              <span id="fv-status-badge" style="padding:4px 12px;border-radius:8px;font-size:11px;font-weight:700"></span>
              <button id="fv-download-btn" class="btn btn-primary" onclick="fullViewerDownload()" style="display:none;padding:8px 16px;font-size:13px" title="Download File">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Download
              </button>
              <button id="fv-request-btn" class="btn btn-outline" onclick="openAccessRequestFromViewer()" style="display:none;padding:8px 16px;font-size:13px" title="Request Access">
                📩 Request Access
              </button>
              <button onclick="closeFullViewer()" style="width:36px;height:36px;border:1px solid #E5E7EB;border-radius:10px;background:#fff;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;color:#6B7280;transition:all .15s" onmouseover="this.style.background='#FEE2E2';this.style.color='#DC2626'" onmouseout="this.style.background='#fff';this.style.color='#6B7280'">✕</button>
            </div>
          </div>
          <!-- Viewer Body: Left=Metadata, Right=Preview -->
          <div style="flex:1;display:flex;overflow:hidden">
            <!-- Left: Metadata Panel -->
            <div id="fv-metadata" style="width:340px;flex-shrink:0;border-right:1px solid #E5E7EB;overflow-y:auto;padding:20px;background:#FAFAFA">
              <div id="fv-metadata-content"></div>
            </div>
            <!-- Right: File Preview -->
            <div id="fv-preview" style="flex:1;display:flex;align-items:center;justify-content:center;background:#F3F4F6;overflow:auto;position:relative">
              <div id="fv-preview-content" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
                <div style="text-align:center;color:#9CA3AF">
                  <div style="font-size:64px;margin-bottom:12px">📄</div>
                  <div style="font-size:14px">Select a document to preview</div>
                </div>
              </div>
              <!-- Access Denied Overlay -->
              <div id="fv-access-denied" style="display:none;position:absolute;inset:0;background:rgba(255,255,255,0.95);display:none;flex-direction:column;align-items:center;justify-content:center;z-index:10">
                <div style="text-align:center;max-width:400px;padding:40px">
                  <div style="width:80px;height:80px;margin:0 auto 20px;background:#FEE2E2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:40px">🔒</div>
                  <h3 style="font-size:20px;font-weight:800;color:#1F2937;margin:0 0 8px">Access Restricted</h3>
                  <p id="fv-denied-msg" style="font-size:14px;color:#6B7280;margin:0 0 20px;line-height:1.6">This document is classified as restricted. You need authorization from a Head Department, Admin, or Manager to view this file.</p>
                  <button id="fv-request-access-btn" class="btn" onclick="openAccessRequestFromViewer()" style="padding:12px 24px;background:#F59E0B;color:#fff;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    📩 Request Access
                  </button>
                  <div id="fv-pending-notice" style="display:none;margin-top:16px;padding:12px 16px;background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;font-size:13px;color:#92400E">
                    ⏳ You have a pending access request for this document. You'll be notified once it's reviewed.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Access Request Modal (from viewer) -->
      <div id="modal-access-request" class="modal-overlay" style="z-index:10000" onclick="if(event.target===this)closeModal('modal-access-request')">
        <div class="modal" style="max-width:500px">
          <div class="modal-header" style="background:linear-gradient(135deg,#F59E0B,#D97706);color:#fff">
            <span class="modal-title" style="color:#fff">📩 Request Document Access</span>
            <button class="modal-close" onclick="closeModal('modal-access-request')" style="color:#fff">&times;</button>
          </div>
          <div class="modal-body" style="padding:24px">
            <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:12px;padding:14px;margin-bottom:18px;display:flex;align-items:center;gap:10px">
              <span style="font-size:24px">📄</span>
              <div>
                <div style="font-weight:700;font-size:14px;color:#92400E" id="ar-modal-doc-title">—</div>
                <div style="font-size:12px;color:#B45309" id="ar-modal-doc-code">—</div>
              </div>
            </div>
            <input type="hidden" id="ar-modal-doc-id">
            <div style="margin-bottom:14px">
              <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">Permission Needed</label>
              <select id="ar-modal-permission" class="form-input" style="width:100%">
                <option value="view">👁️ View — View the document content</option>
                <option value="download">⬇️ Download — View and download the file</option>
              </select>
            </div>
            <div style="margin-bottom:14px">
              <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">Reason for Access</label>
              <textarea id="ar-modal-reason" class="form-input" style="width:100%;min-height:80px;resize:vertical" placeholder="Explain why you need access to this document…"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-access-request')">Cancel</button>
            <button class="btn" onclick="submitAccessRequestFromViewer()" style="background:#F59E0B;color:#fff;border:none">📩 Submit Request</button>
          </div>
        </div>
      </div>


      <!-- Grant Access Modal -->
      <div id="modal-grant-access" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-grant-access')">
        <div class="modal" style="max-width:560px">
          <div class="modal-header" style="background:linear-gradient(135deg,#059669,#065F46);color:#fff">
            <span class="modal-title" style="color:#fff">🔑 Grant Document Access</span>
            <button class="modal-close" onclick="closeModal('modal-grant-access')" style="color:#fff">&times;</button>
          </div>
          <div class="modal-body" style="padding:24px">
            <!-- Department Filter -->
            <div style="margin-bottom:14px">
              <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">🏢 Department</label>
              <select id="ga-department" class="form-input" style="width:100%" onchange="filterGrantByDept()">
                <option value="">All Departments</option>
              </select>
            </div>
            <!-- Department Head Info -->
            <div id="ga-dept-head-info" style="display:none;margin-bottom:14px;background:#ECFDF5;border:1px solid #A7F3D0;border-radius:10px;padding:12px 16px">
              <div style="font-size:12px;font-weight:700;color:#065F46;margin-bottom:4px">👤 Department Head</div>
              <div id="ga-dept-head-name" style="font-size:14px;font-weight:600;color:#1F2937">—</div>
              <div id="ga-dept-head-role" style="font-size:11px;color:#6B7280"></div>
            </div>
            <div style="margin-bottom:14px">
              <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">📄 Document</label>
              <select id="ga-document" class="form-input" style="width:100%">
                <option value="">Select document...</option>
              </select>
            </div>
            <div style="margin-bottom:14px">
              <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">👤 Grant Access To</label>
              <select id="ga-user" class="form-input" style="width:100%">
                <option value="">Select user...</option>
              </select>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px">
              <div>
                <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">🔐 Permission Level</label>
                <select id="ga-permission" class="form-input" style="width:100%">
                  <option value="view">👁️ View</option>
                  <option value="download">⬇️ Download</option>
                  <option value="edit">✏️ Edit</option>
                  <option value="admin">⚙️ Admin</option>
                </select>
              </div>
              <div>
                <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">📅 Expires <span style="color:#9CA3AF">(opt)</span></label>
                <input type="datetime-local" id="ga-expires" class="form-input" style="width:100%">
              </div>
            </div>
            <!-- Modules Info -->
            <div id="ga-modules-info" style="display:none;margin-bottom:14px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:12px 16px">
              <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:6px">📋 System Modules</div>
              <div id="ga-modules-list" style="display:flex;flex-wrap:wrap;gap:6px"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-grant-access')">Cancel</button>
            <button class="btn btn-primary" onclick="submitGrantAccess()" style="background:#059669;border-color:#059669">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
              Grant Access
            </button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Upload Document (New)                         -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-upload" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-upload')">
        <div class="modal" style="max-width:600px">
          <div class="modal-header" style="background:linear-gradient(135deg,#059669,#065F46);color:#fff">
            <span class="modal-title" style="color:#fff">📤 Upload New Document</span>
            <button class="modal-close" onclick="closeModal('modal-upload')" style="color:#fff">&times;</button>
          </div>
          <div class="modal-body" style="padding:24px">
            <form id="upload-form" enctype="multipart/form-data">
              <input type="hidden" name="action" value="upload_document">
              
              <!-- File Upload Area -->
              <div id="drop-area" class="border-2 border-dashed border-brand-border rounded-xl p-6 mb-4 text-center hover:bg-gray-50 transition cursor-pointer" 
                   onclick="document.getElementById('file-input').click()"
                   ondragover="event.preventDefault(); this.classList.add('bg-emerald-50')"
                   ondragleave="this.classList.remove('bg-emerald-50')"
                   ondrop="handleDrop(event)">
                <div id="upload-icon" class="text-5xl mb-2">📄</div>
                <div id="upload-text" class="font-semibold text-gray-700">Drag & drop a file here or <span class="text-brand-primary">browse</span></div>
                <div id="file-info" class="text-sm text-gray-500 mt-2 hidden"></div>
                <input type="file" id="file-input" name="file" class="hidden" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.txt" onchange="handleFileSelect(this)">
              </div>

              <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                  <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">📁 Department</label>
                  <select id="upload-dept" name="department" class="form-input" style="width:100%" required>
                    <option value="">Select department...</option>
                    <option value="HR 1">👥 HR 1 — Talent Acquisition</option>
                    <option value="HR 2">📝 HR 2 — Talent Development</option>
                    <option value="HR 3">🎓 HR 3 — Workforce Operations</option>
                    <option value="HR 4">📋 HR 4 — Compensation & HR Intel</option>
                    <option value="Core 1">🏦 Core 1 — Client Services</option>
                    <option value="Core 2">📊 Core 2 — Institutional Oversight</option>
                    <option value="Log 1">🚚 Log 1 — Supply Chain</option>
                    <option value="Log 2">📦 Log 2 — Fleet Operations</option>
                    <option value="Financial">💵 Financial — Financial Management</option>
                    <option value="Administrative">⚖️ Administrative — Administrative Services</option>
                  </select>
                </div>
                <div>
                  <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">📋 Document Type</label>
                  <input type="text" id="upload-type" name="document_type" class="form-input" style="width:100%" placeholder="e.g. Report, Contract" required>
                </div>
              </div>

              <div class="mb-4">
                <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">📄 Document Title</label>
                <input type="text" id="upload-title" name="title" class="form-input" style="width:100%" placeholder="Enter document title" required>
              </div>

              <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                  <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">🔐 Confidentiality</label>
                  <select id="upload-conf" name="confidentiality" class="form-input" style="width:100%" required>
                    <option value="Public">🟢 Public</option>
                    <option value="Internal" selected>🔵 Internal</option>
                    <option value="Confidential">🟡 Confidential</option>
                    <option value="Restricted">🔴 Restricted</option>
                  </select>
                </div>
                <div>
                  <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">📅 Document Date</label>
                  <input type="date" id="upload-date" name="document_date" class="form-input" style="width:100%" value="<?= date('Y-m-d') ?>" required>
                </div>
              </div>

              <div class="mb-4">
                <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">👤 Designated Employee</label>
                <input type="text" id="upload-employee" name="designated_employee" class="form-input" style="width:100%" placeholder="Name of employee this document is for">
              </div>

              <div class="mb-4">
                <label style="font-weight:600;font-size:13px;color:#374151;display:block;margin-bottom:6px">📝 Description (Optional)</label>
                <textarea id="upload-description" name="description" class="form-input" style="width:100%;min-height:80px;resize:vertical" placeholder="Brief description of the document..."></textarea>
              </div>

              <!-- Progress Bar -->
              <div id="upload-progress-container" class="mb-4 hidden">
                <div class="flex justify-between text-xs mb-1">
                  <span class="font-medium text-brand-primary">Uploading...</span>
                  <span id="upload-progress-percent" class="font-medium text-brand-primary">0%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                  <div id="upload-progress-bar" class="bg-brand-primary h-2 rounded-full transition-all duration-300" style="width:0%"></div>
                </div>
              </div>

            </form>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-upload')">Cancel</button>
            <button class="btn btn-primary" onclick="submitUpload()" style="background:#059669;border-color:#059669">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="17 8 12 3 7 8"/>
                <line x1="12" y1="3" x2="12" y2="15"/>
              </svg>
              Upload Document
            </button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: View Document (No edit, no delete)            -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-view" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-view')">
        <div class="modal" style="max-width:600px">
          <div class="modal-header" style="display:flex;align-items:center;justify-content:space-between">
            <span class="modal-title" id="modal-view-title">Document Details</span>
            <div style="display:flex;align-items:center;gap:8px">
              <button class="btn btn-export-pdf btn-export-sm" onclick="exportDocumentPDF()" title="Download PDF" style="margin:0;padding:5px 12px;font-size:12px">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><polyline points="9 15 12 18 15 15"/></svg>
                PDF
              </button>
              <button class="modal-close" onclick="closeModal('modal-view')">&times;</button>
            </div>
          </div>
          <div class="modal-body" id="modal-view-body">
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-view')">Close</button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Confidential Document PIN Verification        -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-confidential-pin" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-confidential-pin')">
        <div class="modal" style="max-width:520px">
          <div class="modal-header" style="background:linear-gradient(135deg,#DC2626,#991B1B);color:#fff">
            <span class="modal-title" style="color:#fff">🔐 Confidential Document Access</span>
            <button class="modal-close" onclick="closeModal('modal-confidential-pin')" style="color:#fff">&times;</button>
          </div>
          <div class="modal-body" style="padding:32px;text-align:center">
            <!-- Document Info Banner -->
            <div id="conf-pin-doc-info" style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:14px;margin-bottom:20px;text-align:left;display:flex;align-items:center;gap:12px">
              <span style="font-size:28px">📄</span>
              <div>
                <div style="font-weight:700;font-size:14px;color:#991B1B" id="conf-pin-doc-title">—</div>
                <div style="font-size:12px;color:#DC2626">🔴 Restricted — Security PIN required</div>
              </div>
            </div>

            <div style="width:64px;height:64px;margin:0 auto 16px;background:#FEE2E2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px">🔐</div>
            <h3 style="font-size:18px;font-weight:800;color:#1F2937;margin:0 0 6px">Security Verification Required</h3>
            <p style="font-size:13px;color:#6B7280;margin:0 0 20px;line-height:1.5">This document is classified as <strong style="color:#DC2626">Restricted / Internal</strong>. A 4-digit security PIN will be sent to your email to verify your identity.</p>

            <!-- Step 1: Request PIN -->
            <div id="conf-pin-step-request">
              <button class="btn" onclick="requestConfidentialPin()" style="font-size:14px;padding:12px 28px;background:#DC2626;color:#fff;border:none;border-radius:10px">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Send Security PIN
              </button>
            </div>

            <!-- Step 2: Enter PIN -->
            <div id="conf-pin-step-verify" style="display:none">
              <div style="margin-bottom:14px;font-size:13px;color:#059669;font-weight:600">
                ✅ PIN sent to your email!
              </div>
              <div id="conf-pin-fallback-display" style="display:none;margin-bottom:14px;background:#FEF2F2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px">
                <div style="font-size:12px;color:#991B1B;font-weight:600;margin-bottom:6px">⚠️ Email could not be sent. Use this PIN:</div>
                <div id="conf-pin-fallback-code" style="font-size:28px;font-weight:800;color:#DC2626;letter-spacing:8px"></div>
              </div>
              <div style="display:flex;gap:8px;justify-content:center;margin-bottom:14px">
                <input type="text" id="conf-pin-digit-1" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#DC2626;border:2px solid #FECACA;border-radius:12px" oninput="confPinAutoFocus(this,2)" onkeydown="confPinKeyNav(event,1)">
                <input type="text" id="conf-pin-digit-2" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#DC2626;border:2px solid #FECACA;border-radius:12px" oninput="confPinAutoFocus(this,3)" onkeydown="confPinKeyNav(event,2)">
                <input type="text" id="conf-pin-digit-3" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#DC2626;border:2px solid #FECACA;border-radius:12px" oninput="confPinAutoFocus(this,4)" onkeydown="confPinKeyNav(event,3)">
                <input type="text" id="conf-pin-digit-4" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#DC2626;border:2px solid #FECACA;border-radius:12px" oninput="confPinAutoFocus(this,null)" onkeydown="confPinKeyNav(event,4)">
              </div>
              <div id="conf-pin-timer" style="font-size:12px;color:#9CA3AF;margin-bottom:14px">Expires in <span id="conf-pin-countdown">120</span>s</div>
              <div style="display:flex;gap:10px;justify-content:center">
                <button class="btn" onclick="verifyConfidentialPin()" style="font-size:13px;padding:10px 24px;background:#DC2626;color:#fff;border:none;border-radius:10px">Verify PIN</button>
                <button class="btn btn-outline" onclick="requestConfidentialPin()" style="font-size:13px;padding:10px 24px">Resend</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Per-Folder Email OTP Verification              -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-folder-pin" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-folder-pin')">
        <div class="modal" style="max-width:520px">
          <div class="modal-header" style="background:linear-gradient(135deg,#059669,#065F46);color:#fff">
            <span class="modal-title" style="color:#fff">🔐 Folder Security Verification</span>
            <button class="modal-close" onclick="closeModal('modal-folder-pin')" style="color:#fff">&times;</button>
          </div>
          <div class="modal-body" style="padding:32px;text-align:center">
            <!-- Folder Info Banner -->
            <div id="folder-pin-info" style="background:#ECFDF5;border:1px solid #A7F3D0;border-radius:12px;padding:14px;margin-bottom:20px;text-align:left;display:flex;align-items:center;gap:12px">
              <span style="font-size:28px">📂</span>
              <div>
                <div style="font-weight:700;font-size:14px;color:#065F46" id="folder-pin-title">—</div>
                <div style="font-size:12px;color:#059669" id="folder-pin-level">🔒 Secured — Email OTP required</div>
              </div>
            </div>

            <div style="width:64px;height:64px;margin:0 auto 16px;background:#ECFDF5;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px">🔐</div>
            <h3 style="font-size:18px;font-weight:800;color:#1F2937;margin:0 0 6px">Folder Access Verification</h3>
            <p style="font-size:13px;color:#6B7280;margin:0 0 20px;line-height:1.5">A 4-digit security PIN will be sent to your email. Enter it below to access this folder.</p>

            <!-- Step 1: Send PIN -->
            <div id="fpin-step-request">
              <button class="btn" onclick="requestFolderPin()" style="font-size:14px;padding:12px 28px;background:#059669;color:#fff;border:none;border-radius:10px;cursor:pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                Send Security PIN
              </button>
            </div>

            <!-- Step 2: Enter PIN -->
            <div id="fpin-step-verify" style="display:none">
              <div style="margin-bottom:16px;font-size:13px;color:#059669;font-weight:600">✅ PIN sent to your email!</div>
              <div id="fpin-fallback-display" style="display:none;margin-bottom:16px;background:#D1FAE5;border:1px solid #A7F3D0;border-radius:10px;padding:12px 16px">
                <div style="font-size:12px;color:#065F46;font-weight:600;margin-bottom:6px">⚠️ Email could not be sent. Use this PIN:</div>
                <div id="fpin-fallback-code" style="font-size:28px;font-weight:800;color:#059669;letter-spacing:8px"></div>
              </div>
              <div style="display:flex;gap:8px;justify-content:center;margin-bottom:16px">
                <input type="text" id="fpin-digit-1" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#059669;border:2px solid #A7F3D0;border-radius:12px" oninput="fpinAutoFocus(this,2)" onkeydown="fpinKeyNav(event,1)">
                <input type="text" id="fpin-digit-2" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#059669;border:2px solid #A7F3D0;border-radius:12px" oninput="fpinAutoFocus(this,3)" onkeydown="fpinKeyNav(event,2)">
                <input type="text" id="fpin-digit-3" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#059669;border:2px solid #A7F3D0;border-radius:12px" oninput="fpinAutoFocus(this,4)" onkeydown="fpinKeyNav(event,3)">
                <input type="text" id="fpin-digit-4" class="form-input" maxlength="1" style="width:56px;height:60px;text-align:center;font-size:28px;font-weight:700;color:#059669;border:2px solid #A7F3D0;border-radius:12px" oninput="fpinAutoFocus(this,null)" onkeydown="fpinKeyNav(event,4)">
              </div>
              <div id="fpin-timer" style="font-size:12px;color:#9CA3AF;margin-bottom:16px">Expires in <span id="fpin-countdown">120</span>s</div>
              <div style="display:flex;gap:10px;justify-content:center">
                <button class="btn" onclick="verifyFolderPin()" style="font-size:13px;padding:10px 24px;background:#059669;color:#fff;border:none;border-radius:10px">Verify PIN</button>
                <button class="btn btn-outline" onclick="requestFolderPin()" style="font-size:13px;padding:10px 24px">Resend</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </main>
  </div>

<script src="../../admin.js?v=20260304"></script>
<script src="../../export.js?v=20260304"></script>
<script src="../../docu-formatter.js"></script>
<script>
/* ═══════════════════════════════════════════════════════
   DOCUMENT MANAGEMENT MODULE — API-driven
   ═══════════════════════════════════════════════════════ */

// Clear any browser-auto-filled search value on page load
(function() {
  const searchEl = document.getElementById('folder-search');
  if (searchEl) {
    searchEl.value = '';
    // Aggressive clear: browsers may auto-fill after a short delay
    setTimeout(() => { searchEl.value = ''; }, 100);
    setTimeout(() => { searchEl.value = ''; }, 500);
    setTimeout(() => { searchEl.value = ''; }, 1500);
  }
})();

const API = '../../api/documents.php';
let allDocuments = [];
let folders = [];
let stats = {};
let currentViewDocId = null;
let confidentialPinVerified = false;
let confPinCountdownInterval = null;
let pendingConfDocId = null;
let folderPinCountdownInterval = null;
let pendingFolderDeptId = null;

// Pagination
Paginator.create('doc-ar-timeline', { perPage: 10, onPageChange: function() { renderArchiveTimeline(window._lastArchiveTimeline || []); }, alwaysShow: true });
Paginator.create('doc-ac', { perPage: 10, onPageChange: function() { renderAccessTable(); }, alwaysShow: true });
Paginator.create('doc-da-recent', { perPage: 10, onPageChange: function() { renderDocAnalytics(); }, alwaysShow: true });
Paginator.create('doc-view-logs', { perPage: 10, onPageChange: function() { renderViewLogs(); }, alwaysShow: true });
Paginator.create('doc-access-requests', { perPage: 10, onPageChange: function() { renderAccessRequests(); }, alwaysShow: true });
Paginator.create('doc-folder-panel', { perPage: 10, onPageChange: function() { renderFolderPanel(window._currentFolderDocs || []); }, alwaysShow: true });

// ───── Upload Functions ─────
function openUploadModal() {
  // Reset form
  document.getElementById('upload-form').reset();
  document.getElementById('file-info').classList.add('hidden');
  document.getElementById('upload-progress-container').classList.add('hidden');
  document.getElementById('upload-progress-bar').style.width = '0%';
  document.getElementById('upload-icon').innerHTML = '📄';
  document.getElementById('upload-text').innerHTML = 'Drag & drop a file here or <span class="text-brand-primary">browse</span>';
  
  // Set default date
  document.getElementById('upload-date').value = new Date().toISOString().split('T')[0];
  
  openModal('modal-upload');
}

function handleFileSelect(input) {
  if (input.files && input.files[0]) {
    updateFileInfo(input.files[0]);
  }
}

function handleDrop(event) {
  event.preventDefault();
  document.getElementById('drop-area').classList.remove('bg-emerald-50');
  
  if (event.dataTransfer.files && event.dataTransfer.files[0]) {
    document.getElementById('file-input').files = event.dataTransfer.files;
    updateFileInfo(event.dataTransfer.files[0]);
  }
}

function updateFileInfo(file) {
  const fileInfo = document.getElementById('file-info');
  const fileSize = (file.size / 1024).toFixed(1);
  const fileType = file.name.split('.').pop().toUpperCase();
  
  document.getElementById('upload-icon').innerHTML = '📎';
  document.getElementById('upload-text').innerHTML = `<span class="text-brand-primary font-bold">${file.name}</span>`;
  fileInfo.innerHTML = `${fileType} · ${fileSize} KB`;
  fileInfo.classList.remove('hidden');
  
  // Validate file size (max 10MB)
  if (file.size > 10 * 1024 * 1024) {
    Swal.fire({
      icon: 'error',
      title: 'File Too Large',
      text: 'Maximum file size is 10MB',
      confirmButtonColor: '#059669'
    });
    document.getElementById('file-input').value = '';
    document.getElementById('file-info').classList.add('hidden');
    document.getElementById('upload-icon').innerHTML = '📄';
    document.getElementById('upload-text').innerHTML = 'Drag & drop a file here or <span class="text-brand-primary">browse</span>';
  }
}

async function submitUpload() {
  const form = document.getElementById('upload-form');
  const formData = new FormData(form);
  
  // Validate file
  if (!document.getElementById('file-input').files[0]) {
    Swal.fire({
      icon: 'warning',
      title: 'No File Selected',
      text: 'Please select a file to upload',
      confirmButtonColor: '#059669'
    });
    return;
  }
  
  // Validate required fields
  const required = ['department', 'document_type', 'title'];
  for (let field of required) {
    if (!formData.get(field)) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Field',
        text: `Please fill in all required fields`,
        confirmButtonColor: '#059669'
      });
      return;
    }
  }
  
  // Show progress bar
  const progressContainer = document.getElementById('upload-progress-container');
  const progressBar = document.getElementById('upload-progress-bar');
  const progressPercent = document.getElementById('upload-progress-percent');
  progressContainer.classList.remove('hidden');
  
  try {
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
      if (e.lengthComputable) {
        const percent = Math.round((e.loaded / e.total) * 100);
        progressBar.style.width = percent + '%';
        progressPercent.textContent = percent + '%';
      }
    });
    
    const uploadPromise = new Promise((resolve, reject) => {
      xhr.onload = function() {
        if (xhr.status === 200) {
          try {
            const response = JSON.parse(xhr.responseText);
            resolve(response);
          } catch (e) {
            reject(new Error('Invalid server response'));
          }
        } else {
          reject(new Error('Upload failed'));
        }
      };
      xhr.onerror = () => reject(new Error('Network error'));
    });
    
    xhr.open('POST', API, true);
    xhr.send(formData);
    
    const result = await uploadPromise;
    
    if (result.success) {
      // Show success
      await Swal.fire({
        icon: 'success',
        title: 'Upload Successful',
        text: 'Document has been uploaded successfully',
        timer: 2000,
        showConfirmButton: false,
        confirmButtonColor: '#059669'
      });
      
      // Close modal
      closeModal('modal-upload');
      
      // Refresh data
      await loadData(true);
      
    } else {
      throw new Error(result.message || 'Upload failed');
    }
    
  } catch (error) {
    console.error('Upload error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Upload Failed',
      text: error.message || 'Could not upload document. Please try again.',
      confirmButtonColor: '#059669'
    });
  } finally {
    progressContainer.classList.add('hidden');
    progressBar.style.width = '0%';
    progressPercent.textContent = '0%';
  }
}

// ───── Department Display Config (MicroFinancial Management System) ─────
const deptConfig = {
  'HR 1':           { icon: '👥', folder: 'Talent Acquisition & Workforce Entry',        color: '#059669', bg: '#D1FAE5',
                      modules: ['Applicant Management','Recruitment Management','New Hire Onboarding','Performance Management (Initial)','Social Recognition'] },
  'HR 2':           { icon: '📝', folder: 'Talent Development & Career Pathing',          color: '#2563EB', bg: '#DBEAFE',
                      modules: ['Competency Management','Learning Management','Training Management','Succession Planning'] },
  'HR 3':           { icon: '🎓', folder: 'Workforce Operations & Time Management',       color: '#7C3AED', bg: '#EDE9FE',
                      modules: ['Employee Self-Service (ESS)','Time and Attendance System','Shift and Schedule Management','Timesheet Management','Leave Management','Claims and Reimbursement'] },
  'HR 4':           { icon: '📋', folder: 'Compensation & HR Intelligence',               color: '#DC2626', bg: '#FEE2E2',
                      modules: ['Core Human Capital Management (HCM)','Payroll Management','Compensation Planning','HR Analytics Dashboard','HMO & Benefits Administration'] },
  'Core 1':         { icon: '🏦', folder: 'Client Services & Financial Transactions',     color: '#D97706', bg: '#FEF3C7',
                      modules: ['Client Registration & KYC','Loan Application & Disbursement','Loan Repayment & Installments','Savings Account Management','Group Lending & Solidarity Mechanism','Client Self-Service Portal (Mobile/Web)'] },
  'Core 2':         { icon: '📊', folder: 'Institutional Oversight & Financial Control',   color: '#059669', bg: '#D1FAE5',
                      modules: ['Loan Portfolio & Risk Management','Savings & Collection Monitoring','Disbursement & Fund Allocation Tracker','Compliance & Audit Trail System','Reports & Performance Dashboards','User Management & Role-Based Access'] },
  'Log 1':          { icon: '🚚', folder: 'Smart Supply Chain & Procurement Management',   color: '#0891B2', bg: '#CFFAFE',
                      modules: ['Smart Warehousing System (SWS)','Procurement & Sourcing Management (PSM)','Project Logistics Tracker (PLT)','Asset Lifecycle & Maintenance (ALMS)','Document Tracking & Logistics Records (DTRS)'] },
  'Log 2':          { icon: '📦', folder: 'Fleet and Transportation Operations',           color: '#9333EA', bg: '#F3E8FF',
                      modules: ['Fleet & Vehicle Management (FVM)','Vehicle Reservation & Dispatch System (VRDS)','Driver and Trip Performance Monitoring','Transport Cost Analysis & Optimization (TCAO)','Mobile Fleet Command App (optional)'] },
  'Financial':      { icon: '💵', folder: 'Financial Management',                         color: '#16A34A', bg: '#DCFCE7',
                      modules: ['Legal Management','Disbursement','General Ledger','Accounts Payable / Accounts Receivable','Collection','Budget Management'] },
  'Administrative': { icon: '⚖️', folder: 'Administrative Services',                      color: '#B91C1C', bg: '#FEE2E2',
                      modules: ['Facilities Reservation','Document Management (Archiving)','Legal Management','Visitor Management'] },
};
const defaultDept = { icon: '📁', folder: 'Documents', color: '#6B7280', bg: '#F3F4F6', modules: [] };

// ───── Folder Security Configuration ─────
// level: public | internal | confidential | restricted
// roles_allowed: which roles can access without PIN (admin roles always have access)
const folderSecurity = {
  'Administrative': { level: 'secured', label: '🔒 Secured', color: '#B91C1C', bg: '#FEE2E2', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'HR 4':      { level: 'secured', label: '🔒 Secured', color: '#D97706', bg: '#FEF3C7', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'HR 1':      { level: 'secured', label: '🔒 Secured', color: '#059669', bg: '#D1FAE5', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'HR 2':      { level: 'secured', label: '🔒 Secured', color: '#2563EB', bg: '#DBEAFE', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'HR 3':      { level: 'secured', label: '🔒 Secured', color: '#7C3AED', bg: '#EDE9FE', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'Financial': { level: 'secured', label: '🔒 Secured', color: '#16A34A', bg: '#DCFCE7', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'Core 1':    { level: 'secured', label: '🔒 Secured', color: '#D97706', bg: '#FEF3C7', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'Core 2':    { level: 'secured', label: '🔒 Secured', color: '#059669', bg: '#D1FAE5', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'Log 1':     { level: 'secured', label: '🔒 Secured', color: '#0891B2', bg: '#CFFAFE', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
  'Log 2':     { level: 'secured', label: '🔒 Secured', color: '#9333EA', bg: '#F3E8FF', roles_allowed: ['super_admin','admin','manager','head_department','staff'] },
};
const defaultSecurity = { level: 'secured', label: '🔒 Secured', color: '#6B7280', bg: '#F3F4F6', roles_allowed: ['super_admin','admin','manager','head_department','staff'] };

function getFolderSecurity(deptId) {
  return folderSecurity[deptId] || defaultSecurity;
}

// Track which folders have been unlocked this session
const unlockedFolders = new Set();

function getUserRawRole() {
  // Match raw role from session (e.g. 'super_admin', 'admin', 'manager', 'staff')
  const roleStr = (window.__mf_user && window.__mf_user.role) || '';
  if (roleStr.includes('System Admin') || roleStr.includes('super_admin')) return 'super_admin';
  if (roleStr.toLowerCase() === 'admin') return 'admin';
  if (roleStr.toLowerCase() === 'manager') return 'manager';
  if (roleStr.includes('Head') || roleStr.includes('head_department')) return 'head_department';
  return 'staff';
}

function canAccessFolder(deptId) {
  const sec = getFolderSecurity(deptId);
  const role = getUserRawRole();
  return sec.roles_allowed.includes(role);
}

function getDeptInfo(deptId) {
  return deptConfig[deptId] || { ...defaultDept, folder: deptId || 'Documents' };
}

// ───── Lifecycle Computation (client-side from created_at) ─────
const SIX_MONTHS  = 6 * 30.44 * 24 * 60 * 60 * 1000;
const THREE_YEARS = 3 * 365.25 * 24 * 60 * 60 * 1000;

function getLifecycle(dateStr) {
  const age = Date.now() - new Date(dateStr).getTime();
  if (age >= THREE_YEARS) return { status: 'retained', label: '🔒 Retained', badge: 'badge-purple', color: '#7C3AED', bg: '#EDE9FE' };
  if (age >= SIX_MONTHS)  return { status: 'archived', label: '📦 Archived', badge: 'badge-amber',  color: '#D97706', bg: '#FEF3C7' };
  return { status: 'active', label: '🟢 Active', badge: 'badge-green', color: '#059669', bg: '#D1FAE5' };
}

function getAge(dateStr) {
  const diff = Date.now() - new Date(dateStr).getTime();
  const days = Math.floor(diff / 86400000);
  if (days < 0) return '0 days';
  if (days < 31) return days + ' days';
  const months = Math.floor(days / 30.44);
  if (months < 12) return months + ' month' + (months !== 1 ? 's' : '');
  const years = Math.floor(months / 12);
  const rem = months % 12;
  return years + 'yr' + (rem ? ' ' + rem + 'mo' : '');
}

function fmtDate(dateStr) {
  if (!dateStr) return '—';
  return new Date(dateStr).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

// ───── Document Field Accessors ─────
function getFileIcon(doc) {
  const raw = (doc.file_type || doc.file_name || doc.fileType || '').toLowerCase();
  if (raw.includes('pdf'))               return '📕';
  if (raw.includes('xls') || raw.includes('xlsx')) return '📗';
  if (raw.includes('doc') || raw.includes('docx')) return '📘';
  return '📄';
}

function getDocDate(doc)   { return doc.created_at || doc.date || ''; }
function getDocDept(doc)   { return doc.department || doc.dept || ''; }
function getDocCode(doc)   { return doc.document_code || doc.code || ''; }
function getDocType(doc)   { return doc.document_type || doc.type || ''; }
function getDocFolder(doc) { return doc.folder_name || doc.folder || getDeptInfo(getDocDept(doc)).folder; }
function getDocId(doc)     { return doc.document_id || doc.id; }

function getDocFileType(doc) {
  const ft = doc.file_type || doc.fileType || '';
  if (ft) return ft.replace(/^\./, '').toUpperCase();
  const fn = doc.file_name || '';
  const idx = fn.lastIndexOf('.');
  return idx >= 0 ? fn.substring(idx + 1).toUpperCase() : '';
}

function getDocFileSize(doc) {
  if (doc.file_size) {
    const bytes = parseInt(doc.file_size, 10);
    if (!isNaN(bytes)) {
      if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
      if (bytes >= 1024)    return (bytes / 1024).toFixed(0) + ' KB';
      return bytes + ' B';
    }
    return String(doc.file_size);
  }
  return doc.fileSize || '';
}

const viewSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';

// ═══════════════════════════════════════════════════════
// DATA LOADING — All from API
// ═══════════════════════════════════════════════════════

let _dataLoaded = false;     // Guard: prevent duplicate loadData runs

async function loadData(forceReload) {
  if (_dataLoaded && !forceReload) return; // Prevent duplicate init calls
  _dataLoaded = true;
  try {
    const [sRes, dRes, fRes] = await Promise.all([
      fetch(API + '?action=dashboard_stats'),
      fetch(API + '?action=list_documents'),
      fetch(API + '?action=list_folders')
    ]);

    const sJson = await sRes.json();
    const dJson = await dRes.json();
    const fJson = await fRes.json();

    stats        = sJson.data || sJson || {};
    allDocuments = dJson.data || dJson || [];
    folders      = fJson.data || fJson || [];

    if (!Array.isArray(allDocuments)) allDocuments = [];

    renderStats();
    if (typeof refreshSidebarCounts === 'function') refreshSidebarCounts();
    renderDeptGrid();
  } catch (err) {
    console.error('Failed to load document data:', err);
    Swal.fire({
      icon: 'error',
      title: 'Load Error',
      text: 'Could not load document data. Please refresh the page.',
      confirmButtonColor: '#059669'
    });
  }
}

// ═══════════════════════════════════════════════════════
// RENDER STATS
// ═══════════════════════════════════════════════════════

async function loadExternalDocuments() {
  // External integration disabled — local documents only
  return;
}

function deduplicateDocuments() {
  const seen = new Set();
  allDocuments = allDocuments.filter(doc => {
    const key = (doc.title || '').trim().toLowerCase() + '|' + ((doc.department || getDocDept(doc) || '')).trim().toLowerCase();
    if (seen.has(key)) return false;
    seen.add(key);
    return true;
  });
}

// ═══════════════════════════════════════════════════════
// RENDER STATS
// ═══════════════════════════════════════════════════════

function renderStats() {
  let activeCount = 0, archivedCount = 0, retainedCount = 0;
  const uniqueDepts = new Set();

  allDocuments.forEach(doc => {
    const lc = getLifecycle(getDocDate(doc));
    if (lc.status === 'active')   activeCount++;
    if (lc.status === 'archived') archivedCount++;
    if (lc.status === 'retained') retainedCount++;
    const dept = getDocDept(doc);
    if (dept) uniqueDepts.add(dept);
  });

  document.getElementById('stat-active').textContent   = activeCount;
  document.getElementById('stat-archived').textContent = archivedCount;
  document.getElementById('stat-retained').textContent = retainedCount;
  document.getElementById('stat-depts').textContent    = uniqueDepts.size;
}

// ═══════════════════════════════════════════════════════
// DEPARTMENT FILTER DROPDOWN — populated from data
// ═══════════════════════════════════════════════════════



// ═══════════════════════════════════════════════════════
// TAB 1 — DEPARTMENT FOLDERS
// ═══════════════════════════════════════════════════════

let deptList = [];

function renderDeptGrid() {
  const grid = document.getElementById('dept-grid');
  grid.innerHTML = '';

  // Show ALL documents in the folder view (external + local)
  const allDocs = allDocuments;

  // Build unique departments from ALL documents
  const deptMap = {};
  allDocs.forEach(doc => {
    const dept = getDocDept(doc);
    if (!dept) return;
    if (!deptMap[dept]) deptMap[dept] = [];
    deptMap[dept].push(doc);
  });

  deptList = Object.keys(deptMap).sort();

  // Populate folder department filter
  const folderDeptSelect = document.getElementById('folder-filter-dept');
  if (folderDeptSelect) {
    const currentVal = folderDeptSelect.value;
    folderDeptSelect.innerHTML = '<option value="">All Departments</option>';
    deptList.forEach(dept => {
      const opt = document.createElement('option');
      opt.value = dept;
      opt.textContent = dept;
      folderDeptSelect.appendChild(opt);
    });
    folderDeptSelect.value = currentVal;
  }

  // Update stat card to reflect the folder grid count
  document.getElementById('stat-depts').textContent = deptList.length;
  const totalDocs = allDocs.length;
  document.getElementById('folder-summary').textContent =
    deptList.length + ' department folder' + (deptList.length !== 1 ? 's' : '') + ' · ' + totalDocs + ' document' + (totalDocs !== 1 ? 's' : '');

  deptList.forEach(deptId => {
    const info = getDeptInfo(deptId);
    const docs = deptMap[deptId];
    const active   = docs.filter(d => getLifecycle(getDocDate(d)).status === 'active').length;
    const archived = docs.filter(d => getLifecycle(getDocDate(d)).status === 'archived').length;
    const retained = docs.filter(d => getLifecycle(getDocDate(d)).status === 'retained').length;

    const sources = [];
    const srcBadges = '';

    const card = document.createElement('div');
    card.className = 'card dept-folder-card';
    card.dataset.dept = deptId;
    card.dataset.sources = sources.join(',');
    card.style.cssText = 'margin-bottom:0;cursor:pointer;transition:all 0.2s;border:2px solid transparent;';
    card.onmouseover = () => { card.style.borderColor = info.color; card.style.boxShadow = '0 6px 20px rgba(0,0,0,0.08)'; card.style.transform = 'translateY(-2px)'; };
    card.onmouseout  = () => { card.style.borderColor = 'transparent'; card.style.boxShadow = ''; card.style.transform = ''; };
    card.onclick = () => openFolderPanel(deptId);

    const isUnlocked = unlockedFolders.has(deptId);
    const lockIcon = isUnlocked
      ? '<span style="font-size:14px;color:#059669" title="Unlocked">🔓</span>'
      : '<span style="font-size:14px;color:#D97706" title="Secured — Click to verify">🔒</span>';

    card.innerHTML = `
      <div class="card-body padded">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div style="width:52px;height:52px;border-radius:14px;background:${info.bg};display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0">
            ${info.icon}
          </div>
          <div style="flex:1;min-width:0">
            <div style="display:flex;align-items:center;gap:6px">
              <span style="font-size:15px;font-weight:800;color:#1F2937">${deptId}</span>
              ${lockIcon}
            </div>
            <div style="font-size:12px;color:#6B7280;margin-top:2px">📂 ${info.folder}</div>
            <div style="font-size:12px;font-weight:600;color:${info.color};margin-top:6px">${docs.length} document${docs.length !== 1 ? 's' : ''}</div>
          </div>
        </div>
        <div style="display:flex;gap:6px;margin-top:10px;flex-wrap:wrap">${srcBadges}</div>
        <div style="display:flex;gap:8px;margin-top:10px;font-size:11px;flex-wrap:wrap">
          ${active   ? `<span style="background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:6px;font-weight:600">🟢 ${active} Active</span>` : ''}
          ${archived ? `<span style="background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:6px;font-weight:600">📦 ${archived} Archived</span>` : ''}
          ${retained ? `<span style="background:#EDE9FE;color:#5B21B6;padding:2px 8px;border-radius:6px;font-weight:600">🔒 ${retained} Retained</span>` : ''}
        </div>
      </div>`;
    grid.appendChild(card);
  });
}

// Folder grid filters (source, dept, lifecycle)
function filterFolderGrid() {
  const srcFilter  = document.getElementById('folder-filter-source').value;
  const deptFilter = document.getElementById('folder-filter-dept').value;
  const lcFilter   = document.getElementById('folder-filter-lifecycle').value;
  const cards      = document.querySelectorAll('.dept-folder-card');

  let visibleCount = 0;
  cards.forEach(card => {
    const deptId  = card.dataset.dept;
    const sources = (card.dataset.sources || '').split(',');

    // Source filter
    if (srcFilter && !sources.includes(srcFilter)) { card.style.display = 'none'; return; }
    // Dept filter
    if (deptFilter && deptId !== deptFilter) { card.style.display = 'none'; return; }
    // Lifecycle filter: check if dept has docs matching lifecycle
    if (lcFilter) {
      const deptDocs = allDocuments.filter(d => getDocDept(d) === deptId);
      const hasLC = deptDocs.some(d => getLifecycle(getDocDate(d)).status === lcFilter);
      if (!hasLC) { card.style.display = 'none'; return; }
    }

    card.style.display = '';
    visibleCount++;
  });

  document.getElementById('folder-summary').textContent =
    visibleCount + ' department folder' + (visibleCount !== 1 ? 's' : '') + ' shown';
}

function openFolderPanel(deptId) {
  // Per-folder OTP: check if this specific folder was already unlocked this session
  if (unlockedFolders.has(deptId)) {
    showFolderContents(deptId);
    return;
  }

  // Check server-side if this folder was already verified (within 5 min)
  fetch(API + '?action=check_folder_access&folder=' + encodeURIComponent(deptId))
    .then(r => r.json())
    .then(json => {
      if (json.verified && json.folder === deptId) {
        unlockedFolders.add(deptId);
        showFolderContents(deptId);
      } else {
        // Show per-folder PIN modal
        showFolderPinModal(deptId);
      }
    })
    .catch(() => {
      showFolderPinModal(deptId);
    });
}

function showFolderContents(deptId) {
  const panel = document.getElementById('folder-panel');
  const info  = getDeptInfo(deptId);
  const sec   = getFolderSecurity(deptId);
  // Show ALL documents (local + external) matching this department
  const docs  = allDocuments.filter(d => getDocDept(d) === deptId);

  document.getElementById('folder-panel-title').innerHTML = `📂 ${deptId} — ${info.folder}`;
  document.getElementById('folder-panel-count').textContent = `${docs.length} document${docs.length !== 1 ? 's' : ''}`;

  // Store current folder documents for pagination
  window._currentFolderDocs = docs;
  
  // Reset pagination and render
  Paginator.reset('doc-folder-panel');
  renderFolderPanel(docs);
  
  panel.style.display = '';
  panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function renderFolderPanel(docs) {
  const body = document.getElementById('folder-panel-body');
  
  if (!docs.length) {
    body.innerHTML = '<div class="empty-state" style="padding:30px"><div style="font-size:40px;margin-bottom:8px">📭</div><div style="font-weight:600">No documents in this folder</div></div>';
    Paginator.setTotalItems('doc-folder-panel', 0);
    Paginator.renderControls('doc-folder-panel', 'folder-panel-pagination');
    return;
  }

  // Sort documents by date (newest first)
  docs.sort((a, b) => new Date(getDocDate(b)) - new Date(getDocDate(a)));
  
  // Generate all document rows
  var allRows = docs.map(doc => {
    const lc       = getLifecycle(getDocDate(doc));
    const docId    = getDocId(doc);
    const code     = getDocCode(doc);
    const title    = doc.title || '';
    const fileType = getDocFileType(doc);
    const fileSize = getDocFileSize(doc);
    const desc     = doc.description || '';
    const conf     = doc.confidentiality || '';
    const dateStr  = getDocDate(doc);
    const viewSvg  = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';

    return `
      <div style="border:1px solid #E5E7EB;border-radius:12px;padding:16px;margin-bottom:10px;display:flex;gap:14px;align-items:flex-start;transition:all 0.2s;cursor:pointer;border-left:4px solid ${lc.color}"
           onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.06)'" onmouseout="this.style.boxShadow='none'"
           onclick="viewDocument(${docId})">
        <div style="width:44px;height:44px;border-radius:10px;background:${lc.bg};display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">
          ${getFileIcon(doc)}
        </div>
        <div style="flex:1;min-width:0">
          <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;flex-wrap:wrap">
            <div>
              <div style="font-weight:700;font-size:14px;color:#1F2937">${title}</div>
              <div style="font-size:11px;color:#9CA3AF;margin-top:2px">${code}${fileType ? ' · ' + fileType : ''}${fileSize ? ' · ' + fileSize : ''}</div>
            </div>
            <div style="display:flex;gap:6px;align-items:center;flex-shrink:0">
              <span class="badge ${lc.badge}" style="font-size:11px">${lc.label}</span>
            </div>
          </div>
          ${desc ? `<div style="font-size:12px;color:#6B7280;margin-top:6px;line-height:1.5">${desc}</div>` : ''}
          <div style="display:flex;gap:14px;margin-top:8px;font-size:11px;color:#9CA3AF;flex-wrap:wrap">
            <span>📅 ${fmtDate(dateStr)}</span>
            <span>⏱ ${getAge(dateStr)}</span>
            ${conf ? `<span>${conf === 'Restricted' ? '🔴' : conf === 'Internal' ? '🟡' : '🟢'} ${conf}</span>` : ''}
            ${doc.source ? `<span style="background:#DBEAFE;color:#1E40AF;padding:1px 6px;border-radius:4px;font-weight:700;font-size:10px">🔗 ${doc.source}</span>` : ''}
          </div>
        </div>
        <button class="btn btn-outline btn-sm" style="flex-shrink:0" onclick="event.stopPropagation();viewDocument(${docId})" title="View Document">${viewSvg}</button>
      </div>`;
  });

  // Apply pagination
  var paged = Paginator.paginate('doc-folder-panel', allRows);
  body.innerHTML = '<div style="padding:12px">' + paged.join('') + '</div>';
  Paginator.renderControls('doc-folder-panel', 'folder-panel-pagination');
}

function closeFolderPanel() {
  document.getElementById('folder-panel').style.display = 'none';
}

function filterFolderSearch(query) {
  const q = query.toLowerCase();
  const cards = document.querySelectorAll('.dept-folder-card');
  cards.forEach(card => {
    const deptId = card.dataset.dept;
    const info   = getDeptInfo(deptId);
    const docs   = allDocuments.filter(d => getDocDept(d) === deptId);
    const match  = !q
      || deptId.toLowerCase().includes(q)
      || info.folder.toLowerCase().includes(q)
      || docs.some(d => (d.title || '').toLowerCase().includes(q));
    card.style.display = match ? '' : 'none';
  });
}

// ═══════════════════════════════════════════════════════
// TAB 2 — ALL DOCUMENTS TABLE
// ═══════════════════════════════════════════════════════



// ═══════════════════════════════════════════════════════
// TAB 3 — ARCHIVED DOCUMENTS (6 months – 3 years)
// ═══════════════════════════════════════════════════════



// ═══════════════════════════════════════════════════════
// VIEW DOCUMENT MODAL (View only — no edit, no delete)
// ═══════════════════════════════════════════════════════

async function viewDocument(id) {
  const doc = allDocuments.find(d => getDocId(d) == id);
  if (!doc) return;

  const conf = doc.confidentiality || '';

  // Gate: Restricted or Internal documents require access check
  if (conf === 'Restricted' || conf === 'Internal') {
    try {
      const res = await fetch(API + '?action=check_document_access&document_id=' + id);
      const json = await res.json();
      if (!json.access_granted) {
        // No access — show confidential PIN gate
        pendingConfDocId = id;
        document.getElementById('conf-pin-doc-title').textContent = doc.title || 'Untitled Document';
        document.getElementById('conf-pin-step-request').style.display = '';
        document.getElementById('conf-pin-step-verify').style.display = 'none';
        for (let i = 1; i <= 4; i++) document.getElementById('conf-pin-digit-' + i).value = '';
        openModal('modal-confidential-pin');
        return;
      }
    } catch (err) {
      console.error('Access check error:', err);
      Swal.fire({ icon: 'error', title: 'Access Error', text: 'Could not verify document access.', confirmButtonColor: '#059669' });
      return;
    }
  }

  showDocumentDetail(id);
}

function showDocumentDetail(id) {
  const doc = allDocuments.find(d => getDocId(d) == id);
  if (!doc) return;

  const dateStr  = getDocDate(doc);
  const lc       = getLifecycle(dateStr);
  const docDept  = getDocDept(doc);
  const info     = getDeptInfo(docDept);
  const fileIcon = getFileIcon(doc);
  const code     = getDocCode(doc);
  const title    = doc.title || '';
  const fileType = getDocFileType(doc);
  const fileSize = getDocFileSize(doc);
  const folder   = getDocFolder(doc);
  const type     = getDocType(doc);
  const conf     = doc.confidentiality || '';
  const desc     = doc.description || '';

  let confBadge = '—';
  if (conf === 'Restricted')  confBadge = '<span class="badge badge-red">Restricted</span>';
  else if (conf === 'Internal') confBadge = '<span class="badge badge-amber">Internal</span>';
  else if (conf === 'Public')   confBadge = '<span class="badge badge-green">Public</span>';
  else if (conf) confBadge = `<span class="badge">${conf}</span>`;

  document.getElementById('modal-view-title').textContent = title;
  document.getElementById('modal-view-body').innerHTML = `
    <div>
      <!-- Lifecycle banner -->
      <div style="background:${lc.bg};padding:10px 14px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:700;color:${lc.color};display:flex;justify-content:space-between;align-items:center">
        <span>${lc.label}</span>
        <span style="font-size:11px;font-weight:500">Filed ${getAge(dateStr)} ago</span>
      </div>

      <!-- File Info -->
      <div style="display:flex;align-items:center;gap:14px;padding:14px;background:#F9FAFB;border-radius:12px;margin-bottom:16px">
        <div style="font-size:36px">${fileIcon}</div>
        <div>
          <div style="font-weight:700;font-size:14px;color:#1F2937">${title}</div>
          <div style="font-size:12px;color:#6B7280;margin-top:2px">${code}${fileType ? ' · ' + fileType : ''}${fileSize ? ' · ' + fileSize : ''}</div>
        </div>
      </div>

      <!-- Details Table -->
      <table style="width:100%;font-size:13px;border-collapse:collapse">
        <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:10px 0;font-weight:600;color:#6B7280;width:140px">🏢 Department</td><td style="padding:10px 0;color:#1F2937">${docDept}</td></tr>
        <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:10px 0;font-weight:600;color:#6B7280">📂 Folder</td><td style="padding:10px 0;color:#1F2937">${folder}</td></tr>
        <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:10px 0;font-weight:600;color:#6B7280">📋 Type</td><td style="padding:10px 0;color:#1F2937">${type || '—'}</td></tr>
        <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:10px 0;font-weight:600;color:#6B7280">👤 Designated By</td><td style="padding:10px 0;color:#1F2937">${doc.designated_employee || doc.uploaded_by_name || '—'}</td></tr>
        <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:10px 0;font-weight:600;color:#6B7280">📅 Date Filed</td><td style="padding:10px 0;color:#1F2937">${fmtDate(dateStr)}</td></tr>
        <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:10px 0;font-weight:600;color:#6B7280">🔐 Confidentiality</td><td style="padding:10px 0">${confBadge}</td></tr>
        <tr style="border-bottom:1px solid #F3F4F6"><td style="padding:10px 0;font-weight:600;color:#6B7280">📊 Lifecycle</td><td style="padding:10px 0"><span class="badge ${lc.badge}">${lc.label}</span></td></tr>
        ${desc ? `<tr><td style="padding:10px 0;font-weight:600;color:#6B7280;vertical-align:top">📝 Description</td><td style="padding:10px 0;color:#1F2937;line-height:1.6">${desc}</td></tr>` : ''}
      </table>

      <!-- Lifecycle Timeline -->
      <div style="margin-top:20px;padding:16px;background:#F9FAFB;border-radius:12px">
        <div style="font-weight:700;font-size:13px;color:#1F2937;margin-bottom:12px">📐 Document Lifecycle Timeline</div>
        <div style="display:flex;align-items:center;gap:0;font-size:11px">
          <div style="text-align:center;flex:1">
            <div style="height:6px;background:${lc.status === 'active' || lc.status === 'archived' || lc.status === 'retained' ? '#059669' : '#E5E7EB'};border-radius:3px 0 0 3px"></div>
            <div style="margin-top:6px;font-weight:600;color:${lc.status === 'active' ? '#059669' : '#9CA3AF'}">🟢 Active</div>
            <div style="color:#9CA3AF">0–6 months</div>
          </div>
          <div style="text-align:center;flex:1">
            <div style="height:6px;background:${lc.status === 'archived' || lc.status === 'retained' ? '#D97706' : '#E5E7EB'}"></div>
            <div style="margin-top:6px;font-weight:600;color:${lc.status === 'archived' ? '#D97706' : '#9CA3AF'}">📦 Archive</div>
            <div style="color:#9CA3AF">6mo–3yr</div>
          </div>
          <div style="text-align:center;flex:1">
            <div style="height:6px;background:${lc.status === 'retained' ? '#7C3AED' : '#E5E7EB'};border-radius:0 3px 3px 0"></div>
            <div style="margin-top:6px;font-weight:600;color:${lc.status === 'retained' ? '#7C3AED' : '#9CA3AF'}">🔒 Retain</div>
            <div style="color:#9CA3AF">3yr+ forever</div>
          </div>
        </div>
      </div>

      <div style="margin-top:14px;padding:10px 14px;background:#FEF3C7;border-radius:10px;font-size:12px;color:#92400E;display:flex;align-items:center;gap:8px">
        ⚠️ <span>This document is <strong>view-only</strong>. No deletion or modification is allowed per retention policy.</span>
      </div>
    </div>`;
  currentViewDocId = id;
  openModal('modal-view');
}

// ───── Export Single Document as PDF (with access check) ─────
async function downloadDocumentPDF(docId) {
  const doc = allDocuments.find(d => getDocId(d) == docId);
  if (!doc) return;
  // Access gate for restricted docs
  {
    const conf = doc.confidentiality || '';
    if (conf === 'Restricted' || conf === 'Internal') {
      try {
        const res = await fetch(API + '?action=check_document_access&document_id=' + docId);
        const json = await res.json();
        if (!json.access_granted) {
          Swal.fire({ icon: 'warning', title: 'Access Denied', html: '<div style="font-size:14px">This document is <strong style="color:#DC2626">Restricted</strong>. Please verify your identity first by viewing the document.</div>', confirmButtonColor: '#059669' });
          return;
        }
      } catch (err) { console.error('PDF access check error:', err); return; }
    }
  }
  exportDocumentPDF(docId);
}

function exportDocumentPDF(docId) {
  const id = docId || currentViewDocId;
  const doc = allDocuments.find(d => getDocId(d) == id);
  if (!doc) return;

  const dateStr  = getDocDate(doc);
  const lc       = getLifecycle(dateStr);
  const docDept  = getDocDept(doc);
  const code     = getDocCode(doc);
  const title    = doc.title || 'Untitled Document';
  const folder   = getDocFolder(doc);
  const type     = getDocType(doc);
  const conf     = doc.confidentiality || 'Internal';
  const desc     = doc.description || '';
  const srcInfo  = null;
  const isLive   = false;
  const genDate  = new Date().toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' });

  // ═══════════════════════════════════════════════════════════
  // DocuFormatter path: for local records
  // ═══════════════════════════════════════════════════════════
  let records = [];

  // Use DocuFormatter for the professional A-F format PDF
  if (typeof DocuFormatter !== 'undefined') {
    DocuFormatter.generate({
      title: title,
      doc_code: code || '',
      department: docDept || '',
      source_module: '',
      confidentiality: conf,
      status: lc.label.replace(/[^\w\s]/g, '').trim() || 'Active',
      prepared_by: doc.uploaded_by || doc.created_by || '',
      generated_datetime: genDate,
      description: desc,
      records: records,
    });
    return;
  }

  // ═══════════════════════════════════════════════════════════
  // Legacy fallback (if DocuFormatter not loaded)
  // ═══════════════════════════════════════════════════════════
  const age      = getAge(dateStr);
  const fileType = getDocFileType(doc);
  const fileSize = getDocFileSize(doc);
  const dateFiled = fmtDate(dateStr);

  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF('p', 'mm', 'a4');
  const W = pdf.internal.pageSize.getWidth();
  const pageH = pdf.internal.pageSize.getHeight();
  const margin = 18;
  const innerW = W - margin * 2;

  // Color palette
  const brandGreen  = [5, 150, 105];
  const darkGreen   = [4, 120, 87];
  const navy        = [15, 23, 42];
  const gray600     = [75, 85, 99];
  const gray400     = [156, 163, 175];
  const gray200     = [229, 231, 235];
  const white       = [255, 255, 255];
  const srcRGB = srcInfo ? hexToRGB(srcInfo.color) : brandGreen;

  function hexToRGB(hex) {
    const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
    return [r, g, b];
  }

  function addPageFooter(pageNum, totalPages) {
    // Decorative bottom bar
    pdf.setFillColor(...brandGreen);
    pdf.rect(0, pageH - 8, W, 8, 'F');
    // Footer text area
    pdf.setFillColor(248, 250, 252);
    pdf.rect(0, pageH - 22, W, 14, 'F');
    pdf.setDrawColor(...gray200);
    pdf.line(margin, pageH - 22, W - margin, pageH - 22);
    pdf.setFontSize(7);
    pdf.setFont('helvetica', 'normal');
    pdf.setTextColor(...gray400);
    pdf.text('MICROFINANCIAL ADMIN  |  Document Management System', margin, pageH - 14);
    pdf.text('CONFIDENTIAL', W / 2, pageH - 14, { align: 'center' });
    pdf.text('Page ' + pageNum + ' of ' + totalPages, W - margin, pageH - 14, { align: 'right' });
    pdf.setFontSize(6);
    pdf.text('Generated: ' + genDate, margin, pageH - 10);
    pdf.text('Ref: ' + (code || 'N/A'), W - margin, pageH - 10, { align: 'right' });
  }

  function checkPageBreak(needed) { if (y > pageH - needed - 30) { pdf.addPage(); y = 18; return true; } return false; }

  let y = 0;

  // ═══════════════════════════════════════════
  // PAGE 1: LETTERHEAD + DOCUMENT INFO
  // ═══════════════════════════════════════════

  // ——— Top accent bar ———
  pdf.setFillColor(...brandGreen);
  pdf.rect(0, 0, W, 4, 'F');

  // ——— Letterhead area ———
  pdf.setFillColor(248, 250, 252);
  pdf.rect(0, 4, W, 38, 'F');

  // Company name + logo area
  pdf.setFontSize(22);
  pdf.setFont('helvetica', 'bold');
  pdf.setTextColor(...navy);
  pdf.text('MICROFINANCIAL', margin, 20);

  pdf.setFontSize(9);
  pdf.setFont('helvetica', 'normal');
  pdf.setTextColor(...gray600);
  pdf.text('Admin Document Management System', margin, 27);

  // Right side: Document reference box
  pdf.setFillColor(...white);
  pdf.setDrawColor(...brandGreen);
  pdf.setLineWidth(0.5);
  pdf.roundedRect(W - margin - 62, 10, 62, 26, 2, 2, 'FD');
  pdf.setFontSize(7);
  pdf.setFont('helvetica', 'bold');
  pdf.setTextColor(...brandGreen);
  pdf.text('DOCUMENT REF', W - margin - 31, 18, { align: 'center' });
  pdf.setFontSize(8);
  pdf.setFont('helvetica', 'bold');
  pdf.setTextColor(...navy);
  const codeText = code || 'N/A';
  const codeSize = codeText.length > 22 ? 6 : (codeText.length > 16 ? 7 : 8);
  pdf.setFontSize(codeSize);
  pdf.text(codeText, W - margin - 31, 25, { align: 'center' });
  pdf.setFontSize(6);
  pdf.setFont('helvetica', 'normal');
  pdf.setTextColor(...gray400);
  pdf.text(dateFiled || 'N/A', W - margin - 31, 31, { align: 'center' });

  // Divider line under letterhead
  pdf.setDrawColor(...brandGreen);
  pdf.setLineWidth(0.8);
  pdf.line(margin, 42, W - margin, 42);
  pdf.setLineWidth(0.2);
  pdf.setDrawColor(...gray200);
  pdf.line(margin, 43.5, W - margin, 43.5);

  y = 52;

  // ——— Document Title Block ———
  pdf.setFontSize(18);
  pdf.setFont('helvetica', 'bold');
  pdf.setTextColor(...navy);
  const titleLines = pdf.splitTextToSize(title, innerW);
  pdf.text(titleLines, margin, y);
  y += titleLines.length * 8 + 2;

  // Subtitle line
  pdf.setFontSize(10);
  pdf.setFont('helvetica', 'normal');
  pdf.setTextColor(...gray600);
  const subtitleParts = [type, docDept, folder].filter(Boolean);
  if (subtitleParts.length) { pdf.text(subtitleParts.join('  |  '), margin, y); y += 6; }

  // ——— Status + Source badges inline ———
  const statusColors = { active: [5,150,105], archived: [217,119,6], retained: [124,58,237] };
  const sc = statusColors[lc.status] || gray600;
  const statusLabel = lc.label.replace(/[^\w\s]/g, '').trim();
  pdf.setFontSize(8);
  pdf.setFont('helvetica', 'bold');
  const statusBadgeW = pdf.getTextWidth(statusLabel) + 10;
  pdf.setFillColor(...sc);
  pdf.roundedRect(margin, y - 4, statusBadgeW, 7, 2, 2, 'F');
  pdf.setTextColor(...white);
  pdf.text(statusLabel, margin + 5, y);
  let bx = margin + statusBadgeW + 4;

  if (isLive && srcInfo) {
    const srcLabel = srcInfo.name.split(' — ')[0] || srcInfo.name;
    const srcBadgeW = pdf.getTextWidth(srcLabel) + 10;
    pdf.setFillColor(...srcRGB);
    pdf.roundedRect(bx, y - 4, srcBadgeW, 7, 2, 2, 'F');
    pdf.setTextColor(...white);
    pdf.text(srcLabel, bx + 5, y);
    bx += srcBadgeW + 4;
  }

  const confColors = { 'Restricted': [220,38,38], 'Internal': [37,99,235], 'Public': [5,150,105] };
  const cc = confColors[conf] || gray600;
  const confBadgeW = pdf.getTextWidth(conf) + 10;
  pdf.setFillColor(...cc);
  pdf.roundedRect(bx, y - 4, confBadgeW, 7, 2, 2, 'F');
  pdf.setTextColor(...white);
  pdf.text(conf, bx + 5, y);
  y += 14;

  // ——— Thin separator ———
  pdf.setDrawColor(...gray200);
  pdf.line(margin, y - 4, W - margin, y - 4);

  // ——— TWO-COLUMN DOCUMENT PROPERTIES ———
  const leftCol = [
    ['Document Code', code || 'N/A'],
    ['Department', docDept || 'N/A'],
    ['Document Type', type || 'N/A'],
    ['Folder / Category', folder || 'N/A'],
    ['Confidentiality', conf],
  ];
  const rightCol = [
    ['Date Filed', dateFiled || 'N/A'],
    ['Document Age', age || 'N/A'],
    ['Lifecycle Stage', statusLabel],
    ['File Format', fileType || 'N/A'],
    ['File Size', fileSize || 'N/A'],
  ];
  if (isLive && srcInfo) { rightCol.push(['Source System', srcInfo.name]); }

  // Section header
  pdf.setFillColor(248, 250, 252);
  pdf.roundedRect(margin, y, innerW, 8, 2, 2, 'F');
  pdf.setFontSize(9);
  pdf.setFont('helvetica', 'bold');
  pdf.setTextColor(...navy);
  pdf.text('DOCUMENT PROPERTIES', margin + 4, y + 5.5);
  y += 12;

  const colW = (innerW - 8) / 2;
  const propStartY = y;
  const maxRows = Math.max(leftCol.length, rightCol.length);
  for (let i = 0; i < maxRows; i++) {
    const rowY = propStartY + i * 9;
    if (i % 2 === 0) {
      pdf.setFillColor(249, 250, 251);
      pdf.rect(margin, rowY - 3, innerW, 9, 'F');
    }
    if (leftCol[i]) {
      pdf.setFontSize(8);
      pdf.setFont('helvetica', 'bold');
      pdf.setTextColor(...gray600);
      pdf.text(leftCol[i][0], margin + 4, rowY + 2);
      pdf.setFont('helvetica', 'normal');
      pdf.setTextColor(...navy);
      pdf.text(leftCol[i][1], margin + 46, rowY + 2);
    }
    if (rightCol[i]) {
      const rx = margin + colW + 8;
      pdf.setFontSize(8);
      pdf.setFont('helvetica', 'bold');
      pdf.setTextColor(...gray600);
      pdf.text(rightCol[i][0], rx, rowY + 2);
      pdf.setFont('helvetica', 'normal');
      pdf.setTextColor(...navy);
      pdf.text(String(rightCol[i][1]).substring(0, 40), rx + 42, rowY + 2);
    }
  }
  y = propStartY + maxRows * 9 + 6;

  // ——— DESCRIPTION SECTION ———
  if (desc) {
    checkPageBreak(35);
    pdf.setFillColor(...brandGreen);
    pdf.rect(margin, y, 3, 8, 'F');
    pdf.setFillColor(248, 250, 252);
    pdf.rect(margin + 3, y, innerW - 3, 8, 'F');
    pdf.setFontSize(9);
    pdf.setFont('helvetica', 'bold');
    pdf.setTextColor(...navy);
    pdf.text('DESCRIPTION', margin + 8, y + 5.5);
    y += 12;

    pdf.setFillColor(255, 255, 255);
    pdf.setDrawColor(...gray200);
    const descLines = pdf.splitTextToSize(desc, innerW - 12);
    const descH = descLines.length * 4.5 + 8;
    pdf.roundedRect(margin, y, innerW, descH, 2, 2, 'FD');
    pdf.setFontSize(9);
    pdf.setFont('helvetica', 'normal');
    pdf.setTextColor(55, 65, 81);
    pdf.text(descLines, margin + 6, y + 6);
    y += descH + 8;
  }

  // ——— LIFECYCLE TIMELINE ———
  checkPageBreak(40);
  pdf.setFillColor(...brandGreen);
  pdf.rect(margin, y, 3, 8, 'F');
  pdf.setFillColor(248, 250, 252);
  pdf.rect(margin + 3, y, innerW - 3, 8, 'F');
  pdf.setFontSize(9);
  pdf.setFont('helvetica', 'bold');
  pdf.setTextColor(...navy);
  pdf.text('DOCUMENT LIFECYCLE', margin + 8, y + 5.5);
  y += 14;

  const stages = [
    { label: 'Active',  range: '0 - 6 months',  color: [5,150,105],   key: 'active' },
    { label: 'Archive', range: '6 mo - 3 years', color: [217,119,6],   key: 'archived' },
    { label: 'Retain',  range: '3 years+',       color: [124,58,237],  key: 'retained' },
  ];
  const stageW = (innerW - 12) / 3;
  const reached = lc.status === 'active' ? 1 : lc.status === 'archived' ? 2 : 3;
  stages.forEach((st, i) => {
    const x = margin + 2 + i * (stageW + 4);
    const isReached = (i + 1) <= reached;
    const isCurrent = st.key === lc.status;
    // Stage card
    pdf.setFillColor(...(isReached ? st.color : [240,240,240]));
    pdf.roundedRect(x, y, stageW, 6, 2, 2, 'F');
    // Label below
    pdf.setFontSize(9);
    pdf.setFont('helvetica', 'bold');
    pdf.setTextColor(...(isCurrent ? st.color : gray400));
    pdf.text(st.label, x + stageW/2, y + 14, { align: 'center' });
    if (isCurrent) {
      pdf.setFontSize(7);
      pdf.setTextColor(...st.color);
      pdf.text('(CURRENT)', x + stageW/2, y + 19, { align: 'center' });
    }
    pdf.setFontSize(7);
    pdf.setFont('helvetica', 'normal');
    pdf.setTextColor(...gray400);
    pdf.text(st.range, x + stageW/2, y + (isCurrent ? 24 : 19), { align: 'center' });
    // Arrow between stages
    if (i < 2) {
      const ax = x + stageW + 1;
      pdf.setFillColor(...(isReached && (i+2) <= reached ? st.color : gray200));
      pdf.triangle(ax, y + 1, ax, y + 5, ax + 2.5, y + 3, 'F');
    }
  });
  y += 30;

  // ——— RETENTION NOTICE ———
  checkPageBreak(18);
  pdf.setFillColor(254, 252, 232);
  pdf.setDrawColor(253, 224, 71);
  pdf.setLineWidth(0.3);
  pdf.roundedRect(margin, y, innerW, 12, 2, 2, 'FD');
  pdf.setFontSize(7.5);
  pdf.setFont('helvetica', 'normal');
  pdf.setTextColor(146, 64, 14);
  pdf.text('NOTICE: This document is managed under the retention policy framework. Unauthorized modification or deletion is prohibited.', margin + 4, y + 7);
  y += 18;

  // ——— CERTIFICATION BLOCK (for official look) ———
  if (isLive) {
    checkPageBreak(30);
    pdf.setDrawColor(...gray200);
    pdf.setLineWidth(0.3);
    pdf.roundedRect(margin, y, innerW, 22, 2, 2, 'D');
    pdf.setFontSize(8);
    pdf.setFont('helvetica', 'bold');
    pdf.setTextColor(...navy);
    pdf.text('DATA CERTIFICATION', margin + 4, y + 6);
    pdf.setFontSize(7.5);
    pdf.setFont('helvetica', 'normal');
    pdf.setTextColor(...gray600);
    const certText = 'This document was generated from data sourced from ' + (srcInfo ? srcInfo.name : 'an integrated system') + '. The information reflects the state of the record at the time of export (' + genDate + '). This copy is provided for reference purposes.';
    const certLines = pdf.splitTextToSize(certText, innerW - 10);
    pdf.text(certLines, margin + 4, y + 12);
    y += 28;
  }

  // ——— ADD FOOTERS TO ALL PAGES ———
  const totalPages = pdf.internal.getNumberOfPages();
  for (let p = 1; p <= totalPages; p++) { pdf.setPage(p); addPageFooter(p, totalPages); }

  // ——— Save ———
  const safeName = title.replace(/[^a-zA-Z0-9]/g, '_').substring(0, 40);
  pdf.save(`${code || 'Document'}_${safeName}.pdf`);
}

// ───── Section Switching (hash-driven) ─────
function showSection(hash) {
  const sections = document.querySelectorAll('.tab-content');
  const id = hash ? hash.replace('#', '') : 'tab-folders';

  // Highlight active directory card
  document.querySelectorAll('.module-directory-label + .stats-grid .stat-card-link').forEach(c => {
    const href = c.getAttribute('href') || '';
    c.classList.toggle('active-module', href === '#' + id);
    const arrow = c.querySelector('.stat-arrow');
    if (arrow) arrow.textContent = href === '#' + id ? '●' : '→';
  });

  sections.forEach(s => s.classList.remove('active'));
  const target = document.getElementById(id);
  if (target) target.classList.add('active');
  else if (sections[0]) sections[0].classList.add('active');

  if (id === 'tab-folders') checkFolderAccess();
  if (id === 'tab-archiving') checkArchiveAccess();
  if (id === 'tab-access-control') loadAccessControl();
  if (id === 'tab-doc-analytics') renderDocAnalytics();
  if (id === 'tab-view-audit')    { loadViewLogs(); loadViewLogStats(); }
  if (id === 'tab-access-requests') { loadAccessRequests(); loadAccessRequestStats(); }
}
window.addEventListener('hashchange', () => showSection(location.hash));

// ───── Initialize on Load ─────
loadData().then(() => {
  showSection(location.hash);
});

// ─── Real-time auto-refresh every 30 seconds ───
setInterval(async () => {
  try {
    const [sRes, dRes, fRes] = await Promise.all([
      fetch(API + '?action=dashboard_stats'),
      fetch(API + '?action=list_documents'),
      fetch(API + '?action=list_folders')
    ]);
    const sJson = await sRes.json();
    const dJson = await dRes.json();
    const fJson = await fRes.json();
    stats        = sJson.data || sJson || {};
    allDocuments  = dJson.data || dJson || [];
    folders      = fJson.data || fJson || [];
    if (!Array.isArray(allDocuments)) allDocuments = [];

    renderStats();
    renderDeptGrid();
    // If analytics tab is visible, refresh it
    const analyticsTab = document.getElementById('tab-doc-analytics');
    if (analyticsTab && analyticsTab.classList.contains('active')) renderDocAnalytics();
  } catch(e) { console.error('Auto-refresh error:', e); }
  const ts = document.getElementById('updated-doc-analytics');
  if (ts) ts.textContent = new Date().toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', second:'2-digit', hour12:true });
}, 30000);

// ───── Export Functions ─────


// ═══════════════════════════════════════════════════════
// SUBMODULE: DOCUMENT ANALYTICS
// ═══════════════════════════════════════════════════════

function renderDocAnalytics() {
  const docs = allDocuments || [];
  const now = new Date();
  const thisMonth = now.getMonth();
  const thisYear = now.getFullYear();

  // Stats
  const totalDocs = docs.length;
  const folders = new Set();
  const depts = new Set();
  let filedThisMonth = 0;

  docs.forEach(d => {
    const dept = getDocDept(d);
    const folder = getDocFolder(d);
    if (dept) depts.add(dept);
    if (folder) folders.add(folder);
    const dt = new Date(getDocDate(d));
    if (dt.getMonth() === thisMonth && dt.getFullYear() === thisYear) filedThisMonth++;
  });

  document.getElementById('da-total').textContent = totalDocs;
  document.getElementById('da-folders').textContent = folders.size;
  document.getElementById('da-this-month').textContent = filedThisMonth;
  document.getElementById('da-depts').textContent = depts.size;

  // --- Documents by Department (horizontal bars) ---
  const deptCounts = {};
  docs.forEach(d => {
    const dept = getDocDept(d) || 'Uncategorized';
    deptCounts[dept] = (deptCounts[dept] || 0) + 1;
  });
  const sortedDepts = Object.entries(deptCounts).sort((a, b) => b[1] - a[1]);
  const maxDeptCount = sortedDepts.length > 0 ? sortedDepts[0][1] : 1;

  let deptHtml = '';
  sortedDepts.forEach(([dept, count], i) => {
    const pct = Math.round((count / maxDeptCount) * 100);
    const info = getDeptInfo(dept);
    const color = info.color || '#6B7280';
    deptHtml += `<div style="margin-bottom:10px">
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
        <span style="font-weight:600;color:#374151">${info.icon || '📁'} ${dept}</span>
        <span style="font-weight:700;color:${color}">${count}</span>
      </div>
      <div style="height:8px;background:#F3F4F6;border-radius:4px;overflow:hidden">
        <div style="height:100%;width:${pct}%;background:${color};border-radius:4px;transition:width 0.6s ease"></div>
      </div>
    </div>`;
  });
  document.getElementById('da-dept-bars').innerHTML = deptHtml || '<div style="color:#9CA3AF;font-size:13px;text-align:center;padding:20px">No data</div>';

  // --- Lifecycle Distribution ---
  let active = 0, archived = 0, retained = 0;
  docs.forEach(d => {
    const lc = getLifecycle(getDocDate(d));
    if (lc.status === 'active') active++;
    else if (lc.status === 'archived') archived++;
    else retained++;
  });
  const lcTotal = active + archived + retained || 1;
  const lcData = [
    { label: 'Active', count: active, color: '#059669', bg: '#D1FAE5', icon: '🟢' },
    { label: 'Archived', count: archived, color: '#D97706', bg: '#FEF3C7', icon: '📦' },
    { label: 'Retained', count: retained, color: '#7C3AED', bg: '#EDE9FE', icon: '🔒' }
  ];
  let lcHtml = '';
  lcData.forEach(lc => {
    const pct = Math.round((lc.count / lcTotal) * 100);
    lcHtml += `<div style="display:flex;align-items:center;gap:12px">
      <span style="font-size:20px">${lc.icon}</span>
      <div style="flex:1">
        <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
          <span style="font-weight:600;color:${lc.color}">${lc.label}</span>
          <span style="font-weight:700;color:${lc.color}">${lc.count} (${pct}%)</span>
        </div>
        <div style="height:10px;background:#F3F4F6;border-radius:5px;overflow:hidden">
          <div style="height:100%;width:${pct}%;background:${lc.color};border-radius:5px;transition:width 0.6s ease"></div>
        </div>
      </div>
    </div>`;
  });
  document.getElementById('da-lifecycle-chart').innerHTML = lcHtml;

  // --- Document Types ---
  const typeCounts = {};
  docs.forEach(d => {
    const t = getDocType(d) || 'Unknown';
    typeCounts[t] = (typeCounts[t] || 0) + 1;
  });
  const sortedTypes = Object.entries(typeCounts).sort((a, b) => b[1] - a[1]);
  const maxTypeCount = sortedTypes.length > 0 ? sortedTypes[0][1] : 1;
  const typeColors = ['#3B82F6', '#059669', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6'];

  let typeHtml = '';
  sortedTypes.forEach(([type, count], i) => {
    const pct = Math.round((count / maxTypeCount) * 100);
    const color = typeColors[i % typeColors.length];
    typeHtml += `<div style="margin-bottom:10px">
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
        <span style="font-weight:600;color:#374151">${type}</span>
        <span style="font-weight:700;color:${color}">${count}</span>
      </div>
      <div style="height:8px;background:#F3F4F6;border-radius:4px;overflow:hidden">
        <div style="height:100%;width:${pct}%;background:${color};border-radius:4px;transition:width 0.6s ease"></div>
      </div>
    </div>`;
  });
  document.getElementById('da-type-chart').innerHTML = typeHtml || '<div style="color:#9CA3AF;font-size:13px;text-align:center;padding:20px">No data</div>';

  // --- Filing Trend (Last 6 Months) ---
  const months = [];
  for (let i = 5; i >= 0; i--) {
    const d = new Date(thisYear, thisMonth - i, 1);
    months.push({ key: d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0'), label: d.toLocaleString('default', { month: 'short', year: '2-digit' }), count: 0 });
  }
  docs.forEach(d => {
    const dt = new Date(getDocDate(d));
    const key = dt.getFullYear() + '-' + String(dt.getMonth() + 1).padStart(2, '0');
    const m = months.find(m => m.key === key);
    if (m) m.count++;
  });
  const maxTrend = Math.max(...months.map(m => m.count), 1);

  let trendHtml = '<div style="display:flex;align-items:flex-end;gap:8px;height:180px">';
  months.forEach(m => {
    const h = Math.max(Math.round((m.count / maxTrend) * 150), 4);
    trendHtml += `<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
      <div style="font-size:11px;font-weight:700;color:#059669">${m.count}</div>
      <div style="width:100%;height:${h}px;background:linear-gradient(180deg,#059669,#34D399);border-radius:6px 6px 2px 2px;transition:height 0.6s ease"></div>
      <div style="font-size:10px;color:#6B7280;font-weight:600">${m.label}</div>
    </div>`;
  });
  trendHtml += '</div>';
  document.getElementById('da-trend-chart').innerHTML = trendHtml;

  // --- Recently Filed Documents ---
  const recent = [...docs].sort((a, b) => new Date(getDocDate(b)) - new Date(getDocDate(a))).slice(0, 10);
  var recentRows = [];
  recent.forEach(doc => {
    const lc = getLifecycle(getDocDate(doc));
    recentRows.push(`<tr>
      <td><span style="font-weight:700;color:#059669;font-size:12px">${getDocCode(doc)}</span></td>
      <td style="font-weight:600">${doc.title || ''}</td>
      <td>${getDocFolder(doc)}</td>
      <td>${getDocDept(doc)}</td>
      <td><span style="font-size:11px;padding:2px 8px;border-radius:6px;background:#F3F4F6;color:#374151">${getDocType(doc)}</span></td>
      <td>${fmtDate(getDocDate(doc))}</td>
      <td><span class="status-badge" style="font-size:11px;${lc.style}">${lc.icon} ${lc.status}</span></td>
      <td style="font-size:12px;color:#6B7280">${getAge(getDocDate(doc))}</td>
    </tr>`);
  });
  if (!recentRows.length) {
    document.getElementById('da-recent-tbody').innerHTML = '<tr><td colspan="8" class="text-center text-gray-400 py-8">No documents found</td></tr>';
    Paginator.setTotalItems('doc-da-recent', 0);
    Paginator.renderControls('doc-da-recent', 'da-recent-pagination');
  } else {
    var paged = Paginator.paginate('doc-da-recent', recentRows);
    document.getElementById('da-recent-tbody').innerHTML = paged.join('');
    Paginator.renderControls('doc-da-recent', 'da-recent-pagination');
  }
}

function exportDocAnalytics(type, format) {
  if (type === 'dept') {
    const deptCounts = {};
    (allDocuments || []).forEach(d => {
      const dept = getDocDept(d) || 'Uncategorized';
      deptCounts[dept] = (deptCounts[dept] || 0) + 1;
    });
    const headers = ['Department', 'Document Count', 'Percentage'];
    const total = allDocuments.length || 1;
    const rows = Object.entries(deptCounts).sort((a, b) => b[1] - a[1]).map(([dept, count]) => [dept, count, Math.round((count / total) * 100) + '%']);
    format === 'csv' ? ExportHelper.exportCSV('Documents_By_Department', headers, rows)
      : ExportHelper.exportPDF('Documents_By_Department', 'Document Analytics — By Department', headers, rows, { subtitle: allDocuments.length + ' documents across ' + Object.keys(deptCounts).length + ' departments' });
  }
}



// ═══════════════════════════════════════════════════════
// SUBMODULE: ARCHIVING (with Security PIN)
// ═══════════════════════════════════════════════════════

let archivePinVerified = false;
let pinCountdownInterval = null;

// Check if archive access is already granted (session-based)
async function checkArchiveAccess() {
  try {
    const res = await fetch(API + '?action=check_archive_access');
    const json = await res.json();
    if (json.verified) {
      archivePinVerified = true;
      showArchiveContent();
    } else {
      archivePinVerified = false;
      document.getElementById('archive-pin-gate').style.display = '';
      document.getElementById('archive-content').style.display = 'none';
    }
  } catch (err) {
    console.error('Check archive access error:', err);
  }
}

async function requestArchivePin() {
  try {
    const reqBtn = document.querySelector('#pin-step-request button');
    if (reqBtn) { reqBtn.disabled = true; reqBtn.textContent = 'Sending...'; }

    const res = await fetch(API + '?action=send_archive_pin', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({})
    });
    const json = await res.json();

    if (json.success) {
      // SweetAlert: OTP sent successfully
      Swal.fire({
        icon: 'success',
        title: 'OTP Sent!',
        text: json.mail_failed ? 'Security PIN generated. Check fallback code below.' : 'A security PIN has been sent to your registered email address.',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false,
        confirmButtonColor: '#059669',
        customClass: { popup: 'rounded-2xl' }
      });

      document.getElementById('pin-step-request').style.display = 'none';
      document.getElementById('pin-step-verify').style.display = '';

      // Show fallback PIN if email failed
      if (json.mail_failed && json.fallback_pin) {
        document.getElementById('pin-fallback-display').style.display = '';
        document.getElementById('pin-fallback-code').textContent = json.fallback_pin;
      } else {
        document.getElementById('pin-fallback-display').style.display = 'none';
      }

      // Clear pin inputs
      for (let i = 1; i <= 4; i++) {
        document.getElementById('pin-digit-' + i).value = '';
      }
      document.getElementById('pin-digit-1').focus();

      // Start countdown
      startPinCountdown(json.expires_in || 120);
    }
  } catch (err) {
    console.error('Request archive PIN error:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not send security PIN.', confirmButtonColor: '#059669' });
  } finally {
    const reqBtn = document.querySelector('#pin-step-request button');
    if (reqBtn) { reqBtn.disabled = false; reqBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Send Security PIN'; }
  }
}

async function verifyArchivePin() {
  const pin = [1,2,3,4].map(i => document.getElementById('pin-digit-' + i).value).join('');
  if (pin.length !== 4) {
    Swal.fire({ icon: 'warning', title: 'Incomplete PIN', text: 'Please enter all 4 digits.', confirmButtonColor: '#059669' });
    return;
  }

  try {
    const res = await fetch(API + '?action=verify_archive_pin', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ pin })
    });
    const json = await res.json();

    if (json.success) {
      archivePinVerified = true;
      if (pinCountdownInterval) clearInterval(pinCountdownInterval);
      Swal.fire({ icon: 'success', title: 'Verified!', text: 'Archive access granted.', confirmButtonColor: '#059669', timer: 1500 });
      showArchiveContent();
    } else if (json.expired) {
      Swal.fire({ icon: 'warning', title: 'PIN Expired', text: 'Please request a new PIN.', confirmButtonColor: '#059669' });
      resetPinUI();
    } else {
      Swal.fire({ icon: 'error', title: 'Invalid PIN', text: json.message || 'Please try again.', confirmButtonColor: '#059669' });
      // Clear inputs
      for (let i = 1; i <= 4; i++) document.getElementById('pin-digit-' + i).value = '';
      document.getElementById('pin-digit-1').focus();
    }
  } catch (err) {
    console.error('Verify PIN error:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Verification failed.', confirmButtonColor: '#059669' });
  }
}

function showArchiveContent() {
  document.getElementById('archive-pin-gate').style.display = 'none';
  document.getElementById('archive-content').style.display = '';
  loadArchiving();
}

function resetPinUI() {
  document.getElementById('pin-step-request').style.display = '';
  document.getElementById('pin-step-verify').style.display = 'none';
  if (pinCountdownInterval) clearInterval(pinCountdownInterval);
}

function startPinCountdown(seconds) {
  let remaining = seconds;
  const el = document.getElementById('pin-countdown');
  if (pinCountdownInterval) clearInterval(pinCountdownInterval);
  el.textContent = remaining;
  pinCountdownInterval = setInterval(() => {
    remaining--;
    el.textContent = remaining;
    if (remaining <= 0) {
      clearInterval(pinCountdownInterval);
      Swal.fire({ icon: 'warning', title: 'PIN Expired', text: 'Please request a new PIN.', confirmButtonColor: '#059669' });
      resetPinUI();
    }
  }, 1000);
}

// PIN input auto-focus helpers
function pinAutoFocus(el, nextNum) {
  el.value = el.value.replace(/[^0-9]/g, '');
  if (el.value && nextNum) {
    document.getElementById('pin-digit-' + nextNum).focus();
  }
  // Auto-submit when all 4 digits entered
  if (!nextNum && el.value) {
    const pin = [1,2,3,4].map(i => document.getElementById('pin-digit-' + i).value).join('');
    if (pin.length === 4) verifyArchivePin();
  }
}

function pinKeyNav(event, currentNum) {
  if (event.key === 'Backspace' && !event.target.value && currentNum > 1) {
    document.getElementById('pin-digit-' + (currentNum - 1)).focus();
  }
  if (event.key === 'Enter') {
    verifyArchivePin();
  }
}


// ═══════════════════════════════════════════════════════
// SUBMODULE: DEPARTMENT FOLDERS (with Security PIN)
// ═══════════════════════════════════════════════════════

let currentPinFolder = null;
let fpinCountdownInterval = null;

async function checkFolderAccess() {
  // No global gate — folders render immediately, per-folder PIN on click
  document.getElementById('folder-content').style.display = '';
  renderDeptGrid();
}

function showFolderPinModal(deptId) {
  currentPinFolder = deptId;
  const info = getDeptInfo(deptId);
  document.getElementById('folder-pin-title').textContent = deptId + ' — ' + info.folder;
  document.getElementById('folder-pin-level').textContent = '🔒 Secured — Email OTP required';
  // Reset to step 1 (Send PIN)
  document.getElementById('fpin-step-request').style.display = '';
  document.getElementById('fpin-step-verify').style.display = 'none';
  document.getElementById('fpin-fallback-display').style.display = 'none';
  for (let i = 1; i <= 4; i++) { const el = document.getElementById('fpin-digit-' + i); if (el) el.value = ''; }
  if (fpinCountdownInterval) clearInterval(fpinCountdownInterval);
  openModal('modal-folder-pin');
}

async function requestFolderPin() {
  const folder = currentPinFolder || 'Department';
  try {
    const reqBtn = document.querySelector('#fpin-step-request button');
    if (reqBtn) { reqBtn.disabled = true; reqBtn.textContent = 'Sending...'; }

    const res = await fetch(API + '?action=send_folder_pin', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ folder: folder })
    });
    const json = await res.json();

    if (json.success) {
      // SweetAlert: Folder OTP sent successfully
      Swal.fire({
        icon: 'success',
        title: 'OTP Sent!',
        text: json.mail_failed ? 'Security PIN generated. Check fallback code below.' : 'A security PIN has been sent to your registered email for folder access.',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false,
        confirmButtonColor: '#059669',
        customClass: { popup: 'rounded-2xl' }
      });

      document.getElementById('fpin-step-request').style.display = 'none';
      document.getElementById('fpin-step-verify').style.display = '';

      if (json.mail_failed && json.fallback_pin) {
        document.getElementById('fpin-fallback-display').style.display = '';
        document.getElementById('fpin-fallback-code').textContent = json.fallback_pin;
      } else {
        document.getElementById('fpin-fallback-display').style.display = 'none';
      }

      for (let i = 1; i <= 4; i++) document.getElementById('fpin-digit-' + i).value = '';
      document.getElementById('fpin-digit-1').focus();
      startFolderPinCountdown(json.expires_in || 120);
    }
  } catch (err) {
    console.error('Request folder PIN error:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not send security PIN.', confirmButtonColor: '#059669' });
  } finally {
    const reqBtn = document.querySelector('#fpin-step-request button');
    if (reqBtn) { reqBtn.disabled = false; reqBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Send Security PIN'; }
  }
}

async function verifyFolderPin() {
  const pin = [1,2,3,4].map(i => document.getElementById('fpin-digit-' + i).value).join('');
  if (pin.length !== 4) {
    Swal.fire({ icon: 'warning', title: 'Incomplete PIN', text: 'Please enter all 4 digits.', confirmButtonColor: '#059669' });
    return;
  }
  const folder = currentPinFolder || '';
  try {
    const res = await fetch(API + '?action=verify_folder_pin', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ pin, folder })
    });
    const json = await res.json();
    if (json.success) {
      unlockedFolders.add(folder);
      if (fpinCountdownInterval) clearInterval(fpinCountdownInterval);
      closeModal('modal-folder-pin');
      Swal.fire({ icon: 'success', title: 'Verified!', text: folder + ' folder access granted.', confirmButtonColor: '#059669', timer: 1500 });
      showFolderContents(folder);
    } else if (json.expired) {
      Swal.fire({ icon: 'warning', title: 'PIN Expired', text: 'Please request a new PIN.', confirmButtonColor: '#059669' });
      resetFolderPinUI();
    } else {
      Swal.fire({ icon: 'error', title: 'Invalid PIN', text: json.message || 'Please try again.', confirmButtonColor: '#059669' });
      for (let i = 1; i <= 4; i++) document.getElementById('fpin-digit-' + i).value = '';
      document.getElementById('fpin-digit-1').focus();
    }
  } catch (err) {
    console.error('Verify folder PIN error:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Verification failed.', confirmButtonColor: '#059669' });
  }
}

function resetFolderPinUI() {
  document.getElementById('fpin-step-request').style.display = '';
  document.getElementById('fpin-step-verify').style.display = 'none';
  if (fpinCountdownInterval) clearInterval(fpinCountdownInterval);
}

function startFolderPinCountdown(seconds) {
  let remaining = seconds;
  const el = document.getElementById('fpin-countdown');
  if (fpinCountdownInterval) clearInterval(fpinCountdownInterval);
  el.textContent = remaining;
  fpinCountdownInterval = setInterval(() => {
    remaining--;
    el.textContent = remaining;
    if (remaining <= 0) {
      clearInterval(fpinCountdownInterval);
      Swal.fire({ icon: 'warning', title: 'PIN Expired', text: 'Please request a new PIN.', confirmButtonColor: '#059669' });
      resetFolderPinUI();
    }
  }, 1000);
}

function fpinAutoFocus(el, nextNum) {
  el.value = el.value.replace(/[^0-9]/g, '');
  if (el.value && nextNum) document.getElementById('fpin-digit-' + nextNum).focus();
  if (!nextNum && el.value) {
    const pin = [1,2,3,4].map(i => document.getElementById('fpin-digit-' + i).value).join('');
    if (pin.length === 4) verifyFolderPin();
  }
}

function fpinKeyNav(event, currentNum) {
  if (event.key === 'Backspace' && !event.target.value && currentNum > 1) {
    document.getElementById('fpin-digit-' + (currentNum - 1)).focus();
  }
  if (event.key === 'Enter') verifyFolderPin();
}


async function loadArchiving() {
  try {
    const [statsRes, timelineRes] = await Promise.all([
      fetch(API + '?action=archiving_stats'),
      fetch(API + '?action=list_archive_timeline')
    ]);
    const statsJson = await statsRes.json();
    const timelineJson = await timelineRes.json();
    const s = statsJson.data || {};
    const timeline = timelineJson.data || [];

    document.getElementById('ar-active').textContent = s.total_active || 0;
    document.getElementById('ar-archived').textContent = s.total_archived || 0;
    document.getElementById('ar-retained').textContent = s.total_retained || 0;
    document.getElementById('ar-pending').textContent = s.pending_archive || 0;

    if (s.oldest_document) {
      document.getElementById('ar-last-run').textContent = 'Oldest document: ' + fmtDate(s.oldest_document);
    }

    renderArchiveTimeline(timeline);
  } catch (err) { console.error('Archiving load error:', err); }
}

function renderArchiveTimeline(timeline) {
  window._lastArchiveTimeline = timeline;
  const tbody = document.getElementById('ar-timeline-tbody');
  if (!timeline || timeline.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No timeline data available</td></tr>';
    Paginator.setTotalItems('doc-ar-timeline', 0);
    Paginator.renderControls('doc-ar-timeline', 'ar-timeline-pagination');
    return;
  }

  var allRows = timeline.map(row => {
    const total = parseInt(row.total) || 1;
    const active = parseInt(row.active) || 0;
    const archived = parseInt(row.archived) || 0;
    const retained = parseInt(row.retained) || 0;
    const aPct = Math.round(active / total * 100);
    const arPct = Math.round(archived / total * 100);
    const rPct = Math.round(retained / total * 100);

    return `<tr>
      <td style="font-weight:600;font-size:13px">${row.month}</td>
      <td style="font-weight:700">${total}</td>
      <td><span class="badge badge-green" style="font-size:11px">🟢 ${active}</span></td>
      <td><span class="badge badge-amber" style="font-size:11px">📦 ${archived}</span></td>
      <td><span class="badge badge-purple" style="font-size:11px">🔒 ${retained}</span></td>
      <td>
        <div style="display:flex;height:8px;border-radius:4px;overflow:hidden;background:#F3F4F6;min-width:120px">
          ${aPct > 0 ? `<div style="width:${aPct}%;background:#059669" title="Active ${aPct}%"></div>` : ''}
          ${arPct > 0 ? `<div style="width:${arPct}%;background:#D97706" title="Archived ${arPct}%"></div>` : ''}
          ${rPct > 0 ? `<div style="width:${rPct}%;background:#7C3AED" title="Retained ${rPct}%"></div>` : ''}
        </div>
      </td>
    </tr>`;
  });
  var paged = Paginator.paginate('doc-ar-timeline', allRows);
  tbody.innerHTML = paged.join('');
  Paginator.renderControls('doc-ar-timeline', 'ar-timeline-pagination');
}

async function runArchiveCycle() {
  if (!archivePinVerified) {
    Swal.fire({ icon: 'warning', title: 'PIN Required', text: 'Security PIN verification is required to run the archive cycle.', confirmButtonColor: '#059669' });
    return;
  }

  const result = await Swal.fire({
    title: 'Run Archive Cycle?',
    html: '<div style="font-size:14px;color:#4B5563">This will:<br>• Archive active documents older than 6 months<br>• Retain archived documents older than 3 years</div>',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#059669',
    confirmButtonText: 'Run Cycle',
    cancelButtonText: 'Cancel'
  });
  if (!result.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=run_archive_cycle', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({})
    });
    const json = await res.json();
    if (json.success) {
      Swal.fire({
        icon: 'success',
        title: 'Archive Cycle Complete',
        html: `<div style="font-size:14px"><strong>${json.archived || 0}</strong> documents archived<br><strong>${json.retained || 0}</strong> documents retained</div>`,
        confirmButtonColor: '#059669'
      });
      loadArchiving();
      loadData(true); // Force refresh main data after archive cycle
    }
  } catch (err) { console.error('Archive cycle error:', err); }
}

// ═══════════════════════════════════════════════════════
// CONFIDENTIAL DOCUMENT PIN VERIFICATION
// ═══════════════════════════════════════════════════════

async function requestConfidentialPin() {
  try {
    const reqBtn = document.querySelector('#conf-pin-step-request button');
    if (reqBtn) { reqBtn.disabled = true; reqBtn.textContent = 'Sending...'; }

    const res = await fetch(API + '?action=send_confidential_pin', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({})
    });
    const json = await res.json();

    if (json.success) {
      // SweetAlert: Confidential OTP sent successfully
      Swal.fire({
        icon: 'success',
        title: 'OTP Sent!',
        text: json.mail_failed ? 'Security PIN generated. Check fallback code below.' : 'A security PIN has been sent to your registered email for confidential access.',
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: false,
        confirmButtonColor: '#059669',
        customClass: { popup: 'rounded-2xl' }
      });

      document.getElementById('conf-pin-step-request').style.display = 'none';
      document.getElementById('conf-pin-step-verify').style.display = '';

      if (json.mail_failed && json.fallback_pin) {
        document.getElementById('conf-pin-fallback-display').style.display = '';
        document.getElementById('conf-pin-fallback-code').textContent = json.fallback_pin;
      } else {
        document.getElementById('conf-pin-fallback-display').style.display = 'none';
      }

      for (let i = 1; i <= 4; i++) document.getElementById('conf-pin-digit-' + i).value = '';
      document.getElementById('conf-pin-digit-1').focus();
      startConfPinCountdown(json.expires_in || 120);
    }
  } catch (err) {
    console.error('Request confidential PIN error:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Could not send security PIN.', confirmButtonColor: '#059669' });
  } finally {
    const reqBtn = document.querySelector('#conf-pin-step-request button');
    if (reqBtn) {
      reqBtn.disabled = false;
      reqBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:6px"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Send Security PIN';
    }
  }
}

async function verifyConfidentialPin() {
  const pin = [1,2,3,4].map(i => document.getElementById('conf-pin-digit-' + i).value).join('');
  if (pin.length !== 4) {
    Swal.fire({ icon: 'warning', title: 'Incomplete PIN', text: 'Please enter all 4 digits.', confirmButtonColor: '#059669' });
    return;
  }

  try {
    const res = await fetch(API + '?action=verify_confidential_pin', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ pin })
    });
    const json = await res.json();

    if (json.success) {
      confidentialPinVerified = true;
      if (confPinCountdownInterval) clearInterval(confPinCountdownInterval);
      closeModal('modal-confidential-pin');
      Swal.fire({ icon: 'success', title: 'Verified!', text: 'Confidential access granted.', confirmButtonColor: '#059669', timer: 1500 });
      // Now show the pending document
      if (pendingConfDocId) {
        showDocumentDetail(pendingConfDocId);
        pendingConfDocId = null;
      }
    } else if (json.expired) {
      Swal.fire({ icon: 'warning', title: 'PIN Expired', text: 'Please request a new PIN.', confirmButtonColor: '#059669' });
      resetConfPinUI();
    } else {
      Swal.fire({ icon: 'error', title: 'Invalid PIN', text: json.message || 'Please try again.', confirmButtonColor: '#059669' });
      for (let i = 1; i <= 4; i++) document.getElementById('conf-pin-digit-' + i).value = '';
      document.getElementById('conf-pin-digit-1').focus();
    }
  } catch (err) {
    console.error('Verify confidential PIN error:', err);
    Swal.fire({ icon: 'error', title: 'Error', text: 'Verification failed.', confirmButtonColor: '#059669' });
  }
}

function resetConfPinUI() {
  document.getElementById('conf-pin-step-request').style.display = '';
  document.getElementById('conf-pin-step-verify').style.display = 'none';
  if (confPinCountdownInterval) clearInterval(confPinCountdownInterval);
}

function startConfPinCountdown(seconds) {
  let remaining = seconds;
  const el = document.getElementById('conf-pin-countdown');
  if (confPinCountdownInterval) clearInterval(confPinCountdownInterval);
  el.textContent = remaining;
  confPinCountdownInterval = setInterval(() => {
    remaining--;
    el.textContent = remaining;
    if (remaining <= 0) {
      clearInterval(confPinCountdownInterval);
      Swal.fire({ icon: 'warning', title: 'PIN Expired', text: 'Please request a new PIN.', confirmButtonColor: '#059669' });
      resetConfPinUI();
    }
  }, 1000);
}

function confPinAutoFocus(el, nextNum) {
  el.value = el.value.replace(/[^0-9]/g, '');
  if (el.value && nextNum) {
    document.getElementById('conf-pin-digit-' + nextNum).focus();
  }
  if (!nextNum && el.value) {
    const pin = [1,2,3,4].map(i => document.getElementById('conf-pin-digit-' + i).value).join('');
    if (pin.length === 4) verifyConfidentialPin();
  }
}

function confPinKeyNav(event, currentNum) {
  if (event.key === 'Backspace' && !event.target.value && currentNum > 1) {
    document.getElementById('conf-pin-digit-' + (currentNum - 1)).focus();
  }
  if (event.key === 'Enter') {
    verifyConfidentialPin();
  }
}

// ═══════════════════════════════════════════════════════
// FOLDER SECURITY PIN — Now handled via SweetAlert2 in openFolderPanel()
// Legacy functions kept as stubs for backwards compatibility
// ═══════════════════════════════════════════════════════

// Per-folder PIN functions are defined in the DEPARTMENT FOLDERS section above

// ═══════════════════════════════════════════════════════
// SUBMODULE: ACCESS CONTROL
// ═══════════════════════════════════════════════════════

let accessGrants = [];
let allUsers = [];

async function loadAccessControl() {
  try {
    const [statsRes, grantsRes, usersRes] = await Promise.all([
      fetch(API + '?action=access_control_stats'),
      fetch(API + '?action=list_access_grants'),
      fetch(API + '?action=list_users')
    ]);
    const statsJson = await statsRes.json();
    const grantsJson = await grantsRes.json();
    const usersJson = await usersRes.json();
    const s = statsJson.data || {};
    accessGrants = grantsJson.data || [];
    allUsers = usersJson.data || [];

    document.getElementById('ac-total').textContent = s.total_grants || 0;
    document.getElementById('ac-active').textContent = s.active_grants || 0;
    document.getElementById('ac-expired').textContent = s.expired_grants || 0;
    document.getElementById('ac-users').textContent = s.users_with_access || 0;
    document.getElementById('ac-view-count').textContent = s.view_only || 0;
    document.getElementById('ac-download-count').textContent = s.download || 0;
    document.getElementById('ac-edit-count').textContent = s.edit || 0;
    document.getElementById('ac-admin-count').textContent = s.admin || 0;

    renderAccessTable();
  } catch (err) { console.error('Access control load error:', err); }
}

function renderAccessTable() {
  const permFilter = document.getElementById('ac-filter-perm').value;
  const search = document.getElementById('ac-search').value.toLowerCase();

  const filtered = accessGrants.filter(g => {
    if (permFilter && g.permission !== permFilter) return false;
    if (search) {
      const n = (g.user_name || '').toLowerCase();
      const d = (g.document_title || '').toLowerCase();
      const c = (g.document_code || '').toLowerCase();
      if (!n.includes(search) && !d.includes(search) && !c.includes(search)) return false;
    }
    return true;
  });

  document.getElementById('ac-table-count').textContent = filtered.length + ' grant' + (filtered.length !== 1 ? 's' : '');

  const tbody = document.getElementById('ac-tbody');
  if (filtered.length === 0) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">No access grants found</td></tr>';
    Paginator.setTotalItems('doc-ac', 0);
    Paginator.renderControls('doc-ac', 'ac-pagination');
    return;
  }

  const permIcons = { view: '👁️', download: '⬇️', edit: '✏️', admin: '⚙️' };
  const permBadges = { view: 'badge-green', download: 'badge-blue', edit: 'badge-amber', admin: 'badge-red' };

  var allRows = filtered.map(g => {
    const perm = g.permission || 'view';
    const icon = permIcons[perm] || '👁️';
    const badgeCls = permBadges[perm] || 'badge-green';
    const isExpired = g.expires_at && new Date(g.expires_at) < new Date();

    return `<tr style="${isExpired ? 'opacity:0.6' : ''}">
      <td>
        <div style="font-weight:600;font-size:13px">${g.document_title || ''}</div>
        <div style="font-size:11px;color:#9CA3AF">${g.document_code || ''}</div>
      </td>
      <td style="font-weight:600">${g.user_name || ''}</td>
      <td><span class="badge badge-blue" style="font-size:11px">${g.user_department || '—'}</span></td>
      <td><span class="badge ${badgeCls}" style="font-size:11px">${icon} ${perm.charAt(0).toUpperCase() + perm.slice(1)}</span></td>
      <td style="font-size:12px">${g.granted_by_name || '—'}</td>
      <td style="font-size:12px">${g.expires_at ? fmtDate(g.expires_at) : '<span style="color:#059669">Never</span>'}</td>
      <td>
        ${isExpired
          ? '<span class="badge badge-red" style="font-size:10px">Expired</span>'
          : '<span class="badge badge-green" style="font-size:10px">Active</span>'}
      </td>
      <td>
        <button class="btn btn-outline btn-sm" style="color:#DC2626;border-color:#FCA5A5" onclick="revokeAccess(${g.access_id}, '${(g.user_name || '').replace(/'/g, "\\'")}', '${(g.document_title || '').replace(/'/g, "\\'")}')" title="Revoke">✕</button>
      </td>
    </tr>`;
  });
  var paged = Paginator.paginate('doc-ac', allRows);
  tbody.innerHTML = paged.join('');
  Paginator.renderControls('doc-ac', 'ac-pagination');
}

function openGrantAccessModal() {
  // Populate department dropdown
  const deptSelect = document.getElementById('ga-department');
  deptSelect.innerHTML = '<option value="">All Departments</option>';
  Object.keys(deptConfig).forEach(dept => {
    const info = deptConfig[dept];
    const opt = document.createElement('option');
    opt.value = dept;
    opt.textContent = `${info.icon} ${dept} — ${info.folder}`;
    deptSelect.appendChild(opt);
  });

  // Populate all documents
  populateGrantDocuments('');
  // Populate all users
  populateGrantUsers('');

  // Reset
  document.getElementById('ga-permission').value = 'view';
  document.getElementById('ga-expires').value = '';
  document.getElementById('ga-dept-head-info').style.display = 'none';
  document.getElementById('ga-modules-info').style.display = 'none';
  openModal('modal-grant-access');
}

function filterGrantByDept() {
  const dept = document.getElementById('ga-department').value;
  populateGrantDocuments(dept);
  populateGrantUsers(dept);

  // Show department head info
  const headInfo = document.getElementById('ga-dept-head-info');
  const modulesInfo = document.getElementById('ga-modules-info');

  if (dept && deptConfig[dept]) {
    const info = deptConfig[dept];
    const head = allUsers.find(u => u.role === 'head_department' && u.department === dept);
    if (head) {
      document.getElementById('ga-dept-head-name').textContent = `${head.first_name} ${head.last_name}`;
      document.getElementById('ga-dept-head-role').textContent = `Head of ${dept} — ${info.folder}`;
      headInfo.style.display = '';
    } else {
      headInfo.style.display = 'none';
    }

    // Show modules
    if (info.modules && info.modules.length) {
      const modulesList = document.getElementById('ga-modules-list');
      modulesList.innerHTML = info.modules.map(m =>
        `<span style="padding:3px 10px;background:${info.bg};color:${info.color};border-radius:6px;font-size:11px;font-weight:600">${m}</span>`
      ).join('');
      modulesInfo.style.display = '';
    } else {
      modulesInfo.style.display = 'none';
    }
  } else {
    headInfo.style.display = 'none';
    modulesInfo.style.display = 'none';
  }
}

function populateGrantDocuments(dept) {
  const docSelect = document.getElementById('ga-document');
  docSelect.innerHTML = '<option value="">Select document...</option>';
  const docs = dept ? allDocuments.filter(d => getDocDept(d) === dept) : allDocuments;
  docs.forEach(doc => {
    const opt = document.createElement('option');
    opt.value = getDocId(doc);
    opt.textContent = `${getDocCode(doc)} — ${doc.title || 'Untitled'}`;
    docSelect.appendChild(opt);
  });
}

function populateGrantUsers(dept) {
  const userSelect = document.getElementById('ga-user');
  userSelect.innerHTML = '<option value="">Select user...</option>';
  const users = dept ? allUsers.filter(u => u.department === dept || u.role === 'super_admin' || u.role === 'admin') : allUsers;

  // Group: head_department first, then others
  const heads = users.filter(u => u.role === 'head_department');
  const others = users.filter(u => u.role !== 'head_department');

  if (heads.length) {
    const grp = document.createElement('optgroup');
    grp.label = '👑 Department Heads';
    heads.forEach(u => {
      const opt = document.createElement('option');
      opt.value = u.user_id;
      opt.textContent = `${u.first_name} ${u.last_name} — Head of ${u.department}`;
      grp.appendChild(opt);
    });
    userSelect.appendChild(grp);
  }
  if (others.length) {
    const grp = document.createElement('optgroup');
    grp.label = '👤 Staff & Managers';
    others.forEach(u => {
      const opt = document.createElement('option');
      opt.value = u.user_id;
      opt.textContent = `${u.first_name} ${u.last_name} (${u.role} — ${u.department || 'N/A'})`;
      grp.appendChild(opt);
    });
    userSelect.appendChild(grp);
  }
}

async function submitGrantAccess() {
  const docId = document.getElementById('ga-document').value;
  const userId = document.getElementById('ga-user').value;
  const permission = document.getElementById('ga-permission').value;
  const expires = document.getElementById('ga-expires').value;

  if (!docId || !userId) {
    Swal.fire({ icon: 'warning', title: 'Missing Fields', text: 'Please select both a document and a user.', confirmButtonColor: '#059669' });
    return;
  }

  try {
    const res = await fetch(API + '?action=grant_access', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        document_id: parseInt(docId),
        user_id: parseInt(userId),
        permission: permission,
        expires_at: expires || null
      })
    });
    const json = await res.json();
    if (json.success) {
      closeModal('modal-grant-access');
      Swal.fire({ icon: 'success', title: 'Access Granted', text: 'Permission has been assigned.', confirmButtonColor: '#059669', timer: 2000 });
      loadAccessControl();
    }
  } catch (err) { console.error('Grant access error:', err); }
}

async function revokeAccess(accessId, userName, docTitle) {
  const result = await Swal.fire({
    title: 'Revoke Access?',
    html: `<div style="font-size:14px">Remove <strong>${userName}</strong>'s access to <strong>${docTitle}</strong>?</div>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#DC2626',
    confirmButtonText: 'Revoke',
    cancelButtonText: 'Cancel'
  });
  if (!result.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=revoke_access', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ access_id: accessId })
    });
    const json = await res.json();
    if (json.success) {
      Swal.fire({ icon: 'success', title: 'Revoked', text: 'Access has been removed.', confirmButtonColor: '#059669', timer: 2000 });
      loadAccessControl();
    }
  } catch (err) { console.error('Revoke access error:', err); }
}
// ═══════════════════════════════════════════════════════
// CROSS-REFERENCE: HR Employee Directory in Access Control
// ═══════════════════════════════════════════════════════



// ═══════════════════════════════════════════════════════
// FULL-SCREEN DOCUMENT VIEWER + RBAC + AUDIT LOGGING
// ═══════════════════════════════════════════════════════

let fullViewerDocId = null;
let fullViewerDoc = null;
let fullViewerAccess = null;

const VIEWER_PRIVILEGED_ROLES = ['super_admin', 'admin', 'manager', 'head_department', 'System Administrator', 'Admin', 'Manager'];

function isViewerPrivileged() {
  const role = (window.__mf_user && window.__mf_user.role) || '';
  return VIEWER_PRIVILEGED_ROLES.some(r => r.toLowerCase() === role.toLowerCase());
}

// ───── Open the full-screen viewer ─────
async function openFullViewer(id) {
  const doc = allDocuments.find(d => getDocId(d) == id);
  if (!doc) return;

  fullViewerDocId = id;
  fullViewerDoc = doc;

  // Show modal immediately with loading state
  document.getElementById('modal-fullscreen-viewer').classList.add('open');
  document.getElementById('fv-title').textContent = doc.title || 'Untitled Document';
  document.getElementById('fv-icon').textContent = getFileIcon(doc);

  const code = getDocCode(doc);
  const fileType = getDocFileType(doc);
  const fileSize = getDocFileSize(doc);
  const dept = getDocDept(doc);
  document.getElementById('fv-meta').textContent = [code, dept, fileType, fileSize].filter(Boolean).join(' · ');

  // Populate metadata panel
  renderViewerMetadata(doc);

  // Reset preview area
  const previewContent = document.getElementById('fv-preview-content');
  previewContent.innerHTML = '<div style="text-align:center;color:#9CA3AF"><div style="font-size:48px;margin-bottom:12px">⏳</div><div>Checking access…</div></div>';
  document.getElementById('fv-access-denied').style.display = 'none';
  document.getElementById('fv-download-btn').style.display = 'none';
  document.getElementById('fv-request-btn').style.display = 'none';

  // Check RBAC access
  {
    // Local documents: check with server
    try {
      const res = await fetch(API + '?action=check_viewer_access&document_id=' + id).then(r => r.json());
      fullViewerAccess = res;
      updateViewerAccessUI();

      if (res.can_view) {
        renderViewerPreview(doc);
        logDocumentAction(doc, 'view', res.access_method);
      } else {
        showViewerAccessDenied(doc);
      }
    } catch (err) {
      console.error('Viewer access check failed:', err);
      previewContent.innerHTML = '<div style="text-align:center;color:#EF4444"><div style="font-size:48px;margin-bottom:12px">⚠️</div><div>Could not verify document access</div></div>';
    }
  }
}

function closeFullViewer() {
  document.getElementById('modal-fullscreen-viewer').classList.remove('open');
  fullViewerDocId = null;
  fullViewerDoc = null;
  fullViewerAccess = null;
}

// ───── Render Metadata Panel ─────
function renderViewerMetadata(doc) {
  const dateStr = getDocDate(doc);
  const lc      = getLifecycle(dateStr);
  const dept    = getDocDept(doc);
  const code    = getDocCode(doc);
  const folder  = getDocFolder(doc);
  const type    = getDocType(doc);
  const conf    = doc.confidentiality || '';
  const desc    = doc.description || '';
  const fileType = getDocFileType(doc);
  const fileSize = doc.file_size ? formatBytes(doc.file_size) : getDocFileSize(doc);
  const fileName = doc.file_name || '';
  const uploader = doc.designated_employee || doc.uploaded_by_name || '—';

  const confColors = {
    'public':       { bg: '#D1FAE5', color: '#059669', label: '🟢 Public' },
    'internal':     { bg: '#DBEAFE', color: '#1D4ED8', label: '🔵 Internal' },
    'confidential': { bg: '#FEF3C7', color: '#D97706', label: '🟡 Confidential' },
    'restricted':   { bg: '#FEE2E2', color: '#DC2626', label: '🔴 Restricted' },
  };
  const cc = confColors[conf] || confColors['internal'];

  const statusColors = {
    'active':   { bg: '#D1FAE5', color: '#059669', label: 'Active' },
    'draft':    { bg: '#E5E7EB', color: '#6B7280', label: 'Draft' },
    'archived': { bg: '#FEF3C7', color: '#D97706', label: 'Archived' },
    'retained': { bg: '#EDE9FE', color: '#7C3AED', label: 'Retained' },
    'approved': { bg: '#D1FAE5', color: '#059669', label: 'Approved' },
    'restricted':{ bg: '#FEE2E2', color: '#DC2626', label: 'Restricted' },
  };
  const docStatus = doc.status || lc.status;
  const sc = statusColors[docStatus] || statusColors['active'];

  // Update header badges
  document.getElementById('fv-access-badge').innerHTML = cc.label;
  document.getElementById('fv-access-badge').style.cssText = `padding:4px 12px;border-radius:8px;font-size:11px;font-weight:700;background:${cc.bg};color:${cc.color}`;
  document.getElementById('fv-status-badge').innerHTML = sc.label;
  document.getElementById('fv-status-badge').style.cssText = `padding:4px 12px;border-radius:8px;font-size:11px;font-weight:700;background:${sc.bg};color:${sc.color}`;

  const metaHTML = `
    <!-- Lifecycle Banner -->
    <div style="background:${lc.bg};padding:10px 12px;border-radius:10px;margin-bottom:16px;font-size:12px;font-weight:700;color:${lc.color};display:flex;justify-content:space-between;align-items:center">
      <span>${lc.label}</span>
      <span style="font-size:11px;font-weight:500">${getAge(dateStr)} ago</span>
    </div>

    <!-- Metadata Rows -->
    <div style="font-size:13px">
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">📋 Title</span>
        <span style="color:#1F2937;font-weight:600;text-align:right;max-width:180px;overflow:hidden;text-overflow:ellipsis" title="${(doc.title||'').replace(/"/g,'&quot;')}">${doc.title || '—'}</span>
      </div>
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">🏢 Department</span>
        <span style="color:#1F2937">${dept || '—'}</span>
      </div>
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">📜 File Type</span>
        <span style="color:#1F2937">${fileType || '—'}</span>
      </div>
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">💾 File Size</span>
        <span style="color:#1F2937">${fileSize || '—'}</span>
      </div>
      ${fileName ? `<div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">📎 File Name</span>
        <span style="color:#1F2937;font-size:11px;max-width:170px;overflow:hidden;text-overflow:ellipsis" title="${fileName.replace(/"/g,'&quot;')}">${fileName}</span>
      </div>` : ''}
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">📅 Upload Date</span>
        <span style="color:#1F2937">${fmtDate(dateStr)}</span>
      </div>
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">👤 Uploaded By</span>
        <span style="color:#1F2937">${uploader}</span>
      </div>
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">📂 Folder</span>
        <span style="color:#1F2937">${folder || '—'}</span>
      </div>
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">📑 Type</span>
        <span style="color:#1F2937">${type || '—'}</span>
      </div>
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">🔐 Confidentiality</span>
        <span style="background:${cc.bg};color:${cc.color};padding:2px 10px;border-radius:8px;font-size:11px;font-weight:700">${cc.label}</span>
      </div>
      <div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">📊 Status</span>
        <span style="background:${sc.bg};color:${sc.color};padding:2px 10px;border-radius:8px;font-size:11px;font-weight:700">${sc.label}</span>
      </div>
      ${code ? `<div style="padding:10px 0;border-bottom:1px solid #E5E7EB;display:flex;justify-content:space-between">
        <span style="font-weight:600;color:#6B7280">🔖 Code</span>
        <span style="color:#1F2937;font-family:monospace;font-size:12px">${code}</span>
      </div>` : ''}
      ${desc ? `<div style="padding:12px 0">
        <div style="font-weight:600;color:#6B7280;margin-bottom:6px">📝 Description</div>
        <div style="color:#374151;line-height:1.6;font-size:12px">${desc}</div>
      </div>` : ''}
    </div>

    <!-- Lifecycle Timeline -->
    <div style="margin-top:16px;padding:14px;background:#F3F4F6;border-radius:10px">
      <div style="font-weight:700;font-size:12px;color:#1F2937;margin-bottom:10px">Lifecycle Timeline</div>
      <div style="display:flex;align-items:center;gap:0;font-size:10px">
        <div style="text-align:center;flex:1">
          <div style="height:5px;background:${lc.status==='active'||lc.status==='archived'||lc.status==='retained'?'#059669':'#E5E7EB'};border-radius:3px 0 0 3px"></div>
          <div style="margin-top:4px;font-weight:600;color:${lc.status==='active'?'#059669':'#9CA3AF'}">Active</div>
        </div>
        <div style="text-align:center;flex:1">
          <div style="height:5px;background:${lc.status==='archived'||lc.status==='retained'?'#D97706':'#E5E7EB'}"></div>
          <div style="margin-top:4px;font-weight:600;color:${lc.status==='archived'?'#D97706':'#9CA3AF'}">Archive</div>
        </div>
        <div style="text-align:center;flex:1">
          <div style="height:5px;background:${lc.status==='retained'?'#7C3AED':'#E5E7EB'};border-radius:0 3px 3px 0"></div>
          <div style="margin-top:4px;font-weight:600;color:${lc.status==='retained'?'#7C3AED':'#9CA3AF'}">Retain</div>
        </div>
      </div>
    </div>

  `;
  document.getElementById('fv-metadata-content').innerHTML = metaHTML;
}

// ───── Render File Preview ─────
function renderViewerPreview(doc) {
  const previewContent = document.getElementById('fv-preview-content');
  const ft = (doc.file_type || doc.fileType || doc.file_name || '').toLowerCase();
  const docId = getDocId(doc);

  // ── Local documents ──
  const isPDF   = ft.includes('pdf');
  const isImage = ft.includes('png') || ft.includes('jpg') || ft.includes('jpeg') || ft.includes('gif') || ft.includes('webp') || ft.includes('bmp') || ft.includes('svg') || ft.includes('image');
  if (isPDF) {
    previewContent.innerHTML = `
      <div style="position:relative;width:100%;height:100%">
        <iframe src="${API}?action=serve_file&document_id=${docId}" 
                style="width:100%;height:100%;border:none" 
                title="PDF Viewer"
                onerror="this.parentElement.innerHTML='<div style=\\'text-align:center;padding:40px;color:#9CA3AF\\'><div style=\\'font-size:64px;margin-bottom:12px\\'>📕</div><div>PDF file not available on disk</div><div style=\\'font-size:12px;margin-top:8px\\'>The file reference exists but the physical file was not found.</div></div>'">
        </iframe>
      </div>`;
  } else if (isImage) {
    previewContent.innerHTML = `
      <div style="position:relative;width:100%;height:100%">
        <div style="padding:20px;text-align:center;max-width:100%;max-height:100%;overflow:auto">
          <img src="${API}?action=serve_file&document_id=${docId}" 
               alt="${doc.title || 'Document'}" 
               style="max-width:100%;max-height:85vh;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,0.1)"
               onerror="this.parentElement.innerHTML='<div style=\\'font-size:64px;margin-bottom:12px\\'>🖼️</div><div style=\\'color:#9CA3AF\\'>Image file not available on disk</div>'">
        </div>
      </div>`;
  } else {
    previewContent.innerHTML = `
      <div style="text-align:center;max-width:450px;padding:40px">
        <div style="font-size:80px;margin-bottom:20px">${getFileIcon(doc)}</div>
        <h3 style="font-size:18px;font-weight:800;color:#1F2937;margin:0 0 8px">${doc.title || 'Document'}</h3>
        <p style="font-size:13px;color:#6B7280;margin:0 0 20px">This file type (<strong>${getDocFileType(doc) || 'unknown'}</strong>) cannot be previewed inline. Use the Download button to save it.</p>
        <div style="padding:12px 20px;background:#F3F4F6;border-radius:10px;font-size:12px;color:#6B7280">
          📄 ${doc.file_name || 'File'} · ${getDocFileSize(doc) || 'Unknown size'}
        </div>
      </div>`;
  }
}

function formatBytes(bytes) {
  if (!bytes) return '—';
  const k = 1024;
  const sizes = ['B', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// ───── Access Denied Overlay ─────
function showViewerAccessDenied(doc) {
  const previewContent = document.getElementById('fv-preview-content');
  previewContent.innerHTML = ''; // Clear
  const overlay = document.getElementById('fv-access-denied');
  overlay.style.display = 'flex';

  // Check if user has a pending request
  if (fullViewerAccess && !fullViewerAccess.can_view && !fullViewerAccess.is_privileged) {
    // Check for pending request
    fetch(API + '?action=list_access_requests&status=pending')
      .then(r => r.json())
      .then(json => {
        const userId = (window.__mf_user && window.__mf_user.user_id) || 0;
        const pending = (json.data || []).find(r => r.document_id == fullViewerDocId && r.requested_by == userId);
        if (pending) {
          document.getElementById('fv-pending-notice').style.display = 'block';
          document.getElementById('fv-request-access-btn').style.display = 'none';
        } else {
          document.getElementById('fv-pending-notice').style.display = 'none';
          document.getElementById('fv-request-access-btn').style.display = '';
        }
      }).catch(() => {});
  }
}

// ───── Update viewer access UI elements ─────
function updateViewerAccessUI() {
  if (!fullViewerAccess) return;
  const dlBtn = document.getElementById('fv-download-btn');
  const reqBtn = document.getElementById('fv-request-btn');

  if (fullViewerAccess.can_download) {
    dlBtn.style.display = '';
  } else {
    dlBtn.style.display = 'none';
  }

  if (fullViewerAccess.requires_request && !fullViewerAccess.can_view) {
    reqBtn.style.display = '';
  } else {
    reqBtn.style.display = 'none';
  }
}

// ───── Download from viewer ─────
function fullViewerDownload() {
  if (!fullViewerDoc || !fullViewerAccess) return;
  if (!fullViewerAccess.can_download) {
    Swal.fire({ icon: 'warning', title: 'Download Restricted', text: 'You do not have permission to download this document.', confirmButtonColor: '#059669' });
    return;
  }

  const doc = fullViewerDoc;
  const docId = getDocId(doc);

  // Trigger file download
  logDocumentAction(doc, 'download', fullViewerAccess.access_method);
  window.open(API + '?action=download_file&document_id=' + docId, '_blank');
}

// ───── Audit Logging ─────
function logDocumentAction(doc, action, accessMethod) {
  const payload = {
    document_id: getDocId(doc),
    document_code: getDocCode(doc) || null,
    document_title: doc.title || null,
    department: getDocDept(doc) || null,
    source_system: null,
    action: action,
    file_type: getDocFileType(doc) || null,
    file_size: doc.file_size || null,
    access_method: accessMethod || 'direct',
  };
  fetch(API + '?action=log_document_action', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  }).catch(err => console.error('Audit log failed:', err));
}

// ═══════════════════════════════════════════════════════
// ACCESS REQUESTS (from viewer)
// ═══════════════════════════════════════════════════════

function openAccessRequestFromViewer() {
  if (!fullViewerDoc) return;
  const doc = fullViewerDoc;
  document.getElementById('ar-modal-doc-id').value = getDocId(doc);
  document.getElementById('ar-modal-doc-title').textContent = doc.title || 'Untitled';
  document.getElementById('ar-modal-doc-code').textContent = getDocCode(doc) || '—';
  document.getElementById('ar-modal-permission').value = 'view';
  document.getElementById('ar-modal-reason').value = '';
  openModal('modal-access-request');
}

async function submitAccessRequestFromViewer() {
  const docId = document.getElementById('ar-modal-doc-id').value;
  const permission = document.getElementById('ar-modal-permission').value;
  const reason = document.getElementById('ar-modal-reason').value.trim();
  if (!reason) {
    Swal.fire({ icon: 'warning', title: 'Reason Required', text: 'Please provide a reason for your access request.', confirmButtonColor: '#F59E0B' });
    return;
  }

  try {
    const res = await fetch(API + '?action=submit_access_request', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ document_id: docId, permission, reason })
    }).then(r => r.json());

    if (res.success) {
      closeModal('modal-access-request');
      Swal.fire({ icon: 'success', title: 'Request Submitted', html: '<div style="font-size:14px">Your access request has been submitted. A Head Department, Admin, or Manager will review it shortly.</div>', confirmButtonColor: '#059669' });
      // Update the viewer overlay
      document.getElementById('fv-pending-notice').style.display = 'block';
      document.getElementById('fv-request-access-btn').style.display = 'none';
      document.getElementById('fv-request-btn').style.display = 'none';
    } else {
      Swal.fire({ icon: 'error', title: 'Request Failed', text: res.message || 'Could not submit request.', confirmButtonColor: '#DC2626' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error: ' + err.message, confirmButtonColor: '#DC2626' });
  }
}

// ═══════════════════════════════════════════════════════
// VIEW / DOWNLOAD AUDIT LOGS TAB
// ═══════════════════════════════════════════════════════

let viewLogsData = [];

async function loadViewLogStats() {
  try {
    const res = await fetch(API + '?action=view_log_stats').then(r => r.json());
    if (!res.success) return;
    const s = res.data;
    document.getElementById('vl-total-views').textContent = s.total_views ?? 0;
    document.getElementById('vl-total-downloads').textContent = s.total_downloads ?? 0;
    document.getElementById('vl-unique-users').textContent = s.unique_users ?? 0;
    document.getElementById('vl-unique-docs').textContent = s.unique_docs ?? 0;
    document.getElementById('vl-today').textContent = s.today_views ?? 0;
    document.getElementById('vl-most-viewed').textContent = s.most_viewed_doc ?? '—';
    // Update stat card in directory
    document.getElementById('stat-view-logs').textContent = (s.total_views ?? 0) + (s.total_downloads ?? 0);
  } catch (e) { console.error('loadViewLogStats:', e); }
}

async function loadViewLogs() {
  const tbody = document.getElementById('view-logs-tbody');
  tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr>';
  try {
    const params = new URLSearchParams({ action: 'list_view_logs' });
    const fAction = document.getElementById('vl-filter-action')?.value;
    const fFrom   = document.getElementById('vl-filter-from')?.value;
    const fTo     = document.getElementById('vl-filter-to')?.value;
    if (fAction) params.set('action_filter', fAction);
    if (fFrom) params.set('date_from', fFrom);
    if (fTo) params.set('date_to', fTo);

    const res = await fetch(API + '?' + params.toString()).then(r => r.json());
    viewLogsData = res.data || [];
    Paginator.reset('doc-view-logs');
    renderViewLogs();
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>';
  }
}

function renderViewLogs() {
  const tbody = document.getElementById('view-logs-tbody');
  if (!viewLogsData.length) {
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">No audit logs found</td></tr>';
    Paginator.setTotalItems('doc-view-logs', 0);
    Paginator.renderControls('doc-view-logs', 'view-logs-pagination');
    return;
  }
  const actionBadge = (a) => {
    const map = { view: ['👁️','View','#3B82F6'], download: ['⬇️','Download','#059669'], preview: ['🔍','Preview','#7C3AED'], print: ['🖨️','Print','#D97706'] };
    const [icon, label, color] = map[a] || ['📋', a, '#6B7280'];
    return `<span style="background:${color}15;color:${color};padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600">${icon} ${label}</span>`;
  };
  const methodBadge = (m) => {
    const map = { role_based: ['🛡️','Role','#059669'], direct: ['➡️','Direct','#3B82F6'], pin_verified: ['🔐','PIN','#D97706'], grant_based: ['🔑','Grant','#7C3AED'], public_access: ['🟢','Public','#10B981'], internal_access: ['🔵','Internal','#3B82F6'], request_approved: ['✅','Approved','#059669'], external_record: ['🔗','External','#6366F1'] };
    const [icon, label, color] = map[m] || ['📋', m, '#6B7280'];
    return `<span style="background:${color}15;color:${color};padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600">${icon} ${label}</span>`;
  };

  var allRows = viewLogsData.map(log => {
    const dt = log.created_at ? new Date(log.created_at) : null;
    const dateStr = dt ? dt.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '—';
    const timeStr = dt ? dt.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' }) : '';
    return `<tr>
      <td style="font-weight:600;color:#6366F1;font-size:12px">#${log.view_log_id}</td>
      <td>
        <div style="font-weight:600;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${(log.document_title||'').replace(/"/g,'&quot;')}">${log.document_title || '—'}</div>
        <div style="font-size:11px;color:#9CA3AF">${log.document_code || ''} ${log.source_system ? '🔗 '+log.source_system : ''}</div>
      </td>
      <td>${actionBadge(log.action)}</td>
      <td style="font-weight:500">${log.user_name || '—'}</td>
      <td style="font-size:12px">${log.user_role || '—'}</td>
      <td style="font-size:12px">${log.user_department || '—'}</td>
      <td>${methodBadge(log.access_method)}</td>
      <td><div style="font-size:12px;font-weight:500">${dateStr}</div><div style="font-size:11px;color:#9CA3AF">${timeStr}</div></td>
    </tr>`;
  });
  var paged = Paginator.paginate('doc-view-logs', allRows);
  tbody.innerHTML = paged.join('');
  Paginator.renderControls('doc-view-logs', 'view-logs-pagination');
}

function exportViewLogs(format) {
  if (!viewLogsData.length) { Swal.fire('No Data', 'No audit logs to export.', 'info'); return; }
  const headers = ['Log#','Document','Code','Source','Action','User','Role','Department','Access Method','Date'];
  const rows = viewLogsData.map(l => [
    l.view_log_id, `"${(l.document_title||'').replace(/"/g,'""')}"`, l.document_code||'', l.source_system||'',
    l.action, l.user_name||'', l.user_role||'', l.user_department||'', l.access_method||'', l.created_at||''
  ]);
  const csv = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
  const blob = new Blob([csv], { type:'text/csv' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'document_view_logs_' + new Date().toISOString().slice(0,10) + '.csv';
  a.click();
}

// ═══════════════════════════════════════════════════════
// ACCESS REQUESTS TAB (approval workflow)
// ═══════════════════════════════════════════════════════

let accessRequestsData = [];

async function loadAccessRequestStats() {
  try {
    const res = await fetch(API + '?action=access_request_stats').then(r => r.json());
    if (!res.success) return;
    const s = res.data;
    document.getElementById('ar-total').textContent = s.total_requests ?? 0;
    document.getElementById('ar-pending').textContent = s.pending ?? 0;
    document.getElementById('ar-approved').textContent = s.approved ?? 0;
    document.getElementById('ar-denied').textContent = s.denied ?? 0;
    document.getElementById('stat-access-requests').textContent = s.pending ?? 0;
  } catch (e) { console.error('loadAccessRequestStats:', e); }
}

async function loadAccessRequests() {
  const tbody = document.getElementById('access-requests-tbody');
  tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr>';
  try {
    const status = document.getElementById('ar-filter-status')?.value || '';
    const params = new URLSearchParams({ action: 'list_access_requests' });
    if (status) params.set('status', status);
    const res = await fetch(API + '?' + params.toString()).then(r => r.json());
    accessRequestsData = res.data || [];
    Paginator.reset('doc-access-requests');
    renderAccessRequests();
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>';
  }
}

function renderAccessRequests() {
  const tbody = document.getElementById('access-requests-tbody');
  if (!accessRequestsData.length) {
    tbody.innerHTML = '<tr><td colspan="10" style="text-align:center;padding:30px;color:#9CA3AF">No access requests found</td></tr>';
    Paginator.setTotalItems('doc-access-requests', 0);
    Paginator.renderControls('doc-access-requests', 'access-requests-pagination');
    return;
  }
  const privileged = isViewerPrivileged();

  const statusBadge = (s) => {
    const map = { pending: ['⏳','Pending','#F59E0B'], approved: ['✅','Approved','#059669'], denied: ['❌','Denied','#DC2626'], expired: ['⏰','Expired','#6B7280'] };
    const [icon, label, color] = map[s] || ['📋', s, '#6B7280'];
    return `<span style="background:${color}15;color:${color};padding:2px 10px;border-radius:12px;font-size:11px;font-weight:600">${icon} ${label}</span>`;
  };

  var allRows = accessRequestsData.map(req => {
    const dt = req.created_at ? new Date(req.created_at) : null;
    const dateStr = dt ? dt.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '—';
    const actions = (req.status === 'pending' && privileged)
      ? `<div style="display:flex;gap:6px">
           <button class="btn btn-sm" onclick="reviewAccessRequest(${req.request_id},'approved')" style="padding:4px 10px;font-size:11px;background:#059669;color:#fff;border:none;border-radius:6px;cursor:pointer" title="Approve">✅</button>
           <button class="btn btn-sm" onclick="reviewAccessRequest(${req.request_id},'denied')" style="padding:4px 10px;font-size:11px;background:#DC2626;color:#fff;border:none;border-radius:6px;cursor:pointer" title="Deny">❌</button>
         </div>`
      : '<span style="font-size:11px;color:#9CA3AF">—</span>';

    return `<tr>
      <td style="font-weight:600;color:#6366F1;font-size:12px">#${req.request_id}</td>
      <td>
        <div style="font-weight:600;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${(req.document_title||'').replace(/"/g,'&quot;')}">${req.document_title || '—'}</div>
        <div style="font-size:11px;color:#9CA3AF">${req.document_code || ''}</div>
      </td>
      <td style="font-weight:500">${req.requester_name || '—'}</td>
      <td><div style="font-size:12px">${req.requester_role || '—'}</div><div style="font-size:11px;color:#9CA3AF">${req.requester_dept || ''}</div></td>
      <td style="font-size:12px;font-weight:600">${(req.permission_requested || '—').charAt(0).toUpperCase() + (req.permission_requested||'').slice(1)}</td>
      <td style="font-size:12px;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${(req.reason||'').replace(/"/g,'&quot;')}">${req.reason || '—'}</td>
      <td>${statusBadge(req.status)}</td>
      <td style="font-size:12px">${req.reviewer_name || '—'}</td>
      <td style="font-size:12px">${dateStr}</td>
      <td>${actions}</td>
    </tr>`;
  });
  var paged = Paginator.paginate('doc-access-requests', allRows);
  tbody.innerHTML = paged.join('');
  Paginator.renderControls('doc-access-requests', 'access-requests-pagination');
}

async function reviewAccessRequest(requestId, decision) {
  const action = decision === 'approved' ? 'approve' : 'deny';
  const { value: notes } = await Swal.fire({
    title: `${decision === 'approved' ? '✅ Approve' : '❌ Deny'} Access Request #${requestId}`,
    input: 'textarea',
    inputLabel: 'Review Notes (optional)',
    inputPlaceholder: 'Add any notes about this decision…',
    showCancelButton: true,
    confirmButtonText: decision === 'approved' ? 'Approve' : 'Deny',
    confirmButtonColor: decision === 'approved' ? '#059669' : '#DC2626',
    inputAttributes: { style: 'min-height:60px' }
  });
  if (notes === undefined) return; // Cancelled

  try {
    const res = await fetch(API + '?action=review_access_request', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ request_id: requestId, status: decision, review_notes: notes || '' })
    }).then(r => r.json());

    if (res.success) {
      Swal.fire({ icon: 'success', title: decision === 'approved' ? 'Approved' : 'Denied', text: `Access request has been ${decision}.`, timer: 2000, showConfirmButton: false });
      loadAccessRequests();
      loadAccessRequestStats();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to review request.', confirmButtonColor: '#DC2626' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: err.message, confirmButtonColor: '#DC2626' });
  }
}

// ═══════════════════════════════════════════════════════
// WIRE UP: Replace viewDocument calls with full viewer
// Override showDocumentDetail to open full viewer
// ═══════════════════════════════════════════════════════

// Save original showDocumentDetail for backward compat
const _originalShowDocumentDetail = showDocumentDetail;

// Override: Open full-screen viewer instead of mini modal
showDocumentDetail = function(id) {
  openFullViewer(id);
};

// (removed duplicate loadData — already called above)

// Background stats for new tabs
setTimeout(() => {
  loadViewLogStats();
  loadAccessRequestStats();
}, 2000);

</script>
</body>
</html>