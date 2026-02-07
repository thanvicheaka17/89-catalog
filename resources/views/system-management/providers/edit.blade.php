@extends('layouts.app')

@section('title', 'Edit Provider')

@section('breadcrumb')
    <a href="{{ route('system-management.providers.index') }}" class="breadcrumb-item">Providers</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Edit Provider</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit Provider</h2>
            <p>Update provider information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.providers.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('system-management.providers.update', $provider) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="name" class="form-label">Name <span class="form-required">*</span></label>
                    <input type="text" id="provider-name" name="name"
                        class="form-input @error('name') error @enderror" value="{{ old('name', $provider->name) }}"
                        placeholder="Enter name" required>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="slug" class="form-label">Slug <span class="form-required">*</span></label>
                    <input type="text" id="provider-slug" name="slug"
                        class="form-input @error('slug') error @enderror" value="{{ old('slug', $provider->slug) }}"
                        placeholder="Enter slug" required>
                    @error('slug')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description</label>
                    <textarea type="text" id="provider-description" name="description"
                        class="form-input @error('description') error @enderror" value="{{ old('description', $provider->description) }}"
                        placeholder="Enter description">{{ old('description', $provider->description) }}</textarea>
                    @error('description')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-group full-width">
                <label class="form-label">Image <span class="form-required">*</span></label>
                <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                <div class="image-upload-wrapper">
                    <div class="image-drop-zone {{ $provider->hasLogo() ? 'has-file' : '' }}" id="imageDropZone">
                        <input type="file" id="imageInput" name="image" class="image-file-input"
                            accept="image/jpeg,image/png,image/gif,image/webp" required>
                        <div class="drop-zone-content" style="{{ $provider->hasLogo() ? 'display: none;' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="drop-zone-icon">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                            </svg>
                            <span class="drop-zone-text">Drag & drop an image here</span>
                            <span class="drop-zone-subtext">or click to browse</span>
                            <span class="drop-zone-hint">JPEG, PNG, GIF, WebP • Max 10MB</span>
                        </div>
                        <div class="image-preview {{ $provider->hasLogo() ? 'has-image' : '' }}" id="imagePreview">
                            @if ($provider->hasLogo())
                                <div class="image-preview-item">
                                    <img src="{{ $provider->getLogoUrl() }}" alt="Current provider image">
                                    <div class="image-preview-info">
                                        <span class="image-name">Current Image</span>
                                        <span class="image-size">Uploaded</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="remove-image-btn" id="removeImage"
                            style="{{ $provider->hasLogo() ? 'display: flex;' : 'display: none;' }}"
                            title="Remove image">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="image-error" id="imageError" style="display: none;"></div>
                </div>
                @error('logo')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Update
                </button>
                <a href="{{ route('system-management.providers.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
