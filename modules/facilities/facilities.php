<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Facilities Reservation — Microfinancial Admin</title>

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
  <style>
    @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.3; } }
    
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

    /* Room filter buttons with count badges */
    .room-filter {
      position: relative;
      transition: all 0.2s;
    }

    .room-filter span.count-badge {
      font-size: 11px;
      background: rgba(0,0,0,0.1);
      padding: 2px 6px;
      border-radius: 12px;
      margin-left: 6px;
    }

    .room-filter.active span.count-badge {
      background: rgba(255,255,255,0.2);
    }

    /* Room grid styles */
    #room-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 20px;
      min-height: 400px;
    }

    .room-card {
      transition: transform 0.2s, box-shadow 0.2s;
    }

    .room-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }

    /* Empty state styling */
    .empty-state {
      grid-column: 1/-1;
      text-align: center;
      padding: 60px 20px;
      background: white;
      border-radius: 16px;
      border: 1px dashed #D1D5DB;
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
  <script src="../../hr2-integration.js"></script>
  <script src="../../hr4-integration.js"></script>
  <style>
    #hr2-training-view .hr2-panel{display:block!important;margin-bottom:20px}
  </style>
</head>

<body class="bg-brand-background-main min-h-screen font-[Inter,'Segoe_UI',system-ui,-apple-system,sans-serif]">

  <?php $activePage = 'facilities'; $baseUrl = '../../'; include '../../sidebar.php'; ?>

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
            <div class="w-10 h-10 rounded-full bg-white shadow group-hover:shadow-md transition-shadow overflow-hidden flex items-center justify-center border border-gray-100">
              <div class="w-full h-full flex items-center justify-center font-bold text-brand-primary bg-emerald-50"><?= $userInitial ?></div>
            </div>
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
        <h1 class="page-title">Facilities Reservation</h1>
        <p class="page-subtitle">Room monitoring, bookings, VIP reservations, important meetings, maintenance tracking &amp; HR integrations</p>
      </div>

      <!-- SUBMODULE DIRECTORY -->
      <div class="animate-in delay-1">
        <div class="module-directory-label">Submodule Directory</div>
        <div class="stats-grid" style="margin-bottom:18px">
          <a href="#tab-monitoring" onclick="showSection('#tab-monitoring')" class="stat-card stat-card-link">
            <div class="stat-icon green">🏢</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-total">0</div>
              <div class="stat-label">Total Facilities</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-approved" onclick="showSection('#tab-approved')" class="stat-card stat-card-link">
            <div class="stat-icon blue">✅</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-pending">0</div>
              <div class="stat-label">Pending Bookings</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-utilization" onclick="showSection('#tab-utilization')" class="stat-card stat-card-link">
            <div class="stat-icon purple">📊</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-utilization">0%</div>
              <div class="stat-label">Room Utilization</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-maintenance" onclick="showSection('#tab-maintenance')" class="stat-card stat-card-link">
            <div class="stat-icon amber">🔧</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-malfunction">0</div>
              <div class="stat-label">Maintenance Issues</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-approved" onclick="showSection('#tab-approved')" class="stat-card stat-card-link">
            <div class="stat-icon blue">⭐</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-vip">0</div>
              <div class="stat-label">VIP Reservations</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-approved" onclick="showSection('#tab-approved')" class="stat-card stat-card-link" style="border-left:3px solid #DC2626">
            <div class="stat-icon red">🚨</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-emergency">0</div>
              <div class="stat-label">Important Active</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
          <a href="#tab-room-logs" onclick="showSection('#tab-room-logs')" class="stat-card stat-card-link" style="border-left:3px solid #10B981">
            <div class="stat-icon green">📋</div>
            <div class="stat-info">
              <div class="stat-value" id="stat-room-logs">0</div>
              <div class="stat-label">Room Usage Logs</div>
            </div>
            <div class="stat-arrow">→</div>
          </a>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Room Booking & Calendar (merged)               -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-monitoring" class="tab-content active animate-in delay-3">

        <!-- Sub-tabs: Rooms | Reservations | Calendar | HR2 Training -->
        <div class="sub-tabs" id="booking-sub-tabs" style="margin-bottom:20px">
          <button class="sub-tab active" data-booking-tab="rooms-view" onclick="switchBookingTab('rooms-view',this)">🏢 Room Monitoring</button>
          <button class="sub-tab" data-booking-tab="reservations-view" onclick="switchBookingTab('reservations-view',this)">📋 Reservations</button>
          <button class="sub-tab" data-booking-tab="calendar-view" onclick="switchBookingTab('calendar-view',this)">📅 Calendar</button>
          <button class="sub-tab" data-booking-tab="hr2-training-view" onclick="switchBookingTab('hr2-training-view',this)">🏫 HR2 Bookings</button>
        </div>

        <!-- ── Sub-tab: Room Monitoring ── -->
        <div id="rooms-view" class="sub-tab-content active">
          <!-- Filter bar with improved styling -->
          <div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;align-items:center">
            <span style="font-size:13px;font-weight:600;color:#4B5563">Filter:</span>
            <button class="btn btn-sm btn-outline room-filter active" data-filter="all" onclick="filterRooms('all',this)">All Rooms</button>
            <button class="btn btn-sm btn-outline room-filter" data-filter="available" onclick="filterRooms('available',this)">🟢 Available</button>
            <button class="btn btn-sm btn-outline room-filter" data-filter="occupied" onclick="filterRooms('occupied',this)">🔴 Occupied</button>
            <button class="btn btn-sm btn-outline room-filter" data-filter="pending" onclick="filterRooms('pending',this)">⏳ Pending</button>
            <button class="btn btn-sm btn-outline room-filter" data-filter="maintenance" onclick="filterRooms('maintenance',this)">🟡 Maintenance</button>
          </div>
          
          <!-- Room Cards Grid with pagination -->
          <div id="room-grid" style="min-height:400px"></div>
          
          <!-- Pagination container for rooms (always visible) -->
          <div id="rooms-pagination" class="pagination-container"></div>
        </div>

        <!-- ── Sub-tab: Reservations ── -->
        <div id="reservations-view" class="sub-tab-content">
          <div class="card">
            <div class="card-header">
              <span class="card-title">Pending & Upcoming Reservations</span>
              <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportReservations('pdf')" title="Export PDF">📄 PDF</button>
                <button class="btn-export btn-export-csv btn-export-sm" onclick="exportReservations('csv')" title="Export CSV">📊 CSV</button>
                <select id="res-filter-type" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="filterReservations()">
                  <option value="">All Types</option>
                  <option value="regular">Regular</option>
                  <option value="vip">⭐ VIP</option>
                  <option value="emergency">⚡ Important</option>
                </select>
                <select id="res-filter-status" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="filterReservations()">
                  <option value="">All Status</option>
                  <option value="pending">Pending</option>
                  <option value="approved">Approved</option>
                  <option value="ongoing">Ongoing</option>
                  <option value="completed">Completed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
                <button class="btn btn-primary btn-sm" onclick="openReservationModal()">+ New Reservation</button>
              </div>
            </div>
            <div class="card-body">
              <table class="data-table" id="reservations-table">
                <thead><tr>
                  <th>Code</th><th>Facility</th><th>Event / Purpose</th><th>Type</th><th>Date &amp; Time</th>
                  <th>Budget</th><th>Equipment</th><th>Department</th><th>Status</th><th>Validated</th><th>Actions</th>
                </tr></thead>
                <tbody id="reservations-tbody"></tbody>
              </table>
            </div>
            <div id="reservations-pagination"></div>
          </div>
        </div>

        <!-- ── Sub-tab: Calendar ── -->
        <div id="calendar-view" class="sub-tab-content">
          <div class="card">
            <div class="card-header">
              <div style="display:flex;align-items:center;gap:12px">
                <button class="btn btn-outline btn-sm" onclick="changeMonth(-1)" id="cal-prev">◀</button>
                <span class="card-title" id="cal-month-label">📅 Loading...</span>
                <button class="btn btn-outline btn-sm" onclick="changeMonth(1)" id="cal-next">▶</button>
              </div>
              <div style="display:flex;gap:8px;align-items:center">
                <button class="btn btn-outline btn-sm" onclick="goToToday()">Today</button>
              </div>
            </div>
            <div class="card-body" style="padding:20px">
              <!-- Legend -->
              <div style="display:flex;gap:16px;margin-bottom:16px;flex-wrap:wrap;font-size:12px;color:#4B5563">
                <span style="display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#059669;display:inline-block"></span> Approved</span>
                <span style="display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#F59E0B;display:inline-block"></span> Pending</span>
                <span style="display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#7C3AED;display:inline-block"></span> VIP</span>
                <span style="display:flex;align-items:center;gap:4px"><span style="width:10px;height:10px;border-radius:50%;background:#DC2626;display:inline-block"></span> Important</span>
              </div>
              <!-- Calendar grid -->
              <div id="calendar-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;font-size:13px">
                <div style="font-weight:700;color:#6B7280;padding:8px">Sun</div>
                <div style="font-weight:700;color:#6B7280;padding:8px">Mon</div>
                <div style="font-weight:700;color:#6B7280;padding:8px">Tue</div>
                <div style="font-weight:700;color:#6B7280;padding:8px">Wed</div>
                <div style="font-weight:700;color:#6B7280;padding:8px">Thu</div>
                <div style="font-weight:700;color:#6B7280;padding:8px">Fri</div>
                <div style="font-weight:700;color:#6B7280;padding:8px">Sat</div>
              </div>
            </div>
          </div>
          <!-- Day Schedule Panel -->
          <div class="card" id="day-schedule-card" style="margin-top:20px;display:none">
            <div class="card-header">
              <span class="card-title" id="day-schedule-title">Schedule</span>
              <button class="btn btn-primary btn-sm" onclick="openReservationModal()">+ Add to this day</button>
            </div>
            <div class="card-body" id="day-schedule-body"></div>
          </div>
        </div>

        <!-- ── Sub-tab: HR2 Training Room Bookings ── -->
        <div id="hr2-training-view" class="sub-tab-content">
          <!-- HR2 Connection Status (compact) -->
          <div id="hr2-status-bar" style="display:flex;align-items:center;gap:10px;margin-bottom:18px;padding:10px 16px;border-radius:10px;background:#EEF2FF;border:1px solid #C7D2FE">
            <span id="hr2-status-dot" style="width:10px;height:10px;border-radius:50%;background:#9CA3AF;animation:pulse-dot 1.5s infinite"></span>
            <span id="hr2-status-text" style="font-size:13px;font-weight:600;color:#4B5563">Checking HR2 connection…</span>
            <span style="flex:1"></span>
            <span style="font-size:12px;color:#6B7280" id="hr2-training-count"></span>
            <button onclick="hr2RefreshTraining()" class="btn btn-outline" style="font-size:12px;padding:5px 14px">🔄 Refresh</button>
          </div>

          <!-- Training Bookings Filter -->
          <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap;align-items:center">
            <span style="font-size:13px;font-weight:600;color:#4B5563">Filter:</span>
            <select id="hr2-training-filter-status" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="hr2FilterTraining()">
              <option value="">All Status</option>
              <option value="pending" selected>⏳ Pending</option>
              <option value="confirmed">✅ Confirmed</option>
              <option value="approved">✅ Approved</option>
              <option value="cancelled">❌ Cancelled</option>
            </select>
            <input type="text" id="hr2-training-search" placeholder="Search training…" style="font-size:12px;padding:6px 12px;border:1px solid #D1D5DB;border-radius:8px;width:220px" oninput="hr2FilterTraining()">
          </div>

          <!-- Training Bookings Table -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">🏫 HR2 Room Bookings (Training, Interviews & Events)</span>
            </div>
            <div class="card-body" style="padding:0;overflow-x:auto">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Booking Code</th>
                    <th>Course / Training</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Facilitator</th>
                    <th>Attendees</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="hr2-training-tbody">
                  <tr><td colspan="9" style="text-align:center;padding:30px;color:#9CA3AF">Click the "HR2 Bookings" tab to load data…</td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Approved Bookings                              -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-approved" class="tab-content">
        <div class="card">
          <div class="card-header">
            <span class="card-title">✅ Booking Approvals & Status</span>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportApprovedBookings('pdf')" title="Export PDF">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportApprovedBookings('csv')" title="Export CSV">📊 CSV</button>
              <select id="approved-filter-category" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderApprovedBookings()">
                <option value="">All Categories</option>
                <option value="emergency">⚡ Important</option>
                <option value="vip">⭐ VIP</option>
                <option value="regular">📋 Regular</option>
              </select>
              <select id="approved-filter-dept" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderApprovedBookings()">
                <option value="">All Departments</option>
                <option value="own">🏢 My Department</option>
                <option value="inter">🔄 Inter-Department</option>
              </select>
              <select id="approved-filter-status" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderApprovedBookings()">
                <option value="">All Status</option>
                <option value="ongoing">🔴 Ongoing (Occupied)</option>
                <option value="upcoming">⏳ Upcoming (Pending)</option>
                <option value="completed">✅ Completed</option>
              </select>
            </div>
          </div>
          <div class="card-body">
            <table class="data-table" id="approved-table">
              <thead><tr>
                <th>Code</th><th>Facility</th><th>Event / Purpose</th><th>Category</th>
                <th>Date &amp; Time</th><th>Department</th><th>Room Status</th><th>Usage Log</th><th>Actions</th>
              </tr></thead>
              <tbody id="approved-tbody"></tbody>
            </table>
          </div>
          <div id="approved-pagination"></div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Room Usage Logs                                -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-room-logs" class="tab-content">

        <!-- Log Summary Cards -->
        <div class="stats-grid" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon green">📋</div><div class="stat-info"><div class="stat-value" id="stat-log-total">0</div><div class="stat-label">Total Room Logs</div></div></div>
          <div class="stat-card"><div class="stat-icon blue">⏱️</div><div class="stat-info"><div class="stat-value" id="stat-log-hours">0</div><div class="stat-label">Total Hours Used</div></div></div>
          <div class="stat-card"><div class="stat-icon purple">🏢</div><div class="stat-info"><div class="stat-value" id="stat-log-most-room">—</div><div class="stat-label">Most Used Room</div></div></div>
          <div class="stat-card"><div class="stat-icon amber">🚫</div><div class="stat-info"><div class="stat-value" id="stat-log-cancelled">0</div><div class="stat-label">Cancelled</div></div></div>
        </div>

        <!-- Level Breakdown Cards -->
        <div class="stats-grid" style="margin-bottom:20px">
          <div class="stat-card" style="border-left:3px solid #3B82F6"><div class="stat-icon blue">📋</div><div class="stat-info"><div class="stat-value" id="stat-log-lvl1">0</div><div class="stat-label">Level 1 · Normal</div></div></div>
          <div class="stat-card" style="border-left:3px solid #7C3AED"><div class="stat-icon purple">⭐</div><div class="stat-info"><div class="stat-value" id="stat-log-lvl2">0</div><div class="stat-label">Level 2 · VIP</div></div></div>
          <div class="stat-card" style="border-left:3px solid #DC2626"><div class="stat-icon red">⚡</div><div class="stat-info"><div class="stat-value" id="stat-log-lvl3">0</div><div class="stat-label">Level 3 · Important</div></div></div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">📋 Room Usage Logs</span>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportRoomLogs('pdf')" title="Export PDF">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportRoomLogs('csv')" title="Export CSV">📊 CSV</button>
              <select id="log-filter-room" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadRoomLogs()">
                <option value="">All Rooms</option>
                <!-- Populated dynamically -->
              </select>
              <select id="log-filter-level" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadRoomLogs()">
                <option value="">All Levels</option>
                <option value="1">Level 1 · Normal</option>
                <option value="2">Level 2 · VIP</option>
                <option value="3">Level 3 · Important</option>
              </select>
              <select id="log-filter-type" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadRoomLogs()">
                <option value="">All Types</option>
                <option value="regular">Regular</option>
                <option value="vip">⭐ VIP</option>
                <option value="emergency">⚡ Important</option>
              </select>
              <select id="log-filter-status" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadRoomLogs()">
                <option value="">All Status</option>
                <option value="completed">✅ Completed</option>
                <option value="cancelled">🚫 Cancelled</option>
                <option value="no_show">❌ No Show</option>
              </select>
              <input type="date" id="log-filter-from" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadRoomLogs()" title="From Date">
              <input type="date" id="log-filter-to" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="loadRoomLogs()" title="To Date">
            </div>
          </div>
          <div class="card-body">
            <table class="data-table" id="room-logs-table">
              <thead><tr>
                <th>Log #</th>
                <th>Facility</th>
                <th>Event / Purpose</th>
                <th>Type</th>
                <th>Date &amp; Time</th>
                <th>Duration</th>
                <th>Department</th>
                <th>Reserved By</th>
                <th>Status</th>
              </tr></thead>
              <tbody id="room-logs-tbody">
                <tr><td colspan="9" style="text-align:center;padding:30px;color:#9CA3AF">Loading room usage logs…</td></tr>
              </tbody>
            </table>
          </div>
          <div id="room-logs-pagination"></div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Maintenance                                    -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-maintenance" class="tab-content">

        <!-- Maintenance Stat Cards -->
        <div class="stats-grid" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon red">🔧</div><div class="stat-info"><div class="stat-value" id="stat-maint-open">0</div><div class="stat-label">Open Tickets</div></div></div>
          <div class="stat-card"><div class="stat-icon blue">⚙️</div><div class="stat-info"><div class="stat-value" id="stat-maint-progress">0</div><div class="stat-label">In Progress</div></div></div>
          <div class="stat-card"><div class="stat-icon amber">⚠️</div><div class="stat-info"><div class="stat-value" id="stat-maint-equipment">0</div><div class="stat-label">Equipment Malfunction</div></div></div>
          <div class="stat-card"><div class="stat-icon green">✅</div><div class="stat-info"><div class="stat-value" id="stat-maint-resolved">0</div><div class="stat-label">Resolved</div></div></div>
        </div>

        <div class="card">
          <div class="card-header">
            <span class="card-title">🛠️ Maintenance & Repair Tracking</span>
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportMaintenance('pdf')" title="Export PDF">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportMaintenance('csv')" title="Export CSV">📊 CSV</button>
              <select id="maint-filter-status" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderMaintenance()">
                <option value="">All Status</option>
                <option value="open">🔴 Open</option>
                <option value="in_progress">🔵 In Progress</option>
                <option value="resolved">🟢 Resolved</option>
                <option value="closed">⚫ Closed</option>
              </select>
              <select id="maint-filter-type" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderMaintenance()">
                <option value="">All Types</option>
                <option value="equipment">🔧 Equipment Malfunction</option>
                <option value="electrical">⚡ Electrical</option>
                <option value="plumbing">🚿 Plumbing</option>
                <option value="hvac">❄️ HVAC</option>
                <option value="structural">🏗️ Structural</option>
                <option value="cleaning">🧹 Cleaning</option>
                <option value="other">📋 Other</option>
              </select>
              <select id="maint-filter-priority" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderMaintenance()">
                <option value="">All Priority</option>
                <option value="critical">🔴 Critical</option>
                <option value="high">🟠 High</option>
                <option value="medium">🟡 Medium</option>
                <option value="low">🟢 Low</option>
              </select>
              <button class="btn btn-primary btn-sm" onclick="openMaintenanceModal()">+ Report Malfunction</button>
            </div>
          </div>
          <div class="card-body" id="maintenance-body"></div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- TAB: Room Utilization Dashboard                     -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="tab-utilization" class="tab-content">
        <!-- Utilization Summary Cards -->
        <div class="stats-grid" style="margin-bottom:20px">
          <div class="stat-card"><div class="stat-icon green">📊</div><div class="stat-info"><div class="stat-value" id="stat-util-avg">0%</div><div class="stat-label">Avg. Utilization Rate</div></div></div>
          <div class="stat-card"><div class="stat-icon blue">🏢</div><div class="stat-info"><div class="stat-value" id="stat-util-most">—</div><div class="stat-label">Most Used Room</div></div></div>
          <div class="stat-card"><div class="stat-icon amber">😴</div><div class="stat-info"><div class="stat-value" id="stat-util-least">—</div><div class="stat-label">Least Used Room</div></div></div>
          <div class="stat-card"><div class="stat-icon purple">📅</div><div class="stat-info"><div class="stat-value" id="stat-util-hours">0</div><div class="stat-label">Total Booked Hours (Month)</div></div></div>
        </div>

        <div class="card" style="margin-bottom:20px">
          <div class="card-header">
            <span class="card-title">📊 Room Utilization Rate</span>
            <div style="display:flex;gap:8px;align-items:center">
              <select id="util-period-filter" class="form-input" style="width:auto;padding:6px 12px;font-size:12px" onchange="renderUtilization()">
                <option value="30">Last 30 Days</option>
                <option value="60">Last 60 Days</option>
                <option value="90">Last 90 Days</option>
              </select>
              <button class="btn-export btn-export-pdf btn-export-sm" onclick="exportUtilization('pdf')">📄 PDF</button>
              <button class="btn-export btn-export-csv btn-export-sm" onclick="exportUtilization('csv')">📊 CSV</button>
            </div>
          </div>
          <div class="card-body" style="padding:20px">
            <div id="utilization-bars" style="display:flex;flex-direction:column;gap:16px"></div>
          </div>
        </div>

        <!-- Utilization by Day of Week -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">📅 Booking Heatmap by Day & Hour</span>
          </div>
          <div class="card-body" style="padding:20px">
            <div id="utilization-heatmap" style="overflow-x:auto"></div>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: New Maintenance Request                      -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-maintenance" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-maintenance')">
        <div class="modal" style="max-width:600px">
          <div class="modal-header">
            <span class="modal-title" id="modal-maint-title">🔧 Report Equipment Malfunction</span>
            <button class="modal-close" onclick="closeModal('modal-maintenance')">&times;</button>
          </div>
          <div class="modal-body">

            <div style="background:#FEF3C7;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;color:#92400E;border:1px solid #FDE68A">
              ⚠️ <strong>Report a malfunction or maintenance issue.</strong> Critical and high-priority issues will be flagged immediately. Equipment marked as malfunctioning will be taken offline until resolved.
            </div>

            <div class="form-control">
              <label>Issue Type</label>
              <select class="form-input" id="maint-issue-type" onchange="toggleEquipmentField()">
                <option value="equipment" selected>🔧 Equipment Malfunction</option>
                <option value="electrical">⚡ Electrical</option>
                <option value="plumbing">🚿 Plumbing</option>
                <option value="hvac">❄️ HVAC</option>
                <option value="structural">🏗️ Structural</option>
                <option value="cleaning">🧹 Cleaning</option>
                <option value="other">📋 Other</option>
              </select>
            </div>

            <div class="form-control">
              <label>Facility / Location</label>
              <select class="form-input" id="maint-facility" onchange="loadFacilityEquipment()">
                <!-- Populated dynamically -->
              </select>
            </div>

            <div class="form-control" id="maint-equipment-group">
              <label>Malfunctioning Equipment</label>
              <select class="form-input" id="maint-equipment">
                <option value="">— Select equipment —</option>
                <!-- Populated dynamically based on selected facility -->
              </select>
              <span style="font-size:11px;color:#9CA3AF;margin-top:4px;display:block">Select the specific equipment that is malfunctioning. It will be marked as unavailable until repaired.</span>
            </div>

            <div class="form-control">
              <label>Priority</label>
              <select class="form-input" id="maint-priority">
                <option value="low">🟢 Low — Minor issue, can wait</option>
                <option value="medium" selected>🟡 Medium — Should be fixed soon</option>
                <option value="high">🟠 High — Urgent, affects operations</option>
                <option value="critical">🔴 Critical — Immediate attention needed</option>
              </select>
            </div>

            <div class="form-control">
              <label>Description of Issue</label>
              <textarea class="form-input" id="maint-description" rows="4" placeholder="Describe the malfunction in detail: What happened? When did it start? Any error indicators or visible damage?"></textarea>
            </div>

            <div class="form-control">
              <label>Assign To (Optional)</label>
              <input type="text" class="form-input" id="maint-assigned" placeholder="Name of technician or maintenance team">
            </div>

            <div class="form-control" id="maint-facility-offline-group" style="display:none">
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
                <input type="checkbox" id="maint-set-offline" style="accent-color:#DC2626;width:18px;height:18px">
                <span style="font-size:13px;font-weight:600;color:#991B1B">⚠️ Take facility offline (set to Maintenance mode)</span>
              </label>
              <span style="font-size:11px;color:#9CA3AF;margin-top:4px;display:block">This will prevent new bookings for this facility until the issue is resolved.</span>
            </div>

          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-maintenance')">Cancel</button>
            <button class="btn btn-primary" id="btn-submit-maintenance" onclick="submitMaintenance()">🔧 Submit Report</button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Maintenance Detail / Update                  -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-maint-detail" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-maint-detail')">
        <div class="modal" style="max-width:560px">
          <div class="modal-header">
            <span class="modal-title" id="modal-maint-detail-title">Maintenance Details</span>
            <button class="modal-close" onclick="closeModal('modal-maint-detail')">&times;</button>
          </div>
          <div class="modal-body" id="modal-maint-detail-body"></div>
          <div class="modal-footer" id="modal-maint-detail-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-maint-detail')">Close</button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: New / Edit Reservation                       -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-reservation" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-reservation')">
        <div class="modal" style="max-width:640px">
          <div class="modal-header">
            <span class="modal-title" id="modal-res-title">New Reservation</span>
            <button class="modal-close" onclick="closeModal('modal-reservation')">&times;</button>
          </div>
          <div class="modal-body">

            <!-- Reservation Type Banner -->
            <div id="res-type-banner" style="display:none;padding:10px 14px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:600"></div>

            <!-- Auto-VIP Banner (for Directors) -->
            <div id="res-auto-vip-banner" style="display:none;margin-bottom:12px"></div>

            <!-- Room Level Rules -->
            <div id="res-room-rules"></div>

            <div class="form-control">
              <label>Reservation Type</label>
              <div style="display:flex;gap:8px">
                <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px solid #E5E7EB;border-radius:10px;cursor:pointer;font-size:13px;transition:all 0.2s" class="type-option" data-type="regular">
                  <input type="radio" name="res-type" value="regular" checked style="accent-color:#059669"> Regular
                </label>
                <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px solid #E5E7EB;border-radius:10px;cursor:pointer;font-size:13px;transition:all 0.2s" class="type-option" data-type="vip">
                  <input type="radio" name="res-type" value="vip" style="accent-color:#7C3AED"> ⭐ VIP
                </label>
                <label style="flex:1;display:flex;align-items:center;gap:8px;padding:10px 14px;border:2px solid #E5E7EB;border-radius:10px;cursor:pointer;font-size:13px;transition:all 0.2s" class="type-option" data-type="emergency">
                  <input type="radio" name="res-type" value="emergency" style="accent-color:#DC2626"> ⚡ Important
                </label>
              </div>
            </div>

            <div class="form-control">
              <label>Facility / Room</label>
              <select class="form-input" id="res-facility">
                <!-- Populated dynamically from rooms data -->
              </select>
            </div>

            <div class="form-control">
              <label>Event Title</label>
              <input type="text" class="form-input" id="res-event" placeholder="Enter event title">
            </div>

            <div class="form-control">
              <label>Purpose / Description</label>
              <textarea class="form-input" id="res-purpose" rows="2" placeholder="Describe the meeting purpose, agenda, or objectives"></textarea>
            </div>

            <div class="form-control">
              <label>Priority</label>
              <select class="form-input" id="res-priority">
                <option value="low">Low</option>
                <option value="normal" selected>Normal</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>

            <div class="grid-2">
              <div class="form-control">
                <label>Start Date &amp; Time</label>
                <input type="datetime-local" class="form-input" id="res-start">
              </div>
              <div class="form-control">
                <label>End Date &amp; Time</label>
                <input type="datetime-local" class="form-input" id="res-end">
              </div>
            </div>

            <!-- Time Slot Blocking Indicator -->
            <div id="res-time-slots-indicator"></div>

            <div class="grid-2">
              <div class="form-control">
                <label>Department</label>
                <select class="form-input" id="res-dept">
                  <option>Executive Office</option>
                  <option>Human Resources</option>
                  <option>Credit Department</option>
                  <option>IT Department</option>
                  <option>Operations</option>
                  <option>Board of Directors</option>
                  <option>Finance</option>
                  <option>Marketing</option>
                </select>
              </div>
              <div class="form-control">
                <label>Expected Attendees</label>
                <input type="number" class="form-input" id="res-attendees" placeholder="Number of attendees">
              </div>
            </div>

            <div class="form-control">
              <label>Budget (₱)</label>
              <input type="number" class="form-input" id="res-budget" placeholder="0.00" step="0.01" min="0">
              <span style="font-size:11px;color:#9CA3AF;margin-top:4px;display:block">Enter the allocated budget for this event (catering, materials, etc.)</span>
            </div>

            <div class="form-control">
              <label>Equipment Needed</label>
              <div id="equipment-checkboxes" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px">
                <!-- Populated dynamically from equipment data -->
              </div>
            </div>

            <div class="form-control">
              <label>Special Requests</label>
              <textarea class="form-input" id="res-requests" rows="2" placeholder="Any special requirements (optional)"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-reservation')">Cancel</button>
            <button class="btn btn-primary" id="btn-submit-reservation" onclick="submitReservation()">Submit Reservation</button>
          </div>
        </div>
      </div>

      <!-- ═══════════════════════════════════════════════════ -->
      <!-- MODAL: Event Detail                                 -->
      <!-- ═══════════════════════════════════════════════════ -->
      <div id="modal-day-detail" class="modal-overlay" onclick="if(event.target===this)closeModal('modal-day-detail')">
        <div class="modal" style="max-width:520px">
          <div class="modal-header">
            <span class="modal-title" id="modal-day-title">Schedule Details</span>
            <button class="modal-close" onclick="closeModal('modal-day-detail')">&times;</button>
          </div>
          <div class="modal-body" id="modal-day-body"></div>
          <div class="modal-footer">
            <button class="btn btn-outline" onclick="closeModal('modal-day-detail')">Close</button>
          </div>
        </div>
      </div>

    </main>
  </div>

<script src="../../admin.js?v=20260304"></script>
<script src="../../export.js?v=20260304"></script>
<script>
/* ═══════════════════════════════════════════════════════
   FACILITIES MODULE JAVASCRIPT — All data from API
   ═══════════════════════════════════════════════════════ */

const API = '../../api/facilities.php';
let rooms = [], reservations = [], calendarEvents = [], maintenance = [], stats = {};
let currentUser = { position_level: 1, role: 'staff', position: '', department: '', name: '' };

// Add paginator for rooms
Paginator.create('fac-rooms', { 
  perPage: 9, // Show 9 rooms per page (3x3 grid)
  onPageChange: function() { 
    renderRooms(); 
  },
  alwaysShow: true // Always show pagination controls even with few items
});

// Store filtered rooms globally for pagination
let filteredRooms = [];

// ───── Paginator Init ─────
Paginator.create('fac-reservations', { perPage: 10, onPageChange: function() { renderReservations(); }, alwaysShow: true });
Paginator.create('fac-approved', { perPage: 10, onPageChange: function() { renderApprovedBookings(); }, alwaysShow: true });
Paginator.create('fac-room-logs', { perPage: 10, onPageChange: function() { renderRoomLogs(); }, alwaysShow: true });

// Helper: check if current user has a VIP-eligible role (CEO, owner, president, etc.)
const VIP_ROLES = ['ceo', 'owner', 'president', 'chairman', 'vice_president', 'board_member', 'chief', 'super_admin'];
function isUserVipEligible() {
  const role = (currentUser.role || '').toLowerCase();
  const pos = (currentUser.position || currentUser.job_title || '').toLowerCase();
  return VIP_ROLES.some(r => role.includes(r) || pos.includes(r));
}

const ROOM_LEVEL_LABELS = { 1: 'Normal', 2: 'VIP', 3: 'Important' };
const ROOM_LEVEL_COLORS = { 1: '#3B82F6', 2: '#7C3AED', 3: '#DC2626' };

// Auto-detect room level from facility name
function detectRoomLevel(name) {
  const lower = (name || '').toLowerCase();
  if (lower.includes('executive') || lower.includes('boardroom')) return 3;
  if (lower.includes('training') || lower.includes('transaction') || lower.includes('fleet') || lower.includes('operations')) return 2;
  return 1; // Interview rooms and others default to Level 1
}

const monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const now = new Date();
let calYear = now.getFullYear(), calMonth = now.getMonth();

// ───── Helpers ─────
function formatMoney(v) {
  const n = parseFloat(v) || 0;
  return '₱' + n.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(str) {
  if (!str) return '';
  const d = new Date(str);
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatTime(str) {
  if (!str) return '';
  const d = new Date(str);
  return d.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
}

function formatDateTime(start, end) {
  return formatDate(start) + '<br>' + formatTime(start) + ' – ' + formatTime(end);
}

function escHtml(s) {
  const el = document.createElement('div');
  el.textContent = s || '';
  return el.innerHTML;
}

// ───── Data Loading ─────
async function loadData() {
  try {
    const [roomRes, resRes, maintRes, statsRes, userRes] = await Promise.all([
      fetch(API + '?action=room_status').then(r => r.json()),
      fetch(API + '?action=list_reservations').then(r => r.json()),
      fetch(API + '?action=list_maintenance').then(r => r.json()),
      fetch(API + '?action=dashboard_stats').then(r => r.json()),
      fetch(API + '?action=user_info').then(r => r.json()),
    ]);

    rooms = Array.isArray(roomRes) ? roomRes : (roomRes.data || []);
    reservations = Array.isArray(resRes) ? resRes : (resRes.data || []);
    maintenance = Array.isArray(maintRes) ? maintRes : (maintRes.data || []);
    stats = statsRes.data || statsRes || {};
    currentUser = userRes || { position_level: 1, role: 'staff', department: '', name: '' };

    renderStats();
    renderRooms();
    renderReservations();
    renderApprovedBookings();
    renderMaintenance();
    populateFacilityDropdown();
    populateEquipmentCheckboxes();
    populateMaintenanceFacilityDropdown();
    await loadCalendarEvents();
    loadRoomLogStats(); // background load for stat card
    if (typeof refreshSidebarCounts === 'function') refreshSidebarCounts();
  } catch (err) {
    console.error('Failed to load facilities data:', err);
  }
}

async function loadCalendarEvents() {
  try {
    const res = await fetch(API + '?action=calendar_events&month=' + (calMonth + 1) + '&year=' + calYear);
    const data = await res.json();
    calendarEvents = Array.isArray(data) ? data : (data.data || []);
    renderCalendar();
  } catch (err) {
    console.error('Failed to load calendar events:', err);
    calendarEvents = [];
    renderCalendar();
  }
}

// ───── Render Stats ─────
function renderStats() {
  document.getElementById('stat-total').textContent = stats.total_facilities ?? 0;
  document.getElementById('stat-pending').textContent = stats.pending_reservations ?? 0;

  // Room Utilization — percentage of rooms currently occupied or with upcoming bookings
  const occupiedCount = rooms.filter(r => r.is_currently_occupied > 0 || r.status === 'occupied').length;
  const utilPct = rooms.length ? Math.round(occupiedCount / rooms.length * 100) : 0;
  document.getElementById('stat-utilization').textContent = utilPct + '%';

  // VIP: type=vip and status in (pending, approved)
  const vipCount = reservations.filter(r =>
    r.reservation_type === 'vip' && (r.status === 'pending' || r.status === 'approved')
  ).length;
  document.getElementById('stat-vip').textContent = vipCount;

  // Emergency: type=emergency and status=approved
  const emergencyCount = reservations.filter(r =>
    r.reservation_type === 'emergency' && r.status === 'approved'
  ).length;
  document.getElementById('stat-emergency').textContent = emergencyCount;

  // Malfunction: equipment maintenance tickets that are open or in_progress
  const malfunctionCount = maintenance.filter(m =>
    m.issue_type === 'equipment' && (m.status === 'open' || m.status === 'in_progress')
  ).length;
  document.getElementById('stat-malfunction').textContent = malfunctionCount;
}

// ───── Render Rooms with Pagination ─────
function renderRooms() {
  const grid = document.getElementById('room-grid');
  const paginationContainer = document.getElementById('rooms-pagination');
  const currentFilter = document.querySelector('.room-filter.active')?.dataset?.filter || 'all';
  
  // Filter rooms based on current filter
  filteredRooms = rooms.filter(room => {
    const isOccupied = room.is_currently_occupied == 1 || room.is_currently_occupied === true;
    const isMaint = room.status === 'maintenance';
    const hasUpcoming = (room.has_upcoming > 0) && !isOccupied;
    const displayStatus = isMaint ? 'maintenance' : (isOccupied ? 'occupied' : (hasUpcoming ? 'pending' : 'available'));
    
    if (currentFilter === 'all') return true;
    return displayStatus === currentFilter;
  });

  if (!filteredRooms.length) {
    grid.innerHTML = '<div class="empty-state"><div style="font-size:48px;margin-bottom:12px">🏢</div><div style="font-weight:600;color:#1F2937;margin-bottom:4px">No facilities found</div><div style="font-size:13px;color:#9CA3AF">No rooms match the current filter.</div></div>';
    
    // Still show pagination info with 0 items
    Paginator.setTotalItems('fac-rooms', 0);
    Paginator.renderControls('fac-rooms', 'rooms-pagination');
    return;
  }

  // Get paginated data
  const pagedRooms = Paginator.paginate('fac-rooms', filteredRooms);
  
  // Generate HTML for paginated rooms
  grid.innerHTML = pagedRooms.map(room => {
    const isOccupied = room.is_currently_occupied == 1 || room.is_currently_occupied === true;
    const isMaint = room.status === 'maintenance';
    const hasUpcoming = (room.has_upcoming > 0) && !isOccupied;
    const displayStatus = isMaint ? 'maintenance' : (isOccupied ? 'occupied' : (hasUpcoming ? 'pending' : 'available'));
    const roomLevel = room.room_level ? parseInt(room.room_level) : detectRoomLevel(room.name);
    const roomLevelLabel = ROOM_LEVEL_LABELS[roomLevel] || 'Standard';
    const roomLevelColor = ROOM_LEVEL_COLORS[roomLevel] || '#6B7280';

    const equipList = room.equipment_list
      ? (typeof room.equipment_list === 'string' ? room.equipment_list.split(',') : room.equipment_list)
      : [];
    const equipTags = equipList
      .filter(e => e.trim())
      .map(e => '<span style="background:#F3F4F6;padding:3px 8px;border-radius:6px;font-size:11px;color:#4B5563">' + escHtml(e.trim()) + '</span>')
      .join('');

    const safeName = escHtml(room.name).replace(/'/g, "\\'");
    const safeType = escHtml(room.type || '').replace(/'/g, "\\'");
    const fid = room.facility_id;

    let statusBadge = '', statusInfo = '', buttons = '';

    // Room level badge
    const levelBadge = '<span style="background:' + roomLevelColor + '15;color:' + roomLevelColor + ';padding:2px 8px;border-radius:6px;font-size:10px;font-weight:700;border:1px solid ' + roomLevelColor + '30">Lvl ' + roomLevel + ' · ' + roomLevelLabel + '</span>';

    if (isMaint) {
      statusBadge = '<span class="badge badge-amber" style="font-size:12px">🟡 Maintenance</span>';
      statusInfo = '<div style="margin-top:8px;background:#FEF3C7;padding:8px 12px;border-radius:8px;font-size:12px;color:#92400E"><strong>Under maintenance</strong>' + (room.description ? ' — ' + escHtml(room.description) : '') + '</div>';
      buttons = '<button class="btn btn-outline btn-sm" disabled style="width:100%;opacity:0.5">Unavailable</button>';
    } else if (isOccupied) {
      statusBadge = '<span class="badge badge-red" style="font-size:12px">🔴 Occupied</span>';
      const curEvent = room.current_event || 'In use';
      const untilStr = room.occupied_until ? ' — until ' + formatTime(room.occupied_until) : '';
      statusInfo = '<div style="margin-top:8px;background:#FEE2E2;padding:8px 12px;border-radius:8px;font-size:12px;color:#991B1B"><strong>' + escHtml(curEvent) + '</strong>' + untilStr + '</div>';
      buttons = '<button class="btn btn-outline btn-sm" style="flex:1" onclick="openReservationModal(\'' + safeName + '\',\'' + safeType + '\',' + fid + ')">Schedule Later</button>';
      if (currentUser.position_level >= 3) {
        buttons += '<button class="btn btn-outline btn-sm" onclick="openReservationModal(\'' + safeName + '\',\'' + safeType + '\',' + fid + ',\'emergency\')" title="Important Override">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></button>';
      }
    } else if (hasUpcoming) {
      statusBadge = '<span class="badge badge-amber" style="font-size:12px">⏳ Pending</span>';
      statusInfo = '<div style="margin-top:8px;background:#FEF3C7;padding:8px 12px;border-radius:8px;font-size:12px;color:#92400E"><strong>Upcoming reservation scheduled</strong></div>';
      buttons = '<button class="btn btn-primary btn-sm" style="flex:1" onclick="openReservationModal(\'' + safeName + '\',\'' + safeType + '\',' + fid + ')">Reserve</button>';
      if (currentUser.position_level >= 3) {
        buttons += '<button class="btn btn-outline btn-sm" onclick="openReservationModal(\'' + safeName + '\',\'' + safeType + '\',' + fid + ',\'emergency\')" title="Important Meeting">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></button>';
      }
      if (isUserVipEligible()) {
        buttons += '<button class="btn btn-outline btn-sm" onclick="openReservationModal(\'' + safeName + '\',\'' + safeType + '\',' + fid + ',\'vip\')" title="VIP Reservation">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#7C3AED" stroke="#7C3AED" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></button>';
      }
    } else {
      statusBadge = '<span class="badge badge-green" style="font-size:12px">🟢 Available</span>';
      buttons = '<button class="btn btn-primary btn-sm" style="flex:1" onclick="openReservationModal(\'' + safeName + '\',\'' + safeType + '\',' + fid + ')">Reserve</button>';
      if (currentUser.position_level >= 3) {
        buttons += '<button class="btn btn-outline btn-sm" onclick="openReservationModal(\'' + safeName + '\',\'' + safeType + '\',' + fid + ',\'emergency\')" title="Important Meeting">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></button>';
      }
      if (isUserVipEligible()) {
        buttons += '<button class="btn btn-outline btn-sm" onclick="openReservationModal(\'' + safeName + '\',\'' + safeType + '\',' + fid + ',\'vip\')" title="VIP Reservation">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#7C3AED" stroke="#7C3AED" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></button>';
      }
    }

    const todayBookings = room.today_bookings ?? 0;
    const availabilityIndicator = isMaint
      ? '<div style="margin-top:10px;display:flex;align-items:center;gap:6px;font-size:11px;color:#92400E"><span style="width:8px;height:8px;border-radius:50%;background:#F59E0B;display:inline-block"></span>Under maintenance — unavailable for booking</div>'
      : isOccupied
        ? '<div style="margin-top:10px;display:flex;align-items:center;gap:6px;font-size:11px;color:#991B1B"><span style="width:8px;height:8px;border-radius:50%;background:#EF4444;display:inline-block;animation:pulse-dot 2s infinite"></span>Currently occupied' + (room.occupied_until ? ' · Available after ' + formatTime(room.occupied_until) : '') + '</div>'
        : hasUpcoming
          ? '<div style="margin-top:10px;display:flex;align-items:center;gap:6px;font-size:11px;color:#92400E"><span style="width:8px;height:8px;border-radius:50%;background:#F59E0B;display:inline-block;animation:pulse-dot 2s infinite"></span>Pending — approved reservation upcoming · ' + todayBookings + ' booking(s) today</div>'
          : '<div style="margin-top:10px;display:flex;align-items:center;gap:6px;font-size:11px;color:#065F46"><span style="width:8px;height:8px;border-radius:50%;background:#059669;display:inline-block;animation:pulse-dot 2s infinite"></span>Available now · ' + todayBookings + ' booking(s) today</div>';

    return '<div class="card room-card" data-status="' + displayStatus + '" style="margin-bottom:0;border-top:3px solid ' + roomLevelColor + '">'
      + '<div class="card-body padded">'
      + '<div style="display:flex;justify-content:space-between;align-items:flex-start">'
        + '<div>'
          + '<div style="font-size:15px;font-weight:700;color:#1F2937">' + escHtml(room.name) + '</div>'
          + '<div style="font-size:12px;color:#9CA3AF;margin-top:2px">' + (room.floor ? escHtml(room.floor) + ' · ' : '') + (room.capacity ? room.capacity + ' pax' : '') + '</div>'
        + '</div>'
        + '<div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">'
        + statusBadge
        + levelBadge
        + '</div>'
      + '</div>'
      + statusInfo
      + (equipTags ? '<div style="margin-top:14px;display:flex;gap:6px;flex-wrap:wrap">' + equipTags + '</div>' : '')
      + '<div style="margin-top:14px;display:flex;gap:8px">' + buttons + '</div>'
      + availabilityIndicator
      + '</div></div>';
  }).join('');

  // Render pagination controls (always visible)
  Paginator.renderControls('fac-rooms', 'rooms-pagination');
  
  // Update filter button count badges
  updateFilterCounts();
}

// ───── Update Filter Count Badges ─────
function updateFilterCounts() {
  const filters = ['all', 'available', 'occupied', 'pending', 'maintenance'];
  
  filters.forEach(filter => {
    let count = 0;
    
    if (filter === 'all') {
      count = rooms.length;
    } else {
      count = rooms.filter(room => {
        const isOccupied = room.is_currently_occupied == 1 || room.is_currently_occupied === true;
        const isMaint = room.status === 'maintenance';
        const hasUpcoming = (room.has_upcoming > 0) && !isOccupied;
        const displayStatus = isMaint ? 'maintenance' : (isOccupied ? 'occupied' : (hasUpcoming ? 'pending' : 'available'));
        return displayStatus === filter;
      }).length;
    }
    
    const btn = document.querySelector(`.room-filter[data-filter="${filter}"]`);
    if (btn) {
      const filterName = filter === 'all' ? 'All Rooms' : filter.charAt(0).toUpperCase() + filter.slice(1);
      btn.innerHTML = filterName + ` <span class="count-badge">${count}</span>`;
    }
  });
}

// ───── Room Filter ─────
function filterRooms(status, btn) {
  document.querySelectorAll('.room-filter').forEach(b => {
    b.classList.remove('active');
    b.style.background = '';
    b.style.color = '';
    b.style.borderColor = '';
  });
  
  if (btn) {
    btn.classList.add('active');
    btn.style.background = '#059669';
    btn.style.color = '#fff';
    btn.style.borderColor = '#059669';
  }
  
  // Reset to first page when filter changes
  Paginator.reset('fac-rooms');
  renderRooms();
}

// ───── Render Reservations ─────
function renderReservations() {
  const tbody = document.getElementById('reservations-tbody');
  const typeFilter = document.getElementById('res-filter-type').value;
  const statusFilter = document.getElementById('res-filter-status').value;

  let filtered = reservations;
  // Exclude completed/rejected/cancelled by default (remove history)
  if (!statusFilter) {
    filtered = filtered.filter(r => r.status === 'pending' || r.status === 'approved' || r.status === 'ongoing');
  }
  if (typeFilter) filtered = filtered.filter(r => r.reservation_type === typeFilter);
  if (statusFilter) filtered = filtered.filter(r => r.status === statusFilter);

  if (!filtered.length) {
    tbody.innerHTML = '<tr><td colspan="12" class="empty-state" style="padding:40px"><div style="font-size:36px;margin-bottom:8px">📋</div><div style="font-weight:600;color:#1F2937">No reservations found</div><div style="font-size:13px;color:#9CA3AF;margin-top:4px">Adjust filters or create a new reservation.</div></td></tr>';
    Paginator.setTotalItems('fac-reservations', 0);
    Paginator.renderControls('fac-reservations', 'reservations-pagination');
    return;
  }

  var allRows = filtered.map(r => {
    // Type badge with color coding
    const typeBadges = {
      vip: '<span class="badge badge-purple">⭐ VIP</span>',
      emergency: '<span class="badge badge-red">⚡ Important</span>',
      regular: '<span class="badge badge-gray">Regular</span>',
    };
    const typeBadge = typeBadges[r.reservation_type] || typeBadges.regular;

    // Room level badge
    const roomLevel = r.room_level ? parseInt(r.room_level) : detectRoomLevel(r.facility_name || '');
    const roomLevelLabel = ROOM_LEVEL_LABELS[roomLevel] || 'Standard';
    const roomLevelColor = ROOM_LEVEL_COLORS[roomLevel] || '#6B7280';
    const levelBadge = '<span style="background:' + roomLevelColor + '15;color:' + roomLevelColor + ';padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;border:1px solid ' + roomLevelColor + '30">Lvl ' + roomLevel + '</span>';

    // Status badge
    const statusBadges = {
      pending: '<span class="badge badge-amber">Pending</span>',
      approved: '<span class="badge badge-green">Approved</span>',
      ongoing: '<span class="badge" style="background:#DBEAFE;color:#1D4ED8;border:1px solid #93C5FD">🔄 Ongoing</span>',
      rejected: '<span class="badge badge-red">Rejected</span>',
      completed: '<span class="badge badge-blue">Completed</span>',
      cancelled: '<span class="badge badge-gray">Cancelled</span>',
    };
    const statusBadge = statusBadges[r.status] || '<span class="badge badge-gray">' + escHtml(r.status) + '</span>';

    // Auto-tagged indicator
    const autoTagBadge = r.is_auto_tagged == 1 ? '<span style="font-size:9px;color:#7C3AED;display:block">auto-tagged</span>' : '';

    // Validated
    const validatedBadge = (r.is_validated == 1 || r.validated_by)
      ? '<span class="badge badge-green">✓ Yes</span>'
      : '<span class="badge badge-gray">✗ No</span>';

    // Equipment
    const equipRaw = r.equipment_needed || '';
    const equipDisplay = typeof equipRaw === 'string' ? equipRaw : (Array.isArray(equipRaw) ? equipRaw.join(', ') : '');

    // Actions - with Cancel/Reschedule based on room level
    let actions = '';
    if (r.status === 'pending') {
      actions = '<div style="display:flex;gap:4px;flex-wrap:wrap">'
        + '<button class="btn btn-primary btn-sm" title="Approve" onclick="approveReservation(' + r.reservation_id + ')">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></button>'
        + '<button class="btn btn-outline btn-sm" title="Validate" onclick="validateReservation(' + r.reservation_id + ')">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></button>';

      // Cancel button for pending - always available
      actions += '<button class="btn btn-outline btn-sm" title="Cancel" onclick="cancelReservation(' + r.reservation_id + ',' + roomLevel + ',\'' + (r.reservation_type || 'regular') + '\')" style="color:#DC2626;border-color:#FECACA">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';

      // Reschedule button for pending
      actions += '<button class="btn btn-outline btn-sm" title="Reschedule" onclick="rescheduleReservation(' + r.reservation_id + ',' + roomLevel + ',\'' + (r.reservation_type || 'regular') + '\')" style="color:#2563EB;border-color:#BFDBFE">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg></button>';

      actions += '</div>';
    } else if (r.status === 'approved') {
      const evJson = JSON.stringify(r).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
      actions = '<div style="display:flex;gap:4px;flex-wrap:wrap">'
        + '<button class="btn btn-outline btn-sm" title="View" onclick="showEventDetail(JSON.parse(this.dataset.ev))" data-ev="' + evJson + '">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>'
        + '<button class="btn btn-sm" title="Mark Ongoing" onclick="markOngoingReservation(' + r.reservation_id + ')" style="background:#DBEAFE;color:#1D4ED8;border:1px solid #93C5FD">🔄 Ongoing</button>';

      // Cancel button - Room Level 2 VIP: disabled; Room Level 3 Important: admin only
      const isVipLvl2 = roomLevel === 2 && r.reservation_type === 'vip';
      const isLvl3Emergency = roomLevel === 3;
      if (isVipLvl2) {
        actions += '<button class="btn btn-outline btn-sm" disabled title="VIP Level 2: Cannot cancel" style="opacity:0.3;cursor:not-allowed;color:#DC2626;border-color:#FECACA">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
      } else if (isLvl3Emergency && currentUser.role !== 'super_admin' && currentUser.role !== 'admin') {
        actions += '<button class="btn btn-outline btn-sm" disabled title="Important Lvl 3: Admin only" style="opacity:0.3;cursor:not-allowed;color:#DC2626;border-color:#FECACA">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
      } else {
        actions += '<button class="btn btn-outline btn-sm" title="Cancel" onclick="cancelReservation(' + r.reservation_id + ',' + roomLevel + ',\'' + (r.reservation_type || 'regular') + '\')" style="color:#DC2626;border-color:#FECACA">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>';
      }

      // Reschedule button - Room Level 2 VIP: disabled
      if (isVipLvl2) {
        actions += '<button class="btn btn-outline btn-sm" disabled title="VIP Level 2: Cannot reschedule" style="opacity:0.3;cursor:not-allowed;color:#2563EB;border-color:#BFDBFE">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg></button>';
      } else {
        actions += '<button class="btn btn-outline btn-sm" title="Reschedule" onclick="rescheduleReservation(' + r.reservation_id + ',' + roomLevel + ',\'' + (r.reservation_type || 'regular') + '\')" style="color:#2563EB;border-color:#BFDBFE">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg></button>';
      }

      actions += '</div>';
    } else if (r.status === 'ongoing') {
      const evJson = JSON.stringify(r).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
      actions = '<div style="display:flex;gap:4px;flex-wrap:wrap">'
        + '<button class="btn btn-outline btn-sm" title="View" onclick="showEventDetail(JSON.parse(this.dataset.ev))" data-ev="' + evJson + '">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>'
        + '<button class="btn btn-primary btn-sm" title="Mark Complete" onclick="completeReservation(' + r.reservation_id + ')" style="background:#059669;border-color:#059669">✓ Complete</button>'
        + '<button class="btn btn-outline btn-sm" title="Cancel" onclick="cancelReservation(' + r.reservation_id + ',' + roomLevel + ',\'' + (r.reservation_type || 'regular') + '\')" style="color:#DC2626;border-color:#FECACA">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>'
        + '</div>';
    } else {
      const evJson = JSON.stringify(r).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
      actions = '<button class="btn btn-outline btn-sm" title="View" onclick="showEventDetail(JSON.parse(this.dataset.ev))" data-ev="' + evJson + '">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>';
    }

    return '<tr data-type="' + (r.reservation_type || 'regular') + '" data-status="' + (r.status || '') + '">'
      + '<td style="font-weight:600;font-size:12px">' + escHtml(r.reservation_code || '') + '</td>'
      + '<td style="font-weight:600">' + escHtml(r.facility_name || '') + '<br>' + levelBadge + '</td>'
      + '<td>' + escHtml(r.event_title || '') + (r.purpose ? '<br><span style="font-size:11px;color:#9CA3AF">' + escHtml(r.purpose) + '</span>' : '') + '</td>'
      + '<td>' + typeBadge + autoTagBadge + '</td>'
      + '<td style="font-size:12px">' + formatDateTime(r.start_datetime, r.end_datetime) + '</td>'
      + '<td>' + formatMoney(r.budget) + '</td>'
      + '<td><span style="font-size:11px">' + escHtml(equipDisplay || '—') + '</span></td>'
      + '<td>' + escHtml(r.department || '') + '</td>'
      + '<td>' + statusBadge + '</td>'
      + '<td>' + validatedBadge + '</td>'
      + '<td>' + actions + '</td>'
      + '</tr>';
  });

  var paged = Paginator.paginate('fac-reservations', allRows);
  tbody.innerHTML = paged.join('');
  Paginator.renderControls('fac-reservations', 'reservations-pagination');
}

function filterReservations() {
  Paginator.reset('fac-reservations');
  renderReservations();
}

// ───── Render Maintenance ─────
function renderMaintenance() {
  const body = document.getElementById('maintenance-body');
  const statusFilter = document.getElementById('maint-filter-status')?.value || '';
  const typeFilter = document.getElementById('maint-filter-type')?.value || '';
  const priorityFilter = document.getElementById('maint-filter-priority')?.value || '';

  // Update maintenance stat cards
  const openCount = maintenance.filter(m => m.status === 'open').length;
  const progressCount = maintenance.filter(m => m.status === 'in_progress').length;
  const equipCount = maintenance.filter(m => m.issue_type === 'equipment' && (m.status === 'open' || m.status === 'in_progress')).length;
  const resolvedCount = maintenance.filter(m => m.status === 'resolved' || m.status === 'closed').length;

  const el = (id) => document.getElementById(id);
  if (el('stat-maint-open')) el('stat-maint-open').textContent = openCount;
  if (el('stat-maint-progress')) el('stat-maint-progress').textContent = progressCount;
  if (el('stat-maint-equipment')) el('stat-maint-equipment').textContent = equipCount;
  if (el('stat-maint-resolved')) el('stat-maint-resolved').textContent = resolvedCount;

  let filtered = maintenance;
  if (statusFilter) filtered = filtered.filter(m => m.status === statusFilter);
  if (typeFilter) filtered = filtered.filter(m => m.issue_type === typeFilter);
  if (priorityFilter) filtered = filtered.filter(m => m.priority === priorityFilter);

  if (!filtered.length) {
    body.innerHTML = '<div class="empty-state"><div style="font-size:48px;margin-bottom:12px">🔧</div>'
      + '<div style="font-weight:600;color:#1F2937;margin-bottom:4px">No maintenance requests found</div>'
      + '<div style="font-size:13px;color:#9CA3AF">All facilities and equipment are operational. Click "Report Malfunction" to submit a new request.</div></div>';
    return;
  }

  const priBadge = (p) => {
    const pl = (p || '').toLowerCase();
    if (pl === 'critical') return '<span class="badge badge-red">🔴 Critical</span>';
    if (pl === 'high') return '<span class="badge badge-red">🟠 High</span>';
    if (pl === 'medium') return '<span class="badge badge-amber">🟡 Medium</span>';
    if (pl === 'low') return '<span class="badge badge-green">🟢 Low</span>';
    return '<span class="badge badge-gray">' + escHtml(p) + '</span>';
  };

  const stBadge = (s) => {
    const sl = (s || '').toLowerCase();
    if (sl === 'open') return '<span class="badge badge-red">Open</span>';
    if (sl === 'in_progress') return '<span class="badge badge-blue">In Progress</span>';
    if (sl === 'resolved') return '<span class="badge badge-green">Resolved</span>';
    if (sl === 'closed') return '<span class="badge badge-gray">Closed</span>';
    return '<span class="badge badge-gray">' + escHtml(s) + '</span>';
  };

  const issueIcon = (t) => {
    const icons = { equipment: '🔧', electrical: '⚡', plumbing: '🚿', hvac: '❄️', structural: '🏗️', cleaning: '🧹' };
    return (icons[t] || '📋') + ' ' + escHtml((t || 'other').replace(/_/g, ' '));
  };

  body.innerHTML = '<table class="data-table"><thead><tr>'
    + '<th>Ticket</th><th>Facility</th><th>Equipment</th><th>Issue Type</th><th>Description</th><th>Priority</th><th>Status</th><th>Assigned To</th><th>Reported By</th><th>Date</th><th>Actions</th>'
    + '</tr></thead><tbody>'
    + filtered.map(m => {
      const mId = m.maintenance_id;
      let actions = '';
      if (m.status === 'open') {
        actions = '<div style="display:flex;gap:4px">'
          + '<button class="btn btn-primary btn-sm" title="Start Work" onclick="updateMaintenanceStatus(' + mId + ',\'in_progress\')">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg></button>'
          + '<button class="btn btn-outline btn-sm" title="View Details" onclick="showMaintenanceDetail(' + mId + ')">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>'
          + '</div>';
      } else if (m.status === 'in_progress') {
        actions = '<div style="display:flex;gap:4px">'
          + '<button class="btn btn-primary btn-sm" title="Mark Resolved" onclick="resolveMaintenancePrompt(' + mId + ')">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></button>'
          + '<button class="btn btn-outline btn-sm" title="View Details" onclick="showMaintenanceDetail(' + mId + ')">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>'
          + '</div>';
      } else {
        actions = '<button class="btn btn-outline btn-sm" title="View Details" onclick="showMaintenanceDetail(' + mId + ')">'
          + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>';
      }

      return '<tr>'
        + '<td style="font-weight:600;font-size:12px">' + escHtml(m.ticket_number || '') + '</td>'
        + '<td style="font-weight:600">' + escHtml(m.facility_name || '') + '</td>'
        + '<td>' + (m.equipment_name ? '<span style="color:#991B1B;font-weight:600">⚠️ ' + escHtml(m.equipment_name) + '</span>' : '<span style="color:#9CA3AF">—</span>') + '</td>'
        + '<td style="font-size:12px">' + issueIcon(m.issue_type) + '</td>'
        + '<td style="max-width:200px"><div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="' + escHtml(m.description || '') + '">' + escHtml(m.description || '') + '</div></td>'
        + '<td>' + priBadge(m.priority) + '</td>'
        + '<td>' + stBadge(m.status) + '</td>'
        + '<td>' + escHtml(m.assigned_to || '—') + '</td>'
        + '<td style="font-size:12px">' + escHtml(m.reported_by_name || '') + '</td>'
        + '<td style="font-size:12px">' + formatDate(m.created_at || '') + '</td>'
        + '<td>' + actions + '</td>'
        + '</tr>';
    }).join('')
    + '</tbody></table>';
}

// ───── Maintenance Modal Helpers ─────
function toggleEquipmentField() {
  const issueType = document.getElementById('maint-issue-type').value;
  const equipGroup = document.getElementById('maint-equipment-group');
  const offlineGroup = document.getElementById('maint-facility-offline-group');
  equipGroup.style.display = issueType === 'equipment' ? '' : 'none';

  // Show "take offline" option for critical/high priority or equipment issues
  const priority = document.getElementById('maint-priority').value;
  offlineGroup.style.display = (priority === 'critical' || priority === 'high') ? '' : 'none';
}

async function loadFacilityEquipment() {
  const facId = document.getElementById('maint-facility').value;
  const equipSel = document.getElementById('maint-equipment');
  try {
    const res = await fetch(API + '?action=list_equipment').then(r => r.json());
    const equip = Array.isArray(res) ? res : (res.data || []);
    const facEquip = equip.filter(e => String(e.facility_id) === String(facId) || !e.facility_id);
    equipSel.innerHTML = '<option value="">— Select equipment —</option>'
      + facEquip.map(e => '<option value="' + e.equipment_id + '">' + escHtml(e.name || e.equipment_name || '') + ' (' + escHtml(e.condition_status || 'unknown') + ')</option>').join('');
  } catch { equipSel.innerHTML = '<option value="">— No equipment —</option>'; }
}

function populateMaintenanceFacilityDropdown() {
  const sel = document.getElementById('maint-facility');
  if (!sel) return;
  sel.innerHTML = rooms.map(r => '<option value="' + r.facility_id + '">' + escHtml(r.name) + '</option>').join('');
  loadFacilityEquipment();
}

function openMaintenanceModal() {
  // Reset form
  document.getElementById('maint-issue-type').value = 'equipment';
  document.getElementById('maint-priority').value = 'medium';
  document.getElementById('maint-description').value = '';
  document.getElementById('maint-assigned').value = '';
  document.getElementById('maint-set-offline').checked = false;
  document.getElementById('modal-maint-title').textContent = '🔧 Report Equipment Malfunction';

  populateMaintenanceFacilityDropdown();
  toggleEquipmentField();
  openModal('modal-maintenance');
}

// Listen for priority change to toggle offline option
document.getElementById('maint-priority')?.addEventListener('change', function() {
  const offlineGroup = document.getElementById('maint-facility-offline-group');
  offlineGroup.style.display = (this.value === 'critical' || this.value === 'high') ? '' : 'none';
});

async function submitMaintenance() {
  const issueType = document.getElementById('maint-issue-type').value;
  const facilityId = document.getElementById('maint-facility').value;
  const equipmentId = document.getElementById('maint-equipment').value;
  const priority = document.getElementById('maint-priority').value;
  const description = document.getElementById('maint-description').value.trim();
  const assignedTo = document.getElementById('maint-assigned').value.trim();
  const setOffline = document.getElementById('maint-set-offline').checked;

  if (!description) {
    return Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please describe the issue in detail.', confirmButtonColor: '#059669' });
  }

  if (issueType === 'equipment' && !equipmentId) {
    return Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please select the malfunctioning equipment.', confirmButtonColor: '#059669' });
  }

  const priorityLabels = { low: '🟢 Low', medium: '🟡 Medium', high: '🟠 High', critical: '🔴 Critical' };
  const confirmResult = await Swal.fire({
    title: 'Submit Maintenance Report?',
    html: '<div style="text-align:left;font-size:13px;line-height:1.8">'
      + '<b>Type:</b> ' + escHtml(issueType.replace(/_/g, ' ')) + '<br>'
      + '<b>Priority:</b> ' + (priorityLabels[priority] || priority) + '<br>'
      + (equipmentId ? '<b>Equipment:</b> Will be marked as <span style="color:#DC2626;font-weight:700">Needs Repair</span><br>' : '')
      + (setOffline ? '<b>⚠️ Facility will be taken OFFLINE</b><br>' : '')
      + '</div>',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Submit Report',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#DC2626',
    cancelButtonColor: '#6B7280'
  });
  if (!confirmResult.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=create_maintenance', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        facility_id: facilityId,
        equipment_id: equipmentId || null,
        issue_type: issueType,
        priority: priority,
        description: description,
        assigned_to: assignedTo || null,
        set_facility_maintenance: setOffline
      })
    });
    const result = await res.json();

    if (result.success) {
      await Swal.fire({
        icon: 'success',
        title: 'Maintenance Report Submitted!',
        html: '<div style="text-align:left;font-size:14px;line-height:1.8">'
          + '<b>Ticket:</b> ' + escHtml(result.ticket_number || 'N/A') + '<br>'
          + '<b>Status:</b> Open — awaiting action<br>'
          + (equipmentId ? '<b>Equipment:</b> Marked as needs repair (taken offline)<br>' : '')
          + (setOffline ? '<b>Facility:</b> Set to maintenance mode<br>' : '')
          + '</div>',
        confirmButtonColor: '#059669'
      });
      closeModal('modal-maintenance');
      await loadData();
      // Switch to maintenance tab
      switchSection('tab-maintenance', document.querySelector('[data-section="tab-maintenance"]'));
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to submit report.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#059669' });
  }
}

// ───── Update Maintenance Status ─────
async function updateMaintenanceStatus(id, newStatus) {
  const labels = { in_progress: 'Start Work', resolved: 'Mark as Resolved', closed: 'Close Ticket' };
  const confirmResult = await Swal.fire({
    title: labels[newStatus] || 'Update Status',
    text: newStatus === 'in_progress'
      ? 'This will mark the ticket as In Progress. The maintenance team is now working on this issue.'
      : 'Update the maintenance ticket status.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, ' + (labels[newStatus] || 'Update'),
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#059669',
    cancelButtonColor: '#6B7280'
  });
  if (!confirmResult.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=update_maintenance', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ maintenance_id: id, status: newStatus })
    });
    const result = await res.json();

    if (result.success) {
      await Swal.fire({ icon: 'success', title: 'Updated!', text: 'Maintenance ticket status updated.', confirmButtonColor: '#059669' });
      await loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to update.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error.', confirmButtonColor: '#059669' });
  }
}

// ───── Resolve Maintenance with Notes ─────
async function resolveMaintenancePrompt(id) {
  const { value: notes } = await Swal.fire({
    title: '✅ Resolve Maintenance Ticket',
    html: '<div style="text-align:left;font-size:13px;margin-bottom:12px;color:#4B5563">Provide resolution details. The equipment will be restored to service and the facility will be set back to available.</div>',
    input: 'textarea',
    inputPlaceholder: 'Describe what was done to fix the issue...',
    inputAttributes: { rows: 4 },
    showCancelButton: true,
    confirmButtonText: '✅ Mark as Resolved',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#059669',
    cancelButtonColor: '#6B7280',
    inputValidator: (value) => {
      if (!value.trim()) return 'Please provide resolution notes.';
    }
  });

  if (!notes) return;

  try {
    const res = await fetch(API + '?action=update_maintenance', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ maintenance_id: id, status: 'resolved', resolution_notes: notes })
    });
    const result = await res.json();

    if (result.success) {
      await Swal.fire({
        icon: 'success',
        title: 'Resolved!',
        html: 'Maintenance ticket resolved.<br>Equipment restored to service. Facility set to available.',
        confirmButtonColor: '#059669'
      });
      await loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to resolve.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error.', confirmButtonColor: '#059669' });
  }
}

