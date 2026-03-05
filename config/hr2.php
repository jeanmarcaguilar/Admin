<?php
/**
 * MICROFINANCIAL MANAGEMENT SYSTEM I: ADMINISTRATIVE
 * HR2 Integration Configuration
 * 
 * Defines connection settings for the HR2 (Laravel) API endpoints.
 * HR2 runs on the same server or domain — both local and production URLs are supported.
 * 
 * HR2 API Base:
 *   Domain:  https://hr2.microfinancial-1.com/api
 *   Local:   http://localhost:8000/api  (php artisan serve)
 * 
 * Available endpoint groups:
 *   /ess/employee/*          — Employee data (proxied from HR4)
 *   /ess/attendance/*        — Attendance logs (proxied from HR3)
 *   /leaves/*                — Leave management
 *   /training-room-bookings/*— Training room bookings
 *   /successors/*            — Succession / promotion data
 *   /assigned-competencies   — Employee competency assignments
 *   /jobs                    — Job titles / positions
 *   /dashboard/realtime      — Real-time HR dashboard stats
 */

// ─── HR2 API Base URLs ───
// The system auto-detects: if the domain URL fails, it falls back to localhost.

define('HR2_API_DOMAIN',  'https://hr2.microfinancial-1.com/api');
define('HR2_API_LOCAL',   'http://localhost:8000/api');

// Timeout in seconds for HR2 API calls
define('HR2_API_TIMEOUT', 15);

// ─── Feature flags (enable/disable specific integrations) ───
define('HR2_ENABLE_EMPLOYEES',    true);
define('HR2_ENABLE_ATTENDANCE',   true);
define('HR2_ENABLE_LEAVES',       true);
define('HR2_ENABLE_TRAINING',     true);
define('HR2_ENABLE_SUCCESSORS',   true);
define('HR2_ENABLE_COMPETENCIES', true);
define('HR2_ENABLE_JOBS',         true);
define('HR2_ENABLE_DASHBOARD',    true);

/**
 * Make an HTTP request to the HR2 API.
 * Tries the domain URL first, falls back to localhost.
 *
 * @param string $endpoint  API path after /api (e.g. "/ess/employee/123")
 * @param string $method    HTTP method (GET, POST, PUT, DELETE)
 * @param array  $data      POST/PUT body data (JSON-encoded)
 * @param array  $query     Query string parameters for GET requests
 * @return array            ['success' => bool, 'status' => int, 'data' => mixed]
 */
function hr2_api(string $endpoint, string $method = 'GET', array $data = [], array $query = []): array {
    $endpoint = '/' . ltrim($endpoint, '/');

    // Try domain first, then local
    $urls = [HR2_API_DOMAIN . $endpoint, HR2_API_LOCAL . $endpoint];

    foreach ($urls as $baseUrl) {
        $result = hr2_http_request($baseUrl, $method, $data, $query);
        if ($result['success'] || $result['status'] !== 0) {
            return $result;
        }
        // status 0 = connection failed, try next URL
    }

    return [
        'success' => false,
        'status'  => 0,
        'data'    => null,
        'error'   => 'HR2 API unreachable on both domain and local URLs'
    ];
}

/**
 * Low-level cURL request to HR2.
 *
 * @param string $url     Full URL
 * @param string $method  HTTP method
 * @param array  $data    Body data (for POST/PUT)
 * @param array  $query   Query params (appended to URL)
 * @return array
 */
function hr2_http_request(string $url, string $method, array $data, array $query): array {
    if (!empty($query)) {
        $url .= '?' . http_build_query($query);
    }

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => HR2_API_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => 5,
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
