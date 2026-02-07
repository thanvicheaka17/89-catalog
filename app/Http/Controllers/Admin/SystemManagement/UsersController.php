<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\UserDevice;
use App\Helpers\UserAgentParser;

class UsersController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::query();
        $currentUser = auth()->user();

        // Hide system users from non-system users
        if (!$currentUser->isSystem()) {
            $query->where('role', '!=', User::ROLE_SYSTEM);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status') === 'active');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Per page (default 25)
        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('system-management.users.index', compact('users', 'perPage'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = User::getAssignableRoles();
        return view('system-management.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'avatar' => ['nullable', 'file', 'mimes:jpeg,png,gif,webp,svg', 'max:2048'], // 2MB max
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(array_keys(User::getAssignableRoles()))],
            'status' => ['required', 'boolean'],
            'country_code' => ['nullable', 'string', 'max:10', 'regex:/^\+[0-9]+$/'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:1000', 'regex:/^[\w\s\.,#:\-\+\(\)\/\'"&áéíóúüñàèìòùâêîôûäëïöüçÁÉÍÓÚÜÑÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÇ]+$/u'],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ] + [
            'location.regex' => 'The location contains invalid characters. Only letters, numbers, spaces, common address symbols, and international characters are allowed.',
            'birth_date.before' => 'The birth date must be before today.',
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            // 'country_code' => $validated['country_code'] ?? null,
            // 'phone_number' => $validated['phone_number'],
            // 'location' => $validated['location'],
            // 'birth_date' => $validated['birth_date'],
        ];

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/avatars'), $filename);
            $userData['avatar'] = 'images/avatars/' . $filename;
        }

        User::create($userData);

        return redirect()->route('system-management.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $currentUser = auth()->user();
        $devices = UserDevice::where('user_id', $user->id)->orderBy('last_active_at', 'desc')->get();
        // user cannot view admin user
        if (!$currentUser->hasAdminAccess() && $user->hasAdminAccess()) {
            abort(403, 'You do not have permission to view this user.');
        }

        // user cannot view system user
        if (!$currentUser->isSystem() && $user->isSystem()) {
            abort(403, 'You do not have permission to view this user.');
        }

        // Parse user agent for each device
        $parsedUserAgents = [];
        foreach ($devices as $device) {
            if ($device->user_agent) {
                $parsedUserAgents[] = UserAgentParser::parse($device->user_agent)->toArray();
            } else {
                // Provide default values for devices without user agent
                $parsedUserAgents[] = [
                    'device' => $device->device_name ?? 'Unknown Device',
                    'browser' => 'Unknown',
                    'browser_version' => '',
                    'os' => 'Unknown',
                    'os_version' => '',
                ];
            }
        }

        return view('system-management.users.show', compact('user', 'devices', 'parsedUserAgents'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $currentUser = auth()->user();

        // user cannot edit admin user
        if (!$currentUser->hasAdminAccess() && $user->hasAdminAccess()) {
            abort(403, 'You do not have permission to edit this user.');
        }

        // user cannot edit system user
        if (!$currentUser->isSystem() && $user->isSystem()) {
            abort(403, 'You do not have permission to edit this user.');
        }

        $roles = User::getAssignableRoles();
        $canChangeRole = $user->canChangeRole();
        $canChangeStatus = $user->canChangeStatus($currentUser);

        return view('system-management.users.edit', compact('user', 'roles', 'canChangeRole', 'canChangeStatus'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Non-admin users cannot update admin/system users
        $currentUser = auth()->user();
        if (!$currentUser->hasAdminAccess() && $user->hasAdminAccess()) {
            abort(403, 'You do not have permission to update this user.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'avatar' => ['nullable', 'file', 'mimes:jpeg,png,gif,webp,svg', 'max:2048'], // 2MB max
            'country_code' => ['nullable', 'string', 'max:10', 'regex:/^\+[0-9]+$/'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'location' => ['nullable', 'string', 'max:1000', 'regex:/^[\w\s\.,#:\-\+\(\)\/\'"&áéíóúüñàèìòùâêîôûäëïöüçÁÉÍÓÚÜÑÀÈÌÒÙÂÊÎÔÛÄËÏÖÜÇ]+$/u'],
            'birth_date' => ['nullable', 'date', 'before:today'],
        ] + [
            'location.regex' => 'The location contains invalid characters. Only letters, numbers, spaces, common address symbols, and international characters are allowed.',
            'birth_date.before' => 'The birth date must be before today.',
        ];

        // Only validate role if user's role can be changed
        if ($user->canChangeRole()) {
            $rules['role'] = ['required', Rule::in(array_keys(User::getAssignableRoles()))];
        }

        // Only validate status if user's status can be changed
        if ($user->canChangeStatus(auth()->user())) {
            $rules['status'] = ['required', 'boolean'];
        }

        // Password is optional on update
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            // 'country_code' => $validated['country_code'] ?? null,
            // 'phone_number' => $validated['phone_number'],
            // 'location' => $validated['location'],
            // 'birth_date' => $validated['birth_date'],
        ];

        // Handle avatar removal
        if ($request->input('remove_avatar') === '1') {
            if ($user->avatar) {
                $this->deleteAvatarFile($user->avatar);
            }
            $updateData['avatar'] = null;
        }
        // Handle new avatar upload
        elseif ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                $this->deleteAvatarFile($user->avatar);
            }
            $avatar = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/avatars'), $filename);
            $updateData['avatar'] = 'images/avatars/' . $filename;
        }

        // Only update role if allowed
        if ($user->canChangeRole() && isset($validated['role'])) {
            $updateData['role'] = $validated['role'];
        }

        // Only update status if allowed
        if ($user->canChangeStatus(auth()->user()) && isset($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        // Update password if provided
        if ($request->filled('password')) {
            $updateData['password'] = $validated['password'];
        }

        $user->update($updateData);

        return redirect()->route('system-management.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Non-admin users cannot delete admin/system users
        $currentUser = auth()->user();
        if (!$currentUser->hasAdminAccess() && $user->hasAdminAccess()) {
            abort(403, 'You do not have permission to delete this user.');
        }

        // Protect system users from deletion
        if (!$user->canBeDeleted()) {
            return redirect()->route('system-management.users.index')->with('error', 'System users cannot be deleted.');
        }

        // Prevent self-deletion
        if ($user->id === $currentUser->id) {
            return redirect()->route('system-management.users.index')->with('error', 'You cannot delete your own account.');
        }

        // Delete avatar file if exists
        if ($user->avatar) {
            $this->deleteAvatarFile($user->avatar);
        }

        // Delete all related records first to maintain data integrity
        $user->userGamePlays()->delete();
        $user->userAchievements()->delete();
        $user->userFriends()->delete();
        $user->userDevices()->delete();

        $user->delete();

        return redirect()->route('system-management.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Delete an avatar file from the public path.
     */
    private function deleteAvatarFile(string $avatarUrl): void
    {
        // Extract filename from URL
        $path = parse_url($avatarUrl, PHP_URL_PATH);
        $fullPath = public_path($path);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