// ───── Show Maintenance Detail ─────
function showMaintenanceDetail(id) {
  const m = maintenance.find(x => x.maintenance_id == id);
  if (!m) return;

  const issueIcons = { equipment: '🔧', electrical: '⚡', plumbing: '🚿', hvac: '❄️', structural: '🏗️', cleaning: '🧹', other: '📋' };
  const priBg = { critical: '#FEE2E2', high: '#FFEDD5', medium: '#FEF3C7', low: '#D1FAE5' };
  const priColor = { critical: '#991B1B', high: '#9A3412', medium: '#92400E', low: '#065F46' };
  const stBg = { open: '#FEE2E2', in_progress: '#DBEAFE', resolved: '#D1FAE5', closed: '#F3F4F6' };
  const stColor = { open: '#991B1B', in_progress: '#1E40AF', resolved: '#065F46', closed: '#374151' };

  document.getElementById('modal-maint-detail-title').textContent = '🔧 ' + (m.ticket_number || 'Maintenance Detail');

  let html = '<div>'
    + '<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">'
      + '<span style="background:' + (priBg[m.priority] || '#F3F4F6') + ';color:' + (priColor[m.priority] || '#374151') + ';padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700">'
        + (m.priority || 'medium').toUpperCase() + ' PRIORITY</span>'
      + '<span style="background:' + (stBg[m.status] || '#F3F4F6') + ';color:' + (stColor[m.status] || '#374151') + ';padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700">'
        + (m.status || '').replace(/_/g, ' ').toUpperCase() + '</span>'
    + '</div>'
    + '<table style="width:100%;font-size:13px;border-collapse:collapse">'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280;width:140px">🎫 Ticket</td><td style="padding:8px 0;color:#1F2937;font-weight:600">' + escHtml(m.ticket_number || '') + '</td></tr>'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">📍 Facility</td><td style="padding:8px 0;color:#1F2937">' + escHtml(m.facility_name || '') + '</td></tr>'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">' + (issueIcons[m.issue_type] || '📋') + ' Issue Type</td><td style="padding:8px 0;color:#1F2937">' + escHtml((m.issue_type || '').replace(/_/g, ' ')) + '</td></tr>';

  if (m.equipment_name) {
    html += '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">🔧 Equipment</td><td style="padding:8px 0;color:#DC2626;font-weight:600">⚠️ ' + escHtml(m.equipment_name) + ' (Malfunction)</td></tr>';
  }

  html += '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280;vertical-align:top">📝 Description</td><td style="padding:8px 0;color:#1F2937">' + escHtml(m.description || '') + '</td></tr>'
    + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">👤 Reported By</td><td style="padding:8px 0;color:#1F2937">' + escHtml(m.reported_by_name || '') + '</td></tr>'
    + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">🛠️ Assigned To</td><td style="padding:8px 0;color:#1F2937">' + escHtml(m.assigned_to || 'Unassigned') + '</td></tr>'
    + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">📅 Reported</td><td style="padding:8px 0;color:#1F2937">' + formatDate(m.created_at || '') + '</td></tr>';

  if (m.resolved_at) {
    html += '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">✅ Resolved</td><td style="padding:8px 0;color:#065F46">' + formatDate(m.resolved_at) + '</td></tr>';
  }
  if (m.resolution_notes) {
    html += '<tr><td style="padding:8px 0;font-weight:600;color:#6B7280;vertical-align:top">📋 Resolution</td><td style="padding:8px 0;color:#065F46;font-weight:500">' + escHtml(m.resolution_notes) + '</td></tr>';
  }

  html += '</table></div>';

  document.getElementById('modal-maint-detail-body').innerHTML = html;

  // Update footer actions based on status
  const footer = document.getElementById('modal-maint-detail-footer');
  let footerHtml = '<button class="btn btn-outline" onclick="closeModal(\'modal-maint-detail\')">Close</button>';
  if (m.status === 'open') {
    footerHtml += ' <button class="btn btn-primary" onclick="closeModal(\'modal-maint-detail\');updateMaintenanceStatus(' + m.maintenance_id + ',\'in_progress\')">▶ Start Work</button>';
  } else if (m.status === 'in_progress') {
    footerHtml += ' <button class="btn btn-primary" onclick="closeModal(\'modal-maint-detail\');resolveMaintenancePrompt(' + m.maintenance_id + ')">✅ Mark Resolved</button>';
  }
  footer.innerHTML = footerHtml;

  openModal('modal-maint-detail');
}

