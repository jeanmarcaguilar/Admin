<?php
/**
 * ════════════════════════════════════════════════════════════════════
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * Finance Integration Configuration
 * ════════════════════════════════════════════════════════════════════
 *
 * Defines connection settings for the Finance (Plain PHP) API endpoints.
 * Finance is the source-of-truth for Budget Proposals, Disbursement
 * Requests, and Financial Approval Workflows.
 *
 * Finance API Base:
 *   Domain:  https://finance.microfinancial-1.com/api
 *   Local:   http://localhost/finance/api
 *
 * Finance uses plain PHP REST endpoints returning JSON.
 * Most endpoints are open-CORS with no auth; some may require session.
 *
 * Finance Database:
 *   fina_finance — Single DB with tables:
 *     budget_proposals        — Department budget submissions (title, fiscal_year, amount, status)
 *     disbursement_requests   — Payment requests (department, amount, status, external_reference)
 *     admin_received_proposals— Proposals forwarded from other systems
 *     users                   — Finance system users (username, name, email, role)
 *     departments             — Department master list
 *     business_contacts       — External payee directory
 *     pending_disbursements   — Awaiting admin release
 *
 * Available API Endpoints:
 *   GET /api/budget_api.php            — All budget proposals with department info
 *   POST /api/budget_api.php           — Create budget proposal
 *   GET /api/disbursement_api.php      — All disbursement requests
 *   POST /api/disbursement_api.php     — Create disbursement request
 *   GET /api/get_public_proposals.php  — Public proposals with approval flags
 *   GET /api/manage_proposals.php      — Admin-received proposals
 *   POST /api/approve_disbursement.php — Release a pending disbursement
 *   GET /api/users_api.php             — All finance users
 */

// ─── Finance API Base URLs ───
define('FINANCE_API_DOMAIN', 'https://finance.microfinancial-1.com/api');
define('FINANCE_API_LOCAL',  'http://localhost/finance/api');

// Timeout in seconds for Finance API calls
define('FINANCE_API_TIMEOUT', 15);

// ─── Feature flags ───
define('FINANCE_ENABLE_BUDGETS',        true);  // Budget proposals
define('FINANCE_ENABLE_DISBURSEMENTS',  true);  // Disbursement requests
define('FINANCE_ENABLE_PROPOSALS',      true);  // Admin-received proposals
define('FINANCE_ENABLE_USERS',          true);  // Finance user directory
define('FINANCE_ENABLE_APPROVALS',      true);  // Disbursement approvals (write)

/**
 * Make an HTTP request to the Finance API.
 * Tries the domain URL first, falls back to localhost.
 *
 * @param string $endpoint  API path (e.g. "/budget_api.php")
 * @param string $method    HTTP method
 * @param array  $data      POST/PUT body (JSON)
 * @param array  $query     Query string params
 * @return array            ['success' => bool, 'status' => int, 'data' => mixed]
 */
function finance_api(string $endpoint, string $method = 'GET', array $data = [], array $query = []): array {
    $endpoint = '/' . ltrim($endpoint, '/');

    $urls = [FINANCE_API_DOMAIN . $endpoint, FINANCE_API_LOCAL . $endpoint];

    foreach ($urls as $baseUrl) {
        $result = finance_http_request($baseUrl, $method, $data, $query);
        if ($result['success'] || $result['status'] !== 0) {
            return $result;
        }
    }

    return [
        'success' => false,
        'status'  => 0,
        'data'    => null,
        'error'   => 'Finance API unreachable on both domain and local URLs'
    ];
}

/**
 * Low-level cURL request to Finance.
 */
function finance_http_request(string $url, string $method, array $data, array $query): array {
    if (!empty($query)) {
        $url .= (strpos($url, '?') !== false ? '&' : '?') . http_build_query($query);
    }

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => FINANCE_API_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => 7,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Accept: application/json',
        ],
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
