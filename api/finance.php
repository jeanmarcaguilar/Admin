<?php
/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * API Bridge: Finance Integration
 * ════════════════════════════════════════════════════════════════════
 *
 * Proxy between Admin frontend and the Finance PHP API.
 *   1. Auth is checked (Admin session required)
 *   2. Finance URL resolution is centralized (domain ↔ localhost)
 *   3. Response format is normalized for Admin's JS
 *
 * Usage:  /api/finance.php?action=<action>&...params
 *
 * ── Endpoint Map ──────────────────────────────────────────────────
 *  action                | Finance Route                            | Description
 * ───────────────────────|──────────────────────────────────────────|────────────────────────────
 *  health                | GET /budget_api.php                     | Connectivity check
 *  budgets               | GET /budget_api.php                     | All budget proposals
 *  budget                | GET /budget_api.php?id=X                | Single budget proposal
 *  disbursements         | GET /disbursement_api.php               | All disbursement requests
 *  proposals             | GET /get_public_proposals.php           | Public proposals (with flags)
 *  admin_proposals       | GET /manage_proposals.php               | Admin-received proposals
 *  users                 | GET /users_api.php                      | Finance user directory
 *  dashboard             | Aggregate: budgets + disbursements      | Finance overview stats
 *  endpoints             | —                                        | Self-documenting endpoint list
 * ════════════════════════════════════════════════════════════════════
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../config/finance.php';

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

$action = $_GET['action'] ?? '';

// ═══════════════════════════════════════════════════════════════
// HEALTH CHECK (no auth required)
// ═══════════════════════════════════════════════════════════════
if ($action === 'health') {
    $result = finance_api('/budget_api.php');

    out([
        'success'        => true,
        'finance_alive'  => $result['success'] || ($result['status'] >= 200 && $result['status'] < 500),
        'status_code'    => $result['status'],
        'timestamp'      => date('c'),
    ]);
}

// All other actions require auth
requireAuth();

// ═══════════════════════════════════════════════════════════════
//  BUDGET PROPOSALS
// ═══════════════════════════════════════════════════════════════

if ($action === 'budgets') {
    if (!FINANCE_ENABLE_BUDGETS) out(['success' => false, 'error' => 'Finance budgets integration disabled'], 403);

    $result = finance_api('/budget_api.php');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch budgets from Finance'], $result['status'] ?: 502);
    }

    $budgets = $result['data'];
    if (!is_array($budgets)) $budgets = [];

    // If the response has a wrapper like { data: [...] }
    if (isset($budgets['data']) && is_array($budgets['data'])) {
        $budgets = $budgets['data'];
    }

    // Optional filters
    $department = $_GET['department'] ?? '';
    $status     = $_GET['status'] ?? '';
    $fiscal     = $_GET['fiscal_year'] ?? '';

    if ($department) {
        $budgets = array_values(array_filter($budgets, fn($b) =>
            stripos($b['department'] ?? $b['department_name'] ?? '', $department) !== false
        ));
    }
    if ($status) {
        $budgets = array_values(array_filter($budgets, fn($b) =>
            stripos($b['status'] ?? '', $status) !== false
        ));
    }
    if ($fiscal) {
        $budgets = array_values(array_filter($budgets, fn($b) =>
            ($b['fiscal_year'] ?? '') == $fiscal
        ));
    }

    // Compute summary
    $totalAmount    = array_sum(array_column($budgets, 'total_amount'));
    $totalRemaining = array_sum(array_column($budgets, 'remaining_amount'));

    out([
        'success' => true,
        'count'   => count($budgets),
        'summary' => [
            'total_budgeted'  => round($totalAmount, 2),
            'total_remaining' => round($totalRemaining, 2),
            'total_spent'     => round($totalAmount - $totalRemaining, 2),
        ],
        'data'    => $budgets,
        'source'  => 'finance'
    ]);
}

