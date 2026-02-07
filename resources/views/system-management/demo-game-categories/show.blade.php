@extends('layouts.app')

@section('title', 'View Demo Game Category')

@section('breadcrumb')
    <a href="{{ route('system-management.demo-game-categories.index') }}" class="breadcrumb-item">Demo Game Categories</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($demo_game_category->name, 30) }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Demo Game Category Details</h2>
        <p>View demo game category information</p>
    </div>
    
    <div class="page-header-right">
        <a href="{{ route('system-management.demo-game-categories.edit', $demo_game_category) }}" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('system-management.demo-game-categories.index') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
            <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 12h4M8 10v4M15 11h.01M18 13h.01" />
                <path d="M18 7H6a4 4 0 0 0-4 4v3a4 4 0 0 0 4 4h12a4 4 0 0 0 4-4v-3a4 4 0 0 0-4-4z" />
              </svg>
        </div>
        <div class="detail-header-info">
            <h3>{{ $demo_game_category->name }}</h3>
            <p>{{ $demo_game_category->description ?: 'N/A' }}</p>
        </div>
    </div>
    
    <div class="detail-body">
        <!-- Basic Information -->
        <div class="detail-section">
            <h4 class="detail-section-title">Basic Information</h4>
            <div class="detail-grid form-grid-3">
                <div class="detail-item">
                    <label>Title</label>
                    <span>{{ $demo_game_category->name }}</span>
                </div>
                <div class="detail-item">
                    <label>Slug</label>
                    <span class="badge position-top">{{ $demo_game_category->slug }}</span>
                </div>
                <div class="detail-item">
                    <label>Description</label>
                    <span>{{ $demo_game_category->description ?: 'N/A' }}</span>
                </div>
            </div>
        </div>

       
        <!-- Metadata -->
        <div class="detail-section">
            <h4 class="detail-section-title">Metadata</h4>
            <div class="detail-grid form-grid-3">
                <div class="detail-item">
                    <label>Created At</label>
                    <span 
                        class="datetime" 
                        data-iso="{{ $demo_game_category->created_at->toIso8601String() }}"
                    >
                        {{ $demo_game_category->created_at->format('F d, Y h:i A') }}
                    </span>
                </div>
                <div class="detail-item">
                    <label>Last Updated</label>
                    <span 
                        class="datetime" 
                        data-iso="{{ $demo_game_category->updated_at->toIso8601String() }}"
                    >
                        {{ $demo_game_category->updated_at->format('F d, Y h:i A') }}
                    </span>
                </div>
                <div class="detail-item">
                    <label>Category ID</label>
                    <span class="uuid-cell">{{ $demo_game_category->id }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
