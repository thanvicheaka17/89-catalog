@extends('layouts.app')

@section('title', 'Edit Demo Game Category')

@section('breadcrumb')
    <a href="{{ route('system-management.demo-game-categories.index') }}" class="breadcrumb-item">Demo Game Categories</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Edit Demo Game Category</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Edit Demo Game Category</h2>
            <p>Update category information</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.demo-game-categories.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('system-management.demo-game-categories.update', $demo_game_category) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="name" class="form-label">Name <span class="form-required">*</span></label>
                    <input type="text" id="category-name" name="name" class="form-input @error('name') error @enderror"
                        value="{{ old('name', $demo_game_category->name) }}" placeholder="Enter name" required>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="slug" class="form-label">Slug <span class="form-required">*</span></label>
                    <input type="text" id="category-slug" name="slug" class="form-input @error('slug') error @enderror"
                        value="{{ old('slug', $demo_game_category->slug) }}" placeholder="Enter slug" required>
                    @error('slug')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <span class="form-hint">The slug is used to identify the category in the URL</span>
                </div>
            </div>
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="description" class="form-label">Description <span
                            class="form-optional">(Optional)</span></label>
                    <textarea id="description" name="description" class="form-input @error('description') error @enderror"
                         placeholder="Enter description">{{ old('description', $demo_game_category->description) }}</textarea>
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
                <a href="{{ route('system-management.demo-game-categories.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
