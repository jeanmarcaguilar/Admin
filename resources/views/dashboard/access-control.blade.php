@php
// Get the authenticated user
$user = auth()->user();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Access Control & Permissions | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('golden-arc.png') }}?v={{ @filemtime(public_path('golden-arc.png')) }}">
    <style>
        /* Modal Styles */
        .modal {
            display: none; /* Start hidden */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .modal.active {
            display: flex;
            opacity: 1;
        }
        
        .modal > div {
            background: white;
            border-radius: 0.5rem;
            max-width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
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

        /* Custom checkbox */
        .custom-checkbox {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 1.25rem;
            height: 1.25rem;
            border: 2px solid #cbd5e0;
            border-radius: 0.25rem;
            outline: none;
            cursor: pointer;
            position: relative;
            vertical-align: middle;
            margin-right: 0.5rem;
        }

        .custom-checkbox:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .custom-checkbox:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 0.75rem;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        /* Validation error styles */
        .validation-error {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: block;
        }

        .border-red-500 {
            border-color: #ef4444 !important;
        }

        .border-red-500:focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Navigation Bar -->
    <nav class="w-full p-3 h-16 bg-[#28644c] text-white shadow-md fixed top-0 left-0 right-0 z-50">
        <div class="flex justify-between items-center h-full max-w-7xl mx-auto">
            <div class="flex items-center space-x-4">
                <button id="toggle-btn" class="pl-2 focus:outline-none">
                    <i class="fa-solid fa-bars text-2xl cursor-pointer"></i>
                </button>
                <h1 class="text-2xl font-bold tracking-tight">Access Control & Permissions</h1>
            </div>
            <div class="flex items-center space-x-1">
                <button class="relative p-2 transition duration-200 focus:outline-none" id="notificationBtn">
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
    // Show modal function
    function showModal(modalId) {
        console.log('showModal called with ID:', modalId);
        const modal = document.getElementById(modalId);
        
        if (!modal) {
            console.error(`Modal with ID ${modalId} not found`);
            console.log('Available modals:');
            document.querySelectorAll('[id$="Modal"]').forEach(m => {
                console.log(`- ${m.id}`, m);
            });
            return;
        }
        
        console.log('Modal element found:', modal);
        
        // Show the modal
        modal.style.display = 'flex';
        
        // Force reflow to ensure display change takes effect
        void modal.offsetWidth;
        
        // Add active class for opacity transition
        modal.classList.add('active');
        
        // Prevent body scrolling when modal is open
        document.body.style.overflow = 'hidden';
        
        console.log('Modal should now be visible');
    }

    // Close modal function
    function closeModal(modalId) {
        console.log('Closing modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            // Remove active class for opacity transition
            modal.classList.remove('active');
            // Wait for transition to complete before hiding
            setTimeout(() => {
                modal.style.display = 'none';
                // Re-enable body scrolling
                document.body.style.overflow = 'auto';
            }, 300);
        } else {
            console.error(`Modal with ID ${modalId} not found`);
        }
    }

    // Debug function to log element info
    function debugElement(el) {
        if (!el) return 'Element not found';
        return {
            tag: el.tagName,
            id: el.id,
            class: el.className,
            text: el.textContent.trim(),
            html: el.outerHTML
        };
    }

    // Initialize event listeners when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM fully loaded, initializing event listeners');
        
        // Debug: Log all permission buttons
        const viewBtns = document.querySelectorAll('.view-permission-btn, [onclick*="showPermissionDetails"]');
        const editBtns = document.querySelectorAll('.edit-permission-btn, [onclick*="openEditPermissionModal"]');
        const deleteBtns = document.querySelectorAll('.delete-permission-btn, [onclick*="showDeleteConfirmation"]');
        
        console.log('Found view buttons:', viewBtns.length);
        console.log('Found edit buttons:', editBtns.length);
        console.log('Found delete buttons:', deleteBtns.length);
        
        if (viewBtns.length > 0) {
            console.log('First view button:', debugElement(viewBtns[0]));
        }
        
        // Add direct click handlers for debugging
        document.querySelectorAll('.view-permission-btn, [onclick*="showPermissionDetails"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                console.log('Direct click on view button:', debugElement(btn));
                e.preventDefault();
                e.stopPropagation();
                
                let permission;
                if (btn.hasAttribute('data-permission')) {
                    try {
                        permission = JSON.parse(btn.getAttribute('data-permission'));
                    } catch (error) {
                        console.error('Error parsing permission data:', error);
                        return;
                    }
                } else if (btn.onclick) {
                    console.log('Button has onclick handler:', btn.getAttribute('onclick'));
                }
                
                if (permission) {
                    console.log('Showing permission details (direct):', permission);
                    showPermissionDetails(permission);
                }
            });
        });
        
        // Handle View button clicks with event delegation
        document.addEventListener('click', function(e) {
            // Handle View button
            // Handle Save Permission button (event delegation)
            const saveBtn = e.target.closest('#savePermissionBtn');
            if (saveBtn) {
                const form = document.getElementById('newPermissionForm');
                if (form) {
                    e.preventDefault();
                    e.stopPropagation();
                    try { form.requestSubmit ? form.requestSubmit() : form.submit(); } catch (_) { form.submit(); }
                }
                return;
            }

            // Handle View button
            const viewBtn = e.target.closest('.view-permission-btn, [onclick*="showPermissionDetails"]');
            if (viewBtn) {
                console.log('View button clicked (delegation):', debugElement(viewBtn));
                e.preventDefault();
                e.stopPropagation();
                
                let permission;
                if (viewBtn.hasAttribute('data-permission')) {
                    try {
                        permission = JSON.parse(viewBtn.getAttribute('data-permission'));
                    } catch (error) {
                        console.error('Error parsing permission data:', error);
                        return;
                    }
                } else if (viewBtn.onclick) {
                    // Handle old onclick handler
                    const onclickText = viewBtn.getAttribute('onclick');
                    const match = onclickText.match(/showPermissionDetails\((.+?)\)/);
                    if (match && match[1]) {
                        try {
                            permission = JSON.parse(match[1]);
                        } catch (e) {
                            console.error('Error parsing permission from onclick:', e);
                            return;
                        }
                    }
                }
                
                if (permission) {
                    console.log('Showing permission details:', permission);
                    showPermissionDetails(permission);
                }
                return;
            }
            
            // Handle Edit button
            const editBtn = e.target.closest('.edit-permission-btn, [onclick^="openEditPermissionModal"]');
            if (editBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                let permissionId;
                if (editBtn.hasAttribute('data-id')) {
                    permissionId = editBtn.getAttribute('data-id');
                } else if (editBtn.onclick) {
                    // Handle old onclick handler
                    const onclickText = editBtn.getAttribute('onclick');
                    const match = onclickText.match(/openEditPermissionModal\(['"](\d+)['"]\)/);
                    if (match && match[1]) {
                        permissionId = match[1];
                    }
                }
                
                if (permissionId) {
                    console.log('Editing permission ID:', permissionId);
                    openEditPermissionModal(permissionId);
                }
                return;
            }
            
            // Handle Delete button
            const deleteBtn = e.target.closest('.delete-permission-btn, [onclick^="showDeleteConfirmation"]');
            if (deleteBtn) {
                e.preventDefault();
                e.stopPropagation();
                
                let permissionId;
                if (deleteBtn.hasAttribute('data-id')) {
                    permissionId = deleteBtn.getAttribute('data-id');
                } else if (deleteBtn.onclick) {
                    // Handle old onclick handler
                    const onclickText = deleteBtn.getAttribute('onclick');
                    const match = onclickText.match(/showDeleteConfirmation\(['"](\d+)['"]\)/);
                    if (match && match[1]) {
                        permissionId = match[1];
                    }
                }
                
                if (permissionId) {
                    console.log('Deleting permission ID:', permissionId);
                    showDeleteConfirmation(permissionId);
                }
                return;
            }
            
            // Close modals when clicking outside
            if (e.target.classList.contains('modal')) {
                closeModal(e.target.id);
            }
        });
        
        // Close modals when clicking the X button
        document.querySelectorAll('[onclick^="closeModal"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation();
                const match = this.getAttribute('onclick').match(/closeModal\(['"]([^'"]+)['"]\)/);
                if (match && match[1]) {
                    closeModal(match[1]);
                }
            });
        });
        // Handle new permission button click
        const newPermissionBtn = document.getElementById('newPermissionBtn');
        if (newPermissionBtn) {
            newPermissionBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                showModal('newPermissionModal');
            });
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            const modals = ['newPermissionModal', 'viewPermissionModal', 'editPermissionModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal && modal.style.display === 'flex') {
                    if (e.target === modal) {
                        closeModal(modalId);
                    }
                }
            });
        });
    });

    if (typeof window.toggleSidebarDropdown !== 'function') {
        window.toggleSidebarDropdown = function(el){
          try{
            var list = document.querySelectorAll('.has-dropdown > div');
            for (var i=0;i<list.length;i++){
              var t=list[i];
              if (t!==el){
                var m=t.nextElementSibling; var c=t.querySelector('.bx-chevron-down');
                if(m && !m.classList.contains('hidden')) m.classList.add('hidden');
                if(c) c.classList.remove('rotate-180');
              }
            }
            if(el){
              var menu=el.nextElementSibling; var chev=el.querySelector('.bx-chevron-down');
              if(menu) menu.classList.toggle('hidden');
              if(chev) chev.classList.toggle('rotate-180');
            }
          }catch(e){}
        };
      }
      if (typeof window.toggleUserMenu !== 'function') {
        window.toggleUserMenu = function(ev){
          try{
            if(ev && ev.stopPropagation) ev.stopPropagation();
            if(ev && ev.stopImmediatePropagation) ev.stopImmediatePropagation();
            var btn=document.getElementById('userMenuBtn');
            var menu=document.getElementById('userMenuDropdown');
            var notif=document.getElementById('notificationDropdown');
            console.debug('[UserMenu] click', { btn: !!btn, menu: !!menu });
            if(menu){
              var isHidden = menu.classList.contains('hidden');
              if(isHidden){
                menu.classList.remove('hidden');
                try{ menu.style.setProperty('display','block','important'); }catch(_){ menu.style.display = 'block'; }
                if (btn) {
                  var rect = btn.getBoundingClientRect();
                  var top = rect.bottom + 8;
                  menu.style.position = 'fixed';
                  menu.style.top = top + 'px';
                  var width = menu.offsetWidth || 192;
                  var left = Math.max(8, Math.min(rect.right - width, window.innerWidth - width - 8));
                  menu.style.left = left + 'px';
                  menu.style.right = 'auto';
                  menu.style.zIndex = 9999;
                }
                // Post-open verification & fallback
                setTimeout(function(){
                  try{
                    var cs = window.getComputedStyle(menu);
                    var stillHidden = menu.classList.contains('hidden') || cs.display === 'none' || cs.visibility === 'hidden' || cs.opacity === '0';
                    if(stillHidden){
                      menu.classList.remove('hidden');
                      try{ menu.style.setProperty('display','block','important'); }catch(_){ menu.style.display = 'block'; }
                      menu.style.visibility = 'visible';
                      menu.style.opacity = '1';
                    }
                    console.debug('[UserMenu] opened', { display: cs.display, top: menu.style.top, left: menu.style.left });
                  }catch(e){}
                }, 0);
                window.__lastMenuOpenTs = Date.now();
              } else {
                menu.classList.add('hidden');
                try{ menu.style.setProperty('display','none','important'); }catch(_){ menu.style.display = 'none'; }
                console.debug('[UserMenu] closed');
              }
            }
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
      if (typeof window.openCaseWithConfGate !== 'function') {
        window.openCaseWithConfGate = function(href){
          try{ if (window.sessionStorage) sessionStorage.setItem('confOtpPending','1'); }catch(_){ }
          if (href){ window.location.href = href; }
          return false;
        };
      }
      if (typeof window.hideAllMenus !== 'function') {
        window.hideAllMenus = function(){
          var ud=document.getElementById('userMenuDropdown');
          var nd=document.getElementById('notificationDropdown');
          var ub=document.getElementById('userMenuBtn');
          var nb=document.getElementById('notificationBtn');
          if(ud){ ud.classList.add('hidden'); ud.style.display='none'; }
          if(nd){ nd.classList.add('hidden'); }
          if(ub){ ub.setAttribute('aria-expanded','false'); }
          if(nb){ nb.setAttribute('aria-expanded','false'); }
        };
      }
      (function(){
        if (window.__accessControlMenusBound) return; window.__accessControlMenusBound = true;
        // Reparent dropdown to body to avoid stacking/overflow issues
        document.addEventListener('DOMContentLoaded', function(){
          try{
            var menu=document.getElementById('userMenuDropdown');
            if(menu && menu.parentNode !== document.body){ document.body.appendChild(menu); }
          }catch(e){}
        });
        document.addEventListener('click', function(e){
          if (typeof window.__lastMenuOpenTs === 'number' && (Date.now() - window.__lastMenuOpenTs) < 120) { return; }
          var ud=document.getElementById('userMenuDropdown');
          var ub=document.getElementById('userMenuBtn');
          var nd=document.getElementById('notificationDropdown');
          var nb=document.getElementById('notificationBtn');
          var clickInsideUser = (ub && (ub.contains(e.target) || (ud && ud.contains(e.target))));
          var clickInsideNotif = (nb && (nb.contains(e.target) || (nd && nd.contains(e.target))));
          if(!clickInsideUser && !clickInsideNotif){ if(window.hideAllMenus) window.hideAllMenus(); }
        });
        document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ if(window.hideAllMenus) window.hideAllMenus(); }});
        var nb=document.getElementById('notificationBtn'); if(nb){ nb.addEventListener('click', function(e){ if(window.toggleNotification) window.toggleNotification(e); }); }
        var ub=document.getElementById('userMenuBtn');
        if(ub){ var hasInline = !!ub.getAttribute('onclick'); if(!hasInline){ ub.addEventListener('click', function(e){ if(window.toggleUserMenu) window.toggleUserMenu(e); }); } }
        window.addEventListener('resize', function(){ if(window.hideAllMenus) window.hideAllMenus(); });
        window.addEventListener('scroll', function(){ if(window.hideAllMenus) window.hideAllMenus(); }, { passive: true });
      })();
      // Modal fallback open/close to ensure reliability even if later scripts fail
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
                        <i class="fas fa-user-plus"></i>
                    </div>
                </div>
                <div class="flex-grow text-sm">
                    <p class="font-semibold text-gray-900 leading-tight">New Permission Added</p>
                    <p class="text-gray-600 leading-tight text-xs">John Doe was granted Admin access</p>
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
                    <p class="font-semibold text-gray-900 leading-tight">Permission Updated</p>
                    <p class="text-gray-600 leading-tight text-xs">Finance Team permissions modified</p>
                    <p class="text-gray-400 text-xs mt-0.5">2 hours ago</p>
                </div>
            </li>
            <li class="flex items-start px-4 py-3 space-x-3">
                <div class="flex-shrink-0 mt-1">
                    <div class="bg-yellow-200 text-yellow-700 rounded-full p-2">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="flex-grow text-sm">
                    <p class="font-semibold text-gray-900 leading-tight">Action Required</p>
                    <p class="text-gray-600 leading-tight text-xs">Review pending permission requests</p>
                    <p class="text-gray-400 text-xs mt-0.5">Yesterday</p>
                </div>
            </li>
        </ul>
        <div class="text-center py-2 border-t border-gray-200">
            <a class="text-[#28644c] text-xs font-semibold hover:underline" href="#">View all notifications</a>
        </div>
    </div>

    <!-- Sidebar and Main Content -->
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
                        <div onclick="toggleSidebarDropdown(this)" class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
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
                    <li class="has-dropdown active">
                        <div onclick="toggleSidebarDropdown(this)" class="flex items-center font-medium justify-between text-lg bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
                            <div class="flex items-center space-x-2">
                                <i class="bx bx-file"></i>
                                <span>Document Management</span>
                            </div>
                            <i class="bx bx-chevron-down text-2xl transition-transform duration-300"></i>
                        </div>
                        <ul class="dropdown-menu active bg-white/20 mt-2 rounded-lg px-2 py-2 space-y-2">
                            <li><a href="{{ route('document.upload.indexing') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-upload mr-2"></i>Document Upload & Indexing</a></li>
                            <li><a href="{{ route('document.version.control') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-git-branch mr-2"></i>Version Control</a></li>
                            <li><a href="{{ route('document.access.control.permissions') }}" class="block px-3 py-2 text-sm bg-white/30 rounded-lg"><i class="bx bx-lock mr-2"></i>Access Control & Permissions</a></li>
                            <li><a href="{{ route('document.archival.retention.policy') }}" class="block px-3 py-2 text-sm hover:bg-white/30 rounded-lg"><i class="bx bx-archive mr-2"></i>Archival & Retention Policy</a></li>
                        </ul>
                    </li>
                    <li class="has-dropdown">
                        <div onclick="toggleSidebarDropdown(this)" class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
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
                        <div onclick="toggleSidebarDropdown(this)" class="flex items-center font-medium justify-between text-lg hover:bg-white/30 px-4 py-2.5 rounded-lg whitespace-nowrap cursor-pointer">
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

        <!-- Main Content -->
        <main id="main-content" class="flex-1 p-6 w-full mt-16">
            <div class="dashboard-container max-w-7xl mx-auto">
                <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-md border border-gray-100 p-8 space-y-6">
                    <!-- Page Header -->
                    <div class="flex justify-between items-center">
                        <h2 class="text-[#1a4d38] font-bold text-xl mb-1">Access Control & Permissions</h2>
                        <div class="flex space-x-3">
                            <button id="newPermissionBtn" class="bg-[#2f855A] text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-[#276749] transition-colors focus:outline-none focus:ring-2 focus:ring-[#2f855A] focus:ring-offset-2">
                                <i class="bx bx-plus mr-1"></i> New Permission
                            </button>
                        </div>
                    </div>
                    <p class="text-gray-600 text-sm">Manage document access permissions for users and groups.</p>

                    <!-- Search Section -->
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="relative flex-1 max-w-2xl">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class='bx bx-search text-gray-400'></i>
                            </div>
                            <input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent" placeholder="Search permissions by name, role, or type...">
                        </div>
                    </div>

                    <!-- Permissions Table -->
                    <section class="mt-8">
                        <h3 class="font-semibold text-lg text-[#1a4d38] mb-4">
                            <i class='bx bx-list-ul mr-2'></i>Access Permissions
                        </h3>
                        <div class="dashboard-card bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permission</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Document Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            // Use permissions provided by the controller/route
                                            $permissions = $permissions ?? [];
                                        @endphp

                                        @forelse($permissions as $permission)
                                            <tr class="activity-item" data-permission-id="{{ $permission['id'] }}">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                            <i class='{{ $permission['type'] === 'Group' ? 'bx bx-group text-blue-600' : 'bx bx-user text-blue-600' }}'></i>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $permission['name'] }}</div>
                                                            <div class="text-xs text-gray-500">{{ $permission['email'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $permission['role'] === 'Admin' ? 'bg-green-100 text-green-800' : 
                                                           ($permission['role'] === 'Editor' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                                        {{ $permission['role'] }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $permission['document_type'] }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($permission['permissions'] as $perm)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                                {{ ucfirst($perm) }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $permission['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ ucfirst($permission['status']) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button data-permission='@json($permission)' class="view-permission-btn text-blue-600 hover:text-blue-900 mr-3 bg-transparent border-none p-0 cursor-pointer">View</button>
                                                    <button data-id="{{ $permission['id'] }}" class="edit-permission-btn text-green-600 hover:text-green-900 mr-3 bg-transparent border-none p-0 cursor-pointer">Edit</button>
                                                    <button data-id="{{ $permission['id'] }}" class="delete-permission-btn text-red-600 hover:text-red-900 bg-transparent border-none p-0 cursor-pointer">Delete</button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                                    No permissions found. Click "New Permission" to add one.
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

    <!-- View Permission Details Modal -->
    <div id="viewPermissionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900">Permission Details</h3>
                <button onclick="closeModal('viewPermissionModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6" id="permissionDetailsContent">
                <!-- Content will be loaded by JavaScript -->
            </div>
        </div>
    </div>

    <!-- New Permission Modal -->
    <div id="newPermissionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900">Add New Permission</h3>
                <button type="button" onclick="closeModal('newPermissionModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <form id="newPermissionForm" class="p-6 space-y-6" action="{{ route('permissions.store') }}" method="POST" novalidate onsubmit="return window.submitNewPermissionAjax(event)">
                @csrf
                <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="permissionType" class="block text-sm font-medium text-gray-700 mb-1">Permission Type *</label>
                        <select id="permissionType" name="permission_type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="user">User</option>
                            <option value="group">Group</option>
                            <option value="department">Department</option>
                        </select>
                    </div>
                    <div id="userField">
                        <label for="user" class="block text-sm font-medium text-gray-700 mb-1">Select User *</label>
                        <select id="user" name="user" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="">Select a user</option>
                            @foreach(($allUsers ?? []) as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="groupField" class="hidden">
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Select Group *</label>
                        <select id="group" name="group" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="">Select a group</option>
                            <option value="1">Finance Team</option>
                            <option value="2">HR Department</option>
                            <option value="3">IT Support</option>
                        </select>
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select id="role" name="role" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label for="documentType" class="block text-sm font-medium text-gray-700 mb-1">Document Type *</label>
                        <select id="documentType" name="document_type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="all">All Documents</option>
                            <option value="financial">Financial Reports</option>
                            <option value="hr">HR Documents</option>
                            <option value="legal">Legal Contracts</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    </div>

                    <div id="customPermissions" class="hidden border-t pt-4 mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Custom Permissions</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="viewPermission" name="permissions[]" value="view" class="custom-checkbox" checked>
                            <label for="viewPermission" class="ml-2 text-sm text-gray-700">View</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editPermission" name="permissions[]" value="edit" class="custom-checkbox">
                            <label for="editPermission" class="ml-2 text-sm text-gray-700">Edit</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="deletePermission" name="permissions[]" value="delete" class="custom-checkbox">
                            <label for="deletePermission" class="ml-2 text-sm text-gray-700">Delete</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="sharePermission" name="permissions[]" value="share" class="custom-checkbox">
                            <label for="sharePermission" class="ml-2 text-sm text-gray-700">Share</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="downloadPermission" name="permissions[]" value="download" class="custom-checkbox">
                            <label for="downloadPermission" class="ml-2 text-sm text-gray-700">Download</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="printPermission" name="permissions[]" value="print" class="custom-checkbox">
                            <label for="printPermission" class="ml-2 text-sm text-gray-700">Print</label>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4 mt-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#2f855a] focus:border-[#2f855a] sm:text-sm"></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button type="button" onclick="closeModal('newPermissionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button id="savePermissionBtn" type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2f855a] hover:bg-[#276749] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Save Permission
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Permission Modal -->
    <div id="editPermissionModal" class="modal hidden" aria-modal="true" role="dialog" aria-labelledby="edit-permission-modal-title">
        <div class="bg-white rounded-lg w-full max-w-2xl">
            <div class="flex justify-between items-center border-b px-6 py-4">
                <h3 id="edit-permission-modal-title" class="text-xl font-semibold text-gray-900">Edit Permission</h3>
                <button onclick="closeModal('editPermissionModal')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class='fas fa-times text-2xl'></i>
                </button>
            </div>
            <form id="editPermissionForm" class="p-6 space-y-6">
                @csrf
                <input type="hidden" id="editPermissionId" name="id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="editPermissionType" class="block text-sm font-medium text-gray-700 mb-1">Permission Type *</label>
                        <select id="editPermissionType" name="permission_type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="user">User</option>
                            <option value="group">Group</option>
                            <option value="department">Department</option>
                        </select>
                    </div>
                    <div id="editUserField">
                        <label for="editUser" class="block text-sm font-medium text-gray-700 mb-1">Select User *</label>
                        <select id="editUser" name="user" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="">Select a user</option>
                            @foreach(($allUsers ?? []) as $u)
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="editGroupField" class="hidden">
                        <label for="editGroup" class="block text-sm font-medium text-gray-700 mb-1">Select Group *</label>
                        <select id="editGroup" name="group" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="">Select a group</option>
                            <option value="1">Finance Team</option>
                            <option value="2">HR Department</option>
                            <option value="3">IT Support</option>
                        </select>
                    </div>
                    <div>
                        <label for="editRole" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select id="editRole" name="role" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="viewer">Viewer</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label for="editDocumentType" class="block text-sm font-medium text-gray-700 mb-1">Document Type *</label>
                        <select id="editDocumentType" name="document_type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#2f855a] focus:border-transparent sm:text-sm rounded-md">
                            <option value="all">All Documents</option>
                            <option value="financial">Financial Reports</option>
                            <option value="hr">HR Documents</option>
                            <option value="legal">Legal Contracts</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>

                <div id="editCustomPermissions" class="hidden border-t pt-4 mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Custom Permissions</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" id="editViewPermission" name="permissions[]" value="view" class="custom-checkbox">
                            <label for="editViewPermission" class="ml-2 text-sm text-gray-700">View</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editEditPermission" name="permissions[]" value="edit" class="custom-checkbox">
                            <label for="editEditPermission" class="ml-2 text-sm text-gray-700">Edit</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editDeletePermission" name="permissions[]" value="delete" class="custom-checkbox">
                            <label for="editDeletePermission" class="ml-2 text-sm text-gray-700">Delete</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editSharePermission" name="permissions[]" value="share" class="custom-checkbox">
                            <label for="editSharePermission" class="ml-2 text-sm text-gray-700">Share</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editDownloadPermission" name="permissions[]" value="download" class="custom-checkbox">
                            <label for="editDownloadPermission" class="ml-2 text-sm text-gray-700">Download</label>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" id="editPrintPermission" name="permissions[]" value="print" class="custom-checkbox">
                            <label for="editPrintPermission" class="ml-2 text-sm text-gray-700">Print</label>
                        </div>
                    </div>
                </div>

                <div class="border-t pt-4 mt-4">
                    <label for="editNotes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="editNotes" name="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-[#2f855a] focus:border-[#2f855a] sm:text-sm"></textarea>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeModal('editPermissionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Cancel
                    </button>
                    <button type="button" onclick="submitEditPermission()" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-[#2f855a] hover:bg-[#276749] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deletePermissionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Permission</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this permission? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeModal('deletePermissionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </button>
                    <button type="button" id="confirmDeleteBtn" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg w-full max-w-md">
            <div class="p-6 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <i class="fas fa-exclamation text-red-600 text-xl"></i>
                </div>
                <h3 id="delete-permission-modal-title" class="text-lg font-medium text-gray-900 mb-2">Delete Permission</h3>
                <p class="text-sm text-gray-500 mb-6">Are you sure you want to delete this permission? This action cannot be undone.</p>
                <div class="flex justify-center space-x-4">
                    <button type="button" onclick="closeModal('deletePermissionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2f855a]">
                        Cancel
                    </button>
                    <button type="button" id="confirmDeleteBtn" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Menu Dropdown -->
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
                <form class="space-y-4 text-xs text-gray-700" action="{{ route('account.update') }}" method="POST">
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
                <button id="closePrivacySecurityBtn" onclick="closePrivacySecurityModal()" type="button" class="text-gray-400 hover:text-gray-600 rounded-lg p-2 shadow-sm focus:outline-none focus:ring-2 focus:ring-gray-400 transition-all duration-200" aria-label="Close">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <div class="px-8 pt-6 pb-8">
                <form class="space-y-4 text-xs text-gray-900" action="{{ route('privacy.update') }}" method="POST">
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

    <!-- Success Message Toast -->
    @if(session('success'))
    <div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50" style="min-width: 300px;">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button onclick="document.getElementById('successToast').remove()" class="ml-4 text-white hover:text-gray-100">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.remove();
            }
        }, 5000);
    </script>
    @endif
    @if(session('status'))
    <div id="successToast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50" style="min-width: 300px;">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-2"></i>
            <span>{{ session('status') }}</span>
        </div>
        <button onclick="document.getElementById('successToast').remove()" class="ml-4 text-white hover:text-gray-100">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.remove();
            }
        }, 5000);
    </script>
    @endif
    <script>
        document.addEventListener("DOMContentLoaded", () => {
    const dropdownToggles = document.querySelectorAll('.has-dropdown > div');
    function closeAllDropdowns(except) {
        dropdownToggles.forEach((t) => {
            if (t === except) return;
            const menu = t.nextElementSibling;
            const chev = t.querySelector('.bx-chevron-down');
            if (menu && !menu.classList.contains('hidden')) menu.classList.add('hidden');
            if (chev) chev.classList.remove('rotate-180');
        });
    }
    window.toggleSidebarDropdown = function(el) {
        const menu = el ? el.nextElementSibling : null;
        const chev = el ? el.querySelector('.bx-chevron-down') : null;
        closeAllDropdowns(el || null);
        if (menu) menu.classList.toggle('hidden');
        if (chev) chev.classList.toggle('rotate-180');
    };
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const menu = toggle.nextElementSibling;
            const chev = toggle.querySelector('.bx-chevron-down');
            closeAllDropdowns(toggle);
            if (menu) menu.classList.toggle('hidden');
            if (chev) chev.classList.toggle('rotate-180');
        });
    });
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
            const openSignOutBtn = document.getElementById("openSignOutBtn");
            const openPrivacySecurityBtn = document.getElementById("openPrivacySecurityBtn");
            const privacySecurityModal = document.getElementById("privacySecurityModal");
            const closePrivacySecurityBtn = document.getElementById("closePrivacySecurityBtn");
            const cancelPrivacySecurityBtn = document.getElementById("cancelPrivacySecurityBtn");
            const signOutModal = document.getElementById("signOutModal");
            const cancelSignOutBtn = document.getElementById("cancelSignOutBtn");
            const cancelSignOutBtn2 = document.getElementById("cancelSignOutBtn2");
            const newPermissionBtn = document.getElementById("newPermissionBtn");
            const newPermissionModal = document.getElementById("newPermissionModal");
            const editPermissionModal = document.getElementById("editPermissionModal");
            const deletePermissionModal = document.getElementById("deletePermissionModal");
            const permissionType = document.getElementById("permissionType");
            const userField = document.getElementById("userField");
            const groupField = document.getElementById("groupField");
            const roleSelect = document.getElementById("role");
            const customPermissions = document.getElementById("customPermissions");
            const editPermissionType = document.getElementById("editPermissionType");
            const editUserField = document.getElementById("editUserField");
            const editGroupField = document.getElementById("editGroupField");
            const editRoleSelect = document.getElementById("editRole");
            const editCustomPermissions = document.getElementById("editCustomPermissions");
            const searchInput = document.getElementById("searchInput");
            const filterRole = document.getElementById("filterRole");
            const filterStatus = document.getElementById("filterStatus");
            const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");

            // Initialize sidebar state
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
                    if (dropdown && !dropdown.classList.contains("hidden")) {
                        dropdown.classList.add("hidden");
                        if (chevron) chevron.classList.remove("rotate-180");
                    }
                });
            }

            // Toggle dropdown menus
            dropdownToggles.forEach((toggle) => {
                toggle.addEventListener("click", (e) => {
                    e.stopPropagation();
                    const dropdown = toggle.nextElementSibling;
                    const chevron = toggle.querySelector(".bx-chevron-down");
                    
                    // Close other dropdowns
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

            // Delegated handler (more robust) for dropdown toggles
            if (sidebar) {
                sidebar.addEventListener("click", (e) => {
                    const toggle = e.target.closest(".has-dropdown > div");
                    if (!toggle) return;
                    e.stopPropagation();
                    const dropdown = toggle.nextElementSibling;
                    const chevron = toggle.querySelector(".bx-chevron-down");

                    // Close other dropdowns
                    document.querySelectorAll(".has-dropdown > div").forEach((otherToggle) => {
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
            }

            // Close dropdowns when clicking outside
            document.addEventListener("click", (e) => {
                if (!e.target.closest('.has-dropdown')) {
                    closeAllDropdowns();
                }
            });

            // Handle overlay click
            overlay.addEventListener("click", () => {
                sidebar.classList.add("-ml-72");
                overlay.classList.add("hidden");
                document.body.style.overflow = "";
                mainContent.classList.remove("sidebar-open");
                mainContent.classList.add("sidebar-closed");
                closeAllDropdowns();
            });

            // Handle window resize
            function handleResize() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove("-ml-72");
                    mainContent.classList.add("md:ml-72", "sidebar-open");
                    mainContent.classList.remove("sidebar-closed");
                    overlay.classList.add("hidden");
                    document.body.style.overflow = "";
                } else {
                    sidebar.classList.add("-ml-72");
                    mainContent.classList.remove("md:ml-72", "sidebar-open");
                    mainContent.classList.add("sidebar-closed");
                }
            }

            // Set up event listeners
            if (toggleBtn) toggleBtn.addEventListener("click", toggleSidebar);
            window.addEventListener("resize", handleResize);

            // Notification dropdown
            if (notificationBtn) notificationBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                if (notificationDropdown) notificationDropdown.classList.toggle("hidden");
                if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                if (profileModal) profileModal.classList.remove("active");
                if (accountSettingsModal) accountSettingsModal.classList.remove("active");
                if (privacySecurityModal) privacySecurityModal.classList.remove("active");
                if (signOutModal) signOutModal.classList.remove("active");
                if (newPermissionModal) newPermissionModal.classList.remove("active");
                if (editPermissionModal) editPermissionModal.classList.remove("active");
                if (deletePermissionModal) deletePermissionModal.classList.remove("active");
            });

            // User menu dropdown
            if (userMenuBtn) userMenuBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                if (userMenuDropdown) userMenuDropdown.classList.toggle("hidden");
                const expanded = userMenuBtn.getAttribute("aria-expanded") === "true";
                userMenuBtn.setAttribute("aria-expanded", (!expanded).toString());
                if (notificationDropdown) notificationDropdown.classList.add("hidden");
                if (profileModal) profileModal.classList.remove("active");
                if (accountSettingsModal) accountSettingsModal.classList.remove("active");
                if (privacySecurityModal) privacySecurityModal.classList.remove("active");
                if (signOutModal) signOutModal.classList.remove("active");
                if (newPermissionModal) newPermissionModal.classList.remove("active");
                if (editPermissionModal) editPermissionModal.classList.remove("active");
                if (deletePermissionModal) deletePermissionModal.classList.remove("active");
            });

            if (openSignOutBtn) openSignOutBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                if (signOutModal) signOutModal.classList.add("active");
                if (userMenuDropdown) userMenuDropdown.classList.add("hidden");
                if (userMenuBtn) userMenuBtn.setAttribute("aria-expanded", "false");
                if (profileModal) profileModal.classList.remove("active");
                if (accountSettingsModal) accountSettingsModal.classList.remove("active");
                if (privacySecurityModal) privacySecurityModal.classList.remove("active");
                if (notificationDropdown) notificationDropdown.classList.add("hidden");
                if (newPermissionModal) newPermissionModal.classList.remove("active");
                if (editPermissionModal) editPermissionModal.classList.remove("active");
                if (deletePermissionModal) deletePermissionModal.classList.remove("active");
            });

            // Profile modal
            if (openProfileBtn) openProfileBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                profileModal.classList.add("active");
                userMenuDropdown.classList.add("hidden");
                userMenuBtn.setAttribute("aria-expanded", "false");
                accountSettingsModal.classList.remove("active");
                privacySecurityModal.classList.remove("active");
                notificationDropdown.classList.add("hidden");
                signOutModal.classList.remove("active");
                newPermissionModal.classList.remove("active");
                editPermissionModal.classList.remove("active");
                deletePermissionModal.classList.remove("active");
            });

            if (closeProfileBtn) closeProfileBtn.addEventListener("click", () => {
                profileModal.classList.remove("active");
            });
            if (closeProfileBtn2) closeProfileBtn2.addEventListener("click", () => {
                profileModal.classList.remove("active");
            });

            // Account settings modal
            if (openAccountSettingsBtn) openAccountSettingsBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                accountSettingsModal.classList.add("active");
                userMenuDropdown.classList.add("hidden");
                userMenuBtn.setAttribute("aria-expanded", "false");
                profileModal.classList.remove("active");
                privacySecurityModal.classList.remove("active");
                notificationDropdown.classList.add("hidden");
                signOutModal.classList.remove("active");
                newPermissionModal.classList.remove("active");
                editPermissionModal.classList.remove("active");
                deletePermissionModal.classList.remove("active");
            });

            if (closeAccountSettingsBtn) closeAccountSettingsBtn.addEventListener("click", () => {
                accountSettingsModal.classList.remove("active");
            });
            if (cancelAccountSettingsBtn) cancelAccountSettingsBtn.addEventListener("click", () => {
                accountSettingsModal.classList.remove("active");
            });

            // Privacy & Security modal
            if (openPrivacySecurityBtn) openPrivacySecurityBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                privacySecurityModal.classList.add("active");
                userMenuDropdown.classList.add("hidden");
                userMenuBtn.setAttribute("aria-expanded", "false");
                profileModal.classList.remove("active");
                accountSettingsModal.classList.remove("active");
                notificationDropdown.classList.add("hidden");
                signOutModal.classList.remove("active");
                newPermissionModal.classList.remove("active");
                editPermissionModal.classList.remove("active");
                deletePermissionModal.classList.remove("active");
            });

            if (closePrivacySecurityBtn) closePrivacySecurityBtn.addEventListener("click", () => {
                privacySecurityModal.classList.remove("active");
            });
            if (cancelPrivacySecurityBtn) cancelPrivacySecurityBtn.addEventListener("click", () => {
                privacySecurityModal.classList.remove("active");
            });

            // Sign out modal
            if (cancelSignOutBtn) cancelSignOutBtn.addEventListener("click", () => {
                signOutModal.classList.remove("active");
            });
            if (cancelSignOutBtn2) cancelSignOutBtn2.addEventListener("click", () => {
                signOutModal.classList.remove("active");
            });

            // Close modals and dropdowns on outside click
            document.addEventListener("click", (e) => {
                if (notificationDropdown && notificationBtn && !notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                    notificationDropdown.classList.add("hidden");
                }
                if (userMenuDropdown && userMenuBtn && !userMenuDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
                    userMenuDropdown.classList.add("hidden");
                    userMenuBtn.setAttribute("aria-expanded", "false");
                }
                if (profileModal && openProfileBtn && !profileModal.contains(e.target) && !openProfileBtn.contains(e.target)) {
                    profileModal.classList.remove("active");
                }
                if (accountSettingsModal && openAccountSettingsBtn && !accountSettingsModal.contains(e.target) && !openAccountSettingsBtn.contains(e.target)) {
                    accountSettingsModal.classList.remove("active");
                }
                if (privacySecurityModal && openPrivacySecurityBtn && !privacySecurityModal.contains(e.target) && !openPrivacySecurityBtn.contains(e.target)) {
                    privacySecurityModal.classList.remove("active");
                }
                if (signOutModal && !signOutModal.contains(e.target) && (!userMenuDropdown || !userMenuDropdown.contains(e.target))) {
                    signOutModal.classList.remove("active");
                }
                if (newPermissionModal && newPermissionBtn && !newPermissionModal.contains(e.target) && !newPermissionBtn.contains(e.target)) {
                    newPermissionModal.classList.remove("active");
                }
                if (editPermissionModal && !editPermissionModal.contains(e.target)) {
                    editPermissionModal.classList.remove("active");
                }
                if (deletePermissionModal && !deletePermissionModal.contains(e.target)) {
                    deletePermissionModal.classList.remove("active");
                }
            });

            // New Permission Modal
            const newPermissionBtn = document.getElementById('newPermissionBtn');
            const newPermissionModal = document.getElementById('newPermissionModal');
            
            if (newPermissionBtn && newPermissionModal) {
                newPermissionBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('New Permission button clicked');
                    
                    // Show the modal
                    newPermissionModal.style.display = 'flex';
                    // Force reflow to ensure display change takes effect
                    void newPermissionModal.offsetWidth;
                    // Add active class for opacity transition
                    newPermissionModal.classList.add('active');
                    
                    // Prevent body scrolling when modal is open
                    document.body.style.overflow = 'hidden';
                    
                    // Close other dropdowns
                    if (userMenuDropdown) userMenuDropdown.classList.add('hidden');
                    if (userMenuBtn) userMenuBtn.setAttribute('aria-expanded', 'false');
                    if (notificationDropdown) notificationDropdown.classList.add('hidden');
                    if (notificationBtn) notificationBtn.setAttribute('aria-expanded', 'false');
                });
            } else {
                console.error('New Permission button or modal not found');
                if (!newPermissionBtn) console.error('Button with ID newPermissionBtn not found');
                if (!newPermissionModal) console.error('Modal with ID newPermissionModal not found');
            }
        document.getElementById("newPermissionForm").reset();
        permissionType.value = "user";
        userField.classList.remove("hidden");
        groupField.classList.add("hidden");
        roleSelect.value = "admin";
        customPermissions.classList.add("hidden");
    });

    // Permission Type Toggle for New Permission
    permissionType.addEventListener("change", () => {
        if (permissionType.value === "user") {
            userField.classList.remove("hidden");
            groupField.classList.add("hidden");
        } else if (permissionType.value === "group") {
            userField.classList.add("hidden");
            groupField.classList.remove("hidden");
        } else {
            userField.classList.add("hidden");
            groupField.classList.add("hidden");
        }
    });

    // Role Selection for New Permission
    roleSelect.addEventListener("change", () => {
        if (roleSelect.value === "custom") {
            customPermissions.classList.remove("hidden");
        } else {
            customPermissions.classList.add("hidden");
        }
    });

    // Permission Type Toggle for Edit Permission
    editPermissionType.addEventListener("change", () => {
        if (editPermissionType.value === "user") {
            editUserField.classList.remove("hidden");
            editGroupField.classList.add("hidden");
        } else if (editPermissionType.value === "group") {
            editUserField.classList.add("hidden");
            editGroupField.classList.remove("hidden");
        } else {
            editUserField.classList.add("hidden");
            editGroupField.classList.add("hidden");
        }
    });

    // Role Selection for Edit Permission
    editRoleSelect.addEventListener("change", () => {
        if (editRoleSelect.value === "custom") {
            editCustomPermissions.classList.remove("hidden");
        } else {
            editCustomPermissions.classList.add("hidden");
        }
    });

    // Search and Filter Permissions
    function filterPermissions() {
        const searchText = searchInput.value.toLowerCase();
        const roleFilter = filterRole.value.toLowerCase();
        const statusFilter = filterStatus.value.toLowerCase();
        const rows = document.querySelectorAll("tbody tr");

        rows.forEach((row) => {
            const name = row.querySelector("td:nth-child(1) .text-sm").textContent.toLowerCase();
            const email = row.querySelector("td:nth-child(1) .text-xs").textContent.toLowerCase();
            const role = row.querySelector("td:nth-child(2) span").textContent.toLowerCase();
            const status = row.querySelector("td:nth-child(5) span").textContent.toLowerCase();

            const matchesSearch = name.includes(searchText) || email.includes(searchText);
            const matchesRole = roleFilter === "" || role === roleFilter;
            const matchesStatus = statusFilter === "" || status === statusFilter;

            row.style.display = matchesSearch && matchesRole && matchesStatus ? "" : "none";
        });
    }

    searchInput.addEventListener("input", filterPermissions);
    filterRole.addEventListener("change", filterPermissions);
    filterStatus.addEventListener("change", filterPermissions);

    // Helper functions for modals
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            // Force reflow to ensure display change takes effect
            void modal.offsetWidth;
            // Add active class for opacity transition
            modal.classList.add('active');
            // Prevent body scrolling when modal is open
            document.body.style.overflow = 'hidden';
        } else {
            console.error(`Modal with ID ${modalId} not found`);
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            // Remove active class for opacity transition
            modal.classList.remove('active');
            // Wait for transition to complete before hiding
            setTimeout(() => {
                modal.style.display = 'none';
                // Re-enable body scrolling
                document.body.style.overflow = 'auto';
            }, 300);
        }
    }

    // View Permission Details
    window.showPermissionDetails = function(permission) {
        console.log('[DEBUG] showPermissionDetails called with:', permission);
        
        // Ensure permission is an object
        if (typeof permission === 'string') {
            try {
                permission = JSON.parse(permission);
            } catch (e) {
                console.error('Failed to parse permission data:', e);
                return;
            }
        }
        
        const modal = document.getElementById("viewPermissionModal");
        const content = document.getElementById("permissionDetailsContent");
        
        if (!modal) {
            console.error('View modal not found');
            return;
        }
        
        if (!content) {
            console.error('Permission details content element not found');
            return;
        }
        
        // Format permissions array if it's not already an array
        const permissions = Array.isArray(permission.permissions) ? 
            permission.permissions : 
            (typeof permission.permissions === 'string' ? permission.permissions.split(',') : []);
            
        content.innerHTML = `
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">Permission Type</h4>
                        <p class="text-sm text-gray-500">${permission.type || 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">Role</h4>
                        <p class="text-sm text-gray-500">${permission.role || 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">Document Type</h4>
                        <p class="text-sm text-gray-500">${permission.document_type || 'N/A'}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-700">Status</h4>
                        <p class="text-sm text-gray-500">${permission.status || 'N/A'}</p>
                    </div>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Permissions</h4>
                    <div class="flex flex-wrap gap-2">
                        ${permissions.length > 0 ? 
                            permissions.map(perm => 
                                `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    ${perm.trim()}
                                </span>`
                            ).join('') : 
                            '<span class="text-sm text-gray-500">No permissions assigned</span>'
                        }
                    </div>
                </div>
                ${permission.notes ? `
                <div>
                    <h4 class="text-sm font-medium text-gray-700">Notes</h4>
                    <p class="text-sm text-gray-500 whitespace-pre-line">${permission.notes}</p>
                </div>` : ''}
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" onclick="closeModal('viewPermissionModal')" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Close
                </button>
            </div>
        `;
        
        // Show the modal
        showModal('viewPermissionModal');
    };
        document.getElementById("editPermissionId").value = permission.id;
        editPermissionType.value = permission.type;
        document.getElementById("editUser").value = permission.user;
        document.getElementById("editGroup").value = permission.group;
        editRoleSelect.value = permission.role;
        document.getElementById("editDocumentType").value = permission.document_type;
        document.getElementById("editNotes").value = permission.notes;

        // Toggle user/group fields
        if (permission.type === "user") {
            editUserField.classList.remove("hidden");
            editGroupField.classList.add("hidden");
        } else if (permission.type === "group") {
            editUserField.classList.add("hidden");
            editGroupField.classList.remove("hidden");
        } else {
            editUserField.classList.add("hidden");
            editGroupField.classList.add("hidden");
        }

        // Toggle custom permissions
        if (permission.role === "custom") {
            editCustomPermissions.classList.remove("hidden");
            ["view", "edit", "delete", "share", "download", "print"].forEach(perm => {
                const checkbox = document.getElementById(`edit${perm.charAt(0).toUpperCase() + perm.slice(1)}Permission`);
                checkbox.checked = permission.permissions.includes(perm);
            });
        } else {
            editCustomPermissions.classList.add("hidden");
        }

    // Form Submission Handling
    document.getElementById("newPermissionForm").addEventListener("submit", (e) => {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(formData.entries()))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: "success",
                    title: "Permission Added",
                    text: data.message || "The new permission has been successfully added.",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload(); // Reload to show the new permission
                });
            } else {
                throw new Error(data.message || 'Failed to add permission');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: error.message || "An error occurred while adding the permission."
            });
        });
    });

    // Show delete confirmation modal
    window.showDeleteConfirmation = function(permissionId) {
        console.log('showDeleteConfirmation called with ID:', permissionId);
        const modal = document.getElementById('deletePermissionModal');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        
        if (!modal || !confirmBtn) {
            console.error('Delete modal or confirm button not found');
            return;
        }
        
        // Remove any existing click handlers to prevent duplicates
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
        
        // Add new click handler
        newConfirmBtn.addEventListener('click', async function handleConfirm() {
            console.log('Delete confirmed for permission ID:', permissionId);
            
            const original = newConfirmBtn.innerHTML;
            newConfirmBtn.disabled = true;
            newConfirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Deleting...';
            
            try {
                const response = await fetch('{{ route("permissions.destroy", ":id") }}'.replace(':id', permissionId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json().catch(() => ({}));
                
                if (response.ok && data.success === true) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message || 'Permission deleted successfully',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    closeModal('deletePermissionModal');
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to delete permission');
                }
            } catch (error) {
                console.error('Error deleting permission:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred while deleting the permission.',
                    confirmButtonColor: '#2f855a'
                });
            } finally {
                newConfirmBtn.disabled = false;
                newConfirmBtn.innerHTML = original;
            }
        });
        
        // Show the modal
        showModal('deletePermissionModal');
    };

    // Function to open edit modal with permission data
    window.openEditPermissionModal = function(permissionId) {
        console.log('openEditPermissionModal called with ID:', permissionId);
        const modal = document.getElementById('editPermissionModal');
        const form = document.getElementById('editPermissionForm');
        
        if (!modal || !form) {
            console.error('Edit modal or form not found');
            return;
        }
        
        // Show loading state
        const content = modal.querySelector('.modal-content') || modal;
        const originalContent = content.innerHTML;
        content.innerHTML = `
            <div class="flex items-center justify-center p-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#2f855a]"></div>
                <span class="ml-3 text-gray-700">Loading...</span>
            </div>
        `;
        
        // Show the modal before fetching data
        showModal('editPermissionModal');
        
        // Fetch the permission details
        fetch('{{ route("permissions.show", ":id") }}'.replace(':id', permissionId), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Permission data loaded:', data);
            if (data.success) {
                const permission = data.permission;
                
                // Set the form action with the permission ID
                form.action = '{{ route("permissions.update", ":id") }}'.replace(':id', permissionId);
                
                // Set form values based on permission data
                document.getElementById('editPermissionId').value = permissionId;
                document.getElementById('editPermissionType').value = permission.type;
                
                // Toggle user/group fields based on permission type
                togglePermissionFields(permission.type, 'edit');
                
                if (permission.type === 'user') {
                    document.getElementById('editUser').value = permission.user_id || '';
                } else {
                    document.getElementById('editGroup').value = permission.group_id || '';
                }
                
                document.getElementById('editRole').value = permission.role;
                document.getElementById('editDocumentType').value = permission.document_type;
                
                // Handle custom permissions checkboxes
                if (permission.role === 'custom' && Array.isArray(permission.permissions)) {
                    permission.permissions.forEach(perm => {
                        const checkbox = document.getElementById(`edit${perm.charAt(0).toUpperCase() + perm.slice(1)}Permission`);
                        if (checkbox) checkbox.checked = true;
                    });
                }
                
                document.getElementById('editNotes').value = permission.notes || '';
                
                // Show the modal
                document.getElementById('editPermissionModal').classList.add('active');
            } else {
                throw new Error(data.message || 'Failed to load permission details');
            }
        })
        .catch(error => {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: error.message || "Failed to load permission details"
            });
        });
    };

    // Submit edit permission function (matches case management pattern)
    window.submitEditPermission = async function() {
        console.log('submitEditPermission called');
        const form = document.getElementById('editPermissionForm');
        if (!form) {
            console.error('Edit form not found');
            return;
        }
        
        const permissionId = document.getElementById('editPermissionId').value;
        const submitBtn = form.querySelector('button[onclick="submitEditPermission()"]');
        const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
        
        if (submitBtn) { 
            submitBtn.disabled = true; 
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...'; 
        }
        
        try {
            // Clear previous error styles and messages
            clearValidationErrors();
            
            // Client-side validation
            const trim = (v) => (v ?? "").toString().trim();
            const errors = [];
            
            const permissionType = trim(document.getElementById('editPermissionType')?.value);
            const user = trim(document.getElementById('editUser')?.value);
            const group = trim(document.getElementById('editGroup')?.value);
            const role = trim(document.getElementById('editRole')?.value);
            const documentType = trim(document.getElementById('editDocumentType')?.value);

            // Required field validation
            if (!permissionType) errors.push("Permission type is required.");
            if (!role) errors.push("Role is required.");
            if (!documentType) errors.push("Document type is required.");
            
            // Conditional validation based on permission type
            if (permissionType === 'user' && !user) {
                errors.push("User selection is required.");
            }
            if (permissionType === 'group' && !group) {
                errors.push("Group selection is required.");
            }

            // Visual hinting for invalid fields
            const mark = (el, bad) => { 
                if (!el) return; 
                el.classList.toggle("border-red-500", !!bad); 
                el.classList.toggle("border-gray-300", !bad); 
            };
            
            mark(document.getElementById('editPermissionType'), !permissionType);
            mark(document.getElementById('editRole'), !role);
            mark(document.getElementById('editDocumentType'), !documentType);
            mark(document.getElementById('editUser'), permissionType === 'user' && !user);
            mark(document.getElementById('editGroup'), permissionType === 'group' && !group);

            if (errors.length > 0) {
                Swal.fire({
                    icon: "error",
                    title: "Please fix the following",
                    html: `<div style="text-align:left">${errors.map(e => `• ${e}`).join('<br>')}</div>`,
                    confirmButtonColor: "#2f855a",
                });
                // Focus on first invalid field
                const firstInvalid = [document.getElementById('editPermissionType'), document.getElementById('editRole'), document.getElementById('editDocumentType'), document.getElementById('editUser'), document.getElementById('editGroup')].find(el => el && el.classList.contains('border-red-500'));
                if (firstInvalid) firstInvalid.focus();
                return;
            }
            
            const formData = new FormData(form);
            const response = await fetch('{{ route("permissions.update", ":id") }}'.replace(':id', permissionId), {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json().catch(() => ({}));
            
            if (response.ok && data.success === true) {
                await Swal.fire({
                    icon: "success",
                    title: "Permission Updated",
                    text: data.message || "The permission has been successfully updated.",
                    showConfirmButton: false,
                    timer: 1500
                });
                closeModal('editPermissionModal');
                window.location.reload();
            } else if (response.status === 422) {
                const errs = (data && data.errors) || {};
                displayValidationErrors(errs);
            } else {
                const msg = (data && (data.message || data.error)) || 'Failed to update permission. Please try again.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#2f855a' });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: "error",
                title: "Error",
                text: error.message || "An error occurred while updating the permission.",
                confirmButtonColor: '#2f855a'
            });
        } finally {
            if (submitBtn) { 
                submitBtn.disabled = false; 
                submitBtn.innerHTML = originalBtnText; 
            }
        }
    };

    // Edit form submission is now handled by onclick="submitEditPermission()" button
    
    // Helper function to toggle permission fields based on type
    function togglePermissionFields(type, prefix = '') {
        const userField = document.getElementById(`${prefix}UserField`);
        const groupField = document.getElementById(`${prefix}GroupField`);
        
        if (type === 'user') {
            userField.classList.remove('hidden');
            groupField.classList.add('hidden');
        } else {
            userField.classList.add('hidden');
            groupField.classList.remove('hidden');
        }
    }
    
    // Initialize permission type change handlers
    document.getElementById('permissionType').addEventListener('change', (e) => {
        togglePermissionFields(e.target.value);
    });
    
    document.getElementById('editPermissionType').addEventListener('change', (e) => {
        togglePermissionFields(e.target.value, 'edit');
    });

    // Debug: Log when the script loads
    console.log('Access control script loaded');
    // Render a visible success banner under the navbar
    function showPermissionSuccessBanner(message){
        try {
            const existing = document.getElementById('permissionSuccessBanner');
            if (existing) existing.remove();
            const wrap = document.createElement('div');
            wrap.id = 'permissionSuccessBanner';
            // Inline styles to ensure visibility above all overlays
            wrap.style.position = 'fixed';
            wrap.style.top = '4rem';
            wrap.style.left = '0';
            wrap.style.right = '0';
            wrap.style.zIndex = '999999';
            wrap.innerHTML = `
                <div class="mx-auto max-w-7xl px-4">
                  <div class="bg-green-50 border border-green-200 text-green-800 rounded-md px-4 py-3 flex items-center justify-between shadow">
                    <div class="flex items-center">
                      <i class="fas fa-check-circle mr-2"></i>
                      <span>${message || 'Permission created successfully.'}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                      <button id="reloadAfterSuccess" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">Reload</button>
                      <button id="dismissPermissionSuccess" class="text-green-800 hover:underline focus:outline-none">Dismiss</button>
                    </div>
                  </div>
                </div>`;
            document.body.appendChild(wrap);
            const d = document.getElementById('dismissPermissionSuccess');
            if (d) d.addEventListener('click', function(){ try { wrap.remove(); } catch(_) {} });
            const r = document.getElementById('reloadAfterSuccess');
            if (r) r.addEventListener('click', function(){ window.location.reload(); });
            setTimeout(function(){ try { wrap.remove(); } catch(_) {} }, 7000);
        } catch(_) {}
    }
    // Show a toast after reload if a permission was just saved via AJAX
    function showPostSaveToastIfAny(){
        try {
            if (localStorage.getItem('permission_saved') === '1') {
                localStorage.removeItem('permission_saved');
                const toast = document.createElement('div');
                toast.id = 'successToast';
                toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center justify-between z-50';
                toast.style.minWidth = '300px';
                toast.innerHTML = '<div class="flex items-center"><i class="fas fa-check-circle mr-2"></i><span>Permission created successfully.</span></div>' +
                                  '<button class="ml-4 text-white hover:text-gray-100" aria-label="Close">\n\t<i class="fas fa-times"></i>\n</button>';
                document.body.appendChild(toast);
                const closeBtn = toast.querySelector('button');
                if (closeBtn) closeBtn.addEventListener('click', function(){ toast.remove(); });
                setTimeout(function(){ try { toast.remove(); } catch(_) {} }, 5000);
            }
        } catch(_) {}
    }
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        showPostSaveToastIfAny();
    } else {
        document.addEventListener('DOMContentLoaded', showPostSaveToastIfAny);
    }
    
    // Modal and Form Handling
    const newPermissionBtn = document.getElementById('newPermissionBtn');
    const newPermissionModal = document.getElementById('newPermissionModal');
    const newPermissionForm = document.getElementById('newPermissionForm');
    // Guard to prevent duplicate submissions
    let __savingPermission = false;
    const permissionType = document.getElementById('permissionType');
    const userField = document.getElementById('userField');
    const groupField = document.getElementById('groupField');
    const roleSelect = document.getElementById('role');
    const customPermissions = document.getElementById('customPermissions');
    const searchInput = document.getElementById('searchInput');
    const permissionRows = document.querySelectorAll('tbody tr[data-permission-id]');
    
    // Submit new permission function (matches case management pattern)
    window.submitNewPermission = async function() {
        console.log('submitNewPermission called');
        const form = document.getElementById('newPermissionForm');
        if (!form) {
            console.error('Form not found');
            return;
        }
        
        const submitBtn = document.getElementById('savePermissionBtn');
        const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) { 
            submitBtn.disabled = true; 
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...'; 
        }
        
        try {
            // Clear previous error styles and messages
            clearValidationErrors();

            // Client-side validation
            const trim = (v) => (v ?? "").toString().trim();
            const errors = [];
            
            const permissionType = trim(document.getElementById('permissionType')?.value);
            const user = trim(document.getElementById('user')?.value);
            const group = trim(document.getElementById('group')?.value);
            const role = trim(document.getElementById('role')?.value);
            const documentType = trim(document.getElementById('documentType')?.value);

            // Required field validation
            if (!permissionType) errors.push("Permission type is required.");
            if (!role) errors.push("Role is required.");
            if (!documentType) errors.push("Document type is required.");
            
            // Conditional validation based on permission type
            if (permissionType === 'user' && !user) {
                errors.push("User selection is required.");
            }
            if (permissionType === 'group' && !group) {
                errors.push("Group selection is required.");
            }

            // Visual hinting for invalid fields
            const mark = (el, bad) => { 
                if (!el) return; 
                el.classList.toggle("border-red-500", !!bad); 
                el.classList.toggle("border-gray-300", !bad); 
            };
            
            mark(document.getElementById('permissionType'), !permissionType);
            mark(document.getElementById('role'), !role);
            mark(document.getElementById('documentType'), !documentType);
            mark(document.getElementById('user'), permissionType === 'user' && !user);
            mark(document.getElementById('group'), permissionType === 'group' && !group);

            if (errors.length > 0) {
                Swal.fire({
                    icon: "error",
                    title: "Please fix the following",
                    html: `<div style="text-align:left">${errors.map(e => `• ${e}`).join('<br>')}</div>`,
                    confirmButtonColor: "#2f855a",
                });
                // Focus on first invalid field
                const firstInvalid = [document.getElementById('permissionType'), document.getElementById('role'), document.getElementById('documentType'), document.getElementById('user'), document.getElementById('group')].find(el => el && el.classList.contains('border-red-500'));
                if (firstInvalid) firstInvalid.focus();
                return;
            }

            const formData = new FormData(form);
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const csrf = tokenMeta ? tokenMeta.getAttribute('content') : '';
            const response = await fetch('{{ route("permissions.store") }}', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': csrf, 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest' 
                },
                body: formData
            });
            
            const data = await response.json().catch(() => ({}));
            
            if (response.ok && data.success === true) {
                form.reset();
                // Keep modal open and show validation inside
                showNewPermissionModalSuccess('Permission created successfully.');
                // Also show page banner in case user closes modal immediately
                showPermissionSuccessBanner('Permission created successfully.');
                return;
            } else if (response.status === 422) {
                const errs = (data && data.errors) || {};
                displayValidationErrors(errs);
            } else {
                const msg = (data && (data.message || data.error)) || 'Failed to create permission. Please try again.';
                Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#2f855a' });
            }
        } catch(error) {
            console.error('Error:', error);
            Swal.fire({ 
                icon: 'error', 
                title: 'Error', 
                text: (error && error.message) || 'Failed to create permission. Please try again.', 
                confirmButtonColor: '#2f855a' 
            });
        } finally {
            if (submitBtn) { 
                submitBtn.disabled = false; 
                submitBtn.innerHTML = originalBtnText; 
            }
        }
    };

    // Submit handler to keep validation/errors inside modal with client-side validation
    async function submitNewPermissionAjax(ev) {
        if (ev) { ev.preventDefault(); ev.stopPropagation(); }
        if (!newPermissionForm) return false;

        // Clear previous error styles and messages
        clearValidationErrors();

        // Client-side validation
        const trim = (v) => (v ?? "").toString().trim();
        const errors = [];
        
        const permissionType = trim(document.getElementById('permissionType')?.value);
        const user = trim(document.getElementById('user')?.value);
        const group = trim(document.getElementById('group')?.value);
        const role = trim(document.getElementById('role')?.value);
        const documentType = trim(document.getElementById('documentType')?.value);

        // Required field validation
        if (!permissionType) errors.push("Permission type is required.");
        if (!role) errors.push("Role is required.");
        if (!documentType) errors.push("Document type is required.");
        
        // Conditional validation based on permission type
        if (permissionType === 'user' && !user) {
            errors.push("User selection is required.");
        }
        if (permissionType === 'group' && !group) {
            errors.push("Group selection is required.");
        }

        // Visual hinting for invalid fields
        const mark = (el, bad) => { 
            if (!el) return; 
            el.classList.toggle("border-red-500", !!bad); 
            el.classList.toggle("border-gray-300", !bad); 
        };
        
        mark(document.getElementById('permissionType'), !permissionType);
        mark(document.getElementById('role'), !role);
        mark(document.getElementById('documentType'), !documentType);
        mark(document.getElementById('user'), permissionType === 'user' && !user);
        mark(document.getElementById('group'), permissionType === 'group' && !group);

        if (errors.length > 0) {
            Swal.fire({
                icon: "error",
                title: "Please fix the following",
                html: `<div style="text-align:left">${errors.map(e => `• ${e}`).join('<br>')}</div>`,
                confirmButtonColor: "#2f855a",
            });
            // Focus on first invalid field
            const firstInvalid = [document.getElementById('permissionType'), document.getElementById('role'), document.getElementById('documentType'), document.getElementById('user'), document.getElementById('group')].find(el => el && el.classList.contains('border-red-500'));
            if (firstInvalid) firstInvalid.focus();
            return false;
        }

        const formData = new FormData(newPermissionForm);
        const url = '{{ route("permissions.store") }}';
        const submitBtn = newPermissionForm.querySelector('button[type="submit"]');
        const prevText = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...'; }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json().catch(() => ({}));

            if (response.ok && data.success === true) {
                // Keep modal open and show validation inside
                showNewPermissionModalSuccess('Permission created successfully.');
                // Also show page banner
                showPermissionSuccessBanner('Permission created successfully.');
                return false;
            }

            if (response.status === 422) {
                const errs = (data && data.errors) || {};
                displayValidationErrors(errs);
                return false;
            }

            const msg = (data && (data.message || data.error)) || 'Failed to create permission. Please try again.';
            Swal.fire({ icon: 'error', title: 'Error', text: msg, confirmButtonColor: '#2f855a' });
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: (error && error.message) || 'An error occurred while creating the permission.', confirmButtonColor: '#2f855a' });
        } finally {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = prevText; }
        }
        return false;
    }
    // Ensure global access for inline onsubmit
    window.submitNewPermissionAjax = submitNewPermissionAjax;

    // Final fallback: bind submit event directly
    if (newPermissionForm) {
        try { newPermissionForm.addEventListener('submit', submitNewPermissionAjax); } catch(_) {}
    }

    // Function to clear validation errors
    function clearValidationErrors() {
        // Clear error styles from form fields (both new and edit forms)
        const fields = [
            document.getElementById('permissionType'),
            document.getElementById('editPermissionType'),
            document.getElementById('user'),
            document.getElementById('editUser'),
            document.getElementById('group'),
            document.getElementById('editGroup'),
            document.getElementById('role'),
            document.getElementById('editRole'),
            document.getElementById('documentType'),
            document.getElementById('editDocumentType'),
            document.getElementById('notes'),
            document.getElementById('editNotes')
        ];
        
        fields.filter(Boolean).forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });

        // Remove existing error messages
        const existingErrors = document.querySelectorAll('.validation-error');
        existingErrors.forEach(error => error.remove());
    }

    // Function to display validation errors
    function displayValidationErrors(errors) {
        const fieldMap = {
            'permission_type': { 
                element: document.getElementById('permissionType') || document.getElementById('editPermissionType'), 
                label: 'Permission Type' 
            },
            'user': { 
                element: document.getElementById('user') || document.getElementById('editUser'), 
                label: 'User' 
            },
            'group': { 
                element: document.getElementById('group') || document.getElementById('editGroup'), 
                label: 'Group' 
            },
            'role': { 
                element: document.getElementById('role') || document.getElementById('editRole'), 
                label: 'Role' 
            },
            'document_type': { 
                element: document.getElementById('documentType') || document.getElementById('editDocumentType'), 
                label: 'Document Type' 
            },
            'notes': { 
                element: document.getElementById('notes') || document.getElementById('editNotes'), 
                label: 'Notes' 
            }
        };

        Object.keys(errors).forEach(fieldName => {
            const fieldInfo = fieldMap[fieldName];
            if (fieldInfo && fieldInfo.element) {
                // Add red border to field
                fieldInfo.element.classList.remove('border-gray-300');
                fieldInfo.element.classList.add('border-red-500');
                
                // Add error message below the field
                const errorDiv = document.createElement('div');
                errorDiv.className = 'validation-error text-red-500 text-xs mt-1';
                errorDiv.textContent = errors[fieldName][0];
                
                // Insert error message after the field
                const fieldContainer = fieldInfo.element.closest('div');
                if (fieldContainer) {
                    fieldContainer.appendChild(errorDiv);
                }
            }
        });

        // Show general error message if there are validation errors
        if (Object.keys(errors).length > 0) {
            const firstError = Object.values(errors).flat()[0] || 'Please correct the errors below and try again.';
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: firstError,
                confirmButtonColor: '#2f855a'
            });
        }
    }
    
    // Form submission is now handled by onclick="submitNewPermission()" button

    // Debug: Log element status
    console.log('New Permission Button:', newPermissionBtn);
    console.log('New Permission Modal:', newPermissionModal);
    console.log('New Permission Modal:', newPermissionModal);
    
    // Debug: Check if elements exist
    if (!newPermissionBtn) console.error('New Permission button not found!');
    if (!newPermissionModal) console.error('New Permission modal not found!');
    if (!newPermissionForm) console.warn('New Permission form not found!');
    
    // Test validation function
    console.log('Testing validation functions...');
    console.log('clearValidationErrors function:', typeof clearValidationErrors);
    console.log('displayValidationErrors function:', typeof displayValidationErrors);
    console.log('submitNewPermission function:', typeof window.submitNewPermission);
    
    // Fallback: ensure Save button submits the form
    document.addEventListener('DOMContentLoaded', function(){
        const saveBtn = document.getElementById('savePermissionBtn');
        const form = document.getElementById('newPermissionForm');
        if (saveBtn && form) {
            try {
                saveBtn.addEventListener('click', function(ev){
                    ev.preventDefault(); ev.stopPropagation();
                    try { form.requestSubmit ? form.requestSubmit() : form.submit(); } catch (_) { form.submit(); }
                });
            } catch(_) {}
        }
    });
    
    // Removed debug test button

    // Toggle user/group fields based on permission type
    function togglePermissionFields(type) {
        const groupLabel = document.querySelector('label[for="group"]');
        if (type === 'user') {
            userField.classList.remove('hidden');
            groupField.classList.add('hidden');
            if (groupLabel) groupLabel.textContent = 'Select Group *';
        } else if (type === 'group' || type === 'department') {
            userField.classList.add('hidden');
            groupField.classList.remove('hidden');
            if (groupLabel) groupLabel.textContent = (type === 'department') ? 'Select Department *' : 'Select Group *';
        } else {
            userField.classList.add('hidden');
            groupField.classList.add('hidden');
            if (groupLabel) groupLabel.textContent = 'Select Group *';
        }
    }

    // Toggle custom permissions based on role selection
    function toggleCustomPermissions(role) {
        if (role === 'custom') {
            customPermissions.classList.remove('hidden');
        } else {
            customPermissions.classList.add('hidden');
        }
    }

    // Show modal when New Permission button is clicked
    function handleNewPermissionClick(e) {
        console.log('New Permission button clicked', e);
        e.preventDefault();
        e.stopPropagation();
        
        console.log('Modal element before showing:', newPermissionModal);
        
        if (newPermissionModal) {
            console.log('Showing modal');
            
            // Reset form and clear validation errors
            if (newPermissionForm) {
                newPermissionForm.reset();
                clearValidationErrors();
            }
            
            // Reset form fields to default values
            if (permissionType) permissionType.value = 'user';
            if (roleSelect) roleSelect.value = 'admin';
            
            // Toggle fields based on default values
            togglePermissionFields('user');
            toggleCustomPermissions('admin');
            
            // Make sure the modal is visible
            newPermissionModal.style.display = 'flex';
            newPermissionModal.classList.add('active');
            // Force a reflow to ensure the display change takes effect
            void newPermissionModal.offsetWidth;
            // Add a class to make it visible
            newPermissionModal.style.opacity = '1';
            
            // Prevent scrolling on the body
            document.body.style.overflow = 'hidden';
            
            // Log the current state
            console.log('Modal classes:', newPermissionModal.className);
            console.log('Modal display:', window.getComputedStyle(newPermissionModal).display);
            console.log('Modal visibility:', window.getComputedStyle(newPermissionModal).visibility);
        } else {
            console.error('Modal element not found!');
        }
    }

    // Add click event listener to the button
    if (newPermissionBtn) {
        console.log('Adding click event to button');
        // Remove any existing event listeners to avoid duplicates
        const newBtn = newPermissionBtn.cloneNode(true);
        newPermissionBtn.parentNode.replaceChild(newBtn, newPermissionBtn);
        // Add the new event listener
        newBtn.addEventListener('click', handleNewPermissionClick);
        
        // Also try with jQuery if it's available
        if (typeof $ !== 'undefined') {
            console.log('jQuery is available, adding click handler');
            $(newBtn).on('click', handleNewPermissionClick);
        }
    } else {
        console.error('New Permission button not found!');
    }

    // Close modal when clicking outside or on close button
    function closeModal(modalId) {
        console.log('Closing modal:', modalId);
        const modal = document.getElementById(modalId);
        if (modal) {
            // Clear validation errors when closing modal
            if (modalId === 'newPermissionModal') {
                clearValidationErrors();
            }
            
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Re-enable scrolling
        }
    }

    // Event delegation for modal close buttons and overlay
    document.addEventListener('click', function(e) {
        console.log('Document click event:', e.target);
        
        // Check if clicking on modal overlay (outside the modal content)
        if (e.target.classList.contains('modal')) {
            console.log('Clicked on modal overlay');
            closeModal(e.target.id);
            return;
        }
        
        // Check if clicking on a close button or its children
        let closeButton = e.target.closest('[onclick^="closeModal"], .close-modal');
        if (!closeButton && e.target.matches('[onclick^="closeModal"], .close-modal')) {
            closeButton = e.target;
        }
        
        if (closeButton) {
            console.log('Clicked on close button or element:', closeButton);
            e.preventDefault();
            e.stopPropagation();
            
            let modalId = 'newPermissionModal';
            if (closeButton.hasAttribute('data-modal-id')) {
                modalId = closeButton.getAttribute('data-modal-id');
            } else if (closeButton.onclick && typeof closeButton.onclick === 'function') {
                // Try to extract modal ID from onclick handler
                const onclickText = closeButton.onclick.toString();
                const match = onclickText.match(/closeModal\s*\(['"]([^'"]+)['"]\)/);
                if (match && match[1]) {
                    modalId = match[1];
                }
            }
            
            console.log('Closing modal with ID:', modalId);
            closeModal(modalId);
            return;
        }
    }, true); // Use capture phase to ensure we catch the event
    
    // Also close modal with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && newPermissionModal && !newPermissionModal.classList.contains('hidden')) {
            closeModal('newPermissionModal');
        }
    });

    // Handle permission type change
    if (permissionType) {
        permissionType.addEventListener('change', (e) => {
            togglePermissionFields(e.target.value);
        });
    }

    // Handle role selection change
    if (roleSelect) {
        roleSelect.addEventListener('change', (e) => {
            toggleCustomPermissions(e.target.value);
        });
    }

    // Initialize form fields
    togglePermissionFields(permissionType ? permissionType.value : 'user');
    toggleCustomPermissions(roleSelect ? roleSelect.value : 'admin');

    // Search functionality
    function applySearch() {
        const searchTerm = searchInput.value.toLowerCase();

        permissionRows.forEach(row => {
            const role = row.querySelector('td:nth-child(2) span').textContent.toLowerCase();
            const permissionName = row.querySelector('td:nth-child(1) .text-gray-900').textContent.toLowerCase();
            const documentType = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            
            const matchesSearch = permissionName.includes(searchTerm) || 
                                documentType.includes(searchTerm) ||
                                role.includes(searchTerm);
            
            row.style.display = matchesSearch ? '' : 'none';
        });
    }
    
    // Add event listener for search input
    if (searchInput) {
        searchInput.addEventListener('input', applySearch);
        // Initialize search
        applySearch();
    }
</script>
</body>
</html>
