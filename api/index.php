<?php

// Vercel serverless function entry point for Laravel
// This file routes all requests to Laravel's public/index.php

// When Vercel routes a request through vercel.json to this function,
// we need to ensure the original request path is preserved for Laravel routing

// Get the original path from various Vercel server variables
$path = '/';

// Priority 1: Vercel's original path header (most reliable)
if (isset($_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'])) {
    $path = $_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'];
}
// Priority 2: PATH_INFO (set by Vercel routing)
elseif (isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $path = $_SERVER['PATH_INFO'];
}
// Priority 3: REQUEST_URI (fallback)
elseif (isset($_SERVER['REQUEST_URI'])) {
    $requestUri = $_SERVER['REQUEST_URI'];
    // Remove query string
    $queryPos = strpos($requestUri, '?');
    $path = $queryPos !== false ? substr($requestUri, 0, $queryPos) : $requestUri;
}

// Ensure path starts with /
if (substr($path, 0, 1) !== '/') {
    $path = '/' . $path;
}

// Set server variables that Laravel uses for routing
$_SERVER['REQUEST_URI'] = $path;
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
    $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
}

$_SERVER['PATH_INFO'] = $path;
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Ensure REQUEST_METHOD is set
if (empty($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? 'GET';
}

// Forward to Laravel
require __DIR__ . '/../public/index.php';
