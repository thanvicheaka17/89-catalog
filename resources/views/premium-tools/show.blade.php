@extends('layouts.app')

@section('title', 'View Premium Tool')

@section('breadcrumb')
    <a href="{{ route('premium-tools.index') }}" class="breadcrumb-item">Premium Tools</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">{{ Str::limit($premiumTool->name, 30) }}</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Premium Tool Details</h2>
        <p>View premium tool information</p>
    </div>
    
    <div class="page-header-right">
        <a href="{{ route('premium-tools.edit', $premiumTool) }}" class="btn btn-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            Edit
        </a>
        <a href="{{ route('premium-tools.index') }}" class="btn btn-secondary">
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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.797.93.391.158.866.145 1.247-.147l.738-.563c.444-.338 1.077-.277 1.452.14l.772.857c.375.416.354 1.05-.045 1.442l-.659.648c-.326.321-.428.81-.259 1.232.17.421.57.702 1.023.702h.933c.59 0 1.074.467 1.11 1.056l.046 1.011c.036.59-.413 1.085-1.003 1.125l-.93.064c-.452.03-.843.344-1.002.766-.158.423-.043.91.288 1.233l.676.657c.412.4.453 1.036.096 1.48l-.738.913c-.357.443-.984.538-1.45.221l-.79-.537c-.383-.26-.884-.263-1.27-.008-.387.255-.635.698-.635 1.168l.006.942c.004.59-.453 1.077-1.042 1.1l-1.011.04c-.59.023-1.1-.425-1.126-1.015l-.04-.94c-.018-.47-.27-.899-.665-1.144-.396-.245-.893-.232-1.276.035l-.794.553c-.462.322-1.095.234-1.457-.204l-.744-.9c-.361-.439-.328-1.074.077-1.474l.685-.678c.334-.33.453-.82.302-1.25-.152-.43-.55-.751-1.008-.813l-.934-.127c-.585-.08-1.015-.596-.985-1.185l.052-1.01c.03-.59.522-1.05 1.11-1.05h.944c.459 0 .866-.273 1.04-.698.173-.424.08-.918-.238-1.25l-.64-.672c-.39-.408-.403-1.044-.027-1.468l.754-.852c.376-.425 1.012-.497 1.47-.168l.764.55c.38.273.88.293 1.28.053.398-.24.64-.674.616-1.14l-.048-.908z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
        </div>
        <div class="detail-header-info">
            <h3>{{ $premiumTool->title }}</h3>
            <p>{{ $premiumTool->description ?: 'N/A' }}</p>
        </div>
    </div>
    
    <div class="detail-body">
        <!-- Basic Information -->
        <div class="detail-section">
            <h4 class="detail-section-title">Basic Information</h4>
            <div class="detail-grid form-grid-3">
                <div class="detail-item">
                    <label>Title</label>
                    <span>{{ $premiumTool->title }}</span>
                </div>
                <div class="detail-item">
                    <label>Description</label>
                    <span>{{ $premiumTool->description ?: 'N/A' }}</span>
                </div>
                <div class="detail-item">
                    <label>Status</label>
                    <span class="badge {{ $premiumTool->getStatusBadgeClass() }}">
                        @if($premiumTool->getStatus() === 'active')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        @endif
                        {{ $premiumTool->getStatusDisplayName() }}
                    </span>
                </div>
            </div>
        </div>

       
        <!-- Metadata -->
        <div class="detail-section">
            <h4 class="detail-section-title">Metadata</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <label>Created At</label>
                    <span 
                        class="datetime" 
                        data-iso="{{ $premiumTool->created_at->toIso8601String() }}"
                    >
                        {{ $premiumTool->created_at->format('F d, Y h:i A') }}
                    </span>
                </div>
                <div class="detail-item">
                    <label>Last Updated</label>
                    <span 
                        class="datetime" 
                        data-iso="{{ $premiumTool->updated_at->toIso8601String() }}"
                    >
                        {{ $premiumTool->updated_at->format('F d, Y h:i A') }}
                    </span>
                </div>
                <div class="detail-item">
                    <label>Created By</label>
                    <span class="created-by">{{ $premiumTool->creator?->name ?? 'Unknown' }}</span>
                </div>
                <div class="detail-item">
                    <label>Premium Tool ID</label>
                    <span class="uuid-cell">{{ $premiumTool->id }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
