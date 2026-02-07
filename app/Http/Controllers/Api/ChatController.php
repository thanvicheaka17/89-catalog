<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

class ChatController extends Controller
{
    /**
     * Join the chat room with username validation.
     */
    public function joinChat(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:2|max:20|regex:/^[a-zA-Z0-9_]+$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid username format',
                'errors' => $validator->errors()
            ], 422);
        }

        // Store username in cache for this session
        Cache::put("chat_username_{$user->id}", $request->username, now()->addHours(24));

        return response()->json([
            'success' => true,
            'message' => 'Successfully joined chat'
        ]);
    }

    /**
     * Send a chat message with spam protection.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        // Get username from cache or use user's name
        $username = Cache::get("chat_username_{$user->id}", $user->name ?? $user->username);

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|min:1|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Message validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Rate limiting: max 5 messages per minute
        $key = "chat_messages_{$user->id}";
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Too many messages. Please wait {$seconds} seconds.",
                'spam_warning' => true
            ], 429);
        }

        // Check for spam patterns
        if ($this->isSpamMessage($request->message)) {
            return response()->json([
                'success' => false,
                'message' => 'Message contains spam content',
                'spam_warning' => true
            ], 422);
        }

        try {
            $chatMessage = ChatMessage::create([
                'user_id' => $user->id,
                'username' => $username,
                'message' => trim($request->message),
            ]);

            // Hit the rate limiter
            RateLimiter::hit($key, 60); // 1 minute window

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent chat messages for the message area.
     */
    public function getMessages(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $perPage = $request->input('per_page', 25);
        $messages = ChatMessage::where('user_id', $user->id)->orderBy('created_at', 'desc')->paginate($perPage);

        $data = $messages->map(function ($message) {
            return [
                'id' => $message->id,
                'user_id' => $message->user->id,
                'username' => $message->username,
                'message' => $message->message,
                'created_at' => $message->created_at,
                'updated_at' => $message->updated_at,
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $messages->total(),
            'current_page' => $messages->currentPage(),
            'last_page' => $messages->lastPage(),
            'per_page' => $messages->perPage(),
        ]);
    }

    /**
     * Get active members count (for member counter display).
     */
    public function getActiveMembers(Request $request): JsonResponse
    {
        // Note: This is a simplified version. In production, you'd use Redis
        // to track actual active users in presence channels
        $activeMembers = Cache::remember('active_chat_members', 30, function () {
            // Get users who have sent messages in the last hour
            return ChatMessage::where('created_at', '>=', now()->subHour())
                ->distinct('user_id')
                ->count('user_id');
        });

        return response()->json([
            'success' => true,
            'data' => [
                'active_members' => max($activeMembers, 0), // Ensure non-negative
                'last_updated' => now()->toISOString()
            ]
        ]);
    }

    /**
     * Check if message contains spam patterns.
     */
    private function isSpamMessage(string $message): bool
    {
        $spamPatterns = [
            '/(.)\1{10,}/', // Repeated characters (10+ times)
            '/(?:http|https|www\.)\S+/i', // URLs
            '/\b(?:fuck|shit|damn|bitch|crap)\b/i', // Profanity (basic check)
            '/[A-Z]{10,}/', // All caps (10+ characters)
        ];

        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return true;
            }
        }

        return false;
    }
}
