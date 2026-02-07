@extends('layouts.app')

@section('title', 'View Newsletter Subscriber')

@section('breadcrumb')
    <a href="{{ route('newsletter-subscribers.index') }}" class="breadcrumb-item">Newsletter Subscribers</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($newsletterSubscriber->email, 30) }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Newsletter Subscriber Details</h2>
            <p>View subscriber information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('newsletter-subscribers.edit', $newsletterSubscriber) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            <a href="{{ route('newsletter-subscribers.index') }}" class="btn btn-secondary">
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
                    <path d="M22 13V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h9"></path>
                    <polyline points="22,7 12,13 2,7"></polyline>
                    <line x1="19" y1="16" x2="19" y2="22"></line>
                    <line x1="16" y1="19" x2="22" y2="19"></line>
                  </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ $newsletterSubscriber->email }}</h3>
                <p>{{ $newsletterSubscriber->is_active ? 'Active Subscriber' : 'Inactive Subscriber' }}</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-2">
                    <div class="detail-item">
                        <label>Email</label>
                        <span>{{ $newsletterSubscriber->email }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="badge {{ $newsletterSubscriber->is_active ? 'status-active' : 'status-inactive' }}">{{ $newsletterSubscriber->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Subscribed At</label>
                        <span class="datetime" data-iso="{{ $newsletterSubscriber->subscribed_at->toIso8601String() }}">
                            {{ $newsletterSubscriber->subscribed_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    @if($newsletterSubscriber->user)
                    <div class="detail-item">
                        <label>Associated User</label>
                        <span>{{ $newsletterSubscriber->user->name }} ({{ $newsletterSubscriber->user->email }})</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <span class="datetime" data-iso="{{ $newsletterSubscriber->created_at->toIso8601String() }}">
                            {{ $newsletterSubscriber->created_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime" data-iso="{{ $newsletterSubscriber->updated_at->toIso8601String() }}">
                            {{ $newsletterSubscriber->updated_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Subscriber ID</label>
                        <span class="uuid-cell">{{ $newsletterSubscriber->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
