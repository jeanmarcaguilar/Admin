@php
// Get the authenticated user
$user = auth()->user();
// Get calendar bookings from database (passed from route)
$calendarBookings = $calendarBookings ?? [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Scheduling & Calendar Integrations</title>
  <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
  <style>
    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 4px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }
    :root {
      --primary-color: #28644c;
      --primary-light: #3f8a56;
      --primary-dark: #1a4d38;
      --accent-color: #3f8a56;
      --text-primary: #1f2937;
      --text-secondary: #4b5563;
      --bg-light: #f9fafb;
      --bg-card: #ffffff;
      --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    body {
      font-family: "Inter", sans-serif;
      background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
      color: var(--text-primary);
      line-height: 1.6;
      margin: 0;
      padding: 0;
    }
    .modal {
      display: none;
      background: rgba(0, 0, 0, 0.5);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 60;
      align-items: center;
      justify-content: center;
    }
    .modal.active {
      display: flex;
    }
    .modal > div:hover {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
      transition: box-shadow 0.2s ease-in-out;
    }
    #main-content {
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(10px);
      min-height: calc(100vh - 4rem);
      margin-left: auto;
      margin-right: auto;
      max-width: 1200px;
      width: 100%;
      padding: 0 1rem;
      transition: width 0.3s ease-in-out;
    }
    @media (min-width: 768px) {
      #main-content {
        width: calc(100% - 18rem);
      }
      #main-content.sidebar-closed {
        width: calc(100% - 4rem);
      }
    }
    .dashboard-container {
      transition: max-width 0.3s ease-in-out;
    }
    #sidebar.md\\:ml-0 ~ #main-content .dashboard-container {
      max-width: 1152px;
    }
    .dashboard-card {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      background: var(--bg-card);
      border-radius: 12px;
      box-shadow: var(--shadow-sm);
      overflow: hidden;
      position: relative;
      z-index: 1;
      border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .dashboard-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
    }
    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
      z-index: 2;
    }
    .activity-item {
      transition: all 0.2s ease-in-out;
      border-radius: 8px;
      margin: 4px 0;
      padding: 12px 16px;
    }
    .activity-item:hover {
      background-color: rgba(16, 185, 129, 0.05);
      transform: translateX(4px);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
    /* Hide upcoming events by default - only show when locked */
    #upcomingEventsList .dashboard-card {
      display: none;
    }
    #upcomingEventsList .dashboard-card.locked-visible {
      display: block;
    }
  </style>
