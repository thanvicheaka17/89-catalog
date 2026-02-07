/**
 * Promotion Handler
 * Handles all promotion functionality:
 * - Drag-and-drop image upload with size validation
 * - Live promotion preview
 * - Color input sync
 * - Delete confirmation with SweetAlert
 */

class PromotionHandler {
    constructor(options = {}) {
        this.maxSize = options.maxSize || 10 * 1024 * 1024; // 10MB default
        this.maxSizeMB = this.maxSize / (1024 * 1024);
        this.acceptedTypes = options.acceptedTypes || ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        this.dropZone = null;
        this.fileInput = null;
        this.previewContainer = null;
        this.errorContainer = null;
        this.removeBtn = null;
        this.dropZoneContent = null;
        this.currentFile = null;
        
        this.init();
    }

    init() {
        // Image upload elements
        this.dropZone = document.getElementById('imageDropZone');
        this.fileInput = document.getElementById('imageInput');
        this.previewContainer = document.getElementById('imagePreview');
        this.errorContainer = document.getElementById('imageError');
        this.removeBtn = document.getElementById('removeImage');
        this.dropZoneContent = this.dropZone?.querySelector('.drop-zone-content');

        // Initialize features based on what elements exist
        if (this.dropZone && this.fileInput) {
            this.bindImageUploadEvents();
            this.checkExistingImage();
        }

        // Initialize promotion preview (for create/edit pages)
        this.initPromotionPreview();
        
        // Initialize color input sync
        this.initColorInputSync();
        
        // Initialize gradient pickers
        this.initGradientPickers();
        
        
        // Initialize toggle confirmations
        this.initToggleConfirmations();
        
        // Initialize duplicate confirmations
        this.initDuplicateConfirmations();
    }

    // ==========================================
    // Image Upload Functionality
    // ==========================================

