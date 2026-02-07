@extends('layouts.app')

@section('title', 'Create Promotion')

@section('breadcrumb')
    <a href="{{ route('promotions.index') }}" class="breadcrumb-item">Promotions</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Create Promotion</span>
@endsection

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h2>Create Promotion</h2>
        <p>Add a new promotion for your campaigns</p>
    </div>
    
    <div class="page-header-right">
        <a href="{{ route('promotions.index') }}" class="btn btn-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
            </svg>
            Back
        </a>
    </div>
</div>

<!-- Live Preview -->
<div class="promo-preview-wrapper">
    <h3 class="preview-title">Live Preview</h3>
    <div class="promo-preview" id="bannerPreview">
        <div class="promo-preview-content">
            <span class="promo-preview-text" id="previewTitle">Your promotion title here</span>
            <span class="promo-preview-message" id="previewMessage">Your promotion message will appear here</span>
        </div>
        <button class="promo-preview-button" id="previewButton">
            Learn More
        </button>
    </div>
</div>

<div class="form-card">
    <form action="{{ route('promotions.store') }}" method="POST" id="bannerForm" enctype="multipart/form-data">
        @csrf
        
        <!-- Content Section -->
        <div class="form-section">
            <h3 class="form-section-title">Content</h3>
            
            <!-- Banner Title, Button Text, Button URL in same row -->
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="title" class="form-label">Title <span class="form-required">*</span></label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="form-input @error('title') error @enderror" 
                        value="{{ old('title') }}"
                        placeholder="e.g., Holiday Sale - 50% Off!"
                        required
                    >
                    @error('title')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="position" class="form-label">Position <span class="form-required">*</span></label>
                    <select 
                        id="position" 
                        name="position" 
                        class="form-input @error('position') error @enderror" 
                        required
                    >
                        <option value="top" {{ old('position') === 'top' ? 'selected' : '' }}>Top</option>
                        <option value="bottom" {{ old('position') === 'bottom' ? 'selected' : '' }}>Bottom</option>
                        <option value="popup" {{ old('position') === 'popup' ? 'selected' : '' }}>Popup</option>
                    </select>
                    @error('position')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="button_text" class="form-label">Button Text <span class="form-optional">(Optional)</span></label>
                    <input 
                        type="text" 
                        id="button_text" 
                        name="button_text" 
                        class="form-input @error('button_text') error @enderror" 
                        value="{{ old('button_text', 'Learn More') }}"
                        placeholder="e.g., Shop Now"
                    >
                    @error('button_text')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="button_url" class="form-label">Button URL <span class="form-optional">(Optional)</span></label>
                    <input 
                        type="url" 
                        id="button_url" 
                        name="button_url" 
                        class="form-input @error('button_url') error @enderror" 
                        value="{{ old('button_url') }}"
                        placeholder="https://example.com/sale"
                    >
                    @error('button_url')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <!-- Message -->
            <div class="form-grid">
                <div class="form-group full-width">
                    <label for="message" class="form-label">Message <span class="form-optional">(Optional)</span></label>
                    <textarea 
                        id="message" 
                        name="message" 
                        class="form-input form-textarea @error('message') error @enderror" 
                        placeholder="Enter your promotion message..."
                        rows="3"
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group full-width">
                    <label class="form-label">Image <span class="form-optional">(Optional)</span></label>
                    <div class="image-upload-wrapper">
                        <div class="image-drop-zone" id="imageDropZone">
                    <input 
                                type="file" 
                                id="imageInput" 
                                name="image" 
                                class="image-file-input"
                                accept="image/jpeg,image/png,image/gif,image/webp"
                            >
                            <div class="drop-zone-content">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="drop-zone-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="drop-zone-text">Drag & drop an image here</span>
                                <span class="drop-zone-subtext">or click to browse</span>
                                <span class="drop-zone-hint">JPEG, PNG, GIF, WebP • Max 10MB</span>
                            </div>
                            <div class="image-preview" id="imagePreview"></div>
                            <button type="button" class="remove-image-btn" id="removeImage" style="display: none;" title="Remove image">
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
        </div>
        
        <!-- Appearance Fieldset -->
        <fieldset class="form-fieldset">
            <legend>Appearance</legend>
            
            <div class="form-grid form-grid-2 margin-bottom-0">
                <!-- Background Color with Gradient -->
                <div class="form-group">
                    <label class="form-label">Background Color <span class="form-required">*</span></label>
                    <div class="gradient-picker" id="bgGradientPicker">
                        <div class="gradient-type-toggle">
                            <button type="button" class="gradient-type-btn active" data-type="solid" data-target="background">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                                </svg>
                                Solid
                            </button>
                            <button type="button" class="gradient-type-btn" data-type="gradient" data-target="background">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                                Gradient
                            </button>
                        </div>
                        
                        <div class="gradient-colors">
                            <div class="color-input-wrapper">
                                <input type="color" id="background_color" name="background_color" class="form-color-input" value="{{ old('background_color', '#0f172a') }}">
                                <input type="text" class="form-input form-color-text" value="{{ old('background_color', '#0f172a') }}" data-color-for="background_color" pattern="^#[0-9A-Fa-f]{6}$">
                            </div>
                            <div class="gradient-color-2" style="display: none;">
                                <span class="gradient-arrow">→</span>
                    <div class="color-input-wrapper">
                                    <input type="color" id="background_color_2" name="background_color_2" class="form-color-input" value="{{ old('background_color_2', '#1e3a5f') }}">
                                    <input type="text" class="form-input form-color-text" value="{{ old('background_color_2', '#1e3a5f') }}" data-color-for="background_color_2" pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                            </div>
                        </div>
                        
                        <div class="gradient-direction" style="display: none;">
                            <label class="form-label-sm">Direction</label>
                            <select name="background_gradient_direction" id="background_gradient_direction" class="form-input form-input-sm">
                                <option value="to right" {{ old('background_gradient_direction') == 'to right' ? 'selected' : '' }}>Left → Right</option>
                                <option value="to left" {{ old('background_gradient_direction') == 'to left' ? 'selected' : '' }}>Right → Left</option>
                                <option value="to bottom" {{ old('background_gradient_direction') == 'to bottom' ? 'selected' : '' }}>Top → Bottom</option>
                                <option value="to top" {{ old('background_gradient_direction') == 'to top' ? 'selected' : '' }}>Bottom → Top</option>
                                <option value="to bottom right" {{ old('background_gradient_direction') == 'to bottom right' ? 'selected' : '' }}>Diagonal ↘</option>
                                <option value="to bottom left" {{ old('background_gradient_direction') == 'to bottom left' ? 'selected' : '' }}>Diagonal ↙</option>
                            </select>
                        </div>
                        
                        <div class="gradient-preview-box" id="bgGradientPreview" style="background: {{ old('background_color', '#0f172a') }};"></div>
                    </div>
                    <input type="hidden" name="background_gradient_type" id="background_gradient_type" value="{{ old('background_gradient_type', 'solid') }}">
                    @error('background_color')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Button Color with Gradient -->
                <div class="form-group">
                    <label class="form-label">Button Color <span class="form-required">*</span></label>
                    <div class="gradient-picker" id="btnGradientPicker">
                        <div class="gradient-type-toggle">
                            <button type="button" class="gradient-type-btn active" data-type="solid" data-target="button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 7.5A2.25 2.25 0 0 1 7.5 5.25h9a2.25 2.25 0 0 1 2.25 2.25v9a2.25 2.25 0 0 1-2.25 2.25h-9a2.25 2.25 0 0 1-2.25-2.25v-9Z" />
                                </svg>
                                Solid
                            </button>
                            <button type="button" class="gradient-type-btn" data-type="gradient" data-target="button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15m-10.875 0h15.75c.621 0 1.125-.504 1.125-1.125V5.625c0-.621-.504-1.125-1.125-1.125H4.125C3.504 4.5 3 5.004 3 5.625v12.75c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                                Gradient
                            </button>
                        </div>
                        
                        <div class="gradient-colors">
                            <div class="color-input-wrapper">
                                <input type="color" id="button_color" name="button_color" class="form-color-input" value="{{ old('button_color', '#f59e0b') }}">
                                <input type="text" class="form-input form-color-text" value="{{ old('button_color', '#f59e0b') }}" data-color-for="button_color" pattern="^#[0-9A-Fa-f]{6}$">
                            </div>
                            <div class="gradient-color-2" style="display: none;">
                                <span class="gradient-arrow">→</span>
                    <div class="color-input-wrapper">
                                    <input type="color" id="button_color_2" name="button_color_2" class="form-color-input" value="{{ old('button_color_2', '#ea580c') }}">
                                    <input type="text" class="form-input form-color-text" value="{{ old('button_color_2', '#ea580c') }}" data-color-for="button_color_2" pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                            </div>
                        </div>
                        
                        <div class="gradient-direction" style="display: none;">
                            <label class="form-label-sm">Direction</label>
                            <select name="button_gradient_direction" id="button_gradient_direction" class="form-input form-input-sm">
                                <option value="to right" {{ old('button_gradient_direction') == 'to right' ? 'selected' : '' }}>Left → Right</option>
                                <option value="to left" {{ old('button_gradient_direction') == 'to left' ? 'selected' : '' }}>Right → Left</option>
                                <option value="to bottom" {{ old('button_gradient_direction') == 'to bottom' ? 'selected' : '' }}>Top → Bottom</option>
                                <option value="to top" {{ old('button_gradient_direction') == 'to top' ? 'selected' : '' }}>Bottom → Top</option>
                            </select>
                        </div>
                        
                        <div class="gradient-preview-box" id="btnGradientPreview" style="background: {{ old('button_color', '#f59e0b') }};"></div>
                    </div>
                    <input type="hidden" name="button_gradient_type" id="button_gradient_type" value="{{ old('button_gradient_type', 'solid') }}">
                    @error('button_color')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Text Color -->
                <div class="form-group">
                    <label for="text_color" class="form-label">Text Color <span class="form-required">*</span></label>
                    <div class="color-input-wrapper">
                        <input type="color" id="text_color" name="text_color" class="form-color-input" value="{{ old('text_color', '#ffffff') }}">
                        <input type="text" class="form-input form-color-text" value="{{ old('text_color', '#ffffff') }}" data-color-for="text_color" pattern="^#[0-9A-Fa-f]{6}$">
                    </div>
                    @error('text_color')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Button Text Color -->
                <div class="form-group">
                    <label for="button_text_color" class="form-label">Button Text Color <span class="form-required">*</span></label>
                    <div class="color-input-wrapper">
                        <input type="color" id="button_text_color" name="button_text_color" class="form-color-input" value="{{ old('button_text_color', '#ffffff') }}">
                        <input type="text" class="form-input form-color-text" value="{{ old('button_text_color', '#ffffff') }}" data-color-for="button_text_color" pattern="^#[0-9A-Fa-f]{6}$">
                    </div>
                    @error('button_text_color')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </fieldset>
        
        <!-- Schedule Fieldset -->
        <fieldset class="form-fieldset">
            <legend>Schedule</legend>
            
            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="start_date" class="form-label">Start Date & Time <span class="form-optional">(Optional)</span></label>
                    <input 
                        type="datetime-local" 
                        id="start_date" 
                        name="start_date" 
                        class="form-input @error('start_date') error @enderror" 
                        value="{{ old('start_date') }}"
                    >
                    <span class="form-hint">Leave empty for immediate activation</span>
                    @error('start_date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="end_date" class="form-label">End Date & Time <span class="form-optional">(Optional)</span></label>
                    <input 
                        type="datetime-local" 
                        id="end_date" 
                        name="end_date" 
                        class="form-input @error('end_date') error @enderror" 
                        value="{{ old('end_date') }}"
                    >
                    <span class="form-hint">Leave empty for no expiration</span>
                    @error('end_date')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                </div>
                
            <div class="form-grid form-grid-2 margin-bottom-0">
                <div class="form-group">
                    <label for="is_active" class="form-label">Status <span class="form-required">*</span></label>
                    <select 
                        id="is_active" 
                        name="is_active" 
                        class="form-input @error('is_active') error @enderror"
                        required
                    >
                        <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <span class="form-hint">Inactive promotions won't be displayed to users</span>
                    @error('is_active')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="priority" class="form-label">Priority <span class="form-required">*</span></label>
                    <input 
                        type="number" 
                        id="priority" 
                        name="priority" 
                        class="form-input @error('priority') error @enderror" 
                        value="{{ old('priority', $nextPriority ?? 0) }}"
                        min="0"
                        required
                    >
                    <span class="form-hint">Enter a priority number. If the number is already taken, the next available priority will be automatically assigned. Higher priority promotions are displayed first.</span>
                    @error('priority')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </fieldset>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                Create
            </button>
            <a href="{{ route('promotions.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Scroll to first error field when page loads with validation errors
    document.addEventListener('DOMContentLoaded', function() {
        const firstErrorField = document.querySelector('.form-input.error, input.error, select.error, textarea.error');
        if (firstErrorField) {
            // Scroll to the error field with smooth behavior
            firstErrorField.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
            
            // Focus on the field after a short delay to ensure scroll completes
            setTimeout(function() {
                firstErrorField.focus();
            }, 300);
        }
    });
</script>
@endpush

@endsection
