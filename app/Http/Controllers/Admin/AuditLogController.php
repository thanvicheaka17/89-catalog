<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::query()->with('user');

        // If current user is NOT system role, hide audit logs created by system role users
        $currentUser = Auth::user();
        if (!$currentUser->isSystem()) {
            $query->whereHas('user', function ($q) {
                $q->where('role', '!=', User::ROLE_SYSTEM);
            });
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('action_type', 'like', "%{$search}%")
                    ->orWhere('old_value', 'like', "%{$search}%")
                    ->orWhere('new_value', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $auditLogs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('system-management.audit-logs.index', compact('auditLogs', 'perPage'));
    }

    public function show(AuditLog $auditLog)
    {
        // If current user is NOT system role and the audit log was created by a system role user, deny access
        $currentUser = Auth::user();
        if (!$currentUser->isSystem() && $auditLog->user && $auditLog->user->isSystem()) {
            abort(403, 'You do not have permission to view this audit log.');
        }

        return view('system-management.audit-logs.show', compact('auditLog'));
    }
}
