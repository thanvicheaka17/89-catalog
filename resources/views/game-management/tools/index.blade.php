@extends('layouts.app')

@section('title', 'Tools')

@section('breadcrumb')
    <a href="{{ route('game-management.tools.index') }}" class="breadcrumb-item">Top Tier Tools</a>
    <span class="breadcrumb-separator">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
    </span>
    <span class="breadcrumb-item active">Top Tier Tools</span>
@endsection

@section('content')
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="alert alert-success" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span class="alert-content">{{ session('success') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-error" data-auto-dismiss="5000">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <span class="alert-content">{{ session('error') }}</span>
            <button type="button" class="alert-close" onclick="closeAlert(this)">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-left">
            <h2>Top Tier Tools</h2>
            <p>Manage top tier tools</p>
        </div>

        <div class="page-header-right">
            <button type="button" onclick="openFilterSettingsModal()" class="btn btn-secondary" style="margin-right: 0.5rem;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Filter Settings
            </button>
            <a href="{{ route('game-management.tools.create') }}" class="btn btn-info">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add
            </a>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-header-left">
                <form action="{{ route('game-management.tools.index') }}" method="GET" class="per-page-form">
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <label for="per_page">Per page</label>
                    <select name="per_page" id="per_page" class="per-page-select" onchange="this.form.submit()">
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="200" {{ $perPage == 200 ? 'selected' : '' }}>200</option>
                    </select>
                </form>
                <button type="button" class="btn-filter" onclick="toggleFilters()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 0 1-.659 1.591l-5.432 5.432a2.25 2.25 0 0 0-.659 1.591v2.927a2.25 2.25 0 0 1-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 0 0-.659-1.591L3.659 7.409A2.25 2.25 0 0 1 3 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0 1 12 3Z" />
                    </svg>
                    <span>Filters</span>
                </button>
            </div>
            <div class="table-header-right">
                <form action="{{ route('game-management.tools.index') }}" method="GET" class="table-search-form">
                    @if (request('per_page'))
                        <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                    @endif
                    <div class="table-search-box">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input type="text" name="search" placeholder="Search tools..."
                            value="{{ request('search') }}">
                    </div>
                </form>
            </div>
        </div>

        <!-- Filters Panel -->
        <div class="filters-panel" id="filtersPanel">
            <form action="{{ route('game-management.tools.index') }}" method="GET" class="filters-form">
                @if (request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                @if (request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="filters-grid grid-3">
                    <div class="filter-group">
                        <label for="filter_status">Category</label>
                        <select name="category" id="filter_category" class="filter-select">
                            <option value="">All Categories</option>
                            @foreach ($toolCategories as $toolCategory)
                                <option value="{{ $toolCategory->slug }}"
                                    {{ request('category') == $toolCategory->slug ? 'selected' : '' }}>{{ $toolCategory->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter_is_demo">Tier</label>
                        <select name="tier" id="filter_tier" class="filter-select">
                            <option value="">All</option>
                            @foreach($orderedTiers as $tier)
                                <option value="{{ $tier['value'] }}" {{ request('tier') == $tier['value'] ? 'selected' : '' }}>
                                    {{ $tier['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter_sorting">Sorting</label>
                        <select name="sorting" id="filter_sorting" class="filter-select">
                            <option value="">All</option>
                            @foreach($orderedSorting as $sort)
                                <option value="{{ $sort['value'] }}" {{ request('sorting') == $sort['value'] ? 'selected' : '' }}>
                                    {{ $sort['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="filters-actions">
                    <button type="submit" class="btn btn-info">
                        Apply Filters
                    </button>
                    <a href="{{ route('game-management.tools.index', ['per_page' => request('per_page')]) }}"
                        class="btn btn-secondary">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="th-number" style="width: 40px;"></th>
                        <th class="th-number">#</th>
                        <th>Name</th>
                        <th>Image</th>
                        <th>Category</th>
                        <th>Slug</th>
                        <th>Rating</th>
                        <th>Rank</th>
                        <th>Badge</th>
                        <th>Tier</th>
                        <th>Price</th>
                        <th>Win Rate</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="toolsTableBody">
                    @forelse($tools as $index => $tool)
                        <tr data-tool-id="{{ $tool->id }}" data-display-order="{{ $tool->display_order }}">
                            <td class="drag-handle" style="cursor: move; text-align: center; padding: 8px;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px; color: #6b7280;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                </svg>
                            </td>
                            <td class="td-number" data-row-index="{{ $tools->firstItem() + $index }}">{{ $tools->firstItem() + $index }}</td>
                            <td>
                                <div class="banner-cell">
                                    <div class="banner-info">
                                        <div class="banner-title">{{ $tool->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <img src="{{ $tool->getImageUrl() }}" alt="{{ $tool->name }}" class="banner-image" onclick="showImagePreview('{{ $tool->getImageUrl() }}', '{{ addslashes($tool->name) }}')" style="cursor: pointer;">
                            </td>
                            <td>
                                <span class="badge text-blue bg-blue">{{ $tool->category->name }}</span>
                            </td>
                            <td>
                                <span class="badge text-indigo bg-indigo">{{ $tool->slug }}</span>
                            </td>
                            <td>
                                <span class="badge text-yellow bg-yellow">{{ $tool->rating }}</span>
                            </td>
                            <td>
                                <span class="badge text-green bg-green">{{ $tool->rank }}</span>
                            </td>
                            <td>
                                <span class="badge text-purple bg-purple">{{ ucfirst($tool->badge) }}</span>
                            </td>
                            <td>
                                <span class="badge text-orange bg-orange">{{ ucfirst($tool->tier) }}</span>
                            </td>
                            <td>
                                <span class="badge text-green bg-green">{{ $tool->price }}</span>
                            </td>
                            <td>
                                <span class="badge text-teal bg-teal">{{ $tool->win_rate_increase }}%</span>
                            </td>
                            <td>
                                <span class="date-cell">{{ $tool->created_at->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('game-management.tools.show', $tool) }}" class="btn-icon btn-icon-view"
                                        title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('game-management.tools.edit', $tool) }}" class="btn-icon btn-icon-edit"
                                        title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('game-management.tools.destroy', $tool) }}" method="POST"
                                        class="inline-form"
                                        data-confirm-delete="Are you sure you want to delete this tool?"
                                        data-delete-title="Delete Tool">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icon btn-icon-danger" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14">
                                <div class="empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                                    </svg>
                                    <h3>No top tier tools found</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tools->hasPages())
            <div class="table-footer">
                <div class="table-info">
                    Showing {{ number_format($tools->firstItem()) }} to {{ number_format($tools->lastItem()) }} of {{ number_format($tools->total()) }} tools
                </div>
                <div class="pagination-wrapper">
                    {{ $tools->withQueryString()->links('vendor.pagination.custom') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Sortable.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Filter Settings Modal -->
    <div id="filterSettingsModal" class="modal-overlay" style="display: none;">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h3 class="swal2-title swal-custom-title">Tool Filter Settings</h3>
                <button type="button" class="modal-close" onclick="closeFilterSettingsModal()" title="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="close-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body" id="filterSettingsModalBody" style="max-height: 70vh; overflow-y: auto;">
                <div style="text-align: center; padding: 2rem;">
                    <div class="spinner"></div>
                    <p>Loading filter settings...</p>
                </div>
            </div>
            <div class="modal-footer" id="filterSettingsModalFooter">
                <button type="button" class="btn btn-secondary" onclick="closeFilterSettingsModal()">Cancel</button>
                <button type="button" class="btn btn-info" id="saveFilterSettingsBtn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    Save Settings
                </button>
            </div>
        </div>
    </div>

    <!-- Sortable.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    @push('scripts')
    <script>
        // Initialize ToolHandler for drag-and-drop functionality and filter settings modal
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit for modules to load
            setTimeout(function() {
                if (typeof window.ToolHandler !== 'undefined') {
                    // Use relative URLs (paths only) to avoid mixed content issues
                    // The browser will automatically use the same protocol (HTTPS) as the current page
                    window.toolHandler = new window.ToolHandler({
                        firstItem: {{ $tools->firstItem() ?? 1 }},
                        updateOrderUrl: '{{ route("game-management.tools.update-order", [], false) }}',
                        filterSettingsUrl: '{{ route("game-management.tools.filter-settings", [], false) }}',
                        saveFilterSettingsUrl: '{{ route("game-management.tools.save-filter-settings", [], false) }}',
                        tbodyId: 'toolsTableBody'
                    });
                } else {
                    console.error('ToolHandler not found. Make sure tool-handler.js is imported in app.js.');
                }
            }, 100);
        });
    </script>
    @endpush

    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: #f3f4f6;
        }
        
        .sortable-chosen {
            cursor: grabbing;
        }
        
        .sortable-drag {
            opacity: 0.8;
        }
        
        .drag-handle:hover {
            color: #3b82f6 !important;
        }
        
        tbody tr {
            cursor: default;
        }
        
        tbody tr:hover .drag-handle {
            color: #3b82f6;
        }

        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
@endsection
