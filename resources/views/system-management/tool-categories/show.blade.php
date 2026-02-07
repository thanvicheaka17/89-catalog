@extends('layouts.app')

@section('title', 'View Tool Category')

@section('breadcrumb')
    <a href="{{ route('system-management.tool-categories.index') }}" class="breadcrumb-item">Tool Categories</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($toolCategory->name, 30) }}</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Tool Category Details</h2>
            <p>View tool category information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.tool-categories.edit', $toolCategory) }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                Edit
            </a>
            <a href="{{ route('system-management.tool-categories.index') }}" class="btn btn-secondary">
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
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                  </svg>
            </div>
            <div class="detail-header-info">
                <h3>{{ $toolCategory->name }}</h3>
                <p>{{ $toolCategory->description ?: 'N/A' }}</p>
            </div>
        </div>

        <div class="detail-body">
            <!-- Basic Information -->
            <div class="detail-section">
                <h4 class="detail-section-title">Basic Information</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Title</label>
                        <span>{{ $toolCategory->name }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Slug</label>
                        <span class="badge position-top">{{ $toolCategory->slug }}</span>
                    </div>
                    <div class="detail-item">
                        <label>Description</label>
                        <span>{{ $toolCategory->description ?: 'N/A' }}</span>
                    </div>
                </div>
            </div>


            <!-- Metadata -->
            <div class="detail-section">
                <h4 class="detail-section-title">Metadata</h4>
                <div class="detail-grid form-grid-3">
                    <div class="detail-item">
                        <label>Created At</label>
                        <span class="datetime" data-iso="{{ $toolCategory->created_at->toIso8601String() }}">
                            {{ $toolCategory->created_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Last Updated</label>
                        <span class="datetime" data-iso="{{ $toolCategory->updated_at->toIso8601String() }}">
                            {{ $toolCategory->updated_at->format('F d, Y h:i A') }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <label>Category ID</label>
                        <span class="uuid-cell">{{ $toolCategory->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
