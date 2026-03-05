<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Department Management — Microfinancial Admin</title>

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
  <script src="../../hr4-integration.js"></script>
</head>

<body class="bg-brand-background-main min-h-screen font-[Inter,'Segoe_UI',system-ui,-apple-system,sans-serif]">

  <?php $activePage = 'departments'; $baseUrl = '../../'; include '../../sidebar.php'; ?>

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
        <h1 class="page-title">Department Management</h1>
        <p class="page-subtitle">View department directory, employee rosters, and employment contracts — sourced from HR4.</p>
      </div>

      <!-- HR4 Connection Status -->
      <div class="animate-in delay-1" style="display:flex;align-items:center;gap:10px;margin-bottom:16px;padding:10px 16px;background:linear-gradient(135deg,#ECFDF5,#D1FAE5);border:1px solid #A7F3D0;border-radius:12px">
        <div id="dept-hr4-dot" style="width:10px;height:10px;border-radius:50%;background:#D97706;animation:pulse 1.5s infinite"></div>
        <span id="dept-hr4-text" style="font-size:12px;color:#6B7280;font-weight:600">Checking HR4 connection…</span>
        <button class="btn btn-outline btn-sm" onclick="deptRefreshAll()" style="margin-left:auto;font-size:11px;padding:4px 10px">🔄 Refresh</button>
      </div>

      <!-- SUBMODULE DIRECTORY -->
      <div class="animate-in delay-1">
        <div class="module-directory-label">Submodule Directory</div>
        <div class="stats-grid" style="margin-bottom:18px">
          <a href="#tab-overview" onclick="deptShowSection('#tab-overview')" class="stat-card stat-card-link active-module">
            <div class="stat-icon green">🏢</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-dept-count">—</div>
              <div class="stat-label">Departments</div>
            </div>
            <div class="stat-arrow">●</div>
          </a>
          <a href="#tab-employees" onclick="deptShowSection('#tab-employees')" class="stat-card stat-card-link">
            <div class="stat-icon purple">👥</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-emp-count">—</div>
              <div class="stat-label">Total Employees</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-contracts" onclick="deptShowSection('#tab-contracts')" class="stat-card stat-card-link">
            <div class="stat-icon blue">📝</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-contract-count">—</div>
              <div class="stat-label">Contracts</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-positions" onclick="deptShowSection('#tab-positions')" class="stat-card stat-card-link">
            <div class="stat-icon amber">🪑</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-position-count">—</div>
              <div class="stat-label">Vacant Positions</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Department Overview (Grid)                      -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-overview" class="tab-content active animate-in delay-3">

        <!-- Search -->
        <div style="margin-bottom:20px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <div style="flex:1;min-width:260px;position:relative">
            <input type="text" id="dept-search" class="form-input" placeholder="🔍 Search departments…" oninput="deptFilterGrid(this.value)" style="padding-left:14px">
          </div>
          <span id="dept-summary" style="font-size:12px;color:#9CA3AF">Loading…</span>
        </div>

        <!-- Department Grid -->
        <div id="dept-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;margin-bottom:20px"></div>

        <!-- Department Detail Panel (shown when a dept card is clicked) -->
        <div id="dept-detail-panel" class="card" style="display:none;margin-top:4px">
          <div class="card-header" style="background:linear-gradient(135deg,#ECFDF5,#D1FAE5)">
            <div style="display:flex;align-items:center;gap:10px">
              <button class="btn btn-outline btn-sm" onclick="deptCloseDetail()">← Back</button>
              <span class="card-title" id="dept-detail-title" style="color:#065F46">🏢 Department</span>
            </div>
            <span id="dept-detail-summary" style="font-size:12px;color:#6B7280">Loading…</span>
          </div>
          <div class="card-body" id="dept-detail-body" style="padding:0">
            <!-- Sub-tabs inside detail -->
            <div style="display:flex;gap:6px;padding:12px 16px;border-bottom:2px solid #E5E7EB;flex-wrap:wrap">
              <button class="sub-tab active" onclick="deptDetailTab('detail-employees', this)">👥 Employees</button>
              <button class="sub-tab" onclick="deptDetailTab('detail-contracts', this)">📝 Contracts</button>
            </div>
            <!-- Employees sub-panel -->
            <div id="detail-employees" class="dept-detail-sub" style="padding:16px;overflow-x:auto">
              <table class="data-table"><thead><tr><th>Employee ID</th><th>Name</th><th>Email</th><th>Job Title</th><th>Type</th><th>Status</th><th>Hired</th><th>Base Salary</th></tr></thead><tbody id="detail-emp-tbody"><tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">Select a department</td></tr></tbody></table>
            </div>
            <!-- Contracts sub-panel -->
            <div id="detail-contracts" class="dept-detail-sub" style="display:none;padding:16px;overflow-x:auto">
              <table class="data-table"><thead><tr><th>Employee ID</th><th>Name</th><th>Contract No</th><th>Duration</th><th>Start</th><th>End</th><th>Expiry</th></tr></thead><tbody id="detail-contract-tbody"><tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">Select a department</td></tr></tbody></table>
            </div>
          </div>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: All Employees by Department                     -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-employees" class="tab-content">
        <div class="card">
          <div class="card-header" style="background:linear-gradient(135deg,#F5F3FF,#EDE9FE)">
            <span class="card-title" style="color:#5B21B6">👥 Employee Directory by Department</span>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <select id="emp-dept-filter" class="form-input" style="font-size:12px;padding:5px 10px;width:auto" onchange="deptRenderAllEmployees()">
                <option value="">All Departments</option>
              </select>
              <input type="text" id="emp-search" placeholder="Search name, email…" style="font-size:12px;padding:5px 12px;border:1px solid #D1D5DB;border-radius:8px;width:200px" oninput="deptRenderAllEmployees()">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="deptExportEmployees('pdf')" title="Export PDF">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="deptExportEmployees('csv')" title="Export CSV">📊 CSV</button>
            </div>
          </div>
          <div class="card-body" style="overflow-x:auto">
            <table class="data-table"><thead><tr><th>Employee ID</th><th>Name</th><th>Email</th><th>Department</th><th>Job Title</th><th>Type</th><th>Status</th><th>Hired</th></tr></thead><tbody id="all-emp-tbody"><tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr></tbody></table>
          </div>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: All Contracts by Department                     -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-contracts" class="tab-content">
        <div class="card">
          <div class="card-header" style="background:linear-gradient(135deg,#EFF6FF,#DBEAFE)">
            <span class="card-title" style="color:#1E40AF">📝 Employment Contracts</span>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <select id="contract-dept-filter" class="form-input" style="font-size:12px;padding:5px 10px;width:auto" onchange="deptRenderAllContracts()">
                <option value="">All Departments</option>
              </select>
              <select id="contract-expiry-filter" class="form-input" style="font-size:12px;padding:5px 10px;width:auto" onchange="deptRenderAllContracts()">
                <option value="0">All Contracts</option>
                <option value="30">Expiring in 30 days</option>
                <option value="60">Expiring in 60 days</option>
                <option value="90">Expiring in 90 days</option>
              </select>
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="deptExportContracts('pdf')" title="Export PDF">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="deptExportContracts('csv')" title="Export CSV">📊 CSV</button>
            </div>
          </div>
          <div class="card-body" style="overflow-x:auto">
            <table class="data-table"><thead><tr><th>Employee</th><th>Department</th><th>Contract No</th><th>Duration</th><th>Start</th><th>End</th><th>Expiry</th></tr></thead><tbody id="all-contract-tbody"><tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr></tbody></table>
          </div>
        </div>
      </div>


      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Vacant Positions by Department                  -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-positions" class="tab-content">
        <div class="card">
          <div class="card-header" style="background:linear-gradient(135deg,#FFFBEB,#FEF3C7)">
            <span class="card-title" style="color:#92400E">🪑 Vacant Positions</span>
            <div style="display:flex;gap:8px;align-items:center">
              <select id="pos-dept-filter" class="form-input" style="font-size:12px;padding:5px 10px;width:auto" onchange="deptRenderPositions()">
                <option value="">All Departments</option>
              </select>
            </div>
          </div>
          <div class="card-body" style="overflow-x:auto">
            <table class="data-table"><thead><tr><th>Job Title</th><th>Department</th><th>Location</th><th>Employment Type</th><th>Base Salary</th><th>Status</th></tr></thead><tbody id="pos-tbody"><tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr></tbody></table>
          </div>
        </div>
      </div>

    </main>
  </div>

  <script src="../../app.js"></script>
  <script>
