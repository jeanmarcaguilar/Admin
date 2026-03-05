/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * HR4 Integration — JavaScript Service Module
 * ════════════════════════════════════════════════════════════════════
 *
 * Frontend helper for calling the HR4 API bridge (/api/hr4.php).
 * Provides typed methods for every integration endpoint with
 * built-in error handling, caching, and loading state management.
 *
 * HR4 = Source of Truth for:
 *   - Employee Master Data (HCM)
 *   - Job Titles & Positions
 *   - Payroll & Payslips
 *   - Compensation (Allowances/Deductions)
 *   - HMO/Benefits Enrollment
 *   - Employee Contracts & Government IDs
 *   - Terminations & Employee Lifecycle
 *
 * Usage:
 *   <script src="hr4-integration.js"></script>
 *   <script>
 *     const employees = await hr4.getEmployees();
 *     const payslips  = await hr4.getPayslips({ employee_id: 'EMP-2026-001' });
 *     const contracts = await hr4.getContracts({ expiring_within_days: 30 });
 *   </script>
 * ════════════════════════════════════════════════════════════════════
 */

class HR4Integration {
  /**
   * @param {Object} opts
   * @param {string} opts.baseUrl  - Base URL for the HR4 bridge API (auto-detected)
   * @param {number} opts.cacheTTL - Cache time-to-live in ms (default: 2 min)
   */
  constructor(opts = {}) {
    // Auto-detect base URL from current page location
    const pathParts = window.location.pathname.split('/');
    const adminIdx = pathParts.findIndex(p => p.toLowerCase() === 'admin');
    const basePath = adminIdx >= 0
      ? pathParts.slice(0, adminIdx + 1).join('/')
      : '/Admin';

    this.baseUrl  = opts.baseUrl || `${window.location.origin}${basePath}/api/hr4.php`;
    this.cacheTTL = opts.cacheTTL ?? 120_000; // 2 minutes
    this._cache   = new Map();
    this._alive   = null; // null = unchecked, true/false after health check
  }

  // ─── Core HTTP Helper ─────────────────────────────────────────

  /**
   * Call the HR4 bridge API.
   * @param {string}  action    - Action name (e.g. 'employees')
   * @param {Object}  params    - Query params or POST body
   * @param {Object}  opts      - { method, cache, signal }
   * @returns {Promise<Object>} - Parsed JSON response
   */
  async call(action, params = {}, opts = {}) {
    const method = (opts.method || 'GET').toUpperCase();
    const useCache = opts.cache !== false && method === 'GET';
    const cacheKey = `${action}:${JSON.stringify(params)}`;

    // Check cache
    if (useCache && this._cache.has(cacheKey)) {
      const cached = this._cache.get(cacheKey);
      if (Date.now() - cached.ts < this.cacheTTL) return cached.data;
      this._cache.delete(cacheKey);
    }

    // Build URL
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
      credentials: 'same-origin', // send session cookie
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
        throw new HR4Error(json.error || `HTTP ${res.status}`, res.status, json);
      }

      // Store in cache
      if (useCache && json.success) {
        this._cache.set(cacheKey, { ts: Date.now(), data: json });
      }

