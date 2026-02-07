@extends('layouts.app')

@section('title', 'Edit Testimonial')

@section('breadcrumb')
    <a href="{{ route('testimonials.index') }}" class="breadcrumb-item">Testimonials</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Edit Testimonial</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit Testimonial</h2>
            <p>Edit the testimonial</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('testimonials.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @csrf

            <!-- Avatar Upload -->
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Avatar</label>
                <input type="hidden" name="remove_avatar" id="removeAvatarFlag" value="0">
                <div class="avatar-upload-container">
                    <div class="avatar-preview-wrapper">
                        <img src="{{ $testimonial->getAvatarUrl() }}" alt="Avatar" class="avatar-preview-img"
                            id="avatarPreviewImg">
                        <button type="button" class="avatar-remove-btn" id="removeAvatar"
                            style="{{ $testimonial->hasAvatar() ? 'display: flex;' : 'display: none;' }}"
                            title="Remove avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="avatar-upload-actions">
                        <input type="file" id="avatarInput" name="avatar" class="avatar-file-input"
                            accept="image/jpeg,image/png,image/gif,image/webp,image/svg+xml">
                        <button type="button" class="btn btn-secondary btn-sm" id="chooseAvatarBtn">Choose File</button>
                        <span class="avatar-hint">JPG, PNG, GIF, WebP or SVG. Max 2MB.</span>
                    </div>
                </div>
                @error('avatar')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-grid form-grid-3">
                <div class="form-group">
                    <label for="name" class="form-label">Name <span class="form-required">*</span></label>
                    <input type="text" id="user-name" name="user_name"
                        class="form-input @error('user_name') error @enderror"
                        value="{{ old('user_name', $testimonial->user_name) }}" placeholder="Enter name" required>
                    @error('user_name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="user-role" class="form-label">Role <span class="form-required">*</span></label>
                    <input type="text" id="user-role" name="user_role"
                        class="form-input @error('user_role') error @enderror"
                        value="{{ old('user_role', $testimonial->user_role) }}" placeholder="Enter role" required>
                    @error('slug')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">The role is used to identify the user in the URL</span>
                    @error('slug')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="rating" class="form-label">Rating <span class="form-required">*</span></label>
                    <input type="number" id="rating" name="rating" class="form-input @error('rating') error @enderror"
                        value="{{ old('rating', $testimonial->rating) }}" placeholder="Enter rating" required>
                    @error('rating')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    {{-- <label for="is_featured" class="form-label">Featured <span class="form-required">*</span></label> --}} 
                    <select id="is_featured" name="is_featured" class="form-input @error('is_featured') error @enderror"
                        required hidden>
                        <option value="1"
                            {{ old('is_featured', $testimonial->is_featured ? '1' : '0') === '1' ? 'selected' : '' }}>Yes
                        </option>
                        <option value="0"
                            {{ old('is_featured', $testimonial->is_featured ? '1' : '0') === '0' ? 'selected' : '' }}>No
                        </option>
                    </select>
                    @error('is_featured')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    {{-- <label for="is_active" class="form-label">Active <span class="form-required">*</span></label> --}}
                    <select id="is_active" name="is_active" class="form-input @error('is_active') error @enderror"
                        required hidden>
                        <option value="1"
                            {{ old('is_active', $testimonial->is_active ? '1' : '0') === '1' ? 'selected' : '' }}>Active
                        </option>
                        <option value="0"
                            {{ old('is_active', $testimonial->is_active ? '1' : '0') === '0' ? 'selected' : '' }}>Inactive
                        </option>
                    </select>
                    @error('is_active')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="message" class="form-label">Message <span class="form-required">*</span></label>
                    <textarea id="message" name="message" class="form-input @error('message') error @enderror"
                        placeholder="Enter message">{{ old('message', $testimonial->message) }}</textarea>
                    @error('message')
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
                <a href="{{ route('testimonials.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