// ───── Calendar ─────
function renderCalendar() {
  const grid = document.getElementById('calendar-grid');
  while (grid.children.length > 7) grid.removeChild(grid.lastChild);

  document.getElementById('cal-month-label').textContent = '📅 ' + monthNames[calMonth] + ' ' + calYear;

  const firstDay = new Date(calYear, calMonth, 1).getDay();
  const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();
  const todayStr = new Date().toISOString().split('T')[0];

  for (let i = 0; i < firstDay; i++) {
    const cell = document.createElement('div');
    cell.className = 'cal-day';
    cell.style.color = '#D1D5DB';
    grid.appendChild(cell);
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const dateStr = calYear + '-' + String(calMonth + 1).padStart(2, '0') + '-' + String(d).padStart(2, '0');
    const dayEvents = calendarEvents.filter(e => {
      const eDate = e.date || (e.start_datetime ? e.start_datetime.split(' ')[0] : '') || '';
      return eDate === dateStr;
    });

    const cell = document.createElement('div');
    cell.className = 'cal-day' + (dayEvents.length ? ' has-event' : '');

    if (dateStr === todayStr) {
      cell.style.background = '#D1FAE5';
      cell.style.fontWeight = '800';
      cell.style.borderRadius = '10px';
    }

    cell.innerHTML = d;

    if (dayEvents.length) {
      let dots = '';
      dayEvents.forEach(ev => {
        const t = ev.reservation_type || ev.type || 'regular';
        const s = ev.status || '';
        const color = t === 'vip' ? '#7C3AED' : t === 'emergency' ? '#DC2626' : s === 'approved' ? '#059669' : '#F59E0B';
        dots += '<span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:' + color + ';margin:0 1px"></span>';
      });
      cell.innerHTML += '<div style="margin-top:4px;display:flex;justify-content:center;gap:2px">' + dots + '</div>';

      const tip = dayEvents.map(e => {
        const tStr = e.time || (formatTime(e.start_datetime) + ' – ' + formatTime(e.end_datetime));
        return tStr + ' — ' + (e.event || e.event_title || '');
      }).join('\n');
      cell.title = tip;
      cell.style.cursor = 'pointer';
      cell.addEventListener('click', () => showDaySchedule(dateStr, dayEvents));
    }

    grid.appendChild(cell);
  }
}

