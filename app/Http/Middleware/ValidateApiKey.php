<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * Validates the x-api-key header against administrator api_tokens.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('x-api-key');

        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => 'API key is required. Please provide x-api-key header.',
            ], 401);
        }

        // Check if the API key exists and belongs to an active admin user
        $admin = User::where('api_token', $apiKey)
            ->where(function ($query) {
                $query->where('role', User::ROLE_SYSTEM)
                      ->orWhere('role', User::ROLE_ADMIN);
            })
            ->first();

        if (!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.',
            ], 401);
        }

        // Check if admin is active (if status field exists and is used)
        if ($admin->status === false) {
            return response()->json([
                'success' => false,
                'message' => 'API key owner account is deactivated.',
            ], 403);
        }

        // Store the admin who owns the API key in the request for later use
        $request->attributes->set('api_key_owner', $admin);

        return $next($request);
    }
}

