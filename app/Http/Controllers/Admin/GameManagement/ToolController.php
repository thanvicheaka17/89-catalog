<?php

namespace App\Http\Controllers\Admin\GameManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use App\Models\SiteSetting;
class ToolController extends Controller
{
    /**
     * Display a listing of tools.
     */
    public function index(Request $request)
    {
        $query = Tool::query();
        $toolCategories = $this->getOrderedCategories();

        if ($request->has('category') && $request->category) {
            $toolCategory = ToolCategory::where('slug', $request->category)->first();
            if ($toolCategory) {
                $query->where('category_id', $toolCategory->id);
            }
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('tier') && $request->tier) {
            $tier = $request->input('tier');
            $query->where('tier', $tier);
        }

        // Always order by display_order first (manual positioning)
        $query->orderBy('display_order', 'asc');
        
        $sorting = $request->input('sorting', '');
        switch ($sorting) {
            case '':
            case 'all':
                $query->orderBy('created_at', 'desc');
                break;
            case 'most_relevant':
                $query->orderBy('rating', 'desc')
                      ->orderBy('user_count', 'desc')
                      ->orderBy('created_at', 'desc');
                break;
            case 'most_popular':
                $query->orderBy('user_count', 'desc')
                      ->orderBy('rating', 'desc');
                break;
            case 'highest_rated':
                $query->orderBy('rating', 'desc')
                      ->orderBy('user_count', 'desc');
                break;
            case 'price_low_to_high':
                $query->orderBy('price', 'asc')
                      ->orderBy('rating', 'desc');
                break;
            case 'price_high_to_low':
                $query->orderBy('price', 'desc')
                      ->orderBy('rating', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $tools = $query->paginate($perPage);

        $orderedTiers = $this->getOrderedTiers();
        $orderedSorting = $this->getOrderedSortingOptions();

        return view('game-management.tools.index', compact('tools', 'perPage', 'toolCategories', 'orderedTiers', 'orderedSorting'));
    }

    /**
     * Show the form for creating a new tool.
     */
    public function create()
    {
        $toolCategories = ToolCategory::all();

        return view('game-management.tools.create', compact('toolCategories'));
    }

    /**
     * Store a newly created tool.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:tool_categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tools',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:10240',
            'rating' => 'nullable|numeric|min:0|max:5',
            'user_count' => 'nullable|integer|min:0',
            'active_hours' => 'nullable|integer|min:0',
            'rank' => 'nullable|integer|min:0',
            'badge' => 'nullable|string|max:255',
            'tier' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'win_rate_increase' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/tools');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/tools/' . $filename;
        }

        unset($validated['image']);

        $validated['slug'] = Str::slug($validated['slug']);

        $randomData = Tool::randomToolData();

        // Increment all existing tools' display_order by 1
        DB::transaction(function () use (&$tool, $validated, $randomData) {
            // Increment all existing tools' display_order by 1
            Tool::query()->increment('display_order');

            // Create new tool with display_order = 1 (first position)
            $tool = Tool::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
                'image_path' => $validated['image_path'],
                'rating' => $randomData['rating'],
                'user_count' => $randomData['user_count'],
                'active_hours' => $randomData['active_hours'],
                'rank' => $randomData['rank'],
                'badge' => $randomData['badge'],
                'tier' => $validated['tier'] ?? $randomData['tier'],
                'price' => $validated['price'] ?? $randomData['price'],
                'win_rate_increase' => $randomData['win_rate_increase'],
                'category_id' => $validated['category_id'],
                'display_order' => 1,
            ]);
        });

        // Create and broadcast notification for the new top tier tool
        $this->topTierToolNotification($tool);

        return redirect()->route('game-management.tools.index')->with('success', 'Tool created successfully');
    }

    /**
     * Display the specified tool.
     */
    public function show(Tool $tool)
    {
        $toolCategories = ToolCategory::all();
        return view('game-management.tools.show', compact('tool', 'toolCategories'));
    }

    /**
     * Show the form for editing the specified tool.
     */
    public function edit(Tool $tool)
    {
        $toolCategories = ToolCategory::all();

        return view('game-management.tools.edit', compact('tool', 'toolCategories'));
    }

    /**
     * Update the specified tool.
     */
    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tools,slug,' . $tool->id,
            'description' => 'nullable|string|max:1000',
            'category_id' => 'required|exists:tool_categories,id',
            'tier' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($request->input('remove_image') === '1') {
            if (!$request->hasFile('image')) {
                return redirect()->back()
                    ->withErrors(['image' => 'Please upload a new image when removing the current one.'])
                    ->withInput();
            }
            if ($tool->image_path) {
                $this->deleteImageFile($tool->image_path);
            }
        }

        if ($request->hasFile('image')) {
            if ($tool->image_path && $request->input('remove_image') !== '1') {
                $this->deleteImageFile($tool->image_path);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/tools');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/tools/' . $filename;
        } else {
            $validated['image_path'] = $tool->image_path;
        }

        unset($validated['image']);

        if ($validated['tier']) {
            $tool->tier = $validated['tier'];
        }
        if ($validated['price']) {
            $tool->price = $validated['price'];
        }

        $tool->update($validated);

        return redirect()->route('game-management.tools.index')->with('success', 'Tool updated successfully');
    }

    /**
     * Destroy the specified tool.
     */
    public function destroy(Tool $tool)
    {
        $tool->delete();

        if ($tool->image_path) {
            $this->deleteImageFile($tool->image_path);
        }

        return redirect()->route('game-management.tools.index')->with('success', 'Tool deleted successfully');
    }

    /**
     * Move a tool up in the display order.
     */
    public function moveUp(Tool $tool)
    {
        // Get the tool with the next lower display_order
        $previousTool = Tool::where('display_order', '<', $tool->display_order)
            ->orderBy('display_order', 'desc')
            ->first();

        if ($previousTool) {
            // Swap display_order values
            $tempOrder = $tool->display_order;
            $tool->display_order = $previousTool->display_order;
            $previousTool->display_order = $tempOrder;

            $tool->save();
            $previousTool->save();
        }

        return redirect()->back()->with('success', 'Tool moved up successfully');
    }

    /**
     * Move a tool down in the display order.
     */
    public function moveDown(Tool $tool)
    {
        // Get the tool with the next higher display_order
        $nextTool = Tool::where('display_order', '>', $tool->display_order)
            ->orderBy('display_order', 'asc')
            ->first();

        if ($nextTool) {
            // Swap display_order values
            $tempOrder = $tool->display_order;
            $tool->display_order = $nextTool->display_order;
            $nextTool->display_order = $tempOrder;

            $tool->save();
            $nextTool->save();
        }

        return redirect()->back()->with('success', 'Tool moved down successfully');
    }

    /**
     * Update the display order of multiple tools.
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'order.*.id' => 'required|uuid|exists:tools,id',
            'order.*.display_order' => 'required|integer|min:1',
        ]);

        try {
            foreach ($validated['order'] as $item) {
                Tool::where('id', $item['id'])->update([
                    'display_order' => $item['display_order']
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Tool order updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update tool order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get consolidated tool filter settings
     */
    private function getToolFilterSettings()
    {
        $filterSettings = SiteSetting::get('tool_filtering', null);
        
        // Default structure (lazy-loaded for category order)
        $getDefaults = function() {
            return [
                'tool_filter_sorting_order' => [
                    'most_relevant',
                    'most_popular',
                    'highest_rated',
                    'price_low_to_high',
                    'price_high_to_low'
                ],
                'tool_filter_category_order' => ToolCategory::all()->pluck('slug')->toArray(),
                'tool_filter_tier_order' => ['silver', 'gold', 'platinum']
            ];
        };
        
        // If no consolidated setting exists, check individual settings for backward compatibility
        if (!$filterSettings || !is_array($filterSettings)) {
            // Try to migrate from old individual settings
            $categoryOrder = SiteSetting::get('tool_filter_category_order', null);
            $tierOrder = SiteSetting::get('tool_filter_tier_order', null);
            $sortingOrder = SiteSetting::get('tool_filter_sorting_order', null);
            
            if ($categoryOrder || $tierOrder || $sortingOrder) {
                // Migrate old settings to new consolidated format
                $defaults = $getDefaults();
                $filterSettings = [
                    'tool_filter_sorting_order' => is_array($sortingOrder) ? $sortingOrder : $defaults['tool_filter_sorting_order'],
                    'tool_filter_category_order' => is_array($categoryOrder) ? $categoryOrder : $defaults['tool_filter_category_order'],
                    'tool_filter_tier_order' => is_array($tierOrder) ? $tierOrder : $defaults['tool_filter_tier_order']
                ];
                // Save migrated settings
                SiteSetting::set('tool_filtering', $filterSettings, 'tools');
            } else {
                $filterSettings = $getDefaults();
            }
        }
        
        // Ensure all keys exist and are arrays
        $defaults = $getDefaults();
        return [
            'tool_filter_sorting_order' => is_array($filterSettings['tool_filter_sorting_order'] ?? null) 
                ? $filterSettings['tool_filter_sorting_order'] 
                : $defaults['tool_filter_sorting_order'],
            'tool_filter_category_order' => is_array($filterSettings['tool_filter_category_order'] ?? null) 
                ? $filterSettings['tool_filter_category_order'] 
                : $defaults['tool_filter_category_order'],
            'tool_filter_tier_order' => is_array($filterSettings['tool_filter_tier_order'] ?? null) 
                ? $filterSettings['tool_filter_tier_order'] 
                : $defaults['tool_filter_tier_order']
        ];
    }

    /**
     * Get ordered categories based on settings
     */
    private function getOrderedCategories()
    {
        $categories = ToolCategory::all();
        $filterSettings = $this->getToolFilterSettings();
        $orderSetting = $filterSettings['tool_filter_category_order'];
        
        if ($orderSetting && is_array($orderSetting)) {
            // Sort categories based on saved order
            return $categories->sortBy(function ($category) use ($orderSetting) {
                $index = array_search($category->slug, $orderSetting);
                return $index !== false ? $index : 999;
            })->values();
        }
        
        return $categories;
    }

    /**
     * Get ordered tier options based on settings
     */
    private function getOrderedTiers()
    {
        $defaultTiers = [
            ['value' => 'silver', 'label' => 'Silver'],
            ['value' => 'gold', 'label' => 'Gold'],
            ['value' => 'platinum', 'label' => 'Platinum'],
        ];
        
        $filterSettings = $this->getToolFilterSettings();
        $orderSetting = $filterSettings['tool_filter_tier_order'];
        
        if ($orderSetting && is_array($orderSetting)) {
            return collect($orderSetting)->map(function ($tierValue) use ($defaultTiers) {
                $tier = collect($defaultTiers)->firstWhere('value', $tierValue);
                return $tier ?: ['value' => $tierValue, 'label' => ucfirst($tierValue)];
            })->filter()->values()->toArray();
        }
        
        return $defaultTiers;
    }

    /**
     * Get ordered sorting options based on settings
     */
    private function getOrderedSortingOptions()
    {
        $defaultSorting = [
            ['value' => 'most_relevant', 'label' => 'Most Relevant'],
            ['value' => 'most_popular', 'label' => 'Most Popular'],
            ['value' => 'highest_rated', 'label' => 'Highest Rated'],
            ['value' => 'price_low_to_high', 'label' => 'Price: Low to High'],
            ['value' => 'price_high_to_low', 'label' => 'Price: High to Low'],
        ];
        
        $filterSettings = $this->getToolFilterSettings();
        $orderSetting = $filterSettings['tool_filter_sorting_order'];
        
        if ($orderSetting && is_array($orderSetting)) {
            return collect($orderSetting)->map(function ($sortValue) use ($defaultSorting) {
                $sort = collect($defaultSorting)->firstWhere('value', $sortValue);
                return $sort ?: ['value' => $sortValue, 'label' => ucfirst(str_replace('_', ' ', $sortValue))];
            })->filter()->values()->toArray();
        }
        
        return $defaultSorting;
    }

    /**
     * Show filter order settings page
     */
    public function filterSettings()
    {
        try {
            $categories = ToolCategory::all();
            $filterSettings = $this->getToolFilterSettings();
            
            // Get orders from consolidated settings
            $categoryOrder = $filterSettings['tool_filter_category_order'];
            $tierOrder = $filterSettings['tool_filter_tier_order'];
            $sortingOrder = $filterSettings['tool_filter_sorting_order'];
            
            // Ensure all are arrays
            if (!is_array($categoryOrder)) {
                $categoryOrder = $categories->pluck('slug')->toArray();
            }
            if (!is_array($tierOrder)) {
                $tierOrder = ['silver', 'gold', 'platinum'];
            }
            if (!is_array($sortingOrder)) {
                $sortingOrder = [
                    'most_relevant',
                    'most_popular',
                    'highest_rated',
                    'price_low_to_high',
                    'price_high_to_low'
                ];
            }

            // If AJAX request, return partial view
            if (request()->ajax()) {
                return view('game-management.tools.filter-settings-content', compact('categories', 'categoryOrder', 'tierOrder', 'sortingOrder'));
            }

            return view('game-management.tools.filter-settings', compact('categories', 'categoryOrder', 'tierOrder', 'sortingOrder'));
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Error loading filter settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // If AJAX request, return error response
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading filter settings: ' . $e->getMessage()
                ], 500);
            }
            
            // For non-AJAX requests, redirect back with error
            return redirect()->back()->with('error', 'Error loading filter settings: ' . $e->getMessage());
        }
    }

    /**
     * Save filter order settings
     */
    public function saveFilterSettings(Request $request)
    {
        $validated = $request->validate([
            'category_order' => 'nullable|array',
            'category_order.*' => 'string|exists:tool_categories,slug',
            'tier_order' => 'nullable|array',
            'tier_order.*' => 'string|in:silver,gold,platinum',
            'sorting_order' => 'nullable|array',
            'sorting_order.*' => 'string|in:most_relevant,most_popular,highest_rated,price_low_to_high,price_high_to_low',
        ]);

        try {
            // Get existing consolidated settings or defaults
            $filterSettings = $this->getToolFilterSettings();
            
            // Update with new values if provided
            if (isset($validated['category_order'])) {
                $filterSettings['tool_filter_category_order'] = $validated['category_order'];
            }
            
            if (isset($validated['tier_order'])) {
                $filterSettings['tool_filter_tier_order'] = $validated['tier_order'];
            }
            
            if (isset($validated['sorting_order'])) {
                $filterSettings['tool_filter_sorting_order'] = $validated['sorting_order'];
            }
            
            // Save consolidated settings
            SiteSetting::set('tool_filtering', $filterSettings, 'tools');

            // If AJAX request, return JSON response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Filter order settings saved successfully'
                ]);
            }

            return redirect()->route('game-management.tools.filter-settings')
                ->with('success', 'Filter order settings saved successfully');
        } catch (\Exception $e) {
            // If AJAX request, return JSON error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save settings: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to save settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete an image file from the public path.
     */
    private function deleteImageFile(string $imageUrl): void
    {
        // Extract filename from URL
        $path = parse_url($imageUrl, PHP_URL_PATH);
        $fullPath = public_path($path);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Create and broadcast a notification for the new top tier tool.
     */
    private function topTierToolNotification(Tool $tool): void
    {
        try {
            // Create global notification record (user_id = null means it goes to all users)
            Notification::create([
                'type' => 'top_tier_tool',
                'data' => [
                    'type' => 'top_tier_tool',
                    'tool_id' => $tool->id,
                    'title' => '🎯 New ' . ($tool->tier ? ucfirst($tool->tier) . ' ' : '') . 'Tool Available!',
                    'message' => "📢 Discover " . $tool->name . ($tool->tier ? " (" . ucfirst($tool->tier) . ")" : "") . ($tool->rating ? " ⭐ " . number_format($tool->rating, 1) : "") . ($tool->category ? " in " . $tool->category->name : "") . "! " . ($tool->win_rate_increase ? "Boost your win rate by " . $tool->win_rate_increase . "%! " : "") . "Enhance your gaming experience now!",
                    'description' => $tool->description,
                    'created_at' => $tool->created_at,
                ],
                'user_id' => null, // Global notification for all users
                'is_read' => false,
            ]);

            // The notification will be automatically broadcasted via the model's boot method
        } catch (\Exception $e) {
            // Log the error but don't fail the top tier tool creation
            \Illuminate\Support\Facades\Log::error('Failed to create top tier tool notification', [
                'tool_id' => $tool->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
