<?php

// Vercel serverless function entry point for Laravel
// This file routes all requests to Laravel's public/index.php

// When Vercel routes a request to this function via vercel.json,
// the original path should be available in REQUEST_URI or PATH_INFO
// We need to extract it and ensure Laravel receives it correctly

// Try multiple methods to get the original path
$originalPath = null;

// Method 1: REQUEST_URI (most reliable for Vercel)
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] !== '/api/index.php') {
    $requestUri = $_SERVER['REQUEST_URI'];
    // Remove query string
    $queryPos = strpos($requestUri, '?');
    $originalPath = $queryPos !== false ? substr($requestUri, 0, $queryPos) : $requestUri;
    // Remove /api/index.php prefix if present
    $originalPath = preg_replace('#^/api/index\.php#', '', $originalPath);
}

// Method 2: PATH_INFO
if (empty($originalPath) && isset($_SERVER['PATH_INFO']) && $_SERVER['PATH_INFO'] !== '') {
    $originalPath = $_SERVER['PATH_INFO'];
}

// Method 3: Vercel header
if (empty($originalPath) && isset($_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'])) {
    $originalPath = $_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'];
}

// Fallback to root
if (empty($originalPath)) {
    $originalPath = '/';
}

// Ensure it starts with /
if (substr($originalPath, 0, 1) !== '/') {
    $originalPath = '/' . $originalPath;
}

// Set the server variables Laravel needs
$_SERVER['PATH_INFO'] = $originalPath;
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REQUEST_URI'] = $originalPath;

// Preserve query string if present
if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '') {
    $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
}

// Ensure REQUEST_METHOD
if (empty($_SERVER['REQUEST_METHOD'])) {
    $_SERVER['REQUEST_METHOD'] = $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? 'GET';
}

// Ensure HTTP_HOST
if (empty($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
}

// Forward to Laravel
require __DIR__ . '/../public/index.php';