function changeMonth(dir) {
  calMonth += dir;
  if (calMonth > 11) { calMonth = 0; calYear++; }
  if (calMonth < 0) { calMonth = 11; calYear--; }
  document.getElementById('day-schedule-card').style.display = 'none';
  loadCalendarEvents();
}

function goToToday() {
  const n = new Date();
  calYear = n.getFullYear();
  calMonth = n.getMonth();
  loadCalendarEvents();
}

function showDaySchedule(dateStr, events) {
  const card = document.getElementById('day-schedule-card');
  const title = document.getElementById('day-schedule-title');
  const body = document.getElementById('day-schedule-body');

  const dateLabel = new Date(dateStr + 'T00:00:00').toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
  title.textContent = '📋 ' + dateLabel;

  let html = '<div style="padding:16px">';
  if (!events.length) {
    html += '<div class="empty-state" style="padding:24px"><div style="font-size:36px;margin-bottom:8px">📭</div><div style="font-weight:600">No schedules for this day</div></div>';
  } else {
    events.forEach(ev => {
      const t = ev.reservation_type || ev.type || 'regular';
      const typeColor = t === 'vip' ? '#7C3AED' : t === 'emergency' ? '#DC2626' : '#059669';
      const typeBg = t === 'vip' ? '#EDE9FE' : t === 'emergency' ? '#FEE2E2' : '#D1FAE5';
      const typeLabel = t === 'vip' ? '⭐ VIP' : t === 'emergency' ? '⚡ Important' : 'Regular';
      const st = ev.status || '';
      const statusBadge = st === 'approved' ? '<span class="badge badge-green">Approved</span>'
        : st === 'rejected' ? '<span class="badge badge-red">Rejected</span>'
        : '<span class="badge badge-amber">Pending</span>';
      const evTitle = ev.event || ev.event_title || '';
      const timeStr = ev.time || (formatTime(ev.start_datetime) + ' – ' + formatTime(ev.end_datetime));
      const facility = ev.facility || ev.facility_name || '';
      const dept = ev.dept || ev.department || '';
      const budget = ev.budget ?? 0;
      const equipStr = ev.equip || ev.equipment_needed || '';
      const equipDisplay = Array.isArray(equipStr) ? equipStr.join(', ') : (equipStr || '');

      const evJson = JSON.stringify(ev).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');

      html += '<div style="border:1px solid #E5E7EB;border-radius:12px;padding:16px;margin-bottom:12px;border-left:4px solid ' + typeColor + ';cursor:pointer;transition:all 0.2s"'
        + ' onmouseover="this.style.boxShadow=\'0 4px 12px rgba(0,0,0,0.08)\'" onmouseout="this.style.boxShadow=\'none\'"'
        + ' onclick="showEventDetail(JSON.parse(this.dataset.ev))" data-ev="' + evJson + '">'
        + '<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px">'
          + '<div>'
            + '<div style="font-weight:700;font-size:14px;color:#1F2937">' + escHtml(evTitle) + '</div>'
            + '<div style="font-size:12px;color:#6B7280;margin-top:2px">🕐 ' + escHtml(timeStr) + '</div>'
          + '</div>'
          + '<div style="display:flex;gap:6px;align-items:center">'
            + '<span style="background:' + typeBg + ';color:' + typeColor + ';padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600">' + typeLabel + '</span>'
            + statusBadge
          + '</div>'
        + '</div>'
        + '<div style="display:flex;gap:16px;font-size:12px;color:#6B7280;flex-wrap:wrap">'
          + '<span>📍 ' + escHtml(facility) + '</span>'
          + '<span>🏢 ' + escHtml(dept) + '</span>'
          + '<span>💰 ' + formatMoney(budget) + '</span>'
        + '</div>'
        + (equipDisplay ? '<div style="font-size:12px;color:#6B7280;margin-top:6px">🔧 ' + escHtml(equipDisplay) + '</div>' : '')
        + '</div>';
    });
  }
  html += '</div>';
  body.innerHTML = html;
  card.style.display = '';
}

function showEventDetail(ev) {
  const t = ev.reservation_type || ev.type || 'regular';
  const typeLabel = t === 'vip' ? '⭐ VIP Reservation' : t === 'emergency' ? '⚡ Important Meeting' : 'Regular Reservation';
  const typeBg = t === 'vip' ? '#EDE9FE' : t === 'emergency' ? '#FEE2E2' : '#D1FAE5';
  const typeColor = t === 'vip' ? '#5B21B6' : t === 'emergency' ? '#991B1B' : '#065F46';

  const evTitle = ev.event || ev.event_title || 'Event Details';
  const facility = ev.facility || ev.facility_name || '';
  const timeStr = ev.time || (formatTime(ev.start_datetime) + ' – ' + formatTime(ev.end_datetime));
  const dateStr = ev.date || (ev.start_datetime ? ev.start_datetime.split(' ')[0] : '');
  const dept = ev.dept || ev.department || '';
  const purpose = ev.purpose || '';
  const budget = ev.budget ?? 0;
  const equipStr = ev.equip || ev.equipment_needed || '';
  const equipDisplay = Array.isArray(equipStr) ? equipStr.join(', ') : (equipStr || '');
  const st = ev.status || '';
  const statusBadge = st === 'approved' ? '<span class="badge badge-green">Approved</span>'
    : st === 'rejected' ? '<span class="badge badge-red">Rejected</span>'
    : st === 'completed' ? '<span class="badge badge-blue">Completed</span>'
    : st === 'ongoing' ? '<span class="badge" style="background:#DBEAFE;color:#1D4ED8;border:1px solid #93C5FD">🔄 Ongoing</span>'
    : '<span class="badge badge-amber">Pending</span>';

  // Room level info
  const roomLevel = ev.room_level ? parseInt(ev.room_level) : detectRoomLevel(facility || '');
  const roomLevelLabel = ROOM_LEVEL_LABELS[roomLevel] || 'Standard';
  const roomLevelColor = ROOM_LEVEL_COLORS[roomLevel] || '#6B7280';

  document.getElementById('modal-day-title').textContent = evTitle;
  document.getElementById('modal-day-body').innerHTML =
    '<div>'
    + '<div style="background:' + typeBg + ';padding:10px 14px;border-radius:10px;font-size:13px;font-weight:600;color:' + typeColor + ';margin-bottom:16px">' + typeLabel + '</div>'
    + '<table style="width:100%;font-size:13px;border-collapse:collapse">'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280;width:140px">📍 Facility</td><td style="padding:8px 0;color:#1F2937">' + escHtml(facility) + ' <span style="background:' + roomLevelColor + '15;color:' + roomLevelColor + ';padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700">Lvl ' + roomLevel + ' · ' + roomLevelLabel + '</span></td></tr>'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">🕐 Schedule</td><td style="padding:8px 0;color:#1F2937">' + escHtml(dateStr) + '<br>' + escHtml(timeStr) + '</td></tr>'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">🏢 Department</td><td style="padding:8px 0;color:#1F2937">' + escHtml(dept) + '</td></tr>'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">📝 Purpose</td><td style="padding:8px 0;color:#1F2937">' + escHtml(purpose) + '</td></tr>'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">💰 Budget</td><td style="padding:8px 0;color:#1F2937;font-weight:600">' + formatMoney(budget) + '</td></tr>'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">🔧 Equipment</td><td style="padding:8px 0;color:#1F2937">' + (escHtml(equipDisplay) || '—') + '</td></tr>'
      + '<tr style="border-bottom:1px solid #F3F4F6"><td style="padding:8px 0;font-weight:600;color:#6B7280">📊 Status</td><td style="padding:8px 0">' + statusBadge + '</td></tr>'
      + '<tr><td style="padding:8px 0;font-weight:600;color:#6B7280">📋 Usage Log</td><td style="padding:8px 0" id="event-detail-usage-log"><span style="color:#9CA3AF;font-size:12px">Loading...</span></td></tr>'
    + '</table>'
    + '</div>';
  openModal('modal-day-detail');

  // Fetch usage log for this reservation
  const resId = ev.reservation_id;
  if (resId) {
    fetch(API + '?action=get_reservation_log&reservation_id=' + resId)
      .then(r => r.json())
      .then(data => {
        const logEl = document.getElementById('event-detail-usage-log');
        if (!logEl) return;
        if (data.log) {
          const log = data.log;
          const logStatus = log.status || '—';
          const dur = log.duration_minutes 
            ? (log.duration_minutes >= 60 ? Math.floor(log.duration_minutes/60) + 'h ' + (log.duration_minutes%60 ? (log.duration_minutes%60) + 'm' : '') : log.duration_minutes + 'm') 
            : '—';
          const logDate = log.logged_at ? formatDate(log.logged_at) : '—';
          const statusColors = { completed: '#059669', cancelled: '#EF4444', no_show: '#F59E0B' };
          const sColor = statusColors[logStatus] || '#6B7280';
          logEl.innerHTML = '<div style="background:#F0FDF4;border:1px solid #BBF7D0;padding:8px 12px;border-radius:8px;font-size:12px">'
            + '<div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">'
            + '<span style="background:' + sColor + '15;color:' + sColor + ';padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600">' + logStatus.toUpperCase() + '</span>'
            + '<span>⏱️ Duration: <b>' + dur + '</b></span>'
            + '<span>📅 Logged: ' + logDate + '</span>'
            + (log.completed_by_name ? '<span>👤 By: ' + escHtml(log.completed_by_name || '') + '</span>' : '')
            + '</div></div>';
        } else {
          logEl.innerHTML = '<span style="color:#9CA3AF;font-size:12px">No usage log recorded yet</span>';
        }
      })
      .catch(() => {
        const logEl = document.getElementById('event-detail-usage-log');
        if (logEl) logEl.innerHTML = '<span style="color:#9CA3AF;font-size:12px">No usage log available</span>';
      });
  }
}

// ───── Populate Modal Dropdowns ─────
function populateFacilityDropdown() {
  const sel = document.getElementById('res-facility');
  sel.innerHTML = rooms
    .filter(r => r.status !== 'maintenance')
    .map(r => '<option value="' + r.facility_id + '">' + escHtml(r.name) + (r.capacity ? ' (' + r.capacity + ' pax)' : '') + '</option>')
    .join('');
}

