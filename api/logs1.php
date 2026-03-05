<?php
/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * API Bridge: Logs1 (Logistics) Integration
 * ════════════════════════════════════════════════════════════════════
 *
 * Proxy between Admin frontend and the Logs1 Laravel API.
 *   1. Auth is checked (Admin session required)
 *   2. Logs1 URL resolution is centralized (domain ↔ localhost)
 *   3. API key is managed server-side (never exposed to browser)
 *   4. Response format is normalized for Admin's JS
 *
 * Usage:  /api/logs1.php?action=<action>&...params
 *
 * ── Endpoint Map ──────────────────────────────────────────────────
 *  action                | Logs1 Route                                    | Description
 * ───────────────────────|────────────────────────────────────────────────|────────────────────────────
 *  health                | GET /psm/vendors (public)                     | Connectivity check
 *  --- PSM (Procurement) ---
 *  psm_purchases         | GET /psm/external/requisitions                | All purchase requisitions
 *  psm_budget            | GET /psm/external/budget                      | Current procurement budget
 *  psm_budget_logs       | GET /psm/external/budget/logs                 | Budget activity logs
 *  psm_vendors           | GET /psm/vendors                              | Vendor directory (public)
 *  psm_products          | GET /psm/external/products                    | Product catalog
 *  psm_budget_requests   | GET /psm/external/budget-requests             | Department budget requests
 *  --- SWS (Warehousing) ---
 *  sws_inventory         | GET /sws/external/room-requests (key-based)   | Room/inventory requests
 *  sws_warehouses        | GET /sws/warehouse (public test)              | Warehouses list
 *  --- PLT (Projects) ---
 *  plt_projects          | GET /plt/projects (JWT-protected fallback)    | Project list
 *  --- DTLR (Documents) ---
 *  dtlr_documents        | GET /dtlr/document-tracker/external           | External doc tracker
 *  --- ALMS (Assets) ---
 *  alms_assets           | GET /alms/external/maintenance                | Asset maintenance
 *  --- Dashboard ---
 *  dashboard             | Aggregate: PSM + SWS                          | Overview stats
 *  endpoints             | —                                              | Self-documenting list
 * ════════════════════════════════════════════════════════════════════
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once __DIR__ . '/../config/logs1.php';

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
    // Use public vendors endpoint as a lightweight check
    $result = logs1_api('/psm/vendors', 'GET', [], [], false);

    out([
        'success'     => true,
        'logs1_alive' => $result['success'] || ($result['status'] >= 200 && $result['status'] < 500),
        'status_code' => $result['status'],
        'timestamp'   => date('c'),
    ]);
}

// All other actions require auth
requireAuth();

// ═══════════════════════════════════════════════════════════════
//  PSM — PROCUREMENT & SUPPLIER MANAGEMENT
// ═══════════════════════════════════════════════════════════════

// ─── Purchase Orders / Requisitions ──────────────────────────
if ($action === 'psm_purchases') {
    if (!LOGS1_ENABLE_PSM_PURCHASES) out(['success' => false, 'error' => 'PSM purchases integration disabled'], 403);

    $result = logs1_api('/psm/external/requisitions');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch requisitions from Logs1'], $result['status'] ?: 502);
    }

    $items = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($items)) $items = [];

    // Apply optional filters
    $status = $_GET['status'] ?? '';
    $department = $_GET['department'] ?? '';

    if ($status) {
        $items = array_values(array_filter($items, fn($r) =>
            stripos($r['status'] ?? $r['req_status'] ?? '', $status) !== false
        ));
    }
    if ($department) {
        $items = array_values(array_filter($items, fn($r) =>
            stripos($r['department'] ?? $r['department_from'] ?? '', $department) !== false
        ));
    }

    out([
        'success' => true,
        'count'   => count($items),
        'data'    => $items,
        'source'  => 'logs1_psm'
    ]);
}

