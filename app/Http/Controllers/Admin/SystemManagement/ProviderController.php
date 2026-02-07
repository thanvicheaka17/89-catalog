<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Provider;
use Illuminate\Support\Str;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = Provider::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $providers = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('system-management.providers.index', compact('providers', 'perPage'));
    }

    public function create()
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to view the create provider page.');
        }

        return view('system-management.providers.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to create a provider.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:providers,slug',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $provider = Provider::create($validated);
        $provider->slug = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/providers');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $provider->logo = 'images/providers/' . $filename;
            $provider->save();
        }

        return redirect()->route('system-management.providers.index')->with('success', 'Provider created successfully');
    }

    public function edit(Provider $provider)
    {
        return view('system-management.providers.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to update a provider.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:providers,slug,' . $provider->id,
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        if ($request->input('remove_image') === '1') {
            if (!$request->hasFile('image')) {
                return redirect()->back()
                    ->withErrors(['image' => 'Please upload a new image when removing the current one.'])
                    ->withInput();
            }
            if ($provider->logo) {
                $this->deleteImageFile($provider->logo);
            }
        }
        
        if ($request->hasFile('image')) {
            if ($provider->logo && $request->input('remove_image') !== '1') {
                $this->deleteImageFile($provider->logo);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/providers');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $validated['logo'] = 'images/providers/' . $filename;
        }
        else {
            $validated['logo'] = $provider->logo;
        }

        unset($validated['image']);

        $provider->update($validated);

        return redirect()->route('system-management.providers.index')->with('success', 'Provider updated successfully');
    }

    public function destroy(Provider $provider)
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to delete a provider.');
        }

        if ($provider->logo) {
            $this->deleteImageFile($provider->logo);
        }
        
        $provider->delete();

        return redirect()->route('system-management.providers.index')->with('success', 'Provider deleted successfully');
    }

    public function show(Provider $provider)
    {
        return view('system-management.providers.show', compact('provider'));
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
}