function populateEquipmentCheckboxes() {
  const container = document.getElementById('equipment-checkboxes');
  const seen = new Set();
  const items = [];

  // Derive from room equipment / amenities lists
  rooms.forEach(r => {
    const list = r.equipment_list ? (typeof r.equipment_list === 'string' ? r.equipment_list.split(',') : r.equipment_list) : [];
    list.forEach(e => {
      const name = e.trim();
      const key = name.toLowerCase().replace(/\s+/g, '_');
      if (key && !seen.has(key)) {
        seen.add(key);
        items.push({ key, name });
      }
    });
  });

  container.innerHTML = items.map(i =>
    '<label style="display:flex;align-items:center;gap:6px;padding:6px 12px;border:1px solid #E5E7EB;border-radius:8px;font-size:12px;cursor:pointer;transition:all 0.2s" class="eq-label">'
    + '<input type="checkbox" name="equip" value="' + escHtml(i.key) + '" style="accent-color:#059669"> ' + escHtml(i.name)
    + '</label>'
  ).join('');

  container.querySelectorAll('.eq-label').forEach(label => {
    const cb = label.querySelector('input[type="checkbox"]');
    cb.addEventListener('change', () => {
      label.style.borderColor = cb.checked ? '#059669' : '#E5E7EB';
      label.style.background = cb.checked ? '#D1FAE5' : '#fff';
    });
  });
}

// ───── Reservation Modal ─────
function openReservationModal(roomName, roomType, facilityId, resType) {
  // Reset form fields
  document.getElementById('res-event').value = '';
  document.getElementById('res-purpose').value = '';
  document.getElementById('res-start').value = '';
  document.getElementById('res-end').value = '';
  document.getElementById('res-attendees').value = '';
  document.getElementById('res-budget').value = '';
  document.getElementById('res-requests').value = '';
  document.querySelectorAll('input[name="equip"]').forEach(cb => {
    cb.checked = false;
    const lbl = cb.closest('.eq-label');
    if (lbl) { lbl.style.borderColor = '#E5E7EB'; lbl.style.background = '#fff'; }
  });

  // Auto-VIP only for CEO, owner, president, board-level roles — NOT all directors
  const vipRoles = ['ceo', 'owner', 'president', 'chairman', 'vice_president', 'board_member', 'chief', 'super_admin'];
  const userRole = (currentUser.role || '').toLowerCase();
  const userPosition = (currentUser.position || currentUser.job_title || '').toLowerCase();
  const isVipRole = vipRoles.some(r => userRole.includes(r) || userPosition.includes(r));
  // Only auto-VIP for Level 2+ rooms; Level 1 stays normal
  const selectedRoomForVip = rooms.find(r => r.facility_id == facilityId);
  const selectedRoomLevel = selectedRoomForVip ? (selectedRoomForVip.room_level ? parseInt(selectedRoomForVip.room_level) : detectRoomLevel(selectedRoomForVip.name)) : 1;
  if (!resType && isVipRole && selectedRoomLevel >= 2) {
    resType = 'vip';
  }

  // Emergency only allowed for level 3+
  if (resType === 'emergency' && currentUser.position_level < 3) {
    Swal.fire({ icon: 'error', title: 'Unauthorized', text: 'Important reservations require Director level (Level 3+) authorization.', confirmButtonColor: '#059669' });
    return;
  }

  // Set type
  document.querySelectorAll('input[name="res-type"]').forEach(r => r.checked = r.value === (resType || 'regular'));
  if (facilityId) document.getElementById('res-facility').value = facilityId;
  updateResTypeBanner();

  // Find selected room's level for rules display
  const selectedRoom = rooms.find(r => r.facility_id == facilityId);
  const roomLevel = selectedRoom ? (selectedRoom.room_level ? parseInt(selectedRoom.room_level) : detectRoomLevel(selectedRoom.name)) : 1;
  const roomLevelLabel = ROOM_LEVEL_LABELS[roomLevel] || 'Standard';

  // Show room level rules banner
  const rulesEl = document.getElementById('res-room-rules');
  if (rulesEl) {
    let rulesHtml = '';
    if (roomLevel === 1) {
      rulesHtml = '<div style="background:#D1FAE5;padding:10px 14px;border-radius:8px;font-size:12px;color:#065F46;margin-bottom:12px">'
        + '<strong>📋 Level 1 · ' + roomLevelLabel + '</strong><br>'
        + 'Standard booking for interviews, meetings & training. Cancel or reschedule anytime.</div>';
    } else if (roomLevel === 2) {
      rulesHtml = '<div style="background:#EDE9FE;padding:10px 14px;border-radius:8px;font-size:12px;color:#5B21B6;margin-bottom:12px">'
        + '<strong>⚠️ Level 2 · ' + roomLevelLabel + '</strong><br>'
        + 'VIP room for CEO, owner, and partnership meetings. <b>VIP reservations CANNOT be canceled or rescheduled once approved.</b></div>';
    } else if (roomLevel === 3) {
      rulesHtml = '<div style="background:#FEE2E2;padding:10px 14px;border-radius:8px;font-size:12px;color:#991B1B;margin-bottom:12px">'
        + '<strong>⚡ Level 3 · ' + roomLevelLabel + '</strong><br>'
        + 'Important room for urgent meetings (budget shortfall, crisis). Admin-only cancellation. Auto-approved with urgent notifications.</div>';
    }
    rulesEl.innerHTML = rulesHtml;
  }

// Auto-VIP banner only for CEO/owner/president/board roles AND Level 2+ rooms
  const autoVipBanner = document.getElementById('res-auto-vip-banner');
  if (autoVipBanner) {
    if (isVipRole && resType === 'vip' && resType !== 'emergency' && roomLevel >= 2) {
      autoVipBanner.style.display = 'block';
      autoVipBanner.innerHTML = '<div style="background:#EDE9FE;border:1px solid #C4B5FD;padding:10px 14px;border-radius:8px;font-size:12px;color:#5B21B6">' 
        + '⭐ <strong>Auto-VIP:</strong> Your reservation is automatically tagged as VIP due to your executive-level position (CEO/Owner/President) on a Level 2+ room.</div>';
    } else if (isVipRole && roomLevel === 1) {
      autoVipBanner.style.display = 'block';
      autoVipBanner.innerHTML = '<div style="background:#DBEAFE;border:1px solid #93C5FD;padding:10px 14px;border-radius:8px;font-size:12px;color:#1D4ED8">' 
        + 'ℹ️ <strong>Level 1 Room:</strong> VIP auto-tagging does not apply to Normal (Level 1) rooms. Your booking will be processed as a regular reservation.</div>';
    } else {
      autoVipBanner.style.display = 'none';
    }
  }

  // Restrict type radios based on user level/role
  document.querySelectorAll('input[name="res-type"]').forEach(r => {
    if (r.value === 'emergency') {
      r.disabled = currentUser.position_level < 3;
      const lbl = r.closest('.type-option');
      if (lbl && currentUser.position_level < 3) lbl.style.opacity = '0.4';
    }
    if (r.value === 'vip') {
      // VIP only for CEO, owner, president, board-level roles
      const canVip = isUserVipEligible();
      r.disabled = !canVip;
      const lbl = r.closest('.type-option');
      if (lbl && !canVip) lbl.style.opacity = '0.4';
    }
  });

  const titles = { emergency: '⚡ Important Meeting Reservation', vip: '⭐ VIP Executive Reservation', regular: 'New Reservation' };
  document.getElementById('modal-res-title').textContent = titles[resType] || 'New Reservation';

  if (resType === 'emergency') {
    document.getElementById('res-priority').value = 'urgent';
  } else if (resType === 'vip') {
    document.getElementById('res-priority').value = 'high';
  } else {
    document.getElementById('res-priority').value = 'normal';
  }

  // Fetch booked slots for time blocking
  if (facilityId) {
    fetchBookedSlots(facilityId);
  }

  openModal('modal-reservation');
}

// ───── Fetch Booked Slots for Time Blocking ─────
let bookedSlots = [];
async function fetchBookedSlots(facilityId) {
  const dateEl = document.getElementById('res-start');
  const date = dateEl?.value ? dateEl.value.split('T')[0] : new Date().toISOString().split('T')[0];
  try {
    const res = await fetch(API + '?action=booked_slots&facility_id=' + facilityId + '&date=' + date);
    const data = await res.json();
    bookedSlots = data.slots || [];
    updateTimeSlotIndicator();
  } catch(e) { bookedSlots = []; }
}

function updateTimeSlotIndicator() {
  const indicator = document.getElementById('res-time-slots-indicator');
  if (!indicator || !bookedSlots.length) {
    if (indicator) indicator.innerHTML = '';
    return;
  }
  const slotsHtml = bookedSlots.map(s => {
    const typeColor = s.reservation_type === 'emergency' ? '#DC2626' : s.reservation_type === 'vip' ? '#7C3AED' : '#6B7280';
    return '<div style="display:flex;align-items:center;gap:6px;padding:4px 8px;background:#FEE2E2;border-radius:6px;font-size:11px;color:' + typeColor + '">'
      + '<span style="width:6px;height:6px;border-radius:50%;background:' + typeColor + '"></span>'
      + formatTime(s.start_datetime) + ' – ' + formatTime(s.end_datetime)
      + ' (' + s.reservation_type + ')'
      + '</div>';
  }).join('');
  indicator.innerHTML = '<div style="margin-top:8px"><div style="font-size:11px;font-weight:600;color:#991B1B;margin-bottom:4px">⛔ Blocked time slots:</div>'
    + '<div style="display:flex;flex-wrap:wrap;gap:4px">' + slotsHtml + '</div></div>';
}

// Type radio change handler
document.querySelectorAll('input[name="res-type"]').forEach(radio => {
  radio.addEventListener('change', updateResTypeBanner);
});

// Refresh booked slots when date or facility changes
document.getElementById('res-start')?.addEventListener('change', function() {
  const facilityId = document.getElementById('res-facility').value;
  if (facilityId) fetchBookedSlots(facilityId);
});
document.getElementById('res-facility')?.addEventListener('change', function() {
  if (this.value) fetchBookedSlots(this.value);
});

function updateResTypeBanner() {
  const banner = document.getElementById('res-type-banner');
  const val = document.querySelector('input[name="res-type"]:checked')?.value;
  document.querySelectorAll('.type-option').forEach(opt => {
    opt.style.borderColor = opt.dataset.type === val
      ? (val === 'vip' ? '#7C3AED' : val === 'emergency' ? '#DC2626' : '#059669')
      : '#E5E7EB';
    opt.style.background = opt.dataset.type === val
      ? (val === 'vip' ? '#EDE9FE' : val === 'emergency' ? '#FEE2E2' : '#D1FAE5')
      : '#fff';
  });
  if (val === 'vip') {
    banner.style.display = 'block';
    banner.style.background = '#EDE9FE';
    banner.style.color = '#5B21B6';
    banner.textContent = '⭐ VIP reservations are for CEO, owner, president, and partnership meetings. Cannot be canceled/rescheduled once approved.';
  } else if (val === 'emergency') {
    banner.style.display = 'block';
    banner.style.background = '#FEE2E2';
    banner.style.color = '#991B1B';
    banner.textContent = '⚡ Important meetings are for urgent matters (budget shortfall, crisis). Auto-approved and marked urgent.';
  } else {
    banner.style.display = 'none';
  }
}

async function submitReservation() {
  const resType = document.querySelector('input[name="res-type"]:checked')?.value || 'regular';
  const event = document.getElementById('res-event').value.trim();
  const purpose = document.getElementById('res-purpose').value.trim();
  const start = document.getElementById('res-start').value;
  const end = document.getElementById('res-end').value;
  const facilityId = document.getElementById('res-facility').value;

  if (!event) return Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please enter an event title.', confirmButtonColor: '#059669' });
  if (!purpose) return Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please enter the purpose / description.', confirmButtonColor: '#059669' });
  if (!start || !end) return Swal.fire({ icon: 'warning', title: 'Missing Field', text: 'Please set start and end date/time.', confirmButtonColor: '#059669' });
  if (new Date(end) <= new Date(start)) return Swal.fire({ icon: 'error', title: 'Invalid Time', text: 'End time must be after start time.', confirmButtonColor: '#059669' });

  const equip = [];
  document.querySelectorAll('input[name="equip"]:checked').forEach(cb => equip.push(cb.value));

  // Determine room level for rules display
  const selectedRoom = rooms.find(r => r.facility_id == facilityId);
  const roomLevel = selectedRoom ? (selectedRoom.room_level ? parseInt(selectedRoom.room_level) : detectRoomLevel(selectedRoom.name)) : 1;
  const roomLevelLabel = ROOM_LEVEL_LABELS[roomLevel] || 'Standard';

  const data = {
    facility_id: facilityId,
    event_title: event,
    purpose: purpose,
    reservation_type: resType,
    priority: document.getElementById('res-priority').value,
    start_datetime: start,
    end_datetime: end,
    department: document.getElementById('res-dept').value,
    attendees_count: document.getElementById('res-attendees').value || null,
    budget: document.getElementById('res-budget').value || 0,
    equipment_needed: equip,
    special_requests: document.getElementById('res-requests').value,
  };

  // Check for time conflicts first
  try {
    const conflictRes = await fetch(API + '?action=check_conflict', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ facility_id: facilityId, start_datetime: start, end_datetime: end })
    });
    const conflictData = await conflictRes.json();
    if (conflictData.has_conflict && resType !== 'emergency') {
      return Swal.fire({
        icon: 'error',
        title: 'Time Slot Conflict',
        html: 'This time slot conflicts with an existing reservation.<br><small style="color:#6B7280">Only emergency reservations can override conflicts.</small>',
        confirmButtonColor: '#059669'
      });
    }
    if (conflictData.has_conflict && resType === 'emergency') {
      const overrideResult = await Swal.fire({
        icon: 'warning',
        title: '⚠️ Conflict Detected',
        html: 'There is an existing reservation in this time slot.<br><b>Emergency reservations can override this conflict.</b><br>Do you want to proceed?',
        showCancelButton: true,
        confirmButtonText: 'Yes, Override',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280'
      });
      if (!overrideResult.isConfirmed) return;
    }
  } catch(e) { /* continue if conflict check fails */ }

  const typeLabel = { regular: 'Regular', vip: '⭐ VIP', emergency: '⚡ Important' }[resType];

  // Build room-level rules reminder
  let rulesReminder = '';
  if (roomLevel === 1) {
    rulesReminder = '<div style="background:#D1FAE5;padding:8px 12px;border-radius:8px;font-size:12px;color:#065F46;margin-top:12px;text-align:left">'
      + '📋 <b>Level 1 · Normal:</b> Standard booking for interviews, meetings & training. Cancel or reschedule anytime.</div>';
  } else if (roomLevel === 2) {
    rulesReminder = '<div style="background:#EDE9FE;padding:8px 12px;border-radius:8px;font-size:12px;color:#5B21B6;margin-top:12px;text-align:left">'
      + '⚠️ <b>Level 2 · VIP:</b> For CEO, owner & partnership meetings. <b>CANNOT be canceled or rescheduled</b> once approved.</div>';
  } else if (roomLevel === 3) {
    rulesReminder = '<div style="background:#FEE2E2;padding:8px 12px;border-radius:8px;font-size:12px;color:#991B1B;margin-top:12px;text-align:left">'
      + '⚡ <b>Level 3 · Important:</b> Important room. Auto-approved. Admin-only cancellation.</div>';
  }

  const vipRolesConfirm = ['ceo', 'owner', 'president', 'chairman', 'vice_president', 'board_member', 'chief', 'super_admin'];
  const isVipRoleConfirm = vipRolesConfirm.some(r => (currentUser.role || '').toLowerCase().includes(r) || (currentUser.position || currentUser.job_title || '').toLowerCase().includes(r));
  const autoVipNote = (isVipRoleConfirm && resType === 'vip')
    ? '<div style="background:#EDE9FE;padding:6px 12px;border-radius:8px;font-size:11px;color:#5B21B6;margin-top:8px">⭐ Auto-tagged as VIP (CEO/Owner/President level)</div>'
    : '';

  const confirmResult = await Swal.fire({
    title: 'Confirm Reservation',
    html: '<div style="text-align:left;font-size:13px;line-height:1.7">'
      + '<b>Type:</b> ' + typeLabel + '<br>'
      + '<b>Room:</b> ' + escHtml(selectedRoom?.name || '') + ' <span style="font-size:11px;color:#6B7280">(Lvl ' + roomLevel + ' · ' + roomLevelLabel + ')</span><br>'
      + '<b>Event:</b> ' + escHtml(event) + '<br>'
      + '<b>Schedule:</b> ' + start.replace('T', ' ') + ' – ' + end.replace('T', ' ')
      + '</div>'
      + autoVipNote
      + rulesReminder,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Submit',
    cancelButtonText: 'Cancel',
    confirmButtonColor: resType === 'emergency' ? '#DC2626' : '#059669',
    cancelButtonColor: '#6B7280'
  });
  if (!confirmResult.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=create_reservation', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });
    const result = await res.json();

    if (result.success || result.reservation_code) {
      const wasAutoVip = result.auto_vip === true;
      await Swal.fire({
        icon: 'success',
        title: 'Reservation Submitted!',
        html: '<div style="text-align:left;font-size:14px;line-height:1.8">'
          + '<b>Code:</b> ' + escHtml(result.reservation_code || 'N/A') + '<br>'
          + '<b>Type:</b> ' + (wasAutoVip ? '⭐ VIP (Auto-tagged)' : typeLabel) + '<br>'
          + '<b>Room Level:</b> Lvl ' + roomLevel + ' · ' + roomLevelLabel + '<br>'
          + '<b>Event:</b> ' + escHtml(event) + '<br>'
          + '<b>Budget:</b> ' + formatMoney(data.budget) + '<br>'
          + '<b>Equipment:</b> ' + (equip.length ? equip.join(', ') : 'None') + '<br>'
          + '<b>Status:</b> ' + (resType === 'emergency' ? 'Auto-approved ✓' : (wasAutoVip ? 'Auto-approved (VIP) ✓' : 'Pending approval'))
          + '</div>',
        confirmButtonColor: '#059669'
      });
      closeModal('modal-reservation');
      await loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to create reservation.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#059669' });
  }
}

// ───── Approve Reservation ─────
async function approveReservation(id) {
  const confirmResult = await Swal.fire({
    title: 'Approve Reservation?',
    text: 'This will approve the reservation and notify the requestor.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Approve',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#059669',
    cancelButtonColor: '#6B7280'
  });
  if (!confirmResult.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=update_status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reservation_id: id, status: 'approved', remarks: 'Approved by admin' })
    });
    const result = await res.json();

    if (result.success) {
      await Swal.fire({ icon: 'success', title: 'Approved!', text: 'Reservation has been approved. Check Room Monitoring for updated status.', confirmButtonColor: '#059669' });
      await loadData();
      // Auto-switch to Room Monitoring sub-tab so user sees occupied/pending change
      switchBookingTab('rooms-view', document.querySelector('[data-booking-tab="rooms-view"]'));
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to approve.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#059669' });
  }
}

// ───── Mark Ongoing Reservation ─────
async function markOngoingReservation(id) {
  const confirmResult = await Swal.fire({
    title: '🔄 Mark as Ongoing?',
    text: 'This will set the reservation status to Ongoing — indicating the meeting/event is now in progress.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Mark Ongoing',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#1D4ED8',
    cancelButtonColor: '#6B7280'
  });
  if (!confirmResult.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=update_status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reservation_id: id, status: 'ongoing', remarks: 'Marked as ongoing by admin' })
    });
    const result = await res.json();

    if (result.success) {
      await Swal.fire({ icon: 'success', title: 'Ongoing!', text: 'Reservation is now marked as Ongoing. Room is occupied.', confirmButtonColor: '#1D4ED8' });
      await loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to update status.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#059669' });
  }
}

// ───── Validate Reservation ─────
async function validateReservation(id) {
  const confirmResult = await Swal.fire({
    title: 'Validate Reservation?',
    text: 'This will mark the reservation as validated.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Validate',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#059669',
    cancelButtonColor: '#6B7280'
  });
  if (!confirmResult.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=validate_reservation', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reservation_id: id })
    });
    const result = await res.json();

    if (result.success) {
      await Swal.fire({ icon: 'success', title: 'Validated!', text: 'Reservation has been validated.', confirmButtonColor: '#059669' });
      await loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to validate.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#059669' });
  }
}

