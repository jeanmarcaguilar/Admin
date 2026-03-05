/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * HR2 Integration — JavaScript Service Module
 * ════════════════════════════════════════════════════════════════════
 *
 * Frontend helper for calling the HR2 API bridge (/api/hr2.php).
 * Provides typed methods for every integration endpoint with
 * built-in error handling, caching, and loading state management.
 *
 * Usage:
 *   <script src="hr2-integration.js"></script>
 *   <script>
 *     const hr2 = new HR2Integration();
 *     const employees = await hr2.getEmployees();
 *     const profile   = await hr2.getEmployeeProfile({ email: 'ana@example.com' });
 *   </script>
 * ════════════════════════════════════════════════════════════════════
 */

class HR2Integration {
  /**
   * @param {Object} opts
   * @param {string} opts.baseUrl  - Base URL for the bridge API (auto-detected)
   * @param {number} opts.cacheTTL - Cache time-to-live in ms (default: 2 min)
   */
  constructor(opts = {}) {
    // Auto-detect base URL from current page location
    const pathParts = window.location.pathname.split('/');
    const adminIdx = pathParts.findIndex(p => p.toLowerCase() === 'admin');
    const basePath = adminIdx >= 0
      ? pathParts.slice(0, adminIdx + 1).join('/')
      : '/Admin';

    this.baseUrl  = opts.baseUrl || `${window.location.origin}${basePath}/api/hr2.php`;
    this.cacheTTL = opts.cacheTTL ?? 120_000; // 2 minutes
    this._cache   = new Map();
    this._alive   = null; // null = unchecked, true/false after health check
  }

  // ─── Core HTTP Helper ─────────────────────────────────────────

  /**
   * Call the HR2 bridge API.
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
        throw new HR2Error(json.error || `HTTP ${res.status}`, res.status, json);
      }

      // Store in cache
      if (useCache && json.success) {
        this._cache.set(cacheKey, { ts: Date.now(), data: json });
      }

      return json;
    } catch (err) {
      if (err instanceof HR2Error) throw err;
      throw new HR2Error(err.message || 'Network error', 0, null);
    }
  }

  /** Clear all cached responses */
  clearCache() { this._cache.clear(); }

  // ─── Health / Connectivity ─────────────────────────────────────

  /** Check if HR2 is reachable. Caches result for 60s. */
  async checkHealth() {
    try {
      const res = await this.call('health', {}, { cache: true });
      this._alive = res.hr2_alive === true;
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
   * List all employees (from HR4 via HR2).
   * @param {Object} opts - { search }
   */
  async getEmployees(opts = {}) {
    return this.call('employees', { search: opts.search });
  }

  /**
   * Get a single employee by HR4 employee ID.
   * @param {string} id - HR4 employee_id
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
   * Get a full aggregated employee profile (employee + leaves + attendance + competencies + succession).
   * @param {Object} opts - { email, employee_id } — provide at least one
   */
  async getEmployeeProfile(opts = {}) {
    return this.call('employee_profile', {
      email: opts.email,
      employee_id: opts.employee_id,
    });
  }

  // ─── Attendance ─────────────────────────────────────────────────

  /**
   * Get attendance/schedule for an employee.
   * @param {string} employeeId - HR4 employee ID
   */
  async getAttendance(employeeId) {
    return this.call('attendance', { employee_id: employeeId });
  }

  // ─── Leaves ─────────────────────────────────────────────────────

  /**
   * Get all leave requests (filterable).
   * @param {Object} opts - { status, employee_id, page }
   */
  async getLeaves(opts = {}) {
    return this.call('leaves', {
      status: opts.status,
      employee_id: opts.employee_id,
      page: opts.page,
    });
  }

  /** Get available leave types */
  async getLeaveTypes() {
    return this.call('leave_types');
  }

  /**
   * Get leave balances for an employee.
   * @param {string} email
   */
  async getLeaveBalances(email) {
    return this.call('leave_balances', { email });
  }

  /** Get leave statistics summary */
  async getLeaveStats() {
    return this.call('leave_stats');
  }

  // ─── Training Room Bookings ─────────────────────────────────────

  /**
   * List training room bookings.
   * @param {Object} opts - { status, page }
   */
  async getTrainingBookings(opts = {}) {
    return this.call('training_bookings', {
      status: opts.status,
      page: opts.page,
    });
  }

  /**
   * Get a single training room booking.
   * @param {number|string} id
   */
  async getTrainingBooking(id) {
    return this.call('training_booking', { id });
  }

  /** Get training booking statistics */
  async getTrainingStats() {
    return this.call('training_stats');
  }

  // ─── Succession / Promotions ────────────────────────────────────

  /**
   * List all successor/promotion records.
   * @param {Object} opts - { status, employee_id }
   */
  async getSuccessors(opts = {}) {
    return this.call('successors', {
      status: opts.status,
      employee_id: opts.employee_id,
    });
  }

  /**
   * Get a single successor record by employee ID.
   * @param {string} employeeId
   */
  async getSuccessor(employeeId) {
    return this.call('successor', { employee_id: employeeId });
  }

  // ─── Competencies ───────────────────────────────────────────────

  /**
   * Get assigned competencies.
   * @param {Object} opts - { employee_id }
   */
  async getCompetencies(opts = {}) {
    return this.call('competencies', { employee_id: opts.employee_id });
  }

  // ─── Jobs / Positions ───────────────────────────────────────────

  /** Get all job titles/positions from HR4 */
  async getJobs() {
    return this.call('jobs');
  }

  // ─── HR Dashboard ───────────────────────────────────────────────

  /** Get HR2 real-time dashboard data */
  async getDashboard() {
    return this.call('dashboard');
  }

  // ─── Admin Actions ──────────────────────────────────────────────

  /**
   * Trigger HR4 → HR2 employee data sync (admin only).
   * @returns {Promise<Object>} - Sync result with created/updated/skipped counts
   */
  async syncEmployees() {
    return this.call('sync_employees', {}, { method: 'POST', cache: false });
  }
}

/**
 * Custom error class for HR2 API errors.
 */
class HR2Error extends Error {
  constructor(message, status, data) {
    super(message);
    this.name   = 'HR2Error';
    this.status = status;
    this.data   = data;
  }
}

// ─── Auto-instantiate global instance ────────────────────────────
// Available as window.hr2 on any Admin page that includes this script.
window.hr2 = new HR2Integration();

// ─── Optional: Global connectivity indicator ─────────────────────
// On page load, check if HR2 is reachable and add a status class to body.
document.addEventListener('DOMContentLoaded', () => {
  window.hr2.checkHealth().then(alive => {
    document.body.classList.toggle('hr2-connected', alive);
    document.body.classList.toggle('hr2-disconnected', !alive);
    if (alive) console.log('%c✓ HR2 API connected', 'color: #059669; font-weight: bold');
    else console.warn('✗ HR2 API unreachable — HR data will be unavailable');
  });
});