      return json;
    } catch (err) {
      if (err instanceof HR4Error) throw err;
      throw new HR4Error(err.message || 'Network error', 0, null);
    }
  }

  /** Clear all cached responses */
  clearCache() { this._cache.clear(); }

  // ─── Health / Connectivity ─────────────────────────────────────

  /** Check if HR4 is reachable. Caches result for 60s. */
  async checkHealth() {
    try {
      const res = await this.call('health', {}, { cache: true });
      this._alive = res.hr4_alive === true;
      return this._alive;
    } catch {
      this._alive = false;
      return false;
    }
  }

  /** Get the list of available endpoints from the bridge */
  async getEndpoints() {
    return this.call('endpoints');
  }

  // ─── Employees ─────────────────────────────────────────────────

  /**
   * List all employees from HR4 (source of truth).
   * @param {Object} opts - { search, status, department }
   */
  async getEmployees(opts = {}) {
    return this.call('employees', {
      search: opts.search,
      status: opts.status,
      department: opts.department,
    });
  }

  /**
   * Get a single employee by HR4 employee ID.
   * @param {string} id - HR4 employee_id (e.g. EMP-2026-001)
   */
  async getEmployee(id) {
    return this.call('employee', { id });
  }

  /**
   * Get a single employee by email.
   * @param {string} email
   */
  async getEmployeeByEmail(email) {
    return this.call('employee_by_email', { email });
  }

  /**
   * Get a full employee profile with contract, salary, govt IDs, and emergency contacts.
   * @param {Object} opts - { id, email } — provide at least one
   */
  async getEmployeeProfile(opts = {}) {
    return this.call('employee_profile', {
      id: opts.id || opts.employee_id,
      email: opts.email,
    });
  }

  // ─── Positions & Job Titles ─────────────────────────────────────

  /**
   * Get all positions with their job titles.
   * @param {Object} opts - { department, status }
   */
  async getPositions(opts = {}) {
    return this.call('positions', {
      department: opts.department,
      status: opts.status,
    });
  }

  /** Get vacant positions only */
  async getVacantPositions() {
    return this.call('vacant_positions');
  }

  // ─── Contracts ──────────────────────────────────────────────────

  /**
   * Get employee contracts. Includes computed expiry information.
   * @param {Object} opts - { employee_id, expiring_within_days }
   */
  async getContracts(opts = {}) {
    return this.call('contracts', {
      employee_id: opts.employee_id,
      expiring_within_days: opts.expiring_within_days,
    });
  }

  // ─── Government IDs ─────────────────────────────────────────────

  /**
   * Get government identification records (SSS, TIN, PhilHealth, Pag-IBIG).
   * @param {Object} opts - { employee_id }
   */
  async getGovernmentIds(opts = {}) {
    return this.call('government_ids', {
      employee_id: opts.employee_id,
    });
  }

  // ─── Terminations / Lifecycle ───────────────────────────────────

  /** Get all terminated/separated employees */
  async getTerminations() {
    return this.call('terminations');
  }

  // ─── Payroll / Payslips ─────────────────────────────────────────

  /**
   * Get all paid payslips with deduction breakdowns.
   * @param {Object} opts - { employee_id, period }
   */
  async getPayslips(opts = {}) {
    return this.call('payslips', {
      employee_id: opts.employee_id,
      period: opts.period,
    });
  }

  /**
   * Get payslips for a single employee.
   * @param {string} employeeId - HR4 employee_id
   */
  async getPayslip(employeeId) {
    return this.call('payslip', { employee_id: employeeId });
  }

  /** Get payslip summary/statistics (aggregate totals) */
  async getPayslipSummary() {
    return this.call('payslip_summary');
  }

  // ─── Disbursement ───────────────────────────────────────────────

  /** Get the latest payroll disbursement batch */
  async getLatestDisbursement() {
    return this.call('disbursement_latest');
  }

  // ─── Compensation ───────────────────────────────────────────────

  /**
   * Get compensation data (allowances + deductions) for employees.
   * @param {Object} opts - { employee_id }
   */
  async getCompensation(opts = {}) {
    return this.call('compensation', {
      employee_id: opts.employee_id,
    });
  }

  // ─── Departments ────────────────────────────────────────────────

  /** Get department directory with employee counts and position lists */
  async getDepartments() {
    return this.call('departments');
  }

  // ─── Dashboard ──────────────────────────────────────────────────

  /** Get HR4 overview dashboard (employee stats, payroll summary, positions, disbursement) */
  async getDashboard() {
    return this.call('dashboard');
  }

  // ─── Utility Methods ────────────────────────────────────────────

  /**
   * Format currency values (Philippine Peso)
   * @param {number} amount
   * @returns {string} Formatted string e.g. "₱12,345.67"
   */
  formatPeso(amount) {
    if (amount === null || amount === undefined) return '₱0.00';
    return '₱' + Number(amount).toLocaleString('en-PH', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  }

  /**
   * Get status badge HTML for employment status
   * @param {string} status
   * @returns {string} HTML badge span
   */
  statusBadge(status) {
    const s = (status || '').toLowerCase();
    const colors = {
      active:       'bg-green-100 text-green-800',
      regular:      'bg-green-100 text-green-800',
      probationary: 'bg-yellow-100 text-yellow-800',
      terminated:   'bg-red-100 text-red-800',
      resigned:     'bg-red-100 text-red-800',
      separated:    'bg-red-100 text-red-800',
      inactive:     'bg-gray-100 text-gray-800',
      end_of_contract: 'bg-orange-100 text-orange-800',
    };
    const cls = colors[s] || 'bg-gray-100 text-gray-600';
    return `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ${cls}">${status || 'N/A'}</span>`;
  }

  /**
   * Get color indicator for contract expiry
   * @param {number|null} daysLeft
   * @returns {string} Tailwind color class
   */
  expiryColor(daysLeft) {
    if (daysLeft === null || daysLeft === undefined) return 'text-gray-400';
    if (daysLeft < 0) return 'text-red-600 font-bold';    // expired
    if (daysLeft <= 7) return 'text-red-500';              // critical
    if (daysLeft <= 30) return 'text-orange-500';          // warning
    if (daysLeft <= 90) return 'text-yellow-500';          // notice
    return 'text-green-500';                                // ok
  }
}

/**
 * Custom error class for HR4 API errors.
 */
class HR4Error extends Error {
  constructor(message, status, data) {
    super(message);
    this.name   = 'HR4Error';
    this.status = status;
    this.data   = data;
  }
}

// ─── Auto-instantiate global instance ────────────────────────────
// Available as window.hr4 on any Admin page that includes this script.
window.hr4 = new HR4Integration();

// ─── Optional: Global connectivity indicator ─────────────────────
// On page load, check if HR4 is reachable and add a status class to body.
document.addEventListener('DOMContentLoaded', () => {
  window.hr4.checkHealth().then(alive => {
    document.body.classList.toggle('hr4-connected', alive);
    document.body.classList.toggle('hr4-disconnected', !alive);
    if (alive) console.log('%c✓ HR4 API connected', 'color: #7c3aed; font-weight: bold');
    else console.warn('✗ HR4 API unreachable — HR4 data will be unavailable');
  });
});
