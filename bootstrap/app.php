<?php

use App\Http\Middleware\CheckUserStatus;
use App\Http\Middleware\TrustProxies;
use App\Http\Middleware\ValidateApiKey;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // ZONA PROMAX HUB Data Updates - Every 30 seconds
        $schedule->command('zona:update-data')
                ->everyThirtySeconds()
                ->withoutOverlapping()
                ->runInBackground();

        // Auto-update RTP games, tools, and hot_and_fresh with random values - Every 30 seconds
        $schedule->command('data:update-random')
                ->everyThirtyMinutes()
                ->withoutOverlapping()
                ->runInBackground();

        // Add new testimonials - Every hour
        $schedule->command('testimonials:add-new')
                ->everyThreeHours()
                ->withoutOverlapping()
                ->runInBackground();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Register TrustProxies middleware first to handle proxy headers
        $middleware->prepend(TrustProxies::class);
        
        $middleware->alias([
            'api.key' => ValidateApiKey::class,
            'check.status' => CheckUserStatus::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle Validation Exception
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Handle Model Not Found Exception
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found',
                ], 404);
            }
        });

        // Handle Route Not Found Exception
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Endpoint not found',
                ], 404);
            }
        });

        // Handle Method Not Allowed Exception
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Method not allowed',
                ], 405);
            }
        });

        // Handle JWT Token Blacklisted (logged out)
        $exceptions->render(function (TokenBlacklistedException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has been invalidated. Please login again.',
                ], 401);
            }
        });

        // Handle JWT Token Expired
        $exceptions->render(function (TokenExpiredException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token has expired. Please login again.',
                ], 401);
            }
        });

        // Handle JWT Token Invalid
        $exceptions->render(function (TokenInvalidException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token.',
                ], 401);
            }
        });

        // Handle general JWT Exception
        $exceptions->render(function (JWTException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token error: ' . $e->getMessage(),
                ], 401);
            }
        });

        // Handle Authentication Exception (no token provided)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }
        });

        // Handle all other exceptions for API routes
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
                
                return response()->json([
                    'success' => false,
                    'message' => $statusCode === 500 ? 'Internal server error' : $e->getMessage(),
                ], $statusCode);
            }
        });
    })->create();
