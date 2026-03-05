<?php
/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * API Bridge: HR4 Integration
 * ════════════════════════════════════════════════════════════════════
 *
 * This file acts as a proxy/bridge between the Admin frontend and
 * the HR4 Laravel API. All calls go through here so that:
 *   1. Auth is checked (Admin session required)
 *   2. HR4 URL resolution is centralized (domain ↔ localhost)
 *   3. API key is managed server-side (never exposed to browser)
 *   4. Response format is normalized for Admin's JS
 *
 * Usage:  /api/hr4.php?action=<action>&...params
 *
 * ── Endpoint Map ──────────────────────────────────────────────────
 *  action                  | HR4 Route                              | Description
 * ─────────────────────────|────────────────────────────────────────|────────────────────────────
 *  health                  | GET /vacant-positions                  | Connectivity check (no auth)
 *  employees               | GET /allemployees?api_key=...          | All employees with full relations
 *  employee                | GET /allemployees?api_key=...          | Single employee (filtered by ID)
 *  employee_by_email       | GET /allemployees?api_key=...          | Single employee (filtered by email)
 *  employee_profile        | GET /allemployees?api_key=...          | Aggregate: employee + contract + govt IDs + salary
 *  positions               | GET /api/employees/job                 | All positions with job info
 *  vacant_positions        | GET /vacant-positions                  | Vacant positions only
 *  contracts               | GET /allemployees?api_key=...          | Employee contracts (extracted from relations)
 *  government_ids          | GET /allemployees?api_key=...          | Government IDs (SSS, TIN, PhilHealth, PagIBIG)
 *  terminations            | GET /allemployees?api_key=...          | Terminated employees (filtered)
 *  payslips                | GET /GetAllPayslip?api_key=...         | All paid payslips
 *  payslip                 | GET /GetAllPayslip?api_key=...         | Single employee payslips (filtered)
 *  payslip_summary         | GET /GetAllPayslip?api_key=...         | Payroll statistics summary
 *  disbursement_latest     | GET /api/payroll/disbursement/latest   | Latest disbursement batch
 *  compensation            | GET /allemployees?api_key=...          | Allowances + deductions per employee
 *  departments             | GET /allemployees?api_key=...          | Department directory (aggregated)
 *  dashboard               | Aggregate: employees + payslips + positions | HR4 overview stats
 *  endpoints               | —                                      | Self-documenting endpoint list
 * ════════════════════════════════════════════════════════════════════
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../config/hr4.php';

// ── Auth helper ──
function requireAuth(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['authenticated'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized — Admin login required']);
        exit;
    }
}