// ═══════════════════════════════════════════════════════
// DEPARTMENT MANAGEMENT MODULE — JavaScript
// ═══════════════════════════════════════════════════════

const HR4_BRIDGE = '../../api/hr4.php';
let deptData = [];        // departments summary list
let allEmployees = [];    // all employees from HR4
let allContracts = [];    // all contracts from HR4
let allPositions = [];    // vacant positions from HR4

// ── Department colors/icons helper ──
const DEPT_COLORS = [
  { bg:'#D1FAE5', color:'#065F46', icon:'🏦' },
  { bg:'#DBEAFE', color:'#1E40AF', icon:'💼' },
  { bg:'#EDE9FE', color:'#5B21B6', icon:'📊' },
  { bg:'#FEF3C7', color:'#92400E', icon:'⚙️' },
  { bg:'#FFE4E6', color:'#9F1239', icon:'🎯' },
  { bg:'#CCFBF1', color:'#0F766E', icon:'🔬' },
  { bg:'#FCE7F3', color:'#9D174D', icon:'📋' },
  { bg:'#F3E8FF', color:'#6B21A8', icon:'🛡️' },
  { bg:'#CFFAFE', color:'#155E75', icon:'📡' },
  { bg:'#FEF9C3', color:'#854D0E', icon:'🏗️' },
];

