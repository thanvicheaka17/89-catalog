
class ToolHandler {
    constructor(options = {}) {
        this.firstItem = options.firstItem || 1;
        this.updateOrderUrl = options.updateOrderUrl || null;
        this.tbodyId = options.tbodyId || 'toolsTableBody';
        this.filterSettingsUrl = options.filterSettingsUrl || null;
        this.saveFilterSettingsUrl = options.saveFilterSettingsUrl || null;
        this.sortable = null;
        
        this.init();
        this.initFilterSettingsModal();
    }

    init() {
        // Wait for Sortable.js to be available
        if (typeof Sortable === 'undefined') {
            this.loadSortableJS().then(() => {
                this.initSortable();
            });
        } else {
            this.initSortable();
        }
    }

    loadSortableJS() {
        return new Promise((resolve, reject) => {
            // Check if already loaded
            if (typeof Sortable !== 'undefined') {
                resolve();
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js';
            script.onload = () => resolve();
            script.onerror = () => reject(new Error('Failed to load Sortable.js'));
            document.head.appendChild(script);
        });
    }

    initSortable() {
        const tbody = document.getElementById(this.tbodyId);
        
        if (!tbody) {
            return;
        }

        if (!this.updateOrderUrl) {
            return;
        }

        this.sortable = Sortable.create(tbody, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            onEnd: (evt) => this.handleDragEnd(evt)
        });
    }

