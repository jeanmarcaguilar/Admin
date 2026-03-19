<?php
/**
 * Front Controller for production hosting.
 * Document root: /home/admin.microfinancial-1.com/public_html/public
 * App files live in: /home/admin.microfinancial-1.com/public_html/
 */

$parentDir = dirname(__DIR__);

// Get the request path from REQUEST_URI (works on both Apache & OpenLiteSpeed)
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$route = ltrim($uri, '/');

// Strip query string artifacts
$route = strtok($route, '?');
if ($route === false) $route = '';

// Redirect old QR code URLs: /Admin/public/verify.php → /verify.php (keeps query params)
if (preg_match('#^Admin/public/(.+)$#i', $route, $m)) {
    $query = $_SERVER['QUERY_STRING'] ?? '';
    header('Location: /' . $m[1] . ($query ? '?' . $query : ''), true, 301);
    exit;
}

// Default: login page
if ($route === '' || $route === '/' || $route === 'index.php') {
    $route = 'login.php';
}

// Security: block directory traversal and null bytes
if (strpos($route, '..') !== false || strpos($route, "\0") !== false) {
    http_response_code(403);
    exit('Forbidden');
}

// Block direct access to sensitive directories
if (preg_match('#^(config|database|vendor)(/|$)#i', $route)) {
    http_response_code(403);
    exit('Forbidden');
}

// Build target path in the parent directory
$targetFile = realpath($parentDir . '/' . $route);

// Verify the resolved path is within the parent directory (prevent symlink escapes)
if ($targetFile === false || strpos($targetFile, realpath($parentDir)) !== 0) {
    http_response_code(404);
    echo '404 - Not Found';
    exit;
}

if (is_file($targetFile)) {
    $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Static asset MIME types
    $mimeTypes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'svg'   => 'image/svg+xml',
        'ico'   => 'image/x-icon',
        'webp'  => 'image/webp',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'json'  => 'application/json',
        'pdf'   => 'application/pdf',
        'mp4'   => 'video/mp4',
        'webm'  => 'video/webm',
        'tsv'   => 'text/tab-separated-values',
    ];

    // Serve static assets directly
    if (isset($mimeTypes[$ext])) {
        header('Content-Type: ' . $mimeTypes[$ext]);
        header('Content-Length: ' . filesize($targetFile));
        readfile($targetFile);
        exit;
    }

    // Execute PHP files in their own directory context
    if ($ext === 'php') {
        chdir(dirname($targetFile));
        include $targetFile;
        exit;
    }

    // Fallback: serve as binary download
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($targetFile));
    readfile($targetFile);
    exit;
}

// Nothing matched
http_response_code(404);
echo '404 - Not Found';