function getDeptStyle(deptName) {
  let hash = 0;
  for (let i = 0; i < deptName.length; i++) hash = deptName.charCodeAt(i) + ((hash << 5) - hash);
  return DEPT_COLORS[Math.abs(hash) % DEPT_COLORS.length];
}

// ── Section switching ──
function deptShowSection(hash) {
  const sections = document.querySelectorAll('.tab-content');
  const id = hash ? hash.replace('#', '') : 'tab-overview';

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

  if (id === 'tab-employees') deptRenderAllEmployees();
  if (id === 'tab-contracts') deptRenderAllContracts();
  if (id === 'tab-positions') deptRenderPositions();
}
window.addEventListener('hashchange', () => deptShowSection(location.hash));

// ── Formatting helpers ──
const fmtDate = d => d ? new Date(d).toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'}) : '—';
const fmtPeso = v => v ? '₱' + parseFloat(v).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2}) : '—';
function statusBadge(st) {
  const s = (st||'').toLowerCase();
  const c = ['active','regular'].includes(s) ? '#059669' : ['probationary'].includes(s) ? '#D97706' : ['terminated','resigned','separated'].includes(s) ? '#EF4444' : '#6B7280';
  return `<span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${c}20;color:${c}">${st||'—'}</span>`;
}
function expiryBadge(d) {
  if (d === null || d === undefined) return '<span style="color:#9CA3AF">—</span>';
  if (d < 0)   return `<span style="font-weight:700;color:#EF4444">Expired</span>`;
  if (d <= 30)  return `<span style="font-weight:700;color:#F59E0B">${d}d</span>`;
  if (d <= 90)  return `<span style="font-weight:700;color:#D97706">${d}d</span>`;
  return `<span style="font-weight:600;color:#059669">${d}d</span>`;
}

// ═══════════════════════════════════════════════════════
// Data Loading
// ═══════════════════════════════════════════════════════

async function deptCheckConnection() {
  const dot = document.getElementById('dept-hr4-dot');
  const txt = document.getElementById('dept-hr4-text');
  try {
    const res = await fetch(HR4_BRIDGE + '?action=health').then(r => r.json());
    if (res.hr4_alive) {
      dot.style.background = '#059669'; dot.style.animation = 'none';
      txt.textContent = '✓ Connected to HR4'; txt.style.color = '#059669';
    } else {
      dot.style.background = '#EF4444'; dot.style.animation = 'none';
      txt.textContent = '✗ HR4 unreachable'; txt.style.color = '#EF4444';
    }
  } catch {
    dot.style.background = '#EF4444'; dot.style.animation = 'none';
    txt.textContent = '✗ HR4 bridge error'; txt.style.color = '#EF4444';
  }
}

