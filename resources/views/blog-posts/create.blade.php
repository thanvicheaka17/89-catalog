@extends('layouts.app')

@section('title', 'Create Blog Post')

@section('breadcrumb')
    <a href="{{ route('blog-posts.index') }}" class="breadcrumb-item">Blog Posts</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Create Blog Post</span>
@endsection

@section('content')
<!-- Flash Messages -->
@if(session('success'))
    <div class="alert alert-success" data-auto-dismiss="5000">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
        </svg>
        <span class="alert-content">{{ session('success') }}</span>
        <button type="button" class="alert-close" onclick="closeAlert(this)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error" data-auto-dismiss="5000">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
        </svg>
        <span class="alert-content">{{ session('error') }}</span>
        <button type="button" class="alert-close" onclick="closeAlert(this)">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

<div class="form-card">
    <div class="form-header">
        <h3>Create New Blog Post</h3>
        <p>Fill in the details for your new blog post</p>
    </div>

    <form action="{{ route('blog-posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-grid">
            <!-- Title -->
            <div class="form-group">
                <label for="title" class="required">Title</label>
                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
                @error('title')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>


            <!-- Author Name -->
            <div class="form-group">
                <label for="author_name" class="required">Author Name</label>
                <input type="text" id="author_name" name="author_name" class="form-control" value="{{ old('author_name') }}" required>
                @error('author_name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Author Role -->
            <div class="form-group">
                <label for="author_role">Author Role</label>
                <input type="text" id="author_role" name="author_role" class="form-control" value="{{ old('author_role') }}" placeholder="e.g., Gaming Analyst, Strategy Expert">
                @error('author_role')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tags -->
            <div class="form-group">
                <label for="tags">Tags</label>
                <input type="text" id="tags" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="Comma separated tags (e.g., RTP, strategy, casino)">
                <small class="form-help">Separate tags with commas</small>
                @error('tags')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Read Time -->
            <div class="form-group">
                <label for="read_time">Read Time (minutes)</label>
                <input type="number" id="read_time" name="read_time" class="form-control" value="{{ old('read_time', 5) }}" min="1" max="60">
                @error('read_time')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Excerpt -->
        <div class="form-group">
            <label for="excerpt">Excerpt</label>
            <textarea id="excerpt" name="excerpt" class="form-control" rows="3" maxlength="500" placeholder="Brief summary of the blog post...">{{ old('excerpt') }}</textarea>
            <small class="form-help">Maximum 500 characters</small>
            @error('excerpt')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Content -->
        <div class="form-group">
            <label for="content" class="required">Content</label>
            <textarea id="content" name="content" class="form-control" rows="15" required>{{ old('content') }}</textarea>
            @error('content')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Featured Image -->
        <div class="form-group">
            <label for="featured_image">Featured Image</label>
            <input type="file" id="featured_image" name="featured_image" class="form-control" accept="image/*">
            <small class="form-help">Accepted formats: JPEG, PNG, GIF, WebP. Max size: 2MB</small>
            @error('featured_image')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <!-- Publishing Options -->
        <div class="form-section">
            <h4>Publishing Options</h4>
            <div class="form-grid">
                <!-- Is Featured -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        Featured Post
                    </label>
                </div>

                <!-- Is Published -->
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', true) ? 'checked' : '' }}>
                        <span class="checkmark"></span>
                        Publish Immediately
                    </label>
                </div>

                <!-- Published At -->
                <div class="form-group" id="published_at_group" style="{{ old('is_published', true) ? 'display: none;' : '' }}">
                    <label for="published_at">Publish Date & Time</label>
                    <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="{{ old('published_at') }}">
                    @error('published_at')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="{{ route('blog-posts.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Blog Post</button>
        </div>
    </form>
</div>

<script>
document.getElementById('is_published').addEventListener('change', function() {
    const publishedAtGroup = document.getElementById('published_at_group');
    if (this.checked) {
        publishedAtGroup.style.display = 'none';
    } else {
        publishedAtGroup.style.display = 'block';
    }
});
</script>
@endsection