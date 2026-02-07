<?php

// Vercel serverless function entry point for Laravel
// This file routes all requests to Laravel's public/index.php

// Get the original request path from Vercel
// When Vercel routes /api/promo to this function via vercel.json routing,
// we need to preserve the full path including /api prefix

// Method 1: Check Vercel's original path header (most reliable)
if (isset($_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'])) {
    $originalPath = $_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'];
} 
// Method 2: Check if PATH_INFO contains the full path
elseif (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
    $originalPath = $_SERVER['PATH_INFO'];
    // If PATH_INFO doesn't start with /api, it might have been stripped
    // Check if we can reconstruct it from other server variables
    if (substr($originalPath, 0, 4) !== '/api' && isset($_SERVER['REQUEST_URI'])) {
        // Try to get full path from REQUEST_URI
        $requestUri = $_SERVER['REQUEST_URI'];
        $queryPos = strpos($requestUri, '?');
        $uriPath = $queryPos !== false ? substr($requestUri, 0, $queryPos) : $requestUri;
        if (substr($uriPath, 0, 4) === '/api') {
            $originalPath = $uriPath;
        }
    }
}
// Method 3: Extract from REQUEST_URI
elseif (isset($_SERVER['REQUEST_URI'])) {
    $requestUri = $_SERVER['REQUEST_URI'];
    $queryPos = strpos($requestUri, '?');
    $originalPath = $queryPos !== false ? substr($requestUri, 0, $queryPos) : $requestUri;
}
// Method 4: Fallback
else {
    $originalPath = '/';
}

// Normalize the path (ensure it starts with /)
if (substr($originalPath, 0, 1) !== '/') {
    $originalPath = '/' . $originalPath;
}

// CRITICAL: Ensure the path includes /api prefix for Laravel API routes
// Laravel 11 automatically prefixes API routes with /api, so we need the full path
// If the path doesn't start with /api but should be an API route, add it
// However, we should preserve what Vercel gives us, so only do this if we're sure
// Actually, let's trust what Vercel gives us - if it's /api/promo, keep it as is

// Set REQUEST_URI with query string if present
$_SERVER['REQUEST_URI'] = $originalPath;
if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
    $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
}

// Set PATH_INFO (Laravel uses this for routing)
$_SERVER['PATH_INFO'] = $originalPath;

// Ensure REQUEST_METHOD is set
if (!isset($_SERVER['REQUEST_METHOD']) || empty($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? 'GET';
}

// Set SCRIPT_NAME for Laravel (important for route matching)
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Ensure PHP_SELF is set
if (!isset($_SERVER['PHP_SELF'])) {
    $_SERVER['PHP_SELF'] = '/index.php';
}

require __DIR__ . '/../public/index.php';
