<?php
/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * API Bridge: HR2 Integration
 * ════════════════════════════════════════════════════════════════════
 *
 * This file acts as a proxy/bridge between the Admin frontend and
 * the HR2 Laravel API. All calls go through here so that:
 *   1. Auth is checked (Admin session required)
 *   2. HR2 URL resolution is centralized (domain ↔ localhost)
 *   3. Response format is normalized for Admin's JS
 *
 * Usage:  /api/hr2.php?action=<action>&...params
 *
 * ── Endpoint Map ──────────────────────────────────────────────────
 *  action                  | HR2 Route                          | Description
 * ─────────────────────────|────────────────────────────────────|──────────────────────────
 *  employees               | GET  /ess/syncdb (trigger) or     | List all employees (from HR4 via HR2)
 *                          |      /ess/employee/by-email/{e}   |
 *  employee                | GET  /ess/employee/{id}           | Single employee by HR4 ID
 *  employee_by_email       | GET  /ess/employee/by-email/{e}   | Single employee by email
 *  attendance              | GET  /ess/attendance/{id}         | Attendance log for employee
 *  leaves                  | GET  /leaves                      | All leave requests (filterable)
 *  leave_types             | GET  /leaves/types                | Available leave types
 *  leave_balances          | GET  /leaves/balances/{email}     | Employee leave balances
 *  leave_stats             | GET  /leaves/stats/summary        | Leave statistics summary
 *  training_bookings       | GET  /training-room-bookings      | All training room bookings
 *  training_booking        | GET  /training-room-bookings/{id} | Single booking detail
 *  training_stats          | GET  /training-room-bookings/stats| Booking statistics
 *  successors              | GET  /successors                  | All promotion/successor records
 *  successor               | GET  /successors/{employee_id}    | Single successor by employee_id
 *  competencies            | GET  /assigned-competencies       | Assigned competency records
 *  jobs                    | GET  /jobs                        | Job titles from HR4
 *  dashboard               | GET  /dashboard/realtime          | HR2 real-time dashboard
 *  health                  | —                                 | Connectivity check (no auth)
 * ════════════════════════════════════════════════════════════════════
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../config/hr2.php';

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

// ── Grab action ──
$action = $_GET['action'] ?? '';

// Health-check endpoint (no auth, used by JS to detect connectivity)
if ($action === 'health') {
    $result = hr2_api('/jobs', 'GET');
    out([
        'success'   => $result['success'],
        'hr2_alive' => $result['success'],
        'latency'   => 'ok',
        'domain'    => HR2_API_DOMAIN,
        'local'     => HR2_API_LOCAL,
    ]);
}

// All other actions require login
requireAuth();

