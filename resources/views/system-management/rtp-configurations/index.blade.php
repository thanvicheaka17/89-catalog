@extends('layouts.app')

@section('title', 'RTP Configurations')

@section('breadcrumb')
    <a href="{{ route('system-management.rtp-configurations.index') }}" class="breadcrumb-item">RTP Configurations</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">RTP Configurations</span>
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
            <div class="page-title-section">
                <div class="page-title-content">
                    <h1>RTP Configurations</h1>
                    <p class="page-subtitle">Configure RTP settings and multipliers for all games from each provider</p>
                </div>
            </div>
        </div>

        <div class="page-header-right">
            @if (auth()->user()->isSystem())
                @if ($hasExistingData)
                    <button type="button" class="btn btn-danger btn-with-icon"
                        onclick="window.rtpConfigurationHandler.deleteAllData(event)">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                        </svg>
                        Delete All Data
                    </button>
                @else
                    <button type="button" class="btn btn-info btn-with-icon"
                        onclick="window.rtpConfigurationHandler.syncAllProviders(event)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path>
                            <path d="M3 3v5h5"></path>
                            <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path>
                            <path d="M16 16h5v5"></path>
                        </svg>
                        Sync All Data
                    </button>
                @endif
                <button type="button" class="btn btn-secondary btn-with-icon"
                    onclick="window.rtpConfigurationHandler.openGlobalRTPSettings()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Global Settings
                </button>
            @endif
        </div>
    </div>

    <!-- Statistics Overview -->
    @if ($providers->count() > 0)
        <div class="stats-overview animate-fade-in">
            <div class="stat-card amber">
                <div class="stat-icon amber">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-building" viewBox="0 0 16 16">
                        <path
                            d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z" />
                        <path
                            d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3z" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $providers->count() }}</div>
                    <div class="stat-label">Total Providers</div>
                </div>
            </div>

            <div class="stat-card emerald">
                <div class="stat-icon emerald">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-controller"
                        viewBox="0 0 16 16">
                        <path
                            d="M11.5 6.027a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-1.5 1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m2.5-.5a.5.5 0 1 1-1 0 .5.5 0 0 1 1 0m-1.5 1.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1m-6.5-3h1v1h1v1h-1v1h-1v-1h-1v-1h1z" />
                        <path
                            d="M3.051 3.26a.5.5 0 0 1 .354-.613l1.932-.518a.5.5 0 0 1 .62.39c.655-.079 1.35-.117 2.043-.117.72 0 1.443.041 2.12.126a.5.5 0 0 1 .622-.399l1.932.518a.5.5 0 0 1 .306.729q.211.136.373.297c.408.408.78 1.05 1.095 1.772.32.733.599 1.591.805 2.466s.34 1.78.364 2.606c.024.816-.059 1.602-.328 2.21a1.42 1.42 0 0 1-1.445.83c-.636-.067-1.115-.394-1.513-.773-.245-.232-.496-.526-.739-.808-.126-.148-.25-.292-.368-.423-.728-.804-1.597-1.527-3.224-1.527s-2.496.723-3.224 1.527c-.119.131-.242.275-.368.423-.243.282-.494.575-.739.808-.398.38-.877.706-1.513.773a1.42 1.42 0 0 1-1.445-.83c-.27-.608-.352-1.395-.329-2.21.024-.826.16-1.73.365-2.606.206-.875.486-1.733.805-2.466.315-.722.687-1.364 1.094-1.772a2.3 2.3 0 0 1 .433-.335l-.028-.079zm2.036.412c-.877.185-1.469.443-1.733.708-.276.276-.587.783-.885 1.465a14 14 0 0 0-.748 2.295 12.4 12.4 0 0 0-.339 2.406c-.022.755.062 1.368.243 1.776a.42.42 0 0 0 .426.24c.327-.034.61-.199.929-.502.212-.202.4-.423.615-.674.133-.156.276-.323.44-.504C4.861 9.969 5.978 9.027 8 9.027s3.139.942 3.965 1.855c.164.181.307.348.44.504.214.251.403.472.615.674.318.303.601.468.929.503a.42.42 0 0 0 .426-.241c.18-.408.265-1.02.243-1.776a12.4 12.4 0 0 0-.339-2.406 14 14 0 0 0-.748-2.295c-.298-.682-.61-1.19-.885-1.465-.264-.265-.856-.523-1.733-.708-.85-.179-1.877-.27-2.913-.27s-2.063.091-2.913.27" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">
                        {{ number_format($providers->sum(function ($provider) {return $provider->games ? $provider->games->count() : 0;})) }}
                    </div>
                    <div class="stat-label">Total Games</div>
                </div>
            </div>

            <div class="stat-card sky">
                <div class="stat-icon sky">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">
                        {{ number_format($providers->avg(function ($provider) {return $provider->games ? $provider->games->avg('rtp') : 0;}),1) }}%
                    </div>
                    <div class="stat-label">Avg RTP</div>
                </div>
            </div>

            <div class="stat-card violet">
                <div class="stat-icon violet">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                        <path d="M3 3v5h5" />
                        <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                        <path d="M16 16h5v5" />
                    </svg>
                </div>
                <div class="stat-content">
                    <div class="stat-value">
                        {{ number_format($providers->where('games_count', '>', 0)->count()) }}
                    </div>
                    <div class="stat-label">Synced Providers</div>
                </div>
            </div>
        </div>
    @endif

    <!-- RTP Provider Cards -->
    <div
        class="rtp-providers-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6 mt-6 animate-fade-in">
        @forelse($providers ?? [] as $index => $provider)
            @php
                $hasGames = $provider->games && $provider->games->count() > 0;
                $storedGameData = $provider->getStoredGameData();
                $gameCount = $storedGameData ? $storedGameData['game_count'] : $provider->getGameCount();
                $rtpStats = $provider->getRTPStatistics();
                $avgRtp = $rtpStats['avg_rtp'] ?? 0;
                $minRtp = $rtpStats['min_rtp'] ?? 0;
                $maxRtp = $rtpStats['max_rtp'] ?? 0;
            @endphp

            <div class="provider-card {{ $hasGames ? 'has-games' : 'no-games' }}">
                <!-- Card Header with Status -->
                <div class="provider-card-header">
                    <div class="provider-main-info">
                        <div class="provider-details">
                            <div class="flex items-center gap-2">
                                <h3 class="provider-name">{{ $provider->name }}</h3>
                            </div>
                            <div class="provider-meta">
                                {{-- <span class="provider-slug">{{ $provider->slug }}</span> --}}
                                <span class="status-badge {{ $hasGames ? 'status-synced' : 'status-not-synced' }}">
                                    <span class="status-dot"></span>
                                    {{ $hasGames ? 'Synced' : 'Not Synced' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="provider-quick-stats">
                        @if ($gameCount > 0)
                            <div class="quick-stat">
                                <div class="quick-stat-value">{{ number_format($gameCount) }}</div>
                                <div class="quick-stat-label">Games</div>
                            </div>
                            <div class="quick-stat">
                                <div class="quick-stat-value">{{ number_format($avgRtp, 1) }}%</div>
                                <div class="quick-stat-label">Avg RTP</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="provider-card-footer">
                    <div class="provider-meta">
                        <span class="last-updated">Updated
                            {{ $provider->updated_at?->format('M d, Y') ?? 'Never' }}</span>
                    </div>
                    <div class="action-buttons">
                        <button type="button" class="btn-icon {{ $hasGames ? 'btn-icon-view' : 'btn-icon-disabled' }}"
                            title="{{ $hasGames ? 'View RTP Settings' : 'Please sync provider first' }}"
                            onclick="{{ $hasGames ? "window.rtpConfigurationHandler.viewProviderRTP('{$provider->slug}')" : 'window.rtpConfigurationHandler.viewProviderRTPDisabled(event)' }}">
                            @if ($hasGames)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 11-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            @endif
                        </button>
                        <button type="button" class="btn-icon {{ !$hasGames ? 'btn-icon-edit' : 'btn-icon-disabled' }}"
                            title="{{ $hasGames ? 'Already Synced' : 'Sync RTP Data' }}"
                            data-synced="{{ $hasGames ? 'true' : 'false' }}"
                            onclick="window.rtpConfigurationHandler.syncProviderRTP('{{ $provider->id }}', event, {{ $hasGames ? 'true' : 'false' }})">
                            @if ($hasGames)
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                    <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16" />
                                    <path d="m3 3 18 18" />
                                    <path d="M8 8H3V3" />
                                    <path d="M16 16h5v5" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state-card">
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                        </svg>
                    </div>
                    <div class="empty-content">
                        <h3>No Providers Found</h3>
                        <p>You need to add providers before configuring RTP settings. Get started by adding your first
                            provider.</p>
                        <div class="empty-actions">
                            <a href="{{ route('system-management.providers.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Add Provider
                            </a>
                            <a href="{{ route('system-management.providers.index') }}" class="btn btn-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75c0 .414.336.75.75.75h.75a.75.75 0 0 0 .75-.75V16.5m0 0V15c0-.414-.336-.75-.75-.75H9a.75.75 0 0 0-.75.75v1.5m0 0V18a.75.75 0 0 0 .75.75h.75a.75.75 0 0 0 .75-.75v-.75m0 0h3.75a.75.75 0 0 0 .75-.75V15c0-.414-.336-.75-.75-.75h-3.75a.75.75 0 0 0-.75.75v1.5Z" />
                                </svg>
                                View Providers
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Global RTP Settings Modal -->
    <div id="globalRTPSettingsModal" class="modal-overlay" style="display: none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3>Global RTP Configuration</h3>
                <button type="button" class="modal-close"
                    onclick="window.rtpConfigurationHandler.closeGlobalRTPSettingsModal()" title="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="close-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="global-settings-info">
                    <p class="info-text">
                        Configure global RTP and Pola ranges that apply to all providers. These settings will be applied
                        uniformly across all game providers.
                    </p>
                </div>
                <form id="globalRTPSettingsForm">
                    <div id="gamesSettingsContainer" class="games-settings-container">
                        <!-- Game settings will be loaded here -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    onclick="window.rtpConfigurationHandler.closeGlobalRTPSettingsModal()">Cancel</button>
                <button type="button" class="btn btn-info"
                    onclick="window.rtpConfigurationHandler.saveGlobalRTPSettings()">
                    Apply
                </button>
            </div>
        </div>
    </div>


    <!-- Sync All Providers Modal -->
    <div id="syncAllModal" class="modal-overlay sync-modal-overlay" style="display: none;">
        <div class="sync-modal-content">
            <!-- Animated background elements -->
            <div class="sync-bg-elements">
                <div class="sync-bg-circle sync-bg-circle-1"></div>
                <div class="sync-bg-circle sync-bg-circle-2"></div>
                <div class="sync-bg-circle sync-bg-circle-3"></div>
            </div>

            <div class="sync-modal-header">
                <div class="sync-header-content">
                    {{-- <div class="sync-header-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="sync-icon-main">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </div> --}}
                    <div class="sync-indicator">
                        <div class="sync-pulse-ring"></div>
                        <div class="sync-pulse-ring sync-pulse-ring-delay"></div>
                        <div class="sync-center-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="sync-rotating-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </div>
                    </div>
                    <div class="sync-header-text">
                        <h3>Syncing RTP Data</h3>
                        <p>Updating provider configurations across all games</p>
                        <small class="sync-warning-text" style="color: #f59e0b; font-weight: 500; font-size: 12px; margin-top: 4px; display: block;">
                            ⚠️ Do not refresh or close this page during sync
                        </small>
                    </div>
                </div>
                <button type="button" class="sync-modal-close" onclick="window.rtpConfigurationHandler.closeSyncModal()" id="syncCloseBtn" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="sync-modal-body">
                <!-- Progress Section -->
                <div class="sync-progress-section">
                    <div class="sync-progress-container">
                        <div class="sync-progress-info">
                            <div class="sync-progress-label" id="syncProgressLabel">Initializing...</div>
                            <div class="sync-progress-percentage" id="syncProgressPercentage">0%</div>
                        </div>
                        <div class="sync-progress-bar-container">
                            <div class="sync-progress-bar">
                                <div class="sync-progress-fill" id="syncProgressFill"></div>
                                <div class="sync-progress-shine"></div>
                            </div>
                        </div>
                        
                    </div>

                    <!-- Animated sync indicator -->
                    
                </div>

                <!-- Stats Section -->
                <div class="sync-stats-section">
                    <div class="sync-stat-cards">
                        <div class="sync-stat-card">
                            <div class="sync-stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building-fill" viewBox="0 0 16 16">
                                    <path d="M3 0a1 1 0 0 0-1 1v14a1 1 0 0 0 1 1h3v-3.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V16h3a1 1 0 0 0 1-1V1a1 1 0 0 0-1-1zm1 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5M4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM7.5 5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zM4.5 8h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5m2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5zm3.5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5"/>
                                  </svg>
                            </div>
                            <div class="sync-stat-content">
                                <div class="sync-stat-value" id="syncProvidersProcessed">0</div>
                                <div class="sync-stat-label">Providers Processed</div>
                            </div>
                        </div>
                        <div class="sync-stat-card">
                            <div class="sync-stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="sync-stat-content">
                                <div class="sync-stat-value" id="syncElapsedTime">00:00</div>
                                <div class="sync-stat-label">Elapsed Time</div>
                            </div>
                        </div>
                        <div class="sync-stat-card">
                            <div class="sync-stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="sync-stat-content">
                                <div class="sync-stat-value" id="syncSuccessCount">0</div>
                                <div class="sync-stat-label">Success</div>
                            </div>
                        </div>
                        <div class="sync-stat-card">
                            <div class="sync-stat-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
                                </svg>
                            </div>
                            <div class="sync-stat-content">
                                <div class="sync-stat-value" id="syncFailCount">0</div>
                                <div class="sync-stat-label">Failed</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logs Section -->
                <div class="sync-logs-section" style="display: none;">
                    <div class="sync-logs-header">
                        <h4>Activity Log</h4>
                    </div>
                    <div class="sync-logs-container" id="syncLogsContainer">
                        <div class="sync-log-item sync-log-item-info">
                            <div class="sync-log-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sync-icon-main">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
                                </svg>
                            </div>
                            <div class="sync-log-content">
                                <div class="sync-log-message">Initializing sync process...</div>
                                <div class="sync-log-timestamp" id="syncLogTimestamp1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sync-modal-footer" id="syncModalFooter">
                <button type="button" class="btn btn-secondary" id="syncModalCloseBtn" disabled
                    onclick="window.rtpConfigurationHandler.closeSyncModal()">
                    Close
                </button>
            </div>
        </div>
    </div>

@endsection

