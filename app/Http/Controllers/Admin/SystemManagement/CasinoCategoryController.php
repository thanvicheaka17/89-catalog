<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CasinoCategory;
use Illuminate\Support\Str;

class CasinoCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = CasinoCategory::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $categories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('system-management.casino-categories.index', compact('categories', 'perPage'));
    }

    public function create()
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to view the create casino category page.');
        }

        return view('system-management.casino-categories.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to create a casino category.');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:casino_categories,slug',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        $category = CasinoCategory::create($validated);
        $category->slug = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/casino-categories');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $category->logo = 'images/casino-categories/' . $filename;
            $category->save();
        }

        return redirect()->route('system-management.casino-categories.index')->with('success', 'Casino category created successfully');
    }

    public function edit(CasinoCategory $casino_category)
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to edit a casino category.');
        }

        return view('system-management.casino-categories.edit', compact('casino_category'));
    }

    public function update(Request $request, CasinoCategory $casino_category)
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to update a casino category.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:casino_categories,slug,' . $casino_category->id,
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        if ($request->input('remove_image') === '1') {
            if (!$request->hasFile('image')) {
                return redirect()->back()
                    ->withErrors(['image' => 'Please upload a new image when removing the current one.'])
                    ->withInput();
            }
            if ($casino_category->logo) {
                $this->deleteImageFile($casino_category->logo);
            }
        }

        if ($request->hasFile('image')) {
            if ($casino_category->logo && $request->input('remove_image') !== '1') {
                $this->deleteImageFile($casino_category->logo);
            }
            $image = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $targetDir = public_path('images/casino-categories');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
            $image->move($targetDir, $filename);
            $validated['logo'] = 'images/casino-categories/' . $filename;
        }
        else {
            $validated['logo'] = $casino_category->logo;
        }

        unset($validated['image']);

        $casino_category->update($validated);

        return redirect()->route('system-management.casino-categories.index')->with('success', 'Casino category updated successfully');
    }

    public function destroy(CasinoCategory $casino_category)
    {
        if (!auth()->user()->isSystem()) {
            return redirect()->back()->with('error', 'You do not have permission to delete a casino category.');
        }

        if ($casino_category->logo) {
            $this->deleteImageFile($casino_category->logo);
        }

        $casino_category->delete();

        return redirect()->route('system-management.casino-categories.index')->with('success', 'Casino category deleted successfully');
    }

    public function show(CasinoCategory $casino_category)
    {
        return view('system-management.casino-categories.show', compact('casino_category'));
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
