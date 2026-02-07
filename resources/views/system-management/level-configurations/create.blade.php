@extends('layouts.app')

@section('title', 'Add Funds to User')

@section('breadcrumb')
    <a href="{{ route('system-management.level-configurations.index') }}" class="breadcrumb-item">Level Configurations</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Add Funds</span>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header-left">
            <h2>Add Funds to User</h2>
            <p>Add amount to user's account balance and/or level progression</p>
        </div>

        <div class="page-header-right">
            <a href="{{ route('system-management.level-configurations.index') }}" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                </svg>
                Back
            </a>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('system-management.level-configurations.store') }}" method="POST">
            @csrf

            <div class="form-grid form-grid-2">
                <div class="form-group">
                    <label for="user_search" class="form-label">User <span class="form-required">*</span></label>
                    <div class="search-select-container">
                        <input type="text" id="user_search" class="form-input" placeholder="Type to search users..."
                               autocomplete="off">
                        <select id="user_id" name="user_id" class="form-select @error('user_id') error @enderror" required style="display: none;">
                            <option value="">Choose a user...</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-level="{{ $user->current_level ?? 1 }}"
                                        data-tier="{{ $user->tier ?? 'Bronze' }}"
                                        {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }}) -
                                    Level {{ $user->current_level ?? 1 }} {{ $user->tier ?? 'Bronze' }}
                                </option>
                            @endforeach
                        </select>
                        <div id="user_dropdown" class="search-dropdown" style="display: none;">
                            <div id="user_results" class="search-results">
                                <!-- Results will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                    @error('user_id')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount" class="form-label">Amount <span class="form-required">*</span></label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01"
                        class="form-input @error('amount') error @enderror" value="{{ old('amount') }}"
                        placeholder="Enter amount to add" required>
                    @error('amount')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                {{-- <div class="form-group">
                    <label for="reason" class="form-label">Reason (Optional)</label>
                    <input type="text" id="reason" name="reason" maxlength="255"
                        class="form-input @error('reason') error @enderror" value="{{ old('reason') }}"
                        placeholder="Optional reason for this fund addition">
                    @error('reason')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div> --}}
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-info">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    Create
                </button>
                <a href="{{ route('system-management.level-configurations.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('user_search');
            const userSelect = document.getElementById('user_id');
            const dropdown = document.getElementById('user_dropdown');
            const resultsContainer = document.getElementById('user_results');
            const options = Array.from(userSelect.options);

            let selectedIndex = -1;

            // Show all users initially
            showAllUsers();

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                selectedIndex = -1;

                if (searchTerm === '') {
                    showAllUsers();
                } else {
                    filterUsers(searchTerm);
                }

                dropdown.style.display = 'block';
            });

            // Show dropdown on focus
            searchInput.addEventListener('focus', function() {
                if (resultsContainer.children.length > 0) {
                    dropdown.style.display = 'block';
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });

            // Keyboard navigation
            searchInput.addEventListener('keydown', function(e) {
                const items = resultsContainer.querySelectorAll('.search-result-item');

                if (items.length === 0) return;

                // Remove previous selection
                items.forEach(item => item.classList.remove('selected'));

                switch(e.key) {
                    case 'ArrowDown':
                        e.preventDefault();
                        selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                        items[selectedIndex].classList.add('selected');
                        items[selectedIndex].scrollIntoView({ block: 'nearest' });
                        break;
                    case 'ArrowUp':
                        e.preventDefault();
                        selectedIndex = Math.max(selectedIndex - 1, 0);
                        items[selectedIndex].classList.add('selected');
                        items[selectedIndex].scrollIntoView({ block: 'nearest' });
                        break;
                    case 'Enter':
                        e.preventDefault();
                        if (selectedIndex >= 0) {
                            selectUser(items[selectedIndex]);
                        }
                        break;
                    case 'Escape':
                        dropdown.style.display = 'none';
                        selectedIndex = -1;
                        break;
                }
            });

            function showAllUsers() {
                resultsContainer.innerHTML = '';
                options.slice(1).forEach(option => { // Skip the first "Choose a user..." option
                    if (option.value) {
                        createUserItem(option);
                    }
                });
            }

            function filterUsers(searchTerm) {
                resultsContainer.innerHTML = '';
                const filteredOptions = options.slice(1).filter(option => {
                    if (!option.value) return false;
                    const name = option.getAttribute('data-name').toLowerCase();
                    const email = option.getAttribute('data-email').toLowerCase();
                    return name.includes(searchTerm) || email.includes(searchTerm);
                });

                if (filteredOptions.length === 0) {
                    resultsContainer.innerHTML = '<div class="no-results">No users found</div>';
                } else {
                    filteredOptions.forEach(option => createUserItem(option));
                }
            }

            function createUserItem(option) {
                const item = document.createElement('div');
                item.className = 'search-result-item';
                item.setAttribute('data-value', option.value);

                const tier = option.getAttribute('data-tier').toLowerCase();
                item.innerHTML = `
                    <div class="user-name">${option.getAttribute('data-name')}</div>
                    <div class="user-email">${option.getAttribute('data-email')}</div>
                    <div class="user-level tier-bg-${tier}">Level ${option.getAttribute('data-level')} ${option.getAttribute('data-tier')}</div>
                `;

                item.addEventListener('click', function() {
                    selectUser(this);
                });

                resultsContainer.appendChild(item);
            }

            function selectUser(item) {
                const value = item.getAttribute('data-value');
                const option = options.find(opt => opt.value === value);

                if (option) {
                    userSelect.value = value;
                    searchInput.value = `${option.getAttribute('data-name')} (${option.getAttribute('data-email')}) - Level ${option.getAttribute('data-level')} ${option.getAttribute('data-tier')}`;
                    dropdown.style.display = 'none';
                    selectedIndex = -1;
                }
            }
        });
    </script>
@endsection