    handleDragEnd(evt) {
        const tbody = evt.to;
        const toolRows = Array.from(tbody.querySelectorAll('tr[data-tool-id]'));
        
        // Calculate new display_order values based on current position
        const orderData = toolRows.map((row, index) => ({
            id: row.getAttribute('data-tool-id'),
            display_order: this.firstItem + index
        }));
        

        // Send AJAX request to update positions
        fetch(this.updateOrderUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order: orderData })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Network response was not ok');
                });
            }
            return response.json();
        })
        .then(data => {
            
            if (data && data.success) {
                this.updateRowNumbers(toolRows);
                // this.showSuccessMessage();
            } else {
                this.handleError(data?.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error updating order:', error);
            this.handleError('Failed to update tool order. Please try again.');
        });
    }

    updateRowNumbers(toolRows) {
        toolRows.forEach((row, index) => {
            // Find the row number cell (second td, after drag handle)
            const cells = row.querySelectorAll('td');
            const rowNumberCell = cells[1]; // Second cell (index 1) is the row number
            
            if (rowNumberCell && rowNumberCell.classList.contains('td-number')) {
                const newRowNumber = this.firstItem + index;
                rowNumberCell.textContent = newRowNumber;
                if (rowNumberCell.hasAttribute('data-row-index')) {
                    rowNumberCell.setAttribute('data-row-index', newRowNumber);
                }
            }
            // Update display_order attribute
            row.setAttribute('data-display-order', this.firstItem + index);
        });
    }

    showSuccessMessage() {
        if (window.Toast) {
            Toast.success('Tool order updated successfully', 'Success');
        } else {
            alert('Tool order updated successfully');
        }
    }

    handleError(message) {
        console.error('Update failed:', message);
        
        if (window.Toast) {
            Toast.error(message || 'Failed to update tool order', 'Error');
        } else {
            alert('Failed to update order: ' + (message || 'Unknown error'));
        }
        
        // Revert on error after showing message
        setTimeout(() => location.reload(), 2000);
    }

    /**
     * Initialize filter settings modal functionality
     */
    initFilterSettingsModal() {
        // Make methods available globally
        window.openFilterSettingsModal = () => this.openFilterSettingsModal();
        window.closeFilterSettingsModal = () => this.closeFilterSettingsModal();
        
        // Close modal when clicking outside (works even if DOMContentLoaded already fired)
        const setupModalClickHandler = () => {
            const modal = document.getElementById('filterSettingsModal');
            if (modal) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        this.closeFilterSettingsModal();
                    }
                });
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupModalClickHandler);
        } else {
            setupModalClickHandler();
        }
    }

    /**
     * Open filter settings modal
     */
    openFilterSettingsModal() {
        const modal = document.getElementById('filterSettingsModal');
        const modalBody = document.getElementById('filterSettingsModalBody');
        
        if (!modal || !modalBody) {
            console.error('Filter settings modal elements not found');
            return;
        }

        if (!this.filterSettingsUrl) {
            console.error('Filter settings URL not provided');
            return;
        }
        
        // Show modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Load content via AJAX
        fetch(this.filterSettingsUrl, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load filter settings');
            }
            return response.text();
        })
        .then(html => {
            // The response is just the form content
            modalBody.innerHTML = html;
            // Footer is always visible in the modal structure
            this.initializeFilterSettingsSortable();
        })
        .catch(error => {
            console.error('Error loading filter settings:', error);
            modalBody.innerHTML = '<div class="alert alert-error">Error loading filter settings. Please try again.</div>';
        });
    }

    /**
     * Close filter settings modal
     */
    closeFilterSettingsModal() {
        const modal = document.getElementById('filterSettingsModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    /**
     * Initialize Sortable.js for filter settings lists
     */
    initializeFilterSettingsSortable() {
        if (typeof Sortable === 'undefined') {
            console.error('Sortable.js not loaded');
            return;
        }

        // Wait a bit for DOM to be ready
        setTimeout(() => {
            const lists = ['categoryOrderList', 'tierOrderList', 'sortingOrderList'];
            
            lists.forEach(listId => {
                const list = document.getElementById(listId);
                if (list && !list.hasAttribute('data-sortable-initialized')) {
                    Sortable.create(list, {
                        handle: '.sortable-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        onEnd: (evt) => {
                            // Update hidden input order
                            const items = evt.to.querySelectorAll('.sortable-item');
                            items.forEach((item) => {
                                const input = item.querySelector('input[type="hidden"]');
                                if (input) {
                                    input.remove();
                                    item.appendChild(input);
                                }
                            });
                        }
                    });
                    list.setAttribute('data-sortable-initialized', 'true');
                }
            });

            // Handle form submission
            this.handleFilterSettingsFormSubmit();
        }, 200);
    }

    /**
     * Handle filter settings form submission
     */
    handleFilterSettingsFormSubmit() {
        const form = document.getElementById('filterSettingsForm');
        const saveButton = document.getElementById('saveFilterSettingsBtn');
        
        if (!form) {
            return;
        }

        if (!this.saveFilterSettingsUrl) {
            console.error('Save filter settings URL not provided');
            return;
        }

        // Handle save button click from modal footer
        if (saveButton) {
            // Remove any existing listeners to avoid duplicates
            const newSaveButton = saveButton.cloneNode(true);
            saveButton.parentNode.replaceChild(newSaveButton, saveButton);
            
            newSaveButton.addEventListener('click', () => {
                this.submitFilterSettingsForm(form);
            });
        }

        // Also handle form submission (if form has submit button)
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitFilterSettingsForm(form);
        });
    }

    /**
     * Submit filter settings form
     */
    submitFilterSettingsForm(form) {
        const formData = new FormData(form);
        
        fetch(this.saveFilterSettingsUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Failed to save settings');
                }).catch(() => {
                    throw new Error('Failed to save settings');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data && data.success) {
                if (window.Toast) {
                    Toast.success('Filter order settings saved successfully', 'Success');
                } else {
                    alert('Filter order settings saved successfully');
                }
                this.closeFilterSettingsModal();
                // Reload page to apply new filter order
                setTimeout(() => location.reload(), 500);
            } else {
                if (window.Toast) {
                    Toast.error(data?.message || 'Failed to save settings', 'Error');
                } else {
                    alert('Failed to save settings: ' + (data?.message || 'Unknown error'));
                }
            }
        })
        .catch(error => {
            console.error('Error saving filter settings:', error);
            if (window.Toast) {
                Toast.error('Failed to save filter settings. Please try again.', 'Error');
            } else {
                alert('Failed to save filter settings. Please try again.');
            }
        });
    }
}

export default ToolHandler;
