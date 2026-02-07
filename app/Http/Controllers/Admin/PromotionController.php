<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PromotionController extends Controller
{
    /**
     * Display a listing of promotions.
     */
    public function index(Request $request)
    {
        $query = Promotion::with('creator');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->input('status');
            $now = now();
            
            switch ($status) {
                case 'active':
                    $query->where('is_active', true)
                        ->where(function ($q) use ($now) {
                            $q->whereNull('start_date')
                              ->orWhere('start_date', '<=', $now);
                        })
                        ->where(function ($q) use ($now) {
                            $q->whereNull('end_date')
                              ->orWhere('end_date', '>=', $now);
                        });
                    break;
                case 'scheduled':
                    $query->where('is_active', true)
                        ->whereNotNull('start_date')
                        ->where('start_date', '>', $now);
                    break;
                case 'expired':
                    $query->whereNotNull('end_date')
                        ->where('end_date', '<', $now);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->input('position'));
        }

        // Filter by date range (show banners that overlap with the selected range)
        if ($request->filled('date_from')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', $request->input('date_from'));
            });
        }
        if ($request->filled('date_to')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('start_date')
                  ->orWhereDate('start_date', '<=', $request->input('date_to'));
            });
        }

        $query->orderBy('created_at', 'desc');

        // Per page (default 25)
        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $banners = $query->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $positions = Promotion::$positions;

        return view('promotions.index', compact('banners', 'perPage', 'positions'));
    }

    /**
     * Show the form for creating a new promotion.
     */
    public function create()
    {
        $positions = Promotion::$positions;
        // Calculate the next priority number
        $nextPriority = (Promotion::max('priority') ?? -1) + 1;
        return view('promotions.create', compact('positions', 'nextPriority'));
    }

    /**
     * Store a newly created promotion.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'button_url' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'], // 10MB max
            'background_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color_2' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_gradient_type' => ['nullable', 'string', Rule::in(['solid', 'gradient'])],
            'background_gradient_direction' => ['nullable', 'string', 'max:30'],
            'text_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_color_2' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_gradient_type' => ['nullable', 'string', Rule::in(['solid', 'gradient'])],
            'button_gradient_direction' => ['nullable', 'string', 'max:30'],
            'button_text_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'position' => ['required', Rule::in(array_keys(Promotion::$positions))],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
            'priority' => ['required', 'integer', 'min:0'],
        ]);

        // Check if the requested priority is already taken
        $requestedPriority = (int) $validated['priority'];
        $priorityExists = Promotion::where('priority', $requestedPriority)->exists();
        
        if ($priorityExists) {
            // If priority is taken, find the next available priority
            $maxPriority = Promotion::max('priority') ?? -1;
            $validated['priority'] = $maxPriority + 1;
        } else {
            // Use the requested priority if available
            $validated['priority'] = $requestedPriority;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/promotion'), $filename);
            $validated['image_path'] = 'images/promotion/' . $filename;
        }

        // Remove 'image' from validated data as it's not a model field
        unset($validated['image']);

        $validated['created_by'] = auth()->id();

        $promotion = Promotion::create($validated);

        // Create and broadcast notification for the new promotion
        $this->promotionNotification($promotion);

        return redirect()->route('promotions.index')->with('success', 'Promotion created successfully.');
    }

    /**
        * Display the specified promotion.
     */
    public function show(Promotion $promotion)
    {
        $promotion->load('creator');
        return view('promotions.show', compact('promotion'));
    }

    /**
     * Show the form for editing the specified promotion.
     */
    public function edit(Promotion $promotion)
    {
        $positions = Promotion::$positions;
        return view('promotions.edit', compact('promotion', 'positions'));
    }

    /**
     * Update the specified promotion.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:1000'],
            'button_text' => ['nullable', 'string', 'max:50'],
            'button_url' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,gif,webp', 'max:10240'], // 10MB max
            'background_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_color_2' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'background_gradient_type' => ['nullable', 'string', Rule::in(['solid', 'gradient'])],
            'background_gradient_direction' => ['nullable', 'string', 'max:30'],
            'text_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_color_2' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'button_gradient_type' => ['nullable', 'string', Rule::in(['solid', 'gradient'])],
            'button_gradient_direction' => ['nullable', 'string', 'max:30'],
            'button_text_color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'position' => ['required', Rule::in(array_keys(Promotion::$positions))],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
            'priority' => ['required', 'integer', 'min:0', Rule::unique('promotions', 'priority')->ignore($promotion->id)],
        ]);

        // Handle image removal
        if ($request->input('remove_image') === '1') {
            if ($promotion->image_path) {
                $this->deleteImageFile($promotion->image_path);
            }
            $validated['image_path'] = null;
        }
        // Handle new image upload
        elseif ($request->hasFile('image')) {
            // Delete old image if exists
            if ($promotion->image_path) {
                $this->deleteImageFile($promotion->image_path);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/promotion'), $filename);
            $validated['image_path'] = 'images/promotion/' . $filename;
        }

        // Remove 'image' from validated data as it's not a model field
        unset($validated['image']);

        $promotion->update($validated);

        return redirect()->route('promotions.index')->with('success', 'Promotion updated successfully.');
    }

    /**
     * Remove the specified promotion.
     */
    public function destroy(Promotion $promotion)
    {
        // Delete the image file if exists
        if ($promotion->image_path) {
            $this->deleteImageFile($promotion->image_path);
        }

        $promotion->delete();

        return redirect()->route('promotions.index')->with('success', 'Promotion deleted successfully.');
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
     * Duplicate a promotion.
     */
    public function duplicate(Promotion $promotion)
    {
        $newBanner = $promotion->replicate();
        $newBanner->title = $promotion->title . ' (Copy)';
        $newBanner->is_active = false;
        $newBanner->created_by = auth()->id();
        
        // Auto-assign priority as the next number (highest existing priority + 1)
        $maxPriority = Promotion::max('priority') ?? -1;
        $newBanner->priority = $maxPriority + 1;

        // Copy the image file if exists
        if ($promotion->image_path && Storage::disk('public')->exists($promotion->image_path)) {
            $extension = pathinfo($promotion->image_path, PATHINFO_EXTENSION);
            $newPath = 'promotion/' . uniqid() . '.' . $extension;
            Storage::disk('public')->copy($promotion->image_path, $newPath);
            $newBanner->image_path = $newPath;
        }

        $newBanner->save();

        return redirect()->route('promotions.edit', $newBanner)->with('success', 'Promotion duplicated. You can now edit the copy.');
    }

    /**
     * Toggle the active status of a promotion.
     */
    public function toggleStatus(Promotion $promotion)
    {
        $promotion->update(['is_active' => !$promotion->is_active]);

        $status = $promotion->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Promotion {$status} successfully.");
    }

    /**
     * Create and broadcast a notification for the new promotion.
     */
    private function promotionNotification(Promotion $promotion): void
    {
        try {
            // Create global notification record (user_id = null means it goes to all users)
            Notification::create([
                'type' => 'promotion',
                'data' => [
                    'type' => 'promotion',
                    'promotion_id' => $promotion->id,
                    'title' => '🎉 New Promotion!',
                    'message' => "📢 " . $promotion->title . " - Check it out now!",
                    'description' => $promotion->message,
                    'start_date' => $promotion->start_date,
                    'end_date' => $promotion->end_date,
                    'created_at' => $promotion->created_at,
                ],
                'user_id' => null, // Global notification for all users
                'is_read' => false,
            ]);

            // The notification will be automatically broadcasted via the model's boot method
        } catch (\Exception $e) {
            // Log the error but don't fail the promotion creation
            \Illuminate\Support\Facades\Log::error('Failed to create promotion notification', [
                'promotion_id' => $promotion->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
