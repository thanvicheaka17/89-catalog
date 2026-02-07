@extends('layouts.app')

@section('title', 'Create Demo Game')

@section('breadcrumb')
    <a href="{{ route('game-management.demo-games.index') }}" class="breadcrumb-item">Demo Games</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Create Demo Game</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Create Demo Game</h2>
            <p>Add a new demo game to the system</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('game-management.demo-games.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('game-management.demo-games.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="title" class="form-label">Title <span class="form-required">*</span></label>
                    <input type="text" id="category-name" name="title"
                        class="form-input @error('title') error @enderror" value="{{ old('title') }}"
                        placeholder="Enter title" required>
                    @error('title')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_demo" class="form-label">Demo <span class="form-required">*</span></label>
                    <select id="is_demo" name="is_demo" class="form-input @error('is_demo') error @enderror" required>
                        <option value="1" {{ old('is_demo', '1') === '1' ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('is_demo') === '0' ? 'selected' : '' }}>No</option>
                    </select>
                    @error('is_demo')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="slug" class="form-label">Slug <span class="form-required">*</span></label>
                    <input type="text" id="category-slug" name="slug"
                        class="form-input @error('slug') error @enderror" value="{{ old('slug') }}"
                        placeholder="Enter slug" required>
                    <span class="form-hint">The slug is the URL-friendly version of the title. It will be used to generate
                        the URL for the demo game.</span>
                    @error('slug')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="url" class="form-label">URL <span class="form-required">*</span></label>
                    <input type="url" id="url" name="url" class="form-input @error('url') error @enderror"
                        value="{{ old('url') }}" placeholder="Enter URL">
                    @error('url')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">The URL is the link to the demo game. It will be used to display the demo game
                        in the website.</span>
                </div>
            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group full-width">
                    <label class="form-label">Image <span class="form-required">*</span></label>
                    <div class="image-upload-wrapper">
                        <div class="image-drop-zone" id="imageDropZone">
                            <input type="file" id="imageInput" name="image" class="image-file-input"
                                accept="image/jpeg,image/png,image/gif,image/webp" required>
                            <div class="drop-zone-content">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="drop-zone-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="drop-zone-text">Drag & drop an image here</span>
                                <span class="drop-zone-subtext">or click to browse</span>
                                <span class="drop-zone-hint">JPEG, PNG, GIF, WebP • Max 10MB</span>
                            </div>
                            <div class="image-preview" id="imagePreview"></div>
                            <button type="button" class="remove-image-btn" id="removeImage" style="display: none;"
                                title="Remove image">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor">
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
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description <span
                            class="form-optional">(Optional)</span></label>
                    <textarea id="description" name="description" class="form-input @error('description') error @enderror"
                        value="{{ old('description') }}" placeholder="Enter description"></textarea>
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
                    Create
                </button>
                <a href="{{ route('game-management.demo-games.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
