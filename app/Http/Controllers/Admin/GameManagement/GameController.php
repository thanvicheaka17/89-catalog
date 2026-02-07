<?php

namespace App\Http\Controllers\Admin\GameManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\Provider;
use Illuminate\Support\Str;
class GameController extends Controller
{
    public function index(Request $request)
    {
        $query = Game::query();
        $providers = Provider::all();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('provider') && $request->provider) {
            $provider = Provider::where('slug', $request->provider)->first();
            if ($provider) {
                $query->where('provider_id', $provider->id);
            }
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $games = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('game-management.games.index', compact('games', 'providers', 'perPage'));
    }

    public function create()
    {
        $providers = Provider::all();
        return view('game-management.games.create', compact('providers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:10240',
            'provider_id' => 'required|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/games');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/games/' . $filename;
        }

        unset($validated['image']);
        $validated['slug'] = Str::slug($validated['slug']);


        $game = Game::create($validated);
        return redirect()->route('game-management.games.index')->with('success', 'Game created successfully.');
    }

    public function show(Game $game)
    {
        $providers = Provider::all();
        return view('game-management.games.show', compact('game', 'providers'));
    }

    public function edit(Game $game)
    {
        $providers = Provider::all();
        return view('game-management.games.edit', compact('game', 'providers'));
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games,slug,' . $game->id,
            'description' => 'nullable|string',
            'provider_id' => 'required|string|max:255',
        ]);

        if ($request->input('remove_image') === '1') {
            if (!$request->hasFile('image')) {
                return redirect()->back()
                    ->withErrors(['image' => 'Please upload a new image when removing the current one.'])
                    ->withInput();
            }
            if ($game->image_path) {
                $this->deleteImageFile($game->image_path);
            }
        }

        if ($request->hasFile('image')) {
            if ($game->image_path && $request->input('remove_image') !== '1') {
                $this->deleteImageFile($game->image_path);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/games');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/games/' . $filename;
        } else {
            $validated['image_path'] = $game->image_path;
        }

        unset($validated['image']);

        $game->update($validated);

        return redirect()->route('game-management.games.index')->with('success', 'Game updated successfully.');
    }

    public function destroy(Game $game)
    {
        $game->delete();
        $this->deleteImageFile($game->image_path);
        return redirect()->route('game-management.games.index')->with('success', 'Game deleted successfully.');
    }

    private function deleteImageFile(string $imagePath): void
    {
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}
