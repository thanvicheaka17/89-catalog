import './bootstrap';
import PromotionHandler from './promotion-handler';
import UserHandler from './user-handler';
import BannerHandler from './banner-handler';
import RTPGameHandler from './rtp-game-handler';
import RTPConfigurationHandler from './rtp-configuration-handler';
import ToolHandler from './tool-handler';
import SiteSettingHandler from './site-setting-handler';
/**
 * ==========================================
 * Global SweetAlert Utility
 * ==========================================
 * Provides consistent alert styling across the application
 */
const SwalAlert = {
    // Default custom classes for consistent styling
    customClass: {
        popup: 'swal-custom-popup',
        title: 'swal-custom-title',
        confirmButton: 'swal-custom-button',
        cancelButton: 'swal-custom-button'
    },

    /**
     * Show error alert
     * @param {string} title - Alert title
     * @param {string} message - Alert message
     */
    error(title, message) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                icon: 'error',
                title: title,
                text: message,
                confirmButtonText: 'Got it',
                confirmButtonColor: '#0f172a',
                customClass: this.customClass
            });
        } else {
            alert(`${title}: ${message}`);
            return Promise.resolve();
        }
    },

    /**
     * Show warning alert
     * @param {string} title - Alert title
     * @param {string} message - Alert message
     */
    warning(title, message) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                icon: 'warning',
                title: title,
                text: message,
                confirmButtonText: 'Okay',
                confirmButtonColor: '#f59e0b',
                customClass: this.customClass
            });
        } else {
            alert(`${title}: ${message}`);
            return Promise.resolve();
        }
    },

    /**
     * Show success alert
     * @param {string} title - Alert title
     * @param {string} message - Alert message
     * @param {boolean} autoClose - Auto close after timer (default: true)
     */
    success(title, message, autoClose = true) {
        if (typeof Swal !== 'undefined') {
            const options = {
                icon: 'success',
                title: title,
                text: message,
                confirmButtonText: 'Great!',
                confirmButtonColor: '#16a34a',
                customClass: this.customClass
            };
            if (autoClose) {
                options.timer = 2000;
                options.timerProgressBar = true;
            }
            return Swal.fire(options);
        } else {
            alert(`${title}: ${message}`);
            return Promise.resolve();
        }
    },

    /**
     * Show info alert
     * @param {string} title - Alert title
     * @param {string} message - Alert message
     */
    info(title, message) {
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                icon: 'info',
                title: title,
                text: message,
                confirmButtonText: 'Got it',
                confirmButtonColor: '#3b82f6',
                customClass: this.customClass
            });
        } else {
            alert(`${title}: ${message}`);
            return Promise.resolve();
        }
    },

    /**
     * Show confirmation dialog
     * @param {Object} options - Configuration options
     * @param {string} options.title - Dialog title
     * @param {string} options.message - Dialog message
     * @param {string} options.confirmText - Confirm button text (default: 'Yes')
     * @param {string} options.cancelText - Cancel button text (default: 'Cancel')
     * @param {string} options.confirmColor - Confirm button color (default: '#dc2626')
     * @param {string} options.icon - Icon type (default: 'warning')
     * @returns {Promise} Resolves with result object
     */
    confirm(options = {}) {
        const {
            title = 'Are you sure?',
            message = '',
            confirmText = 'Yes',
            cancelText = 'Cancel',
            confirmColor = '#dc2626',
            icon = 'warning'
        } = options;

        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                icon: icon,
                title: title,
                text: message,
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: cancelText,
                confirmButtonColor: confirmColor,
                cancelButtonColor: '#64748b',
                customClass: this.customClass
            });
        } else {
            const result = confirm(`${title}\n${message}`);
            return Promise.resolve({ isConfirmed: result });
        }
    },

    /**
     * Show delete confirmation
     * @param {string} itemName - Name of item being deleted
     * @returns {Promise} Resolves with result object
     */
    confirmDelete(itemName = 'this item') {
        return this.confirm({
            title: 'Delete Confirmation',
            message: `Are you sure you want to delete ${itemName}?`,
            confirmText: 'Yes, delete it',
            confirmColor: '#dc2626',
            icon: 'warning'
        });
    }
};