async function deptLoadDepartments() {
  try {
    const res = await fetch(HR4_BRIDGE + '?action=departments').then(r => r.json());
    deptData = res.data || [];
    document.getElementById('stat-dept-count').textContent = deptData.length;
    deptRenderGrid(deptData);
  } catch (err) {
    document.getElementById('dept-grid').innerHTML = `<div class="card" style="grid-column:1/-1;text-align:center;padding:40px;color:#EF4444">Failed to load departments: ${err.message}</div>`;
  }
}

async function deptLoadEmployees() {
  try {
    const res = await fetch(HR4_BRIDGE + '?action=employees').then(r => r.json());
    allEmployees = res.data || [];
    document.getElementById('stat-emp-count').textContent = allEmployees.length;
    deptPopulateDeptFilters();
  } catch (err) { console.error('Failed to load employees:', err); }
}

async function deptLoadContracts() {
  try {
    const res = await fetch(HR4_BRIDGE + '?action=contracts').then(r => r.json());
    allContracts = res.data || [];
    document.getElementById('stat-contract-count').textContent = allContracts.length;
  } catch (err) { console.error('Failed to load contracts:', err); }
}

async function deptLoadPositions() {
  try {
    const res = await fetch(HR4_BRIDGE + '?action=vacant_positions').then(r => r.json());
    allPositions = res.data || [];
    document.getElementById('stat-position-count').textContent = allPositions.length;
  } catch (err) { console.error('Failed to load positions:', err); }
}

function deptPopulateDeptFilters() {
  const depts = [...new Set(allEmployees.map(e => (e.position && e.position.department) || 'Unassigned'))].sort();
  ['emp-dept-filter', 'contract-dept-filter', 'pos-dept-filter'].forEach(id => {
    const sel = document.getElementById(id);
    if (!sel) return;
    const val = sel.value;
    sel.innerHTML = '<option value="">All Departments</option>';
    depts.forEach(d => { const o = document.createElement('option'); o.value = d; o.textContent = d; sel.appendChild(o); });
    sel.value = val;
  });
}

// ═══════════════════════════════════════════════════════
// TAB 1 — Department Grid
// ═══════════════════════════════════════════════════════

function deptRenderGrid(list) {
  const grid = document.getElementById('dept-grid');
  grid.innerHTML = '';

  document.getElementById('dept-summary').textContent =
    list.length + ' department' + (list.length !== 1 ? 's' : '') + ' found';

  if (!list.length) {
    grid.innerHTML = '<div class="card" style="grid-column:1/-1;text-align:center;padding:40px;color:#9CA3AF"><div style="font-size:40px;margin-bottom:8px">🏢</div><div style="font-weight:600">No departments found</div></div>';
    return;
  }

  list.forEach(dept => {
    const style = getDeptStyle(dept.department);
    const card = document.createElement('div');
    card.className = 'card dept-card';
    card.dataset.dept = dept.department;
    card.style.cssText = 'margin-bottom:0;cursor:pointer;transition:all 0.2s;border:2px solid transparent;';
    card.onmouseover = () => { card.style.borderColor = style.color; card.style.boxShadow = '0 6px 20px rgba(0,0,0,0.08)'; card.style.transform = 'translateY(-2px)'; };
    card.onmouseout  = () => { card.style.borderColor = 'transparent'; card.style.boxShadow = ''; card.style.transform = ''; };
    card.onclick = () => deptOpenDetail(dept.department);

    card.innerHTML = `
      <div class="card-body padded">
        <div style="display:flex;align-items:flex-start;gap:14px">
          <div style="width:52px;height:52px;border-radius:14px;background:${style.bg};display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0">${style.icon}</div>
          <div style="flex:1;min-width:0">
            <div style="font-size:15px;font-weight:800;color:#1F2937">${dept.department}</div>
            <div style="font-size:12px;color:#6B7280;margin-top:2px">📍 ${dept.location || '—'}</div>
            <div style="font-size:12px;font-weight:600;color:${style.color};margin-top:6px">${dept.total} employee${dept.total !== 1 ? 's' : ''}</div>
          </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:14px;font-size:11px;flex-wrap:wrap">
          ${dept.active   ? `<span style="background:#D1FAE5;color:#065F46;padding:2px 8px;border-radius:6px;font-weight:600">🟢 ${dept.active} Active</span>` : ''}
          ${dept.inactive ? `<span style="background:#FEE2E2;color:#9F1239;padding:2px 8px;border-radius:6px;font-weight:600">🔴 ${dept.inactive} Inactive</span>` : ''}
        </div>
        <div style="margin-top:10px;font-size:11px;color:#9CA3AF">
          ${(dept.positions||[]).length ? `<span title="${(dept.positions||[]).join(', ')}">🏷️ ${(dept.positions||[]).length} position${(dept.positions||[]).length!==1?'s':''}</span>` : ''}
        </div>
      </div>`;
    grid.appendChild(card);
  });
}

