<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BlogPost;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::published();


        // Filter by tag
        if ($request->has('tag') && $request->tag) {
            $query->where('tags', 'like', "%{$request->tag}%");
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('tags', 'like', "%{$search}%");
            });
        }

        // Featured posts only
        if ($request->boolean('featured')) {
            $query->featured();
        }

        $perPage = $request->input('per_page', 12);
        $perPage = in_array($perPage, [6, 12, 24, 48]) ? $perPage : 12;

        $blogPosts = $query->orderBy('published_at', 'desc')->paginate($perPage);

        $data = $blogPosts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->getExcerpt(),
                'content' => $post->content,
                'featured_image' => $post->getFeaturedImageUrl(),
                'author_name' => $post->author_name,
                'author_role' => $post->author_role,
                'tags' => $post->getTagsArray(),
                'read_time' => $post->read_time,
                'view_count' => $post->view_count,
                'is_featured' => $post->is_featured,
                'published_at' => $post->published_at?->format('Y-m-d H:i:s'),
                'created_at' => $post->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => $blogPosts->total(),
            'current_page' => $blogPosts->currentPage(),
            'last_page' => $blogPosts->lastPage(),
            'per_page' => $blogPosts->perPage(),
        ]);
    }

    public function show($slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->first();

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Blog post not found'
            ], 404);
        }

        // Increment view count
        $post->increment('view_count');

        $data = [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->getExcerpt(),
            'content' => $post->content,
            'featured_image' => $post->getFeaturedImageUrl(),
            'author_name' => $post->author_name,
            'author_role' => $post->author_role,
            'tags' => $post->getTagsArray(),
            'read_time' => $post->read_time,
            'view_count' => $post->view_count,
            'is_featured' => $post->is_featured,
            'published_at' => $post->published_at?->format('Y-m-d H:i:s'),
            'created_at' => $post->created_at?->format('Y-m-d H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function tags()
    {
        $allTags = BlogPost::published()
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatMap(function ($tags) {
                return array_map('trim', explode(',', $tags));
            })
            ->filter()
            ->unique()
            ->values()
            ->map(function ($tag) {
                $count = BlogPost::published()
                    ->where('tags', 'like', "%{$tag}%")
                    ->count();
                return [
                    'name' => $tag,
                    'count' => $count
                ];
            })
            ->sortByDesc('count')
            ->take(20);

        return response()->json([
            'success' => true,
            'data' => $allTags
        ]);
    }
}
