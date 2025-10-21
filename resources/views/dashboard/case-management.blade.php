@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Case Management | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            transition: max-width 0.3s ease-in-out;
        }

        #sidebar.md\:ml-0 ~ #main-content .dashboard-container {
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

        /* Sidebar */
        #sidebar {
            transition: margin-left 0.3s ease-in-out;
        }

        /* Overlay */
        #overlay {
            transition: opacity 0.3s ease-in-out;
        }
        
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none" aria-controls="sidebar" aria-expanded="false">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Case Management</h1>
            </div>
            <div class="flex items-center space-x-1">
                <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn" aria-expanded="false">
                    <i class="fa-solid fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 bg-red-500 text-xs text-white rounded-full px-1">3</span>
                </button>
                <div onclick="toggleUserMenu(event)" class="flex items-center space-x-2 cursor-pointer px-3 py-2 transition duration-200" id="userMenuBtn" aria-label="User menu" aria-haspopup="true" aria-expanded="false">
                    <i class="fa-solid fa-user text-[18px] bg-white text-[#28644c] px-2.5 py-2 rounded-full"></i>
                    <span class="text-white font-medium">{{ $user->name }}</span>
                    <i class="fa-solid fa-chevron-down text-sm"></i>
                </div>
            </div>
        </div>
    </nav>
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
      if (typeof window.openProfileModal !== 'function') {
        window.openProfileModal = function(){
          var m=document.getElementById('profileModal'); if(!m) return;
          m.classList.add('active'); m.classList.remove('hidden'); m.style.display='flex';
          var d=document.getElementById('userMenuDropdown'); if(d) d.classList.add('hidden');
          var b=document.getElementById('userMenuBtn'); if(b) b.setAttribute('aria-expanded','false');
        };
      }
      if (typeof window.closeProfileModal !== 'function') {
        window.closeProfileModal = function(){
          var m=document.getElementById('profileModal'); if(!m) return;
          m.classList.remove('active'); m.classList.add('hidden'); m.style.display='none';
        };
      }
      if (typeof window.openAccountSettingsModal !== 'function') {
        window.openAccountSettingsModal = function(){
          var m=document.getElementById('accountSettingsModal'); if(!m) return;
          m.classList.add('active'); m.classList.remove('hidden'); m.style.display='flex';
          var d=document.getElementById('userMenuDropdown'); if(d) d.classList.add('hidden');
          var b=document.getElementById('userMenuBtn'); if(b) b.setAttribute('aria-expanded','false');
        };
      }
      if (typeof window.closeAccountSettingsModal !== 'function') {
        window.closeAccountSettingsModal = function(){
          var m=document.getElementById('accountSettingsModal'); if(!m) return;
          m.classList.remove('active'); m.classList.add('hidden'); m.style.display='none';
        };
      }
      if (typeof window.openPrivacySecurityModal !== 'function') {
        window.openPrivacySecurityModal = function(){
          var m=document.getElementById('privacySecurityModal'); if(!m) return;
          m.classList.add('active'); m.classList.remove('hidden'); m.style.display='flex';
          var d=document.getElementById('userMenuDropdown'); if(d) d.classList.add('hidden');
          var b=document.getElementById('userMenuBtn'); if(b) b.setAttribute('aria-expanded','false');
        };
      }
      if (typeof window.closePrivacySecurityModal !== 'function') {
        window.closePrivacySecurityModal = function(){
          var m=document.getElementById('privacySecurityModal'); if(!m) return;
          m.classList.remove('active'); m.classList.add('hidden'); m.style.display='none';
        };
      }
      if (typeof window.openSignOutModal !== 'function') {
        window.openSignOutModal = function(){
          var m=document.getElementById('signOutModal'); if(!m) return;
          m.classList.add('active'); m.classList.remove('hidden'); m.style.display='flex';
          var d=document.getElementById('userMenuDropdown'); if(d) d.classList.add('hidden');
          var b=document.getElementById('userMenuBtn'); if(b) b.setAttribute('aria-expanded','false');
        };
      }
      if (typeof window.closeSignOutModal !== 'function') {
        window.closeSignOutModal = function(){
          var m=document.getElementById('signOutModal'); if(!m) return;
          m.classList.remove('active'); m.classList.add('hidden'); m.style.display='none';
        };
      }
      // Notification dropdown toggle
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

      // New Case modal helpers (global)
      if (typeof window.openNewCaseModal !== 'function') {
        window.openNewCaseModal = function(){
          try{
            var now = new Date();
            var randomNum = Math.floor(1000 + Math.random() * 9000);
            var caseNumEl = document.getElementById('caseNumber');
            if (caseNumEl) caseNumEl.value = 'C-' + now.getFullYear() + '-' + randomNum;
            var filingDateEl = document.getElementById('filingDate');
            if (filingDateEl) filingDateEl.valueAsDate = new Date();
            if (typeof window.showModal === 'function') {
              window.showModal('newCaseModal');
            } else {
              var modal = document.getElementById('newCaseModal');
              if (modal){ modal.classList.remove('hidden'); modal.classList.add('active'); document.body.style.overflow='hidden'; }
            }
          }catch(e){}
        };
      }
      if (typeof window.showModal !== 'function') {
        window.showModal = function(modalId){
          var modal = document.getElementById(modalId);
          if (!modal) return;
          modal.style.display = 'flex';
          // force reflow for transition
          void modal.offsetWidth;
          modal.classList.add('active');
          document.body.style.overflow = 'hidden';
        };
      }
      if (typeof window.closeModal !== 'function') {
        window.closeModal = function(modalId){
          var modal = document.getElementById(modalId);
          if (!modal) return;
          modal.classList.remove('active');
          setTimeout(function(){
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
          }, 300);
          if (modalId === 'newCaseModal'){
            var form = document.getElementById('newCaseForm');
            if (form) form.reset();
          }
        };
      }
      if (typeof window.submitNewCase !== 'function') {
        window.submitNewCase = async function(){
          var form = document.getElementById('newCaseForm');
          if (!form) return;
          var submitBtn = form.querySelector('button[type="button"]');
          var originalBtnText = submitBtn ? submitBtn.innerHTML : '';
          if (submitBtn){ submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...'; }
          try{
            var formData = new FormData(form);
            var tokenMeta = document.querySelector('meta[name="csrf-token"]');
            var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
            var response = await fetch('{{ route("case.create") }}', {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
              body: formData
            });
            var data = await response.json();
            if (!response.ok){ throw new Error(data.message || 'Failed to create case'); }
            await Swal.fire({ icon: 'success', title: 'Success!', text: 'Case has been created successfully.', showConfirmButton: false, timer: 1500 });
            form.reset();
            window.closeModal('newCaseModal');
            window.location.reload();
          }catch(error){
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: (error && error.message) || 'Failed to create case. Please try again.', confirmButtonColor: '#2f855a' });
          }finally{
            if (submitBtn){ submitBtn.disabled = false; submitBtn.innerHTML = originalBtnText; }
          }
        };
      }

      // Utility to hide all menus
      if (typeof window.hideAllMenus !== 'function') {
        window.hideAllMenus = function(){
          var ud=document.getElementById('userMenuDropdown');
          var nd=document.getElementById('notificationDropdown');
          var ub=document.getElementById('userMenuBtn');
          var nb=document.getElementById('notificationBtn');
          if(ud){ ud.classList.add('hidden'); }
          if(nd){ nd.classList.add('hidden'); }
          if(ub){ ub.setAttribute('aria-expanded','false'); }
          if(nb){ nb.setAttribute('aria-expanded','false'); }
        };
      }

      // One-time setup of global listeners
      (function(){
        if (window.__menusBound) return; window.__menusBound = true;
        // Bind user menu button click
        var ub=document.getElementById('userMenuBtn');
        if(ub){
          ub.addEventListener('click', function(e){ if(window.toggleUserMenu) window.toggleUserMenu(e); });
        }
        // Bind notification button click
        var nb=document.getElementById('notificationBtn');
        if(nb){
          nb.addEventListener('click', function(e){ window.toggleNotification(e); });
        }
        // Close on outside click
        document.addEventListener('click', function(e){
          var ud=document.getElementById('userMenuDropdown');
          var ub=document.getElementById('userMenuBtn');
          var nd=document.getElementById('notificationDropdown');
          var nb=document.getElementById('notificationBtn');
          var clickInsideUser = (ub && (ub.contains(e.target) || (ud && ud.contains(e.target))));
          var clickInsideNotif = (nb && (nb.contains(e.target) || (nd && nd.contains(e.target))));
          if(!clickInsideUser && !clickInsideNotif){ window.hideAllMenus(); }
        });
        // Close on Escape
        document.addEventListener('keydown', function(e){
          if(e.key === 'Escape'){ window.hideAllMenus(); }
        });
      })();
    </script>

    <!-- Notification Dropdown -->
    <div id="notificationDropdown" class="hidden absolute right-4 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 text-gray-800 z-50" style="top: 4rem;">
        <div class="flex justify-between items-center px-4 py-2 border-b border-gray-200">
            <span class="font-semibold text-sm">Notifications</span>
            <span class="bg-red-600 text-white text-xs font-semibold rounded-full px-2 py-0.5">3 new</span>
        </div>
        <ul class="divide-y divide-gray-200 max-h-72 overflow-y-auto">
            <li class="flex items-start px-4 py-3 space-x-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="bg-green-200 text-green-700 rounded-full p-2">
                        <i class="fas fa-gavel"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">New Case Assignment</p>
                    <p class="text-sm text-gray-500">Case #C-2023-045 has been assigned to you</p>
                    <p class="text-xs text-gray-400 mt-1">10 minutes ago</p>
                </div>
            </li>
            <li class="flex items-start px-4 py-3 space-x-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="bg-blue-200 text-blue-700 rounded-full p-2">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Upcoming Hearing</p>
                    <p class="text-sm text-gray-500">Hearing for Case #C-2023-042 is tomorrow at 10:00 AM</p>
                    <p class="text-xs text-gray-400 mt-1">1 hour ago</p>
                </div>
            </li>
            <li class="flex items-start px-4 py-3 space-x-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="bg-yellow-200 text-yellow-700 rounded-full p-2">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900">Deadline Approaching</p>
                    <p class="text-sm text-gray-500">Filing deadline for Case #C-2023-040 is in 2 days</p>
                    <p class="text-xs text-gray-400 mt-1">3 hours ago</p>
                </div>
            </li>
        </ul>
        <div class="bg-gray-50 px-4 py-2 text-center">
            <a href="#" class="text-sm font-medium text-[#2f855A] hover:text-[#1a4d38]">View all notifications</a>
        </div>
    </div>

    <!-- User Menu Dropdown -->
    <!-- User Menu Dropdown -->
    <div id="userMenuDropdown" class="hidden absolute right-4 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200" style="top: 4rem; z-index:60;" role="menu" aria-labelledby="userMenuBtn">
        <div class="py-4 px-6 border-b border-gray-100 text-center">
            <div class="w-14 h-14 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-2">
                <i class="fas fa-user-circle text-3xl"></i>
            </div>
            <p class="font-semibold text-[#28644c]">{{ $user->name }}</p>
            <p class="text-xs text-gray-400">Administrator</p>
        </div>
        <ul class="text-sm text-gray-700">
            <li><button id="openProfileBtn" onclick="(function(e){ e&&e.stopPropagation&&e.stopPropagation(); openProfileModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-user-circle mr-2"></i> My Profile</button></li>
            <li><button id="openAccountSettingsBtn" onclick="(function(e){ e&&e.stopPropagation&&e.stopPropagation(); openAccountSettingsModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-cog mr-2"></i> Account Settings</button></li>
            <li><button id="openPrivacySecurityBtn" onclick="(function(e){ e&&e.stopPropagation&&e.stopPropagation(); openPrivacySecurityModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-shield-alt mr-2"></i> Privacy & Security</button></li>
            <li><button id="signOutBtn" onclick="(function(e){ e&&e.stopPropagation&&e.stopPropagation(); openSignOutModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</button></li>
        </ul>
    </div>

    <div class="flex w-full min-h-screen pt-16">
        <div id="overlay" class="hidden fixed inset-0 bg-black opacity-50 z-40"></div>

        <!-- Sidebar -->
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
                        <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" aria-expanded="false" role="button">
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
                        <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" aria-expanded="false" role="button">
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
                        <div class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" aria-expanded="true" role="button">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-file"></i>
                                <span>Legal Management</span>
                            </div>
                            <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
                        </div>
                        <ul class="dropdown-menu bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('case.management') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-briefcase mr-2"></i>Case Management</a></li>
                            <li><a href="{{ route('contract.management') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-file-blank mr-2"></i>Contract Management</a></li>
                            <li><a href="{{ route('compliance.tracking') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-check-double mr-2"></i>Compliance Tracking</a></li>
                            <li><a href="{{ route('deadline.hearing.alerts') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-alarm mr-2"></i>Deadline & Hearing Alerts</a></li>
                        </ul>
                    </li>
                    <li class="has-dropdown">
                        <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" aria-expanded="false" role="button">
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
                    Need Legal Assistance?<br />
                    Contact support team at<br />
                    <a href="mailto:legal-support@example.com" class="text-blue-600 hover:underline">legal-support@example.com</a>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="flex-1 p-6 w-full mt-16">
            <div class="dashboard-container max-w-7xl mx-auto">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h2 class="text-[#1a4d38] font-bold text-2xl">Case Management</h2>
                            <p class="text-gray-600 text-sm mt-1">Manage all legal cases, track progress, and monitor deadlines in one place.</p>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-print mr-2"></i> Print
                            </button>
                            <button id="newCaseBtn" onclick="openNewCaseModal()" class="bg-[#2f855A] hover:bg-[#28644c] text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-plus mr-2"></i> New Case
                            </button>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Active Cases -->
                        <div class="dashboard-card p-6 lg:col-span-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Active Cases</p>
                                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['active_cases'] ?? 0 }}</h3>
                                </div>
                                <div class="p-3 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-gavel text-xl"></i>
                                </div>
                            </div>
                            @php $totalCases = is_countable($cases ?? []) ? count($cases ?? []) : 0; @endphp
                            <div class="mt-4 flex items-center text-sm">
                                <span class="text-green-600 font-medium flex items-center">
                                    <i class="fas fa-arrow-up mr-1"></i> 12%
                                </span>
                                <span class="text-gray-500 ml-2">vs last month</span>
                            </div>
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $active = (int) ($stats['active_cases'] ?? 0);
                                        $pct = $totalCases > 0 ? min(100, round(($active / $totalCases) * 100)) : 0;
                                    @endphp
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $active }} of {{ $totalCases }} cases active ({{ $pct }}%)</p>
                            </div>
                        </div>
                        <!-- Upcoming Hearings -->
                        <div class="dashboard-card p-6 lg:col-span-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Upcoming Hearings</p>
                                    <h3 id="upcomingStatCount" class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['upcoming_hearings'] ?? 0 }}</h3>
                                </div>
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-calendar-day text-xl"></i>
                                </div>
                            </div>
                            <div id="nextHearingInfo" class="mt-4">
                                @php $nh = $stats['next_hearing'] ?? null; @endphp
                                @if($nh)
                                    <p class="text-sm text-gray-600">Next: <span class="font-medium">{{ $nh['name'] }}</span> ({{ $nh['date'] }}{{ !empty($nh['time']) ? ' • '.$nh['time'] : '' }})</p>
                                @else
                                    <p class="text-sm text-gray-600">No upcoming hearings</p>
                                @endif
                            </div>
                            <div class="mt-4">
                                @php $upc = (int) ($stats['upcoming_hearings'] ?? 0); @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php $upPct = ($totalCases > 0) ? min(100, round(($upc / $totalCases) * 100)) : 0; @endphp
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $upPct }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $upc }} upcoming across {{ $totalCases }} cases ({{ $upPct }}%)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Search and Filter -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full pl-10 p-2.5" placeholder="Search cases...">
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <select id="filterStatus" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full p-2.5">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="closed">Closed</option>
                                <option value="appeal">On Appeal</option>
                            </select>
                            <select id="filterType" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] block w-full p-2.5">
                                <option value="">All Types</option>
                                <option value="civil">Civil</option>
                                <option value="criminal">Criminal</option>
                                <option value="family">Family Law</option>
                                <option value="corporate">Corporate</option>
                                <option value="ip">Intellectual Property</option>
                            </select>
                            <button class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                <i class="fas fa-filter mr-2"></i> Filter
                            </button>
                        </div>
                    </div>

                    <!-- Upcoming Hearings (connected) -->
                    <div class="dashboard-card p-6 mt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-semibold text-lg text-[#1a4d38]"><i class="fas fa-calendar-day mr-2"></i>Upcoming Hearings</h3>
                            <span id="upcomingTotal" class="text-xs text-gray-500">{{ isset($stats['upcoming_hearings']) ? $stats['upcoming_hearings'] : 0 }} total</span>
                        </div>
                        <ul id="upcomingList" class="divide-y divide-gray-200">
                            @forelse(($upcoming ?? []) as $u)
                                <li class="py-3 flex items-center justify-between">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $u['name'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $u['number'] }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-900">{{ $u['date'] ?? '-' }}</div>
                                        @if(!empty($u['time']))
                                            <div class="text-xs text-gray-500">{{ $u['time'] }}</div>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="py-6 text-center text-sm text-gray-500">No upcoming hearings</li>
                            @endforelse
                        </ul>
                    </div>

                    <!-- Cases Table -->
                    <div class="dashboard-card overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Number</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Hearing</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="casesTbody" class="bg-white divide-y divide-gray-200">
                                    @if(!empty($cases))
                                        @foreach($cases as $c)
                                            @php $typeKey = strtolower($c['type_badge'] ?? 'civil'); @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $c['number'] }}</div>
                                                    <div class="text-xs text-gray-500">Filed: {{ $c['filed'] }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $c['name'] }}</div>
                                                    <div class="text-xs text-gray-500">{{ $c['type_label'] }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-medium text-sm mr-2">{{ $c['client_initials'] ?? '--' }}</div>
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ $c['client'] }}</div>
                                                            <div class="text-xs text-gray-500">{{ $c['client_org'] ?? '' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ $c['type_badge'] }}</span></td>
                                                <td class="px-6 py-4 whitespace-nowrap"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ $c['status'] }}</span></td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $c['hearing_date'] ?? '-' }}</div>
                                                    <div class="text-xs text-gray-500">{{ $c['hearing_time'] ?? '' }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="#" class="viewCaseBtn text-[#2f855A] hover:text-[#1a4d38] mr-3"
                                                       title="View Details"
                                                       data-number="{{ $c['number'] }}"
                                                       data-name="{{ $c['name'] }}"
                                                       data-client="{{ $c['client'] }}"
                                                       data-type="{{ $typeKey }}"
                                                       data-type-label="{{ $c['type_label'] }}"
                                                       data-status="{{ $c['status'] }}"
                                                       data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                                       data-hearing-time="{{ $c['hearing_time'] ?? '' }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="#" class="editCaseBtn text-blue-600 hover:text-blue-800 mr-3"
                                                       title="Edit"
                                                       data-number="{{ $c['number'] }}"
                                                       data-name="{{ $c['name'] }}"
                                                       data-client="{{ $c['client'] }}"
                                                       data-type="{{ $typeKey }}"
                                                       data-status="{{ $c['status'] }}"
                                                       data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                                       data-hearing-time="{{ $c['hearing_time'] ?? '' }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="deleteCaseBtn text-gray-600 hover:text-gray-900" title="More options"
                                                       data-number="{{ $c['number'] }}">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="px-6 py-6 text-center text-sm text-gray-500">No cases found.</td>
                                        </tr>
                                    @endif
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
                                    <p class="text-sm text-gray-700">Showing <span class="font-medium">0</span> to <span class="font-medium">0</span> of <span class="font-medium">0</span> results</p>
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
                                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                            ...
                                        </span>
                                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            8
                                        </a>
                                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <i class="fas fa-chevron-right h-5 w-5"></i>
                                        </a>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="profile-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="profile-modal-title" class="font-semibold text-sm text-gray-900 select-none">My Profile</h3>
                <button id="closeProfileBtn" onclick="closeProfileModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
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
                        <button id="closeProfileBtn2" onclick="closeProfileModal()" type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
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
                <button id="closeAccountSettingsBtn" onclick="closeAccountSettingsModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
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
                        <button type="button" id="cancelAccountSettingsBtn" onclick="closeAccountSettingsModal()" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
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
                <button id="closePrivacySecurityBtn" onclick="closePrivacySecurityModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8">
                <form action="{{ route('profile.update') }}" method="POST">
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
                        <button class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200" id="cancelPrivacySecurityBtn" onclick="closePrivacySecurityModal()" type="button">Cancel</button>
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
                <button id="cancelSignOutBtn" onclick="closeSignOutModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8">
                <div class="mx-auto mb-4 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
                </div>
                <p class="text-xs text-gray-600 mb-6">Are you sure you want to sign out of your account?</p>
                <div class="flex justify-center space-x-4">
                    <button id="cancelSignOutBtn2" onclick="closeSignOutModal()" class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Sign Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- New Case Modal -->
    <div id="newCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="new-case-title">
        <div class="bg-white rounded-lg w-full max-w-3xl mx-4">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="new-case-title" class="text-lg font-semibold text-gray-900">Create New Case</h3>
                <button type="button" onclick="closeModal('newCaseModal')" class="text-gray-400 hover:text-gray-600" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <form id="newCaseForm" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="caseTitle" class="block text-sm font-medium text-gray-700">Case Title *</label>
                            <input type="text" name="case_name" id="caseTitle" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>
                        <div>
                            <label for="caseType" class="block text-sm font-medium text-gray-700">Case Type *</label>
                            <select id="caseType" name="case_type" required class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                                <option value="">Select case type</option>
                                <option value="civil">Civil</option>
                                <option value="criminal">Criminal</option>
                                <option value="family">Family Law</option>
                                <option value="corporate">Corporate</option>
                                <option value="labor">Labor</option>
                            </select>
                        </div>
                        <div>
                            <label for="caseNumber" class="block text-sm font-medium text-gray-700">Case Number</label>
                            <input type="text" name="case_number" id="caseNumber" readonly class="mt-1 block w-full border border-gray-300 bg-gray-100 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm cursor-not-allowed">
                        </div>
                        <div>
                            <label for="filingDate" class="block text-sm font-medium text-gray-700">Filing Date *</label>
                            <input type="date" name="filing_date" id="filingDate" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select id="status" name="status" required class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                                <option value="active">Active</option>
                                <option value="pending">Pending</option>
                                <option value="closed">Closed</option>
                                <option value="on_hold">On Hold</option>
                            </select>
                        </div>
                        <div>
                            <label for="client" class="block text-sm font-medium text-gray-700">Client *</label>
                            <select id="client" name="client_id" required class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                                <option value="">Select client</option>
                                @foreach($clients ?? [] as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="court" class="block text-sm font-medium text-gray-700">Court</label>
                            <input type="text" id="court" name="court" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>
                        <div>
                            <label for="judge" class="block text-sm font-medium text-gray-700">Judge</label>
                            <input type="text" id="judge" name="judge" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea id="description" name="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm"></textarea>
                        </div>
                    </div>
                    <div class="mt-5 flex justify-end gap-3">
                        <button type="button" onclick="closeModal('newCaseModal')" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                        <button type="button" onclick="submitNewCase()" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c]">Create Case</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div id="profileModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="profile-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="profile-modal-title" class="font-semibold text-sm text-gray-900 select-none">My Profile</h3>
                <button id="closeProfileBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="flex flex-col items-center mb-6">
                    <div class="relative mb-4">
                        <div class="h-24 w-24 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <i class="fas fa-user text-4xl text-gray-400"></i>
                        </div>
                        <button class="absolute bottom-0 right-0 bg-white rounded-full p-1.5 shadow-md border border-gray-200 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                            <i class="fas fa-camera text-gray-600 text-xs"></i>
                        </button>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900">{{ $user->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" value="{{ $user->name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" value="{{ $user->email }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" value="+1 (555) 123-4567" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Department</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm bg-white">
                            <option>Legal</option>
                            <option>Administrative</option>
                            <option>Finance</option>
                            <option>Human Resources</option>
                            <option>IT</option>
                            <option>Operations</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button id="closeProfileBtn2" type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Cancel
                    </button>
                    <button type="button" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Settings Modal -->
    <div id="accountSettingsModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="account-settings-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="account-settings-modal-title" class="font-semibold text-sm text-gray-900 select-none">Account Settings</h3>
                <button id="closeAccountSettingsBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Change Password</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Current Password</label>
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">New Password</label>
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Confirm New Password</label>
                                <input type="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Two-Factor Authentication</h4>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Status: <span class="text-green-600">Active</span></p>
                                <p class="text-xs text-gray-500">Requires verification code at login</p>
                            </div>
                            <button class="text-sm text-[#2f855A] hover:text-[#1a4d38] font-medium">Manage</button>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Login Activity</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-medium">Current Session</p>
                                    <p class="text-xs text-gray-500">Chrome on Windows • Just now</p>
                                </div>
                                <button class="text-red-600 hover:text-red-800 text-xs font-medium">Sign out</button>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div>
                                    <p class="font-medium">Previous Session</p>
                                    <p class="text-xs text-gray-500">Safari on iPhone • 2 hours ago</p>
                                </div>
                                <button class="text-red-600 hover:text-red-800 text-xs font-medium">Report</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button id="cancelAccountSettingsBtn" type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Cancel
                    </button>
                    <button type="button" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy & Security Modal -->
    <div id="privacySecurityModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="privacy-security-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="privacy-security-modal-title" class="font-semibold text-sm text-gray-900 select-none">Privacy & Security</h3>
                <button id="closePrivacySecurityBtn" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Data Privacy</h4>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="dataCollection" type="checkbox" class="h-4 w-4 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" checked>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="dataCollection" class="font-medium text-gray-700">Allow data collection for analytics</label>
                                    <p class="text-xs text-gray-500">Help us improve our services by sharing usage data</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="personalizedAds" type="checkbox" class="h-4 w-4 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="personalizedAds" class="font-medium text-gray-700">Personalized advertising</label>
                                    <p class="text-xs text-gray-500">Show me relevant content and ads based on my activity</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Security</h4>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="loginAlerts" type="checkbox" class="h-4 w-4 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" checked>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="loginAlerts" class="font-medium text-gray-700">Login alerts</label>
                                    <p class="text-xs text-gray-500">Get notified when someone logs into your account from a new device</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="backupCodes" type="checkbox" class="h-4 w-4 text-[#2f855A] focus:ring-[#2f855A] border-gray-300 rounded" checked>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="backupCodes" class="font-medium text-gray-700">Backup codes</label>
                                    <p class="text-xs text-gray-500">Generate backup codes for two-factor authentication</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Data & Privacy</h4>
                        <div class="space-y-2">
                            <button class="w-full text-left text-sm text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg flex items-center justify-between">
                                <span>Download your data</span>
                                <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                            </button>
                            <button class="w-full text-left text-sm text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg flex items-center justify-between">
                                <span>Request data deletion</span>
                                <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                            </button>
                            <button class="w-full text-left text-sm text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg flex items-center justify-between">
                                <span>Delete account</span>
                                <i class="fas fa-chevron-right text-xs text-red-400"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button id="cancelPrivacySecurityBtn" type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Cancel
                    </button>
                    <button type="button" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sign Out Modal -->
    <div id="signOutModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="sign-out-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[360px] max-w-full mx-4 text-center" role="document">
            <div class="p-6">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
                </div>
                <h3 id="sign-out-modal-title" class="text-lg font-medium text-gray-900 mb-2">Sign out</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to sign out? You'll need to sign in again to access your account.</p>
                <div class="flex justify-center space-x-4">
                    <button id="cancelSignOutBtn" type="button" class="px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A]">
                        Cancel
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
    // Sidebar elements
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    const toggleBtn = document.getElementById("toggle-btn");
    const overlay = document.getElementById("overlay");
    const dropdownToggles = document.querySelectorAll(".has-dropdown > div");

    // Notification and user menu dropdowns
    const notificationBtn = document.getElementById("notificationBtn");
    const notificationDropdown = document.getElementById("notificationDropdown");
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userMenuDropdown = document.getElementById("userMenuDropdown");

    // Modal elements
    const profileModal = document.getElementById("profileModal");
    const openProfileBtn = document.getElementById("openProfileBtn");
    const closeProfileBtn = document.getElementById("closeProfileBtn");
    const closeProfileBtn2 = document.getElementById("closeProfileBtn2");

    const accountSettingsModal = document.getElementById("accountSettingsModal");
    const openAccountSettingsBtn = document.getElementById("openAccountSettingsBtn");
    const closeAccountSettingsBtn = document.getElementById("closeAccountSettingsBtn");
    const cancelAccountSettingsBtn = document.getElementById("cancelAccountSettingsBtn");

    const privacySecurityModal = document.getElementById("privacySecurityModal");
    const openPrivacySecurityBtn = document.getElementById("openPrivacySecurityBtn");
    const closePrivacySecurityBtn = document.getElementById("closePrivacySecurityBtn");
    const cancelPrivacySecurityBtn = document.getElementById("cancelPrivacySecurityBtn");

    const signOutModal = document.getElementById("signOutModal");
    const signOutBtn = document.getElementById("signOutBtn");
    const cancelSignOutBtn = document.getElementById("cancelSignOutBtn");

    // Add Case Modal
    const addCaseModal = document.getElementById("addCaseModal");
    const openAddCaseBtn = document.querySelector("button:has(i.fa-plus)");
    const closeAddCaseModal = document.getElementById("closeAddCaseModal");
    const cancelAddCase = document.getElementById("cancelAddCase");
    const addCaseForm = document.getElementById("addCaseForm");

    // Toggle sidebar
    function toggleSidebar() {
        const isOpen = sidebar.classList.contains("md:ml-0") || sidebar.classList.contains("ml-0");
        
        if (window.innerWidth >= 768) {
            // Desktop behavior
            sidebar.classList.toggle("-ml-72");
            sidebar.classList.toggle("md:ml-0");
            mainContent.classList.toggle("sidebar-closed");
        } else {
            // Mobile behavior
            sidebar.classList.toggle("-ml-72");
            sidebar.classList.toggle("ml-0");
            overlay.classList.toggle("hidden");
            document.body.classList.toggle("overflow-hidden");
        }

        // Update toggle button icon and ARIA
        const icon = toggleBtn.querySelector("i");
        icon.classList.toggle("fa-bars", !isOpen);
        icon.classList.toggle("fa-times", isOpen);
        toggleBtn.setAttribute("aria-expanded", !isOpen);
    }

    // Close all dropdowns
    function closeAllDropdowns(exceptToggle = null) {
        dropdownToggles.forEach((toggle) => {
            if (toggle !== exceptToggle) {
                const dropdown = toggle.nextElementSibling;
                const chevron = toggle.querySelector(".bx-chevron-down");
                dropdown.classList.add("hidden");
                chevron.classList.remove("rotate-180");
                toggle.setAttribute("aria-expanded", "false");
            }
        });
    }

    // Handle sidebar dropdown toggles
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", (e) => {
            e.stopPropagation();
            const dropdown = toggle.nextElementSibling;
            const chevron = toggle.querySelector(".bx-chevron-down");
            const isOpen = !dropdown.classList.contains("hidden");

            // Close all other dropdowns
            closeAllDropdowns(toggle);

            // Toggle the clicked dropdown
            dropdown.classList.toggle("hidden");
            chevron.classList.toggle("rotate-180");
            toggle.setAttribute("aria-expanded", !isOpen);
        });
    });

    // Handle sidebar toggle button
    if (toggleBtn) {
        toggleBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Close sidebar and dropdowns when clicking overlay
    if (overlay) {
        overlay.addEventListener("click", () => {
            sidebar.classList.add("-ml-72");
            sidebar.classList.remove("ml-0", "md:ml-0");
            overlay.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");
            mainContent.classList.add("sidebar-closed");
            closeAllDropdowns();

            // Reset toggle button
            const icon = toggleBtn.querySelector("i");
            icon.classList.add("fa-bars");
            icon.classList.remove("fa-times");
            toggleBtn.setAttribute("aria-expanded", "false");
        });
    }

    // Handle window resize
    function handleResize() {
        if (window.innerWidth >= 768) {
            // Desktop: Show sidebar by default
            sidebar.classList.remove("-ml-72");
            sidebar.classList.add("md:ml-0");
            mainContent.classList.remove("sidebar-closed");
            overlay.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");
            const icon = toggleBtn?.querySelector("i");
            if (icon) {
                icon.classList.add("fa-bars");
                icon.classList.remove("fa-times");
                toggleBtn.setAttribute("aria-expanded", "true");
            }
        } else {
            // Mobile: Hide sidebar by default
            sidebar.classList.add("-ml-72");
            sidebar.classList.remove("md:ml-0", "ml-0");
            mainContent.classList.add("sidebar-closed");
            overlay.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");
            const icon = toggleBtn?.querySelector("i");
            if (icon) {
                icon.classList.add("fa-bars");
                icon.classList.remove("fa-times");
                toggleBtn.setAttribute("aria-expanded", "false");
            }
        }
        closeAllDropdowns();
    }

    // Initialize resize handler
    window.addEventListener("resize", handleResize);
    handleResize();

    // Close dropdowns and sidebar when clicking outside
    document.addEventListener("click", (e) => {
        if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && !sidebar.classList.contains("-ml-72")) {
            if (window.innerWidth < 768) {
                sidebar.classList.add("-ml-72");
                sidebar.classList.remove("ml-0");
                overlay.classList.add("hidden");
                document.body.classList.remove("overflow-hidden");
                const icon = toggleBtn.querySelector("i");
                icon.classList.add("fa-bars");
                icon.classList.remove("fa-times");
                toggleBtn.setAttribute("aria-expanded", "false");
            }
            closeAllDropdowns();
        }
    });

    // Notification dropdown toggle
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            notificationDropdown.classList.toggle("hidden");
            notificationBtn.setAttribute("aria-expanded", !notificationDropdown.classList.contains("hidden"));
            userMenuDropdown.classList.add("hidden");
            userMenuBtn.setAttribute("aria-expanded", "false");
        });
    }

    // User menu dropdown toggle
    if (userMenuBtn && userMenuDropdown) {
        userMenuBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            userMenuDropdown.classList.toggle("hidden");
            userMenuBtn.setAttribute("aria-expanded", !userMenuDropdown.classList.contains("hidden"));
            notificationDropdown.classList.add("hidden");
            notificationBtn.setAttribute("aria-expanded", "false");
        });
    }

    // Close dropdowns when clicking outside
    document.addEventListener("click", (e) => {
        if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add("hidden");
            notificationBtn.setAttribute("aria-expanded", "false");
        }
        if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
            userMenuDropdown.classList.add("hidden");
            userMenuBtn.setAttribute("aria-expanded", "false");
        }
    });

    // Modal handling
    function closeAllModals() {
        const modals = [addCaseModal, profileModal, accountSettingsModal, privacySecurityModal, signOutModal];
        modals.forEach((modal) => {
            if (modal) modal.classList.add("hidden");
        });
    }

    // Add Case Modal
    if (openAddCaseBtn) {
        openAddCaseBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            closeAllModals();
            addCaseModal.classList.remove("hidden");
        });
    }

    // Open New Case Modal
    function openNewCaseModal() {
        // Generate case number (format: C-YYYY-XXXX)
        const now = new Date();
        const randomNum = Math.floor(1000 + Math.random() * 9000);
        document.getElementById('caseNumber').value = `C-${now.getFullYear()}-${randomNum}`;
        
        // Set today's date as default filing date
        document.getElementById('filingDate').valueAsDate = new Date();
        
        // Show modal
        document.getElementById('newCaseModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    // Close modal function
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Reset the form when closing
        if (modalId === 'newCaseModal') {
            document.getElementById('newCaseForm').reset();
        }
    }

    // Handle New Case button click
    const newCaseBtn = document.getElementById('newCaseBtn');
    if (newCaseBtn) {
        newCaseBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openNewCaseModal();
        });
    }
    
    // Submit New Case Form
    async function submitNewCase() {
        const form = document.getElementById('newCaseForm');
        const submitBtn = form ? form.querySelector('button[type="button"]') : null;
        const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
        
        if (!form) return;
        
        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
        }
        
        try {
            const formData = new FormData(form);
            const response = await fetch('{{ route("case.create") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'Failed to create case');
            }
            
            // Show success message
            await Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Case has been created successfully.',
                showConfirmButton: false,
                timer: 1500
            });
            
            // Reset form and close modal
            form.reset();
            closeModal('newCaseModal');
            
            // Reload the page to show the new case
            window.location.reload();
            
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to create case. Please try again.',
                confirmButtonColor: '#2f855a'
            });
        } finally {
            // Reset button state
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        }
    }
    
    // Close modal when clicking outside or pressing Escape
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            closeModal('newCaseModal');
        }
    });
    
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal('newCaseModal');
        }
    });
    
    // Handle form submission
    const addCaseForm = document.getElementById('addCaseForm');
    if (addCaseForm) {
        addCaseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Here you would typically send the form data to your backend
            // For now, we'll just show a success message and close the modal
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'New case has been created successfully!',
                showConfirmButton: false,
                timer: 2000
            });
            
            // Close the modal
            closeModal('addCaseModal');
            
            // Reset the form
            this.reset();
            
            // In a real application, you would refresh the cases list or add the new case to the table
            // window.location.reload(); // Uncomment to refresh the page
        });
    }

    if (cancelAddCase) {
        cancelAddCase.addEventListener("click", () => {
            closeAllModals();
        });
    }

    if (addCaseForm) {
        addCaseForm.addEventListener("submit", (e) => {
            e.preventDefault();
            closeAllModals();
            Swal.fire({
                title: "Success!",
                text: "Case added successfully.",
                icon: "success",
                confirmButtonColor: "#2f855A",
                confirmButtonText: "OK",
            });
        });
    }

    // Profile Modal
    if (openProfileBtn) {
        openProfileBtn.addEventListener("click", () => {
            closeAllModals();
            profileModal.classList.remove("hidden");
            userMenuDropdown.classList.add("hidden");
            userMenuBtn.setAttribute("aria-expanded", "false");
        });
    }

    if (closeProfileBtn) {
        closeProfileBtn.addEventListener("click", () => {
            closeAllModals();
        });
    }

    if (closeProfileBtn2) {
        closeProfileBtn2.addEventListener("click", () => {
            closeAllModals();
        });
    }

    // Account Settings Modal
    if (openAccountSettingsBtn) {
        openAccountSettingsBtn.addEventListener("click", () => {
            closeAllModals();
            accountSettingsModal.classList.remove("hidden");
            userMenuDropdown.classList.add("hidden");
            userMenuBtn.setAttribute("aria-expanded", "false");
        });
    }

    if (closeAccountSettingsBtn) {
        closeAccountSettingsBtn.addEventListener("click", () => {
            closeAllModals();
        });
    }

    if (cancelAccountSettingsBtn) {
        cancelAccountSettingsBtn.addEventListener("click", () => {
            closeAllModals();
        });
    }

    // Privacy & Security Modal
    if (openPrivacySecurityBtn) {
        openPrivacySecurityBtn.addEventListener("click", () => {
            closeAllModals();
            privacySecurityModal.classList.remove("hidden");
            userMenuDropdown.classList.add("hidden");
            userMenuBtn.setAttribute("aria-expanded", "false");
        });
    }

    if (closePrivacySecurityBtn) {
        closePrivacySecurityBtn.addEventListener("click", () => {
            closeAllModals();
        });
    }

    if (cancelPrivacySecurityBtn) {
        cancelPrivacySecurityBtn.addEventListener("click", () => {
            closeAllModals();
        });
    }

    // Sign Out Modal
    if (signOutBtn) {
        signOutBtn.addEventListener("click", () => {
            closeAllModals();
            signOutModal.classList.remove("hidden");
            userMenuDropdown.classList.add("hidden");
            userMenuBtn.setAttribute("aria-expanded", "false");
        });
    }

    if (cancelSignOutBtn) {
        cancelSignOutBtn.addEventListener("click", () => {
            closeAllModals();
        });
    }

    // Close modals when clicking outside
    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("modal")) {
            closeAllModals();
        }
    });
});
</script>
    

    <!-- View Case Modal -->
    <div id="viewCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-case-title">
        <div class="bg-white rounded-lg w-full max-w-xl mx-4">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="view-case-title" class="text-lg font-semibold text-gray-900">Case Details</h3>
                <button id="closeViewCaseBtn" class="text-gray-400 hover:text-gray-600" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4 text-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-500">Case Number</p>
                        <p id="vcNumber" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p id="vcStatus" class="font-medium text-gray-900">—</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Case Name</p>
                        <p id="vcName" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Client</p>
                        <p id="vcClient" class="font-medium text-gray-900">—</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Type</p>
                        <p id="vcType" class="font-medium text-gray-900">—</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Next Hearing</p>
                        <p id="vcHearing" class="font-medium text-gray-900">—</p>
                    </div>
                </div>
                <div class="flex justify-end pt-2">
                    <button id="closeViewCaseBtn2" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c]">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Case Modal -->
    <div id="editCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-case-title">
        <div class="bg-white rounded-lg w-full max-w-xl mx-4">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="edit-case-title" class="text-lg font-semibold text-gray-900">Edit Case</h3>
                <button id="closeEditCaseBtn" class="text-gray-400 hover:text-gray-600" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="editCaseForm" class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecNumber">Case Number</label>
                        <input id="ecNumber" name="number" type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" readonly />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecStatus">Status</label>
                        <select id="ecStatus" name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                            <option value="In Progress">In Progress</option>
                            <option value="Active">Active</option>
                            <option value="Pending">Pending</option>
                            <option value="Closed">Closed</option>
                            <option value="appeal">On Appeal</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-600 mb-1" for="ecName">Case Name</label>
                        <input id="ecName" name="name" type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecClient">Client</label>
                        <input id="ecClient" name="client" type="text" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecType">Type</label>
                        <select id="ecType" name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                            <option value="civil">Civil</option>
                            <option value="criminal">Criminal</option>
                            <option value="family">Family</option>
                            <option value="corporate">Corporate</option>
                            <option value="ip">IP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecHearingDate">Next Hearing Date</label>
                        <input id="ecHearingDate" name="hearing_date" type="date" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" />
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecHearingTime">Next Hearing Time</label>
                        <input id="ecHearingTime" name="hearing_time" type="time" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" id="cancelEditCaseBtn" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c]">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Case Modal -->
    <div id="deleteCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-case-title">
        <div class="bg-white rounded-lg w-full max-w-sm mx-4">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="delete-case-title" class="text-lg font-semibold text-gray-900">Delete Case</h3>
                <button id="closeDeleteCaseBtn" class="text-gray-400 hover:text-gray-600" aria-label="Close">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 text-sm text-gray-700">
                <p>Are you sure you want to delete case <span class="font-semibold" id="delCaseNumberText">—</span>? This action cannot be undone.</p>
            </div>
            <div class="px-6 pb-6 flex justify-end gap-3">
                <button id="cancelDeleteCaseBtn" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                <button id="confirmDeleteCaseBtn" class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">Delete</button>
            </div>
        </div>
    </div>

</body>
</html>