    bindImageUploadEvents() {
        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.dropZone.addEventListener(eventName, this.preventDefaults.bind(this), false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            this.dropZone.addEventListener(eventName, this.highlight.bind(this), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            this.dropZone.addEventListener(eventName, this.unhighlight.bind(this), false);
        });

        this.dropZone.addEventListener('drop', this.handleDrop.bind(this), false);

        // Click to upload - but not if clicking on the file input itself
        this.dropZone.addEventListener('click', (e) => {
            if (e.target === this.fileInput || 
                e.target.closest('.remove-image-btn') || 
                this.dropZone.classList.contains('has-file')) {
                return;
            }
            this.fileInput.click();
        });

        // Prevent file input click from bubbling to drop zone
        this.fileInput.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // File input change
        this.fileInput.addEventListener('change', this.handleFileSelect.bind(this), false);

        // Remove image button
        if (this.removeBtn) {
            this.removeBtn.addEventListener('click', this.removeImage.bind(this), false);
        }
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    highlight() {
        this.dropZone.classList.add('drag-over');
    }

    unhighlight() {
        this.dropZone.classList.remove('drag-over');
    }

    handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            this.handleFile(files[0]);
        }
    }

    handleFileSelect(e) {
        const files = e.target.files;

        if (files.length > 0) {
            this.handleFile(files[0]);
        }
    }

    handleFile(file) {
        this.clearError();

        // Validate file type
        if (!this.acceptedTypes.includes(file.type)) {
            this.showError(`Invalid file type. Please upload an image (JPEG, PNG, GIF, or WebP).`);
            return;
        }

        // Validate file size
        if (file.size > this.maxSize) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            this.showError(`File size (${fileSizeMB}MB) exceeds the maximum allowed size of ${this.maxSizeMB}MB.`);
            return;
        }

        this.currentFile = file;
        this.showImagePreview(file);
        this.updateFileInput(file);
    }

    showImagePreview(file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            this.previewContainer.innerHTML = `
                <div class="image-preview-item">
                    <img src="${e.target.result}" alt="Preview">
                    <div class="image-preview-info">
                        <span class="image-name">${this.truncateFileName(file.name, 30)}</span>
                        <span class="image-size">${this.formatFileSize(file.size)}</span>
                    </div>
                </div>
            `;
            this.previewContainer.classList.add('has-image');
            this.dropZone.classList.add('has-file');
            
            if (this.dropZoneContent) {
                this.dropZoneContent.style.display = 'none';
            }
            
            if (this.removeBtn) {
                this.removeBtn.style.display = 'flex';
            }
        };

        reader.readAsDataURL(file);
    }

    updateFileInput(file) {
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        this.fileInput.files = dataTransfer.files;
    }

    removeImage(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        this.currentFile = null;
        this.fileInput.value = '';
        this.previewContainer.innerHTML = '';
        this.previewContainer.classList.remove('has-image');
        this.dropZone.classList.remove('has-file');
        
        if (this.dropZoneContent) {
            this.dropZoneContent.style.display = 'flex';
        }
        
        if (this.removeBtn) {
            this.removeBtn.style.display = 'none';
        }

        const removeInput = document.getElementById('removeImageFlag');
        if (removeInput) {
            removeInput.value = '1';
        }

        this.clearError();
    }

    checkExistingImage() {
        const existingImage = this.previewContainer?.querySelector('img');
        if (existingImage && existingImage.src) {
            this.dropZone.classList.add('has-file');
            this.previewContainer.classList.add('has-image');
            
            if (this.dropZoneContent) {
                this.dropZoneContent.style.display = 'none';
            }
            
            if (this.removeBtn) {
                this.removeBtn.style.display = 'flex';
            }
        }
    }

    // ==========================================
    // Promotion Preview Functionality
    // ==========================================

    initPromotionPreview() {
        const preview = document.getElementById('promotionPreview');
        const previewTitle = document.getElementById('previewTitle');
        const previewMessage = document.getElementById('previewMessage');
        const previewButton = document.getElementById('previewButton');
        
        if (!preview || !previewTitle) return;
        
        const titleInput = document.getElementById('title');
        const messageInput = document.getElementById('message');
        const buttonTextInput = document.getElementById('button_text');
        const bgColorInput = document.getElementById('background_color');
        const textColorInput = document.getElementById('text_color');
        const buttonColorInput = document.getElementById('button_color');
        const buttonTextColorInput = document.getElementById('button_text_color');
        
        const updatePreview = () => {
            if (previewTitle) {
                previewTitle.textContent = titleInput?.value || 'Your promotion title here';
            }
            if (previewMessage) {
                previewMessage.textContent = messageInput?.value || 'Your promotion message will appear here';
            }
            if (previewButton) {
                previewButton.textContent = buttonTextInput?.value || 'Learn More';
            }
            
            if (preview && bgColorInput) {
                preview.style.backgroundColor = bgColorInput.value;
            }
            if (previewTitle && textColorInput) {
                previewTitle.style.color = textColorInput.value;
            }
            if (previewMessage && textColorInput) {
                previewMessage.style.color = textColorInput.value;
            }
            if (previewButton && buttonColorInput) {
                previewButton.style.backgroundColor = buttonColorInput.value;
            }
            if (previewButton && buttonTextColorInput) {
                previewButton.style.color = buttonTextColorInput.value;
            }
        };
        
        // Add event listeners
        if (titleInput) titleInput.addEventListener('input', updatePreview);
        if (messageInput) messageInput.addEventListener('input', updatePreview);
        if (buttonTextInput) buttonTextInput.addEventListener('input', updatePreview);
        
        // Initial update
        updatePreview();
    }

    // ==========================================
    // Color Input Sync
    // ==========================================

    initColorInputSync() {
        document.querySelectorAll('.form-color-input').forEach(colorInput => {
            const textInput = document.querySelector(`[data-color-for="${colorInput.id}"]`);
            
            if (!textInput) return;
            
            colorInput.addEventListener('input', () => {
                textInput.value = colorInput.value;
                this.updateGradientPreview();
                this.updatePromotionPreviewWithGradients();
            });
            
            textInput.addEventListener('input', () => {
                if (/^#[0-9A-Fa-f]{6}$/.test(textInput.value)) {
                    colorInput.value = textInput.value;
                    this.updateGradientPreview();
                    this.updatePromotionPreviewWithGradients();
                }
            });
        });
    }

    // ==========================================
    // Gradient Pickers
    // ==========================================

    initGradientPickers() {
        // Handle gradient type toggle buttons
        document.querySelectorAll('.gradient-type-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const type = btn.dataset.type;
                const target = btn.dataset.target;
                const picker = btn.closest('.gradient-picker');
                
                // Update active state
                picker.querySelectorAll('.gradient-type-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                // Show/hide gradient options
                const gradientColor2 = picker.querySelector('.gradient-color-2');
                const gradientDirection = picker.querySelector('.gradient-direction');
                
                if (type === 'gradient') {
                    gradientColor2.style.display = 'flex';
                    gradientDirection.style.display = 'flex';
                } else {
                    gradientColor2.style.display = 'none';
                    gradientDirection.style.display = 'none';
                }
                
                // Update hidden input
                const typeInput = document.getElementById(`${target}_gradient_type`);
                if (typeInput) {
                    typeInput.value = type;
                }
                
                this.updateGradientPreview();
                this.updatePromotionPreviewWithGradients();
            });
        });
        
        // Handle gradient direction changes
        document.querySelectorAll('[id$="_gradient_direction"]').forEach(select => {
            select.addEventListener('change', () => {
                this.updateGradientPreview();
                this.updatePromotionPreviewWithGradients();
            });
        });
        
        // Initial preview update
        this.updateGradientPreview();
    }

    updateGradientPreview() {
        // Background gradient preview
        const bgPreview = document.getElementById('bgGradientPreview');
        if (bgPreview) {
            bgPreview.style.background = this.getBackgroundStyle();
        }
        
        // Button gradient preview
        const btnPreview = document.getElementById('btnGradientPreview');
        if (btnPreview) {
            btnPreview.style.background = this.getButtonStyle();
        }
    }

    getBackgroundStyle() {
        const type = document.getElementById('background_gradient_type')?.value || 'solid';
        const color1 = document.getElementById('background_color')?.value || '#0f172a';
        const color2 = document.getElementById('background_color_2')?.value || '#1e3a5f';
        const direction = document.getElementById('background_gradient_direction')?.value || 'to right';
        
        if (type === 'gradient') {
            return `linear-gradient(${direction}, ${color1}, ${color2})`;
        }
        return color1;
    }

    getButtonStyle() {
        const type = document.getElementById('button_gradient_type')?.value || 'solid';
        const color1 = document.getElementById('button_color')?.value || '#f59e0b';
        const color2 = document.getElementById('button_color_2')?.value || '#ea580c';
        const direction = document.getElementById('button_gradient_direction')?.value || 'to right';
        
        if (type === 'gradient') {
            return `linear-gradient(${direction}, ${color1}, ${color2})`;
        }
        return color1;
    }

    updatePromotionPreviewWithGradients() {
        const preview = document.getElementById('promotionPreview');
        const previewButton = document.getElementById('previewButton');
        
        if (preview) {
            preview.style.background = this.getBackgroundStyle();
        }
        
        if (previewButton) {
            previewButton.style.background = this.getButtonStyle();
        }
    }

    // ==========================================
    // Toggle Confirmation
    // ==========================================

    initToggleConfirmations() {
        document.querySelectorAll('form[data-confirm-toggle]').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                const action = form.dataset.confirmToggle || 'toggle';
                const isEnabling = action === 'enable';
                
                if (typeof window.SwalAlert !== 'undefined') {
                    window.SwalAlert.confirm({
                        title: isEnabling ? 'Enable Promotion' : 'Disable Promotion',
                        message: isEnabling 
                            ? 'Are you sure you want to enable this promotion?' 
                            : 'Are you sure you want to disable this promotion?',
                        confirmText: isEnabling ? 'Yes, enable it' : 'Yes, disable it',
                        confirmColor: isEnabling ? '#16a34a' : '#f59e0b',
                        icon: 'question'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    form.submit();
                }
            });
        });
    }

    // ==========================================
    // Duplicate Confirmation
    // ==========================================

    initDuplicateConfirmations() {
        document.querySelectorAll('form[data-confirm-duplicate]').forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                
                if (typeof window.SwalAlert !== 'undefined') {
                    window.SwalAlert.confirm({
                        title: 'Duplicate Promotion',
                        message: 'This will create a copy of this promotion. Continue?',
                        confirmText: 'Yes, duplicate it',
                        confirmColor: '#0f172a',
                        icon: 'question'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                } else {
                    form.submit();
                }
            });
        });
    }

    // ==========================================
    // Alert Methods
    // ==========================================

    showError(message) {
        if (typeof window.SwalAlert !== 'undefined') {
            window.SwalAlert.error('Upload Error', message);
        } else if (this.errorContainer) {
            this.errorContainer.textContent = message;
            this.errorContainer.style.display = 'block';
        } else {
            alert('Upload Error: ' + message);
        }
    }

    showWarning(message) {
        if (typeof window.SwalAlert !== 'undefined') {
            window.SwalAlert.warning('Warning', message);
        } else {
            alert('Warning: ' + message);
        }
    }

    showSuccess(message) {
        if (typeof window.SwalAlert !== 'undefined') {
            window.SwalAlert.success('Success', message);
        } else {
            alert('Success: ' + message);
        }
    }

    clearError() {
        if (this.errorContainer) {
            this.errorContainer.textContent = '';
            this.errorContainer.style.display = 'none';
        }
    }

    // ==========================================
    // Utility Methods
    // ==========================================

    truncateFileName(name, maxLength) {
        if (name.length <= maxLength) return name;
        
        const ext = name.split('.').pop();
        const baseName = name.substring(0, name.lastIndexOf('.'));
        const truncatedBase = baseName.substring(0, maxLength - ext.length - 4);
        
        return `${truncatedBase}...${ext}`;
    }

    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// Export for module usage
export default PromotionHandler;

// Also make available globally for non-module usage
window.PromotionHandler = PromotionHandler;
