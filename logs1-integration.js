/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * Logs1 (Logistics) Integration — JavaScript Service Module
 * ════════════════════════════════════════════════════════════════════
 *
 * Frontend helper for calling the Logs1 API bridge (/api/logs1.php).
 * Provides typed methods for every integration endpoint with
 * built-in error handling, caching, and loading state management.
 *
 * Logs1 = Source of Truth for:
 *   - PSM: Procurement, Purchase Orders, Vendors, Budgets
 *   - SWS: Warehouse Inventory, Stock Levels, Room Requests
 *   - PLT: Project Logistics, Milestones, Dispatches
 *   - DTLR: Document Tracking, Logistics Audit Trail
 *   - ALMS: Asset Lifecycle, Maintenance Records
 *
 * Usage:
 *   <script src="logs1-integration.js"></script>
 *   <script>
 *     const purchases = await logs1.getPurchases();
 *     const vendors   = await logs1.getVendors({ search: 'Office' });
 *     const budget    = await logs1.getBudget();
 *   </script>
 * ════════════════════════════════════════════════════════════════════
 */

class Logs1Integration {
  /**
   * @param {Object} opts
   * @param {string} opts.baseUrl  - Base URL for the Logs1 bridge API (auto-detected)
   * @param {number} opts.cacheTTL - Cache time-to-live in ms (default: 2 min)
   */
  constructor(opts = {}) {
    const pathParts = window.location.pathname.split('/');
    const adminIdx = pathParts.findIndex(p => p.toLowerCase() === 'admin');
    const basePath = adminIdx >= 0
      ? pathParts.slice(0, adminIdx + 1).join('/')
      : '/Admin';

    this.baseUrl  = opts.baseUrl || `${window.location.origin}${basePath}/api/logs1.php`;
    this.cacheTTL = opts.cacheTTL ?? 120_000;
    this._cache   = new Map();
    this._alive   = null;
  }

  // ─── Core HTTP Helper ─────────────────────────────────────────

  async call(action, params = {}, opts = {}) {
    const method = (opts.method || 'GET').toUpperCase();
    const useCache = opts.cache !== false && method === 'GET';
    const cacheKey = `${action}:${JSON.stringify(params)}`;

    if (useCache && this._cache.has(cacheKey)) {
      const cached = this._cache.get(cacheKey);
      if (Date.now() - cached.ts < this.cacheTTL) return cached.data;
      this._cache.delete(cacheKey);
    }

    const url = new URL(this.baseUrl, window.location.origin);
    url.searchParams.set('action', action);

    if (method === 'GET') {
      Object.entries(params).forEach(([k, v]) => {
        if (v !== undefined && v !== null && v !== '') {
          url.searchParams.set(k, v);
        }
      });
    }

    const fetchOpts = {
      method,
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json' },
    };

    if (method !== 'GET' && Object.keys(params).length) {
      fetchOpts.headers['Content-Type'] = 'application/json';
      fetchOpts.body = JSON.stringify(params);
    }

    if (opts.signal) fetchOpts.signal = opts.signal;

    try {
      const res = await fetch(url.toString(), fetchOpts);
      const json = await res.json();

      if (!res.ok) {
        throw new Logs1Error(json.error || `HTTP ${res.status}`, res.status, json);
      }

      if (useCache && json.success) {
        this._cache.set(cacheKey, { ts: Date.now(), data: json });
      }

      return json;
    } catch (err) {
      if (err instanceof Logs1Error) throw err;
      throw new Logs1Error(err.message || 'Network error', 0, null);
    }
  }

  clearCache() { this._cache.clear(); }

  // ─── Health / Connectivity ─────────────────────────────────────

  async checkHealth() {
    try {
      const res = await this.call('health', {}, { cache: true });
      this._alive = res.logs1_alive === true;
      return this._alive;
    } catch {
      this._alive = false;
      return false;
    }
  }

  async getEndpoints() {
    return this.call('endpoints');
  }

