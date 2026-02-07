@extends('layouts.app')

@section('title', 'Users')

@section('content')
<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success" data-auto-dismiss="5000">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
        <span class="alert-content">{{ session('success') }}</span>
        <button type="button" class="alert-close" onclick="closeAlert(this)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" data-auto-dismiss="5000">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
        <span class="alert-content">{{ session('error') }}</span>
        <button type="button" class="alert-close" onclick="closeAlert(this)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

<!-- Page Header -->
<div class="page-header">
    <div class="page-header-left">
        <h2>Users</h2>
        <p>Manage all registered users in the system</p>
    </div>
    
    <div class="page-header-right">
        <a href="{{ route('system-management.users.create') }}" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Add
        </a>
    </div>
</div>

<!-- Users Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-header-left">
            <form action="{{ route('system-management.users.index') }}" method="GET" class="per-page-form">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <label for="per_page">Per page</label>
                <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                </select>
            </form>
            <button type="button" class="btn-filter" onclick="toggleFilters()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                </svg>
                <span>Filters</span>
            </button>
        </div>
        <div class="table-header-right">
            <form action="{{ route('system-management.users.index') }}" method="GET" class="table-search-form">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <div class="table-search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>
    
    <!-- Filters Panel -->
    <div class="filters-panel" id="filtersPanel">
        <form action="{{ route('system-management.users.index') }}" method="GET" class="filters-form">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            
            <div class="filters-grid grid-4">
                <div class="filter-group">
                    <label for="filter_role">Role</label>
                    <select name="role" id="filter_role" class="filter-select">
                        <option value="">All Roles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="filter_status">Status</label>
                    <select name="status" id="filter_status" class="filter-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="filter_date_from">Date From</label>
                    <input type="date" name="date_from" id="filter_date_from" class="filter-input" value="{{ request('date_from') }}">
                </div>
                
                <div class="filter-group">
                    <label for="filter_date_to">Date To</label>
                    <input type="date" name="date_to" id="filter_date_to" class="filter-input" value="{{ request('date_to') }}">
                </div>
            </div>
            
            <div class="filters-actions">
                <button type="submit" class="btn btn-info">
                    Apply Filters
                </button>
                <a href="{{ route('system-management.users.index', ['per_page' => request('per_page')]) }}" class="btn btn-secondary">
                    Clear Filters
                </a>
            </div>
        </form>
    </div>
    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="th-number">#</th>
                    <th>User</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                    <tr>
                        <td class="td-number">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="user-cell">
                                <img src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" class="user-avatar-img">
                                <div class="user-info">
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge role-{{ $user->role }}">
                                {{ $user->getRoleDisplayName() }}
                            </span>
                        </td>
                        <td>
                            @if($user->isActive())
                                <span class="badge status-active">
                                    Active
                                </span>
                            @else
                                <span class="badge status-inactive">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="date-cell">{{ $user->created_at->format('M d, Y') }}</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ route('system-management.users.show', $user) }}" class="btn-icon btn-icon-view" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a href="{{ route('system-management.users.edit', $user) }}" class="btn-icon btn-icon-edit" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </a>
                                @if($user->canBeDeleted() && $user->id !== auth()->id())
                                    <form action="{{ route('system-management.users.destroy', $user) }}" method="POST" class="inline-form" data-confirm-delete="Are you sure you want to delete this user?" data-delete-title="Delete User">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-danger" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="btn-icon btn-icon-disabled" title="{{ $user->isSystem() ? 'System user protected' : 'Cannot delete yourself' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                <h3>No users found</h3>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
        <div class="table-footer">
            <div class="table-info">
                Showing {{ number_format($users->firstItem()) }} to {{ number_format($users->lastItem()) }} of {{ number_format($users->total()) }} users
            </div>
            <div class="pagination-wrapper">
                {{ $users->withQueryString()->links('vendor.pagination.custom') }}
            </div>
        </div>
    @endif
</div>
@endsection
