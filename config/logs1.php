<?php
/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * Logs1 (Logistics) Integration Configuration
 * ════════════════════════════════════════════════════════════════════
 *
 * Defines connection settings for the Logs1 (Laravel) API endpoints.
 * Logs1 is the source-of-truth for Procurement, Warehousing,
 * Project Logistics, Document Tracking, and Asset Management.
 *
 * Logs1 API Base:
 *   Domain:  https://logs1.microfinancial-1.com/api/v1
 *   Local:   http://localhost:8002/api/v1  (php artisan serve --port=8002)
 *
 * Authentication:
 *   External endpoints use API-Key: X-API-KEY header or ?key= query param
 *   Internal endpoints require JWT (not used by Admin — we use external routes)
 *
 * Logs1 Modules (5):
 *   PSM  — Procurement & Supplier Management (purchases, budgets, vendors)
 *   SWS  — Smart Warehousing System (items, stock, warehouses, categories)
 *   PLT  — Project Logistics Tracker (projects, milestones, dispatches)
 *   DTLR — Document Tracking & Logistics Record (documents, audit trail)
 *   ALMS — Asset Lifecycle & Maintenance System (assets, maintenance)
 *
 * Logs1 Databases (4):
 *   psm  — Procurement tables (purchases, budgets, vendors, products)
 *   sws  — Warehousing tables (items, transactions, warehouses, categories)
 *   plt  — Project tables (projects, milestones, resources, dispatches)
 *   dtlr — Document tables (documents, logistics_records)
 */

// ─── Logs1 API Base URLs ───
define('LOGS1_API_DOMAIN', 'https://logs1.microfinancial-1.com/api/v1');
define('LOGS1_API_LOCAL',  'http://localhost:8002/api/v1');

// API Key for external-access endpoints (CheckApiKey middleware)
define('LOGS1_API_KEY', '63cfb7730dcc34299fa38cb1a620f701');

// Timeout in seconds for Logs1 API calls
define('LOGS1_API_TIMEOUT', 20);

// ─── Feature flags (enable/disable specific data pulls) ───
define('LOGS1_ENABLE_PSM_PURCHASES',  true);  // Purchase orders
define('LOGS1_ENABLE_PSM_BUDGETS',    true);  // Procurement budgets
define('LOGS1_ENABLE_PSM_VENDORS',    true);  // Vendor directory
define('LOGS1_ENABLE_PSM_PRODUCTS',   true);  // Product catalog
define('LOGS1_ENABLE_PSM_REQUISITIONS', true); // Requisitions
define('LOGS1_ENABLE_SWS_INVENTORY',  true);  // Inventory & stock levels
define('LOGS1_ENABLE_SWS_WAREHOUSES', true);  // Warehouse utilization
define('LOGS1_ENABLE_PLT_PROJECTS',   true);  // Project tracking
define('LOGS1_ENABLE_DTLR_DOCUMENTS', true);  // Document tracking
define('LOGS1_ENABLE_ALMS_ASSETS',    true);  // Asset tracking

/**
 * Make an HTTP request to the Logs1 API.
 * Tries the domain URL first, falls back to localhost.
 *
 * @param string $endpoint  API path (e.g. "/psm/external/budget")
 * @param string $method    HTTP method (GET, POST, PUT, DELETE)
 * @param array  $data      POST/PUT body data (JSON-encoded)
 * @param array  $query     Query string parameters
 * @param bool   $withKey   Append API key (default: true)
 * @return array            ['success' => bool, 'status' => int, 'data' => mixed]
 */
function logs1_api(string $endpoint, string $method = 'GET', array $data = [], array $query = [], bool $withKey = true): array {
    $endpoint = '/' . ltrim($endpoint, '/');

    if ($withKey) {
        $query['key'] = LOGS1_API_KEY;
    }

    // Try domain first, then local
    $urls = [LOGS1_API_DOMAIN . $endpoint, LOGS1_API_LOCAL . $endpoint];

    foreach ($urls as $baseUrl) {
        $result = logs1_http_request($baseUrl, $method, $data, $query, $withKey);
        if ($result['success'] || $result['status'] !== 0) {
            return $result;
        }
    }

    return [
        'success' => false,
        'status'  => 0,
        'data'    => null,
        'error'   => 'Logs1 API unreachable on both domain and local URLs'
    ];
}

/**
 * Low-level cURL request to Logs1.
 */
function logs1_http_request(string $url, string $method, array $data, array $query, bool $withKey): array {
    if (!empty($query)) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($query);
    }

    $ch = curl_init();

    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    // Also send API key in header for external routes
    if ($withKey) {
        $headers[] = 'X-API-KEY: ' . LOGS1_API_KEY;
    }

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => LOGS1_API_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => 7,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
    ]);

    switch (strtoupper($method)) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'PUT':
        case 'PATCH':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
    }

    $response   = curl_exec($ch);
    $httpCode   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError  = curl_error($ch);
    curl_close($ch);

    if ($response === false || $httpCode === 0) {
        return [
            'success' => false,
            'status'  => 0,
            'data'    => null,
            'error'   => $curlError ?: 'Connection failed'
        ];
    }

    $decoded = json_decode($response, true);

    return [
        'success' => ($httpCode >= 200 && $httpCode < 300),
        'status'  => $httpCode,
        'data'    => $decoded ?? $response,
        'error'   => ($httpCode >= 400) ? ($decoded['message'] ?? 'HTTP ' . $httpCode) : null
    ];
}
