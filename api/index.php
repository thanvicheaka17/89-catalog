<?php

// Vercel serverless function entry point for Laravel
// This file routes all requests to Laravel's public/index.php

// Get the original request path from Vercel
// When Vercel routes /api/promo to this function, we need to extract the path correctly
$originalPath = '/';

// Check various ways Vercel might pass the path
if (isset($_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'])) {
    // Vercel's original path header
    $originalPath = $_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'];
} elseif (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
    // PATH_INFO is set by Vercel
    $originalPath = $_SERVER['PATH_INFO'];
} elseif (isset($_SERVER['REQUEST_URI'])) {
    // Extract path from REQUEST_URI (remove query string)
    $requestUri = $_SERVER['REQUEST_URI'];
    $queryPos = strpos($requestUri, '?');
    $originalPath = $queryPos !== false ? substr($requestUri, 0, $queryPos) : $requestUri;
}

// Normalize the path (ensure it starts with /)
if (substr($originalPath, 0, 1) !== '/') {
    $originalPath = '/' . $originalPath;
}

// Set REQUEST_URI with query string if present
$_SERVER['REQUEST_URI'] = $originalPath;
if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
    $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
}

// Set PATH_INFO (Laravel uses this for routing)
$_SERVER['PATH_INFO'] = $originalPath;

// Ensure REQUEST_METHOD is set
if (!isset($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET';
}

// Set SCRIPT_NAME for Laravel
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Ensure PHP_SELF is set
if (!isset($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = '/index.php';
}

require __DIR__ . '/../public/index.php';
