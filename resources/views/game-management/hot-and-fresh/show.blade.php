@extends('layouts.app')

@section('title', 'View Hot & Fresh')

@section('breadcrumb')
    <a href="{{ route('game-management.hot-and-fresh.index') }}" class="breadcrumb-item">Hot & Fresh</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($hotAndFresh->name, 30) }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Hot & Fresh Details</h2>
            <p>View hot and fresh information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('game-management.hot-and-fresh.edit', $hotAndFresh) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            <a href="{{ route('game-management.hot-and-fresh.index') }}" class="btn btn-secondary">
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
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                  </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ $hotAndFresh->name }}</h3>
                <p>{{ $hotAndFresh->description ?: 'N/A' }}</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-4">
                    <div class="detail-item">
                        <label>Title</label>
                        <span>{{ $hotAndFresh->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Slug</label>
                        <span class="badge text-indigo bg-indigo">{{ $hotAndFresh->slug }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Rating</label>
                            <span class="badge text-yellow bg-yellow">{{ $hotAndFresh->rating }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Rank</label>
                        <span class="badge text-green bg-green">{{ $hotAndFresh->rank }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Badge</label>
                        <span class="badge text-purple bg-purple">{{ ucfirst($hotAndFresh->badge) }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Tier</label>
                        <span class="badge text-orange bg-orange">{{ ucfirst($hotAndFresh->tier) }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label>Price</label>
                        <span class="badge text-green bg-green">{{ $hotAndFresh->price }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Win Rate</label>
                        <span class="badge text-teal bg-teal">{{ $hotAndFresh->win_rate_increase }}%</span>
                    </div>
                    <div class="detail-item">
                        <label>User Count</label>
                        <span class="badge text-green bg-green">{{ number_format($hotAndFresh->user_count) }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Active Hours</label>
                        <span class="badge text-yellow bg-yellow">{{ number_format($hotAndFresh->active_hours) }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Description</label>
                        <span>{{ $hotAndFresh->description ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4 class="detail-section-title">Image</h4>
                <div class="detail-item">
                    <div class="detail-image-preview">
                        <a href="{{ $hotAndFresh->getImageUrl() }}" target="_blank">
                            <img src="{{ $hotAndFresh->getImageUrl() }}" alt="{{ $hotAndFresh->name }}" class="img-fluid">
                        </a>
                    </div>
                </div>
            </div>


            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                            <span class="datetime" data-iso="{{ $hotAndFresh->created_at->toIso8601String() }}">
                            {{ $hotAndFresh->created_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime" data-iso="{{ $hotAndFresh->updated_at->toIso8601String() }}">
                            {{ $hotAndFresh->updated_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Hot & Fresh ID</label>
                        <span class="uuid-cell">{{ $hotAndFresh->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
