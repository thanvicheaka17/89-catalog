<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('author_name', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhere('tags', 'like', "%{$search}%");
        }

        if ($request->has('is_featured') && $request->is_featured !== '') {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->has('is_published') && $request->is_published !== '') {
            $query->where('is_published', $request->boolean('is_published'));
        }


        $perPage = $request->input('per_page', 25);
        $perPage = in_array($perPage, [25, 50, 100, 200]) ? $perPage : 25;
        $blogPosts = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return view('blog-posts.index', compact('blogPosts', 'perPage'));
    }

    public function create()
    {
        return view('blog-posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author_name' => 'required|string|max:255',
            'author_role' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:500',
            'read_time' => 'nullable|integer|min:1|max:60',
            'is_featured' => 'required|boolean',
            'is_published' => 'required|boolean',
            'published_at' => 'nullable|date',
        ]);

        // Generate slug from title
        $validated['slug'] = Str::slug($validated['title']);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blog'), $filename);
            $validated['featured_image'] = 'images/blog/' . $filename;
        }

        // Set published_at if publishing now
        if ($validated['is_published'] && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return redirect()->route('blog-posts.index')->with('success', 'Blog post created successfully');
    }

    public function edit(BlogPost $blogPost)
    {
        return view('blog-posts.edit', compact('blogPost'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author_name' => 'required|string|max:255',
            'author_role' => 'nullable|string|max:255',
            'tags' => 'nullable|string|max:500',
            'read_time' => 'nullable|integer|min:1|max:60',
            'is_featured' => 'required|boolean',
            'is_published' => 'required|boolean',
            'published_at' => 'nullable|date',
        ]);

        // Update slug if title changed
        if ($blogPost->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle featured image removal
        if ($request->input('remove_featured_image') === '1') {
            if ($blogPost->featured_image) {
                $this->deleteFeaturedImageFile($blogPost->featured_image);
            }
            $validated['featured_image'] = null;
        }

        // Handle new featured image upload
        elseif ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($blogPost->featured_image) {
                $this->deleteFeaturedImageFile($blogPost->featured_image);
            }
            $image = $request->file('featured_image');
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/blog'), $filename);
            $validated['featured_image'] = 'images/blog/' . $filename;
        }

        // Set published_at if publishing now
        if ($validated['is_published'] && !$blogPost->is_published && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $blogPost->update($validated);

        return redirect()->route('blog-posts.index')->with('success', 'Blog post updated successfully');
    }

    public function destroy(BlogPost $blogPost)
    {
        // Delete featured image file if exists
        if ($blogPost->featured_image) {
            $this->deleteFeaturedImageFile($blogPost->featured_image);
        }

        $blogPost->delete();

        return redirect()->route('blog-posts.index')->with('success', 'Blog post deleted successfully');
    }

    public function show(BlogPost $blogPost)
    {
        return view('blog-posts.show', compact('blogPost'));
    }

    /**
     * Delete a featured image file from the public path.
     */
    private function deleteFeaturedImageFile(string $imageUrl): void
    {
        $path = parse_url($imageUrl, PHP_URL_PATH);
        $fullPath = public_path($path);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