// ───── Render Approved Bookings ─────
function renderApprovedBookings() {
  const tbody = document.getElementById('approved-tbody');
  const filter = document.getElementById('approved-filter-status').value;
  const categoryFilter = document.getElementById('approved-filter-category')?.value || '';
  const deptFilter = document.getElementById('approved-filter-dept')?.value || '';
  const now = new Date();

  // Get approved + completed reservations
  let approved = reservations.filter(r => r.status === 'approved' || r.status === 'completed');

  // Determine room status for each reservation
  approved = approved.map(r => {
    const start = new Date(r.start_datetime);
    const end = new Date(r.end_datetime);
    let roomStatus = 'upcoming'; // pending
    if (r.status === 'completed') {
      roomStatus = 'completed';
    } else if (now >= start && now <= end) {
      roomStatus = 'ongoing'; // occupied
    } else if (now > end) {
      roomStatus = 'completed';
    }
    return { ...r, _roomStatus: roomStatus };
  });

  // Apply filters
  if (filter) approved = approved.filter(r => r._roomStatus === filter);
  if (categoryFilter) approved = approved.filter(r => r.reservation_type === categoryFilter);
  if (deptFilter === 'inter') {
    approved = approved.filter(r => r.department !== currentUser.department && r.user_department !== currentUser.department);
  } else if (deptFilter === 'own') {
    approved = approved.filter(r => r.department === currentUser.department || r.user_department === currentUser.department);
  }

  // Sort: ongoing first, then upcoming, then completed; emergency always on top
  const statusOrder = { ongoing: 0, upcoming: 1, completed: 2 };
  approved.sort((a, b) => {
    // Emergency priority first
    if (a.reservation_type === 'emergency' && b.reservation_type !== 'emergency') return -1;
    if (b.reservation_type === 'emergency' && a.reservation_type !== 'emergency') return 1;
    return (statusOrder[a._roomStatus] ?? 9) - (statusOrder[b._roomStatus] ?? 9);
  });

  if (!approved.length) {
    tbody.innerHTML = '<tr><td colspan="9" class="empty-state" style="padding:40px"><div style="font-size:36px;margin-bottom:8px">✅</div><div style="font-weight:600;color:#1F2937">No approved bookings</div><div style="font-size:13px;color:#9CA3AF;margin-top:4px">Approved reservations will appear here.</div></td></tr>';
    Paginator.setTotalItems('fac-approved', 0);
    Paginator.renderControls('fac-approved', 'approved-pagination');
    return;
  }

  var allRows = approved.map(r => {
    // Color-coded category badge
    const roomLevel = r.room_level ? parseInt(r.room_level) : detectRoomLevel(r.facility_name || '');
    const roomLevelLabel = ROOM_LEVEL_LABELS[roomLevel] || 'Standard';
    const roomLevelColor = ROOM_LEVEL_COLORS[roomLevel] || '#6B7280';

    let categoryBadge = '';
    if (r.reservation_type === 'emergency') {
      categoryBadge = '<span style="background:#FEE2E2;color:#DC2626;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;border:1px solid #FECACA">⚡ IMPORTANT</span>';
    } else if (r.reservation_type === 'vip') {
      categoryBadge = '<span style="background:#FEF3C7;color:#B45309;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;border:1px solid #FDE68A">⭐ VIP</span>';
    } else {
      categoryBadge = '<span style="background:#DBEAFE;color:#1D4ED8;padding:3px 10px;border-radius:6px;font-size:11px;font-weight:700;border:1px solid #BFDBFE">📋 REGULAR</span>';
    }
    // Auto-tagged indicator
    if (r.is_auto_tagged == 1) {
      categoryBadge += '<br><span style="font-size:9px;color:#7C3AED">auto-tagged</span>';
    }

    // Room level mini badge
    const levelBadge = '<span style="background:' + roomLevelColor + '15;color:' + roomLevelColor + ';padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700">Lvl ' + roomLevel + '</span>';

    // Room status badge
    let roomStatusBadge = '';
    if (r._roomStatus === 'ongoing') {
      roomStatusBadge = '<span class="badge badge-red">🔴 Occupied</span>';
    } else if (r._roomStatus === 'upcoming') {
      roomStatusBadge = '<span class="badge badge-amber">⏳ Pending</span>';
    } else {
      roomStatusBadge = '<span class="badge badge-green">🟢 Available</span>';
    }

    // Inter-department indicator
    const isInterDept = r.department !== currentUser.department && r.user_department !== currentUser.department;
    const deptDisplay = escHtml(r.department || '') + (isInterDept
      ? '<br><span style="font-size:9px;background:#FEF3C7;color:#92400E;padding:1px 6px;border-radius:3px">External</span>'
      : '');

    // Actions with level-based rules
    let actions = '';
    const isVipLvl2 = roomLevel === 2 && r.reservation_type === 'vip';
    const isLvl3Emergency = roomLevel === 3;

    if (r._roomStatus === 'ongoing') {
      actions = '<div style="display:flex;gap:4px;flex-wrap:wrap">'
        + '<button class="btn btn-primary btn-sm" title="Mark as Done" onclick="completeReservation(' + r.reservation_id + ')">✓ Done</button>';
      // Can cancel ongoing if not restricted
      if (!isVipLvl2) {
        actions += '<button class="btn btn-outline btn-sm" title="Cancel" onclick="cancelReservation(' + r.reservation_id + ',' + roomLevel + ',\'' + (r.reservation_type || 'regular') + '\')" style="color:#DC2626;border-color:#FECACA;font-size:11px">Cancel</button>';
      }
      actions += '</div>';
    } else if (r._roomStatus === 'upcoming') {
      const evJson = JSON.stringify(r).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
      actions = '<div style="display:flex;gap:4px;flex-wrap:wrap">'
        + '<button class="btn btn-outline btn-sm" title="View" onclick="showEventDetail(JSON.parse(this.dataset.ev))" data-ev="' + evJson + '">'
        + '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>';
      // Cancel button with level logic
      if (isVipLvl2) {
        actions += '<button class="btn btn-outline btn-sm" disabled title="VIP Lvl 2: No cancel" style="opacity:0.3;cursor:not-allowed;font-size:11px;color:#DC2626">Cancel</button>';
      } else if (isLvl3Emergency && currentUser.role !== 'super_admin' && currentUser.role !== 'admin') {
        actions += '<button class="btn btn-outline btn-sm" disabled title="Important Lvl 3: Admin only" style="opacity:0.3;cursor:not-allowed;font-size:11px;color:#DC2626">Cancel</button>';
      } else {
        actions += '<button class="btn btn-outline btn-sm" title="Cancel" onclick="cancelReservation(' + r.reservation_id + ',' + roomLevel + ',\'' + (r.reservation_type || 'regular') + '\')" style="color:#DC2626;border-color:#FECACA;font-size:11px">Cancel</button>';
      }
      // Reschedule button with level logic
      if (isVipLvl2) {
        actions += '<button class="btn btn-outline btn-sm" disabled title="VIP Lvl 2: No reschedule" style="opacity:0.3;cursor:not-allowed;font-size:11px;color:#2563EB">Resched</button>';
      } else {
        actions += '<button class="btn btn-outline btn-sm" title="Reschedule" onclick="rescheduleReservation(' + r.reservation_id + ',' + roomLevel + ',\'' + (r.reservation_type || 'regular') + '\')" style="color:#2563EB;border-color:#BFDBFE;font-size:11px">Resched</button>';
      }
      actions += '</div>';
    } else {
      actions = '<span class="badge badge-blue">Completed</span>';
    }

    // Usage log indicator
    let usageLogCell = '';
    if (r._roomStatus === 'completed' || r.status === 'completed') {
      usageLogCell = '<span class="badge badge-green" style="cursor:pointer;font-size:10px" '
        + 'onclick="showEventDetail(JSON.parse(this.closest(\'tr\').querySelector(\'[data-ev]\')?.dataset?.ev || \'{}\'))" '
        + 'title="View usage log details">📋 Logged</span>';
    } else if (r._roomStatus === 'ongoing') {
      usageLogCell = '<span class="badge" style="background:#DBEAFE;color:#1D4ED8;font-size:10px;border:1px solid #93C5FD">🔄 In Use</span>';
    } else {
      usageLogCell = '<span style="color:#9CA3AF;font-size:11px">—</span>';
    }

    return '<tr' + (r.reservation_type === 'emergency' ? ' style="background:#FEF2F2"' : (r.reservation_type === 'vip' ? ' style="background:#FFFBEB"' : '')) + '>'
      + '<td style="font-weight:600;font-size:12px">' + escHtml(r.reservation_code || '') + '</td>'
      + '<td style="font-weight:600">' + escHtml(r.facility_name || '') + '<br>' + levelBadge + '</td>'
      + '<td>' + escHtml(r.event_title || '') + (r.purpose ? '<br><span style="font-size:11px;color:#9CA3AF">' + escHtml(r.purpose) + '</span>' : '') + '</td>'
      + '<td>' + categoryBadge + '</td>'
      + '<td style="font-size:12px">' + formatDateTime(r.start_datetime, r.end_datetime) + '</td>'
      + '<td>' + deptDisplay + '</td>'
      + '<td>' + roomStatusBadge + '</td>'
      + '<td>' + usageLogCell + '</td>'
      + '<td>' + actions + '</td>'
      + '</tr>';
  });

  var paged = Paginator.paginate('fac-approved', allRows);
  tbody.innerHTML = paged.join('');
  Paginator.renderControls('fac-approved', 'approved-pagination');
}

// ───── Complete Reservation (Mark as Done) ─────
async function completeReservation(id) {
  const confirmResult = await Swal.fire({
    title: 'Mark as Complete?',
    text: 'This will mark the reservation as done and set the room back to available.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Complete',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#059669',
    cancelButtonColor: '#6B7280'
  });
  if (!confirmResult.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=complete_reservation', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reservation_id: id })
    });
    const result = await res.json();

    if (result.success) {
      await Swal.fire({ icon: 'success', title: 'Completed!', text: 'Reservation marked as done. Room is now available.', confirmButtonColor: '#059669' });
      await loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to complete reservation.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#059669' });
  }
}

// ───── Cancel Reservation (with room-level rules) ─────
async function cancelReservation(id, roomLevel, resType) {
  // Room Level 2 VIP: cannot cancel
  if (roomLevel === 2 && resType === 'vip') {
    return Swal.fire({ icon: 'error', title: 'Cannot Cancel', text: 'VIP Level 2 reservations cannot be canceled once approved.', confirmButtonColor: '#059669' });
  }
  // Room Level 3 Emergency: admin only
  if (roomLevel === 3 && currentUser.role !== 'super_admin' && currentUser.role !== 'admin') {
    return Swal.fire({ icon: 'error', title: 'Cannot Cancel', text: 'Important Level 3 reservations can only be canceled by administrators.', confirmButtonColor: '#059669' });
  }

  let reason = '';

  // Room Level 2+: require reason
  if (roomLevel >= 2) {
    const reasonResult = await Swal.fire({
      title: 'Cancel Reservation',
      html: '<div style="text-align:left;font-size:13px;margin-bottom:12px">'
        + '<b>Room Level ' + roomLevel + ':</b> A reason for cancellation is required.</div>',
      input: 'textarea',
      inputPlaceholder: 'Enter reason for cancellation...',
      inputAttributes: { 'aria-label': 'Cancellation reason' },
      showCancelButton: true,
      confirmButtonText: 'Cancel Reservation',
      cancelButtonText: 'Go Back',
      confirmButtonColor: '#DC2626',
      cancelButtonColor: '#6B7280',
      inputValidator: (value) => { if (!value || !value.trim()) return 'Please provide a reason for cancellation.'; }
    });
    if (!reasonResult.isConfirmed) return;
    reason = reasonResult.value;
  } else {
    // Level 1: simple confirm
    const confirmResult = await Swal.fire({
      title: 'Cancel Reservation?',
      text: 'Are you sure you want to cancel this reservation?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, Cancel It',
      cancelButtonText: 'Go Back',
      confirmButtonColor: '#DC2626',
      cancelButtonColor: '#6B7280'
    });
    if (!confirmResult.isConfirmed) return;
  }

  try {
    const res = await fetch(API + '?action=cancel_reservation', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ reservation_id: id, reason: reason })
    });
    const result = await res.json();
    if (result.success) {
      await Swal.fire({ icon: 'success', title: 'Cancelled', text: 'Reservation has been cancelled.', confirmButtonColor: '#059669' });
      await loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to cancel.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#059669' });
  }
}

// ───── Reschedule Reservation (with room-level rules) ─────
async function rescheduleReservation(id, roomLevel, resType) {
  // Room Level 2 VIP: cannot reschedule
  if (roomLevel === 2 && resType === 'vip') {
    return Swal.fire({ icon: 'error', title: 'Cannot Reschedule', text: 'VIP Level 2 reservations cannot be rescheduled once approved.', confirmButtonColor: '#059669' });
  }

  let reason = '';

  // Room Level 2+: require reason before reschedule
  if (roomLevel >= 2) {
    const reasonResult = await Swal.fire({
      title: 'Reschedule Reservation',
      html: '<div style="text-align:left;font-size:13px;margin-bottom:12px">'
        + '<b>Room Level ' + roomLevel + ':</b> A reason for rescheduling is required.</div>',
      input: 'textarea',
      inputPlaceholder: 'Enter reason for rescheduling...',
      inputAttributes: { 'aria-label': 'Reschedule reason' },
      showCancelButton: true,
      confirmButtonText: 'Continue',
      cancelButtonText: 'Go Back',
      confirmButtonColor: '#2563EB',
      cancelButtonColor: '#6B7280',
      inputValidator: (value) => { if (!value || !value.trim()) return 'Please provide a reason for rescheduling.'; }
    });
    if (!reasonResult.isConfirmed) return;
    reason = reasonResult.value;
  }

  // Get new date/time
  const schedResult = await Swal.fire({
    title: 'New Schedule',
    html: '<div style="text-align:left">'
      + '<label style="font-size:13px;font-weight:600;display:block;margin-bottom:4px">New Start Date/Time</label>'
      + '<input type="datetime-local" id="swal-resched-start" class="swal2-input" style="width:100%">'
      + '<label style="font-size:13px;font-weight:600;display:block;margin-bottom:4px;margin-top:12px">New End Date/Time</label>'
      + '<input type="datetime-local" id="swal-resched-end" class="swal2-input" style="width:100%">'
      + (reason ? '<div style="background:#DBEAFE;padding:8px;border-radius:8px;font-size:12px;color:#1E40AF;margin-top:12px"><b>Reason:</b> ' + escHtml(reason) + '</div>' : '')
      + '</div>',
    showCancelButton: true,
    confirmButtonText: 'Reschedule',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#2563EB',
    cancelButtonColor: '#6B7280',
    preConfirm: () => {
      const newStart = document.getElementById('swal-resched-start').value;
      const newEnd = document.getElementById('swal-resched-end').value;
      if (!newStart || !newEnd) { Swal.showValidationMessage('Please set both start and end times.'); return false; }
      if (new Date(newEnd) <= new Date(newStart)) { Swal.showValidationMessage('End time must be after start time.'); return false; }
      return { start: newStart, end: newEnd };
    }
  });
  if (!schedResult.isConfirmed) return;

  try {
    const res = await fetch(API + '?action=reschedule_reservation', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        reservation_id: id,
        new_start: schedResult.value.start,
        new_end: schedResult.value.end,
        reason: reason
      })
    });
    const result = await res.json();
    if (result.success) {
      await Swal.fire({ icon: 'success', title: 'Rescheduled', text: 'Reservation has been rescheduled successfully.', confirmButtonColor: '#059669' });
      await loadData();
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: result.message || 'Failed to reschedule.', confirmButtonColor: '#059669' });
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Network error. Please try again.', confirmButtonColor: '#059669' });
  }
}

// Room filter button styling on load
document.querySelectorAll('.room-filter').forEach(btn => {
  if (btn.classList.contains('active')) {
    btn.style.background = '#059669';
    btn.style.color = '#fff';
    btn.style.borderColor = '#059669';
  }
  btn.addEventListener('click', () => {
    document.querySelectorAll('.room-filter').forEach(b => {
      b.style.background = '';
      b.style.color = '';
      b.style.borderColor = '';
    });
    btn.style.background = '#059669';
    btn.style.color = '#fff';
    btn.style.borderColor = '#059669';
  });
});

// ───── Section Switching (hash-driven) ─────
function showSection(hash) {
  const sections = document.querySelectorAll('.tab-content');
  let id = hash ? hash.replace('#', '') : 'tab-monitoring';

  // Highlight active directory card
  document.querySelectorAll('.module-directory-label + .stats-grid .stat-card-link').forEach(c => {
    const href = c.getAttribute('href') || '';
    c.classList.toggle('active-module', href === '#' + id);
    const arrow = c.querySelector('.stat-arrow');
    if (arrow) arrow.textContent = href === '#' + id ? '●' : '→';
  });

  // Map old tab-calendar and tab-booking hashes to the merged tab-monitoring
  if (id === 'tab-calendar') {
    id = 'tab-monitoring';
    setTimeout(() => switchBookingTab('calendar-view', document.querySelector('[data-booking-tab="calendar-view"]')), 100);
  } else if (id === 'tab-booking') {
    id = 'tab-monitoring';
    setTimeout(() => switchBookingTab('reservations-view', document.querySelector('[data-booking-tab="reservations-view"]')), 100);
  } else if (id === 'tab-approved') {
    setTimeout(() => renderApprovedBookings(), 100);
  } else if (id === 'tab-utilization') {
    setTimeout(() => renderUtilization(), 100);
  } else if (id === 'tab-room-logs') {
    setTimeout(() => loadRoomLogs(), 100);
    setTimeout(() => loadRoomLogStats(), 150);
  }

  sections.forEach(s => s.classList.remove('active'));
  const target = document.getElementById(id);
  if (target) target.classList.add('active');
  else if (sections[0]) sections[0].classList.add('active');
}

// ───── Section Tab Switcher (top-level tab bar) ─────
function switchSection(sectionId, btn) {
  location.hash = '#' + sectionId;
}

// ───── Booking Sub-tab Switcher ─────
function switchBookingTab(tabId, btn) {
  // Toggle sub-tab buttons
  document.querySelectorAll('#booking-sub-tabs .sub-tab').forEach(t => t.classList.remove('active'));
  if (btn) btn.classList.add('active');

  // Toggle sub-tab content panels
  document.querySelectorAll('#tab-monitoring .sub-tab-content').forEach(p => p.classList.remove('active'));
  const panel = document.getElementById(tabId);
  if (panel) panel.classList.add('active');

  // Render calendar when switching to calendar view
  if (tabId === 'calendar-view') renderCalendar();
  // Load HR2 training bookings when switching to that sub-tab
  if (tabId === 'hr2-training-view') hr2LoadTrainingSubtab();
}

window.addEventListener('hashchange', () => showSection(location.hash));

// ───── Init ─────
loadData().then(() => {
  showSection(location.hash);
});

// ───── Export Functions ─────
function exportReservations(format) {
  const headers = ['Code', 'Facility', 'Event / Purpose', 'Type', 'Start Date', 'End Date', 'Budget', 'Department', 'Status', 'Validated'];
  const rows = reservations.map(r => [
    r.reservation_code || '', r.facility_name || '', r.title || r.purpose || '',
    r.reservation_type || 'regular', r.start_datetime || '', r.end_datetime || '',
    r.budget || '0', r.department || '', r.status || '', r.validated ? 'Yes' : 'No'
  ]);
  if (format === 'csv') {
    ExportHelper.exportCSV('Facilities_Reservations', headers, rows);
  } else {
    ExportHelper.exportPDF('Facilities_Reservations', 'Facilities — All Reservations', headers, rows, { landscape: true, subtitle: reservations.length + ' records' });
  }
}

function exportMaintenance(format) {
  const headers = ['Request ID', 'Facility', 'Issue', 'Priority', 'Status', 'Requested Date', 'Assigned To'];
  const rows = maintenance.map(m => [
    m.request_id || m.id || '', m.facility_name || '', m.issue || m.description || '',
    m.priority || '', m.status || '', m.created_at || m.request_date || '', m.assigned_to || ''
  ]);
  if (format === 'csv') {
    ExportHelper.exportCSV('Facilities_Maintenance', headers, rows);
  } else {
    ExportHelper.exportPDF('Facilities_Maintenance', 'Facilities — Maintenance Requests', headers, rows, { subtitle: maintenance.length + ' requests' });
  }
}

// ───── Room Utilization Dashboard ─────
function renderUtilization() {
  const days = parseInt(document.getElementById('util-period-filter')?.value || '30');
  const cutoff = new Date();
  cutoff.setDate(cutoff.getDate() - days);

  // Calculate hours booked per room
  const roomUsage = {};
  rooms.forEach(r => {
    roomUsage[r.facility_id] = { name: r.name, hours: 0, bookings: 0 };
  });

  const approvedRes = reservations.filter(r => r.status === 'approved' || r.status === 'completed');
  approvedRes.forEach(r => {
    const start = new Date(r.start_datetime);
    if (start >= cutoff && roomUsage[r.facility_id]) {
      const end = new Date(r.end_datetime);
      const hrs = Math.max(0, (end - start) / 3600000);
      roomUsage[r.facility_id].hours += hrs;
      roomUsage[r.facility_id].bookings++;
    }
  });

  // Working hours = days * 8 hrs
  const totalAvailHours = days * 8;
  const entries = Object.values(roomUsage).filter(r => r.name);
  entries.sort((a, b) => b.hours - a.hours);

  // Stats
  const totalBookedHours = entries.reduce((s, e) => s + e.hours, 0);
  const avgUtil = entries.length ? (entries.reduce((s, e) => s + (e.hours / totalAvailHours * 100), 0) / entries.length) : 0;
  const most = entries[0] || { name: '—' };
  const least = entries.filter(e => e.bookings > 0).pop() || entries[entries.length - 1] || { name: '—' };

  document.getElementById('stat-util-avg').textContent = avgUtil.toFixed(1) + '%';
  document.getElementById('stat-util-most').textContent = most.name;
  document.getElementById('stat-util-least').textContent = least.name;
  document.getElementById('stat-util-hours').textContent = totalBookedHours.toFixed(0) + 'h';

  // Render utilization bars
  const barsDiv = document.getElementById('utilization-bars');
  if (!entries.length) {
    barsDiv.innerHTML = '<div class="empty-state" style="padding:40px"><div style="font-size:48px;margin-bottom:12px">📊</div><div style="font-weight:600">No utilization data</div></div>';
    return;
  }
  barsDiv.innerHTML = entries.map(e => {
    const pct = Math.min(100, (e.hours / totalAvailHours * 100));
    const color = pct >= 70 ? '#059669' : pct >= 40 ? '#F59E0B' : '#EF4444';
    return `<div style="display:flex;align-items:center;gap:12px">
      <div style="width:140px;font-size:13px;font-weight:600;color:#1F2937;text-align:right;flex-shrink:0">${escHtml(e.name)}</div>
      <div style="flex:1;background:#F3F4F6;border-radius:8px;height:28px;overflow:hidden;position:relative">
        <div style="width:${pct}%;background:${color};height:100%;border-radius:8px;transition:width 0.6s ease"></div>
        <span style="position:absolute;right:8px;top:50%;transform:translateY(-50%);font-size:11px;font-weight:700;color:#1F2937">${pct.toFixed(1)}%</span>
      </div>
      <div style="width:80px;font-size:12px;color:#6B7280;flex-shrink:0">${e.hours.toFixed(1)}h / ${e.bookings} res.</div>
    </div>`;
  }).join('');

  // Render heatmap
  renderUtilizationHeatmap();
}

