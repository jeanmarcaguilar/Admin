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
    <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
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
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }

        .modal.active {
            display: flex;
        }

        .modal > div:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: box-shadow 0.2s ease-in-out;
        }

        /* Match Visitors Registration modal overlay for New Case only */
        #newCaseModal {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
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

        /* OTP segmented inputs */
        .otp-grid { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 10px; }
        .otp-input { text-align: center; font-size: 18px; letter-spacing: 2px; }
        .otp-input::-webkit-outer-spin-button,
        .otp-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .otp-input[disabled] { background-color: #f3f4f6; cursor: not-allowed; }

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
            if(ev && ev.stopImmediatePropagation) ev.stopImmediatePropagation();
            var btn=document.getElementById('userMenuBtn');
            var menu=document.getElementById('userMenuDropdown');
            var notif=document.getElementById('notificationDropdown');
            if(menu){
              var isHidden = menu.classList.contains('hidden');
              if(isHidden){
                menu.classList.remove('hidden');
                menu.style.display = 'block';
                // dynamic positioning under the button
                if (btn) {
                  var rect = btn.getBoundingClientRect();
                  var top = rect.bottom + 8; // 8px gap
                  menu.style.position = 'fixed';
                  menu.style.top = top + 'px';
                  // compute width and align menu's right edge to button's right edge
                  var width = menu.offsetWidth || 192; // fallback ~12rem
                  var left = Math.max(8, Math.min(rect.right - width, window.innerWidth - width - 8));
                  menu.style.left = left + 'px';
                  menu.style.right = 'auto';
                  menu.style.zIndex = 9999;
                }
                window.__lastMenuOpenTs = Date.now();
              } else {
                menu.classList.add('hidden');
                menu.style.display = 'none';
              }
            }
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

      // Continue with other modal functions
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
          if (submitBtn){ 
            submitBtn.disabled = true; 
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...'; 
          }
          
          try {
            // Get all form data including contract_type
            var formData = new FormData(form);
            var formObj = {};
            formData.forEach((value, key) => {
              // Handle form data properly, especially for checkboxes and selects
              if (formObj[key]) {
                if (!Array.isArray(formObj[key])) {
                  formObj[key] = [formObj[key]];
                }
                formObj[key].push(value);
              } else {
                formObj[key] = value;
              }
            });

            // Ensure contract_type is included even if empty
            if (!formObj.contract_type) {
              formObj.contract_type = '';
            }

            var tokenMeta = document.querySelector('meta[name="csrf-token"]');
            var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
            
            var response = await fetch('{{ route("case.create") }}', {
              method: 'POST',
              headers: { 
                'X-CSRF-TOKEN': csrf, 
                'Accept': 'application/json', 
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: formData
            });
            
            var data = await response.json();
            if (!response.ok) { 
              console.error('Server error:', data);
              throw new Error(data.message || 'Failed to create case. ' + (data.error || '')); 
            }
            
            await Swal.fire({ 
              icon: 'success', 
              title: 'Success!', 
              text: 'Case has been created successfully.', 
              showConfirmButton: false, 
              timer: 1500 
            });
            
            form.reset();
            window.closeModal('newCaseModal');
            window.location.reload();
          }catch(error){
            console.error('Error:', error);
            // Check if this is a validation error with error details
            let errorMessage = (error && error.message) || 'Failed to create case. Please try again.';
            
            // If it's a validation error from the server, extract the error messages
            if (error.response && error.response.data && error.response.data.errors) {
              const errors = error.response.data.errors;
              errorMessage = Object.values(errors).flat().join('\n');
            }
            
            Swal.fire({ 
              icon: 'error', 
              title: 'Error', 
              html: errorMessage.replace(/\n/g, '<br>'),
              confirmButtonColor: '#2f855a',
              width: '500px'
            });
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
          // If an inline onclick already exists, do not bind another listener to avoid double toggle
          var hasInline = !!ub.getAttribute('onclick');
          if(!hasInline){
            ub.addEventListener('click', function(e){ if(window.toggleUserMenu) window.toggleUserMenu(e); });
          }
        }
        // Bind notification button click
        var nb=document.getElementById('notificationBtn');
        if(nb){
          nb.addEventListener('click', function(e){ window.toggleNotification(e); });
        }
        // Close on outside click
        document.addEventListener('click', function(e){
          if (typeof window.__lastMenuOpenTs === 'number' && (Date.now() - window.__lastMenuOpenTs) < 120) { return; }
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
      // Close dropdowns on resize/scroll to avoid stale positioning
      window.addEventListener('resize', function(){ if(window.hideAllMenus) window.hideAllMenus(); });
      window.addEventListener('scroll', function(){ if(window.hideAllMenus) window.hideAllMenus(); }, { passive: true });
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

    <script>
      (function(){
        function showDeleteModal(){ var m=document.getElementById('deleteCaseModal'); if(!m) return; m.classList.add('active'); m.classList.remove('hidden'); }
        function hideDeleteModal(){ var m=document.getElementById('deleteCaseModal'); if(!m) return; m.classList.remove('active'); m.classList.add('hidden'); }
        function bindDeleteHandlers(){
          try{
            var btns = document.querySelectorAll('.deleteCaseBtn');
            btns.forEach(function(b){
              b.addEventListener('click', function(e){
                e.preventDefault();
                var num = b.getAttribute('data-number') || '';
                var disp = document.getElementById('delCaseNumber'); if(disp) disp.textContent = num;
                var input = document.getElementById('delCaseNumberInput'); if(input) input.value = num;
                showDeleteModal();
              });
            });
            var close1 = document.getElementById('closeDeleteCaseBtn'); if(close1) close1.addEventListener('click', hideDeleteModal);
            var cancel = document.getElementById('cancelDeleteCaseBtn'); if(cancel) cancel.addEventListener('click', hideDeleteModal);
            var confirmBtn = document.getElementById('confirmDeleteCaseBtn');
            if (confirmBtn){
              confirmBtn.addEventListener('click', async function(){
                var num = (document.getElementById('delCaseNumberInput') || {}).value || '';
                if (!num) { hideDeleteModal(); return; }
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');
                var csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                try{
                  var fd = new FormData(); fd.append('number', num);
                  var res = await fetch('{{ route("case.delete") }}', { method: 'POST', headers: { 'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': csrf, 'Accept':'application/json' }, body: fd });
                  var data = await res.json().catch(function(){ return {}; });
                  if (!res.ok || !data.success){ throw new Error((data && data.message) || 'Delete failed'); }
                  // Remove the row or refresh
                  try{
                    var row = document.querySelector('tr[data-number="'+CSS.escape(num)+'"]');
                    if (row && row.parentNode){ row.parentNode.removeChild(row); }
                  }catch(_){ }
                  hideDeleteModal();
                  // To keep stats consistent, refresh the page
                  window.location.reload();
                }catch(err){
                  alert('Failed to delete case.');
                }
              });
            }
          }catch(_){ }
        }
        if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', bindDeleteHandlers, { once: true }); }
        else { bindDeleteHandlers(); }
      })();
    </script>

    <!-- Delete Case Modal -->
    <div id="deleteCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="delete-case-title">
        <div class="bg-white rounded-lg w-full max-w-sm mx-4">
            <div class="flex justify-between items-center border-b px-6 py-3">
                <h3 id="delete-case-title" class="text-base font-semibold text-gray-900">Delete Case</h3>
                <button type="button" id="closeDeleteCaseBtn" class="text-gray-400 hover:text-gray-600" aria-label="Close">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-700">Are you sure you want to delete case <span id="delCaseNumber" class="font-semibold"></span>? This action cannot be undone.</p>
                <input type="hidden" id="delCaseNumberInput" />
                <div class="mt-5 flex justify-end space-x-3">
                    <button type="button" id="cancelDeleteCaseBtn" class="bg-gray-200 text-gray-800 rounded-lg px-4 py-2 text-sm font-semibold hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-400">Cancel</button>
                    <button type="button" id="confirmDeleteCaseBtn" class="bg-red-600 text-white rounded-lg px-4 py-2 text-sm font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Menu Dropdown -->
    <!-- User Menu Dropdown -->
    <div id="userMenuDropdown" class="hidden fixed right-4 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200" style="top: 4rem; z-index: 9999;" role="menu" aria-labelledby="userMenuBtn" onclick="(function(e){ if(e&&e.stopPropagation) e.stopPropagation(); if(e&&e.stopImmediatePropagation) e.stopImmediatePropagation(); })(event)">
        <div class="py-4 px-6 border-b border-gray-100 text-center">
            <div class="w-14 h-14 rounded-full bg-[#28644c] text-white mx-auto flex items-center justify-center mb-2">
                <i class="fas fa-user-circle text-3xl"></i>
            </div>
            <p class="font-semibold text-[#28644c]">{{ $user->name }}</p>
            <p class="text-xs text-gray-400">Administrator</p>
        </div>
        <ul class="text-sm text-gray-700">
            <li><button type="button" id="openProfileBtn" onclick="(function(e){ if(e&&e.stopPropagation) e.stopPropagation(); if(e&&e.stopImmediatePropagation) e.stopImmediatePropagation(); var d=document.getElementById('userMenuDropdown'); if(d){ d.classList.add('hidden'); d.style.display='none'; } var b=document.getElementById('userMenuBtn'); if(b){ b.setAttribute('aria-expanded','false'); } openProfileModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-user-circle mr-2"></i> My Profile</button></li>
            <li><button type="button" id="openAccountSettingsBtn" onclick="(function(e){ if(e&&e.stopPropagation) e.stopPropagation(); if(e&&e.stopImmediatePropagation) e.stopImmediatePropagation(); var d=document.getElementById('userMenuDropdown'); if(d){ d.classList.add('hidden'); d.style.display='none'; } var b=document.getElementById('userMenuBtn'); if(b){ b.setAttribute('aria-expanded','false'); } openAccountSettingsModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-cog mr-2"></i> Account Settings</button></li>
            <li><button type="button" id="openPrivacySecurityBtn" onclick="(function(e){ if(e&&e.stopPropagation) e.stopPropagation(); if(e&&e.stopImmediatePropagation) e.stopImmediatePropagation(); var d=document.getElementById('userMenuDropdown'); if(d){ d.classList.add('hidden'); d.style.display='none'; } var b=document.getElementById('userMenuBtn'); if(b){ b.setAttribute('aria-expanded','false'); } openPrivacySecurityModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-shield-alt mr-2"></i> Privacy & Security</button></li>
            <li><button type="button" id="signOutBtn" onclick="(function(e){ if(e&&e.stopPropagation) e.stopPropagation(); if(e&&e.stopImmediatePropagation) e.stopImmediatePropagation(); var d=document.getElementById('userMenuDropdown'); if(d){ d.classList.add('hidden'); d.style.display='none'; } var b=document.getElementById('userMenuBtn'); if(b){ b.setAttribute('aria-expanded','false'); } openSignOutModal(); })(event)" class="w-full text-left flex items-center px-6 py-2 text-red-600 hover:bg-gray-100 focus:outline-none" role="menuitem" tabindex="-1"><i class="fas fa-sign-out-alt mr-2"></i> Sign Out</button></li>
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
                        <div class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer" aria-expanded="false" role="button">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <!-- Active Cases Only -->
                        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Active Cases</p>
                                    <h3 class="text-2xl font-bold text-green-600 mt-2" id="activeCasesCount">
                                        {{ $stats['active_cases'] }}
                                    </h3>
                                    <p class="text-xs text-gray-500 mt-1">Currently active</p>
                                </div>
                                <div class="p-3 rounded-full bg-gradient-to-br from-green-400 to-green-600 text-white shadow-lg">
                                    <i class="fas fa-gavel text-lg"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php 
                                    $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['active_cases'] / $stats['total_cases']) * 100)) : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">{{ $pct }}% of total cases</p>
                            </div>
                        </div>

                        <!-- Pending Cases -->
                        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Pending</p>
                                    <h3 class="text-2xl font-bold text-blue-600 mt-2" id="pendingCasesCount">{{ $stats['pending_tasks'] ?? 0 }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">In progress</p>
                                </div>
                                <div class="p-3 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white shadow-lg">
                                    <i class="fas fa-clock text-lg"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php 
                                    $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['pending_tasks'] / $stats['total_cases']) * 100)) : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">{{ $pct }}% of total cases</p>
                            </div>
                        </div>

                        <!-- Urgent Cases -->
                        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">Urgent</p>
                                    <h3 class="text-2xl font-bold text-yellow-600 mt-2" id="urgentCasesCount">{{ $stats['urgent_cases'] ?? 0 }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">High priority</p>
                                </div>
                                <div class="p-3 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 text-white shadow-lg">
                                    <i class="fas fa-exclamation-triangle text-lg"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php 
                                    $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['urgent_cases'] / $stats['total_cases']) * 100)) : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $pct }}% of total cases</p>
                            </div>
                        </div>

                        <!-- Total Active Cases (Combined) -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl shadow-lg p-6 lg:col-span-2 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-blue-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Active Cases</p>
                                    <h3 class="text-3xl font-bold text-gray-900 mt-2" id="totalActiveCasesCount">{{ $stats['active_cases'] ?? 0 }}</h3>
                                    <p class="text-xs text-gray-600 mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                                            <i class="fas fa-check-circle mr-1"></i>{{ $stats['active_cases'] }} active
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mr-2">
                                            <i class="fas fa-clock mr-1"></i>{{ $stats['pending_tasks'] ?? 0 }} pending
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-exclamation-circle mr-1"></i>{{ $stats['urgent_cases'] ?? 0 }} urgent
                                        </span>
                                    </p>
                                </div>
                                <div class="p-4 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg">
                                    <i class="fas fa-chart-line text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                @php 
                                    $pct = $stats['total_cases'] > 0 ? min(100, round(($stats['active_cases'] / $stats['total_cases']) * 100)) : 0;
                                    $trend = $stats['total_cases'] > 0 ? round(($stats['active_cases'] / $stats['total_cases']) * 100) - 50 : 0;
                                @endphp
                                <div class="w-full bg-white rounded-full h-3 overflow-hidden shadow-inner">
                                    <div class="bg-gradient-to-r from-blue-400 to-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <p class="text-xs text-gray-600">{{ $pct }}% of total cases</p>
                                    @if($trend > 0)
                                        <span class="text-green-600 text-xs font-medium flex items-center">
                                            <i class="fas fa-arrow-up mr-1"></i> {{ abs($trend) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-500 text-xs font-medium flex items-center">
                                            <i class="fas fa-minus mr-1"></i> Stable
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        </div>
                        <!-- Upcoming Hearings -->
                        <div class="dashboard-card p-6 lg:col-span-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Upcoming Hearings</p>
                                    <h3 id="upcomingStatCount" class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['upcoming_hearings'] ?? 0 }}</h3>
                                    <p class="text-xs text-gray-500 mt-0.5">[{{ $stats['upcoming_hearings'] ?? 0 }}]</p>
                                </div>
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-calendar-day text-xl"></i>
                                </div>
                            </div>
                            <div id="nextHearingInfo" class="mt-4">
                                @php $nh = $stats['next_hearing'] ?? null; @endphp
                                @if($nh)
                                    <p class="text-sm text-gray-600">Next: <span class="font-medium">{{ $nh['title'] }}</span> ({{ $nh['hearing_date'] }}{{ !empty($nh['hearing_time']) ? ' • '.$nh['hearing_time'] : '' }})</p>
                                @else
                                    <p class="text-sm text-gray-600">No upcoming hearings</p>
                                @endif
                            </div>
                            <div class="mt-4">
                                @php 
                                    $upc = (int) ($stats['upcoming_hearings'] ?? 0);
                                    $upPct = ($stats['total_cases'] > 0) ? min(100, round(($upc / $stats['total_cases']) * 100)) : 0;
                                @endphp
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $upPct }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $upc }} upcoming across {{ $stats['total_cases'] }} cases ({{ $upPct }}%)</p>
                            </div>
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
                                        <div class="text-sm font-medium text-gray-900">{{ $u['title'] }}</div>
                                        <div class="text-xs text-gray-500">{{ $u['code'] }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-900">{{ $u['hearing_date'] ?? '-' }}</div>
                                        @if(!empty($u['hearing_time']))
                                            @php
                                                try { $__ut_disp = \Carbon\Carbon::parse($u['hearing_time'])->format('g:i A'); }
                                                catch (\Exception $e) { $__ut_disp = $u['hearing_time']; }
                                            @endphp
                                            <div class="text-xs text-gray-500">{{ $__ut_disp }}</div>
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
                        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Cases Management</h3>
                            <button id="lockAllCasesBtn" type="button" class="inline-flex items-center px-3 py-1.5 bg-gray-700 text-white rounded-md text-xs hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-600">
                                <i class="bx bx-lock mr-1"></i>
                                Lock All
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Case Number</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contract Type</th>
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
                                            @php
                                                $__ht_raw = $c['hearing_time'] ?? '';
                                                try { $__ht_norm = $__ht_raw ? \Carbon\Carbon::parse($__ht_raw)->format('H:i') : ''; }
                                                catch (\Exception $e) { $__ht_norm = preg_match('/^\d{2}:\d{2}$/', (string)$__ht_raw) ? $__ht_raw : ''; }
                                            @endphp
                                            <tr class="hover:bg-gray-50"
                                                data-number="{{ $c['number'] }}"
                                                data-name="{{ $c['name'] }}"
                                                data-client="{{ $c['client'] }}"
                                                data-type="{{ $typeKey }}"
                                                data-status="{{ $c['status'] }}"
                                                data-hearing-date="{{ $c['hearing_date'] ?? '' }}"
                                                data-hearing-time="{{ $__ht_norm }}"
                                            >
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                                            <i class="bx bx-briefcase text-sm"></i>
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-bold text-gray-900">{{ $c['number'] }}</div>
                                                            <div class="text-xs text-gray-500">Filed: {{ $c['filed'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if(!empty($c['contract_type']))
                                                        @php
                                                            $statusClass = 'bg-gray-100 text-gray-800';
                                                            $statusText = 'Unknown';
                                                            
                                                            // Set status class and text based on contract status
                                                            if (isset($c['contract_status'])) {
                                                                $rawContractStatus = strtolower((string) $c['contract_status']);
                                                                if ($rawContractStatus === 'inactive') $rawContractStatus = 'active';
                                                                $statusMap = [
                                                                    'active' => ['class' => 'bg-green-100 text-green-800', 'text' => 'Active'],
                                                                    'expired' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Expired'],
                                                                    'terminated' => ['class' => 'bg-red-100 text-red-800', 'text' => 'Terminated'],
                                                                    'renewed' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Renewed'],
                                                                    'pending' => ['class' => 'bg-yellow-100 text-yellow-800', 'text' => 'Pending'],
                                                                    'upcoming' => ['class' => 'bg-blue-100 text-blue-800', 'text' => 'Upcoming']
                                                                ];
                                                                
                                                                $statusInfo = $statusMap[$rawContractStatus] ?? ['class' => 'bg-gray-100 text-gray-800', 'text' => ucfirst($rawContractStatus)];
                                                                $statusClass = $statusInfo['class'];
                                                                $statusText = $statusInfo['text'];
                                                            } else {
                                                                $statusText = 'Active';
                                                                $statusClass = 'bg-green-100 text-green-800';
                                                            }
                                                            
                                                            // Get contract type label
                                                            $contractLabel = $c['contract_type_label'] ?? (
                                                                [
                                                                    'employee' => 'Employee Contract',
                                                                    'employment' => 'Employment Agreement',
                                                                    'service' => 'Service Contract',
                                                                    'other' => 'Other Agreement'
                                                                ][$c['contract_type']] ?? 'Contract'
                                                            );
                                                        @endphp
                                                        
                                                        <div class="flex flex-col space-y-1">
                                                            <div class="flex items-center">
                                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusClass }} mr-2">
                                                                    {{ $statusText }}
                                                                </span>
                                                                <span class="text-sm font-medium text-gray-900">
                                                                    {{ $contractLabel }}
                                                                </span>
                                                            </div>
                                                            @if(isset($c['contract_expiration']))
                                                                <div class="text-xs text-gray-500">
                                                                    @if($c['contract_status'] === 'expired')
                                                                        Expired on {{ \Carbon\Carbon::parse($c['contract_expiration'])->format('M d, Y') }}
                                                                    @else
                                                                        Expires on {{ \Carbon\Carbon::parse($c['contract_expiration'])->format('M d, Y') }}
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            No Contract
                                                        </span>
                                                    @endif
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
                                                    @php
                                                        $__ht = $c['hearing_time'] ?? '';
                                                        try { $__ht_disp = $__ht ? \Carbon\Carbon::parse($__ht)->format('g:i A') : ''; }
                                                        catch (\Exception $e) { $__ht_disp = $__ht; }
                                                    @endphp
                                                    <div class="text-xs text-gray-500">{{ $__ht_disp }}</div>
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
                                                       data-hearing-time="{{ (function($t){ try { return $t ? \Carbon\Carbon::parse($t)->format('g:i A') : ''; } catch (\Exception $e) { return $t; } })($c['hearing_time'] ?? '') }}">
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
                                                       data-hearing-time="{{ (function($t){ try { return $t ? \Carbon\Carbon::parse($t)->format('g:i A') : ''; } catch (\Exception $e) { return $t; } })($c['hearing_time'] ?? '') }}">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" class="deleteCaseBtn text-red-600 hover:text-red-800" title="Delete"
                                                       data-number="{{ $c['number'] }}">
                                                        <i class="fas fa-trash"></i>
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
        <div class="bg-white rounded-lg w-full max-w-lg mx-4">
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
                            <label for="title" class="block text-sm font-medium text-gray-700">Case Title *</label>
                            <input type="text" name="title" id="title" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                            <textarea name="description" id="description" required rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm"></textarea>
                        </div>
                        <div>
                            <label for="case_type" class="block text-sm font-medium text-gray-700">Case Type *</label>
                            <select id="case_type" name="case_type" required class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                                <option value="">Select case type</option>
                                <option value="civil">Civil</option>
                                <option value="criminal">Criminal</option>
                                <option value="family">Family Law</option>
                                <option value="corporate">Corporate</option>
                                <option value="contract">Contract</option>
                                <option value="labor">Labor</option>
                            </select>
                        </div>
                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                            <select id="priority" name="priority" required class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                                <option value="">Select priority</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select id="status" name="status" required class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                                <option value="">Select status</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="closed">Closed</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>
                        <div>
                            <label for="hearing_date" class="block text-sm font-medium text-gray-700">Next Hearing Date</label>
                            <input type="date" id="hearing_date" name="hearing_date" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>
                        <div>
                            <label for="hearing_time" class="block text-sm font-medium text-gray-700">Next Hearing Time</label>
                            <input type="time" id="hearing_time" name="hearing_time" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>
                        <div>
                            <label for="client" class="block text-sm font-medium text-gray-700">Client Name *</label>
                            <input type="text" id="client" name="client" required placeholder="Enter client name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm" />
                        </div>
                        
                        <div>
                            <label for="court" class="block text-sm font-medium text-gray-700">Court</label>
                            <input type="text" id="court" name="court" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>
                        <div>
                            <label for="judge" class="block text-sm font-medium text-gray-700">Judge</label>
                            <input type="text" id="judge" name="judge" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                        </div>

                        <div>
                            <label for="contract_type" class="block text-sm font-medium text-gray-700">Contract Type</label>
                            <select id="contract_type" name="contract_type" class="mt-1 block w-full border border-gray-300 bg-white rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-[#2f855A] focus:border-[#2f855A] sm:text-sm">
                                <option value="">Select contract type</option>
                                <option value="employee">Employee Contract</option>
                                <option value="employment">Employment Agreement</option>
                                <option value="service">Service Contract</option>
                                <option value="other">Other Agreement</option>
                            </select>
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
    const modals = {
        profile: document.getElementById("profileModal"),
        accountSettings: document.getElementById("accountSettingsModal"),
        privacySecurity: document.getElementById("privacySecurityModal"),
        signOut: document.getElementById("signOutModal"),
        newCase: document.getElementById("newCaseModal"),
        viewCase: document.getElementById("viewCaseModal"),
        editCase: document.getElementById("editCaseModal"),
        deleteCase: document.getElementById("deleteCaseModal")
    };

    const modalButtons = {
        profile: {
            open: document.getElementById("openProfileBtn"),
            close: [document.getElementById("closeProfileBtn"), document.getElementById("closeProfileBtn2")]
        },
        accountSettings: {
            open: document.getElementById("openAccountSettingsBtn"),
            close: [document.getElementById("closeAccountSettingsBtn"), document.getElementById("cancelAccountSettingsBtn")]
        },
        privacySecurity: {
            open: document.getElementById("openPrivacySecurityBtn"),
            close: [document.getElementById("closePrivacySecurityBtn"), document.getElementById("cancelPrivacySecurityBtn")]
        },
        signOut: {
            open: document.getElementById("signOutBtn"),
            close: [document.getElementById("cancelSignOutBtn"), document.getElementById("cancelSignOutBtn2")]
        },
        viewCase: {
            close: [document.getElementById("closeViewCaseBtn"), document.getElementById("closeViewCaseBtn2")]
        },
        editCase: {
            close: [document.getElementById("closeEditCaseBtn"), document.getElementById("cancelEditCaseBtn")]
        },
        deleteCase: {
            close: [document.getElementById("closeDeleteCaseBtn"), document.getElementById("cancelDeleteCaseBtn")]
        }
    };

    // View Case Modal fields
    const vcFields = {
        number: document.getElementById("vcNumber"),
        status: document.getElementById("vcStatus"),
        name: document.getElementById("vcName"),
        client: document.getElementById("vcClient"),
        type: document.getElementById("vcType"),
        hearing: document.getElementById("vcHearing"),
        contractType: document.getElementById("view-contract-type"),
        contractStatus: document.getElementById("view-contract-status"),
        contractStart: document.getElementById("view-contract-start"),
        contractExpiration: document.getElementById("view-contract-expiration"),
        contractNotes: document.getElementById("view-contract-notes")
    };

    // Edit Case Modal fields
    const ecFields = {
        number: document.getElementById("ecNumber"),
        status: document.getElementById("ecStatus"),
        name: document.getElementById("ecName"),
        client: document.getElementById("ecClient"),
        type: document.getElementById("ecType"),
        hearingDate: document.getElementById("ecHearingDate"),
        hearingTime: document.getElementById("ecHearingTime")
    };

    // Toggle sidebar
    function toggleSidebar() {
        const isOpen = sidebar.classList.contains("md:ml-0") || sidebar.classList.contains("ml-0");
        if (window.innerWidth >= 768) {
            sidebar.classList.toggle("-ml-72");
            sidebar.classList.toggle("md:ml-0");
            mainContent.classList.toggle("sidebar-closed");
        } else {
            sidebar.classList.toggle("-ml-72");
            sidebar.classList.toggle("ml-0");
            overlay.classList.toggle("hidden");
            document.body.classList.toggle("overflow-hidden");
        }
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

    // Close all modals
    function closeAllModals() {
        Object.values(modals).forEach(modal => {
            if (modal) {
                modal.classList.remove("active");
                modal.classList.add("hidden");
                modal.style.display = "none";
                document.body.style.overflow = "auto";
            }
        });
        if (modals.newCase) {
            const form = document.getElementById("newCaseForm");
            if (form) form.reset();
        }
        if (modals.editCase) {
            const form = document.getElementById("editCaseForm");
            if (form) form.reset();
        }
    }

    // Handle sidebar dropdown toggles
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener("click", (e) => {
            e.stopPropagation();
            const dropdown = toggle.nextElementSibling;
            const chevron = toggle.querySelector(".bx-chevron-down");
            const isOpen = !dropdown.classList.contains("hidden");
            closeAllDropdowns(toggle);
            dropdown.classList.toggle("hidden");
            chevron.classList.toggle("rotate-180");
            toggle.setAttribute("aria-expanded", !isOpen);
        });
    });

    // Handle sidebar toggle
    if (toggleBtn) {
        toggleBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    // Handle overlay click
    if (overlay) {
        overlay.addEventListener("click", () => {
            sidebar.classList.add("-ml-72");
            sidebar.classList.remove("ml-0", "md:ml-0");
            overlay.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");
            mainContent.classList.add("sidebar-closed");
            closeAllDropdowns();
            const icon = toggleBtn.querySelector("i");
            icon.classList.add("fa-bars");
            icon.classList.remove("fa-times");
            toggleBtn.setAttribute("aria-expanded", "false");
        });
    }

    // Handle window resize
    function handleResize() {
        if (window.innerWidth >= 768) {
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

    window.addEventListener("resize", handleResize);
    handleResize();

    // Close dropdowns and sidebar on outside click
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
        if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
            notificationDropdown.classList.add("hidden");
            notificationBtn.setAttribute("aria-expanded", "false");
        }
        if (!userMenuBtn.contains(e.target) && !userMenuDropdown.contains(e.target)) {
            userMenuDropdown.classList.add("hidden");
            userMenuBtn.setAttribute("aria-expanded", "false");
        }
    });

    // Close modals on outside click
    document.addEventListener("click", (e) => {
        if (e.target.classList.contains("modal")) {
            closeAllModals();
        }
    });

    // Close modals on Escape key
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeAllModals();
            closeAllDropdowns();
            window.hideAllMenus();
        }
    });

    // Modal button event listeners
    Object.entries(modalButtons).forEach(([key, { open, close }]) => {
        if (open) {
            open.addEventListener("click", (e) => {
                e.stopPropagation();
                closeAllModals();
                if (modals[key]) {
                    modals[key].classList.remove("hidden");
                    modals[key].classList.add("active");
                    modals[key].style.display = "flex";
                    document.body.style.overflow = "hidden";
                    userMenuDropdown.classList.add("hidden");
                    userMenuBtn.setAttribute("aria-expanded", "false");
                }
            });
        }
        if (close) {
            close.forEach(btn => {
                if (btn) {
                    btn.addEventListener("click", () => {
                        closeAllModals();
                    });
                }
            });
        }
    });

    // View Case Modal handler
    function openViewCaseModal(btn) {
        if (!btn || !modals.viewCase) return;
        const d = btn.dataset || {};
        const modal = modals.viewCase;
        
        // Update contract information
        const contractType = document.getElementById('view-contract-type');
        const contractStatus = document.getElementById('view-contract-status');
        const contractStart = document.getElementById('view-contract-start');
        const contractExpiration = document.getElementById('view-contract-expiration');
        const contractNotes = document.getElementById('view-contract-notes');
        
        if (d.contractType) {
            contractType.textContent = d.contractTypeLabel || 'N/A';
            
            // Update status badge
            const statusClass = d.contractStatus === 'Expired' ? 
                'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
            contractStatus.innerHTML = `
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                    ${d.contractStatus || 'N/A'}
                </span>
            `;
            
            // Format dates
            contractStart.textContent = d.contractDate ? new Date(d.contractDate).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            }) : '-';
            
            contractExpiration.textContent = d.contractExpiration ? new Date(d.contractExpiration).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            }) : '-';
            
            // Show days until expiration if not expired
            if (d.contractExpiration && d.contractStatus !== 'Expired') {
                const expDate = new Date(d.contractExpiration);
                const today = new Date();
                const diffTime = expDate - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                contractExpiration.textContent += ` (${diffDays} days remaining)`;
            }
            
            contractNotes.textContent = d.contractNotes || '-';
        } else {
            contractType.textContent = '-';
            contractStatus.innerHTML = `
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                    No Contract
                </span>
            `;
            contractStart.textContent = '-';
            contractExpiration.textContent = '-';
            contractNotes.textContent = '-';
        }
        vcFields.number && (vcFields.number.textContent = d.number || '—');
        vcFields.status && (vcFields.status.textContent = d.status || '—');
        vcFields.name && (vcFields.name.textContent = d.name || '—');
        vcFields.client && (vcFields.client.textContent = d.client || '—');
        vcFields.type && (vcFields.type.textContent = (d.typeLabel || d.type || '—').split('_').join(' '));
        vcFields.hearing && (vcFields.hearing.textContent = (d.hearingDate ? d.hearingDate : '—') + (d.hearingTime ? ' • ' + d.hearingTime : ''));
        closeAllModals();
        modals.viewCase.classList.remove("hidden");
        modals.viewCase.classList.add("active");
        modals.viewCase.style.display = "flex";
        document.body.style.overflow = "hidden";
    }

    // Helper: set <select> value by matching value or visible text (case-insensitive)
    function setSelectByTextOrValue(sel, val){
        if (!sel) return;
        const target = (val || '').toString().trim();
        if (!target){ sel.value = ''; return; }
        // Try direct value match first
        sel.value = target;
        if (sel.value === target) return;
        // Fallback: match by option text (case-insensitive)
        const tLower = target.toLowerCase();
        for (const opt of sel.options){
            if ((opt.text || '').toLowerCase() === tLower){ sel.value = opt.value; return; }
        }
        // Last resort: case-insensitive value match
        for (const opt of sel.options){
            if ((opt.value || '').toLowerCase() === tLower){ sel.value = opt.value; return; }
        }
        // If no match, clear
        sel.value = '';
    }

    // Edit Case Modal handler
    function openEditCaseModal(btn) {
        if (!btn || !modals.editCase) return;
        const d = btn.dataset || {};
        // Prefer row-level data attributes for robustness
        const tr = btn.closest('tr');
        const rd = tr ? tr.dataset || {} : {};
        function txt(el){ return (el ? el.textContent : '').trim(); }
        function byIdx(i){ return tr && tr.querySelectorAll('td')[i] || null; }
        // Columns (0-based): 0 Number, 1 Name, 2 Client, 3 Type, 4 Status, 5 Hearing
        const colNumber = byIdx(0);
        const colName = byIdx(1);
        const colClient = byIdx(2);
        const colType = byIdx(3);
        const colStatus = byIdx(4);
        const colHearing = byIdx(5);

        const numberVal = rd.number || d.number || txt(colNumber && colNumber.querySelector('.text-sm.font-medium'));
        const nameVal = rd.name || d.name || txt(colName && colName.querySelector('.text-sm.font-medium'));
        const clientVal = rd.client || d.client || txt(colClient && colClient.querySelector('.text-sm.font-medium'));
        const typeVal = (rd.type || d.type || (txt(colType))).toString().toLowerCase();
        const statusVal = rd.status || d.status || txt(colStatus && colStatus.querySelector('span'));
        let hearingDateVal = rd.hearingDate || d.hearingDate || '';
        let hearingTimeVal = rd.hearingTime || d.hearingTime || '';
        if (!hearingDateVal || !hearingTimeVal) {
            if (colHearing){
                const dateEl = colHearing.querySelector('.text-sm');
                const timeEl = colHearing.querySelector('.text-xs');
                hearingDateVal = hearingDateVal || txt(dateEl);
                hearingTimeVal = hearingTimeVal || txt(timeEl);
            }
        }

        if (ecFields.number) ecFields.number.value = numberVal || '';
        if (ecFields.status) setSelectByTextOrValue(ecFields.status, statusVal || '');
        if (ecFields.name) ecFields.name.value = nameVal || '';
        if (ecFields.client) {
            setSelectByTextOrValue(ecFields.client, clientVal || '');
            if (ecFields.client.value === '' && clientVal) {
                const opt = document.createElement('option');
                opt.value = clientVal;
                opt.text = clientVal;
                ecFields.client.appendChild(opt);
                ecFields.client.value = clientVal;
            }
        }
        if (ecFields.type) {
            setSelectByTextOrValue(ecFields.type, typeVal || '');
            if (ecFields.type.value === '' && typeVal) {
                const known = ['civil','criminal','family','corporate','contract','ip'];
                const guess = known.includes(typeVal) ? typeVal : 'civil';
                ecFields.type.value = guess;
            }
        }
        if (ecFields.hearingDate) ecFields.hearingDate.value = hearingDateVal || '';
        // Convert 12-hour time (e.g., "1:05 PM") from data-hearing-time into 24-hour HH:MM for <input type="time">
        if (ecFields.hearingTime) {
            let ht = hearingTimeVal || '';
            // If already HH:MM or HH:MM:SS, use first 5 chars
            const hhmmMatch = /^\d{2}:\d{2}(:\d{2})?$/.test(ht);
            if (hhmmMatch) {
                ecFields.hearingTime.value = ht.substring(0,5);
            } else if (ht) {
                // Parse formats like "h:mm AM/PM" or "hh:mm AM/PM"
                const m = ht.match(/^(\d{1,2}):(\d{2})\s*([AaPp][Mm])$/);
                if (m) {
                    let h = parseInt(m[1],10);
                    const min = m[2];
                    const ampm = m[3].toUpperCase();
                    if (ampm === 'PM' && h !== 12) h += 12;
                    if (ampm === 'AM' && h === 12) h = 0;
                    const hh = String(h).padStart(2,'0');
                    ecFields.hearingTime.value = `${hh}:${min}`;
                } else {
                    // Fallback: leave empty if unrecognized
                    ecFields.hearingTime.value = '';
                }
            } else {
                ecFields.hearingTime.value = '';
            }
        }
        closeAllModals();
        modals.editCase.classList.remove("hidden");
        modals.editCase.classList.add("active");
        modals.editCase.style.display = "flex";
        document.body.style.overflow = "hidden";
    }

    // Submit handler: Edit Case form (AJAX -> route('case.update'))
    (function(){
        const form = document.getElementById('editCaseForm');
        if (!form) return;
        form.addEventListener('submit', async function(e){
            e.preventDefault();
            let submitBtn = form.querySelector('button[type="submit"]');
            let original = submitBtn ? submitBtn.innerHTML : '';
            try{
                if (submitBtn){ submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...'; }

                // Map UI fields to backend expected keys
                const payload = {
                    number: (ecFields.number?.value || '').trim(),
                    case_name: (ecFields.name?.value || '').trim(),
                    client_name: (ecFields.client?.value || '').trim(),
                    case_type: (ecFields.type?.value || '').trim(),
                    status: (ecFields.status?.value || '').trim(),
                    hearing_date: (ecFields.hearingDate?.value || '').trim() || null,
                    hearing_time: (ecFields.hearingTime?.value || '').trim() || null,
                };

                // Basic validation
                if (!payload.title){ throw new Error('Title is required.'); }
                if (!payload.description){ throw new Error('Description is required.'); }
                if (!payload.case_type){ throw new Error('Case type is required.'); }
                if (!payload.priority){ throw new Error('Priority is required.'); }
                if (!payload.status){ throw new Error('Status is required.'); }

                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
                const fd = new FormData();
                fd.append('title', payload.title);
                fd.append('description', payload.description);
                fd.append('case_type', payload.case_type);
                fd.append('priority', payload.priority);
                fd.append('status', payload.status);
                fd.append('hearing_date', payload.hearing_date || '');
                fd.append('hearing_time', payload.hearing_time || '');
                if (payload.hearing_date) fd.append('hearing_date', payload.hearing_date);
                if (payload.hearing_time) fd.append('hearing_time', payload.hearing_time);
                fd.append('_token', csrf);

                const res = await fetch('{{ route("case.update") }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: fd
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false){
                    throw new Error((data && (data.message || data.error)) || 'Failed to update case');
                }

                await Swal.fire({ icon: 'success', title: 'Saved', text: 'Case has been updated.', showConfirmButton: false, timer: 1200 });
                closeAllModals();
                // Refresh to reflect updates in the table and stats
                window.location.reload();
            }catch(err){
                console.error('Update failed:', err);
                Swal.fire({ icon: 'error', title: 'Error', text: (err && err.message) || 'Failed to update case. Please try again.', confirmButtonColor: '#2f855a' });
            }finally{
                submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn){ submitBtn.disabled = false; submitBtn.innerHTML = original || 'Save'; }
            }
        });
    })();

    // Delete Case Modal handler
    function openDeleteCaseModal(btn){
        if (!btn || !modals.deleteCase) return;
        const tr = btn.closest('tr');
        const rd = tr ? (tr.dataset || {}) : {};
        const number = rd.number || btn.dataset.number || '';
        const txtEl = document.getElementById('delCaseNumberText');
        if (txtEl) txtEl.textContent = number || '—';
        // store pending number on confirm button dataset
        const confirmBtn = document.getElementById('confirmDeleteCaseBtn');
        if (confirmBtn) confirmBtn.dataset.number = number || '';
        closeAllModals();
        modals.deleteCase.classList.remove('hidden');
        modals.deleteCase.classList.add('active');
        modals.deleteCase.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    // Bind confirm delete (ensure single binding)
    (function(){
        const confirmBtn = document.getElementById('confirmDeleteCaseBtn');
        if (!confirmBtn || confirmBtn.__bound) return; // prevent double-binding
        confirmBtn.__bound = true;
        confirmBtn.addEventListener('click', async function(){
            const number = (confirmBtn.dataset.number || '').trim();
            if (!number){
                Swal.fire({ icon: 'error', title: 'Error', text: 'Missing case number to delete.' });
                return;
            }
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
            const fd = new FormData();
            fd.append('number', number);
            fd.append('_token', csrf);
            // Loading state
            const original = confirmBtn.innerHTML;
            confirmBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Deleting...';
            try{
                const res = await fetch('{{ route("case.delete") }}', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: fd
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || data.success === false){
                    throw new Error((data && (data.message || data.error)) || 'Failed to delete case');
                }
                await Swal.fire({ icon: 'success', title: 'Deleted', text: 'Case has been deleted.', showConfirmButton: false, timer: 1200 });
                closeAllModals();
                window.location.reload();
            }catch(err){
                console.error('Delete failed:', err);
                Swal.fire({ icon: 'error', title: 'Error', text: (err && err.message) || 'Failed to delete case. Please try again.' });
            }finally{
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = original;
            }
        });
    })();

    // Event delegation for view and edit case buttons
    document.addEventListener("click", (e) => {
        const viewBtn = e.target.closest(".viewCaseBtn");
        const editBtn = e.target.closest(".editCaseBtn");
        const delBtn = e.target.closest(".deleteCaseBtn");
        if (viewBtn) {
            e.preventDefault();
            openViewCaseModal(viewBtn);
        } else if (editBtn) {
            e.preventDefault();
            openEditCaseModal(editBtn);
        } else if (delBtn) {
            e.preventDefault();
            openDeleteCaseModal(delBtn);
        }
    });

    // Gate sidebar 'Legal Management' links behind OTP
    (function(){
        try{
            const sections = document.querySelectorAll('#sidebar .has-dropdown');
            sections.forEach(function(sec){
                const label = sec.querySelector('div > div > span');
                if (!label) return;
                const name = (label.textContent || '').trim().toLowerCase();
                if (name !== 'legal management') return;
                const links = sec.querySelectorAll('.dropdown-menu a[href]');
                links.forEach(function(a){
                    const txt = (a.textContent || '').trim().toLowerCase();
                    if (txt !== 'case management') return;
                    if (a.__otpWired) return; a.__otpWired = true;
                    a.addEventListener('click', function(ev){
                        ev.preventDefault();
                        const href = a.getAttribute('href');
                        window.location.href = href;
                    });
                });
            });
        }catch(e){}
    })();

    // Case lock synchronization functionality
    const lockAllCasesBtn = document.getElementById('lockAllCasesBtn');
    
    // Lock all cases function
    window.lockAllCases = function() {
        const tableRows = document.querySelectorAll('#casesTbody tr');
        const upcomingListItems = document.querySelectorAll('#upcomingList li');
        
        tableRows.forEach(row => {
            // Store original data if not already stored
            if (!row.dataset.originalData) {
                const caseNumber = row.querySelector('td:nth-child(1) .text-sm');
                const contractType = row.querySelector('td:nth-child(2) .text-sm');
                const caseName = row.querySelector('td:nth-child(3) .text-sm');
                const client = row.querySelector('td:nth-child(4) .text-sm');
                const type = row.querySelector('td:nth-child(5) span');
                const status = row.querySelector('td:nth-child(6) span');
                const hearing = row.querySelector('td:nth-child(7) .text-sm');
                const viewButton = row.querySelector('.viewCaseBtn');
                const editButton = row.querySelector('.editCaseBtn');
                const deleteButton = row.querySelector('.deleteCaseBtn');
                
                row.dataset.originalData = JSON.stringify({
                    caseNumber: caseNumber?.textContent || '',
                    contractType: contractType?.textContent || '',
                    caseName: caseName?.textContent || '',
                    client: client?.textContent || '',
                    type: type?.textContent || '',
                    status: status?.textContent || '',
                    hearing: hearing?.textContent || '',
                    statusClass: status?.className || ''
                });
            }
            
            // Mask the data
            const originalData = JSON.parse(row.dataset.originalData);
            const caseNumber = row.querySelector('td:nth-child(1) .text-sm');
            const contractType = row.querySelector('td:nth-child(2) .text-sm');
            const caseName = row.querySelector('td:nth-child(3) .text-sm');
            const client = row.querySelector('td:nth-child(4) .text-sm');
            const type = row.querySelector('td:nth-child(5) span');
            const status = row.querySelector('td:nth-child(6) span');
            const hearing = row.querySelector('td:nth-child(7) .text-sm');
            const viewButton = row.querySelector('.viewCaseBtn');
            const editButton = row.querySelector('.editCaseBtn');
            const deleteButton = row.querySelector('.deleteCaseBtn');
            
            if (caseNumber) {
                caseNumber.innerHTML = '**** <i class="fas fa-lock text-red-500 text-xs ml-1"></i>';
            }
            if (contractType) {
                contractType.textContent = '****';
            }
            if (caseName) {
                caseName.textContent = '****';
            }
            if (client) {
                client.textContent = '****';
            }
            if (type) {
                type.textContent = '****';
                type.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
            }
            if (status) {
                status.textContent = '****';
                status.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
            }
            if (hearing) {
                hearing.textContent = '** ** ****';
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
        });
        
        // Lock upcoming hearings list items
        upcomingListItems.forEach(item => {
            // Store original content if not already stored
            if (!item.dataset.originalContent) {
                item.dataset.originalContent = item.innerHTML;
            }
            
            // Add lock styling to list item
            item.style.opacity = '0.7';
            item.classList.add('locked-row');
            // Add lock icon to indicate locked state
            if (!item.querySelector('.lock-icon')) {
                const lockIcon = document.createElement('i');
                lockIcon.className = 'fas fa-lock text-gray-400 text-xs mr-2 lock-icon';
                const titleDiv = item.querySelector('.text-sm.font-medium');
                if (titleDiv) {
                    titleDiv.insertBefore(lockIcon, titleDiv.firstChild);
                }
            }
        });
        
        // Save lock state to localStorage
        localStorage.setItem('casesLocked', 'true');
        console.log('Set localStorage casesLocked to true');
        
        // Trigger storage event manually for cross-tab sync
        window.dispatchEvent(new StorageEvent('storage', {
            key: 'casesLocked',
            newValue: 'true',
            oldValue: 'false'
        }));
        
        // Update button
        if (lockAllCasesBtn) {
            lockAllCasesBtn.innerHTML = '<i class="bx bx-lock-open mr-1"></i>Unlock All';
            lockAllCasesBtn.classList.remove('bg-gray-700', 'hover:bg-gray-800');
            lockAllCasesBtn.classList.add('bg-green-600', 'hover:bg-green-700');
        }
    };

    // Unlock all cases function
    window.unlockAllCases = function() {
        const tableRows = document.querySelectorAll('#casesTbody tr');
        const upcomingListItems = document.querySelectorAll('#upcomingList li');
        
        tableRows.forEach(row => {
            if (row.dataset.originalData) {
                try {
                    const originalData = JSON.parse(row.dataset.originalData);
                    
                    const caseNumber = row.querySelector('td:nth-child(1) .text-sm');
                    const contractType = row.querySelector('td:nth-child(2) .text-sm');
                    const caseName = row.querySelector('td:nth-child(3) .text-sm');
                    const client = row.querySelector('td:nth-child(4) .text-sm');
                    const type = row.querySelector('td:nth-child(5) span');
                    const status = row.querySelector('td:nth-child(6) span');
                    const hearing = row.querySelector('td:nth-child(7) .text-sm');
                    const viewButton = row.querySelector('.viewCaseBtn');
                    const editButton = row.querySelector('.editCaseBtn');
                    const deleteButton = row.querySelector('.deleteCaseBtn');
                    
                    if (caseNumber) {
                        caseNumber.textContent = originalData.caseNumber;
                    }
                    if (contractType) {
                        contractType.textContent = originalData.contractType;
                    }
                    if (caseName) {
                        caseName.textContent = originalData.caseName;
                    }
                    if (client) {
                        client.textContent = originalData.client;
                    }
                    if (type) {
                        type.textContent = originalData.type;
                        type.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800';
                    }
                    if (status) {
                        status.textContent = originalData.status;
                        status.className = originalData.statusClass;
                    }
                    if (hearing) {
                        hearing.textContent = originalData.hearing;
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
                } catch (e) {
                    console.error('Error restoring original data:', e);
                }
            }
        });
        
        // Unlock upcoming hearings list items
        upcomingListItems.forEach(item => {
            // Restore original content if available
            if (item.dataset.originalContent) {
                item.innerHTML = item.dataset.originalContent;
            }
            
            // Remove lock styling from list item
            item.style.opacity = '1';
            item.classList.remove('locked-row');
        });
        
        // Save unlock state to localStorage
        localStorage.setItem('casesLocked', 'false');
        console.log('Set localStorage casesLocked to false');
        
        // Trigger storage event manually for cross-tab sync
        window.dispatchEvent(new StorageEvent('storage', {
            key: 'casesLocked',
            newValue: 'false',
            oldValue: 'true'
        }));
        
        // Update button
        if (lockAllCasesBtn) {
            lockAllCasesBtn.innerHTML = '<i class="bx bx-lock mr-1"></i>Lock All';
            lockAllCasesBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            lockAllCasesBtn.classList.add('bg-gray-700', 'hover:bg-gray-800');
        }
    };

    // Lock button click handler
    if (lockAllCasesBtn) {
        lockAllCasesBtn.addEventListener('click', () => {
            const isLocked = localStorage.getItem('casesLocked') === 'true';
            
            if (isLocked) {
                // Currently locked, so unlock with OTP validation
                if (typeof window.unlockAllCases === 'function') {
                    // Check if there are any cases to unlock
                    const tableRows = document.querySelectorAll('#casesTbody tr');
                    if (tableRows.length === 0) {
                        if (window.Swal && Swal.fire) {
                            Swal.fire({
                                icon: 'info',
                                title: 'No Cases Found',
                                text: 'There are no cases to unlock.',
                                confirmButtonColor: '#2f855a'
                            });
                        } else {
                            alert('No cases found to unlock.');
                        }
                        return;
                    }
                    
                    // Check if any rows are actually locked
                    let lockedRowsCount = 0;
                    tableRows.forEach(row => {
                        if (row.classList.contains('locked-row')) {
                            lockedRowsCount++;
                        }
                    });
                    
                    if (lockedRowsCount === 0) {
                        // Cases appear unlocked but localStorage says locked - sync the state
                        localStorage.setItem('casesLocked', 'false');
                        // Update button to show "Lock All" state
                        if (lockAllCasesBtn) {
                            lockAllCasesBtn.innerHTML = '<i class="bx bx-lock mr-1"></i>Lock All';
                            lockAllCasesBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                            lockAllCasesBtn.classList.add('bg-gray-700', 'hover:bg-gray-800');
                        }
                        if (window.Swal && Swal.fire) {
                            Swal.fire({
                                icon: 'info',
                                title: 'State Synced',
                                text: 'Cases are already unlocked. State has been synchronized.',
                                confirmButtonColor: '#2f855a'
                            });
                        } else {
                            alert('Cases are already unlocked. State has been synchronized.');
                        }
                        return;
                    }
                    
                    // Unlock all cases directly
                    window.unlockAllCases();
                    if (window.Swal && Swal.fire) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Unlocked',
                            text: `${lockedRowsCount} case(s) have been unlocked.`,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            } else {
                // Currently unlocked, so lock with validation and confirmation
                if (typeof window.lockAllCases === 'function') {
                    // Check if there are any cases to lock
                    const tableRows = document.querySelectorAll('#casesTbody tr');
                    if (tableRows.length === 0) {
                        if (window.Swal && Swal.fire) {
                            Swal.fire({
                                icon: 'info',
                                title: 'No Cases Found',
                                text: 'There are no cases to lock.',
                                confirmButtonColor: '#2f855a'
                            });
                        } else {
                            alert('No cases found to lock.');
                        }
                        return;
                    }
                    
                    // Check if any rows are already locked
                    let unlockedRowsCount = 0;
                    tableRows.forEach(row => {
                        if (!row.classList.contains('locked-row')) {
                            unlockedRowsCount++;
                        }
                    });
                    
                    if (unlockedRowsCount === 0) {
                        // Cases appear locked but localStorage says unlocked - sync the state
                        localStorage.setItem('casesLocked', 'true');
                        // Update button to show "Unlock All" state
                        if (lockAllCasesBtn) {
                            lockAllCasesBtn.innerHTML = '<i class="bx bx-lock-open mr-1"></i>Unlock All';
                            lockAllCasesBtn.classList.remove('bg-gray-700', 'hover:bg-gray-800');
                            lockAllCasesBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                        }
                        if (window.Swal && Swal.fire) {
                            Swal.fire({
                                icon: 'info',
                                title: 'State Synced',
                                text: 'Cases are already locked. State has been synchronized.',
                                confirmButtonColor: '#2f855a'
                            });
                        } else {
                            alert('Cases are already locked. State has been synchronized.');
                        }
                        return;
                    }
                    
                    // Show confirmation dialog
                    if (window.Swal && Swal.fire) {
                        Swal.fire({
                            title: 'Lock All Cases?',
                            text: `Are you sure you want to lock ${unlockedRowsCount} case(s) for confidentiality? This will mask all sensitive data.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Lock',
                            cancelButtonText: 'No, Cancel'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.lockAllCases();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Locked',
                                    text: `${unlockedRowsCount} case(s) have been locked for confidentiality.`,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        });
                    } else {
                        // Fallback to browser confirm
                        const confirmed = confirm(`Are you sure you want to lock ${unlockedRowsCount} case(s) for confidentiality? This will mask all sensitive data.`);
                        if (confirmed) {
                            window.lockAllCases();
                            alert(`${unlockedRowsCount} case(s) have been locked for confidentiality.`);
                        }
                    }
                }
            }
        });
    }

    // Initialize lock state on page load
    function initializeCaseLockState() {
        const isLocked = localStorage.getItem('casesLocked') === 'true';
        console.log('Initializing lock state. isLocked:', isLocked);
        
        if (isLocked) {
            // Apply lock state without confirmation
            const tableRows = document.querySelectorAll('#casesTbody tr');
            console.log('Found table rows:', tableRows.length);
            
            tableRows.forEach((row, index) => {
                console.log(`Processing row ${index}:`, row);
                
                // Store original data if not already stored
                if (!row.dataset.originalData) {
                    const caseNumber = row.querySelector('td:nth-child(1) .text-sm');
                    const contractType = row.querySelector('td:nth-child(2) .text-sm');
                    const caseName = row.querySelector('td:nth-child(3) .text-sm');
                    const client = row.querySelector('td:nth-child(4) .text-sm');
                    const type = row.querySelector('td:nth-child(5) span');
                    const status = row.querySelector('td:nth-child(6) span');
                    const hearing = row.querySelector('td:nth-child(7) .text-sm');
                    const viewButton = row.querySelector('.viewCaseBtn');
                    const editButton = row.querySelector('.editCaseBtn');
                    const deleteButton = row.querySelector('.deleteCaseBtn');
                    
                    row.dataset.originalData = JSON.stringify({
                        caseNumber: caseNumber?.textContent || '',
                        contractType: contractType?.textContent || '',
                        caseName: caseName?.textContent || '',
                        client: client?.textContent || '',
                        type: type?.textContent || '',
                        status: status?.textContent || '',
                        hearing: hearing?.textContent || '',
                        statusClass: status?.className || ''
                    });
                }
                
                // Apply masking
                const originalData = JSON.parse(row.dataset.originalData);
                const caseNumber = row.querySelector('td:nth-child(1) .text-sm');
                const contractType = row.querySelector('td:nth-child(2) .text-sm');
                const caseName = row.querySelector('td:nth-child(3) .text-sm');
                const client = row.querySelector('td:nth-child(4) .text-sm');
                const type = row.querySelector('td:nth-child(5) span');
                const status = row.querySelector('td:nth-child(6) span');
                const hearing = row.querySelector('td:nth-child(7) .text-sm');
                const viewButton = row.querySelector('.viewCaseBtn');
                const editButton = row.querySelector('.editCaseBtn');
                const deleteButton = row.querySelector('.deleteCaseBtn');
                
                if (caseNumber) {
                    caseNumber.innerHTML = '**** <i class="fas fa-lock text-red-500 text-xs ml-1"></i>';
                }
                if (contractType) {
                    contractType.textContent = '****';
                }
                if (caseName) {
                    caseName.textContent = '****';
                }
                if (client) {
                    client.textContent = '****';
                }
                if (type) {
                    type.textContent = '****';
                    type.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
                }
                if (status) {
                    status.textContent = '****';
                    status.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
                }
                if (hearing) {
                    hearing.textContent = '** ** ****';
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
            });
            
            // Update button to show "Unlock All" state
            if (lockAllCasesBtn) {
                lockAllCasesBtn.innerHTML = '<i class="bx bx-lock-open mr-1"></i>Unlock All';
                lockAllCasesBtn.classList.remove('bg-gray-700', 'hover:bg-gray-800');
                lockAllCasesBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                console.log('Updated button to Unlock All');
            }
        } else {
            // Apply unlock state on page load
            const tableRows = document.querySelectorAll('#casesTbody tr');
            console.log('Applying unlock state. Found rows:', tableRows.length);
            
            tableRows.forEach((row, index) => {
                // Ensure data is stored for potential future locking
                if (!row.dataset.originalData) {
                    const caseNumber = row.querySelector('td:nth-child(1) .text-sm');
                    const contractType = row.querySelector('td:nth-child(2) .text-sm');
                    const caseName = row.querySelector('td:nth-child(3) .text-sm');
                    const client = row.querySelector('td:nth-child(4) .text-sm');
                    const type = row.querySelector('td:nth-child(5) span');
                    const status = row.querySelector('td:nth-child(6) span');
                    const hearing = row.querySelector('td:nth-child(7) .text-sm');
                    
                    row.dataset.originalData = JSON.stringify({
                        caseNumber: caseNumber?.textContent || '',
                        contractType: contractType?.textContent || '',
                        caseName: caseName?.textContent || '',
                        client: client?.textContent || '',
                        type: type?.textContent || '',
                        status: status?.textContent || '',
                        hearing: hearing?.textContent || '',
                        statusClass: status?.className || ''
                    });
                }
                
                // Ensure rows are unlocked
                const viewButton = row.querySelector('.viewCaseBtn');
                const editButton = row.querySelector('.editCaseBtn');
                const deleteButton = row.querySelector('.deleteCaseBtn');
                
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
            });
            
            // Update button to show "Lock All" state
            if (lockAllCasesBtn) {
                lockAllCasesBtn.innerHTML = '<i class="bx bx-lock mr-1"></i>Lock All';
                lockAllCasesBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                lockAllCasesBtn.classList.add('bg-gray-700', 'hover:bg-gray-800');
                console.log('Updated button to Lock All');
            }
        }
    }

    // Listen for storage changes (for cross-tab synchronization)
    window.addEventListener('storage', (e) => {
        if (e.key === 'casesLocked') {
            const isLocked = e.newValue === 'true';
            if (isLocked) {
                if (typeof window.lockAllCases === 'function') {
                    // Apply lock without confirmation
                    const tableRows = document.querySelectorAll('#casesTbody tr');
                    
                    tableRows.forEach(row => {
                        // Store original data if not already stored
                        if (!row.dataset.originalData) {
                            const caseNumber = row.querySelector('td:nth-child(1) .text-sm');
                            const contractType = row.querySelector('td:nth-child(2) .text-sm');
                            const caseName = row.querySelector('td:nth-child(3) .text-sm');
                            const client = row.querySelector('td:nth-child(4) .text-sm');
                            const type = row.querySelector('td:nth-child(5) span');
                            const status = row.querySelector('td:nth-child(6) span');
                            const hearing = row.querySelector('td:nth-child(7) .text-sm');
                            const viewButton = row.querySelector('.viewCaseBtn');
                            const editButton = row.querySelector('.editCaseBtn');
                            const deleteButton = row.querySelector('.deleteCaseBtn');
                            
                            row.dataset.originalData = JSON.stringify({
                                caseNumber: caseNumber?.textContent || '',
                                contractType: contractType?.textContent || '',
                                caseName: caseName?.textContent || '',
                                client: client?.textContent || '',
                                type: type?.textContent || '',
                                status: status?.textContent || '',
                                hearing: hearing?.textContent || '',
                                statusClass: status?.className || ''
                            });
                        }
                        
                        // Apply masking
                        if (caseNumber) {
                            caseNumber.innerHTML = '**** <i class="fas fa-lock text-red-500 text-xs ml-1"></i>';
                        }
                        if (contractType) {
                            contractType.textContent = '****';
                        }
                        if (caseName) {
                            caseName.textContent = '****';
                        }
                        if (client) {
                            client.textContent = '****';
                        }
                        if (type) {
                            type.textContent = '****';
                            type.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
                        }
                        if (status) {
                            status.textContent = '****';
                            status.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800';
                        }
                        if (hearing) {
                            hearing.textContent = '** ** ****';
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
                    });
                    
                    // Update button
                    if (lockAllCasesBtn) {
                        lockAllCasesBtn.innerHTML = '<i class="bx bx-lock-open mr-1"></i>Unlock All';
                        lockAllCasesBtn.classList.remove('bg-gray-700', 'hover:bg-gray-800');
                        lockAllCasesBtn.classList.add('bg-green-600', 'hover:bg-green-700');
                    }
                }
            } else {
                if (typeof window.unlockAllCases === 'function') {
                    window.unlockAllCases();
                }
            }
        }
    });

    // Initialize lock state when DOM is ready (call at the end of DOMContentLoaded)
    initializeCaseLockState();
});
</script>
    <!-- View Case Modal -->
    <div id="viewCaseModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="view-case-title">
        <div class="bg-white rounded-lg w-full max-w-3xl mx-4">
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
                        <select id="ecClient" name="client" class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                            <option value="">Select client</option>
                            @foreach($clients ?? [] as $client)
                                <option value="{{ $client->name }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1" for="ecType">Type</label>
                        <select id="ecType" name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#2f855A]">
                            <option value="civil">Civil</option>
                            <option value="criminal">Criminal</option>
                            <option value="family">Family</option>
                            <option value="corporate">Corporate</option>
                            <option value="contract">Contract</option>
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
