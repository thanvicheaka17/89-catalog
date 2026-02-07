<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PremiumTool;
use Illuminate\Http\Request;

class PremiumToolController extends Controller
{
    /**
     * Display a listing of premium tools.
     */
    public function index(Request $request)
    {
        $query = PremiumTool::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->input('status');
            switch ($status) {
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                default:
                    $query->where('is_active', true);
                    break;
            }
        }

        $query->orderBy('created_at', 'desc');

        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $premiumTools = $query->paginate($perPage);

        return view('premium-tools.index', compact('premiumTools', 'perPage'));
    }

    /**
     * Show the form for creating a new premium tool.
     */
    public function create()
    {
        return view('premium-tools.create');
    }

    /**
     * Store a newly created premium tool.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $premiumTool = PremiumTool::create($validated);
        $premiumTool->created_by = auth()->id();
        $premiumTool->save();
        
        return redirect()->route('premium-tools.index')->with('success', 'Premium tool created successfully');
    }

    /**
     * Show the form for editing a premium tool.
     */
    public function edit(PremiumTool $premiumTool)
    {
        return view('premium-tools.edit', compact('premiumTool'));
    }

    /**
     * Update the specified premium tool.
     */
    public function update(Request $request, PremiumTool $premiumTool)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);
        $premiumTool->update($validated);

        return redirect()->route('premium-tools.index')->with('success', 'Premium tool updated successfully');
    }

    /**
     * Remove the specified premium tool.
     */
    public function destroy(PremiumTool $premiumTool)
    {
        $premiumTool->delete();

        return redirect()->route('premium-tools.index')->with('success', 'Premium tool deleted successfully');
    }

    /**
     * Show the form for viewing a premium tool.
     */
    public function show(PremiumTool $premiumTool)
    {
        return view('premium-tools.show', compact('premiumTool'));
    }
}