// ─── Current Procurement Budget ──────────────────────────────
if ($action === 'psm_budget') {
    if (!LOGS1_ENABLE_PSM_BUDGETS) out(['success' => false, 'error' => 'PSM budgets integration disabled'], 403);

    $result = logs1_api('/psm/external/budget');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch budget from Logs1'], $result['status'] ?: 502);
    }

    $budget = $result['data']['data'] ?? $result['data'] ?? [];

    out([
        'success' => true,
        'data'    => $budget,
        'source'  => 'logs1_psm'
    ]);
}

// ─── Budget Activity Logs ────────────────────────────────────
if ($action === 'psm_budget_logs') {
    if (!LOGS1_ENABLE_PSM_BUDGETS) out(['success' => false, 'error' => 'PSM budgets integration disabled'], 403);

    $result = logs1_api('/psm/external/budget/logs');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch budget logs'], $result['status'] ?: 502);
    }

    $logs = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($logs)) $logs = [];

    out([
        'success' => true,
        'count'   => count($logs),
        'data'    => $logs,
        'source'  => 'logs1_psm'
    ]);
}

// ─── Budget Requests from Departments ────────────────────────
if ($action === 'psm_budget_requests') {
    if (!LOGS1_ENABLE_PSM_BUDGETS) out(['success' => false, 'error' => 'PSM budgets integration disabled'], 403);

    $result = logs1_api('/psm/external/budget-requests');

    if (!$result['success']) {
        // Fallback: try the /budget-requests endpoint
        $result = logs1_api('/psm/external/budget-requests');
    }

    $requests = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($requests)) $requests = [];

    out([
        'success' => true,
        'count'   => count($requests),
        'data'    => $requests,
        'source'  => 'logs1_psm'
    ]);
}

// ─── Vendor Directory ────────────────────────────────────────
if ($action === 'psm_vendors') {
    if (!LOGS1_ENABLE_PSM_VENDORS) out(['success' => false, 'error' => 'PSM vendors integration disabled'], 403);

    $result = logs1_api('/psm/vendors', 'GET', [], [], false);

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch vendors from Logs1'], $result['status'] ?: 502);
    }

    $vendors = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($vendors)) $vendors = [];

    // Optional search
    $search = $_GET['search'] ?? '';
    if ($search) {
        $vendors = array_values(array_filter($vendors, fn($v) =>
            stripos($v['ven_name'] ?? $v['name'] ?? '', $search) !== false ||
            stripos($v['ven_type'] ?? $v['type'] ?? '', $search) !== false
        ));
    }

    out([
        'success' => true,
        'count'   => count($vendors),
        'data'    => $vendors,
        'source'  => 'logs1_psm'
    ]);
}

// ─── Product Catalog ─────────────────────────────────────────
if ($action === 'psm_products') {
    if (!LOGS1_ENABLE_PSM_PRODUCTS) out(['success' => false, 'error' => 'PSM products integration disabled'], 403);

    $result = logs1_api('/psm/external/products');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch products from Logs1'], $result['status'] ?: 502);
    }

    $products = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($products)) $products = [];

    out([
        'success' => true,
        'count'   => count($products),
        'data'    => $products,
        'source'  => 'logs1_psm'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  SWS — SMART WAREHOUSING SYSTEM
// ═══════════════════════════════════════════════════════════════

// ─── Room / Inventory Requests ───────────────────────────────
if ($action === 'sws_inventory') {
    if (!LOGS1_ENABLE_SWS_INVENTORY) out(['success' => false, 'error' => 'SWS inventory integration disabled'], 403);

    $result = logs1_api('/sws/external/room-requests');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch inventory from Logs1'], $result['status'] ?: 502);
    }

    $items = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($items)) $items = [];

    out([
        'success' => true,
        'count'   => count($items),
        'data'    => $items,
        'source'  => 'logs1_sws'
    ]);
}

