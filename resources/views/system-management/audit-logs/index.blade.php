@extends('layouts.app')

@section('title', 'Audit Logs')

@section('breadcrumb')
    <a href="{{ route('system-management.audit-logs.index') }}" class="breadcrumb-item">Audit Logs</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Audit Logs</span>
@endsection

@section('content')
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span class="alert-content">{{ session('success') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span class="alert-content">{{ session('error') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h2>Audit Logs</h2>
            <p>Manage audit logs</p>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-header-left">
                <form action="{{ route('system-management.audit-logs.index') }}" method="GET" class="per-page-form">
                    @if (request('search'))
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
            </div>
            <div class="table-header-right">
                <form action="{{ route('system-management.audit-logs.index') }}" method="GET" class="table-filters">
                    <div class="table-search-box">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input type="text" name="search" placeholder="Search audit logs..."
                            value="{{ request('search') }}">
                        <button type="submit" class="search-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="th-number">#</th>
                        <th>Date/Time</th>
                        <th>Created By</th>
                        <th>To User</th>
                        <th>Action</th>
                        <th>IP Address</th>
                        <th>Changes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auditLogs as $index => $auditLog)
                        <tr>
                            <td class="td-number">{{ $auditLogs->firstItem() + $index }}</td>
                            <td>
                                <span class="local-time"
                                    data-utc="{{ $auditLog->created_at->toIso8601String() }}">{{ $auditLog->created_at->format('M d, Y H:i A') }}</span>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-name">{{ $auditLog->user->name ?? 'N/A' }}</div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $targetUser = isset($auditLog->new_value['user_id'])
                                        ? \App\Models\User::find($auditLog->new_value['user_id'])
                                        : null;
                                @endphp
                                <div class="user-cell">
                                    <img src="{{ $targetUser ? $targetUser->getAvatarUrl() : asset('images/avatars/default-avatar.webp') }}"
                                        alt="{{ $auditLog->new_value['user_name'] ?? '' }}" class="user-avatar-img">
                                    <div class="user-info">
                                        <div class="user-name">{{ $auditLog->new_value['user_name'] ?? 'N/A' }}</div>
                                        <div class="user-email">{{ $auditLog->new_value['user_email'] ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge status-active">
                                    {{ str_replace('_', ' ', ucfirst($auditLog->action_type ?? 'N/A')) }}
                                </span>
                            </td>
                            <td>
                                <span
                                    style="font-family: monospace; font-size: 0.85rem;">{{ $auditLog->ip_address ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <a href="{{ route('system-management.audit-logs.show', $auditLog) }}"
                                    class="btn-icon btn-icon-view" title="View">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                    </svg>
                                    <h3>No audit logs found</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($auditLogs->hasPages())
            <div class="table-footer">
                <div class="table-info">
                    Showing {{ $auditLogs->firstItem() }} to {{ $auditLogs->lastItem() }} of
                    {{ $auditLogs->total() }} audit logs
                </div>
                <div class="pagination-wrapper">
                    {{ $auditLogs->withQueryString()->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <style>
        .user-agent-text {
            font-size: 0.75rem;
            color: #9ca3af;
            cursor: help;
        }

        .badge-success {
            background-color: #05966930;
            color: #059669;
        }

        .badge-info {
            background-color: #3b82f6;
            color: white;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #4b5563;
            color: #e5e7eb;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.75rem;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-outline:hover {
            background: #374151;
            border-color: #6b7280;
        }

        .btn-outline svg {
            width: 14px;
            height: 14px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Convert UTC timestamps to local time
            document.querySelectorAll('.local-time').forEach(function(element) {
                const utcDate = new Date(element.dataset.utc);
                const options = {
                    year: 'numeric',
                    month: 'short',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                };
                element.textContent = utcDate.toLocaleDateString('en-US', options).replace(',', '');
            });
        });
    </script>
@endpush
