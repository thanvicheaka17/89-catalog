<?php

namespace App\Http\Controllers\Admin\SystemManagement;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ToolCategory;
use Illuminate\Support\Str;

class ToolCategoryController extends Controller
{
    /**
     * Display a listing of tool categories.
     */
    public function index(Request $request)
    {
        $query = ToolCategory::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;

        $toolCategories = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('system-management.tool-categories.index', compact('toolCategories', 'perPage'));
    }

    /**
     * Show the form for creating a new tool category.
     */
    public function create()
    {
        return view('system-management.tool-categories.create');
    }

    /**
     * Store a newly created tool category.
     */
    public function store(Request $request)
    {   
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tool_categories',
            'description' => 'nullable|string',
        ]);

        $toolCategory = ToolCategory::create($validated);
        $toolCategory->slug = Str::slug($validated['name']);

        return redirect()->route('system-management.tool-categories.index')->with('success', 'Tool category created successfully');
    }

    /**
     * Show the form for editing a tool category.
     */
    public function edit(ToolCategory $toolCategory)
    {
        return view('system-management.tool-categories.edit', compact('toolCategory'));
    }

    /**
     * Update the specified tool category.
     */
    public function update(Request $request, ToolCategory $toolCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:tool_categories,slug,' . $toolCategory->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $toolCategory->update($validated);

        return redirect()->route('system-management.tool-categories.index')->with('success', 'Tool category updated successfully');
    }

    /**
     * Delete the specified tool category.
     */
    public function destroy(ToolCategory $toolCategory)
    {
        $toolCategory->delete();
        return redirect()->route('system-management.tool-categories.index')->with('success', 'Tool category deleted successfully');
    }

    /**
     * Display the specified tool category.
     */
    public function show(ToolCategory $toolCategory)
    {
        return view('system-management.tool-categories.show', compact('toolCategory'));
    }
}
