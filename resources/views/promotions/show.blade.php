@extends('layouts.app')

@section('title', 'View Promotion Banner')

@section('breadcrumb')
    <a href="{{ route('promotions.index') }}" class="breadcrumb-item">Promotion Banners</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($promotion->title, 30) }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Promotion Details</h2>
        <p>View Promotion banner information</p>
    </div>
    
    <div class="page-header-right">
        <a href="{{ route('promotions.edit', $promotion) }}" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            Back
        </a>
    </div>
</div>

<!-- Banner Preview -->
<div class="promo-preview-wrapper promo-preview-full">
    <h3 class="preview-title">Promotion Preview</h3>
    <div class="promo-preview promo-preview-large" style="background: {{ $promotion->getBackgroundStyle() }};">
        @if($promotion->hasImage())
            <img src="{{ $promotion->getImageUrl() }}" alt="{{ $promotion->title }}" class="promo-preview-image">
        @endif
        <div class="promo-preview-content">
            <span class="promo-preview-text" style="color: {{ $promotion->text_color }};">{{ $promotion->title }}</span>
            @if($promotion->message)
                <span class="promo-preview-message" style="color: {{ $promotion->text_color }};">{{ $promotion->message }}</span>
            @endif
        </div>
        @if($promotion->button_text)
            <a href="{{ $promotion->button_url ?: '#' }}" class="promo-preview-button" style="background: {{ $promotion->getButtonStyle() }}; color: {{ $promotion->button_text_color }};" target="_blank">
                {{ $promotion->button_text }}
            </a>
        @endif
    </div>
</div>

<!-- Detail Card -->
<div class="detail-card">
    <div class="detail-header" style="background: linear-gradient(135deg, {{ $promotion->background_color }}22 0%, {{ $promotion->background_color }}11 100%);">
        <div class="promo-icon" style="background: {{ $promotion->getBackgroundStyle() }}; color: {{ $promotion->text_color }};">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
            </svg>
        </div>
        <div class="detail-header-info">
            <h3>{{ $promotion->title }}</h3>
            <p>{{ $promotion->message ?: 'No message set' }}</p>
        </div>
        {{-- <div class="detail-badges">
            <span class="badge {{ $promotion->getStatusBadgeClass() }}">
                @if($promotion->getStatus() === 'active')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                @elseif($promotion->getStatus() === 'scheduled')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                @elseif($promotion->getStatus() === 'expired')
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                @endif
                {{ $promotion->getStatusDisplayName() }}
            </span>
        </div> --}}
    </div>
    
    <div class="detail-body">
        <div class="detail-section">
            <h4 class="detail-section-title">Schedule Information</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Start Date</label>
                    <span>{{ $promotion->start_date ? $promotion->start_date->format('F d, Y \a\t h:i A') : 'Immediate' }}</span>
                </div>
                <div class="detail-item">
                    <label>End Date</label>
                    <span>{{ $promotion->end_date ? $promotion->end_date->format('F d, Y \a\t h:i A') : 'No expiration' }}</span>
                </div>
                <div class="detail-item">
                    <label>Duration</label>
                    <span>
                        @if($promotion->start_date && $promotion->end_date)
                            {{ $promotion->start_date->diffForHumans($promotion->end_date, true) }}
                        @else
                            Unlimited
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <div class="detail-section">
            <h4 class="detail-section-title">Button Configuration</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Button Text</label>
                    <span>{{ $promotion->button_text ?: 'Not set' }}</span>
                </div>
                <div class="detail-item">
                    <label>Button URL</label>
                    @if($promotion->button_url)
                        <a href="{{ $promotion->button_url }}" target="_blank" class="detail-link">
                            {{ Str::limit($promotion->button_url, 40) }}
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    @else
                        <span class="text-muted">Not set</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="detail-section">
            <h4 class="detail-section-title">Appearance</h4>
            <div class="detail-grid detail-grid-2">
                <!-- Background Color -->
                <div class="detail-item">
                    <label>Background Color</label>
                    @if($promotion->background_gradient_type === 'gradient')
                        <div class="gradient-detail">
                            <div class="gradient-swatch" style="background: {{ $promotion->getBackgroundStyle() }};"></div>
                            <div class="gradient-info">
                                <div class="gradient-colors-display">
                                    <span class="color-value">{{ $promotion->background_color }}</span>
                                    <span class="gradient-arrow-sm">→</span>
                                    <span class="color-value">{{ $promotion->background_color_2 }}</span>
                                </div>
                                <span class="gradient-direction-text">{{ ucwords(str_replace('to ', '', $promotion->background_gradient_direction ?? 'to right')) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="color-preview">
                            <span class="color-swatch" style="background-color: {{ $promotion->background_color }};"></span>
                            <span class="color-value">{{ $promotion->background_color }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- Button Color -->
                <div class="detail-item">
                    <label>Button Color</label>
                    @if($promotion->button_gradient_type === 'gradient')
                        <div class="gradient-detail">
                            <div class="gradient-swatch" style="background: {{ $promotion->getButtonStyle() }};"></div>
                            <div class="gradient-info">
                                <div class="gradient-colors-display">
                                    <span class="color-value">{{ $promotion->button_color }}</span>
                                    <span class="gradient-arrow-sm">→</span>
                                    <span class="color-value">{{ $promotion->button_color_2 }}</span>
                                </div>
                                <span class="gradient-direction-text">{{ ucwords(str_replace('to ', '', $promotion->button_gradient_direction ?? 'to right')) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="color-preview">
                            <span class="color-swatch" style="background-color: {{ $promotion->button_color }};"></span>
                            <span class="color-value">{{ $promotion->button_color }}</span>
                        </div>
                    @endif
                </div>
                
                <!-- Text Color -->
                <div class="detail-item">
                    <label>Text Color</label>
                    <div class="color-preview">
                        <span class="color-swatch" style="background-color: {{ $promotion->text_color }};"></span>
                        <span class="color-value">{{ $promotion->text_color }}</span>
                    </div>
                </div>
                
                <!-- Button Text Color -->
                <div class="detail-item">
                    <label>Button Text Color</label>
                    <div class="color-preview">
                        <span class="color-swatch" style="background-color: {{ $promotion->button_text_color }};"></span>
                        <span class="color-value">{{ $promotion->button_text_color }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        @if($promotion->hasImage())
        <div class="detail-section">
            <h4 class="detail-section-title">Promotion Image</h4>
            <div class="detail-item">
                <div class="detail-image-preview">
                    <a href="{{ $promotion->getImageUrl() }}" target="_blank">
                        <img src="{{ $promotion->getImageUrl() }}" alt="Banner image">
                    </a>
                </div>
            </div>
        </div>
        @endif
        
        <div class="detail-section">
            <h4 class="detail-section-title">Metadata</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Created At</label>
                    <span 
                        class="datetime" 
                        data-iso="{{ $promotion->created_at->toIso8601String() }}"
                    >
                        {{ $promotion->created_at->format('F d, Y h:i A') }}
                    </span>
                </div>
                <div class="detail-item">
                    <label>Last Updated</label>
                    <span 
                        class="datetime" 
                        data-iso="{{ $promotion->updated_at->toIso8601String() }}"
                    >
                        {{ $promotion->updated_at->format('F d, Y h:i A') }}
                    </span>
                </div>
                <div class="detail-item">
                    <label>Created By</label>
                    <span>{{ $promotion->creator?->name ?? 'Unknown' }}</span>
                </div>
                <div class="detail-item">
                    <label>Promotion ID</label>
                    <span class="uuid-cell">{{ $promotion->id }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

