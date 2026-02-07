@extends('layouts.app')

@section('title', 'Create Site Setting')

@section('breadcrumb')
    <a href="{{ route('system-management.site-settings.index') }}" class="breadcrumb-item">Site Settings</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Create Site Setting</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Create Site Setting</h2>
            <p>Add a new site setting to the system</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.site-settings.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('system-management.site-settings.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Key <span class="form-required">*</span></label>
                    <input type="text" id="key" name="key" class="form-input @error('key') error @enderror"
                        value="{{ old('key') }}" placeholder="Enter key" required>
                    @error('key')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Group <span class="form-required">*</span></label>
                    <select id="group" name="group" class="form-input @error('group') error @enderror" required>
                        <option value="general" {{ old('group') === 'general' ? 'selected' : '' }}>General</option>
                        <option value="contact" {{ old('group') === 'contact' ? 'selected' : '' }}>Contact</option>
                        <option value="social" {{ old('group') === 'social' ? 'selected' : '' }}>Social Media</option>
                        <option value="analytics" {{ old('group') === 'analytics' ? 'selected' : '' }}>Analytics</option>
                        <option value="seo" {{ old('group') === 'seo' ? 'selected' : '' }}>SEO</option>
                        <option value="footer" {{ old('group') === 'footer' ? 'selected' : '' }}>Footer</option>
                        <option value="global" {{ old('group') === 'global' ? 'selected' : '' }}>Global</option>
                        <option value="tools" {{ old('group') === 'tools' ? 'selected' : '' }}>Tools
                        </option>
                        <option value="other" {{ old('group') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('group')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-grid">

                <div class="form-group full-width">
                    <label class="form-label">Value <span class="form-required">*</span></label>
                    <textarea id="value" name="value" class="form-input form-textarea @error('value') error @enderror"
                        placeholder="Enter value (can be JSON for complex data)" rows="4" required>{{ old('value') }}</textarea>
                    <small class="form-hint">For simple values, enter text. For complex data, use JSON format.</small>
                    @error('value')
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
                    Create
                </button>
                <a href="{{ route('system-management.site-settings.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
