@extends('layouts.app')

@section('title', 'View Demo Game')

@section('breadcrumb')
    <a href="{{ route('game-management.demo-games.index') }}" class="breadcrumb-item">Demo Games</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($demoGame->title, 30) }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Demo Game Details</h2>
            <p>View demo game information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('game-management.demo-games.edit', $demoGame) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            <a href="{{ route('game-management.demo-games.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>


    <!-- Detail Card -->
    <div class="detail-card">
        <div class="detail-header">
            <div class="banner-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" />
                    <path d="M8 21h8" />
                    <path d="M12 17v4" />
                    <polygon points="10 8 10 12 14 10 10 8" fill="currentColor" />
                </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ $demoGame->title }}</h3>
                <p>{{ $demoGame->description ?: 'N/A' }}</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Title</label>
                        <span>{{ $demoGame->title }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Slug</label>
                        <span class="badge text-indigo bg-indigo">{{ $demoGame->slug }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Demo</label>
                        <span
                            class="badge {{ $demoGame->is_demo ? 'status-active' : 'status-inactive' }}">{{ $demoGame->is_demo ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>URL</label>
                        @if ($demoGame->url)
                            <a href="{{ $demoGame->url }}" target="_blank" rel="noopener noreferrer"
                                style="color: #3b82f6; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                <span>{{ $demoGame->url }}</span>
                            </a>
                        @else
                            <span>N/A</span>
                        @endif
                    </div>
                    <div class="detail-item">
                        <label>Description</label>
                        <span>{{ $demoGame->description ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Demo Game Image -->
            @if ($demoGame->hasImage())
                <div class="detail-section">
                    <h4 class="detail-section-title">Image</h4>
                    <div class="detail-item">
                        <div class="detail-image-preview">
                            <a href="{{ $demoGame->getImageUrl() }}" target="_blank">
                                <img src="{{ $demoGame->getImageUrl() }}" alt="{{ $demoGame->title }}">
                            </a>
                        </div>
                    </div>
                </div>
            @endif


            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Created At</label>
                        <span class="datetime" data-iso="{{ $demoGame->created_at->toIso8601String() }}">
                            {{ $demoGame->created_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime" data-iso="{{ $demoGame->updated_at->toIso8601String() }}">
                            {{ $demoGame->updated_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Created By</label>
                        <span class="">{{ $demoGame->creator?->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Demo Game ID</label>
                        <span class="uuid-cell">{{ $demoGame->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