function renderUtilizationHeatmap() {
  const dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  const hours = Array.from({length: 12}, (_, i) => i + 7); // 7AM to 6PM
  // Count bookings per day-of-week per hour
  const grid = {};
  dayNames.forEach((d, di) => { grid[di] = {}; hours.forEach(h => { grid[di][h] = 0; }); });

  const approvedRes = reservations.filter(r => r.status === 'approved' || r.status === 'completed');
  approvedRes.forEach(r => {
    const start = new Date(r.start_datetime);
    const end = new Date(r.end_datetime);
    const dow = start.getDay();
    for (let h = start.getHours(); h <= Math.min(end.getHours(), 18); h++) {
      if (h >= 7 && h <= 18 && grid[dow] && grid[dow][h] !== undefined) grid[dow][h]++;
    }
  });

  const maxVal = Math.max(1, ...Object.values(grid).flatMap(d => Object.values(d)));

  const container = document.getElementById('utilization-heatmap');
  let html = '<table style="width:100%;border-collapse:collapse;font-size:12px">';
  html += '<thead><tr><th style="padding:6px;text-align:left"></th>';
  hours.forEach(h => { html += `<th style="padding:6px;text-align:center;color:#6B7280;font-weight:600">${h > 12 ? (h-12)+'PM' : h+'AM'}</th>`; });
  html += '</tr></thead><tbody>';

  dayNames.forEach((name, di) => {
    html += `<tr><td style="padding:6px;font-weight:600;color:#1F2937">${name}</td>`;
    hours.forEach(h => {
      const val = grid[di][h];
      const intensity = val / maxVal;
      const bg = val === 0 ? '#F9FAFB' : `rgba(5, 150, 105, ${0.15 + intensity * 0.85})`;
      const textColor = intensity > 0.5 ? '#fff' : '#4B5563';
      html += `<td style="padding:6px;text-align:center;background:${bg};color:${textColor};border-radius:4px;font-weight:${val > 0 ? '700' : '400'}">${val || '·'}</td>`;
    });
    html += '</tr>';
  });
  html += '</tbody></table>';
  container.innerHTML = html;
}

function exportUtilization(format) {
  const days = parseInt(document.getElementById('util-period-filter')?.value || '30');
  const cutoff = new Date();
  cutoff.setDate(cutoff.getDate() - days);
  const totalAvailHours = days * 8;
  const roomUsage = {};
  rooms.forEach(r => { roomUsage[r.facility_id] = { name: r.name, hours: 0, bookings: 0 }; });
  reservations.filter(r => r.status === 'approved' || r.status === 'completed').forEach(r => {
    const start = new Date(r.start_datetime);
    if (start >= cutoff && roomUsage[r.facility_id]) {
      roomUsage[r.facility_id].hours += Math.max(0, (new Date(r.end_datetime) - start) / 3600000);
      roomUsage[r.facility_id].bookings++;
    }
  });
  const headers = ['Room', 'Booked Hours', 'Bookings', 'Utilization Rate'];
  const rows = Object.values(roomUsage).filter(r => r.name).sort((a, b) => b.hours - a.hours)
    .map(e => [e.name, e.hours.toFixed(1), e.bookings, (e.hours / totalAvailHours * 100).toFixed(1) + '%']);
  if (format === 'csv') ExportHelper.exportCSV('Room_Utilization', headers, rows);
  else ExportHelper.exportPDF('Room_Utilization', 'Facilities — Room Utilization Report', headers, rows, { subtitle: 'Last ' + days + ' days' });
}

function exportApprovedBookings(format) {
  const now = new Date();
  const approved = reservations.filter(r => r.status === 'approved' || r.status === 'completed').map(r => {
    const start = new Date(r.start_datetime);
    const end   = new Date(r.end_datetime);
    let roomStat = 'Pending';
    if (r.status === 'completed' || now > end) roomStat = 'Available';
    else if (now >= start && now <= end) roomStat = 'Occupied';
    return { ...r, _roomLabel: roomStat };
  });
  const headers = ['Code', 'Facility', 'Event / Purpose', 'Type', 'Date & Time', 'Department', 'Room Status'];
  const rows = approved.map(r => [
    r.reservation_code || '', r.facility_name || '', r.event_title || r.purpose || '',
    r.reservation_type || 'regular',
    (r.start_datetime || '') + ' — ' + (r.end_datetime || ''),
    r.department || '', r._roomLabel
  ]);
  if (format === 'csv') {
    ExportHelper.exportCSV('Approved_Bookings', headers, rows);
  } else {
    ExportHelper.exportPDF('Approved_Bookings', 'Facilities — Approved Bookings', headers, rows, { landscape: true, subtitle: approved.length + ' bookings' });
  }
}

// ═══════════════════════════════════════════════════════
// HR2 INTEGRATION — Data Display Logic
// ═══════════════════════════════════════════════════════

let hr2Loaded = false;
let hr2EmployeesData = [];
const HR2_BRIDGE = '../../api/hr2.php';

// Sub-tab switcher
function switchHR2Tab(panelId, btn) {
  document.querySelectorAll('#hr2-sub-tabs .sub-tab').forEach(t => t.classList.remove('active'));
  if (btn) btn.classList.add('active');
  document.querySelectorAll('.hr2-panel').forEach(p => p.style.display = 'none');
  const panel = document.getElementById(panelId);
  if (panel) panel.style.display = '';
}

// Initialize HR2 tab (called when tab shown)
async function hr2InitTab() {
  if (hr2Loaded) return;
  hr2Loaded = true;
  await hr2CheckConnection();
  hr2LoadEmployees();
  hr2LoadLeaves();
  hr2LoadTraining();
  hr2LoadSuccessors();
  hr2LoadJobs();
}

function hr2RefreshAll() {
  hr2Loaded = false;
  if (window.hr2) window.hr2.clearCache();
  hr2InitTab();
}

// Connection check
async function hr2CheckConnection() {
  const dot = document.getElementById('hr2-status-dot');
  const txt = document.getElementById('hr2-status-text');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=health').then(r => r.json());
    if (res.hr2_alive) {
      if (dot) dot.style.background = '#10B981';
      if (txt) { txt.textContent = 'HR2 Connected'; txt.style.color = '#059669'; }
    } else {
      if (dot) dot.style.background = '#EF4444';
      if (txt) { txt.textContent = 'HR2 Unreachable — showing cached/empty data'; txt.style.color = '#DC2626'; }
    }
  } catch {
    if (dot) dot.style.background = '#EF4444';
    if (txt) { txt.textContent = 'HR2 Unreachable — check server'; txt.style.color = '#DC2626'; }
  }
}

// ── Employees ──
async function hr2LoadEmployees() {
  const tbody = document.getElementById('hr2-emp-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=employees').then(r => r.json());
    const employees = res.data || [];
    hr2EmployeesData = employees;
    document.getElementById('hr2-stat-employees').textContent = employees.length;
    hr2RenderEmployees(employees);
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#EF4444">Failed to load employees: ' + err.message + '</td></tr>';
  }
}

function hr2RenderEmployees(list) {
  const tbody = document.getElementById('hr2-emp-tbody');
  if (!list.length) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No employees found</td></tr>';
    return;
  }
  tbody.innerHTML = list.slice(0, 100).map(e => {
    const name = e.full_name || e.firstname || e.name || '—';
    const email = e.email || '—';
    const dept = e.department || e.dept || '—';
    const job = e.job_title || (e.job && e.job.job_title) || e.position || '—';
    const empId = e.employee_id || '—';
    const status = e.employment_status || e.status || 'active';
    const statusColor = status === 'active' || status === 'Active' ? '#059669' : '#DC2626';
    return `<tr>
      <td><span style="font-weight:600;color:#4B5563">${empId}</span></td>
      <td>${name}</td>
      <td style="color:#6B7280;font-size:12px">${email}</td>
      <td>${dept}</td>
      <td>${job}</td>
      <td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${statusColor}20;color:${statusColor}">${status}</span></td>
    </tr>`;
  }).join('');
}

function hr2SearchEmployees() {
  const q = (document.getElementById('hr2-emp-search').value || '').toLowerCase();
  if (!q) { hr2RenderEmployees(hr2EmployeesData); return; }
  const filtered = hr2EmployeesData.filter(e => {
    const n = (e.full_name || e.firstname || e.name || '').toLowerCase();
    const em = (e.email || '').toLowerCase();
    const id = (e.employee_id || '').toLowerCase();
    return n.includes(q) || em.includes(q) || id.includes(q);
  });
  hr2RenderEmployees(filtered);
}