// ─── Warehouses ──────────────────────────────────────────────
if ($action === 'sws_warehouses') {
    if (!LOGS1_ENABLE_SWS_WAREHOUSES) out(['success' => false, 'error' => 'SWS warehouses integration disabled'], 403);

    // Try external first, fallback to public
    $result = logs1_api('/sws/external/room-requests');

    $warehouses = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($warehouses)) $warehouses = [];

    out([
        'success' => true,
        'count'   => count($warehouses),
        'data'    => $warehouses,
        'source'  => 'logs1_sws'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  PLT — PROJECT LOGISTICS TRACKER
// ═══════════════════════════════════════════════════════════════

if ($action === 'plt_projects') {
    if (!LOGS1_ENABLE_PLT_PROJECTS) out(['success' => false, 'error' => 'PLT projects integration disabled'], 403);

    // PLT is JWT-protected; try external or public paths
    // Fallback: list from any accessible endpoint
    $result = logs1_api('/plt/projects', 'GET', [], [], true);

    $projects = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($projects)) $projects = [];

    // Optional status filter
    $status = $_GET['status'] ?? '';
    if ($status) {
        $projects = array_values(array_filter($projects, fn($p) =>
            stripos($p['proj_status'] ?? $p['status'] ?? '', $status) !== false
        ));
    }

    out([
        'success' => $result['success'],
        'count'   => count($projects),
        'data'    => $projects,
        'source'  => 'logs1_plt',
        'note'    => !$result['success'] ? 'PLT may require JWT auth — data may be limited' : null
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  DTLR — DOCUMENT TRACKING & LOGISTICS RECORD
// ═══════════════════════════════════════════════════════════════

if ($action === 'dtlr_documents') {
    if (!LOGS1_ENABLE_DTLR_DOCUMENTS) out(['success' => false, 'error' => 'DTLR document integration disabled'], 403);

    $result = logs1_api('/dtlr/document-tracker/external');

    if (!$result['success']) {
        out(['success' => false, 'error' => $result['error'] ?? 'Failed to fetch documents from Logs1'], $result['status'] ?: 502);
    }

    $documents = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($documents)) $documents = [];

    // Optional search
    $search = $_GET['search'] ?? '';
    if ($search) {
        $documents = array_values(array_filter($documents, fn($d) =>
            stripos($d['doc_title'] ?? $d['title'] ?? '', $search) !== false ||
            stripos($d['doc_type'] ?? $d['type'] ?? '', $search) !== false
        ));
    }

    out([
        'success' => true,
        'count'   => count($documents),
        'data'    => $documents,
        'source'  => 'logs1_dtlr'
    ]);
}

// ── DTLR — Proxy: view file inline ──────────────────────────
if ($action === 'dtlr_view_file') {
    if (!LOGS1_ENABLE_DTLR_DOCUMENTS) out(['success' => false, 'error' => 'DTLR disabled'], 403);
    $docId = $_GET['doc_id'] ?? '';
    if (!$docId) out(['success' => false, 'error' => 'doc_id required'], 400);

    // Proxy the binary response from Logs1
    $urls = [
        LOGS1_API_DOMAIN . '/dtlr/document-tracker/' . urlencode($docId) . '/view',
        LOGS1_API_LOCAL  . '/dtlr/document-tracker/' . urlencode($docId) . '/view',
    ];

    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
            $headerStr = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            // Forward content-type and disposition headers
            foreach (explode("\r\n", $headerStr) as $h) {
                $hl = strtolower(trim($h));
                if (str_starts_with($hl, 'content-type:') || str_starts_with($hl, 'content-disposition:')) {
                    header($h);
                }
            }
            header('Content-Length: ' . strlen($body));
            echo $body;
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'DTLR file not found or system unreachable']);
    exit;
}

// ── DTLR — Proxy: download file ─────────────────────────────
if ($action === 'dtlr_download_file') {
    if (!LOGS1_ENABLE_DTLR_DOCUMENTS) out(['success' => false, 'error' => 'DTLR disabled'], 403);
    $docId = $_GET['doc_id'] ?? '';
    if (!$docId) out(['success' => false, 'error' => 'doc_id required'], 400);

    $urls = [
        LOGS1_API_DOMAIN . '/dtlr/document-tracker/' . urlencode($docId) . '/download',
        LOGS1_API_LOCAL  . '/dtlr/document-tracker/' . urlencode($docId) . '/download',
    ];

    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER         => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300 && $response !== false) {
            $headerStr = substr($response, 0, $headerSize);
            $body = substr($response, $headerSize);

            foreach (explode("\r\n", $headerStr) as $h) {
                $hl = strtolower(trim($h));
                if (str_starts_with($hl, 'content-type:') || str_starts_with($hl, 'content-disposition:') || str_starts_with($hl, 'content-length:')) {
                    header($h);
                }
            }
            echo $body;
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'DTLR file not found or system unreachable']);
    exit;
}

