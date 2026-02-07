@extends('layouts.app')

@section('title', 'RTP Games')

@section('breadcrumb')
    <a href="{{ route('rtp-games') }}" class="breadcrumb-item">RTP Games</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">RTP Games</span>
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
        <h2>RTP Games</h2>
        <p>Manage RTP games</p>
    </div>
    
    <div class="page-header-right">
        <a href="{{ route('system-management.rtp-configurations.index') }}" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            RTP Configuration
        </a>
    </div>
</div>

<!-- Categories Table -->
<div class="table-card">
    <div class="table-header">
        <div class="table-header-left">
            <form action="{{ route('rtp-games') }}" method="GET" class="per-page-form">
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
            <form action="{{ route('rtp-games') }}" method="GET" class="table-search-form">
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <div class="table-search-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="search" placeholder="Search RTP games..." value="{{ request('search') }}">
                </div>
            </form>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="filters-panel" id="filtersPanel">
        <form action="{{ route('rtp-games') }}" method="GET" class="filters-form">
            @if(request('per_page'))
                <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            @endif
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            
            <div class="filters-grid grid-3">
                <div class="filter-group">
                    <label for="filter_provider">Provider</label>
                    <select name="provider" id="filter_provider" class="filter-select">
                        <option value="">All Providers</option>
                        @foreach($providers as $provider)
                            <option value="{{ $provider->slug }}" {{ request('provider') == $provider->slug ? 'selected' : '' }}>{{ $provider->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="filters-actions">
                <button type="submit" class="btn btn-info">
                    Apply Filters
                </button>
                <a href="{{ route('rtp-games', ['per_page' => request('per_page')]) }}" class="btn btn-secondary">
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
                    <th>Name</th>
                    <th>Image</th>
                    <th>Provider</th>
                    <th>Rating</th>
                    <th>RTP</th>
                    <th>Pola</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rtpGames as $index => $rtpGame)
                    <tr>
                        <td class="td-number">{{ $rtpGames->firstItem() + $index }}</td>
                        <td>
                            <div class="banner-cell">
                                <div class="banner-info">
                                    <div class="banner-title">{{ $rtpGame->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <img src="{{ $rtpGame->img_src }}" alt="{{ $rtpGame->name }}" class="banner-image" onclick="showImagePreview('{{ $rtpGame->img_src }}', '{{ addslashes($rtpGame->name) }}')" style="cursor: pointer;">
                        </td>
                        <td>
                            <span class="badge text-purple bg-purple">{{ $rtpGame->provider->name }}</span>
                        </td>
                        <td>
                            <span class="badge text-yellow bg-yellow">{{ $rtpGame->rating }}</span>
                        </td>
                        <td>
                            <span class="badge position-top">{{ $rtpGame->rtp }}%</span>
                        </td>
                        <td>
                            <span class="badge text-green bg-green">{{ $rtpGame->pola ?: 'N/A' }}%</span>
                        </td>
                        <td>
                            <span class="date-cell">{{ $rtpGame->created_at->format('M d, Y') }}</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" 
                                    class="btn-icon btn-icon-view" 
                                    title="View Steps"
                                    onclick="showStepOverviewModal({{ json_encode([
                                        'name' => $rtpGame->name,
                                        'img_src' => $rtpGame->img_src,
                                        'provider' => $rtpGame->provider->name,
                                        'rtp' => $rtpGame->rtp,
                                        'pola' => $rtpGame->pola,
                                        'rating' => $rtpGame->rating,
                                        'step_one' => $rtpGame->step_one,
                                        'type_step_one' => $rtpGame->type_step_one,
                                        'desc_step_one' => $rtpGame->desc_step_one,
                                        'step_two' => $rtpGame->step_two,
                                        'type_step_two' => $rtpGame->type_step_two,
                                        'desc_step_two' => $rtpGame->desc_step_two,
                                        'step_three' => $rtpGame->step_three,
                                        'type_step_three' => $rtpGame->type_step_three,
                                        'desc_step_three' => $rtpGame->desc_step_three,
                                        'step_four' => $rtpGame->step_four,
                                        'type_step_four' => $rtpGame->type_step_four,
                                        'desc_step_four' => $rtpGame->desc_step_four,
                                        'stake_bet' => $rtpGame->stake_bet,
                                    ]) }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </button>
                            </div>
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
                                <h3>No RTP games found</h3>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($rtpGames->hasPages())
        <div class="table-footer">
            <div class="table-info">
                Showing {{ number_format($rtpGames->firstItem()) }} to {{ number_format($rtpGames->lastItem()) }} of {{ number_format($rtpGames->total()) }} RTP games
            </div>
            <div class="pagination-wrapper">
                {{ $rtpGames->withQueryString()->links('vendor.pagination.custom') }}
            </div>
        </div>
    @endif
</div>

<!-- Step Overview Modal -->
<div id="stepOverviewModal" class="modal-overlay" style="display: none;">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h3>Step Overview</h3>
            <button type="button" class="modal-close" onclick="closeStepOverviewModal()" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="close-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="modal-body">
            <div class="step-overview-header">
                <div class="step-game-info">
                    <img id="stepModalGameImage" src="" alt="" class="step-game-image">
                    <div class="step-game-details">
                        <h4 id="stepModalGameName"></h4>
                        <div class="step-game-meta">
                            <span class="badge text-purple bg-purple" id="stepModalProvider"></span>
                            <span class="badge position-top" id="stepModalRTP"></span>
                            <span class="badge text-green bg-green" id="stepModalPola"></span>
                            <span class="badge text-yellow bg-yellow" id="stepModalRating"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="steps-container">
                <div class="step-item" id="stepOneContainer">
                    <div class="step-header">
                        <div class="step-number">1</div>
                        <div class="step-info">
                            <h5>Step One</h5>
                            <span class="step-type" id="stepOneType"></span>
                        </div>
                        <div class="step-value" id="stepOneValue"></div>
                    </div>
                    <p class="step-description" id="stepOneDesc"></p>
                </div>

                <div class="step-item" id="stepTwoContainer">
                    <div class="step-header">
                        <div class="step-number">2</div>
                        <div class="step-info">
                            <h5>Step Two</h5>
                            <span class="step-type" id="stepTwoType"></span>
                        </div>
                        <div class="step-value" id="stepTwoValue"></div>
                    </div>
                    <p class="step-description" id="stepTwoDesc"></p>
                </div>

                <div class="step-item" id="stepThreeContainer">
                    <div class="step-header">
                        <div class="step-number">3</div>
                        <div class="step-info">
                            <h5>Step Three</h5>
                            <span class="step-type" id="stepThreeType"></span>
                        </div>
                        <div class="step-value" id="stepThreeValue"></div>
                    </div>
                    <p class="step-description" id="stepThreeDesc"></p>
                </div>

                <div class="step-item" id="stepFourContainer">
                    <div class="step-header">
                        <div class="step-number">4</div>
                        <div class="step-info">
                            <h5>Step Four</h5>
                            <span class="step-type" id="stepFourType"></span>
                        </div>
                        <div class="step-value" id="stepFourValue"></div>
                    </div>
                    <p class="step-description" id="stepFourDesc"></p>
                </div>
            </div>

            <div class="step-footer-info">
                <div class="info-item">
                    <span class="info-label">Stake Bet:</span>
                    <span class="info-value" id="stepModalStakeBet"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeStepOverviewModal()">Close</button>
        </div>
    </div>
</div>

@endsection