// Make SwalAlert globally available
window.SwalAlert = SwalAlert;

/**
 * ==========================================
 * Custom Toast Notification System
 * ==========================================
 * Modern toast notifications with animations and multiple types
 */
const Toast = {
    // Toast Types Configuration
    TYPES: {
        SUCCESS: {
            color: 'text-emerald-500',
            bg: 'bg-emerald-50',
            border: 'border-l-emerald-500',
            icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
        },
        ERROR: {
            color: 'text-rose-500',
            bg: 'bg-rose-50',
            border: 'border-l-rose-500',
            icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
        },
        INFO: {
            color: 'text-blue-500',
            bg: 'bg-blue-50',
            border: 'border-l-blue-500',
            icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
        },
        WARNING: {
            color: 'text-amber-500',
            bg: 'bg-amber-50',
            border: 'border-l-amber-500',
            icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>`,
        }
    },

    /**
     * Show toast notification
     * @param {string} message - The message to display
     * @param {string} type - Toast type (SUCCESS, ERROR, INFO, WARNING)
     * @param {string} title - Optional title, defaults based on type
     * @param {number} duration - Auto-dismiss duration in ms (default: 5000)
     */
    show(message, type = 'INFO', title = null, duration = 5000) {
        const config = this.TYPES[type.toUpperCase()] || this.TYPES.INFO;
        const toastId = 'toast-' + Math.random().toString(36).substr(2, 9);

        // Set default titles if not provided
        if (!title) {
            switch (type.toUpperCase()) {
                case 'SUCCESS':
                    title = 'Success';
                    break;
                case 'ERROR':
                    title = 'Error';
                    break;
                case 'WARNING':
                    title = 'Warning';
                    break;
                default:
                    title = 'Information';
            }
        }

        const toastHTML = `
            <div id="${toastId}" class="toast-notification relative flex items-start w-80 mb-4 overflow-hidden bg-white rounded-r-xl rounded-l-md shadow-xl border-l-4 ${config.border} border-y border-r border-gray-100 transition-all duration-300 transform translate-x-0 animate-in slide-in-from-right-full pointer-events-auto" role="alert">
                <div class="flex p-4 w-full">
                    <div class="flex-shrink-0 ${config.color} mt-0.5">
                        ${config.icon}
                    </div>
                    <div class="ml-3 mr-6 flex-1">
                        <h3 class="text-sm font-bold text-gray-900 leading-tight">${title}</h3>
                        <p class="mt-1 text-xs text-gray-500 leading-relaxed font-medium">${message}</p>
                    </div>
                    <button onclick="Toast.close('${toastId}')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-900 hover:bg-gray-100 p-1 rounded-lg transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `;

        // Ensure toast container exists
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed top-8 right-8 z-50 flex flex-col items-end pointer-events-none';
            document.body.appendChild(container);
        }

        container.insertAdjacentHTML('beforeend', toastHTML);

        // Auto-remove after duration
        setTimeout(() => {
            this.close(toastId);
        }, duration);
    },

    /**
     * Close toast notification
     * @param {string} toastId - The ID of the toast to close
     */
    close(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('animate-out', 'slide-out-to-right-full');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }
    },

    // Convenience methods for different toast types
    success(message, title = 'Success') {
        this.show(message, 'SUCCESS', title);
    },

    error(message, title = 'Error') {
        this.show(message, 'ERROR', title);
    },

    info(message, title = 'Information') {
        this.show(message, 'INFO', title);
    },

    warning(message, title = 'Warning') {
        this.show(message, 'WARNING', title);
    }
};

// Make Toast globally available
window.Toast = Toast;

/**
 * Toggle Sidebar (Mobile)
 */
window.toggleSidebar = function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
    if (overlay) {
        overlay.classList.toggle('open');
    }
};

/**
 * Toggle User Dropdown Menu
 */
window.toggleDropdown = function() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('open');
    }
};

