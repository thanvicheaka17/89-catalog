@extends('layouts.app')

@section('title', 'View Event')

@section('breadcrumb')
    <a href="{{ route('events.index') }}" class="breadcrumb-item">Events</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($event->title, 30) }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Event Details</h2>
            <p>View event information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('events.edit', $event) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            <a href="{{ route('events.index') }}" class="btn btn-secondary">
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
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                  </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ $event->title }}</h3>
                <p>{{ $event->description ?: 'N/A' }}</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-2">
                    <div class="detail-item">
                        <label>Title</label>
                        <span>{{ $event->title }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Status</label>
                        <span class="badge {{ $event->is_active ? 'status-active' : 'status-inactive' }}">{{ $event->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Start At</label>
                        <span class="datetime" data-iso="{{ $event->start_at->toIso8601String() }}">
                            {{ $event->start_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>End At</label>
                        <span class="datetime" data-iso="{{ $event->end_at->toIso8601String() }}">
                            {{ $event->end_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Created By</label>
                        <span>{{ $event->creator?->name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Description</label>
                        <span>{{ $event->description ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <span class="datetime" data-iso="{{ $event->created_at->toIso8601String() }}">
                            {{ $event->created_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime" data-iso="{{ $event->updated_at->toIso8601String() }}">
                            {{ $event->updated_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Event ID</label>
                        <span class="uuid-cell">{{ $event->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
