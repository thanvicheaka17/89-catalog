@extends('layouts.app')

@section('title', 'Edit Event')

@section('breadcrumb')
    <a href="{{ route('events.index') }}" class="breadcrumb-item">Events</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Edit Event</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit Event</h2>
            <p>Edit the event details</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('events.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Title <span class="form-required">*</span></label>
                    <input type="text" id="title" name="title" class="form-input @error('title') error @enderror"
                        value="{{ old('title', $event->title) }}" placeholder="Enter title" required>
                    @error('title')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Status <span class="form-required">*</span></label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror" required>
                        <option value="1" {{ old('is_active', $event->is_active) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $event->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Start At <span class="form-required">*</span></label>
                    <input type="date" id="start_at" name="start_at" class="form-input @error('start_at') error @enderror"
                        value="{{ old('start_at', $event->start_at?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" placeholder="Enter start at" required>
                    @error('start_at')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">End At <span class="form-required">*</span></label>
                    <input type="date" id="end_at" name="end_at" class="form-input @error('end_at') error @enderror"
                        value="{{ old('end_at', $event->end_at?->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d')) }}" placeholder="Enter end at" required>
                    @error('end_at')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group full-width">
                    <label class="form-label">Description <span class="form-optional">(Optional)</span></label>
                    <textarea id="description" name="description" class="form-input @error('description') error @enderror"
                        placeholder="Enter description">{{ old('description', $event->description) }}</textarea>
                    @error('description')
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
                <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