// ── PSM — Proxy: product image ───────────────────────────────
if ($action === 'psm_product_image') {
    $imagePath = $_GET['path'] ?? '';
    if (!$imagePath) out(['success' => false, 'error' => 'path required'], 400);

    // Sanitize: only allow images/product-picture/... pattern
    if (!preg_match('#^images/product-picture/[a-zA-Z0-9_\-\.]+$#', $imagePath)) {
        out(['success' => false, 'error' => 'Invalid path'], 400);
    }

    $urls = [
        'https://logs1.microfinancial-1.com/' . $imagePath,
        'http://localhost:8002/' . $imagePath,
        'http://localhost/logs1/public/' . $imagePath,
    ];

    foreach ($urls as $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300 && $body !== false && strlen($body) > 0) {
            header('Content-Type: ' . ($contentType ?: 'image/jpeg'));
            header('Content-Length: ' . strlen($body));
            header('Cache-Control: public, max-age=86400');
            echo $body;
            exit;
        }
    }
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Image not found']);
    exit;
}

// ═══════════════════════════════════════════════════════════════
//  ALMS — ASSET LIFECYCLE & MAINTENANCE
// ═══════════════════════════════════════════════════════════════

if ($action === 'alms_assets') {
    if (!LOGS1_ENABLE_ALMS_ASSETS) out(['success' => false, 'error' => 'ALMS asset integration disabled'], 403);

    $result = logs1_api('/alms/external/maintenance');

    $assets = $result['data']['data'] ?? $result['data'] ?? [];
    if (!is_array($assets)) $assets = [];

    out([
        'success' => $result['success'],
        'count'   => count($assets),
        'data'    => $assets,
        'source'  => 'logs1_alms'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  DASHBOARD — Aggregate statistics
// ═══════════════════════════════════════════════════════════════

if ($action === 'dashboard') {
    $stats = [
        'procurement' => ['purchases' => 0, 'budget_total' => 0, 'budget_spent' => 0, 'budget_remaining' => 0, 'vendors' => 0],
        'warehouse'   => ['items' => 0, 'requests' => 0],
        'projects'    => ['total' => 0, 'active' => 0],
        'documents'   => ['total' => 0],
        'assets'      => ['total' => 0],
    ];

    // PSM Purchases
    $psmRes = logs1_api('/psm/external/requisitions');
    if ($psmRes['success']) {
        $psmData = $psmRes['data']['data'] ?? $psmRes['data'] ?? [];
        $stats['procurement']['purchases'] = is_array($psmData) ? count($psmData) : 0;
    }

    // PSM Budget
    $budgetRes = logs1_api('/psm/external/budget');
    if ($budgetRes['success']) {
        $bData = $budgetRes['data']['data'] ?? $budgetRes['data'] ?? [];
        if (is_array($bData) && !empty($bData)) {
            // Could be single budget or array
            $budgetItem = isset($bData['bgt_allocated']) ? $bData : ($bData[0] ?? []);
            $stats['procurement']['budget_total']     = (float) ($budgetItem['bgt_allocated'] ?? $budgetItem['total_amount'] ?? 0);
            $stats['procurement']['budget_spent']     = (float) ($budgetItem['bgt_spent'] ?? $budgetItem['spent'] ?? 0);
            $stats['procurement']['budget_remaining'] = (float) ($budgetItem['bgt_remaining'] ?? $budgetItem['remaining'] ?? 0);
        }
    }

    // PSM Vendors
    $vendorRes = logs1_api('/psm/vendors', 'GET', [], [], false);
    if ($vendorRes['success']) {
        $vData = $vendorRes['data']['data'] ?? $vendorRes['data'] ?? [];
        $stats['procurement']['vendors'] = is_array($vData) ? count($vData) : 0;
    }

    // SWS Inventory Requests
    $swsRes = logs1_api('/sws/external/room-requests');
    if ($swsRes['success']) {
        $swsData = $swsRes['data']['data'] ?? $swsRes['data'] ?? [];
        $stats['warehouse']['requests'] = is_array($swsData) ? count($swsData) : 0;
    }

    // DTLR Documents
    $dtlrRes = logs1_api('/dtlr/document-tracker/external');
    if ($dtlrRes['success']) {
        $dtlrData = $dtlrRes['data']['data'] ?? $dtlrRes['data'] ?? [];
        $stats['documents']['total'] = is_array($dtlrData) ? count($dtlrData) : 0;
    }

    // ALMS Assets
    $almsRes = logs1_api('/alms/external/maintenance');
    if ($almsRes['success']) {
        $almsData = $almsRes['data']['data'] ?? $almsRes['data'] ?? [];
        $stats['assets']['total'] = is_array($almsData) ? count($almsData) : 0;
    }

    out([
        'success'   => true,
        'data'      => $stats,
        'timestamp' => date('c'),
        'source'    => 'logs1_aggregate'
    ]);
}

// ═══════════════════════════════════════════════════════════════
//  ENDPOINTS — Self-documenting
// ═══════════════════════════════════════════════════════════════

if ($action === 'endpoints') {
    out([
        'success' => true,
        'bridge'  => 'Logs1 (Logistics) Integration — Admin API Bridge',
        'usage'   => '/api/logs1.php?action=<action>&...params',
        'endpoints' => [
            ['action' => 'health',              'method' => 'GET', 'auth' => false, 'description' => 'Logs1 connectivity check'],
            ['action' => 'psm_purchases',       'method' => 'GET', 'auth' => true,  'description' => 'Purchase requisitions (PSM)', 'params' => 'status, department'],
            ['action' => 'psm_budget',          'method' => 'GET', 'auth' => true,  'description' => 'Current procurement budget (PSM)'],
            ['action' => 'psm_budget_logs',     'method' => 'GET', 'auth' => true,  'description' => 'Budget activity logs (PSM)'],
            ['action' => 'psm_budget_requests', 'method' => 'GET', 'auth' => true,  'description' => 'Department budget requests (PSM)'],
            ['action' => 'psm_vendors',         'method' => 'GET', 'auth' => true,  'description' => 'Vendor directory (PSM)', 'params' => 'search'],
            ['action' => 'psm_products',        'method' => 'GET', 'auth' => true,  'description' => 'Product catalog (PSM)'],
            ['action' => 'sws_inventory',       'method' => 'GET', 'auth' => true,  'description' => 'Inventory / room requests (SWS)'],
            ['action' => 'sws_warehouses',      'method' => 'GET', 'auth' => true,  'description' => 'Warehouse list (SWS)'],
            ['action' => 'plt_projects',        'method' => 'GET', 'auth' => true,  'description' => 'Project list (PLT)', 'params' => 'status'],
            ['action' => 'dtlr_documents',      'method' => 'GET', 'auth' => true,  'description' => 'Document tracker (DTLR)', 'params' => 'search'],
            ['action' => 'alms_assets',         'method' => 'GET', 'auth' => true,  'description' => 'Asset maintenance records (ALMS)'],
            ['action' => 'dashboard',           'method' => 'GET', 'auth' => true,  'description' => 'Aggregate Logs1 overview stats'],
            ['action' => 'endpoints',           'method' => 'GET', 'auth' => false, 'description' => 'This endpoint list'],
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
