<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Testimonial;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Notification;
class TestimonialController extends Controller
{
    public function index(Request $request  )
    {
        $query = Testimonial::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('user_name', 'like', "%{$search}%")
                ->orWhere('user_role', 'like', "%{$search}%")
                ->orWhere('message', 'like', "%{$search}%");
        }
        if ($request->has('is_featured') && $request->is_featured) {
            $is_featured = $request->is_featured;
            $query->where('is_featured', $is_featured);
        }
        if ($request->has('is_active') && $request->is_active) {
            $is_active = $request->is_active;
            $query->where('is_active', $is_active);
        }
        if ($request->has('rating') && $request->rating) {
            $rating = $request->rating;
            $query->where('rating', $rating);
        }
        
        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $testimonials = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('testimonials.index', compact('testimonials', 'perPage'));
    }

    public function create()
    {
        $users = User::all();
        return view('testimonials.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_role' => 'required|string|max:255',
            'avatar' => ['nullable', 'file', 'mimes:jpeg,png,gif,webp,svg', 'max:2048'], // 2MB max
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'is_featured' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        $testimonialData = [
            'user_name' => $validated['user_name'],
            'user_role' => $validated['user_role'],
            'message' => $validated['message'],
            'rating' => $validated['rating'],
            'is_featured' => $validated['is_featured'],
            'is_active' => $validated['is_active'],
        ];

         // Handle avatar upload
         if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/avatars'), $filename);
            $testimonialData['avatar'] = 'images/avatars/' . $filename;
        }

        $testimonial = Testimonial::create($testimonialData);

        // Create and broadcast notification for the new testimonial
        $this->testimonialNotification($testimonial);

        return redirect()->route('testimonials.index')->with('success', 'Testimonial created successfully');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_role' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'message' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'is_featured' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        // Handle avatar removal
        if ($request->input('remove_avatar') === '1') {
            if ($testimonial->avatar) {
                $this->deleteAvatarFile($testimonial->avatar);
            }
            $validated['avatar'] = null;
        }
        // Handle new avatar upload
        elseif ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($testimonial->avatar) {
                $this->deleteAvatarFile($testimonial->avatar);
            }
            $avatar = $request->file('avatar');
            $filename = time() . '_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/avatars'), $filename);
            $validated['avatar'] = 'images/avatars/' . $filename;
        }

        $testimonial->update($validated);

        return redirect()->route('testimonials.index')->with('success', 'Testimonial updated successfully');
    }

    public function destroy(Testimonial $testimonial)
    {
        // Delete avatar file if exists
        if ($testimonial->avatar) {
            $this->deleteAvatarFile($testimonial->avatar);
        }
        
        $testimonial->delete();

        return redirect()->route('testimonials.index')->with('success', 'Testimonial deleted successfully');
    }

    public function show(Testimonial $testimonial)
    {
        return view('testimonials.show', compact('testimonial'));
    }

    /**
     * Delete an avatar file from the public path.
     */
    private function deleteAvatarFile(string $avatarUrl): void
    {
        // Extract filename from URL
        $path = parse_url($avatarUrl, PHP_URL_PATH);
        $fullPath = public_path($path);
        
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Create and broadcast a notification for the new testimonial.
     */
    private function testimonialNotification(Testimonial $testimonial): void
    {
        try {
            // Create global notification record (user_id = null means it goes to all users)
            Notification::create([
                'type' => 'testimonial',
                'data' => [
                    'type' => 'testimonial',
                    'testimonial_id' => $testimonial->id,
                    'title' => '🎉 New Testimonial!',
                    'message' => "⭐ " . $testimonial->user_name . " gave the platform a " . $testimonial->rating . "-star rating!",
                    'description' => $testimonial->message,
                    'created_at' => $testimonial->created_at,
                ],
                'user_id' => null, // Global notification for all users
                'is_read' => false,
            ]);

            // The notification will be automatically broadcasted via the model's boot method
        } catch (\Exception $e) {
            // Log the error but don't fail the event creation
            \Illuminate\Support\Facades\Log::error('Failed to create testimonial notification', [
                'testimonial_id' => $testimonial->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
