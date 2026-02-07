

class SiteSettingHandler {
    constructor() {
        this.siteSetting = null;
        this.selectedFiles = new Map(); // Track selected files across multiple selections
        this.selectedFileKeys = new Set(); // Track file keys for duplicate detection
        this.fileIdCounter = 0; // Counter for generating safe unique IDs
    }

    setSiteSetting(siteSetting) {
        this.siteSetting = siteSetting;
    }

    // Avatar gallery management methods
    previewFiles(input) {

        // Check if we're on the avatar settings page
        const avatarGrid = document.querySelector('.avatar-display-grid');
        if (!avatarGrid) {
            return;
        }

        if (input.files && input.files.length > 0) {

            // Process each file from the input
            Array.from(input.files).forEach((file, index) => {

                // Validate file size (2MB = 2 * 1024 * 1024 bytes)
                const maxSize = 2 * 1024 * 1024; // 2MB
                if (file.size > maxSize) {
                    SwalAlert.error('File Too Large', `File "${file.name}" is too large. Maximum size is 2MB. Current size: ${(file.size / (1024 * 1024)).toFixed(2)}MB`);
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
                if (!allowedTypes.includes(file.type)) {
                    SwalAlert.error('Invalid File Type', `File "${file.name}" is not a valid image type. Allowed types: JPEG, PNG, GIF, WebP, SVG.`);
                    return;
                }

                // Create unique identifier for the file based on its properties
                const fileKey = `${file.name}-${file.size}-${file.lastModified}`;

                // Check if file is already selected
                if (this.selectedFileKeys.has(fileKey)) {
                    return;
                }

                // Generate a safe numeric ID for DOM/onclick usage (no special characters)
                const fileId = `file_${++this.fileIdCounter}`;

                // Store the file in our maps
                this.selectedFiles.set(fileId, { file, fileKey });
                this.selectedFileKeys.add(fileKey);

                // Create object URL for preview
                const objectUrl = URL.createObjectURL(file);

                // Escape filename for safe HTML display
                const safeFileName = file.name.replace(/</g, '&lt;').replace(/>/g, '&gt;');

                // Create avatar display item
                const avatarItem = document.createElement('div');
                avatarItem.className = 'avatar-display-item uploaded-avatar';
                avatarItem.setAttribute('data-file-id', fileId);
                avatarItem.innerHTML = `
                    <div class="avatar-image-container">
                        <img src="${objectUrl}" alt="New Avatar" class="avatar-display-image">
                        <button type="button" class="avatar-remove-btn"
                                onclick="siteSettingHandler.removeUploadedAvatar(this, '${fileId}')"
                                title="Remove uploaded avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 1 1-.708-.708L7.293 8 2.146 2.854Z"/>
                            </svg>
                        </button>
                    </div>
                    <div class="avatar-info">
                        <div class="avatar-path">${safeFileName}</div>
                    </div>
                    <div class="upload-indicator">
                        <span class="badge bg-success">New Upload</span>
                    </div>
                `;

                // Add to the beginning of the grid (before existing avatars)
                avatarGrid.insertBefore(avatarItem, avatarGrid.firstChild);

                // Hide empty state when files are added
                const emptyState = document.getElementById('avatar-empty-state');
                if (emptyState) {
                    emptyState.style.display = 'none';
                }
            });
        } else {
            // If no files selected, clear all selected files tracking
            this.selectedFiles.clear();
            this.clearUploadPreview();
        }
    }

    clearUploadPreview() {
        // Clear selected files tracking
        this.selectedFiles.clear();
        this.selectedFileKeys.clear();

        // Remove any existing uploaded avatars from preview
        document.querySelectorAll('.uploaded-avatar').forEach(item => {
            const img = item.querySelector('.avatar-display-image');
            if (img && img.src.startsWith('blob:')) {
                URL.revokeObjectURL(img.src);
            }
            item.remove();
        });
    }

    clearAllUploads() {
        // Public method to clear all uploaded previews (can be called externally)
        this.clearUploadPreview();
    }

    removeUploadedAvatar(buttonElement, fileId) {
        const avatarItem = buttonElement.closest('.avatar-display-item');

        // Remove from selected files tracking
        if (fileId && this.selectedFiles.has(fileId)) {
            const fileData = this.selectedFiles.get(fileId);
            if (fileData && fileData.fileKey) {
                this.selectedFileKeys.delete(fileData.fileKey);
            }
            this.selectedFiles.delete(fileId);
        }

        // Revoke the object URL to free memory
        const img = avatarItem.querySelector('.avatar-display-image');
        if (img && img.src && img.src.startsWith('blob:')) {
            URL.revokeObjectURL(img.src);
        }

        // Remove the item from DOM
        avatarItem.remove();

        // Show empty state if no avatars left
        this.checkEmptyState();
    }

    toggleAvatarSelection(avatarPath, buttonElement) {
        const avatarItem = buttonElement.closest('.avatar-display-item');

        if (avatarItem) {
            avatarItem.remove();
        }

        // Show empty state if no avatars left
        this.checkEmptyState();
    }

    checkEmptyState() {
        const avatarGrid = document.querySelector('.avatar-display-grid');
        const emptyState = document.getElementById('avatar-empty-state');

        if (avatarGrid && emptyState) {
            const hasAvatars = avatarGrid.querySelector('.avatar-display-item') !== null;
            emptyState.style.display = hasAvatars ? 'none' : 'flex';
        }
    }

    initializeAvatarGallery() {
        // Set up form submission handler to include all selected files
        // Note: This runs directly since DOMContentLoaded has already fired when this is called
        const form = document.querySelector('form[action*="site-settings"]');
        if (form) {
            form.addEventListener('submit', (e) => {
                const fileInput = document.querySelector('input[name="avatar_files[]"]');
                if (fileInput) {
                    // Create a new DataTransfer to hold all selected files
                    const dt = new DataTransfer();
                    
                    // Add only the files that are still in our selectedFiles map
                    this.selectedFiles.forEach((fileData) => {
                        if (fileData && fileData.file) {
                            dt.items.add(fileData.file);
                        }
                    });
                    
                    // Update the file input with accumulated files (or empty if all were removed)
                    fileInput.files = dt.files;
                }
            });
        }
        // Note: No need for beforeunload cleanup - browser automatically frees memory on page unload
    }
}

// Create global instance
export default SiteSettingHandler;