<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'blog_posts';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'featured_image',
        'author_name',
        'author_role',
        'tags',
        'read_time',
        'view_count',
        'is_featured',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'read_time' => 'integer',
        'view_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hasFeaturedImage(): bool
    {
        return !empty($this->featured_image);
    }

    public function getFeaturedImageUrl(): string
    {
        if ($this->hasFeaturedImage()) {
            return url($this->featured_image);
        }

        return url('images/blog/default-featured.webp');
    }

    public function getTagsArray(): array
    {
        if (empty($this->tags)) {
            return [];
        }

        return array_map('trim', explode(',', $this->tags));
    }

    public function getExcerpt($length = 150): string
    {
        if (!empty($this->excerpt)) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags($this->content), $length);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }


    public function scopeByTag($query, $tag)
    {
        return $query->where('tags', 'like', "%{$tag}%");
    }
}
