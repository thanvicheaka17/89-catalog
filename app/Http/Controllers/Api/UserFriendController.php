<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\UserFriend;
use App\Http\Controllers\Controller;
use App\Models\User;
class UserFriendController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();
        $friends = UserFriend::where('user_id', $user->id)->paginate($request->input('per_page', 25));

        $data = $friends->map(function ($friend) {
            return [
                'id' => $friend->id,
                'user_id' => $friend->user_id,
                'friend_id' => $friend->friend_id,
                'status' => $friend->status,
                'accepted_at' => $friend->accepted_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $friends->total(),
            'current_page' => $friends->currentPage(),
            'last_page' => $friends->lastPage(),
            'per_page' => $friends->perPage(),
        ], 200);
    }

    public function store(Request $request)
    {
        $user = auth('api')->user();

        $friend = User::find($request->friend_id);

        if (!$friend) {
            return response()->json([
                'success' => false,
                'message' => 'Friend not found',
            ], 404);
        }

        if ($user->id == $friend->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot add yourself as a friend',
            ], 400);
        }

        $friend = UserFriend::where('user_id', $user->id)->where('friend_id', $friend->id)->first();
        if ($friend) {
            return response()->json([
                'success' => false,
                'message' => 'You are already friends with this user',
            ], 400);
        }

        $friend = UserFriend::create([
            'user_id' => $user->id,
            'friend_id' => $friend->id,
            'status' => 'pending',
            'accepted_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'data' => $friend,
        ], 201);
    }

    public function accept(Request $request)
    {
        $user = auth('api')->user();
        $friend = UserFriend::where('user_id', $user->id)->where('friend_id', $request->friend_id)->first();
        $friend->status = 'accepted';
        $friend->accepted_at = now();
        $friend->save();

        return response()->json([
            'success' => true,
            'data' => $friend,
        ], 200);
    }

    public function reject(Request $request)
    {
        $user = auth('api')->user();
        $friend = UserFriend::where('user_id', $user->id)->where('friend_id', $request->friend_id)->first();
        $friend->status = 'rejected';
        $friend->rejected_at = now();
        $friend->save();

        return response()->json([
            'success' => true,
            'data' => $friend,
        ], 200);
    }
}
