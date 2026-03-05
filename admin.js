/**
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * Master JavaScript — Dashboard & All Modules
 * Works with Tailwind CSS UI (matching Microfinancial UI reference)
 */

/* ═══════════ AUTH GUARD ═══════════ */
(function authGuard() {
  const depth = (window.location.pathname.match(/modules\/[^/]+\//g) || []).length;
  const prefix = depth > 0 ? '../../' : '';

  fetch(prefix + 'api/auth.php?action=check', { credentials: 'same-origin' })
    .then(r => r.json())
    .then(d => {
      if (!d.authenticated) {
        sessionStorage.removeItem('mf_auth');
        window.location.href = prefix + 'login.php';
      }
    })
    .catch(() => {
      if (!sessionStorage.getItem('mf_auth')) {
        window.location.href = prefix + 'login.php';
      }
    });
})();

document.addEventListener("DOMContentLoaded", () => {

  // ───── Real-Time Clock ─────
  const clockEl = document.getElementById("real-time-clock");
  const updateClock = () => {
    if (!clockEl) return;
    clockEl.textContent = new Date().toLocaleTimeString("en-US", {
      hour12: true, hour: "2-digit", minute: "2-digit", second: "2-digit"
    });
  };
  if (clockEl) { updateClock(); setInterval(updateClock, 1000); }

  // ───── User Dropdown (Tailwind animated) ─────
  const userBtn = document.getElementById("user-menu-button");
  const userDropdown = document.getElementById("user-menu-dropdown");

  const openDropdown = () => {
    if (!userDropdown) return;
    userDropdown.classList.remove("hidden");
    requestAnimationFrame(() => {
      userDropdown.classList.remove("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
      userDropdown.classList.add("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
    });
  };

  const closeDropdown = () => {
    if (!userDropdown) return;
    userDropdown.classList.add("opacity-0", "translate-y-2", "scale-95", "pointer-events-none");
    userDropdown.classList.remove("opacity-100", "translate-y-0", "scale-100", "pointer-events-auto");
    setTimeout(() => userDropdown.classList.add("hidden"), 100);
  };

  if (userBtn && userDropdown) {
    userBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      const isHidden = userDropdown.classList.contains("hidden");
      if (isHidden) openDropdown();
      else closeDropdown();
    });
    document.addEventListener("click", () => {
      if (userDropdown && !userDropdown.classList.contains("hidden")) closeDropdown();
    });
  }

  // ───── Mobile Sidebar Toggle + Overlay ─────
  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebar-overlay");
  const mobileBtn = document.getElementById("mobile-menu-btn");

  const openSidebar = () => {
    if (!sidebar || !overlay) return;
    sidebar.classList.remove("-translate-x-full");
    overlay.classList.remove("hidden");
    requestAnimationFrame(() => overlay.classList.remove("opacity-0"));
  };

  const closeSidebar = () => {
    if (!sidebar || !overlay) return;
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("opacity-0");
    setTimeout(() => overlay.classList.add("hidden"), 150);
  };

  if (mobileBtn && sidebar && overlay) {
    mobileBtn.addEventListener("click", () => {
      const closed = sidebar.classList.contains("-translate-x-full");
      if (closed) openSidebar();
      else closeSidebar();
    });
    overlay.addEventListener("click", closeSidebar);
  }

  // ───── Sidebar Submenu Toggles ─────
  const currentFile = window.location.pathname.split('/').pop() || 'dashboard.php';

  // Active/inactive class sets for module buttons
  const MOD_ACTIVE_CLS = ['bg-brand-primary', 'text-white', 'shadow', 'font-semibold'];
  const MOD_INACTIVE_CLS = ['text-gray-700', 'hover:bg-green-50', 'hover:text-brand-primary', 'hover:translate-x-1'];

  function setModuleBtnActive(btn, active) {
    const iconEl = btn.querySelector('span > span:first-child');
    const arrow = btn.querySelector('.arrow');
    if (active) {
      MOD_INACTIVE_CLS.forEach(c => btn.classList.remove(c));
      MOD_ACTIVE_CLS.forEach(c => btn.classList.add(c));
      if (iconEl) { iconEl.classList.remove('bg-emerald-50'); iconEl.classList.add('bg-white/15'); }
      if (arrow) { arrow.classList.remove('text-emerald-400'); arrow.classList.add('text-white'); }
    } else {
      MOD_ACTIVE_CLS.forEach(c => btn.classList.remove(c));
      MOD_INACTIVE_CLS.forEach(c => btn.classList.add(c));
      if (iconEl) { iconEl.classList.remove('bg-white/15'); iconEl.classList.add('bg-emerald-50'); }
      if (arrow) { arrow.classList.remove('text-white'); arrow.classList.add('text-emerald-400'); }
    }
  }

  document.querySelectorAll("[data-submenu]").forEach(btn => {
    const submenu = document.getElementById(btn.dataset.submenu);
    const arrow = btn.querySelector(".arrow");
    if (!submenu) return;

    // Initialize arrow state if submenu is already open
    if (submenu.classList.contains("is-open") && arrow) {
      arrow.classList.add("rotate-180");
    }

    btn.addEventListener("click", (e) => {
      e.preventDefault();

      // Close all OTHER submenus and deactivate their buttons
      document.querySelectorAll("[data-submenu]").forEach(otherBtn => {
        if (otherBtn === btn) return;
        const otherMenu = document.getElementById(otherBtn.dataset.submenu);
        const otherArrow = otherBtn.querySelector(".arrow");
        if (otherMenu && otherMenu.classList.contains("is-open")) {
          otherMenu.classList.remove("is-open");
          if (otherArrow) otherArrow.classList.remove("rotate-180");
        }
        setModuleBtnActive(otherBtn, false);
      });

      // Toggle this submenu
      const isNowOpen = !submenu.classList.contains("is-open");
      submenu.classList.toggle("is-open");
      if (arrow) arrow.classList.toggle("rotate-180");

      // Set this button active/inactive based on open state
      setModuleBtnActive(btn, isNowOpen);
    });
  });

  // ───── Sidebar Submodule Active State (hash-driven) ─────
  const ACTIVE_SUB = 'flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-brand-primary bg-green-50 font-semibold transition-all duration-200';
  const INACTIVE_SUB = 'flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-brand-primary transition-all duration-200 hover:translate-x-1';

  function updateActiveSublink() {
    const hash = location.hash.replace('#', '') || '';
    const sublinks = document.querySelectorAll('.sidebar-sublink');
    // Default map: first sublink in each submenu is active when no hash or unknown hash
    const submenuDefaults = {};
    document.querySelectorAll('[id$="-submenu"]').forEach(sm => {
      const first = sm.querySelector('.sidebar-sublink');
      if (first) submenuDefaults[sm.id] = first;
    });

    let matched = false;
    sublinks.forEach(link => {
      const linkHash = link.getAttribute('data-hash') || '';
      const subtab = link.getAttribute('data-subtab') || '';
      let isActive = false;
      if (hash && linkHash === hash) {
        // If link has data-subtab, check if that sub-tab is currently active
        if (subtab) {
          const subtabEl = document.getElementById('subtab-' + subtab);
          isActive = subtabEl ? subtabEl.classList.contains('active') : (subtab === 'registered');
        } else {
          isActive = true;
        }
      }
      if (isActive) matched = true;

      // Update link classes
      link.className = 'sidebar-sublink ' + (isActive ? ACTIVE_SUB : INACTIVE_SUB);

      // Update the arrow SVG color inside the link
      const svg = link.querySelector('svg');
      if (svg) {
        svg.classList.remove('text-brand-primary', 'text-gray-400', 'group-hover:text-brand-primary');
        svg.classList.add(isActive ? 'text-brand-primary' : 'text-gray-400');
      }
    });

    // If no hash matched but we're on a module page, highlight the first sublink of the open submenu
    if (!matched) {
      document.querySelectorAll('.submenu.is-open').forEach(sm => {
        const first = sm.querySelector('.sidebar-sublink');
        if (first) {
          first.className = 'sidebar-sublink ' + ACTIVE_SUB;
          const svg = first.querySelector('svg');
          if (svg) {
            svg.classList.remove('text-gray-400');
            svg.classList.add('text-brand-primary');
          }
        }
      });
    }
  }

  // Run on load and on hash change
  updateActiveSublink();
  window.addEventListener('hashchange', updateActiveSublink);

  // Click handler for sublinks — optimize same-page navigation and update active state
  document.querySelectorAll('.sidebar-sublink').forEach(link => {
    link.addEventListener('click', (e) => {
      const href = link.getAttribute('href') || '';
      const parts = href.split('#');
      const hashPart = parts[1] || '';
      const filePart = parts[0].split('/').pop();

      // If already on the same module page, prevent full reload and just update hash
      if (filePart === currentFile && hashPart) {
        e.preventDefault();
        if (location.hash !== '#' + hashPart) {
          location.hash = '#' + hashPart;
        } else {
          // Hash is the same — manually trigger showSection if available
          if (typeof showSection === 'function') showSection('#' + hashPart);
        }
      }

      // Handle sub-tab switching for links with data-subtab
      const subtab = link.getAttribute('data-subtab');
      if (subtab) {
        setTimeout(() => {
          // Activate correct sub-tab inside the tab
          const container = document.getElementById('tab-registration');
          if (container) {
            const btn = container.querySelector(`.sub-tab[onclick*="'${subtab}'"]`);
            if (btn && typeof switchSubTab === 'function') switchSubTab(btn, subtab);
          }
        }, 80);
      }
      // Small delay to let hash change propagate, then update
      setTimeout(updateActiveSublink, 20);
    });
  });

  // ───── Modal Helpers ─────
  window.openModal = (id) => {
    const m = document.getElementById(id);
    if (m) { m.classList.add("open"); document.body.style.overflow = "hidden"; }
  };

  window.closeModal = (id) => {
    const m = document.getElementById(id);
    if (m) { m.classList.remove("open"); document.body.style.overflow = ""; }
  };

  document.querySelectorAll(".modal-overlay").forEach(m => {
    m.addEventListener("click", (e) => {
      if (e.target === m) { m.classList.remove("open"); document.body.style.overflow = ""; }
    });
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      document.querySelectorAll(".modal-overlay.open").forEach(m => {
        m.classList.remove("open");
        document.body.style.overflow = "";
      });
    }
  });

  // ───── API Helper ─────
  window.api = async (endpoint, params = {}) => {
    const url = new URL(endpoint, window.location.origin);
    Object.entries(params).forEach(([k, v]) => { if (v !== undefined) url.searchParams.set(k, v); });
    try {
      const res = await fetch(url.toString());
      return await res.json();
    } catch (err) {
      console.error("API Error:", err);
      return { error: err.message };
    }
  };

  window.apiPost = async (endpoint, body = {}) => {
    try {
      const res = await fetch(endpoint, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(body)
      });
      return await res.json();
    } catch (err) {
      console.error("API Error:", err);
      return { error: err.message };
    }
  };

  // ───── Format helpers ─────
  window.formatDate = (str) => {
    if (!str) return "—";
    return new Date(str).toLocaleDateString("en-US", { year: "numeric", month: "short", day: "numeric" });
  };

  window.formatDateTime = (str) => {
    if (!str) return "—";
    return new Date(str).toLocaleString("en-US", {
      year: "numeric", month: "short", day: "numeric",
      hour: "2-digit", minute: "2-digit"
    });
  };

  window.formatCurrency = (val) => {
    return new Intl.NumberFormat("en-PH", { style: "currency", currency: "PHP" }).format(val || 0);
  };

  window.statusBadge = (status) => {
    const map = {
      pending: "badge-amber", approved: "badge-green", rejected: "badge-red",
      cancelled: "badge-gray", completed: "badge-blue", active: "badge-green",
      archived: "badge-gray", draft: "badge-amber", deleted: "badge-red",
      processing: "badge-blue", failed: "badge-red", not_applicable: "badge-gray",
      open: "badge-amber", in_progress: "badge-blue", pending_review: "badge-purple",
      resolved: "badge-green", closed: "badge-gray", appealed: "badge-red",
      compliant: "badge-green", non_compliant: "badge-red", exempted: "badge-gray",
      expired: "badge-red", terminated: "badge-red", renewed: "badge-teal",
      under_review: "badge-purple", pre_registered: "badge-amber",
      checked_in: "badge-green", checked_out: "badge-gray", no_show: "badge-red",
      excellent: "badge-green", good: "badge-blue", fair: "badge-amber",
      needs_repair: "badge-red", retired: "badge-gray",
      available: "badge-green", occupied: "badge-blue", maintenance: "badge-amber",
    };
    const cls = map[status] || "badge-gray";
    const label = (status || "unknown").replace(/_/g, " ");
    return `<span class="badge ${cls}">${label}</span>`;
  };

  // ───── QR Code Generation ─────
  window.generateQR = (data, size = 200) => {
    return `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(data)}&size=${size}x${size}&color=059669`;
  };

  // ───── Notification Bell ─────
  const depth = (window.location.pathname.match(/modules\/[^/]+\//g) || []).length;
  const apiPrefix = depth > 0 ? '../../' : '';

  const bellBtn  = document.getElementById('notification-bell');
  const badge    = document.getElementById('notif-badge');
  let notifPanel = document.getElementById('notif-panel');

  // Build notification dropdown panel (once)
  if (bellBtn && !notifPanel) {
    notifPanel = document.createElement('div');
    notifPanel.id = 'notif-panel';
    notifPanel.className = 'hidden absolute right-0 mt-3 w-80 max-h-96 overflow-y-auto bg-white rounded-xl shadow-lg border border-gray-100 z-50';
    notifPanel.innerHTML = `
      <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
        <span class="text-sm font-bold text-gray-800">Notifications</span>
        <button id="mark-all-read" class="text-xs text-emerald-600 hover:underline font-medium">Mark all read</button>
      </div>
      <div id="notif-list" class="divide-y divide-gray-50"></div>
      <div id="notif-empty" class="hidden px-4 py-6 text-center text-sm text-gray-400">No notifications</div>`;
    bellBtn.parentElement.style.position = 'relative';
    bellBtn.parentElement.appendChild(notifPanel);
  }

  const moduleIcons = { facilities: '🏢', documents: '📄', legal: '⚖️', visitors: '👥', system: '🔔' };

  async function loadNotifications() {
    try {
      const res = await fetch(apiPrefix + 'api/notifications.php?action=list', { credentials: 'same-origin' });
      const json = await res.json();
      const items = json.data || [];
      const listEl  = document.getElementById('notif-list');
      const emptyEl = document.getElementById('notif-empty');
      if (!listEl) return;

      const unread = items.filter(n => !n.is_read).length;
      if (badge) badge.style.display = unread > 0 ? '' : 'none';

      if (items.length === 0) {
        listEl.innerHTML = '';
        if (emptyEl) emptyEl.classList.remove('hidden');
        return;
      }
      if (emptyEl) emptyEl.classList.add('hidden');

      listEl.innerHTML = items.map(n => {
        const icon = moduleIcons[n.module] || '🔔';
        const time = window.formatDateTime ? window.formatDateTime(n.created_at) : n.created_at;
        const bg = n.is_read ? '' : 'bg-emerald-50/40';
        return `<div class="px-4 py-3 hover:bg-gray-50 cursor-pointer transition ${bg}" data-nid="${n.notification_id}" data-link="${n.link || ''}">
          <div class="flex items-start gap-2">
            <span class="text-lg">${icon}</span>
            <div class="flex-1 min-w-0">
              <div class="text-sm font-semibold text-gray-800 ${n.is_read ? '' : 'text-emerald-700'}">${n.title}</div>
              <div class="text-xs text-gray-500 mt-0.5 line-clamp-2">${n.message}</div>
              <div class="text-[10px] text-gray-400 mt-1">${time}</div>
            </div>
            ${n.is_read ? '' : '<span class="mt-1 w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>'}
          </div>
        </div>`;
      }).join('');

      // Click to mark read & navigate
      listEl.querySelectorAll('[data-nid]').forEach(el => {
        el.addEventListener('click', async () => {
          const nid = el.dataset.nid;
          await fetch(apiPrefix + 'api/notifications.php?action=mark_read', {
            method: 'POST', credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ notification_id: parseInt(nid) })
          });
          el.querySelector('.bg-emerald-500')?.remove();
          el.classList.remove('bg-emerald-50/40');
          loadNotifications();
          if (el.dataset.link) window.location.href = apiPrefix + el.dataset.link.replace(/^\//, '');
        });
      });
    } catch (err) { console.error('Notification load error:', err); }
  }

  if (bellBtn && notifPanel) {
    bellBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isHidden = notifPanel.classList.contains('hidden');
      notifPanel.classList.toggle('hidden');
      if (isHidden) loadNotifications();
    });

    document.addEventListener('click', (e) => {
      if (!notifPanel.contains(e.target) && e.target !== bellBtn) {
        notifPanel.classList.add('hidden');
      }
    });

    // Mark all read
    document.addEventListener('click', async (e) => {
      if (e.target.id === 'mark-all-read') {
        await fetch(apiPrefix + 'api/notifications.php?action=mark_all_read', {
          method: 'POST', credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json' }
        });
        loadNotifications();
      }
    });

    // Initial badge check
    (async () => {
      try {
        const res = await fetch(apiPrefix + 'api/notifications.php?action=unread_count', { credentials: 'same-origin' });
        const json = await res.json();
        if (badge) badge.style.display = (json.cnt > 0) ? '' : 'none';
      } catch (_) {}
    })();
  }

  // ───── Profile Modal ─────
  window.openProfileModal = () => {
    let modal = document.getElementById('profile-modal');
    if (!modal) {
      modal = document.createElement('div');
      modal.id = 'profile-modal';
      modal.className = 'modal-overlay fixed inset-0 z-[9999] flex items-center justify-center bg-black/40';
      modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 overflow-hidden max-h-[90vh] flex flex-col">
          <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-6 text-white">
            <div class="flex items-center gap-4">
              <div id="prof-avatar" class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center text-2xl font-bold"></div>
              <div><div id="prof-name" class="text-lg font-bold"></div><div id="prof-role" class="text-sm opacity-90"></div></div>
            </div>
          </div>

          <!-- Tab Navigation -->
          <div class="flex border-b border-gray-200">
            <button id="prof-tab-info" onclick="switchProfileTab('info')" class="flex-1 py-3 text-sm font-semibold text-emerald-600 border-b-2 border-emerald-600 transition">Profile Info</button>
            <button id="prof-tab-password" onclick="switchProfileTab('password')" class="flex-1 py-3 text-sm font-semibold text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition">Change Password</button>
          </div>

          <div class="overflow-y-auto flex-1">
            <!-- Profile Info Tab -->
            <div id="prof-panel-info" class="px-6 py-5 space-y-4">
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label class="text-xs text-gray-400 uppercase font-medium">Employee ID</label>
                  <div id="prof-eid" class="text-sm font-semibold text-gray-800 mt-1 bg-gray-50 px-3 py-2 rounded-lg"></div>
                </div>
                <div>
                  <label class="text-xs text-gray-400 uppercase font-medium">Department</label>
                  <div id="prof-dept" class="text-sm font-semibold text-gray-800 mt-1 bg-gray-50 px-3 py-2 rounded-lg"></div>
                </div>
              </div>
              <div>
                <label class="text-xs text-gray-400 uppercase font-medium">First Name</label>
                <input id="prof-fname" type="text" class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-500 outline-none transition" />
              </div>
              <div>
                <label class="text-xs text-gray-400 uppercase font-medium">Last Name</label>
                <input id="prof-lname" type="text" class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-500 outline-none transition" />
              </div>
              <div>
                <label class="text-xs text-gray-400 uppercase font-medium">Email Address</label>
                <input id="prof-email" type="email" class="w-full mt-1 px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-500 outline-none transition" />
              </div>
              <div id="prof-info-msg" class="hidden text-sm rounded-lg px-3 py-2"></div>
            </div>

            <!-- Change Password Tab -->
            <div id="prof-panel-password" class="px-6 py-5 space-y-4 hidden">
              <div>
                <label class="text-xs text-gray-400 uppercase font-medium">Current Password</label>
                <div class="relative mt-1">
                  <input id="prof-cur-pw" type="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-500 outline-none transition pr-10" placeholder="Enter current password" />
                  <button type="button" onclick="togglePwVis('prof-cur-pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                </div>
              </div>
              <div>
                <label class="text-xs text-gray-400 uppercase font-medium">New Password</label>
                <div class="relative mt-1">
                  <input id="prof-new-pw" type="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-500 outline-none transition pr-10" placeholder="Minimum 6 characters" />
                  <button type="button" onclick="togglePwVis('prof-new-pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                </div>
              </div>
              <div>
                <label class="text-xs text-gray-400 uppercase font-medium">Confirm New Password</label>
                <div class="relative mt-1">
                  <input id="prof-confirm-pw" type="password" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-300 focus:border-emerald-500 outline-none transition pr-10" placeholder="Re-enter new password" />
                  <button type="button" onclick="togglePwVis('prof-confirm-pw', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></button>
                </div>
              </div>
              <div id="prof-pw-msg" class="hidden text-sm rounded-lg px-3 py-2"></div>
            </div>
          </div>

          <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
            <button onclick="closeModal('profile-modal')" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Close</button>
            <button id="prof-save-btn" onclick="saveProfile()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              Save Changes
            </button>
          </div>
        </div>`;
      document.body.appendChild(modal);
      modal.addEventListener('click', (e) => { if (e.target === modal) closeModal('profile-modal'); });
    }

    // Populate from embedded data
    const u = window.__mf_user || {};
    document.getElementById('prof-avatar').textContent = u.initial || '?';
    document.getElementById('prof-name').textContent = u.name || 'Unknown';
    document.getElementById('prof-role').textContent = (u.role || '').replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
    document.getElementById('prof-eid').textContent = u.employee_id || '—';
    document.getElementById('prof-dept').textContent = u.department || '—';

    // Split name into first/last for editable fields
    const nameParts = (u.name || '').split(' ');
    document.getElementById('prof-fname').value = u.first_name || nameParts[0] || '';
    document.getElementById('prof-lname').value = u.last_name || nameParts.slice(1).join(' ') || '';
    document.getElementById('prof-email').value = u.email || '';

    // Reset password fields
    document.getElementById('prof-cur-pw').value = '';
    document.getElementById('prof-new-pw').value = '';
    document.getElementById('prof-confirm-pw').value = '';

    // Reset messages
    const infoMsg = document.getElementById('prof-info-msg');
    const pwMsg = document.getElementById('prof-pw-msg');
    if (infoMsg) { infoMsg.classList.add('hidden'); infoMsg.textContent = ''; }
    if (pwMsg) { pwMsg.classList.add('hidden'); pwMsg.textContent = ''; }

    // Show info tab by default
    switchProfileTab('info');
    openModal('profile-modal');
  };

  // Toggle password visibility
  window.togglePwVis = (inputId, btn) => {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.innerHTML = isPassword
      ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>'
      : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
  };

  // Profile tab switching
  window.switchProfileTab = (tab) => {
    const infoTab = document.getElementById('prof-tab-info');
    const pwTab = document.getElementById('prof-tab-password');
    const infoPanel = document.getElementById('prof-panel-info');
    const pwPanel = document.getElementById('prof-panel-password');
    const saveBtn = document.getElementById('prof-save-btn');
    if (tab === 'info') {
      infoTab.className = 'flex-1 py-3 text-sm font-semibold text-emerald-600 border-b-2 border-emerald-600 transition';
      pwTab.className = 'flex-1 py-3 text-sm font-semibold text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition';
      infoPanel.classList.remove('hidden');
      pwPanel.classList.add('hidden');
      saveBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save Changes';
    } else {
      pwTab.className = 'flex-1 py-3 text-sm font-semibold text-emerald-600 border-b-2 border-emerald-600 transition';
      infoTab.className = 'flex-1 py-3 text-sm font-semibold text-gray-400 border-b-2 border-transparent hover:text-gray-600 transition';
      pwPanel.classList.remove('hidden');
      infoPanel.classList.add('hidden');
      saveBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg> Update Password';
    }
  };

  // Save profile or change password
  window.saveProfile = async () => {
    const activeTab = document.getElementById('prof-panel-info').classList.contains('hidden') ? 'password' : 'info';
    const saveBtn = document.getElementById('prof-save-btn');
    const origText = saveBtn.innerHTML;
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Saving…';

    try {
      if (activeTab === 'info') {
        const firstName = document.getElementById('prof-fname').value.trim();
        const lastName = document.getElementById('prof-lname').value.trim();
        const email = document.getElementById('prof-email').value.trim();
        const msgEl = document.getElementById('prof-info-msg');

        if (!firstName) { showProfileMsg(msgEl, 'First name is required.', false); saveBtn.disabled = false; saveBtn.innerHTML = origText; return; }
        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showProfileMsg(msgEl, 'Please enter a valid email address.', false); saveBtn.disabled = false; saveBtn.innerHTML = origText; return; }

        const res = await fetch(apiPrefix + 'api/auth.php?action=update_profile', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify({ first_name: firstName, last_name: lastName, email })
        });
        const data = await res.json();
        if (data.success) {
          // Update window.__mf_user
          window.__mf_user.first_name = firstName;
          window.__mf_user.last_name = lastName;
          window.__mf_user.name = firstName + (lastName ? ' ' + lastName : '');
          window.__mf_user.email = email;
          window.__mf_user.initial = firstName.charAt(0).toUpperCase();

          // Update header display
          document.querySelectorAll('[data-user-name]').forEach(el => el.textContent = window.__mf_user.name);
          document.querySelectorAll('[data-user-initial]').forEach(el => el.textContent = window.__mf_user.initial);
          document.getElementById('prof-name').textContent = window.__mf_user.name;
          document.getElementById('prof-avatar').textContent = window.__mf_user.initial;

          showProfileMsg(msgEl, 'Profile updated successfully!', true);
          Swal.fire({ icon: 'success', title: 'Profile Updated', text: 'Your profile has been saved.', timer: 2000, showConfirmButton: false });
        } else {
          showProfileMsg(msgEl, data.message || 'Failed to update profile.', false);
        }
      } else {
        // Change password
        const curPw = document.getElementById('prof-cur-pw').value;
        const newPw = document.getElementById('prof-new-pw').value;
        const confirmPw = document.getElementById('prof-confirm-pw').value;
        const msgEl = document.getElementById('prof-pw-msg');

        if (!curPw) { showProfileMsg(msgEl, 'Current password is required.', false); saveBtn.disabled = false; saveBtn.innerHTML = origText; return; }
        if (!newPw || newPw.length < 6) { showProfileMsg(msgEl, 'New password must be at least 6 characters.', false); saveBtn.disabled = false; saveBtn.innerHTML = origText; return; }
        if (newPw !== confirmPw) { showProfileMsg(msgEl, 'New passwords do not match.', false); saveBtn.disabled = false; saveBtn.innerHTML = origText; return; }

        const res = await fetch(apiPrefix + 'api/auth.php?action=change_password', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          credentials: 'same-origin',
          body: JSON.stringify({ current_password: curPw, new_password: newPw })
        });
        const data = await res.json();
        if (data.success) {
          document.getElementById('prof-cur-pw').value = '';
          document.getElementById('prof-new-pw').value = '';
          document.getElementById('prof-confirm-pw').value = '';
          showProfileMsg(msgEl, 'Password changed successfully!', true);
          Swal.fire({ icon: 'success', title: 'Password Changed', text: 'Your password has been updated.', timer: 2000, showConfirmButton: false });
        } else {
          showProfileMsg(msgEl, data.message || 'Failed to change password.', false);
        }
      }
    } catch (err) {
      console.error('Profile save error:', err);
      Swal.fire({ icon: 'error', title: 'Error', text: 'An unexpected error occurred. Please try again.' });
    } finally {
      saveBtn.disabled = false;
      saveBtn.innerHTML = origText;
    }
  };

  function showProfileMsg(el, text, success) {
    if (!el) return;
    el.classList.remove('hidden', 'bg-emerald-50', 'text-emerald-700', 'bg-red-50', 'text-red-700');
    el.classList.add(success ? 'bg-emerald-50' : 'bg-red-50', success ? 'text-emerald-700' : 'text-red-700');
    el.textContent = text;
  }

  // ───── Settings Modal ─────
  const SETTINGS_KEY = 'mf_admin_settings';

  function loadSettings() {
    try { return JSON.parse(localStorage.getItem(SETTINGS_KEY)) || {}; } catch(e) { return {}; }
  }

  function saveSettings(settings) {
    localStorage.setItem(SETTINGS_KEY, JSON.stringify(settings));
  }

  // Apply settings on page load
  function applySettings() {
    try {
      const s = loadSettings();
      if (s.compactView) document.body.classList.add('compact-view');
      else document.body.classList.remove('compact-view');
      if (s.desktopNotifications && typeof Notification !== 'undefined' && Notification.permission === 'granted') {
        window.__desktopNotifsEnabled = true;
      }
    } catch (e) { console.warn('Settings apply error:', e); }
  }
  applySettings();

  window.openSettingsModal = () => {
    let modal = document.getElementById('settings-modal');
    if (!modal) {
      modal = document.createElement('div');
      modal.id = 'settings-modal';
      modal.className = 'modal-overlay fixed inset-0 z-[9999] flex items-center justify-center bg-black/40';
      modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
              <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              Settings
            </h3>
          </div>
          <div class="px-6 py-5 space-y-5">
            <div class="flex items-center justify-between">
              <div><div class="text-sm font-semibold text-gray-700">Email Notifications</div><div class="text-xs text-gray-400">Receive alerts via email</div></div>
              <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" id="set-email-notif" class="sr-only peer"><div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-emerald-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div></label>
            </div>
            <div class="flex items-center justify-between">
              <div><div class="text-sm font-semibold text-gray-700">Desktop Notifications</div><div class="text-xs text-gray-400">Browser push alerts</div></div>
              <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" id="set-desktop-notif" class="sr-only peer"><div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-emerald-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div></label>
            </div>
            <div class="flex items-center justify-between">
              <div><div class="text-sm font-semibold text-gray-700">Compact View</div><div class="text-xs text-gray-400">Reduce spacing in tables</div></div>
              <label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" id="set-compact-view" class="sr-only peer"><div class="w-10 h-5 bg-gray-200 peer-focus:ring-2 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:bg-emerald-600 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all"></div></label>
            </div>
          </div>
          <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3">
            <button onclick="closeModal('settings-modal')" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">Cancel</button>
            <button onclick="saveSettingsFromModal()" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-medium transition flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              Save
            </button>
          </div>
        </div>`;
      document.body.appendChild(modal);
      modal.addEventListener('click', (e) => { if (e.target === modal) closeModal('settings-modal'); });
    }

    // Load current settings into toggles
    const s = loadSettings();
    document.getElementById('set-email-notif').checked = s.emailNotifications !== false; // default on
    document.getElementById('set-desktop-notif').checked = !!s.desktopNotifications;
    document.getElementById('set-compact-view').checked = !!s.compactView;

    openModal('settings-modal');
  };

  window.saveSettingsFromModal = async () => {
    const emailNotif = document.getElementById('set-email-notif').checked;
    const desktopNotif = document.getElementById('set-desktop-notif').checked;
    const compactView = document.getElementById('set-compact-view').checked;

    // Request browser notification permission if enabling desktop notifications
    if (desktopNotif && 'Notification' in window && Notification.permission === 'default') {
      const perm = await Notification.requestPermission();
      if (perm !== 'granted') {
        Swal.fire({ icon: 'info', title: 'Permission Denied', text: 'Browser notification permission was not granted. Desktop notifications will not work.', timer: 3000, showConfirmButton: false });
      }
    }

    saveSettings({ emailNotifications: emailNotif, desktopNotifications: desktopNotif, compactView });
    applySettings();
    closeModal('settings-modal');
    Swal.fire({ icon: 'success', title: 'Settings Saved', text: 'Your preferences have been saved.', timer: 1500, showConfirmButton: false });
  };

  // ───── Logout Handler ─────
  document.querySelectorAll('.logout').forEach(link => {
    link.addEventListener('click', async (e) => {
      e.preventDefault();
      e.stopPropagation();

      // Guard: if SweetAlert2 not loaded, use native confirm
      if (typeof Swal === 'undefined') {
        if (!confirm('Are you sure you want to log out?')) return;
      } else {
        const result = await Swal.fire({
          title: 'Log Out?',
          text: 'Are you sure you want to end your session?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#059669',
          cancelButtonColor: '#6B7280',
          confirmButtonText: 'Yes, log out',
          cancelButtonText: 'Cancel',
          reverseButtons: true,
          customClass: { popup: 'rounded-2xl' }
        });
        if (!result.isConfirmed) return;
      }

      try {
        await fetch(apiPrefix + 'api/auth.php?action=logout', { credentials: 'same-origin' });
      } catch (_) {}
      sessionStorage.removeItem('mf_auth');

      // Show success message before redirect
      if (typeof Swal !== 'undefined') {
        await Swal.fire({
          icon: 'success',
          title: 'Logged Out Successfully',
          text: 'You have been securely logged out. Redirecting to login page...',
          timer: 2000,
          timerProgressBar: true,
          showConfirmButton: false,
          allowOutsideClick: false,
          customClass: { popup: 'rounded-2xl' }
        });
      }
      window.location.href = apiPrefix + 'login.php';
    });
  });

  // ───── Sidebar Stats (fetch for dashboard/modules to use) ─────
  async function loadSidebarCounts() {
    try {
      const [fac, doc, leg, vis] = await Promise.all([
        fetch(apiPrefix + 'api/facilities.php?action=dashboard_stats').then(r => r.json()).catch(() => ({})),
        fetch(apiPrefix + 'api/documents.php?action=dashboard_stats').then(r => r.json()).catch(() => ({})),
        fetch(apiPrefix + 'api/legal.php?action=dashboard_stats').then(r => r.json()).catch(() => ({})),
        fetch(apiPrefix + 'api/visitors.php?action=dashboard_stats').then(r => r.json()).catch(() => ({}))
      ]);
      // Store stats globally for modules to use
      window.__sidebarStats = { fac, doc, leg, vis };
    } catch(e) {
      console.warn('Sidebar stats load error:', e);
    }
  }

  // Load sidebar stats on page load
  loadSidebarCounts();

  // Expose for modules to refresh after data changes
  window.refreshSidebarCounts = loadSidebarCounts;

}); // end DOMContentLoaded