</head>
<body>
  <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
    <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
      <div class="flex items-center space-x-4">
        <button id="toggle-btn" class="pl-2 focus:outline-none">
          <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
        </button>
        <h1 class="text-2xl font-bold tracking-tight">Scheduling & Calendar Integrations</h1>
      </div>
      <div class="flex items-center space-x-1">
        <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn">
          <i class="fa-solid fa-bell text-xl"></i>
          <span class="absolute top-1 right-1 bg-red-500 text-xs text-white rounded-full px-1">3</span>
        </button>
        <div class="flex items-center space-x-2 cursor-pointer px-3 py-2 transition duration-200" id="userMenuBtn" aria-label="User menu" aria-haspopup="true" aria-expanded="false">
          <i class="fa-solid fa-user text-[18px] bg-white text-[#28644c] px-2.5 py-2 rounded-full"></i>
          <span class="text-white font-medium">{{ $user->name }}</span>
          <i class="fa-solid fa-chevron-down text-sm"></i>
        </div>
      </div>
    </div>
  </nav>
  <script>
    (function(){
      // Utilities
      function qs(sel, root){ return (root||document).querySelector(sel); }
      function qsa(sel, root){ return Array.from((root||document).querySelectorAll(sel)); }
      function hide(el){ if(!el) return; el.classList.add('hidden'); el.style.display='none'; }
      function show(el){ if(!el) return; el.classList.remove('hidden'); el.style.display='block'; }

      // User menu toggle
      function toggleUserMenu(ev){
        try{
          if(ev){ ev.stopPropagation && ev.stopPropagation(); ev.stopImmediatePropagation && ev.stopImmediatePropagation(); }
          var btn = qs('#userMenuBtn');
          var menu = qs('#userMenuDropdown');
          var open = menu && menu.classList.contains('hidden');
          if(!menu) return;
          if(open){
            // Position under button
            var rect = btn.getBoundingClientRect();
            menu.style.position='fixed';
            menu.style.top = (rect.bottom + 8) + 'px';
            var width = menu.offsetWidth || 192;
            var left = Math.max(8, Math.min(rect.right - width, window.innerWidth - width - 8));
            menu.style.left = left + 'px';
            menu.style.right = 'auto';
            show(menu);
            btn && btn.setAttribute('aria-expanded','true');
          } else {
            hide(menu);
            btn && btn.setAttribute('aria-expanded','false');
          }
        }catch(e){}
      }

      // Notification toggle (if present)
      function toggleNotification(ev){
        try{
          if(ev){ ev.stopPropagation && ev.stopPropagation(); }
          var nd = qs('#notificationDropdown');
          if(!nd) return;
          if(nd.classList.contains('hidden')) show(nd); else hide(nd);
          // Close user menu when opening notifications
          hide(qs('#userMenuDropdown'));
          var ub = qs('#userMenuBtn'); ub && ub.setAttribute('aria-expanded','false');
        }catch(e){}
      }

      // Sidebar open/close
      function toggleSidebar(){
        var sidebar = qs('#sidebar');
        var overlay = qs('#overlay');
        if(!sidebar) return;
        var isClosed = sidebar.classList.contains('-ml-72');
        if(isClosed){ sidebar.classList.remove('-ml-72'); show(overlay); }
        else { sidebar.classList.add('-ml-72'); hide(overlay); }
      }

      // Dropdowns in sidebar
      function wireSidebarDropdowns(){
        qsa('#sidebar .has-dropdown').forEach(function(sec){
          var header = qs('div', sec);
          var caret = qs('.bx-chevron-down', header);
          var menu = qs('.dropdown-menu', sec);
          if(header && !header.__wired){
            header.__wired = true;
            header.addEventListener('click', function(){
              if(!menu) return;
              var hidden = menu.classList.contains('hidden');
              if(hidden){ menu.classList.remove('hidden'); menu.style.display='block'; caret && caret.classList.add('rotate-180'); }
              else { menu.classList.add('hidden'); menu.style.display='none'; caret && caret.classList.remove('rotate-180'); }
            });
          }
        });
      }

      // One-time init
      function init(){
        var ub = qs('#userMenuBtn'); if(ub && !ub.__wired){ ub.__wired=true; ub.addEventListener('click', toggleUserMenu); }
        var nb = qs('#notificationBtn'); if(nb && !nb.__wired){ nb.__wired=true; nb.addEventListener('click', toggleNotification); }
        var tb = qs('#toggle-btn'); if(tb && !tb.__wired){ tb.__wired=true; tb.addEventListener('click', function(e){ e.preventDefault(); toggleSidebar(); }); }
        var ov = qs('#overlay'); if(ov && !ov.__wired){ ov.__wired=true; ov.addEventListener('click', function(){ var s=qs('#sidebar'); s && s.classList.add('-ml-72'); hide(ov); }); }
        wireSidebarDropdowns();
        // Close menus on outside click / ESC
        document.addEventListener('click', function(e){
          var menu = qs('#userMenuDropdown'); var btn = qs('#userMenuBtn');
          if(menu && btn && !menu.contains(e.target) && !btn.contains(e.target)){ hide(menu); btn.setAttribute('aria-expanded','false'); }
          var nd = qs('#notificationDropdown'); var nb2 = qs('#notificationBtn');
          if(nd && nb2 && !nd.contains(e.target) && !nb2.contains(e.target)){ hide(nd); }
        });
        document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ hide(qs('#userMenuDropdown')); hide(qs('#notificationDropdown')); } });
      }

      if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', init, {once:true}); } else { init(); }
    })();
  </script>
  <script>
    (function(){
      if (typeof window.openCaseWithConfGate !== 'function'){
        window.openCaseWithConfGate = function(href){
          try{ if (window.sessionStorage) sessionStorage.setItem('confOtpPending','1'); }catch(_){ }
          if (href){ window.location.href = href; }
          return false;
        };
      }
    })();
  </script>

  <div id="notificationDropdown" class="hidden absolute right-4 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 text-gray-800 z-50" style="top: 4rem;">
    <div class="flex justify-between items-center px-4 py-2 border-b border-gray-200">
      <span class="font-semibold text-sm">Notifications</span>
      <span class="bg-red-600 text-white text-xs font-semibold rounded-full px-2 py-0.5">3 new</span>
    </div>
    <ul class="divide-y divide-gray-200 max-h-72 overflow-y-auto">
      <li class="flex items-start px-4 py-3 space-x-3">
        <div class="flex-shrink-0 mt-1">
          <div class="bg-green-200 text-green-700 rounded-full p-2">
            <i class="fas fa-user-plus"></i>
          </div>
        </div>
        <div class="flex-grow text-sm">
          <p class="font-semibold text-gray-900 leading-tight">Employee Onboarding</p>
          <p class="text-gray-600 leading-tight text-xs">New employee added: {{ $user->name }}</p>
          <p class="text-gray-400 text-xs mt-0.5">30 min ago</p>
        </div>
      </li>
      <li class="flex items-start px-4 py-3 space-x-3">
        <div class="flex-shrink-0 mt-1">
          <div class="bg-blue-200 text-blue-700 rounded-full p-2">
            <i class="fas fa-file-alt"></i>
          </div>
        </div>
        <div class="flex-grow text-sm">
          <p class="font-semibold text-gray-900 leading-tight">Report Generated</p>
          <p class="text-gray-600 leading-tight text-xs">Monthly report generated</p>
          <p class="text-gray-400 text-xs mt-0.5">2 hours ago</p>
        </div>
      </li>
      <li class="flex items-start px-4 py-3 space-x-3">
        <div class="flex-shrink-0 mt-1">
          <div class="bg-blue-200 text-blue-700 rounded-full p-2">
            <i class="fas fa-file-alt"></i>
          </div>
        </div>
        <div class="flex-grow text-sm">
          <p class="font-semibold text-gray-900 leading-tight">Document Uploaded</p>
          <p class="text-gray-600 leading-tight text-xs">New document uploaded</p>
          <p class="text-gray-400 text-xs mt-0.5">Yesterday</p>
        </div>
      </li>
    </ul>
    <div class="text-center py-2 border-t border-gray-200">
      <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
    </div>
  </div>

  <div class="flex w-full min-h-screen pt-16">
    <div id="overlay" class="hidden fixed inset-0 bg-black opacity-50 z-40"></div>
    <aside id="sidebar" class="bg-[#2f855A] text-white flex flex-col z-40 fixed top-16 bottom-0 w-72 -ml-72 md:sticky md:ml-0 transition-all duration-300 ease-in-out overflow-y-auto">
      <div class="department-header px-2 py-4 mx-2 border-b border-white/50">
        <h1 class="text-xl font-bold">Administrative Department</h1>
      </div>
      <div class="px-3 py-10 flex-1">
        <ul class="space-y-6">
          <li>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center font-medium space-x-2 text-lg hover:bg-white/30 px-3 py-2.5 rounded-lg whitespace-nowrap">
              <i class="bx bx-grid-alt"></i>
              <span>Dashboard</span>
            </a>
          </li>
          <li class="has-dropdown">
            <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-group"></i>
                <span>Visitor Management</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('visitors.registration') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-id-card mr-2"></i>Visitors Registration</a></li>
              <li><a href="{{ route('checkinout.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-transfer mr-2"></i>Check In/Out Tracking</a></li>
              
              <li><a href="{{ route('visitor.history.records') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-history mr-2"></i>Visitor History Records</a></li>
            </ul>
          </li>
          <li class="has-dropdown">
            <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-file"></i>
                <span>Document Management</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('document.upload.indexing') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-upload mr-2"></i>Document Upload & Indexing</a></li>
              <li><a href="{{ route('document.version.control') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-git-branch mr-2"></i>Version Control</a></li>
              <li><a href="{{ route('document.access.control.permissions') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-lock mr-2"></i>Access Control & Permissions</a></li>
              <li><a href="{{ route('document.archival.retention.policy') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-archive mr-2"></i>Archival & Retention Policy</a></li>
            </ul>
          </li>
          <li class="has-dropdown">
            <div class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-calendar-check"></i>
                <span>Facilities Management</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('room-equipment') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-door-open mr-2"></i>Room & Equipment Booking</a></li>
              <li><a href="{{ route('scheduling.calendar') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-calendar mr-2"></i>Scheduling & Calendar Integrations</a></li>
              <li><a href="{{ route('approval.workflow') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-circle mr-2"></i>Approval Workflow</a></li>
              <li><a href="{{ route('reservation.history') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-history mr-2"></i>Reservation History</a></li>
            </ul>
          </li>
          <li class="has-dropdown">
            <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
              <div class="flex items-center space-x-2">
                <i class="bx bx-file"></i>
                <span>Legal Management</span>
              </div>
              <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
            </div>
            <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
              <li><a href="{{ route('case.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg" onclick="return openCaseWithConfGate(this.href)"><i class="bx bx-briefcase mr-2"></i>Case Management</a></li>
              <li><a href="{{ route('contract.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-file-blank mr-2"></i>Contract Management</a></li>
              <li><a href="{{ route('compliance.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-double mr-2"></i>Compliance Tracking</a></li>
              <li><a href="{{ route('deadline.hearing.alerts') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-alarm mr-2"></i>Deadline & Hearing Alerts</a></li>
            </ul>
          </li>
          <li>
            <a href="#" class="flex items-center font-medium space-x-2 text-lg hover:bg-white/30 px-3 py-2.5 rounded-lg whitespace-nowrap">
              <i class="bx bx-user-shield"></i>
              <span>Administrator</span>
            </a>
          </li>
        </ul>
      </div>
      <div class="px-5 pb-6">
        <div class="bg-white rounded-md p-4 text-center text-[#2f855A] text-sm font-semibold select-none">
          Need Help?<br />
          Contact support team at<br />
          <button type="button" class="mt-2 bg-[#3f8a56] text-white text-sm font-semibold px-4 py-2 rounded-lg shadow-sm hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-[#3f8a56] transition-all duration-200">
            Contact Support
          </button>
        </div>
      </div>
    </aside>

    <main id="main-content" class="flex-1 p-6 w-full mt-16">
      <div class="dashboard-container max-w-7xl mx-auto">
        <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
          <h2 class="text-[#1a4d38] font-bold text-xl mb-1">Scheduling & Calendar Integrations</h2>
          <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Calendar Panel -->
            <section class="lg:col-span-2 dashboard-card">
              <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-[#1a4d38] font-bold text-sm">Calendar View</h3>
                <div class="flex items-center gap-2">
                  <button id="todayBtn" class="text-xs bg-[#28644c] text-white px-3 py-1.5 rounded-md hover:bg-[#2f855A]">Today</button>
                  <button id="prevMonthBtn" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md"><i class="fa-solid fa-chevron-left mr-1"></i>Prev</button>
                  <span id="monthLabel" class="text-sm font-semibold select-none min-w-[120px] text-center">{{ now()->format('F Y') }}</span>
                  <button id="nextMonthBtn" class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md">Next<i class="fa-solid fa-chevron-right ml-1"></i></button>
                </div>
              </div>
              <div class="p-6">
                <div class="grid grid-cols-7 gap-2 text-xs mb-2">
                  <div class="text-gray-500 text-center font-semibold py-2">Sun</div>
                  <div class="text-gray-500 text-center font-semibold py-2">Mon</div>
                  <div class="text-gray-500 text-center font-semibold py-2">Tue</div>
                  <div class="text-gray-500 text-center font-semibold py-2">Wed</div>
                  <div class="text-gray-500 text-center font-semibold py-2">Thu</div>
                  <div class="text-gray-500 text-center font-semibold py-2">Fri</div>
                  <div class="text-gray-500 text-center font-semibold py-2">Sat</div>
                </div>
                <div id="calendarGrid" class="grid grid-cols-7 gap-2"></div>
                <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                  <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-100 text-green-700 rounded"></span> Approved</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-100 text-yellow-800 rounded"></span> Pending</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-100 text-red-700 rounded"></span> Rejected</span>
                  </div>
                  <div id="eventCount" class="text-xs">0 events this month</div>
                </div>
              </div>
            </section>
            <aside class="space-y-4">
              <div class="dashboard-card">
                <div class="px-6 py-4 flex items-center justify-between">
                  <h3 class="text-[#1a4d38] font-bold text-sm">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-3">
                  <a href="{{ route('room-equipment') }}" class="w-full flex items-center justify-center gap-2 bg-[#28644c] text-white px-4 py-2.5 rounded-md hover:bg-[#2f855A] transition-colors text-sm font-medium">
                    <i class="bx bx-plus"></i> New Booking
                  </a>
                  <button id="lockAllBtn" class="w-full flex items-center justify-center gap-2 bg-orange-600 hover:bg-orange-700 text-white px-4 py-2.5 rounded-md transition-colors text-sm font-medium" onclick="lockAllReservations()">
                    <i class="bx bx-lock"></i> Lock All Reservations
                  </button>
                  <button id="exportBtn" class="w-full flex items-center justify-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2.5 rounded-md transition-colors text-sm font-medium">
                    <i class="bx bx-download"></i> Export Calendar
                  </button>
                  @if (Route::has('calendar.clear'))
                    <form method="POST" action="{{ route('calendar.clear') }}" class="w-full">
                      @csrf
                      <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2.5 rounded-md transition-colors text-sm font-medium">
                        <i class="bx bx-trash"></i> Clear All
                      </button>
                    </form>
                  @endif
                </div>
              </div>
              <div class="dashboard-card">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                  <h3 class="text-[#1a4d38] font-bold text-sm">Upcoming Events</h3>
                  <span id="upcomingCount" class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">0</span>
                </div>
                <div id="upcomingEventsList" class="max-h-80 overflow-y-auto">
                  @php
                    $upcoming = $calendarBookings;
                  @endphp
                  @if (!empty($upcoming))
                    <ul class="p-4 space-y-3 text-sm">
                      @foreach ($upcoming as $booking)
                        @php
                          $date = isset($booking['date']) ? \Carbon\Carbon::parse($booking['date']) : null;
                          $day = $date ? $date->format('d') : '--';
                          $monthShort = $date ? $date->format('M') : '';
                          $time = isset($booking['start_time']) && $booking['start_time']
                            ? (\Carbon\Carbon::createFromFormat('H:i', $booking['start_time'])->format('g:i A'))
                            : '';
                          $title = $booking['name'] ?? ($booking['title'] ?? 'Booking');
                          $status = strtolower($booking['status'] ?? 'pending');
                          $statusClass = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                          ][$status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <li class="activity-item flex space-x-3 hover:bg-gray-50 rounded-lg p-2 transition-colors cursor-pointer" onclick="showEventDetails({{ json_encode($booking) }})">
                          <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-green-100 text-green-700">
                            <span class="text-xs font-bold">{{ $day }}</span>
                          </div>
                          <div class="flex-grow">
                            <p class="font-semibold text-gray-800 flex items-center gap-2">
                              <span>{{ Str::limit($title, 25) }}</span>
                              <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusClass }}">{{ ucfirst($status) }}</span>
                            </p>
                            <p class="text-gray-500 text-xs">{{ $day }} {{ $monthShort }} @if($time) · {{ $time }} @endif</p>
                          </div>
                        </li>
                      @endforeach
                    </ul>
                  @else
                    <div class="p-6 text-sm text-gray-500">
                      <p class="text-center">No upcoming events.</p>
                    </div>
                  @endif
                </div>
              </div>
              <div class="dashboard-card">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                  <h3 class="text-[#1a4d38] font-bold text-sm">Integrations</h3>
                </div>
                <div class="p-6 space-y-4 text-sm">
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <i class="fa-brands fa-google text-red-500"></i>
                      <span>Google Calendar</span>
                    </div>
                    <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md">Connect</button>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <i class="fa-brands fa-microsoft text-blue-600"></i>
                      <span>Microsoft Outlook</span>
                    </div>
                    <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md">Connect</button>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                      <i class="fa-solid fa-link text-gray-600"></i>
                      <span>WebCal/iCal</span>
                    </div>
                    <button class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-md">Configure</button>
                  </div>
                </div>
              </div>
            </aside>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- Event Details Modal -->
  <div id="eventDetailsModal" class="modal hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg w-full max-w-md mx-4">
      <div class="flex justify-between items-center border-b px-6 py-4">
        <h3 class="text-xl font-semibold text-gray-900">Event Details</h3>
        <button onclick="closeEventDetails()" class="text-gray-400 hover:text-gray-500">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <div id="eventDetailsContent" class="p-6">
        <!-- Content will be loaded by JavaScript -->
      </div>
    </div>
  </div>

  <!-- Lock Reservations Confirmation Modal -->
  <div id="lockConfirmModal" class="modal hidden" aria-modal="true" role="dialog">
    <div class="bg-white rounded-lg w-full max-w-md mx-4">
      <div class="flex justify-between items-center border-b px-6 py-4">
        <h3 class="text-xl font-semibold text-gray-900">Lock All Reservations</h3>
        <button onclick="closeLockConfirmModal()" class="text-gray-400 hover:text-gray-500">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      <div class="p-6">
        <div class="flex items-center mb-4">
          <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center mr-4">
            <i class="fas fa-lock text-orange-600 text-xl"></i>
          </div>
          <div>
            <p class="text-gray-900 font-medium">Confirm Lock Action</p>
            <p class="text-gray-600 text-sm">This will secure all reservations for confidential data handling.</p>
          </div>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mb-4">
          <p class="text-orange-800 text-sm"><strong>Warning:</strong> This action will restrict access to all reservation data and may affect ongoing bookings.</p>
        </div>
        <div class="flex justify-end space-x-3">
          <button onclick="closeLockConfirmModal()" class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
            Cancel
          </button>
          <button onclick="confirmLockReservations()" class="bg-orange-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 shadow-sm transition-all duration-200">
            <i class="fas fa-lock mr-2"></i>Lock All Reservations
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modals -->
  <div id="userMenuDropdown" class="hidden absolute right-4 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" style="top: 4rem;" role="menu" aria-labelledby="userMenuBtn">
    <div class="py-4 px-6 border-b border-gray-100 text-center">
      <div class="w-14 h-14 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-2">
        <i class="fas fa-user-circle text-3xl"></i>
      </div>
      <p class="font-semibold text-[#28644c]">{{ $user->name }}</p>
      <p class="text-xs text-gray-400">Administrator</p>
    </div>
    <ul class="text-sm text-gray-700">
      <li><button id="openProfileBtn" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-user-circle mr-2"></i> My Profile</button></li>
      <li><button id="openAccountSettingsBtn" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-cog mr-2"></i> Account Settings</button></li>
      <li><button id="openPrivacySecurityBtn" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-shield-alt mr-2"></i> Privacy & Security</button></li>
      <li><button id="openSignOutBtn" class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</button></li>
    </ul>
  </div>

  <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="profile-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="profile-modal-title" class="font-semibold text-sm text-gray-900 select-none">My Profile</h3>
        <button id="closeProfileBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <div class="flex flex-col items-center mb-4">
          <div class="bg-[#28644c] rounded-full w-20 h-20 flex items-center justify-center mb-3">
            <i class="fas fa-user text-white text-3xl"></i>
          </div>
          <p class="font-semibold text-gray-900 text-base leading-5 mb-0.5">{{ $user->name }}</p>
          <p class="text-xs text-gray-500 leading-4">Administrator</p>
        </div>
        <form class="space-y-4">
          <div>
            <label for="emailProfile" class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
            <input id="emailProfile" type="email" readonly value="{{ $user->email }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
          </div>
          <div>
            <label for="phone" class="block text-xs font-semibold text-gray-700 mb-1">Phone</label>
            <input id="phone" type="text" readonly value="+1234567890" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
          </div>
          <div>
            <label for="department" class="block text-xs font-semibold text-gray-700 mb-1">Department</label>
            <input id="department" type="text" readonly value="Administrative" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
          </div>
          <div>
            <label for="location" class="block text-xs font-semibold text-gray-700 mb-1">Location</label>
            <input id="location" type="text" readonly value="Manila, Philippines" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
          </div>
          <div>
            <label for="joined" class="block text-xs font-semibold text-gray-700 mb-1">Joined</label>
            <input id="joined" type="text" readonly value="{{ $user->created_at->format('F d, Y') }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs text-gray-700 bg-white cursor-default" />
          </div>
          <div class="flex justify-end pt-2">
            <button id="closeProfileBtn2" type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="accountSettingsModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="account-settings-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="account-settings-modal-title" class="font-semibold text-sm text-gray-900 select-none">Account Settings</h3>
        <button id="closeAccountSettingsBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.update') }}" method="POST">
          @csrf
          @method('PATCH')
          <div>
            <label for="username" class="block mb-1 font-semibold">Username</label>
            <input id="username" name="username" type="text" value="{{ $user->name }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
          </div>
          <div>
            <label for="emailAccount" class="block mb-1 font-semibold">Email</label>
            <input id="emailAccount" name="email" type="email" value="{{ $user->email }}" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" />
          </div>
          <div>
            <label for="language" class="block mb-1 font-semibold">Language</label>
            <select id="language" name="language" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]">
              <option selected>English</option>
            </select>
          </div>
          <div>
            <label for="timezone" class="block mb-1 font-semibold">Time Zone</label>
            <select id="timezone" name="timezone" class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]">
              <option selected>Philippine Time (GMT+8)</option>
            </select>
          </div>
          <fieldset class="space-y-1">
            <legend class="font-semibold text-xs mb-1">Notifications</legend>
            <div class="flex items-center space-x-2">
              <input id="email-notifications" name="email_notifications" type="checkbox" checked class="w-3.5 h-3.5 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" />
              <label for="email-notifications" class="text-xs">Email notifications</label>
            </div>
            <div class="flex items-center space-x-2">
              <input id="browser-notifications" name="browser_notifications" type="checkbox" checked class="w-3.5 h-3.5 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" />
              <label for="browser-notifications" class="text-xs">Browser notifications</label>
            </div>
          </fieldset>
          <div class="flex justify-end space-x-3 pt-2">
            <button type="button" id="cancelAccountSettingsBtn" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
            <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="privacy-security-modal-title">
    <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">Privacy & Security</h3>
        <button id="closePrivacySecurityBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <form class="space-y-4 text-xs text-gray-700" action="{{ route('profile.update') }}" method="POST">
          @csrf
          @method('PATCH')
          <fieldset>
            <legend class="font-semibold mb-2 select-none">Change Password</legend>
            <label class="block mb-1 font-normal select-none" for="current-password">Current Password</label>
            <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="current-password" name="current_password" type="password"/>
            <label class="block mt-3 mb-1 font-normal select-none" for="new-password">New Password</label>
            <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="new-password" name="new_password" type="password"/>
            <label class="block mt-3 mb-1 font-normal select-none" for="confirm-password">Confirm New Password</label>
            <input class="w-full border border-gray-300 rounded px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-[#2f855A]" id="confirm-password" name="confirm_password" type="password"/>
          </fieldset>
          <fieldset>
            <legend class="font-semibold mb-1 select-none">Two-Factor Authentication</legend>
            <p class="text-[10px] mb-1 select-none">Enhance your account security</p>
            <div class="flex items-center justify-between">
              <span class="text-[10px] text-[#2f855A] font-semibold select-none">Status: Enabled</span>
              <button class="text-[10px] bg-gray-200 text-gray-700 rounded-lg px-3 py-1.5 font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200" type="button">Configure</button>
            </div>
          </fieldset>
          <fieldset>
            <legend class="font-semibold mb-1 select-none">Session Management</legend>
            <div class="bg-gray-100 rounded px-3 py-2 text-[10px] text-gray-700 select-none">
              <div class="font-semibold">Current Session</div>
              <div class="text-[9px] text-gray-500">Manila, Philippines • Chrome</div>
              <div class="inline-block mt-1 bg-green-100 text-green-700 text-[9px] font-semibold rounded px-2 py-0.5 select-none">Active</div>
            </div>
          </fieldset>
          <fieldset>
            <legend class="font-semibold mb-1 select-none">Privacy Settings</legend>
            <label class="flex items-center space-x-2 text-[10px] select-none">
              <input checked class="w-3 h-3" type="checkbox" name="show_profile" />
              <span>Show my profile to all employees</span>
            </label>
            <label class="flex items-center space-x-2 text-[10px] select-none mt-1">
              <input checked class="w-3 h-3" type="checkbox" name="log_activity" />
              <span>Log my account activity</span>
            </label>
          </fieldset>
          <div class="flex justify-end space-x-3 pt-2">
            <button class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200" id="cancelPrivacySecurityBtn" type="button">Cancel</button>
            <button class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200" type="submit">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="signOutModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="sign-out-modal-title">
    <div class="bg-white rounded-md shadow-lg w-[360px] max-w-full mx-4 text-center" role="document">
      <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
        <h3 id="sign-out-modal-title" class="font-semibold text-sm text-gray-900 select-none">Sign Out</h3>
        <button id="cancelSignOutBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
          <i class="fas fa-times text-xs"></i>
        </button>
      </div>
      <div class="px-8 pt-6 pb-8">
        <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
          <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
        </div>
        <p class="text-xs text-gray-600 mb-6">Are you sure you want to sign out of your account?</p>
        <div class="flex justify-center space-x-4">
          <button id="cancelSignOutBtn2" class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Sign Out</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sidebar = document.getElementById("sidebar");
      const mainContent = document.getElementById("main-content");
      const toggleBtn = document.getElementById("toggle-btn");
      const overlay = document.getElementById("overlay");
      const dropdownToggles = document.querySelectorAll(".has-dropdown > div");
      const notificationBtn = document.getElementById("notificationBtn");
      const notificationDropdown = document.getElementById("notificationDropdown");
      const userMenuBtn = document.getElementById("userMenuBtn");
      const userMenuDropdown = document.getElementById("userMenuDropdown");
      const profileModal = document.getElementById("profileModal");
      const openProfileBtn = document.getElementById("openProfileBtn");
      const closeProfileBtn = document.getElementById("closeProfileBtn");
      const closeProfileBtn2 = document.getElementById("closeProfileBtn2");
      const openAccountSettingsBtn = document.getElementById("openAccountSettingsBtn");
      const accountSettingsModal = document.getElementById("accountSettingsModal");
      const closeAccountSettingsBtn = document.getElementById("closeAccountSettingsBtn");
      const cancelAccountSettingsBtn = document.getElementById("cancelAccountSettingsBtn");
      const openPrivacySecurityBtn = document.getElementById("openPrivacySecurityBtn");
      const privacySecurityModal = document.getElementById("privacySecurityModal");
      const closePrivacySecurityBtn = document.getElementById("closePrivacySecurityBtn");
      const cancelPrivacySecurityBtn = document.getElementById("cancelPrivacySecurityBtn");
      const signOutModal = document.getElementById("signOutModal");
      const cancelSignOutBtn = document.getElementById("cancelSignOutBtn");
      const cancelSignOutBtn2 = document.getElementById("cancelSignOutBtn2");
      const openSignOutBtn = document.getElementById("openSignOutBtn");

      // Initialize sidebar state based on screen size
      if (window.innerWidth >= 768) {
        sidebar.classList.remove("-ml-72");
        mainContent.classList.add("md:ml-72", "sidebar-open");
      } else {
        sidebar.classList.add("-ml-72");
        mainContent.classList.remove("md:ml-72", "sidebar-open");
        mainContent.classList.add("sidebar-closed");
      }

      function toggleSidebar() {
        if (window.innerWidth >= 768) {
          sidebar.classList.toggle("md:-ml-72");
          mainContent.classList.toggle("md:ml-72");
          mainContent.classList.toggle("sidebar-open");
          mainContent.classList.toggle("sidebar-closed");
        } else {
          sidebar.classList.toggle("-ml-72");
          overlay.classList.toggle("hidden");
          document.body.style.overflow = sidebar.classList.contains("-ml-72") ? "" : "hidden";
          mainContent.classList.toggle("sidebar-open", !sidebar.classList.contains("-ml-72"));
          mainContent.classList.toggle("sidebar-closed", sidebar.classList.contains("-ml-72"));
        }
      }

      function closeAllDropdowns() {
        dropdownToggles.forEach((toggle) => {
          const dropdown = toggle.nextElementSibling;
          const chevron = toggle.querySelector(".bx-chevron-down");
          if (!dropdown.classList.contains("hidden")) {
            dropdown.classList.add("hidden");
            chevron.classList.remove("rotate-180");
          }
        });
      }

      dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", () => {
          const dropdown = toggle.nextElementSibling;
          const chevron = toggle.querySelector(".bx-chevron-down");
          dropdownToggles.forEach((otherToggle) => {
            if (otherToggle !== toggle) {
              otherToggle.nextElementSibling.classList.add("hidden");
              otherToggle.querySelector(".bx-chevron-down").classList.remove("rotate-180");
            }
          });
          dropdown.classList.toggle("hidden");
          chevron.classList.toggle("rotate-180");
        });
      });

      overlay.addEventListener("click", () => {
        sidebar.classList.add("-ml-72");
        overlay.classList.add("hidden");
        document.body.style.overflow = "";
        mainContent.classList.remove("sidebar-open");
        mainContent.classList.add("sidebar-closed");
      });

      toggleBtn.addEventListener("click", toggleSidebar);

      notificationBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        notificationDropdown.classList.toggle("hidden");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        signOutModal.classList.remove("active");
      });

      userMenuBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        userMenuDropdown.classList.toggle("hidden");
        const expanded = userMenuBtn.getAttribute("aria-expanded") === "true";
        userMenuBtn.setAttribute("aria-expanded", !expanded);
        notificationDropdown.classList.add("hidden");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        signOutModal.classList.remove("active");
      });

      openSignOutBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        signOutModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
      });

      openProfileBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        profileModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        accountSettingsModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
        signOutModal.classList.remove("active");
      });

      closeProfileBtn.addEventListener("click", () => {
        profileModal.classList.remove("active");
      });
      closeProfileBtn2.addEventListener("click", () => {
        profileModal.classList.remove("active");
      });

      openAccountSettingsBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        accountSettingsModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        privacySecurityModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
        signOutModal.classList.remove("active");
      });

      closeAccountSettingsBtn.addEventListener("click", () => {
        accountSettingsModal.classList.remove("active");
      });
      cancelAccountSettingsBtn.addEventListener("click", () => {
        accountSettingsModal.classList.remove("active");
      });

      openPrivacySecurityBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        privacySecurityModal.classList.add("active");
        userMenuDropdown.classList.add("hidden");
        userMenuBtn.setAttribute("aria-expanded", "false");
        profileModal.classList.remove("active");
        accountSettingsModal.classList.remove("active");
        notificationDropdown.classList.add("hidden");
        signOutModal.classList.remove("active");
      });

      closePrivacySecurityBtn.addEventListener("click", () => {
        privacySecurityModal.classList.remove("active");
      });
      cancelPrivacySecurityBtn.addEventListener("click", () => {
        privacySecurityModal.classList.remove("active");
      });

      cancelSignOutBtn.addEventListener("click", () => {
        signOutModal.classList.remove("active");
      });
      cancelSignOutBtn2.addEventListener("click", () => {
        signOutModal.classList.remove("active");
      });

      window.addEventListener("click", (e) => {
        if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
          notificationDropdown.classList.add("hidden");
        }
        if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
          userMenuDropdown.classList.add("hidden");
          userMenuBtn.setAttribute("aria-expanded", "false");
        }
        if (!profileModal.contains(e.target) && !openProfileBtn.contains(e.target)) {
          profileModal.classList.remove("active");
        }
        if (!accountSettingsModal.contains(e.target) && !openAccountSettingsBtn.contains(e.target)) {
          accountSettingsModal.classList.remove("active");
        }
        if (!privacySecurityModal.contains(e.target) && !openPrivacySecurityBtn.contains(e.target)) {
          privacySecurityModal.classList.remove("active");
        }
        if (!signOutModal.contains(e.target)) {
          signOutModal.classList.remove("active");
        }
        // Close event details modal when clicking outside
        const eventDetailsModal = document.getElementById('eventDetailsModal');
        const lockConfirmModal = document.getElementById('lockConfirmModal');
        if (eventDetailsModal && !eventDetailsModal.contains(e.target) && !e.target.closest('[onclick*="showEventDetails"]')) {
          closeEventDetails();
        }
        if (lockConfirmModal && !lockConfirmModal.contains(e.target) && !e.target.closest('[onclick*="lockAllReservations"]')) {
          closeLockConfirmModal();
        }
      });

      profileModal.querySelector("div").addEventListener("click", (e) => {
        e.stopPropagation();
      });
      accountSettingsModal.querySelector("div").addEventListener("click", (e) => {
        e.stopPropagation();
      });
      privacySecurityModal.querySelector("div").addEventListener("click", (e) => {
        e.stopPropagation();
      });
      signOutModal.querySelector("div").addEventListener("click", (e) => {
        e.stopPropagation();
      });

      window.addEventListener("resize", () => {
        if (window.innerWidth >= 768) {
          sidebar.classList.remove("-ml-72");
          overlay.classList.add("hidden");
          document.body.style.overflow = "";
          if (!mainContent.classList.contains("md:ml-72")) {
            mainContent.classList.add("md:ml-72", "sidebar-open");
            mainContent.classList.remove("sidebar-closed");
          }
        } else {
          sidebar.classList.add("-ml-72");
          mainContent.classList.remove("md:ml-72", "sidebar-open");
          mainContent.classList.add("sidebar-closed");
          overlay.classList.add("hidden");
          document.body.style.overflow = "";
        }
        closeAllDropdowns();
      });

      // Flash success from server (after booking redirect)
      const flashSuccess = @json(session('success'));
      if (flashSuccess) {
        Swal.fire({
          icon: 'success',
          title: 'Success',
          text: flashSuccess,
          confirmButtonColor: '#28644c'
        });
      }

      // Calendar: dynamic month rendering and navigation
      const calendarGrid = document.getElementById('calendarGrid');
      const monthLabel = document.getElementById('monthLabel');
      const prevMonthBtn = document.getElementById('prevMonthBtn');
      const nextMonthBtn = document.getElementById('nextMonthBtn');
      const todayBtn = document.getElementById('todayBtn');
      const eventCount = document.getElementById('eventCount');
      const upcomingCount = document.getElementById('upcomingCount');
      const exportBtn = document.getElementById('exportBtn');

      let currentDate = new Date();
      // Database bookings passed from backend
      const sessionBookings = @json($calendarBookings);

      function daysInMonth(year, monthIndex) {
        return new Date(year, monthIndex + 1, 0).getDate();
      }

      function updateEventCount() {
        const year = currentDate.getFullYear();
        const monthIndex = currentDate.getMonth();
        const monthEvents = sessionBookings.filter(b => {
          if (!b.date) return false;
          const d = new Date(b.date);
          return d.getFullYear() === year && d.getMonth() === monthIndex;
        });
        eventCount.textContent = `${monthEvents.length} event${monthEvents.length !== 1 ? 's' : ''} this month`;
        upcomingCount.textContent = monthEvents.length;
      }

      function renderCalendar() {
        const year = currentDate.getFullYear();
        const monthIndex = currentDate.getMonth(); // 0-11
        const today = new Date();

        // Update month label
        const monthName = currentDate.toLocaleString('default', { month: 'long' });
        monthLabel.textContent = `${monthName} ${year}`;

        // Prepare grid
        calendarGrid.innerHTML = '';

        const firstDayOfMonth = new Date(year, monthIndex, 1);
        const startWeekday = firstDayOfMonth.getDay(); // 0=Sun
        const totalDays = daysInMonth(year, monthIndex);

        // We fill up to 42 cells (6 weeks) for a consistent layout
        const totalCells = 42;

        // Leading empty cells
        for (let i = 0; i < startWeekday; i++) {
          const cell = document.createElement('div');
          cell.className = 'h-24 border border-gray-100 rounded-md p-1 bg-gray-50';
          calendarGrid.appendChild(cell);
        }

        // Day cells
        for (let day = 1; day <= totalDays; day++) {
          const cell = document.createElement('div');
          const isToday = today.getFullYear() === year && today.getMonth() === monthIndex && today.getDate() === day;
          cell.className = `h-24 border border-gray-100 rounded-md p-1 relative bg-white hover:bg-gray-50 ${isToday ? 'ring-2 ring-[#28644c]' : ''}`;

          const badge = document.createElement('span');
          badge.className = `absolute top-1 right-1 text-[10px] ${isToday ? 'text-[#28644c] font-bold' : 'text-gray-400'}`;
          badge.textContent = day;
          cell.appendChild(badge);

          // Render bookings that fall on this day/month/year
          const events = (sessionBookings || []).filter(b => {
            if (!b.date) return false;
            const d = new Date(b.date);
            return d.getFullYear() === year && d.getMonth() === monthIndex && d.getDate() === day;
          });

          if (events.length) {
            const container = document.createElement('div');
            container.className = 'mt-5 space-y-1';
            events.forEach(ev => {
              const pill = document.createElement('div');
              const status = (ev.status || '').toLowerCase();
              let color = '';
              if (status === 'approved') color = 'bg-green-100 text-green-700';
              else if (status === 'rejected') color = 'bg-red-100 text-red-700';
              else if (status === 'pending') color = 'bg-yellow-100 text-yellow-800';
              else color = ev.type === 'room' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700';

              const start = ev.start_time ? ev.start_time.substring(0,5) : '';
              const end = ev.end_time ? ev.end_time.substring(0,5) : '';
              const timeStr = start && end ? ` (${start}-${end})` : (start ? ` (${start})` : '');

              pill.className = `text-[10px] px-2 py-1 rounded truncate ${color} cursor-pointer hover:opacity-80`;
              pill.title = (ev.purpose || ev.title || ev.name || 'Booking') + (status ? ` [${status.charAt(0).toUpperCase() + status.slice(1)}]` : '');
              pill.textContent = `${ev.name || ev.title || 'Booking'}${timeStr}`;
              pill.onclick = () => showEventDetails(ev);
              container.appendChild(pill);
            });
            cell.appendChild(container);
          }

          calendarGrid.appendChild(cell);
        }

        // Trailing empty cells
        const usedCells = startWeekday + totalDays;
        for (let i = usedCells; i < totalCells; i++) {
          const cell = document.createElement('div');
          cell.className = 'h-24 border border-gray-100 rounded-md p-1 bg-gray-50';
          calendarGrid.appendChild(cell);
        }

        // Update event count
        updateEventCount();
      }

      // Navigation buttons
      prevMonthBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
      });

      nextMonthBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
      });

      todayBtn?.addEventListener('click', (e) => {
        e.preventDefault();
        currentDate = new Date();
        renderCalendar();
      });

      // Export functionality
      exportBtn?.addEventListener('click', () => {
        const icsContent = generateICSFile(sessionBookings);
        downloadICSFile(icsContent, 'calendar.ics');
      });

      function generateICSFile(bookings) {
        let ics = 'BEGIN:VCALENDAR\nVERSION:2.0\nPRODID:-//Administrative Department//Calendar//EN\n';
        
        bookings.forEach(booking => {
          if (!booking.date) return;
          
          const start = new Date(booking.date);
          const end = new Date(booking.date);
          
          if (booking.start_time) {
            const [hours, minutes] = booking.start_time.split(':');
            start.setHours(parseInt(hours), parseInt(minutes), 0);
          }
          
          if (booking.end_time) {
            const [hours, minutes] = booking.end_time.split(':');
            end.setHours(parseInt(hours), parseInt(minutes), 0);
          } else {
            end.setDate(end.getDate() + 1); // All day event
          }
          
          const status = booking.status || 'pending';
          const title = booking.name || booking.title || 'Booking';
          const description = booking.purpose || '';
          
          ics += 'BEGIN:VEVENT\n';
          ics += `DTSTART:${formatDateForICS(start)}\n`;
          ics += `DTEND:${formatDateForICS(end)}\n`;
          ics += `SUMMARY:${title}\n`;
          ics += `DESCRIPTION:${description}\n`;
          ics += `STATUS:${status.toUpperCase()}\n`;
          ics += 'END:VEVENT\n';
        });
        
        ics += 'END:VCALENDAR';
        return ics;
      }

      function formatDateForICS(date) {
        return date.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
      }

      function downloadICSFile(content, filename) {
        const blob = new Blob([content], { type: 'text/calendar' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
      }

      // Initial render
      renderCalendar();

      // Event details modal functions
      window.showEventDetails = function(event) {
        const modal = document.getElementById('eventDetailsModal');
        const content = document.getElementById('eventDetailsContent');
        
        const status = (event.status || '').toLowerCase();
        const statusClass = {
          'pending': 'bg-yellow-100 text-yellow-800',
          'approved': 'bg-green-100 text-green-800',
          'rejected' : 'bg-red-100 text-red-800',
        }[status] || 'bg-gray-100 text-gray-800';
        
        const date = event.date ? new Date(event.date).toLocaleDateString('en-US', { 
          weekday: 'long', 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        }) : 'No date specified';
        
        const time = event.start_time && event.end_time ? 
          `${event.start_time} - ${event.end_time}` : 
          event.start_time || 'All day';
        
        content.innerHTML = `
          <div class="space-y-4">
            <div>
              <h4 class="font-semibold text-gray-700">Title</h4>
              <p class="text-sm text-gray-900">${event.name || event.title || 'Booking'}</p>
            </div>
            <div>
              <h4 class="font-semibold text-gray-700">Status</h4>
              <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                ${status.charAt(0).toUpperCase() + status.slice(1)}
              </span>
            </div>
            <div>
              <h4 class="font-semibold text-gray-700">Date</h4>
              <p class="text-sm text-gray-900">${date}</p>
            </div>
            <div>
              <h4 class="font-semibold text-gray-700">Time</h4>
              <p class="text-sm text-gray-900">${time}</p>
            </div>
            ${event.room ? `
            <div>
              <h4 class="font-semibold text-gray-700">Room</h4>
              <p class="text-sm text-gray-900">${event.room}</p>
            </div>
            ` : ''}
            ${event.equipment ? `
            <div>
              <h4 class="font-semibold text-gray-700">Equipment</h4>
              <p class="text-sm text-gray-900">${event.equipment}</p>
            </div>
            ` : ''}
            ${event.purpose ? `
            <div>
              <h4 class="font-semibold text-gray-700">Purpose</h4>
              <p class="text-sm text-gray-900">${event.purpose}</p>
            </div>
            ` : ''}
          </div>
        `;
        
        modal.classList.remove('hidden');
        modal.classList.add('active');
        modal.style.display = 'flex';
      };

      window.closeEventDetails = function() {
        const modal = document.getElementById('eventDetailsModal');
        modal.classList.add('hidden');
        modal.classList.remove('active');
        modal.style.display = 'none';
      };

      // Check and restore lock state on page load
      function restoreLockState() {
        const isLocked = localStorage.getItem('reservationsLocked') === 'true';
        const lockBtn = document.getElementById('lockAllBtn');
        if (lockBtn) {
          if (isLocked) {
            lockBtn.innerHTML = '<i class="bx bx-lock-open"></i> Unlock Reservations';
            lockBtn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
            lockBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            lockBtn.setAttribute('onclick', 'unlockAllReservations()');
          } else {
            lockBtn.innerHTML = '<i class="bx bx-lock"></i> Lock All Reservations';
            lockBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            lockBtn.classList.add('bg-orange-600', 'hover:bg-orange-700');
            lockBtn.setAttribute('onclick', 'lockAllReservations()');
          }
        }
        // Update upcoming events display based on lock state
        updateUpcomingEventsLockState(isLocked);
        // Update calendar events display based on lock state
        updateCalendarEventsLockState(isLocked);
      }

      // Update calendar events to show locked/unlocked state
      function updateCalendarEventsLockState(isLocked) {
        const calendarGrid = document.getElementById('calendarGrid');
        if (!calendarGrid) return;
        
        const eventPills = calendarGrid.querySelectorAll('.text-xs.px-2.py-1.rounded');
        
        eventPills.forEach(pill => {
          if (isLocked) {
            // Store original data if not already stored
            if (!pill.dataset.originalText) {
              pill.dataset.originalText = pill.textContent;
              pill.dataset.originalClasses = pill.className;
            }
            
            // Mask the event content
            pill.textContent = '****';
            pill.className = 'text-[10px] px-2 py-1 rounded bg-gray-100 text-gray-800 cursor-pointer hover:opacity-80';
            pill.title = 'Confidential data protected';
            
            // Disable click events
            const originalOnclick = pill.onclick;
            if (originalOnclick && !pill.dataset.originalOnclick) {
              pill.dataset.originalOnclick = originalOnclick.toString();
              pill.onclick = null;
              pill.style.pointerEvents = 'none';
            }
          } else {
            // Restore original data
            if (pill.dataset.originalText) {
              pill.textContent = pill.dataset.originalText;
              pill.className = pill.dataset.originalClasses;
              pill.title = '';
              
              // Restore click functionality
              if (pill.dataset.originalOnclick) {
                try {
                  // Restore the original onclick function
                  const originalFunction = new Function('return ' + pill.dataset.originalOnclick)();
                  pill.onclick = originalFunction;
                  pill.style.pointerEvents = 'auto';
                } catch (e) {
                  console.error('Error restoring onclick:', e);
                }
              }
            }
          }
        });
      }

      // Update upcoming events to show locked/unlocked state
      function updateUpcomingEventsLockState(isLocked) {
        const upcomingEventsList = document.getElementById('upcomingEventsList');
        const upcomingCard = upcomingEventsList?.closest('.dashboard-card');
        
        if (!upcomingEventsList || !upcomingCard) return;
        
        const eventItems = upcomingEventsList.querySelectorAll('.activity-item');
        
        if (isLocked) {
          // Show upcoming events when locked
          upcomingCard.classList.add('locked-visible');
          
          eventItems.forEach(item => {
            const statusElement = item.querySelector('.px-2.py-0.rounded-full');
            const titleElement = item.querySelector('.font-semibold');
            const dateElement = item.querySelector('.text-gray-500');
            
            // Store original data if not already stored
            if (!item.dataset.originalData) {
              const originalTitle = titleElement?.textContent || '';
              const originalDate = dateElement?.textContent || '';
              const originalStatus = statusElement?.textContent || '';
              item.dataset.originalData = JSON.stringify({
                title: originalTitle,
                date: originalDate,
                status: originalStatus
              });
            }
            
            // Add lock indicator to events
            if (!item.querySelector('.lock-indicator')) {
              const lockIndicator = document.createElement('i');
              lockIndicator.className = 'fas fa-lock text-red-500 text-xs lock-indicator ml-2';
              lockIndicator.title = 'Confidential data protected';
              
              if (titleElement) {
                titleElement.innerHTML = '****<span class="lock-indicator-container"></span>';
                titleElement.appendChild(lockIndicator);
              }
              
              // Mask the date information
              if (dateElement) {
                dateElement.textContent = '** ** ****';
              }
              
              // Mask the status
              if (statusElement) {
                statusElement.textContent = '****';
                statusElement.className = 'px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-800';
              }
              
              // Dim the event item to show it's locked
              item.style.opacity = '0.7';
              item.style.cursor = 'not-allowed';
              
              // Add locked class for styling
              item.classList.add('locked-event');
              
              // Disable click events
              item.onclick = null;
              item.style.pointerEvents = 'none';
            }
          });
        } else {
          // Hide upcoming events when unlocked
          upcomingCard.classList.remove('locked-visible');
          
          // Restore original data if it exists
          eventItems.forEach(item => {
            const lockIndicator = item.querySelector('.lock-indicator');
            if (lockIndicator) {
              lockIndicator.remove();
            }
            
            // Restore original data
            if (item.dataset.originalData) {
              try {
                const originalData = JSON.parse(item.dataset.originalData);
                const titleElement = item.querySelector('.font-semibold');
                const dateElement = item.querySelector('.text-gray-500');
                const statusElement = item.querySelector('.px-2.py-0.rounded-full');
                
                if (titleElement) {
                  titleElement.textContent = originalData.title;
                }
                if (dateElement) {
                  dateElement.textContent = originalData.date;
                }
                if (statusElement) {
                  statusElement.textContent = originalData.status;
                  // Restore original status color classes
                  const status = originalData.status.toLowerCase();
                  if (status === 'pending') {
                    statusElement.className = 'px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-800';
                  } else if (status === 'approved') {
                    statusElement.className = 'px-2 py-0.5 rounded-full text-[10px] font-semibold bg-green-100 text-green-800';
                  } else if (status === 'rejected') {
                    statusElement.className = 'px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-800';
                  }
                }
              } catch (e) {
                console.error('Error restoring original data:', e);
              }
            }
            
            // Reset appearance
            item.style.opacity = '1';
            item.style.cursor = 'pointer';
            item.classList.remove('locked-event');
            item.style.pointerEvents = 'auto';
            
            // Restore click functionality
            const bookingData = item.getAttribute('onclick')?.match(/showEventDetails\(([^)]+)\)/);
            if (bookingData) {
              try {
                const booking = JSON.parse(bookingData[1]);
                item.onclick = () => showEventDetails(booking);
              } catch (e) {
                // Fallback if parsing fails
                item.onclick = null;
              }
            }
          });
        }
      }

      // Call restore function when DOM is ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', restoreLockState);
      } else {
        restoreLockState();
      }

      // Lock all reservations function for confidential data - moved outside DOMContentLoaded for global access
      window.lockAllReservations = function() {
        const modal = document.getElementById('lockConfirmModal');
        modal.classList.remove('hidden');
        modal.classList.add('active');
        modal.style.display = 'flex';
      };

      window.closeLockConfirmModal = function() {
        const modal = document.getElementById('lockConfirmModal');
        modal.classList.add('hidden');
        modal.classList.remove('active');
        modal.style.display = 'none';
      };

      window.confirmLockReservations = function() {
        // Close the confirmation modal
        closeLockConfirmModal();
        
        // Show loading modal
        const loadingModal = document.createElement('div');
        loadingModal.id = 'loadingModal';
        loadingModal.className = 'modal active';
        loadingModal.style.display = 'flex';
        loadingModal.innerHTML = `
          <div class="bg-white rounded-lg w-full max-w-sm mx-4 p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-orange-100 flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-spinner fa-spin text-orange-600 text-2xl"></i>
            </div>
            <p class="text-gray-900 font-medium mb-2">Locking Reservations</p>
            <p class="text-gray-600 text-sm">Securing all reservations for confidential data...</p>
          </div>
        `;
        document.body.appendChild(loadingModal);

        // Simulate API call or backend processing
        setTimeout(() => {
          // Remove loading modal
          document.body.removeChild(loadingModal);
          
          // Show success modal
          const successModal = document.createElement('div');
          successModal.id = 'successModal';
          successModal.className = 'modal active';
          successModal.style.display = 'flex';
          successModal.innerHTML = `
            <div class="bg-white rounded-lg w-full max-w-sm mx-4 p-6 text-center">
              <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-2xl"></i>
              </div>
              <p class="text-gray-900 font-medium mb-2">Reservations Locked</p>
              <p class="text-gray-600 text-sm mb-4">All reservations have been successfully locked for confidential data handling.</p>
              <button onclick="closeSuccessModal(this)" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">
                OK
              </button>
            </div>
          `;
          document.body.appendChild(successModal);
          
          // Update button to unlock state
          const lockBtn = document.getElementById('lockAllBtn');
          if (lockBtn) {
            lockBtn.innerHTML = '<i class="bx bx-lock-open"></i> Unlock Reservations';
            lockBtn.classList.remove('bg-orange-600', 'hover:bg-orange-700');
            lockBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            lockBtn.setAttribute('onclick', 'unlockAllReservations()');
            // Save lock state to localStorage
            localStorage.setItem('reservationsLocked', 'true');
            // Update upcoming events to show locked state
            updateUpcomingEventsLockState(true);
            // Update calendar events to show locked state
            updateCalendarEventsLockState(true);
          }
        }, 2000);
      };

      // Unlock all reservations function
      window.unlockAllReservations = function() {
        const otpModal = document.createElement('div');
        otpModal.id = 'otpConfirmModal';
        otpModal.className = 'modal active';
        otpModal.style.display = 'flex';
        otpModal.innerHTML = `
          <div class="bg-white rounded-lg w-full max-w-md mx-4">
            <div class="flex justify-between items-center border-b px-6 py-4">
              <h3 class="text-xl font-semibold text-gray-900">Security Verification</h3>
              <button onclick="closeOtpModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times text-xl"></i>
              </button>
            </div>
            <div class="p-6">
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">
                  <i class="fas fa-shield-alt text-red-600 text-xl"></i>
                </div>
                <div>
                  <p class="text-gray-900 font-medium">OTP Required</p>
                  <p class="text-gray-600 text-sm">Enter the 6-digit code sent to your registered device</p>
                </div>
              </div>
              <div class="mb-4">
                <label for="otpInput" class="block text-sm font-medium text-gray-700 mb-2">One-Time Password</label>
                <input type="text" id="otpInput" maxlength="6" placeholder="000000" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center text-lg font-mono focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <p class="text-xs text-gray-500 mt-1">Enter the 6-digit code</p>
              </div>
              <div class="flex justify-between items-center mb-4">
                <button onclick="resendOtp()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                  <i class="fas fa-redo mr-1"></i>Resend Code
                </button>
                <span id="otpTimer" class="text-sm text-gray-500">Code expires in 2:00</span>
              </div>
              <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                <p class="text-red-800 text-sm"><strong>Security Notice:</strong> This action will remove confidential data protection from all reservations.</p>
              </div>
              <div class="flex justify-end space-x-3">
                <button onclick="closeOtpModal()" class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                  Cancel
                </button>
                <button onclick="verifyOtpAndUnlock()" class="bg-blue-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm transition-all duration-200">
                  <i class="fas fa-lock-open mr-2"></i>Verify & Unlock
                </button>
              </div>
            </div>
          </div>
        `;
        document.body.appendChild(otpModal);
        
        // Start OTP timer
        startOtpTimer();
        
        // Focus on OTP input
        setTimeout(() => {
          document.getElementById('otpInput')?.focus();
        }, 100);
      };

      window.closeOtpModal = function() {
        const modal = document.getElementById('otpConfirmModal');
        if (modal) {
          document.body.removeChild(modal);
        }
        stopOtpTimer();
      };

      let otpTimerInterval;
      let otpTimeLeft = 120; // 2 minutes

      function startOtpTimer() {
        otpTimeLeft = 120;
        updateOtpTimerDisplay();
        otpTimerInterval = setInterval(() => {
          otpTimeLeft--;
          updateOtpTimerDisplay();
          if (otpTimeLeft <= 0) {
            stopOtpTimer();
            const timerElement = document.getElementById('otpTimer');
            if (timerElement) {
              timerElement.textContent = 'Code expired';
              timerElement.classList.add('text-red-500');
            }
          }
        }, 1000);
      }

      function stopOtpTimer() {
        if (otpTimerInterval) {
          clearInterval(otpTimerInterval);
          otpTimerInterval = null;
        }
      }

      function updateOtpTimerDisplay() {
        const timerElement = document.getElementById('otpTimer');
        if (timerElement && otpTimeLeft > 0) {
          const minutes = Math.floor(otpTimeLeft / 60);
          const seconds = otpTimeLeft % 60;
          timerElement.textContent = `Code expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
      }

      window.resendOtp = function() {
        // Reset timer
        stopOtpTimer();
        startOtpTimer();
        
        // Show success message
        const timerElement = document.getElementById('otpTimer');
        if (timerElement) {
          timerElement.classList.remove('text-red-500');
          timerElement.classList.add('text-green-500');
          timerElement.textContent = 'Code sent!';
        }
        
        // Clear input
        const otpInput = document.getElementById('otpInput');
        if (otpInput) {
          otpInput.value = '';
          otpInput.focus();
        }
        
        // Reset timer display after 2 seconds
        setTimeout(() => {
          if (timerElement) {
            timerElement.classList.remove('text-green-500');
          }
        }, 2000);
      };

      window.verifyOtpAndUnlock = function() {
        const otpInput = document.getElementById('otpInput');
        const otp = otpInput?.value || '';
        
        // Validate OTP (6 digits)
        if (otp.length !== 6) {
          if (otpInput) {
            otpInput.classList.add('border-red-500');
            otpInput.placeholder = 'Enter 6 digits';
          }
          return;
        }
        
        // For demo purposes, accept any 6-digit code
        // In production, this would verify against a real OTP system
        closeOtpModal();
        
        // Show loading modal
        const loadingModal = document.createElement('div');
        loadingModal.id = 'unlockLoadingModal';
        loadingModal.className = 'modal active';
        loadingModal.style.display = 'flex';
        loadingModal.innerHTML = `
          <div class="bg-white rounded-lg w-full max-w-sm mx-4 p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
              <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
            </div>
            <p class="text-gray-900 font-medium mb-2">Unlocking Reservations</p>
            <p class="text-gray-600 text-sm">Restoring normal access to reservations...</p>
          </div>
        `;
        document.body.appendChild(loadingModal);

        // Simulate API call or backend processing
        setTimeout(() => {
          // Remove loading modal
          document.body.removeChild(loadingModal);
          
          // Show success modal
          const successModal = document.createElement('div');
          successModal.id = 'unlockSuccessModal';
          successModal.className = 'modal active';
          successModal.style.display = 'flex';
          successModal.innerHTML = `
            <div class="bg-white rounded-lg w-full max-w-sm mx-4 p-6 text-center">
              <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-blue-600 text-2xl"></i>
              </div>
              <p class="text-gray-900 font-medium mb-2">Reservations Unlocked</p>
              <p class="text-gray-600 text-sm mb-4">All reservations have been successfully unlocked and normal access has been restored.</p>
              <button onclick="closeUnlockSuccessModal(this)" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">
                OK
              </button>
            </div>
          `;
          document.body.appendChild(successModal);
          
          // Update button back to lock state
          const lockBtn = document.getElementById('lockAllBtn');
          if (lockBtn) {
            lockBtn.innerHTML = '<i class="bx bx-lock"></i> Lock All Reservations';
            lockBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            lockBtn.classList.add('bg-orange-600', 'hover:bg-orange-700');
            lockBtn.setAttribute('onclick', 'lockAllReservations()');
            // Save unlock state to localStorage
            localStorage.setItem('reservationsLocked', 'false');
            // Update upcoming events to show unlocked state
            updateUpcomingEventsLockState(false);
            // Update calendar events to show unlocked state
            updateCalendarEventsLockState(false);
          }
        }, 2000);
      };

      window.closeUnlockSuccessModal = function(button) {
        const modal = button.closest('.modal');
        if (modal && modal.id === 'unlockSuccessModal') {
          document.body.removeChild(modal);
        }
      };

      window.closeSuccessModal = function(button) {
        const modal = button.closest('.modal');
        if (modal && modal.id === 'successModal') {
          document.body.removeChild(modal);
        }
      };
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const bookings = @json($calendarBookings ?? []);

      function toMinutes(t) {
        if (!t) return null;
        const [h, m] = t.split(':').map(Number);
        if (Number.isNaN(h) || Number.isNaN(m)) return null;
        return h * 60 + m;
      }
      function overlaps(aStart, aEnd, bStart, bEnd) { return aStart < bEnd && aEnd > bStart; }
      function normalize(v){ return (v||'').toString().toLowerCase(); }

      // Detect a booking form on this page (if any quick-create exists)
      const possibleForms = Array.from(document.querySelectorAll('form'));
      possibleForms.forEach(form => {
        const dateEl = form.querySelector('#bookingDate, input[name="date"]');
        const startEl = form.querySelector('#startTime, input[name="start_time"]');
        const endEl   = form.querySelector('#endTime, input[name="end_time"]');
        const roomEl  = form.querySelector('#roomSelect, select[name="room"], input[name="room"]');
        if (!dateEl || !startEl || !endEl || !roomEl) return;

        form.addEventListener('submit', (e) => {
          const dateVal = dateEl.value;
          const startVal = startEl.value;
          const endVal = endEl.value;
          const roomKey = normalize(roomEl.value);
          if (!dateVal || !startVal || !endVal || !roomKey) return; // leave to HTML5 required

          const s = toMinutes(startVal);
          const f = toMinutes(endVal);
          if (s === null || f === null || f <= s) return;

          const conflict = (bookings || []).find(b => {
            try {
              const bDate = (b.date || '').slice(0,10);
              const status = normalize(b.status);
              const occupying = ['approved','pending','occupied'].includes(status) || status === '';
              if (!occupying) return false;
              if (bDate !== dateVal) return false;
              const bName = normalize(b.name || b.title || '');
              const bType = normalize(b.type || '');
              const sameRoom = (bType === 'room') && (bName.includes(roomKey) || roomKey.includes(bName));
              if (!sameRoom) return false;
              const bs = toMinutes((b.start_time || '').slice(0,5));
              const be = toMinutes((b.end_time || '').slice(0,5));
              if (bs === null || be === null) return false;
              return overlaps(s, f, bs, be);
            } catch(_) { return false; }
          });

          if (conflict) {
            e.preventDefault();
            const range = `${(conflict.start_time||'').slice(0,5)} - ${(conflict.end_time||'').slice(0,5)}`;
            Swal.fire({
              icon: 'error',
              title: 'Time slot unavailable',
              html: `<div class="text-left">This room is already booked on <b>${dateVal}</b> between <b>${range}</b>.<br/><div class="mt-2">Please select a different time or room.</div></div>`
            });
          }
        });
      });
    });
  </script>
  <script>
    // Fallback calendar renderer: if #calendarGrid is empty after load, render a basic month view
    document.addEventListener('DOMContentLoaded', () => {
      try {
        const grid = document.getElementById('calendarGrid');
        const monthLabel = document.getElementById('monthLabel');
        const prevBtn = document.getElementById('prevMonthBtn');
        const nextBtn = document.getElementById('nextMonthBtn');
        if (!grid || grid.children.length > 0) return;

        let cur = new Date();
        const bookings = @json($calendarBookings ?? []);

        function daysInMonth(y, m){ return new Date(y, m + 1, 0).getDate(); }
        function render(){
          const y = cur.getFullYear();
          const m = cur.getMonth();
          if (monthLabel) monthLabel.textContent = cur.toLocaleString('default', { month: 'long' }) + ' ' + y;
          grid.innerHTML = '';
          const first = new Date(y, m, 1);
          const startW = first.getDay();
          const total = daysInMonth(y, m);
          // Leading blanks
          for (let i=0;i<startW;i++) {
            const c = document.createElement('div');
            c.className = 'h-24 border border-gray-100 rounded-md p-1 bg-gray-50';
            grid.appendChild(c);
          }
          // Days
          for (let d=1; d<=total; d++){
            const cell = document.createElement('div');
            cell.className = 'h-24 border border-gray-100 rounded-md p-1 relative bg-white hover:bg-gray-50';
            const badge = document.createElement('span');
            badge.className = 'absolute top-1 right-1 text-[10px] text-gray-400';
            badge.textContent = d;
            cell.appendChild(badge);
            // simple event pills
            const evs = (bookings||[]).filter(b=>{
              if (!b.date) return false; const dt=new Date(b.date);
              return dt.getFullYear()===y && dt.getMonth()===m && dt.getDate()===d;
            });
            if (evs.length){
              const list = document.createElement('div'); list.className='mt-5 space-y-1';
              evs.forEach(ev=>{
                const pill = document.createElement('div');
                const st = (ev.status||'').toLowerCase();
                let color = st==='approved'?'bg-green-100 text-green-700':st==='rejected'?'bg-red-100 text-red-700':st==='pending'?'bg-yellow-100 text-yellow-800': (ev.type==='room'?'bg-green-100 text-green-700':'bg-blue-100 text-blue-700');
                const s = (ev.start_time||'').slice(0,5); const e=(ev.end_time||'').slice(0,5);
                pill.className = `text-[10px] px-2 py-1 rounded truncate ${color}`;
                pill.textContent = `${ev.name||ev.title||'Booking'}${s?` (${s}${e?`-${e}`:''})`:''}`;
                list.appendChild(pill);
              });
              cell.appendChild(list);
            }
            grid.appendChild(cell);
          }
          // Trailing blanks to fill 42 cells
          const used = startW + total;
          for (let i=used;i<42;i++){ const c=document.createElement('div'); c.className='h-24 border border-gray-100 rounded-md p-1 bg-gray-50'; grid.appendChild(c); }
        }
        render();
        prevBtn?.addEventListener('click', (e)=>{ e.preventDefault(); cur.setMonth(cur.getMonth()-1); render(); });
        nextBtn?.addEventListener('click', (e)=>{ e.preventDefault(); cur.setMonth(cur.getMonth()+1); render(); });
      } catch(_) {}
    });
  </script>
</body>
</html>