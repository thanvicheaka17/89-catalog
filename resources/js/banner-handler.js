

class BannerHandler {
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
        this.isOpeningFileDialog = false;
        
        this.init();
    }

    init() {
        this.dropZone = document.getElementById('imageDropZone');
        this.fileInput = document.getElementById('imageInput');
        this.previewContainer = document.getElementById('imagePreview');
        this.errorContainer = document.getElementById('imageError');
        this.removeBtn = document.getElementById('removeImage');
        this.dropZoneContent = this.dropZone?.querySelector('.drop-zone-content');

        if (this.dropZone && this.fileInput) {
            this.bindImageUploadEvents();
            this.checkExistingImage();
        }
    }

    bindImageUploadEvents() {
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

        this.dropZone.addEventListener('click', (e) => {
            const path = e.composedPath ? e.composedPath() : (e.path || []);
            const clickedFileInput = path.includes(this.fileInput);
            
            if (clickedFileInput ||
                e.target === this.fileInput || 
                e.target === this.removeBtn ||
                e.target.closest('input[type="file"]') ||
                e.target.closest('.remove-image-btn') || 
                this.dropZone.classList.contains('has-file') ||
                this.isOpeningFileDialog) {
                return;
            }
            
            e.preventDefault();
            e.stopPropagation();
            
            this.isOpeningFileDialog = true;
            this.fileInput.click();
            setTimeout(() => {
                this.isOpeningFileDialog = false;
            }, 300);
        });

        this.fileInput.addEventListener('click', (e) => {
            e.stopPropagation();
            e.stopImmediatePropagation();
            this.isOpeningFileDialog = true;
            setTimeout(() => {
                this.isOpeningFileDialog = false;
            }, 300);
        }, true); 

        this.fileInput.addEventListener('change', this.handleFileSelect.bind(this), false);

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

        if (!this.acceptedTypes.includes(file.type)) {
            this.showError(`Invalid file type. Please upload an image (JPEG, PNG, GIF, or WebP).`);
            return;
        }

        if (file.size > this.maxSize) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            this.showError(`File size (${fileSizeMB}MB) exceeds the maximum allowed size of ${this.maxSizeMB}MB.`);
            return;
        }

        const removeInput = document.getElementById('removeImageFlag');
        if (removeInput) {
            removeInput.value = '0';
        }

        this.currentFile = file;
        this.showImagePreview(file);
        this.updateFileInput(file);
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
    
    clearError() {
        if (this.errorContainer) {
            this.errorContainer.textContent = '';
            this.errorContainer.style.display = 'none';
        }
    }
    
    checkExistingImage() {
        const existingImage = this.previewContainer?.querySelector('img');
        if (existingImage && existingImage.src) {
            this.dropZone.classList.add('has-file');
            this.previewContainer.classList.add('has-image');
            
            if (this.dropZoneContent) {
                this.dropZoneContent.style.display = 'none';
            }
        }
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

export default BannerHandler;