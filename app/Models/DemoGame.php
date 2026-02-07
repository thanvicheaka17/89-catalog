<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoGame extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'demo_games';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_demo',
        'url',
        'image_path',
        'created_by',
    ];
    
    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_demo' => 'boolean',
            'slug' => 'string',
            'url' => 'string',
        ];
    }
    
    /**
     * Get the user who created this demo game.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

     /**
     * Get the full URL to the banner image.
     */
    public function getImageUrl(): ?string
    {
        return $this->image_path ? url($this->image_path) : null;
    }

    /**
     * Check if banner has an image.
     */
    public function hasImage(): bool
    {
        return !empty($this->image_path);
    }
}
