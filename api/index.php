<?php

// Vercel serverless function entry point for Laravel
// This file routes all requests to Laravel's public/index.php

// Fix request URI for Vercel serverless environment
// Vercel may not set REQUEST_URI correctly, so we need to construct it
if (isset($_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'])) {
    // Vercel provides the original path in this header
    $_SERVER['REQUEST_URI'] = $_SERVER['HTTP_X_VERCEL_ORIGINAL_PATH'];
    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
    }
} elseif (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])) {
    // Fallback: construct from available server variables
    $path = $_SERVER['PATH_INFO'] ?? $_SERVER['ORIG_PATH_INFO'] ?? $_SERVER['SCRIPT_NAME'] ?? '/';
    
    // Remove the /api/index.php prefix if present
    $path = preg_replace('#^/api/index\.php#', '', $path);
    if (empty($path)) {
        $path = '/';
    }
    
    $_SERVER['REQUEST_URI'] = $path;
    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
    }
}

// Ensure PATH_INFO is set correctly
if (!isset($_SERVER['PATH_INFO']) || empty($_SERVER['PATH_INFO'])) {
    if (isset($_SERVER['REQUEST_URI'])) {
        $requestUri = $_SERVER['REQUEST_URI'];
        $queryPos = strpos($requestUri, '?');
        $_SERVER['PATH_INFO'] = $queryPos !== false ? substr($requestUri, 0, $queryPos) : $requestUri;
    }
}

require __DIR__ . '/../public/index.php';