function deptFilterGrid(q) {
  const query = (q || '').toLowerCase();
  if (!query) { deptRenderGrid(deptData); return; }
  deptRenderGrid(deptData.filter(d =>
    (d.department || '').toLowerCase().includes(query) ||
    (d.location || '').toLowerCase().includes(query) ||
    (d.positions || []).some(p => p.toLowerCase().includes(query))
  ));
}

// ═══════════════════════════════════════════════════════
// Department Detail Panel
// ═══════════════════════════════════════════════════════

async function deptOpenDetail(deptName) {
  const panel = document.getElementById('dept-detail-panel');
  panel.style.display = '';

  document.getElementById('dept-detail-title').textContent = '🏢 ' + deptName;
  document.getElementById('dept-detail-summary').textContent = 'Loading…';
  document.getElementById('detail-emp-tbody').innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr>';
  document.getElementById('detail-contract-tbody').innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr>';

  // Scroll to panel
  panel.scrollIntoView({ behavior: 'smooth', block: 'start' });

  try {
    const res = await fetch(HR4_BRIDGE + '?action=department_detail&department=' + encodeURIComponent(deptName)).then(r => r.json());
    if (!res.success) throw new Error(res.error || 'Failed');

    const s = res.summary;
    document.getElementById('dept-detail-summary').textContent =
      `${s.total} employees · ${s.active} active · ${s.inactive} inactive · ${s.contracts} contracts · ${(s.positions||[]).length} positions`;

    // Render employees
    const empTbody = document.getElementById('detail-emp-tbody');
    if (!res.employees.length) {
      empTbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">No employees in this department</td></tr>';
    } else {
      empTbody.innerHTML = res.employees.map(e => `<tr>
        <td style="font-weight:600;font-size:12px">${e.employee_id||'—'}</td>
        <td style="font-weight:600">${e.full_name||'—'}</td>
        <td style="font-size:12px;color:#6B7280">${e.email||'—'}</td>
        <td>${e.job_title||'—'}</td>
        <td style="font-size:12px">${e.employment_type||'—'}</td>
        <td>${statusBadge(e.employment_status)}</td>
        <td style="font-size:12px;color:#6B7280">${fmtDate(e.hired_date)}</td>
        <td style="text-align:right;font-size:12px">${fmtPeso(e.base_salary)}</td>
      </tr>`).join('');
    }

    // Render contracts
    const conTbody = document.getElementById('detail-contract-tbody');
    if (!res.contracts.length) {
      conTbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">No contracts in this department</td></tr>';
    } else {
      conTbody.innerHTML = res.contracts.map(c => `<tr>
        <td style="font-weight:600;font-size:12px">${c.employee_id||'—'}</td>
        <td style="font-weight:600">${c.full_name||'—'}</td>
        <td style="font-size:12px">${c.contract_no||'—'}</td>
        <td style="text-align:center">${c.contract_duration_months ? c.contract_duration_months + ' mo' : '—'}</td>
        <td style="font-size:12px">${fmtDate(c.start_date)}</td>
        <td style="font-size:12px">${fmtDate(c.end_date)}</td>
        <td style="text-align:center">${expiryBadge(c.days_until_expiry)}</td>
      </tr>`).join('');
    }

  } catch (err) {
    document.getElementById('dept-detail-summary').textContent = 'Error: ' + err.message;
    document.getElementById('detail-emp-tbody').innerHTML = `<tr><td colspan="8" style="text-align:center;padding:30px;color:#EF4444">Failed: ${err.message}</td></tr>`;
  }
}

