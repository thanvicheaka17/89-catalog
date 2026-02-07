@extends('layouts.app')

@section('title', 'Edit Banner')

@section('breadcrumb')
    <a href="{{ route('banners.index') }}" class="breadcrumb-item">Banners</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Edit Banner</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit Banner</h2>
            <p>Edit banner information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('banners.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <input type="hidden" name="visibility" id="visibility" value="{{ old('visibility', $banner->visibility ?? 'public') }}">
            <input type="hidden" name="start_at" id="start_at" value="{{ old('start_at', $banner->start_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s')) }}">
            <input type="hidden" name="end_at" id="end_at" value="{{ old('end_at', $banner->end_at?->format('Y-m-d H:i:s') ?? now()->addDays(30)->format('Y-m-d H:i:s')) }}">
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="title" class="form-label">Title <span class="form-required">*</span></label>
                    <input type="text" id="title" name="title" class="form-input @error('title') error @enderror"
                        value="{{ old('title', $banner->title) }}" placeholder="Enter title" required>
                    @error('title')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="subtitle" class="form-label">Subtitle <span class="form-optional">(Optional)</span></label>
                    <input type="text" id="subtitle" name="subtitle"
                        class="form-input @error('subtitle') error @enderror" value="{{ old('subtitle', $banner->subtitle) }}"
                        placeholder="Enter subtitle">
                    @error('subtitle')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-grid form-grid-3">
                <div class="form-group">
                    <label for="link_url" class="form-label">Link URL <span class="form-optional">(Optional)</span></label>
                    <input type="text" id="link_url" name="link_url"
                        class="form-input @error('link_url') error @enderror" value="{{ old('link_url', $banner->link_url) }}"
                        placeholder="Enter link URL">
                    @error('link_url')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="priority" class="form-label">Priority <span class="form-required">*</span></label>
                    <input type="number" id="priority" name="priority"
                        class="form-input @error('priority') error @enderror" value="{{ old('priority', $banner->priority) }}"
                        placeholder="Enter priority" min="0" max="100" required>
                    @error('priority')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="is_active" class="form-label">Status <span class="form-required">*</span></label>
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror" required>
                        <option value="1" {{ old('is_active', $banner->is_active) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $banner->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- <div class="form-grid">
                <div class="form-group full-width">
                    <label for="meta" class="form-label">Meta <span class="form-optional">(Optional)</span> </label>
                    <textarea id="meta" name="meta" class="form-input form-textarea @error('meta') error @enderror" rows="3">{{ old('meta', is_array($banner->meta) ? json_encode($banner->meta) : $banner->meta) }}</textarea>
                    @error('meta')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div> --}}

            <div class="form-grid">
                <div class="form-group full-width">
                    <label class="form-label">Image <span class="form-required">*</span></label>
                    <input type="hidden" name="remove_image" id="removeImageFlag" value="0">
                    <div class="image-upload-wrapper">
                        <div class="image-drop-zone {{ $banner->hasImage() ? 'has-file' : '' }}" id="imageDropZone">
                    <input 
                                type="file" 
                                id="imageInput" 
                                name="image" 
                                class="image-file-input"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                            >
                            <div class="drop-zone-content" style="{{ $banner->hasImage() ? 'display: none;' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="drop-zone-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="drop-zone-text">Drag & drop an image here</span>
                                <span class="drop-zone-subtext">or click to browse</span>
                                <span class="drop-zone-hint">JPEG, PNG, GIF, WebP • Max 10MB</span>
                            </div>
                            <div class="image-preview {{ $banner->hasImage() ? 'has-image' : '' }}" id="imagePreview">
                                @if($banner->hasImage())
                                    <div class="image-preview-item">
                                        <img src="{{ $banner->getImageUrl() }}" alt="Current banner image">
                                        <div class="image-preview-info">
                                            <span class="image-name">Current Image</span>
                                            <span class="image-size">Uploaded</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="remove-image-btn" id="removeImage" style="{{ $banner->hasImage() ? 'display: flex;' : 'display: none;' }}" title="Remove image">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="image-error" id="imageError" style="display: none;"></div>
                    </div>
                    @error('image')
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
                <a href="{{ route('banners.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
