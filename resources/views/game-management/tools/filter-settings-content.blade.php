<form action="{{ route('game-management.tools.save-filter-settings') }}" method="POST" id="filterSettingsForm">
    @csrf

    <!-- Category Order -->
    <div class="filter-settings-section">
        <h3 class="filter-settings-title">Category Filter Order</h3>
        <p class="filter-settings-description">Drag and drop to reorder categories in the filter menu</p>
        <ul id="categoryOrderList" class="sortable-list">
            @foreach($categoryOrder as $slug)
                @php $category = $categories->firstWhere('slug', $slug); @endphp
                @if($category)
                    <li class="sortable-item" data-value="{{ $category->slug }}">
                        <div class="sortable-handle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </div>
                        <span class="sortable-label">{{ $category->name }}</span>
                        <input type="hidden" name="category_order[]" value="{{ $category->slug }}">
                    </li>
                @endif
            @endforeach
            @foreach($categories as $category)
                @if(!in_array($category->slug, $categoryOrder))
                    <li class="sortable-item" data-value="{{ $category->slug }}">
                        <div class="sortable-handle">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </div>
                        <span class="sortable-label">{{ $category->name }}</span>
                        <input type="hidden" name="category_order[]" value="{{ $category->slug }}">
                    </li>
                @endif
            @endforeach
        </ul>
    </div>

    <!-- Tier Order -->
    <div class="filter-settings-section">
        <h3 class="filter-settings-title">Tier Filter Order</h3>
        <p class="filter-settings-description">Drag and drop to reorder tiers in the filter menu</p>
        <ul id="tierOrderList" class="sortable-list">
            @php
                $tierLabels = ['silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum'];
            @endphp
            @foreach($tierOrder as $tier)
                <li class="sortable-item" data-value="{{ $tier }}">
                    <div class="sortable-handle">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </div>
                    <span class="sortable-label">{{ $tierLabels[$tier] ?? ucfirst($tier) }}</span>
                    <input type="hidden" name="tier_order[]" value="{{ $tier }}">
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Sorting Order -->
    <div class="filter-settings-sections">
        <h3 class="filter-settings-title">Sorting Filter Order</h3>
        <p class="filter-settings-description">Drag and drop to reorder sorting options in the filter menu</p>
        <ul id="sortingOrderList" class="sortable-list">
            @php
                $sortingLabels = [
                    'most_relevant' => 'Most Relevant',
                    'most_popular' => 'Most Popular',
                    'highest_rated' => 'Highest Rated',
                    'price_low_to_high' => 'Price: Low to High',
                    'price_high_to_low' => 'Price: High to Low'
                ];
            @endphp
            @foreach($sortingOrder as $sort)
                <li class="sortable-item" data-value="{{ $sort }}">
                    <div class="sortable-handle">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </div>
                    <span class="sortable-label">{{ $sortingLabels[$sort] ?? ucfirst(str_replace('_', ' ', $sort)) }}</span>
                    <input type="hidden" name="sorting_order[]" value="{{ $sort }}">
                </li>
            @endforeach
        </ul>
    </div>

</form>

<script>
    // Re-initialize Sortable when content is loaded in modal
    if (typeof Sortable !== 'undefined') {
        setTimeout(function() {
            const lists = ['categoryOrderList', 'tierOrderList', 'sortingOrderList'];
            
            lists.forEach(listId => {
                const list = document.getElementById(listId);
                if (list && !list.hasAttribute('data-sortable-initialized')) {
                    Sortable.create(list, {
                        handle: '.sortable-handle',
                        animation: 150,
                        ghostClass: 'sortable-ghost',
                        chosenClass: 'sortable-chosen',
                        onEnd: function(evt) {
                            // Update hidden input order
                            const items = evt.to.querySelectorAll('.sortable-item');
                            items.forEach((item, index) => {
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
        }, 100);
    }
</script>

<style>
    .filter-settings-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .filter-settings-section:last-child {
        border-bottom: none;
    }

    .filter-settings-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .filter-settings-description {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .sortable-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sortable-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        cursor: move;
        transition: all 0.2s;
    }

    .sortable-item:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
    }

    .sortable-handle {
        display: flex;
        align-items: center;
        margin-right: 0.75rem;
        color: #6b7280;
        cursor: grab;
    }

    .sortable-handle:active {
        cursor: grabbing;
    }

    .sortable-label {
        flex: 1;
        font-weight: 500;
        color: #111827;
    }

    .sortable-ghost {
        opacity: 0.4;
        background: #e5e7eb;
    }

    .sortable-chosen {
        background: #dbeafe;
        border-color: #3b82f6;
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
