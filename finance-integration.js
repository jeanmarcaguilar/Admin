/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * Finance Integration — JavaScript Service Module
 * ════════════════════════════════════════════════════════════════════
 *
 * Frontend helper for calling the Finance API bridge (/api/finance.php).
 * Provides typed methods for every integration endpoint with
 * built-in error handling, caching, and loading state management.
 *
 * Finance = Source of Truth for:
 *   - Budget Proposals (per department, per fiscal year)
 *   - Disbursement Requests (payment tracking)
 *   - Admin-Received Proposals (cross-system approvals)
 *   - Financial User Directory
 *
 * Usage:
 *   <script src="finance-integration.js"></script>
 *   <script>
 *     const budgets       = await fin.getBudgets();
 *     const disbursements = await fin.getDisbursements({ status: 'pending' });
 *     const dashboard     = await fin.getDashboard();
 *   </script>
 * ════════════════════════════════════════════════════════════════════
 */

class FinanceIntegration {
  /**
   * @param {Object} opts
   * @param {string} opts.baseUrl  - Base URL for the Finance bridge API
   * @param {number} opts.cacheTTL - Cache TTL in ms (default: 2 min)
   */
  constructor(opts = {}) {
    const pathParts = window.location.pathname.split('/');
    const adminIdx = pathParts.findIndex(p => p.toLowerCase() === 'admin');
    const basePath = adminIdx >= 0
      ? pathParts.slice(0, adminIdx + 1).join('/')
      : '/Admin';

    this.baseUrl  = opts.baseUrl || `${window.location.origin}${basePath}/api/finance.php`;
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
        throw new FinanceError(json.error || `HTTP ${res.status}`, res.status, json);
      }

      if (useCache && json.success) {
        this._cache.set(cacheKey, { ts: Date.now(), data: json });
      }

      return json;
    } catch (err) {
      if (err instanceof FinanceError) throw err;
      throw new FinanceError(err.message || 'Network error', 0, null);
    }
  }

  clearCache() { this._cache.clear(); }

  // ─── Health / Connectivity ─────────────────────────────────────

  async checkHealth() {
    try {
      const res = await this.call('health', {}, { cache: true });
      this._alive = res.finance_alive === true;
      return this._alive;
    } catch {
      this._alive = false;
      return false;
    }
  }

  async getEndpoints() {
    return this.call('endpoints');
  }

  // ─── Budget Proposals ──────────────────────────────────────────

  /**
   * Get all budget proposals.
   * @param {Object} opts - { department, status, fiscal_year }
   */
  async getBudgets(opts = {}) {
    return this.call('budgets', {
      department: opts.department,
      status: opts.status,
      fiscal_year: opts.fiscal_year,
    });
  }

  /**
   * Get a single budget proposal by ID.
   * @param {number|string} id
   */
  async getBudget(id) {
    return this.call('budget', { id });
  }

  // ─── Disbursement Requests ──────────────────────────────────────

  /**
   * Get all disbursement requests.
   * @param {Object} opts - { status, department }
   */
  async getDisbursements(opts = {}) {
    return this.call('disbursements', {
      status: opts.status,
      department: opts.department,
    });
  }

  // ─── Proposals ──────────────────────────────────────────────────

  /** Get public proposals with approval flags */
  async getProposals() {
    return this.call('proposals');
  }

  /** Get admin-received proposals */
  async getAdminProposals() {
    return this.call('admin_proposals');
  }

  // ─── Users ──────────────────────────────────────────────────────

  /** Get finance user directory */
  async getUsers() {
    return this.call('users');
  }

  // ─── Dashboard ──────────────────────────────────────────────────

  /** Get aggregate finance overview stats */
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
      approved:   'bg-green-100 text-green-800',
      released:   'bg-green-100 text-green-800',
      pending:    'bg-yellow-100 text-yellow-800',
      submitted:  'bg-blue-100 text-blue-800',
      draft:      'bg-gray-100 text-gray-600',
      rejected:   'bg-red-100 text-red-800',
      cancelled:  'bg-red-100 text-red-800',
      review:     'bg-orange-100 text-orange-800',
    };
    const cls = colors[s] || 'bg-gray-100 text-gray-600';
    return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${status || 'N/A'}</span>`;
  }

  /**
   * Get color class for budget health indicator.
   * @param {number} remaining - Remaining amount
   * @param {number} total - Total budgeted
   */
  budgetHealthColor(remaining, total) {
    if (!total || total === 0) return 'text-gray-400';
    const pct = (remaining / total) * 100;
    if (pct <= 10) return 'text-red-600 font-bold';
    if (pct <= 25) return 'text-orange-500';
    if (pct <= 50) return 'text-yellow-500';
    return 'text-green-500';
  }
}

class FinanceError extends Error {
  constructor(message, status, data) {
    super(message);
    this.name   = 'FinanceError';
    this.status = status;
    this.data   = data;
  }
}

// ─── Auto-instantiate global instance ────────────────────────────
window.fin = new FinanceIntegration();

// ─── Optional: Global connectivity indicator ─────────────────────
document.addEventListener('DOMContentLoaded', () => {
  window.fin.checkHealth().then(alive => {
    document.body.classList.toggle('finance-connected', alive);
    document.body.classList.toggle('finance-disconnected', !alive);
    if (alive) console.log('%c✓ Finance API connected', 'color: #d97706; font-weight: bold');
    else console.warn('✗ Finance API unreachable — Financial data will be unavailable');
  });
});