/**
 * Toggle Filters Panel with animation
 */
window.toggleFilters = function() {
    const filtersPanel = document.getElementById('filtersPanel');
    if (filtersPanel) {
        filtersPanel.classList.toggle('open');
    }
};

/**
 * Toggle Sub-menu with smooth animation
 * @param {string} menuId - The ID of the submenu to toggle
 */
window.toggleSubMenu = function(menuId) {
    const submenu = document.getElementById(menuId + '-submenu');
    const parentItem = document.querySelector(`[onclick="toggleSubMenu('${menuId}')"]`);

    if (submenu) {
        // Close all other sub-menus first
        const allSubmenus = document.querySelectorAll('.nav-submenu');
        const allParents = document.querySelectorAll('.nav-item-parent');

        allSubmenus.forEach(menu => {
            if (menu !== submenu) {
                menu.classList.remove('open');
            }
        });

        allParents.forEach(parent => {
            if (parent !== parentItem) {
                parent.classList.remove('active');
            }
        });

        // Toggle the clicked sub-menu
        submenu.classList.toggle('open');
        if (parentItem) {
            parentItem.classList.toggle('active');
        }
    }
};

/**
 * Initialize sub-menu states on page load based on active routes
 */
function initializeActiveSubMenus() {
    // Check if any parent menu items are already active and open their sub-menus
    const activeParents = document.querySelectorAll('.nav-item-parent.active');

    activeParents.forEach(parent => {
        const onclickAttr = parent.getAttribute('onclick');
        if (onclickAttr) {
            // Extract menuId from onclick attribute (e.g., "toggleSubMenu('content-management')")
            const menuIdMatch = onclickAttr.match(/toggleSubMenu\('([^']+)'\)/);
            if (menuIdMatch) {
                const menuId = menuIdMatch[1];
                const submenu = document.getElementById(menuId + '-submenu');
                if (submenu && !submenu.classList.contains('open')) {
                    submenu.classList.add('open');
                }
            }
        }
    });
}

// Run initialization when page is fully loaded
window.addEventListener('load', function() {
    // Small delay to ensure all DOM manipulations are complete
    setTimeout(initializeActiveSubMenus, 50);
});

// Also try immediately in case load event already fired
if (document.readyState === 'complete') {
    setTimeout(initializeActiveSubMenus, 50);
}

/**
 * Close dropdown when clicking outside
 */
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown && !dropdown.contains(event.target)) {
        dropdown.classList.remove('open');
    }
});

/**
 * Close sidebar and dropdown on Escape key
 */
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const dropdown = document.getElementById('userDropdown');
        
        if (sidebar) {
            sidebar.classList.remove('open');
        }
        if (overlay) {
            overlay.classList.remove('open');
        }
        if (dropdown) {
            dropdown.classList.remove('open');
        }
    }
});

/**
 * Convert HSL to RGB
 * @param {number} h - Hue (0-360)
 * @param {number} s - Saturation (0-100)
 * @param {number} l - Lightness (0-100)
 * @returns {Array} [r, g, b] values (0-255)
 */
function hslToRgb(h, s, l) {
    s /= 100;
    l /= 100;
    const k = n => (n + h / 30) % 12;
    const a = s * Math.min(l, 1 - l);
    const f = n => l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)));
    return [
        Math.round(f(0) * 255),
        Math.round(f(8) * 255),
        Math.round(f(4) * 255)
    ];
}

/**
 * Convert RGB to Hex
 * @param {number} r - Red (0-255)
 * @param {number} g - Green (0-255)
 * @param {number} b - Blue (0-255)
 * @returns {string} Hex color string
 */
function rgbToHex(r, g, b) {
    return '#' + [r, g, b].map(x => {
        const hex = x.toString(16);
        return hex.length === 1 ? '0' + hex : hex;
    }).join('');
}

/**
 * Generate gradient based on username
 * @param {string} username - The username to generate gradient from
 * @returns {string} CSS gradient string
 */