// ── Leaves ──
async function hr2LoadLeaves() {
  const tbody = document.getElementById('hr2-leaves-tbody');
  const status = document.getElementById('hr2-leave-filter')?.value || '';
  try {
    let url = HR2_BRIDGE + '?action=leaves';
    if (status) url += '&status=' + status;
    const res = await fetch(url).then(r => r.json());
    let leaves = res.data || [];
    if (Array.isArray(leaves.data)) leaves = leaves.data; // paginated
    const pendingCount = leaves.filter(l => l.status === 'pending').length;
    document.getElementById('hr2-stat-leaves').textContent = pendingCount || leaves.length;
    if (!leaves.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">No leave requests found</td></tr>';
      return;
    }
    tbody.innerHTML = leaves.slice(0, 50).map(l => {
      const name = l.employee_name || l.employee_email || l.employee_id || '—';
      const type = l.leave_type || '—';
      const start = l.start_date || '—';
      const end = l.end_date || '—';
      const days = l.days_requested || l.total_days || '—';
      const st = l.status || '—';
      const reason = l.reason || l.remarks || '—';
      const stColor = st === 'approved' ? '#059669' : st === 'pending' ? '#D97706' : '#DC2626';
      return `<tr>
        <td>${name}</td><td>${type}</td><td>${start}</td><td>${end}</td><td>${days}</td>
        <td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td>
        <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:12px;color:#6B7280">${reason}</td>
      </tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#EF4444">Failed to load leaves: ' + err.message + '</td></tr>';
  }
}

// ── Training Bookings ──
let hr2TrainingData = []; // Store all training bookings for filtering
let hr2TrainingLoaded = false;

// Load training bookings for the sub-tab in tab-monitoring
async function hr2LoadTrainingSubtab() {
  if (hr2TrainingLoaded) return;
  hr2TrainingLoaded = true;
  await hr2CheckConnection();
  await hr2LoadTraining();
}

function hr2RefreshTraining() {
  hr2TrainingLoaded = false;
  hr2TrainingData = [];
  if (window.hr2) window.hr2.clearCache();
  hr2LoadTrainingSubtab();
}

async function hr2LoadTraining() {
  const tbody = document.getElementById('hr2-training-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=training_bookings').then(r => r.json());
    let bookings = res.data || [];
    if (Array.isArray(bookings.data)) bookings = bookings.data;
    hr2TrainingData = bookings;
    const countEl = document.getElementById('hr2-training-count');
    if (countEl) countEl.textContent = bookings.length + ' booking(s)';
    hr2RenderTrainingTable(bookings);
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:#EF4444">Failed to load training bookings: ' + err.message + '</td></tr>';
  }
}

function hr2FilterTraining() {
  const statusFilter = document.getElementById('hr2-training-filter-status').value;
  const searchVal = (document.getElementById('hr2-training-search').value || '').toLowerCase();
  let filtered = hr2TrainingData;
  if (statusFilter) filtered = filtered.filter(b => (b.status || '').toLowerCase() === statusFilter);
  if (searchVal) filtered = filtered.filter(b => {
    const text = (b.course_name || '') + ' ' + (b.title || '') + ' ' + (b.booking_code || '') + ' ' + (b.location || '') + ' ' + (b.facilitator || '');
    return text.toLowerCase().includes(searchVal);
  });
  hr2RenderTrainingTable(filtered);
}

function hr2RenderTrainingTable(bookings) {
  const tbody = document.getElementById('hr2-training-tbody');
  if (!bookings.length) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:#9CA3AF">No training bookings found</td></tr>';
    return;
  }
  tbody.innerHTML = bookings.slice(0, 100).map(b => {
    const code = b.booking_code || '—';
    const course = b.course_name || b.title || '—';
    const date = b.session_date || '—';
    const time = (b.start_time || '—') + ' – ' + (b.end_time || '');
    const loc = b.location || '—';
    const fac = b.facilitator || '—';
    const att = b.attendee_count || (b.attendees ? (typeof b.attendees === 'string' ? JSON.parse(b.attendees || '[]').length : (Array.isArray(b.attendees) ? b.attendees.length : 0)) : '—');
    const st = b.status || '—';
    const stColor = st === 'confirmed' || st === 'approved' ? '#059669' : st === 'pending' ? '#D97706' : st === 'cancelled' ? '#DC2626' : '#6B7280';
    const isPending = st === 'pending';
    const actionBtn = isPending
      ? `<button class="btn btn-primary btn-sm" onclick="hr2ReserveTraining('${escHtml(code)}','${escHtml(course)}','${escHtml(date)}','${escHtml(loc)}')" title="Reserve Room for this Training">📌 Reserve</button>`
      : `<span style="color:#9CA3AF;font-size:12px">${st === 'confirmed' || st === 'approved' ? '✅ Booked' : '—'}</span>`;
    return `<tr${isPending ? ' style="background:#FFFBEB"' : ''}>
      <td style="font-weight:600">${code}</td><td>${course}</td><td>${date}</td><td style="font-size:12px">${time}</td>
      <td>${loc}</td><td>${fac}</td><td style="text-align:center">${att}</td>
      <td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td>
      <td>${actionBtn}</td>
    </tr>`;
  }).join('');
}

// Quick-reserve a room for an HR2 training booking (Training Hall = Level 1 Normal)
function hr2ReserveTraining(bookingCode, course, date, location) {
  // Auto-detect best room: prefer Training Hall (Level 1 · Normal)
  const trainingRooms = rooms.filter(r => 
    r.status !== 'maintenance' && 
    (r.name || '').toLowerCase().includes('training')
  ).sort((a, b) => {
    const aLvl = a.room_level ? parseInt(a.room_level) : detectRoomLevel(a.name);
    const bLvl = b.room_level ? parseInt(b.room_level) : detectRoomLevel(b.name);
    return aLvl - bLvl;
  });

  const lvl1Rooms = rooms.filter(r => r.status !== 'maintenance' && (r.room_level ? parseInt(r.room_level) : detectRoomLevel(r.name)) === 1);

  // Pick the best room: training rooms first, then any Level 1 room
  const bestRoom = trainingRooms[0] || lvl1Rooms[0] || rooms[0];
  const bestRoomId = bestRoom ? bestRoom.facility_id : null;
  const detectedLevel = bestRoom ? (bestRoom.room_level ? parseInt(bestRoom.room_level) : detectRoomLevel(bestRoom.name)) : 1;
  const detectedLevelLabel = ROOM_LEVEL_LABELS[detectedLevel] || 'Normal';

  // Training Hall is Level 2 · VIP — regular reservation type for training
  const autoResType = 'regular';

  // Pre-fill the reservation modal with training details and auto-detected room
  openReservationModal(bestRoom?.name, bestRoom?.type, bestRoomId, autoResType);
  setTimeout(() => {
    const eventEl = document.getElementById('res-event');
    const purposeEl = document.getElementById('res-purpose');
    const startEl = document.getElementById('res-start');
    if (eventEl) eventEl.value = '🏫 Training: ' + course;
    if (purposeEl) purposeEl.value = 'HR2 Training Booking ' + bookingCode + ' — ' + course + (location ? ' at ' + location : '');
    if (startEl && date && date !== '—') {
      // Set the date portion, user picks the time
      const dateFormatted = date.includes('T') ? date : date + 'T09:00';
      startEl.value = dateFormatted;
    }
    // Auto-select the best room in the dropdown
    if (bestRoomId) {
      const facilityEl = document.getElementById('res-facility');
      if (facilityEl) facilityEl.value = bestRoomId;
    }
    // Set department to HR
    const deptEl = document.getElementById('res-dept');
    if (deptEl) {
      for (let i = 0; i < deptEl.options.length; i++) {
        if (deptEl.options[i].text.toLowerCase().includes('human')) { deptEl.selectedIndex = i; break; }
      }
    }

    // Show auto-detection info banner
    const rulesEl = document.getElementById('res-room-rules');
    if (rulesEl) {
      const autoDetectInfo = '<div style="background:#DBEAFE;border:1px solid #93C5FD;padding:10px 14px;border-radius:8px;font-size:12px;color:#1D4ED8;margin-bottom:8px">'
        + '🏫 <strong>HR2 Auto-Detection:</strong> Automatically assigned <b>' + escHtml(bestRoom?.name || 'N/A') + '</b> '
        + '(Level ' + detectedLevel + ' · ' + detectedLevelLabel + ') for this training session.</div>';
      rulesEl.innerHTML = autoDetectInfo + rulesEl.innerHTML;
    }
  }, 200);
}

// ── Successors ──
async function hr2LoadSuccessors() {
  const tbody = document.getElementById('hr2-successors-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=successors').then(r => r.json());
    const records = res.data || [];
    document.getElementById('hr2-stat-successors').textContent = records.length;
    if (!records.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No successor records found</td></tr>';
      return;
    }
    tbody.innerHTML = records.slice(0, 50).map(s => {
      const name = s.employee_name || '—';
      const cJob = s.current_job || s.job_title || '—';
      const pJob = s.potential_job || '—';
      const score = s.assessment_score != null ? parseFloat(s.assessment_score).toFixed(1) + '%' : '—';
      const st = s.status || '—';
      const date = s.created_at ? new Date(s.created_at).toLocaleDateString() : '—';
      const stColor = st === 'approved' || st === 'ready' ? '#059669' : st === 'pending' || st === 'under_review' ? '#D97706' : '#6B7280';
      return `<tr>
        <td style="font-weight:600">${name}</td><td>${cJob}</td><td>${pJob}</td>
        <td style="text-align:center;font-weight:600">${score}</td>
        <td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td>
        <td style="font-size:12px;color:#6B7280">${date}</td>
      </tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#EF4444">Failed to load successors: ' + err.message + '</td></tr>';
  }
}

// ── Jobs ──
async function hr2LoadJobs() {
  const tbody = document.getElementById('hr2-jobs-tbody');
  try {
    const res = await fetch(HR2_BRIDGE + '?action=jobs').then(r => r.json());
    let jobs = res.data || [];
    if (!Array.isArray(jobs)) jobs = Object.values(jobs);
    if (!jobs.length) {
      tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:30px;color:#9CA3AF">No job titles found</td></tr>';
      return;
    }
    tbody.innerHTML = jobs.slice(0, 100).map(j => {
      const title = j.job_title || j.title || j.name || (typeof j === 'string' ? j : '—');
      const dept = j.department || j.dept || '—';
      const cat = j.category || j.type || '—';
      return `<tr><td style="font-weight:600">${title}</td><td>${dept}</td><td>${cat}</td></tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;padding:30px;color:#EF4444">Failed to load jobs: ' + err.message + '</td></tr>';
  }
}

// ═══════════════════════════════════════════════════════
// HR4 INTEGRATION — Data Display Logic
// ═══════════════════════════════════════════════════════

let hr4Loaded = false;
let hr4EmployeesData = [];
const HR4_BRIDGE = '../../api/hr4.php';

// Sub-tab switcher
function switchHR4Tab(panelId, btn) {
  document.querySelectorAll('#hr4-sub-tabs .sub-tab').forEach(t => t.classList.remove('active'));
  if (btn) btn.classList.add('active');
  document.querySelectorAll('.hr4-panel').forEach(p => p.style.display = 'none');
  const panel = document.getElementById(panelId);
  if (panel) panel.style.display = '';
}

async function hr4InitTab() {
  if (hr4Loaded) return;
  hr4Loaded = true;
  hr4CheckConnection();
  hr4LoadEmployees();
  hr4LoadPayslips();
  hr4LoadContracts();
  hr4LoadPositions();
  hr4LoadCompensation();
  hr4LoadGovIds();
  hr4LoadDepartments();
}

function hr4RefreshAll() {
  hr4Loaded = false;
  hr4InitTab();
}

// ── Connection Check ──
async function hr4CheckConnection() {
  const dot = document.getElementById('hr4-status-dot');
  const txt = document.getElementById('hr4-status-text');
  try {
    const res = await fetch(HR4_BRIDGE + '?action=health').then(r => r.json());
    if (res.hr4_alive) {
      dot.style.background = '#059669'; dot.style.animation = 'none';
      txt.textContent = '✓ Connected to HR4 (' + (res.domain || 'hr4.microfinancial-1.com') + ')';
      txt.style.color = '#059669';
      document.getElementById('stat-hr4').innerHTML = '<span style="color:#059669;font-weight:700">Online</span>';
    } else {
      dot.style.background = '#EF4444'; dot.style.animation = 'none';
      txt.textContent = '✗ HR4 API unreachable'; txt.style.color = '#EF4444';
      document.getElementById('stat-hr4').innerHTML = '<span style="color:#EF4444">Offline</span>';
    }
  } catch {
    dot.style.background = '#EF4444'; dot.style.animation = 'none';
    txt.textContent = '✗ HR4 bridge error'; txt.style.color = '#EF4444';
    document.getElementById('stat-hr4').innerHTML = '<span style="color:#EF4444">Error</span>';
  }
}

// ── Employees ──
async function hr4LoadEmployees() {
  const tbody = document.getElementById('hr4-emp-tbody');
  try {
    const res = await fetch(HR4_BRIDGE + '?action=employees').then(r => r.json());
    hr4EmployeesData = res.data || [];
    document.getElementById('hr4-stat-employees').textContent = hr4EmployeesData.length;
    hr4RenderEmployees(hr4EmployeesData);
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#EF4444">Failed to load employees: ' + err.message + '</td></tr>';
  }
}

function hr4RenderEmployees(list) {
  const tbody = document.getElementById('hr4-emp-tbody');
  if (!list.length) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">No employees found</td></tr>';
    return;
  }
  tbody.innerHTML = list.slice(0, 100).map(e => {
    const id = e.employee_id || '—';
    const name = e.full_name || '—';
    const email = e.email || '—';
    const dept = (e.position && e.position.department) || '—';
    const job = (e.position && e.position.job && e.position.job.job_title) || (e.job_title && e.job_title.job_title) || '—';
    const st = e.employment_status || e.status || '—';
    const hired = e.hired_date ? new Date(e.hired_date).toLocaleDateString() : '—';
    const stColor = ['active','regular'].includes((st+'').toLowerCase()) ? '#059669' : ['terminated','resigned','separated'].includes((st+'').toLowerCase()) ? '#EF4444' : '#D97706';
    return `<tr>
      <td style="font-weight:600;font-size:12px">${id}</td>
      <td style="font-weight:600">${name}</td>
      <td style="font-size:12px;color:#6B7280">${email}</td>
      <td>${dept}</td>
      <td>${job}</td>
      <td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td>
      <td style="font-size:12px;color:#6B7280">${hired}</td>
    </tr>`;
  }).join('');
}

function hr4SearchEmployees() {
  const q = (document.getElementById('hr4-emp-search').value || '').toLowerCase();
  if (!q) { hr4RenderEmployees(hr4EmployeesData); return; }
  const filtered = hr4EmployeesData.filter(e => {
    return (e.full_name || '').toLowerCase().includes(q)
      || (e.email || '').toLowerCase().includes(q)
      || (e.employee_id || '').toLowerCase().includes(q)
      || ((e.position && e.position.department) || '').toLowerCase().includes(q);
  });
  hr4RenderEmployees(filtered);
}

// ── Payslips ──
async function hr4LoadPayslips() {
  const tbody = document.getElementById('hr4-payslips-tbody');
  try {
    const empFilter = (document.getElementById('hr4-payslip-filter')?.value || '').trim();
    let url = HR4_BRIDGE + '?action=payslips';
    if (empFilter) url += '&employee_id=' + encodeURIComponent(empFilter);
    const res = await fetch(url).then(r => r.json());
    const payslips = res.data || [];
    document.getElementById('hr4-stat-payslips').textContent = payslips.length;
    if (!payslips.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">No payslips found</td></tr>';
      return;
    }
    tbody.innerHTML = payslips.slice(0, 100).map(p => {
      const input = p.payroll_data_input || p;
      const result = p.payroll_result || p.result || {};
      const period = (p.period || p.pay_period || {});
      const empName = input.full_name || input.employee_id || '—';
      const periodLabel = period.month_year || period.period_name || '—';
      const base = parseFloat(input.base_salary || 0);
      const gross = parseFloat(result.gross_pay || base);
      const ded = parseFloat(result.total_deductions || 0);
      const net = parseFloat(result.net_pay || 0);
      const st = input.status || result.status || 'paid';
      const stColor = st === 'paid' ? '#059669' : st === 'pending' ? '#D97706' : '#6B7280';
      const fmt = v => '₱' + v.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
      return `<tr>
        <td style="font-weight:600">${empName}</td>
        <td>${periodLabel}</td>
        <td style="text-align:right">${fmt(base)}</td>
        <td style="text-align:right;font-weight:600">${fmt(gross)}</td>
        <td style="text-align:right;color:#EF4444">${fmt(ded)}</td>
        <td style="text-align:right;font-weight:700;color:#059669">${fmt(net)}</td>
        <td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td>
      </tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#EF4444">Failed to load payslips: ' + err.message + '</td></tr>';
  }
}

// ── Contracts ──
async function hr4LoadContracts() {
  const tbody = document.getElementById('hr4-contracts-tbody');
  try {
    const days = document.getElementById('hr4-contract-filter')?.value || '0';
    let url = HR4_BRIDGE + '?action=contracts';
    if (days !== '0') url += '&expiring_within_days=' + days;
    const res = await fetch(url).then(r => r.json());
    const contracts = res.data || [];
    document.getElementById('hr4-stat-contracts').textContent = contracts.length;
    if (!contracts.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">No contracts found</td></tr>';
      return;
    }
    tbody.innerHTML = contracts.slice(0, 100).map(c => {
      const name = c.full_name || c.employee_id || '—';
      const dept = c.department || '—';
      const cno = c.contract_no || '—';
      const dur = c.contract_duration_months ? c.contract_duration_months + ' mo' : '—';
      const start = c.start_date ? new Date(c.start_date).toLocaleDateString() : '—';
      const end = c.end_date ? new Date(c.end_date).toLocaleDateString() : '—';
      const days = c.days_until_expiry;
      let daysLabel = '—', daysColor = '#6B7280';
      if (days !== null && days !== undefined) {
        if (days < 0) { daysLabel = 'Expired'; daysColor = '#EF4444'; }
        else if (days <= 7) { daysLabel = days + 'd'; daysColor = '#EF4444'; }
        else if (days <= 30) { daysLabel = days + 'd'; daysColor = '#F59E0B'; }
        else if (days <= 90) { daysLabel = days + 'd'; daysColor = '#D97706'; }
        else { daysLabel = days + 'd'; daysColor = '#059669'; }
      }
      return `<tr>
        <td style="font-weight:600">${name}</td>
        <td>${dept}</td>
        <td style="font-size:12px">${cno}</td>
        <td style="text-align:center">${dur}</td>
        <td style="font-size:12px">${start}</td>
        <td style="font-size:12px">${end}</td>
        <td style="text-align:center;font-weight:700;color:${daysColor}">${daysLabel}</td>
      </tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#EF4444">Failed to load contracts: ' + err.message + '</td></tr>';
  }
}

// ── Positions ──
async function hr4LoadPositions() {
  const tbody = document.getElementById('hr4-positions-tbody');
  try {
    const res = await fetch(HR4_BRIDGE + '?action=vacant_positions').then(r => r.json());
    const positions = res.data || [];
    document.getElementById('hr4-stat-positions').textContent = positions.length;
    if (!positions.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No vacant positions found</td></tr>';
      return;
    }
    tbody.innerHTML = positions.slice(0, 100).map(p => {
      const job = (p.job && p.job.job_title) || p.job_title || '—';
      const dept = p.department || '—';
      const loc = p.location || '—';
      const type = p.employment_type || '—';
      const salary = p.base_salary ? '₱' + parseFloat(p.base_salary).toLocaleString('en-PH') : '—';
      const st = p.status || 'open';
      const stColor = st === 'open' || st === 'vacant' ? '#059669' : '#6B7280';
      return `<tr>
        <td style="font-weight:600">${job}</td>
        <td>${dept}</td>
        <td>${loc}</td>
        <td>${type}</td>
        <td style="text-align:right">${salary}</td>
        <td><span style="padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;background:${stColor}20;color:${stColor}">${st}</span></td>
      </tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#EF4444">Failed to load positions: ' + err.message + '</td></tr>';
  }
}

// ── Compensation ──
async function hr4LoadCompensation() {
  const tbody = document.getElementById('hr4-compensation-tbody');
  try {
    const res = await fetch(HR4_BRIDGE + '?action=compensation').then(r => r.json());
    const records = res.data || [];
    if (!records.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">No compensation data found</td></tr>';
      return;
    }
    const fmt = v => '₱' + parseFloat(v || 0).toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
    tbody.innerHTML = records.slice(0, 100).map(c => {
      const name = c.full_name || c.employee_id || '—';
      const dept = c.department || '—';
      const base = fmt(c.base_salary);
      const allow = fmt(c.total_allowances);
      const ded = fmt(c.total_deductions);
      const net = fmt(c.net_salary);
      const payType = c.pay_type || '—';
      return `<tr>
        <td style="font-weight:600">${name}</td>
        <td>${dept}</td>
        <td style="text-align:right">${base}</td>
        <td style="text-align:right;color:#059669">${allow}</td>
        <td style="text-align:right;color:#EF4444">${ded}</td>
        <td style="text-align:right;font-weight:700;color:#7C3AED">${net}</td>
        <td style="text-align:center">${payType}</td>
      </tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#EF4444">Failed to load compensation: ' + err.message + '</td></tr>';
  }
}

// ── Government IDs ──
async function hr4LoadGovIds() {
  const tbody = document.getElementById('hr4-govids-tbody');
  try {
    const res = await fetch(HR4_BRIDGE + '?action=government_ids').then(r => r.json());
    const records = res.data || [];
    if (!records.length) {
      tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#9CA3AF">No government ID records found</td></tr>';
      return;
    }
    tbody.innerHTML = records.slice(0, 100).map(g => {
      const id = g.employee_id || '—';
      const name = g.full_name || '—';
      const dept = g.department || '—';
      const sss = g.sss_number || '<span style="color:#D1D5DB">—</span>';
      const tin = g.tin_number || '<span style="color:#D1D5DB">—</span>';
      const phil = g.philhealth_number || '<span style="color:#D1D5DB">—</span>';
      const pag = g.pagibig_number || '<span style="color:#D1D5DB">—</span>';
      return `<tr>
        <td style="font-weight:600;font-size:12px">${id}</td>
        <td style="font-weight:600">${name}</td>
        <td>${dept}</td>
        <td style="font-size:12px">${sss}</td>
        <td style="font-size:12px">${tin}</td>
        <td style="font-size:12px">${phil}</td>
        <td style="font-size:12px">${pag}</td>
      </tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;padding:30px;color:#EF4444">Failed to load government IDs: ' + err.message + '</td></tr>';
  }
}

// ── Departments ──
async function hr4LoadDepartments() {
  const tbody = document.getElementById('hr4-departments-tbody');
  try {
    const res = await fetch(HR4_BRIDGE + '?action=departments').then(r => r.json());
    const depts = res.data || [];
    if (!depts.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#9CA3AF">No departments found</td></tr>';
      return;
    }
    tbody.innerHTML = depts.map(d => {
      const name = d.department || '—';
      const loc = d.location || '—';
      const total = d.total || 0;
      const active = d.active || 0;
      const inactive = d.inactive || 0;
      const positions = (d.positions || []).join(', ') || '—';
      return `<tr>
        <td style="font-weight:600">${name}</td>
        <td>${loc}</td>
        <td style="text-align:center;font-weight:600">${total}</td>
        <td style="text-align:center;color:#059669;font-weight:600">${active}</td>
        <td style="text-align:center;color:#EF4444;font-weight:600">${inactive}</td>
        <td style="font-size:12px;color:#6B7280">${positions}</td>
      </tr>`;
    }).join('');
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;padding:30px;color:#EF4444">Failed to load departments: ' + err.message + '</td></tr>';
  }
}

// ═══════════════════════════════════════════════════════
//  ROOM USAGE LOGS
// ═══════════════════════════════════════════════════════
let roomLogsData = [];

async function loadRoomLogStats() {
  try {
    const res = await fetch(API + '?action=room_log_stats').then(r => r.json());
    if (!res.success) return;
    const s = res.data;
    document.getElementById('stat-log-total').textContent   = s.total_logs ?? 0;
    document.getElementById('stat-log-hours').textContent    = s.total_hours ?? '0';
    document.getElementById('stat-log-most-room').textContent = s.most_used_room ?? '—';
    document.getElementById('stat-log-cancelled').textContent = s.total_cancelled ?? 0;
    document.getElementById('stat-log-lvl1').textContent     = s.level_1_count ?? 0;
    document.getElementById('stat-log-lvl2').textContent     = s.level_2_count ?? 0;
    document.getElementById('stat-log-lvl3').textContent     = s.level_3_count ?? 0;
    // Also update the directory card
    document.getElementById('stat-room-logs').textContent    = s.total_logs ?? 0;
  } catch (e) { console.error('loadRoomLogStats:', e); }
}

async function loadRoomLogs() {
  const tbody = document.getElementById('room-logs-tbody');
  tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:#9CA3AF">Loading…</td></tr>';
  try {
    const params = new URLSearchParams({ action: 'list_room_logs' });
    const fRoom   = document.getElementById('log-filter-room')?.value;
    const fLevel  = document.getElementById('log-filter-level')?.value;
    const fType   = document.getElementById('log-filter-type')?.value;
    const fStatus = document.getElementById('log-filter-status')?.value;
    const fFrom   = document.getElementById('log-filter-from')?.value;
    const fTo     = document.getElementById('log-filter-to')?.value;
    if (fRoom)   params.set('facility_id', fRoom);
    if (fLevel)  params.set('room_level', fLevel);
    if (fType)   params.set('reservation_type', fType);
    if (fStatus) params.set('status', fStatus);
    if (fFrom)   params.set('date_from', fFrom);
    if (fTo)     params.set('date_to', fTo);

    const res = await fetch(API + '?' + params.toString()).then(r => r.json());
    if (!res.success) { tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:#EF4444">' + (res.message || 'Error') + '</td></tr>'; return; }
    roomLogsData = res.data || [];
    renderRoomLogs();
    // Also populate the room dropdown if empty
    populateRoomLogRoomFilter();
  } catch (err) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:#EF4444">Failed: ' + err.message + '</td></tr>';
  }
}

function renderRoomLogs() {
  const tbody = document.getElementById('room-logs-tbody');
  if (!roomLogsData.length) {
    tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:#9CA3AF">No room usage logs found</td></tr>';
    Paginator.setTotalItems('fac-room-logs', 0);
    Paginator.renderControls('fac-room-logs', 'room-logs-pagination');
    return;
  }
  const levelBadge = (lvl) => {
    const map = { 1: ['🔵','Normal','#3B82F6'], 2: ['⭐','VIP','#7C3AED'], 3: ['⚡','Important','#DC2626'] };
    const [icon, label, color] = map[lvl] || ['📋','—','#6B7280'];
    return `<span style="background:${color}15;color:${color};padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600">${icon} ${label}</span>`;
  };
  const typeBadge = (t) => {
    const map = { regular: ['📋','Regular','#3B82F6'], vip: ['⭐','VIP','#7C3AED'], emergency: ['⚡','Important','#DC2626'] };
    const [icon, label, color] = map[t] || ['📋', t, '#6B7280'];
    return `<span style="background:${color}15;color:${color};padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600">${icon} ${label}</span>`;
  };
  const statusBadge = (s) => {
    const map = { completed: ['✅','Completed','#059669'], cancelled: ['🚫','Cancelled','#EF4444'], no_show: ['❌','No Show','#F59E0B'] };
    const [icon, label, color] = map[s] || ['📋', s, '#6B7280'];
    return `<span style="background:${color}15;color:${color};padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600">${icon} ${label}</span>`;
  };

  var allRows = roomLogsData.map((log, idx) => {
    const start = log.start_datetime ? new Date(log.start_datetime) : null;
    const end   = log.end_datetime   ? new Date(log.end_datetime)   : null;
    const dateStr = start ? start.toLocaleDateString('en-US', { month:'short', day:'numeric', year:'numeric' }) : '—';
    const timeStr = start && end
      ? start.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' }) + ' – ' + end.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' })
      : '—';
    const dur = log.duration_minutes ? (log.duration_minutes >= 60 ? Math.floor(log.duration_minutes/60) + 'h ' + (log.duration_minutes%60 ? (log.duration_minutes%60) + 'm' : '') : log.duration_minutes + 'm') : '—';
    return `<tr>
      <td style="font-weight:600;color:#6366F1">#${log.log_id}</td>
      <td>
        <div style="font-weight:600">${log.facility_name || '—'}</div>
        <div style="font-size:11px;color:#9CA3AF">${levelBadge(log.room_level)}</div>
      </td>
      <td>
        <div style="font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${(log.event_title||'').replace(/"/g,'&quot;')}">${log.event_title || '—'}</div>
        <div style="font-size:11px;color:#9CA3AF">${log.purpose || ''}</div>
      </td>
      <td>${typeBadge(log.reservation_type)}</td>
      <td>
        <div style="font-weight:500">${dateStr}</div>
        <div style="font-size:11px;color:#9CA3AF">${timeStr}</div>
      </td>
      <td style="font-weight:600;text-align:center">${dur}</td>
      <td>${log.department || '—'}</td>
      <td>
        <div style="font-weight:500">${log.reserved_by_name || '—'}</div>
        <div style="font-size:11px;color:#9CA3AF">ID: ${log.reserved_by || '—'}</div>
      </td>
      <td>${statusBadge(log.status)}</td>
    </tr>`;
  });

  var paged = Paginator.paginate('fac-room-logs', allRows);
  tbody.innerHTML = paged.join('');
  Paginator.renderControls('fac-room-logs', 'room-logs-pagination');
}

function populateRoomLogRoomFilter() {
  const sel = document.getElementById('log-filter-room');
  if (!sel || sel.options.length > 1) return; // Already populated
  const rooms = {};
  roomLogsData.forEach(l => { if (l.facility_id && l.facility_name) rooms[l.facility_id] = l.facility_name; });
  Object.entries(rooms).forEach(([id, name]) => {
    const opt = document.createElement('option');
    opt.value = id; opt.textContent = name;
    sel.appendChild(opt);
  });
}

function exportRoomLogs(format) {
  if (!roomLogsData.length) { Swal.fire('No Data', 'No room usage logs to export', 'info'); return; }

  if (format === 'csv') {
    const headers = ['Log#','Reservation Code','Facility','Level','Type','Event','Purpose','Department','Reserved By','Start','End','Duration (min)','Status','Logged At'];
    const rows = roomLogsData.map(l => [
      l.log_id, l.reservation_code || '', l.facility_name || '', l.room_level || '',
      l.reservation_type || '', `"${(l.event_title||'').replace(/"/g,'""')}"`,
      `"${(l.purpose||'').replace(/"/g,'""')}"`, l.department || '',
      l.reserved_by_name || '', l.start_datetime || '', l.end_datetime || '',
      l.duration_minutes || '', l.status || '', l.logged_at || ''
    ]);
    const csv = [headers.join(','), ...rows.map(r => r.join(','))].join('\n');
    const blob = new Blob([csv], { type:'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'room_usage_logs_' + new Date().toISOString().slice(0,10) + '.csv';
    a.click();
    return;
  }

  // PDF export using jsPDF + autoTable
  if (typeof jspdf === 'undefined' && typeof window.jspdf === 'undefined') {
    Swal.fire('Error', 'jsPDF library not loaded', 'error'); return;
  }
  const { jsPDF } = window.jspdf;
  const doc = new jsPDF('landscape', 'mm', 'a4');
  doc.setFontSize(16);
  doc.text('Room Usage Logs', 14, 18);
  doc.setFontSize(9);
  doc.setTextColor(120);
  doc.text('Generated: ' + new Date().toLocaleString(), 14, 24);

  const tableData = roomLogsData.map(l => {
    const dur = l.duration_minutes?(l.duration_minutes>=60?Math.floor(l.duration_minutes/60)+'h '+(l.duration_minutes%60||'')+'m':l.duration_minutes+'m'):'—';
    const levelMap = {1:'Normal',2:'VIP',3:'Emergency'};
    return ['#'+l.log_id,l.facility_name||'—',levelMap[l.room_level]||'—',l.event_title||'—',(l.reservation_type||'—').charAt(0).toUpperCase()+(l.reservation_type||'—').slice(1),l.start_datetime?new Date(l.start_datetime).toLocaleString('en-US',{month:'short',day:'numeric',year:'numeric',hour:'2-digit',minute:'2-digit'}):'—',dur,l.department||'—',l.reserved_by_name||'—',(l.status||'—').charAt(0).toUpperCase()+(l.status||'—').slice(1)];
  });
  doc.autoTable({ startY:28, head:[['Log#','Facility','Level','Event','Type','Date & Time','Duration','Dept','By','Status']], body:tableData, styles:{fontSize:7.5,cellPadding:2}, headStyles:{fillColor:[99,102,241],textColor:255}, alternateRowStyles:{fillColor:[245,247,250]}, margin:{left:14,right:14} });
  doc.save('room_usage_logs_'+new Date().toISOString().slice(0,10)+'.pdf');
}

/* ─── Init ─── */
loadData().then(() => { showSection(location.hash); });
</script>
</body>
</html>