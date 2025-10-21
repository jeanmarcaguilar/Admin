@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compliance Tracking | Administrative Dashboard</title>
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
            max-width: 1152px;
            margin: 0 auto;
            transition: max-width 0.3s ease-in-out;
        }
        .hidden {
    display: none;
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

        .dashboard-card:nth-child(1)::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .dashboard-card:nth-child(2)::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .dashboard-card:nth-child(3)::before { background: linear-gradient(90deg, #10b981, #34d399); }
        .dashboard-card:nth-child(4)::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
            z-index: 2;
        }

        .table-row {
            transition: all 0.2s ease-in-out;
        }

        .table-row:hover {
            background-color: rgba(16, 185, 129, 0.05);
            transform: translateX(4px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none" aria-label="Toggle Sidebar">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Compliance Tracking</h1>
            </div>
            <div class="flex items-center space-x-1">
                <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn" aria-label="Notifications">
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
      if (typeof window.toggleSidebarDropdown !== 'function') {
        window.toggleSidebarDropdown = function(el){
          try{
            var headers = document.querySelectorAll('.has-dropdown > div');
            for (var i=0;i<headers.length;i++){
              var h = headers[i];
              if (h !== el){
                var m = h.nextElementSibling;
                var c = h.querySelector('.bx-chevron-down');
                if (m && !m.classList.contains('hidden')) m.classList.add('hidden');
                if (c) c.classList.remove('rotate-180');
                h.setAttribute('aria-expanded','false');
              }
            }
            if (el){
              var menu = el.nextElementSibling;
              var chev = el.querySelector('.bx-chevron-down');
              if (menu) menu.classList.toggle('hidden');
              if (chev) chev.classList.toggle('rotate-180');
              var isOpen = menu && !menu.classList.contains('hidden');
              el.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            }
          }catch(e){}
        };
      }
    </script>

    <script>
      if (typeof window.toggleUserMenu !== 'function') {
        window.toggleUserMenu = function(ev){
          try{
            if(ev && ev.stopPropagation) ev.stopPropagation();
            var btn=document.getElementById('userMenuBtn');
            var menu=document.getElementById('userMenuDropdown');
            var notif=document.getElementById('notificationDropdown');
            if(menu){ menu.classList.toggle('hidden'); }
            if(btn){ var ex=btn.getAttribute('aria-expanded')==='true'; btn.setAttribute('aria-expanded', (!ex).toString()); }
            if(notif){ notif.classList.add('hidden'); }
          }catch(e){}
        };
      }
      if (typeof window.toggleNotification !== 'function') {
        window.toggleNotification = function(ev){
          try{
            if(ev && ev.stopPropagation) ev.stopPropagation();
            var nb=document.getElementById('notificationBtn');
            var nd=document.getElementById('notificationDropdown');
            var ud=document.getElementById('userMenuDropdown');
            if(nd){ nd.classList.toggle('hidden'); }
            if(nb){ var ex=nb.getAttribute('aria-expanded')==='true'; nb.setAttribute('aria-expanded',(!ex).toString()); }
            if(ud){ ud.classList.add('hidden'); }
            var ub=document.getElementById('userMenuBtn'); if(ub){ ub.setAttribute('aria-expanded','false'); }
          }catch(e){}
        };
      }
      if (!window.__complianceMenusBound) {
        window.__complianceMenusBound = true;
        document.addEventListener('click', function(e){
          var ud=document.getElementById('userMenuDropdown');
          var ub=document.getElementById('userMenuBtn');
          var nd=document.getElementById('notificationDropdown');
          var nb=document.getElementById('notificationBtn');
          var clickInsideUser = (ub && (ub.contains(e.target) || (ud && ud.contains(e.target))));
          var clickInsideNotif = (nb && (nb.contains(e.target) || (nd && nd.contains(e.target))));
          if(!clickInsideUser && !clickInsideNotif){
            if(ud){ ud.classList.add('hidden'); }
            if(nd){ nd.classList.add('hidden'); }
            if(ub){ ub.setAttribute('aria-expanded','false'); }
            if(nb){ nb.setAttribute('aria-expanded','false'); }
          }
        });
        document.addEventListener('keydown', function(e){ if(e.key==='Escape'){
          var ud=document.getElementById('userMenuDropdown');
          var nd=document.getElementById('notificationDropdown');
          var ub=document.getElementById('userMenuBtn');
          var nb=document.getElementById('notificationBtn');
          if(ud){ ud.classList.add('hidden'); }
          if(nd){ nd.classList.add('hidden'); }
          if(ub){ ub.setAttribute('aria-expanded','false'); }
          if(nb){ nb.setAttribute('aria-expanded','false'); }
        }});
        var nb=document.getElementById('notificationBtn');
        if(nb){ nb.addEventListener('click', function(e){ if(window.toggleNotification) window.toggleNotification(e); }); }
        var ub=document.getElementById('userMenuBtn');
        if(ub){ ub.addEventListener('click', function(e){ if(window.toggleUserMenu) window.toggleUserMenu(e); }); }
      }
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
                        <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" onclick="toggleSidebarDropdown(this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-calendar-check"></i>
                                <span>Facilities Reservations</span>
                            </div>
                            <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
                        </div>
                        <ul class="dropdown-menu hidden bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('room-equipment') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-door-open mr-2"></i>Room & Equipment Booking</a></li>
                            <li><a href="{{ route('scheduling.calendar') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-calendar mr-2"></i>Scheduling & Calendar Integrations</a></li>
                            <li><a href="{{ route('approval.workflow') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-circle mr-2"></i>Approval Workflow</a></li>
                            <li><a href="{{ route('reservation.history') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-history mr-2"></i>Reservation History</a></li>
                        </ul>
                    </li>
                    <li class="has-dropdown">
                        <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" onclick="toggleSidebarDropdown(this)">
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
                    <li class="has-dropdown active">
                        <div class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" onclick="toggleSidebarDropdown(this)">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-file"></i>
                                <span>Legal Management</span>
                            </div>
                            <i class="bx bx-chevron-down text-2xl transition-transform duration-300 rotate-180"></i>
                        </div>
                        <ul class="dropdown-menu bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('case.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-briefcase mr-2"></i>Case Management</a></li>
                            <li><a href="{{ route('contract.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-file-blank mr-2"></i>Contract Management</a></li>
                            <li><a href="{{ route('document.compliance.tracking') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-check-double mr-2"></i>Compliance Tracking</a></li>
                            <li><a href="{{ route('deadline.hearing.alerts') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-alarm mr-2"></i>Deadline & Hearing Alerts</a></li>
                        </ul>
                    </li>
                    <li class="has-dropdown">
                        <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" onclick="toggleSidebarDropdown(this)">
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
        <script>
          // Sidebar toggle for mobile and focus management
          (function(){
            if (window.__complianceSidebarBound) return; window.__complianceSidebarBound = true;
            try{
              var toggleBtn = document.getElementById('toggle-btn');
              var sidebar = document.getElementById('sidebar');
              var overlay = document.getElementById('overlay');
              var main = document.getElementById('main-content');
              function openSidebar(){
                if(sidebar){ sidebar.classList.remove('-ml-72'); sidebar.classList.add('ml-0'); }
                if(overlay){ overlay.classList.remove('hidden'); }
                document.body.style.overflow='hidden';
              }
              function closeSidebar(){
                if(sidebar){ sidebar.classList.add('-ml-72'); sidebar.classList.remove('ml-0'); }
                if(overlay){ overlay.classList.add('hidden'); }
                document.body.style.overflow='';
              }
              if(toggleBtn){ toggleBtn.addEventListener('click', function(){
                var isClosed = sidebar && sidebar.classList.contains('-ml-72');
                if(isClosed){ openSidebar(); } else { closeSidebar(); }
              }); }
              if(overlay){ overlay.addEventListener('click', function(){ closeSidebar(); }); }
              document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ closeSidebar(); }});
              // Ensure proper state on resize: sidebar always visible on md+ due to md:ml-0
              window.addEventListener('resize', function(){
                if(window.innerWidth>=768){
                  if(overlay){ overlay.classList.add('hidden'); }
                  document.body.style.overflow='';
                }
              });
            }catch(e){}
          })();
        </script>
        <main id="main-content" class="flex-1 p-6 w-full mt-16">
            <div class="dashboard-container">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-[#1a4d38]">Compliance Tracking</h1>
                            <p class="text-gray-600 text-sm">Monitor and manage all compliance requirements and deadlines</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button id="addComplianceBtn" type="button" onclick="if(window.__openAddCompliance){window.__openAddCompliance(event);}" class="px-4 py-2 bg-[#2f855A] text-white rounded-lg hover:bg-[#28644c] transition-colors duration-200 flex items-center text-sm font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                <i class="fas fa-plus mr-2"></i> Add New Compliance
                            </button>
                        </div>
                    </div>
                    <!-- Stats Cards -->
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Active Compliances</p>

                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $stats['active'] ?? 0 }}</p>

                                </div>
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-clipboard-check text-xl"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="dashboard-card p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Pending Review</p>

                                    <p class="font-extrabold text-2xl mt-1 text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                                </div>
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-search text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm" placeholder="Search compliances...">
                            </div>
                            <div class="flex space-x-3">
                                <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                                <select id="filterType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="">All Types</option>
                                    <option value="legal">Legal</option>
                                    <option value="financial">Financial</option>
                                    <option value="hr">HR</option>
                                    <option value="safety">Safety</option>
                                </select>
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                    <i class="fas fa-filter text-gray-600 mr-2"></i>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </section>
                    <section class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compliance ID</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse((isset($complianceItems) ? $complianceItems : []) as $item)
                                        @php
                                            $daysUntilDue = now()->diffInDays($item->due_date, false);
                                            $daysText = $daysUntilDue > 0 ? "in {$daysUntilDue} days" : ($daysUntilDue == 0 ? "today" : abs($daysUntilDue) . " days overdue");
                                            $typeBadge = ucfirst($item->type);
                                            $statusBadge = ucfirst($item->status);
                                            $statusClasses = $item->status_badge_classes;
                                        @endphp
                                        <tr class="table-row" data-id="{{ $item->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->code }}</div>
                                                <div class="text-xs text-gray-500">Created: {{ $item->created_at->format('Y-m-d') }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->title }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->description ? Str::limit($item->description, 50) : 'No description' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $typeBadge }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $item->due_date->format('Y-m-d') }}</div>
                                                <div class="text-xs text-gray-500">{{ $daysText }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}">{{ $statusBadge }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="#" class="viewComplianceBtn text-[#2f855A] hover:text-[#1a4d38] mr-3" data-tooltip="View" data-id="{{ $item->id }}" data-code="{{ $item->code }}" data-title="{{ $item->title }}" data-type="{{ $typeBadge }}" data-status="{{ $statusBadge }}" data-due-date="{{ $item->due_date->format('Y-m-d') }}" data-description="{{ $item->description }}" data-responsible="{{ $item->responsible_person }}" data-priority="{{ $item->priority }}"><i class="fas fa-eye"></i></a>
                                                <a href="#" class="editComplianceBtn text-blue-600 hover:text-blue-900 mr-3" data-tooltip="Edit" data-id="{{ $item->id }}" data-title="{{ $item->title }}" data-type="{{ $item->type }}" data-status="{{ $item->status }}" data-due-date="{{ $item->due_date->format('Y-m-d') }}" data-description="{{ $item->description }}" data-responsible="{{ $item->responsible_person }}" data-priority="{{ $item->priority }}"><i class="fas fa-edit"></i></a>
                                                <a href="#" class="deleteComplianceBtn text-red-600 hover:text-red-900" data-tooltip="Delete" data-id="{{ $item->id }}" data-title="{{ $item->title }}"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No compliance items found.</td>
                                        </tr>
                                    @endforelse
                                    <tr class="table-row">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">CPL-2023-045</div>
                                            <div class="text-xs text-gray-500">Created: 2023-09-15</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">Annual Financial Report</div>
                                            <div class="text-xs text-gray-500">SEC Compliance</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Financial</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">2023-12-31</div>
                                            <div class="text-xs text-gray-500">in 89 days</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">On Track</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="#" class="text-[#2f855A] hover:text-[#1a4d38] mr-3" data-tooltip="View"><i class="fas fa-eye"></i></a>
                                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-3" data-tooltip="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="#" class="text-red-600 hover:text-red-900" data-tooltip="Delete"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                                <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium">20</span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <i class="fas fa-chevron-left h-5 w-5"></i>
                                        </a>
                                        <a href="#" aria-current="page" class="z-10 bg-[#2f855A] border-[#2f855A] text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            1
                                        </a>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            2
                                        </a>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            3
                                        </a>
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right h-5 w-5"></i>
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <!-- User Menu Dropdown -->
            <div id="userMenuDropdown" onclick="event.stopPropagation();" class="hidden fixed right-4 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200" style="top: 4rem; z-index: 60;" role="menu" aria-labelledby="userMenuBtn">
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

            <script>
              // Profile modal controls (aligned with case-management behavior)
              if (typeof window.openProfileModal !== 'function') {
                window.openProfileModal = function(){
                  try{
                    var m=document.getElementById('profileModal');
                    if(!m) return;
                    m.classList.remove('hidden');
                    m.style.display='flex';
                    document.body.style.overflow='hidden';
                    // close user menu
                    var d=document.getElementById('userMenuDropdown'); if(d) d.classList.add('hidden');
                    var b=document.getElementById('userMenuBtn'); if(b) b.setAttribute('aria-expanded','false');
                  }catch(e){}
                };
              }
              if (typeof window.closeProfileModal !== 'function') {
                window.closeProfileModal = function(){
                  try{
                    var m=document.getElementById('profileModal');
                    if(!m) return;
                    m.classList.add('hidden');
                    m.style.display='none';
                    document.body.style.overflow='auto';
                  }catch(e){}
                };
              }
              // Bind buttons
              document.addEventListener('DOMContentLoaded', function(){
                var op=document.getElementById('openProfileBtn');
                if(op){ op.addEventListener('click', function(e){ e.stopPropagation(); if(window.openProfileModal) window.openProfileModal(); }); }
                var cp=document.getElementById('closeProfileBtn');
                if(cp){ cp.addEventListener('click', function(){ if(window.closeProfileModal) window.closeProfileModal(); }); }
                var cp2=document.getElementById('closeProfileBtn2');
                if(cp2){ cp2.addEventListener('click', function(){ if(window.closeProfileModal) window.closeProfileModal(); }); }
                // Close on Escape
                document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ if(window.closeProfileModal) window.closeProfileModal(); }});
                // Close when clicking on backdrop
                document.addEventListener('click', function(e){
                  var modal=document.getElementById('profileModal');
                  if(modal && e.target===modal){ if(window.closeProfileModal) window.closeProfileModal(); }
                });
              });
            </script>

            <script>
              // Account Settings modal
              if (typeof window.openAccountSettingsModal !== 'function') {
                window.openAccountSettingsModal = function(){
                  try{
                    var m=document.getElementById('accountSettingsModal'); if(!m) return;
                    m.classList.remove('hidden'); m.style.display='flex'; document.body.style.overflow='hidden';
                    var d=document.getElementById('userMenuDropdown'); if(d) d.classList.add('hidden');
                    var b=document.getElementById('userMenuBtn'); if(b) b.setAttribute('aria-expanded','false');
                  }catch(e){}
                };
              }
              if (typeof window.closeAccountSettingsModal !== 'function') {
                window.closeAccountSettingsModal = function(){
                  try{
                    var m=document.getElementById('accountSettingsModal'); if(!m) return;
                    m.classList.add('hidden'); m.style.display='none'; document.body.style.overflow='auto';
                  }catch(e){}
                };
              }

              // Privacy & Security modal
              if (typeof window.openPrivacySecurityModal !== 'function') {
                window.openPrivacySecurityModal = function(){
                  try{
                    var m=document.getElementById('privacySecurityModal'); if(!m) return;
                    m.classList.remove('hidden'); m.style.display='flex'; document.body.style.overflow='hidden';
                    var d=document.getElementById('userMenuDropdown'); if(d) d.classList.add('hidden');
                    var b=document.getElementById('userMenuBtn'); if(b) b.setAttribute('aria-expanded','false');
                  }catch(e){}
                };
              }
              if (typeof window.closePrivacySecurityModal !== 'function') {
                window.closePrivacySecurityModal = function(){
                  try{
                    var m=document.getElementById('privacySecurityModal'); if(!m) return;
                    m.classList.add('hidden'); m.style.display='none'; document.body.style.overflow='auto';
                  }catch(e){}
                };
              }

              // Sign Out modal
              if (typeof window.openSignOutModal !== 'function') {
                window.openSignOutModal = function(){
                  try{
                    var m=document.getElementById('signOutModal'); if(!m) return;
                    m.classList.remove('hidden'); m.style.display='flex'; document.body.style.overflow='hidden';
                    var d=document.getElementById('userMenuDropdown'); if(d) d.classList.add('hidden');
                    var b=document.getElementById('userMenuBtn'); if(b) b.setAttribute('aria-expanded','false');
                  }catch(e){}
                };
              }
              if (typeof window.closeSignOutModal !== 'function') {
                window.closeSignOutModal = function(){
                  try{
                    var m=document.getElementById('signOutModal'); if(!m) return;
                    m.classList.add('hidden'); m.style.display='none'; document.body.style.overflow='auto';
                  }catch(e){}
                };
              }

              // Bind openers from user menu
              document.addEventListener('DOMContentLoaded', function(){
                var oas=document.getElementById('openAccountSettingsBtn');
                if(oas){ oas.addEventListener('click', function(e){ e.stopPropagation(); if(window.openAccountSettingsModal) window.openAccountSettingsModal(); }); }
                var ops=document.getElementById('openPrivacySecurityBtn');
                if(ops){ ops.addEventListener('click', function(e){ e.stopPropagation(); if(window.openPrivacySecurityModal) window.openPrivacySecurityModal(); }); }
                var oso=document.getElementById('openSignOutBtn');
                if(oso){ oso.addEventListener('click', function(e){ e.stopPropagation(); if(window.openSignOutModal) window.openSignOutModal(); }); }

                // Account Settings close buttons
                var cas=document.getElementById('closeAccountSettingsBtn');
                if(cas){ cas.addEventListener('click', function(){ if(window.closeAccountSettingsModal) window.closeAccountSettingsModal(); }); }
                var xas=document.getElementById('cancelAccountSettingsBtn');
                if(xas){ xas.addEventListener('click', function(){ if(window.closeAccountSettingsModal) window.closeAccountSettingsModal(); }); }

                // Privacy & Security close buttons
                var cps=document.getElementById('closePrivacySecurityBtn');
                if(cps){ cps.addEventListener('click', function(){ if(window.closePrivacySecurityModal) window.closePrivacySecurityModal(); }); }
                var xps=document.getElementById('cancelPrivacySecurityBtn');
                if(xps){ xps.addEventListener('click', function(){ if(window.closePrivacySecurityModal) window.closePrivacySecurityModal(); }); }

                // Sign Out close buttons
                var cso=document.getElementById('cancelSignOutBtn');
                if(cso){ cso.addEventListener('click', function(){ if(window.closeSignOutModal) window.closeSignOutModal(); }); }
                var cso2=document.getElementById('cancelSignOutBtn2');
                if(cso2){ cso2.addEventListener('click', function(){ if(window.closeSignOutModal) window.closeSignOutModal(); }); }

                // Escape closes any of the three modals
                document.addEventListener('keydown', function(e){ if(e.key==='Escape'){
                  if(window.closeAccountSettingsModal) window.closeAccountSettingsModal();
                  if(window.closePrivacySecurityModal) window.closePrivacySecurityModal();
                  if(window.closeSignOutModal) window.closeSignOutModal();
                }});

                // Backdrop click closes modals
                document.addEventListener('click', function(e){
                  var as=document.getElementById('accountSettingsModal'); if(as && e.target===as){ if(window.closeAccountSettingsModal) window.closeAccountSettingsModal(); }
                  var ps=document.getElementById('privacySecurityModal'); if(ps && e.target===ps){ if(window.closePrivacySecurityModal) window.closePrivacySecurityModal(); }
                  var so=document.getElementById('signOutModal'); if(so && e.target===so){ if(window.closeSignOutModal) window.closeSignOutModal(); }
                });
              });
            </script>

            <!-- Profile, Account, Privacy, and Sign Out modals moved below to be outside main content for full-page overlay -->
        </main>
    </div>

    <!-- Profile Modal (moved outside main content) -->
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

    <!-- Account Settings Modal (moved outside main content) -->
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

    <!-- Privacy & Security Modal (moved outside main content) -->
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

    <!-- Sign Out Modal (moved outside main content) -->
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

    <!-- Add Compliance Modal (moved outside main content) -->
    <div id="addComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="add-compliance-modal-title">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
                <h3 id="add-compliance-modal-title" class="text-lg font-medium text-gray-900">Add New Compliance</h3>
                <button id="closeAddComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="addComplianceForm" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="complianceTitle" class="block text-sm font-medium text-gray-700 mb-1">Compliance Title *</label>
                            <input type="text" id="complianceTitle" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div>
                            <label for="complianceType" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                            <select id="complianceType" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                                <option value="">Select a type</option>
                                <option value="legal">Legal</option>
                                <option value="financial">Financial</option>
                                <option value="hr">HR</option>
                                <option value="safety">Safety</option>
                                <option value="environmental">Environmental</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-1">Due Date *</label>
                            <input type="date" id="dueDate" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                        </div>
                        <div>
                            <label for="responsiblePerson" class="block text-sm font-medium text-gray-700 mb-1">Responsible Person</label>
                            <input type="text" id="responsiblePerson" name="responsible_person" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                        </div>
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                            <select id="priority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                                <option value="critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" id="cancelAddCompliance" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">
                            Save Compliance
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Compliance Modal (moved outside main content) -->
    <div id="viewComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-compliance-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="view-compliance-modal-title" class="font-semibold text-sm text-gray-900 select-none">Compliance Details</h3>
                <button id="closeViewComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8 text-xs text-gray-700 space-y-2">
                <div><span class="font-semibold">Code:</span> <span id="viewComplianceCode"></span></div>
                <div><span class="font-semibold">Title:</span> <span id="viewComplianceTitle"></span></div>
                <div><span class="font-semibold">Type:</span> <span id="viewComplianceType"></span></div>
                <div><span class="font-semibold">Status:</span> <span id="viewComplianceStatus"></span></div>
                <div><span class="font-semibold">Due Date:</span> <span id="viewComplianceDueDate"></span></div>
                <div><span class="font-semibold">Responsible:</span> <span id="viewComplianceResponsible"></span></div>
                <div><span class="font-semibold">Priority:</span> <span id="viewCompliancePriority"></span></div>
                <div><span class="font-semibold">Description:</span> <span id="viewComplianceDescription"></span></div>
                <div class="pt-4 text-right">
                    <button id="closeViewComplianceModal2" type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Compliance Modal (moved outside main content) -->
    <div id="editComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-compliance-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="edit-compliance-modal-title" class="font-semibold text-sm text-gray-900 select-none">Edit Compliance</h3>
                <button id="closeEditComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8">
                <form id="editComplianceForm" class="space-y-3 text-xs text-gray-700">
                    <input type="hidden" id="editComplianceId">
                    <div>
                        <label for="editComplianceTitle" class="block mb-1 font-semibold">Title</label>
                        <input type="text" id="editComplianceTitle" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                    </div>
                    <div>
                        <label for="editComplianceType" class="block mb-1 font-semibold">Type</label>
                        <select id="editComplianceType" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            <option value="legal">Legal</option>
                            <option value="financial">Financial</option>
                            <option value="hr">HR</option>
                            <option value="safety">Safety</option>
                            <option value="environmental">Environmental</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="editComplianceStatus" class="block mb-1 font-semibold">Status</label>
                        <select id="editComplianceStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="overdue">Overdue</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    <div>
                        <label for="editComplianceDueDate" class="block mb-1 font-semibold">Due Date</label>
                        <input type="date" id="editComplianceDueDate" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                    </div>
                    <div>
                        <label for="editComplianceResponsible" class="block mb-1 font-semibold">Responsible Person</label>
                        <input type="text" id="editComplianceResponsible" name="responsible_person" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                    </div>
                    <div>
                        <label for="editCompliancePriority" class="block mb-1 font-semibold">Priority</label>
                        <select id="editCompliancePriority" name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                    <div>
                        <label for="editComplianceDescription" class="block mb-1 font-semibold">Description</label>
                        <textarea id="editComplianceDescription" name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" id="cancelEditCompliance" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                        <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Compliance Modal (moved outside main content) -->
    <div id="deleteComplianceModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-compliance-modal-title">
        <div class="bg-white rounded-md shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="delete-compliance-modal-title" class="font-semibold text-sm text-gray-900 select-none">Delete Compliance</h3>
                <button id="closeDeleteComplianceModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8 text-center">
                <p class="text-xs text-gray-700 mb-4">Are you sure you want to delete <span class="font-semibold" id="deleteComplianceTitle"></span>?</p>
                <div class="flex justify-center space-x-3">
                    <button type="button" id="cancelDeleteCompliance" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                    <button type="button" id="confirmDeleteCompliance" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Delete</button>
                </div>
            </div>
        </div>
    </div>
            <!-- Profile Modal -->
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

            <!-- Account Settings Modal -->
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

            <!-- Privacy & Security Modal -->
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

            <!-- Sign Out Modal -->
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
        </main>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", () => {
    // Element references
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    const toggleBtn = document.getElementById("toggle-btn");
    const overlay = document.getElementById("overlay");
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
    const addComplianceBtn = document.getElementById("addComplianceBtn");
    const addComplianceModal = document.getElementById("addComplianceModal");
    const closeAddComplianceModal = document.getElementById("closeAddComplianceModal");
    const cancelAddCompliance = document.getElementById("cancelAddCompliance");
    const addComplianceForm = document.getElementById("addComplianceForm");
    const tooltipTriggers = document.querySelectorAll('[data-tooltip]');

    // Initialize sidebar state
    if (window.innerWidth >= 768) {
        sidebar.classList.remove("-ml-72");
        mainContent.classList.add("md:ml-72", "sidebar-open");
    } else {
        sidebar.classList.add("-ml-72");
        mainContent.classList.remove("md:ml-72", "sidebar-open");
        mainContent.classList.add("sidebar-closed");
    }

    // Toggle sidebar
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

    // Dropdown functionality
    function bindDropdownListeners() {
        const dropdownToggles = document.querySelectorAll('.has-dropdown > div');
        dropdownToggles.forEach((toggle) => {
            // Skip if already bound
            if (toggle.__dropdownBound) return;
            toggle.__dropdownBound = true;

            // Set accessibility attributes
            const menu = toggle.nextElementSibling;
            const isOpen = menu && !menu.classList.contains('hidden');
            toggle.setAttribute('role', 'button');
            toggle.setAttribute('tabindex', '0');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            // Enhance menu accessibility
            if (menu) {
                menu.setAttribute('role', 'menu');
                menu.querySelectorAll('a').forEach(link => link.setAttribute('role', 'menuitem'));
            }

            // Click handler (avoid double-toggle if inline onclick exists)
            if (!toggle.getAttribute('onclick')) {
                toggle.addEventListener("click", (e) => {
                    e.stopPropagation();
                    window.toggleSidebarDropdown(toggle);
                });
            }

            // Keyboard handler
            toggle.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    window.toggleSidebarDropdown(toggle);
                }
            });
        });
    }

    // User menu functionality
    function bindUserMenuListeners() {
        // If the early navbar script already bound handlers, avoid double-binding
        if (window.__complianceMenusBound) return;
        if (!userMenuBtn || userMenuBtn.__userMenuBound) return;
        userMenuBtn.__userMenuBound = true;

        // Set accessibility attributes
        userMenuBtn.setAttribute('role', 'button');
        userMenuBtn.setAttribute('tabindex', '0');
        userMenuBtn.setAttribute('aria-expanded', userMenuDropdown && !userMenuDropdown.classList.contains('hidden') ? 'true' : 'false');

        // Enhance dropdown accessibility
        if (userMenuDropdown) {
            userMenuDropdown.setAttribute('role', 'menu');
            userMenuDropdown.querySelectorAll('a').forEach(link => link.setAttribute('role', 'menuitem'));
        }

        // Click handler
        userMenuBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            if (userMenuDropdown) {
                userMenuDropdown.classList.toggle("hidden");
                userMenuBtn.setAttribute('aria-expanded', userMenuDropdown.classList.contains('hidden') ? 'false' : 'true');
                notificationDropdown.classList.add("hidden");
                closeAllModals();
                closeAllSidebarDropdowns(); // Close sidebar dropdowns when opening user menu
            }
        });

        // Keyboard handler
        userMenuBtn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (userMenuDropdown) {
                    userMenuDropdown.classList.toggle("hidden");
                    userMenuBtn.setAttribute('aria-expanded', userMenuDropdown.classList.contains('hidden') ? 'false' : 'true');
                    notificationDropdown.classList.add("hidden");
                    closeAllModals();
                    closeAllSidebarDropdowns();
                }
            }
        });
    }

    function closeAllSidebarDropdowns(except = null) {
        const dropdownToggles = document.querySelectorAll('.has-dropdown > div');
        dropdownToggles.forEach((toggle) => {
            if (toggle === except) return;
            const menu = toggle.nextElementSibling;
            const chev = toggle.querySelector('.bx-chevron-down');
            if (menu && !menu.classList.contains('hidden')) {
                menu.classList.add('hidden');
                toggle.setAttribute('aria-expanded', 'false');
            }
            if (chev) chev.classList.remove('rotate-180');
        });
    }

    // Global function for inline onclick handlers
    window.toggleSidebarDropdown = function(el) {
        if (!el) return;
        const menu = el.nextElementSibling;
        const chev = el.querySelector('.bx-chevron-down');
        closeAllSidebarDropdowns(el);
        if (menu) {
            menu.classList.toggle('hidden');
            el.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
        }
        if (chev) chev.classList.toggle('rotate-180');
        // Close user menu and notification dropdown when sidebar dropdown is toggled
        if (userMenuDropdown) userMenuDropdown.classList.add('hidden');
        if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
        if (notificationDropdown) notificationDropdown.classList.add('hidden');
    };

    // Close dropdowns and user menu on outside click or Escape key
    document.addEventListener("click", (e) => {
        if (!e.target.closest('.has-dropdown')) closeAllSidebarDropdowns();
        if (!e.target.closest('#userMenuDropdown') && !e.target.closest('#userMenuBtn')) {
            if (userMenuDropdown && !userMenuDropdown.classList.contains('hidden')) {
                userMenuDropdown.classList.add('hidden');
                if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
            }
        }
        if (!e.target.closest('#notificationDropdown') && !e.target.closest('#notificationBtn')) {
            if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
                notificationDropdown.classList.add('hidden');
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeAllSidebarDropdowns();
            if (userMenuDropdown && !userMenuDropdown.classList.contains('hidden')) {
                userMenuDropdown.classList.add('hidden');
                if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
            }
            if (notificationDropdown && !notificationDropdown.classList.contains('hidden')) {
                notificationDropdown.classList.add('hidden');
            }
            if (addComplianceModal && !addComplianceModal.classList.contains('hidden')) {
                addComplianceModal.classList.add('hidden');
                addComplianceModal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    });

    // Auto-expand sidebar dropdown based on URL
    function autoExpandDropdowns() {
        try {
            const currentPath = window.location.pathname.replace(/\/$/, '');
            const links = document.querySelectorAll('#sidebar .dropdown-menu a');
            links.forEach((link) => {
                let linkPath;
                try {
                    linkPath = new URL(link.href, window.location.origin).pathname;
                } catch (_) {
                    linkPath = link.getAttribute('href') || '';
                }
                if (linkPath) linkPath = linkPath.replace(/\/$/, '');
                const isMatch = linkPath && (
                    currentPath === linkPath ||
                    currentPath.endsWith(linkPath) ||
                    linkPath.endsWith(currentPath)
                );
                if (isMatch) {
                    const menu = link.closest('.dropdown-menu');
                    if (menu) {
                        menu.classList.remove('hidden');
                        const toggle = menu.previousElementSibling;
                        if (toggle) {
                            toggle.setAttribute('aria-expanded', 'true');
                            const chev = toggle.querySelector('.bx-chevron-down');
                            if (chev) chev.classList.add('rotate-180');
                        }
                    }
                    link.classList.add('bg-white/30');
                }
            });
        } catch (err) {
            console.error('Error in auto-expand dropdowns:', err);
        }
    }

    // Sidebar and modal event listeners
    if (overlay) {
        overlay.addEventListener("click", () => {
            sidebar.classList.add("-ml-72");
            overlay.classList.add("hidden");
            document.body.style.overflow = "";
            mainContent.classList.remove("sidebar-open");
            mainContent.classList.add("sidebar-closed");
            closeAllSidebarDropdowns();
            if (userMenuDropdown) userMenuDropdown.classList.add('hidden');
            if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
            if (notificationDropdown) notificationDropdown.classList.add('hidden');
        });
    }

    if (toggleBtn) toggleBtn.addEventListener("click", toggleSidebar);

    if (notificationBtn) {
        notificationBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle("hidden");
            if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
            if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
            closeAllModals();
            closeAllSidebarDropdowns();
        });
    }

    // Function to close all modals
    function closeAllModals(except = null) {
        const modals = [profileModal, accountSettingsModal, privacySecurityModal, signOutModal, addComplianceModal];
        modals.forEach((modal) => {
            if (modal && modal !== except && !modal.classList.contains('hidden')) {
                modal.classList.add("hidden");
                modal.classList.remove("active");
                document.body.style.overflow = '';
            }
        });
    }

    // Modal event listeners
    if (openProfileBtn) {
        openProfileBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            profileModal.classList.remove("hidden");
            profileModal.classList.add("active");
            if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
            if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
            closeAllModals(profileModal);
            closeAllSidebarDropdowns();
        });
    }

    if (closeProfileBtn) closeProfileBtn.addEventListener("click", () => closeModal(profileModal));
    if (closeProfileBtn2) closeProfileBtn2.addEventListener("click", () => closeModal(profileModal));

    if (openAccountSettingsBtn) {
        openAccountSettingsBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            accountSettingsModal.classList.remove("hidden");
            accountSettingsModal.classList.add("active");
            if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
            if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
            closeAllModals(accountSettingsModal);
            closeAllSidebarDropdowns();
        });
    }

    if (closeAccountSettingsBtn) closeAccountSettingsBtn.addEventListener("click", () => closeModal(accountSettingsModal));
    if (cancelAccountSettingsBtn) cancelAccountSettingsBtn.addEventListener("click", () => closeModal(accountSettingsModal));

    if (openPrivacySecurityBtn) {
        openPrivacySecurityBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            privacySecurityModal.classList.remove("hidden");
            privacySecurityModal.classList.add("active");
            if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
            if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
            closeAllModals(privacySecurityModal);
            closeAllSidebarDropdowns();
        });
    }

    if (closePrivacySecurityBtn) closePrivacySecurityBtn.addEventListener("click", () => closeModal(privacySecurityModal));
    if (cancelPrivacySecurityBtn) cancelPrivacySecurityBtn.addEventListener("click", () => closeModal(privacySecurityModal));

    if (openSignOutBtn) {
        openSignOutBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            signOutModal.classList.remove("hidden");
            signOutModal.classList.add("active");
            if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
            if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
            closeAllModals(signOutModal);
            closeAllSidebarDropdowns();
        });
    }

    if (cancelSignOutBtn) cancelSignOutBtn.addEventListener("click", () => closeModal(signOutModal));
    if (cancelSignOutBtn2) cancelSignOutBtn2.addEventListener("click", () => closeModal(signOutModal));

    // Add New Compliance Modal
    if (addComplianceBtn) {
        addComplianceBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            if (addComplianceModal) {
                addComplianceModal.classList.remove("hidden");
                addComplianceModal.classList.add("active");
                document.body.style.overflow = 'hidden';
                if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                if (notificationDropdown) notificationDropdown.classList.add("hidden");
                closeAllModals(addComplianceModal);
                closeAllSidebarDropdowns();
                const firstInput = addComplianceModal.querySelector('input');
                if (firstInput) firstInput.focus();
            }
        });
    }

    if (closeAddComplianceModal) {
        closeAddComplianceModal.addEventListener("click", () => closeModal(addComplianceModal));
    }
    if (cancelAddCompliance) {
        cancelAddCompliance.addEventListener("click", () => closeModal(addComplianceModal));
    }

    // Helper function to close a modal
    function closeModal(modal) {
        if (modal) {
            modal.classList.add("hidden");
            modal.classList.remove("active");
            document.body.style.overflow = '';
        }
    }

    // Add Compliance Form Submission
    if (addComplianceForm) {
        addComplianceForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            const submitButton = e.target.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            const title = document.getElementById('complianceTitle').value.trim();
            const type = document.getElementById('complianceType').value;
            const dueDate = document.getElementById('dueDate').value;

            if (!title || !type || !dueDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields (Title, Type, Due Date).',
                    confirmButtonColor: '#2f855a'
                });
                submitButton.disabled = false;
                return;
            }

            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());

            try {
                const resp = await fetch('{{ route('compliance.create') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams(data)
                });

                const result = await resp.json();
                if (result.success && result.compliance) {
                    storeComplianceInBackup(result.compliance);
                    addComplianceModal.classList.add("hidden");
                    addComplianceModal.classList.remove("active");
                    document.body.style.overflow = '';
                    addComplianceForm.reset();
                    addComplianceToTable(result.compliance);
                    updateStats();

                    Swal.fire({
                        icon: 'success',
                        title: 'Compliance Added',
                        text: 'The compliance has been added successfully.',
                        confirmButtonColor: '#2f855a'
                    });
                } else {
                    throw new Error(result.message || 'Failed to add compliance.');
                }
            } catch (error) {
                console.error('Error adding compliance:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while adding the compliance.',
                    confirmButtonColor: '#2f855a'
                });
            } finally {
                submitButton.disabled = false;
            }
        });
    }

    // Function to add compliance to table dynamically
    function addComplianceToTable(compliance) {
        const tbody = document.querySelector('tbody');
        const emptyRow = tbody.querySelector('tr td[colspan="6"]');
        if (emptyRow) emptyRow.closest('tr').remove();

        const today = new Date();
        const dueDate = new Date(compliance.due_date);
        const daysUntilDue = Math.round((dueDate - today) / (1000 * 60 * 60 * 24));
        const daysText = daysUntilDue > 0 ? `in ${daysUntilDue} days` : (daysUntilDue === 0 ? 'today' : `${Math.abs(daysUntilDue)} days overdue`);

        const statusClasses = {
            active: 'bg-green-100 text-green-800',
            pending: 'bg-yellow-100 text-yellow-800',
            overdue: 'bg-red-100 text-red-800',
            completed: 'bg-blue-100 text-blue-800'
        }[compliance.status] || 'bg-gray-100 text-gray-800';

        const tr = document.createElement('tr');
        tr.className = 'table-row';
        tr.dataset.id = compliance.id;
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${compliance.code || 'CPL-' + compliance.id}</div>
                <div class="text-xs text-gray-500">Created: ${new Date(compliance.created_at).toISOString().split('T')[0]}</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900">${compliance.title}</div>
                <div class="text-xs text-gray-500">${compliance.description ? compliance.description.substring(0, 50) + (compliance.description.length > 50 ? '...' : '') : 'No description'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">${compliance.type.charAt(0).toUpperCase() + compliance.type.slice(1)}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${compliance.due_date.split('T')[0]}</div>
                <div class="text-xs text-gray-500">${daysText}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClasses}">${compliance.status.charAt(0).toUpperCase() + compliance.status.slice(1)}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="#" class="viewComplianceBtn text-[#2f855A] hover:text-[#1a4d38] mr-3" data-tooltip="View" 
                   data-id="${compliance.id}" data-code="${compliance.code || 'CPL-' + compliance.id}" 
                   data-title="${compliance.title}" data-type="${compliance.type.charAt(0).toUpperCase() + compliance.type.slice(1)}" 
                   data-status="${compliance.status.charAt(0).toUpperCase() + compliance.status.slice(1)}" 
                   data-due-date="${compliance.due_date.split('T')[0]}" 
                   data-description="${compliance.description || ''}" 
                   data-responsible="${compliance.responsible_person || ''}" 
                   data-priority="${compliance.priority || 'Medium'}"><i class="fas fa-eye"></i></a>
                <a href="#" class="editComplianceBtn text-blue-600 hover:text-blue-900 mr-3" data-tooltip="Edit" 
                   data-id="${compliance.id}" data-title="${compliance.title}" 
                   data-type="${compliance.type}" data-status="${compliance.status}" 
                   data-due-date="${compliance.due_date.split('T')[0]}" 
                   data-description="${compliance.description || ''}" 
                   data-responsible="${compliance.responsible_person || ''}" 
                   data-priority="${compliance.priority || 'Medium'}"><i class="fas fa-edit"></i></a>
                <a href="#" class="deleteComplianceBtn text-red-600 hover:text-red-900" data-tooltip="Delete" 
                   data-id="${compliance.id}" data-title="${compliance.title}"><i class="fas fa-trash"></i></a>
            </td>
        `;
        tbody.insertBefore(tr, tbody.firstChild);
        attachEventListenersToTable();
    }

    // Function to update stats
    function updateStats() {
        const tbody = document.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr.table-row');
        let activeCount = 0;
        let pendingCount = 0;

        rows.forEach(row => {
            const status = row.querySelector('td:nth-child(5) span').textContent.toLowerCase();
            if (status === 'active' || status === 'on track') activeCount++;
            if (status === 'pending') pendingCount++;
        });

        const activeStat = document.querySelector('.dashboard-card:nth-child(1) .font-extrabold');
        const pendingStat = document.querySelector('.dashboard-card:nth-child(2) .font-extrabold');
        if (activeStat) activeStat.textContent = activeCount;
        if (pendingStat) pendingStat.textContent = pendingCount;
    }

    // Store compliance in localStorage as backup
    function storeComplianceInBackup(compliance) {
        const existingData = JSON.parse(localStorage.getItem('compliance_backup') || '[]');
        existingData.push(compliance);
        localStorage.setItem('compliance_backup', JSON.stringify(existingData));
    }

    // Handle View/Edit/Delete buttons
    const viewComplianceModal = document.getElementById('viewComplianceModal');
    const editComplianceModal = document.getElementById('editComplianceModal');
    const deleteComplianceModal = document.getElementById('deleteComplianceModal');
    const closeViewComplianceModal = document.getElementById('closeViewComplianceModal');
    const closeViewComplianceModal2 = document.getElementById('closeViewComplianceModal2');
    const closeEditComplianceModal = document.getElementById('closeEditComplianceModal');
    const cancelEditCompliance = document.getElementById('cancelEditCompliance');
    const closeDeleteComplianceModal = document.getElementById('closeDeleteComplianceModal');
    const cancelDeleteCompliance = document.getElementById('cancelDeleteCompliance');
    const confirmDeleteCompliance = document.getElementById('confirmDeleteCompliance');

    function openViewModal(data) {
        document.getElementById('viewComplianceCode').textContent = data.code;
        document.getElementById('viewComplianceTitle').textContent = data.title;
        document.getElementById('viewComplianceType').textContent = data.type;
        document.getElementById('viewComplianceStatus').textContent = data.status;
        document.getElementById('viewComplianceDueDate').textContent = data.dueDate;
        document.getElementById('viewComplianceResponsible').textContent = data.responsible || 'Not assigned';
        document.getElementById('viewCompliancePriority').textContent = data.priority || 'Medium';
        document.getElementById('viewComplianceDescription').textContent = data.description || 'No description';
        viewComplianceModal.classList.remove('hidden');
        viewComplianceModal.classList.add('active');
    }

    function openEditModal(data) {
        document.getElementById('editComplianceId').value = data.id;
        document.getElementById('editComplianceTitle').value = data.title;
        document.getElementById('editComplianceType').value = data.type.toLowerCase();
        document.getElementById('editComplianceStatus').value = data.status.toLowerCase();
        document.getElementById('editComplianceDueDate').value = data.dueDate;
        document.getElementById('editComplianceResponsible').value = data.responsible || '';
        document.getElementById('editCompliancePriority').value = data.priority.toLowerCase() || 'medium';
        document.getElementById('editComplianceDescription').value = data.description || '';
        editComplianceModal.classList.remove('hidden');
        editComplianceModal.classList.add('active');
    }

    function openDeleteModal(data) {
        document.getElementById('deleteComplianceTitle').textContent = data.title;
        deleteComplianceModal.classList.remove('hidden');
        deleteComplianceModal.classList.add('active');
    }

    function attachEventListenersToTable() {
        const viewBtns = document.querySelectorAll('.viewComplianceBtn');
        const editBtns = document.querySelectorAll('.editComplianceBtn');
        const deleteBtns = document.querySelectorAll('.deleteComplianceBtn');

        viewBtns.forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        editBtns.forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });
        deleteBtns.forEach(btn => {
            btn.replaceWith(btn.cloneNode(true));
        });

        const newViewBtns = document.querySelectorAll('.viewComplianceBtn');
        const newEditBtns = document.querySelectorAll('.editComplianceBtn');
        const newDeleteBtns = document.querySelectorAll('.deleteComplianceBtn');

        newViewBtns.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const d = e.currentTarget.dataset;
            openViewModal({
                code: d.code,
                title: d.title,
                type: d.type,
                status: d.status,
                dueDate: d.dueDate,
                responsible: d.responsible,
                priority: d.priority,
                description: d.description
            });
        }));

        newEditBtns.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const d = e.currentTarget.dataset;
            openEditModal({
                id: d.id,
                title: d.title,
                type: d.type,
                status: d.status,
                dueDate: d.dueDate,
                responsible: d.responsible,
                priority: d.priority,
                description: d.description
            });
        }));

        newDeleteBtns.forEach(btn => btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const d = e.currentTarget.dataset;
            openDeleteModal({ id: d.id, title: d.title });
        }));
    }

    // Modal close handlers
    if (closeViewComplianceModal) closeViewComplianceModal.addEventListener('click', () => closeModal(viewComplianceModal));
    if (closeViewComplianceModal2) closeViewComplianceModal2.addEventListener('click', () => closeModal(viewComplianceModal));
    if (viewComplianceModal) viewComplianceModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

    if (closeEditComplianceModal) closeEditComplianceModal.addEventListener('click', () => closeModal(editComplianceModal));
    if (cancelEditCompliance) cancelEditCompliance.addEventListener('click', () => closeModal(editComplianceModal));
    if (editComplianceModal) editComplianceModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

    if (closeDeleteComplianceModal) closeDeleteComplianceModal.addEventListener('click', () => closeModal(deleteComplianceModal));
    if (cancelDeleteCompliance) cancelDeleteCompliance.addEventListener('click', () => closeModal(deleteComplianceModal));
    if (deleteComplianceModal) deleteComplianceModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());

    // Edit and Delete form submissions
    if (document.getElementById('editComplianceForm')) {
        document.getElementById('editComplianceForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData.entries());
            data.id = document.getElementById('editComplianceId').value;

            try {
                const resp = await fetch('{{ route('compliance.update') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams(data)
                });
                const result = await resp.json();
                if (result.success) {
                    closeModal(editComplianceModal);
                    await loadComplianceData();
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated',
                        text: 'Compliance updated successfully.',
                        confirmButtonColor: '#2f855a'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: result.message || 'Update failed.',
                        confirmButtonColor: '#2f855a'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the compliance.',
                    confirmButtonColor: '#2f855a'
                });
            }
        });
    }

    if (confirmDeleteCompliance) {
        confirmDeleteCompliance.addEventListener('click', async () => {
            const id = document.getElementById('deleteComplianceTitle').closest('.modal').querySelector('[data-id]')?.dataset?.id;
            if (!id) return;

            try {
                const resp = await fetch('{{ route('compliance.delete') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({ id })
                });
                const result = await resp.json();
                if (result.success) {
                    closeModal(deleteComplianceModal);
                    await loadComplianceData();
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted',
                        text: 'Compliance deleted successfully.',
                        confirmButtonColor: '#2f855a'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: result.message || 'Delete failed.',
                        confirmButtonColor: '#2f855a'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting the compliance.',
                    confirmButtonColor: '#2f855a'
                });
            }
        });
    }

    // Load compliance data
    async function loadComplianceData() {
        try {
            const resp = await fetch('{{ route('document.compliance.tracking') }}', {
                method: 'GET',
                headers: { 'Accept': 'text/html' }
            });
            if (resp.ok) {
                const html = await resp.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTableBody = doc.querySelector('tbody');
                if (newTableBody) {
                    const currentTableBody = document.querySelector('tbody');
                    currentTableBody.innerHTML = newTableBody.innerHTML;
                    attachEventListenersToTable();
                    updateStatsFromServer(doc);
                }
            }
        } catch (error) {
            console.error('Error loading compliance data:', error);
            loadBackupData();
        }
    }

    function updateStatsFromServer(doc) {
        const statsCards = doc.querySelectorAll('.dashboard-card');
        const currentStatsCards = document.querySelectorAll('.dashboard-card');
        if (statsCards.length === currentStatsCards.length) {
            statsCards.forEach((card, index) => {
                const statValue = card.querySelector('.font-extrabold');
                if (statValue && currentStatsCards[index]) {
                    const currentStatValue = currentStatsCards[index].querySelector('.font-extrabold');
                    if (currentStatValue) currentStatValue.textContent = statValue.textContent;
                }
            });
        }
    }

    function loadBackupData() {
        const backupData = JSON.parse(localStorage.getItem('compliance_backup') || '[]');
        if (backupData.length > 0) {
            const tbody = document.querySelector('tbody');
            const emptyRow = tbody.querySelector('tr td[colspan="6"]');
            if (emptyRow) emptyRow.closest('tr').remove();
            backupData.forEach(compliance => addComplianceToTable(compliance));
        }
    }

    // Initialize compliance tracking
    function initComplianceTracking() {
        bindDropdownListeners();
        bindUserMenuListeners();
        autoExpandDropdowns();
        attachEventListenersToTable();
        loadComplianceData();

        // Close modals and dropdowns on outside click
        window.addEventListener("click", (e) => {
            if (!e.target.closest('#notificationDropdown') && !e.target.closest('#notificationBtn')) {
                if (notificationDropdown) notificationDropdown.classList.add("hidden");
            }
            if (!e.target.closest('#userMenuDropdown') && !e.target.closest('#userMenuBtn')) {
                if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
            }
            if (!e.target.closest('#profileModal') && !e.target.closest('#openProfileBtn')) {
                closeModal(profileModal);
            }
            if (!e.target.closest('#accountSettingsModal') && !e.target.closest('#openAccountSettingsBtn')) {
                closeModal(accountSettingsModal);
            }
            if (!e.target.closest('#privacySecurityModal') && !e.target.closest('#openPrivacySecurityBtn')) {
                closeModal(privacySecurityModal);
            }
            if (!e.target.closest('#signOutModal') && !e.target.closest('#openSignOutBtn')) {
                closeModal(signOutModal);
            }
            if (!e.target.closest('#addComplianceModal') && !e.target.closest('#addComplianceBtn')) {
                closeModal(addComplianceModal);
            }
        });

        // Stop propagation for modal content
        [profileModal, accountSettingsModal, privacySecurityModal, signOutModal, addComplianceModal].forEach(modal => {
            if (modal) {
                const content = modal.querySelector("div");
                if (content) content.addEventListener("click", (e) => e.stopPropagation());
            }
        });

        // Resize handler
        window.addEventListener("resize", () => {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove("-ml-72");
                overlay.classList.add("hidden");
                document.body.style.overflow = "";
                mainContent.classList.add("md:ml-72", "sidebar-open");
                mainContent.classList.remove("sidebar-closed");
            } else {
                sidebar.classList.add("-ml-72");
                mainContent.classList.remove("md:ml-72", "sidebar-open");
                mainContent.classList.add("sidebar-closed");
                overlay.classList.add("hidden");
                document.body.style.overflow = "";
            }
            closeAllSidebarDropdowns();
            if (userMenuDropdown) userMenuDropdown.classList.add('hidden');
            if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
            if (notificationDropdown) notificationDropdown.classList.add('hidden');
        });

        // Tooltip handlers
        tooltipTriggers.forEach(trigger => {
            trigger.addEventListener('mouseenter', e => {
                const tooltip = document.createElement('div');
                tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg';
                tooltip.textContent = e.target.dataset.tooltip;
                document.body.appendChild(tooltip);
                const rect = e.target.getBoundingClientRect();
                tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
                tooltip.style.left = `${rect.left + window.scrollX}px`;
                e.target._tooltip = tooltip;
            });
            trigger.addEventListener('mouseleave', e => {
                if (e.target._tooltip) {
                    e.target._tooltip.remove();
                    delete e.target._tooltip;
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initComplianceTracking);
    } else {
        initComplianceTracking();
    }
});
</script>
</body>
</html>