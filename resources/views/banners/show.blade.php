@extends('layouts.app')

@section('title', 'View Banner')

@section('breadcrumb')
    <a href="{{ route('banners.index') }}" class="breadcrumb-item">Banners</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($banner->title, 30) }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Banner Details</h2>
        <p>View banner information</p>
    </div>
    
    <div class="page-header-right">
        <a href="{{ route('banners.edit', $banner) }}" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('banners.index') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            Back
        </a>
    </div>
</div>

<!-- Banner Preview -->
<div class="promo-preview-wrapper promo-preview-full">
    <h3 class="preview-title">Banner Preview</h3>
    <div class="banner-preview-container">
        @if($banner->hasImage())
            <div class="banner-image-wrapper">
                <a href="{{ $banner->getImageUrl() }}" target="_blank" class="banner-image-link">
                    <img src="{{ $banner->getImageUrl() }}" alt="{{ $banner->title }}" class="banner-image-preview">
                </a>
            </div>
        @endif
        <div class="banner-content-preview">
            <h3 class="banner-preview-title">{{ $banner->title }}</h3>
            @if($banner->subtitle)
                <p class="banner-preview-subtitle">{{ $banner->subtitle }}</p>
            @endif
            @if($banner->link_url)
                <a href="{{ $banner->link_url }}" target="_blank" class="banner-preview-link">
                    {{ $banner->link_url }}
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </a>
            @endif
        </div>
    </div>
</div>


<!-- Detail Card -->
<div class="detail-card">
    <div class="detail-header">
        <div class="banner-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
        </div>
        <div class="detail-header-info">
            <h3>{{ $banner->title }}</h3>
            <p>{{ $banner->subtitle ?: 'No subtitle set' }}</p>
        </div>
    </div>
    
    <div class="detail-body">
        <!-- Basic Information -->
        <div class="detail-section">
            <h4 class="detail-section-title">Basic Information</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Title</label>
                    <span>{{ $banner->title }}</span>
                </div>
                <div class="detail-item">
                    <label>Subtitle</label>
                    <span>{{ $banner->subtitle ?: 'Not set' }}</span>
                </div>
                <div class="detail-item">
                    <label>Priority</label>
                    <span class="priority-badge">{{ $banner->priority }}</span>
                </div>
                <div class="detail-item">
                    <label>Visibility</label>
                    <span class="visibility-badge visibility-{{ $banner->visibility }}">
                        {{ ucfirst($banner->visibility) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Schedule Information -->
        <div class="detail-section" style="display: none;">
            <h4 class="detail-section-title">Schedule Information</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Start Date</label>
                    <span>
                        @if($banner->start_at)
                            {{ $banner->start_at->format('F d, Y \a\t h:i A') }}
                        @else
                            <span class="text-muted">Immediate</span>
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <label>End Date</label>
                    <span>
                        @if($banner->end_at)
                            {{ $banner->end_at->format('F d, Y \a\t h:i A') }}
                        @else
                            <span class="text-muted">No expiration</span>
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <label>Duration</label>
                    <span>
                        @if($banner->start_at && $banner->end_at)
                            {{ $banner->start_at->diffForHumans($banner->end_at, true) }}
                        @elseif($banner->start_at)
                            Starts {{ $banner->start_at->diffForHumans() }}
                        @else
                            <span class="text-muted">Unlimited</span>
                        @endif
                    </span>
                </div>
                <div class="detail-item">
                    <label>Current Status</label>
                    <span>
                        @if($banner->is_active)
                            <span class="status-badge status-active">Active</span>
                        @else
                            <span class="status-badge status-inactive">Inactive</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Link Configuration -->
        @if($banner->link_url)
        <div class="detail-section">
            <h4 class="detail-section-title">Link Configuration</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Link URL</label>
                    <a href="{{ $banner->link_url }}" target="_blank" class="detail-link">
                        {{ Str::limit($banner->link_url, 50) }}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Banner Image -->
        @if($banner->hasImage())
        <div class="detail-section">
            <h4 class="detail-section-title">Image</h4>
            <div class="detail-item">
                <div class="detail-image-preview">
                    <a href="{{ $banner->getImageUrl() }}" target="_blank">
                        <img src="{{ $banner->getImageUrl() }}" alt="{{ $banner->title }}">
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Meta Information -->
        {{-- @if($banner->meta)
        <div class="detail-section">
            <h4 class="detail-section-title">Additional Information</h4>
            <div class="detail-item">
                <div class="meta-content">
                    @if(is_array($banner->meta))
                        <pre class="meta-json">{{ json_encode($banner->meta, JSON_PRETTY_PRINT) }}</pre>
                    @else
                        <p>{{ $banner->meta }}</p>
                    @endif
                </div>
            </div>
        </div>
        @endif --}}

        <!-- Metadata -->
        <div class="detail-section">
            <h4 class="detail-section-title">Metadata</h4>
            <div class="detail-grid form-grid-3">
                <div class="detail-item">
                    <label>Created At</label>
                    <span 
                        class="datetime" 
                        data-iso="{{ $banner->created_at->toIso8601String() }}"
                    >
                        {{ $banner->created_at->format('F d, Y h:i A') }}
                    </span>
                </div>
                <div class="detail-item">
                    <label>Last Updated</label>
                    <span 
                        class="datetime" 
                        data-iso="{{ $banner->updated_at->toIso8601String() }}"
                    >
                        {{ $banner->updated_at->format('F d, Y h:i A') }}
                    </span>
                </div>
                <div class="detail-item">
                    <label>Banner ID</label>
                    <span class="uuid-cell">{{ $banner->id }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