// ─── Single Budget Proposal ──────────────────────────────────
if ($action === 'budget') {
    if (!FINANCE_ENABLE_BUDGETS) out(['success' => false, 'error' => 'Finance budgets integration disabled'], 403);

    $id = $_GET['id'] ?? '';
    if (!$id) out(['success' => false, 'error' => 'Missing required parameter: id'], 400);

    $result = finance_api('/budget_api.php', 'GET', [], ['id' => $id]);

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch budget'], $result['status'] ?: 502);
    }

    out([
        'success' => true,
        'data'    => $result['data'],
        'source'  => 'finance'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  DISBURSEMENT REQUESTS
// ═══════════════════════════════════════════════════════════════

if ($action === 'disbursements') {
    if (!FINANCE_ENABLE_DISBURSEMENTS) out(['success' => false, 'error' => 'Finance disbursements integration disabled'], 403);

    $result = finance_api('/disbursement_api.php');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch disbursements from Finance'], $result['status'] ?: 502);
    }

    $items = $result['data'];
    if (!is_array($items)) $items = [];
    if (isset($items['data']) && is_array($items['data'])) $items = $items['data'];

    // Optional filters
    $status     = $_GET['status'] ?? '';
    $department = $_GET['department'] ?? '';

    if ($status) {
        $items = array_values(array_filter($items, fn($d) =>
            stripos($d['status'] ?? '', $status) !== false
        ));
    }
    if ($department) {
        $items = array_values(array_filter($items, fn($d) =>
            stripos($d['department'] ?? $d['department_name'] ?? '', $department) !== false
        ));
    }

    // Compute totals
    $totalAmount = array_sum(array_column($items, 'amount'));
    $pending  = count(array_filter($items, fn($d) => stripos($d['status'] ?? '', 'pending') !== false));
    $released = count(array_filter($items, fn($d) => stripos($d['status'] ?? '', 'released') !== false || stripos($d['status'] ?? '', 'approved') !== false));

    out([
        'success' => true,
        'count'   => count($items),
        'summary' => [
            'total_amount' => round($totalAmount, 2),
            'pending'      => $pending,
            'released'     => $released,
        ],
        'data'    => $items,
        'source'  => 'finance'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  PUBLIC PROPOSALS
// ═══════════════════════════════════════════════════════════════

if ($action === 'proposals') {
    if (!FINANCE_ENABLE_PROPOSALS) out(['success' => false, 'error' => 'Finance proposals integration disabled'], 403);

    $result = finance_api('/get_public_proposals.php');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch proposals from Finance'], $result['status'] ?: 502);
    }

    $proposals = $result['data'];
    if (!is_array($proposals)) $proposals = [];
    if (isset($proposals['data']) && is_array($proposals['data'])) $proposals = $proposals['data'];

    out([
        'success' => true,
        'count'   => count($proposals),
        'data'    => $proposals,
        'source'  => 'finance'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  ADMIN-RECEIVED PROPOSALS
// ═══════════════════════════════════════════════════════════════

if ($action === 'admin_proposals') {
    if (!FINANCE_ENABLE_PROPOSALS) out(['success' => false, 'error' => 'Finance proposals integration disabled'], 403);

    $result = finance_api('/manage_proposals.php');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch admin proposals from Finance'], $result['status'] ?: 502);
    }

    $proposals = $result['data'];
    if (!is_array($proposals)) $proposals = [];
    if (isset($proposals['data']) && is_array($proposals['data'])) $proposals = $proposals['data'];

    out([
        'success' => true,
        'count'   => count($proposals),
        'data'    => $proposals,
        'source'  => 'finance'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  FINANCE USERS
// ═══════════════════════════════════════════════════════════════

if ($action === 'users') {
    if (!FINANCE_ENABLE_USERS) out(['success' => false, 'error' => 'Finance users integration disabled'], 403);

    $result = finance_api('/users_api.php');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch users from Finance'], $result['status'] ?: 502);
    }

    $users = $result['data'];
    if (!is_array($users)) $users = [];
    if (isset($users['data']) && is_array($users['data'])) $users = $users['data'];

    out([
        'success' => true,
        'count'   => count($users),
        'data'    => $users,
        'source'  => 'finance'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  DASHBOARD — Aggregate statistics
// ═══════════════════════════════════════════════════════════════

if ($action === 'dashboard') {
    $stats = [
        'budgets'       => ['total' => 0, 'approved' => 0, 'pending' => 0, 'total_amount' => 0, 'remaining' => 0],
        'disbursements' => ['total' => 0, 'pending' => 0, 'released' => 0, 'total_amount' => 0],
        'proposals'     => ['total' => 0, 'approved' => 0],
    ];

    // Budgets
    $budgetRes = finance_api('/budget_api.php');
    if ($budgetRes['success']) {
        $bData = $budgetRes['data'];
        if (isset($bData['data'])) $bData = $bData['data'];
        if (is_array($bData)) {
            $stats['budgets']['total'] = count($bData);
            $stats['budgets']['approved'] = count(array_filter($bData, fn($b) => stripos($b['status'] ?? '', 'approved') !== false));
            $stats['budgets']['pending']  = count(array_filter($bData, fn($b) => stripos($b['status'] ?? '', 'pending') !== false));
            $stats['budgets']['total_amount'] = round(array_sum(array_column($bData, 'total_amount')), 2);
            $stats['budgets']['remaining']    = round(array_sum(array_column($bData, 'remaining_amount')), 2);
        }
    }

    // Disbursements
    $disbRes = finance_api('/disbursement_api.php');
    if ($disbRes['success']) {
        $dData = $disbRes['data'];
        if (isset($dData['data'])) $dData = $dData['data'];
        if (is_array($dData)) {
            $stats['disbursements']['total'] = count($dData);
            $stats['disbursements']['pending']  = count(array_filter($dData, fn($d) => stripos($d['status'] ?? '', 'pending') !== false));
            $stats['disbursements']['released'] = count(array_filter($dData, fn($d) => stripos($d['status'] ?? '', 'released') !== false || stripos($d['status'] ?? '', 'approved') !== false));
            $stats['disbursements']['total_amount'] = round(array_sum(array_column($dData, 'amount')), 2);
        }
    }

    // Proposals
    $propRes = finance_api('/get_public_proposals.php');
    if ($propRes['success']) {
        $pData = $propRes['data'];
        if (isset($pData['data'])) $pData = $pData['data'];
        if (is_array($pData)) {
            $stats['proposals']['total'] = count($pData);
            $stats['proposals']['approved'] = count(array_filter($pData, fn($p) =>
                ($p['is_approved'] ?? false) === true || ($p['is_approved'] ?? 0) === 1 || stripos($p['status'] ?? '', 'approved') !== false
            ));
        }
    }

    out([
        'success'   => true,
        'data'      => $stats,
        'timestamp' => date('c'),
        'source'    => 'finance_aggregate'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  ENDPOINTS — Self-documenting
// ═══════════════════════════════════════════════════════════════

if ($action === 'endpoints') {
    out([
        'success' => true,
        'bridge'  => 'Finance Integration — Admin API Bridge',
        'usage'   => '/api/finance.php?action=<action>&...params',
        'endpoints' => [
            ['action' => 'health',          'method' => 'GET', 'auth' => false, 'description' => 'Finance connectivity check'],
            ['action' => 'budgets',         'method' => 'GET', 'auth' => true,  'description' => 'All budget proposals', 'params' => 'department, status, fiscal_year'],
            ['action' => 'budget',          'method' => 'GET', 'auth' => true,  'description' => 'Single budget proposal', 'params' => 'id (required)'],
            ['action' => 'disbursements',   'method' => 'GET', 'auth' => true,  'description' => 'All disbursement requests', 'params' => 'status, department'],
            ['action' => 'proposals',       'method' => 'GET', 'auth' => true,  'description' => 'Public proposals with approval flags'],
            ['action' => 'admin_proposals', 'method' => 'GET', 'auth' => true,  'description' => 'Admin-received proposals'],
            ['action' => 'users',           'method' => 'GET', 'auth' => true,  'description' => 'Finance user directory'],
            ['action' => 'dashboard',       'method' => 'GET', 'auth' => true,  'description' => 'Aggregate finance overview stats'],
            ['action' => 'endpoints',       'method' => 'GET', 'auth' => false, 'description' => 'This endpoint list'],
        ]
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  UNKNOWN ACTION
// ═══════════════════════════════════════════════════════════════

out([
    'success' => false,
    'error'   => "Unknown action: '{$action}'",
    'hint'    => 'Use ?action=endpoints to see available actions'
], 400);