function deptCloseDetail() {
  document.getElementById('dept-detail-panel').style.display = 'none';
}

function deptDetailTab(panelId, btn) {
  document.querySelectorAll('#dept-detail-body .sub-tab').forEach(t => t.classList.remove('active'));
  if (btn) btn.classList.add('active');
  document.querySelectorAll('.dept-detail-sub').forEach(p => p.style.display = 'none');
  const panel = document.getElementById(panelId);
  if (panel) panel.style.display = '';
}

// ═══════════════════════════════════════════════════════
// TAB 2 — All Employees Table
// ═══════════════════════════════════════════════════════

function deptRenderAllEmployees() {
  const tbody = document.getElementById('all-emp-tbody');
  const deptFilter = document.getElementById('emp-dept-filter')?.value || '';
  const searchQ = (document.getElementById('emp-search')?.value || '').toLowerCase();

  let list = allEmployees;
  if (deptFilter) list = list.filter(e => ((e.position && e.position.department) || 'Unassigned') === deptFilter);
  if (searchQ) list = list.filter(e =>
    (e.full_name||'').toLowerCase().includes(searchQ) ||
    (e.email||'').toLowerCase().includes(searchQ) ||
    (e.employee_id||'').toLowerCase().includes(searchQ)
  );

  if (!list.length) { tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:30px;color:#9CA3AF">No employees found</td></tr>'; return; }

  tbody.innerHTML = list.slice(0, 200).map(e => {
    const dept = (e.position && e.position.department) || 'Unassigned';
    const job = (e.position && e.position.job && e.position.job.job_title) || (e.job_title && e.job_title.job_title) || '—';
    const st = e.employment_status || e.status || '—';
    const tp = (e.position && e.position.employment_type) || e.employment_type || '—';
    const hired = fmtDate(e.hired_date);
    return `<tr>
      <td style="font-weight:600;font-size:12px">${e.employee_id||'—'}</td>
      <td style="font-weight:600">${e.full_name||'—'}</td>
      <td style="font-size:12px;color:#6B7280">${e.email||'—'}</td>
      <td>${dept}</td>
      <td>${job}</td>
      <td style="font-size:12px">${tp}</td>
      <td>${statusBadge(st)}</td>
      <td style="font-size:12px;color:#6B7280">${hired}</td>
    </tr>`;
  }).join('');
}

// ═══════════════════════════════════════════════════════
// TAB 3 — All Contracts Table
// ═══════════════════════════════════════════════════════

function deptRenderAllContracts() {
  const tbody = document.getElementById('all-contract-tbody');
  const deptFilter = document.getElementById('contract-dept-filter')?.value || '';
  const expiryDays = parseInt(document.getElementById('contract-expiry-filter')?.value || '0');

  let list = allContracts;
  if (deptFilter) list = list.filter(c => c.department === deptFilter);
  if (expiryDays > 0) list = list.filter(c => c.days_until_expiry !== null && c.days_until_expiry >= 0 && c.days_until_expiry <= expiryDays);

  if (!list.length) { tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">No contracts found</td></tr>'; return; }

  tbody.innerHTML = list.slice(0, 200).map(c => `<tr>
    <td style="font-weight:600">${c.full_name||c.employee_id||'—'}</td>
    <td>${c.department||'—'}</td>
    <td style="font-size:12px">${c.contract_no||'—'}</td>
    <td style="text-align:center">${c.contract_duration_months ? c.contract_duration_months + ' mo' : '—'}</td>
    <td style="font-size:12px">${fmtDate(c.start_date)}</td>
    <td style="font-size:12px">${fmtDate(c.end_date)}</td>
    <td style="text-align:center">${expiryBadge(c.days_until_expiry)}</td>
  </tr>`).join('');
}

// ═══════════════════════════════════════════════════════
// TAB 4 — Vacant Positions
// ═══════════════════════════════════════════════════════

function deptRenderPositions() {
  const tbody = document.getElementById('pos-tbody');
  const deptFilter = document.getElementById('pos-dept-filter')?.value || '';

  let list = allPositions;
  if (deptFilter) list = list.filter(p => p.department === deptFilter);

  if (!list.length) { tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No vacant positions</td></tr>'; return; }

  tbody.innerHTML = list.slice(0, 200).map(p => {
    const st = p.status || 'open';
    const stColor = (st === 'open' || st === 'vacant') ? '#059669' : '#6B7280';
    return `<tr>
      <td style="font-weight:600">${(p.job && p.job.job_title) || p.job_title || '—'}</td>
      <td>${p.department||'—'}</td>
      <td>${p.location||'—'}</td>
      <td>${p.employment_type||'—'}</td>
      <td style="text-align:right">${fmtPeso(p.base_salary)}</td>
      <td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td>
    </tr>`;
  }).join('');
}

// ═══════════════════════════════════════════════════════
// Export Functions
// ═══════════════════════════════════════════════════════

function deptExportEmployees(format) {
  const deptFilter = document.getElementById('emp-dept-filter')?.value || '';
  let list = allEmployees;
  if (deptFilter) list = list.filter(e => ((e.position && e.position.department) || 'Unassigned') === deptFilter);

  const headers = ['Employee ID', 'Name', 'Email', 'Department', 'Job Title', 'Type', 'Status', 'Hired'];
  const rows = list.map(e => [
    e.employee_id || '', e.full_name || '', e.email || '',
    (e.position && e.position.department) || 'Unassigned',
    (e.position && e.position.job && e.position.job.job_title) || (e.job_title && e.job_title.job_title) || '',
    (e.position && e.position.employment_type) || e.employment_type || '',
    e.employment_status || e.status || '',
    e.hired_date || ''
  ]);

  if (format === 'pdf') exportPDF('Employee Directory' + (deptFilter ? ' — ' + deptFilter : ''), headers, rows);
  else exportCSV('employees' + (deptFilter ? '_' + deptFilter : ''), headers, rows);
}

function deptExportContracts(format) {
  const deptFilter = document.getElementById('contract-dept-filter')?.value || '';
  let list = allContracts;
  if (deptFilter) list = list.filter(c => c.department === deptFilter);

  const headers = ['Employee', 'Department', 'Contract No', 'Duration (mo)', 'Start', 'End', 'Days to Expiry'];
  const rows = list.map(c => [
    c.full_name || c.employee_id || '', c.department || '', c.contract_no || '',
    c.contract_duration_months || '', c.start_date || '', c.end_date || '',
    c.days_until_expiry !== null ? c.days_until_expiry : ''
  ]);

  if (format === 'pdf') exportPDF('Employment Contracts' + (deptFilter ? ' — ' + deptFilter : ''), headers, rows);
  else exportCSV('contracts' + (deptFilter ? '_' + deptFilter : ''), headers, rows);
}

function exportPDF(title, headers, rows) {
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('l', 'mm', 'a4');
  doc.setFontSize(16);
  doc.text(title, 14, 18);
  doc.setFontSize(9);
  doc.text('Generated: ' + new Date().toLocaleString(), 14, 24);
  doc.autoTable({ head: [headers], body: rows, startY: 28, styles: { fontSize: 8 }, headStyles: { fillColor: [5, 150, 105] } });
  doc.save(title.replace(/[^a-zA-Z0-9]/g, '_') + '.pdf');
}

function exportCSV(filename, headers, rows) {
  const escape = v => '"' + String(v).replace(/"/g, '""') + '"';
  const csv = [headers.map(escape).join(','), ...rows.map(r => r.map(escape).join(','))].join('\n');
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8' });
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = filename + '.csv';
  a.click();
}

// ═══════════════════════════════════════════════════════
// Refresh & Initialize
// ═══════════════════════════════════════════════════════

function deptRefreshAll() {
  deptData = []; allEmployees = []; allContracts = []; allPositions = [];
  deptInit();
}

async function deptInit() {
  deptCheckConnection();
  await Promise.all([
    deptLoadDepartments(),
    deptLoadEmployees(),
    deptLoadContracts(),
    deptLoadPositions()
  ]);
  deptShowSection(location.hash);
}

deptInit();
  </script>
</body>
</html>
