@extends('layouts.app')

@section('title', 'View Casino')

@section('breadcrumb')
    <a href="{{ route('game-management.casinos.index') }}" class="breadcrumb-item">Casinos</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($casino->name, 30) }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Casino Details</h2>
            <p>View casino information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('game-management.casinos.edit', $casino) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            <a href="{{ route('game-management.casinos.index') }}" class="btn btn-secondary">
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
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dice-5" viewBox="0 0 16 16">
                    <path d="M13 1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2zM3 0a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V3a3 3 0 0 0-3-3z"/>
                    <path d="M5.5 4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m8 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m0 8a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m-8 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m4-4a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                  </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ $casino->name }}</h3>
                <p>{{ $casino->description ?: 'N/A' }}</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-4">
                    <div class="detail-item">
                        <label>Title</label>
                        <span>{{ $casino->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Category</label>
                        <span class="badge text-blue bg-blue">{{ $casino->category->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Slug</label>
                                <span class="badge text-indigo bg-indigo">{{ $casino->slug }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Rating</label>
                        <span class="badge text-yellow bg-yellow">{{ $casino->rating }}</span>
                    </div>
                    <div class="detail-item">
                        <label>RTP</label>
                        <span class="badge text-green bg-green">{{ $casino->rtp }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Daily Withdrawal Amount</label>
                        <span class="badge text-green bg-green">{{ $casino->daily_withdrawal_amount }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Daily Withdrawal Players</label>
                        <span class="badge text-teal bg-teal">{{ $casino->daily_withdrawal_players }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Last Withdrawal Update</label>
                        <span class="datetime badge text-green bg-green" data-iso="{{ $casino->last_withdrawal_update->toIso8601String() }}">
                            {{ $casino->last_withdrawal_update->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Total Withdrawn</label>
                        <span class="badge text-yellow bg-yellow">{{ $casino->total_withdrawn }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Description</label>
                        <span>{{ $casino->description ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h4 class="detail-section-title">Image</h4>
                <div class="detail-item">
                    <div class="detail-image-preview">
                        <a href="{{ $casino->getImageUrl() }}" target="_blank">
                            <img src="{{ $casino->getImageUrl() }}" alt="{{ $casino->name }}" class="img-fluid">
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
                        <span class="datetime" data-iso="{{ $casino->created_at->toIso8601String() }}">
                            {{ $casino->created_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                            <span class="datetime" data-iso="{{ $casino->updated_at->toIso8601String() }}">
                            {{ $casino->updated_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Casino ID</label>
                        <span class="uuid-cell">{{ $casino->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
