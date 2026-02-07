@extends('layouts.app')

@section('title', 'Edit Site Setting')

@section('breadcrumb')
    <a href="{{ route('system-management.site-settings.index') }}" class="breadcrumb-item">Site Settings</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Edit Site Setting</span>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="alert-content">{{ session('success') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit Site Setting</h2>
            <p>Edit site setting information</p>
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
        <form action="{{ route('system-management.site-settings.update', $siteSetting) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label class="form-label">Key <span class="form-required">*</span></label>
                    <input type="text" id="key" name="key" class="form-input @error('key') error @enderror"
                        value="{{ old('key', $siteSetting->key) }}" placeholder="Enter key" disabled readonly required>
                    <input type="hidden" name="key" value="{{ $siteSetting->key }}">
                    <small class="form-hint text-muted">Key cannot be changed as it must remain unique.</small>
                    @error('key')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Group <span class="form-required">*</span></label>
                    <select id="group" name="group" class="form-input @error('group') error @enderror" required>
                        <option value="general" {{ old('group', $siteSetting->group) === 'general' ? 'selected' : '' }}>
                            General</option>
                        <option value="contact" {{ old('group', $siteSetting->group) === 'contact' ? 'selected' : '' }}>
                            Contact</option>
                        <option value="social" {{ old('group', $siteSetting->group) === 'social' ? 'selected' : '' }}>Social
                            Media</option>
                        <option value="analytics" {{ old('group', $siteSetting->group) === 'analytics' ? 'selected' : '' }}>
                            Analytics</option>
                        <option value="seo" {{ old('group', $siteSetting->group) === 'seo' ? 'selected' : '' }}>SEO
                        </option>
                        <option value="footer" {{ old('group', $siteSetting->group) === 'footer' ? 'selected' : '' }}>
                            Footer</option>
                        <option value="email" {{ old('group', $siteSetting->group) === 'email' ? 'selected' : '' }}>Email
                        </option>
                        <option value="global" {{ old('group', $siteSetting->group) === 'global' ? 'selected' : '' }}>
                            Global</option>
                        <option value="tools" {{ old('group', $siteSetting->group) === 'tools' ? 'selected' : '' }}>Tools
                        </option>
                        <option value="other" {{ old('group', $siteSetting->group) === 'other' ? 'selected' : '' }}>Other
                        </option>
                    </select>
                    @error('group')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Value <span class="form-required">*</span></label>

                    @if ($siteSetting->key === 'available_avatars')
                        <!-- Special interface for avatar gallery management -->
                        <div class="avatar-gallery-manager">
                            <div class="avatar-upload-section">
                                <label class="form-label">Upload New Avatars</label>
                                <input type="file" id="avatar_files" name="avatar_files[]" class="form-input" multiple
                                    accept="image/*" onchange="previewFiles(this)">
                                <small class="form-hint">Select multiple image files (JPEG, PNG, GIF, WebP, SVG). Max 2MB
                                    each.</small>
                                <div id="file-preview" class="file-preview" style="display: none;">
                                    <h4>Files to upload:</h4>
                                    <ul id="file-list"></ul>
                                </div>
                            </div>

                            <div class="avatar-gallery-display">
                                <div class="avatar-display-grid">
                                    @if (is_array($siteSetting->value) && !empty($siteSetting->value))
                                        @foreach ($siteSetting->value as $avatar)
                                            <div class="avatar-display-item">
                                                <div class="avatar-image-container">
                                                    <img src="{{ url($avatar) }}" alt="Avatar"
                                                        class="avatar-display-image">
                                                    <button type="button" class="avatar-remove-btn"
                                                        onclick="toggleAvatarSelection('{{ $avatar }}', this)"
                                                        data-avatar="{{ $avatar }}" title="Remove avatar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" fill="currentColor" class="bi bi-x"
                                                            viewBox="0 0 16 16">
                                                            <path
                                                                d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 1 1-.708-.708L7.293 8 2.146 2.854Z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="avatar-info">
                                                    <div class="avatar-path">{{ basename($avatar) }}</div>
                                                </div>
                                                <!-- Hidden input to track which avatars should be kept server-side -->
                                                <input type="hidden" name="keep_avatars[]" value="{{ $avatar }}">
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                @if (!is_array($siteSetting->value) || empty($siteSetting->value))
                                    <div class="empty-state" id="avatar-empty-state">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 6h.008v.008H6V6Z" />
                                        </svg>
                                        <h5 class="text-muted">No Avatars Configured</h5>
                                        <p class="text-muted">Upload new avatars above to get started.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @else
                        <!-- Default textarea for other settings -->
                        <textarea id="value" name="value" class="form-input form-textarea @error('value') error @enderror"
                            placeholder="Enter value (can be JSON for complex data)" rows="6" required>{{ old('value', is_array($siteSetting->value) ? json_encode($siteSetting->value, JSON_PRETTY_PRINT) : $siteSetting->value) }}</textarea>
                        <small class="form-hint">For simple values, enter text. For complex data, use JSON format.</small>
                    @endif

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
                    Update
                </button>
                <a href="{{ route('system-management.site-settings.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

