<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'banners';

    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'link_url',
        'priority',
        'is_active',
        'visibility',
        'start_at',
        'end_at',
        'meta',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'meta' => 'json',
    ];

    public function getStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }
        
        return 'active';
    }


    public function getStatusDisplayName(): string
    {
        return match($this->getStatus()) {
            'active' => 'Active',
            'inactive' => 'Inactive',
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match($this->getStatus()) {
            'active' => 'status-active',
            'inactive' => 'status-inactive',
        };
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
