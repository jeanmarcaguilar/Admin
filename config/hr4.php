<?php
/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * HR4 Integration Configuration
 * ════════════════════════════════════════════════════════════════════
 *
 * Defines connection settings for the HR4 (Laravel) API endpoints.
 * HR4 is the source-of-truth for Employee Master Data, Payroll,
 * Compensation, HMO/Benefits, and Job/Position management.
 *
 * HR4 API Base:
 *   Domain:  https://hr4.microfinancial-1.com
 *   Local:   http://localhost:8001  (php artisan serve --port=8001)
 *
 * HR4 uses api_key authentication for public endpoints:
 *   ?api_key=<HR4_API_KEY>
 *
 * Available endpoint groups (JSON-returning):
 *   GET /allemployees?api_key=...       — All employees with full relations
 *   GET /vacant-positions               — All vacant positions with job info
 *   GET /api/employees/job              — All positions with job titles
 *   GET /GetAllPayslip?api_key=...      — All paid payslips
 *   GET /api/payroll/disbursement/latest — Latest disbursement batch
 *   POST /api/payroll/disbursement/callback — Finance callback (write-back)
 *
 * HR4 Databases (5):
 *   hr4_auth_db              — Users, sessions, OTP, login history
 *   hr4_hcm_services         — Employees, jobs, positions, contracts, govt IDs
 *   hr4_payroll_services     — Pay periods, payroll results, disbursement
 *   hr4_compensation_services— Allowances, deductions, employee pivots
 *   hr4_hmo_service           — Plans, packages, enrollment, dependents
 */

// ─── HR4 API Base URLs ───
// The system auto-detects: if the domain URL fails, it falls back to localhost.

define('HR4_API_DOMAIN', 'https://hr4.microfinancial-1.com');
define('HR4_API_LOCAL',  'http://localhost:8001');

// API Key for authenticated endpoints (api.key.param middleware)
define('HR4_API_KEY', 'b24e8778f104db434adedd4342e94d39cee6d0668ec595dc6f02c739c522b57a');

// Timeout in seconds for HR4 API calls
define('HR4_API_TIMEOUT', 20);

// ─── Feature flags (enable/disable specific integrations) ───
define('HR4_ENABLE_EMPLOYEES',    true);   // Employee directory (HCM)
define('HR4_ENABLE_POSITIONS',    true);   // Job titles & positions
define('HR4_ENABLE_CONTRACTS',    true);   // Employment contracts
define('HR4_ENABLE_GOVT_IDS',     true);   // Government IDs (SSS, TIN, etc.)
define('HR4_ENABLE_PAYROLL',      true);   // Payslips & pay period data
define('HR4_ENABLE_DISBURSEMENT', true);   // Payroll disbursement batches
define('HR4_ENABLE_COMPENSATION', true);   // Allowances & deductions
define('HR4_ENABLE_HMO',         true);    // HMO / Benefits enrollment
define('HR4_ENABLE_LIFECYCLE',    true);    // Terminations, status history

/**
 * Make an HTTP request to the HR4 API.
 * Tries the domain URL first, falls back to localhost.
 *
 * @param string $endpoint  API path (e.g. "/allemployees" or "/api/employees/job")
 * @param string $method    HTTP method (GET, POST, PUT, DELETE)
 * @param array  $data      POST/PUT body data (JSON-encoded)
 * @param array  $query     Query string parameters
 * @param bool   $withKey   Whether to append the api_key query param (default: true)
 * @return array            ['success' => bool, 'status' => int, 'data' => mixed]
 */
function hr4_api(string $endpoint, string $method = 'GET', array $data = [], array $query = [], bool $withKey = true): array {
    $endpoint = '/' . ltrim($endpoint, '/');

    // Append API key for authenticated routes
    if ($withKey) {
        $query['api_key'] = HR4_API_KEY;
    }

    // Try domain first, then local
    $urls = [HR4_API_DOMAIN . $endpoint, HR4_API_LOCAL . $endpoint];

    foreach ($urls as $baseUrl) {
        $result = hr4_http_request($baseUrl, $method, $data, $query);
        if ($result['success'] || $result['status'] !== 0) {
            return $result;
        }
        // status 0 = connection failed, try next URL
    }

    return [
        'success' => false,
        'status'  => 0,
        'data'    => null,
        'error'   => 'HR4 API unreachable on both domain and local URLs'
    ];
}

/**
 * Low-level cURL request to HR4.
 *
 * @param string $url     Full URL
 * @param string $method  HTTP method
 * @param array  $data    Body data (for POST/PUT)
 * @param array  $query   Query params (appended to URL)
 * @return array
 */
function hr4_http_request(string $url, string $method, array $data, array $query): array {
    if (!empty($query)) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($query);
    }

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => HR4_API_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => 7,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
        CURLOPT_SSL_VERIFYPEER => false, // same-server; safe for internal calls
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
