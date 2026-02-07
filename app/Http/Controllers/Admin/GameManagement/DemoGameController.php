<?php

namespace App\Http\Controllers\Admin\GameManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DemoGame;
use Illuminate\Support\Str;
use App\Models\Notification;
class DemoGameController extends Controller
{
    public function index(Request $request)
    {
        $query = DemoGame::query();

        if ($request->has('is_demo')) {
            $isDemo = $request->input('is_demo');
            switch ($isDemo) {
                case 'yes':
                    $query->where('is_demo', true);
                    break;
                case 'no':
                    $query->where('is_demo', false);
                    break;
            }
        }


        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
            $query->orWhere('description', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $demoGames = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('game-management.demo-games.index', compact('demoGames', 'perPage'));
    }

    public function create()
    {
        return view('game-management.demo-games.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:demo_games',
            'description' => 'nullable|string',
            'is_demo' => 'required|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'url' => 'required|url|unique:demo_games',
        ], [
            'url.required' => 'The URL field is required.',
            'url.url' => 'Please input a valid correct URL',
            'url.unique' => 'The URL has already been taken.',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/demo-games');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/demo-games/' . $filename;
        }

        // Remove 'image' from validated data as it's not a model field
        unset($validated['image']);

        $demoGame = DemoGame::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'slug' =>  Str::slug($validated['slug']),
            'is_demo' => $validated['is_demo'],
            'image_path' => $validated['image_path'],
            'url' => $validated['url'],
        ]);

        $demoGame->created_by = auth()->id();

        $demoGame->save();

        // Create and broadcast notification for the new demo game
        $this->demoGameNotification($demoGame);

        return redirect()->route('game-management.demo-games.index')->with('success', 'Demo game created successfully.');
    }   

    public function show(DemoGame $demoGame)
    {
        return view('game-management.demo-games.show', compact('demoGame'));
    }

    public function edit(DemoGame $demoGame)
    {
        return view('game-management.demo-games.edit', compact('demoGame'));
    }

    public function update(Request $request, DemoGame $demoGame)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:demo_games,slug,' . $demoGame->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:10240',
            'is_demo' => 'required|boolean',
            'url' => 'required|url|unique:demo_games,url,' . $demoGame->id,
        ], [
            'url.required' => 'The URL field is required.',
            'url.url' => 'Please input a valid correct URL',
            'url.unique' => 'The URL has already been taken.',
        ]);

        if ($request->input('remove_image') === '1') {
            if (!$request->hasFile('image')) {
                return redirect()->back()
                    ->withErrors(['image' => 'Please upload a new image when removing the current one.'])
                    ->withInput();
            }
            if ($demoGame->image_path) {
                $this->deleteImageFile($demoGame->image_path);
            }
        }
        
        if ($request->hasFile('image')) {
            if ($demoGame->image_path && $request->input('remove_image') !== '1') {
                $this->deleteImageFile($demoGame->image_path);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/demo-games');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/demo-games/' . $filename;
        }
        else {
            $validated['image_path'] = $demoGame->image_path;
        }

        unset($validated['image']);

        $demoGame->update($validated);

        return redirect()->route('game-management.demo-games.index')->with('success', 'Demo game updated successfully.');
    }

    public function destroy(DemoGame $demoGame) 
    {
        $demoGame->delete();
        if ($demoGame->image_path) {
            $this->deleteImageFile($demoGame->image_path);
        }

        return redirect()->route('game-management.demo-games.index')->with('success', 'Demo game deleted successfully.');
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
     * Create and broadcast a notification for the new demo game.
     */
    private function demoGameNotification(DemoGame $demoGame): void
    {
        try {
            // Create global notification record (user_id = null means it goes to all users)
            Notification::create([
                'type' => 'demo_game',
                'data' => [
                    'type' => 'demo_game',
                    'demo_game_id' => $demoGame->id,
                    'title' => '🎯 New ' . ($demoGame->is_demo ? 'Demo Game' : 'Game') . ' Available!',
                    'message' => "🎮 Try " . $demoGame->title . ($demoGame->is_demo ? " in Demo Mode" : "") . "! " . ($demoGame->is_demo ? "Practice risk-free before playing for real. " : "") . "Experience the excitement now!",
                    'description' => $demoGame->description,
                    'created_at' => $demoGame->created_at,
                ],
                'user_id' => null, // Global notification for all users
                'is_read' => false,
            ]);

            // The notification will be automatically broadcasted via the model's boot method
        } catch (\Exception $e) {
            // Log the error but don't fail the demo game creation
            \Illuminate\Support\Facades\Log::error('Failed to create demo game notification', [
                'demo_game_id' => $demoGame->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