function generateAvatarGradient(username) {
    // Generate a hash from the username
    let hash = 0;
    for (let i = 0; i < username.length; i++) {
        hash = username.charCodeAt(i) + ((hash << 5) - hash);
    }
    
    // Generate hue from hash (0-360)
    const hue = Math.abs(hash % 360);
    
    // Create two colors with same hue but different lightness
    const [r1, g1, b1] = hslToRgb(hue, 70, 55);
    const [r2, g2, b2] = hslToRgb(hue, 80, 40);
    
    const hexTop = rgbToHex(r1, g1, b1);
    const hexBottom = rgbToHex(r2, g2, b2);
    
    return `linear-gradient(180deg, ${hexTop} 0%, ${hexBottom} 100%)`;
}

/**
 * Apply avatar gradients to all elements with data-username attribute
 */
function applyAvatarGradients() {
    const avatars = document.querySelectorAll('[data-username]');
    avatars.forEach(avatar => {
        const username = avatar.getAttribute('data-username');
        if (username) {
            avatar.style.background = generateAvatarGradient(username);
        }
    });
}

/**
 * Close alert with animation
 * @param {HTMLElement} button - The close button element
 */
window.closeAlert = function(button) {
    const alert = button.closest('.alert');
    if (alert) {
        alert.classList.add('hiding');
        setTimeout(() => {
            alert.remove();
        }, 300);
    }
};

/**
 * Auto-dismiss alerts after specified time
 */
function initAutoDissmissAlerts() {
    const alerts = document.querySelectorAll('.alert[data-auto-dismiss]');
    alerts.forEach(alert => {
        const dismissTime = parseInt(alert.getAttribute('data-auto-dismiss')) || 5000;
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.classList.add('hiding');
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        }, dismissTime);
    });
}

/**
 * Initialize global delete confirmations
 * Works for any form with data-confirm-delete attribute
 */
