<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
class NewsletterSubscriberController extends Controller
{
    public function subscribe(Request $request)
    {
        $user = auth('api')->user();

        $validated = $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email',
        ]);

        NewsletterSubscriber::create([
            'email' => $validated['email'],
            'is_active' => true,
            'user_id' => $user ? $user->id : null,
            'unsubscribed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Newsletter subscribed successfully'
        ], 200);
    }
}