// ── JSON output helper ──
function out(array $data, int $status = 200): void {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

// ── Cache: store /allemployees in memory within same request ──
// HR4's /allemployees is a single endpoint that returns everything;
// multiple actions (employee, contracts, govt_ids, etc.) extract slices from it.
$_HR4_EMPLOYEES_CACHE = null;

/**
 * Fetch the full /allemployees response once, cache for reuse within the same request.
 * Many actions extract different slices from this single API call.
 */
function hr4_get_all_employees(): array {
    global $_HR4_EMPLOYEES_CACHE;
    if ($_HR4_EMPLOYEES_CACHE !== null) return $_HR4_EMPLOYEES_CACHE;

    $result = hr4_api('/allemployees');
    if (!$result['success']) {
        return ['success' => false, 'error' => $result['error'] ?? 'Failed to fetch employees from HR4', 'data' => []];
    }

    // HR4 returns employees in various possible structures
    $employees = $result['data']['data'] ?? $result['data']['employees'] ?? $result['data'] ?? [];
    if (!is_array($employees)) $employees = [];

    $_HR4_EMPLOYEES_CACHE = ['success' => true, 'data' => $employees];
    return $_HR4_EMPLOYEES_CACHE;
}

// ── Grab action ──
$action = $_GET['action'] ?? '';

// ═══════════════════════════════════════════════════════════════
// HEALTH CHECK (no auth required)
// ═══════════════════════════════════════════════════════════════
if ($action === 'health') {
    // Use vacant-positions as a lightweight no-auth endpoint
    $result = hr4_api('/vacant-positions', 'GET', [], [], false);
    out([
        'success'   => $result['success'],
        'hr4_alive' => $result['success'],
        'latency'   => 'ok',
        'domain'    => HR4_API_DOMAIN,
        'local'     => HR4_API_LOCAL,
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
}

// All other actions require login
requireAuth();

switch ($action) {

    // ─────────────────────────────────────────────────────────────
    // EMPLOYEES — Full Directory
    // ─────────────────────────────────────────────────────────────

    /**
     * List all employees with their full relations.
     * GET ?action=employees&search=<name_or_email>&status=<active|inactive>&department=<dept>
     *
     * Each employee includes: contract, salary_detail, government_ids,
     * emergency_contacts, position (with job_title)
     */
    case 'employees':
        if (!HR4_ENABLE_EMPLOYEES) out(['success' => false, 'error' => 'Employee integration disabled'], 403);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $employees = $allResult['data'];

        // Filter: search
        $search = strtolower(trim($_GET['search'] ?? ''));
        if ($search !== '') {
            $employees = array_values(array_filter($employees, function($emp) use ($search) {
                $name  = strtolower($emp['full_name'] ?? '');
                $email = strtolower($emp['email'] ?? '');
                $id    = strtolower($emp['employee_id'] ?? '');
                $dept  = strtolower($emp['position']['department'] ?? '');
                return str_contains($name, $search) || str_contains($email, $search)
                    || str_contains($id, $search)   || str_contains($dept, $search);
            }));
        }

        // Filter: employment status
        $statusFilter = strtolower(trim($_GET['status'] ?? ''));
        if ($statusFilter !== '') {
            $employees = array_values(array_filter($employees, function($emp) use ($statusFilter) {
                return strtolower($emp['employment_status'] ?? '') === $statusFilter
                    || strtolower($emp['status'] ?? '') === $statusFilter;
            }));
        }

        // Filter: department
        $deptFilter = strtolower(trim($_GET['department'] ?? ''));
        if ($deptFilter !== '') {
            $employees = array_values(array_filter($employees, function($emp) use ($deptFilter) {
                return strtolower($emp['position']['department'] ?? '') === $deptFilter;
            }));
        }

        out([
            'success' => true,
            'count'   => count($employees),
            'data'    => $employees,
            'source'  => 'hr4 (direct)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // SINGLE EMPLOYEE — By ID
    // ─────────────────────────────────────────────────────────────

    /**
     * Get a single employee by employee_id (e.g. EMP-2026-001)
     * GET ?action=employee&id=<employee_id>
     */
    case 'employee':
        if (!HR4_ENABLE_EMPLOYEES) out(['success' => false, 'error' => 'Employee integration disabled'], 403);

        $id = strtoupper(trim($_GET['id'] ?? ''));
        if (!$id) out(['success' => false, 'error' => 'Missing required parameter: id'], 400);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $found = null;
        foreach ($allResult['data'] as $emp) {
            if (strtoupper($emp['employee_id'] ?? '') === $id || (string)($emp['id'] ?? '') === $id) {
                $found = $emp;
                break;
            }
        }

        if (!$found) {
            out(['success' => false, 'error' => "Employee '{$id}' not found"], 404);
        }

        out([
            'success'  => true,
            'employee' => $found,
            'source'   => 'hr4 (direct)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // SINGLE EMPLOYEE — By Email
    // ─────────────────────────────────────────────────────────────

    /**
     * Get a single employee by email address
     * GET ?action=employee_by_email&email=<email>
     */
    case 'employee_by_email':
        if (!HR4_ENABLE_EMPLOYEES) out(['success' => false, 'error' => 'Employee integration disabled'], 403);

        $email = strtolower(trim($_GET['email'] ?? ''));
        if (!$email) out(['success' => false, 'error' => 'Missing required parameter: email'], 400);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $found = null;
        foreach ($allResult['data'] as $emp) {
            if (strtolower($emp['email'] ?? '') === $email) {
                $found = $emp;
                break;
            }
        }

        if (!$found) {
            out(['success' => false, 'error' => "Employee with email '{$email}' not found"], 404);
        }

        out([
            'success'  => true,
            'employee' => $found,
            'source'   => 'hr4 (direct)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // AGGREGATE EMPLOYEE PROFILE
    // ─────────────────────────────────────────────────────────────

    /**
     * Get a full employee profile (all relations included):
     * - Basic info, contract, salary details, government IDs
     * - Emergency contacts, position, job title
     *
     * GET ?action=employee_profile&id=<employee_id>
     * GET ?action=employee_profile&email=<email>
     */
    case 'employee_profile':
        $id    = strtoupper(trim($_GET['id'] ?? $_GET['employee_id'] ?? ''));
        $email = strtolower(trim($_GET['email'] ?? ''));

        if (!$id && !$email) {
            out(['success' => false, 'error' => 'Provide id or email'], 400);
        }

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $found = null;
        foreach ($allResult['data'] as $emp) {
            if ($id && (strtoupper($emp['employee_id'] ?? '') === $id || (string)($emp['id'] ?? '') === $id)) {
                $found = $emp;
                break;
            }
            if ($email && strtolower($emp['email'] ?? '') === $email) {
                $found = $emp;
                break;
            }
        }

        if (!$found) {
            out(['success' => false, 'error' => 'Employee not found'], 404);
        }

        // Structure the profile response
        out([
            'success' => true,
            'source'  => 'hr4 (aggregate)',
            'employee' => [
                'employee_id'       => $found['employee_id'] ?? null,
                'full_name'         => $found['full_name'] ?? null,
                'email'             => $found['email'] ?? null,
                'phone'             => $found['phone'] ?? null,
                'address'           => $found['address'] ?? null,
                'date_of_birth'     => $found['date_of_birth'] ?? null,
                'gender'            => $found['gender'] ?? null,
                'marital_status'    => $found['marital_status'] ?? null,
                'nationality'       => $found['nationality'] ?? null,
                'employment_status' => $found['employment_status'] ?? null,
                'employment_type'   => $found['employment_type'] ?? null,
                'hired_date'        => $found['hired_date'] ?? null,
                'end_date'          => $found['end_date'] ?? null,
                'work_location'     => $found['work_location'] ?? null,
                'status'            => $found['status'] ?? null,
            ],
            'position' => $found['position'] ?? null,
            'job_title' => $found['job_title'] ?? $found['position']['job'] ?? null,
            'contract' => $found['contract'] ?? null,
            'salary_detail' => $found['salary_detail'] ?? null,
            'government_ids' => $found['government_id'] ?? $found['government_ids'] ?? null,
            'emergency_contacts' => $found['emergency_contacts'] ?? [],
        ]);

    // ─────────────────────────────────────────────────────────────
    // POSITIONS & JOB TITLES
    // ─────────────────────────────────────────────────────────────

    /**
     * Get all positions with their job titles
     * GET ?action=positions&department=<dept>&status=<open|filled>
     */
    case 'positions':
        if (!HR4_ENABLE_POSITIONS) out(['success' => false, 'error' => 'Positions integration disabled'], 403);

        $result = hr4_api('/api/employees/job', 'GET', [], [], false);
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch positions'], $result['status'] ?: 502);
        }

        $positions = $result['data']['data'] ?? $result['data'] ?? [];

        // Filter: department
        $deptFilter = strtolower(trim($_GET['department'] ?? ''));
        if ($deptFilter !== '') {
            $positions = array_values(array_filter($positions, function($pos) use ($deptFilter) {
                return strtolower($pos['department'] ?? '') === $deptFilter;
            }));
        }

        // Filter: status (open/filled)
        $statusFilter = strtolower(trim($_GET['status'] ?? ''));
        if ($statusFilter !== '') {
            $positions = array_values(array_filter($positions, function($pos) use ($statusFilter) {
                return strtolower($pos['status'] ?? '') === $statusFilter;
            }));
        }

        out([
            'success' => true,
            'count'   => count($positions),
            'data'    => $positions,
            'source'  => 'hr4 (direct)'
        ]);

    /**
     * Get vacant positions only
     * GET ?action=vacant_positions
     */
    case 'vacant_positions':
        if (!HR4_ENABLE_POSITIONS) out(['success' => false, 'error' => 'Positions integration disabled'], 403);

        $result = hr4_api('/vacant-positions', 'GET', [], [], false);
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch vacant positions'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'data'    => $result['data']['data'] ?? $result['data'] ?? [],
            'source'  => 'hr4 (direct)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // EMPLOYEE CONTRACTS
    // ─────────────────────────────────────────────────────────────

    /**
     * Get all employee contracts (extracted from /allemployees relations)
     * GET ?action=contracts&employee_id=<id>&expiring_within_days=<30>
     */
    case 'contracts':
        if (!HR4_ENABLE_CONTRACTS) out(['success' => false, 'error' => 'Contracts integration disabled'], 403);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $contracts = [];
        $empIdFilter = strtoupper(trim($_GET['employee_id'] ?? ''));
        $expiringDays = intval($_GET['expiring_within_days'] ?? 0);
        $now = time();

        foreach ($allResult['data'] as $emp) {
            $contract = $emp['contract'] ?? null;
            if (!$contract) continue;

            // Filter by employee_id
            if ($empIdFilter && strtoupper($emp['employee_id'] ?? '') !== $empIdFilter) continue;

            $record = [
                'employee_id'             => $emp['employee_id'] ?? null,
                'full_name'               => $emp['full_name'] ?? null,
                'email'                   => $emp['email'] ?? null,
                'department'              => $emp['position']['department'] ?? null,
                'job_title'               => $emp['position']['job']['job_title'] ?? $emp['job_title']['job_title'] ?? null,
                'contract_no'             => $contract['contract_no'] ?? null,
                'contract_duration_months' => $contract['contract_duration_months'] ?? null,
                'start_date'              => $contract['start_date'] ?? null,
                'end_date'                => $contract['end_date'] ?? null,
                'is_expiring_soon'        => false,
                'days_until_expiry'       => null,
            ];

            // Calculate expiry status
            if (!empty($contract['end_date'])) {
                $endTs = strtotime($contract['end_date']);
                if ($endTs) {
                    $daysLeft = intval(($endTs - $now) / 86400);
                    $record['days_until_expiry'] = $daysLeft;
                    $record['is_expiring_soon'] = $daysLeft <= 30 && $daysLeft >= 0;
                }
            }

            // Filter: expiring within N days
            if ($expiringDays > 0 && ($record['days_until_expiry'] === null || $record['days_until_expiry'] > $expiringDays || $record['days_until_expiry'] < 0)) {
                continue;
            }

            $contracts[] = $record;
        }

        // Sort by end_date ascending (soonest expiry first)
        usort($contracts, function($a, $b) {
            return ($a['days_until_expiry'] ?? PHP_INT_MAX) - ($b['days_until_expiry'] ?? PHP_INT_MAX);
        });

        out([
            'success' => true,
            'count'   => count($contracts),
            'data'    => $contracts,
            'source'  => 'hr4 (derived)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // GOVERNMENT IDs
    // ─────────────────────────────────────────────────────────────

    /**
     * Get government ID records (SSS, TIN, PhilHealth, Pag-IBIG) for employees
     * GET ?action=government_ids&employee_id=<id>
     */
    case 'government_ids':
        if (!HR4_ENABLE_GOVT_IDS) out(['success' => false, 'error' => 'Government IDs integration disabled'], 403);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $records = [];
        $empIdFilter = strtoupper(trim($_GET['employee_id'] ?? ''));

        foreach ($allResult['data'] as $emp) {
            $govIds = $emp['government_id'] ?? $emp['government_ids'] ?? null;
            if (!$govIds) continue;

            if ($empIdFilter && strtoupper($emp['employee_id'] ?? '') !== $empIdFilter) continue;

            $records[] = [
                'employee_id'       => $emp['employee_id'] ?? null,
                'full_name'         => $emp['full_name'] ?? null,
                'department'        => $emp['position']['department'] ?? null,
                'sss_number'        => $govIds['sss_number'] ?? null,
                'tin_number'        => $govIds['tin_number'] ?? null,
                'philhealth_number' => $govIds['philhealth_number'] ?? null,
                'pagibig_number'    => $govIds['pagibig_number'] ?? null,
            ];
        }

        out([
            'success' => true,
            'count'   => count($records),
            'data'    => $records,
            'source'  => 'hr4 (derived)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // TERMINATIONS — Employees who have been separated
    // ─────────────────────────────────────────────────────────────

    /**
     * Get terminated/separated employees
     * GET ?action=terminations
     */
    case 'terminations':
        if (!HR4_ENABLE_LIFECYCLE) out(['success' => false, 'error' => 'Lifecycle integration disabled'], 403);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $terminated = [];
        foreach ($allResult['data'] as $emp) {
            $status = strtolower($emp['employment_status'] ?? $emp['status'] ?? '');
            if (in_array($status, ['terminated', 'resigned', 'separated', 'inactive', 'end_of_contract'])) {
                $terminated[] = [
                    'employee_id'       => $emp['employee_id'] ?? null,
                    'full_name'         => $emp['full_name'] ?? null,
                    'email'             => $emp['email'] ?? null,
                    'department'        => $emp['position']['department'] ?? null,
                    'job_title'         => $emp['position']['job']['job_title'] ?? null,
                    'employment_status' => $emp['employment_status'] ?? null,
                    'hired_date'        => $emp['hired_date'] ?? null,
                    'end_date'          => $emp['end_date'] ?? null,
                    'termination'       => $emp['termination'] ?? $emp['employee_terminate'] ?? null,
                ];
            }
        }

        out([
            'success' => true,
            'count'   => count($terminated),
            'data'    => $terminated,
            'source'  => 'hr4 (derived)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // PAYSLIPS — All paid payslips
    // ─────────────────────────────────────────────────────────────

    /**
     * Get all paid payslips with deduction breakdowns
     * GET ?action=payslips&employee_id=<id>&period=<month_year>
     */
    case 'payslips':
        if (!HR4_ENABLE_PAYROLL) out(['success' => false, 'error' => 'Payroll integration disabled'], 403);

        $result = hr4_api('/GetAllPayslip');
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch payslips'], $result['status'] ?: 502);
        }

        $payslips = $result['data']['data'] ?? $result['data'] ?? [];

        // Filter: employee_id
        $empIdFilter = strtoupper(trim($_GET['employee_id'] ?? ''));
        if ($empIdFilter !== '') {
            $payslips = array_values(array_filter($payslips, function($p) use ($empIdFilter) {
                return strtoupper($p['employee_id'] ?? $p['payroll_data_input']['employee_id'] ?? '') === $empIdFilter;
            }));
        }

        // Filter: period (month_year)
        $periodFilter = trim($_GET['period'] ?? '');
        if ($periodFilter !== '') {
            $payslips = array_values(array_filter($payslips, function($p) use ($periodFilter) {
                $period = $p['period']['month_year'] ?? $p['pay_period']['month_year'] ?? '';
                return stripos($period, $periodFilter) !== false;
            }));
        }

        out([
            'success' => true,
            'count'   => count($payslips),
            'data'    => $payslips,
            'source'  => 'hr4 (direct)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // SINGLE EMPLOYEE PAYSLIPS
    // ─────────────────────────────────────────────────────────────

    /**
     * Get payslips for a single employee
     * GET ?action=payslip&employee_id=<id>
     */
    case 'payslip':
        if (!HR4_ENABLE_PAYROLL) out(['success' => false, 'error' => 'Payroll integration disabled'], 403);

        $empId = strtoupper(trim($_GET['employee_id'] ?? ''));
        if (!$empId) out(['success' => false, 'error' => 'Missing required parameter: employee_id'], 400);

        $result = hr4_api('/GetAllPayslip');
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch payslips'], $result['status'] ?: 502);
        }

        $payslips = $result['data']['data'] ?? $result['data'] ?? [];
        $empPayslips = array_values(array_filter($payslips, function($p) use ($empId) {
            return strtoupper($p['employee_id'] ?? $p['payroll_data_input']['employee_id'] ?? '') === $empId;
        }));

        out([
            'success' => true,
            'employee_id' => $empId,
            'count'   => count($empPayslips),
            'data'    => $empPayslips,
            'source'  => 'hr4 (direct)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // PAYROLL SUMMARY — Aggregate statistics
    // ─────────────────────────────────────────────────────────────

    /**
     * Get payroll summary statistics
     * GET ?action=payslip_summary
     */
    case 'payslip_summary':
        if (!HR4_ENABLE_PAYROLL) out(['success' => false, 'error' => 'Payroll integration disabled'], 403);

        $result = hr4_api('/GetAllPayslip');
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch payslips'], $result['status'] ?: 502);
        }

        $payslips = $result['data']['data'] ?? $result['data'] ?? [];

        $totalGross = 0;
        $totalNet = 0;
        $totalDeductions = 0;
        $employeeCount = [];
        $periods = [];

        foreach ($payslips as $p) {
            $input  = $p['payroll_data_input'] ?? $p;
            $result_data = $p['payroll_result'] ?? $p['result'] ?? $p;

            $gross = floatval($result_data['gross_pay'] ?? $input['base_salary'] ?? 0);
            $net   = floatval($result_data['net_pay'] ?? 0);
            $ded   = floatval($result_data['total_deductions'] ?? 0);

            $totalGross += $gross;
            $totalNet += $net;
            $totalDeductions += $ded;

            $empId = $input['employee_id'] ?? '';
            if ($empId) $employeeCount[$empId] = true;

            $period = $p['period']['month_year'] ?? $p['pay_period']['month_year'] ?? '';
            if ($period) $periods[$period] = true;
        }

        out([
            'success' => true,
            'summary' => [
                'total_payslips'    => count($payslips),
                'total_employees'   => count($employeeCount),
                'total_gross_pay'   => round($totalGross, 2),
                'total_net_pay'     => round($totalNet, 2),
                'total_deductions'  => round($totalDeductions, 2),
                'pay_periods'       => array_keys($periods),
                'period_count'      => count($periods),
            ],
            'source' => 'hr4 (computed)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // DISBURSEMENT — Latest batch
    // ─────────────────────────────────────────────────────────────

    /**
     * Get the latest payroll disbursement batch
     * GET ?action=disbursement_latest
     */
    case 'disbursement_latest':
        if (!HR4_ENABLE_DISBURSEMENT) out(['success' => false, 'error' => 'Disbursement integration disabled'], 403);

        $result = hr4_api('/api/payroll/disbursement/latest', 'GET', [], [], false);
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch disbursement'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'data'    => $result['data']['data'] ?? $result['data'],
            'source'  => 'hr4 (direct)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // COMPENSATION — Allowances & Deductions per employee
    // ─────────────────────────────────────────────────────────────

    /**
     * Get compensation data (allowances + deductions) extracted from employee relations
     * GET ?action=compensation&employee_id=<id>
     */
    case 'compensation':
        if (!HR4_ENABLE_COMPENSATION) out(['success' => false, 'error' => 'Compensation integration disabled'], 403);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $records = [];
        $empIdFilter = strtoupper(trim($_GET['employee_id'] ?? ''));

        foreach ($allResult['data'] as $emp) {
            if ($empIdFilter && strtoupper($emp['employee_id'] ?? '') !== $empIdFilter) continue;

            $salary = $emp['salary_detail'] ?? null;
            $allowances = $emp['employee_allowances'] ?? $emp['allowances'] ?? [];
            $deductions = $emp['employee_deductions'] ?? $emp['deductions'] ?? [];

            $totalAllowances = 0;
            $totalDeductions = 0;
            foreach ($allowances as $a) { $totalAllowances += floatval($a['amount'] ?? 0); }
            foreach ($deductions as $d) { $totalDeductions += floatval($d['amount'] ?? 0); }

            $baseSalary = floatval($salary['base_salary'] ?? $emp['base_salary'] ?? 0);

            $records[] = [
                'employee_id'       => $emp['employee_id'] ?? null,
                'full_name'         => $emp['full_name'] ?? null,
                'department'        => $emp['position']['department'] ?? null,
                'base_salary'       => $baseSalary,
                'salary_grade'      => $salary['salary_grade'] ?? null,
                'pay_type'          => $salary['pay_type'] ?? $emp['pay_type'] ?? null,
                'payroll_cycle'     => $salary['payroll_cycle'] ?? null,
                'tax_status'        => $salary['tax_status'] ?? null,
                'allowances'        => $allowances,
                'deductions'        => $deductions,
                'total_allowances'  => round($totalAllowances, 2),
                'total_deductions'  => round($totalDeductions, 2),
                'net_salary'        => round($baseSalary + $totalAllowances - $totalDeductions, 2),
            ];
        }

        out([
            'success' => true,
            'count'   => count($records),
            'data'    => $records,
            'source'  => 'hr4 (derived)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // DEPARTMENTS — Aggregated from employee data
    // ─────────────────────────────────────────────────────────────

    /**
     * Get department directory with employee counts
     * GET ?action=departments
     */
    case 'departments':
        if (!HR4_ENABLE_EMPLOYEES) out(['success' => false, 'error' => 'Employee integration disabled'], 403);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $depts = [];
        foreach ($allResult['data'] as $emp) {
            $dept = $emp['position']['department'] ?? 'Unassigned';
            $loc  = $emp['position']['location'] ?? $emp['work_location'] ?? null;
            $status = strtolower($emp['employment_status'] ?? $emp['status'] ?? 'active');

            if (!isset($depts[$dept])) {
                $depts[$dept] = [
                    'department'    => $dept,
                    'location'      => $loc,
                    'total'         => 0,
                    'active'        => 0,
                    'inactive'      => 0,
                    'positions'     => [],
                ];
            }

            $depts[$dept]['total']++;
            if (in_array($status, ['active', 'regular', 'probationary'])) {
                $depts[$dept]['active']++;
            } else {
                $depts[$dept]['inactive']++;
            }

            // Track unique positions
            $jobTitle = $emp['position']['job']['job_title'] ?? $emp['job_title']['job_title'] ?? null;
            if ($jobTitle && !in_array($jobTitle, $depts[$dept]['positions'])) {
                $depts[$dept]['positions'][] = $jobTitle;
            }
        }

        out([
            'success' => true,
            'count'   => count($depts),
            'data'    => array_values($depts),
            'source'  => 'hr4 (aggregated)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // DEPARTMENT DETAIL — Employees + Contracts for a single department
    // ─────────────────────────────────────────────────────────────

    /**
     * Get detailed info for a specific department: all employees with contracts
     * GET ?action=department_detail&department=<name>
     */
    case 'department_detail':
        if (!HR4_ENABLE_EMPLOYEES) out(['success' => false, 'error' => 'Employee integration disabled'], 403);

        $deptFilter = trim($_GET['department'] ?? '');
        if (!$deptFilter) out(['success' => false, 'error' => 'Missing required parameter: department'], 400);

        $allResult = hr4_get_all_employees();
        if (!$allResult['success']) {
            out(['success' => false, 'error' => $allResult['error']], 502);
        }

        $employees = [];
        $contracts = [];
        $activeCount = 0;
        $inactiveCount = 0;
        $positions = [];
        $now = time();

        foreach ($allResult['data'] as $emp) {
            $dept = $emp['position']['department'] ?? 'Unassigned';
            if (strcasecmp($dept, $deptFilter) !== 0) continue;

            $status = strtolower($emp['employment_status'] ?? $emp['status'] ?? 'active');
            if (in_array($status, ['active', 'regular', 'probationary'])) $activeCount++;
            else $inactiveCount++;

            $jobTitle = $emp['position']['job']['job_title'] ?? $emp['job_title']['job_title'] ?? null;
            if ($jobTitle && !in_array($jobTitle, $positions)) $positions[] = $jobTitle;

            $employees[] = [
                'employee_id'       => $emp['employee_id'] ?? null,
                'full_name'         => $emp['full_name'] ?? null,
                'email'             => $emp['email'] ?? null,
                'phone'             => $emp['phone'] ?? null,
                'job_title'         => $jobTitle,
                'employment_status' => $emp['employment_status'] ?? $emp['status'] ?? null,
                'employment_type'   => $emp['position']['employment_type'] ?? $emp['employment_type'] ?? null,
                'hired_date'        => $emp['hired_date'] ?? null,
                'base_salary'       => $emp['base_salary'] ?? $emp['position']['base_salary'] ?? null,
                'location'          => $emp['position']['location'] ?? $emp['work_location'] ?? null,
            ];

            // Extract contract info
            $contract = $emp['contract'] ?? null;
            if ($contract) {
                $record = [
                    'employee_id'              => $emp['employee_id'] ?? null,
                    'full_name'                => $emp['full_name'] ?? null,
                    'contract_no'              => $contract['contract_no'] ?? null,
                    'contract_duration_months'  => $contract['contract_duration_months'] ?? null,
                    'start_date'               => $contract['start_date'] ?? null,
                    'end_date'                 => $contract['end_date'] ?? null,
                    'days_until_expiry'        => null,
                ];
                if (!empty($contract['end_date'])) {
                    $endTs = strtotime($contract['end_date']);
                    if ($endTs) $record['days_until_expiry'] = intval(($endTs - $now) / 86400);
                }
                $contracts[] = $record;
            }
        }

        // Sort contracts by expiry
        usort($contracts, function($a, $b) {
            return ($a['days_until_expiry'] ?? PHP_INT_MAX) - ($b['days_until_expiry'] ?? PHP_INT_MAX);
        });

        out([
            'success'    => true,
            'department' => $deptFilter,
            'summary'    => [
                'total'     => count($employees),
                'active'    => $activeCount,
                'inactive'  => $inactiveCount,
                'positions' => $positions,
                'contracts' => count($contracts),
            ],
            'employees'  => $employees,
            'contracts'  => $contracts,
            'source'     => 'hr4 (aggregated)'
        ]);

    // ─────────────────────────────────────────────────────────────
    // DASHBOARD — HR4 overview (aggregate)
    // ─────────────────────────────────────────────────────────────

    /**
     * Get HR4 dashboard overview with key metrics
     * GET ?action=dashboard
     */
    case 'dashboard':
        $stats = [
            'success' => true,
            'source'  => 'hr4 (aggregate)',
            'employees' => null,
            'payroll'   => null,
            'positions' => null,
        ];

        // 1. Employee stats
        if (HR4_ENABLE_EMPLOYEES) {
            $allResult = hr4_get_all_employees();
            if ($allResult['success']) {
                $emps = $allResult['data'];
                $active = 0; $inactive = 0; $departments = [];
                foreach ($emps as $e) {
                    $s = strtolower($e['employment_status'] ?? $e['status'] ?? '');
                    if (in_array($s, ['active', 'regular', 'probationary'])) $active++; else $inactive++;
                    $d = $e['position']['department'] ?? null;
                    if ($d) $departments[$d] = ($departments[$d] ?? 0) + 1;
                }
                $stats['employees'] = [
                    'total'             => count($emps),
                    'active'            => $active,
                    'inactive'          => $inactive,
                    'department_count'  => count($departments),
                    'by_department'     => $departments,
                ];
            }
        }

        // 2. Payslip stats
        if (HR4_ENABLE_PAYROLL) {
            $payResult = hr4_api('/GetAllPayslip');
            if ($payResult['success']) {
                $payslips = $payResult['data']['data'] ?? $payResult['data'] ?? [];
                $totalNet = 0;
                $periods = [];
                foreach ($payslips as $p) {
                    $totalNet += floatval($p['payroll_result']['net_pay'] ?? $p['net_pay'] ?? 0);
                    $period = $p['period']['month_year'] ?? $p['pay_period']['month_year'] ?? '';
                    if ($period) $periods[$period] = true;
                }
                $stats['payroll'] = [
                    'total_payslips' => count($payslips),
                    'total_net_pay'  => round($totalNet, 2),
                    'pay_periods'    => count($periods),
                ];
            }
        }

        // 3. Position stats
        if (HR4_ENABLE_POSITIONS) {
            $posResult = hr4_api('/vacant-positions', 'GET', [], [], false);
            if ($posResult['success']) {
                $positions = $posResult['data']['data'] ?? $posResult['data'] ?? [];
                $stats['positions'] = [
                    'vacant_count' => count($positions),
                ];
            }
        }

        // 4. Disbursement
        if (HR4_ENABLE_DISBURSEMENT) {
            $disbResult = hr4_api('/api/payroll/disbursement/latest', 'GET', [], [], false);
            if ($disbResult['success']) {
                $stats['disbursement'] = $disbResult['data']['data'] ?? $disbResult['data'];
            }
        }

        out($stats);

    // ─────────────────────────────────────────────────────────────
    // META: List available endpoints
    // ─────────────────────────────────────────────────────────────

    /**
     * GET ?action=endpoints
     * Returns a full list of all available HR4 bridge actions and their status.
     */
    case 'endpoints':
        out([
            'success' => true,
            'bridge'  => 'HR4 Integration API v1.0',
            'note'    => 'HR4 = source of truth for Employee Master Data, Payroll, Compensation, HMO/Benefits',
            'endpoints' => [
                // Health
                ['action' => 'health',              'method' => 'GET',  'auth' => false, 'enabled' => true,                    'description' => 'Check HR4 connectivity'],

                // Employees
                ['action' => 'employees',           'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_EMPLOYEES,    'description' => 'All employees with relations',         'params' => 'search?, status?, department?'],
                ['action' => 'employee',            'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_EMPLOYEES,    'description' => 'Single employee by ID',                'params' => 'id'],
                ['action' => 'employee_by_email',   'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_EMPLOYEES,    'description' => 'Single employee by email',             'params' => 'email'],
                ['action' => 'employee_profile',    'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_EMPLOYEES,    'description' => 'Full employee profile (aggregate)',    'params' => 'id|email'],

                // Positions
                ['action' => 'positions',           'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_POSITIONS,    'description' => 'All positions with job titles',        'params' => 'department?, status?'],
                ['action' => 'vacant_positions',    'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_POSITIONS,    'description' => 'Vacant positions only'],

                // Contracts & Legal
                ['action' => 'contracts',           'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_CONTRACTS,    'description' => 'Employee contracts',                   'params' => 'employee_id?, expiring_within_days?'],
                ['action' => 'government_ids',      'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_GOVT_IDS,     'description' => 'Government IDs (SSS/TIN/etc.)',       'params' => 'employee_id?'],
                ['action' => 'terminations',        'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_LIFECYCLE,    'description' => 'Terminated/separated employees'],

                // Payroll
                ['action' => 'payslips',            'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_PAYROLL,      'description' => 'All paid payslips',                    'params' => 'employee_id?, period?'],
                ['action' => 'payslip',             'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_PAYROLL,      'description' => 'Payslips for single employee',         'params' => 'employee_id'],
                ['action' => 'payslip_summary',     'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_PAYROLL,      'description' => 'Payroll statistics summary'],
                ['action' => 'disbursement_latest', 'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_DISBURSEMENT, 'description' => 'Latest disbursement batch'],

                // Compensation
                ['action' => 'compensation',        'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_COMPENSATION, 'description' => 'Employee allowances & deductions',    'params' => 'employee_id?'],

                // Aggregated
                ['action' => 'departments',         'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_EMPLOYEES,    'description' => 'Department directory with counts'],
                ['action' => 'department_detail',   'method' => 'GET',  'auth' => true,  'enabled' => HR4_ENABLE_EMPLOYEES,    'description' => 'Department detail with employees & contracts', 'params' => 'department'],
                ['action' => 'dashboard',           'method' => 'GET',  'auth' => true,  'enabled' => true,                    'description' => 'HR4 overview dashboard (aggregate)'],
            ]
        ]);

    // ─────────────────────────────────────────────────────────────
    default:
        out([
            'success' => false,
            'error'   => "Unknown action: '{$action}'",
            'hint'    => 'Use ?action=endpoints to see all available actions'
        ], 400);
}