function initGlobalDeleteConfirmations() {
    document.querySelectorAll('form[data-confirm-delete]').forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const message = form.dataset.confirmDelete || 'Are you sure you want to delete this item?';
            const title = form.dataset.deleteTitle || 'Delete Confirmation';
            
            SwalAlert.confirm({
                title: title,
                message: message,
                confirmText: 'Yes, delete it',
                confirmColor: '#dc2626',
                icon: 'warning'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    applyAvatarGradients();
    initAutoDissmissAlerts();
    initGlobalDeleteConfirmations();
    
    // Initialize Promotion Handler on all promotion pages
    // It handles: image upload, live preview, color sync, and confirmation dialogs
    window.promotionHandler = new PromotionHandler({
        maxSize: 10 * 1024 * 1024, // 10MB
        acceptedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    });

    // Initialize User Handler on user create/edit pages
    // It handles: avatar upload with validation and SweetAlert error messages
    window.userHandler = new UserHandler({
        maxSize: 2 * 1024 * 1024, // 2MB
        acceptedTypes: ['image/jrpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml']
    });

    // Initialize Banner Handler on banner create/edit pages
    // It handles: image upload with validation and SweetAlert error messages
    window.bannerHandler = new BannerHandler({
        maxSize: 10 * 1024 * 1024, // 10MB
        acceptedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp']
    });

    // Initialize RTP Configuration Handler on RTP configuration pages
    window.rtpConfigurationHandler = new RTPConfigurationHandler();

    // Trigger enhancement if the enhancement function exists
    if (typeof window.enhanceRTPHandler === 'function') {
        window.enhanceRTPHandler();
    }

    window.rtpGameHandler = new RTPGameHandler();

    // Make ToolHandler available globally for initialization on tools page
    window.ToolHandler = ToolHandler;

    // Make SiteSettingHandler available globally for initialization on site settings page
    window.siteSettingHandler = new SiteSettingHandler();

    // Make SiteSettingHandler functions globally available for HTML onclick handlers
    window.previewFiles = (input) => window.siteSettingHandler.previewFiles(input);
    window.toggleAvatarSelection = (avatarPath, buttonElement) => window.siteSettingHandler.toggleAvatarSelection(avatarPath, buttonElement);

    // Initialize avatar gallery on site settings edit page
    if (window.location.pathname.includes('site-settings') && window.location.pathname.includes('edit')) {
        window.siteSettingHandler.initializeAvatarGallery();
    }

});



function convertDatesToLocalTimezone() {
    document.querySelectorAll('.datetime[data-iso]').forEach(function(el) {
        const iso = el.dataset.iso;
        if (iso && !el.dataset.converted) {
            const d = new Date(iso);
            if (!isNaN(d.getTime())) {
                const date = d.toLocaleDateString('en-US', { 
                    month: 'short', 
                    day: '2-digit', 
                    year: 'numeric' 
                });
                const time = d.toLocaleTimeString('en-US', { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    // second: '2-digit', 
                    hour12: true 
                });
                el.textContent = date + ' at ' + time;
                el.dataset.converted = 'true';
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const categoryNameInput = document.querySelector('#category-name');
    const categorySlugInput = document.querySelector('#category-slug');

    if (categoryNameInput && categorySlugInput) {
        categoryNameInput.addEventListener('keyup', function() {
            categorySlugInput.value = generateSlug(this.value);
        });
    }

    function generateSlug(text) {
        return text
            .toString()
            .toLowerCase()
            .trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')      
            .replace(/\-\-+/g, '-');       
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const activeParents = document.querySelectorAll('.nav-item-parent.active');

    activeParents.forEach((parent, index) => {
        const onclickAttr = parent.getAttribute('onclick');

        if (onclickAttr) {
            const menuIdMatch = onclickAttr.match(/toggleSubMenu\('([^']+)'\)/);
            if (menuIdMatch) {
                const menuId = menuIdMatch[1];

                const submenu = document.getElementById(menuId + '-submenu');

                if (submenu) {
                    submenu.classList.add('open');
                }
            }
        }
    });
});
    

// Run on DOM ready
document.addEventListener('DOMContentLoaded', convertDatesToLocalTimezone);
setTimeout(convertDatesToLocalTimezone, 100);

/**
 * Show image preview in modal
 * @param {string|File} imageSource - Image URL or File object
 * @param {string} title - Optional title for the modal
 */
window.showImagePreview = function(imageSource, title = 'Image Preview') {
    let imageUrl;
    
    // Handle File object
    if (imageSource instanceof File) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imageUrl = e.target.result;
            displayImageModal(imageUrl, title);
        };
        reader.readAsDataURL(imageSource);
        return;
    }
    
    // Handle URL string
    imageUrl = imageSource;
    displayImageModal(imageUrl, title);
};

function displayImageModal(imageUrl, title) {
    // Remove existing modal if any
    const existingModal = document.getElementById('imagePreviewModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Create modal overlay
    const modalOverlay = document.createElement('div');
    modalOverlay.id = 'imagePreviewModal';
    modalOverlay.className = 'modal-overlay';
    modalOverlay.onclick = function(e) {
        if (e.target === modalOverlay) {
            closeImageModal();
        }
    };
    
    // Create image container (position relative for absolute positioned close button)
    const imageContainer = document.createElement('div');
    imageContainer.className = 'image-preview-container';
    imageContainer.onclick = function(e) {
        e.stopPropagation();
    };
    
    // Create image at original size
    const image = document.createElement('img');
    image.className = 'image-preview-img';
    image.src = imageUrl;
    image.alt = title || 'Image Preview';
    
    // Create close button (positioned absolutely in top-right corner)
    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'image-preview-close';
    closeButton.onclick = function(e) {
        e.stopPropagation();
        closeImageModal();
    };
    closeButton.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
    `;
    closeButton.title = 'Close';
    
    // Assemble
    imageContainer.appendChild(image);
    imageContainer.appendChild(closeButton);
    modalOverlay.appendChild(imageContainer);
    
    // Add to body
    document.body.appendChild(modalOverlay);
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

/**
 * Close image preview modal
 */
window.closeImageModal = function() {
    const modal = document.getElementById('imagePreviewModal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = '';
    }
};

// Close modal on Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeImageModal();
    }
});