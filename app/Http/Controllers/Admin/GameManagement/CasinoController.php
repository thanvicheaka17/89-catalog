<?php

namespace App\Http\Controllers\Admin\GameManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Casino;
use App\Models\CasinoCategory;
use Illuminate\Support\Str;
use App\Models\Notification;
class CasinoController extends Controller
{
    public function index(Request $request)
    {
        $query = Casino::query();
        $casinoCategories = CasinoCategory::all();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category) {
            $category = CasinoCategory::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $casinos = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('game-management.casinos.index', compact('casinos', 'casinoCategories', 'perPage'))->with('success', 'Casinos fetched successfully');
    }

    public function create()
    {
        $casinoCategories = CasinoCategory::all();
        return view('game-management.casinos.create', compact('casinoCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:casino_categories,id',
            'slug' => 'required|string|max:255|unique:casinos',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            'rtp' => 'nullable|integer|min:0|max:100',
            'rating' => 'nullable|integer|min:1|max:5',
            'daily_withdrawal_amount' => 'nullable|decimal:2|min:0',
            'daily_withdrawal_players' => 'nullable|integer|min:0',
            'last_withdrawal_update' => 'nullable|datetime',
            'total_withdrawn' => 'nullable|decimal:2|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['rtp'] = rand(50, 95);
        $validated['rating'] = rand(1, 5);
        $validated['daily_withdrawal_amount'] = rand(100000, 1000000);
        $validated['daily_withdrawal_players'] = rand(1000, 10000);
        $validated['last_withdrawal_update'] = now();
        $validated['total_withdrawn'] = rand(100000, 1000000);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/casinos');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $image->move($targetDir, $filename);
            $validated['image'] = 'images/casinos/' . $filename;
        }

        $casino = Casino::create($validated);

        // Create and broadcast notification for the new casino
        $this->casinoNotification($casino);

        return redirect()->route('game-management.casinos.index')->with('success', 'Casino created successfully');
    }

    public function edit(Casino $casino)
    {
        $casinoCategories = CasinoCategory::all();
        return view('game-management.casinos.edit', compact('casino', 'casinoCategories'));
    }

    public function update(Request $request, Casino $casino)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:casino_categories,id',
            'slug' => 'required|string|max:255|unique:casinos,slug,' . $casino->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        if ($request->input('remove_image') === '1') {
            if (!$request->hasFile('image')) {
                return redirect()->back()
                    ->withErrors(['image' => 'Please upload a new image when removing the current one.'])
                    ->withInput();
            }
            if ($casino->image) {
                $this->deleteImageFile($casino->image);
            }
        }

        if ($request->hasFile('image')) {
            if ($casino->image && $request->input('remove_image') !== '1') {
                $this->deleteImageFile($casino->image);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/casinos');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $validated['image'] = 'images/casinos/' . $filename;
        } else {
            $validated['image'] = $casino->image;
        }

        $casino->update($validated);

        return redirect()->route('game-management.casinos.index')->with('success', 'Casino updated successfully');
    }

    public function destroy(Casino $casino)
    {
        if ($casino->image) {
            $this->deleteImageFile($casino->image);
        }

        $casino->delete();

        return redirect()->route('game-management.casinos.index')->with('success', 'Casino deleted successfully');
    }

    public function show(Casino $casino)
    {
        $casinoCategories = CasinoCategory::all();
        return view('game-management.casinos.show', compact('casino', 'casinoCategories'));
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
     * Create and broadcast a notification for the new casino.
     */
    private function casinoNotification(Casino $casino): void
    {
        try {
            // Create global notification record (user_id = null means it goes to all users)
            Notification::create([
                'type' => 'casino',
                'data' => [
                    'type' => 'casino',
                    'casino_id' => $casino->id,
                    'title' => '🎰 New Casino Just Launched!',
                    'message' => "🎮 Explore " . $casino->name . ($casino->rating ? " ⭐ " . number_format($casino->rating, 1) : "") . ($casino->category ? " in " . $casino->category->name : "") . "! " . ($casino->rtp ? "High RTP: " . $casino->rtp . "% " : "") . "Start winning big today!",
                    'description' => $casino->description,
                    'created_at' => $casino->created_at,
                ],
                'user_id' => null, // Global notification for all users
                'is_read' => false,
            ]);

            // The notification will be automatically broadcasted via the model's boot method
        } catch (\Exception $e) {
            // Log the error but don't fail the casino creation
            \Illuminate\Support\Facades\Log::error('Failed to create casino notification', [
                'casino_id' => $casino->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