switch ($action) {

    // ─────────────────────────────────────────────────────────────
    // EMPLOYEES
    // ─────────────────────────────────────────────────────────────

    /**
     * List all employees (via HR2's sync with HR4)
     * GET ?action=employees
     * Optional: &search=<name_or_email>
     */
    case 'employees':
        if (!HR2_ENABLE_EMPLOYEES) out(['success' => false, 'error' => 'Employee integration disabled'], 403);

        // HR2 doesn't have a direct "list all employees" GET.
        // We use the jobs endpoint (which calls HR4) to get employee list.
        // Or better: query HR2's local users table via a custom endpoint.
        // For now, use the /jobs endpoint which returns employees+jobs from HR4.
        $result = hr2_api('/jobs');
        
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch employees from HR2'], $result['status'] ?: 502);
        }

        $employees = $result['data']['data'] ?? $result['data'] ?? [];
        
        // Optional search filter (client-side for now)
        $search = strtolower(trim($_GET['search'] ?? ''));
        if ($search !== '') {
            $employees = array_values(array_filter($employees, function($emp) use ($search) {
                $name  = strtolower($emp['full_name'] ?? $emp['firstname'] ?? '');
                $email = strtolower($emp['email'] ?? '');
                $id    = strtolower($emp['employee_id'] ?? '');
                return str_contains($name, $search) || str_contains($email, $search) || str_contains($id, $search);
            }));
        }

        out([
            'success' => true,
            'count'   => count($employees),
            'data'    => $employees,
            'source'  => 'hr2 → hr4'
        ]);

    /**
     * Get single employee by HR4 ID
     * GET ?action=employee&id=<employee_id>
     */
    case 'employee':
        if (!HR2_ENABLE_EMPLOYEES) out(['success' => false, 'error' => 'Employee integration disabled'], 403);

        $id = $_GET['id'] ?? '';
        if (!$id) out(['success' => false, 'error' => 'Missing required parameter: id'], 400);

        $result = hr2_api("/ess/employee/{$id}");
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Employee not found'], $result['status'] ?: 404);
        }

        out([
            'success'  => true,
            'employee' => $result['data']['employee'] ?? $result['data'],
            'source'   => 'hr2 → hr4'
        ]);

    /**
     * Get single employee by email
     * GET ?action=employee_by_email&email=<email>
     */
    case 'employee_by_email':
        if (!HR2_ENABLE_EMPLOYEES) out(['success' => false, 'error' => 'Employee integration disabled'], 403);

        $email = $_GET['email'] ?? '';
        if (!$email) out(['success' => false, 'error' => 'Missing required parameter: email'], 400);

        $result = hr2_api("/ess/employee/by-email/{$email}");
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Employee not found'], $result['status'] ?: 404);
        }

        out([
            'success'  => true,
            'employee' => $result['data']['employee'] ?? $result['data'],
            'source'   => 'hr2 → hr4'
        ]);

    // ─────────────────────────────────────────────────────────────
    // ATTENDANCE
    // ─────────────────────────────────────────────────────────────

    /**
     * Get attendance/schedule log for an employee
     * GET ?action=attendance&employee_id=<id>
     */
    case 'attendance':
        if (!HR2_ENABLE_ATTENDANCE) out(['success' => false, 'error' => 'Attendance integration disabled'], 403);

        $empId = $_GET['employee_id'] ?? '';
        if (!$empId) out(['success' => false, 'error' => 'Missing required parameter: employee_id'], 400);

        $result = hr2_api("/ess/attendance/{$empId}");
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Attendance not found'], $result['status'] ?: 404);
        }

        out([
            'success'    => true,
            'attendance' => $result['data']['attendance'] ?? $result['data'],
            'source'     => 'hr2 → hr3'
        ]);

    // ─────────────────────────────────────────────────────────────
    // LEAVES
    // ─────────────────────────────────────────────────────────────

    /**
     * Get all leave requests (filterable)
     * GET ?action=leaves&status=<pending|approved|rejected>&employee_id=<id>
     */
    case 'leaves':
        if (!HR2_ENABLE_LEAVES) out(['success' => false, 'error' => 'Leave integration disabled'], 403);

        $query = [];
        if (!empty($_GET['status']))      $query['status'] = $_GET['status'];
        if (!empty($_GET['employee_id'])) $query['employee_id'] = $_GET['employee_id'];
        if (!empty($_GET['page']))        $query['page'] = $_GET['page'];

        $result = hr2_api('/leaves', 'GET', [], $query);
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch leaves'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'data'    => $result['data']['data'] ?? $result['data'],
            'source'  => 'hr2'
        ]);

    /**
     * Get available leave types
     * GET ?action=leave_types
     */
    case 'leave_types':
        if (!HR2_ENABLE_LEAVES) out(['success' => false, 'error' => 'Leave integration disabled'], 403);

        $result = hr2_api('/leaves/types');
        out($result['success']
            ? ['success' => true, 'data' => $result['data']['data'] ?? $result['data']]
            : ['success' => false, 'error' => $result['error'] ?? 'Failed to fetch leave types']
        );

    /**
     * Get leave balances for a specific employee
     * GET ?action=leave_balances&email=<email>
     */
    case 'leave_balances':
        if (!HR2_ENABLE_LEAVES) out(['success' => false, 'error' => 'Leave integration disabled'], 403);

        $email = $_GET['email'] ?? '';
        if (!$email) out(['success' => false, 'error' => 'Missing required parameter: email'], 400);

        $result = hr2_api("/leaves/balances/{$email}");
        out($result['success']
            ? ['success' => true, 'data' => $result['data']['data'] ?? $result['data'], 'source' => 'hr2']
            : ['success' => false, 'error' => $result['error'] ?? 'Failed to fetch leave balances']
        );

    /**
     * Get leave statistics summary
     * GET ?action=leave_stats
     */
    case 'leave_stats':
        if (!HR2_ENABLE_LEAVES) out(['success' => false, 'error' => 'Leave integration disabled'], 403);

        $result = hr2_api('/leaves/stats/summary');
        out($result['success']
            ? ['success' => true, 'data' => $result['data']['data'] ?? $result['data'], 'source' => 'hr2']
            : ['success' => false, 'error' => $result['error'] ?? 'Failed to fetch leave stats']
        );

    // ─────────────────────────────────────────────────────────────
    // TRAINING ROOM BOOKINGS
    // ─────────────────────────────────────────────────────────────

    /**
     * List all training room bookings
     * GET ?action=training_bookings&status=<status>
     */
    case 'training_bookings':
        if (!HR2_ENABLE_TRAINING) out(['success' => false, 'error' => 'Training integration disabled'], 403);

        $query = [];
        if (!empty($_GET['status'])) $query['status'] = $_GET['status'];
        if (!empty($_GET['page']))   $query['page'] = $_GET['page'];

        $result = hr2_api('/training-room-bookings', 'GET', [], $query);
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch training bookings'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'data'    => $result['data']['data'] ?? $result['data'],
            'source'  => 'hr2'
        ]);

    /**
     * Get a single training room booking
     * GET ?action=training_booking&id=<id>
     */
    case 'training_booking':
        if (!HR2_ENABLE_TRAINING) out(['success' => false, 'error' => 'Training integration disabled'], 403);

        $id = $_GET['id'] ?? '';
        if (!$id) out(['success' => false, 'error' => 'Missing required parameter: id'], 400);

        $result = hr2_api("/training-room-bookings/{$id}");
        out($result['success']
            ? ['success' => true, 'data' => $result['data']['data'] ?? $result['data'], 'source' => 'hr2']
            : ['success' => false, 'error' => $result['error'] ?? 'Booking not found']
        );

    /**
     * Get training room booking statistics
     * GET ?action=training_stats
     */
    case 'training_stats':
        if (!HR2_ENABLE_TRAINING) out(['success' => false, 'error' => 'Training integration disabled'], 403);

        $result = hr2_api('/training-room-bookings/stats');
        out($result['success']
            ? ['success' => true, 'data' => $result['data']['data'] ?? $result['data'], 'source' => 'hr2']
            : ['success' => false, 'error' => $result['error'] ?? 'Failed to fetch training stats']
        );

    // ─────────────────────────────────────────────────────────────
    // SUCCESSION PLANNING / PROMOTIONS
    // ─────────────────────────────────────────────────────────────

    /**
     * List all successor/promotion records
     * GET ?action=successors&status=<status>&employee_id=<id>
     */
    case 'successors':
        if (!HR2_ENABLE_SUCCESSORS) out(['success' => false, 'error' => 'Successors integration disabled'], 403);

        $query = [];
        if (!empty($_GET['status']))      $query['status'] = $_GET['status'];
        if (!empty($_GET['employee_id'])) $query['employee_id'] = $_GET['employee_id'];

        $result = hr2_api('/successors', 'GET', [], $query);
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch successors'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'count'   => $result['data']['count'] ?? count($result['data']['data'] ?? []),
            'data'    => $result['data']['data'] ?? $result['data'],
            'source'  => 'hr2'
        ]);

    /**
     * Get a single successor record by employee_id
     * GET ?action=successor&employee_id=<id>
     */
    case 'successor':
        if (!HR2_ENABLE_SUCCESSORS) out(['success' => false, 'error' => 'Successors integration disabled'], 403);

        $empId = $_GET['employee_id'] ?? '';
        if (!$empId) out(['success' => false, 'error' => 'Missing required parameter: employee_id'], 400);

        $result = hr2_api("/successors/{$empId}");
        out($result['success']
            ? ['success' => true, 'data' => $result['data']['data'] ?? $result['data'], 'source' => 'hr2']
            : ['success' => false, 'error' => $result['error'] ?? 'Successor not found']
        );

    // ─────────────────────────────────────────────────────────────
    // COMPETENCIES
    // ─────────────────────────────────────────────────────────────

    /**
     * Get assigned competencies
     * GET ?action=competencies&employee_id=<id>
     */
    case 'competencies':
        if (!HR2_ENABLE_COMPETENCIES) out(['success' => false, 'error' => 'Competency integration disabled'], 403);

        $query = [];
        if (!empty($_GET['employee_id'])) $query['employee_id'] = $_GET['employee_id'];

        $result = hr2_api('/assigned-competencies', 'GET', [], $query);
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch competencies'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'data'    => $result['data']['data'] ?? $result['data'],
            'source'  => 'hr2'
        ]);

    // ─────────────────────────────────────────────────────────────
    // JOBS (Job Titles / Positions)
    // ─────────────────────────────────────────────────────────────

    /**
     * Get all job titles/positions from HR4 via HR2
     * GET ?action=jobs
     */
    case 'jobs':
        if (!HR2_ENABLE_JOBS) out(['success' => false, 'error' => 'Jobs integration disabled'], 403);

        $result = hr2_api('/jobs');
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch jobs'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'data'    => $result['data']['data'] ?? $result['data'],
            'source'  => 'hr2 → hr4'
        ]);

    // ─────────────────────────────────────────────────────────────
    // HR DASHBOARD (Real-time stats)
    // ─────────────────────────────────────────────────────────────

    /**
     * Get HR2 real-time dashboard data
     * GET ?action=dashboard
     */
    case 'dashboard':
        if (!HR2_ENABLE_DASHBOARD) out(['success' => false, 'error' => 'Dashboard integration disabled'], 403);

        $result = hr2_api('/dashboard/realtime');
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch HR dashboard'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'data'    => $result['data']['data'] ?? $result['data'],
            'source'  => 'hr2'
        ]);

    // ─────────────────────────────────────────────────────────────
    // AGGREGATE: Employee Profile (combined data from multiple endpoints)
    // ─────────────────────────────────────────────────────────────

    /**
     * Get a full employee profile with leaves, competencies, attendance
     * GET ?action=employee_profile&email=<email>&employee_id=<id>
     * 
     * Returns combined data from multiple HR2 endpoints in one call,
     * so the Admin frontend doesn't need to make 5 separate requests.
     */
    case 'employee_profile':
        $email = $_GET['email'] ?? '';
        $empId = $_GET['employee_id'] ?? '';
        if (!$email && !$empId) {
            out(['success' => false, 'error' => 'Provide email or employee_id'], 400);
        }

        $profile = ['success' => true, 'source' => 'hr2 (aggregate)'];

        // 1. Employee basic info
        if ($email && HR2_ENABLE_EMPLOYEES) {
            $empResult = hr2_api("/ess/employee/by-email/{$email}");
            $profile['employee'] = $empResult['success'] ? ($empResult['data']['employee'] ?? null) : null;
            // Extract employee_id if we got it
            if (!$empId && isset($profile['employee']['employee_id'])) {
                $empId = $profile['employee']['employee_id'];
            }
        } elseif ($empId && HR2_ENABLE_EMPLOYEES) {
            $empResult = hr2_api("/ess/employee/{$empId}");
            $profile['employee'] = $empResult['success'] ? ($empResult['data']['employee'] ?? null) : null;
        }

        // 2. Leave balances (by email)
        if ($email && HR2_ENABLE_LEAVES) {
            $leaveResult = hr2_api("/leaves/balances/{$email}");
            $profile['leave_balances'] = $leaveResult['success'] ? ($leaveResult['data']['data'] ?? $leaveResult['data']) : null;
        }

        // 3. Attendance (by employee_id)
        if ($empId && HR2_ENABLE_ATTENDANCE) {
            $attResult = hr2_api("/ess/attendance/{$empId}");
            $profile['attendance'] = $attResult['success'] ? ($attResult['data']['attendance'] ?? null) : null;
        }

        // 4. Competencies (by employee_id)
        if ($empId && HR2_ENABLE_COMPETENCIES) {
            $compResult = hr2_api('/assigned-competencies', 'GET', [], ['employee_id' => $empId]);
            $profile['competencies'] = $compResult['success'] ? ($compResult['data']['data'] ?? $compResult['data']) : null;
        }

        // 5. Successor/promotion record (by employee_id)
        if ($empId && HR2_ENABLE_SUCCESSORS) {
            $sucResult = hr2_api("/successors/{$empId}");
            $profile['succession'] = $sucResult['success'] ? ($sucResult['data']['data'] ?? $sucResult['data']) : null;
        }

        out($profile);

    // ─────────────────────────────────────────────────────────────
    // SYNC: Push employee updates to HR2 (trigger)
    // ─────────────────────────────────────────────────────────────

    /**
     * Trigger HR2 ↔ HR4 employee sync
     * POST ?action=sync_employees
     * 
     * This calls HR2's /ess/syncdb endpoint which pulls latest employee
     * data from HR4 into HR2's local users table.
     * Requires super_admin or admin role.
     */
    case 'sync_employees':
        if (session_status() === PHP_SESSION_NONE) session_start();
        $role = $_SESSION['user']['role'] ?? '';
        if (!in_array($role, ['super_admin', 'admin'])) {
            out(['success' => false, 'error' => 'Insufficient privileges — admin required'], 403);
        }

        $result = hr2_api('/ess/syncdb', 'POST');
        if (!$result['success']) {
            out(['success' => false, 'error' => $result['error'] ?? 'Sync failed'], $result['status'] ?: 502);
        }

        out([
            'success' => true,
            'message' => 'Employee sync triggered',
            'result'  => $result['data'],
            'source'  => 'hr2 → hr4'
        ]);

    // ─────────────────────────────────────────────────────────────
    // META: List available endpoints
    // ─────────────────────────────────────────────────────────────

    /**
     * GET ?action=endpoints
     * Returns a map of all available HR2 bridge actions and their status.
     */
    case 'endpoints':
        out([
            'success' => true,
            'bridge'  => 'HR2 Integration API v1.0',
            'endpoints' => [
                ['action' => 'health',            'method' => 'GET',  'auth' => false, 'enabled' => true,                   'description' => 'Check HR2 connectivity'],
                ['action' => 'employees',         'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_EMPLOYEES,    'description' => 'List all employees', 'params' => 'search?'],
                ['action' => 'employee',          'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_EMPLOYEES,    'description' => 'Get employee by ID', 'params' => 'id'],
                ['action' => 'employee_by_email', 'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_EMPLOYEES,    'description' => 'Get employee by email', 'params' => 'email'],
                ['action' => 'employee_profile',  'method' => 'GET',  'auth' => true,  'enabled' => true,                   'description' => 'Full employee profile (aggregate)', 'params' => 'email|employee_id'],
                ['action' => 'attendance',        'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_ATTENDANCE,   'description' => 'Employee attendance', 'params' => 'employee_id'],
                ['action' => 'leaves',            'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_LEAVES,       'description' => 'Leave requests', 'params' => 'status?, employee_id?, page?'],
                ['action' => 'leave_types',       'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_LEAVES,       'description' => 'Leave type list'],
                ['action' => 'leave_balances',    'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_LEAVES,       'description' => 'Employee leave balance', 'params' => 'email'],
                ['action' => 'leave_stats',       'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_LEAVES,       'description' => 'Leave statistics summary'],
                ['action' => 'training_bookings', 'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_TRAINING,     'description' => 'Training room bookings', 'params' => 'status?, page?'],
                ['action' => 'training_booking',  'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_TRAINING,     'description' => 'Single training booking', 'params' => 'id'],
                ['action' => 'training_stats',    'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_TRAINING,     'description' => 'Training booking stats'],
                ['action' => 'successors',        'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_SUCCESSORS,   'description' => 'Successor/promotion list', 'params' => 'status?, employee_id?'],
                ['action' => 'successor',         'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_SUCCESSORS,   'description' => 'Single successor record', 'params' => 'employee_id'],
                ['action' => 'competencies',      'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_COMPETENCIES, 'description' => 'Assigned competencies', 'params' => 'employee_id?'],
                ['action' => 'jobs',              'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_JOBS,         'description' => 'Job titles/positions'],
                ['action' => 'dashboard',         'method' => 'GET',  'auth' => true,  'enabled' => HR2_ENABLE_DASHBOARD,    'description' => 'HR2 real-time dashboard'],
                ['action' => 'sync_employees',    'method' => 'POST', 'auth' => true,  'enabled' => HR2_ENABLE_EMPLOYEES,    'description' => 'Trigger HR4→HR2 employee sync (admin only)'],
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
