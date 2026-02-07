

class RTPConfigurationHandler {
    constructor(options = {}) {
        this.options = options;
        this.stats = {
            providers_processed: 0,
            elapsed_time: 0,
            start_time: null,
            timer_interval: null
        };
        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        // Bind modal close event
        const modal = document.getElementById('globalRTPSettingsModal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeGlobalRTPSettingsModal();
                }
            });
        }
    }

    viewProviderRTP(slug) {
        // Redirect to detailed RTP view for this provider
        window.location.href = `/admin/rtp-games?provider=${slug}`;
    }

    async viewProviderRTPDisabled(event) {
        event.preventDefault();
        event.stopPropagation();

        // Show alert if provider is not synced yet
        await SwalAlert.warning(
            'Provider Not Synced',
            'Please sync this provider first before viewing RTP data.'
        );
    }

    async syncProviderRTP(providerId, event, isSynced = false) {
        // Check if provider is already synced
        const syncBtn = event.target.closest('button');
        const syncedStatus = syncBtn?.getAttribute('data-synced') === 'true' || isSynced;

        if (syncedStatus) {
            // Show alert if already synced
            await SwalAlert.info(
                'Already Synced',
                'This provider has already been synced. If you want to sync again, please wait for the sync to complete or refresh the page.'
            );
            return;
        }

        const result = await SwalAlert.confirm({
            title: 'Sync Provider Data',
            message: 'Are you sure you want to sync RTP data for this provider? This may take a few moments.',
            confirmText: 'Yes, sync it!',
            confirmColor: '#16a34a',
            icon: 'question'
        });

        if (!result.isConfirmed) {
            return;
        }

        // Show loading state
        const originalHTML = syncBtn.innerHTML;
        syncBtn.innerHTML = '<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
        syncBtn.disabled = true;
        syncBtn.classList.add('loading');

        try {
            // Make API call to sync RTP data
            const response = await fetch(`/admin/rtp-configurations/provider/${providerId}/sync`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                Toast.success('RTP data synced successfully', 'Success');
                // Reload page to show updated data
                setTimeout(() => location.reload(), 1000);
            } else {
                Toast.error(data.message || 'Failed to sync RTP data', 'Error');
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.error('An error occurred while syncing RTP data', 'Error');
        } finally {
            // Restore button
            syncBtn.innerHTML = originalHTML;
            syncBtn.disabled = false;
            syncBtn.classList.remove('loading');
        }
    }

    async syncAllProviders(event) {
        const result = await SwalAlert.confirm({
            title: 'Sync All Providers',
            message: 'Are you sure you want to sync RTP data for ALL providers? This may take several minutes.',
            confirmText: 'Yes, sync all!',
            confirmColor: '#16a34a',
            icon: 'warning'
        });

        if (!result.isConfirmed) {
            return;
        }

        // Show the modal
        this.showSyncModal();

        // Disable the button
        const syncBtn = event.target.closest('button');
        syncBtn.disabled = true;
        syncBtn.classList.add('loading');

        try {
            // Make API call to sync all providers with progress updates
            const response = await fetch('/admin/rtp-configurations/sync-all-progress', {
                method: 'POST',
                headers: {
                    'Accept': 'text/plain',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let buffer = '';

            while (true) {
                const { done, value } = await reader.read();

                if (done) break;

                buffer += decoder.decode(value, { stream: true });
                const lines = buffer.split('\n');

                // Process complete lines
                for (let i = 0; i < lines.length - 1; i++) {
                    const line = lines[i].trim();
                    if (line) {
                        try {
                            const data = JSON.parse(line);
                            this.handleProgressUpdate(data);
                        } catch (e) {
                            console.warn('Failed to parse progress update:', line);
                        }
                    }
                }

                buffer = lines[lines.length - 1];
            }

        } catch (error) {
            console.error('Error:', error);
            this.stopTimer();
            this.addLogMessage('❌ Network error occurred', 'error');
            const closeBtn = document.getElementById('syncCloseBtn');
            if (closeBtn) closeBtn.style.display = 'block';
        } finally {
            // Restore button
            syncBtn.disabled = false;
            syncBtn.classList.remove('loading');
        }
    }

    async deleteAllData(event) {
        const result = await SwalAlert.confirm({
            title: 'Delete All RTP Data',
            message: 'Are you sure you want to delete all RTP game data? This action cannot be undone.',
            confirmText: 'Yes, delete everything!',
            confirmColor: '#dc2626',
            icon: 'warning'
        });

        if (!result.isConfirmed) {
            return;
        }

        // Disable the button
        const deleteBtn = event.target.closest('button');
        const originalHTML = deleteBtn.innerHTML;
        deleteBtn.innerHTML = '<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Deleting...';
        deleteBtn.disabled = true;
        deleteBtn.classList.add('loading');

        try {
            // Make API call to delete all data
            const response = await fetch('/admin/rtp-configurations/delete-all-data', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                console.error('HTTP Error:', response.status, response.statusText);
                const errorText = await response.text();
                console.error('Error response:', errorText);
                Toast.error(`HTTP ${response.status}: ${response.statusText}`, 'Error');
                return;
            }

            const data = await response.json();

            if (data.success) {
                Toast.success(data.message || 'All RTP data deleted successfully', 'Success');
                // Reload page to show updated state
                setTimeout(() => location.reload(), 1500);
            } else {
                Toast.error(data.message || 'Failed to delete RTP data', 'Error');
            }
        } catch (error) {
            console.error('Network/Parse Error:', error);
            Toast.error('Network error: ' + error.message, 'Error');
        } finally {
            // Restore button
            deleteBtn.innerHTML = originalHTML;
            deleteBtn.disabled = false;
            deleteBtn.classList.remove('loading');
        }
    }

    showSyncModal() {
        const modal = document.getElementById('syncAllModal');
        if (modal) {
            modal.style.display = 'flex';
            this.clearLogs();
            this.resetStats();
            this.startTimer();
            this.addLogMessage('🚀 Starting sync process...', 'info');
            this.updateProgress(0, 'Initializing...');
        }
    }

    resetStats() {
        this.stats = {
            providers_processed: 0,
            elapsed_time: 0,
            start_time: Date.now(),
            timer_interval: null
        };
        this.updateStatsDisplay();
    }

    startTimer() {
        // Clear any existing timer
        if (this.stats.timer_interval) {
            clearInterval(this.stats.timer_interval);
        }

        this.stats.timer_interval = setInterval(() => {
            this.stats.elapsed_time = Math.floor((Date.now() - this.stats.start_time) / 1000);
            this.updateStatsDisplay();
        }, 1000);
    }

    stopTimer() {
        if (this.stats.timer_interval) {
            clearInterval(this.stats.timer_interval);
            this.stats.timer_interval = null;
        }
    }

    updateStatsDisplay() {
        const providersProcessed = document.getElementById('syncProvidersProcessed');
        const elapsedTime = document.getElementById('syncElapsedTime');

        if (providersProcessed) {
            providersProcessed.textContent = this.stats.providers_processed;
        }
        if (elapsedTime) {
            elapsedTime.textContent = this.formatElapsedTime(this.stats.elapsed_time);
        }
    }

    closeSyncModal() {
        const modal = document.getElementById('syncAllModal');
        if (modal) {
            modal.style.display = 'none';
            this.stopTimer();
            location.reload(); // Reload to show updated data
        }
    }

    updateProgress(percentage, text) {
        const progressFill = document.getElementById('syncProgressFill');
        const progressPercentage = document.getElementById('syncProgressPercentage');
        const progressLabel = document.getElementById('syncProgressLabel');

        if (progressFill) {
            progressFill.style.width = percentage + '%';
        }
        
        if (progressPercentage) {
            progressPercentage.textContent = percentage + '%';
        }

        if (progressLabel) {
            progressLabel.textContent = text;
        }
    }

    addLogMessage(message, type = 'info') {
        const logsContainer = document.getElementById('syncLogsContainer');
        if (!logsContainer) return;

        const logItem = document.createElement('div');
        logItem.className = `sync-log-item sync-log-item-${type}`;

        const timestamp = new Date().toLocaleTimeString();
        const iconSvg = this.getLogIcon(type);

        logItem.innerHTML = `
            <div class="sync-log-icon">
                ${iconSvg}
            </div>
            <div class="sync-log-content">
                <div class="sync-log-message">${message}</div>
                <div class="sync-log-timestamp" id="syncLogTimestamp${Date.now()}">${timestamp}</div>
            </div>
        `;

        logsContainer.appendChild(logItem);
        logsContainer.scrollTop = logsContainer.scrollHeight;

        // Update log count
        this.updateLogCount();
    }

    clearLogs() {
        const logsContainer = document.getElementById('syncLogsContainer');
        if (logsContainer) {
            logsContainer.innerHTML = '';
            this.updateLogCount();
        }
    }

    updateLogCount() {
        const logsContainer = document.getElementById('syncLogsContainer');
    }

    getLogIcon(type) {
        const icons = {
            info: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="sync-icon-main">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"></path>
                                </svg>`,
            success: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>`,
            error: `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>`
        };
        return icons[type] || icons.info;
    }

    handleProgressUpdate(data) {
        switch (data.type) {
            case 'start':
                this.addLogMessage('🚀 ' + data.data.message, 'info');
                break;

            case 'progress':
                // Update provider count as we progress
                this.stats.providers_processed = data.data.current;
                const progressPercent = Math.round((data.data.current / data.data.total) * 100);
                this.updateProgress(progressPercent, data.data.message);
                this.addLogMessage(`🔄 ${data.data.message}`, 'info');
                this.updateStatsDisplay();
                break;

            case 'success':
                // Provider successfully synced, count it
                this.stats.providers_processed = Math.max(this.stats.providers_processed, data.data.current || 0);
                this.addLogMessage(data.data.message, 'success');
                this.updateStatsDisplay();
                break;

            case 'error':
                // Still count the provider as processed even if it failed
                this.stats.providers_processed = Math.max(this.stats.providers_processed, data.data.current || 0);
                this.addLogMessage(data.data.message, 'error');
                this.updateStatsDisplay();
                break;

            case 'complete':
                this.stopTimer();
                this.updateProgress(100, 'Sync completed!');
                this.showFinalResults(data.data);
                this.updateStatsDisplay();
                break;
        }
    }


    formatElapsedTime(seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    showFinalResults(data) {
        // Show the modal footer with close button
        const modalFooter = document.getElementById('syncModalFooter');
        if (modalFooter) {
            modalFooter.style.display = 'flex';
        }

        // Update success and fail count displays
        const successCountEl = document.getElementById('syncSuccessCount');
        const failCountEl = document.getElementById('syncFailCount');

        if (successCountEl) {
            successCountEl.textContent = data.successful_syncs || 0;
        }
        if (failCountEl) {
            failCountEl.textContent = data.failed_syncs || 0;
        }

        this.addLogMessage(`📊 Final Results:`, 'info');
        this.addLogMessage(`Total providers: ${data.total_providers}`, 'info');
        this.addLogMessage(`Successful syncs: ${data.successful_syncs}`, 'success');
        this.addLogMessage(`Failed syncs: ${data.failed_syncs}`, data.failed_syncs > 0 ? 'error' : 'success');

        if (data.errors && data.errors.length > 0) {
            // this.addLogMessage('❌ Errors encountered:', 'error');
            data.errors.forEach(error => {
                this.addLogMessage(`• ${error}`, 'error');
            });
        }
    }

    openGlobalRTPSettings() {
        // Open global RTP settings modal
        const modal = document.getElementById('globalRTPSettingsModal');
        if (modal) {
            modal.style.display = 'flex';
            // Load current settings for all providers
            this.loadGlobalRTPSettings();
        }
    }

    closeGlobalRTPSettingsModal() {
        const modal = document.getElementById('globalRTPSettingsModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    async loadGlobalRTPSettings() {
        const container = document.getElementById('gamesSettingsContainer');
        container.innerHTML = '<div class="loading-state"><div class="loading-spinner"></div><p>Loading global RTP configuration...</p></div>';

        try {
            const response = await fetch('/admin/rtp-configurations/global-settings', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success && data.data && data.data.providers && data.data.global_config) {
                this.renderGlobalRTPSettings(data.data.global_config, data.data.providers);
            } else {
                const errorMessage = data.message || 'Invalid response structure';
                container.innerHTML = '<div class="error-state"><p>Error loading settings: ' + errorMessage + '</p></div>';
            }
        } catch (error) {
            console.error('Error:', error);
            container.innerHTML = '<div class="error-state"><p>An error occurred while loading settings</p></div>';
        }
    }

    renderGlobalRTPSettings(globalConfig, providers) {
        const container = document.getElementById('gamesSettingsContainer');
        const providerList = providers.map(p => p.name).join(', ');

        const html = `
            <div class="global-settings-card">
                <div class="global-settings-body">
                    <div class="settings-row">
                        <div class="setting-group">
                            <label>Global RTP Range (%)</label>
                            <div class="input-pair">
                                <div class="input-wrapper">
                                    <input type="number" id="min_rtp" name="min_rtp"
                                           value="${globalConfig.min_rtp}" min="0" max="100" step="1"
                                           placeholder="Min RTP" required>
                                    <span class="input-label">Min</span>
                                </div>
                                <div class="input-wrapper">
                                    <input type="number" id="max_rtp" name="max_rtp"
                                           value="${globalConfig.max_rtp}" min="0" max="100" step="1"
                                           placeholder="Max RTP" required>
                                    <span class="input-label">Max</span>
                                </div>
                            </div>
                        </div>
                        <div class="setting-group">
                            <label>Global Pola Range (%)</label>
                            <div class="input-pair">
                                <div class="input-wrapper">
                                    <input type="number" id="min_pola" name="min_pola"
                                           value="${globalConfig.min_pola}" min="0" max="100" step="1"
                                           placeholder="Min Pola" required>
                                    <span class="input-label">Min</span>
                                </div>
                                <div class="input-wrapper">
                                    <input type="number" id="max_pola" name="max_pola"
                                           value="${globalConfig.max_pola}" min="0" max="100" step="1"
                                           placeholder="Max Pola" required>
                                    <span class="input-label">Max</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = html;
    }

    async saveGlobalRTPSettings() {
        const saveBtn = document.querySelector('#globalRTPSettingsModal .btn-primary');
        const spinner = saveBtn.querySelector('.loading-spinner');
        const originalHTML = saveBtn.innerHTML;

        // Show loading state
        saveBtn.disabled = true;
        if (spinner) {
            spinner.style.display = 'inline-block';
        }

        // Get global config values
        const payload = {
            min_rtp: parseInt(document.getElementById('min_rtp').value),
            max_rtp: parseInt(document.getElementById('max_rtp').value),
            min_pola: parseInt(document.getElementById('min_pola').value),
            max_pola: parseInt(document.getElementById('max_pola').value)
        };

        try {
            const response = await fetch('/admin/rtp-configurations/global-settings', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                Toast.success('Global RTP settings applied to all providers successfully', 'Success');
                this.closeGlobalRTPSettingsModal();
            } else {
                Toast.error('Failed to save configuration: ' + data.message, 'Error');
            }
        } catch (error) {
            console.error('Error:', error);
            Toast.error('An error occurred while saving configuration', 'Error');
        } finally {
            // Restore button
            saveBtn.disabled = false;
            if (spinner) {
                spinner.style.display = 'none';
            }
            // Restore original HTML (which includes the hidden spinner)
            saveBtn.innerHTML = originalHTML;
        }
    }
}

// Function to enhance RTPConfigurationHandler
window.enhanceRTPHandler = function() {
    // Enhance the RTPConfigurationHandler with real-time statistics
    if (window.rtpConfigurationHandler) {
        // Add syncing state tracking
        window.rtpConfigurationHandler.isSyncing = false;
        // Add escape key and click outside to close modal (only when not syncing)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('syncAllModal');
                if (modal && modal.style.display === 'flex' && !window.rtpConfigurationHandler.isSyncing) {
                    window.rtpConfigurationHandler.closeSyncModal();
                }
            }
        });

        // Click outside modal to close (only when not syncing)
        const modal = document.getElementById('syncAllModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal && !window.rtpConfigurationHandler.isSyncing) {
                    window.rtpConfigurationHandler.closeSyncModal();
                }
            });
        }
        // Add statistics tracking
        window.rtpConfigurationHandler.stats = {
            providers_processed: 0,
            elapsed_time: 0,
            start_time: null,
            timer_interval: null
        };

        // Override showSyncModal to initialize stats and timer
        const originalShowSyncModal = window.rtpConfigurationHandler.showSyncModal;
        window.rtpConfigurationHandler.showSyncModal = function() {
            const modal = document.getElementById('syncAllModal');
            if (modal) {
                modal.style.display = 'flex';
                document.documentElement.classList.add('hide-overflow');
                document.body.classList.add('hide-overflow');

                const headerCloseBtn = document.getElementById('syncCloseBtn');
                if (headerCloseBtn) {
                    headerCloseBtn.disabled = true;
                    headerCloseBtn.style.opacity = '0.5';
                }

                const footerCloseBtn = document.getElementById('syncModalCloseBtn');
                if (footerCloseBtn) {
                    footerCloseBtn.disabled = true;
                    footerCloseBtn.style.opacity = '0.5';
                }

                this.clearLogs();
                this.resetStats();
                this.startTimer();
                this.addLogMessage('🚀 Starting sync process...', 'info');
                this.updateProgress(0, 'Initializing...');
            }
        };

        // Add new methods
        window.rtpConfigurationHandler.resetStats = function() {
            this.stats = {
                providers_processed: 0,
                elapsed_time: 0,
                start_time: Date.now(),
                timer_interval: null
            };
            this.updateStatsDisplay();
        };

        window.rtpConfigurationHandler.startTimer = function() {
            if (this.stats.timer_interval) {
                clearInterval(this.stats.timer_interval);
            }

            this.stats.timer_interval = setInterval(() => {
                this.stats.elapsed_time = Math.floor((Date.now() - this.stats.start_time) / 1000);
                this.updateStatsDisplay();
            }, 1000);
        };

        window.rtpConfigurationHandler.stopTimer = function() {
            if (this.stats.timer_interval) {
                clearInterval(this.stats.timer_interval);
                this.stats.timer_interval = null;
            }
        };

        window.rtpConfigurationHandler.updateStatsDisplay = function() {
            const providersProcessed = document.getElementById('syncProvidersProcessed');
            const elapsedTime = document.getElementById('syncElapsedTime');

            if (providersProcessed) {
                providersProcessed.textContent = this.stats.providers_processed;
            }
            if (elapsedTime) {
                elapsedTime.textContent = this.formatElapsedTime(this.stats.elapsed_time);
            }
        };

        window.rtpConfigurationHandler.enableCloseButton = function() {
            // Enable header close button
            const headerCloseBtn = document.getElementById('syncCloseBtn');
            if (headerCloseBtn) {
                headerCloseBtn.disabled = false;
                headerCloseBtn.style.opacity = '1';
            }

            // Enable footer close button
            const footerCloseBtn = document.getElementById('syncModalCloseBtn');
            if (footerCloseBtn) {
                footerCloseBtn.disabled = false;
                footerCloseBtn.style.opacity = '1';
            }
        };

        // Override handleProgressUpdate to track statistics
        const originalHandleProgressUpdate = window.rtpConfigurationHandler.handleProgressUpdate;
        window.rtpConfigurationHandler.handleProgressUpdate = function(data) {
            switch (data.type) {
                case 'start':
                    this.addLogMessage('🚀 ' + data.data.message, 'info');
                    break;

                case 'progress':
                    this.stats.providers_processed = data.data.current;
                    const progressPercent = Math.round((data.data.current / data.data.total) * 100);
                    this.updateProgress(progressPercent, data.data.message);
                    this.addLogMessage(`🔄 ${data.data.message}`, 'info');
                    this.updateStatsDisplay();
                    break;

                case 'success':
                    this.stats.providers_processed = Math.max(this.stats.providers_processed, data.data.current || 0);
                    this.addLogMessage(data.data.message, 'success');
                    this.updateStatsDisplay();
                    break;

                case 'error':
                    this.stats.providers_processed = Math.max(this.stats.providers_processed, data.data.current || 0);
                    this.addLogMessage(data.data.message, 'error');
                    this.updateStatsDisplay();
                    break;

                case 'complete':
                    this.stopTimer();
                    this.isSyncing = false;
                    this.updateProgress(100, 'Sync completed!');
                    this.showFinalResults(data.data);
                    this.enableCloseButton();
                    this.updateStatsDisplay();
                    break;
            }
        };

        // Override syncAllProviders to use our enhanced modal
        const originalSyncAllProviders = window.rtpConfigurationHandler.syncAllProviders;
        window.rtpConfigurationHandler.syncAllProviders = async function(event) {

            const result = await SwalAlert.confirm({
                title: 'Sync All Providers',
                message: 'Are you sure you want to sync RTP data for ALL providers? This may take several minutes.',
                confirmText: 'Yes, sync all!',
                confirmColor: '#16a34a',
                icon: 'warning'
            });

            if (!result.isConfirmed) {
                return;
            }

            // Show the modal
            this.showSyncModal();

            // Set syncing state
            this.isSyncing = true;

            // Disable the button
            const syncBtn = event.target.closest('button');
            syncBtn.disabled = true;
            syncBtn.classList.add('loading');

            try {
                // Make API call to sync all providers with progress updates
                const response = await fetch('/admin/rtp-configurations/sync-all-progress', {
                    method: 'POST',
                    headers: {
                        'Accept': 'text/plain',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const reader = response.body.getReader();
                const decoder = new TextDecoder();
                let buffer = '';

                while (true) {
                    const { done, value } = await reader.read();

                    if (done) break;

                    buffer += decoder.decode(value, { stream: true });
                    const lines = buffer.split('\n');

                    for (let i = 0; i < lines.length - 1; i++) {
                        const line = lines[i].trim();
                        if (line) {
                            try {
                                const data = JSON.parse(line);
                                this.handleProgressUpdate(data);
                            } catch (e) {
                                console.warn('Failed to parse progress update:', line);
                            }
                        }
                    }

                    buffer = lines[lines.length - 1];
                }

            } catch (error) {
                console.error('Error:', error);
                this.stopTimer();
                this.isSyncing = false;
                this.addLogMessage('❌ Network error occurred', 'error');
                this.enableCloseButton();
            } finally {
                // Restore button
                syncBtn.disabled = false;
                syncBtn.classList.remove('loading');
                this.isSyncing = false;
                setTimeout(() => {
                    this.enableCloseButton();
                }, 1000);
            }
        };

        // Add page refresh/close warning during sync
        window.addEventListener('beforeunload', function(e) {
            if (window.rtpConfigurationHandler && window.rtpConfigurationHandler.isSyncing) {
                e.preventDefault();
                e.returnValue = 'Data synchronization is in progress. Are you sure you want to leave? This will stop the sync process.';
                return e.returnValue;
            }
        });

        // Override closeSyncModal to stop timer and prevent closing during sync
        const originalCloseSyncModal = window.rtpConfigurationHandler.closeSyncModal;
        window.rtpConfigurationHandler.closeSyncModal = function() {
            // Prevent closing modal while syncing is in progress
            if (this.isSyncing) {
                return;
            }

            const modal = document.getElementById('syncAllModal');
            if (modal) {
                modal.style.display = 'none';
                document.documentElement.classList.remove('hide-overflow');
                document.body.classList.remove('hide-overflow');

                this.isSyncing = false;
                this.stopTimer();
                location.reload();
            }
        };
    }
}

// The handler will be enhanced automatically by app.js when it's instantiated

// Also check on window load as a fallback
window.addEventListener('load', function() {
    if (window.rtpConfigurationHandler) {
        window.enhanceRTPHandler();
    }
});

export default RTPConfigurationHandler;