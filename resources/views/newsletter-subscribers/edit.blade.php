@extends('layouts.app')

@section('title', 'Edit Newsletter Subscriber')

@section('breadcrumb')
    <a href="{{ route('newsletter-subscribers.index') }}" class="breadcrumb-item">Newsletter Subscribers</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Edit Newsletter Subscriber</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit Newsletter Subscriber</h2>
            <p>Edit a newsletter subscriber</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('newsletter-subscribers.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('newsletter-subscribers.update', $newsletterSubscriber) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Email <span class="form-required">*</span></label>
                    <input type="email" id="email" name="email" class="form-input @error('email') error @enderror"
                        value="{{ $newsletterSubscriber->email }}" placeholder="Enter email" required>
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span class="form-required">*</span></label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror" required>
                        <option value="1" {{ $newsletterSubscriber->is_active == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ $newsletterSubscriber->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Update
                </button>
                <a href="{{ route('newsletter-subscribers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