  // ─── PSM: Procurement & Supplier Management ────────────────────

  /**
   * Get purchase requisitions from PSM.
   * @param {Object} opts - { status, department }
   */
  async getPurchases(opts = {}) {
    return this.call('psm_purchases', {
      status: opts.status,
      department: opts.department,
    });
  }

  /** Get current procurement budget */
  async getBudget() {
    return this.call('psm_budget');
  }

  /** Get budget activity logs */
  async getBudgetLogs() {
    return this.call('psm_budget_logs');
  }

  /** Get department budget requests */
  async getBudgetRequests() {
    return this.call('psm_budget_requests');
  }

  /**
   * Get vendor directory.
   * @param {Object} opts - { search }
   */
  async getVendors(opts = {}) {
    return this.call('psm_vendors', { search: opts.search });
  }

  /** Get product catalog */
  async getProducts() {
    return this.call('psm_products');
  }

  // ─── SWS: Smart Warehousing System ─────────────────────────────

  /** Get inventory / room requests */
  async getInventory() {
    return this.call('sws_inventory');
  }

  /** Get warehouse list */
  async getWarehouses() {
    return this.call('sws_warehouses');
  }

  // ─── PLT: Project Logistics Tracker ────────────────────────────

  /**
   * Get project list.
   * @param {Object} opts - { status }
   */
  async getProjects(opts = {}) {
    return this.call('plt_projects', { status: opts.status });
  }

  // ─── DTLR: Document Tracking ───────────────────────────────────

  /**
   * Get tracked documents.
   * @param {Object} opts - { search }
   */
  async getDocuments(opts = {}) {
    return this.call('dtlr_documents', { search: opts.search });
  }

  // ─── ALMS: Asset Lifecycle & Maintenance ───────────────────────

  /** Get asset maintenance records */
  async getAssets() {
    return this.call('alms_assets');
  }

  // ─── Dashboard ──────────────────────────────────────────────────

  /** Get aggregate Logs1 overview stats */
  async getDashboard() {
    return this.call('dashboard');
  }

  // ─── Utility Methods ────────────────────────────────────────────

  formatPeso(amount) {
    if (amount === null || amount === undefined) return '₱0.00';
    return '₱' + Number(amount).toLocaleString('en-PH', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  statusBadge(status) {
    const s = (status || '').toLowerCase();
    const colors = {
      approved:      'bg-green-100 text-green-800',
      completed:     'bg-green-100 text-green-800',
      active:        'bg-blue-100 text-blue-800',
      in_progress:   'bg-blue-100 text-blue-800',
      pending:       'bg-yellow-100 text-yellow-800',
      draft:         'bg-gray-100 text-gray-600',
      rejected:      'bg-red-100 text-red-800',
      cancelled:     'bg-red-100 text-red-800',
      expired:       'bg-orange-100 text-orange-800',
      delivered:     'bg-emerald-100 text-emerald-800',
      in_transit:    'bg-indigo-100 text-indigo-800',
    };
    const cls = colors[s] || 'bg-gray-100 text-gray-600';
    return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${status || 'N/A'}</span>`;
  }
}

class Logs1Error extends Error {
  constructor(message, status, data) {
    super(message);
    this.name   = 'Logs1Error';
    this.status = status;
    this.data   = data;
  }
}

// ─── Auto-instantiate global instance ────────────────────────────
window.logs1 = new Logs1Integration();

// ─── Optional: Global connectivity indicator ─────────────────────
document.addEventListener('DOMContentLoaded', () => {
  window.logs1.checkHealth().then(alive => {
    document.body.classList.toggle('logs1-connected', alive);
    document.body.classList.toggle('logs1-disconnected', !alive);
    if (alive) console.log('%c✓ Logs1 API connected', 'color: #2563eb; font-weight: bold');
    else console.warn('✗ Logs1 API unreachable — Logistics data will be unavailable');
  });
});
