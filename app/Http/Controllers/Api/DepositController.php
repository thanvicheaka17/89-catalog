<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DepositController extends Controller
{
    /**
     * Add funds/deposit to user account
     * This will update both account_balance and total_accumulated_funds (for level system)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'transaction_id' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'update_account_balance' => 'nullable|boolean', // Whether to also update account_balance
        ], [
            'amount.required' => 'The deposit amount is required.',
            'amount.numeric' => 'The deposit amount must be a number.',
            'amount.min' => 'The deposit amount must be at least 0.01.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = auth('api')->user();
        $amount = (float) $request->amount;
        $updateAccountBalance = $request->boolean('update_account_balance', true);
        $previousLevel = $user->current_level ?? 1;

        DB::beginTransaction();
        try {
            // Add to total accumulated funds (for level system)
            $user->addAccumulatedFunds($amount);

            // Optionally update account balance
            if ($updateAccountBalance) {
                $user->account_balance = ($user->account_balance ?? 0) + $amount;
            }

            $user->save();

            DB::commit();

            // Refresh user to get updated level info
            $user->refresh();
            $levelInfo = $user->getLevelInfo();

            return response()->json([
                'success' => true,
                'message' => 'Deposit added successfully!',
                'data' => [
                    'amount' => $amount,
                    'transaction_id' => $request->transaction_id,
                    'description' => $request->description,
                    'account_balance' => $user->account_balance,
                    'total_accumulated_funds' => $user->total_accumulated_funds,
                ],
                'level_info' => [
                    'level' => $levelInfo['level'],
                    'tier' => $levelInfo['tier'],
                    'tier_description' => $levelInfo['tier_description'],
                    'progress_percentage' => $levelInfo['progress_percentage'],
                    'funds_needed_for_next_level' => $levelInfo['funds_needed_for_next_level'],
                    'leveled_up' => $levelInfo['level'] > $previousLevel,
                    'previous_level' => $previousLevel,
                    'new_level' => $levelInfo['level'],
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process deposit.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get deposit history (if you have a deposits table)
     * For now, this is a placeholder
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();

        // This would query a deposits table if it exists
        // For now, return user's current fund information
        $levelInfo = $user->getLevelInfo();

        return response()->json([
            'success' => true,
            'data' => [
                'total_accumulated_funds' => $user->total_accumulated_funds ?? 0,
                'account_balance' => $user->account_balance ?? 0,
                'current_level' => $user->current_level ?? 1,
                'tier' => $user->tier ?? 'Bronze',
                'level_info' => $levelInfo,
            ],
        ]);
    }
}
