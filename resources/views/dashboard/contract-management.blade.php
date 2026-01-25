@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contract Management</title>
    <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        /* Match New Case overlay style for Add Contract modal */
        #addContractModal {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
        /* Match overlay style for View Contract modal as well */
        #viewContractModal {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
        /* Match overlay style for Edit Contract modal */
        #editContractModal {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
        /* Match overlay style for Delete Contract modal */
        #deleteContractModal {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
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
                <button id="toggle-btn" class="pl-2 focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Contract Management</h1>
            </div>
            <div class="flex items-center space-x-1">
                <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn">
                    <i class="fa-solid fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 bg-red-500 text-xs text-white rounded-full px-1">3</span>
                </button>
                <div class="flex items-center space-x-2 cursor-pointer px-3 py-2 transition duration-200" id="userMenuBtn" aria-label="User menu" aria-haspopup="true" aria-expanded="false">
                    <i class="fa-solid fa-user text-[18px] bg-white text-[#28644c] px-2.5 py-2 rounded-full"></i>
                    <span class="text-white font-medium">{{ auth()->user()->name }}</span>
                    <i class="fa-solid fa-chevron-down text-sm"></i>
                </div>
            </div>
        </div>
    </nav>
    <script>
      (function(){
        if (typeof window.openCaseWithConfGate !== 'function'){
          window.openCaseWithConfGate = function(href){
            if (href){ window.location.href = href; }
            return false;
          };
        }
      })();
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
      if (!window.__contractMenusBound) {
        window.__contractMenusBound = true;
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
    <script>
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
    </script>

    <!-- User Menu Dropdown (moved outside main content to avoid clipping) -->
    <div id="userMenuDropdown" class="hidden absolute right-4 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-50" style="top: 4rem;" role="menu" aria-labelledby="userMenuBtn">
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
            <li><button id="openSignOutBtn" onclick="(function(e){ e&&e.stopPropagation&&e.stopPropagation(); openSignOutModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</button></li>
        </ul>
    </div>

    <!-- Profile Modal (moved) -->
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

    <!-- Account Settings Modal (moved) -->
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

    <!-- Privacy & Security Modal (moved) -->
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

    <!-- Sign Out Modal (moved) -->
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
                    <p class="text-gray-600 leading-tight text-xs">New employee added: {{ auth()->user()->name }}</p>
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
                        <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-calendar-check"></i>
                                <span>Facilities Management</span>
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
                    <!-- Page Header -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                        <div>
                            <h1 class="text-2xl font-bold text-[#1a4d38]">Contract Management</h1>
                            <p class="text-gray-600 text-sm">Manage and track all legal contracts in one place</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button id="addContractBtn" class="px-4 py-2 bg-[#2f855A] text-white rounded-lg hover:bg-[#28644c] transition-colors duration-200 flex items-center text-sm font-semibold shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                <i class="fas fa-plus mr-2"></i> Add New Contract
                            </button>
                        </div>
                    </div>
                    <!-- Stats Cards -->
                    <!-- Stats Cards -->
                    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Total Contracts -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Contracts</p>
                                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total'] ?? 0 }}</p>
                                </div>
                                <div class="p-3 rounded-full bg-blue-50 text-blue-600">
                                    <i class="fas fa-file-contract text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Contracts -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Active</p>
                                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['active'] ?? 0 }}</p>
                                </div>
                                <div class="p-3 rounded-full bg-green-50 text-green-600">
                                    <i class="fas fa-check-circle text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php
                                    $activePercent = $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100) : 0;
                                @endphp
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full" style="width: {{ $activePercent }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ $activePercent }}% of total</p>
                            </div>
                        </div>

                        <!-- Pending Review -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Pending Review</p>
                                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] ?? 0 }}</p>
                                </div>
                                <div class="p-3 rounded-full bg-yellow-50 text-yellow-600">
                                    <i class="fas fa-clock text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php
                                    $pendingPercent = $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100) : 0;
                                @endphp
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-yellow-500 rounded-full" style="width: {{ $pendingPercent }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ $pendingPercent }}% of total</p>
                            </div>
                        </div>

                        <!-- Expiring Soon -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Expiring Soon</p>
                                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['expiring'] ?? 0 }}</p>
                                </div>
                                <div class="p-3 rounded-full bg-red-50 text-red-600">
                                    <i class="fas fa-exclamation-triangle text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php
                                    $expiringPercent = $stats['total'] > 0 ? round(($stats['expiring'] / $stats['total']) * 100) : 0;
                                @endphp
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-red-500 rounded-full" style="width: {{ $expiringPercent }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right">{{ $expiringPercent }}% of total</p>
                            </div>
                        </div>
                    </section>

                    <!-- Search and Filter -->
                    <section class="bg-gradient-to-br from-white to-gray-50 rounded-lg p-6 shadow-sm border border-gray-100">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="relative flex-1 max-w-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A] text-sm" placeholder="Search contracts...">
                            </div>
                            <div class="flex space-x-3">
                                <select id="filterStatus" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                    <option value="expired">Expired</option>
                                </select>
                                <select id="filterType" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#2f855A] focus:border-[#2f855A]">
                                    <option value="">All Types</option>
                                    <option value="nda">NDA</option>
                                    <option value="service">Service</option>
                                    <option value="employment">Employment</option>
                                    <option value="employee">Employee</option>
                                    <option value="consultancy">Consultancy</option>
                                    <option value="internship">Internship</option>
                                    <option value="probation">Probation</option>
                                    <option value="vendor">Vendor</option>
                                    <option value="supplier">Supplier</option>
                                    <option value="lease">Lease</option>
                                    <option value="license">License</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="purchase">Purchase</option>
                                    <option value="sales">Sales</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="loan">Loan</option>
                                </select>
                                <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                                    <i class="fas fa-filter text-gray-600 mr-2"></i>
                                    <span>Filter</span>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Lock Button Section -->
                    <div class="flex justify-end mb-4">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 w-full">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-lock text-gray-500"></i>
                                    <span class="text-sm font-medium text-gray-700">Secure Access</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contracts Table -->
                    <section class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-base font-semibold text-gray-900">Contract Management</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse(($contracts ?? []) as $c)
                                            @php
                                                // Initialize status value with default 'draft' if not set
                                                $statusValue = strtolower($c->contract_status ?? $c->status ?? 'draft');
                                                
                                                // Set default dates if not set
                                                $startDate = $c->contract_start_date ?? $c->start_date ?? null;
                                                $endDate = $c->contract_end_date ?? $c->end_date ?? $c->expires_on ?? $c->contract_expiration ?? null;
                                                
                                                // Convert string dates to Carbon instances if they exist
                                                $startDate = $startDate ? \Carbon\Carbon::parse($startDate) : null;
                                                $endDate = $endDate ? \Carbon\Carbon::parse($endDate) : null;

                                                $startDateDisplay = (!empty($c->formatted_start_date) && $c->formatted_start_date !== 'N/A')
                                                    ? $c->formatted_start_date
                                                    : ($startDate ? $startDate->format('M d, Y') : null);
                                                $endDateDisplay = (!empty($c->formatted_end_date) && $c->formatted_end_date !== 'N/A')
                                                    ? $c->formatted_end_date
                                                    : ($endDate ? $endDate->format('M d, Y') : null);
                                                
                                                // Calculate days remaining and status
                                                $daysRemaining = null;
                                                $isExpired = false;
                                                $isExpiringSoon = false;
                                                
                                                if ($endDate) {
                                                    $now = now();
                                                    $daysRemaining = $now->diffInDays($endDate, false);
                                                    $isExpired = $daysRemaining < 0;
                                                    $isExpiringSoon = $daysRemaining >= 0 && $daysRemaining <= 30;
                                                    
                                                    // Auto-update status based on dates if not explicitly set
                                                    if ($isExpired && $statusValue !== 'terminated') {
                                                        $statusValue = 'expired';
                                                    } elseif ($isExpiringSoon && $statusValue === 'active') {
                                                        $statusValue = 'active';
                                                    }
                                                }
                                                
                                                // Ensure status is one of the expected values
                                                $validStatuses = ['draft', 'active', 'pending', 'expired', 'terminated', 'renewed'];
                                                if (!in_array($statusValue, $validStatuses)) {
                                                    $statusValue = 'draft'; // Default to draft if status is invalid
                                                }
                                                
                                                $statusConfig = [
                                                    'draft' => [
                                                        'bg' => 'bg-gray-100',
                                                        'text' => 'text-gray-800',
                                                        'ring' => 'ring-gray-300',
                                                        'icon' => 'fa-file-lines',
                                                        'label' => 'Draft'
                                                    ],
                                                    'active' => [
                                                        'bg' => 'bg-green-50',
                                                        'text' => 'text-green-800',
                                                        'ring' => 'ring-green-600/20',
                                                        'icon' => 'fa-circle-check',
                                                        'label' => 'Active'
                                                    ],
                                                    'expired' => [
                                                        'bg' => 'bg-red-50',
                                                        'text' => 'text-red-800',
                                                        'ring' => 'ring-red-600/20',
                                                        'icon' => 'fa-clock-rotate-left',
                                                        'label' => 'Expired'
                                                    ],
                                                    'terminated' => [
                                                        'bg' => 'bg-red-50',
                                                        'text' => 'text-red-800',
                                                        'ring' => 'ring-red-600/20',
                                                        'icon' => 'fa-ban',
                                                        'label' => 'Terminated'
                                                    ],
                                                    'renewed' => [
                                                        'bg' => 'bg-blue-50',
                                                        'text' => 'text-blue-800',
                                                        'ring' => 'ring-blue-600/20',
                                                        'icon' => 'fa-rotate',
                                                        'label' => 'Renewed'
                                                    ],
                                                    'pending' => [
                                                        'bg' => 'bg-yellow-50',
                                                        'text' => 'text-yellow-800',
                                                        'ring' => 'ring-yellow-600/20',
                                                        'icon' => 'fa-clock',
                                                        'label' => 'Pending'
                                                    ]
                                                ];

                                                // Get status config or default to draft
                                                $statusInfo = $statusConfig[$statusValue] ?? [
                                                    'bg' => 'bg-gray-50',
                                                    'text' => 'text-gray-700',
                                                    'ring' => 'ring-0',
                                                    'icon' => 'fa-circle-info',
                                                    'label' => ucfirst($statusValue)
                                                ];

                                                $statusClasses = "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$statusInfo['bg']} {$statusInfo['text']} ring-1 ring-inset {$statusInfo['ring']}";
                                                $statusLabel = $statusInfo['label'];
                                                $statusIcon = $statusInfo['icon'];
                                                
                                                // Process dates
                                                // Dates are already processed above
                                            @endphp
                                            <tr class="hover:bg-gray-50 cursor-pointer" data-id="{{ $c->id }}" onclick="toggleContractActions(event, this)">
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ $c->code ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    <div class="font-medium">{{ $c->title ?? 'N/A' }}</div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ ucfirst($c->type ?? 'N/A') }}
                                                </td>
                                                <!-- Status Column -->
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    @php
                                                        // Simple status display
                                                        $status = strtolower($c->status ?? 'draft');
                                                        $statusMap = [
                                                            'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'label' => 'Active'],
                                                            'draft' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-file', 'label' => 'Draft'],
                                                            'expired' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-clock-rotate-left', 'label' => 'Expired'],
                                                            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-clock', 'label' => 'Pending'],
                                                            'terminated' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-ban', 'label' => 'Terminated'],
                                                            'renewed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-rotate', 'label' => 'Renewed']
                                                        ];
                                                        
                                                        $statusInfo = $statusMap[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-question-circle', 'label' => ucfirst($status)];
                                                    @endphp
                                                    
                                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }}">
                                                        <i class="fas {{ $statusInfo['icon'] }} mr-1.5"></i>
                                                        {{ $statusInfo['label'] }}
                                                    </div>
                                                        @if($isExpired && $statusValue !== 'expired')
                                                            <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800 border border-red-200">
                                                                <i class="fas fa-exclamation-triangle mr-1"></i>Expired
                                                            </span>
                                                        @elseif($isExpiringSoon && $statusValue === 'active')
                                                            <span class="ml-2 px-2 py-0.5 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                                                <i class="fas fa-clock mr-1"></i>Expiring Soon
                                                            </span>
                                                        @endif
                                                    </div>
                                                        @if($statusValue === 'active' && $endDate)
                                                            <div class="text-xs {{ $isExpiringSoon ? 'text-yellow-600' : 'text-gray-600' }} flex items-center">
                                                                @if($isExpiringSoon)
                                                                    <i class="fas fa-hourglass-half mr-1"></i>
                                                                    <span>{{ $daysRemaining }} day{{ $daysRemaining !== 1 ? 's' : '' }} remaining ({{ $endDate->format('M d, Y') }})</span>
                                                                @else
                                                                    <i class="fas fa-calendar-check mr-1"></i>
                                                                    <span>Active until {{ $endDate->format('M d, Y') }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ $c->company ?? 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    @if($startDateDisplay)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-calendar-day mr-2 text-gray-400"></i>
                                                            <span>{{ $startDateDisplay }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400">Not set</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    @if($endDateDisplay)
                                                        <div class="flex flex-col">
                                                            <div class="flex items-center">
                                                                <i class="fas fa-calendar-check mr-2 text-gray-400"></i>
                                                                <span>{{ $endDateDisplay }}</span>
                                                            </div>
                                                            @if($isExpired)
                                                                <span class="text-xs text-red-600 mt-1">
                                                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                                                    Expired {{ abs($daysRemaining) }} days ago
                                                                </span>
                                                            @elseif($isExpiringSoon)
                                                                <span class="text-xs text-yellow-600 mt-1">
                                                                    <i class="fas fa-clock mr-1"></i>
                                                                    {{ $daysRemaining }} days left
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400">No end date</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex items-center space-x-2">
                                                        <!-- Actions container - hidden by default -->
                                                        <div class="contract-actions hidden absolute right-4 bg-white shadow-lg rounded-md p-2 z-10">
                                                            <button class="viewContractBtn text-blue-600 hover:text-blue-800 mr-2" data-tooltip="View" 
                                                                data-id="{{ $c->id }}" 
                                                                data-code="{{ $c->code }}" 
                                                                data-title="{{ $c->title }}"
                                                                data-type="{{ $c->type }}"
                                                                data-status="{{ $statusValue }}"
                                                                data-start-date="{{ $startDate }}"
                                                                data-end-date="{{ $endDate }}"
                                                                data-value="{{ $c->value ?? '' }}"
                                                                data-notes="{{ $c->notes ?? '' }}"
                                                                data-created-at="{{ is_object($c->created_at) ? $c->created_at->format('M d, Y') : (is_string($c->created_at) ? \Carbon\Carbon::parse($c->created_at)->format('M d, Y') : 'N/A') }}"
                                                                data-updated-at="{{ is_object($c->updated_at) ? $c->updated_at->format('M d, Y') : (is_string($c->updated_at) ? \Carbon\Carbon::parse($c->updated_at)->format('M d, Y') : 'N/A') }}">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="editContractBtn text-yellow-600 hover:text-yellow-800 mr-2" data-tooltip="Edit" 
                                                                data-id="{{ $c->id }}" 
                                                                data-code="{{ $c->code }}" 
                                                                data-title="{{ $c->title }}"
                                                                data-type="{{ $c->type }}"
                                                                data-status="{{ $statusValue }}"
                                                                data-start-date="{{ $startDate }}"
                                                                data-end-date="{{ $endDate }}"
                                                                data-value="{{ $c->value ?? '' }}"
                                                                data-notes="{{ $c->notes ?? '' }}">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="deleteContractBtn text-red-600 hover:text-red-800" data-tooltip="Delete" 
                                                                data-id="{{ $c->id }}" 
                                                                data-title="{{ $c->title }}">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No contracts found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

        </main>
    </div>

    <!-- Contract Modals (moved outside main content for proper overlay coverage) -->
    <!-- View Contract Modal -->
    <div id="viewContractModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-contract-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="view-contract-modal-title" class="font-semibold text-sm text-gray-900 select-none">Contract Details</h3>
                <button id="closeViewContractModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8 text-xs text-gray-700 space-y-2">
                <div><span class="font-semibold">Contract ID:</span> <span id="viewContractId"></span></div>
                <div><span class="font-semibold">Title:</span> <span id="viewContractTitle"></span></div>
                <div><span class="font-semibold">Company:</span> <span id="viewContractCompany"></span></div>
                <div><span class="font-semibold">Type:</span> <span id="viewContractType"></span></div>
                <div><span class="font-semibold">Expiration:</span> <span id="viewContractExpiration"></span></div>
                <div><span class="font-semibold">Status:</span> <span id="viewContractStatus"></span></div>
                <div><span class="font-semibold">Created:</span> <span id="viewContractCreated"></span></div>
                <div class="pt-4 text-right">
                    <button id="closeViewContractModal2" type="button" class="bg-[#28644c] hover:bg-[#2f855A] text-white text-sm font-semibold rounded-lg px-4 py-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A] transition-all duration-200">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Contract Modal -->
    <div id="editContractModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-contract-modal-title">
        <div class="bg-white rounded-lg shadow-lg w-[380px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="edit-contract-modal-title" class="font-semibold text-sm text-gray-900 select-none">Edit Contract</h3>
                <button id="closeEditContractModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8">
                <form id="editContractForm" class="space-y-3 text-xs text-gray-700">
                    <input type="hidden" id="editContractId">
                    <div>
                        <label for="editContractTitle" class="block mb-1 font-semibold">Title</label>
                        <input type="text" id="editContractTitle" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                    </div>
                    <div>
                        <label for="editContractCompany" class="block mb-1 font-semibold">Company</label>
                        <input type="text" id="editContractCompany" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                    </div>
                    <div>
                        <label for="editContractType" class="block mb-1 font-semibold">Type</label>
                        <select id="editContractType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            <option value="nda">NDA</option>
                            <option value="service">Service</option>
                            <option value="employment">Employment</option>
                            <option value="employee">Employee</option>
                            <option value="consultancy">Consultancy</option>
                            <option value="internship">Internship</option>
                            <option value="probation">Probation</option>
                            <option value="vendor">Vendor</option>
                            <option value="supplier">Supplier</option>
                            <option value="lease">Lease</option>
                            <option value="license">License</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="purchase">Purchase</option>
                            <option value="sales">Sales</option>
                            <option value="partnership">Partnership</option>
                            <option value="loan">Loan</option>
                        </select>
                    </div>
                    <div>
                        <label for="editContractExpiration" class="block mb-1 font-semibold">Expiration</label>
                        <input type="date" id="editContractExpiration" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]">
                    </div>
                    <div>
                        <label for="editContractStatus" class="block mb-1 font-semibold">Status</label>
                        <select id="editContractStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-[#2f855A] focus:border-[#2f855A]" required>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3 pt-2">
                        <button type="button" id="cancelEditContract" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                        <button type="submit" class="bg-[#28644c] text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-[#2f855A] focus:outline-none focus:ring-2 focus:ring-[#2f855A] shadow-sm transition-all duration-200">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Contract Modal -->
    <div id="addContractModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="add-contract-modal-title">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl mx-4 overflow-hidden" role="document">
            <!-- Modal Header -->
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 id="add-contract-modal-title" class="text-lg font-semibold text-gray-900">Add New Contract</h3>
                <button type="button" id="closeAddContractModal" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <form id="addContractForm" class="space-y-4">
                    @csrf
                    
                    <!-- Contract Code & Title -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contractCode" class="block text-sm font-medium text-gray-700 mb-1">Contract Code</label>
                            <div class="relative">
                                <input type="text" id="contractCode" name="code" readonly
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 text-sm"
                                    value="CTR-{{ strtoupper(uniqid()) }}">
                                <button type="button" id="regenerateCode" class="absolute inset-y-0 right-0 px-3 flex items-center text-[#2f855A] hover:text-[#28644c] focus:outline-none">
                                    <i class="fas fa-sync-alt text-sm"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="contractTitle" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                            <input type="text" id="contractTitle" name="title" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm"
                                placeholder="Contract Title">
                        </div>
                    </div>

                    <!-- Contract Type & Company -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="contractType" class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                            <select id="contractType" name="type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                                <option value="">Select Type</option>
                                <option value="nda">NDA</option>
                                <option value="service">Service</option>
                                <option value="employment">Employment</option>
                                <option value="employee">Employee</option>
                                <option value="consultancy">Consultancy</option>
                                <option value="internship">Internship</option>
                                <option value="probation">Probation</option>
                                <option value="vendor">Vendor</option>
                                <option value="supplier">Supplier</option>
                                <option value="lease">Lease</option>
                                <option value="license">License</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="purchase">Purchase</option>
                                <option value="sales">Sales</option>
                                <option value="partnership">Partnership</option>
                                <option value="loan">Loan</option>
                            </select>
                        </div>
                        <div>
                            <label for="contractCompany" class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                            <input type="text" id="contractCompany" name="company" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm"
                                placeholder="Company Name">
                        </div>
                    </div>

                    <!-- Status & Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="contractStatus" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select id="contractStatus" name="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                                <option value="draft">Draft</option>
                                <option value="active" selected>Active</option>
                                <option value="pending">Pending</option>
                                <option value="expired">Expired</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                        <div>
                            <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                            <input type="date" id="startDate" name="start_date" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                        </div>
                        <div>
                            <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" id="endDate" name="end_date"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm">
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="space-y-2">
                        <label for="contractFile" class="block text-sm font-medium text-gray-700 mb-2">Contract Document</label>
                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col w-full h-32 border-2 border-dashed hover:bg-gray-50 hover:border-gray-300 rounded-lg cursor-pointer">
                                <div class="flex flex-col items-center justify-center pt-7">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400 group-hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="pt-1 text-sm tracking-wider text-gray-500 group-hover:text-gray-600">
                                        Upload a file or drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 10MB</p>
                                </div>
                                <input id="contractFile" name="file" type="file" class="opacity-0" />
                            </label>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="contractDescription" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea id="contractDescription" name="description" rows="2"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#2f855A] focus:border-[#2f855A] text-sm"
                            placeholder="Brief description of the contract"></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" id="cancelAddContract" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A] transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" form="addContractForm" class="px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-[#2f855A] hover:bg-[#28644c] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855A] transition-colors duration-200">
                    Save Contract
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Contract Modal -->
    <div id="deleteContractModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-contract-modal-title">
        <div class="bg-white rounded-md shadow-lg w-[360px] max-w-full mx-4" role="document">
            <div class="flex justify-between items-center border-b border-gray-200 px-4 py-2">
                <h3 id="delete-contract-modal-title" class="font-semibold text-sm text-gray-900 select-none">Delete Contract</h3>
                <button id="closeDeleteContractModal" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8 text-center">
                <p class="text-xs text-gray-700 mb-4">Are you sure you want to delete <span class="font-semibold" id="deleteContractTitle"></span> (<span id="deleteContractId"></span>)?</p>
                <div class="flex justify-center space-x-3">
                    <button type="button" id="cancelDeleteContract" class="bg-gray-200 text-gray-700 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400 shadow-sm transition-all duration-200">Cancel</button>
                    <button type="button" id="confirmDeleteContract" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 shadow-sm transition-all duration-200">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Track selected contract
        let selectedContractId = null;

        // Toggle actions for a contract row
        function toggleContractActions(event, contractRow) {
            // Prevent event bubbling to avoid immediate hiding
            event.stopPropagation();
            
            // Hide all actions first
            document.querySelectorAll('.contract-actions').forEach(actions => {
                actions.classList.add('hidden');
            });
            
            // If clicking the same row, deselect it
            if (selectedContractId === contractRow.dataset.id) {
                selectedContractId = null;
            } else {
                // Show actions for the clicked row
                const actions = contractRow.querySelector('.contract-actions');
                if (actions) {
                    actions.classList.remove('hidden');
                    selectedContractId = contractRow.dataset.id;
                }
            }
        }

        // Close actions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.contract-actions') && !e.target.closest('tr[data-id]')) {
                document.querySelectorAll('.contract-actions').forEach(actions => {
                    actions.classList.add('hidden');
                });
                selectedContractId = null;
            }
        });

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
            const signOutModal = document.getElementById('signOutModal');
            const cancelSignOutBtn = document.getElementById('cancelSignOutBtn');
            const cancelSignOutBtn2 = document.getElementById('cancelSignOutBtn2');
            const openSignOutBtn = document.getElementById('openSignOutBtn');
            const addContractBtn = document.getElementById('addContractBtn');
            const addContractModal = document.getElementById('addContractModal');
            const closeAddContractModal = document.getElementById('closeAddContractModal');
            const cancelAddContract = document.getElementById('cancelAddContract');
            const viewContractModal = document.getElementById('viewContractModal');
            const closeViewContractModal = document.getElementById('closeViewContractModal');
            const closeViewContractModal2 = document.getElementById('closeViewContractModal2');
            const editContractModal = document.getElementById('editContractModal');
            const closeEditContractModal = document.getElementById('closeEditContractModal');
            const cancelEditContract = document.getElementById('cancelEditContract');
            const deleteContractModal = document.getElementById('deleteContractModal');
            const closeDeleteContractModal = document.getElementById('closeDeleteContractModal');
            const cancelDeleteContract = document.getElementById('cancelDeleteContract');
            const confirmDeleteContract = document.getElementById('confirmDeleteContract');
            let __currentEditOriginalStatus = null;

            // Masking state and helpers
            window.__contractsUnmasked = false;
            function renderRowDisplay(row, masked) {
                try {
                    const tds = row.querySelectorAll('td');
                    if (!tds || tds.length === 0) return;
                    if (tds.length === 1 && tds[0].hasAttribute('colspan')) return;

                    if (!row.__origCells || row.__origCells.length !== tds.length) {
                        row.__origCells = Array.from(tds).map(td => td.innerHTML);
                    }

                    if (!masked) {
                        if (row.__origCells && row.__origCells.length === tds.length) {
                            tds.forEach((td, i) => { td.innerHTML = row.__origCells[i]; });
                        }
                        return;
                    }

                    tds.forEach((td, i) => {
                        if (i === tds.length - 1) return;
                        td.textContent = '******';
                    });
                } catch (_) {}
            }

            function setTableMasked(masked) {
                document.querySelectorAll('table tbody tr').forEach(tr => renderRowDisplay(tr, masked));
            }

            function setActionsVisible(visible){
                try{
                    const th = document.querySelector('th.actions-col');
                    const cells = document.querySelectorAll('td.actions-cell');
                    if (th){ th.style.display = visible ? '' : 'none'; }
                    cells.forEach(td => { td.style.display = visible ? '' : 'none'; });
                }catch(_){ }
            }

            function labelizeType(t) {
                try {
                    t = (t || '').toString().trim();
                    if (!t) return '';
                    t = t.replace(/[_-]+/g, ' ');
                    return t.split(' ').filter(Boolean).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
                } catch (_) { return t || ''; }
            }

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

            // Handle sidebar dropdown toggles - Main implementation
            dropdownToggles.forEach((toggle) => {
                toggle.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const dropdown = toggle.nextElementSibling;
                    const chevron = toggle.querySelector(".bx-chevron-down");
                    
                    // Close all other dropdowns
                    dropdownToggles.forEach((otherToggle) => {
                        if (otherToggle !== toggle) {
                            const otherDropdown = otherToggle.nextElementSibling;
                            const otherChevron = otherToggle.querySelector(".bx-chevron-down");
                            if (otherDropdown) otherDropdown.classList.add("hidden");
                            if (otherChevron) otherChevron.classList.remove("rotate-180");
                        }
                    });
                    
                    // Toggle current dropdown
                    if (dropdown) dropdown.classList.toggle("hidden");
                    if (chevron) chevron.classList.toggle("rotate-180");
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e){
                if (!e.target.closest('.has-dropdown')) {
                    closeAllDropdowns();
                }
            });

            // Close dropdowns on escape key
            document.addEventListener('keydown', function(e){ 
                if(e.key==='Escape') closeAllDropdowns(); 
            });

            // Auto-expand the correct dropdown and highlight active submodule based on current URL
            try {
                var currentPath = window.location.pathname.replace(/\/$/, '');
                var links = document.querySelectorAll('#sidebar .dropdown-menu a');
                links.forEach(function(link){
                    var linkPath;
                    try { linkPath = new URL(link.href).pathname; } catch (_) { linkPath = link.getAttribute('href') || ''; }
                    if (linkPath) linkPath = linkPath.replace(/\/$/, '');
                    var isMatch = false;
                    if (linkPath) {
                        isMatch = (currentPath === linkPath) ||
                                  (currentPath.endsWith(linkPath)) ||
                                  (linkPath.endsWith(currentPath));
                    }
                    if (isMatch) {
                        var menu = link.closest('.dropdown-menu');
                        if (menu) {
                            menu.classList.remove('hidden');
                            var toggle = menu.previousElementSibling;
                            if (toggle) {
                                var chev = toggle.querySelector('.bx-chevron-down');
                                if (chev) chev.classList.add('rotate-180');
                            }
                        }
                        link.classList.add('bg-white/30');
                    }
                });
            } catch (err) { /* no-op */ }

            // Sidebar toggle functionality
            overlay.addEventListener("click", () => {
                sidebar.classList.add("-ml-72");
                overlay.classList.add("hidden");
                document.body.style.overflow = "";
                mainContent.classList.remove("sidebar-open");
                mainContent.classList.add("sidebar-closed");
                closeAllDropdowns();
            });

            toggleBtn.addEventListener("click", toggleSidebar);

            // Note: User menu and notification dropdown event listeners are handled by global functions above

            // Profile modal handlers
            if (openProfileBtn) {
                openProfileBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    profileModal.classList.add("active");
                    userMenuDropdown.classList.add("hidden");
                    userMenuBtn.setAttribute("aria-expanded", "false");
                    accountSettingsModal.classList.remove("active");
                    privacySecurityModal.classList.remove("active");
                    notificationDropdown.classList.add("hidden");
                    signOutModal.classList.remove("active");
                    addContractModal.classList.remove("active");
                });
            }

            if (closeProfileBtn) {
                closeProfileBtn.addEventListener("click", () => {
                    profileModal.classList.remove("active");
                });
            }
            if (closeProfileBtn2) {
                closeProfileBtn2.addEventListener("click", () => {
                    profileModal.classList.remove("active");
                });
            }

            // Account settings modal handlers
            if (openAccountSettingsBtn) {
                openAccountSettingsBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    accountSettingsModal.classList.add("active");
                    userMenuDropdown.classList.add("hidden");
                    userMenuBtn.setAttribute("aria-expanded", "false");
                    profileModal.classList.remove("active");
                    privacySecurityModal.classList.remove("active");
                    notificationDropdown.classList.add("hidden");
                    signOutModal.classList.remove("active");
                    addContractModal.classList.remove("active");
                });
            }

            if (closeAccountSettingsBtn) {
                closeAccountSettingsBtn.addEventListener("click", () => {
                    accountSettingsModal.classList.remove("active");
                });
            }
            if (cancelAccountSettingsBtn) {
                cancelAccountSettingsBtn.addEventListener("click", () => {
                    accountSettingsModal.classList.remove("active");
                });
            }

            // Privacy & Security modal handlers
            if (openPrivacySecurityBtn) {
                openPrivacySecurityBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    privacySecurityModal.classList.add("active");
                    userMenuDropdown.classList.add("hidden");
                    userMenuBtn.setAttribute("aria-expanded", "false");
                    profileModal.classList.remove("active");
                    accountSettingsModal.classList.remove("active");
                    notificationDropdown.classList.add("hidden");
                    signOutModal.classList.remove("active");
                    addContractModal.classList.remove("active");
                });
            }

            if (closePrivacySecurityBtn) {
                closePrivacySecurityBtn.addEventListener("click", () => {
                    privacySecurityModal.classList.remove("active");
                });
            }
            if (cancelPrivacySecurityBtn) {
                cancelPrivacySecurityBtn.addEventListener("click", () => {
                    privacySecurityModal.classList.remove("active");
                });
            }

            // Sign out modal handlers
            if (openSignOutBtn) {
                openSignOutBtn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    signOutModal.classList.add("active");
                    userMenuDropdown.classList.add("hidden");
                    userMenuBtn.setAttribute("aria-expanded", "false");
                    profileModal.classList.remove("active");
                    accountSettingsModal.classList.remove("active");
                    privacySecurityModal.classList.remove("active");
                    notificationDropdown.classList.add("hidden");
                    addContractModal.classList.remove("active");
                });
            }

            if (cancelSignOutBtn) {
                cancelSignOutBtn.addEventListener("click", () => {
                    signOutModal.classList.remove("active");
                });
            }
            if (cancelSignOutBtn2) {
                cancelSignOutBtn2.addEventListener("click", () => {
                    signOutModal.classList.remove("active");
                });
            }

            // Contract modals functionality
            function toggleAddContractModal() {
                if (addContractModal) {
                    addContractModal.classList.toggle("hidden");
                    addContractModal.classList.toggle("active");
                    document.body.classList.toggle("overflow-hidden");
                    notificationDropdown.classList.add("hidden");
                    userMenuDropdown.classList.add("hidden");
                    userMenuBtn.setAttribute("aria-expanded", "false");
                    profileModal.classList.remove("active");
                    accountSettingsModal.classList.remove("active");
                    privacySecurityModal.classList.remove("active");
                    signOutModal.classList.remove("active");
                    viewContractModal.classList.remove("active");
                    editContractModal.classList.remove("active");
                    deleteContractModal.classList.remove("active");
                }
            }

            if (addContractBtn) addContractBtn.addEventListener('click', toggleAddContractModal);
            if (closeAddContractModal) closeAddContractModal.addEventListener('click', toggleAddContractModal);
            if (cancelAddContract) cancelAddContract.addEventListener('click', toggleAddContractModal);

            // Handle View/Edit/Delete buttons
            const viewBtns = document.querySelectorAll('.viewContractBtn');
            const editBtns = document.querySelectorAll('.editContractBtn');
            const deleteBtns = document.querySelectorAll('.deleteContractBtn');

            function openViewModal(data) {
                document.getElementById('viewContractId').textContent = data.id;
                document.getElementById('viewContractTitle').textContent = data.title;
                document.getElementById('viewContractCompany').textContent = data.company;
                document.getElementById('viewContractType').textContent = data.type;
                const expEl = document.getElementById('viewContractExpiration');
                if (expEl) expEl.textContent = data.expiration || '';
                document.getElementById('viewContractStatus').textContent = data.status;
                document.getElementById('viewContractCreated').textContent = data.created;
                viewContractModal.classList.remove('hidden');
                viewContractModal.classList.add('active');
            }

            function openEditModal(data) {
                document.getElementById('editContractId').value = data.id;
                document.getElementById('editContractTitle').value = data.title;
                document.getElementById('editContractCompany').value = data.company;
                document.getElementById('editContractType').value = (data.type || '').toLowerCase();
                const normalized = (data.status || '').toLowerCase();
                document.getElementById('editContractStatus').value = normalized.includes('pending') ? 'pending' : normalized;
                __currentEditOriginalStatus = normalized.includes('pending') ? 'pending' : normalized;
                const xInp = document.getElementById('editContractExpiration');
                if (xInp) xInp.value = (data.expiration || '').substring(0,10);
                editContractModal.classList.remove('hidden');
                editContractModal.classList.add('active');
            }

            function openDeleteModal(data) {
                document.getElementById('deleteContractId').textContent = data.id;
                document.getElementById('deleteContractTitle').textContent = data.title;
                deleteContractModal.classList.remove('hidden');
                deleteContractModal.classList.add('active');
            }

            viewBtns.forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const d = e.currentTarget.dataset;
                openViewModal({ id: d.id, title: d.title, company: d.company, type: d.type, status: d.status, created: d.created, expiration: d.expiration });
            }));

            editBtns.forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const d = e.currentTarget.dataset;
                openEditModal({ id: d.id, title: d.title, company: d.company, type: d.type, status: d.status, created: d.created, expiration: d.expiration });
            }));

            deleteBtns.forEach(btn => btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const d = e.currentTarget.dataset;
                openDeleteModal({ id: d.id, title: d.title });
            }));

            // Close buttons for modals
            if (closeViewContractModal) {
                closeViewContractModal.addEventListener('click', () => { 
                    viewContractModal.classList.remove('active'); 
                    viewContractModal.classList.add('hidden'); 
                });
            }
            if (closeViewContractModal2) {
                closeViewContractModal2.addEventListener('click', () => { 
                    viewContractModal.classList.remove('active'); 
                    viewContractModal.classList.add('hidden'); 
                });
            }
            if (viewContractModal) {
                viewContractModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());
            }

            if (closeEditContractModal) {
                closeEditContractModal.addEventListener('click', () => { 
                    editContractModal.classList.remove('active'); 
                    editContractModal.classList.add('hidden'); 
                });
            }
            if (cancelEditContract) {
                cancelEditContract.addEventListener('click', () => { 
                    editContractModal.classList.remove('active'); 
                    editContractModal.classList.add('hidden'); 
                });
            }
            if (editContractModal) {
                editContractModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());
            }

            if (closeDeleteContractModal) {
                closeDeleteContractModal.addEventListener('click', () => { 
                    deleteContractModal.classList.remove('active'); 
                    deleteContractModal.classList.add('hidden'); 
                });
            }
            if (cancelDeleteContract) {
                cancelDeleteContract.addEventListener('click', () => { 
                    deleteContractModal.classList.remove('active'); 
                    deleteContractModal.classList.add('hidden'); 
                });
            }
            if (deleteContractModal) {
                deleteContractModal.querySelector('div').addEventListener('click', (e) => e.stopPropagation());
            }

            // Function to generate a new contract code
            function generateContractCode() {
                const timestamp = new Date().getTime().toString(36);
                const random = Math.random().toString(36).substr(2, 6).toUpperCase();
                return `CTR-${timestamp}-${random}`;
            }

            // Initialize contract code when modal opens
            document.getElementById('addContractModal').addEventListener('shown.bs.modal', function () {
                document.getElementById('contractCode').value = generateContractCode();
            });

            // Handle regenerate code button click
            document.getElementById('regenerateCode').addEventListener('click', function() {
                document.getElementById('contractCode').value = generateContractCode();
                
                // Add a small animation to the refresh icon
                const icon = this.querySelector('i');
                icon.classList.add('animate-spin');
                setTimeout(() => {
                    icon.classList.remove('animate-spin');
                }, 500);
            });

            // Submit handlers (wired to backend)
            const editContractForm = document.getElementById('editContractForm');
            if (editContractForm) {
                editContractForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const code = document.getElementById('editContractId').value;
                    const title = document.getElementById('editContractTitle').value.trim();
                    const company = document.getElementById('editContractCompany').value.trim();
                    const type = document.getElementById('editContractType').value;
                    const status = document.getElementById('editContractStatus').value;
                    const expiration = document.getElementById('editContractExpiration')?.value || '';

                    const resp = await fetch('{{ route('contracts.update') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ code, title, company, type, status, expiration })
                    });
                    const data = await resp.json();
                    if (data.success) {
                        // Update row in-place (find by data-code since display is masked)
                        const row = document.querySelector(`tbody tr[data-code="${code}"]`);
                        if (row) {
                            const statusKey = (status || '').toLowerCase();
                            const statusMap = {
                                active: { bg: 'bg-green-100', text: 'text-green-800', icon: 'fa-check-circle', label: 'Active' },
                                draft: { bg: 'bg-gray-100', text: 'text-gray-800', icon: 'fa-file', label: 'Draft' },
                                expired: { bg: 'bg-red-100', text: 'text-red-800', icon: 'fa-clock-rotate-left', label: 'Expired' },
                                pending: { bg: 'bg-yellow-100', text: 'text-yellow-800', icon: 'fa-clock', label: 'Pending' },
                                terminated: { bg: 'bg-red-100', text: 'text-red-800', icon: 'fa-ban', label: 'Terminated' },
                                renewed: { bg: 'bg-blue-100', text: 'text-blue-800', icon: 'fa-rotate', label: 'Renewed' },
                            };
                            const statusInfo = statusMap[statusKey] || { bg: 'bg-gray-100', text: 'text-gray-800', icon: 'fa-question-circle', label: (statusKey ? statusKey.charAt(0).toUpperCase() + statusKey.slice(1) : '') };

                            const titleEl = row.querySelector('td:nth-child(2) .font-medium');
                            if (titleEl) titleEl.textContent = title;
                            const typeCell = row.querySelector('td:nth-child(3)');
                            if (typeCell) typeCell.textContent = (type || '').toString();

                            const statusCell = row.querySelector('td:nth-child(4)');
                            if (statusCell) {
                                statusCell.innerHTML = `<div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${statusInfo.bg} ${statusInfo.text}"><i class="fas ${statusInfo.icon} mr-1.5"></i>${statusInfo.label}</div>`;
                            }

                            const companyCell = row.querySelector('td:nth-child(5)');
                            if (companyCell) companyCell.textContent = company || 'N/A';

                            const endDateCell = row.querySelector('td:nth-child(7)');
                            if (endDateCell) {
                                if (expiration) {
                                    endDateCell.innerHTML = `<div class="flex items-center"><i class="fas fa-calendar-check mr-2 text-gray-400"></i><span>${expiration}</span></div>`;
                                } else {
                                    endDateCell.innerHTML = `<span class="text-gray-400">No end date</span>`;
                                }
                            }

                            // Update action buttons datasets
                            const viewBtn = row.querySelector('.viewContractBtn');
                            const editBtn = row.querySelector('.editContractBtn');
                            if (viewBtn) {
                                const badgeLabel = labelizeType(type);
                                viewBtn.dataset.title = title;
                                viewBtn.dataset.company = company;
                                viewBtn.dataset.type = badgeLabel;
                                viewBtn.dataset.status = statusInfo.label;
                                viewBtn.dataset.expiration = expiration || '';
                            }
                            if (editBtn) {
                                editBtn.dataset.title = title;
                                editBtn.dataset.company = company;
                                editBtn.dataset.type = type;
                                editBtn.dataset.status = status;
                                editBtn.dataset.expiration = expiration || '';
                            }

                            // Refresh cached row HTML for masking/unmasking
                            try {
                                row.__origCells = Array.from(row.querySelectorAll('td')).map(td => td.innerHTML);
                            } catch (_) {}

                            // Re-render display respecting current mask state
                            renderRowDisplay(row, !window.__contractsUnmasked);
                            // Update stats if status changed
                            try {
                                const totalEl = document.getElementById('totalContractsText');
                                const activeCountEl = document.getElementById('activeCountEl');
                                const activeCountText = document.getElementById('activeCountText');
                                const pendingCountEl = document.getElementById('pendingCountEl');
                                const pendingCountText = document.getElementById('pendingCountText');
                                let total = parseInt(totalEl?.textContent || '0', 10);
                                let active = parseInt(activeCountEl?.textContent || '0', 10);
                                let pending = parseInt(pendingCountEl?.textContent || '0', 10);
                                const prev = (__currentEditOriginalStatus || '').toLowerCase();
                                const next = (status || '').toLowerCase();
                                if (prev !== next) {
                                    if (prev === 'active') active = Math.max(0, active - 1);
                                    if (prev === 'pending') pending = Math.max(0, pending - 1);
                                    if (next === 'active') active += 1;
                                    if (next === 'pending') pending += 1;
                                }
                                if (activeCountEl) activeCountEl.textContent = active;
                                if (activeCountText) activeCountText.textContent = active;
                                if (pendingCountEl) pendingCountEl.textContent = pending;
                                if (pendingCountText) pendingCountText.textContent = pending;
                                const activePct = total > 0 ? Math.round((active / total) * 100) : 0;
                                const pendingPct = total > 0 ? Math.round((pending / total) * 100) : 0;
                                const activeBar = document.getElementById('activeBar');
                                const pendingBar = document.getElementById('pendingBar');
                                const activePctEl = document.getElementById('activePctEl');
                                const pendingPctEl = document.getElementById('pendingPctEl');
                                const activeTotalEl = document.getElementById('activeTotalEl');
                                const pendingTotalEl = document.getElementById('pendingTotalEl');
                                if (activeBar) activeBar.style.width = activePct + '%';
                                if (pendingBar) pendingBar.style.width = pendingPct + '%';
                                if (activePctEl) activePctEl.textContent = activePct + '%';
                                if (pendingPctEl) pendingPctEl.textContent = pendingPct + '%';
                                if (activeTotalEl) activeTotalEl.textContent = total;
                                if (pendingTotalEl) pendingTotalEl.textContent = total;
                            } catch (e) { /* no-op */ }
                            __currentEditOriginalStatus = null;
                        }
                        editContractModal.classList.remove('active');
                        editContractModal.classList.add('hidden');
                        Swal.fire({ icon: 'success', title: 'Saved', text: 'Contract updated.', confirmButtonColor: '#2f855a' });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: 'Update failed.', confirmButtonColor: '#2f855a' });
                    }
                });
            }

            if (confirmDeleteContract) {
                confirmDeleteContract.addEventListener('click', async () => {
                    const code = document.getElementById('deleteContractId').textContent;
                    const resp = await fetch('{{ route('contracts.delete') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ code })
                    });
                    const data = await resp.json();
                    if (data.success) {
                        const row = document.querySelector(`tbody tr[data-code="${code}"]`);
                        if (row) row.remove();
                        deleteContractModal.classList.remove('active');
                        deleteContractModal.classList.add('hidden');
                        Swal.fire({ icon: 'success', title: 'Deleted', text: 'Contract deleted.', confirmButtonColor: '#2f855a' });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Failed', text: 'Delete failed.', confirmButtonColor: '#2f855a' });
                    }
                });
            }

            const addContractForm = document.getElementById('addContractForm');
            if (addContractForm) {
                addContractForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    // Get form values
                    const form = e.target;
                    const formData = new FormData(form);
                    
                    const type = document.getElementById('contractType')?.value || 'service';
                    const status = document.getElementById('contractStatus')?.value || 'draft';
                    const startDate = document.getElementById('startDate')?.value || new Date().toISOString().split('T')[0];
                    const endDate = document.getElementById('endDate')?.value || '';

                    formData.delete('type');
                    formData.delete('status');
                    formData.delete('start_date');
                    formData.delete('end_date');

                    formData.append('type', type);
                    formData.append('status', status);
                    formData.append('start_date', startDate);
                    if (endDate) {
                        formData.append('end_date', endDate);
                    }
                    
                    // Log the form data for debugging
                    console.log('Form data:', Object.fromEntries(formData.entries()));

                    try {
                        console.log('Sending request to server...');
                        const resp = await fetch('{{ route('contracts.create') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        });
                        
                        const responseText = await resp.text();
                        let data;
                        try {
                            data = JSON.parse(responseText);
                            console.log('Server response:', data);
                            
                            if (!resp.ok) {
                                let errorMsg = 'Server error: ' + (data?.message || resp.statusText || 'Unknown error');
                                if (data?.errors) {
                                    errorMsg += '\n' + Object.entries(data.errors)
                                        .map(([field, errors]) => `${field}: ${Array.isArray(errors) ? errors.join(', ') : errors}`)
                                        .join('\n');
                                }
                                throw new Error(errorMsg);
                            }
                            
                            if (!data.contract) {
                                console.error('Unexpected response format. Missing contract data.');
                                throw new Error('Invalid response format from server');
                            }
                        } catch (e) {
                            console.error('Error parsing server response:', e);
                            console.error('Raw response:', responseText);
                            throw new Error('Invalid server response. Please check the console for details.');
                        }
                        
                        if (!data || !data.success) {
                            throw new Error(data?.message || 'Failed to create contract. No success response from server.');
                        }

                        // Update stats
                        const totalEl = document.getElementById('totalContractsText');
                        const activeCountEl = document.getElementById('activeCountEl');
                        const activeCountText = document.getElementById('activeCountText');
                        const pendingCountEl = document.getElementById('pendingCountEl');
                        const pendingCountText = document.getElementById('pendingCountText');
                        let total = parseInt(totalEl?.textContent || '0', 10) + 1;
                        let active = parseInt(activeCountEl?.textContent || '0', 10);
                        let pending = parseInt(pendingCountEl?.textContent || '0', 10);
                        const statusValue = formData.get('status');
                        if (statusValue === 'active') active += 1; 
                        else if (statusValue === 'pending') pending += 1;
                        if (totalEl) totalEl.textContent = total;
                        if (activeCountEl) activeCountEl.textContent = active;
                        if (activeCountText) activeCountText.textContent = active;
                        if (pendingCountEl) pendingCountEl.textContent = pending;
                        if (pendingCountText) pendingCountText.textContent = pending;
                        // Update bars and pct labels
                        const activePct = total > 0 ? Math.round((active / total) * 100) : 0;
                        const pendingPct = total > 0 ? Math.round((pending / total) * 100) : 0;
                        const activeBar = document.getElementById('activeBar');
                        const pendingBar = document.getElementById('pendingBar');
                        const activePctEl = document.getElementById('activePctEl');
                        const pendingPctEl = document.getElementById('pendingPctEl');
                        const activeTotalEl = document.getElementById('activeTotalEl');
                        const pendingTotalEl = document.getElementById('pendingTotalEl');
                        if (activeBar) activeBar.style.width = activePct + '%';
                        if (pendingBar) pendingBar.style.width = pendingPct + '%';
                        if (activePctEl) activePctEl.textContent = activePct + '%';
                        if (pendingPctEl) pendingPctEl.textContent = pendingPct + '%';
                        if (activeTotalEl) activeTotalEl.textContent = total;
                        if (pendingTotalEl) pendingTotalEl.textContent = total;

                        // Append new row to table top
                        const type = formData.get('type');
                        const expiration = formData.get('end_date');
                        const c = data.contract || {};
                        const badge = labelizeType(c.type || type);
                        const statusLabel = (c.status || '').charAt(0).toUpperCase() + (c.status || '').slice(1);
                        const statusKey = (c.status || '').toLowerCase();
                        const statusMap = {
                            active: { bg: 'bg-green-100', text: 'text-green-800', icon: 'fa-check-circle', label: 'Active' },
                            draft: { bg: 'bg-gray-100', text: 'text-gray-800', icon: 'fa-file', label: 'Draft' },
                            expired: { bg: 'bg-red-100', text: 'text-red-800', icon: 'fa-clock-rotate-left', label: 'Expired' },
                            pending: { bg: 'bg-yellow-100', text: 'text-yellow-800', icon: 'fa-clock', label: 'Pending' },
                            terminated: { bg: 'bg-red-100', text: 'text-red-800', icon: 'fa-ban', label: 'Terminated' },
                            renewed: { bg: 'bg-blue-100', text: 'text-blue-800', icon: 'fa-rotate', label: 'Renewed' },
                        };
                        const statusInfo = statusMap[statusKey] || { bg: 'bg-gray-100', text: 'text-gray-800', icon: 'fa-question-circle', label: statusLabel };
                        const startDateDisplay = c.start_date || formData.get('start_date') || '';
                        const endDateDisplay = c.end_date || c.expires_on || formData.get('end_date') || '';
                        const tbody = document.querySelector('table tbody');
                        if (tbody) {
                            const tr = document.createElement('tr');
                            tr.className = 'table-row';
                            tr.setAttribute('data-code', c.code);
                            tr.innerHTML = `
                                <td class="px-4 py-3 text-sm text-gray-500">${c.code || 'N/A'}</td>
                                <td class="px-4 py-3 text-sm text-gray-900"><div class="font-medium">${c.title || ''}</div></td>
                                <td class="px-4 py-3 text-sm text-gray-500">${badge || 'N/A'}</td>
                                <td class="px-4 py-3 whitespace-nowrap"><div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${statusInfo.bg} ${statusInfo.text}"><i class="fas ${statusInfo.icon} mr-1.5"></i>${statusInfo.label}</div></td>
                                <td class="px-4 py-3 text-sm text-gray-500">${c.company || 'N/A'}</td>
                                <td class="px-4 py-3 text-sm text-gray-500"><div class="flex items-center"><i class="fas fa-calendar-day mr-2 text-gray-400"></i><span>${startDateDisplay || 'Not set'}</span></div></td>
                                <td class="px-4 py-3 text-sm text-gray-500">${endDateDisplay ? `<div class=\"flex items-center\"><i class=\"fas fa-calendar-check mr-2 text-gray-400\"></i><span>${endDateDisplay}</span></div>` : `<span class=\"text-gray-400\">No end date</span>`}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="viewContractBtn text-[#2f855A] hover:text-[#1a4d38] mr-3" data-tooltip="View" data-id="${c.code}" data-title="${c.title || ''}" data-company="${c.company || ''}" data-type="${badge}" data-status="${statusLabel}" data-created="${c.created_on || ''}" data-expiration="${c.expires_on || c.expiration || expiration || ''}">View</a>
                                    <a href="#" class="editContractBtn text-blue-600 hover:text-blue-900 mr-3" data-tooltip="Edit" data-id="${c.code}" data-title="${c.title || ''}" data-company="${c.company || ''}" data-type="${c.type || ''}" data-status="${c.status || ''}" data-created="${c.created_on || ''}" data-expiration="${c.expires_on || c.expiration || expiration || ''}">Edit</a>
                                    <a href="#" class="deleteContractBtn text-red-600 hover:text-red-900" data-tooltip="Delete" data-id="${c.code}" data-title="${c.title || ''}">Delete</a>
                                </td>`;
                            // Insert at top (after potential empty row removed)
                            const emptyRow = tbody.querySelector('tr td[colspan]');
                            if (emptyRow) emptyRow.parentElement?.remove();
                            tbody.insertBefore(tr, tbody.firstChild);
                            // Cache original HTML for masking/unmasking
                            try { tr.__origCells = Array.from(tr.querySelectorAll('td')).map(td => td.innerHTML); } catch (_) {}

                            // Render masked/unmasked according to current state
                            renderRowDisplay(tr, !window.__contractsUnmasked);

                            // Rebind action buttons for the new row
                            tr.querySelectorAll('.viewContractBtn').forEach(btn => btn.addEventListener('click', (e) => {
                                e.preventDefault(); e.stopPropagation();
                                const d = e.currentTarget.dataset;
                                openViewModal({ id: d.id, title: d.title, company: d.company, type: d.type, status: d.status, created: d.created });
                            }));
                            tr.querySelectorAll('.editContractBtn').forEach(btn => btn.addEventListener('click', (e) => {
                                e.preventDefault(); e.stopPropagation();
                                const d = e.currentTarget.dataset;
                                openEditModal({ id: d.id, title: d.title, company: d.company, type: d.type, status: d.status, created: d.created });
                            }));
                            tr.querySelectorAll('.deleteContractBtn').forEach(btn => btn.addEventListener('click', (e) => {
                                e.preventDefault(); e.stopPropagation();
                                const d = e.currentTarget.dataset;
                                openDeleteModal({ id: d.id, title: d.title });
                            }));
                        }

                        // Reset and close modal
                        addContractForm.reset();
                        toggleAddContractModal();
                        Swal.fire({ icon: 'success', title: 'Contract Added', text: 'The contract has been added successfully.', confirmButtonColor: '#2f855a' });
                    } catch (err) {
console.error('Error creating contract:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Create Contract',
                            text: err.message || 'An unknown error occurred while creating the contract.',
                            confirmButtonColor: '#2f855a',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }

            // Handle window resize
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

            // Tooltip functionality
            const tooltipTriggers = document.querySelectorAll("[data-tooltip]");
            tooltipTriggers.forEach(trigger => {
                trigger.addEventListener("mouseenter", (e) => {
                    const tooltip = document.createElement("div");
                    tooltip.className = "absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg";
                    tooltip.textContent = e.target.dataset.tooltip;
                    document.body.appendChild(tooltip);

                    const rect = e.target.getBoundingClientRect();
                    tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
                    tooltip.style.left = `${rect.left + window.scrollX}px`;

                    e.target._tooltip = tooltip;
                });

                trigger.addEventListener("mouseleave", (e) => {
                    if (e.target._tooltip) {
                        e.target._tooltip.remove();
                        delete e.target._tooltip;
                    }
                });
            });

            // Case lock synchronization with case management
            function updateContractLockState(isLocked) {
                const tableRows = document.querySelectorAll('tbody tr[data-id]');
                console.log('Contract management found rows:', tableRows.length, 'isLocked:', isLocked);
                
                tableRows.forEach((row, index) => {
                    console.log(`Processing contract row ${index}:`, row);
                    
                    const titleCell = row.querySelector('td:nth-child(2) .font-medium');
                    const companyCell = row.querySelector('td:nth-child(5)');
                    const typeCell = row.querySelector('td:nth-child(3)');
                    const statusCell = row.querySelector('td:nth-child(4) div');
                    const endDateCell = row.querySelector('td:nth-child(7)');
                    const viewButton = row.querySelector('.viewContractBtn');
                    const editButton = row.querySelector('.editContractBtn');
                    const deleteButton = row.querySelector('.deleteContractBtn');
                    
                    console.log('Found cells:', { titleCell, companyCell, typeCell, statusCell, endDateCell });
                    
                    if (isLocked) {
                        // Store original data if not already stored
                        if (!row.dataset.originalData) {
                            row.dataset.originalData = JSON.stringify({
                                title: titleCell?.textContent || '',
                                company: companyCell?.textContent || '',
                                type: typeCell?.textContent || '',
                                status: statusCell?.textContent || '',
                                statusClass: statusCell?.className || '',
                                expiration: endDateCell?.textContent || ''
                            });
                        }
                        
                        // Mask the data
                        if (titleCell) {
                            const maskedTitle = titleCell.textContent.replace(/./g, '*');
                            titleCell.innerHTML = maskedTitle + ' <i class="fas fa-lock text-red-500 text-xs ml-1"></i>';
                        }
                        if (companyCell) {
                            companyCell.textContent = '****';
                        }
                        if (typeCell) {
                            typeCell.textContent = '****';
                        }
                        if (statusCell) {
                            statusCell.textContent = '****';
                            statusCell.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
                        }
                        if (endDateCell) {
                            endDateCell.textContent = '** ** ****';
                        }
                        
                        // Disable action buttons
                        if (viewButton) {
                            viewButton.disabled = true;
                            viewButton.style.opacity = '0.5';
                            viewButton.style.cursor = 'not-allowed';
                            viewButton.style.pointerEvents = 'none';
                        }
                        if (editButton) {
                            editButton.disabled = true;
                            editButton.style.opacity = '0.5';
                            editButton.style.cursor = 'not-allowed';
                            editButton.style.pointerEvents = 'none';
                        }
                        if (deleteButton) {
                            deleteButton.disabled = true;
                            deleteButton.style.opacity = '0.5';
                            deleteButton.style.cursor = 'not-allowed';
                            deleteButton.style.pointerEvents = 'none';
                        }
                        
                        // Add lock styling to row
                        row.style.opacity = '0.7';
                        row.classList.add('locked-row');
                    } else {
                        // Restore original data
                        if (row.dataset.originalData) {
                            try {
                                const originalData = JSON.parse(row.dataset.originalData);
                                
                                if (titleCell) {
                                    titleCell.textContent = originalData.title;
                                }
                                if (companyCell) {
                                    companyCell.textContent = originalData.company;
                                }
                                if (typeCell) {
                                    typeCell.textContent = originalData.type;
                                }
                                if (statusCell) {
                                    statusCell.textContent = originalData.status;
                                    statusCell.className = originalData.statusClass;
                                }
                                if (endDateCell) {
                                    endDateCell.textContent = originalData.expiration;
                                }
                            } catch (e) {
                                console.error('Error restoring original data:', e);
                            }
                        }
                        
                        // Restore action buttons
                        if (viewButton) {
                            viewButton.disabled = false;
                            viewButton.style.opacity = '1';
                            viewButton.style.cursor = 'pointer';
                            viewButton.style.pointerEvents = 'auto';
                        }
                        if (editButton) {
                            editButton.disabled = false;
                            editButton.style.opacity = '1';
                            editButton.style.cursor = 'pointer';
                            editButton.style.pointerEvents = 'auto';
                        }
                        if (deleteButton) {
                            deleteButton.disabled = false;
                            deleteButton.style.opacity = '1';
                            deleteButton.style.cursor = 'pointer';
                            deleteButton.style.pointerEvents = 'auto';
                        }
                        
                        // Remove lock styling from row
                        row.style.opacity = '1';
                        row.classList.remove('locked-row');
                    }
                });
            }

            // Check and apply lock state on page load
            function checkAndApplyContractLockState() {
                const isLocked = localStorage.getItem('casesLocked') === 'true';
                updateContractLockState(isLocked);
            }

            // Listen for storage changes (for cross-tab synchronization)
            window.addEventListener('storage', (e) => {
                console.log('Contract management received storage event:', e.key, e.newValue);
                if (e.key === 'casesLocked') {
                    const isLocked = e.newValue === 'true';
                    console.log('Contract management updating lock state to:', isLocked);
                    updateContractLockState(isLocked);
                }
            });

            // Apply lock state on page load
            checkAndApplyContractLockState();
        });
    </script>
</body>
</html>