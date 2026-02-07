@extends('layouts.app')

@section('title', 'Users Level Configurations')

@section('breadcrumb')
    <a href="{{ route('system-management.level-configurations.index') }}" class="breadcrumb-item">Users Level Configurations</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Users Level Configurations</span>
@endsection

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
        <h2>Users Level Configurations</h2>
        <p>Manage user level system configurations (Level 1-50)</p>
    </div>

    @if(auth()->user()->isSystem() || auth()->user()->hasAdminAccess())
        <div class="page-header-right">
            <a href="{{ route('system-management.level-configurations.create') }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Funds
            </a>
        </div>
    @endif
</div>

<!-- Level Configurations Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-header-left">
            <form action="{{ route('system-management.level-configurations.index') }}" method="GET" class="per-page-form">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('tier'))
                    <input type="hidden" name="tier" value="{{ request('tier') }}">
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
            <form action="{{ route('system-management.level-configurations.index') }}" method="GET" class="table-search-form">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                @if(request('tier'))
                    <input type="hidden" name="tier" value="{{ request('tier') }}">
                @endif
                @if(request('sorting'))
                    <input type="hidden" name="sorting" value="{{ request('sorting') }}">
                @endif
                <div class="table-search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="search" placeholder="Search users..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="filters-panel" id="filtersPanel">
        <form action="{{ route('system-management.level-configurations.index') }}" method="GET" class="filters-form">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('sorting'))
                <input type="hidden" name="sorting" value="{{ request('sorting') }}">
            @endif
            
            <div class="filters-grid grid-4">
                <div class="filter-group">
                    <label for="filter_tier">Tier</label>
                    <select name="tier" id="filter_tier" class="filter-select">
                        <option value="">All Tiers</option>
                        <option value="bronze" {{ request('tier') == 'bronze' ? 'selected' : '' }}>Bronze</option>
                        <option value="silver" {{ request('tier') == 'silver' ? 'selected' : '' }}>Silver</option>
                        <option value="gold" {{ request('tier') == 'gold' ? 'selected' : '' }}>Gold</option>
                        <option value="platinum" {{ request('tier') == 'platinum' ? 'selected' : '' }}>Platinum</option>
                        <option value="diamond" {{ request('tier') == 'diamond' ? 'selected' : '' }}>Diamond</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filter_sorting">Sorting</label>
                    <select name="sorting" id="filter_sorting" class="filter-select">
                        <option value="">All</option>
                        <option value="level_asc" {{ request('sorting') == 'level_asc' ? 'selected' : '' }}>Level: Low to High</option>
                        <option value="level_desc" {{ request('sorting') == 'level_desc' ? 'selected' : '' }}>Level: High to Low</option>
                        <option value="total_funds_asc" {{ request('sorting') == 'total_funds_asc' ? 'selected' : '' }}>Total Funds: Low to High</option>
                        <option value="total_funds_desc" {{ request('sorting') == 'total_funds_desc' ? 'selected' : '' }}>Total Funds: High to Low</option>
                    </select>
                </div>
            </div>
            
            <div class="filters-actions">
                <button type="submit" class="btn btn-info">
                    Apply Filters
                </button>
                <a href="{{ route('system-management.level-configurations.index', ['per_page' => request('per_page')]) }}" class="btn btn-secondary">
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
                    <th class="">User</th>
                    <th>Level</th>
                    <th>Tier</th>
                    <th>Progress</th>
                    <th>Total Deposits (IDR)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td class="td-number">{{ $users->firstItem() + $loop->index }}</td>
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
                            <div class="level-info" style="display: flex; align-items: center; gap: 8px;">
                                <span class="badge status-active">
                                    {{ $user->current_level }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge tier-badge tier-bg-{{ strtolower($user->tier) }}">
                                {{ ucfirst($user->tier) }}
                            </span>
                        </td>
                        
                        <td>
                            @php
                                $levelInfo = $user->getLevelInfo();
                                $isMaxLevel = $levelInfo['is_max_level'] ?? false;
                            @endphp
                            @if($isMaxLevel)
                                <strong class="text-max-level">MAX</strong>
                            @else
                                @php
                                    $progress = $levelInfo['progress_percentage'];
                                    $colorClass = match(true) {
                                        $progress >= 90 => 'progress-excellent', // 90-99%
                                        $progress >= 75 => 'progress-very-good', // 75-89%
                                        $progress >= 50 => 'progress-good',      // 50-74%
                                        $progress >= 25 => 'progress-fair',      // 25-49%
                                        default => 'progress-poor'               // 0-24%
                                    };
                                @endphp
                                <strong class="progress-percentage {{ $colorClass }}">
                                    {{ $progress }}%
                                </strong>
                            @endif
                        </td>
                        <td>
                            <strong> {{ number_format($user->total_accumulated_funds, 0, '.', ',') }}</strong>
                        </td>
                       
                    </tr>
                @empty
                    <tr>
                        <td colspan="25">
                            <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
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
