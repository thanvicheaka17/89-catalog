<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Http\Controllers\Controller;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%");
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
                            $q->whereNull('start_at')
                                ->orWhere('start_at', '<=', $now);
                        })
                        ->where(function ($q) use ($now) {
                            $q->whereNull('end_at')
                                ->orWhere('end_at', '>=', $now);
                        });
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // Filter by date range (show banners that overlap with the selected range)
        if ($request->filled('date_from')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('end_at')
                    ->orWhereDate('end_at', '>=', $request->input('date_from'));
            });
        }
        if ($request->filled('date_to')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('start_at')
                    ->orWhereDate('start_at', '<=', $request->input('date_to'));
            });
        }

        $query->orderBy('created_at', 'desc');

        // Per page (default 25)
        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $banners = $query->orderBy('priority', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return view('banners.index', compact('banners', 'perPage'));
    }

    public function create()
    {
        return view('banners.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,gif,webp|max:10240',
            'link_url' => 'nullable|url',
            'priority' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'visibility' => 'nullable|in:public,members',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            'meta' => 'nullable|string',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/banners');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/banners/' . $filename;
        }

        unset($validated['image']);

        if (isset($validated['meta']) && $validated['meta'] !== null && $validated['meta'] !== '') {
            $decoded = json_decode($validated['meta'], true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $validated['meta'] = $decoded;
            } else {
                $validated['meta'] = ['text' => $validated['meta']];
            }
        } else {
            $validated['meta'] = null;
        }

        Banner::create($validated);

        return redirect()->route('banners.index')
            ->with('success', 'Banner created successfully');
    }

    public function show(Banner $banner)
    {
        return view('banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        return view('banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,gif,webp|max:10240',
            'link_url' => 'nullable|url',
            'priority' => 'required|integer|min:0',
            'is_active' => 'required|boolean',
            'visibility' => 'nullable|in:public,members',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after:start_at',
            'meta' => 'nullable|string',
        ]);

        if ($request->input('remove_image') === '1') {
            if (!$request->hasFile('image')) {
                return redirect()->back()
                    ->withErrors(['image' => 'Please upload a new image when removing the current one.'])
                    ->withInput();
            }
            if ($banner->image_path) {
                $this->deleteImageFile($banner->image_path);
            }
        }
        
        if ($request->hasFile('image')) {
            if ($banner->image_path && $request->input('remove_image') !== '1') {
                $this->deleteImageFile($banner->image_path);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/banners');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $validated['image_path'] = 'images/banners/' . $filename;
        }
        else {
            $validated['image_path'] = $banner->image_path;
        }

        unset($validated['image']);

        $banner->update($validated);

        return redirect()
            ->route('banners.index')
            ->with('success', 'Banner updated successfully');
    }


    public function destroy(Banner $banner)
    {
        $banner->delete();
        if ($banner->image_path) {
            $this->deleteImageFile($banner->image_path);
        }

        return redirect()->route('banners.index')->with('success', 'Banner deleted successfully');
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
}
