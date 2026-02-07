<?php

namespace App\Http\Controllers\Admin\GameManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\HotAndFresh;
use Illuminate\Support\Str;
use App\Models\Notification;
class HotAndFreshController extends Controller
{
    public function index(Request $request)
    {
        $query = HotAndFresh::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('tier') && $request->tier) {
            $tier = $request->input('tier');
            $query->where('tier', $tier);
        }

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

        $hotAndFreshes = $query->paginate($perPage);

        return view('game-management.hot-and-fresh.index', compact('hotAndFreshes', 'perPage'));
    }

    public function create()
    {
        return view('game-management.hot-and-fresh.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:hot_and_fresh',
            'image' => 'required|image|mimes:jpeg,png,gif,webp|max:10240',
            'description' => 'nullable|string',
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
            $targetDir = public_path('images/hot-and-fresh');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/hot-and-fresh/' . $filename;
        }

        unset($validated['image']);

        $validated['slug'] = Str::slug($validated['slug']);

        $randomData = HotAndFresh::randomHotAndFreshData();

        $hotAndFresh = HotAndFresh::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'image_path' => $validated['image_path'],
            'description' => $validated['description'],
            'rating' => $randomData['rating'],
            'user_count' => $randomData['user_count'],
            'active_hours' => $randomData['active_hours'],
            'rank' => $randomData['rank'],
            'badge' => $randomData['badge'],
            'tier' => $validated['tier'] ?? $randomData['tier'],
            'price' => $validated['price'] ?? $randomData['price'],
            'win_rate_increase' => $randomData['win_rate_increase'],
        ]);
        
        // Create and broadcast notification for the new hot and fresh
        $this->hotAndFreshNotification($hotAndFresh);

        return redirect()->route('game-management.hot-and-fresh.index')->with('success', 'Hot and fresh created successfully');
    }

    public function edit(HotAndFresh $hotAndFresh) {
        return view('game-management.hot-and-fresh.edit', compact('hotAndFresh'));
    }

    public function update(Request $request, HotAndFresh $hotAndFresh)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:hot_and_fresh,slug,' . $hotAndFresh->id,
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:10240',
            'description' => 'nullable|string',
            'tier' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
        ]);

        if ($request->input('remove_image') === '1') {
            if (!$request->hasFile('image')) {
                return redirect()->back()
                    ->withErrors(['image' => 'Please upload a new image when removing the current one.'])
                    ->withInput();
            }
            if ($hotAndFresh->image_path) {
                $this->deleteImageFile($hotAndFresh->image_path);
            }
        }
        
        if ($request->hasFile('image')) {
            if ($hotAndFresh->image_path && $request->input('remove_image') !== '1') {
                $this->deleteImageFile($hotAndFresh->image_path);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/hot-and-fresh');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/hot-and-fresh/' . $filename;
        }
        else {
            $validated['image_path'] = $hotAndFresh->image_path;
        }

        unset($validated['image']);

        if ($validated['tier']) {
            $hotAndFresh->tier = $validated['tier'];
        }
        if ($validated['price']) {
            $hotAndFresh->price = $validated['price'];
        }

        $hotAndFresh->update($validated);

        return redirect()->route('game-management.hot-and-fresh.index')->with('success', 'Hot and fresh updated successfully');
    }

    public function destroy(HotAndFresh $hotAndFresh)
    {
        if ($hotAndFresh->image_path) {
            $this->deleteImageFile($hotAndFresh->image_path);
        }

        $hotAndFresh->delete();

        return redirect()->route('game-management.hot-and-fresh.index')->with('success', 'Hot and fresh deleted successfully');
    }

    public function show(HotAndFresh $hotAndFresh)
    {
        return view('game-management.hot-and-fresh.show', compact('hotAndFresh'));
    }

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
     * Create and broadcast a notification for the new hot and fresh.
     */
    private function hotAndFreshNotification(HotAndFresh $hotAndFresh): void
    {
        try {
            // Create global notification record (user_id = null means it goes to all users)
            Notification::create([
                'type' => 'hot_and_fresh',
                'data' => [
                    'type' => 'hot_and_fresh',
                    'hot_and_fresh_id' => $hotAndFresh->id,
                    'title' => '🔥 New Hot & Fresh Game Available!',
                    'message' => "🎮 Discover " . $hotAndFresh->name . ($hotAndFresh->rating ? " ⭐" . number_format($hotAndFresh->rating, 1) : "") . ($hotAndFresh->tier ? " (" . ucfirst($hotAndFresh->tier) . ")" : "") . "! " . ($hotAndFresh->win_rate_increase ? "Win rate increased by " . $hotAndFresh->win_rate_increase . "%! " : "") . "Play now and enjoy the thrill!",
                    'description' => $hotAndFresh->description,
                    'created_at' => $hotAndFresh->created_at,
                ],
                'user_id' => null, // Global notification for all users
                'is_read' => false,
            ]);

            // The notification will be automatically broadcasted via the model's boot method
        } catch (\Exception $e) {
            // Log the error but don't fail the hot and fresh creation
            \Illuminate\Support\Facades\Log::error('Failed to create hot and fresh notification', [
                'hot_and_fresh_id' => $hotAndFresh->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
