<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     *
     * Check if the authenticated user is still active.
     * If user is inactive, invalidate their token and return 401.
     * 
     * IMPORTANT: We fetch the user fresh from the database to ensure
     * we're checking the current status, not the cached status from the JWT token.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticatedUser = auth('api')->user();

        if (!$authenticatedUser) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Fetch user fresh from database to check current status
        // This ensures we check the latest status even if admin changed it
        $user = User::find($authenticatedUser->id);

        if (!$user) {
            // User was deleted, invalidate and blacklist token
            try {
                $token = JWTAuth::getToken();
                if ($token) {
                    JWTAuth::setToken($token)->invalidate(true);
                }
            } catch (\Exception $e) {
                // Token might already be invalid, continue
            }

            return response()->json([
                'success' => false,
                'message' => 'User account not found.',
            ], 401);
        }

        if ($user->isInactive()) {
            // Invalidate and blacklist the token immediately
            // This ensures the token cannot be used again for any future requests
            try {
                $token = JWTAuth::getToken();
                if ($token) {
                    JWTAuth::setToken($token)->invalidate(true);
                }
            } catch (\Exception $e) {
                // Token might already be invalid, continue
            }

            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact administrator.',
            ], 403);
        }

        return $next($request);
    }
}

