<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use App\Helpers\IpHelper;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\LevelConfiguration;
use App\Models\User;
use App\Services\IpGeolocationService;
use App\Services\LevelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LevelConfigurationController extends Controller
{
    protected $levelService;

    public function __construct(LevelService $levelService)
    {
        $this->levelService = $levelService;
    }

    /**
     * Display user deposits information.
     */
    public function index(Request $request)
    {
        $userQuery = User::where('role', User::ROLE_USER)
            ->where('status', true)
            ->with(['userAchievements' => function($query) {
                $query->where('achievement_code', 'like', 'TIER_%')
                    ->orderBy('unlocked_at', 'desc');
            }]);

        // Search functionality for users
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $userQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by tier
        if ($request->filled('tier')) {
            $userQuery->where('tier', $request->input('tier'));
        }

        // Filter by level range
        if ($request->filled('min_level')) {
            $userQuery->where('current_level', '>=', $request->input('min_level'));
        }
        if ($request->filled('max_level')) {
            $userQuery->where('current_level', '<=', $request->input('max_level'));
        }

        // Per page (default 50)
        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 50;

        // Sorting functionality
        $sorting = $request->input('sorting', '');
        switch ($sorting) {
            case 'level_asc':
                $userQuery->orderBy('current_level', 'asc');
                break;
            case 'level_desc':
                $userQuery->orderBy('current_level', 'desc');
                break;
            case 'total_funds_asc':
                $userQuery->orderBy('total_accumulated_funds', 'asc');
                break;
            case 'total_funds_desc':
                $userQuery->orderBy('total_accumulated_funds', 'desc');
                break;
            default:
                // Default sorting: level desc, then total funds desc
                $userQuery->orderBy('current_level', 'desc')
                          ->orderBy('total_accumulated_funds', 'desc');
                break;
        }

        $users = $userQuery->paginate($perPage);

        // Get unique user tiers for filter
        $tiers = User::where('role', User::ROLE_USER)
            ->where('status', true)
            ->whereNotNull('tier')
            ->select('tier')
            ->distinct()
            ->orderBy('tier')
            ->get()
            ->map(function ($item) {
                return [
                    'value' => $item->tier,
                    'label' => ucfirst($item->tier),
                ];
            });

        return view('system-management.level-configurations.index', compact(
            'users',
            'perPage',
            'tiers'
        ));
    }

    /**
     * Show the form for creating a new level configuration.
     */
    public function create()
    {
        if (!auth()->user()->hasAdminAccess()) {
            return redirect()->back()->with('error', 'You do not have permission to adjust user funds.');
        }

        // Get active regular users for selection
        $users = User::where('status', true)
            ->where('role', User::ROLE_USER)
            ->select('id', 'name', 'email', 'current_level', 'tier', 'account_balance', 'total_accumulated_funds')
            ->orderBy('name')
            ->get();

        return view('system-management.level-configurations.create', compact('users'));
    }

    /**
     * Store a newly created level configuration.
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasAdminAccess()) {
            return redirect()->back()->with('error', 'You do not have permission to adjust user funds.');
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0.01',
            'update_account_balance' => 'nullable|boolean',
            'update_level' => 'nullable|boolean',
            'reason' => 'nullable|string|max:255',
        ], [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'Selected user does not exist.',
            'amount.required' => 'Amount is required.',
            'amount.numeric' => 'Amount must be a number.',
            'amount.min' => 'Amount must be at least 0.01.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::findOrFail($request->user_id);
        $amount = (float) $request->amount;
        $previousLevel = $user->current_level ?? 1;

        // Store old values before update for audit log
        $oldValues = [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'account_balance' => $user->account_balance ?? 0,
            'total_accumulated_funds' => $user->total_accumulated_funds ?? 0,
            'current_level' => $previousLevel,
            'tier' => $user->tier,
        ];

        DB::beginTransaction();
        try {
            // Update level (total accumulated funds)
            $user->addAccumulatedFunds($amount);

            // Update account balance
            $user->account_balance = ($user->account_balance ?? 0) + $amount;

            $user->save();

            // Refresh user to get updated level info
            $user->refresh();
            $levelInfo = $user->getLevelInfo();

            // Create audit log
            $ipAddress = IpHelper::getClientIp($request);
            $geolocationService = app(IpGeolocationService::class);
            $location = $geolocationService->getLocation($ipAddress);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action_type' => AuditLog::ACTION_TYPE_ADD_FOUNDS,
                'old_value' => $oldValues,
                'new_value' => [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'amount_added' => $amount,
                    'account_balance' => $user->account_balance,
                    'total_accumulated_funds' => $user->total_accumulated_funds,
                    'current_level' => $levelInfo['level'],
                    'tier' => $user->tier,
                    'reason' => $request->reason,
                    'location' => $location,
                ],
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
            ]);

            DB::commit();

            return redirect()->route('system-management.level-configurations.index')->with('success',
                "Successfully added " . number_format($amount, 0, '.', ',') . " to user {$user->name} ({$user->email}). " .
                ($levelInfo['level'] > $previousLevel ? "Level increased from {$previousLevel} to {$levelInfo['level']}!" : "")
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to process amount adjustment: ' . $e->getMessage())
                ->withInput();
        }
    }
}
